<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventParticipation extends Model
{
    use HasFactory;

    /**
     * Database connection for this model
     * Connection: sashvini (MariaDB)
     * Tables: volunteer, volunteer_skill, skill, event_participation
     */
    protected $connection = 'sashvini';

    protected $table = 'event_participation';

    // Composite primary key
    protected $primaryKey = ['Volunteer_ID', 'Event_ID'];

    public $incrementing = false;

    protected $fillable = [
        'Volunteer_ID',
        'Event_ID',
        'Role_ID',
        'Status',
        'Total_Hours',
    ];

    protected $casts = [
        'Total_Hours' => 'integer',
    ];

    // Relationships

    /**
     * Get the volunteer for this participation (same database - sashvini)
     */
    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class, 'Volunteer_ID', 'Volunteer_ID');
    }

    /**
     * Get the event for this participation (izzati database - PostgreSQL)
     * ⚠️ Cross-database relationship - uses separate query to avoid connection mutation
     */
    public function event()
    {
        // Direct query to avoid setConnection() mutation issue
        return Event::where('Event_ID', $this->Event_ID);
    }

    /**
     * Get the event object directly (cross-database safe)
     */
    public function getEvent()
    {
        return Event::find($this->Event_ID);
    }
}
