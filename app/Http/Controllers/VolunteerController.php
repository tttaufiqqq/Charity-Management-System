<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventParticipation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VolunteerController extends Controller
{
    /**
     * Display all available events for volunteers
     */
    public function browseEvents()
    {
        $volunteer = Auth::user()->volunteer;

        if (!$volunteer) {
            return redirect()->route('dashboard')->with('error', 'Volunteer profile not found.');
        }

        // Get all upcoming and ongoing events
        $events = Event::whereIn('Status', ['Upcoming', 'Ongoing'])
            ->orderBy('Start_Date', 'asc')
            ->paginate(12);

        // Get volunteer's registered event IDs
        $registeredEventIds = $volunteer->events()->pluck('event.Event_ID')->toArray();

        return view('volunteer-management.events.browse', compact('events', 'registeredEventIds'));
    }

    /**
     * Show a specific event details
     */
    public function showEvent(Event $event)
    {
        $volunteer = Auth::user()->volunteer;

        if (!$volunteer) {
            return redirect()->route('dashboard')->with('error', 'Volunteer profile not found.');
        }

        // Check if volunteer is already registered
        $isRegistered = $volunteer->events()
            ->where('event.Event_ID', $event->Event_ID)
            ->exists();

        // Get participation details if registered
        $participation = null;
        if ($isRegistered) {
            $participation = EventParticipation::where('Volunteer_ID', $volunteer->Volunteer_ID)
                ->where('Event_ID', $event->Event_ID)
                ->first();
        }

        // Check if event is full
        $isFull = false;
        if ($event->Capacity) {
            $currentVolunteers = $event->volunteers()->count();
            $isFull = $currentVolunteers >= $event->Capacity;
        }

        return view('volunteer-management.events.show', compact('event', 'isRegistered', 'participation', 'isFull'));
    }

    /**
     * Register volunteer for an event
     */
    public function registerForEvent(Event $event)
    {
        $volunteer = Auth::user()->volunteer;

        if (!$volunteer) {
            return redirect()->route('dashboard')->with('error', 'Volunteer profile not found.');
        }

        // Check if event is still accepting volunteers
        if (!in_array($event->Status, ['Upcoming', 'Ongoing'])) {
            return back()->with('error', 'This event is no longer accepting volunteers.');
        }

        // Check if already registered
        $alreadyRegistered = $volunteer->events()
            ->where('event.Event_ID', $event->Event_ID)
            ->exists();

        if ($alreadyRegistered) {
            return back()->with('error', 'You are already registered for this event.');
        }

        // Check capacity
        if ($event->Capacity) {
            $currentVolunteers = $event->volunteers()->count();
            if ($currentVolunteers >= $event->Capacity) {
                return back()->with('error', 'This event is full.');
            }
        }

        // Register volunteer
        DB::beginTransaction();
        try {
            EventParticipation::create([
                'Volunteer_ID' => $volunteer->Volunteer_ID,
                'Event_ID' => $event->Event_ID,
                'Status' => 'Registered',
                'Total_Hours' => 0,
            ]);

            DB::commit();

            return redirect()
                ->route('volunteer.events.show', ['event' => $event->Event_ID])
                ->with('success', 'Successfully registered for the event!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to register for event. Please try again.');
        }
    }

    /**
     * Cancel event registration
     */
    public function cancelRegistration(Event $event)
    {
        $volunteer = Auth::user()->volunteer;

        if (!$volunteer) {
            return redirect()->route('dashboard')->with('error', 'Volunteer profile not found.');
        }

        // Find participation using DB query builder for composite keys
        $deleted = DB::table('event_participation')
            ->where('Volunteer_ID', $volunteer->Volunteer_ID)
            ->where('Event_ID', $event->Event_ID)
            ->delete();

        if (!$deleted) {
            return back()->with('error', 'You are not registered for this event.');
        }

        return redirect()
            ->route('volunteer.events.browse')
            ->with('success', 'Event registration cancelled successfully.');
    }

    /**
     * Display volunteer's registered events
     */
    public function myEvents()
    {
        $volunteer = Auth::user()->volunteer;

        if (!$volunteer) {
            return redirect()->route('dashboard')->with('error', 'Volunteer profile not found.');
        }

        // Get all events the volunteer is registered for
        $registeredEvents = $volunteer->events()
            ->withPivot('Status', 'Total_Hours')
            ->orderBy('Start_Date', 'desc')
            ->paginate(10);

        // Calculate total hours
        $totalHours = EventParticipation::where('Volunteer_ID', $volunteer->Volunteer_ID)
            ->sum('Total_Hours');

        // Count events by status
        $upcomingCount = $volunteer->events()
            ->whereIn('event.Status', ['Upcoming', 'Ongoing'])
            ->count();

        $completedCount = $volunteer->events()
            ->where('event.Status', 'Completed')
            ->count();

        return view('volunteer-management.events.my-events', compact(
            'registeredEvents',
            'totalHours',
            'upcomingCount',
            'completedCount'
        ));
    }

    /**
     * Display volunteer dashboard
     */
    public function dashboard()
    {
        $volunteer = Auth::user()->volunteer;

        if (!$volunteer) {
            return redirect()->route('dashboard')->with('error', 'Volunteer profile not found.');
        }

        // Get upcoming events
        $upcomingEvents = $volunteer->events()
            ->whereIn('event.Status', ['Upcoming', 'Ongoing'])
            ->orderBy('Start_Date', 'asc')
            ->limit(5)
            ->get();

        // Calculate statistics
        $totalHours = EventParticipation::where('Volunteer_ID', $volunteer->Volunteer_ID)
            ->sum('Total_Hours');

        $totalEvents = $volunteer->events()->count();

        $completedEvents = $volunteer->events()
            ->where('event.Status', 'Completed')
            ->count();

        // Get recent activity
        $recentEvents = $volunteer->events()
            ->orderBy('event_participation.created_at', 'desc')
            ->limit(5)
            ->get();

        return view('volunteer-management.dashboard', compact(
            'volunteer',
            'upcomingEvents',
            'totalHours',
            'totalEvents',
            'completedEvents',
            'recentEvents'
        ));
    }
}
