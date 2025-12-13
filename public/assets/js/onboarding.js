/**
 * Leadbusiness - Onboarding Wizard JavaScript
 * 
 * Verbesserte Version mit Touch-Support und Mobile-Optimierung
 * Fixed: E-Mail-Tool-Schritt (6) wird jetzt korrekt behandelt
 */

document.addEventListener('DOMContentLoaded', function() {
    
    console.log('Onboarding Wizard initialized');
    
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
    const totalSteps = 9; // Korrigiert: 9 Schritte inkl. E-Mail-Tool
    let formData = {};
    let showEmailToolStep = false; // Wird basierend auf Branche gesetzt
    
    // ================================
    // INDUSTRY CARD SELECTION
    // ================================
    
    function initIndustryCards() {
        const industryCards = document.querySelectorAll('.industry-card');
        
        console.log('Found industry cards:', industryCards.length);
        
        industryCards.forEach(card => {
            // Remove existing listeners to prevent duplicates
            const newCard = card.cloneNode(true);
            card.parentNode.replaceChild(newCard, card);
            
            // Add click handler
            newCard.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Industry card clicked:', this.dataset.industry);
                
                // Remove selection from all cards
                document.querySelectorAll('.industry-card').forEach(c => {
                    c.classList.remove('selected');
                });
                
                // Add selection to clicked card
                this.classList.add('selected');
                
                // Check the radio input
                const radio = this.querySelector('.industry-radio');
                if (radio) {
                    radio.checked = true;
                    console.log('Radio checked:', radio.value);
                }
                
                // Update backgrounds for selected industry
                const industry = this.dataset.industry;
                if (industry) {
                    updateBackgroundsForIndustry(industry);
                    updateEmailToolStepVisibility(industry);
                }
            });
            
            // Touch support
            newCard.addEventListener('touchend', function(e) {
                // Trigger click on touch
                this.click();
            }, { passive: true });
        });
    }
    
    // Initialize industry cards
    initIndustryCards();
    
    // ================================
    // EMAIL TOOL STEP VISIBILITY
    // ================================
    
    function updateEmailToolStepVisibility(industry) {
        // Branchen die den E-Mail-Tool-Schritt sehen
        const emailToolBranches = ['onlinemarketing', 'coach', 'onlineshop', 'newsletter', 'software'];
        showEmailToolStep = emailToolBranches.includes(industry);
        
        console.log('Email tool step visible:', showEmailToolStep, 'for industry:', industry);
        
        // Step 6 in der Progress-Leiste ein-/ausblenden
        const step6Item = document.querySelector('.step-item[data-step="6"]');
        if (step6Item) {
            if (showEmailToolStep) {
                step6Item.classList.remove('hidden');
            } else {
                step6Item.classList.add('hidden');
            }
        }
        
        updateProgress();
    }
    
    // ================================
    // LOGO UPLOAD
    // ================================
    
    const logoDropzone = document.getElementById('logoDropzone');
    const logoInput = document.getElementById('logoInput');
    const logoPreview = document.getElementById('logoPreview');
    const logoPlaceholder = document.getElementById('logoPlaceholder');
    
    if (logoDropzone && logoInput) {
        logoDropzone.addEventListener('click', function(e) {
            e.preventDefault();
            logoInput.click();
        });
        
        logoDropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-primary-500', 'bg-primary-50');
        });
        
        logoDropzone.addEventListener('dragleave', function() {
            this.classList.remove('border-primary-500', 'bg-primary-50');
        });
        
        logoDropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-primary-500', 'bg-primary-50');
            
            if (e.dataTransfer.files.length) {
                logoInput.files = e.dataTransfer.files;
                handleLogoUpload(e.dataTransfer.files[0]);
            }
        });
        
        logoInput.addEventListener('change', function() {
            if (this.files.length) {
                handleLogoUpload(this.files[0]);
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
        reader.onload = function(e) {
            if (logoPreview && logoPlaceholder) {
                logoPreview.querySelector('img').src = e.target.result;
                logoPreview.classList.remove('hidden');
                logoPlaceholder.classList.add('hidden');
            }
        };
        reader.readAsDataURL(file);
    }
    
    // ================================
    // BACKGROUND SELECTION
    // ================================
    
    function updateBackgroundsForIndustry(industry) {
        const container = document.getElementById('backgroundsContainer');
        if (!container) return;
        
        console.log('Updating backgrounds for industry:', industry);
        
        // Get backgrounds from global variable (set by PHP)
        let backgrounds = [];
        if (typeof backgroundsByIndustry !== 'undefined') {
            backgrounds = backgroundsByIndustry[industry] || backgroundsByIndustry['allgemein'] || [];
        }
        
        console.log('Found backgrounds:', backgrounds.length);
        
        container.innerHTML = '';
        
        if (backgrounds.length === 0) {
            container.innerHTML = '<p class="col-span-3 text-center text-gray-500 py-8">Keine Hintergrundbilder für diese Branche verfügbar.</p>';
            return;
        }
        
        backgrounds.forEach(function(bg, index) {
            const card = document.createElement('div');
            card.className = 'background-card relative rounded-xl overflow-hidden cursor-pointer border-4 transition-all';
            card.dataset.id = bg.id;
            
            // First one is selected by default
            if (index === 0) {
                card.classList.add('border-primary-500');
                document.getElementById('selectedBackground').value = bg.id;
            } else {
                card.classList.add('border-transparent', 'hover:border-primary-300');
            }
            
            card.innerHTML = `
                <img src="/assets/backgrounds/${bg.industry}/${bg.filename}" 
                     alt="${bg.display_name}" 
                     class="w-full h-24 sm:h-32 object-cover"
                     onerror="this.src='/assets/images/placeholder-bg.jpg'; this.onerror=null;">
                <div class="absolute inset-0 bg-black/40"></div>
                <div class="absolute bottom-0 left-0 right-0 p-2 bg-gradient-to-t from-black/60">
                    <span class="text-white text-xs sm:text-sm font-medium">${bg.display_name}</span>
                </div>
                ${index === 0 ? '<div class="absolute top-2 right-2 w-5 h-5 sm:w-6 sm:h-6 bg-primary-500 rounded-full flex items-center justify-center"><i class="fas fa-check text-white text-xs"></i></div>' : ''}
            `;
            
            card.addEventListener('click', function() {
                selectBackground(this, bg.id);
            });
            
            container.appendChild(card);
        });
    }
    
    function selectBackground(card, id) {
        // Remove selection from all
        document.querySelectorAll('.background-card').forEach(function(c) {
            c.classList.remove('border-primary-500');
            c.classList.add('border-transparent');
            const check = c.querySelector('.fa-check');
            if (check) check.parentElement.remove();
        });
        
        // Add selection to clicked
        card.classList.remove('border-transparent');
        card.classList.add('border-primary-500');
        card.insertAdjacentHTML('beforeend', 
            '<div class="absolute top-2 right-2 w-5 h-5 sm:w-6 sm:h-6 bg-primary-500 rounded-full flex items-center justify-center"><i class="fas fa-check text-white text-xs"></i></div>'
        );
        
        document.getElementById('selectedBackground').value = id;
    }
    
    // ================================
    // SUBDOMAIN INPUT
    // ================================
    
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
                previewUrl.textContent = (value || 'ihre-firma') + '.empfehlungen.cloud';
            }
            
            // Check availability (debounced)
            clearTimeout(debounceTimer);
            if (value.length >= 3) {
                debounceTimer = setTimeout(function() {
                    checkSubdomainAvailability(value);
                }, 500);
            } else {
                if (subdomainStatus) subdomainStatus.classList.add('hidden');
            }
        });
    }
    
    async function checkSubdomainAvailability(subdomain) {
        try {
            const response = await fetch('/api/check-subdomain.php?subdomain=' + encodeURIComponent(subdomain));
            const data = await response.json();
            
            if (subdomainStatus) {
                subdomainStatus.classList.remove('hidden');
                const span = subdomainStatus.querySelector('span');
                
                if (data.available) {
                    span.className = 'text-sm text-green-600';
                    span.innerHTML = '<i class="fas fa-check-circle mr-1"></i>Diese Subdomain ist verfügbar!';
                } else {
                    span.className = 'text-sm text-red-600';
                    span.innerHTML = '<i class="fas fa-times-circle mr-1"></i>Diese Subdomain ist bereits vergeben.';
                }
            }
        } catch (error) {
            console.error('Error checking subdomain:', error);
        }
    }
    
    // ================================
    // ADD REWARD BUTTON (Professional only)
    // ================================
    
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
            newReward.className = 'reward-level border rounded-xl p-4 sm:p-6';
            newReward.innerHTML = `
                <div class="flex items-center gap-3 sm:gap-4 mb-4">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-primary-100 rounded-full flex items-center justify-center text-primary-600 font-bold text-sm sm:text-base">
                        ${rewardCount}
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-sm sm:text-base">Stufe ${rewardCount}</h3>
                        <p class="text-xs sm:text-sm text-gray-500">Nach <input type="number" name="reward_${rewardCount}_threshold" value="${10 + (rewardCount - 3) * 5}" min="1" max="100" class="w-12 sm:w-16 px-2 py-1 border rounded text-center text-sm"> erfolgreichen Empfehlungen</p>
                    </div>
                    <button type="button" class="remove-reward text-red-500 hover:text-red-700 p-2" title="Entfernen">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Belohnungstyp</label>
                        <select name="reward_${rewardCount}_type" class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="discount">Rabatt (%)</option>
                            <option value="coupon_code">Gutschein-Code</option>
                            <option value="free_product">Gratis-Produkt</option>
                            <option value="free_service">Gratis-Service</option>
                            <option value="voucher">Wertgutschein (€)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Beschreibung</label>
                        <input type="text" name="reward_${rewardCount}_description" 
                            class="w-full px-3 py-2 border rounded-lg text-sm"
                            placeholder="z.B. VIP-Status">
                    </div>
                </div>
            `;
            
            // Add remove handler
            const removeBtn = newReward.querySelector('.remove-reward');
            removeBtn.addEventListener('click', function() {
                newReward.remove();
                rewardCount--;
            });
            
            container.appendChild(newReward);
        });
    }
    
    // ================================
    // NAVIGATION FUNCTIONS
    // ================================
    
    function getVisibleSteps() {
        // Gibt die Liste der sichtbaren Schritte zurück
        let steps = [1, 2, 3, 4, 5];
        
        if (showEmailToolStep) {
            steps.push(6);
        }
        
        steps.push(7, 8, 9);
        
        return steps;
    }
    
    function getNextStep(current) {
        const visibleSteps = getVisibleSteps();
        const currentIndex = visibleSteps.indexOf(current);
        if (currentIndex < visibleSteps.length - 1) {
            return visibleSteps[currentIndex + 1];
        }
        return current;
    }
    
    function getPrevStep(current) {
        const visibleSteps = getVisibleSteps();
        const currentIndex = visibleSteps.indexOf(current);
        if (currentIndex > 0) {
            return visibleSteps[currentIndex - 1];
        }
        return current;
    }
    
    function isLastStep(step) {
        const visibleSteps = getVisibleSteps();
        return step === visibleSteps[visibleSteps.length - 1];
    }
    
    function isFirstStep(step) {
        const visibleSteps = getVisibleSteps();
        return step === visibleSteps[0];
    }
    
    function updateProgress() {
        const visibleSteps = getVisibleSteps();
        const currentIndex = visibleSteps.indexOf(currentStep);
        const progress = (currentIndex / (visibleSteps.length - 1)) * 100;
        
        if (progressBar) {
            progressBar.style.width = progress + '%';
        }
        
        stepItems.forEach(function(item) {
            const stepNum = parseInt(item.dataset.step);
            item.classList.remove('active', 'completed');
            
            // Schritt 6 Sichtbarkeit
            if (stepNum === 6) {
                if (showEmailToolStep) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            }
            
            if (stepNum < currentStep && visibleSteps.includes(stepNum)) {
                item.classList.add('completed');
            } else if (stepNum === currentStep) {
                item.classList.add('active');
            }
        });
    }
    
    function showPanel(step) {
        panels.forEach(function(panel) {
            panel.classList.remove('active');
            if (parseInt(panel.dataset.panel) === step) {
                panel.classList.add('active');
            }
        });
        
        // Update buttons
        if (prevBtn) prevBtn.classList.toggle('hidden', isFirstStep(step));
        if (nextBtn) nextBtn.classList.toggle('hidden', isLastStep(step));
        if (submitBtn) submitBtn.classList.toggle('hidden', !isLastStep(step));
        
        // Generate summary on last step
        if (isLastStep(step)) {
            generateSummary();
        }
        
        updateProgress();
    }
    
    function validateStep(step) {
        const panel = document.querySelector(`.wizard-panel[data-panel="${step}"]`);
        if (!panel) return true;
        
        // Schritt 6 (E-Mail Tool) - immer gültig, da optional
        if (step === 6) {
            const selectedTool = document.getElementById('selectedEmailTool');
            // Wenn "skip" gewählt oder gar nichts gewählt wurde, ist es OK
            if (!selectedTool || !selectedTool.value || selectedTool.value === 'skip' || selectedTool.value === '') {
                return true;
            }
            // Wenn ein Tool gewählt wurde, prüfen wir nicht ob die Verbindung getestet wurde
            // Das ist optional - der Benutzer kann die Einrichtung später abschließen
            return true;
        }
        
        const inputs = panel.querySelectorAll('input[required], select[required]');
        
        let valid = true;
        inputs.forEach(function(input) {
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
            const password = form.querySelector('input[name="password"]');
            const passwordConfirm = form.querySelector('input[name="password_confirm"]');
            
            if (password && passwordConfirm) {
                if (password.value !== passwordConfirm.value) {
                    alert('Die Passwörter stimmen nicht überein.');
                    return false;
                }
                
                if (password.value.length < 8) {
                    alert('Das Passwort muss mindestens 8 Zeichen lang sein.');
                    return false;
                }
            }
        }
        
        if (step === 8) {
            if (subdomainInput && subdomainInput.value.length < 3) {
                alert('Die Subdomain muss mindestens 3 Zeichen lang sein.');
                return false;
            }
        }
        
        return valid;
    }
    
    function generateSummary() {
        const container = document.getElementById('summaryContainer');
        if (!container) return;
        
        const industry = form.querySelector('input[name="industry"]:checked');
        const companyName = form.querySelector('input[name="company_name"]');
        const contactName = form.querySelector('input[name="contact_name"]');
        const email = form.querySelector('input[name="email"]');
        const subdomain = form.querySelector('input[name="subdomain"]');
        const plan = form.querySelector('input[name="plan"]');
        const emailTool = document.getElementById('selectedEmailTool');
        
        // E-Mail-Tool Info
        let emailToolInfo = 'Nicht verbunden';
        if (emailTool && emailTool.value && emailTool.value !== 'skip' && emailTool.value !== '') {
            const toolNames = {
                'klicktipp': 'KlickTipp',
                'quentn': 'Quentn',
                'cleverreach': 'CleverReach'
            };
            emailToolInfo = toolNames[emailTool.value] || emailTool.value;
        }
        
        container.innerHTML = `
            <div class="grid sm:grid-cols-2 gap-4 sm:gap-6">
                <div>
                    <h4 class="font-semibold text-gray-700 dark:text-slate-300 mb-1 text-sm sm:text-base">Unternehmen</h4>
                    <p class="text-gray-600 dark:text-slate-400 text-sm sm:text-base">${companyName ? companyName.value : '-'}</p>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-slate-500">${industry ? industry.value : '-'}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 dark:text-slate-300 mb-1 text-sm sm:text-base">Kontakt</h4>
                    <p class="text-gray-600 dark:text-slate-400 text-sm sm:text-base">${contactName ? contactName.value : '-'}</p>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-slate-500">${email ? email.value : '-'}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 dark:text-slate-300 mb-1 text-sm sm:text-base">Empfehlungsseite</h4>
                    <p class="text-gray-600 dark:text-slate-400 text-sm sm:text-base">${subdomain ? subdomain.value : 'ihre-firma'}.empfehlungen.cloud</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 dark:text-slate-300 mb-1 text-sm sm:text-base">Tarif</h4>
                    <p class="text-gray-600 dark:text-slate-400 text-sm sm:text-base">${plan && plan.value === 'professional' ? 'Professional' : 'Starter'}</p>
                </div>
                ${showEmailToolStep ? `
                <div>
                    <h4 class="font-semibold text-gray-700 dark:text-slate-300 mb-1 text-sm sm:text-base">E-Mail-Tool</h4>
                    <p class="text-gray-600 dark:text-slate-400 text-sm sm:text-base">${emailToolInfo}</p>
                </div>
                ` : ''}
            </div>
        `;
    }
    
    // ================================
    // EVENT LISTENERS
    // ================================
    
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            const prevStep = getPrevStep(currentStep);
            if (prevStep !== currentStep) {
                currentStep = prevStep;
                showPanel(currentStep);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            console.log('Next button clicked, current step:', currentStep);
            
            if (validateStep(currentStep)) {
                const nextStep = getNextStep(currentStep);
                console.log('Validation passed, next step:', nextStep);
                
                if (nextStep !== currentStep) {
                    currentStep = nextStep;
                    showPanel(currentStep);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            } else {
                console.log('Validation failed for step:', currentStep);
            }
        });
    }
    
    // Form Submit
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!validateStep(currentStep)) {
                return;
            }
            
            const termsCheckbox = document.getElementById('acceptTerms');
            if (termsCheckbox && !termsCheckbox.checked) {
                alert('Bitte akzeptieren Sie die AGB und Datenschutzerklärung.');
                return;
            }
            
            // Show loading state
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Wird eingerichtet...';
            }
            
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
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-rocket mr-2"></i><span class="hidden sm:inline">Einrichtung </span>starten';
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-rocket mr-2"></i><span class="hidden sm:inline">Einrichtung </span>starten';
                }
            }
        });
    }
    
    // ================================
    // INITIALIZE
    // ================================
    
    // Check initial industry selection for email tool step visibility
    const initialIndustry = document.querySelector('input[name="industry"]:checked');
    if (initialIndustry) {
        updateEmailToolStepVisibility(initialIndustry.value);
        updateBackgroundsForIndustry(initialIndustry.value);
    } else {
        // Default: Email tool step hidden
        showEmailToolStep = false;
        updateBackgroundsForIndustry('allgemein');
    }
    
    showPanel(1);
    
    console.log('Onboarding Wizard ready');
});
