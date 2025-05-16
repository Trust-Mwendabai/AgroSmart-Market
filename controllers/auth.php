<?php
// Start session
session_start();

// Include database connection and utilities
$conn = require_once '../config/database.php';
require_once '../config/utils.php';

// Include user model
require_once '../models/User.php';

// Initialize user model
$user_model = new User($conn);

// Handle different actions
$action = isset($_GET['action']) ? $_GET['action'] : '';
$error = '';
$success = '';

switch ($action) {
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF token
            if (!verify_csrf_token($_POST['csrf_token'])) {
                $error = "Invalid request";
                break;
            }
            
            // Validate input
            $name = sanitize_input($_POST['name']);
            $email = sanitize_input($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $user_type = sanitize_input($_POST['user_type']);
            $location = isset($_POST['location']) ? sanitize_input($_POST['location']) : '';
            
            // Check for empty fields
            if (empty($name) || empty($email) || empty($password) || empty($user_type)) {
                $error = "All fields are required";
                break;
            }
            
            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Invalid email format";
                break;
            }
            
            // Check password length
            if (strlen($password) < 6) {
                $error = "Password must be at least 6 characters long";
                break;
            }
            
            // Check if passwords match
            if ($password !== $confirm_password) {
                $error = "Passwords do not match";
                break;
            }
            
            // Validate user type
            $allowed_types = ['farmer', 'buyer'];
            if (!in_array($user_type, $allowed_types)) {
                $error = "Invalid user type";
                break;
            }
            
            // Register user
            $result = $user_model->register($name, $email, $password, $user_type, $location);
            
            if (isset($result['success'])) {
                // Send verification email (in a real app)
                // For now, we'll just verify automatically for demo purposes
                $user_model->verify_email($result['verification_token']);
                
                $success = "Registration successful! You can now login.";
                
                // Redirect to login page after 2 seconds
                header("Refresh: 2; URL=auth.php?action=login");
            } else {
                $error = $result['error'];
            }
        }
        include '../views/auth/register.php';
        break;
        
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF token
            if (!verify_csrf_token($_POST['csrf_token'])) {
                $error = "Invalid request";
                break;
            }
            
            // Validate input
            $email = sanitize_input($_POST['email']);
            $password = $_POST['password'];
            
            // Check for empty fields
            if (empty($email) || empty($password)) {
                $error = "Email and password are required";
                break;
            }
            
            // Login user
            $result = $user_model->login($email, $password);
            
            if (isset($result['success'])) {
                // Set session variables
                $_SESSION['user_id'] = $result['user']['id'];
                $_SESSION['user_name'] = $result['user']['name'];
                $_SESSION['user_email'] = $result['user']['email'];
                $_SESSION['user_type'] = $result['user']['user_type'];
                
                // Redirect based on user type
                if ($result['user']['user_type'] === 'admin') {
                    redirect("../admin/dashboard.php");
                } else {
                    redirect("../dashboard.php");
                }
            } else {
                $error = $result['error'];
            }
        }
        include '../views/auth/login.php';
        break;
        
    case 'logout':
        // Destroy session
        session_unset();
        session_destroy();
        
        // Redirect to home page
        redirect("../index.php");
        break;
        
    case 'verify':
        $token = isset($_GET['token']) ? $_GET['token'] : '';
        
        if (empty($token)) {
            $error = "Invalid verification link";
        } else {
            if ($user_model->verify_email($token)) {
                $success = "Email verified successfully! You can now login.";
                
                // Redirect to login page after 2 seconds
                header("Refresh: 2; URL=auth.php?action=login");
            } else {
                $error = "Invalid or expired verification link";
            }
        }
        include '../views/auth/verify.php';
        break;
    
    default:
        // Redirect to login page
        redirect("auth.php?action=login");
        break;
}
?>
