<?php
/**
 * Leadbusiness - E-Mail Integration Sync Service
 * 
 * Synchronisiert Leads zum E-Mail-Tool des Kunden.
 * Wird aufgerufen wenn ein neuer Lead sich anmeldet.
 * 
 * WICHTIG: Marketing-Mails werden weiterhin über Mailgun versendet!
 * Der Sync ist nur, damit der Kunde die Leads in seinem System hat.
 */

namespace Leadbusiness\Services;

require_once __DIR__ . '/emailtools/EmailToolFactory.php';

use Leadbusiness\Database;
use Leadbusiness\EmailTools\EmailToolFactory;

class EmailIntegrationService
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Lead zu allen aktiven Integrationen des Kunden syncen
     * 
     * @param int $customerId Kunden-ID
     * @param array $leadData Lead-Daten (email, name, referral_code, etc.)
     * @return array Ergebnisse der Syncs
     */
    public function syncLead(int $customerId, array $leadData): array
    {
        $results = [];
        
        // Alle aktiven Integrationen des Kunden laden
        $integrations = $this->db->fetchAll(
            "SELECT * FROM customer_email_integrations 
             WHERE customer_id = ? AND is_active = 1",
            [$customerId]
        );
        
        if (empty($integrations)) {
            return ['message' => 'Keine aktiven Integrationen'];
        }
        
        foreach ($integrations as $integration) {
            $result = $this->syncToIntegration($integration, $leadData);
            $results[$integration['tool_name']] = $result;
            
            // Log erstellen
            $this->logSync($integration['id'], $leadData['id'] ?? null, $result);
            
            // Integration-Status aktualisieren
            $this->updateIntegrationStatus($integration['id'], $result);
        }
        
        return $results;
    }
    
    /**
     * Lead zu einer spezifischen Integration syncen
     */
    private function syncToIntegration(array $integration, array $leadData): array
    {
        try {
            // Credentials zusammenstellen
            $credentials = [
                'api_key' => $this->decrypt($integration['api_key']),
                'api_secret' => $integration['api_secret'] ? $this->decrypt($integration['api_secret']) : null,
                'api_url' => $integration['api_url'] ?? null,
                'list_id' => $integration['list_id'] ?? null
            ];
            
            // Service erstellen
            $service = EmailToolFactory::create($integration['tool_name'], $credentials);
            
            if (!$service) {
                return [
                    'success' => false,
                    'action' => 'error',
                    'message' => 'Service konnte nicht erstellt werden'
                ];
            }
            
            // Custom Fields zusammenstellen
            $customFields = [
                'source' => 'leadbusiness',
                'referral_code' => $leadData['referral_code'] ?? '',
                'campaign' => $leadData['campaign_name'] ?? ''
            ];
            
            // Lead syncen
            $tagId = $integration['default_tag_id'] ?? null;
            
            if ($tagId && method_exists($service, 'subscribeWithTag')) {
                $result = $service->subscribeWithTag(
                    $leadData['email'],
                    $tagId,
                    $leadData['name'] ?? '',
                    $customFields
                );
            } else {
                $result = $service->subscribe(
                    $leadData['email'],
                    $leadData['name'] ?? '',
                    $customFields
                );
                
                // Tag separat hinzufügen wenn vorhanden
                if ($result['success'] && $tagId) {
                    $service->addTag($leadData['email'], $tagId);
                }
            }
            
            $result['action'] = 'subscribe';
            return $result;
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'action' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Sync-Log erstellen
     */
    private function logSync(int $integrationId, ?int $leadId, array $result): void
    {
        $this->db->query(
            "INSERT INTO email_integration_logs 
             (integration_id, lead_id, action, status, error_message, created_at)
             VALUES (?, ?, ?, ?, ?, NOW())",
            [
                $integrationId,
                $leadId,
                $result['action'] ?? 'subscribe',
                $result['success'] ? 'success' : 'error',
                $result['success'] ? null : ($result['message'] ?? 'Unbekannter Fehler')
            ]
        );
    }
    
    /**
     * Integration-Status aktualisieren
     */
    private function updateIntegrationStatus(int $integrationId, array $result): void
    {
        if ($result['success']) {
            $this->db->query(
                "UPDATE customer_email_integrations 
                 SET last_sync_at = NOW(), 
                     last_sync_status = 'success',
                     last_error_message = NULL,
                     total_synced = total_synced + 1
                 WHERE id = ?",
                [$integrationId]
            );
        } else {
            $this->db->query(
                "UPDATE customer_email_integrations 
                 SET last_sync_at = NOW(), 
                     last_sync_status = 'error',
                     last_error_message = ?
                 WHERE id = ?",
                [$result['message'] ?? 'Unbekannter Fehler', $integrationId]
            );
        }
    }
    
    /**
     * Integration für Kunden anlegen
     */
    public function createIntegration(int $customerId, array $data): array
    {
        // Prüfen ob Tool implementiert ist
        if (!EmailToolFactory::isImplemented($data['tool_name'])) {
            return [
                'success' => false,
                'message' => 'Dieses E-Mail-Tool wird noch nicht unterstützt'
            ];
        }
        
        // Verbindung testen
        $credentials = [
            'api_key' => $data['api_key'],
            'api_secret' => $data['api_secret'] ?? null,
            'api_url' => $data['api_url'] ?? null,
            'list_id' => $data['list_id'] ?? null
        ];
        
        $service = EmailToolFactory::create($data['tool_name'], $credentials);
        
        if (!$service) {
            return [
                'success' => false,
                'message' => 'Ungültige Zugangsdaten'
            ];
        }
        
        $testResult = $service->testConnection();
        
        if (!$testResult['success']) {
            return [
                'success' => false,
                'message' => 'Verbindungstest fehlgeschlagen: ' . $testResult['message']
            ];
        }
        
        // Integration speichern
        try {
            $this->db->query(
                "INSERT INTO customer_email_integrations 
                 (customer_id, tool_name, api_key, api_secret, api_url, list_id, list_name, default_tag_id, default_tag_name, is_active, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())
                 ON DUPLICATE KEY UPDATE
                 api_key = VALUES(api_key),
                 api_secret = VALUES(api_secret),
                 api_url = VALUES(api_url),
                 list_id = VALUES(list_id),
                 list_name = VALUES(list_name),
                 default_tag_id = VALUES(default_tag_id),
                 default_tag_name = VALUES(default_tag_name),
                 is_active = 1,
                 updated_at = NOW()",
                [
                    $customerId,
                    $data['tool_name'],
                    $this->encrypt($data['api_key']),
                    $data['api_secret'] ? $this->encrypt($data['api_secret']) : null,
                    $data['api_url'] ?? null,
                    $data['list_id'] ?? null,
                    $data['list_name'] ?? null,
                    $data['tag_id'] ?? null,
                    $data['tag_name'] ?? null
                ]
            );
            
            return [
                'success' => true,
                'message' => 'Integration erfolgreich eingerichtet'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Datenbankfehler: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Integration deaktivieren
     */
    public function deactivateIntegration(int $customerId, string $toolName): bool
    {
        return $this->db->query(
            "UPDATE customer_email_integrations 
             SET is_active = 0, updated_at = NOW()
             WHERE customer_id = ? AND tool_name = ?",
            [$customerId, $toolName]
        ) !== false;
    }
    
    /**
     * Integration löschen
     */
    public function deleteIntegration(int $customerId, string $toolName): bool
    {
        return $this->db->query(
            "DELETE FROM customer_email_integrations 
             WHERE customer_id = ? AND tool_name = ?",
            [$customerId, $toolName]
        ) !== false;
    }
    
    /**
     * Alle Integrationen eines Kunden abrufen
     */
    public function getIntegrations(int $customerId): array
    {
        return $this->db->fetchAll(
            "SELECT id, tool_name, list_id, list_name, default_tag_id, default_tag_name,
                    is_active, last_sync_at, last_sync_status, last_error_message, total_synced, created_at
             FROM customer_email_integrations 
             WHERE customer_id = ?
             ORDER BY created_at DESC",
            [$customerId]
        ) ?? [];
    }
    
    /**
     * Listen für ein Tool abrufen (für Dropdown im Onboarding)
     */
    public function getListsForTool(string $toolName, array $credentials): array
    {
        $service = EmailToolFactory::create($toolName, $credentials);
        
        if (!$service) {
            return [];
        }
        
        return $service->getLists();
    }
    
    /**
     * Tags für ein Tool abrufen (für Dropdown im Onboarding)
     */
    public function getTagsForTool(string $toolName, array $credentials): array
    {
        $service = EmailToolFactory::create($toolName, $credentials);
        
        if (!$service) {
            return [];
        }
        
        return $service->getTags();
    }
    
    /**
     * Einfache Verschlüsselung für API-Keys
     * In Produktion sollte ein richtiges Encryption-System verwendet werden!
     */
    private function encrypt(string $value): string
    {
        $key = $this->getEncryptionKey();
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($value, 'AES-256-CBC', $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }
    
    private function decrypt(string $value): string
    {
        $key = $this->getEncryptionKey();
        $data = base64_decode($value);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
    }
    
    private function getEncryptionKey(): string
    {
        global $settings;
        return $settings['encryption_key'] ?? hash('sha256', 'leadbusiness-default-key-change-in-production');
    }
}
