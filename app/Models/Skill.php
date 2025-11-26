<?php

// File: app/Models/Skill.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $table = 'skill';
    protected $primaryKey = 'Skill_ID';

    protected $fillable = [
        'Skill_Name',
        'Description'
    ];

    // Relationships
    public function volunteers()
    {
        return $this->belongsToMany(
            Volunteer::class,
            'volunteer_skill',
            'Skill_ID',
            'Volunteer_ID'
        )->withPivot('Skill_Level')->withTimestamps();
    }
}
