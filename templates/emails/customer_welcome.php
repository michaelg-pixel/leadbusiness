<?php
/**
 * Leadbusiness - Willkommens-E-Mail für Kunden
 * 
 * Wird versendet wenn ein Kunde das Onboarding abgeschlossen hat.
 * 
 * Verfügbare Variablen:
 * - $contact_name: Name des Ansprechpartners
 * - $company_name: Firmenname des Kunden
 * - $subdomain: Subdomain des Empfehlungsprogramms
 * - $referral_page_url: URL zur Empfehlungsseite
 * - $dashboard_url: URL zum Kunden-Dashboard
 * - $plan: Tarif (starter, professional)
 * - $trial_days: Tage bis Testende
 * - $primary_color: Hauptfarbe
 */

// Defaults
$primary_color = $primary_color ?? '#667eea';
$contact_name = $contact_name ?? 'Kunde';
$subdomain = $subdomain ?? 'ihre-firma';
$referral_page_url = $referral_page_url ?? 'https://' . $subdomain . '.empfehlungen.cloud';
$dashboard_url = $dashboard_url ?? 'https://empfehlungen.cloud/dashboard';
$plan = $plan ?? 'starter';
$trial_days = $trial_days ?? 7;

// E-Mail Konfiguration
$subject = '🚀 Ihr Empfehlungsprogramm ist bereit!';
$preheader = 'Alles eingerichtet – starten Sie jetzt mit ' . $company_name . ' durch!';

// Plan-spezifische Features
$planFeatures = [
    'starter' => [
        'Bis zu 200 Empfehler',
        '3 Belohnungsstufen',
        'E-Mail-Benachrichtigungen',
        'Live-Counter & Leaderboard',
        'Share-Grafiken & QR-Code'
    ],
    'professional' => [
        'Bis zu 5.000 Empfehler',
        'Bis zu 10 Belohnungsstufen',
        'Erweiterte Belohnungstypen',
        'Lead-Export',
        'Mehrere Kampagnen',
        'Eigenes Branding',
        'API & Webhooks',
        'Prioritäts-Support'
    ]
];

$features = $planFeatures[$plan] ?? $planFeatures['starter'];

// Content
ob_start();
?>

<!-- Willkommens-Header -->
<div style="text-align: center; margin-bottom: 30px;">
    <div style="font-size: 64px; line-height: 1; margin-bottom: 15px;">
        🚀
    </div>
    <h1 style="margin: 0 0 10px 0; color: #1e293b; font-size: 28px; font-weight: 700;">
        Herzlichen Glückwunsch, <?= htmlspecialchars($contact_name) ?>!
    </h1>
    <p style="margin: 0; color: #64748b; font-size: 16px;">
        Ihr Empfehlungsprogramm für <strong><?= htmlspecialchars($company_name) ?></strong> ist jetzt live!
    </p>
</div>

<!-- Erfolgs-Box -->
<div style="background: linear-gradient(135deg, #10b98115 0%, #10b98105 100%); border: 2px solid #10b98140; border-radius: 12px; padding: 25px; margin-bottom: 30px; text-align: center;">
    <div style="font-size: 48px; margin-bottom: 10px;">✅</div>
    <h2 style="margin: 0 0 10px 0; color: #047857; font-size: 20px; font-weight: 700;">
        Einrichtung abgeschlossen!
    </h2>
    <p style="margin: 0; color: #059669; font-size: 15px;">
        Ihre Empfehlungsseite ist unter folgender Adresse erreichbar:
    </p>
    <div style="background-color: #ffffff; border-radius: 8px; padding: 15px; margin-top: 15px;">
        <a href="<?= htmlspecialchars($referral_page_url) ?>" style="color: <?= htmlspecialchars($primary_color) ?>; font-size: 18px; font-weight: 700; text-decoration: none;">
            <?= htmlspecialchars($referral_page_url) ?>
        </a>
    </div>
</div>

