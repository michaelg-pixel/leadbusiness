<?php
/**
 * Leadbusiness - AVV PDF Generator
 * 
 * Generiert das AVV-PDF bei Bedarf und liefert es aus.
 * Das PDF wird gecached, um wiederholte Generierung zu vermeiden.
 */

// Fehlerbehandlung
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Cache-Verzeichnis
$cacheDir = __DIR__ . '/../cache';
$cacheFile = $cacheDir . '/avv-leadbusiness.pdf';

// Wenn Cache existiert und nicht älter als 30 Tage
if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 2592000)) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="avv-leadbusiness.pdf"');
    header('Content-Length: ' . filesize($cacheFile));
    header('Cache-Control: public, max-age=86400');
    readfile($cacheFile);
    exit;
}

// TCPDF laden (muss installiert sein)
$tcpdfPath = __DIR__ . '/../../vendor/tecnickcom/tcpdf/tcpdf.php';

// Prüfen ob TCPDF verfügbar
if (!file_exists($tcpdfPath)) {
    // Fallback: Statische Version
    $staticPdf = __DIR__ . '/avv-leadbusiness-static.pdf';
    if (file_exists($staticPdf)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="avv-leadbusiness.pdf"');
        header('Content-Length: ' . filesize($staticPdf));
        readfile($staticPdf);
        exit;
    }
    
    // Fehler: Weiterleitung zur HTML-Version
    header('Location: /avv');
    exit;
}

require_once $tcpdfPath;

