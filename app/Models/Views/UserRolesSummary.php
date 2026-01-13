<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

/**
 * Read-only model for vw_user_roles_summary view (izzhilmy database)
 * Provides user summary with role information
 */
class UserRolesSummary extends Model
{
    protected $connection = 'izzhilmy';

    protected $table = 'vw_user_roles_summary';

    protected $primaryKey = 'user_id';

    public $timestamps = false;

    protected $casts = [
        'registered_at' => 'datetime',
        'role_count' => 'integer',
    ];

    /**
     * Scope for filtering by role
     */
    public function scopeWithRole($query, string $role)
    {
        return $query->where('user_roles', 'LIKE', "%{$role}%");
    }

    /**
     * Scope for new users (last 30 days)
     */
    public function scopeNewUsers($query)
    {
        return $query->where('user_status', 'New');
    }

    /**
     * Scope for users with no role assigned
     */
    public function scopeNoRole($query)
    {
        return $query->where('user_roles', 'No Role');
    }
}
