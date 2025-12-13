<?php
/**
 * Leadbusiness - Setup Wizard
 * 
 * Berechnet den Einrichtungs-Fortschritt und zeigt fehlende Schritte
 */

namespace Leadbusiness;

class SetupWizard {
    
    private $customer;
    private $db;
    private $steps = [];
    
    public function __construct($customer) {
        $this->customer = $customer;
        $this->db = Database::getInstance();
        $this->calculateSteps();
    }
    
    /**
     * Berechnet alle Schritte und deren Status
     */
    private function calculateSteps() {
        $c = $this->customer;
        
        // Pflicht-Schritte (müssen für vollständige Einrichtung erledigt werden)
        $this->steps = [
            'account' => [
                'id' => 'account',
                'title' => 'Account erstellt',
                'description' => 'Grunddaten wurden im Onboarding erfasst',
                'icon' => 'fa-user-check',
                'color' => 'green',
                'completed' => true, // Immer erledigt nach Onboarding
                'required' => true,
                'link' => null,
                'priority' => 1
            ],
            
            'logo' => [
                'id' => 'logo',
                'title' => 'Logo hochladen',
                'description' => 'Ihr Logo erscheint auf der Empfehlungsseite und in E-Mails',
                'icon' => 'fa-image',
                'color' => 'blue',
                'completed' => !empty($c['logo_url']),
                'required' => true,
                'link' => '/dashboard/settings.php#logo',
                'priority' => 2
            ],
            
            'website' => [
                'id' => 'website',
                'title' => 'Website hinzufügen',
                'description' => 'Link zu Ihrer Hauptwebsite für mehr Vertrauen',
                'icon' => 'fa-globe',
                'color' => 'cyan',
                'completed' => !empty($c['website']),
                'required' => false,
                'link' => '/dashboard/settings.php#website',
                'priority' => 3
            ],
            
            'phone' => [
                'id' => 'phone',
                'title' => 'Telefonnummer hinzufügen',
                'description' => 'Für Rückfragen und Support-Anfragen',
                'icon' => 'fa-phone',
                'color' => 'emerald',
                'completed' => !empty($c['phone']),
                'required' => false,
                'link' => '/dashboard/settings.php#phone',
                'priority' => 4
            ],
            
            'design' => [
                'id' => 'design',
                'title' => 'Design anpassen',
                'description' => 'Hintergrundbild und Farben für Ihre Empfehlungsseite',
                'icon' => 'fa-palette',
                'color' => 'purple',
                'completed' => $this->isDesignCustomized(),
                'required' => true,
                'link' => '/dashboard/design.php',
                'priority' => 5
            ],
            
            'rewards' => [
                'id' => 'rewards',
                'title' => 'Belohnungen prüfen',
                'description' => 'Stellen Sie sicher, dass Ihre Belohnungen korrekt sind',
                'icon' => 'fa-gift',
                'color' => 'amber',
                'completed' => $this->hasReviewed('rewards'),
                'required' => true,
                'link' => '/dashboard/rewards.php',
                'priority' => 6
            ],
            
            'email_tool' => [
                'id' => 'email_tool',
                'title' => 'E-Mail-Tool verbinden',
                'description' => 'Automatischer Export Ihrer Empfehler in Ihr Newsletter-Tool',
                'icon' => 'fa-plug',
                'color' => 'orange',
                'completed' => !empty($c['email_tool']),
                'required' => false,
                'link' => '/dashboard/integrations.php',
                'priority' => 7
            ],
            
            'test_link' => [
                'id' => 'test_link',
                'title' => 'Empfehlungsseite testen',
                'description' => 'Prüfen Sie, wie Ihre Seite für Empfehler aussieht',
                'icon' => 'fa-external-link-alt',
                'color' => 'indigo',
                'completed' => $this->hasReviewed('test_link'),
                'required' => true,
                'link' => 'https://' . $c['subdomain'] . '.empfehlungen.cloud',
                'external' => true,
                'priority' => 8
            ],
            
            'share' => [
                'id' => 'share',
                'title' => 'Link teilen',
                'description' => 'Teilen Sie Ihren Empfehlungslink mit Ihren Kunden',
                'icon' => 'fa-share-alt',
                'color' => 'pink',
                'completed' => $this->hasSharedLink(),
                'required' => true,
                'link' => '/dashboard/share.php',
                'priority' => 9
            ]
        ];
        
        // Nach Priorität sortieren
        uasort($this->steps, function($a, $b) {
            return $a['priority'] - $b['priority'];
        });
    }
    
    /**
     * Prüft ob das Design angepasst wurde (Farbe oder Hintergrund geändert)
     */
    private function isDesignCustomized() {
        // Prüfen ob Custom-Background oder nicht-Standard-Farbe
        $hasCustomBackground = !empty($this->customer['custom_background_url']);
        $hasCustomColor = !empty($this->customer['primary_color']) && $this->customer['primary_color'] !== '#667eea';
        $hasSelectedBackground = !empty($this->customer['background_image_id']);
        
        // Als erledigt markieren wenn eins davon zutrifft ODER wenn der Kunde das Review gemacht hat
        return $hasCustomBackground || $hasCustomColor || $hasSelectedBackground || $this->hasReviewed('design');
    }
    
