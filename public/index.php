<?php
/**
 * Leadbusiness - Landingpage
 * Vollautomatisches Empfehlungsprogramm für Unternehmen
 */

$pageTitle = 'Empfehlungsprogramm für Ihr Unternehmen';
$metaDescription = 'Vollautomatisches Empfehlungsprogramm: Kunden werben Kunden und werden automatisch belohnt. Einrichtung in 5 Minuten. Ab 49€/Monat.';
$currentPage = 'home';

require_once __DIR__ . '/../templates/marketing/header.php';

// Slider Konfiguration - 6 Zielgruppen
$heroSlides = [
    [
        'id' => 'online-marketer',
        'hook' => 'Ihre Kunden als<br>Marketing-Kanal',
        'icon' => 'fa-bullhorn',
        'business' => 'Marketing Agentur Digital First',
        'subdomain' => 'empfohlen.de/digital-first',
        'stats' => ['empfehler' => '312', 'neukunden' => '124', 'conversion' => '40'],
        'activity' => [
            ['type' => 'user', 'title' => 'Neuer Empfehler', 'desc' => 'Agentur XY - vor 3 Min', 'bg' => 'green'],
            ['type' => 'gift', 'title' => 'Provision ausgezahlt', 'desc' => 'Markus W. - 150€ Bonus', 'bg' => 'yellow'],
            ['type' => 'share', 'title' => 'Kampagne geteilt', 'desc' => 'Lisa M. via LinkedIn', 'bg' => 'blue'],
        ],
        'gradient' => 'from-blue-600 to-blue-800'
    ],
    [
        'id' => 'gastronomie',
        'hook' => 'Wenn Gäste<br>neue Gäste bringen',
        'icon' => 'fa-utensils',
        'business' => 'Restaurant Bella Italia',
        'subdomain' => 'empfohlen.de/bella-italia',
        'stats' => ['empfehler' => '189', 'neukunden' => '67', 'conversion' => '35'],
        'activity' => [
            ['type' => 'user', 'title' => 'Neuer Empfehler', 'desc' => 'Familie Schmidt - vor 5 Min', 'bg' => 'green'],
            ['type' => 'gift', 'title' => 'Gutschein eingelöst', 'desc' => 'Mario B. - Gratis Dessert', 'bg' => 'yellow'],
            ['type' => 'share', 'title' => 'Reservierung geteilt', 'desc' => 'Anna K. via WhatsApp', 'bg' => 'blue'],
        ],
        'gradient' => 'from-blue-700 to-slate-800'
    ],
    [
        'id' => 'coaches',
        'hook' => 'Empfehlungen<br>systematisch statt zufällig',
        'icon' => 'fa-lightbulb',
        'business' => 'Business Coach Weber',
        'subdomain' => 'empfohlen.de/coach-weber',
        'stats' => ['empfehler' => '156', 'neukunden' => '52', 'conversion' => '33'],
        'activity' => [
            ['type' => 'user', 'title' => 'Neuer Empfehler', 'desc' => 'Startup Gründer - vor 8 Min', 'bg' => 'green'],
            ['type' => 'gift', 'title' => 'Bonus-Session', 'desc' => 'Stefan K. - 30 Min Call', 'bg' => 'yellow'],
            ['type' => 'share', 'title' => 'Link geteilt', 'desc' => 'Carla M. via XING', 'bg' => 'blue'],
        ],
        'gradient' => 'from-slate-700 to-blue-900'
    ],
    [
        'id' => 'einzelhandel',
        'hook' => 'Stammkunden zu<br>Botschaftern machen',
        'icon' => 'fa-store',
        'business' => 'Mode Boutique Eleganz',
        'subdomain' => 'empfohlen.de/boutique-eleganz',
        'stats' => ['empfehler' => '234', 'neukunden' => '78', 'conversion' => '33'],
        'activity' => [
            ['type' => 'user', 'title' => 'Neue Empfehlerin', 'desc' => 'Sandra M. - vor 2 Min', 'bg' => 'green'],
            ['type' => 'gift', 'title' => 'Rabatt freigeschaltet', 'desc' => 'Nina K. - 20% Gutschein', 'bg' => 'yellow'],
            ['type' => 'share', 'title' => 'Story geteilt', 'desc' => 'Julia H. via Instagram', 'bg' => 'blue'],
        ],
        'gradient' => 'from-blue-800 to-slate-700'
    ],
    [
        'id' => 'ecommerce',
        'hook' => 'Shop-Kunden als<br>Vertriebspartner',
        'icon' => 'fa-shopping-cart',
        'business' => 'TechGadgets Online Shop',
        'subdomain' => 'empfohlen.de/techgadgets',
        'stats' => ['empfehler' => '487', 'neukunden' => '203', 'conversion' => '42'],
        'activity' => [
            ['type' => 'user', 'title' => 'Neuer Empfehler', 'desc' => 'Tech-Blogger Max - vor 1 Min', 'bg' => 'green'],
            ['type' => 'gift', 'title' => 'Gratis Versand', 'desc' => 'Peter S. - 1 Jahr freigeschaltet', 'bg' => 'yellow'],
            ['type' => 'share', 'title' => 'Produkt geteilt', 'desc' => 'Leon K. via Telegram', 'bg' => 'blue'],
        ],
        'gradient' => 'from-slate-800 to-blue-700'
    ],
    [
        'id' => 'dienstleister',
        'hook' => 'Mundpropaganda<br>digitalisiert',
        'icon' => 'fa-briefcase',
        'business' => 'Handwerker Meisterbetrieb',
        'subdomain' => 'empfohlen.de/meisterbetrieb',
        'stats' => ['empfehler' => '178', 'neukunden' => '64', 'conversion' => '36'],
        'activity' => [
            ['type' => 'user', 'title' => 'Neuer Empfehler', 'desc' => 'Hausbesitzer Müller - vor 4 Min', 'bg' => 'green'],
            ['type' => 'gift', 'title' => 'Gutschrift erhalten', 'desc' => 'Thomas B. - 50€ Rabatt', 'bg' => 'yellow'],
            ['type' => 'share', 'title' => 'Bewertung geteilt', 'desc' => 'Klaus W. via E-Mail', 'bg' => 'blue'],
        ],
        'gradient' => 'from-blue-900 to-slate-800'
    ],
];
?>

