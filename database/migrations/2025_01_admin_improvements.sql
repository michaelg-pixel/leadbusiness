-- =====================================================
-- Admin Verbesserungen - Migration
-- Leadbusiness - Empfehlungsprogramm
-- Datum: 2025-01-XX
-- =====================================================

-- Admin Activity Log (für Impersonation und andere Admin-Aktionen)
CREATE TABLE IF NOT EXISTS admin_activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action VARCHAR(50) NOT NULL COMMENT 'z.B. impersonate, login, change_status',
    target_type VARCHAR(50) NULL COMMENT 'z.B. customer, lead, system',
    target_id INT NULL,
    details JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_admin_id (admin_id),
    INDEX idx_action (action),
    INDEX idx_target (target_type, target_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kundennotizen für Admin (falls noch nicht vorhanden)
CREATE TABLE IF NOT EXISTS customer_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    admin_id INT NOT NULL,
    note TEXT NOT NULL,
    is_pinned BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_customer (customer_id),
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sicherstellen dass last_login_at Spalte existiert
-- (Falls nicht, manuell hinzufügen)
-- ALTER TABLE customers ADD COLUMN IF NOT EXISTS last_login_at DATETIME NULL;
