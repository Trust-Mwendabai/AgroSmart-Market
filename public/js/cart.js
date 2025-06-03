console.log('cart.js loaded');

// Use event delegation for dynamically added elements
// Handle add to cart form submissions
document.addEventListener('submit', function(e) {
    console.log('Form submitted');
    console.log('Form data:', new FormData(e.target));
    // Check if the submitted form has the add-to-cart class
    const form = e.target.closest('form.add-to-cart-form');
    if (!form) return;
    
    e.preventDefault();
    
    const addToCartBtn = form.querySelector('.add-to-cart');
    if (!addToCartBtn) {
        console.error('No add to cart button found in form');
        return;
    }
    
    const formData = new FormData(form);
    const productId = formData.get('product_id');
    const quantity = formData.get('quantity') || 1;
    
    if (!productId) {
        console.error('No product ID found in form');
        return;
    }
    
    // Show loading state
    const originalText = addToCartBtn.innerHTML;
    addToCartBtn.disabled = true;
    addToCartBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...';
    
    // Send AJAX request
    fetch('ajax/cart_actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count in header
            updateCartCount(data.cart_count);
            
            // Show success message
            showToast('Success', data.message || 'Product added to cart', 'success');
            
            // Show the modal
            const addToCartModal = new bootstrap.Modal(document.getElementById('addToCartModal'));
            addToCartModal.show();
            
            // Update mini-cart if it exists
            if (data.cart_items) {
                updateMiniCart(data.cart_items, data.cart_total);
            }
        } else {
            showToast('Error', data.message || 'Failed to add to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error', 'An error occurred. Please try again.', 'error');
    })
    .finally(() => {
        // Reset button state
        addToCartBtn.disabled = false;
        addToCartBtn.innerHTML = originalText;
    });
});

// Handle quantity changes
document.addEventListener('DOMContentLoaded', function() {
    // Quantity controls
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            if (this.value < 1) {
                this.value = 1;
            } else if (this.max && this.value > this.max) {
                this.value = this.max;
            }
        });
    });
    
    // Quantity increase/decrease buttons
    document.addEventListener('click', function(e) {
        if (e.target.matches('.quantity-increase') || e.target.closest('.quantity-increase')) {
            const button = e.target.matches('.quantity-increase') ? e.target : e.target.closest('.quantity-increase');
            const targetId = button.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (input) {
                const max = input.getAttribute('max') ? parseInt(input.getAttribute('max')) : Infinity;
                if (parseInt(input.value) < max) {
                    input.value = parseInt(input.value) + 1;
                    input.dispatchEvent(new Event('change'));
                }
            }
        } else if (e.target.matches('.quantity-decrease') || e.target.closest('.quantity-decrease')) {
            const button = e.target.matches('.quantity-decrease') ? e.target : e.target.closest('.quantity-decrease');
            const targetId = button.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (input) {
                if (parseInt(input.value) > 1) {
                    input.value = parseInt(input.value) - 1;
                    input.dispatchEvent(new Event('change'));
                }
            }
        }
    });
});

// Update cart count in header
function updateCartCount(count) {
    const cartBadge = document.querySelector('.cart-count-badge');
    if (cartBadge) {
        cartBadge.textContent = count;
        if (count > 0) {
            cartBadge.style.display = 'inline-block';
        } else {
            cartBadge.style.display = 'none';
        }
    }
    
    // Also update any other elements that show cart count
    document.querySelectorAll('.cart-count').forEach(el => {
        el.textContent = count;
    });
}

// Update mini-cart content
function updateMiniCart(items, total) {
    const miniCart = document.querySelector('.mini-cart-items');
    const cartEmpty = document.querySelector('.cart-empty');
    const cartItems = document.querySelector('.cart-items');
    const cartTotal = document.querySelector('.cart-total');
    
    if (!items || Object.keys(items).length === 0) {
        if (miniCart) miniCart.innerHTML = `
            <div class="p-3 text-center">
                <i class="fas fa-shopping-cart fa-2x text-muted mb-2"></i>
                <p class="mb-0">Your cart is empty</p>
                <a href="marketplace.php" class="btn btn-outline-primary btn-sm mt-2">Start Shopping</a>
            </div>`;
        return;
    }
    
    // Update items list
    let itemsHtml = '';
    Object.values(items).slice(-3).forEach(item => {
        itemsHtml += `
            <div class="p-3 border-bottom">
                <div class="d-flex">
                    <div style="width: 60px; height: 60px; overflow: hidden;" class="me-3">
                        <img src="${item.image || 'assets/img/placeholder-product.png'}" 
                             alt="${item.name}" 
                             class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${item.name}</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Qty: ${item.quantity}</small>
                            <strong>K${(item.price * item.quantity).toFixed(2)}</strong>
                        </div>
                    </div>
                </div>
            </div>`;
    });
    
    if (miniCart) miniCart.innerHTML = itemsHtml + `
        <div class="p-3 border-top">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Total:</strong>
                <strong>K${parseFloat(total).toFixed(2)}</strong>
            </div>
            <a href="cart.php" class="btn btn-primary w-100 btn-sm">View Cart</a>
        </div>`;
    }
}

// Show toast notification
function showToast(title, message, type = 'info') {
    // Create toast container if it doesn't exist
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.role = 'alert';
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    // Add toast content
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <strong>${title}</strong><br>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>`;
    
    // Add to container
    toastContainer.appendChild(toast);
    
    // Initialize Bootstrap toast
    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 3000
    });
    
    // Show the toast
    bsToast.show();
    
    // Remove toast after it's hidden
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
    
    // Close button functionality
    const closeBtn = toast.querySelector('.btn-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        });
    }
}
