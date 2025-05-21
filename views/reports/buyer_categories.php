<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Purchase Categories Report - <?php echo get_period_name($period); ?></h5>
    </div>
    <div class="card-body">
        <?php if (empty($data)): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            No category data available for this period.
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Category</th>
                        <th class="text-center">Orders</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-end">Amount Spent</th>
                        <th class="text-center">% of Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Calculate total spent across all categories
                    $total_spent = array_sum(array_column($data, 'total_spent'));
                    
                    foreach ($data as $category): 
                        $category_name = !empty($category['category']) ? $category['category'] : 'Uncategorized';
                        $percentage = ($category['total_spent'] / $total_spent) * 100;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($category_name); ?></td>
                        <td class="text-center"><?php echo $category['orders_count']; ?></td>
                        <td class="text-center"><?php echo number_format($category['total_quantity']); ?></td>
                        <td class="text-end"><?php echo format_currency($category['total_spent']); ?></td>
                        <td class="text-center"><?php echo number_format($percentage, 1); ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th>Total</th>
                        <th class="text-center">
                            <?php 
                            $total_orders = array_sum(array_column($data, 'orders_count'));
                            echo $total_orders;
                            ?>
                        </th>
                        <th class="text-center">
                            <?php 
                            $total_quantity = array_sum(array_column($data, 'total_quantity'));
                            echo number_format($total_quantity);
                            ?>
                        </th>
                        <th class="text-end"><?php echo format_currency($total_spent); ?></th>
                        <th class="text-center">100%</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Visualizations -->
