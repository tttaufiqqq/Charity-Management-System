<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventParticipation;
use App\Models\EventRole;
use App\Models\Skill;
use App\Models\Volunteer;
use App\Traits\ValidatesCrossDatabaseReferences;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VolunteerController extends Controller
{
    use ValidatesCrossDatabaseReferences;

    /**
     * Display all available events for volunteers
     */
    public function browseEvents()
    {
        $volunteer = Auth::user()->volunteer;

        if (! $volunteer) {
            return redirect()->route('dashboard')->with('error', 'Volunteer profile not found.');
        }

        // Get all upcoming and ongoing events with roles eager loaded
        $events = Event::whereIn('Status', ['Upcoming', 'Ongoing'])
            ->with('roles')
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

        if (! $volunteer) {
            return redirect()->route('dashboard')->with('error', 'Volunteer profile not found.');
        }

        // Load roles with volunteer counts
        $event->load(['roles', 'organization.user']);

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

        // Get assigned role if registered
        $assignedRole = null;
        if ($participation && $participation->Role_ID) {
            $assignedRole = EventRole::find($participation->Role_ID);
        }

        // Check if event is full based on role capacity
        $totalCapacity = $event->roles->sum('Volunteers_Needed');
        $totalFilled = $event->roles->sum('Volunteers_Filled');
        $isFull = $totalCapacity > 0 && $totalFilled >= $totalCapacity;

        return view('volunteer-management.events.show', compact('event', 'isRegistered', 'participation', 'assignedRole', 'isFull', 'totalCapacity', 'totalFilled'));
    }

    /**
     * Register volunteer for an event
     * Note: Event validated via route model binding (cross-database: sashvini -> izzati)
     */
    public function registerForEvent(Request $request, Event $event)
    {
        $volunteer = Auth::user()->volunteer;

        if (! $volunteer) {
            return redirect()->route('dashboard')->with('error', 'Volunteer profile not found.');
        }

        // Validate event exists (cross-database validation: sashvini -> izzati)
        // Event already validated via route model binding, but explicitly validate here for clarity
        $this->validateEventExists($event->Event_ID);

        // Validate role selection if event has roles
        $roleId = null;
        if ($event->roles()->count() > 0) {
            $validated = $request->validate([
                'role_id' => ['required', 'exists:event_role,Role_ID'],
            ]);
            $roleId = $validated['role_id'];

            // Verify role belongs to this event
            $role = EventRole::where('Role_ID', $roleId)
                ->where('Event_ID', $event->Event_ID)
                ->first();

            if (! $role) {
                return back()->with('error', 'Invalid role selected.');
            }

            // Check if role is full
            if ($role->isFull()) {
                return back()->with('error', 'This role is already full. Please select another role.');
            }
        }

        // Check if event is still accepting volunteers
        if (! in_array($event->Status, ['Upcoming', 'Ongoing'])) {
            return back()->with('error', 'This event is no longer accepting volunteers.');
        }

        // Check if already registered
        $alreadyRegistered = $volunteer->events()
            ->where('event.Event_ID', $event->Event_ID)
            ->exists();

        if ($alreadyRegistered) {
            return back()->with('error', 'You are already registered for this event.');
        }

        // Check for date conflicts with other registered events
        $conflictingEvent = $volunteer->events()
            ->where('event.Event_ID', '!=', $event->Event_ID)
            ->where(function ($query) use ($event) {
                $query->where(function ($q) use ($event) {
                    // New event starts during existing event
                    $q->where('event.Start_Date', '<=', $event->Start_Date)
                        ->where('event.End_Date', '>=', $event->Start_Date);
                })->orWhere(function ($q) use ($event) {
                    // New event ends during existing event
                    $q->where('event.Start_Date', '<=', $event->End_Date)
                        ->where('event.End_Date', '>=', $event->End_Date);
                })->orWhere(function ($q) use ($event) {
                    // New event completely contains existing event
                    $q->where('event.Start_Date', '>=', $event->Start_Date)
                        ->where('event.End_Date', '<=', $event->End_Date);
                });
            })
            ->first();

        if ($conflictingEvent) {
            return back()->with('error', 'You are already registered for "'.$conflictingEvent->Title.'" which overlaps with this event ('.\Carbon\Carbon::parse($conflictingEvent->Start_Date)->format('M d, Y').' - '.\Carbon\Carbon::parse($conflictingEvent->End_Date)->format('M d, Y').').');
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
                'Role_ID' => $roleId,
                'Status' => 'Registered',
                'Total_Hours' => 0,
            ]);

            // Update role volunteer count if role was selected
            if ($roleId) {
                DB::table('event_role')
                    ->where('Role_ID', $roleId)
                    ->increment('Volunteers_Filled');
            }

            DB::commit();

            return redirect()
                ->route('volunteer.events.show', ['event' => $event->Event_ID])
                ->with('success', 'Successfully registered for the event! (Database: Sashvini)');
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

        if (! $volunteer) {
            return redirect()->route('dashboard')->with('error', 'Volunteer profile not found.');
        }

        // Get the participation to check for role assignment
        $participation = DB::table('event_participation')
            ->where('Volunteer_ID', $volunteer->Volunteer_ID)
            ->where('Event_ID', $event->Event_ID)
            ->first();

        if (! $participation) {
            return back()->with('error', 'You are not registered for this event.');
        }

        DB::beginTransaction();
        try {
            // Decrement role volunteer count if role was assigned
            if ($participation->Role_ID) {
                DB::table('event_role')
                    ->where('Role_ID', $participation->Role_ID)
                    ->decrement('Volunteers_Filled');
            }

            // Delete participation
            DB::table('event_participation')
                ->where('Volunteer_ID', $volunteer->Volunteer_ID)
                ->where('Event_ID', $event->Event_ID)
                ->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to cancel registration. Please try again.');
        }

        return redirect()
            ->route('volunteer.events.browse')
            ->with('success', 'Event registration cancelled successfully. (Database: Sashvini)');
    }

    /**
     * Display volunteer's registered events
     */
    public function myEvents()
    {
        $volunteer = Auth::user()->volunteer;

        if (! $volunteer) {
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

        if (! $volunteer) {
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

    // Display volunteer's skills
    public function showSkills()
    {
        $volunteer = Auth::user()->volunteer;

        if (! $volunteer) {
            return redirect()->route('dashboard')->with('error', 'Volunteer profile not found.');
        }

        $volunteerSkills = $volunteer->skills()->withPivot('Skill_Level')->get();
        $allSkills = Skill::all();

        return view('volunteer-management.skill.index', compact('volunteerSkills', 'allSkills'));
    }

    // Store new skill for volunteer
    public function storeSkill(Request $request)
    {
        $request->validate([
            'skill_id' => 'required|exists:skill,Skill_ID',
            'skill_level' => 'required|in:Beginner,Intermediate,Advanced,Expert',
        ]);

        $volunteer = Auth::user()->volunteer;

        if (! $volunteer) {
            return redirect()->route('dashboard')->with('error', 'Volunteer profile not found.');
        }

        // Check if skill already exists - specify table name to avoid ambiguity
        if ($volunteer->skills()->where('volunteer_skill.Skill_ID', $request->skill_id)->exists()) {
            return redirect()->back()->with('error', 'You already have this skill added.');
        }

        // Attach skill to volunteer with skill level
        $volunteer->skills()->attach($request->skill_id, [
            'Skill_Level' => $request->skill_level,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('volunteer.skills.index')->with('success', 'Skill added successfully! (Database: Sashvini)');
    }

    // Update skill level
    public function updateSkill(Request $request, $skillId)
    {
        $request->validate([
            'skill_level' => 'required|in:Beginner,Intermediate,Advanced,Expert',
        ]);

        $volunteer = Auth::user()->volunteer;

        if (! $volunteer) {
            return redirect()->route('dashboard')->with('error', 'Volunteer profile not found.');
        }

        // Check if volunteer has this skill - specify table name to avoid ambiguity
        if (! $volunteer->skills()->where('volunteer_skill.Skill_ID', $skillId)->exists()) {
            return redirect()->back()->with('error', 'Skill not found.');
        }

        // Update skill level
        $volunteer->skills()->updateExistingPivot($skillId, [
            'Skill_Level' => $request->skill_level,
            'updated_at' => now(),
        ]);

        return redirect()->route('volunteer.skills.index')->with('success', 'Skill level updated successfully! (Database: Sashvini)');
    }

    // Delete skill
    public function deleteSkill($skillId)
    {
        $volunteer = Auth::user()->volunteer;

        if (! $volunteer) {
            return redirect()->route('dashboard')->with('error', 'Volunteer profile not found.');
        }

        // Check if volunteer has this skill - specify table name to avoid ambiguity
        if (! $volunteer->skills()->where('volunteer_skill.Skill_ID', $skillId)->exists()) {
            return redirect()->back()->with('error', 'Skill not found.');
        }

        // Detach skill from volunteer
        $volunteer->skills()->detach($skillId);

        return redirect()->route('volunteer.skills.index')->with('success', 'Skill removed successfully! (Database: Sashvini)');
    }

    /**
     * Display volunteer schedule (calendar view)
     */
    public function schedule()
    {
        $volunteer = Auth::user()->volunteer;

        if (! $volunteer) {
            return redirect()->route('dashboard')->with('error', 'Volunteer profile not found.');
        }

        // Get current month and year
        $currentMonth = request('month', now()->month);
        $currentYear = request('year', now()->year);

        // Get all registered events for the selected month
        $events = $volunteer->events()
            ->whereYear('Start_Date', $currentYear)
            ->whereMonth('Start_Date', $currentMonth)
            ->orderBy('Start_Date', 'asc')
            ->get();

        // Get upcoming events (next 30 days)
        $upcomingEvents = $volunteer->events()
            ->where('Start_Date', '>=', now())
            ->where('Start_Date', '<=', now()->addDays(30))
            ->whereIn('event.Status', ['Upcoming', 'Ongoing'])
            ->orderBy('Start_Date', 'asc')
            ->get();

        // Create calendar data
        $calendar = $this->generateCalendar($currentYear, $currentMonth, $events);

        return view('volunteer-management.schedule', compact(
            'events',
            'upcomingEvents',
            'calendar',
            'currentMonth',
            'currentYear'
        ));
    }

    /**
     * Generate calendar data for the schedule view
     */
    private function generateCalendar($year, $month, $events)
    {
        $firstDay = \Carbon\Carbon::create($year, $month, 1);
        $lastDay = $firstDay->copy()->endOfMonth();
        $startDay = $firstDay->copy()->startOfWeek();
        $endDay = $lastDay->copy()->endOfWeek();

        $calendar = [];
        $currentDay = $startDay->copy();

        while ($currentDay <= $endDay) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $dayEvents = $events->filter(function ($event) use ($currentDay) {
                    return $event->Start_Date->isSameDay($currentDay);
                });

                $week[] = [
                    'date' => $currentDay->copy(),
                    'isCurrentMonth' => $currentDay->month === $month,
                    'isToday' => $currentDay->isToday(),
                    'events' => $dayEvents,
                ];

                $currentDay->addDay();
            }
            $calendar[] = $week;
        }

        return $calendar;
    }

    /**
     * Display volunteer profile
     */
    public function profile()
    {
        $volunteer = Auth::user()->volunteer;

        if (! $volunteer) {
            return redirect()->route('dashboard')->with('error', 'Volunteer profile not found.');
        }

        // Get statistics
        $totalHours = EventParticipation::where('Volunteer_ID', $volunteer->Volunteer_ID)
            ->sum('Total_Hours');

        $totalEvents = $volunteer->events()->count();

        $completedEvents = $volunteer->events()
            ->where('event.Status', 'Completed')
            ->count();

        $upcomingEvents = $volunteer->events()
            ->whereIn('event.Status', ['Upcoming', 'Ongoing'])
            ->count();

        // Get skills
        $skills = $volunteer->skills;

        return view('volunteer-management.profile', compact(
            'volunteer',
            'totalHours',
            'totalEvents',
            'completedEvents',
            'upcomingEvents',
            'skills'
        ));
    }

    /**
     * Show edit profile form
     */
    public function editProfile()
    {
        $volunteer = Auth::user()->volunteer;

        if (! $volunteer) {
            return redirect()->route('dashboard')->with('error', 'Volunteer profile not found.');
        }

        return view('volunteer-management.edit-profile', compact('volunteer'));
    }

    /**
     * Update volunteer profile
     */
    public function updateProfile(Request $request)
    {
        $volunteer = Auth::user()->volunteer;

        if (! $volunteer) {
            return redirect()->route('dashboard')->with('error', 'Volunteer profile not found.');
        }

        $validated = $request->validate([
            'availability' => ['required', 'string'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'phone_num' => ['required', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
        ]);

        $volunteer->update([
            'Availability' => $validated['availability'],
            'Address' => $validated['address'],
            'City' => $validated['city'],
            'State' => $validated['state'],
            'Phone_Num' => $validated['phone_num'],
            'Description' => $validated['description'],
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profile updated successfully! (Database: Sashvini)');
    }
}
