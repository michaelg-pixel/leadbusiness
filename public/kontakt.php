<?php
/**
 * Leadbusiness - Kontakt
 * Kontaktformular und Informationen
 */

$pageTitle = 'Kontakt';
$metaDescription = 'Kontaktieren Sie uns – wir helfen Ihnen gerne bei Fragen zu Leadbusiness, Ihrem Empfehlungsprogramm oder technischem Support.';
$currentPage = 'kontakt';

// Form processing
$formSubmitted = false;
$formError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Here would be the actual form processing
    // For now, just show success
    $formSubmitted = true;
}

require_once __DIR__ . '/../templates/marketing/header.php';
?>

<!-- Hero Section -->
<section class="py-20 bg-gradient-to-br from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto">
            <span class="text-primary-500 font-semibold uppercase tracking-wide">Kontakt</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mt-3 mb-6">
                Wir sind <span class="gradient-text">für Sie da</span>
            </h1>
            <p class="text-xl text-gray-600">
                Haben Sie Fragen? Möchten Sie eine Demo? 
                Wir antworten in der Regel innerhalb von 24 Stunden.
            </p>
        </div>
    </div>
</section>

<!-- Contact Content -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-12">
            
            <!-- Contact Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-gray-200 p-8">
                    <h2 class="text-2xl font-bold mb-6">Nachricht senden</h2>
                    
                    <?php if ($formSubmitted): ?>
                    <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center">
                        <div class="text-4xl mb-4">✅</div>
                        <h3 class="text-xl font-bold text-green-800 mb-2">Nachricht gesendet!</h3>
                        <p class="text-green-700">Vielen Dank für Ihre Nachricht. Wir melden uns in Kürze bei Ihnen.</p>
                    </div>
                    <?php else: ?>
                    
                    <form method="POST" action="/kontakt" class="space-y-6">
                        <!-- Honeypot -->
                        <div style="display: none;">
                            <input type="text" name="website" value="">
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="form-label">Name *</label>
                                <input type="text" id="name" name="name" required 
                                       class="form-input" placeholder="Ihr Name">
                            </div>
                            <div>
                                <label for="email" class="form-label">E-Mail *</label>
                                <input type="email" id="email" name="email" required 
                                       class="form-input" placeholder="ihre@email.de">
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="company" class="form-label">Unternehmen</label>
                                <input type="text" id="company" name="company" 
                                       class="form-input" placeholder="Ihr Unternehmen">
                            </div>
                            <div>
                                <label for="phone" class="form-label">Telefon</label>
                                <input type="tel" id="phone" name="phone" 
                                       class="form-input" placeholder="+49 123 456789">
                            </div>
                        </div>
                        
                        <div>
                            <label for="subject" class="form-label">Betreff *</label>
                            <select id="subject" name="subject" required class="form-input">
                                <option value="">Bitte wählen...</option>
                                <option value="demo" <?= ($_GET['subject'] ?? '') === 'demo' ? 'selected' : '' ?>>Demo anfragen</option>
                                <option value="sales" <?= ($_GET['subject'] ?? '') === 'sales' ? 'selected' : '' ?>>Vertriebsanfrage</option>
                                <option value="enterprise" <?= ($_GET['subject'] ?? '') === 'enterprise' ? 'selected' : '' ?>>Enterprise-Anfrage</option>
                                <option value="support">Technischer Support</option>
                                <option value="partnership">Partnerschaft</option>
                                <option value="other">Sonstiges</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="message" class="form-label">Nachricht *</label>
                            <textarea id="message" name="message" required rows="5" 
                                      class="form-input" placeholder="Wie können wir Ihnen helfen?"></textarea>
                        </div>
                        
                        <div class="flex items-start gap-3">
                            <input type="checkbox" id="privacy" name="privacy" required 
                                   class="checkbox-custom mt-1">
                            <label for="privacy" class="text-sm text-gray-600">
                                Ich habe die <a href="/datenschutz" class="text-primary-500 hover:underline">Datenschutzerklärung</a> 
                                gelesen und bin mit der Verarbeitung meiner Daten einverstanden. *
                            </label>
                        </div>
                        
                        <button type="submit" class="btn-primary btn-large w-full">
                            <i class="fas fa-paper-plane mr-2"></i> Nachricht senden
                        </button>
                    </form>
                    
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Contact Info -->
            <div class="space-y-8">
                <!-- Direct Contact -->
                <div class="bg-gray-50 rounded-2xl p-6">
                    <h3 class="text-lg font-bold mb-4">Direkter Kontakt</h3>
                    <div class="space-y-4">
                        <a href="mailto:support@leadbusiness.de" class="flex items-center gap-4 p-3 bg-white rounded-xl hover:shadow-md transition-shadow">
                            <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center text-primary-500">
                                <i class="fas fa-envelope text-xl"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">E-Mail</div>
                                <div class="text-gray-600">support@leadbusiness.de</div>
                            </div>
                        </a>
                        
                        <a href="tel:+4930123456789" class="flex items-center gap-4 p-3 bg-white rounded-xl hover:shadow-md transition-shadow">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-500">
                                <i class="fas fa-phone text-xl"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">Telefon</div>
                                <div class="text-gray-600">+49 30 123 456 789</div>
                            </div>
                        </a>
                    </div>
                </div>
                
                <!-- Office Hours -->
                <div class="bg-gray-50 rounded-2xl p-6">
                    <h3 class="text-lg font-bold mb-4">Erreichbarkeit</h3>
                    <div class="space-y-3 text-gray-600">
                        <div class="flex justify-between">
                            <span>Montag - Freitag</span>
                            <span class="font-semibold">9:00 - 18:00 Uhr</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Samstag - Sonntag</span>
                            <span class="text-gray-400">Geschlossen</span>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-4">
                        <i class="fas fa-info-circle mr-1"></i>
                        E-Mail-Anfragen werden auch außerhalb der Geschäftszeiten bearbeitet.
                    </p>
                </div>
                
                <!-- Quick Links -->
                <div class="bg-gray-50 rounded-2xl p-6">
                    <h3 class="text-lg font-bold mb-4">Schnelle Hilfe</h3>
                    <div class="space-y-3">
                        <a href="/faq" class="flex items-center gap-3 text-gray-600 hover:text-primary-500 transition-colors">
                            <i class="fas fa-question-circle"></i>
                            <span>Häufige Fragen (FAQ)</span>
                        </a>
                        <a href="/funktionen" class="flex items-center gap-3 text-gray-600 hover:text-primary-500 transition-colors">
                            <i class="fas fa-star"></i>
                            <span>Funktionen entdecken</span>
                        </a>
                        <a href="/preise" class="flex items-center gap-3 text-gray-600 hover:text-primary-500 transition-colors">
                            <i class="fas fa-tags"></i>
                            <span>Preise vergleichen</span>
                        </a>
                    </div>
                </div>
                
                <!-- Address -->
                <div class="bg-gray-50 rounded-2xl p-6">
                    <h3 class="text-lg font-bold mb-4">Adresse</h3>
                    <address class="text-gray-600 not-italic">
                        Leadbusiness GmbH<br>
                        Musterstraße 123<br>
                        10115 Berlin<br>
                        Deutschland
                    </address>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Preview -->
