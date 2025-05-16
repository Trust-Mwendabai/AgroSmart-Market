<?php
// Start the session
session_start();

// Include necessary files
require_once 'config/database.php';
require_once 'config/utils.php';
require_once 'models/Product.php';
require_once 'models/Order.php';
require_once 'models/Message.php';

// Check if user is logged in and is a buyer
if (!isset($_SESSION['user_id']) || !is_buyer()) {
    redirect('auth.php?action=login');
}

// Set page title
$page_title = 'Buyer Dashboard';

// Initialize models
$product_model = new Product($conn);
$order_model = new Order($conn);
$message_model = new Message($conn);

// Get buyer's recent orders using direct SQL query
$sql = "SELECT o.*, p.name as product_name 
        FROM orders o 
        JOIN products p ON o.product_id = p.id 
        WHERE o.buyer_id = ? 
        ORDER BY o.order_date DESC LIMIT 5";
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

// Sample recently viewed products (placeholder for now)
$recently_viewed = [];

// Sample SQL to get random products as recommended items
$sql = "SELECT * FROM products ORDER BY RAND() LIMIT 4";
$result = $conn->query($sql);
$recommended_products = [];
while ($row = $result->fetch_assoc()) {
    $recommended_products[] = $row;
}

// Include header
include 'views/partials/header.php';
?>

<!-- Buyer Dashboard -->
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="fw-bold">Welcome back, <?php echo $_SESSION['user_name']; ?>!</h1>
            <p class="text-muted">Your buyer dashboard at a glance</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="marketplace.php" class="btn btn-primary">
                <i class="fas fa-shopping-basket me-2"></i>Browse Marketplace
            </a>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mb-5">
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-primary text-white me-3">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Orders</h6>
                            <h3 class="mb-0"><?php echo count($recent_orders); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-success text-white me-3">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Available Products</h6>
                            <h3 class="mb-0"><?php echo count($recommended_products); ?>+</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
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
    
    <!-- Recent Orders and Recommended Products -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-6 mb-4">
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
                            <p class="text-muted">You haven't placed any orders yet.</p>
                            <a href="marketplace.php" class="btn btn-primary">Start Shopping</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Recommended Products -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recommended for You</h5>
                        <a href="marketplace.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($recommended_products)): ?>
                        <div class="row g-3">
                            <?php foreach ($recommended_products as $product): ?>
                                <div class="col-md-6">
                                    <div class="product-card-mini">
                                        <div class="d-flex">
                                            <?php if (!empty($product['image'])): ?>
                                                <img src="public/uploads/products/<?php echo $product['image']; ?>" class="rounded me-3" style="width: 70px; height: 70px; object-fit: cover;" alt="<?php echo $product['name']; ?>">
                                            <?php else: ?>
                                                <div class="rounded me-3 bg-light d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                                    <i class="fas fa-seedling text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="mb-1"><?php echo $product['name']; ?></h6>
                                                <p class="text-primary mb-1"><?php echo format_price($product['price']); ?></p>
                                                <small class="text-muted"><?php echo $product['category']; ?></small>
                                            </div>
                                        </div>
                                        <a href="product.php?action=view&id=<?php echo $product['id']; ?>" class="stretched-link"></a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-leaf text-muted fa-3x"></i>
                            </div>
                            <h5>No recommendations yet</h5>
                            <p class="text-muted">Start browsing products to get personalized recommendations.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recently Viewed Products -->
    <div class="row mt-2">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Recently Viewed</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($recently_viewed)): ?>
                        <div class="row">
                            <?php foreach ($recently_viewed as $product): ?>
                                <div class="col-lg-4 mb-3">
                                    <div class="product-card-horizontal">
                                        <div class="d-flex">
                                            <?php if (!empty($product['image'])): ?>
                                                <img src="public/uploads/products/<?php echo $product['image']; ?>" class="rounded me-3" style="width: 100px; height: 100px; object-fit: cover;" alt="<?php echo $product['name']; ?>">
                                            <?php else: ?>
                                                <div class="rounded me-3 bg-light d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                                    <i class="fas fa-seedling text-muted fa-2x"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <h5 class="mb-1"><?php echo $product['name']; ?></h5>
                                                <p class="text-primary mb-1"><?php echo format_price($product['price']); ?></p>
                                                <p class="mb-1"><small class="text-muted"><?php echo $product['category']; ?></small></p>
                                                <a href="product.php?action=view&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary mt-2">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-eye text-muted fa-3x"></i>
                            </div>
                            <h5>No recently viewed products</h5>
                            <p class="text-muted">Products you view will appear here for easy access.</p>
                            <a href="marketplace.php" class="btn btn-primary">Explore Products</a>
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
                            <a href="marketplace.php" class="btn btn-light btn-lg w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="fas fa-store text-primary fa-2x mb-2"></i>
                                <span>Marketplace</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="order.php" class="btn btn-light btn-lg w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="fas fa-clipboard-list text-success fa-2x mb-2"></i>
                                <span>My Orders</span>
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
