<div class="row">
    <div class="col-12 mb-3">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Sales Summary - <?php echo get_period_name($period); ?></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="p-3 border rounded text-center">
                            <h3 class="text-primary mb-0"><?php echo format_currency($data['total_sales']); ?></h3>
                            <p class="text-muted">Total Sales</p>
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
                            <h3 class="text-primary mb-0"><?php echo $data['unique_customers']; ?></h3>
                            <p class="text-muted">Unique Customers</p>
                        </div>
                    </div>
                </div>

                <?php if ($data['total_orders'] > 0): ?>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Sales Summary:</strong> You've made <?php echo format_currency($data['total_sales']); ?> from <?php echo $data['total_orders']; ?> orders with an average order value of <?php echo format_currency($data['average_order_value']); ?> during this period.
                </div>
                <?php else: ?>
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>No sales data:</strong> There are no sales recorded for this period. Try selecting a different time period or check back after you've made some sales.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Get product sales data for pie chart
$product_model = new Product($conn);
// Fix: Ensure we access the user ID correctly whether $user is an object or array
$user_id = is_object($user) ? $user->get_id() : $user['id'];
$products = $product_model->get_farmer_products($user_id);
$sales_data = $report_model->get_farmer_product_sales($user_id, $period);
?>

<div class="row">
    <!-- Top Products Table -->
    <div class="col-md-7 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-medal me-2"></i>Top Products</h5>
            </div>
            <div class="card-body">
                <?php if (empty($sales_data)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No product sales data available for this period.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Quantity Sold</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $counter = 0;
                            foreach ($sales_data as $product): 
                                if ($counter++ < 5): // Show top 5
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category']); ?></td>
                                <td><?php echo number_format($product['total_quantity_sold']); ?></td>
                                <td><?php echo format_currency($product['total_revenue']); ?></td>
                            </tr>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-2 text-end">
                    <a href="?type=products&period=<?php echo $period; ?>" class="btn btn-sm btn-outline-primary">
                        View All Products <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Sales Distribution Chart -->
    <div class="col-md-5 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Sales Distribution</h5>
            </div>
            <div class="card-body">
                <?php if (empty($sales_data)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No product sales data available for this period.
                </div>
                <?php else: ?>
                <canvas id="salesDistributionChart" width="100%" height="250"></canvas>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Prepare data for chart
                    const productNames = [];
                    const productSales = [];
                    const backgroundColors = [
                        '#4CAF50', '#FFA726', '#42A5F5', '#9C27B0', '#F44336',
                        '#2196F3', '#FF9800', '#009688', '#673AB7', '#E91E63'
                    ];
                    
                    <?php 
                    $counter = 0;
                    foreach ($sales_data as $product): 
                        if ($counter < 5): // Limit to top 5
                    ?>
                    productNames.push('<?php echo addslashes($product['name']); ?>');
                    productSales.push(<?php echo $product['total_revenue']; ?>);
                    <?php 
                        endif;
                        $counter++;
                    endforeach; 
                    ?>
                    
                    // Create chart
                    const ctx = document.getElementById('salesDistributionChart').getContext('2d');
                    const salesDistributionChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: productNames,
                            datasets: [{
                                data: productSales,
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

<div class="row">
    <!-- Monthly Trend -->
    <div class="col-12 mb-3">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Monthly Sales Trend</h5>
            </div>
            <div class="card-body">
                <?php 
                // Fix: Ensure we access the user ID correctly whether $user is an object or array
                $user_id = is_object($user) ? $user->get_id() : $user['id'];
                $trend_data = $report_model->get_farmer_monthly_trend($user_id, 6);
                if (empty($trend_data)): 
                ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No trend data available for this period.
                </div>
                <?php else: ?>
                <canvas id="monthlyTrendChart" width="100%" height="300"></canvas>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Prepare data for chart
                    const months = [];
                    const sales = [];
                    const orders = [];
                    
                    <?php foreach ($trend_data as $item): ?>
                    months.push('<?php echo date("M Y", strtotime($item['month'] . "-01")); ?>');
                    sales.push(<?php echo $item['total_sales']; ?>);
                    orders.push(<?php echo $item['orders_count']; ?>);
                    <?php endforeach; ?>
                    
                    // Create chart
                    const ctx = document.getElementById('monthlyTrendChart').getContext('2d');
                    const monthlyTrendChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: months,
                            datasets: [
                                {
                                    label: 'Sales (ZMW)',
                                    data: sales,
                                    backgroundColor: 'rgba(76, 175, 80, 0.5)',
                                    borderColor: '#4CAF50',
                                    borderWidth: 1,
                                    yAxisID: 'y'
                                },
                                {
                                    label: 'Orders',
                                    data: orders,
                                    type: 'line',
                                    backgroundColor: 'rgba(33, 150, 243, 0.5)',
                                    borderColor: '#2196F3',
                                    borderWidth: 2,
                                    pointRadius: 4,
                                    fill: false,
                                    yAxisID: 'y1'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    position: 'left',
                                    title: {
                                        display: true,
                                        text: 'Sales (ZMW)'
                                    }
                                },
                                y1: {
                                    beginAtZero: true,
                                    position: 'right',
                                    title: {
                                        display: true,
                                        text: 'Orders'
                                    },
                                    grid: {
                                        drawOnChartArea: false
                                    }
                                }
                            }
                        }
                    });
                });
                </script>
                <div class="mt-3 text-end">
                    <a href="?type=trend&period=<?php echo $period; ?>" class="btn btn-sm btn-outline-primary">
                        View Detailed Trend <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
