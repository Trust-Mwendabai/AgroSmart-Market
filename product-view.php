<?php
// Start session
session_start();

// Include database connection and utilities
$conn = require_once 'config/database.php';
require_once 'config/utils.php';

// Include models
require_once 'models/Product.php';
require_once 'models/Cart.php';

// Initialize models
$product_model = new Product($conn);
$cart_model = new Cart($conn);

// Handle actions
$message = '';
$error = '';
$product = null;

// Check for product ID parameter
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    $product = $product_model->get_product_by_id($product_id);
    
    if (!$product) {
        $error = "Product not found";
    } else {
        // Track this product in recently viewed
        if (!isset($_SESSION['recently_viewed'])) {
            $_SESSION['recently_viewed'] = [];
        }
        
        // Remove the product if it already exists in the list
        $_SESSION['recently_viewed'] = array_filter($_SESSION['recently_viewed'], function($id) use ($product_id) {
            return $id != $product_id;
        });
        
        // Add the product ID to the beginning of the array
        array_unshift($_SESSION['recently_viewed'], $product_id);
        
        // Limit to 8 most recent products
        if (count($_SESSION['recently_viewed']) > 8) {
            $_SESSION['recently_viewed'] = array_slice($_SESSION['recently_viewed'], 0, 8);
        }
    }
} else {
    $error = "Invalid product ID";
}

// Handle cart actions
if (isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    // Verify CSRF token
    $csrf_valid = verify_csrf_token($_POST['csrf_token'] ?? '');
    
    if (!$csrf_valid) {
        $error = "Invalid request. Please try again.";
    } else if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
        $product_id = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        
        $result = $cart_model->add_item($product_id, $quantity);
        
        if (isset($result['success'])) {
            $message = $result['message'];
        } else {
            $error = $result['error'];
        }
    }
}

// Set page title
$page_title = $product ? $product['name'] . " - AgroSmart Market" : "Product Not Found - AgroSmart Market";

// Include header
include 'views/partials/header.php';
?>

<div class="container py-5">
    <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        
        <div class="text-center py-5">
            <h3>Product Not Found</h3>
            <p class="mb-4">Sorry, the product you requested could not be found.</p>
            <a href="marketplace.php" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>Return to Marketplace
            </a>
        </div>
    <?php else: ?>
        <!-- Product Information -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <?php if (!empty($product['image'])): ?>
                            <img src="public/uploads/products/<?php echo $product['image']; ?>" class="img-fluid rounded" alt="<?php echo $product['name']; ?>">
                        <?php else: ?>
                            <div class="text-center p-5 bg-light rounded">
                                <i class="fas fa-seedling fa-5x text-muted"></i>
                                <p class="mt-3 text-muted">No image available</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="marketplace.php">Marketplace</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo $product['name']; ?></li>
                    </ol>
                </nav>
                
                <h1 class="fw-bold mb-2"><?php echo $product['name']; ?></h1>
                
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-light text-dark me-2"><?php echo $product['category']; ?></span>
                    <?php if (isset($product['is_organic']) && $product['is_organic']): ?>
                        <span class="badge bg-success me-2">Organic</span>
                    <?php endif; ?>
                    <span class="text-muted">
                        <i class="fas fa-map-marker-alt me-1"></i><?php echo $product['location'] ?? 'Local'; ?>
                    </span>
                </div>
                
                <h2 class="text-primary fw-bold mb-4"><?php echo format_price($product['price']); ?></h2>
                
                <div class="stock-info mb-4">
                    <?php if ($product['stock'] > 0): ?>
                        <span class="badge bg-success p-2">
                            <i class="fas fa-check-circle me-1"></i>In Stock: <?php echo $product['stock']; ?> available
                        </span>
                    <?php else: ?>
                        <span class="badge bg-danger p-2">
                            <i class="fas fa-times-circle me-1"></i>Out of Stock
                        </span>
                    <?php endif; ?>
                </div>
                
                <div class="mb-4">
                    <h5 class="fw-bold">Description</h5>
                    <p><?php echo nl2br($product['description']); ?></p>
                </div>
                
                <?php if ($product['stock'] > 0): ?>
                    <form action="product-view.php?id=<?php echo $product['id']; ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <input type="hidden" name="action" value="add_to_cart">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                            </div>
                            <div class="col-md-9 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-lg px-4">
                                    <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                </button>
                                <?php if (isset($product['farmer_id'])): ?>
                                    <a href="message.php?action=compose&to=<?php echo $product['farmer_id']; ?>&product_id=<?php echo $product['id']; ?>" class="btn btn-outline-secondary btn-lg ms-3">
                                        <i class="fas fa-comment me-2"></i>Contact Farmer
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This product is currently out of stock. Please check back later or contact the farmer for more information.
                    </div>
                    
                    <?php if (isset($product['farmer_id'])): ?>
                        <a href="message.php?action=compose&to=<?php echo $product['farmer_id']; ?>&product_id=<?php echo $product['id']; ?>" class="btn btn-outline-primary">
                            <i class="fas fa-comment me-2"></i>Contact Farmer
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                
                <div class="farmer-info mt-5">
                    <?php if (isset($product['farmer_id'])): ?>
                        <?php 
                        // Get farmer info
                        $sql = "SELECT * FROM users WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $product['farmer_id']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $farmer = $result->fetch_assoc();
                        ?>
                        
                        <?php if ($farmer): ?>
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h5 class="mb-3"><i class="fas fa-user-circle me-2"></i>Sold by</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <?php if (!empty($farmer['image'])): ?>
                                                <img src="public/uploads/users/<?php echo $farmer['image']; ?>" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;" alt="<?php echo $farmer['name']; ?>">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <h6 class="mb-1"><?php echo $farmer['name']; ?></h6>
                                            <p class="small text-muted mb-0">
                                                <i class="fas fa-map-marker-alt me-1"></i><?php echo $farmer['location'] ?? 'Local Farmer'; ?>
                                            </p>
                                        </div>
                                        <div class="ms-auto">
                                            <a href="farmer-profile.php?id=<?php echo $farmer['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                View Profile
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Related Products -->
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">Similar Products</h3>
            
            <?php
            // Get products from the same category
            $similar_products = [];
            if ($product) {
                $similar_products = $product_model->get_products_by_category($product['category'], 4, $product['id']);
            }
            ?>
            
            <?php if (!empty($similar_products)): ?>
                <div class="row g-4">
                    <?php foreach ($similar_products as $similar_product): ?>
                        <div class="col-md-6 col-lg-3">
                            <div class="product-card h-100">
                                <div class="img-container">
                                    <?php if (!empty($similar_product['image'])): ?>
                                        <img src="public/uploads/products/<?php echo $similar_product['image']; ?>" class="product-img" alt="<?php echo $similar_product['name']; ?>">
                                    <?php else: ?>
                                        <div class="product-placeholder">
                                            <i class="fas fa-seedling"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($similar_product['is_organic']) && $similar_product['is_organic']): ?>
                                        <span class="organic-badge card-badge">Organic</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="card-body p-3">
                                    <h5 class="card-title mb-2"><?php echo $similar_product['name']; ?></h5>
                                    <p class="card-text text-truncate small text-muted"><?php echo $similar_product['description']; ?></p>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="fw-bold"><?php echo format_price($similar_product['price']); ?></span>
                                        <a href="product-view.php?id=<?php echo $similar_product['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-light text-center">
                    <p class="mb-0">No similar products found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Include footer
include 'views/partials/footer.php';
?>