<!-- Hero Section mit Slider (nur Desktop) -->
<section id="hero-slider" class="hero-slider-section min-h-screen flex items-center relative overflow-hidden">
    <!-- Slider Background - Desktop only -->
    <div class="hero-slider-backgrounds absolute inset-0 hidden lg:block">
        <?php foreach ($heroSlides as $index => $slide): ?>
        <div class="hero-bg-slide absolute inset-0 bg-gradient-to-br <?= $slide['gradient'] ?> transition-opacity duration-1000 <?= $index === 0 ? 'opacity-100' : 'opacity-0' ?>" data-slide="<?= $index ?>"></div>
        <?php endforeach; ?>
    </div>
    
    <!-- Mobile Background (statisch) -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-blue-800 lg:hidden"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            
            <!-- Hero Content -->
            <div class="text-white">
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full mb-6">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    <span class="text-sm font-medium">Bereits 500+ Unternehmen vertrauen uns</span>
                </div>
                
                <!-- Mobile: Statische Hook -->
                <h1 class="lg:hidden text-4xl md:text-5xl font-extrabold leading-tight mb-6">
                    Kunden werben Kunden –
                    <span class="text-amber-300">vollautomatisch</span>
                </h1>
                
                <!-- Desktop: Slider Hooks Container -->
                <div class="hidden lg:block relative" style="min-height: 180px;">
                    <?php foreach ($heroSlides as $index => $slide): ?>
                    <h1 class="hero-hook-slide text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-6 transition-all duration-500 <?= $index === 0 ? 'opacity-100 relative' : 'opacity-0 absolute top-0 left-0' ?>" data-slide="<?= $index ?>">
                        <?= $slide['hook'] ?> –
                        <span class="text-amber-300">vollautomatisch</span>
                    </h1>
                    <?php endforeach; ?>
                </div>
                
                <p class="text-xl md:text-2xl text-white/90 mb-8 leading-relaxed">
                    Ihr eigenes Empfehlungsprogramm in 5 Minuten. 
                    Ohne Technik-Wissen. Ohne manuellen Aufwand.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 mb-12">
                    <a href="/onboarding/" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-amber-400 to-orange-500 text-gray-900 font-bold text-lg rounded-full shadow-[0_0_30px_rgba(251,191,36,0.4)] hover:shadow-[0_0_50px_rgba(251,191,36,0.6)] hover:scale-105 transition-all duration-300">
                        <span>Jetzt kostenlos starten</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="/demo" class="btn-ghost btn-large inline-flex items-center justify-center gap-2">
                        <i class="fas fa-play-circle"></i>
                        <span>Demo ansehen</span>
                    </a>
                </div>
                
                <!-- Trust Indicators -->
                <div class="flex flex-wrap gap-6 text-white/80">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-400"></i>
                        <span>DSGVO-konform</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-400"></i>
                        <span>Hosting in Deutschland</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-400"></i>
                        <span>7 Tage testen</span>
                    </div>
                </div>
                
                <!-- Slider Dots (Desktop only) -->
                <div class="hidden lg:flex items-center gap-2 mt-8" id="slider-dots">
                    <?php foreach ($heroSlides as $index => $slide): ?>
                    <button class="hero-slider-dot w-2.5 h-2.5 rounded-full transition-all duration-300 <?= $index === 0 ? 'bg-white w-8' : 'bg-white/40 hover:bg-white/60' ?>" data-slide="<?= $index ?>" aria-label="Slide <?= $index + 1 ?>"></button>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Hero Visual - Mobile: Statisch -->
            <div class="relative hidden md:block lg:hidden">
                <div class="relative z-10 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl p-6 transform rotate-2 hover:rotate-0 transition-transform duration-500">
                    <div class="border-b border-gray-200 dark:border-slate-600 pb-4 mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white">
                                <i class="fas fa-tooth"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 dark:text-white">Zahnarzt Dr. Müller</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">empfohlen.de/dr-mueller</div>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-sm font-medium">Aktiv</span>
                    </div>
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="text-center p-3 bg-gray-50 dark:bg-slate-700 rounded-xl">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">247</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Empfehler</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 dark:bg-slate-700 rounded-xl">
                            <div class="text-2xl font-bold text-green-500">89</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Neukunden</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 dark:bg-slate-700 rounded-xl">
                            <div class="text-2xl font-bold text-amber-500">36%</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Conversion</div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">Neuer Empfehler</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Maria S. - vor 2 Min</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-2 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                            <div class="w-8 h-8 bg-amber-500 rounded-full flex items-center justify-center text-white text-sm">
                                <i class="fas fa-gift"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">Belohnung freigeschaltet</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Thomas K. - Stufe 2 erreicht</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm">
                                <i class="fas fa-share-alt"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">Link geteilt</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Anna M. via WhatsApp</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hero Visual - Desktop: Slider Dashboard Cards -->
            <div class="relative hidden lg:block" id="slider-cards">
                <?php foreach ($heroSlides as $index => $slide): ?>
                <div class="hero-card-slide transition-all duration-500 <?= $index === 0 ? 'opacity-100 relative' : 'opacity-0 absolute top-0 left-0 right-0 pointer-events-none' ?>" data-slide="<?= $index ?>">
                    <div class="relative z-10 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl p-6 transform rotate-2 hover:rotate-0 transition-transform duration-500">
                        <!-- Dashboard Header -->
                        <div class="border-b border-gray-200 dark:border-slate-600 pb-4 mb-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br <?= $slide['gradient'] ?> rounded-lg flex items-center justify-center text-white">
                                    <i class="fas <?= $slide['icon'] ?>"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-white"><?= $slide['business'] ?></div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400"><?= $slide['subdomain'] ?></div>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-sm font-medium">Aktiv</span>
                        </div>
                        
                        <!-- Stats -->
                        <div class="grid grid-cols-3 gap-4 mb-6">
                            <div class="text-center p-3 bg-gray-50 dark:bg-slate-700 rounded-xl">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400"><?= $slide['stats']['empfehler'] ?></div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Empfehler</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 dark:bg-slate-700 rounded-xl">
                                <div class="text-2xl font-bold text-green-500"><?= $slide['stats']['neukunden'] ?></div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Neukunden</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 dark:bg-slate-700 rounded-xl">
                                <div class="text-2xl font-bold text-amber-500"><?= $slide['stats']['conversion'] ?>%</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Conversion</div>
                            </div>
                        </div>
                        
                        <!-- Recent Activity -->
                        <div class="space-y-3">
                            <?php foreach ($slide['activity'] as $activity): 
                                $bgClass = $activity['bg'] === 'green' ? 'bg-green-50 dark:bg-green-900/20' : 
                                          ($activity['bg'] === 'yellow' ? 'bg-amber-50 dark:bg-amber-900/20' : 'bg-blue-50 dark:bg-blue-900/20');
                                $iconBg = $activity['bg'] === 'green' ? 'bg-green-500' : 
                                         ($activity['bg'] === 'yellow' ? 'bg-amber-500' : 'bg-blue-500');
                                $icon = $activity['type'] === 'user' ? 'fa-user-plus' : 
                                       ($activity['type'] === 'gift' ? 'fa-gift' : 'fa-share-alt');
                            ?>
                            <div class="flex items-center gap-3 p-2 <?= $bgClass ?> rounded-lg">
                                <div class="w-8 h-8 <?= $iconBg ?> rounded-full flex items-center justify-center text-white text-sm">
                                    <i class="fas <?= $icon ?>"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white"><?= $activity['title'] ?></div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400"><?= $activity['desc'] ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <!-- Floating Elements -->
                <div class="absolute -top-4 -right-4 w-20 h-20 bg-amber-400 rounded-2xl flex items-center justify-center text-3xl floating shadow-lg z-20">
                    🎉
                </div>
                <div class="absolute -bottom-4 -left-4 w-16 h-16 bg-green-400 rounded-2xl flex items-center justify-center text-2xl floating shadow-lg z-20" style="animation-delay: 1s;">
                    💰
                </div>
            </div>
        </div>
    </div>
    
    <!-- Wave Divider -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="fill-white dark:fill-slate-900">
            <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0V120Z"/>
        </svg>
    </div>
