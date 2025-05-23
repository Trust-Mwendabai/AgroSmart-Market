<!-- Dashboard Overview Title -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Dashboard Overview</h4>
        <p class="text-muted mb-0">Welcome to your AgroSmart Market admin panel</p>
    </div>
    <div>
        <span class="badge bg-primary p-2">
            <i class="far fa-calendar-alt me-1"></i> <?php echo date('F j, Y'); ?>
        </span>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card stat-card h-100 border-0 shadow-sm" style="border-left: 4px solid #4CAF50 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stat-label text-uppercase fw-bold mb-1" style="color: #666;">Farmers</h6>
                        <h2 class="stat-value mb-0"><?php echo number_format($stats['total_farmers']); ?></h2>
                        <div class="stat-change mt-2">
                            <span class="badge bg-success bg-opacity-10 text-success">Active Producers</span>
                        </div>
                    </div>
                    <div class="stat-icon-container rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: rgba(76, 175, 80, 0.1);">
                        <i class="fas fa-tractor fa-2x" style="color: #4CAF50;"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="users.php?type=farmer" class="text-decoration-none d-flex justify-content-between align-items-center" style="color: #4CAF50;">
                    <span>View Details</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card stat-card h-100 border-0 shadow-sm" style="border-left: 4px solid #2196F3 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stat-label text-uppercase fw-bold mb-1" style="color: #666;">Buyers</h6>
                        <h2 class="stat-value mb-0"><?php echo number_format($stats['total_buyers']); ?></h2>
                        <div class="stat-change mt-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary">Registered Customers</span>
                        </div>
                    </div>
                    <div class="stat-icon-container rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: rgba(33, 150, 243, 0.1);">
                        <i class="fas fa-users fa-2x" style="color: #2196F3;"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="users.php?type=buyer" class="text-decoration-none d-flex justify-content-between align-items-center" style="color: #2196F3;">
                    <span>View Details</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card stat-card h-100 border-0 shadow-sm" style="border-left: 4px solid #FFC107 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stat-label text-uppercase fw-bold mb-1" style="color: #666;">Products</h6>
                        <h2 class="stat-value mb-0"><?php echo number_format($stats['total_products']); ?></h2>
                        <div class="stat-change mt-2">
                            <span class="badge bg-warning bg-opacity-10 text-warning">Available Items</span>
                        </div>
                    </div>
                    <div class="stat-icon-container rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: rgba(255, 193, 7, 0.1);">
                        <i class="fas fa-box-open fa-2x" style="color: #FFC107;"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="products.php" class="text-decoration-none d-flex justify-content-between align-items-center" style="color: #FFC107;">
                    <span>View Details</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card stat-card h-100 border-0 shadow-sm" style="border-left: 4px solid #9C27B0 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stat-label text-uppercase fw-bold mb-1" style="color: #666;">Orders</h6>
                        <h2 class="stat-value mb-0"><?php echo number_format($stats['total_orders']); ?></h2>
                        <div class="stat-change mt-2">
                            <span class="badge bg-purple bg-opacity-10 text-purple" style="background-color: rgba(156, 39, 176, 0.1) !important; color: #9C27B0 !important;">Total Transactions</span>
                        </div>
                    </div>
                    <div class="stat-icon-container rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: rgba(156, 39, 176, 0.1);">
                        <i class="fas fa-shopping-cart fa-2x" style="color: #9C27B0;"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="orders.php" class="text-decoration-none d-flex justify-content-between align-items-center" style="color: #9C27B0;">
                    <span>View Details</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Revenue Card -->
    <div class="col-md-3 mb-4">
        <div class="card stat-card h-100 border-0 shadow-sm" style="border-left: 4px solid #F44336 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stat-label text-uppercase fw-bold mb-1" style="color: #666;">Revenue</h6>
                        <h2 class="stat-value mb-0">K<?php echo number_format($stats['total_revenue'], 2); ?></h2>
                        <div class="stat-change mt-2">
                            <span class="badge bg-danger bg-opacity-10 text-danger">Total Platform Income</span>
                        </div>
                    </div>
                    <div class="stat-icon-container rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: rgba(244, 67, 54, 0.1);">
                        <i class="fas fa-money-bill-wave fa-2x" style="color: #F44336;"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="#revenue-section" class="text-decoration-none d-flex justify-content-between align-items-center" style="color: #F44336;">
                    <span>View Breakdown</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <!-- Order Status Chart -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-header bg-transparent border-bottom-0 d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold"><i class="fas fa-chart-pie text-primary me-2"></i>Orders by Status</h5>
            </div>
            <div class="card-body" style="height: 340px;">
                <div class="chart-container" style="position: relative; height: 100%; width: 100%;">
                    <canvas id="orderStatusChart"></canvas>
                </div>
                
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Get the canvas element
                        var ctx = document.getElementById('orderStatusChart').getContext('2d');
                        
                        // Define the data for the chart
                        var statuses = [
                            <?php 
                            $statuses = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];
                            $labels = array_map(function($status) {
                                return "'".ucfirst($status)."'";
                            }, $statuses);
                            echo implode(',', $labels);
                            ?>
                        ];
                        
                        var counts = [
                            <?php 
                            $counts = [];
                            foreach ($statuses as $status) {
                                $counts[] = isset($stats['orders_by_status'][$status]) ? $stats['orders_by_status'][$status] : 0;
                            }
                            echo implode(',', $counts);
                            ?>
                        ];
                        
                        var data = {
                            labels: statuses,
                            datasets: [{
                                data: counts,
                                backgroundColor: [
                                    '#ffc107', // Pending - Yellow
                                    '#4CAF50', // Confirmed - Green
                                    '#2196F3', // Shipped - Blue
                                    '#9C27B0', // Delivered - Purple
                                    '#f44336'  // Cancelled - Red
                                ],
                                hoverBackgroundColor: [
                                    '#e0a800',
                                    '#3d8b40',
                                    '#0c7cd5',
                                    '#7b1fa2',
                                    '#d32f2f'
                                ],
                                borderWidth: 2
                            }]
                        };
                        
                        // Check if there's any data
                        var hasData = counts.some(function(count) { return count > 0; });
                        
                        // Create the pie chart
                        var myPieChart = new Chart(ctx, {
                            type: 'doughnut',
                            data: data,
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '65%',
                                plugins: {
                                    legend: {
                                        position: 'right',
                                        labels: {
                                            padding: 20,
                                            usePointStyle: true,
                                            font: {
                                                size: 12
                                            }
                                        }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                var label = context.label || '';
                                                var value = context.raw || 0;
                                                var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                var percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                                return label + ': ' + value + ' (' + percentage + '%)';
                                            }
                                        }
                                    }
                                }
                            }
                        });
                        
                        // If no data, show a message
                        if (!hasData) {
                            var chartArea = document.querySelector('.chart-container');
                            var noDataMsg = document.createElement('div');
                            noDataMsg.className = 'text-center py-5';
                            noDataMsg.innerHTML = '<p class="text-muted"><i class="fas fa-info-circle me-2"></i>No order data available yet.</p>';
                            chartArea.appendChild(noDataMsg);
                        }
                    });
                </script>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity Timeline -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Recent Orders</h6>
                <a href="orders.php" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <?php if (empty($stats['recent_orders'])): ?>
                    <div class="text-center p-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p>No orders found in the system.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($stats['recent_orders'] as $order): ?>
                            <a href="orders.php?id=<?php echo $order['id']; ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Order #<?php echo $order['id']; ?></h6>
                                    <small class="text-muted">
                                        <?php 
                                        // Format date to a more readable format
                                        $date = new DateTime($order['date_ordered']);
                                        echo $date->format('M d, Y'); 
                                        ?>
                                    </small>
                                </div>
                                <p class="mb-1">
                                    <?php echo $order['quantity']; ?> × <?php echo htmlspecialchars($order['product_name']); ?>
                                </p>
                                <small class="text-muted">
                                    Buyer: <?php echo htmlspecialchars($order['buyer_name']); ?> | 
                                    Seller: <?php echo htmlspecialchars($order['farmer_name']); ?> | 
                                    Status: <span class="badge 
                                        <?php 
                                        switch($order['status']) {
                                            case 'pending': echo 'bg-warning'; break;
                                            case 'confirmed': echo 'bg-primary'; break;
                                            case 'shipped': echo 'bg-info'; break;
                                            case 'delivered': echo 'bg-success'; break;
                                            case 'cancelled': echo 'bg-danger'; break;
                                            default: echo 'bg-secondary';
                                        }
                                        ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Row -->
