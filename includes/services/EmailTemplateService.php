<?php
/**
 * Leadbusiness - Email Template Service
 * 
 * Rendert E-Mail-Templates und bereitet sie für den Versand vor.
 * Später kann hier Mailgun angebunden werden.
 */

namespace Leadbusiness\Services;

class EmailTemplateService
{
    private string $templatesPath;
    private array $globalVariables = [];
    
    public function __construct(string $templatesPath = null)
    {
        $this->templatesPath = $templatesPath ?? __DIR__ . '/../../templates/emails/';
    }
    
    /**
     * Setzt globale Variablen die in allen Templates verfügbar sind
     */
    public function setGlobalVariables(array $variables): void
    {
        $this->globalVariables = $variables;
    }
    
    /**
     * Rendert ein E-Mail-Template
     * 
     * @param string $template Template-Name (ohne .php)
     * @param array $variables Template-Variablen
     * @return array ['subject' => ..., 'html' => ..., 'text' => ...]
     */
    public function render(string $template, array $variables = []): array
    {
        $templateFile = $this->templatesPath . $template . '.php';
        
        if (!file_exists($templateFile)) {
            throw new \Exception("E-Mail-Template nicht gefunden: {$template}");
        }
        
        // Variablen zusammenführen
        $allVariables = array_merge($this->globalVariables, $variables);
        
        // Variablen extrahieren
        extract($allVariables);
        
        // Template rendern
        ob_start();
        include $templateFile;
        $html = ob_get_clean();
        
        // Betreff extrahieren (wird im Template als $subject gesetzt)
        $subject = $subject ?? 'Nachricht von ' . ($company_name ?? 'Leadbusiness');
        
        return [
            'subject' => $subject,
            'html' => $html,
            'text' => $this->htmlToText($html)
        ];
    }
    
    /**
     * Rendert die Belohnungs-E-Mail für einen bestimmten Belohnungstyp
     */
    public function renderRewardNotification(array $reward, array $lead, array $customer): array
    {
        return $this->render('reward_notification', [
            'lead_name' => $lead['name'] ?? 'Empfehler',
            'lead_email' => $lead['email'] ?? '',
            'company_name' => $customer['company_name'] ?? '',
            'company_logo' => $customer['logo_url'] ?? null,
            'primary_color' => $customer['primary_color'] ?? '#667eea',
            'reward' => $reward,
            'conversions_count' => $lead['conversions'] ?? 0,
            'footer_address' => $this->formatAddress($customer),
            'unsubscribe_url' => $this->getUnsubscribeUrl($lead['id'] ?? 0)
        ]);
    }
    
    /**
     * Rendert die Willkommens-E-Mail für einen neuen Empfehler
     */
    public function renderLeadWelcome(array $lead, array $customer, array $rewards = []): array
    {
        return $this->render('lead_welcome', [
            'lead_name' => $lead['name'] ?? 'Empfehler',
            'lead_email' => $lead['email'] ?? '',
            'referral_link' => $this->getReferralLink($customer['subdomain'] ?? '', $lead['referral_code'] ?? ''),
            'company_name' => $customer['company_name'] ?? '',
            'company_logo' => $customer['logo_url'] ?? null,
            'primary_color' => $customer['primary_color'] ?? '#667eea',
            'rewards' => $rewards,
            'dashboard_url' => $this->getLeadDashboardUrl($customer['subdomain'] ?? '', $lead['id'] ?? 0),
            'share_url' => $this->getReferralLink($customer['subdomain'] ?? '', $lead['referral_code'] ?? ''),
            'footer_address' => $this->formatAddress($customer),
            'unsubscribe_url' => $this->getUnsubscribeUrl($lead['id'] ?? 0)
        ]);
    }
    
