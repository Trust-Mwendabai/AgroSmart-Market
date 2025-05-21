/**
 * AgroSmart Market - Main JavaScript File
 * Contains animations, interactions, and functionality for the marketplace
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize animations
    initAnimations();
    
    // Initialize product filters
    initProductFilters();
    
    // Initialize image zoom effect
    initImageZoom();
    
    // Initialize quantity selectors
    initQuantitySelectors();
    
    // Initialize tooltips and popovers
    initTooltips();
    
    // Initialize message notifications
    initMessageNotifications();
    
    // Initialize scroll to top button
    initScrollToTop();
});

/**
 * Initialize animations for elements with animation classes
 */
function initAnimations() {
    // Initialize AOS (Animate On Scroll) library
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            mirror: false,
            offset: 50
        });
    }
    
    // Hero section parallax effect
    const heroSection = document.querySelector('.hero');
    if (heroSection) {
        const heroBackground = heroSection.querySelector('.hero-bg-image');
        if (heroBackground) {
            window.addEventListener('scroll', function() {
                const scrollPosition = window.scrollY;
                if (scrollPosition < heroSection.offsetHeight) {
                    // Apply parallax effect
                    heroBackground.style.transform = `scale(1.05) translateY(${scrollPosition * 0.2}px)`;
                }
            });
        }
    }
    
    // Add animation to about section stats counter
    const statsNumbers = document.querySelectorAll('.stats-number');
    if (statsNumbers.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statsElement = entry.target;
                    const target = parseInt(statsElement.textContent.replace(/[^0-9]/g, ''));
                    let count = 0;
                    const duration = 2000; // 2 seconds
                    const step = Math.ceil(target / (duration / 30)); // Update every 30ms
                    
                    const counter = setInterval(() => {
                        count += step;
                        if (count > target) {
                            count = target;
                            clearInterval(counter);
                        }
                        statsElement.textContent = count + '+';
                    }, 30);
                    
                    observer.unobserve(statsElement);
                }
            });
        }, { threshold: 0.5 });
        
        statsNumbers.forEach(number => {
            observer.observe(number);
        });
    }
}

/**
 * Initialize product filters
 */
function initProductFilters() {
    const filterForm = document.getElementById('product-filters');
    if (!filterForm) return;
    
    // Price range slider
    const priceRange = document.getElementById('price-range');
    const priceOutput = document.getElementById('price-output');
    
    if (priceRange && priceOutput) {
        priceRange.addEventListener('input', function() {
            priceOutput.textContent = '$' + this.value;
        });
    }
    
    // Category filters
    const categoryFilters = document.querySelectorAll('.category-filter');
    categoryFilters.forEach(filter => {
        filter.addEventListener('change', function() {
            filterForm.submit();
        });
    });
    
    // Location filters
    const locationSelect = document.getElementById('location-filter');
    if (locationSelect) {
        locationSelect.addEventListener('change', function() {
            filterForm.submit();
        });
    }
    
    // Reset filters button
    const resetButton = document.getElementById('reset-filters');
    if (resetButton) {
        resetButton.addEventListener('click', function(e) {
            e.preventDefault();
            // Reset form inputs
            filterForm.reset();
            // Submit the form
            filterForm.submit();
        });
    }
}

/**
 * Initialize image zoom effect for product images
 */
function initImageZoom() {
    const productImages = document.querySelectorAll('.product-detail-img');
    
    productImages.forEach(img => {
        img.addEventListener('mousemove', function(e) {
            const zoomer = this.closest('.img-zoom-container');
            if (!zoomer) return;
            
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const xPercent = (x / rect.width) * 100;
            const yPercent = (y / rect.height) * 100;
            
            this.style.transformOrigin = `${xPercent}% ${yPercent}%`;
        });
        
        // Add zoom effect on hover
        img.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.5)';
        });
        
        // Remove zoom effect when mouse leaves
        img.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
}

/**
 * Initialize quantity selectors for product orders
 */
