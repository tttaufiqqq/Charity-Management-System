<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Donor Model
class Donor extends Model
{
    protected $table = 'donor';
    protected $primaryKey = 'Donor_ID';

    protected $fillable = [
        'User_ID',
        'Full_Name',
        'Phone_Num',
        'Total_Donated'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'User_ID');
    }

    public function donations(){
        return $this->hasMany(Donation::class, 'Donor_ID');
    }
}
