<?php
/**
 * Leadbusiness - FAQ
 * Häufig gestellte Fragen
 */

$pageTitle = 'FAQ - Häufige Fragen';
$metaDescription = 'Antworten auf alle Fragen zu Leadbusiness: Einrichtung, Funktionen, DSGVO, Preise, technische Anforderungen und mehr.';
$currentPage = 'faq';

require_once __DIR__ . '/../templates/marketing/header.php';
?>

<!-- Hero Section -->
<section class="py-20 bg-gradient-to-br from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto">
            <span class="text-primary-500 font-semibold uppercase tracking-wide">FAQ</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mt-3 mb-6">
                Häufig gestellte <span class="gradient-text">Fragen</span>
            </h1>
            <p class="text-xl text-gray-600">
                Finden Sie schnell Antworten auf Ihre Fragen. 
                Noch nicht dabei? <a href="/kontakt" class="text-primary-500 hover:underline">Kontaktieren Sie uns</a>.
            </p>
        </div>
    </div>
</section>

<!-- FAQ Categories -->
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Category: Allgemein -->
        <div class="mb-16">
            <h2 class="text-2xl font-bold mb-6 flex items-center gap-3">
                <span class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center text-primary-500">
                    <i class="fas fa-info-circle"></i>
                </span>
                Allgemein
            </h2>
            
            <div class="space-y-4">
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Was ist Leadbusiness?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Leadbusiness ist ein vollautomatisches Empfehlungsprogramm für Unternehmen. Ihre Kunden bekommen persönliche Links zum Teilen und werden automatisch belohnt, wenn sie neue Kunden bringen. Das System übernimmt alles: Tracking, E-Mails, Belohnungen – Sie müssen nichts tun.</p>
                    </div>
                </div>
                
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Für wen ist Leadbusiness geeignet?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Leadbusiness eignet sich für alle Unternehmen mit zufriedenen Kunden: Zahnärzte, Friseure, Fitnessstudios, Restaurants, Online-Shops, Coaches, Berater, SaaS-Anbieter und viele mehr. Wenn Sie Kunden haben, die Sie weiterempfehlen könnten, ist Leadbusiness für Sie.</p>
                    </div>
                </div>
                
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Brauche ich technisches Wissen?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Nein, überhaupt nicht! Sie füllen nur ein Onboarding-Formular aus (ca. 5 Minuten) – alles andere erledigen wir. Keine Installation, kein Code, keine Technik. Ihr Empfehlungsprogramm läuft sofort nach der Einrichtung.</p>
                    </div>
                </div>
                
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Wie unterscheidet sich Leadbusiness von anderen Tools?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Leadbusiness ist speziell für den deutschen Markt entwickelt: DSGVO-konform, auf Deutsch, Hosting in Deutschland. Außerdem ist es ein "Done-for-You" Service – Sie müssen nichts selbst konfigurieren. Wir übernehmen die komplette Einrichtung basierend auf Ihrer Branche.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Category: Einrichtung -->
        <div class="mb-16">
            <h2 class="text-2xl font-bold mb-6 flex items-center gap-3">
                <span class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center text-green-500">
                    <i class="fas fa-cogs"></i>
                </span>
                Einrichtung
            </h2>
            
            <div class="space-y-4">
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Wie lange dauert die Einrichtung?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Das Onboarding dauert nur etwa 5 Minuten. Sie beantworten 8 einfache Fragen zu Ihrem Unternehmen, laden Ihr Logo hoch und wählen ein Design. Danach ist Ihr Empfehlungsprogramm sofort aktiv und bereit.</p>
                    </div>
                </div>
                
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Was ist in der Einrichtung enthalten?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Die 499€ Einrichtungsgebühr beinhaltet: Eigene Subdomain (z.B. ihre-firma.empfohlen.de), komplettes E-Mail-System, branchenspezifische Belohnungsvorschläge, professionelles Design passend zu Ihrer Branche, Logo-Integration, QR-Code und Share-Grafiken.</p>
                    </div>
                </div>
                
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Muss ich etwas auf meiner Website installieren?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Nein. Ihr Empfehlungsprogramm läuft komplett auf unseren Servern unter Ihrer Subdomain. Sie müssen nichts installieren oder in Ihre Website einbinden. Optional können Professional-Kunden ein Widget einbetten, aber das ist nicht erforderlich.</p>
                    </div>
                </div>
                
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Kann ich mein Programm später anpassen?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Ja, Sie können jederzeit über Ihr Dashboard Anpassungen vornehmen: Belohnungsstufen ändern, Texte anpassen, Hintergrundbilder wechseln, Farben ändern. Alle Änderungen werden sofort wirksam.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Category: Funktionen -->
        <div class="mb-16">
            <h2 class="text-2xl font-bold mb-6 flex items-center gap-3">
                <span class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-blue-500">
                    <i class="fas fa-star"></i>
                </span>
                Funktionen
            </h2>
            
            <div class="space-y-4">
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Wie funktioniert das Empfehlungsprogramm?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>1. Ihre Kunden melden sich auf Ihrer Empfehlungsseite an. 2. Sie bekommen einen persönlichen Link. 3. Wenn jemand über diesen Link kommt und sich anmeldet, wird das getrackt. 4. Bei genug Empfehlungen wird automatisch die Belohnung versendet. Alles vollautomatisch.</p>
                    </div>
                </div>
                
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Welche Belohnungen kann ich anbieten?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Sie haben 6 Belohnungstypen zur Auswahl: Rabatte (%), Gutschein-Codes, Gratis-Produkte, Gratis-Services, digitale Downloads (z.B. E-Books) und Wertgutscheine (€). Die Belohnungen werden automatisch per E-Mail versendet.</p>
                    </div>
                </div>
                
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Was ist Gamification?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Gamification macht das Empfehlen zum Spiel: Empfehler sehen einen Fortschrittsbalken zur nächsten Belohnung, sammeln Badges für Erfolge, können sich im Leaderboard mit anderen messen. Das motiviert sie, aktiv zu bleiben und mehr zu empfehlen.</p>
                    </div>
                </div>
                
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Welche Share-Buttons gibt es?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>11 Plattformen: WhatsApp, Facebook, Telegram, E-Mail, SMS/iMessage, LinkedIn, XING, Twitter/X, Pinterest, Link kopieren und QR-Code. So können Ihre Kunden auf der Plattform teilen, die sie am liebsten nutzen.</p>
                    </div>
                </div>
                
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Werden automatisch E-Mails versendet?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Ja, das System versendet automatisch: Willkommens-E-Mail, Erinnerungen bei Inaktivität, Benachrichtigung kurz vor einer neuen Stufe, Belohnungs-E-Mails und Badge-Benachrichtigungen. Professional-Kunden können auch manuelle Broadcast-E-Mails senden.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Category: DSGVO & Datenschutz -->
        <div class="mb-16">
            <h2 class="text-2xl font-bold mb-6 flex items-center gap-3">
                <span class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center text-red-500">
                    <i class="fas fa-shield-alt"></i>
                </span>
                DSGVO & Datenschutz
            </h2>
            
            <div class="space-y-4">
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Ist Leadbusiness DSGVO-konform?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Ja, zu 100%. Wir sind ein deutsches Unternehmen, alle Daten werden in Deutschland gehostet, wir nutzen Double-Opt-In für E-Mail-Bestätigungen, und Sie erhalten alle nötigen Rechtstexte (AGB, Datenschutz) automatisch für Ihre Empfehlungsseite.</p>
                    </div>
                </div>
                
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Wo werden die Daten gespeichert?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Alle Daten werden auf Servern in Deutschland gespeichert. Unser Hosting-Partner ist Hostinger mit Rechenzentren in Deutschland. Wir nutzen keine US-amerikanischen Cloud-Dienste für sensible Daten.</p>
                    </div>
                </div>
                
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Was passiert mit den E-Mail-Adressen der Empfehler?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Die E-Mail-Adressen gehören Ihnen als Kunde. Professional-Kunden können diese exportieren. Empfehler bestätigen ihre E-Mail per Double-Opt-In und können sich jederzeit abmelden. Nach Kündigung werden alle Daten auf Wunsch gelöscht.</p>
                    </div>
                </div>
                
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Brauche ich einen AV-Vertrag?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Ja, wir stellen Ihnen automatisch einen Auftragsverarbeitungsvertrag (AV-Vertrag) zur Verfügung. Dieser wird bei der Registrierung digital abgeschlossen und ist jederzeit in Ihrem Dashboard abrufbar.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Category: Preise & Zahlung -->
        <div class="mb-16">
            <h2 class="text-2xl font-bold mb-6 flex items-center gap-3">
                <span class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center text-yellow-500">
                    <i class="fas fa-credit-card"></i>
                </span>
                Preise & Zahlung
            </h2>
            
            <div class="space-y-4">
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Was kostet Leadbusiness?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Einmalig 499€ für die Einrichtung, dann 49€/Monat (Starter) oder 99€/Monat (Professional). Die ersten 7 Tage sind kostenlos zum Testen. Alle Preise verstehen sich zzgl. gesetzlicher MwSt.</p>
                    </div>
                </div>
                
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Gibt es eine Testphase?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Ja, Sie können Leadbusiness 7 Tage lang kostenlos testen. Keine Kreditkarte erforderlich. Nach der Testphase entscheiden Sie, ob Sie weitermachen möchten. Wenn nicht, werden alle Daten gelöscht.</p>
                    </div>
                </div>
                
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Welche Zahlungsmethoden gibt es?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Wir akzeptieren Kreditkarten (Visa, Mastercard), SEPA-Lastschrift, PayPal und auf Anfrage auch Rechnung (nur Enterprise). Alle Zahlungen werden sicher über Digistore24 abgewickelt.</p>
                    </div>
                </div>
                
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Kann ich jederzeit kündigen?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Ja, es gibt keine Mindestlaufzeit. Sie können monatlich zum Ende des Abrechnungszeitraums kündigen. Die einmalige Einrichtungsgebühr wird nicht erstattet. Bei Kündigung können Sie Ihre Daten exportieren.</p>
                    </div>
                </div>
                
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Kann ich meinen Plan wechseln?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Ja, jederzeit. Bei einem Upgrade von Starter auf Professional wird der Unterschied anteilig berechnet. Ein Downgrade ist zum nächsten Abrechnungszeitraum möglich (vorausgesetzt, Sie sind unter den Starter-Limits).</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Category: Technisches -->
        <div class="mb-16">
            <h2 class="text-2xl font-bold mb-6 flex items-center gap-3">
                <span class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center text-purple-500">
                    <i class="fas fa-code"></i>
                </span>
                Technisches
            </h2>
            
            <div class="space-y-4">
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Kann ich eine eigene Domain verwenden?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Standard ist eine Subdomain unter empfohlen.de (z.B. ihre-firma.empfohlen.de). Professional-Kunden können gegen Aufpreis eine eigene Domain verwenden. Enterprise-Kunden bekommen dies inklusive.</p>
                    </div>
                </div>
                
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Gibt es eine API?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Ja, Professional- und Enterprise-Kunden haben Zugang zu unserer REST-API. Sie können damit Empfehler-Daten abrufen, Conversions melden und das System in Ihre bestehende Software integrieren. Dokumentation wird bereitgestellt.</p>
                    </div>
                </div>
                
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Was sind Webhooks?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Webhooks informieren Ihre Systeme in Echtzeit über Ereignisse: Neue Empfehler angemeldet, Conversion registriert, Belohnung freigeschaltet. So können Sie z.B. automatisch in Ihr CRM übertragen. Verfügbar für Professional und Enterprise.</p>
                    </div>
                </div>
                
                <div class="faq-item border rounded-xl p-4">
                    <div class="faq-question">
                        <span>Funktioniert Leadbusiness auf allen Geräten?</span>
                    </div>
                    <div class="faq-answer text-gray-600">
                        <p>Ja, alle Seiten sind responsive und funktionieren auf Desktop, Tablet und Smartphone. Die Share-Buttons sind für mobile Nutzung optimiert – WhatsApp öffnet z.B. direkt die App.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Still have questions -->
        <div class="bg-gray-50 rounded-2xl p-8 text-center">
            <h3 class="text-xl font-bold mb-4">Noch Fragen?</h3>
            <p class="text-gray-600 mb-6">
                Wir sind für Sie da und beantworten gerne alle Ihre Fragen persönlich.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/kontakt" class="btn-primary inline-flex items-center justify-center gap-2">
                    <i class="fas fa-envelope"></i>
                    <span>Kontakt aufnehmen</span>
                </a>
                <a href="mailto:support@leadbusiness.de" class="btn-secondary inline-flex items-center justify-center gap-2">
                    <span>support@leadbusiness.de</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 gradient-bg text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold mb-6">
            Bereit loszulegen?
        </h2>
        <p class="text-xl text-white/90 mb-8">
            Starten Sie Ihr Empfehlungsprogramm in nur 5 Minuten.
        </p>
        <a href="/onboarding" class="btn-primary btn-large bg-white text-primary-600 hover:bg-gray-100 inline-flex items-center gap-2">
            <span>Jetzt kostenlos starten</span>
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</section>

<?php require_once __DIR__ . '/../templates/marketing/footer.php'; ?>
