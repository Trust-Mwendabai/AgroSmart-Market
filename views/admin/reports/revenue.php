<!-- Revenue Report View -->
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0"><?php echo $report_title; ?></h3>
            <p class="text-muted mb-0">Detailed overview of platform revenue streams</p>
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

    <!-- Revenue Overview Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Revenue</h5>
                    <h2 class="display-6 fw-bold text-danger mb-0">K<?php echo number_format($report_data['total_revenue'], 2); ?></h2>
                    <p class="text-muted">Platform income across all streams</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-8 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Revenue Distribution</h5>
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <canvas id="revenueDistributionChart" height="200"></canvas>
                        </div>
                        <div class="col-md-5">
                            <ul class="list-group list-group-flush">
                                <?php
                                // Revenue stream labels and their descriptions
                                $stream_labels = [
                                    'commission' => '1% Commission',
                                    'ad' => 'In-app Ads',
                                    'premium_listing' => 'Premium Listings',
                                    'transport_fee' => 'Transport Fees',
                                    'subscription' => 'Subscriptions'
                                ];
                                
                                $total = $report_data['total_revenue'] > 0 ? $report_data['total_revenue'] : 1; // Avoid division by zero
                                
                                foreach ($stream_labels as $key => $label) {
                                    $amount = isset($report_data['revenue_streams'][$key]) ? $report_data['revenue_streams'][$key] : 0;
                                    $percentage = ($amount / $total) * 100;
                                    echo '<li class="list-group-item d-flex justify-content-between align-items-center px-0">';
                                    echo $label;
                                    echo '<span class="badge bg-primary rounded-pill">K' . number_format($amount, 2) . ' (' . number_format($percentage, 1) . '%)</span>';
                                    echo '</li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Revenue Trend -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Monthly Revenue Trend (<?php echo date('Y'); ?>)</h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="monthlyRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Streams Breakdown -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Revenue Streams Breakdown</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Revenue Stream</th>
                                    <th>Description</th>
                                    <th>Amount (K)</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Revenue streams with detailed descriptions
                                $stream_details = [
                                    'commission' => ['name' => '1% Commission', 'description' => 'Platform fee applied to all marketplace transactions'],
                                    'ad' => ['name' => 'In-app Ads', 'description' => 'Revenue from advertisements placed by buyers, NGOs, and other stakeholders'],
                                    'premium_listing' => ['name' => 'Premium Listings', 'description' => 'Featured placement fees for farmers and products on the marketplace'],
                                    'transport_fee' => ['name' => 'Transport Facilitation', 'description' => 'Fees from coordinating transportation between farmers and buyers'],
                                    'subscription' => ['name' => 'Buyer Subscriptions', 'description' => 'Premium subscription plans for regular and business buyers']
                                ];
                                
                                foreach ($stream_details as $key => $details) {
                                    $amount = isset($report_data['revenue_streams'][$key]) ? $report_data['revenue_streams'][$key] : 0;
                                    $percentage = ($amount / $total) * 100;
                                    echo '<tr>';
                                    echo '<td><strong>' . $details['name'] . '</strong></td>';
                                    echo '<td>' . $details['description'] . '</td>';
                                    echo '<td>K' . number_format($amount, 2) . '</td>';
                                    echo '<td>' . number_format($percentage, 1) . '%</td>';
                                    echo '</tr>';
                                }
                                ?>
                                <tr class="table-light fw-bold">
                                    <td>Total</td>
                                    <td>All revenue streams</td>
                                    <td>K<?php echo number_format($report_data['total_revenue'], 2); ?></td>
                                    <td>100%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Revenue Transactions</h5>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#transactionsCollapse">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="collapse" id="transactionsCollapse">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Stream</th>
                                        <th>Description</th>
                                        <th class="text-end">Amount (K)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($report_data['transactions'])): ?>
                                        <?php foreach ($report_data['transactions'] as $transaction): ?>
                                            <tr>
                                                <td><?php echo $transaction['id']; ?></td>
                                                <td><?php echo date('M d, Y', strtotime($transaction['transaction_date'])); ?></td>
                                                <td>
                                                    <?php 
                                                    $stream_badge_colors = [
                                                        'commission' => 'primary',
                                                        'ad' => 'info',
                                                        'premium_listing' => 'warning',
                                                        'transport_fee' => 'success',
                                                        'subscription' => 'danger'
                                                    ];
                                                    $badge_color = isset($stream_badge_colors[$transaction['stream_type']]) ? $stream_badge_colors[$transaction['stream_type']] : 'secondary';
                                                    echo '<span class="badge bg-' . $badge_color . '">' . $stream_labels[$transaction['stream_type']] . '</span>';
                                                    ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                                                <td class="text-end">K<?php echo number_format($transaction['amount'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-3">No transactions found</td>
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
</div>

<!-- Charts JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Distribution Chart
    const distributionCtx = document.getElementById('revenueDistributionChart').getContext('2d');
    
    const distributionData = {
        labels: ['1% Commission', 'In-app Ads', 'Premium Listings', 'Transport Fees', 'Subscriptions'],
        datasets: [{
            data: [
                <?php 
                echo (isset($report_data['revenue_streams']['commission']) ? $report_data['revenue_streams']['commission'] : 0) . ', ';
                echo (isset($report_data['revenue_streams']['ad']) ? $report_data['revenue_streams']['ad'] : 0) . ', ';
                echo (isset($report_data['revenue_streams']['premium_listing']) ? $report_data['revenue_streams']['premium_listing'] : 0) . ', ';
                echo (isset($report_data['revenue_streams']['transport_fee']) ? $report_data['revenue_streams']['transport_fee'] : 0) . ', ';
                echo (isset($report_data['revenue_streams']['subscription']) ? $report_data['revenue_streams']['subscription'] : 0);
                ?>
            ],
            backgroundColor: [
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 99, 132, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(153, 102, 255, 0.7)'
            ],
            borderColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
        }]
    };
    
    new Chart(distributionCtx, {
        type: 'doughnut',
        data: distributionData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 15,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
    
    // Monthly Revenue Chart
    const monthlyCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
    
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    
    const monthlyData = {
        labels: monthNames,
        datasets: [{
            label: 'Revenue (K)',
            data: [
                <?php 
                foreach ($report_data['monthly_revenue'] as $revenue) {
                    echo $revenue . ',';
                }
                ?>
            ],
            fill: {
                target: 'origin',
                above: 'rgba(54, 162, 235, 0.2)'
            },
            borderColor: 'rgba(54, 162, 235, 1)',
            tension: 0.4
        }]
    };
    
    new Chart(monthlyCtx, {
        type: 'line',
        data: monthlyData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'K' + value;
                        }
                    }
                }
            }
        }
    });
});
</script>

<!-- Include admin footer -->
<?php include '../views/admin/partials/footer.php'; ?>
