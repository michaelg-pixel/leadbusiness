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
<section class="py-20 bg-gradient-to-br from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto">
            <span class="text-primary-500 font-semibold uppercase tracking-wide">Funktionen</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mt-3 mb-6">
                Alles für Ihr <span class="gradient-text">Empfehlungsprogramm</span>
            </h1>
            <p class="text-xl text-gray-600">
                Ein vollautomatisches System mit allem, was Sie brauchen – 
                fertig konfiguriert und sofort einsatzbereit.
            </p>
        </div>
    </div>
</section>

<!-- Main Features -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Feature 1: Referral Links -->
        <div class="grid lg:grid-cols-2 gap-16 items-center mb-24">
            <div>
                <div class="inline-flex items-center gap-2 bg-primary-100 text-primary-600 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    <i class="fas fa-link"></i> Kernfunktion
                </div>
                <h2 class="text-3xl md:text-4xl font-bold mb-6">Persönliche Empfehlungslinks</h2>
                <p class="text-gray-600 text-lg mb-6">
                    Jeder Kunde bekommt einen einzigartigen Link, den er mit Freunden, Familie 
                    und Kollegen teilen kann. So können Sie jede Empfehlung exakt nachverfolgen.
                </p>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span><strong>Eindeutige Zuordnung:</strong> Jeder Link ist einzigartig</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span><strong>Kurze URLs:</strong> Leicht zu merken und zu teilen</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span><strong>QR-Code:</strong> Für Offline-Marketing (Flyer, Visitenkarten)</span>
                    </li>
                </ul>
            </div>
            <div class="bg-gray-50 rounded-2xl p-8">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="text-sm text-gray-500 mb-2">Ihr persönlicher Empfehlungslink:</div>
                    <div class="flex items-center gap-2 bg-gray-100 rounded-lg p-4">
                        <code class="flex-1 text-primary-600 font-mono">zahnarzt-mueller.empfohlen.de/<strong>maria</strong></code>
                        <button class="p-2 hover:bg-gray-200 rounded-lg transition-colors">
                            <i class="fas fa-copy text-gray-400"></i>
                        </button>
                    </div>
                    <div class="mt-4 flex items-center gap-4 text-sm text-gray-500">
                        <span><i class="fas fa-eye mr-1"></i> 127 Klicks</span>
                        <span><i class="fas fa-user-plus mr-1"></i> 8 Empfehlungen</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Feature 2: Automatic Rewards -->
        <div class="grid lg:grid-cols-2 gap-16 items-center mb-24">
            <div class="order-2 lg:order-1 bg-gray-50 rounded-2xl p-8">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="text-center mb-6">
                        <div class="text-6xl mb-2">🎉</div>
                        <h3 class="text-xl font-bold text-gray-900">Belohnung freigeschaltet!</h3>
                        <p class="text-gray-500">Du hast Stufe 2 erreicht</p>
                    </div>
                    <div class="bg-gradient-to-r from-yellow-100 to-yellow-50 rounded-lg p-4 border border-yellow-200">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-yellow-400 rounded-full flex items-center justify-center text-2xl">
                                🎁
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">50€ Gutschein</div>
                                <div class="text-sm text-gray-600">Für deine nächste Behandlung</div>
                            </div>
                        </div>
                    </div>
                    <p class="text-center text-sm text-gray-500 mt-4">
                        Dein Gutschein-Code wurde dir per E-Mail zugesendet.
                    </p>
                </div>
            </div>
            <div class="order-1 lg:order-2">
                <div class="inline-flex items-center gap-2 bg-green-100 text-green-600 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    <i class="fas fa-gift"></i> Automatisierung
                </div>
                <h2 class="text-3xl md:text-4xl font-bold mb-6">Automatische Belohnungen</h2>
                <p class="text-gray-600 text-lg mb-6">
                    Definieren Sie Belohnungsstufen – das System kümmert sich um den Rest. 
                    Sobald ein Empfehler eine Stufe erreicht, wird die Belohnung automatisch versendet.
                </p>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span><strong>6 Belohnungstypen:</strong> Rabatte, Gutscheine, Downloads, Gratis-Produkte...</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span><strong>E-Mail-Benachrichtigung:</strong> Empfehler werden automatisch informiert</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span><strong>Branchen-Presets:</strong> Passende Vorschläge für Ihre Branche</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Feature 3: Gamification -->
        <div class="grid lg:grid-cols-2 gap-16 items-center mb-24">
            <div>
                <div class="inline-flex items-center gap-2 bg-yellow-100 text-yellow-600 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    <i class="fas fa-trophy"></i> Motivation
                </div>
                <h2 class="text-3xl md:text-4xl font-bold mb-6">Gamification-System</h2>
                <p class="text-gray-600 text-lg mb-6">
                    Spielerische Elemente motivieren Ihre Kunden, aktiv zu bleiben und 
                    mehr zu empfehlen. Punkte sammeln, Badges verdienen, im Leaderboard aufsteigen.
                </p>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span><strong>Fortschrittsbalken:</strong> Visualisierung bis zur nächsten Stufe</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span><strong>9 Badges:</strong> Achievements zum Sammeln</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span><strong>Leaderboard:</strong> Top 10 der besten Empfehler</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span><strong>Confetti:</strong> Feiern bei neuen Stufen</span>
                    </li>
                </ul>
            </div>
            <div class="bg-gray-50 rounded-2xl p-8">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Deine Badges</h3>
                    <div class="grid grid-cols-3 gap-4">
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
                        <div class="text-center p-3 rounded-xl <?= $badge['earned'] ? 'bg-primary-50' : 'bg-gray-100 opacity-50' ?>">
                            <div class="text-2xl mb-1"><?= $badge['icon'] ?></div>
                            <div class="text-xs font-medium <?= $badge['earned'] ? 'text-primary-600' : 'text-gray-400' ?>"><?= $badge['name'] ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4 text-center text-sm text-gray-500">
                        4 von 9 Badges verdient
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Feature 4: Share Buttons -->
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div class="order-2 lg:order-1 bg-gray-50 rounded-2xl p-8">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Link teilen via:</h3>
                    <div class="grid grid-cols-4 gap-3">
                        <button class="p-4 bg-green-500 text-white rounded-xl hover:bg-green-600 transition-colors">
                            <i class="fab fa-whatsapp text-2xl"></i>
                        </button>
                        <button class="p-4 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
                            <i class="fab fa-facebook text-2xl"></i>
                        </button>
                        <button class="p-4 bg-sky-500 text-white rounded-xl hover:bg-sky-600 transition-colors">
                            <i class="fab fa-telegram text-2xl"></i>
                        </button>
                        <button class="p-4 bg-gray-700 text-white rounded-xl hover:bg-gray-800 transition-colors">
                            <i class="fas fa-envelope text-2xl"></i>
                        </button>
                        <button class="p-4 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-colors">
                            <i class="fab fa-linkedin text-2xl"></i>
                        </button>
                        <button class="p-4 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-colors">
                            <i class="fab fa-xing text-2xl"></i>
                        </button>
                        <button class="p-4 bg-black text-white rounded-xl hover:bg-gray-900 transition-colors">
                            <i class="fab fa-x-twitter text-2xl"></i>
                        </button>
                        <button class="p-4 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors">
                            <i class="fas fa-copy text-2xl"></i>
                        </button>
                    </div>
                    <div class="mt-4 text-center">
                        <button class="text-primary-500 font-medium text-sm">
                            <i class="fas fa-qrcode mr-1"></i> QR-Code anzeigen
                        </button>
                    </div>
                </div>
            </div>
            <div class="order-1 lg:order-2">
                <div class="inline-flex items-center gap-2 bg-blue-100 text-blue-600 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    <i class="fas fa-share-alt"></i> Verbreitung
                </div>
                <h2 class="text-3xl md:text-4xl font-bold mb-6">11 Share-Buttons</h2>
                <p class="text-gray-600 text-lg mb-6">
                    Mit einem Klick teilen – auf der Plattform, die Ihre Kunden am liebsten nutzen.
                    Von WhatsApp bis LinkedIn, von E-Mail bis QR-Code.
                </p>
                <div class="grid grid-cols-2 gap-4 text-gray-600">
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
                        <i class="fas fa-envelope text-gray-700"></i> E-Mail
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
                        <i class="fab fa-x-twitter"></i> Twitter/X
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fab fa-pinterest text-red-500"></i> Pinterest
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-copy text-gray-500"></i> Link kopieren
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-qrcode text-gray-700"></i> QR-Code
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- More Features Grid -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold">Und noch viel mehr...</h2>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature: Email Sequences -->
            <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-purple-500 text-xl mb-4">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <h3 class="text-lg font-bold mb-2">E-Mail-Sequenzen</h3>
                <p class="text-gray-600">
                    Automatische E-Mails bei Anmeldung, Reminder bei Inaktivität, 
                    Glückwünsche bei neuen Stufen.
                </p>
            </div>
            
            <!-- Feature: Industry Backgrounds -->
            <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center text-pink-500 text-xl mb-4">
                    <i class="fas fa-image"></i>
                </div>
                <h3 class="text-lg font-bold mb-2">Branchen-Designs</h3>
                <p class="text-gray-600">
                    Professionelle Hintergrundbilder passend zu Ihrer Branche. 
                    Zahnarzt, Friseur, Fitness, Coach und mehr.
                </p>
            </div>
            
            <!-- Feature: Live Counter -->
            <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-500 text-xl mb-4">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="text-lg font-bold mb-2">Live-Counter</h3>
                <p class="text-gray-600">
                    "247 Personen nehmen bereits teil" – Social Proof motiviert 
                    neue Besucher zur Anmeldung.
                </p>
            </div>
            
            <!-- Feature: Share Graphics -->
            <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-orange-500 text-xl mb-4">
                    <i class="fas fa-paint-brush"></i>
                </div>
                <h3 class="text-lg font-bold mb-2">Share-Grafiken</h3>
                <p class="text-gray-600">
                    Automatisch generierte Bilder mit Ihrem Logo für 
                    Social Media Posts und Stories.
                </p>
            </div>
            
            <!-- Feature: Double Opt-In -->
            <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-500 text-xl mb-4">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="text-lg font-bold mb-2">Double Opt-In</h3>
                <p class="text-gray-600">
                    DSGVO-konforme E-Mail-Bestätigung. Alle rechtlichen 
                    Anforderungen sind abgedeckt.
                </p>
            </div>
            
            <!-- Feature: Anti-Spam -->
            <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center text-red-500 text-xl mb-4">
                    <i class="fas fa-ban"></i>
                </div>
                <h3 class="text-lg font-bold mb-2">Anti-Spam-Schutz</h3>
                <p class="text-gray-600">
                    Bot-Erkennung, Wegwerf-E-Mail-Blocker und Fraud-Detection 
                    schützen vor Missbrauch.
                </p>
            </div>
            
            <!-- Feature: Dashboard -->
            <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-500 text-xl mb-4">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3 class="text-lg font-bold mb-2">Dashboard</h3>
                <p class="text-gray-600">
                    Alle Statistiken auf einen Blick: Empfehler, Klicks, 
                    Conversions, Belohnungen.
                </p>
            </div>
            
            <!-- Feature: Subdomain -->
            <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-cyan-100 rounded-xl flex items-center justify-center text-cyan-500 text-xl mb-4">
                    <i class="fas fa-globe"></i>
                </div>
                <h3 class="text-lg font-bold mb-2">Eigene Subdomain</h3>
                <p class="text-gray-600">
                    Ihre persönliche URL: firma-name.empfohlen.de – 
                    professionell und vertrauenswürdig.
                </p>
            </div>
            
            <!-- Feature: API (Pro) -->
            <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow relative overflow-hidden">
                <span class="absolute top-2 right-2 bg-primary-500 text-white text-xs px-2 py-1 rounded-full">Pro</span>
                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500 text-xl mb-4">
                    <i class="fas fa-code"></i>
                </div>
                <h3 class="text-lg font-bold mb-2">API & Webhooks</h3>
                <p class="text-gray-600">
                    Integration in Ihre bestehenden Systeme via API 
                    und Echtzeit-Webhooks.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Comparison Table -->
