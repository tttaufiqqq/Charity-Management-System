<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventParticipation;
use App\Services\Api\OrganizationApiService;
use Illuminate\Http\Request;

class EventApiController extends Controller
{
    protected $organizationService;

    public function __construct(OrganizationApiService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    /**
     * Get all events
     */
    public function index(Request $request)
    {
        $query = Event::query();

        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'upcoming':
                    $query->upcoming();
                    break;
                case 'completed':
                    $query->completed();
                    break;
                case 'ongoing':
                    $query->ongoing();
                    break;
            }
        }

        if ($request->has('organizer_id')) {
            $query->where('Organizer_ID', $request->organizer_id);
        }

        $events = $query->get();

        return response()->json($events);
    }

    /**
     * Get single event
     */
    public function show($id)
    {
        $event = Event::with('roles')->findOrFail($id);

        return response()->json($event);
    }

    /**
     * Create a new event
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Organizer_ID' => 'required|integer',
            'Title' => 'required|string|max:255',
            'Description' => 'required|string',
            'Location' => 'required|string',
            'Start_Date' => 'required|date',
            'End_Date' => 'required|date|after:Start_Date',
            'Capacity' => 'required|integer|min:1',
        ]);

        // Validate organization exists via API
        if (! $this->organizationService->exists($validated['Organizer_ID'])) {
            return response()->json(['error' => 'Organization not found'], 404);
        }

        $event = Event::create(array_merge($validated, [
            'Status' => 'Pending', // Requires admin approval
        ]));

        return response()->json($event, 201);
    }

    /**
     * Update event
     */
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $validated = $request->validate([
            'Title' => 'sometimes|string|max:255',
            'Description' => 'sometimes|string',
            'Location' => 'sometimes|string',
            'Start_Date' => 'sometimes|date',
            'End_Date' => 'sometimes|date|after:Start_Date',
            'Capacity' => 'sometimes|integer|min:1',
            'Status' => 'sometimes|in:Upcoming,Ongoing,Completed,Pending',
        ]);

        $event->update($validated);

        return response()->json($event);
    }

    /**
     * Get event participants
     */
    public function getParticipants($id)
    {
        $event = Event::findOrFail($id);
        $participants = EventParticipation::where('Event_ID', $id)
            ->with('volunteer')
            ->get();

        return response()->json($participants);
    }

    /**
     * Register volunteer for event
     */
    public function registerVolunteer(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $validated = $request->validate([
            'volunteer_id' => 'required|integer',
            'role_id' => 'required|integer',
        ]);

        // Check if already registered
        $existing = EventParticipation::where('Event_ID', $id)
            ->where('Volunteer_ID', $validated['volunteer_id'])
            ->first();

        if ($existing) {
            return response()->json(['error' => 'Volunteer already registered'], 400);
        }

        $participation = EventParticipation::create([
            'Event_ID' => $id,
            'Volunteer_ID' => $validated['volunteer_id'],
            'Role_ID' => $validated['role_id'],
            'Status' => 'Registered',
            'Total_Hours' => 0,
        ]);

        return response()->json($participation, 201);
    }

    /**
     * Cancel volunteer registration
     */
    public function cancelRegistration($eventId, $volunteerId)
    {
        $participation = EventParticipation::where('Event_ID', $eventId)
            ->where('Volunteer_ID', $volunteerId)
            ->firstOrFail();

        $participation->delete();

        return response()->json(['message' => 'Registration cancelled successfully']);
    }

    /**
     * Update participant hours
     */
    public function updateParticipantHours(Request $request, $eventId, $volunteerId)
    {
        $validated = $request->validate([
            'hours' => 'required|numeric|min:0',
        ]);

        $participation = EventParticipation::where('Event_ID', $eventId)
            ->where('Volunteer_ID', $volunteerId)
            ->firstOrFail();

        $participation->update(['Total_Hours' => $validated['hours']]);

        return response()->json($participation);
    }
}
