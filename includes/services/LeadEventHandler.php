<?php
/**
 * Leadbusiness - Lead Event Handler
 * 
 * Wird nach Lead-Erstellung aufgerufen und führt alle notwendigen
 * Aktionen durch (E-Mail-Tool Sync, etc.)
 */

require_once __DIR__ . '/EmailIntegrationService.php';

use Leadbusiness\Services\EmailIntegrationService;

class LeadEventHandler
{
    private static $instance = null;
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
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
     */
    public function onConversion($leadId, $referrerId, $customerId)
    {
        // Bei Conversion ggf. Tag im E-Mail-Tool setzen
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

function triggerConversion($leadId, $referrerId, $customerId)
{
    LeadEventHandler::getInstance()->onConversion($leadId, $referrerId, $customerId);
}
