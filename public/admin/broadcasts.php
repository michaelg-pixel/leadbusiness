<?php
/**
 * Admin E-Mail Broadcasts
 * Leadbusiness - E-Mail Marketing System
 */

require_once __DIR__ . '/../../includes/init.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$db = db();
$pageTitle = 'E-Mail Broadcasts';

// Aktionen verarbeiten
if (isPost()) {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'save_draft':
        case 'schedule':
        case 'send_now':
            $broadcastId = intval($_POST['broadcast_id'] ?? 0);
            $name = sanitize($_POST['name'] ?? '');
            $subject = sanitize($_POST['subject'] ?? '');
            $bodyHtml = $_POST['body_html'] ?? '';
            $fromEmail = sanitize($_POST['from_email'] ?? 'info@empfehlungen.cloud');
            $fromName = sanitize($_POST['from_name'] ?? 'Leadbusiness');
            $replyTo = sanitize($_POST['reply_to'] ?? '');
            
            $targetType = sanitize($_POST['target_type'] ?? 'all');
            $targetFilters = [];
            $targetTagIds = [];
            $targetCustomerIds = [];
            
            // Filter sammeln
            if ($targetType === 'filter') {
                if (!empty($_POST['filter_plan']) && $_POST['filter_plan'] !== 'all') {
                    $targetFilters['plan'] = $_POST['filter_plan'];
                }
                if (!empty($_POST['filter_status']) && $_POST['filter_status'] !== 'all') {
                    $targetFilters['status'] = $_POST['filter_status'];
                }
                if (!empty($_POST['filter_industry']) && $_POST['filter_industry'] !== 'all') {
                    $targetFilters['industry'] = $_POST['filter_industry'];
                }
            } elseif ($targetType === 'tags') {
                $targetTagIds = array_map('intval', $_POST['target_tags'] ?? []);
            } elseif ($targetType === 'manual') {
                $targetCustomerIds = array_map('intval', $_POST['target_customers'] ?? []);
            }
            
            // Status und Zeitplanung
            $status = 'draft';
            $scheduledFor = null;
            
            if ($action === 'schedule' && !empty($_POST['scheduled_for'])) {
                $status = 'scheduled';
                $scheduledFor = $_POST['scheduled_for'];
            } elseif ($action === 'send_now') {
                $status = 'sending';
            }
            
            // Empfänger zählen
            $recipientCount = countRecipients($db, $targetType, $targetFilters, $targetTagIds, $targetCustomerIds);
            
            $data = [
                'name' => $name,
                'subject' => $subject,
                'body_html' => $bodyHtml,
                'from_email' => $fromEmail,
                'from_name' => $fromName,
                'reply_to' => $replyTo,
                'target_type' => $targetType,
                'target_filters' => json_encode($targetFilters),
                'target_tag_ids' => json_encode($targetTagIds),
                'target_customer_ids' => json_encode($targetCustomerIds),
                'status' => $status,
                'scheduled_for' => $scheduledFor,
                'total_recipients' => $recipientCount,
                'created_by' => $_SESSION['admin_id']
            ];
            
            if ($broadcastId) {
                // Update
                $setClauses = [];
                $params = [];
                foreach ($data as $key => $value) {
                    $setClauses[] = "$key = ?";
                    $params[] = $value;
                }
                $params[] = $broadcastId;
                $db->execute("UPDATE admin_broadcasts SET " . implode(', ', $setClauses) . " WHERE id = ?", $params);
            } else {
                // Insert
                $columns = implode(', ', array_keys($data));
                $placeholders = implode(', ', array_fill(0, count($data), '?'));
                $db->execute("INSERT INTO admin_broadcasts ($columns) VALUES ($placeholders)", array_values($data));
                $broadcastId = $db->lastInsertId();
            }
            
            // Bei "send_now" - Empfänger erstellen und Versand starten
            if ($action === 'send_now') {
                createRecipients($db, $broadcastId, $targetType, $targetFilters, $targetTagIds, $targetCustomerIds);
                $db->execute("UPDATE admin_broadcasts SET started_at = NOW() WHERE id = ?", [$broadcastId]);
                $_SESSION['flash_success'] = "Broadcast wird an $recipientCount Empfänger gesendet...";
            } elseif ($action === 'schedule') {
                createRecipients($db, $broadcastId, $targetType, $targetFilters, $targetTagIds, $targetCustomerIds);
                $_SESSION['flash_success'] = "Broadcast geplant für " . date('d.m.Y H:i', strtotime($scheduledFor));
            } else {
                $_SESSION['flash_success'] = 'Entwurf gespeichert.';
            }
            
            header('Location: /admin/broadcasts.php');
            exit;
            
        case 'delete':
            $broadcastId = intval($_POST['broadcast_id'] ?? 0);
            if ($broadcastId) {
                $db->execute("DELETE FROM admin_broadcasts WHERE id = ?", [$broadcastId]);
                $_SESSION['flash_success'] = 'Broadcast wurde gelöscht.';
            }
            break;
            
        case 'pause':
            $broadcastId = intval($_POST['broadcast_id'] ?? 0);
            if ($broadcastId) {
                $db->execute("UPDATE admin_broadcasts SET status = 'paused' WHERE id = ?", [$broadcastId]);
                $_SESSION['flash_success'] = 'Broadcast wurde pausiert.';
            }
            break;
            
        case 'resume':
            $broadcastId = intval($_POST['broadcast_id'] ?? 0);
            if ($broadcastId) {
                $db->execute("UPDATE admin_broadcasts SET status = 'sending' WHERE id = ?", [$broadcastId]);
                $_SESSION['flash_success'] = 'Broadcast wird fortgesetzt.';
            }
            break;
            
        case 'cancel':
            $broadcastId = intval($_POST['broadcast_id'] ?? 0);
            if ($broadcastId) {
                $db->execute("UPDATE admin_broadcasts SET status = 'cancelled' WHERE id = ?", [$broadcastId]);
                $_SESSION['flash_success'] = 'Broadcast wurde abgebrochen.';
            }
            break;
            
        case 'duplicate':
            $broadcastId = intval($_POST['broadcast_id'] ?? 0);
            if ($broadcastId) {
                $original = $db->fetch("SELECT * FROM admin_broadcasts WHERE id = ?", [$broadcastId]);
                if ($original) {
                    $db->execute("
                        INSERT INTO admin_broadcasts (name, subject, body_html, from_email, from_name, reply_to, target_type, target_filters, target_tag_ids, target_customer_ids, status, created_by)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'draft', ?)
                    ", [
                        $original['name'] . ' (Kopie)',
                        $original['subject'],
                        $original['body_html'],
                        $original['from_email'],
                        $original['from_name'],
                        $original['reply_to'],
                        $original['target_type'],
                        $original['target_filters'],
                        $original['target_tag_ids'],
                        $original['target_customer_ids'],
                        $_SESSION['admin_id']
                    ]);
                    $_SESSION['flash_success'] = 'Broadcast wurde dupliziert.';
                }
            }
            break;
    }
    
    header('Location: /admin/broadcasts.php');
    exit;
}

