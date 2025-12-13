<?php
/**
 * Dashboard-Modul: E-Mail-Vorlagen
 * Fertige E-Mail-Texte zum Kopieren und Versenden
 * Verfügbar: Professional & Enterprise
 */

if (!isset($customer) || !isset($dashboardLayout)) {
    return;
}

$referralUrl = "https://{$customer['subdomain']}.empfehlungen.cloud";
$companyName = $customer['company_name'];
$customerTerm = $dashboardLayout['customer_term'] ?? 'Kunden';

// E-Mail-Vorlagen basierend auf Branche
$emailTemplates = [
    [
        'id' => 'personal',
        'name' => 'Persönliche Empfehlung',
        'icon' => 'fas fa-heart',
        'subject' => "Empfehlung von {$companyName}",
        'body' => "Hallo,

ich bin begeistert von {$companyName} und möchte das gerne mit dir teilen!

Über diesen Link kannst du dich anmelden:
{$referralUrl}

Ich freue mich, wenn du es auch mal ausprobierst!

Viele Grüße"
    ],
    [
        'id' => 'professional',
        'name' => 'Business-Empfehlung',
        'icon' => 'fas fa-briefcase',
        'subject' => "{$companyName} - Meine Empfehlung",
        'body' => "Sehr geehrte Damen und Herren,

ich möchte Ihnen {$companyName} empfehlen. Ich selbst bin dort zufriedener Kunde und kann den Service uneingeschränkt weiterempfehlen.

Weitere Informationen finden Sie hier:
{$referralUrl}

Mit freundlichen Grüßen"
    ],
    [
        'id' => 'reward',
        'name' => 'Mit Belohnung',
        'icon' => 'fas fa-gift',
        'subject' => "Spare bei {$companyName}!",
        'body' => "Hey!

Ich habe einen tollen Tipp für dich: Bei {$companyName} gibt es gerade ein super Empfehlungsprogramm. Wenn du dich über meinen Link anmeldest, profitieren wir beide!

Hier ist mein Link:
{$referralUrl}

Probier es aus!

LG"
    ]
];
?>

<div class="p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-slate-800 dark:text-white">
            <i class="fas fa-envelope text-primary-500 mr-2"></i>
            E-Mail-Vorlagen
        </h3>
    </div>
    
    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
        Fertige Texte zum Kopieren und per E-Mail versenden.
    </p>
    
    <!-- Template Tabs -->
    <div class="flex gap-2 mb-4 overflow-x-auto pb-2">
        <?php foreach ($emailTemplates as $index => $template): ?>
        <button 
            onclick="showEmailTemplate('<?= $template['id'] ?>')"
            class="email-tab flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors
                <?= $index === 0 ? 'bg-primary-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' ?>"
            data-template="<?= $template['id'] ?>">
            <i class="<?= $template['icon'] ?>"></i>
            <?= e($template['name']) ?>
        </button>
        <?php endforeach; ?>
    </div>
    
    <!-- Template Content -->
    <?php foreach ($emailTemplates as $index => $template): ?>
    <div class="email-template-content <?= $index === 0 ? '' : 'hidden' ?>" data-template="<?= $template['id'] ?>">
        
        <!-- Subject -->
        <div class="mb-3">
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">BETREFF</label>
            <div class="flex items-center gap-2">
                <input type="text" readonly value="<?= e($template['subject']) ?>" 
                    class="flex-1 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm text-slate-800 dark:text-white">
                <button onclick="copyEmailPart('<?= $template['id'] ?>', 'subject')" class="px-3 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-colors" title="Betreff kopieren">
                    <i class="fas fa-copy text-slate-600 dark:text-slate-400"></i>
                </button>
            </div>
        </div>
        
        <!-- Body -->
        <div class="mb-3">
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">NACHRICHT</label>
            <div class="relative">
                <textarea readonly rows="8" 
                    class="w-full bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm text-slate-800 dark:text-white resize-none"><?= e($template['body']) ?></textarea>
                <button onclick="copyEmailPart('<?= $template['id'] ?>', 'body')" class="absolute top-2 right-2 px-2 py-1 bg-slate-200 dark:bg-slate-600 hover:bg-slate-300 dark:hover:bg-slate-500 rounded transition-colors" title="Text kopieren">
                    <i class="fas fa-copy text-slate-600 dark:text-slate-400"></i>
                </button>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="flex gap-2">
            <button onclick="copyFullEmail('<?= $template['id'] ?>')" class="flex-1 btn btn-primary">
                <i class="fas fa-copy mr-2"></i>
                Alles kopieren
            </button>
            <a href="mailto:?subject=<?= urlencode($template['subject']) ?>&body=<?= urlencode($template['body']) ?>" class="btn btn-outline">
                <i class="fas fa-envelope mr-2"></i>
                E-Mail öffnen
            </a>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<script>
const emailTemplates = <?= json_encode($emailTemplates) ?>;

function showEmailTemplate(templateId) {
    // Tabs aktualisieren
    document.querySelectorAll('.email-tab').forEach(tab => {
        if (tab.dataset.template === templateId) {
            tab.classList.add('bg-primary-500', 'text-white');
            tab.classList.remove('bg-slate-100', 'dark:bg-slate-700', 'text-slate-600', 'dark:text-slate-300');
        } else {
            tab.classList.remove('bg-primary-500', 'text-white');
            tab.classList.add('bg-slate-100', 'dark:bg-slate-700', 'text-slate-600', 'dark:text-slate-300');
        }
    });
    
    // Content anzeigen
    document.querySelectorAll('.email-template-content').forEach(content => {
        content.classList.toggle('hidden', content.dataset.template !== templateId);
    });
}

function copyEmailPart(templateId, part) {
    const template = emailTemplates.find(t => t.id === templateId);
    if (!template) return;
    
    const text = part === 'subject' ? template.subject : template.body;
    navigator.clipboard.writeText(text).then(() => {
        showToast('Kopiert!', 'success');
    });
}

function copyFullEmail(templateId) {
    const template = emailTemplates.find(t => t.id === templateId);
    if (!template) return;
    
    const fullText = `Betreff: ${template.subject}\n\n${template.body}`;
    navigator.clipboard.writeText(fullText).then(() => {
        showToast('E-Mail-Vorlage kopiert!', 'success');
    });
}

function showToast(message, type = 'info') {
    // Falls globale Toast-Funktion existiert
    if (typeof window.toast === 'function') {
        window.toast(message, type);
    } else {
        alert(message);
    }
}
</script>
