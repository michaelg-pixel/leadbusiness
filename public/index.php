<?php
/**
 * Leadbusiness - Landingpage
 * Vollautomatisches Empfehlungsprogramm für Unternehmen
 */

$pageTitle = 'Empfehlungsprogramm für Ihr Unternehmen';
$metaDescription = 'Vollautomatisches Empfehlungsprogramm: Kunden werben Kunden und werden automatisch belohnt. Einrichtung in 5 Minuten. Ab 49€/Monat.';
$currentPage = 'home';

require_once __DIR__ . '/../templates/marketing/header.php';
?>

<!-- Hero Section -->
<section class="hero-pattern min-h-screen flex items-center relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-primary-500/90 to-secondary/90"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            
            <!-- Hero Content -->
            <div class="text-white">
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full mb-6">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    <span class="text-sm font-medium">Bereits 500+ Unternehmen vertrauen uns</span>
                </div>
                
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
                    Kunden werben Kunden –
                    <span class="text-yellow-300">vollautomatisch</span>
                </h1>
                
                <p class="text-xl md:text-2xl text-white/90 mb-8 leading-relaxed">
                    Ihr eigenes Empfehlungsprogramm in 5 Minuten. 
                    Ohne Technik-Wissen. Ohne manuellen Aufwand.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 mb-12">
                    <a href="/onboarding" class="btn-white btn-large inline-flex items-center justify-center gap-2">
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
                        <span>14 Tage testen</span>
                    </div>
                </div>
            </div>
            
            <!-- Hero Visual -->
            <div class="relative hidden lg:block">
                <div class="relative z-10 bg-white rounded-2xl shadow-2xl p-6 transform rotate-2 hover:rotate-0 transition-transform duration-500">
                    <!-- Mock Dashboard -->
                    <div class="border-b pb-4 mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 gradient-bg rounded-lg flex items-center justify-center text-white">
                                <i class="fas fa-tooth"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">Zahnarzt Dr. Müller</div>
                                <div class="text-sm text-gray-500">empfohlen.de/dr-mueller</div>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">Aktiv</span>
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="text-center p-3 bg-gray-50 rounded-xl">
                            <div class="text-2xl font-bold text-primary-500">247</div>
                            <div class="text-xs text-gray-500">Empfehler</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-xl">
                            <div class="text-2xl font-bold text-green-500">89</div>
                            <div class="text-xs text-gray-500">Neukunden</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-xl">
                            <div class="text-2xl font-bold text-yellow-500">36%</div>
                            <div class="text-xs text-gray-500">Conversion</div>
                        </div>
                    </div>
                    
                    <!-- Recent Activity -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-2 bg-green-50 rounded-lg">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">Neuer Empfehler</div>
                                <div class="text-xs text-gray-500">Maria S. - vor 2 Min</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-2 bg-yellow-50 rounded-lg">
                            <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center text-white text-sm">
                                <i class="fas fa-gift"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">Belohnung freigeschaltet</div>
                                <div class="text-xs text-gray-500">Thomas K. - Stufe 2 erreicht</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-2 bg-blue-50 rounded-lg">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm">
                                <i class="fas fa-share-alt"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">Link geteilt</div>
                                <div class="text-xs text-gray-500">Anna M. via WhatsApp</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Floating Elements -->
                <div class="absolute -top-4 -right-4 w-20 h-20 bg-yellow-400 rounded-2xl flex items-center justify-center text-3xl floating shadow-lg">
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

