/**
 * AgroSmart Market - Marketplace page functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    // Handle image loading to prevent flickering
    const productImages = document.querySelectorAll('.product-image');
    
    productImages.forEach(img => {
        // Set up proper image loading with fade-in effect
        if (img.complete) {
            img.classList.add('loaded');
        } else {
            img.addEventListener('load', function() {
                img.classList.add('loaded');
            });
            
            img.addEventListener('error', function() {
                // If image fails to load, show placeholder
                const container = img.parentElement;
                const placeholder = document.createElement('div');
                placeholder.className = 'image-placeholder';
                placeholder.innerHTML = '<i class="fas fa-seedling"></i>';
                container.appendChild(placeholder);
                img.style.display = 'none';
            });
        }
    });
    
    // Enhanced cart functionality
    function setupCartButtons() {
        // Handle overlay cart buttons
        const overlayCartButtons = document.querySelectorAll('.product-overlay form button');
        overlayCartButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                submitCartForm(form);
            });
        });
        
        // Handle regular cart buttons
        const regularCartForms = document.querySelectorAll('.add-to-cart-form');
        regularCartForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                submitCartForm(this);
            });
        });
    }
    
    // Function to submit cart forms with feedback
    function submitCartForm(form) {
        const productId = form.querySelector('input[name="product_id"]').value;
        const quantity = form.querySelector('input[name="quantity"]').value;
        const csrfToken = form.querySelector('input[name="csrf_token"]').value;
        
        // Create form data
        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('product_id', productId);
        formData.append('quantity', quantity);
        formData.append('csrf_token', csrfToken);
        
        // Disable the button and show loading state
        const button = form.querySelector('button');
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        
        // Send AJAX request
        fetch('cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then((data) => {
            // Update cart counter in header
            if (data.cart_count) {
                // Find and update the cart counter badge
                const cartBadge = document.querySelector('.fa-shopping-cart').nextElementSibling;
                if (cartBadge && cartBadge.classList.contains('badge')) {
                    cartBadge.textContent = data.cart_count;
                    cartBadge.style.display = 'inline';
                } else {
                    // Create new badge if it doesn't exist
                    const cartIcon = document.querySelector('.fa-shopping-cart');
                    if (cartIcon) {
                        const newBadge = document.createElement('span');
                        newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark';
                        newBadge.textContent = data.cart_count;
                        cartIcon.parentElement.classList.add('position-relative');
                        cartIcon.parentElement.appendChild(newBadge);
                    }
                }
            }
            
            // Show success message
            const alertHtml = `
                <div class="alert alert-success alert-dismissible fade show position-fixed" 
                     style="top: 20px; right: 20px; z-index: 9999; max-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);" 
                     role="alert">
                    <i class="fas fa-check-circle me-2"></i> Added to cart!
                    <a href="cart.php" class="alert-link ms-2">View Cart</a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            // Add alert to the page
            const alertContainer = document.createElement('div');
            alertContainer.innerHTML = alertHtml;
            document.body.appendChild(alertContainer.firstElementChild);
            
            // Remove alert after 3 seconds
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    if (alert && alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                });
            }, 3000);
            
            // Restore button
            button.disabled = false;
            button.innerHTML = originalText;
        })
        .catch(error => {
            console.error('Error adding to cart:', error);
            button.disabled = false;
            button.innerHTML = originalText;
        });
    }
    
    // Initialize cart buttons
    setupCartButtons();
    
    // Grid and List view toggle
    const gridViewBtn = document.getElementById('gridViewBtn');
    const listViewBtn = document.getElementById('listViewBtn');
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');

    if (gridViewBtn && listViewBtn && gridView && listView) {
        gridViewBtn.addEventListener('click', function() {
            gridView.classList.remove('d-none');
            listView.classList.add('d-none');
            gridViewBtn.classList.add('active');
            listViewBtn.classList.remove('active');
            // Save preference to localStorage
            localStorage.setItem('agrosmartViewPreference', 'grid');
        });

        listViewBtn.addEventListener('click', function() {
            gridView.classList.add('d-none');
            listView.classList.remove('d-none');
            listViewBtn.classList.add('active');
            gridViewBtn.classList.remove('active');
            // Save preference to localStorage
            localStorage.setItem('agrosmartViewPreference', 'list');
        });

        // Check for saved preference
        const savedView = localStorage.getItem('agrosmartViewPreference');
        if (savedView === 'list') {
            listViewBtn.click();
        }
    }

    // Sorting functionality
    const sortSelect = document.getElementById('sortOrder');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            // Get current URL
            const url = new URL(window.location.href);
            // Set sort parameter
            url.searchParams.set('sort', this.value);
            // Redirect to new URL
            window.location.href = url.toString();
        });
    }

    // Price range slider initialization
    const priceRange = document.getElementById('priceRange');
    const minPriceInput = document.querySelector('input[name="min_price"]');
    const maxPriceInput = document.querySelector('input[name="max_price"]');
    
    if (priceRange && minPriceInput && maxPriceInput) {
        noUiSlider.create(priceRange, {
            start: [
                minPriceInput.value ? parseInt(minPriceInput.value) : 0,
                maxPriceInput.value ? parseInt(maxPriceInput.value) : 1000
            ],
            connect: true,
            step: 10,
            range: {
                'min': 0,
                'max': 1000
            },
            format: {
                to: function (value) {
                    return Math.round(value);
                },
                from: function (value) {
                    return Number(value);
                }
            }
        });

        // Update inputs when slider changes
        priceRange.noUiSlider.on('update', function(values, handle) {
            const value = values[handle];
            if (handle === 0) {
                minPriceInput.value = value;
            } else {
                maxPriceInput.value = value;
            }
        });

        // Update slider when inputs change
        minPriceInput.addEventListener('change', function() {
            priceRange.noUiSlider.set([this.value, null]);
        });

        maxPriceInput.addEventListener('change', function() {
            priceRange.noUiSlider.set([null, this.value]);
        });
    }

    // Quick view functionality
    const quickViewButtons = document.querySelectorAll('.quick-view-btn');
    if (quickViewButtons.length > 0) {
        quickViewButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.getAttribute('data-product-id');
                // Fetch product details using AJAX
                fetch('ajax/get_product.php?id=' + productId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Populate modal with product data
                            const modal = document.getElementById('quickViewModal');
                            modal.querySelector('.modal-title').textContent = data.product.name;
                            modal.querySelector('.product-price').textContent = 'K' + data.product.price;
                            modal.querySelector('.product-description').textContent = data.product.description;
                            modal.querySelector('.product-category').textContent = data.product.category;
                            modal.querySelector('.product-farmer').textContent = data.product.farmer_name;
                            
                            // Update image
                            const imgElement = modal.querySelector('.product-image');
                            if (data.product.image) {
                                imgElement.src = 'public/uploads/products/' + data.product.image;
                            } else {
                                imgElement.src = 'public/images/default-product.jpg';
                            }
                            
                            // Update add to cart form
                            const cartForm = modal.querySelector('form');
                            if (cartForm) {
                                cartForm.querySelector('input[name="product_id"]').value = data.product.id;
                            }
                            
                            // Show modal
                            const bsModal = new bootstrap.Modal(modal);
                            bsModal.show();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching product details:', error);
                    });
            });
        });
    }

    // Lazy loading for images
    const lazyImages = document.querySelectorAll('.lazy-load');
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy-load');
                    imageObserver.unobserve(img);
                }
            });
        });

        lazyImages.forEach(img => {
            imageObserver.observe(img);
        });
    } else {
        // Fallback for browsers that don't support IntersectionObserver
        lazyImages.forEach(img => {
            img.src = img.dataset.src;
            img.classList.remove('lazy-load');
        });
    }

    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    if (tooltips.length > 0) {
        tooltips.forEach(tooltip => {
            new bootstrap.Tooltip(tooltip);
        });
    }

    // Scroll to top button
    const scrollToTopBtn = document.getElementById('scroll-to-top');
    if (scrollToTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollToTopBtn.classList.add('show');
            } else {
                scrollToTopBtn.classList.remove('show');
            }
        });

        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});
