<?php
/**
 * Leadbusiness - Belohnungs-Benachrichtigung E-Mail
 * 
 * Wird versendet wenn ein Empfehler eine Belohnungsstufe erreicht.
 * 
 * Verfügbare Variablen:
 * - $lead_name: Name des Empfehlers
 * - $lead_email: E-Mail des Empfehlers
 * - $company_name: Name des Unternehmens
 * - $company_logo: URL zum Logo (optional)
 * - $primary_color: Hauptfarbe (Hex)
 * - $reward: Array mit Belohnungsdaten
 *   - type: Belohnungstyp (discount, coupon_code, etc.)
 *   - title: Titel der Belohnung
 *   - description: Beschreibung
 *   - level: Stufe (1-10)
 *   - conversions_required: Anzahl Empfehlungen
 *   - discount_percent, coupon_code, redeem_url, etc.
 * - $conversions_count: Aktuelle Anzahl Conversions
 * - $footer_address: Impressums-Adresse
 * - $unsubscribe_url: Abmelde-Link
 * 
 * Platzhalter für E-Mail-Texte:
 * {{empfehler_name}}, {{firmenname}}, {{stufe}}, {{empfehlungen}}
 * {{rabatt_prozent}}, {{gutschein_code}}, {{gutschein_wert}}, {{bar_betrag}}
 * {{einloese_link}}, {{download_link}}, {{bestell_link}}, {{buchungs_link}}
 * {{videokurs_link}}, {{webinar_link}}, {{exklusiv_link}}, {{membership_link}}, {{event_link}}
 * {{affiliate_prozent}}, {{coaching_dauer}}, {{event_name}}, {{event_datum}}
 */

// Defaults
$primary_color = $primary_color ?? '#667eea';
$lead_name = $lead_name ?? 'Empfehler';
$reward = $reward ?? [];

// Belohnungstyp-spezifische Konfiguration
$rewardConfig = getRewardTypeConfig($reward['type'] ?? 'discount');

/**
 * Gibt Konfiguration für jeden Belohnungstyp zurück
 */
function getRewardTypeConfig($type) {
    $configs = [
        'discount' => [
            'icon' => '💰',
            'color' => '#10b981',
            'title_prefix' => 'Rabatt freigeschaltet',
            'emoji' => '🎉'
        ],
        'coupon_code' => [
            'icon' => '🎟️',
            'color' => '#8b5cf6',
            'title_prefix' => 'Gutschein-Code freigeschaltet',
            'emoji' => '🎁'
        ],
        'free_product' => [
            'icon' => '🎁',
            'color' => '#f59e0b',
            'title_prefix' => 'Gratis-Produkt freigeschaltet',
            'emoji' => '📦'
        ],
        'free_service' => [
            'icon' => '⭐',
            'color' => '#06b6d4',
            'title_prefix' => 'Gratis-Service freigeschaltet',
            'emoji' => '✨'
        ],
        'digital_download' => [
            'icon' => '📥',
            'color' => '#3b82f6',
            'title_prefix' => 'Download freigeschaltet',
            'emoji' => '📚'
        ],
        'voucher' => [
            'icon' => '💶',
            'color' => '#10b981',
            'title_prefix' => 'Wertgutschein freigeschaltet',
            'emoji' => '💵'
        ],
        'video_course' => [
            'icon' => '🎬',
            'color' => '#8b5cf6',
            'title_prefix' => 'Video-Kurs freigeschaltet',
            'emoji' => '🎓'
        ],
        'coaching_session' => [
            'icon' => '🎯',
            'color' => '#10b981',
            'title_prefix' => 'Coaching-Session freigeschaltet',
            'emoji' => '💪'
        ],
        'webinar_access' => [
            'icon' => '📹',
            'color' => '#6366f1',
            'title_prefix' => 'Webinar-Zugang freigeschaltet',
            'emoji' => '🖥️'
        ],
        'exclusive_content' => [
            'icon' => '🔐',
            'color' => '#f59e0b',
            'title_prefix' => 'Exklusiver Inhalt freigeschaltet',
            'emoji' => '🌟'
        ],
        'affiliate_commission' => [
            'icon' => '💸',
            'color' => '#10b981',
            'title_prefix' => 'Affiliate-Provision freigeschaltet',
            'emoji' => '🤝'
        ],
        'cash_bonus' => [
            'icon' => '🏆',
            'color' => '#f59e0b',
            'title_prefix' => 'Bar-Auszahlung freigeschaltet',
            'emoji' => '💰'
        ],
        'membership_upgrade' => [
            'icon' => '👑',
            'color' => '#8b5cf6',
            'title_prefix' => 'Membership-Upgrade freigeschaltet',
            'emoji' => '🚀'
        ],
        'event_ticket' => [
            'icon' => '🎫',
            'color' => '#ec4899',
            'title_prefix' => 'Event-Ticket freigeschaltet',
            'emoji' => '🎪'
        ]
    ];
    
    return $configs[$type] ?? $configs['discount'];
}

