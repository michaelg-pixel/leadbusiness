<?php
/**
 * Dashboard Header Component
 * Mit Dark/Light Mode Toggle
 */

// Theme aus Cookie
$theme = $_COOKIE['dashboard_theme'] ?? 'light';

// Aktuelle Seite für Navigation
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Kunde laden falls nicht vorhanden
if (!isset($customer) && isset($_SESSION['customer_id'])) {
    $db = Database::getInstance();
    $customer = $db->fetch("SELECT * FROM customers WHERE id = ?", [$_SESSION['customer_id']]);
}

// Plan-Check für API-Zugang
$hasApiAccess = in_array($customer['plan'] ?? '', ['professional', 'enterprise']);
$isEnterprise = ($customer['plan'] ?? '') === 'enterprise';

// Custom Domain URL ermitteln
$siteUrl = !empty($customer['custom_domain']) && !empty($customer['custom_domain_verified']) && $customer['custom_domain_ssl_status'] === 'active'
    ? 'https://' . $customer['custom_domain']
    : 'https://' . ($customer['subdomain'] ?? '') . '.empfehlungen.cloud';
?>
<!DOCTYPE html>
<html lang="de" class="<?= $theme === 'dark' ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard') ?> - Leadbusiness</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc',
                            400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1',
                            800: '#075985', 900: '#0c4a6e', 950: '#082f49'
                        }
                    }
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .nav-item.active { 
            background: linear-gradient(90deg, rgba(14, 165, 233, 0.15) 0%, transparent 100%);
            border-left: 3px solid #0ea5e9;
        }
        .nav-item.active.enterprise {
            background: linear-gradient(90deg, rgba(168, 85, 247, 0.15) 0%, transparent 100%);
            border-left-color: #a855f7;
        }
        .pro-feature {
            position: relative;
        }
        .pro-badge-nav {
            font-size: 0.5rem;
            padding: 0.125rem 0.375rem;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            border-radius: 9999px;
            margin-left: 0.5rem;
        }
        .enterprise-badge-nav {
            font-size: 0.5rem;
            padding: 0.125rem 0.375rem;
            background: linear-gradient(135deg, #a855f7 0%, #7c3aed 100%);
            color: white;
            border-radius: 9999px;
            margin-left: 0.5rem;
        }
    </style>
</head>
<body class="bg-slate-100 dark:bg-slate-900 min-h-screen">
    
    <!-- Mobile Menu Button -->
    <button onclick="toggleSidebar()" class="lg:hidden fixed top-4 left-4 z-50 p-2 bg-white dark:bg-slate-800 rounded-lg shadow-lg">
        <i class="fas fa-bars text-slate-600 dark:text-slate-300"></i>
    </button>
    
    <!-- Sidebar -->
    <aside id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-white dark:bg-slate-800 shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-300 z-40 flex flex-col">
        
        <!-- Logo -->
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <?php if (!empty($customer['logo_url'])): ?>
                <img src="<?= e($customer['logo_url']) ?>" alt="" class="w-10 h-10 rounded-lg object-cover">
                <?php else: ?>
                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-primary-600 dark:text-primary-400"></i>
                </div>
                <?php endif; ?>
                <div class="min-w-0">
                    <h1 class="font-bold text-slate-800 dark:text-white truncate text-sm"><?= e($customer['company_name'] ?? 'Dashboard') ?></h1>
                    <?php if (!empty($customer['custom_domain']) && !empty($customer['custom_domain_verified'])): ?>
                    <p class="text-xs text-purple-500 truncate"><?= e($customer['custom_domain']) ?></p>
                    <?php else: ?>
                    <p class="text-xs text-slate-500 truncate"><?= e($customer['subdomain'] ?? '') ?>.empfehlungen.cloud</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Navigation - Scrollable -->
        <nav class="p-4 space-y-1 flex-1 overflow-y-auto">
            <a href="/dashboard/" class="nav-item <?= $currentPage === 'index' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                <i class="fas fa-chart-pie w-5 text-center"></i>
                <span>Übersicht</span>
            </a>
            
            <a href="/dashboard/leads.php" class="nav-item <?= $currentPage === 'leads' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                <i class="fas fa-users w-5 text-center"></i>
                <span>Empfehler</span>
            </a>
            
            <a href="/dashboard/rewards.php" class="nav-item <?= $currentPage === 'rewards' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                <i class="fas fa-gift w-5 text-center"></i>
                <span>Belohnungen</span>
            </a>
            
            <a href="/dashboard/design.php" class="nav-item <?= $currentPage === 'design' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                <i class="fas fa-palette w-5 text-center"></i>
                <span>Design</span>
            </a>
            
            <a href="/dashboard/settings.php" class="nav-item <?= $currentPage === 'settings' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                <i class="fas fa-cog w-5 text-center"></i>
                <span>Einstellungen</span>
            </a>
            
            <?php if ($hasApiAccess): ?>
            <div class="border-t border-slate-200 dark:border-slate-700 my-4"></div>
            
            <p class="px-4 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Entwickler</p>
            
            <a href="/dashboard/api.php" class="nav-item <?= $currentPage === 'api' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                <i class="fas fa-code w-5 text-center"></i>
                <span>API-Zugang</span>
                <span class="<?= $isEnterprise ? 'enterprise-badge-nav' : 'pro-badge-nav' ?>"><?= $isEnterprise ? 'ENT' : 'PRO' ?></span>
            </a>
            
            <a href="/dashboard/webhooks.php" class="nav-item <?= $currentPage === 'webhooks' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                <i class="fas fa-bolt w-5 text-center"></i>
                <span>Webhooks</span>
                <span class="<?= $isEnterprise ? 'enterprise-badge-nav' : 'pro-badge-nav' ?>"><?= $isEnterprise ? 'ENT' : 'PRO' ?></span>
            </a>
            <?php endif; ?>
            
            <?php if ($isEnterprise): ?>
            <div class="border-t border-slate-200 dark:border-slate-700 my-4"></div>
            
            <p class="px-4 text-xs font-semibold text-purple-500 dark:text-purple-400 uppercase tracking-wider mb-2">
                <i class="fas fa-crown mr-1"></i>Enterprise
            </p>
            
            <a href="/dashboard/domain.php" class="nav-item <?= $currentPage === 'domain' ? 'active enterprise' : '' ?> flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all">
                <i class="fas fa-globe w-5 text-center text-purple-500"></i>
                <span>Eigene Domain</span>
                <?php if (!empty($customer['custom_domain']) && !empty($customer['custom_domain_verified'])): ?>
                <i class="fas fa-check-circle text-green-500 ml-auto text-xs"></i>
                <?php endif; ?>
            </a>
            
            <a href="/dashboard/whitelabel.php" class="nav-item <?= $currentPage === 'whitelabel' ? 'active enterprise' : '' ?> flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all">
                <i class="fas fa-tag w-5 text-center text-purple-500"></i>
                <span>White-Label</span>
            </a>
            <?php endif; ?>
            
            <div class="border-t border-slate-200 dark:border-slate-700 my-4"></div>
            
            <a href="<?= e($siteUrl) ?>" target="_blank" class="nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                <i class="fas fa-external-link w-5 text-center"></i>
                <span>Empfehlungsseite</span>
            </a>
        </nav>
        
        <!-- User Info - Fixed at bottom -->
        <div class="p-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                    <span class="text-primary-600 dark:text-primary-400 font-medium">
                        <?= strtoupper(substr($customer['contact_name'] ?? 'U', 0, 1)) ?>
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-800 dark:text-white truncate"><?= e($customer['contact_name'] ?? 'Benutzer') ?></p>
                    <p class="text-xs text-slate-500 truncate"><?= e($customer['email'] ?? '') ?></p>
                </div>
            </div>
            <a href="/dashboard/logout.php" class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 rounded-lg text-sm text-slate-700 dark:text-slate-200 transition-all">
                <i class="fas fa-sign-out-alt"></i>
                Abmelden
            </a>
        </div>
    </aside>
    
    <!-- Overlay für Mobile -->
    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-30 lg:hidden hidden"></div>
    
    <!-- Main Content -->
    <main class="lg:ml-64 min-h-screen">
        
        <!-- Top Bar -->
        <header class="sticky top-0 z-20 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-4 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="lg:hidden w-8"></div>
                
                <h1 class="text-xl font-semibold text-slate-800 dark:text-white">
                    <?= e($pageTitle ?? 'Dashboard') ?>
                </h1>
                
                <div class="flex items-center gap-3">
                    <!-- Plan Badge -->
                    <?php
                    $planColors = [
                        'starter' => 'bg-slate-100 text-slate-700 dark:bg-slate-600 dark:text-slate-200',
                        'professional' => 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300',
                        'enterprise' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'
                    ];
                    $planColor = $planColors[$customer['plan'] ?? 'starter'] ?? $planColors['starter'];
                    ?>
                    <span class="hidden sm:inline-flex items-center gap-1 px-3 py-1 text-xs font-medium rounded-full <?= $planColor ?>">
                        <?php if ($isEnterprise): ?><i class="fas fa-crown text-xs"></i><?php endif; ?>
                        <?= ucfirst($customer['plan'] ?? 'Starter') ?>
                    </span>
                    
                    <!-- Theme Toggle -->
                    <button onclick="toggleTheme()" 
                            class="p-2 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition-all"
                            title="Design wechseln">
                        <i class="fas fa-moon dark:hidden"></i>
                        <i class="fas fa-sun hidden dark:inline"></i>
                    </button>
                </div>
            </div>
        </header>
        
        <!-- Page Content -->
        <div class="p-4 lg:p-8">
