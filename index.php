<?php
// Start session
session_start();

// Include database connection
$conn = require_once 'config/database.php';
require_once 'config/utils.php';

// Include models
require_once 'models/User.php';
require_once 'models/Product.php';

// Initialize models
$user_model = new User($conn);
$product_model = new Product($conn);

// Get latest products for homepage
$filters = [];
$latest_products = $product_model->get_all_products(8, 0, $filters);

// Get categories for filter sidebar
$categories = $product_model->get_categories();

// Include the header
$page_title = "AgroSmart Market - Connect Farmers & Buyers";
include 'views/partials/header.php';

// Include the home page content
include 'views/home.php';

// Include the footer
include 'views/partials/footer.php';
?>
