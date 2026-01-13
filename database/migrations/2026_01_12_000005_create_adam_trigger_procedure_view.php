<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * ADAM Database (MySQL) - Public/Recipients Domain
     *
     * Creates:
     * - TRIGGER: trg_recipient_approval - Auto-sets Approved_At when status changes to Approved
     * - PROCEDURE: sp_get_recipient_summary - Returns recipient summary with application stats
     * - VIEW: vw_recipient_status_summary - Recipient status and profile summary
     */
    protected $connection = 'adam';

    public function up(): void
    {
        // =====================================================================
        // 1. CREATE RECIPIENT AUDIT LOG TABLE (required for trigger)
        // =====================================================================
        DB::connection($this->connection)->statement('
            CREATE TABLE IF NOT EXISTS recipient_audit_log (
                log_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                recipient_id BIGINT NOT NULL,
                action VARCHAR(50) NOT NULL,
                old_status VARCHAR(50),
                new_status VARCHAR(50),
                approved_at_set TIMESTAMP NULL,
                changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                notes TEXT,
                INDEX idx_recipient_id (recipient_id),
                INDEX idx_changed_at (changed_at),
                INDEX idx_new_status (new_status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        // =====================================================================
        // 2. CREATE TRIGGER - Auto-set Approved_At on status change (BEFORE UPDATE)
        // =====================================================================
        DB::connection($this->connection)->unprepared('
            DROP TRIGGER IF EXISTS trg_recipient_approval_before
        ');

        DB::connection($this->connection)->unprepared("
            CREATE TRIGGER trg_recipient_approval_before
            BEFORE UPDATE ON recipient
            FOR EACH ROW
            BEGIN
                -- Auto-set Approved_At when status changes to Approved
                IF OLD.Status != 'Approved' AND NEW.Status = 'Approved' THEN
                    SET NEW.Approved_At = CURRENT_TIMESTAMP;
                END IF;

                -- Clear Approved_At if status changes from Approved to something else
                IF OLD.Status = 'Approved' AND NEW.Status != 'Approved' THEN
                    SET NEW.Approved_At = NULL;
                END IF;
            END
        ");

        // =====================================================================
        // 3. CREATE TRIGGER - Log status changes (AFTER UPDATE)
        // =====================================================================
        DB::connection($this->connection)->unprepared('
            DROP TRIGGER IF EXISTS trg_recipient_approval_after
        ');

        DB::connection($this->connection)->unprepared("
            CREATE TRIGGER trg_recipient_approval_after
            AFTER UPDATE ON recipient
            FOR EACH ROW
            BEGIN
                -- Log status changes
                IF OLD.Status != NEW.Status THEN
                    INSERT INTO recipient_audit_log (
                        recipient_id, action, old_status, new_status, approved_at_set, notes
                    ) VALUES (
                        NEW.Recipient_ID,
                        CASE
                            WHEN NEW.Status = 'Approved' THEN 'APPROVED'
                            WHEN NEW.Status = 'Rejected' THEN 'REJECTED'
                            ELSE 'STATUS_CHANGED'
                        END,
                        OLD.Status,
                        NEW.Status,
                        CASE WHEN NEW.Status = 'Approved' THEN NEW.Approved_At ELSE NULL END,
                        CONCAT('Status changed from ', OLD.Status, ' to ', NEW.Status)
                    );
                END IF;
            END
        ");

        // =====================================================================
        // 4. CREATE TRIGGER - Log new recipient applications (AFTER INSERT)
        // =====================================================================
        DB::connection($this->connection)->unprepared('
            DROP TRIGGER IF EXISTS trg_recipient_insert_log
        ');

        DB::connection($this->connection)->unprepared("
            CREATE TRIGGER trg_recipient_insert_log
            AFTER INSERT ON recipient
            FOR EACH ROW
            BEGIN
                INSERT INTO recipient_audit_log (
                    recipient_id, action, old_status, new_status, notes
                ) VALUES (
                    NEW.Recipient_ID,
                    'APPLICATION_SUBMITTED',
                    NULL,
                    NEW.Status,
                    CONCAT('New recipient application submitted: ', NEW.Name)
                );
            END
        ");

        // =====================================================================
        // 5. CREATE STORED PROCEDURE
        // =====================================================================
        DB::connection($this->connection)->unprepared('
            DROP PROCEDURE IF EXISTS sp_get_recipient_summary
        ');

        DB::connection($this->connection)->unprepared("
            CREATE PROCEDURE sp_get_recipient_summary(
                IN p_recipient_id BIGINT,
                IN p_status_filter VARCHAR(50) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci,
                IN p_start_date DATE,
                IN p_end_date DATE
            )
            BEGIN
                SELECT
                    r.Recipient_ID,
                    r.Public_ID,
                    r.Name AS recipient_name,
                    r.Address,
                    r.Contact,
                    r.Need_Description,
                    r.Status,
                    r.Approved_At,
                    p.Full_Name AS applicant_name,
                    p.Email AS applicant_email,
                    p.Phone AS applicant_phone,
                    r.created_at AS application_date,
                    DATEDIFF(
                        COALESCE(r.Approved_At, CURRENT_TIMESTAMP),
                        r.created_at
                    ) AS days_in_review,
                    CASE
                        WHEN r.Status = 'Approved' THEN 'Eligible for fund allocation'
                        WHEN r.Status = 'Pending' THEN 'Awaiting admin review'
                        WHEN r.Status = 'Rejected' THEN 'Application rejected'
                        ELSE 'Unknown status'
                    END AS status_description,
                    (
                        SELECT COUNT(*) FROM recipient_audit_log
                        WHERE recipient_id = r.Recipient_ID
                    ) AS audit_log_count
                FROM recipient r
                LEFT JOIN public p ON r.Public_ID = p.Public_ID
                WHERE (p_recipient_id IS NULL OR r.Recipient_ID = p_recipient_id)
                  AND (p_status_filter IS NULL OR r.Status COLLATE utf8mb4_unicode_ci = p_status_filter)
                  AND (p_start_date IS NULL OR r.created_at >= p_start_date)
                  AND (p_end_date IS NULL OR r.created_at <= p_end_date)
                ORDER BY
                    CASE r.Status
                        WHEN 'Pending' THEN 1
                        WHEN 'Approved' THEN 2
                        WHEN 'Rejected' THEN 3
                        ELSE 4
                    END,
                    r.created_at DESC;
            END
        ");

        // =====================================================================
        // 6. CREATE VIEW - Recipient Status Summary
        // =====================================================================
        DB::connection($this->connection)->statement("
            CREATE OR REPLACE VIEW vw_recipient_status_summary AS
            SELECT
                r.Recipient_ID,
                r.Public_ID,
                r.Name AS recipient_name,
                r.Address AS recipient_address,
                r.Contact AS recipient_contact,
                LEFT(r.Need_Description, 200) AS need_summary,
                r.Status AS application_status,
                r.Approved_At,
                p.Full_Name AS applicant_full_name,
                p.Email AS applicant_email,
                p.Phone AS applicant_phone,
                p.Position AS applicant_position,
                p.User_ID AS applicant_user_id,
                r.created_at AS application_submitted_at,
                r.updated_at AS last_updated_at,
                DATEDIFF(CURRENT_TIMESTAMP, r.created_at) AS days_since_application,
                CASE
                    WHEN r.Status = 'Approved' THEN
                        DATEDIFF(r.Approved_At, r.created_at)
                    ELSE NULL
                END AS days_to_approval,
                CASE
                    WHEN r.Status = 'Pending' AND DATEDIFF(CURRENT_TIMESTAMP, r.created_at) > 14 THEN 'Overdue'
                    WHEN r.Status = 'Pending' AND DATEDIFF(CURRENT_TIMESTAMP, r.created_at) > 7 THEN 'Needs Attention'
                    WHEN r.Status = 'Pending' THEN 'In Review'
                    WHEN r.Status = 'Approved' THEN 'Active'
                    WHEN r.Status = 'Rejected' THEN 'Closed'
                    ELSE 'Unknown'
                END AS review_priority,
                CASE
                    WHEN r.Status = 'Approved' THEN TRUE
                    ELSE FALSE
                END AS is_eligible_for_allocation
            FROM recipient r
            LEFT JOIN public p ON r.Public_ID = p.Public_ID
            ORDER BY
                CASE
                    WHEN r.Status = 'Pending' AND DATEDIFF(CURRENT_TIMESTAMP, r.created_at) > 14 THEN 1
                    WHEN r.Status = 'Pending' AND DATEDIFF(CURRENT_TIMESTAMP, r.created_at) > 7 THEN 2
                    WHEN r.Status = 'Pending' THEN 3
                    WHEN r.Status = 'Approved' THEN 4
                    ELSE 5
                END,
                r.created_at ASC
        ");
    }

    public function down(): void
    {
        // Drop in reverse order of dependencies
        DB::connection($this->connection)->statement('DROP VIEW IF EXISTS vw_recipient_status_summary');
        DB::connection($this->connection)->unprepared('DROP PROCEDURE IF EXISTS sp_get_recipient_summary');
        DB::connection($this->connection)->unprepared('DROP TRIGGER IF EXISTS trg_recipient_insert_log');
        DB::connection($this->connection)->unprepared('DROP TRIGGER IF EXISTS trg_recipient_approval_after');
        DB::connection($this->connection)->unprepared('DROP TRIGGER IF EXISTS trg_recipient_approval_before');
        DB::connection($this->connection)->statement('DROP TABLE IF EXISTS recipient_audit_log');
    }
};
