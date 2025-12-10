<?php
/**
 * Leadbusiness - Erinnerungs-E-Mail für Empfehler
 * 
 * Wird versendet wenn ein Empfehler X Tage inaktiv war.
 * 
 * Verfügbare Variablen:
 * - $lead_name: Name des Empfehlers
 * - $company_name: Name des Unternehmens
 * - $company_logo: URL zum Logo (optional)
 * - $primary_color: Hauptfarbe (Hex)
 * - $referral_link: Persönlicher Empfehlungslink
 * - $current_conversions: Aktuelle Anzahl Conversions
 * - $next_reward: Nächste Belohnung (Array)
 * - $conversions_needed: Anzahl noch benötigter Conversions
 * - $dashboard_url: Link zum Dashboard
 * - $days_inactive: Tage seit letzter Aktivität
 * - $reminder_type: Typ der Erinnerung (first, second, reactivation)
 * - $footer_address: Impressums-Adresse
 * - $unsubscribe_url: Abmelde-Link
 */

// Defaults
$primary_color = $primary_color ?? '#667eea';
$lead_name = $lead_name ?? 'Empfehler';
$reminder_type = $reminder_type ?? 'first';
$current_conversions = $current_conversions ?? 0;
$conversions_needed = $conversions_needed ?? 3;
$next_reward = $next_reward ?? null;
$days_inactive = $days_inactive ?? 7;

// Betreff je nach Erinnerungs-Typ
$subjects = [
    'first' => '👋 Haben Sie uns schon empfohlen?',
    'second' => '🎯 Nur noch ' . $conversions_needed . ' Empfehlungen bis zur Belohnung!',
    'reactivation' => '💜 Wir vermissen Sie, ' . $lead_name . '!'
];
$subject = $subjects[$reminder_type] ?? $subjects['first'];

$preheaders = [
    'first' => 'Teilen Sie Ihren persönlichen Link und sichern Sie sich Belohnungen!',
    'second' => 'So nah dran! Noch ' . $conversions_needed . ' erfolgreiche Empfehlungen.',
    'reactivation' => 'Ihr Empfehlungsprogramm wartet auf Sie – starten Sie jetzt durch!'
];
$preheader = $preheaders[$reminder_type] ?? '';

// Content
ob_start();
?>

<?php if ($reminder_type === 'first'): ?>
<!-- Erste Erinnerung -->
<div style="text-align: center; margin-bottom: 30px;">
    <div style="font-size: 64px; line-height: 1; margin-bottom: 15px;">
        👋
    </div>
    <h1 style="margin: 0 0 10px 0; color: #1e293b; font-size: 26px; font-weight: 700;">
        Hallo <?= htmlspecialchars($lead_name) ?>!
    </h1>
    <p style="margin: 0; color: #64748b; font-size: 16px; line-height: 1.6;">
        Sie haben sich für das Empfehlungsprogramm von <?= htmlspecialchars($company_name) ?> angemeldet, 
        aber noch keine Empfehlungen geteilt.
    </p>
</div>

<div style="background-color: #f8fafc; border-radius: 12px; padding: 25px; margin-bottom: 30px; text-align: center;">
    <p style="margin: 0 0 15px 0; color: #1e293b; font-size: 16px; font-weight: 600;">
        🎁 Es warten tolle Belohnungen auf Sie!
    </p>
    <p style="margin: 0; color: #64748b; font-size: 15px;">
        Teilen Sie einfach Ihren persönlichen Link mit Freunden und Bekannten.
    </p>
</div>

<?php elseif ($reminder_type === 'second'): ?>
<!-- Zweite Erinnerung - Fast geschafft -->
<div style="text-align: center; margin-bottom: 30px;">
    <div style="font-size: 64px; line-height: 1; margin-bottom: 15px;">
        🎯
    </div>
    <h1 style="margin: 0 0 10px 0; color: #1e293b; font-size: 26px; font-weight: 700;">
        Fast geschafft, <?= htmlspecialchars($lead_name) ?>!
    </h1>
    <p style="margin: 0; color: #64748b; font-size: 16px;">
        Sie sind so nah an Ihrer nächsten Belohnung dran!
    </p>
