<?php 
// Page title is set in the controller
?>

<style>
    .product-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 5px;
    }
    
    .badge-category {
        background-color: var(--bs-warning);
        color: var(--bs-dark);
    }
    
    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .seller-info {
        border-left: 3px solid var(--bs-success);
        padding-left: 15px;
    }
</style>

    <!-- Main Content -->
    <div class="container py-4" style="min-height: 60vh;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="../marketplace.php">Marketplace</a></li>
                <?php if (!empty($product['category'])): ?>
                    <li class="breadcrumb-item"><a href="../marketplace.php?category=<?php echo $product['category']; ?>"><?php echo ucfirst($product['category']); ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page"><?php echo $product['name']; ?></li>
            </ol>
        </nav>
        
        <!-- Product Details -->
        <div class="row">
            <!-- Product Image -->
            <div class="col-md-5 mb-4">
                <?php if (!empty($product['image'])): ?>
                    <?php 
                    // Check if the image path is already a full URL
                    if (filter_var($product['image'], FILTER_VALIDATE_URL)) {
                        $image_src = $product['image'];
                    } 
                    // Check if the path starts with 'public/uploads/'
                    else if (strpos($product['image'], 'public/uploads/') === 0) {
                        $image_src = '../' . $product['image'];
                    }
                    // Check if it's just a filename
                    else if (strpos($product['image'], '/') === false) {
                        $image_src = '../public/uploads/' . $product['image'];
                    }
                    // Use as is for any other cases
                    else {
                        $image_src = $product['image'];
                    }
                    ?>
                    <img src="<?php echo $image_src; ?>" class="product-image" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php else: ?>
                    <img src="../public/images/<?php echo strtolower(str_replace(' ', '_', $product['category'])); ?>.jpg" class="product-image" alt="<?php echo htmlspecialchars($product['name']); ?>" onerror="this.onerror=null; this.src='../public/images/default-product.jpg'">
                <?php endif; ?>
            </div>
            
            <!-- Product Info -->
            <div class="col-md-7">
                <div class="card">
                    <div class="card-body">
                        <h2 class="mb-2"><?php echo $product['name']; ?></h2>
                        
                        <div class="mb-3">
                            <?php if (!empty($product['category'])): ?>
                                <span class="badge badge-category"><?php echo ucfirst($product['category']); ?></span>
                            <?php endif; ?>
                            <span class="ms-2 text-muted"><i class="fas fa-clock me-1"></i>Listed on <?php echo date('F d, Y', strtotime($product['date_added'])); ?></span>
                        </div>
                        
                        <h3 class="text-primary mb-4"><?php echo format_price($product['price']); ?></h3>
                        
                        <div class="mb-4">
                            <h5>Description</h5>
                            <p><?php echo nl2br($product['description']); ?></p>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Availability</h5>
                            <?php if ($product['stock'] > 10): ?>
                                <div class="text-success"><i class="fas fa-check-circle me-2"></i>In Stock (<?php echo $product['stock']; ?> available)</div>
                            <?php elseif ($product['stock'] > 0): ?>
                                <div class="text-warning"><i class="fas fa-exclamation-circle me-2"></i>Low Stock (Only <?php echo $product['stock']; ?> left)</div>
                            <?php else: ?>
                                <div class="text-danger"><i class="fas fa-times-circle me-2"></i>Out of Stock</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-body bg-light">
                                <h5 class="mb-3">Seller Information</h5>
                                <div class="d-flex align-items-center mb-3">
                                    <?php if (!empty($farmer['profile_image'])): ?>
                                        <img src="../public/uploads/<?php echo $farmer['profile_image']; ?>" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;" alt="Farmer Profile">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; font-size: 24px;">
                                            <?php echo strtoupper(substr($farmer['name'], 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h6 class="mb-1"><?php echo $farmer['name']; ?></h6>
                                        <div><i class="fas fa-map-marker-alt me-1 text-muted"></i><?php echo $farmer['location']; ?></div>
                                    </div>
                                </div>
                                <?php if (is_logged_in() && $_SESSION['user_id'] != $product['farmer_id']): ?>
                                    <div class="d-grid gap-2">
                                        <a href="message.php?action=compose&to=<?php echo $product['farmer_id']; ?>&product_id=<?php echo $product['id']; ?>" class="btn btn-outline-primary">
                                            <i class="fas fa-envelope me-2"></i>Contact Seller
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if (is_logged_in()): ?>
                            <?php if ($_SESSION['user_id'] == $product['farmer_id']): ?>
                                <!-- Farmer's actions -->
                                <div class="d-flex gap-2">
                                    <a href="product.php?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-warning flex-grow-1">
                                        <i class="fas fa-edit me-2"></i>Edit Product
                                    </a>
                                    <a href="product.php?action=delete&id=<?php echo $product['id']; ?>" class="btn btn-danger flex-grow-1" onclick="return confirm('Are you sure you want to delete this product?')">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </a>
                                </div>
                            <?php elseif (is_buyer() && $product['stock'] > 0): ?>
                                <!-- Buyer's actions -->
                                <form class="add-to-cart-form" method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="quantity" class="form-label">Quantity</label>
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-secondary quantity-decrease" data-target="quantity">-</button>
                                                <input type="number" class="form-control text-center quantity-input" 
                                                       id="quantity" name="quantity" value="1" min="1" 
                                                       max="<?php echo $product['stock']; ?>" required>
                                                <button type="button" class="btn btn-outline-secondary quantity-increase" data-target="quantity">+</button>
                                            </div>
                                            <div class="form-text">Max: <?php echo $product['stock']; ?> available</div>
                                        </div>
                                        <div class="col-md-8 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary btn-lg w-100 add-to-cart">
                                                <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                
                                <div class="d-grid mt-2">
                                    <a href="order.php?action=create&product_id=<?php echo $product['id']; ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-bolt me-2"></i>Buy Now
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <!-- Not logged in actions -->
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>Please <a href="auth.php?action=login">login</a> or <a href="auth.php?action=register">register</a> to order this product.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Similar Products -->
        <div class="mt-5">
            <h3 class="mb-4">Similar Products</h3>
            
            <?php
            // Get some related products
            require_once dirname(__DIR__, 2) . '/models/Product.php';
            $product_model = new Product($conn);
            $filters = ['category' => $product['category']];
            $similar_products = $product_model->get_all_products(4, 0, $filters);
            
            // Remove current product from list
            foreach ($similar_products as $key => $similar_product) {
                if ($similar_product['id'] == $product['id']) {
                    unset($similar_products[$key]);
                    break;
                }
            }
            ?>
            
            <?php if (!empty($similar_products)): ?>
                <div class="row">
                    <?php foreach (array_slice($similar_products, 0, 4) as $similar_product): ?>
                        <div class="col-md-3 mb-4">
                            <div class="card h-100">
                                <?php if (!empty($similar_product['image'])): ?>
                                    <?php 
                                    // Check if the image path is already a full URL
                                    if (filter_var($similar_product['image'], FILTER_VALIDATE_URL)) {
                                        $image_src = $similar_product['image'];
                                    } 
                                    // Check if the path starts with 'public/uploads/'
                                    else if (strpos($similar_product['image'], 'public/uploads/') === 0) {
                                        $image_src = '../' . $similar_product['image'];
                                    }
                                    // Check if it's just a filename
                                    else if (strpos($similar_product['image'], '/') === false) {
                                        $image_src = '../public/uploads/' . $similar_product['image'];
                                    }
                                    // Use as is for any other cases
                                    else {
                                        $image_src = $similar_product['image'];
                                    }
                                    ?>
                                    <img src="<?php echo $image_src; ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="<?php echo htmlspecialchars($similar_product['name']); ?>">
                                <?php else: ?>
                                    <img src="../public/images/<?php echo strtolower(str_replace(' ', '_', $similar_product['category'])); ?>.jpg" class="card-img-top" style="height: 180px; object-fit: cover;" alt="<?php echo htmlspecialchars($similar_product['name']); ?>" onerror="this.onerror=null; this.src='../public/images/default-product.jpg'">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $similar_product['name']; ?></h5>
                                    <p class="card-text text-truncate"><?php echo $similar_product['description']; ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-primary"><?php echo format_price($similar_product['price']); ?></span>
                                        <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i><?php echo $similar_product['farmer_location']; ?></small>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <a href="product.php?action=view&id=<?php echo $similar_product['id']; ?>" class="btn btn-sm btn-outline-primary w-100">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No similar products found.
                </div>
            <?php endif; ?>
        </div>
    
    <!-- Add to Cart Modal -->
    <div class="modal fade" id="addToCartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Success!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-success me-3" style="font-size: 2.5rem;"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Item added to your cart</h5>
                            <p class="mb-0" id="cartModalMessage"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Continue Shopping</button>
                    <a href="cart.php" class="btn btn-primary">View Cart</a>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Quantity controls
        document.querySelectorAll('.quantity-increase').forEach(button => {
            button.addEventListener('click', function() {
                const target = this.getAttribute('data-target');
                const input = document.getElementById(target);
                const max = parseInt(input.getAttribute('max') || '999');
                let value = parseInt(input.value) || 0;
                if (value < max) {
                    input.value = value + 1;
                }
            });
        });

        document.querySelectorAll('.quantity-decrease').forEach(button => {
            button.addEventListener('click', function() {
                const target = this.getAttribute('data-target');
                const input = document.getElementById(target);
                const min = parseInt(input.getAttribute('min') || '1');
                let value = parseInt(input.value) || 1;
                if (value > min) {
                    input.value = value - 1;
                }
            });
        });

        // Handle add to cart form submission
        const addToCartForm = document.querySelector('.add-to-cart-form');
        if (addToCartForm) {
            addToCartForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const submitButton = this.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;
                
                // Disable button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Adding...';
                
                // Get form data
                const formData = new FormData(this);
                
                // Send AJAX request
                fetch('../ajax/cart_actions.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update cart count in header
                        updateCartCount(data.cart_count);
                        
                        // Show success message
                        const productName = '<?php echo addslashes($product["name"]); ?>';
                        const quantity = formData.get('quantity');
                        const modalMessage = document.getElementById('cartModalMessage');
                        modalMessage.textContent = `${quantity} x ${productName} has been added to your cart.`;
                        
                        // Show modal
                        const modal = new bootstrap.Modal(document.getElementById('addToCartModal'));
                        modal.show();
                        
                        // Update mini cart
                        if (data.cart_items) {
                            updateMiniCart(data.cart_items, data.cart_total);
                        }
                    } else {
                        // Show error message
                        showToast('Error', data.message || 'Failed to add to cart', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Error', 'An error occurred. Please try again.', 'error');
                })
                .finally(() => {
                    // Reset button state
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                });
            });
        }
        
        // Function to update cart count in header
        function updateCartCount(count) {
            const cartBadge = document.querySelector('.cart-count');
            const cartCountBadge = document.querySelector('.cart-count-badge');
            
            if (cartBadge) cartBadge.textContent = count;
            if (cartCountBadge) {
                if (count > 0) {
                    cartCountBadge.textContent = count;
                    cartCountBadge.style.display = 'inline-block';
                } else {
                    cartCountBadge.style.display = 'none';
                }
            }
        }
        
        // Function to update mini cart
        function updateMiniCart(items, total) {
            const miniCart = document.querySelector('.mini-cart-items');
            const cartEmpty = document.querySelector('.cart-empty');
            const cartItems = document.querySelector('.cart-items');
            const cartTotal = document.querySelector('.cart-total');
            
            if (!miniCart) return;
            
            if (!items || items.length === 0) {
                miniCart.innerHTML = `
                    <div class="p-3 text-center">
                        <i class="fas fa-shopping-cart fa-2x text-muted mb-2"></i>
                        <p class="mb-0">Your cart is empty</p>
                        <a href="marketplace.php" class="btn btn-outline-primary btn-sm mt-2">Start Shopping</a>
                    </div>`;
                return;
            }
            
            // Update items list
            let itemsHtml = '';
            items.slice(0, 3).forEach(item => {
                itemsHtml += `
                    <div class="p-3 border-bottom">
                        <div class="d-flex">
                            <div style="width: 60px; height: 60px; overflow: hidden;" class="me-3">
                                <img src="${item.image || '../assets/img/placeholder-product.png'}" 
                                     alt="${item.name}" 
                                     class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${item.name}</h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Qty: ${item.quantity}</small>
                                    <strong>${item.subtotal_formatted || 'K' + (item.price * item.quantity).toFixed(2)}</strong>
                                </div>
                            </div>
                        </div>
                    </div>`;
            });
            
            // Add view all items if more than 3
            if (items.length > 3) {
                itemsHtml += `
                    <div class="p-2 text-center border-top">
                        <small class="text-muted">+${items.length - 3} more item${items.length - 3 > 1 ? 's' : ''} in cart</small>
                    </div>`;
            }
            
            // Add total and view cart button
            itemsHtml += `
                <div class="p-3 border-top bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Total:</strong>
                        <strong>K${parseFloat(total).toFixed(2)}</strong>
                    </div>
                    <a href="cart.php" class="btn btn-primary w-100 btn-sm">View Cart</a>
                </div>`;
                
            miniCart.innerHTML = itemsHtml;
        }
        
        // Function to show toast notifications
        function showToast(title, message, type = 'info') {
            // Create toast container if it doesn't exist
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.style.position = 'fixed';
                toastContainer.style.top = '20px';
                toastContainer.style.right = '20px';
                toastContainer.style.zIndex = '9999';
                document.body.appendChild(toastContainer);
            }
            
            const toastId = 'toast-' + Date.now();
            const icon = {
                'success': 'check-circle',
                'error': 'exclamation-circle',
                'warning': 'exclamation-triangle',
                'info': 'info-circle'
            }[type] || 'info-circle';
            
            const toast = document.createElement('div');
            toast.id = toastId;
            toast.className = `toast show bg-${type} text-white mb-2`;
            toast.role = 'alert';
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            toast.style.minWidth = '300px';
            toast.style.borderRadius = '8px';
            toast.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
            
            toast.innerHTML = `
                <div class="toast-header bg-${type} text-white border-0">
                    <i class="fas fa-${icon} me-2"></i>
                    <strong class="me-auto">${title}</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>`;
            
            toastContainer.appendChild(toast);
            
            // Auto-remove toast after 5 seconds
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
            
            // Close button functionality
            const closeBtn = toast.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 300);
                });
            }
        }
    });
    </script>
</div>
