<?php
// Start the session
session_start();

// Include necessary files
require_once 'config/database.php';
require_once 'config/utils.php';
require_once 'models/Order.php';
require_once 'models/User.php';
require_once 'models/Product.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect('auth.php?action=login');
}

// Initialize models
$order_model = new Order($conn);
$product_model = new Product($conn);
$user_model = new User($conn);

// Get action from URL
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$error = '';
$success = '';

switch ($action) {
    case 'create':
        // Only buyers can create orders
        if (!is_buyer()) {
            redirect("AgroSmart Market/dashboard.php");
        }
        
        $product_id = isset($_GET['product_id']) ? (int) $_GET['product_id'] : 0;
        
        if ($product_id === 0) {
            redirect("AgroSmart Market/marketplace.php");
        }
        
        // Get product data
        $product = $product_model->get_product($product_id);
        
        // Check if product exists
        if (!$product) {
            redirect("AgroSmart Market/marketplace.php");
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF token
            if (!verify_csrf_token($_POST['csrf_token'])) {
                $error = "Invalid request";
                break;
            }
            
            // Validate and sanitize input
            $quantity = (int) $_POST['quantity'];
            $buyer_id = $_SESSION['user_id'];
            $farmer_id = $product['farmer_id'];
            
            // Check for valid quantity
            if ($quantity <= 0) {
                $error = "Please enter a valid quantity";
                break;
            }
            
            // Check if enough stock
            if ($quantity > $product['stock']) {
                $error = "Not enough stock available. Only " . $product['stock'] . " available.";
                break;
            }
            
            // Create order
            $result = $order_model->create_order(
                $buyer_id,
                $farmer_id,
                $product_id,
                $quantity
            );
            
            if (isset($result['success'])) {
                $success = "Order placed successfully!";
                // Redirect to orders list after 2 seconds
                header("Refresh: 2; URL=order.php");
            } else {
                $error = isset($result['error']) ? $result['error'] : "Unknown error occurred";
            }
        }
        
        include 'views/orders/create.php';
        break;
        
    case 'view':
        $order_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        
        if ($order_id === 0) {
            redirect("order.php");
        }
        
        // Get order data
        $order = $order_model->get_order($order_id);
        
        // Check if order exists and belongs to the current user
        if (!$order || ($order['buyer_id'] != $_SESSION['user_id'] && $order['farmer_id'] != $_SESSION['user_id'])) {
            redirect("order.php");
        }
        
        include 'views/orders/view.php';
        break;
        
    case 'update':
        $order_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        
        if ($order_id === 0) {
            redirect("order.php");
        }
        
        // Get order data
        $order = $order_model->get_order($order_id);
        
        // Check if order exists and belongs to the current user
        if (!$order || ($order['buyer_id'] != $_SESSION['user_id'] && $order['farmer_id'] != $_SESSION['user_id'])) {
            redirect("order.php");
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF token
            if (!verify_csrf_token($_POST['csrf_token'])) {
                $error = "Invalid request";
                break;
            }
            
            // Validate and sanitize input
            $status = sanitize_input($_POST['status']);
            $user_id = $_SESSION['user_id'];
            $is_farmer = is_farmer();
            
            // Update order status
            $result = $order_model->update_status($order_id, $status, $user_id, $is_farmer);
            
            if (isset($result['success'])) {
                $success = "Order status updated successfully!";
                // Reload the page with updated data
                header("Refresh: 2; URL=order.php?action=view&id=" . $order_id);
            } else {
                $error = isset($result['error']) ? $result['error'] : "Unknown error occurred";
            }
        }
        
        include 'views/orders/update.php';
        break;
        
    default:
        // Get orders based on user type
        if (is_farmer()) {
            $orders = $order_model->get_farmer_orders($_SESSION['user_id']);
        } else {
            $orders = $order_model->get_buyer_orders($_SESSION['user_id']);
        }
        
        include 'views/orders/list.php';
        break;
}
?>
