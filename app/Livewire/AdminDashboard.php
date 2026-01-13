<?php

namespace App\Livewire;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Event;
use App\Models\Organization;
use App\Models\Recipient;
use App\Models\User;
use App\Models\Views\DonorDonationSummary;
use App\Models\Volunteer;
use App\Services\DatabaseProcedureService;
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

    // Procedure-based stats
    public $userRoleStats;

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
        // Extend execution time for analytics page (allow up to 120 seconds)
        set_time_limit(120);

        // Initialize with default values first
        $this->totalUsers = 0;
        $this->totalCampaigns = 0;
        $this->totalEvents = 0;
        $this->totalDonations = 0;
        $this->totalVolunteers = 0;
        $this->totalOrganizations = 0;
        $this->totalRaised = 0;
        $this->totalAllocated = 0;
        $this->totalRecipientsHelped = 0;
        $this->pendingApprovals = ['campaigns' => 0, 'events' => 0, 'recipients' => 0];
        $this->userRoleStats = [];

        // Basic Statistics (with timeout protection - catch Throwable for fatal errors)
        try {
            $this->totalUsers = User::count();
        } catch (\Throwable $e) {
        }

        try {
            $this->totalCampaigns = Campaign::count();
        } catch (\Throwable $e) {
        }

        try {
            $this->totalEvents = Event::count();
        } catch (\Throwable $e) {
        }

        try {
            $this->totalDonations = Donation::count();
        } catch (\Throwable $e) {
        }

        try {
            $this->totalVolunteers = Volunteer::count();
        } catch (\Throwable $e) {
        }

        try {
            $this->totalOrganizations = Organization::count();
        } catch (\Throwable $e) {
        }

        // Financial (with timeout protection)
        try {
            $this->totalRaised = Campaign::sum('Collected_Amount') ?? 0;
        } catch (\Throwable $e) {
        }

        try {
            $this->totalAllocated = DB::connection('hannah')->table('donation_allocation')->sum('Amount_Allocated') ?? 0;
        } catch (\Throwable $e) {
        }

        // Recipients helped
        try {
            $this->totalRecipientsHelped = DB::connection('hannah')->table('donation_allocation')
                ->distinct('Recipient_ID')
                ->count('Recipient_ID');
        } catch (\Throwable $e) {
        }

        // Pending approvals
        try {
            $this->pendingApprovals = [
                'campaigns' => Campaign::where('Status', 'Pending')->count(),
                'events' => Event::where('Status', 'Pending')->count(),
                'recipients' => Recipient::where('Status', 'Pending')->count(),
            ];
        } catch (\Throwable $e) {
        }

        // Load user role statistics using stored procedure
        try {
            $this->userRoleStats = DatabaseProcedureService::getUserRoleStats();
        } catch (\Throwable $e) {
        }

        // Load advanced analytics based on active tab
        $this->loadAdvancedAnalytics();

        // Load charts
        $this->loadChartData();
    }

    private function loadAdvancedAnalytics()
    {
        // Top Performing Campaigns (with timeout protection)
        try {
            $this->topCampaigns = Campaign::with('organization.user')
                ->where('Status', 'Active')
                ->orderByDesc('Collected_Amount')
                ->limit(10)
                ->get()
                ->map(function ($campaign) {
                    $donationCount = 0;
                    $donorCount = 0;
                    try {
                        $donationCount = Donation::where('Campaign_ID', $campaign->Campaign_ID)->count();
                        $donorCount = Donation::where('Campaign_ID', $campaign->Campaign_ID)->distinct('Donor_ID')->count('Donor_ID');
                    } catch (Throwable $e) {
                        // Donation database unavailable
                    }

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
        } catch (Throwable $e) {
            $this->topCampaigns = collect();
        }

        // Organization Leaderboard (with timeout protection)
        try {
            $this->organizationLeaderboard = Organization::with('user')
                ->limit(20)
                ->get()
                ->map(function ($org) {
                    $totalCampaigns = 0;
                    $totalEvents = 0;
                    $totalRaised = 0;
                    $activeCampaigns = 0;
                    try {
                        $totalCampaigns = Campaign::where('Organization_ID', $org->Organization_ID)->count();
                        $totalRaised = Campaign::where('Organization_ID', $org->Organization_ID)->sum('Collected_Amount') ?? 0;
                        $activeCampaigns = Campaign::where('Organization_ID', $org->Organization_ID)->where('Status', 'Active')->count();
                    } catch (Throwable $e) {
                    }
                    try {
                        $totalEvents = Event::where('Organizer_ID', $org->Organization_ID)->count();
                    } catch (Throwable $e) {
                    }

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
        } catch (Throwable $e) {
            $this->organizationLeaderboard = collect();
        }

        // Donor Insights (with timeout protection)
        try {
            $this->donorInsights = DonorDonationSummary::topDonors(10)
                ->get()
                ->map(function ($donor) {
                    return (object) [
                        'donor_name' => $donor->donor_name,
                        'email' => '',
                        'donation_count' => $donor->completed_donation_count,
                        'total_donated' => $donor->cached_total_donated,
                        'avg_donation' => round($donor->avg_donation_amount ?? 0, 2),
                        'first_donation' => $donor->first_donation_date,
                        'last_donation' => $donor->last_donation_date,
                        'campaigns_supported' => $donor->campaigns_supported,
                        'donor_tier' => $donor->donor_tier,
                    ];
                });
        } catch (Throwable $e) {
            $this->donorInsights = collect();
        }

        // Event Metrics (with timeout protection)
        try {
            $this->eventMetrics = Event::with('organization.user')
                ->whereIn('Status', ['Upcoming', 'Ongoing', 'Completed'])
                ->limit(20)
                ->get()
                ->map(function ($event) {
                    $volunteersRegistered = 0;
                    $totalHours = 0;
                    try {
                        $participations = DB::connection('sashvini')->table('event_participation')
                            ->where('Event_ID', $event->Event_ID)
                            ->get();
                        $volunteersRegistered = $participations->unique('Volunteer_ID')->count();
                        $totalHours = $participations->sum('Total_Hours');
                    } catch (Throwable $e) {
                    }
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
        } catch (Throwable $e) {
            $this->eventMetrics = collect();
        }

        // Campaign Success Rate Analysis (with timeout protection)
        try {
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
                'total' => $campaignStats->total ?? 0,
                'successful' => $campaignStats->successful ?? 0,
                'active' => $campaignStats->active ?? 0,
                'pending' => $campaignStats->pending ?? 0,
                'success_rate' => ($campaignStats->total ?? 0) > 0 ? round((($campaignStats->successful ?? 0) / $campaignStats->total) * 100, 2) : 0,
                'avg_achievement_rate' => round($campaignStats->avg_achievement_rate ?? 0, 2),
            ];
        } catch (Throwable $e) {
            $this->campaignSuccessRate = ['total' => 0, 'successful' => 0, 'active' => 0, 'pending' => 0, 'success_rate' => 0, 'avg_achievement_rate' => 0];
        }

        // Allocation Efficiency (with timeout protection)
        try {
            $campaigns = DB::connection('izzati')->table('campaign')
                ->select('Campaign_ID', 'Title', 'Collected_Amount')
                ->where('Status', '!=', 'Pending')
                ->get();

            $allocations = collect();
            try {
                $allocations = DB::connection('hannah')->table('donation_allocation')
                    ->select(
                        'Campaign_ID',
                        DB::raw('SUM(Amount_Allocated) as total_allocated'),
                        DB::raw('COUNT(DISTINCT Recipient_ID) as recipient_count')
                    )
                    ->groupBy('Campaign_ID')
                    ->get()
                    ->keyBy('Campaign_ID');
            } catch (Throwable $e) {
            }

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

            if ($this->efficiencyFilter === 'most_efficient') {
                $efficiencyData = $efficiencyData->sortByDesc('allocation_percentage');
            } elseif ($this->efficiencyFilter === 'least_efficient') {
                $efficiencyData = $efficiencyData->sortBy('allocation_percentage');
            } else {
                if ($this->recipientSortDirection === 'desc') {
                    $efficiencyData = $efficiencyData->sortByDesc($this->recipientSortBy);
                } else {
                    $efficiencyData = $efficiencyData->sortBy($this->recipientSortBy);
                }
            }

            $this->allocationEfficiency = $efficiencyData->take(10)->values();
        } catch (Throwable $e) {
            $this->allocationEfficiency = collect();
        }

        // Recent Activity (with timeout protection)
        $activities = collect();

        if ($this->activityFilter === 'all' || $this->activityFilter === 'donation') {
            try {
                $recentDonations = Donation::with('donor.user', 'campaign')
                    ->orderByDesc('created_at')
                    ->limit($this->activityFilter === 'donation' ? 10 : 5)
                    ->get()
                    ->map(function ($donation) {
                        return (object) [
                            'type' => 'donation',
                            'actor' => $donation->donor->user->name ?? 'Unknown',
                            'description' => 'donated RM '.$donation->Amount.' to '.($donation->campaign->Title ?? 'Unknown'),
                            'activity_date' => $donation->created_at,
                        ];
                    });
                $activities = $activities->merge($recentDonations);
            } catch (Throwable $e) {
            }
        }

        if ($this->activityFilter === 'all' || $this->activityFilter === 'campaign') {
            try {
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
            } catch (Throwable $e) {
            }
        }

        $this->recentActivity = $activities
            ->sortByDesc('activity_date')
            ->take(10)
            ->values();
    }

    private function loadChartData()
    {
        // Donations chart (with timeout protection)
        try {
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
        } catch (Throwable $e) {
            $this->donationsChart = [];
        }

        // Campaigns chart (with timeout protection)
        try {
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
        } catch (Throwable $e) {
            $this->campaignsChart = [];
        }

        // Events chart (with timeout protection)
        try {
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
        } catch (Throwable $e) {
            $this->eventsChart = [];
        }

        // User growth chart (with timeout protection)
        try {
            $users = User::where('created_at', '>=', now()->subDays(90))
                ->with('roles')
                ->orderBy('created_at')
                ->get();

            $groupedByDate = $users->groupBy(function ($user) {
                return Carbon::parse($user->created_at)->format('Y-m-d');
            });

            $this->userGrowthChart = $groupedByDate->map(function ($usersOnDate, $date) {
                $roleCounts = ['volunteer' => 0, 'donor' => 0, 'organizer' => 0, 'public' => 0];

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
        } catch (Throwable $e) {
            $this->userGrowthChart = [];
        }

        // Campaign status chart (with timeout protection)
        try {
            $statusCounts = Campaign::select('Status', DB::raw('COUNT(*) as count'))
                ->groupBy('Status')
                ->get();

            $this->campaignStatusChart = $statusCounts->map(function ($stat) {
                return [
                    'status' => $stat->Status,
                    'count' => $stat->count,
                ];
            })->toArray();
        } catch (Throwable $e) {
            $this->campaignStatusChart = [];
        }
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
