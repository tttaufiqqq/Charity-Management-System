<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

/**
 * Read-only model for vw_volunteer_hours_summary view (sashvini database)
 * Provides volunteer participation and hours summary
 */
class VolunteerHoursSummary extends Model
{
    protected $connection = 'sashvini';

    protected $table = 'vw_volunteer_hours_summary';

    protected $primaryKey = 'Volunteer_ID';

    public $timestamps = false;

    protected $casts = [
        'total_events' => 'integer',
        'registered_count' => 'integer',
        'attended_count' => 'integer',
        'cancelled_count' => 'integer',
        'total_volunteer_hours' => 'integer',
        'verified_hours' => 'integer',
        'avg_hours_per_event' => 'decimal:2',
        'attendance_rate' => 'decimal:2',
        'unique_roles_count' => 'integer',
        'last_activity' => 'datetime',
        'volunteer_since' => 'datetime',
    ];

    /**
     * Scope for filtering by volunteer tier
     */
    public function scopeTier($query, string $tier)
    {
        return $query->where('volunteer_tier', $tier);
    }

    /**
     * Scope for top volunteers by hours
     */
    public function scopeTopVolunteers($query, int $limit = 10)
    {
        return $query->orderByDesc('verified_hours')->limit($limit);
    }

    /**
     * Scope for active volunteers (have participated)
     */
    public function scopeActive($query)
    {
        return $query->where('total_events', '>', 0);
    }

    /**
     * Scope for legend tier volunteers
     */
    public function scopeLegends($query)
    {
        return $query->where('volunteer_tier', 'Legend');
    }

    /**
     * Scope for champions
     */
    public function scopeChampions($query)
    {
        return $query->where('volunteer_tier', 'Champion');
    }

    /**
     * Get volunteer by User_ID
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('User_ID', $userId);
    }

    /**
     * Scope for volunteers by city
     */
    public function scopeInCity($query, string $city)
    {
        return $query->where('City', $city);
    }

    /**
     * Scope for volunteers by state
     */
    public function scopeInState($query, string $state)
    {
        return $query->where('State', $state);
    }

    /**
     * Scope for high attendance rate (>= 80%)
     */
    public function scopeHighAttendance($query)
    {
        return $query->where('attendance_rate', '>=', 80);
    }
}