<!-- Logo Slider (Social Proof) -->
<section class="py-12 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-center text-gray-500 dark:text-gray-400 mb-8 font-medium">Vertrauen von Unternehmen aus allen Branchen</p>
        <div class="flex flex-wrap justify-center items-center gap-8 md:gap-16 opacity-60">
            <div class="flex items-center gap-2 text-xl font-bold text-gray-400">
                <i class="fas fa-tooth text-2xl"></i> Zahnärzte
            </div>
            <div class="flex items-center gap-2 text-xl font-bold text-gray-400">
                <i class="fas fa-cut text-2xl"></i> Friseure
            </div>
            <div class="flex items-center gap-2 text-xl font-bold text-gray-400">
                <i class="fas fa-dumbbell text-2xl"></i> Fitness
            </div>
            <div class="flex items-center gap-2 text-xl font-bold text-gray-400">
                <i class="fas fa-shopping-cart text-2xl"></i> Online-Shops
            </div>
            <div class="flex items-center gap-2 text-xl font-bold text-gray-400">
                <i class="fas fa-lightbulb text-2xl"></i> Coaches
            </div>
        </div>
    </div>
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
            <span class="text-primary-500 font-semibold uppercase tracking-wide">So funktioniert's</span>
            <h2 class="text-3xl md:text-4xl font-bold mt-3 dark:text-white">In 3 Schritten zum Empfehlungsprogramm</h2>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Step 1 -->
            <div class="relative">
                <div class="bg-white dark:bg-slate-700 rounded-2xl p-8 shadow-lg card-hover h-full">
                    <div class="w-16 h-16 gradient-bg rounded-2xl flex items-center justify-center text-white text-2xl font-bold mb-6">
                        1
                    </div>
                    <h3 class="text-xl font-bold mb-4 dark:text-white">Onboarding ausfüllen</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Beantworten Sie 8 einfache Fragen zu Ihrem Unternehmen. 
                        Das dauert nur 5 Minuten.
                    </p>
                </div>
                <div class="hidden md:block absolute top-1/2 -right-4 transform -translate-y-1/2 text-primary-300 text-4xl">
                    →
                </div>
            </div>
            
            <!-- Step 2 -->
            <div class="relative">
                <div class="bg-white dark:bg-slate-700 rounded-2xl p-8 shadow-lg card-hover h-full">
                    <div class="w-16 h-16 gradient-bg rounded-2xl flex items-center justify-center text-white text-2xl font-bold mb-6">
                        2
                    </div>
                    <h3 class="text-xl font-bold mb-4 dark:text-white">Automatische Einrichtung</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Wir erstellen automatisch Ihre Empfehlungsseite, 
                        E-Mails und Belohnungsstufen.
                    </p>
                </div>
                <div class="hidden md:block absolute top-1/2 -right-4 transform -translate-y-1/2 text-primary-300 text-4xl">
                    →
                </div>
            </div>
            
            <!-- Step 3 -->
            <div>
                <div class="bg-white dark:bg-slate-700 rounded-2xl p-8 shadow-lg card-hover h-full">
                    <div class="w-16 h-16 gradient-bg rounded-2xl flex items-center justify-center text-white text-2xl font-bold mb-6">
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
            <a href="/onboarding" class="btn-primary btn-large inline-flex items-center gap-2">
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
            <span class="text-primary-500 font-semibold uppercase tracking-wide">Features</span>
            <h2 class="text-3xl md:text-4xl font-bold mt-3 dark:text-white">Alles, was Sie brauchen</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-4 max-w-2xl mx-auto">
                Ein komplettes Empfehlungsprogramm – fertig konfiguriert und einsatzbereit.
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="p-6 rounded-2xl border border-gray-200 dark:border-slate-600 hover:border-primary-500 hover:shadow-lg transition-all bg-white dark:bg-slate-800">
                <div class="feature-icon bg-primary-100 dark:bg-primary-900/30">
                    <i class="fas fa-link text-primary-500"></i>
                </div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">Persönliche Empfehlungslinks</h3>
                <p class="text-gray-600 dark:text-gray-300">Jeder Kunde bekommt einen einzigartigen Link zum Teilen.</p>
            </div>
            
            <!-- Feature 2 -->
            <div class="p-6 rounded-2xl border border-gray-200 dark:border-slate-600 hover:border-primary-500 hover:shadow-lg transition-all bg-white dark:bg-slate-800">
                <div class="feature-icon bg-green-100 dark:bg-green-900/30">
                    <i class="fas fa-gift text-green-500"></i>
                </div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">Automatische Belohnungen</h3>
                <p class="text-gray-600 dark:text-gray-300">Empfehler werden automatisch per E-Mail über ihre Belohnung informiert.</p>
            </div>
            
            <!-- Feature 3 -->
            <div class="p-6 rounded-2xl border border-gray-200 dark:border-slate-600 hover:border-primary-500 hover:shadow-lg transition-all bg-white dark:bg-slate-800">
                <div class="feature-icon bg-yellow-100 dark:bg-yellow-900/30">
                    <i class="fas fa-trophy text-yellow-500"></i>
                </div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">Gamification</h3>
                <p class="text-gray-600 dark:text-gray-300">Leaderboards, Badges und Fortschrittsbalken motivieren zum Weiterempfehlen.</p>
            </div>
            
            <!-- Feature 4 -->
            <div class="p-6 rounded-2xl border border-gray-200 dark:border-slate-600 hover:border-primary-500 hover:shadow-lg transition-all bg-white dark:bg-slate-800">
                <div class="feature-icon bg-blue-100 dark:bg-blue-900/30">
                    <i class="fas fa-share-alt text-blue-500"></i>
                </div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">11 Share-Buttons</h3>
                <p class="text-gray-600 dark:text-gray-300">WhatsApp, Facebook, E-Mail, SMS und mehr – mit einem Klick teilen.</p>
            </div>
            
            <!-- Feature 5 -->
            <div class="p-6 rounded-2xl border border-gray-200 dark:border-slate-600 hover:border-primary-500 hover:shadow-lg transition-all bg-white dark:bg-slate-800">
                <div class="feature-icon bg-purple-100 dark:bg-purple-900/30">
                    <i class="fas fa-palette text-purple-500"></i>
                </div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">Branchen-Designs</h3>
                <p class="text-gray-600 dark:text-gray-300">Professionelle Hintergrundbilder passend zu Ihrer Branche.</p>
            </div>
            
            <!-- Feature 6 -->
            <div class="p-6 rounded-2xl border border-gray-200 dark:border-slate-600 hover:border-primary-500 hover:shadow-lg transition-all bg-white dark:bg-slate-800">
                <div class="feature-icon bg-red-100 dark:bg-red-900/30">
                    <i class="fas fa-chart-line text-red-500"></i>
                </div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">Live-Statistiken</h3>
                <p class="text-gray-600 dark:text-gray-300">Sehen Sie in Echtzeit, wer empfiehlt und wie erfolgreich Sie sind.</p>
            </div>
        </div>
        
        <div class="text-center mt-12">
            <a href="/funktionen" class="text-primary-500 font-semibold hover:underline inline-flex items-center gap-2">
                Alle Funktionen ansehen <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Industries Section -->
