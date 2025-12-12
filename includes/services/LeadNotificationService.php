<?php
/**
 * Lead Notification Service
 * 
 * Sendet E-Mail-Benachrichtigungen an Leads:
 * - Neue Conversion
 * - Belohnung freigeschaltet
 * - Wöchentliche Zusammenfassung
 * 
 * Verwendet MailgunService::sendLeadEmail für konsistentes Branding
 * inkl. Impressum im Footer
 */

class LeadNotificationService {
    
    private $db;
    private $mailgun;
    
    public function __construct() {
        $this->db = Database::getInstance();
        require_once __DIR__ . '/MailgunService.php';
        $this->mailgun = new MailgunService();
    }
    
    /**
     * Benachrichtigung bei neuer Conversion
     */
    public function notifyNewConversion(int $leadId, array $conversion): void {
        $lead = $this->getLeadWithCustomer($leadId);
        
        if (!$lead || !$lead['notification_new_conversion']) {
            return;
        }
        
        // Referred Lead Info
        $referredLead = $this->db->fetch(
            "SELECT name, email, created_at FROM leads WHERE id = ?",
            [$conversion['referred_lead_id']]
        );
        
        $referredName = $referredLead['name'] ?: 'Jemand';
        $totalConversions = $lead['conversions'] + 1;
        
        // E-Mail über Queue mit lead_id (verwendet sendLeadEmail für Impressum)
        $this->mailgun->queue(
            $lead['customer_id'],
            $lead['email'],
            $lead['name'],
            'new_conversion',
            [
                'lead_name' => $lead['name'] ?: 'Empfehler',
                'referred_name' => $referredName,
                'total_conversions' => $totalConversions,
                'dashboard_url' => "https://{$lead['subdomain']}.empfohlen.de/lead/dashboard.php"
            ],
            8, // Hohe Priorität
            null,
            $leadId // lead_id für Impressum im Footer
        );
    }
    
    /**
     * Benachrichtigung bei freigeschalteter Belohnung
     */
    public function notifyRewardUnlocked(int $leadId, array $reward): void {
        $lead = $this->getLeadWithCustomer($leadId);
        
        if (!$lead || !$lead['notification_reward_unlocked']) {
            return;
        }
        
        // E-Mail über Queue mit lead_id (verwendet sendLeadEmail für Impressum)
        $this->mailgun->queue(
            $lead['customer_id'],
            $lead['email'],
            $lead['name'],
            'reward_unlocked',
            [
                'lead_name' => $lead['name'] ?: 'Empfehler',
                'reward_level' => $reward['level'],
                'reward_description' => $reward['description'],
                'dashboard_url' => "https://{$lead['subdomain']}.empfohlen.de/lead/dashboard.php?tab=rewards"
            ],
            9, // Sehr hohe Priorität
            null,
            $leadId // lead_id für Impressum im Footer
        );
    }
    
    /**
     * Wöchentliche Zusammenfassung
     */
    public function sendWeeklySummary(int $leadId): void {
        $lead = $this->getLeadWithCustomer($leadId);
        
        if (!$lead || !$lead['notification_weekly_summary']) {
            return;
        }
        
        // Stats der letzten Woche
        $weekAgo = date('Y-m-d H:i:s', strtotime('-7 days'));
        
        $weeklyStats = $this->db->fetch(
            "SELECT 
                COUNT(CASE WHEN c.created_at >= ? THEN 1 END) as new_conversions,
                (SELECT COUNT(*) FROM clicks WHERE lead_id = l.id AND created_at >= ?) as new_clicks
             FROM leads l
             LEFT JOIN conversions c ON c.lead_id = l.id
             WHERE l.id = ?",
            [$weekAgo, $weekAgo, $leadId]
        );
        
        // E-Mail über Queue mit lead_id (verwendet sendLeadEmail für Impressum)
        $this->mailgun->queue(
            $lead['customer_id'],
            $lead['email'],
            $lead['name'],
            'weekly_summary',
            [
                'lead_name' => $lead['name'] ?: 'Empfehler',
                'new_conversions' => $weeklyStats['new_conversions'] ?? 0,
                'new_clicks' => $weeklyStats['new_clicks'] ?? 0,
                'total_conversions' => $lead['conversions'],
                'dashboard_url' => "https://{$lead['subdomain']}.empfohlen.de/lead/dashboard.php"
            ],
            3, // Niedrige Priorität
            null,
            $leadId // lead_id für Impressum im Footer
        );
    }
    
    /**
     * Lead mit Kunden-Daten laden
     */
    private function getLeadWithCustomer(int $leadId): ?array {
        return $this->db->fetch(
            "SELECT l.*, c.id as customer_id, c.company_name, c.subdomain, c.logo_url, c.primary_color
             FROM leads l
             JOIN campaigns ca ON l.campaign_id = ca.id
             JOIN customers c ON ca.customer_id = c.id
             WHERE l.id = ? AND l.status = 'active' AND l.email_unsubscribed = 0",
            [$leadId]
        );
    }
}
