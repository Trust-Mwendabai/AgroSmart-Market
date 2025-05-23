<!-- Users Report View -->
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0"><?php echo $report_title; ?></h3>
            <p class="text-muted mb-0">Detailed overview of platform users and demographics</p>
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

    <!-- Users Overview Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Farmers</h5>
                    <h2 class="display-6 fw-bold text-success mb-0"><?php echo isset($report_data['total_farmers']) ? number_format($report_data['total_farmers']) : 0; ?></h2>
                    <p class="text-muted">Registered agricultural producers</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Buyers</h5>
                    <h2 class="display-6 fw-bold text-primary mb-0"><?php echo isset($report_data['total_buyers']) ? number_format($report_data['total_buyers']) : 0; ?></h2>
                    <p class="text-muted">Registered customers and businesses</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Active Users</h5>
                    <h2 class="display-6 fw-bold text-info mb-0"><?php echo isset($report_data['active_users']) ? number_format($report_data['active_users']) : 0; ?></h2>
                    <p class="text-muted">Users active in last 30 days</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">New Registrations</h5>
                    <h2 class="display-6 fw-bold text-warning mb-0"><?php echo isset($report_data['new_users']) ? number_format($report_data['new_users']) : 0; ?></h2>
                    <p class="text-muted">Registrations in last 30 days</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Placeholder for user reports - to be implemented with actual data -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">User Demographics</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This report section will display detailed user demographics including locations, registration trends, and user activity metrics.
                    </div>
                    
                    <!-- Placeholder chart -->
                    <div class="text-center py-5">
                        <i class="fas fa-chart-pie fa-4x text-muted mb-3"></i>
                        <h5>User Demographics Visualization</h5>
                        <p class="text-muted">Detailed user statistics charts will appear here</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent User Registrations</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User ID</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Email</th>
                                    <th>Location</th>
                                    <th>Registered Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($report_data['recent_users'])): ?>
                                    <?php foreach ($report_data['recent_users'] as $user): ?>
                                        <tr>
                                            <td><?php echo $user['id']; ?></td>
                                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                                            <td><span class="badge bg-<?php echo $user['user_type'] == 'farmer' ? 'success' : 'primary'; ?>"><?php echo ucfirst($user['user_type']); ?></span></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><?php echo htmlspecialchars($user['location'] ?? 'Not specified'); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                            <td>
                                                <?php if ($user['is_active']): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-3">No recent user registrations found</td>
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

<!-- Include admin footer -->
<?php include '../views/admin/partials/footer.php'; ?>
