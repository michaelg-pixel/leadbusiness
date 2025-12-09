<?php
/**
 * Admin Header Component
 * Mit Dark/Light Mode Toggle
 */

// Theme aus Cookie
$theme = $_COOKIE['admin_theme'] ?? 'light';

// Aktuelle Seite für Navigation
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Benachrichtigungen zählen (z.B. Fraud Reviews)
$pendingFraudCount = db()->fetchColumn(
    "SELECT COUNT(*) FROM fraud_log WHERE action_taken = 'review' AND reviewed_at IS NULL"
) ?? 0;
?>
<!DOCTYPE html>
<html lang="de" class="<?= $theme === 'dark' ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin' ?> - Leadbusiness</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <style>
        .sidebar-item.active {
            background: linear-gradient(90deg, rgba(14, 165, 233, 0.2) 0%, transparent 100%);
            border-left: 3px solid #0ea5e9;
        }
        .sidebar-item:hover:not(.active) {
            background: rgba(255, 255, 255, 0.05);
        }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #475569; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #64748b; }
    </style>
</head>
<body class="bg-slate-100 dark:bg-slate-900 min-h-screen">
    
    <!-- Mobile Menu Button -->
    <button onclick="toggleSidebar()" class="lg:hidden fixed top-4 left-4 z-50 p-2 bg-white dark:bg-slate-800 rounded-lg shadow-lg">
        <i class="fas fa-bars text-slate-600 dark:text-slate-300"></i>
    </button>
    
    <!-- Sidebar -->
    <aside id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-slate-800 dark:bg-slate-950 text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-300 z-40">
        
        <!-- Logo -->
        <div class="p-6 border-b border-slate-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shield-halved text-white"></i>
                </div>
                <div>
                    <h1 class="font-bold text-lg">Leadbusiness</h1>
                    <p class="text-xs text-slate-400">Admin Panel</p>
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <nav class="p-4 space-y-1">
            <a href="/admin/" class="sidebar-item <?= $currentPage === 'index' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:text-white transition-all">
                <i class="fas fa-chart-pie w-5"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="/admin/customers.php" class="sidebar-item <?= $currentPage === 'customers' || $currentPage === 'customer-detail' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:text-white transition-all">
                <i class="fas fa-building w-5"></i>
                <span>Kunden</span>
            </a>
            
            <a href="/admin/fraud-review.php" class="sidebar-item <?= $currentPage === 'fraud-review' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:text-white transition-all">
                <i class="fas fa-shield-exclamation w-5"></i>
                <span>Fraud Review</span>
                <?php if ($pendingFraudCount > 0): ?>
                <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full"><?= $pendingFraudCount ?></span>
                <?php endif; ?>
            </a>
            
            <a href="/admin/backgrounds.php" class="sidebar-item <?= $currentPage === 'backgrounds' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:text-white transition-all">
                <i class="fas fa-image w-5"></i>
                <span>Hintergrundbilder</span>
            </a>
            
            <a href="/admin/logs.php" class="sidebar-item <?= $currentPage === 'logs' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:text-white transition-all">
                <i class="fas fa-file-lines w-5"></i>
                <span>System Logs</span>
            </a>
            
            <a href="/admin/settings.php" class="sidebar-item <?= $currentPage === 'settings' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:text-white transition-all">
                <i class="fas fa-cog w-5"></i>
                <span>Einstellungen</span>
            </a>
            
            <div class="border-t border-slate-700 my-4"></div>
            
            <a href="/" target="_blank" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:text-white transition-all">
                <i class="fas fa-external-link w-5"></i>
                <span>Website öffnen</span>
            </a>
        </nav>
        
        <!-- User Info -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-700 bg-slate-900/50">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-primary-600 rounded-full flex items-center justify-center">
                    <span class="text-white font-medium">
                        <?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?>
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate"><?= e($_SESSION['admin_name'] ?? 'Admin') ?></p>
                    <p class="text-xs text-slate-400 truncate"><?= e($_SESSION['admin_email'] ?? '') ?></p>
                </div>
            </div>
            <a href="/admin/logout.php" class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-slate-700 hover:bg-slate-600 rounded-lg text-sm transition-all">
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
                    <?= $pageTitle ?? 'Dashboard' ?>
                </h1>
                
                <div class="flex items-center gap-3">
                    <!-- Theme Toggle -->
                    <button onclick="toggleTheme()" 
                            class="p-2 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition-all"
                            title="Design wechseln">
                        <i class="fas fa-moon dark:hidden w-5 h-5"></i>
                        <i class="fas fa-sun hidden dark:inline w-5 h-5"></i>
                    </button>
                    
                    <!-- Notifications -->
                    <?php if ($pendingFraudCount > 0): ?>
                    <a href="/admin/fraud-review.php" 
                       class="relative p-2 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition-all">
                        <i class="fas fa-bell"></i>
                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                            <?= $pendingFraudCount ?>
                        </span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </header>
        
        <!-- Page Content -->
        <div class="p-4 lg:p-8">
