<?php
/**
 * DashboardLayoutService
 * 
 * Steuert das branchenspezifische Dashboard-Layout basierend auf:
 * - Industry (Handwerker, Coach, Online-Shop, etc.)
 * - Plan (Starter, Professional, Enterprise)
 * 
 * Drei Dashboard-Varianten:
 * 1. offline_local - QR-Code fokussiert (Handwerker, Friseur, Zahnarzt)
 * 2. online_business - Link/Social fokussiert (Coach, Online-Shop, Newsletter)
 * 3. hybrid - Beides (Agentur, Fotograf, Sonstiges)
 */

class DashboardLayoutService
{
    private $db;
    private $cache = [];
    
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    /**
     * Holt die komplette Dashboard-Konfiguration für einen Kunden
     */
    public function getCustomerDashboard($customerId)
    {
        $cacheKey = "dashboard_{$customerId}";
        
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }
        
        $stmt = $this->db->prepare("
            SELECT 
                c.id as customer_id,
                c.company_name,
                c.industry,
                c.plan,
                c.subdomain,
                c.primary_color,
                
                -- Industry Mapping
                idm.business_type,
                idm.customer_term,
                idm.referral_term,
                idm.success_term,
                idm.industry_icon,
                idm.industry_color,
                idm.industry_display_name,
                
                -- Dashboard Layout
                dl.layout_key,
                dl.display_name as layout_display_name,
                dl.welcome_text,
                dl.cta_text,
                dl.share_order,
                
                -- Plan-spezifische Werte
                CASE c.plan 
                    WHEN 'starter' THEN dl.primary_module_starter
                    WHEN 'professional' THEN dl.primary_module_professional
                    ELSE dl.primary_module_enterprise
                END as primary_module,
                
                CASE c.plan 
                    WHEN 'starter' THEN dl.modules_starter
                    WHEN 'professional' THEN dl.modules_professional
                    ELSE dl.modules_enterprise
                END as available_modules,
                
                CASE c.plan 
                    WHEN 'starter' THEN dl.quick_actions_starter
                    WHEN 'professional' THEN dl.quick_actions_professional
                    ELSE dl.quick_actions_enterprise
                END as quick_actions
                
            FROM customers c
            LEFT JOIN industry_dashboard_mapping idm ON idm.industry = c.industry
            LEFT JOIN dashboard_layouts dl ON dl.layout_key = idm.dashboard_layout_key
            WHERE c.id = ?
        ");
        
        $stmt->execute([$customerId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return $this->getDefaultDashboard();
        }
        
        // JSON-Felder dekodieren
        $result['available_modules'] = json_decode($result['available_modules'] ?? '[]', true);
        $result['quick_actions'] = json_decode($result['quick_actions'] ?? '[]', true);
        $result['share_order'] = json_decode($result['share_order'] ?? '[]', true);
        
        // Module mit Details anreichern
        $result['modules'] = $this->getModulesWithDetails($result['available_modules'], $result['plan']);
        
        // Upgrade-Hinweise für nicht verfügbare Module
        $result['upgrade_modules'] = $this->getUpgradeModules($result['plan'], $result['layout_key']);
        
        $this->cache[$cacheKey] = $result;
        
        return $result;
    }
    
    /**
     * Holt Modul-Details mit Verfügbarkeitsstatus
     */
    public function getModulesWithDetails($moduleKeys, $plan)
    {
        if (empty($moduleKeys)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($moduleKeys), '?'));
        
        $stmt = $this->db->prepare("
            SELECT 
                module_key,
                display_name,
                description,
                icon,
                module_type,
                grid_size,
                component_path,
                available_starter,
                available_professional,
                available_enterprise
            FROM dashboard_modules
            WHERE module_key IN ({$placeholders})
            AND is_active = TRUE
            ORDER BY sort_order
        ");
        
        $stmt->execute($moduleKeys);
        $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Verfügbarkeit pro Modul prüfen
        foreach ($modules as &$module) {
            $module['is_available'] = $this->isModuleAvailable($module, $plan);
        }
        
        return $modules;
    }
    
    /**
     * Prüft ob ein Modul für den aktuellen Plan verfügbar ist
     */
    public function isModuleAvailable($module, $plan)
    {
        switch ($plan) {
            case 'enterprise':
                return (bool)$module['available_enterprise'];
            case 'professional':
                return (bool)$module['available_professional'];
            case 'starter':
            default:
                return (bool)$module['available_starter'];
        }
    }
    
    /**
     * Holt Module die im aktuellen Plan NICHT verfügbar sind (für Upgrade-Hinweise)
     */
    public function getUpgradeModules($plan, $layoutKey)
    {
        $planField = "available_{$plan}";
        
        // Hole das Layout um zu wissen welche Module relevant wären
        $stmt = $this->db->prepare("
            SELECT modules_professional, modules_enterprise
            FROM dashboard_layouts
            WHERE layout_key = ?
        ");
        $stmt->execute([$layoutKey]);
        $layout = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$layout) {
            return [];
        }
        
        // Sammle alle Module aus höheren Plans
        $allModules = array_unique(array_merge(
            json_decode($layout['modules_professional'] ?? '[]', true),
            json_decode($layout['modules_enterprise'] ?? '[]', true)
        ));
        
        if (empty($allModules)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($allModules), '?'));
        
        $stmt = $this->db->prepare("
            SELECT 
                module_key,
                display_name,
                icon,
                upgrade_text,
                CASE 
                    WHEN available_professional = TRUE AND available_starter = FALSE THEN 'professional'
                    WHEN available_enterprise = TRUE AND available_professional = FALSE THEN 'enterprise'
                    ELSE 'professional'
                END as required_plan
            FROM dashboard_modules
            WHERE module_key IN ({$placeholders})
            AND {$planField} = FALSE
            AND show_upgrade_badge = TRUE
            AND is_active = TRUE
            ORDER BY sort_order
        ");
        
        $stmt->execute($allModules);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Holt alle verfügbaren Industries für das Onboarding
     */
    public function getAvailableIndustries()
    {
        $stmt = $this->db->query("
            SELECT 
                industry,
                industry_display_name,
                business_type,
                industry_icon,
                industry_color,
                dashboard_layout_key
            FROM industry_dashboard_mapping
            WHERE is_active = TRUE
            ORDER BY sort_order
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Holt die Share-Button Reihenfolge für eine Branche
     */
    public function getShareOrder($industry)
    {
        $stmt = $this->db->prepare("
            SELECT dl.share_order
            FROM industry_dashboard_mapping idm
            JOIN dashboard_layouts dl ON dl.layout_key = idm.dashboard_layout_key
            WHERE idm.industry = ?
        ");
        
        $stmt->execute([$industry]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && !empty($result['share_order'])) {
            return json_decode($result['share_order'], true);
        }
        
        // Default Share Order
        return ['whatsapp', 'facebook', 'email', 'linkedin', 'sms', 'link_copy'];
    }
    
    /**
     * Holt branchenspezifische Texte
     */
    public function getIndustryTexts($industry)
    {
        $stmt = $this->db->prepare("
            SELECT 
                customer_term,
                referral_term,
                success_term,
                industry_display_name
            FROM industry_dashboard_mapping
            WHERE industry = ?
        ");
        
        $stmt->execute([$industry]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: [
            'customer_term' => 'Kunden',
            'referral_term' => 'Empfehlung',
            'success_term' => 'Neukunde',
            'industry_display_name' => 'Unternehmen'
        ];
    }
    
    /**
     * Prüft ob ein Feature/Modul für Plan verfügbar ist
     */
    public function isFeatureAvailable($feature, $plan)
    {
        $stmt = $this->db->prepare("
            SELECT available_starter, available_professional, available_enterprise
            FROM dashboard_modules
            WHERE module_key = ?
        ");
        
        $stmt->execute([$feature]);
        $module = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$module) {
            return false;
        }
        
        return $this->isModuleAvailable($module, $plan);
    }
    
    /**
     * Gibt das Quick-Action Set für Dashboard-Header zurück
     */
    public function getQuickActions($customerId)
    {
        $dashboard = $this->getCustomerDashboard($customerId);
        
        $actionDefinitions = [
            'download_qr' => [
                'label' => 'QR-Code',
                'icon' => 'fas fa-qrcode',
                'action' => 'downloadQR',
                'class' => 'btn-primary'
            ],
            'download_poster' => [
                'label' => 'Poster',
                'icon' => 'fas fa-file-image',
                'action' => 'downloadPoster',
                'class' => 'btn-secondary',
                'pro' => true
            ],
            'download_flyer' => [
                'label' => 'Flyer',
                'icon' => 'fas fa-file-alt',
                'action' => 'downloadFlyer',
                'class' => 'btn-secondary',
                'pro' => true
            ],
            'copy_link' => [
                'label' => 'Link kopieren',
                'icon' => 'fas fa-copy',
                'action' => 'copyLink',
                'class' => 'btn-primary'
            ],
            'share_whatsapp' => [
                'label' => 'WhatsApp',
                'icon' => 'fab fa-whatsapp',
                'action' => 'shareWhatsApp',
                'class' => 'btn-success'
            ],
            'share_social' => [
                'label' => 'Teilen',
                'icon' => 'fas fa-share-alt',
                'action' => 'openShareModal',
                'class' => 'btn-secondary'
            ],
            'email_template' => [
                'label' => 'E-Mail-Vorlage',
                'icon' => 'fas fa-envelope',
                'action' => 'openEmailTemplate',
                'class' => 'btn-secondary',
                'pro' => true
            ],
            'embed_widget' => [
                'label' => 'Widget',
                'icon' => 'fas fa-code',
                'action' => 'openWidgetModal',
                'class' => 'btn-secondary',
                'pro' => true
            ],
            'broadcast_email' => [
                'label' => 'Broadcast',
                'icon' => 'fas fa-paper-plane',
                'action' => 'openBroadcast',
                'class' => 'btn-secondary',
                'pro' => true
            ],
            'api_docs' => [
                'label' => 'API',
                'icon' => 'fas fa-plug',
                'action' => 'openApiDocs',
                'class' => 'btn-secondary',
                'enterprise' => true
            ]
        ];
        
        $actions = [];
        foreach ($dashboard['quick_actions'] as $actionKey) {
            if (isset($actionDefinitions[$actionKey])) {
                $action = $actionDefinitions[$actionKey];
                $action['key'] = $actionKey;
                
                // Prüfe ob Action für Plan verfügbar
                if (isset($action['enterprise']) && $dashboard['plan'] !== 'enterprise') {
                    $action['locked'] = true;
                    $action['required_plan'] = 'enterprise';
                } elseif (isset($action['pro']) && $dashboard['plan'] === 'starter') {
                    $action['locked'] = true;
                    $action['required_plan'] = 'professional';
                } else {
                    $action['locked'] = false;
                }
                
                $actions[] = $action;
            }
        }
        
        return $actions;
    }
    
    /**
     * Default Dashboard für Fallback
     */
    private function getDefaultDashboard()
    {
        return [
            'layout_key' => 'hybrid',
            'layout_display_name' => 'Standard',
            'business_type' => 'hybrid',
            'customer_term' => 'Kunden',
            'referral_term' => 'Empfehlung',
            'success_term' => 'Neukunde',
            'industry_icon' => 'fas fa-briefcase',
            'primary_module' => 'referral_link',
            'available_modules' => ['referral_link', 'qr_code_simple', 'quick_stats', 'quick_share'],
            'quick_actions' => ['copy_link', 'download_qr'],
            'share_order' => ['whatsapp', 'facebook', 'email', 'linkedin', 'sms', 'link_copy'],
            'welcome_text' => 'Teilen Sie Ihr Empfehlungsprogramm!',
            'cta_text' => 'Jetzt teilen',
            'modules' => [],
            'upgrade_modules' => []
        ];
    }
    
    /**
     * Invalidiert den Cache für einen Kunden (z.B. nach Plan-Upgrade)
     */
    public function invalidateCache($customerId)
    {
        unset($this->cache["dashboard_{$customerId}"]);
    }
    
    /**
     * Prüft ob Dashboard-Typ "offline" ist (für QR-Code-Priorisierung)
     */
    public function isOfflineBusiness($customerId)
    {
        $dashboard = $this->getCustomerDashboard($customerId);
        return $dashboard['business_type'] === 'offline';
    }
    
    /**
     * Prüft ob Dashboard-Typ "online" ist (für Link-Priorisierung)
     */
    public function isOnlineBusiness($customerId)
    {
        $dashboard = $this->getCustomerDashboard($customerId);
        return $dashboard['business_type'] === 'online';
    }
}
