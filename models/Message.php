<?php
/**
 * Message Model
 * 
 * Handles all database operations related to user messages and conversations
 * in the AgroSmart Market platform. This includes sending, retrieving,
 * and managing messages between users, with support for product-related conversations.
 *
 * @package Models
 * @version 1.0.0
 * @since 2024-05-23
 */
class Message {
    /**
     * Database connection
     *
     * @var mysqli Database connection for message operations
     */
    private $conn;
    
    /**
     * @var int Default limit for message retrieval
     */
    private const DEFAULT_MESSAGE_LIMIT = 50;
    
    /**
     * @var array Message status constants
     */
    private const MESSAGE_STATUS = [
        'unread' => 0,
        'read' => 1,
        'archived' => 2,
        'deleted' => 3
    ];
    
    /**
     * @var array Message types
     */
    private const MESSAGE_TYPES = [
        'general' => 'General Inquiry',
        'product' => 'Product Inquiry',
        'order' => 'Order Related',
        'support' => 'Support Request'
    ];
    
    /**
     * Initialize the Message model with a database connection
     *
     * @param mysqli $db Database connection
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Send a new message between users
     *
     * @param int $sender_id The ID of the user sending the message
     * @param int $receiver_id The ID of the user receiving the message
     * @param string $subject The message subject
     * @param string $message The message content (max 2000 characters)
     * @param int|null $product_id Optional related product ID for product-specific conversations
     * @return array Result with success status and message ID or error details
     * @throws Exception If message sending fails
     */
    public function send_message($sender_id, $receiver_id, $subject, $message, $product_id = null) {
        // Check if related_product_id column exists in the messages table
        $check_column = mysqli_query($this->conn, "SHOW COLUMNS FROM messages LIKE 'related_product_id'");
        
        if (mysqli_num_rows($check_column) > 0) {
            // Column exists, use it
            $sql = "INSERT INTO messages (sender_id, receiver_id, subject, message, related_product_id, date_sent) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "iissi", $sender_id, $receiver_id, $subject, $message, $product_id);
        } else {
            // Column doesn't exist, skip it
            $sql = "INSERT INTO messages (sender_id, receiver_id, subject, message, date_sent) 
                    VALUES (?, ?, ?, ?, NOW())";
            
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "iiss", $sender_id, $receiver_id, $subject, $message);
            
            // Try to add the column for future use
            $alter_query = "ALTER TABLE messages ADD COLUMN related_product_id INT NULL DEFAULT NULL";
            mysqli_query($this->conn, $alter_query);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            return [
                "success" => true,
                "message_id" => mysqli_insert_id($this->conn)
            ];
        } else {
            return ["error" => "Failed to send message: " . mysqli_error($this->conn)];
        }
    }
    
    /**
     * Retrieve all messages in a user's inbox
     *
     * @param int $user_id The ID of the user whose inbox to retrieve
     * @param int $limit Maximum number of messages to return (default: 50)
     * @param int $offset Number of messages to skip (for pagination)
     * @return array List of messages with sender information, ordered by date (newest first)
     */
    public function get_inbox_messages($user_id) {
        $sql = "SELECT m.*, u.name as sender_name, u.profile_image as sender_image, 
                u.user_type as sender_type
                FROM messages m
                JOIN users u ON m.sender_id = u.id
                WHERE m.receiver_id = ?
                ORDER BY m.date_sent DESC";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $messages = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $messages[] = $row;
        }
        
        return $messages;
    }
    
    /**
     * Retrieve all messages sent by a user
     *
     * @param int $user_id The ID of the user whose sent messages to retrieve
     * @param int $limit Maximum number of messages to return (default: 50)
     * @param int $offset Number of messages to skip (for pagination)
     * @return array List of sent messages with recipient information, ordered by date (newest first)
     */
    public function get_sent_messages($user_id) {
        $sql = "SELECT m.*, u.name as receiver_name, u.profile_image as receiver_image, 
                u.user_type as receiver_type
                FROM messages m
                JOIN users u ON m.receiver_id = u.id
                WHERE m.sender_id = ?
                ORDER BY m.date_sent DESC";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $messages = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $messages[] = $row;
        }
        
        return $messages;
    }
    
    /**
     * Get a single message by ID (alias for compatibility)
     *
     * @param int $message_id The ID of the message to retrieve
     * @param int $user_id The ID of the user requesting the message (for permission check)
     * @return array|bool Message details or false if not found/not authorized
     * @see get_message_by_id()
     */
    public function get_message($message_id, $user_id) {
        // First verify the user has permission to view this message
        $sql = "SELECT id FROM messages WHERE id = ? AND (sender_id = ? OR receiver_id = ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $message_id, $user_id, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            return $this->get_message_by_id($message_id);
        }
        
        return false;
    }
    
    /**
     * Get a message by its ID with full details
     *
     * @param int $message_id The ID of the message to retrieve
     * @return array|bool Message details with sender and receiver info, or false if not found
     * @throws Exception If database error occurs
     */
    public function get_message_by_id($message_id) {
        // Get full message details including sender and receiver info
        $sql = "SELECT m.*, 
                sender.name as sender_name, sender.profile_image as sender_image, sender.user_type as sender_type, sender.location as sender_location,
                receiver.name as receiver_name, receiver.profile_image as receiver_image, receiver.user_type as receiver_type, receiver.location as receiver_location
                FROM messages m
                JOIN users sender ON m.sender_id = sender.id
                JOIN users receiver ON m.receiver_id = receiver.id
                WHERE m.id = ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $message_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 1) {
            $message = mysqli_fetch_assoc($result);
            return $message;
        }
        
        return false;
    }
    
    /**
     * Mark a message as read
     *
     * @param int $message_id The ID of the message to mark as read
     * @param int $user_id The ID of the user marking the message as read (must be the receiver)
     * @return bool True if update was successful, false otherwise
     */
    public function mark_as_read($message_id, $user_id) {
        $sql = "UPDATE messages SET is_read = 1, date_read = NOW() WHERE id = ? AND receiver_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $message_id, $user_id);
        return mysqli_stmt_execute($stmt);
    }
    
    /**
     * Delete a message
     *
     * @param int $message_id The ID of the message to delete
     * @param int $user_id The ID of the user attempting to delete the message
     * @return array Result with success/error message
     */
    public function delete_message($message_id, $user_id) {
        // First check if the message belongs to the user
        $sql = "SELECT * FROM messages WHERE id = ? AND (sender_id = ? OR receiver_id = ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $message_id, $user_id, $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 0) {
            return ["error" => "Message not found or you don't have permission to delete it."];
        }
        
        // Delete the message
        $sql = "DELETE FROM messages WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $message_id);
        
        if (mysqli_stmt_execute($stmt)) {
            return ["success" => "Message deleted successfully."];
        } else {
            return ["error" => "Failed to delete message: " . mysqli_error($this->conn)];
        }
    }
    
    /**
     * Count the number of unread messages for a user
     *
     * @param int $user_id The ID of the user
     * @param int|null $sender_id Optional sender ID to count unread messages from a specific user
     * @return int Number of unread messages
     * @throws Exception If database error occurs
     */
    public function count_unread_messages($user_id, $sender_id = null) {
        $sql = "SELECT COUNT(*) as count FROM messages 
                WHERE receiver_id = ? AND is_read = 0";
        
        $params = [$user_id];
        $types = "i";
        
        if ($sender_id !== null) {
            $sql .= " AND sender_id = ?";
            $params[] = $sender_id;
            $types .= "i";
        }
        
        $stmt = mysqli_prepare($this->conn, $sql);
        
        if (count($params) > 1) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        } else {
            mysqli_stmt_bind_param($stmt, $types, $params[0]);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        return (int)$row['count'];
    }
    
    /**
     * Get conversation messages between two users
     *
     * @param int $user1_id The ID of the first user in the conversation
     * @param int $user2_id The ID of the second user in the conversation
     * @param int|null $limit Maximum number of messages to return (default: 50)
     * @param int $offset Number of messages to skip (for pagination)
     * @param int|null $exclude_id Optional message ID to exclude from results
     * @return array List of messages in the conversation, ordered by date (newest first)
     * @throws Exception If database error occurs
     */
    public function get_conversation_messages($user1_id, $user2_id, $limit = null, $offset = 0, $exclude_id = null) {
        // Ensure we have valid parameters
        $limit = is_numeric($limit) ? (int)$limit : self::DEFAULT_MESSAGE_LIMIT;
        $offset = max(0, (int)$offset);
        
        $sql = "SELECT m.*, 
                sender.name as sender_name, 
                sender.user_type as sender_type,
                sender.profile_image as sender_avatar,
                receiver.name as receiver_name, 
                receiver.user_type as receiver_type,
                p.name as product_name,
                p.image as product_image
                FROM messages m 
                JOIN users sender ON m.sender_id = sender.id 
                JOIN users receiver ON m.receiver_id = receiver.id
                LEFT JOIN products p ON m.related_product_id = p.id
                WHERE ((m.sender_id = ? AND m.receiver_id = ?) 
                      OR (m.sender_id = ? AND m.receiver_id = ?))";
        
        $params = [$user1_id, $user2_id, $user2_id, $user1_id];
        $types = "iiii";
        
        if ($exclude_id) {
            $sql .= " AND m.id != ?";
            $params[] = $exclude_id;
            $types .= "i";
        }
        
        $sql .= " ORDER BY m.date_sent DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result === false) {
            throw new Exception("Failed to fetch conversation: " . mysqli_error($this->conn));
        }
        
        $messages = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $messages[] = $row;
        }
        
        return $messages;
    }
    
    /**
     * Get a list of users who have messaged or been messaged by the current user
     *
     * @param int $user_id The ID of the current user
     * @return array List of users with message previews and unread counts
     * @throws Exception If database error occurs
     */
    public function get_conversation_list($user_id) {
        // First, get the most recent message from each conversation
        $sql = "SELECT 
                    m1.*,
                    CASE 
                        WHEN m1.sender_id = ? THEN r.id
                        ELSE s.id
                    END as other_user_id,
                    CASE 
                        WHEN m1.sender_id = ? THEN r.name
                        ELSE s.name
                    END as other_user_name,
                    CASE 
                        WHEN m1.sender_id = ? THEN r.profile_image
                        ELSE s.profile_image
                    END as other_user_avatar,
                    CASE 
                        WHEN m1.sender_id = ? THEN r.user_type
                        ELSE s.user_type
                    END as other_user_type,
                    (SELECT COUNT(*) FROM messages m2 
                     WHERE ((m2.sender_id = m1.sender_id AND m2.receiver_id = m1.receiver_id) 
                            OR (m2.sender_id = m1.receiver_id AND m2.receiver_id = m1.sender_id))
                     AND m2.is_read = 0 AND m2.receiver_id = ?) as unread_count
                FROM messages m1
                LEFT JOIN users s ON m1.sender_id = s.id
                LEFT JOIN users r ON m1.receiver_id = r.id
                WHERE m1.id IN (
                    SELECT MAX(id)
                    FROM messages 
                    WHERE sender_id = ? OR receiver_id = ?
                    GROUP BY LEAST(sender_id, receiver_id), GREATEST(sender_id, receiver_id)
                )
                AND (m1.sender_id = ? OR m1.receiver_id = ?)
                ORDER BY m1.date_sent DESC";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiiiiiii", 
            $user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result === false) {
            throw new Exception("Failed to fetch conversation list: " . mysqli_error($this->conn));
        }
        
        $conversations = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $conversations[] = $row;
        }
        
        return $conversations;
    }
}
?>
