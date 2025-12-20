# Event Status & Volunteer Hours Implementation Guide

This guide explains the best practices for inserting and updating data in `Event.Status` and `EventParticipation.Total_Hours`.

## Table of Contents
1. [Overview](#overview)
2. [Event Status Management](#event-status-management)
3. [Volunteer Hours Management](#volunteer-hours-management)
4. [Automated Processes](#automated-processes)
5. [Usage Examples](#usage-examples)

---

## Overview

The project now includes two service classes and automated commands for managing event statuses and volunteer hours:

### Service Classes
- **EventStatusService** (`app/Services/EventStatusService.php`) - Handles event status transitions
- **VolunteerHoursService** (`app/Services/VolunteerHoursService.php`) - Handles volunteer hour calculations

### Automated Command
- **UpdateEventStatusesCommand** - Runs hourly to auto-update statuses and hours
- Command: `php artisan events:update-statuses --calculate-hours`

---

## Event Status Management

### Status Flow Diagram
```
Pending → Upcoming → Ongoing → Completed
   ↓          ↓         ↓
Rejected   Cancelled  Cancelled
```

### Valid Status Transitions

| From Status | To Status(es) |
|------------|---------------|
| Pending | Upcoming, Rejected, Cancelled |
| Upcoming | Ongoing, Cancelled |
| Ongoing | Completed, Cancelled |
| Completed | (Terminal - No transitions) |
| Rejected | (Terminal - No transitions) |
| Cancelled | (Terminal - No transitions) |

### Method 1: Using EventStatusService (Recommended)

```php
use App\Services\EventStatusService;

class EventManagementController extends Controller
{
    protected EventStatusService $statusService;

    public function __construct(EventStatusService $statusService)
    {
        $this->statusService = $statusService;
    }

    // Admin approves event
    public function adminApproveEvent(Event $event)
    {
        if ($this->statusService->approve($event)) {
            return back()->with('success', 'Event approved!');
        }

        return back()->with('error', 'Event cannot be approved.');
    }

    // Organizer cancels event
    public function cancelEvent(Event $event)
    {
        if ($this->statusService->cancel($event)) {
            return back()->with('success', 'Event cancelled.');
        }

        return back()->with('error', 'Event cannot be cancelled.');
    }

    // Manual status transition with validation
    public function updateStatus(Event $event, string $newStatus)
    {
        if ($this->statusService->transitionTo($event, $newStatus)) {
            return back()->with('success', "Event status updated to {$newStatus}");
        }

        return back()->with('error', 'Invalid status transition.');
    }
}
```

### Method 2: Direct Model Update (Simple Cases)

```php
// Creating a new event (always starts as Pending)
Event::create([
    'Organizer_ID' => $organization->Organization_ID,
    'Title' => $request->title,
    'Status' => 'Pending', // Always start as Pending
    // ... other fields
]);

// Admin approval (changes to Upcoming)
$event->update(['Status' => 'Upcoming']);

// Admin rejection
$event->update(['Status' => 'Rejected']);
```

### Method 3: Automated Updates (Runs Hourly)

The scheduler automatically updates event statuses based on dates:
- **Upcoming → Ongoing**: When Start_Date arrives
- **Ongoing → Completed**: When End_Date passes

```php
// This runs automatically every hour via scheduler
// Can also be run manually:
php artisan events:update-statuses
```

---

## Volunteer Hours Management

### Method 1: Using VolunteerHoursService (Recommended)

```php
use App\Services\VolunteerHoursService;

class EventManagementController extends Controller
{
    protected VolunteerHoursService $hoursService;

    public function __construct(VolunteerHoursService $hoursService)
    {
        $this->hoursService = $hoursService;
    }

    // Update individual volunteer hours
    public function updateVolunteerHours(Request $request, Event $event, $volunteerId)
    {
        $validated = $request->validate([
            'total_hours' => 'required|numeric|min:0|max:24',
            'status' => 'required|in:Registered,Attended,No-Show,Cancelled',
        ]);

        $success = $this->hoursService->updateHours(
            $volunteerId,
            $event->Event_ID,
            $validated['total_hours'],
            $validated['status']
        );

        return $success
            ? back()->with('success', 'Hours updated!')
            : back()->with('error', 'Failed to update hours.');
    }

    // Mark volunteer as attended (auto-calculates hours from event duration)
    public function markAttended(Event $event, $volunteerId)
    {
        if ($this->hoursService->markAttended($volunteerId, $event)) {
            return back()->with('success', 'Volunteer marked as attended!');
        }

        return back()->with('error', 'Failed to mark attendance.');
    }

    // Mark as no-show (sets hours to 0)
    public function markNoShow(Event $event, $volunteerId)
    {
        if ($this->hoursService->markNoShow($volunteerId, $event->Event_ID)) {
            return back()->with('success', 'Volunteer marked as no-show.');
        }

        return back()->with('error', 'Failed to update status.');
    }

    // Auto-calculate hours for all volunteers in a completed event
    public function autoCalculateHours(Event $event)
    {
        $updated = $this->hoursService->autoCalculateForEvent($event);

        return back()->with('success', "Calculated hours for {$updated} volunteers!");
    }

    // Bulk update multiple volunteers at once
    public function bulkUpdate(Request $request, Event $event)
    {
        $validated = $request->validate([
            'volunteer_ids' => 'required|array',
            'hours' => 'required|numeric|min:0|max:24',
            'status' => 'required|in:Attended,No-Show',
        ]);

        $updated = $this->hoursService->bulkUpdate(
            $event->Event_ID,
            $validated['volunteer_ids'],
            $validated['hours'],
            $validated['status']
        );

        return back()->with('success', "Updated {$updated} volunteers!");
    }

    // Get volunteer hours statistics
    public function getEventStats(Event $event)
    {
        $stats = $this->hoursService->getEventHoursStatistics($event->Event_ID);

        return view('events.stats', compact('stats'));
        // Returns: volunteers_attended, total_hours, avg_hours, max_hours, min_hours
    }
}
```

### Method 2: Using Form Requests (Best Practice)

```php
use App\Http\Requests\UpdateVolunteerHoursRequest;

public function updateVolunteerHours(
    UpdateVolunteerHoursRequest $request,
    Event $event,
    $volunteerId
) {
    // Validation and authorization already handled by Form Request
    $this->hoursService->updateHours(
        $volunteerId,
        $event->Event_ID,
        $request->total_hours,
        $request->status
    );

    return back()->with('success', 'Hours updated successfully!');
}
```

### Method 3: Direct Database Update (Not Recommended)

```php
// Only use for simple cases
DB::table('event_participation')
    ->where('Event_ID', $eventId)
    ->where('Volunteer_ID', $volunteerId)
    ->update([
        'Total_Hours' => 5.5,
        'Status' => 'Attended',
        'updated_at' => now(),
    ]);
```

---

## Automated Processes

### Scheduled Tasks

The system automatically runs the following tasks every hour:

```php
// In bootstrap/app.php
$schedule->command('events:update-statuses --calculate-hours')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();
```

This command:
1. Updates event statuses based on Start_Date and End_Date
2. Auto-calculates volunteer hours for newly completed events

### Manual Execution

```bash
# Update event statuses only
php artisan events:update-statuses

# Update statuses AND calculate hours
php artisan events:update-statuses --calculate-hours
```

---

## Usage Examples

### Example 1: Creating a New Event

```php
use Illuminate\Support\Facades\DB;

DB::transaction(function () use ($request, $organization) {
    // Create event (always starts as Pending)
    $event = Event::create([
        'Organizer_ID' => $organization->Organization_ID,
        'Title' => $request->title,
        'Description' => $request->description,
        'Location' => $request->location,
        'Start_Date' => $request->start_date,
        'End_Date' => $request->end_date,
        'Capacity' => $request->capacity,
        'Status' => 'Pending', // ALWAYS start as Pending
    ]);

    // Create roles for the event
    foreach ($request->roles as $role) {
        EventRole::create([
            'Event_ID' => $event->Event_ID,
            'Role_Name' => $role['name'],
            'Volunteers_Needed' => $role['volunteers_needed'],
        ]);
    }
});
```

### Example 2: Admin Approval Workflow

```php
use App\Services\EventStatusService;

class AdminController extends Controller
{
    public function approveEvent(Event $event, EventStatusService $statusService)
    {
        // Service handles date-based logic
        // If event already passed, sets to Completed instead of Upcoming
        if ($statusService->approve($event)) {
            return redirect()->back()->with('success', 'Event approved!');
        }

        return redirect()->back()->with('error', 'Cannot approve this event.');
    }
}
```

### Example 3: Recording Volunteer Attendance

```php
use App\Services\VolunteerHoursService;

class VolunteerAttendanceController extends Controller
{
    public function recordAttendance(
        Event $event,
        Request $request,
        VolunteerHoursService $hoursService
    ) {
        $validated = $request->validate([
            'volunteers' => 'required|array',
            'volunteers.*.id' => 'required|exists:volunteer,Volunteer_ID',
            'volunteers.*.attended' => 'required|boolean',
            'volunteers.*.hours' => 'nullable|numeric|min:0|max:24',
        ]);

        foreach ($validated['volunteers'] as $volunteer) {
            if ($volunteer['attended']) {
                $hours = $volunteer['hours'] ?? $hoursService->calculateEventDuration($event);
                $hoursService->updateHours(
                    $volunteer['id'],
                    $event->Event_ID,
                    $hours,
                    'Attended'
                );
            } else {
                $hoursService->markNoShow($volunteer['id'], $event->Event_ID);
            }
        }

        return back()->with('success', 'Attendance recorded successfully!');
    }
}
```

### Example 4: Volunteer Dashboard Statistics

```php
use App\Services\VolunteerHoursService;

public function volunteerDashboard(VolunteerHoursService $hoursService)
{
    $volunteer = auth()->user()->volunteer;

    // Get total hours contributed
    $totalHours = $hoursService->getTotalHoursForVolunteer($volunteer->Volunteer_ID);

    // Get monthly breakdown
    $monthlyHours = $hoursService->getMonthlyHoursForVolunteer($volunteer->Volunteer_ID);

    return view('volunteer.dashboard', compact('totalHours', 'monthlyHours'));
}
```

### Example 5: Leaderboard

```php
use App\Services\VolunteerHoursService;

public function leaderboard(VolunteerHoursService $hoursService)
{
    $topVolunteers = $hoursService->getTopVolunteersByHours(10);

    return view('leaderboard', compact('topVolunteers'));
}
```

---

## Testing the Command

```bash
# Test the command manually
php artisan events:update-statuses --calculate-hours

# Check scheduled tasks
php artisan schedule:list

# Run scheduler (for testing)
php artisan schedule:run
```

---

## Benefits of This Approach

1. **Validation**: Business rules enforced in one place
2. **Consistency**: Same logic used everywhere
3. **Automation**: Status updates happen automatically
4. **Logging**: All changes are logged for audit
5. **Reusability**: Services can be used anywhere in the app
6. **Testability**: Easy to unit test service methods
7. **Type Safety**: Full IDE autocomplete and type checking

---

## Common Pitfalls to Avoid

❌ **DON'T** update statuses without validation
```php
// BAD - No validation of state transitions
$event->update(['Status' => 'Completed']);
```

✅ **DO** use the service for validation
```php
// GOOD - Validates transition is allowed
$statusService->transitionTo($event, 'Completed');
```

❌ **DON'T** set hours outside valid range
```php
// BAD - No validation
$participation->Total_Hours = -5; // Invalid!
```

✅ **DO** validate hours before updating
```php
// GOOD - Validates range
$hoursService->updateHours($volunteerId, $eventId, 5.5, 'Attended');
```

❌ **DON'T** forget to update timestamps
```php
// BAD - Timestamp not updated
DB::update('event_participation', ['Total_Hours' => 5]);
```

✅ **DO** include updated_at
```php
// GOOD - Service handles timestamps
$hoursService->updateHours($volunteerId, $eventId, 5, 'Attended');
```

---

## Summary

**For Event Status:**
- Use `EventStatusService` for complex transitions
- Always start new events with `Status = 'Pending'`
- Let admin approval change to 'Upcoming'
- Let scheduler auto-update to 'Ongoing' and 'Completed'

**For Volunteer Hours:**
- Use `VolunteerHoursService` for all hour calculations
- Use `markAttended()` to auto-calculate from event duration
- Use `bulkUpdate()` for multiple volunteers
- Use Form Requests for validation
- Hours are automatically calculated for completed events

**Automation:**
- Scheduler runs `events:update-statuses --calculate-hours` every hour
- No manual intervention needed for date-based status changes
- All changes are logged for audit trails
