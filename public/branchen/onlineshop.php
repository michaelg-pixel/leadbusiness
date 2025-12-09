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

<!-- Integration Section -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
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
                    <div class="flex items-start gap-4 bg-white dark:bg-slate-700 rounded-xl p-4 shadow-sm">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center text-green-600 dark:text-green-400 flex-shrink-0">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white">Nach Bestellung</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Automatisch Empfehlungslink per E-Mail senden</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 bg-white dark:bg-slate-700 rounded-xl p-4 shadow-sm">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center text-green-600 dark:text-green-400 flex-shrink-0">
                            <i class="fas fa-sync"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white">Conversion-Tracking</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Webhook bei Kauf → automatische Zuordnung</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 bg-white dark:bg-slate-700 rounded-xl p-4 shadow-sm">
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
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
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
                    <div class="flex items-center gap-4 bg-gray-50 dark:bg-slate-800 rounded-xl p-4">
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

<!-- ROI Calculator Preview -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-8 md:p-12 text-white">
            <div class="grid md:grid-cols-2 gap-8 items-center">
                <div>
                    <h3 class="text-2xl md:text-3xl font-bold mb-4">
                        💰 ROI-Rechnung
                    </h3>
                    <p class="text-white/90 text-lg mb-6">
                        Ein Beispiel aus der Praxis: Bei 1.000 Empfehlern und 30% Conversion-Rate...
                    </p>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center bg-white/20 rounded-lg p-3">
                            <span>Neukunden durch Empfehlungen</span>
                            <span class="font-bold">300</span>
                        </div>
                        <div class="flex justify-between items-center bg-white/20 rounded-lg p-3">
                            <span>Ø Warenkorbwert</span>
                            <span class="font-bold">65€</span>
                        </div>
                        <div class="flex justify-between items-center bg-white/20 rounded-lg p-3">
                            <span>Zusätzlicher Umsatz</span>
                            <span class="font-bold text-yellow-300">19.500€</span>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-6xl mb-4">📈</div>
                    <div class="text-5xl font-bold mb-2">195x</div>
                    <div class="text-white/80">ROI bei 99€/Monat</div>
                </div>
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
