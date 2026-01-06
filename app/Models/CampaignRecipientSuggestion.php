<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignRecipientSuggestion extends Model
{
    /**
     * Database connection for this model
     * Connection: izzati (PostgreSQL)
     * Tables: organization, event, campaign, event_role, campaign_recipient_suggestions
     */
    protected $connection = 'izzati';

    protected $table = 'campaign_recipient_suggestions';

    protected $fillable = [
        'Campaign_ID',
        'Recipient_ID',
        'Suggested_By',
        'Suggestion_Reason',
        'Status',
    ];

    // Relationships

    /**
     * Get the campaign for this suggestion (same database - izzati)
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'Campaign_ID', 'Campaign_ID');
    }

    /**
     * Get the recipient for this suggestion (adam database - MySQL)
     * ⚠️ Cross-database relationship
     */
    public function recipient()
    {
        return $this->setConnection('adam')
            ->belongsTo(Recipient::class, 'Recipient_ID', 'Recipient_ID');
    }

    /**
     * Get the user who suggested this recipient (izzhilmy database - PostgreSQL)
     * ⚠️ Cross-database relationship
     */
    public function suggestedBy()
    {
        return $this->setConnection('izzhilmy')
            ->belongsTo(User::class, 'Suggested_By');
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
