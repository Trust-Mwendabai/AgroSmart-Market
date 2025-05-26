/**
 * Progressive Image Loading
 * Improves image loading performance on mobile devices by implementing:
 * - Lazy loading
 * - Progressive loading with low-quality placeholders
 * - Image size optimization
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize progressive image loading
    initProgressiveImageLoading();
    
    // Setup intersection observer for lazy loading
    setupLazyLoading();
    
    // Add swipe detection for touch devices
    initTouchInteractions();
});

/**
 * Initialize progressive image loading
 */
function initProgressiveImageLoading() {
    // Get all product images
    const productImages = document.querySelectorAll('.product-image, .category-img');
    
    productImages.forEach(img => {
        // Create placeholder element if it doesn't exist
        if (!img.parentElement.querySelector('.image-placeholder')) {
            const placeholder = document.createElement('div');
            placeholder.className = 'image-placeholder';
            placeholder.innerHTML = '<i class="fas fa-image"></i>';
            img.parentElement.appendChild(placeholder);
        }
        
        // Store original image source
        const originalSrc = img.getAttribute('src');
        
        // Don't proceed if using default image or if src is empty
        if (!originalSrc || originalSrc.includes('default-product.jpg')) {
            return;
        }
        
        // Create optimal image source based on screen size
        const optimizedSrc = getOptimizedImageSrc(originalSrc);
        
        // Set data-src for lazy loading
        img.setAttribute('data-src', optimizedSrc);
        
        // Remove src to prevent immediate loading
        img.removeAttribute('src');
        
        // Add loading class
        img.classList.add('progressive-image');
    });
}

/**
 * Get optimized image source based on device screen width
 * @param {string} src - Original image source
 * @returns {string} - Optimized image source
 */
function getOptimizedImageSrc(src) {
    // Get screen width
    const screenWidth = window.innerWidth;
    
    // Check if src contains /uploads/ which means it's a user uploaded image
    if (src.includes('/uploads/')) {
        // For user uploaded images, we'll use the original for now
        // In a real implementation, you would have different sized versions
        return src;
    }
    
    // For category images or other static images, we can optimize
    // This assumes you have different sized versions available
    if (screenWidth <= 576) {
        // Mobile size (small)
        return src.replace(/\.jpg$|\.png$|\.jpeg$/i, '-sm$&');
    } else if (screenWidth <= 992) {
        // Tablet size (medium)
        return src.replace(/\.jpg$|\.png$|\.jpeg$/i, '-md$&');
    } else {
        // Desktop (original or large)
        return src;
    }
}

/**
 * Setup lazy loading with Intersection Observer
 */
function setupLazyLoading() {
    // Check if IntersectionObserver is available
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const src = img.getAttribute('data-src');
                    
                    if (src) {
                        // Create a new image to preload
                        const newImg = new Image();
                        
                        // When the image is loaded, update the visible image
                        newImg.onload = function() {
                            // Set the src attribute to show the image
                            img.setAttribute('src', src);
                            
                            // Add loaded class to fade in the image
                            setTimeout(() => {
                                img.classList.add('loaded');
                                
                                // Remove placeholder after image loads
                                const placeholder = img.parentElement.querySelector('.image-placeholder');
                                if (placeholder) {
                                    placeholder.style.opacity = '0';
                                    setTimeout(() => {
                                        placeholder.remove();
                                    }, 300);
                                }
                            }, 50);
                            
                            // Stop observing the image
                            observer.unobserve(img);
                        };
                        
                        // Handle image loading errors
                        newImg.onerror = function() {
                            // If loading fails, use default image
                            img.setAttribute('src', 'public/images/default-product.jpg');
                            img.classList.add('loaded');
                            observer.unobserve(img);
                        };
                        
                        // Start loading the image
                        newImg.src = src;
                    }
                }
            });
        }, {
            rootMargin: '50px 0px',
            threshold: 0.01
        });
        
        // Observe all images with the progressive-image class
        document.querySelectorAll('.progressive-image').forEach(img => {
            imageObserver.observe(img);
        });
    } else {
        // Fallback for browsers that don't support IntersectionObserver
        loadImagesImmediately();
    }
}

/**
 * Fallback function to load images immediately if IntersectionObserver is not supported
 */
function loadImagesImmediately() {
    const progressiveImages = document.querySelectorAll('.progressive-image');
    
    progressiveImages.forEach(img => {
        const src = img.getAttribute('data-src');
        if (src) {
            img.setAttribute('src', src);
            img.classList.add('loaded');
        }
    });
}

/**
 * Initialize touch-friendly interactions
 */
