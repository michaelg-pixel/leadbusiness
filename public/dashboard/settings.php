<?php
/**
 * Leadbusiness - Einstellungen
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/helpers.php';

$auth = new Auth();
if (!$auth->isLoggedIn() || $auth->getUserType() !== 'customer') {
    redirect('/dashboard/login.php');
}

$customer = $auth->getCurrentCustomer();
$customerId = $customer['id'];
$db = Database::getInstance();

$message = '';
$error = '';

// POST: Einstellungen speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $companyName = trim($_POST['company_name'] ?? '');
        $contactName = trim($_POST['contact_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $website = trim($_POST['website'] ?? '');
        $emailSenderName = trim($_POST['email_sender_name'] ?? '');
        
        if (empty($companyName) || empty($contactName)) {
            $error = 'Firmenname und Ansprechpartner sind Pflichtfelder.';
        } else {
            $db->update('customers', [
                'company_name' => $companyName,
                'contact_name' => $contactName,
                'phone' => $phone,
                'website' => $website,
                'email_sender_name' => $emailSenderName ?: $companyName,
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = ?', [$customerId]);
            
            $message = 'Profil aktualisiert!';
            $customer = $db->fetch("SELECT * FROM customers WHERE id = ?", [$customerId]);
        }
    }
    
    if ($action === 'update_address') {
        $street = trim($_POST['address_street'] ?? '');
        $zip = trim($_POST['address_zip'] ?? '');
        $city = trim($_POST['address_city'] ?? '');
        $taxId = trim($_POST['tax_id'] ?? '');
        
        if (empty($street) || empty($zip) || empty($city)) {
            $error = 'Adresse ist unvollständig.';
        } else {
            $db->update('customers', [
                'address_street' => $street,
                'address_zip' => $zip,
                'address_city' => $city,
                'tax_id' => $taxId,
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = ?', [$customerId]);
            
            $message = 'Adresse aktualisiert!';
            $customer = $db->fetch("SELECT * FROM customers WHERE id = ?", [$customerId]);
        }
    }
    
    if ($action === 'update_features') {
        $liveCounter = isset($_POST['live_counter_enabled']);
        $leaderboard = isset($_POST['leaderboard_enabled']);
        $weeklyDigest = isset($_POST['weekly_digest_enabled']);
        
        $db->update('customers', [
            'live_counter_enabled' => $liveCounter ? 1 : 0,
            'leaderboard_enabled' => $leaderboard ? 1 : 0,
            'weekly_digest_enabled' => ($weeklyDigest && $customer['plan'] !== 'starter') ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$customerId]);
        
        $message = 'Einstellungen gespeichert!';
        $customer = $db->fetch("SELECT * FROM customers WHERE id = ?", [$customerId]);
    }
    
    if ($action === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (!password_verify($currentPassword, $customer['password_hash'])) {
            $error = 'Aktuelles Passwort ist falsch.';
        } elseif (strlen($newPassword) < 8) {
            $error = 'Neues Passwort muss mindestens 8 Zeichen haben.';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'Passwörter stimmen nicht überein.';
        } else {
            $db->update('customers', [
                'password_hash' => password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]),
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = ?', [$customerId]);
            
            $message = 'Passwort geändert!';
        }
    }
}

$pageTitle = 'Einstellungen';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Leadbusiness</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50">
    
    <div class="flex h-screen">
        
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r hidden lg:block">
            <div class="p-6 border-b">
                <a href="/" class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-share-nodes text-white"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900">Leadbusiness</span>
                </a>
            </div>
            
            <nav class="p-4 space-y-1">
                <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-xl">
                    <i class="fas fa-home w-5"></i><span>Übersicht</span>
                </a>
                <a href="/dashboard/leads.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-xl">
                    <i class="fas fa-users w-5"></i><span>Empfehler</span>
                </a>
                <a href="/dashboard/rewards.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-xl">
                    <i class="fas fa-gift w-5"></i><span>Belohnungen</span>
                </a>
                <a href="/dashboard/design.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-xl">
                    <i class="fas fa-palette w-5"></i><span>Design</span>
                </a>
                <a href="/dashboard/settings.php" class="flex items-center gap-3 px-4 py-3 text-indigo-600 bg-indigo-50 rounded-xl font-medium">
                    <i class="fas fa-cog w-5"></i><span>Einstellungen</span>
                </a>
            </nav>
            
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t bg-white w-64">
                <a href="/dashboard/logout.php" class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-sign-out-alt"></i>Abmelden
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            
            <header class="bg-white border-b px-6 py-4">
                <h1 class="text-2xl font-bold text-gray-900">Einstellungen</h1>
            </header>
            
            <div class="p-6 max-w-3xl">
                
                <?php if ($message): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6">
                    <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($message) ?>
                </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>
                
                <!-- Plan Info -->
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 text-white mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-lg"><?= ucfirst($customer['plan']) ?>-Plan</h3>
                            <p class="text-white/80 text-sm">
                                <?php if ($customer['subscription_ends_at']): ?>
                                Aktiv bis: <?= date('d.m.Y', strtotime($customer['subscription_ends_at'])) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        <?php if ($customer['plan'] === 'starter'): ?>
                        <a href="/preise" class="px-4 py-2 bg-white text-indigo-600 rounded-lg font-medium hover:bg-gray-100">
                            Upgrade
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Profil -->
                <div class="bg-white rounded-xl p-6 shadow-sm mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4">
                        <i class="fas fa-building mr-2 text-gray-400"></i>Unternehmensdaten
                    </h3>
                    
                    <form method="POST">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Firmenname *</label>
                                <input type="text" name="company_name" value="<?= htmlspecialchars($customer['company_name']) ?>" required
                                       class="w-full px-4 py-2 border rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ansprechpartner *</label>
                                <input type="text" name="contact_name" value="<?= htmlspecialchars($customer['contact_name']) ?>" required
                                       class="w-full px-4 py-2 border rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                                <input type="text" name="phone" value="<?= htmlspecialchars($customer['phone'] ?? '') ?>"
                                       class="w-full px-4 py-2 border rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                                <input type="url" name="website" value="<?= htmlspecialchars($customer['website'] ?? '') ?>"
                                       class="w-full px-4 py-2 border rounded-lg">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">E-Mail-Absendername</label>
                                <input type="text" name="email_sender_name" value="<?= htmlspecialchars($customer['email_sender_name'] ?? '') ?>"
                                       class="w-full px-4 py-2 border rounded-lg" placeholder="z.B. Zahnarztpraxis Dr. Müller">
                                <p class="text-xs text-gray-500 mt-1">Dieser Name erscheint als Absender bei E-Mails an Ihre Empfehler.</p>
                            </div>
                        </div>
                        
                        <button type="submit" class="mt-4 px-6 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600">
                            Speichern
                        </button>
                    </form>
                </div>
                
                <!-- Impressum -->
                <div class="bg-white rounded-xl p-6 shadow-sm mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4">
                        <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>Impressumsadresse
                    </h3>
                    
                    <form method="POST">
                        <input type="hidden" name="action" value="update_address">
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Straße & Hausnummer *</label>
                                <input type="text" name="address_street" value="<?= htmlspecialchars($customer['address_street']) ?>" required
                                       class="w-full px-4 py-2 border rounded-lg">
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">PLZ *</label>
                                    <input type="text" name="address_zip" value="<?= htmlspecialchars($customer['address_zip']) ?>" required
                                           class="w-full px-4 py-2 border rounded-lg">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Stadt *</label>
                                    <input type="text" name="address_city" value="<?= htmlspecialchars($customer['address_city']) ?>" required
                                           class="w-full px-4 py-2 border rounded-lg">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">USt-IdNr.</label>
                                <input type="text" name="tax_id" value="<?= htmlspecialchars($customer['tax_id'] ?? '') ?>"
                                       class="w-full px-4 py-2 border rounded-lg" placeholder="DE123456789">
                            </div>
                        </div>
                        
                        <button type="submit" class="mt-4 px-6 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600">
                            Speichern
                        </button>
                    </form>
                </div>
                
                <!-- Features -->
                <div class="bg-white rounded-xl p-6 shadow-sm mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4">
                        <i class="fas fa-sliders-h mr-2 text-gray-400"></i>Funktionen
                    </h3>
                    
                    <form method="POST">
                        <input type="hidden" name="action" value="update_features">
                        
                        <div class="space-y-4">
                            <label class="flex items-center justify-between p-4 bg-gray-50 rounded-lg cursor-pointer">
                                <div>
                                    <div class="font-medium text-gray-900">Live-Counter</div>
                                    <div class="text-sm text-gray-500">"47 Personen nehmen bereits teil"</div>
                                </div>
                                <input type="checkbox" name="live_counter_enabled" <?= $customer['live_counter_enabled'] ? 'checked' : '' ?>
                                       class="w-5 h-5 text-indigo-500 rounded">
                            </label>
                            
                            <label class="flex items-center justify-between p-4 bg-gray-50 rounded-lg cursor-pointer">
                                <div>
                                    <div class="font-medium text-gray-900">Leaderboard</div>
                                    <div class="text-sm text-gray-500">Top Empfehler öffentlich anzeigen</div>
                                </div>
                                <input type="checkbox" name="leaderboard_enabled" <?= $customer['leaderboard_enabled'] ? 'checked' : '' ?>
                                       class="w-5 h-5 text-indigo-500 rounded">
                            </label>
                            
                            <label class="flex items-center justify-between p-4 rounded-lg cursor-pointer <?= $customer['plan'] === 'starter' ? 'bg-gray-100 opacity-60' : 'bg-gray-50' ?>">
                                <div>
                                    <div class="font-medium text-gray-900">
                                        Wöchentlicher Digest
                                        <?php if ($customer['plan'] === 'starter'): ?>
                                        <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full ml-2">Pro</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-sm text-gray-500">Wöchentliche E-Mail mit Statistiken an aktive Empfehler</div>
                                </div>
                                <input type="checkbox" name="weekly_digest_enabled" 
                                       <?= $customer['weekly_digest_enabled'] ? 'checked' : '' ?>
                                       <?= $customer['plan'] === 'starter' ? 'disabled' : '' ?>
                                       class="w-5 h-5 text-indigo-500 rounded">
                            </label>
                        </div>
                        
                        <button type="submit" class="mt-4 px-6 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600">
                            Speichern
                        </button>
                    </form>
                </div>
                
                <!-- Passwort -->
                <div class="bg-white rounded-xl p-6 shadow-sm mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4">
                        <i class="fas fa-lock mr-2 text-gray-400"></i>Passwort ändern
                    </h3>
                    
                    <form method="POST">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Aktuelles Passwort</label>
                                <input type="password" name="current_password" required
                                       class="w-full px-4 py-2 border rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Neues Passwort</label>
                                <input type="password" name="new_password" required minlength="8"
                                       class="w-full px-4 py-2 border rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Passwort bestätigen</label>
                                <input type="password" name="confirm_password" required
                                       class="w-full px-4 py-2 border rounded-lg">
                            </div>
                        </div>
                        
                        <button type="submit" class="mt-4 px-6 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600">
                            Passwort ändern
                        </button>
                    </form>
                </div>
                
                <!-- Subdomain Info -->
                <div class="bg-gray-100 rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Ihre Empfehlungsseite</h3>
                    <p class="text-gray-600 mb-2">
                        <i class="fas fa-globe mr-2"></i>
                        <a href="https://<?= htmlspecialchars($customer['subdomain']) ?>.empfohlen.de" target="_blank" class="text-indigo-500 hover:underline">
                            <?= htmlspecialchars($customer['subdomain']) ?>.empfohlen.de
                        </a>
                    </p>
                    <p class="text-sm text-gray-500">Die Subdomain kann nach der Einrichtung nicht mehr geändert werden.</p>
                </div>
                
            </div>
        </main>
    </div>
    
</body>
</html>