<section class="py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-primary-500 font-semibold uppercase tracking-wide">Branchen</span>
            <h2 class="text-3xl md:text-4xl font-bold mt-3 dark:text-white">Perfekt für jede Branche</h2>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            <?php
            $industries = [
                ['icon' => 'fa-tooth', 'name' => 'Zahnärzte', 'color' => 'blue'],
                ['icon' => 'fa-cut', 'name' => 'Friseure', 'color' => 'pink'],
                ['icon' => 'fa-dumbbell', 'name' => 'Fitness', 'color' => 'green'],
                ['icon' => 'fa-utensils', 'name' => 'Restaurants', 'color' => 'orange'],
                ['icon' => 'fa-shopping-bag', 'name' => 'Online-Shops', 'color' => 'purple'],
                ['icon' => 'fa-lightbulb', 'name' => 'Coaches', 'color' => 'yellow'],
            ];
            
            foreach ($industries as $industry):
            ?>
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 text-center shadow-sm hover:shadow-lg transition-shadow cursor-pointer group">
                <div class="industry-icon mx-auto group-hover:scale-110 transition-transform">
                    <i class="fas <?= $industry['icon'] ?>"></i>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white"><?= $industry['name'] ?></h3>
            </div>
            <?php endforeach; ?>
        </div>
        
        <p class="text-center text-gray-500 dark:text-gray-400 mt-8">
            Und viele weitere: Handwerker, Therapeuten, SaaS, Newsletter, Agenturen...
        </p>
    </div>
</section>

<!-- Pricing Preview Section -->
<section class="py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-primary-500 font-semibold uppercase tracking-wide">Preise</span>
            <h2 class="text-3xl md:text-4xl font-bold mt-3 dark:text-white">Einfach & transparent</h2>
        </div>
        
        <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <!-- Starter Plan -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border-2 border-gray-200 dark:border-slate-600 p-8 hover:border-primary-500 transition-colors">
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
                
                <a href="/onboarding?plan=starter" class="btn-secondary w-full text-center block">
                    Starter wählen
                </a>
            </div>
            
            <!-- Professional Plan -->
            <div class="pricing-card popular bg-white dark:bg-slate-800 rounded-2xl border-2 border-primary-500 p-8 shadow-xl relative">
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
                
                <a href="/onboarding?plan=professional" class="btn-primary w-full text-center block">
                    Professional wählen
                </a>
            </div>
        </div>
        
        <p class="text-center text-gray-500 dark:text-gray-400 mt-8">
            <i class="fas fa-info-circle mr-1"></i>
            Einmalige Einrichtungsgebühr: 499€ · 14 Tage kostenlos testen
        </p>
        
        <div class="text-center mt-6">
            <a href="/preise" class="text-primary-500 font-semibold hover:underline">
                Alle Details vergleichen →
            </a>
        </div>
    </div>
