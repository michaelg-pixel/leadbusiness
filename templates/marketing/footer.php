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
                            <a href="/onboarding" class="text-gray-400 hover:text-white transition-colors">
                                Jetzt starten
                            </a>
                        </li>
                        <li>
                            <a href="/dashboard/login" class="text-gray-400 hover:text-white transition-colors">
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
                            <a href="mailto:support@leadbusiness.de" class="text-gray-400 hover:text-white transition-colors">
                                support@leadbusiness.de
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
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Cookie Banner -->
    <div id="cookie-banner" class="hidden fixed bottom-0 left-0 right-0 bg-white shadow-2xl border-t z-50 p-4 md:p-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex-1">
                <p class="text-gray-600 text-sm md:text-base">
                    Wir verwenden Cookies, um Ihre Erfahrung auf unserer Website zu verbessern. 
                    Durch die weitere Nutzung stimmen Sie unserer 
                    <a href="/datenschutz" class="text-primary-500 hover:underline">Datenschutzerklärung</a> zu.
                </p>
            </div>
            <div class="flex gap-4">
                <button id="cookie-accept" class="btn-primary text-sm">
                    Akzeptieren
                </button>
                <a href="/datenschutz" class="btn-secondary text-sm">
                    Mehr erfahren
                </a>
            </div>
        </div>
    </div>
    
    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-8 right-8 w-12 h-12 gradient-bg rounded-full shadow-lg text-white opacity-0 invisible transition-all duration-300 hover:scale-110 z-40">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <!-- JavaScript -->
    <script src="/assets/js/main.js"></script>
    
    <!-- Back to Top Script -->
    <script>
        const backToTop = document.getElementById('back-to-top');
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 500) {
                backToTop.classList.remove('opacity-0', 'invisible');
                backToTop.classList.add('opacity-100', 'visible');
            } else {
                backToTop.classList.add('opacity-0', 'invisible');
                backToTop.classList.remove('opacity-100', 'visible');
            }
        });
        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
    
    <?php if (isset($additionalScripts)): ?>
        <?= $additionalScripts ?>
    <?php endif; ?>
    
</body>
</html>
