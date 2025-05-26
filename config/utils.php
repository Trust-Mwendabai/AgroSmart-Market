<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Function to check if user is a farmer
function is_farmer() {
    return is_logged_in() && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'farmer';
}

// Function to check if user is a buyer
function is_buyer() {
    return is_logged_in() && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'buyer';
}

// Function to check if user is an admin
function is_admin() {
    return is_logged_in() && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

// Function to redirect user
function redirect($location) {
    header("Location: $location");
    exit;
}

// Function to generate random token
function generate_token($length = 32) {
    return bin2hex(random_bytes($length));
}

// Function to display error messages
function display_error($error) {
    return '<div class="alert alert-danger">' . $error . '</div>';
}

// Function to display success messages
function display_success($message) {
    return '<div class="alert alert-success">' . $message . '</div>';
}

// Include FileStorage class
require_once dirname(__DIR__) . '/includes/FileStorage.php';

// Load CDN configuration
$cdn_config = require_once __DIR__ . '/cdn.php';

// Function to upload image using the FileStorage class
function upload_image($file, $target_dir = 'products', $custom_name = null) {
    // Initialize the FileStorage class
    static $fileStorage = null;
    
    if ($fileStorage === null) {
        // Use the provider defined in configuration (defaults to local storage)
        $fileStorage = new FileStorage();
    }
    
    // Upload the file using the FileStorage class
    $result = $fileStorage->uploadFile($file, $target_dir, $custom_name);
    
    // Convert to the expected format for backward compatibility
    if (isset($result['success'])) {
        return [
            "success" => true, 
            "filename" => $result['filename'],
            "url" => $result['url'],
            "variants" => $result['variants'] ?? []
        ];
    } else {
        return ["error" => $result['error']];
    }
}

// Function to get asset URL (with CDN support)
function asset_url($path) {
    global $cdn_config;
    
    // Use the CDN URL helper function
    return cdn_url($path);
}

// Function to get image URL with proper sizing
function get_image_url($filename, $type = 'products', $size = 'medium') {
    // Check if the filename contains size information
    if (strpos($filename, '-' . $size) === false) {
        // If not, create the sized filename
        $pathinfo = pathinfo($filename);
        $base = $pathinfo['filename'];
        $ext = isset($pathinfo['extension']) ? '.' . $pathinfo['extension'] : '';
        
        // Check if sized version might exist
        $sized_filename = $base . '-' . $size . $ext;
        
        // Check if the sized file exists, otherwise use original
        $sized_path = dirname(__DIR__) . '/public/uploads/' . $type . '/' . $sized_filename;
        if (file_exists($sized_path)) {
            $filename = $sized_filename;
        }
    }
    
    // Build the URL path
    $path = 'uploads/' . $type . '/' . $filename;
    
    // Use CDN if available
    return asset_url($path);
}

// Generate CSRF token
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

// Function to format price
function format_price($price) {
    return 'K' . number_format($price, 2);
}

// Function to format currency (alias for format_price for compatibility)
function format_currency($price) {
    return format_price($price);
}

// Function to get user data
function get_user_data($conn, $user_id) {
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 1) {
        return mysqli_fetch_assoc($result);
    }
    
    return false;
}
?>
