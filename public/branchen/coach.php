<?php
/**
 * Branchenseite: Coaches & Berater
 * Mit interaktiven Animationen und Live-Demo
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
    ['stufe' => 1, 'belohnung' => 'Gratis E-Book oder Workbook', 'icon' => '📚'],
    ['stufe' => 3, 'belohnung' => '30-Min Bonus-Coaching-Call', 'icon' => '📞'],
    ['stufe' => 5, 'belohnung' => '50% Rabatt auf nächstes Coaching-Paket', 'icon' => '🎁'],
];

$testimonials = [
    [
        'text' => 'Als Life Coach lebte ich von Mundpropaganda – aber ohne System. Mit Leadbusiness habe ich endlich einen strukturierten Prozess. 60% meiner Neukunden kommen jetzt über Empfehlungen.',
        'name' => 'Julia Weber',
        'rolle' => 'Life & Business Coach, München',
        'initialen' => 'JW',
        'avatar_bg' => 'bg-purple-600',
    ],
    [
        'text' => 'Meine Klienten empfehlen mich jetzt aktiv weiter, weil sie selbst davon profitieren. Das System läuft komplett automatisch – ich konzentriere mich aufs Coaching.',
        'name' => 'Thomas Richter',
        'rolle' => 'Karriere-Coach, Frankfurt',
        'initialen' => 'TR',
        'avatar_bg' => 'bg-indigo-600',
    ],
    [
        'text' => 'Das Beste: Die vorqualifizierten Leads. Wenn jemand über eine Empfehlung kommt, ist das Vertrauen schon da. Keine langen Verkaufsgespräche mehr nötig.',
        'name' => 'Sarah Hoffmann',
        'rolle' => 'Mindset-Coach, Hamburg',
        'initialen' => 'SH',
        'avatar_bg' => 'bg-pink-600',
    ],
];

$stats = [
    'empfehler' => '167',
    'conversions' => '78',
    'rate' => '47',
];
?>

<!-- Hero Section mit Animation -->
<section class="relative py-16 md:py-24 overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-purple-600 via-indigo-600 to-purple-700"></div>
    <div class="absolute inset-0 opacity-30">
        <div class="absolute top-20 left-10 w-72 h-72 bg-white rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-indigo-300 rounded-full blur-3xl animate-float-delayed"></div>
        <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-purple-300 rounded-full blur-3xl animate-pulse-slow"></div>
    </div>
    
    <!-- Floating Icons -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[15%] left-[10%] animate-bounce-slow opacity-20">
            <i class="fas fa-lightbulb text-white text-4xl"></i>
        </div>
        <div class="absolute top-[25%] right-[15%] animate-bounce-slow-delayed opacity-20">
            <i class="fas fa-brain text-white text-3xl"></i>
        </div>
        <div class="absolute bottom-[30%] left-[20%] animate-float opacity-20">
            <i class="fas fa-heart text-white text-3xl"></i>
        </div>
        <div class="absolute bottom-[20%] right-[25%] animate-float-delayed opacity-20">
            <i class="fas fa-star text-white text-3xl"></i>
        </div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
            <div class="text-white" data-aos="fade-right">
                <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full mb-6 animate-fade-in-down">
                    <i class="fas <?= $branche['icon'] ?>"></i>
                    <span class="text-sm font-medium">Für <?= $branche['name'] ?></span>
                </div>
                
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold mb-6 leading-tight animate-fade-in-up">
                    <?= $branche['heroTitle'] ?>
                </h1>
                
                <p class="text-lg md:text-xl text-white/90 mb-8 leading-relaxed animate-fade-in-up animation-delay-200">
                    <?= $branche['heroSubtitle'] ?>
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 animate-fade-in-up animation-delay-400">
                    <a href="/onboarding" class="group btn-white btn-large inline-flex items-center justify-center gap-2 transform hover:scale-105 transition-all duration-300 hover:shadow-2xl">
                        <span>Jetzt kostenlos starten</span>
                        <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </a>
                    <a href="/preise" class="btn-ghost-white btn-large inline-flex items-center justify-center gap-2 hover:bg-white/20 transition-all duration-300">
                        <span>Preise ansehen</span>
                    </a>
                </div>
                
                <div class="mt-8 flex flex-wrap gap-6 text-white/80 text-sm animate-fade-in-up animation-delay-600">
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
            
            <!-- Interactive Dashboard Demo -->
            <div class="hidden lg:block" data-aos="fade-left" data-aos-delay="200">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20 transform hover:scale-[1.02] transition-transform duration-500">
                    <div class="bg-white rounded-xl shadow-2xl overflow-hidden" id="hero-dashboard">
                        <!-- Dashboard Header -->
                        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-4 text-white">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-lightbulb text-lg"></i>
                                </div>
                                <div>
                                    <div class="font-bold">Julia Weber Coaching</div>
                                    <div class="text-xs text-white/70">empfohlen.de/julia-weber</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Dashboard Stats -->
                        <div class="p-6">
                            <div class="grid grid-cols-3 gap-3 mb-5">
                                <div class="text-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-purple-50 transition-colors group" id="stat-empfehler">
                                    <div class="text-2xl font-bold text-purple-600 group-hover:scale-110 transition-transform">
                                        <span class="counter" data-target="<?= $stats['empfehler'] ?>">0</span>
                                    </div>
                                    <div class="text-xs text-gray-500">Empfehler</div>
                                </div>
                                <div class="text-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-green-50 transition-colors group" id="stat-conversions">
                                    <div class="text-2xl font-bold text-green-600 group-hover:scale-110 transition-transform">
                                        <span class="counter" data-target="<?= $stats['conversions'] ?>">0</span>
                                    </div>
                                    <div class="text-xs text-gray-500">Neuklienten</div>
                                </div>
                                <div class="text-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-yellow-50 transition-colors group" id="stat-rate">
                                    <div class="text-2xl font-bold text-yellow-600 group-hover:scale-110 transition-transform">
                                        <span class="counter" data-target="<?= $stats['rate'] ?>">0</span><span>%</span>
                                    </div>
                                    <div class="text-xs text-gray-500">Conversion</div>
                                </div>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="mb-5">
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-gray-600 font-medium">Nächste Belohnung</span>
                                    <span class="text-purple-600 font-bold">4/5 Empfehlungen</span>
                                </div>
                                <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-purple-500 to-indigo-500 rounded-full progress-bar" style="width: 0%"></div>
                                </div>
                                <div class="mt-2 text-center">
                                    <span class="text-xs text-gray-500">Noch <span class="font-bold text-purple-600">1 Empfehlung</span> bis zum Bonus-Call!</span>
                                </div>
                            </div>
                            
                            <!-- Live Activity Feed -->
                            <div class="border-t pt-4">
                                <div class="text-xs text-gray-500 uppercase tracking-wide mb-3">Live Aktivität</div>
                                <div id="activity-feed" class="space-y-2 text-sm">
                                    <div class="activity-item flex items-center gap-2 text-gray-600 opacity-0 translate-y-2 transition-all duration-500">
                                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                        <span>Max M. hat gerade geteilt</span>
                                        <span class="text-xs text-gray-400">vor 2 Min</span>
                                    </div>
                                    <div class="activity-item flex items-center gap-2 text-gray-600 opacity-0 translate-y-2 transition-all duration-500">
                                        <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                                        <span>Neue Empfehlung von Sarah K.</span>
                                        <span class="text-xs text-gray-400">vor 15 Min</span>
                                    </div>
                                    <div class="activity-item flex items-center gap-2 text-gray-600 opacity-0 translate-y-2 transition-all duration-500">
                                        <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                                        <span>🎉 Lisa erreichte Stufe 2!</span>
                                        <span class="text-xs text-gray-400">vor 1 Std</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Dashboard Footer -->
                        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 px-6 py-3 text-center border-t">
                            <div class="inline-flex items-center gap-2 text-sm">
                                <i class="fas fa-award text-purple-500"></i>
                                <span class="text-gray-600">Top 10% der Empfehlungsprogramme</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Vorteile Section mit Scroll-Animation -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4" data-aos="fade-up">
                Warum Empfehlungsmarketing für <?= $branche['name'] ?>?
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                Ihre Transformation spricht für sich – lassen Sie Ihre Klienten davon erzählen.
            </p>
        </div>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
            <?php foreach ($vorteile as $index => $vorteil): ?>
            <div class="group bg-gray-50 dark:bg-slate-800 rounded-2xl p-6 hover:shadow-xl transition-all duration-500 hover:-translate-y-2 cursor-pointer" 
                 data-aos="fade-up" 
                 data-aos-delay="<?= $index * 100 ?>">
                <div class="w-14 h-14 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center text-purple-600 dark:text-purple-400 text-2xl mb-4 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                    <i class="fas <?= $vorteil['icon'] ?>"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 group-hover:text-purple-600 transition-colors"><?= $vorteil['title'] ?></h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm"><?= $vorteil['text'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Interactive Belohnungen Section -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            <div data-aos="fade-right">
                <span class="text-purple-600 dark:text-purple-400 font-semibold uppercase tracking-wide text-sm">Belohnungssystem</span>
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mt-2 mb-6">
                    Beispiel-Belohnungen für Ihr Coaching
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-lg mb-8">
                    Belohnen Sie Empfehlungen mit Mehrwert, der zu Ihrem Angebot passt:
                </p>
                
                <!-- Interactive Reward Steps -->
                <div class="space-y-4" id="reward-steps">
                    <?php foreach ($belohnungen as $index => $b): ?>
                    <div class="reward-step flex items-center gap-4 bg-white dark:bg-slate-700 rounded-xl p-4 shadow-sm cursor-pointer hover:shadow-lg transition-all duration-300 hover:-translate-x-2 <?= $index === 1 ? 'ring-2 ring-purple-500 bg-purple-50 dark:bg-purple-900/20' : '' ?>"
                         data-step="<?= $index ?>"
                         data-aos="fade-right"
                         data-aos-delay="<?= $index * 150 ?>">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0 <?= $index === 1 ? 'animate-pulse-ring' : '' ?>">
                            <?= $b['stufe'] ?>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-500 dark:text-gray-400"><?= $b['stufe'] ?> Empfehlung<?= $b['stufe'] > 1 ? 'en' : '' ?></div>
                            <div class="font-semibold text-gray-900 dark:text-white"><?= $b['belohnung'] ?></div>
                        </div>
                        <div class="text-3xl"><?= $b['icon'] ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-6 flex items-center gap-2" data-aos="fade-up" data-aos-delay="400">
                    <i class="fas fa-lightbulb text-yellow-500"></i>
                    <span>Tipp: Digitale Downloads (E-Books, Workbooks) haben null Grenzkosten.</span>
                </p>
            </div>
            
            <!-- Interactive Reward Demo -->
            <div data-aos="fade-left" data-aos-delay="200">
                <div id="reward-demo" class="bg-white dark:bg-slate-700 rounded-2xl p-6 md:p-8 shadow-xl relative overflow-hidden">
                    <!-- Confetti Container -->
                    <div id="confetti-container" class="absolute inset-0 pointer-events-none z-10"></div>
                    
                    <!-- Demo Content -->
                    <div class="relative z-0">
                        <div class="text-center mb-6">
                            <div class="text-6xl mb-3 reward-icon animate-bounce-slow">🎯</div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                <span id="reward-title">Belohnung freigeschaltet!</span>
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400">
                                <span id="reward-subtitle">Du hast Stufe 3 erreicht</span>
                            </p>
                        </div>
                        
                        <div id="reward-card" class="bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/10 rounded-xl p-5 border border-purple-200 dark:border-purple-700/30 mb-6 transform transition-all duration-500">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center text-3xl reward-card-icon animate-wiggle">
                                    📞
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900 dark:text-white text-lg" id="reward-name">Bonus-Coaching-Call</div>
                                    <div class="text-gray-600 dark:text-gray-300" id="reward-desc">30 Minuten 1:1 mit Julia</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <button id="claim-reward-btn" class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-calendar-check"></i>
                                <span>Termin jetzt buchen</span>
                            </button>
                        </div>
                        
                        <!-- Success State (hidden initially) -->
                        <div id="reward-success" class="hidden text-center">
                            <div class="text-green-500 text-6xl mb-4">✓</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-white">Belohnung eingelöst!</div>
                            <div class="text-gray-500 dark:text-gray-400 mt-2">Prüfe deine E-Mails für Details</div>
                        </div>
                    </div>
                </div>
                
                <!-- Click hint -->
                <p class="text-center text-sm text-gray-400 mt-4 animate-pulse">
                    <i class="fas fa-hand-pointer mr-1"></i>
                    Klicke auf die Stufen links, um verschiedene Belohnungen zu sehen
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Use Cases with Hover Effects -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4" data-aos="fade-up">
                Perfekt für jeden Coaching-Bereich
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                Egal welche Transformation Sie begleiten – Empfehlungen funktionieren überall
            </p>
        </div>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php 
            $useCases = [
                ['icon' => 'fa-heart', 'title' => 'Life Coaching', 'text' => 'Persönliche Transformation wird gerne weiterempfohlen', 'color' => 'pink'],
                ['icon' => 'fa-briefcase', 'title' => 'Business Coaching', 'text' => 'Unternehmer empfehlen Unternehmer', 'color' => 'blue'],
                ['icon' => 'fa-running', 'title' => 'Fitness & Health', 'text' => 'Sichtbare Ergebnisse sprechen für sich', 'color' => 'green'],
                ['icon' => 'fa-brain', 'title' => 'Mindset Coaching', 'text' => 'Mentale Durchbrüche werden geteilt', 'color' => 'purple'],
                ['icon' => 'fa-users', 'title' => 'Beziehungscoaching', 'text' => 'Glückliche Paare empfehlen gerne', 'color' => 'red'],
                ['icon' => 'fa-graduation-cap', 'title' => 'Karriere Coaching', 'text' => 'Erfolgreiche Karrieren inspirieren andere', 'color' => 'indigo'],
            ];
            $colorClasses = [
                'pink' => 'bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 group-hover:bg-pink-200',
                'blue' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 group-hover:bg-blue-200',
                'green' => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 group-hover:bg-green-200',
                'purple' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 group-hover:bg-purple-200',
                'red' => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 group-hover:bg-red-200',
                'indigo' => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-200',
            ];
            foreach ($useCases as $index => $useCase): ?>
            <div class="group bg-gray-50 dark:bg-slate-800 rounded-xl p-5 flex items-start gap-4 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer"
                 data-aos="zoom-in"
                 data-aos-delay="<?= $index * 100 ?>">
                <div class="w-12 h-12 <?= $colorClasses[$useCase['color']] ?> rounded-lg flex items-center justify-center flex-shrink-0 transition-all duration-300 group-hover:scale-110 group-hover:rotate-6">
                    <i class="fas <?= $useCase['icon'] ?> text-lg"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-purple-600 transition-colors"><?= $useCase['title'] ?></h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm"><?= $useCase['text'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Testimonial Carousel -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800 overflow-hidden">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10" data-aos="fade-up">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Das sagen andere Coaches
            </h2>
            <p class="text-gray-600 dark:text-gray-400">Echte Erfahrungen von echten Coaches</p>
        </div>
        
        <!-- Testimonial Slider -->
        <div class="relative" data-aos="fade-up" data-aos-delay="200">
            <div id="testimonial-carousel" class="overflow-hidden rounded-2xl">
                <div id="testimonial-track" class="flex transition-transform duration-500 ease-out">
                    <?php foreach ($testimonials as $index => $testimonial): ?>
                    <div class="testimonial-slide min-w-full px-4">
                        <div class="bg-white dark:bg-slate-700 rounded-2xl p-8 md:p-10 shadow-lg">
                            <div class="flex justify-center gap-1 text-yellow-400 mb-6">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                <i class="fas fa-star text-xl"></i>
                                <?php endfor; ?>
                            </div>
                            
                            <blockquote class="text-lg md:text-xl font-medium text-gray-900 dark:text-white mb-8 leading-relaxed text-center">
                                "<?= $testimonial['text'] ?>"
                            </blockquote>
                            
                            <div class="flex items-center justify-center gap-4">
                                <div class="w-14 h-14 <?= $testimonial['avatar_bg'] ?> rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    <?= $testimonial['initialen'] ?>
                                </div>
                                <div class="text-left">
                                    <div class="font-bold text-gray-900 dark:text-white"><?= $testimonial['name'] ?></div>
                                    <div class="text-gray-600 dark:text-gray-400 text-sm"><?= $testimonial['rolle'] ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Carousel Controls -->
            <div class="flex justify-center items-center gap-4 mt-6">
                <button id="prev-testimonial" class="w-10 h-10 rounded-full bg-white dark:bg-slate-700 shadow-md flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-purple-100 dark:hover:bg-purple-900/30 hover:text-purple-600 transition-all">
                    <i class="fas fa-chevron-left"></i>
                </button>
                
                <div id="testimonial-dots" class="flex gap-2">
                    <?php foreach ($testimonials as $index => $t): ?>
                    <button class="testimonial-dot w-2.5 h-2.5 rounded-full transition-all duration-300 <?= $index === 0 ? 'bg-purple-600 w-6' : 'bg-gray-300 dark:bg-gray-600' ?>" data-index="<?= $index ?>"></button>
                    <?php endforeach; ?>
                </div>
                
                <button id="next-testimonial" class="w-10 h-10 rounded-full bg-white dark:bg-slate-700 shadow-md flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-purple-100 dark:hover:bg-purple-900/30 hover:text-purple-600 transition-all">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- How It Works - Animated Steps -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4" data-aos="fade-up">
                In 3 Schritten zu mehr Klienten
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                Ihr automatisches Empfehlungsprogramm ist in wenigen Minuten einsatzbereit
            </p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8 relative">
            <!-- Connection Lines (Desktop) -->
            <div class="hidden md:block absolute top-16 left-1/4 right-1/4 h-0.5 bg-gradient-to-r from-purple-500 via-indigo-500 to-purple-500"></div>
            
            <?php
            $steps = [
                ['number' => '1', 'icon' => 'fa-magic', 'title' => 'Einrichten', 'text' => 'Wählen Sie Ihre Belohnungen aus – wir haben passende Vorschläge für Coaches.'],
                ['number' => '2', 'icon' => 'fa-share-alt', 'title' => 'Teilen', 'text' => 'Ihre Klienten erhalten einen persönlichen Empfehlungslink mit Ihrer Seite.'],
                ['number' => '3', 'icon' => 'fa-chart-line', 'title' => 'Wachsen', 'text' => 'Neue Klienten kommen automatisch – Sie konzentrieren sich aufs Coaching.'],
            ];
            foreach ($steps as $index => $step): ?>
            <div class="relative text-center group" data-aos="fade-up" data-aos-delay="<?= $index * 200 ?>">
                <div class="relative z-10 w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-2xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                    <i class="fas <?= $step['icon'] ?> text-3xl"></i>
                </div>
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-8 h-8 bg-white dark:bg-slate-900 rounded-full border-4 border-purple-500 text-purple-600 font-bold text-sm flex items-center justify-center z-20">
                    <?= $step['number'] ?>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-purple-600 transition-colors"><?= $step['title'] ?></h3>
                <p class="text-gray-600 dark:text-gray-400"><?= $step['text'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section with Animation -->
<section class="py-12 md:py-20 bg-gradient-to-r from-purple-600 via-indigo-600 to-purple-700 text-white relative overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0 opacity-20">
        <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-0 right-0 w-80 h-80 bg-indigo-300 rounded-full blur-3xl animate-float-delayed"></div>
    </div>
    
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div data-aos="zoom-in">
            <div class="text-6xl mb-6">🚀</div>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold mb-4 md:mb-6">
                Bereit für mehr Klienten durch Empfehlungen?
            </h2>
            <p class="text-lg md:text-xl text-white/90 mb-6 md:mb-8">
                Starten Sie noch heute und lassen Sie Ihre Klienten für Sie sprechen.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/onboarding" class="group btn-white btn-large inline-flex items-center justify-center gap-2 transform hover:scale-105 transition-all duration-300 hover:shadow-2xl">
                    <span>Jetzt 7 Tage kostenlos testen</span>
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
            <p class="text-white/70 mt-6 text-sm">
                <i class="fas fa-lock mr-1"></i>
                Keine Kreditkarte erforderlich · Einrichtung in 5 Minuten · DSGVO-konform
            </p>
        </div>
    </div>
</section>

<!-- Andere Branchen -->
<section class="py-12 md:py-16 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h3 class="text-center text-lg font-semibold text-gray-900 dark:text-white mb-8" data-aos="fade-up">
            Leadbusiness für andere Branchen
        </h3>
        <div class="flex flex-wrap justify-center gap-3" data-aos="fade-up" data-aos-delay="100">
            <?php
            $branches = [
                ['slug' => 'zahnarzt', 'icon' => 'fa-tooth', 'name' => 'Zahnärzte'],
                ['slug' => 'friseur', 'icon' => 'fa-cut', 'name' => 'Friseure'],
                ['slug' => 'fitness', 'icon' => 'fa-dumbbell', 'name' => 'Fitnessstudios'],
                ['slug' => 'restaurant', 'icon' => 'fa-utensils', 'name' => 'Restaurants'],
                ['slug' => 'onlineshop', 'icon' => 'fa-shopping-cart', 'name' => 'Online-Shops'],
                ['slug' => 'onlinemarketing', 'icon' => 'fa-bullhorn', 'name' => 'Online-Marketing'],
                ['slug' => 'handwerker', 'icon' => 'fa-hammer', 'name' => 'Handwerker'],
            ];
            foreach ($branches as $branch): ?>
            <a href="/branchen/<?= $branch['slug'] ?>" class="group px-4 py-2 bg-gray-100 dark:bg-slate-800 hover:bg-purple-100 dark:hover:bg-purple-900/30 rounded-full text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-300 text-sm font-medium hover:-translate-y-1">
                <i class="fas <?= $branch['icon'] ?> mr-1 group-hover:rotate-12 transition-transform"></i> <?= $branch['name'] ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Custom Styles -->
<style>
/* Animations */
@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
}