// Hilfsfunktionen
function countRecipients($db, $targetType, $filters, $tagIds, $customerIds) {
    $where = ["email NOT IN (SELECT email FROM admin_broadcast_unsubscribes)"];
    $params = [];
    
    if ($targetType === 'filter') {
        if (!empty($filters['plan'])) {
            $where[] = "plan = ?";
            $params[] = $filters['plan'];
        }
        if (!empty($filters['status'])) {
            $where[] = "subscription_status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['industry'])) {
            $where[] = "industry = ?";
            $params[] = $filters['industry'];
        }
    } elseif ($targetType === 'tags' && !empty($tagIds)) {
        $placeholders = implode(',', array_fill(0, count($tagIds), '?'));
        $where[] = "id IN (SELECT customer_id FROM customer_tag_assignments WHERE tag_id IN ($placeholders))";
        $params = array_merge($params, $tagIds);
    } elseif ($targetType === 'manual' && !empty($customerIds)) {
        $placeholders = implode(',', array_fill(0, count($customerIds), '?'));
        $where[] = "id IN ($placeholders)";
        $params = array_merge($params, $customerIds);
    }
    
    $whereClause = implode(' AND ', $where);
    return $db->fetchColumn("SELECT COUNT(*) FROM customers WHERE $whereClause", $params) ?? 0;
}

function createRecipients($db, $broadcastId, $targetType, $filters, $tagIds, $customerIds) {
    $where = ["email NOT IN (SELECT email FROM admin_broadcast_unsubscribes)"];
    $params = [];
    
    if ($targetType === 'filter') {
        if (!empty($filters['plan'])) {
            $where[] = "plan = ?";
            $params[] = $filters['plan'];
        }
        if (!empty($filters['status'])) {
            $where[] = "subscription_status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['industry'])) {
            $where[] = "industry = ?";
            $params[] = $filters['industry'];
        }
    } elseif ($targetType === 'tags' && !empty($tagIds)) {
        $placeholders = implode(',', array_fill(0, count($tagIds), '?'));
        $where[] = "id IN (SELECT customer_id FROM customer_tag_assignments WHERE tag_id IN ($placeholders))";
        $params = array_merge($params, $tagIds);
    } elseif ($targetType === 'manual' && !empty($customerIds)) {
        $placeholders = implode(',', array_fill(0, count($customerIds), '?'));
        $where[] = "id IN ($placeholders)";
        $params = array_merge($params, $customerIds);
    }
    
    $whereClause = implode(' AND ', $where);
    $customers = $db->fetchAll("SELECT id, email FROM customers WHERE $whereClause", $params);
    
    foreach ($customers as $c) {
        $db->execute(
            "INSERT IGNORE INTO admin_broadcast_recipients (broadcast_id, customer_id, email) VALUES (?, ?, ?)",
            [$broadcastId, $c['id'], $c['email']]
        );
    }
}

// Compose Mode?
$composeMode = isset($_GET['compose']) || isset($_GET['edit']);
$editBroadcast = null;

if (isset($_GET['edit'])) {
    $editBroadcast = $db->fetch("SELECT * FROM admin_broadcasts WHERE id = ?", [intval($_GET['edit'])]);
}

// Pre-select Tag from URL
$preselectedTag = intval($_GET['tag'] ?? 0);

// Filter
$filter = sanitize($_GET['filter'] ?? 'all');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;

$where = [];
$params = [];

if ($filter !== 'all') {
    $where[] = "status = ?";
    $params[] = $filter;
}

$whereClause = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);

// Statistiken
$stats = [
    'total' => $db->fetchColumn("SELECT COUNT(*) FROM admin_broadcasts") ?? 0,
    'draft' => $db->fetchColumn("SELECT COUNT(*) FROM admin_broadcasts WHERE status = 'draft'") ?? 0,
    'scheduled' => $db->fetchColumn("SELECT COUNT(*) FROM admin_broadcasts WHERE status = 'scheduled'") ?? 0,
    'sending' => $db->fetchColumn("SELECT COUNT(*) FROM admin_broadcasts WHERE status = 'sending'") ?? 0,
    'sent' => $db->fetchColumn("SELECT COUNT(*) FROM admin_broadcasts WHERE status = 'sent'") ?? 0,
    'total_sent' => $db->fetchColumn("SELECT COALESCE(SUM(sent_count), 0) FROM admin_broadcasts") ?? 0,
    'total_opened' => $db->fetchColumn("SELECT COALESCE(SUM(opened_count), 0) FROM admin_broadcasts") ?? 0,
    'total_clicked' => $db->fetchColumn("SELECT COALESCE(SUM(clicked_count), 0) FROM admin_broadcasts") ?? 0,
];

