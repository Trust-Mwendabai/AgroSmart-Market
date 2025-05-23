<?php
session_start();

// Check if user was logged in before proceeding with logout
$was_logged_in = isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin';

// Delete the remember me cookie if it exists
if (isset($_COOKIE['admin_remember'])) {
    // Extract user ID from cookie
    list($user_id, $token) = explode(':', $_COOKIE['admin_remember']);
    
    // Connect to database to remove token
    $conn = require_once '../config/database.php';
    
    // Remove token from database
    $stmt = mysqli_prepare($conn, "DELETE FROM remember_tokens WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    
    // Delete the cookie by setting expiration in the past
    setcookie('admin_remember', '', time() - 3600, '/', '', false, true);
}

// Unset all session variables
$_SESSION = array();

// If session is using cookies, delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Set a temporary message for feedback
if ($was_logged_in) {
    // Start a new session just for the message
    session_start();
    $_SESSION['logout_message'] = "You have been successfully logged out. Thank you for using AgroSmart Admin!";
}

// Redirect to login page
header('Location: login.php');
exit();
?>