/**
 * Ersetzt Platzhalter im Text
 */
function replacePlaceholders($text, $data) {
    $replacements = [
        '{{empfehler_name}}' => $data['lead_name'] ?? '',
        '{{firmenname}}' => $data['company_name'] ?? '',
        '{{stufe}}' => $data['reward']['level'] ?? '',
        '{{empfehlungen}}' => $data['conversions_count'] ?? '',
        '{{rabatt_prozent}}' => ($data['reward']['discount_percent'] ?? 0) . '%',
        '{{gutschein_code}}' => $data['reward']['coupon_code'] ?? '',
        '{{gutschein_wert}}' => number_format($data['reward']['voucher_amount'] ?? 0, 2, ',', '.') . '€',
        '{{bar_betrag}}' => number_format($data['reward']['cash_amount'] ?? 0, 2, ',', '.') . '€',
        '{{einloese_link}}' => $data['reward']['redeem_url'] ?? '',
        '{{download_link}}' => $data['reward']['download_file_url'] ?? '',
        '{{bestell_link}}' => $data['reward']['product_url'] ?? '',
        '{{buchungs_link}}' => $data['reward']['service_url'] ?? $data['reward']['coaching_booking_url'] ?? '',
        '{{videokurs_link}}' => $data['reward']['video_url'] ?? '',
        '{{webinar_link}}' => $data['reward']['webinar_url'] ?? '',
        '{{exklusiv_link}}' => $data['reward']['exclusive_url'] ?? '',
        '{{membership_link}}' => $data['reward']['membership_url'] ?? '',
        '{{event_link}}' => $data['reward']['event_url'] ?? '',
        '{{affiliate_prozent}}' => ($data['reward']['affiliate_percent'] ?? 0) . '%',
        '{{coaching_dauer}}' => ($data['reward']['coaching_duration'] ?? 30) . ' Minuten',
        '{{event_name}}' => $data['reward']['event_name'] ?? '',
        '{{event_datum}}' => !empty($data['reward']['event_date']) 
            ? date('d.m.Y', strtotime($data['reward']['event_date'])) : '',
        '{{zugangscode}}' => $data['reward']['video_access_code'] ?? ''
    ];
    
    return str_replace(array_keys($replacements), array_values($replacements), $text);
}

// E-Mail-Betreff
$subject = $rewardConfig['emoji'] . ' ' . $rewardConfig['title_prefix'] . ' – Stufe ' . ($reward['level'] ?? 1);
$preheader = 'Herzlichen Glückwunsch! Sie haben eine neue Belohnung bei ' . $company_name . ' freigeschaltet.';

// Content generieren
ob_start();
?>

