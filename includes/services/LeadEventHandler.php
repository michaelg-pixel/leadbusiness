<?php
/**
 * Leadbusiness - Lead Event Handler
 * 
 * Wird nach Lead-Erstellung aufgerufen und führt alle notwendigen
 * Aktionen durch (E-Mail-Tool Sync, Benachrichtigungen, etc.)
 */

require_once __DIR__ . '/EmailIntegrationService.php';
require_once __DIR__ . '/LeadNotificationService.php';

use Leadbusiness\Services\EmailIntegrationService;

class LeadEventHandler
{
    private static $instance = null;
    private $notificationService = null;
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function getNotificationService()
    {
        if ($this->notificationService === null) {
            $this->notificationService = new LeadNotificationService();
        }
        return $this->notificationService;
    }
    
    /**
     * Wird aufgerufen wenn ein neuer Lead erstellt wurde
     */
    public function onLeadCreated($leadId, $customerId, $leadData)
    {
        $this->syncToEmailTool($customerId, array_merge($leadData, ['id' => $leadId]));
    }
    
    /**
     * Wird aufgerufen wenn ein Lead seine E-Mail bestaetigt hat
     */
    public function onLeadConfirmed($leadId, $customerId, $leadData)
    {
        // Bei Bestaetigung ggf. Tag im E-Mail-Tool aktualisieren
    }
    
    /**
     * Wird aufgerufen wenn eine Conversion stattfindet
     * 
     * @param int $leadId Der neue Lead (der sich angemeldet hat)
     * @param int $referrerId Der werbende Lead (der die Empfehlung gemacht hat)
     * @param int $customerId Der Kunde
     * @param array $conversionData Zusätzliche Conversion-Daten
     */
    public function onConversion($leadId, $referrerId, $customerId, $conversionData = [])
    {
        // Benachrichtigung an den Werber senden
        if ($referrerId) {
            try {
                $this->getNotificationService()->notifyNewConversion($referrerId, [
                    'referred_lead_id' => $leadId,
                    'customer_id' => $customerId
                ]);
            } catch (Exception $e) {
                error_log("Conversion notification error: " . $e->getMessage());
            }
            
            // Prüfen ob Belohnung freigeschaltet wurde
            $this->checkAndNotifyReward($referrerId);
        }
    }
    
    /**
     * Prüft ob eine neue Belohnung freigeschaltet wurde und sendet Benachrichtigung
     */
    private function checkAndNotifyReward($leadId)
    {
        $db = Database::getInstance();
        
        // Lead-Daten mit Conversions holen
        $lead = $db->fetch(
            "SELECT l.*, ca.id as campaign_id 
             FROM leads l 
             JOIN campaigns ca ON l.campaign_id = ca.id
             WHERE l.id = ?",
            [$leadId]
        );
        
        if (!$lead) return;
        
        // Nächste nicht-freigeschaltete Belohnung finden
        // Verwendet conversions_required (korrekter Spaltenname)
        $nextReward = $db->fetch(
            "SELECT r.*, r.conversions_required as required_conversions 
             FROM rewards r
             LEFT JOIN reward_deliveries rd ON rd.reward_id = r.id AND rd.lead_id = ?
             WHERE r.campaign_id = ? 
               AND r.is_active = 1 
               AND r.conversions_required <= ?
               AND rd.id IS NULL
             ORDER BY r.level ASC
             LIMIT 1",
            [$leadId, $lead['campaign_id'], $lead['conversions']]
        );
        
        if ($nextReward) {
            // Reward Delivery erstellen
            $downloadToken = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
            
            $db->execute(
                "INSERT INTO reward_deliveries 
                 (lead_id, reward_id, status, download_token, download_expires_at, created_at)
                 VALUES (?, ?, 'sent', ?, ?, NOW())",
                [$leadId, $nextReward['id'], $downloadToken, $expiresAt]
            );
            
            // Lead Reward Level aktualisieren
            $db->execute(
                "UPDATE leads SET current_reward_level = ? WHERE id = ?",
                [$nextReward['level'], $leadId]
            );
            
            // Benachrichtigung senden
            try {
                $this->getNotificationService()->notifyRewardUnlocked($leadId, $nextReward);
            } catch (Exception $e) {
                error_log("Reward notification error: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Lead zum E-Mail-Tool des Kunden syncen
     */
    private function syncToEmailTool($customerId, $leadData)
    {
        try {
            $emailService = new EmailIntegrationService();
            $result = $emailService->syncLead($customerId, $leadData);
            
            if (!empty($result)) {
                error_log("Email sync for lead {$leadData['id']}: " . json_encode($result));
            }
            
        } catch (Exception $e) {
            error_log("Email sync error for customer {$customerId}: " . $e->getMessage());
        }
    }
}

/**
 * Globale Funktionen fuer einfachen Aufruf
 */
function triggerLeadCreated($leadId, $customerId, $leadData)
{
    LeadEventHandler::getInstance()->onLeadCreated($leadId, $customerId, $leadData);
}

function triggerLeadConfirmed($leadId, $customerId, $leadData)
{
    LeadEventHandler::getInstance()->onLeadConfirmed($leadId, $customerId, $leadData);
}

function triggerConversion($leadId, $referrerId, $customerId, $conversionData = [])
{
    LeadEventHandler::getInstance()->onConversion($leadId, $referrerId, $customerId, $conversionData);
}
