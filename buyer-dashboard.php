<?php
// Start the session
session_start();

// Include necessary files
require_once 'config/database.php';
require_once 'config/utils.php';
require_once 'config/languages.php';
require_once 'models/Product.php';
require_once 'models/Order.php';
require_once 'models/Message.php';

// Check if user is logged in and is a buyer
if (!isset($_SESSION['user_id']) || !is_buyer()) {
    redirect('auth.php?action=login');
}

// Set page title
$page_title = __('buyer_dashboard', 'Buyer Dashboard');

// Add enhanced dashboard CSS
$additional_css = '<link rel="stylesheet" href="public/css/enhanced-dashboard.css">';

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

// Include Cart model
require_once 'models/Cart.php';
$cart_model = new Cart($conn);

// Get recently viewed products from session
$recently_viewed = [];
if (isset($_SESSION['recently_viewed']) && is_array($_SESSION['recently_viewed'])) {
    // Get at most 4 recently viewed products
    $recent_product_ids = array_slice($_SESSION['recently_viewed'], 0, 4);
    
    if (!empty($recent_product_ids)) {
        $placeholders = implode(',', array_fill(0, count($recent_product_ids), '?'));
        $sql = "SELECT * FROM products WHERE id IN ($placeholders) ORDER BY FIELD(id, $placeholders)";
        
        $stmt = $conn->prepare($sql);
        $types = str_repeat('i', count($recent_product_ids) * 2);
        $params = array_merge($recent_product_ids, $recent_product_ids);
        
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $recently_viewed[] = $row;
        }
    }
}

// Get frequently bought products
$sql = "SELECT p.*, COUNT(o.id) AS order_count 
        FROM products p 
        JOIN orders o ON p.id = o.product_id 
        WHERE o.buyer_id = ? AND p.stock > 0 
        GROUP BY p.id 
        ORDER BY order_count DESC 
        LIMIT 4";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

$frequent_products = [];
while ($row = $result->fetch_assoc()) {
    $frequent_products[] = $row;
}

// If not enough frequent products, fill with recommendations based on category preferences
if (count($frequent_products) < 4) {
    // Get user's preferred categories based on order history
    $sql = "SELECT p.category, COUNT(o.id) as category_count 
            FROM orders o 
            JOIN products p ON o.product_id = p.id 
            WHERE o.buyer_id = ? 
            GROUP BY p.category 
            ORDER BY category_count DESC 
            LIMIT 3";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $preferred_categories = [];
    while ($row = $result->fetch_assoc()) {
        $preferred_categories[] = $row['category'];
    }
    
    if (!empty($preferred_categories)) {
        // Get product IDs that are already in frequent_products to exclude them
        $exclude_ids = [];
        foreach ($frequent_products as $product) {
            $exclude_ids[] = $product['id'];
        }
        
        $placeholders_categories = implode(',', array_map(function($cat) use ($conn) { 
            return "'" . $conn->real_escape_string($cat) . "'"; 
        }, $preferred_categories));
        
        $placeholders_exclude = !empty($exclude_ids) ? implode(',', $exclude_ids) : '0';
        
        $sql = "SELECT * FROM products 
                WHERE category IN ($placeholders_categories) 
                AND id NOT IN ($placeholders_exclude) 
                AND stock > 0 
                ORDER BY RAND() 
                LIMIT ?";
                
        $limit = 4 - count($frequent_products);
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $frequent_products[] = $row;
        }
    }
}

// If still not enough products, fill with random recommendations
if (count($frequent_products) < 4) {
    $exclude_ids = [];
    foreach ($frequent_products as $product) {
        $exclude_ids[] = $product['id'];
    }
    
    $placeholders_exclude = !empty($exclude_ids) ? implode(',', $exclude_ids) : '0';
    
    $sql = "SELECT * FROM products 
            WHERE id NOT IN ($placeholders_exclude) 
            AND stock > 0 
            ORDER BY RAND() 
            LIMIT ?";
            
    $limit = 4 - count($frequent_products);
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $frequent_products[] = $row;
    }
}

// These are our final recommended products
$recommended_products = $frequent_products;

// Include header
include 'views/partials/header.php';
?>