</section>

<!-- Hero Slider JavaScript - Mit Theme-Change-Support und Debug-Logging -->
<script>
(function() {
    'use strict';
    
    // Nur auf Desktop (>= 1024px) aktivieren
    if (window.innerWidth < 1024) {
        console.log('[Slider] Mobile erkannt, Slider deaktiviert');
        return;
    }
    
    var SLIDE_INTERVAL = 6000; // 6 Sekunden
    var totalSlides = <?= count($heroSlides) ?>;
    var currentSlide = 0;
    var timerId = null;
    var isPaused = false;
    
    console.log('[Slider] Initialisierung, Interval:', SLIDE_INTERVAL, 'ms');
    
    // Elemente werden bei Bedarf frisch geholt
    function getElements() {
        return {
            bgSlides: document.querySelectorAll('.hero-bg-slide'),
            hookSlides: document.querySelectorAll('.hero-hook-slide'),
            cardSlides: document.querySelectorAll('.hero-card-slide'),
            dots: document.querySelectorAll('.hero-slider-dot')
        };
    }
    
    function goToSlide(index) {
        var els = getElements();
        if (!els.bgSlides.length) return;
        
        index = ((index % totalSlides) + totalSlides) % totalSlides;
        
        for (var i = 0; i < els.bgSlides.length; i++) {
            if (i === index) {
                els.bgSlides[i].classList.add('opacity-100');
                els.bgSlides[i].classList.remove('opacity-0');
            } else {
                els.bgSlides[i].classList.remove('opacity-100');
                els.bgSlides[i].classList.add('opacity-0');
            }
        }
        
        for (var i = 0; i < els.hookSlides.length; i++) {
            if (i === index) {
                els.hookSlides[i].classList.add('opacity-100', 'relative');
                els.hookSlides[i].classList.remove('opacity-0', 'absolute');
            } else {
                els.hookSlides[i].classList.remove('opacity-100', 'relative');
                els.hookSlides[i].classList.add('opacity-0', 'absolute');
            }
        }
        
        for (var i = 0; i < els.cardSlides.length; i++) {
            if (i === index) {
                els.cardSlides[i].classList.add('opacity-100', 'relative');
                els.cardSlides[i].classList.remove('opacity-0', 'absolute', 'pointer-events-none');
            } else {
                els.cardSlides[i].classList.remove('opacity-100', 'relative');
                els.cardSlides[i].classList.add('opacity-0', 'absolute', 'pointer-events-none');
            }
        }
        
        for (var i = 0; i < els.dots.length; i++) {
            if (i === index) {
                els.dots[i].classList.add('bg-white', 'w-8');
                els.dots[i].classList.remove('bg-white/40');
            } else {
                els.dots[i].classList.remove('bg-white', 'w-8');
                els.dots[i].classList.add('bg-white/40');
            }
        }
        
        currentSlide = index;
    }
    
    function tick() {
        if (timerId) {
            clearTimeout(timerId);
            timerId = null;
        }
        
        if (isPaused || document.hidden) {
            timerId = setTimeout(tick, SLIDE_INTERVAL);
            return;
        }
        
        var nextSlide = (currentSlide + 1) % totalSlides;
        console.log('[Slider] tick() -> Slide', nextSlide, 'um', new Date().toLocaleTimeString());
        goToSlide(nextSlide);
        
        timerId = setTimeout(tick, SLIDE_INTERVAL);
    }
    
    function restart() {
        console.log('[Slider] restart() aufgerufen um', new Date().toLocaleTimeString());
        if (timerId) {
            clearTimeout(timerId);
            timerId = null;
        }
        timerId = setTimeout(tick, SLIDE_INTERVAL);
    }
    
    function init() {
        var els = getElements();
        
        if (!els.bgSlides.length || !els.hookSlides.length || !els.cardSlides.length || !els.dots.length) {
            console.log('[Slider] Elemente nicht gefunden');
            return;
        }
        
        console.log('[Slider] Elemente gefunden:', els.bgSlides.length, 'Slides');
        
        // Dot Click Handler
        for (var i = 0; i < els.dots.length; i++) {
            (function(index) {
                els.dots[index].addEventListener('click', function() {
                    console.log('[Slider] Dot geklickt:', index);
                    goToSlide(index);
                    restart();
                });
            })(i);
        }
        
        // Hover Pause
        var heroSection = document.getElementById('hero-slider');
        if (heroSection) {
            heroSection.addEventListener('mouseenter', function() {
                isPaused = true;
            });
            heroSection.addEventListener('mouseleave', function() {
                isPaused = false;
            });
        }
        
        // Tab Visibility
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                console.log('[Slider] Tab wieder sichtbar');
                restart();
            }
        });
        
        // Theme-Wechsel Event - Timer neu starten
        window.addEventListener('themechange', function(e) {
            console.log('[Slider] >>> themechange Event empfangen! <<<', e.detail);
            restart();
        });
        
        console.log('[Slider] Event Listener registriert, starte...');
        restart();
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>

