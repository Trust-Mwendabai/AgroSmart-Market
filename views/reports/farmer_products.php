<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Product Sales Report - <?php echo get_period_name($period); ?></h5>
    </div>
    <div class="card-body">
        <?php if (empty($data)): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            No product sales data available for this period.
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th class="text-center">Quantity Sold</th>
                        <th class="text-center">Orders</th>
                        <th class="text-end">Revenue</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['category']); ?></td>
                        <td class="text-center"><?php echo number_format($product['total_quantity_sold']); ?></td>
                        <td class="text-center"><?php echo $product['orders_count']; ?></td>
                        <td class="text-end"><?php echo format_currency($product['total_revenue']); ?></td>
                        <td class="text-center">
                            <a href="product.php?action=view&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="2">Totals</th>
                        <th class="text-center">
                            <?php 
                                $total_quantity = array_sum(array_column($data, 'total_quantity_sold'));
                                echo number_format($total_quantity);
                            ?>
                        </th>
                        <th class="text-center">
                            <?php 
                                $total_orders = array_sum(array_column($data, 'orders_count'));
                                echo $total_orders; 
                            ?>
                        </th>
                        <th class="text-end">
                            <?php 
                                $total_revenue = array_sum(array_column($data, 'total_revenue'));
                                echo format_currency($total_revenue);
                            ?>
                        </th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Visualizations -->
