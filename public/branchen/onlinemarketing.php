<?php
/**
 * Branchenseite: Online-Marketing & Kursanbieter
 * Mit drei interaktiven Animationen
 */

$pageTitle = 'Empfehlungsprogramm für Online-Kurse & Infoprodukte';
$metaDescription = 'Automatisches Empfehlungsprogramm für Kursanbieter, Infoprodukte und Online-Marketing. Kursteilnehmer empfehlen und verdienen Rabatte, Bonus-Module oder Provisionen.';
$currentPage = 'branchen';

require_once __DIR__ . '/../../templates/marketing/header.php';

// Branchenspezifische Daten
$branche = [
    'name' => 'Online-Kurse & Infoprodukte',
    'slug' => 'onlinemarketing',
    'icon' => 'fa-graduation-cap',
    'color' => 'indigo',
    'heroTitle' => 'Mehr Kursteilnehmer durch Empfehlungen',
    'heroSubtitle' => 'Erfolgreiche Absolventen sind Ihre beste Werbung. Verwandeln Sie jeden Kursabschluss in eine Empfehlungs-Maschine – ohne Affiliate-Komplexität.',
];

$vorteile = [
    [
        'icon' => 'fa-users',
        'title' => 'Warme Leads',
        'text' => 'Empfohlene Teilnehmer vertrauen dem Kurs bereits – die Hürde zum Kauf ist deutlich niedriger.'
    ],
    [
        'icon' => 'fa-euro-sign',
        'title' => 'Null CAC',
        'text' => 'Keine Werbekosten für empfohlene Kunden. Belohnungen kosten nur bei Erfolg.'
    ],
    [
        'icon' => 'fa-rocket',
        'title' => 'Skalierbar',
        'text' => 'Digitale Belohnungen (Bonus-Module, E-Books) haben null Grenzkosten – unbegrenzt skalierbar.'
    ],
    [
        'icon' => 'fa-heart',
        'title' => 'Community-Building',
        'text' => 'Empfehler fühlen sich als Teil Ihrer Community und bleiben langfristig treu.'
    ],
];

$belohnungen = [
    ['stufe' => 1, 'belohnung' => 'Exklusives Bonus-Modul freischalten'],
    ['stufe' => 3, 'belohnung' => '30% Rabatt auf nächsten Kurs'],
    ['stufe' => 5, 'belohnung' => 'VIP-Zugang zu allen zukünftigen Updates'],
];

$testimonial = [
    'text' => 'Ich habe mein Affiliate-Programm durch Leadbusiness ersetzt. Viel weniger Verwaltung, und die Empfehlungsrate ist sogar gestiegen. Die Teilnehmer lieben die Gamification!',
    'name' => 'Thomas Kern',
    'rolle' => 'Gründer, Online Marketing Akademie',
    'initialen' => 'TK',
];

$stats = [
    'empfehler' => '892',
    'conversions' => '267',
    'rate' => '30%',
];
?>

