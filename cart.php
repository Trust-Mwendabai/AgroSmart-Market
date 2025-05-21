<?php
// Start session
session_start();

// Include database connection and utilities
$conn = require_once 'config/database.php';
require_once 'config/utils.php';

// Include models
require_once 'models/Cart.php';
require_once 'models/Product.php';

// Initialize models
$cart_model = new Cart($conn);
$product_model = new Product($conn);

// Handle cart actions
$message = '';
$error = '';

if (isset($_POST['action'])) {
    $csrf_valid = verify_csrf_token($_POST['csrf_token'] ?? '');
    
    if (!$csrf_valid) {
        $error = "Invalid request. Please try again.";
    } else {
        switch ($_POST['action']) {
            case 'add':
                if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
                    $result = $cart_model->add_item(
                        (int)$_POST['product_id'], 
                        (int)$_POST['quantity']
                    );
                    
                    if (isset($result['success'])) {
                        $message = $result['message'];
                    } else {
                        $error = $result['error'];
                    }
                }
                break;
                
            case 'update':
                if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
                    $result = $cart_model->update_item(
                        (int)$_POST['product_id'], 
                        (int)$_POST['quantity']
                    );
                    
                    if (isset($result['success'])) {
                        $message = $result['message'];
                    } else {
                        $error = $result['error'];
                    }
                }
                break;
                
            case 'remove':
                if (isset($_POST['product_id'])) {
                    $result = $cart_model->remove_item((int)$_POST['product_id']);
                    
                    if (isset($result['success'])) {
                        $message = $result['message'];
                    } else {
                        $error = $result['error'];
                    }
                }
                break;
                
            case 'clear':
                $result = $cart_model->clear_cart();
                
                if (isset($result['success'])) {
                    $message = $result['message'];
                } else {
                    $error = $result['error'];
                }
                break;
                
            case 'checkout':
                if (!is_logged_in()) {
                    $error = "Please log in to checkout";
                    break;
                }
                
                if (!is_buyer()) {
                    $error = "Only buyers can checkout";
                    break;
                }
                
                $result = $cart_model->checkout($_SESSION['user_id']);
                
                if (isset($result['success'])) {
                    // Redirect to order confirmation page
                    redirect('order.php?action=confirmation&orders=' . implode(',', $result['order_ids']));
                } else {
                    $error = $result['error'];
                }
                break;
        }
    }
}

// Get cart items with details
$cart_items = $cart_model->get_cart_items_with_details();
$cart = $cart_model->get_cart();

// Set page title
$page_title = "Shopping Cart - AgroSmart Market";

// Include header
include 'views/partials/header.php';
?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fw-bold mb-0">Shopping Cart</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="marketplace.php">Marketplace</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cart</li>
                </ol>
            </nav>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="row">
        <?php if (empty($cart_items)): ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <h4 class="alert-heading">Your cart is empty</h4>
                    <p>Looks like you haven't added any products to your cart yet.</p>
                    <hr>
                    <p class="mb-0">
                        <a href="marketplace.php" class="btn btn-primary">
                            <i class="fas fa-shopping-basket me-2"></i>Browse Products
                        </a>
                    </p>
                </div>
            </div>
        <?php else: ?>
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Cart Items (<?php echo $cart['total_quantity']; ?>)</h5>
                            <form action="cart.php" method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <input type="hidden" name="action" value="clear">
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to clear your cart?')">
                                    <i class="fas fa-trash me-2"></i>Clear Cart
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="ps-4">Product</th>
                                        <th scope="col" class="text-center">Price</th>
                                        <th scope="col" class="text-center">Quantity</th>
                                        <th scope="col" class="text-center">Subtotal</th>
                                        <th scope="col" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="product-img me-3" style="width: 60px; height: 60px; overflow: hidden; border-radius: 8px;">
                                                        <?php if (!empty($item['image'])): ?>
                                                            <img src="public/uploads/products/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="img-fluid">
                                                        <?php else: ?>
                                                            <div class="category-<?php echo strtolower(str_replace(' ', '_', $item['category'])); ?> img-placeholder" style="width: 60px; height: 60px; border-radius: 8px;">
                                                                <?php echo substr($item['name'], 0, 1); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-bold mb-1"><?php echo $item['name']; ?></h6>
                                                        <small class="text-muted">
                                                            <span class="badge bg-light text-dark"><?php echo $item['category']; ?></span>
                                                            <span class="ms-2">Sold by: <?php echo $item['farmer_name']; ?></span>
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <?php echo format_price($item['price']); ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <form action="cart.php" method="POST" class="quantity-form d-flex align-items-center justify-content-center">
                                                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                                    <input type="hidden" name="action" value="update">
                                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                                    
                                                    <div class="input-group" style="width: 120px;">
                                                        <button type="button" class="btn btn-outline-secondary quantity-btn" data-operation="minus">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <input type="number" class="form-control text-center quantity-input" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>">
                                                        <button type="button" class="btn btn-outline-secondary quantity-btn" data-operation="plus">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </form>
                                            </td>
                                            <td class="text-center align-middle fw-bold">
                                                <?php echo format_price($item['subtotal']); ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <form action="cart.php" method="POST">
                                                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                                    <input type="hidden" name="action" value="remove">
                                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <a href="marketplace.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                </a>
            </div>
            
            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span class="fw-bold"><?php echo format_price($cart['total_price']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Delivery Fee</span>
                            <span class="text-success">Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold">Total</span>
                            <span class="fw-bold fs-5"><?php echo format_price($cart['total_price']); ?></span>
                        </div>
                        
                        <?php if (is_logged_in() && is_buyer()): ?>
                            <form action="cart.php" method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <input type="hidden" name="action" value="checkout">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-check-circle me-2"></i>Proceed to Checkout
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-info mb-3">
                                <small>Please log in as a buyer to checkout</small>
                            </div>
                            <a href="auth.php?action=login" class="btn btn-primary w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Login to Checkout
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity increase/decrease functionality
    const quantityBtns = document.querySelectorAll('.quantity-btn');
    
    quantityBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const operation = this.dataset.operation;
            const inputField = this.closest('.input-group').querySelector('.quantity-input');
            let currentValue = parseInt(inputField.value);
            const maxValue = parseInt(inputField.getAttribute('max'));
            
            if (operation === 'plus' && currentValue < maxValue) {
                inputField.value = currentValue + 1;
            } else if (operation === 'minus' && currentValue > 1) {
                inputField.value = currentValue - 1;
            }
            
            // Automatically submit the form when quantity changes
            this.closest('form').submit();
        });
    });
});
</script>

<?php
// Include footer
include 'views/partials/footer.php';
?>
