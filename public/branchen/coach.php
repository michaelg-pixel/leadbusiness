<?php
/**
 * Branchenseite: Coaches & Berater
 * Mit drei interaktiven Animationen
 */

$pageTitle = 'Empfehlungsprogramm für Coaches & Berater';
$metaDescription = 'Automatisches Empfehlungsprogramm für Coaches, Berater und Trainer. Zufriedene Klienten empfehlen Sie weiter und werden automatisch belohnt.';
$currentPage = 'branchen';

require_once __DIR__ . '/../../templates/marketing/header.php';

// Branchenspezifische Daten
$branche = [
    'name' => 'Coaches & Berater',
    'slug' => 'coach',
    'icon' => 'fa-lightbulb',
    'color' => 'purple',
    'heroTitle' => 'Mehr Klienten durch Empfehlungen',
    'heroSubtitle' => 'Coaching lebt von Vertrauen. Und nichts baut Vertrauen schneller auf als eine persönliche Empfehlung von jemandem, der bereits Ergebnisse erzielt hat.',
];

$vorteile = [
    [
        'icon' => 'fa-handshake',
        'title' => 'Vorqualifizierte Klienten',
        'text' => 'Empfohlene Klienten kommen mit Vertrauen und sind bereits überzeugt von Ihrem Ansatz.'
    ],
    [
        'icon' => 'fa-chart-line',
        'title' => 'Höhere Abschlussquote',
        'text' => 'Empfehlungsklienten haben eine 70% höhere Abschlussquote als Kaltakquise-Leads.'
    ],
    [
        'icon' => 'fa-clock',
        'title' => 'Weniger Einwände',
        'text' => 'Die Empfehlung eines Freundes eliminiert typische Einwände zu Preis und Wirksamkeit.'
    ],
    [
        'icon' => 'fa-rocket',
        'title' => 'Skalierung ohne Ads',
        'text' => 'Bauen Sie Ihr Coaching-Business organisch auf – ohne teure Werbeanzeigen.'
    ],
];

$belohnungen = [
    ['stufe' => 1, 'belohnung' => 'Gratis E-Book oder Workbook'],
    ['stufe' => 3, 'belohnung' => '30-Min Bonus-Coaching-Call'],
    ['stufe' => 5, 'belohnung' => '50% Rabatt auf nächstes Coaching-Paket'],
];

$testimonial = [
    'text' => 'Als Life Coach lebte ich von Mundpropaganda – aber ohne System. Mit Leadbusiness habe ich endlich einen strukturierten Prozess. 60% meiner Neukunden kommen jetzt über Empfehlungen.',
    'name' => 'Julia Weber',
    'rolle' => 'Life & Business Coach, München',
    'initialen' => 'JW',
];

$stats = [
    'empfehler' => '167',
    'conversions' => '78',
    'rate' => '47%',
];
?>

