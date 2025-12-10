<?php
/**
 * Branchenseite: Online-Shops (E-Commerce)
 */

$pageTitle = 'Empfehlungsprogramm für Online-Shops';
$metaDescription = 'Automatisches Empfehlungsprogramm für E-Commerce und Online-Shops. Kunden empfehlen Kunden und erhalten Rabatte, Gutscheine oder Gratis-Versand.';
$currentPage = 'branchen';

require_once __DIR__ . '/../../templates/marketing/header.php';

// Branchenspezifische Daten
$branche = [
    'name' => 'Online-Shops',
    'slug' => 'onlineshop',
    'icon' => 'fa-shopping-cart',
    'color' => 'green',
    'heroTitle' => 'Mehr Umsatz durch Kundenempfehlungen',
    'heroSubtitle' => 'Ihre zufriedenen Kunden sind Ihre beste Werbung. Verwandeln Sie jeden Kauf in eine Empfehlungs-Chance und senken Sie Ihre Customer Acquisition Costs drastisch.',
];

$vorteile = [
    [
        'icon' => 'fa-euro-sign',
        'title' => 'Niedrigerer CAC',
        'text' => 'Empfehlungskunden kosten 5-10x weniger als Facebook/Google Ads – bei höherer Qualität.'
    ],
    [
        'icon' => 'fa-chart-line',
        'title' => 'Höherer CLV',
        'text' => 'Empfohlene Kunden haben einen 25% höheren Customer Lifetime Value.'
    ],
    [
        'icon' => 'fa-redo',
        'title' => 'Bessere Retention',
        'text' => 'Kunden, die durch Freunde kommen, kaufen 2x häufiger wieder.'
    ],
    [
        'icon' => 'fa-rocket',
        'title' => 'Virales Wachstum',
        'text' => 'Jeder zufriedene Kunde kann neue Kunden bringen – exponentielles Wachstum möglich.'
    ],
];

$belohnungen = [
    ['stufe' => 1, 'belohnung' => '10% Rabatt auf nächste Bestellung'],
    ['stufe' => 3, 'belohnung' => 'Gratis Versand für 1 Jahr'],
    ['stufe' => 5, 'belohnung' => '50€ Shop-Gutschein'],
];

$testimonial = [
    'text' => 'Unsere CAC sind um 40% gesunken seit wir Leadbusiness nutzen. Das Beste: Die Kunden, die über Empfehlungen kommen, haben eine viel höhere Wiederkaufrate.',
    'name' => 'Lisa Schmidt',
    'rolle' => 'Gründerin, NaturKosmetik Shop',
    'initialen' => 'LS',
];

$stats = [
    'empfehler' => '1.247',
    'conversions' => '489',
    'rate' => '39%',
];
?>