</section>

<!-- ========================================== -->
<!-- 💰 COST CALCULATOR SECTION -->
<!-- ========================================== -->
<section id="kostenrechner" class="py-20 bg-slate-900 dark:bg-black relative overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0 opacity-30">
        <div class="absolute inset-0" style="background: radial-gradient(ellipse at 20% 50%, rgba(102, 126, 234, 0.15) 0%, transparent 50%), radial-gradient(ellipse at 80% 50%, rgba(118, 75, 162, 0.15) 0%, transparent 50%);"></div>
    </div>
    
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Header -->
        <div class="text-center mb-12">
            <span class="inline-block px-4 py-1 rounded-full bg-primary-500/20 text-primary-400 text-sm font-semibold mb-4">
                <i class="fas fa-calculator mr-2"></i>Kostenvergleich
            </span>
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white mb-4">
                <span class="bg-gradient-to-r from-primary-400 via-cyan-400 to-primary-400 bg-clip-text text-transparent bg-[length:200%_auto] animate-pulse">
                    Leadbusiness vs. Werbeplattformen
                </span>
            </h2>
            <p class="text-lg text-gray-400 max-w-2xl mx-auto">
                Entdecken Sie, wie viel Sie mit Empfehlungsmarketing im Vergleich zu herkömmlichen Werbeplattformen sparen können.
            </p>
        </div>
        
        <!-- Slider Box -->
        <div class="bg-white/5 backdrop-blur-xl rounded-2xl p-6 md:p-10 border border-white/10 mb-10">
            <label class="block text-xl font-semibold text-white text-center mb-6">
                <i class="fas fa-users mr-2 text-primary-400"></i>
                Erwartete Leads pro Monat:
            </label>
            
            <div class="relative mb-6">
                <input 
                    type="range" 
                    id="leadCalcSlider" 
                    min="10" 
                    max="1000" 
                    value="250"
                    class="w-full h-3 rounded-full appearance-none cursor-pointer bg-white/20"
                    style="accent-color: #667eea;"
                    oninput="updateCostCalculator(this.value)"
                >
            </div>
            
            <div id="leadCalcCount" class="text-center text-3xl md:text-4xl font-bold text-primary-400 p-4 rounded-xl bg-primary-500/10 border-2 border-primary-500/50">
                250 Leads
            </div>
        </div>
        
        <!-- Cost Table -->
        <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px]">
                    <thead>
                        <tr class="bg-white/10">
                            <th class="px-4 md:px-6 py-4 text-left text-sm font-bold text-white uppercase tracking-wide">Anbieter</th>
                            <th class="px-4 md:px-6 py-4 text-left text-sm font-bold text-white uppercase tracking-wide">Grundgebühr</th>
                            <th class="px-4 md:px-6 py-4 text-left text-sm font-bold text-white uppercase tracking-wide">Kosten/Lead</th>
                            <th class="px-4 md:px-6 py-4 text-left text-sm font-bold text-white uppercase tracking-wide">Gesamt/Monat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        <!-- Google Ads -->
                        <tr class="hover:bg-white/5 transition-colors border-l-4 border-red-500/50 bg-red-500/5">
                            <td class="px-4 md:px-6 py-4 font-semibold text-red-300">
                                <i class="fab fa-google mr-2"></i>Google Ads
                            </td>
                            <td class="px-4 md:px-6 py-4 text-gray-400">0,00 €</td>
                            <td class="px-4 md:px-6 py-4 text-gray-400">12,50 €</td>
                            <td class="px-4 md:px-6 py-4 text-red-400 font-bold" id="costGoogle">3.125,00 €</td>
                        </tr>
                        <!-- Meta Ads -->
                        <tr class="hover:bg-white/5 transition-colors border-l-4 border-red-500/50 bg-red-500/5">
                            <td class="px-4 md:px-6 py-4 font-semibold text-red-300">
                                <i class="fab fa-meta mr-2"></i>Meta Ads
                            </td>
                            <td class="px-4 md:px-6 py-4 text-gray-400">0,00 €</td>
                            <td class="px-4 md:px-6 py-4 text-gray-400">11,80 €</td>
                            <td class="px-4 md:px-6 py-4 text-red-400 font-bold" id="costMeta">2.950,00 €</td>
                        </tr>
                        <!-- YouTube Ads -->
                        <tr class="hover:bg-white/5 transition-colors border-l-4 border-red-500/50 bg-red-500/5">
                            <td class="px-4 md:px-6 py-4 font-semibold text-red-300">
                                <i class="fab fa-youtube mr-2"></i>YouTube Ads
                            </td>
                            <td class="px-4 md:px-6 py-4 text-gray-400">0,00 €</td>
                            <td class="px-4 md:px-6 py-4 text-gray-400">5,00 €</td>
                            <td class="px-4 md:px-6 py-4 text-red-400 font-bold" id="costYouTube">1.250,00 €</td>
                        </tr>
                        <!-- TikTok Ads -->
                        <tr class="hover:bg-white/5 transition-colors border-l-4 border-red-500/50 bg-red-500/5">
                            <td class="px-4 md:px-6 py-4 font-semibold text-red-300">
                                <i class="fab fa-tiktok mr-2"></i>TikTok Ads
                            </td>
                            <td class="px-4 md:px-6 py-4 text-gray-400">0,00 €</td>
                            <td class="px-4 md:px-6 py-4 text-gray-400">8,75 €</td>
                            <td class="px-4 md:px-6 py-4 text-red-400 font-bold" id="costTikTok">2.187,50 €</td>
                        </tr>
                        <!-- LinkedIn Ads -->
                        <tr class="hover:bg-white/5 transition-colors border-l-4 border-red-500/50 bg-red-500/5">
                            <td class="px-4 md:px-6 py-4 font-semibold text-red-300">
                                <i class="fab fa-linkedin mr-2"></i>LinkedIn Ads
                            </td>
                            <td class="px-4 md:px-6 py-4 text-gray-400">0,00 €</td>
                            <td class="px-4 md:px-6 py-4 text-gray-400">45,00 €</td>
                            <td class="px-4 md:px-6 py-4 text-red-400 font-bold" id="costLinkedIn">11.250,00 €</td>
                        </tr>
                        <!-- Other Tools -->
                        <tr class="hover:bg-white/5 transition-colors border-l-4 border-yellow-500/50 bg-yellow-500/5">
                            <td class="px-4 md:px-6 py-4 font-semibold text-yellow-300">
                                <i class="fas fa-tools mr-2"></i>Andere Lead-Tools
                            </td>
                            <td class="px-4 md:px-6 py-4 text-gray-400">199,00 €</td>
                            <td class="px-4 md:px-6 py-4 text-gray-400">1,50 €</td>
                            <td class="px-4 md:px-6 py-4 text-yellow-400 font-bold" id="costOther">574,00 €</td>
                        </tr>
                        <!-- Leadbusiness (Highlighted) -->
                        <tr class="bg-gradient-to-r from-green-500/20 to-green-500/10 border-l-4 border-green-500 hover:from-green-500/30 hover:to-green-500/20 transition-all">
                            <td class="px-4 md:px-6 py-5 font-bold text-white text-lg">
                                <i class="fas fa-rocket mr-2 text-green-400"></i>Leadbusiness
                            </td>
                            <td class="px-4 md:px-6 py-5 text-green-300 font-semibold">49,00 €</td>
                            <td class="px-4 md:px-6 py-5 text-green-300 font-semibold">~1,00 €</td>
                            <td class="px-4 md:px-6 py-5 text-green-400 font-bold text-xl" id="costLeadbusiness">299,00 €</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Savings Box -->
        <div class="bg-gradient-to-br from-green-500/20 to-green-500/10 rounded-2xl p-6 md:p-8 border-2 border-green-500/50 text-center mb-8">
            <h3 class="text-xl md:text-2xl font-bold text-white mb-4">
                <i class="fas fa-piggy-bank mr-2 text-green-400"></i>
                Ihre monatliche Ersparnis mit Leadbusiness:
            </h3>
            <div id="savingsDisplay" class="text-lg md:text-xl text-green-300 space-y-2">
                <p>Gegenüber anderen Tools: <span class="inline-block px-3 py-1 bg-white/10 rounded-lg font-bold text-green-400" id="savingsTools">275,00 €</span></p>
                <p>Gegenüber Werbeplattformen: <span class="inline-block px-3 py-1 bg-white/10 rounded-lg font-bold text-green-400" id="savingsAds">3.853,50 €</span></p>
            </div>
        </div>
        
        <!-- Disclaimer -->
        <div class="bg-white/5 rounded-xl p-4 md:p-6 border border-white/10 text-center">
            <p class="text-sm text-gray-500 leading-relaxed">
                <strong class="text-gray-400">Hinweis:</strong> Die hier dargestellten Kosten pro Lead beruhen auf branchenüblichen Durchschnittswerten aus öffentlich zugänglichen Quellen und Erfahrungswerten. Die tatsächlichen Kosten können je nach Kampagne, Zielgruppe und Plattform variieren. Die Leads bei Leadbusiness basieren auf unserem Empfehlungsmodell und sind nicht direkt mit klassischen Werbekampagnen vergleichbar.
            </p>
        </div>
        
        <!-- CTA -->
        <div class="text-center mt-10">
            <a href="/onboarding" class="btn-success btn-large inline-flex items-center gap-2">
                <i class="fas fa-rocket"></i>
                <span>Jetzt Kosten sparen</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Calculator JavaScript -->