<!-- Buyer Dashboard -->
<div class="container dashboard-container">
    <!-- Welcome Banner -->
    <div class="welcome-banner p-4 mb-4">
        <div class="row align-items-center">
            <div class="col-md-7 welcome-text">
                <h1 class="fw-bold"><?php echo __('welcome_back', 'Welcome back'); ?>, <?php echo $_SESSION['user_name']; ?>!</h1>
                <p class="text-muted"><?php echo __('dashboard_glance', 'Your dashboard at a glance'); ?></p>
                <div class="d-none d-md-block mt-3">
                    <a href="marketplace.php" class="btn btn-primary">
                        <i class="fas fa-shopping-basket me-2"></i><?php echo __('browse_marketplace', 'Browse Marketplace'); ?>
                    </a>
                </div>
            </div>
            <div class="col-md-5 welcome-image d-none d-md-block">
                <img src="public/img/buyer-welcome.svg" alt="Welcome" class="img-fluid">
            </div>
        </div>
    </div>

    <!-- Connection Status Banner (Only shows when offline) -->
    <div class="offline-alert alert alert-warning mb-4" style="display: none;">
        <div class="d-flex align-items-center">
            <i class="fas fa-wifi-slash me-3 fa-2x"></i>
            <div>
                <h5 class="mb-1">You are currently offline</h5>
                <p class="mb-0">Your actions will be saved locally and synced when you're back online</p>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>

    <!-- Visible only on mobile -->
    <div class="d-block d-md-none mb-4">
        <a href="marketplace.php" class="btn btn-primary w-100">
            <i class="fas fa-shopping-basket me-2"></i><?php echo __('browse_marketplace', 'Browse Marketplace'); ?>
        </a>
    </div>
    
    <!-- Cart Status Bar -->
    <?php
    // Get cart contents
    $cart_contents = $cart_model->get_cart();
    $has_items = !empty($cart_contents['items']);
    $cart_items_count = count($cart_contents['items'] ?? []);
    $cart_total = $cart_contents['total'] ?? 0;
    ?>
    <div class="card border-0 shadow-sm mb-5 cart-status-bar <?php echo !$has_items ? 'cart-empty' : ''; ?>">
        <div class="card-body p-4">
            <div class="cart-status-image">
                <i class="fas <?php echo $has_items ? 'fa-shopping-cart' : 'fa-shopping-basket'; ?>"></i>
            </div>
            <div class="row align-items-center cart-status-content">
                <div class="col-lg-4 mb-3 mb-lg-0">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary text-white p-3 me-3">
                    <?php if ($has_items): ?>
                        <h4 class="mb-1">
                            <span class="d-inline-flex align-items-center">
                                <span class="badge bg-primary rounded-pill me-2"><?php echo $cart_items_count; ?></span>
                                <?php echo $cart_items_count; ?> <?php echo __('items_in_cart', 'items in your cart'); ?>
                            </span>
                        </h4>
                        <p class="mb-0 mt-2">
                            <?php echo __('total', 'Total'); ?>: 
                            <strong class="cart-total">K<?php echo number_format($cart_total, 2); ?></strong>
                        </p>
                    <?php else: ?>
                        <h4 class="mb-1"><?php echo __('cart_empty', 'Your cart is empty'); ?></h4>
                        <p class="mb-0"><?php echo __('start_shopping', 'Start shopping to add products to your cart'); ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-5 text-md-end">
                    <div class="d-flex <?php echo isset($_GET['mobile']) ? 'flex-column' : 'justify-content-md-end'; ?> gap-2 cart-status-actions">
                        <?php if ($has_items): ?>
                            <a href="cart.php" class="btn btn-primary">
                                <i class="fas fa-shopping-cart me-2"></i><?php echo __('view_cart', 'View Cart'); ?>
                            </a>
                            <a href="checkout.php" class="btn btn-success">
                                <i class="fas fa-credit-card me-2"></i><?php echo __('checkout_now', 'Checkout Now'); ?>
                            </a>
                        <?php else: ?>
                            <a href="marketplace.php" class="btn btn-primary">
                                <i class="fas fa-shopping-basket me-2"></i><?php echo __('browse_products', 'Browse Products'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <!-- Stats Cards -->
    <div class="row mb-5">
        <div class="col-md-3 col-6 mb-4">
            <div class="stat-card h-100 border-0 shadow-sm">
                <div class="stat-icon bg-orders"><i class="fas fa-shopping-bag"></i></div>
                <h6 class="stat-title"><?php echo __('total_orders', 'Total Orders'); ?></h6>
                <?php
                // Count total orders
                $sql = "SELECT COUNT(*) as count FROM orders WHERE buyer_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $order_count = $result->fetch_assoc()['count'];
                ?>
                <h3 class="stat-value"><?php echo $order_count; ?></h3>
                <i class="fas fa-shopping-bag stat-bg-icon"></i>
            </div>
        </div>
        
        <div class="col-md-3 col-6 mb-4">
            <div class="stat-card h-100 border-0 shadow-sm">
                <div class="stat-icon bg-messages"><i class="fas fa-envelope"></i></div>
                <h6 class="stat-title"><?php echo __('unread_messages', 'Unread Messages'); ?></h6>
                <h3 class="stat-value"><?php echo $unread_count; ?></h3>
                <i class="fas fa-envelope stat-bg-icon"></i>
            </div>
        </div>
        
        <div class="col-md-3 col-6 mb-4">
            <div class="stat-card h-100 border-0 shadow-sm">
                <div class="stat-icon bg-cart"><i class="fas fa-shopping-cart"></i></div>
                <h6 class="stat-title"><?php echo __('cart_items', 'Cart Items'); ?></h6>
                <h3 class="stat-value"><?php echo $cart_items_count; ?></h3>
                <i class="fas fa-shopping-cart stat-bg-icon"></i>
            </div>
        </div>

        <!-- Available Products -->
        <div class="col-md-3 col-6 mb-4">
            <div class="stat-card h-100 border-0 shadow-sm">
                <div class="stat-icon bg-success-light text-success"><i class="fas fa-tag"></i></div>
                <h6 class="stat-title"><?php echo __('available_products', 'Available Products'); ?></h6>
                <h3 class="stat-value"><?php echo count($recommended_products); ?>+</h3>
                <i class="fas fa-tag stat-bg-icon"></i>
            </div>
        </div>
    </div>
    
    <!-- Recent Orders and Recommended Products -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-6 mb-4">
            <div class="dashboard-card h-100">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title"><i class="fas fa-shopping-bag text-primary me-2"></i><?php echo __('recent_orders', 'Recent Orders'); ?></h5>
                        <a href="order.php" class="btn btn-sm btn-outline-primary"><?php echo __('view_all', 'View All'); ?></a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($recent_orders)): ?>
                        <div class="order-list">
                            <?php foreach ($recent_orders as $order): 
                                // Get product image if exists
                                $product_image = 'public/img/default-product.jpg';
                                $sql = "SELECT image FROM products WHERE id = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $order['product_id']);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                if ($row = $result->fetch_assoc()) {
                                    if (!empty($row['image'])) {
                                        $product_image = 'public/uploads/products/' . $row['image'];
                                    }
                                }
                            ?>
                                <div class="order-item">
                                    <div class="order-image">
                                        <img src="<?php echo $product_image; ?>" alt="<?php echo htmlspecialchars($order['product_name']); ?>" onerror="this.src='public/img/default-product.jpg'">
                                    </div>
                                    <div class="order-content">
                                        <h6 class="order-title"><?php echo htmlspecialchars($order['product_name']); ?></h6>
                                        <div class="order-meta">
                                            <span class="order-date">
                                                <i class="far fa-calendar-alt me-1"></i>
                                                <?php echo date('M j, Y', strtotime($order['order_date'])); ?>
                                            </span>
                                            <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                                <?php echo __('status_' . strtolower($order['status']), ucfirst($order['status'])); ?>
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <span class="fw-bold">
                                                <?php echo $order['quantity']; ?> × K<?php echo number_format($order['price_per_unit'], 2); ?>
                                            </span>
                                            <a href="order.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i> <?php echo __('view_details', 'View'); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state p-4">
                            <div class="empty-icon mb-3">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                            <h5><?php echo __('no_orders', 'No orders yet'); ?></h5>
                            <p class="text-muted"><?php echo __('place_order', 'Place your first order'); ?></p>
                            <a href="marketplace.php" class="btn btn-primary">
                                <i class="fas fa-shopping-basket me-2"></i><?php echo __('browse_marketplace', 'Browse Marketplace'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Recommended Products -->
        <div class="col-lg-6 mb-4">
            <div class="dashboard-card h-100">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title"><i class="fas fa-thumbs-up text-primary me-2"></i><?php echo __('quick_order', 'Quick Order Favorites'); ?></h5>
                        <a href="marketplace.php" class="btn btn-sm btn-outline-primary"><?php echo __('view_all', 'View All'); ?></a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($recommended_products)): ?>
                        <div class="product-scroll">
                            <div class="product-scroll-inner">
                                <?php foreach ($recommended_products as $product): ?>
                                    <div class="product-card">
                                        <?php include 'views/partials/product-card-simple.php'; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <h5><?php echo __('no_recommendations', 'No recommended products'); ?></h5>
                            <p class="text-muted"><?php echo __('recommendations_info', 'Browse and order products to see recommendations'); ?></p>
                            <a href="marketplace.php" class="btn btn-primary">
                                <i class="fas fa-shopping-basket me-2"></i><?php echo __('browse_marketplace', 'Browse Marketplace'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recently Viewed Products -->
    <div class="row mt-4">
        <div class="col-12 mb-4">
            <div class="dashboard-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title"><i class="fas fa-history text-primary me-2"></i><?php echo __('recently_viewed', 'Recently Viewed Products'); ?></h5>
                        <a href="marketplace.php" class="btn btn-sm btn-outline-primary"><?php echo __('view_all', 'View All'); ?></a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($recently_viewed)): ?>
                        <div class="product-scroll">
                            <div class="product-scroll-inner">
                                <?php foreach ($recently_viewed as $product): ?>
                                    <div class="product-card">
                                        <?php include 'views/partials/product-card-simple.php'; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <h5><?php echo __('no_recent_products', 'No recently viewed products'); ?></h5>
                            <p class="text-muted"><?php echo __('recent_products_info', 'Products you view will appear here'); ?></p>
                            <a href="marketplace.php" class="btn btn-primary">
                                <i class="fas fa-shopping-basket me-2"></i><?php echo __('browse_marketplace', 'Browse Marketplace'); ?>
                            </a>
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