<div class="row">
    <!-- Recent Products -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-header bg-transparent border-bottom-0 d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold"><i class="fas fa-box-open text-success me-2"></i>Recent Products</h5>
                <a href="products.php" class="btn btn-sm btn-outline-success rounded-pill px-3">
                    <i class="fas fa-external-link-alt me-1"></i>View All
                </a>
            </div>
            <div class="card-body p-0 pt-2">
                <?php if (empty($stats['recent_products'])): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No products found in the system.</p>
                        <a href="products.php?action=add" class="btn btn-sm btn-light mt-2">
                            <i class="fas fa-plus me-1"></i>Add Product
                        </a>
                    </div>
                <?php else: ?>
                    <div class="px-4">
                        <?php foreach ($stats['recent_products'] as $product): ?>
                            <div class="d-flex align-items-center py-3 border-bottom">
                                <div class="flex-shrink-0">
                                    <div class="product-icon-container rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: rgba(76, 175, 80, 0.1);">
                                        <?php if (!empty($product['image'])): ?>
                                            <img src="../uploads/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid rounded" style="max-width: 45px; max-height: 45px;">
                                        <?php else: ?>
                                            <i class="fas fa-seedling fa-2x" style="color: #4CAF50;"></i>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($product['name']); ?></h6>
                                        <span class="badge bg-success rounded-pill">
                                            ZMW <?php echo number_format($product['price'], 2); ?>
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <p class="mb-1 small text-muted">
                                            <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($product['farmer_name']); ?>
                                        </p>
                                        <span class="badge <?php echo $product['stock'] > 10 ? 'bg-success' : ($product['stock'] > 0 ? 'bg-warning' : 'bg-danger'); ?> text-white">
                                            <?php echo $product['stock'] > 0 ? $product['stock'] . ' in stock' : 'Out of stock'; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 ms-2">
                                    <a href="products.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-light rounded-circle">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Row -->
