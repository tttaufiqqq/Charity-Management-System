<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

/**
 * Read-only model for vw_recipient_status_summary view (adam database)
 * Provides recipient status and profile summary
 */
class RecipientStatusSummary extends Model
{
    protected $connection = 'adam';

    protected $table = 'vw_recipient_status_summary';

    protected $primaryKey = 'Recipient_ID';

    public $timestamps = false;

    protected $casts = [
        'Approved_At' => 'datetime',
        'application_submitted_at' => 'datetime',
        'last_updated_at' => 'datetime',
        'days_since_application' => 'integer',
        'days_to_approval' => 'integer',
        'is_eligible_for_allocation' => 'boolean',
    ];

    /**
     * Scope for pending recipients
     */
    public function scopePending($query)
    {
        return $query->where('application_status', 'Pending');
    }

    /**
     * Scope for approved recipients
     */
    public function scopeApproved($query)
    {
        return $query->where('application_status', 'Approved');
    }

    /**
     * Scope for rejected recipients
     */
    public function scopeRejected($query)
    {
        return $query->where('application_status', 'Rejected');
    }

    /**
     * Scope for overdue reviews (pending > 14 days)
     */
    public function scopeOverdue($query)
    {
        return $query->where('review_priority', 'Overdue');
    }

    /**
     * Scope for reviews needing attention (pending > 7 days)
     */
    public function scopeNeedsAttention($query)
    {
        return $query->where('review_priority', 'Needs Attention');
    }

    /**
     * Scope for eligible recipients (can receive allocations)
     */
    public function scopeEligible($query)
    {
        return $query->where('is_eligible_for_allocation', true);
    }

    /**
     * Scope for recipients by Public_ID
     */
    public function scopeForPublicProfile($query, int $publicId)
    {
        return $query->where('Public_ID', $publicId);
    }

    /**
     * Scope for recipients by User_ID
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('applicant_user_id', $userId);
    }

    /**
     * Get stats for dashboard
     */
    public static function getStats(): array
    {
        return [
            'total' => self::count(),
            'pending' => self::pending()->count(),
            'approved' => self::approved()->count(),
            'rejected' => self::rejected()->count(),
            'overdue' => self::overdue()->count(),
            'needs_attention' => self::needsAttention()->count(),
        ];
    }
}
