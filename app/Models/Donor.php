<?php

// File: app/Models/Donor.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    use HasFactory;

    protected $table = 'donor';
    protected $primaryKey = 'Donor_ID';

    protected $fillable = [
        'User_ID',
        'Full_Name',
        'Phone_Num',
        'Total_Donated'
    ];

    protected $casts = [
        'Total_Donated' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'User_ID');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class, 'Donor_ID', 'Donor_ID');
    }
}
