<?php

namespace App\Livewire;

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

        // Complex Query 1: Detailed donation report with multiple joins
        // Joins: donation -> donor -> user, donation -> campaign -> organization -> user
        $this->detailedDonations = DB::table('donation as d')
            ->join('donor as don', 'd.Donor_ID', '=', 'don.Donor_ID')
            ->join('users as u', 'don.User_ID', '=', 'u.id')
            ->join('campaign as c', 'd.Campaign_ID', '=', 'c.Campaign_ID')
            ->join('organization as o', 'c.Organization_ID', '=', 'o.Organization_ID')
            ->join('users as org_user', 'o.Organizer_ID', '=', 'org_user.id')
            ->select(
                'd.Donation_ID',
                'd.Receipt_No',
                'd.Amount',
                'd.Payment_Method',
                'd.Payment_Status',
                'd.Donation_Date',
                'u.name as donor_name',
                'u.email as donor_email',
                'don.Total_Donated',
                'c.Title as campaign_title',
                'c.Goal_Amount',
                'c.Collected_Amount',
                'org_user.name as organizer_name',
                'o.Organization_ID'
            )
            ->where('d.Donation_Date', '>=', $startDate)
            ->orderBy('d.Donation_Date', 'desc')
            ->limit(100)
            ->get();

        // Complex Query 2: Campaign performance with donor statistics
        // Aggregates donations per campaign with donor counts and allocation info
        $this->campaignPerformance = DB::table('campaign as c')
            ->leftJoin('donation as d', 'c.Campaign_ID', '=', 'd.Campaign_ID')
            ->leftJoin('donation_allocation as da', 'c.Campaign_ID', '=', 'da.Campaign_ID')
            ->join('organization as o', 'c.Organization_ID', '=', 'o.Organization_ID')
            ->join('users as u', 'o.Organizer_ID', '=', 'u.id')
            ->select(
                'c.Campaign_ID',
                'c.Title',
                'c.Goal_Amount',
                'c.Collected_Amount',
                'c.Start_Date',
                'c.End_Date',
                'c.Status',
                'u.name as organizer_name',
                DB::raw('COUNT(DISTINCT d.Donor_ID) as unique_donors'),
                DB::raw('COUNT(d.Donation_ID) as total_donations'),
                DB::raw('COALESCE(SUM(da.Amount_Allocated), 0) as total_allocated'),
                DB::raw('(c.Collected_Amount - COALESCE(SUM(da.Amount_Allocated), 0)) as unallocated_funds'),
                DB::raw('CASE WHEN c.Goal_Amount > 0 THEN ROUND((c.Collected_Amount / c.Goal_Amount) * 100, 2) ELSE 0 END as completion_percentage')
            )
            ->groupBy(
                'c.Campaign_ID',
                'c.Title',
                'c.Goal_Amount',
                'c.Collected_Amount',
                'c.Start_Date',
                'c.End_Date',
                'c.Status',
                'u.name'
            )
            ->orderBy('c.Collected_Amount', 'desc')
            ->limit(20)
            ->get();

        // Complex Query 3: Allocation report with recipient, campaign, and donor details
        // Shows fund flow from campaigns to recipients with allocation details
        $this->allocationReport = DB::table('donation_allocation as da')
            ->join('campaign as c', 'da.Campaign_ID', '=', 'c.Campaign_ID')
            ->join('recipient as r', 'da.Recipient_ID', '=', 'r.Recipient_ID')
            ->join('public_profile as pp', 'r.Public_ID', '=', 'pp.Public_ID')
            ->join('users as rec_user', 'pp.User_ID', '=', 'rec_user.id')
            ->join('organization as o', 'c.Organization_ID', '=', 'o.Organization_ID')
            ->join('users as org_user', 'o.Organizer_ID', '=', 'org_user.id')
            ->select(
                'da.Allocation_ID',
                'da.Amount_Allocated',
                'da.Allocated_At',
                'c.Title as campaign_title',
                'c.Collected_Amount as campaign_total',
                'rec_user.name as recipient_name',
                'rec_user.email as recipient_email',
                'r.Family_Size',
                'r.Monthly_Income',
                'r.Need_Description',
                'r.Status as recipient_status',
                'org_user.name as allocated_by'
            )
            ->where('da.Allocated_At', '>=', $startDate)
            ->orderBy('da.Allocated_At', 'desc')
            ->limit(50)
            ->get();

        // Complex Query 4: Donor-Campaign contribution matrix
        // Shows which donors contributed to which campaigns
        $this->donorCampaignMatrix = DB::table('donation as d')
            ->join('donor as don', 'd.Donor_ID', '=', 'don.Donor_ID')
            ->join('users as u', 'don.User_ID', '=', 'u.id')
            ->join('campaign as c', 'd.Campaign_ID', '=', 'c.Campaign_ID')
            ->select(
                'u.name as donor_name',
                'u.email as donor_email',
                'c.Title as campaign_title',
                DB::raw('COUNT(d.Donation_ID) as donation_count'),
                DB::raw('SUM(d.Amount) as total_contributed'),
                DB::raw('AVG(d.Amount) as average_donation'),
                DB::raw('MIN(d.Donation_Date) as first_donation'),
                DB::raw('MAX(d.Donation_Date) as last_donation')
            )
            ->where('d.Donation_Date', '>=', $startDate)
            ->groupBy('u.name', 'u.email', 'c.Title')
            ->orderBy('total_contributed', 'desc')
            ->limit(50)
            ->get();

        // Complex Query 5: Recipient allocation details with campaign and donor aggregates
        // Shows recipients with total allocations and source campaign information
        $this->recipientAllocationDetails = DB::table('recipient as r')
            ->join('public_profile as pp', 'r.Public_ID', '=', 'pp.Public_ID')
            ->join('users as u', 'pp.User_ID', '=', 'u.id')
            ->leftJoin('donation_allocation as da', 'r.Recipient_ID', '=', 'da.Recipient_ID')
            ->leftJoin('campaign as c', 'da.Campaign_ID', '=', 'c.Campaign_ID')
            ->select(
                'r.Recipient_ID',
                'u.name as recipient_name',
                'u.email as recipient_email',
                'r.Family_Size',
                'r.Monthly_Income',
                'r.Need_Description',
                'r.Status',
                'r.created_at as application_date',
                DB::raw('COUNT(DISTINCT da.Campaign_ID) as campaigns_received_from'),
                DB::raw('COALESCE(SUM(da.Amount_Allocated), 0) as total_received'),
                DB::raw('COUNT(da.Allocation_ID) as allocation_count')
            )
            ->groupBy(
                'r.Recipient_ID',
                'u.name',
                'u.email',
                'r.Family_Size',
                'r.Monthly_Income',
                'r.Need_Description',
                'r.Status',
                'r.created_at'
            )
            ->orderBy('total_received', 'desc')
            ->limit(30)
            ->get();

        // Complex Query 6: Payment method breakdown with campaign and timing analysis
        $this->paymentMethodBreakdown = DB::table('donation as d')
            ->join('campaign as c', 'd.Campaign_ID', '=', 'c.Campaign_ID')
            ->select(
                'd.Payment_Method',
                'd.Payment_Status',
                'c.Title as campaign_title',
                DB::raw('COUNT(d.Donation_ID) as transaction_count'),
                DB::raw('SUM(d.Amount) as total_amount'),
                DB::raw('AVG(d.Amount) as average_amount'),
                DB::raw("COUNT(CASE WHEN d.Payment_Status = 'Completed' THEN 1 END) as successful_count"),
                DB::raw("COUNT(CASE WHEN d.Payment_Status = 'Failed' THEN 1 END) as failed_count"),
                DB::raw("COUNT(CASE WHEN d.Payment_Status = 'Pending' THEN 1 END) as pending_count")
            )
            ->where('d.Donation_Date', '>=', $startDate)
            ->groupBy('d.Payment_Method', 'd.Payment_Status', 'c.Title')
            ->orderBy('total_amount', 'desc')
            ->get();

        // Complex Query 7: Organization funding report
        // Shows how much each organization has raised, allocated, and has available
        $this->organizationFundingReport = DB::table('organization as o')
            ->join('users as u', 'o.Organizer_ID', '=', 'u.id')
            ->leftJoin('campaign as c', 'o.Organization_ID', '=', 'c.Organization_ID')
            ->leftJoin('donation as d', 'c.Campaign_ID', '=', 'd.Campaign_ID')
            ->leftJoin('donation_allocation as da', 'c.Campaign_ID', '=', 'da.Campaign_ID')
            ->select(
                'o.Organization_ID',
                'u.name as organization_name',
                'u.email as contact_email',
                DB::raw('COUNT(DISTINCT c.Campaign_ID) as total_campaigns'),
                DB::raw('COUNT(DISTINCT CASE WHEN c.Status = \'Active\' THEN c.Campaign_ID END) as active_campaigns'),
                DB::raw('COUNT(DISTINCT d.Donor_ID) as unique_donors'),
                DB::raw('COALESCE(SUM(DISTINCT c.Collected_Amount), 0) as total_raised'),
                DB::raw('COALESCE(SUM(da.Amount_Allocated), 0) as total_allocated'),
                DB::raw('(COALESCE(SUM(DISTINCT c.Collected_Amount), 0) - COALESCE(SUM(da.Amount_Allocated), 0)) as available_funds'),
                DB::raw('COUNT(DISTINCT da.Recipient_ID) as recipients_helped')
            )
            ->groupBy('o.Organization_ID', 'u.name', 'u.email')
            ->orderBy('total_raised', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.donation-detail-report');
    }
}
