<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventParticipation extends Model
{
    use HasFactory;

    protected $table = 'event_participation';

    // Composite primary key
    protected $primaryKey = ['Volunteer_ID', 'Event_ID'];
    public $incrementing = false;

    protected $fillable = [
        'Volunteer_ID',
        'Event_ID',
        'Status',
        'Total_Hours'
    ];

    protected $casts = [
        'Total_Hours' => 'integer',
    ];

    // Relationships
    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class, 'Volunteer_ID', 'Volunteer_ID');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'Event_ID', 'Event_ID');
    }
}
