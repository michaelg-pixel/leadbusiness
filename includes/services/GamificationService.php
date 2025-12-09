<?php
/**
 * Leadbusiness - Gamification Service
 * 
 * Verwaltet alle Gamification-Features: Punkte, Streaks, Levels
 */

class GamificationService {
    
    private $db;
    
    // Punkte-System
    private $pointsConfig = [
        'share' => 5,          // Für jeden Share
        'click' => 1,          // Für jeden Klick auf Referral-Link
        'conversion' => 20,    // Für jede erfolgreiche Empfehlung
        'streak_week' => 50,   // Wochen-Streak Bonus
        'badge' => 25,         // Für jedes neue Badge
        'first_conversion' => 30  // Bonus für erste Conversion
    ];
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Punkte hinzufügen
     */
    public function addPoints($leadId, $action, $amount = null) {
        $points = $amount ?? ($this->pointsConfig[$action] ?? 0);
        
        if ($points <= 0) {
            return false;
        }
        
        // Punkte aktualisieren
        $this->db->query(
            "UPDATE leads SET 
                total_points = total_points + ?,
                last_activity_at = NOW(),
                updated_at = NOW()
             WHERE id = ?",
            [$points, $leadId]
        );
        
        // In Log speichern
        $this->db->insert('gamification_log', [
            'lead_id' => $leadId,
            'action' => $action,
            'points' => $points,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        return $points;
    }
    
    /**
     * Streak aktualisieren
     */
    public function updateStreak($leadId) {
        $lead = $this->db->fetch(
            "SELECT current_streak, longest_streak, last_share_at FROM leads WHERE id = ?",
            [$leadId]
        );
        
        if (!$lead) {
            return false;
        }
        
        $lastShare = $lead['last_share_at'] ? strtotime($lead['last_share_at']) : 0;
        $now = time();
        $daysSinceLastShare = ($now - $lastShare) / 86400;
        
        $newStreak = $lead['current_streak'];
        $streakBonus = 0;
        
        if ($daysSinceLastShare <= 7) {
            // Streak fortsetzen
            $newStreak++;
            
            // Bonus bei bestimmten Milestones
            if ($newStreak == 3) {
                $streakBonus = $this->pointsConfig['streak_week'];
            } elseif ($newStreak == 8) {
                $streakBonus = $this->pointsConfig['streak_week'] * 2;
            } elseif ($newStreak % 4 == 0) {
                $streakBonus = $this->pointsConfig['streak_week'];
            }
        } else {
            // Streak zurücksetzen
            $newStreak = 1;
        }
        
        // Längsten Streak aktualisieren
        $longestStreak = max($lead['longest_streak'], $newStreak);
        
        $this->db->update('leads', [
            'current_streak' => $newStreak,
            'longest_streak' => $longestStreak,
            'last_share_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$leadId]);
        
        // Streak-Bonus hinzufügen
        if ($streakBonus > 0) {
            $this->addPoints($leadId, 'streak_week', $streakBonus);
        }
        
        return [
            'streak' => $newStreak,
            'longest' => $longestStreak,
            'bonus' => $streakBonus
        ];
    }
    
    /**
     * Conversion verarbeiten
     */
    public function processConversion($leadId, $isFirst = false) {
        // Basis-Punkte
        $this->addPoints($leadId, 'conversion');
        
        // Bonus für erste Conversion
        if ($isFirst) {
            $this->addPoints($leadId, 'first_conversion');
        }
        
        // Conversion zählen
        $this->db->query(
            "UPDATE leads SET 
                conversions = conversions + 1,
                last_activity_at = NOW()
             WHERE id = ?",
            [$leadId]
        );
        
        // Belohnungsstufe prüfen
        $this->checkRewardLevel($leadId);
        
        // Badges prüfen
        $badgeService = new BadgeService();
        $badgeService->checkAndAward($leadId);
    }
    
    /**
     * Share verarbeiten
     */
    public function processShare($leadId, $platform) {
        // Punkte für Share
        $this->addPoints($leadId, 'share');
        
        // Streak aktualisieren
        $this->updateStreak($leadId);
        
        // Badges prüfen
        $badgeService = new BadgeService();
        $badgeService->checkAndAward($leadId);
    }
    
    /**
     * Klick verarbeiten
     */
    public function processClick($leadId) {
        // Punkte für Klick
        $this->addPoints($leadId, 'click');
        
        // Klicks zählen
        $this->db->query(
            "UPDATE leads SET clicks = clicks + 1 WHERE id = ?",
            [$leadId]
        );
    }
    
    /**
     * Belohnungsstufe prüfen und aktualisieren
     */
    public function checkRewardLevel($leadId) {
        $lead = $this->db->fetch(
            "SELECT l.*, c.customer_id 
             FROM leads l
             JOIN campaigns c ON l.campaign_id = c.id
             WHERE l.id = ?",
            [$leadId]
        );
        
        if (!$lead) {
            return null;
        }
        
        // Aktuelle Belohnungsstufen des Kunden
        $rewards = $this->db->fetchAll(
            "SELECT * FROM rewards 
             WHERE campaign_id = ? AND is_active = 1
             ORDER BY required_conversions ASC",
            [$lead['campaign_id']]
        );
        
        $currentLevel = $lead['current_reward_level'];
        $newLevel = 0;
        $newReward = null;
        
        foreach ($rewards as $reward) {
            if ($lead['conversions'] >= $reward['required_conversions']) {
                $newLevel = $reward['level'];
                $newReward = $reward;
            }
        }
        
        // Level gestiegen?
        if ($newLevel > $currentLevel) {
            // Level aktualisieren
            $this->db->update('leads', [
                'current_reward_level' => $newLevel
            ], 'id = ?', [$leadId]);
            
            // Belohnung auslösen
            if ($newReward) {
                $this->triggerReward($lead, $newReward);
            }
            
            return [
                'old_level' => $currentLevel,
                'new_level' => $newLevel,
                'reward' => $newReward
            ];
        }
        
        return null;
    }
    
    /**
     * Belohnung auslösen (E-Mail in Queue)
     */
    private function triggerReward($lead, $reward) {
        // Delivery-Eintrag erstellen
        $deliveryId = $this->db->insert('reward_deliveries', [
            'lead_id' => $lead['id'],
            'reward_id' => $reward['id'],
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        // E-Mail in Queue
        $mailgun = new MailgunService();
        $mailgun->queue(
            $lead['customer_id'],
            $lead['email'],
            $lead['name'],
            'reward_earned',
            [
                'lead_name' => $lead['name'] ?: 'Empfehler',
                'reward_level' => $reward['level'],
                'reward_description' => $reward['description'],
                'reward_value' => $reward['reward_value'],
                'reward_type' => $this->getRewardTypeName($reward['reward_type']),
                'total_conversions' => $lead['conversions']
            ],
            10 // Hohe Priorität
        );
        
        // Punkte für Badge
        $this->addPoints($lead['id'], 'badge');
        
        return $deliveryId;
    }
    
    /**
     * Belohnungstyp-Name
     */
    private function getRewardTypeName($type) {
        $names = [
            'discount' => 'Rabatt',
            'coupon_code' => 'Gutschein-Code',
            'free_product' => 'Gratis-Produkt',
            'free_service' => 'Gratis-Service',
            'digital_download' => 'Digital-Download',
            'voucher' => 'Wertgutschein'
        ];
        return $names[$type] ?? $type;
    }
    
    /**
     * Lead-Level berechnen (1-10 basierend auf Punkten)
     */
    public function calculateLevel($points) {
        $levels = [
            0 => 1,
            100 => 2,
            250 => 3,
            500 => 4,
            1000 => 5,
            2000 => 6,
            3500 => 7,
            5000 => 8,
            7500 => 9,
            10000 => 10
        ];
        
        $level = 1;
        foreach ($levels as $threshold => $lvl) {
            if ($points >= $threshold) {
                $level = $lvl;
            }
        }
        
        return $level;
    }
    
    /**
     * Fortschritt zur nächsten Belohnungsstufe
     */
    public function getProgressToNextReward($leadId) {
        $lead = $this->db->fetch(
            "SELECT l.conversions, l.current_reward_level, l.campaign_id
             FROM leads l WHERE l.id = ?",
            [$leadId]
        );
        
        if (!$lead) {
            return null;
        }
        
        // Nächste Stufe finden
        $nextReward = $this->db->fetch(
            "SELECT * FROM rewards 
             WHERE campaign_id = ? 
             AND level > ?
             AND is_active = 1
             ORDER BY level ASC
             LIMIT 1",
            [$lead['campaign_id'], $lead['current_reward_level']]
        );
        
        if (!$nextReward) {
            // Alle Stufen erreicht
            return [
                'completed' => true,
                'current' => $lead['conversions'],
                'required' => $lead['conversions'],
                'progress' => 100,
                'remaining' => 0
            ];
        }
        
        $remaining = $nextReward['required_conversions'] - $lead['conversions'];
        $progress = min(100, round(($lead['conversions'] / $nextReward['required_conversions']) * 100));
        
        return [
            'completed' => false,
            'current' => $lead['conversions'],
            'required' => $nextReward['required_conversions'],
            'progress' => $progress,
            'remaining' => max(0, $remaining),
            'next_reward' => $nextReward
        ];
    }
    
    /**
     * Gamification-Statistiken für Lead
     */
    public function getLeadStats($leadId) {
        $lead = $this->db->fetch(
            "SELECT * FROM leads WHERE id = ?",
            [$leadId]
        );
        
        if (!$lead) {
            return null;
        }
        
        $badgeService = new BadgeService();
        $badges = $badgeService->getLeadBadges($leadId);
        
        return [
            'points' => $lead['total_points'],
            'level' => $this->calculateLevel($lead['total_points']),
            'conversions' => $lead['conversions'],
            'clicks' => $lead['clicks'],
            'current_streak' => $lead['current_streak'],
            'longest_streak' => $lead['longest_streak'],
            'reward_level' => $lead['current_reward_level'],
            'badges' => $badges,
            'badge_count' => count($badges),
            'progress' => $this->getProgressToNextReward($leadId)
        ];
    }
    
    /**
     * Wochen-Streaks berechnen (Cron-Job)
     */
    public function calculateWeeklyStreaks() {
        // Leads deren letzter Share > 7 Tage her ist
        $expiredLeads = $this->db->fetchAll(
            "SELECT id FROM leads 
             WHERE current_streak > 0 
             AND last_share_at < DATE_SUB(NOW(), INTERVAL 7 DAY)"
        );
        
        $reset = 0;
        foreach ($expiredLeads as $lead) {
            $this->db->update('leads', ['current_streak' => 0], 'id = ?', [$lead['id']]);
            $reset++;
        }
        
        return $reset;
    }
}
