<?php

namespace App\Livewire;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\DonationAllocation;
use App\Models\Donor;
use App\Models\Organization;
use App\Models\Recipient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DonationDetailReport extends Component
{
    public $dateRange = 30; // Default 30 days

    public $detailedDonations;

    public $campaignPerformance;

    public $allocationReport;

    public $donorCampaignMatrix;

    public $recipientAllocationDetails;

    public $paymentMethodBreakdown;

    public $organizationFundingReport;

    public function mount()
    {
        $this->loadReports();
    }

    public function updatedDateRange()
    {
        $this->loadReports();
    }

    public function loadReports()
    {
        $startDate = Carbon::now()->subDays($this->dateRange);

        // Query 1: Detailed donation report (cross-database safe)
        $this->detailedDonations = $this->getDetailedDonations($startDate);

        // Query 2: Campaign performance (cross-database safe)
        $this->campaignPerformance = $this->getCampaignPerformance($startDate);

        // Query 3: Allocation report (cross-database safe)
        $this->allocationReport = $this->getAllocationReport($startDate);

        // Query 4: Donor-Campaign matrix (cross-database safe)
        $this->donorCampaignMatrix = $this->getDonorCampaignMatrix($startDate);

        // Query 5: Recipient allocation details (cross-database safe)
        $this->recipientAllocationDetails = $this->getRecipientAllocationDetails();

        // Query 6: Payment method breakdown (cross-database safe)
        $this->paymentMethodBreakdown = $this->getPaymentMethodBreakdown($startDate);

        // Query 7: Organization funding report (cross-database safe)
        $this->organizationFundingReport = $this->getOrganizationFundingReport();
    }

    /**
     * Query 1: Detailed donation report with multiple relationships
     * Cross-database: hannah (donations, donors) -> izzhilmy (users) -> izzati (campaigns, organizations)
     */
    private function getDetailedDonations($startDate)
    {
        // Step 1: Get donations from hannah database
        $donations = Donation::where('Donation_Date', '>=', $startDate)
            ->orderBy('Donation_Date', 'desc')
            ->limit(100)
            ->get();

        if ($donations->isEmpty()) {
            return collect();
        }

        // Step 2: Get donor IDs and campaign IDs
        $donorIds = $donations->pluck('Donor_ID')->unique()->toArray();
        $campaignIds = $donations->pluck('Campaign_ID')->unique()->toArray();

        // Step 3: Load donors with users (hannah -> izzhilmy)
        $donors = Donor::whereIn('Donor_ID', $donorIds)
            ->with('user')
            ->get()
            ->keyBy('Donor_ID');

        // Step 4: Load campaigns with organizations and organizer users (izzati -> izzhilmy)
        $campaigns = Campaign::whereIn('Campaign_ID', $campaignIds)
            ->with('organization.user')
            ->get()
            ->keyBy('Campaign_ID');

        // Step 5: Merge data manually
        return $donations->map(function ($donation) use ($donors, $campaigns) {
            $donor = $donors->get($donation->Donor_ID);
            $campaign = $campaigns->get($donation->Campaign_ID);

            return (object) [
                'Donation_ID' => $donation->Donation_ID,
                'Receipt_No' => $donation->Receipt_No,
                'Amount' => $donation->Amount,
                'Payment_Method' => $donation->Payment_Method,
                'Payment_Status' => $donation->Payment_Status,
                'Donation_Date' => $donation->Donation_Date,
                'donor_name' => $donor?->user?->name ?? 'N/A',
                'donor_email' => $donor?->user?->email ?? 'N/A',
                'Total_Donated' => $donor?->Total_Donated ?? 0,
                'campaign_title' => $campaign?->Title ?? 'N/A',
                'Goal_Amount' => $campaign?->Goal_Amount ?? 0,
                'Collected_Amount' => $campaign?->Collected_Amount ?? 0,
                'organizer_name' => $campaign?->organization?->user?->name ?? 'N/A',
                'Organization_ID' => $campaign?->Organization_ID ?? null,
            ];
        });
    }

    /**
     * Query 2: Campaign performance with aggregated statistics
     * Cross-database: izzati (campaigns, organizations) -> hannah (donations, allocations) -> izzhilmy (users)
     */
    private function getCampaignPerformance($startDate)
    {
        // Step 1: Get campaigns with organizations from izzati
        $campaigns = Campaign::with('organization.user')
            ->orderBy('Collected_Amount', 'desc')
            ->limit(20)
            ->get();

        if ($campaigns->isEmpty()) {
            return collect();
        }

        $campaignIds = $campaigns->pluck('Campaign_ID')->toArray();

        // Step 2: Get donation statistics from hannah (grouped by campaign)
        $donationStats = Donation::whereIn('Campaign_ID', $campaignIds)
            ->where('Donation_Date', '>=', $startDate)
            ->select(
                'Campaign_ID',
                DB::raw('COUNT(DISTINCT Donor_ID) as unique_donors'),
                DB::raw('COUNT(Donation_ID) as total_donations')
            )
            ->groupBy('Campaign_ID')
            ->get()
            ->keyBy('Campaign_ID');

        // Step 3: Get allocation totals from hannah (grouped by campaign)
        $allocationStats = DonationAllocation::whereIn('Campaign_ID', $campaignIds)
            ->select(
                'Campaign_ID',
                DB::raw('SUM(Amount_Allocated) as total_allocated')
            )
            ->groupBy('Campaign_ID')
            ->get()
            ->keyBy('Campaign_ID');

        // Step 4: Merge data
        return $campaigns->map(function ($campaign) use ($donationStats, $allocationStats) {
            $donStats = $donationStats->get($campaign->Campaign_ID);
            $allocStats = $allocationStats->get($campaign->Campaign_ID);

            $totalAllocated = $allocStats?->total_allocated ?? 0;
            $completionPercentage = $campaign->Goal_Amount > 0
                ? round(($campaign->Collected_Amount / $campaign->Goal_Amount) * 100, 2)
                : 0;

            return (object) [
                'Campaign_ID' => $campaign->Campaign_ID,
                'Title' => $campaign->Title,
                'Goal_Amount' => $campaign->Goal_Amount,
                'Collected_Amount' => $campaign->Collected_Amount,
                'Start_Date' => $campaign->Start_Date,
                'End_Date' => $campaign->End_Date,
                'Status' => $campaign->Status,
                'organizer_name' => $campaign->organization?->user?->name ?? 'N/A',
                'unique_donors' => $donStats?->unique_donors ?? 0,
                'total_donations' => $donStats?->total_donations ?? 0,
                'total_allocated' => $totalAllocated,
                'unallocated_funds' => $campaign->Collected_Amount - $totalAllocated,
                'completion_percentage' => $completionPercentage,
            ];
        });
    }

    /**
     * Query 3: Allocation report with recipient and campaign details
     * Cross-database: hannah (allocations) -> izzati (campaigns, organizations) -> adam (recipients) -> izzhilmy (users)
     */
    private function getAllocationReport($startDate)
    {
        // Step 1: Get allocations from hannah
        $allocations = DonationAllocation::where('Allocated_At', '>=', $startDate)
            ->orderBy('Allocated_At', 'desc')
            ->limit(50)
            ->get();

        if ($allocations->isEmpty()) {
            return collect();
        }

        // Step 2: Get campaign IDs and recipient IDs
        $campaignIds = $allocations->pluck('Campaign_ID')->unique()->toArray();
        $recipientIds = $allocations->pluck('Recipient_ID')->unique()->toArray();

        // Step 3: Load campaigns with organizations from izzati
        $campaigns = Campaign::whereIn('Campaign_ID', $campaignIds)
            ->with('organization.user')
            ->get()
            ->keyBy('Campaign_ID');

        // Step 4: Load recipients with public profiles and users from adam -> izzhilmy
        $recipients = Recipient::whereIn('Recipient_ID', $recipientIds)
            ->with('publicProfile.user')
            ->get()
            ->keyBy('Recipient_ID');

        // Step 5: Merge data
        return $allocations->map(function ($allocation) use ($campaigns, $recipients) {
            $campaign = $campaigns->get($allocation->Campaign_ID);
            $recipient = $recipients->get($allocation->Recipient_ID);

            return (object) [
                'Allocation_ID' => $allocation->Allocation_ID,
                'Amount_Allocated' => $allocation->Amount_Allocated,
                'Allocated_At' => $allocation->Allocated_At,
                'campaign_title' => $campaign?->Title ?? 'N/A',
                'campaign_total' => $campaign?->Collected_Amount ?? 0,
                'recipient_name' => $recipient?->publicProfile?->user?->name ?? 'N/A',
                'recipient_email' => $recipient?->publicProfile?->user?->email ?? 'N/A',
                'Family_Size' => $recipient?->Family_Size ?? 0,
                'Monthly_Income' => $recipient?->Monthly_Income ?? 0,
                'Need_Description' => $recipient?->Need_Description ?? 'N/A',
                'recipient_status' => $recipient?->Status ?? 'N/A',
                'allocated_by' => $campaign?->organization?->user?->name ?? 'N/A',
            ];
        });
    }

    /**
     * Query 4: Donor-Campaign contribution matrix
     * Cross-database: hannah (donations, donors) -> izzhilmy (users) -> izzati (campaigns)
     */
    private function getDonorCampaignMatrix($startDate)
    {
        // Step 1: Get donations from hannah with aggregation
        $donationGroups = Donation::where('Donation_Date', '>=', $startDate)
            ->select(
                'Donor_ID',
                'Campaign_ID',
                DB::raw('COUNT(Donation_ID) as donation_count'),
                DB::raw('SUM(Amount) as total_contributed'),
                DB::raw('AVG(Amount) as average_donation'),
                DB::raw('MIN(Donation_Date) as first_donation'),
                DB::raw('MAX(Donation_Date) as last_donation')
            )
            ->groupBy('Donor_ID', 'Campaign_ID')
            ->orderByDesc('total_contributed')
            ->limit(50)
            ->get();

        if ($donationGroups->isEmpty()) {
            return collect();
        }

        // Step 2: Get unique donor and campaign IDs
        $donorIds = $donationGroups->pluck('Donor_ID')->unique()->toArray();
        $campaignIds = $donationGroups->pluck('Campaign_ID')->unique()->toArray();

        // Step 3: Load donors with users
        $donors = Donor::whereIn('Donor_ID', $donorIds)
            ->with('user')
            ->get()
            ->keyBy('Donor_ID');

        // Step 4: Load campaigns
        $campaigns = Campaign::whereIn('Campaign_ID', $campaignIds)
            ->get()
            ->keyBy('Campaign_ID');

        // Step 5: Merge data
        return $donationGroups->map(function ($group) use ($donors, $campaigns) {
            $donor = $donors->get($group->Donor_ID);
            $campaign = $campaigns->get($group->Campaign_ID);

            return (object) [
                'donor_name' => $donor?->user?->name ?? 'N/A',
                'donor_email' => $donor?->user?->email ?? 'N/A',
                'campaign_title' => $campaign?->Title ?? 'N/A',
                'donation_count' => $group->donation_count,
                'total_contributed' => $group->total_contributed,
                'average_donation' => $group->average_donation,
                'first_donation' => $group->first_donation,
                'last_donation' => $group->last_donation,
            ];
        });
    }

    /**
     * Query 5: Recipient allocation details with campaign aggregates
     * Cross-database: adam (recipients) -> izzhilmy (users) -> hannah (allocations) -> izzati (campaigns)
     */
    private function getRecipientAllocationDetails()
    {
        // Step 1: Get recipients with public profiles from adam
        $recipients = Recipient::with('publicProfile.user')
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        if ($recipients->isEmpty()) {
            return collect();
        }

        $recipientIds = $recipients->pluck('Recipient_ID')->toArray();

        // Step 2: Get allocation statistics from hannah (grouped by recipient)
        $allocationStats = DonationAllocation::whereIn('Recipient_ID', $recipientIds)
            ->select(
                'Recipient_ID',
                DB::raw('COUNT(DISTINCT Campaign_ID) as campaigns_received_from'),
                DB::raw('SUM(Amount_Allocated) as total_received'),
                DB::raw('COUNT(Allocation_ID) as allocation_count')
            )
            ->groupBy('Recipient_ID')
            ->get()
            ->keyBy('Recipient_ID');

        // Step 3: Merge data and sort by total received
        return $recipients->map(function ($recipient) use ($allocationStats) {
            $stats = $allocationStats->get($recipient->Recipient_ID);

            return (object) [
                'Recipient_ID' => $recipient->Recipient_ID,
                'recipient_name' => $recipient->publicProfile?->user?->name ?? 'N/A',
                'recipient_email' => $recipient->publicProfile?->user?->email ?? 'N/A',
                'Family_Size' => $recipient->Family_Size,
                'Monthly_Income' => $recipient->Monthly_Income,
                'Need_Description' => $recipient->Need_Description,
                'Status' => $recipient->Status,
                'application_date' => $recipient->created_at,
                'campaigns_received_from' => $stats?->campaigns_received_from ?? 0,
                'total_received' => $stats?->total_received ?? 0,
                'allocation_count' => $stats?->allocation_count ?? 0,
            ];
        })->sortByDesc('total_received')->values();
    }

    /**
     * Query 6: Payment method breakdown with campaign and status analysis
     * Cross-database: hannah (donations) -> izzati (campaigns)
     */
    private function getPaymentMethodBreakdown($startDate)
    {
        // Step 1: Get payment method breakdown from hannah
        $paymentStats = Donation::where('Donation_Date', '>=', $startDate)
            ->select(
                'Payment_Method',
                'Payment_Status',
                'Campaign_ID',
                DB::raw('COUNT(Donation_ID) as transaction_count'),
                DB::raw('SUM(Amount) as total_amount'),
                DB::raw('AVG(Amount) as average_amount'),
                DB::raw("COUNT(CASE WHEN Payment_Status = 'Completed' THEN 1 END) as successful_count"),
                DB::raw("COUNT(CASE WHEN Payment_Status = 'Failed' THEN 1 END) as failed_count"),
                DB::raw("COUNT(CASE WHEN Payment_Status = 'Pending' THEN 1 END) as pending_count")
            )
            ->groupBy('Payment_Method', 'Payment_Status', 'Campaign_ID')
            ->orderByDesc('total_amount')
            ->get();

        if ($paymentStats->isEmpty()) {
            return collect();
        }

        // Step 2: Get unique campaign IDs
        $campaignIds = $paymentStats->pluck('Campaign_ID')->unique()->toArray();

        // Step 3: Load campaigns from izzati
        $campaigns = Campaign::whereIn('Campaign_ID', $campaignIds)
            ->get()
            ->keyBy('Campaign_ID');

        // Step 4: Merge data
        return $paymentStats->map(function ($stat) use ($campaigns) {
            $campaign = $campaigns->get($stat->Campaign_ID);

            return (object) [
                'Payment_Method' => $stat->Payment_Method,
                'Payment_Status' => $stat->Payment_Status,
                'campaign_title' => $campaign?->Title ?? 'N/A',
                'transaction_count' => $stat->transaction_count,
                'total_amount' => $stat->total_amount,
                'average_amount' => $stat->average_amount,
                'successful_count' => $stat->successful_count,
                'failed_count' => $stat->failed_count,
                'pending_count' => $stat->pending_count,
            ];
        });
    }

    /**
     * Query 7: Organization funding report with comprehensive statistics
     * Cross-database: izzati (organizations, campaigns) -> izzhilmy (users) -> hannah (donations, allocations)
     */
    private function getOrganizationFundingReport()
    {
        // Step 1: Get organizations with users from izzati
        $organizations = Organization::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($organizations->isEmpty()) {
            return collect();
        }

        $organizationIds = $organizations->pluck('Organization_ID')->toArray();

        // Step 2: Get campaigns for these organizations
        $campaigns = Campaign::whereIn('Organization_ID', $organizationIds)
            ->get();

        $campaignIds = $campaigns->pluck('Campaign_ID')->toArray();

        // Step 3: Get campaign statistics (per organization)
        $campaignStatsByOrg = $campaigns->groupBy('Organization_ID')->map(function ($orgCampaigns) {
            return (object) [
                'total_campaigns' => $orgCampaigns->count(),
                'active_campaigns' => $orgCampaigns->where('Status', 'Active')->count(),
                'total_raised' => $orgCampaigns->sum('Collected_Amount'),
            ];
        });

        // Step 4: Get donation statistics from hannah (donor count per campaign)
        $donorStats = ! empty($campaignIds) ? Donation::whereIn('Campaign_ID', $campaignIds)
            ->select('Campaign_ID', DB::raw('COUNT(DISTINCT Donor_ID) as unique_donors'))
            ->groupBy('Campaign_ID')
            ->get()
            ->keyBy('Campaign_ID') : collect();

        // Step 5: Get allocation statistics from hannah
        $allocationStats = ! empty($campaignIds) ? DonationAllocation::whereIn('Campaign_ID', $campaignIds)
            ->select(
                'Campaign_ID',
                DB::raw('SUM(Amount_Allocated) as total_allocated'),
                DB::raw('COUNT(DISTINCT Recipient_ID) as recipients_helped')
            )
            ->groupBy('Campaign_ID')
            ->get()
            ->keyBy('Campaign_ID') : collect();

        // Step 6: Map campaigns to organizations for aggregation
        $campaignsByOrg = $campaigns->groupBy('Organization_ID');

        // Step 7: Merge all data
        return $organizations->map(function ($organization) use ($campaignStatsByOrg, $campaignsByOrg, $donorStats, $allocationStats) {
            $stats = $campaignStatsByOrg->get($organization->Organization_ID);
            $orgCampaigns = $campaignsByOrg->get($organization->Organization_ID, collect());

            // Aggregate donor and allocation stats across all campaigns
            $totalAllocated = 0;
            $uniqueDonors = 0;
            $recipientsHelped = 0;

            foreach ($orgCampaigns as $campaign) {
                $donStat = $donorStats->get($campaign->Campaign_ID);
                $allocStat = $allocationStats->get($campaign->Campaign_ID);

                $uniqueDonors += $donStat?->unique_donors ?? 0;
                $totalAllocated += $allocStat?->total_allocated ?? 0;
                $recipientsHelped += $allocStat?->recipients_helped ?? 0;
            }

            $totalRaised = $stats?->total_raised ?? 0;

            return (object) [
                'Organization_ID' => $organization->Organization_ID,
                'organization_name' => $organization->user?->name ?? 'N/A',
                'contact_email' => $organization->user?->email ?? 'N/A',
                'total_campaigns' => $stats?->total_campaigns ?? 0,
                'active_campaigns' => $stats?->active_campaigns ?? 0,
                'unique_donors' => $uniqueDonors,
                'total_raised' => $totalRaised,
                'total_allocated' => $totalAllocated,
                'available_funds' => $totalRaised - $totalAllocated,
                'recipients_helped' => $recipientsHelped,
            ];
        })->sortByDesc('total_raised')->values();
    }

    public function render()
    {
        return view('livewire.donation-detail-report');
    }
}
