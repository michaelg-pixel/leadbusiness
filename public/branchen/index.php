<?php
/**
 * Branchen-Übersicht
 * Zeigt alle verfügbaren Branchen-Landingpages
 */

$pageTitle = 'Empfehlungsprogramm nach Branche';
$metaDescription = 'Leadbusiness für Ihre Branche: Zahnärzte, Friseure, Fitnessstudios, Restaurants, Coaches, Online-Shops und mehr. Finden Sie die perfekte Lösung für Ihr Empfehlungsprogramm.';
$currentPage = 'branchen';

require_once __DIR__ . '/../../templates/marketing/header.php';

$branchen = [
    [
        'name' => 'Zahnärzte',
        'slug' => 'zahnarzt',
        'icon' => 'fa-tooth',
        'color' => 'blue',
        'bgFrom' => 'from-blue-500',
        'bgTo' => 'to-blue-600',
        'description' => 'Patienten werben Patienten – mit Belohnungen wie Zahnreinigung oder Bleaching.',
    ],
    [
        'name' => 'Friseure',
        'slug' => 'friseur',
        'icon' => 'fa-cut',
        'color' => 'pink',
        'bgFrom' => 'from-pink-500',
        'bgTo' => 'to-purple-600',
        'description' => 'Zufriedene Kunden teilen ihren neuen Look und werden mit Pflegeprodukten belohnt.',
    ],
    [
        'name' => 'Fitnessstudios',
        'slug' => 'fitness',
        'icon' => 'fa-dumbbell',
        'color' => 'orange',
        'bgFrom' => 'from-orange-500',
        'bgTo' => 'to-red-500',
        'description' => 'Mitglieder werben Trainingspartner – Gratismonate oder Personal Training als Belohnung.',
    ],
    [
        'name' => 'Restaurants',
        'slug' => 'restaurant',
        'icon' => 'fa-utensils',
        'color' => 'amber',
        'bgFrom' => 'from-amber-500',
        'bgTo' => 'to-orange-500',
        'description' => 'Gäste empfehlen Ihr Restaurant weiter und erhalten Gratis-Desserts oder Dinner-Gutscheine.',
    ],
    [
        'name' => 'Coaches & Berater',
        'slug' => 'coach',
        'icon' => 'fa-lightbulb',
        'color' => 'purple',
        'bgFrom' => 'from-purple-500',
        'bgTo' => 'to-indigo-600',
        'description' => 'Zufriedene Klienten empfehlen Sie weiter – Bonus-Calls oder E-Books als Belohnung.',
    ],
    [
        'name' => 'Online-Shops',
        'slug' => 'onlineshop',
        'icon' => 'fa-shopping-cart',
        'color' => 'green',
        'bgFrom' => 'from-green-500',
        'bgTo' => 'to-emerald-600',
        'description' => 'Kunden empfehlen Ihren Shop – Rabatte, Gutscheine oder Gratis-Versand als Belohnung.',
    ],
    [
        'name' => 'Online-Kurse & Infoprodukte',
        'slug' => 'onlinemarketing',
        'icon' => 'fa-graduation-cap',
        'color' => 'indigo',
        'bgFrom' => 'from-indigo-500',
        'bgTo' => 'to-violet-600',
        'description' => 'Kursteilnehmer empfehlen Sie – Bonus-Module oder VIP-Zugang als Belohnung.',
    ],
];
?>

<!-- Hero Section -->
<section class="py-16 md:py-24 bg-gradient-to-br from-gray-50 to-white dark:from-slate-900 dark:to-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto">
            <span class="text-primary-500 font-semibold uppercase tracking-wide">Branchen</span>
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-gray-900 dark:text-white mt-3 mb-6">
                Empfehlungsprogramm für <span class="gradient-text">Ihre Branche</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-600 dark:text-gray-400">
                Leadbusiness funktioniert für jedes Unternehmen. Finden Sie die perfekte Lösung 
                mit branchenspezifischen Belohnungen und Vorlagen.
            </p>
        </div>
    </div>
