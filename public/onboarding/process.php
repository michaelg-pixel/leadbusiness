<?php
/**
 * Leadbusiness - Onboarding Process
 * 
 * Verarbeitet das Onboarding-Formular und richtet den Kunden-Account ein
 */

require_once __DIR__ . '/../../includes/init.php';

header('Content-Type: application/json');

// Nur POST-Requests
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
        'website' => input('website'),
        'contact_name' => input('contact_name'),
        'email' => input('email'),
        'phone' => input('phone'),
        'password' => $_POST['password'] ?? '',
        'password_confirm' => $_POST['password_confirm'] ?? '',
        'address_street' => input('address_street'),
        'address_zip' => input('address_zip'),
        'address_city' => input('address_city'),
        'tax_id' => input('tax_id'),
        'subdomain' => input('subdomain'),
        'background_image_id' => intval(input('background_image_id', 0)),
        'primary_color' => input('primary_color', '#667eea'),
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
    
    // Logo hochladen
    $logoUrl = null;
    if (!empty($_FILES['logo']['tmp_name'])) {
        $logoUrl = handleLogoUpload($_FILES['logo'], $data['subdomain']);
    }
    
    // Custom Background hochladen (Professional only)
    $customBackgroundUrl = null;
    if ($data['plan'] === 'professional' && !empty($_FILES['custom_background']['tmp_name'])) {
        $customBackgroundUrl = handleBackgroundUpload($_FILES['custom_background'], $data['subdomain']);
    }
    
    // Passwort hashen
    $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
    
    // Kundendaten
    $customerData = [
        'email' => $data['email'],
        'password_hash' => $passwordHash,
        'company_name' => $data['company_name'],
        'industry' => $data['industry'],
        'logo_url' => $logoUrl,
        'primary_color' => $data['primary_color'],
        'background_image_id' => $data['background_image_id'] ?: null,
        'custom_background_url' => $customBackgroundUrl,
        'subdomain' => strtolower($data['subdomain']),
        'contact_name' => $data['contact_name'],
        'phone' => $data['phone'],
        'website' => $data['website'],
        'address_street' => $data['address_street'],
        'address_zip' => $data['address_zip'],
        'address_city' => $data['address_city'],
        'tax_id' => $data['tax_id'],
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
    
    // Belohnungen erstellen (mit allen neuen Feldern)
    createRewardsFromForm($db, $customerId, $campaignId, $_POST, $data['plan']);
    
    // E-Mail-Sequenzen aktivieren
    activateEmailSequences($db, $customerId);
    
    // Willkommens-E-Mail in Queue
    $db->insert('email_queue', [
        'customer_id' => $customerId,
        'recipient_email' => $data['email'],
        'recipient_name' => $data['contact_name'],
        'subject' => 'Willkommen bei Leadbusiness – Ihr Empfehlungsprogramm ist bereit!',
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

function handleLogoUpload($file, $subdomain) {
    $uploadDir = __DIR__ . '/../uploads/logos/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) throw new Exception('Ungültiges Bildformat');
    if ($file['size'] > 2 * 1024 * 1024) throw new Exception('Logo zu groß. Maximal 2MB.');
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $subdomain . '-logo-' . time() . '.' . $extension;
    
    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
        throw new Exception('Fehler beim Hochladen des Logos');
    }
    
    return '/uploads/logos/' . $filename;
}

function handleBackgroundUpload($file, $subdomain) {
    $uploadDir = __DIR__ . '/../uploads/backgrounds/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) throw new Exception('Ungültiges Bildformat');
    if ($file['size'] > 5 * 1024 * 1024) throw new Exception('Hintergrundbild zu groß. Maximal 5MB.');
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $subdomain . '-background-' . time() . '.' . $extension;
    
    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
        throw new Exception('Fehler beim Hochladen des Hintergrundbildes');
    }
    
    return '/uploads/backgrounds/' . $filename;
}

/**
 * Erstellt Belohnungen aus dem Onboarding-Formular
 * Speichert alle typspezifischen Felder inkl. URLs für E-Mail-Platzhalter
 */
function createRewardsFromForm($db, $customerId, $campaignId, $formData, $plan) {
    // Professional kann bis zu 10 Stufen haben, Starter nur 3
    $maxLevels = ($plan === 'professional' || $plan === 'enterprise') ? 10 : 3;
    
    for ($i = 1; $i <= $maxLevels; $i++) {
        $threshold = intval($formData["reward_{$i}_threshold"] ?? 0);
        $type = $formData["reward_{$i}_type"] ?? '';
        $description = trim($formData["reward_{$i}_description"] ?? '');
        
        // Nur speichern wenn Threshold und Typ vorhanden
        if ($threshold > 0 && !empty($type)) {
            
            // Basis-Daten für alle Typen
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
            
            // Typspezifische Felder - alle URL-Felder werden validiert
            switch ($type) {
                case 'discount':
                    // Rabatt in Prozent + Einlöse-URL
                    $rewardData['discount_percent'] = intval($formData["reward_{$i}_discount_percent"] ?? 0);
                    $rewardData['redeem_url'] = validateUrl($formData["reward_{$i}_discount_url"] ?? '');
                    break;
                    
                case 'coupon_code':
                    // Gutschein-Code, Gültigkeit und Einlöse-URL
                    $rewardData['coupon_code'] = trim($formData["reward_{$i}_coupon_code"] ?? '');
                    $rewardData['coupon_validity_days'] = intval($formData["reward_{$i}_coupon_validity"] ?? 30);
                    $rewardData['redeem_url'] = validateUrl($formData["reward_{$i}_coupon_url"] ?? '');
                    break;
                    
                case 'digital_download':
                    // Download-URL (erforderlich)
                    $rewardData['download_file_url'] = validateUrl($formData["reward_{$i}_download_url"] ?? '');
                    break;
                    
                case 'voucher':
                    // Wertgutschein in Euro + Einlöse-URL
                    $amount = floatval($formData["reward_{$i}_voucher_amount"] ?? 0);
                    $rewardData['voucher_amount'] = $amount > 0 ? $amount : null;
                    $rewardData['redeem_url'] = validateUrl($formData["reward_{$i}_voucher_url"] ?? '');
                    break;
                    
                case 'free_product':
                    // Adressabfrage + Bestell-URL
                    $rewardData['requires_address'] = isset($formData["reward_{$i}_requires_address"]) ? 1 : 0;
                    $rewardData['product_url'] = validateUrl($formData["reward_{$i}_product_url"] ?? '');
                    break;
                    
                case 'free_service':
                    // Buchungs-URL
                    $rewardData['service_url'] = validateUrl($formData["reward_{$i}_service_url"] ?? '');
                    break;
                    
                // ==================== PROFESSIONAL BELOHNUNGEN ====================
                
                case 'video_course':
                    // Video-Kurs URL, Zugangscode und Gültigkeit
                    $rewardData['video_url'] = validateUrl($formData["reward_{$i}_video_url"] ?? '');
                    $rewardData['video_access_code'] = trim($formData["reward_{$i}_video_access_code"] ?? '');
                    $rewardData['video_validity_days'] = intval($formData["reward_{$i}_video_validity"] ?? 365);
                    break;
                    
                case 'coaching_session':
                    // Coaching-Dauer, -Art und Buchungslink
                    $rewardData['coaching_duration'] = intval($formData["reward_{$i}_coaching_duration"] ?? 30);
                    $rewardData['coaching_type'] = $formData["reward_{$i}_coaching_type"] ?? 'video_call';
                    $rewardData['coaching_booking_url'] = validateUrl($formData["reward_{$i}_coaching_booking_url"] ?? '');
                    break;
                    
                case 'webinar_access':
                    // Webinar-URL, Datum und Uhrzeit
                    $rewardData['webinar_url'] = validateUrl($formData["reward_{$i}_webinar_url"] ?? '');
                    $rewardData['webinar_date'] = !empty($formData["reward_{$i}_webinar_date"]) 
                        ? $formData["reward_{$i}_webinar_date"] : null;
                    $rewardData['webinar_time'] = !empty($formData["reward_{$i}_webinar_time"]) 
                        ? $formData["reward_{$i}_webinar_time"] : null;
                    break;
                    
                case 'exclusive_content':
                    // Exklusiver Inhalt URL und Typ
                    $rewardData['exclusive_url'] = validateUrl($formData["reward_{$i}_exclusive_url"] ?? '');
                    $rewardData['exclusive_type'] = $formData["reward_{$i}_exclusive_type"] ?? 'ebook';
                    break;
                    
                case 'affiliate_commission':
                    // Provision, Max. Betrag und Produkt
                    $rewardData['affiliate_percent'] = intval($formData["reward_{$i}_affiliate_percent"] ?? 0);
                    $maxAmount = floatval($formData["reward_{$i}_affiliate_max"] ?? 0);
                    $rewardData['affiliate_max_amount'] = $maxAmount > 0 ? $maxAmount : null;
                    $rewardData['affiliate_product'] = trim($formData["reward_{$i}_affiliate_product"] ?? '');
                    break;
                    
                case 'cash_bonus':
                    // Bar-Auszahlung Betrag und Methode
                    $cashAmount = floatval($formData["reward_{$i}_cash_amount"] ?? 0);
                    $rewardData['cash_amount'] = $cashAmount > 0 ? $cashAmount : null;
                    $rewardData['cash_method'] = $formData["reward_{$i}_cash_method"] ?? 'bank_transfer';
                    break;
                    
                case 'membership_upgrade':
                    // Membership Level, Dauer und URL
                    $rewardData['membership_level'] = trim($formData["reward_{$i}_membership_level"] ?? '');
                    $rewardData['membership_duration'] = $formData["reward_{$i}_membership_duration"] ?? '12';
                    $rewardData['membership_url'] = validateUrl($formData["reward_{$i}_membership_url"] ?? '');
                    break;
                    
                case 'event_ticket':
                    // Event-Name, Datum, Ort und URL
                    $rewardData['event_name'] = trim($formData["reward_{$i}_event_name"] ?? '');
                    $rewardData['event_date'] = !empty($formData["reward_{$i}_event_date"]) 
                        ? $formData["reward_{$i}_event_date"] : null;
                    $rewardData['event_location'] = trim($formData["reward_{$i}_event_location"] ?? '');
                    $rewardData['event_url'] = validateUrl($formData["reward_{$i}_event_url"] ?? '');
                    break;
            }
            
            $db->insert('rewards', $rewardData);
        }
    }
}

/**
 * Validiert eine URL und gibt sie zurück oder null wenn ungültig/leer
 */
function validateUrl($url) {
    $url = trim($url);
    if (empty($url)) return null;
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        return $url;
    }
    return null;
}

/**
 * Gibt einen Standard-Titel für einen Belohnungstyp zurück
 */
function getDefaultRewardTitle($type) {
    $titles = [
        'discount' => 'Rabatt',
        'coupon_code' => 'Gutschein-Code',
        'free_product' => 'Gratis-Produkt',
        'free_service' => 'Gratis-Service',
        'digital_download' => 'Digital-Download',
        'voucher' => 'Wertgutschein',
        'video_course' => 'Video-Kurs',
        'coaching_session' => 'Coaching-Session',
        'webinar_access' => 'Webinar-Zugang',
        'exclusive_content' => 'Exklusiver Inhalt',
        'affiliate_commission' => 'Affiliate-Provision',
        'cash_bonus' => 'Bar-Auszahlung',
        'membership_upgrade' => 'Membership-Upgrade',
        'event_ticket' => 'Event-Ticket'
    ];
    return $titles[$type] ?? 'Belohnung';
}

function activateEmailSequences($db, $customerId) {
    // Standard-Sequenzen nutzen
}
