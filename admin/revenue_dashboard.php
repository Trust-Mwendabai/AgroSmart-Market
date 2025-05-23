<?php
// Start session
session_start();

// Include database connection and utilities
$conn = require_once '../config/database.php';
require_once '../config/utils.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Get revenue statistics
$stats = [
    'total_revenue' => 0,
    'revenue_streams' => [
        'commissions' => 0,
        'ads' => 0,
        'premium_listings' => 0,
        'transport_fees' => 0,
        'subscriptions' => 0
    ],
    'monthly_revenue' => []
];

// Get total revenue
$query = "SELECT SUM(amount) as total FROM revenue_transactions";
$result = mysqli_query($conn, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $stats['total_revenue'] = $row['total'] ?: 0;
}

// Get revenue by stream
$query = "SELECT stream_type, SUM(amount) as total FROM revenue_transactions GROUP BY stream_type";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        switch ($row['stream_type']) {
            case 'commission':
                $stats['revenue_streams']['commissions'] = $row['total'];
                break;
            case 'ad':
                $stats['revenue_streams']['ads'] = $row['total'];
                break;
            case 'premium_listing':
                $stats['revenue_streams']['premium_listings'] = $row['total'];
                break;
            case 'transport_fee':
                $stats['revenue_streams']['transport_fees'] = $row['total'];
                break;
            case 'subscription':
                $stats['revenue_streams']['subscriptions'] = $row['total'];
                break;
        }
    }
}

// Get monthly revenue for the current year
$current_year = date('Y');
$query = "SELECT MONTH(transaction_date) as month, SUM(amount) as total FROM revenue_transactions 
          WHERE YEAR(transaction_date) = '{$current_year}' GROUP BY month ORDER BY month";
$result = mysqli_query($conn, $query);
if ($result) {
    $monthly_data = array_fill(1, 12, 0); // Initialize all months with zero
    while ($row = mysqli_fetch_assoc($result)) {
        $monthly_data[$row['month']] = $row['total'];
    }
    $stats['monthly_revenue'] = $monthly_data;
}

// Set page title
$page_title = "Revenue Dashboard - AgroSmart Market";

// Include admin header
include '../views/admin/partials/header.php';
?>

<!-- Revenue Dashboard Content -->
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0">Revenue Dashboard</h3>
            <p class="text-muted mb-0">Platform revenue streams and statistics</p>
        </div>
        <div>
            <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
            </a>
            <a href="reports.php?type=revenue" class="btn btn-outline-danger btn-sm ms-2">
                <i class="fas fa-file-pdf me-1"></i>Detailed Report
            </a>
        </div>
    </div>

    <!-- Revenue Overview Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted mb-2">Total Revenue</h6>
                    <h2 class="display-4 fw-bold text-danger mb-0">K<?php echo number_format($stats['total_revenue'], 2); ?></h2>
                    <p class="text-muted mt-2 mb-0">Platform income from all streams</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-8 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted mb-3">Revenue Distribution</h6>
                    <div class="row">
                        <div class="col-lg-7">
                            <canvas id="revenueDistributionChart" height="200"></canvas>
                        </div>
                        <div class="col-lg-5">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Revenue Stream</th>
                                            <th class="text-end">Amount (K)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1% Commission</td>
                                            <td class="text-end"><?php echo number_format($stats['revenue_streams']['commissions'], 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td>In-app Ads</td>
                                            <td class="text-end"><?php echo number_format($stats['revenue_streams']['ads'], 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Premium Listings</td>
                                            <td class="text-end"><?php echo number_format($stats['revenue_streams']['premium_listings'], 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Transport Fees</td>
                                            <td class="text-end"><?php echo number_format($stats['revenue_streams']['transport_fees'], 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Subscriptions</td>
                                            <td class="text-end"><?php echo number_format($stats['revenue_streams']['subscriptions'], 2); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Revenue Streams Breakdown</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Revenue Stream</th>
                                    <th>Description</th>
                                    <th class="text-end">Amount (K)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>1% Commission</strong></td>
                                    <td>Platform fee applied to all marketplace transactions</td>
                                    <td class="text-end"><?php echo number_format($stats['revenue_streams']['commissions'], 2); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>In-app Ads</strong></td>
                                    <td>Revenue from advertisements placed by buyers, NGOs, and other stakeholders</td>
                                    <td class="text-end"><?php echo number_format($stats['revenue_streams']['ads'], 2); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Premium Listings</strong></td>
                                    <td>Featured placement fees for farmers and products on the marketplace</td>
                                    <td class="text-end"><?php echo number_format($stats['revenue_streams']['premium_listings'], 2); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Transport Fees</strong></td>
                                    <td>Fees from coordinating transportation between farmers and buyers</td>
                                    <td class="text-end"><?php echo number_format($stats['revenue_streams']['transport_fees'], 2); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Subscriptions</strong></td>
                                    <td>Premium subscription plans for regular and business buyers</td>
                                    <td class="text-end"><?php echo number_format($stats['revenue_streams']['subscriptions'], 2); ?></td>
                                </tr>
                                <tr class="table-light fw-bold">
                                    <td>Total</td>
                                    <td>All revenue streams</td>
                                    <td class="text-end"><?php echo number_format($stats['total_revenue'], 2); ?></td>
                                </tr>
                            </tbody>
                        </table>
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
                <?php echo $stats['revenue_streams']['commissions']; ?>,
                <?php echo $stats['revenue_streams']['ads']; ?>,
                <?php echo $stats['revenue_streams']['premium_listings']; ?>,
                <?php echo $stats['revenue_streams']['transport_fees']; ?>,
                <?php echo $stats['revenue_streams']['subscriptions']; ?>
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
                        boxWidth: 15
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
                foreach ($stats['monthly_revenue'] as $revenue) {
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

<?php
// Include admin footer
include '../views/admin/partials/footer.php';
?>