@keyframes float-delayed {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-15px) rotate(-3deg); }
}

@keyframes bounce-slow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

@keyframes bounce-slow-delayed {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}

@keyframes pulse-slow {
    0%, 100% { opacity: 0.3; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.05); }
}

@keyframes pulse-ring {
    0% { box-shadow: 0 0 0 0 rgba(139, 92, 246, 0.5); }
    70% { box-shadow: 0 0 0 15px rgba(139, 92, 246, 0); }
    100% { box-shadow: 0 0 0 0 rgba(139, 92, 246, 0); }
}

@keyframes wiggle {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(-5deg); }
    75% { transform: rotate(5deg); }
}

@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fade-in-down {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes confetti-fall {
    0% { transform: translateY(-100%) rotate(0deg); opacity: 1; }
    100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
}

.animate-float { animation: float 6s ease-in-out infinite; }
.animate-float-delayed { animation: float-delayed 8s ease-in-out infinite; }
.animate-bounce-slow { animation: bounce-slow 3s ease-in-out infinite; }
.animate-bounce-slow-delayed { animation: bounce-slow-delayed 4s ease-in-out infinite 0.5s; }
.animate-pulse-slow { animation: pulse-slow 4s ease-in-out infinite; }
.animate-pulse-ring { animation: pulse-ring 2s ease-out infinite; }
.animate-wiggle { animation: wiggle 1s ease-in-out infinite; }
.animate-fade-in-up { animation: fade-in-up 0.6s ease-out forwards; }
.animate-fade-in-down { animation: fade-in-down 0.6s ease-out forwards; }

.animation-delay-200 { animation-delay: 0.2s; }
.animation-delay-400 { animation-delay: 0.4s; }
.animation-delay-600 { animation-delay: 0.6s; }

/* Progress bar animation */
.progress-bar {
    animation: progress-fill 1.5s ease-out forwards;
    animation-delay: 0.5s;
}

@keyframes progress-fill {
    from { width: 0%; }
    to { width: 80%; }
}

/* Confetti */
.confetti {
    position: absolute;
    width: 10px;
    height: 10px;
    animation: confetti-fall 3s linear forwards;
}

/* Reward card hover effect */
#reward-card {
    transform-style: preserve-3d;
}

