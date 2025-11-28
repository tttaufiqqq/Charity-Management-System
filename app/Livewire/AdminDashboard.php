<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Campaign;
use App\Models\Event;
use App\Models\Donation;
use App\Models\Volunteer;
use App\Models\Recipient;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboard extends Component
{
    public $dateRange = '30'; // days
    public $selectedMetric = 'overview';

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

    // Charts
    public $donationsChart;
    public $campaignsChart;
    public $eventsChart;
    public $userGrowthChart;

    public function mount()
    {
        $this->loadStatistics();
    }

    public function updatedDateRange()
    {
        $this->loadStatistics();
    }

    public function loadStatistics()
    {
        $days = (int) $this->dateRange;
        $startDate = now()->subDays($days);

        // Basic Statistics - using Laravel's count() method
        $this->totalUsers = User::count();
        $this->totalCampaigns = Campaign::count();
        $this->totalEvents = Event::count();
        $this->totalDonations = Donation::count();
        $this->totalVolunteers = Volunteer::count();
        $this->totalOrganizations = Organization::count();

        // Financial - using Laravel's sum() method
        $this->totalRaised = Campaign::sum('Collected_Amount') ?? 0;
        $this->totalAllocated = DB::table('donation_allocation')->sum('Amount_Allocated') ?? 0;

        // Pending approvals - using Laravel's where() and count()
        $this->pendingApprovals = [
            'campaigns'  => Campaign::where('Status', 'Pending')->count(),
            'events'     => Event::where('Status', 'Pending')->count(),
            'recipients' => Recipient::where('Status', 'Pending')->count(),
        ];

        // Load charts
        $this->loadChartData($startDate);
    }

    private function loadChartData($startDate)
    {
        // Donations chart - process at PHP level for cross-database compatibility
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
                    'amount' => (float) $group->sum('Amount')
                ];
            })
            ->values()
            ->toArray();

        // Campaigns chart - process at PHP level
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
                    'count' => $group->count()
                ];
            })
            ->values()
            ->toArray();

        // Events chart - process at PHP level
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
                    'count' => $group->count()
                ];
            })
            ->values()
            ->toArray();

        // User growth chart - process at PHP level
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
                    'count' => $group->count()
                ];
            })
            ->values()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin-dashboard');
    }
}
