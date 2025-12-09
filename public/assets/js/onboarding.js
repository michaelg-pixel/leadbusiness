/**
 * Leadbusiness - Onboarding Wizard JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Elements
    const form = document.getElementById('onboardingForm');
    const panels = document.querySelectorAll('.wizard-panel');
    const stepItems = document.querySelectorAll('.step-item');
    const progressBar = document.getElementById('progressBar');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    // State
    let currentStep = 1;
    const totalSteps = 8;
    let formData = {};
    
    // Industry Card Selection
    const industryCards = document.querySelectorAll('.industry-card');
    industryCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove selection from all
            industryCards.forEach(c => c.classList.remove('selected'));
            // Add selection to clicked
            this.classList.add('selected');
            // Check the radio
            const radio = this.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
            
            // Update backgrounds for selected industry
            updateBackgroundsForIndustry(radio.value);
        });
    });
    
    // Logo Upload
    const logoDropzone = document.getElementById('logoDropzone');
    const logoInput = document.getElementById('logoInput');
    const logoPreview = document.getElementById('logoPreview');
    const logoPlaceholder = document.getElementById('logoPlaceholder');
    
    if (logoDropzone) {
        logoDropzone.addEventListener('click', () => logoInput.click());
        
        logoDropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            logoDropzone.classList.add('border-primary-500', 'bg-primary-50');
        });
        
        logoDropzone.addEventListener('dragleave', () => {
            logoDropzone.classList.remove('border-primary-500', 'bg-primary-50');
        });
        
        logoDropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            logoDropzone.classList.remove('border-primary-500', 'bg-primary-50');
            
            if (e.dataTransfer.files.length) {
                logoInput.files = e.dataTransfer.files;
                handleLogoUpload(e.dataTransfer.files[0]);
            }
        });
        
        logoInput.addEventListener('change', () => {
            if (logoInput.files.length) {
                handleLogoUpload(logoInput.files[0]);
            }
        });
    }
    
    function handleLogoUpload(file) {
        if (!file.type.startsWith('image/')) {
            alert('Bitte nur Bilddateien hochladen.');
            return;
        }
        
        if (file.size > 2 * 1024 * 1024) {
            alert('Die Datei ist zu groß. Maximal 2MB erlaubt.');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = (e) => {
            logoPreview.querySelector('img').src = e.target.result;
            logoPreview.classList.remove('hidden');
            logoPlaceholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
    
    // Update backgrounds based on industry
    function updateBackgroundsForIndustry(industry) {
        const container = document.getElementById('backgroundsContainer');
        if (!container) return;
        
        container.innerHTML = '';
        
        let backgrounds = backgroundsByIndustry[industry] || backgroundsByIndustry['allgemein'] || [];
        
        backgrounds.forEach((bg, index) => {
            const card = document.createElement('div');
            card.className = 'background-card relative rounded-xl overflow-hidden cursor-pointer border-4 border-transparent hover:border-primary-300';
            card.dataset.id = bg.id;
            
            if (index === 0) {
                card.classList.add('border-primary-500');
                document.getElementById('selectedBackground').value = bg.id;
            }
            
            card.innerHTML = `
                <img src="/assets/backgrounds/${bg.industry}/${bg.filename}" 
                     alt="${bg.display_name}" 
                     class="w-full h-32 object-cover">
                <div class="absolute inset-0 bg-black/40"></div>
                <div class="absolute bottom-0 left-0 right-0 p-2 bg-gradient-to-t from-black/60">
                    <span class="text-white text-sm font-medium">${bg.display_name}</span>
                </div>
                ${index === 0 ? '<div class="absolute top-2 right-2 w-6 h-6 bg-primary-500 rounded-full flex items-center justify-center"><i class="fas fa-check text-white text-xs"></i></div>' : ''}
            `;
            
            card.addEventListener('click', () => selectBackground(card, bg.id));
            container.appendChild(card);
        });
    }
    
    function selectBackground(card, id) {
        // Remove selection from all
        document.querySelectorAll('.background-card').forEach(c => {
            c.classList.remove('border-primary-500');
            const check = c.querySelector('.fa-check');
            if (check) check.parentElement.remove();
        });
        
        // Add selection to clicked
        card.classList.add('border-primary-500');
        card.insertAdjacentHTML('beforeend', 
            '<div class="absolute top-2 right-2 w-6 h-6 bg-primary-500 rounded-full flex items-center justify-center"><i class="fas fa-check text-white text-xs"></i></div>'
        );
        
        document.getElementById('selectedBackground').value = id;
    }
    
    // Subdomain Input
    const subdomainInput = document.getElementById('subdomainInput');
    const subdomainStatus = document.getElementById('subdomainStatus');
    const previewUrl = document.getElementById('previewUrl');
    
    if (subdomainInput) {
        let debounceTimer;
        
        subdomainInput.addEventListener('input', function() {
            // Sanitize input
            let value = this.value.toLowerCase()
                .replace(/[^a-z0-9-]/g, '')
                .replace(/--+/g, '-')
                .replace(/^-|-$/g, '');
            
            this.value = value;
            
            // Update preview
            if (previewUrl) {
                previewUrl.textContent = (value || 'ihre-firma') + '.empfohlen.de';
            }
            
            // Check availability (debounced)
            clearTimeout(debounceTimer);
            if (value.length >= 3) {
                debounceTimer = setTimeout(() => checkSubdomainAvailability(value), 500);
            } else {
                subdomainStatus.classList.add('hidden');
            }
        });
    }
    
    async function checkSubdomainAvailability(subdomain) {
        try {
            const response = await fetch('/api/check-subdomain.php?subdomain=' + encodeURIComponent(subdomain));
            const data = await response.json();
            
            subdomainStatus.classList.remove('hidden');
            const span = subdomainStatus.querySelector('span');
            
            if (data.available) {
                span.className = 'text-sm text-green-600';
                span.innerHTML = '<i class="fas fa-check-circle mr-1"></i>Diese Subdomain ist verfügbar!';
            } else {
                span.className = 'text-sm text-red-600';
                span.innerHTML = '<i class="fas fa-times-circle mr-1"></i>Diese Subdomain ist bereits vergeben.';
            }
        } catch (error) {
            console.error('Error checking subdomain:', error);
        }
    }
    
    // Add Reward Button (Professional only)
    const addRewardBtn = document.getElementById('addRewardBtn');
    let rewardCount = 3;
    
    if (addRewardBtn) {
        addRewardBtn.addEventListener('click', function() {
            if (rewardCount >= 5) {
                alert('Maximal 5 Belohnungsstufen möglich.');
                return;
            }
            
            rewardCount++;
            const container = document.getElementById('rewardsContainer');
            
            const newReward = document.createElement('div');
            newReward.className = 'reward-level border rounded-xl p-6';
            newReward.innerHTML = `
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center text-primary-600 font-bold">
                        ${rewardCount}
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold">Stufe ${rewardCount}</h3>
                        <p class="text-sm text-gray-500">Nach <input type="number" name="reward_${rewardCount}_threshold" value="${10 + (rewardCount - 3) * 5}" min="1" max="100" class="w-16 px-2 py-1 border rounded text-center"> erfolgreichen Empfehlungen</p>
                    </div>
                    <button type="button" class="remove-reward text-red-500 hover:text-red-700" onclick="this.closest('.reward-level').remove(); rewardCount--;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Belohnungstyp</label>
                        <select name="reward_${rewardCount}_type" class="w-full px-3 py-2 border rounded-lg">
                            <option value="discount">Rabatt (%)</option>
                            <option value="coupon_code">Gutschein-Code</option>
                            <option value="free_product">Gratis-Produkt</option>
                            <option value="free_service">Gratis-Service</option>
                            <option value="voucher">Wertgutschein (€)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Beschreibung</label>
                        <input type="text" name="reward_${rewardCount}_description" 
                            class="w-full px-3 py-2 border rounded-lg"
                            placeholder="z.B. VIP-Status">
                    </div>
                </div>
            `;
            
            container.appendChild(newReward);
        });
    }
    
    // Navigation Functions
    function updateProgress() {
        const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
        progressBar.style.width = progress + '%';
        
        stepItems.forEach((item, index) => {
            const stepNum = index + 1;
            item.classList.remove('active', 'completed');
            
            if (stepNum < currentStep) {
                item.classList.add('completed');
            } else if (stepNum === currentStep) {
                item.classList.add('active');
            }
        });
    }
    
    function showPanel(step) {
        panels.forEach(panel => {
            panel.classList.remove('active');
            if (parseInt(panel.dataset.panel) === step) {
                panel.classList.add('active');
            }
        });
        
        // Update buttons
        prevBtn.classList.toggle('hidden', step === 1);
        nextBtn.classList.toggle('hidden', step === totalSteps);
        submitBtn.classList.toggle('hidden', step !== totalSteps);
        
        // Generate summary on last step
        if (step === totalSteps) {
            generateSummary();
        }
        
        updateProgress();
    }
    
    function validateStep(step) {
        const panel = document.querySelector(`.wizard-panel[data-panel="${step}"]`);
        const inputs = panel.querySelectorAll('input[required], select[required]');
        
        let valid = true;
        inputs.forEach(input => {
            if (!input.checkValidity()) {
                input.classList.add('border-red-500');
                valid = false;
            } else {
                input.classList.remove('border-red-500');
            }
        });
        
        // Special validations
        if (step === 1) {
            const selectedIndustry = document.querySelector('input[name="industry"]:checked');
            if (!selectedIndustry) {
                alert('Bitte wählen Sie eine Branche aus.');
                return false;
            }
        }
        
        if (step === 3) {
            const password = form.querySelector('input[name="password"]').value;
            const passwordConfirm = form.querySelector('input[name="password_confirm"]').value;
            
            if (password !== passwordConfirm) {
                alert('Die Passwörter stimmen nicht überein.');
                return false;
            }
            
            if (password.length < 8) {
                alert('Das Passwort muss mindestens 8 Zeichen lang sein.');
                return false;
            }
        }
        
        if (step === 7) {
            const subdomain = subdomainInput.value;
            if (subdomain.length < 3) {
                alert('Die Subdomain muss mindestens 3 Zeichen lang sein.');
                return false;
            }
        }
        
        return valid;
    }
    
    function generateSummary() {
        const container = document.getElementById('summaryContainer');
        
        const industry = form.querySelector('input[name="industry"]:checked');
        const companyName = form.querySelector('input[name="company_name"]').value;
        const contactName = form.querySelector('input[name="contact_name"]').value;
        const email = form.querySelector('input[name="email"]').value;
        const subdomain = form.querySelector('input[name="subdomain"]').value;
        const plan = form.querySelector('input[name="plan"]').value;
        
        container.innerHTML = `
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Unternehmen</h4>
                    <p class="text-gray-600">${companyName || '-'}</p>
                    <p class="text-sm text-gray-500">${industry ? industry.value : '-'}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Kontakt</h4>
                    <p class="text-gray-600">${contactName}</p>
                    <p class="text-sm text-gray-500">${email}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Empfehlungsseite</h4>
                    <p class="text-gray-600">${subdomain}.empfohlen.de</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Tarif</h4>
                    <p class="text-gray-600">${plan === 'professional' ? 'Professional' : 'Starter'}</p>
                </div>
            </div>
        `;
    }
    
    // Event Listeners
    prevBtn.addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            showPanel(currentStep);
        }
    });
    
    nextBtn.addEventListener('click', () => {
        if (validateStep(currentStep)) {
            if (currentStep < totalSteps) {
                currentStep++;
                showPanel(currentStep);
                window.scrollTo(0, 0);
            }
        }
    });
    
    // Form Submit
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!validateStep(currentStep)) {
            return;
        }
        
        const termsCheckbox = document.getElementById('acceptTerms');
        if (!termsCheckbox.checked) {
            alert('Bitte akzeptieren Sie die AGB und Datenschutzerklärung.');
            return;
        }
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Wird eingerichtet...';
        
        try {
            const formData = new FormData(form);
            
            const response = await fetch('/onboarding/process.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Redirect to success page or dashboard
                window.location.href = result.redirect || '/dashboard';
            } else {
                alert(result.error || 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-rocket mr-2"></i>Einrichtung starten';
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-rocket mr-2"></i>Einrichtung starten';
        }
    });
    
    // Initialize
    showPanel(1);
    
    // Auto-fill backgrounds for default industry
    const defaultIndustry = document.querySelector('input[name="industry"]:checked');
    if (defaultIndustry) {
        updateBackgroundsForIndustry(defaultIndustry.value);
    } else {
        updateBackgroundsForIndustry('allgemein');
    }
});
