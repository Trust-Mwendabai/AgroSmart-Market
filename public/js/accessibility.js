/**
 * AgroSmart Market Accessibility and Language Enhancements
 * Supports language switching, high contrast mode, and screen reader features
 */

document.addEventListener('DOMContentLoaded', function() {
    // Language Switching
    initLanguageSwitcher();
    
    // High Contrast Mode
    initHighContrastMode();
    
    // Text-to-Speech Functionality
    initTextToSpeech();
    
    // Enhanced Tooltips
    initEnhancedTooltips();
    
    // Service Worker for Offline Support
    initServiceWorker();
});

/**
 * Language Switcher Initialization
 */
function initLanguageSwitcher() {
    const languageOptions = document.querySelectorAll('.language-option');
    
    if (languageOptions.length > 0) {
        languageOptions.forEach(option => {
            option.addEventListener('click', function() {
                const languageCode = this.getAttribute('data-lang');
                
                // Set language cookie/local storage
                localStorage.setItem('preferred_language', languageCode);
                
                // Submit form to change language
                const form = document.getElementById('language-form');
                const langInput = document.getElementById('selected-language');
                
                if (form && langInput) {
                    langInput.value = languageCode;
                    form.submit();
                } else {
                    // Fallback: redirect with query parameter
                    const url = new URL(window.location);
                    url.searchParams.set('lang', languageCode);
                    window.location = url.toString();
                }
            });
        });
    }
}

/**
 * High Contrast Mode Toggle
 */
function initHighContrastMode() {
    const contrastToggle = document.getElementById('contrast-toggle');
    
    if (contrastToggle) {
        // Check if high contrast was previously enabled
        const highContrast = localStorage.getItem('high_contrast') === 'true';
        
        // Set initial state
        if (highContrast) {
            document.body.classList.add('high-contrast');
            contrastToggle.checked = true;
        }
        
        // Toggle event listener
        contrastToggle.addEventListener('change', function() {
            if (this.checked) {
                document.body.classList.add('high-contrast');
                localStorage.setItem('high_contrast', 'true');
                
                // Announce to screen readers
                announceToScreenReader('High contrast mode enabled');
            } else {
                document.body.classList.remove('high-contrast');
                localStorage.setItem('high_contrast', 'false');
                
                // Announce to screen readers
                announceToScreenReader('High contrast mode disabled');
            }
        });
    }
}

/**
 * Text-to-Speech Functionality
 */
function initTextToSpeech() {
    // Check if browser supports speech synthesis
    if ('speechSynthesis' in window) {
        const speechButtons = document.querySelectorAll('.speech-text-btn');
        
        speechButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const textToRead = this.getAttribute('data-text') || 
                                  this.parentElement.textContent.trim();
                
                if (textToRead) {
                    speakText(textToRead);
                }
            });
        });
    } else {
        // Hide speech buttons if not supported
        document.querySelectorAll('.speech-text-btn').forEach(btn => {
            btn.style.display = 'none';
        });
    }
}

/**
 * Speak text using the Web Speech API
 */
function speakText(text) {
    if ('speechSynthesis' in window) {
        // Cancel any ongoing speech
        window.speechSynthesis.cancel();
        
        const utterance = new SpeechSynthesisUtterance(text);
        
        // Get current language from page or default to English
        const lang = document.documentElement.lang || 'en-US';
        utterance.lang = lang;
        
        window.speechSynthesis.speak(utterance);
    }
}

/**
 * Announce text to screen readers using ARIA live regions
 */
function announceToScreenReader(text) {
    const announcer = document.getElementById('screen-reader-announcer');
    
    if (!announcer) {
        const newAnnouncer = document.createElement('div');
        newAnnouncer.id = 'screen-reader-announcer';
        newAnnouncer.setAttribute('aria-live', 'polite');
        newAnnouncer.setAttribute('aria-atomic', 'true');
        newAnnouncer.classList.add('sr-only');
        document.body.appendChild(newAnnouncer);
        
        setTimeout(() => {
            newAnnouncer.textContent = text;
        }, 100);
    } else {
        announcer.textContent = '';
        
        setTimeout(() => {
            announcer.textContent = text;
        }, 100);
    }
}

/**
 * Enhanced Tooltips for Better Accessibility
 */
function initEnhancedTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    
    tooltips.forEach(element => {
        const tooltipText = element.getAttribute('data-tooltip');
        
        if (tooltipText) {
            // Create tooltip element
            const tooltip = document.createElement('span');
            tooltip.classList.add('tooltip-text');
            tooltip.textContent = tooltipText;
            
            // Make the parent relatively positioned
            element.classList.add('tooltip-enhanced');
            
            // Add tooltip to element
            element.appendChild(tooltip);
            
            // Add keyboard accessibility
            element.setAttribute('tabindex', '0');
            
            // Handle keyboard events
            element.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    tooltip.style.visibility = 'visible';
                    tooltip.style.opacity = '1';
                }
            });
            
            element.addEventListener('blur', function() {
                tooltip.style.visibility = 'hidden';
                tooltip.style.opacity = '0';
            });
        }
    });
}

/**
 * Service Worker for Offline Support
 */
function initServiceWorker() {
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js')
            .then(function(registration) {
                console.log('Service Worker registered with scope:', registration.scope);
            })
            .catch(function(error) {
                console.log('Service Worker registration failed:', error);
            });
    }
    
    // Online/Offline status indicator
    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);
    
    // Initial status check
    updateOnlineStatus();
}

/**
 * Update UI based on online/offline status
 */
function updateOnlineStatus() {
    const statusIndicator = document.getElementById('connection-status');
    
    if (statusIndicator) {
        if (navigator.onLine) {
            statusIndicator.textContent = 'Online';
            statusIndicator.classList.remove('offline');
            statusIndicator.classList.add('online');
            
            // Check for pending offline data
            const pendingData = localStorage.getItem('pendingSync');
            
            if (pendingData && pendingData !== '{}') {
                const syncButton = document.getElementById('sync-data-button');
                
                if (syncButton) {
                    syncButton.style.display = 'inline-flex';
                }
            }
        } else {
            statusIndicator.textContent = 'Offline';
            statusIndicator.classList.remove('online');
            statusIndicator.classList.add('offline');
            
            // Enable offline mode automatically
            localStorage.setItem('offlineMode', 'true');
            
            // Announce to screen readers
            announceToScreenReader('You are currently offline. Your changes will be saved locally.');
        }
    }
}

/**
 * Add a product to cart with visual feedback
 */
function addToCart(productId, quantity) {
    // Default quantity to 1 if not specified
    quantity = quantity || 1;
    
    const cartButton = document.querySelector(`[data-product-id="${productId}"] .add-to-cart`);
    
    if (cartButton) {
        // Add loading state
        cartButton.classList.add('loading');
        cartButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        
        // If offline, store in pending actions
        if (!navigator.onLine) {
            let pendingActions = JSON.parse(localStorage.getItem('pendingSync') || '{}');
            
            if (!pendingActions.cart) {
                pendingActions.cart = [];
            }
            
            pendingActions.cart.push({
                product_id: productId,
                quantity: quantity,
                timestamp: new Date().getTime()
            });
            
            localStorage.setItem('pendingSync', JSON.stringify(pendingActions));
            
            // Show success feedback
            setTimeout(() => {
                cartButton.classList.remove('loading');
                cartButton.classList.add('success');
                cartButton.innerHTML = '<i class="fas fa-check"></i> Added';
                
                // Reset after a while
                setTimeout(() => {
                    cartButton.classList.remove('success');
                    cartButton.innerHTML = '<i class="fas fa-cart-plus"></i> Add to cart';
                }, 2000);
            }, 500);
            
            return;
        }
        
        // Online addition to cart via fetch API
        fetch('cart.php?action=add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `product_id=${productId}&quantity=${quantity}&csrf_token=${getCsrfToken()}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart count in header
                updateCartCount(data.cart_count);
                
                // Show success feedback
                cartButton.classList.remove('loading');
                cartButton.classList.add('success');
                cartButton.innerHTML = '<i class="fas fa-check"></i> Added';
                
                // Announce to screen readers
                announceToScreenReader('Product added to cart');
                
                // Reset after a while
                setTimeout(() => {
                    cartButton.classList.remove('success');
                    cartButton.innerHTML = '<i class="fas fa-cart-plus"></i> Add to cart';
                }, 2000);
            } else {
                // Show error feedback
                cartButton.classList.remove('loading');
                cartButton.classList.add('error');
                cartButton.innerHTML = '<i class="fas fa-times"></i> Error';
                
                // Announce to screen readers
                announceToScreenReader('Error adding product to cart: ' + data.message);
                
                // Reset after a while
                setTimeout(() => {
                    cartButton.classList.remove('error');
                    cartButton.innerHTML = '<i class="fas fa-cart-plus"></i> Add to cart';
                }, 2000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Show error feedback
            cartButton.classList.remove('loading');
            cartButton.classList.add('error');
            cartButton.innerHTML = '<i class="fas fa-times"></i> Error';
            
            // Reset after a while
            setTimeout(() => {
                cartButton.classList.remove('error');
                cartButton.innerHTML = '<i class="fas fa-cart-plus"></i> Add to cart';
            }, 2000);
        });
    }
}