$stats['open_rate'] = $stats['total_sent'] > 0 ? round(($stats['total_opened'] / $stats['total_sent']) * 100, 1) : 0;
$stats['click_rate'] = $stats['total_opened'] > 0 ? round(($stats['total_clicked'] / $stats['total_opened']) * 100, 1) : 0;

// Broadcasts laden
$totalCount = $db->fetchColumn("SELECT COUNT(*) FROM admin_broadcasts $whereClause", $params);
$totalPages = ceil($totalCount / $perPage);
$offset = ($page - 1) * $perPage;

$broadcasts = $db->fetchAll("
    SELECT * FROM admin_broadcasts 
    $whereClause
    ORDER BY created_at DESC
    LIMIT $perPage OFFSET $offset
", $params);

// Tags und Kunden für Compose
$tags = $db->fetchAll("SELECT * FROM customer_tags ORDER BY name");
$customers = $db->fetchAll("SELECT id, company_name, email, plan, subscription_status, industry FROM customers ORDER BY company_name");
$industries = $db->fetchAll("SELECT DISTINCT industry FROM customers WHERE industry IS NOT NULL AND industry != '' ORDER BY industry");

// Unsubscribes
$unsubscribeCount = $db->fetchColumn("SELECT COUNT(*) FROM admin_broadcast_unsubscribes") ?? 0;

// Admin E-Mail für Test
$adminEmail = $db->fetchColumn("SELECT email FROM admin_users WHERE id = ?", [$_SESSION['admin_id']]) ?? '';

// Status Config
$statusConfig = [
    'draft' => ['label' => 'Entwurf', 'color' => 'slate', 'icon' => 'fa-file-alt'],
    'scheduled' => ['label' => 'Geplant', 'color' => 'blue', 'icon' => 'fa-clock'],
    'sending' => ['label' => 'Wird gesendet', 'color' => 'amber', 'icon' => 'fa-spinner fa-spin'],
    'sent' => ['label' => 'Gesendet', 'color' => 'green', 'icon' => 'fa-check-circle'],
    'paused' => ['label' => 'Pausiert', 'color' => 'orange', 'icon' => 'fa-pause-circle'],
    'cancelled' => ['label' => 'Abgebrochen', 'color' => 'red', 'icon' => 'fa-times-circle']
];

include __DIR__ . '/../../includes/admin-header.php';
?>

<!-- TinyMCE Editor -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<?php if (isset($_SESSION['flash_success'])): ?>
<div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg mb-6">
    <i class="fas fa-check-circle mr-2"></i><?= e($_SESSION['flash_success']) ?>
</div>
<?php unset($_SESSION['flash_success']); endif; ?>

<?php if ($composeMode): ?>
<!-- COMPOSE MODE -->
<div class="mb-6 flex items-center justify-between">
    <a href="/admin/broadcasts.php" class="text-slate-500 hover:text-primary-600">
        <i class="fas fa-arrow-left mr-2"></i>Zurück zur Übersicht
    </a>
    <a href="/admin/sequences.php" class="text-sm text-primary-600 hover:text-primary-700">
        <i class="fas fa-stream mr-1"></i>E-Mail Sequenzen
    </a>
</div>

<form method="POST" id="broadcastForm">
    <input type="hidden" name="action" id="formAction" value="save_draft">
    <input type="hidden" name="broadcast_id" value="<?= $editBroadcast['id'] ?? '' ?>">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Linke Spalte: Editor -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <h2 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">
                    <i class="fas fa-envelope text-primary-500 mr-2"></i>
                    <?= $editBroadcast ? 'Broadcast bearbeiten' : 'Neuer Broadcast' ?>
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                            Interner Name *
                        </label>
                        <input type="text" name="name" required value="<?= e($editBroadcast['name'] ?? '') ?>"
                               placeholder="z.B. Newsletter Dezember 2024"
                               class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                Absender Name
                            </label>
                            <input type="text" name="from_name" id="fromName" value="<?= e($editBroadcast['from_name'] ?? 'Leadbusiness') ?>"
                                   class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                Absender E-Mail
                            </label>
                            <input type="email" name="from_email" id="fromEmail" value="<?= e($editBroadcast['from_email'] ?? 'info@empfehlungen.cloud') ?>"
                                   class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                            Reply-To (optional)
                        </label>
                        <input type="email" name="reply_to" value="<?= e($editBroadcast['reply_to'] ?? '') ?>"
                               placeholder="support@empfehlungen.cloud"
                               class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                            Betreff *
                        </label>
                        <input type="text" name="subject" id="subjectField" required value="<?= e($editBroadcast['subject'] ?? '') ?>"
                               placeholder="Ihr Betreff hier..."
                               class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                        <div class="flex flex-wrap gap-1 mt-1">
                            <span class="text-xs text-slate-500">Variablen:</span>
                            <button type="button" onclick="insertIntoField('subjectField', '{company_name}')" class="text-xs text-primary-600 hover:underline">{company_name}</button>
                            <button type="button" onclick="insertIntoField('subjectField', '{contact_name}')" class="text-xs text-primary-600 hover:underline">{contact_name}</button>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Inhalt *
                            </label>
                            <button type="button" onclick="toggleEditor()" id="toggleEditorBtn" class="text-xs text-primary-600 hover:text-primary-700">
                                <i class="fas fa-code mr-1"></i><span id="toggleEditorText">HTML-Modus</span>
                            </button>
                        </div>
                        <textarea name="body_html" id="bodyHtml" rows="15" required
                                  class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white"><?= e($editBroadcast['body_html'] ?? '') ?></textarea>
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <span class="text-xs text-slate-500">Variablen einfügen:</span>
                            <button type="button" onclick="insertVariable('{company_name}')" class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 rounded text-xs">{company_name}</button>
                            <button type="button" onclick="insertVariable('{contact_name}')" class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 rounded text-xs">{contact_name}</button>
                            <button type="button" onclick="insertVariable('{email}')" class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 rounded text-xs">{email}</button>
                            <button type="button" onclick="insertVariable('{subdomain}')" class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 rounded text-xs">{subdomain}</button>
                            <button type="button" onclick="insertVariable('{unsubscribe_link}')" class="px-2 py-0.5 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 text-red-700 rounded text-xs">{unsubscribe_link}</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Vorlagen -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-white mb-3">
                    <i class="fas fa-magic text-purple-500 mr-2"></i>Schnellvorlagen
                </h3>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="insertTemplate('welcome')" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg text-sm">
                        <i class="fas fa-hand-wave mr-1 text-amber-500"></i>Willkommen
                    </button>
                    <button type="button" onclick="insertTemplate('update')" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg text-sm">
                        <i class="fas fa-rocket mr-1 text-blue-500"></i>Produkt-Update
                    </button>
                    <button type="button" onclick="insertTemplate('tip')" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg text-sm">
                        <i class="fas fa-lightbulb mr-1 text-yellow-500"></i>Tipp der Woche
                    </button>
                    <button type="button" onclick="insertTemplate('promo')" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg text-sm">
                        <i class="fas fa-gift mr-1 text-green-500"></i>Sonderangebot
                    </button>
                    <button type="button" onclick="insertTemplate('reminder')" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg text-sm">
                        <i class="fas fa-bell mr-1 text-red-500"></i>Erinnerung
                    </button>
                </div>
            </div>
            
            <!-- Vorschau -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-white">
                        <i class="fas fa-eye text-cyan-500 mr-2"></i>Live-Vorschau
                    </h3>
                    <button type="button" onclick="updatePreview()" class="text-xs text-primary-600 hover:text-primary-700">
                        <i class="fas fa-sync-alt mr-1"></i>Aktualisieren
                    </button>
                </div>
                <div class="border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden bg-white">
                    <iframe id="previewFrame" class="w-full h-64" style="border: none;"></iframe>
                </div>
            </div>
        </div>
        
        <!-- Rechte Spalte: Targeting & Aktionen -->
        <div class="space-y-6">
            <!-- Test-E-Mail -->
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-xl shadow-sm border border-amber-200 dark:border-amber-800 p-6">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-white mb-3">
                    <i class="fas fa-flask text-amber-500 mr-2"></i>Test-E-Mail senden
                </h3>
                <div class="space-y-3">
                    <input type="email" id="testEmail" value="<?= e($adminEmail) ?>" placeholder="test@example.com"
                           class="w-full px-3 py-2 border border-amber-200 dark:border-amber-700 rounded-lg bg-white dark:bg-slate-800 text-sm">
                    <button type="button" onclick="sendTestEmail()" id="testEmailBtn" 
                            class="w-full px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition-all text-sm">
                        <i class="fas fa-paper-plane mr-2"></i>Test senden
                    </button>
                    <p class="text-xs text-amber-700 dark:text-amber-400">
                        Sendet eine Vorschau mit Beispieldaten an die angegebene Adresse.
                    </p>
                </div>
            </div>
            
            <!-- Empfänger -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-white mb-4">
                    <i class="fas fa-users text-green-500 mr-2"></i>Empfänger
                </h3>
                
                <div class="space-y-3">
                    <label class="flex items-center gap-3 p-3 border border-slate-200 dark:border-slate-700 rounded-lg cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50">
                        <input type="radio" name="target_type" value="all" <?= ($editBroadcast['target_type'] ?? 'all') === 'all' ? 'checked' : '' ?> class="text-primary-600" onchange="updateTargetUI()">
                        <div>
                            <p class="font-medium text-slate-800 dark:text-white">Alle Kunden</p>
                            <p class="text-xs text-slate-500"><?= count($customers) ?> Empfänger</p>
                        </div>
                    </label>
                    
                    <label class="flex items-center gap-3 p-3 border border-slate-200 dark:border-slate-700 rounded-lg cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50">
                        <input type="radio" name="target_type" value="filter" <?= ($editBroadcast['target_type'] ?? '') === 'filter' ? 'checked' : '' ?> class="text-primary-600" onchange="updateTargetUI()">
                        <div>
                            <p class="font-medium text-slate-800 dark:text-white">Nach Filter</p>
                            <p class="text-xs text-slate-500">Plan, Status, Branche</p>
                        </div>
                    </label>
                    
                    <label class="flex items-center gap-3 p-3 border border-slate-200 dark:border-slate-700 rounded-lg cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50">
                        <input type="radio" name="target_type" value="tags" <?= ($editBroadcast['target_type'] ?? '') === 'tags' || $preselectedTag ? 'checked' : '' ?> class="text-primary-600" onchange="updateTargetUI()">
                        <div>
                            <p class="font-medium text-slate-800 dark:text-white">Nach Tags</p>
                            <p class="text-xs text-slate-500"><?= count($tags) ?> Tags verfügbar</p>
                        </div>
                    </label>
                    
                    <label class="flex items-center gap-3 p-3 border border-slate-200 dark:border-slate-700 rounded-lg cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50">
                        <input type="radio" name="target_type" value="manual" <?= ($editBroadcast['target_type'] ?? '') === 'manual' ? 'checked' : '' ?> class="text-primary-600" onchange="updateTargetUI()">
                        <div>
                            <p class="font-medium text-slate-800 dark:text-white">Manuell auswählen</p>
                            <p class="text-xs text-slate-500">Einzelne Kunden</p>
                        </div>
                    </label>
                </div>
                
                <!-- Filter Options -->
                <div id="filterOptions" class="mt-4 space-y-3 hidden">
                    <select name="filter_plan" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-sm">
                        <option value="all">Alle Pläne</option>
                        <option value="starter">Starter</option>
                        <option value="professional">Professional</option>
                        <option value="enterprise">Enterprise</option>
                    </select>
                    <select name="filter_status" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-sm">
                        <option value="all">Alle Status</option>
                        <option value="active">Aktiv</option>
                        <option value="trial">Trial</option>
                        <option value="cancelled">Gekündigt</option>
                        <option value="paused">Pausiert</option>
                    </select>
                    <select name="filter_industry" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-sm">
                        <option value="all">Alle Branchen</option>
                        <?php foreach ($industries as $ind): ?>
                        <option value="<?= e($ind['industry']) ?>"><?= e(ucfirst($ind['industry'])) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Tag Options -->
                <div id="tagOptions" class="mt-4 space-y-2 hidden max-h-48 overflow-y-auto">
                    <?php foreach ($tags as $tag): ?>
                    <label class="flex items-center gap-2 p-2 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded cursor-pointer">
                        <input type="checkbox" name="target_tags[]" value="<?= $tag['id'] ?>" <?= $preselectedTag == $tag['id'] ? 'checked' : '' ?> class="rounded text-primary-600">
                        <span class="w-3 h-3 rounded-full" style="background-color: <?= e($tag['color']) ?>"></span>
                        <span class="text-sm"><?= e($tag['name']) ?></span>
                    </label>
                    <?php endforeach; ?>
                    <?php if (empty($tags)): ?>
                    <p class="text-sm text-slate-500 p-2">Noch keine Tags. <a href="/admin/tags.php" class="text-primary-600">Tags erstellen →</a></p>
                    <?php endif; ?>
                </div>
                
                <!-- Manual Options -->
                <div id="manualOptions" class="mt-4 hidden">
                    <input type="text" id="customerSearch" placeholder="Kunde suchen..." 
                           class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-sm mb-2">
                    <div class="max-h-48 overflow-y-auto space-y-1">
                        <?php foreach ($customers as $c): ?>
                        <label class="flex items-center gap-2 p-2 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded cursor-pointer customer-option" data-search="<?= e(strtolower($c['company_name'] . ' ' . $c['email'])) ?>">
                            <input type="checkbox" name="target_customers[]" value="<?= $c['id'] ?>" class="rounded text-primary-600">
                            <span class="text-sm truncate"><?= e($c['company_name']) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Recipient Count -->
                <div class="mt-4 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Geschätzte Empfänger: <strong id="recipientCount" class="text-primary-600"><?= count($customers) ?></strong>
                    </p>
                    <p class="text-xs text-slate-500 mt-1">
                        Abgemeldet: <?= $unsubscribeCount ?> (werden ausgeschlossen)
                    </p>
                </div>
            </div>
            
            <!-- Zeitplanung -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-white mb-4">
                    <i class="fas fa-clock text-blue-500 mr-2"></i>Zeitplanung
                </h3>
                <div>
                    <label class="block text-sm text-slate-600 dark:text-slate-400 mb-1">Senden am</label>
                    <input type="datetime-local" name="scheduled_for" id="scheduledFor"
                           value="<?= $editBroadcast['scheduled_for'] ? date('Y-m-d\TH:i', strtotime($editBroadcast['scheduled_for'])) : '' ?>"
                           class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-sm">
                </div>
            </div>
            
            <!-- Aktionen -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 space-y-3">
                <button type="button" onclick="submitForm('save_draft')" class="w-full px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-800 dark:text-white rounded-lg transition-all">
                    <i class="fas fa-save mr-2"></i>Als Entwurf speichern
                </button>
                
                <button type="button" onclick="submitForm('schedule')" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all">
                    <i class="fas fa-clock mr-2"></i>Planen
                </button>
                
                <button type="button" onclick="if(confirm('Broadcast jetzt an alle Empfänger senden?')) submitForm('send_now')" 
                        class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-all">
                    <i class="fas fa-paper-plane mr-2"></i>Jetzt senden
                </button>
            </div>
        </div>
    </div>
</form>

<script>
let editorActive = false;

// TinyMCE initialisieren
document.addEventListener('DOMContentLoaded', function() {
    initEditor();
    updatePreview();
});

function initEditor() {
    tinymce.init({
        selector: '#bodyHtml',
        height: 400,
        menubar: false,
        plugins: 'lists link image code table hr',
        toolbar: 'undo redo | blocks | bold italic underline | forecolor backcolor | alignleft aligncenter alignright | bullist numlist | link image | table hr | code',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px; line-height: 1.6; }',
        branding: false,
        promotion: false,
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
                updatePreview();
            });
        }
    });
    editorActive = true;
}

