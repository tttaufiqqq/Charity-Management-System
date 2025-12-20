<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventStatusService
{
    /**
     * Transition event to a new status with validation
     */
    public function transitionTo(Event $event, string $newStatus): bool
    {
        $validTransitions = $this->getValidTransitions($event->Status);

        if (! in_array($newStatus, $validTransitions)) {
            Log::warning("Invalid status transition from {$event->Status} to {$newStatus} for Event ID {$event->Event_ID}");

            return false;
        }

        $event->update(['Status' => $newStatus]);

        Log::info("Event ID {$event->Event_ID} status changed from {$event->Status} to {$newStatus}");

        return true;
    }

    /**
     * Get valid status transitions from current status
     */
    private function getValidTransitions(string $currentStatus): array
    {
        return match ($currentStatus) {
            'Pending' => ['Upcoming', 'Rejected', 'Cancelled'],
            'Upcoming' => ['Ongoing', 'Cancelled'],
            'Ongoing' => ['Completed', 'Cancelled'],
            'Completed' => [],  // Terminal state
            'Rejected' => [],   // Terminal state
            'Cancelled' => [],  // Terminal state
            default => [],
        };
    }

    /**
     * Auto-update event statuses based on dates
     */
    public function autoUpdateStatuses(): array
    {
        $updated = [
            'to_ongoing' => 0,
            'to_completed' => 0,
        ];

        DB::transaction(function () use (&$updated) {
            // Update Upcoming events that have started to Ongoing
            $updated['to_ongoing'] = Event::where('Status', 'Upcoming')
                ->where('Start_Date', '<=', now())
                ->where('End_Date', '>=', now())
                ->update(['Status' => 'Ongoing', 'updated_at' => now()]);

            // Update Upcoming or Ongoing events that have ended to Completed
            $updated['to_completed'] = Event::whereIn('Status', ['Upcoming', 'Ongoing'])
                ->where('End_Date', '<', now())
                ->update(['Status' => 'Completed', 'updated_at' => now()]);
        });

        if ($updated['to_ongoing'] > 0 || $updated['to_completed'] > 0) {
            Log::info('Auto-updated event statuses', $updated);
        }

        return $updated;
    }

    /**
     * Check if event should auto-transition to Ongoing
     */
    public function shouldBeOngoing(Event $event): bool
    {
        return $event->Status === 'Upcoming'
            && $event->Start_Date <= now()
            && $event->End_Date >= now();
    }

    /**
     * Check if event should auto-transition to Completed
     */
    public function shouldBeCompleted(Event $event): bool
    {
        return in_array($event->Status, ['Upcoming', 'Ongoing'])
            && $event->End_Date < now();
    }

    /**
     * Get events that need status updates
     */
    public function getEventsNeedingStatusUpdate(): Collection
    {
        return Event::whereIn('Status', ['Upcoming', 'Ongoing'])
            ->where(function ($query) {
                $query->where(function ($q) {
                    // Upcoming events that should be Ongoing
                    $q->where('Status', 'Upcoming')
                        ->where('Start_Date', '<=', now())
                        ->where('End_Date', '>=', now());
                })->orWhere(function ($q) {
                    // Events that should be Completed
                    $q->whereIn('Status', ['Upcoming', 'Ongoing'])
                        ->where('End_Date', '<', now());
                });
            })
            ->get();
    }

    /**
     * Approve a pending event (admin action)
     */
    public function approve(Event $event): bool
    {
        if ($event->Status !== 'Pending') {
            return false;
        }

        // Check if event should go straight to Ongoing or Completed based on dates
        $now = now();
        if ($event->End_Date < $now) {
            $newStatus = 'Completed';
        } elseif ($event->Start_Date <= $now && $event->End_Date >= $now) {
            $newStatus = 'Ongoing';
        } else {
            $newStatus = 'Upcoming';
        }

        return $event->update(['Status' => $newStatus]);
    }

    /**
     * Reject a pending event (admin action)
     */
    public function reject(Event $event): bool
    {
        if ($event->Status !== 'Pending') {
            return false;
        }

        return $event->update(['Status' => 'Rejected']);
    }

    /**
     * Cancel an event (organizer action)
     */
    public function cancel(Event $event): bool
    {
        // Can only cancel if not already completed or rejected
        if (in_array($event->Status, ['Completed', 'Rejected', 'Cancelled'])) {
            return false;
        }

        return $event->update(['Status' => 'Cancelled']);
    }

    /**
     * Get status color for UI display
     */
    public function getStatusColor(string $status): string
    {
        return match ($status) {
            'Pending' => 'yellow',
            'Upcoming' => 'blue',
            'Ongoing' => 'green',
            'Completed' => 'gray',
            'Rejected' => 'red',
            'Cancelled' => 'orange',
            default => 'gray',
        };
    }

    /**
     * Get status badge class for Tailwind
     */
    public function getStatusBadgeClass(string $status): string
    {
        return match ($status) {
            'Pending' => 'bg-yellow-100 text-yellow-800',
            'Upcoming' => 'bg-blue-100 text-blue-800',
            'Ongoing' => 'bg-green-100 text-green-800',
            'Completed' => 'bg-gray-100 text-gray-800',
            'Rejected' => 'bg-red-100 text-red-800',
            'Cancelled' => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
