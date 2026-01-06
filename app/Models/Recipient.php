<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
    use HasFactory;

    /**
     * Database connection for this model
     * Connection: adam (MySQL)
     * Tables: public_profile, recipient
     */
    protected $connection = 'adam';

    protected $table = 'recipient';

    protected $primaryKey = 'Recipient_ID';

    protected $fillable = [
        'Public_ID',
        'Name',
        'Address',
        'Contact',
        'Need_Description',
        'Status',
        'Approved_At',
    ];

    protected $casts = [
        'Approved_At' => 'datetime',
    ];

    // Relationships

    /**
     * Get the public profile for this recipient (same database - adam)
     */
    public function publicProfile()
    {
        return $this->belongsTo(PublicProfile::class, 'Public_ID', 'Public_ID');
    }

    /**
     * Get all donation allocations for this recipient (hannah database - MySQL)
     * ⚠️ Cross-database relationship
     */
    public function donationAllocations()
    {
        return $this->setConnection('hannah')
            ->hasMany(DonationAllocation::class, 'Recipient_ID', 'Recipient_ID');
    }

    /**
     * Get all campaigns that allocated funds to this recipient (izzati database - PostgreSQL)
     * ⚠️ Cross-database relationship with pivot in hannah database
     */
    public function campaigns()
    {
        return $this->setConnection('hannah')
            ->belongsToMany(
                Campaign::class,
                'donation_allocation',
                'Recipient_ID',
                'Campaign_ID'
            )->withPivot('Amount_Allocated', 'Allocated_At')->withTimestamps();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('Status', 'Pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('Status', 'Approved');
    }
}
