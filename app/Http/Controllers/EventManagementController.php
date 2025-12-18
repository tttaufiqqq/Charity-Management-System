<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Event;
use App\Models\EventRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        if (! $organization) {
            return redirect()->route('dashboard')->with('error', 'Organization profile not found.');
        }

        $campaigns = Campaign::where('Organization_ID', $organization->Organization_ID)
            ->where('Status', 'Active')
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

        if (! $organization) {
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
            'Status' => 'Pending',
        ]);

        return redirect()->route('campaigns.index')->with('success', 'Campaign created successfully! It will be visible once admin approved it.');
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

        if (! $organization) {
            return redirect()->route('dashboard')->with('error', 'Organization profile not found.');
        }

        $events = Event::where('Organizer_ID', $organization->Organization_ID)
            ->where('Status', 'Upcoming')
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

        if (! $organization) {
            return redirect()->route('dashboard')->with('error', 'Organization profile not found.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*.name' => ['required', 'string', 'max:255'],
            'roles.*.description' => ['nullable', 'string'],
            'roles.*.volunteers_needed' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($validated, $organization) {
            // Create the event
            $event = Event::create([
                'Organizer_ID' => $organization->Organization_ID,
                'Title' => $validated['title'],
                'Description' => $validated['description'],
                'Location' => $validated['location'],
                'Start_Date' => $validated['start_date'],
                'End_Date' => $validated['end_date'],
                'Capacity' => $validated['capacity'],
                'Status' => 'Pending',
            ]);

            // Create roles for the event
            foreach ($validated['roles'] as $role) {
                EventRole::create([
                    'Event_ID' => $event->Event_ID,
                    'Role_Name' => $role['name'],
                    'Role_Description' => $role['description'] ?? null,
                    'Volunteers_Needed' => $role['volunteers_needed'],
                    'Volunteers_Filled' => 0,
                ]);
            }
        });

        return redirect()->route('events.index')->with('success', 'Event created successfully! It will be visible once admin approve it');
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

    /**
     * Show volunteers for an event (organizer view)
     */
    /**
     * Show volunteers for an event (organizer view)
     */
    public function manageVolunteers(Event $event)
    {
        // Check if user owns this event
        if ($event->Organizer_ID !== Auth::user()->organization->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        $volunteers = $event->volunteers()
            ->withPivot('Status', 'Total_Hours', 'created_at')
            ->orderBy('event_participation.created_at', 'desc')
            ->get();

        return view('event-management.events.manage-volunteers', compact('event', 'volunteers'));
    }

    /**
     * Update volunteer attendance and hours
     */
    public function updateVolunteerHours(Request $request, Event $event, $volunteerId)
    {
        // Check if user owns this event
        if ($event->Organizer_ID !== Auth::user()->organization->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:Registered,Attended,No-Show,Cancelled'],
            'total_hours' => ['required', 'numeric', 'min:0', 'max:24'],
        ]);

        DB::beginTransaction();

        try {
            // Update volunteer participation
            DB::table('event_participation')
                ->where('Event_ID', $event->Event_ID)
                ->where('Volunteer_ID', $volunteerId)
                ->update([
                    'Status' => $validated['status'],
                    'Total_Hours' => $validated['total_hours'],
                    'updated_at' => now(),
                ]);

            // Check if event should be marked as completed
            // If event end date has passed and we're updating volunteer hours, mark as completed
            if ($event->End_Date < now() && $event->Status !== 'Completed') {
                $event->update(['Status' => 'Completed']);
            }

            DB::commit();

            return back()->with('success', 'Volunteer hours updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to update volunteer hours.');
        }
    }

    /**
     * Auto-calculate and update all volunteer hours for completed event
     */
    public function autoCalculateHours(Event $event)
    {
        // Check if user owns this event
        if ($event->Organizer_ID !== Auth::user()->organization->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        // Can only auto-calculate for completed events
        if ($event->Status !== 'Completed') {
            return back()->with('error', 'Can only calculate hours for completed events.');
        }

        // Calculate event duration in hours
        $eventHours = $event->Start_Date->diffInHours($event->End_Date);

        // Update all volunteers with "Registered" or "Attended" status
        $updated = DB::table('event_participation')
            ->where('Event_ID', $event->Event_ID)
            ->whereIn('Status', ['Registered', 'Attended'])
            ->update([
                'Status' => 'Attended',
                'Total_Hours' => $eventHours,
                'updated_at' => now(),
            ]);

        return back()->with('success', "Auto-calculated {$eventHours} hours for {$updated} volunteers!");
    }

    /**
     * Bulk update volunteer statuses
     */
    public function bulkUpdateVolunteers(Request $request, Event $event)
    {
        // Check if user owns this event
        if ($event->Organizer_ID !== Auth::user()->organization->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'volunteer_ids' => ['required', 'array'],
            'volunteer_ids.*' => ['required', 'integer'],
            'status' => ['required', 'in:Attended,No-Show'],
            'hours' => ['nullable', 'numeric', 'min:0', 'max:24'],
        ]);

        $hours = $validated['hours'] ?? $event->Start_Date->diffInHours($event->End_Date);

        DB::table('event_participation')
            ->where('Event_ID', $event->Event_ID)
            ->whereIn('Volunteer_ID', $validated['volunteer_ids'])
            ->update([
                'Status' => $validated['status'],
                'Total_Hours' => $validated['status'] === 'Attended' ? $hours : 0,
                'updated_at' => now(),
            ]);

        $count = count($validated['volunteer_ids']);

        return back()->with('success', "Updated {$count} volunteers!");
    }

    // ========================================
    // ADMIN APPROVAL METHODS
    // ========================================

    /**
     * Display pending campaigns for admin approval
     */
    public function adminPendingCampaigns()
    {
        $campaigns = Campaign::where('Status', 'Pending')
            ->with('organization.user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('event-management.admin.campaigns-pending', compact('campaigns'));
    }

    /**
     * Display pending events for admin approval
     */
    public function adminPendingEvents()
    {
        $events = Event::where('Status', 'Pending')
            ->with('organization.user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('event-management.admin.events-pending', compact('events'));
    }

    /**
     * Approve a campaign
     */
    public function adminApproveCampaign(Campaign $campaign)
    {
        if ($campaign->Status !== 'Pending') {
            return back()->with('error', 'Campaign is not pending approval.');
        }

        $campaign->update(['Status' => 'Active']);

        return back()->with('success', 'Campaign approved successfully!');
    }

    /**
     * Reject a campaign
     */
    public function adminRejectCampaign(Request $request, Campaign $campaign)
    {
        if ($campaign->Status !== 'Pending') {
            return back()->with('error', 'Campaign is not pending approval.');
        }

        $campaign->update(['Status' => 'Rejected']);

        return back()->with('success', 'Campaign rejected.');
    }

    /**
     * Approve an event
     */
    public function adminApproveEvent(Event $event)
    {
        if ($event->Status !== 'Pending') {
            return back()->with('error', 'Event is not pending approval.');
        }

        $event->update(['Status' => 'Upcoming']);

        return back()->with('success', 'Event approved successfully!');
    }

    /**
     * Reject an event
     */
    public function adminRejectEvent(Request $request, Event $event)
    {
        if ($event->Status !== 'Pending') {
            return back()->with('error', 'Event is not pending approval.');
        }

        $event->update(['Status' => 'Rejected']);

        return back()->with('success', 'Event rejected.');
    }

    /**
     * Admin dashboard - overview of pending items
     */
    public function adminDashboard()
    {
        $pendingCampaigns = Campaign::where('Status', 'Pending')->count();
        $pendingEvents = Event::where('Status', 'Pending')->count();
        $pendingRecipients = \App\Models\Recipient::where('Status', 'Pending')->count();

        $recentCampaigns = Campaign::where('Status', 'Pending')
            ->with('organization.user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentEvents = Event::where('Status', 'Pending')
            ->with('organization.user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Platform overview statistics
        $stats = [
            'totalRaised' => DB::table('donation')
                ->where('Payment_Status', 'Completed')
                ->sum('Amount'),
            'totalDonations' => DB::table('donation')
                ->where('Payment_Status', 'Completed')
                ->count(),
            'activeCampaigns' => Campaign::where('Status', 'Active')->count(),
            'totalCampaigns' => Campaign::count(),
            'activeEvents' => Event::whereIn('Status', ['Upcoming', 'Ongoing'])->count(),
            'totalVolunteers' => DB::table('event_participation')
                ->distinct('Volunteer_ID')
                ->count(),
            'recipientsHelped' => DB::table('donation_allocation')
                ->distinct('Recipient_ID')
                ->count(),
            'totalAllocated' => DB::table('donation_allocation')
                ->sum('Amount_Allocated'),
        ];

        return view('event-management.admin.dashboard', compact(
            'pendingCampaigns',
            'pendingEvents',
            'pendingRecipients',
            'recentCampaigns',
            'recentEvents',
            'stats'
        ));
    }
}
