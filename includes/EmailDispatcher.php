<?php
/**
 * Leadbusiness - E-Mail Dispatcher
 * 
 * Entscheidet basierend auf Kundeneinstellungen, ob E-Mails
 * über Leadbusiness (Mailgun) versendet oder per Webhook weitergeleitet werden.
 */

namespace Leadbusiness;

class EmailDispatcher {
    
    private $db;
    private $customer;
    
    // E-Mail Event-Typen
    const EVENT_REFERRER_WELCOME = 'referrer.welcome';
    const EVENT_REFERRER_CONVERSION = 'referrer.conversion';
    const EVENT_REWARD_UNLOCKED = 'reward.unlocked';
    const EVENT_REWARD_CLAIMED = 'reward.claimed';
    const EVENT_WEEKLY_DIGEST = 'weekly.digest';
    
    public function __construct(int $customerId) {
        $this->db = Database::getInstance();
        $this->customer = $this->db->fetch(
            "SELECT * FROM customers WHERE id = ?",
            [$customerId]
        );
    }
    
    /**
     * Prüft ob Leadbusiness E-Mails versenden soll
     */
    public function shouldSendEmail(): bool {
        // Wenn email_self_managed = 1, versendet der Kunde selbst
        return empty($this->customer['email_self_managed']);
    }
    
    /**
     * Prüft ob Webhook-Events gesendet werden sollen
     */
    public function shouldSendWebhook(): bool {
        // Webhook senden wenn:
        // - Kunde ist Enterprise UND
        // - webhook_email_events = 1 ODER email_self_managed = 1
        if ($this->customer['plan'] !== 'enterprise') {
            return false;
        }
        
        return !empty($this->customer['webhook_email_events']) || 
               !empty($this->customer['email_self_managed']);
    }
    
    /**
     * Hauptmethode: Entscheidet und führt aus
     * 
     * @param string $eventType Event-Typ (z.B. EVENT_REFERRER_WELCOME)
     * @param array $recipient Empfänger-Daten (email, name)
     * @param array $data Event-Daten für E-Mail/Webhook
     * @return array Status-Array
     */
    public function dispatch(string $eventType, array $recipient, array $data): array {
        $result = [
            'email_sent' => false,
            'webhook_sent' => false,
            'errors' => []
        ];
        
        // 1. E-Mail versenden (wenn nicht self_managed)
        if ($this->shouldSendEmail()) {
            try {
                $emailResult = $this->sendEmail($eventType, $recipient, $data);
                $result['email_sent'] = $emailResult;
            } catch (\Exception $e) {
                $result['errors'][] = 'Email: ' . $e->getMessage();
            }
        }
        
        // 2. Webhook senden (wenn aktiviert)
        if ($this->shouldSendWebhook()) {
            try {
                $webhookResult = $this->sendWebhookEvent($eventType, $recipient, $data);
                $result['webhook_sent'] = $webhookResult;
            } catch (\Exception $e) {
                $result['errors'][] = 'Webhook: ' . $e->getMessage();
            }
        }
        
        // Logging
        $this->logDispatch($eventType, $recipient, $result);
        
        return $result;
    }
    
    /**
     * E-Mail über Mailgun versenden
     */
    private function sendEmail(string $eventType, array $recipient, array $data): bool {
        // E-Mail-Template basierend auf Event-Typ laden
        $template = $this->getEmailTemplate($eventType);
        
        if (!$template) {
            return false;
        }
        
        // Variablen ersetzen
        $subject = $this->replaceVariables($template['subject'], $data);
        $body = $this->replaceVariables($template['body'], $data);
        
        // Absender-Name vom Kunden oder Standard
        $senderName = $this->customer['email_sender_name'] ?? $this->customer['company_name'];
        
        // Hier würde der tatsächliche Mailgun-Versand stattfinden
        // Für jetzt: Delegieren an bestehende Mailer-Klasse
        
        if (class_exists('Leadbusiness\\Mailer')) {
            $mailer = new Mailer();
            return $mailer->send(
                $recipient['email'],
                $subject,
                $body,
                $senderName
            );
        }
        
        // Fallback: E-Mail in Queue speichern
        $this->db->query(
            "INSERT INTO email_queue (customer_id, recipient_email, recipient_name, subject, body, event_type, status, created_at)
             VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())",
            [
                $this->customer['id'],
                $recipient['email'],
                $recipient['name'] ?? '',
                $subject,
                $body,
                $eventType
            ]
        );
        
