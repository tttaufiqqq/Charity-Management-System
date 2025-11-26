<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventManagementController extends Controller
{
    // ========================================
    // CAMPAIGN METHODS
    // ========================================

    /**
     * Display all campaigns for the logged-in organizer
     */
    public function indexCampaigns()
    {
        $organization = Auth::user()->organization;

        if (!$organization) {
            return redirect()->route('dashboard')->with('error', 'Organization profile not found.');
        }

        $campaigns = Campaign::where('Organization_ID', $organization->Organization_ID)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('event-management.campaigns.index', compact('campaigns'));
    }

    /**
     * Show form to create a new campaign
     */
    public function createCampaign()
    {
        return view('event-management.campaigns.create');
    }

    /**
     * Store a new campaign
     */
    public function storeCampaign(Request $request)
    {
        $organization = Auth::user()->organization;

        if (!$organization) {
            return redirect()->route('dashboard')->with('error', 'Organization profile not found.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'goal_amount' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
        ]);

        Campaign::create([
            'Organization_ID' => $organization->Organization_ID,
            'Title' => $validated['title'],
            'Description' => $validated['description'],
            'Goal_Amount' => $validated['goal_amount'],
            'Collected_Amount' => 0,
            'Start_Date' => $validated['start_date'],
            'End_Date' => $validated['end_date'],
            'Status' => 'Active',
        ]);

        return redirect()->route('campaigns.index')->with('success', 'Campaign created successfully!');
    }

    /**
     * Show a specific campaign
     */
    public function showCampaign(Campaign $campaign)
    {
        // Check if user owns this campaign
        if ($campaign->Organization_ID !== Auth::user()->organization->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        return view('event-management.campaigns.show', compact('campaign'));
    }

    /**
     * Show form to edit a campaign
     */
    public function editCampaign(Campaign $campaign)
    {
        // Check if user owns this campaign
        if ($campaign->Organization_ID !== Auth::user()->organization->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        return view('event-management.campaigns.edit', compact('campaign'));
    }

    /**
     * Update a campaign
     */
    public function updateCampaign(Request $request, Campaign $campaign)
    {
        // Check if user owns this campaign
        if ($campaign->Organization_ID !== Auth::user()->organization->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'goal_amount' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', 'in:Active,Completed,Cancelled'],
        ]);

        $campaign->update([
            'Title' => $validated['title'],
            'Description' => $validated['description'],
            'Goal_Amount' => $validated['goal_amount'],
            'Start_Date' => $validated['start_date'],
            'End_Date' => $validated['end_date'],
            'Status' => $validated['status'],
        ]);

        return redirect()->route('campaigns.show', $campaign->Campaign_ID)
            ->with('success', 'Campaign updated successfully!');
    }

    /**
     * Delete a campaign
     */
    public function destroyCampaign(Campaign $campaign)
    {
        // Check if user owns this campaign
        if ($campaign->Organization_ID !== Auth::user()->organization->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        $campaign->delete();

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign deleted successfully!');
    }

    // ========================================
    // EVENT METHODS
    // ========================================

    /**
     * Display all events for the logged-in organizer
     */
    public function indexEvents()
    {
        $organization = Auth::user()->organization;

        if (!$organization) {
            return redirect()->route('dashboard')->with('error', 'Organization profile not found.');
        }

        $events = Event::where('Organizer_ID', $organization->Organization_ID)
            ->orderBy('Start_Date', 'desc')
            ->paginate(10);

        return view('event-management.events.index', compact('events'));
    }

    /**
     * Show form to create a new event
     */
    public function createEvent()
    {
        return view('event-management.events.create');
    }

    /**
     * Store a new event
     */
    public function storeEvent(Request $request)
    {
        $organization = Auth::user()->organization;

        if (!$organization) {
            return redirect()->route('dashboard')->with('error', 'Organization profile not found.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'capacity' => ['nullable', 'integer', 'min:1'],
        ]);

        Event::create([
            'Organizer_ID' => $organization->Organization_ID,
            'Title' => $validated['title'],
            'Description' => $validated['description'],
            'Location' => $validated['location'],
            'Start_Date' => $validated['start_date'],
            'End_Date' => $validated['end_date'],
            'Capacity' => $validated['capacity'],
            'Status' => 'Upcoming',
        ]);

        return redirect()->route('events.index')->with('success', 'Event created successfully!');
    }

    /**
     * Show a specific event
     */
    public function showEvent(Event $event)
    {
        // Check if user owns this event
        if ($event->Organizer_ID !== Auth::user()->organization->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        $volunteers = $event->volunteers()->paginate(10);

        return view('event-management.events.show', compact('event', 'volunteers'));
    }

    /**
     * Show form to edit an event
     */
    public function editEvent(Event $event)
    {
        // Check if user owns this event
        if ($event->Organizer_ID !== Auth::user()->organization->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        return view('event-management.events.edit', compact('event'));
    }

    /**
     * Update an event
     */
    public function updateEvent(Request $request, Event $event)
    {
        // Check if user owns this event
        if ($event->Organizer_ID !== Auth::user()->organization->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'in:Upcoming,Ongoing,Completed,Cancelled'],
        ]);

        $event->update([
            'Title' => $validated['title'],
            'Description' => $validated['description'],
            'Location' => $validated['location'],
            'Start_Date' => $validated['start_date'],
            'End_Date' => $validated['end_date'],
            'Capacity' => $validated['capacity'],
            'Status' => $validated['status'],
        ]);

        return redirect()->route('events.show', $event->Event_ID)
            ->with('success', 'Event updated successfully!');
    }

    /**
     * Delete an event
     */
    public function destroyEvent(Event $event)
    {
        // Check if user owns this event
        if ($event->Organizer_ID !== Auth::user()->organization->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        $event->delete();

        return redirect()->route('events.index')
            ->with('success', 'Event deleted successfully!');
    }
}