function initTouchInteractions() {
    // Add touch feedback to product cards
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        // Add touch start feedback
        card.addEventListener('touchstart', function() {
            this.classList.add('touch-active');
        }, { passive: true });
        
        // Remove feedback on touch end
        card.addEventListener('touchend', function() {
            this.classList.remove('touch-active');
        }, { passive: true });
        
        // Remove feedback if touch moves away
        card.addEventListener('touchmove', function() {
            this.classList.remove('touch-active');
        }, { passive: true });
    });
    
    // Setup swipe detection for product cards in list view
    if (document.getElementById('listView')) {
        setupSwipeActions();
    }
    
    // Optimize quick view modal for touch
    optimizeModalForTouch();
}

/**
 * Setup swipe actions for list view items
 */
function setupSwipeActions() {
    const listItems = document.querySelectorAll('#listView .list-group-item');
    
    listItems.forEach(item => {
        let startX, moveX, startTime;
        
        // Add swipe container if not already present
        if (!item.classList.contains('swipe-container')) {
            item.classList.add('swipe-container');
            
            // Create swipe actions if they don't exist
            if (!item.querySelector('.swipe-actions')) {
                const actionsContainer = document.createElement('div');
                actionsContainer.className = 'swipe-actions';
                
                // Add quick view button
                const quickViewBtn = document.createElement('button');
                quickViewBtn.className = 'btn btn-info swipe-btn';
                quickViewBtn.innerHTML = '<i class="fas fa-eye"></i>';
                quickViewBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    // Trigger quick view for this product
                    const productId = item.getAttribute('data-product-id');
                    if (productId) {
                        document.querySelector(`[data-quick-view="${productId}"]`).click();
                    }
                });
                
                // Add add-to-cart button
                const cartBtn = document.createElement('button');
                cartBtn.className = 'btn btn-success swipe-btn';
                cartBtn.innerHTML = '<i class="fas fa-cart-plus"></i>';
                cartBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    // Trigger add to cart for this product
                    const addToCartBtn = item.querySelector('.add-to-cart-btn');
                    if (addToCartBtn) {
                        addToCartBtn.click();
                    }
                });
                
                // Add buttons to container
                actionsContainer.appendChild(quickViewBtn);
                actionsContainer.appendChild(cartBtn);
                
                // Add to item
                item.appendChild(actionsContainer);
            }
        }
        
        // Touch start event
        item.addEventListener('touchstart', function(e) {
            startX = e.touches[0].clientX;
            startTime = new Date().getTime();
            this.querySelector('.swipe-actions')?.classList.remove('visible');
        }, { passive: true });
        
        // Touch move event
        item.addEventListener('touchmove', function(e) {
            if (!startX) return;
            
            moveX = e.touches[0].clientX;
            const diff = startX - moveX;
            
            // If swiping left, show actions
            if (diff > 50) {
                e.preventDefault();
                this.querySelector('.swipe-actions')?.classList.add('visible');
            } else if (diff < -50) {
                this.querySelector('.swipe-actions')?.classList.remove('visible');
            }
        }, { passive: false });
        
        // Touch end event
        item.addEventListener('touchend', function() {
            const endTime = new Date().getTime();
            const timeDiff = endTime - startTime;
            
            // Reset start position
            startX = null;
            
            // If it was a quick tap, hide the actions
            if (timeDiff < 300 && !moveX) {
                this.querySelector('.swipe-actions')?.classList.remove('visible');
            }
            
            moveX = null;
        }, { passive: true });
    });
}

/**
 * Optimize quick view modal for touch interactions
 */
function optimizeModalForTouch() {
    const modal = document.getElementById('quickViewModal');
    if (!modal) return;
    
    // Add touch-friendly close button
    const closeBtn = modal.querySelector('.btn-close');
    if (closeBtn) {
        closeBtn.classList.add('touch-friendly-close');
        closeBtn.style.padding = '15px';
        closeBtn.style.position = 'absolute';
        closeBtn.style.right = '15px';
        closeBtn.style.top = '15px';
        closeBtn.style.zIndex = '5';
    }
    
    // Make modal content scrollable with momentum scrolling
    const modalBody = modal.querySelector('.modal-body');
    if (modalBody) {
        modalBody.style.overflow = 'auto';
        modalBody.style.webkitOverflowScrolling = 'touch';
        modalBody.style.maxHeight = 'calc(100vh - 200px)';
    }
}

/**
 * Refresh images when grid/list view is toggled
 */
function refreshImages() {
    // Re-initialize progressive image loading
    initProgressiveImageLoading();
    
    // Re-setup lazy loading
    setupLazyLoading();
}

// Global function to be called when views are toggled
window.refreshImageLoading = refreshImages;
