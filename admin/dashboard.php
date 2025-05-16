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

// Set page title
$page_title = "Admin Dashboard - AgroSmart Market";

// Include admin header
include '../views/admin/partials/header.php';

// Include dashboard view
include '../views/admin/dashboard.php';

// Include admin footer
include '../views/admin/partials/footer.php';
?>
