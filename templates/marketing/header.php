<?php
/**
 * Header Template
 * Gemeinsamer Header für alle Marketing-Seiten
 * Mit Dark Mode Toggle
 */

// Aktuelle Seite für Navigation
$currentPage = $currentPage ?? '';

// Theme aus Cookie
$theme = $_COOKIE['site_theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="de" class="<?= $theme === 'dark' ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($metaDescription ?? 'Vollautomatisches Empfehlungsprogramm für Ihr Unternehmen. Kunden werben Kunden und werden automatisch belohnt.') ?>">
    <meta name="keywords" content="Empfehlungsprogramm, Referral Marketing, Kunden werben Kunden, Empfehlungsmarketing, Viral Marketing">
    <meta name="author" content="Leadbusiness">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle ?? 'Leadbusiness - Empfehlungsprogramm') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($metaDescription ?? 'Vollautomatisches Empfehlungsprogramm für Ihr Unternehmen.') ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl ?? 'https://empfehlungen.cloud') ?>">
    <meta property="og:image" content="<?= htmlspecialchars($ogImage ?? 'https://empfehlungen.cloud/assets/images/og-image.jpg') ?>">
    <meta property="og:locale" content="de_DE">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle ?? 'Leadbusiness - Empfehlungsprogramm') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($metaDescription ?? 'Vollautomatisches Empfehlungsprogramm für Ihr Unternehmen.') ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon-16x16.png">
    <link rel="apple-touch-icon" href="/assets/images/apple-touch-icon.png">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl ?? 'https://empfehlungen.cloud') ?>">
    
    <title><?= htmlspecialchars($pageTitle ?? 'Leadbusiness') ?> | Empfehlungsprogramm</title>
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#667eea',
                            600: '#5a67d8',
                            700: '#4c51bf',
                            800: '#434190',
                            900: '#3c366b',
                        },
                        secondary: '#764ba2',
                        accent: '#f093fb',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <style>
        /* Dark Mode Styles */
        .dark body { background-color: #0f172a; color: #e2e8f0; }
        .dark .bg-white { background-color: #1e293b; }
        .dark .bg-gray-50 { background-color: #0f172a; }
        .dark .bg-gray-100 { background-color: #1e293b; }
        .dark .text-gray-900 { color: #f1f5f9; }
        .dark .text-gray-800 { color: #e2e8f0; }
        .dark .text-gray-700 { color: #cbd5e1; }
        .dark .text-gray-600 { color: #94a3b8; }
        .dark .text-gray-500 { color: #64748b; }
        .dark .border-gray-200 { border-color: #334155; }
        .dark .border-gray-300 { border-color: #475569; }
        .dark .hover\:bg-gray-100:hover { background-color: #334155; }
        .dark .hover\:bg-gray-50:hover { background-color: #1e293b; }
        .dark #header { background-color: #1e293b; border-color: #334155; }
        .dark .shadow-lg { box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3); }
        
        /* Header Navigation - Dark Mode Fix */
        .dark .nav-link {
            color: #e2e8f0 !important;
        }
        .dark .nav-link:hover {
            color: #a5b4fc !important;
        }
        .dark .nav-link.active {
            color: #a5b4fc !important;
        }
        
        /* Header CTA Button */
        .header-cta {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.5rem 1.25rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }
        .header-cta:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        /* Header Login Button - Light Mode */
        .header-login {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            color: #4b5563;
            background-color: #f3f4f6;
        }
        .header-login:hover {
            background-color: #e5e7eb;
            color: #1f2937;
        }
        
        /* Header Login Button - Dark Mode */
        .dark .header-login {
            color: #e2e8f0;
            background-color: #334155;
        }
        .dark .header-login:hover {
            background-color: #475569;
            color: #fff;
        }
    </style>
    
    <?php if (isset($additionalHead)): ?>
        <?= $additionalHead ?>
    <?php endif; ?>
</head>
<body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-white dark:bg-slate-900 transition-colors duration-300">
    
    <!-- Header -->
    <header id="header" class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-slate-800 border-b border-gray-100 dark:border-slate-700 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                
                <!-- Logo -->
                <a href="/" class="flex items-center space-x-3">
                    <div class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center">
                        <i class="fas fa-paper-plane text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900 dark:text-white">Lead<span class="text-primary-500 dark:text-primary-400">business</span></span>
                </a>
                
                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="/funktionen" class="nav-link font-medium transition-colors <?= $currentPage === 'funktionen' ? 'active text-primary-500 dark:text-primary-400' : 'text-gray-600 dark:text-gray-200 hover:text-primary-500 dark:hover:text-primary-400' ?>">
                        Funktionen
                    </a>
                    <a href="/preise" class="nav-link font-medium transition-colors <?= $currentPage === 'preise' ? 'active text-primary-500 dark:text-primary-400' : 'text-gray-600 dark:text-gray-200 hover:text-primary-500 dark:hover:text-primary-400' ?>">
                        Preise
                    </a>
                    <a href="/faq" class="nav-link font-medium transition-colors <?= $currentPage === 'faq' ? 'active text-primary-500 dark:text-primary-400' : 'text-gray-600 dark:text-gray-200 hover:text-primary-500 dark:hover:text-primary-400' ?>">
                        FAQ
                    </a>
                    <a href="/kontakt" class="nav-link font-medium transition-colors <?= $currentPage === 'kontakt' ? 'active text-primary-500 dark:text-primary-400' : 'text-gray-600 dark:text-gray-200 hover:text-primary-500 dark:hover:text-primary-400' ?>">
                        Kontakt
                    </a>
                </nav>
                
                <!-- Right Side: Theme Toggle + Login + CTA -->
                <div class="hidden md:flex items-center space-x-3">
                    <!-- Dark Mode Toggle -->
                    <button onclick="toggleTheme()" 
                            class="p-2 rounded-lg bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-yellow-400 hover:bg-gray-200 dark:hover:bg-slate-600 transition-all"
                            title="Design wechseln">
                        <i class="fas fa-moon dark:hidden"></i>
                        <i class="fas fa-sun hidden dark:inline"></i>
                    </button>
                    
                    <!-- Login Button -->
                    <a href="/admin/login.php" class="header-login">
                        <i class="fas fa-user mr-1.5"></i>Login
                    </a>
                    
                    <!-- CTA Button -->
                    <a href="/onboarding" class="header-cta inline-flex items-center gap-1.5">
                        <span>Kostenlos starten</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
                
                <!-- Mobile Menu Button -->
                <div class="flex items-center gap-2 md:hidden">
                    <!-- Mobile Dark Mode Toggle -->
                    <button onclick="toggleTheme()" 
                            class="p-2 rounded-lg bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-yellow-400"
                            title="Design wechseln">
                        <i class="fas fa-moon dark:hidden"></i>
                        <i class="fas fa-sun hidden dark:inline"></i>
                    </button>
                    
                    <button id="mobile-menu-btn" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <i class="fas fa-bars text-2xl text-gray-700 dark:text-gray-200"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mobile-menu fixed inset-0 z-50 bg-white dark:bg-slate-900">
        <div class="flex flex-col h-full">
            <!-- Mobile Header -->
            <div class="flex justify-between items-center h-20 px-4 border-b dark:border-slate-700">
                <a href="/" class="flex items-center space-x-3">
                    <div class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center">
                        <i class="fas fa-paper-plane text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900 dark:text-white">Lead<span class="text-primary-500 dark:text-primary-400">business</span></span>
                </a>
                <button id="mobile-menu-close" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700">
                    <i class="fas fa-times text-2xl text-gray-700 dark:text-gray-200"></i>
                </button>
            </div>
            
            <!-- Mobile Navigation -->
            <nav class="flex-1 overflow-y-auto py-6 px-4">
                <div class="space-y-2">
                    <a href="/" class="block py-3 px-4 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 font-medium text-lg text-gray-800 dark:text-gray-100">
                        <i class="fas fa-home w-8 text-primary-500 dark:text-primary-400"></i> Startseite
                    </a>
                    <a href="/funktionen" class="block py-3 px-4 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 font-medium text-lg text-gray-800 dark:text-gray-100 <?= $currentPage === 'funktionen' ? 'bg-primary-50 dark:bg-primary-900/30' : '' ?>">
                        <i class="fas fa-star w-8 text-primary-500 dark:text-primary-400"></i> Funktionen
                    </a>
                    <a href="/preise" class="block py-3 px-4 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 font-medium text-lg text-gray-800 dark:text-gray-100 <?= $currentPage === 'preise' ? 'bg-primary-50 dark:bg-primary-900/30' : '' ?>">
                        <i class="fas fa-tags w-8 text-primary-500 dark:text-primary-400"></i> Preise
                    </a>
                    <a href="/faq" class="block py-3 px-4 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 font-medium text-lg text-gray-800 dark:text-gray-100 <?= $currentPage === 'faq' ? 'bg-primary-50 dark:bg-primary-900/30' : '' ?>">
                        <i class="fas fa-question-circle w-8 text-primary-500 dark:text-primary-400"></i> FAQ
                    </a>
                    <a href="/kontakt" class="block py-3 px-4 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 font-medium text-lg text-gray-800 dark:text-gray-100 <?= $currentPage === 'kontakt' ? 'bg-primary-50 dark:bg-primary-900/30' : '' ?>">
                        <i class="fas fa-envelope w-8 text-primary-500 dark:text-primary-400"></i> Kontakt
                    </a>
                </div>
                
                <div class="border-t dark:border-slate-700 mt-6 pt-6 space-y-3">
                    <a href="/admin/login.php" class="block py-3 px-4 rounded-lg bg-gray-100 dark:bg-slate-800 text-center font-semibold text-gray-800 dark:text-gray-100">
                        <i class="fas fa-user mr-2"></i>Login
                    </a>
                    <a href="/onboarding" class="block py-3 px-4 rounded-xl header-cta text-center">
                        <i class="fas fa-rocket mr-2"></i>Kostenlos starten
                    </a>
                </div>
            </nav>
        </div>
    </div>
    
    <!-- Theme Toggle Script -->
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            
            if (isDark) {
                html.classList.remove('dark');
                document.cookie = 'site_theme=light;path=/;max-age=31536000';
            } else {
                html.classList.add('dark');
                document.cookie = 'site_theme=dark;path=/;max-age=31536000';
            }
        }
    </script>
    
    <!-- Main Content -->
    <main class="pt-20">
