<?php
// Start session
session_start();

// Include necessary files
require_once 'config/database.php';
require_once 'config/utils.php';

// Check CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        // Invalid token, redirect to homepage
        redirect('index.php?error=invalid_token');
    }
    
    // Get language code
    $language = sanitize_input($_POST['language']);
    
    // Validate language code
    require_once 'config/languages.php';
    
    if (array_key_exists($language, $available_languages)) {
        // Set language in session
        $_SESSION['language'] = $language;
        
        // Set cookie for 30 days
        setcookie('language', $language, time() + (86400 * 30), '/');
        
        // Log language change if user is logged in
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $sql = "UPDATE users SET language_preference = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $language, $user_id);
            mysqli_stmt_execute($stmt);
        }
    }
    
    // Redirect back to previous page
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'index.php';
    
    // Ensure redirect is safe (only relative URLs)
    if (strpos($redirect, '://') !== false) {
        $redirect = 'index.php';
    }
    
    redirect($redirect);
} else {
    // Invalid request method, redirect to homepage
    redirect('index.php');
}
?>
