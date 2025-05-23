<?php
// Start session
session_start();

// Set content type to JSON
header('Content-Type: application/json');

// Include database connection and utilities
$conn = require_once '../config/database.php';
require_once '../config/utils.php';

// Include models
require_once '../models/Product.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => 'Invalid request',
    'product' => null
];

// Check if product ID is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = (int) $_GET['id'];
    
    // Initialize Product model
    $product_model = new Product($conn);
    
    // Get product details
    $product = $product_model->get_product($product_id);
    
    if ($product) {
        $response['success'] = true;
        $response['message'] = 'Product retrieved successfully';
        $response['product'] = $product;
    } else {
        $response['message'] = 'Product not found';
    }
} else {
    $response['message'] = 'Invalid product ID';
}

// Output JSON response
echo json_encode($response);
exit;
