<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Views\VolunteerHoursSummary;
use Livewire\Component;

class EventAnalytics extends Component
{
    public $topEvents;

    public $eventsByStatus;

    public $totalVolunteerHours;

    public $averageVolunteersPerEvent;

    public $totalEventParticipations;

    public $volunteerTierBreakdown;

    public $topVolunteers;

    public function mount()
    {
        $this->loadAnalytics();
    }

    public function loadAnalytics()
    {
        // Top events by volunteer count
        $this->topEvents = Event::withCount('volunteers')
            ->orderByDesc('volunteers_count')
            ->limit(10)
            ->get();

        // Events by status - using selectRaw for cross-database compatibility
        $this->eventsByStatus = Event::select('Status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('Status')
            ->get()
            ->pluck('count', 'Status')
            ->toArray();

        // Volunteer statistics - Using vw_volunteer_hours_summary view (sashvini database)
        $this->totalVolunteerHours = VolunteerHoursSummary::sum('verified_hours') ?? 0;

        $this->totalEventParticipations = VolunteerHoursSummary::sum('total_events') ?? 0;

        // Average volunteers per event - calculate at PHP level for better cross-database compatibility
        $eventVolunteerCounts = Event::withCount('volunteers')->get();

        if ($eventVolunteerCounts->isNotEmpty()) {
            $this->averageVolunteersPerEvent = round(
                $eventVolunteerCounts->avg('volunteers_count'),
                2
            );
        } else {
            $this->averageVolunteersPerEvent = 0;
        }

        // Volunteer tier breakdown - new metric from view
        $this->volunteerTierBreakdown = VolunteerHoursSummary::selectRaw('volunteer_tier, COUNT(*) as count')
            ->groupBy('volunteer_tier')
            ->get()
            ->pluck('count', 'volunteer_tier')
            ->toArray();

        // Top volunteers by hours
        $this->topVolunteers = VolunteerHoursSummary::topVolunteers(5)->get();
    }

    public function render()
    {
        return view('livewire.event-analytics');
    }
}
