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
            ->orderBy('volunteers_count', 'desc')
            ->limit(10)
            ->get();

        // Events by status
        $this->eventsByStatus = Event::select('Status', DB::raw('COUNT(*) as count'))
            ->groupBy('Status')
            ->get()
            ->pluck('count', 'Status')
            ->toArray();

        // Volunteer statistics
        $this->totalVolunteerHours = EventParticipation::sum('Total_Hours');
        $this->totalEventParticipations = EventParticipation::count();
        $this->averageVolunteersPerEvent = Event::withCount('volunteers')
            ->avg('volunteers_count') ?? 0;
    }

    public function render()
    {
        return view('livewire.event-analytics');
    }
}
