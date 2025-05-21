<?php
// Start session
session_start();

// Include necessary files
require_once 'config/database.php';
require_once 'config/utils.php';
require_once 'models/User.php';
require_once 'models/Agent.php';
require_once 'models/Product.php';

// Check if user is logged in and is an agent
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'agent') {
    redirect('login.php?message=You must be logged in as an agent to access this page');
}

// Set page title
$page_title = 'Agent Dashboard';

// Initialize models
$user_model = new User($conn);
$agent_model = new Agent($conn);
$product_model = new Product($conn);

// Get agent information
$agent_id = $_SESSION['user_id'];
$agent = $agent_model->get_agent_by_id($agent_id);

// Get registered farmers
$registered_farmers = $agent_model->get_registered_farmers($agent_id, 5, 0);
$total_farmers = $agent_model->count_registered_farmers($agent_id);

// Get agent activity metrics
$metrics = $agent_model->get_activity_metrics($agent_id);

// Include header
include 'views/partials/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-3">
            <!-- Sidebar -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <?php if (!empty($agent['image'])): ?>
                            <img src="public/uploads/users/<?php echo $agent['image']; ?>" alt="<?php echo $agent['name']; ?>" class="rounded-circle img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px;">
                                <i class="fas fa-user-tie fa-3x"></i>
                            </div>
                        <?php endif; ?>
                        <h4 class="mt-2 mb-0"><?php echo $agent['name']; ?></h4>
                        <p class="text-muted">Agricultural Agent</p>
                        
                        <?php if (!empty($agent['organization'])): ?>
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-building me-1"></i> <?php echo $agent['organization']; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="list-group list-group-flush">
                        <a href="agent-dashboard.php" class="list-group-item list-group-item-action active">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                        <a href="farmer-registration.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-user-plus me-2"></i> Register Farmer
                        </a>
                        <a href="manage-farmers.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-users me-2"></i> Manage Farmers
                        </a>
                        <a href="add-product.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-carrot me-2"></i> Add Product
                        </a>
                        <a href="pending-orders.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-shopping-basket me-2"></i> Pending Orders
                        </a>
                        <a href="agent-profile.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-user-cog me-2"></i> My Profile
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Offline Mode Status -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-wifi me-2"></i> Connection Status
                    </h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="offlineMode">
                        <label class="form-check-label" for="offlineMode">Enable Offline Mode</label>
                    </div>
                    <p class="text-muted small mt-2 mb-0">
                        When enabled, data will be saved locally and synced when you're back online.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <!-- Welcome Banner -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h2 class="fw-bold mb-2">Welcome back, <?php echo $agent['name']; ?>!</h2>
                            <p class="text-muted">
                                <?php echo date('l, F j, Y'); ?> | 
                                Last login: <?php echo !empty($_SESSION['last_login']) ? date('M j, g:i a', strtotime($_SESSION['last_login'])) : 'First time login'; ?>
                            </p>
                            <div class="mt-3">
                                <a href="farmer-registration.php" class="btn btn-primary me-2">
                                    <i class="fas fa-user-plus me-2"></i>Register New Farmer
                                </a>
                                <a href="add-product.php" class="btn btn-outline-primary">
                                    <i class="fas fa-plus me-2"></i>Add Product
                                </a>
                            </div>
                        </div>
                        <div class="col-md-5 text-center">
                            <img src="public/img/agent-dashboard.svg" alt="Agent Dashboard" class="img-fluid" style="max-height: 180px;">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box bg-primary-light text-primary me-3">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Total Farmers</h6>
                                    <h3 class="mb-0"><?php echo $total_farmers; ?></h3>
                                </div>
                            </div>
                            <p class="text-success mb-0 small">
                                <i class="fas fa-arrow-up me-1"></i>
                                <?php echo $metrics['farmers_this_month']; ?> this month
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box bg-success-light text-success me-3">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Products Added</h6>
                                    <h3 class="mb-0"><?php echo $metrics['products_this_month']; ?></h3>
                                </div>
                            </div>
                            <p class="text-success mb-0 small">
                                <i class="fas fa-arrow-up me-1"></i>
                                This month
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box bg-info-light text-info me-3">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Orders</h6>
                                    <h3 class="mb-0"><?php echo $metrics['orders_this_month']; ?></h3>
                                </div>
                            </div>
                            <p class="text-success mb-0 small">
                                <i class="fas fa-arrow-up me-1"></i>
                                This month
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box bg-warning-light text-warning me-3">
                                    <i class="fas fa-money-bill"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Sales Volume</h6>
                                    <h3 class="mb-0">K<?php echo number_format($metrics['sales_value_this_month'], 2); ?></h3>
                                </div>
                            </div>
                            <p class="text-success mb-0 small">
                                <i class="fas fa-arrow-up me-1"></i>
                                This month
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Farmers & Quick Actions -->
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recently Registered Farmers</h5>
                            <a href="manage-farmers.php" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($registered_farmers)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Farmer</th>
                                                <th>Location</th>
                                                <th>Products</th>
                                                <th>Registered</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($registered_farmers as $farmer): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <?php if (!empty($farmer['image'])): ?>
                                                                <img src="public/uploads/users/<?php echo $farmer['image']; ?>" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                                                            <?php else: ?>
                                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width:40px;height:40px">
                                                                    <i class="fas fa-user text-muted"></i>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div>
                                                                <h6 class="mb-0"><?php echo $farmer['name']; ?></h6>
                                                                <small class="text-muted"><?php echo $farmer['phone']; ?></small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?php echo $farmer['location']; ?></td>
                                                    <td>
                                                        <span class="badge bg-light text-dark">
                                                            <?php echo $farmer['product_count']; ?> products
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('M j, Y', strtotime($farmer['created_at'])); ?></td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="farmer-profile.php?id=<?php echo $farmer['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="add-product.php?farmer_id=<?php echo $farmer['id']; ?>" class="btn btn-sm btn-outline-success">
                                                                <i class="fas fa-plus"></i>
                                                            </a>
                                                            <a href="generate-qr.php?farmer_id=<?php echo $farmer['id']; ?>" class="btn btn-sm btn-outline-dark">
                                                                <i class="fas fa-qrcode"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center p-4">
                                    <img src="public/img/empty-farmers.svg" alt="No farmers" style="max-height: 150px;" class="mb-3">
                                    <h5>No farmers registered yet</h5>
                                    <p class="text-muted">Start by registering farmers using the button above</p>
                                    <a href="farmer-registration.php" class="btn btn-primary">
                                        <i class="fas fa-user-plus me-2"></i>Register First Farmer
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="quick-actions">
                                <a href="farmer-registration.php" class="quick-action-item">
                                    <div class="icon-box bg-primary-light text-primary">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <span>Register Farmer</span>
                                </a>
                                
                                <a href="add-product.php" class="quick-action-item">
                                    <div class="icon-box bg-success-light text-success">
                                        <i class="fas fa-carrot"></i>
                                    </div>
                                    <span>Add Product</span>
                                </a>
                                
                                <a href="generate-qr.php" class="quick-action-item">
                                    <div class="icon-box bg-info-light text-info">
                                        <i class="fas fa-qrcode"></i>
                                    </div>
                                    <span>Generate QR</span>
                                </a>
                                
                                <a href="sync-data.php" class="quick-action-item">
                                    <div class="icon-box bg-warning-light text-warning">
                                        <i class="fas fa-sync"></i>
                                    </div>
                                    <span>Sync Data</span>
                                </a>
                                
                                <a href="pending-orders.php" class="quick-action-item">
                                    <div class="icon-box bg-danger-light text-danger">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <span>Pending Orders</span>
                                </a>
                                
                                <a href="reports.php" class="quick-action-item">
                                    <div class="icon-box bg-dark-light text-dark">
                                        <i class="fas fa-chart-bar"></i>
                                    </div>
                                    <span>Reports</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pending Tasks -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Pending Tasks</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div class="d-flex align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="task1">
                                </div>
                                <label class="ms-2 form-check-label" for="task1">
                                    Complete farmer registration for John Mwanza
                                </label>
                            </div>
                            <span class="badge bg-warning">Today</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div class="d-flex align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="task2">
                                </div>
                                <label class="ms-2 form-check-label" for="task2">
                                    Upload new product photos for Maria's farm
                                </label>
                            </div>
                            <span class="badge bg-danger">Overdue</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div class="d-flex align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="task3">
                                </div>
                                <label class="ms-2 form-check-label" for="task3">
                                    Generate QR codes for 5 farmers
                                </label>
                            </div>
                            <span class="badge bg-info">Tomorrow</span>
                        </li>
                    </ul>
                </div>
                <div class="card-footer bg-white py-3">
                    <button class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus me-1"></i> Add New Task
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS for Agent Dashboard -->
<style>
    .icon-box {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    
    .bg-primary-light {
        background-color: rgba(13, 110, 253, 0.1);
    }
    
    .bg-success-light {
        background-color: rgba(25, 135, 84, 0.1);
    }
    
    .bg-info-light {
        background-color: rgba(13, 202, 240, 0.1);
    }
    
    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1);
    }
    
    .bg-danger-light {
        background-color: rgba(220, 53, 69, 0.1);
    }
    
    .bg-dark-light {
        background-color: rgba(33, 37, 41, 0.1);
    }
    
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .quick-action-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 15px;
        border-radius: 10px;
        background-color: #f8f9fa;
        text-decoration: none;
        color: #212529;
        transition: all 0.2s ease;
    }
    
    .quick-action-item:hover {
        background-color: #e9ecef;
        transform: translateY(-3px);
    }
    
    .quick-action-item .icon-box {
        margin-bottom: 10px;
    }
</style>

<!-- Offline Support Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if browser supports Service Worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('service-worker.js')
        .then(function(registration) {
            console.log('Service Worker registered with scope:', registration.scope);
        })
        .catch(function(error) {
            console.log('Service Worker registration failed:', error);
        });
    }
    
    // Offline mode toggle
    const offlineToggle = document.getElementById('offlineMode');
    
    if (offlineToggle) {
        // Check if offline mode was previously enabled
        const offlineModeEnabled = localStorage.getItem('offlineMode') === 'true';
        offlineToggle.checked = offlineModeEnabled;
        
        offlineToggle.addEventListener('change', function() {
            localStorage.setItem('offlineMode', this.checked);
            
            if (this.checked) {
                // Enable offline mode
                alert('Offline mode enabled. Data will be stored locally and synced when you reconnect.');
            } else {
                // Disable offline mode
                const pendingData = localStorage.getItem('pendingSync');
                
                if (pendingData && pendingData !== '{}') {
                    if (confirm('You have pending data to sync. Would you like to sync now?')) {
                        window.location.href = 'sync-data.php';
                    }
                }
            }
        });
    }
});
</script>

<?php
// Include footer
include 'views/partials/footer.php';
?>
