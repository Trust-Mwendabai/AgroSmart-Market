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

// Get report type from URL
$report_type = isset($_GET['type']) ? $_GET['type'] : 'general';

// Initialize data
$report_data = [];
$report_title = '';

// Handle different report types
switch ($report_type) {
    case 'revenue':
        $report_title = 'Revenue Report';
        
        // Get total revenue
        $query = "SELECT SUM(amount) as total FROM revenue_transactions";
        $result = mysqli_query($conn, $query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $report_data['total_revenue'] = $row['total'] ?: 0;
        }
        
        // Get revenue by stream
        $query = "SELECT stream_type, SUM(amount) as total FROM revenue_transactions GROUP BY stream_type";
        $result = mysqli_query($conn, $query);
        if ($result) {
            $report_data['revenue_streams'] = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $report_data['revenue_streams'][$row['stream_type']] = $row['total'];
            }
        }
        
        // Get monthly revenue for the current year
        $current_year = date('Y');
        $query = "SELECT MONTH(transaction_date) as month, SUM(amount) as total FROM revenue_transactions 
                  WHERE YEAR(transaction_date) = '{$current_year}' GROUP BY month ORDER BY month";
        $result = mysqli_query($conn, $query);
        if ($result) {
            $monthly_data = array_fill(1, 12, 0); // Initialize all months with zero
            while ($row = mysqli_fetch_assoc($result)) {
                $monthly_data[$row['month']] = $row['total'];
            }
            $report_data['monthly_revenue'] = $monthly_data;
        }
        
        // Get revenue transactions for detailed view
        $query = "SELECT * FROM revenue_transactions ORDER BY transaction_date DESC LIMIT 100";
        $result = mysqli_query($conn, $query);
        if ($result) {
            $report_data['transactions'] = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $report_data['transactions'][] = $row;
            }
        }
        
        include '../views/admin/reports/revenue.php';
        break;
        
    case 'users':
        $report_title = 'Users Report';
        // Code for users report
        include '../views/admin/reports/users.php';
        break;
        
    case 'orders':
        $report_title = 'Orders Report';
        // Code for orders report
        include '../views/admin/reports/orders.php';
        break;
        
    default:
        $report_title = 'General Report';
        // Code for general report
        include '../views/admin/reports/general.php';
        break;
}

// Set page title
$page_title = $report_title . " - AgroSmart Market";

// Include admin header if not already included
if (!isset($header_included)) {
    include '../views/admin/partials/header.php';
}

// Footer is included in the respective report view files
?>
