<?php
/**
 * Branchenseite: Zahnärzte
 */

$pageTitle = 'Empfehlungsprogramm für Zahnärzte';
$metaDescription = 'Automatisches Empfehlungsprogramm für Zahnarztpraxen. Patienten werben Patienten und erhalten Belohnungen wie Rabatte auf Zahnreinigung oder Bleaching.';
$currentPage = 'branchen';

require_once __DIR__ . '/../../templates/marketing/header.php';

// Branchenspezifische Daten
$branche = [
    'name' => 'Zahnärzte',
    'slug' => 'zahnarzt',
    'icon' => 'fa-tooth',
    'color' => 'blue',
    'heroTitle' => 'Mehr Patienten durch Empfehlungen',
    'heroSubtitle' => 'Ihre zufriedenen Patienten sind Ihre besten Werber. Mit Leadbusiness machen Sie Mundpropaganda messbar und belohnen treue Patienten automatisch.',
    'image' => '/assets/images/branchen/zahnarzt-hero.jpg',
];

$vorteile = [
    [
        'icon' => 'fa-users',
        'title' => 'Qualifizierte Neupatienten',
        'text' => 'Empfohlene Patienten haben bereits Vertrauen in Ihre Praxis – durch die persönliche Empfehlung von Freunden und Familie.'
    ],
    [
        'icon' => 'fa-euro-sign',
        'title' => 'Geringere Akquisekosten',
        'text' => 'Empfehlungsmarketing ist günstiger als klassische Werbung und bringt Patienten, die wirklich zu Ihnen passen.'
    ],
    [
        'icon' => 'fa-heart',
        'title' => 'Stärkere Patientenbindung',
        'text' => 'Patienten, die aktiv empfehlen, fühlen sich Ihrer Praxis verbunden und bleiben länger treu.'
    ],
    [
        'icon' => 'fa-clock',
        'title' => 'Null Aufwand für Sie',
        'text' => 'Das System läuft vollautomatisch. Sie konzentrieren sich auf Ihre Patienten – wir kümmern uns um den Rest.'
    ],
];

$belohnungen = [
    ['stufe' => 3, 'belohnung' => '10% Rabatt auf professionelle Zahnreinigung'],
    ['stufe' => 5, 'belohnung' => '50€ Gutschein für Ihre nächste Behandlung'],
    ['stufe' => 10, 'belohnung' => 'Gratis Bleaching-Behandlung'],
];

$testimonial = [
    'text' => 'In den ersten 6 Monaten haben wir 89 Neupatienten durch Empfehlungen gewonnen. Das System läuft komplett automatisch – ich muss nichts tun außer gute Arbeit zu leisten.',
    'name' => 'Dr. Thomas Müller',
    'rolle' => 'Zahnarztpraxis München',
    'initialen' => 'TM',
];
?>

