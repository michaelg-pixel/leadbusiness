<?php
/**
 * Leadbusiness - Demo Seite
 * Video-Vorschau und Verkaufstext
 */

$pageTitle = 'Demo - So funktioniert Leadbusiness';
$metaDescription = 'Sehen Sie in dieser Demo, wie Leadbusiness funktioniert. Vollautomatisches Empfehlungsprogramm für Ihr Unternehmen in 5 Minuten eingerichtet.';
$currentPage = 'demo';

require_once __DIR__ . '/../templates/marketing/header.php';
?>

<!-- Hero Section -->
<section class="bg-gradient-to-br from-blue-600 to-blue-800 text-white py-20 lg:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-4xl mx-auto">
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full mb-6">
                <i class="fas fa-play-circle text-amber-400"></i>
                <span class="text-sm font-medium">Produkt-Demo</span>
            </div>
            
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
                Sehen Sie Leadbusiness <span class="text-amber-300">in Aktion</span>
            </h1>
            
            <p class="text-xl md:text-2xl text-white/90 mb-8 leading-relaxed">
                In nur 3 Minuten zeigen wir Ihnen, wie Sie Ihr eigenes Empfehlungsprogramm einrichten und automatisch Neukunden gewinnen.
            </p>
        </div>
    </div>
</section>

<!-- Video Section -->
<section class="py-16 lg:py-24 bg-white dark:bg-slate-900">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Video Container -->
        <div class="relative rounded-2xl overflow-hidden shadow-2xl bg-slate-900 aspect-video mb-12">
            <!-- Placeholder für Video -->
            <div id="video-placeholder" class="absolute inset-0 flex flex-col items-center justify-center bg-gradient-to-br from-slate-800 to-slate-900 cursor-pointer group">
                <!-- Play Button -->
                <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-gradient-to-r from-amber-400 to-orange-500 flex items-center justify-center shadow-[0_0_50px_rgba(251,191,36,0.4)] group-hover:shadow-[0_0_80px_rgba(251,191,36,0.6)] group-hover:scale-110 transition-all duration-300">
                    <i class="fas fa-play text-4xl md:text-5xl text-gray-900 ml-2"></i>
                </div>
                
                <p class="mt-6 text-white/80 text-lg font-medium">Klicken zum Abspielen</p>
                <p class="mt-2 text-white/50 text-sm">Video wird demnächst hinzugefügt</p>
                
                <!-- Thumbnail Overlay Pattern -->
                <div class="absolute inset-0 opacity-10 pointer-events-none">
                    <div class="absolute top-8 left-8 w-64 h-40 border-2 border-white/30 rounded-lg"></div>
                    <div class="absolute bottom-8 right-8 w-48 h-32 border-2 border-white/30 rounded-lg"></div>
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-48 border-2 border-white/30 rounded-lg"></div>
                </div>
            </div>
            
            <!-- Video Element (hidden until ready) -->
            <iframe id="demo-video" class="hidden absolute inset-0 w-full h-full" 
                    src="" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen>
            </iframe>
        </div>
        
        <!-- Video Caption -->
        <div class="text-center">
            <p class="text-gray-500 dark:text-gray-400 text-sm">
                <i class="fas fa-clock mr-2"></i>Dauer: ca. 3 Minuten · Keine Anmeldung erforderlich
            </p>
        </div>
    </div>
</section>

