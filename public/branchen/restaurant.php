<?php
/**
 * Branchenseite: Restaurants & Gastronomie
 */

$pageTitle = 'Empfehlungsprogramm für Restaurants';
$metaDescription = 'Automatisches Empfehlungsprogramm für Restaurants und Gastronomie. Gäste empfehlen Gäste und erhalten Belohnungen wie Gratis-Desserts oder Dinner-Gutscheine.';
$currentPage = 'branchen';

require_once __DIR__ . '/../../templates/marketing/header.php';

// Branchenspezifische Daten
$branche = [
    'name' => 'Restaurants',
    'slug' => 'restaurant',
    'icon' => 'fa-utensils',
    'color' => 'amber',
    'heroTitle' => 'Mehr Gäste durch Empfehlungen',
    'heroSubtitle' => 'Gutes Essen wird weitererzählt. Machen Sie aus begeisterten Gästen Ihre besten Botschafter – automatisch und ohne Aufwand.',
];

$vorteile = [
    [
        'icon' => 'fa-users',
        'title' => 'Qualifizierte Neugäste',
        'text' => 'Wer durch Freunde empfohlen wird, passt zu Ihrem Restaurant und kommt mit positiver Erwartung.'
    ],
    [
        'icon' => 'fa-star',
        'title' => 'Bessere Reviews',
        'text' => 'Empfohlene Gäste hinterlassen 40% häufiger positive Bewertungen auf Google & TripAdvisor.'
    ],
    [
        'icon' => 'fa-euro-sign',
        'title' => 'Höherer Umsatz',
        'text' => 'Empfohlene Gäste bestellen mehr und kommen häufiger wieder – nachweislich.'
    ],
    [
        'icon' => 'fa-heart',
        'title' => 'Stammgäste gewinnen',
        'text' => 'Aus Neugästen werden Stammgäste – besonders wenn sie durch Freunde kamen.'
    ],
];

$belohnungen = [
    ['stufe' => 1, 'belohnung' => 'Gratis Dessert beim nächsten Besuch'],
    ['stufe' => 3, 'belohnung' => '10% Rabatt auf die gesamte Rechnung'],
    ['stufe' => 5, 'belohnung' => 'Dinner für 2 Personen gratis'],
];

$testimonial = [
    'text' => 'Unsere Gäste lieben es! Sie teilen ihren Empfehlungslink direkt nach dem Essen – wenn die Begeisterung am größten ist. Wir haben 23% mehr Reservierungen seit dem Start.',
    'name' => 'Marco Rossi',
    'rolle' => 'Ristorante Bella Vista, Köln',
    'initialen' => 'MR',
];

$stats = [
    'empfehler' => '289',
    'conversions' => '112',
    'rate' => '39%',
];
?>

<!-- Hero Section -->
<section class="relative py-16 md:py-24 overflow-hidden">
    <!-- Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-amber-500 to-orange-600"></div>
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
                                <i class="fas fa-utensils text-amber-600 text-xl"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">Ristorante Bella Vista</div>
                                <div class="text-sm text-gray-500">empfohlen.de/bella-vista</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-amber-600"><?= $stats['empfehler'] ?></div>
                                <div class="text-xs text-gray-500">Empfehler</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600"><?= $stats['conversions'] ?></div>
                                <div class="text-xs text-gray-500">Neugäste</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600"><?= $stats['rate'] ?></div>
                                <div class="text-xs text-gray-500">Conversion</div>
                            </div>
                        </div>
                        <div class="text-center text-sm text-gray-500">
                            <i class="fas fa-chart-line text-green-500 mr-1"></i>
                            +18% mehr Reservierungen diesen Monat
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
                Die beste Werbung ist ein zufriedener Gast, der seinen Freunden von Ihnen erzählt.
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

<!-- Belohnungen Section -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            <div>
                <span class="text-amber-600 dark:text-amber-400 font-semibold uppercase tracking-wide text-sm">Belohnungssystem</span>
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mt-2 mb-6">
                    Beispiel-Belohnungen für Ihr Restaurant
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-lg mb-8">
                    Belohnen Sie Ihre Gäste mit dem, was sie lieben – gutem Essen:
                </p>
                
                <div class="space-y-4">
                    <?php foreach ($belohnungen as $b): ?>
                    <div class="flex items-center gap-4 bg-white dark:bg-slate-700 rounded-xl p-4 shadow-sm">
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-500 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
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
                    Tipp: Gratis-Desserts haben niedrige Kosten, aber hohe emotionale Wirkung.
                </p>
            </div>
            
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 md:p-8 shadow-lg">
                <div class="text-center mb-6">
                    <div class="text-5xl mb-3">🍰</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Belohnung freigeschaltet!</h3>
                    <p class="text-gray-500 dark:text-gray-400">Sie haben Stufe 1 erreicht</p>
                </div>
                
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/10 rounded-xl p-5 border border-amber-200 dark:border-amber-700/30 mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-amber-400 rounded-full flex items-center justify-center text-2xl">
                            🎂
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 dark:text-white text-lg">Gratis Dessert</div>
                            <div class="text-gray-600 dark:text-gray-300">Beim nächsten Besuch</div>
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

