<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - AgroSmart Market</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #4CAF50;
            --secondary-color: #FFC107;
            --dark-color: #333;
            --light-color: #f4f4f4;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-color);
        }
        
        .navbar {
            background-color: var(--primary-color);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .product-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .badge-category {
            background-color: var(--secondary-color);
            color: var(--dark-color);
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-leaf me-2"></i>AgroSmart Market
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../marketplace.php">Marketplace</a>
                    </li>
                    <?php if (is_logged_in() && is_farmer()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="product.php">My Products</a>
                        </li>
                    <?php endif; ?>
                    <?php if (is_logged_in()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="order.php">Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="message.php">Messages</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (is_logged_in()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i><?php echo $_SESSION['user_name']; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="../dashboard.php">Dashboard</a></li>
                                <li><a class="dropdown-item" href="../profile.php">My Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="auth.php?action=logout">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="auth.php?action=login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="auth.php?action=register">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

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
                    <img src="../public/uploads/<?php echo $product['image']; ?>" class="product-image" alt="<?php echo $product['name']; ?>">
                <?php else: ?>
                    <img src="../images/<?php echo strtolower(str_replace(' ', '_', $product['category'])); ?>.jpg" class="product-image" alt="<?php echo $product['name']; ?>" onerror="this.src='../images/default-product.jpg'">
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
            require_once '../models/Product.php';
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
                                    <img src="../public/uploads/<?php echo $similar_product['image']; ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="<?php echo $similar_product['name']; ?>">
                                <?php else: ?>
                                    <img src="../images/<?php echo strtolower(str_replace(' ', '_', $similar_product['category'])); ?>.jpg" class="card-img-top" style="height: 180px; object-fit: cover;" alt="<?php echo $similar_product['name']; ?>" onerror="this.src='../images/default-product.jpg'">
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
    
    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-leaf me-2"></i>AgroSmart Market</h5>
                    <p class="small">Connecting farmers directly with buyers</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="small">&copy; <?php echo date('Y'); ?> AgroSmart Market. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
