<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * IZZATI Database (PostgreSQL) - Operations Domain
     *
     * Creates:
     * - TRIGGER: trg_campaign_goal_protection - Prevents goal reduction below collected amount
     * - PROCEDURE: sp_update_campaign_collected_amount - Safely update collected amounts
     * - VIEW: vw_campaign_progress - Campaign progress analytics
     */
    protected $connection = 'izzati';

    public function up(): void
    {
        // =====================================================================
        // 1. CREATE CAMPAIGN AUDIT LOG TABLE (required for trigger)
        // =====================================================================
        DB::connection($this->connection)->statement('
            CREATE TABLE IF NOT EXISTS campaign_audit_log (
                log_id SERIAL PRIMARY KEY,
                campaign_id BIGINT NOT NULL,
                action VARCHAR(50) NOT NULL,
                field_changed VARCHAR(100),
                old_value TEXT,
                new_value TEXT,
                changed_by VARCHAR(255),
                changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                notes TEXT
            )
        ');

        // Create index for better query performance
        DB::connection($this->connection)->statement('
            CREATE INDEX IF NOT EXISTS idx_campaign_audit_campaign_id ON campaign_audit_log(campaign_id)
        ');

        DB::connection($this->connection)->statement('
            CREATE INDEX IF NOT EXISTS idx_campaign_audit_changed_at ON campaign_audit_log(changed_at)
        ');

        // =====================================================================
        // 2. CREATE TRIGGER FUNCTION - Campaign Goal Protection
        // =====================================================================
        DB::connection($this->connection)->statement("
            CREATE OR REPLACE FUNCTION fn_campaign_goal_protection()
            RETURNS TRIGGER AS \$\$
            BEGIN
                -- Prevent Goal_Amount reduction below Collected_Amount
                IF NEW.\"Goal_Amount\" < OLD.\"Collected_Amount\" THEN
                    RAISE EXCEPTION 'Cannot reduce goal amount below collected amount. Current collected: %, Attempted goal: %',
                        OLD.\"Collected_Amount\", NEW.\"Goal_Amount\";
                END IF;

                -- Prevent negative collected amounts
                IF NEW.\"Collected_Amount\" < 0 THEN
                    RAISE EXCEPTION 'Collected amount cannot be negative';
                END IF;

                -- Auto-update status to Completed if goal reached and dates passed
                IF NEW.\"Collected_Amount\" >= NEW.\"Goal_Amount\"
                   AND NEW.\"End_Date\" <= CURRENT_DATE
                   AND NEW.\"Status\" = 'Active' THEN
                    NEW.\"Status\" := 'Completed';
                END IF;

                -- Log significant changes
                IF OLD.\"Goal_Amount\" IS DISTINCT FROM NEW.\"Goal_Amount\" THEN
                    INSERT INTO campaign_audit_log (campaign_id, action, field_changed, old_value, new_value)
                    VALUES (NEW.\"Campaign_ID\", 'UPDATE', 'Goal_Amount',
                            OLD.\"Goal_Amount\"::TEXT, NEW.\"Goal_Amount\"::TEXT);
                END IF;

                IF OLD.\"Collected_Amount\" IS DISTINCT FROM NEW.\"Collected_Amount\" THEN
                    INSERT INTO campaign_audit_log (campaign_id, action, field_changed, old_value, new_value)
                    VALUES (NEW.\"Campaign_ID\", 'UPDATE', 'Collected_Amount',
                            OLD.\"Collected_Amount\"::TEXT, NEW.\"Collected_Amount\"::TEXT);
                END IF;

                IF OLD.\"Status\" IS DISTINCT FROM NEW.\"Status\" THEN
                    INSERT INTO campaign_audit_log (campaign_id, action, field_changed, old_value, new_value)
                    VALUES (NEW.\"Campaign_ID\", 'UPDATE', 'Status', OLD.\"Status\", NEW.\"Status\");
                END IF;

                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql
        ");

        // =====================================================================
        // 3. CREATE TRIGGER
        // =====================================================================
        DB::connection($this->connection)->statement('
            DROP TRIGGER IF EXISTS trg_campaign_goal_protection ON campaign
        ');

        DB::connection($this->connection)->statement('
            CREATE TRIGGER trg_campaign_goal_protection
            BEFORE UPDATE ON campaign
            FOR EACH ROW
            EXECUTE FUNCTION fn_campaign_goal_protection()
        ');

        // =====================================================================
        // 4. CREATE RESULT TABLE FOR PROCEDURE
        // =====================================================================
        DB::connection($this->connection)->statement('
            CREATE TABLE IF NOT EXISTS campaign_update_result (
                result_id SERIAL PRIMARY KEY,
                session_id VARCHAR(100),
                campaign_id BIGINT,
                success BOOLEAN,
                message TEXT,
                new_collected_amount DECIMAL(10,2),
                goal_amount DECIMAL(10,2),
                progress_percentage DECIMAL(5,2),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ');

        DB::connection($this->connection)->statement('
            CREATE INDEX IF NOT EXISTS idx_campaign_update_session ON campaign_update_result(session_id)
        ');

        // =====================================================================
        // 5. CREATE STORED PROCEDURE - Update Campaign Collected Amount
        // =====================================================================
        DB::connection($this->connection)->statement("
            CREATE OR REPLACE PROCEDURE sp_update_campaign_collected_amount(
                IN p_campaign_id BIGINT,
                IN p_amount DECIMAL(10,2),
                IN p_operation VARCHAR(10) DEFAULT 'ADD',
                IN p_session_id VARCHAR DEFAULT NULL
            )
            LANGUAGE plpgsql
            AS \$\$
            DECLARE
                v_current_collected DECIMAL(10,2);
                v_goal DECIMAL(10,2);
                v_new_collected DECIMAL(10,2);
                v_progress DECIMAL(5,2);
                v_session_id VARCHAR(100);
            BEGIN
                -- Generate session ID if not provided
                v_session_id := COALESCE(p_session_id, 'sess_' || gen_random_uuid()::VARCHAR);

                -- Clear previous results for this session
                DELETE FROM campaign_update_result WHERE session_id = v_session_id;

                -- Get current values
                SELECT \"Collected_Amount\", \"Goal_Amount\"
                INTO v_current_collected, v_goal
                FROM campaign
                WHERE \"Campaign_ID\" = p_campaign_id
                FOR UPDATE;

                IF NOT FOUND THEN
                    INSERT INTO campaign_update_result (session_id, campaign_id, success, message, new_collected_amount, goal_amount, progress_percentage)
                    VALUES (v_session_id, p_campaign_id, FALSE, 'Campaign not found', 0, 0, 0);
                    RETURN;
                END IF;

                -- Calculate new collected amount
                IF UPPER(p_operation) = 'ADD' THEN
                    v_new_collected := v_current_collected + p_amount;
                ELSIF UPPER(p_operation) = 'SUBTRACT' THEN
                    v_new_collected := GREATEST(0, v_current_collected - p_amount);
                ELSIF UPPER(p_operation) = 'SET' THEN
                    v_new_collected := p_amount;
                ELSE
                    v_progress := CASE WHEN v_goal > 0 THEN (v_current_collected / v_goal * 100)::DECIMAL(5,2) ELSE 0 END;
                    INSERT INTO campaign_update_result (session_id, campaign_id, success, message, new_collected_amount, goal_amount, progress_percentage)
                    VALUES (v_session_id, p_campaign_id, FALSE, 'Invalid operation. Use ADD, SUBTRACT, or SET', v_current_collected, v_goal, v_progress);
                    RETURN;
                END IF;

                -- Update the campaign
                UPDATE campaign
                SET \"Collected_Amount\" = v_new_collected
                WHERE \"Campaign_ID\" = p_campaign_id;

                -- Calculate progress
                v_progress := CASE
                    WHEN v_goal > 0 THEN (v_new_collected / v_goal * 100)::DECIMAL(5,2)
                    ELSE 0
                END;

                -- Store result
                INSERT INTO campaign_update_result (session_id, campaign_id, success, message, new_collected_amount, goal_amount, progress_percentage)
                VALUES (v_session_id, p_campaign_id, TRUE, 'Successfully updated campaign collected amount', v_new_collected, v_goal, v_progress);

                -- Clean up old session data (older than 1 hour)
                DELETE FROM campaign_update_result WHERE created_at < CURRENT_TIMESTAMP - INTERVAL '1 hour';
            END;
            \$\$
        ");

        // =====================================================================
        // 6. CREATE VIEW - Campaign Progress Analytics
        // =====================================================================
        DB::connection($this->connection)->statement("
            CREATE OR REPLACE VIEW vw_campaign_progress AS
            SELECT
                c.\"Campaign_ID\",
                c.\"Organization_ID\",
                o.\"Register_No\" AS organization_register_no,
                o.\"City\" AS organization_city,
                o.\"State\" AS organization_state,
                c.\"Title\" AS campaign_title,
                c.\"Description\" AS campaign_description,
                c.\"Goal_Amount\",
                c.\"Collected_Amount\",
                CASE
                    WHEN c.\"Goal_Amount\" > 0
                    THEN ROUND((c.\"Collected_Amount\" / c.\"Goal_Amount\" * 100)::NUMERIC, 2)
                    ELSE 0
                END AS progress_percentage,
                c.\"Goal_Amount\" - c.\"Collected_Amount\" AS remaining_amount,
                c.\"Start_Date\",
                c.\"End_Date\",
                CASE
                    WHEN c.\"End_Date\" < CURRENT_DATE THEN 0
                    ELSE (c.\"End_Date\" - CURRENT_DATE)
                END AS days_remaining,
                CASE
                    WHEN c.\"Start_Date\" > CURRENT_DATE THEN 'Not Started'
                    WHEN c.\"End_Date\" < CURRENT_DATE AND c.\"Collected_Amount\" >= c.\"Goal_Amount\" THEN 'Goal Reached'
                    WHEN c.\"End_Date\" < CURRENT_DATE THEN 'Ended'
                    WHEN c.\"Collected_Amount\" >= c.\"Goal_Amount\" THEN 'Goal Reached'
                    WHEN (c.\"Collected_Amount\" / NULLIF(c.\"Goal_Amount\", 0)) >= 0.75 THEN 'Almost There'
                    WHEN (c.\"Collected_Amount\" / NULLIF(c.\"Goal_Amount\", 0)) >= 0.50 THEN 'Halfway'
                    WHEN (c.\"Collected_Amount\" / NULLIF(c.\"Goal_Amount\", 0)) >= 0.25 THEN 'Making Progress'
                    ELSE 'Just Started'
                END AS funding_status,
                c.\"Status\" AS campaign_status,
                CASE
                    WHEN c.\"End_Date\" < CURRENT_DATE THEN TRUE
                    ELSE FALSE
                END AS is_expired,
                CASE
                    WHEN c.\"Start_Date\" <= CURRENT_DATE AND c.\"End_Date\" >= CURRENT_DATE THEN TRUE
                    ELSE FALSE
                END AS is_active_period,
                c.created_at AS campaign_created_at,
                c.updated_at AS campaign_updated_at
            FROM campaign c
            LEFT JOIN organization o ON c.\"Organization_ID\" = o.\"Organization_ID\"
            ORDER BY
                CASE WHEN c.\"Status\" = 'Active' THEN 0 ELSE 1 END,
                c.\"End_Date\" ASC
        ");
    }

    public function down(): void
    {
        // Drop in reverse order of dependencies
        DB::connection($this->connection)->statement('DROP VIEW IF EXISTS vw_campaign_progress');
        DB::connection($this->connection)->statement('DROP PROCEDURE IF EXISTS sp_update_campaign_collected_amount(BIGINT, DECIMAL, VARCHAR, VARCHAR)');
        DB::connection($this->connection)->statement('DROP INDEX IF EXISTS idx_campaign_update_session');
        DB::connection($this->connection)->statement('DROP TABLE IF EXISTS campaign_update_result');
        DB::connection($this->connection)->statement('DROP TRIGGER IF EXISTS trg_campaign_goal_protection ON campaign');
        DB::connection($this->connection)->statement('DROP FUNCTION IF EXISTS fn_campaign_goal_protection()');
        DB::connection($this->connection)->statement('DROP INDEX IF EXISTS idx_campaign_audit_changed_at');
        DB::connection($this->connection)->statement('DROP INDEX IF EXISTS idx_campaign_audit_campaign_id');
        DB::connection($this->connection)->statement('DROP TABLE IF EXISTS campaign_audit_log');
    }
};
