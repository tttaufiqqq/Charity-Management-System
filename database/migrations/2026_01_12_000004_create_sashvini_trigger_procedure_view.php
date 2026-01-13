<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * SASHVINI Database (MariaDB) - Volunteer Domain
     *
     * Creates:
     * - TRIGGER: trg_log_volunteer_hours - Logs volunteer participation changes
     * - PROCEDURE: sp_get_volunteer_hours - Returns volunteer hour statistics
     * - VIEW: vw_volunteer_hours_summary - Volunteer participation summary
     */
    protected $connection = 'sashvini';

    public function up(): void
    {
        // =====================================================================
        // 1. CREATE VOLUNTEER HOURS AUDIT LOG TABLE (required for trigger)
        // =====================================================================
        DB::connection($this->connection)->statement('
            CREATE TABLE IF NOT EXISTS volunteer_hours_log (
                log_id BIGINT AUTO_INCREMENT PRIMARY KEY,
                volunteer_id BIGINT NOT NULL,
                event_id BIGINT NOT NULL,
                role_id BIGINT,
                action VARCHAR(50) NOT NULL,
                old_status VARCHAR(50),
                new_status VARCHAR(50),
                old_hours INT DEFAULT 0,
                new_hours INT DEFAULT 0,
                hours_change INT DEFAULT 0,
                logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_volunteer_id (volunteer_id),
                INDEX idx_event_id (event_id),
                INDEX idx_logged_at (logged_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        // =====================================================================
        // 2. CREATE TRIGGER - Log hours on INSERT
        // =====================================================================
        DB::connection($this->connection)->unprepared('
            DROP TRIGGER IF EXISTS trg_log_volunteer_hours_insert
        ');

        DB::connection($this->connection)->unprepared("
            CREATE TRIGGER trg_log_volunteer_hours_insert
            AFTER INSERT ON event_participation
            FOR EACH ROW
            BEGIN
                INSERT INTO volunteer_hours_log (
                    volunteer_id, event_id, role_id, action,
                    old_status, new_status, old_hours, new_hours, hours_change
                ) VALUES (
                    NEW.Volunteer_ID, NEW.Event_ID, NEW.Role_ID, 'REGISTERED',
                    NULL, NEW.Status, 0, COALESCE(NEW.Total_Hours, 0), COALESCE(NEW.Total_Hours, 0)
                );
            END
        ");

        // =====================================================================
        // 3. CREATE TRIGGER - Log hours on UPDATE
        // =====================================================================
        DB::connection($this->connection)->unprepared('
            DROP TRIGGER IF EXISTS trg_log_volunteer_hours_update
        ');

        DB::connection($this->connection)->unprepared("
            CREATE TRIGGER trg_log_volunteer_hours_update
            AFTER UPDATE ON event_participation
            FOR EACH ROW
            BEGIN
                DECLARE v_action VARCHAR(50);
                DECLARE v_hours_change INT;

                -- Determine action type based on status change
                IF OLD.Status != NEW.Status THEN
                    SET v_action = CONCAT('STATUS_CHANGE_', UPPER(NEW.Status));
                ELSEIF OLD.Total_Hours != NEW.Total_Hours THEN
                    SET v_action = 'HOURS_UPDATED';
                ELSEIF OLD.Role_ID != NEW.Role_ID OR (OLD.Role_ID IS NULL AND NEW.Role_ID IS NOT NULL) THEN
                    SET v_action = 'ROLE_CHANGED';
                ELSE
                    SET v_action = 'UPDATED';
                END IF;

                -- Calculate hours change
                SET v_hours_change = COALESCE(NEW.Total_Hours, 0) - COALESCE(OLD.Total_Hours, 0);

                -- Log the change
                INSERT INTO volunteer_hours_log (
                    volunteer_id, event_id, role_id, action,
                    old_status, new_status, old_hours, new_hours, hours_change
                ) VALUES (
                    NEW.Volunteer_ID, NEW.Event_ID, NEW.Role_ID, v_action,
                    OLD.Status, NEW.Status, COALESCE(OLD.Total_Hours, 0),
                    COALESCE(NEW.Total_Hours, 0), v_hours_change
                );
            END
        ");

        // =====================================================================
        // 4. CREATE TRIGGER - Log hours on DELETE
        // =====================================================================
        DB::connection($this->connection)->unprepared('
            DROP TRIGGER IF EXISTS trg_log_volunteer_hours_delete
        ');

        DB::connection($this->connection)->unprepared("
            CREATE TRIGGER trg_log_volunteer_hours_delete
            AFTER DELETE ON event_participation
            FOR EACH ROW
            BEGIN
                INSERT INTO volunteer_hours_log (
                    volunteer_id, event_id, role_id, action,
                    old_status, new_status, old_hours, new_hours, hours_change
                ) VALUES (
                    OLD.Volunteer_ID, OLD.Event_ID, OLD.Role_ID, 'CANCELLED',
                    OLD.Status, 'Cancelled', COALESCE(OLD.Total_Hours, 0), 0,
                    -COALESCE(OLD.Total_Hours, 0)
                );
            END
        ");

        // =====================================================================
        // 5. CREATE STORED PROCEDURE
        // =====================================================================
        DB::connection($this->connection)->unprepared('
            DROP PROCEDURE IF EXISTS sp_get_volunteer_hours
        ');

        DB::connection($this->connection)->unprepared("
            CREATE PROCEDURE sp_get_volunteer_hours(
                IN p_volunteer_id BIGINT,
                IN p_status_filter VARCHAR(50) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci,
                IN p_start_date DATE,
                IN p_end_date DATE
            )
            BEGIN
                SELECT
                    v.Volunteer_ID,
                    COUNT(DISTINCT ep.Event_ID) AS total_events_participated,
                    SUM(CASE WHEN ep.Status = 'Attended' THEN ep.Total_Hours ELSE 0 END) AS total_attended_hours,
                    SUM(CASE WHEN ep.Status = 'Registered' THEN 1 ELSE 0 END) AS registered_events,
                    SUM(CASE WHEN ep.Status = 'Attended' THEN 1 ELSE 0 END) AS attended_events,
                    SUM(CASE WHEN ep.Status = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled_events,
                    AVG(CASE WHEN ep.Status = 'Attended' AND ep.Total_Hours > 0 THEN ep.Total_Hours ELSE NULL END) AS avg_hours_per_event,
                    MAX(ep.Total_Hours) AS max_hours_single_event,
                    MIN(ep.created_at) AS first_participation_date,
                    MAX(ep.updated_at) AS last_activity_date,
                    COUNT(DISTINCT ep.Role_ID) AS unique_roles_taken
                FROM volunteer v
                LEFT JOIN event_participation ep ON v.Volunteer_ID = ep.Volunteer_ID
                WHERE (p_volunteer_id IS NULL OR v.Volunteer_ID = p_volunteer_id)
                  AND (p_status_filter IS NULL OR ep.Status COLLATE utf8mb4_unicode_ci = p_status_filter)
                  AND (p_start_date IS NULL OR ep.created_at >= p_start_date)
                  AND (p_end_date IS NULL OR ep.created_at <= p_end_date)
                GROUP BY v.Volunteer_ID;
            END
        ");

        // =====================================================================
        // 6. CREATE VIEW - Volunteer Hours Summary
        // =====================================================================
        DB::connection($this->connection)->statement("
            CREATE OR REPLACE VIEW vw_volunteer_hours_summary AS
            SELECT
                v.Volunteer_ID,
                v.User_ID,
                v.Availability,
                v.City,
                v.State,
                COUNT(DISTINCT ep.Event_ID) AS total_events,
                SUM(CASE WHEN ep.Status = 'Registered' THEN 1 ELSE 0 END) AS registered_count,
                SUM(CASE WHEN ep.Status = 'Attended' THEN 1 ELSE 0 END) AS attended_count,
                SUM(CASE WHEN ep.Status = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled_count,
                SUM(COALESCE(ep.Total_Hours, 0)) AS total_volunteer_hours,
                SUM(CASE WHEN ep.Status = 'Attended' THEN COALESCE(ep.Total_Hours, 0) ELSE 0 END) AS verified_hours,
                AVG(CASE WHEN ep.Status = 'Attended' AND ep.Total_Hours > 0 THEN ep.Total_Hours ELSE NULL END) AS avg_hours_per_event,
                CASE
                    WHEN SUM(CASE WHEN ep.Status = 'Attended' THEN COALESCE(ep.Total_Hours, 0) ELSE 0 END) >= 500 THEN 'Legend'
                    WHEN SUM(CASE WHEN ep.Status = 'Attended' THEN COALESCE(ep.Total_Hours, 0) ELSE 0 END) >= 200 THEN 'Champion'
                    WHEN SUM(CASE WHEN ep.Status = 'Attended' THEN COALESCE(ep.Total_Hours, 0) ELSE 0 END) >= 100 THEN 'Dedicated'
                    WHEN SUM(CASE WHEN ep.Status = 'Attended' THEN COALESCE(ep.Total_Hours, 0) ELSE 0 END) >= 50 THEN 'Active'
                    WHEN SUM(CASE WHEN ep.Status = 'Attended' THEN COALESCE(ep.Total_Hours, 0) ELSE 0 END) >= 10 THEN 'Regular'
                    ELSE 'New'
                END AS volunteer_tier,
                CASE
                    WHEN COUNT(DISTINCT ep.Event_ID) > 0
                    THEN ROUND(
                        SUM(CASE WHEN ep.Status = 'Attended' THEN 1 ELSE 0 END) * 100.0 /
                        COUNT(DISTINCT ep.Event_ID), 2
                    )
                    ELSE 0
                END AS attendance_rate,
                COUNT(DISTINCT ep.Role_ID) AS unique_roles_count,
                GROUP_CONCAT(DISTINCT s.Skill_Name SEPARATOR ', ') AS skills_list,
                MAX(ep.updated_at) AS last_activity,
                v.created_at AS volunteer_since
            FROM volunteer v
            LEFT JOIN event_participation ep ON v.Volunteer_ID = ep.Volunteer_ID
            LEFT JOIN volunteer_skill vs ON v.Volunteer_ID = vs.Volunteer_ID
            LEFT JOIN skill s ON vs.Skill_ID = s.Skill_ID
            GROUP BY v.Volunteer_ID, v.User_ID, v.Availability, v.City, v.State, v.created_at
            ORDER BY verified_hours DESC, attended_count DESC
        ");
    }

    public function down(): void
    {
        // Drop in reverse order of dependencies
        DB::connection($this->connection)->statement('DROP VIEW IF EXISTS vw_volunteer_hours_summary');
        DB::connection($this->connection)->unprepared('DROP PROCEDURE IF EXISTS sp_get_volunteer_hours');
        DB::connection($this->connection)->unprepared('DROP TRIGGER IF EXISTS trg_log_volunteer_hours_delete');
        DB::connection($this->connection)->unprepared('DROP TRIGGER IF EXISTS trg_log_volunteer_hours_update');
        DB::connection($this->connection)->unprepared('DROP TRIGGER IF EXISTS trg_log_volunteer_hours_insert');
        DB::connection($this->connection)->statement('DROP TABLE IF EXISTS volunteer_hours_log');
    }
};
