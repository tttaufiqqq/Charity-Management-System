<?php

// File: app/Models/Event.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

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
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'Organizer_ID', 'Organization_ID');
    }

    public function eventParticipations()
    {
        return $this->hasMany(EventParticipation::class, 'Event_ID', 'Event_ID');
    }

    public function volunteers()
    {
        return $this->belongsToMany(
            Volunteer::class,
            'event_participation',
            'Event_ID',
            'Volunteer_ID'
        )->withPivot('Status', 'Total_Hours', 'Role_ID')->withTimestamps();
    }

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
}
