<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * HANNAH Database (MySQL) - Finance Domain
     *
     * Creates:
     * - TRIGGER: trg_update_donor_total - Auto-update donor.Total_Donated on completed donations
     * - PROCEDURE: sp_get_donation_stats - Returns donation statistics for a campaign
     * - VIEW: vw_donor_donation_summary - Donor statistics and donation history
     */
    protected $connection = 'hannah';

    public function up(): void
    {
        // =====================================================================
        // 1. CREATE DONATION AUDIT LOG TABLE (required for trigger)
        // =====================================================================
        DB::connection($this->connection)->statement('
            CREATE TABLE IF NOT EXISTS donation_audit_log (
                log_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                donation_id BIGINT NOT NULL,
                donor_id BIGINT NOT NULL,
                campaign_id BIGINT NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                payment_status VARCHAR(50) NOT NULL,
                action VARCHAR(50) NOT NULL,
                previous_donor_total DECIMAL(10,2),
                new_donor_total DECIMAL(10,2),
                logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_donation_id (donation_id),
                INDEX idx_donor_id (donor_id),
                INDEX idx_logged_at (logged_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        // =====================================================================
        // 2. CREATE TRIGGER - Auto-update donor.Total_Donated on INSERT
        // =====================================================================
        DB::connection($this->connection)->unprepared('
            DROP TRIGGER IF EXISTS trg_update_donor_total_insert
        ');

        DB::connection($this->connection)->unprepared("
            CREATE TRIGGER trg_update_donor_total_insert
            AFTER INSERT ON donation
            FOR EACH ROW
            BEGIN
                DECLARE v_old_total DECIMAL(10,2) DEFAULT 0;
                DECLARE v_new_total DECIMAL(10,2) DEFAULT 0;

                -- Only process completed payments
                IF NEW.Payment_Status = 'Completed' THEN
                    -- Get current donor total
                    SELECT COALESCE(Total_Donated, 0) INTO v_old_total
                    FROM donor WHERE Donor_ID = NEW.Donor_ID;

                    -- Calculate new total
                    SET v_new_total = v_old_total + NEW.Amount;

                    -- Update donor total
                    UPDATE donor
                    SET Total_Donated = v_new_total
                    WHERE Donor_ID = NEW.Donor_ID;

                    -- Log the transaction
                    INSERT INTO donation_audit_log (
                        donation_id, donor_id, campaign_id, amount,
                        payment_status, action, previous_donor_total, new_donor_total
                    ) VALUES (
                        NEW.Donation_ID, NEW.Donor_ID, NEW.Campaign_ID, NEW.Amount,
                        NEW.Payment_Status, 'INSERT_COMPLETED', v_old_total, v_new_total
                    );
                END IF;
            END
        ");

        // =====================================================================
        // 3. CREATE TRIGGER - Handle payment status UPDATE
        // =====================================================================
        DB::connection($this->connection)->unprepared('
            DROP TRIGGER IF EXISTS trg_update_donor_total_update
        ');

        DB::connection($this->connection)->unprepared("
            CREATE TRIGGER trg_update_donor_total_update
            AFTER UPDATE ON donation
            FOR EACH ROW
            BEGIN
                DECLARE v_old_total DECIMAL(10,2) DEFAULT 0;
                DECLARE v_new_total DECIMAL(10,2) DEFAULT 0;

                -- Get current donor total
                SELECT COALESCE(Total_Donated, 0) INTO v_old_total
                FROM donor WHERE Donor_ID = NEW.Donor_ID;

                -- Handle status change TO Completed (add to total)
                IF OLD.Payment_Status != 'Completed' AND NEW.Payment_Status = 'Completed' THEN
                    SET v_new_total = v_old_total + NEW.Amount;

                    UPDATE donor
                    SET Total_Donated = v_new_total
                    WHERE Donor_ID = NEW.Donor_ID;

                    INSERT INTO donation_audit_log (
                        donation_id, donor_id, campaign_id, amount,
                        payment_status, action, previous_donor_total, new_donor_total
                    ) VALUES (
                        NEW.Donation_ID, NEW.Donor_ID, NEW.Campaign_ID, NEW.Amount,
                        NEW.Payment_Status, 'STATUS_TO_COMPLETED', v_old_total, v_new_total
                    );

                -- Handle status change FROM Completed (remove from total)
                ELSEIF OLD.Payment_Status = 'Completed' AND NEW.Payment_Status != 'Completed' THEN
                    SET v_new_total = GREATEST(0, v_old_total - OLD.Amount);

                    UPDATE donor
                    SET Total_Donated = v_new_total
                    WHERE Donor_ID = NEW.Donor_ID;

                    INSERT INTO donation_audit_log (
                        donation_id, donor_id, campaign_id, amount,
                        payment_status, action, previous_donor_total, new_donor_total
                    ) VALUES (
                        NEW.Donation_ID, NEW.Donor_ID, NEW.Campaign_ID, OLD.Amount,
                        NEW.Payment_Status, 'STATUS_FROM_COMPLETED', v_old_total, v_new_total
                    );
                END IF;
            END
        ");

        // =====================================================================
        // 4. CREATE STORED PROCEDURE
        // =====================================================================
        DB::connection($this->connection)->unprepared('
            DROP PROCEDURE IF EXISTS sp_get_donation_stats
        ');

        DB::connection($this->connection)->unprepared("
            CREATE PROCEDURE sp_get_donation_stats(
                IN p_campaign_id BIGINT,
                IN p_start_date DATE,
                IN p_end_date DATE
            )
            BEGIN
                SELECT
                    COALESCE(p_campaign_id, 0) AS campaign_id,
                    COUNT(*) AS total_donations,
                    COUNT(DISTINCT Donor_ID) AS unique_donors,
                    SUM(CASE WHEN Payment_Status = 'Completed' THEN Amount ELSE 0 END) AS total_completed_amount,
                    SUM(CASE WHEN Payment_Status = 'Pending' THEN Amount ELSE 0 END) AS total_pending_amount,
                    SUM(CASE WHEN Payment_Status = 'Failed' THEN Amount ELSE 0 END) AS total_failed_amount,
                    AVG(CASE WHEN Payment_Status = 'Completed' THEN Amount ELSE NULL END) AS avg_donation_amount,
                    MAX(CASE WHEN Payment_Status = 'Completed' THEN Amount ELSE NULL END) AS max_donation_amount,
                    MIN(CASE WHEN Payment_Status = 'Completed' THEN Amount ELSE NULL END) AS min_donation_amount,
                    SUM(CASE WHEN Payment_Status = 'Completed' THEN 1 ELSE 0 END) AS completed_count,
                    SUM(CASE WHEN Payment_Status = 'Pending' THEN 1 ELSE 0 END) AS pending_count,
                    SUM(CASE WHEN Payment_Status = 'Failed' THEN 1 ELSE 0 END) AS failed_count
                FROM donation
                WHERE (p_campaign_id IS NULL OR Campaign_ID = p_campaign_id)
                  AND (p_start_date IS NULL OR Donation_Date >= p_start_date)
                  AND (p_end_date IS NULL OR Donation_Date <= p_end_date);
            END
        ");

        // =====================================================================
        // 5. CREATE VIEW
        // =====================================================================
        DB::connection($this->connection)->statement("
            CREATE OR REPLACE VIEW vw_donor_donation_summary AS
            SELECT
                d.Donor_ID,
                d.Full_Name AS donor_name,
                d.Phone_Num AS donor_phone,
                d.User_ID,
                d.Total_Donated AS cached_total_donated,
                COUNT(dn.Donation_ID) AS total_donation_count,
                SUM(CASE WHEN dn.Payment_Status = 'Completed' THEN 1 ELSE 0 END) AS completed_donation_count,
                SUM(CASE WHEN dn.Payment_Status = 'Completed' THEN dn.Amount ELSE 0 END) AS actual_total_donated,
                AVG(CASE WHEN dn.Payment_Status = 'Completed' THEN dn.Amount ELSE NULL END) AS avg_donation_amount,
                MAX(dn.Donation_Date) AS last_donation_date,
                MIN(dn.Donation_Date) AS first_donation_date,
                COUNT(DISTINCT dn.Campaign_ID) AS campaigns_supported,
                CASE
                    WHEN d.Total_Donated >= 10000 THEN 'Platinum'
                    WHEN d.Total_Donated >= 5000 THEN 'Gold'
                    WHEN d.Total_Donated >= 1000 THEN 'Silver'
                    WHEN d.Total_Donated >= 100 THEN 'Bronze'
                    ELSE 'Supporter'
                END AS donor_tier,
                d.created_at AS donor_since,
                d.updated_at AS last_updated
            FROM donor d
            LEFT JOIN donation dn ON d.Donor_ID = dn.Donor_ID
            GROUP BY d.Donor_ID, d.Full_Name, d.Phone_Num, d.User_ID,
                     d.Total_Donated, d.created_at, d.updated_at
            ORDER BY d.Total_Donated DESC
        ");
    }

    public function down(): void
    {
        // Drop in reverse order of dependencies
        DB::connection($this->connection)->statement('DROP VIEW IF EXISTS vw_donor_donation_summary');
        DB::connection($this->connection)->unprepared('DROP PROCEDURE IF EXISTS sp_get_donation_stats');
        DB::connection($this->connection)->unprepared('DROP TRIGGER IF EXISTS trg_update_donor_total_update');
        DB::connection($this->connection)->unprepared('DROP TRIGGER IF EXISTS trg_update_donor_total_insert');
        DB::connection($this->connection)->statement('DROP TABLE IF EXISTS donation_audit_log');
    }
};
