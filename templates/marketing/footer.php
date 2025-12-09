    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300">
        
        <!-- Main Footer -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                
                <!-- Company Info -->
                <div class="space-y-6">
                    <a href="/" class="flex items-center space-x-3">
                        <div class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center">
                            <i class="fas fa-paper-plane text-white text-lg"></i>
                        </div>
                        <span class="text-xl font-bold text-white">Lead<span class="text-primary-400">business</span></span>
                    </a>
                    <p class="text-gray-400 leading-relaxed">
                        Vollautomatisches Empfehlungsprogramm für Ihr Unternehmen. 
                        Kunden werben Kunden und werden automatisch belohnt.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center hover:bg-primary-500 transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center hover:bg-primary-500 transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center hover:bg-primary-500 transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center hover:bg-primary-500 transition-colors">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-white font-semibold text-lg mb-6">Produkt</h3>
                    <ul class="space-y-4">
                        <li>
                            <a href="/funktionen" class="text-gray-400 hover:text-white transition-colors">
                                Funktionen
                            </a>
                        </li>
                        <li>
                            <a href="/preise" class="text-gray-400 hover:text-white transition-colors">
                                Preise
                            </a>
                        </li>
                        <li>
                            <a href="/faq" class="text-gray-400 hover:text-white transition-colors">
                                FAQ
                            </a>
                        </li>
                        <li>
                            <a href="/admin/login.php" class="text-gray-400 hover:text-white transition-colors">
                                Login
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Branchen -->
                <div>
                    <h3 class="text-white font-semibold text-lg mb-6">Branchen</h3>
                    <ul class="space-y-4">
                        <li>
                            <a href="/branchen/zahnarzt" class="text-gray-400 hover:text-white transition-colors">
                                Zahnärzte
                            </a>
                        </li>
                        <li>
                            <a href="/branchen/friseur" class="text-gray-400 hover:text-white transition-colors">
                                Friseure
                            </a>
                        </li>
                        <li>
                            <a href="/branchen/fitness" class="text-gray-400 hover:text-white transition-colors">
                                Fitnessstudios
                            </a>
                        </li>
                        <li>
                            <a href="/branchen/coach" class="text-gray-400 hover:text-white transition-colors">
                                Coaches & Berater
                            </a>
                        </li>
                        <li>
                            <a href="/branchen/onlineshop" class="text-gray-400 hover:text-white transition-colors">
                                Online-Shops
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Kontakt -->
                <div>
                    <h3 class="text-white font-semibold text-lg mb-6">Kontakt</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-envelope mt-1 text-primary-400"></i>
                            <a href="mailto:support@empfehlungen.cloud" class="text-gray-400 hover:text-white transition-colors">
                                support@empfehlungen.cloud
                            </a>
                        </li>
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-phone mt-1 text-primary-400"></i>
                            <a href="tel:+4930123456789" class="text-gray-400 hover:text-white transition-colors">
                                +49 30 123 456 789
                            </a>
                        </li>
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-clock mt-1 text-primary-400"></i>
                            <span class="text-gray-400">
                                Mo-Fr: 9:00 - 18:00 Uhr
                            </span>
                        </li>
                    </ul>
                    
                    <!-- Trust Badges -->
                    <div class="mt-8 flex flex-wrap gap-3">
                        <div class="trust-badge">
                            <i class="fas fa-shield-alt text-green-500"></i>
                            <span>DSGVO-konform</span>
                        </div>
                        <div class="trust-badge">
                            <i class="fas fa-server text-blue-500"></i>
                            <span>Hosting in DE</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bottom Footer -->
        <div class="border-t border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    <p class="text-gray-500 text-sm">
                        © <?= date('Y') ?> Leadbusiness. Alle Rechte vorbehalten.
                    </p>
                    <div class="flex flex-wrap justify-center gap-6 text-sm">
                        <a href="/impressum" class="text-gray-500 hover:text-white transition-colors">
                            Impressum
                        </a>
                        <a href="/datenschutz" class="text-gray-500 hover:text-white transition-colors">
                            Datenschutz
                        </a>
                        <a href="/agb" class="text-gray-500 hover:text-white transition-colors">
                            AGB
                        </a>
                        <button onclick="openCookieSettings()" class="text-gray-500 hover:text-white transition-colors">
                            Cookie-Einstellungen
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Cookie Banner -->
    <div id="cookie-banner" style="display:none;" class="fixed inset-0 z-[9999] flex items-end sm:items-center justify-center">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
        
        <!-- Banner Content -->
        <div class="relative w-full max-w-2xl mx-4 mb-4 sm:mb-0 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-primary-500 to-purple-600 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-cookie-bite text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-lg">Cookie-Einstellungen</h3>
                        <p class="text-white/80 text-sm">Wir respektieren Ihre Privatsphäre</p>
                    </div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="px-6 py-5">
                <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed mb-6">
                    Wir verwenden Cookies, um Ihnen die bestmögliche Erfahrung auf unserer Website zu bieten. 
                    Einige Cookies sind für den Betrieb der Website unerlässlich, während andere uns helfen, 
                    die Website und Ihr Erlebnis zu verbessern.
                </p>
                
                <!-- Quick Info -->
                <div class="grid grid-cols-3 gap-3 mb-6">
                    <div class="bg-gray-50 dark:bg-slate-700 rounded-xl p-3 text-center">
                        <div class="w-8 h-8 mx-auto mb-2 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check text-green-500 text-sm"></i>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-300 font-medium">Notwendig</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700 rounded-xl p-3 text-center">
                        <div class="w-8 h-8 mx-auto mb-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-bar text-blue-500 text-sm"></i>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-300 font-medium">Statistik</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700 rounded-xl p-3 text-center">
                        <div class="w-8 h-8 mx-auto mb-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-bullhorn text-purple-500 text-sm"></i>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-300 font-medium">Marketing</p>
                    </div>
                </div>
                
                <!-- Buttons -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <button onclick="acceptAllCookies()" class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-check"></i>
                        Alle akzeptieren
                    </button>
                    <button onclick="rejectAllCookies()" class="flex-1 px-6 py-3 bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-gray-200 font-semibold rounded-xl hover:bg-gray-300 dark:hover:bg-slate-500 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i>
                        Nur notwendige
                    </button>
                    <button onclick="openCookieSettings()" class="flex-1 px-6 py-3 border-2 border-gray-300 dark:border-slate-500 text-gray-700 dark:text-gray-200 font-semibold rounded-xl hover:border-primary-500 hover:text-primary-500 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-cog"></i>
                        Einstellungen
                    </button>
                </div>
                
                <!-- Footer Links -->
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-slate-600 flex justify-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                    <a href="/datenschutz" class="hover:text-primary-500">Datenschutz</a>
                    <span>|</span>
                    <a href="/impressum" class="hover:text-primary-500">Impressum</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Cookie Settings Modal -->
    <div id="cookie-settings-modal" style="display:none;" class="fixed inset-0 z-[10000] flex items-center justify-center">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeCookieSettings()"></div>
        
        <!-- Modal Content -->
        <div class="relative w-full max-w-lg mx-4 max-h-[90vh] overflow-auto bg-white dark:bg-slate-800 rounded-2xl shadow-2xl">
            <!-- Header -->
            <div class="sticky top-0 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Cookie-Einstellungen</h3>
                <button onclick="closeCookieSettings()" class="p-2 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>
            
            <!-- Content -->
            <div class="px-6 py-5 space-y-4">
                
                <!-- Notwendig -->
                <div class="bg-gray-50 dark:bg-slate-700 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-shield-alt text-green-500"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white">Notwendig</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Erforderlich für Grundfunktionen</p>
                            </div>
                        </div>
                        <div class="relative">
                            <input type="checkbox" checked disabled class="sr-only peer">
                            <div class="w-11 h-6 bg-green-500 rounded-full"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform translate-x-5"></div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Diese Cookies sind für das Funktionieren der Website unerlässlich und können nicht deaktiviert werden.
                    </p>
                </div>
                
                <!-- Statistik -->
                <div class="bg-gray-50 dark:bg-slate-700 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-line text-blue-500"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white">Statistik</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Hilft uns, die Website zu verbessern</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="cookie-statistics" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-300 dark:bg-slate-600 peer-checked:bg-blue-500 rounded-full peer-focus:ring-2 peer-focus:ring-blue-300 transition-colors"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
                        </label>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Diese Cookies sammeln anonyme Informationen darüber, wie Besucher unsere Website nutzen.
                    </p>
                </div>
                
                <!-- Marketing -->
                <div class="bg-gray-50 dark:bg-slate-700 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-bullhorn text-purple-500"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white">Marketing</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Personalisierte Werbung</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="cookie-marketing" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-300 dark:bg-slate-600 peer-checked:bg-purple-500 rounded-full peer-focus:ring-2 peer-focus:ring-purple-300 transition-colors"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
                        </label>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Diese Cookies werden verwendet, um Ihnen relevante Werbung anzuzeigen.
                    </p>
                </div>
                
            </div>
            
            <!-- Footer -->
            <div class="sticky bottom-0 bg-white dark:bg-slate-800 border-t border-gray-200 dark:border-slate-700 px-6 py-4 flex gap-3">
                <button onclick="saveCustomCookies()" class="flex-1 px-6 py-3 bg-gradient-to-r from-primary-500 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all">
                    Auswahl speichern
                </button>
                <button onclick="acceptAllCookies()" class="px-6 py-3 bg-green-500 text-white font-semibold rounded-xl hover:bg-green-600 transition-all">
                    Alle akzeptieren
                </button>
            </div>
        </div>
    </div>
    
    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-8 right-8 w-12 h-12 gradient-bg rounded-full shadow-lg text-white opacity-0 invisible transition-all duration-300 hover:scale-110 z-40">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <!-- JavaScript -->
    <script src="/assets/js/main.js"></script>
    
    <!-- Cookie Consent Script v6 - Production -->
    <script>
    (function() {
        'use strict';
        
        var COOKIE_NAME = 'lb_cookie_consent';
        
        function setCookie(name, value, days) {
            var d = new Date();
            d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = name + '=' + value + ';expires=' + d.toUTCString() + ';path=/;SameSite=Lax';
        }
        
        function getCookie(name) {
            var cname = name + '=';
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i].trim();
                if (c.indexOf(cname) === 0) {
                    return c.substring(cname.length);
                }
            }
            return null;
        }
        
        function hasConsent() {
            if (getCookie(COOKIE_NAME)) return true;
            try { 
                if (localStorage.getItem(COOKIE_NAME)) return true; 
            } catch(e) {}
            return false;
        }
        
        function saveConsent(value) {
            setCookie(COOKIE_NAME, value, 365);
            try { localStorage.setItem(COOKIE_NAME, value); } catch(e) {}
        }
        
        function hideBanner() {
            var banner = document.getElementById('cookie-banner');
            var modal = document.getElementById('cookie-settings-modal');
            if (banner) banner.style.display = 'none';
            if (modal) modal.style.display = 'none';
        }
        
        function showBanner() {
            var banner = document.getElementById('cookie-banner');
            if (banner) banner.style.display = 'flex';
        }
        
        // Global functions
        window.acceptAllCookies = function() {
            saveConsent('all');
            hideBanner();
        };
        
        window.rejectAllCookies = function() {
            saveConsent('necessary');
            hideBanner();
        };
        
        window.saveCustomCookies = function() {
            var stats = document.getElementById('cookie-statistics');
            var marketing = document.getElementById('cookie-marketing');
            var consent = 'necessary';
            if (stats && stats.checked && marketing && marketing.checked) consent = 'all';
            else if (stats && stats.checked) consent = 'statistics';
            else if (marketing && marketing.checked) consent = 'marketing';
            saveConsent(consent);
            hideBanner();
        };
        
        window.openCookieSettings = function() {
            document.getElementById('cookie-banner').style.display = 'none';
            document.getElementById('cookie-settings-modal').style.display = 'flex';
        };
        
        window.closeCookieSettings = function() {
            document.getElementById('cookie-settings-modal').style.display = 'none';
            if (!hasConsent()) showBanner();
        };
        
        // Init - show banner if no consent
        if (!hasConsent()) {
            showBanner();
        }
    })();
    
    // Back to top button
    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('back-to-top');
        if (btn) {
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 500) {
                    btn.classList.remove('opacity-0', 'invisible');
                    btn.classList.add('opacity-100', 'visible');
                } else {
                    btn.classList.add('opacity-0', 'invisible');
                    btn.classList.remove('opacity-100', 'visible');
                }
            });
            btn.addEventListener('click', function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    });
    </script>
    
    <?php if (isset($additionalScripts)): ?>
        <?= $additionalScripts ?>
    <?php endif; ?>
    
</body>
</html>
