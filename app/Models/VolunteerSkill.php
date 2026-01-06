<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerSkill extends Model
{
    use HasFactory;

    /**
     * Database connection for this model
     * Connection: sashvini (MariaDB)
     * Tables: volunteer, volunteer_skill, skill, event_participation
     */
    protected $connection = 'sashvini';

    protected $table = 'volunteer_skill';

    // Composite primary key
    protected $primaryKey = ['Skill_ID', 'Volunteer_ID'];

    public $incrementing = false;

    protected $fillable = [
        'Skill_ID',
        'Volunteer_ID',
        'Skill_Level',
    ];

    // Relationships

    /**
     * Get the skill for this volunteer skill (same database - sashvini)
     */
    public function skill()
    {
        return $this->belongsTo(Skill::class, 'Skill_ID', 'Skill_ID');
    }

    /**
     * Get the volunteer for this skill (same database - sashvini)
     */
    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class, 'Volunteer_ID', 'Volunteer_ID');
    }
}
