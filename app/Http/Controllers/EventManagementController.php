<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Event;
use App\Models\EventParticipation;
use App\Models\EventRole;
use App\Models\Volunteer;
use App\Traits\ValidatesCrossDatabaseReferences;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventManagementController extends Controller
{
    use ValidatesCrossDatabaseReferences;
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
            return redirect()->route('dashboard')->with('error', 'Organization profile not found. (Database: Izzati)');
        }

        // Fetch all campaigns (approved and pending)
        $campaigns = Campaign::where('Organization_ID', $organization->Organization_ID)
            ->withCount([
                'recipientSuggestions',
                'recipientSuggestions as pending_suggestions_count' => function ($query) {
                    $query->where('Status', 'Pending');
                },
            ])
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
            return redirect()->route('dashboard')->with('error', 'Organization profile not found. (Database: Izzati)');
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

        return redirect()->route('campaigns.index')->with('success', 'Campaign created successfully! It will be visible once admin approved it. (Database: Izzati)');
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

        // Security: Get minimum goal amount (must be at least the collected amount)
        $minGoalAmount = $campaign->Collected_Amount;

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'goal_amount' => ['required', 'numeric', 'min:'.$minGoalAmount],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', 'in:Active,Completed,Cancelled'],
        ], [
            'goal_amount.min' => 'Goal amount cannot be less than the collected amount (RM '.number_format($minGoalAmount, 2).').',
        ]);

        // Security: Double-check goal amount constraint
        if ($validated['goal_amount'] < $campaign->Collected_Amount) {
            return redirect()->back()
                ->with('error', 'Security Error: Goal amount cannot be reduced below collected donations.')
                ->withInput();
        }

        $campaign->update([
            'Title' => $validated['title'],
            'Description' => $validated['description'],
            'Goal_Amount' => $validated['goal_amount'],
            'Start_Date' => $validated['start_date'],
            'End_Date' => $validated['end_date'],
            'Status' => $validated['status'],
        ]);

        return redirect()->route('campaigns.show', $campaign->Campaign_ID)
            ->with('success', 'Campaign updated successfully! (Database: Izzati)');
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

        // Security: Prevent deletion of campaigns with donations
        if ($campaign->Collected_Amount > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete campaign with donations. Collected amount: RM '.number_format($campaign->Collected_Amount, 2));
        }

        // Security: Prevent deletion of campaigns with existing donations records
        if ($campaign->donations()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete campaign that has received donations.');
        }

        // Security: Prevent deletion of campaigns with fund allocations
        if ($campaign->donationAllocations()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete campaign with existing fund allocations.');
        }

        $campaign->delete();

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign deleted successfully! (Database: Izzati)');
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
            return redirect()->route('dashboard')->with('error', 'Organization profile not found. (Database: Izzati)');
        }

        // Fetch all events (approved and pending)
        $allEvents = Event::where('Organizer_ID', $organization->Organization_ID)
            ->orderBy('created_at', 'desc')
            ->get();

        // Separate by approval status
        $approvedEvents = $allEvents->whereIn('Status', ['Upcoming', 'Ongoing', 'Completed']);
        $pendingEvents = $allEvents->where('Status', 'Pending');

        // Statistics
        $stats = [
            'total_events' => $allEvents->count(),
            'approved_events' => $approvedEvents->count(),
            'pending_events' => $pendingEvents->count(),
        ];

        return view('event-management.events.index', compact(
            'allEvents',
            'approvedEvents',
            'pendingEvents',
            'stats'
        ));
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
            return redirect()->route('dashboard')->with('error', 'Organization profile not found. (Database: Izzati)');
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

            // Validate event was created successfully before creating roles (cross-database validation)
            $this->validateEventExists($event->Event_ID);

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

        return redirect()->route('events.index')->with('success', 'Event created successfully! It will be visible once admin approve it (Database: Izzati)');
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

        // Load roles only (don't eager load volunteers - cross-database issue)
        $event->load('roles');

        // Calculate statistics
        $totalCapacity = $event->roles->sum('Volunteers_Needed');
        $totalFilled = $event->roles->sum('Volunteers_Filled');
        $capacityPercentage = $totalCapacity > 0 ? round(($totalFilled / $totalCapacity) * 100) : 0;

        // Group volunteers by role with their skills and user details (cross-database safe)
        // Step 1: Get participations from sashvini
        $participations = EventParticipation::where('Event_ID', $event->Event_ID)->get();

        // Step 2: Get volunteer IDs
        $volunteerIds = $participations->pluck('Volunteer_ID')->unique()->toArray();

        // Step 3: Load volunteers with user and skills from izzhilmy/adam
        $volunteers = ! empty($volunteerIds)
            ? Volunteer::whereIn('Volunteer_ID', $volunteerIds)
                ->with(['user', 'skills'])
                ->get()
                ->keyBy('Volunteer_ID')
            : collect();

        // Step 4: Attach pivot data and group by role
        $volunteersByRole = $participations->map(function ($participation) use ($volunteers) {
            $volunteer = $volunteers->get($participation->Volunteer_ID);
            if ($volunteer) {
                $volunteer->pivot = (object) [
                    'Role_ID' => $participation->Role_ID,
                    'Status' => $participation->Status,
                    'Total_Hours' => $participation->Total_Hours,
                ];
            }

            return $volunteer;
        })->filter()->groupBy('pivot.Role_ID');

        return view('event-management.events.show', compact('event', 'volunteersByRole', 'totalCapacity', 'totalFilled', 'capacityPercentage'));
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
            ->with('success', 'Event updated successfully! (Database: Izzati)');
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
            ->with('success', 'Event deleted successfully! (Database: Izzati)');
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

        // Load roles
        $roles = $event->roles;

        // Load volunteers with role information (cross-database safe)
        // Step 1: Get participations from sashvini
        $participations = EventParticipation::where('Event_ID', $event->Event_ID)
            ->orderBy('created_at', 'desc')
            ->get();

        // Step 2: Get volunteer IDs
        $volunteerIds = $participations->pluck('Volunteer_ID')->unique()->toArray();

        // Step 3: Load volunteers with user from izzhilmy
        $volunteersCollection = ! empty($volunteerIds)
            ? Volunteer::whereIn('Volunteer_ID', $volunteerIds)
                ->with('user')
                ->get()
                ->keyBy('Volunteer_ID')
            : collect();

        // Step 4: Attach pivot data to volunteers
        $volunteers = $participations->map(function ($participation) use ($volunteersCollection) {
            $volunteer = $volunteersCollection->get($participation->Volunteer_ID);
            if ($volunteer) {
                $volunteer->pivot = (object) [
                    'Status' => $participation->Status,
                    'Total_Hours' => $participation->Total_Hours,
                    'Role_ID' => $participation->Role_ID,
                    'created_at' => $participation->created_at,
                ];
            }

            return $volunteer;
        })->filter();

        // Get role statistics
        $roleStats = $volunteers->groupBy('pivot.Role_ID')->map(function ($group) {
            return $group->count();
        });

        return view('event-management.events.manage-volunteers', compact('event', 'volunteers', 'roles', 'roleStats'));
    }

    /**
     * Update volunteer attendance and hours
     * Cross-database operation: Event (Izzati) -> event_participation (Sashvini)
     */
    public function updateVolunteerHours(Request $request, Event $event, $volunteerId)
    {
        // Check if user owns this event
        if ($event->Organizer_ID !== Auth::user()->organization->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        // Note: Volunteer validation happens via event_participation table query
        // Cross-database: Event (Izzati) updates volunteer participation (Sashvini)

        // Base validation
        $validated = $request->validate([
            'status' => ['required', 'in:Registered,Attended,No-Show,Cancelled'],
            'total_hours' => ['nullable', 'integer', 'min:0', 'max:24'],
            'role_id' => ['nullable', 'integer'], // event_role is in izzati database - manual validation below
        ]);

        // Additional validation: If status is "Attended", hours must be provided and greater than 0
        if ($validated['status'] === 'Attended') {
            if (! isset($validated['total_hours']) || $validated['total_hours'] <= 0) {
                return back()->withErrors([
                    'total_hours' => 'Hours must be provided and greater than 0 when marking attendance as "Attended".',
                ])->withInput();
            }
        }

        // For No-Show status, validate hours cannot be > 0
        if ($validated['status'] === 'No-Show') {
            if (isset($validated['total_hours']) && $validated['total_hours'] > 0) {
                return back()->withErrors([
                    'total_hours' => 'Hours must be 0 for "No-Show" status. Volunteers who did not show cannot have hours logged.',
                ])->withInput();
            }
            $validated['total_hours'] = 0;
        }

        DB::beginTransaction();

        try {
            // Get current participation (sashvini database)
            $participation = DB::connection('sashvini')->table('event_participation')
                ->where('Event_ID', $event->Event_ID)
                ->where('Volunteer_ID', $volunteerId)
                ->first();

            if (! $participation) {
                return back()->with('error', 'Volunteer not found for this event.');
            }

            // Handle role change if provided
            $updateData = [
                'Status' => $validated['status'],
                'Total_Hours' => $validated['total_hours'],
                'updated_at' => now(),
            ];

            if (isset($validated['role_id'])) {
                $newRoleId = $validated['role_id'] ?: null;
                $oldRoleId = $participation->Role_ID;

                // Only update role counts if role actually changed
                if ($newRoleId != $oldRoleId) {
                    // Verify new role belongs to this event
                    if ($newRoleId) {
                        $newRole = EventRole::find($newRoleId);
                        if (! $newRole || $newRole->Event_ID !== $event->Event_ID) {
                            return back()->with('error', 'Invalid role selected.');
                        }

                        // Check if new role is full
                        if ($newRole->isFull()) {
                            return back()->with('error', 'Selected role is full.');
                        }
                    }

                    // Decrement old role count (izzati database)
                    if ($oldRoleId) {
                        DB::connection('izzati')->table('event_role')
                            ->where('Role_ID', $oldRoleId)
                            ->decrement('Volunteers_Filled');
                    }

                    // Increment new role count (izzati database)
                    if ($newRoleId) {
                        DB::connection('izzati')->table('event_role')
                            ->where('Role_ID', $newRoleId)
                            ->increment('Volunteers_Filled');
                    }

                    $updateData['Role_ID'] = $newRoleId;
                }
            }

            // Update volunteer participation (sashvini database)
            DB::connection('sashvini')->table('event_participation')
                ->where('Event_ID', $event->Event_ID)
                ->where('Volunteer_ID', $volunteerId)
                ->update($updateData);

            // Check if event should be marked as completed
            if ($event->End_Date < now() && $event->Status !== 'Completed') {
                $event->update(['Status' => 'Completed']);
            }

            DB::commit();

            return back()->with('success', 'Volunteer updated successfully! (Database: Sashvini)');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to update volunteer.');
        }
    }

    /**
     * Auto-calculate and update all volunteer hours for completed event
     * Cross-database operation: Event (Izzati) -> event_participation (Sashvini)
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

        // Cross-database: Update volunteer participation records in Sashvini
        $updated = DB::connection('sashvini')->table('event_participation')
            ->where('Event_ID', $event->Event_ID)
            ->whereIn('Status', ['Registered', 'Attended'])
            ->update([
                'Status' => 'Attended',
                'Total_Hours' => $eventHours,
                'updated_at' => now(),
            ]);

        return back()->with('success', "Auto-calculated {$eventHours} hours for {$updated} volunteers! (Database: Sashvini)");
    }

    /**
     * Bulk update volunteer statuses
     * Cross-database operation: Event (Izzati) -> event_participation (Sashvini)
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
            'hours' => ['nullable', 'integer', 'min:0', 'max:24'],
        ]);

        // Validate: If status is "Attended", hours must be provided
        if ($validated['status'] === 'Attended') {
            if (! isset($validated['hours']) || $validated['hours'] <= 0) {
                return back()->withErrors([
                    'hours' => 'Hours must be provided and greater than 0 when marking volunteers as "Attended".',
                ])->withInput();
            }
        }

        // Validate: If status is "No-Show", hours must be 0
        if ($validated['status'] === 'No-Show') {
            if (isset($validated['hours']) && $validated['hours'] > 0) {
                return back()->withErrors([
                    'hours' => 'Hours must be 0 for "No-Show" status. Volunteers who did not show cannot have hours logged.',
                ])->withInput();
            }
        }

        $hours = $validated['hours'] ?? 0;

        // Update event_participation in sashvini database
        DB::connection('sashvini')->table('event_participation')
            ->where('Event_ID', $event->Event_ID)
            ->whereIn('Volunteer_ID', $validated['volunteer_ids'])
            ->update([
                'Status' => $validated['status'],
                'Total_Hours' => $validated['status'] === 'Attended' ? $hours : 0,
                'updated_at' => now(),
            ]);

        $count = count($validated['volunteer_ids']);

        return back()->with('success', "Updated {$count} volunteers! (Database: Sashvini)");
    }

    /**
     * Update a volunteer's assigned role
     * Cross-database operation: Event (Izzati) -> event_participation (Sashvini)
     */
    public function updateVolunteerRole(Request $request, Event $event, $volunteerId)
    {
        // Check if user owns this event
        if ($event->Organizer_ID !== Auth::user()->organization->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'role_id' => ['required', 'integer'], // event_role is in izzati database - manual validation below
        ]);

        $newRole = EventRole::findOrFail($validated['role_id']);

        // Verify role belongs to this event
        if ($newRole->Event_ID !== $event->Event_ID) {
            return back()->with('error', 'Invalid role selected.');
        }

        // Check if new role is full
        if ($newRole->isFull()) {
            return back()->with('error', 'Selected role is full.');
        }

        DB::beginTransaction();

        try {
            // Get current participation (sashvini database)
            $participation = DB::connection('sashvini')->table('event_participation')
                ->where('Event_ID', $event->Event_ID)
                ->where('Volunteer_ID', $volunteerId)
                ->first();

            if (! $participation) {
                return back()->with('error', 'Volunteer not found for this event.');
            }

            $oldRoleId = $participation->Role_ID;

            // Decrement old role count (izzati database)
            if ($oldRoleId) {
                DB::connection('izzati')->table('event_role')
                    ->where('Role_ID', $oldRoleId)
                    ->decrement('Volunteers_Filled');
            }

            // Update participation (sashvini database)
            DB::connection('sashvini')->table('event_participation')
                ->where('Event_ID', $event->Event_ID)
                ->where('Volunteer_ID', $volunteerId)
                ->update(['Role_ID' => $validated['role_id']]);

            // Increment new role count (izzati database)
            DB::connection('izzati')->table('event_role')
                ->where('Role_ID', $validated['role_id'])
                ->increment('Volunteers_Filled');

            DB::commit();

            return back()->with('success', 'Volunteer role updated successfully! (Database: Sashvini)');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to update role.');
        }
    }

    // ========================================
    // ADMIN APPROVAL METHODS
    // ========================================

    /**
     * Display pending campaigns for admin approval
     */
    public function adminPendingCampaigns(Request $request)
    {
        $search = $request->get('search');

        $campaigns = Campaign::where('Status', 'Pending')
            ->with('organization.user')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('Title', 'like', "%{$search}%")
                        ->orWhere('Description', 'like', "%{$search}%")
                        ->orWhereHas('organization.user', function ($orgQuery) use ($search) {
                            $orgQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends(['search' => $search]);

        // Load organizer statistics for each campaign
        $campaigns->getCollection()->transform(function ($campaign) {
            $campaign->organizerStats = $this->getOrganizerCampaignStats($campaign->Organization_ID);

            return $campaign;
        });

        return view('event-management.admin.campaigns-pending', compact('campaigns', 'search'));
    }

    /**
     * Display pending events for admin approval
     */
    public function adminPendingEvents(Request $request)
    {
        $search = $request->get('search');

        $events = Event::where('Status', 'Pending')
            ->with(['organization.user', 'roles'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('Title', 'like', "%{$search}%")
                        ->orWhere('Description', 'like', "%{$search}%")
                        ->orWhere('Location', 'like', "%{$search}%")
                        ->orWhereHas('organization.user', function ($orgQuery) use ($search) {
                            $orgQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends(['search' => $search]);

        // Load organizer statistics for each event
        $events->getCollection()->transform(function ($event) {
            $event->organizerStats = $this->getOrganizerEventStats($event->Organizer_ID);

            return $event;
        });

        return view('event-management.admin.events-pending', compact('events', 'search'));
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

        return back()->with('success', 'Campaign approved successfully! (Database: Izzati)');
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

        return back()->with('success', 'Campaign rejected. (Database: Izzati)');
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

        return back()->with('success', 'Event approved successfully! (Database: Izzati)');
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

        return back()->with('success', 'Event rejected. (Database: Izzati)');
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

    /**
     * Get campaign statistics for an organizer
     */
    private function getOrganizerCampaignStats($organizationId)
    {
        return [
            'total' => Campaign::where('Organization_ID', $organizationId)->count(),
            'approved' => Campaign::where('Organization_ID', $organizationId)
                ->whereIn('Status', ['Active', 'Completed'])->count(),
            'rejected' => Campaign::where('Organization_ID', $organizationId)
                ->where('Status', 'Rejected')->count(),
            'recent' => Campaign::where('Organization_ID', $organizationId)
                ->whereIn('Status', ['Active', 'Completed', 'Rejected'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['Campaign_ID', 'Title', 'Status', 'created_at']),
        ];
    }

    /**
     * Get event statistics for an organizer
     */
    private function getOrganizerEventStats($organizationId)
    {
        return [
            'total' => Event::where('Organizer_ID', $organizationId)->count(),
            'approved' => Event::where('Organizer_ID', $organizationId)
                ->whereIn('Status', ['Upcoming', 'Ongoing', 'Completed'])->count(),
            'rejected' => Event::where('Organizer_ID', $organizationId)
                ->where('Status', 'Rejected')->count(),
            'recent' => Event::where('Organizer_ID', $organizationId)
                ->whereIn('Status', ['Upcoming', 'Ongoing', 'Completed', 'Rejected'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['Event_ID', 'Title', 'Status', 'created_at']),
        ];
    }
}
