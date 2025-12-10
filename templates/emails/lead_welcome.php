<?php
/**
 * Leadbusiness - Willkommens-E-Mail für Empfehler
 * 
 * Wird versendet wenn sich ein neuer Empfehler registriert.
 * 
 * Verfügbare Variablen:
 * - $lead_name: Name des Empfehlers
 * - $lead_email: E-Mail des Empfehlers
 * - $referral_link: Persönlicher Empfehlungslink
 * - $company_name: Name des Unternehmens
 * - $company_logo: URL zum Logo (optional)
 * - $primary_color: Hauptfarbe (Hex)
 * - $rewards: Array mit Belohnungsstufen
 * - $dashboard_url: Link zum Empfehler-Dashboard
 * - $share_url: Direkter Share-Link
 * - $footer_address: Impressums-Adresse
 * - $unsubscribe_url: Abmelde-Link
 */

// Defaults
$primary_color = $primary_color ?? '#667eea';
$lead_name = $lead_name ?? 'Empfehler';
$rewards = $rewards ?? [];

// E-Mail Konfiguration
$subject = '🎉 Willkommen im Empfehlungsprogramm von ' . $company_name;
$preheader = 'Starten Sie jetzt und sichern Sie sich attraktive Belohnungen für Ihre Empfehlungen!';

// Content
ob_start();
?>

<!-- Willkommens-Header -->
<div style="text-align: center; margin-bottom: 30px;">
    <div style="font-size: 64px; line-height: 1; margin-bottom: 15px;">
        🎉
    </div>
    <h1 style="margin: 0 0 10px 0; color: #1e293b; font-size: 28px; font-weight: 700;">
        Willkommen, <?= htmlspecialchars($lead_name) ?>!
    </h1>
    <p style="margin: 0; color: #64748b; font-size: 16px;">
        Sie sind jetzt Teil des Empfehlungsprogramms von <?= htmlspecialchars($company_name) ?>
    </p>
</div>

<!-- So funktioniert's -->
<div style="background-color: #f8fafc; border-radius: 12px; padding: 25px; margin-bottom: 30px;">
    <h2 style="margin: 0 0 20px 0; color: #1e293b; font-size: 18px; font-weight: 700;">
        📋 So einfach geht's:
    </h2>
    
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="padding: 10px 0; vertical-align: top;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td style="width: 40px; vertical-align: top;">
                            <div style="width: 32px; height: 32px; background: <?= htmlspecialchars($primary_color) ?>; border-radius: 50%; color: white; text-align: center; line-height: 32px; font-weight: 700; font-size: 14px;">1</div>
                        </td>
                        <td style="padding-left: 10px;">
                            <p style="margin: 0; color: #1e293b; font-size: 15px; font-weight: 600;">Teilen Sie Ihren Link</p>
                            <p style="margin: 5px 0 0 0; color: #64748b; font-size: 14px;">Per WhatsApp, E-Mail, Social Media oder persönlich</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 0; vertical-align: top;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td style="width: 40px; vertical-align: top;">
                            <div style="width: 32px; height: 32px; background: <?= htmlspecialchars($primary_color) ?>; border-radius: 50%; color: white; text-align: center; line-height: 32px; font-weight: 700; font-size: 14px;">2</div>
                        </td>
                        <td style="padding-left: 10px;">
                            <p style="margin: 0; color: #1e293b; font-size: 15px; font-weight: 600;">Freunde melden sich an</p>
                            <p style="margin: 5px 0 0 0; color: #64748b; font-size: 14px;">Jede erfolgreiche Empfehlung wird gezählt</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 0; vertical-align: top;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td style="width: 40px; vertical-align: top;">
                            <div style="width: 32px; height: 32px; background: <?= htmlspecialchars($primary_color) ?>; border-radius: 50%; color: white; text-align: center; line-height: 32px; font-weight: 700; font-size: 14px;">3</div>
                        </td>
                        <td style="padding-left: 10px;">
                            <p style="margin: 0; color: #1e293b; font-size: 15px; font-weight: 600;">Belohnungen kassieren</p>
                            <p style="margin: 5px 0 0 0; color: #64748b; font-size: 14px;">Erreichen Sie Stufen und erhalten Sie tolle Prämien!</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<!-- Persönlicher Empfehlungslink -->