<!-- Logo Slider / Industry Ticker (Social Proof) -->
<section class="py-12 bg-gray-50 dark:bg-slate-800 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-center text-gray-500 dark:text-gray-400 mb-8 font-medium">Vertrauen von Unternehmen aus allen Branchen</p>
    </div>
    
    <!-- Animierte Laufschrift -->
    <div class="industry-ticker-wrapper relative">
        <!-- Gradient Overlays für sanftes Ein-/Ausblenden -->
        <div class="absolute left-0 top-0 bottom-0 w-20 md:w-40 bg-gradient-to-r from-gray-50 dark:from-slate-800 to-transparent z-10 pointer-events-none"></div>
        <div class="absolute right-0 top-0 bottom-0 w-20 md:w-40 bg-gradient-to-l from-gray-50 dark:from-slate-800 to-transparent z-10 pointer-events-none"></div>
        
        <div class="industry-ticker">
            <div class="industry-ticker-track">
                <?php
                $industries = [
                    ['icon' => 'fa-tooth', 'name' => 'Zahnärzte'],
                    ['icon' => 'fa-cut', 'name' => 'Friseure'],
                    ['icon' => 'fa-dumbbell', 'name' => 'Fitnessstudios'],
                    ['icon' => 'fa-shopping-cart', 'name' => 'Online-Shops'],
                    ['icon' => 'fa-lightbulb', 'name' => 'Coaches'],
                    ['icon' => 'fa-bullhorn', 'name' => 'Online Marketer'],
                    ['icon' => 'fa-utensils', 'name' => 'Restaurants'],
                    ['icon' => 'fa-hammer', 'name' => 'Handwerker'],
                    ['icon' => 'fa-stethoscope', 'name' => 'Ärzte'],
                    ['icon' => 'fa-heart', 'name' => 'Therapeuten'],
                    ['icon' => 'fa-graduation-cap', 'name' => 'Kursanbieter'],
                    ['icon' => 'fa-envelope', 'name' => 'Newsletter'],
                    ['icon' => 'fa-laptop-code', 'name' => 'SaaS & Software'],
                    ['icon' => 'fa-paint-brush', 'name' => 'Webdesigner'],
                    ['icon' => 'fa-chart-line', 'name' => 'SEO-Agenturen'],
                    ['icon' => 'fa-camera', 'name' => 'Fotografen'],
                    ['icon' => 'fa-spa', 'name' => 'Kosmetikstudios'],
                    ['icon' => 'fa-car', 'name' => 'Autowerkstätten'],
                    ['icon' => 'fa-home', 'name' => 'Immobilienmakler'],
                    ['icon' => 'fa-balance-scale', 'name' => 'Rechtsanwälte'],
                    ['icon' => 'fa-calculator', 'name' => 'Steuerberater'],
                    ['icon' => 'fa-dog', 'name' => 'Tierärzte'],
                    ['icon' => 'fa-music', 'name' => 'Musikschulen'],
                    ['icon' => 'fa-chalkboard-teacher', 'name' => 'Nachhilfelehrer'],
                    ['icon' => 'fa-hotel', 'name' => 'Hotels'],
                    ['icon' => 'fa-bicycle', 'name' => 'Fahrradläden'],
                    ['icon' => 'fa-leaf', 'name' => 'Gärtner'],
                    ['icon' => 'fa-baby', 'name' => 'Hebammen'],
                    ['icon' => 'fa-glasses', 'name' => 'Optiker'],
                    ['icon' => 'fa-podcast', 'name' => 'Podcaster'],
                    ['icon' => 'fa-video', 'name' => 'Content Creator'],
                    ['icon' => 'fa-gem', 'name' => 'Juweliere'],
                ];
                
                for ($i = 0; $i < 2; $i++):
                    foreach ($industries as $industry):
                ?>
                <div class="industry-ticker-item">
                    <i class="fas <?= $industry['icon'] ?>"></i>
                    <span><?= $industry['name'] ?></span>
                </div>
                <?php 
                    endforeach;
                endfor;
                ?>
            </div>
        </div>
    </div>
    
    <style>
        .industry-ticker-wrapper { width: 100%; overflow: hidden; }
        .industry-ticker { display: flex; width: 100%; }
        .industry-ticker-track {
            display: flex;
            gap: 2rem;
            animation: ticker-scroll 60s linear infinite;
            will-change: transform;
        }
        .industry-ticker:hover .industry-ticker-track { animation-play-state: paused; }
        .industry-ticker-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
            padding: 0.75rem 1.5rem;
            background: white;
            border-radius: 9999px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            font-weight: 600;
            color: #6b7280;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }
        .dark .industry-ticker-item { background: #334155; color: #9ca3af; }
        .industry-ticker-item:hover { transform: scale(1.05); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); color: #2563eb; }
        .dark .industry-ticker-item:hover { color: #60a5fa; }
        .industry-ticker-item i { font-size: 1.25rem; color: #2563eb; }
        .dark .industry-ticker-item i { color: #60a5fa; }
        @keyframes ticker-scroll { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        @media (max-width: 768px) {
            .industry-ticker-track { animation-duration: 45s; gap: 1rem; }
            .industry-ticker-item { padding: 0.5rem 1rem; font-size: 0.875rem; }
            .industry-ticker-item i { font-size: 1rem; }
        }
    </style>
</section>

<!-- Problem/Solution Section -->
<section class="py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <span class="text-red-500 font-semibold uppercase tracking-wide">Das Problem</span>
                <h2 class="text-3xl md:text-4xl font-bold mt-3 mb-6 dark:text-white">Empfehlungen passieren – aber zufällig</h2>
                <div class="space-y-4 text-gray-600 dark:text-gray-300">
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <i class="fas fa-times text-red-500"></i>
                        </div>
                        <p>Zufriedene Kunden empfehlen Sie weiter, aber Sie wissen nicht wer und wie oft</p>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <i class="fas fa-times text-red-500"></i>
                        </div>
                        <p>Kein System, um Empfehlungen zu tracken und Kunden zu belohnen</p>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <i class="fas fa-times text-red-500"></i>
                        </div>
                        <p>Wertvolles Wachstumspotenzial bleibt ungenutzt</p>
                    </div>
                </div>
            </div>
            <div>
                <span class="text-green-500 font-semibold uppercase tracking-wide">Die Lösung</span>
                <h2 class="text-3xl md:text-4xl font-bold mt-3 mb-6 dark:text-white">Leadbusiness automatisiert alles</h2>
                <div class="space-y-4 text-gray-600 dark:text-gray-300">
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <i class="fas fa-check text-green-500"></i>
                        </div>
                        <p>Jeder Kunde bekommt einen persönlichen Empfehlungslink</p>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <i class="fas fa-check text-green-500"></i>
                        </div>
                        <p>Empfehlungen werden automatisch getrackt und Belohnungen versendet</p>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <i class="fas fa-check text-green-500"></i>
                        </div>
                        <p>Gamification motiviert Kunden, noch mehr zu empfehlen</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ROI Calculator Section -->
<section class="py-20 bg-gradient-to-br from-slate-50 to-blue-50 dark:from-slate-800 dark:to-slate-900" id="roi-rechner">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-blue-600 dark:text-blue-400 font-semibold uppercase tracking-wide">Kostenvergleich</span>
            <h2 class="text-3xl md:text-4xl font-bold mt-3 dark:text-white">So viel sparen Sie mit Empfehlungen</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-4 max-w-2xl mx-auto">Vergleichen Sie die Kosten für Neukunden über bezahlte Werbung mit unserem Empfehlungsprogramm</p>
        </div>
        
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl p-6 md:p-10 border border-gray-100 dark:border-slate-700">
            <!-- Branchenauswahl -->
            <div class="mb-8">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                    <i class="fas fa-industry mr-2 text-blue-500"></i>Ihre Branche
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" id="industry-selector">
                    <button type="button" data-industry="zahnarzt" data-cpl="95" class="industry-btn active flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-blue-500 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 transition-all">
                        <i class="fas fa-tooth text-2xl"></i>
                        <span class="text-sm font-medium">Zahnarzt</span>
                    </button>
                    <button type="button" data-industry="handwerker" data-cpl="80" class="industry-btn flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-gray-200 dark:border-slate-600 hover:border-blue-400 bg-white dark:bg-slate-700 text-gray-600 dark:text-gray-300 transition-all">
                        <i class="fas fa-hammer text-2xl"></i>
                        <span class="text-sm font-medium">Handwerker</span>
                    </button>
                    <button type="button" data-industry="coach" data-cpl="65" class="industry-btn flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-gray-200 dark:border-slate-600 hover:border-blue-400 bg-white dark:bg-slate-700 text-gray-600 dark:text-gray-300 transition-all">
                        <i class="fas fa-lightbulb text-2xl"></i>
                        <span class="text-sm font-medium">Coach</span>
                    </button>
                    <button type="button" data-industry="fitness" data-cpl="50" class="industry-btn flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-gray-200 dark:border-slate-600 hover:border-blue-400 bg-white dark:bg-slate-700 text-gray-600 dark:text-gray-300 transition-all">
                        <i class="fas fa-dumbbell text-2xl"></i>
                        <span class="text-sm font-medium">Fitness</span>
                    </button>
                    <button type="button" data-industry="friseur" data-cpl="45" class="industry-btn flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-gray-200 dark:border-slate-600 hover:border-blue-400 bg-white dark:bg-slate-700 text-gray-600 dark:text-gray-300 transition-all">
                        <i class="fas fa-cut text-2xl"></i>
                        <span class="text-sm font-medium">Friseur</span>
                    </button>
                    <button type="button" data-industry="onlineshop" data-cpl="35" class="industry-btn flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-gray-200 dark:border-slate-600 hover:border-blue-400 bg-white dark:bg-slate-700 text-gray-600 dark:text-gray-300 transition-all">
                        <i class="fas fa-shopping-cart text-2xl"></i>
                        <span class="text-sm font-medium">Online-Shop</span>
                    </button>
                    <button type="button" data-industry="restaurant" data-cpl="25" class="industry-btn flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-gray-200 dark:border-slate-600 hover:border-blue-400 bg-white dark:bg-slate-700 text-gray-600 dark:text-gray-300 transition-all">
                        <i class="fas fa-utensils text-2xl"></i>
                        <span class="text-sm font-medium">Restaurant</span>
                    </button>
                    <button type="button" data-industry="marketing" data-cpl="85" class="industry-btn flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-gray-200 dark:border-slate-600 hover:border-blue-400 bg-white dark:bg-slate-700 text-gray-600 dark:text-gray-300 transition-all">
                        <i class="fas fa-bullhorn text-2xl"></i>
                        <span class="text-sm font-medium">Marketing</span>
                    </button>
                </div>
            </div>
            
            <!-- Leads Slider -->
            <div class="mb-10">
                <div class="flex justify-between items-center mb-3">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <i class="fas fa-users mr-2 text-blue-500"></i>Gewünschte Neukunden pro Monat
                    </label>
                    <span class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="leads-value">20</span>
                </div>
                <input type="range" min="5" max="100" value="20" step="5" id="leads-slider" 
                    class="w-full h-3 bg-gray-200 dark:bg-slate-600 rounded-full appearance-none cursor-pointer accent-blue-600">
                <div class="flex justify-between text-xs text-gray-400 mt-2">
                    <span>5</span>
                    <span>25</span>
                    <span>50</span>
                    <span>75</span>
                    <span>100</span>
                </div>
            </div>
            
            <!-- Ergebnis -->
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <!-- Bezahlte Werbung -->
                <div class="relative bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 rounded-2xl p-6 border border-red-200 dark:border-red-800">
                    <div class="absolute -top-3 left-4 px-3 py-1 bg-red-500 text-white text-xs font-semibold rounded-full">
                        <i class="fas fa-ad mr-1"></i>Bezahlte Werbung
                    </div>
                    <div class="mt-4 mb-4">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex gap-1">
                                <i class="fab fa-facebook text-blue-600 text-lg"></i>
                                <i class="fab fa-instagram text-pink-600 text-lg"></i>
                                <i class="fab fa-google text-yellow-600 text-lg"></i>
                                <i class="fab fa-tiktok text-gray-800 dark:text-white text-lg"></i>
                            </div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Meta, Google, TikTok, etc.</span>
                        </div>
                        <div class="text-4xl md:text-5xl font-extrabold text-red-600 dark:text-red-400" id="ads-cost">1.900€</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">pro Monat</div>
                    </div>
                    <div class="pt-4 border-t border-red-200 dark:border-red-800">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Ø Kosten pro Lead:</span>
                            <span class="font-semibold text-red-600 dark:text-red-400" id="cpl-value">95€</span>
                        </div>
                    </div>
                    
                    <!-- Balken -->
                    <div class="mt-4 h-4 bg-red-200 dark:bg-red-900/50 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-red-500 to-orange-500 rounded-full transition-all duration-500" id="ads-bar" style="width: 100%"></div>
                    </div>
                </div>
                
                <!-- Leadbusiness -->
                <div class="relative bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-2xl p-6 border border-green-200 dark:border-green-800">
                    <div class="absolute -top-3 left-4 px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded-full">
                        <i class="fas fa-heart mr-1"></i>Leadbusiness
                    </div>
                    <div class="mt-4 mb-4">
                        <div class="flex items-center gap-3 mb-3">
                            <i class="fas fa-users text-green-600 text-xl"></i>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Empfehlungsprogramm</span>
                        </div>
                        <div class="text-4xl md:text-5xl font-extrabold text-green-600 dark:text-green-400" id="lb-cost">99€</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">pro Monat (Professional)</div>
                    </div>
                    <div class="pt-4 border-t border-green-200 dark:border-green-800">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Inklusive bis:</span>
                            <span class="font-semibold text-green-600 dark:text-green-400">5.000 Empfehler</span>
                        </div>
                    </div>
                    
                    <!-- Balken -->
                    <div class="mt-4 h-4 bg-green-200 dark:bg-green-900/50 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-green-500 to-emerald-500 rounded-full transition-all duration-500" id="lb-bar" style="width: 5%"></div>
                    </div>
                </div>
            </div>
            
            <!-- Ersparnis Highlight -->
            <div class="relative bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl p-6 md:p-8 text-white text-center overflow-hidden">
                <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-center gap-2 mb-2">
                        <i class="fas fa-piggy-bank text-amber-300 text-2xl"></i>
                        <span class="text-lg font-medium text-white/90">Ihre monatliche Ersparnis</span>
                    </div>
                    <div class="text-5xl md:text-6xl font-extrabold text-amber-300" id="savings-value">1.801€</div>
                    <div class="text-white/80 mt-2">Das sind <span class="font-bold text-white" id="savings-percent">95%</span> weniger Kosten!</div>
                    <div class="mt-6 text-sm text-white/70">
                        <span class="font-semibold text-white" id="yearly-savings">21.612€</span> Ersparnis pro Jahr
                    </div>
                </div>
            </div>
            
            <!-- CTA -->
            <div class="text-center mt-8">
                <a href="/onboarding/" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-amber-400 to-orange-500 text-gray-900 font-bold text-lg rounded-full shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                    <span>Jetzt Kosten senken</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <!-- Disclaimer -->
            <div class="mt-8 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-xl border border-gray-200 dark:border-slate-600">
                <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Hinweis:</strong> Die angezeigten Kosten pro Lead (CPL) für bezahlte Werbung sind Durchschnittswerte basierend auf Branchendaten für den deutschen Markt (Quellen: WordStream, LocaliQ Benchmarks 2024/2025). 
                    Die tatsächlichen Kosten können je nach Zielgruppe, Region, Wettbewerb und Kampagnenqualität variieren. 
                    Leadbusiness garantiert keine bestimmte Anzahl von Empfehlungen – der Erfolg hängt von der Aktivierung Ihrer Bestandskunden ab.
                </p>
            </div>
        </div>
    </div>
    
    <script>
    (function() {
        'use strict';
        
        // CPL-Daten pro Branche (Durchschnitt aus Meta, Google, etc.)
        // Quellen: WordStream, LocaliQ Facebook/Google Ads Benchmarks 2024/2025
        var industryData = {
            zahnarzt: { cpl: 95, name: 'Zahnarzt' },
            handwerker: { cpl: 80, name: 'Handwerker' },
            coach: { cpl: 65, name: 'Coach' },
            fitness: { cpl: 50, name: 'Fitness' },
            friseur: { cpl: 45, name: 'Friseur' },
            onlineshop: { cpl: 35, name: 'Online-Shop' },
            restaurant: { cpl: 25, name: 'Restaurant' },
            marketing: { cpl: 85, name: 'Marketing' }
        };
        
        var currentIndustry = 'zahnarzt';
        var leadbusinessCost = 99; // Professional Plan
        
        function formatCurrency(value) {
            return value.toLocaleString('de-DE') + '€';
        }
        
        function calculate() {
            var leads = parseInt(document.getElementById('leads-slider').value);
            var cpl = industryData[currentIndustry].cpl;
            
            // Berechnungen
            var adsCost = leads * cpl;
            var savings = adsCost - leadbusinessCost;
            var savingsPercent = Math.round((savings / adsCost) * 100);
            var yearlySavings = savings * 12;
            
            // UI aktualisieren
            document.getElementById('leads-value').textContent = leads;
            document.getElementById('ads-cost').textContent = formatCurrency(adsCost);
            document.getElementById('cpl-value').textContent = formatCurrency(cpl);
            document.getElementById('lb-cost').textContent = formatCurrency(leadbusinessCost);
            document.getElementById('savings-value').textContent = formatCurrency(savings);
            document.getElementById('savings-percent').textContent = savingsPercent + '%';
            document.getElementById('yearly-savings').textContent = formatCurrency(yearlySavings);
            
            // Balken animieren
            var maxCost = Math.max(adsCost, leadbusinessCost);
            document.getElementById('ads-bar').style.width = '100%';
            document.getElementById('lb-bar').style.width = Math.max(2, (leadbusinessCost / adsCost) * 100) + '%';
        }
        
        function selectIndustry(industry) {
            currentIndustry = industry;
            
            // Buttons aktualisieren
            document.querySelectorAll('.industry-btn').forEach(function(btn) {
                btn.classList.remove('active', 'border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/30', 'text-blue-700', 'dark:text-blue-300');
                btn.classList.add('border-gray-200', 'dark:border-slate-600', 'bg-white', 'dark:bg-slate-700', 'text-gray-600', 'dark:text-gray-300');
            });
            
            var activeBtn = document.querySelector('.industry-btn[data-industry="' + industry + '"]');
            if (activeBtn) {
                activeBtn.classList.add('active', 'border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/30', 'text-blue-700', 'dark:text-blue-300');
                activeBtn.classList.remove('border-gray-200', 'dark:border-slate-600', 'bg-white', 'dark:bg-slate-700', 'text-gray-600', 'dark:text-gray-300');
            }
            
            calculate();
        }
        
        // Event Listeners
        document.getElementById('leads-slider').addEventListener('input', calculate);
        
        document.querySelectorAll('.industry-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                selectIndustry(this.dataset.industry);
            });
        });
        
        // Initial berechnen
        calculate();
    })();
    </script>
    
    <style>
        /* Slider Styling */
        #leads-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.4);
            transition: all 0.2s ease;
        }
        #leads-slider::-webkit-slider-thumb:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 15px rgba(37, 99, 235, 0.5);
        }
        #leads-slider::-moz-range-thumb {
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 50%;
            cursor: pointer;
            border: none;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.4);
        }
        
        .industry-btn.active {
            transform: scale(1.02);
        }
        .industry-btn:hover:not(.active) {
            transform: translateY(-2px);
        }
    </style>