</section>

<!-- Branchen Grid -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <?php foreach ($branchen as $branche): ?>
            <a href="/branchen/<?= $branche['slug'] ?>" 
               class="group bg-white dark:bg-slate-800 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-slate-700">
                
                <!-- Header mit Gradient -->
                <div class="h-32 bg-gradient-to-br <?= $branche['bgFrom'] ?> <?= $branche['bgTo'] ?> relative flex items-center justify-center">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                        <i class="fas <?= $branche['icon'] ?> text-white text-3xl"></i>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-black/20 to-transparent"></div>
                </div>
                
                <!-- Content -->
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                        <?= $branche['name'] ?>
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        <?= $branche['description'] ?>
                    </p>
                    <div class="flex items-center text-primary-500 font-medium text-sm group-hover:text-primary-600 transition-colors">
                        <span>Mehr erfahren</span>
                        <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Ihre Branche nicht dabei? -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="bg-white dark:bg-slate-700 rounded-2xl p-8 md:p-12 shadow-sm">
            <div class="text-4xl mb-4">🤔</div>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-4">
                Ihre Branche nicht dabei?
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg mb-8 max-w-2xl mx-auto">
                Kein Problem! Leadbusiness funktioniert für jedes Unternehmen mit zufriedenen Kunden. 
                Sie können alle Texte, Belohnungen und das Design individuell anpassen.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/onboarding" class="btn-primary btn-large inline-flex items-center justify-center gap-2">
                    <span>Jetzt kostenlos starten</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
                <a href="/kontakt" class="btn-secondary btn-large inline-flex items-center justify-center gap-2">
                    <span>Kontakt aufnehmen</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Vorteile für alle Branchen -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Das bekommen Sie in jeder Branche
            </h2>
        </div>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
            <div class="text-center">
                <div class="w-14 h-14 bg-primary-100 dark:bg-primary-900/30 rounded-2xl flex items-center justify-center text-primary-600 dark:text-primary-400 text-2xl mx-auto mb-4">
                    <i class="fas fa-magic"></i>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">Branchenspezifische Vorlagen</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Texte, Belohnungen und Designs passend zu Ihrer Branche.</p>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 bg-green-100 dark:bg-green-900/30 rounded-2xl flex items-center justify-center text-green-600 dark:text-green-400 text-2xl mx-auto mb-4">
                    <i class="fas fa-cog"></i>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">Vollautomatisch</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">0 Minuten Aufwand nach der Einrichtung.</p>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 bg-purple-100 dark:bg-purple-900/30 rounded-2xl flex items-center justify-center text-purple-600 dark:text-purple-400 text-2xl mx-auto mb-4">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">DSGVO-konform</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Hosting in Deutschland, Double-Opt-In, alle Rechtstexte.</p>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 bg-yellow-100 dark:bg-yellow-900/30 rounded-2xl flex items-center justify-center text-yellow-600 dark:text-yellow-400 text-2xl mx-auto mb-4">
                    <i class="fas fa-trophy"></i>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">Gamification</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Punkte, Badges und Leaderboards motivieren zum Teilen.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-12 md:py-20 gradient-bg text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold mb-4 md:mb-6">
            Bereit für Ihr Empfehlungsprogramm?
        </h2>
        <p class="text-lg md:text-xl text-white/90 mb-6 md:mb-8">
            Starten Sie jetzt kostenlos – Einrichtung in nur 5 Minuten.
        </p>
        <a href="/onboarding" class="btn-white btn-large inline-flex items-center justify-center gap-2">
            <span>Jetzt 14 Tage kostenlos testen</span>
            <i class="fas fa-arrow-right"></i>
        </a>
        <p class="text-white/70 mt-6 text-sm">
            Keine Kreditkarte erforderlich · Keine Technik nötig · DSGVO-konform
        </p>
    </div>
</section>

<?php require_once __DIR__ . '/../../templates/marketing/footer.php'; ?>
