<?php

// File: app/Models/Donor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    use HasFactory;

    /**
     * Database connection for this model
     * Connection: hannah (MySQL)
     * Tables: donor, donation, donation_allocation
     */
    protected $connection = 'hannah';

    protected $table = 'donor';

    protected $primaryKey = 'Donor_ID';

    protected $fillable = [
        'User_ID',
        'Full_Name',
        'Phone_Num',
        'Total_Donated',
    ];

    protected $casts = [
        'Total_Donated' => 'decimal:2',
    ];

    // Relationships

    /**
     * Get the user account for this donor (izzhilmy database - PostgreSQL)
     * ⚠️ Cross-database relationship - User model has its own $connection property
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'User_ID');
    }

    /**
     * Get all donations made by this donor (same database - hannah)
     */
    public function donations()
    {
        return $this->hasMany(Donation::class, 'Donor_ID', 'Donor_ID');
    }
}
