<?php
/**
 * Wissen - Übersichtsseite
 * Ratgeber & Case Studies statt klassischem Blog
 */

$pageTitle = 'Wissen & Ratgeber - Empfehlungsmarketing';
$metaDescription = 'Praxiswissen rund um Empfehlungsmarketing: Branchenratgeber, Erfolgsgeschichten und Expertentipps für mehr Kunden durch Mundpropaganda.';
$currentPage = 'wissen';

require_once __DIR__ . '/../../templates/marketing/header.php';
?>

<!-- Hero Section -->
<section class="relative py-16 md:py-24 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-emerald-600 to-teal-700"></div>
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-40 h-40 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 right-10 w-60 h-60 bg-white rounded-full blur-3xl"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full mb-6">
            <i class="fas fa-graduation-cap"></i>
            <span class="text-sm font-medium text-white">Wissen & Ratgeber</span>
        </div>
        
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white mb-6 leading-tight">
            Empfehlungsmarketing meistern
        </h1>
        
        <p class="text-lg md:text-xl text-white/90 mb-8 max-w-2xl mx-auto leading-relaxed">
            Praxiserprobte Strategien, Branchenratgeber und echte Erfolgsgeschichten – 
            alles was Sie für erfolgreiches Empfehlungsmarketing brauchen.
        </p>
    </div>
</section>

<!-- Branchen-Ratgeber Section -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-emerald-600 dark:text-emerald-400 font-semibold uppercase tracking-wide text-sm">Branchen-Ratgeber</span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mt-2 mb-4">
                Empfehlungsmarketing für Ihre Branche
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg max-w-2xl mx-auto">
                Detaillierte Leitfäden mit branchenspezifischen Strategien, Belohnungsideen und Best Practices.
            </p>
        </div>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            
            <!-- Zahnarzt Ratgeber -->
            <a href="/wissen/empfehlungsmarketing-zahnarzt" class="group bg-gray-50 dark:bg-slate-800 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300">
                <div class="h-40 bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center">
                    <i class="fas fa-tooth text-white text-5xl opacity-80 group-hover:scale-110 transition-transform"></i>
                </div>
                <div class="p-6">
                    <span class="inline-block px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-semibold rounded-full mb-3">Gesundheit</span>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                        Empfehlungsmarketing für Zahnärzte
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        So gewinnen Zahnarztpraxen neue Patienten durch systematische Mundpropaganda.
                    </p>
                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold text-sm inline-flex items-center gap-1">
                        Jetzt lesen <i class="fas fa-arrow-right text-xs"></i>
                    </span>
                </div>
            </a>
            
            <!-- Friseur Ratgeber -->
            <a href="/wissen/empfehlungsmarketing-friseur" class="group bg-gray-50 dark:bg-slate-800 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300">
                <div class="h-40 bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center">
                    <i class="fas fa-cut text-white text-5xl opacity-80 group-hover:scale-110 transition-transform"></i>
                </div>
                <div class="p-6">
                    <span class="inline-block px-3 py-1 bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 text-xs font-semibold rounded-full mb-3">Beauty</span>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                        Empfehlungsmarketing für Friseure
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Wie Friseursalons mit Empfehlungsprogrammen ihre Kundschaft verdoppeln.
                    </p>
                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold text-sm inline-flex items-center gap-1">
                        Jetzt lesen <i class="fas fa-arrow-right text-xs"></i>
                    </span>
                </div>
            </a>
            
            <!-- Fitness Ratgeber -->
            <a href="/wissen/empfehlungsmarketing-fitness" class="group bg-gray-50 dark:bg-slate-800 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300">
                <div class="h-40 bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center">
                    <i class="fas fa-dumbbell text-white text-5xl opacity-80 group-hover:scale-110 transition-transform"></i>
                </div>
                <div class="p-6">
                    <span class="inline-block px-3 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 text-xs font-semibold rounded-full mb-3">Sport</span>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                        Empfehlungsmarketing für Fitnessstudios
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Mitglieder als Wachstumsmotor: So funktioniert Empfehlungsmarketing im Fitnessbereich.
                    </p>
                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold text-sm inline-flex items-center gap-1">
                        Jetzt lesen <i class="fas fa-arrow-right text-xs"></i>
                    </span>
                </div>
            </a>
            
            <!-- Coach Ratgeber -->
            <a href="/wissen/empfehlungsmarketing-coach" class="group bg-gray-50 dark:bg-slate-800 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300">
                <div class="h-40 bg-gradient-to-br from-purple-500 to-violet-700 flex items-center justify-center">
                    <i class="fas fa-lightbulb text-white text-5xl opacity-80 group-hover:scale-110 transition-transform"></i>
                </div>
                <div class="p-6">
                    <span class="inline-block px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-xs font-semibold rounded-full mb-3">Beratung</span>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                        Empfehlungsmarketing für Coaches
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Warum Empfehlungen für Coaches der effektivste Akquisekanal sind.
                    </p>
                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold text-sm inline-flex items-center gap-1">
                        Jetzt lesen <i class="fas fa-arrow-right text-xs"></i>
                    </span>
                </div>
            </a>
            
            <!-- Online-Shop Ratgeber -->
            <a href="/wissen/empfehlungsmarketing-onlineshop" class="group bg-gray-50 dark:bg-slate-800 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300">
                <div class="h-40 bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-white text-5xl opacity-80 group-hover:scale-110 transition-transform"></i>
                </div>
                <div class="p-6">
                    <span class="inline-block px-3 py-1 bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400 text-xs font-semibold rounded-full mb-3">E-Commerce</span>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                        Empfehlungsmarketing für Online-Shops
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Customer Acquisition Cost senken: Empfehlungsprogramme im E-Commerce.
                    </p>
                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold text-sm inline-flex items-center gap-1">
                        Jetzt lesen <i class="fas fa-arrow-right text-xs"></i>
                    </span>
                </div>
            </a>
            
            <!-- Handwerker Ratgeber -->
            <a href="/wissen/empfehlungsmarketing-handwerker" class="group bg-gray-50 dark:bg-slate-800 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300">
                <div class="h-40 bg-gradient-to-br from-amber-500 to-yellow-600 flex items-center justify-center">
                    <i class="fas fa-hammer text-white text-5xl opacity-80 group-hover:scale-110 transition-transform"></i>
                </div>
                <div class="p-6">
                    <span class="inline-block px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 text-xs font-semibold rounded-full mb-3">Handwerk</span>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                        Empfehlungsmarketing für Handwerker
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Warum Mundpropaganda im Handwerk besser funktioniert als jede Werbung.
                    </p>
                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold text-sm inline-flex items-center gap-1">
                        Jetzt lesen <i class="fas fa-arrow-right text-xs"></i>
                    </span>
                </div>
            </a>
            
        </div>
    </div>
