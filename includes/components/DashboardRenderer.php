<?php
/**
 * DashboardRenderer
 * 
 * Rendert das Dashboard dynamisch basierend auf:
 * - Branche (offline_local, online_business, hybrid)
 * - Tarif (starter, professional, enterprise)
 * 
 * Lädt die passenden Module und zeigt Upgrade-Hinweise für nicht verfügbare Features.
 */

class DashboardRenderer
{
    private $db;
    private $layoutService;
    private $customer;
    private $campaign;
    private $dashboardLayout;
    
    public function __construct($db, $customer, $campaign = null)
    {
        $this->db = $db;
        $this->customer = $customer;
        $this->campaign = $campaign ?? $this->getDefaultCampaign();
        
        require_once __DIR__ . '/../services/DashboardLayoutService.php';
        $this->layoutService = new DashboardLayoutService($db);
        $this->dashboardLayout = $this->layoutService->getCustomerDashboard($customer['id']);
    }
    
    /**
     * Rendert das komplette Dashboard
     */
    public function render()
    {
        $layout = $this->dashboardLayout;
        
        ob_start();
        ?>
        <div class="dashboard-container" 
             data-layout="<?= htmlspecialchars($layout['layout_key']) ?>"
             data-plan="<?= htmlspecialchars($this->customer['plan']) ?>"
             data-business-type="<?= htmlspecialchars($layout['business_type']) ?>">
            
            <!-- Dashboard Header mit Quick Actions -->
            <?php $this->renderHeader(); ?>
            
            <!-- Hauptbereich mit Modulen -->
            <div class="dashboard-modules grid gap-6">
                
                <!-- Primäres Modul (groß, oben) -->
                <div class="primary-module-area">
                    <?php $this->renderModule($layout['primary_module'], 'primary'); ?>
                </div>
                
                <!-- Sekundäre Module (Grid) -->
                <div class="secondary-modules-area grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php 
                    foreach ($layout['available_modules'] as $moduleKey) {
                        // Primäres Modul überspringen (wurde oben gerendert)
                        if ($moduleKey === $layout['primary_module']) continue;
                        
                        $this->renderModule($moduleKey, 'secondary');
                    }
                    ?>
                </div>
                
                <!-- Upgrade-Hinweise -->
                <?php if (!empty($layout['upgrade_modules']) && $this->customer['plan'] !== 'enterprise'): ?>
                <div class="upgrade-section mt-8">
                    <?php $this->renderUpgradeHints(); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Rendert den Dashboard-Header mit Quick Actions
     */
    private function renderHeader()
    {
        $layout = $this->dashboardLayout;
        $quickActions = $this->layoutService->getQuickActions($this->customer['id']);
        $industryIcon = $layout['industry_icon'] ?? 'fas fa-briefcase';
        
        ?>
        <div class="dashboard-header mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Titel & Willkommen -->
                <div class="header-left">
                    <div class="flex items-center gap-3">
                        <div class="industry-icon w-12 h-12 rounded-xl flex items-center justify-center"
                             style="background-color: <?= htmlspecialchars($layout['industry_color'] ?? '#6366f1') ?>20">
                            <i class="<?= htmlspecialchars($industryIcon) ?> text-xl"
                               style="color: <?= htmlspecialchars($layout['industry_color'] ?? '#6366f1') ?>"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-800 dark:text-white">
                                <?= htmlspecialchars($this->customer['company_name']) ?>
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <?= htmlspecialchars($layout['welcome_text'] ?? 'Willkommen in Ihrem Empfehlungsprogramm') ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="header-actions flex flex-wrap gap-2">
                    <?php foreach ($quickActions as $action): ?>
                        <?php if ($action['locked']): ?>
                            <button class="btn btn-sm btn-outline opacity-50 cursor-not-allowed relative group"
                                    disabled
                                    title="Verfügbar im <?= ucfirst($action['required_plan']) ?>-Plan">
                                <i class="<?= htmlspecialchars($action['icon']) ?> mr-1"></i>
                                <?= htmlspecialchars($action['label']) ?>
                                <i class="fas fa-lock text-xs ml-1"></i>
                                
                                <!-- Tooltip -->
                                <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                                    <?= ucfirst($action['required_plan']) ?>-Plan erforderlich
                                </span>
                            </button>
                        <?php else: ?>
                            <button class="btn btn-sm <?= htmlspecialchars($action['class']) ?>"
                                    onclick="<?= htmlspecialchars($action['action']) ?>()">
                                <i class="<?= htmlspecialchars($action['icon']) ?> mr-1"></i>
                                <?= htmlspecialchars($action['label']) ?>
                            </button>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Plan Badge -->
            <div class="plan-badge mt-4 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                        <?php 
                        switch($this->customer['plan']) {
                            case 'enterprise': echo 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300'; break;
                            case 'professional': echo 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'; break;
                            default: echo 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                        }
                        ?>">
                <i class="fas fa-<?= $this->customer['plan'] === 'starter' ? 'seedling' : ($this->customer['plan'] === 'professional' ? 'star' : 'crown') ?> mr-1"></i>
                <?= ucfirst($this->customer['plan']) ?>-Plan
                
                <?php if ($this->customer['plan'] !== 'enterprise'): ?>
                    <a href="/dashboard/upgrade.php" class="ml-2 underline hover:no-underline">
                        Upgrade
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Rendert ein einzelnes Modul
     */
    private function renderModule($moduleKey, $type = 'secondary')
    {
        // Modul-Pfad bestimmen
        $modulePath = __DIR__ . "/dashboard-modules/{$moduleKey}.php";
        
        if (!file_exists($modulePath)) {
            // Fallback: Platzhalter für nicht implementierte Module
            $this->renderModulePlaceholder($moduleKey, $type);
            return;
        }
        
        // Variablen für das Modul bereitstellen
        $customer = $this->customer;
        $campaign = $this->campaign;
        $dashboardLayout = $this->dashboardLayout;
        $db = $this->db;
        
        // CSS-Klassen basierend auf Grid-Size
        $moduleInfo = $this->getModuleInfo($moduleKey);
        $gridClass = $this->getGridClass($moduleInfo['grid_size'] ?? 'half', $type);
        
        ?>
        <div class="module-wrapper <?= $gridClass ?>" data-module-key="<?= htmlspecialchars($moduleKey) ?>">
            <?php include $modulePath; ?>
        </div>
        <?php
    }
    
    /**
     * Rendert einen Platzhalter für nicht implementierte Module
     */
    private function renderModulePlaceholder($moduleKey, $type)
    {
        $moduleInfo = $this->getModuleInfo($moduleKey);
        $gridClass = $this->getGridClass($moduleInfo['grid_size'] ?? 'half', $type);
        
        ?>
        <div class="module-wrapper <?= $gridClass ?>">
            <div class="dashboard-module module-placeholder p-6 bg-gray-100 dark:bg-gray-700/50 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600">
                <div class="text-center text-gray-500 dark:text-gray-400">
                    <i class="<?= htmlspecialchars($moduleInfo['icon'] ?? 'fas fa-puzzle-piece') ?> text-3xl mb-2"></i>
                    <p class="font-medium"><?= htmlspecialchars($moduleInfo['display_name'] ?? $moduleKey) ?></p>
                    <p class="text-sm mt-1">Modul wird vorbereitet...</p>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Rendert Upgrade-Hinweise für nicht verfügbare Module
     */
    private function renderUpgradeHints()
    {
        $upgradeModules = $this->dashboardLayout['upgrade_modules'];
        
        if (empty($upgradeModules)) return;
        
        // Gruppieren nach erforderlichem Plan
        $byPlan = ['professional' => [], 'enterprise' => []];
        foreach ($upgradeModules as $module) {
            $plan = $module['required_plan'] ?? 'professional';
            $byPlan[$plan][] = $module;
        }
        
        ?>
        <div class="upgrade-hints bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                <i class="fas fa-rocket text-blue-500 mr-2"></i>
                Mehr Funktionen freischalten
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php if (!empty($byPlan['professional']) && $this->customer['plan'] === 'starter'): ?>
                <div class="upgrade-plan-card bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300 rounded text-xs font-medium">
                            <i class="fas fa-star mr-1"></i> Professional
                        </span>
                        <span class="text-sm text-gray-500">99€/Monat</span>
                    </div>
                    <ul class="space-y-2">
                        <?php foreach (array_slice($byPlan['professional'], 0, 4) as $module): ?>
                        <li class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                            <i class="<?= htmlspecialchars($module['icon']) ?> w-5 text-gray-400 mr-2"></i>
                            <?= htmlspecialchars($module['display_name']) ?>
                        </li>
                        <?php endforeach; ?>
                        <?php if (count($byPlan['professional']) > 4): ?>
                        <li class="text-sm text-gray-500">
                            + <?= count($byPlan['professional']) - 4 ?> weitere Funktionen
                        </li>
                        <?php endif; ?>
                    </ul>
                    <a href="/dashboard/upgrade.php?plan=professional" 
                       class="btn btn-primary btn-sm w-full mt-4">
                        Jetzt upgraden
                    </a>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($byPlan['enterprise'])): ?>
                <div class="upgrade-plan-card bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-2 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300 rounded text-xs font-medium">
                            <i class="fas fa-crown mr-1"></i> Enterprise
                        </span>
                        <span class="text-sm text-gray-500">Auf Anfrage</span>
                    </div>
                    <ul class="space-y-2">
                        <?php foreach (array_slice($byPlan['enterprise'], 0, 4) as $module): ?>
                        <li class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                            <i class="<?= htmlspecialchars($module['icon']) ?> w-5 text-gray-400 mr-2"></i>
                            <?= htmlspecialchars($module['display_name']) ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="/kontakt.php?subject=enterprise" 
                       class="btn btn-outline btn-sm w-full mt-4">
                        Kontakt aufnehmen
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Holt Modul-Info aus der Datenbank
     */
    private function getModuleInfo($moduleKey)
    {
        static $cache = [];
        
        if (isset($cache[$moduleKey])) {
            return $cache[$moduleKey];
        }
        
        $stmt = $this->db->prepare("
            SELECT * FROM dashboard_modules WHERE module_key = ?
        ");
        $stmt->execute([$moduleKey]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $cache[$moduleKey] = $result ?: [
            'module_key' => $moduleKey,
            'display_name' => ucfirst(str_replace('_', ' ', $moduleKey)),
            'icon' => 'fas fa-puzzle-piece',
            'grid_size' => 'half'
        ];
        
        return $cache[$moduleKey];
    }
    
    /**
     * Bestimmt die CSS Grid-Klasse
     */
    private function getGridClass($size, $type)
    {
        if ($type === 'primary') {
            return 'col-span-full';
        }
        
        switch ($size) {
            case 'full':
                return 'md:col-span-2';
            case 'third':
                return 'md:col-span-1 lg:col-span-1';
            case 'quarter':
                return 'col-span-1';
            case 'half':
            default:
                return 'col-span-1';
        }
    }
    
    /**
     * Holt die Standard-Kampagne des Kunden
     */
    private function getDefaultCampaign()
    {
        $stmt = $this->db->prepare("
            SELECT * FROM campaigns 
            WHERE customer_id = ? AND is_active = TRUE 
            ORDER BY created_at ASC LIMIT 1
        ");
        $stmt->execute([$this->customer['id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
    
    /**
     * Gibt das Dashboard-Layout zurück (für externe Nutzung)
     */
    public function getLayout()
    {
        return $this->dashboardLayout;
    }
    
    /**
     * Prüft ob ein Feature verfügbar ist
     */
    public function isFeatureAvailable($feature)
    {
        return $this->layoutService->isFeatureAvailable($feature, $this->customer['plan']);
    }
}
