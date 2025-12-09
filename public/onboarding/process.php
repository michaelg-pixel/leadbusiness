<?php
/**
 * Leadbusiness - Onboarding Process
 * 
 * Verarbeitet das Onboarding-Formular und richtet den Kunden-Account ein
 */

// KORRIGIERTE PFADE - von /public/onboarding/ zwei Ebenen hoch
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/settings.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/helpers.php';

// Session starten
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Nur POST-Requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Ungültige Anfrage', 405);
}

try {
    $db = Database::getInstance();
    
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
    
    // Prüfen ob E-Mail bereits existiert (ohne Token)
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
        'onboarding_token' => null, // Token löschen
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    // Kunde aktualisieren oder erstellen
    if ($existingEmail && $existingEmail['onboarding_token'] === $data['token']) {
        // Bestehenden Kunden aktualisieren (kam von Digistore24)
        $customerId = $existingEmail['id'];
        $db->update('customers', $customerData, 'id = ?', [$customerId]);
    } else if (!$existingEmail) {
        // Neuen Kunden erstellen (direktes Onboarding)
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
    createRewardsFromForm($db, $customerId, $campaignId, $_POST);
    
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
    
    // Erfolg
    jsonSuccess([
        'customer_id' => $customerId,
        'subdomain' => $data['subdomain'],
        'redirect' => '/dashboard?welcome=1'
    ], 'Einrichtung erfolgreich abgeschlossen!');
    
} catch (Exception $e) {
    error_log('Onboarding Error: ' . $e->getMessage());
    jsonError('Ein Fehler ist aufgetreten: ' . $e->getMessage(), 500);
}

/**
 * Validierung der Onboarding-Daten
 */
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
    
    // E-Mail Format
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Ungültige E-Mail-Adresse';
    }
    
    // Passwort
    if (!empty($data['password'])) {
        if (strlen($data['password']) < 8) {
            $errors[] = 'Passwort muss mindestens 8 Zeichen lang sein';
        }
        if ($data['password'] !== $data['password_confirm']) {
            $errors[] = 'Passwörter stimmen nicht überein';
        }
    }
    
    // Subdomain Format
    if (!empty($data['subdomain'])) {
        if (!preg_match('/^[a-z0-9-]{3,50}$/', $data['subdomain'])) {
            $errors[] = 'Subdomain darf nur Kleinbuchstaben, Zahlen und Bindestriche enthalten (3-50 Zeichen)';
        }
        
        // Reservierte Subdomains
        $reserved = ['www', 'admin', 'api', 'app', 'dashboard', 'mail', 'smtp', 'ftp', 'test', 'dev', 'staging'];
        if (in_array($data['subdomain'], $reserved)) {
            $errors[] = 'Diese Subdomain ist reserviert';
        }
    }
    
    // PLZ Format (Deutschland)
    if (!empty($data['address_zip']) && !preg_match('/^[0-9]{5}$/', $data['address_zip'])) {
        $errors[] = 'Ungültige PLZ (5 Ziffern)';
    }
    
    // Plan
    if (!in_array($data['plan'], ['starter', 'professional', 'enterprise'])) {
        $errors[] = 'Ungültiger Tarif';
    }
    
    return $errors;
}

/**
 * Logo hochladen
 * KORRIGIERTER PFAD: Von /public/onboarding/ eine Ebene hoch zu /public/uploads/
 */
function handleLogoUpload($file, $subdomain) {
    $uploadDir = __DIR__ . '/../uploads/logos/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Validierung
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Ungültiges Bildformat. Erlaubt: JPG, PNG, GIF, WebP');
    }
    
    if ($file['size'] > 2 * 1024 * 1024) {
        throw new Exception('Logo zu groß. Maximal 2MB erlaubt.');
    }
    
    // Dateiname generieren
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $subdomain . '-logo-' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Fehler beim Hochladen des Logos');
    }
    
    return '/uploads/logos/' . $filename;
}

/**
 * Custom Background hochladen
 * KORRIGIERTER PFAD: Von /public/onboarding/ eine Ebene hoch zu /public/uploads/
 */
function handleBackgroundUpload($file, $subdomain) {
    $uploadDir = __DIR__ . '/../uploads/backgrounds/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Validierung
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Ungültiges Bildformat. Erlaubt: JPG, PNG, WebP');
    }
    
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception('Hintergrundbild zu groß. Maximal 5MB erlaubt.');
    }
    
    // Dateiname generieren
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $subdomain . '-background-' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Fehler beim Hochladen des Hintergrundbildes');
    }
    
    return '/uploads/backgrounds/' . $filename;
}

/**
 * Belohnungen aus Formular erstellen
 */
function createRewardsFromForm($db, $customerId, $campaignId, $formData) {
    for ($i = 1; $i <= 5; $i++) {
        $threshold = intval($formData["reward_{$i}_threshold"] ?? 0);
        $type = $formData["reward_{$i}_type"] ?? '';
        $description = $formData["reward_{$i}_description"] ?? '';
        
        if ($threshold > 0 && !empty($type) && !empty($description)) {
            $db->insert('rewards', [
                'customer_id' => $customerId,
                'campaign_id' => $campaignId,
                'level' => $i,
                'required_conversions' => $threshold,
                'reward_type' => $type,
                'reward_value' => $description,
                'description' => $description,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}

/**
 * E-Mail-Sequenzen für Kunden aktivieren
 */
function activateEmailSequences($db, $customerId) {
    // Standard-Sequenzen kopieren und aktivieren
    $sequences = $db->fetchAll(
        "SELECT * FROM email_sequences WHERE is_default = 1 AND is_active = 1"
    );
    
    // Hier könnten kundenspezifische Sequenzen erstellt werden
    // Für jetzt nutzen wir die globalen Standard-Sequenzen
}
