<?php
/**
 * Leadbusiness - Auftragsverarbeitungsvertrag (AVV)
 * 
 * Gemäß Art. 28 DSGVO
 */

$pageTitle = 'Auftragsverarbeitungsvertrag (AVV)';
$pageDescription = 'Auftragsverarbeitungsvertrag gemäß Art. 28 DSGVO für die Nutzung von Leadbusiness';

// Header
require_once __DIR__ . '/templates/marketing/header.php';

// PDF-Verfügbarkeit prüfen
$pdfExists = file_exists(__DIR__ . '/downloads/avv-leadbusiness.pdf');
?>

<main class="min-h-screen bg-gray-50">
    <!-- Header -->
    <section class="bg-gradient-to-br from-slate-900 to-slate-800 text-white py-16">
        <div class="container mx-auto px-4 max-w-4xl">
            <h1 class="text-3xl md:text-4xl font-bold mb-4">Auftragsverarbeitungsvertrag</h1>
            <p class="text-xl text-slate-300">gemäß Art. 28 DSGVO</p>
        </div>
    </section>

    <!-- Content -->
    <section class="py-12">
        <div class="container mx-auto px-4 max-w-4xl">
            <div class="bg-white rounded-2xl shadow-sm p-8 md:p-12 prose prose-slate max-w-none">
                
                <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-r-lg mb-8 not-prose">
                    <p class="text-blue-800">
                        <strong>Hinweis:</strong> Dieser AVV wird automatisch Bestandteil des Vertrags bei Abschluss eines Leadbusiness-Abonnements. 
                        Mit der Bestellung akzeptieren Sie die nachfolgenden Bedingungen zur Auftragsverarbeitung.
                    </p>
                </div>

                <h2>Präambel</h2>
                <p>
                    Der vorliegende Auftragsverarbeitungsvertrag (nachfolgend „AVV") regelt die Rechte und Pflichten 
                    der Parteien im Hinblick auf die Verarbeitung personenbezogener Daten im Rahmen der Nutzung der 
                    SaaS-Plattform „Leadbusiness" (empfehlungen.cloud).
                </p>

                <h2>§ 1 Vertragsparteien</h2>
                <h3>1.1 Auftraggeber (Verantwortlicher)</h3>
                <p>
                    Der Kunde, der die Dienste von Leadbusiness zur Durchführung eines Empfehlungsprogramms nutzt 
                    (nachfolgend „Auftraggeber" oder „Verantwortlicher").
                </p>
                
                <h3>1.2 Auftragnehmer (Auftragsverarbeiter)</h3>
                <p>
                    <strong>Leadbusiness / empfehlungen.cloud</strong><br>
                    Betrieben von: [Ihr Unternehmen]<br>
                    [Ihre Adresse]<br>
                    E-Mail: datenschutz@empfehlungen.cloud<br>
                    (nachfolgend „Auftragnehmer" oder „Auftragsverarbeiter")
                </p>

                <h2>§ 2 Gegenstand und Dauer der Verarbeitung</h2>
                <h3>2.1 Gegenstand</h3>
                <p>
                    Der Auftragnehmer verarbeitet im Auftrag des Auftraggebers personenbezogene Daten im Rahmen 
                    der Bereitstellung der Leadbusiness-Plattform für automatisierte Empfehlungsprogramme.
                </p>
                
                <h3>2.2 Dauer</h3>
                <p>
                    Die Verarbeitung beginnt mit Abschluss des Hauptvertrags (Bestellung eines Leadbusiness-Abonnements) 
                    und endet mit Beendigung des Hauptvertrags, zuzüglich gesetzlicher Aufbewahrungspflichten.
                </p>

                <h2>§ 3 Art und Zweck der Verarbeitung</h2>
                <h3>3.1 Art der Verarbeitung</h3>
                <p>Die Verarbeitung umfasst folgende Tätigkeiten:</p>
                <ul>
                    <li>Speicherung von Teilnehmerdaten (Leads) des Empfehlungsprogramms</li>
                    <li>Versand von E-Mail-Benachrichtigungen im Namen des Auftraggebers</li>
                    <li>Tracking von Empfehlungen und Conversions</li>
                    <li>Verwaltung von Belohnungen und Gamification-Elementen</li>
                    <li>Bereitstellung von Statistiken und Auswertungen</li>
                    <li>Betrugsprävention und Missbrauchserkennung</li>
                </ul>

                <h3>3.2 Zweck der Verarbeitung</h3>
                <p>
                    Die Verarbeitung erfolgt ausschließlich zum Zweck der Durchführung des Empfehlungsprogramms 
                    des Auftraggebers gemäß dem Hauptvertrag.
                </p>

                <h2>§ 4 Art der personenbezogenen Daten</h2>
                <p>Folgende Kategorien personenbezogener Daten werden verarbeitet:</p>
                <ul>
                    <li><strong>Kontaktdaten:</strong> E-Mail-Adresse, ggf. Name, ggf. Telefonnummer</li>
                    <li><strong>Empfehlungsdaten:</strong> Referral-Code, Anzahl Empfehlungen, erreichte Stufen, Belohnungen</li>
                    <li><strong>Technische Daten:</strong> IP-Adresse (anonymisiert), Browser-Fingerprint, Zeitstempel</li>
                    <li><strong>Kommunikationsdaten:</strong> E-Mail-Versandhistorie, Öffnungs-/Klickraten</li>
                    <li><strong>Zahlungsdaten:</strong> Werden NICHT durch den Auftragnehmer verarbeitet (Abwicklung über Digistore24)</li>
                </ul>

                <h2>§ 5 Kategorien betroffener Personen</h2>
                <ul>
                    <li>Teilnehmer des Empfehlungsprogramms des Auftraggebers (Leads/Empfehler)</li>
                    <li>Personen, die über einen Empfehlungslink auf die Seite des Auftraggebers gelangen</li>
                </ul>

                <h2>§ 6 Pflichten des Auftragnehmers</h2>
                <h3>6.1 Weisungsgebundenheit</h3>
                <p>
                    Der Auftragnehmer verarbeitet personenbezogene Daten ausschließlich auf dokumentierte Weisung 
                    des Auftraggebers, es sei denn, er ist durch Unionsrecht oder das Recht eines Mitgliedstaats 
                    zur Verarbeitung verpflichtet.
                </p>

                <h3>6.2 Vertraulichkeit</h3>
                <p>
                    Der Auftragnehmer gewährleistet, dass sich die zur Verarbeitung der personenbezogenen Daten 
                    befugten Personen zur Vertraulichkeit verpflichtet haben oder einer angemessenen gesetzlichen 
                    Verschwiegenheitspflicht unterliegen.
                </p>

                <h3>6.3 Technische und organisatorische Maßnahmen</h3>
                <p>Der Auftragnehmer ergreift alle gemäß Art. 32 DSGVO erforderlichen Maßnahmen, insbesondere:</p>
                <ul>
                    <li>Verschlüsselung aller Datenübertragungen (TLS/HTTPS)</li>
                    <li>Verschlüsselte Speicherung sensibler Daten</li>
                    <li>Regelmäßige Backups mit Verschlüsselung</li>
                    <li>Zugriffskontrolle und Authentifizierung</li>
                    <li>Protokollierung von Zugriffen</li>
                    <li>Regelmäßige Sicherheitsupdates</li>
                    <li>Getrennte Datenhaltung pro Kunde (Multi-Tenant-Isolation)</li>
                </ul>

                <h3>6.4 Unterauftragsverarbeiter</h3>
                <p>
                    Der Auftragnehmer setzt folgende Unterauftragsverarbeiter ein. Der Auftraggeber stimmt deren 
                    Einsatz mit Abschluss dieses AVV zu:
                </p>
                
                <div class="overflow-x-auto not-prose my-6">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 border-b">Unterauftragsverarbeiter</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 border-b">Tätigkeit</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 border-b">Serverstandort</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-700">Hostinger International Ltd.</td>
                                <td class="px-4 py-3 text-sm text-gray-700">Webhosting, Datenbank</td>
                                <td class="px-4 py-3 text-sm text-gray-700">EU (Niederlande/Litauen)</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-700">Mailgun Technologies, Inc.</td>
                                <td class="px-4 py-3 text-sm text-gray-700">E-Mail-Versand</td>
                                <td class="px-4 py-3 text-sm text-gray-700">EU (Frankfurt)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <p>
                    Der Auftragnehmer wird den Auftraggeber über jede beabsichtigte Änderung in Bezug auf die 
                    Hinzuziehung oder Ersetzung von Unterauftragsverarbeitern informieren. Der Auftraggeber 
                    kann Einspruch gegen die Änderung erheben.
                </p>

                <h3>6.5 Unterstützung des Auftraggebers</h3>
                <p>Der Auftragnehmer unterstützt den Auftraggeber:</p>
                <ul>
                    <li>Bei der Beantwortung von Anfragen betroffener Personen (Art. 15-22 DSGVO)</li>
                    <li>Bei der Einhaltung der Pflichten gemäß Art. 32-36 DSGVO</li>
                    <li>Bei Datenschutz-Folgenabschätzungen, sofern erforderlich</li>
                </ul>

                <h3>6.6 Meldung von Datenschutzverletzungen</h3>
                <p>
                    Der Auftragnehmer meldet dem Auftraggeber unverzüglich, spätestens jedoch innerhalb von 
                    24 Stunden nach Kenntniserlangung, jede Verletzung des Schutzes personenbezogener Daten.
                </p>

                <h3>6.7 Löschung und Rückgabe</h3>
                <p>
                    Nach Beendigung des Hauptvertrags löscht der Auftragnehmer alle personenbezogenen Daten, 
                    sofern nicht eine gesetzliche Pflicht zur Speicherung besteht. Auf Verlangen stellt der 
                    Auftragnehmer dem Auftraggeber zuvor einen Export der Daten zur Verfügung.
                </p>

                <h2>§ 7 Pflichten des Auftraggebers</h2>
                <p>Der Auftraggeber ist verpflichtet:</p>
                <ul>
                    <li>Die datenschutzrechtliche Zulässigkeit der Verarbeitung sicherzustellen</li>
                    <li>Betroffene Personen über die Verarbeitung zu informieren (Datenschutzerklärung)</li>
                    <li>Erforderliche Einwilligungen einzuholen (Double-Opt-In)</li>
                    <li>Den Auftragnehmer unverzüglich über erkannte Fehler oder Unregelmäßigkeiten zu informieren</li>
                </ul>

                <h2>§ 8 Kontrollrechte</h2>
                <p>
                    Der Auftraggeber ist berechtigt, die Einhaltung der technischen und organisatorischen 
                    Maßnahmen zu überprüfen. Der Auftragnehmer stellt dem Auftraggeber auf Anfrage alle 
                    erforderlichen Informationen zum Nachweis der Einhaltung der Pflichten zur Verfügung.
                </p>

                <h2>§ 9 Haftung</h2>
                <p>
                    Die Haftung richtet sich nach den gesetzlichen Bestimmungen der DSGVO sowie den 
                    Regelungen im Hauptvertrag.
                </p>

                <h2>§ 10 Schlussbestimmungen</h2>
                <h3>10.1 Änderungen</h3>
                <p>
                    Änderungen und Ergänzungen dieses AVV bedürfen der Schriftform. Dies gilt auch für 
                    die Abbedingung dieser Schriftformklausel.
                </p>

                <h3>10.2 Salvatorische Klausel</h3>
                <p>
                    Sollten einzelne Bestimmungen dieses AVV unwirksam sein oder werden, bleibt die 
                    Wirksamkeit der übrigen Bestimmungen unberührt.
                </p>

                <h3>10.3 Anwendbares Recht</h3>
                <p>
                    Es gilt deutsches Recht unter Ausschluss des UN-Kaufrechts.
                </p>

                <hr class="my-8">

                <h2>Anlage 1: Technische und organisatorische Maßnahmen (TOM)</h2>
                
                <h3>1. Zutrittskontrolle</h3>
                <p>
                    Die Daten werden auf Servern von Hostinger in EU-Rechenzentren gespeichert. 
                    Diese verfügen über 24/7-Überwachung, Zutrittskontrollsysteme und Alarmanlagen.
                </p>

                <h3>2. Zugangskontrolle</h3>
                <ul>
                    <li>Passwortgeschützter Zugang zu allen Systemen</li>
                    <li>Zwei-Faktor-Authentifizierung für Admin-Zugänge</li>
                    <li>Automatische Session-Timeouts</li>
                    <li>Protokollierung aller Anmeldeversuche</li>
                </ul>

                <h3>3. Zugriffskontrolle</h3>
                <ul>
                    <li>Rollenbasiertes Berechtigungskonzept</li>
                    <li>Strikte Mandantentrennung (Multi-Tenant-Isolation)</li>
                    <li>Minimalprinzip bei Zugriffsrechten</li>
                </ul>

                <h3>4. Weitergabekontrolle</h3>
                <ul>
                    <li>Verschlüsselte Datenübertragung (TLS 1.2/1.3)</li>
                    <li>Verschlüsselte E-Mail-Übertragung</li>
                    <li>Keine Datenweitergabe an Dritte ohne Weisung</li>
                </ul>

                <h3>5. Eingabekontrolle</h3>
                <ul>
                    <li>Protokollierung aller Dateneingaben und -änderungen</li>
                    <li>Audit-Logs für sensible Aktionen</li>
                </ul>

                <h3>6. Auftragskontrolle</h3>
                <ul>
                    <li>Schriftliche Weisungen erforderlich</li>
                    <li>Vertragliche Bindung der Unterauftragsverarbeiter</li>
                </ul>

                <h3>7. Verfügbarkeitskontrolle</h3>
                <ul>
                    <li>Tägliche automatische Backups</li>
                    <li>Redundante Datenspeicherung</li>
                    <li>USV und Notstromversorgung im Rechenzentrum</li>
                    <li>Monitoring und Alerting</li>
                </ul>

                <h3>8. Trennungskontrolle</h3>
                <ul>
                    <li>Logische Datentrennung pro Kunde</li>
                    <li>Separate Subdomains pro Kunde</li>
                    <li>Getrennte Datenbankbereiche</li>
                </ul>

                <p class="text-gray-500 text-sm mt-8">
                    Stand: <?= date('F Y') ?> | Version 1.0
                </p>
            </div>
            
            <!-- Download Button -->
            <div class="mt-8 text-center">
                <?php if ($pdfExists): ?>
                <a href="/avv-download.php" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    AVV als PDF herunterladen
                </a>
                <?php else: ?>
                <div class="inline-flex items-center gap-2 bg-gray-400 text-white px-6 py-3 rounded-lg font-semibold cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    PDF wird vorbereitet...
                </div>
                <p class="text-sm text-gray-500 mt-2">Der vollständige Text ist oben einsehbar. Das PDF wird in Kürze verfügbar sein.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/templates/marketing/footer.php'; ?>
