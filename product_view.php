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

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id === 0) {
    redirect('marketplace.php');
}

// Get product details
$product = $product_model->get_product($product_id);

if (!$product) {
    redirect('marketplace.php');
}

// Get farmer details
$farmer = $user_model->get_user_by_id($product['farmer_id']);

// Set page title
$page_title = htmlspecialchars($product['name']) . ' - AgroSmart Market';

// Set current page for active navigation highlighting
$current_page = 'marketplace.php';

// Include the header
include 'views/partials/header.php';

// Include the product view template
include 'views/products/view.php';

// Include the footer
include 'views/partials/footer.php';
?>
