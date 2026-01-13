<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

/**
 * Read-only model for vw_donor_donation_summary view (hannah database)
 * Provides donor statistics and donation history summary
 */
class DonorDonationSummary extends Model
{
    protected $connection = 'hannah';

    protected $table = 'vw_donor_donation_summary';

    protected $primaryKey = 'Donor_ID';

    public $timestamps = false;

    protected $casts = [
        'cached_total_donated' => 'decimal:2',
        'actual_total_donated' => 'decimal:2',
        'avg_donation_amount' => 'decimal:2',
        'total_donation_count' => 'integer',
        'completed_donation_count' => 'integer',
        'campaigns_supported' => 'integer',
        'last_donation_date' => 'date',
        'first_donation_date' => 'date',
        'donor_since' => 'datetime',
        'last_updated' => 'datetime',
    ];

    /**
     * Scope for filtering by donor tier
     */
    public function scopeTier($query, string $tier)
    {
        return $query->where('donor_tier', $tier);
    }

    /**
     * Scope for top donors
     */
    public function scopeTopDonors($query, int $limit = 10)
    {
        return $query->orderByDesc('cached_total_donated')->limit($limit);
    }

    /**
     * Scope for active donors (have donated)
     */
    public function scopeActive($query)
    {
        return $query->where('cached_total_donated', '>', 0);
    }

    /**
     * Scope for platinum donors
     */
    public function scopePlatinum($query)
    {
        return $query->where('donor_tier', 'Platinum');
    }

    /**
     * Get donor by User_ID
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('User_ID', $userId);
    }
}