<!-- Was Sie lernen werden -->
<section class="py-16 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold dark:text-white">Was Sie in der Demo sehen</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-4 max-w-2xl mx-auto">
                Ein kompletter Überblick über alle Funktionen von Leadbusiness
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 shadow-lg">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-magic text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">5-Minuten Onboarding</h3>
                <p class="text-gray-600 dark:text-gray-300">Sehen Sie, wie Sie in nur 8 Schritten Ihr komplettes Empfehlungsprogramm einrichten.</p>
            </div>
            
            <!-- Feature 2 -->
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 shadow-lg">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-cogs text-green-500 text-xl"></i>
                </div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">Automatische Prozesse</h3>
                <p class="text-gray-600 dark:text-gray-300">Erfahren Sie, wie Belohnungen automatisch versendet und Empfehlungen getrackt werden.</p>
            </div>
            
            <!-- Feature 3 -->
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 shadow-lg">
                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-gift text-amber-500 text-xl"></i>
                </div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">Belohnungssystem</h3>
                <p class="text-gray-600 dark:text-gray-300">Entdecken Sie die flexiblen Belohnungsstufen und wie Sie diese anpassen können.</p>
            </div>
            
            <!-- Feature 4 -->
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 shadow-lg">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-chart-bar text-purple-500 text-xl"></i>
                </div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">Dashboard & Analytics</h3>
                <p class="text-gray-600 dark:text-gray-300">Werfen Sie einen Blick auf das Dashboard mit Echtzeit-Statistiken und Analysen.</p>
            </div>
            
            <!-- Feature 5 -->
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 shadow-lg">
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-share-alt text-red-500 text-xl"></i>
                </div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">Share-Funktionen</h3>
                <p class="text-gray-600 dark:text-gray-300">11 verschiedene Wege, wie Ihre Kunden ihren Empfehlungslink teilen können.</p>
            </div>
            
            <!-- Feature 6 -->
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 shadow-lg">
                <div class="w-12 h-12 bg-cyan-100 dark:bg-cyan-900/30 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-trophy text-cyan-500 text-xl"></i>
                </div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">Gamification</h3>
                <p class="text-gray-600 dark:text-gray-300">Leaderboards, Badges und Fortschrittsbalken, die Ihre Kunden motivieren.</p>
            </div>
        </div>
    </div>
</section>

<!-- Warum Leadbusiness -->
<section class="py-16 lg:py-24 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            
            <!-- Text Content -->
            <div>
                <span class="text-blue-600 dark:text-blue-400 font-semibold uppercase tracking-wide">Warum Leadbusiness?</span>
                <h2 class="text-3xl md:text-4xl font-bold mt-3 mb-6 dark:text-white">
                    Das einzige Empfehlungsprogramm, das wirklich automatisch läuft
                </h2>
                
                <div class="space-y-6 text-gray-600 dark:text-gray-300">
                    <p class="text-lg">
                        Andere Anbieter versprechen viel, aber am Ende müssen Sie doch alles selbst machen: 
                        Belohnungen versenden, E-Mails schreiben, Links verwalten...
                    </p>
                    
                    <p class="text-lg">
                        <strong class="text-gray-900 dark:text-white">Bei Leadbusiness ist das anders.</strong> 
                        Nach dem 5-Minuten-Onboarding läuft Ihr Empfehlungsprogramm vollständig automatisch:
                    </p>
                    
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-check text-green-500 text-xs"></i>
                            </div>
                            <span>Automatische E-Mail-Sequenzen aktivieren und motivieren Ihre Empfehler</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-check text-green-500 text-xs"></i>
                            </div>
                            <span>Belohnungen werden automatisch versendet, wenn eine Stufe erreicht wird</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-check text-green-500 text-xs"></i>
                            </div>
                            <span>Gamification-Elemente sorgen für kontinuierliches Engagement</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-check text-green-500 text-xs"></i>
                            </div>
                            <span>Sie konzentrieren sich auf Ihr Geschäft – wir kümmern uns um den Rest</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Stats Box -->
            <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-3xl p-8 lg:p-12 text-white shadow-2xl">
                <h3 class="text-2xl font-bold mb-8">Was unsere Kunden erreichen</h3>
                
                <div class="space-y-8">
                    <div>
                        <div class="flex items-end gap-2 mb-2">
                            <span class="text-5xl font-extrabold">36%</span>
                            <span class="text-white/70 text-lg pb-1">Ø Conversion</span>
                        </div>
                        <p class="text-white/70">Von Klick zu Neukunde – deutlich über Branchendurchschnitt</p>
                    </div>
                    
                    <div class="border-t border-white/20 pt-8">
                        <div class="flex items-end gap-2 mb-2">
                            <span class="text-5xl font-extrabold">47</span>
                            <span class="text-white/70 text-lg pb-1">Ø Neukunden</span>
                        </div>
                        <p class="text-white/70">Pro Unternehmen in den ersten 3 Monaten</p>
                    </div>
                    
                    <div class="border-t border-white/20 pt-8">
                        <div class="flex items-end gap-2 mb-2">
                            <span class="text-5xl font-extrabold">0</span>
                            <span class="text-white/70 text-lg pb-1">Minuten Aufwand</span>
                        </div>
                        <p class="text-white/70">Nach der Einrichtung – alles läuft automatisch</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Branchen Beispiele -->