</section>

<!-- How it Works Section -->
<section class="py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-blue-600 dark:text-blue-400 font-semibold uppercase tracking-wide">So funktioniert's</span>
            <h2 class="text-3xl md:text-4xl font-bold mt-3 dark:text-white">In 3 Schritten zum Empfehlungsprogramm</h2>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <div class="relative">
                <div class="bg-white dark:bg-slate-700 rounded-2xl p-8 shadow-lg card-hover h-full">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mb-6">1</div>
                    <h3 class="text-xl font-bold mb-4 dark:text-white">Onboarding ausfüllen</h3>
                    <p class="text-gray-600 dark:text-gray-300">Beantworten Sie 8 einfache Fragen zu Ihrem Unternehmen. Das dauert nur 5 Minuten.</p>
                </div>
                <div class="hidden md:block absolute top-1/2 -right-4 transform -translate-y-1/2 text-blue-300 dark:text-blue-600 text-4xl">→</div>
            </div>
            <div class="relative">
                <div class="bg-white dark:bg-slate-700 rounded-2xl p-8 shadow-lg card-hover h-full">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mb-6">2</div>
                    <h3 class="text-xl font-bold mb-4 dark:text-white">Automatische Einrichtung</h3>
                    <p class="text-gray-600 dark:text-gray-300">Wir erstellen automatisch Ihre Empfehlungsseite, E-Mails und Belohnungsstufen.</p>
                </div>
                <div class="hidden md:block absolute top-1/2 -right-4 transform -translate-y-1/2 text-blue-300 dark:text-blue-600 text-4xl">→</div>
            </div>
            <div>
                <div class="bg-white dark:bg-slate-700 rounded-2xl p-8 shadow-lg card-hover h-full">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mb-6">3</div>
                    <h3 class="text-xl font-bold mb-4 dark:text-white">Kunden einladen</h3>
                    <p class="text-gray-600 dark:text-gray-300">Teilen Sie Ihren Link mit Kunden. Ab jetzt läuft alles automatisch!</p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-12">
            <a href="/onboarding/" class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold text-lg rounded-full shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                <span>Jetzt in 5 Minuten starten</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-blue-600 dark:text-blue-400 font-semibold uppercase tracking-wide">Features</span>
            <h2 class="text-3xl md:text-4xl font-bold mt-3 dark:text-white">Alles, was Sie brauchen</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-4 max-w-2xl mx-auto">Ein komplettes Empfehlungsprogramm – fertig konfiguriert und einsatzbereit.</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="p-6 rounded-2xl border border-gray-200 dark:border-slate-600 hover:border-blue-500 hover:shadow-lg transition-all bg-white dark:bg-slate-800">
                <div class="feature-icon bg-blue-100 dark:bg-blue-900/30"><i class="fas fa-link text-blue-600"></i></div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">Persönliche Empfehlungslinks</h3>
                <p class="text-gray-600 dark:text-gray-300">Jeder Kunde bekommt einen einzigartigen Link zum Teilen.</p>
            </div>
            <div class="p-6 rounded-2xl border border-gray-200 dark:border-slate-600 hover:border-blue-500 hover:shadow-lg transition-all bg-white dark:bg-slate-800">
                <div class="feature-icon bg-green-100 dark:bg-green-900/30"><i class="fas fa-gift text-green-500"></i></div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">Automatische Belohnungen</h3>
                <p class="text-gray-600 dark:text-gray-300">Empfehler werden automatisch per E-Mail über ihre Belohnung informiert.</p>
            </div>
            <div class="p-6 rounded-2xl border border-gray-200 dark:border-slate-600 hover:border-blue-500 hover:shadow-lg transition-all bg-white dark:bg-slate-800">
                <div class="feature-icon bg-amber-100 dark:bg-amber-900/30"><i class="fas fa-trophy text-amber-500"></i></div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">Gamification</h3>
                <p class="text-gray-600 dark:text-gray-300">Leaderboards, Badges und Fortschrittsbalken motivieren zum Weiterempfehlen.</p>
            </div>
            <div class="p-6 rounded-2xl border border-gray-200 dark:border-slate-600 hover:border-blue-500 hover:shadow-lg transition-all bg-white dark:bg-slate-800">
                <div class="feature-icon bg-blue-100 dark:bg-blue-900/30"><i class="fas fa-share-alt text-blue-500"></i></div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">11 Share-Buttons</h3>
                <p class="text-gray-600 dark:text-gray-300">WhatsApp, Facebook, E-Mail, SMS und mehr – mit einem Klick teilen.</p>
            </div>
            <div class="p-6 rounded-2xl border border-gray-200 dark:border-slate-600 hover:border-blue-500 hover:shadow-lg transition-all bg-white dark:bg-slate-800">
                <div class="feature-icon bg-slate-100 dark:bg-slate-700"><i class="fas fa-palette text-slate-600 dark:text-slate-300"></i></div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">Branchen-Designs</h3>
                <p class="text-gray-600 dark:text-gray-300">Professionelle Hintergrundbilder passend zu Ihrer Branche.</p>
            </div>
            <div class="p-6 rounded-2xl border border-gray-200 dark:border-slate-600 hover:border-blue-500 hover:shadow-lg transition-all bg-white dark:bg-slate-800">
                <div class="feature-icon bg-red-100 dark:bg-red-900/30"><i class="fas fa-chart-line text-red-500"></i></div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">Live-Statistiken</h3>
                <p class="text-gray-600 dark:text-gray-300">Sehen Sie in Echtzeit, wer empfiehlt und wie erfolgreich Sie sind.</p>
            </div>
        </div>
        
        <div class="text-center mt-12">
            <a href="/funktionen" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline inline-flex items-center gap-2">
                Alle Funktionen ansehen <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Industries Section -->
