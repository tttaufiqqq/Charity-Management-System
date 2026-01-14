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
     * ⚠️ Cross-database relationship - DonationAllocation model has its own $connection property
     */
    public function donationAllocations()
    {
        return $this->hasMany(DonationAllocation::class, 'Recipient_ID', 'Recipient_ID');
    }

    /**
     * Get campaign IDs that allocated funds to this recipient (cross-database safe helper)
     */
    public function getCampaignIds(): array
    {
        return $this->donationAllocations()->pluck('Campaign_ID')->unique()->toArray();
    }

    /**
     * Get campaigns that allocated funds to this recipient (cross-database safe helper)
     */
    public function getCampaigns()
    {
        $campaignIds = $this->getCampaignIds();

        if (empty($campaignIds)) {
            return collect();
        }

        return Campaign::whereIn('Campaign_ID', $campaignIds)->get();
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
