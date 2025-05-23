<!-- General Reports View -->
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0"><?php echo $report_title; ?></h3>
            <p class="text-muted mb-0">Platform reports and analytics</p>
        </div>
        <div>
            <a href="../dashboard.php" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Report Types -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                            <i class="fas fa-chart-line text-danger fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Revenue Reports</h5>
                            <p class="text-muted small mb-0">Income analysis and breakdowns</p>
                        </div>
                    </div>
                    <p class="card-text">View detailed reports on platform revenue streams including commissions, ads, premium listings, transport fees, and subscriptions.</p>
                    <a href="reports.php?type=revenue" class="btn btn-outline-danger">View Reports</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="fas fa-users text-primary fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">User Reports</h5>
                            <p class="text-muted small mb-0">Farmers and buyers analysis</p>
                        </div>
                    </div>
                    <p class="card-text">Analyze user registration trends, demographics, active users, and usage patterns for both farmers and buyers.</p>
                    <a href="reports.php?type=users" class="btn btn-outline-primary">View Reports</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="fas fa-shopping-cart text-success fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Order Reports</h5>
                            <p class="text-muted small mb-0">Transaction and sales analysis</p>
                        </div>
                    </div>
                    <p class="card-text">Track order volumes, popular products, seasonal trends, and delivery metrics across the platform.</p>
                    <a href="reports.php?type=orders" class="btn btn-outline-success">View Reports</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include admin footer -->
<?php include '../views/admin/partials/footer.php'; ?>
