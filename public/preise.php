<?php
/**
 * Leadbusiness - Preise
 * Preisübersicht und Plan-Vergleich
 */

$pageTitle = 'Preise';
$metaDescription = 'Transparente Preise für Ihr Empfehlungsprogramm: Starter ab 49€/Monat, Professional ab 99€/Monat. 7 Tage kostenlos testen.';
$currentPage = 'preise';

require_once __DIR__ . '/../templates/marketing/header.php';
?>

<!-- Hero Section -->
<section class="py-20 bg-gradient-to-br from-gray-50 to-white dark:from-slate-800 dark:to-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto">
            <span class="text-primary-500 dark:text-primary-400 font-semibold uppercase tracking-wide">Preise</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mt-3 mb-6 text-gray-900 dark:text-white">
                Einfach & <span class="gradient-text">transparent</span>
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-300">
                Keine versteckten Kosten. Keine Überraschungen. 
                Wählen Sie den Plan, der zu Ihrem Unternehmen passt.
            </p>
        </div>
    </div>
</section>

<!-- Pricing Cards -->
<section class="py-12 bg-white dark:bg-slate-900">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Setup Fee Banner -->
        <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-700/50 rounded-2xl p-6 mb-12 text-center">
            <p class="text-primary-800 dark:text-primary-300">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Einmalige Einrichtungsgebühr: 499€</strong> – 
                Wir richten alles für Sie ein: Subdomain, E-Mails, Belohnungen, Design.
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <!-- Starter Plan -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border-2 border-gray-200 dark:border-slate-700 p-8 hover:border-primary-300 dark:hover:border-primary-600 transition-colors h-full flex flex-col">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Starter</h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">Perfekt für den Einstieg</p>
                </div>
                
                <div class="mb-6">
                    <div class="flex items-baseline">
                        <span class="text-5xl font-extrabold text-gray-900 dark:text-white">49€</span>
                        <span class="text-gray-500 dark:text-gray-400 ml-2">/Monat</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">zzgl. 499€ einmalige Einrichtung</p>
                </div>
                
                <div class="flex-1">
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span><strong class="text-gray-900 dark:text-white">Bis 200</strong> aktive Empfehler</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span><strong class="text-gray-900 dark:text-white">3</strong> Belohnungsstufen</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span><strong class="text-gray-900 dark:text-white">1</strong> Kampagne</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span>Eigene Subdomain</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span>11 Share-Buttons</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span>E-Mail-Sequenzen</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span>Basis-Gamification</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span>QR-Code & Share-Grafiken</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span>E-Mail-Support</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-400 dark:text-gray-500">
                            <i class="fas fa-times mt-1"></i>
                            <span>Lead-Export</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-400 dark:text-gray-500">
                            <i class="fas fa-times mt-1"></i>
                            <span>API & Webhooks</span>
                        </li>
                    </ul>
                </div>
                
                <a href="/onboarding?plan=starter" class="btn-secondary w-full text-center block mt-auto">
                    Starter wählen
                </a>
            </div>
            
            <!-- Professional Plan -->
            <div class="pricing-card popular bg-white dark:bg-slate-800 rounded-2xl border-2 border-primary-500 p-8 shadow-2xl relative h-full flex flex-col transform scale-105 z-10">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Professional</h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">Für wachsende Unternehmen</p>
                </div>
                
                <div class="mb-6">
                    <div class="flex items-baseline">
                        <span class="text-5xl font-extrabold text-primary-600 dark:text-primary-400">99€</span>
                        <span class="text-gray-500 dark:text-gray-400 ml-2">/Monat</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">zzgl. 499€ einmalige Einrichtung</p>
                </div>
                
                <div class="flex-1">
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span><strong class="text-gray-900 dark:text-white">Bis 5.000</strong> aktive Empfehler</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span><strong class="text-gray-900 dark:text-white">5</strong> Belohnungsstufen</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span><strong class="text-gray-900 dark:text-white">Unbegrenzte</strong> Kampagnen</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span>Eigene Subdomain</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span>11 Share-Buttons</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span>E-Mail-Sequenzen + Broadcast</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span>Erweiterte Gamification</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span>QR-Code & Share-Grafiken</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span><strong class="text-gray-900 dark:text-white">Lead-Export (CSV)</strong></span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span><strong class="text-gray-900 dark:text-white">API & Webhooks</strong></span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span><strong class="text-gray-900 dark:text-white">Eigenes Hintergrundbild</strong></span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span><strong class="text-gray-900 dark:text-white">Branding entfernen</strong></span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span><strong class="text-gray-900 dark:text-white">Prioritäts-Support</strong></span>
                        </li>
                    </ul>
                </div>
                
                <a href="/onboarding?plan=professional" class="btn-primary w-full text-center block mt-auto">
                    Professional wählen
                </a>
            </div>
            
            <!-- Enterprise Plan -->
            <div class="bg-gray-900 dark:bg-slate-950 rounded-2xl p-8 text-white h-full flex flex-col">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold">Enterprise</h2>
                    <p class="text-gray-400 mt-1">Für große Organisationen</p>
                </div>
                
                <div class="mb-6">
                    <div class="flex items-baseline">
                        <span class="text-3xl font-extrabold">Auf Anfrage</span>
                    </div>
                    <p class="text-sm text-gray-400 mt-2">Individuelle Konditionen</p>
                </div>
                
                <div class="flex-1">
                    <ul class="space-y-4 mb-8 text-gray-300">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check text-green-400 mt-1"></i>
                            <span><strong class="text-white">Unbegrenzte</strong> Empfehler</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check text-green-400 mt-1"></i>
                            <span><strong class="text-white">10</strong> Belohnungsstufen</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check text-green-400 mt-1"></i>
                            <span>Alles aus Professional</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check text-green-400 mt-1"></i>
                            <span><strong class="text-white">Eigene Domain</strong> möglich</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check text-green-400 mt-1"></i>
                            <span><strong class="text-white">Dedizierter Account Manager</strong></span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check text-green-400 mt-1"></i>
                            <span><strong class="text-white">SLA</strong> garantiert</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check text-green-400 mt-1"></i>
                            <span><strong class="text-white">Custom Integrations</strong></span>
                        </li>
                    </ul>
                </div>
                
                <a href="/kontakt?subject=enterprise" class="bg-white text-gray-900 hover:bg-gray-100 font-semibold py-3 px-6 rounded-full text-center block mt-auto transition-colors">
                    Kontakt aufnehmen
                </a>
            </div>
        </div>
        
        <!-- Trust Badges -->
        <div class="flex flex-wrap justify-center gap-6 mt-12">
            <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                <i class="fas fa-shield-alt text-green-500"></i>
                <span>DSGVO-konform</span>
            </div>
            <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                <i class="fas fa-undo text-blue-500"></i>
                <span>7 Tage testen</span>
            </div>
            <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                <i class="fas fa-credit-card text-purple-500"></i>
                <span>Sichere Zahlung</span>
            </div>
            <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                <i class="fas fa-times-circle text-red-500"></i>
                <span>Jederzeit kündbar</span>
            </div>
        </div>
    </div>