        return true;
    }
    
    /**
     * Webhook-Event an Kunden senden
     */
    private function sendWebhookEvent(string $eventType, array $recipient, array $data): bool {
        // Aktive Webhooks für diesen Event-Typ finden
        $webhooks = $this->db->fetchAll(
            "SELECT * FROM webhooks 
             WHERE customer_id = ? AND is_active = 1 
             AND JSON_CONTAINS(events, ?)",
            [$this->customer['id'], json_encode($this->mapEventToWebhookEvent($eventType))]
        );
        
        if (empty($webhooks)) {
            // Kein passender Webhook konfiguriert - trotzdem als "erfolgreich" werten
            return true;
        }
        
        $success = true;
        
        foreach ($webhooks as $webhook) {
            // Payload erstellen
            $payload = [
                'event' => $this->mapEventToWebhookEvent($eventType),
                'timestamp' => date('c'),
                'data' => array_merge($data, [
                    'recipient' => $recipient,
                    'email_event_type' => $eventType
                ])
            ];
            
            // Signatur erstellen
            $signature = hash_hmac('sha256', json_encode($payload), $webhook['secret']);
            
            // Webhook aufrufen
            $ch = curl_init($webhook['url']);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'X-Webhook-Signature: ' . $signature,
                    'X-Webhook-Event: ' . $payload['event'],
                    'X-Email-Event: ' . $eventType,
                    'User-Agent: Leadbusiness-Webhook/1.0'
                ]
            ]);
            
            $response = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            // Webhook-Status aktualisieren
            $this->db->query(
                "UPDATE webhooks SET 
                    last_triggered_at = NOW(),
                    last_status_code = ?,
                    failure_count = IF(? >= 200 AND ? < 300, 0, failure_count + 1)
                 WHERE id = ?",
                [$statusCode, $statusCode, $statusCode, $webhook['id']]
            );
            
            if ($statusCode < 200 || $statusCode >= 300) {
                $success = false;
            }
        }
        
        return $success;
    }
    
    /**
     * E-Mail Event-Typ zu Webhook Event-Typ mappen
     */
    private function mapEventToWebhookEvent(string $emailEvent): string {
        $mapping = [
            self::EVENT_REFERRER_WELCOME => 'referrer.created',
            self::EVENT_REFERRER_CONVERSION => 'conversion.created',
            self::EVENT_REWARD_UNLOCKED => 'reward.unlocked',
            self::EVENT_REWARD_CLAIMED => 'reward.claimed',
            self::EVENT_WEEKLY_DIGEST => 'digest.sent'
        ];
        
        return $mapping[$emailEvent] ?? $emailEvent;
    }
    
    /**
     * E-Mail-Template laden
     */
    private function getEmailTemplate(string $eventType): ?array {
        // Standard-Templates
        $templates = [
            self::EVENT_REFERRER_WELCOME => [
                'subject' => 'Willkommen im Empfehlungsprogramm von {{company_name}}!',
                'body' => 'Hallo {{referrer_name}},

vielen Dank für Ihre Registrierung im Empfehlungsprogramm von {{company_name}}.

Ihr persönlicher Empfehlungslink:
{{referral_link}}

Teilen Sie diesen Link mit Freunden und Bekannten und erhalten Sie tolle Belohnungen!

Viel Erfolg!
{{company_name}}'
            ],
            
            self::EVENT_REFERRER_CONVERSION => [
                'subject' => '🎉 Neue erfolgreiche Empfehlung!',
                'body' => 'Hallo {{referrer_name}},

großartige Neuigkeiten! Ihre Empfehlung war erfolgreich.

{{converted_name}} hat sich über Ihren Link angemeldet/gekauft.

Ihr aktueller Stand:
- Erfolgreiche Empfehlungen: {{total_conversions}}
- Aktuelle Belohnungsstufe: {{current_level}}

Weiter so!
{{company_name}}'
            ],
            
            self::EVENT_REWARD_UNLOCKED => [
                'subject' => '🏆 Neue Belohnung freigeschaltet!',
                'body' => 'Hallo {{referrer_name}},

herzlichen Glückwunsch! Sie haben Stufe {{reward_level}} erreicht!

Ihre Belohnung: {{reward_description}}

{{#if reward_code}}
Ihr Gutschein-Code: {{reward_code}}
{{/if}}

{{#if requires_claim}}
Fordern Sie Ihre Belohnung hier an:
{{claim_link}}
{{/if}}

Vielen Dank für Ihre Empfehlungen!
{{company_name}}'
            ],
            
            self::EVENT_REWARD_CLAIMED => [
                'subject' => '✅ Belohnung wird bearbeitet',
                'body' => 'Hallo {{referrer_name}},

wir haben Ihre Anforderung für die Belohnung "{{reward_description}}" erhalten.

Wir werden uns in Kürze bei Ihnen melden.

{{company_name}}'
            ]
        ];
        
        // Kunden-spezifische Templates prüfen (falls in DB gespeichert)
        $customTemplate = $this->db->fetch(
            "SELECT subject, body FROM email_templates 
             WHERE customer_id = ? AND event_type = ? AND is_active = 1",
            [$this->customer['id'], $eventType]
        );
        
        if ($customTemplate) {
            return $customTemplate;
        }
        
        return $templates[$eventType] ?? null;
    }
    
    /**
     * Variablen in Template ersetzen
     */
    private function replaceVariables(string $template, array $data): string {
        // Standard-Variablen
        $data['company_name'] = $this->customer['company_name'];
        $data['subdomain'] = $this->customer['subdomain'];
        
        // Einfache Variablen ersetzen
        foreach ($data as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $template = str_replace('{{' . $key . '}}', $value, $template);
            }
        }
        
        // Bedingte Blöcke entfernen (vereinfachte Implementierung)
        $template = preg_replace('/\{\{#if\s+\w+\}\}.*?\{\{\/if\}\}/s', '', $template);
        
        return $template;
    }
    
    /**
     * Dispatch loggen
     */
    private function logDispatch(string $eventType, array $recipient, array $result): void {
        try {
            $this->db->query(
                "INSERT INTO email_dispatch_log (customer_id, event_type, recipient_email, email_sent, webhook_sent, errors, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, NOW())",
                [
                    $this->customer['id'],
                    $eventType,
                    $recipient['email'],
                    $result['email_sent'] ? 1 : 0,
                    $result['webhook_sent'] ? 1 : 0,
                    !empty($result['errors']) ? json_encode($result['errors']) : null
                ]
            );
        } catch (\Exception $e) {
            // Logging-Fehler ignorieren
        }
    }
    
    /**
     * Hilfsmethode: Empfehler-Willkommens-E-Mail senden
     */
    public static function sendReferrerWelcome(int $customerId, array $referrer): array {
        $dispatcher = new self($customerId);
        
        $customer = $dispatcher->customer;
        
        return $dispatcher->dispatch(
            self::EVENT_REFERRER_WELCOME,
            [
                'email' => $referrer['email'],
                'name' => $referrer['name'] ?? ''
            ],
            [
                'referrer_name' => $referrer['name'] ?? 'Empfehler',
                'referrer_email' => $referrer['email'],
                'referral_code' => $referrer['referral_code'],
                'referral_link' => "https://{$customer['subdomain']}.empfehlungen.cloud/r/{$referrer['referral_code']}"
            ]
        );
    }
    
    /**
     * Hilfsmethode: Conversion-Benachrichtigung senden
     */
    public static function sendConversionNotification(int $customerId, array $referrer, array $conversion): array {
        $dispatcher = new self($customerId);
        
        return $dispatcher->dispatch(
            self::EVENT_REFERRER_CONVERSION,
            [
                'email' => $referrer['email'],
                'name' => $referrer['name'] ?? ''
            ],
            [
                'referrer_name' => $referrer['name'] ?? 'Empfehler',
                'converted_name' => $conversion['converted_name'] ?? 'Ein Neukunde',
                'total_conversions' => $referrer['conversions'] + 1,
                'current_level' => $referrer['current_reward_level'] ?? 0,
                'order_value' => $conversion['order_value'] ?? 0
            ]
        );
    }
    
    /**
     * Hilfsmethode: Belohnungs-Benachrichtigung senden
     */
    public static function sendRewardUnlocked(int $customerId, array $referrer, array $reward): array {
        $dispatcher = new self($customerId);
        
        $customer = $dispatcher->customer;
        
        return $dispatcher->dispatch(
            self::EVENT_REWARD_UNLOCKED,
            [
                'email' => $referrer['email'],
                'name' => $referrer['name'] ?? ''
            ],
            [
                'referrer_name' => $referrer['name'] ?? 'Empfehler',
                'reward_level' => $reward['level'],
                'reward_description' => $reward['reward_description'],
                'reward_type' => $reward['reward_type'],
                'reward_code' => $reward['reward_code'] ?? null,
                'requires_claim' => $reward['requires_address'] ?? false,
                'claim_link' => "https://{$customer['subdomain']}.empfehlungen.cloud/claim/{$referrer['referral_code']}"
            ]
        );
    }
}
