<?php

// File: app/Models/Volunteer.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;

class Volunteer extends Model
{
    use HasFactory, AsPivot;

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
        'Description'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'User_ID');
    }

    public function skills()
    {
        return $this->belongsToMany(
            Skill::class,
            'volunteer_skill',
            'Volunteer_ID',
            'Skill_ID'
        )->withPivot('Skill_Level')->withTimestamps();
    }

    public function eventParticipations()
    {
        return $this->hasMany(EventParticipation::class, 'Volunteer_ID', 'Volunteer_ID');
    }

    public function events()
    {
        return $this->belongsToMany(
            Event::class,
            'event_participation',
            'Volunteer_ID',
            'Event_ID'
        )->withPivot('Status', 'Total_Hours')->withTimestamps();
    }
}
