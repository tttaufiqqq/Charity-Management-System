<?php

namespace App\Services\Api;

class OrganizationApiService extends BaseApiService
{
    public function __construct()
    {
        $this->baseUrl = env('EVENT_SERVICE_URL', 'http://localhost:8003').'/api/v1';
        $this->serviceName = 'organization';
    }

    /**
     * Get organization by ID
     */
    public function find(int $organizationId, int $cacheTtl = 300)
    {
        return $this->get("organizations/{$organizationId}", [], $cacheTtl);
    }

    /**
     * Validate if organization exists
     */
    public function exists(int $organizationId): bool
    {
        try {
            $organization = $this->find($organizationId, 300);

            return ! empty($organization);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get all organizations
     */
    public function all(array $filters = [])
    {
        return $this->get('organizations', $filters);
    }

    /**
     * Create a new organization
     */
    public function create(array $data)
    {
        return $this->post('organizations', $data);
    }

    /**
     * Update organization
     */
    public function update(int $organizationId, array $data)
    {
        $this->invalidateCache("organizations/{$organizationId}");

        return $this->put("organizations/{$organizationId}", $data);
    }

    /**
     * Get organization campaigns
     */
    public function getCampaigns(int $organizationId)
    {
        return $this->get("organizations/{$organizationId}/campaigns");
    }

    /**
     * Get organization events
     */
    public function getEvents(int $organizationId)
    {
        return $this->get("organizations/{$organizationId}/events");
    }
}
