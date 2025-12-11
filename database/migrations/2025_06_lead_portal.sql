-- =====================================================
-- Lead Portal Erweiterungen
-- Migration: 2025_06_lead_portal.sql
-- 
-- Fügt hinzu:
-- - Login-System für Leads (Passwort + Magic Link)
-- - Benachrichtigungs-Einstellungen
-- - Lead Sessions
-- =====================================================

-- 1. Leads-Tabelle erweitern für Login
ALTER TABLE leads 
ADD COLUMN password_hash VARCHAR(255) NULL AFTER email,
ADD COLUMN magic_link_token VARCHAR(100) NULL AFTER confirmation_token,
ADD COLUMN magic_link_expires_at DATETIME NULL AFTER magic_link_token,
ADD COLUMN last_login_at DATETIME NULL AFTER last_activity_at,
ADD COLUMN login_count INT DEFAULT 0 AFTER last_login_at,
ADD COLUMN notification_new_conversion TINYINT(1) DEFAULT 1 AFTER login_count,
ADD COLUMN notification_reward_unlocked TINYINT(1) DEFAULT 1 AFTER notification_new_conversion,
ADD COLUMN notification_weekly_summary TINYINT(1) DEFAULT 0 AFTER notification_reward_unlocked,
ADD COLUMN notification_tips TINYINT(1) DEFAULT 1 AFTER notification_weekly_summary;

-- Index für Magic Link Token
ALTER TABLE leads ADD INDEX idx_magic_link_token (magic_link_token);

-- 2. Lead Sessions Tabelle (für persistente Logins)
CREATE TABLE IF NOT EXISTS lead_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT NOT NULL,
    session_token VARCHAR(100) NOT NULL UNIQUE,
    user_agent TEXT NULL,
    ip_hash VARCHAR(64) NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_used_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_lead_id (lead_id),
    INDEX idx_session_token (session_token),
    INDEX idx_expires_at (expires_at),
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Aktivitäts-Log für Leads erweitern (falls nicht vorhanden)
CREATE TABLE IF NOT EXISTS lead_activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT NOT NULL,
    activity_type ENUM(
        'login', 'logout', 'password_set', 'password_reset',
        'share_click', 'share_copy', 'share_qr',
        'reward_viewed', 'reward_claimed', 'reward_downloaded',
        'profile_updated', 'notifications_updated',
        'conversion_received', 'badge_earned'
    ) NOT NULL,
    details JSON NULL,
    ip_hash VARCHAR(64) NULL,
    user_agent TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_lead_id (lead_id),
    INDEX idx_activity_type (activity_type),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Reward Codes Tabelle für Gutschein-Codes
CREATE TABLE IF NOT EXISTS reward_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reward_delivery_id INT NOT NULL,
    code VARCHAR(50) NOT NULL,
    is_used TINYINT(1) DEFAULT 0,
    used_at DATETIME NULL,
    expires_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_reward_delivery_id (reward_delivery_id),
    INDEX idx_code (code),
    FOREIGN KEY (reward_delivery_id) REFERENCES reward_deliveries(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Rewards-Tabelle erweitern für mehr Belohnungstypen
ALTER TABLE rewards
ADD COLUMN IF NOT EXISTS code_prefix VARCHAR(20) NULL COMMENT 'Prefix für generierte Codes z.B. BONUS-',
ADD COLUMN IF NOT EXISTS code_suffix_length INT DEFAULT 8 COMMENT 'Länge des zufälligen Teils',
ADD COLUMN IF NOT EXISTS download_file_path VARCHAR(500) NULL COMMENT 'Pfad zur Download-Datei',
ADD COLUMN IF NOT EXISTS download_file_name VARCHAR(255) NULL COMMENT 'Anzeigename der Datei',
ADD COLUMN IF NOT EXISTS instructions TEXT NULL COMMENT 'Einlöse-Anleitung für den Lead';