function toggleEditor() {
    if (editorActive) {
        tinymce.get('bodyHtml').remove();
        document.getElementById('bodyHtml').style.fontFamily = 'monospace';
        document.getElementById('bodyHtml').style.fontSize = '12px';
        document.getElementById('toggleEditorText').textContent = 'Visual-Modus';
        editorActive = false;
    } else {
        initEditor();
        document.getElementById('toggleEditorText').textContent = 'HTML-Modus';
    }
}

function insertVariable(variable) {
    if (editorActive && tinymce.get('bodyHtml')) {
        tinymce.get('bodyHtml').execCommand('mceInsertContent', false, variable);
    } else {
        const textarea = document.getElementById('bodyHtml');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        textarea.value = textarea.value.substring(0, start) + variable + textarea.value.substring(end);
        textarea.selectionStart = textarea.selectionEnd = start + variable.length;
        textarea.focus();
    }
    updatePreview();
}

function insertIntoField(fieldId, text) {
    const field = document.getElementById(fieldId);
    const start = field.selectionStart;
    const end = field.selectionEnd;
    field.value = field.value.substring(0, start) + text + field.value.substring(end);
    field.selectionStart = field.selectionEnd = start + text.length;
    field.focus();
}

function updatePreview() {
    let content = '';
    if (editorActive && tinymce.get('bodyHtml')) {
        content = tinymce.get('bodyHtml').getContent();
    } else {
        content = document.getElementById('bodyHtml').value;
    }
    
    // Variablen durch Beispieldaten ersetzen
    const testData = {
        '{company_name}': '<span style="background:#fef3c7;padding:2px 4px;border-radius:3px;">Test Firma GmbH</span>',
        '{contact_name}': '<span style="background:#fef3c7;padding:2px 4px;border-radius:3px;">Max Mustermann</span>',
        '{email}': '<span style="background:#fef3c7;padding:2px 4px;border-radius:3px;">test@example.com</span>',
        '{subdomain}': '<span style="background:#fef3c7;padding:2px 4px;border-radius:3px;">test-firma</span>',
        '{unsubscribe_link}': '<span style="background:#fee2e2;padding:2px 4px;border-radius:3px;">#unsubscribe</span>'
    };
    
    for (const [key, value] of Object.entries(testData)) {
        content = content.split(key).join(value);
    }
    
    const frame = document.getElementById('previewFrame');
    frame.srcdoc = `<!DOCTYPE html><html><head><style>body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;font-size:14px;line-height:1.6;padding:15px;margin:0;}</style></head><body>${content}</body></html>`;
}

