<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
    use HasFactory;

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
    public function publicProfile()
    {
        return $this->belongsTo(PublicProfile::class, 'Public_ID', 'Public_ID');
    }

    public function donationAllocations()
    {
        return $this->hasMany(DonationAllocation::class, 'Recipient_ID', 'Recipient_ID');
    }

    public function campaigns()
    {
        return $this->belongsToMany(
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
