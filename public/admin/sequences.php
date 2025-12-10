<?php
/**
 * Admin E-Mail Sequenzen
 * Leadbusiness - Automatisierte Follow-up E-Mails
 */

require_once __DIR__ . '/../../includes/init.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$db = db();
$pageTitle = 'E-Mail Sequenzen';

// Aktionen verarbeiten
if (isPost()) {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create_sequence':
        case 'update_sequence':
            $sequenceId = intval($_POST['sequence_id'] ?? 0);
            $name = sanitize($_POST['name'] ?? '');
            $description = sanitize($_POST['description'] ?? '');
            $triggerType = sanitize($_POST['trigger_type'] ?? 'after_broadcast');
            $triggerBroadcastId = intval($_POST['trigger_broadcast_id'] ?? 0) ?: null;
            $delayHours = intval($_POST['delay_hours'] ?? 24);
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            
            if (empty($name)) {
                $_SESSION['flash_error'] = 'Name ist erforderlich.';
                break;
            }
            
            $data = [
                'name' => $name,
                'description' => $description,
                'trigger_type' => $triggerType,
                'trigger_broadcast_id' => $triggerBroadcastId,
                'delay_hours' => $delayHours,
                'is_active' => $isActive,
            ];
            
            if ($sequenceId) {
                $setClauses = [];
                $params = [];
                foreach ($data as $key => $value) {
                    $setClauses[] = "$key = ?";
                    $params[] = $value;
                }
                $params[] = $sequenceId;
                $db->execute("UPDATE admin_broadcast_sequences SET " . implode(', ', $setClauses) . " WHERE id = ?", $params);
                $_SESSION['flash_success'] = 'Sequenz aktualisiert.';
            } else {
                $data['created_by'] = $_SESSION['admin_id'];
                $columns = implode(', ', array_keys($data));
                $placeholders = implode(', ', array_fill(0, count($data), '?'));
                $db->execute("INSERT INTO admin_broadcast_sequences ($columns) VALUES ($placeholders)", array_values($data));
                $sequenceId = $db->lastInsertId();
                $_SESSION['flash_success'] = 'Sequenz erstellt.';
            }
            
            header("Location: /admin/sequences.php?edit=$sequenceId");
            exit;
            
        case 'delete_sequence':
            $sequenceId = intval($_POST['sequence_id'] ?? 0);
            if ($sequenceId) {
                $db->execute("DELETE FROM admin_broadcast_sequences WHERE id = ?", [$sequenceId]);
                $_SESSION['flash_success'] = 'Sequenz gelöscht.';
            }
            break;
            
        case 'toggle_sequence':
            $sequenceId = intval($_POST['sequence_id'] ?? 0);
            if ($sequenceId) {
                $db->execute("UPDATE admin_broadcast_sequences SET is_active = NOT is_active WHERE id = ?", [$sequenceId]);
                $_SESSION['flash_success'] = 'Status geändert.';
            }
            break;
            
        case 'save_step':
        case 'update_step':
            $stepId = intval($_POST['step_id'] ?? 0);
            $sequenceId = intval($_POST['sequence_id'] ?? 0);
            $stepName = sanitize($_POST['step_name'] ?? '');
            $subject = sanitize($_POST['subject'] ?? '');
            $bodyHtml = $_POST['body_html'] ?? '';
            $fromName = sanitize($_POST['from_name'] ?? 'Leadbusiness');
            $fromEmail = sanitize($_POST['from_email'] ?? 'info@empfehlungen.cloud');
            $delayHours = intval($_POST['step_delay_hours'] ?? 0);
            $conditionType = sanitize($_POST['condition_type'] ?? 'all');
            $stepOrder = intval($_POST['step_order'] ?? 1);
            
            if (empty($stepName) || empty($subject) || empty($bodyHtml)) {
                $_SESSION['flash_error'] = 'Name, Betreff und Inhalt sind erforderlich.';
                header("Location: /admin/sequences.php?edit=$sequenceId");
                exit;
            }
            
            $data = [
                'sequence_id' => $sequenceId,
                'step_order' => $stepOrder,
                'name' => $stepName,
                'subject' => $subject,
                'body_html' => $bodyHtml,
                'from_name' => $fromName,
                'from_email' => $fromEmail,
                'delay_hours' => $delayHours,
                'condition_type' => $conditionType,
            ];
            
            if ($stepId) {
                unset($data['sequence_id']);
                $setClauses = [];
                $params = [];
                foreach ($data as $key => $value) {
                    $setClauses[] = "$key = ?";
                    $params[] = $value;
                }
                $params[] = $stepId;
                $db->execute("UPDATE admin_broadcast_sequence_steps SET " . implode(', ', $setClauses) . " WHERE id = ?", $params);
                $_SESSION['flash_success'] = 'Schritt aktualisiert.';
            } else {
                // Höchste step_order finden
                $maxOrder = $db->fetchColumn("SELECT MAX(step_order) FROM admin_broadcast_sequence_steps WHERE sequence_id = ?", [$sequenceId]) ?? 0;
                $data['step_order'] = $maxOrder + 1;
                
                $columns = implode(', ', array_keys($data));
                $placeholders = implode(', ', array_fill(0, count($data), '?'));
                $db->execute("INSERT INTO admin_broadcast_sequence_steps ($columns) VALUES ($placeholders)", array_values($data));
                $_SESSION['flash_success'] = 'Schritt hinzugefügt.';
            }
            
            header("Location: /admin/sequences.php?edit=$sequenceId");
            exit;
            
        case 'delete_step':
            $stepId = intval($_POST['step_id'] ?? 0);
            $sequenceId = intval($_POST['sequence_id'] ?? 0);
            if ($stepId) {
                $db->execute("DELETE FROM admin_broadcast_sequence_steps WHERE id = ?", [$stepId]);
                $_SESSION['flash_success'] = 'Schritt gelöscht.';
            }
            header("Location: /admin/sequences.php?edit=$sequenceId");
            exit;
            
        case 'toggle_step':
            $stepId = intval($_POST['step_id'] ?? 0);
            $sequenceId = intval($_POST['sequence_id'] ?? 0);
            if ($stepId) {
                $db->execute("UPDATE admin_broadcast_sequence_steps SET is_active = NOT is_active WHERE id = ?", [$stepId]);
            }
            header("Location: /admin/sequences.php?edit=$sequenceId");
            exit;
    }
    
    header('Location: /admin/sequences.php');
    exit;
}