<section class="py-20 bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl font-bold">Vielleicht schon beantwortet?</h2>
            <p class="text-gray-600 mt-2">Schauen Sie in unsere FAQ</p>
        </div>
        
        <div class="space-y-4">
            <div class="faq-item bg-white border rounded-xl p-4">
                <div class="faq-question">
                    <span>Wie lange dauert die Einrichtung?</span>
                </div>
                <div class="faq-answer text-gray-600">
                    <p>Das Onboarding dauert nur etwa 5 Minuten. Danach ist Ihr Empfehlungsprogramm sofort aktiv.</p>
                </div>
            </div>
            
            <div class="faq-item bg-white border rounded-xl p-4">
                <div class="faq-question">
                    <span>Gibt es eine Testphase?</span>
                </div>
                <div class="faq-answer text-gray-600">
                    <p>Ja, Sie können Leadbusiness 14 Tage lang kostenlos testen. Keine Kreditkarte erforderlich.</p>
                </div>
            </div>
            
            <div class="faq-item bg-white border rounded-xl p-4">
                <div class="faq-question">
                    <span>Ist Leadbusiness DSGVO-konform?</span>
                </div>
                <div class="faq-answer text-gray-600">
                    <p>Ja, zu 100%. Deutsches Unternehmen, Hosting in Deutschland, Double-Opt-In, alle Rechtstexte inklusive.</p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-8">
            <a href="/faq" class="text-primary-500 font-semibold hover:underline">
                Alle FAQ ansehen →
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../templates/marketing/footer.php'; ?>
