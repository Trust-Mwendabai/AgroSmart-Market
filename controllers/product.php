<?php
// Start session
session_start();

// Include database connection and utilities
$conn = require_once '../config/database.php';
require_once '../config/utils.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect("auth.php?action=login");
}

// Include models
require_once '../models/Product.php';
require_once '../models/User.php';

// Initialize models
$product_model = new Product($conn);
$user_model = new User($conn);

// Get action from URL
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$error = '';
$success = '';

switch ($action) {
    case 'add':
        // Only farmers can add products
        if (!is_farmer()) {
            redirect("../dashboard.php");
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF token
            if (!verify_csrf_token($_POST['csrf_token'])) {
                $error = "Invalid request";
                break;
            }
            
            // Validate and sanitize input
            $name = sanitize_input($_POST['name']);
            $description = sanitize_input($_POST['description']);
            $price = (float) $_POST['price'];
            $category = sanitize_input($_POST['category']);
            $stock = (int) $_POST['stock'];
            $farmer_id = $_SESSION['user_id'];
            
            // Check for required fields
            if (empty($name) || empty($description) || empty($price)) {
                $error = "Product name, description, and price are required";
                break;
            }
            
            // Handle image upload
            $image_filename = "";
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_result = upload_image($_FILES['image'], '../public/uploads/');
                
                if (isset($upload_result['error'])) {
                    $error = $upload_result['error'];
                    break;
                } else {
                    $image_filename = $upload_result['filename'];
                }
            }
            
            // Add product to database
            $result = $product_model->add_product(
                $farmer_id,
                $name,
                $description,
                $price,
                $image_filename,
                $category,
                $stock
            );
            
            if (isset($result['success'])) {
                $success = "Product added successfully!";
                // Redirect to product list after 2 seconds
                header("Refresh: 2; URL=product.php");
            } else {
                $error = $result['error'];
            }
        }
        
        include '../views/products/add.php';
        break;
        
    case 'edit':
        // Only farmers can edit products
        if (!is_farmer()) {
            redirect("../dashboard.php");
        }
        
        $product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        
        if ($product_id === 0) {
            redirect("product.php");
        }
        
        // Get product data
        $product = $product_model->get_product($product_id);
        
        // Check if product exists and belongs to the farmer
        if (!$product || $product['farmer_id'] != $_SESSION['user_id']) {
            redirect("product.php");
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF token
            if (!verify_csrf_token($_POST['csrf_token'])) {
                $error = "Invalid request";
                break;
            }
            
            // Validate and sanitize input
            $name = sanitize_input($_POST['name']);
            $description = sanitize_input($_POST['description']);
            $price = (float) $_POST['price'];
            $category = sanitize_input($_POST['category']);
            $stock = (int) $_POST['stock'];
            $farmer_id = $_SESSION['user_id'];
            
            // Check for required fields
            if (empty($name) || empty($description) || empty($price)) {
                $error = "Product name, description, and price are required";
                break;
            }
            
            // Prepare data for update
            $data = [
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'category' => $category,
                'stock' => $stock
            ];
            
            // Handle image upload if new image is provided
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_result = upload_image($_FILES['image'], '../public/uploads/');
                
                if (isset($upload_result['error'])) {
                    $error = $upload_result['error'];
                    break;
                } else {
                    $data['image'] = $upload_result['filename'];
                }
            }
            
            // Update product
            $result = $product_model->update_product($product_id, $farmer_id, $data);
            
            if (isset($result['success'])) {
                $success = "Product updated successfully!";
                // Reload the page with updated data
                header("Refresh: 2; URL=product.php?action=edit&id=" . $product_id);
            } else {
                $error = $result['error'];
            }
        }
        
        include '../views/products/edit.php';
        break;
        
    case 'delete':
        // Only farmers can delete products
        if (!is_farmer()) {
            redirect("../dashboard.php");
        }
        
        $product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        
        if ($product_id === 0) {
            redirect("product.php");
        }
        
        // Delete product
        $result = $product_model->delete_product($product_id, $_SESSION['user_id']);
        
        if (isset($result['success'])) {
            $success = "Product deleted successfully!";
            // Redirect to product list after 2 seconds
            header("Refresh: 2; URL=product.php");
        } else {
            $error = $result['error'];
            // Redirect to product list after 2 seconds
            header("Refresh: 2; URL=product.php");
        }
        
        include '../views/products/delete.php';
        break;
        
    case 'view':
        $product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        
        if ($product_id === 0) {
            redirect("../marketplace.php");
        }
        
        // Get product data
        $product = $product_model->get_product($product_id);
        
        // Check if product exists
        if (!$product) {
            redirect("../marketplace.php");
        }
        
        // Get farmer data
        $farmer = $user_model->get_user($product['farmer_id']);
        
        include '../views/products/view.php';
        break;
        
    default:
        // For farmers, show their products
        if (is_farmer()) {
            $products = $product_model->get_farmer_products($_SESSION['user_id']);
            include '../views/products/list.php';
        } else {
            // Redirect buyers to marketplace
            redirect("../marketplace.php");
        }
        break;
}
?>
