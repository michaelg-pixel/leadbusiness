<?php
/**
 * Branchenseite: Online-Marketing & Kursanbieter
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
                        <span>Digistore24-kompatibel</span>
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

<!-- vs Affiliate Section -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
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
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
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
                    <div class="flex items-center gap-4 bg-gray-50 dark:bg-slate-800 rounded-xl p-4">
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

<!-- Digistore Integration -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-indigo-600 to-violet-600 rounded-2xl p-8 md:p-12 text-white">
            <div class="grid md:grid-cols-2 gap-8 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full mb-6">
                        <i class="fas fa-plug"></i>
                        <span class="text-sm font-medium">Integration</span>
                    </div>
                    <h3 class="text-2xl md:text-3xl font-bold mb-4">
                        Funktioniert mit Digistore24 & Co.
                    </h3>
                    <p class="text-white/90 text-lg mb-6">
                        Verbinden Sie Leadbusiness mit Ihrer bestehenden Infrastruktur:
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-green-300"></i>
                            <span>Digistore24 IPN-Webhook</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-green-300"></i>
                            <span>CopeCart Integration</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-green-300"></i>
                            <span>Elopage Anbindung</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-green-300"></i>
                            <span>KlickTipp & andere E-Mail-Tools</span>
                        </li>
                    </ul>
                </div>
                <div class="text-center">
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20 inline-block">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white rounded-xl p-4 flex items-center justify-center">
                                <span class="font-bold text-gray-800">Digistore24</span>
                            </div>
                            <div class="bg-white rounded-xl p-4 flex items-center justify-center">
                                <span class="font-bold text-gray-800">CopeCart</span>
                            </div>
                            <div class="bg-white rounded-xl p-4 flex items-center justify-center">
                                <span class="font-bold text-gray-800">Elopage</span>
                            </div>
                            <div class="bg-white rounded-xl p-4 flex items-center justify-center">
                                <span class="font-bold text-gray-800">KlickTipp</span>
                            </div>
                        </div>
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
            <div class="bg-white dark:bg-slate-700 rounded-xl p-5 flex items-start gap-4">
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
            Keine Kreditkarte erforderlich · Digistore24-kompatibel · DSGVO-konform
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
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../templates/marketing/footer.php'; ?>
