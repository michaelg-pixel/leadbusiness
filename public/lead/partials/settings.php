<?php
/**
 * Lead Dashboard - Settings Tab
 * Einstellungen für Profil, Passwort und Benachrichtigungen
 */

// Erfolgs-/Fehlermeldungen
$message = $_SESSION['settings_message'] ?? null;
$messageType = $_SESSION['settings_message_type'] ?? 'success';
unset($_SESSION['settings_message'], $_SESSION['settings_message_type']);
?>

<div class="max-w-2xl space-y-6">
    
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Einstellungen</h1>
        <p class="text-gray-500">Verwalten Sie Ihr Profil und Ihre Benachrichtigungen</p>
    </div>
    
    <?php if ($message): ?>
    <div class="p-4 rounded-xl <?= $messageType === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' ?>">
        <i class="fas <?= $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> mr-2"></i>
        <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>
    
    <!-- Profil -->
    <div class="card p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-user text-gray-400"></i>
            Profil
        </h2>
        
        <form action="/lead/api/update-profile.php" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($lead['name'] ?? '') ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">E-Mail</label>
                <input type="email" value="<?= htmlspecialchars($lead['email']) ?>" disabled
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-500">
                <p class="text-xs text-gray-500 mt-1">Die E-Mail-Adresse kann nicht geändert werden.</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefon (optional)</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($lead['phone'] ?? '') ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary">
            </div>
            
            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-xl hover:opacity-90 transition">
                Speichern
            </button>
        </form>
    </div>
    
    <!-- Passwort -->
    <div class="card p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-lock text-gray-400"></i>
            Passwort
        </h2>
        
        <?php if (empty($lead['password_hash'])): ?>
        <p class="text-gray-600 mb-4">
            Sie haben noch kein Passwort festgelegt. Mit einem Passwort können Sie sich ohne E-Mail-Link anmelden.
        </p>
        <?php else: ?>
        <p class="text-gray-600 mb-4">
            <i class="fas fa-check-circle text-green-500 mr-2"></i>
            Ein Passwort ist bereits eingerichtet.
        </p>
        <?php endif; ?>
        
        <form action="/lead/api/update-password.php" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            
            <?php if (!empty($lead['password_hash'])): ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Aktuelles Passwort</label>
                <input type="password" name="current_password" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary">
            </div>
            <?php endif; ?>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <?= empty($lead['password_hash']) ? 'Neues Passwort' : 'Neues Passwort' ?>
                </label>
                <input type="password" name="new_password" minlength="8"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <p class="text-xs text-gray-500 mt-1">Mindestens 8 Zeichen</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Passwort bestätigen</label>
                <input type="password" name="confirm_password" minlength="8"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary">
            </div>
            
            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-xl hover:opacity-90 transition">
                <?= empty($lead['password_hash']) ? 'Passwort festlegen' : 'Passwort ändern' ?>
            </button>
        </form>
    </div>
    
    <!-- Benachrichtigungen -->
    <div class="card p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-bell text-gray-400"></i>
            E-Mail-Benachrichtigungen
        </h2>
        
        <form action="/lead/api/update-notifications.php" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            
            <label class="flex items-center justify-between p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition">
                <div>
                    <div class="font-medium text-gray-900">Neue Empfehlung</div>
                    <div class="text-sm text-gray-500">Benachrichtigung wenn sich jemand über Ihren Link anmeldet</div>
                </div>
                <input type="checkbox" name="notification_new_conversion" value="1"
                       <?= ($lead['notification_new_conversion'] ?? 1) ? 'checked' : '' ?>
                       class="w-5 h-5 text-primary rounded focus:ring-primary">
            </label>
            
            <label class="flex items-center justify-between p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition">
                <div>
                    <div class="font-medium text-gray-900">Belohnung freigeschaltet</div>
                    <div class="text-sm text-gray-500">E-Mail wenn Sie eine neue Belohnungsstufe erreichen</div>
                </div>
                <input type="checkbox" name="notification_reward_unlocked" value="1"
                       <?= ($lead['notification_reward_unlocked'] ?? 1) ? 'checked' : '' ?>
                       class="w-5 h-5 text-primary rounded focus:ring-primary">
            </label>
            
            <label class="flex items-center justify-between p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition">
                <div>
                    <div class="font-medium text-gray-900">Wöchentliche Zusammenfassung</div>
                    <div class="text-sm text-gray-500">Übersicht Ihrer Statistiken jeden Montag</div>
                </div>
                <input type="checkbox" name="notification_weekly_summary" value="1"
                       <?= ($lead['notification_weekly_summary'] ?? 0) ? 'checked' : '' ?>
                       class="w-5 h-5 text-primary rounded focus:ring-primary">
            </label>
            
            <label class="flex items-center justify-between p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition">
                <div>
                    <div class="font-medium text-gray-900">Tipps & Tricks</div>
                    <div class="text-sm text-gray-500">Gelegentliche E-Mails mit Tipps für mehr Empfehlungen</div>
                </div>
                <input type="checkbox" name="notification_tips" value="1"
                       <?= ($lead['notification_tips'] ?? 1) ? 'checked' : '' ?>
                       class="w-5 h-5 text-primary rounded focus:ring-primary">
            </label>
            
            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-xl hover:opacity-90 transition">
                Einstellungen speichern
            </button>
        </form>
    </div>
    
    <!-- Account-Info -->
    <div class="card p-6 bg-gray-50">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-info-circle text-gray-400"></i>
            Account-Informationen
        </h2>
        
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Mitglied seit</dt>
                <dd class="font-medium text-gray-900"><?= date('d.m.Y', strtotime($lead['created_at'])) ?></dd>
            </div>
            <div>
                <dt class="text-gray-500">Letzter Login</dt>
                <dd class="font-medium text-gray-900">
                    <?= $lead['last_login_at'] ? date('d.m.Y H:i', strtotime($lead['last_login_at'])) : 'Nie' ?>
                </dd>
            </div>
            <div>
                <dt class="text-gray-500">Empfehlungscode</dt>
                <dd class="font-mono font-medium text-primary"><?= htmlspecialchars($lead['referral_code']) ?></dd>
            </div>
            <div>
                <dt class="text-gray-500">Status</dt>
                <dd class="font-medium">
                    <?php if ($lead['status'] === 'active'): ?>
                    <span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>Aktiv</span>
                    <?php else: ?>
                    <span class="text-yellow-600"><?= htmlspecialchars($lead['status']) ?></span>
                    <?php endif; ?>
                </dd>
            </div>
        </dl>
    </div>
    
    <!-- Datenschutz-Link -->
    <div class="text-center text-sm text-gray-500">
        <a href="/r/datenschutz.php" class="hover:text-primary">Datenschutzerklärung</a>
        <span class="mx-2">•</span>
        <a href="/lead/logout.php" class="hover:text-red-600">Abmelden</a>
    </div>
    
</div>
