<?php
/**
 * API Endpoint: Get Product Details
 * 
 * Returns detailed information about a specific product for quick view functionality
 */

// Start session
session_start();

// Include database connection
$conn = require_once '../config/database.php';
require_once '../config/utils.php';

// Include models
require_once '../models/Product.php';

// Set content type to JSON
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'product' => null
];

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $response['message'] = 'Product ID is required';
    echo json_encode($response);
    exit;
}

// Sanitize input
$product_id = (int) sanitize_input($_GET['id']);

// Initialize product model
$product_model = new Product($conn);

// Get product details
$product = $product_model->get_product($product_id);

if (!$product) {
    $response['message'] = 'Product not found';
    echo json_encode($response);
    exit;
}

// Track this product in recently viewed items
if (!isset($_SESSION['recently_viewed'])) {
    $_SESSION['recently_viewed'] = [];
}

// Remove product if already in the list
$_SESSION['recently_viewed'] = array_values(array_filter($_SESSION['recently_viewed'], function($id) use ($product_id) {
    return $id != $product_id;
}));

// Add product to the beginning of the array
array_unshift($_SESSION['recently_viewed'], $product_id);

// Keep only the most recent 10 products
if (count($_SESSION['recently_viewed']) > 10) {
    $_SESSION['recently_viewed'] = array_slice($_SESSION['recently_viewed'], 0, 10);
}

// Prepare response
$response['success'] = true;
$response['product'] = $product;

// Return JSON response
echo json_encode($response);
?>
