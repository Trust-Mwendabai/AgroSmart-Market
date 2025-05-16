<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order Status - AgroSmart Market</title>
    
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
            max-height: 120px;
            width: auto;
            border-radius: 5px;
        }
        
        .status-card {
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        
        .status-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .status-card.selected {
            border-color: var(--primary-color);
            background-color: rgba(76, 175, 80, 0.1);
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
                    <?php if (is_farmer()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="product.php">My Products</a>
                        </li>
                    <?php endif; ?>
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
            <h2>Update Order Status</h2>
            <a href="order.php?action=view&id=<?php echo $order['id']; ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Order Details
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
        
        <!-- Order Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Order #<?php echo $order['id']; ?></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center mb-3">
                            <?php if (!empty($order['product_image'])): ?>
                                <img src="../public/uploads/<?php echo $order['product_image']; ?>" class="product-image me-3" alt="<?php echo $order['product_name']; ?>">
                            <?php else: ?>
                                <img src="../images/default-product.jpg" class="product-image me-3" alt="<?php echo $order['product_name']; ?>">
                            <?php endif; ?>
                            <div>
                                <h5 class="mb-1"><?php echo $order['product_name']; ?></h5>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-user me-1"></i>
                                    <?php echo is_farmer() ? 'Buyer: ' . $order['buyer_name'] : 'Seller: ' . $order['farmer_name']; ?>
                                </p>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    Order Date: <?php echo date('M d, Y', strtotime($order['date_ordered'])); ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Quantity:</strong> <?php echo $order['quantity']; ?></p>
                                <p class="mb-1"><strong>Price per unit:</strong> <?php echo format_price($order['price']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Total Amount:</strong> <?php echo format_price($order['price'] * $order['quantity']); ?></p>
                                <p class="mb-1">
                                    <strong>Current Status:</strong>
                                    <?php
                                        $status_class = '';
                                        switch ($order['status']) {
                                            case 'pending':
                                                $status_class = 'bg-warning';
                                                break;
                                            case 'confirmed':
                                                $status_class = 'bg-info';
                                                break;
                                            case 'shipped':
                                                $status_class = 'bg-primary';
                                                break;
                                            case 'delivered':
                                                $status_class = 'bg-success';
                                                break;
                                            case 'cancelled':
                                                $status_class = 'bg-danger';
                                                break;
                                            default:
                                                $status_class = 'bg-secondary';
                                        }
                                    ?>
                                    <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($order['status']); ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Update Status Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Select New Status</h5>
            </div>
            <div class="card-body">
                <form action="order.php?action=update&id=<?php echo $order['id']; ?>" method="POST">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    
                    <?php if (is_farmer()): ?>
                        <!-- Status options for farmer -->
                        <div class="row mb-4">
                            <?php if ($order['status'] === 'pending'): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card status-card" onclick="selectStatus('confirmed')">
                                        <div class="card-body text-center">
                                            <i class="fas fa-thumbs-up fa-3x text-info mb-3"></i>
                                            <h5>Confirm Order</h5>
                                            <p class="text-muted mb-0">Accept the order and prepare for delivery</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card status-card" onclick="selectStatus('cancelled')">
                                        <div class="card-body text-center">
                                            <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                                            <h5>Cancel Order</h5>
                                            <p class="text-muted mb-0">Reject the order and refund the buyer</p>
                                        </div>
                                    </div>
                                </div>
                            <?php elseif ($order['status'] === 'confirmed'): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card status-card" onclick="selectStatus('shipped')">
                                        <div class="card-body text-center">
                                            <i class="fas fa-truck fa-3x text-primary mb-3"></i>
                                            <h5>Mark as Shipped</h5>
                                            <p class="text-muted mb-0">Order is on its way to the buyer</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card status-card" onclick="selectStatus('cancelled')">
                                        <div class="card-body text-center">
                                            <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                                            <h5>Cancel Order</h5>
                                            <p class="text-muted mb-0">Cancel the order and refund the buyer</p>
                                        </div>
                                    </div>
                                </div>
                            <?php elseif ($order['status'] === 'shipped'): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card status-card" onclick="selectStatus('delivered')">
                                        <div class="card-body text-center">
                                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                            <h5>Mark as Delivered</h5>
                                            <p class="text-muted mb-0">Order has been successfully delivered</p>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        This order is already <?php echo strtolower($order['status']); ?>. No further status updates are available.
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <!-- Status options for buyer -->
                        <?php if ($order['status'] === 'pending'): ?>
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3">
                                    <div class="card status-card" onclick="selectStatus('cancelled')">
                                        <div class="card-body text-center">
                                            <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                                            <h5>Cancel Order</h5>
                                            <p class="text-muted mb-0">Cancel your order and request a refund</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                This order is already being processed. Please contact the seller if you need to make changes.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <input type="hidden" name="status" id="selected_status" value="">
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Additional Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add any notes about this status update..."></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="order.php?action=view&id=<?php echo $order['id']; ?>" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="update_button" disabled>Update Status</button>
                    </div>
                </form>
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
        function selectStatus(status) {
            // Update hidden input value
            document.getElementById('selected_status').value = status;
            
            // Update UI to show selected card
            const cards = document.querySelectorAll('.status-card');
            cards.forEach(card => {
                card.classList.remove('selected');
            });
            
            const clickedCard = event.currentTarget;
            clickedCard.classList.add('selected');
            
            // Enable the update button
            document.getElementById('update_button').disabled = false;
        }
    </script>
</body>
</html>
