-- Migration: E-Mail Tool Integrationen
-- Leads parallel zum Kunden-E-Mail-Tool syncen
-- Marketing bleibt bei Leadbusiness/Mailgun (außer Enterprise)

CREATE TABLE IF NOT EXISTS customer_email_integrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    
    -- Tool-Konfiguration
    tool_name ENUM('klicktipp', 'quentn', 'cleverreach', 'activecampaign', 'brevo', 'getresponse', 'mailchimp') NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    
    -- API Credentials (verschlüsselt speichern)
    api_key VARCHAR(500) NOT NULL,
    api_secret VARCHAR(500) NULL,
    api_url VARCHAR(255) NULL,
    
    -- Ziel-Konfiguration (Liste wohin Leads gepusht werden)
    list_id VARCHAR(100) NULL,
    list_name VARCHAR(255) NULL,
    
    -- Tag der bei Anmeldung gesetzt wird (optional)
    default_tag_id VARCHAR(100) NULL,
    default_tag_name VARCHAR(255) NULL,
    
    -- Enterprise-Only: Kunde darf eigenen Autoresponder nutzen
    allow_customer_autoresponder TINYINT(1) DEFAULT 0,
    
    -- Status & Logging
    last_sync_at DATETIME NULL,
    last_sync_status ENUM('success', 'error', 'pending') NULL,
    last_error_message TEXT NULL,
    total_synced INT DEFAULT 0,
    
    -- Timestamps
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Constraints
    UNIQUE KEY unique_customer_tool (customer_id, tool_name),
    INDEX idx_customer (customer_id),
    INDEX idx_active (is_active),
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sync-Log für Debugging
CREATE TABLE IF NOT EXISTS email_integration_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    integration_id INT NOT NULL,
    lead_id INT NULL,
    
    action ENUM('subscribe', 'tag', 'error') NOT NULL,
    status ENUM('success', 'error', 'skipped') NOT NULL,
    error_message TEXT NULL,
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_integration (integration_id),
    INDEX idx_lead (lead_id),
    INDEX idx_created (created_at),
    FOREIGN KEY (integration_id) REFERENCES customer_email_integrations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