<div class="row">
    <!-- Revenue by Product -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Revenue by Product</h5>
            </div>
            <div class="card-body">
                <?php if (empty($data)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No data available for visualization.
                </div>
                <?php else: ?>
                <canvas id="revenueChart" width="100%" height="300"></canvas>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Prepare data for chart
                    const productNames = [];
                    const productRevenues = [];
                    const backgroundColors = [
                        '#4CAF50', '#FFA726', '#42A5F5', '#9C27B0', '#F44336',
                        '#2196F3', '#FF9800', '#009688', '#673AB7', '#E91E63'
                    ];
                    
                    <?php 
                    $counter = 0;
                    // Sort by revenue in descending order
                    usort($data, function($a, $b) {
                        return $b['total_revenue'] - $a['total_revenue'];
                    });
                    
                    foreach ($data as $product): 
                        if ($counter < 10): // Limit to top 10
                    ?>
                    productNames.push('<?php echo addslashes($product['name']); ?>');
                    productRevenues.push(<?php echo $product['total_revenue']; ?>);
                    <?php 
                        endif;
                        $counter++;
                    endforeach; 
                    ?>
                    
                    // Create chart
                    const ctx = document.getElementById('revenueChart').getContext('2d');
                    const revenueChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: productNames,
                            datasets: [{
                                label: 'Revenue (ZMW)',
                                data: productRevenues,
                                backgroundColor: backgroundColors,
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Revenue (ZMW)'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
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
    
    <!-- Quantity Sold by Product -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Quantity Sold by Product</h5>
            </div>
            <div class="card-body">
                <?php if (empty($data)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No data available for visualization.
                </div>
                <?php else: ?>
                <canvas id="quantityChart" width="100%" height="300"></canvas>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Prepare data for chart
                    const productNames = [];
                    const productQuantities = [];
                    const backgroundColors = [
                        '#4CAF50', '#FFA726', '#42A5F5', '#9C27B0', '#F44336',
                        '#2196F3', '#FF9800', '#009688', '#673AB7', '#E91E63'
                    ];
                    
                    <?php 
                    $counter = 0;
                    // Sort by quantity in descending order
                    usort($data, function($a, $b) {
                        return $b['total_quantity_sold'] - $a['total_quantity_sold'];
                    });
                    
                    foreach ($data as $product): 
                        if ($counter < 10): // Limit to top 10
                    ?>
                    productNames.push('<?php echo addslashes($product['name']); ?>');
                    productQuantities.push(<?php echo $product['total_quantity_sold']; ?>);
                    <?php 
                        endif;
                        $counter++;
                    endforeach; 
                    ?>
                    
                    // Create chart
                    const ctx = document.getElementById('quantityChart').getContext('2d');
                    const quantityChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: productNames,
                            datasets: [{
                                label: 'Quantity Sold',
                                data: productQuantities,
                                backgroundColor: backgroundColors,
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Quantity Sold'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
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

<!-- Category Breakdown -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Category Breakdown</h5>
    </div>
    <div class="card-body">
        <?php if (empty($data)): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            No data available for category breakdown.
        </div>
        <?php else: ?>
        <?php
            // Organize data by category
            $categories = [];
            foreach ($data as $product) {
                $category = $product['category'] ?: 'Uncategorized';
                
                if (!isset($categories[$category])) {
                    $categories[$category] = [
                        'total_revenue' => 0,
                        'total_quantity' => 0,
                        'orders_count' => 0
                    ];
                }
                
                $categories[$category]['total_revenue'] += $product['total_revenue'];
                $categories[$category]['total_quantity'] += $product['total_quantity_sold'];
                $categories[$category]['orders_count'] += $product['orders_count'];
            }
            
            // Sort categories by revenue
            uasort($categories, function($a, $b) {
                return $b['total_revenue'] - $a['total_revenue'];
            });
        ?>
        
        <div class="row">
            <div class="col-md-8">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th class="text-center">Products</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Revenue</th>
                                <th class="text-center">% of Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach ($categories as $category => $stats): 
                                // Count products in this category
                                $products_count = 0;
                                foreach ($data as $product) {
                                    if (($product['category'] ?: 'Uncategorized') === $category) {
                                        $products_count++;
                                    }
                                }
                                
                                // Calculate percentage of total revenue
                                $percentage = ($stats['total_revenue'] / $total_revenue) * 100;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($category); ?></td>
                                <td class="text-center"><?php echo $products_count; ?></td>
                                <td class="text-center"><?php echo number_format($stats['total_quantity']); ?></td>
                                <td class="text-end"><?php echo format_currency($stats['total_revenue']); ?></td>
                                <td class="text-center"><?php echo number_format($percentage, 1); ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-4">
                <canvas id="categoryChart" width="100%" height="250"></canvas>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Prepare data for chart
                    const categoryNames = [];
                    const categoryRevenues = [];
                    const backgroundColors = [
                        '#4CAF50', '#FFA726', '#42A5F5', '#9C27B0', '#F44336',
                        '#2196F3', '#FF9800', '#009688', '#673AB7', '#E91E63'
                    ];
                    
                    <?php foreach ($categories as $category => $stats): ?>
                    categoryNames.push('<?php echo addslashes($category); ?>');
                    categoryRevenues.push(<?php echo $stats['total_revenue']; ?>);
                    <?php endforeach; ?>
                    
                    // Create chart
                    const ctx = document.getElementById('categoryChart').getContext('2d');
                    const categoryChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: categoryNames,
                            datasets: [{
                                data: categoryRevenues,
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
                                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            let percentage = Math.round((value / total) * 100);
                                            return label + ': ' + percentage + '% (ZMW ' + value.toFixed(2) + ')';
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
                </script>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Insights and Recommendations -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Insights & Recommendations</h5>
    </div>
    <div class="card-body">
        <?php if (empty($data)): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            No data available for insights.
        </div>
        <?php else: ?>
        <?php
            // Identify top performer
            usort($data, function($a, $b) {
                return $b['total_revenue'] - $a['total_revenue'];
            });
            $top_product = $data[0];
            
            // Identify low performers (products with sales but low revenue)
            $low_performers = [];
            foreach ($data as $product) {
                if ($product['total_revenue'] > 0 && $product['total_revenue'] < ($top_product['total_revenue'] * 0.1)) {
                    $low_performers[] = $product;
                }
            }
        ?>
        
        <div class="row">
            <div class="col-md-6">
                <h5><i class="fas fa-chart-line text-success me-2"></i>Top Performing Product</h5>
                <?php if (isset($top_product)): ?>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($top_product['name']); ?></h5>
                        <p class="card-text">
                            <strong>Category:</strong> <?php echo htmlspecialchars($top_product['category']); ?><br>
                            <strong>Revenue:</strong> <?php echo format_currency($top_product['total_revenue']); ?><br>
                            <strong>Quantity Sold:</strong> <?php echo number_format($top_product['total_quantity_sold']); ?><br>
                            <strong>Orders:</strong> <?php echo $top_product['orders_count']; ?>
                        </p>
                        <div class="alert alert-success mt-2">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Insight:</strong> This is your best-selling product! Consider increasing stock levels and promoting similar products.
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No top performing product identified.
                </div>
                <?php endif; ?>
            </div>
            
            <div class="col-md-6">
                <h5><i class="fas fa-lightbulb text-warning me-2"></i>Recommendations</h5>
                <ul class="list-group">
                    <?php if (count($data) > 0): ?>
                    <li class="list-group-item d-flex">
                        <i class="fas fa-chart-pie text-primary mt-1 me-3"></i>
                        <div>
                            <strong>Diversify Your Offerings</strong>
                            <p class="mb-0 small">Consider expanding your product range to include more categories and reduce dependency on top performers.</p>
                        </div>
                    </li>
                    <?php endif; ?>
                    
                    <?php if (isset($top_product)): ?>
                    <li class="list-group-item d-flex">
                        <i class="fas fa-tags text-success mt-1 me-3"></i>
                        <div>
                            <strong>Focus on <?php echo htmlspecialchars($top_product['category']); ?> Category</strong>
                            <p class="mb-0 small">Your top-performing product is in this category. Consider adding more products in this category.</p>
                        </div>
                    </li>
                    <?php endif; ?>
                    
                    <?php if (!empty($low_performers)): ?>
                    <li class="list-group-item d-flex">
                        <i class="fas fa-exclamation-triangle text-warning mt-1 me-3"></i>
                        <div>
                            <strong>Improve Low Performers</strong>
                            <p class="mb-0 small">Consider updating descriptions, improving images, or adjusting prices for:
                                <?php
                                $low_names = array_map(function($product) {
                                    return htmlspecialchars($product['name']);
                                }, array_slice($low_performers, 0, 2));
                                echo implode(', ', $low_names);
                                if (count($low_performers) > 2) {
                                    echo ' and ' . (count($low_performers) - 2) . ' others';
                                }
                                ?>
                            </p>
                        </div>
                    </li>
                    <?php endif; ?>
                    
                    <li class="list-group-item d-flex">
                        <i class="fas fa-bullhorn text-primary mt-1 me-3"></i>
                        <div>
                            <strong>Promote Your Products</strong>
                            <p class="mb-0 small">Use social media and local marketing to increase sales and attract more customers.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