<!-- Hero Section -->
<section class="relative py-16 md:py-24 overflow-hidden">
    <!-- Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-green-500 to-emerald-600"></div>
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
                        <i class="fas fa-check-circle text-green-300"></i>
                        <span>7 Tage kostenlos</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-300"></i>
                        <span>API & Webhooks</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-300"></i>
                        <span>DSGVO-konform</span>
                    </div>
                </div>
            </div>
            
            <!-- Visual -->
            <div class="hidden lg:block">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="bg-white rounded-xl shadow-2xl p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">NaturKosmetik Shop</div>
                                <div class="text-sm text-gray-500">empfohlen.de/naturkosmetik</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600"><?= $stats['empfehler'] ?></div>
                                <div class="text-xs text-gray-500">Empfehler</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-emerald-600"><?= $stats['conversions'] ?></div>
                                <div class="text-xs text-gray-500">Neukunden</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600"><?= $stats['rate'] ?></div>
                                <div class="text-xs text-gray-500">Conversion</div>
                            </div>
                        </div>
                        <div class="text-center text-sm text-gray-500">
                            <i class="fas fa-bolt text-yellow-500 mr-1"></i>
                            12.340€ zusätzlicher Umsatz diesen Monat
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
                Die beste Alternative zu teuren Facebook und Google Ads.
            </p>
        </div>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
            <?php foreach ($vorteile as $vorteil): ?>
            <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-6 hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center text-green-600 dark:text-green-400 text-xl mb-4">
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
            <span class="inline-flex items-center gap-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white px-5 py-2 rounded-full text-sm font-bold shadow-lg mb-4">
                <span>🛒</span> Live erleben
            </span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                So funktioniert Empfehlungsmarketing im E-Commerce
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg max-w-2xl mx-auto">
                Drei interaktive Demos zeigen den Weg von der Empfehlung zum Umsatz.
            </p>
        </div>
        
        <!-- Tab Navigation -->
        <div class="flex flex-wrap justify-center gap-3 mb-8" id="shop-animation-tabs">
            <button onclick="showShopAnimation('journey')" id="tab-journey" class="shop-tab active px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-gradient-to-r from-green-500 to-emerald-600 text-white shadow-lg">
                📦 Paket-Reise
            </button>
            <button onclick="showShopAnimation('cac')" id="tab-cac" class="shop-tab px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-white dark:bg-slate-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-600 hover:shadow-md">
                💰 CAC-Vergleich
            </button>
            <button onclick="showShopAnimation('viral')" id="tab-viral" class="shop-tab px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-white dark:bg-slate-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-600 hover:shadow-md">
                🚀 Virale Bestell-Kette
            </button>
        </div>
        
        <!-- Animation Containers -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-8 min-h-[600px] shadow-lg">
            
            <!-- ========================================= -->
            <!-- ANIMATION 1: PAKET-REISE                 -->
            <!-- ========================================= -->
            <div id="animation-journey" class="shop-animation-content">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Die Paket-Reise</h3>
                    <p class="text-gray-500 dark:text-gray-400">Vom Empfehlungslink zum Paket – und wieder zurück</p>
                </div>
                
                <div class="max-w-3xl mx-auto">
                    <!-- Progress Line -->
                    <div class="relative mb-8">
                        <div class="absolute top-8 left-0 right-0 h-1 bg-gray-200 dark:bg-slate-700 rounded-full">
                            <div id="journey-progress" class="h-full bg-gradient-to-r from-green-500 to-emerald-500 rounded-full transition-all duration-500" style="width: 0%"></div>
                        </div>
                        
                        <!-- Journey Steps -->
                        <div class="grid grid-cols-7 gap-2 relative">
                            <!-- Step 1: Link -->
                            <div id="journey-step-1" class="journey-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">
                                    🔗
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Link teilen</span>
                            </div>
                            
                            <!-- Step 2: Click -->
                            <div id="journey-step-2" class="journey-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">
                                    👆
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Klick</span>
                            </div>
                            
                            <!-- Step 3: Shop -->
                            <div id="journey-step-3" class="journey-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">
                                    🛒
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Warenkorb</span>
                            </div>
                            
                            <!-- Step 4: Checkout -->
                            <div id="journey-step-4" class="journey-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">
                                    💳
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Checkout</span>
                            </div>
                            
                            <!-- Step 5: Shipping -->
                            <div id="journey-step-5" class="journey-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">
                                    🚚
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Versand</span>
                            </div>
                            
                            <!-- Step 6: Delivery -->
                            <div id="journey-step-6" class="journey-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">
                                    📦
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Ankunft</span>
                            </div>
                            
                            <!-- Step 7: Loop -->
                            <div id="journey-step-7" class="journey-step flex flex-col items-center opacity-40 transition-all duration-500">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl mb-2 transition-all duration-500">
                                    ♻️
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center font-medium">Wieder!</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Animated Package -->
                    <div class="relative h-32 mb-6 overflow-hidden">
                        <div id="journey-package" class="absolute transition-all duration-1000 ease-out" style="left: 0%">
                            <div class="text-6xl animate-bounce-slow">📦</div>
                        </div>
                        <!-- Flying elements container -->
                        <div id="journey-effects" class="absolute inset-0 pointer-events-none"></div>
                    </div>
                    
                    <!-- Info Box -->
                    <div id="journey-info" class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-4 mb-6 text-center">
                        <div id="journey-text" class="text-gray-700 dark:text-gray-300 font-medium">
                            Klicken Sie, um die Reise zu starten!
                        </div>
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-4 text-center">
                            <div id="journey-orders" class="text-2xl font-bold text-green-600 dark:text-green-400">0</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Bestellungen</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-4 text-center">
                            <div id="journey-revenue" class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">0€</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Umsatz</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-4 text-center">
                            <div id="journey-reward" class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">-</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Belohnung</div>
                        </div>
                    </div>
                    
                    <!-- Action Button -->
                    <button onclick="startJourney()" id="journey-btn" class="w-full py-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-bold text-lg hover:shadow-lg hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-play"></i>
                        <span>Reise starten</span>
                    </button>
                    
                    <!-- Reset -->
                    <button onclick="resetJourney()" class="w-full mt-3 py-2 text-gray-500 dark:text-gray-400 text-sm hover:text-green-600 dark:hover:text-green-400 transition-colors">
                        ↻ Demo zurücksetzen
                    </button>
                </div>
            </div>
            
            <!-- ========================================= -->
            <!-- ANIMATION 2: CAC-VERGLEICHSRECHNER       -->
            <!-- ========================================= -->
            <div id="animation-cac" class="shop-animation-content hidden">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">CAC-Vergleichsrechner</h3>
                    <p class="text-gray-500 dark:text-gray-400">So viel sparen Sie mit Empfehlungen vs. Facebook Ads</p>
                </div>
                
                <div class="max-w-2xl mx-auto">
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-slate-800 dark:to-slate-700 rounded-2xl p-6 md:p-8">
                        
                        <!-- Slider -->
                        <div class="mb-8">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Wie viele Neukunden möchten Sie gewinnen?
                            </label>
                            <div class="flex items-center gap-4">
                                <input type="range" id="cac-slider" min="10" max="500" value="100" class="flex-1 h-3 bg-gray-300 dark:bg-slate-600 rounded-full appearance-none cursor-pointer accent-green-500" oninput="updateCAC()">
                                <div class="bg-white dark:bg-slate-600 rounded-xl px-4 py-2 min-w-[80px] text-center">
                                    <span id="cac-customers" class="text-2xl font-bold text-gray-900 dark:text-white">100</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Comparison Cards -->
                        <div class="grid md:grid-cols-2 gap-6 mb-8">
                            <!-- Facebook Ads -->
                            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 border-2 border-red-200 dark:border-red-900/50">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                                        <i class="fab fa-facebook-f text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white">Facebook Ads</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Ø 35€ pro Neukunde</div>
                                    </div>
                                </div>
                                
                                <!-- Money Stack -->
                                <div class="h-40 flex items-end justify-center mb-4">
                                    <div id="cac-facebook-stack" class="flex flex-wrap justify-center gap-1 transition-all duration-500">
                                        <!-- Money icons will be added by JS -->
                                    </div>
                                </div>
                                
                                <div class="text-center">
                                    <div class="text-3xl font-black text-red-600 dark:text-red-400" id="cac-facebook-cost">3.500€</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Gesamtkosten</div>
                                </div>
                            </div>
                            
                            <!-- Empfehlungen -->
                            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 border-2 border-green-400 dark:border-green-500 relative overflow-hidden">
                                <!-- Winner Badge -->
                                <div class="absolute top-2 right-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                    EMPFOHLEN
                                </div>
                                
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-users text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white">Empfehlungen</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Ø 3,50€ pro Neukunde</div>
                                    </div>
                                </div>
                                
                                <!-- Money Stack (smaller) -->
                                <div class="h-40 flex items-end justify-center mb-4">
                                    <div id="cac-referral-stack" class="flex flex-wrap justify-center gap-1 transition-all duration-500">
                                        <!-- Money icons will be added by JS -->
                                    </div>
                                </div>
                                
                                <div class="text-center">
                                    <div class="text-3xl font-black text-green-600 dark:text-green-400" id="cac-referral-cost">350€</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Gesamtkosten</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Savings Result -->
                        <div id="cac-savings-box" class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-6 text-white text-center transform transition-all duration-500">
                            <div class="text-lg mb-2">💰 Ihre Ersparnis</div>
                            <div class="text-5xl font-black mb-2" id="cac-savings">3.150€</div>
                            <div class="text-xl text-white/90">
                                Das sind <span id="cac-percent" class="font-bold">90%</span> weniger!
                            </div>
                        </div>
                        
                        <!-- Pro Tip -->
                        <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-4 flex items-start gap-3">
                            <div class="text-2xl">💡</div>
                            <div class="text-sm text-yellow-800 dark:text-yellow-200">
                                <strong>Pro-Tipp:</strong> Bei Leadbusiness zahlen Sie nur 99€/Monat – unabhängig davon, wie viele Neukunden Sie gewinnen. Das macht den Unterschied noch größer!
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ========================================= -->
            <!-- ANIMATION 3: VIRALE BESTELL-KETTE        -->
            <!-- ========================================= -->
            <div id="animation-viral" class="shop-animation-content hidden">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Virale Bestell-Kette</h3>
                    <p class="text-gray-500 dark:text-gray-400">Sehen Sie, wie aus einer Empfehlung exponentielles Wachstum entsteht!</p>
                </div>
                
                <div class="max-w-3xl mx-auto">
                    <!-- Tree Visualization -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-slate-800 dark:to-slate-700 rounded-2xl p-6 md:p-8 mb-6">
                        
                        <!-- Stats Bar -->
                        <div class="grid grid-cols-4 gap-3 mb-6">
                            <div class="bg-white dark:bg-slate-600 rounded-xl p-3 text-center">
                                <div id="viral-generation" class="text-2xl font-bold text-green-600 dark:text-green-400">0</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Generation</div>
                            </div>
                            <div class="bg-white dark:bg-slate-600 rounded-xl p-3 text-center">
                                <div id="viral-orders" class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">0</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Bestellungen</div>
                            </div>
                            <div class="bg-white dark:bg-slate-600 rounded-xl p-3 text-center">
                                <div id="viral-revenue" class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">0€</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Umsatz</div>
                            </div>
                            <div class="bg-white dark:bg-slate-600 rounded-xl p-3 text-center">
                                <div id="viral-reward" class="text-2xl font-bold text-purple-600 dark:text-purple-400">-</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Belohnung</div>
                            </div>
                        </div>
                        
                        <!-- Tree Container -->
                        <div id="viral-tree" class="min-h-[300px] flex flex-col items-center justify-start py-4 relative">
                            <!-- Root Node (Empfehler) -->
                            <div id="viral-root" class="relative z-10">
                                <div class="viral-node w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex flex-col items-center justify-center text-white shadow-lg transform transition-all duration-500 scale-100">
                                    <div class="text-2xl">👤</div>
                                    <div class="text-xs font-bold">Max</div>
                                </div>
                            </div>
                            
                            <!-- Generation 1 -->
                            <div id="viral-gen-1" class="flex justify-center gap-8 mt-4 opacity-0 transform translate-y-4 transition-all duration-500">
                                <div class="viral-branch flex flex-col items-center">
                                    <div class="w-0.5 h-6 bg-green-400"></div>
                                    <div class="viral-node w-16 h-16 bg-white dark:bg-slate-600 rounded-xl flex flex-col items-center justify-center shadow-md border-2 border-green-400">
                                        <div class="text-xl">📦</div>
                                        <div class="text-[10px] text-gray-600 dark:text-gray-300">Lisa</div>
                                    </div>
                                </div>
                                <div class="viral-branch flex flex-col items-center">
                                    <div class="w-0.5 h-6 bg-green-400"></div>
                                    <div class="viral-node w-16 h-16 bg-white dark:bg-slate-600 rounded-xl flex flex-col items-center justify-center shadow-md border-2 border-green-400">
                                        <div class="text-xl">📦</div>
                                        <div class="text-[10px] text-gray-600 dark:text-gray-300">Tom</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Generation 2 -->
                            <div id="viral-gen-2" class="flex justify-center gap-4 mt-4 opacity-0 transform translate-y-4 transition-all duration-500">
                                <div class="viral-branch flex flex-col items-center">
                                    <div class="w-0.5 h-4 bg-emerald-400"></div>
                                    <div class="viral-node w-12 h-12 bg-white dark:bg-slate-600 rounded-lg flex items-center justify-center shadow border-2 border-emerald-400 text-sm">
                                        📦
                                    </div>
                                </div>
                                <div class="viral-branch flex flex-col items-center">
                                    <div class="w-0.5 h-4 bg-emerald-400"></div>
                                    <div class="viral-node w-12 h-12 bg-white dark:bg-slate-600 rounded-lg flex items-center justify-center shadow border-2 border-emerald-400 text-sm">
                                        📦
                                    </div>
                                </div>
                                <div class="viral-branch flex flex-col items-center">
                                    <div class="w-0.5 h-4 bg-emerald-400"></div>
                                    <div class="viral-node w-12 h-12 bg-white dark:bg-slate-600 rounded-lg flex items-center justify-center shadow border-2 border-emerald-400 text-sm">
                                        📦
                                    </div>
                                </div>
                                <div class="viral-branch flex flex-col items-center">
                                    <div class="w-0.5 h-4 bg-emerald-400"></div>
                                    <div class="viral-node w-12 h-12 bg-white dark:bg-slate-600 rounded-lg flex items-center justify-center shadow border-2 border-emerald-400 text-sm">
                                        📦
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Generation 3 -->
                            <div id="viral-gen-3" class="flex justify-center gap-2 mt-4 opacity-0 transform translate-y-4 transition-all duration-500">
                                <div class="viral-node w-10 h-10 bg-white dark:bg-slate-600 rounded flex items-center justify-center shadow border-2 border-teal-400 text-xs">📦</div>
                                <div class="viral-node w-10 h-10 bg-white dark:bg-slate-600 rounded flex items-center justify-center shadow border-2 border-teal-400 text-xs">📦</div>
                                <div class="viral-node w-10 h-10 bg-white dark:bg-slate-600 rounded flex items-center justify-center shadow border-2 border-teal-400 text-xs">📦</div>
                                <div class="viral-node w-10 h-10 bg-white dark:bg-slate-600 rounded flex items-center justify-center shadow border-2 border-teal-400 text-xs">📦</div>
                                <div class="viral-node w-10 h-10 bg-white dark:bg-slate-600 rounded flex items-center justify-center shadow border-2 border-teal-400 text-xs">📦</div>
                                <div class="viral-node w-10 h-10 bg-white dark:bg-slate-600 rounded flex items-center justify-center shadow border-2 border-teal-400 text-xs">📦</div>
                                <div class="viral-node w-10 h-10 bg-white dark:bg-slate-600 rounded flex items-center justify-center shadow border-2 border-teal-400 text-xs">📦</div>
                                <div class="viral-node w-10 h-10 bg-white dark:bg-slate-600 rounded flex items-center justify-center shadow border-2 border-teal-400 text-xs">📦</div>
                            </div>
                            
                            <!-- Floating package animation container -->
                            <div id="viral-effects" class="absolute inset-0 pointer-events-none overflow-hidden"></div>
                        </div>
                        
                        <!-- Info Text -->
                        <div id="viral-info" class="text-center bg-white dark:bg-slate-600 rounded-xl p-4 mb-4">
                            <div id="viral-text" class="text-gray-700 dark:text-gray-300 font-medium">
                                Klicken Sie, um die Kettenreaktion zu starten!
                            </div>
                        </div>
                        
                        <!-- Action Button -->
                        <button onclick="triggerViralChain()" id="viral-btn" class="w-full py-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-bold text-lg hover:shadow-lg hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-rocket"></i>
                            <span>Erste Empfehlung auslösen</span>
                        </button>
                        
                        <!-- Reset -->
                        <button onclick="resetViralChain()" class="w-full mt-3 py-2 text-gray-500 dark:text-gray-400 text-sm hover:text-green-600 dark:hover:text-green-400 transition-colors">
                            ↻ Demo zurücksetzen
                        </button>
                    </div>
                    
                    <!-- Final Celebration -->
                    <div id="viral-celebration" class="hidden">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-6 text-white text-center animate-bounce-in">
                            <div class="text-4xl mb-2">🎉🚀🎉</div>
                            <div class="font-black text-xl mb-2">EXPONENTIELLES WACHSTUM!</div>
                            <div class="text-white/90">Aus 1 Empfehler wurden <span class="font-bold">15 Bestellungen</span></div>
                            <div class="mt-3 inline-flex items-center gap-2 bg-white/20 px-4 py-2 rounded-full text-sm">
                                <span>🎁</span> Max erhält: 50€ Gutschein
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
    @keyframes bounce-slow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    @keyframes pulse-green {
        0%, 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        50% { box-shadow: 0 0 0 15px rgba(34, 197, 94, 0); }
    }
    @keyframes float-up {
        0% { opacity: 1; transform: translateY(0) scale(1); }
        100% { opacity: 0; transform: translateY(-50px) scale(0.5); }
    }
    @keyframes money-shake {
        0%, 100% { transform: rotate(-3deg); }
        50% { transform: rotate(3deg); }
    }
    @keyframes confetti-fall {
        0% { transform: translateY(-100%) rotate(0deg); opacity: 1; }
        100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
    }
    @keyframes package-fly {
        0% { opacity: 1; transform: translate(0, 0) scale(1); }
        50% { opacity: 1; transform: translate(var(--tx, 50px), var(--ty, -30px)) scale(1.2); }
        100% { opacity: 0; transform: translate(var(--tx2, 100px), var(--ty2, 50px)) scale(0.5); }
    }
    
    /* Animation Classes */
    .animate-bounce-in { animation: bounceIn 0.5s ease forwards; }
    .animate-bounce-slow { animation: bounce-slow 2s ease-in-out infinite; }
    .animate-pulse-green { animation: pulse-green 2s ease-in-out infinite; }
    .animate-money-shake { animation: money-shake 0.3s ease-in-out infinite; }
    
    /* Tab Styling */
    .shop-tab.active {
        background: linear-gradient(to right, #22c55e, #10b981);
        color: white;
        box-shadow: 0 10px 15px -3px rgba(34, 197, 94, 0.3);
        border: none;
    }
    
    /* Journey step active state */
    .journey-step.active {
        opacity: 1 !important;
    }
    .journey-step.active > div:first-child {
        background: linear-gradient(to bottom right, #22c55e, #10b981) !important;
        transform: scale(1.1);
        box-shadow: 0 10px 25px -5px rgba(34, 197, 94, 0.5);
    }
    .journey-step.completed {
        opacity: 1 !important;
    }
    .journey-step.completed > div:first-child {
        background: #d1fae5 !important;
    }
    .dark .journey-step.completed > div:first-child {
        background: #064e3b !important;
    }
    
    /* Range slider styling */
    input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 24px;
        height: 24px;
        background: linear-gradient(to bottom right, #22c55e, #10b981);
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
    input[type="range"]::-moz-range-thumb {
        width: 24px;
        height: 24px;
        background: linear-gradient(to bottom right, #22c55e, #10b981);
        border-radius: 50%;
        cursor: pointer;
        border: none;
    }
    
    /* Viral node pulse on add */
    .viral-node.new {
        animation: bounceIn 0.5s ease forwards, pulse-green 1s ease-in-out;
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
        display: inline-block;
        font-size: 1.5rem;
        transition: all 0.3s ease;
    }
    .money-icon.shake {
        animation: money-shake 0.3s ease-in-out infinite;
    }
</style>

<!-- Animation JavaScript -->
<script>
// ==================== TAB SWITCHING ====================
function showShopAnimation(type) {
    // Hide all animation contents
    document.querySelectorAll('.shop-animation-content').forEach(el => el.classList.add('hidden'));
    document.getElementById('animation-' + type).classList.remove('hidden');
    
    // Update tab styles
    document.querySelectorAll('.shop-tab').forEach(tab => {
        tab.classList.remove('active');
        tab.classList.add('bg-white', 'dark:bg-slate-700', 'text-gray-600', 'dark:text-gray-300', 'border', 'border-gray-200', 'dark:border-slate-600');
    });
    const activeTab = document.getElementById('tab-' + type);
    activeTab.classList.add('active');
    activeTab.classList.remove('bg-white', 'dark:bg-slate-700', 'text-gray-600', 'dark:text-gray-300', 'border', 'border-gray-200', 'dark:border-slate-600');
    
    // Initialize animations
    if (type === 'journey') resetJourney();
    if (type === 'cac') updateCAC();
    if (type === 'viral') resetViralChain();
}

// ==================== ANIMATION 1: PAKET-REISE ====================
let journeyStep = 0;
let journeyInterval = null;
const journeySteps = [
    { text: '🔗 Empfehlungslink wird geteilt...', progress: 14 },
    { text: '👆 Freund klickt auf den Link!', progress: 28 },
    { text: '🛒 Produkte werden in den Warenkorb gelegt...', progress: 42 },
    { text: '💳 Bestellung wird abgeschlossen!', progress: 56, order: true },
    { text: '🚚 Paket ist unterwegs...', progress: 70 },
    { text: '📦 Paket ist angekommen!', progress: 84, unbox: true },
    { text: '♻️ Im Paket: Eigener Empfehlungslink! Der Kreislauf beginnt von vorn...', progress: 100, complete: true }
];

function startJourney() {
    if (journeyStep > 0) return;
    
    document.getElementById('journey-btn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Läuft...</span>';
    document.getElementById('journey-btn').disabled = true;
    
    journeyInterval = setInterval(() => {
        if (journeyStep >= journeySteps.length) {
            clearInterval(journeyInterval);
            document.getElementById('journey-btn').innerHTML = '<i class="fas fa-redo"></i> <span>Nochmal ansehen</span>';
            document.getElementById('journey-btn').disabled = false;
            document.getElementById('journey-btn').onclick = resetJourney;
            createConfetti();
            return;
        }
        
        const step = journeySteps[journeyStep];
        
        // Update progress
        document.getElementById('journey-progress').style.width = step.progress + '%';
        
        // Update step visuals
        for (let i = 1; i <= 7; i++) {
            const stepEl = document.getElementById('journey-step-' + i);
            stepEl.classList.remove('active', 'completed');
            if (i < journeyStep + 1) stepEl.classList.add('completed');
            if (i === journeyStep + 1) stepEl.classList.add('active');
        }
        
        // Move package
        const packagePos = (journeyStep / 6) * 85;
        document.getElementById('journey-package').style.left = packagePos + '%';
        
        // Update text
        document.getElementById('journey-text').innerHTML = step.text;
        
        // Special effects
        if (step.order) {
            document.getElementById('journey-orders').textContent = '1';
            document.getElementById('journey-revenue').textContent = '65€';
            addFloatingEffect('journey-effects', '💰', 3);
        }
        
        if (step.unbox) {
            addFloatingEffect('journey-effects', '🎁', 5);
            document.getElementById('journey-reward').textContent = '10%';
        }
        
        if (step.complete) {
            document.getElementById('journey-info').classList.add('bg-gradient-to-r', 'from-green-500', 'to-emerald-500');
            document.getElementById('journey-info').classList.remove('bg-gradient-to-r', 'from-green-50', 'to-emerald-50', 'dark:from-green-900/20', 'dark:to-emerald-900/20');
            document.getElementById('journey-text').classList.add('text-white');
            document.getElementById('journey-text').classList.remove('text-gray-700', 'dark:text-gray-300');
        }
        
        journeyStep++;
    }, 1500);
}

function resetJourney() {
    clearInterval(journeyInterval);
    journeyStep = 0;
    
    // Reset progress
    document.getElementById('journey-progress').style.width = '0%';
    
    // Reset steps
    for (let i = 1; i <= 7; i++) {
        document.getElementById('journey-step-' + i).classList.remove('active', 'completed');
    }
    
    // Reset package
    document.getElementById('journey-package').style.left = '0%';
    
    // Reset stats
    document.getElementById('journey-orders').textContent = '0';
    document.getElementById('journey-revenue').textContent = '0€';
    document.getElementById('journey-reward').textContent = '-';
    
    // Reset info box
    document.getElementById('journey-info').classList.remove('bg-gradient-to-r', 'from-green-500', 'to-emerald-500');
    document.getElementById('journey-info').classList.add('bg-gradient-to-r', 'from-green-50', 'to-emerald-50', 'dark:from-green-900/20', 'dark:to-emerald-900/20');
    document.getElementById('journey-text').classList.remove('text-white');
    document.getElementById('journey-text').classList.add('text-gray-700', 'dark:text-gray-300');
    document.getElementById('journey-text').innerHTML = 'Klicken Sie, um die Reise zu starten!';
    
    // Reset button
    document.getElementById('journey-btn').innerHTML = '<i class="fas fa-play"></i> <span>Reise starten</span>';
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

// ==================== ANIMATION 2: CAC-VERGLEICHSRECHNER ====================
function updateCAC() {
    const customers = parseInt(document.getElementById('cac-slider').value);
    document.getElementById('cac-customers').textContent = customers;
    
    const facebookCost = customers * 35;
    const referralCost = customers * 3.5;
    const savings = facebookCost - referralCost;
    const percent = Math.round((savings / facebookCost) * 100);
    
    // Update costs
    document.getElementById('cac-facebook-cost').textContent = facebookCost.toLocaleString('de-DE') + '€';
    document.getElementById('cac-referral-cost').textContent = referralCost.toLocaleString('de-DE') + '€';
    document.getElementById('cac-savings').textContent = savings.toLocaleString('de-DE') + '€';
    document.getElementById('cac-percent').textContent = percent + '%';
    
    // Update money stacks
    updateMoneyStack('cac-facebook-stack', Math.min(customers / 10, 30), '💸');
    updateMoneyStack('cac-referral-stack', Math.min(customers / 100, 5), '💵');
    
    // Animate savings box
    const savingsBox = document.getElementById('cac-savings-box');
    savingsBox.style.transform = 'scale(1.02)';
    setTimeout(() => {
        savingsBox.style.transform = 'scale(1)';
    }, 200);
}

function updateMoneyStack(containerId, count, emoji) {
    const container = document.getElementById(containerId);
    const currentCount = container.children.length;
    const targetCount = Math.floor(count);
    
    if (targetCount > currentCount) {
        // Add money
        for (let i = currentCount; i < targetCount; i++) {
            const money = document.createElement('span');
            money.className = 'money-icon';
            money.textContent = emoji;
            money.style.animationDelay = (i * 0.05) + 's';
            container.appendChild(money);
        }
    } else if (targetCount < currentCount) {
        // Remove money
        for (let i = currentCount; i > targetCount; i--) {
            if (container.lastChild) {
                container.removeChild(container.lastChild);
            }
        }
    }
}

// ==================== ANIMATION 3: VIRALE BESTELL-KETTE ====================
let viralGeneration = 0;
const viralData = [
    { gen: 1, orders: 2, revenue: 130, text: '🎉 Generation 1: 2 Freunde haben bestellt!' },
    { gen: 2, orders: 6, revenue: 390, text: '🚀 Generation 2: Jeder hat 2 Freunde eingeladen!', reward: '10%' },
    { gen: 3, orders: 14, revenue: 910, text: '🔥 Generation 3: Die Kette wächst exponentiell!', reward: 'Gratis Versand' },
    { gen: 4, orders: 15, revenue: 975, text: '🏆 VIRAL! Max erhält den 50€ Gutschein!', reward: '50€ Gutschein', final: true }
];

function triggerViralChain() {
    if (viralGeneration >= viralData.length) return;
    
    const data = viralData[viralGeneration];
    
    // Show generation
    if (viralGeneration === 0) {
        document.getElementById('viral-gen-1').classList.remove('opacity-0', 'translate-y-4');
        addViralPackages(2);
    } else if (viralGeneration === 1) {
        document.getElementById('viral-gen-2').classList.remove('opacity-0', 'translate-y-4');
        addViralPackages(4);
    } else if (viralGeneration === 2) {
        document.getElementById('viral-gen-3').classList.remove('opacity-0', 'translate-y-4');
        addViralPackages(8);
    }
    
    // Update stats
    document.getElementById('viral-generation').textContent = data.gen;
    document.getElementById('viral-orders').textContent = data.orders;
    document.getElementById('viral-revenue').textContent = data.revenue + '€';
    if (data.reward) {
        document.getElementById('viral-reward').textContent = data.reward;
    }
    
    // Update info text
    document.getElementById('viral-text').innerHTML = data.text;
    
    // Update button
    viralGeneration++;
    if (viralGeneration < viralData.length) {
        document.getElementById('viral-btn').innerHTML = '<i class="fas fa-rocket"></i> <span>Nächste Generation (+' + (viralGeneration === 1 ? '2' : viralGeneration === 2 ? '4' : '8') + ' Bestellungen)</span>';
    } else {
        document.getElementById('viral-btn').innerHTML = '<i class="fas fa-check"></i> <span>Kettenreaktion komplett!</span>';
        document.getElementById('viral-btn').disabled = true;
        document.getElementById('viral-btn').classList.add('opacity-70');
        document.getElementById('viral-celebration').classList.remove('hidden');
        createConfetti();
        
        // Highlight root node
        document.querySelector('#viral-root .viral-node').classList.add('animate-pulse-green');
    }
}

function addViralPackages(count) {
    const container = document.getElementById('viral-effects');
    for (let i = 0; i < count; i++) {
        setTimeout(() => {
            const pkg = document.createElement('div');
            pkg.textContent = '📦';
            pkg.className = 'absolute text-2xl';
            pkg.style.left = '50%';
            pkg.style.top = '20%';
            pkg.style.setProperty('--tx', (Math.random() - 0.5) * 200 + 'px');
            pkg.style.setProperty('--ty', Math.random() * 100 + 'px');
            pkg.style.setProperty('--tx2', (Math.random() - 0.5) * 300 + 'px');
            pkg.style.setProperty('--ty2', Math.random() * 150 + 50 + 'px');
            pkg.style.animation = 'package-fly 1s ease forwards';
            container.appendChild(pkg);
            setTimeout(() => pkg.remove(), 1000);
        }, i * 100);
    }
}

function resetViralChain() {
    viralGeneration = 0;
    
    // Hide generations
    document.getElementById('viral-gen-1').classList.add('opacity-0', 'translate-y-4');
    document.getElementById('viral-gen-2').classList.add('opacity-0', 'translate-y-4');
    document.getElementById('viral-gen-3').classList.add('opacity-0', 'translate-y-4');
    
    // Reset stats
    document.getElementById('viral-generation').textContent = '0';
    document.getElementById('viral-orders').textContent = '0';
    document.getElementById('viral-revenue').textContent = '0€';
    document.getElementById('viral-reward').textContent = '-';
    
    // Reset info
    document.getElementById('viral-text').innerHTML = 'Klicken Sie, um die Kettenreaktion zu starten!';
    
    // Reset button
    document.getElementById('viral-btn').innerHTML = '<i class="fas fa-rocket"></i> <span>Erste Empfehlung auslösen</span>';
    document.getElementById('viral-btn').disabled = false;
    document.getElementById('viral-btn').classList.remove('opacity-70');
    
    // Hide celebration
    document.getElementById('viral-celebration').classList.add('hidden');
    
    // Remove pulse from root
    document.querySelector('#viral-root .viral-node').classList.remove('animate-pulse-green');
    
    // Clear effects
    document.getElementById('viral-effects').innerHTML = '';
}

// ==================== HELPER FUNCTIONS ====================
function createConfetti() {
    const colors = ['#22c55e', '#10b981', '#fbbf24', '#3b82f6', '#8b5cf6'];
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
    // Initialize CAC calculator
    updateCAC();
});
</script>

<!-- Integration Section -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            <div>
                <span class="text-green-600 dark:text-green-400 font-semibold uppercase tracking-wide text-sm">E-Commerce Integration</span>
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mt-2 mb-6">
                    Nahtlose Shop-Integration
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-lg mb-8">
                    Verbinden Sie Leadbusiness mit Ihrem Shop-System über Webhooks und API:
                </p>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-4 bg-gray-50 dark:bg-slate-800 rounded-xl p-4">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center text-green-600 dark:text-green-400 flex-shrink-0">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white">Nach Bestellung</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Automatisch Empfehlungslink per E-Mail senden</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 bg-gray-50 dark:bg-slate-800 rounded-xl p-4">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center text-green-600 dark:text-green-400 flex-shrink-0">
                            <i class="fas fa-sync"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white">Conversion-Tracking</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Webhook bei Kauf → automatische Zuordnung</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 bg-gray-50 dark:bg-slate-800 rounded-xl p-4">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center text-green-600 dark:text-green-400 flex-shrink-0">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white">Gutschein-Codes</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Automatisch generierte Codes für Ihr Shop-System</div>
                        </div>
                    </div>
                </div>
                
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-6">
                    <i class="fas fa-plug mr-1 text-green-500"></i>
                    Kompatibel mit Shopify, WooCommerce, Shopware und mehr
                </p>
            </div>
            
            <div class="bg-slate-900 dark:bg-slate-950 rounded-2xl p-6 md:p-8 shadow-lg">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    <span class="text-gray-500 text-sm ml-2">Webhook Payload</span>
                </div>
                <pre class="text-sm text-green-400 overflow-x-auto"><code>{
  "event": "order.completed",
  "referrer_code": "MAX123",
  "order": {
    "id": "ORD-2024-001",
    "total": 89.90,
    "customer_email": "neu@kunde.de"
  },
  "attribution": {
    "referrer_id": 456,
    "reward_triggered": true,
    "reward_level": 2
  }
}</code></pre>
            </div>
        </div>
    </div>
</section>

<!-- Belohnungen Section -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            <div class="order-2 lg:order-1">
                <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 md:p-8 shadow-lg border border-gray-200 dark:border-slate-600">
                    <div class="text-center mb-6">
                        <div class="text-5xl mb-3">🎁</div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Belohnung freigeschaltet!</h3>
                        <p class="text-gray-500 dark:text-gray-400">Du hast Stufe 3 erreicht</p>
                    </div>
                    
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/10 rounded-xl p-5 border border-green-200 dark:border-green-700/30 mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-green-500 rounded-full flex items-center justify-center text-2xl">
                                🚚
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 dark:text-white text-lg">Gratis Versand</div>
                                <div class="text-gray-600 dark:text-gray-300">Für 1 ganzes Jahr!</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-slate-800 rounded-lg p-4 text-center">
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Dein Gutscheincode:</div>
                        <div class="font-mono text-lg font-bold text-green-600 dark:text-green-400 bg-white dark:bg-slate-700 rounded px-4 py-2 inline-block">
                            FREESHIP-MAX123
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="order-1 lg:order-2">
                <span class="text-green-600 dark:text-green-400 font-semibold uppercase tracking-wide text-sm">Belohnungssystem</span>
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mt-2 mb-6">
                    Beispiel-Belohnungen für Ihren Shop
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-lg mb-8">
                    Belohnungen, die Kunden lieben und die Ihre Marge schonen:
                </p>
                
                <div class="space-y-4">
                    <?php foreach ($belohnungen as $b): ?>
                    <div class="flex items-center gap-4 bg-white dark:bg-slate-700 rounded-xl p-4 shadow-sm">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
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
                    Tipp: "Gratis Versand" hat hohen wahrgenommenen Wert bei niedrigen Kosten.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonial Section -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-slate-800 dark:to-slate-700 rounded-2xl p-8 md:p-12 text-center">
            <div class="flex justify-center gap-1 text-yellow-400 mb-6">
                <?php for ($i = 0; $i < 5; $i++): ?>
                <i class="fas fa-star text-xl"></i>
                <?php endfor; ?>
            </div>
            
            <blockquote class="text-xl md:text-2xl font-medium text-gray-900 dark:text-white mb-8 leading-relaxed">
                "<?= $testimonial['text'] ?>"
            </blockquote>
            
            <div class="flex items-center justify-center gap-4">
                <div class="w-14 h-14 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
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
<section class="py-12 md:py-20 bg-gradient-to-r from-green-500 to-emerald-600 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold mb-4 md:mb-6">
            Bereit für mehr Umsatz durch Empfehlungen?
        </h2>
        <p class="text-lg md:text-xl text-white/90 mb-6 md:mb-8">
            Starten Sie noch heute und senken Sie Ihre Customer Acquisition Costs.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/onboarding" class="btn-white btn-large inline-flex items-center justify-center gap-2">
                <span>Jetzt 7 Tage kostenlos testen</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <p class="text-white/70 mt-6 text-sm">
            Keine Kreditkarte erforderlich · API & Webhooks inklusive · DSGVO-konform
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
            <a href="/branchen/onlinemarketing" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-full text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors text-sm font-medium">
                <i class="fas fa-bullhorn mr-1"></i> Online-Marketing
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../templates/marketing/footer.php'; ?>
