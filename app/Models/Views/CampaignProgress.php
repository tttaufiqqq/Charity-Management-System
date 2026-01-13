<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

/**
 * Read-only model for vw_campaign_progress view (izzati database)
 * Provides campaign progress analytics
 */
class CampaignProgress extends Model
{
    protected $connection = 'izzati';

    protected $table = 'vw_campaign_progress';

    protected $primaryKey = 'Campaign_ID';

    public $timestamps = false;

    protected $casts = [
        'Goal_Amount' => 'decimal:2',
        'Collected_Amount' => 'decimal:2',
        'progress_percentage' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'Start_Date' => 'date',
        'End_Date' => 'date',
        'days_remaining' => 'integer',
        'is_expired' => 'boolean',
        'is_active_period' => 'boolean',
        'campaign_created_at' => 'datetime',
        'campaign_updated_at' => 'datetime',
    ];

    /**
     * Scope for active campaigns
     */
    public function scopeActive($query)
    {
        return $query->where('campaign_status', 'Active');
    }

    /**
     * Scope for completed campaigns
     */
    public function scopeCompleted($query)
    {
        return $query->where('campaign_status', 'Completed');
    }

    /**
     * Scope for campaigns that reached goal
     */
    public function scopeGoalReached($query)
    {
        return $query->where('funding_status', 'Goal Reached');
    }

    /**
     * Scope for expired campaigns
     */
    public function scopeExpired($query)
    {
        return $query->where('is_expired', true);
    }

    /**
     * Scope for campaigns in active period
     */
    public function scopeInActivePeriod($query)
    {
        return $query->where('is_active_period', true);
    }

    /**
     * Scope for top performing campaigns
     */
    public function scopeTopPerforming($query, int $limit = 10)
    {
        return $query->orderByDesc('Collected_Amount')->limit($limit);
    }

    /**
     * Scope for campaigns by organization
     */
    public function scopeForOrganization($query, int $organizationId)
    {
        return $query->where('Organization_ID', $organizationId);
    }

    /**
     * Scope for campaigns needing attention (low progress, ending soon)
     */
    public function scopeNeedingAttention($query)
    {
        return $query->where('campaign_status', 'Active')
            ->where('days_remaining', '<=', 7)
            ->where('progress_percentage', '<', 50);
    }
}
