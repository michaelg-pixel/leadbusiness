<?php
/**
 * Lead Notification Service
 * 
 * Sendet E-Mail-Benachrichtigungen an Leads:
 * - Neue Conversion
 * - Belohnung freigeschaltet
 * - Wöchentliche Zusammenfassung
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
        
        $subject = "🎉 Neue Empfehlung registriert!";
        
        $referredName = $referredLead['name'] ?: 'Jemand';
        $totalConversions = $lead['conversions'] + 1;
        
        $html = $this->renderEmail($lead, '
            <h2 style="color: ' . $lead['primary_color'] . '; margin-bottom: 20px;">
                Herzlichen Glückwunsch, ' . htmlspecialchars($lead['name'] ?: 'Empfehler') . '! 🎉
            </h2>
            
            <p style="font-size: 18px; color: #333;">
                <strong>' . htmlspecialchars($referredName) . '</strong> hat sich gerade über Ihren Empfehlungslink angemeldet!
            </p>
            
            <div style="background: #f8f9fa; border-radius: 12px; padding: 20px; margin: 24px 0; text-align: center;">
                <div style="font-size: 48px; font-weight: bold; color: ' . $lead['primary_color'] . ';">
                    ' . $totalConversions . '
                </div>
                <div style="color: #666; font-size: 14px;">
                    Erfolgreiche Empfehlungen
                </div>
            </div>
            
            <p style="color: #666;">
                Teilen Sie Ihren Link weiter, um noch mehr Belohnungen freizuschalten!
            </p>
            
            <p style="text-align: center; margin: 30px 0;">
                <a href="https://' . $lead['subdomain'] . '.empfohlen.de/lead/dashboard.php" 
                   style="background-color: ' . $lead['primary_color'] . '; color: white; padding: 15px 30px; 
                          text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;">
                    Zum Dashboard
                </a>
            </p>
        ');
        
        $this->send($lead, $subject, $html);
    }
    
    /**
     * Benachrichtigung bei freigeschalteter Belohnung
     */
    public function notifyRewardUnlocked(int $leadId, array $reward): void {
        $lead = $this->getLeadWithCustomer($leadId);
        
        if (!$lead || !$lead['notification_reward_unlocked']) {
            return;
        }
        
        $subject = "🎁 Belohnung freigeschaltet: " . $reward['description'];
        
        $html = $this->renderEmail($lead, '
            <h2 style="color: ' . $lead['primary_color'] . '; margin-bottom: 20px;">
                Fantastisch, ' . htmlspecialchars($lead['name'] ?: 'Empfehler') . '! 🎁
            </h2>
            
            <p style="font-size: 18px; color: #333;">
                Sie haben eine neue Belohnungsstufe erreicht!
            </p>
            
            <div style="background: linear-gradient(135deg, #ffd700 0%, #ffb700 100%); border-radius: 12px; padding: 30px; margin: 24px 0; text-align: center; color: #333;">
                <div style="font-size: 24px; font-weight: bold; margin-bottom: 10px;">
                    Stufe ' . $reward['level'] . ' erreicht
                </div>
                <div style="font-size: 20px;">
                    ' . htmlspecialchars($reward['description']) . '
                </div>
            </div>
            
            <p style="color: #666;">
                Gehen Sie jetzt zu Ihrem Dashboard, um Ihre Belohnung einzulösen.
            </p>
            
            <p style="text-align: center; margin: 30px 0;">
                <a href="https://' . $lead['subdomain'] . '.empfohlen.de/lead/dashboard.php?tab=rewards" 
                   style="background-color: ' . $lead['primary_color'] . '; color: white; padding: 15px 30px; 
                          text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;">
                    Belohnung einlösen
                </a>
            </p>
        ');
        
        $this->send($lead, $subject, $html);
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
        
        $subject = "📊 Ihre Wochen-Übersicht";
        
        $html = $this->renderEmail($lead, '
            <h2 style="color: ' . $lead['primary_color'] . '; margin-bottom: 20px;">
                Hallo ' . htmlspecialchars($lead['name'] ?: 'Empfehler') . '! 👋
            </h2>
            
            <p style="font-size: 16px; color: #333;">
                Hier ist Ihre wöchentliche Übersicht:
            </p>
            
            <div style="display: flex; gap: 20px; margin: 24px 0;">
                <div style="flex: 1; background: #f8f9fa; border-radius: 12px; padding: 20px; text-align: center;">
                    <div style="font-size: 36px; font-weight: bold; color: ' . $lead['primary_color'] . ';">
                        ' . ($weeklyStats['new_conversions'] ?? 0) . '
                    </div>
                    <div style="color: #666; font-size: 12px;">Neue Empfehlungen</div>
                </div>
                <div style="flex: 1; background: #f8f9fa; border-radius: 12px; padding: 20px; text-align: center;">
                    <div style="font-size: 36px; font-weight: bold; color: ' . $lead['primary_color'] . ';">
                        ' . ($weeklyStats['new_clicks'] ?? 0) . '
                    </div>
                    <div style="color: #666; font-size: 12px;">Link-Klicks</div>
                </div>
            </div>
            
            <div style="background: #f8f9fa; border-radius: 12px; padding: 20px; margin: 24px 0;">
                <div style="font-size: 14px; color: #666;">Gesamt-Empfehlungen</div>
                <div style="font-size: 24px; font-weight: bold; color: #333;">
                    ' . $lead['conversions'] . '
                </div>
            </div>
            
            <p style="text-align: center; margin: 30px 0;">
                <a href="https://' . $lead['subdomain'] . '.empfohlen.de/lead/dashboard.php" 
                   style="background-color: ' . $lead['primary_color'] . '; color: white; padding: 15px 30px; 
                          text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;">
                    Zum Dashboard
                </a>
            </p>
        ');
        
        $this->send($lead, $subject, $html);
    }
    
    /**
     * Lead mit Kunden-Daten laden
     */
    private function getLeadWithCustomer(int $leadId): ?array {
        return $this->db->fetch(
            "SELECT l.*, c.company_name, c.subdomain, c.logo_url, c.primary_color
             FROM leads l
             JOIN campaigns ca ON l.campaign_id = ca.id
             JOIN customers c ON ca.customer_id = c.id
             WHERE l.id = ? AND l.status = 'active' AND l.email_unsubscribed = 0",
            [$leadId]
        );
    }
    
    /**
     * E-Mail-Template rendern
     */
    private function renderEmail(array $lead, string $content): string {
        $primaryColor = $lead['primary_color'] ?? '#667eea';
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; margin: 0; padding: 0; background-color: #f4f4f5;">
            <div style="max-width: 600px; margin: 0 auto; padding: 40px 20px;">
                <div style="background: white; border-radius: 16px; padding: 40px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    
                    <!-- Logo -->
                    ' . ($lead['logo_url'] ? '<img src="' . htmlspecialchars($lead['logo_url']) . '" alt="' . htmlspecialchars($lead['company_name']) . '" style="max-height: 50px; margin-bottom: 30px;">' : '<div style="font-size: 24px; font-weight: bold; color: #333; margin-bottom: 30px;">' . htmlspecialchars($lead['company_name']) . '</div>') . '
                    
                    ' . $content . '
                    
                </div>
                
                <!-- Footer -->
                <div style="text-align: center; margin-top: 30px; color: #999; font-size: 12px;">
                    <p>' . htmlspecialchars($lead['company_name']) . ' - Empfehlungsprogramm</p>
                    <p>
                        <a href="https://' . $lead['subdomain'] . '.empfohlen.de/lead/dashboard.php?tab=settings" style="color: #999;">
                            Benachrichtigungseinstellungen
                        </a>
                    </p>
                </div>
            </div>
        </body>
        </html>';
    }
    
    /**
     * E-Mail senden
     */
    private function send(array $lead, string $subject, string $html): void {
        try {
            $this->mailgun->send([
                'to' => $lead['email'],
                'subject' => $subject,
                'html' => $html,
                'lead_id' => $lead['id'],
                'email_type' => 'notification'
            ]);
        } catch (Exception $e) {
            error_log("LeadNotification Error: " . $e->getMessage());
        }
    }
}