    /**
     * Prüft ob ein Schritt als "gesehen/reviewed" markiert wurde
     */
    private function hasReviewed($step) {
        $reviews = json_decode($this->customer['setup_reviews'] ?? '{}', true);
        return !empty($reviews[$step]);
    }
    
    /**
     * Markiert einen Schritt als reviewed
     */
    public function markAsReviewed($step) {
        $reviews = json_decode($this->customer['setup_reviews'] ?? '{}', true);
        $reviews[$step] = date('Y-m-d H:i:s');
        
        $this->db->execute(
            "UPDATE customers SET setup_reviews = ? WHERE id = ?",
            [json_encode($reviews), $this->customer['id']]
        );
        
        // Step aktualisieren
        if (isset($this->steps[$step])) {
            $this->steps[$step]['completed'] = true;
        }
    }
    
    /**
     * Prüft ob der Kunde seinen Link schon geteilt hat (erste Aktivität)
     */
    private function hasSharedLink() {
        // Prüfen ob es Klicks oder Leads gibt
        $hasActivity = $this->db->fetch(
            "SELECT 1 FROM leads WHERE customer_id = ? LIMIT 1",
            [$this->customer['id']]
        );
        
        return !empty($hasActivity) || $this->hasReviewed('share');
    }
    
    /**
     * Gibt alle Schritte zurück
     */
    public function getSteps() {
        return $this->steps;
    }
    
    /**
     * Gibt nur die erforderlichen Schritte zurück
     */
    public function getRequiredSteps() {
        return array_filter($this->steps, function($step) {
            return $step['required'];
        });
    }
    
    /**
     * Gibt nur offene Schritte zurück
     */
    public function getOpenSteps() {
        return array_filter($this->steps, function($step) {
            return !$step['completed'];
        });
    }
    
    /**
     * Gibt nur offene erforderliche Schritte zurück
     */
    public function getOpenRequiredSteps() {
        return array_filter($this->steps, function($step) {
            return $step['required'] && !$step['completed'];
        });
    }
    
    /**
     * Berechnet den Fortschritt in Prozent (nur erforderliche Schritte)
     */
    public function getProgress() {
        $required = $this->getRequiredSteps();
        $completed = array_filter($required, function($step) {
            return $step['completed'];
        });
        
        if (count($required) === 0) return 100;
        
        return round((count($completed) / count($required)) * 100);
    }
    
    /**
     * Berechnet den Gesamtfortschritt (alle Schritte)
     */
    public function getTotalProgress() {
        $completed = array_filter($this->steps, function($step) {
            return $step['completed'];
        });
        
        return round((count($completed) / count($this->steps)) * 100);
    }
    
    /**
     * Gibt den nächsten offenen Schritt zurück
     */
    public function getNextStep() {
        foreach ($this->steps as $step) {
            if (!$step['completed']) {
                return $step;
            }
        }
        return null;
    }
    
    /**
     * Gibt den nächsten offenen erforderlichen Schritt zurück
     */
    public function getNextRequiredStep() {
        foreach ($this->steps as $step) {
            if ($step['required'] && !$step['completed']) {
                return $step;
            }
        }
        return null;
    }
    
    /**
     * Prüft ob alle erforderlichen Schritte erledigt sind
     */
    public function isSetupComplete() {
        return $this->getProgress() === 100;
    }
    
    /**
     * Prüft ob alle Schritte erledigt sind
     */
    public function isFullyComplete() {
        return $this->getTotalProgress() === 100;
    }
    
    /**
     * Prüft ob der Wizard ausgeblendet wurde
     */
    public function isHidden() {
        return !empty($this->customer['setup_wizard_hidden']);
    }
    
    /**
     * Blendet den Wizard aus
     */
    public function hide() {
        $this->db->execute(
            "UPDATE customers SET setup_wizard_hidden = 1 WHERE id = ?",
            [$this->customer['id']]
        );
    }
    
    /**
     * Zeigt den Wizard wieder an
     */
    public function show() {
        $this->db->execute(
            "UPDATE customers SET setup_wizard_hidden = 0 WHERE id = ?",
            [$this->customer['id']]
        );
    }
    
    /**
     * Gibt Statistiken zurück
     */
    public function getStats() {
        return [
            'total' => count($this->steps),
            'completed' => count(array_filter($this->steps, fn($s) => $s['completed'])),
            'open' => count(array_filter($this->steps, fn($s) => !$s['completed'])),
            'required_total' => count($this->getRequiredSteps()),
            'required_completed' => count(array_filter($this->getRequiredSteps(), fn($s) => $s['completed'])),
            'progress' => $this->getProgress(),
            'total_progress' => $this->getTotalProgress()
        ];
    }
}
