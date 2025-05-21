<?php
// Start session
session_start();

// Include database connection and utilities
$conn = require_once '../config/database.php';
require_once '../config/utils.php';

// Check if user is logged in and is admin
if (!is_admin()) {
    redirect("../controllers/auth.php?action=login");
}

// Include models
require_once '../models/User.php';
require_once '../models/Product.php';
require_once '../models/Order.php';

// Initialize models
$user_model = new User($conn);
$product_model = new Product($conn);
$order_model = new Order($conn);

// Get summary statistics
$user_counts = $user_model->count_users_by_type();
$total_products = $product_model->count_products();
$order_counts = $order_model->count_orders_by_status();

// Get statistics
$stats = [
    'total_users' => 0,
    'total_farmers' => 0,
    'total_products' => 0,
    'total_orders' => 0,
    'recent_orders' => [],
    'recent_users' => []
];

// Get total users
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
$stats['total_users'] = $result->fetch_assoc()['count'];

// Get total farmers
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'farmer'");
$stats['total_farmers'] = $result->fetch_assoc()['count'];

// Get total products
$result = $conn->query("SELECT COUNT(*) as count FROM products");
$stats['total_products'] = $result->fetch_assoc()['count'];

// Get total orders
$result = $conn->query("SELECT COUNT(*) as count FROM orders");
$stats['total_orders'] = $result->fetch_assoc()['count'];

// Get recent orders
$result = $conn->query("
    SELECT o.*, u.name as user_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC 
    LIMIT 5
");
$stats['recent_orders'] = $result->fetch_all(MYSQLI_ASSOC);

// Get recent users
$result = $conn->query("
    SELECT * FROM users 
    ORDER BY created_at DESC 
    LIMIT 5
");
$stats['recent_users'] = $result->fetch_all(MYSQLI_ASSOC);

// Set page title
$page_title = "Admin Dashboard - AgroSmart Market";

// Include admin header
include '../views/admin/partials/header.php';

// Include dashboard view
include '../views/admin/dashboard.php';

// Include admin footer
include '../views/admin/partials/footer.php';
?>