<section class="py-16 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold dark:text-white">Funktioniert in jeder Branche</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-4 max-w-2xl mx-auto">
                Von lokalen Dienstleistern bis zu Online-Shops – Leadbusiness passt sich Ihrer Branche an
            </p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <?php
            $examples = [
                ['icon' => 'fa-tooth', 'name' => 'Zahnärzte'],
                ['icon' => 'fa-utensils', 'name' => 'Restaurants'],
                ['icon' => 'fa-shopping-cart', 'name' => 'Online-Shops'],
                ['icon' => 'fa-lightbulb', 'name' => 'Coaches'],
                ['icon' => 'fa-hammer', 'name' => 'Handwerker'],
                ['icon' => 'fa-bullhorn', 'name' => 'Agenturen'],
                ['icon' => 'fa-dumbbell', 'name' => 'Fitness'],
                ['icon' => 'fa-cut', 'name' => 'Friseure'],
                ['icon' => 'fa-spa', 'name' => 'Kosmetik'],
                ['icon' => 'fa-laptop-code', 'name' => 'SaaS'],
                ['icon' => 'fa-graduation-cap', 'name' => 'Kurse'],
                ['icon' => 'fa-envelope', 'name' => 'Newsletter'],
            ];
            foreach ($examples as $ex):
            ?>
            <div class="bg-white dark:bg-slate-700 rounded-xl p-4 text-center shadow-sm hover:shadow-md transition-shadow">
                <i class="fas <?= $ex['icon'] ?> text-2xl text-blue-600 dark:text-blue-400 mb-2"></i>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300"><?= $ex['name'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-br from-blue-600 to-blue-800 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-5xl font-extrabold mb-6">
            Bereit, es selbst auszuprobieren?
        </h2>
        <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
            Starten Sie jetzt Ihre 7-tägige Testphase – kostenlos und unverbindlich. Keine Kreditkarte erforderlich.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/onboarding/" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-amber-400 to-orange-500 text-gray-900 font-bold text-lg rounded-full shadow-[0_0_30px_rgba(251,191,36,0.4)] hover:shadow-[0_0_50px_rgba(251,191,36,0.6)] hover:scale-105 transition-all duration-300">
                <span>Jetzt kostenlos starten</span>
                <i class="fas fa-arrow-right"></i>
            </a>
            <a href="/preise" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white/10 backdrop-blur-sm text-white font-bold text-lg rounded-full border-2 border-white/50 hover:bg-white/20 hover:border-white transition-all duration-300">
                <span>Preise ansehen</span>
            </a>
        </div>
        
        <div class="flex flex-wrap justify-center gap-6 mt-10 text-white/70">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle text-green-400"></i>
                <span>7 Tage kostenlos</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle text-green-400"></i>
                <span>Keine Kreditkarte</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle text-green-400"></i>
                <span>Jederzeit kündbar</span>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Mini Section -->
<section class="py-16 bg-white dark:bg-slate-900">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold mb-8 text-center dark:text-white">Häufige Fragen zur Demo</h2>
        
        <div class="space-y-4">
            <div class="faq-item border dark:border-slate-600 rounded-xl p-4 bg-white dark:bg-slate-800">
                <div class="faq-question dark:text-white">
                    <span>Muss ich mich für die Demo anmelden?</span>
                </div>
                <div class="faq-answer text-gray-600 dark:text-gray-300">
                    <p>Nein, Sie können die Demo jederzeit ohne Anmeldung ansehen. Wenn Sie Leadbusiness selbst testen möchten, können Sie eine kostenlose 7-Tage-Testphase starten.</p>
                </div>
            </div>
            
            <div class="faq-item border dark:border-slate-600 rounded-xl p-4 bg-white dark:bg-slate-800">
                <div class="faq-question dark:text-white">
                    <span>Wie lange dauert die Einrichtung wirklich?</span>
                </div>
                <div class="faq-answer text-gray-600 dark:text-gray-300">
                    <p>Die meisten Kunden sind in 5-10 Minuten fertig. Sie beantworten 8 einfache Fragen zu Ihrem Unternehmen, und wir erledigen den Rest automatisch.</p>
                </div>
            </div>
            
            <div class="faq-item border dark:border-slate-600 rounded-xl p-4 bg-white dark:bg-slate-800">
                <div class="faq-question dark:text-white">
                    <span>Kann ich eine persönliche Demo bekommen?</span>
                </div>
                <div class="faq-answer text-gray-600 dark:text-gray-300">
                    <p>Ja! Kontaktieren Sie uns über unser <a href="/kontakt" class="text-blue-600 dark:text-blue-400 hover:underline">Kontaktformular</a> und wir vereinbaren einen persönlichen Demo-Termin mit Ihnen.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../templates/marketing/footer.php'; ?>
