<!-- Orders Report View -->
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0"><?php echo $report_title; ?></h3>
            <p class="text-muted mb-0">Detailed overview of platform orders and transactions</p>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-sm btn-outline-dark me-2">
                <i class="fas fa-print me-1"></i>Print Report
            </button>
            <a href="../dashboard.php" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Orders Overview Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Orders</h5>
                    <h2 class="display-6 fw-bold text-purple mb-0"><?php echo isset($report_data['total_orders']) ? number_format($report_data['total_orders']) : 0; ?></h2>
                    <p class="text-muted">Completed transactions</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Active Orders</h5>
                    <h2 class="display-6 fw-bold text-info mb-0"><?php echo isset($report_data['active_orders']) ? number_format($report_data['active_orders']) : 0; ?></h2>
                    <p class="text-muted">Currently being processed</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Order Value</h5>
                    <h2 class="display-6 fw-bold text-success mb-0">K<?php echo isset($report_data['order_value']) ? number_format($report_data['order_value'], 2) : '0.00'; ?></h2>
                    <p class="text-muted">Total transaction value</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Avg. Order Value</h5>
                    <h2 class="display-6 fw-bold text-warning mb-0">K<?php echo isset($report_data['avg_order']) ? number_format($report_data['avg_order'], 2) : '0.00'; ?></h2>
                    <p class="text-muted">Average transaction size</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders by Status -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Orders by Status</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <canvas id="orderStatusChart" height="250"></canvas>
                        </div>
                        <div class="col-lg-8">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Status</th>
                                            <th>Count</th>
                                            <th>Value (K)</th>
                                            <th>%</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $statuses = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];
                                        $status_colors = [
                                            'pending' => 'warning',
                                            'confirmed' => 'primary',
                                            'shipped' => 'info',
                                            'delivered' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                        
                                        foreach ($statuses as $status) {
                                            $count = isset($report_data['orders_by_status'][$status]) ? $report_data['orders_by_status'][$status]['count'] : 0;
                                            $value = isset($report_data['orders_by_status'][$status]) ? $report_data['orders_by_status'][$status]['value'] : 0;
                                            $percentage = isset($report_data['total_orders']) && $report_data['total_orders'] > 0 ? 
                                                          round(($count / $report_data['total_orders']) * 100, 1) : 0;
                                            
                                            echo '<tr>';
                                            echo '<td><span class="badge bg-' . $status_colors[$status] . '">' . ucfirst($status) . '</span></td>';
                                            echo '<td>' . number_format($count) . '</td>';
                                            echo '<td>K' . number_format($value, 2) . '</td>';
                                            echo '<td>' . $percentage . '%</td>';
                                            echo '<td><a href="../orders.php?status=' . $status . '" class="btn btn-sm btn-outline-secondary">View</a></td>';
                                            echo '</tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Orders Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Orders</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Buyer</th>
                                    <th>Farmer</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($report_data['recent_orders'])): ?>
                                    <?php foreach ($report_data['recent_orders'] as $order): ?>
                                        <tr>
                                            <td><a href="../orders.php?id=<?php echo $order['id']; ?>">#<?php echo $order['id']; ?></a></td>
                                            <td><?php echo date('M d, Y', strtotime($order['date_ordered'])); ?></td>
                                            <td><?php echo htmlspecialchars($order['buyer_name']); ?></td>
                                            <td><?php echo htmlspecialchars($order['farmer_name']); ?></td>
                                            <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                                            <td><?php echo $order['quantity'] . ' ' . ($order['unit'] ?? 'units'); ?></td>
                                            <td>K<?php echo number_format($order['total_price'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    switch($order['status']) {
                                                        case 'pending': echo 'warning'; break;
                                                        case 'confirmed': echo 'primary'; break;
                                                        case 'shipped': echo 'info'; break;
                                                        case 'delivered': echo 'success'; break;
                                                        case 'cancelled': echo 'danger'; break;
                                                        default: echo 'secondary';
                                                    }
                                                ?>"><?php echo ucfirst($order['status']); ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-3">No recent orders found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for charts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Order Status Chart
    const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
    
    const statusData = {
        labels: ['Pending', 'Confirmed', 'Shipped', 'Delivered', 'Cancelled'],
        datasets: [{
            data: [
                <?php 
                echo (isset($report_data['orders_by_status']['pending']) ? $report_data['orders_by_status']['pending']['count'] : 0) . ', ';
                echo (isset($report_data['orders_by_status']['confirmed']) ? $report_data['orders_by_status']['confirmed']['count'] : 0) . ', ';
                echo (isset($report_data['orders_by_status']['shipped']) ? $report_data['orders_by_status']['shipped']['count'] : 0) . ', ';
                echo (isset($report_data['orders_by_status']['delivered']) ? $report_data['orders_by_status']['delivered']['count'] : 0) . ', ';
                echo (isset($report_data['orders_by_status']['cancelled']) ? $report_data['orders_by_status']['cancelled']['count'] : 0);
                ?>
            ],
            backgroundColor: [
                '#ffc107', // Pending - Yellow/Warning
                '#0d6efd', // Confirmed - Blue/Primary
                '#0dcaf0', // Shipped - Light Blue/Info
                '#198754', // Delivered - Green/Success
                '#dc3545'  // Cancelled - Red/Danger
            ],
            borderWidth: 1
        }]
    };
    
    new Chart(statusCtx, {
        type: 'doughnut',
        data: statusData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 15
                    }
                }
            }
        }
    });
});
</script>

<!-- Include admin footer -->
<?php include '../views/admin/partials/footer.php'; ?>
