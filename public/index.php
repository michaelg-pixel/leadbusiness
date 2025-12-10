<?php
/**
 * Leadbusiness - Landingpage
 * Vollautomatisches Empfehlungsprogramm für Unternehmen
 * Auto-Deploy Test: OK
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
<section class="hero-slider-section min-h-screen flex items-center relative overflow-hidden">
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
                
                <!-- Desktop: Slider Hooks -->
                <div class="hidden lg:block">
                    <?php foreach ($heroSlides as $index => $slide): ?>
                    <h1 class="hero-hook-slide text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-6 transition-all duration-700 <?= $index === 0 ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4 absolute' ?>" data-slide="<?= $index ?>">
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
                    <a href="/onboarding" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-amber-400 to-orange-500 text-gray-900 font-bold text-lg rounded-full shadow-[0_0_30px_rgba(251,191,36,0.4)] hover:shadow-[0_0_50px_rgba(251,191,36,0.6)] hover:scale-105 transition-all duration-300">
                        <span>Jetzt kostenlos starten</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="#demo" class="btn-ghost btn-large inline-flex items-center justify-center gap-2">
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
                <div class="hidden lg:flex items-center gap-2 mt-8">
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
            <div class="relative hidden lg:block">
                <?php foreach ($heroSlides as $index => $slide): ?>
                <div class="hero-card-slide transition-all duration-700 <?= $index === 0 ? 'opacity-100 scale-100' : 'opacity-0 scale-95 absolute top-0 left-0 right-0' ?>" data-slide="<?= $index ?>">
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
                <div class="absolute -top-4 -right-4 w-20 h-20 bg-amber-400 rounded-2xl flex items-center justify-center text-3xl floating shadow-lg">
                    🎉
                </div>
                <div class="absolute -bottom-4 -left-4 w-16 h-16 bg-green-400 rounded-2xl flex items-center justify-center text-2xl floating shadow-lg" style="animation-delay: 1s;">
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

<!-- Hero Slider JavaScript (nur Desktop, optimiert) -->
<script>
(function() {
    // Nur auf Desktop aktivieren
    if (window.innerWidth < 1024) return;
    
    const totalSlides = <?= count($heroSlides) ?>;
    let currentSlide = 0;
    let sliderInterval;
    let isPaused = false;
    
    const bgSlides = document.querySelectorAll('.hero-bg-slide');
    const hookSlides = document.querySelectorAll('.hero-hook-slide');
    const cardSlides = document.querySelectorAll('.hero-card-slide');
    const dots = document.querySelectorAll('.hero-slider-dot');
    
    function showSlide(index) {
        // Backgrounds
        bgSlides.forEach((slide, i) => {
            slide.classList.toggle('opacity-100', i === index);
            slide.classList.toggle('opacity-0', i !== index);
        });
        
        // Hooks
        hookSlides.forEach((slide, i) => {
            if (i === index) {
                slide.classList.remove('opacity-0', 'translate-y-4', 'absolute');
                slide.classList.add('opacity-100', 'translate-y-0');
            } else {
                slide.classList.add('opacity-0', 'translate-y-4', 'absolute');
                slide.classList.remove('opacity-100', 'translate-y-0');
            }
        });
        
        // Cards
        cardSlides.forEach((slide, i) => {
            if (i === index) {
                slide.classList.remove('opacity-0', 'scale-95', 'absolute');
                slide.classList.add('opacity-100', 'scale-100');
            } else {
                slide.classList.add('opacity-0', 'scale-95', 'absolute');
                slide.classList.remove('opacity-100', 'scale-100');
            }
        });
        
        // Dots
        dots.forEach((dot, i) => {
            if (i === index) {
                dot.classList.add('bg-white', 'w-8');
                dot.classList.remove('bg-white/40');
            } else {
                dot.classList.remove('bg-white', 'w-8');
                dot.classList.add('bg-white/40');
            }
        });
        
        currentSlide = index;
    }
    
    function nextSlide() {
        if (isPaused) return;
        showSlide((currentSlide + 1) % totalSlides);
    }
    
    function startSlider() {
        sliderInterval = setInterval(nextSlide, 6000);
    }
    
    function resetInterval() {
        clearInterval(sliderInterval);
        startSlider();
    }
    
    // Dot-Klick Handler
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            showSlide(index);
            resetInterval();
        });
    });
    
    // Pause bei Hover
    const heroSection = document.querySelector('.hero-slider-section');
    if (heroSection) {
        heroSection.addEventListener('mouseenter', () => { isPaused = true; });
        heroSection.addEventListener('mouseleave', () => { isPaused = false; });
    }
    
    // Visibility API - pausiere wenn Tab nicht sichtbar
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            clearInterval(sliderInterval);
        } else {
            startSlider();
        }
    });
    
    // Start
    startSlider();
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
                // Erweiterte Liste aller Zielgruppen
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
                
                // Items zweimal für nahtlose Schleife
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
        .industry-ticker-wrapper {
            width: 100%;
            overflow: hidden;
        }
        
        .industry-ticker {
            display: flex;
            width: 100%;
        }
        
        .industry-ticker-track {
            display: flex;
            gap: 2rem;
            animation: ticker-scroll 60s linear infinite;
            will-change: transform;
        }
        
        .industry-ticker:hover .industry-ticker-track {
            animation-play-state: paused;
        }
        
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
        
        .dark .industry-ticker-item {
            background: #334155;
            color: #9ca3af;
        }
        
        .industry-ticker-item:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            color: #2563eb;
        }
        
        .dark .industry-ticker-item:hover {
            color: #60a5fa;
        }
        
        .industry-ticker-item i {
            font-size: 1.25rem;
            color: #2563eb;
        }
        
        .dark .industry-ticker-item i {
            color: #60a5fa;
        }
        
        @keyframes ticker-scroll {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%);
            }
        }
        
        /* Responsive Anpassungen */
        @media (max-width: 768px) {
            .industry-ticker-track {
                animation-duration: 45s;
                gap: 1rem;
            }
            
            .industry-ticker-item {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }
            
            .industry-ticker-item i {
                font-size: 1rem;
            }
        }
    </style>
