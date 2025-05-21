<div class="row">
    <div class="col-12 mb-3">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Purchase Summary - <?php echo get_period_name($period); ?></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="p-3 border rounded text-center">
                            <h3 class="text-primary mb-0"><?php echo format_currency($data['total_spent']); ?></h3>
                            <p class="text-muted">Total Spent</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="p-3 border rounded text-center">
                            <h3 class="text-primary mb-0"><?php echo $data['total_orders']; ?></h3>
                            <p class="text-muted">Total Orders</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="p-3 border rounded text-center">
                            <h3 class="text-primary mb-0"><?php echo format_currency($data['average_order_value']); ?></h3>
                            <p class="text-muted">Average Order Value</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="p-3 border rounded text-center">
                            <h3 class="text-primary mb-0"><?php echo $data['unique_farmers']; ?></h3>
                            <p class="text-muted">Farmers Supported</p>
                        </div>
                    </div>
                </div>

                <?php if ($data['total_orders'] > 0): ?>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Purchase Summary:</strong> You've spent <?php echo format_currency($data['total_spent']); ?> on <?php echo $data['total_orders']; ?> orders with an average order value of <?php echo format_currency($data['average_order_value']); ?> during this period. You've supported <?php echo $data['unique_farmers']; ?> different farmers.
                </div>
                <?php else: ?>
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>No purchase data:</strong> There are no purchases recorded for this period. Try selecting a different time period or explore the marketplace to make purchases.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Get category data for pie chart
$category_data = $report_model->get_buyer_category_breakdown($user['id'], $period);
?>

<div class="row">
    <!-- Category Breakdown Table -->
    <div class="col-md-7 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Category Breakdown</h5>
            </div>
            <div class="card-body">
                <?php if (empty($category_data)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No category data available for this period.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="text-center">Orders</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Amount Spent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $counter = 0;
                            foreach ($category_data as $category): 
                                $category_name = !empty($category['category']) ? $category['category'] : 'Uncategorized';
                                if ($counter++ < 5): // Show top 5
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($category_name); ?></td>
                                <td class="text-center"><?php echo $category['orders_count']; ?></td>
                                <td class="text-center"><?php echo number_format($category['total_quantity']); ?></td>
                                <td class="text-end"><?php echo format_currency($category['total_spent']); ?></td>
                            </tr>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-2 text-end">
                    <a href="?type=categories&period=<?php echo $period; ?>" class="btn btn-sm btn-outline-primary">
                        View All Categories <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Purchase Distribution Chart -->
    <div class="col-md-5 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Purchase Distribution</h5>
            </div>
            <div class="card-body">
                <?php if (empty($category_data)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No category data available for this period.
                </div>
                <?php else: ?>
                <canvas id="purchaseDistributionChart" width="100%" height="250"></canvas>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Prepare data for chart
                    const categoryNames = [];
                    const categorySpending = [];
                    const backgroundColors = [
                        '#4CAF50', '#FFA726', '#42A5F5', '#9C27B0', '#F44336',
                        '#2196F3', '#FF9800', '#009688', '#673AB7', '#E91E63'
                    ];
                    
                    <?php 
                    $counter = 0;
                    foreach ($category_data as $category): 
                        $category_name = !empty($category['category']) ? $category['category'] : 'Uncategorized';
                        if ($counter < 5): // Limit to top 5
                    ?>
                    categoryNames.push('<?php echo addslashes($category_name); ?>');
                    categorySpending.push(<?php echo $category['total_spent']; ?>);
                    <?php 
                        endif;
                        $counter++;
                    endforeach; 
                    ?>
                    
                    // Create chart
                    const ctx = document.getElementById('purchaseDistributionChart').getContext('2d');
                    const purchaseDistributionChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: categoryNames,
                            datasets: [{
                                data: categorySpending,
                                backgroundColor: backgroundColors,
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 12
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.label || '';
                                            let value = context.raw || 0;
                                            return label + ': ZMW ' + value.toFixed(2);
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
                </script>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Recent Orders</h5>
    </div>
    <div class="card-body">
        <?php
        // Get recent orders
        $transactions = $report_model->get_transaction_details($user['id'], 'buyer', $period, 5, 0);
        
        if (empty($transactions)): 
        ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            No recent orders found for this period.
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Farmer</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $order): ?>
                    <tr>
                        <td><?php echo format_date($order['date_ordered']); ?></td>
                        <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['farmer_name']); ?></td>
                        <td class="text-center"><?php echo $order['quantity']; ?></td>
                        <td class="text-end"><?php echo format_currency($order['total_amount']); ?></td>
                        <td class="text-center">
                            <?php 
                            $status_class = '';
                            switch ($order['status']) {
                                case 'pending': $status_class = 'bg-warning'; break;
                                case 'confirmed': $status_class = 'bg-info'; break;
                                case 'shipped': $status_class = 'bg-primary'; break;
                                case 'delivered': $status_class = 'bg-success'; break;
                                case 'cancelled': $status_class = 'bg-danger'; break;
                                default: $status_class = 'bg-secondary';
                            }
                            ?>
                            <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($order['status']); ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-3 text-end">
            <a href="?type=transactions&period=<?php echo $period; ?>" class="btn btn-sm btn-outline-primary">
                View All Transactions <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Purchases Insights -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Purchase Insights</h5>
    </div>
    <div class="card-body">
        <?php if ($data['total_orders'] == 0): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            No purchase data available to generate insights.
        </div>
        <?php else: ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card border-light mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-leaf text-success me-2"></i>Your Impact</h5>
                    </div>
                    <div class="card-body">
                        <p>
                            By making <?php echo $data['total_orders']; ?> purchase(s), you've directly supported <?php echo $data['unique_farmers']; ?> local farmer(s) in Zambia.
                        </p>
                        
                        <div class="alert alert-success">
                            <i class="fas fa-thumbs-up me-2"></i>
                            <strong>Thank you!</strong> Your purchases help strengthen the local agricultural economy.
                        </div>
                        
                        <?php if ($data['unique_categories'] > 1): ?>
                        <p>
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Your diverse purchases across <?php echo $data['unique_categories']; ?> different categories help support a variety of farming practices.
                        </p>
                        <?php else: ?>
                        <p>
                            <i class="fas fa-info-circle text-info me-2"></i>
                            Consider exploring products from other categories to support diverse farming practices.
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card border-light mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-seedling text-primary me-2"></i>Recommendations</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php if ($data['total_orders'] > 0): ?>
                            <li class="list-group-item d-flex">
                                <i class="fas fa-calendar-alt text-primary mt-1 me-3"></i>
                                <div>
                                    <strong>Set a Regular Schedule</strong>
                                    <p class="mb-0 small">Consider setting up a regular purchase schedule for fresh products. This helps farmers plan their harvests better.</p>
                                </div>
                            </li>
                            <?php endif; ?>
                            
                            <li class="list-group-item d-flex">
                                <i class="fas fa-users text-success mt-1 me-3"></i>
                                <div>
                                    <strong>Build Relationships</strong>
                                    <p class="mb-0 small">Building long-term relationships with farmers can lead to better prices and priority access to premium products.</p>
                                </div>
                            </li>
                            
                            <li class="list-group-item d-flex">
                                <i class="fas fa-star text-warning mt-1 me-3"></i>
                                <div>
                                    <strong>Leave Reviews</strong>
                                    <p class="mb-0 small">Your reviews help farmers improve their products and help other buyers make informed decisions.</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