</section>

<!-- Case Studies Section -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-emerald-600 dark:text-emerald-400 font-semibold uppercase tracking-wide text-sm">Erfolgsgeschichten</span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mt-2 mb-4">
                Case Studies aus der Praxis
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg max-w-2xl mx-auto">
                Erfahren Sie, wie echte Unternehmen mit Leadbusiness messbare Erfolge erzielen.
            </p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-6 md:gap-8">
            
            <!-- Case Study 1 -->
            <a href="/wissen/case-study-zahnarztpraxis" class="group bg-white dark:bg-slate-700 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                            <i class="fas fa-tooth text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 dark:text-white">Zahnarztpraxis</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">München</div>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                        89 Neupatienten in 6 Monaten
                    </h3>
                    
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <div class="text-center p-2 bg-gray-50 dark:bg-slate-600 rounded-lg">
                            <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400">+89</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Patienten</div>
                        </div>
                        <div class="text-center p-2 bg-gray-50 dark:bg-slate-600 rounded-lg">
                            <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400">247</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Empfehler</div>
                        </div>
                        <div class="text-center p-2 bg-gray-50 dark:bg-slate-600 rounded-lg">
                            <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400">36%</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Conversion</div>
                        </div>
                    </div>
                    
                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold text-sm inline-flex items-center gap-1">
                        Case Study lesen <i class="fas fa-arrow-right text-xs"></i>
                    </span>
                </div>
            </a>
            
            <!-- Case Study 2 -->
            <a href="/wissen/case-study-friseursalon" class="group bg-white dark:bg-slate-700 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-pink-100 dark:bg-pink-900/30 rounded-xl flex items-center justify-center">
                            <i class="fas fa-cut text-pink-600 dark:text-pink-400 text-xl"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 dark:text-white">Friseursalon</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Berlin</div>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                        Kundenstamm verdoppelt in 4 Monaten
                    </h3>
                    
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <div class="text-center p-2 bg-gray-50 dark:bg-slate-600 rounded-lg">
                            <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400">+156</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Neukunden</div>
                        </div>
                        <div class="text-center p-2 bg-gray-50 dark:bg-slate-600 rounded-lg">
                            <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400">312</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Empfehler</div>
                        </div>
                        <div class="text-center p-2 bg-gray-50 dark:bg-slate-600 rounded-lg">
                            <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400">42%</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Conversion</div>
                        </div>
                    </div>
                    
                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold text-sm inline-flex items-center gap-1">
                        Case Study lesen <i class="fas fa-arrow-right text-xs"></i>
                    </span>
                </div>
            </a>
            
            <!-- Case Study 3 -->
            <a href="/wissen/case-study-fitnessstudio" class="group bg-white dark:bg-slate-700 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center">
                            <i class="fas fa-dumbbell text-orange-600 dark:text-orange-400 text-xl"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 dark:text-white">Fitnessstudio</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Hamburg</div>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                        73% weniger Akquisekosten
                    </h3>
                    
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <div class="text-center p-2 bg-gray-50 dark:bg-slate-600 rounded-lg">
                            <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400">+203</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Mitglieder</div>
                        </div>
                        <div class="text-center p-2 bg-gray-50 dark:bg-slate-600 rounded-lg">
                            <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400">487</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Empfehler</div>
                        </div>
                        <div class="text-center p-2 bg-gray-50 dark:bg-slate-600 rounded-lg">
                            <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400">-73%</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Kosten</div>
                        </div>
                    </div>
                    
                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold text-sm inline-flex items-center gap-1">
                        Case Study lesen <i class="fas fa-arrow-right text-xs"></i>
                    </span>
                </div>
            </a>
            
        </div>
    </div>
