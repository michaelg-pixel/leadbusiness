<?php
/**
 * Leadbusiness - Badge Service
 * 
 * Verwaltet Achievements/Badges für Empfehler
 */

class BadgeService {
    
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Alle verfügbaren Badges abrufen
     */
    public function getAllBadges() {
        return $this->db->fetchAll(
            "SELECT * FROM badges WHERE is_active = 1 ORDER BY sort_order, id"
        );
    }
    
    /**
     * Badge per ID abrufen
     */
    public function getBadge($id) {
        return $this->db->fetch(
            "SELECT * FROM badges WHERE id = ?",
            [$id]
        );
    }
    
    /**
     * Badges eines Leads abrufen
     */
    public function getLeadBadges($leadId) {
        return $this->db->fetchAll(
            "SELECT b.*, lb.earned_at
             FROM lead_badges lb
             JOIN badges b ON lb.badge_id = b.id
             WHERE lb.lead_id = ?
             ORDER BY lb.earned_at DESC",
            [$leadId]
        );
    }
    
    /**
     * Prüfen ob Lead ein Badge hat
     */
    public function hasBadge($leadId, $badgeId) {
        $result = $this->db->fetch(
            "SELECT id FROM lead_badges WHERE lead_id = ? AND badge_id = ?",
            [$leadId, $badgeId]
        );
        return !empty($result);
    }
    
    /**
     * Badge vergeben
     */
    public function award($leadId, $badgeId) {
        // Bereits vergeben?
        if ($this->hasBadge($leadId, $badgeId)) {
            return false;
        }
        
        $badge = $this->getBadge($badgeId);
        if (!$badge) {
            return false;
        }
        
        // Badge vergeben
        $this->db->insert('lead_badges', [
            'lead_id' => $leadId,
            'badge_id' => $badgeId,
            'earned_at' => date('Y-m-d H:i:s')
        ]);
        
        // Lead-Daten für E-Mail
        $lead = $this->db->fetch(
            "SELECT l.*, c.customer_id 
             FROM leads l
             JOIN campaigns c ON l.campaign_id = c.id
             WHERE l.id = ?",
            [$leadId]
        );
        
        if ($lead) {
            // E-Mail in Queue
            $mailgun = new MailgunService();
            $mailgun->queue(
                $lead['customer_id'],
                $lead['email'],
                $lead['name'],
                'badge_earned',
                [
                    'lead_name' => $lead['name'] ?: 'Empfehler',
                    'badge_name' => $badge['name'],
                    'badge_description' => $badge['description'],
                    'badge_icon' => $badge['icon']
                ],
                5
            );
        }
        
        return true;
    }
    
    /**
     * Alle Badges für einen Lead prüfen und ggf. vergeben
     */
    public function checkAndAward($leadId) {
        $lead = $this->db->fetch(
            "SELECT l.*, 
                    (SELECT COUNT(*) FROM leads WHERE id < l.id AND campaign_id = l.campaign_id) as signup_rank,
                    DATEDIFF(NOW(), l.created_at) as days_active
             FROM leads l 
             WHERE l.id = ?",
            [$leadId]
        );
        
        if (!$lead) {
            return [];
        }
        
        $awarded = [];
        $badges = $this->getAllBadges();
        
        foreach ($badges as $badge) {
            // Bereits vergeben?
            if ($this->hasBadge($leadId, $badge['id'])) {
                continue;
            }
            
            // Bedingung prüfen
            if ($this->checkCondition($badge, $lead)) {
                if ($this->award($leadId, $badge['id'])) {
                    $awarded[] = $badge;
                }
            }
        }
        
        return $awarded;
    }
    
    /**
     * Badge-Bedingung prüfen
     */
    private function checkCondition($badge, $lead) {
        $condition = $badge['condition_type'];
        $value = $badge['condition_value'];
        
        switch ($condition) {
            case 'conversions':
                // X Conversions erreicht
                return $lead['conversions'] >= $value;
                
            case 'streak':
                // X Wochen Streak
                return $lead['current_streak'] >= $value;
                
            case 'longest_streak':
                // Längster Streak von X Wochen
                return $lead['longest_streak'] >= $value;
                
            case 'early_adopter':
                // Unter den ersten X Anmeldungen
                return $lead['signup_rank'] < $value;
                
            case 'days_active':
                // X Tage dabei (Jubiläum)
                return $lead['days_active'] >= $value;
                
            case 'points':
                // X Punkte erreicht
                return $lead['total_points'] >= $value;
                
            case 'clicks':
                // X Klicks generiert
                return $lead['clicks'] >= $value;
                
            default:
                return false;
        }
    }
    
