<?php
// Set page title
$page_title = 'Order #' . $order['id'] . ' - AgroSmart Market';

// Get correct path for includes
$root_path = dirname(dirname(dirname(__FILE__)));

// Include header
include_once $root_path . '/views/partials/header.php';
?>

<style>
    .product-image {
        width: 100%;
        max-width: 150px;
        height: auto;
        border-radius: 8px;
        object-fit: cover;
    }
    
    .timeline {
        position: relative;
        padding-left: 30px;
        margin-top: 20px;
    }
    
    .timeline:before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 2px;
        background-color: #ddd;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 20px;
    }
    
    .timeline-badge {
        position: absolute;
        left: -38px;
        top: 0;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: #ddd;
        border: 3px solid #fff;
    }
    
    .timeline-badge.active {
        background-color: var(--primary-color);
    }
    
    .timeline-content {
        padding-left: 10px;
    }
</style>

    <!-- Main Content -->
    <div class="container py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Order #<?php echo $order['id']; ?></h2>
            <a href="order.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Orders
            </a>
        </div>
        
        <!-- Order Status -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="card-title">Order Status</h5>
                        <?php
                            $status_class = '';
                            $status_icon = '';
                            switch ($order['status']) {
                                case 'pending':
                                    $status_class = 'bg-warning';
                                    $status_icon = 'fa-clock';
                                    break;
                                case 'confirmed':
                                    $status_class = 'bg-info';
                                    $status_icon = 'fa-thumbs-up';
                                    break;
                                case 'shipped':
                                    $status_class = 'bg-primary';
                                    $status_icon = 'fa-truck';
                                    break;
                                case 'delivered':
                                    $status_class = 'bg-success';
                                    $status_icon = 'fa-check-circle';
                                    break;
                                case 'cancelled':
                                    $status_class = 'bg-danger';
                                    $status_icon = 'fa-times-circle';
                                    break;
                                default:
                                    $status_class = 'bg-secondary';
                                    $status_icon = 'fa-question-circle';
                            }
                        ?>
                        <div class="d-flex align-items-center">
                            <span class="badge <?php echo $status_class; ?> p-2 me-2">
                                <i class="fas <?php echo $status_icon; ?> me-1"></i>
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                            <span class="text-muted">Order placed on <?php echo date('F d, Y', strtotime($order['date_ordered'])); ?></span>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <?php if (is_farmer() && $order['status'] === 'pending'): ?>
                            <a href="order.php?action=update&id=<?php echo $order['id']; ?>" class="btn btn-success">
                                <i class="fas fa-check me-2"></i>Update Status
                            </a>
                        <?php elseif (!is_farmer() && $order['status'] === 'pending'): ?>
                            <a href="order.php?action=update&id=<?php echo $order['id']; ?>" class="btn btn-danger">
                                <i class="fas fa-times me-2"></i>Cancel Order
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Details -->
        <div class="row">
            <!-- Order Information -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-3 text-center mb-3 mb-md-0">
                                <?php if (!empty($order['product_image'])): ?>
                                    <img src="public/uploads/<?php echo $order['product_image']; ?>" class="product-image" alt="<?php echo $order['product_name']; ?>">
                                <?php else: ?>
                                    <img src="public/img/default-product.jpg" class="product-image" alt="<?php echo $order['product_name']; ?>">
                                <?php endif; ?>
                            </div>
                            <div class="col-md-9">
                                <h5><?php echo $order['product_name']; ?></h5>
                                <p class="text-muted mb-1">
                                    <i class="fas fa-user me-1"></i>
                                    <?php echo is_farmer() ? 'Buyer: ' . $order['buyer_name'] : 'Seller: ' . $order['farmer_name']; ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <p class="mb-0"><strong>Quantity:</strong> <?php echo $order['quantity']; ?></p>
                                        <p class="mb-0"><strong>Price per unit:</strong> <?php echo format_price($order['price']); ?></p>
                                    </div>
                                    <div>
                                        <h5 class="text-primary mb-0">Total: <?php echo format_price($order['price'] * $order['quantity']); ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- Order Timeline -->
                        <h5 class="mb-3">Order Timeline</h5>
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-badge active"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-0">Order Placed</h6>
                                    <p class="text-muted small mb-0"><?php echo date('F d, Y h:i A', strtotime($order['date_ordered'])); ?></p>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-badge <?php echo ($order['status'] == 'confirmed' || $order['status'] == 'shipped' || $order['status'] == 'delivered') ? 'active' : ''; ?>"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-0">Order Confirmed</h6>
                                    <?php if ($order['status'] == 'confirmed' || $order['status'] == 'shipped' || $order['status'] == 'delivered'): ?>
                                        <p class="text-muted small mb-0">The seller has confirmed your order</p>
                                    <?php else: ?>
                                        <p class="text-muted small mb-0">Waiting for seller confirmation</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-badge <?php echo ($order['status'] == 'shipped' || $order['status'] == 'delivered') ? 'active' : ''; ?>"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-0">Order Shipped</h6>
                                    <?php if ($order['status'] == 'shipped' || $order['status'] == 'delivered'): ?>
                                        <p class="text-muted small mb-0">Your order is on its way</p>
                                    <?php else: ?>
                                        <p class="text-muted small mb-0">Waiting for shipment</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-badge <?php echo ($order['status'] == 'delivered') ? 'active' : ''; ?>"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-0">Order Delivered</h6>
                                    <?php if ($order['status'] == 'delivered'): ?>
                                        <p class="text-muted small mb-0">Your order has been delivered</p>
                                    <?php else: ?>
                                        <p class="text-muted small mb-0">Waiting for delivery</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <?php if ($order['status'] == 'cancelled'): ?>
                                <div class="timeline-item">
                                    <div class="timeline-badge active" style="background-color: #dc3545; border-color: #dc3545;"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-0 text-danger">Order Cancelled</h6>
                                        <p class="text-muted small mb-0">This order has been cancelled</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><?php echo is_farmer() ? 'Buyer' : 'Seller'; ?> Information</h5>
                    </div>
                    <div class="card-body">
                        <?php if (is_farmer()): ?>
                            <!-- Show buyer info to farmer -->
                            <h6><?php echo $order['buyer_name']; ?></h6>
                            <p class="mb-1">
                                <i class="fas fa-envelope me-2 text-muted"></i><?php echo $order['buyer_email']; ?>
                            </p>
                        <?php else: ?>
                            <!-- Show farmer info to buyer -->
                            <h6><?php echo $order['farmer_name']; ?></h6>
                            <p class="mb-1">
                                <i class="fas fa-envelope me-2 text-muted"></i><?php echo $order['farmer_email']; ?>
                            </p>
                        <?php endif; ?>
                        
                        <hr>
                        
                        <div class="d-grid">
                            <a href="message.php?action=compose&to=<?php echo is_farmer() ? $order['buyer_id'] : $order['farmer_id']; ?>" class="btn btn-outline-primary">
                                <i class="fas fa-envelope me-2"></i>Send Message
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <?php if (is_farmer() && $order['status'] === 'pending'): ?>
                                <a href="order.php?action=update&id=<?php echo $order['id']; ?>" class="btn btn-success">
                                    <i class="fas fa-check-circle me-2"></i>Confirm Order
                                </a>
                            <?php elseif (is_farmer() && $order['status'] === 'confirmed'): ?>
                                <a href="order.php?action=update&id=<?php echo $order['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-truck me-2"></i>Mark as Shipped
                                </a>
                            <?php elseif (is_farmer() && $order['status'] === 'shipped'): ?>
                                <a href="order.php?action=update&id=<?php echo $order['id']; ?>" class="btn btn-info">
                                    <i class="fas fa-check-circle me-2"></i>Mark as Delivered
                                </a>
                            <?php elseif (!is_farmer() && $order['status'] === 'pending'): ?>
                                <a href="order.php?action=update&id=<?php echo $order['id']; ?>" class="btn btn-danger">
                                    <i class="fas fa-times-circle me-2"></i>Cancel Order
                                </a>
                            <?php endif; ?>
                            
                            <a href="product.php?action=view&id=<?php echo $order['product_id']; ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-eye me-2"></i>View Product
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
// Include footer
include_once $root_path . '/views/partials/footer.php';
?>
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
