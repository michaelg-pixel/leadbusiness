<?php
/**
 * Branchenseite: Friseure
 */

$pageTitle = 'Empfehlungsprogramm für Friseure';
$metaDescription = 'Automatisches Empfehlungsprogramm für Friseursalons. Kunden werben Kunden und erhalten Belohnungen wie Gratis-Pflegeprodukte oder Rabatte.';
$currentPage = 'branchen';

require_once __DIR__ . '/../../templates/marketing/header.php';
?>

<!-- Hero Section -->
<section class="relative py-16 md:py-24 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-pink-500 to-purple-600"></div>
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-40 h-40 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 right-10 w-60 h-60 bg-white rounded-full blur-3xl"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
            <div class="text-white">
                <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full mb-6">
                    <i class="fas fa-cut"></i>
                    <span class="text-sm font-medium">Für Friseure</span>
                </div>
                
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold mb-6 leading-tight">
                    Mehr Kunden durch begeisterte Stammkunden
                </h1>
                
                <p class="text-lg md:text-xl text-white/90 mb-8 leading-relaxed">
                    Ihre zufriedenen Kunden sind Ihre beste Werbung. Mit Leadbusiness belohnen Sie Empfehlungen automatisch und gewinnen neue Stammkunden.
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
            
            <div class="hidden lg:block">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="bg-white rounded-xl shadow-2xl p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-cut text-pink-600 text-xl"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">Salon Style & Cut</div>
                                <div class="text-sm text-gray-500">empfohlen.de/style-cut</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-pink-600">183</div>
                                <div class="text-xs text-gray-500">Empfehler</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">67</div>
                                <div class="text-xs text-gray-500">Neukunden</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600">37%</div>
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

<!-- Vorteile Section -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Warum Empfehlungsmarketing für Friseursalons?
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg max-w-2xl mx-auto">
                Ihre Kunden sprechen über ihre neue Frisur – nutzen Sie das für Ihr Wachstum.
            </p>
        </div>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
            <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-6 hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-pink-100 dark:bg-pink-900/30 rounded-xl flex items-center justify-center text-pink-600 dark:text-pink-400 text-xl mb-4">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Neue Stammkunden</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Empfohlene Kunden werden oft selbst zu treuen Stammkunden.</p>
            </div>
            
            <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-6 hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-pink-100 dark:bg-pink-900/30 rounded-xl flex items-center justify-center text-pink-600 dark:text-pink-400 text-xl mb-4">
                    <i class="fas fa-comments"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Natürliche Werbung</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Komplimente für die Frisur führen zu "Wo warst du?" – perfekte Empfehlung!</p>
            </div>
            
            <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-6 hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-pink-100 dark:bg-pink-900/30 rounded-xl flex items-center justify-center text-pink-600 dark:text-pink-400 text-xl mb-4">
                    <i class="fas fa-heart"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Kundenbindung</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Belohnungen stärken die Bindung zu Ihrem Salon.</p>
            </div>
            
            <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-6 hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-pink-100 dark:bg-pink-900/30 rounded-xl flex items-center justify-center text-pink-600 dark:text-pink-400 text-xl mb-4">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Null Aufwand</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Alles läuft automatisch – Sie stylen, wir kümmern uns um den Rest.</p>
            </div>
        </div>
    </div>
</section>

<!-- Belohnungen Section -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            <div>
                <span class="text-pink-600 dark:text-pink-400 font-semibold uppercase tracking-wide text-sm">Belohnungssystem</span>
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mt-2 mb-6">
                    Beispiel-Belohnungen für Ihren Salon
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-lg mb-8">
                    Belohnen Sie Ihre treuen Kunden mit attraktiven Prämien:
                </p>
                
                <div class="space-y-4">
                    <div class="flex items-center gap-4 bg-white dark:bg-slate-700 rounded-xl p-4 shadow-sm">
                        <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">3</div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">3 Empfehlungen</div>
                            <div class="font-semibold text-gray-900 dark:text-white">Gratis Pflegeprodukt Ihrer Wahl</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 bg-white dark:bg-slate-700 rounded-xl p-4 shadow-sm">
                        <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">5</div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">5 Empfehlungen</div>
                            <div class="font-semibold text-gray-900 dark:text-white">20% Rabatt auf alle Leistungen</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 bg-white dark:bg-slate-700 rounded-xl p-4 shadow-sm">
                        <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">10</div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">10 Empfehlungen</div>
                            <div class="font-semibold text-gray-900 dark:text-white">Gratis Styling-Treatment (Olaplex, Keratin...)</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 md:p-8 shadow-lg">
                <div class="text-center mb-6">
                    <div class="text-5xl mb-3">💇‍♀️</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Teile deinen Look!</h3>
                    <p class="text-gray-500 dark:text-gray-400">Empfehle Salon Style & Cut weiter</p>
                </div>
                
                <div class="mb-6">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600 dark:text-gray-400">Fortschritt zur nächsten Belohnung</span>
                        <span class="font-medium text-pink-600 dark:text-pink-400">4/5</span>
                    </div>
                    <div class="h-3 bg-gray-200 dark:bg-slate-600 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-pink-500 to-purple-600 rounded-full" style="width: 80%"></div>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Noch 1 Empfehlung bis: <strong class="text-gray-900 dark:text-white">20% Rabatt</strong></p>
                </div>
                
                <div class="grid grid-cols-4 gap-2">
                    <button class="p-3 bg-green-500 text-white rounded-xl hover:bg-green-600 transition-colors">
                        <i class="fab fa-whatsapp text-xl"></i>
                    </button>
                    <button class="p-3 bg-pink-500 text-white rounded-xl hover:bg-pink-600 transition-colors">
                        <i class="fab fa-instagram text-xl"></i>
                    </button>
                    <button class="p-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
                        <i class="fab fa-facebook text-xl"></i>
                    </button>
                    <button class="p-3 bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-300 dark:hover:bg-slate-500 transition-colors">
                        <i class="fas fa-copy text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonial -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-br from-pink-50 to-purple-50 dark:from-slate-800 dark:to-slate-700 rounded-2xl p-8 md:p-12 text-center">
            <div class="flex justify-center gap-1 text-yellow-400 mb-6">
                <i class="fas fa-star text-xl"></i>
                <i class="fas fa-star text-xl"></i>
                <i class="fas fa-star text-xl"></i>
                <i class="fas fa-star text-xl"></i>
                <i class="fas fa-star text-xl"></i>
            </div>
            
            <blockquote class="text-xl md:text-2xl font-medium text-gray-900 dark:text-white mb-8 leading-relaxed">
                "Meine Kundinnen lieben das Punktesystem! Sie teilen Fotos ihrer neuen Frisur auf Instagram und markieren uns. Win-Win für alle!"
            </blockquote>
            
            <div class="flex items-center justify-center gap-4">
                <div class="w-14 h-14 bg-pink-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                    SB
                </div>
                <div class="text-left">
                    <div class="font-bold text-gray-900 dark:text-white">Sandra Becker</div>
                    <div class="text-gray-600 dark:text-gray-400">Salon Style & Cut, Hamburg</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-12 md:py-20 bg-gradient-to-r from-pink-500 to-purple-600 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold mb-4 md:mb-6">
            Bereit für mehr Kunden durch Empfehlungen?
        </h2>
        <p class="text-lg md:text-xl text-white/90 mb-6 md:mb-8">
            Starten Sie noch heute und machen Sie Ihre Kunden zu Ihren besten Werbern.
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
