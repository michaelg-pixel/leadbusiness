<?php
/**
 * Leadbusiness - Kunden-Dashboard
 * Branchenspezifisches, modulares Dashboard mit Tarif-Unterstützung
 * 
 * Dashboard-Varianten:
 * - offline_local: QR-Code fokussiert (Handwerker, Friseur, Zahnarzt)
 * - online_business: Link fokussiert (Coach, Online-Shop, Newsletter)
 * - hybrid: Beides (Agentur, Sonstiges)
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/settings.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/SetupWizard.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/services/DashboardLayoutService.php';

use Leadbusiness\Auth;
use Leadbusiness\Database;
use Leadbusiness\SetupWizard;

// Auth prüfen
$auth = new Auth();
if (!$auth->isLoggedIn() || $auth->getUserType() !== 'customer') {
    redirect('/dashboard/login.php');
}

$customer = $auth->getCurrentCustomer();
$customerId = $customer['id'];
$db = Database::getInstance();
$pdo = $db->getPdo();

// Dashboard-Layout basierend auf Branche + Tarif laden
$layoutService = new DashboardLayoutService($pdo);
$dashboardLayout = $layoutService->getCustomerDashboard($customerId);

// Setup-Wizard initialisieren
$setupWizard = new SetupWizard($customer);

// Kampagne laden
$campaign = $db->fetch(
    "SELECT * FROM campaigns WHERE customer_id = ? AND is_active = TRUE ORDER BY created_at ASC LIMIT 1",
    [$customerId]
) ?: [];

// Statistiken laden
$stats = $db->fetch(
    "SELECT 
        (SELECT COUNT(*) FROM leads WHERE customer_id = ? AND status IN ('active', 'pending')) as total_leads,
        (SELECT COUNT(*) FROM leads WHERE customer_id = ? AND status = 'active') as active_leads,
        (SELECT COALESCE(SUM(conversions), 0) FROM leads WHERE customer_id = ?) as total_conversions,
        (SELECT COALESCE(SUM(clicks), 0) FROM leads WHERE customer_id = ?) as total_clicks,
        (SELECT COUNT(*) FROM leads WHERE customer_id = ? AND DATE(created_at) = CURDATE()) as leads_today,
        (SELECT COUNT(*) FROM conversions c 
         JOIN leads l ON c.lead_id = l.id 
         WHERE l.customer_id = ? AND c.status = 'confirmed' AND DATE(c.created_at) = CURDATE()) as conversions_today
    ",
    [$customerId, $customerId, $customerId, $customerId, $customerId, $customerId]
);

// Letzte Aktivitäten
$recentActivity = $db->fetchAll(
    "SELECT 'lead' as type, l.name, l.email, l.created_at, 'Neuer Empfehler' as action
     FROM leads l WHERE l.customer_id = ?
     UNION ALL
     SELECT 'conversion' as type, l.name, l.email, c.created_at, 'Erfolgreiche Empfehlung' as action
     FROM conversions c JOIN leads l ON c.lead_id = l.id
     WHERE l.customer_id = ? AND c.status = 'confirmed'
     ORDER BY created_at DESC LIMIT 8",
    [$customerId, $customerId]
);

// Top Empfehler
$topLeads = $db->fetchAll(
    "SELECT name, email, conversions, clicks FROM leads
     WHERE customer_id = ? AND status = 'active' AND conversions > 0
     ORDER BY conversions DESC LIMIT 5",
    [$customerId]
);

$isNewCustomer = isset($_GET['welcome']);
$pageTitle = 'Übersicht';

// Branchenspezifische Variablen für Templates
$businessType = $dashboardLayout['business_type'] ?? 'hybrid';
$customerTerm = $dashboardLayout['customer_term'] ?? 'Kunden';
$industryIcon = $dashboardLayout['industry_icon'] ?? 'fas fa-briefcase';
$industryColor = $dashboardLayout['industry_color'] ?? '#6366f1';
$primaryModule = $dashboardLayout['primary_module'] ?? 'referral_link';

include __DIR__ . '/../../includes/dashboard-header.php';
?>

<?php if ($isNewCustomer): ?>
<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-6 text-white mb-6">
    <div class="flex items-start gap-4">
        <div class="text-4xl">🎉</div>
        <div>
            <h2 class="text-xl font-bold mb-2">Herzlichen Glückwunsch!</h2>
            <p class="text-white/90 mb-4">
                Ihr Empfehlungsprogramm ist bereit. 
                <?php if ($businessType === 'offline'): ?>
                    Drucken Sie den QR-Code aus und zeigen Sie ihn Ihren <?= e($customerTerm) ?>!
                <?php else: ?>
                    Teilen Sie Ihren Empfehlungslink mit Ihren <?= e($customerTerm) ?>!
                <?php endif; ?>
            </p>
            <div class="flex items-center gap-2 bg-white/20 rounded-lg px-4 py-2 inline-flex">
                <code class="text-sm"><?= e($customer['subdomain']) ?>.empfehlungen.cloud</code>
                <button onclick="copyToClipboard('https://<?= e($customer['subdomain']) ?>.empfehlungen.cloud', this)" class="text-white/80 hover:text-white">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php 
// Setup-Wizard anzeigen
if (!$setupWizard->isHidden() || !$setupWizard->isSetupComplete()):
    include __DIR__ . '/../../includes/components/setup-wizard-widget.php';
endif;
?>

<!-- Dashboard Header mit Quick Actions -->
<div class="dashboard-header mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <!-- Titel & Branche -->
        <div class="header-left flex items-center gap-4">
            <div class="industry-icon w-14 h-14 rounded-xl flex items-center justify-center"
                 style="background-color: <?= e($industryColor) ?>20">
                <i class="<?= e($industryIcon) ?> text-2xl" style="color: <?= e($industryColor) ?>"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">
                    <?= e($customer['company_name']) ?>
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    <?= e($dashboardLayout['welcome_text'] ?? 'Willkommen in Ihrem Empfehlungsprogramm') ?>
                </p>
            </div>
        </div>
        
        <!-- Quick Actions basierend auf Business-Typ -->
        <div class="header-actions flex flex-wrap gap-2">
            <?php if ($businessType === 'offline' || $businessType === 'hybrid'): ?>
                <button onclick="downloadQR()" class="btn btn-primary">
                    <i class="fas fa-qrcode mr-2"></i>
                    QR-Code
                </button>
            <?php endif; ?>
            
            <?php if ($businessType === 'online' || $businessType === 'hybrid'): ?>
                <button onclick="copyMainLink()" id="copyMainLinkBtn" class="btn <?= $businessType === 'online' ? 'btn-primary' : 'btn-outline' ?>">
                    <i class="fas fa-copy mr-2"></i>
                    Link kopieren
                </button>
            <?php endif; ?>
            
            <a href="/dashboard/share.php" class="btn btn-outline">
                <i class="fas fa-share-alt mr-2"></i>
                Teilen
            </a>
            
            <?php if ($customer['plan'] !== 'starter'): ?>
                <a href="/dashboard/leads.php" class="btn btn-outline">
                    <i class="fas fa-users mr-2"></i>
                    Empfehler
                </a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Plan Badge -->
    <div class="mt-4 flex items-center gap-3">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
            <?php 
            switch($customer['plan']) {
                case 'enterprise': echo 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300'; break;
                case 'professional': echo 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'; break;
                default: echo 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300';
            }
            ?>">
            <i class="fas fa-<?= $customer['plan'] === 'starter' ? 'seedling' : ($customer['plan'] === 'professional' ? 'star' : 'crown') ?> mr-1"></i>
            <?= ucfirst($customer['plan']) ?>
        </span>
        
        <?php if ($customer['plan'] === 'starter'): ?>
            <a href="/dashboard/upgrade.php" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">
                <i class="fas fa-arrow-up mr-1"></i>Upgrade für mehr Funktionen
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- PRIMÄRES MODUL (branchenspezifisch) -->
<div class="primary-module-section mb-8">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <?php
        // Primäres Modul basierend auf Layout laden
        $primaryModulePath = __DIR__ . "/../../includes/components/dashboard-modules/{$primaryModule}.php";
        if (file_exists($primaryModulePath)) {
            include $primaryModulePath;
        } else {
            // Fallback: Referral Link
            include __DIR__ . "/../../includes/components/dashboard-modules/referral_link.php";
        }
        ?>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-700 hover:-translate-y-1 transition-transform">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                <i class="fas fa-users text-blue-500 dark:text-blue-400"></i>
            </div>
            <?php if ($stats['leads_today'] > 0): ?>
            <span class="px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs font-medium rounded-full">
                +<?= $stats['leads_today'] ?>
            </span>
            <?php endif; ?>
        </div>
        <div class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['total_leads'] ?? 0, 0, ',', '.') ?></div>
        <div class="text-sm text-slate-500 dark:text-slate-400">Empfehler</div>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-700 hover:-translate-y-1 transition-transform">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                <i class="fas fa-user-check text-green-500 dark:text-green-400"></i>
            </div>
            <?php if ($stats['conversions_today'] > 0): ?>
            <span class="px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs font-medium rounded-full">
                +<?= $stats['conversions_today'] ?>
            </span>
            <?php endif; ?>
        </div>
        <div class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['total_conversions'] ?? 0, 0, ',', '.') ?></div>
        <div class="text-sm text-slate-500 dark:text-slate-400">Neue <?= e($customerTerm) ?></div>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-700 hover:-translate-y-1 transition-transform">
        <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center mb-3">
            <i class="fas fa-mouse-pointer text-amber-500 dark:text-amber-400"></i>
        </div>
        <div class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['total_clicks'] ?? 0, 0, ',', '.') ?></div>
        <div class="text-sm text-slate-500 dark:text-slate-400">Link-Klicks</div>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-700 hover:-translate-y-1 transition-transform">
        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-3">
            <i class="fas fa-percentage text-purple-500 dark:text-purple-400"></i>
        </div>
        <?php $conversionRate = $stats['total_clicks'] > 0 ? round(($stats['total_conversions'] / $stats['total_clicks']) * 100, 1) : 0; ?>
        <div class="text-2xl font-bold text-slate-800 dark:text-white"><?= $conversionRate ?>%</div>
        <div class="text-sm text-slate-500 dark:text-slate-400">Conversion</div>
    </div>
</div>

<!-- Sekundäre Module Grid -->
<div class="grid lg:grid-cols-2 gap-6 mb-8">
    
    <!-- Quick Share Modul -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <?php include __DIR__ . "/../../includes/components/dashboard-modules/quick_share.php"; ?>
    </div>
    
    <!-- Belohnungen Modul -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <?php include __DIR__ . "/../../includes/components/dashboard-modules/rewards_overview.php"; ?>
    </div>
</div>

<!-- Activity & Top Leads -->
<div class="grid lg:grid-cols-3 gap-6 mb-8">
    
    <!-- Letzte Aktivitäten -->
    <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">
            <i class="fas fa-clock text-primary-500 mr-2"></i>Letzte Aktivitäten
        </h3>
        
        <?php if (empty($recentActivity)): ?>
        <div class="text-center py-8">
            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-share-alt text-slate-400 dark:text-slate-500 text-2xl"></i>
            </div>
            <p class="text-slate-500 dark:text-slate-400 text-sm mb-4">Noch keine Aktivitäten.</p>
            <?php if ($businessType === 'offline'): ?>
            <p class="text-sm text-slate-600 dark:text-slate-300">
                Drucken Sie Ihren QR-Code aus und zeigen Sie ihn Ihren <?= e($customerTerm) ?>!
            </p>
            <?php else: ?>
            <a href="/dashboard/share.php" class="text-primary-600 dark:text-primary-400 text-sm font-medium hover:underline">
                Jetzt Empfehlungslink teilen →
            </a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($recentActivity as $activity): ?>
            <div class="flex items-center gap-4 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                <div class="w-10 h-10 rounded-full flex items-center justify-center
                    <?= $activity['type'] === 'conversion' ? 'bg-green-100 dark:bg-green-900/30 text-green-500 dark:text-green-400' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-500 dark:text-blue-400' ?>">
                    <i class="fas <?= $activity['type'] === 'conversion' ? 'fa-check' : 'fa-user-plus' ?>"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-slate-800 dark:text-white truncate"><?= e($activity['action']) ?></div>
                    <div class="text-sm text-slate-500 dark:text-slate-400 truncate">
                        <?= e($activity['name'] ?: $activity['email']) ?> · <?= timeAgo($activity['created_at']) ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Top Empfehler -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">
            <i class="fas fa-trophy text-amber-500 mr-2"></i>Top Empfehler
        </h3>
        
        <?php if (empty($topLeads)): ?>
        <div class="text-center py-6">
            <div class="w-14 h-14 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-medal text-slate-400 dark:text-slate-500 text-xl"></i>
            </div>
            <p class="text-slate-500 dark:text-slate-400 text-sm">
                Noch keine aktiven Empfehler.
            </p>
        </div>
        <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($topLeads as $index => $lead): ?>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                    <?= $index === 0 ? 'bg-amber-400 text-white' : ($index === 1 ? 'bg-slate-300 text-slate-700' : 'bg-amber-700 text-white') ?>">
                    <?= $index + 1 ?>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-slate-800 dark:text-white truncate">
                        <?= e($lead['name'] ?: 'Anonymer Empfehler') ?>
                    </div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">
                        <?= $lead['conversions'] ?> <?= $lead['conversions'] == 1 ? 'Empfehlung' : 'Empfehlungen' ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($customer['plan'] !== 'starter'): ?>
        <a href="/dashboard/leads.php" class="mt-4 text-primary-600 dark:text-primary-400 text-sm font-medium hover:underline inline-block">
            Alle Empfehler ansehen →
        </a>
        <?php else: ?>
        <div class="mt-4 text-sm text-slate-500 dark:text-slate-400">
            <i class="fas fa-lock text-xs mr-1"></i>
            <a href="/dashboard/upgrade.php" class="text-primary-600 dark:text-primary-400 hover:underline">
                Upgrade für vollständige Liste
            </a>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- ============================================= -->
<!-- PROFESSIONAL FEATURES SECTION                 -->
<!-- Nur für Professional & Enterprise Kunden      -->
<!-- ============================================= -->
<?php if ($customer['plan'] !== 'starter'): ?>
<div class="professional-features-section mb-8">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
            <i class="fas fa-star text-blue-500"></i>
            Professional Features
        </h2>
        <span class="text-xs text-slate-500 dark:text-slate-400">
            Exklusiv für <?= ucfirst($customer['plan']) ?>
        </span>
    </div>
    
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        
        <!-- Broadcast E-Mails -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <?php 
            $broadcastModulePath = __DIR__ . "/../../includes/components/dashboard-modules/broadcast.php";
            if (file_exists($broadcastModulePath)) {
                include $broadcastModulePath;
            } else {
                // Fallback wenn Modul noch nicht existiert
                ?>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">
                        <i class="fas fa-paper-plane text-primary-500 mr-2"></i>
                        Broadcast E-Mails
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                        Senden Sie E-Mails an alle Ihre Empfehler.
                    </p>
                    <a href="/dashboard/broadcasts.php" class="btn btn-primary w-full">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Broadcasts verwalten
                    </a>
                </div>
                <?php
            }
            ?>
        </div>
        
        <!-- Website Widget -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <?php 
            $widgetModulePath = __DIR__ . "/../../includes/components/dashboard-modules/website_widget.php";
            if (file_exists($widgetModulePath)) {
                include $widgetModulePath;
            } else {
                ?>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">
                        <i class="fas fa-code text-primary-500 mr-2"></i>
                        Website-Widget
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                        Binden Sie das Empfehlungsprogramm auf Ihrer Website ein.
                    </p>
                    <a href="/dashboard/api.php#widget" class="btn btn-primary w-full">
                        <i class="fas fa-code mr-2"></i>
                        Widget einrichten
                    </a>
                </div>
                <?php
            }
            ?>
        </div>
        
        <!-- API & Webhooks -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <?php 
            $apiModulePath = __DIR__ . "/../../includes/components/dashboard-modules/api_access.php";
            if (file_exists($apiModulePath)) {
                include $apiModulePath;
            } else {
                ?>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">
                        <i class="fas fa-plug text-primary-500 mr-2"></i>
                        API & Webhooks
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                        Integrieren Sie Leadbusiness in Ihre Systeme.
                    </p>
                    <a href="/dashboard/api.php" class="btn btn-primary w-full">
                        <i class="fas fa-book mr-2"></i>
                        API-Dokumentation
                    </a>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    
    <!-- E-Mail-Vorlagen (volle Breite - nur für Online-Businesses) -->
    <?php if ($businessType === 'online' || $businessType === 'hybrid'): ?>
    <div class="mt-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <?php 
            $emailTemplatesPath = __DIR__ . "/../../includes/components/dashboard-modules/email_templates.php";
            if (file_exists($emailTemplatesPath)) {
                include $emailTemplatesPath;
            } else {
                ?>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">
                        <i class="fas fa-envelope text-primary-500 mr-2"></i>
                        E-Mail-Vorlagen
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Fertige Texte zum Kopieren und per E-Mail versenden.
                    </p>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Weitere Professional Links -->
    <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3">
        <a href="/dashboard/leads.php?export=1" class="flex items-center gap-2 p-3 bg-slate-100 dark:bg-slate-700/50 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors text-sm text-slate-700 dark:text-slate-300">
            <i class="fas fa-download text-green-500"></i>
            Lead-Export
        </a>
        <a href="/dashboard/webhooks.php" class="flex items-center gap-2 p-3 bg-slate-100 dark:bg-slate-700/50 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors text-sm text-slate-700 dark:text-slate-300">
            <i class="fas fa-bolt text-purple-500"></i>
            Webhooks
        </a>
        <a href="/dashboard/whitelabel.php" class="flex items-center gap-2 p-3 bg-slate-100 dark:bg-slate-700/50 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors text-sm text-slate-700 dark:text-slate-300">
            <i class="fas fa-paint-brush text-pink-500"></i>
            Whitelabel
        </a>
        <a href="/dashboard/domain.php" class="flex items-center gap-2 p-3 bg-slate-100 dark:bg-slate-700/50 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors text-sm text-slate-700 dark:text-slate-300">
            <i class="fas fa-globe text-blue-500"></i>
            Eigene Domain
        </a>
    </div>
</div>
<?php endif; ?>

<!-- Upgrade-Hinweis für Starter -->
<?php if ($customer['plan'] === 'starter'): ?>
<div class="upgrade-cta bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl p-6 border border-blue-200 dark:border-blue-800 mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1">
                <i class="fas fa-rocket text-blue-500 mr-2"></i>
                Mehr Funktionen freischalten
            </h3>
            <p class="text-sm text-slate-600 dark:text-slate-300 mb-3">
                <?php if ($businessType === 'offline'): ?>
                    Mit Professional: Druckvorlagen, Empfehler-Liste, detaillierte Statistiken und mehr.
                <?php else: ?>
                    Mit Professional: E-Mail-Vorlagen, Website-Widget, detaillierte Analytics und mehr.
                <?php endif; ?>
            </p>
            <!-- Feature Preview -->
            <div class="flex flex-wrap gap-2">
                <?php 
                $proFeatures = ['Broadcast E-Mails', 'Website-Widget', 'API & Webhooks', 'Lead-Export', 'Eigene Domain'];
                foreach (array_slice($proFeatures, 0, 4) as $feature): 
                ?>
                <span class="inline-flex items-center px-2 py-1 bg-white/50 dark:bg-slate-800/50 rounded text-xs text-slate-600 dark:text-slate-300">
                    <i class="fas fa-lock text-slate-400 mr-1"></i>
                    <?= $feature ?>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
        <a href="/dashboard/upgrade.php" class="btn btn-primary whitespace-nowrap">
            <i class="fas fa-arrow-up mr-2"></i>
            Jetzt upgraden
        </a>
    </div>
</div>
<?php endif; ?>

<script>
    const referralUrl = 'https://<?= e($customer['subdomain']) ?>.empfehlungen.cloud';
    
    // QR-Code Download
    function downloadQR() {
        const downloadUrl = `/api/qr-code.php?url=${encodeURIComponent(referralUrl)}&size=1000&format=png&download=1`;
        const a = document.createElement('a');
        a.href = downloadUrl;
        a.download = 'qr-code-<?= e($customer['subdomain']) ?>.png';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        
        // Track
        trackAction('qr_download', { source: 'header_button' });
    }
    
    // Link kopieren
    function copyMainLink() {
        navigator.clipboard.writeText(referralUrl).then(() => {
            const btn = document.getElementById('copyMainLinkBtn');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check mr-2"></i>Kopiert!';
            btn.classList.add('btn-success');
            btn.classList.remove('btn-primary', 'btn-outline');
            
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.classList.remove('btn-success');
                btn.classList.add('<?= $businessType === 'online' ? 'btn-primary' : 'btn-outline' ?>');
            }, 2000);
            
            trackAction('link_copy', { source: 'header_button' });
        });
    }
    
    // Allgemeiner Copy-Helper
    function copyToClipboard(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const icon = btn.querySelector('i');
            if (icon) {
                icon.className = 'fas fa-check';
                setTimeout(() => { icon.className = 'fas fa-copy'; }, 2000);
            }
        });
    }
    
    // Analytics Tracking
    function trackAction(action, data = {}) {
        fetch('/api/track-event.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                customer_id: <?= (int)$customerId ?>,
                action: action,
                ...data
            })
        }).catch(() => {});
    }
    
    // Toast Funktion (falls nicht global vorhanden)
    if (typeof window.showToast !== 'function') {
        window.showToast = function(message, type = 'info') {
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-amber-500',
                info: 'bg-blue-500'
            };
            
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 ${colors[type] || colors.info} text-white px-4 py-2 rounded-lg shadow-lg z-50 animate-fade-in`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('animate-fade-out');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        };
    }
</script>

<?php include __DIR__ . '/../../includes/dashboard-footer.php'; ?>