<!-- Hero Section -->
<section class="relative py-16 md:py-24 overflow-hidden">
    <!-- Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-blue-800"></div>
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
                        <span>Keine Technik nötig</span>
                    </div>
                </div>
            </div>
            
            <!-- Visual -->
            <div class="hidden lg:block">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="bg-white rounded-xl shadow-2xl p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-tooth text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">Zahnarztpraxis Dr. Müller</div>
                                <div class="text-sm text-gray-500">empfohlen.de/dr-mueller</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">247</div>
                                <div class="text-xs text-gray-500">Empfehler</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">89</div>
                                <div class="text-xs text-gray-500">Neupatienten</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600">36%</div>
                                <div class="text-xs text-gray-500">Conversion</div>
                            </div>
                        </div>
                        <div class="text-center text-sm text-gray-500">
                            <i class="fas fa-chart-line text-green-500 mr-1"></i>
                            +23% mehr Empfehlungen als letzten Monat
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
                Nutzen Sie das Vertrauen Ihrer Patienten für nachhaltiges Praxiswachstum.
            </p>
        </div>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
            <?php foreach ($vorteile as $vorteil): ?>
            <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-6 hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400 text-xl mb-4">
                    <i class="fas <?= $vorteil['icon'] ?>"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2"><?= $vorteil['title'] ?></h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm"><?= $vorteil['text'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Belohnungen Section -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            <div>
                <span class="text-blue-600 dark:text-blue-400 font-semibold uppercase tracking-wide text-sm">Belohnungssystem</span>
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mt-2 mb-6">
                    Beispiel-Belohnungen für Ihre Praxis
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-lg mb-8">
                    Definieren Sie Belohnungsstufen, die zu Ihrer Praxis passen. Hier sind beliebte Beispiele:
                </p>
                
                <div class="space-y-4">
                    <?php foreach ($belohnungen as $b): ?>
                    <div class="flex items-center gap-4 bg-white dark:bg-slate-700 rounded-xl p-4 shadow-sm">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                            <?= $b['stufe'] ?>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400"><?= $b['stufe'] ?> Empfehlungen</div>
                            <div class="font-semibold text-gray-900 dark:text-white"><?= $b['belohnung'] ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-6">
                    <i class="fas fa-info-circle mr-1"></i>
                    Sie können die Belohnungen jederzeit anpassen.
                </p>
            </div>
            
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 md:p-8 shadow-lg">
                <div class="text-center mb-6">
                    <div class="text-5xl mb-3">🎉</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Belohnung freigeschaltet!</h3>
                    <p class="text-gray-500 dark:text-gray-400">Sie haben Stufe 2 erreicht</p>
                </div>
                
                <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/10 rounded-xl p-5 border border-yellow-200 dark:border-yellow-700/30 mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-yellow-400 rounded-full flex items-center justify-center text-2xl">
                            🎁
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 dark:text-white text-lg">50€ Gutschein</div>
                            <div class="text-gray-600 dark:text-gray-300">Für Ihre nächste Behandlung</div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="inline-flex items-center gap-2 text-green-600 dark:text-green-400 font-medium">
                        <i class="fas fa-envelope"></i>
                        <span>Gutschein per E-Mail erhalten</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonial Section -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-slate-800 dark:to-slate-700 rounded-2xl p-8 md:p-12 text-center">
            <div class="flex justify-center gap-1 text-yellow-400 mb-6">
                <?php for ($i = 0; $i < 5; $i++): ?>
                <i class="fas fa-star text-xl"></i>
                <?php endfor; ?>
            </div>
            
            <blockquote class="text-xl md:text-2xl font-medium text-gray-900 dark:text-white mb-8 leading-relaxed">
                "<?= $testimonial['text'] ?>"
            </blockquote>
            
            <div class="flex items-center justify-center gap-4">
                <div class="w-14 h-14 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
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

<!-- So funktioniert's -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                So einfach funktioniert's
            </h2>
        </div>
        
        <div class="grid md:grid-cols-3 gap-6 md:gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">1</div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Patient erhält Link</h3>
                <p class="text-gray-600 dark:text-gray-400">Nach der Behandlung bekommt Ihr Patient einen persönlichen Empfehlungslink per E-Mail.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">2</div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Patient empfiehlt weiter</h3>
                <p class="text-gray-600 dark:text-gray-400">Der Link wird mit Freunden und Familie geteilt – per WhatsApp, E-Mail oder Social Media.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">3</div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Automatische Belohnung</h3>
                <p class="text-gray-600 dark:text-gray-400">Bei erfolgreicher Empfehlung wird die Belohnung automatisch per E-Mail versendet.</p>
            </div>
        </div>
    </div>
</section>

<!-- NEUE SECTION: Interaktive Animationen -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white px-5 py-2 rounded-full text-sm font-bold shadow-lg mb-4">
                <span>🎬</span> Live erleben
            </span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Erleben Sie das Empfehlungsprogramm in Aktion
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg max-w-2xl mx-auto">
                Drei interaktive Demos zeigen Ihnen, wie einfach und effektiv Empfehlungsmarketing für Ihre Praxis funktioniert.
            </p>
        </div>
        
        <!-- Tab Navigation -->
        <div class="flex flex-wrap justify-center gap-3 mb-8" id="animation-tabs">
            <button onclick="showAnimation('whatsapp')" id="tab-whatsapp" class="animation-tab active px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-lg">
                💬 WhatsApp Chat
            </button>
            <button onclick="showAnimation('demo')" id="tab-demo" class="animation-tab px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-white dark:bg-slate-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-700 hover:shadow-md">
                🎮 Interaktive Demo
            </button>
            <button onclick="showAnimation('network')" id="tab-network" class="animation-tab px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-white dark:bg-slate-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-700 hover:shadow-md">
                🌐 Virales Netzwerk
            </button>
        </div>
        
        <!-- Animation Containers -->
        <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-6 md:p-8 min-h-[600px]">
            
            <!-- WhatsApp Animation -->
            <div id="animation-whatsapp" class="animation-content">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">So teilen Ihre Patienten</h3>
                    <p class="text-gray-500 dark:text-gray-400">Ein WhatsApp-Gespräch zeigt den natürlichen Empfehlungsprozess</p>
                </div>
                
                <div class="max-w-sm mx-auto">
                    <!-- Phone Frame -->
                    <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-[2.5rem] p-3 shadow-2xl">
                        <div class="w-24 h-6 bg-black rounded-full mx-auto mb-2"></div>
                        <div class="bg-slate-900 rounded-[2rem] overflow-hidden h-[480px] flex flex-col">
                            <!-- Chat Header -->
                            <div class="bg-slate-800 p-3 flex items-center gap-3 border-b border-slate-700">
                                <span class="text-emerald-400 text-lg">←</span>
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-bold">S</div>
                                <div class="flex-1">
                                    <div class="text-white font-medium">Sarah 👩‍⚕️</div>
                                    <div class="text-slate-400 text-xs">online</div>
                                </div>
                                <span class="text-slate-400">📹</span>
                                <span class="text-slate-400">📞</span>
                            </div>
                            
                            <!-- Chat Messages -->
                            <div class="flex-1 p-3 overflow-y-auto flex flex-col gap-2" id="whatsapp-messages">
                                <!-- Messages will be inserted here by JavaScript -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Replay Button -->
                    <button onclick="restartWhatsApp()" id="whatsapp-replay" class="hidden mt-5 mx-auto block px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full font-semibold text-sm hover:shadow-lg transition-all">
                        ↻ Animation wiederholen
                    </button>
                </div>
            </div>
            
            <!-- Interactive Demo -->
            <div id="animation-demo" class="animation-content hidden">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Probieren Sie es selbst aus!</h3>
                    <p class="text-gray-500 dark:text-gray-400">Klicken Sie auf "Teilen" und erleben Sie das Belohnungssystem live</p>
                </div>
                
                <div class="max-w-md mx-auto">
                    <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 shadow-xl border border-gray-100 dark:border-slate-600 relative overflow-hidden">
                        <!-- Top gradient bar -->
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 via-cyan-500 to-violet-500"></div>
                        
                        <!-- Stats -->
                        <div class="grid grid-cols-3 gap-3 mb-6">
                            <div class="bg-gray-50 dark:bg-slate-600 rounded-xl p-3 text-center">
                                <div id="demo-referrals" class="text-2xl font-black bg-gradient-to-r from-emerald-500 to-teal-500 bg-clip-text text-transparent">0</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Empfehlungen</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-slate-600 rounded-xl p-3 text-center">
                                <div id="demo-level" class="text-2xl font-black text-gray-800 dark:text-white">0</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Stufe</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-slate-600 rounded-xl p-3 text-center">
                                <div id="demo-remaining" class="text-2xl font-black text-gray-800 dark:text-white">3</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Bis nächste</div>
                            </div>
                        </div>
                        
                        <!-- Level Info -->
                        <div class="flex items-center gap-3 mb-3">
                            <div id="demo-level-icon" class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-lg shadow-md">🦷</div>
                            <div>
                                <div id="demo-level-name" class="font-bold text-gray-800 dark:text-white">Stufe 0: Start</div>
                                <div id="demo-level-reward" class="text-sm text-gray-500 dark:text-gray-400">Starten Sie jetzt!</div>
                            </div>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="h-3 bg-gray-200 dark:bg-slate-500 rounded-full overflow-hidden mb-2">
                            <div id="demo-progress" class="h-full bg-gradient-to-r from-emerald-500 to-cyan-500 rounded-full transition-all duration-500" style="width: 0%"></div>
                        </div>
                        
                        <!-- Milestones -->
                        <div class="flex justify-between mb-6">
                            <div class="flex flex-col items-center opacity-100" data-milestone="0">
                                <div class="w-7 h-7 rounded-full bg-emerald-500 flex items-center justify-center text-xs">🦷</div>
                                <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">0</div>
                            </div>
                            <div class="flex flex-col items-center opacity-40" data-milestone="3">
                                <div class="w-7 h-7 rounded-full bg-gray-300 dark:bg-slate-500 flex items-center justify-center text-xs">⭐</div>
                                <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">3</div>
                            </div>
                            <div class="flex flex-col items-center opacity-40" data-milestone="5">
                                <div class="w-7 h-7 rounded-full bg-gray-300 dark:bg-slate-500 flex items-center justify-center text-xs">🌟</div>
                                <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">5</div>
                            </div>
                            <div class="flex flex-col items-center opacity-40" data-milestone="10">
                                <div class="w-7 h-7 rounded-full bg-gray-300 dark:bg-slate-500 flex items-center justify-center text-xs">👑</div>
                                <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">10</div>
                            </div>
                        </div>
                        
                        <!-- Share Buttons -->
                        <div class="mb-5">
                            <div class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-3">📤 Jetzt teilen und Belohnung sichern</div>
                            <div class="grid grid-cols-4 gap-2">
                                <button onclick="handleShare('whatsapp')" id="share-whatsapp" class="share-btn p-3 rounded-xl border-2 border-gray-200 dark:border-slate-500 flex flex-col items-center gap-1 hover:border-emerald-400 hover:shadow-md hover:-translate-y-1 transition-all bg-white dark:bg-slate-600">
                                    <span class="text-xl">💬</span>
                                    <span class="text-[10px] text-gray-500 dark:text-gray-400">WhatsApp</span>
                                </button>
                                <button onclick="handleShare('email')" id="share-email" class="share-btn p-3 rounded-xl border-2 border-gray-200 dark:border-slate-500 flex flex-col items-center gap-1 hover:border-emerald-400 hover:shadow-md hover:-translate-y-1 transition-all bg-white dark:bg-slate-600">
                                    <span class="text-xl">📧</span>
                                    <span class="text-[10px] text-gray-500 dark:text-gray-400">E-Mail</span>
                                </button>
                                <button onclick="handleShare('facebook')" id="share-facebook" class="share-btn p-3 rounded-xl border-2 border-gray-200 dark:border-slate-500 flex flex-col items-center gap-1 hover:border-emerald-400 hover:shadow-md hover:-translate-y-1 transition-all bg-white dark:bg-slate-600">
                                    <span class="text-xl">👤</span>
                                    <span class="text-[10px] text-gray-500 dark:text-gray-400">Facebook</span>
                                </button>
                                <button onclick="handleShare('link')" id="share-link" class="share-btn p-3 rounded-xl border-2 border-gray-200 dark:border-slate-500 flex flex-col items-center gap-1 hover:border-emerald-400 hover:shadow-md hover:-translate-y-1 transition-all bg-white dark:bg-slate-600">
                                    <span class="text-xl">🔗</span>
                                    <span class="text-[10px] text-gray-500 dark:text-gray-400">Link</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Activity Feed -->
                        <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-4 border border-emerald-200 dark:border-emerald-700/30">
                            <div class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 mb-2">⚡ Live-Aktivität</div>
                            <div id="demo-activity" class="space-y-2">
                                <div class="text-center text-gray-500 dark:text-gray-400 text-xs py-2">
                                    Teilen Sie, um Aktivität zu sehen...
                                </div>
                            </div>
                        </div>
                        
                        <!-- Reset Button -->
                        <button onclick="resetDemo()" class="w-full mt-4 py-3 bg-gradient-to-r from-indigo-500 to-violet-500 text-white rounded-xl font-semibold text-sm hover:shadow-lg transition-all">
                            ↻ Demo zurücksetzen
                        </button>
                    </div>
                </div>
                
                <!-- Reward Modal -->
                <div id="demo-reward-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
                    <div class="absolute inset-0 bg-black/50" onclick="closeRewardModal()"></div>
                    <div class="relative bg-white dark:bg-slate-700 rounded-2xl p-8 shadow-2xl text-center max-w-xs mx-4 animate-bounce-in">
                        <div class="text-6xl mb-4">🎉</div>
                        <div id="reward-title" class="text-2xl font-black text-gray-800 dark:text-white mb-2">Stufe 1 erreicht!</div>
                        <div id="reward-text" class="text-gray-600 dark:text-gray-300 mb-6">10% Rabatt auf Zahnreinigung</div>
                        <button onclick="closeRewardModal()" class="px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-xl font-semibold hover:shadow-lg transition-all">
                            Belohnung einlösen
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Viral Network Animation -->
            <div id="animation-network" class="animation-content hidden">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Virales Wachstum visualisiert</h3>
                    <p class="text-gray-500 dark:text-gray-400">Sehen Sie, wie eine Empfehlung zu 89 Neupatienten wird</p>
                </div>
                
                <div class="max-w-lg mx-auto">
                    <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 shadow-2xl">
                        <!-- Stats Bar -->
                        <div class="grid grid-cols-3 gap-3 mb-6">
                            <div class="text-center p-3 bg-white/5 rounded-xl">
                                <div id="network-patients" class="text-2xl font-black bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent">1</div>
                                <div class="text-xs text-slate-400 mt-1">Aktive Empfehler</div>
                            </div>
                            <div class="text-center p-3 bg-white/5 rounded-xl">
                                <div id="network-reach" class="text-2xl font-black bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent">0</div>
                                <div class="text-xs text-slate-400 mt-1">Erreichte Personen</div>
                            </div>
                            <div class="text-center p-3 bg-white/5 rounded-xl">
                                <div id="network-conversions" class="text-2xl font-black bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent">0</div>
                                <div class="text-xs text-slate-400 mt-1">Neue Patienten</div>
                            </div>
                        </div>
                        
                        <!-- Network Visualization -->
                        <div class="relative w-72 h-72 mx-auto" id="network-container">
                            <svg class="absolute inset-0 w-full h-full" id="network-lines"></svg>
                            <div id="network-nodes"></div>
                        </div>
                        
                        <!-- Timeline -->
                        <div class="flex justify-between mt-6 px-2">
                            <button onclick="setNetworkPhase(0)" class="network-phase flex flex-col items-center transition-all opacity-100" data-phase="0">
                                <div class="w-3 h-3 rounded-full bg-emerald-400 shadow-lg shadow-emerald-400/50 scale-150 mb-2"></div>
                                <div class="text-xs text-emerald-400">Start</div>
                            </button>
                            <button onclick="setNetworkPhase(1)" class="network-phase flex flex-col items-center transition-all opacity-30" data-phase="1">
                                <div class="w-3 h-3 rounded-full bg-slate-600 mb-2"></div>
                                <div class="text-xs text-slate-500">1 Woche</div>
                            </button>
                            <button onclick="setNetworkPhase(2)" class="network-phase flex flex-col items-center transition-all opacity-30" data-phase="2">
                                <div class="w-3 h-3 rounded-full bg-slate-600 mb-2"></div>
                                <div class="text-xs text-slate-500">2 Wochen</div>
                            </button>
                            <button onclick="setNetworkPhase(3)" class="network-phase flex flex-col items-center transition-all opacity-30" data-phase="3">
                                <div class="w-3 h-3 rounded-full bg-slate-600 mb-2"></div>
                                <div class="text-xs text-slate-500">1 Monat</div>
                            </button>
                            <button onclick="setNetworkPhase(4)" class="network-phase flex flex-col items-center transition-all opacity-30" data-phase="4">
                                <div class="w-3 h-3 rounded-full bg-slate-600 mb-2"></div>
                                <div class="text-xs text-slate-500">3 Monate</div>
                            </button>
                        </div>
                        
                        <!-- Explanation -->
                        <div class="bg-white/5 rounded-xl p-4 mt-6 text-center">
                            <div id="network-title" class="text-white font-bold mb-1">🦷 Starten Sie mit einem zufriedenen Patienten</div>
                            <div id="network-desc" class="text-slate-400 text-sm">Jeder zufriedene Patient kann Ihre Praxis weiterempfehlen.</div>
                        </div>
                        
                        <!-- Controls -->
                        <div class="flex justify-center gap-3 mt-6">
                            <button onclick="toggleNetworkPlay()" id="network-play-btn" class="px-5 py-2 bg-white/10 text-slate-300 rounded-lg text-sm font-medium hover:bg-white/20 transition-colors">
                                ⏸ Pause
                            </button>
                            <button onclick="restartNetwork()" class="px-5 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg text-sm font-medium hover:shadow-lg transition-all">
                                ↻ Neu starten
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Animation Styles -->
<style>
    @keyframes messageIn {
        from { opacity: 0; transform: scale(0.8) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes bounceIn {
        0% { transform: scale(0); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    @keyframes nodeAppear {
        from { transform: scale(0); }
        to { transform: scale(1); }
    }
    .animate-message-in { animation: messageIn 0.3s ease forwards; }
    .animate-bounce-in { animation: bounceIn 0.4s ease forwards; }
    .animate-node-appear { animation: nodeAppear 0.3s ease forwards; }
    
    .animation-tab.active {
        background: linear-gradient(to right, #10b981, #14b8a6);
        color: white;
        box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
        border: none;
    }
    
    .share-btn.used {
        opacity: 0.4;
        cursor: not-allowed;
        pointer-events: none;
    }
    
    .network-node {
        position: absolute;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transform: scale(0);
    }
    .network-node.center { background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 0 20px rgba(16, 185, 129, 0.5); }
    .network-node.referral { background: linear-gradient(135deg, #06b6d4, #0891b2); box-shadow: 0 0 15px rgba(6, 182, 212, 0.4); }
    .network-node.secondary { background: linear-gradient(135deg, #8b5cf6, #7c3aed); box-shadow: 0 0 12px rgba(139, 92, 246, 0.4); }
    .network-node.tertiary { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 0 10px rgba(245, 158, 11, 0.4); }
</style>

<!-- Animation JavaScript -->
<script>
// ==================== TAB SWITCHING ====================
function showAnimation(type) {
    // Hide all content
    document.querySelectorAll('.animation-content').forEach(el => el.classList.add('hidden'));
    // Show selected
    document.getElementById('animation-' + type).classList.remove('hidden');
    
    // Update tab styles
    document.querySelectorAll('.animation-tab').forEach(tab => {
        tab.classList.remove('active', 'bg-gradient-to-r', 'from-emerald-500', 'to-teal-500', 'text-white', 'shadow-lg');
        tab.classList.add('bg-white', 'dark:bg-slate-800', 'text-gray-600', 'dark:text-gray-300', 'border', 'border-gray-200', 'dark:border-slate-700');
    });
    const activeTab = document.getElementById('tab-' + type);
    activeTab.classList.add('active');
    activeTab.classList.remove('bg-white', 'dark:bg-slate-800', 'text-gray-600', 'dark:text-gray-300', 'border', 'border-gray-200', 'dark:border-slate-700');
    
    // Initialize animation
    if (type === 'whatsapp') restartWhatsApp();
    if (type === 'demo') resetDemo();
    if (type === 'network') restartNetwork();
}

// ==================== WHATSAPP ANIMATION ====================
const whatsappMessages = [
    { sender: 'me', text: 'Hey! War gerade beim Zahnarzt Dr. Müller – mega nett und gar nicht weh getan! 🦷✨', time: '14:32' },
    { sender: 'me', text: 'Kann ich dir nur empfehlen!', time: '14:32' },
    { sender: 'me', text: 'Hier mein Empfehlungslink – du bekommst 10% Rabatt:', time: '14:33', isLink: true, link: 'empfohlen.de/dr-mueller/anna' },
    { sender: 'other', text: 'Oh nice! Brauche eh dringend einen neuen 😅', time: '14:35' },
    { sender: 'other', text: 'Danke dir! Hab direkt Termin gemacht 🙌', time: '14:38' }
];

let whatsappIndex = 0;
let whatsappTimeout = null;

function restartWhatsApp() {
    whatsappIndex = 0;
    clearTimeout(whatsappTimeout);
    document.getElementById('whatsapp-messages').innerHTML = '';
    document.getElementById('whatsapp-replay').classList.add('hidden');
    showNextWhatsAppMessage();
}

function showNextWhatsAppMessage() {
    if (whatsappIndex >= whatsappMessages.length) {
        // Show success popup
        const container = document.getElementById('whatsapp-messages');
        const popup = document.createElement('div');
        popup.className = 'bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl p-4 text-center animate-bounce-in mx-auto max-w-[200px]';
        popup.innerHTML = '<div class="text-2xl mb-1">🎉</div><div class="text-white font-bold text-sm">+1 Empfehlung!</div><div class="text-white/80 text-xs">Noch 2 bis zur Belohnung</div>';
        container.appendChild(popup);
        document.getElementById('whatsapp-replay').classList.remove('hidden');
        return;
    }
    
    // Show typing indicator
    const container = document.getElementById('whatsapp-messages');
    const typing = document.createElement('div');
    typing.id = 'typing';
    typing.className = 'flex gap-1 p-3 bg-slate-700 rounded-lg w-fit';
    typing.innerHTML = '<div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay:0s"></div><div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay:0.15s"></div><div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay:0.3s"></div>';
    container.appendChild(typing);
    container.scrollTop = container.scrollHeight;
    
    whatsappTimeout = setTimeout(() => {
        // Remove typing
        document.getElementById('typing')?.remove();
        
        // Add message
        const msg = whatsappMessages[whatsappIndex];
        const msgEl = document.createElement('div');
        msgEl.className = `max-w-[85%] p-2 rounded-lg animate-message-in ${msg.sender === 'me' ? 'bg-emerald-800 self-end rounded-br-none ml-auto' : 'bg-slate-700 self-start rounded-bl-none'}`;
        
        let content = `<div class="text-white text-xs leading-relaxed">${msg.text}`;
        if (msg.isLink) {
            content += `<div class="bg-emerald-900/50 border border-emerald-700/50 rounded-md p-2 mt-1 text-emerald-300 text-xs">🔗 ${msg.link}</div>`;
        }
        content += `</div><div class="text-slate-400 text-[10px] text-right mt-1 flex items-center justify-end gap-1">${msg.time}`;
        if (msg.sender === 'me') content += '<span class="text-sky-400">✓✓</span>';
        content += '</div>';
        
        msgEl.innerHTML = content;
        container.appendChild(msgEl);
        container.scrollTop = container.scrollHeight;
        
        whatsappIndex++;
        whatsappTimeout = setTimeout(showNextWhatsAppMessage, 1000);
    }, 600);
}

// ==================== INTERACTIVE DEMO ====================
const demoLevels = [
    { req: 0, reward: null, icon: '🦷', name: 'Start' },
    { req: 3, reward: '10% Rabatt auf Zahnreinigung', icon: '⭐', name: 'Bronze' },
    { req: 5, reward: '50€ Gutschein', icon: '🌟', name: 'Silber' },
    { req: 10, reward: 'Gratis Bleaching!', icon: '👑', name: 'Gold' }
];
const demoNames = ['Anna', 'Max', 'Lisa', 'Tom', 'Sarah', 'Jan', 'Maria', 'Felix'];

let demoReferrals = 0;
let demoLevel = 0;
let usedButtons = {};

function handleShare(type) {
    if (usedButtons[type] || demoReferrals >= 10) return;
    usedButtons[type] = true;
    
    const btn = document.getElementById('share-' + type);
    btn.classList.add('used');
    
    setTimeout(() => {
        demoReferrals = Math.min(demoReferrals + 1, 10);
        updateDemoUI();
        
        // Add activity
        const name = demoNames[Math.floor(Math.random() * demoNames.length)];
        const activity = document.getElementById('demo-activity');
        activity.innerHTML = `<div class="flex items-center gap-2 bg-white dark:bg-slate-600 rounded-lg p-2 animate-message-in">
            <div class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center text-white text-xs">✓</div>
            <div class="text-xs font-medium text-gray-800 dark:text-white">${name} hat sich angemeldet!</div>
        </div>` + activity.innerHTML.replace('Teilen Sie, um Aktivität zu sehen...', '');
        
        // Check level up
        const newLevel = demoLevels.filter(l => demoReferrals >= l.req).length - 1;
        if (newLevel > demoLevel) {
            demoLevel = newLevel;
            showRewardModal();
        }
    }, 800);
}

function updateDemoUI() {
    document.getElementById('demo-referrals').textContent = demoReferrals;
    document.getElementById('demo-level').textContent = demoLevel;
    
    const nextLevel = demoLevels[demoLevel + 1];
    document.getElementById('demo-remaining').textContent = nextLevel ? nextLevel.req - demoReferrals : '✓';
    
    document.getElementById('demo-level-icon').textContent = demoLevels[demoLevel].icon;
    document.getElementById('demo-level-name').textContent = `Stufe ${demoLevel}: ${demoLevels[demoLevel].name}`;
    document.getElementById('demo-level-reward').textContent = demoLevel > 0 ? demoLevels[demoLevel].reward : 'Starten Sie jetzt!';
    
    // Progress bar
    const currentReq = demoLevels[demoLevel].req;
    const nextReq = nextLevel ? nextLevel.req : currentReq;
    const progress = nextLevel ? ((demoReferrals - currentReq) / (nextReq - currentReq)) * 100 : 100;
    document.getElementById('demo-progress').style.width = progress + '%';
    
    // Milestones
    document.querySelectorAll('[data-milestone]').forEach(el => {
        const milestone = parseInt(el.dataset.milestone);
        if (demoReferrals >= milestone) {
            el.classList.remove('opacity-40');
            el.classList.add('opacity-100');
            el.querySelector('div').classList.remove('bg-gray-300', 'dark:bg-slate-500');
            el.querySelector('div').classList.add('bg-emerald-500');
        }
    });
}

function showRewardModal() {
    document.getElementById('reward-title').textContent = `Stufe ${demoLevel} erreicht!`;
    document.getElementById('reward-text').textContent = demoLevels[demoLevel].reward;
    document.getElementById('demo-reward-modal').classList.remove('hidden');
}

function closeRewardModal() {
    document.getElementById('demo-reward-modal').classList.add('hidden');
}

function resetDemo() {
    demoReferrals = 0;
    demoLevel = 0;
    usedButtons = {};
    
    document.querySelectorAll('.share-btn').forEach(btn => btn.classList.remove('used'));
    document.getElementById('demo-activity').innerHTML = '<div class="text-center text-gray-500 dark:text-gray-400 text-xs py-2">Teilen Sie, um Aktivität zu sehen...</div>';
    
    document.querySelectorAll('[data-milestone]').forEach(el => {
        const milestone = parseInt(el.dataset.milestone);
        if (milestone > 0) {
            el.classList.add('opacity-40');
            el.classList.remove('opacity-100');
            el.querySelector('div').classList.add('bg-gray-300', 'dark:bg-slate-500');
            el.querySelector('div').classList.remove('bg-emerald-500');
        }
    });
    
    updateDemoUI();
}

// ==================== VIRAL NETWORK ====================
const networkPhases = [
    { patients: 1, reach: 0, conversions: 0, title: '🦷 Starten Sie mit einem zufriedenen Patienten', desc: 'Jeder zufriedene Patient kann Ihre Praxis weiterempfehlen.' },
    { patients: 1, reach: 8, conversions: 3, title: '📤 Ihr Patient teilt seinen Empfehlungslink', desc: 'Nach einer Woche: 8 Personen erreicht, 3 neue Patienten!' },
    { patients: 4, reach: 32, conversions: 12, title: '👥 Die Empfehlungen wachsen exponentiell', desc: 'Das Wachstum beschleunigt sich: 12 Neupatienten in 2 Wochen!' },
    { patients: 16, reach: 128, conversions: 47, title: '🚀 Das Netzwerk expandiert weiter', desc: '47 Neupatienten nach einem Monat – exponentielles Wachstum!' },
    { patients: 47, reach: 376, conversions: 89, title: '🏆 89 Neupatienten in nur 3 Monaten!', desc: 'Ohne Werbekosten: Reine Mundpropaganda bringt 89 Neupatienten!' }
];

let networkPhase = 0;
let networkPlaying = true;
let networkInterval = null;

function setNetworkPhase(phase) {
    networkPhase = phase;
    networkPlaying = false;
    document.getElementById('network-play-btn').textContent = '▶ Abspielen';
    renderNetwork();
}

function toggleNetworkPlay() {
    networkPlaying = !networkPlaying;
    document.getElementById('network-play-btn').textContent = networkPlaying ? '⏸ Pause' : '▶ Abspielen';
    if (networkPlaying) startNetworkAnimation();
}

function restartNetwork() {
    networkPhase = 0;
    networkPlaying = true;
    document.getElementById('network-play-btn').textContent = '⏸ Pause';
    renderNetwork();
    startNetworkAnimation();
}

function startNetworkAnimation() {
    clearInterval(networkInterval);
    if (!networkPlaying) return;
    
    networkInterval = setInterval(() => {
        if (!networkPlaying) {
            clearInterval(networkInterval);
            return;
        }
        if (networkPhase < networkPhases.length - 1) {
            networkPhase++;
            renderNetwork();
        } else {
            clearInterval(networkInterval);
        }
    }, 2500);
}

function renderNetwork() {
    const data = networkPhases[networkPhase];
    
    // Update stats
    document.getElementById('network-patients').textContent = data.patients;
    document.getElementById('network-reach').textContent = data.reach;
    document.getElementById('network-conversions').textContent = data.conversions;
    document.getElementById('network-title').textContent = data.title;
    document.getElementById('network-desc').textContent = data.desc;
    
    // Update timeline
    document.querySelectorAll('.network-phase').forEach(el => {
        const phase = parseInt(el.dataset.phase);
        const dot = el.querySelector('div');
        const label = el.querySelector('.text-xs');
        
        if (phase === networkPhase) {
            el.classList.remove('opacity-30');
            el.classList.add('opacity-100');
            dot.classList.remove('bg-slate-600', 'bg-emerald-500');
            dot.classList.add('bg-emerald-400', 'shadow-lg', 'shadow-emerald-400/50', 'scale-150');
            label.classList.remove('text-slate-500');
            label.classList.add('text-emerald-400');
        } else if (phase < networkPhase) {
            el.classList.remove('opacity-30');
            el.classList.add('opacity-100');
            dot.classList.remove('bg-slate-600', 'bg-emerald-400', 'shadow-lg', 'shadow-emerald-400/50', 'scale-150');
            dot.classList.add('bg-emerald-500');
            label.classList.remove('text-emerald-400');
            label.classList.add('text-slate-500');
        } else {
            el.classList.add('opacity-30');
            el.classList.remove('opacity-100');
            dot.classList.remove('bg-emerald-400', 'bg-emerald-500', 'shadow-lg', 'shadow-emerald-400/50', 'scale-150');
            dot.classList.add('bg-slate-600');
            label.classList.remove('text-emerald-400');
            label.classList.add('text-slate-500');
        }
    });
    
    // Generate nodes
    const cx = 144, cy = 144;
    const nodesContainer = document.getElementById('network-nodes');
    const linesContainer = document.getElementById('network-lines');
    nodesContainer.innerHTML = '';
    linesContainer.innerHTML = '';
    
    const nodes = [];
    const lines = [];
    
    // Center node
    nodes.push({ x: cx, y: cy, size: 22, type: 'center', delay: 0 });
    
    if (networkPhase >= 1) {
        const count = Math.min(data.conversions, 5);
        for (let i = 0; i < count; i++) {
            const angle = (i / count) * Math.PI * 2 - Math.PI / 2;
            const x = cx + Math.cos(angle) * 50;
            const y = cy + Math.sin(angle) * 50;
            nodes.push({ x, y, size: 14, type: 'referral', delay: i * 100 });
            lines.push({ x1: cx, y1: cy, x2: x, y2: y, delay: i * 100 });
        }
    }
    
    if (networkPhase >= 2) {
        const count = Math.min(data.conversions - 5, 7);
        for (let i = 0; i < count; i++) {
            const angle = (i / count) * Math.PI * 2 - Math.PI / 2 + 0.3;
            const x = cx + Math.cos(angle) * 90;
            const y = cy + Math.sin(angle) * 90;
            const parentAngle = Math.floor(i / 1.5) / 5 * Math.PI * 2 - Math.PI / 2;
            const px = cx + Math.cos(parentAngle) * 50;
            const py = cy + Math.sin(parentAngle) * 50;
            nodes.push({ x, y, size: 10, type: 'secondary', delay: 500 + i * 80 });
            lines.push({ x1: px, y1: py, x2: x, y2: y, delay: 500 + i * 80 });
        }
    }
    
    if (networkPhase >= 3) {
        const count = Math.min(data.conversions - 12, 10);
        for (let i = 0; i < count; i++) {
            const angle = (i / count) * Math.PI * 2 - Math.PI / 2 + 0.15;
            const x = cx + Math.cos(angle) * 125;
            const y = cy + Math.sin(angle) * 125;
            const parentAngle = Math.floor(i / 1.4) / 7 * Math.PI * 2 - Math.PI / 2 + 0.3;
            const px = cx + Math.cos(parentAngle) * 90;
            const py = cy + Math.sin(parentAngle) * 90;
            nodes.push({ x, y, size: 7, type: 'tertiary', delay: 1000 + i * 50 });
            lines.push({ x1: px, y1: py, x2: x, y2: y, delay: 1000 + i * 50 });
        }
    }
    
    // Render lines
    lines.forEach(line => {
        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'line');
        svg.setAttribute('x1', line.x1);
        svg.setAttribute('y1', line.y1);
        svg.setAttribute('x2', line.x2);
        svg.setAttribute('y2', line.y2);
        svg.setAttribute('stroke', 'rgba(255,255,255,0.15)');
        svg.setAttribute('stroke-width', '1.5');
        svg.style.strokeDasharray = '100';
        svg.style.strokeDashoffset = '100';
        svg.style.animation = `drawLine 0.5s ease forwards ${line.delay}ms`;
        linesContainer.appendChild(svg);
    });
    
    // Render nodes
    nodes.forEach(node => {
        const div = document.createElement('div');
        div.className = `network-node ${node.type} animate-node-appear`;
        div.style.left = (node.x - node.size / 2) + 'px';
        div.style.top = (node.y - node.size / 2) + 'px';
        div.style.width = node.size + 'px';
        div.style.height = node.size + 'px';
        div.style.animationDelay = node.delay + 'ms';
        nodesContainer.appendChild(div);
    });
}

// Add line drawing animation
const styleSheet = document.createElement('style');
styleSheet.textContent = '@keyframes drawLine { to { stroke-dashoffset: 0; } }';
document.head.appendChild(styleSheet);

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    restartWhatsApp();
});
</script>

<!-- CTA Section -->
<section class="py-12 md:py-20 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold mb-4 md:mb-6">
            Bereit für mehr Patienten durch Empfehlungen?
        </h2>
        <p class="text-lg md:text-xl text-white/90 mb-6 md:mb-8">
            Starten Sie noch heute und machen Sie Ihre Patienten zu Ihren besten Werbern.
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
            <a href="/branchen/onlinemarketing" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-full text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors text-sm font-medium">
                <i class="fas fa-bullhorn mr-1"></i> Online-Marketing
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../templates/marketing/footer.php'; ?>
