<?php
/**
 * Leadbusiness - Datenschutzerklärung
 */

$pageTitle = 'Datenschutzerklärung';
$metaDescription = 'Datenschutzerklärung von Leadbusiness - Ihr Empfehlungsprogramm DSGVO-konform.';
$currentPage = 'datenschutz';

require_once __DIR__ . '/../templates/marketing/header.php';
?>

<!-- Page Header -->
<section class="pt-32 pb-12 gradient-bg text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Datenschutzerklärung</h1>
        <p class="text-xl text-white/90">Stand: <?= date('d.m.Y') ?></p>
    </div>
</section>

<!-- Content -->
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="prose prose-lg max-w-none">
            
            <h2>1. Verantwortlicher</h2>
            <p>
                Verantwortlicher im Sinne der Datenschutz-Grundverordnung (DSGVO) ist:
            </p>
            <p>
                <strong>[Ihr Unternehmensname]</strong><br>
                [Straße und Hausnummer]<br>
                [PLZ und Ort]<br>
                E-Mail: kontakt@empfohlen.de<br>
                Telefon: [Ihre Telefonnummer]
            </p>
            
            <h2>2. Übersicht der Verarbeitungen</h2>
            <p>
                Die nachfolgende Übersicht fasst die Arten der verarbeiteten Daten und die Zwecke ihrer 
                Verarbeitung zusammen und verweist auf die betroffenen Personen.
            </p>
            
            <h3>2.1 Arten der verarbeiteten Daten</h3>
            <ul>
                <li>Bestandsdaten (z.B. Namen, Adressen)</li>
                <li>Kontaktdaten (z.B. E-Mail, Telefonnummern)</li>
                <li>Inhaltsdaten (z.B. Eingaben in Onlineformularen)</li>
                <li>Vertragsdaten (z.B. Vertragsgegenstand, Laufzeit)</li>
                <li>Zahlungsdaten (z.B. Bankverbindung, Zahlungshistorie)</li>
                <li>Nutzungsdaten (z.B. besuchte Webseiten, Zugriffszeiten)</li>
                <li>Meta-/Kommunikationsdaten (z.B. IP-Adressen, Geräte-Informationen)</li>
            </ul>
            
            <h3>2.2 Kategorien betroffener Personen</h3>
            <ul>
                <li>Kunden (Unternehmen, die unsere Plattform nutzen)</li>
                <li>Empfehler (Teilnehmer an Empfehlungsprogrammen)</li>
                <li>Interessenten und Besucher der Website</li>
                <li>Kommunikationspartner</li>
            </ul>
            
            <h3>2.3 Zwecke der Verarbeitung</h3>
            <ul>
                <li>Bereitstellung unserer Plattform und deren Funktionen</li>
                <li>Verwaltung von Empfehlungsprogrammen</li>
                <li>Tracking von Empfehlungen und Conversions</li>
                <li>Versand von E-Mail-Benachrichtigungen</li>
                <li>Vertragserfüllung und Kundenservice</li>
                <li>Abrechnung und Zahlungsabwicklung</li>
                <li>Sicherheitsmaßnahmen und Betrugsprävention</li>
                <li>Marketing und Kommunikation</li>
            </ul>
            
            <h2>3. Rechtsgrundlagen</h2>
            <p>
                Die Verarbeitung personenbezogener Daten erfolgt auf Grundlage folgender Rechtsgrundlagen:
            </p>
            <ul>
                <li><strong>Vertragserfüllung (Art. 6 Abs. 1 lit. b DSGVO):</strong> 
                    Verarbeitung zur Erfüllung vertraglicher Pflichten</li>
                <li><strong>Einwilligung (Art. 6 Abs. 1 lit. a DSGVO):</strong> 
                    Verarbeitung auf Basis einer erteilten Einwilligung</li>
                <li><strong>Berechtigte Interessen (Art. 6 Abs. 1 lit. f DSGVO):</strong> 
                    Verarbeitung zur Wahrung berechtigter Interessen</li>
                <li><strong>Rechtliche Verpflichtung (Art. 6 Abs. 1 lit. c DSGVO):</strong> 
                    Verarbeitung zur Erfüllung rechtlicher Pflichten</li>
            </ul>
            
            <h2>4. Datenverarbeitung auf unserer Plattform</h2>
            
            <h3>4.1 Empfehlungsprogramm-Funktionen</h3>
            <p>
                Wenn Sie als Unternehmen ein Empfehlungsprogramm einrichten oder als Empfehler daran 
                teilnehmen, verarbeiten wir folgende Daten:
            </p>
            <p><strong>Für Unternehmen (Kunden):</strong></p>
            <ul>
                <li>Unternehmensdaten (Name, Branche, Adresse)</li>
                <li>Kontaktdaten (E-Mail, Telefon)</li>
                <li>Zugangsdaten (verschlüsseltes Passwort)</li>
                <li>Zahlungsdaten (über Digistore24)</li>
                <li>Nutzungsdaten der Plattform</li>
            </ul>
            <p><strong>Für Empfehler:</strong></p>
            <ul>
                <li>E-Mail-Adresse (für Double-Opt-In und Benachrichtigungen)</li>
                <li>Name (optional)</li>
                <li>Empfehlungsdaten (Klicks, Conversions, verdiente Belohnungen)</li>
                <li>Technische Daten (IP-Adresse gehasht, Browser-Fingerprint für Betrugsschutz)</li>
            </ul>
            <p>
                <strong>Rechtsgrundlage:</strong> Vertragserfüllung (Art. 6 Abs. 1 lit. b DSGVO) und 
                berechtigte Interessen (Art. 6 Abs. 1 lit. f DSGVO) für Sicherheitsmaßnahmen.
            </p>
            
            <h3>4.2 Double-Opt-In bei Empfehler-Registrierung</h3>
            <p>
                Empfehler müssen ihre E-Mail-Adresse durch Klick auf einen Bestätigungslink verifizieren 
                (Double-Opt-In). Dies stellt sicher, dass nur berechtigte Personen am Programm teilnehmen.
            </p>
            
            <h3>4.3 Tracking und Analyse</h3>
            <p>
                Für die Funktionalität des Empfehlungsprogramms tracken wir:
            </p>
            <ul>
                <li>Klicks auf Empfehlungslinks</li>
                <li>Conversions (erfolgreiche Empfehlungen)</li>
                <li>Share-Aktivitäten (welche Plattformen genutzt werden)</li>
            </ul>
            <p>
                <strong>Rechtsgrundlage:</strong> Vertragserfüllung (Art. 6 Abs. 1 lit. b DSGVO), da 
                das Tracking für die Funktion des Empfehlungsprogramms erforderlich ist.
            </p>
            
            <h3>4.4 Sicherheitsmaßnahmen und Betrugsprävention</h3>
            <p>
                Zum Schutz vor Missbrauch setzen wir folgende Sicherheitsmaßnahmen ein:
            </p>
            <ul>
                <li>Rate-Limiting (Begrenzung von Anfragen pro IP)</li>
                <li>Bot-Erkennung (Honeypot-Felder, Timing-Checks)</li>
                <li>Fraud-Detection (Erkennung verdächtiger Muster)</li>
                <li>Blockierung von Wegwerf-E-Mail-Adressen</li>
            </ul>
            <p>
                IP-Adressen werden dabei gehasht gespeichert und sind nicht direkt einer Person zuordenbar.
            </p>
            <p>
                <strong>Rechtsgrundlage:</strong> Berechtigte Interessen (Art. 6 Abs. 1 lit. f DSGVO) 
                an der Sicherheit und Integrität unserer Plattform.
            </p>
            
            <h2>5. E-Mail-Versand</h2>
            <p>
                Wir versenden E-Mails über den Dienst Mailgun (EU-Server). Folgende E-Mails werden versendet:
            </p>
            <ul>
                <li>Bestätigungs-E-Mails (Double-Opt-In)</li>
                <li>Belohnungs-Benachrichtigungen</li>
                <li>Erinnerungs-E-Mails (bei Inaktivität)</li>
                <li>Wöchentliche Zusammenfassungen (optional)</li>
            </ul>
            <p>
                <strong>Auftragsverarbeiter:</strong> Mailgun Technologies, Inc. (EU-Rechenzentrum)<br>
                <strong>Rechtsgrundlage:</strong> Vertragserfüllung (Art. 6 Abs. 1 lit. b DSGVO)
            </p>
            
            <h2>6. Zahlungsabwicklung</h2>
            <p>
                Die Zahlungsabwicklung erfolgt über Digistore24. Bei einem Kauf werden Sie zu 
                Digistore24 weitergeleitet, wo die Zahlungsdaten verarbeitet werden.
            </p>
            <p>
                <strong>Auftragsverarbeiter:</strong> Digistore24 GmbH, St.-Godehard-Straße 32, 31139 Hildesheim<br>
                <strong>Rechtsgrundlage:</strong> Vertragserfüllung (Art. 6 Abs. 1 lit. b DSGVO)
            </p>
            
            <h2>7. Cookies und lokale Speicherung</h2>
            <p>
                Wir verwenden ausschließlich technisch notwendige Cookies:
            </p>
            <ul>
                <li><strong>Session-Cookie:</strong> Für die Anmeldung (wird beim Schließen des Browsers gelöscht)</li>
                <li><strong>CSRF-Token:</strong> Zum Schutz vor Cross-Site-Request-Forgery</li>
            </ul>
            <p>
                Wir setzen keine Tracking-Cookies oder Marketing-Cookies ein.
            </p>
            
            <h2>8. Hosting</h2>
            <p>
                Unsere Website und Plattform werden bei Hostinger (EU-Server) gehostet. 
                Bei jedem Zugriff werden automatisch Server-Logfiles erstellt, die folgende Daten enthalten:
            </p>
            <ul>
                <li>Besuchte Seite</li>
                <li>Datum und Uhrzeit des Zugriffs</li>
                <li>IP-Adresse (gekürzt/anonymisiert)</li>
                <li>Browsertyp und -version</li>
                <li>Betriebssystem</li>
                <li>Referrer-URL</li>
            </ul>
            <p>
                Diese Daten werden nach 7 Tagen automatisch gelöscht und dienen ausschließlich der 
                Sicherheit und Fehleranalyse.
            </p>
            
            <h2>9. Speicherdauer</h2>
            <table class="w-full border-collapse border border-gray-300 my-4">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 p-2 text-left">Datenart</th>
                        <th class="border border-gray-300 p-2 text-left">Speicherdauer</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-gray-300 p-2">Kundendaten</td>
                        <td class="border border-gray-300 p-2">Bis Vertragsende + 10 Jahre (gesetzliche Aufbewahrungspflicht)</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 p-2">Empfehler-Daten</td>
                        <td class="border border-gray-300 p-2">Bis Löschung durch Kunde oder Widerruf</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 p-2">Tracking-Daten</td>
                        <td class="border border-gray-300 p-2">12 Monate</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 p-2">Server-Logfiles</td>
                        <td class="border border-gray-300 p-2">7 Tage</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 p-2">Sicherheits-Logs</td>
                        <td class="border border-gray-300 p-2">30 Tage</td>
                    </tr>
                </tbody>
            </table>
            
            <h2>10. Ihre Rechte</h2>
            <p>Sie haben folgende Rechte bezüglich Ihrer personenbezogenen Daten:</p>
            <ul>
                <li><strong>Auskunftsrecht (Art. 15 DSGVO):</strong> 
                    Sie können Auskunft über Ihre gespeicherten Daten verlangen.</li>
                <li><strong>Berichtigungsrecht (Art. 16 DSGVO):</strong> 
                    Sie können die Berichtigung unrichtiger Daten verlangen.</li>
                <li><strong>Löschungsrecht (Art. 17 DSGVO):</strong> 
                    Sie können die Löschung Ihrer Daten verlangen.</li>
                <li><strong>Einschränkung der Verarbeitung (Art. 18 DSGVO):</strong> 
                    Sie können die Einschränkung der Verarbeitung verlangen.</li>
                <li><strong>Datenübertragbarkeit (Art. 20 DSGVO):</strong> 
                    Sie können Ihre Daten in einem maschinenlesbaren Format erhalten.</li>
                <li><strong>Widerspruchsrecht (Art. 21 DSGVO):</strong> 
                    Sie können der Verarbeitung Ihrer Daten widersprechen.</li>
                <li><strong>Widerruf der Einwilligung (Art. 7 Abs. 3 DSGVO):</strong> 
                    Sie können erteilte Einwilligungen jederzeit widerrufen.</li>
                <li><strong>Beschwerderecht (Art. 77 DSGVO):</strong> 
                    Sie können sich bei einer Aufsichtsbehörde beschweren.</li>
            </ul>
            <p>
                Zur Ausübung Ihrer Rechte wenden Sie sich bitte an: 
                <a href="mailto:datenschutz@empfohlen.de" class="text-primary-500">datenschutz@empfohlen.de</a>
            </p>
            
            <h2>11. Datensicherheit</h2>
            <p>
                Wir setzen umfangreiche technische und organisatorische Maßnahmen zum Schutz Ihrer 
                Daten ein:
            </p>
            <ul>
                <li>SSL/TLS-Verschlüsselung aller Datenübertragungen</li>
                <li>Verschlüsselte Speicherung von Passwörtern (bcrypt)</li>
                <li>Regelmäßige Sicherheitsupdates</li>
                <li>Zugriffsbeschränkungen nach Need-to-know-Prinzip</li>
                <li>Regelmäßige Backups</li>
            </ul>
            
            <h2>12. Auftragsverarbeiter</h2>
            <p>Wir setzen folgende Auftragsverarbeiter ein:</p>
            <table class="w-full border-collapse border border-gray-300 my-4">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 p-2 text-left">Dienstleister</th>
                        <th class="border border-gray-300 p-2 text-left">Zweck</th>
                        <th class="border border-gray-300 p-2 text-left">Standort</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-gray-300 p-2">Hostinger</td>
                        <td class="border border-gray-300 p-2">Webhosting</td>
                        <td class="border border-gray-300 p-2">EU</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 p-2">Mailgun</td>
                        <td class="border border-gray-300 p-2">E-Mail-Versand</td>
                        <td class="border border-gray-300 p-2">EU</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 p-2">Digistore24</td>
                        <td class="border border-gray-300 p-2">Zahlungsabwicklung</td>
                        <td class="border border-gray-300 p-2">Deutschland</td>
                    </tr>
                </tbody>
            </table>
            
            <h2>13. Änderungen dieser Datenschutzerklärung</h2>
            <p>
                Wir behalten uns vor, diese Datenschutzerklärung anzupassen, um sie an geänderte 
                Rechtslagen oder bei Änderungen des Dienstes anzupassen. Die aktuelle Version ist 
                stets auf dieser Seite verfügbar.
            </p>
            
            <h2>14. Kontakt</h2>
            <p>
                Bei Fragen zum Datenschutz erreichen Sie uns unter:<br>
                <a href="mailto:datenschutz@empfohlen.de" class="text-primary-500">datenschutz@empfohlen.de</a>
            </p>
            
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/../templates/marketing/footer.php';
?>
