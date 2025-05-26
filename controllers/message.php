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
require_once '../models/Message.php';
require_once '../models/User.php';

// Initialize models
$message_model = new Message($conn);
$user_model = new User($conn);

// Get action from URL
$action = isset($_GET['action']) ? $_GET['action'] : 'inbox';
$error = '';
$success = '';

switch ($action) {
    case 'compose':
        $receiver_id = isset($_GET['to']) ? (int) $_GET['to'] : 0;
        $product_id = isset($_GET['product_id']) ? (int) $_GET['product_id'] : 0;
        
        // If receiver is specified, get their details
        if ($receiver_id > 0) {
            $receiver = $user_model->get_user($receiver_id);
            if (!$receiver) {
                $error = "User not found";
            }
        }
        
        // If product is specified, get its details for the subject
        $product_name = '';
        if ($product_id > 0) {
            require_once '../models/Product.php';
            $product_model = new Product($conn);
            $product = $product_model->get_product($product_id);
            if ($product) {
                $product_name = $product['name'];
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF token
            if (!verify_csrf_token($_POST['csrf_token'])) {
                $error = "Invalid request";
                break;
            }
            
            // Validate and sanitize input
            $receiver_id = (int) $_POST['receiver_id'];
            $subject = sanitize_input($_POST['subject']);
            $message_text = sanitize_input($_POST['message']);
            $sender_id = $_SESSION['user_id'];
            
            // Check for required fields
            if (empty($subject) || empty($message_text) || empty($receiver_id)) {
                $error = "All fields are required";
                break;
            }
            
            // Check if receiver exists
            $receiver = $user_model->get_user($receiver_id);
            if (!$receiver) {
                $error = "Recipient not found";
                break;
            }
            
            // Send message
            $result = $message_model->send_message(
                $sender_id,
                $receiver_id,
                $subject,
                $message_text
            );
            
            if (isset($result['success'])) {
                $success = "Message sent successfully!";
                // Redirect to inbox after 2 seconds
                header("Refresh: 2; URL=message.php");
            } else {
                $error = $result['error'];
            }
        }
        
        include '../views/messages/compose.php';
        break;
        
    case 'view':
        $message_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        
        if ($message_id === 0) {
            redirect("message.php");
        }
        
        // Get message data
        $message_item = $message_model->get_message($message_id, $_SESSION['user_id']);
        
        // Check if message exists and belongs to the current user
        if (!$message_item) {
            redirect("message.php");
        }
        
        include '../views/messages/view.php';
        break;
        
    case 'delete':
        $message_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        
        if ($message_id === 0) {
            redirect("message.php");
        }
        
        // Delete message
        $result = $message_model->delete_message($message_id, $_SESSION['user_id']);
        
        if (isset($result['success'])) {
            $success = "Message deleted successfully!";
            // Redirect to inbox after 2 seconds
            header("Refresh: 2; URL=message.php");
        } else {
            $error = "Failed to delete message";
            // Redirect to inbox after 2 seconds
            header("Refresh: 2; URL=message.php");
        }
        
        include '../views/messages/delete.php';
        break;
        
    case 'sent':
        // Get sent messages
        $messages = $message_model->get_sent_messages($_SESSION['user_id']);
        
        include '../views/messages/sent.php';
        break;
        
    case 'inbox':
    default:
        // Get inbox messages
        $messages = $message_model->get_inbox_messages($_SESSION['user_id']);
        
        include '../views/messages/inbox.php';
        break;
}
?>