<div class="row">
    <!-- Recent Users -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-header bg-transparent border-bottom-0 d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold"><i class="fas fa-users text-primary me-2"></i>New Users</h5>
                <a href="users.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    <i class="fas fa-external-link-alt me-1"></i>View All
                </a>
            </div>
            <div class="card-body p-0 pt-2">
                <?php if (empty($stats['recent_users'])): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No users found in the system.</p>
                        <a href="users.php?action=add" class="btn btn-sm btn-light mt-2">
                            <i class="fas fa-user-plus me-1"></i>Add User
                        </a>
                    </div>
                <?php else: ?>
                    <div class="px-4">
                        <?php foreach ($stats['recent_users'] as $user): ?>
                            <div class="d-flex align-items-center py-3 border-bottom">
                                <div class="flex-shrink-0">
                                    <?php 
                                        $bg_color = $user['user_type'] === 'farmer' ? 'rgba(76, 175, 80, 0.1)' : 'rgba(33, 150, 243, 0.1)';
                                        $icon_color = $user['user_type'] === 'farmer' ? '#4CAF50' : '#2196F3';
                                        $icon = $user['user_type'] === 'farmer' ? 'tractor' : 'shopping-bag';
                                    ?>
                                    <div class="user-icon-container rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: <?php echo $bg_color; ?>">
                                        <?php if (!empty($user['profile_image'])): ?>
                                            <img src="../uploads/profile/<?php echo htmlspecialchars($user['profile_image']); ?>" alt="<?php echo htmlspecialchars($user['name']); ?>" class="img-fluid rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
                                        <?php else: ?>
                                            <i class="fas fa-<?php echo $icon; ?> fa-lg" style="color: <?php echo $icon_color; ?>"></i>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($user['name']); ?></h6>
                                        <span class="badge <?php echo $user['user_type'] === 'farmer' ? 'bg-success' : 'bg-primary'; ?> rounded-pill">
                                            <?php echo ucfirst($user['user_type']); ?>
                                        </span>
                                    </div>
                                    <p class="mb-1 small text-muted">
                                        <i class="fas fa-envelope me-1"></i> <?php echo htmlspecialchars($user['email']); ?>
                                    </p>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-light text-dark">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            <?php 
                                                $date = new DateTime($user['date_registered']);
                                                echo $date->format('M d, Y'); 
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 ms-2">
                                    <a href="users.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-light rounded-circle">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-header bg-transparent border-bottom-0">
                <h5 class="m-0 fw-bold"><i class="fas fa-bolt text-warning me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6 col-lg-6">
                        <div class="quick-action-card border rounded p-3 h-100 d-flex flex-column" style="background-color: rgba(76, 175, 80, 0.1); transition: all 0.3s ease;">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: #4CAF50;">
                                    <i class="fas fa-user-plus text-white"></i>
                                </div>
                                <h6 class="ms-3 mb-0 fw-bold">Add User</h6>
                            </div>
                            <p class="text-muted small mb-3">Register a new farmer or buyer in the system</p>
                            <a href="users.php?action=add" class="btn btn-sm btn-outline-success mt-auto">
                                <i class="fas fa-arrow-right me-1"></i>Create Now
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6">
                        <div class="quick-action-card border rounded p-3 h-100 d-flex flex-column" style="background-color: rgba(33, 150, 243, 0.1); transition: all 0.3s ease;">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: #2196F3;">
                                    <i class="fas fa-box-open text-white"></i>
                                </div>
                                <h6 class="ms-3 mb-0 fw-bold">Add Product</h6>
                            </div>
                            <p class="text-muted small mb-3">Create a new product listing in the marketplace</p>
                            <a href="products.php?action=add" class="btn btn-sm btn-outline-primary mt-auto">
                                <i class="fas fa-arrow-right me-1"></i>Create Now
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6">
                        <div class="quick-action-card border rounded p-3 h-100 d-flex flex-column" style="background-color: rgba(255, 193, 7, 0.1); transition: all 0.3s ease;">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: #FFC107;">
                                    <i class="fas fa-cart-plus text-white"></i>
                                </div>
                                <h6 class="ms-3 mb-0 fw-bold">Create Order</h6>
                            </div>
                            <p class="text-muted small mb-3">Create and manage new customer orders</p>
                            <a href="orders.php?action=add" class="btn btn-sm btn-outline-warning mt-auto">
                                <i class="fas fa-arrow-right me-1"></i>Create Now
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6">
                        <div class="quick-action-card border rounded p-3 h-100 d-flex flex-column" style="background-color: rgba(156, 39, 176, 0.1); transition: all 0.3s ease;">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: #9C27B0;">
                                    <i class="fas fa-chart-bar text-white"></i>
                                </div>
                                <h6 class="ms-3 mb-0 fw-bold">Reports</h6>
                            </div>
                            <p class="text-muted small mb-3">View system reports and revenue analytics</p>
                            <div class="d-flex mt-auto">
                                <a href="revenue_dashboard.php" class="btn btn-sm btn-outline-danger me-2">
                                    <i class="fas fa-chart-line me-1"></i>Revenue
                                </a>
                                <a href="reports.php" class="btn btn-sm btn-outline-purple" style="color: #9C27B0; border-color: #9C27B0;">
                                    <i class="fas fa-file-alt me-1"></i>All Reports
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
