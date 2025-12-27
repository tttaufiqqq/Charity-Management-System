<?php

namespace App\Services\Api;

class CampaignApiService extends BaseApiService
{
    public function __construct()
    {
        $this->baseUrl = env('EVENT_SERVICE_URL', 'http://localhost:8003').'/api/v1';
        $this->serviceName = 'campaign';
    }

    /**
     * Get campaign by ID
     */
    public function find(int $campaignId, int $cacheTtl = 300)
    {
        return $this->get("campaigns/{$campaignId}", [], $cacheTtl);
    }

    /**
     * Validate if campaign exists and is active
     */
    public function exists(int $campaignId): bool
    {
        try {
            $campaign = $this->find($campaignId, 300);

            return ! empty($campaign);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if campaign is active
     */
    public function isActive(int $campaignId): bool
    {
        try {
            $campaign = $this->find($campaignId, 300);

            return isset($campaign['Status']) && $campaign['Status'] === 'Active';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get all campaigns (with filters)
     */
    public function all(array $filters = [])
    {
        return $this->get('campaigns', $filters);
    }

    /**
     * Get active campaigns
     */
    public function active()
    {
        return $this->get('campaigns', ['status' => 'Active']);
    }

    /**
     * Create a new campaign
     */
    public function create(array $data)
    {
        return $this->post('campaigns', $data);
    }

    /**
     * Update campaign
     */
    public function update(int $campaignId, array $data)
    {
        $this->invalidateCache("campaigns/{$campaignId}");

        return $this->put("campaigns/{$campaignId}", $data);
    }

    /**
     * Update campaign collected amount
     */
    public function updateCollectedAmount(int $campaignId, float $amount)
    {
        $this->invalidateCache("campaigns/{$campaignId}");

        return $this->put("campaigns/{$campaignId}/collected-amount", [
            'amount' => $amount,
        ]);
    }

    /**
     * Sync campaign collected amount (for reconciliation)
     */
    public function syncCollectedAmount(int $campaignId, float $totalAmount)
    {
        $this->invalidateCache("campaigns/{$campaignId}");

        return $this->put("campaigns/{$campaignId}/sync-collected-amount", [
            'total_amount' => $totalAmount,
        ]);
    }

    /**
     * Get available funds for allocation
     */
    public function getAvailableFunds(int $campaignId): float
    {
        try {
            $result = $this->get("campaigns/{$campaignId}/available-funds");

            return $result['available_funds'] ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Approve campaign (admin action)
     */
    public function approve(int $campaignId)
    {
        $this->invalidateCache("campaigns/{$campaignId}");

        return $this->put("campaigns/{$campaignId}/approve", []);
    }

    /**
     * Reject campaign (admin action)
     */
    public function reject(int $campaignId, string $reason = '')
    {
        $this->invalidateCache("campaigns/{$campaignId}");

        return $this->put("campaigns/{$campaignId}/reject", ['reason' => $reason]);
    }
}