<section class="py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-blue-600 dark:text-blue-400 font-semibold uppercase tracking-wide">Branchen</span>
            <h2 class="text-3xl md:text-4xl font-bold mt-3 dark:text-white">Perfekt für jede Branche</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-4 max-w-2xl mx-auto">Entdecken Sie, wie Unternehmen aus verschiedenen Branchen Leadbusiness nutzen</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            <?php
            $industriesList = [
                ['icon' => 'fa-tooth', 'name' => 'Zahnärzte', 'link' => '/branchen/zahnarzt', 'gradient' => 'from-blue-500 to-blue-600'],
                ['icon' => 'fa-cut', 'name' => 'Friseure', 'link' => '/branchen/friseur', 'gradient' => 'from-slate-500 to-slate-600'],
                ['icon' => 'fa-dumbbell', 'name' => 'Fitness', 'link' => '/branchen/fitness', 'gradient' => 'from-green-500 to-green-600'],
                ['icon' => 'fa-utensils', 'name' => 'Restaurants', 'link' => '/branchen/restaurant', 'gradient' => 'from-amber-500 to-orange-500'],
                ['icon' => 'fa-shopping-bag', 'name' => 'Online-Shops', 'link' => '/branchen/onlineshop', 'gradient' => 'from-blue-600 to-blue-700'],
                ['icon' => 'fa-lightbulb', 'name' => 'Coaches', 'link' => '/branchen/coach', 'gradient' => 'from-amber-400 to-amber-500'],
                ['icon' => 'fa-hammer', 'name' => 'Handwerker', 'link' => '/branchen/handwerker', 'gradient' => 'from-slate-600 to-slate-700'],
                ['icon' => 'fa-bullhorn', 'name' => 'Online-Marketing', 'link' => '/branchen/onlinemarketing', 'gradient' => 'from-blue-700 to-slate-700'],
            ];
            foreach ($industriesList as $industry):
            ?>
            <a href="<?= $industry['link'] ?>" class="group block">
                <div class="bg-white dark:bg-slate-700 rounded-2xl p-4 md:p-6 text-center shadow-sm hover:shadow-xl transition-all duration-300 h-full border-2 border-transparent hover:border-blue-500 dark:hover:border-blue-400">
                    <div class="w-14 h-14 md:w-16 md:h-16 mx-auto mb-3 md:mb-4 rounded-2xl bg-gradient-to-br <?= $industry['gradient'] ?> flex items-center justify-center text-white transform group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <i class="fas <?= $industry['icon'] ?> text-xl md:text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm md:text-base group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors"><?= $industry['name'] ?></h3>
                    <span class="inline-flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500 mt-2 opacity-0 group-hover:opacity-100 transition-opacity">Mehr erfahren <i class="fas fa-arrow-right"></i></span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-10">
            <a href="/branchen" class="inline-flex items-center gap-2 text-blue-600 dark:text-blue-400 font-semibold hover:underline"><span>Alle Branchen ansehen</span><i class="fas fa-arrow-right"></i></a>
            <p class="text-gray-500 dark:text-gray-400 mt-3 text-sm">Und viele weitere: Therapeuten, SaaS, Newsletter, Agenturen, Ärzte...</p>
        </div>
    </div>