function updateTargetUI() {
    const targetType = document.querySelector('input[name="target_type"]:checked').value;
    
    document.getElementById('filterOptions').classList.add('hidden');
    document.getElementById('tagOptions').classList.add('hidden');
    document.getElementById('manualOptions').classList.add('hidden');
    
    if (targetType === 'filter') {
        document.getElementById('filterOptions').classList.remove('hidden');
    } else if (targetType === 'tags') {
        document.getElementById('tagOptions').classList.remove('hidden');
    } else if (targetType === 'manual') {
        document.getElementById('manualOptions').classList.remove('hidden');
    }
}

function submitForm(action) {
    // TinyMCE Inhalt sichern
    if (editorActive && tinymce.get('bodyHtml')) {
        tinymce.get('bodyHtml').save();
    }
    
    document.getElementById('formAction').value = action;
    
    if (action === 'schedule') {
        const scheduled = document.getElementById('scheduledFor').value;
        if (!scheduled) {
            alert('Bitte wählen Sie einen Zeitpunkt für die Planung.');
            return;
        }
    }
    
    document.getElementById('broadcastForm').submit();
}

function sendTestEmail() {
    const testEmail = document.getElementById('testEmail').value;
    const subject = document.getElementById('subjectField').value;
    const fromName = document.getElementById('fromName').value;
    const fromEmail = document.getElementById('fromEmail').value;
    
    let bodyHtml = '';
    if (editorActive && tinymce.get('bodyHtml')) {
        bodyHtml = tinymce.get('bodyHtml').getContent();
    } else {
        bodyHtml = document.getElementById('bodyHtml').value;
    }
    
    if (!testEmail) {
        alert('Bitte geben Sie eine E-Mail-Adresse ein.');
        return;
    }
    
    if (!subject || !bodyHtml) {
        alert('Bitte füllen Sie Betreff und Inhalt aus.');
        return;
    }
    
    const btn = document.getElementById('testEmailBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Senden...';
    
    const formData = new FormData();
    formData.append('test_email', testEmail);
    formData.append('subject', subject);
    formData.append('body_html', bodyHtml);
    formData.append('from_name', fromName);
    formData.append('from_email', fromEmail);
    
    fetch('/admin/api/send-test-email.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
        } else {
            alert('❌ Fehler: ' + data.error);
        }
    })
    .catch(error => {
        alert('❌ Fehler: ' + error.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Test senden';
    });
}

