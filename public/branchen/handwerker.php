<?php
/**
 * Branchenseite: Handwerker
 * Mit drei interaktiven Animationen
 */

$pageTitle = 'Empfehlungsprogramm für Handwerker';
$metaDescription = 'Automatisches Empfehlungsprogramm für Handwerksbetriebe. Zufriedene Kunden empfehlen Sie weiter und erhalten Rabatte oder Gutscheine. DSGVO-konform.';
$currentPage = 'branchen';

require_once __DIR__ . '/../../templates/marketing/header.php';

// Branchenspezifische Daten
$branche = [
    'name' => 'Handwerker',
    'slug' => 'handwerker',
    'icon' => 'fa-hammer',
    'color' => 'amber',
    'heroTitle' => 'Mehr Aufträge durch Kundenempfehlungen',
    'heroSubtitle' => 'Zufriedene Kunden sind Ihre beste Werbung. Mit Leadbusiness machen Sie Mundpropaganda messbar und belohnen treue Kunden automatisch.',
];

$vorteile = [
    [
        'icon' => 'fa-users',
        'title' => 'Qualifizierte Anfragen',
        'text' => 'Empfohlene Kunden vertrauen Ihnen bereits – durch die persönliche Empfehlung von Nachbarn, Freunden und Familie.'
    ],
    [
        'icon' => 'fa-euro-sign',
        'title' => 'Geringere Akquisekosten',
        'text' => 'Empfehlungsmarketing ist günstiger als klassische Werbung und bringt Kunden, die wirklich zu Ihnen passen.'
    ],
    [
        'icon' => 'fa-map-marker-alt',
        'title' => 'Lokale Reichweite',
        'text' => 'Empfehlungen im Freundeskreis bringen oft Aufträge aus der direkten Umgebung – kurze Anfahrtswege, mehr Effizienz.'
    ],
    [
        'icon' => 'fa-clock',
        'title' => 'Null Aufwand für Sie',
        'text' => 'Das System läuft vollautomatisch. Sie konzentrieren sich auf Ihr Handwerk – wir kümmern uns um den Rest.'
    ],
];

$belohnungen = [
    ['stufe' => 3, 'belohnung' => '50€ Rabatt auf den nächsten Auftrag'],
    ['stufe' => 5, 'belohnung' => 'Gratis Anfahrt bei allen Aufträgen (1 Jahr)'],
    ['stufe' => 10, 'belohnung' => '150€ Wertgutschein'],
];

$testimonial = [
    'text' => 'Früher hatte ich vielleicht 2-3 Empfehlungen pro Jahr, von denen ich wusste. Jetzt sehe ich genau, wer empfiehlt, und kann mich bedanken. In 4 Monaten kamen 23 neue Aufträge durch Empfehlungen!',
    'name' => 'Klaus Bergmann',
    'rolle' => 'Bergmann Sanitär & Heizung',
    'initialen' => 'KB',
];

$stats = [
    'empfehler' => '156',
    'conversions' => '47',
    'rate' => '30%',
];

$gewerke = [
    ['icon' => 'fa-faucet', 'name' => 'Sanitär & Heizung'],
    ['icon' => 'fa-bolt', 'name' => 'Elektriker'],
    ['icon' => 'fa-paint-roller', 'name' => 'Maler & Lackierer'],
    ['icon' => 'fa-ruler', 'name' => 'Tischler & Schreiner'],
    ['icon' => 'fa-hard-hat', 'name' => 'Dachdecker'],
    ['icon' => 'fa-door-open', 'name' => 'Fenster & Türen'],
    ['icon' => 'fa-warehouse', 'name' => 'Garten- & Landschaftsbau'],
    ['icon' => 'fa-tools', 'name' => 'Schlosser'],
    ['icon' => 'fa-home', 'name' => 'Trockenbau'],
    ['icon' => 'fa-wrench', 'name' => 'Klempner'],
    ['icon' => 'fa-snowflake', 'name' => 'Kälte- & Klimatechnik'],
    ['icon' => 'fa-th-large', 'name' => 'Fliesenleger'],
];
?>

