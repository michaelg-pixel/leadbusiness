<?php
/**
 * Branchenseite: Coaches & Berater
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
                        <span>14 Tage kostenlos</span>
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

<!-- Belohnungen Section -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
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
                    <div class="flex items-center gap-4 bg-white dark:bg-slate-700 rounded-xl p-4 shadow-sm">
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
            
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 md:p-8 shadow-lg">
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
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
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
            <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-5 flex items-start gap-4">
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
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
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
