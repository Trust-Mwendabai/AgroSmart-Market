<!-- Buyer Dashboard Header -->
<div class="bg-primary text-white py-4 mb-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2">Buyer Dashboard</h1>
                <p class="lead mb-0">Manage your orders and account</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="marketplace.php" class="btn btn-light"><i class="fas fa-shopping-basket me-2"></i>Browse Products</a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <?php if (!empty($user['profile_image'])): ?>
                        <img src="public/uploads/<?php echo $user['profile_image']; ?>" class="rounded-circle img-fluid mb-3" style="width: 120px; height: 120px; object-fit: cover;" alt="Profile Image">
                    <?php else: ?>
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 120px; height: 120px; font-size: 48px;">
                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <h5 class="mb-1"><?php echo $user['name']; ?></h5>
                    <p class="text-muted mb-3"><?php echo $user['location']; ?></p>
                    <a href="profile.php" class="btn btn-outline-primary btn-sm"><i class="fas fa-edit me-2"></i>Edit Profile</a>
                </div>
            </div>

            <div class="list-group mb-4">
                <a href="dashboard.php" class="list-group-item list-group-item-action active">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a href="marketplace.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-shopping-basket me-2"></i>Marketplace
                </a>
                <a href="controllers/order.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-shopping-bag me-2"></i>My Orders
                </a>
                <a href="controllers/message.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-envelope me-2"></i>Messages
                    <?php if ($unread_messages > 0): ?>
                        <span class="badge bg-danger float-end"><?php echo $unread_messages; ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Total Orders</h6>
                                    <h2 class="mt-2 mb-0"><?php echo count($orders); ?></h2>
                                </div>
                                <i class="fas fa-shopping-bag fa-3x opacity-50"></i>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between bg-primary bg-opacity-75 border-top-0">
                            <a href="controllers/order.php" class="text-white text-decoration-none">View Details</a>
                            <i class="fas fa-arrow-circle-right text-white"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Active Orders</h6>
                                    <h2 class="mt-2 mb-0">
                                        <?php 
                                            $active_count = 0;
                                            foreach ($orders as $order) {
                                                if ($order['status'] !== 'delivered' && $order['status'] !== 'cancelled') {
                                                    $active_count++;
                                                }
                                            }
                                            echo $active_count;
                                        ?>
                                    </h2>
                                </div>
                                <i class="fas fa-clock fa-3x opacity-50"></i>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between bg-success bg-opacity-75 border-top-0">
                            <a href="controllers/order.php" class="text-white text-decoration-none">View Details</a>
                            <i class="fas fa-arrow-circle-right text-white"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-info text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Unread Messages</h6>
                                    <h2 class="mt-2 mb-0"><?php echo $unread_messages; ?></h2>
                                </div>
                                <i class="fas fa-envelope fa-3x opacity-50"></i>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between bg-info bg-opacity-75 border-top-0">
                            <a href="controllers/message.php" class="text-white text-decoration-none">View Details</a>
                            <i class="fas fa-arrow-circle-right text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Your Recent Orders</h5>
                    <a href="controllers/order.php" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($orders)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Product</th>
                                        <th>Farmer</th>
                                        <th>Total</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($orders, 0, 5) as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order['id']; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($order['product_image'])): ?>
                                                        <img src="public/uploads/<?php echo $order['product_image']; ?>" class="me-3" style="width: 40px; height: 40px; object-fit: cover;" alt="<?php echo $order['product_name']; ?>">
                                                    <?php else: ?>
                                                        <img src="images/default-product.jpg" class="me-3" style="width: 40px; height: 40px; object-fit: cover;" alt="<?php echo $order['product_name']; ?>">
                                                    <?php endif; ?>
                                                    <span><?php echo $order['product_name']; ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo $order['farmer_name']; ?></td>
                                            <td><?php echo format_price($order['price'] * $order['quantity']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($order['date_ordered'])); ?></td>
                                            <td>
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
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if (count($orders) > 5): ?>
                            <div class="text-center mt-3">
                                <a href="controllers/order.php" class="btn btn-outline-primary">View All Orders</a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                            <h5>No orders yet</h5>
                            <p class="text-muted">You haven't placed any orders yet. Browse the marketplace to find fresh products from local farmers.</p>
                            <a href="marketplace.php" class="btn btn-primary mt-2">Browse Marketplace</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Featured Products -->
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Featured Products</h5>
                    <a href="marketplace.php" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php
                    // Get some featured products
                    require_once 'models/Product.php';
                    $product_model = new Product($conn);
                    $featured_products = $product_model->get_all_products(4);
                    ?>
                    
                    <?php if (!empty($featured_products)): ?>
                        <div class="row">
                            <?php foreach ($featured_products as $product): ?>
                                <div class="col-md-3 mb-3">
                                    <div class="card h-100">
                                        <?php if (!empty($product['image'])): ?>
                                            <img src="public/uploads/<?php echo $product['image']; ?>" class="card-img-top" style="height: 150px; object-fit: cover;" alt="<?php echo $product['name']; ?>">
                                        <?php else: ?>
                                            <img src="images/<?php echo strtolower(str_replace(' ', '_', $product['category'])); ?>.jpg" class="card-img-top" style="height: 150px; object-fit: cover;" alt="<?php echo $product['name']; ?>" onerror="this.src='images/default-product.jpg'">
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <h6 class="card-title"><?php echo $product['name']; ?></h6>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold text-primary"><?php echo format_price($product['price']); ?></span>
                                                <?php if (!empty($product['category'])): ?>
                                                    <span class="badge badge-category"><?php echo $product['category']; ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white border-top-0">
                                            <a href="controllers/product.php?action=view&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary w-100">View</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <p class="text-muted">No featured products available at the moment.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