<script>
function updateCostCalculator(leads) {
    const leadCount = parseInt(leads);
    
    // Update display
    document.getElementById('leadCalcCount').textContent = leadCount + ' Leads';
    
    // Calculate costs (Cost per Lead * Leads)
    const costs = {
        google: 12.50 * leadCount,
        meta: 11.80 * leadCount,
        youtube: 5.00 * leadCount,
        tiktok: 8.75 * leadCount,
        linkedin: 45.00 * leadCount,
        other: 199 + (1.50 * leadCount),
        leadbusiness: 49 + (1.00 * leadCount)
    };
    
    // Format currency
    const formatCurrency = (amount) => {
        return amount.toLocaleString('de-DE', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' €';
    };
    
    // Update table
    document.getElementById('costGoogle').textContent = formatCurrency(costs.google);
    document.getElementById('costMeta').textContent = formatCurrency(costs.meta);
    document.getElementById('costYouTube').textContent = formatCurrency(costs.youtube);
    document.getElementById('costTikTok').textContent = formatCurrency(costs.tiktok);
    document.getElementById('costLinkedIn').textContent = formatCurrency(costs.linkedin);
    document.getElementById('costOther').textContent = formatCurrency(costs.other);
    document.getElementById('costLeadbusiness').textContent = formatCurrency(costs.leadbusiness);
    
    // Calculate savings
    const avgAdsCost = (costs.google + costs.meta + costs.youtube + costs.tiktok + costs.linkedin) / 5;
    const savingsVsTools = costs.other - costs.leadbusiness;
    const savingsVsAds = avgAdsCost - costs.leadbusiness;
    
    // Update savings display
    document.getElementById('savingsTools').textContent = formatCurrency(Math.max(0, savingsVsTools));
    document.getElementById('savingsAds').textContent = formatCurrency(Math.max(0, savingsVsAds));
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCostCalculator(250);
});
</script>
<!-- END COST CALCULATOR SECTION -->