</section>

<!-- Problem/Solution Section -->
<section class="py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            
            <!-- Problem -->
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
            
            <!-- Solution -->
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

<!-- How it Works Section -->
<section class="py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-blue-600 dark:text-blue-400 font-semibold uppercase tracking-wide">So funktioniert's</span>
            <h2 class="text-3xl md:text-4xl font-bold mt-3 dark:text-white">In 3 Schritten zum Empfehlungsprogramm</h2>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Step 1 -->
            <div class="relative">
                <div class="bg-white dark:bg-slate-700 rounded-2xl p-8 shadow-lg card-hover h-full">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mb-6">
                        1
                    </div>
                    <h3 class="text-xl font-bold mb-4 dark:text-white">Onboarding ausfüllen</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Beantworten Sie 8 einfache Fragen zu Ihrem Unternehmen. 
                        Das dauert nur 5 Minuten.
                    </p>
                </div>
                <div class="hidden md:block absolute top-1/2 -right-4 transform -translate-y-1/2 text-blue-300 dark:text-blue-600 text-4xl">
                    →
                </div>
            </div>
            
            <!-- Step 2 -->
            <div class="relative">
                <div class="bg-white dark:bg-slate-700 rounded-2xl p-8 shadow-lg card-hover h-full">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mb-6">
                        2
                    </div>
                    <h3 class="text-xl font-bold mb-4 dark:text-white">Automatische Einrichtung</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Wir erstellen automatisch Ihre Empfehlungsseite, 
                        E-Mails und Belohnungsstufen.
                    </p>
                </div>
                <div class="hidden md:block absolute top-1/2 -right-4 transform -translate-y-1/2 text-blue-300 dark:text-blue-600 text-4xl">
                    →
                </div>
            </div>
            
            <!-- Step 3 -->
            <div>
                <div class="bg-white dark:bg-slate-700 rounded-2xl p-8 shadow-lg card-hover h-full">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mb-6">
                        3
                    </div>
                    <h3 class="text-xl font-bold mb-4 dark:text-white">Kunden einladen</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Teilen Sie Ihren Link mit Kunden. 
                        Ab jetzt läuft alles automatisch!
                    </p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-12">
            <a href="/onboarding" class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold text-lg rounded-full shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
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
            <p class="text-gray-600 dark:text-gray-400 mt-4 max-w-2xl mx-auto">
                Ein komplettes Empfehlungsprogramm – fertig konfiguriert und einsatzbereit.
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="p-6 rounded-2xl border border-gray-200 dark:border-slate-600 hover:border-blue-500 hover:shadow-lg transition-all bg-white dark:bg-slate-800">
                <div class="feature-icon bg-blue-100 dark:bg-blue-900/30">
                    <i class="fas fa-link text-blue-600"></i>
                </div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">Persönliche Empfehlungslinks</h3>
                <p class="text-gray-600 dark:text-gray-300">Jeder Kunde bekommt einen einzigartigen Link zum Teilen.</p>
            </div>
            
            <!-- Feature 2 -->
            <div class="p-6 rounded-2xl border border-gray-200 dark:border-slate-600 hover:border-blue-500 hover:shadow-lg transition-all bg-white dark:bg-slate-800">
                <div class="feature-icon bg-green-100 dark:bg-green-900/30">
                    <i class="fas fa-gift text-green-500"></i>
                </div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">Automatische Belohnungen</h3>
                <p class="text-gray-600 dark:text-gray-300">Empfehler werden automatisch per E-Mail über ihre Belohnung informiert.</p>
            </div>
            
            <!-- Feature 3 -->
            <div class="p-6 rounded-2xl border border-gray-200 dark:border-slate-600 hover:border-blue-500 hover:shadow-lg transition-all bg-white dark:bg-slate-800">
                <div class="feature-icon bg-amber-100 dark:bg-amber-900/30">
                    <i class="fas fa-trophy text-amber-500"></i>
                </div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">Gamification</h3>
                <p class="text-gray-600 dark:text-gray-300">Leaderboards, Badges und Fortschrittsbalken motivieren zum Weiterempfehlen.</p>
            </div>
            
            <!-- Feature 4 -->
            <div class="p-6 rounded-2xl border border-gray-200 dark:border-slate-600 hover:border-blue-500 hover:shadow-lg transition-all bg-white dark:bg-slate-800">
                <div class="feature-icon bg-blue-100 dark:bg-blue-900/30">
                    <i class="fas fa-share-alt text-blue-500"></i>
                </div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">11 Share-Buttons</h3>
                <p class="text-gray-600 dark:text-gray-300">WhatsApp, Facebook, E-Mail, SMS und mehr – mit einem Klick teilen.</p>
            </div>
            
            <!-- Feature 5 -->
            <div class="p-6 rounded-2xl border border-gray-200 dark:border-slate-600 hover:border-blue-500 hover:shadow-lg transition-all bg-white dark:bg-slate-800">
                <div class="feature-icon bg-slate-100 dark:bg-slate-700">
                    <i class="fas fa-palette text-slate-600 dark:text-slate-300"></i>
                </div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">Branchen-Designs</h3>
                <p class="text-gray-600 dark:text-gray-300">Professionelle Hintergrundbilder passend zu Ihrer Branche.</p>
            </div>
            
            <!-- Feature 6 -->
            <div class="p-6 rounded-2xl border border-gray-200 dark:border-slate-600 hover:border-blue-500 hover:shadow-lg transition-all bg-white dark:bg-slate-800">
                <div class="feature-icon bg-red-100 dark:bg-red-900/30">
                    <i class="fas fa-chart-line text-red-500"></i>
                </div>
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
            <p class="text-gray-600 dark:text-gray-400 mt-4 max-w-2xl mx-auto">
                Entdecken Sie, wie Unternehmen aus verschiedenen Branchen Leadbusiness nutzen
            </p>
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
                    <span class="inline-flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500 mt-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        Mehr erfahren <i class="fas fa-arrow-right"></i>
                    </span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-10">
            <a href="/branchen" class="inline-flex items-center gap-2 text-blue-600 dark:text-blue-400 font-semibold hover:underline">
                <span>Alle Branchen ansehen</span>
                <i class="fas fa-arrow-right"></i>
            </a>
            <p class="text-gray-500 dark:text-gray-400 mt-3 text-sm">
                Und viele weitere: Therapeuten, SaaS, Newsletter, Agenturen, Ärzte...
            </p>
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
            <!-- Starter Plan -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border-2 border-gray-200 dark:border-slate-600 p-8 hover:border-blue-500 transition-colors">
                <h3 class="text-2xl font-bold mb-2 dark:text-white">Starter</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">Für den Einstieg</p>
                
                <div class="mb-6">
                    <span class="text-4xl font-extrabold dark:text-white">49€</span>
                    <span class="text-gray-500 dark:text-gray-400">/Monat</span>
                </div>
                
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                        <i class="fas fa-check text-green-500"></i>
                        Bis 200 Empfehler
                    </li>
                    <li class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                        <i class="fas fa-check text-green-500"></i>
                        3 Belohnungsstufen
                    </li>
                    <li class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                        <i class="fas fa-check text-green-500"></i>
                        Eigene Subdomain
                    </li>
                    <li class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                        <i class="fas fa-check text-green-500"></i>
                        E-Mail-Support
                    </li>
                </ul>
                
                <a href="/onboarding?plan=starter" class="block w-full py-3 px-6 text-center rounded-full border-2 border-blue-600 text-blue-600 dark:border-blue-400 dark:text-blue-400 font-semibold hover:bg-blue-600 hover:text-white dark:hover:bg-blue-400 dark:hover:text-slate-900 transition-colors">
                    Starter wählen
                </a>
            </div>
            
            <!-- Professional Plan -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border-2 border-blue-600 p-8 shadow-xl relative">
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-blue-600 text-white text-sm font-semibold px-4 py-1 rounded-full">
                    Beliebt
                </div>
                <h3 class="text-2xl font-bold mb-2 dark:text-white">Professional</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">Für wachsende Unternehmen</p>
                
                <div class="mb-6">
                    <span class="text-4xl font-extrabold dark:text-white">99€</span>
                    <span class="text-gray-500 dark:text-gray-400">/Monat</span>
                </div>
                
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                        <i class="fas fa-check text-green-500"></i>
                        Bis 5.000 Empfehler
                    </li>
                    <li class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                        <i class="fas fa-check text-green-500"></i>
                        5 Belohnungsstufen
                    </li>
                    <li class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                        <i class="fas fa-check text-green-500"></i>
                        Mehrere Kampagnen
                    </li>
                    <li class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                        <i class="fas fa-check text-green-500"></i>
                        Lead-Export & API
                    </li>
                    <li class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                        <i class="fas fa-check text-green-500"></i>
                        Prioritäts-Support
                    </li>
                </ul>
                
                <a href="/onboarding?plan=professional" class="block w-full py-3 px-6 text-center rounded-full bg-blue-600 text-white font-semibold hover:bg-blue-700 transition-colors shadow-lg">
                    Professional wählen
                </a>
            </div>
        </div>
        
        <p class="text-center text-gray-500 dark:text-gray-400 mt-8">
            <i class="fas fa-info-circle mr-1"></i>
            Einmalige Einrichtungsgebühr: 499€ · 7 Tage kostenlos testen
        </p>
        
        <div class="text-center mt-6">
            <a href="/preise" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline">
                Alle Details vergleichen →
            </a>
        </div>
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
            <!-- Testimonial 1 -->
            <div class="testimonial-card bg-white dark:bg-slate-700 rounded-2xl p-8 shadow-lg">
                <div class="flex items-center gap-1 text-amber-400 mb-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="text-gray-600 dark:text-gray-300 mb-6">
                    "In den ersten 3 Monaten haben wir 47 Neukunden durch Empfehlungen gewonnen. 
                    Das System läuft komplett automatisch – ich muss nichts tun."
                </p>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold">
                        TM
                    </div>
                    <div>
                        <div class="font-semibold dark:text-white">Dr. Thomas Müller</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Zahnarztpraxis München</div>
                    </div>
                </div>
            </div>
            
            <!-- Testimonial 2 -->
            <div class="testimonial-card bg-white dark:bg-slate-700 rounded-2xl p-8 shadow-lg">
                <div class="flex items-center gap-1 text-amber-400 mb-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="text-gray-600 dark:text-gray-300 mb-6">
                    "Meine Kunden lieben das Punktesystem! Sie teilen ihren Link aktiv und 
                    freuen sich über die Belohnungen. Einfach genial."
                </p>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-slate-100 dark:bg-slate-600 rounded-full flex items-center justify-center text-slate-600 dark:text-slate-300 font-bold">
                        SB
                    </div>
                    <div>
                        <div class="font-semibold dark:text-white">Sandra Becker</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Friseursalon Style & Cut</div>
                    </div>
                </div>
            </div>
            
            <!-- Testimonial 3 -->
            <div class="testimonial-card bg-white dark:bg-slate-700 rounded-2xl p-8 shadow-lg">
                <div class="flex items-center gap-1 text-amber-400 mb-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="text-gray-600 dark:text-gray-300 mb-6">
                    "Als Online-Coach war ich skeptisch, aber die Ergebnisse sprechen für sich: 
                    32% meiner Neukunden kommen jetzt über Empfehlungen."
                </p>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center text-green-500 font-bold">
                        MK
                    </div>
                    <div>
                        <div class="font-semibold dark:text-white">Michael Klein</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Business Coach</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-20 bg-gradient-to-br from-blue-600 to-blue-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl md:text-5xl font-extrabold mb-2" data-count="500">500+</div>
                <div class="text-white/80">Unternehmen</div>
            </div>
            <div>
                <div class="text-4xl md:text-5xl font-extrabold mb-2" data-count="50000">50.000+</div>
                <div class="text-white/80">Empfehler</div>
            </div>
            <div>
                <div class="text-4xl md:text-5xl font-extrabold mb-2" data-count="18000">18.000+</div>
                <div class="text-white/80">Conversions</div>
            </div>
            <div>
                <div class="text-4xl md:text-5xl font-extrabold mb-2">36%</div>
                <div class="text-white/80">Ø Conversion-Rate</div>
            </div>
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
            <!-- FAQ Item 1 -->
            <div class="faq-item border dark:border-slate-600 rounded-xl p-4 bg-white dark:bg-slate-800">
                <div class="faq-question dark:text-white">
                    <span>Brauche ich technisches Wissen?</span>
                </div>
                <div class="faq-answer text-gray-600 dark:text-gray-300">
                    <p>Nein, überhaupt nicht! Sie füllen nur unser Onboarding-Formular aus – alles andere erledigen wir automatisch. Keine Installation, kein Code, keine Technik.</p>
                </div>
            </div>
            
            <!-- FAQ Item 2 -->
            <div class="faq-item border dark:border-slate-600 rounded-xl p-4 bg-white dark:bg-slate-800">
                <div class="faq-question dark:text-white">
                    <span>Wie lange dauert die Einrichtung?</span>
                </div>
                <div class="faq-answer text-gray-600 dark:text-gray-300">
                    <p>Das Onboarding dauert etwa 5 Minuten. Danach ist Ihr Empfehlungsprogramm sofort einsatzbereit – inklusive eigener Subdomain, E-Mail-System und Belohnungsstufen.</p>
                </div>
            </div>
            
            <!-- FAQ Item 3 -->
            <div class="faq-item border dark:border-slate-600 rounded-xl p-4 bg-white dark:bg-slate-800">
                <div class="faq-question dark:text-white">
                    <span>Ist Leadbusiness DSGVO-konform?</span>
                </div>
                <div class="faq-answer text-gray-600 dark:text-gray-300">
                    <p>Ja, zu 100%! Alle Daten werden in Deutschland gehostet, wir nutzen Double-Opt-In und stellen Ihnen alle nötigen Rechtstexte zur Verfügung.</p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-8">
            <a href="/faq" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline">
                Alle Fragen ansehen →
            </a>
        </div>
    </div>
</section>

<!-- Final CTA Section -->
<section class="cta-section py-20 bg-gradient-to-br from-blue-600 to-blue-800 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <h2 class="text-3xl md:text-5xl font-extrabold mb-6">
            Bereit für mehr Kunden durch Empfehlungen?
        </h2>
        <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
            Starten Sie noch heute und verwandeln Sie zufriedene Kunden in Ihre besten Botschafter.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/onboarding" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-amber-400 to-orange-500 text-gray-900 font-bold text-lg rounded-full shadow-[0_0_30px_rgba(251,191,36,0.4)] hover:shadow-[0_0_50px_rgba(251,191,36,0.6)] hover:scale-105 transition-all duration-300">
                <span>Jetzt 7 Tage kostenlos testen</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <p class="text-white/70 mt-6 text-sm">
            Keine Kreditkarte erforderlich · Einrichtung in 5 Minuten · DSGVO-konform
        </p>
    </div>
</section>

<?php
require_once __DIR__ . '/../templates/marketing/footer.php';
?>
