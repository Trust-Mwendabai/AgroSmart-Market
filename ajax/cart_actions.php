<?php
// Start session
session_start();

// Include required files
require_once '../config/database.php';
require_once '../config/utils.php';
require_once '../models/Product.php';
require_once '../models/Cart.php';

// Set JSON content type
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => 'An error occurred',
    'cart_count' => 0,
    'cart_items' => [],
    'cart_total' => 0
];

try {
    // Verify request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        throw new Exception('Invalid CSRF token');
    }
    
    // Get action
    $action = $_POST['action'] ?? '';
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);
    
    // Validate product ID
    if ($product_id <= 0 && !in_array($action, ['clear', 'get'])) {
        throw new Exception('Invalid product ID');
    }
    
    // Validate quantity
    if ($quantity <= 0 && !in_array($action, ['remove', 'clear', 'get'])) {
        throw new Exception('Invalid quantity');
    }
    
    // Initialize models
    $conn = require_once '../config/database.php';
    $product_model = new Product($conn);
    $cart = new Cart($conn);
    
    // Process action
    switch ($action) {
        case 'add':
            // Add item to cart
            $result = $cart->add_item($product_id, $quantity);
            if (isset($result['error'])) {
                throw new Exception($result['error']);
            }
            $response['message'] = $result['message'] ?? 'Product added to cart';
            break;
            
        case 'update':
            // Update cart item quantity
            $result = $cart->update_item($product_id, $quantity);
            if (isset($result['error'])) {
                throw new Exception($result['error']);
            }
            $response['message'] = $result['message'] ?? 'Cart updated';
            break;
            
        case 'remove':
            // Remove item from cart
            $result = $cart->remove_item($product_id);
            if (isset($result['error'])) {
                throw new Exception($result['error']);
            }
            $response['message'] = $result['message'] ?? 'Item removed from cart';
            break;
            
        case 'clear':
            // Clear cart
            $result = $cart->clear_cart();
            $response['message'] = $result['message'] ?? 'Cart cleared';
            break;
            
        case 'get':
            // Just get cart data
            $response['message'] = 'Cart data retrieved';
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
    // Get updated cart data
    $cart_data = $cart->get_cart();
    $response['success'] = true;
    $response['cart_count'] = $cart_data['total_quantity'];
    $response['cart_total'] = number_format($cart_data['total_price'], 2);
    $response['cart_items'] = $cart->get_cart_items_with_details();
    
    // Format prices for display
    foreach ($response['cart_items'] as &$item) {
        $item['price_formatted'] = 'K' . number_format($item['price'], 2);
        $item['subtotal_formatted'] = 'K' . number_format($item['subtotal'], 2);
        
        // Ensure image URL is correct
        if (!empty($item['image']) && !filter_var($item['image'], FILTER_VALIDATE_URL)) {
            $item['image'] = '../public/uploads/' . $item['image'];
        } else if (empty($item['image'])) {
            $item['image'] = '../assets/img/placeholder-product.png';
        }
    }
    
} catch (Exception $e) {
    http_response_code(400);
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?>