<!-- Nächste Schritte -->
<div style="background-color: #f8fafc; border-radius: 12px; padding: 25px; margin-bottom: 30px;">
    <h2 style="margin: 0 0 20px 0; color: #1e293b; font-size: 18px; font-weight: 700;">
        📋 Ihre nächsten Schritte:
    </h2>
    
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td style="width: 40px; vertical-align: top;">
                            <div style="width: 28px; height: 28px; background: <?= htmlspecialchars($primary_color) ?>; border-radius: 50%; color: white; text-align: center; line-height: 28px; font-weight: 700; font-size: 13px;">1</div>
                        </td>
                        <td style="padding-left: 12px;">
                            <p style="margin: 0; color: #1e293b; font-size: 15px; font-weight: 600;">Empfehlungsseite testen</p>
                            <p style="margin: 5px 0 0 0; color: #64748b; font-size: 14px;">Rufen Sie Ihre Seite auf und testen Sie die Anmeldung</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td style="width: 40px; vertical-align: top;">
                            <div style="width: 28px; height: 28px; background: <?= htmlspecialchars($primary_color) ?>; border-radius: 50%; color: white; text-align: center; line-height: 28px; font-weight: 700; font-size: 13px;">2</div>
                        </td>
                        <td style="padding-left: 12px;">
                            <p style="margin: 0; color: #1e293b; font-size: 15px; font-weight: 600;">Kunden informieren</p>
                            <p style="margin: 5px 0 0 0; color: #64748b; font-size: 14px;">Teilen Sie den Link per E-Mail, Social Media oder vor Ort</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td style="width: 40px; vertical-align: top;">
                            <div style="width: 28px; height: 28px; background: <?= htmlspecialchars($primary_color) ?>; border-radius: 50%; color: white; text-align: center; line-height: 28px; font-weight: 700; font-size: 13px;">3</div>
                        </td>
                        <td style="padding-left: 12px;">
                            <p style="margin: 0; color: #1e293b; font-size: 15px; font-weight: 600;">QR-Code ausdrucken</p>
                            <p style="margin: 5px 0 0 0; color: #64748b; font-size: 14px;">Laden Sie den QR-Code herunter und hängen Sie ihn aus</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 12px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td style="width: 40px; vertical-align: top;">
                            <div style="width: 28px; height: 28px; background: <?= htmlspecialchars($primary_color) ?>; border-radius: 50%; color: white; text-align: center; line-height: 28px; font-weight: 700; font-size: 13px;">4</div>
                        </td>
                        <td style="padding-left: 12px;">
                            <p style="margin: 0; color: #1e293b; font-size: 15px; font-weight: 600;">Statistiken verfolgen</p>
                            <p style="margin: 5px 0 0 0; color: #64748b; font-size: 14px;">Beobachten Sie im Dashboard, wie Ihr Programm wächst</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<!-- Dashboard Button -->
<div style="text-align: center; margin-bottom: 30px;">
    <a href="<?= htmlspecialchars($dashboard_url) ?>" style="display: inline-block; background: <?= htmlspecialchars($primary_color) ?>; color: #ffffff; text-decoration: none; padding: 16px 40px; border-radius: 8px; font-weight: 600; font-size: 16px;">
        📊 Zum Dashboard
    </a>
</div>

<!-- Tarif-Info -->
<div style="background-color: #fef3c7; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
    <p style="margin: 0; color: #92400e; font-size: 14px;">
        <strong>⏰ Ihr Testzeitraum:</strong> Sie haben noch <strong><?= intval($trial_days) ?> Tage</strong> kostenlos zum Ausprobieren. 
        Danach läuft Ihr <strong><?= ucfirst($plan) ?>-Tarif</strong> automatisch weiter.
    </p>
</div>

<!-- Features -->
<div style="margin-bottom: 30px;">
    <h3 style="margin: 0 0 15px 0; color: #1e293b; font-size: 16px; font-weight: 600;">
        ✨ Ihre <?= ucfirst($plan) ?>-Features:
    </h3>
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <?php foreach ($features as $feature): ?>
        <tr>
            <td style="padding: 6px 0;">
                <span style="color: #10b981; margin-right: 8px;">✓</span>
                <span style="color: #475569; font-size: 14px;"><?= htmlspecialchars($feature) ?></span>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<!-- Support-Info -->
<div style="background-color: #f0f9ff; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
    <p style="margin: 0 0 10px 0; color: #0369a1; font-size: 15px; font-weight: 600;">
        💬 Fragen? Wir helfen gerne!
    </p>
    <p style="margin: 0; color: #0284c7; font-size: 14px;">
        Antworten Sie einfach auf diese E-Mail oder besuchen Sie unsere 
        <a href="https://empfehlungen.cloud/faq" style="color: #0369a1; text-decoration: underline;">FAQ</a>.
    </p>
</div>

<!-- Abschluss -->
<div style="text-align: center; margin-top: 30px; padding-top: 25px; border-top: 1px solid #e2e8f0;">
    <p style="margin: 0; color: #475569; font-size: 15px; line-height: 1.6;">
        Wir wünschen Ihnen viel Erfolg mit Ihrem Empfehlungsprogramm! 🎉
    </p>
    <p style="margin: 10px 0 0 0; color: #64748b; font-size: 14px;">
        Ihr Team von Leadbusiness
    </p>
</div>

<?php
$content = ob_get_clean();

// Für Kunden-E-Mails: Leadbusiness als Absender
$company_name = 'Leadbusiness';
$footer_address = 'Leadbusiness • empfehlungen.cloud';

// Base-Template einbinden
include __DIR__ . '/_base.php';
