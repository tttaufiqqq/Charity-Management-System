<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRole extends Model
{
    use HasFactory;

    /**
     * Database connection for this model
     * Connection: izzati (PostgreSQL)
     * Tables: organization, event, campaign, event_role
     */
    protected $connection = 'izzati';

    protected $table = 'event_role';

    protected $primaryKey = 'Role_ID';

    protected $fillable = [
        'Event_ID',
        'Role_Name',
        'Role_Description',
        'Volunteers_Needed',
        'Volunteers_Filled',
    ];

    protected $casts = [
        'Volunteers_Needed' => 'integer',
        'Volunteers_Filled' => 'integer',
    ];

    // Relationships

    /**
     * Get the event for this role (same database - izzati)
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'Event_ID', 'Event_ID');
    }

    /**
     * Get event participations for this role (sashvini database - MariaDB)
     * ⚠️ Cross-database relationship - use hasMany instead of belongsToMany
     */
    public function eventParticipations()
    {
        return $this->hasMany(EventParticipation::class, 'Role_ID', 'Role_ID');
    }

    /**
     * Get volunteer IDs for this role (cross-database safe helper)
     */
    public function getVolunteerIds(): array
    {
        return $this->eventParticipations()->pluck('Volunteer_ID')->toArray();
    }

    /**
     * Get volunteers for this role (cross-database safe helper)
     */
    public function getVolunteers()
    {
        $volunteerIds = $this->getVolunteerIds();

        if (empty($volunteerIds)) {
            return collect();
        }

        return Volunteer::whereIn('Volunteer_ID', $volunteerIds)->get();
    }

    // Check if role is full
    public function isFull()
    {
        return $this->Volunteers_Filled >= $this->Volunteers_Needed;
    }

    // Get available slots
    public function getAvailableSlots()
    {
        return max(0, $this->Volunteers_Needed - $this->Volunteers_Filled);
    }
}
