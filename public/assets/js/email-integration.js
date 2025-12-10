/**
 * Leadbusiness - E-Mail Tool Integration (Onboarding)
 * 
 * Optionaler Schritt für Branchen: onlinemarketing, coach, onlineshop
 * Leads werden passiv zum Kunden-Tool gesynct.
 */

const EmailToolIntegration = {
    
    apiEndpoint: '/api/email-integration.php',
    selectedTool: null,
    credentials: {},
    
    init: function() {
        this.bindEvents();
        this.loadTools();
    },
    
    bindEvents: function() {
        document.querySelectorAll('.email-tool-card').forEach(card => {
            card.addEventListener('click', (e) => {
                this.selectTool(card.dataset.tool);
            });
        });
        
        const testBtn = document.getElementById('testEmailConnection');
        if (testBtn) {
            testBtn.addEventListener('click', () => this.testConnection());
        }
        
        const skipBtn = document.getElementById('skipEmailIntegration');
        if (skipBtn) {
            skipBtn.addEventListener('click', () => this.skip());
        }
    },
    
    loadTools: async function() {
        try {
            const response = await fetch(this.apiEndpoint + '?action=get_tools');
            const data = await response.json();
            
            if (data.success && data.tools) {
                this.renderTools(data.tools);
            }
        } catch (error) {
            console.error('Fehler beim Laden der Tools:', error);
        }
    },
    
    renderTools: function(tools) {
        const container = document.getElementById('emailToolsContainer');
        if (!container) return;
        
        container.innerHTML = '';
        
        const implementedTools = ['klicktipp', 'quentn', 'cleverreach'];
        
        for (const [key, tool] of Object.entries(tools)) {
            if (!implementedTools.includes(key)) continue;
            
            const card = document.createElement('div');
            card.className = 'email-tool-card cursor-pointer border-2 border-gray-200 dark:border-gray-600 rounded-xl p-4 hover:border-primary-300 transition-all bg-white dark:bg-gray-700';
            card.dataset.tool = key;
            
            card.innerHTML = '<div class="flex items-center gap-3">' +
                '<div class="w-12 h-12 bg-gray-100 dark:bg-gray-600 rounded-lg flex items-center justify-center">' +
                '<span class="text-lg font-bold text-primary-600">' + tool.name.charAt(0) + '</span>' +
                '</div>' +
                '<div>' +
                '<h4 class="font-semibold dark:text-white">' + tool.name + '</h4>' +
                (tool.popular ? '<span class="text-xs text-green-600 bg-green-50 dark:bg-green-900/30 px-2 py-0.5 rounded-full">Beliebt</span>' : '') +
                '</div>' +
                '</div>';
            
            card.addEventListener('click', () => this.selectTool(key));
            container.appendChild(card);
        }
        
        const noToolCard = document.createElement('div');
        noToolCard.className = 'email-tool-card cursor-pointer border-2 border-gray-200 dark:border-gray-600 rounded-xl p-4 hover:border-gray-400 transition-all bg-white dark:bg-gray-700';
        noToolCard.dataset.tool = 'none';
        noToolCard.innerHTML = '<div class="flex items-center gap-3">' +
            '<div class="w-12 h-12 bg-gray-100 dark:bg-gray-600 rounded-lg flex items-center justify-center text-gray-400">' +
            '<i class="fas fa-forward text-xl"></i>' +
            '</div>' +
            '<div>' +
            '<h4 class="font-semibold text-gray-600 dark:text-gray-300">Später einrichten</h4>' +
            '<span class="text-xs text-gray-500">Im Dashboard nachholen</span>' +
            '</div>' +
            '</div>';
        noToolCard.addEventListener('click', () => this.skip());
        container.appendChild(noToolCard);
    },
    
    selectTool: async function(toolName) {
        this.selectedTool = toolName;
        
        document.querySelectorAll('.email-tool-card').forEach(card => {
            card.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
            if (card.dataset.tool === toolName) {
                card.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
            }
        });
        
        const credentialsForm = document.getElementById('emailCredentialsForm');
        if (credentialsForm) {
            credentialsForm.classList.remove('hidden');
            await this.loadSetupHelp(toolName);
        }
        
        const hiddenInput = document.getElementById('selectedEmailTool');
        if (hiddenInput) {
            hiddenInput.value = toolName;
        }
    },
    
    loadSetupHelp: async function(toolName) {
        try {
            const response = await fetch(this.apiEndpoint + '?action=get_setup_help&tool=' + toolName);
            const data = await response.json();
            
            if (data.success && data.help) {
                this.renderCredentialsForm(data.help);
            }
        } catch (error) {
            console.error('Fehler beim Laden der Setup-Hilfe:', error);
        }
    },
    
    renderCredentialsForm: function(help) {
        const container = document.getElementById('credentialsFields');
        if (!container) return;
        
        let html = '<div class="mb-4">' +
            '<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">' +
            (help.api_key_label || 'API-Key') +
            '</label>' +
            '<input type="text" id="emailApiKey" name="email_api_key" ' +
            'class="w-full px-3 py-2 border dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white" ' +
            'placeholder="API-Key eingeben">' +
            '<p class="text-xs text-gray-500 mt-1">' + (help.api_key_help || '') + '</p>' +
            '</div>';
        
        if (help.requires_secret) {
            html += '<div class="mb-4">' +
                '<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">' +
                (help.api_secret_label || 'API-Secret') +
                '</label>' +
                '<input type="password" id="emailApiSecret" name="email_api_secret" ' +
                'class="w-full px-3 py-2 border dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white" ' +
                'placeholder="API-Secret eingeben">' +
                '</div>';
        }
        
        if (help.requires_url) {
            html += '<div class="mb-4">' +
                '<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">' +
                (help.api_url_label || 'Subdomain') +
                '</label>' +
                '<input type="text" id="emailApiUrl" name="email_api_url" ' +
                'class="w-full px-3 py-2 border dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white" ' +
                'placeholder="' + (help.api_url_help || 'z.B. meinefirma') + '">' +
                '<p class="text-xs text-gray-500 mt-1">' + (help.api_url_help || '') + '</p>' +
                '</div>';
        }
        
        if (help.docs_url) {
            html += '<p class="text-sm text-gray-500 mb-4">' +
                '<i class="fas fa-external-link-alt mr-1"></i>' +
                '<a href="' + help.docs_url + '" target="_blank" class="text-primary-600 hover:underline">' +
                'API-Dokumentation</a></p>';
        }
        
        html += '<button type="button" id="testEmailConnection" ' +
            'class="w-full bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 text-gray-700 dark:text-white font-medium py-2 px-4 rounded-lg transition-colors">' +
            '<i class="fas fa-plug mr-2"></i>Verbindung testen</button>' +
            '<div id="connectionStatus" class="mt-3 hidden"></div>';
        
        container.innerHTML = html;
        
        const testBtn = document.getElementById('testEmailConnection');
        if (testBtn) {
            testBtn.addEventListener('click', () => this.testConnection());
        }
    },
    
    testConnection: async function() {
        const statusDiv = document.getElementById('connectionStatus');
        const testBtn = document.getElementById('testEmailConnection');
        
        if (!this.selectedTool) {
            this.showStatus('error', 'Bitte wählen Sie zuerst ein Tool aus.');
            return;
        }
        
        this.credentials = {
            tool_name: this.selectedTool,
            api_key: document.getElementById('emailApiKey')?.value || '',
            api_secret: document.getElementById('emailApiSecret')?.value || null,
            api_url: document.getElementById('emailApiUrl')?.value || null
        };
        
        if (!this.credentials.api_key) {
            this.showStatus('error', 'Bitte geben Sie den API-Key ein.');
            return;
        }
        
        testBtn.disabled = true;
        testBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Teste...';
        
        try {
            const response = await fetch(this.apiEndpoint + '?action=test_connection', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(this.credentials)
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showStatus('success', data.message || 'Verbindung erfolgreich!');
                await this.loadTags();
            } else {
                this.showStatus('error', data.message || 'Verbindung fehlgeschlagen.');
            }
            
        } catch (error) {
            console.error('Fehler:', error);
            this.showStatus('error', 'Netzwerkfehler.');
        }
        
        testBtn.disabled = false;
        testBtn.innerHTML = '<i class="fas fa-plug mr-2"></i>Verbindung testen';
    },
    
    loadTags: async function() {
        try {
            const response = await fetch(this.apiEndpoint + '?action=get_tags', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(this.credentials)
            });
            
            const data = await response.json();
            
            if (data.success && data.tags && data.tags.length > 0) {
                this.renderTagSelector(data.tags);
            }
            
        } catch (error) {
            console.error('Fehler beim Laden der Tags:', error);
        }
    },
    
    renderTagSelector: function(tags) {
        const container = document.getElementById('credentialsFields');
        if (!container) return;
        
        const existingSelector = document.getElementById('tagSelectorWrapper');
        if (existingSelector) existingSelector.remove();
        
        const wrapper = document.createElement('div');
        wrapper.id = 'tagSelectorWrapper';
        wrapper.className = 'mt-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800';
        
        let optionsHtml = '<option value="">-- Kein Tag --</option>';
        tags.forEach(tag => {
            optionsHtml += '<option value="' + tag.id + '">' + tag.name + '</option>';
        });
        
        wrapper.innerHTML = '<p class="text-sm text-green-700 dark:text-green-400 mb-2">' +
            '<i class="fas fa-check-circle mr-1"></i>' +
            'Verbindung erfolgreich! Optional: Tag wählen</p>' +
            '<select id="emailDefaultTag" name="email_default_tag" ' +
            'class="w-full px-3 py-2 border dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">' +
            optionsHtml + '</select>' +
            '<p class="text-xs text-gray-500 mt-1">Dieser Tag wird bei jedem neuen Lead gesetzt.</p>';
        
        container.appendChild(wrapper);
    },
    
    showStatus: function(type, message) {
        const statusDiv = document.getElementById('connectionStatus');
        if (!statusDiv) return;
        
        statusDiv.classList.remove('hidden');
        
        if (type === 'success') {
            statusDiv.className = 'mt-3 p-3 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-lg text-sm';
            statusDiv.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + message;
        } else {
            statusDiv.className = 'mt-3 p-3 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg text-sm';
            statusDiv.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>' + message;
        }
    },
    
    skip: function() {
        const toolInput = document.getElementById('selectedEmailTool');
        if (toolInput) toolInput.value = '';
        
        const credentialsForm = document.getElementById('emailCredentialsForm');
        if (credentialsForm) credentialsForm.classList.add('hidden');
        
        document.querySelectorAll('.email-tool-card').forEach(card => {
            card.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
            if (card.dataset.tool === 'none') {
                card.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
            }
        });
        
        this.selectedTool = null;
        this.credentials = {};
    },
    
    getFormData: function() {
        if (!this.selectedTool || this.selectedTool === 'none') {
            return null;
        }
        
        return {
            tool_name: this.selectedTool,
            api_key: document.getElementById('emailApiKey')?.value || '',
            api_secret: document.getElementById('emailApiSecret')?.value || null,
            api_url: document.getElementById('emailApiUrl')?.value || null,
            tag_id: document.getElementById('emailDefaultTag')?.value || null
        };
    }
};

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('emailToolsContainer')) {
        EmailToolIntegration.init();
    }
});
