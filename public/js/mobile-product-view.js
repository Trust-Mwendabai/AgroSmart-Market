/**
 * Mobile Product View Enhancements
 * Improves the product viewing experience on mobile devices
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize mobile product view optimizations
    initMobileProductView();
    
    // Setup touch-friendly image gallery
    setupTouchGallery();
    
    // Add mobile product actions bar
    addMobileProductActions();
    
    // Enable pinch-to-zoom for product images
    enablePinchZoom();
});

/**
 * Initialize mobile product view optimizations
 */
function initMobileProductView() {
    // Add mobile-specific body class
    document.body.classList.add('product-view');
    
    // Add image placeholders if not already present
    document.querySelectorAll('.product-image-main, .product-thumb').forEach(imgContainer => {
        const img = imgContainer.querySelector('img');
        if (img && !imgContainer.querySelector('.image-placeholder')) {
            const placeholder = document.createElement('div');
            placeholder.className = 'image-placeholder';
            placeholder.innerHTML = '<i class="fas fa-image"></i>';
            imgContainer.insertBefore(placeholder, img);
            
            // Set up lazy loading
            if (img.getAttribute('src')) {
                const originalSrc = img.getAttribute('src');
                img.setAttribute('data-src', originalSrc);
                img.removeAttribute('src');
                img.classList.add('progressive-image');
            }
        }
    });
    
    // Show swipe indicator on first visit
    if (window.innerWidth < 768 && !sessionStorage.getItem('swipeIndicatorShown')) {
        const gallery = document.querySelector('.product-gallery');
        if (gallery) {
            const swipeIndicator = document.createElement('div');
            swipeIndicator.className = 'swipe-indicator';
            swipeIndicator.innerHTML = '<i class="fas fa-hand-point-up me-1"></i> Swipe to view more';
            gallery.appendChild(swipeIndicator);
            
            setTimeout(() => {
                swipeIndicator.classList.add('visible');
                sessionStorage.setItem('swipeIndicatorShown', 'true');
                
                // Remove after animation
                setTimeout(() => {
                    swipeIndicator.remove();
                }, 2500);
            }, 1000);
        }
    }
    
    // Add zoom hint for mobile
    if (window.innerWidth < 768) {
        const mainImage = document.querySelector('.product-image-main');
        if (mainImage) {
            const zoomHint = document.createElement('div');
            zoomHint.className = 'zoom-hint';
            zoomHint.innerHTML = '<i class="fas fa-search-plus me-1"></i> Tap to zoom';
            mainImage.appendChild(zoomHint);
            
            // Hide hint after 3 seconds
            setTimeout(() => {
                zoomHint.style.opacity = '0';
                setTimeout(() => {
                    zoomHint.remove();
                }, 300);
            }, 3000);
        }
    }
}

/**
 * Setup touch-friendly image gallery
 */
function setupTouchGallery() {
    const mainImage = document.querySelector('.product-image-main img');
    const thumbs = document.querySelectorAll('.product-thumb img');
    
    if (!mainImage || thumbs.length === 0) return;
    
    // Add gallery navigation for mobile
    if (window.innerWidth < 768) {
        const gallery = document.querySelector('.product-gallery');
        if (gallery) {
            const navContainer = document.createElement('div');
            navContainer.className = 'gallery-navigation';
            
            // Previous button
            const prevBtn = document.createElement('button');
            prevBtn.className = 'gallery-nav-button prev-image';
            prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
            prevBtn.setAttribute('aria-label', 'Previous image');
            
            // Next button
            const nextBtn = document.createElement('button');
            nextBtn.className = 'gallery-nav-button next-image';
            nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
            nextBtn.setAttribute('aria-label', 'Next image');
            
            // Add buttons to container
            navContainer.appendChild(prevBtn);
            navContainer.appendChild(nextBtn);
            
            // Add container to gallery
            gallery.appendChild(navContainer);
            
            // Set up navigation functionality
            let currentIndex = 0;
            
            // Function to update main image
            function updateMainImage(index) {
                // Ensure index is within bounds
                if (index < 0) index = thumbs.length - 1;
                if (index >= thumbs.length) index = 0;
                
                currentIndex = index;
                
                // Get source from thumbnail
                const newSrc = thumbs[index].getAttribute('data-src') || thumbs[index].getAttribute('src');
                
                // Update main image
                mainImage.setAttribute('src', newSrc);
                
                // Update active thumbnail
                thumbs.forEach((thumb, i) => {
                    if (i === index) {
                        thumb.parentElement.classList.add('active');
                    } else {
                        thumb.parentElement.classList.remove('active');
                    }
                });
            }
            
            // Add click events to navigation buttons
            prevBtn.addEventListener('click', function() {
                updateMainImage(currentIndex - 1);
            });
            
            nextBtn.addEventListener('click', function() {
                updateMainImage(currentIndex + 1);
            });
            
            // Add swipe gesture for gallery navigation
            let startX, moveX;
            
            gallery.addEventListener('touchstart', function(e) {
                startX = e.touches[0].clientX;
            }, { passive: true });
            
            gallery.addEventListener('touchmove', function(e) {
                moveX = e.touches[0].clientX;
            }, { passive: true });
            
            gallery.addEventListener('touchend', function() {
                if (startX && moveX) {
                    const diff = startX - moveX;
                    
                    // If significant swipe
                    if (Math.abs(diff) > 50) {
                        if (diff > 0) {
                            // Swiped left, go to next
                            updateMainImage(currentIndex + 1);
                        } else {
                            // Swiped right, go to previous
                            updateMainImage(currentIndex - 1);
                        }
                    }
                }
                
                // Reset values
                startX = null;
                moveX = null;
            }, { passive: true });
        }
    }
    
    // Make thumbnails interactive
    thumbs.forEach((thumb, index) => {
        thumb.parentElement.addEventListener('click', function() {
            const src = thumb.getAttribute('data-src') || thumb.getAttribute('src');
            
            // Update main image
            if (mainImage.classList.contains('progressive-image')) {
                mainImage.setAttribute('data-src', src);
                // Force load if using progressive loading
                const newImg = new Image();
                newImg.onload = function() {
                    mainImage.setAttribute('src', src);
                    mainImage.classList.add('loaded');
                };
                newImg.src = src;
            } else {
                mainImage.setAttribute('src', src);
            }
            
            // Update active state
            thumbs.forEach(t => t.parentElement.classList.remove('active'));
            thumb.parentElement.classList.add('active');
        });
    });
}

