<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * IZZHILMY Database (PostgreSQL) - Centralized Infrastructure + Authentication
     *
     * Creates:
     * - TRIGGER: trg_user_activity_log - Logs user creation and updates
     * - PROCEDURE: sp_get_user_role_stats - Returns user statistics by role
     * - VIEW: vw_user_roles_summary - User summary with role information
     */
    protected $connection = 'izzhilmy';

    public function up(): void
    {
        // =====================================================================
        // 1. CREATE AUDIT LOG TABLE (required for trigger)
        // =====================================================================
        DB::connection($this->connection)->statement('
            CREATE TABLE IF NOT EXISTS user_activity_log (
                log_id SERIAL PRIMARY KEY,
                user_id BIGINT NOT NULL,
                action VARCHAR(50) NOT NULL,
                old_email VARCHAR(255),
                new_email VARCHAR(255),
                old_name VARCHAR(255),
                new_name VARCHAR(255),
                changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                ip_address VARCHAR(45)
            )
        ');

        // =====================================================================
        // 2. CREATE TRIGGER FUNCTION (PostgreSQL uses functions for triggers)
        // =====================================================================
        DB::connection($this->connection)->statement("
            CREATE OR REPLACE FUNCTION fn_log_user_activity()
            RETURNS TRIGGER AS \$\$
            BEGIN
                IF TG_OP = 'INSERT' THEN
                    INSERT INTO user_activity_log (user_id, action, new_email, new_name, changed_at)
                    VALUES (NEW.id, 'INSERT', NEW.email, NEW.name, CURRENT_TIMESTAMP);
                    RETURN NEW;
                ELSIF TG_OP = 'UPDATE' THEN
                    -- Only log if email or name changed
                    IF OLD.email IS DISTINCT FROM NEW.email OR OLD.name IS DISTINCT FROM NEW.name THEN
                        INSERT INTO user_activity_log (user_id, action, old_email, new_email, old_name, new_name, changed_at)
                        VALUES (NEW.id, 'UPDATE', OLD.email, NEW.email, OLD.name, NEW.name, CURRENT_TIMESTAMP);
                    END IF;
                    RETURN NEW;
                END IF;
                RETURN NULL;
            END;
            \$\$ LANGUAGE plpgsql
        ");

        // =====================================================================
        // 3. CREATE TRIGGER
        // =====================================================================
        DB::connection($this->connection)->statement('
            DROP TRIGGER IF EXISTS trg_user_activity_log ON users
        ');

        DB::connection($this->connection)->statement('
            CREATE TRIGGER trg_user_activity_log
            AFTER INSERT OR UPDATE ON users
            FOR EACH ROW
            EXECUTE FUNCTION fn_log_user_activity()
        ');

        // =====================================================================
        // 4. CREATE RESULT TABLE FOR PROCEDURE
        // =====================================================================
        DB::connection($this->connection)->statement('
            CREATE TABLE IF NOT EXISTS user_role_stats_result (
                result_id SERIAL PRIMARY KEY,
                session_id VARCHAR(100),
                role_name VARCHAR(255),
                user_count BIGINT,
                latest_user_created TIMESTAMP,
                oldest_user_created TIMESTAMP,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ');

        DB::connection($this->connection)->statement('
            CREATE INDEX IF NOT EXISTS idx_user_role_stats_session ON user_role_stats_result(session_id)
        ');

        // =====================================================================
        // 5. CREATE STORED PROCEDURE
        // =====================================================================
        DB::connection($this->connection)->statement("
            CREATE OR REPLACE PROCEDURE sp_get_user_role_stats(
                IN p_role_name VARCHAR DEFAULT NULL,
                IN p_session_id VARCHAR DEFAULT NULL
            )
            LANGUAGE plpgsql
            AS \$\$
            DECLARE
                v_session_id VARCHAR(100);
            BEGIN
                -- Generate session ID if not provided
                v_session_id := COALESCE(p_session_id, 'sess_' || gen_random_uuid()::VARCHAR);

                -- Clear previous results for this session
                DELETE FROM user_role_stats_result WHERE session_id = v_session_id;

                -- Insert new results
                INSERT INTO user_role_stats_result (session_id, role_name, user_count, latest_user_created, oldest_user_created)
                SELECT
                    v_session_id,
                    r.name::VARCHAR AS role_name,
                    COUNT(DISTINCT mhr.model_id)::BIGINT AS user_count,
                    MAX(u.created_at)::TIMESTAMP AS latest_user_created,
                    MIN(u.created_at)::TIMESTAMP AS oldest_user_created
                FROM roles r
                LEFT JOIN model_has_roles mhr ON r.id = mhr.role_id
                    AND mhr.model_type = 'App\\Models\\User'
                LEFT JOIN users u ON mhr.model_id = u.id
                WHERE (p_role_name IS NULL OR r.name = p_role_name)
                GROUP BY r.name
                ORDER BY COUNT(DISTINCT mhr.model_id) DESC;

                -- Clean up old session data (older than 1 hour)
                DELETE FROM user_role_stats_result WHERE created_at < CURRENT_TIMESTAMP - INTERVAL '1 hour';
            END;
            \$\$
        ");

        // =====================================================================
        // 6. CREATE VIEW
        // =====================================================================
        DB::connection($this->connection)->statement("
            CREATE OR REPLACE VIEW vw_user_roles_summary AS
            SELECT
                u.id AS user_id,
                u.name AS user_name,
                u.email AS user_email,
                u.created_at AS registered_at,
                COALESCE(
                    STRING_AGG(r.name, ', ' ORDER BY r.name),
                    'No Role'
                ) AS user_roles,
                COUNT(r.id) AS role_count,
                CASE
                    WHEN u.created_at >= CURRENT_DATE - INTERVAL '30 days' THEN 'New'
                    WHEN u.created_at >= CURRENT_DATE - INTERVAL '90 days' THEN 'Recent'
                    ELSE 'Established'
                END AS user_status
            FROM users u
            LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id
                AND mhr.model_type = 'App\\Models\\User'
            LEFT JOIN roles r ON mhr.role_id = r.id
            GROUP BY u.id, u.name, u.email, u.created_at
            ORDER BY u.created_at DESC
        ");
    }

    public function down(): void
    {
        // Drop in reverse order of dependencies
        DB::connection($this->connection)->statement('DROP VIEW IF EXISTS vw_user_roles_summary');
        DB::connection($this->connection)->statement('DROP PROCEDURE IF EXISTS sp_get_user_role_stats(VARCHAR, VARCHAR)');
        DB::connection($this->connection)->statement('DROP INDEX IF EXISTS idx_user_role_stats_session');
        DB::connection($this->connection)->statement('DROP TABLE IF EXISTS user_role_stats_result');
        DB::connection($this->connection)->statement('DROP TRIGGER IF EXISTS trg_user_activity_log ON users');
        DB::connection($this->connection)->statement('DROP FUNCTION IF EXISTS fn_log_user_activity()');
        DB::connection($this->connection)->statement('DROP TABLE IF EXISTS user_activity_log');
    }
};
