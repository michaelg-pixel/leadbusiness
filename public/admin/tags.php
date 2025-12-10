<?php
/**
 * Admin Tag-Verwaltung
 * Leadbusiness - E-Mail Marketing
 */

require_once __DIR__ . '/../../includes/init.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$db = db();
$pageTitle = 'Tags verwalten';

// Aktionen verarbeiten
if (isPost()) {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            $name = sanitize($_POST['name'] ?? '');
            $color = sanitize($_POST['color'] ?? '#6366f1');
            $description = sanitize($_POST['description'] ?? '');
            
            if (!empty($name)) {
                $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
                $slug = trim($slug, '-');
                
                // Unique Slug sicherstellen
                $baseSlug = $slug;
                $counter = 1;
                while ($db->fetchColumn("SELECT id FROM customer_tags WHERE slug = ?", [$slug])) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                
                $db->execute(
                    "INSERT INTO customer_tags (name, slug, color, description) VALUES (?, ?, ?, ?)",
                    [$name, $slug, $color, $description]
                );
                $_SESSION['flash_success'] = 'Tag wurde erstellt.';
            }
            break;
            
        case 'update':
            $tagId = intval($_POST['tag_id'] ?? 0);
            $name = sanitize($_POST['name'] ?? '');
            $color = sanitize($_POST['color'] ?? '#6366f1');
            $description = sanitize($_POST['description'] ?? '');
            
            if ($tagId && !empty($name)) {
                $db->execute(
                    "UPDATE customer_tags SET name = ?, color = ?, description = ? WHERE id = ?",
                    [$name, $color, $description, $tagId]
                );
                $_SESSION['flash_success'] = 'Tag wurde aktualisiert.';
            }
            break;
            
        case 'delete':
            $tagId = intval($_POST['tag_id'] ?? 0);
            if ($tagId) {
                $db->execute("DELETE FROM customer_tags WHERE id = ?", [$tagId]);
                $_SESSION['flash_success'] = 'Tag wurde gelöscht.';
            }
            break;
            
        case 'assign':
            $tagId = intval($_POST['tag_id'] ?? 0);
            $customerIds = $_POST['customer_ids'] ?? [];
            
            if ($tagId && !empty($customerIds)) {
                foreach ($customerIds as $customerId) {
                    $db->execute(
                        "INSERT IGNORE INTO customer_tag_assignments (customer_id, tag_id, assigned_by) VALUES (?, ?, 'admin')",
                        [$customerId, $tagId]
                    );
                }
                $_SESSION['flash_success'] = count($customerIds) . ' Kunden wurden getaggt.';
            }
            break;
            
        case 'unassign':
            $tagId = intval($_POST['tag_id'] ?? 0);
            $customerId = intval($_POST['customer_id'] ?? 0);
            
            if ($tagId && $customerId) {
                $db->execute(
                    "DELETE FROM customer_tag_assignments WHERE tag_id = ? AND customer_id = ?",
                    [$tagId, $customerId]
                );
                $_SESSION['flash_success'] = 'Tag wurde vom Kunden entfernt.';
            }
            break;
            
        case 'bulk_tag':
            $tagId = intval($_POST['tag_id'] ?? 0);
            $filterPlan = sanitize($_POST['filter_plan'] ?? 'all');
            $filterStatus = sanitize($_POST['filter_status'] ?? 'all');
            $filterIndustry = sanitize($_POST['filter_industry'] ?? 'all');
            
            if ($tagId) {
                $where = [];
                $params = [];
                
                if ($filterPlan !== 'all') {
                    $where[] = "plan = ?";
                    $params[] = $filterPlan;
                }
                if ($filterStatus !== 'all') {
                    $where[] = "subscription_status = ?";
                    $params[] = $filterStatus;
                }
                if ($filterIndustry !== 'all') {
                    $where[] = "industry = ?";
                    $params[] = $filterIndustry;
                }
                
                $whereClause = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);
                $customers = $db->fetchAll("SELECT id FROM customers $whereClause", $params);
                
                $count = 0;
                foreach ($customers as $c) {
                    $result = $db->execute(
                        "INSERT IGNORE INTO customer_tag_assignments (customer_id, tag_id, assigned_by) VALUES (?, ?, 'admin')",
                        [$c['id'], $tagId]
                    );
                    if ($result) $count++;
                }
                $_SESSION['flash_success'] = "$count Kunden wurden getaggt.";
            }
            break;
    }
    
    header('Location: /admin/tags.php');
    exit;
}

