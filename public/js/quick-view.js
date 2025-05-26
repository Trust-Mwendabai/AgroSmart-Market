/**
 * Quick View Functionality for AgroSmart Market
 * 
 * This script handles the quick view functionality on the marketplace page,
 * loading product details via AJAX and displaying them in a modal.
 */
document.addEventListener('DOMContentLoaded', function() {
    // Track recently viewed products in session storage
    function trackViewedProduct(productId) {
        let recentlyViewed = JSON.parse(sessionStorage.getItem('recentlyViewed') || '[]');
        // Remove product if already in the list
        recentlyViewed = recentlyViewed.filter(id => id !== productId);
        // Add product to the beginning of the array
        recentlyViewed.unshift(productId);
        // Keep only the most recent 10 products
        if (recentlyViewed.length > 10) {
            recentlyViewed = recentlyViewed.slice(0, 10);
        }
        // Save back to session storage
        sessionStorage.setItem('recentlyViewed', JSON.stringify(recentlyViewed));
        
        // Also update server-side session via AJAX
        fetch('api/track_viewed_product.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ product_id: productId }),
        });
    }

    // Handle quick view button clicks
    const quickViewButtons = document.querySelectorAll('.quick-view-btn');
    
    quickViewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-id');
            
            // Show loading spinner in the modal
            document.getElementById('quickview-image').innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
            document.getElementById('quickview-content').innerHTML = '<div class="text-center p-5"><h5>Loading product details...</h5></div>';
            
            // Update add to cart button product ID
            document.getElementById('quickview-add-to-cart').setAttribute('data-product-id', productId);
            
            // Update view details link
            document.getElementById('quickview-view-details').href = `product.php?id=${productId}`;
            
            // Show the modal
            const quickViewModal = new bootstrap.Modal(document.getElementById('quickViewModal'));
            quickViewModal.show();
            
            // Load product details via AJAX
            fetch(`api/get_product_details.php?id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const product = data.product;
                        
                        // Update modal title
                        document.getElementById('quickViewModalLabel').textContent = product.name;
                        
                        // Update product image
                        let imageHtml = '';
                        if (product.image && product.image !== '') {
                            imageHtml = `<img src="public/uploads/products/${product.image}" class="img-fluid rounded" alt="${product.name}" onerror="this.src='public/images/default-product.jpg'">`;
                        } else {
                            // Try category image
                            const categoryImg = `public/images/categories/${product.category.toLowerCase()}.jpg`;
                            imageHtml = `<img src="${categoryImg}" class="img-fluid rounded" alt="${product.name}" onerror="this.src='public/images/default-product.jpg'">`;
                        }
                        document.getElementById('quickview-image').innerHTML = imageHtml;
                        
                        // Format price
                        const formattedPrice = new Intl.NumberFormat('en-ZM', { 
                            style: 'currency', 
                            currency: 'ZMW',
                            minimumFractionDigits: 2
                        }).format(product.price);
                        
                        // Generate star rating HTML
                        let ratingHtml = '<div class="rating-stars mb-2">';
                        const avgRating = parseFloat(product.avg_rating || 0);
                        for (let i = 1; i <= 5; i++) {
                            if (i <= avgRating) {
                                ratingHtml += '<i class="fas fa-star text-warning"></i>';
                            } else if (i <= avgRating + 0.5) {
                                ratingHtml += '<i class="fas fa-star-half-alt text-warning"></i>';
                            } else {
                                ratingHtml += '<i class="far fa-star text-warning"></i>';
                            }
                        }
                        ratingHtml += ` <small class="text-muted">(${product.review_count || 0} reviews)</small></div>`;
                        
                        // Update product details
                        let contentHtml = `
                            <h4 class="mb-3">${product.name}</h4>
                            <div class="mb-3">
                                <span class="badge bg-primary">${product.category}</span>
                                ${product.stock > 0 ? `<span class="badge bg-success ms-2">In Stock (${product.stock})</span>` : '<span class="badge bg-danger ms-2">Out of Stock</span>'}
                            </div>
                            <div class="mb-3">
                                <h5 class="text-primary fw-bold">${formattedPrice}</h5>
                            </div>
                            ${ratingHtml}
                            <p class="mb-3">${product.description}</p>
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i> Seller: ${product.farmer_name}
                                </small>
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i> Location: ${product.farmer_location}
                                </small>
                            </div>
                        `;
                        
                        document.getElementById('quickview-content').innerHTML = contentHtml;
                        
                        // Disable add to cart button if out of stock
                        const addToCartBtn = document.getElementById('quickview-add-to-cart');
                        if (product.stock <= 0) {
                            addToCartBtn.disabled = true;
                            addToCartBtn.textContent = 'Out of Stock';
                        } else {
                            addToCartBtn.disabled = false;
                            addToCartBtn.textContent = 'Add to Cart';
                        }
                        
                        // Track this product as viewed
                        trackViewedProduct(productId);
                    } else {
                        document.getElementById('quickview-content').innerHTML = '<div class="alert alert-danger">Error loading product details.</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('quickview-content').innerHTML = '<div class="alert alert-danger">Error loading product details. Please try again later.</div>';
                });
        });
    });
    
    // Handle Add to Cart button in the modal
    document.getElementById('quickview-add-to-cart').addEventListener('click', function() {
        const productId = this.getAttribute('data-product-id');
        
        // Show loading state
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...';
        
        // Add to cart via AJAX
        fetch('cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=add&product_id=${productId}&quantity=1`,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                this.innerHTML = '<i class="fas fa-check"></i> Added to Cart';
                this.classList.remove('btn-success');
                this.classList.add('btn-outline-success');
                
                // Update cart count if applicable
                if (document.getElementById('cart-count')) {
                    const currentCount = parseInt(document.getElementById('cart-count').textContent || '0');
                    document.getElementById('cart-count').textContent = currentCount + 1;
                }
                
                // Optional: Close the modal after a short delay
                setTimeout(() => {
                    bootstrap.Modal.getInstance(document.getElementById('quickViewModal')).hide();
                    
                    // Reset button state
                    setTimeout(() => {
                        this.innerHTML = 'Add to Cart';
                        this.classList.remove('btn-outline-success');
                        this.classList.add('btn-success');
                        this.disabled = false;
                    }, 500);
                }, 1500);
            } else {
                // Show error message
                this.innerHTML = 'Error';
                this.classList.remove('btn-success');
                this.classList.add('btn-danger');
                alert('Error adding item to cart: ' + (data.message || 'Unknown error'));
                
                // Reset button after delay
                setTimeout(() => {
                    this.innerHTML = 'Add to Cart';
                    this.classList.remove('btn-danger');
                    this.classList.add('btn-success');
                    this.disabled = false;
                }, 2000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.innerHTML = 'Error';
            this.classList.remove('btn-success');
            this.classList.add('btn-danger');
            
            // Reset button after delay
            setTimeout(() => {
                this.innerHTML = 'Add to Cart';
                this.classList.remove('btn-danger');
                this.classList.add('btn-success');
                this.disabled = false;
            }, 2000);
        });
    });
});
