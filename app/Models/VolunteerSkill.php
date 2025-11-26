<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerSkill extends Model
{
    use HasFactory;

    protected $table = 'volunteer_skill';

    // Composite primary key
    protected $primaryKey = ['Skill_ID', 'Volunteer_ID'];
    public $incrementing = false;

    protected $fillable = [
        'Skill_ID',
        'Volunteer_ID',
        'Skill_Level'
    ];

    // Relationships
    public function skill()
    {
        return $this->belongsTo(Skill::class, 'Skill_ID', 'Skill_ID');
    }

    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class, 'Volunteer_ID', 'Volunteer_ID');
    }
}
