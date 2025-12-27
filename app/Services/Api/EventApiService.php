<?php

namespace App\Services\Api;

class EventApiService extends BaseApiService
{
    public function __construct()
    {
        $this->baseUrl = env('VOLUNTEER_SERVICE_URL', 'http://localhost:8002').'/api/v1';
        $this->serviceName = 'event';
    }

    /**
     * Get event by ID
     */
    public function find(int $eventId, int $cacheTtl = 300)
    {
        return $this->get("events/{$eventId}", [], $cacheTtl);
    }

    /**
     * Validate if event exists
     */
    public function exists(int $eventId): bool
    {
        try {
            $event = $this->find($eventId, 300);

            return ! empty($event);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if event is active/upcoming
     */
    public function isUpcoming(int $eventId): bool
    {
        try {
            $event = $this->find($eventId, 300);

            return isset($event['Start_Date']) && strtotime($event['Start_Date']) > time();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get all events (with filters)
     */
    public function all(array $filters = [])
    {
        return $this->get('events', $filters);
    }

    /**
     * Get upcoming events
     */
    public function upcoming()
    {
        return $this->get('events', ['filter' => 'upcoming']);
    }

    /**
     * Create a new event
     */
    public function create(array $data)
    {
        return $this->post('events', $data);
    }

    /**
     * Update event
     */
    public function update(int $eventId, array $data)
    {
        $this->invalidateCache("events/{$eventId}");

        return $this->put("events/{$eventId}", $data);
    }

    /**
     * Get event participants
     */
    public function getParticipants(int $eventId)
    {
        return $this->get("events/{$eventId}/participants");
    }

    /**
     * Register volunteer for event
     */
    public function registerVolunteer(int $eventId, int $volunteerId, int $roleId)
    {
        return $this->post("events/{$eventId}/register", [
            'volunteer_id' => $volunteerId,
            'role_id' => $roleId,
        ]);
    }

    /**
     * Cancel volunteer registration
     */
    public function cancelRegistration(int $eventId, int $volunteerId)
    {
        return $this->delete("events/{$eventId}/participants/{$volunteerId}");
    }

    /**
     * Update participant hours
     */
    public function updateParticipantHours(int $eventId, int $volunteerId, float $hours)
    {
        return $this->put("events/{$eventId}/participants/{$volunteerId}/hours", [
            'hours' => $hours,
        ]);
    }

    /**
     * Get event role from Event Management service
     */
    public function getRole(int $roleId)
    {
        $eventMgmtUrl = env('EVENT_SERVICE_URL', 'http://localhost:8003');
        $url = "{$eventMgmtUrl}/api/v1/event-roles/{$roleId}";

        try {
            $response = \Illuminate\Support\Facades\Http::get($url);

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