<!-- Hero Section -->
<section class="relative py-16 md:py-24 overflow-hidden">
    <!-- Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-amber-600 to-orange-700"></div>
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
                            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-hammer text-amber-600 text-xl"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">Bergmann Sanitär</div>
                                <div class="text-sm text-gray-500">empfohlen.de/bergmann-sanitaer</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-amber-600"><?= $stats['empfehler'] ?></div>
                                <div class="text-xs text-gray-500">Empfehler</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600"><?= $stats['conversions'] ?></div>
                                <div class="text-xs text-gray-500">Neue Aufträge</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600"><?= $stats['rate'] ?></div>
                                <div class="text-xs text-gray-500">Conversion</div>
                            </div>
                        </div>
                        <div class="text-center text-sm text-gray-500">
                            <i class="fas fa-chart-line text-green-500 mr-1"></i>
                            +18% mehr Empfehlungen als letzten Monat
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Gewerke Section -->
<section class="py-8 md:py-12 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-center text-gray-500 dark:text-gray-400 mb-6 font-medium">Für alle Gewerke geeignet</p>
        <div class="flex flex-wrap justify-center gap-3">
            <?php foreach ($gewerke as $gewerk): ?>
            <div class="px-4 py-2 bg-white dark:bg-slate-700 rounded-full shadow-sm flex items-center gap-2">
                <i class="fas <?= $gewerk['icon'] ?> text-amber-500"></i>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300"><?= $gewerk['name'] ?></span>
            </div>
            <?php endforeach; ?>
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
                Nutzen Sie das Vertrauen Ihrer Kunden für nachhaltiges Wachstum.
            </p>
        </div>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
            <?php foreach ($vorteile as $vorteil): ?>
            <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-6 hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center text-amber-600 dark:text-amber-400 text-xl mb-4">
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
            <span class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-500 to-orange-600 text-white px-5 py-2 rounded-full text-sm font-bold shadow-lg mb-4">
                <span>🔧</span> Live erleben
            </span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                So funktioniert Empfehlungsmarketing für Handwerker
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg max-w-2xl mx-auto">
                Drei interaktive Demos zeigen den Weg vom zufriedenen Kunden zum vollen Auftragsbuch.
            </p>
        </div>
        
        <!-- Tab Navigation -->
        <div class="flex flex-wrap justify-center gap-3 mb-8" id="handwerker-animation-tabs">
            <button onclick="showHandwerkerAnimation('pipeline')" id="tab-pipeline" class="hw-tab active px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-lg">
                🔧 Auftrags-Pipeline
            </button>
            <button onclick="showHandwerkerAnimation('kosten')" id="tab-kosten" class="hw-tab px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-white dark:bg-slate-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-600 hover:shadow-md">
                💰 Werbekosten-Rechner
            </button>
            <button onclick="showHandwerkerAnimation('nachbar')" id="tab-nachbar" class="hw-tab px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-white dark:bg-slate-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-600 hover:shadow-md">
                🏘️ Nachbarschafts-Effekt
            </button>
        </div>
        
        <!-- Animation Containers -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-8 min-h-[600px] shadow-lg">
            
            <!-- ========================================= -->
            <!-- ANIMATION 1: AUFTRAGS-PIPELINE           -->
            <!-- ========================================= -->
            <div id="animation-pipeline" class="hw-animation-content">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Die Auftrags-Pipeline</h3>
                    <p class="text-gray-500 dark:text-gray-400">Vom zufriedenen Kunden zum vollen Auftragsbuch</p>
                </div>
                
                <div class="max-w-3xl mx-auto">
                    <!-- Progress Line -->
                    <div class="relative mb-8">
                        <div class="absolute top-8 left-0 right-0 h-1 bg-gray-200 dark:bg-slate-700 rounded-full">
                            <div id="pipeline-progress" class="h-full bg-gradient-to-r from-amber-500 to-orange-500 rounded-full transition-all duration-500" style="width: 0%"></div>
                        </div>
                        
                        <!-- Pipeline Steps -->
                        <div class="grid grid-cols-6 gap-2 relative">
                            <!-- Step 1: Auftrag -->
                            <div id="pipeline-step-1" class="pipeline-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">
                                    🔧
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Auftrag</span>
                            </div>
                            
                            <!-- Step 2: Zufrieden -->
                            <div id="pipeline-step-2" class="pipeline-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">
                                    😊
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Zufrieden</span>
                            </div>
                            
                            <!-- Step 3: Empfehlung -->
                            <div id="pipeline-step-3" class="pipeline-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">
                                    💬
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Empfehlung</span>
                            </div>
                            
                            <!-- Step 4: Anfrage -->
                            <div id="pipeline-step-4" class="pipeline-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">
                                    📞
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Anfrage</span>
                            </div>
                            
                            <!-- Step 5: Neuer Auftrag -->
                            <div id="pipeline-step-5" class="pipeline-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">
                                    ✅
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Auftrag!</span>
                            </div>
                            
                            <!-- Step 6: Folgeaufträge -->
                            <div id="pipeline-step-6" class="pipeline-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">
                                    🔄
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Mehr!</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Animated Tool -->
                    <div class="relative h-24 mb-6 overflow-hidden bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-xl">
                        <div id="pipeline-tool" class="absolute transition-all duration-1000 ease-out flex items-center gap-3" style="left: 5%; top: 50%; transform: translateY(-50%)">
                            <div class="text-5xl" id="tool-emoji">🔧</div>
                            <div id="tool-speech" class="bg-white dark:bg-slate-700 rounded-lg px-3 py-2 text-sm shadow-md opacity-0 transition-opacity duration-300 max-w-[200px]">
                                <span id="speech-text">Arbeit erledigt!</span>
                            </div>
                        </div>
                        <div id="pipeline-effects" class="absolute inset-0 pointer-events-none"></div>
                    </div>
                    
                    <!-- Auftragsbuch -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">📖 Auftragsbuch</span>
                            <span id="auftragsbuch-status" class="text-sm text-amber-600 dark:text-amber-400 font-medium">Wartend...</span>
                        </div>
                        <div class="h-4 bg-gray-200 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div id="auftragsbuch-bar" class="h-full bg-gradient-to-r from-amber-400 via-amber-500 to-orange-500 rounded-full transition-all duration-1000" style="width: 20%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-400 mt-1">
                            <span>Leer</span>
                            <span>Halb voll</span>
                            <span>Voll! 🎉</span>
                        </div>
                    </div>
                    
                    <!-- Info Box -->
                    <div id="pipeline-info" class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-xl p-4 mb-6 text-center">
                        <div id="pipeline-text" class="text-gray-700 dark:text-gray-300 font-medium">
                            Klicken Sie, um die Pipeline zu starten!
                        </div>
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-4 text-center">
                            <div id="pipeline-auftraege" class="text-2xl font-bold text-amber-600 dark:text-amber-400">0</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Neue Aufträge</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-4 text-center">
                            <div id="pipeline-umsatz" class="text-2xl font-bold text-green-600 dark:text-green-400">0€</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Umsatz</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-4 text-center">
                            <div id="pipeline-reward" class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">-</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Belohnung</div>
                        </div>
                    </div>
                    
                    <!-- Action Button -->
                    <button onclick="startPipeline()" id="pipeline-btn" class="w-full py-4 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl font-bold text-lg hover:shadow-lg hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-play"></i>
                        <span>Pipeline starten</span>
                    </button>
                    
                    <button onclick="resetPipeline()" class="w-full mt-3 py-2 text-gray-500 dark:text-gray-400 text-sm hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                        ↻ Demo zurücksetzen
                    </button>
                </div>
            </div>
            
            <!-- ========================================= -->
            <!-- ANIMATION 2: WERBEKOSTEN-RECHNER         -->
            <!-- ========================================= -->
            <div id="animation-kosten" class="hw-animation-content hidden">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Werbekosten-Rechner</h3>
                    <p class="text-gray-500 dark:text-gray-400">Klassische Werbung vs. Empfehlungen – der ehrliche Vergleich</p>
                </div>
                
                <div class="max-w-2xl mx-auto">
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-slate-800 dark:to-slate-700 rounded-2xl p-6 md:p-8">
                        
                        <!-- Slider: Auftragswert -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Durchschnittlicher Auftragswert?
                            </label>
                            <div class="flex items-center gap-4">
                                <input type="range" id="kosten-wert-slider" min="200" max="5000" step="100" value="1500" class="flex-1 h-3 bg-gray-300 dark:bg-slate-600 rounded-full appearance-none cursor-pointer accent-amber-500" oninput="updateKosten()">
                                <div class="bg-white dark:bg-slate-600 rounded-xl px-4 py-2 min-w-[100px] text-center">
                                    <span id="kosten-wert" class="text-2xl font-bold text-gray-900 dark:text-white">1.500€</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Slider: Gewünschte Aufträge -->
                        <div class="mb-8">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Wie viele Aufträge pro Monat?
                            </label>
                            <div class="flex items-center gap-4">
                                <input type="range" id="kosten-auftraege-slider" min="1" max="30" value="10" class="flex-1 h-3 bg-gray-300 dark:bg-slate-600 rounded-full appearance-none cursor-pointer accent-amber-500" oninput="updateKosten()">
                                <div class="bg-white dark:bg-slate-600 rounded-xl px-4 py-2 min-w-[80px] text-center">
                                    <span id="kosten-auftraege" class="text-2xl font-bold text-gray-900 dark:text-white">10</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Comparison Cards -->
                        <div class="grid md:grid-cols-2 gap-6 mb-8">
                            <!-- Klassische Werbung -->
                            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 border-2 border-red-200 dark:border-red-900/50">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-newspaper text-gray-500 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white">Klassische Werbung</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Anzeigen, Flyer, etc.</div>
                                    </div>
                                </div>
                                
                                <!-- Cost breakdown -->
                                <div class="space-y-2 mb-4 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Zeitungsanzeige/Monat</span>
                                        <span class="text-gray-700 dark:text-gray-300">~300€</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Google Ads/Monat</span>
                                        <span class="text-gray-700 dark:text-gray-300">~400€</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Flyer & Druck</span>
                                        <span class="text-gray-700 dark:text-gray-300">~150€</span>
                                    </div>
                                </div>
                                
                                <!-- Money Stack -->
                                <div class="h-20 flex items-end justify-center mb-4">
                                    <div id="kosten-werbung-stack" class="flex flex-wrap justify-center gap-1">
                                    </div>
                                </div>
                                
                                <div class="text-center border-t pt-4">
                                    <div class="text-3xl font-black text-red-600 dark:text-red-400" id="kosten-werbung-total">850€</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Kosten pro Monat</div>
                                    <div class="text-xs text-gray-400 mt-1" id="kosten-werbung-pro">= 85€ pro Auftrag</div>
                                </div>
                            </div>
                            
                            <!-- Empfehlungen -->
                            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 border-2 border-amber-400 dark:border-amber-500 relative overflow-hidden">
                                <div class="absolute top-2 right-2 bg-amber-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                    EMPFOHLEN
                                </div>
                                
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-users text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white">Empfehlungen</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Mit Leadbusiness</div>
                                    </div>
                                </div>
                                
                                <!-- Cost breakdown -->
                                <div class="space-y-2 mb-4 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Leadbusiness Starter</span>
                                        <span class="text-gray-700 dark:text-gray-300">49€/Monat</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Belohnungen (Ø)</span>
                                        <span class="text-gray-700 dark:text-gray-300" id="kosten-belohnungen">~30€</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Sonstige Kosten</span>
                                        <span class="text-green-600 dark:text-green-400">0€</span>
                                    </div>
                                </div>
                                
                                <!-- Money Stack (smaller) -->
                                <div class="h-20 flex items-end justify-center mb-4">
                                    <div id="kosten-empfehlung-stack" class="flex flex-wrap justify-center gap-1">
                                    </div>
                                </div>
                                
                                <div class="text-center border-t pt-4">
                                    <div class="text-3xl font-black text-amber-600 dark:text-amber-400" id="kosten-empfehlung-total">79€</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Kosten pro Monat</div>
                                    <div class="text-xs text-amber-500 mt-1" id="kosten-empfehlung-pro">= 7,90€ pro Auftrag</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Savings Result -->
                        <div id="kosten-savings-box" class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-2xl p-6 text-white text-center">
                            <div class="text-lg mb-2">💰 Ihre Ersparnis pro Monat</div>
                            <div class="text-5xl font-black mb-2" id="kosten-ersparnis">771€</div>
                            <div class="text-xl text-white/90 mb-3">
                                <span id="kosten-prozent" class="font-bold">91%</span> weniger Werbekosten!
                            </div>
                            <div class="bg-white/20 rounded-lg px-4 py-2 inline-block">
                                <span class="text-lg">Pro Jahr: </span>
                                <span class="text-2xl font-bold" id="kosten-jahr">9.252€</span>
                                <span class="text-lg"> gespart</span>
                            </div>
                        </div>
                        
                        <!-- Pro Tip -->
                        <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-4 flex items-start gap-3">
                            <div class="text-2xl">💡</div>
                            <div class="text-sm text-yellow-800 dark:text-yellow-200">
                                <strong>Bonus:</strong> Empfohlene Kunden sind oft die besseren Kunden! Sie vertrauen Ihnen schon, zahlen pünktlich und empfehlen selbst weiter.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ========================================= -->
            <!-- ANIMATION 3: NACHBARSCHAFTS-EFFEKT       -->
            <!-- ========================================= -->
            <div id="animation-nachbar" class="hw-animation-content hidden">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Der Nachbarschafts-Effekt</h3>
                    <p class="text-gray-500 dark:text-gray-400">Wie sich Ihre gute Arbeit in der Straße herumspricht</p>
                </div>
                
                <div class="max-w-3xl mx-auto">
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-slate-800 dark:to-slate-700 rounded-2xl p-6 md:p-8 mb-6">
                        
                        <!-- Stats Bar -->
                        <div class="grid grid-cols-4 gap-3 mb-6">
                            <div class="bg-white dark:bg-slate-600 rounded-xl p-3 text-center">
                                <div id="nachbar-woche" class="text-2xl font-bold text-amber-600 dark:text-amber-400">0</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Woche</div>
                            </div>
                            <div class="bg-white dark:bg-slate-600 rounded-xl p-3 text-center">
                                <div id="nachbar-auftraege" class="text-2xl font-bold text-green-600 dark:text-green-400">0</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Aufträge</div>
                            </div>
                            <div class="bg-white dark:bg-slate-600 rounded-xl p-3 text-center">
                                <div id="nachbar-umsatz" class="text-2xl font-bold text-blue-600 dark:text-blue-400">0€</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Umsatz</div>
                            </div>
                            <div class="bg-white dark:bg-slate-600 rounded-xl p-3 text-center">
                                <div id="nachbar-km" class="text-2xl font-bold text-purple-600 dark:text-purple-400">0km</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Anfahrt</div>
                            </div>
                        </div>
                        
                        <!-- Street Visualization -->
                        <div class="relative mb-6">
                            <!-- Street -->
                            <div class="bg-gray-300 dark:bg-slate-600 h-4 rounded-full mb-4"></div>
                            
                            <!-- Houses Row -->
                            <div id="nachbar-strasse" class="grid grid-cols-7 gap-2 md:gap-4">
                                <!-- House 1 (Start) -->
                                <div id="house-1" class="nachbar-house flex flex-col items-center">
                                    <div class="house-icon w-12 h-12 md:w-16 md:h-16 bg-amber-500 rounded-lg flex items-center justify-center text-2xl md:text-3xl shadow-lg transform transition-all duration-500">
                                        🏠
                                    </div>
                                    <div class="w-2 h-2 bg-amber-500 rounded-full mt-2 animate-pulse"></div>
                                    <span class="text-xs text-amber-600 dark:text-amber-400 font-bold mt-1">START</span>
                                </div>
                                
                                <!-- House 2 -->
                                <div id="house-2" class="nachbar-house flex flex-col items-center opacity-50">
                                    <div class="house-icon w-12 h-12 md:w-16 md:h-16 bg-gray-200 dark:bg-slate-700 rounded-lg flex items-center justify-center text-2xl md:text-3xl shadow transform transition-all duration-500">
                                        🏡
                                    </div>
                                    <div class="w-2 h-2 bg-gray-300 rounded-full mt-2"></div>
                                </div>
                                
                                <!-- House 3 -->
                                <div id="house-3" class="nachbar-house flex flex-col items-center opacity-50">
                                    <div class="house-icon w-12 h-12 md:w-16 md:h-16 bg-gray-200 dark:bg-slate-700 rounded-lg flex items-center justify-center text-2xl md:text-3xl shadow transform transition-all duration-500">
                                        🏠
                                    </div>
                                    <div class="w-2 h-2 bg-gray-300 rounded-full mt-2"></div>
                                </div>
                                
                                <!-- House 4 -->
                                <div id="house-4" class="nachbar-house flex flex-col items-center opacity-50">
                                    <div class="house-icon w-12 h-12 md:w-16 md:h-16 bg-gray-200 dark:bg-slate-700 rounded-lg flex items-center justify-center text-2xl md:text-3xl shadow transform transition-all duration-500">
                                        🏡
                                    </div>
                                    <div class="w-2 h-2 bg-gray-300 rounded-full mt-2"></div>
                                </div>
                                
                                <!-- House 5 -->
                                <div id="house-5" class="nachbar-house flex flex-col items-center opacity-50">
                                    <div class="house-icon w-12 h-12 md:w-16 md:h-16 bg-gray-200 dark:bg-slate-700 rounded-lg flex items-center justify-center text-2xl md:text-3xl shadow transform transition-all duration-500">
                                        🏠
                                    </div>
                                    <div class="w-2 h-2 bg-gray-300 rounded-full mt-2"></div>
                                </div>
                                
                                <!-- House 6 -->
                                <div id="house-6" class="nachbar-house flex flex-col items-center opacity-50">
                                    <div class="house-icon w-12 h-12 md:w-16 md:h-16 bg-gray-200 dark:bg-slate-700 rounded-lg flex items-center justify-center text-2xl md:text-3xl shadow transform transition-all duration-500">
                                        🏡
                                    </div>
                                    <div class="w-2 h-2 bg-gray-300 rounded-full mt-2"></div>
                                </div>
                                
                                <!-- House 7 -->
                                <div id="house-7" class="nachbar-house flex flex-col items-center opacity-50">
                                    <div class="house-icon w-12 h-12 md:w-16 md:h-16 bg-gray-200 dark:bg-slate-700 rounded-lg flex items-center justify-center text-2xl md:text-3xl shadow transform transition-all duration-500">
                                        🏠
                                    </div>
                                    <div class="w-2 h-2 bg-gray-300 rounded-full mt-2"></div>
                                </div>
                            </div>
                            
                            <!-- Van animation container -->
                            <div id="nachbar-van" class="absolute -bottom-2 transition-all duration-1000" style="left: 5%">
                                <div class="text-3xl transform -scale-x-100">🚐</div>
                            </div>
                            
                            <!-- Speech bubbles container -->
                            <div id="nachbar-effects" class="absolute inset-0 pointer-events-none overflow-hidden"></div>
                        </div>
                        
                        <!-- Legend -->
                        <div class="flex justify-center gap-6 text-sm mb-6">
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 bg-amber-500 rounded"></div>
                                <span class="text-gray-600 dark:text-gray-400">Auftrag erledigt</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 bg-green-500 rounded"></div>
                                <span class="text-gray-600 dark:text-gray-400">Durch Empfehlung</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 bg-gray-300 dark:bg-slate-600 rounded"></div>
                                <span class="text-gray-600 dark:text-gray-400">Noch nicht</span>
                            </div>
                        </div>
                        
                        <!-- Info Text -->
                        <div id="nachbar-info" class="text-center bg-white dark:bg-slate-600 rounded-xl p-4 mb-4">
                            <div id="nachbar-text" class="text-gray-700 dark:text-gray-300 font-medium">
                                🏠 Sie haben gerade einen Auftrag in der Musterstraße 1 erledigt. Klicken Sie, um zu sehen, was als nächstes passiert!
                            </div>
                        </div>
                        
                        <!-- Action Button -->
                        <button onclick="triggerNachbar()" id="nachbar-btn" class="w-full py-4 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl font-bold text-lg hover:shadow-lg hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-home"></i>
                            <span>Nächste Woche</span>
                        </button>
                        
                        <button onclick="resetNachbar()" class="w-full mt-3 py-2 text-gray-500 dark:text-gray-400 text-sm hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                            ↻ Demo zurücksetzen
                        </button>
                    </div>
                    
                    <!-- Final Celebration -->
                    <div id="nachbar-celebration" class="hidden">
                        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl p-6 text-white text-center animate-bounce-in">
                            <div class="text-4xl mb-2">🏘️🎉🔧</div>
                            <div class="font-black text-xl mb-2">DIE GANZE STRASSE KENNT SIE!</div>
                            <div class="text-white/90">Aus 1 Auftrag wurden 7 – ohne einen Cent Werbung!</div>
                            <div class="mt-3 grid grid-cols-3 gap-2 text-sm">
                                <div class="bg-white/20 rounded-lg px-3 py-2">
                                    <div class="font-bold">7</div>
                                    <div class="text-xs text-white/70">Aufträge</div>
                                </div>
                                <div class="bg-white/20 rounded-lg px-3 py-2">
                                    <div class="font-bold">~5km</div>
                                    <div class="text-xs text-white/70">Anfahrt gesamt</div>
                                </div>
                                <div class="bg-white/20 rounded-lg px-3 py-2">
                                    <div class="font-bold">0€</div>
                                    <div class="text-xs text-white/70">Werbekosten</div>
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
    @keyframes pulse-amber {
        0%, 100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); }
        50% { box-shadow: 0 0 0 15px rgba(245, 158, 11, 0); }
    }
    @keyframes float-up {
        0% { opacity: 1; transform: translateY(0) scale(1); }
        100% { opacity: 0; transform: translateY(-50px) scale(0.5); }
    }
    @keyframes speech-appear {
        0% { opacity: 0; transform: translateY(10px) scale(0.8); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }
    @keyframes confetti-fall {
        0% { transform: translateY(-100%) rotate(0deg); opacity: 1; }
        100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
    }
    @keyframes house-activate {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
    @keyframes van-bounce {
        0%, 100% { transform: translateY(0) scaleX(-1); }
        50% { transform: translateY(-5px) scaleX(-1); }
    }
    
    /* Animation Classes */
    .animate-bounce-in { animation: bounceIn 0.5s ease forwards; }
    .animate-pulse-amber { animation: pulse-amber 2s ease-in-out infinite; }
    .animate-house-activate { animation: house-activate 0.5s ease forwards; }
    .animate-van-bounce { animation: van-bounce 0.3s ease infinite; }
    
    /* Tab Styling */
    .hw-tab.active {
        background: linear-gradient(to right, #f59e0b, #ea580c);
        color: white;
        box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.3);
        border: none;
    }
    
    /* Pipeline step styles */
    .pipeline-step.active {
        opacity: 1 !important;
    }
    .pipeline-step.active > div:first-child {
        background: linear-gradient(to bottom right, #f59e0b, #ea580c) !important;
        transform: scale(1.1);
        box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.5);
    }
    .pipeline-step.completed {
        opacity: 1 !important;
    }
    .pipeline-step.completed > div:first-child {
        background: #fef3c7 !important;
    }
    .dark .pipeline-step.completed > div:first-child {
        background: #78350f !important;
    }
    
    /* House activated state */
    .nachbar-house.activated {
        opacity: 1 !important;
    }
    .nachbar-house.activated .house-icon {
        background: linear-gradient(to bottom right, #f59e0b, #ea580c) !important;
        animation: house-activate 0.5s ease forwards;
    }
    .nachbar-house.recommended {
        opacity: 1 !important;
    }
    .nachbar-house.recommended .house-icon {
        background: linear-gradient(to bottom right, #22c55e, #16a34a) !important;
        animation: house-activate 0.5s ease forwards;
    }
    
    /* Range slider styling */
    input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 24px;
        height: 24px;
        background: linear-gradient(to bottom right, #f59e0b, #ea580c);
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
    input[type="range"]::-moz-range-thumb {
        width: 24px;
        height: 24px;
        background: linear-gradient(to bottom right, #f59e0b, #ea580c);
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
    
    /* Money icon */
    .money-icon {
        display: inline-flex;
        font-size: 1.5rem;
    }
    
    /* Speech bubble animation */
    .speech-bubble {
        animation: speech-appear 0.5s ease forwards;
    }
</style>

<!-- Animation JavaScript -->
<script>
// ==================== TAB SWITCHING ====================
function showHandwerkerAnimation(type) {
    document.querySelectorAll('.hw-animation-content').forEach(el => el.classList.add('hidden'));
    document.getElementById('animation-' + type).classList.remove('hidden');
    
    document.querySelectorAll('.hw-tab').forEach(tab => {
        tab.classList.remove('active');
        tab.classList.add('bg-white', 'dark:bg-slate-700', 'text-gray-600', 'dark:text-gray-300', 'border', 'border-gray-200', 'dark:border-slate-600');
    });
    const activeTab = document.getElementById('tab-' + type);
    activeTab.classList.add('active');
    activeTab.classList.remove('bg-white', 'dark:bg-slate-700', 'text-gray-600', 'dark:text-gray-300', 'border', 'border-gray-200', 'dark:border-slate-600');
    
    if (type === 'pipeline') resetPipeline();
    if (type === 'kosten') updateKosten();
    if (type === 'nachbar') resetNachbar();
}

// ==================== ANIMATION 1: AUFTRAGS-PIPELINE ====================
let pipelineStep = 0;
let pipelineInterval = null;
const pipelineSteps = [
    { text: '🔧 Sie erledigen einen Auftrag bei Familie Müller...', progress: 16, tool: '🔧', speech: 'Arbeit erledigt!', auftragsbuch: 25 },
    { text: '😊 Familie Müller ist begeistert von Ihrer Arbeit!', progress: 32, tool: '😊', speech: 'Super Arbeit!', auftragsbuch: 30 },
    { text: '💬 "Der Handwerker war super!" – Frau Müller erzählt der Nachbarin...', progress: 48, tool: '👩', speech: 'Unser Handwerker war toll!', auftragsbuch: 35 },
    { text: '📞 Die Nachbarin ruft Sie an – sie braucht auch Hilfe!', progress: 64, tool: '📞', speech: 'Können Sie auch bei mir?', auftragsbuch: 45, auftrag: true },
    { text: '✅ Neuer Auftrag! Dank der Empfehlung von Familie Müller.', progress: 80, tool: '✅', speech: 'Auftrag gesichert!', auftragsbuch: 60, umsatz: 1200 },
    { text: '🔄 Und der neue Kunde empfiehlt Sie auch weiter... der Kreislauf beginnt!', progress: 100, tool: '🎉', speech: 'Die Pipeline füllt sich!', auftragsbuch: 85, complete: true, reward: '50€ Rabatt' }
];

function startPipeline() {
    if (pipelineStep > 0) return;
    
    document.getElementById('pipeline-btn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Läuft...</span>';
    document.getElementById('pipeline-btn').disabled = true;
    document.getElementById('tool-speech').style.opacity = '1';
    
    pipelineInterval = setInterval(() => {
        if (pipelineStep >= pipelineSteps.length) {
            clearInterval(pipelineInterval);
            document.getElementById('pipeline-btn').innerHTML = '<i class="fas fa-redo"></i> <span>Nochmal ansehen</span>';
            document.getElementById('pipeline-btn').disabled = false;
            document.getElementById('pipeline-btn').onclick = resetPipeline;
            createConfetti();
            return;
        }
        
        const step = pipelineSteps[pipelineStep];
        
        document.getElementById('pipeline-progress').style.width = step.progress + '%';
        document.getElementById('auftragsbuch-bar').style.width = step.auftragsbuch + '%';
        
        // Update tool
        document.getElementById('tool-emoji').textContent = step.tool;
        document.getElementById('speech-text').textContent = step.speech;
        
        // Move tool
        const toolPos = 5 + (pipelineStep / 5) * 75;
        document.getElementById('pipeline-tool').style.left = toolPos + '%';
        
        // Update steps
        for (let i = 1; i <= 6; i++) {
            const stepEl = document.getElementById('pipeline-step-' + i);
            stepEl.classList.remove('active', 'completed');
            if (i < pipelineStep + 1) stepEl.classList.add('completed');
            if (i === pipelineStep + 1) stepEl.classList.add('active');
        }
        
        document.getElementById('pipeline-text').innerHTML = step.text;
        
        if (step.auftrag) {
            document.getElementById('pipeline-auftraege').textContent = '1';
            addFloatingEffect('pipeline-effects', '📋', 3);
        }
        
        if (step.umsatz) {
            document.getElementById('pipeline-umsatz').textContent = step.umsatz + '€';
            addFloatingEffect('pipeline-effects', '💰', 3);
        }
        
        if (step.reward) {
            document.getElementById('pipeline-reward').textContent = step.reward;
            document.getElementById('pipeline-auftraege').textContent = '3';
            document.getElementById('pipeline-umsatz').textContent = '3.600€';
            document.getElementById('auftragsbuch-status').textContent = 'Fast voll! 🎉';
        }
        
        if (step.complete) {
            document.getElementById('pipeline-info').classList.remove('from-amber-50', 'to-orange-50', 'dark:from-amber-900/20', 'dark:to-orange-900/20');
            document.getElementById('pipeline-info').classList.add('from-amber-500', 'to-orange-600');
            document.getElementById('pipeline-text').classList.add('text-white');
            addFloatingEffect('pipeline-effects', '⭐', 5);
        }
        
        pipelineStep++;
    }, 2000);
}

function resetPipeline() {
    clearInterval(pipelineInterval);
    pipelineStep = 0;
    
    document.getElementById('pipeline-progress').style.width = '0%';
    document.getElementById('auftragsbuch-bar').style.width = '20%';
    document.getElementById('auftragsbuch-status').textContent = 'Wartend...';
    document.getElementById('tool-emoji').textContent = '🔧';
    document.getElementById('pipeline-tool').style.left = '5%';
    document.getElementById('tool-speech').style.opacity = '0';
    
    for (let i = 1; i <= 6; i++) {
        document.getElementById('pipeline-step-' + i).classList.remove('active', 'completed');
    }
    
    document.getElementById('pipeline-auftraege').textContent = '0';
    document.getElementById('pipeline-umsatz').textContent = '0€';
    document.getElementById('pipeline-reward').textContent = '-';
    
    document.getElementById('pipeline-info').classList.remove('from-amber-500', 'to-orange-600');
    document.getElementById('pipeline-info').classList.add('from-amber-50', 'to-orange-50', 'dark:from-amber-900/20', 'dark:to-orange-900/20');
    document.getElementById('pipeline-text').classList.remove('text-white');
    document.getElementById('pipeline-text').innerHTML = 'Klicken Sie, um die Pipeline zu starten!';
    
    document.getElementById('pipeline-btn').innerHTML = '<i class="fas fa-play"></i> <span>Pipeline starten</span>';
    document.getElementById('pipeline-btn').disabled = false;
    document.getElementById('pipeline-btn').onclick = startPipeline;
    
    document.getElementById('pipeline-effects').innerHTML = '';
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

// ==================== ANIMATION 2: WERBEKOSTEN-RECHNER ====================
function updateKosten() {
    const auftragswert = parseInt(document.getElementById('kosten-wert-slider').value);
    const auftraege = parseInt(document.getElementById('kosten-auftraege-slider').value);
    
    document.getElementById('kosten-wert').textContent = auftragswert.toLocaleString('de-DE') + '€';
    document.getElementById('kosten-auftraege').textContent = auftraege;
    
    // Klassische Werbung: Fixkosten + variable
    const werbungFix = 850; // Zeitung + Google + Flyer
    const werbungProAuftrag = werbungFix / auftraege;
    
    // Empfehlungen: Leadbusiness + Belohnungen
    const leadbusiness = 49;
    const belohnungenProAuftrag = Math.round(auftragswert * 0.02); // ~2% als Belohnung
    const belohnungenGesamt = Math.round(belohnungenProAuftrag * auftraege * 0.3); // 30% lösen ein
    const empfehlungGesamt = leadbusiness + belohnungenGesamt;
    const empfehlungProAuftrag = empfehlungGesamt / auftraege;
    
    document.getElementById('kosten-belohnungen').textContent = '~' + belohnungenGesamt + '€';
    document.getElementById('kosten-werbung-total').textContent = werbungFix + '€';
    document.getElementById('kosten-werbung-pro').textContent = '= ' + Math.round(werbungProAuftrag) + '€ pro Auftrag';
    document.getElementById('kosten-empfehlung-total').textContent = empfehlungGesamt + '€';
    document.getElementById('kosten-empfehlung-pro').textContent = '= ' + empfehlungProAuftrag.toFixed(2) + '€ pro Auftrag';
    
    const ersparnis = werbungFix - empfehlungGesamt;
    const prozent = Math.round((ersparnis / werbungFix) * 100);
    
    document.getElementById('kosten-ersparnis').textContent = ersparnis.toLocaleString('de-DE') + '€';
    document.getElementById('kosten-prozent').textContent = prozent + '%';
    document.getElementById('kosten-jahr').textContent = (ersparnis * 12).toLocaleString('de-DE') + '€';
    
    // Update money stacks
    updateMoneyStack('kosten-werbung-stack', Math.min(werbungFix / 100, 10), '💸');
    updateMoneyStack('kosten-empfehlung-stack', Math.min(empfehlungGesamt / 50, 3), '💵');
}

function updateMoneyStack(containerId, count, emoji) {
    const container = document.getElementById(containerId);
    const targetCount = Math.floor(count);
    
    container.innerHTML = '';
    for (let i = 0; i < targetCount; i++) {
        const money = document.createElement('span');
        money.className = 'money-icon';
        money.textContent = emoji;
        container.appendChild(money);
    }
}

// ==================== ANIMATION 3: NACHBARSCHAFTS-EFFEKT ====================
let nachbarWoche = 0;
const nachbarData = [
    { woche: 1, text: '💬 Familie Müller erzählt den Nachbarn von Ihrer tollen Arbeit...', houses: [2], auftraege: 1, umsatz: 1200 },
    { woche: 2, text: '📞 Der Nachbar aus Nr. 3 ruft an: "Können Sie auch bei mir?"', houses: [3], auftraege: 2, umsatz: 2800 },
    { woche: 3, text: '🏠 Nr. 5 hat es gesehen und will auch renovieren!', houses: [5], auftraege: 3, umsatz: 4500 },
    { woche: 4, text: '👋 "Ich hab Sie bei den Müllers arbeiten sehen..." – Nr. 4 meldet sich!', houses: [4], auftraege: 4, umsatz: 6200 },
    { woche: 6, text: '🎉 Die ganze Straße spricht über Sie! Nr. 6 und 7 wollen Termine.', houses: [6, 7], auftraege: 7, umsatz: 10500, final: true }
];

function triggerNachbar() {
    if (nachbarWoche >= nachbarData.length) return;
    
    const data = nachbarData[nachbarWoche];
    
    // Animate van
    const vanPos = 5 + (nachbarWoche + 1) * 13;
    document.getElementById('nachbar-van').style.left = vanPos + '%';
    document.getElementById('nachbar-van').querySelector('div').classList.add('animate-van-bounce');
    setTimeout(() => {
        document.getElementById('nachbar-van').querySelector('div').classList.remove('animate-van-bounce');
    }, 1000);
    
    // Activate houses
    data.houses.forEach((houseNum, index) => {
        setTimeout(() => {
            const house = document.getElementById('house-' + houseNum);
            house.classList.add(nachbarWoche === 0 ? 'activated' : 'recommended');
            
            // Add speech bubble
            addSpeechBubble(houseNum);
        }, index * 300);
    });
    
    // Update stats
    document.getElementById('nachbar-woche').textContent = data.woche;
    document.getElementById('nachbar-auftraege').textContent = data.auftraege;
    document.getElementById('nachbar-umsatz').textContent = data.umsatz.toLocaleString('de-DE') + '€';
    document.getElementById('nachbar-km').textContent = '~' + Math.round(data.auftraege * 0.7) + 'km';
    
    document.getElementById('nachbar-text').innerHTML = data.text;
    
    nachbarWoche++;
    
    if (nachbarWoche < nachbarData.length) {
        document.getElementById('nachbar-btn').innerHTML = '<i class="fas fa-forward"></i> <span>Nächste Woche (Woche ' + nachbarData[nachbarWoche].woche + ')</span>';
    } else {
        document.getElementById('nachbar-btn').innerHTML = '<i class="fas fa-check"></i> <span>Straße komplett!</span>';
        document.getElementById('nachbar-btn').disabled = true;
        document.getElementById('nachbar-btn').classList.add('opacity-70');
        document.getElementById('nachbar-celebration').classList.remove('hidden');
        createConfetti();
    }
}

function addSpeechBubble(houseNum) {
    const container = document.getElementById('nachbar-effects');
    const bubble = document.createElement('div');
    bubble.className = 'speech-bubble absolute bg-white dark:bg-slate-700 rounded-lg px-2 py-1 text-xs shadow-md';
    bubble.style.left = (houseNum * 14 - 5) + '%';
    bubble.style.top = '-10px';
    bubble.textContent = ['👍', '⭐', '🔧', '💪', '👏', '🎉'][Math.floor(Math.random() * 6)];
    container.appendChild(bubble);
    setTimeout(() => bubble.remove(), 2000);
}

function resetNachbar() {
    nachbarWoche = 0;
    
    // Reset houses
    for (let i = 2; i <= 7; i++) {
        const house = document.getElementById('house-' + i);
        house.classList.remove('activated', 'recommended');
    }
    
    // Reset van
    document.getElementById('nachbar-van').style.left = '5%';
    
    // Reset stats
    document.getElementById('nachbar-woche').textContent = '0';
    document.getElementById('nachbar-auftraege').textContent = '0';
    document.getElementById('nachbar-umsatz').textContent = '0€';
    document.getElementById('nachbar-km').textContent = '0km';
    
    document.getElementById('nachbar-text').innerHTML = '🏠 Sie haben gerade einen Auftrag in der Musterstraße 1 erledigt. Klicken Sie, um zu sehen, was als nächstes passiert!';
    
    document.getElementById('nachbar-btn').innerHTML = '<i class="fas fa-home"></i> <span>Nächste Woche</span>';
    document.getElementById('nachbar-btn').disabled = false;
    document.getElementById('nachbar-btn').classList.remove('opacity-70');
    
    document.getElementById('nachbar-celebration').classList.add('hidden');
    document.getElementById('nachbar-effects').innerHTML = '';
}

// ==================== HELPER ====================
function createConfetti() {
    const colors = ['#f59e0b', '#ea580c', '#fbbf24', '#22c55e', '#8b5cf6'];
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
    updateKosten();
});
</script>

<!-- Belohnungen Section -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            <div>
                <span class="text-amber-600 dark:text-amber-400 font-semibold uppercase tracking-wide text-sm">Belohnungssystem</span>
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mt-2 mb-6">
                    Beispiel-Belohnungen für Ihren Betrieb
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-lg mb-8">
                    Definieren Sie Belohnungsstufen, die zu Ihrem Handwerksbetrieb passen:
                </p>
                
                <div class="space-y-4">
                    <?php foreach ($belohnungen as $b): ?>
                    <div class="flex items-center gap-4 bg-gray-50 dark:bg-slate-800 rounded-xl p-4 shadow-sm">
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-500 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                            <?= $b['stufe'] ?>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400"><?= $b['stufe'] ?> Empfehlungen</div>
                            <div class="font-semibold text-gray-900 dark:text-white"><?= $b['belohnung'] ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="mt-8 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-700/30">
                    <h4 class="font-semibold text-amber-900 dark:text-amber-300 mb-2">
                        <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                        Beliebte Belohnungen bei Handwerkern:
                    </h4>
                    <ul class="text-sm text-amber-800 dark:text-amber-200 space-y-1">
                        <li>• Rabatte auf den nächsten Auftrag</li>
                        <li>• Gratis Anfahrt für einen Zeitraum</li>
                        <li>• Wertgutscheine für Dienstleistungen</li>
                        <li>• Kostenloser Wartungsservice</li>
                        <li>• Prioritäts-Termine bei Notfällen</li>
                    </ul>
                </div>
            </div>
            
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 md:p-8 shadow-lg border border-gray-200 dark:border-slate-600">
                <div class="text-center mb-6">
                    <div class="text-5xl mb-3">🎉</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Belohnung freigeschaltet!</h3>
                    <p class="text-gray-500 dark:text-gray-400">Sie haben Stufe 2 erreicht</p>
                </div>
                
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/10 rounded-xl p-5 border border-amber-200 dark:border-amber-700/30 mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-amber-400 rounded-full flex items-center justify-center text-2xl">
                            🚗
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 dark:text-white text-lg">Gratis Anfahrt</div>
                            <div class="text-gray-600 dark:text-gray-300">Für 1 Jahr bei allen Aufträgen</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 dark:bg-slate-800 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Ihr Fortschritt</span>
                        <span class="text-sm font-semibold text-amber-600 dark:text-amber-400">5/10 zur nächsten Stufe</span>
                    </div>
                    <div class="h-3 bg-gray-200 dark:bg-slate-600 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-amber-500 to-orange-500 rounded-full" style="width: 50%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonial Section -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-slate-700 dark:to-slate-600 rounded-2xl p-8 md:p-12 text-center">
            <div class="flex justify-center gap-1 text-yellow-400 mb-6">
                <?php for ($i = 0; $i < 5; $i++): ?>
                <i class="fas fa-star text-xl"></i>
                <?php endfor; ?>
            </div>
            
            <blockquote class="text-xl md:text-2xl font-medium text-gray-900 dark:text-white mb-8 leading-relaxed">
                "<?= $testimonial['text'] ?>"
            </blockquote>
            
            <div class="flex items-center justify-center gap-4">
                <div class="w-14 h-14 bg-amber-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
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

<!-- QR-Code Feature -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-amber-600 to-orange-600 rounded-2xl p-8 md:p-12 text-white">
            <div class="grid md:grid-cols-2 gap-8 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full mb-6">
                        <i class="fas fa-qrcode"></i>
                        <span class="text-sm font-medium">Offline-Empfehlungen</span>
                    </div>
                    <h3 class="text-2xl md:text-3xl font-bold mb-4">
                        QR-Code auf Rechnung & Visitenkarte
                    </h3>
                    <p class="text-white/90 text-lg mb-6">
                        Perfekt für Handwerker: Drucken Sie Ihren QR-Code auf:
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-green-300"></i>
                            <span>Rechnungen & Angebote</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-green-300"></i>
                            <span>Visitenkarten</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-green-300"></i>
                            <span>Fahrzeugbeschriftung</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-green-300"></i>
                            <span>Flyer & Werbematerial</span>
                        </li>
                    </ul>
                </div>
                <div class="text-center">
                    <div class="bg-white rounded-2xl p-8 inline-block shadow-xl">
                        <div class="w-40 h-40 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-qrcode text-gray-400 text-6xl"></i>
                        </div>
                        <p class="text-gray-600 font-medium">Ihr persönlicher QR-Code</p>
                        <p class="text-gray-400 text-sm">Automatisch generiert</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-12 md:py-20 bg-gradient-to-r from-amber-600 to-orange-700 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold mb-4 md:mb-6">
            Bereit für mehr Aufträge durch Empfehlungen?
        </h2>
        <p class="text-lg md:text-xl text-white/90 mb-6 md:mb-8">
            Starten Sie noch heute und machen Sie Ihre zufriedenen Kunden zu Ihren besten Werbern.
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
