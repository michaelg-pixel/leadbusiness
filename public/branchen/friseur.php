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
                        <span>7 Tage kostenlos</span>
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

<!-- NEUE SECTION: Interaktive Animationen -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="inline-flex items-center gap-2 bg-gradient-to-r from-pink-500 to-purple-600 text-white px-5 py-2 rounded-full text-sm font-bold shadow-lg mb-4">
                <span>✨</span> Live erleben
            </span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                So funktioniert Empfehlungsmarketing im Salon
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg max-w-2xl mx-auto">
                Drei interaktive Demos zeigen Ihnen, wie Ihre Kunden begeistert weiterempfehlen.
            </p>
        </div>
        
        <!-- Tab Navigation -->
        <div class="flex flex-wrap justify-center gap-3 mb-8" id="animation-tabs">
            <button onclick="showAnimation('instagram')" id="tab-instagram" class="animation-tab active px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-gradient-to-r from-pink-500 to-purple-600 text-white shadow-lg">
                📸 Instagram Story
            </button>
            <button onclick="showAnimation('transform')" id="tab-transform" class="animation-tab px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-white dark:bg-slate-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-600 hover:shadow-md">
                💇‍♀️ Transformation
            </button>
            <button onclick="showAnimation('compliment')" id="tab-compliment" class="animation-tab px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-white dark:bg-slate-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-600 hover:shadow-md">
                💬 Kompliment-Kette
            </button>
        </div>
        
        <!-- Animation Containers -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-8 min-h-[650px] shadow-lg">
            
            <!-- 1. Instagram Story Animation -->
            <div id="animation-instagram" class="animation-content">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">So teilen Ihre Kundinnen auf Instagram</h3>
                    <p class="text-gray-500 dark:text-gray-400">Eine Story – und schon klingelt das Telefon für neue Termine</p>
                </div>
                
                <div class="max-w-sm mx-auto">
                    <!-- Phone Frame -->
                    <div class="bg-gradient-to-br from-purple-900 via-pink-900 to-orange-900 rounded-[2.5rem] p-3 shadow-2xl">
                        <div class="w-24 h-6 bg-black rounded-full mx-auto mb-2"></div>
                        <div class="bg-black rounded-[2rem] overflow-hidden h-[520px] flex flex-col relative">
                            
                            <!-- Instagram Header -->
                            <div class="absolute top-0 left-0 right-0 z-10 p-4 bg-gradient-to-b from-black/60 to-transparent">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-pink-500 to-purple-600 p-0.5">
                                        <div class="w-full h-full rounded-full bg-black flex items-center justify-center text-white text-sm font-bold">A</div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-white text-sm font-semibold">anna_loves_style</div>
                                        <div class="text-white/60 text-xs">Hamburg • Gerade eben</div>
                                    </div>
                                    <span class="text-white/80">•••</span>
                                </div>
                                <!-- Progress bars -->
                                <div class="flex gap-1 mt-3">
                                    <div class="flex-1 h-0.5 bg-white/30 rounded-full overflow-hidden">
                                        <div id="story-progress" class="h-full bg-white rounded-full" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Story Content -->
                            <div class="flex-1 bg-gradient-to-br from-pink-400 via-purple-500 to-indigo-600 flex items-center justify-center relative overflow-hidden">
                                <!-- Selfie placeholder -->
                                <div id="story-selfie" class="text-center opacity-0 transform scale-90 transition-all duration-500">
                                    <div class="text-8xl mb-4">💇‍♀️</div>
                                    <div class="text-white text-xl font-bold">Meine neuen Highlights! ✨</div>
                                </div>
                                
                                <!-- Floating elements -->
                                <div id="story-elements" class="absolute inset-0 pointer-events-none">
                                    <!-- Will be filled by JS -->
                                </div>
                            </div>
                            
                            <!-- Story Footer -->
                            <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/80 to-transparent">
                                <!-- Location Tag -->
                                <div id="story-location" class="opacity-0 transform translate-y-4 transition-all duration-500 mb-3">
                                    <span class="inline-flex items-center gap-1 bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-white text-xs">
                                        <i class="fas fa-map-marker-alt"></i> Salon Style & Cut
                                    </span>
                                </div>
                                
                                <!-- Mention -->
                                <div id="story-mention" class="opacity-0 transform translate-y-4 transition-all duration-500 mb-3">
                                    <span class="inline-flex items-center gap-1 bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-white text-sm font-medium">
                                        @SalonStyleCut 💕
                                    </span>
                                </div>
                                
                                <!-- Referral Link -->
                                <div id="story-link" class="opacity-0 transform translate-y-4 transition-all duration-500">
                                    <div class="bg-white rounded-xl p-3 flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-600 rounded-lg flex items-center justify-center text-white">
                                            <i class="fas fa-gift"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-gray-900 font-semibold text-sm">10% Rabatt für dich!</div>
                                            <div class="text-gray-500 text-xs">Tippe für deinen Gutschein</div>
                                        </div>
                                        <i class="fas fa-chevron-up text-gray-400"></i>
                                    </div>
                                </div>
                                
                                <!-- Reply Bar -->
                                <div class="flex items-center gap-3 mt-3">
                                    <div class="flex-1 bg-white/10 rounded-full px-4 py-2 text-white/50 text-sm">
                                        Nachricht senden...
                                    </div>
                                    <i class="far fa-heart text-white text-xl"></i>
                                    <i class="far fa-paper-plane text-white text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Results Counter -->
                    <div id="story-results" class="mt-6 opacity-0 transform translate-y-4 transition-all duration-500">
                        <div class="bg-gradient-to-r from-pink-500 to-purple-600 rounded-xl p-4 text-white text-center">
                            <div class="text-2xl font-bold mb-1" id="story-counter">0</div>
                            <div class="text-white/80 text-sm">neue Terminanfragen durch diese Story</div>
                        </div>
                    </div>
                    
                    <!-- Replay Button -->
                    <button onclick="restartInstagram()" id="instagram-replay" class="hidden mt-4 mx-auto block px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-full font-semibold text-sm hover:shadow-lg transition-all">
                        ↻ Animation wiederholen
                    </button>
                </div>
            </div>
            
            <!-- 2. Transformation Animation -->
            <div id="animation-transform" class="animation-content hidden">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Ihre Styling-Reise</h3>
                    <p class="text-gray-500 dark:text-gray-400">Jede Empfehlung bringt Sie näher zum nächsten Gratis-Treatment</p>
                </div>
                
                <div class="max-w-lg mx-auto">
                    <!-- Transformation Card -->
                    <div class="bg-gradient-to-br from-pink-50 to-purple-50 dark:from-slate-800 dark:to-slate-700 rounded-2xl p-6 shadow-lg">
                        
                        <!-- Before/After Slider -->
                        <div class="relative mb-8">
                            <div class="flex items-center justify-center gap-8">
                                <!-- Before -->
                                <div class="text-center">
                                    <div id="transform-before" class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center text-4xl md:text-5xl transition-all duration-500 shadow-lg">
                                        😐
                                    </div>
                                    <div class="mt-3 text-gray-500 dark:text-gray-400 text-sm font-medium">Vorher</div>
                                </div>
                                
                                <!-- Arrow -->
                                <div id="transform-arrow" class="flex flex-col items-center opacity-0 transform scale-0 transition-all duration-500">
                                    <div class="text-4xl">✂️</div>
                                    <div class="text-pink-500 font-bold text-sm mt-1">Styling</div>
                                </div>
                                
                                <!-- After -->
                                <div class="text-center">
                                    <div id="transform-after" class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center text-4xl md:text-5xl transition-all duration-500 shadow-lg">
                                        😐
                                    </div>
                                    <div class="mt-3 text-gray-500 dark:text-gray-400 text-sm font-medium">Nachher</div>
                                </div>
                            </div>
                            
                            <!-- Sparkles -->
                            <div id="transform-sparkles" class="absolute inset-0 pointer-events-none overflow-hidden">
                                <!-- Will be filled by JS -->
                            </div>
                        </div>
                        
                        <!-- Progress Section -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-700 dark:text-gray-300 font-medium">Ihre Empfehlungen</span>
                                <span id="transform-count" class="text-pink-600 dark:text-pink-400 font-bold">0 / 10</span>
                            </div>
                            <div class="h-4 bg-gray-200 dark:bg-slate-600 rounded-full overflow-hidden">
                                <div id="transform-progress" class="h-full bg-gradient-to-r from-pink-500 to-purple-600 rounded-full transition-all duration-500" style="width: 0%"></div>
                            </div>
                        </div>
                        
                        <!-- Rewards -->
                        <div class="grid grid-cols-3 gap-3 mb-6">
                            <div id="reward-1" class="reward-card bg-white dark:bg-slate-700 rounded-xl p-3 text-center transition-all duration-300 opacity-40 transform scale-95">
                                <div class="text-2xl mb-1">🧴</div>
                                <div class="text-xs font-medium text-gray-700 dark:text-gray-300">Pflegeprodukt</div>
                                <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">3 Empf.</div>
                                <div class="mt-2">
                                    <span class="inline-block w-5 h-5 rounded-full bg-gray-200 dark:bg-slate-600 text-gray-400 text-xs flex items-center justify-center">🔒</span>
                                </div>
                            </div>
                            <div id="reward-2" class="reward-card bg-white dark:bg-slate-700 rounded-xl p-3 text-center transition-all duration-300 opacity-40 transform scale-95">
                                <div class="text-2xl mb-1">💆</div>
                                <div class="text-xs font-medium text-gray-700 dark:text-gray-300">20% Rabatt</div>
                                <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">5 Empf.</div>
                                <div class="mt-2">
                                    <span class="inline-block w-5 h-5 rounded-full bg-gray-200 dark:bg-slate-600 text-gray-400 text-xs flex items-center justify-center">🔒</span>
                                </div>
                            </div>
                            <div id="reward-3" class="reward-card bg-white dark:bg-slate-700 rounded-xl p-3 text-center transition-all duration-300 opacity-40 transform scale-95">
                                <div class="text-2xl mb-1">✨</div>
                                <div class="text-xs font-medium text-gray-700 dark:text-gray-300">Olaplex gratis</div>
                                <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">10 Empf.</div>
                                <div class="mt-2">
                                    <span class="inline-block w-5 h-5 rounded-full bg-gray-200 dark:bg-slate-600 text-gray-400 text-xs flex items-center justify-center">🔒</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Button -->
                        <button onclick="addTransformReferral()" id="transform-btn" class="w-full py-4 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-xl font-bold text-lg hover:shadow-lg transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-share-alt"></i>
                            <span>Jetzt empfehlen</span>
                        </button>
                        
                        <!-- Reset -->
                        <button onclick="resetTransform()" class="w-full mt-3 py-2 text-gray-500 dark:text-gray-400 text-sm hover:text-pink-600 dark:hover:text-pink-400 transition-colors">
                            ↻ Demo zurücksetzen
                        </button>
                    </div>
                </div>
                
                <!-- Unlock Modal -->
                <div id="transform-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
                    <div class="absolute inset-0 bg-black/50" onclick="closeTransformModal()"></div>
                    <div class="relative bg-white dark:bg-slate-700 rounded-2xl p-8 shadow-2xl text-center max-w-xs mx-4 animate-bounce-in">
                        <div id="transform-modal-icon" class="text-6xl mb-4">🎉</div>
                        <div id="transform-modal-title" class="text-2xl font-black text-gray-800 dark:text-white mb-2">Belohnung freigeschaltet!</div>
                        <div id="transform-modal-text" class="text-gray-600 dark:text-gray-300 mb-6">Gratis Pflegeprodukt</div>
                        <button onclick="closeTransformModal()" class="px-8 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-xl font-semibold hover:shadow-lg transition-all">
                            Jetzt einlösen! 💕
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- 3. Compliment Chain Animation -->
            <div id="animation-compliment" class="animation-content hidden">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Die Kompliment-Kette</h3>
                    <p class="text-gray-500 dark:text-gray-400">Von einem Kompliment zur Buchung – so natürlich funktioniert's</p>
                </div>
                
                <div class="max-w-2xl mx-auto">
                    <!-- Chain Visualization -->
                    <div class="relative py-8">
                        
                        <!-- Step 1: Anna -->
                        <div id="chain-step-1" class="flex items-center gap-4 mb-4 opacity-0 transform -translate-x-8 transition-all duration-500">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-pink-400 to-pink-600 flex items-center justify-center text-2xl shadow-lg flex-shrink-0">
                                👩
                            </div>
                            <div class="flex-1">
                                <div class="font-bold text-gray-900 dark:text-white">Anna</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Zufriedene Kundin</div>
                            </div>
                            <div class="bg-pink-100 dark:bg-pink-900/30 rounded-2xl rounded-bl-none px-4 py-2 max-w-[200px]">
                                <p class="text-sm text-gray-700 dark:text-gray-300">War gerade beim Friseur! 💇‍♀️✨</p>
                            </div>
                        </div>
                        
                        <!-- Connection Line 1 -->
                        <div id="chain-line-1" class="ml-8 w-0.5 h-0 bg-gradient-to-b from-pink-400 to-purple-400 transition-all duration-500 mb-4"></div>
                        
                        <!-- Step 2: Compliment -->
                        <div id="chain-step-2" class="flex items-center gap-4 mb-4 opacity-0 transform translate-x-8 transition-all duration-500">
                            <div class="bg-purple-100 dark:bg-purple-900/30 rounded-2xl rounded-br-none px-4 py-2 max-w-[220px] ml-auto">
                                <p class="text-sm text-gray-700 dark:text-gray-300">"Wow, deine Haare sehen mega aus! Wo warst du?" 😍</p>
                            </div>
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-2xl shadow-lg flex-shrink-0">
                                👩‍🦰
                            </div>
                        </div>
                        
                        <!-- Connection Line 2 -->
                        <div id="chain-line-2" class="ml-8 w-0.5 h-0 bg-gradient-to-b from-purple-400 to-pink-400 transition-all duration-500 mb-4"></div>
                        
                        <!-- Step 3: Share Link -->
                        <div id="chain-step-3" class="flex items-center gap-4 mb-4 opacity-0 transform -translate-x-8 transition-all duration-500">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-pink-400 to-pink-600 flex items-center justify-center text-2xl shadow-lg flex-shrink-0">
                                👩
                            </div>
                            <div class="bg-white dark:bg-slate-700 rounded-2xl px-4 py-3 shadow-lg max-w-[280px] border border-pink-200 dark:border-pink-800">
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">"Bei Salon Style & Cut! Hier mein Link – du bekommst 10% Rabatt:"</p>
                                <div class="bg-pink-50 dark:bg-pink-900/20 rounded-lg px-3 py-2 text-pink-600 dark:text-pink-400 text-sm font-medium flex items-center gap-2">
                                    <i class="fas fa-link"></i>
                                    empfohlen.de/anna
                                </div>
                            </div>
                        </div>
                        
                        <!-- Connection Line 3 -->
                        <div id="chain-line-3" class="ml-8 w-0.5 h-0 bg-gradient-to-b from-pink-400 to-green-400 transition-all duration-500 mb-4"></div>
                        
                        <!-- Step 4: Booking -->
                        <div id="chain-step-4" class="flex items-center gap-4 mb-4 opacity-0 transform translate-x-8 transition-all duration-500">
                            <div class="bg-green-100 dark:bg-green-900/30 rounded-2xl px-4 py-3 shadow-lg max-w-[220px] ml-auto">
                                <p class="text-sm text-gray-700 dark:text-gray-300 flex items-center gap-2">
                                    <i class="fas fa-calendar-check text-green-500"></i>
                                    Lisa bucht Termin!
                                </p>
                            </div>
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-2xl shadow-lg flex-shrink-0">
                                👩‍🦰
                            </div>
                        </div>
                        
                        <!-- Connection Line 4 -->
                        <div id="chain-line-4" class="ml-8 w-0.5 h-0 bg-gradient-to-b from-green-400 to-yellow-400 transition-all duration-500 mb-4"></div>
                        
                        <!-- Step 5: Rewards -->
                        <div id="chain-step-5" class="opacity-0 transform translate-y-8 transition-all duration-500">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gradient-to-br from-pink-500 to-purple-600 rounded-2xl p-4 text-white text-center shadow-lg">
                                    <div class="text-3xl mb-2">🎁</div>
                                    <div class="font-bold">Anna erhält</div>
                                    <div class="text-white/90 text-sm">Gratis Pflegeprodukt</div>
                                </div>
                                <div class="bg-gradient-to-br from-green-500 to-teal-600 rounded-2xl p-4 text-white text-center shadow-lg">
                                    <div class="text-3xl mb-2">✂️</div>
                                    <div class="font-bold">Salon gewinnt</div>
                                    <div class="text-white/90 text-sm">+1 neue Kundin</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Continue Hint -->
                        <div id="chain-continue" class="mt-6 text-center opacity-0 transform translate-y-4 transition-all duration-500">
                            <div class="inline-flex items-center gap-2 bg-gradient-to-r from-pink-100 to-purple-100 dark:from-pink-900/30 dark:to-purple-900/30 px-4 py-2 rounded-full text-pink-600 dark:text-pink-400 text-sm font-medium">
                                <i class="fas fa-sync-alt animate-spin"></i>
                                Und Lisa empfiehlt weiter...
                            </div>
                        </div>
                    </div>
                    
                    <!-- Controls -->
                    <div class="flex justify-center gap-3 mt-6">
                        <button onclick="restartComplimentChain()" class="px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-full font-semibold text-sm hover:shadow-lg transition-all">
                            ↻ Animation wiederholen
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Animation Styles -->
<style>
    @keyframes messageIn {
        from { opacity: 0; transform: scale(0.8) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes bounceIn {
        0% { transform: scale(0); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(10deg); }
    }
    @keyframes sparkle {
        0%, 100% { opacity: 0; transform: scale(0) rotate(0deg); }
        50% { opacity: 1; transform: scale(1) rotate(180deg); }
    }
    @keyframes countUp {
        from { transform: scale(1.5); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .animate-message-in { animation: messageIn 0.3s ease forwards; }
    .animate-bounce-in { animation: bounceIn 0.4s ease forwards; }
    .animate-float { animation: float 3s ease-in-out infinite; }
    .animate-sparkle { animation: sparkle 1s ease-in-out; }
    .animate-count { animation: countUp 0.3s ease; }
    
    .animation-tab.active {
        background: linear-gradient(to right, #ec4899, #9333ea);
        color: white;
        box-shadow: 0 10px 15px -3px rgba(236, 72, 153, 0.3);
        border: none;
    }
    
    .reward-card.unlocked {
        opacity: 1 !important;
        transform: scale(1) !important;
        box-shadow: 0 0 20px rgba(236, 72, 153, 0.3);
        border: 2px solid #ec4899;
    }
    
    .reward-card.unlocked span {
        background: linear-gradient(to right, #ec4899, #9333ea) !important;
        color: white !important;
    }
</style>

<!-- Animation JavaScript -->
<script>
// ==================== TAB SWITCHING ====================
function showAnimation(type) {
    document.querySelectorAll('.animation-content').forEach(el => el.classList.add('hidden'));
    document.getElementById('animation-' + type).classList.remove('hidden');
    
    document.querySelectorAll('.animation-tab').forEach(tab => {
        tab.classList.remove('active', 'bg-gradient-to-r', 'from-pink-500', 'to-purple-600', 'text-white', 'shadow-lg');
        tab.classList.add('bg-white', 'dark:bg-slate-700', 'text-gray-600', 'dark:text-gray-300', 'border', 'border-gray-200', 'dark:border-slate-600');
    });
    const activeTab = document.getElementById('tab-' + type);
    activeTab.classList.add('active');
    activeTab.classList.remove('bg-white', 'dark:bg-slate-700', 'text-gray-600', 'dark:text-gray-300', 'border', 'border-gray-200', 'dark:border-slate-600');
    
    if (type === 'instagram') restartInstagram();
    if (type === 'transform') resetTransform();
    if (type === 'compliment') restartComplimentChain();
}

// ==================== 1. INSTAGRAM STORY ANIMATION ====================
let instagramTimeout = null;

function restartInstagram() {
    clearTimeout(instagramTimeout);
    
    // Reset all elements
    document.getElementById('story-progress').style.width = '0%';
    document.getElementById('story-selfie').classList.add('opacity-0', 'scale-90');
    document.getElementById('story-selfie').classList.remove('opacity-100', 'scale-100');
    document.getElementById('story-location').classList.add('opacity-0', 'translate-y-4');
    document.getElementById('story-location').classList.remove('opacity-100', 'translate-y-0');
    document.getElementById('story-mention').classList.add('opacity-0', 'translate-y-4');
    document.getElementById('story-mention').classList.remove('opacity-100', 'translate-y-0');
    document.getElementById('story-link').classList.add('opacity-0', 'translate-y-4');
    document.getElementById('story-link').classList.remove('opacity-100', 'translate-y-0');
    document.getElementById('story-results').classList.add('opacity-0', 'translate-y-4');
    document.getElementById('story-results').classList.remove('opacity-100', 'translate-y-0');
    document.getElementById('story-elements').innerHTML = '';
    document.getElementById('story-counter').textContent = '0';
    document.getElementById('instagram-replay').classList.add('hidden');
    
    // Start animation sequence
    instagramTimeout = setTimeout(() => {
        // Show selfie
        document.getElementById('story-selfie').classList.remove('opacity-0', 'scale-90');
        document.getElementById('story-selfie').classList.add('opacity-100', 'scale-100');
        
        // Animate progress bar
        document.getElementById('story-progress').style.transition = 'width 8s linear';
        document.getElementById('story-progress').style.width = '100%';
        
        instagramTimeout = setTimeout(() => {
            // Show location
            document.getElementById('story-location').classList.remove('opacity-0', 'translate-y-4');
            document.getElementById('story-location').classList.add('opacity-100', 'translate-y-0');
            
            instagramTimeout = setTimeout(() => {
                // Show mention
                document.getElementById('story-mention').classList.remove('opacity-0', 'translate-y-4');
                document.getElementById('story-mention').classList.add('opacity-100', 'translate-y-0');
                
                // Add floating reactions
                addStoryReactions();
                
                instagramTimeout = setTimeout(() => {
                    // Show link
                    document.getElementById('story-link').classList.remove('opacity-0', 'translate-y-4');
                    document.getElementById('story-link').classList.add('opacity-100', 'translate-y-0');
                    
                    instagramTimeout = setTimeout(() => {
                        // Show results
                        document.getElementById('story-results').classList.remove('opacity-0', 'translate-y-4');
                        document.getElementById('story-results').classList.add('opacity-100', 'translate-y-0');
                        
                        // Count up
                        animateCounter('story-counter', 0, 5, 1500);
                        
                        document.getElementById('instagram-replay').classList.remove('hidden');
                    }, 1500);
                }, 1200);
            }, 1000);
        }, 800);
    }, 500);
}

function addStoryReactions() {
    const container = document.getElementById('story-elements');
    const reactions = ['❤️', '🔥', '😍', '💕', '✨', '💇‍♀️', '👏'];
    
    for (let i = 0; i < 12; i++) {
        setTimeout(() => {
            const emoji = document.createElement('div');
            emoji.textContent = reactions[Math.floor(Math.random() * reactions.length)];
            emoji.className = 'absolute text-2xl animate-float';
            emoji.style.left = Math.random() * 80 + 10 + '%';
            emoji.style.top = Math.random() * 60 + 20 + '%';
            emoji.style.animationDelay = Math.random() * 2 + 's';
            emoji.style.animationDuration = (2 + Math.random() * 2) + 's';
            container.appendChild(emoji);
            
            setTimeout(() => emoji.remove(), 4000);
        }, i * 300);
    }
}

function animateCounter(elementId, start, end, duration) {
    const element = document.getElementById(elementId);
    const range = end - start;
    const stepTime = duration / range;
    let current = start;
    
    const timer = setInterval(() => {
        current++;
        element.textContent = current;
        element.classList.add('animate-count');
        setTimeout(() => element.classList.remove('animate-count'), 200);
        
        if (current >= end) clearInterval(timer);
    }, stepTime);
}

// ==================== 2. TRANSFORMATION ANIMATION ====================
let transformReferrals = 0;
const transformMilestones = [
    { count: 3, reward: 'Gratis Pflegeprodukt', icon: '🧴', rewardId: 'reward-1' },
    { count: 5, reward: '20% Rabatt', icon: '💆', rewardId: 'reward-2' },
    { count: 10, reward: 'Olaplex gratis', icon: '✨', rewardId: 'reward-3' }
];

function addTransformReferral() {
    if (transformReferrals >= 10) return;
    
    transformReferrals++;
    updateTransformUI();
    
    // Check milestones
    const milestone = transformMilestones.find(m => m.count === transformReferrals);
    if (milestone) {
        showTransformModal(milestone);
        unlockReward(milestone.rewardId);
    }
}

function updateTransformUI() {
    document.getElementById('transform-count').textContent = transformReferrals + ' / 10';
    document.getElementById('transform-progress').style.width = (transformReferrals / 10 * 100) + '%';
    
    // Update emoji transformation
    const progress = transformReferrals / 10;
    const beforeEmojis = ['😐', '🙂', '😊', '😄'];
    const afterEmojis = ['😐', '😊', '😍', '🤩'];
    
    const beforeIndex = Math.min(Math.floor(progress * beforeEmojis.length), beforeEmojis.length - 1);
    const afterIndex = Math.min(Math.floor(progress * afterEmojis.length), afterEmojis.length - 1);
    
    document.getElementById('transform-before').textContent = beforeEmojis[beforeIndex];
    document.getElementById('transform-after').textContent = afterEmojis[afterIndex];
    
    // Show arrow after first referral
    if (transformReferrals >= 1) {
        document.getElementById('transform-arrow').classList.remove('opacity-0', 'scale-0');
        document.getElementById('transform-arrow').classList.add('opacity-100', 'scale-100');
    }
    
    // Update after circle colors
    if (transformReferrals >= 3) {
        document.getElementById('transform-after').classList.remove('from-gray-300', 'to-gray-400');
        document.getElementById('transform-after').classList.add('from-pink-400', 'to-purple-500');
    }
    
    // Add sparkles
    if (transformReferrals > 0) {
        addSparkles();
    }
    
    // Disable button at max
    if (transformReferrals >= 10) {
        document.getElementById('transform-btn').innerHTML = '<i class="fas fa-check"></i> <span>Alle Belohnungen freigeschaltet!</span>';
        document.getElementById('transform-btn').disabled = true;
        document.getElementById('transform-btn').classList.add('opacity-70', 'cursor-not-allowed');
    }
}

function addSparkles() {
    const container = document.getElementById('transform-sparkles');
    const sparkles = ['✨', '💫', '⭐'];
    
    for (let i = 0; i < 5; i++) {
        const sparkle = document.createElement('div');
        sparkle.textContent = sparkles[Math.floor(Math.random() * sparkles.length)];
        sparkle.className = 'absolute text-xl animate-sparkle';
        sparkle.style.left = Math.random() * 100 + '%';
        sparkle.style.top = Math.random() * 100 + '%';
        sparkle.style.animationDelay = Math.random() * 0.5 + 's';
        container.appendChild(sparkle);
        
        setTimeout(() => sparkle.remove(), 1500);
    }
}

function unlockReward(rewardId) {
    const reward = document.getElementById(rewardId);
    reward.classList.add('unlocked');
    reward.querySelector('span').textContent = '✓';
}

function showTransformModal(milestone) {
    document.getElementById('transform-modal-icon').textContent = milestone.icon;
    document.getElementById('transform-modal-title').textContent = 'Belohnung freigeschaltet!';
    document.getElementById('transform-modal-text').textContent = milestone.reward;
    document.getElementById('transform-modal').classList.remove('hidden');
}

function closeTransformModal() {
    document.getElementById('transform-modal').classList.add('hidden');
}

function resetTransform() {
    transformReferrals = 0;
    
    document.getElementById('transform-count').textContent = '0 / 10';
    document.getElementById('transform-progress').style.width = '0%';
    document.getElementById('transform-before').textContent = '😐';
    document.getElementById('transform-after').textContent = '😐';
    document.getElementById('transform-before').classList.add('from-gray-300', 'to-gray-400');
    document.getElementById('transform-before').classList.remove('from-pink-400', 'to-purple-500');
    document.getElementById('transform-after').classList.add('from-gray-300', 'to-gray-400');
    document.getElementById('transform-after').classList.remove('from-pink-400', 'to-purple-500');
    document.getElementById('transform-arrow').classList.add('opacity-0', 'scale-0');
    document.getElementById('transform-arrow').classList.remove('opacity-100', 'scale-100');
    
    // Reset rewards
    document.querySelectorAll('.reward-card').forEach(card => {
        card.classList.remove('unlocked');
        card.querySelector('span').textContent = '🔒';
    });
    
    // Reset button
    document.getElementById('transform-btn').innerHTML = '<i class="fas fa-share-alt"></i> <span>Jetzt empfehlen</span>';
    document.getElementById('transform-btn').disabled = false;
    document.getElementById('transform-btn').classList.remove('opacity-70', 'cursor-not-allowed');
}

// ==================== 3. COMPLIMENT CHAIN ANIMATION ====================
let chainTimeout = null;

function restartComplimentChain() {
    clearTimeout(chainTimeout);
    
    // Reset all steps
    for (let i = 1; i <= 5; i++) {
        const step = document.getElementById('chain-step-' + i);
        if (step) {
            step.classList.add('opacity-0');
            step.classList.remove('opacity-100');
            if (i % 2 === 1) {
                step.classList.add('-translate-x-8');
                step.classList.remove('translate-x-0');
            } else {
                step.classList.add('translate-x-8');
                step.classList.remove('translate-x-0');
            }
        }
    }
    
    // Reset lines
    for (let i = 1; i <= 4; i++) {
        const line = document.getElementById('chain-line-' + i);
        if (line) {
            line.style.height = '0px';
        }
    }
    
    // Reset continue hint
    document.getElementById('chain-continue').classList.add('opacity-0', 'translate-y-4');
    document.getElementById('chain-continue').classList.remove('opacity-100', 'translate-y-0');
    
    // Start animation sequence
    const delays = [500, 1200, 2000, 2800, 3600, 4400, 5200];
    
    chainTimeout = setTimeout(() => {
        // Step 1
        showChainStep(1);
        
        chainTimeout = setTimeout(() => {
            showChainLine(1);
            
            chainTimeout = setTimeout(() => {
                // Step 2
                showChainStep(2);
                
                chainTimeout = setTimeout(() => {
                    showChainLine(2);
                    
                    chainTimeout = setTimeout(() => {
                        // Step 3
                        showChainStep(3);
                        
                        chainTimeout = setTimeout(() => {
                            showChainLine(3);
                            
                            chainTimeout = setTimeout(() => {
                                // Step 4
                                showChainStep(4);
                                
                                chainTimeout = setTimeout(() => {
                                    showChainLine(4);
                                    
                                    chainTimeout = setTimeout(() => {
                                        // Step 5 (rewards)
                                        showChainStep(5);
                                        
                                        chainTimeout = setTimeout(() => {
                                            // Show continue
                                            document.getElementById('chain-continue').classList.remove('opacity-0', 'translate-y-4');
                                            document.getElementById('chain-continue').classList.add('opacity-100', 'translate-y-0');
                                        }, 800);
                                    }, 600);
                                }, 400);
                            }, 700);
                        }, 400);
                    }, 700);
                }, 400);
            }, 700);
        }, 400);
    }, 500);
}

function showChainStep(num) {
    const step = document.getElementById('chain-step-' + num);
    if (step) {
        step.classList.remove('opacity-0', '-translate-x-8', 'translate-x-8', 'translate-y-8');
        step.classList.add('opacity-100', 'translate-x-0', 'translate-y-0');
    }
}

function showChainLine(num) {
    const line = document.getElementById('chain-line-' + num);
    if (line) {
        line.style.transition = 'height 0.4s ease';
        line.style.height = '40px';
    }
}

// Initialize first animation on page load
document.addEventListener('DOMContentLoaded', function() {
    restartInstagram();
});
</script>

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
                <span>Jetzt 7 Tage kostenlos testen</span>
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
            <a href="/branchen/handwerker" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-full text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors text-sm font-medium">
                <i class="fas fa-hammer mr-1"></i> Handwerker
            </a>
            <a href="/branchen/onlinemarketing" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-full text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors text-sm font-medium">
                <i class="fas fa-bullhorn mr-1"></i> Online-Marketing
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../templates/marketing/footer.php'; ?>
