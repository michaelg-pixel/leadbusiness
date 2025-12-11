<?php
/**
 * Pillar-Artikel: Empfehlungsmarketing für Online-Shops
 */

$pageTitle = 'Empfehlungsmarketing für Online-Shops: Customer Acquisition Cost senken';
$metaDescription = 'Wie E-Commerce-Unternehmen mit Empfehlungsprogrammen die Akquisekosten drastisch senken. Strategien, ROI-Berechnung und Best Practices.';
$currentPage = 'wissen';

require_once __DIR__ . '/../../templates/marketing/header.php';
?>

<!-- Hero Section -->
<section class="relative py-16 md:py-20 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-cyan-500 to-blue-600"></div>
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-40 h-40 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 right-10 w-60 h-60 bg-white rounded-full blur-3xl"></div>
    </div>
    
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <a href="/wissen" class="inline-flex items-center gap-2 text-white/80 hover:text-white mb-6 transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Zurück zur Übersicht</span>
            </a>
            
            <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full mb-6">
                <i class="fas fa-shopping-cart"></i>
                <span class="text-sm font-medium text-white">Branchenratgeber</span>
            </div>
            
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white mb-6 leading-tight">
                Empfehlungsmarketing für Online-Shops
            </h1>
            
            <p class="text-lg md:text-xl text-white/90 mb-8 leading-relaxed">
                So senken Sie Ihre Customer Acquisition Cost um bis zu 80% 
                und bauen gleichzeitig eine loyale Community auf.
            </p>
        </div>
    </div>
</section>

