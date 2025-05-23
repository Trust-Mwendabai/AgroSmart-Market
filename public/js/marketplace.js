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
