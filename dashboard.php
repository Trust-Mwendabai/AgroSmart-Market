<?php
// Start session
session_start();

// Include database connection and utilities
$conn = require_once 'config/database.php';
require_once 'config/utils.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect("controllers/auth.php?action=login");
}

// Include models
require_once 'models/User.php';
require_once 'models/Product.php';
require_once 'models/Order.php';
require_once 'models/Message.php';

// Initialize models
$user_model = new User($conn);
$product_model = new Product($conn);
$order_model = new Order($conn);
$message_model = new Message($conn);

// Get user data
$user_id = $_SESSION['user_id'];
$user = $user_model->get_user($user_id);

// Get user-specific data based on user type
if (is_farmer()) {
    // Get farmer's products
    $products = $product_model->get_farmer_products($user_id);
    
    // Get farmer's orders
    $orders = $order_model->get_farmer_orders($user_id);
} else {
    // Get buyer's orders
    $orders = $order_model->get_buyer_orders($user_id);
}

// Get unread messages count
$unread_messages = $message_model->count_unread_messages($user_id);

// Set page title
$page_title = "Dashboard - AgroSmart Market";

// Include header
include 'views/partials/header.php';

// Include dashboard view based on user type
if (is_farmer()) {
    include 'views/dashboard/farmer.php';
} else {
    include 'views/dashboard/buyer.php';
}

// Include footer
include 'views/partials/footer.php';
?>