<!-- Herzlichen Glückwunsch Header -->
<div style="text-align: center; margin-bottom: 30px;">
    <div style="font-size: 64px; line-height: 1; margin-bottom: 15px;">
        <?= $rewardConfig['icon'] ?>
    </div>
    <h1 style="margin: 0 0 10px 0; color: #1e293b; font-size: 28px; font-weight: 700;">
        Herzlichen Glückwunsch, <?= htmlspecialchars($lead_name) ?>!
    </h1>
    <p style="margin: 0; color: #64748b; font-size: 16px;">
        Sie haben Stufe <?= $reward['level'] ?? 1 ?> erreicht 🎉
    </p>
</div>

<!-- Erfolgs-Box -->
<div style="background: linear-gradient(135deg, <?= htmlspecialchars($rewardConfig['color']) ?>15 0%, <?= htmlspecialchars($rewardConfig['color']) ?>05 100%); border: 2px solid <?= htmlspecialchars($rewardConfig['color']) ?>40; border-radius: 12px; padding: 25px; margin-bottom: 30px;">
    <p style="margin: 0 0 5px 0; color: <?= htmlspecialchars($rewardConfig['color']) ?>; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
        Ihre Belohnung
    </p>
    <h2 style="margin: 0 0 10px 0; color: #1e293b; font-size: 22px; font-weight: 700;">
        <?= htmlspecialchars($reward['title'] ?? 'Belohnung') ?>
    </h2>
    <?php if (!empty($reward['description'])): ?>
    <p style="margin: 0; color: #475569; font-size: 15px; line-height: 1.6;">
        <?= nl2br(htmlspecialchars($reward['description'])) ?>
    </p>
    <?php endif; ?>
</div>

