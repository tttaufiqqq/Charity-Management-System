<?php

// File: app/Models/Organization.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    /**
     * Database connection for this model
     * Connection: izzati (PostgreSQL)
     * Tables: organization, event, campaign, event_role
     */
    protected $connection = 'izzati';

    protected $table = 'organization';

    protected $primaryKey = 'Organization_ID';

    protected $fillable = [
        'Organizer_ID',
        'Phone_No',
        'Register_No',
        'Address',
        'State',
        'City',
        'Description',
    ];

    // Relationships

    /**
     * Get the user who owns this organization (izzhilmy database - PostgreSQL)
     * ⚠️ Cross-database relationship - User model has its own $connection property
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'Organizer_ID');
    }

    /**
     * Get all campaigns for this organization (same database - izzati)
     */
    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'Organization_ID', 'Organization_ID');
    }

    /**
     * Get all events for this organization (same database - izzati)
     */
    public function events()
    {
        return $this->hasMany(Event::class, 'Organizer_ID', 'Organization_ID');
    }
}