</div>

<!-- Fortschritts-Box -->
<div style="background: linear-gradient(135deg, <?= htmlspecialchars($primary_color) ?>15 0%, <?= htmlspecialchars($primary_color) ?>05 100%); border: 2px solid <?= htmlspecialchars($primary_color) ?>40; border-radius: 12px; padding: 25px; margin-bottom: 30px; text-align: center;">
    <p style="margin: 0 0 5px 0; color: #64748b; font-size: 14px;">Ihr aktueller Stand:</p>
    <p style="margin: 0; color: <?= htmlspecialchars($primary_color) ?>; font-size: 48px; font-weight: 800;">
        <?= intval($current_conversions) ?>
    </p>
    <p style="margin: 5px 0 0 0; color: #64748b; font-size: 14px;">
        erfolgreiche Empfehlungen
    </p>
    
    <!-- Fortschrittsbalken -->
    <?php 
    $targetConversions = $current_conversions + $conversions_needed;
    $progress = ($targetConversions > 0) ? ($current_conversions / $targetConversions) * 100 : 0;
    ?>
    <div style="background-color: #e2e8f0; border-radius: 10px; height: 12px; margin: 20px 0 10px 0; overflow: hidden;">
        <div style="background: linear-gradient(90deg, <?= htmlspecialchars($primary_color) ?>, #8b5cf6); height: 100%; width: <?= min(100, $progress) ?>%; border-radius: 10px;"></div>
    </div>
    <p style="margin: 0; color: #475569; font-size: 15px; font-weight: 600;">
        Nur noch <span style="color: <?= htmlspecialchars($primary_color) ?>;"><?= intval($conversions_needed) ?></span> bis zur nächsten Belohnung!
    </p>
</div>

<?php if ($next_reward): ?>
<div style="background-color: #fef3c7; border-radius: 8px; padding: 20px; margin-bottom: 25px; text-align: center;">
    <p style="margin: 0 0 5px 0; color: #92400e; font-size: 13px; font-weight: 600; text-transform: uppercase;">
        Ihre nächste Belohnung
    </p>
    <p style="margin: 0; color: #b45309; font-size: 18px; font-weight: 700;">
        🎁 <?= htmlspecialchars($next_reward['title'] ?? 'Belohnung') ?>
    </p>
</div>
<?php endif; ?>

<?php else: ?>
<!-- Reaktivierung -->
<div style="text-align: center; margin-bottom: 30px;">
    <div style="font-size: 64px; line-height: 1; margin-bottom: 15px;">
        💜
    </div>
    <h1 style="margin: 0 0 10px 0; color: #1e293b; font-size: 26px; font-weight: 700;">
        Wir vermissen Sie, <?= htmlspecialchars($lead_name) ?>!
    </h1>
    <p style="margin: 0; color: #64748b; font-size: 16px; line-height: 1.6;">
        Es ist <?= intval($days_inactive) ?> Tage her, seit Sie das Empfehlungsprogramm 
        von <?= htmlspecialchars($company_name) ?> genutzt haben.
    </p>
</div>

<?php if ($current_conversions > 0): ?>
<div style="background-color: #f0fdf4; border-radius: 8px; padding: 20px; margin-bottom: 25px; text-align: center;">
    <p style="margin: 0; color: #166534; font-size: 15px;">
        🏆 Sie haben bereits <strong><?= intval($current_conversions) ?> Empfehlungen</strong> gesammelt! 
        Machen Sie weiter so!
    </p>
</div>
<?php endif; ?>