<div class="row">
    <!-- Category Spending Breakdown -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Category Spending Breakdown</h5>
            </div>
            <div class="card-body">
                <?php if (empty($data)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No data available for visualization.
                </div>
                <?php else: ?>
                <canvas id="categoryPieChart" width="100%" height="300"></canvas>
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
                    foreach ($data as $category): 
                        $category_name = !empty($category['category']) ? $category['category'] : 'Uncategorized';
                    ?>
                    categoryNames.push('<?php echo addslashes($category_name); ?>');
                    categorySpending.push(<?php echo $category['total_spent']; ?>);
                    <?php endforeach; ?>
                    
                    // Create chart
                    const ctx = document.getElementById('categoryPieChart').getContext('2d');
                    const categoryPieChart = new Chart(ctx, {
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
                                    position: 'right',
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
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Orders by Category -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Orders by Category</h5>
            </div>
            <div class="card-body">
                <?php if (empty($data)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No data available for visualization.
                </div>
                <?php else: ?>
                <canvas id="categoryBarChart" width="100%" height="300"></canvas>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Prepare data for chart
                    const categoryNames = [];
                    const categoryOrders = [];
                    const backgroundColors = [
                        'rgba(76, 175, 80, 0.7)',
                        'rgba(255, 167, 38, 0.7)',
                        'rgba(66, 165, 245, 0.7)',
                        'rgba(156, 39, 176, 0.7)',
                        'rgba(244, 67, 54, 0.7)',
                        'rgba(33, 150, 243, 0.7)',
                        'rgba(255, 152, 0, 0.7)',
                        'rgba(0, 150, 136, 0.7)',
                        'rgba(103, 58, 183, 0.7)',
                        'rgba(233, 30, 99, 0.7)'
                    ];
                    
                    <?php 
                    // Sort by orders count
                    usort($data, function($a, $b) {
                        return $b['orders_count'] - $a['orders_count'];
                    });
                    
                    foreach ($data as $category): 
                        $category_name = !empty($category['category']) ? $category['category'] : 'Uncategorized';
                    ?>
                    categoryNames.push('<?php echo addslashes($category_name); ?>');
                    categoryOrders.push(<?php echo $category['orders_count']; ?>);
                    <?php endforeach; ?>
                    
                    // Create chart
                    const ctx = document.getElementById('categoryBarChart').getContext('2d');
                    const categoryBarChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: categoryNames,
                            datasets: [{
                                label: 'Number of Orders',
                                data: categoryOrders,
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
                                        text: 'Orders'
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

<!-- Spending Trends by Category -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Spending Behavior Analysis</h5>
    </div>
    <div class="card-body">
        <?php if (empty($data)): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            No data available for analysis.
        </div>
        <?php else: ?>
        <?php
            // Sort categories by spending
            usort($data, function($a, $b) {
                return $b['total_spent'] - $a['total_spent'];
            });
            
            // Get top category
            $top_category = $data[0];
            $top_category_name = !empty($top_category['category']) ? $top_category['category'] : 'Uncategorized';
            
            // Calculate average spending per order for each category
            foreach ($data as &$category) {
                $category['avg_per_order'] = $category['orders_count'] > 0 ? 
                    $category['total_spent'] / $category['orders_count'] : 0;
            }
            
            // Sort by average per order
            usort($data, function($a, $b) {
                return $b['avg_per_order'] - $a['avg_per_order'];
            });
            
            // Get highest average category
            $highest_avg_category = $data[0];
            $highest_avg_name = !empty($highest_avg_category['category']) ? 
                $highest_avg_category['category'] : 'Uncategorized';
        ?>
        
        <div class="row">
            <div class="col-md-6">
                <h5><i class="fas fa-star text-warning me-2"></i>Top Spending Categories</h5>
                <p>
                    You spend the most on <strong><?php echo htmlspecialchars($top_category_name); ?></strong> products, 
                    with a total of <strong><?php echo format_currency($top_category['total_spent']); ?></strong> 
                    across <strong><?php echo $top_category['orders_count']; ?></strong> orders.
                </p>
                
                <p>
                    Your highest average spending per order is in the <strong><?php echo htmlspecialchars($highest_avg_name); ?></strong> category, 
                    with an average of <strong><?php echo format_currency($highest_avg_category['avg_per_order']); ?></strong> per order.
                </p>
                
                <div class="alert alert-primary mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Did you know?</strong> Understanding your spending patterns can help you budget better and make more informed purchases.
                </div>
            </div>
            
            <div class="col-md-6">
                <h5><i class="fas fa-lightbulb text-warning me-2"></i>Recommendations</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex">
                        <i class="fas fa-balance-scale text-primary mt-1 me-3"></i>
                        <div>
                            <strong>Diversify Your Purchases</strong>
                            <p class="mb-0 small">Consider exploring products from different categories to ensure a balanced diet and support various farming practices.</p>
                        </div>
                    </li>
                    
                    <li class="list-group-item d-flex">
                        <i class="fas fa-calendar-alt text-success mt-1 me-3"></i>
                        <div>
                            <strong>Seasonal Shopping</strong>
                            <p class="mb-0 small">Buy products when they're in season to get better prices and fresher products.</p>
                        </div>
                    </li>
                    
                    <li class="list-group-item d-flex">
                        <i class="fas fa-hand-holding-usd text-warning mt-1 me-3"></i>
                        <div>
                            <strong>Budget Planning</strong>
                            <p class="mb-0 small">Use this category breakdown to plan your monthly food budget more effectively.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        
        <hr>
        
        <div class="row mt-4">
            <div class="col-12">
                <h5><i class="fas fa-chart-bar text-primary me-2"></i>Average Spending per Order by Category</h5>
                <canvas id="avgSpendingChart" width="100%" height="200"></canvas>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Prepare data for chart
                    const categoryNames = [];
                    const avgSpending = [];
                    const backgroundColors = [
                        'rgba(76, 175, 80, 0.7)',
                        'rgba(255, 167, 38, 0.7)',
                        'rgba(66, 165, 245, 0.7)',
                        'rgba(156, 39, 176, 0.7)',
                        'rgba(244, 67, 54, 0.7)',
                        'rgba(33, 150, 243, 0.7)',
                        'rgba(255, 152, 0, 0.7)',
                        'rgba(0, 150, 136, 0.7)',
                        'rgba(103, 58, 183, 0.7)',
                        'rgba(233, 30, 99, 0.7)'
                    ];
                    
                    <?php 
                    foreach ($data as $category): 
                        $category_name = !empty($category['category']) ? $category['category'] : 'Uncategorized';
                    ?>
                    categoryNames.push('<?php echo addslashes($category_name); ?>');
                    avgSpending.push(<?php echo $category['avg_per_order']; ?>);
                    <?php endforeach; ?>
                    
                    // Create chart
                    const ctx = document.getElementById('avgSpendingChart').getContext('2d');
                    const avgSpendingChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: categoryNames,
                            datasets: [{
                                label: 'Average Spending per Order (ZMW)',
                                data: avgSpending,
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
                                        text: 'Amount (ZMW)'
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

<!-- Seasonal Products Recommendations -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Seasonal Recommendations</h5>
    </div>
    <div class="card-body">
        <?php
        // Get current month
        $current_month = date('n');
        $season = '';
        $seasonal_products = [];
        
        // Define Zambia seasons and seasonal products
        if ($current_month >= 11 || $current_month <= 3) {
            // Rainy season (November to March)
            $season = 'Rainy Season';
            $seasonal_products = [
                'Maize' => 'Fresh maize is abundant during this period',
                'Tomatoes' => 'Local tomatoes are plentiful and affordable',
                'Green Vegetables' => 'Leafy greens like rape, cabbage, and spinach grow well in this season',
                'Sweet Potatoes' => 'Perfect time for sweet potatoes',
                'Pumpkins' => 'Pumpkins and pumpkin leaves are in season'
            ];
        } elseif ($current_month >= 4 && $current_month <= 7) {
            // Cool dry season (April to July)
            $season = 'Cool Dry Season';
            $seasonal_products = [
                'Beans' => 'Dry beans are harvested during this period',
                'Groundnuts' => 'Perfect time for groundnuts',
                'Irish Potatoes' => 'Quality potatoes are available',
                'Onions' => 'Local onions are harvested',
                'Carrots' => 'Carrots are in good supply'
            ];
        } else {
            // Hot dry season (August to October)
            $season = 'Hot Dry Season';
            $seasonal_products = [
                'Mangoes' => 'Mango season starts late in this period',
                'Watermelons' => 'Available and refreshing in the hot weather',
                'Pineapples' => 'Sweet pineapples are in season',
                'Dried Vegetables' => 'Preserved vegetables from previous seasons',
                'Cassava' => 'Staple that stores well during dry season'
            ];
        }
        ?>
        
        <div class="alert alert-success">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-seedling fa-2x"></i>
                </div>
                <div>
                    <h5 class="alert-heading mb-1">Current Season: <?php echo $season; ?></h5>
                    <p class="mb-0">Here are some seasonal products to consider in your next purchase:</p>
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <?php foreach ($seasonal_products as $product => $description): ?>
            <div class="col-md-4 mb-3">
                <div class="card h-100 border-success">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($product); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($description); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="alert alert-info mt-3">
            <i class="fas fa-lightbulb me-2"></i>
            <strong>Buying tip:</strong> Seasonal products are typically fresher, more nutritious, and better priced. Consider stocking up on these items during their peak season.
        </div>
    </div>
</div>