<!-- Hero Section -->
<section class="relative py-16 md:py-24 overflow-hidden">
    <!-- Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-purple-600 to-indigo-700"></div>
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
                        <span>DSGVO-konform</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-400"></i>
                        <span>Perfekt für 1:1 & Gruppen</span>
                    </div>
                </div>
            </div>
            
            <!-- Visual -->
            <div class="hidden lg:block">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="bg-white rounded-xl shadow-2xl p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-lightbulb text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">Julia Weber Coaching</div>
                                <div class="text-sm text-gray-500">empfohlen.de/julia-weber</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600"><?= $stats['empfehler'] ?></div>
                                <div class="text-xs text-gray-500">Empfehler</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600"><?= $stats['conversions'] ?></div>
                                <div class="text-xs text-gray-500">Neuklienten</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600"><?= $stats['rate'] ?></div>
                                <div class="text-xs text-gray-500">Conversion</div>
                            </div>
                        </div>
                        <div class="text-center text-sm text-gray-500">
                            <i class="fas fa-award text-purple-500 mr-1"></i>
                            Top 10% der Empfehlungsprogramme
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
                Warum Empfehlungsmarketing für <?= $branche['name'] ?>?
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg max-w-2xl mx-auto">
                Ihre Transformation spricht für sich – lassen Sie Ihre Klienten davon erzählen.
            </p>
        </div>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
            <?php foreach ($vorteile as $vorteil): ?>
            <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-6 hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center text-purple-600 dark:text-purple-400 text-xl mb-4">
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
            <span class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-5 py-2 rounded-full text-sm font-bold shadow-lg mb-4">
                <span>🎯</span> Live erleben
            </span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                So funktioniert Empfehlungsmarketing für Coaches
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg max-w-2xl mx-auto">
                Drei interaktive Demos zeigen, wie aus Vertrauen Wachstum entsteht.
            </p>
        </div>
        
        <!-- Tab Navigation -->
        <div class="flex flex-wrap justify-center gap-3 mb-8" id="coach-animation-tabs">
            <button onclick="showCoachAnimation('journey')" id="tab-journey" class="coach-tab active px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-gradient-to-r from-purple-500 to-indigo-600 text-white shadow-lg">
                🎯 Transformations-Journey
            </button>
            <button onclick="showCoachAnimation('roi')" id="tab-roi" class="coach-tab px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-white dark:bg-slate-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-600 hover:shadow-md">
                ⏱️ Zeit-ROI-Rechner
            </button>
            <button onclick="showCoachAnimation('trust')" id="tab-trust" class="coach-tab px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-white dark:bg-slate-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-600 hover:shadow-md">
                🌐 Vertrauens-Netzwerk
            </button>
        </div>
        
        <!-- Animation Containers -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-8 min-h-[600px] shadow-lg">
            
            <!-- ========================================= -->
            <!-- ANIMATION 1: TRANSFORMATIONS-JOURNEY     -->
            <!-- ========================================= -->
            <div id="animation-journey" class="coach-animation-content">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Die Transformations-Journey</h3>
                    <p class="text-gray-500 dark:text-gray-400">Von der Empfehlung zum Durchbruch – und wieder zurück</p>
                </div>
                
                <div class="max-w-3xl mx-auto">
                    <!-- Progress Line -->
                    <div class="relative mb-8">
                        <div class="absolute top-8 left-0 right-0 h-1 bg-gray-200 dark:bg-slate-700 rounded-full">
                            <div id="journey-progress" class="h-full bg-gradient-to-r from-purple-500 to-indigo-500 rounded-full transition-all duration-500" style="width: 0%"></div>
                        </div>
                        
                        <!-- Journey Steps -->
                        <div class="grid grid-cols-6 gap-2 relative">
                            <!-- Step 1: Empfehlung -->
                            <div id="journey-step-1" class="journey-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">
                                    💬
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Empfehlung</span>
                            </div>
                            
                            <!-- Step 2: Erstgespräch -->
                            <div id="journey-step-2" class="journey-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">
                                    📞
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Erstgespräch</span>
                            </div>
                            
                            <!-- Step 3: Coaching -->
                            <div id="journey-step-3" class="journey-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">
                                    🎯
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Coaching</span>
                            </div>
                            
                            <!-- Step 4: Durchbruch -->
                            <div id="journey-step-4" class="journey-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">
                                    💡
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Durchbruch</span>
                            </div>
                            
                            <!-- Step 5: Erfolg -->
                            <div id="journey-step-5" class="journey-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">
                                    🌟
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Erfolg</span>
                            </div>
                            
                            <!-- Step 6: Weiterempfehlung -->
                            <div id="journey-step-6" class="journey-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">
                                    ♻️
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Empfiehlt!</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mindset Progress Bar -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Mindset-Level</span>
                            <span id="mindset-emoji" class="text-2xl">🌱</span>
                        </div>
                        <div class="h-4 bg-gray-200 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div id="mindset-bar" class="h-full bg-gradient-to-r from-purple-400 via-purple-500 to-indigo-600 rounded-full transition-all duration-1000" style="width: 10%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-400 mt-1">
                            <span>Zweifel</span>
                            <span>Klarheit</span>
                            <span>Action</span>
                            <span>Erfolg</span>
                        </div>
                    </div>
                    
                    <!-- Client Avatar Animation -->
                    <div class="relative h-24 mb-6 overflow-hidden bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 rounded-xl">
                        <div id="journey-avatar" class="absolute transition-all duration-1000 ease-out flex items-center gap-3" style="left: 5%; top: 50%; transform: translateY(-50%)">
                            <div class="text-5xl" id="avatar-emoji">😟</div>
                            <div id="avatar-thought" class="bg-white dark:bg-slate-700 rounded-lg px-3 py-2 text-sm shadow-md opacity-0 transition-opacity duration-300">
                                <span id="thought-text">Ob Coaching mir hilft?</span>
                            </div>
                        </div>
                        <!-- Floating effects container -->
                        <div id="journey-effects" class="absolute inset-0 pointer-events-none"></div>
                    </div>
                    
                    <!-- Info Box -->
                    <div id="journey-info" class="bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 rounded-xl p-4 mb-6 text-center">
                        <div id="journey-text" class="text-gray-700 dark:text-gray-300 font-medium">
                            Klicken Sie, um die Transformation zu starten!
                        </div>
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-4 text-center">
                            <div id="journey-clients" class="text-2xl font-bold text-purple-600 dark:text-purple-400">0</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Neue Klienten</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-4 text-center">
                            <div id="journey-trust" class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">0%</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Vertrauens-Start</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-4 text-center">
                            <div id="journey-reward" class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">-</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Belohnung</div>
                        </div>
                    </div>
                    
                    <!-- Action Button -->
                    <button onclick="startJourney()" id="journey-btn" class="w-full py-4 bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-xl font-bold text-lg hover:shadow-lg hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-play"></i>
                        <span>Transformation starten</span>
                    </button>
                    
                    <!-- Reset -->
                    <button onclick="resetJourney()" class="w-full mt-3 py-2 text-gray-500 dark:text-gray-400 text-sm hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                        ↻ Demo zurücksetzen
                    </button>
                </div>
            </div>
            
            <!-- ========================================= -->
            <!-- ANIMATION 2: ZEIT-ROI-RECHNER            -->
            <!-- ========================================= -->
            <div id="animation-roi" class="coach-animation-content hidden">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Zeit-ROI-Rechner</h3>
                    <p class="text-gray-500 dark:text-gray-400">So viel Zeit gewinnen Sie mit Empfehlungen statt Social Media Akquise</p>
                </div>
                
                <div class="max-w-2xl mx-auto">
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-slate-800 dark:to-slate-700 rounded-2xl p-6 md:p-8">
                        
                        <!-- Slider: Coaching-Preis -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Was kostet Ihr Coaching-Paket?
                            </label>
                            <div class="flex items-center gap-4">
                                <input type="range" id="roi-price-slider" min="500" max="5000" step="100" value="2000" class="flex-1 h-3 bg-gray-300 dark:bg-slate-600 rounded-full appearance-none cursor-pointer accent-purple-500" oninput="updateROI()">
                                <div class="bg-white dark:bg-slate-600 rounded-xl px-4 py-2 min-w-[100px] text-center">
                                    <span id="roi-price" class="text-2xl font-bold text-gray-900 dark:text-white">2.000€</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Slider: Gewünschte Neukunden -->
                        <div class="mb-8">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Wie viele Neuklienten pro Monat?
                            </label>
                            <div class="flex items-center gap-4">
                                <input type="range" id="roi-clients-slider" min="1" max="20" value="5" class="flex-1 h-3 bg-gray-300 dark:bg-slate-600 rounded-full appearance-none cursor-pointer accent-purple-500" oninput="updateROI()">
                                <div class="bg-white dark:bg-slate-600 rounded-xl px-4 py-2 min-w-[80px] text-center">
                                    <span id="roi-clients" class="text-2xl font-bold text-gray-900 dark:text-white">5</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Comparison Cards -->
                        <div class="grid md:grid-cols-2 gap-6 mb-8">
                            <!-- Social Media Akquise -->
                            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 border-2 border-red-200 dark:border-red-900/50">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-orange-500 rounded-xl flex items-center justify-center">
                                        <i class="fab fa-instagram text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white">Social Media</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Content & DMs</div>
                                    </div>
                                </div>
                                
                                <!-- Time Visualization -->
                                <div class="mb-4">
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Zeitaufwand pro Neuklient:</div>
                                    <div id="roi-social-clocks" class="flex flex-wrap gap-1 min-h-[60px]">
                                        <!-- Clock icons will be added by JS -->
                                    </div>
                                </div>
                                
                                <div class="text-center border-t pt-4">
                                    <div class="text-3xl font-black text-red-600 dark:text-red-400" id="roi-social-hours">40h</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Stunden/Monat für Akquise</div>
                                </div>
                            </div>
                            
                            <!-- Empfehlungen -->
                            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 border-2 border-purple-400 dark:border-purple-500 relative overflow-hidden">
                                <!-- Winner Badge -->
                                <div class="absolute top-2 right-2 bg-purple-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                    EMPFOHLEN
                                </div>
                                
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-users text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white">Empfehlungen</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Läuft automatisch</div>
                                    </div>
                                </div>
                                
                                <!-- Time Visualization (smaller) -->
                                <div class="mb-4">
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Zeitaufwand pro Neuklient:</div>
                                    <div id="roi-referral-clocks" class="flex flex-wrap gap-1 min-h-[60px]">
                                        <!-- Clock icons will be added by JS -->
                                    </div>
                                </div>
                                
                                <div class="text-center border-t pt-4">
                                    <div class="text-3xl font-black text-purple-600 dark:text-purple-400" id="roi-referral-hours">5h</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Stunden/Monat für Setup</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Time Saved Result -->
                        <div id="roi-savings-box" class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-2xl p-6 text-white text-center transform transition-all duration-500">
                            <div class="text-lg mb-2">⏱️ Ihre Zeitersparnis</div>
                            <div class="text-5xl font-black mb-2" id="roi-time-saved">35h</div>
                            <div class="text-xl text-white/90 mb-3">
                                Das sind <span id="roi-sessions" class="font-bold">35 Coaching-Sessions</span> mehr!
                            </div>
                            <div class="bg-white/20 rounded-lg px-4 py-2 inline-block">
                                <span class="text-2xl font-bold" id="roi-extra-revenue">+ 3.500€</span>
                                <span class="text-white/80 ml-2">zusätzlicher Umsatz</span>
                            </div>
                        </div>
                        
                        <!-- Pro Tip -->
                        <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-4 flex items-start gap-3">
                            <div class="text-2xl">💡</div>
                            <div class="text-sm text-yellow-800 dark:text-yellow-200">
                                <strong>Realität:</strong> Die meisten Coaches verbringen 8-10 Stunden pro Woche nur mit Content-Erstellung und DMs. Mit Empfehlungen investieren Sie diese Zeit in das, was Sie lieben: Coaching!
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ========================================= -->
            <!-- ANIMATION 3: VERTRAUENS-NETZWERK         -->
            <!-- ========================================= -->
            <div id="animation-trust" class="coach-animation-content hidden">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Vertrauens-Netzwerk</h3>
                    <p class="text-gray-500 dark:text-gray-400">Sehen Sie, wie Vertrauen sich durch Empfehlungen exponentiell vermehrt!</p>
                </div>
                
                <div class="max-w-3xl mx-auto">
                    <!-- Network Visualization -->
                    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-slate-800 dark:to-slate-700 rounded-2xl p-6 md:p-8 mb-6">
                        
                        <!-- Stats Bar -->
                        <div class="grid grid-cols-4 gap-3 mb-6">
                            <div class="bg-white dark:bg-slate-600 rounded-xl p-3 text-center">
                                <div id="trust-level" class="text-2xl font-bold text-purple-600 dark:text-purple-400">0</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Ebene</div>
                            </div>
                            <div class="bg-white dark:bg-slate-600 rounded-xl p-3 text-center">
                                <div id="trust-clients" class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">0</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Klienten</div>
                            </div>
                            <div class="bg-white dark:bg-slate-600 rounded-xl p-3 text-center">
                                <div id="trust-score" class="text-2xl font-bold text-green-600 dark:text-green-400">0%</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Vertrauens-Score</div>
                            </div>
                            <div class="bg-white dark:bg-slate-600 rounded-xl p-3 text-center">
                                <div id="trust-conversion" class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">-</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Abschlussquote</div>
                            </div>
                        </div>
                        
                        <!-- Network Container -->
                        <div id="trust-network" class="min-h-[320px] flex flex-col items-center justify-start py-4 relative">
                            <!-- Center Node (Coach) -->
                            <div id="trust-center" class="relative z-10 mb-2">
                                <div class="trust-node coach-node w-24 h-24 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-full flex flex-col items-center justify-center text-white shadow-xl transform transition-all duration-500">
                                    <div class="text-3xl">👩‍🏫</div>
                                    <div class="text-xs font-bold mt-1">Sie</div>
                                </div>
                                <!-- Trust Ring -->
                                <div id="trust-ring" class="absolute inset-0 rounded-full border-4 border-purple-300 dark:border-purple-700 opacity-0 scale-100 transition-all duration-500"></div>
                            </div>
                            
                            <!-- Level 1: Direct Clients (Kaltakquise) -->
                            <div id="trust-level-1" class="flex justify-center gap-6 mt-2 opacity-0 transform translate-y-4 transition-all duration-500">
                                <div class="trust-branch flex flex-col items-center">
                                    <div class="w-1 h-6 bg-gray-300 dark:bg-slate-600"></div>
                                    <div class="trust-node w-16 h-16 bg-white dark:bg-slate-600 rounded-full flex flex-col items-center justify-center shadow-lg border-3 border-gray-300 dark:border-slate-500">
                                        <div class="text-xl">😐</div>
                                        <div class="text-[8px] text-gray-500">20% Trust</div>
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">Kaltakquise</div>
                                </div>
                                <div class="trust-branch flex flex-col items-center">
                                    <div class="w-1 h-6 bg-gray-300 dark:bg-slate-600"></div>
                                    <div class="trust-node w-16 h-16 bg-white dark:bg-slate-600 rounded-full flex flex-col items-center justify-center shadow-lg border-3 border-gray-300 dark:border-slate-500">
                                        <div class="text-xl">😐</div>
                                        <div class="text-[8px] text-gray-500">20% Trust</div>
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">Kaltakquise</div>
                                </div>
                            </div>
                            
                            <!-- Level 2: Empfohlene Klienten -->
                            <div id="trust-level-2" class="flex justify-center gap-4 mt-4 opacity-0 transform translate-y-4 transition-all duration-500">
                                <div class="trust-branch flex flex-col items-center">
                                    <div class="w-1 h-5 bg-purple-400"></div>
                                    <div class="trust-node w-14 h-14 bg-white dark:bg-slate-600 rounded-full flex flex-col items-center justify-center shadow-md border-3 border-purple-400">
                                        <div class="text-lg">😊</div>
                                        <div class="text-[8px] text-purple-600">70% Trust</div>
                                    </div>
                                </div>
                                <div class="trust-branch flex flex-col items-center">
                                    <div class="w-1 h-5 bg-purple-400"></div>
                                    <div class="trust-node w-14 h-14 bg-white dark:bg-slate-600 rounded-full flex flex-col items-center justify-center shadow-md border-3 border-purple-400">
                                        <div class="text-lg">😊</div>
                                        <div class="text-[8px] text-purple-600">70% Trust</div>
                                    </div>
                                </div>
                                <div class="trust-branch flex flex-col items-center">
                                    <div class="w-1 h-5 bg-purple-400"></div>
                                    <div class="trust-node w-14 h-14 bg-white dark:bg-slate-600 rounded-full flex flex-col items-center justify-center shadow-md border-3 border-purple-400">
                                        <div class="text-lg">😊</div>
                                        <div class="text-[8px] text-purple-600">70% Trust</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Level 3: Empfehlungen von Empfehlungen -->
                            <div id="trust-level-3" class="flex justify-center gap-2 mt-4 opacity-0 transform translate-y-4 transition-all duration-500">
                                <div class="trust-node w-11 h-11 bg-white dark:bg-slate-600 rounded-full flex items-center justify-center shadow border-2 border-indigo-400 text-sm">😄</div>
                                <div class="trust-node w-11 h-11 bg-white dark:bg-slate-600 rounded-full flex items-center justify-center shadow border-2 border-indigo-400 text-sm">😄</div>
                                <div class="trust-node w-11 h-11 bg-white dark:bg-slate-600 rounded-full flex items-center justify-center shadow border-2 border-indigo-400 text-sm">😄</div>
                                <div class="trust-node w-11 h-11 bg-white dark:bg-slate-600 rounded-full flex items-center justify-center shadow border-2 border-indigo-400 text-sm">😄</div>
                                <div class="trust-node w-11 h-11 bg-white dark:bg-slate-600 rounded-full flex items-center justify-center shadow border-2 border-indigo-400 text-sm">😄</div>
                                <div class="trust-node w-11 h-11 bg-white dark:bg-slate-600 rounded-full flex items-center justify-center shadow border-2 border-indigo-400 text-sm">😄</div>
                            </div>
                            
                            <!-- Trust Burst Effects -->
                            <div id="trust-effects" class="absolute inset-0 pointer-events-none overflow-hidden"></div>
                        </div>
                        
                        <!-- Comparison Box -->
                        <div id="trust-comparison" class="grid grid-cols-2 gap-4 mt-4">
                            <div class="bg-white dark:bg-slate-700 rounded-xl p-4 text-center border-2 border-gray-200 dark:border-slate-600">
                                <div class="text-gray-500 dark:text-gray-400 text-sm mb-1">Kaltakquise</div>
                                <div class="text-2xl font-bold text-gray-600 dark:text-gray-400" id="trust-cold-rate">20%</div>
                                <div class="text-xs text-gray-400">Abschlussquote</div>
                            </div>
                            <div class="bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-purple-900/30 dark:to-indigo-900/30 rounded-xl p-4 text-center border-2 border-purple-400">
                                <div class="text-purple-600 dark:text-purple-400 text-sm mb-1 font-medium">Empfehlung</div>
                                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400" id="trust-warm-rate">70%</div>
                                <div class="text-xs text-purple-500 dark:text-purple-300">Abschlussquote</div>
                            </div>
                        </div>
                        
                        <!-- Info Text -->
                        <div id="trust-info" class="text-center bg-white dark:bg-slate-600 rounded-xl p-4 mt-4">
                            <div id="trust-text" class="text-gray-700 dark:text-gray-300 font-medium">
                                Klicken Sie, um das Vertrauens-Netzwerk aufzubauen!
                            </div>
                        </div>
                        
                        <!-- Action Button -->
                        <button onclick="triggerTrustNetwork()" id="trust-btn" class="w-full py-4 mt-4 bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-xl font-bold text-lg hover:shadow-lg hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-network-wired"></i>
                            <span>Netzwerk aufbauen</span>
                        </button>
                        
                        <!-- Reset -->
                        <button onclick="resetTrustNetwork()" class="w-full mt-3 py-2 text-gray-500 dark:text-gray-400 text-sm hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                            ↻ Demo zurücksetzen
                        </button>
                    </div>
                    
                    <!-- Final Celebration -->
                    <div id="trust-celebration" class="hidden">
                        <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl p-6 text-white text-center animate-bounce-in">
                            <div class="text-4xl mb-2">🏆✨🎯</div>
                            <div class="font-black text-xl mb-2">EXPERTENSTATUS ERREICHT!</div>
                            <div class="text-white/90">Ihr Vertrauens-Netzwerk wächst exponentiell</div>
                            <div class="mt-3 grid grid-cols-3 gap-2 text-sm">
                                <div class="bg-white/20 rounded-lg px-3 py-2">
                                    <div class="font-bold">11</div>
                                    <div class="text-xs text-white/70">Klienten</div>
                                </div>
                                <div class="bg-white/20 rounded-lg px-3 py-2">
                                    <div class="font-bold">70%+</div>
                                    <div class="text-xs text-white/70">Abschluss</div>
                                </div>
                                <div class="bg-white/20 rounded-lg px-3 py-2">
                                    <div class="font-bold">0€</div>
                                    <div class="text-xs text-white/70">Ad-Kosten</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Animation Styles -->
