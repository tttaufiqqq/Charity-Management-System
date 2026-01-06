<?php

// File: app/Models/Campaign.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    /**
     * Database connection for this model
     * Connection: izzati (PostgreSQL)
     * Tables: organization, event, campaign, event_role
     */
    protected $connection = 'izzati';

    protected $table = 'campaign';

    protected $primaryKey = 'Campaign_ID';

    protected $fillable = [
        'Organization_ID',
        'Title',
        'Description',
        'Goal_Amount',
        'Collected_Amount',
        'Start_Date',
        'End_Date',
        'Status',
    ];

    protected $casts = [
        'Goal_Amount' => 'decimal:2',
        'Collected_Amount' => 'decimal:2',
        'Start_Date' => 'date',
        'End_Date' => 'date',
    ];

    // Relationships

    /**
     * Get the organization that owns this campaign (same database - izzati)
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'Organization_ID', 'Organization_ID');
    }

    /**
     * Get all donations for this campaign (hannah database - MySQL)
     * ⚠️ Cross-database relationship
     */
    public function donations()
    {
        return $this->setConnection('hannah')
            ->hasMany(Donation::class, 'Campaign_ID', 'Campaign_ID');
    }

    /**
     * Get all donation allocations for this campaign (hannah database - MySQL)
     * ⚠️ Cross-database relationship
     */
    public function donationAllocations()
    {
        return $this->setConnection('hannah')
            ->hasMany(DonationAllocation::class, 'Campaign_ID', 'Campaign_ID');
    }

    /**
     * Get recipients through donation allocations (adam database - MySQL)
     * ⚠️ Cross-database relationship with pivot in hannah database
     */
    public function recipients()
    {
        return $this->setConnection('hannah')
            ->belongsToMany(
                Recipient::class,
                'donation_allocation',
                'Campaign_ID',
                'Recipient_ID'
            )->withPivot('Amount_Allocated', 'Allocated_At')->withTimestamps();
    }

    /**
     * Get recipient suggestions for this campaign (izzati database - same DB)
     */
    public function recipientSuggestions()
    {
        return $this->hasMany(CampaignRecipientSuggestion::class, 'Campaign_ID', 'Campaign_ID');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('Status', 'Active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('Status', 'Completed');
    }
}
