<?php

// File: app/Models/PublicProfile.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicProfile extends Model
{
    use HasFactory;

    protected $table = 'public';
    protected $primaryKey = 'Public_ID';

    protected $fillable = [
        'User_ID',
        'Full_Name',
        'Phone',
        'Email',
        'Position'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'User_ID');
    }

    public function recipients()
    {
        return $this->hasMany(Recipient::class, 'Public_ID', 'Public_ID');
    }
}