function initQuantitySelectors() {
    const quantityContainers = document.querySelectorAll('.quantity-selector');
    
    quantityContainers.forEach(container => {
        const decreaseBtn = container.querySelector('.decrease-quantity');
        const increaseBtn = container.querySelector('.increase-quantity');
        const quantityInput = container.querySelector('.quantity-input');
        
        if (!decreaseBtn || !increaseBtn || !quantityInput) return;
        
        decreaseBtn.addEventListener('click', function() {
            let value = parseInt(quantityInput.value);
            if (value > 1) {
                value--;
                quantityInput.value = value;
                
                // Trigger change event for any listeners
                const event = new Event('change', { bubbles: true });
                quantityInput.dispatchEvent(event);
            }
        });
        
        increaseBtn.addEventListener('click', function() {
            let value = parseInt(quantityInput.value);
            let max = parseInt(quantityInput.getAttribute('max') || 100);
            
            if (value < max) {
                value++;
                quantityInput.value = value;
                
                // Trigger change event for any listeners
                const event = new Event('change', { bubbles: true });
                quantityInput.dispatchEvent(event);
            }
        });
        
        // Update order total when quantity changes
        quantityInput.addEventListener('change', function() {
            const priceElement = document.getElementById('product-price');
            const totalElement = document.getElementById('order-total');
            
            if (priceElement && totalElement) {
                const price = parseFloat(priceElement.dataset.price);
                const quantity = parseInt(this.value);
                const total = price * quantity;
                
                totalElement.textContent = '$' + total.toFixed(2);
            }
        });
    });
}

/**
 * Initialize Bootstrap tooltips and popovers
 */
function initTooltips() {
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });
    
    // Initialize popovers
    const popovers = document.querySelectorAll('[data-bs-toggle="popover"]');
    popovers.forEach(popover => {
        new bootstrap.Popover(popover);
    });
}

/**
 * Initialize real-time message notifications
 */
function initMessageNotifications() {
    // Check for notifications every 30 seconds
    setInterval(checkNewMessages, 30000);
    
    function checkNewMessages() {
        // Only check if the user is logged in (indicated by user-id in the DOM)
        const userId = document.body.dataset.userId;
        if (!userId) return;
        
        // Fetch notifications with AJAX
        fetch('controllers/message.php?action=check_notifications')
            .then(response => response.json())
            .then(data => {
                if (data.unread_count > 0) {
                    // Update notification badge in the navbar
                    const badge = document.querySelector('.message-badge');
                    if (badge) {
                        badge.textContent = data.unread_count;
                        badge.classList.remove('d-none');
                    }
                    
                    // Show notification toast if new messages
                    if (data.new_messages && data.new_messages.length > 0) {
                        showNotificationToast(data.new_messages[0]);
                    }
                }
            })
            .catch(error => console.error('Error checking messages:', error));
    }
    
    function showNotificationToast(message) {
        const toastContainer = document.getElementById('toast-container');
        if (!toastContainer) return;
        
        // Create toast element
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        // Populate toast content
        toast.innerHTML = `
            <div class="toast-header">
                <strong class="me-auto">New Message</strong>
                <small>${message.time_ago}</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <strong>${message.sender_name}:</strong> ${message.subject}
            </div>
        `;
        
        // Add click handler to navigate to the message
        toast.addEventListener('click', () => {
            window.location.href = `message.php?action=view&id=${message.id}`;
        });
        
        // Add toast to container
        toastContainer.appendChild(toast);
        
        // Initialize and show Bootstrap toast
        const bsToast = new bootstrap.Toast(toast, {
            delay: 5000
        });
        bsToast.show();
        
        // Remove from DOM after hiding
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }
}

/**
 * Initialize scroll to top button
 */
function initScrollToTop() {
    const scrollTopBtn = document.getElementById('scroll-to-top');
    if (!scrollTopBtn) return;
    
    // Show button when page is scrolled down
    window.addEventListener('scroll', function() {
        if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
            scrollTopBtn.classList.add('active');
        } else {
            scrollTopBtn.classList.remove('active');
        }
    });
    
    // Scroll to top when button is clicked
    scrollTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Add animation to floating elements in CTA section
    const floatingElements = document.querySelectorAll('.floating-element');
    if (floatingElements.length > 0) {
        floatingElements.forEach((element, index) => {
            element.style.animationDelay = (index * 0.5) + 's';
        });
    }
}

