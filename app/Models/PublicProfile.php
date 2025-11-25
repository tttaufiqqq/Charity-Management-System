<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Public Profile Model
class PublicProfile extends Model
{
    protected $table = 'public';
    protected $primaryKey = 'Public_ID';

    protected $fillable = [
        'User_ID',
        'Full_Name',
        'Phone',
        'Email',
        'Position'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'User_ID');
    }
}

