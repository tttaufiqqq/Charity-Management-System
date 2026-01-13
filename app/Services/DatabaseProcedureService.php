<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Service class for calling database stored procedures
 * across the 5 distributed databases
 */
class DatabaseProcedureService
{
    /**
     * Get user role statistics from izzhilmy database
     * Calls: sp_get_user_role_stats(p_role_name, p_session_id)
     * Uses CALL for PostgreSQL procedure and reads from result table
     */
    public static function getUserRoleStats(?string $roleName = null): array
    {
        $sessionId = 'sess_'.Str::uuid()->toString();

        // Call the procedure
        DB::connection('izzhilmy')
            ->statement('CALL sp_get_user_role_stats(?, ?)', [$roleName, $sessionId]);

        // Read results from the result table
        $results = DB::connection('izzhilmy')
            ->select('SELECT role_name, user_count, latest_user_created, oldest_user_created FROM user_role_stats_result WHERE session_id = ? ORDER BY user_count DESC', [$sessionId]);

        // Clean up session data
        DB::connection('izzhilmy')
            ->delete('DELETE FROM user_role_stats_result WHERE session_id = ?', [$sessionId]);

        return collect($results)->map(function ($row) {
            return [
                'role_name' => $row->role_name,
                'user_count' => (int) $row->user_count,
                'latest_user_created' => $row->latest_user_created,
                'oldest_user_created' => $row->oldest_user_created,
            ];
        })->toArray();
    }

    /**
     * Get donation statistics from hannah database
     * Calls: sp_get_donation_stats(p_campaign_id, p_start_date, p_end_date)
     */
    public static function getDonationStats(?int $campaignId = null, ?string $startDate = null, ?string $endDate = null): ?object
    {
        $results = DB::connection('hannah')
            ->select('CALL sp_get_donation_stats(?, ?, ?)', [$campaignId, $startDate, $endDate]);

        return ! empty($results) ? $results[0] : null;
    }

    /**
     * Update campaign collected amount in izzati database
     * Calls: sp_update_campaign_collected_amount(p_campaign_id, p_amount, p_operation, p_session_id)
     * Operation can be: 'ADD', 'SUBTRACT', or 'SET'
     * Uses CALL for PostgreSQL procedure and reads from result table
     */
    public static function updateCampaignCollectedAmount(int $campaignId, float $amount, string $operation = 'ADD'): ?object
    {
        $sessionId = 'sess_'.Str::uuid()->toString();

        // Call the procedure
        DB::connection('izzati')
            ->statement('CALL sp_update_campaign_collected_amount(?, ?, ?, ?)', [$campaignId, $amount, $operation, $sessionId]);

        // Read results from the result table
        $results = DB::connection('izzati')
            ->select('SELECT success, message, new_collected_amount, goal_amount, progress_percentage FROM campaign_update_result WHERE session_id = ?', [$sessionId]);

        // Clean up session data
        DB::connection('izzati')
            ->delete('DELETE FROM campaign_update_result WHERE session_id = ?', [$sessionId]);

        return ! empty($results) ? $results[0] : null;
    }

    /**
     * Get volunteer hours statistics from sashvini database
     * Calls: sp_get_volunteer_hours(p_volunteer_id, p_status_filter, p_start_date, p_end_date)
     */
    public static function getVolunteerHours(?int $volunteerId = null, ?string $statusFilter = null, ?string $startDate = null, ?string $endDate = null): array
    {
        $results = DB::connection('sashvini')
            ->select('CALL sp_get_volunteer_hours(?, ?, ?, ?)', [$volunteerId, $statusFilter, $startDate, $endDate]);

        return collect($results)->toArray();
    }

    /**
     * Get recipient summary from adam database
     * Calls: sp_get_recipient_summary(p_recipient_id, p_status_filter, p_start_date, p_end_date)
     */
    public static function getRecipientSummary(?int $recipientId = null, ?string $statusFilter = null, ?string $startDate = null, ?string $endDate = null): array
    {
        $results = DB::connection('adam')
            ->select('CALL sp_get_recipient_summary(?, ?, ?, ?)', [$recipientId, $statusFilter, $startDate, $endDate]);

        return collect($results)->toArray();
    }

    /**
     * Get comprehensive donation stats for a campaign
     * Uses the procedure for aggregated stats
     */
    public static function getCampaignDonationReport(int $campaignId): array
    {
        $stats = self::getDonationStats($campaignId);

        return [
            'campaign_id' => $campaignId,
            'total_donations' => $stats->total_donations ?? 0,
            'unique_donors' => $stats->unique_donors ?? 0,
            'total_completed' => $stats->total_completed_amount ?? 0,
            'total_pending' => $stats->total_pending_amount ?? 0,
            'total_failed' => $stats->total_failed_amount ?? 0,
            'average_donation' => $stats->avg_donation_amount ?? 0,
            'max_donation' => $stats->max_donation_amount ?? 0,
            'min_donation' => $stats->min_donation_amount ?? 0,
            'completed_count' => $stats->completed_count ?? 0,
            'pending_count' => $stats->pending_count ?? 0,
            'failed_count' => $stats->failed_count ?? 0,
        ];
    }

    /**
     * Get volunteer summary with tier information
     */
    public static function getVolunteerSummary(int $volunteerId): ?array
    {
        $results = self::getVolunteerHours($volunteerId);

        if (empty($results)) {
            return null;
        }

        $volunteer = $results[0];

        return [
            'volunteer_id' => $volunteer->Volunteer_ID ?? $volunteerId,
            'total_events' => $volunteer->total_events_participated ?? 0,
            'total_hours' => $volunteer->total_attended_hours ?? 0,
            'registered_events' => $volunteer->registered_events ?? 0,
            'attended_events' => $volunteer->attended_events ?? 0,
            'cancelled_events' => $volunteer->cancelled_events ?? 0,
            'avg_hours_per_event' => $volunteer->avg_hours_per_event ?? 0,
            'max_hours_single_event' => $volunteer->max_hours_single_event ?? 0,
            'first_participation' => $volunteer->first_participation_date ?? null,
            'last_activity' => $volunteer->last_activity_date ?? null,
            'unique_roles' => $volunteer->unique_roles_taken ?? 0,
        ];
    }
}