#reward-card:hover {
    transform: perspective(1000px) rotateY(5deg) scale(1.02);
}

/* Testimonial dots */
.testimonial-dot {
    cursor: pointer;
}
.testimonial-dot:hover {
    background-color: rgba(139, 92, 246, 0.5);
}
</style>

<!-- Interactive JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========================
    // Counter Animation
    // ========================
    const counters = document.querySelectorAll('.counter');
    let countersAnimated = false;
    
    function animateCounters() {
        if (countersAnimated) return;
        
        counters.forEach(counter => {
            const target = parseInt(counter.dataset.target);
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;
            
            const updateCounter = () => {
                current += step;
                if (current < target) {
                    counter.textContent = Math.floor(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target;
                }
            };
            
            updateCounter();
        });
        
        countersAnimated = true;
    }
    
    // Trigger counters when hero section is visible
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                setTimeout(animateCounters, 500);
            }
        });
    }, { threshold: 0.5 });
    
    const heroDashboard = document.getElementById('hero-dashboard');
    if (heroDashboard) observer.observe(heroDashboard);
    
    // ========================
    // Activity Feed Animation
    // ========================
    const activityItems = document.querySelectorAll('.activity-item');
    activityItems.forEach((item, index) => {
        setTimeout(() => {
            item.classList.remove('opacity-0', 'translate-y-2');
        }, 1000 + (index * 500));
    });
    
    // ========================
    // Reward Steps Interaction
    // ========================
    const rewardSteps = document.querySelectorAll('.reward-step');
    const rewardData = [
        { icon: '📚', title: 'Gratis E-Book', desc: 'Exklusives Workbook für Ihren Erfolg', subtitle: 'Du hast Stufe 1 erreicht' },
        { icon: '📞', title: 'Bonus-Coaching-Call', desc: '30 Minuten 1:1 mit Julia', subtitle: 'Du hast Stufe 3 erreicht' },
        { icon: '🎁', title: '50% Rabatt', desc: 'Auf Ihr nächstes Coaching-Paket', subtitle: 'Du hast Stufe 5 erreicht' }
    ];
    
    rewardSteps.forEach(step => {
        step.addEventListener('click', function() {
            const stepIndex = parseInt(this.dataset.step);
            const data = rewardData[stepIndex];
            
            // Update active state
            rewardSteps.forEach(s => {
                s.classList.remove('ring-2', 'ring-purple-500', 'bg-purple-50', 'dark:bg-purple-900/20');
            });
            this.classList.add('ring-2', 'ring-purple-500', 'bg-purple-50', 'dark:bg-purple-900/20');
            
            // Update demo
            const rewardCard = document.getElementById('reward-card');
            rewardCard.style.transform = 'scale(0.95)';
            
            setTimeout(() => {
                document.getElementById('reward-subtitle').textContent = data.subtitle;
                document.getElementById('reward-name').textContent = data.title;
                document.getElementById('reward-desc').textContent = data.desc;
                document.querySelector('.reward-card-icon').textContent = data.icon;
                rewardCard.style.transform = 'scale(1)';
            }, 200);
        });
    });
    
    // ========================
    // Claim Reward Button
    // ========================
    const claimBtn = document.getElementById('claim-reward-btn');
    const rewardSuccess = document.getElementById('reward-success');
    const confettiContainer = document.getElementById('confetti-container');
    
    if (claimBtn) {
        claimBtn.addEventListener('click', function() {
            // Create confetti
            createConfetti();
            
            // Hide button, show success
            claimBtn.style.display = 'none';
            document.getElementById('reward-card').style.display = 'none';
            document.querySelector('.reward-icon').style.display = 'none';
            document.getElementById('reward-title').style.display = 'none';
            document.querySelector('#reward-demo p').style.display = 'none';
            rewardSuccess.classList.remove('hidden');
            
            // Reset after 3 seconds
            setTimeout(() => {
                claimBtn.style.display = 'flex';
                document.getElementById('reward-card').style.display = 'block';
                document.querySelector('.reward-icon').style.display = 'block';
                document.getElementById('reward-title').style.display = 'block';
                document.querySelector('#reward-demo p').style.display = 'block';
                rewardSuccess.classList.add('hidden');
            }, 3000);
        });
    }
    
    function createConfetti() {
        const colors = ['#8b5cf6', '#6366f1', '#ec4899', '#f59e0b', '#10b981', '#3b82f6'];
        
        for (let i = 0; i < 50; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.animationDelay = Math.random() * 0.5 + 's';
            confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
            confettiContainer.appendChild(confetti);
            
            // Remove after animation
            setTimeout(() => confetti.remove(), 3000);
        }
    }
    
    // ========================
    // Testimonial Carousel
    // ========================
    const track = document.getElementById('testimonial-track');
    const dots = document.querySelectorAll('.testimonial-dot');
    const prevBtn = document.getElementById('prev-testimonial');
    const nextBtn = document.getElementById('next-testimonial');
    let currentSlide = 0;
    const totalSlides = <?= count($testimonials) ?>;
    
    function goToSlide(index) {
        if (index < 0) index = totalSlides - 1;
        if (index >= totalSlides) index = 0;
        
        currentSlide = index;
        track.style.transform = `translateX(-${currentSlide * 100}%)`;
        
        // Update dots
        dots.forEach((dot, i) => {
            if (i === currentSlide) {
                dot.classList.add('bg-purple-600', 'w-6');
                dot.classList.remove('bg-gray-300', 'dark:bg-gray-600');
            } else {
                dot.classList.remove('bg-purple-600', 'w-6');
                dot.classList.add('bg-gray-300', 'dark:bg-gray-600');
            }
        });
    }
    
    if (prevBtn && nextBtn) {
        prevBtn.addEventListener('click', () => goToSlide(currentSlide - 1));
        nextBtn.addEventListener('click', () => goToSlide(currentSlide + 1));
    }
    
    dots.forEach(dot => {
        dot.addEventListener('click', () => {
            goToSlide(parseInt(dot.dataset.index));
        });
    });
    
    // Auto-advance carousel
    setInterval(() => {
        goToSlide(currentSlide + 1);
    }, 6000);
    
    // ========================
    // AOS-like Scroll Animations
    // ========================
    const animatedElements = document.querySelectorAll('[data-aos]');
    
    const animationObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const delay = entry.target.dataset.aosDelay || 0;
                setTimeout(() => {
                    entry.target.classList.add('aos-animate');
                }, delay);
            }
        });
    }, { threshold: 0.1 });
    
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        animationObserver.observe(el);
    });
    
    // Add animation class styles
    const style = document.createElement('style');
    style.textContent = `
        .aos-animate {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
        [data-aos="zoom-in"].aos-animate {
            transform: scale(1) !important;
        }
        [data-aos="zoom-in"] {
            transform: scale(0.9);
        }
        [data-aos="fade-left"] {
            transform: translateX(30px);
        }
        [data-aos="fade-left"].aos-animate {
            transform: translateX(0) !important;
        }
        [data-aos="fade-right"] {
            transform: translateX(-30px);
        }
        [data-aos="fade-right"].aos-animate {
            transform: translateX(0) !important;
        }
    `;
    document.head.appendChild(style);
});
</script>

<?php require_once __DIR__ . '/../../templates/marketing/footer.php'; ?>
