<?php

namespace App\Livewire;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Donor;
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
    public $activeTab = 'overview'; // overview, campaigns, organizations, donors, events

    // Sorting and Filtering
    public $recipientSortBy = 'Collected_Amount';

    public $recipientSortDirection = 'desc';

    public $efficiencyFilter = 'all'; // all, most_efficient, least_efficient

    public $activityFilter = 'all'; // all, donation, campaign

    // Statistics
    public $totalUsers;

    public $totalCampaigns;

    public $totalEvents;

    public $totalDonations;

    public $totalVolunteers;

    public $totalOrganizations;

    public $totalRecipientsHelped;

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

    public $allocationEfficiency;

    public $recentActivity;

    // Charts
    public $donationsChart;

    public $campaignsChart;

    public $eventsChart;

    public $userGrowthChart;

    public $campaignStatusChart;

    public function mount()
    {
        $this->loadStatistics();
    }

    public function updatedActiveTab()
    {
        $this->loadStatistics();
    }

    public function loadStatistics()
    {

        // Basic Statistics
        $this->totalUsers = User::count();
        $this->totalCampaigns = Campaign::count();
        $this->totalEvents = Event::count();
        $this->totalDonations = Donation::count();
        $this->totalVolunteers = Volunteer::count();
        $this->totalOrganizations = Organization::count();

        // Financial
        $this->totalRaised = Campaign::sum('Collected_Amount') ?? 0;
        $this->totalAllocated = DB::connection('hannah')->table('donation_allocation')->sum('Amount_Allocated') ?? 0;

        // Recipients helped - count DISTINCT recipients who have received allocations
        $this->totalRecipientsHelped = DB::connection('hannah')->table('donation_allocation')
            ->distinct('Recipient_ID')
            ->count('Recipient_ID');

        // Pending approvals
        $this->pendingApprovals = [
            'campaigns' => Campaign::where('Status', 'Pending')->count(),
            'events' => Event::where('Status', 'Pending')->count(),
            'recipients' => Recipient::where('Status', 'Pending')->count(),
        ];

        // Load advanced analytics based on active tab
        $this->loadAdvancedAnalytics();

        // Load charts
        $this->loadChartData();
    }

    private function loadAdvancedAnalytics()
    {
        // Database-agnostic column quoting helper
        $quotedColumn = function ($table, $column) {
            $driver = DB::connection()->getDriverName();
            if ($driver === 'pgsql') {
                return "\"{$table}\".\"{$column}\"";
            }

            return "`{$table}`.`{$column}`";
        };

        // Top Performing Campaigns - Cross-database query (izzati + izzhilmy + hannah)
        // TODO: Refactor with proper application-level joins for production
        // Simplified query for now to avoid cross-database JOIN errors
        $this->topCampaigns = Campaign::with('organization.user')
            ->where('Status', 'Active')
            ->orderByDesc('Collected_Amount')
            ->limit(10)
            ->get()
            ->map(function ($campaign) {
                $donationCount = Donation::where('Campaign_ID', $campaign->Campaign_ID)->count();
                $donorCount = Donation::where('Campaign_ID', $campaign->Campaign_ID)->distinct('Donor_ID')->count('Donor_ID');

                return (object) [
                    'Campaign_ID' => $campaign->Campaign_ID,
                    'campaign_title' => $campaign->Title,
                    'organizer_name' => $campaign->organization->user->name ?? 'Unknown',
                    'Collected_Amount' => $campaign->Collected_Amount,
                    'Goal_Amount' => $campaign->Goal_Amount,
                    'donation_count' => $donationCount,
                    'donor_count' => $donorCount,
                    'achievement_percentage' => $campaign->Goal_Amount > 0 ? round(($campaign->Collected_Amount / $campaign->Goal_Amount) * 100, 2) : 0,
                ];
            });

        // Organization Leaderboard - Cross-database query (izzati + izzhilmy)
        // TODO: Refactor with proper application-level joins for production
        $this->organizationLeaderboard = Organization::with('user')
            ->limit(20) // Get more for aggregation
            ->get()
            ->map(function ($org) {
                $totalCampaigns = Campaign::where('Organization_ID', $org->Organization_ID)->count();
                $totalEvents = Event::where('Organizer_ID', $org->Organization_ID)->count();
                $totalRaised = Campaign::where('Organization_ID', $org->Organization_ID)->sum('Collected_Amount') ?? 0;
                $activeCampaigns = Campaign::where('Organization_ID', $org->Organization_ID)->where('Status', 'Active')->count();

                return (object) [
                    'Organization_ID' => $org->Organization_ID,
                    'organizer_name' => $org->user->name ?? 'Unknown',
                    'City' => $org->City,
                    'State' => $org->State,
                    'total_campaigns' => $totalCampaigns,
                    'total_events' => $totalEvents,
                    'total_raised' => $totalRaised,
                    'active_campaigns' => $activeCampaigns,
                ];
            })
            ->sortByDesc('total_raised')
            ->take(10)
            ->values();

        // Donor Insights - Cross-database query (hannah + izzhilmy)
        // TODO: Refactor with proper application-level joins for production
        $this->donorInsights = Donor::with('user')
            ->limit(20)
            ->get()
            ->map(function ($donor) {
                $donations = Donation::where('Donor_ID', $donor->Donor_ID)->get();
                $donationCount = $donations->count();
                $totalDonated = $donations->sum('Amount');
                $avgDonation = $donationCount > 0 ? $totalDonated / $donationCount : 0;
                $firstDonation = $donations->min('Donation_Date');
                $lastDonation = $donations->max('Donation_Date');
                $campaignsSupported = $donations->unique('Campaign_ID')->count();

                return (object) [
                    'donor_name' => $donor->user->name ?? 'Unknown',
                    'email' => $donor->user->email ?? '',
                    'donation_count' => $donationCount,
                    'total_donated' => $totalDonated,
                    'avg_donation' => round($avgDonation, 2),
                    'first_donation' => $firstDonation,
                    'last_donation' => $lastDonation,
                    'campaigns_supported' => $campaignsSupported,
                ];
            })
            ->sortByDesc('total_donated')
            ->take(10)
            ->values();

        // Event Metrics - Cross-database query (izzati + izzhilmy + sashvini)
        // TODO: Refactor with proper application-level joins for production
        $this->eventMetrics = Event::with('organization.user')
            ->whereIn('Status', ['Upcoming', 'Ongoing', 'Completed'])
            ->limit(20)
            ->get()
            ->map(function ($event) {
                $participations = DB::connection('sashvini')->table('event_participation')
                    ->where('Event_ID', $event->Event_ID)
                    ->get();

                $volunteersRegistered = $participations->unique('Volunteer_ID')->count();
                $totalHours = $participations->sum('Total_Hours');
                $fillRate = $event->Capacity > 0 ? round(($volunteersRegistered / $event->Capacity) * 100, 2) : 0;

                return (object) [
                    'Event_ID' => $event->Event_ID,
                    'event_title' => $event->Title,
                    'organizer_name' => $event->organization->user->name ?? 'Unknown',
                    'Capacity' => $event->Capacity,
                    'Status' => $event->Status,
                    'volunteers_registered' => $volunteersRegistered,
                    'total_hours' => $totalHours,
                    'fill_rate' => $fillRate,
                ];
            })
            ->sortByDesc('volunteers_registered')
            ->take(10)
            ->values();

        // Campaign Success Rate Analysis (izzati database)
        $campaignStats = DB::connection('izzati')->table('campaign')
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN "Collected_Amount" >= "Goal_Amount" THEN 1 ELSE 0 END) as successful'),
                DB::raw('SUM(CASE WHEN "Status" = \'Active\' THEN 1 ELSE 0 END) as active'),
                DB::raw('SUM(CASE WHEN "Status" = \'Pending\' THEN 1 ELSE 0 END) as pending'),
                DB::raw('AVG(CAST("Collected_Amount" AS DECIMAL) / NULLIF("Goal_Amount", 0) * 100) as avg_achievement_rate')
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

        // Allocation Efficiency - Cross-database query (izzati + hannah)
        // Load campaigns from izzati database
        $campaigns = DB::connection('izzati')->table('campaign')
            ->select('Campaign_ID', 'Title', 'Collected_Amount')
            ->where('Status', '!=', 'Pending')
            ->get();

        // Load allocation data from hannah database
        $allocations = DB::connection('hannah')->table('donation_allocation')
            ->select(
                'Campaign_ID',
                DB::raw('SUM(Amount_Allocated) as total_allocated'),
                DB::raw('COUNT(DISTINCT Recipient_ID) as recipient_count')
            )
            ->groupBy('Campaign_ID')
            ->get()
            ->keyBy('Campaign_ID');

        // Combine data in PHP (application-level join)
        $efficiencyData = $campaigns->map(function ($campaign) use ($allocations) {
            $allocation = $allocations->get($campaign->Campaign_ID);
            $allocatedAmount = $allocation ? (float) $allocation->total_allocated : 0;
            $recipientCount = $allocation ? (int) $allocation->recipient_count : 0;
            $collectedAmount = (float) $campaign->Collected_Amount;

            return (object) [
                'Campaign_ID' => $campaign->Campaign_ID,
                'Title' => $campaign->Title,
                'Collected_Amount' => $collectedAmount,
                'allocated_amount' => $allocatedAmount,
                'unallocated_amount' => $collectedAmount - $allocatedAmount,
                'allocation_percentage' => $collectedAmount > 0 ? round(($allocatedAmount / $collectedAmount) * 100, 2) : 0,
                'recipient_count' => $recipientCount,
            ];
        });

        // Apply sorting
        if ($this->efficiencyFilter === 'most_efficient') {
            $efficiencyData = $efficiencyData->sortByDesc('allocation_percentage');
        } elseif ($this->efficiencyFilter === 'least_efficient') {
            $efficiencyData = $efficiencyData->sortBy('allocation_percentage');
        } else {
            // Default sorting based on column selection
            if ($this->recipientSortDirection === 'desc') {
                $efficiencyData = $efficiencyData->sortByDesc($this->recipientSortBy);
            } else {
                $efficiencyData = $efficiencyData->sortBy($this->recipientSortBy);
            }
        }

        $this->allocationEfficiency = $efficiencyData->take(10)->values();

        // Recent Activity - Cross-database query (hannah + izzati + izzhilmy)
        // TODO: Refactor with proper application-level joins for production
        $activities = collect();

        if ($this->activityFilter === 'all' || $this->activityFilter === 'donation') {
            $recentDonations = Donation::with('donor.user', 'campaign')
                ->orderByDesc('created_at')
                ->limit($this->activityFilter === 'donation' ? 10 : 5)
                ->get()
                ->map(function ($donation) {
                    return (object) [
                        'type' => 'donation',
                        'actor' => $donation->donor->user->name ?? 'Unknown',
                        'description' => 'donated RM '.$donation->Amount.' to '.$donation->campaign->Title,
                        'activity_date' => $donation->created_at,
                    ];
                });
            $activities = $activities->merge($recentDonations);
        }

        if ($this->activityFilter === 'all' || $this->activityFilter === 'campaign') {
            $recentCampaigns = Campaign::with('organization.user')
                ->orderByDesc('created_at')
                ->limit($this->activityFilter === 'campaign' ? 10 : 5)
                ->get()
                ->map(function ($campaign) {
                    return (object) [
                        'type' => 'campaign',
                        'actor' => $campaign->organization->user->name ?? 'Unknown',
                        'description' => 'created campaign: '.$campaign->Title,
                        'activity_date' => $campaign->created_at,
                    ];
                });
            $activities = $activities->merge($recentCampaigns);
        }

        $this->recentActivity = $activities
            ->sortByDesc('activity_date')
            ->take(10)
            ->values();
    }

    private function loadChartData()
    {
        // Donations chart - last 90 days
        $donations = Donation::where('Donation_Date', '>=', now()->subDays(90))
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

        // Campaigns chart (last 90 days for performance)
        $campaigns = Campaign::where('created_at', '>=', now()->subDays(90))
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

        // Events chart (last 90 days for performance)
        $events = Event::where('created_at', '>=', now()->subDays(90))
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

        // User growth chart - last 90 days, all roles
        $users = User::where('created_at', '>=', now()->subDays(90))
            ->with('roles')
            ->orderBy('created_at')
            ->get();

        // Group users by date and role
        $groupedByDate = $users->groupBy(function ($user) {
            return Carbon::parse($user->created_at)->format('Y-m-d');
        });

        $this->userGrowthChart = $groupedByDate->map(function ($usersOnDate, $date) {
            // Count users by role
            $roleCounts = [
                'volunteer' => 0,
                'donor' => 0,
                'organizer' => 0,
                'public' => 0,
            ];

            foreach ($usersOnDate as $user) {
                foreach ($user->roles as $role) {
                    if (isset($roleCounts[$role->name])) {
                        $roleCounts[$role->name]++;
                    }
                }
            }

            return [
                'date' => $date,
                'volunteer' => $roleCounts['volunteer'],
                'donor' => $roleCounts['donor'],
                'organizer' => $roleCounts['organizer'],
                'public' => $roleCounts['public'],
            ];
        })->values()->toArray();

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

    public function sortRecipients($column)
    {
        if ($this->recipientSortBy === $column) {
            $this->recipientSortDirection = $this->recipientSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->recipientSortBy = $column;
            $this->recipientSortDirection = 'desc';
        }

        $this->loadStatistics();
    }

    public function updatedActivityFilter()
    {
        $this->loadStatistics();
    }

    public function updatedEfficiencyFilter()
    {
        $this->loadStatistics();
    }

    public function render()
    {
        return view('livewire.admin-dashboard');
    }
}
