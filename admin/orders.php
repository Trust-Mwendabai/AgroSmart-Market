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

// Include models
require_once '../models/User.php';
require_once '../models/Order.php';
require_once '../models/Product.php';

// Initialize models
$user_model = new User($conn);
$order_model = new Order($conn);
$product_model = new Product($conn);

// Handle order status update
if (isset($_POST['update_status']) && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    
    if ($order_model->update_status($order_id, $status, $_SESSION['user_id'], false)) {
        $_SESSION['success_message'] = "Order status has been updated to " . ucfirst($status) . ".";
    } else {
        $_SESSION['error_message'] = "Failed to update order status. Please try again.";
    }
    header('Location: orders.php' . (isset($_GET['id']) ? '?id=' . $_GET['id'] : ''));
    exit();
}

// Check if viewing a specific order
$order_detail = null;
if (isset($_GET['id'])) {
    $order_id = $_GET['id'];
    $order_detail = $order_model->get_order($order_id);
    
    if (!$order_detail) {
        $_SESSION['error_message'] = "Order not found.";
        header('Location: orders.php');
        exit();
    }
    
    // Get buyer and farmer details
    $buyer = $user_model->get_user_by_id($order_detail['buyer_id']);
    $farmer = $user_model->get_user_by_id($order_detail['farmer_id']);
    
    // Get product details
    $product = $product_model->get_product($order_detail['product_id']);
}

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$buyer_id = isset($_GET['buyer_id']) ? $_GET['buyer_id'] : '';
$farmer_id = isset($_GET['farmer_id']) ? $_GET['farmer_id'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Get all orders with optional filters
// Since we don't have the getAllForAdmin method in the Order model yet, 
// we'll create a custom query here for now
$query = "SELECT o.*, 
          b.name as buyer_name, 
          f.name as farmer_name,
          p.name as product_name,
          p.price as product_price
          FROM orders o
          JOIN users b ON o.buyer_id = b.id
          JOIN users f ON o.farmer_id = f.id
          JOIN products p ON o.product_id = p.id
          WHERE 1=1";

// Add filters if they exist
if (!empty($status)) {
    $query .= " AND o.status = '" . mysqli_real_escape_string($conn, $status) . "'";
}
if (!empty($buyer_id)) {
    $query .= " AND o.buyer_id = " . (int)$buyer_id;
}
if (!empty($farmer_id)) {
    $query .= " AND o.farmer_id = " . (int)$farmer_id;
}
if (!empty($date_from)) {
    $query .= " AND o.date_ordered >= '" . mysqli_real_escape_string($conn, $date_from) . "'";
}
if (!empty($date_to)) {
    $query .= " AND o.date_ordered <= '" . mysqli_real_escape_string($conn, $date_to) . " 23:59:59'";
}

$query .= " ORDER BY o.date_ordered DESC";
$result = mysqli_query($conn, $query);
$orders = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
}

// Get buyers for filter dropdown
$query = "SELECT DISTINCT u.id, u.name FROM users u JOIN orders o ON u.id = o.buyer_id WHERE u.user_type = 'buyer' ORDER BY u.name";
$result = mysqli_query($conn, $query);
$buyers = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $buyers[] = $row;
    }
}

// Get farmers for filter dropdown
$query = "SELECT DISTINCT u.id, u.name FROM users u JOIN orders o ON u.id = o.farmer_id WHERE u.user_type = 'farmer' ORDER BY u.name";
$result = mysqli_query($conn, $query);
$farmers = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $farmers[] = $row;
    }
}

// Set page title
$page_title = isset($order_detail) ? "Order #" . $order_detail['id'] . " - AgroSmart Market" : "Manage Orders - AgroSmart Market";

// Include admin header
include '../views/admin/partials/header.php';
?>

