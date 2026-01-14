<?php

// File: app/Models/Volunteer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;

class Volunteer extends Model
{
    use AsPivot, HasFactory;

    /**
     * Database connection for this model
     * Connection: sashvini (MariaDB)
     * Tables: volunteer, volunteer_skill, skill, event_participation
     */
    protected $connection = 'sashvini';

    protected $table = 'volunteer';

    protected $primaryKey = 'Volunteer_ID';

    protected $fillable = [
        'User_ID',
        'Availability',
        'Address',
        'City',
        'State',
        'Gender',
        'Phone_Num',
        'Description',
    ];

    // Relationships

    /**
     * Get the user account for this volunteer (izzhilmy database - PostgreSQL)
     * ⚠️ Cross-database relationship - uses separate query to avoid connection mutation
     */
    public function user()
    {
        // Direct query to avoid setConnection() mutation issue
        return User::where('id', $this->User_ID);
    }

    /**
     * Get the user object directly (cross-database safe)
     */
    public function getUser()
    {
        return User::find($this->User_ID);
    }

    /**
     * Get all skills for this volunteer (same database - sashvini)
     */
    public function skills()
    {
        return $this->belongsToMany(
            Skill::class,
            'volunteer_skill',
            'Volunteer_ID',
            'Skill_ID'
        )->withPivot('Skill_Level')->withTimestamps();
    }

    /**
     * Get all event participations for this volunteer (same database - sashvini)
     */
    public function eventParticipations()
    {
        return $this->hasMany(EventParticipation::class, 'Volunteer_ID', 'Volunteer_ID');
    }

    /**
     * Get event IDs for this volunteer (cross-database safe)
     * DO NOT use setConnection() as it mutates the model's connection
     */
    public function getEventIds(): array
    {
        return $this->eventParticipations()->pluck('Event_ID')->toArray();
    }

    /**
     * Get events for this volunteer (cross-database safe)
     * Uses separate queries instead of belongsToMany to avoid connection mutation
     */
    public function getEvents()
    {
        $eventIds = $this->getEventIds();

        if (empty($eventIds)) {
            return collect();
        }

        return Event::whereIn('Event_ID', $eventIds)->get();
    }
}
