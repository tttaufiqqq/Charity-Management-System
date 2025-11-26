<?php

// File: app/Models/Organization.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $table = 'organization';
    protected $primaryKey = 'Organization_ID';

    protected $fillable = [
        'Organizer_ID',
        'Phone_No',
        'Register_No',
        'Address',
        'State',
        'City',
        'Description'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'Organizer_ID');
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'Organization_ID', 'Organization_ID');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'Organizer_ID', 'Organization_ID');
    }
}