<!-- Typ-spezifische Details -->
<?php
switch ($reward['type'] ?? 'discount') {
    case 'discount':
        if (!empty($reward['discount_percent'])):
?>
<div style="background-color: #f8fafc; border-radius: 8px; padding: 20px; margin-bottom: 20px; text-align: center;">
    <p style="margin: 0 0 5px 0; color: #64748b; font-size: 14px;">Ihr Rabatt:</p>
    <p style="margin: 0; color: <?= htmlspecialchars($rewardConfig['color']) ?>; font-size: 36px; font-weight: 800;">
        <?= intval($reward['discount_percent']) ?>%
    </p>
</div>
<?php if (!empty($reward['redeem_url'])): ?>
<div style="text-align: center; margin-bottom: 25px;">
    <a href="<?= htmlspecialchars($reward['redeem_url']) ?>" style="display: inline-block; background: <?= htmlspecialchars($primary_color) ?>; color: #ffffff; text-decoration: none; padding: 14px 35px; border-radius: 8px; font-weight: 600; font-size: 16px;">
        Jetzt einlösen →
    </a>
</div>
<?php endif; endif; break;

    case 'coupon_code':
        if (!empty($reward['coupon_code'])):
?>
<div style="background-color: #f8fafc; border-radius: 8px; padding: 20px; margin-bottom: 20px; text-align: center;">
    <p style="margin: 0 0 10px 0; color: #64748b; font-size: 14px;">Ihr Gutschein-Code:</p>
    <div style="background-color: #1e293b; border-radius: 6px; padding: 15px 25px; display: inline-block;">
        <code style="color: #22c55e; font-size: 24px; font-weight: 700; letter-spacing: 2px; font-family: 'Courier New', monospace;">
            <?= htmlspecialchars($reward['coupon_code']) ?>
        </code>
    </div>
    <?php if (!empty($reward['coupon_validity_days'])): ?>
    <p style="margin: 15px 0 0 0; color: #94a3b8; font-size: 13px;">
        Gültig für <?= intval($reward['coupon_validity_days']) ?> Tage
    </p>
    <?php endif; ?>
</div>
<?php if (!empty($reward['redeem_url'])): ?>
<div style="text-align: center; margin-bottom: 25px;">
    <a href="<?= htmlspecialchars($reward['redeem_url']) ?>" style="display: inline-block; background: <?= htmlspecialchars($primary_color) ?>; color: #ffffff; text-decoration: none; padding: 14px 35px; border-radius: 8px; font-weight: 600; font-size: 16px;">
        Jetzt einlösen →
    </a>
</div>
<?php endif; endif; break;

    case 'voucher':
        if (!empty($reward['voucher_amount'])):
?>
<div style="background-color: #f8fafc; border-radius: 8px; padding: 20px; margin-bottom: 20px; text-align: center;">
    <p style="margin: 0 0 5px 0; color: #64748b; font-size: 14px;">Ihr Gutscheinwert:</p>
    <p style="margin: 0; color: <?= htmlspecialchars($rewardConfig['color']) ?>; font-size: 36px; font-weight: 800;">
        <?= number_format($reward['voucher_amount'], 2, ',', '.') ?> €
    </p>
</div>
<?php if (!empty($reward['redeem_url'])): ?>
<div style="text-align: center; margin-bottom: 25px;">
    <a href="<?= htmlspecialchars($reward['redeem_url']) ?>" style="display: inline-block; background: <?= htmlspecialchars($primary_color) ?>; color: #ffffff; text-decoration: none; padding: 14px 35px; border-radius: 8px; font-weight: 600; font-size: 16px;">
        Jetzt einlösen →
    </a>
</div>
<?php endif; endif; break;

    case 'digital_download':
        if (!empty($reward['download_file_url'])):
?>
<div style="text-align: center; margin-bottom: 25px;">
    <a href="<?= htmlspecialchars($reward['download_file_url']) ?>" style="display: inline-block; background: <?= htmlspecialchars($primary_color) ?>; color: #ffffff; text-decoration: none; padding: 14px 35px; border-radius: 8px; font-weight: 600; font-size: 16px;">
        📥 Jetzt herunterladen
    </a>
    <p style="margin: 15px 0 0 0; color: #94a3b8; font-size: 13px;">
        Der Download-Link ist 7 Tage gültig.
    </p>
</div>
<?php endif; break;

    case 'free_product':
?>
<div style="background-color: #fef3c7; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
    <p style="margin: 0; color: #92400e; font-size: 14px;">
        <strong>📦 Gratis-Produkt:</strong> <?= htmlspecialchars($reward['title']) ?>
    </p>
    <?php if (!empty($reward['requires_address'])): ?>
    <p style="margin: 10px 0 0 0; color: #b45309; font-size: 13px;">
        Wir werden Sie kontaktieren, um Ihre Lieferadresse abzufragen.
    </p>
    <?php endif; ?>
</div>
<?php if (!empty($reward['product_url'])): ?>
<div style="text-align: center; margin-bottom: 25px;">
    <a href="<?= htmlspecialchars($reward['product_url']) ?>" style="display: inline-block; background: <?= htmlspecialchars($primary_color) ?>; color: #ffffff; text-decoration: none; padding: 14px 35px; border-radius: 8px; font-weight: 600; font-size: 16px;">
        Produkt ansehen →
    </a>
</div>
<?php endif; break;

    case 'free_service':
?>
<div style="background-color: #e0f2fe; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
    <p style="margin: 0; color: #0369a1; font-size: 14px;">
        <strong>⭐ Gratis-Service:</strong> <?= htmlspecialchars($reward['title']) ?>
    </p>
    <p style="margin: 10px 0 0 0; color: #0284c7; font-size: 13px;">
        Wir werden uns in Kürze bei Ihnen melden, um einen Termin zu vereinbaren.
    </p>
</div>
<?php if (!empty($reward['service_url'])): ?>
<div style="text-align: center; margin-bottom: 25px;">
    <a href="<?= htmlspecialchars($reward['service_url']) ?>" style="display: inline-block; background: <?= htmlspecialchars($primary_color) ?>; color: #ffffff; text-decoration: none; padding: 14px 35px; border-radius: 8px; font-weight: 600; font-size: 16px;">
        Termin buchen →
    </a>
</div>
<?php endif; break;

    case 'video_course':
        if (!empty($reward['video_url'])):
?>
<div style="background-color: #f5f3ff; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
    <p style="margin: 0 0 10px 0; color: #6d28d9; font-size: 14px; font-weight: 600;">
        🎬 Ihr Video-Kurs wartet auf Sie!
    </p>
    <?php if (!empty($reward['video_access_code'])): ?>
    <p style="margin: 0 0 10px 0; color: #7c3aed; font-size: 14px;">
        <strong>Zugangscode:</strong> <code style="background: #ddd6fe; padding: 3px 8px; border-radius: 4px;"><?= htmlspecialchars($reward['video_access_code']) ?></code>
    </p>
    <?php endif; ?>
    <?php if (!empty($reward['video_validity_days'])): ?>
    <p style="margin: 0; color: #8b5cf6; font-size: 13px;">
        Zugang gültig für <?= intval($reward['video_validity_days']) ?> Tage
    </p>
    <?php endif; ?>
</div>
<div style="text-align: center; margin-bottom: 25px;">
    <a href="<?= htmlspecialchars($reward['video_url']) ?>" style="display: inline-block; background: <?= htmlspecialchars($primary_color) ?>; color: #ffffff; text-decoration: none; padding: 14px 35px; border-radius: 8px; font-weight: 600; font-size: 16px;">
        🎬 Zum Video-Kurs →
    </a>
</div>
<?php endif; break;

    case 'coaching_session':
?>
<div style="background-color: #ecfdf5; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
    <p style="margin: 0 0 10px 0; color: #047857; font-size: 14px; font-weight: 600;">
        🎯 Ihre Coaching-Session
    </p>
    <p style="margin: 0; color: #059669; font-size: 14px;">
        <strong>Dauer:</strong> <?= intval($reward['coaching_duration'] ?? 30) ?> Minuten<br>
        <strong>Art:</strong> <?= htmlspecialchars(getCoachingTypeLabel($reward['coaching_type'] ?? 'video_call')) ?>
    </p>
</div>
<?php if (!empty($reward['coaching_booking_url'])): ?>
<div style="text-align: center; margin-bottom: 25px;">
    <a href="<?= htmlspecialchars($reward['coaching_booking_url']) ?>" style="display: inline-block; background: <?= htmlspecialchars($primary_color) ?>; color: #ffffff; text-decoration: none; padding: 14px 35px; border-radius: 8px; font-weight: 600; font-size: 16px;">
        📅 Termin buchen →
    </a>
</div>
<?php endif; break;

    case 'webinar_access':
        if (!empty($reward['webinar_url'])):
?>
<div style="background-color: #eef2ff; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
    <p style="margin: 0 0 10px 0; color: #4338ca; font-size: 14px; font-weight: 600;">
        📹 Ihr Webinar-Zugang
    </p>
    <?php if (!empty($reward['webinar_date'])): ?>
    <p style="margin: 0; color: #4f46e5; font-size: 14px;">
        <strong>Datum:</strong> <?= date('d.m.Y', strtotime($reward['webinar_date'])) ?>
        <?php if (!empty($reward['webinar_time'])): ?>
        um <?= date('H:i', strtotime($reward['webinar_time'])) ?> Uhr
        <?php endif; ?>
    </p>
    <?php endif; ?>
</div>
<div style="text-align: center; margin-bottom: 25px;">
    <a href="<?= htmlspecialchars($reward['webinar_url']) ?>" style="display: inline-block; background: <?= htmlspecialchars($primary_color) ?>; color: #ffffff; text-decoration: none; padding: 14px 35px; border-radius: 8px; font-weight: 600; font-size: 16px;">
        📹 Zum Webinar →
    </a>
</div>
<?php endif; break;

    case 'exclusive_content':
        if (!empty($reward['exclusive_url'])):
?>
<div style="background-color: #fffbeb; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
    <p style="margin: 0; color: #b45309; font-size: 14px; font-weight: 600;">
        🔐 Exklusiver Inhalt freigeschaltet: <?= htmlspecialchars(getExclusiveTypeLabel($reward['exclusive_type'] ?? 'ebook')) ?>
    </p>
</div>
<div style="text-align: center; margin-bottom: 25px;">
    <a href="<?= htmlspecialchars($reward['exclusive_url']) ?>" style="display: inline-block; background: <?= htmlspecialchars($primary_color) ?>; color: #ffffff; text-decoration: none; padding: 14px 35px; border-radius: 8px; font-weight: 600; font-size: 16px;">
        🔓 Jetzt zugreifen →
    </a>
</div>
<?php endif; break;

    case 'affiliate_commission':
        if (!empty($reward['affiliate_percent'])):
?>
<div style="background-color: #ecfdf5; border-radius: 8px; padding: 20px; margin-bottom: 20px; text-align: center;">
    <p style="margin: 0 0 5px 0; color: #047857; font-size: 14px;">Ihre Provision:</p>
    <p style="margin: 0; color: #059669; font-size: 36px; font-weight: 800;">
        <?= intval($reward['affiliate_percent']) ?>%
    </p>
    <?php if (!empty($reward['affiliate_product'])): ?>
    <p style="margin: 10px 0 0 0; color: #10b981; font-size: 13px;">
        auf: <?= htmlspecialchars($reward['affiliate_product']) ?>
    </p>
    <?php endif; ?>
    <?php if (!empty($reward['affiliate_max_amount'])): ?>
    <p style="margin: 5px 0 0 0; color: #6ee7b7; font-size: 12px;">
        (max. <?= number_format($reward['affiliate_max_amount'], 2, ',', '.') ?> € pro Verkauf)
    </p>
    <?php endif; ?>
</div>
<p style="color: #64748b; font-size: 14px; text-align: center;">
    Wir werden Sie kontaktieren, um die Details zur Auszahlung zu klären.
</p>
<?php endif; break;

    case 'cash_bonus':
        if (!empty($reward['cash_amount'])):
?>
<div style="background-color: #fef3c7; border-radius: 8px; padding: 20px; margin-bottom: 20px; text-align: center;">
    <p style="margin: 0 0 5px 0; color: #92400e; font-size: 14px;">Ihre Bar-Auszahlung:</p>
    <p style="margin: 0; color: #d97706; font-size: 36px; font-weight: 800;">
        <?= number_format($reward['cash_amount'], 2, ',', '.') ?> €
    </p>
    <p style="margin: 10px 0 0 0; color: #b45309; font-size: 13px;">
        Auszahlung per: <?= htmlspecialchars(getCashMethodLabel($reward['cash_method'] ?? 'bank_transfer')) ?>
    </p>
</div>
<p style="color: #64748b; font-size: 14px; text-align: center;">
    Wir werden Sie kontaktieren, um Ihre Zahlungsdaten abzufragen.
</p>
<?php endif; break;

    case 'membership_upgrade':
?>
<div style="background-color: #f5f3ff; border-radius: 8px; padding: 20px; margin-bottom: 20px; text-align: center;">
    <p style="margin: 0 0 5px 0; color: #6d28d9; font-size: 14px;">Ihr Upgrade:</p>
    <p style="margin: 0; color: #7c3aed; font-size: 24px; font-weight: 700;">
        👑 <?= htmlspecialchars($reward['membership_level'] ?? 'Premium-Mitgliedschaft') ?>
    </p>
    <?php if (!empty($reward['membership_duration'])): ?>
    <p style="margin: 10px 0 0 0; color: #8b5cf6; font-size: 13px;">
        Laufzeit: <?= getMembershipDurationLabel($reward['membership_duration']) ?>
    </p>
    <?php endif; ?>
</div>
<?php if (!empty($reward['membership_url'])): ?>
<div style="text-align: center; margin-bottom: 25px;">
    <a href="<?= htmlspecialchars($reward['membership_url']) ?>" style="display: inline-block; background: <?= htmlspecialchars($primary_color) ?>; color: #ffffff; text-decoration: none; padding: 14px 35px; border-radius: 8px; font-weight: 600; font-size: 16px;">
        👑 Jetzt aktivieren →
    </a>
</div>
<?php endif; break;

    case 'event_ticket':
?>
<div style="background-color: #fdf2f8; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
    <p style="margin: 0 0 10px 0; color: #be185d; font-size: 14px; font-weight: 600;">
        🎫 Ihr Event-Ticket
    </p>
    <?php if (!empty($reward['event_name'])): ?>
    <p style="margin: 0 0 5px 0; color: #db2777; font-size: 18px; font-weight: 700;">
        <?= htmlspecialchars($reward['event_name']) ?>
    </p>
    <?php endif; ?>
    <p style="margin: 0; color: #ec4899; font-size: 14px;">
        <?php if (!empty($reward['event_date'])): ?>
        📅 <?= date('d.m.Y', strtotime($reward['event_date'])) ?>
        <?php endif; ?>
        <?php if (!empty($reward['event_location'])): ?>
        &nbsp;|&nbsp; 📍 <?= htmlspecialchars($reward['event_location']) ?>
        <?php endif; ?>
    </p>
</div>
<?php if (!empty($reward['event_url'])): ?>
<div style="text-align: center; margin-bottom: 25px;">
    <a href="<?= htmlspecialchars($reward['event_url']) ?>" style="display: inline-block; background: <?= htmlspecialchars($primary_color) ?>; color: #ffffff; text-decoration: none; padding: 14px 35px; border-radius: 8px; font-weight: 600; font-size: 16px;">
        🎫 Ticket sichern →
    </a>
</div>
<?php endif; break;
}
?>

