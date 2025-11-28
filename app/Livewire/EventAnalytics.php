<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Event;
use App\Models\EventParticipation;
use Illuminate\Support\Facades\DB;

class EventAnalytics extends Component
{
    public $topEvents;
    public $eventsByStatus;
    public $totalVolunteerHours;
    public $averageVolunteersPerEvent;
    public $totalEventParticipations;

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

        // Volunteer statistics
        // Using Laravel's sum() method which handles column names across all RDBMS
        $this->totalVolunteerHours = EventParticipation::sum('Total_Hours') ?? 0;

        $this->totalEventParticipations = EventParticipation::count();

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
    }

    public function render()
    {
        return view('livewire.event-analytics');
    }
}