    /**
     * Badge-Fortschritt für einen Lead
     */
    public function getBadgeProgress($leadId) {
        $lead = $this->db->fetch(
            "SELECT l.*, 
                    (SELECT COUNT(*) FROM leads WHERE id < l.id AND campaign_id = l.campaign_id) as signup_rank,
                    DATEDIFF(NOW(), l.created_at) as days_active
             FROM leads l 
             WHERE l.id = ?",
            [$leadId]
        );
        
        if (!$lead) {
            return [];
        }
        
        $badges = $this->getAllBadges();
        $earnedBadges = $this->getLeadBadges($leadId);
        $earnedIds = array_column($earnedBadges, 'id');
        
        $progress = [];
        
        foreach ($badges as $badge) {
            $isEarned = in_array($badge['id'], $earnedIds);
            $currentValue = $this->getCurrentValue($badge['condition_type'], $lead);
            $targetValue = $badge['condition_value'];
            
            $progress[] = [
                'badge' => $badge,
                'earned' => $isEarned,
                'earned_at' => $isEarned ? $this->getEarnedDate($leadId, $badge['id'], $earnedBadges) : null,
                'current' => $currentValue,
                'target' => $targetValue,
                'progress_percent' => min(100, round(($currentValue / max(1, $targetValue)) * 100))
            ];
        }
        
        return $progress;
    }
    
    /**
     * Aktuellen Wert für Bedingungstyp ermitteln
     */
    private function getCurrentValue($conditionType, $lead) {
        switch ($conditionType) {
            case 'conversions':
                return $lead['conversions'];
            case 'streak':
            case 'longest_streak':
                return $lead['longest_streak'];
            case 'early_adopter':
                return $lead['signup_rank'];
            case 'days_active':
                return $lead['days_active'];
            case 'points':
                return $lead['total_points'];
            case 'clicks':
                return $lead['clicks'];
            default:
                return 0;
        }
    }
    
    /**
     * Earned-Datum aus Badge-Liste holen
     */
    private function getEarnedDate($leadId, $badgeId, $earnedBadges) {
        foreach ($earnedBadges as $eb) {
            if ($eb['id'] == $badgeId) {
                return $eb['earned_at'];
            }
        }
        return null;
    }
    
    /**
     * Badges pro Kampagne zählen
     */
    public function getBadgeStatsByCustomer($customerId) {
        return $this->db->fetch(
            "SELECT 
                COUNT(DISTINCT lb.lead_id) as leads_with_badges,
                COUNT(lb.id) as total_badges_awarded,
                COUNT(DISTINCT lb.badge_id) as unique_badges_awarded
             FROM lead_badges lb
             JOIN leads l ON lb.lead_id = l.id
             JOIN campaigns c ON l.campaign_id = c.id
             WHERE c.customer_id = ?",
            [$customerId]
        );
    }
    
    /**
     * Top Badge-Earner für eine Kampagne
     */
    public function getTopBadgeEarners($campaignId, $limit = 10) {
        return $this->db->fetchAll(
            "SELECT 
                l.id, l.name, l.email,
                COUNT(lb.id) as badge_count,
                GROUP_CONCAT(b.icon SEPARATOR ' ') as badges
             FROM leads l
             LEFT JOIN lead_badges lb ON l.id = lb.lead_id
             LEFT JOIN badges b ON lb.badge_id = b.id
             WHERE l.campaign_id = ?
             GROUP BY l.id
             HAVING badge_count > 0
             ORDER BY badge_count DESC
             LIMIT ?",
            [$campaignId, $limit]
        );
    }
    
    /**
     * Neue Badges prüfen (Cron-Job)
     */
    public function checkAllPendingBadges($limit = 100) {
        // Aktive Leads mit kürzlicher Aktivität
        $leads = $this->db->fetchAll(
            "SELECT id FROM leads 
             WHERE status = 'active' 
             AND last_activity_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)
             ORDER BY last_activity_at DESC
             LIMIT ?",
            [$limit]
        );
        
        $totalAwarded = 0;
        foreach ($leads as $lead) {
            $awarded = $this->checkAndAward($lead['id']);
            $totalAwarded += count($awarded);
        }
        
        return $totalAwarded;
    }
}