</section>

<!-- Pricing Preview Section -->
<section class="py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-blue-600 dark:text-blue-400 font-semibold uppercase tracking-wide">Preise</span>
            <h2 class="text-3xl md:text-4xl font-bold mt-3 dark:text-white">Einfach & transparent</h2>
        </div>
        
        <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border-2 border-gray-200 dark:border-slate-600 p-8 hover:border-blue-500 transition-colors">
                <h3 class="text-2xl font-bold mb-2 dark:text-white">Starter</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">Für den Einstieg</p>
                <div class="mb-6"><span class="text-4xl font-extrabold dark:text-white">49€</span><span class="text-gray-500 dark:text-gray-400">/Monat</span></div>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center gap-2 text-gray-600 dark:text-gray-300"><i class="fas fa-check text-green-500"></i>Bis 200 Empfehler</li>
                    <li class="flex items-center gap-2 text-gray-600 dark:text-gray-300"><i class="fas fa-check text-green-500"></i>3 Belohnungsstufen</li>
                    <li class="flex items-center gap-2 text-gray-600 dark:text-gray-300"><i class="fas fa-check text-green-500"></i>Eigene Subdomain</li>
                    <li class="flex items-center gap-2 text-gray-600 dark:text-gray-300"><i class="fas fa-check text-green-500"></i>E-Mail-Support</li>
                </ul>
                <a href="/onboarding/?plan=starter" class="block w-full py-3 px-6 text-center rounded-full border-2 border-blue-600 text-blue-600 dark:border-blue-400 dark:text-blue-400 font-semibold hover:bg-blue-600 hover:text-white dark:hover:bg-blue-400 dark:hover:text-slate-900 transition-colors">Starter wählen</a>
            </div>
            
            <div class="bg-white dark:bg-slate-800 rounded-2xl border-2 border-blue-600 p-8 shadow-xl relative">
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-blue-600 text-white text-sm font-semibold px-4 py-1 rounded-full">Beliebt</div>
                <h3 class="text-2xl font-bold mb-2 dark:text-white">Professional</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">Für wachsende Unternehmen</p>
                <div class="mb-6"><span class="text-4xl font-extrabold dark:text-white">99€</span><span class="text-gray-500 dark:text-gray-400">/Monat</span></div>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center gap-2 text-gray-600 dark:text-gray-300"><i class="fas fa-check text-green-500"></i>Bis 5.000 Empfehler</li>
                    <li class="flex items-center gap-2 text-gray-600 dark:text-gray-300"><i class="fas fa-check text-green-500"></i>5 Belohnungsstufen</li>
                    <li class="flex items-center gap-2 text-gray-600 dark:text-gray-300"><i class="fas fa-check text-green-500"></i>Mehrere Kampagnen</li>
                    <li class="flex items-center gap-2 text-gray-600 dark:text-gray-300"><i class="fas fa-check text-green-500"></i>Lead-Export & API</li>
                    <li class="flex items-center gap-2 text-gray-600 dark:text-gray-300"><i class="fas fa-check text-green-500"></i>Prioritäts-Support</li>
                </ul>
                <a href="/onboarding/?plan=professional" class="block w-full py-3 px-6 text-center rounded-full bg-blue-600 text-white font-semibold hover:bg-blue-700 transition-colors shadow-lg">Professional wählen</a>
            </div>
        </div>
        
        <p class="text-center text-gray-500 dark:text-gray-400 mt-8"><i class="fas fa-info-circle mr-1"></i>Einmalige Einrichtungsgebühr: 499€ · 7 Tage kostenlos testen</p>
        <div class="text-center mt-6"><a href="/preise" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline">Alle Details vergleichen →</a></div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-blue-600 dark:text-blue-400 font-semibold uppercase tracking-wide">Kundenstimmen</span>
            <h2 class="text-3xl md:text-4xl font-bold mt-3 dark:text-white">Das sagen unsere Kunden</h2>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <div class="testimonial-card bg-white dark:bg-slate-700 rounded-2xl p-8 shadow-lg">
                <div class="flex items-center gap-1 text-amber-400 mb-4"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                <p class="text-gray-600 dark:text-gray-300 mb-6">"In den ersten 3 Monaten haben wir 47 Neukunden durch Empfehlungen gewonnen. Das System läuft komplett automatisch – ich muss nichts tun."</p>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold">TM</div>
                    <div><div class="font-semibold dark:text-white">Dr. Thomas Müller</div><div class="text-sm text-gray-500 dark:text-gray-400">Zahnarztpraxis München</div></div>
                </div>
            </div>
            <div class="testimonial-card bg-white dark:bg-slate-700 rounded-2xl p-8 shadow-lg">
                <div class="flex items-center gap-1 text-amber-400 mb-4"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                <p class="text-gray-600 dark:text-gray-300 mb-6">"Meine Kunden lieben das Punktesystem! Sie teilen ihren Link aktiv und freuen sich über die Belohnungen. Einfach genial."</p>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-slate-100 dark:bg-slate-600 rounded-full flex items-center justify-center text-slate-600 dark:text-slate-300 font-bold">SB</div>
                    <div><div class="font-semibold dark:text-white">Sandra Becker</div><div class="text-sm text-gray-500 dark:text-gray-400">Friseursalon Style & Cut</div></div>
                </div>
            </div>
            <div class="testimonial-card bg-white dark:bg-slate-700 rounded-2xl p-8 shadow-lg">
                <div class="flex items-center gap-1 text-amber-400 mb-4"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                <p class="text-gray-600 dark:text-gray-300 mb-6">"Als Online-Coach war ich skeptisch, aber die Ergebnisse sprechen für sich: 32% meiner Neukunden kommen jetzt über Empfehlungen."</p>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center text-green-500 font-bold">MK</div>
                    <div><div class="font-semibold dark:text-white">Michael Klein</div><div class="text-sm text-gray-500 dark:text-gray-400">Business Coach</div></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-20 bg-gradient-to-br from-blue-600 to-blue-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div><div class="text-4xl md:text-5xl font-extrabold mb-2">500+</div><div class="text-white/80">Unternehmen</div></div>
            <div><div class="text-4xl md:text-5xl font-extrabold mb-2">50.000+</div><div class="text-white/80">Empfehler</div></div>
            <div><div class="text-4xl md:text-5xl font-extrabold mb-2">18.000+</div><div class="text-white/80">Conversions</div></div>
            <div><div class="text-4xl md:text-5xl font-extrabold mb-2">36%</div><div class="text-white/80">Ø Conversion-Rate</div></div>
        </div>
    </div>
