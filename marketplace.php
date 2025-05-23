<?php
// Start session
session_start();

// Include database connection and utilities
$conn = require_once 'config/database.php';
require_once 'config/utils.php';

// Include language support
require_once 'config/languages.php';

// Include models
require_once 'models/Product.php';

// Initialize models
$product_model = new Product($conn);

// Pagination settings
$limit = 12; // Products per page
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Build filters from GET parameters
$filters = [];

if (isset($_GET['category']) && !empty($_GET['category'])) {
    $filters['category'] = sanitize_input($_GET['category']);
}

if (isset($_GET['location']) && !empty($_GET['location'])) {
    $filters['location'] = sanitize_input($_GET['location']);
}

if (isset($_GET['min_price']) && $_GET['min_price'] !== '') {
    $filters['min_price'] = (float) $_GET['min_price'];
}

if (isset($_GET['max_price']) && $_GET['max_price'] !== '') {
    $filters['max_price'] = (float) $_GET['max_price'];
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $filters['search'] = sanitize_input($_GET['search']);
}

// Get products with filters
$products = $product_model->get_all_products($limit, $offset, $filters);

// Get categories for filter sidebar
$categories = $product_model->get_categories();

// Set page title
$page_title = __('marketplace', 'Marketplace') . " - AgroSmart Market";

// Add enhanced marketplace CSS
$additional_css = '<link rel="stylesheet" href="public/css/enhanced-marketplace.css">
<link rel="stylesheet" href="public/css/marketplace-custom.css">'; // Added custom CSS to remove diagonal bar

// Add marketplace JavaScript
$additional_js = '<script src="public/js/marketplace.js"></script>';

// Include header
include 'views/partials/header.php';

// Include marketplace view
include 'views/marketplace.php';

// Include footer
include 'views/partials/footer.php';
?>
