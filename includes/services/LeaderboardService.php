<?php
/**
 * Leadbusiness - Leaderboard Service
 * 
 * Verwaltet das Leaderboard/Rangliste der Top-Empfehler
 * PHP 7.4+ kompatibel
 */

use Leadbusiness\Database;

class LeaderboardService {
    
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Leaderboard für eine Kampagne abrufen
     */
    public function getLeaderboard($campaignId, $limit = 10) {
        return $this->db->fetchAll(
            "SELECT 
                l.id,
                l.name,
                l.conversions,
                l.total_points,
                l.current_streak,
                l.created_at,
                (SELECT COUNT(*) FROM lead_badges lb WHERE lb.lead_id = l.id) as badge_count
             FROM leads l
             WHERE l.campaign_id = ?
             AND l.status = 'active'
             AND l.conversions > 0
             ORDER BY l.conversions DESC, l.total_points DESC
             LIMIT ?",
            [$campaignId, $limit]
        );
    }
    
    /**
     * Leaderboard mit anonymisierten Namen
     */
    public function getAnonymizedLeaderboard($campaignId, $limit = 10) {
        $leaders = $this->getLeaderboard($campaignId, $limit);
        
        foreach ($leaders as &$leader) {
            $leader['display_name'] = $this->anonymizeName($leader['name']);
        }
        
        return $leaders;
    }
    
    /**
     * Namen anonymisieren (z.B. "Max M.")
     */
    private function anonymizeName($name) {
        if (empty($name)) {
            return 'Anonymer Empfehler';
        }
        
        $parts = explode(' ', trim($name));
        
        if (count($parts) === 1) {
            // Nur Vorname
            return $parts[0];
        }
        
        // Vorname + Initial des Nachnamens
        $firstName = $parts[0];
        $lastInitial = strtoupper(substr($parts[count($parts) - 1], 0, 1));
        
        return "{$firstName} {$lastInitial}.";
    }
    
    /**
     * Rang eines Leads ermitteln
     */
    public function getLeadRank($leadId) {
        $lead = $this->db->fetch(
            "SELECT campaign_id, conversions, total_points FROM leads WHERE id = ?",
            [$leadId]
        );
        
        if (!$lead) {
            return null;
        }
        
        // Rang berechnen
        $rank = $this->db->fetch(
            "SELECT COUNT(*) + 1 as rank
             FROM leads
             WHERE campaign_id = ?
             AND status = 'active'
             AND (conversions > ? OR (conversions = ? AND total_points > ?))",
            [$lead['campaign_id'], $lead['conversions'], $lead['conversions'], $lead['total_points']]
        );
        
        // Gesamtzahl aktiver Leads
        $total = $this->db->fetch(
            "SELECT COUNT(*) as total 
             FROM leads 
             WHERE campaign_id = ? AND status = 'active' AND conversions > 0",
            [$lead['campaign_id']]
        );
        
        return [
            'rank' => $rank['rank'],
            'total' => $total['total'],
            'percentile' => $total['total'] > 0 
                ? round((1 - ($rank['rank'] - 1) / $total['total']) * 100) 
                : 100
        ];
    }
    
