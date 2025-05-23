<?php
// Start session
session_start();

// Include database connection and utilities
$conn = require_once '../config/database.php';
require_once '../config/utils.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Include models
require_once '../models/User.php';
require_once '../models/Product.php';
require_once '../models/Order.php';
require_once '../models/Message.php';

// Initialize models
$user_model = new User($conn);
$product_model = new Product($conn);
$order_model = new Order($conn);
$message_model = new Message($conn);

// Get statistics
$stats = [
    'total_buyers' => 0,
    'total_farmers' => 0,
    'total_products' => 0,
    'total_orders' => 0,
    'recent_orders' => [],
    'recent_users' => [],
    'recent_products' => [],
    'total_messages' => 0,
    'orders_by_status' => [],
    // Revenue statistics
    'total_revenue' => 0,
    'revenue_streams' => [
        'commissions' => 0,
        'ads' => 0,
        'premium_listings' => 0,
        'transport_fees' => 0,
        'subscriptions' => 0
    ],
    'monthly_revenue' => []
];

// Get total buyers
$query = "SELECT COUNT(*) as count FROM users WHERE user_type = 'buyer'";
$result = mysqli_query($conn, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $stats['total_buyers'] = $row['count'];
}

// Get total farmers
$query = "SELECT COUNT(*) as count FROM users WHERE user_type = 'farmer'";
$result = mysqli_query($conn, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $stats['total_farmers'] = $row['count'];
}

// Get total products
$query = "SELECT COUNT(*) as count FROM products";
$result = mysqli_query($conn, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $stats['total_products'] = $row['count'];
}

// Get total orders
$query = "SELECT COUNT(*) as count FROM orders";
$result = mysqli_query($conn, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $stats['total_orders'] = $row['count'];
}

// Get orders by status
$query = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
$result = mysqli_query($conn, $query);
if ($result) {
    $stats['orders_by_status'] = []; 
    while ($row = mysqli_fetch_assoc($result)) {
        $stats['orders_by_status'][$row['status']] = $row['count'];
    }
}

// Get total messages
$query = "SELECT COUNT(*) as count FROM messages";
$result = mysqli_query($conn, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $stats['total_messages'] = $row['count'];
}

// Get total revenue (with error handling in case table doesn't exist)
try {
    $query = "SELECT SUM(amount) as total FROM revenue_transactions";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $stats['total_revenue'] = $row['total'] ?: 0;
    }
} catch (Exception $e) {
    // Table might not exist, just leave the default value
    error_log('Revenue table query failed: ' . $e->getMessage());
}

// Get revenue by stream (with error handling)
try {
    $query = "SELECT stream_type, SUM(amount) as total FROM revenue_transactions GROUP BY stream_type";
    $result = mysqli_query($conn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            switch ($row['stream_type']) {
                case 'commission':
                    $stats['revenue_streams']['commissions'] = $row['total'];
                    break;
                case 'ad':
                    $stats['revenue_streams']['ads'] = $row['total'];
                    break;
                case 'premium_listing':
                    $stats['revenue_streams']['premium_listings'] = $row['total'];
                    break;
                case 'transport_fee':
                    $stats['revenue_streams']['transport_fees'] = $row['total'];
                    break;
                case 'subscription':
                    $stats['revenue_streams']['subscriptions'] = $row['total'];
                    break;
            }
        }
    }
} catch (Exception $e) {
    // Table might not exist, just leave the default values
    error_log('Revenue streams query failed: ' . $e->getMessage());
}

// Get monthly revenue for the current year (with error handling)
try {
    $current_year = date('Y');
    $query = "SELECT MONTH(transaction_date) as month, SUM(amount) as total FROM revenue_transactions 
            WHERE YEAR(transaction_date) = '{$current_year}' GROUP BY month ORDER BY month";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $monthly_data = array_fill(1, 12, 0); // Initialize all months with zero
        while ($row = mysqli_fetch_assoc($result)) {
            $monthly_data[$row['month']] = $row['total'];
        }
        $stats['monthly_revenue'] = $monthly_data;
    }
} catch (Exception $e) {
    // Table might not exist, just initialize with zeros
    $stats['monthly_revenue'] = array_fill(1, 12, 0);
    error_log('Monthly revenue query failed: ' . $e->getMessage());
}

// Get recent orders (last 5)
$query = "
    SELECT o.*, b.name as buyer_name, f.name as farmer_name, p.name as product_name 
    FROM orders o 
    JOIN users b ON o.buyer_id = b.id 
    JOIN users f ON o.farmer_id = f.id 
    JOIN products p ON o.product_id = p.id 
    ORDER BY o.date_ordered DESC 
    LIMIT 5";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $stats['recent_orders'][] = $row;
    }
}

// Get recent users (last 5)
$query = "SELECT * FROM users ORDER BY date_registered DESC LIMIT 5";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $stats['recent_users'][] = $row;
    }
}

// Get recent products (last 5)
$query = "
    SELECT p.*, u.name as farmer_name 
    FROM products p 
    JOIN users u ON p.farmer_id = u.id 
    ORDER BY p.date_added DESC 
    LIMIT 5";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $stats['recent_products'][] = $row;
    }
}

// Set page title
$page_title = "Admin Dashboard - AgroSmart Market";

// Include admin header
include '../views/admin/partials/header.php';

// Add a revenue widget to the dashboard
$revenue_widget = [
    'total' => format_price($stats['total_revenue']),
    'streams' => [
        ['name' => '1% Commission', 'amount' => $stats['revenue_streams']['commissions'], 'icon' => 'percentage'],
        ['name' => 'In-app Ads', 'amount' => $stats['revenue_streams']['ads'], 'icon' => 'ad'],
        ['name' => 'Premium Listings', 'amount' => $stats['revenue_streams']['premium_listings'], 'icon' => 'star'],
        ['name' => 'Transport Fees', 'amount' => $stats['revenue_streams']['transport_fees'], 'icon' => 'truck'],
        ['name' => 'Subscriptions', 'amount' => $stats['revenue_streams']['subscriptions'], 'icon' => 'user-shield']
    ]
];

// Include dashboard view
include '../views/admin/dashboard.php';

// Include admin footer
include '../views/admin/partials/footer.php';
?>