// AVV Inhalte
$avvContent = [
    'title' => 'Auftragsverarbeitungsvertrag',
    'subtitle' => 'gemäß Art. 28 DSGVO',
    'version' => '1.0',
    'date' => date('F Y'),
    
    'sections' => [
        [
            'title' => 'Präambel',
            'content' => 'Der vorliegende Auftragsverarbeitungsvertrag (nachfolgend „AVV") regelt die Rechte und Pflichten der Parteien im Hinblick auf die Verarbeitung personenbezogener Daten im Rahmen der Nutzung der SaaS-Plattform „Leadbusiness" (empfehlungen.cloud).'
        ],
        [
            'title' => '§ 1 Vertragsparteien',
            'subsections' => [
                [
                    'title' => '1.1 Auftraggeber (Verantwortlicher)',
                    'content' => 'Der Kunde, der die Dienste von Leadbusiness zur Durchführung eines Empfehlungsprogramms nutzt (nachfolgend „Auftraggeber" oder „Verantwortlicher").'
                ],
                [
                    'title' => '1.2 Auftragnehmer (Auftragsverarbeiter)',
                    'content' => "Leadbusiness / empfehlungen.cloud\nBetrieben von: [Ihr Unternehmen]\n[Ihre Adresse]\nE-Mail: datenschutz@empfehlungen.cloud\n\n(nachfolgend „Auftragnehmer" oder „Auftragsverarbeiter")"
                ]
            ]
        ],
        [
            'title' => '§ 2 Gegenstand und Dauer der Verarbeitung',
            'subsections' => [
                [
                    'title' => '2.1 Gegenstand',
                    'content' => 'Der Auftragnehmer verarbeitet im Auftrag des Auftraggebers personenbezogene Daten im Rahmen der Bereitstellung der Leadbusiness-Plattform für automatisierte Empfehlungsprogramme.'
                ],
                [
                    'title' => '2.2 Dauer',
                    'content' => 'Die Verarbeitung beginnt mit Abschluss des Hauptvertrags (Bestellung eines Leadbusiness-Abonnements) und endet mit Beendigung des Hauptvertrags, zuzüglich gesetzlicher Aufbewahrungspflichten.'
                ]
            ]
        ],
        [
            'title' => '§ 3 Art und Zweck der Verarbeitung',
            'subsections' => [
                [
                    'title' => '3.1 Art der Verarbeitung',
                    'content' => 'Die Verarbeitung umfasst folgende Tätigkeiten:',
                    'list' => [
                        'Speicherung von Teilnehmerdaten (Leads) des Empfehlungsprogramms',
                        'Versand von E-Mail-Benachrichtigungen im Namen des Auftraggebers',
                        'Tracking von Empfehlungen und Conversions',
                        'Verwaltung von Belohnungen und Gamification-Elementen',
                        'Bereitstellung von Statistiken und Auswertungen',
                        'Betrugsprävention und Missbrauchserkennung'
                    ]
                ],
                [
                    'title' => '3.2 Zweck der Verarbeitung',
                    'content' => 'Die Verarbeitung erfolgt ausschließlich zum Zweck der Durchführung des Empfehlungsprogramms des Auftraggebers gemäß dem Hauptvertrag.'
                ]
            ]
        ],
        [
            'title' => '§ 4 Art der personenbezogenen Daten',
            'content' => 'Folgende Kategorien personenbezogener Daten werden verarbeitet:',
            'list' => [
                'Kontaktdaten: E-Mail-Adresse, ggf. Name, ggf. Telefonnummer',
                'Empfehlungsdaten: Referral-Code, Anzahl Empfehlungen, erreichte Stufen, Belohnungen',
                'Technische Daten: IP-Adresse (anonymisiert), Browser-Fingerprint, Zeitstempel',
                'Kommunikationsdaten: E-Mail-Versandhistorie, Öffnungs-/Klickraten',
                'Zahlungsdaten: Werden NICHT durch den Auftragnehmer verarbeitet (Abwicklung über Digistore24)'
            ]
        ],
        [
            'title' => '§ 5 Kategorien betroffener Personen',
            'list' => [
                'Teilnehmer des Empfehlungsprogramms des Auftraggebers (Leads/Empfehler)',
                'Personen, die über einen Empfehlungslink auf die Seite des Auftraggebers gelangen'
            ]
        ],
        [
            'title' => '§ 6 Pflichten des Auftragnehmers',
            'subsections' => [
                [
                    'title' => '6.1 Weisungsgebundenheit',
                    'content' => 'Der Auftragnehmer verarbeitet personenbezogene Daten ausschließlich auf dokumentierte Weisung des Auftraggebers, es sei denn, er ist durch Unionsrecht oder das Recht eines Mitgliedstaats zur Verarbeitung verpflichtet.'
                ],
                [
                    'title' => '6.2 Vertraulichkeit',
                    'content' => 'Der Auftragnehmer gewährleistet, dass sich die zur Verarbeitung der personenbezogenen Daten befugten Personen zur Vertraulichkeit verpflichtet haben oder einer angemessenen gesetzlichen Verschwiegenheitspflicht unterliegen.'
                ],
                [
                    'title' => '6.3 Technische und organisatorische Maßnahmen',
                    'content' => 'Der Auftragnehmer ergreift alle gemäß Art. 32 DSGVO erforderlichen Maßnahmen, insbesondere:',
                    'list' => [
                        'Verschlüsselung aller Datenübertragungen (TLS/HTTPS)',
                        'Verschlüsselte Speicherung sensibler Daten',
                        'Regelmäßige Backups mit Verschlüsselung',
                        'Zugriffskontrolle und Authentifizierung',
                        'Protokollierung von Zugriffen',
                        'Regelmäßige Sicherheitsupdates',
                        'Getrennte Datenhaltung pro Kunde (Multi-Tenant-Isolation)'
                    ]
                ],
                [
                    'title' => '6.4 Unterauftragsverarbeiter',
                    'content' => 'Der Auftragnehmer setzt folgende Unterauftragsverarbeiter ein:',
                    'table' => [
                        'headers' => ['Unterauftragsverarbeiter', 'Tätigkeit', 'Serverstandort'],
                        'rows' => [
                            ['Hostinger International Ltd.', 'Webhosting, Datenbank', 'EU (Niederlande/Litauen)'],
                            ['Mailgun Technologies, Inc.', 'E-Mail-Versand', 'EU (Frankfurt)']
                        ]
                    ]
                ],
                [
                    'title' => '6.5 Unterstützung des Auftraggebers',
                    'content' => 'Der Auftragnehmer unterstützt den Auftraggeber:',
                    'list' => [
                        'Bei der Beantwortung von Anfragen betroffener Personen (Art. 15-22 DSGVO)',
                        'Bei der Einhaltung der Pflichten gemäß Art. 32-36 DSGVO',
                        'Bei Datenschutz-Folgenabschätzungen, sofern erforderlich'
                    ]
                ],
                [
                    'title' => '6.6 Meldung von Datenschutzverletzungen',
                    'content' => 'Der Auftragnehmer meldet dem Auftraggeber unverzüglich, spätestens jedoch innerhalb von 24 Stunden nach Kenntniserlangung, jede Verletzung des Schutzes personenbezogener Daten.'
                ],
                [
                    'title' => '6.7 Löschung und Rückgabe',
                    'content' => 'Nach Beendigung des Hauptvertrags löscht der Auftragnehmer alle personenbezogenen Daten, sofern nicht eine gesetzliche Pflicht zur Speicherung besteht. Auf Verlangen stellt der Auftragnehmer dem Auftraggeber zuvor einen Export der Daten zur Verfügung.'
                ]
            ]
        ],
        [
            'title' => '§ 7 Pflichten des Auftraggebers',
            'content' => 'Der Auftraggeber ist verpflichtet:',
            'list' => [
                'Die datenschutzrechtliche Zulässigkeit der Verarbeitung sicherzustellen',
                'Betroffene Personen über die Verarbeitung zu informieren (Datenschutzerklärung)',
                'Erforderliche Einwilligungen einzuholen (Double-Opt-In)',
                'Den Auftragnehmer unverzüglich über erkannte Fehler oder Unregelmäßigkeiten zu informieren'
            ]
        ],
        [
            'title' => '§ 8 Kontrollrechte',
            'content' => 'Der Auftraggeber ist berechtigt, die Einhaltung der technischen und organisatorischen Maßnahmen zu überprüfen. Der Auftragnehmer stellt dem Auftraggeber auf Anfrage alle erforderlichen Informationen zum Nachweis der Einhaltung der Pflichten zur Verfügung.'
        ],
        [
            'title' => '§ 9 Haftung',
            'content' => 'Die Haftung richtet sich nach den gesetzlichen Bestimmungen der DSGVO sowie den Regelungen im Hauptvertrag.'
        ],
        [
            'title' => '§ 10 Schlussbestimmungen',
            'subsections' => [
                [
                    'title' => '10.1 Änderungen',
                    'content' => 'Änderungen und Ergänzungen dieses AVV bedürfen der Schriftform. Dies gilt auch für die Abbedingung dieser Schriftformklausel.'
                ],
                [
                    'title' => '10.2 Salvatorische Klausel',
                    'content' => 'Sollten einzelne Bestimmungen dieses AVV unwirksam sein oder werden, bleibt die Wirksamkeit der übrigen Bestimmungen unberührt.'
                ],
                [
                    'title' => '10.3 Anwendbares Recht',
                    'content' => 'Es gilt deutsches Recht unter Ausschluss des UN-Kaufrechts.'
                ]
            ]
        ]
    ],
    
    'tom' => [
        'title' => 'Anlage 1: Technische und organisatorische Maßnahmen (TOM)',
        'sections' => [
            [
                'title' => '1. Zutrittskontrolle',
                'content' => 'Die Daten werden auf Servern von Hostinger in EU-Rechenzentren gespeichert. Diese verfügen über 24/7-Überwachung, Zutrittskontrollsysteme und Alarmanlagen.'
            ],
            [
                'title' => '2. Zugangskontrolle',
                'list' => [
                    'Passwortgeschützter Zugang zu allen Systemen',
                    'Zwei-Faktor-Authentifizierung für Admin-Zugänge',
                    'Automatische Session-Timeouts',
                    'Protokollierung aller Anmeldeversuche'
                ]
            ],
            [
                'title' => '3. Zugriffskontrolle',
                'list' => [
                    'Rollenbasiertes Berechtigungskonzept',
                    'Strikte Mandantentrennung (Multi-Tenant-Isolation)',
                    'Minimalprinzip bei Zugriffsrechten'
                ]
            ],
            [
                'title' => '4. Weitergabekontrolle',
                'list' => [
                    'Verschlüsselte Datenübertragung (TLS 1.2/1.3)',
                    'Verschlüsselte E-Mail-Übertragung',
                    'Keine Datenweitergabe an Dritte ohne Weisung'
                ]
            ],
            [
                'title' => '5. Eingabekontrolle',
                'list' => [
                    'Protokollierung aller Dateneingaben und -änderungen',
                    'Audit-Logs für sensible Aktionen'
                ]
            ],
            [
                'title' => '6. Auftragskontrolle',
                'list' => [
                    'Schriftliche Weisungen erforderlich',
                    'Vertragliche Bindung der Unterauftragsverarbeiter'
                ]
            ],
            [
                'title' => '7. Verfügbarkeitskontrolle',
                'list' => [
                    'Tägliche automatische Backups',
                    'Redundante Datenspeicherung',
                    'USV und Notstromversorgung im Rechenzentrum',
                    'Monitoring und Alerting'
                ]
            ],
            [
                'title' => '8. Trennungskontrolle',
                'list' => [
                    'Logische Datentrennung pro Kunde',
                    'Separate Subdomains pro Kunde',
                    'Getrennte Datenbankbereiche'
                ]
            ]
        ]
    ]
];

// Hinweis: Dieses Script wird das PDF bei TCPDF-Verfügbarkeit generieren
// Für den Moment leiten wir zur HTML-Version um
header('Location: /avv');
exit;
