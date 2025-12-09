<?php
/**
 * Leadbusiness - Mailgun Service
 * 
 * E-Mail-Versand über Mailgun API (EU-Region, DSGVO-konform)
 */

class MailgunService {
    
    private $apiKey;
    private $domain;
    private $baseUrl;
    private $fromEmail;
    private $fromName;
    private $db;
    
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
        
        // Tags für Tracking
        if (!empty($options['tags'])) {
            $data['o:tag'] = $options['tags'];
        }
        
        // Tracking
        $data['o:tracking'] = 'yes';
        $data['o:tracking-clicks'] = 'yes';
        $data['o:tracking-opens'] = 'yes';
        
        // Custom Variables
        if (!empty($options['variables'])) {
            foreach ($options['variables'] as $key => $value) {
                $data['v:' . $key] = $value;
            }
        }
        
        $response = $this->request($endpoint, $data);
        
        return $response;
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
        
        foreach ($emails as $email) {
            try {
                // Template laden und Variablen ersetzen
                $variables = json_decode($email['variables'] ?? '{}', true);
                
                $options = [
                    'tags' => [$email['template'], 'customer_' . $email['customer_id']],
                    'variables' => [
                        'email_id' => $email['id'],
                        'customer_id' => $email['customer_id']
                    ]
                ];
                
                // Von Customer-spezifischem Sender?
                if (!empty($email['customer_id'])) {
                    $customer = $this->db->fetch(
                        "SELECT email_sender_name, company_name FROM customers WHERE id = ?",
                        [$email['customer_id']]
                    );
                    if ($customer) {
                        $options['from_name'] = $customer['email_sender_name'] ?: $customer['company_name'];
                    }
                }
                
                $result = $this->sendTemplate(
                    $email['recipient_email'],
                    $email['template'],
                    $variables,
                    $options
                );
                
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
        
        return ['sent' => $sent, 'failed' => $failed];
    }
    
    /**
     * E-Mail zur Queue hinzufügen
     */
    public function queue($customerId, $recipientEmail, $recipientName, $template, $variables = [], $priority = 5, $scheduledAt = null) {
        return $this->db->insert('email_queue', [
            'customer_id' => $customerId,
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
     * Template laden
     */
    private function loadTemplate($name) {
        return $this->db->fetch(
            "SELECT * FROM email_templates WHERE name = ? AND is_active = 1",
            [$name]
        );
    }
    
    /**
     * Variablen im Text ersetzen
     */
    private function replaceVariables($text, $variables) {
        foreach ($variables as $key => $value) {
            $text = str_replace('{{' . $key . '}}', $value, $text);
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
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed
             FROM email_queue 
             WHERE customer_id = ? 
             AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)",
            [$customerId, $days]
        );
        
        return $stats;
    }
}
