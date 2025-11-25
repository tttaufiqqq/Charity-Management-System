<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
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

    public function user()
    {
        return $this->belongsTo(User::class, 'Organizer_ID');
    }
}
