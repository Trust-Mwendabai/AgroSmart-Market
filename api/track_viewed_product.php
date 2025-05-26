<?php
/**
 * API Endpoint: Track Viewed Product
 * 
 * Records a product view in the user's session for the "Recently Viewed Products" feature
 */

// Start session
session_start();

// Include utilities
require_once '../config/utils.php';

// Set content type to JSON
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => ''
];

// Get JSON input
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Check if product ID is provided
if (!isset($data['product_id']) || empty($data['product_id'])) {
    $response['message'] = 'Product ID is required';
    echo json_encode($response);
    exit;
}

// Sanitize input
$product_id = (int) sanitize_input($data['product_id']);

// Initialize recently viewed array if it doesn't exist
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

// Set success response
$response['success'] = true;
$response['message'] = 'Product view tracked successfully';

// Return JSON response
echo json_encode($response);
?>