/**
 * Update cart count in header
 */
function updateCartCount(count) {
    const cartCountElement = document.getElementById('cart-count');
    
    if (cartCountElement) {
        cartCountElement.textContent = count;
        
        if (count > 0) {
            cartCountElement.style.display = 'inline-flex';
        } else {
            cartCountElement.style.display = 'none';
        }
    }
}

/**
 * Get CSRF token from meta tag
 */
function getCsrfToken() {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    return metaTag ? metaTag.getAttribute('content') : '';
}

/**
 * Form auto-save for offline support
 */
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[data-autosave="true"]');
    
    forms.forEach(form => {
        const formId = form.id;
        
        if (formId) {
            // Load any saved form data
            loadFormData(form);
            
            // Set up auto-save
            const formInputs = form.querySelectorAll('input, select, textarea');
            
            formInputs.forEach(input => {
                input.addEventListener('change', function() {
                    saveFormData(form);
                });
                
                // For text inputs, save after stopped typing
                if (input.type === 'text' || input.tagName.toLowerCase() === 'textarea') {
                    let timeout = null;
                    
                    input.addEventListener('keyup', function() {
                        clearTimeout(timeout);
                        
                        timeout = setTimeout(function() {
                            saveFormData(form);
                        }, 1000);
                    });
                }
            });
            
            // Clear saved data on successful submit
            form.addEventListener('submit', function() {
                localStorage.removeItem(`form_${formId}`);
            });
        }
    });
});

/**
 * Save form data to localStorage
 */
function saveFormData(form) {
    const formId = form.id;
    const formData = {};
    
    // Collect form data
    const formElements = form.elements;
    
    for (let i = 0; i < formElements.length; i++) {
        const element = formElements[i];
        
        if (element.name && element.name !== 'csrf_token') {
            if (element.type === 'checkbox' || element.type === 'radio') {
                if (element.checked) {
                    formData[element.name] = element.value;
                }
            } else if (element.type !== 'submit' && element.type !== 'button') {
                formData[element.name] = element.value;
            }
        }
    }
    
    // Save to localStorage
    localStorage.setItem(`form_${formId}`, JSON.stringify(formData));
    
    // Show saving indicator if it exists
    const savingIndicator = document.getElementById(`${formId}_saving`);
    
    if (savingIndicator) {
        savingIndicator.textContent = 'Saved locally';
        savingIndicator.style.display = 'block';
        
        setTimeout(() => {
            savingIndicator.style.display = 'none';
        }, 2000);
    }
}

/**
 * Load saved form data from localStorage
 */
function loadFormData(form) {
    const formId = form.id;
    const savedData = localStorage.getItem(`form_${formId}`);
    
    if (savedData) {
        const formData = JSON.parse(savedData);
        
        // Fill form with saved data
        Object.keys(formData).forEach(name => {
            const value = formData[name];
            const elements = form.elements[name];
            
            if (elements) {
                if (elements.length) {
                    // Multiple elements with same name (radio, checkbox)
                    for (let i = 0; i < elements.length; i++) {
                        if (elements[i].value === value) {
                            elements[i].checked = true;
                        }
                    }
                } else {
                    // Single element
                    if (elements.type === 'checkbox') {
                        elements.checked = value === elements.value;
                    } else {
                        elements.value = value;
                    }
                }
            }
        });
        
        // Show restore notification if it exists
        const restoreNotice = document.getElementById(`${formId}_restore`);
        
        if (restoreNotice) {
            restoreNotice.style.display = 'block';
            
            // Add clear button functionality
            const clearButton = document.getElementById(`${formId}_clear`);
            
            if (clearButton) {
                clearButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    localStorage.removeItem(`form_${formId}`);
                    form.reset();
                    restoreNotice.style.display = 'none';
                });
            }
        }
    }
}

// Accessibility enhancements for AgroSmart Market

// High Contrast Mode
const toggleHighContrast = () => {
    document.body.classList.toggle('high-contrast');
    const isHighContrast = document.body.classList.contains('high-contrast');
    localStorage.setItem('highContrast', isHighContrast);
};

// Initialize high contrast mode from localStorage
if (localStorage.getItem('highContrast') === 'true') {
    document.body.classList.add('high-contrast');
}

// Font Size Controls
const increaseFontSize = () => {
    const currentSize = parseInt(getComputedStyle(document.body).fontSize);
    document.body.style.fontSize = `${currentSize + 2}px`;
    localStorage.setItem('fontSize', currentSize + 2);
};

