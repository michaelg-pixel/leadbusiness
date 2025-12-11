<?php
/**
 * Case Study: Friseursalon Berlin
 */

$pageTitle = 'Case Study: Kundenstamm verdoppelt in 4 Monaten – Friseursalon Berlin';
$metaDescription = 'Wie ein Berliner Friseursalon mit Leadbusiness 156 Neukunden gewann und seinen Kundenstamm verdoppelte. Die komplette Erfolgsgeschichte.';
$currentPage = 'wissen';

require_once __DIR__ . '/../../templates/marketing/header.php';
?>

<!-- Hero Section -->
<section class="relative py-16 md:py-20 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-800 to-slate-900"></div>
    <div class="absolute inset-0 opacity-20">
        <div class="absolute top-10 left-10 w-40 h-40 bg-pink-500 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 right-10 w-60 h-60 bg-emerald-500 rounded-full blur-3xl"></div>
    </div>
    
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <a href="/wissen" class="inline-flex items-center gap-2 text-white/80 hover:text-white mb-6 transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Zurück zur Übersicht</span>
            </a>
            
            <div class="inline-flex items-center gap-2 bg-emerald-500/20 backdrop-blur-sm px-4 py-2 rounded-full mb-6">
                <i class="fas fa-chart-line text-emerald-400"></i>
                <span class="text-sm font-medium text-emerald-300">Case Study</span>
            </div>
            
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white mb-6 leading-tight">
                Kundenstamm verdoppelt in 4 Monaten
            </h1>
            
            <p class="text-lg md:text-xl text-white/80 mb-8 leading-relaxed">
                Wie ein Berliner Friseursalon mit der Kraft von Empfehlungen 
                156 Neukunden gewann – und eine treue Community aufbaute.
            </p>
            
            <!-- Key Stats -->
            <div class="grid grid-cols-3 gap-4 max-w-lg mx-auto">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <div class="text-2xl md:text-3xl font-bold text-emerald-400">+156</div>
                    <div class="text-xs text-white/60">Neukunden</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <div class="text-2xl md:text-3xl font-bold text-emerald-400">312</div>
                    <div class="text-xs text-white/60">Aktive Empfehler</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <div class="text-2xl md:text-3xl font-bold text-emerald-400">42%</div>
                    <div class="text-xs text-white/60">Conversion</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Content Section -->
