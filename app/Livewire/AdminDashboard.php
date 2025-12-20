<?php

namespace App\Livewire;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Event;
use App\Models\Organization;
use App\Models\Recipient;
use App\Models\User;
use App\Models\Volunteer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminDashboard extends Component
{
    public $dateRange = '30'; // days

    public $activeTab = 'overview'; // overview, campaigns, organizations, donors, events

    // Statistics
    public $totalUsers;

    public $totalCampaigns;

    public $totalEvents;

    public $totalDonations;

    public $totalVolunteers;

    public $totalOrganizations;

    // Financial
    public $totalRaised;

    public $totalAllocated;

    public $pendingApprovals;

    // Advanced Analytics
    public $topCampaigns;

    public $organizationLeaderboard;

    public $donorInsights;

    public $eventMetrics;

    public $campaignSuccessRate;

    public $geographicDistribution;

    public $paymentMethodStats;

    public $allocationEfficiency;

    public $recentActivity;

    // Charts
    public $donationsChart;

    public $campaignsChart;

    public $eventsChart;

    public $userGrowthChart;

    public $donationsByMethodChart;

    public $campaignStatusChart;

    public function mount()
    {
        $this->loadStatistics();
    }

    public function updatedDateRange()
    {
        $this->loadStatistics();
    }

    public function updatedActiveTab()
    {
        $this->loadStatistics();
    }

    public function loadStatistics()
    {
        $days = (int) $this->dateRange;
        $startDate = now()->subDays($days);

        // Basic Statistics
        $this->totalUsers = User::count();
        $this->totalCampaigns = Campaign::count();
        $this->totalEvents = Event::count();
        $this->totalDonations = Donation::count();
        $this->totalVolunteers = Volunteer::count();
        $this->totalOrganizations = Organization::count();

        // Financial
        $this->totalRaised = Campaign::sum('Collected_Amount') ?? 0;
        $this->totalAllocated = DB::table('donation_allocation')->sum('Amount_Allocated') ?? 0;

        // Pending approvals
        $this->pendingApprovals = [
            'campaigns' => Campaign::where('Status', 'Pending')->count(),
            'events' => Event::where('Status', 'Pending')->count(),
            'recipients' => Recipient::where('Status', 'Pending')->count(),
        ];

        // Load advanced analytics based on active tab
        $this->loadAdvancedAnalytics($startDate);

        // Load charts
        $this->loadChartData($startDate);
    }

    private function loadAdvancedAnalytics($startDate)
    {
        // Database-agnostic column quoting helper
        $quotedColumn = function ($table, $column) {
            $driver = DB::connection()->getDriverName();
            if ($driver === 'pgsql') {
                return "\"{$table}\".\"{$column}\"";
            }

            return "`{$table}`.`{$column}`";
        };

        // Top Performing Campaigns - Complex JOIN query
        $this->topCampaigns = DB::table('campaign')
            ->join('organization', 'campaign.Organization_ID', '=', 'organization.Organization_ID')
            ->join('users', 'organization.Organizer_ID', '=', 'users.id')
            ->leftJoin('donation', 'campaign.Campaign_ID', '=', 'donation.Campaign_ID')
            ->select(
                'campaign.Campaign_ID',
                'campaign.Title as campaign_title',
                'users.name as organizer_name',
                'campaign.Collected_Amount',
                'campaign.Goal_Amount',
                DB::raw('COUNT(DISTINCT '.$quotedColumn('donation', 'Donation_ID').') as donation_count'),
                DB::raw('COUNT(DISTINCT '.$quotedColumn('donation', 'Donor_ID').') as donor_count'),
                DB::raw('ROUND(CAST('.$quotedColumn('campaign', 'Collected_Amount').' AS DECIMAL) / NULLIF('.$quotedColumn('campaign', 'Goal_Amount').', 0) * 100, 2) as achievement_percentage')
            )
            ->where('campaign.Status', 'Active')
            ->groupBy('campaign.Campaign_ID', 'campaign.Title', 'users.name', 'campaign.Collected_Amount', 'campaign.Goal_Amount')
            ->orderByDesc('campaign.Collected_Amount')
            ->limit(10)
            ->get();

        // Organization Leaderboard - Multiple JOINs
        $this->organizationLeaderboard = DB::table('organization')
            ->join('users', 'organization.Organizer_ID', '=', 'users.id')
            ->leftJoin('campaign', 'organization.Organization_ID', '=', 'campaign.Organization_ID')
            ->leftJoin('event', 'organization.Organization_ID', '=', 'event.Organizer_ID')
            ->select(
                'organization.Organization_ID',
                'users.name as organizer_name',
                'organization.City',
                'organization.State',
                DB::raw('COUNT(DISTINCT '.$quotedColumn('campaign', 'Campaign_ID').') as total_campaigns'),
                DB::raw('COUNT(DISTINCT '.$quotedColumn('event', 'Event_ID').') as total_events'),
                DB::raw('SUM('.$quotedColumn('campaign', 'Collected_Amount').') as total_raised'),
                DB::raw('COUNT(DISTINCT CASE WHEN '.$quotedColumn('campaign', 'Status')." = 'Active' THEN ".$quotedColumn('campaign', 'Campaign_ID').' END) as active_campaigns')
            )
            ->groupBy('organization.Organization_ID', 'users.name', 'organization.City', 'organization.State')
            ->orderByDesc('total_raised')
            ->limit(10)
            ->get();

        // Donor Insights - Advanced aggregation
        $this->donorInsights = DB::table('donation')
            ->join('donor', 'donation.Donor_ID', '=', 'donor.Donor_ID')
            ->join('users', 'donor.User_ID', '=', 'users.id')
            ->select(
                'users.name as donor_name',
                'users.email',
                DB::raw('COUNT('.$quotedColumn('donation', 'Donation_ID').') as donation_count'),
                DB::raw('SUM('.$quotedColumn('donation', 'Amount').') as total_donated'),
                DB::raw('AVG('.$quotedColumn('donation', 'Amount').') as avg_donation'),
                DB::raw('MIN('.$quotedColumn('donation', 'Donation_Date').') as first_donation'),
                DB::raw('MAX('.$quotedColumn('donation', 'Donation_Date').') as last_donation'),
                DB::raw('COUNT(DISTINCT '.$quotedColumn('donation', 'Campaign_ID').') as campaigns_supported')
            )
            ->where('donation.created_at', '>=', $startDate)
            ->groupBy('donation.Donor_ID', 'users.name', 'users.email')
            ->orderByDesc('total_donated')
            ->limit(10)
            ->get();

        // Event Metrics - Complex JOIN with participation data
        $this->eventMetrics = DB::table('event')
            ->join('organization', 'event.Organizer_ID', '=', 'organization.Organization_ID')
            ->join('users', 'organization.Organizer_ID', '=', 'users.id')
            ->leftJoin('event_participation', 'event.Event_ID', '=', 'event_participation.Event_ID')
            ->select(
                'event.Event_ID',
                'event.Title as event_title',
                'users.name as organizer_name',
                'event.Capacity',
                'event.Status',
                DB::raw('COUNT(DISTINCT '.$quotedColumn('event_participation', 'Volunteer_ID').') as volunteers_registered'),
                DB::raw('SUM('.$quotedColumn('event_participation', 'Total_Hours').') as total_hours'),
                DB::raw('ROUND(CAST(COUNT(DISTINCT '.$quotedColumn('event_participation', 'Volunteer_ID').') AS DECIMAL) / NULLIF('.$quotedColumn('event', 'Capacity').', 0) * 100, 2) as fill_rate')
            )
            ->whereIn('event.Status', ['Upcoming', 'Ongoing', 'Completed'])
            ->groupBy('event.Event_ID', 'event.Title', 'users.name', 'event.Capacity', 'event.Status')
            ->orderByDesc('volunteers_registered')
            ->limit(10)
            ->get();

        // Campaign Success Rate Analysis
        $campaignStats = DB::table('campaign')
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN '.$quotedColumn('campaign', 'Collected_Amount').' >= '.$quotedColumn('campaign', 'Goal_Amount').' THEN 1 ELSE 0 END) as successful'),
                DB::raw('SUM(CASE WHEN '.$quotedColumn('campaign', 'Status')." = 'Active' THEN 1 ELSE 0 END) as active"),
                DB::raw('SUM(CASE WHEN '.$quotedColumn('campaign', 'Status')." = 'Pending' THEN 1 ELSE 0 END) as pending"),
                DB::raw('AVG(CAST('.$quotedColumn('campaign', 'Collected_Amount').' AS DECIMAL) / NULLIF('.$quotedColumn('campaign', 'Goal_Amount').', 0) * 100) as avg_achievement_rate')
            )
            ->first();

        $this->campaignSuccessRate = [
            'total' => $campaignStats->total,
            'successful' => $campaignStats->successful,
            'active' => $campaignStats->active,
            'pending' => $campaignStats->pending,
            'success_rate' => $campaignStats->total > 0 ? round(($campaignStats->successful / $campaignStats->total) * 100, 2) : 0,
            'avg_achievement_rate' => round($campaignStats->avg_achievement_rate ?? 0, 2),
        ];

        // Geographic Distribution
        $this->geographicDistribution = DB::table('organization')
            ->leftJoin('campaign', 'organization.Organization_ID', '=', 'campaign.Organization_ID')
            ->select(
                'organization.State',
                'organization.City',
                DB::raw('COUNT(DISTINCT '.$quotedColumn('organization', 'Organization_ID').') as org_count'),
                DB::raw('COUNT(DISTINCT '.$quotedColumn('campaign', 'Campaign_ID').') as campaign_count'),
                DB::raw('SUM('.$quotedColumn('campaign', 'Collected_Amount').') as total_raised')
            )
            ->groupBy('organization.State', 'organization.City')
            ->orderByDesc('total_raised')
            ->limit(15)
            ->get();

        // Payment Method Statistics
        $this->paymentMethodStats = DB::table('donation')
            ->select(
                'Payment_Method',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM('.$quotedColumn('donation', 'Amount').') as total_amount'),
                DB::raw('AVG('.$quotedColumn('donation', 'Amount').') as avg_amount')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('Payment_Method')
            ->orderByDesc('total_amount')
            ->get();

        // Allocation Efficiency - JOIN across multiple tables
        $this->allocationEfficiency = DB::table('campaign')
            ->leftJoin('donation_allocation', 'campaign.Campaign_ID', '=', 'donation_allocation.Campaign_ID')
            ->select(
                'campaign.Campaign_ID',
                'campaign.Title',
                'campaign.Collected_Amount',
                DB::raw('COALESCE(SUM('.$quotedColumn('donation_allocation', 'Amount_Allocated').'), 0) as allocated_amount'),
                DB::raw($quotedColumn('campaign', 'Collected_Amount').' - COALESCE(SUM('.$quotedColumn('donation_allocation', 'Amount_Allocated').'), 0) as unallocated_amount'),
                DB::raw('ROUND((COALESCE(SUM('.$quotedColumn('donation_allocation', 'Amount_Allocated').'), 0) / NULLIF('.$quotedColumn('campaign', 'Collected_Amount').', 0)) * 100, 2) as allocation_percentage'),
                DB::raw('COUNT(DISTINCT '.$quotedColumn('donation_allocation', 'Recipient_ID').') as recipient_count')
            )
            ->where('campaign.Status', '!=', 'Pending')
            ->groupBy('campaign.Campaign_ID', 'campaign.Title', 'campaign.Collected_Amount')
            ->orderByDesc('campaign.Collected_Amount')
            ->limit(10)
            ->get();

        // Recent Activity - Combined from multiple sources
        // Use database-agnostic string concatenation
        $driver = DB::connection()->getDriverName();
        $concatDonation = $driver === 'pgsql'
            ? "CONCAT('donated RM ', ".$quotedColumn('donation', 'Amount').", ' to ', ".$quotedColumn('campaign', 'Title').')'
            : "CONCAT('donated RM ', ".$quotedColumn('donation', 'Amount').", ' to ', ".$quotedColumn('campaign', 'Title').')';
        $concatCampaign = $driver === 'pgsql'
            ? "CONCAT('created campaign: ', ".$quotedColumn('campaign', 'Title').')'
            : "CONCAT('created campaign: ', ".$quotedColumn('campaign', 'Title').')';

        $recentDonations = DB::table('donation')
            ->join('donor', 'donation.Donor_ID', '=', 'donor.Donor_ID')
            ->join('users', 'donor.User_ID', '=', 'users.id')
            ->join('campaign', 'donation.Campaign_ID', '=', 'campaign.Campaign_ID')
            ->select(
                DB::raw("'donation' as type"),
                'users.name as actor',
                DB::raw($concatDonation.' as description'),
                'donation.created_at as activity_date'
            )
            ->where('donation.created_at', '>=', $startDate)
            ->limit(5);

        $recentCampaigns = DB::table('campaign')
            ->join('organization', 'campaign.Organization_ID', '=', 'organization.Organization_ID')
            ->join('users', 'organization.Organizer_ID', '=', 'users.id')
            ->select(
                DB::raw("'campaign' as type"),
                'users.name as actor',
                DB::raw($concatCampaign.' as description'),
                'campaign.created_at as activity_date'
            )
            ->where('campaign.created_at', '>=', $startDate)
            ->limit(5);

        $this->recentActivity = $recentDonations
            ->union($recentCampaigns)
            ->orderByDesc('activity_date')
            ->limit(10)
            ->get();
    }

    private function loadChartData($startDate)
    {
        // Donations chart - aggregated by date
        $donations = Donation::where('Donation_Date', '>=', $startDate)
            ->orderBy('Donation_Date')
            ->get();

        $this->donationsChart = $donations
            ->groupBy(function ($donation) {
                return Carbon::parse($donation->Donation_Date)->format('Y-m-d');
            })
            ->map(function ($group) {
                return [
                    'date' => $group->first()->Donation_Date,
                    'amount' => (float) $group->sum('Amount'),
                    'count' => $group->count(),
                ];
            })
            ->values()
            ->toArray();

        // Campaigns chart
        $campaigns = Campaign::where('created_at', '>=', $startDate)
            ->orderBy('created_at')
            ->get();

        $this->campaignsChart = $campaigns
            ->groupBy(function ($campaign) {
                return Carbon::parse($campaign->created_at)->format('Y-m-d');
            })
            ->map(function ($group, $date) {
                return [
                    'date' => $date,
                    'count' => $group->count(),
                ];
            })
            ->values()
            ->toArray();

        // Events chart
        $events = Event::where('created_at', '>=', $startDate)
            ->orderBy('created_at')
            ->get();

        $this->eventsChart = $events
            ->groupBy(function ($event) {
                return Carbon::parse($event->created_at)->format('Y-m-d');
            })
            ->map(function ($group, $date) {
                return [
                    'date' => $date,
                    'count' => $group->count(),
                ];
            })
            ->values()
            ->toArray();

        // User growth chart
        $users = User::where('created_at', '>=', $startDate)
            ->orderBy('created_at')
            ->get();

        $this->userGrowthChart = $users
            ->groupBy(function ($user) {
                return Carbon::parse($user->created_at)->format('Y-m-d');
            })
            ->map(function ($group, $date) {
                return [
                    'date' => $date,
                    'count' => $group->count(),
                ];
            })
            ->values()
            ->toArray();

        // Donations by payment method - Pie chart data
        $this->donationsByMethodChart = $this->paymentMethodStats->map(function ($stat) {
            return [
                'method' => $stat->Payment_Method,
                'count' => $stat->transaction_count,
                'amount' => (float) $stat->total_amount,
            ];
        })->toArray();

        // Campaign status distribution - Pie chart
        $statusCounts = Campaign::select('Status', DB::raw('COUNT(*) as count'))
            ->groupBy('Status')
            ->get();

        $this->campaignStatusChart = $statusCounts->map(function ($stat) {
            return [
                'status' => $stat->Status,
                'count' => $stat->count,
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.admin-dashboard');
    }
}
