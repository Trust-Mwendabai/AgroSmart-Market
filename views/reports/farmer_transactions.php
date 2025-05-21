<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Transaction Details - <?php echo get_period_name($period); ?></h5>
    </div>
    <div class="card-body">
        <?php if (empty($data['transactions'])): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            No transaction data available for this period.
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Buyer</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-end">Price</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['transactions'] as $transaction): ?>
                    <tr>
                        <td>#<?php echo $transaction['id']; ?></td>
                        <td><?php echo format_date($transaction['date_ordered']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['category']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['buyer_name']); ?></td>
                        <td class="text-center"><?php echo $transaction['quantity']; ?></td>
                        <td class="text-end"><?php echo format_currency($transaction['price']); ?></td>
                        <td class="text-end"><?php echo format_currency($transaction['total_amount']); ?></td>
                        <td class="text-center">
                            <?php 
                            $status_class = '';
                            switch ($transaction['status']) {
                                case 'pending': $status_class = 'bg-warning'; break;
                                case 'confirmed': $status_class = 'bg-info'; break;
                                case 'shipped': $status_class = 'bg-primary'; break;
                                case 'delivered': $status_class = 'bg-success'; break;
                                case 'cancelled': $status_class = 'bg-danger'; break;
                                default: $status_class = 'bg-secondary';
                            }
                            ?>
                            <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($transaction['status']); ?></span>
                        </td>
                        <td class="text-center">
                            <a href="order.php?action=view&id=<?php echo $transaction['id']; ?>" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($data['pages'] > 1): ?>
        <nav aria-label="Transaction pagination" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($data['current_page'] > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?type=transactions&period=<?php echo $period; ?>&page=<?php echo $data['current_page'] - 1; ?>">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                </li>
                <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link"><i class="fas fa-chevron-left"></i> Previous</span>
                </li>
                <?php endif; ?>
                
                <?php
                // Calculate range of pages to show
                $start_page = max(1, $data['current_page'] - 2);
                $end_page = min($data['pages'], $data['current_page'] + 2);
                
                // Always show first page
                if ($start_page > 1) {
                    echo '<li class="page-item"><a class="page-link" href="?type=transactions&period=' . $period . '&page=1">1</a></li>';
                    if ($start_page > 2) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                }
                
                // Page links
                for ($i = $start_page; $i <= $end_page; $i++) {
                    if ($i == $data['current_page']) {
                        echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                    } else {
                        echo '<li class="page-item"><a class="page-link" href="?type=transactions&period=' . $period . '&page=' . $i . '">' . $i . '</a></li>';
                    }
                }
                
                // Always show last page
                if ($end_page < $data['pages']) {
                    if ($end_page < $data['pages'] - 1) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                    echo '<li class="page-item"><a class="page-link" href="?type=transactions&period=' . $period . '&page=' . $data['pages'] . '">' . $data['pages'] . '</a></li>';
                }
                ?>
                
                <?php if ($data['current_page'] < $data['pages']): ?>
                <li class="page-item">
                    <a class="page-link" href="?type=transactions&period=<?php echo $period; ?>&page=<?php echo $data['current_page'] + 1; ?>">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link">Next <i class="fas fa-chevron-right"></i></span>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
        
        <div class="alert alert-info mt-3">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-info-circle fa-2x"></i>
                </div>
                <div>
                    <h5 class="alert-heading mb-1">Transaction Summary</h5>
                    <p class="mb-0">
                        Showing <?php echo count($data['transactions']); ?> out of <?php echo $data['total']; ?> total transactions.
                        <br>Use the pagination controls to navigate through all transactions or change the time period filter above.
                        <br>Click the <i class="fas fa-eye"></i> icon to view order details.
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Transaction Stats -->
<div class="row">
    <!-- Status Breakdown -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Order Status Breakdown</h5>
            </div>
            <div class="card-body">
                <?php if (empty($data['transactions'])): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No data available for visualization.
                </div>
                <?php else: ?>
                <?php
                    // Count transactions by status
                    $status_counts = [];
                    foreach ($data['transactions'] as $transaction) {
                        $status = $transaction['status'];
                        if (!isset($status_counts[$status])) {
                            $status_counts[$status] = 0;
                        }
                        $status_counts[$status]++;
                    }
                ?>
                <div class="row">
                    <div class="col-md-7">
                        <canvas id="statusChart" width="100%" height="220"></canvas>
                    </div>
                    <div class="col-md-5">
                        <div class="list-group mt-3">
                            <?php 
                            $status_colors = [
                                'pending' => '#ffc107',
                                'confirmed' => '#17a2b8',
                                'shipped' => '#007bff',
                                'delivered' => '#28a745',
                                'cancelled' => '#dc3545'
                            ];
                            
                            foreach ($status_counts as $status => $count): 
                                $percent = round(($count / count($data['transactions'])) * 100);
                                $color = isset($status_colors[$status]) ? $status_colors[$status] : '#6c757d';
                            ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-circle" style="color: <?php echo $color; ?>"></i>
                                    <?php echo ucfirst($status); ?>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo $count; ?> (<?php echo $percent; ?>%)</span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Prepare data for chart
                    const statusLabels = [];
                    const statusData = [];
                    const statusColors = [];
                    
                    <?php 
                    foreach ($status_counts as $status => $count): 
                        $color = isset($status_colors[$status]) ? $status_colors[$status] : '#6c757d';
                    ?>
                    statusLabels.push('<?php echo ucfirst($status); ?>');
                    statusData.push(<?php echo $count; ?>);
                    statusColors.push('<?php echo $color; ?>');
                    <?php endforeach; ?>
                    
                    // Create chart
                    const ctx = document.getElementById('statusChart').getContext('2d');
                    const statusChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: statusLabels,
                            datasets: [{
                                data: statusData,
                                backgroundColor: statusColors,
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
    
    <!-- Buyer Distribution -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Top Buyers</h5>
            </div>
            <div class="card-body">
                <?php if (empty($data['transactions'])): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No data available for visualization.
                </div>
                <?php else: ?>
                <?php
                    // Group transactions by buyer
                    $buyers = [];
                    foreach ($data['transactions'] as $transaction) {
                        $buyer_name = $transaction['buyer_name'];
                        if (!isset($buyers[$buyer_name])) {
                            $buyers[$buyer_name] = [
                                'orders' => 0,
                                'amount' => 0,
                                'location' => $transaction['buyer_location'] ?? 'Unknown'
                            ];
                        }
                        $buyers[$buyer_name]['orders']++;
                        $buyers[$buyer_name]['amount'] += $transaction['total_amount'];
                    }
                    
                    // Sort by amount spent
                    uasort($buyers, function($a, $b) {
                        return $b['amount'] - $a['amount'];
                    });
                    
                    // Take top 5
                    $top_buyers = array_slice($buyers, 0, 5, true);
                ?>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Buyer</th>
                                <th>Location</th>
                                <th class="text-center">Orders</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($top_buyers as $name => $data): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($name); ?></td>
                                <td><?php echo htmlspecialchars($data['location']); ?></td>
                                <td class="text-center"><?php echo $data['orders']; ?></td>
                                <td class="text-end"><?php echo format_currency($data['amount']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="alert alert-success mt-3">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Tip:</strong> Cultivate relationships with your top buyers by offering them special deals or priority on new products.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