    /**
     * Rendert die Willkommens-E-Mail für einen neuen Kunden
     */
    public function renderCustomerWelcome(array $customer): array
    {
        return $this->render('customer_welcome', [
            'contact_name' => $customer['contact_name'] ?? 'Kunde',
            'company_name' => $customer['company_name'] ?? '',
            'subdomain' => $customer['subdomain'] ?? '',
            'referral_page_url' => 'https://' . ($customer['subdomain'] ?? 'demo') . '.empfehlungen.cloud',
            'dashboard_url' => 'https://empfehlungen.cloud/dashboard',
            'plan' => $customer['plan'] ?? 'starter',
            'trial_days' => $this->calculateTrialDays($customer['subscription_ends_at'] ?? null),
            'primary_color' => $customer['primary_color'] ?? '#667eea'
        ]);
    }
    
    /**
     * Rendert eine Erinnerungs-E-Mail
     */
    public function renderLeadReminder(array $lead, array $customer, string $reminderType = 'first', array $nextReward = null): array
    {
        $conversionsNeeded = 0;
        if ($nextReward) {
            $conversionsNeeded = max(0, ($nextReward['conversions_required'] ?? 3) - ($lead['conversions'] ?? 0));
        }
        
        return $this->render('lead_reminder', [
            'lead_name' => $lead['name'] ?? 'Empfehler',
            'company_name' => $customer['company_name'] ?? '',
            'company_logo' => $customer['logo_url'] ?? null,
            'primary_color' => $customer['primary_color'] ?? '#667eea',
            'referral_link' => $this->getReferralLink($customer['subdomain'] ?? '', $lead['referral_code'] ?? ''),
            'current_conversions' => $lead['conversions'] ?? 0,
            'next_reward' => $nextReward,
            'conversions_needed' => $conversionsNeeded,
            'dashboard_url' => $this->getLeadDashboardUrl($customer['subdomain'] ?? '', $lead['id'] ?? 0),
            'days_inactive' => $this->calculateDaysInactive($lead['last_activity_at'] ?? null),
            'reminder_type' => $reminderType,
            'footer_address' => $this->formatAddress($customer),
            'unsubscribe_url' => $this->getUnsubscribeUrl($lead['id'] ?? 0)
        ]);
    }
    
    /**
     * Ersetzt Platzhalter in einem benutzerdefinierten E-Mail-Text
     */
    public function replacePlaceholders(string $text, array $data): string
    {
        $replacements = [
            '{{empfehler_name}}' => $data['lead_name'] ?? '',
            '{{empfehler_email}}' => $data['lead_email'] ?? '',
            '{{firmenname}}' => $data['company_name'] ?? '',
            '{{stufe}}' => $data['reward_level'] ?? '',
            '{{empfehlungen}}' => $data['conversions_count'] ?? '',
            '{{rabatt_prozent}}' => ($data['discount_percent'] ?? 0) . '%',
            '{{gutschein_code}}' => $data['coupon_code'] ?? '',
            '{{gutschein_wert}}' => number_format($data['voucher_amount'] ?? 0, 2, ',', '.') . '€',
            '{{bar_betrag}}' => number_format($data['cash_amount'] ?? 0, 2, ',', '.') . '€',
            '{{einloese_link}}' => $data['redeem_url'] ?? '',
            '{{download_link}}' => $data['download_file_url'] ?? '',
            '{{bestell_link}}' => $data['product_url'] ?? '',
            '{{buchungs_link}}' => $data['service_url'] ?? $data['coaching_booking_url'] ?? '',
            '{{videokurs_link}}' => $data['video_url'] ?? '',
            '{{webinar_link}}' => $data['webinar_url'] ?? '',
            '{{exklusiv_link}}' => $data['exclusive_url'] ?? '',
            '{{membership_link}}' => $data['membership_url'] ?? '',
            '{{event_link}}' => $data['event_url'] ?? '',
            '{{affiliate_prozent}}' => ($data['affiliate_percent'] ?? 0) . '%',
            '{{coaching_dauer}}' => ($data['coaching_duration'] ?? 30) . ' Minuten',
            '{{event_name}}' => $data['event_name'] ?? '',
            '{{event_datum}}' => !empty($data['event_date']) 
                ? date('d.m.Y', strtotime($data['event_date'])) : '',
            '{{zugangscode}}' => $data['video_access_code'] ?? '',
            '{{empfehlungslink}}' => $data['referral_link'] ?? '',
            '{{dashboard_link}}' => $data['dashboard_url'] ?? ''
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }
    
    /**
     * Konvertiert HTML zu Plain-Text
     */
    private function htmlToText(string $html): string
    {
        // Entferne Style und Script Tags
        $text = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);
        $text = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
        
        // Ersetze einige Tags durch Textäquivalente
        $text = preg_replace('/<br\s*\/?>/i', "\n", $text);
        $text = preg_replace('/<\/p>/i', "\n\n", $text);
        $text = preg_replace('/<\/h[1-6]>/i', "\n\n", $text);
        $text = preg_replace('/<hr\s*\/?>/i', "\n---\n", $text);
        
        // Links extrahieren
        $text = preg_replace('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>([^<]+)<\/a>/i', '$2 ($1)', $text);
        
        // Alle übrigen Tags entfernen
        $text = strip_tags($text);
        
        // HTML-Entities dekodieren
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        
        // Mehrfache Leerzeilen reduzieren
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        
        // Leerzeichen am Anfang/Ende von Zeilen entfernen
        $text = preg_replace('/^ +| +$/m', '', $text);
        
        return trim($text);
    }
    