</section>

<!-- Detailed Comparison Table -->
<section class="py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Detaillierter Vergleich</h2>
        </div>
        
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-slate-800">
                        <tr>
                            <th class="text-left py-4 px-6 font-semibold text-gray-900 dark:text-white">Funktion</th>
                            <th class="text-center py-4 px-6 font-semibold text-gray-900 dark:text-white">Starter<br><span class="font-normal text-sm text-gray-500 dark:text-gray-400">49€/Monat</span></th>
                            <th class="text-center py-4 px-6 font-semibold text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20">Professional<br><span class="font-normal text-sm">99€/Monat</span></th>
                            <th class="text-center py-4 px-6 font-semibold text-gray-900 dark:text-white">Enterprise<br><span class="font-normal text-sm text-gray-500 dark:text-gray-400">Auf Anfrage</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700 text-gray-700 dark:text-gray-300">
                        <!-- Limits -->
                        <tr class="bg-gray-50 dark:bg-slate-800">
                            <td colspan="4" class="py-3 px-6 font-semibold text-gray-700 dark:text-gray-200">Limits</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">Aktive Empfehler</td>
                            <td class="text-center py-4 px-6">200</td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20 font-semibold">5.000</td>
                            <td class="text-center py-4 px-6">Unbegrenzt</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">Belohnungsstufen</td>
                            <td class="text-center py-4 px-6">3</td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20 font-semibold">5</td>
                            <td class="text-center py-4 px-6">10</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">Kampagnen</td>
                            <td class="text-center py-4 px-6">1</td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20 font-semibold">Unbegrenzt</td>
                            <td class="text-center py-4 px-6">Unbegrenzt</td>
                        </tr>
                        
                        <!-- Core Features -->
                        <tr class="bg-gray-50 dark:bg-slate-800">
                            <td colspan="4" class="py-3 px-6 font-semibold text-gray-700 dark:text-gray-200">Kernfunktionen</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">Eigene Subdomain</td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">Eigene Domain</td>
                            <td class="text-center py-4 px-6"><i class="fas fa-times text-gray-300 dark:text-gray-600"></i></td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20">Aufpreis</td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">11 Share-Buttons</td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">QR-Code</td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">Share-Grafiken</td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        
                        <!-- Design -->
                        <tr class="bg-gray-50 dark:bg-slate-800">
                            <td colspan="4" class="py-3 px-6 font-semibold text-gray-700 dark:text-gray-200">Design</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">Branchen-Hintergrundbilder</td>
                            <td class="text-center py-4 px-6">3 pro Branche</td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20">3 pro Branche</td>
                            <td class="text-center py-4 px-6">3 pro Branche</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">Eigenes Hintergrundbild</td>
                            <td class="text-center py-4 px-6"><i class="fas fa-times text-gray-300 dark:text-gray-600"></i></td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">Branding entfernen</td>
                            <td class="text-center py-4 px-6"><i class="fas fa-times text-gray-300 dark:text-gray-600"></i></td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        
                        <!-- Gamification -->
                        <tr class="bg-gray-50 dark:bg-slate-800">
                            <td colspan="4" class="py-3 px-6 font-semibold text-gray-700 dark:text-gray-200">Gamification</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">Fortschrittsbalken</td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">Leaderboard</td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">Badges</td>
                            <td class="text-center py-4 px-6">5 Basis</td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20 font-semibold">Alle 9</td>
                            <td class="text-center py-4 px-6">Alle 9 + Custom</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">Streak-Bonus</td>
                            <td class="text-center py-4 px-6"><i class="fas fa-times text-gray-300 dark:text-gray-600"></i></td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        
                        <!-- E-Mail -->
                        <tr class="bg-gray-50 dark:bg-slate-800">
                            <td colspan="4" class="py-3 px-6 font-semibold text-gray-700 dark:text-gray-200">E-Mail</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">Automatische Sequenzen</td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">Wöchentlicher Digest</td>
                            <td class="text-center py-4 px-6"><i class="fas fa-times text-gray-300 dark:text-gray-600"></i></td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">Broadcast-E-Mails</td>
                            <td class="text-center py-4 px-6"><i class="fas fa-times text-gray-300 dark:text-gray-600"></i></td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        
                        <!-- Integration -->
                        <tr class="bg-gray-50 dark:bg-slate-800">
                            <td colspan="4" class="py-3 px-6 font-semibold text-gray-700 dark:text-gray-200">Integration</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">Lead-Export (CSV)</td>
                            <td class="text-center py-4 px-6"><i class="fas fa-times text-gray-300 dark:text-gray-600"></i></td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">API-Zugang</td>
                            <td class="text-center py-4 px-6"><i class="fas fa-times text-gray-300 dark:text-gray-600"></i></td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">Webhooks</td>
                            <td class="text-center py-4 px-6"><i class="fas fa-times text-gray-300 dark:text-gray-600"></i></td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">Embed Widget</td>
                            <td class="text-center py-4 px-6"><i class="fas fa-times text-gray-300 dark:text-gray-600"></i></td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        
                        <!-- Support -->
                        <tr class="bg-gray-50 dark:bg-slate-800">
                            <td colspan="4" class="py-3 px-6 font-semibold text-gray-700 dark:text-gray-200">Support</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">E-Mail-Support</td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">Prioritäts-Support</td>
                            <td class="text-center py-4 px-6"><i class="fas fa-times text-gray-300 dark:text-gray-600"></i></td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">Dedizierter Account Manager</td>
                            <td class="text-center py-4 px-6"><i class="fas fa-times text-gray-300 dark:text-gray-600"></i></td>
                            <td class="text-center py-4 px-6 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-times text-gray-300 dark:text-gray-600"></i></td>
                            <td class="text-center py-4 px-6"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-20 bg-white dark:bg-slate-900">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Häufige Fragen zu den Preisen</h2>
        </div>
        
        <div class="space-y-4">
            <div class="faq-item border dark:border-slate-700 rounded-xl p-4 bg-white dark:bg-slate-800">
                <div class="faq-question dark:text-white">
                    <span>Was ist mit der einmaligen Einrichtungsgebühr enthalten?</span>
                </div>
                <div class="faq-answer text-gray-600 dark:text-gray-300">
                    <p>Die 499€ Einrichtungsgebühr beinhaltet die komplette Ersteinrichtung Ihres Empfehlungsprogramms: Subdomain-Konfiguration, E-Mail-System-Setup, Belohnungsstufen nach Ihren Wünschen, Branchen-spezifisches Design und die Integration Ihres Logos.</p>
                </div>
            </div>
            
            <div class="faq-item border dark:border-slate-700 rounded-xl p-4 bg-white dark:bg-slate-800">
                <div class="faq-question dark:text-white">
                    <span>Kann ich den Plan später wechseln?</span>
                </div>
                <div class="faq-answer text-gray-600 dark:text-gray-300">
                    <p>Ja, Sie können jederzeit vom Starter- zum Professional-Plan upgraden. Die Differenz wird anteilig berechnet. Ein Downgrade ist zum nächsten Abrechnungszeitraum möglich.</p>
                </div>
            </div>
            
            <div class="faq-item border dark:border-slate-700 rounded-xl p-4 bg-white dark:bg-slate-800">
                <div class="faq-question dark:text-white">
                    <span>Was passiert, wenn ich mein Empfehler-Limit erreiche?</span>
                </div>
                <div class="faq-answer text-gray-600 dark:text-gray-300">
                    <p>Sie erhalten rechtzeitig eine Benachrichtigung, bevor Sie Ihr Limit erreichen. Neue Empfehler können sich dann nicht mehr anmelden, bestehende bleiben aktiv. Upgraden Sie auf Professional oder kontaktieren Sie uns für ein Enterprise-Angebot.</p>
                </div>
            </div>
            
            <div class="faq-item border dark:border-slate-700 rounded-xl p-4 bg-white dark:bg-slate-800">
                <div class="faq-question dark:text-white">
                    <span>Gibt es eine Mindestlaufzeit?</span>
                </div>
                <div class="faq-answer text-gray-600 dark:text-gray-300">
                    <p>Nein, Sie können monatlich kündigen. Die Einrichtungsgebühr wird jedoch nicht erstattet. Wir empfehlen, mindestens 3 Monate dabei zu bleiben, um aussagekräftige Ergebnisse zu erzielen.</p>
                </div>
            </div>
            
            <div class="faq-item border dark:border-slate-700 rounded-xl p-4 bg-white dark:bg-slate-800">
                <div class="faq-question dark:text-white">
                    <span>Welche Zahlungsmethoden werden akzeptiert?</span>
                </div>
                <div class="faq-answer text-gray-600 dark:text-gray-300">
                    <p>Wir akzeptieren Kreditkarten (Visa, Mastercard), SEPA-Lastschrift, PayPal und auf Anfrage auch Rechnung (Enterprise). Alle Zahlungen werden über unseren deutschen Zahlungsanbieter Digistore24 abgewickelt.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 gradient-bg text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold mb-6">
            7 Tage kostenlos testen
        </h2>
        <p class="text-xl text-white/90 mb-8">
            Keine Kreditkarte erforderlich. Kein Risiko. Überzeugen Sie sich selbst.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/onboarding" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-yellow-400 to-orange-500 text-gray-900 font-bold text-lg rounded-full shadow-[0_0_30px_rgba(251,191,36,0.5)] hover:shadow-[0_0_50px_rgba(251,191,36,0.8)] hover:scale-105 transition-all duration-300">
                <span>Jetzt kostenlos starten</span>
                <i class="fas fa-arrow-right"></i>
            </a>
            <a href="/kontakt" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white/10 backdrop-blur-sm text-white font-semibold text-lg rounded-full border-2 border-white/30 hover:bg-white hover:text-primary-600 hover:border-white transition-all duration-300">
                <span>Beratung anfordern</span>
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../templates/marketing/footer.php'; ?>