<div style="background: linear-gradient(135deg, <?= htmlspecialchars($primary_color) ?>15 0%, <?= htmlspecialchars($primary_color) ?>05 100%); border: 2px solid <?= htmlspecialchars($primary_color) ?>40; border-radius: 12px; padding: 25px; margin-bottom: 30px; text-align: center;">
    <p style="margin: 0 0 10px 0; color: <?= htmlspecialchars($primary_color) ?>; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
        🔗 Ihr persönlicher Empfehlungslink
    </p>
    <div style="background-color: #ffffff; border-radius: 8px; padding: 15px; margin-bottom: 15px;">
        <a href="<?= htmlspecialchars($referral_link) ?>" style="color: <?= htmlspecialchars($primary_color) ?>; font-size: 16px; font-weight: 600; text-decoration: none; word-break: break-all;">
            <?= htmlspecialchars($referral_link) ?>
        </a>
    </div>
    <a href="<?= htmlspecialchars($share_url ?? $referral_link) ?>" style="display: inline-block; background: <?= htmlspecialchars($primary_color) ?>; color: #ffffff; text-decoration: none; padding: 14px 35px; border-radius: 8px; font-weight: 600; font-size: 16px;">
        🚀 Jetzt teilen
    </a>
</div>

<!-- Belohnungsstufen -->
<?php if (!empty($rewards)): ?>
<div style="margin-bottom: 30px;">
    <h2 style="margin: 0 0 20px 0; color: #1e293b; font-size: 18px; font-weight: 700; text-align: center;">
        🎁 Diese Belohnungen warten auf Sie:
    </h2>
    
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <?php foreach ($rewards as $index => $reward): 
            $icons = ['🥉', '🥈', '🥇', '💎', '👑'];
            $icon = $icons[$index] ?? '🏆';
        ?>
        <tr>
            <td style="padding: 10px 0;">
                <div style="background-color: #f8fafc; border-radius: 8px; padding: 15px; display: flex; align-items: center;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <td style="width: 50px; text-align: center; vertical-align: middle;">
                                <span style="font-size: 28px;"><?= $icon ?></span>
                            </td>
                            <td style="padding-left: 10px;">
                                <p style="margin: 0; color: #1e293b; font-size: 15px; font-weight: 600;">
                                    Stufe <?= $reward['level'] ?? ($index + 1) ?>: <?= htmlspecialchars($reward['title'] ?? 'Belohnung') ?>
                                </p>
                                <p style="margin: 3px 0 0 0; color: #64748b; font-size: 13px;">
                                    Bei <?= intval($reward['conversions_required'] ?? 3) ?> Empfehlungen
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endif; ?>

<!-- Dashboard-Link -->
<?php if (!empty($dashboard_url)): ?>
<div style="text-align: center; padding: 20px; background-color: #f8fafc; border-radius: 8px; margin-bottom: 20px;">
    <p style="margin: 0 0 15px 0; color: #64748b; font-size: 14px;">
        Verfolgen Sie Ihren Fortschritt jederzeit in Ihrem persönlichen Dashboard:
    </p>
    <a href="<?= htmlspecialchars($dashboard_url) ?>" style="display: inline-block; background: transparent; color: <?= htmlspecialchars($primary_color) ?>; text-decoration: none; padding: 12px 30px; border-radius: 8px; font-weight: 600; font-size: 15px; border: 2px solid <?= htmlspecialchars($primary_color) ?>;">
        📊 Zum Dashboard
    </a>
</div>
<?php endif; ?>

<!-- Abschluss -->
<div style="text-align: center; margin-top: 30px; padding-top: 25px; border-top: 1px solid #e2e8f0;">
    <p style="margin: 0; color: #475569; font-size: 15px; line-height: 1.6;">
        Wir freuen uns auf Ihre ersten Empfehlungen! 💪
    </p>
    <p style="margin: 10px 0 0 0; color: #64748b; font-size: 14px;">
        Ihr Team von <?= htmlspecialchars($company_name) ?>
    </p>
</div>

<?php
$content = ob_get_clean();

// Base-Template einbinden
include __DIR__ . '/_base.php';
