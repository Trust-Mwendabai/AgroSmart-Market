<?php
class User {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Register new user
    public function register($name, $email, $password, $user_type, $location = null) {
        // Check if email already exists
        if ($this->email_exists($email)) {
            return ["error" => "Email already exists"];
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Generate verification token
        $verification_token = generate_token();
        
        // Prepare query
        $sql = "INSERT INTO users (name, email, password, user_type, location, verification_token) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssss", $name, $email, $hashed_password, $user_type, $location, $verification_token);
        
        if (mysqli_stmt_execute($stmt)) {
            $user_id = mysqli_insert_id($this->conn);
            return [
                "success" => true,
                "user_id" => $user_id,
                "verification_token" => $verification_token
            ];
        } else {
            return ["error" => "Registration failed: " . mysqli_error($this->conn)];
        }
    }
    
    // Login user
    public function login($email, $password) {
        $sql = "SELECT id, name, email, password, user_type, email_verified, is_active 
                FROM users WHERE email = ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Check if account is active
            if (!$user['is_active']) {
                return ["error" => "Your account has been suspended. Please contact admin."];
            }
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Check if email is verified
                if (!$user['email_verified']) {
                    return ["error" => "Please verify your email before logging in."];
                }
                
                // Remove password from user array
                unset($user['password']);
                
                return [
                    "success" => true,
                    "user" => $user
                ];
            } else {
                return ["error" => "Invalid password"];
            }
        } else {
            return ["error" => "Email not found"];
        }
    }
    
    // Check if email exists
    private function email_exists($email) {
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        return mysqli_stmt_num_rows($stmt) > 0;
    }
    
    // Verify email
    public function verify_email($token) {
        $sql = "UPDATE users SET email_verified = 1 WHERE verification_token = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $token);
        
        if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
            return true;
        }
        
        return false;
    }
    
    // Update profile
    public function update_profile($user_id, $data) {
        $allowed_fields = ['name', 'location', 'profile_image', 'bio', 'phone'];
        $updates = [];
        $types = "";
        $values = [];
        
        foreach ($data as $field => $value) {
            if (in_array($field, $allowed_fields)) {
                $updates[] = "$field = ?";
                $types .= "s";
                $values[] = $value;
            }
        }
        
        if (empty($updates)) {
            return ["error" => "No valid fields to update"];
        }
        
        $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
        $types .= "i";
        $values[] = $user_id;
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$values);
        
        if (mysqli_stmt_execute($stmt)) {
            return ["success" => true];
        } else {
            return ["error" => "Profile update failed: " . mysqli_error($this->conn)];
        }
    }
    
    // Get user by ID
    public function get_user($user_id) {
        $sql = "SELECT id, name, email, user_type, location, profile_image, bio, phone, date_registered 
                FROM users WHERE id = ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 1) {
            return mysqli_fetch_assoc($result);
        }
        
        return false;
    }
    
    // Get all farmers
    public function get_farmers($limit = 10, $offset = 0) {
        $sql = "SELECT id, name, location, profile_image 
                FROM users WHERE user_type = 'farmer' 
                LIMIT ? OFFSET ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $farmers = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $farmers[] = $row;
        }
        
        return $farmers;
    }
    
    // Get all users (for admin)
    public function get_all_users($limit = 20, $offset = 0) {
        $sql = "SELECT id, name, email, user_type, location, date_registered, is_active 
                FROM users ORDER BY date_registered DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
        
        return $users;
    }
    
    // Update user status (for admin)
    public function update_status($user_id, $status) {
        $sql = "UPDATE users SET is_active = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        $active = $status ? 1 : 0;
        mysqli_stmt_bind_param($stmt, "ii", $active, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            return ["success" => true];
        } else {
            return ["error" => "Status update failed: " . mysqli_error($this->conn)];
        }
    }
    
    // Count total users by type
    public function count_users_by_type() {
        $sql = "SELECT user_type, COUNT(*) as count FROM users GROUP BY user_type";
        $result = mysqli_query($this->conn, $sql);
        
        $counts = [
            'farmer' => 0,
            'buyer' => 0,
            'admin' => 0
        ];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $counts[$row['user_type']] = $row['count'];
        }
        
        return $counts;
    }
}
?>
