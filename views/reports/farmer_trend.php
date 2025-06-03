<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Monthly Sales Trend</h5>
    </div>
    <div class="card-body">
        <?php if (empty($data)): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            No trend data available for this period.
        </div>
        <?php else: ?>
        <!-- Month selector -->
        <div class="mb-4">
            <form action="" method="get" class="row g-3 align-items-end">
                <input type="hidden" name="type" value="trend">
                <input type="hidden" name="period" value="<?php echo $period; ?>">
                
                <div class="col-md-3">
                    <label for="monthsSelect" class="form-label">Display Months</label>
                    <select id="monthsSelect" name="months" class="form-select" onchange="this.form.submit()">
                        <option value="3" <?php echo (isset($_GET['months']) && $_GET['months'] == 3) ? 'selected' : ''; ?>>Last 3 Months</option>
                        <option value="6" <?php echo (!isset($_GET['months']) || $_GET['months'] == 6) ? 'selected' : ''; ?>>Last 6 Months</option>
                        <option value="12" <?php echo (isset($_GET['months']) && $_GET['months'] == 12) ? 'selected' : ''; ?>>Last 12 Months</option>
                        <option value="24" <?php echo (isset($_GET['months']) && $_GET['months'] == 24) ? 'selected' : ''; ?>>Last 24 Months</option>
                    </select>
                </div>
            </form>
        </div>
        
        <!-- Main Chart -->
        <div class="chart-container" style="position: relative; height:400px; overflow-x:auto;">
            <canvas id="salesTrendChart" style="min-width:600px;"></canvas>
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Prepare data for chart
            const months = [];
            const salesData = [];
            const ordersData = [];
            
            <?php foreach ($data as $item): ?>
            months.push('<?php echo date("M Y", strtotime($item['month'] . "-01")); ?>');
            salesData.push(<?php echo $item['total_sales'] ?? 0; ?>);
            ordersData.push(<?php echo $item['orders_count'] ?? 0; ?>);
            <?php endforeach; ?>
            
            // Create chart
            const ctx = document.getElementById('salesTrendChart').getContext('2d');
            const salesTrendChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Sales (ZMW)',
                            data: salesData,
                            backgroundColor: 'rgba(76, 175, 80, 0.5)',
                            borderColor: '#4CAF50',
                            borderWidth: 1,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Orders',
                            data: ordersData,
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
                    maintainAspectRatio: false,
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
        <?php endif; ?>
    </div>
</div>

