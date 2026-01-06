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
     * ⚠️ Cross-database relationship
     */
    public function user()
    {
        return $this->setConnection('izzhilmy')
            ->belongsTo(User::class, 'User_ID');
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
     * Get all events for this volunteer through event_participation (izzati database - PostgreSQL)
     * ⚠️ Cross-database relationship with pivot in sashvini database
     */
    public function events()
    {
        return $this->setConnection('izzati')
            ->belongsToMany(
                Event::class,
                'event_participation',
                'Volunteer_ID',
                'Event_ID'
            )->withPivot('Status', 'Total_Hours', 'Role_ID')->withTimestamps();
    }
}