<!-- Content Section -->
<article class="py-12 md:py-16 bg-white dark:bg-slate-900">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Intro -->
        <div class="prose prose-lg dark:prose-invert max-w-none mb-12">
            <p class="text-xl text-gray-600 dark:text-gray-300 leading-relaxed">
                Die Customer Acquisition Cost (CAC) im E-Commerce steigt Jahr für Jahr. 
                Meta-Werbung wird teurer, Google Ads konkurrenzstärker. Zeit für einen 
                <strong>Kanal, der sich selbst verstärkt</strong>: Empfehlungsmarketing.
            </p>
        </div>
        
        <!-- Stats -->
        <div class="grid md:grid-cols-3 gap-6 my-12">
            <div class="bg-cyan-50 dark:bg-cyan-900/20 rounded-xl p-6 text-center">
                <div class="text-3xl font-bold text-cyan-600 dark:text-cyan-400 mb-2">-80%</div>
                <p class="text-gray-700 dark:text-gray-300 text-sm">geringere CAC vs. Paid Ads</p>
            </div>
            <div class="bg-cyan-50 dark:bg-cyan-900/20 rounded-xl p-6 text-center">
                <div class="text-3xl font-bold text-cyan-600 dark:text-cyan-400 mb-2">25%</div>
                <p class="text-gray-700 dark:text-gray-300 text-sm">höherer Average Order Value</p>
            </div>
            <div class="bg-cyan-50 dark:bg-cyan-900/20 rounded-xl p-6 text-center">
                <div class="text-3xl font-bold text-cyan-600 dark:text-cyan-400 mb-2">37%</div>
                <p class="text-gray-700 dark:text-gray-300 text-sm">bessere Retention Rate</p>
            </div>
        </div>
        
        <!-- Section: ROI -->
        <section class="mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-6">
                Die Mathematik dahinter: Warum Empfehlungen überlegen sind
            </h2>
            
            <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Beispielrechnung für einen Fashion-Shop</h3>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4">
                        <h4 class="font-semibold text-red-700 dark:text-red-400 mb-2">❌ Paid Ads</h4>
                        <ul class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                            <li>CPM: 15€</li>
                            <li>CTR: 1%</li>
                            <li>Conversion Rate: 2%</li>
                            <li><strong>CAC: 75€</strong></li>
                        </ul>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4">
                        <h4 class="font-semibold text-green-700 dark:text-green-400 mb-2">✅ Empfehlungen</h4>
                        <ul class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                            <li>Belohnungskosten: 10€</li>
                            <li>Conversion Rate: 15%</li>
                            <li>Software: ~3€/Kunde</li>
                            <li><strong>CAC: 13€</strong></li>
                        </ul>
                    </div>
                </div>
                
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-4 mb-0">
                    <strong>Ersparnis: 62€ pro Neukunde</strong> – bei 100 Neukunden/Monat sind das 6.200€ weniger Marketingkosten.
                </p>
            </div>
        </section>
        
        <!-- Section: Belohnungen -->
        <section class="mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-6">
                Belohnungsstrategien für E-Commerce
            </h2>
            
            <div class="bg-gradient-to-br from-cyan-50 to-blue-100 dark:from-slate-800 dark:to-slate-700 rounded-2xl p-6 my-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-gift text-cyan-500 mr-2"></i>Zwei Ansätze, die funktionieren
                </h3>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-slate-600 rounded-xl p-5">
                        <h4 class="font-bold text-gray-900 dark:text-white mb-3">💰 Rabatt-basiert</h4>
                        <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-2">
                            <li>• 10% Rabatt für Empfehler</li>
                            <li>• 10% Rabatt für Neukunden</li>
                            <li>• Gestaffelt: 15% ab 3 Empfehlungen</li>
                        </ul>
                        <p class="text-xs text-gray-500 mt-3 mb-0">Best für: Shops mit guten Margen</p>
                    </div>
                    <div class="bg-white dark:bg-slate-600 rounded-xl p-5">
                        <h4 class="font-bold text-gray-900 dark:text-white mb-3">🎁 Produkt-basiert</h4>
                        <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-2">
                            <li>• Gratis-Produkt bei Empfehlung</li>
                            <li>• Exklusive Limited Editions</li>
                            <li>• Early Access zu Neuheiten</li>
                        </ul>
                        <p class="text-xs text-gray-500 mt-3 mb-0">Best für: Lifestyle & Beauty</p>
                    </div>
                </div>
            </div>
            
            <div class="prose prose-lg dark:prose-invert max-w-none">
                <h3>Gestaffelte Belohnungen erhöhen die Aktivität</h3>
                <p>
                    Studien zeigen: Kunden, die ein "Level-up" vor Augen haben, empfehlen 
                    <strong>3x häufiger</strong> als bei einfachen Einmal-Belohnungen.
                </p>
                
                <div class="bg-cyan-50 dark:bg-cyan-900/20 border-l-4 border-cyan-500 p-4 my-6">
                    <p class="text-cyan-800 dark:text-cyan-200 mb-0">
                        <strong>Profi-Tipp:</strong> Zeigen Sie Kunden nach jeder Empfehlung, wie nah sie 
                        am nächsten Level sind. Der "Fortschrittsbalken-Effekt" ist mächtig.
                    </p>
                </div>
            </div>
        </section>
        
        <!-- Section: Integration -->
        <section class="mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-6">
                So integrieren Sie Empfehlungen in Ihre Customer Journey
            </h2>
            
            <div class="space-y-4">
                <div class="flex gap-4 bg-gray-50 dark:bg-slate-800 rounded-xl p-5">
                    <div class="w-10 h-10 bg-cyan-600 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">1</div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white mb-1">Post-Purchase E-Mail</h4>
                        <p class="text-gray-600 dark:text-gray-400 mb-0">24-48h nach dem Kauf: "Hat Ihnen Ihre Bestellung gefallen? Teilen Sie mit Freunden!"</p>
                    </div>
                </div>
                
                <div class="flex gap-4 bg-gray-50 dark:bg-slate-800 rounded-xl p-5">
                    <div class="w-10 h-10 bg-cyan-600 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">2</div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white mb-1">Danke-Seite</h4>
                        <p class="text-gray-600 dark:text-gray-400 mb-0">Direkt nach dem Checkout: Empfehlungslink mit Share-Buttons anzeigen.</p>
                    </div>
                </div>
                
                <div class="flex gap-4 bg-gray-50 dark:bg-slate-800 rounded-xl p-5">
                    <div class="w-10 h-10 bg-cyan-600 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">3</div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white mb-1">Paketbeilage</h4>
                        <p class="text-gray-600 dark:text-gray-400 mb-0">QR-Code in der Verpackung: "Teilen Sie Ihre Lieblingsprodukte mit Freunden!"</p>
                    </div>
                </div>
                
                <div class="flex gap-4 bg-gray-50 dark:bg-slate-800 rounded-xl p-5">
                    <div class="w-10 h-10 bg-cyan-600 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">4</div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white mb-1">Kundenkonto</h4>
                        <p class="text-gray-600 dark:text-gray-400 mb-0">Permanenter Tab "Freunde einladen" im Account-Bereich mit Status-Anzeige.</p>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- CTA Box -->
        <div class="bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl p-8 text-center text-white">
            <h3 class="text-2xl font-bold mb-4">Bereit, Ihre CAC zu senken?</h3>
            <p class="text-cyan-100 mb-6">
                Starten Sie noch heute Ihr eigenes Empfehlungsprogramm für Ihren Online-Shop.
            </p>
            <a href="/onboarding" class="inline-flex items-center gap-2 bg-white text-cyan-600 px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition-all">
                <span>Jetzt 7 Tage kostenlos testen</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
    </div>
</article>

<!-- Related Content -->
<section class="py-12 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Das könnte Sie auch interessieren</h2>
        
        <div class="grid md:grid-cols-3 gap-6">
            <a href="/branchen/onlineshop" class="bg-white dark:bg-slate-700 rounded-xl p-5 hover:shadow-lg transition-shadow">
                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">Lösung</span>
                <h3 class="font-bold text-gray-900 dark:text-white mt-2">Leadbusiness für Online-Shops</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Alle Features für E-Commerce im Überblick.</p>
            </a>
            
            <a href="/wissen/empfehlungsmarketing-handwerker" class="bg-white dark:bg-slate-700 rounded-xl p-5 hover:shadow-lg transition-shadow">
                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400">Ratgeber</span>
                <h3 class="font-bold text-gray-900 dark:text-white mt-2">Empfehlungsmarketing für Handwerker</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Strategien für lokale Dienstleister.</p>
            </a>
            
            <a href="/wissen/empfehlungsmarketing-coach" class="bg-white dark:bg-slate-700 rounded-xl p-5 hover:shadow-lg transition-shadow">
                <span class="text-xs font-semibold text-purple-600 dark:text-purple-400">Ratgeber</span>
                <h3 class="font-bold text-gray-900 dark:text-white mt-2">Empfehlungsmarketing für Coaches</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Strategien für digitale Dienstleister.</p>
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../templates/marketing/footer.php'; ?>
