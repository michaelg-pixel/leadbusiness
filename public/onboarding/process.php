<?php
/**
 * Leadbusiness - Onboarding Process
 * 
 * Verarbeitet das Onboarding-Formular und richtet den Kunden-Account ein
 * Verschlankte Version - setzt Defaults für optionale Felder
 */

require_once __DIR__ . '/../../includes/init.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Ungültige Anfrage', 405);
}

try {
    $db = db();
    
    // Formulardaten sammeln
    $data = [
        'token' => input('onboarding_token'),
        'plan' => input('plan', 'starter'),
        'industry' => input('industry'),
        'company_name' => input('company_name'),
        'contact_name' => input('contact_name'),
        'email' => input('email'),
        'password' => $_POST['password'] ?? '',
        'password_confirm' => $_POST['password_confirm'] ?? '',
        'address_street' => input('address_street'),
        'address_zip' => input('address_zip'),
        'address_city' => input('address_city'),
        'subdomain' => input('subdomain'),
        'accept_terms' => isset($_POST['accept_terms'])
    ];
    
    // Validierung
    $errors = validateOnboardingData($data);
    if (!empty($errors)) {
        jsonError(implode(', ', $errors), 400);
    }
    
    // Prüfen ob Subdomain verfügbar
    $existingSubdomain = $db->fetch(
        "SELECT id FROM customers WHERE subdomain = ?",
        [strtolower($data['subdomain'])]
    );
    
    if ($existingSubdomain) {
        jsonError('Diese Subdomain ist bereits vergeben.', 400);
    }
    
    // Prüfen ob E-Mail bereits existiert
    $existingEmail = $db->fetch(
        "SELECT id, onboarding_token FROM customers WHERE email = ?",
        [$data['email']]
    );
    
    // Passwort hashen
    $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
    
    // Default-Hintergrundbild für Branche laden
    $defaultBackground = $db->fetch(
        "SELECT id FROM background_images WHERE industry = ? AND is_default = 1 AND is_active = 1 LIMIT 1",
        [$data['industry']]
    );
    
    // Falls kein Default, erstes aktives Bild nehmen
    if (!$defaultBackground) {
        $defaultBackground = $db->fetch(
            "SELECT id FROM background_images WHERE industry = ? AND is_active = 1 ORDER BY sort_order LIMIT 1",
            [$data['industry']]
        );
    }
    
    // Falls auch kein branchenspezifisches, allgemeines nehmen
    if (!$defaultBackground) {
        $defaultBackground = $db->fetch(
            "SELECT id FROM background_images WHERE industry = 'allgemein' AND is_active = 1 ORDER BY sort_order LIMIT 1"
        );
    }
    
    // Kundendaten mit Defaults für optionale Felder
    $customerData = [
        'email' => $data['email'],
        'password_hash' => $passwordHash,
        'company_name' => $data['company_name'],
        'industry' => $data['industry'],
        'logo_url' => null,                                          // Später im Dashboard
        'primary_color' => '#667eea',                               // Default-Farbe
        'background_image_id' => $defaultBackground['id'] ?? null,  // Auto-Default basierend auf Branche
        'custom_background_url' => null,
        'subdomain' => strtolower($data['subdomain']),
        'contact_name' => $data['contact_name'],
        'phone' => null,                                            // Später im Dashboard
        'website' => null,                                          // Später im Dashboard
        'address_street' => $data['address_street'],
        'address_zip' => $data['address_zip'],
        'address_city' => $data['address_city'],
        'tax_id' => null,                                           // Später im Dashboard
        'plan' => $data['plan'],
        'subscription_status' => 'active',
        'subscription_started_at' => date('Y-m-d H:i:s'),
        'subscription_ends_at' => date('Y-m-d H:i:s', strtotime('+35 days')),
        'email_sender_name' => $data['company_name'],
        'onboarding_completed_at' => date('Y-m-d H:i:s'),
        'onboarding_token' => null,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    // Kunde aktualisieren oder erstellen
    if ($existingEmail && $existingEmail['onboarding_token'] === $data['token']) {
        $customerId = $existingEmail['id'];
        $db->update('customers', $customerData, 'id = ?', [$customerId]);
    } else if (!$existingEmail) {
        $customerData['created_at'] = date('Y-m-d H:i:s');
        $customerId = $db->insert('customers', $customerData);
    } else {
        jsonError('Diese E-Mail-Adresse ist bereits registriert.', 400);
    }
    
    // Standard-Kampagne erstellen
    $campaignId = $db->insert('campaigns', [
        'customer_id' => $customerId,
        'name' => 'Hauptkampagne',
        'slug' => 'main',
        'is_default' => 1,
        'is_active' => 1,
        'settings' => json_encode([
            'double_opt_in' => true,
            'welcome_email' => true,
            'leaderboard_enabled' => true,
            'live_counter_enabled' => true
        ]),
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    // Belohnungen erstellen
    createRewardsFromForm($db, $customerId, $campaignId, $_POST, $data['plan']);
    
    // E-Mail-Sequenzen aktivieren
    activateEmailSequences($db, $customerId);
    
    // Willkommens-E-Mail in Queue (mit korrekten Spaltennamen)
    $welcomeHtml = generateWelcomeEmailHtml($data['contact_name'], $data['company_name'], $data['subdomain']);
    
    $db->insert('email_queue', [
        'customer_id' => $customerId,
        'to_email' => $data['email'],
        'to_name' => $data['contact_name'],
        'from_email' => 'noreply@empfehlungen.cloud',
        'from_name' => 'Leadbusiness',
        'subject' => 'Willkommen bei Leadbusiness – Ihr Empfehlungsprogramm ist bereit!',
        'body_html' => $welcomeHtml,
        'body_text' => strip_tags($welcomeHtml),
        'template' => 'customer_welcome',
        'variables' => json_encode([
            'contact_name' => $data['contact_name'],
            'company_name' => $data['company_name'],
            'subdomain' => $data['subdomain'],
            'dashboard_url' => 'https://empfehlungen.cloud/dashboard'
        ]),
        'priority' => 10,
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    // Session setzen (Auto-Login)
    $_SESSION['customer_id'] = $customerId;
    $_SESSION['customer_email'] = $data['email'];
    $_SESSION['user_type'] = 'customer';
    
    jsonSuccess([
        'customer_id' => $customerId,
        'subdomain' => $data['subdomain'],
        'redirect' => '/dashboard?welcome=1'
    ], 'Einrichtung erfolgreich abgeschlossen!');
    
} catch (Exception $e) {
    error_log('Onboarding Error: ' . $e->getMessage());
    jsonError('Ein Fehler ist aufgetreten: ' . $e->getMessage(), 500);
}

function validateOnboardingData($data) {
    $errors = [];
    
    // Pflichtfelder
    if (empty($data['industry'])) $errors[] = 'Branche ist erforderlich';
    if (empty($data['company_name'])) $errors[] = 'Firmenname ist erforderlich';
    if (empty($data['contact_name'])) $errors[] = 'Ansprechpartner ist erforderlich';
    if (empty($data['email'])) $errors[] = 'E-Mail ist erforderlich';
    if (empty($data['password'])) $errors[] = 'Passwort ist erforderlich';
    if (empty($data['address_street'])) $errors[] = 'Straße ist erforderlich';
    if (empty($data['address_zip'])) $errors[] = 'PLZ ist erforderlich';
    if (empty($data['address_city'])) $errors[] = 'Stadt ist erforderlich';
    if (empty($data['subdomain'])) $errors[] = 'Subdomain ist erforderlich';
    if (!$data['accept_terms']) $errors[] = 'Sie müssen die AGB akzeptieren';
    
    // Format-Validierung
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Ungültige E-Mail-Adresse';
    }
    
    if (!empty($data['password'])) {
        if (strlen($data['password']) < 8) $errors[] = 'Passwort muss mindestens 8 Zeichen lang sein';
        if ($data['password'] !== $data['password_confirm']) $errors[] = 'Passwörter stimmen nicht überein';
    }
    
    if (!empty($data['subdomain'])) {
        if (!preg_match('/^[a-z0-9-]{3,50}$/', $data['subdomain'])) {
            $errors[] = 'Subdomain darf nur Kleinbuchstaben, Zahlen und Bindestriche enthalten (3-50 Zeichen)';
        }
        $reserved = ['www', 'admin', 'api', 'app', 'dashboard', 'mail', 'smtp', 'ftp', 'test', 'dev', 'staging'];
        if (in_array($data['subdomain'], $reserved)) $errors[] = 'Diese Subdomain ist reserviert';
    }
    
    if (!empty($data['address_zip']) && !preg_match('/^[0-9]{5}$/', $data['address_zip'])) {
        $errors[] = 'Ungültige PLZ (5 Ziffern)';
    }
    
    if (!in_array($data['plan'], ['starter', 'professional', 'enterprise'])) {
        $errors[] = 'Ungültiger Tarif';
    }
    
    return $errors;
}

/**
 * Erstellt Belohnungen aus dem Onboarding-Formular
 */
function createRewardsFromForm($db, $customerId, $campaignId, $formData, $plan) {
    $maxLevels = ($plan === 'professional' || $plan === 'enterprise') ? 10 : 3;
    
    for ($i = 1; $i <= $maxLevels; $i++) {
        $threshold = intval($formData["reward_{$i}_threshold"] ?? 0);
        $type = $formData["reward_{$i}_type"] ?? '';
        $description = trim($formData["reward_{$i}_description"] ?? '');
        
        if ($threshold > 0 && !empty($type)) {
            
            $rewardData = [
                'customer_id' => $customerId,
                'campaign_id' => $campaignId,
                'level' => $i,
                'conversions_required' => $threshold,
                'reward_type' => $type,
                'title' => $description ?: getDefaultRewardTitle($type),
                'description' => $description,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // Typspezifische Felder
            switch ($type) {
                case 'discount':
                    $rewardData['discount_percent'] = intval($formData["reward_{$i}_discount_percent"] ?? 0);
                    $rewardData['redeem_url'] = validateUrl($formData["reward_{$i}_discount_url"] ?? '');
                    break;
                    
                case 'coupon_code':
                    $rewardData['coupon_code'] = trim($formData["reward_{$i}_coupon_code"] ?? '');
                    $rewardData['coupon_validity_days'] = 30;
                    $rewardData['redeem_url'] = validateUrl($formData["reward_{$i}_coupon_url"] ?? '');
                    break;
                    
                case 'voucher':
                    $amount = floatval($formData["reward_{$i}_voucher_amount"] ?? 0);
                    $rewardData['voucher_amount'] = $amount > 0 ? $amount : null;
                    $rewardData['redeem_url'] = validateUrl($formData["reward_{$i}_voucher_url"] ?? '');
                    break;
                    
                case 'free_product':
                    $rewardData['product_url'] = validateUrl($formData["reward_{$i}_product_url"] ?? '');
                    break;
                    
                case 'free_service':
                    $rewardData['service_url'] = validateUrl($formData["reward_{$i}_service_url"] ?? '');
                    break;
            }
            
            $db->insert('rewards', $rewardData);
        }
    }
}

function validateUrl($url) {
    $url = trim($url);
    if (empty($url)) return null;
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        return $url;
    }
    return null;
}

function getDefaultRewardTitle($type) {
    $titles = [
        'discount' => 'Rabatt',
        'coupon_code' => 'Gutschein-Code',
        'free_product' => 'Gratis-Produkt',
        'free_service' => 'Gratis-Service',
        'voucher' => 'Wertgutschein'
    ];
    return $titles[$type] ?? 'Belohnung';
}

function activateEmailSequences($db, $customerId) {
    // Standard-Sequenzen nutzen
}

/**
 * Generiert HTML für Willkommens-E-Mail
 */
function generateWelcomeEmailHtml($contactName, $companyName, $subdomain) {
    $dashboardUrl = 'https://empfehlungen.cloud/dashboard';
    $referralUrl = "https://{$subdomain}.empfehlungen.cloud";
    
    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">🎉 Willkommen bei Leadbusiness!</h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="font-size: 16px; color: #333; margin: 0 0 20px;">Hallo {$contactName},</p>
                            
                            <p style="font-size: 16px; color: #333; margin: 0 0 20px;">
                                herzlichen Glückwunsch! Ihr Empfehlungsprogramm für <strong>{$companyName}</strong> ist jetzt eingerichtet und bereit.
                            </p>
                            
                            <div style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                                <p style="font-size: 14px; color: #666; margin: 0 0 10px;"><strong>Ihre Empfehlungsseite:</strong></p>
                                <a href="{$referralUrl}" style="color: #667eea; font-size: 16px; word-break: break-all;">{$referralUrl}</a>
                            </div>
                            
                            <p style="font-size: 16px; color: #333; margin: 20px 0;">
                                <strong>Nächste Schritte:</strong>
                            </p>
                            <ol style="font-size: 15px; color: #333; padding-left: 20px;">
                                <li style="margin-bottom: 10px;">Teilen Sie den Link mit Ihren bestehenden Kunden</li>
                                <li style="margin-bottom: 10px;">Beobachten Sie im Dashboard, wie neue Empfehler hinzukommen</li>
                                <li style="margin-bottom: 10px;">Belohnungen werden automatisch versendet</li>
                            </ol>
                            
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="{$dashboardUrl}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; padding: 14px 30px; border-radius: 6px; font-weight: bold; font-size: 16px;">Zum Dashboard</a>
                            </div>
                            
                            <p style="font-size: 14px; color: #666; margin: 30px 0 0; border-top: 1px solid #eee; padding-top: 20px;">
                                Bei Fragen stehen wir Ihnen gerne zur Verfügung.<br>
                                Ihr Leadbusiness-Team
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px; text-align: center;">
                            <p style="font-size: 12px; color: #999; margin: 0;">
                                © 2025 Leadbusiness | <a href="https://empfehlungen.cloud/datenschutz" style="color: #667eea;">Datenschutz</a> | <a href="https://empfehlungen.cloud/impressum" style="color: #667eea;">Impressum</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
}