<div class="container-fluid">
    <?php if (isset($order_detail)): ?>
        <!-- Order Detail View -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Order #<?php echo $order_detail['id']; ?></h1>
            <a href="orders.php" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to All Orders
            </a>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Order Information Card -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Order Information</h6>
                        <span class="badge bg-<?php 
                            switch($order_detail['status']) {
                                case 'pending': echo 'warning'; break;
                                case 'confirmed': echo 'primary'; break;
                                case 'shipped': echo 'info'; break;
                                case 'delivered': echo 'success'; break;
                                case 'cancelled': echo 'danger'; break;
                                default: echo 'secondary';
                            }
                        ?>">
                            <?php echo ucfirst($order_detail['status']); ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Order Details</h5>
                                <p><strong>Order Date:</strong> <?php echo date('F d, Y H:i', strtotime($order_detail['date_ordered'])); ?></p>
                                <p><strong>Quantity:</strong> <?php echo $order_detail['quantity']; ?> <?php echo $product['unit']; ?></p>
                                <p><strong>Unit Price:</strong> <?php echo format_currency($product['price']); ?></p>
                                <p><strong>Total Amount:</strong> <?php echo format_currency($order_detail['quantity'] * $product['price']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h5>Product Information</h5>
                                <p><strong>Product:</strong> <?php echo htmlspecialchars($product['name']); ?></p>
                                <p><strong>Category:</strong> <?php echo ucfirst($product['category']); ?></p>
                                <?php if (!empty($product['image_url'])): ?>
                                    <img src="<?php echo '../' . $product['image_url']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-thumbnail" style="max-height: 100px;">
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Buyer Information</h5>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($buyer['name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($buyer['email']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($buyer['phone'] ?? 'N/A'); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h5>Farmer Information</h5>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($farmer['name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($farmer['email']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($farmer['phone'] ?? 'N/A'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Notes -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Order Notes</h6>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($order_detail['notes'])): ?>
                            <p><?php echo nl2br(htmlspecialchars($order_detail['notes'])); ?></p>
                        <?php else: ?>
                            <p class="text-muted">No notes available for this order.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Order Management Card -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Order Management</h6>
                    </div>
                    <div class="card-body">
                        <form method="post" action="">
                            <input type="hidden" name="order_id" value="<?php echo $order_detail['id']; ?>">
                            
                            <div class="mb-3">
                                <label for="status" class="form-label">Update Order Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="pending" <?php echo ($order_detail['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo ($order_detail['status'] == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="shipped" <?php echo ($order_detail['status'] == 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo ($order_detail['status'] == 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo ($order_detail['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            
                            <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                        </form>
                        
                        <hr>
                        
                        <div class="timeline small">
                            <h6>Order Timeline</h6>
                            <?php
                            // For now, we'll show a simple timeline based on the current status
                            $statuses = ['pending', 'confirmed', 'shipped', 'delivered'];
                            $current_index = array_search($order_detail['status'], $statuses);
                            
                            foreach ($statuses as $index => $timeline_status):
                                $completed = $index <= $current_index && $order_detail['status'] != 'cancelled';
                                $current = $index == $current_index && $order_detail['status'] != 'cancelled';
                            ?>
                                <div class="timeline-item <?php echo $completed ? 'completed' : ''; ?> <?php echo $current ? 'current' : ''; ?>">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <h3 class="timeline-title"><?php echo ucfirst($timeline_status); ?></h3>
                                        <?php if ($completed): ?>
                                            <p class="timeline-date">
                                                <?php 
                                                // In a real implementation, you would store timestamps for each status change
                                                if ($current) {
                                                    echo date('M d, Y', strtotime($order_detail['date_ordered']));
                                                } else {
                                                    echo date('M d, Y', strtotime($order_detail['date_ordered'] . ' + ' . $index . ' days'));
                                                }
                                                ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php if ($order_detail['status'] == 'cancelled'): ?>
                                <div class="timeline-item cancelled current">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <h3 class="timeline-title">Cancelled</h3>
                                        <p class="timeline-date"><?php echo date('M d, Y', strtotime($order_detail['date_ordered'])); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Orders List View -->
        <h1 class="h3 mb-2 text-gray-800">Manage Orders</h1>
        <p class="mb-4">View and manage all orders placed on AgroSmart Market.</p>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <!-- Filters Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Filter Orders</h6>
            </div>
            <div class="card-body">
                <form method="get" action="" class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="pending" <?php echo ($status == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="confirmed" <?php echo ($status == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="shipped" <?php echo ($status == 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                            <option value="delivered" <?php echo ($status == 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo ($status == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="buyer_id" class="form-label">Buyer</label>
                        <select name="buyer_id" id="buyer_id" class="form-select">
                            <option value="">All Buyers</option>
                            <?php foreach ($buyers as $buyer): ?>
                                <option value="<?php echo $buyer['id']; ?>" <?php echo ($buyer_id == $buyer['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($buyer['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="farmer_id" class="form-label">Farmer</label>
                        <select name="farmer_id" id="farmer_id" class="form-select">
                            <option value="">All Farmers</option>
                            <?php foreach ($farmers as $farmer): ?>
                                <option value="<?php echo $farmer['id']; ?>" <?php echo ($farmer_id == $farmer['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($farmer['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo $date_from; ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo $date_to; ?>">
                    </div>
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="orders.php" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Orders Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Order Listings</h6>
                <div>
                    <span class="badge bg-primary"><?php echo count($orders); ?> Orders</span>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($orders)): ?>
                    <div class="text-center p-4">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p>No orders found matching your criteria.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="ordersTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Buyer</th>
                                    <th>Farmer</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?php echo $order['id']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($order['date_ordered'])); ?></td>
                                        <td><?php echo htmlspecialchars($order['buyer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($order['farmer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                                        <td><?php echo $order['quantity'] . ' ' . ($order['product_unit'] ?? ''); ?></td>
                                        <td><?php echo format_currency($order['quantity'] * $order['product_price']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                switch($order['status']) {
                                                    case 'pending': echo 'warning'; break;
                                                    case 'confirmed': echo 'primary'; break;
                                                    case 'shipped': echo 'info'; break;
                                                    case 'delivered': echo 'success'; break;
                                                    case 'cancelled': echo 'danger'; break;
                                                    default: echo 'secondary';
                                                }
                                            ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="orders.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-cog"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <?php if ($order['status'] == 'pending'): ?>
                                                        <li>
                                                            <form method="post" action="">
                                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                                <input type="hidden" name="status" value="confirmed">
                                                                <button type="submit" name="update_status" class="dropdown-item">
                                                                    <i class="fas fa-check text-success"></i> Confirm
                                                                </button>
                                                            </form>
                                                        </li>
                                                    <?php endif; ?>
                                                    <?php if ($order['status'] == 'confirmed'): ?>
                                                        <li>
                                                            <form method="post" action="">
                                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                                <input type="hidden" name="status" value="shipped">
                                                                <button type="submit" name="update_status" class="dropdown-item">
                                                                    <i class="fas fa-truck text-info"></i> Mark as Shipped
                                                                </button>
                                                            </form>
                                                        </li>
                                                    <?php endif; ?>
                                                    <?php if ($order['status'] == 'shipped'): ?>
                                                        <li>
                                                            <form method="post" action="">
                                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                                <input type="hidden" name="status" value="delivered">
                                                                <button type="submit" name="update_status" class="dropdown-item">
                                                                    <i class="fas fa-check-circle text-success"></i> Mark as Delivered
                                                                </button>
                                                            </form>
                                                        </li>
                                                    <?php endif; ?>
                                                    <?php if ($order['status'] != 'cancelled' && $order['status'] != 'delivered'): ?>
                                                        <li>
                                                            <form method="post" action="">
                                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                                <input type="hidden" name="status" value="cancelled">
                                                                <button type="submit" name="update_status" class="dropdown-item">
                                                                    <i class="fas fa-times-circle text-danger"></i> Cancel
                                                                </button>
                                                            </form>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
/* Timeline Styling */
.timeline {
    position: relative;
    padding: 20px 0;
    list-style: none;
    max-width: 100%;
}

.timeline:before {
    content: " ";
    position: absolute;
    top: 0;
    bottom: 0;
    left: 20px;
    width: 3px;
    background-color: #eee;
}

.timeline-item {
    position: relative;
    margin-bottom: 25px;
    padding-left: 40px;
}

.timeline-marker {
    position: absolute;
    top: 5px;
    left: 15px;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    border: 3px solid #eee;
    background-color: white;
    z-index: 1;
}

.timeline-item.completed .timeline-marker {
    background-color: #4CAF50;
    border-color: #4CAF50;
}

.timeline-item.current .timeline-marker {
    border-color: #4CAF50;
}

.timeline-item.cancelled .timeline-marker {
    background-color: #f44336;
    border-color: #f44336;
}

.timeline-content {
    padding-bottom: 10px;
}

.timeline-title {
    margin: 0;
    font-size: 0.9rem;
    font-weight: bold;
}

.timeline-date {
    margin: 0;
    font-size: 0.75rem;
    color: #6c757d;
}
</style>

<script>
    // Initialize DataTable for better user experience
    $(document).ready(function() {
        $('#ordersTable').DataTable({
            "order": [[ 1, "desc" ]], // Sort by date by default
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]
        });
    });
</script>

<?php
// Include admin footer
include '../views/admin/partials/footer.php';
?>