</section>

<!-- FAQ Preview Section -->
<section class="py-20 bg-white dark:bg-slate-900">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-blue-600 dark:text-blue-400 font-semibold uppercase tracking-wide">FAQ</span>
            <h2 class="text-3xl md:text-4xl font-bold mt-3 dark:text-white">Häufige Fragen</h2>
        </div>
        
        <div class="space-y-4">
            <div class="faq-item border dark:border-slate-600 rounded-xl p-4 bg-white dark:bg-slate-800">
                <div class="faq-question dark:text-white"><span>Brauche ich technisches Wissen?</span></div>
                <div class="faq-answer text-gray-600 dark:text-gray-300"><p>Nein, überhaupt nicht! Sie füllen nur unser Onboarding-Formular aus – alles andere erledigen wir automatisch. Keine Installation, kein Code, keine Technik.</p></div>
            </div>
            <div class="faq-item border dark:border-slate-600 rounded-xl p-4 bg-white dark:bg-slate-800">
                <div class="faq-question dark:text-white"><span>Wie lange dauert die Einrichtung?</span></div>
                <div class="faq-answer text-gray-600 dark:text-gray-300"><p>Das Onboarding dauert etwa 5 Minuten. Danach ist Ihr Empfehlungsprogramm sofort einsatzbereit – inklusive eigener Subdomain, E-Mail-System und Belohnungsstufen.</p></div>
            </div>
            <div class="faq-item border dark:border-slate-600 rounded-xl p-4 bg-white dark:bg-slate-800">
                <div class="faq-question dark:text-white"><span>Ist Leadbusiness DSGVO-konform?</span></div>
                <div class="faq-answer text-gray-600 dark:text-gray-300"><p>Ja, zu 100%! Alle Daten werden in Deutschland gehostet, wir nutzen Double-Opt-In und stellen Ihnen alle nötigen Rechtstexte zur Verfügung.</p></div>
            </div>
        </div>
        
        <div class="text-center mt-8"><a href="/faq" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline">Alle Fragen ansehen →</a></div>
    </div>
</section>

<!-- Final CTA Section -->
<section class="cta-section py-20 bg-gradient-to-br from-blue-600 to-blue-800 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <h2 class="text-3xl md:text-5xl font-extrabold mb-6">Bereit für mehr Kunden durch Empfehlungen?</h2>
        <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">Starten Sie noch heute und verwandeln Sie zufriedene Kunden in Ihre besten Botschafter.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/onboarding/" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-amber-400 to-orange-500 text-gray-900 font-bold text-lg rounded-full shadow-[0_0_30px_rgba(251,191,36,0.4)] hover:shadow-[0_0_50px_rgba(251,191,36,0.6)] hover:scale-105 transition-all duration-300">
                <span>Jetzt 7 Tage kostenlos testen</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <p class="text-white/70 mt-6 text-sm">Keine Kreditkarte erforderlich · Einrichtung in 5 Minuten · DSGVO-konform</p>
    </div>
</section>

<?php require_once __DIR__ . '/../templates/marketing/footer.php'; ?>
