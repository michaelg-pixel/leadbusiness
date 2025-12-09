/* =====================================================
   LEADBUSINESS - Main JavaScript
   ===================================================== */

document.addEventListener('DOMContentLoaded', function() {
    
    // =====================================================
    // Mobile Navigation
    // =====================================================
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuClose = document.getElementById('mobile-menu-close');
    
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
        
        if (mobileMenuClose) {
            mobileMenuClose.addEventListener('click', () => {
                mobileMenu.classList.remove('active');
                document.body.style.overflow = '';
            });
        }
        
        // Close on link click
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('active');
                document.body.style.overflow = '';
            });
        });
    }
    
    // =====================================================
    // Sticky Header
    // =====================================================
    const header = document.getElementById('header');
    let lastScroll = 0;
    
    if (header) {
        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll > 100) {
                header.classList.add('shadow-lg', 'bg-white/95', 'backdrop-blur-sm');
            } else {
                header.classList.remove('shadow-lg', 'bg-white/95', 'backdrop-blur-sm');
            }
            
            lastScroll = currentScroll;
        });
    }
    
    // =====================================================
    // FAQ Accordion
    // =====================================================
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        
        question.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            
            // Close all
            faqItems.forEach(i => i.classList.remove('active'));
            
            // Open clicked (if not already active)
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });
    
    // =====================================================
    // Animated Counter
    // =====================================================
    const counters = document.querySelectorAll('[data-counter]');
    
    const animateCounter = (counter) => {
        const target = parseInt(counter.getAttribute('data-counter'));
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;
        
        const updateCounter = () => {
            current += step;
            if (current < target) {
                counter.textContent = Math.floor(current).toLocaleString('de-DE');
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target.toLocaleString('de-DE');
            }
        };
        
        updateCounter();
    };
    
    // Intersection Observer for counters
    if (counters.length > 0) {
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                    entry.target.classList.add('counted');
                    animateCounter(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        counters.forEach(counter => counterObserver.observe(counter));
    }
    
    // =====================================================
    // Scroll Animations
    // =====================================================
    const animatedElements = document.querySelectorAll('[data-animate]');
    
    if (animatedElements.length > 0) {
        const animateObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const animation = entry.target.getAttribute('data-animate');
                    const delay = entry.target.getAttribute('data-delay') || 0;
                    
                    setTimeout(() => {
                        entry.target.classList.add('animate-' + animation);
                        entry.target.style.opacity = '1';
                    }, delay);
                    
                    animateObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        animatedElements.forEach(el => {
            el.style.opacity = '0';
            animateObserver.observe(el);
        });
    }
    
    // =====================================================
    // Smooth Scroll for Anchor Links
    // =====================================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                const headerOffset = 80;
                const elementPosition = target.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // =====================================================
    // Toast Notifications
    // =====================================================
    window.showToast = function(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => toast.classList.add('show'), 100);
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    };
    
    // =====================================================
    // Form Validation
    // =====================================================
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                const errorEl = field.parentElement.querySelector('.error-message');
                
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                    if (errorEl) errorEl.classList.remove('hidden');
                } else {
                    field.classList.remove('border-red-500');
                    if (errorEl) errorEl.classList.add('hidden');
                }
                
                // Email validation
                if (field.type === 'email' && field.value) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(field.value)) {
                        isValid = false;
                        field.classList.add('border-red-500');
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showToast('Bitte füllen Sie alle Pflichtfelder aus.', 'error');
            }
        });
    });
    
    // =====================================================
    // Pricing Toggle (Monthly/Yearly)
    // =====================================================
    const pricingToggle = document.getElementById('pricing-toggle');
    const monthlyPrices = document.querySelectorAll('[data-price-monthly]');
    const yearlyPrices = document.querySelectorAll('[data-price-yearly]');
    
    if (pricingToggle) {
        pricingToggle.addEventListener('change', function() {
            const isYearly = this.checked;
            
            monthlyPrices.forEach(el => {
                el.classList.toggle('hidden', isYearly);
            });
            
            yearlyPrices.forEach(el => {
                el.classList.toggle('hidden', !isYearly);
            });
        });
    }
    
    // =====================================================
    // Copy to Clipboard
    // =====================================================
    document.querySelectorAll('[data-copy]').forEach(btn => {
        btn.addEventListener('click', function() {
            const text = this.getAttribute('data-copy');
            navigator.clipboard.writeText(text).then(() => {
                showToast('In Zwischenablage kopiert!', 'success');
            });
        });
    });
    
    // =====================================================
    // Testimonial Slider
    // =====================================================
    const testimonialSlider = document.querySelector('.testimonial-slider');
    
    if (testimonialSlider) {
        let currentSlide = 0;
        const slides = testimonialSlider.querySelectorAll('.testimonial-slide');
        const totalSlides = slides.length;
        
        const showSlide = (index) => {
            slides.forEach((slide, i) => {
                slide.style.transform = `translateX(${(i - index) * 100}%)`;
            });
        };
        
        // Auto-advance
        setInterval(() => {
            currentSlide = (currentSlide + 1) % totalSlides;
            showSlide(currentSlide);
        }, 5000);
        
        // Initialize
        showSlide(0);
    }
    
    // =====================================================
    // Live Counter Animation (Fake for Demo)
    // =====================================================
    const liveCounter = document.getElementById('live-counter');
    
    if (liveCounter) {
        const baseCount = parseInt(liveCounter.getAttribute('data-base') || 1247);
        let currentCount = baseCount;
        
        // Random increment every few seconds
        setInterval(() => {
            if (Math.random() > 0.7) {
                currentCount++;
                liveCounter.textContent = currentCount.toLocaleString('de-DE');
                liveCounter.classList.add('scale-110');
                setTimeout(() => liveCounter.classList.remove('scale-110'), 200);
            }
        }, 3000);
    }
    
    // =====================================================
    // Parallax Effect
    // =====================================================
    const parallaxElements = document.querySelectorAll('[data-parallax]');
    
    if (parallaxElements.length > 0) {
        window.addEventListener('scroll', () => {
            const scrollY = window.pageYOffset;
            
            parallaxElements.forEach(el => {
                const speed = parseFloat(el.getAttribute('data-parallax')) || 0.5;
                el.style.transform = `translateY(${scrollY * speed}px)`;
            });
        });
    }
    
    // =====================================================
    // Cookie Consent
    // =====================================================
    const cookieBanner = document.getElementById('cookie-banner');
    const cookieAccept = document.getElementById('cookie-accept');
    
    if (cookieBanner && !localStorage.getItem('cookies-accepted')) {
        cookieBanner.classList.remove('hidden');
        
        cookieAccept?.addEventListener('click', () => {
            localStorage.setItem('cookies-accepted', 'true');
            cookieBanner.classList.add('hidden');
        });
    }
    
    // =====================================================
    // Industry Selector (Onboarding)
    // =====================================================
    const industryCards = document.querySelectorAll('.industry-card');
    const industryInput = document.getElementById('industry-input');
    
    industryCards.forEach(card => {
        card.addEventListener('click', function() {
            industryCards.forEach(c => c.classList.remove('ring-2', 'ring-primary'));
            this.classList.add('ring-2', 'ring-primary');
            
            if (industryInput) {
                industryInput.value = this.getAttribute('data-industry');
            }
        });
    });
    
    // =====================================================
    // Password Strength Indicator
    // =====================================================
    const passwordInput = document.getElementById('password');
    const strengthIndicator = document.getElementById('password-strength');
    
    if (passwordInput && strengthIndicator) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-400', 'bg-green-600'];
            const labels = ['Sehr schwach', 'Schwach', 'Mittel', 'Stark', 'Sehr stark'];
            
            strengthIndicator.className = `h-2 rounded transition-all ${colors[strength - 1] || 'bg-gray-200'}`;
            strengthIndicator.style.width = `${strength * 20}%`;
            
            const labelEl = document.getElementById('password-strength-label');
            if (labelEl) labelEl.textContent = labels[strength - 1] || '';
        });
    }
    
    // =====================================================
    // File Upload Preview
    // =====================================================
    const fileInputs = document.querySelectorAll('input[type="file"][data-preview]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const previewId = this.getAttribute('data-preview');
            const preview = document.getElementById(previewId);
            
            if (preview && this.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
    
    // =====================================================
    // Confetti Animation (for celebrations)
    // =====================================================
    window.triggerConfetti = function() {
        const colors = ['#667eea', '#764ba2', '#f093fb', '#48bb78', '#ed8936'];
        const confettiCount = 100;
        
        for (let i = 0; i < confettiCount; i++) {
            const confetti = document.createElement('div');
            confetti.style.cssText = `
                position: fixed;
                width: 10px;
                height: 10px;
                background: ${colors[Math.floor(Math.random() * colors.length)]};
                left: ${Math.random() * 100}vw;
                top: -10px;
                border-radius: ${Math.random() > 0.5 ? '50%' : '0'};
                z-index: 9999;
                pointer-events: none;
            `;
            document.body.appendChild(confetti);
            
            const animation = confetti.animate([
                { transform: 'translateY(0) rotate(0deg)', opacity: 1 },
                { transform: `translateY(100vh) rotate(${Math.random() * 720}deg)`, opacity: 0 }
            ], {
                duration: 2000 + Math.random() * 2000,
                easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
            });
            
            animation.onfinish = () => confetti.remove();
        }
    };
    
    // =====================================================
    // Initialize Animations
    // =====================================================
    
    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        .animate-fadeIn { animation: fadeIn 0.6s ease forwards; }
        .animate-fadeInUp { animation: fadeInUp 0.6s ease forwards; }
        .animate-fadeInDown { animation: fadeInDown 0.6s ease forwards; }
        .animate-fadeInLeft { animation: fadeInLeft 0.6s ease forwards; }
        .animate-fadeInRight { animation: fadeInRight 0.6s ease forwards; }
        .animate-scaleIn { animation: scaleIn 0.6s ease forwards; }
        
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInLeft { from { opacity: 0; transform: translateX(-30px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes fadeInRight { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes scaleIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
    `;
    document.head.appendChild(style);
    
});

// =====================================================
// Utility Functions
// =====================================================

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('de-DE', {
        style: 'currency',
        currency: 'EUR'
    }).format(amount);
}

// Format date
function formatDate(date) {
    return new Intl.DateTimeFormat('de-DE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).format(new Date(date));
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Throttle function
function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}