/**
 * Add mobile product actions bar
 */
function addMobileProductActions() {
    // Only add on mobile
    if (window.innerWidth >= 768) return;
    
    const productPrice = document.querySelector('.product-price');
    const addToCartBtn = document.querySelector('.add-to-cart-btn');
    
    if (!productPrice || !addToCartBtn) return;
    
    // Create mobile actions bar
    const actionsBar = document.createElement('div');
    actionsBar.className = 'mobile-product-actions';
    
    // Add price
    const priceElement = document.createElement('div');
    priceElement.className = 'mobile-product-price';
    priceElement.innerHTML = productPrice.innerHTML;
    
    // Clone add to cart button
    const cartBtn = addToCartBtn.cloneNode(true);
    cartBtn.classList.add('mobile-cart-btn');
    
    // Add elements to actions bar
    actionsBar.appendChild(priceElement);
    actionsBar.appendChild(cartBtn);
    
    // Add to body
    document.body.appendChild(actionsBar);
    
    // Sync the mobile cart button with the original
    cartBtn.addEventListener('click', function(e) {
        e.preventDefault();
        addToCartBtn.click();
    });
}

/**
 * Enable pinch-to-zoom for product images
 */
function enablePinchZoom() {
    // Only enable on mobile
    if (window.innerWidth >= 768) return;
    
    const mainImage = document.querySelector('.product-image-main img');
    if (!mainImage) return;
    
    // Create zoom overlay
    const overlay = document.createElement('div');
    overlay.className = 'image-zoom-overlay';
    
    // Create zoom container
    const container = document.createElement('div');
    container.className = 'image-zoom-container';
    
    // Create close button
    const closeBtn = document.createElement('button');
    closeBtn.className = 'zoom-close';
    closeBtn.innerHTML = '<i class="fas fa-times"></i>';
    closeBtn.setAttribute('aria-label', 'Close zoom');
    
    // Create zoomed image
    const zoomedImg = document.createElement('img');
    zoomedImg.className = 'zoomed-image';
    
    // Add elements to overlay
    container.appendChild(zoomedImg);
    overlay.appendChild(closeBtn);
    overlay.appendChild(container);
    
    // Add overlay to body
    document.body.appendChild(overlay);
    
    // Add click event to main image
    mainImage.addEventListener('click', function() {
        // Get image source
        const src = mainImage.getAttribute('src') || mainImage.getAttribute('data-src');
        zoomedImg.setAttribute('src', src);
        
        // Show overlay
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent scrolling
    });
    
    // Add close event
    closeBtn.addEventListener('click', function() {
        overlay.classList.remove('active');
        document.body.style.overflow = ''; // Restore scrolling
    });
    
    // Close on overlay click (but not on image)
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay || e.target === container) {
            overlay.classList.remove('active');
            document.body.style.overflow = ''; // Restore scrolling
        }
    });
    
    // Enable pinch-to-zoom on zoomed image (using CSS transform)
    let currentScale = 1;
    let startDistance = 0;
    
    // Touch start event
    zoomedImg.addEventListener('touchstart', function(e) {
        if (e.touches.length === 2) {
            // Get initial distance between two touches
            startDistance = Math.hypot(
                e.touches[0].pageX - e.touches[1].pageX,
                e.touches[0].pageY - e.touches[1].pageY
            );
        }
    }, { passive: true });
    
    // Touch move event
    zoomedImg.addEventListener('touchmove', function(e) {
        if (e.touches.length === 2) {
            // Get current distance between two touches
            const currentDistance = Math.hypot(
                e.touches[0].pageX - e.touches[1].pageX,
                e.touches[0].pageY - e.touches[1].pageY
            );
            
            // Calculate new scale
            let newScale = currentScale * (currentDistance / startDistance);
            
            // Limit scale
            newScale = Math.min(Math.max(1, newScale), 3);
            
            // Apply scale
            zoomedImg.style.transform = `scale(${newScale})`;
            
            // Update start distance and current scale
            startDistance = currentDistance;
            currentScale = newScale;
            
            // Prevent default to avoid page zoom
            e.preventDefault();
        }
    }, { passive: false });
    
    // Reset scale on touch end
    zoomedImg.addEventListener('touchend', function() {
        startDistance = 0;
    }, { passive: true });
}