function insertTemplate(type) {
    const templates = {
        welcome: `<h2 style="color: #1e293b; margin-bottom: 20px;">Willkommen bei Leadbusiness, {contact_name}!</h2>
<p>Vielen Dank, dass Sie sich für Leadbusiness entschieden haben.</p>
<p>Ihr Empfehlungsprogramm ist jetzt aktiv unter:<br>
<a href="https://{subdomain}.empfehlungen.cloud" style="color: #667eea;">https://{subdomain}.empfehlungen.cloud</a></p>
<p>Bei Fragen stehen wir Ihnen gerne zur Verfügung.</p>
<p>Mit freundlichen Grüßen,<br><strong>Ihr Leadbusiness Team</strong></p>
<hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">
<p style="font-size: 12px; color: #64748b;"><a href="{unsubscribe_link}" style="color: #64748b;">Von diesem Newsletter abmelden</a></p>`,
        
        update: `<h2 style="color: #1e293b; margin-bottom: 20px;">🚀 Neues Feature verfügbar!</h2>
<p>Hallo {contact_name},</p>
<p>wir freuen uns, Ihnen ein spannendes neues Feature vorzustellen:</p>
<div style="background: #f0f9ff; padding: 20px; border-radius: 8px; margin: 20px 0;">
<h3 style="color: #0369a1; margin-top: 0;">Feature-Name</h3>
<ul style="color: #475569;">
<li>Vorteil 1</li>
<li>Vorteil 2</li>
<li>Vorteil 3</li>
</ul>
</div>
<p>Probieren Sie es gleich aus in Ihrem <a href="https://empfehlungen.cloud/dashboard" style="color: #667eea;">Dashboard</a>!</p>
<p>Mit freundlichen Grüßen,<br><strong>Ihr Leadbusiness Team</strong></p>
<hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">
<p style="font-size: 12px; color: #64748b;"><a href="{unsubscribe_link}" style="color: #64748b;">Von diesem Newsletter abmelden</a></p>`,
        
        tip: `<h2 style="color: #1e293b; margin-bottom: 20px;">💡 Tipp der Woche</h2>
<p>Hallo {contact_name},</p>
<p>diese Woche möchten wir Ihnen einen wertvollen Tipp geben:</p>
<blockquote style="border-left: 4px solid #667eea; padding: 15px 20px; margin: 20px 0; background: #f8fafc; color: #475569; font-style: italic;">
"Hier kommt Ihr Tipp..."
</blockquote>
<p>Viel Erfolg bei der Umsetzung!</p>
<p>Mit freundlichen Grüßen,<br><strong>Ihr Leadbusiness Team</strong></p>
<hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">
<p style="font-size: 12px; color: #64748b;"><a href="{unsubscribe_link}" style="color: #64748b;">Von diesem Newsletter abmelden</a></p>`,
        
        promo: `<h2 style="color: #1e293b; margin-bottom: 20px;">🎉 Exklusives Angebot für Sie!</h2>
<p>Hallo {contact_name},</p>
<p>als geschätzter Kunde erhalten Sie ein exklusives Angebot:</p>
<div style="background: linear-gradient(135deg, #667eea, #764ba2); padding: 30px; border-radius: 12px; text-align: center; margin: 20px 0;">
<h3 style="color: white; font-size: 28px; margin: 0;">XX% Rabatt</h3>
<p style="color: rgba(255,255,255,0.9); margin: 10px 0;">auf Ihr Upgrade zum Professional-Plan</p>
<p style="color: rgba(255,255,255,0.8); font-size: 14px;">Nur bis zum XX.XX.XXXX</p>
<a href="#" style="display: inline-block; background: white; color: #667eea; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-top: 15px;">Jetzt upgraden</a>
</div>
<p>Mit freundlichen Grüßen,<br><strong>Ihr Leadbusiness Team</strong></p>
<hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">
<p style="font-size: 12px; color: #64748b;"><a href="{unsubscribe_link}" style="color: #64748b;">Von diesem Newsletter abmelden</a></p>`,
        
        reminder: `<h2 style="color: #1e293b; margin-bottom: 20px;">⏰ Freundliche Erinnerung</h2>
<p>Hallo {contact_name},</p>
<p>wir möchten Sie kurz daran erinnern:</p>
<div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px 20px; margin: 20px 0;">
<p style="color: #92400e; margin: 0;"><strong>Ihre Erinnerung hier...</strong></p>
</div>
<p>Bei Fragen stehen wir Ihnen gerne zur Verfügung.</p>
<p>Mit freundlichen Grüßen,<br><strong>Ihr Leadbusiness Team</strong></p>
<hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">
<p style="font-size: 12px; color: #64748b;"><a href="{unsubscribe_link}" style="color: #64748b;">Von diesem Newsletter abmelden</a></p>`
    };
    
    if (templates[type]) {
        if (editorActive && tinymce.get('bodyHtml')) {
            tinymce.get('bodyHtml').setContent(templates[type]);
        } else {
            document.getElementById('bodyHtml').value = templates[type];
        }
        updatePreview();
    }
}