</section>

<!-- Grundlagen Section -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-emerald-600 dark:text-emerald-400 font-semibold uppercase tracking-wide text-sm">Grundlagen</span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mt-2 mb-4">
                Empfehlungsmarketing verstehen
            </h2>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-6 hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-xl mb-4">
                    <i class="fas fa-question-circle"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Was ist Empfehlungsmarketing?</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                    Die systematische Nutzung von Kundenempfehlungen als Marketingkanal.
                </p>
            </div>
            
            <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-6 hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-xl mb-4">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Warum funktioniert es?</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                    92% der Konsumenten vertrauen Empfehlungen von Freunden mehr als Werbung.
                </p>
            </div>
            
            <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-6 hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-xl mb-4">
                    <i class="fas fa-gift"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Richtig belohnen</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                    Die richtigen Anreize setzen, ohne die Authentizität zu verlieren.
                </p>
            </div>
            
            <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-6 hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-xl mb-4">
                    <i class="fas fa-rocket"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Erfolgreich starten</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                    In 5 Minuten zum eigenen Empfehlungsprogramm – ohne Technik-Wissen.
                </p>
            </div>
            
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-12 md:py-20 bg-gradient-to-r from-emerald-600 to-teal-700 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold mb-4 md:mb-6">
            Bereit für Ihr eigenes Empfehlungsprogramm?
        </h2>
        <p class="text-lg md:text-xl text-white/90 mb-6 md:mb-8">
            Starten Sie noch heute und machen Sie Ihre Kunden zu Ihren besten Werbern.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/onboarding" class="btn-white btn-large inline-flex items-center justify-center gap-2">
                <span>Jetzt 7 Tage kostenlos testen</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <p class="text-white/70 mt-6 text-sm">
            Keine Kreditkarte erforderlich · Einrichtung in 5 Minuten · DSGVO-konform
        </p>
    </div>
</section>

<?php require_once __DIR__ . '/../../templates/marketing/footer.php'; ?>