// Tags laden mit Statistiken
$tags = $db->fetchAll("
    SELECT t.*, 
           (SELECT COUNT(*) FROM customer_tag_assignments WHERE tag_id = t.id) as customer_count
    FROM customer_tags t
    ORDER BY t.name
");

// Kunden für Zuweisungs-Dialog
$customers = $db->fetchAll("SELECT id, company_name, email, plan, subscription_status, industry FROM customers ORDER BY company_name");

// Branchen für Filter
$industries = $db->fetchAll("SELECT DISTINCT industry FROM customers WHERE industry IS NOT NULL AND industry != '' ORDER BY industry");

// Statistiken
$stats = [
    'total_tags' => count($tags),
    'total_assignments' => $db->fetchColumn("SELECT COUNT(*) FROM customer_tag_assignments") ?? 0,
    'customers_with_tags' => $db->fetchColumn("SELECT COUNT(DISTINCT customer_id) FROM customer_tag_assignments") ?? 0,
    'total_customers' => $db->fetchColumn("SELECT COUNT(*) FROM customers") ?? 0,
];

include __DIR__ . '/../../includes/admin-header.php';
?>

<?php if (isset($_SESSION['flash_success'])): ?>
<div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg mb-6">
    <i class="fas fa-check-circle mr-2"></i><?= e($_SESSION['flash_success']) ?>
</div>
<?php unset($_SESSION['flash_success']); endif; ?>

<!-- Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">
            <i class="fas fa-tags text-indigo-500 mr-2"></i>Tag-Verwaltung
        </h1>
        <p class="text-slate-500">Kunden kategorisieren für gezieltes E-Mail-Marketing</p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="openCreateModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-all">
            <i class="fas fa-plus mr-2"></i>Neuen Tag erstellen
        </button>
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-tags text-indigo-600"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= $stats['total_tags'] ?></h3>
                <p class="text-sm text-slate-500">Tags</p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-user-tag text-green-600"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= $stats['customers_with_tags'] ?></h3>
                <p class="text-sm text-slate-500">Getaggte Kunden</p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-link text-purple-600"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= $stats['total_assignments'] ?></h3>
                <p class="text-sm text-slate-500">Zuweisungen</p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-slate-100 dark:bg-slate-700 rounded-lg flex items-center justify-center">
                <i class="fas fa-user-slash text-slate-600"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= $stats['total_customers'] - $stats['customers_with_tags'] ?></h3>
                <p class="text-sm text-slate-500">Ohne Tags</p>
            </div>
        </div>
    </div>
</div>

<!-- Tags Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($tags as $tag): ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="w-4 h-4 rounded-full" style="background-color: <?= e($tag['color']) ?>"></span>
                <h3 class="font-semibold text-slate-800 dark:text-white"><?= e($tag['name']) ?></h3>
            </div>
            <div class="flex items-center gap-1">
                <button onclick="openEditModal(<?= htmlspecialchars(json_encode($tag), ENT_QUOTES) ?>)" 
                        class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-all">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="openAssignModal(<?= $tag['id'] ?>, '<?= e($tag['name']) ?>')" 
                        class="p-2 text-slate-400 hover:text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-all">
                    <i class="fas fa-user-plus"></i>
                </button>
                <form method="POST" class="inline">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="tag_id" value="<?= $tag['id'] ?>">
                    <button type="submit" onclick="return confirm('Tag wirklich löschen?')"
                            class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="p-4">
            <?php if ($tag['description']): ?>
            <p class="text-sm text-slate-500 mb-3"><?= e($tag['description']) ?></p>
            <?php endif; ?>
            <div class="flex items-center justify-between">
                <span class="text-2xl font-bold text-slate-800 dark:text-white"><?= $tag['customer_count'] ?></span>
                <span class="text-sm text-slate-500">Kunden</span>
            </div>
            
            <?php
            // Kunden mit diesem Tag laden
            $taggedCustomers = $db->fetchAll("
                SELECT c.id, c.company_name 
                FROM customers c
                JOIN customer_tag_assignments cta ON c.id = cta.customer_id
                WHERE cta.tag_id = ?
                ORDER BY c.company_name
                LIMIT 5
            ", [$tag['id']]);
            ?>
            
            <?php if (!empty($taggedCustomers)): ?>
            <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                <p class="text-xs text-slate-400 mb-2">Kunden:</p>
                <div class="flex flex-wrap gap-1">
                    <?php foreach ($taggedCustomers as $tc): ?>
                    <span class="text-xs bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded"><?= e($tc['company_name']) ?></span>
                    <?php endforeach; ?>
                    <?php if ($tag['customer_count'] > 5): ?>
                    <span class="text-xs text-slate-400">+<?= $tag['customer_count'] - 5 ?> weitere</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="px-4 py-3 bg-slate-50 dark:bg-slate-700/50 flex items-center justify-between">
            <code class="text-xs text-slate-500"><?= e($tag['slug']) ?></code>
            <a href="/admin/broadcasts.php?compose=1&tag=<?= $tag['id'] ?>" class="text-sm text-primary-600 hover:text-primary-700">
                <i class="fas fa-envelope mr-1"></i>E-Mail senden
            </a>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php if (empty($tags)): ?>
    <div class="col-span-full bg-white dark:bg-slate-800 rounded-xl p-12 text-center border border-slate-200 dark:border-slate-700">
        <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-tags text-indigo-500 text-2xl"></i>
        </div>
        <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-2">Noch keine Tags</h3>
        <p class="text-slate-500 mb-4">Erstellen Sie Tags, um Kunden zu kategorisieren.</p>
        <button onclick="openCreateModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
            <i class="fas fa-plus mr-2"></i>Ersten Tag erstellen
        </button>
    </div>
    <?php endif; ?>
</div>

<!-- Bulk Tag Section -->
<div class="mt-8 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <div class="p-4 border-b border-slate-200 dark:border-slate-700">
        <h3 class="font-semibold text-slate-800 dark:text-white">
            <i class="fas fa-users-cog text-amber-500 mr-2"></i>Massen-Tagging
        </h3>
        <p class="text-sm text-slate-500">Alle Kunden nach Kriterien taggen</p>
    </div>
    <form method="POST" class="p-4">
        <input type="hidden" name="action" value="bulk_tag">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tag auswählen</label>
                <select name="tag_id" required class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                    <option value="">-- Tag wählen --</option>
                    <?php foreach ($tags as $tag): ?>
                    <option value="<?= $tag['id'] ?>"><?= e($tag['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Plan</label>
                <select name="filter_plan" class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                    <option value="all">Alle Pläne</option>
                    <option value="starter">Starter</option>
                    <option value="professional">Professional</option>
                    <option value="enterprise">Enterprise</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status</label>
                <select name="filter_status" class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                    <option value="all">Alle Status</option>
                    <option value="active">Aktiv</option>
                    <option value="trial">Trial</option>
                    <option value="cancelled">Gekündigt</option>
                    <option value="paused">Pausiert</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Branche</label>
                <select name="filter_industry" class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                    <option value="all">Alle Branchen</option>
                    <?php foreach ($industries as $ind): ?>
                    <option value="<?= e($ind['industry']) ?>"><?= e(ucfirst($ind['industry'])) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-all">
                    <i class="fas fa-tags mr-2"></i>Alle taggen
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Create/Edit Modal -->
<div id="tagModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-slate-800 rounded-xl max-w-md w-full mx-4">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <h3 id="tagModalTitle" class="text-lg font-semibold text-slate-800 dark:text-white">Tag erstellen</h3>
            <button onclick="closeTagModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" class="p-6">
            <input type="hidden" name="action" id="tagAction" value="create">
            <input type="hidden" name="tag_id" id="tagId" value="">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Name *</label>
                    <input type="text" name="name" id="tagName" required
                           class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Farbe</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="color" id="tagColor" value="#6366f1" 
                               class="w-12 h-10 rounded cursor-pointer border border-slate-200 dark:border-slate-600">
                        <div class="flex gap-2">
                            <button type="button" onclick="document.getElementById('tagColor').value='#6366f1'" class="w-6 h-6 rounded-full bg-indigo-500"></button>
                            <button type="button" onclick="document.getElementById('tagColor').value='#10b981'" class="w-6 h-6 rounded-full bg-emerald-500"></button>
                            <button type="button" onclick="document.getElementById('tagColor').value='#f59e0b'" class="w-6 h-6 rounded-full bg-amber-500"></button>
                            <button type="button" onclick="document.getElementById('tagColor').value='#ef4444'" class="w-6 h-6 rounded-full bg-red-500"></button>
                            <button type="button" onclick="document.getElementById('tagColor').value='#8b5cf6'" class="w-6 h-6 rounded-full bg-violet-500"></button>
                            <button type="button" onclick="document.getElementById('tagColor').value='#06b6d4'" class="w-6 h-6 rounded-full bg-cyan-500"></button>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Beschreibung</label>
                    <textarea name="description" id="tagDescription" rows="2"
                              class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white"></textarea>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeTagModal()" class="px-4 py-2 text-slate-600 hover:text-slate-800">
                    Abbrechen
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                    <i class="fas fa-save mr-2"></i>Speichern
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Assign Modal -->
<div id="assignModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-slate-800 rounded-xl max-w-lg w-full mx-4 max-h-[90vh] overflow-hidden flex flex-col">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
                <span id="assignTagName"></span> zuweisen
            </h3>
            <button onclick="closeAssignModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" class="flex-1 overflow-hidden flex flex-col">
            <input type="hidden" name="action" value="assign">
            <input type="hidden" name="tag_id" id="assignTagId" value="">
            
            <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                <input type="text" id="customerSearch" placeholder="Kunde suchen..." 
                       class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            </div>
            
            <div class="flex-1 overflow-y-auto p-4">
                <div class="space-y-2" id="customerList">
                    <?php foreach ($customers as $c): ?>
                    <label class="flex items-center gap-3 p-2 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-lg cursor-pointer customer-item" data-name="<?= e(strtolower($c['company_name'])) ?>">
                        <input type="checkbox" name="customer_ids[]" value="<?= $c['id'] ?>" class="rounded border-slate-300 text-indigo-600">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-800 dark:text-white"><?= e($c['company_name']) ?></p>
                            <p class="text-xs text-slate-500"><?= e($c['email']) ?> • <?= ucfirst($c['plan']) ?> • <?= ucfirst($c['subscription_status']) ?></p>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="p-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-3">
                <button type="button" onclick="closeAssignModal()" class="px-4 py-2 text-slate-600 hover:text-slate-800">
                    Abbrechen
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                    <i class="fas fa-user-plus mr-2"></i>Zuweisen
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateModal() {
    document.getElementById('tagModalTitle').textContent = 'Tag erstellen';
    document.getElementById('tagAction').value = 'create';
    document.getElementById('tagId').value = '';
    document.getElementById('tagName').value = '';
    document.getElementById('tagColor').value = '#6366f1';
    document.getElementById('tagDescription').value = '';
    
    document.getElementById('tagModal').classList.remove('hidden');
    document.getElementById('tagModal').classList.add('flex');
}

function openEditModal(tag) {
    document.getElementById('tagModalTitle').textContent = 'Tag bearbeiten';
    document.getElementById('tagAction').value = 'update';
    document.getElementById('tagId').value = tag.id;
    document.getElementById('tagName').value = tag.name;
    document.getElementById('tagColor').value = tag.color;
    document.getElementById('tagDescription').value = tag.description || '';
    
    document.getElementById('tagModal').classList.remove('hidden');
    document.getElementById('tagModal').classList.add('flex');
}

function closeTagModal() {
    document.getElementById('tagModal').classList.add('hidden');
    document.getElementById('tagModal').classList.remove('flex');
}

function openAssignModal(tagId, tagName) {
    document.getElementById('assignTagId').value = tagId;
    document.getElementById('assignTagName').textContent = tagName;
    document.getElementById('customerSearch').value = '';
    
    // Alle Checkboxen zurücksetzen
    document.querySelectorAll('#customerList input[type="checkbox"]').forEach(cb => cb.checked = false);
    document.querySelectorAll('.customer-item').forEach(item => item.style.display = '');
    
    document.getElementById('assignModal').classList.remove('hidden');
    document.getElementById('assignModal').classList.add('flex');
}

function closeAssignModal() {
    document.getElementById('assignModal').classList.add('hidden');
    document.getElementById('assignModal').classList.remove('flex');
}

// Kundensuche
document.getElementById('customerSearch').addEventListener('input', function() {
    const search = this.value.toLowerCase();
    document.querySelectorAll('.customer-item').forEach(item => {
        const name = item.dataset.name;
        item.style.display = name.includes(search) ? '' : 'none';
    });
});

// Modal schließen bei Klick außerhalb
document.getElementById('tagModal').addEventListener('click', function(e) {
    if (e.target === this) closeTagModal();
});
document.getElementById('assignModal').addEventListener('click', function(e) {
    if (e.target === this) closeAssignModal();
});
</script>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
