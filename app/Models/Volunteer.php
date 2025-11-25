<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Volunteer Model
class Volunteer extends Model
{
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

    public function user()
    {
        return $this->belongsTo(User::class, 'User_ID');
    }
}
