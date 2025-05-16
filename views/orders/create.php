<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order - AgroSmart Market</title>
    
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
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.25);
        }
        
        .product-image {
            max-height: 150px;
            width: auto;
            object-fit: cover;
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
                    <li class="nav-item">
                        <a class="nav-link active" href="order.php">Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="message.php">Messages</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
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
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Place an Order</h2>
            <a href="../marketplace.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Marketplace
            </a>
        </div>
        
        <!-- Alerts -->
        <?php if (isset($error) && !empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success) && !empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <!-- Order Form -->
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Details</h5>
                    </div>
                    <div class="card-body">
                        <form action="order.php?action=create&product_id=<?php echo $product['id']; ?>" method="POST">
                            <!-- CSRF Token -->
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            
                            <div class="row mb-4">
                                <div class="col-md-4 text-center">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="../public/uploads/<?php echo $product['image']; ?>" class="product-image rounded" alt="<?php echo $product['name']; ?>">
                                    <?php else: ?>
                                        <img src="../images/<?php echo strtolower(str_replace(' ', '_', $product['category'])); ?>.jpg" class="product-image rounded" alt="<?php echo $product['name']; ?>" onerror="this.src='../images/default-product.jpg'">
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-8">
                                    <h4><?php echo $product['name']; ?></h4>
                                    <p class="text-muted mb-1">
                                        <i class="fas fa-user me-1"></i>Seller: <?php echo $product['farmer_name']; ?>
                                    </p>
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-map-marker-alt me-1"></i>Location: <?php echo $product['farmer_location']; ?>
                                    </p>
                                    <?php if (!empty($product['category'])): ?>
                                        <p class="mb-2">
                                            <span class="badge bg-secondary"><?php echo ucfirst($product['category']); ?></span>
                                        </p>
                                    <?php endif; ?>
                                    <h5 class="text-primary mb-3"><?php echo format_price($product['price']); ?> per unit</h5>
                                    <p class="mb-0">
                                        <?php if ($product['stock'] > 10): ?>
                                            <span class="text-success"><i class="fas fa-check-circle me-2"></i>In Stock (<?php echo $product['stock']; ?> available)</span>
                                        <?php elseif ($product['stock'] > 0): ?>
                                            <span class="text-warning"><i class="fas fa-exclamation-circle me-2"></i>Low Stock (Only <?php echo $product['stock']; ?> left)</span>
                                        <?php else: ?>
                                            <span class="text-danger"><i class="fas fa-times-circle me-2"></i>Out of Stock</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity*</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" min="1" max="<?php echo $product['stock']; ?>" value="1" required onchange="updateTotal()">
                                <div class="form-text">Enter the quantity you want to order (maximum <?php echo $product['stock']; ?> available).</div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Order Total</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control bg-light" id="totalPrice" value="<?php echo number_format($product['price'], 2); ?>" readonly>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notes" class="form-label">Order Notes (Optional)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any special instructions for the seller?"></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-shopping-cart me-2"></i>Place Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Seller Information</h5>
                    </div>
                    <div class="card-body">
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
                        <hr>
                        <div class="d-grid">
                            <a href="message.php?action=compose&to=<?php echo $product['farmer_id']; ?>&product_id=<?php echo $product['id']; ?>" class="btn btn-outline-primary">
                                <i class="fas fa-envelope me-2"></i>Contact Seller
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Product:</span>
                            <span><?php echo $product['name']; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Price per unit:</span>
                            <span><?php echo format_price($product['price']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Quantity:</span>
                            <span id="summaryQuantity">1</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-0">
                            <strong>Total:</strong>
                            <strong id="summaryTotal"><?php echo format_price($product['price']); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
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
    
    <!-- Custom Script -->
    <script>
        function updateTotal() {
            const quantity = document.getElementById('quantity').value;
            const price = <?php echo $product['price']; ?>;
            const total = quantity * price;
            
            document.getElementById('totalPrice').value = total.toFixed(2);
            document.getElementById('summaryQuantity').textContent = quantity;
            document.getElementById('summaryTotal').textContent = '$' + total.toFixed(2);
        }
    </script>
</body>
</html>
