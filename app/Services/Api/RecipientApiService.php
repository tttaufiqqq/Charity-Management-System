<?php

namespace App\Services\Api;

class RecipientApiService extends BaseApiService
{
    public function __construct()
    {
        $this->baseUrl = env('RECIPIENT_SERVICE_URL', 'http://localhost:8005').'/api/v1';
        $this->serviceName = 'recipient';
    }

    /**
     * Get recipient by ID
     */
    public function find(int $recipientId, int $cacheTtl = 300)
    {
        return $this->get("recipients/{$recipientId}", [], $cacheTtl);
    }

    /**
     * Validate if recipient exists and is approved
     */
    public function exists(int $recipientId): bool
    {
        try {
            $recipient = $this->find($recipientId, 300);

            return ! empty($recipient);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if recipient is approved
     */
    public function isApproved(int $recipientId): bool
    {
        try {
            $recipient = $this->find($recipientId, 300);

            return isset($recipient['Status']) && $recipient['Status'] === 'Approved';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get all recipients (with filters)
     */
    public function all(array $filters = [])
    {
        return $this->get('recipients', $filters);
    }

    /**
     * Get approved recipients
     */
    public function approved()
    {
        return $this->get('recipients', ['status' => 'Approved']);
    }

    /**
     * Create a new recipient application
     */
    public function create(array $data)
    {
        return $this->post('recipients', $data);
    }

    /**
     * Update recipient
     */
    public function update(int $recipientId, array $data)
    {
        $this->invalidateCache("recipients/{$recipientId}");

        return $this->put("recipients/{$recipientId}", $data);
    }

    /**
     * Approve recipient (admin action)
     */
    public function approve(int $recipientId)
    {
        $this->invalidateCache("recipients/{$recipientId}");

        return $this->put("recipients/{$recipientId}/approve", []);
    }

    /**
     * Reject recipient (admin action)
     */
    public function reject(int $recipientId, string $reason = '')
    {
        $this->invalidateCache("recipients/{$recipientId}");

        return $this->put("recipients/{$recipientId}/reject", ['reason' => $reason]);
    }

    /**
     * Get recipient allocations
     */
    public function getAllocations(int $recipientId)
    {
        return $this->get("recipients/{$recipientId}/allocations");
    }
}
