/**
 * Leadbusiness - E-Mail-Tool Integration
 * 
 * Handles email tool selection, credential input, and connection testing
 * for onboarding wizard integration
 */

const EmailToolIntegration = {
    
    selectedTool: null,
    connectionTested: false,
    tags: [],
    
    // Tool-Konfigurationen
    tools: {
        klicktipp: {
            name: 'KlickTipp',
            fields: [
                { name: 'api_key', label: 'Benutzername', type: 'text', placeholder: 'Ihr KlickTipp Benutzername', required: true },
                { name: 'api_secret', label: 'Passwort / API-Key', type: 'password', placeholder: 'Ihr KlickTipp Passwort', required: true }
            ],
            helpText: 'Nutzen Sie Ihre KlickTipp Login-Daten (Benutzername und Passwort).'
        },
        quentn: {
            name: 'Quentn',
            fields: [
                { name: 'api_key', label: 'API-Key', type: 'text', placeholder: 'Ihr Quentn API-Key', required: true },
                { name: 'api_secret', label: 'API-Secret', type: 'password', placeholder: 'Ihr Quentn API-Secret', required: true },
                { name: 'api_url', label: 'Subdomain', type: 'text', placeholder: 'ihre-firma (ohne .quentn.com)', required: true }
            ],
            helpText: 'API-Zugangsdaten finden Sie unter Einstellungen → API in Quentn.'
        },
        cleverreach: {
            name: 'CleverReach',
            fields: [
                { name: 'api_key', label: 'API-Token', type: 'text', placeholder: 'Ihr CleverReach API-Token', required: true }
            ],
            helpText: 'Erstellen Sie einen API-Token unter Mein Account → Extras → REST API.'
        }
    },
    
    /**
     * Initialize the email tool integration
     */
    init: function() {
        console.log('EmailToolIntegration initialized');
        this.bindToolCards();
        this.bindTestButton();
    },
    
    /**
     * Bind click events to tool cards
     */
    bindToolCards: function() {
        const toolCards = document.querySelectorAll('.tool-card');
        
        toolCards.forEach(card => {
            card.addEventListener('click', () => {
                const tool = card.dataset.tool;
                this.selectTool(tool);
                
                // Visual selection
                toolCards.forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
            });
        });
    },
    
    /**
     * Bind test connection button
     */
    bindTestButton: function() {
        const testBtn = document.getElementById('testConnectionBtn');
        if (testBtn) {
            testBtn.addEventListener('click', () => this.testConnection());
        }
    },
    
    /**
     * Handle tool selection
     */
    selectTool: function(tool) {
        this.selectedTool = tool;
        this.connectionTested = false;
        
        // Update hidden field
        const hiddenField = document.getElementById('selectedEmailTool');
        if (hiddenField) {
            hiddenField.value = tool;
        }
        
        // Handle skip option
        if (tool === 'skip') {
            const credentialsDiv = document.getElementById('emailToolCredentials');
            if (credentialsDiv) {
                credentialsDiv.classList.add('hidden');
            }
            return;
        }
        
        // Show credentials form
        this.renderCredentialsForm(tool);
    },
    
    /**
     * Render the credentials form for selected tool
     */
    renderCredentialsForm: function(tool) {
        const config = this.tools[tool];
        if (!config) return;
        
        const container = document.getElementById('emailToolCredentials');
        const fieldsContainer = document.getElementById('credentialsFields');
        const title = document.getElementById('toolCredentialsTitle');
        
        if (!container || !fieldsContainer || !title) return;
        
        // Update title
        title.textContent = `${config.name} verbinden`;
        
        // Clear existing fields
        fieldsContainer.innerHTML = '';
        
        // Add help text
        if (config.helpText) {
            const helpDiv = document.createElement('div');
            helpDiv.className = 'bg-gray-50 dark:bg-slate-700/50 rounded-lg p-3 mb-4';
            helpDiv.innerHTML = `
                <p class="text-xs text-gray-600 dark:text-slate-400">
                    <i class="fas fa-info-circle text-primary-500 mr-1"></i>
                    ${config.helpText}
                </p>
            `;
            fieldsContainer.appendChild(helpDiv);
        }
        
        // Add input fields
        config.fields.forEach(field => {
            const fieldDiv = document.createElement('div');
            fieldDiv.className = 'mb-4';
            fieldDiv.innerHTML = `
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">${field.label} ${field.required ? '*' : ''}</label>
                <input type="${field.type}" 
                       id="emailTool_${field.name}"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500"
                       placeholder="${field.placeholder}"
                       ${field.required ? 'required' : ''}>
            `;
            fieldsContainer.appendChild(fieldDiv);
        });
        
        // Show container
        container.classList.remove('hidden');
        
        // Hide tag selection and connection status
        const tagSelection = document.getElementById('tagSelection');
        const connectionStatus = document.getElementById('connectionStatus');
        if (tagSelection) tagSelection.classList.add('hidden');
        if (connectionStatus) connectionStatus.classList.add('hidden');
    },
    
    /**
     * Test the API connection
     */
    testConnection: async function() {
        const tool = this.selectedTool;
        const config = this.tools[tool];
        if (!tool || !config) return;
        
        const statusDiv = document.getElementById('connectionStatus');
        const testBtn = document.getElementById('testConnectionBtn');
        
        // Collect credentials
        const credentials = {
            tool: tool
        };
        config.fields.forEach(field => {
            const input = document.getElementById(`emailTool_${field.name}`);
            if (input) {
                credentials[field.name] = input.value;
            }
        });
        
        // Validate required fields
        for (const field of config.fields) {
            if (field.required && !credentials[field.name]) {
                this.showStatus('error', `Bitte füllen Sie alle Pflichtfelder aus.`);
                return;
            }
        }
        
        // Show loading state
        if (testBtn) {
            testBtn.disabled = true;
            testBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Teste...';
        }
        if (statusDiv) {
            statusDiv.classList.remove('hidden');
        }
        this.showStatus('loading', 'Verbindung wird getestet...');
        
        try {
            // Send request with action in URL
            const response = await fetch('/api/email-integration.php?action=test_connection', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(credentials)
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.connectionTested = true;
                this.showStatus('success', result.message || 'Verbindung erfolgreich!');
                
                // Store credentials in hidden fields
                const apiKeyField = document.getElementById('emailToolApiKey');
                const apiSecretField = document.getElementById('emailToolApiSecret');
                const apiUrlField = document.getElementById('emailToolApiUrl');
                
                if (apiKeyField) apiKeyField.value = credentials.api_key || '';
                if (apiSecretField) apiSecretField.value = credentials.api_secret || '';
                if (apiUrlField) apiUrlField.value = credentials.api_url || '';
                
                // Load tags
                this.loadTags(tool, credentials);
            } else {
                this.showStatus('error', result.error || 'Verbindung fehlgeschlagen');
            }
        } catch (error) {
            console.error('Connection test error:', error);
            this.showStatus('error', 'Verbindungstest fehlgeschlagen. Bitte prüfen Sie Ihre Eingaben.');
        }
        
        // Reset button
        if (testBtn) {
            testBtn.disabled = false;
            testBtn.innerHTML = '<i class="fas fa-plug mr-2"></i>Verbindung testen';
        }
    },
    
    /**
     * Load tags from the email tool
     */
    loadTags: async function(tool, credentials) {
        try {
            const response = await fetch('/api/email-integration.php?action=get_tags', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    tool: tool,
                    api_key: credentials.api_key,
                    api_secret: credentials.api_secret,
                    api_url: credentials.api_url
                })
            });
            
            const result = await response.json();
            
            if (result.success && result.tags) {
                this.tags = result.tags;
                this.renderTagSelector(result.tags);
            }
        } catch (error) {
            console.error('Error loading tags:', error);
        }
    },
    
    /**
     * Render tag selector dropdown
     */
    renderTagSelector: function(tags) {
        const container = document.getElementById('tagSelection');
        const select = document.getElementById('tagSelect');
        
        if (!container || !select) return;
        
        // Clear existing options (except first)
        while (select.options.length > 1) {
            select.remove(1);
        }
        
        // Add tags
        tags.forEach(tag => {
            const option = document.createElement('option');
            option.value = tag.id;
            option.textContent = tag.name;
            select.appendChild(option);
        });
        
        // Bind change event
        select.addEventListener('change', () => {
            const selectedOption = select.options[select.selectedIndex];
            const tagNameField = document.getElementById('emailToolTagName');
            if (tagNameField) {
                tagNameField.value = selectedOption.text !== '-- Kein Tag --' ? selectedOption.text : '';
            }
        });
        
        // Show container if tags available
        if (tags.length > 0) {
            container.classList.remove('hidden');
        }
    },
    
    /**
     * Show status message
     */
    showStatus: function(type, message) {
        const statusDiv = document.getElementById('connectionStatus');
        if (!statusDiv) return;
        
        let bgClass, textClass, icon;
        switch (type) {
            case 'success':
                bgClass = 'bg-green-50 dark:bg-green-900/30 border-green-200 dark:border-green-800';
                textClass = 'text-green-700 dark:text-green-300';
                icon = 'fas fa-check-circle';
                break;
            case 'error':
                bgClass = 'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800';
                textClass = 'text-red-700 dark:text-red-300';
                icon = 'fas fa-times-circle';
                break;
            case 'loading':
                bgClass = 'bg-blue-50 dark:bg-blue-900/30 border-blue-200 dark:border-blue-800';
                textClass = 'text-blue-700 dark:text-blue-300';
                icon = 'fas fa-spinner fa-spin';
                break;
            default:
                bgClass = 'bg-gray-50 dark:bg-slate-700 border-gray-200 dark:border-slate-600';
                textClass = 'text-gray-700 dark:text-slate-300';
                icon = 'fas fa-info-circle';
        }
        
        statusDiv.className = `mt-4 p-3 rounded-lg border ${bgClass}`;
        statusDiv.innerHTML = `
            <div class="flex items-center gap-2 ${textClass}">
                <i class="${icon}"></i>
                <span class="text-sm">${message}</span>
            </div>
        `;
        statusDiv.classList.remove('hidden');
    },
    
    /**
     * Get form data for submission
     */
    getFormData: function() {
        if (!this.selectedTool || this.selectedTool === 'skip') {
            return null;
        }
        
        const apiKeyField = document.getElementById('emailToolApiKey');
        const apiSecretField = document.getElementById('emailToolApiSecret');
        const apiUrlField = document.getElementById('emailToolApiUrl');
        const tagSelect = document.getElementById('tagSelect');
        const tagNameField = document.getElementById('emailToolTagName');
        
        return {
            tool: this.selectedTool,
            api_key: apiKeyField?.value || '',
            api_secret: apiSecretField?.value || '',
            api_url: apiUrlField?.value || '',
            tag_id: tagSelect?.value || '',
            tag_name: tagNameField?.value || ''
        };
    },
    
    /**
     * Skip this step
     */
    skip: function() {
        this.selectedTool = 'skip';
        const hiddenField = document.getElementById('selectedEmailTool');
        if (hiddenField) {
            hiddenField.value = 'skip';
        }
        const credentialsDiv = document.getElementById('emailToolCredentials');
        if (credentialsDiv) {
            credentialsDiv.classList.add('hidden');
        }
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if we're on the email tool step
    const emailToolContainer = document.getElementById('emailToolContainer');
    if (emailToolContainer) {
        EmailToolIntegration.init();
    }
});