// Customer search
document.getElementById('customerSearch')?.addEventListener('input', function() {
    const search = this.value.toLowerCase();
    document.querySelectorAll('.customer-option').forEach(el => {
        el.style.display = el.dataset.search.includes(search) ? '' : 'none';
    });
});

// Initialize
updateTargetUI();
</script>

<?php else: ?>
<!-- LIST MODE -->

<!-- Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">
            <i class="fas fa-broadcast-tower text-primary-500 mr-2"></i>E-Mail Broadcasts
        </h1>
        <p class="text-slate-500">Marketing-E-Mails an Ihre Kunden senden</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="/admin/sequences.php" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-white rounded-lg transition-all">
            <i class="fas fa-stream mr-2"></i>Sequenzen
        </a>
        <a href="/admin/tags.php" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-white rounded-lg transition-all">
            <i class="fas fa-tags mr-2"></i>Tags
        </a>
        <a href="?compose=1" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all">
            <i class="fas fa-plus mr-2"></i>Neuer Broadcast
        </a>
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl p-4 text-white">
        <div class="flex items-center justify-between mb-2">
            <i class="fas fa-paper-plane text-white/80"></i>
        </div>
        <h3 class="text-2xl font-bold"><?= number_format($stats['total_sent'], 0, ',', '.') ?></h3>
        <p class="text-sm text-white/80">Gesendet</p>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-envelope-open text-green-600"></i>
            </div>
            <span class="text-xs text-green-500 font-medium"><?= $stats['open_rate'] ?>%</span>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['total_opened'], 0, ',', '.') ?></h3>
        <p class="text-sm text-slate-500">Geöffnet</p>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-mouse-pointer text-blue-600"></i>
            </div>
            <span class="text-xs text-blue-500 font-medium"><?= $stats['click_rate'] ?>%</span>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['total_clicked'], 0, ',', '.') ?></h3>
        <p class="text-sm text-slate-500">Geklickt</p>
    </div>
    
    <a href="?filter=draft" class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border-2 <?= $filter === 'draft' ? 'border-slate-500' : 'border-slate-200 dark:border-slate-700' ?> hover:border-slate-400 transition-all">
        <div class="w-10 h-10 bg-slate-100 dark:bg-slate-700 rounded-lg flex items-center justify-center mb-2">
            <i class="fas fa-file-alt text-slate-600"></i>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= $stats['draft'] ?></h3>
        <p class="text-sm text-slate-500">Entwürfe</p>
    </a>
    
    <a href="?filter=scheduled" class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border-2 <?= $filter === 'scheduled' ? 'border-blue-500' : 'border-slate-200 dark:border-slate-700' ?> hover:border-blue-400 transition-all">
        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-2">
            <i class="fas fa-clock text-blue-600"></i>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= $stats['scheduled'] ?></h3>
        <p class="text-sm text-slate-500">Geplant</p>
    </a>
    
    <a href="?filter=sending" class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border-2 <?= $filter === 'sending' ? 'border-amber-500' : 'border-slate-200 dark:border-slate-700' ?> hover:border-amber-400 transition-all">
        <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center mb-2">
            <i class="fas fa-spinner fa-spin text-amber-600"></i>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= $stats['sending'] ?></h3>
        <p class="text-sm text-slate-500">Wird gesendet</p>
    </a>
