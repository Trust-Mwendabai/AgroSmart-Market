<?php
// Start the session
session_start();

// Include necessary files
require_once 'config/database.php';
require_once 'config/utils.php';
require_once 'models/Message.php';
require_once 'models/User.php';
require_once 'models/Product.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect('auth.php?action=login');
}

// Initialize models
$message = new Message($conn);
$user = new User($conn);
$product = new Product($conn);

// Determine the action
$action = isset($_GET['action']) ? $_GET['action'] : 'inbox';

// Handle the actions
switch ($action) {
    case 'inbox':
        // Get messages for the current user
        $messages = $message->get_inbox_messages($_SESSION['user_id']);
        
        // Mark as viewed
        if (!empty($messages)) {
            foreach ($messages as $msg) {
                if (!$msg['is_read']) {
                    $message->mark_as_read($msg['id']);
                }
            }
        }
        
        // Include the view
        include 'views/messages/inbox.php';
        break;
        
    case 'sent':
        // Get sent messages for the current user
        $messages = $message->get_sent_messages($_SESSION['user_id']);
        
        // Include the view
        include 'views/messages/sent.php';
        break;
        
    case 'compose':
        // Get all users for the recipient dropdown
        $users = $user->get_all_users();
        
        // Check if this is a product inquiry
        if (isset($_GET['to']) && isset($_GET['product_id'])) {
            $receiver_id = sanitize_input($_GET['to']);
            $product_id = sanitize_input($_GET['product_id']);
            
            // Get receiver details
            $receiver = $user->get_user_by_id($receiver_id);
            $receiver_name = $receiver['name'];
            
            // Get product details
            $product_details = $product->get_product($product_id);
            $product_name = $product_details['name'];
            $product_price = '$' . number_format($product_details['price'], 2);
        }
        
        // Check if this is a reply
        if (isset($_GET['reply_to'])) {
            $reply_to_id = sanitize_input($_GET['reply_to']);
            $original_message = $message->get_message_by_id($reply_to_id);
            
            if ($original_message) {
                // Set the receiver to the original sender
                $receiver_id = $original_message['sender_id'];
                $receiver = $user->get_user_by_id($receiver_id);
                $receiver_name = $receiver['name'];
                
                // Create a reply subject
                $subject = "Re: " . $original_message['subject'];
                
                // Create a quoted message
                $quoted_message = "\n\n\n------ Original Message ------\n";
                $quoted_message .= "From: " . $original_message['sender_name'] . "\n";
                $quoted_message .= "Date: " . date('Y-m-d H:i', strtotime($original_message['date_sent'])) . "\n";
                $quoted_message .= "Subject: " . $original_message['subject'] . "\n\n";
                $quoted_message .= strip_tags($original_message['message']);
            }
        }
        
        // Include the view
        include 'views/messages/compose.php';
        break;
        
    case 'send':
        // Check if form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF token
            if (!verify_csrf_token($_POST['csrf_token'])) {
                $error = "Invalid request, please try again.";
                include 'views/messages/compose.php';
                exit;
            }
            
            // Get form data
            $receiver_id = sanitize_input($_POST['receiver_id']);
            $subject = sanitize_input($_POST['subject']);
            $message_content = $_POST['message']; // Don't sanitize to preserve HTML formatting
            
            // Add product ID if this is a product inquiry
            $product_id = isset($_POST['product_id']) ? sanitize_input($_POST['product_id']) : null;
            
            // Send the message
            $result = $message->send_message($_SESSION['user_id'], $receiver_id, $subject, $message_content, $product_id);
            
            if (isset($result['error'])) {
                $error = $result['error'];
                $users = $user->get_all_users();
                include 'views/messages/compose.php';
            } else {
                $success = "Message sent successfully!";
                redirect('message.php?success=' . urlencode($success));
            }
        } else {
            redirect('message.php?action=compose');
        }
        break;
        
    case 'view':
        if (!isset($_GET['id'])) {
            redirect('message.php');
        }
        
        $message_id = sanitize_input($_GET['id']);
        
        // Get the message data
        $message_data = $message->get_message_by_id($message_id);
        
        // Check if message exists and belongs to current user
        if (!$message_data || ($message_data['sender_id'] != $_SESSION['user_id'] && $message_data['receiver_id'] != $_SESSION['user_id'])) {
            $error = "Message not found or you don't have permission to view it.";
            redirect('message.php?error=' . urlencode($error));
        }
        
        // Mark as read if this is a received message
        if ($message_data['receiver_id'] == $_SESSION['user_id'] && !$message_data['is_read']) {
            $message->mark_as_read($message_id);
        }
        
        // Get previous messages in this conversation
        $previous_messages = $message->get_conversation_messages(
            $message_data['sender_id'],
            $message_data['receiver_id'],
            $message_id
        );
        
        // Get product details if this is a product inquiry
        if (isset($message_data['related_product_id']) && !empty($message_data['related_product_id'])) {
            $product_data = $product->get_product($message_data['related_product_id']);
        }
        
        // Rename for the view to avoid conflict with the Message model
        $message_item = $message_data;
        
        // Include the view
        include 'views/messages/view.php';
        break;
        
    case 'delete':
        if (!isset($_GET['id'])) {
            redirect('message.php');
        }
        
        $message_id = sanitize_input($_GET['id']);
        
        // Delete the message
        $result = $message->delete_message($message_id, $_SESSION['user_id']);
        
        if (isset($result['error'])) {
            $error = $result['error'];
        } else {
            $success = "Message deleted successfully!";
        }
        
        // Redirect back to inbox
        redirect('message.php' . (isset($error) ? '?error=' . urlencode($error) : '?success=' . urlencode($success)));
        break;
        
    default:
        redirect('message.php');
        break;
}

// Set the page title
$page_title = 'Messages';

// Include header
include 'views/partials/header.php';

// Include the appropriate view based on the action
switch ($action) {
    case 'compose':
        include 'views/messages/compose.php';
        break;
    case 'view':
        include 'views/messages/view.php';
        break;
    case 'inbox':
    default:
        include 'views/messages/inbox.php';
        break;
}

// Include footer
include 'views/partials/footer.php';
?>
