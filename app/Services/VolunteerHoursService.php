<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Volunteer;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VolunteerHoursService
{
    /**
     * Calculate hours based on event duration
     */
    public function calculateEventDuration(Event $event): float
    {
        $startDate = Carbon::parse($event->Start_Date);
        $endDate = Carbon::parse($event->End_Date);

        // Calculate total hours between start and end
        $totalHours = $startDate->diffInHours($endDate);

        // Cap at reasonable limits (e.g., 24 hours per day, multi-day events)
        $days = $startDate->diffInDays($endDate);
        if ($days > 0) {
            // For multi-day events, assume 8-hour workdays
            return min($totalHours, ($days + 1) * 8);
        }

        return min($totalHours, 24);
    }

    /**
     * Update volunteer hours for a participation record
     */
    public function updateHours(int $volunteerId, int $eventId, float $hours, string $status = 'Attended'): bool
    {
        if ($hours < 0 || $hours > 24) {
            Log::warning("Invalid hours value: {$hours} for Volunteer ID {$volunteerId}, Event ID {$eventId}");

            return false;
        }

        $updated = DB::table('event_participation')
            ->where('Volunteer_ID', $volunteerId)
            ->where('Event_ID', $eventId)
            ->update([
                'Total_Hours' => $hours,
                'Status' => $status,
                'updated_at' => now(),
            ]);

        if ($updated) {
            Log::info("Updated hours for Volunteer ID {$volunteerId}, Event ID {$eventId}: {$hours} hours");
        }

        return $updated > 0;
    }

    /**
     * Auto-calculate and assign hours for all volunteers in an event
     */
    public function autoCalculateForEvent(Event $event, array $statuses = ['Registered', 'Attended']): int
    {
        $eventHours = $this->calculateEventDuration($event);

        $updated = DB::table('event_participation')
            ->where('Event_ID', $event->Event_ID)
            ->whereIn('Status', $statuses)
            ->update([
                'Total_Hours' => $eventHours,
                'Status' => 'Attended',
                'updated_at' => now(),
            ]);

        if ($updated > 0) {
            Log::info("Auto-calculated {$eventHours} hours for {$updated} volunteers in Event ID {$event->Event_ID}");
        }

        return $updated;
    }

    /**
     * Bulk update hours for specific volunteers
     */
    public function bulkUpdate(int $eventId, array $volunteerIds, float $hours, string $status = 'Attended'): int
    {
        if ($hours < 0 || $hours > 24) {
            return 0;
        }

        $updated = DB::table('event_participation')
            ->where('Event_ID', $eventId)
            ->whereIn('Volunteer_ID', $volunteerIds)
            ->update([
                'Total_Hours' => $status === 'Attended' ? $hours : 0,
                'Status' => $status,
                'updated_at' => now(),
            ]);

        if ($updated > 0) {
            Log::info("Bulk updated {$updated} volunteers in Event ID {$eventId}: {$hours} hours, status: {$status}");
        }

        return $updated;
    }

    /**
     * Mark volunteer as attended with calculated hours
     */
    public function markAttended(int $volunteerId, Event $event): bool
    {
        $hours = $this->calculateEventDuration($event);

        return $this->updateHours($volunteerId, $event->Event_ID, $hours, 'Attended');
    }

    /**
     * Mark volunteer as no-show (0 hours)
     */
    public function markNoShow(int $volunteerId, int $eventId): bool
    {
        return $this->updateHours($volunteerId, $eventId, 0, 'No-Show');
    }

    /**
     * Get total hours contributed by a volunteer across all events
     */
    public function getTotalHoursForVolunteer(int $volunteerId): float
    {
        return (float) DB::table('event_participation')
            ->where('Volunteer_ID', $volunteerId)
            ->where('Status', 'Attended')
            ->sum('Total_Hours');
    }

    /**
     * Get volunteer hours grouped by month for a specific volunteer
     */
    public function getMonthlyHoursForVolunteer(int $volunteerId, ?int $year = null): Collection
    {
        $year = $year ?? now()->year;

        return DB::table('event_participation')
            ->join('event', 'event_participation.Event_ID', '=', 'event.Event_ID')
            ->where('event_participation.Volunteer_ID', $volunteerId)
            ->where('event_participation.Status', 'Attended')
            ->whereYear('event.Start_Date', $year)
            ->select(
                DB::raw('MONTH(event.Start_Date) as month'),
                DB::raw('SUM(event_participation.Total_Hours) as total_hours')
            )
            ->groupBy(DB::raw('MONTH(event.Start_Date)'))
            ->orderBy('month')
            ->get();
    }

    /**
     * Get leaderboard of top volunteers by hours
     */
    public function getTopVolunteersByHours(int $limit = 10): Collection
    {
        return DB::table('event_participation')
            ->join('volunteer', 'event_participation.Volunteer_ID', '=', 'volunteer.Volunteer_ID')
            ->join('users', 'volunteer.User_ID', '=', 'users.id')
            ->where('event_participation.Status', 'Attended')
            ->select(
                'volunteer.Volunteer_ID',
                'users.name',
                'users.email',
                DB::raw('SUM(event_participation.Total_Hours) as total_hours'),
                DB::raw('COUNT(DISTINCT event_participation.Event_ID) as events_attended')
            )
            ->groupBy('volunteer.Volunteer_ID', 'users.name', 'users.email')
            ->orderByDesc('total_hours')
            ->limit($limit)
            ->get();
    }

    /**
     * Validate hours value
     */
    public function validateHours(float $hours): array
    {
        $errors = [];

        if ($hours < 0) {
            $errors[] = 'Hours cannot be negative';
        }

        if ($hours > 24) {
            $errors[] = 'Hours cannot exceed 24 in a single day';
        }

        if (! is_numeric($hours)) {
            $errors[] = 'Hours must be a valid number';
        }

        return $errors;
    }

    /**
     * Get hours statistics for an event
     */
    public function getEventHoursStatistics(int $eventId): array
    {
        $stats = DB::table('event_participation')
            ->where('Event_ID', $eventId)
            ->where('Status', 'Attended')
            ->select(
                DB::raw('COUNT(*) as volunteers_attended'),
                DB::raw('SUM(Total_Hours) as total_hours'),
                DB::raw('AVG(Total_Hours) as avg_hours'),
                DB::raw('MAX(Total_Hours) as max_hours'),
                DB::raw('MIN(Total_Hours) as min_hours')
            )
            ->first();

        return [
            'volunteers_attended' => $stats->volunteers_attended ?? 0,
            'total_hours' => round($stats->total_hours ?? 0, 2),
            'avg_hours' => round($stats->avg_hours ?? 0, 2),
            'max_hours' => round($stats->max_hours ?? 0, 2),
            'min_hours' => round($stats->min_hours ?? 0, 2),
        ];
    }

    /**
     * Auto-calculate hours for completed events that haven't been processed
     */
    public function autoCalculateForCompletedEvents(): int
    {
        $completedEvents = Event::where('Status', 'Completed')
            ->whereHas('eventParticipations', function ($query) {
                $query->where('Total_Hours', 0)
                    ->whereIn('Status', ['Registered', 'Attended']);
            })
            ->get();

        $totalUpdated = 0;

        foreach ($completedEvents as $event) {
            $updated = $this->autoCalculateForEvent($event);
            $totalUpdated += $updated;
        }

        if ($totalUpdated > 0) {
            Log::info("Auto-calculated hours for {$totalUpdated} volunteers across {$completedEvents->count()} completed events");
        }

        return $totalUpdated;
    }
}