// Edit Mode?
$editSequence = null;
$sequenceSteps = [];

if (isset($_GET['edit'])) {
    $editSequence = $db->fetch("SELECT * FROM admin_broadcast_sequences WHERE id = ?", [intval($_GET['edit'])]);
    if ($editSequence) {
        $sequenceSteps = $db->fetchAll("SELECT * FROM admin_broadcast_sequence_steps WHERE sequence_id = ? ORDER BY step_order", [$editSequence['id']]);
    }
}

// Create Mode?
$createMode = isset($_GET['create']);

// Alle Sequenzen laden
$sequences = $db->fetchAll("
    SELECT s.*, 
           (SELECT COUNT(*) FROM admin_broadcast_sequence_steps WHERE sequence_id = s.id) as step_count,
           (SELECT COUNT(*) FROM admin_broadcast_sequence_sends WHERE sequence_id = s.id AND status = 'sent') as sent_count
    FROM admin_broadcast_sequences s
    ORDER BY s.created_at DESC
");

// Broadcasts für Trigger-Auswahl
$broadcasts = $db->fetchAll("SELECT id, name, subject FROM admin_broadcasts ORDER BY created_at DESC LIMIT 50");

// Trigger-Types
$triggerTypes = [
    'after_broadcast' => ['label' => 'Nach Broadcast', 'icon' => 'fa-paper-plane', 'description' => 'Startet X Stunden nach einem Broadcast'],
    'not_opened' => ['label' => 'Nicht geöffnet', 'icon' => 'fa-eye-slash', 'description' => 'Wenn E-Mail nicht geöffnet wurde'],
    'not_clicked' => ['label' => 'Nicht geklickt', 'icon' => 'fa-mouse-pointer', 'description' => 'Wenn E-Mail geöffnet aber nicht geklickt'],
    'opened' => ['label' => 'Geöffnet', 'icon' => 'fa-envelope-open', 'description' => 'Wenn E-Mail geöffnet wurde'],
    'clicked' => ['label' => 'Geklickt', 'icon' => 'fa-hand-pointer', 'description' => 'Wenn auf Link geklickt wurde'],
];

// Condition Types für Steps
$conditionTypes = [
    'all' => 'Immer senden',
    'not_opened' => 'Nur wenn vorherige nicht geöffnet',
    'not_clicked' => 'Nur wenn vorherige nicht geklickt',
    'opened' => 'Nur wenn vorherige geöffnet',
    'clicked' => 'Nur wenn vorherige geklickt',
];

include __DIR__ . '/../../includes/admin-header.php';
?>

<?php if (isset($_SESSION['flash_success'])): ?>
<div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg mb-6">
    <i class="fas fa-check-circle mr-2"></i><?= e($_SESSION['flash_success']) ?>
</div>
<?php unset($_SESSION['flash_success']); endif; ?>

<?php if (isset($_SESSION['flash_error'])): ?>
<div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-6">
    <i class="fas fa-exclamation-circle mr-2"></i><?= e($_SESSION['flash_error']) ?>
</div>
<?php unset($_SESSION['flash_error']); endif; ?>

<?php if ($editSequence): ?>
<!-- EDIT SEQUENCE MODE -->
<div class="mb-6">
    <a href="/admin/sequences.php" class="text-slate-500 hover:text-primary-600">
        <i class="fas fa-arrow-left mr-2"></i>Zurück zur Übersicht
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Sequenz-Einstellungen -->
    <div class="lg:col-span-1">
        <form method="POST" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
            <input type="hidden" name="action" value="update_sequence">
            <input type="hidden" name="sequence_id" value="<?= $editSequence['id'] ?>">
            
            <h2 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">
                <i class="fas fa-cog text-primary-500 mr-2"></i>Sequenz-Einstellungen
            </h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Name *</label>
                    <input type="text" name="name" required value="<?= e($editSequence['name']) ?>"
                           class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Beschreibung</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700"><?= e($editSequence['description']) ?></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Trigger</label>
                    <select name="trigger_type" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700">
                        <?php foreach ($triggerTypes as $key => $type): ?>
                        <option value="<?= $key ?>" <?= $editSequence['trigger_type'] === $key ? 'selected' : '' ?>>
                            <?= $type['label'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Referenz-Broadcast</label>
                    <select name="trigger_broadcast_id" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700">
                        <option value="">-- Keiner --</option>
                        <?php foreach ($broadcasts as $b): ?>
                        <option value="<?= $b['id'] ?>" <?= $editSequence['trigger_broadcast_id'] == $b['id'] ? 'selected' : '' ?>>
                            <?= e($b['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Verzögerung (Stunden)</label>
                    <input type="number" name="delay_hours" value="<?= $editSequence['delay_hours'] ?>" min="0"
                           class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700">
                </div>
                
                <div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" <?= $editSequence['is_active'] ? 'checked' : '' ?> class="rounded text-primary-600">
                        <span class="text-sm text-slate-700 dark:text-slate-300">Sequenz aktiv</span>
                    </label>
                </div>
                
                <button type="submit" class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all">
                    <i class="fas fa-save mr-2"></i>Speichern
                </button>
            </div>
        </form>
    </div>
    
    <!-- Sequenz-Schritte -->
    <div class="lg:col-span-2 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-800 dark:text-white">
                <i class="fas fa-list-ol text-green-500 mr-2"></i>E-Mail-Schritte
            </h2>
            <button onclick="document.getElementById('addStepModal').classList.remove('hidden')" 
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-all">
                <i class="fas fa-plus mr-2"></i>Schritt hinzufügen
            </button>
        </div>
        
        <?php if (empty($sequenceSteps)): ?>
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-8 text-center">
            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-inbox text-slate-400 text-2xl"></i>
            </div>
            <p class="text-slate-500 mb-4">Noch keine E-Mail-Schritte</p>
            <button onclick="document.getElementById('addStepModal').classList.remove('hidden')"
                    class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg">
                <i class="fas fa-plus mr-2"></i>Ersten Schritt erstellen
            </button>
        </div>
        <?php else: ?>
        
        <div class="space-y-4">
            <?php foreach ($sequenceSteps as $index => $step): ?>
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center text-primary-600 font-bold">
                        <?= $step['step_order'] ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-medium text-slate-800 dark:text-white"><?= e($step['name']) ?></h3>
                            <?php if (!$step['is_active']): ?>
                            <span class="px-2 py-0.5 bg-slate-200 text-slate-600 text-xs rounded-full">Inaktiv</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-sm text-slate-500 mb-2"><?= e($step['subject']) ?></p>
                        <div class="flex items-center gap-4 text-xs text-slate-400">
                            <span><i class="fas fa-clock mr-1"></i><?= $step['delay_hours'] ?>h Verzögerung</span>
                            <span><i class="fas fa-filter mr-1"></i><?= $conditionTypes[$step['condition_type']] ?? $step['condition_type'] ?></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <button onclick="editStep(<?= htmlspecialchars(json_encode($step), ENT_QUOTES) ?>)" 
                                class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg" title="Bearbeiten">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form method="POST" class="inline">
                            <input type="hidden" name="action" value="toggle_step">
                            <input type="hidden" name="step_id" value="<?= $step['id'] ?>">
                            <input type="hidden" name="sequence_id" value="<?= $editSequence['id'] ?>">
                            <button type="submit" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg" title="<?= $step['is_active'] ? 'Deaktivieren' : 'Aktivieren' ?>">
                                <i class="fas <?= $step['is_active'] ? 'fa-pause' : 'fa-play' ?>"></i>
                            </button>
                        </form>
                        <form method="POST" class="inline" onsubmit="return confirm('Schritt löschen?')">
                            <input type="hidden" name="action" value="delete_step">
                            <input type="hidden" name="step_id" value="<?= $step['id'] ?>">
                            <input type="hidden" name="sequence_id" value="<?= $editSequence['id'] ?>">
                            <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg" title="Löschen">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add/Edit Step Modal -->
<div id="addStepModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <form method="POST" id="stepForm">
            <input type="hidden" name="action" id="stepAction" value="save_step">
            <input type="hidden" name="step_id" id="stepId" value="">
            <input type="hidden" name="sequence_id" value="<?= $editSequence['id'] ?>">
            <input type="hidden" name="step_order" id="stepOrder" value="<?= count($sequenceSteps) + 1 ?>">
            
            <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <h3 id="modalTitle" class="text-lg font-semibold">Neuer E-Mail-Schritt</h3>
                <button type="button" onclick="closeStepModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Schritt-Name *</label>
                    <input type="text" name="step_name" id="stepName" required placeholder="z.B. Erinnerung"
                           class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Verzögerung (Stunden)</label>
                        <input type="number" name="step_delay_hours" id="stepDelayHours" value="24" min="0"
                               class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Bedingung</label>
                        <select name="condition_type" id="stepCondition" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700">
                            <?php foreach ($conditionTypes as $key => $label): ?>
                            <option value="<?= $key ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Absender Name</label>
                        <input type="text" name="from_name" id="stepFromName" value="Leadbusiness"
                               class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Absender E-Mail</label>
                        <input type="email" name="from_email" id="stepFromEmail" value="info@empfehlungen.cloud"
                               class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Betreff *</label>
                    <input type="text" name="subject" id="stepSubject" required placeholder="E-Mail Betreff..."
                           class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Inhalt (HTML) *</label>
                    <textarea name="body_html" id="stepBodyHtml" rows="10" required
                              class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 font-mono text-sm"></textarea>
                    <p class="text-xs text-slate-500 mt-1">
                        Variablen: {company_name}, {contact_name}, {email}, {subdomain}, {unsubscribe_link}
                    </p>
                </div>
            </div>
            
            <div class="p-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end gap-3">
                <button type="button" onclick="closeStepModal()" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-white rounded-lg">
                    Abbrechen
                </button>
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg">
                    <i class="fas fa-save mr-2"></i>Speichern
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editStep(step) {
    document.getElementById('modalTitle').textContent = 'Schritt bearbeiten';
    document.getElementById('stepAction').value = 'update_step';
    document.getElementById('stepId').value = step.id;
    document.getElementById('stepOrder').value = step.step_order;
    document.getElementById('stepName').value = step.name;
    document.getElementById('stepSubject').value = step.subject;
    document.getElementById('stepBodyHtml').value = step.body_html;
    document.getElementById('stepFromName').value = step.from_name;
    document.getElementById('stepFromEmail').value = step.from_email;
    document.getElementById('stepDelayHours').value = step.delay_hours;
    document.getElementById('stepCondition').value = step.condition_type;
    document.getElementById('addStepModal').classList.remove('hidden');
}

function closeStepModal() {
    document.getElementById('addStepModal').classList.add('hidden');
    document.getElementById('stepForm').reset();
    document.getElementById('modalTitle').textContent = 'Neuer E-Mail-Schritt';
    document.getElementById('stepAction').value = 'save_step';
    document.getElementById('stepId').value = '';
}
</script>

<?php elseif ($createMode): ?>
<!-- CREATE SEQUENCE MODE -->
<div class="mb-6">
    <a href="/admin/sequences.php" class="text-slate-500 hover:text-primary-600">
        <i class="fas fa-arrow-left mr-2"></i>Zurück zur Übersicht
    </a>
</div>

<div class="max-w-xl mx-auto">
    <form method="POST" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <input type="hidden" name="action" value="create_sequence">
        
        <h2 class="text-xl font-semibold text-slate-800 dark:text-white mb-6">
            <i class="fas fa-plus-circle text-green-500 mr-2"></i>Neue Sequenz erstellen
        </h2>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Name *</label>
                <input type="text" name="name" required placeholder="z.B. Willkommens-Sequenz"
                       class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Beschreibung</label>
                <textarea name="description" rows="2" placeholder="Optional: Wofür ist diese Sequenz?"
                          class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700"></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Trigger-Typ</label>
                <div class="space-y-2">
                    <?php foreach ($triggerTypes as $key => $type): ?>
                    <label class="flex items-center gap-3 p-3 border border-slate-200 dark:border-slate-700 rounded-lg cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50">
                        <input type="radio" name="trigger_type" value="<?= $key ?>" <?= $key === 'after_broadcast' ? 'checked' : '' ?> class="text-primary-600">
                        <div>
                            <p class="font-medium text-slate-800 dark:text-white">
                                <i class="fas <?= $type['icon'] ?> mr-1 text-slate-400"></i><?= $type['label'] ?>
                            </p>
                            <p class="text-xs text-slate-500"><?= $type['description'] ?></p>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Referenz-Broadcast (optional)</label>
                <select name="trigger_broadcast_id" class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700">
                    <option value="">-- Keiner --</option>
                    <?php foreach ($broadcasts as $b): ?>
                    <option value="<?= $b['id'] ?>"><?= e($b['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Start-Verzögerung (Stunden)</label>
                <input type="number" name="delay_hours" value="24" min="0"
                       class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700">
                <p class="text-xs text-slate-500 mt-1">Nach wie vielen Stunden soll die Sequenz starten?</p>
            </div>
            
            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-primary-600">
                    <span class="text-sm text-slate-700 dark:text-slate-300">Sequenz sofort aktivieren</span>
                </label>
            </div>
            
            <div class="pt-4">
                <button type="submit" class="w-full px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all">
                    <i class="fas fa-save mr-2"></i>Sequenz erstellen
                </button>
            </div>
        </div>
    </form>
</div>

<?php else: ?>
<!-- LIST MODE -->

<!-- Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">
            <i class="fas fa-stream text-primary-500 mr-2"></i>E-Mail Sequenzen
        </h1>
        <p class="text-slate-500">Automatisierte Follow-up E-Mail-Serien</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="/admin/broadcasts.php" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-white rounded-lg transition-all">
            <i class="fas fa-broadcast-tower mr-2"></i>Broadcasts
        </a>
        <a href="?create=1" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all">
            <i class="fas fa-plus mr-2"></i>Neue Sequenz
        </a>
    </div>
</div>

<!-- Sequenzen Grid -->
<?php if (empty($sequences)): ?>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-12 text-center">
    <div class="w-20 h-20 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
        <i class="fas fa-stream text-primary-500 text-3xl"></i>
    </div>
    <h2 class="text-xl font-semibold text-slate-800 dark:text-white mb-2">Keine Sequenzen</h2>
    <p class="text-slate-500 mb-6">Erstellen Sie Ihre erste automatische E-Mail-Sequenz</p>
    <a href="?create=1" class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all">
        <i class="fas fa-plus mr-2"></i>Erste Sequenz erstellen
    </a>
</div>
<?php else: ?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($sequences as $seq): ?>
    <?php $tt = $triggerTypes[$seq['trigger_type']] ?? ['label' => $seq['trigger_type'], 'icon' => 'fa-cog']; ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-all">
        <div class="flex items-start justify-between mb-4">
            <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-xl flex items-center justify-center">
                <i class="fas <?= $tt['icon'] ?> text-primary-600 text-xl"></i>
            </div>
            <div class="flex items-center gap-1">
                <?php if ($seq['is_active']): ?>
                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">Aktiv</span>
                <?php else: ?>
                <span class="px-2 py-1 bg-slate-100 text-slate-600 text-xs rounded-full">Inaktiv</span>
                <?php endif; ?>
            </div>
        </div>
        
        <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-1"><?= e($seq['name']) ?></h3>
        <p class="text-sm text-slate-500 mb-4"><?= e($seq['description']) ?: 'Keine Beschreibung' ?></p>
        
        <div class="flex items-center gap-4 text-sm text-slate-500 mb-4">
            <span><i class="fas fa-envelope mr-1"></i><?= $seq['step_count'] ?> Schritte</span>
            <span><i class="fas fa-paper-plane mr-1"></i><?= $seq['sent_count'] ?> gesendet</span>
        </div>
        
        <div class="flex items-center gap-2">
            <a href="?edit=<?= $seq['id'] ?>" class="flex-1 px-3 py-2 bg-primary-100 dark:bg-primary-900/30 hover:bg-primary-200 text-primary-700 rounded-lg text-sm text-center transition-all">
                <i class="fas fa-edit mr-1"></i>Bearbeiten
            </a>
            <form method="POST" class="inline">
                <input type="hidden" name="action" value="toggle_sequence">
                <input type="hidden" name="sequence_id" value="<?= $seq['id'] ?>">
                <button type="submit" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg" title="<?= $seq['is_active'] ? 'Deaktivieren' : 'Aktivieren' ?>">
                    <i class="fas <?= $seq['is_active'] ? 'fa-pause' : 'fa-play' ?>"></i>
                </button>
            </form>
            <form method="POST" class="inline" onsubmit="return confirm('Sequenz löschen?')">
                <input type="hidden" name="action" value="delete_sequence">
                <input type="hidden" name="sequence_id" value="<?= $seq['id'] ?>">
                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg" title="Löschen">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php endif; ?>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