<article class="py-12 md:py-16 bg-white dark:bg-slate-900">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Company Info -->
        <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-6 mb-12 flex flex-col md:flex-row gap-6 items-center">
            <div class="w-20 h-20 bg-pink-100 dark:bg-pink-900/30 rounded-2xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-cut text-pink-600 dark:text-pink-400 text-3xl"></i>
            </div>
            <div class="text-center md:text-left">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Salon "Style & Cut"*</h2>
                <p class="text-gray-600 dark:text-gray-400">Berlin-Prenzlauer Berg · 4 Stylisten · Inhabergeführt</p>
                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">*Name auf Wunsch geändert</p>
            </div>
        </div>
        
        <!-- Die Ausgangssituation -->
        <section class="mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-6">
                Die Ausgangssituation
            </h2>
            
            <div class="prose prose-lg dark:prose-invert max-w-none">
                <p>
                    Sandra B. eröffnete ihren Salon vor drei Jahren. Trotz guter Lage und zufriedener Kunden 
                    wuchs der Salon langsamer als erhofft. Instagram-Werbung brachte zwar Follower, 
                    aber kaum echte Kunden in den Stuhl.
                </p>
                
                <blockquote class="border-l-4 border-pink-500 pl-4 italic text-gray-700 dark:text-gray-300">
                    "Meine Kundinnen haben mich ständig gelobt und gesagt, sie würden mich weiterempfehlen. 
                    Aber irgendwie kam da nie viel bei rum. Ich wusste nicht, wie ich das aktivieren kann."
                </blockquote>
            </div>
            
            <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-5 mt-6">
                <h4 class="font-bold text-red-800 dark:text-red-300 mb-2">Die Herausforderungen:</h4>
                <ul class="text-red-700 dark:text-red-400 space-y-1 text-sm">
                    <li>• Langsames Wachstum trotz guter Bewertungen</li>
                    <li>• Instagram-Ads teuer und ineffektiv (150€+ pro Neukunde)</li>
                    <li>• Keine Systematik für Empfehlungen</li>
                    <li>• Hohe No-Show-Rate bei Neukunden aus Werbung</li>
                </ul>
            </div>
        </section>
        
        <!-- Die Lösung -->
        <section class="mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-6">
                Die Lösung: Ein Empfehlungsprogramm, das zur Marke passt
            </h2>
            
            <div class="prose prose-lg dark:prose-invert max-w-none">
                <p>
                    Sandra entschied sich für Leadbusiness, weil es sich nahtlos in ihren Workflow 
                    integrieren ließ. Besonders wichtig: Die Belohnungen sollten zum Premium-Anspruch 
                    des Salons passen.
                </p>
            </div>
            
            <div class="bg-pink-50 dark:bg-pink-900/20 rounded-xl p-6 my-6">
                <h4 class="font-bold text-gray-900 dark:text-white mb-4">Die gewählten Belohnungen:</h4>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-pink-600 rounded-full flex items-center justify-center text-white font-bold text-sm">1</div>
                        <span class="text-gray-700 dark:text-gray-300">Gratis Haarkur beim nächsten Besuch (Wert: 15€)</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-pink-600 rounded-full flex items-center justify-center text-white font-bold text-sm">3</div>
                        <span class="text-gray-700 dark:text-gray-300">20% Rabatt auf Färbung oder Strähnen</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-pink-600 rounded-full flex items-center justify-center text-white font-bold text-sm">5</div>
                        <span class="text-gray-700 dark:text-gray-300">Gratis Haarschnitt (Wert: 45€)</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-pink-600 rounded-full flex items-center justify-center text-white font-bold text-sm">10</div>
                        <span class="text-gray-700 dark:text-gray-300">VIP-Styling-Paket mit Kopfmassage & Premium-Pflege</span>
                    </div>
                </div>
            </div>
            
            <div class="prose prose-lg dark:prose-invert max-w-none">
                <h3>Der kreative Twist: Instagram + Empfehlungen</h3>
                <p>
                    Sandra kombinierte ihr Empfehlungsprogramm mit Instagram: Nach jedem Styling machte 
                    sie (mit Erlaubnis) ein Foto und bot an, es zu teilen. Der perfekte Moment, um auch 
                    den Empfehlungslink zu erwähnen.
                </p>
                <p>
                    "Deine Freundin findet deine Haare bestimmt auch toll – hier ist dein Link, 
                    falls sie auch mal kommen möchte!"
                </p>
            </div>
        </section>
        
        <!-- Die Ergebnisse -->
        <section class="mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-6">
                Die Ergebnisse nach 4 Monaten
            </h2>
            
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-6">
                    <div class="text-4xl font-bold text-emerald-600 dark:text-emerald-400 mb-2">156</div>
                    <div class="font-semibold text-gray-900 dark:text-white">Neukunden durch Empfehlungen</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kundenstamm von 150 auf 306 gewachsen</div>
                </div>
                <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-6">
                    <div class="text-4xl font-bold text-emerald-600 dark:text-emerald-400 mb-2">312</div>
                    <div class="font-semibold text-gray-900 dark:text-white">Aktive Empfehler</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Fast jede Kundin teilt mindestens 1x</div>
                </div>
                <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-6">
                    <div class="text-4xl font-bold text-emerald-600 dark:text-emerald-400 mb-2">42%</div>
                    <div class="font-semibold text-gray-900 dark:text-white">Conversion Rate</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Sehr hoch durch persönliche Empfehlungen</div>
                </div>
                <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-6">
                    <div class="text-4xl font-bold text-emerald-600 dark:text-emerald-400 mb-2">8€</div>
                    <div class="font-semibold text-gray-900 dark:text-white">Kosten pro Neukunde</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">vs. 150€+ bei Instagram Ads (-95%)</div>
                </div>
            </div>
            
            <div class="bg-pink-50 dark:bg-pink-900/20 border-l-4 border-pink-500 p-5">
                <h4 class="font-bold text-pink-800 dark:text-pink-300 mb-2">Bonus-Effekt: Weniger No-Shows</h4>
                <p class="text-pink-700 dark:text-pink-400 mb-0">
                    Empfohlene Kunden erscheinen zuverlässiger zu Terminen. Die No-Show-Rate sank von 15% auf unter 3%.
                </p>
            </div>
        </section>
        
        <!-- Zitat -->
        <section class="mb-12">
            <div class="bg-gradient-to-br from-pink-50 to-pink-100 dark:from-slate-800 dark:to-slate-700 rounded-2xl p-8">
                <div class="flex justify-center gap-1 text-yellow-400 mb-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <blockquote class="text-xl md:text-2xl font-medium text-gray-900 dark:text-white text-center mb-6 leading-relaxed">
                    "Meine Kundinnen lieben das Punktesystem! Sie teilen ihren Link aktiv und freuen sich 
                    riesig, wenn sie eine Stufe erreichen. Es fühlt sich wie ein Spiel an – aber mit echten Vorteilen."
                </blockquote>
                <div class="text-center">
                    <div class="font-bold text-gray-900 dark:text-white">Sandra B.</div>
                    <div class="text-gray-600 dark:text-gray-400">Inhaberin, Salon "Style & Cut"</div>
                </div>
            </div>
        </section>
        
        <!-- Der Gamification-Effekt -->
        <section class="mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-6">
                Der Gamification-Effekt
            </h2>
            
            <div class="prose prose-lg dark:prose-invert max-w-none">
                <p>
                    Was Sandra besonders überraschte: Viele Kundinnen fragen aktiv nach ihrem 
                    Punktestand. Das Stufensystem motiviert, "noch eine Freundin" einzuladen, 
                    um das nächste Level zu erreichen.
                </p>
            </div>
            
            <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-6 my-6">
                <h4 class="font-bold text-gray-900 dark:text-white mb-4">Top-Empfehlerinnen nach 4 Monaten:</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center text-yellow-900 font-bold text-sm">🥇</div>
                            <span class="text-gray-700 dark:text-gray-300">Maria K.</span>
                        </div>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">12 Empfehlungen</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-gray-700 font-bold text-sm">🥈</div>
                            <span class="text-gray-700 dark:text-gray-300">Lisa M.</span>
                        </div>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">9 Empfehlungen</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-amber-600 rounded-full flex items-center justify-center text-white font-bold text-sm">🥉</div>
                            <span class="text-gray-700 dark:text-gray-300">Anna S.</span>
                        </div>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">7 Empfehlungen</span>
                    </div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-4 mb-0">
                    Maria hat allein 12 neue Kundinnen gebracht – das sind über 1.000€ Umsatz, 
                    bei Belohnungskosten von ca. 100€.
                </p>
            </div>
        </section>
        
        <!-- CTA Box -->
        <div class="bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl p-8 text-center text-white">
            <h3 class="text-2xl font-bold mb-4">Ähnliche Ergebnisse für Ihren Salon?</h3>
            <p class="text-pink-100 mb-6">
                Starten Sie noch heute Ihr eigenes Empfehlungsprogramm.
            </p>
            <a href="/onboarding" class="inline-flex items-center gap-2 bg-white text-pink-600 px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition-all">
                <span>Jetzt 7 Tage kostenlos testen</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
    </div>
</article>

<!-- Related Content -->
<section class="py-12 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Weitere Case Studies</h2>
        
        <div class="grid md:grid-cols-2 gap-6">
            <a href="/wissen/case-study-zahnarztpraxis" class="bg-white dark:bg-slate-700 rounded-xl p-5 hover:shadow-lg transition-shadow">
                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">Zahnarzt</span>
                <h3 class="font-bold text-gray-900 dark:text-white mt-2">89 Neupatienten in 6 Monaten</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Wie eine Münchner Praxis ihr Wachstum verdreifachte.</p>
            </a>
            
            <a href="/wissen/case-study-fitnessstudio" class="bg-white dark:bg-slate-700 rounded-xl p-5 hover:shadow-lg transition-shadow">
                <span class="text-xs font-semibold text-orange-600 dark:text-orange-400">Fitness</span>
                <h3 class="font-bold text-gray-900 dark:text-white mt-2">73% weniger Akquisekosten</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Wie ein Hamburger Studio 203 neue Mitglieder gewann.</p>
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../templates/marketing/footer.php'; ?>
