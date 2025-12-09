<?php
/**
 * Header Template
 * Gemeinsamer Header für alle Marketing-Seiten
 */

// Aktuelle Seite für Navigation
$currentPage = $currentPage ?? '';
?>
<!DOCTYPE html>
<html lang="de">
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
    <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl ?? 'https://leadbusiness.de') ?>">
    <meta property="og:image" content="<?= htmlspecialchars($ogImage ?? 'https://leadbusiness.de/assets/images/og-image.jpg') ?>">
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
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl ?? 'https://leadbusiness.de') ?>">
    
    <title><?= htmlspecialchars($pageTitle ?? 'Leadbusiness') ?> | Empfehlungsprogramm</title>
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
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
    
    <?php if (isset($additionalHead)): ?>
        <?= $additionalHead ?>
    <?php endif; ?>
</head>
<body class="font-sans antialiased text-gray-900 bg-white">
    
    <!-- Header -->
    <header id="header" class="fixed top-0 left-0 right-0 z-50 bg-white transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                
                <!-- Logo -->
                <a href="/" class="flex items-center space-x-3">
                    <div class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center">
                        <i class="fas fa-paper-plane text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900">Lead<span class="text-primary-500">business</span></span>
                </a>
                
                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="/funktionen" class="text-gray-600 hover:text-primary-500 font-medium transition-colors <?= $currentPage === 'funktionen' ? 'text-primary-500' : '' ?>">
                        Funktionen
                    </a>
                    <a href="/preise" class="text-gray-600 hover:text-primary-500 font-medium transition-colors <?= $currentPage === 'preise' ? 'text-primary-500' : '' ?>">
                        Preise
                    </a>
                    <a href="/faq" class="text-gray-600 hover:text-primary-500 font-medium transition-colors <?= $currentPage === 'faq' ? 'text-primary-500' : '' ?>">
                        FAQ
                    </a>
                    <a href="/kontakt" class="text-gray-600 hover:text-primary-500 font-medium transition-colors <?= $currentPage === 'kontakt' ? 'text-primary-500' : '' ?>">
                        Kontakt
                    </a>
                </nav>
                
                <!-- CTA Buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="/dashboard/login" class="text-gray-600 hover:text-primary-500 font-medium transition-colors">
                        Login
                    </a>
                    <a href="/onboarding" class="btn-primary">
                        Jetzt starten
                    </a>
                </div>
                
                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-bars text-2xl text-gray-700"></i>
                </button>
            </div>
        </div>
    </header>
    
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mobile-menu fixed inset-0 z-50 bg-white">
        <div class="flex flex-col h-full">
            <!-- Mobile Header -->
            <div class="flex justify-between items-center h-20 px-4 border-b">
                <a href="/" class="flex items-center space-x-3">
                    <div class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center">
                        <i class="fas fa-paper-plane text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-bold">Lead<span class="text-primary-500">business</span></span>
                </a>
                <button id="mobile-menu-close" class="p-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-times text-2xl text-gray-700"></i>
                </button>
            </div>
            
            <!-- Mobile Navigation -->
            <nav class="flex-1 overflow-y-auto py-6 px-4">
                <div class="space-y-2">
                    <a href="/" class="block py-3 px-4 rounded-lg hover:bg-gray-50 font-medium text-lg">
                        <i class="fas fa-home w-8 text-primary-500"></i> Startseite
                    </a>
                    <a href="/funktionen" class="block py-3 px-4 rounded-lg hover:bg-gray-50 font-medium text-lg">
                        <i class="fas fa-star w-8 text-primary-500"></i> Funktionen
                    </a>
                    <a href="/preise" class="block py-3 px-4 rounded-lg hover:bg-gray-50 font-medium text-lg">
                        <i class="fas fa-tags w-8 text-primary-500"></i> Preise
                    </a>
                    <a href="/faq" class="block py-3 px-4 rounded-lg hover:bg-gray-50 font-medium text-lg">
                        <i class="fas fa-question-circle w-8 text-primary-500"></i> FAQ
                    </a>
                    <a href="/kontakt" class="block py-3 px-4 rounded-lg hover:bg-gray-50 font-medium text-lg">
                        <i class="fas fa-envelope w-8 text-primary-500"></i> Kontakt
                    </a>
                </div>
                
                <div class="border-t mt-6 pt-6 space-y-4">
                    <a href="/dashboard/login" class="block py-3 px-4 rounded-lg bg-gray-100 text-center font-semibold">
                        Login
                    </a>
                    <a href="/onboarding" class="block py-3 px-4 rounded-lg gradient-bg text-white text-center font-semibold">
                        Jetzt starten
                    </a>
                </div>
            </nav>
        </div>
    </div>
    
    <!-- Main Content -->
    <main class="pt-20">