<!-- Hero Section -->
<section class="relative py-16 md:py-24 overflow-hidden">
    <!-- Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 to-violet-700"></div>
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-40 h-40 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 right-10 w-60 h-60 bg-white rounded-full blur-3xl"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
            <div class="text-white">
                <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full mb-6">
                    <i class="fas <?= $branche['icon'] ?>"></i>
                    <span class="text-sm font-medium">Für <?= $branche['name'] ?></span>
                </div>
                
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold mb-6 leading-tight">
                    <?= $branche['heroTitle'] ?>
                </h1>
                
                <p class="text-lg md:text-xl text-white/90 mb-8 leading-relaxed">
                    <?= $branche['heroSubtitle'] ?>
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="/onboarding" class="btn-white btn-large inline-flex items-center justify-center gap-2">
                        <span>Jetzt kostenlos starten</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="/preise" class="btn-ghost-white btn-large inline-flex items-center justify-center gap-2">
                        <span>Preise ansehen</span>
                    </a>
                </div>
                
                <div class="mt-8 flex flex-wrap gap-6 text-white/80 text-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-400"></i>
                        <span>7 Tage kostenlos</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-400"></i>
                        <span>E-Mail-Tool Export</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-400"></i>
                        <span>DSGVO-konform</span>
                    </div>
                </div>
            </div>
            
            <!-- Visual -->
            <div class="hidden lg:block">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="bg-white rounded-xl shadow-2xl p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-indigo-600 text-xl"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">Marketing Akademie</div>
                                <div class="text-sm text-gray-500">empfohlen.de/marketing-akademie</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-indigo-600"><?= $stats['empfehler'] ?></div>
                                <div class="text-xs text-gray-500">Empfehler</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600"><?= $stats['conversions'] ?></div>
                                <div class="text-xs text-gray-500">Neue Teilnehmer</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600"><?= $stats['rate'] ?></div>
                                <div class="text-xs text-gray-500">Conversion</div>
                            </div>
                        </div>
                        <div class="text-center text-sm text-gray-500">
                            <i class="fas fa-trophy text-yellow-500 mr-1"></i>
                            Top-Empfehler: Max M. mit 12 Empfehlungen
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Vorteile Section -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Warum Empfehlungsmarketing für Kursanbieter?
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg max-w-2xl mx-auto">
                Einfacher als Affiliate-Programme, effektiver als bezahlte Werbung.
            </p>
        </div>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
            <?php foreach ($vorteile as $vorteil): ?>
            <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-6 hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-xl mb-4">
                    <i class="fas <?= $vorteil['icon'] ?>"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2"><?= $vorteil['title'] ?></h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm"><?= $vorteil['text'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================== -->
<!-- INTERAKTIVE ANIMATIONEN SECTION               -->
<!-- ============================================== -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-500 to-violet-600 text-white px-5 py-2 rounded-full text-sm font-bold shadow-lg mb-4">
                <span>🚀</span> Live erleben
            </span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                So boosten Empfehlungen deinen Kurs-Launch
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg max-w-2xl mx-auto">
                Drei interaktive Demos zeigen die Power von Empfehlungsmarketing für Online-Kurse.
            </p>
        </div>
        
        <!-- Tab Navigation -->
        <div class="flex flex-wrap justify-center gap-3 mb-8" id="om-animation-tabs">
            <button onclick="showOMAnimation('launch')" id="tab-launch" class="om-tab active px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-gradient-to-r from-indigo-500 to-violet-600 text-white shadow-lg">
                🚀 Launch-Boost
            </button>
            <button onclick="showOMAnimation('cac')" id="tab-cac" class="om-tab px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-white dark:bg-slate-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-600 hover:shadow-md">
                💰 CAC-Killer
            </button>
            <button onclick="showOMAnimation('leaderboard')" id="tab-leaderboard" class="om-tab px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-white dark:bg-slate-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-600 hover:shadow-md">
                🎮 Leaderboard-Demo
            </button>
        </div>
        
        <!-- Animation Containers -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-8 min-h-[600px] shadow-lg">
            
            <!-- ========================================= -->
            <!-- ANIMATION 1: LAUNCH-BOOST-SIMULATOR      -->
            <!-- ========================================= -->
            <div id="animation-launch" class="om-animation-content">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Launch-Boost-Simulator</h3>
                    <p class="text-gray-500 dark:text-gray-400">Sehe, wie Empfehlungen deinen Kurs-Launch explodieren lassen</p>
                </div>
                
                <div class="max-w-3xl mx-auto">
                    <!-- Timeline -->
                    <div class="relative mb-8">
                        <div class="absolute top-8 left-0 right-0 h-1 bg-gray-200 dark:bg-slate-700 rounded-full">
                            <div id="launch-progress" class="h-full bg-gradient-to-r from-indigo-500 to-violet-500 rounded-full transition-all duration-500" style="width: 0%"></div>
                        </div>
                        
                        <!-- Timeline Steps -->
                        <div class="grid grid-cols-5 gap-2 relative">
                            <div id="launch-step-1" class="launch-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">🎬</div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Launch!</span>
                            </div>
                            <div id="launch-step-2" class="launch-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">📢</div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Woche 1</span>
                            </div>
                            <div id="launch-step-3" class="launch-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">🔥</div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Woche 2</span>
                            </div>
                            <div id="launch-step-4" class="launch-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">🚀</div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Woche 3</span>
                            </div>
                            <div id="launch-step-5" class="launch-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">🏆</div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Ergebnis</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Comparison Bars -->
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <!-- Without Referrals -->
                        <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-5">
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-bold text-gray-700 dark:text-gray-300">❌ Ohne Empfehlungen</span>
                                <span id="launch-without" class="text-2xl font-black text-gray-500">0</span>
                            </div>
                            <div class="h-8 bg-gray-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div id="launch-bar-without" class="h-full bg-gray-400 rounded-full transition-all duration-1000" style="width: 0%"></div>
                            </div>
                            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">Nur direkte Käufe</div>
                        </div>
                        
                        <!-- With Referrals -->
                        <div class="bg-gradient-to-br from-indigo-50 to-violet-50 dark:from-indigo-900/20 dark:to-violet-900/20 rounded-xl p-5 border-2 border-indigo-300 dark:border-indigo-700">
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-bold text-indigo-700 dark:text-indigo-300">✅ Mit Empfehlungen</span>
                                <span id="launch-with" class="text-2xl font-black text-indigo-600 dark:text-indigo-400">0</span>
                            </div>
                            <div class="h-8 bg-indigo-100 dark:bg-indigo-900/50 rounded-full overflow-hidden">
                                <div id="launch-bar-with" class="h-full bg-gradient-to-r from-indigo-500 to-violet-500 rounded-full transition-all duration-1000" style="width: 0%"></div>
                            </div>
                            <div class="mt-2 text-sm text-indigo-600 dark:text-indigo-400">Direkt + Empfehlungen</div>
                        </div>
                    </div>
                    
                    <!-- Participant Visualization -->
                    <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-4 mb-6">
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-3">Teilnehmer-Wachstum:</div>
                        <div id="launch-participants" class="flex flex-wrap gap-1 min-h-[60px] transition-all duration-500">
                            <!-- Participants will be added here -->
                        </div>
                    </div>
                    
                    <!-- Info Box -->
                    <div id="launch-info" class="bg-gradient-to-r from-indigo-50 to-violet-50 dark:from-indigo-900/20 dark:to-violet-900/20 rounded-xl p-4 mb-6 text-center">
                        <div id="launch-text" class="text-gray-700 dark:text-gray-300 font-medium">
                            Klicke, um deinen virtuellen Kurs zu launchen!
                        </div>
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-4 gap-3 mb-6">
                        <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-3 text-center">
                            <div id="launch-direct" class="text-xl font-bold text-gray-600 dark:text-gray-400">0</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Direkt</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-3 text-center">
                            <div id="launch-referral" class="text-xl font-bold text-indigo-600 dark:text-indigo-400">0</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Empfohlen</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-3 text-center">
                            <div id="launch-boost" class="text-xl font-bold text-green-600 dark:text-green-400">0%</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Boost</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-3 text-center">
                            <div id="launch-revenue" class="text-xl font-bold text-yellow-600 dark:text-yellow-400">0€</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Extra-Umsatz</div>
                        </div>
                    </div>
                    
                    <!-- Action Button -->
                    <button onclick="startLaunch()" id="launch-btn" class="w-full py-4 bg-gradient-to-r from-indigo-500 to-violet-600 text-white rounded-xl font-bold text-lg hover:shadow-lg hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-rocket"></i>
                        <span>Launch starten!</span>
                    </button>
                    
                    <button onclick="resetLaunch()" class="w-full mt-3 py-2 text-gray-500 dark:text-gray-400 text-sm hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        ↻ Demo zurücksetzen
                    </button>
                </div>
            </div>
            
            <!-- ========================================= -->
            <!-- ANIMATION 2: CAC-KILLER-RECHNER          -->
            <!-- ========================================= -->
            <div id="animation-cac" class="om-animation-content hidden">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">CAC-Killer-Rechner</h3>
                    <p class="text-gray-500 dark:text-gray-400">Facebook Ads vs. Empfehlungen – der brutale ROI-Vergleich</p>
                </div>
                
                <div class="max-w-2xl mx-auto">
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-slate-800 dark:to-slate-700 rounded-2xl p-6 md:p-8">
                        
                        <!-- Slider: Kurs-Preis -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Was kostet dein Kurs?
                            </label>
                            <div class="flex items-center gap-4">
                                <input type="range" id="cac-preis-slider" min="97" max="2997" step="100" value="497" class="flex-1 h-3 bg-gray-300 dark:bg-slate-600 rounded-full appearance-none cursor-pointer accent-indigo-500" oninput="updateCAC()">
                                <div class="bg-white dark:bg-slate-600 rounded-xl px-4 py-2 min-w-[100px] text-center">
                                    <span id="cac-preis" class="text-2xl font-bold text-gray-900 dark:text-white">497€</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Slider: Ad Spend -->
                        <div class="mb-8">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Monatliches Ad Budget?
                            </label>
                            <div class="flex items-center gap-4">
                                <input type="range" id="cac-budget-slider" min="500" max="10000" step="500" value="3000" class="flex-1 h-3 bg-gray-300 dark:bg-slate-600 rounded-full appearance-none cursor-pointer accent-indigo-500" oninput="updateCAC()">
                                <div class="bg-white dark:bg-slate-600 rounded-xl px-4 py-2 min-w-[100px] text-center">
                                    <span id="cac-budget" class="text-2xl font-bold text-gray-900 dark:text-white">3.000€</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Comparison Cards -->
                        <div class="grid md:grid-cols-2 gap-6 mb-8">
                            <!-- Facebook Ads -->
                            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 border-2 border-blue-200 dark:border-blue-900/50">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                                        <i class="fab fa-facebook-f text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white">Facebook Ads</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Bezahlte Werbung</div>
                                    </div>
                                </div>
                                
                                <div class="space-y-3 mb-4">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">CPM (1000 Impressions)</span>
                                        <span class="text-gray-700 dark:text-gray-300">~15€</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">CTR (Klickrate)</span>
                                        <span class="text-gray-700 dark:text-gray-300">~1.5%</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Conversion Rate</span>
                                        <span class="text-gray-700 dark:text-gray-300">~2%</span>
                                    </div>
                                </div>
                                
                                <!-- CAC Visualization -->
                                <div class="bg-blue-50 dark:bg-blue-900/30 rounded-xl p-4 text-center mb-4">
                                    <div class="text-sm text-blue-600 dark:text-blue-400 mb-1">Cost per Acquisition</div>
                                    <div class="text-4xl font-black text-blue-600 dark:text-blue-400" id="cac-fb-cac">150€</div>
                                </div>
                                
                                <div class="text-center border-t pt-4">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Verkäufe/Monat:</div>
                                    <div class="text-2xl font-bold text-gray-700 dark:text-gray-300" id="cac-fb-sales">20</div>
                                </div>
                            </div>
                            
                            <!-- Empfehlungen -->
                            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 border-2 border-indigo-400 dark:border-indigo-500 relative overflow-hidden">
                                <div class="absolute top-2 right-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                    -90% CAC
                                </div>
                                
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-heart text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white">Empfehlungen</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Mit Leadbusiness</div>
                                    </div>
                                </div>
                                
                                <div class="space-y-3 mb-4">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Leadbusiness/Monat</span>
                                        <span class="text-gray-700 dark:text-gray-300">49€</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Belohnungen (Ø)</span>
                                        <span class="text-gray-700 dark:text-gray-300" id="cac-rewards">~25€</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Empfehlungsrate</span>
                                        <span class="text-green-600 dark:text-green-400">~30%</span>
                                    </div>
                                </div>
                                
                                <!-- CAC Visualization -->
                                <div class="bg-gradient-to-r from-indigo-50 to-violet-50 dark:from-indigo-900/30 dark:to-violet-900/30 rounded-xl p-4 text-center mb-4">
                                    <div class="text-sm text-indigo-600 dark:text-indigo-400 mb-1">Cost per Acquisition</div>
                                    <div class="text-4xl font-black text-indigo-600 dark:text-indigo-400" id="cac-ref-cac">15€</div>
                                </div>
                                
                                <div class="text-center border-t pt-4">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Verkäufe/Monat (extra):</div>
                                    <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400" id="cac-ref-sales">+6</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ROI Result -->
                        <div id="cac-result" class="bg-gradient-to-r from-indigo-500 to-violet-600 rounded-2xl p-6 text-white text-center">
                            <div class="text-lg mb-2">💰 Dein Vorteil mit Empfehlungen</div>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <div class="text-3xl font-black" id="cac-savings">2.700€</div>
                                    <div class="text-sm text-white/70">Gespart/Monat</div>
                                </div>
                                <div>
                                    <div class="text-3xl font-black" id="cac-extra-revenue">2.982€</div>
                                    <div class="text-sm text-white/70">Extra-Umsatz</div>
                                </div>
                                <div>
                                    <div class="text-3xl font-black" id="cac-roi">5.682€</div>
                                    <div class="text-sm text-white/70">Total Benefit</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pro Tip -->
                        <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-4 flex items-start gap-3">
                            <div class="text-2xl">💡</div>
                            <div class="text-sm text-yellow-800 dark:text-yellow-200">
                                <strong>Pro-Tipp:</strong> Empfehlungen und Ads kombinieren! Nutze Ads für neue Zielgruppen und Empfehlungen für virales Wachstum in bestehenden Communities.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ========================================= -->
            <!-- ANIMATION 3: LEADERBOARD-DEMO            -->
            <!-- ========================================= -->
            <div id="animation-leaderboard" class="om-animation-content hidden">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Leaderboard-Demo</h3>
                    <p class="text-gray-500 dark:text-gray-400">So motiviert Gamification deine Community zum Empfehlen</p>
                </div>
                
                <div class="max-w-3xl mx-auto">
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Leaderboard -->
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-indigo-500 to-violet-600 p-4 text-white text-center">
                                <div class="text-2xl mb-1">🏆</div>
                                <h4 class="font-bold">Top Empfehler</h4>
                                <p class="text-sm text-white/70">Diese Woche</p>
                            </div>
                            
                            <div id="leaderboard-list" class="p-4 space-y-3">
                                <!-- Leaderboard entries will be added here -->
                            </div>
                            
                            <!-- Your Position -->
                            <div class="border-t border-gray-200 dark:border-slate-700 p-4 bg-indigo-50 dark:bg-indigo-900/20">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center text-white font-bold text-sm" id="your-rank">11</div>
                                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-violet-500 rounded-full flex items-center justify-center text-white font-bold">DU</div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white">Du</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400" id="your-referrals">0 Empfehlungen</div>
                                        </div>
                                    </div>
                                    <div id="your-badge" class="text-2xl">🌱</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Interactive Panel -->
                        <div class="space-y-4">
                            <!-- Share Simulation -->
                            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-lg">
                                <h4 class="font-bold text-gray-900 dark:text-white mb-4">📤 Simuliere Empfehlungen</h4>
                                
                                <div class="space-y-3">
                                    <button onclick="simulateReferral()" id="refer-btn" class="w-full py-3 bg-gradient-to-r from-indigo-500 to-violet-600 text-white rounded-xl font-bold hover:shadow-lg hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
                                        <i class="fas fa-share"></i>
                                        <span>Empfehlung senden!</span>
                                    </button>
                                    
                                    <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                                        Klicke mehrmals, um das Leaderboard zu erklimmen!
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Progress to Next Reward -->
                            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-lg">
                                <h4 class="font-bold text-gray-900 dark:text-white mb-3">🎁 Nächste Belohnung</h4>
                                
                                <div id="next-reward-box" class="bg-gradient-to-r from-indigo-50 to-violet-50 dark:from-indigo-900/20 dark:to-violet-900/20 rounded-xl p-4 mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="text-3xl" id="next-reward-icon">📚</div>
                                        <div>
                                            <div class="font-semibold text-gray-900 dark:text-white" id="next-reward-name">Bonus-Modul</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400" id="next-reward-desc">Bei 1 Empfehlung</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-2">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-500 dark:text-gray-400">Fortschritt</span>
                                        <span class="font-medium text-indigo-600 dark:text-indigo-400" id="progress-text">0/1</span>
                                    </div>
                                    <div class="h-3 bg-gray-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div id="progress-bar" class="h-full bg-gradient-to-r from-indigo-500 to-violet-500 rounded-full transition-all duration-500" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Badges Collection -->
                            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-lg">
                                <h4 class="font-bold text-gray-900 dark:text-white mb-3">🏅 Deine Badges</h4>
                                <div id="badges-collection" class="flex flex-wrap gap-2">
                                    <div class="badge-slot w-12 h-12 bg-gray-100 dark:bg-slate-700 rounded-xl flex items-center justify-center text-2xl opacity-30" title="Erster Schritt (1 Empfehlung)">🌱</div>
                                    <div class="badge-slot w-12 h-12 bg-gray-100 dark:bg-slate-700 rounded-xl flex items-center justify-center text-2xl opacity-30" title="5er Club">⭐</div>
                                    <div class="badge-slot w-12 h-12 bg-gray-100 dark:bg-slate-700 rounded-xl flex items-center justify-center text-2xl opacity-30" title="10er Club">🌟</div>
                                    <div class="badge-slot w-12 h-12 bg-gray-100 dark:bg-slate-700 rounded-xl flex items-center justify-center text-2xl opacity-30" title="Super-Werber (25)">🚀</div>
                                    <div class="badge-slot w-12 h-12 bg-gray-100 dark:bg-slate-700 rounded-xl flex items-center justify-center text-2xl opacity-30" title="Legende (50)">👑</div>
                                </div>
                            </div>
                            
                            <!-- Celebration Box (hidden initially) -->
                            <div id="celebration-box" class="hidden bg-gradient-to-r from-yellow-400 to-orange-500 rounded-2xl p-5 text-white text-center animate-bounce-in">
                                <div class="text-4xl mb-2" id="celebration-emoji">🎉</div>
                                <div class="font-bold text-lg" id="celebration-title">Badge freigeschaltet!</div>
                                <div class="text-white/90" id="celebration-text">Du hast deinen ersten Badge erhalten!</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Reset -->
                    <button onclick="resetLeaderboard()" class="w-full mt-6 py-2 text-gray-500 dark:text-gray-400 text-sm hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        ↻ Demo zurücksetzen
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Animation Styles -->
<style>
    @keyframes bounceIn {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes pulse-indigo {
        0%, 100% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.7); }
        50% { box-shadow: 0 0 0 15px rgba(99, 102, 241, 0); }
    }
    @keyframes float-up {
        0% { opacity: 1; transform: translateY(0) scale(1); }
        100% { opacity: 0; transform: translateY(-50px) scale(0.5); }
    }
    @keyframes confetti-fall {
        0% { transform: translateY(-100%) rotate(0deg); opacity: 1; }
        100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
    }
    @keyframes rank-up {
        0% { transform: scale(1); }
        50% { transform: scale(1.3); background: linear-gradient(to bottom right, #fbbf24, #f59e0b); }
        100% { transform: scale(1); }
    }
    @keyframes participant-appear {
        0% { transform: scale(0); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
    
    .animate-bounce-in { animation: bounceIn 0.5s ease forwards; }
    .animate-pulse-indigo { animation: pulse-indigo 2s ease-in-out infinite; }
    .animate-rank-up { animation: rank-up 0.5s ease forwards; }
    
    .om-tab.active {
        background: linear-gradient(to right, #6366f1, #8b5cf6);
        color: white;
        box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
        border: none;
    }
    
    .launch-step.active { opacity: 1 !important; }
    .launch-step.active > div:first-child {
        background: linear-gradient(to bottom right, #6366f1, #8b5cf6) !important;
        transform: scale(1.1);
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.5);
    }
    .launch-step.completed { opacity: 1 !important; }
    .launch-step.completed > div:first-child {
        background: #e0e7ff !important;
    }
    .dark .launch-step.completed > div:first-child {
        background: #312e81 !important;
    }
    
    .leaderboard-entry {
        transition: all 0.5s ease;
    }
    .leaderboard-entry.moving-up {
        animation: rank-up 0.5s ease;
    }
    
    .badge-slot.earned {
        opacity: 1 !important;
        background: linear-gradient(to bottom right, #fef3c7, #fde68a) !important;
        animation: bounceIn 0.5s ease forwards;
    }
    .dark .badge-slot.earned {
        background: linear-gradient(to bottom right, #78350f, #92400e) !important;
    }
    
    .participant-icon {
        animation: participant-appear 0.3s ease forwards;
    }
    
    .confetti {
        position: fixed;
        width: 10px;
        height: 10px;
        top: -10px;
        animation: confetti-fall 3s linear forwards;
        z-index: 100;
    }
    
    input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 24px;
        height: 24px;
        background: linear-gradient(to bottom right, #6366f1, #8b5cf6);
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
</style>

<!-- Animation JavaScript -->
<script>
// ==================== TAB SWITCHING ====================
function showOMAnimation(type) {
    document.querySelectorAll('.om-animation-content').forEach(el => el.classList.add('hidden'));
    document.getElementById('animation-' + type).classList.remove('hidden');
    
    document.querySelectorAll('.om-tab').forEach(tab => {
        tab.classList.remove('active');
        tab.classList.add('bg-white', 'dark:bg-slate-700', 'text-gray-600', 'dark:text-gray-300', 'border', 'border-gray-200', 'dark:border-slate-600');
    });
    const activeTab = document.getElementById('tab-' + type);
    activeTab.classList.add('active');
    activeTab.classList.remove('bg-white', 'dark:bg-slate-700', 'text-gray-600', 'dark:text-gray-300', 'border', 'border-gray-200', 'dark:border-slate-600');
    
    if (type === 'launch') resetLaunch();
    if (type === 'cac') updateCAC();
    if (type === 'leaderboard') resetLeaderboard();
}

// ==================== ANIMATION 1: LAUNCH-BOOST ====================
let launchStep = 0;
let launchInterval = null;
const launchSteps = [
    { text: '🎬 LAUNCH! Du öffnest den Kurs für 100 Käufer.', progress: 20, direct: 100, referral: 0, participants: 100 },
    { text: '📢 Woche 1: Teilnehmer starten, erste teilen ihren Link...', progress: 40, direct: 100, referral: 15, participants: 115 },
    { text: '🔥 Woche 2: Empfehlungen kommen rein! Weitere 25 durch Mundpropaganda.', progress: 60, direct: 100, referral: 40, participants: 140 },
    { text: '🚀 Woche 3: Virale Welle! Die Empfohlenen empfehlen weiter!', progress: 80, direct: 100, referral: 72, participants: 172 },
    { text: '🏆 ERGEBNIS: 72% mehr Teilnehmer – ohne einen Cent mehr für Ads!', progress: 100, direct: 100, referral: 72, participants: 172, final: true }
];

function startLaunch() {
    if (launchStep > 0) return;
    
    document.getElementById('launch-btn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Launch läuft...</span>';
    document.getElementById('launch-btn').disabled = true;
    
    launchInterval = setInterval(() => {
        if (launchStep >= launchSteps.length) {
            clearInterval(launchInterval);
            document.getElementById('launch-btn').innerHTML = '<i class="fas fa-redo"></i> <span>Nochmal launchen</span>';
            document.getElementById('launch-btn').disabled = false;
            document.getElementById('launch-btn').onclick = resetLaunch;
            createConfetti();
            return;
        }
        
        const step = launchSteps[launchStep];
        
        // Update progress
        document.getElementById('launch-progress').style.width = step.progress + '%';
        
        // Update steps
        for (let i = 1; i <= 5; i++) {
            const stepEl = document.getElementById('launch-step-' + i);
            stepEl.classList.remove('active', 'completed');
            if (i < launchStep + 1) stepEl.classList.add('completed');
            if (i === launchStep + 1) stepEl.classList.add('active');
        }
        
        // Update bars
        document.getElementById('launch-bar-without').style.width = (step.direct / 172 * 100) + '%';
        document.getElementById('launch-bar-with').style.width = (step.participants / 172 * 100) + '%';
        
        // Update numbers
        document.getElementById('launch-without').textContent = step.direct;
        document.getElementById('launch-with').textContent = step.participants;
        document.getElementById('launch-direct').textContent = step.direct;
        document.getElementById('launch-referral').textContent = step.referral;
        document.getElementById('launch-boost').textContent = '+' + Math.round(step.referral / step.direct * 100) + '%';
        document.getElementById('launch-revenue').textContent = (step.referral * 497).toLocaleString('de-DE') + '€';
        
        // Update participants visualization
        updateParticipants(step.participants, step.referral);
        
        document.getElementById('launch-text').innerHTML = step.text;
        
        if (step.final) {
            document.getElementById('launch-info').classList.remove('from-indigo-50', 'to-violet-50');
            document.getElementById('launch-info').classList.add('from-indigo-500', 'to-violet-600');
            document.getElementById('launch-text').classList.add('text-white');
        }
        
        launchStep++;
    }, 2000);
}

function updateParticipants(total, referrals) {
    const container = document.getElementById('launch-participants');
    const direct = total - referrals;
    
    // Clear and rebuild
    container.innerHTML = '';
    
    // Add direct participants
    for (let i = 0; i < Math.min(direct, 50); i++) {
        setTimeout(() => {
            const p = document.createElement('span');
            p.className = 'participant-icon text-lg';
            p.textContent = '👤';
            container.appendChild(p);
        }, i * 20);
    }
    
    // Add referred participants
    for (let i = 0; i < Math.min(referrals, 36); i++) {
        setTimeout(() => {
            const p = document.createElement('span');
            p.className = 'participant-icon text-lg';
            p.textContent = '🌟';
            container.appendChild(p);
        }, (direct + i) * 20);
    }
    
    if (total > 86) {
        setTimeout(() => {
            const more = document.createElement('span');
            more.className = 'text-sm text-gray-500 ml-2';
            more.textContent = `+${total - 86} mehr`;
            container.appendChild(more);
        }, 86 * 20);
    }
}

function resetLaunch() {
    clearInterval(launchInterval);
    launchStep = 0;
    
    document.getElementById('launch-progress').style.width = '0%';
    document.getElementById('launch-bar-without').style.width = '0%';
    document.getElementById('launch-bar-with').style.width = '0%';
    document.getElementById('launch-without').textContent = '0';
    document.getElementById('launch-with').textContent = '0';
    document.getElementById('launch-direct').textContent = '0';
    document.getElementById('launch-referral').textContent = '0';
    document.getElementById('launch-boost').textContent = '0%';
    document.getElementById('launch-revenue').textContent = '0€';
    document.getElementById('launch-participants').innerHTML = '';
    
    for (let i = 1; i <= 5; i++) {
        document.getElementById('launch-step-' + i).classList.remove('active', 'completed');
    }
    
    document.getElementById('launch-info').classList.remove('from-indigo-500', 'to-violet-600');
    document.getElementById('launch-info').classList.add('from-indigo-50', 'to-violet-50');
    document.getElementById('launch-text').classList.remove('text-white');
    document.getElementById('launch-text').innerHTML = 'Klicke, um deinen virtuellen Kurs zu launchen!';
    
    document.getElementById('launch-btn').innerHTML = '<i class="fas fa-rocket"></i> <span>Launch starten!</span>';
    document.getElementById('launch-btn').disabled = false;
    document.getElementById('launch-btn').onclick = startLaunch;
}

// ==================== ANIMATION 2: CAC-KILLER ====================
function updateCAC() {
    const preis = parseInt(document.getElementById('cac-preis-slider').value);
    const budget = parseInt(document.getElementById('cac-budget-slider').value);
    
    document.getElementById('cac-preis').textContent = preis + '€';
    document.getElementById('cac-budget').textContent = budget.toLocaleString('de-DE') + '€';
    
    // Facebook Ads calculation
    // CPM ~15€, CTR ~1.5%, Conversion ~2%
    const impressions = budget / 15 * 1000;
    const clicks = impressions * 0.015;
    const fbSales = Math.round(clicks * 0.02);
    const fbCAC = fbSales > 0 ? Math.round(budget / fbSales) : 999;
    
    document.getElementById('cac-fb-cac').textContent = fbCAC + '€';
    document.getElementById('cac-fb-sales').textContent = fbSales;
    
    // Empfehlungen calculation
    // 30% of FB customers will refer, each brings ~0.5 new customers on average
    const refRate = 0.30;
    const refMultiplier = 0.5;
    const refSales = Math.round(fbSales * refRate * refMultiplier);
    const leadbusinessCost = 49;
    const rewardCost = Math.round(preis * 0.05); // 5% als Belohnung
    const totalRefCost = leadbusinessCost + (rewardCost * refSales);
    const refCAC = refSales > 0 ? Math.round(totalRefCost / refSales) : 49;
    
    document.getElementById('cac-rewards').textContent = '~' + (rewardCost * refSales) + '€';
    document.getElementById('cac-ref-cac').textContent = refCAC + '€';
    document.getElementById('cac-ref-sales').textContent = '+' + refSales;
    
    // Results
    const savings = Math.round((fbCAC - refCAC) * refSales);
    const extraRevenue = refSales * preis;
    const totalBenefit = savings + extraRevenue;
    
    document.getElementById('cac-savings').textContent = savings.toLocaleString('de-DE') + '€';
    document.getElementById('cac-extra-revenue').textContent = extraRevenue.toLocaleString('de-DE') + '€';
    document.getElementById('cac-roi').textContent = totalBenefit.toLocaleString('de-DE') + '€';
}

// ==================== ANIMATION 3: LEADERBOARD ====================
let userReferrals = 0;
const leaderboardData = [
    { name: 'Max M.', referrals: 12, badge: '🚀' },
    { name: 'Sarah K.', referrals: 9, badge: '🌟' },
    { name: 'Tom B.', referrals: 7, badge: '🌟' },
    { name: 'Lisa W.', referrals: 6, badge: '⭐' },
    { name: 'Jan P.', referrals: 5, badge: '⭐' },
];

const rewards = [
    { threshold: 1, name: 'Bonus-Modul', desc: 'Bei 1 Empfehlung', icon: '📚' },
    { threshold: 3, name: '30% Rabatt', desc: 'Bei 3 Empfehlungen', icon: '🎫' },
    { threshold: 5, name: 'VIP-Zugang', desc: 'Bei 5 Empfehlungen', icon: '👑' },
    { threshold: 10, name: '1:1 Call', desc: 'Bei 10 Empfehlungen', icon: '📞' },
];

const badges = [
    { threshold: 1, emoji: '🌱', name: 'Erster Schritt' },
    { threshold: 5, emoji: '⭐', name: '5er Club' },
    { threshold: 10, emoji: '🌟', name: '10er Club' },
    { threshold: 25, emoji: '🚀', name: 'Super-Werber' },
    { threshold: 50, emoji: '👑', name: 'Legende' },
];

function initLeaderboard() {
    const container = document.getElementById('leaderboard-list');
    container.innerHTML = '';
    
    leaderboardData.forEach((entry, index) => {
        const div = document.createElement('div');
        div.className = 'leaderboard-entry flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700 rounded-xl';
        div.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 ${index < 3 ? 'bg-gradient-to-br from-yellow-400 to-orange-500' : 'bg-gray-300 dark:bg-slate-600'} rounded-full flex items-center justify-center text-white font-bold text-sm">${index + 1}</div>
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-violet-500 rounded-full flex items-center justify-center text-white font-bold">${entry.name.charAt(0)}</div>
                <div>
                    <div class="font-semibold text-gray-900 dark:text-white">${entry.name}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">${entry.referrals} Empfehlungen</div>
                </div>
            </div>
            <div class="text-2xl">${entry.badge}</div>
        `;
        container.appendChild(div);
    });
}

function simulateReferral() {
    userReferrals++;
    
    // Update your position
    document.getElementById('your-referrals').textContent = userReferrals + ' Empfehlung' + (userReferrals > 1 ? 'en' : '');
    
    // Calculate rank
    let rank = 6;
    for (let i = 0; i < leaderboardData.length; i++) {
        if (userReferrals > leaderboardData[i].referrals) {
            rank = i + 1;
            break;
        }
    }
    
    const rankEl = document.getElementById('your-rank');
    if (rank <= 5) {
        rankEl.textContent = rank;
        rankEl.classList.add('animate-rank-up');
        setTimeout(() => rankEl.classList.remove('animate-rank-up'), 500);
    } else {
        rankEl.textContent = rank;
    }
    
    // Update badge
    let currentBadge = '🌱';
    for (const badge of badges) {
        if (userReferrals >= badge.threshold) {
            currentBadge = badge.emoji;
        }
    }
    document.getElementById('your-badge').textContent = currentBadge;
    
    // Check for badge unlock
    const badgeSlots = document.querySelectorAll('.badge-slot');
    badges.forEach((badge, index) => {
        if (userReferrals >= badge.threshold && !badgeSlots[index].classList.contains('earned')) {
            badgeSlots[index].classList.add('earned');
            showCelebration(badge.emoji, 'Badge freigeschaltet!', badge.name + ' erreicht!');
        }
    });
    
    // Update progress to next reward
    let nextReward = rewards[0];
    for (const reward of rewards) {
        if (userReferrals < reward.threshold) {
            nextReward = reward;
            break;
        }
    }
    
    document.getElementById('next-reward-icon').textContent = nextReward.icon;
    document.getElementById('next-reward-name').textContent = nextReward.name;
    document.getElementById('next-reward-desc').textContent = nextReward.desc;
    
    const prevThreshold = rewards.indexOf(nextReward) > 0 ? rewards[rewards.indexOf(nextReward) - 1].threshold : 0;
    const progress = Math.min(100, ((userReferrals - prevThreshold) / (nextReward.threshold - prevThreshold)) * 100);
    document.getElementById('progress-bar').style.width = progress + '%';
    document.getElementById('progress-text').textContent = userReferrals + '/' + nextReward.threshold;
    
    // Check for reward unlock
    for (const reward of rewards) {
        if (userReferrals === reward.threshold) {
            showCelebration(reward.icon, 'Belohnung freigeschaltet!', reward.name);
            createConfetti();
        }
    }
    
    // Button feedback
    const btn = document.getElementById('refer-btn');
    btn.innerHTML = '<i class="fas fa-check"></i> <span>+1 Empfehlung!</span>';
    btn.classList.add('bg-green-500');
    setTimeout(() => {
        btn.innerHTML = '<i class="fas fa-share"></i> <span>Empfehlung senden!</span>';
        btn.classList.remove('bg-green-500');
    }, 500);
}

function showCelebration(emoji, title, text) {
    const box = document.getElementById('celebration-box');
    document.getElementById('celebration-emoji').textContent = emoji;
    document.getElementById('celebration-title').textContent = title;
    document.getElementById('celebration-text').textContent = text;
    box.classList.remove('hidden');
    
    setTimeout(() => {
        box.classList.add('hidden');
    }, 3000);
}

function resetLeaderboard() {
    userReferrals = 0;
    document.getElementById('your-referrals').textContent = '0 Empfehlungen';
    document.getElementById('your-rank').textContent = '11';
    document.getElementById('your-badge').textContent = '🌱';
    
    document.querySelectorAll('.badge-slot').forEach(slot => {
        slot.classList.remove('earned');
    });
    
    document.getElementById('progress-bar').style.width = '0%';
    document.getElementById('progress-text').textContent = '0/1';
    document.getElementById('next-reward-icon').textContent = '📚';
    document.getElementById('next-reward-name').textContent = 'Bonus-Modul';
    document.getElementById('next-reward-desc').textContent = 'Bei 1 Empfehlung';
    
    document.getElementById('celebration-box').classList.add('hidden');
    
    initLeaderboard();
}

// ==================== HELPERS ====================
function createConfetti() {
    const colors = ['#6366f1', '#8b5cf6', '#a855f7', '#fbbf24', '#22c55e'];
    for (let i = 0; i < 50; i++) {
        const confetti = document.createElement('div');
        confetti.className = 'confetti';
        confetti.style.left = Math.random() * 100 + 'vw';
        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.animationDelay = Math.random() * 2 + 's';
        confetti.style.animationDuration = (2 + Math.random() * 2) + 's';
        confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
        document.body.appendChild(confetti);
        setTimeout(() => confetti.remove(), 5000);
    }
}

// ==================== INIT ====================
document.addEventListener('DOMContentLoaded', function() {
    updateCAC();
    initLeaderboard();
});
</script>

<!-- vs Affiliate Section -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-indigo-600 dark:text-indigo-400 font-semibold uppercase tracking-wide text-sm">Vergleich</span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mt-2 mb-4">
                Empfehlungsprogramm vs. Affiliate
            </h2>
        </div>
        
        <div class="grid md:grid-cols-2 gap-8">
            <!-- Affiliate -->
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 md:p-8 border-2 border-gray-200 dark:border-slate-600">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-gray-100 dark:bg-slate-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-link text-gray-500 dark:text-gray-400 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Affiliate-Programm</h3>
                </div>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-times text-red-500 mt-1"></i>
                        <span class="text-gray-600 dark:text-gray-300">Komplexe Provisionsabrechnungen</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-times text-red-500 mt-1"></i>
                        <span class="text-gray-600 dark:text-gray-300">Steuerliche Komplexität bei Auszahlungen</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-times text-red-500 mt-1"></i>
                        <span class="text-gray-600 dark:text-gray-300">Hoher Verwaltungsaufwand</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-times text-red-500 mt-1"></i>
                        <span class="text-gray-600 dark:text-gray-300">Oft für professionelle Marketer optimiert</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-times text-red-500 mt-1"></i>
                        <span class="text-gray-600 dark:text-gray-300">Risiko: Affiliate-Missbrauch</span>
                    </li>
                </ul>
            </div>
            
            <!-- Empfehlungsprogramm -->
            <div class="bg-gradient-to-br from-indigo-50 to-violet-50 dark:from-indigo-900/20 dark:to-violet-900/20 rounded-2xl p-6 md:p-8 border-2 border-indigo-300 dark:border-indigo-700 relative">
                <div class="absolute -top-3 right-6 bg-indigo-600 text-white text-xs font-bold px-3 py-1 rounded-full">
                    EMPFOHLEN
                </div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-heart text-indigo-600 dark:text-indigo-400 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Empfehlungsprogramm</h3>
                </div>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check text-green-500 mt-1"></i>
                        <span class="text-gray-700 dark:text-gray-200">Einfache Sachprämien statt Geld</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check text-green-500 mt-1"></i>
                        <span class="text-gray-700 dark:text-gray-200">Keine steuerlichen Komplikationen</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check text-green-500 mt-1"></i>
                        <span class="text-gray-700 dark:text-gray-200">Vollautomatisch – null Aufwand</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check text-green-500 mt-1"></i>
                        <span class="text-gray-700 dark:text-gray-200">Für normale Kursteilnehmer optimiert</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check text-green-500 mt-1"></i>
                        <span class="text-gray-700 dark:text-gray-200">Gamification motiviert langfristig</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Belohnungen Section -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            <div>
                <span class="text-indigo-600 dark:text-indigo-400 font-semibold uppercase tracking-wide text-sm">Belohnungssystem</span>
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mt-2 mb-6">
                    Digitale Belohnungen mit null Grenzkosten
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-lg mb-8">
                    Belohnen Sie Empfehlungen mit dem, was Sie sowieso haben – Wissen:
                </p>
                
                <div class="space-y-4">
                    <?php foreach ($belohnungen as $b): ?>
                    <div class="flex items-center gap-4 bg-white dark:bg-slate-700 rounded-xl p-4 shadow-sm">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-violet-500 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                            <?= $b['stufe'] ?>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400"><?= $b['stufe'] ?> Empfehlung<?= $b['stufe'] > 1 ? 'en' : '' ?></div>
                            <div class="font-semibold text-gray-900 dark:text-white"><?= $b['belohnung'] ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="mt-8 p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl border border-indigo-200 dark:border-indigo-700/30">
                    <h4 class="font-semibold text-indigo-900 dark:text-indigo-300 mb-2">
                        <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                        Ideen für digitale Belohnungen:
                    </h4>
                    <ul class="text-sm text-indigo-800 dark:text-indigo-200 space-y-1">
                        <li>• Bonus-Module & Masterclasses</li>
                        <li>• Exklusive Templates & Vorlagen</li>
                        <li>• Private Community-Zugang</li>
                        <li>• 1:1 Q&A Sessions</li>
                        <li>• Lifetime-Updates für alle Kurse</li>
                    </ul>
                </div>
            </div>
            
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 md:p-8 shadow-lg border border-gray-200 dark:border-slate-600">
                <div class="text-center mb-6">
                    <div class="text-5xl mb-3">🎓</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Belohnung freigeschaltet!</h3>
                    <p class="text-gray-500 dark:text-gray-400">Du hast Stufe 1 erreicht</p>
                </div>
                
                <div class="bg-gradient-to-r from-indigo-50 to-violet-50 dark:from-indigo-900/20 dark:to-violet-900/10 rounded-xl p-5 border border-indigo-200 dark:border-indigo-700/30 mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-indigo-500 rounded-full flex items-center justify-center text-2xl">
                            📚
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 dark:text-white text-lg">Bonus-Modul freigeschaltet</div>
                            <div class="text-gray-600 dark:text-gray-300">"Advanced Funnel Strategies"</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 dark:bg-slate-800 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Dein Fortschritt</span>
                        <span class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">1/3 zur nächsten Stufe</span>
                    </div>
                    <div class="h-3 bg-gray-200 dark:bg-slate-600 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-indigo-500 to-violet-500 rounded-full" style="width: 33%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonial Section -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-br from-indigo-50 to-violet-50 dark:from-slate-800 dark:to-slate-700 rounded-2xl p-8 md:p-12 text-center">
            <div class="flex justify-center gap-1 text-yellow-400 mb-6">
                <?php for ($i = 0; $i < 5; $i++): ?>
                <i class="fas fa-star text-xl"></i>
                <?php endfor; ?>
            </div>
            
            <blockquote class="text-xl md:text-2xl font-medium text-gray-900 dark:text-white mb-8 leading-relaxed">
                "<?= $testimonial['text'] ?>"
            </blockquote>
            
            <div class="flex items-center justify-center gap-4">
                <div class="w-14 h-14 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                    <?= $testimonial['initialen'] ?>
                </div>
                <div class="text-left">
                    <div class="font-bold text-gray-900 dark:text-white"><?= $testimonial['name'] ?></div>
                    <div class="text-gray-600 dark:text-gray-400"><?= $testimonial['rolle'] ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Use Cases -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Perfekt für jeden Online-Kurs-Bereich
            </h2>
        </div>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php 
            $useCases = [
                ['icon' => 'fa-chart-line', 'title' => 'Online-Marketing Kurse', 'text' => 'SEO, Ads, Social Media – Erfolge werden geteilt'],
                ['icon' => 'fa-code', 'title' => 'Programmier-Kurse', 'text' => 'Entwickler empfehlen gerne gute Ressourcen'],
                ['icon' => 'fa-briefcase', 'title' => 'Business & Finance', 'text' => 'Unternehmer vernetzen sich und empfehlen'],
                ['icon' => 'fa-camera', 'title' => 'Kreativ-Kurse', 'text' => 'Fotografie, Design, Video – kreative Community'],
                ['icon' => 'fa-heart', 'title' => 'Health & Lifestyle', 'text' => 'Transformation wird gerne weitererzählt'],
                ['icon' => 'fa-language', 'title' => 'Sprach-Kurse', 'text' => 'Lernerfolge motivieren zum Teilen'],
            ];
            foreach ($useCases as $useCase): ?>
            <div class="bg-white dark:bg-slate-700 rounded-xl p-5 flex items-start gap-4 hover:shadow-lg transition-shadow">
                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400 flex-shrink-0">
                    <i class="fas <?= $useCase['icon'] ?>"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-1"><?= $useCase['title'] ?></h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm"><?= $useCase['text'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-12 md:py-20 bg-gradient-to-r from-indigo-600 to-violet-700 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold mb-4 md:mb-6">
            Bereit für mehr Kursteilnehmer durch Empfehlungen?
        </h2>
        <p class="text-lg md:text-xl text-white/90 mb-6 md:mb-8">
            Starten Sie noch heute und machen Sie Ihre Absolventen zu Botschaftern.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/onboarding" class="btn-white btn-large inline-flex items-center justify-center gap-2">
                <span>Jetzt 7 Tage kostenlos testen</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <p class="text-white/70 mt-6 text-sm">
            Keine Kreditkarte erforderlich · E-Mail-Tool Export inklusive · DSGVO-konform
        </p>
    </div>
</section>

<!-- Andere Branchen -->
<section class="py-12 md:py-16 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h3 class="text-center text-lg font-semibold text-gray-900 dark:text-white mb-8">
            Leadbusiness für andere Branchen
        </h3>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="/branchen/zahnarzt" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-full text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors text-sm font-medium">
                <i class="fas fa-tooth mr-1"></i> Zahnärzte
            </a>
            <a href="/branchen/friseur" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-full text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors text-sm font-medium">
                <i class="fas fa-cut mr-1"></i> Friseure
            </a>
            <a href="/branchen/fitness" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-full text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors text-sm font-medium">
                <i class="fas fa-dumbbell mr-1"></i> Fitnessstudios
            </a>
            <a href="/branchen/restaurant" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-full text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors text-sm font-medium">
                <i class="fas fa-utensils mr-1"></i> Restaurants
            </a>
            <a href="/branchen/coach" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-full text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors text-sm font-medium">
                <i class="fas fa-lightbulb mr-1"></i> Coaches
            </a>
            <a href="/branchen/onlineshop" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-full text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors text-sm font-medium">
                <i class="fas fa-shopping-cart mr-1"></i> Online-Shops
            </a>
            <a href="/branchen/handwerker" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-full text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors text-sm font-medium">
                <i class="fas fa-hammer mr-1"></i> Handwerker
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../templates/marketing/footer.php'; ?>
