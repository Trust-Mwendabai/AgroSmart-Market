<?php
class Message {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Send a new message
    public function send_message($sender_id, $receiver_id, $subject, $message, $product_id = null) {
        $sql = "INSERT INTO messages (sender_id, receiver_id, subject, message, related_product_id, date_sent) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "iissi", $sender_id, $receiver_id, $subject, $message, $product_id);
        
        if (mysqli_stmt_execute($stmt)) {
            return [
                "success" => true,
                "message_id" => mysqli_insert_id($this->conn)
            ];
        } else {
            return ["error" => "Failed to send message: " . mysqli_error($this->conn)];
        }
    }
    
    // Get inbox messages
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
    
    // Get sent messages
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
    
    // Get a single message
    public function get_message($message_id, $user_id) {
        return $this->get_message_by_id($message_id);
    }
    
    // Get a message by ID (alias for compatibility with controller)
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
    
    // Mark message as read
    public function mark_as_read($message_id) {
        $sql = "UPDATE messages SET is_read = 1 WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $message_id);
        mysqli_stmt_execute($stmt);
    }
    
    // Delete a message
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
    
    // Count unread messages
    public function count_unread_messages($user_id) {
        $sql = "SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND is_read = 0";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        return $row['count'];
    }
    
    // Get conversation messages between two users
    public function get_conversation_messages($sender_id, $receiver_id, $exclude_id = null) {
        $sql = "SELECT m.*, 
                sender.name as sender_name, sender.user_type as sender_type,
                receiver.name as receiver_name, receiver.user_type as receiver_type
                FROM messages m 
                JOIN users sender ON m.sender_id = sender.id 
                JOIN users receiver ON m.receiver_id = receiver.id 
                WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)";
        
        if ($exclude_id) {
            $sql .= " AND m.id != ?";
        }
        
        $sql .= " ORDER BY m.date_sent DESC LIMIT 10";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        
        if ($exclude_id) {
            mysqli_stmt_bind_param($stmt, "iiiii", $sender_id, $receiver_id, $receiver_id, $sender_id, $exclude_id);
        } else {
            mysqli_stmt_bind_param($stmt, "iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $messages = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $messages[] = $row;
        }
        
        return $messages;
    }
}
?>
