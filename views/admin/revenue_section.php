<!-- Revenue Streams Section -->
<div class="row mb-4" id="revenue-section">
    <div class="col-12">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-header bg-transparent border-bottom-0 d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold"><i class="fas fa-chart-line text-danger me-2"></i>Revenue Streams</h5>
                <a href="reports.php?type=revenue" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-file-pdf me-1"></i>Generate Report
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Revenue Chart -->
                    <div class="col-lg-8 mb-4">
                        <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                            <canvas id="revenueStreamChart"></canvas>
                        </div>
                        
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                // Get the canvas element
                                var revenueCtx = document.getElementById('revenueStreamChart').getContext('2d');
                                
                                // Revenue data
                                var revenueData = {
                                    labels: ['Commissions', 'In-app Ads', 'Premium Listings', 'Transport Fees', 'Subscriptions'],
                                    datasets: [{
                                        label: 'Revenue (K)',
                                        data: [
                                            <?php echo $stats['revenue_streams']['commissions']; ?>,
                                            <?php echo $stats['revenue_streams']['ads']; ?>,
                                            <?php echo $stats['revenue_streams']['premium_listings']; ?>,
                                            <?php echo $stats['revenue_streams']['transport_fees']; ?>,
                                            <?php echo $stats['revenue_streams']['subscriptions']; ?>
                                        ],
                                        backgroundColor: [
                                            'rgba(255, 99, 132, 0.7)',
                                            'rgba(54, 162, 235, 0.7)',
                                            'rgba(255, 206, 86, 0.7)',
                                            'rgba(75, 192, 192, 0.7)',
                                            'rgba(153, 102, 255, 0.7)'
                                        ],
                                        borderColor: [
                                            'rgba(255, 99, 132, 1)',
                                            'rgba(54, 162, 235, 1)',
                                            'rgba(255, 206, 86, 1)',
                                            'rgba(75, 192, 192, 1)',
                                            'rgba(153, 102, 255, 1)'
                                        ],
                                        borderWidth: 1
                                    }]
                                };
                                
                                // Config for revenue chart
                                var revenueChart = new Chart(revenueCtx, {
                                    type: 'bar',
                                    data: revenueData,
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: {
                                                display: false
                                            },
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        return 'K' + context.raw.toFixed(2);
                                                    }
                                                }
                                            }
                                        },
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
                                
                                // Monthly revenue trend chart
                                var monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
                                
                                // Monthly revenue data
                                var monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                var monthlyRevenueData = {
                                    labels: monthNames,
                                    datasets: [{
                                        label: 'Monthly Revenue (K)',
                                        data: [
                                            <?php 
                                                foreach ($stats['monthly_revenue'] as $revenue) {
                                                    echo $revenue . ',';
                                                }
                                            ?>
                                        ],
                                        fill: true,
                                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                        borderColor: 'rgba(255, 99, 132, 1)',
                                        borderWidth: 2,
                                        tension: 0.4
                                    }]
                                };
                                
                                // Config for monthly revenue chart
                                var monthlyRevenueChart = new Chart(monthlyRevenueCtx, {
                                    type: 'line',
                                    data: monthlyRevenueData,
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: {
                                                display: false
                                            },
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        return 'K' + context.raw.toFixed(2);
                                                    }
                                                }
                                            }
                                        },
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
                    </div>
                    
                    <!-- Revenue Breakdown Table -->
                    <div class="col-lg-4 mb-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Revenue Stream</th>
                                        <th class="text-end">Amount</th>
                                        <th class="text-end">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Calculate total revenue to get percentages
                                    $total = $stats['total_revenue'] > 0 ? $stats['total_revenue'] : 1; // Avoid division by zero
                                    
                                    // Revenue streams data with descriptions
                                    $streams = [
                                        ['name' => '1% Commission', 'amount' => $stats['revenue_streams']['commissions'], 'description' => 'From all marketplace transactions'],
                                        ['name' => 'In-app Ads', 'amount' => $stats['revenue_streams']['ads'], 'description' => 'Sponsored content from buyers and NGOs'],
                                        ['name' => 'Premium Listings', 'amount' => $stats['revenue_streams']['premium_listings'], 'description' => 'Featured farmers and products'],
                                        ['name' => 'Transport Fees', 'amount' => $stats['revenue_streams']['transport_fees'], 'description' => 'Delivery arrangement services'],
                                        ['name' => 'Subscriptions', 'amount' => $stats['revenue_streams']['subscriptions'], 'description' => 'Premium buyer plans']
                                    ];
                                    
                                    foreach ($streams as $stream) {
                                        $percentage = ($stream['amount'] / $total) * 100;
                                        echo '<tr data-bs-toggle="tooltip" title="' . $stream['description'] . '">';
                                        echo '<td>' . $stream['name'] . '</td>';
                                        echo '<td class="text-end">K' . number_format($stream['amount'], 2) . '</td>';
                                        echo '<td class="text-end">' . number_format($percentage, 1) . '%</td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                    <tr class="table-light fw-bold">
                                        <td>Total Revenue</td>
                                        <td class="text-end">K<?php echo number_format($stats['total_revenue'], 2); ?></td>
                                        <td class="text-end">100%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Monthly Trend -->
                        <div class="mt-3">
                            <h6 class="mb-3 fw-bold">Monthly Trend (<?php echo date('Y'); ?>)</h6>
                            <div class="chart-container" style="position: relative; height: 150px; width: 100%;">
                                <canvas id="monthlyRevenueChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
