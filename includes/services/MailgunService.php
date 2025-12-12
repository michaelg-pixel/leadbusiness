<?php
/**
 * Leadbusiness - Mailgun Service
 * 
 * E-Mail-Versand über Mailgun API (EU-Region, DSGVO-konform)
 * 
 * Features:
 * - One-Click Unsubscribe (RFC 8058, Gmail/Yahoo 2024)
 * - Template-basierter Versand
 * - E-Mail-Queue für asynchronen Versand
 * - Tracking (Opens, Clicks)
 */

class MailgunService {
    
    private $apiKey;
    private $domain;
    private $baseUrl;
    private $fromEmail;
    private $fromName;
    private $db;
    
    // Base URL für Unsubscribe-Links
    private $unsubscribeBaseUrl = 'https://empfehlungen.cloud/unsubscribe';
    
    public function __construct() {
        $config = require __DIR__ . '/../../config/mailgun.php';
        
        $this->apiKey = $config['api_key'];
        $this->domain = $config['domain'];
        $this->baseUrl = $config['base_url'] ?? 'https://api.eu.mailgun.net/v3';
        $this->fromEmail = $config['from_email'];
        $this->fromName = $config['from_name'];
        $this->db = Database::getInstance();
    }
    
    /**
     * E-Mail senden
     * 
     * @param string $to Empfänger E-Mail
     * @param string $subject Betreff
     * @param string $html HTML-Inhalt
     * @param array $options Zusätzliche Optionen
     * @return array Mailgun Response
     */
    public function send($to, $subject, $html, $options = []) {
        $endpoint = "{$this->baseUrl}/{$this->domain}/messages";
        
        $data = [
            'from' => ($options['from_name'] ?? $this->fromName) . ' <' . ($options['from_email'] ?? $this->fromEmail) . '>',
            'to' => $to,
            'subject' => $subject,
            'html' => $html
        ];
        
        // Plain-Text Version
        if (!empty($options['text'])) {
            $data['text'] = $options['text'];
        } else {
            $data['text'] = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $html));
        }
        
        // Reply-To
        if (!empty($options['reply_to'])) {
            $data['h:Reply-To'] = $options['reply_to'];
        }
        
        // =====================================================
        // ONE-CLICK UNSUBSCRIBE (RFC 8058 + Gmail/Yahoo 2024)
        // =====================================================
        if (!empty($options['lead_id'])) {
            $unsubscribeToken = $this->getOrCreateUnsubscribeToken($options['lead_id']);
            $unsubscribeUrl = $this->unsubscribeBaseUrl . '?token=' . urlencode($unsubscribeToken);
            
            // List-Unsubscribe Header (RFC 2369)
            // Enthält sowohl mailto: als auch https: URL
            $data['h:List-Unsubscribe'] = '<mailto:unsubscribe@empfehlungen.cloud?subject=unsubscribe-' . $options['lead_id'] . '>, <' . $unsubscribeUrl . '>';
            
            // List-Unsubscribe-Post Header (RFC 8058)
            // Erforderlich für One-Click Unsubscribe
            $data['h:List-Unsubscribe-Post'] = 'List-Unsubscribe=One-Click';
        }
        
        // Tags für Tracking
        if (!empty($options['tags'])) {
            $data['o:tag'] = $options['tags'];
        }
        
        // Tracking
        $data['o:tracking'] = 'yes';
        $data['o:tracking-clicks'] = 'yes';
        $data['o:tracking-opens'] = 'yes';
        
        // Custom Variables (für Webhooks)
        if (!empty($options['variables'])) {
            foreach ($options['variables'] as $key => $value) {
                $data['v:' . $key] = $value;
            }
        }
        
        // Lead-ID als Variable für Tracking
        if (!empty($options['lead_id'])) {
            $data['v:lead_id'] = $options['lead_id'];
        }
        if (!empty($options['customer_id'])) {
            $data['v:customer_id'] = $options['customer_id'];
        }
        
        $response = $this->request($endpoint, $data);
        
        return $response;
    }
    
    /**
     * Unsubscribe-Token für Lead erstellen/abrufen
     */
    private function getOrCreateUnsubscribeToken($leadId) {
        $lead = $this->db->fetch(
            "SELECT unsubscribe_token FROM leads WHERE id = ?",
            [$leadId]
        );
        
        if ($lead && !empty($lead['unsubscribe_token'])) {
            return $lead['unsubscribe_token'];
        }
        
        // Neuen Token generieren
        $token = bin2hex(random_bytes(32));
        
        $this->db->update('leads', [
            'unsubscribe_token' => $token
        ], 'id = ?', [$leadId]);
        
        return $token;
    }
    
    /**
     * Template-basierte E-Mail senden
     */
    public function sendTemplate($to, $templateName, $variables = [], $options = []) {
        // Template laden
        $template = $this->loadTemplate($templateName);
        
        if (!$template) {
            throw new Exception("E-Mail-Template '{$templateName}' nicht gefunden.");
        }
        
        // Variablen ersetzen
        $subject = $this->replaceVariables($template['subject'], $variables);
        $html = $this->replaceVariables($template['body_html'], $variables);
        
        return $this->send($to, $subject, $html, $options);
    }
    
    /**
     * E-Mail mit Base-Template senden (für Lead-E-Mails)
     * 
     * Enthält automatisch:
     * - Kunden-Logo
     * - Vollständiges Impressum
     * - Datenschutz-Link
     * - Abmeldelink
     */
    public function sendLeadEmail($leadId, $templateName, $additionalVariables = []) {
        // Lead und Kunde laden
        $lead = $this->db->fetch(
            "SELECT l.*, c.id as customer_id, c.company_name, c.logo_url, c.primary_color,
                    c.email_sender_name, c.contact_name, c.address_street, c.address_zip, 
                    c.address_city, c.phone as company_phone, c.email as company_email,
                    c.tax_id, c.subdomain, c.whitelabel_custom_privacy_url
             FROM leads l
             JOIN customers c ON l.customer_id = c.id
             WHERE l.id = ?",
            [$leadId]
        );
        
        if (!$lead) {
            throw new Exception("Lead nicht gefunden: {$leadId}");
        }
        
        // Bereits abgemeldet?
        if ($lead['email_unsubscribed']) {
            return ['skipped' => true, 'reason' => 'unsubscribed'];
        }
        
        // Unsubscribe-URL generieren
        $unsubscribeToken = $this->getOrCreateUnsubscribeToken($leadId);
        $unsubscribeUrl = $this->unsubscribeBaseUrl . '?token=' . urlencode($unsubscribeToken);
        
        // Datenschutz-URL (Kunden-eigene oder unsere dynamische)
        $privacyUrl = $lead['whitelabel_custom_privacy_url'] 
            ?: "https://{$lead['subdomain']}.empfohlen.de/datenschutz";
        
        // Impressum zusammenbauen (Straße, PLZ, Stadt, optional USt-IdNr.)
        $footerAddress = '';
        if (!empty($lead['address_street'])) {
            $footerAddress = $lead['address_street'];
        }
        if (!empty($lead['address_zip']) && !empty($lead['address_city'])) {
            $footerAddress .= ', ' . $lead['address_zip'] . ' ' . $lead['address_city'];
        }
        if (!empty($lead['company_phone'])) {
            $footerAddress .= ' | Tel: ' . $lead['company_phone'];
        }
        if (!empty($lead['tax_id'])) {
            $footerAddress .= ' | USt-IdNr.: ' . $lead['tax_id'];
        }
        
        // Basis-Variablen
        $baseVariables = [
            'lead_name' => $lead['name'] ?: 'Teilnehmer',
            'lead_email' => $lead['email'],
            'lead_referral_code' => $lead['referral_code'],
            'lead_conversions' => $lead['conversions'],
            'lead_current_level' => $lead['current_reward_level'],
            
            'company_name' => $lead['company_name'],
            'company_logo' => $lead['logo_url'],
            'company_email' => $lead['company_email'],
            'company_phone' => $lead['company_phone'],
            'company_address' => $footerAddress,
            'company_tax_id' => $lead['tax_id'],
            
            'primary_color' => $lead['primary_color'] ?? '#667eea',
            
            'referral_page_url' => "https://{$lead['subdomain']}.empfohlen.de",
            'dashboard_url' => "https://{$lead['subdomain']}.empfohlen.de/lead",
            
            'unsubscribe_url' => $unsubscribeUrl,
            'privacy_url' => $privacyUrl,
            
            'current_year' => date('Y'),
            'current_date' => date('d.m.Y'),
        ];
        
        // Zusätzliche Variablen mergen
        $variables = array_merge($baseVariables, $additionalVariables);
        
        // Template laden (sucht nach slug oder name)
        $template = $this->loadTemplate($templateName);
        if (!$template) {
            throw new Exception("E-Mail-Template '{$templateName}' nicht gefunden.");
        }
        
        // Betreff und Body ersetzen
        $subject = $this->replaceVariables($template['subject'], $variables);
        $bodyContent = $this->replaceVariables($template['body_html'], $variables);
        
        // In Base-Template einbetten
        $html = $this->renderBaseTemplate([
            'subject' => $subject,
            'preheader' => $template['preheader'] ?? '',
            'content' => $bodyContent,
            'company_name' => $lead['company_name'],
            'company_logo' => $lead['logo_url'],
            'primary_color' => $lead['primary_color'] ?? '#667eea',
            'footer_address' => $footerAddress,
            'footer_email' => $lead['company_email'],
            'footer_phone' => $lead['company_phone'],
            'footer_tax_id' => $lead['tax_id'],
            'unsubscribe_url' => $unsubscribeUrl,
            'privacy_url' => $privacyUrl,
        ]);
        
        // Senden
        return $this->send($lead['email'], $subject, $html, [
            'from_name' => $lead['email_sender_name'] ?: $lead['company_name'],
            'reply_to' => $lead['company_email'],
            'lead_id' => $leadId,
            'customer_id' => $lead['customer_id'],
            'tags' => [$templateName, 'customer_' . $lead['customer_id']],
            'variables' => [
                'lead_id' => $leadId,
                'customer_id' => $lead['customer_id'],
                'template' => $templateName
            ]
        ]);
    }
    
    /**
     * Base-Template rendern
     */
    private function renderBaseTemplate($data) {
        // Defaults
        $data['primary_color'] = $data['primary_color'] ?? '#667eea';
        $data['preheader'] = $data['preheader'] ?? '';
        
        ob_start();
        extract($data);
        include __DIR__ . '/../../templates/emails/_base_full.php';
        return ob_get_clean();
    }
    
    /**
     * E-Mail aus Queue senden
     */
    public function processQueue($limit = 50) {
        $emails = $this->db->fetchAll(
            "SELECT * FROM email_queue 
             WHERE status = 'pending' 
             AND (scheduled_at IS NULL OR scheduled_at <= NOW())
             ORDER BY priority DESC, created_at ASC 
             LIMIT ?",
            [$limit]
        );
        
        $sent = 0;
        $failed = 0;
        $skipped = 0;
        
        foreach ($emails as $email) {
            try {
                // Prüfen ob Lead abgemeldet ist
                if (!empty($email['lead_id'])) {
                    $lead = $this->db->fetch(
                        "SELECT email_unsubscribed FROM leads WHERE id = ?",
                        [$email['lead_id']]
                    );
                    
                    if ($lead && $lead['email_unsubscribed']) {
                        $this->db->update('email_queue', [
                            'status' => 'skipped',
                            'error_message' => 'Lead has unsubscribed'
                        ], 'id = ?', [$email['id']]);
                        $skipped++;
                        continue;
                    }
                }
                
                // Template-Variablen
                $variables = json_decode($email['variables'] ?? '{}', true);
                
                $options = [
                    'tags' => [$email['template'], 'customer_' . $email['customer_id']],
                    'lead_id' => $email['lead_id'] ?? null,
                    'customer_id' => $email['customer_id'],
                    'variables' => [
                        'email_id' => $email['id'],
                        'customer_id' => $email['customer_id'],
                        'lead_id' => $email['lead_id'] ?? null
                    ]
                ];
                
                // Von Customer-spezifischem Sender?
                if (!empty($email['customer_id'])) {
                    $customer = $this->db->fetch(
                        "SELECT email_sender_name, company_name, email FROM customers WHERE id = ?",
                        [$email['customer_id']]
                    );
                    if ($customer) {
                        $options['from_name'] = $customer['email_sender_name'] ?: $customer['company_name'];
                        $options['reply_to'] = $customer['email'];
                    }
                }
                
                // Wenn lead_id vorhanden, sendLeadEmail nutzen (mit Impressum im Footer)
                if (!empty($email['lead_id'])) {
                    $result = $this->sendLeadEmail($email['lead_id'], $email['template'], $variables);
                } else {
                    $result = $this->sendTemplate(
                        $email['recipient_email'],
                        $email['template'],
                        $variables,
                        $options
                    );
                }
                
                // Übersprungen?
                if (!empty($result['skipped'])) {
                    $this->db->update('email_queue', [
                        'status' => 'skipped',
                        'error_message' => $result['reason'] ?? 'Skipped'
                    ], 'id = ?', [$email['id']]);
                    $skipped++;
                    continue;
                }
                
                // Status aktualisieren
                $this->db->update('email_queue', [
                    'status' => 'sent',
                    'sent_at' => date('Y-m-d H:i:s'),
                    'mailgun_id' => $result['id'] ?? null
                ], 'id = ?', [$email['id']]);
                
                $sent++;
                
            } catch (Exception $e) {
                $this->db->update('email_queue', [
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'attempts' => ($email['attempts'] ?? 0) + 1
                ], 'id = ?', [$email['id']]);
                
                $failed++;
                error_log("Email send failed: " . $e->getMessage());
            }
        }
        
        return ['sent' => $sent, 'failed' => $failed, 'skipped' => $skipped];
    }
    
    /**
     * E-Mail zur Queue hinzufügen
     */
    public function queue($customerId, $recipientEmail, $recipientName, $template, $variables = [], $priority = 5, $scheduledAt = null, $leadId = null) {
        return $this->db->insert('email_queue', [
            'customer_id' => $customerId,
            'lead_id' => $leadId,
            'recipient_email' => $recipientEmail,
            'recipient_name' => $recipientName,
            'template' => $template,
            'variables' => json_encode($variables),
            'priority' => $priority,
            'scheduled_at' => $scheduledAt,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Template laden (sucht nach slug oder name für Kompatibilität)
     */
    private function loadTemplate($templateIdentifier) {
        // Erst nach slug suchen (bevorzugt)
        $template = $this->db->fetch(
            "SELECT * FROM email_templates WHERE slug = ? AND is_active = 1",
            [$templateIdentifier]
        );
        
        // Falls nicht gefunden, nach name suchen (Fallback)
        if (!$template) {
            $template = $this->db->fetch(
                "SELECT * FROM email_templates WHERE name = ? AND is_active = 1",
                [$templateIdentifier]
            );
        }
        
        return $template;
    }
    
    /**
     * Variablen im Text ersetzen
     */
    private function replaceVariables($text, $variables) {
        foreach ($variables as $key => $value) {
            $text = str_replace('{{' . $key . '}}', $value ?? '', $text);
        }
        
        // Standard-Variablen
        $text = str_replace('{{current_year}}', date('Y'), $text);
        $text = str_replace('{{current_date}}', date('d.m.Y'), $text);
        
        return $text;
    }
    
    /**
     * HTTP Request an Mailgun API
     */
    private function request($endpoint, $data) {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $endpoint,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => 'api:' . $this->apiKey,
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception("Mailgun request failed: {$error}");
        }
        
        $result = json_decode($response, true);
        
        if ($httpCode >= 400) {
            throw new Exception("Mailgun error: " . ($result['message'] ?? $response));
        }
        
        return $result;
    }
    
    /**
     * Webhook Signature prüfen
     */
    public function verifyWebhook($timestamp, $token, $signature) {
        $config = require __DIR__ . '/../../config/mailgun.php';
        $signingKey = $config['webhook_signing_key'] ?? '';
        
        $hmac = hash_hmac('sha256', $timestamp . $token, $signingKey);
        
        return hash_equals($hmac, $signature);
    }
    
    /**
     * E-Mail-Statistiken abrufen
     */
    public function getStats($customerId, $days = 30) {
        $stats = $this->db->fetch(
            "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = 'opened' THEN 1 ELSE 0 END) as opened,
                SUM(CASE WHEN status = 'clicked' THEN 1 ELSE 0 END) as clicked,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN status = 'skipped' THEN 1 ELSE 0 END) as skipped
             FROM email_queue 
             WHERE customer_id = ? 
             AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)",
            [$customerId, $days]
        );
        
        return $stats;
    }
    
    /**
     * Unsubscribe-Status prüfen
     */
    public function isUnsubscribed($leadId) {
        $lead = $this->db->fetch(
            "SELECT email_unsubscribed FROM leads WHERE id = ?",
            [$leadId]
        );
        
        return $lead && $lead['email_unsubscribed'];
    }
    
    /**
     * Lead wieder anmelden (Resubscribe)
     */
    public function resubscribe($leadId) {
        $this->db->update('leads', [
            'email_unsubscribed' => 0,
            'email_unsubscribed_at' => null,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$leadId]);
        
        // Log
        $lead = $this->db->fetch("SELECT customer_id FROM leads WHERE id = ?", [$leadId]);
        if ($lead) {
            $this->db->insert('activity_log', [
                'customer_id' => $lead['customer_id'],
                'lead_id' => $leadId,
                'action' => 'email_resubscribed',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        return true;
    }
}