const decreaseFontSize = () => {
    const currentSize = parseInt(getComputedStyle(document.body).fontSize);
    document.body.style.fontSize = `${currentSize - 2}px`;
    localStorage.setItem('fontSize', currentSize - 2);
};

const resetFontSize = () => {
    document.body.style.fontSize = '16px';
    localStorage.setItem('fontSize', '16');
};

// Initialize font size from localStorage
const savedFontSize = localStorage.getItem('fontSize');
if (savedFontSize) {
    document.body.style.fontSize = `${savedFontSize}px`;
}

// Screen Reader Announcements
const announceToScreenReader = (message) => {
    const announcement = document.createElement('div');
    announcement.setAttribute('aria-live', 'polite');
    announcement.setAttribute('aria-atomic', 'true');
    announcement.setAttribute('class', 'sr-only');
    announcement.textContent = message;
    document.body.appendChild(announcement);
    setTimeout(() => announcement.remove(), 1000);
};

// Focus Management
const trapFocus = (element) => {
    const focusableElements = element.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    const firstFocusable = focusableElements[0];
    const lastFocusable = focusableElements[focusableElements.length - 1];

    element.addEventListener('keydown', (e) => {
        if (e.key === 'Tab') {
            if (e.shiftKey) {
                if (document.activeElement === firstFocusable) {
                    e.preventDefault();
                    lastFocusable.focus();
                }
            } else {
                if (document.activeElement === lastFocusable) {
                    e.preventDefault();
                    firstFocusable.focus();
                }
            }
        }
    });
};

// Keyboard Navigation
document.addEventListener('keydown', (e) => {
    // Skip to main content
    if (e.key === 's' && e.ctrlKey) {
        e.preventDefault();
        const mainContent = document.querySelector('main');
        if (mainContent) {
            mainContent.focus();
            announceToScreenReader('Skipped to main content');
        }
    }

    // Toggle high contrast mode
    if (e.key === 'c' && e.ctrlKey && e.altKey) {
        e.preventDefault();
        toggleHighContrast();
        announceToScreenReader(
            document.body.classList.contains('high-contrast')
                ? 'High contrast mode enabled'
                : 'High contrast mode disabled'
        );
    }
});

// Form Accessibility
const enhanceFormAccessibility = () => {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        // Add error announcements
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('invalid', () => {
                const errorMessage = input.validationMessage;
                announceToScreenReader(errorMessage);
            });
        });

        // Add success announcements
        form.addEventListener('submit', (e) => {
            if (form.checkValidity()) {
                announceToScreenReader('Form submitted successfully');
            }
        });
    });
};

// Image Accessibility
const enhanceImageAccessibility = () => {
    const images = document.querySelectorAll('img:not([alt])');
    images.forEach(img => {
        if (!img.hasAttribute('alt')) {
            img.setAttribute('alt', '');
            img.setAttribute('role', 'presentation');
        }
    });
};

// Table Accessibility
const enhanceTableAccessibility = () => {
    const tables = document.querySelectorAll('table');
    tables.forEach(table => {
        if (!table.hasAttribute('role')) {
            table.setAttribute('role', 'table');
        }

        const headers = table.querySelectorAll('th');
        headers.forEach(header => {
            if (!header.hasAttribute('scope')) {
                header.setAttribute('scope', 'col');
            }
        });
    });
};

// Initialize accessibility enhancements
document.addEventListener('DOMContentLoaded', () => {
    enhanceFormAccessibility();
    enhanceImageAccessibility();
    enhanceTableAccessibility();

    // Add accessibility controls to the page
    const accessibilityControls = document.createElement('div');
    accessibilityControls.className = 'accessibility-controls';
    accessibilityControls.innerHTML = `
        <button onclick="toggleHighContrast()" aria-label="Toggle high contrast mode">
            <span class="icon">👁️</span> High Contrast
        </button>
        <button onclick="increaseFontSize()" aria-label="Increase font size">
            <span class="icon">A+</span>
        </button>
        <button onclick="decreaseFontSize()" aria-label="Decrease font size">
            <span class="icon">A-</span>
        </button>
        <button onclick="resetFontSize()" aria-label="Reset font size">
            <span class="icon">A</span>
        </button>
    `;
    document.body.insertBefore(accessibilityControls, document.body.firstChild);
});

// Export functions for use in other files
window.accessibility = {
    toggleHighContrast,
    increaseFontSize,
    decreaseFontSize,
    resetFontSize,
    announceToScreenReader,
    trapFocus
};