<!-- ============================================== -->
<!-- INTERAKTIVE ANIMATIONEN SECTION               -->
<!-- ============================================== -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-500 to-orange-600 text-white px-5 py-2 rounded-full text-sm font-bold shadow-lg mb-4">
                <span>🍽️</span> Live erleben
            </span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                So funktioniert Empfehlungsmarketing in der Gastro
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg max-w-2xl mx-auto">
                Drei interaktive Demos zeigen, wie Ihre Gäste begeistert Freunde einladen.
            </p>
        </div>
        
        <!-- Tab Navigation -->
        <div class="flex flex-wrap justify-center gap-3 mb-8" id="restaurant-animation-tabs">
            <button onclick="showRestaurantAnimation('foodstory')" id="tab-foodstory" class="restaurant-tab active px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-lg">
                📸 Food-Foto Story
            </button>
            <button onclick="showRestaurantAnimation('chain')" id="tab-chain" class="restaurant-tab px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-white dark:bg-slate-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-600 hover:shadow-md">
                🍽️ Reservierungs-Kette
            </button>
            <button onclick="showRestaurantAnimation('wheel')" id="tab-wheel" class="restaurant-tab px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-white dark:bg-slate-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-600 hover:shadow-md">
                🎰 Belohnungs-Rad
            </button>
        </div>
        
        <!-- Animation Containers -->
        <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-6 md:p-8 min-h-[650px] shadow-lg">
            
            <!-- ========================================= -->
            <!-- ANIMATION 1: FOOD-FOTO INSTAGRAM STORY   -->
            <!-- ========================================= -->
            <div id="animation-foodstory" class="restaurant-animation-content">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">So teilen Ihre Gäste auf Instagram</h3>
                    <p class="text-gray-500 dark:text-gray-400">Ein Food-Foto – und schon kommen neue Reservierungen rein</p>
                </div>
                
                <div class="max-w-sm mx-auto">
                    <!-- Phone Frame -->
                    <div class="bg-gradient-to-br from-purple-900 via-pink-900 to-orange-900 rounded-[2.5rem] p-3 shadow-2xl">
                        <div class="w-24 h-6 bg-black rounded-full mx-auto mb-2"></div>
                        <div class="bg-black rounded-[2rem] overflow-hidden h-[520px] flex flex-col relative">
                            
                            <!-- Instagram Header -->
                            <div class="absolute top-0 left-0 right-0 z-10 p-4 bg-gradient-to-b from-black/60 to-transparent">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-500 to-orange-600 p-0.5">
                                        <div class="w-full h-full rounded-full bg-black flex items-center justify-center text-white text-sm font-bold">S</div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-white text-sm font-semibold">sarah_foodie</div>
                                        <div class="text-white/60 text-xs">Köln • Gerade eben</div>
                                    </div>
                                    <span class="text-white/80">•••</span>
                                </div>
                                <!-- Progress bars -->
                                <div class="flex gap-1 mt-3">
                                    <div class="flex-1 h-0.5 bg-white/30 rounded-full overflow-hidden">
                                        <div id="food-story-progress" class="h-full bg-white rounded-full" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Story Content - Food Image -->
                            <div class="flex-1 bg-gradient-to-br from-amber-600 via-orange-500 to-red-500 flex items-center justify-center relative overflow-hidden">
                                <!-- Food plate -->
                                <div id="food-plate" class="text-center opacity-0 transform scale-90 transition-all duration-500">
                                    <div class="text-8xl mb-2 animate-float-slow">🍝</div>
                                    <div id="food-steam" class="absolute top-1/4 left-1/2 transform -translate-x-1/2 opacity-0">
                                        <div class="text-4xl animate-steam">♨️</div>
                                    </div>
                                </div>
                                
                                <!-- Floating food emojis -->
                                <div id="food-emojis" class="absolute inset-0 pointer-events-none"></div>
                            </div>
                            
                            <!-- Story Footer -->
                            <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/80 to-transparent">
                                <!-- Caption -->
                                <div id="food-caption" class="opacity-0 transform translate-y-4 transition-all duration-500 mb-3">
                                    <p class="text-white text-sm font-medium">Bestes Pasta ever! 😍🇮🇹✨</p>
                                </div>
                                
                                <!-- Location Tag -->
                                <div id="food-location" class="opacity-0 transform translate-y-4 transition-all duration-500 mb-3">
                                    <span class="inline-flex items-center gap-1 bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-white text-xs">
                                        <i class="fas fa-map-marker-alt"></i> Ristorante Bella Vista
                                    </span>
                                </div>
                                
                                <!-- Referral Link Sticker -->
                                <div id="food-link" class="opacity-0 transform translate-y-4 transition-all duration-500">
                                    <div class="bg-white rounded-xl p-3 flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center text-white">
                                            <i class="fas fa-gift"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-gray-900 font-semibold text-sm">10% Rabatt für dich!</div>
                                            <div class="text-gray-500 text-xs">Tippe für deinen Gutschein</div>
                                        </div>
                                        <i class="fas fa-chevron-up text-gray-400"></i>
                                    </div>
                                </div>
                                
                                <!-- Reply Bar -->
                                <div class="flex items-center gap-3 mt-3">
                                    <div class="flex-1 bg-white/10 rounded-full px-4 py-2 text-white/50 text-sm">
                                        Nachricht senden...
                                    </div>
                                    <i class="far fa-heart text-white text-xl"></i>
                                    <i class="far fa-paper-plane text-white text-xl"></i>
                                </div>
                            </div>
                            
                            <!-- Reaction bubbles -->
                            <div id="food-reactions" class="absolute right-4 top-1/2 transform -translate-y-1/2 space-y-2"></div>
                        </div>
                    </div>
                    
                    <!-- DM Previews -->
                    <div id="food-dms" class="mt-4 space-y-2 opacity-0 transform translate-y-4 transition-all duration-500">
                        <div class="bg-white dark:bg-slate-700 rounded-xl p-3 flex items-center gap-3 shadow-md animate-slide-in">
                            <div class="w-10 h-10 bg-pink-500 rounded-full flex items-center justify-center text-white font-bold">L</div>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900 dark:text-white text-sm">Lisa</div>
                                <div class="text-gray-500 dark:text-gray-400 text-xs">Omg wo ist das?! Will auch hin! 😍</div>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-slate-700 rounded-xl p-3 flex items-center gap-3 shadow-md animate-slide-in" style="animation-delay: 0.2s">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">M</div>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900 dark:text-white text-sm">Max</div>
                                <div class="text-gray-500 dark:text-gray-400 text-xs">Link bitte!! 🙏</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Results Counter -->
                    <div id="food-results" class="mt-4 opacity-0 transform translate-y-4 transition-all duration-500">
                        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl p-4 text-white text-center">
                            <div class="text-2xl font-bold mb-1"><span id="food-counter">0</span> neue Reservierungen</div>
                            <div class="text-white/80 text-sm">durch diese Story</div>
                        </div>
                    </div>
                    
                    <!-- Replay Button -->
                    <button onclick="restartFoodStory()" id="food-replay" class="hidden mt-4 mx-auto block px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-full font-semibold text-sm hover:shadow-lg transition-all">
                        ↻ Animation wiederholen
                    </button>
                </div>
            </div>
            
            <!-- ========================================= -->
            <!-- ANIMATION 2: RESERVIERUNGS-KETTENREAKTION -->
            <!-- ========================================= -->
            <div id="animation-chain" class="restaurant-animation-content hidden">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Die Empfehlungs-Kettenreaktion</h3>
                    <p class="text-gray-500 dark:text-gray-400">Sehen Sie, wie aus einer Empfehlung viele Reservierungen werden!</p>
                </div>
                
                <div class="max-w-2xl mx-auto">
                    <!-- Restaurant Floor Plan -->
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-slate-800 dark:to-slate-700 rounded-2xl p-6 shadow-lg">
                        
                        <!-- Stats Bar -->
                        <div class="flex justify-between items-center mb-6 p-4 bg-white dark:bg-slate-600 rounded-xl">
                            <div class="text-center">
                                <div id="chain-tables" class="text-3xl font-black text-amber-600 dark:text-amber-400">0</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Tische gebucht</div>
                            </div>
                            <div class="text-center">
                                <div id="chain-guests" class="text-3xl font-black text-green-600 dark:text-green-400">0</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Gäste</div>
                            </div>
                            <div class="text-center">
                                <div id="chain-revenue" class="text-3xl font-black text-orange-600 dark:text-orange-400">0€</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Umsatz</div>
                            </div>
                        </div>
                        
                        <!-- Restaurant Grid -->
                        <div class="grid grid-cols-4 gap-3 mb-6" id="restaurant-grid">
                            <!-- Row 1 -->
                            <div id="table-1" class="table-spot aspect-square bg-white dark:bg-slate-600 rounded-xl flex flex-col items-center justify-center transition-all duration-500 cursor-pointer hover:scale-105 border-2 border-transparent">
                                <div class="text-2xl mb-1">🪑</div>
                                <div class="text-xs text-gray-400">Tisch 1</div>
                            </div>
                            <div id="table-2" class="table-spot aspect-square bg-white dark:bg-slate-600 rounded-xl flex flex-col items-center justify-center transition-all duration-500 cursor-pointer hover:scale-105 border-2 border-transparent">
                                <div class="text-2xl mb-1">🪑</div>
                                <div class="text-xs text-gray-400">Tisch 2</div>
                            </div>
                            <div id="table-3" class="table-spot aspect-square bg-white dark:bg-slate-600 rounded-xl flex flex-col items-center justify-center transition-all duration-500 cursor-pointer hover:scale-105 border-2 border-transparent">
                                <div class="text-2xl mb-1">🪑</div>
                                <div class="text-xs text-gray-400">Tisch 3</div>
                            </div>
                            <div id="table-4" class="table-spot aspect-square bg-white dark:bg-slate-600 rounded-xl flex flex-col items-center justify-center transition-all duration-500 cursor-pointer hover:scale-105 border-2 border-transparent">
                                <div class="text-2xl mb-1">🪑</div>
                                <div class="text-xs text-gray-400">Tisch 4</div>
                            </div>
                            <!-- Row 2 -->
                            <div id="table-5" class="table-spot aspect-square bg-white dark:bg-slate-600 rounded-xl flex flex-col items-center justify-center transition-all duration-500 cursor-pointer hover:scale-105 border-2 border-transparent">
                                <div class="text-2xl mb-1">🪑</div>
                                <div class="text-xs text-gray-400">Tisch 5</div>
                            </div>
                            <div id="table-6" class="table-spot aspect-square bg-white dark:bg-slate-600 rounded-xl flex flex-col items-center justify-center transition-all duration-500 cursor-pointer hover:scale-105 border-2 border-transparent">
                                <div class="text-2xl mb-1">🪑</div>
                                <div class="text-xs text-gray-400">Tisch 6</div>
                            </div>
                            <div id="table-7" class="table-spot aspect-square bg-white dark:bg-slate-600 rounded-xl flex flex-col items-center justify-center transition-all duration-500 cursor-pointer hover:scale-105 border-2 border-transparent">
                                <div class="text-2xl mb-1">🪑</div>
                                <div class="text-xs text-gray-400">Tisch 7</div>
                            </div>
                            <div id="table-8" class="table-spot aspect-square bg-white dark:bg-slate-600 rounded-xl flex flex-col items-center justify-center transition-all duration-500 cursor-pointer hover:scale-105 border-2 border-transparent">
                                <div class="text-2xl mb-1">🪑</div>
                                <div class="text-xs text-gray-400">Tisch 8</div>
                            </div>
                        </div>
                        
                        <!-- Chain Visualization -->
                        <div id="chain-info" class="text-center mb-4 p-3 bg-amber-100 dark:bg-amber-900/30 rounded-xl opacity-0 transition-all duration-500">
                            <div class="text-sm text-amber-800 dark:text-amber-300">
                                <span id="chain-text">Klicken Sie, um die Kette zu starten!</span>
                            </div>
                        </div>
                        
                        <!-- Action Button -->
                        <button onclick="triggerChainReaction()" id="chain-btn" class="w-full py-4 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl font-bold text-lg hover:shadow-lg hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-share-alt"></i>
                            <span>Erste Empfehlung auslösen</span>
                        </button>
                        
                        <!-- Reset -->
                        <button onclick="resetChainReaction()" class="w-full mt-3 py-2 text-gray-500 dark:text-gray-400 text-sm hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                            ↻ Demo zurücksetzen
                        </button>
                    </div>
                    
                    <!-- Full House Celebration -->
                    <div id="chain-celebration" class="hidden mt-6">
                        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl p-6 text-white text-center animate-bounce-in">
                            <div class="text-4xl mb-2">🎉🍽️🎉</div>
                            <div class="font-black text-xl">RESTAURANT AUSGEBUCHT!</div>
                            <div class="text-white/90">Alles durch 1 Empfehlung gestartet</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ========================================= -->
            <!-- ANIMATION 3: BELOHNUNGS-GLÜCKSRAD        -->
            <!-- ========================================= -->
            <div id="animation-wheel" class="restaurant-animation-content hidden">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Das Belohnungs-Glücksrad</h3>
                    <p class="text-gray-500 dark:text-gray-400">Drehen Sie und gewinnen Sie Ihre Belohnung!</p>
                </div>
                
                <div class="max-w-md mx-auto">
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-slate-800 dark:to-slate-700 rounded-2xl p-6 shadow-lg">
                        
                        <!-- Wheel Container -->
                        <div class="relative mb-6">
                            <!-- Pointer -->
                            <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-2 z-10">
                                <div class="w-0 h-0 border-l-[15px] border-r-[15px] border-t-[25px] border-l-transparent border-r-transparent border-t-amber-600"></div>
                            </div>
                            
                            <!-- Wheel -->
                            <div class="relative w-64 h-64 mx-auto">
                                <svg id="reward-wheel" class="w-full h-full transition-transform duration-[4000ms] ease-out" viewBox="0 0 200 200">
                                    <!-- Wheel segments -->
                                    <g transform="translate(100,100)">
                                        <!-- Segment 1: Dessert -->
                                        <path d="M0,0 L0,-95 A95,95 0 0,1 82.3,-47.5 Z" fill="#f59e0b"/>
                                        <text transform="rotate(30) translate(0,-70)" text-anchor="middle" fill="white" font-size="10" font-weight="bold">🍰</text>
                                        
                                        <!-- Segment 2: Wine -->
                                        <path d="M0,0 L82.3,-47.5 A95,95 0 0,1 82.3,47.5 Z" fill="#ea580c"/>
                                        <text transform="rotate(90) translate(0,-70)" text-anchor="middle" fill="white" font-size="10" font-weight="bold">🍷</text>
                                        
                                        <!-- Segment 3: 10% -->
                                        <path d="M0,0 L82.3,47.5 A95,95 0 0,1 0,95 Z" fill="#f97316"/>
                                        <text transform="rotate(150) translate(0,-70)" text-anchor="middle" fill="white" font-size="8" font-weight="bold">10%</text>
                                        
                                        <!-- Segment 4: Appetizer -->
                                        <path d="M0,0 L0,95 A95,95 0 0,1 -82.3,47.5 Z" fill="#fb923c"/>
                                        <text transform="rotate(210) translate(0,-70)" text-anchor="middle" fill="white" font-size="10" font-weight="bold">🥗</text>
                                        
                                        <!-- Segment 5: VIP -->
                                        <path d="M0,0 L-82.3,47.5 A95,95 0 0,1 -82.3,-47.5 Z" fill="#d97706"/>
                                        <text transform="rotate(270) translate(0,-70)" text-anchor="middle" fill="white" font-size="10" font-weight="bold">⭐</text>
                                        
                                        <!-- Segment 6: Surprise -->
                                        <path d="M0,0 L-82.3,-47.5 A95,95 0 0,1 0,-95 Z" fill="#b45309"/>
                                        <text transform="rotate(330) translate(0,-70)" text-anchor="middle" fill="white" font-size="10" font-weight="bold">🎁</text>
                                        
                                        <!-- Center circle -->
                                        <circle r="25" fill="#78350f"/>
                                        <text text-anchor="middle" dy="5" fill="white" font-size="12" font-weight="bold">DREH!</text>
                                    </g>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Reward Legend -->
                        <div class="grid grid-cols-3 gap-2 mb-6 text-center">
                            <div class="bg-white dark:bg-slate-600 rounded-lg p-2">
                                <div class="text-lg">🍰</div>
                                <div class="text-[10px] text-gray-600 dark:text-gray-300">Dessert</div>
                            </div>
                            <div class="bg-white dark:bg-slate-600 rounded-lg p-2">
                                <div class="text-lg">🍷</div>
                                <div class="text-[10px] text-gray-600 dark:text-gray-300">Wein</div>
                            </div>
                            <div class="bg-white dark:bg-slate-600 rounded-lg p-2">
                                <div class="text-lg">10%</div>
                                <div class="text-[10px] text-gray-600 dark:text-gray-300">Rabatt</div>
                            </div>
                            <div class="bg-white dark:bg-slate-600 rounded-lg p-2">
                                <div class="text-lg">🥗</div>
                                <div class="text-[10px] text-gray-600 dark:text-gray-300">Vorspeise</div>
                            </div>
                            <div class="bg-white dark:bg-slate-600 rounded-lg p-2">
                                <div class="text-lg">⭐</div>
                                <div class="text-[10px] text-gray-600 dark:text-gray-300">VIP-Tisch</div>
                            </div>
                            <div class="bg-white dark:bg-slate-600 rounded-lg p-2">
                                <div class="text-lg">🎁</div>
                                <div class="text-[10px] text-gray-600 dark:text-gray-300">Überraschung</div>
                            </div>
                        </div>
                        
                        <!-- Spin Count -->
                        <div class="text-center mb-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Ihre Empfehlungen: <span id="wheel-referrals" class="font-bold text-amber-600">3</span></div>
                            <div class="flex justify-center gap-1 mt-2">
                                <span class="text-xl">⭐</span>
                                <span class="text-xl">⭐</span>
                                <span class="text-xl">⭐</span>
                                <span class="text-xl text-gray-300">☆</span>
                                <span class="text-xl text-gray-300">☆</span>
                            </div>
                        </div>
                        
                        <!-- Spin Button -->
                        <button onclick="spinWheel()" id="spin-btn" class="w-full py-4 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl font-bold text-lg hover:shadow-lg hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-sync-alt"></i>
                            <span>Am Rad drehen!</span>
                        </button>
                        
                        <!-- Reset -->
                        <button onclick="resetWheel()" class="w-full mt-3 py-2 text-gray-500 dark:text-gray-400 text-sm hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                            ↻ Nochmal spielen
                        </button>
                    </div>
                    
                    <!-- Win Modal -->
                    <div id="wheel-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
                        <div class="absolute inset-0 bg-black/50" onclick="closeWheelModal()"></div>
                        <div class="relative bg-white dark:bg-slate-700 rounded-2xl p-8 shadow-2xl text-center max-w-sm mx-4 animate-bounce-in">
                            <div id="wheel-modal-icon" class="text-6xl mb-4">🎉</div>
                            <div class="text-2xl font-black text-gray-800 dark:text-white mb-2">GEWONNEN!</div>
                            <div id="wheel-modal-prize" class="text-xl text-amber-600 dark:text-amber-400 font-bold mb-4">Gratis Dessert</div>
                            <div class="bg-gray-100 dark:bg-slate-600 rounded-lg p-3 mb-4">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Dein Gutschein-Code:</div>
                                <div id="wheel-modal-code" class="text-lg font-mono font-bold text-gray-900 dark:text-white">DESSERT-2024-XYZ</div>
                            </div>
                            <button onclick="closeWheelModal()" class="w-full px-8 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl font-semibold hover:shadow-lg transition-all">
                                Super! 🎊
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
    /* Keyframes */
    @keyframes bounceIn {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes float-slow {
        0%, 100% { transform: translateY(0) rotate(-5deg); }
        50% { transform: translateY(-10px) rotate(5deg); }
    }
    @keyframes steam {
        0%, 100% { opacity: 0.3; transform: translateY(0) scale(1); }
        50% { opacity: 0.8; transform: translateY(-15px) scale(1.2); }
    }
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 5px rgba(245, 158, 11, 0.5); }
        50% { box-shadow: 0 0 20px rgba(245, 158, 11, 0.8); }
    }
    @keyframes confetti-fall {
        0% { transform: translateY(-100%) rotate(0deg); opacity: 1; }
        100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
    }
    @keyframes reaction-float {
        0% { opacity: 0; transform: translateY(20px) scale(0.5); }
        20% { opacity: 1; transform: translateY(0) scale(1); }
        80% { opacity: 1; transform: translateY(-30px) scale(1); }
        100% { opacity: 0; transform: translateY(-50px) scale(0.8); }
    }
    
    /* Animation Classes */
    .animate-bounce-in { animation: bounceIn 0.5s ease forwards; }
    .animate-slide-up { animation: slideUp 0.4s ease forwards; }
    .animate-slide-in { animation: slideIn 0.4s ease forwards; }
    .animate-float-slow { animation: float-slow 3s ease-in-out infinite; }
    .animate-steam { animation: steam 2s ease-in-out infinite; }
    .animate-pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }
    .animate-reaction { animation: reaction-float 2s ease forwards; }
    
    /* Tab Styling */
    .restaurant-tab.active {
        background: linear-gradient(to right, #f59e0b, #ea580c);
        color: white;
        box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.3);
        border: none;
    }
    
    /* Table styling */
    .table-spot.occupied {
        background: linear-gradient(to br, #fef3c7, #fde68a) !important;
        border-color: #f59e0b !important;
        animation: pulse-glow 2s ease-in-out infinite;
    }
    .table-spot.occupied .text-2xl {
        display: none;
    }
    .table-spot.occupied::before {
        content: '👨‍👩‍👧';
        font-size: 1.5rem;
    }
    .dark .table-spot.occupied {
        background: linear-gradient(to br, #78350f, #92400e) !important;
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
</style>

<!-- Animation JavaScript -->
<script>
// ==================== TAB SWITCHING ====================
function showRestaurantAnimation(type) {
    // Hide all animation contents
    document.querySelectorAll('.restaurant-animation-content').forEach(el => el.classList.add('hidden'));
    document.getElementById('animation-' + type).classList.remove('hidden');
    
    // Update tab styles
    document.querySelectorAll('.restaurant-tab').forEach(tab => {
        tab.classList.remove('active');
        tab.classList.add('bg-white', 'dark:bg-slate-700', 'text-gray-600', 'dark:text-gray-300', 'border', 'border-gray-200', 'dark:border-slate-600');
    });
    const activeTab = document.getElementById('tab-' + type);
    activeTab.classList.add('active');
    activeTab.classList.remove('bg-white', 'dark:bg-slate-700', 'text-gray-600', 'dark:text-gray-300', 'border', 'border-gray-200', 'dark:border-slate-600');
    
    // Start animations
    if (type === 'foodstory') restartFoodStory();
    if (type === 'chain') resetChainReaction();
    if (type === 'wheel') resetWheel();
}

// ==================== ANIMATION 1: FOOD-FOTO STORY ====================
let foodTimeout = null;

function restartFoodStory() {
    clearTimeout(foodTimeout);
    
    // Reset all elements
    document.getElementById('food-story-progress').style.width = '0%';
    document.getElementById('food-plate').classList.add('opacity-0', 'scale-90');
    document.getElementById('food-plate').classList.remove('opacity-100', 'scale-100');
    document.getElementById('food-steam').classList.add('opacity-0');
    document.getElementById('food-steam').classList.remove('opacity-100');
    document.getElementById('food-caption').classList.add('opacity-0', 'translate-y-4');
    document.getElementById('food-location').classList.add('opacity-0', 'translate-y-4');
    document.getElementById('food-link').classList.add('opacity-0', 'translate-y-4');
    document.getElementById('food-dms').classList.add('opacity-0', 'translate-y-4');
    document.getElementById('food-results').classList.add('opacity-0', 'translate-y-4');
    document.getElementById('food-emojis').innerHTML = '';
    document.getElementById('food-reactions').innerHTML = '';
    document.getElementById('food-counter').textContent = '0';
    document.getElementById('food-replay').classList.add('hidden');
    
    // Start animation sequence
    foodTimeout = setTimeout(() => {
        // Show food plate
        document.getElementById('food-plate').classList.remove('opacity-0', 'scale-90');
        document.getElementById('food-plate').classList.add('opacity-100', 'scale-100');
        
        // Show steam
        setTimeout(() => {
            document.getElementById('food-steam').classList.remove('opacity-0');
            document.getElementById('food-steam').classList.add('opacity-100');
        }, 300);
        
        // Animate progress bar
        document.getElementById('food-story-progress').style.transition = 'width 10s linear';
        document.getElementById('food-story-progress').style.width = '100%';
        
        // Add floating food emojis
        addFoodEmojis();
        
        foodTimeout = setTimeout(() => {
            // Show caption
            document.getElementById('food-caption').classList.remove('opacity-0', 'translate-y-4');
            
            foodTimeout = setTimeout(() => {
                // Show location
                document.getElementById('food-location').classList.remove('opacity-0', 'translate-y-4');
                
                // Add reactions
                addFoodReactions();
                
                foodTimeout = setTimeout(() => {
                    // Show link
                    document.getElementById('food-link').classList.remove('opacity-0', 'translate-y-4');
                    
                    foodTimeout = setTimeout(() => {
                        // Show DMs
                        document.getElementById('food-dms').classList.remove('opacity-0', 'translate-y-4');
                        
                        foodTimeout = setTimeout(() => {
                            // Show results
                            document.getElementById('food-results').classList.remove('opacity-0', 'translate-y-4');
                            animateCounter('food-counter', 0, 3, 1000);
                            document.getElementById('food-replay').classList.remove('hidden');
                            createConfetti();
                        }, 1500);
                    }, 1200);
                }, 1000);
            }, 800);
        }, 600);
    }, 500);
}

function addFoodEmojis() {
    const container = document.getElementById('food-emojis');
    const emojis = ['🍝', '🍕', '🍷', '🥗', '🍰', '😍', '🔥', '✨'];
    
    for (let i = 0; i < 8; i++) {
        setTimeout(() => {
            const emoji = document.createElement('div');
            emoji.textContent = emojis[Math.floor(Math.random() * emojis.length)];
            emoji.className = 'absolute text-2xl';
            emoji.style.left = Math.random() * 80 + 10 + '%';
            emoji.style.top = Math.random() * 60 + 20 + '%';
            emoji.style.animation = `float-slow ${2 + Math.random() * 2}s ease-in-out infinite`;
            emoji.style.animationDelay = Math.random() * 2 + 's';
            container.appendChild(emoji);
            
            setTimeout(() => emoji.remove(), 6000);
        }, i * 400);
    }
}

function addFoodReactions() {
    const container = document.getElementById('food-reactions');
    const reactions = ['❤️', '🔥', '😍', '🤤', '👏'];
    
    for (let i = 0; i < 5; i++) {
        setTimeout(() => {
            const reaction = document.createElement('div');
            reaction.textContent = reactions[i];
            reaction.className = 'text-2xl animate-reaction';
            container.appendChild(reaction);
            
            setTimeout(() => reaction.remove(), 2000);
        }, i * 400);
    }
}

// ==================== ANIMATION 2: RESERVIERUNGS-KETTE ====================
let chainStep = 0;
const tableOrder = [1, 3, 2, 5, 7, 4, 8, 6]; // Order tables fill up
const guestEmojis = ['👨‍👩‍👧', '👫', '👨‍👨‍👦', '👩‍👩‍👧', '👨‍👧', '👩‍👦', '👫', '👨‍👩‍👧‍👦'];
let chainInterval = null;

function triggerChainReaction() {
    if (chainStep >= 8) return;
    
    // Show info
    document.getElementById('chain-info').classList.remove('opacity-0');
    
    if (chainStep === 0) {
        document.getElementById('chain-text').textContent = '🎯 Erste Empfehlung gesendet!';
        document.getElementById('chain-btn').innerHTML = '<i class="fas fa-share-alt"></i> <span>Nächste Empfehlung</span>';
    }
    
    // Fill next table
    const tableNum = tableOrder[chainStep];
    const table = document.getElementById('table-' + tableNum);
    table.classList.add('occupied');
    table.innerHTML = `<div class="text-2xl mb-1">${guestEmojis[chainStep]}</div><div class="text-xs text-amber-700 dark:text-amber-300 font-medium">Gebucht!</div>`;
    
    chainStep++;
    
    // Update stats
    document.getElementById('chain-tables').textContent = chainStep;
    document.getElementById('chain-guests').textContent = chainStep * 2 + Math.floor(Math.random() * 2);
    document.getElementById('chain-revenue').textContent = (chainStep * 85 + Math.floor(Math.random() * 50)) + '€';
    
    // Update chain text
    const texts = [
        '👨‍👩‍👧 Familie empfiehlt Freunde...',
        '👫 Pärchen lädt Kollegen ein...',
        '👨‍👨‍👦 Freunde erzählen es weiter...',
        '👩‍👩‍👧 Die Kette wächst!',
        '🔥 Viral! Noch mehr Empfehlungen...',
        '📱 Social Media tut sein Übriges...',
        '🎉 Fast ausgebucht!',
        '🏆 VOLLSTÄNDIG AUSGEBUCHT!'
    ];
    document.getElementById('chain-text').textContent = texts[chainStep - 1];
    
    // Check if all tables are full
    if (chainStep >= 8) {
        document.getElementById('chain-btn').innerHTML = '<i class="fas fa-check"></i> <span>Alle Tische gebucht!</span>';
        document.getElementById('chain-btn').disabled = true;
        document.getElementById('chain-btn').classList.add('opacity-70', 'cursor-not-allowed');
        document.getElementById('chain-celebration').classList.remove('hidden');
        createConfetti();
    }
}

function resetChainReaction() {
    chainStep = 0;
    clearInterval(chainInterval);
    
    // Reset all tables
    for (let i = 1; i <= 8; i++) {
        const table = document.getElementById('table-' + i);
        table.classList.remove('occupied');
        table.innerHTML = `<div class="text-2xl mb-1">🪑</div><div class="text-xs text-gray-400">Tisch ${i}</div>`;
    }
    
    // Reset stats
    document.getElementById('chain-tables').textContent = '0';
    document.getElementById('chain-guests').textContent = '0';
    document.getElementById('chain-revenue').textContent = '0€';
    
    // Reset UI
    document.getElementById('chain-info').classList.add('opacity-0');
    document.getElementById('chain-text').textContent = 'Klicken Sie, um die Kette zu starten!';
    document.getElementById('chain-celebration').classList.add('hidden');
    
    // Reset button
    document.getElementById('chain-btn').innerHTML = '<i class="fas fa-share-alt"></i> <span>Erste Empfehlung auslösen</span>';
    document.getElementById('chain-btn').disabled = false;
    document.getElementById('chain-btn').classList.remove('opacity-70', 'cursor-not-allowed');
}

// ==================== ANIMATION 3: BELOHNUNGS-GLÜCKSRAD ====================
let isSpinning = false;
let currentRotation = 0;

const prizes = [
    { name: 'Gratis Dessert', icon: '🍰', code: 'DESSERT' },
    { name: 'Glas Wein gratis', icon: '🍷', code: 'VINO' },
    { name: '10% Rabatt', icon: '💰', code: 'RABATT10' },
    { name: 'Gratis Vorspeise', icon: '🥗', code: 'STARTER' },
    { name: 'VIP-Tisch', icon: '⭐', code: 'VIP' },
    { name: 'Überraschung!', icon: '🎁', code: 'SURPRISE' }
];

function spinWheel() {
    if (isSpinning) return;
    isSpinning = true;
    
    // Disable button
    document.getElementById('spin-btn').disabled = true;
    document.getElementById('spin-btn').classList.add('opacity-70', 'cursor-not-allowed');
    document.getElementById('spin-btn').innerHTML = '<i class="fas fa-sync-alt animate-spin"></i> <span>Dreht...</span>';
    
    // Calculate random prize (weighted towards dessert for demo)
    const prizeIndex = Math.floor(Math.random() * prizes.length);
    const prize = prizes[prizeIndex];
    
    // Calculate rotation (each segment is 60 degrees)
    // We want to land in the middle of the segment
    const segmentAngle = 360 / 6;
    const targetAngle = segmentAngle * prizeIndex + segmentAngle / 2;
    
    // Add multiple full rotations + target
    const spins = 5 + Math.floor(Math.random() * 3); // 5-7 full spins
    const totalRotation = spins * 360 + (360 - targetAngle) + 15; // +15 to account for pointer position
    
    currentRotation += totalRotation;
    
    // Apply rotation
    const wheel = document.getElementById('reward-wheel');
    wheel.style.transform = `rotate(${currentRotation}deg)`;
    
    // Show result after spin
    setTimeout(() => {
        showWheelResult(prize);
        isSpinning = false;
    }, 4500);
}

function showWheelResult(prize) {
    const code = prize.code + '-' + Math.random().toString(36).substring(2, 8).toUpperCase();
    
    document.getElementById('wheel-modal-icon').textContent = prize.icon;
    document.getElementById('wheel-modal-prize').textContent = prize.name;
    document.getElementById('wheel-modal-code').textContent = code;
    document.getElementById('wheel-modal').classList.remove('hidden');
    
    createConfetti();
}

function closeWheelModal() {
    document.getElementById('wheel-modal').classList.add('hidden');
    
    // Reset button
    document.getElementById('spin-btn').disabled = false;
    document.getElementById('spin-btn').classList.remove('opacity-70', 'cursor-not-allowed');
    document.getElementById('spin-btn').innerHTML = '<i class="fas fa-sync-alt"></i> <span>Nochmal drehen!</span>';
}

function resetWheel() {
    closeWheelModal();
    currentRotation = 0;
    document.getElementById('reward-wheel').style.transition = 'none';
    document.getElementById('reward-wheel').style.transform = 'rotate(0deg)';
    
    // Re-enable transition after reset
    setTimeout(() => {
        document.getElementById('reward-wheel').style.transition = 'transform 4s cubic-bezier(0.17, 0.67, 0.12, 0.99)';
    }, 100);
    
    document.getElementById('spin-btn').innerHTML = '<i class="fas fa-sync-alt"></i> <span>Am Rad drehen!</span>';
}

// ==================== HELPER FUNCTIONS ====================
function animateCounter(elementId, start, end, duration) {
    const element = document.getElementById(elementId);
    const range = end - start;
    const stepTime = duration / range;
    let current = start;
    
    const timer = setInterval(() => {
        current++;
        element.textContent = current;
        
        if (current >= end) clearInterval(timer);
    }, stepTime);
}

function createConfetti() {
    const colors = ['#f59e0b', '#ea580c', '#22c55e', '#eab308', '#ef4444'];
    for (let i = 0; i < 50; i++) {
        const confetti = document.createElement('div');
        confetti.className = 'confetti';
        confetti.style.left = Math.random() * 100 + 'vw';
        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.animationDelay = Math.random() * 2 + 's';
        confetti.style.animationDuration = (2 + Math.random() * 2) + 's';
        document.body.appendChild(confetti);
        
        setTimeout(() => confetti.remove(), 5000);
    }
}

// ==================== INITIALIZE ====================
document.addEventListener('DOMContentLoaded', function() {
    // Start first animation
    restartFoodStory();
});
</script>

<!-- QR-Code Feature -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            <div class="order-2 lg:order-1">
                <div class="bg-white dark:bg-slate-700 rounded-2xl p-8 text-center">
                    <div class="bg-gray-50 dark:bg-slate-600 inline-block p-4 rounded-xl shadow-lg mb-4">
                        <div class="w-40 h-40 bg-white dark:bg-slate-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-qrcode text-6xl text-gray-400 dark:text-gray-300"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        Beispiel QR-Code für Ihren Tisch
                    </p>
                </div>
            </div>
            <div class="order-1 lg:order-2">
                <span class="text-amber-600 dark:text-amber-400 font-semibold uppercase tracking-wide text-sm">QR-Code Feature</span>
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mt-2 mb-6">
                    QR-Code auf jedem Tisch
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-lg mb-6">
                    Platzieren Sie den QR-Code auf Tischaufstellern, Speisekarten oder der Rechnung. 
                    Gäste scannen ihn direkt nach dem Essen – wenn die Begeisterung am größten ist.
                </p>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-check text-green-600 dark:text-green-400 text-xs"></i>
                        </div>
                        <span class="text-gray-700 dark:text-gray-300">Automatisch generierter QR-Code für Ihr Restaurant</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-check text-green-600 dark:text-green-400 text-xs"></i>
                        </div>
                        <span class="text-gray-700 dark:text-gray-300">Druckfertig in hoher Auflösung</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-check text-green-600 dark:text-green-400 text-xs"></i>
                        </div>
                        <span class="text-gray-700 dark:text-gray-300">Funktioniert mit jedem Smartphone</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Testimonial Section -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-slate-800 dark:to-slate-700 rounded-2xl p-8 md:p-12 text-center">
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

<!-- CTA Section -->
<section class="py-12 md:py-20 bg-gradient-to-r from-amber-500 to-orange-600 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold mb-4 md:mb-6">
            Bereit für mehr Gäste durch Empfehlungen?
        </h2>
        <p class="text-lg md:text-xl text-white/90 mb-6 md:mb-8">
            Starten Sie noch heute und machen Sie Ihre Gäste zu Ihren besten Botschaftern.
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
