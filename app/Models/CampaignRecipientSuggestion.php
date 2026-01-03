<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignRecipientSuggestion extends Model
{
    protected $table = 'campaign_recipient_suggestions';

    protected $fillable = [
        'Campaign_ID',
        'Recipient_ID',
        'Suggested_By',
        'Suggestion_Reason',
        'Status',
    ];

    // Relationships
    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'Campaign_ID', 'Campaign_ID');
    }

    public function recipient()
    {
        return $this->belongsTo(Recipient::class, 'Recipient_ID', 'Recipient_ID');
    }

    public function suggestedBy()
    {
        return $this->belongsTo(User::class, 'Suggested_By');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('Status', 'Pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('Status', 'Accepted');
    }

    public function scopeRejected($query)
    {
        return $query->where('Status', 'Rejected');
    }
}