<style>
    /* Keyframes */
    @keyframes bounceIn {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes pulse-purple {
        0%, 100% { box-shadow: 0 0 0 0 rgba(139, 92, 246, 0.7); }
        50% { box-shadow: 0 0 0 20px rgba(139, 92, 246, 0); }
    }
    @keyframes float-up {
        0% { opacity: 1; transform: translateY(0) scale(1); }
        100% { opacity: 0; transform: translateY(-60px) scale(0.5); }
    }
    @keyframes trust-burst {
        0% { transform: scale(0); opacity: 1; }
        100% { transform: scale(3); opacity: 0; }
    }
    @keyframes confetti-fall {
        0% { transform: translateY(-100%) rotate(0deg); opacity: 1; }
        100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
    }
    @keyframes glow {
        0%, 100% { filter: drop-shadow(0 0 5px rgba(139, 92, 246, 0.5)); }
        50% { filter: drop-shadow(0 0 20px rgba(139, 92, 246, 0.8)); }
    }
    @keyframes thought-appear {
        0% { opacity: 0; transform: translateX(-10px); }
        100% { opacity: 1; transform: translateX(0); }
    }
    
    /* Animation Classes */
    .animate-bounce-in { animation: bounceIn 0.5s ease forwards; }
    .animate-pulse-purple { animation: pulse-purple 2s ease-in-out infinite; }
    .animate-glow { animation: glow 2s ease-in-out infinite; }
    
    /* Tab Styling */
    .coach-tab.active {
        background: linear-gradient(to right, #8b5cf6, #6366f1);
        color: white;
        box-shadow: 0 10px 15px -3px rgba(139, 92, 246, 0.3);
        border: none;
    }
    
    /* Journey step styles */
    .journey-step.active {
        opacity: 1 !important;
    }
    .journey-step.active > div:first-child {
        background: linear-gradient(to bottom right, #8b5cf6, #6366f1) !important;
        transform: scale(1.1);
        box-shadow: 0 10px 25px -5px rgba(139, 92, 246, 0.5);
    }
    .journey-step.completed {
        opacity: 1 !important;
    }
    .journey-step.completed > div:first-child {
        background: #ede9fe !important;
    }
    .dark .journey-step.completed > div:first-child {
        background: #4c1d95 !important;
    }
    
    /* Trust node new animation */
    .trust-node.new {
        animation: bounceIn 0.5s ease forwards, pulse-purple 1s ease-in-out;
    }
    
    /* Trust ring pulse */
    .trust-ring-active {
        animation: pulse-purple 1.5s ease-in-out infinite;
        opacity: 1 !important;
    }
    
    /* Range slider styling */
    input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 24px;
        height: 24px;
        background: linear-gradient(to bottom right, #8b5cf6, #6366f1);
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
    input[type="range"]::-moz-range-thumb {
        width: 24px;
        height: 24px;
        background: linear-gradient(to bottom right, #8b5cf6, #6366f1);
        border-radius: 50%;
        cursor: pointer;
        border: none;
    }
    
    /* Confetti */
    .confetti {
        position: fixed;
        width: 10px;
        height: 10px;
        top: -10px;
        animation: confetti-fall 3s linear forwards;
        z-index: 100;
    }
    
    /* Clock icon */
    .clock-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }
    
    /* Border width fix */
    .border-3 {
        border-width: 3px;
    }
</style>

<!-- Animation JavaScript -->
<script>
// ==================== TAB SWITCHING ====================
function showCoachAnimation(type) {
    // Hide all animation contents
    document.querySelectorAll('.coach-animation-content').forEach(el => el.classList.add('hidden'));
    document.getElementById('animation-' + type).classList.remove('hidden');
    
    // Update tab styles
    document.querySelectorAll('.coach-tab').forEach(tab => {
        tab.classList.remove('active');
        tab.classList.add('bg-white', 'dark:bg-slate-700', 'text-gray-600', 'dark:text-gray-300', 'border', 'border-gray-200', 'dark:border-slate-600');
    });
    const activeTab = document.getElementById('tab-' + type);
    activeTab.classList.add('active');
    activeTab.classList.remove('bg-white', 'dark:bg-slate-700', 'text-gray-600', 'dark:text-gray-300', 'border', 'border-gray-200', 'dark:border-slate-600');
    
    // Initialize animations
    if (type === 'journey') resetJourney();
    if (type === 'roi') updateROI();
    if (type === 'trust') resetTrustNetwork();
}

// ==================== ANIMATION 1: TRANSFORMATIONS-JOURNEY ====================
let journeyStep = 0;
let journeyInterval = null;
const journeySteps = [
    { 
        text: '💬 "Meine Freundin hat mir von dir erzählt..."', 
        progress: 16,
        mindset: 25,
        emoji: '🤔',
        thought: 'Sie hat so gute Erfahrungen gemacht...',
        avatar: '😟'
    },
    { 
        text: '📞 Erstgespräch: "Ich verstehe deine Situation..."', 
        progress: 32,
        mindset: 40,
        emoji: '🌱',
        thought: 'Er/Sie versteht mich wirklich!',
        avatar: '🙂',
        trust: '70%'
    },
    { 
        text: '🎯 Coaching läuft: Tiefe Arbeit an den Themen...', 
        progress: 48,
        mindset: 60,
        emoji: '🌿',
        thought: 'Da ist was in Bewegung!',
        avatar: '😊',
        client: true
    },
    { 
        text: '💡 DER DURCHBRUCH! Ein Aha-Moment verändert alles.', 
        progress: 64,
        mindset: 80,
        emoji: '✨',
        thought: 'WOW! Jetzt verstehe ich es!',
        avatar: '😃'
    },
    { 
        text: '🌟 TRANSFORMATION KOMPLETT! Ziele erreicht!', 
        progress: 80,
        mindset: 95,
        emoji: '🌟',
        thought: 'Das hätte ich nie gedacht möglich!',
        avatar: '🤩',
        reward: 'E-Book'
    },
    { 
        text: '♻️ "Das muss ich weitererzählen!" - Der Kreislauf beginnt von vorn.', 
        progress: 100,
        mindset: 100,
        emoji: '🚀',
        thought: 'Meine Freunde müssen das wissen!',
        avatar: '🥳',
        complete: true
    }
];

function startJourney() {
    if (journeyStep > 0) return;
    
    document.getElementById('journey-btn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Transformation läuft...</span>';
    document.getElementById('journey-btn').disabled = true;
    
    // Show thought bubble
    document.getElementById('avatar-thought').style.opacity = '1';
    
    journeyInterval = setInterval(() => {
        if (journeyStep >= journeySteps.length) {
            clearInterval(journeyInterval);
            document.getElementById('journey-btn').innerHTML = '<i class="fas fa-redo"></i> <span>Nochmal erleben</span>';
            document.getElementById('journey-btn').disabled = false;
            document.getElementById('journey-btn').onclick = resetJourney;
            createConfetti();
            return;
        }
        
        const step = journeySteps[journeyStep];
        
        // Update progress bar
        document.getElementById('journey-progress').style.width = step.progress + '%';
        
        // Update mindset bar
        document.getElementById('mindset-bar').style.width = step.mindset + '%';
        document.getElementById('mindset-emoji').textContent = step.emoji;
        
        // Update avatar
        document.getElementById('avatar-emoji').textContent = step.avatar;
        document.getElementById('thought-text').textContent = step.thought;
        
        // Move avatar
        const avatarPos = 5 + (journeyStep / 5) * 70;
        document.getElementById('journey-avatar').style.left = avatarPos + '%';
        
        // Update step visuals
        for (let i = 1; i <= 6; i++) {
            const stepEl = document.getElementById('journey-step-' + i);
            stepEl.classList.remove('active', 'completed');
            if (i < journeyStep + 1) stepEl.classList.add('completed');
            if (i === journeyStep + 1) stepEl.classList.add('active');
        }
        
        // Update text
        document.getElementById('journey-text').innerHTML = step.text;
        
        // Special effects
        if (step.trust) {
            document.getElementById('journey-trust').textContent = step.trust;
        }
        
        if (step.client) {
            document.getElementById('journey-clients').textContent = '1';
            addFloatingEffect('journey-effects', '💰', 3);
        }
        
        if (step.reward) {
            document.getElementById('journey-reward').textContent = step.reward;
            addFloatingEffect('journey-effects', '🎁', 3);
        }
        
        if (step.complete) {
            document.getElementById('journey-info').classList.remove('from-purple-50', 'to-indigo-50', 'dark:from-purple-900/20', 'dark:to-indigo-900/20');
            document.getElementById('journey-info').classList.add('from-purple-500', 'to-indigo-600');
            document.getElementById('journey-text').classList.add('text-white');
            document.getElementById('journey-text').classList.remove('text-gray-700', 'dark:text-gray-300');
            addFloatingEffect('journey-effects', '⭐', 5);
        }
        
        journeyStep++;
    }, 2000);
}

function resetJourney() {
    clearInterval(journeyInterval);
    journeyStep = 0;
    
    // Reset progress
    document.getElementById('journey-progress').style.width = '0%';
    document.getElementById('mindset-bar').style.width = '10%';
    document.getElementById('mindset-emoji').textContent = '🌱';
    
    // Reset avatar
    document.getElementById('avatar-emoji').textContent = '😟';
    document.getElementById('journey-avatar').style.left = '5%';
    document.getElementById('avatar-thought').style.opacity = '0';
    document.getElementById('thought-text').textContent = 'Ob Coaching mir hilft?';
    
    // Reset steps
    for (let i = 1; i <= 6; i++) {
        document.getElementById('journey-step-' + i).classList.remove('active', 'completed');
    }
    
    // Reset stats
    document.getElementById('journey-clients').textContent = '0';
    document.getElementById('journey-trust').textContent = '0%';
    document.getElementById('journey-reward').textContent = '-';
    
    // Reset info box
    document.getElementById('journey-info').classList.remove('from-purple-500', 'to-indigo-600');
    document.getElementById('journey-info').classList.add('from-purple-50', 'to-indigo-50', 'dark:from-purple-900/20', 'dark:to-indigo-900/20');
    document.getElementById('journey-text').classList.remove('text-white');
    document.getElementById('journey-text').classList.add('text-gray-700', 'dark:text-gray-300');
    document.getElementById('journey-text').innerHTML = 'Klicken Sie, um die Transformation zu starten!';
    
    // Reset button
    document.getElementById('journey-btn').innerHTML = '<i class="fas fa-play"></i> <span>Transformation starten</span>';
    document.getElementById('journey-btn').disabled = false;
    document.getElementById('journey-btn').onclick = startJourney;
    
    // Clear effects
    document.getElementById('journey-effects').innerHTML = '';
}

function addFloatingEffect(containerId, emoji, count) {
    const container = document.getElementById(containerId);
    for (let i = 0; i < count; i++) {
        setTimeout(() => {
            const el = document.createElement('div');
            el.textContent = emoji;
            el.className = 'absolute text-2xl';
            el.style.left = (30 + Math.random() * 40) + '%';
            el.style.top = (30 + Math.random() * 40) + '%';
            el.style.animation = 'float-up 1s ease forwards';
            container.appendChild(el);
            setTimeout(() => el.remove(), 1000);
        }, i * 150);
    }
}

// ==================== ANIMATION 2: ZEIT-ROI-RECHNER ====================
function updateROI() {
    const price = parseInt(document.getElementById('roi-price-slider').value);
    const clients = parseInt(document.getElementById('roi-clients-slider').value);
    
    // Update displays
    document.getElementById('roi-price').textContent = price.toLocaleString('de-DE') + '€';
    document.getElementById('roi-clients').textContent = clients;
    
    // Calculate hours
    // Social Media: ~8h per client (content, DMs, calls, follow-ups)
    const socialHoursPerClient = 8;
    const socialTotalHours = clients * socialHoursPerClient;
    
    // Referrals: ~1h initial setup, then mostly automatic
    const referralHours = Math.max(5, clients * 0.5);
    
    const timeSaved = socialTotalHours - referralHours;
    const extraSessions = Math.floor(timeSaved); // 1h per session
    const extraRevenue = extraSessions * (price / 10); // Assuming 10 sessions per package
    
    // Update displays
    document.getElementById('roi-social-hours').textContent = socialTotalHours + 'h';
    document.getElementById('roi-referral-hours').textContent = Math.round(referralHours) + 'h';
    document.getElementById('roi-time-saved').textContent = timeSaved + 'h';
    document.getElementById('roi-sessions').textContent = extraSessions + ' Coaching-Sessions';
    document.getElementById('roi-extra-revenue').textContent = '+ ' + extraRevenue.toLocaleString('de-DE') + '€';
    
    // Update clock visualizations
    updateClocks('roi-social-clocks', Math.min(socialTotalHours / 4, 15), '⏰');
    updateClocks('roi-referral-clocks', Math.min(referralHours / 4, 3), '⏱️');
    
    // Animate savings box
    const savingsBox = document.getElementById('roi-savings-box');
    savingsBox.style.transform = 'scale(1.02)';
    setTimeout(() => {
        savingsBox.style.transform = 'scale(1)';
    }, 200);
}

function updateClocks(containerId, count, emoji) {
    const container = document.getElementById(containerId);
    const currentCount = container.children.length;
    const targetCount = Math.floor(count);
    
    if (targetCount > currentCount) {
        for (let i = currentCount; i < targetCount; i++) {
            const clock = document.createElement('span');
            clock.className = 'clock-icon';
            clock.textContent = emoji;
            container.appendChild(clock);
        }
    } else if (targetCount < currentCount) {
        for (let i = currentCount; i > targetCount; i--) {
            if (container.lastChild) {
                container.removeChild(container.lastChild);
            }
        }
    }
}

// ==================== ANIMATION 3: VERTRAUENS-NETZWERK ====================
let trustLevel = 0;
const trustData = [
    { 
        level: 1, 
        clients: 2, 
        score: 20, 
        conversion: '20%',
        text: '😐 Ebene 1: Kaltakquise-Klienten - Sie starten bei 0 Vertrauen'
    },
    { 
        level: 2, 
        clients: 5, 
        score: 55, 
        conversion: '70%',
        text: '😊 Ebene 2: Empfohlene Klienten kommen mit 70% Vertrauen!'
    },
    { 
        level: 3, 
        clients: 11, 
        score: 85, 
        conversion: '75%',
        text: '😄 Ebene 3: Empfehlungen von Empfehlungen - Ihr Ruf eilt Ihnen voraus!'
    },
    { 
        level: 4, 
        clients: 11, 
        score: 95, 
        conversion: '80%',
        text: '🏆 EXPERTENSTATUS! Sie sind die Go-To-Person in Ihrem Bereich!',
        final: true
    }
];

function triggerTrustNetwork() {
    if (trustLevel >= trustData.length) return;
    
    const data = trustData[trustLevel];
    
    // Show levels
    if (trustLevel === 0) {
        document.getElementById('trust-level-1').classList.remove('opacity-0', 'translate-y-4');
        addTrustBurst();
    } else if (trustLevel === 1) {
        document.getElementById('trust-level-2').classList.remove('opacity-0', 'translate-y-4');
        document.getElementById('trust-ring').classList.add('trust-ring-active');
        addTrustBurst();
    } else if (trustLevel === 2) {
        document.getElementById('trust-level-3').classList.remove('opacity-0', 'translate-y-4');
        addTrustBurst();
    }
    
    // Update stats
    document.getElementById('trust-level').textContent = data.level;
    document.getElementById('trust-clients').textContent = data.clients;
    document.getElementById('trust-score').textContent = data.score + '%';
    document.getElementById('trust-conversion').textContent = data.conversion;
    
    // Update info text
    document.getElementById('trust-text').innerHTML = data.text;
    
    // Update comparison (highlight warm rate more as levels increase)
    if (trustLevel >= 1) {
        document.getElementById('trust-warm-rate').textContent = data.conversion;
    }
    
    // Update button
    trustLevel++;
    if (trustLevel < trustData.length) {
        const nextClients = trustData[trustLevel].clients - data.clients;
        document.getElementById('trust-btn').innerHTML = '<i class="fas fa-network-wired"></i> <span>Nächste Ebene (+' + nextClients + ' Klienten)</span>';
    } else {
        document.getElementById('trust-btn').innerHTML = '<i class="fas fa-check"></i> <span>Netzwerk komplett!</span>';
        document.getElementById('trust-btn').disabled = true;
        document.getElementById('trust-btn').classList.add('opacity-70');
        document.getElementById('trust-celebration').classList.remove('hidden');
        
        // Add glow to center node
        document.querySelector('.coach-node').classList.add('animate-glow');
        
        createConfetti();
    }
}

function addTrustBurst() {
    const container = document.getElementById('trust-effects');
    const burst = document.createElement('div');
    burst.className = 'absolute left-1/2 top-8 w-20 h-20 -translate-x-1/2 rounded-full border-4 border-purple-400';
    burst.style.animation = 'trust-burst 1s ease-out forwards';
    container.appendChild(burst);
    setTimeout(() => burst.remove(), 1000);
    
    // Add floating hearts/stars
    for (let i = 0; i < 5; i++) {
        setTimeout(() => {
            const el = document.createElement('div');
            el.textContent = ['💜', '⭐', '✨', '💫', '🌟'][Math.floor(Math.random() * 5)];
            el.className = 'absolute text-xl';
            el.style.left = (40 + Math.random() * 20) + '%';
            el.style.top = '20%';
            el.style.animation = 'float-up 1.5s ease forwards';
            container.appendChild(el);
            setTimeout(() => el.remove(), 1500);
        }, i * 100);
    }
}

function resetTrustNetwork() {
    trustLevel = 0;
    
    // Hide levels
    document.getElementById('trust-level-1').classList.add('opacity-0', 'translate-y-4');
    document.getElementById('trust-level-2').classList.add('opacity-0', 'translate-y-4');
    document.getElementById('trust-level-3').classList.add('opacity-0', 'translate-y-4');
    
    // Reset trust ring
    document.getElementById('trust-ring').classList.remove('trust-ring-active');
    
    // Reset stats
    document.getElementById('trust-level').textContent = '0';
    document.getElementById('trust-clients').textContent = '0';
    document.getElementById('trust-score').textContent = '0%';
    document.getElementById('trust-conversion').textContent = '-';
    
    // Reset comparison
    document.getElementById('trust-warm-rate').textContent = '70%';
    
    // Reset info
    document.getElementById('trust-text').innerHTML = 'Klicken Sie, um das Vertrauens-Netzwerk aufzubauen!';
    
    // Reset button
    document.getElementById('trust-btn').innerHTML = '<i class="fas fa-network-wired"></i> <span>Netzwerk aufbauen</span>';
    document.getElementById('trust-btn').disabled = false;
    document.getElementById('trust-btn').classList.remove('opacity-70');
    
    // Hide celebration
    document.getElementById('trust-celebration').classList.add('hidden');
    
    // Remove glow from center
    document.querySelector('.coach-node').classList.remove('animate-glow');
    
    // Clear effects
    document.getElementById('trust-effects').innerHTML = '';
}

// ==================== HELPER FUNCTIONS ====================
function createConfetti() {
    const colors = ['#8b5cf6', '#6366f1', '#a855f7', '#fbbf24', '#ec4899'];
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

// ==================== INITIALIZE ====================
document.addEventListener('DOMContentLoaded', function() {
    // Initialize ROI calculator
    updateROI();
});
</script>

<!-- Belohnungen Section -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            <div>
                <span class="text-purple-600 dark:text-purple-400 font-semibold uppercase tracking-wide text-sm">Belohnungssystem</span>
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mt-2 mb-6">
                    Beispiel-Belohnungen für Ihr Coaching
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-lg mb-8">
                    Belohnen Sie Empfehlungen mit Mehrwert, der zu Ihrem Angebot passt:
                </p>
                
                <div class="space-y-4">
                    <?php foreach ($belohnungen as $b): ?>
                    <div class="flex items-center gap-4 bg-gray-50 dark:bg-slate-800 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                            <?= $b['stufe'] ?>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400"><?= $b['stufe'] ?> Empfehlung<?= $b['stufe'] > 1 ? 'en' : '' ?></div>
                            <div class="font-semibold text-gray-900 dark:text-white"><?= $b['belohnung'] ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-6">
                    <i class="fas fa-lightbulb mr-1 text-yellow-500"></i>
                    Tipp: Digitale Downloads (E-Books, Workbooks) haben null Grenzkosten.
                </p>
            </div>
            
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 md:p-8 shadow-lg border border-gray-200 dark:border-slate-600">
                <div class="text-center mb-6">
                    <div class="text-5xl mb-3">🎯</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Belohnung freigeschaltet!</h3>
                    <p class="text-gray-500 dark:text-gray-400">Du hast Stufe 3 erreicht</p>
                </div>
                
                <div class="bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/10 rounded-xl p-5 border border-purple-200 dark:border-purple-700/30 mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-purple-500 rounded-full flex items-center justify-center text-2xl">
                            📞
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 dark:text-white text-lg">Bonus-Coaching-Call</div>
                            <div class="text-gray-600 dark:text-gray-300">30 Minuten 1:1 mit Julia</div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="inline-flex items-center gap-2 text-green-600 dark:text-green-400 font-medium">
                        <i class="fas fa-calendar-check"></i>
                        <span>Termin jetzt buchbar</span>
                    </div>
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
                Perfekt für jeden Coaching-Bereich
            </h2>
        </div>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php 
            $useCases = [
                ['icon' => 'fa-heart', 'title' => 'Life Coaching', 'text' => 'Persönliche Transformation wird gerne weiterempfohlen'],
                ['icon' => 'fa-briefcase', 'title' => 'Business Coaching', 'text' => 'Unternehmer empfehlen Unternehmer'],
                ['icon' => 'fa-running', 'title' => 'Fitness & Health', 'text' => 'Sichtbare Ergebnisse sprechen für sich'],
                ['icon' => 'fa-brain', 'title' => 'Mindset Coaching', 'text' => 'Mentale Durchbrüche werden geteilt'],
                ['icon' => 'fa-users', 'title' => 'Beziehungscoaching', 'text' => 'Glückliche Paare empfehlen gerne'],
                ['icon' => 'fa-graduation-cap', 'title' => 'Karriere Coaching', 'text' => 'Erfolgreiche Karrieren inspirieren andere'],
            ];
            foreach ($useCases as $useCase): ?>
            <div class="bg-white dark:bg-slate-700 rounded-xl p-5 flex items-start gap-4 hover:shadow-lg transition-shadow">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center text-purple-600 dark:text-purple-400 flex-shrink-0">
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

<!-- Testimonial Section -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-slate-800 dark:to-slate-700 rounded-2xl p-8 md:p-12 text-center">
            <div class="flex justify-center gap-1 text-yellow-400 mb-6">
                <?php for ($i = 0; $i < 5; $i++): ?>
                <i class="fas fa-star text-xl"></i>
                <?php endfor; ?>
            </div>
            
            <blockquote class="text-xl md:text-2xl font-medium text-gray-900 dark:text-white mb-8 leading-relaxed">
                "<?= $testimonial['text'] ?>"
            </blockquote>
            
            <div class="flex items-center justify-center gap-4">
                <div class="w-14 h-14 bg-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
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

<!-- CTA Section -->
<section class="py-12 md:py-20 bg-gradient-to-r from-purple-600 to-indigo-700 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold mb-4 md:mb-6">
            Bereit für mehr Klienten durch Empfehlungen?
        </h2>
        <p class="text-lg md:text-xl text-white/90 mb-6 md:mb-8">
            Starten Sie noch heute und lassen Sie Ihre Klienten für Sie sprechen.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/onboarding" class="btn-white btn-large inline-flex items-center justify-center gap-2">
                <span>Jetzt 7 Tage kostenlos testen</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <p class="text-white/70 mt-6 text-sm">
            Keine Kreditkarte erforderlich · Einrichtung in 5 Minuten · DSGVO-konform
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
            <a href="/branchen/onlineshop" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-full text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors text-sm font-medium">
                <i class="fas fa-shopping-cart mr-1"></i> Online-Shops
            </a>
            <a href="/branchen/onlinemarketing" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-full text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors text-sm font-medium">
                <i class="fas fa-bullhorn mr-1"></i> Online-Marketing
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../templates/marketing/footer.php'; ?>