</div>

<!-- Broadcasts Table -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Broadcast</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Empfänger</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Gesendet</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Öffnungen</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Klicks</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php foreach ($broadcasts as $b): ?>
                <?php $sc = $statusConfig[$b['status']] ?? ['label' => $b['status'], 'color' => 'slate', 'icon' => 'fa-question']; ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                    <td class="px-4 py-3">
                        <div class="font-medium text-slate-800 dark:text-white"><?= e($b['name']) ?></div>
                        <div class="text-sm text-slate-500 truncate max-w-xs"><?= e($b['subject']) ?></div>
                        <div class="text-xs text-slate-400 mt-1"><?= date('d.m.Y H:i', strtotime($b['created_at'])) ?></div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="font-medium"><?= number_format($b['total_recipients'], 0, ',', '.') ?></span>
                        <span class="text-xs text-slate-500 block"><?= ucfirst($b['target_type']) ?></span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="font-medium"><?= number_format($b['sent_count'], 0, ',', '.') ?></span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="font-medium"><?= number_format($b['opened_count'], 0, ',', '.') ?></span>
                        <?php if ($b['sent_count'] > 0): ?>
                        <span class="text-xs text-green-500 block"><?= round(($b['opened_count'] / $b['sent_count']) * 100, 1) ?>%</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="font-medium"><?= number_format($b['clicked_count'], 0, ',', '.') ?></span>
                        <?php if ($b['opened_count'] > 0): ?>
                        <span class="text-xs text-blue-500 block"><?= round(($b['clicked_count'] / $b['opened_count']) * 100, 1) ?>%</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-<?= $sc['color'] ?>-100 text-<?= $sc['color'] ?>-700 dark:bg-<?= $sc['color'] ?>-900/30 dark:text-<?= $sc['color'] ?>-300">
                            <i class="fas <?= $sc['icon'] ?>"></i>
                            <?= $sc['label'] ?>
                        </span>
                        <?php if ($b['status'] === 'scheduled' && $b['scheduled_for']): ?>
                        <span class="text-xs text-slate-500 block mt-1"><?= date('d.m. H:i', strtotime($b['scheduled_for'])) ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <?php if ($b['status'] === 'draft'): ?>
                            <a href="?edit=<?= $b['id'] ?>" class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg" title="Bearbeiten">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php endif; ?>
                            
                            <?php if ($b['status'] === 'sending'): ?>
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="pause">
                                <input type="hidden" name="broadcast_id" value="<?= $b['id'] ?>">
                                <button type="submit" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg" title="Pausieren">
                                    <i class="fas fa-pause"></i>
                                </button>
                            </form>
                            <?php elseif ($b['status'] === 'paused'): ?>
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="resume">
                                <input type="hidden" name="broadcast_id" value="<?= $b['id'] ?>">
                                <button type="submit" class="p-2 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded-lg" title="Fortsetzen">
                                    <i class="fas fa-play"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            
                            <?php if (in_array($b['status'], ['scheduled', 'sending'])): ?>
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="cancel">
                                <input type="hidden" name="broadcast_id" value="<?= $b['id'] ?>">
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg" title="Abbrechen" onclick="return confirm('Broadcast abbrechen?')">
                                    <i class="fas fa-stop"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="duplicate">
                                <input type="hidden" name="broadcast_id" value="<?= $b['id'] ?>">
                                <button type="submit" class="p-2 text-slate-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg" title="Duplizieren">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </form>
                            
                            <a href="/admin/broadcast-stats.php?id=<?= $b['id'] ?>" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg" title="Statistiken">
                                <i class="fas fa-chart-bar"></i>
                            </a>
                            
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="broadcast_id" value="<?= $b['id'] ?>">
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg" title="Löschen" onclick="return confirm('Broadcast löschen?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($broadcasts)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                        <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-broadcast-tower text-primary-500 text-2xl"></i>
                        </div>
                        <p class="mb-4">Noch keine Broadcasts</p>
                        <a href="?compose=1" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg inline-block">
                            <i class="fas fa-plus mr-2"></i>Ersten Broadcast erstellen
                        </a>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if ($totalPages > 1): ?>
    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
        <p class="text-sm text-slate-500">Seite <?= $page ?> von <?= $totalPages ?></p>
        <div class="flex items-center gap-2">
            <?php if ($page > 1): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" class="px-3 py-1 border border-slate-200 dark:border-slate-600 rounded-lg text-sm hover:bg-slate-50">
                <i class="fas fa-chevron-left"></i>
            </a>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" class="px-3 py-1 border border-slate-200 dark:border-slate-600 rounded-lg text-sm hover:bg-slate-50">
                <i class="fas fa-chevron-right"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php endif; ?>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
