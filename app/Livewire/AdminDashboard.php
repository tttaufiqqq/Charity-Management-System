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

    private function dbDateCast($column)
    {
        $driver = DB::getDriverName();

        return match ($driver) {
            'pgsql'  => 'CAST("' . $column . '" AS DATE)',
            'sqlsrv' => 'CAST(' . $column . ' AS DATE)',
            default  => 'DATE(' . $column . ')',  // MySQL/MariaDB
        };
    }

    private function quoteColumn($column)
    {
        $driver = DB::getDriverName();

        return match ($driver) {
            'pgsql'  => '"' . $column . '"',       // PG requires quotes for PascalCase
            default  => $column,                   // MySQL / SQL Server OK
        };
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
        $this->totalRaised = Campaign::sum('Collected_Amount');
        $this->totalAllocated = DB::table('donation_allocation')->sum('Amount_Allocated');

        // Pending approvals
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
        // Proper cross-DB date casting
        $donationDateCast = $this->dbDateCast('Donation_Date');
        $createdDateCast  = $this->dbDateCast('created_at');

        $amountCol = $this->quoteColumn('Amount');

        // Donations chart
        $this->donationsChart = Donation::where('Donation_Date', '>=', $startDate)
            ->selectRaw("$donationDateCast AS date, SUM($amountCol) AS total")
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date'   => $row->date,
                'amount' => (float) $row->total
            ])
            ->toArray();

        // Campaigns chart
        $countCol = "COUNT(*)";

        $this->campaignsChart = Campaign::where('created_at', '>=', $startDate)
            ->selectRaw("$createdDateCast AS date, $countCol AS count")
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date'  => $row->date,
                'count' => $row->count
            ])
            ->toArray();

        // Events chart
        $this->eventsChart = Event::where('created_at', '>=', $startDate)
            ->selectRaw("$createdDateCast AS date, $countCol AS count")
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date'  => $row->date,
                'count' => $row->count
            ])
            ->toArray();

        // User growth
        $this->userGrowthChart = User::where('created_at', '>=', $startDate)
            ->selectRaw("$createdDateCast AS date, $countCol AS count")
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date'  => $row->date,
                'count' => $row->count
            ])
            ->toArray();
    }


    public function render()
    {
        return view('livewire.admin-dashboard');
    }
}
