<?php 
// Set page title
$page_title = htmlspecialchars($product['name']) . ' - AgroSmart Market';

// Include header
require_once __DIR__ . '/../partials/header.php';
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
    <div class="container py-4">
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
                                <div class="d-grid">
                                    <a href="order.php?action=create&product_id=<?php echo $product['id']; ?>" class="btn btn-primary btn-lg">
                                        <i class="fas fa-shopping-cart me-2"></i>Order Now
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
    </div>
    
<?php
// Include footer
require_once __DIR__ . '/../../views/partials/footer.php';
?>