    /**
     * Formatiert die Adresse für den Footer
     */
    private function formatAddress(array $customer): string
    {
        $parts = [];
        
        if (!empty($customer['company_name'])) {
            $parts[] = $customer['company_name'];
        }
        if (!empty($customer['address_street'])) {
            $parts[] = $customer['address_street'];
        }
        if (!empty($customer['address_zip']) || !empty($customer['address_city'])) {
            $parts[] = trim(($customer['address_zip'] ?? '') . ' ' . ($customer['address_city'] ?? ''));
        }
        
        return implode(' • ', $parts);
    }
    
    /**
     * Generiert den Empfehlungslink
     */
    private function getReferralLink(string $subdomain, string $referralCode): string
    {
        if (empty($subdomain) || empty($referralCode)) {
            return '';
        }
        return 'https://' . $subdomain . '.empfehlungen.cloud/?ref=' . $referralCode;
    }
    
    /**
     * Generiert die Lead-Dashboard-URL
     */
    private function getLeadDashboardUrl(string $subdomain, int $leadId): string
    {
        if (empty($subdomain)) {
            return '';
        }
        return 'https://' . $subdomain . '.empfehlungen.cloud/dashboard';
    }
    
    /**
     * Generiert die Abmelde-URL
     */
    private function getUnsubscribeUrl(int $leadId): string
    {
        if ($leadId <= 0) {
            return '';
        }
        // Token für sicheres Abmelden generieren
        $token = hash('sha256', $leadId . '-' . date('Y-m'));
        return 'https://empfehlungen.cloud/unsubscribe?id=' . $leadId . '&token=' . $token;
    }
    
    /**
     * Berechnet die verbleibenden Testtage
     */
    private function calculateTrialDays(?string $endsAt): int
    {
        if (empty($endsAt)) {
            return 7;
        }
        
        $endDate = new \DateTime($endsAt);
        $now = new \DateTime();
        $diff = $now->diff($endDate);
        
        return max(0, $diff->days * ($diff->invert ? -1 : 1));
    }
    
    /**
     * Berechnet die Tage seit letzter Aktivität
     */
    private function calculateDaysInactive(?string $lastActivity): int
    {
        if (empty($lastActivity)) {
            return 30;
        }
        
        $lastDate = new \DateTime($lastActivity);
        $now = new \DateTime();
        $diff = $now->diff($lastDate);
        
        return $diff->days;
    }
}