<!-- Testimonials Section -->
<section class="py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-primary-500 font-semibold uppercase tracking-wide">Kundenstimmen</span>
            <h2 class="text-3xl md:text-4xl font-bold mt-3 dark:text-white">Das sagen unsere Kunden</h2>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Testimonial 1 -->
            <div class="testimonial-card bg-white dark:bg-slate-700 rounded-2xl p-8 shadow-lg">
                <div class="flex items-center gap-1 text-yellow-400 mb-4">
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
                    <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center text-primary-500 font-bold">
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
                <div class="flex items-center gap-1 text-yellow-400 mb-4">
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
                    <div class="w-12 h-12 bg-pink-100 dark:bg-pink-900/30 rounded-full flex items-center justify-center text-pink-500 font-bold">
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
                <div class="flex items-center gap-1 text-yellow-400 mb-4">
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
<section class="py-20 gradient-bg text-white">
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
            <span class="text-primary-500 font-semibold uppercase tracking-wide">FAQ</span>
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
            <a href="/faq" class="text-primary-500 font-semibold hover:underline">
                Alle Fragen ansehen →
            </a>
        </div>
    </div>
</section>

<!-- Final CTA Section -->
<section class="cta-section py-20 gradient-bg text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <h2 class="text-3xl md:text-5xl font-extrabold mb-6">
            Bereit für mehr Kunden durch Empfehlungen?
        </h2>
        <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
            Starten Sie noch heute und verwandeln Sie zufriedene Kunden in Ihre besten Verkäufer.
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

<?php
require_once __DIR__ . '/../templates/marketing/footer.php';
?>
