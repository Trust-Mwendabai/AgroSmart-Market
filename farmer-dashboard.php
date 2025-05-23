<?php
// Start the session
session_start();

// Include necessary files
require_once 'config/database.php';
require_once 'config/utils.php';
require_once 'models/Product.php';
require_once 'models/Order.php';
require_once 'models/Message.php';

// Check if user is logged in and is a farmer
if (!isset($_SESSION['user_id']) || !is_farmer()) {
    redirect('auth.php?action=login');
}

// Set page title
$page_title = 'Farmer Dashboard';

// Initialize models
$product_model = new Product($conn);
$order_model = new Order($conn);
$message_model = new Message($conn);

// Get farmer's products
// Using a direct SQL query since the method doesn't exist
$sql = "SELECT * FROM products WHERE farmer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Get recent orders for farmer's products
// Using a direct SQL query since the method doesn't exist
$sql = "SELECT o.*, p.name as product_name, (o.quantity * p.price) as total_price 
        FROM orders o 
        JOIN products p ON o.product_id = p.id 
        WHERE p.farmer_id = ? 
        ORDER BY o.date_ordered DESC LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$recent_orders = [];
while ($row = $result->fetch_assoc()) {
    $recent_orders[] = $row;
}

// Get unread messages count
$unread_count = $message_model->count_unread_messages($_SESSION['user_id']);

// Get product analytics (you can expand this functionality)
$total_products = count($products);
$active_products = 0;
$out_of_stock = 0;
$total_stock = 0;

foreach ($products as $product) {
    $total_stock += $product['stock'];
    if ($product['stock'] > 0) {
        $active_products++;
    } else {
        $out_of_stock++;
    }
}

// Include header
include 'views/partials/header.php';
?>

<!-- Farmer Dashboard -->
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="fw-bold">Welcome back, <?php echo $_SESSION['user_name']; ?>!</h1>
            <p class="text-muted">Your farmer dashboard at a glance</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="product.php?action=add" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Add New Product
            </a>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mb-5">
        <div class="col-md-3 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-primary text-white me-3">
                            <i class="fas fa-box"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Products</h6>
                            <h3 class="mb-0"><?php echo $total_products; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-success text-white me-3">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Active Products</h6>
                            <h3 class="mb-0"><?php echo $active_products; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-warning text-white me-3">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Out of Stock</h6>
                            <h3 class="mb-0"><?php echo $out_of_stock; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-info text-white me-3">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Unread Messages</h6>
                            <h3 class="mb-0"><?php echo $unread_count; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Products and Orders Section -->
    <div class="row">
        <!-- Products Summary -->
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Your Products</h5>
                        <a href="product.php?action=manage" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($products)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Limit to showing just 5 products
                                    $counter = 0;
                                    foreach ($products as $product): 
                                        if ($counter >= 5) break;
                                        $counter++;
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($product['image'])): ?>
                                                        <img src="public/uploads/products/<?php echo $product['image']; ?>" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;" alt="<?php echo $product['name']; ?>">
                                                    <?php else: ?>
                                                        <div class="rounded me-2 bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                            <i class="fas fa-seedling text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <span><?php echo $product['name']; ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo $product['category']; ?></td>
                                            <td><?php echo format_price($product['price']); ?></td>
                                            <td><?php echo $product['stock']; ?></td>
                                            <td>
                                                <?php if ($product['stock'] > 0): ?>
                                                    <span class="badge bg-success">In Stock</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Out of Stock</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-box-open text-muted fa-3x"></i>
                            </div>
                            <h5>No products yet</h5>
                            <p class="text-muted">You haven't added any products to sell.</p>
                            <a href="product.php?action=add" class="btn btn-primary">Add Your First Product</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Orders</h5>
                        <a href="order.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($recent_orders)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($recent_orders as $order): ?>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-0"><strong>Order #<?php echo $order['id']; ?></strong></p>
                                            <small class="text-muted"><?php echo $order['product_name']; ?> &times; <?php echo $order['quantity']; ?></small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-<?php echo get_order_status_color($order['status']); ?>"><?php echo ucfirst($order['status']); ?></span>
                                            <p class="mb-0 mt-1"><?php echo format_price($order['total_price']); ?></p>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-shopping-cart text-muted fa-3x"></i>
                            </div>
                            <h5>No orders yet</h5>
                            <p class="text-muted">You haven't received any orders yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Links -->
    <div class="row mt-2">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Quick Links</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="product.php?action=add" class="btn btn-light btn-lg w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="fas fa-plus-circle text-success fa-2x mb-2"></i>
                                <span>Add Product</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="order.php" class="btn btn-light btn-lg w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="fas fa-clipboard-list text-primary fa-2x mb-2"></i>
                                <span>Manage Orders</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="message.php" class="btn btn-light btn-lg w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="fas fa-envelope text-info fa-2x mb-2"></i>
                                <span>Messages</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="profile.php" class="btn btn-light btn-lg w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="fas fa-user-cog text-secondary fa-2x mb-2"></i>
                                <span>Profile Settings</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Helper function for order status color -->
<?php 
function get_order_status_color($status) {
    switch ($status) {
        case 'pending':
            return 'warning';
        case 'processing':
            return 'info';
        case 'shipped':
            return 'primary';
        case 'delivered':
            return 'success';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}
?>

<?php
// Include footer
include 'views/partials/footer.php';
?>