    /**
     * Leaderboard Cache aktualisieren
     */
    public function updateCache($campaignId) {
        // Alte Cache-Einträge löschen
        $this->db->query(
            "DELETE FROM leaderboard_cache WHERE campaign_id = ?",
            [$campaignId]
        );
        
        // Top 10 neu berechnen
        $leaders = $this->getLeaderboard($campaignId, 10);
        
        $rank = 0;
        foreach ($leaders as $leader) {
            $rank++;
            $this->db->insert('leaderboard_cache', [
                'campaign_id' => $campaignId,
                'lead_id' => $leader['id'],
                'rank' => $rank,
                'conversions' => $leader['conversions'],
                'total_points' => $leader['total_points'],
                'display_name' => $this->anonymizeName($leader['name']),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        return $rank;
    }
    
    /**
     * Gecachtes Leaderboard abrufen
     */
    public function getCachedLeaderboard($campaignId) {
        return $this->db->fetchAll(
            "SELECT * FROM leaderboard_cache 
             WHERE campaign_id = ?
             ORDER BY rank ASC",
            [$campaignId]
        );
    }
    
    /**
     * Alle Kampagnen-Leaderboards aktualisieren (Cron)
     */
    public function updateAllCaches() {
        $campaigns = $this->db->fetchAll(
            "SELECT id FROM campaigns WHERE is_active = 1"
        );
        
        $updated = 0;
        foreach ($campaigns as $campaign) {
            $this->updateCache($campaign['id']);
            $updated++;
        }
        
        return $updated;
    }
    
    /**
     * Live-Counter Daten für eine Kampagne
     */
    public function getLiveCounterData($campaignId) {
        // Gesamtzahl der Teilnehmer
        $totalLeads = $this->db->fetch(
            "SELECT COUNT(*) as count FROM leads 
             WHERE campaign_id = ? AND status IN ('active', 'pending')",
            [$campaignId]
        );
        
        // Heute beigetreten
        $todayLeads = $this->db->fetch(
            "SELECT COUNT(*) as count FROM leads 
             WHERE campaign_id = ? AND DATE(created_at) = CURDATE()",
            [$campaignId]
        );
        
        // Aktive in letzten 24h
        $activeRecently = $this->db->fetch(
            "SELECT COUNT(*) as count FROM leads 
             WHERE campaign_id = ? 
             AND last_activity_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)",
            [$campaignId]
        );
        
        return [
            'total' => $totalLeads['count'] ?? 0,
            'today' => $todayLeads['count'] ?? 0,
            'active_24h' => $activeRecently['count'] ?? 0
        ];
    }
    
    /**
     * Wöchentliche Top-Performer
     */
    public function getWeeklyTopPerformers($campaignId, $limit = 5) {
        return $this->db->fetchAll(
            "SELECT 
                l.id,
                l.name,
                COUNT(c.id) as weekly_conversions,
                SUM(CASE WHEN c.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as recent_conversions
             FROM leads l
             LEFT JOIN conversions c ON l.id = c.referrer_id
             WHERE l.campaign_id = ?
             AND l.status = 'active'
             AND c.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
             GROUP BY l.id
             ORDER BY weekly_conversions DESC
             LIMIT ?",
            [$campaignId, $limit]
        );
    }
    
    /**
     * Statistiken für Kunden-Dashboard
     */
    public function getCustomerStats($customerId) {
        // Alle Kampagnen des Kunden
        $stats = $this->db->fetch(
            "SELECT 
                COUNT(DISTINCT l.id) as total_leads,
                SUM(l.conversions) as total_conversions,
                SUM(l.clicks) as total_clicks,
                AVG(l.conversions) as avg_conversions,
                MAX(l.conversions) as max_conversions
             FROM leads l
             JOIN campaigns c ON l.campaign_id = c.id
             WHERE c.customer_id = ?
             AND l.status = 'active'",
            [$customerId]
        );
        
        // Top-Performer
        $topPerformer = $this->db->fetch(
            "SELECT l.name, l.conversions, l.total_points
             FROM leads l
             JOIN campaigns c ON l.campaign_id = c.id
             WHERE c.customer_id = ?
             AND l.status = 'active'
             ORDER BY l.conversions DESC
             LIMIT 1",
            [$customerId]
        );
        
        return [
            'stats' => $stats,
            'top_performer' => $topPerformer
        ];
    }
    
    /**
     * Trend-Daten (letzte 30 Tage)
     */
    public function getTrendData($campaignId, $days = 30) {
        return $this->db->fetchAll(
            "SELECT 
                DATE(created_at) as date,
                COUNT(*) as new_leads,
                (SELECT SUM(conversions) FROM leads WHERE campaign_id = ? AND DATE(created_at) <= dates.date) as cumulative_conversions
             FROM leads dates
             WHERE campaign_id = ?
             AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
             GROUP BY DATE(created_at)
             ORDER BY date ASC",
            [$campaignId, $campaignId, $days]
        );
    }
}
