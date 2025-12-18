<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRole extends Model
{
    use HasFactory;

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
    public function event()
    {
        return $this->belongsTo(Event::class, 'Event_ID', 'Event_ID');
    }

    public function volunteers()
    {
        return $this->belongsToMany(
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
