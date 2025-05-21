<?php
session_start();
require_once 'config/database.php';
require_once 'models/User.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Check if file was uploaded
if (!isset($_FILES['profile_image'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No file uploaded']);
    exit;
}

// Validate file
$file = $_FILES['profile_image'];
$max_size = 5 * 1024 * 1024; // 5MB
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Upload error: ' . $file['error']]);
    exit;
}

if ($file['size'] > $max_size) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'File too large (max 5MB)']);
    exit;
}

if (!in_array($file['type'], $allowed_types)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid file type']);
    exit;
}

// Create upload directory if it doesn't exist
$upload_dir = __DIR__ . '/images/profiles/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Generate unique filename
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$filename = "profile_" . $user_id . "_" . time() . "." . $extension;
$upload_path = $upload_dir . $filename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $upload_path)) {
    // Update user's profile image in database
    $user = new User($GLOBALS['conn']);
    $result = $user->update_profile($user_id, ['profile_image' => $filename]);
    
    if (isset($result['success'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'filename' => $filename]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => $result['error']]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Failed to move uploaded file']);
}
?>