<!-- Trend Data Table -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-table me-2"></i>Monthly Sales Data</h5>
    </div>
    <div class="card-body">
        <?php if (empty($data)): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            No trend data available for this period.
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Month</th>
                        <th class="text-center">Orders</th>
                        <th class="text-end">Sales (ZMW)</th>
                        <th class="text-end">Avg. Order Value</th>
                        <th class="text-center">Change from Previous</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_orders = 0;
                    $total_sales = 0;
                    $prev_sales = null;
                    
                    // Reverse array to show most recent first
                    $reversed_data = array_reverse($data);
                    
                    foreach ($reversed_data as $index => $item): 
                        $total_orders += $item['orders_count'];
                        $total_sales += $item['total_sales'];
                        
                        // Calculate average order value
                        $avg_order = ($item['orders_count'] > 0) ? ($item['total_sales'] / $item['orders_count']) : 0;
                        
                        // Calculate change from previous month
                        $change_percent = 0;
                        $change_class = '';
                        $change_icon = '';
                        
                        if ($prev_sales !== null && $prev_sales > 0) {
                            $change_percent = (($item['total_sales'] - $prev_sales) / $prev_sales) * 100;
                            
                            if ($change_percent > 0) {
                                $change_class = 'text-success';
                                $change_icon = '<i class="fas fa-arrow-up me-1"></i>';
                            } elseif ($change_percent < 0) {
                                $change_class = 'text-danger';
                                $change_icon = '<i class="fas fa-arrow-down me-1"></i>';
                            } else {
                                $change_class = 'text-muted';
                                $change_icon = '<i class="fas fa-minus me-1"></i>';
                            }
                        }
                        
                        $prev_sales = $item['total_sales'];
                    ?>
                    <tr>
                        <td><?php echo date("F Y", strtotime($item['month'] . "-01")); ?></td>
                        <td class="text-center"><?php echo $item['orders_count']; ?></td>
                        <td class="text-end"><?php echo format_currency($item['total_sales']); ?></td>
                        <td class="text-end"><?php echo format_currency($avg_order); ?></td>
                        <td class="text-center <?php echo $change_class; ?>">
                            <?php 
                            if ($index < count($reversed_data) - 1) {
                                echo $change_icon . abs(round($change_percent, 1)) . '%';
                            } else {
                                echo '<span class="text-muted">N/A</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th>Total</th>
                        <th class="text-center"><?php echo $total_orders; ?></th>
                        <th class="text-end"><?php echo format_currency($total_sales); ?></th>
                        <th class="text-end">
                            <?php 
                            $overall_avg = ($total_orders > 0) ? ($total_sales / $total_orders) : 0;
                            echo format_currency($overall_avg);
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

<!-- Trend Analysis -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Trend Analysis</h5>
    </div>
    <div class="card-body">
        <?php if (empty($data) || count($data) < 2): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Not enough data available for trend analysis. At least two months of data are required.
        </div>
        <?php else: ?>
        <?php
            // Calculate trend data
            $growth_rate = 0;
            $is_growing = false;
            $first_month = reset($data);
            $last_month = end($data);
            
            if ($first_month['total_sales'] > 0) {
                $growth_rate = (($last_month['total_sales'] - $first_month['total_sales']) / $first_month['total_sales']) * 100;
                $is_growing = $growth_rate > 0;
            }
            
            // Calculate best and worst months
            $max_sales = 0;
            $min_sales = PHP_FLOAT_MAX;
            $best_month = null;
            $worst_month = null;
            
            foreach ($data as $item) {
                if ($item['total_sales'] > $max_sales) {
                    $max_sales = $item['total_sales'];
                    $best_month = $item;
                }
                
                if ($item['total_sales'] < $min_sales && $item['total_sales'] > 0) {
                    $min_sales = $item['total_sales'];
                    $worst_month = $item;
                }
            }
            
            // Calculate seasonality (if enough data)
            $has_seasonality = false;
            $season_pattern = '';
            
            if (count($data) >= 6) {
                // Simple seasonality check (this is a simplified approach)
                $monthly_averages = [];
                
                foreach ($data as $item) {
                    $month = date("n", strtotime($item['month'] . "-01"));
                    if (!isset($monthly_averages[$month])) {
                        $monthly_averages[$month] = ['total' => 0, 'count' => 0];
                    }
                    $monthly_averages[$month]['total'] += $item['total_sales'];
                    $monthly_averages[$month]['count']++;
                }
                
                foreach ($monthly_averages as $month => $values) {
                    $monthly_averages[$month]['average'] = $values['total'] / $values['count'];
                }
                
                // Sort by average sales
                uasort($monthly_averages, function($a, $b) {
                    return $b['average'] - $a['average'];
                });
                
                // Get top 2 months
                $top_months = array_slice($monthly_averages, 0, 2, true);
                
                if (!empty($top_months)) {
                    $has_seasonality = true;
                    $season_months = [];
                    
                    foreach (array_keys($top_months) as $month_num) {
                        $month_name = date("F", mktime(0, 0, 0, $month_num, 1, 2023));
                        $season_months[] = $month_name;
                    }
                    
                    $season_pattern = implode(' and ', $season_months);
                }
            }
        ?>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card border-<?php echo $is_growing ? 'success' : 'warning'; ?> mb-3">
                    <div class="card-header bg-<?php echo $is_growing ? 'success' : 'warning'; ?> text-white">
                        <h5 class="mb-0">
                            <?php if ($is_growing): ?>
                            <i class="fas fa-chart-line me-2"></i>Growth Trend
                            <?php else: ?>
                            <i class="fas fa-chart-line me-2"></i>Sales Trend
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="display-4 me-3 <?php echo $is_growing ? 'text-success' : 'text-danger'; ?>">
                                <?php echo $is_growing ? '+' : ''; ?><?php echo round($growth_rate, 1); ?>%
                            </div>
                            <div>
                                <p class="mb-0">
                                    <?php if ($is_growing): ?>
                                    <span class="text-success"><i class="fas fa-arrow-up me-1"></i>Growth</span>
                                    <?php else: ?>
                                    <span class="text-danger"><i class="fas fa-arrow-down me-1"></i>Decline</span>
                                    <?php endif; ?>
                                    <br>over this period
                                </p>
                            </div>
                        </div>
                        
                        <p>
                            Your sales have <?php echo $is_growing ? 'grown' : 'declined'; ?> from 
                            <strong><?php echo format_currency($first_month['total_sales']); ?></strong> 
                            to <strong><?php echo format_currency($last_month['total_sales']); ?></strong>
                            over the selected period.
                        </p>
                        
                        <?php if ($is_growing): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Great job!</strong> Keep up the good work and consider expanding your product offerings.
                        </div>
                        <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Action needed:</strong> Consider revising your pricing, improving product listings, or expanding your marketing efforts.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card border-info mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Monthly Performance</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($best_month): ?>
                        <div class="mb-3">
                            <h6 class="text-success"><i class="fas fa-trophy me-2"></i>Best Performing Month</h6>
                            <p>
                                <strong><?php echo date("F Y", strtotime($best_month['month'] . "-01")); ?></strong> with
                                <strong><?php echo format_currency($best_month['total_sales']); ?></strong> in sales and
                                <strong><?php echo $best_month['orders_count']; ?></strong> orders.
                            </p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($worst_month): ?>
                        <div class="mb-3">
                            <h6 class="text-danger"><i class="fas fa-exclamation-circle me-2"></i>Lowest Performing Month</h6>
                            <p>
                                <strong><?php echo date("F Y", strtotime($worst_month['month'] . "-01")); ?></strong> with
                                <strong><?php echo format_currency($worst_month['total_sales']); ?></strong> in sales and
                                <strong><?php echo $worst_month['orders_count']; ?></strong> orders.
                            </p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($has_seasonality): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Seasonality detected:</strong> Your sales tend to perform better during <strong><?php echo $season_pattern; ?></strong>. Consider planning your inventory and marketing efforts around these months.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Strategic Recommendations -->
        <div class="card border-primary mt-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Strategic Recommendations</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h6><i class="fas fa-chart-line text-primary me-2"></i>Growth Strategies</h6>
                            <ul class="list-unstyled ps-4">
                                <?php if ($is_growing): ?>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Continue expanding your product range</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Consider premium pricing for top products</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Explore new market locations</li>
                                <?php else: ?>
                                <li><i class="fas fa-exclamation-circle text-warning me-2"></i>Review pricing strategy</li>
                                <li><i class="fas fa-exclamation-circle text-warning me-2"></i>Improve product descriptions and images</li>
                                <li><i class="fas fa-exclamation-circle text-warning me-2"></i>Consider promotional discounts</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h6><i class="fas fa-calendar-alt text-primary me-2"></i>Seasonal Planning</h6>
                            <ul class="list-unstyled ps-4">
                                <?php if ($has_seasonality): ?>
                                <li><i class="fas fa-info-circle text-info me-2"></i>Increase inventory before <?php echo $season_pattern; ?></li>
                                <li><i class="fas fa-info-circle text-info me-2"></i>Plan marketing campaigns for peak seasons</li>
                                <li><i class="fas fa-info-circle text-info me-2"></i>Consider special offers during low seasons</li>
                                <?php else: ?>
                                <li><i class="fas fa-info-circle text-info me-2"></i>Monitor sales patterns for emerging trends</li>
                                <li><i class="fas fa-info-circle text-info me-2"></i>Maintain consistent inventory levels</li>
                                <li><i class="fas fa-info-circle text-info me-2"></i>Plan regular marketing activities</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h6><i class="fas fa-bullseye text-primary me-2"></i>Focus Areas</h6>
                            <ul class="list-unstyled ps-4">
                                <?php if ($best_month && $worst_month): ?>
                                <li><i class="fas fa-lightbulb text-warning me-2"></i>Analyze what worked well in <?php echo date("F", strtotime($best_month['month'] . "-01")); ?></li>
                                <li><i class="fas fa-lightbulb text-warning me-2"></i>Identify challenges in <?php echo date("F", strtotime($worst_month['month'] . "-01")); ?></li>
                                <?php endif; ?>
                                <li><i class="fas fa-lightbulb text-warning me-2"></i>Build relationships with repeat buyers</li>
                                <li><i class="fas fa-lightbulb text-warning me-2"></i>Track competitors' pricing and offerings</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
