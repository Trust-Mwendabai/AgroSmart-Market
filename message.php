<?php
/**
 * Message Controller
 * 
 * Handles all message-related operations including viewing, sending, and managing messages.
 * This controller works with the Message model to provide messaging functionality
 * between users in the AgroSmart Market platform.
 * 
 * @package Controllers
 */

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

// Determine the action from the request
$action = isset($_GET['action']) ? sanitize_input($_GET['action']) : 'inbox';

// Initialize variables to store messages and errors
$success = isset($_GET['success']) ? sanitize_input($_GET['success']) : '';
$error = isset($_GET['error']) ? sanitize_input($_GET['error']) : '';

/**
 * Route the request to the appropriate handler based on the action
 * 
 * Available actions:
 * - inbox: View received messages
 * - sent: View sent messages
 * - compose: Display message composition form
 * - send: Process message sending form
 * - view: View a specific message
 * - delete: Delete a message
 */
switch ($action) {
    /**
     * Display the inbox with received messages
     * Marks all unread messages as read when viewing the inbox
     */
    case 'inbox':
        try {
            // Get messages for the current user
            $messages = $message->get_inbox_messages($_SESSION['user_id']);
            
            // Mark messages as viewed when the inbox is opened
            if (!empty($messages)) {
                foreach ($messages as $msg) {
                    if (!$msg['is_read']) {
                        $message->mark_as_read($msg['id']);
                    }
                }
            }
            
            // Include the inbox view
            include 'views/messages/inbox.php';
        } catch (Exception $e) {
            error_log('Error loading inbox: ' . $e->getMessage());
            $error = 'An error occurred while loading your messages. Please try again.';
            include 'views/error.php';
        }
        break;
        
    /**
     * Display sent messages
     */
    case 'sent':
        try {
            // Get sent messages for the current user
            $messages = $message->get_sent_messages($_SESSION['user_id']);
            
            // Include the sent messages view
            include 'views/messages/sent.php';
        } catch (Exception $e) {
            error_log('Error loading sent messages: ' . $e->getMessage());
            $error = 'An error occurred while loading your sent messages. Please try again.';
            include 'views/error.php';
        }
        break;
        
    /**
     * Display the message composition form
     * Handles both new messages and replies
     */
    case 'compose':
        try {
            // Initialize variables
            $receiver_id = null;
            $receiver_name = '';
            $subject = '';
            $quoted_message = '';
            $product_id = null;
            $product_name = '';
            $product_price = '';
            
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
                if ($product_details) {
                    $product_name = $product_details['name'];
                    $product_price = '$' . number_format($product_details['price'], 2);
                    $subject = "Inquiry about: " . $product_name;
                }
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
                    if (strpos($original_message['subject'], 'Re: ') !== 0) {
                        $subject = "Re: " . $original_message['subject'];
                    } else {
                        $subject = $original_message['subject'];
                    }
                    
                    // Create a quoted message
                    $quoted_message = "\n\n\n------ Original Message ------\n";
                    $quoted_message .= "From: " . $original_message['sender_name'] . "\n";
                    $quoted_message .= "Date: " . date('Y-m-d H:i', strtotime($original_message['date_sent'])) . "\n";
                    $quoted_message .= "Subject: " . $original_message['subject'] . "\n\n";
                    $quoted_message .= strip_tags($original_message['message']);
                }
            }
            
            // Include the compose view
            include 'views/messages/compose.php';
            
        } catch (Exception $e) {
            error_log('Error loading compose form: ' . $e->getMessage());
            $error = 'An error occurred while loading the message form. Please try again.';
            include 'views/error.php';
        }
        break;
        
    /**
     * Handle message submission
     * Processes the message form and sends the message
     */
    case 'send':
        // Check if form is submitted via POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('message.php?action=compose');
            exit;
        }
        
        try {
            // Verify CSRF token
            if (!verify_csrf_token($_POST['csrf_token'])) {
                throw new Exception("Invalid request. Please try again.");
            }
            
            // Validate required fields
            if (empty($_POST['receiver_id']) || empty($_POST['subject']) || empty($_POST['message'])) {
                throw new Exception("All fields are required.");
            }
            
            // Sanitize input
            $receiver_id = (int)$_POST['receiver_id'];
            $subject = trim(strip_tags($_POST['subject']));
            $message_content = trim($_POST['message']);
            
            // Basic validation
            if (empty($subject) || empty($message_content)) {
                throw new Exception("Subject and message cannot be empty.");
            }
            
            // Validate receiver exists
            $receiver = $user->get_user_by_id($receiver_id);
            if (!$receiver) {
                throw new Exception("Invalid recipient selected.");
            }
            
            // Don't allow sending to self
            if ($receiver_id == $_SESSION['user_id']) {
                throw new Exception("You cannot send a message to yourself.");
            }
            
            // Add product ID if this is a product inquiry
            $product_id = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : null;
            
            // Send the message
            $result = $message->send_message(
                $_SESSION['user_id'], 
                $receiver_id, 
                $subject, 
                $message_content, 
                $product_id
            );
            
            if (isset($result['error'])) {
                throw new Exception($result['error']);
            }
            
            // Redirect with success message
            $success = "Message sent successfully!";
            redirect('message.php?action=sent&success=' . urlencode($success));
            
        } catch (Exception $e) {
            // Log the error
            error_log('Error sending message: ' . $e->getMessage());
            
            // Prepare data for form repopulation
            $receiver_id = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;
            $subject = isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : '';
            $message_content = isset($_POST['message']) ? $_POST['message'] : '';
            $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : null;
            
            // Get users for the dropdown
            $users = $user->get_all_users();
            
            // Set error message
            $error = $e->getMessage();
            
            // Show the form again with error
            include 'views/messages/compose.php';
        }
        break;
        
    /**
     * View a specific message
     * Marks the message as read if it's being viewed by the recipient
     */
    case 'view':
        try {
            // Validate message ID
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                throw new Exception('Invalid message ID');
            }
            
            $message_id = (int)$_GET['id'];
            
            // Get the message data
            $message_data = $message->get_message_by_id($message_id);
            
            // Check if message exists and belongs to current user
            if (!$message_data) {
                throw new Exception('Message not found');
            }
            
            if ($message_data['sender_id'] != $_SESSION['user_id'] && $message_data['receiver_id'] != $_SESSION['user_id']) {
                throw new Exception('You do not have permission to view this message');
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
            $product_data = null;
            if (!empty($message_data['related_product_id'])) {
                $product_data = $product->get_product($message_data['related_product_id']);
            }
            
            // Rename for the view to avoid conflict with the Message model
            $message_item = $message_data;
            
            // Include the view
            include 'views/messages/view.php';
            
        } catch (Exception $e) {
            error_log('Error viewing message: ' . $e->getMessage());
            $error = 'An error occurred while loading the message. ' . $e->getMessage();
            redirect('message.php?error=' . urlencode($error));
        }
        break;
        
    /**
     * Delete a message
     * Only the sender or recipient can delete their copy of the message
     */
    case 'delete':
        try {
            // Validate message ID
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                throw new Exception('Invalid message ID');
            }
            
            $message_id = (int)$_GET['id'];
            
            // Delete the message
            $result = $message->delete_message($message_id, $_SESSION['user_id']);
            
            if (isset($result['error'])) {
                throw new Exception($result['error']);
            }
            
            $success = "Message deleted successfully!";
            redirect('message.php?success=' . urlencode($success));
            
        } catch (Exception $e) {
            error_log('Error deleting message: ' . $e->getMessage());
            $error = $e->getMessage();
            redirect('message.php?error=' . urlencode($error));
        }
        break;
        
    /**
     * Default action - redirect to inbox
     */
    default:
        redirect('message.php');
        break;
}

// Note: 
// 1. Views are included within each case statement to maintain proper scope
// 2. Each view is responsible for including its own header/footer
// 3. No output should occur after this point to prevent 'headers already sent' errors
// 4. All database connections are automatically closed by PHP at script termination
?>
