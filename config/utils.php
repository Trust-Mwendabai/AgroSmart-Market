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

// Function to upload image
function upload_image($file, $target_dir = '../public/uploads/') {
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $target_file = $target_dir . basename($file["name"]);
    $upload_ok = 1;
    $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if image file is an actual image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return ["error" => "File is not an image."];
    }
    
    // Check file size (limit to 5MB)
    if ($file["size"] > 5000000) {
        return ["error" => "Sorry, your file is too large."];
    }
    
    // Allow certain file formats
    if($image_file_type != "jpg" && $image_file_type != "png" && $image_file_type != "jpeg"
    && $image_file_type != "gif" ) {
        return ["error" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed."];
    }
    
    // Generate unique filename to prevent overwriting
    $new_filename = uniqid() . '.' . $image_file_type;
    $target_file = $target_dir . $new_filename;
    
    // Try to upload file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["success" => true, "filename" => $new_filename];
    } else {
        return ["error" => "Sorry, there was an error uploading your file."];
    }
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
    return '$' . number_format($price, 2);
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
