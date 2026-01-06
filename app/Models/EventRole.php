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
     * Get all volunteers for this role through event_participation (sashvini database - MariaDB)
     * ⚠️ Cross-database relationship with pivot in sashvini database
     */
    public function volunteers()
    {
        return $this->setConnection('sashvini')
            ->belongsToMany(
                Volunteer::class,
                'event_participation',
                'Role_ID',
                'Volunteer_ID'
            )->withPivot('Status', 'Total_Hours', 'Event_ID')->withTimestamps();
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
