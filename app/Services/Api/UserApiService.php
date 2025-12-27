<?php

namespace App\Services\Api;

class UserApiService extends BaseApiService
{
    public function __construct()
    {
        $this->baseUrl = env('USER_SERVICE_URL', 'http://localhost:8001').'/api/v1';
        $this->serviceName = 'user';
    }

    /**
     * Get user by ID
     */
    public function find(int $userId, int $cacheTtl = 300)
    {
        return $this->get("users/{$userId}", [], $cacheTtl);
    }

    /**
     * Validate if user exists
     */
    public function exists(int $userId): bool
    {
        try {
            $user = $this->find($userId, 300);

            return ! empty($user);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get all users (with optional role filter)
     */
    public function all(array $filters = [])
    {
        return $this->get('users', $filters);
    }

    /**
     * Create a new user
     */
    public function create(array $data)
    {
        return $this->post('users', $data);
    }

    /**
     * Update user
     */
    public function update(int $userId, array $data)
    {
        $this->invalidateCache("users/{$userId}");

        return $this->put("users/{$userId}", $data);
    }

    /**
     * Delete user
     */
    public function destroy(int $userId)
    {
        $this->invalidateCache("users/{$userId}");

        return $this->delete("users/{$userId}");
    }

    /**
     * Get users by role
     */
    public function getByRole(string $role)
    {
        return $this->get('users', ['role' => $role]);
    }

    /**
     * Validate user has specific role
     */
    public function hasRole(int $userId, string $role): bool
    {
        try {
            $user = $this->find($userId, 300);

            return isset($user['roles']) && in_array($role, $user['roles']);
        } catch (\Exception $e) {
            return false;
        }
    }
}