<section class="py-20 bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold">Starter vs. Professional</h2>
            <p class="text-gray-600 mt-4">Wählen Sie den Plan, der zu Ihnen passt</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b-2">
                        <th class="text-left py-4 pr-4">Funktion</th>
                        <th class="text-center py-4 px-4">Starter</th>
                        <th class="text-center py-4 px-4 bg-primary-50">Professional</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="py-4 pr-4">Empfehler</td>
                        <td class="text-center py-4 px-4">Bis 200</td>
                        <td class="text-center py-4 px-4 bg-primary-50">Bis 5.000</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-4 pr-4">Belohnungsstufen</td>
                        <td class="text-center py-4 px-4">3</td>
                        <td class="text-center py-4 px-4 bg-primary-50">5</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-4 pr-4">E-Mails inklusive</td>
                        <td class="text-center py-4 px-4"><i class="fas fa-check text-green-500"></i></td>
                        <td class="text-center py-4 px-4 bg-primary-50"><i class="fas fa-check text-green-500"></i></td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-4 pr-4">Share-Buttons</td>
                        <td class="text-center py-4 px-4"><i class="fas fa-check text-green-500"></i></td>
                        <td class="text-center py-4 px-4 bg-primary-50"><i class="fas fa-check text-green-500"></i></td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-4 pr-4">Gamification</td>
                        <td class="text-center py-4 px-4">Basis</td>
                        <td class="text-center py-4 px-4 bg-primary-50">Erweitert</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-4 pr-4">Mehrere Kampagnen</td>
                        <td class="text-center py-4 px-4"><i class="fas fa-times text-gray-300"></i></td>
                        <td class="text-center py-4 px-4 bg-primary-50"><i class="fas fa-check text-green-500"></i></td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-4 pr-4">Lead-Export</td>
                        <td class="text-center py-4 px-4"><i class="fas fa-times text-gray-300"></i></td>
                        <td class="text-center py-4 px-4 bg-primary-50"><i class="fas fa-check text-green-500"></i></td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-4 pr-4">API & Webhooks</td>
                        <td class="text-center py-4 px-4"><i class="fas fa-times text-gray-300"></i></td>
                        <td class="text-center py-4 px-4 bg-primary-50"><i class="fas fa-check text-green-500"></i></td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-4 pr-4">Eigenes Hintergrundbild</td>
                        <td class="text-center py-4 px-4"><i class="fas fa-times text-gray-300"></i></td>
                        <td class="text-center py-4 px-4 bg-primary-50"><i class="fas fa-check text-green-500"></i></td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-4 pr-4">Support</td>
                        <td class="text-center py-4 px-4">E-Mail</td>
                        <td class="text-center py-4 px-4 bg-primary-50">Priorität</td>
                    </tr>
                    <tr>
                        <td class="py-4 pr-4 font-bold">Monatlich</td>
                        <td class="text-center py-4 px-4 font-bold">49€</td>
                        <td class="text-center py-4 px-4 bg-primary-50 font-bold text-primary-600">99€</td>
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
<section class="py-20 gradient-bg text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold mb-6">
            Überzeugt? Starten Sie jetzt!
        </h2>
        <p class="text-xl text-white/90 mb-8">
            14 Tage kostenlos testen – keine Kreditkarte erforderlich.
        </p>
        <a href="/onboarding" class="btn-primary btn-large bg-white text-primary-600 hover:bg-gray-100 inline-flex items-center gap-2">
            <span>Jetzt starten</span>
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</section>

<?php require_once __DIR__ . '/../templates/marketing/footer.php'; ?>