/**
 * Handle product search autocomplete
 */
function handleSearchAutocomplete() {
    const searchInput = document.getElementById('product-search');
    if (!searchInput) return;
    
    let timeout = null;
    
    searchInput.addEventListener('input', function() {
        // Clear previous timeout
        clearTimeout(timeout);
        
        const query = this.value.trim();
        const resultsContainer = document.getElementById('search-results');
        
        // Hide results if query is empty
        if (query.length < 2) {
            resultsContainer.innerHTML = '';
            resultsContainer.classList.add('d-none');
            return;
        }
        
        // Set a timeout to avoid excessive requests
        timeout = setTimeout(() => {
            // Make AJAX request to get search results
            fetch(`controllers/product.php?action=search&q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    resultsContainer.innerHTML = '';
                    
                    if (data.length > 0) {
                        // Create results list
                        data.forEach(product => {
                            const resultItem = document.createElement('a');
                            resultItem.className = 'dropdown-item';
                            resultItem.href = `products/view.php?id=${product.id}`;
                            
                            resultItem.innerHTML = `
                                <div class="d-flex align-items-center">
                                    <div class="search-result-img me-2">
                                        <img src="public/uploads/products/${product.image || 'default.jpg'}" alt="${product.name}" width="40" height="40">
                                    </div>
                                    <div>
                                        <div class="fw-bold">${product.name}</div>
                                        <div class="small">$${product.price} • ${product.category}</div>
                                    </div>
                                </div>
                            `;
                            
                            resultsContainer.appendChild(resultItem);
                        });
                        
                        // Show results container
                        resultsContainer.classList.remove('d-none');
                    } else {
                        // Show no results message
                        resultsContainer.innerHTML = '<span class="dropdown-item">No products found</span>';
                        resultsContainer.classList.remove('d-none');
                    }
                })
                .catch(error => console.error('Error searching products:', error));
        }, 300);
    });
    
    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        const resultsContainer = document.getElementById('search-results');
        if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
            resultsContainer.classList.add('d-none');
        }
    });
}

// Main JavaScript file for AgroSmart Market

// DOM Elements
const forms = document.querySelectorAll('form');
const loadingElements = document.querySelectorAll('.loading');
const errorMessages = document.querySelectorAll('.error-message');
const successMessages = document.querySelectorAll('.success-message');

// Utility Functions
const showLoading = (element) => {
    element.classList.add('loading');
};

const hideLoading = (element) => {
    element.classList.remove('loading');
};

const showError = (element, message) => {
    element.textContent = message;
    element.style.display = 'block';
    setTimeout(() => {
        element.style.display = 'none';
    }, 5000);
};

const showSuccess = (element, message) => {
    element.textContent = message;
    element.style.display = 'block';
    setTimeout(() => {
        element.style.display = 'none';
    }, 5000);
};

// Form Validation
const validateForm = (form) => {
    const inputs = form.querySelectorAll('input, textarea, select');
    let isValid = true;

    inputs.forEach(input => {
        // Reset previous error states
        input.classList.remove('error');
        const errorElement = input.nextElementSibling;
        if (errorElement && errorElement.classList.contains('error-message')) {
            errorElement.style.display = 'none';
        }

        // Required field validation
        if (input.hasAttribute('required') && !input.value.trim()) {
            isValid = false;
            input.classList.add('error');
            if (errorElement && errorElement.classList.contains('error-message')) {
                showError(errorElement, 'This field is required');
            }
        }

        // Email validation
        if (input.type === 'email' && input.value.trim()) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value.trim())) {
                isValid = false;
                input.classList.add('error');
                if (errorElement && errorElement.classList.contains('error-message')) {
                    showError(errorElement, 'Please enter a valid email address');
                }
            }
        }

        // Password validation
        if (input.type === 'password' && input.value.trim()) {
            if (input.value.length < 6) {
                isValid = false;
                input.classList.add('error');
                if (errorElement && errorElement.classList.contains('error-message')) {
                    showError(errorElement, 'Password must be at least 6 characters long');
                }
            }
        }

        // Phone number validation
        if (input.type === 'tel' && input.value.trim()) {
            const phoneRegex = /^\+?[\d\s-]{10,}$/;
            if (!phoneRegex.test(input.value.trim())) {
                isValid = false;
                input.classList.add('error');
                if (errorElement && errorElement.classList.contains('error-message')) {
                    showError(errorElement, 'Please enter a valid phone number');
                }
            }
        }
    });

    return isValid;
};

// Form Submission
forms.forEach(form => {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (!validateForm(form)) {
            return;
        }

        const submitButton = form.querySelector('button[type="submit"]');
        const errorElement = form.querySelector('.error-message');
        const successElement = form.querySelector('.success-message');

        try {
            showLoading(submitButton);

            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: form.method,
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                showSuccess(successElement, data.message || 'Operation successful!');
                form.reset();
            } else {
                showError(errorElement, data.message || 'An error occurred. Please try again.');
            }
        } catch (error) {
            showError(errorElement, 'An error occurred. Please try again.');
        } finally {
            hideLoading(submitButton);
        }
    });
});

// Image Preview
const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
imageInputs.forEach(input => {
    input.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            const preview = input.parentElement.querySelector('.image-preview');
            
            reader.onload = (e) => {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            
            reader.readAsDataURL(file);
        }
    });
});

// Responsive Navigation
const menuToggle = document.querySelector('.menu-toggle');
const navMenu = document.querySelector('.nav-menu');

if (menuToggle && navMenu) {
    menuToggle.addEventListener('click', () => {
        navMenu.classList.toggle('active');
    });
}

// Scroll to Top
const scrollToTop = document.querySelector('.scroll-to-top');
if (scrollToTop) {
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 100) {
            scrollToTop.classList.add('active');
        } else {
            scrollToTop.classList.remove('active');
        }
    });

    scrollToTop.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// Lazy Loading Images
const lazyImages = document.querySelectorAll('img[data-src]');
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });

    lazyImages.forEach(img => imageObserver.observe(img));
} else {
    // Fallback for browsers that don't support IntersectionObserver
    lazyImages.forEach(img => {
        img.src = img.dataset.src;
        img.removeAttribute('data-src');
    });
}

// Accessibility
document.addEventListener('keydown', (e) => {
    // Skip to main content
    if (e.key === 's' && e.ctrlKey) {
        e.preventDefault();
        const mainContent = document.querySelector('main');
        if (mainContent) {
            mainContent.focus();
        }
    }
});

// Add ARIA labels to interactive elements
const interactiveElements = document.querySelectorAll('button, a, input, select, textarea');
interactiveElements.forEach(element => {
    if (!element.hasAttribute('aria-label') && !element.textContent.trim()) {
        const label = element.getAttribute('title') || element.getAttribute('placeholder');
        if (label) {
            element.setAttribute('aria-label', label);
        }
    }
});

// Initialize tooltips
const tooltips = document.querySelectorAll('[data-tooltip]');
tooltips.forEach(tooltip => {
    tooltip.addEventListener('mouseenter', (e) => {
        const text = e.target.dataset.tooltip;
        const tooltipElement = document.createElement('div');
        tooltipElement.className = 'tooltip';
        tooltipElement.textContent = text;
        document.body.appendChild(tooltipElement);

        const rect = e.target.getBoundingClientRect();
        tooltipElement.style.top = `${rect.bottom + 5}px`;
        tooltipElement.style.left = `${rect.left + (rect.width / 2) - (tooltipElement.offsetWidth / 2)}px`;
    });

    tooltip.addEventListener('mouseleave', () => {
        const tooltipElement = document.querySelector('.tooltip');
        if (tooltipElement) {
            tooltipElement.remove();
        }
    });
});
