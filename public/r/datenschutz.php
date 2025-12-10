<?php
/**
 * Leadbusiness - Dynamische Datenschutzseite für Empfehlungsseiten
 * 
 * Option C: Kombinierte Datenschutzseite
 * - Enthält Kunden-spezifische Daten (Verantwortlicher)
 * - Verweist auf Leadbusiness als Auftragsverarbeiter
 * - Link zur vollständigen Datenschutzerklärung des Kunden (falls vorhanden)
 */

require_once __DIR__ . '/../../includes/Database.php';

// Subdomain ermitteln
$host = $_SERVER['HTTP_HOST'] ?? '';
$subdomain = explode('.', $host)[0];

// Kunde laden
$db = Database::getInstance();
$customer = $db->fetch(
    "SELECT c.*, bi.filename as bg_filename 
     FROM customers c 
     LEFT JOIN background_images bi ON c.background_image_id = bi.id 
     WHERE c.subdomain = ? AND c.subscription_status IN ('active', 'trial')",
    [$subdomain]
);

if (!$customer) {
    header('Location: https://empfehlungen.cloud');
    exit;
}

// Kunden-eigene Datenschutz-URL (falls vorhanden)
$customerPrivacyUrl = $customer['whitelabel_custom_privacy_url'] ?? null;
$primaryColor = $customer['primary_color'] ?? '#667eea';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datenschutzerklärung - <?= htmlspecialchars($customer['company_name']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: <?= htmlspecialchars($primaryColor) ?>;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            line-height: 1.7;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo {
            max-height: 60px;
            max-width: 200px;
            margin-bottom: 20px;
        }
        
        h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #64748b;
            font-size: 1rem;
        }
        
        .content {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #0f172a;
            margin: 30px 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        h2:first-child {
            margin-top: 0;
        }
        
        h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #334155;
            margin: 20px 0 10px 0;
        }
        
        p {
            margin-bottom: 15px;
            color: #475569;
        }
        
        ul {
            margin: 15px 0;
            padding-left: 25px;
        }
        
        li {
            margin-bottom: 8px;
            color: #475569;
        }
        
        .info-box {
            background: #f1f5f9;
            border-left: 4px solid var(--primary-color);
            padding: 20px;
            border-radius: 0 8px 8px 0;
            margin: 20px 0;
        }
        
        .info-box strong {
            display: block;
            margin-bottom: 10px;
            color: #0f172a;
        }
        
        a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
        }
        
        .external-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: var(--primary-color);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            margin-top: 10px;
        }
        
        .external-link:hover {
            opacity: 0.9;
            text-decoration: none;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e2e8f0;
            color: #94a3b8;
            font-size: 0.875rem;
        }
        
        .footer a {
            color: #64748b;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        
        @media (max-width: 640px) {
            .container {
                padding: 20px 15px;
            }
            
            .content {
                padding: 25px 20px;
            }
            
            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="back-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Zurück zur Empfehlungsseite
        </a>
        
        <div class="header">
            <?php if ($customer['logo_url']): ?>
                <img src="<?= htmlspecialchars($customer['logo_url']) ?>" alt="<?= htmlspecialchars($customer['company_name']) ?>" class="logo">
            <?php endif; ?>
            <h1>Datenschutzerklärung</h1>
            <p class="subtitle">für das Empfehlungsprogramm von <?= htmlspecialchars($customer['company_name']) ?></p>
        </div>
        
        <div class="content">
            <h2>1. Verantwortlicher</h2>
            <div class="info-box">
                <strong><?= htmlspecialchars($customer['company_name']) ?></strong>
                <?= htmlspecialchars($customer['address_street']) ?><br>
                <?= htmlspecialchars($customer['address_zip']) ?> <?= htmlspecialchars($customer['address_city']) ?><br>
                <?php if ($customer['phone']): ?>
                    Tel: <?= htmlspecialchars($customer['phone']) ?><br>
                <?php endif; ?>
                E-Mail: <?= htmlspecialchars($customer['email']) ?>
                <?php if ($customer['tax_id']): ?>
                    <br>USt-IdNr.: <?= htmlspecialchars($customer['tax_id']) ?>
                <?php endif; ?>
            </div>
            
            <?php if ($customerPrivacyUrl): ?>
            <p>
                Die vollständige Datenschutzerklärung von <?= htmlspecialchars($customer['company_name']) ?> finden Sie hier:
                <br>
                <a href="<?= htmlspecialchars($customerPrivacyUrl) ?>" target="_blank" rel="noopener" class="external-link">
                    Vollständige Datenschutzerklärung
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14L21 3"/>
                    </svg>
                </a>
            </p>
            <?php endif; ?>
            
            <h2>2. Auftragsverarbeiter</h2>
            <p>
                Für den technischen Betrieb des Empfehlungsprogramms setzen wir folgenden Dienstleister als Auftragsverarbeiter gemäß Art. 28 DSGVO ein:
            </p>
            <div class="info-box">
                <strong>Leadbusiness / empfehlungen.cloud</strong>
                Betrieben von: [Ihr Unternehmen]<br>
                [Ihre Adresse]<br>
                E-Mail: datenschutz@empfehlungen.cloud
            </div>
            <p>
                Mit diesem Auftragsverarbeiter wurde ein Auftragsverarbeitungsvertrag (AVV) gemäß Art. 28 DSGVO geschlossen.
                <a href="https://empfehlungen.cloud/avv" target="_blank">AVV einsehen</a>
            </p>
            
            <h2>3. Welche Daten werden verarbeitet?</h2>
            <p>Im Rahmen des Empfehlungsprogramms werden folgende personenbezogene Daten verarbeitet:</p>
            <ul>
                <li><strong>Anmeldedaten:</strong> E-Mail-Adresse, ggf. Name</li>
                <li><strong>Empfehlungsdaten:</strong> Anzahl geteilter Links, Anzahl erfolgreicher Empfehlungen, erreichte Belohnungsstufen</li>
                <li><strong>Technische Daten:</strong> IP-Adresse (anonymisiert gespeichert), Browser-Informationen, Zeitstempel der Aktivitäten</li>
                <li><strong>Kommunikationsdaten:</strong> E-Mail-Versandhistorie, Öffnungs- und Klickraten</li>
            </ul>
            
            <h2>4. Zweck der Datenverarbeitung</h2>
            <p>Die Daten werden ausschließlich für folgende Zwecke verarbeitet:</p>
            <ul>
                <li>Verwaltung Ihrer Teilnahme am Empfehlungsprogramm</li>
                <li>Nachverfolgung Ihrer Empfehlungen und Zuweisung von Belohnungen</li>
                <li>Versand von E-Mail-Benachrichtigungen zu Ihrem Fortschritt und Ihren Belohnungen</li>
                <li>Betrugsprävention und Missbrauchserkennung</li>
                <li>Statistische Auswertung (anonymisiert)</li>
            </ul>
            
            <h2>5. Rechtsgrundlage</h2>
            <p>Die Verarbeitung erfolgt auf Grundlage von:</p>
            <ul>
                <li><strong>Art. 6 Abs. 1 lit. a DSGVO (Einwilligung):</strong> Sie haben bei der Anmeldung zum Empfehlungsprogramm Ihre Einwilligung zur Datenverarbeitung erteilt.</li>
                <li><strong>Art. 6 Abs. 1 lit. b DSGVO (Vertragserfüllung):</strong> Die Verarbeitung ist erforderlich, um die Teilnahme am Empfehlungsprogramm zu ermöglichen und Belohnungen zuzuweisen.</li>
                <li><strong>Art. 6 Abs. 1 lit. f DSGVO (berechtigtes Interesse):</strong> Zur Betrugsprävention und zum Schutz vor Missbrauch.</li>
            </ul>
            
            <h2>6. E-Mail-Versand</h2>
            <p>
                Für den Versand von E-Mails nutzen wir den Dienst <strong>Mailgun</strong> (Mailgun Technologies, Inc.). 
                Die Server befinden sich in der EU (Frankfurt). Ihre E-Mail-Adresse wird ausschließlich für den Versand 
                von Benachrichtigungen im Rahmen des Empfehlungsprogramms verwendet.
            </p>
            <h3>Sie erhalten folgende E-Mails:</h3>
            <ul>
                <li>Bestätigung Ihrer Anmeldung (Double-Opt-In)</li>
                <li>Benachrichtigungen über erfolgreiche Empfehlungen</li>
                <li>Information über erreichte Belohnungen</li>
                <li>Erinnerungen und Tipps (optional)</li>
            </ul>
            <p>
                <strong>Abmeldung:</strong> Sie können sich jederzeit von E-Mail-Benachrichtigungen abmelden, indem Sie den 
                Abmeldelink am Ende jeder E-Mail nutzen oder uns direkt kontaktieren.
            </p>
            
            <h2>7. Cookies und Tracking</h2>
            <p>
                Das Empfehlungsprogramm verwendet technisch notwendige Cookies, um Ihre Teilnahme zu verwalten und 
                Empfehlungen korrekt zuzuordnen. Diese Cookies sind für den Betrieb zwingend erforderlich.
            </p>
            <ul>
                <li><strong>Session-Cookie:</strong> Für Ihre Anmeldung (wird nach Sitzungsende gelöscht)</li>
                <li><strong>Referral-Cookie:</strong> Zur Zuordnung von Empfehlungen (30 Tage gültig)</li>
            </ul>
            
            <h2>8. Speicherdauer</h2>
            <p>Ihre Daten werden wie folgt gespeichert:</p>
            <ul>
                <li><strong>Aktive Teilnahme:</strong> Solange Sie am Empfehlungsprogramm teilnehmen</li>
                <li><strong>Nach Beendigung:</strong> 3 Jahre (gesetzliche Aufbewahrungspflichten)</li>
                <li><strong>Anonymisierte Statistiken:</strong> Unbegrenzt</li>
            </ul>
            
            <h2>9. Ihre Rechte</h2>
            <p>Sie haben folgende Rechte bezüglich Ihrer personenbezogenen Daten:</p>
            <ul>
                <li><strong>Auskunft (Art. 15 DSGVO):</strong> Sie können Auskunft über die zu Ihrer Person gespeicherten Daten verlangen.</li>
                <li><strong>Berichtigung (Art. 16 DSGVO):</strong> Sie können die Korrektur unrichtiger Daten verlangen.</li>
                <li><strong>Löschung (Art. 17 DSGVO):</strong> Sie können die Löschung Ihrer Daten verlangen, sofern keine gesetzlichen Aufbewahrungspflichten entgegenstehen.</li>
                <li><strong>Einschränkung (Art. 18 DSGVO):</strong> Sie können die Einschränkung der Verarbeitung verlangen.</li>
                <li><strong>Datenübertragbarkeit (Art. 20 DSGVO):</strong> Sie können Ihre Daten in einem gängigen Format erhalten.</li>
                <li><strong>Widerspruch (Art. 21 DSGVO):</strong> Sie können der Verarbeitung jederzeit widersprechen.</li>
                <li><strong>Widerruf der Einwilligung (Art. 7 Abs. 3 DSGVO):</strong> Sie können Ihre Einwilligung jederzeit widerrufen.</li>
            </ul>
            <p>
                Zur Ausübung Ihrer Rechte wenden Sie sich bitte an den oben genannten Verantwortlichen oder an 
                <a href="mailto:datenschutz@empfehlungen.cloud">datenschutz@empfehlungen.cloud</a>.
            </p>
            
            <h2>10. Beschwerderecht</h2>
            <p>
                Sie haben das Recht, sich bei einer Datenschutz-Aufsichtsbehörde zu beschweren, wenn Sie der Ansicht sind, 
                dass die Verarbeitung Ihrer Daten gegen die DSGVO verstößt.
            </p>
            
            <h2>11. Datensicherheit</h2>
            <p>
                Wir setzen technische und organisatorische Sicherheitsmaßnahmen ein, um Ihre Daten gegen zufällige oder 
                vorsätzliche Manipulation, Verlust, Zerstörung oder den Zugriff unberechtigter Personen zu schützen. 
                Die Datenübertragung erfolgt ausschließlich verschlüsselt über HTTPS/TLS.
            </p>
            
            <p style="margin-top: 30px; color: #94a3b8; font-size: 0.9rem;">
                Stand: <?= date('F Y') ?>
            </p>
        </div>
        
        <div class="footer">
            <p>
                Diese Datenschutzerklärung gilt für das Empfehlungsprogramm von <?= htmlspecialchars($customer['company_name']) ?>.<br>
                Technisch betrieben von <a href="https://empfehlungen.cloud" target="_blank">Leadbusiness</a>
            </p>
        </div>
    </div>
</body>
</html>
