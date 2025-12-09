<?php
/**
 * Branchenseite: Handwerker
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
                        <span>14 Tage kostenlos</span>
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

<!-- Problem/Lösung Section -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            <!-- Problem -->
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 md:p-8 border-2 border-red-200 dark:border-red-900/30">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-times text-red-500 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Das Problem heute</h3>
                </div>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-times text-red-500 mt-1 flex-shrink-0"></i>
                        <span class="text-gray-600 dark:text-gray-300">Empfehlungen passieren, aber Sie wissen nicht wer und wie oft</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-times text-red-500 mt-1 flex-shrink-0"></i>
                        <span class="text-gray-600 dark:text-gray-300">Treue Kunden werden nicht belohnt</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-times text-red-500 mt-1 flex-shrink-0"></i>
                        <span class="text-gray-600 dark:text-gray-300">Teure Werbung mit ungewissem Ergebnis</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-times text-red-500 mt-1 flex-shrink-0"></i>
                        <span class="text-gray-600 dark:text-gray-300">Wertvolles Wachstumspotenzial ungenutzt</span>
                    </li>
                </ul>
            </div>
            
            <!-- Lösung -->
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-2xl p-6 md:p-8 border-2 border-amber-300 dark:border-amber-700 relative">
                <div class="absolute -top-3 right-6 bg-amber-600 text-white text-xs font-bold px-3 py-1 rounded-full">
                    MIT LEADBUSINESS
                </div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check text-amber-600 dark:text-amber-400 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Die Lösung</h3>
                </div>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check text-green-500 mt-1 flex-shrink-0"></i>
                        <span class="text-gray-700 dark:text-gray-200">Jede Empfehlung wird automatisch erfasst</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check text-green-500 mt-1 flex-shrink-0"></i>
                        <span class="text-gray-700 dark:text-gray-200">Empfehler werden automatisch belohnt</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check text-green-500 mt-1 flex-shrink-0"></i>
                        <span class="text-gray-700 dark:text-gray-200">Kunden werben aktiv für Sie</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check text-green-500 mt-1 flex-shrink-0"></i>
                        <span class="text-gray-700 dark:text-gray-200">Null manueller Aufwand für Sie</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

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

<!-- So funktioniert's -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                So einfach funktioniert's
            </h2>
        </div>
        
        <div class="grid md:grid-cols-4 gap-6 md:gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-amber-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">1</div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Auftrag erledigt</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Sie schließen einen Auftrag erfolgreich ab.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-amber-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">2</div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Kunde erhält Link</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Der Kunde bekommt seinen persönlichen Empfehlungslink.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-amber-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">3</div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Kunde empfiehlt</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Er teilt den Link mit Nachbarn, Freunden und Familie.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-amber-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">4</div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Automatische Belohnung</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Bei jedem neuen Auftrag wird die Belohnung automatisch versendet.</p>
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

<!-- QR-Code Feature -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
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
                <span>Jetzt 14 Tage kostenlos testen</span>
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
