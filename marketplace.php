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

// Set default values for pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;
$offset = ($page - 1) * $limit;

// Build filters from GET parameters
$filters = [];
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $filters['category'] = $_GET['category'];
}
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $filters['search'] = $_GET['search'];
}
if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
    $filters['min_price'] = (float)$_GET['min_price'];
}
if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
    $filters['max_price'] = (float)$_GET['max_price'];
}
if (isset($_GET['location']) && !empty($_GET['location'])) {
    $filters['location'] = $_GET['location'];
}

// Get products with pagination and filters
$products = $product_model->get_all_products($limit, $offset, $filters);
$total_products = $product_model->count_products($filters);
$total_pages = ceil($total_products / $limit);

// Get categories for filter sidebar
$categories = $product_model->get_categories();

// Locations will be handled directly in the view if needed

// Include the header
$page_title = "Marketplace - AgroSmart Market";
$current_page = 'marketplace.php';
include 'views/partials/header.php';

// Include the marketplace view
include 'views/marketplace.php';

// Include the footer
include 'views/partials/footer.php';
?>
