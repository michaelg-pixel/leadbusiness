<?php
/**
 * Leadbusiness - Funktionen
 * Alle Features des Empfehlungsprogramms
 */

$pageTitle = 'Funktionen';
$metaDescription = 'Alle Funktionen von Leadbusiness: Persönliche Empfehlungslinks, automatische Belohnungen, Gamification, 11 Share-Buttons, Live-Statistiken und mehr.';
$currentPage = 'funktionen';

require_once __DIR__ . '/../templates/marketing/header.php';
?>

<!-- Hero Section -->
<section class="py-12 md:py-20 bg-gradient-to-br from-gray-50 to-white dark:from-slate-800 dark:to-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto">
            <span class="text-primary-500 dark:text-primary-400 font-semibold uppercase tracking-wide text-sm">Funktionen</span>
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold mt-3 mb-4 md:mb-6 text-gray-900 dark:text-white">
                Alles für Ihr <span class="gradient-text">Empfehlungsprogramm</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300">
                Ein vollautomatisches System mit allem, was Sie brauchen – 
                fertig konfiguriert und sofort einsatzbereit.
            </p>
        </div>
    </div>
</section>

<!-- Main Features -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Feature 1: Referral Links -->
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center mb-16 md:mb-24">
            <div>
                <div class="inline-flex items-center gap-2 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 px-3 py-1.5 md:px-4 md:py-2 rounded-full text-xs md:text-sm font-semibold mb-4">
                    <i class="fas fa-link"></i> Kernfunktion
                </div>
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-4 md:mb-6 text-gray-900 dark:text-white">Persönliche Empfehlungslinks</h2>
                <p class="text-gray-600 dark:text-gray-300 text-base md:text-lg mb-4 md:mb-6">
                    Jeder Kunde bekommt einen einzigartigen Link, den er mit Freunden, Familie 
                    und Kollegen teilen kann. So können Sie jede Empfehlung exakt nachverfolgen.
                </p>
                <ul class="space-y-3 md:space-y-4">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-green-500 mt-1 flex-shrink-0"></i>
                        <span class="text-gray-700 dark:text-gray-300"><strong class="text-gray-900 dark:text-white">Eindeutige Zuordnung:</strong> Jeder Link ist einzigartig</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-green-500 mt-1 flex-shrink-0"></i>
                        <span class="text-gray-700 dark:text-gray-300"><strong class="text-gray-900 dark:text-white">Kurze URLs:</strong> Leicht zu merken und zu teilen</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-green-500 mt-1 flex-shrink-0"></i>
                        <span class="text-gray-700 dark:text-gray-300"><strong class="text-gray-900 dark:text-white">QR-Code:</strong> Für Offline-Marketing</span>
                    </li>
                </ul>
            </div>
            <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-4 md:p-8">
                <div class="bg-white dark:bg-slate-700 rounded-xl shadow-lg p-4 md:p-6">
                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">Ihr persönlicher Empfehlungslink:</div>
                    <div class="flex items-center gap-2 bg-gray-100 dark:bg-slate-600 rounded-lg p-3 md:p-4">
                        <code class="flex-1 text-primary-600 dark:text-primary-400 font-mono text-xs md:text-sm break-all">zahnarzt-mueller.empfohlen.de/<strong>maria</strong></code>
                        <button class="p-2 hover:bg-gray-200 dark:hover:bg-slate-500 rounded-lg transition-colors flex-shrink-0">
                            <i class="fas fa-copy text-gray-400 dark:text-gray-300"></i>
                        </button>
                    </div>
                    <div class="mt-4 flex items-center gap-4 text-xs md:text-sm text-gray-500 dark:text-gray-400">
                        <span><i class="fas fa-eye mr-1"></i> 127 Klicks</span>
                        <span><i class="fas fa-user-plus mr-1"></i> 8 Empfehlungen</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Feature 2: Automatic Rewards -->
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center mb-16 md:mb-24">
            <div class="order-2 lg:order-1 bg-gray-50 dark:bg-slate-800 rounded-2xl p-4 md:p-8">
                <div class="bg-white dark:bg-slate-700 rounded-xl shadow-lg p-4 md:p-6">
                    <div class="text-center mb-4 md:mb-6">
                        <div class="text-5xl md:text-6xl mb-2">🎉</div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">Belohnung freigeschaltet!</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Du hast Stufe 2 erreicht</p>
                    </div>
                    <div class="bg-gradient-to-r from-yellow-100 to-yellow-50 dark:from-yellow-900/30 dark:to-yellow-800/20 rounded-lg p-3 md:p-4 border border-yellow-200 dark:border-yellow-700/50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 md:w-12 md:h-12 bg-yellow-400 rounded-full flex items-center justify-center text-xl md:text-2xl flex-shrink-0">
                                🎁
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 dark:text-white text-sm md:text-base">50€ Gutschein</div>
                                <div class="text-xs md:text-sm text-gray-600 dark:text-gray-300">Für deine nächste Behandlung</div>
                            </div>
                        </div>
                    </div>
                    <p class="text-center text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-4">
                        Dein Gutschein-Code wurde dir per E-Mail zugesendet.
                    </p>
                </div>
            </div>
            <div class="order-1 lg:order-2">
                <div class="inline-flex items-center gap-2 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 px-3 py-1.5 md:px-4 md:py-2 rounded-full text-xs md:text-sm font-semibold mb-4">
                    <i class="fas fa-gift"></i> Automatisierung
                </div>
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-4 md:mb-6 text-gray-900 dark:text-white">Automatische Belohnungen</h2>
                <p class="text-gray-600 dark:text-gray-300 text-base md:text-lg mb-4 md:mb-6">
                    Definieren Sie Belohnungsstufen – das System kümmert sich um den Rest. 
                    Sobald ein Empfehler eine Stufe erreicht, wird die Belohnung automatisch versendet.
                </p>
                <ul class="space-y-3 md:space-y-4">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-green-500 mt-1 flex-shrink-0"></i>
                        <span class="text-gray-700 dark:text-gray-300"><strong class="text-gray-900 dark:text-white">6 Belohnungstypen:</strong> Rabatte, Gutscheine, Downloads...</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-green-500 mt-1 flex-shrink-0"></i>
                        <span class="text-gray-700 dark:text-gray-300"><strong class="text-gray-900 dark:text-white">E-Mail-Benachrichtigung:</strong> Automatisch informiert</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-green-500 mt-1 flex-shrink-0"></i>
                        <span class="text-gray-700 dark:text-gray-300"><strong class="text-gray-900 dark:text-white">Branchen-Presets:</strong> Passende Vorschläge</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Feature 3: Gamification -->
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center mb-16 md:mb-24">
            <div>
                <div class="inline-flex items-center gap-2 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 px-3 py-1.5 md:px-4 md:py-2 rounded-full text-xs md:text-sm font-semibold mb-4">
                    <i class="fas fa-trophy"></i> Motivation
                </div>
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-4 md:mb-6 text-gray-900 dark:text-white">Gamification-System</h2>
                <p class="text-gray-600 dark:text-gray-300 text-base md:text-lg mb-4 md:mb-6">
                    Spielerische Elemente motivieren Ihre Kunden, aktiv zu bleiben und 
                    mehr zu empfehlen. Punkte sammeln, Badges verdienen, im Leaderboard aufsteigen.
                </p>
                <ul class="space-y-3 md:space-y-4">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-green-500 mt-1 flex-shrink-0"></i>
                        <span class="text-gray-700 dark:text-gray-300"><strong class="text-gray-900 dark:text-white">Fortschrittsbalken:</strong> Bis zur nächsten Stufe</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-green-500 mt-1 flex-shrink-0"></i>
                        <span class="text-gray-700 dark:text-gray-300"><strong class="text-gray-900 dark:text-white">9 Badges:</strong> Achievements zum Sammeln</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-green-500 mt-1 flex-shrink-0"></i>
                        <span class="text-gray-700 dark:text-gray-300"><strong class="text-gray-900 dark:text-white">Leaderboard:</strong> Top 10 Empfehler</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-green-500 mt-1 flex-shrink-0"></i>
                        <span class="text-gray-700 dark:text-gray-300"><strong class="text-gray-900 dark:text-white">Confetti:</strong> Feiern bei neuen Stufen</span>
                    </li>
                </ul>
            </div>
            <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-4 md:p-8">
                <div class="bg-white dark:bg-slate-700 rounded-xl shadow-lg p-4 md:p-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4">Deine Badges</h3>
                    <div class="grid grid-cols-3 gap-2 md:gap-4">
                        <?php
                        $badges = [
                            ['icon' => '🌱', 'name' => 'Erster Schritt', 'earned' => true],
                            ['icon' => '⭐', 'name' => '5er Club', 'earned' => true],
                            ['icon' => '🌟', 'name' => '10er Club', 'earned' => true],
                            ['icon' => '🚀', 'name' => 'Super-Werber', 'earned' => false],
                            ['icon' => '🔥', 'name' => 'Durchstarter', 'earned' => true],
                            ['icon' => '👑', 'name' => 'Legende', 'earned' => false],
                        ];
                        foreach ($badges as $badge):
                        ?>
                        <div class="text-center p-2 md:p-3 rounded-xl <?= $badge['earned'] ? 'bg-primary-50 dark:bg-primary-900/30' : 'bg-gray-100 dark:bg-slate-600 opacity-50' ?>">
                            <div class="text-xl md:text-2xl mb-1"><?= $badge['icon'] ?></div>
                            <div class="text-xs font-medium <?= $badge['earned'] ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 dark:text-gray-500' ?>"><?= $badge['name'] ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4 text-center text-xs md:text-sm text-gray-500 dark:text-gray-400">
                        4 von 9 Badges verdient
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Feature 4: Share Buttons -->
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            <div class="order-2 lg:order-1 bg-gray-50 dark:bg-slate-800 rounded-2xl p-4 md:p-8">
                <div class="bg-white dark:bg-slate-700 rounded-xl shadow-lg p-4 md:p-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4">Link teilen via:</h3>
                    <div class="grid grid-cols-4 gap-2 md:gap-3">
                        <button class="p-3 md:p-4 bg-green-500 text-white rounded-xl hover:bg-green-600 transition-colors">
                            <i class="fab fa-whatsapp text-xl md:text-2xl"></i>
                        </button>
                        <button class="p-3 md:p-4 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
                            <i class="fab fa-facebook text-xl md:text-2xl"></i>
                        </button>
                        <button class="p-3 md:p-4 bg-sky-500 text-white rounded-xl hover:bg-sky-600 transition-colors">
                            <i class="fab fa-telegram text-xl md:text-2xl"></i>
                        </button>
                        <button class="p-3 md:p-4 bg-gray-700 text-white rounded-xl hover:bg-gray-800 transition-colors">
                            <i class="fas fa-envelope text-xl md:text-2xl"></i>
                        </button>
                        <button class="p-3 md:p-4 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-colors">
                            <i class="fab fa-linkedin text-xl md:text-2xl"></i>
                        </button>
                        <button class="p-3 md:p-4 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-colors">
                            <i class="fab fa-xing text-xl md:text-2xl"></i>
                        </button>
                        <button class="p-3 md:p-4 bg-black text-white rounded-xl hover:bg-gray-900 transition-colors">
                            <i class="fab fa-x-twitter text-xl md:text-2xl"></i>
                        </button>
                        <button class="p-3 md:p-4 bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-300 dark:hover:bg-slate-500 transition-colors">
                            <i class="fas fa-copy text-xl md:text-2xl"></i>
                        </button>
                    </div>
                    <div class="mt-4 text-center">
                        <button class="text-primary-500 dark:text-primary-400 font-medium text-sm">
                            <i class="fas fa-qrcode mr-1"></i> QR-Code anzeigen
                        </button>
                    </div>
                </div>
            </div>
            <div class="order-1 lg:order-2">
                <div class="inline-flex items-center gap-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-3 py-1.5 md:px-4 md:py-2 rounded-full text-xs md:text-sm font-semibold mb-4">
                    <i class="fas fa-share-alt"></i> Verbreitung
                </div>
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-4 md:mb-6 text-gray-900 dark:text-white">11 Share-Buttons</h2>
                <p class="text-gray-600 dark:text-gray-300 text-base md:text-lg mb-4 md:mb-6">
                    Mit einem Klick teilen – auf der Plattform, die Ihre Kunden am liebsten nutzen.
                    Von WhatsApp bis LinkedIn, von E-Mail bis QR-Code.
                </p>
                <div class="grid grid-cols-2 gap-3 md:gap-4 text-sm md:text-base text-gray-600 dark:text-gray-300">
                    <div class="flex items-center gap-2">
                        <i class="fab fa-whatsapp text-green-500"></i> WhatsApp
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fab fa-facebook text-blue-600"></i> Facebook
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fab fa-telegram text-sky-500"></i> Telegram
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-envelope text-gray-700 dark:text-gray-400"></i> E-Mail
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-comment-sms text-green-600"></i> SMS
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fab fa-linkedin text-blue-500"></i> LinkedIn
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fab fa-xing text-teal-600"></i> XING
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fab fa-x-twitter text-gray-800 dark:text-gray-300"></i> Twitter/X
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fab fa-pinterest text-red-500"></i> Pinterest
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-copy text-gray-500"></i> Link kopieren
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-qrcode text-gray-700 dark:text-gray-400"></i> QR-Code
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- More Features Grid -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 md:mb-16">
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">Und noch viel mehr...</h2>
        </div>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-8">
            <!-- Feature: Email Sequences -->
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-5 md:p-6 shadow-sm hover:shadow-lg transition-shadow">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center text-purple-500 text-lg md:text-xl mb-3 md:mb-4">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <h3 class="text-base md:text-lg font-bold mb-2 text-gray-900 dark:text-white">E-Mail-Sequenzen</h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm md:text-base">
                    Automatische E-Mails bei Anmeldung, Reminder bei Inaktivität, 
                    Glückwünsche bei neuen Stufen.
                </p>
            </div>
            
            <!-- Feature: Industry Backgrounds -->
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-5 md:p-6 shadow-sm hover:shadow-lg transition-shadow">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-pink-100 dark:bg-pink-900/30 rounded-xl flex items-center justify-center text-pink-500 text-lg md:text-xl mb-3 md:mb-4">
                    <i class="fas fa-image"></i>
                </div>
                <h3 class="text-base md:text-lg font-bold mb-2 text-gray-900 dark:text-white">Branchen-Designs</h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm md:text-base">
                    Professionelle Hintergrundbilder passend zu Ihrer Branche. 
                    Zahnarzt, Friseur, Fitness, Coach und mehr.
                </p>
            </div>
            
            <!-- Feature: Live Counter -->
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-5 md:p-6 shadow-sm hover:shadow-lg transition-shadow">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center text-green-500 text-lg md:text-xl mb-3 md:mb-4">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="text-base md:text-lg font-bold mb-2 text-gray-900 dark:text-white">Live-Counter</h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm md:text-base">
                    "247 Personen nehmen bereits teil" – Social Proof motiviert 
                    neue Besucher zur Anmeldung.
                </p>
            </div>
            
            <!-- Feature: Share Graphics -->
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-5 md:p-6 shadow-sm hover:shadow-lg transition-shadow">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center text-orange-500 text-lg md:text-xl mb-3 md:mb-4">
                    <i class="fas fa-paint-brush"></i>
                </div>
                <h3 class="text-base md:text-lg font-bold mb-2 text-gray-900 dark:text-white">Share-Grafiken</h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm md:text-base">
                    Automatisch generierte Bilder mit Ihrem Logo für 
                    Social Media Posts und Stories.
                </p>
            </div>
            
            <!-- Feature: Double Opt-In -->
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-5 md:p-6 shadow-sm hover:shadow-lg transition-shadow">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-500 text-lg md:text-xl mb-3 md:mb-4">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="text-base md:text-lg font-bold mb-2 text-gray-900 dark:text-white">Double Opt-In</h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm md:text-base">
                    DSGVO-konforme E-Mail-Bestätigung. Alle rechtlichen 
                    Anforderungen sind abgedeckt.
                </p>
            </div>
            
            <!-- Feature: Anti-Spam -->
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-5 md:p-6 shadow-sm hover:shadow-lg transition-shadow">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center text-red-500 text-lg md:text-xl mb-3 md:mb-4">
                    <i class="fas fa-ban"></i>
                </div>
                <h3 class="text-base md:text-lg font-bold mb-2 text-gray-900 dark:text-white">Anti-Spam-Schutz</h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm md:text-base">
                    Bot-Erkennung, Wegwerf-E-Mail-Blocker und Fraud-Detection 
                    schützen vor Missbrauch.
                </p>
            </div>
            
            <!-- Feature: Dashboard -->
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-5 md:p-6 shadow-sm hover:shadow-lg transition-shadow">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-indigo-500 text-lg md:text-xl mb-3 md:mb-4">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3 class="text-base md:text-lg font-bold mb-2 text-gray-900 dark:text-white">Dashboard</h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm md:text-base">
                    Alle Statistiken auf einen Blick: Empfehler, Klicks, 
                    Conversions, Belohnungen.
                </p>
            </div>
            
            <!-- Feature: Subdomain -->
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-5 md:p-6 shadow-sm hover:shadow-lg transition-shadow">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-cyan-100 dark:bg-cyan-900/30 rounded-xl flex items-center justify-center text-cyan-500 text-lg md:text-xl mb-3 md:mb-4">
                    <i class="fas fa-globe"></i>
                </div>
                <h3 class="text-base md:text-lg font-bold mb-2 text-gray-900 dark:text-white">Eigene Subdomain</h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm md:text-base">
                    Ihre persönliche URL: firma-name.empfohlen.de – 
                    professionell und vertrauenswürdig.
                </p>
            </div>
            
            <!-- Feature: API (Pro) -->
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-5 md:p-6 shadow-sm hover:shadow-lg transition-shadow relative overflow-hidden">
                <span class="absolute top-2 right-2 bg-primary-500 text-white text-xs px-2 py-1 rounded-full">Pro</span>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-gray-100 dark:bg-slate-600 rounded-xl flex items-center justify-center text-gray-500 dark:text-gray-400 text-lg md:text-xl mb-3 md:mb-4">
                    <i class="fas fa-code"></i>
                </div>
                <h3 class="text-base md:text-lg font-bold mb-2 text-gray-900 dark:text-white">API & Webhooks</h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm md:text-base">
                    Integration in Ihre bestehenden Systeme via API 
                    und Echtzeit-Webhooks.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Comparison Table -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 md:mb-16">
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">Starter vs. Professional</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-3 md:mt-4">Wählen Sie den Plan, der zu Ihnen passt</p>
        </div>
        
        <!-- Mobile Card View -->
        <div class="md:hidden space-y-6">
            <!-- Starter Card -->
            <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Starter</h3>
                <div class="text-3xl font-bold text-gray-900 dark:text-white mb-6">49€<span class="text-base font-normal text-gray-500">/Monat</span></div>
                <ul class="space-y-3 text-sm">
                    <li class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Empfehler</span><span class="font-medium text-gray-900 dark:text-white">Bis 200</span></li>
                    <li class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Belohnungsstufen</span><span class="font-medium text-gray-900 dark:text-white">3</span></li>
                    <li class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">E-Mails inklusive</span><i class="fas fa-check text-green-500"></i></li>
                    <li class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Share-Buttons</span><i class="fas fa-check text-green-500"></i></li>
                    <li class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Gamification</span><span class="font-medium text-gray-900 dark:text-white">Basis</span></li>
                    <li class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Mehrere Kampagnen</span><i class="fas fa-times text-gray-300 dark:text-gray-600"></i></li>
                    <li class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Lead-Export</span><i class="fas fa-times text-gray-300 dark:text-gray-600"></i></li>
                    <li class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">API & Webhooks</span><i class="fas fa-times text-gray-300 dark:text-gray-600"></i></li>
                    <li class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Support</span><span class="font-medium text-gray-900 dark:text-white">E-Mail</span></li>
                </ul>
            </div>
            
            <!-- Professional Card -->
            <div class="bg-primary-50 dark:bg-primary-900/20 border-2 border-primary-500 rounded-2xl p-6 relative">
                <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-primary-500 text-white text-xs font-bold px-3 py-1 rounded-full">BELIEBT</span>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Professional</h3>
                <div class="text-3xl font-bold text-primary-600 dark:text-primary-400 mb-6">99€<span class="text-base font-normal text-gray-500 dark:text-gray-400">/Monat</span></div>
                <ul class="space-y-3 text-sm">
                    <li class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Empfehler</span><span class="font-medium text-gray-900 dark:text-white">Bis 5.000</span></li>
                    <li class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Belohnungsstufen</span><span class="font-medium text-gray-900 dark:text-white">5</span></li>
                    <li class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">E-Mails inklusive</span><i class="fas fa-check text-green-500"></i></li>
                    <li class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Share-Buttons</span><i class="fas fa-check text-green-500"></i></li>
                    <li class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Gamification</span><span class="font-medium text-gray-900 dark:text-white">Erweitert</span></li>
                    <li class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Mehrere Kampagnen</span><i class="fas fa-check text-green-500"></i></li>
                    <li class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Lead-Export</span><i class="fas fa-check text-green-500"></i></li>
                    <li class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">API & Webhooks</span><i class="fas fa-check text-green-500"></i></li>
                    <li class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Support</span><span class="font-medium text-gray-900 dark:text-white">Priorität</span></li>
                </ul>
            </div>
        </div>
        
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b-2 border-gray-200 dark:border-slate-700">
                        <th class="text-left py-4 pr-4 text-gray-900 dark:text-white">Funktion</th>
                        <th class="text-center py-4 px-4 text-gray-900 dark:text-white">Starter</th>
                        <th class="text-center py-4 px-4 bg-primary-50 dark:bg-primary-900/20 text-gray-900 dark:text-white rounded-t-lg">Professional</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 dark:text-gray-300">
                    <tr class="border-b border-gray-200 dark:border-slate-700">
                        <td class="py-4 pr-4">Empfehler</td>
                        <td class="text-center py-4 px-4">Bis 200</td>
                        <td class="text-center py-4 px-4 bg-primary-50 dark:bg-primary-900/20">Bis 5.000</td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-slate-700">
                        <td class="py-4 pr-4">Belohnungsstufen</td>
                        <td class="text-center py-4 px-4">3</td>
                        <td class="text-center py-4 px-4 bg-primary-50 dark:bg-primary-900/20">5</td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-slate-700">
                        <td class="py-4 pr-4">E-Mails inklusive</td>
                        <td class="text-center py-4 px-4"><i class="fas fa-check text-green-500"></i></td>
                        <td class="text-center py-4 px-4 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-slate-700">
                        <td class="py-4 pr-4">Share-Buttons</td>
                        <td class="text-center py-4 px-4"><i class="fas fa-check text-green-500"></i></td>
                        <td class="text-center py-4 px-4 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-slate-700">
                        <td class="py-4 pr-4">Gamification</td>
                        <td class="text-center py-4 px-4">Basis</td>
                        <td class="text-center py-4 px-4 bg-primary-50 dark:bg-primary-900/20">Erweitert</td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-slate-700">
                        <td class="py-4 pr-4">Mehrere Kampagnen</td>
                        <td class="text-center py-4 px-4"><i class="fas fa-times text-gray-300 dark:text-gray-600"></i></td>
                        <td class="text-center py-4 px-4 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-slate-700">
                        <td class="py-4 pr-4">Lead-Export</td>
                        <td class="text-center py-4 px-4"><i class="fas fa-times text-gray-300 dark:text-gray-600"></i></td>
                        <td class="text-center py-4 px-4 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-slate-700">
                        <td class="py-4 pr-4">API & Webhooks</td>
                        <td class="text-center py-4 px-4"><i class="fas fa-times text-gray-300 dark:text-gray-600"></i></td>
                        <td class="text-center py-4 px-4 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-slate-700">
                        <td class="py-4 pr-4">Eigenes Hintergrundbild</td>
                        <td class="text-center py-4 px-4"><i class="fas fa-times text-gray-300 dark:text-gray-600"></i></td>
                        <td class="text-center py-4 px-4 bg-primary-50 dark:bg-primary-900/20"><i class="fas fa-check text-green-500"></i></td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-slate-700">
                        <td class="py-4 pr-4">Support</td>
                        <td class="text-center py-4 px-4">E-Mail</td>
                        <td class="text-center py-4 px-4 bg-primary-50 dark:bg-primary-900/20">Priorität</td>
                    </tr>
                    <tr>
                        <td class="py-4 pr-4 font-bold text-gray-900 dark:text-white">Monatlich</td>
                        <td class="text-center py-4 px-4 font-bold text-gray-900 dark:text-white">49€</td>
                        <td class="text-center py-4 px-4 bg-primary-50 dark:bg-primary-900/20 font-bold text-primary-600 dark:text-primary-400 rounded-b-lg">99€</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="text-center mt-8">
            <a href="/preise" class="btn-primary inline-flex items-center gap-2">
                Alle Details vergleichen <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-12 md:py-20 gradient-bg text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold mb-4 md:mb-6">
            Überzeugt? Starten Sie jetzt!
        </h2>
        <p class="text-lg md:text-xl text-white/90 mb-6 md:mb-8">
            14 Tage kostenlos testen – keine Kreditkarte erforderlich.
        </p>
        <a href="/onboarding" class="btn-primary btn-large bg-white text-primary-600 hover:bg-gray-100 inline-flex items-center gap-2">
            <span>Jetzt starten</span>
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</section>

<?php require_once __DIR__ . '/../templates/marketing/footer.php'; ?>
