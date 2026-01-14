<?php

// File: app/Models/Event.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    /**
     * Database connection for this model
     * Connection: izzati (PostgreSQL)
     * Tables: organization, event, campaign, event_role
     */
    protected $connection = 'izzati';

    protected $table = 'event';

    protected $primaryKey = 'Event_ID';

    protected $fillable = [
        'Organizer_ID',
        'Title',
        'Description',
        'Location',
        'Start_Date',
        'End_Date',
        'Capacity',
        'Status',
    ];

    protected $casts = [
        'Start_Date' => 'date',
        'End_Date' => 'date',
        'Capacity' => 'integer',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'Event_ID';
    }

    // Relationships

    /**
     * Get the organization that owns this event (same database - izzati)
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'Organizer_ID', 'Organization_ID');
    }

    /**
     * Get all event participations for this event (sashvini database - MariaDB)
     * ⚠️ Cross-database relationship - use separate query instead of Eloquent relationship
     * DO NOT use setConnection() as it mutates the model's connection
     */
    public function eventParticipations()
    {
        // Return a query builder for event_participation on sashvini
        // This avoids the setConnection() issue that corrupts the model
        return EventParticipation::where('Event_ID', $this->Event_ID);
    }

    /**
     * Get volunteer count for this event (cross-database safe)
     * Uses direct query instead of relationship to avoid connection mutation
     */
    public function getVolunteerCount(): int
    {
        return EventParticipation::where('Event_ID', $this->Event_ID)->count();
    }

    /**
     * Get all roles for this event (same database - izzati)
     */
    public function roles()
    {
        return $this->hasMany(EventRole::class, 'Event_ID', 'Event_ID');
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('Status', 'Upcoming');
    }

    public function scopeCompleted($query)
    {
        return $query->where('Status', 'Completed');
    }

    public function scopeOngoing($query)
    {
        return $query->where('Status', 'Ongoing');
    }

    // Helper methods for volunteer counts
    public function getTotalVolunteerCapacity()
    {
        // Use cached roles if already loaded, otherwise query
        if ($this->relationLoaded('roles')) {
            return $this->roles->sum('Volunteers_Needed') ?: $this->Capacity;
        }

        return $this->roles()->sum('Volunteers_Needed') ?: $this->Capacity;
    }

    public function getTotalVolunteersFilled()
    {
        // Use cached roles if already loaded
        if ($this->relationLoaded('roles')) {
            $rolesFilled = $this->roles->sum('Volunteers_Filled');
            if ($rolesFilled > 0) {
                return $rolesFilled;
            }
        } else {
            $rolesFilled = $this->roles()->sum('Volunteers_Filled');
            if ($rolesFilled > 0) {
                return $rolesFilled;
            }
        }

        // Fallback to counting event participations (cross-database safe)
        return $this->getVolunteerCount();
    }
}