<!-- Statistik-Box -->
<div style="background-color: #f8fafc; border-radius: 8px; padding: 20px; margin-top: 30px; text-align: center;">
    <p style="margin: 0; color: #64748b; font-size: 14px;">
        🏆 Sie haben insgesamt <strong style="color: <?= htmlspecialchars($primary_color) ?>;"><?= intval($conversions_count ?? 0) ?> erfolgreiche Empfehlungen</strong> gesammelt!
    </p>
</div>

<!-- Danke-Nachricht -->
<div style="text-align: center; margin-top: 30px; padding-top: 25px; border-top: 1px solid #e2e8f0;">
    <p style="margin: 0; color: #475569; font-size: 15px; line-height: 1.6;">
        Vielen Dank, dass Sie <?= htmlspecialchars($company_name) ?> weiterempfehlen! 💜
    </p>
</div>

<?php
$content = ob_get_clean();

// Helper-Funktionen
function getCoachingTypeLabel($type) {
    $labels = [
        'video_call' => 'Video-Call (Zoom/Meet)',
        'phone' => 'Telefon',
        'in_person' => 'Vor Ort',
        'chat' => 'Chat/Messenger'
    ];
    return $labels[$type] ?? $type;
}

function getExclusiveTypeLabel($type) {
    $labels = [
        'ebook' => 'E-Book / PDF',
        'template' => 'Templates / Vorlagen',
        'checklist' => 'Checkliste',
        'bonus_video' => 'Bonus-Video',
        'audio' => 'Audio / Podcast',
        'software' => 'Software / Tool',
        'other' => 'Exklusiver Inhalt'
    ];
    return $labels[$type] ?? $type;
}

function getCashMethodLabel($method) {
    $labels = [
        'bank_transfer' => 'Banküberweisung',
        'paypal' => 'PayPal',
        'amazon_gift' => 'Amazon Gutschein'
    ];
    return $labels[$method] ?? $method;
}

function getMembershipDurationLabel($duration) {
    if ($duration === 'lifetime') return 'Lebenslang';
    return $duration . ' ' . ($duration == 1 ? 'Monat' : 'Monate');
}

// Base-Template einbinden
include __DIR__ . '/_base.php';
