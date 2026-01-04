<?php

// File: app/Models/Campaign.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

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
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'Organization_ID', 'Organization_ID');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class, 'Campaign_ID', 'Campaign_ID');
    }

    public function donationAllocations()
    {
        return $this->hasMany(DonationAllocation::class, 'Campaign_ID', 'Campaign_ID');
    }

    public function recipients()
    {
        return $this->belongsToMany(
            Recipient::class,
            'donation_allocation',
            'Campaign_ID',
            'Recipient_ID'
        )->withPivot('Amount_Allocated', 'Allocated_At')->withTimestamps();
    }

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