<div style="background-color: #f8fafc; border-radius: 12px; padding: 25px; margin-bottom: 30px; text-align: center;">
    <p style="margin: 0 0 15px 0; color: #1e293b; font-size: 16px; font-weight: 600;">
        ✨ Ihre Belohnungen warten noch auf Sie!
    </p>
    <p style="margin: 0; color: #64748b; font-size: 15px;">
        Teilen Sie Ihren Link noch heute und sichern Sie sich tolle Prämien.
    </p>
</div>
<?php endif; ?>

<!-- Empfehlungslink -->
<div style="background-color: #ffffff; border: 2px dashed #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 30px; text-align: center;">
    <p style="margin: 0 0 10px 0; color: #64748b; font-size: 14px;">
        🔗 Ihr persönlicher Empfehlungslink:
    </p>
    <a href="<?= htmlspecialchars($referral_link) ?>" style="color: <?= htmlspecialchars($primary_color) ?>; font-size: 15px; font-weight: 600; text-decoration: none; word-break: break-all;">
        <?= htmlspecialchars($referral_link) ?>
    </a>
</div>

<!-- CTA Button -->
<div style="text-align: center; margin-bottom: 30px;">
    <a href="<?= htmlspecialchars($referral_link) ?>" style="display: inline-block; background: <?= htmlspecialchars($primary_color) ?>; color: #ffffff; text-decoration: none; padding: 16px 40px; border-radius: 8px; font-weight: 600; font-size: 16px;">
        🚀 Jetzt teilen
    </a>
</div>

<!-- Tipps zum Teilen -->
<div style="margin-bottom: 30px;">
    <h3 style="margin: 0 0 15px 0; color: #1e293b; font-size: 16px; font-weight: 600;">
        💡 Tipps zum erfolgreichen Empfehlen:
    </h3>
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="padding: 8px 0; color: #475569; font-size: 14px;">
                <span style="color: <?= htmlspecialchars($primary_color) ?>; margin-right: 10px;">•</span>
                Teilen Sie per WhatsApp mit Freunden und Familie
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #475569; font-size: 14px;">
                <span style="color: <?= htmlspecialchars($primary_color) ?>; margin-right: 10px;">•</span>
                Posten Sie auf Facebook oder Instagram
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #475569; font-size: 14px;">
                <span style="color: <?= htmlspecialchars($primary_color) ?>; margin-right: 10px;">•</span>
                Erzählen Sie Kollegen oder Bekannten davon
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #475569; font-size: 14px;">
                <span style="color: <?= htmlspecialchars($primary_color) ?>; margin-right: 10px;">•</span>
                Schicken Sie den Link per E-Mail weiter
            </td>
        </tr>
    </table>
</div>

<!-- Dashboard Link -->
<?php if (!empty($dashboard_url)): ?>
<div style="text-align: center; padding: 20px; background-color: #f8fafc; border-radius: 8px; margin-bottom: 20px;">
    <p style="margin: 0 0 10px 0; color: #64748b; font-size: 14px;">
        Ihren Fortschritt können Sie jederzeit hier einsehen:
    </p>
    <a href="<?= htmlspecialchars($dashboard_url) ?>" style="color: <?= htmlspecialchars($primary_color) ?>; text-decoration: none; font-weight: 600;">
        📊 Zum Dashboard →
    </a>
</div>
<?php endif; ?>

<!-- Abschluss -->
<div style="text-align: center; margin-top: 30px; padding-top: 25px; border-top: 1px solid #e2e8f0;">
    <p style="margin: 0; color: #475569; font-size: 15px; line-height: 1.6;">
        Wir freuen uns auf Ihre Empfehlungen! 💪
    </p>
    <p style="margin: 10px 0 0 0; color: #64748b; font-size: 14px;">
        Ihr Team von <?= htmlspecialchars($company_name) ?>
    </p>
</div>

<?php
$content = ob_get_clean();

// Base-Template einbinden
include __DIR__ . '/_base.php';
