<?php
/**
 * User Model
 * 
 * Handles all user-related database operations including registration, authentication,
 * profile management, and user data retrieval for the AgroSmart Market platform.
 * 
 * @package Models
 */
class User {
    /**
     * @var mysqli Database connection
     */
    private $conn;
    
    /**
     * @var array Default user types and their display names
     */
    private const USER_TYPES = [
        'farmer' => 'Farmer',
        'buyer' => 'Buyer',
        'admin' => 'Administrator'
    ];
    
    /**
     * User constructor.
     *
     * @param mysqli $db Database connection
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Register a new user
     *
     * @param string $name User's full name
     * @param string $email User's email address (must be unique)
     * @param string $password Plain text password (will be hashed)
     * @param string $user_type Type of user ('farmer', 'buyer', or 'admin')
     * @param string|null $location User's location (optional)
     * @return array Associative array with success status and user data or error message
     * @throws Exception If registration fails
     */
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
    
    /**
     * Authenticate a user
     *
     * @param string $email User's email
     * @param string $password Plain text password
     * @return array Associative array with success status and user data or error message
     * @throws Exception If login fails or account is inactive
     */
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
                
                // Update last login timestamp
                $update_sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
                $update_stmt = mysqli_prepare($this->conn, $update_sql);
                mysqli_stmt_bind_param($update_stmt, "i", $user['id']);
                mysqli_stmt_execute($update_stmt);
                
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
    
    /**
     * Check if an email address is already registered
     *
     * @param string $email Email to check
     * @return bool True if email exists, false otherwise
     */
    private function email_exists($email) {
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        return mysqli_stmt_num_rows($stmt) > 0;
    }
    
    /**
     * Get user details by user ID
     *
     * @param int $user_id User ID to retrieve
     * @return array|false Associative array of user data or false if not found
     */
    public function get_user_by_id($user_id) {
        $sql = "SELECT id, name, email, user_type, location, profile_image, bio, phone, nrc_number, literacy_level, date_joined AS registration_date, 
                last_login, email_verified, is_active 
                FROM users WHERE id = ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 1) {
            return mysqli_fetch_assoc($result);
        } else {
            return false;
        }
    }
    
    /**
     * Verify a user's email using a verification token
     *
     * @param string $token Verification token sent to user's email
     * @return bool True if verification was successful, false otherwise
     */
    public function verify_email($token) {
        $sql = "UPDATE users SET email_verified = 1 WHERE verification_token = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $token);
        
        if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Update user profile information
     *
     * @param int $user_id ID of user to update
     * @param array $data Associative array of fields to update
     * @return array Associative array with success status or error message
     * @throws Exception If update fails or user not found
     */
    public function update_profile($user_id, $data) {
        $allowed_fields = ['name', 'location', 'profile_image', 'bio', 'phone', 'email', 'nrc_number', 'literacy_level'];
        $updates = [];
        $types = "";
        $values = [];
        
        // Get current user data to check user_type
        $current_user = $this->get_user_by_id($user_id);
        if (!$current_user) {
            return ["error" => "User not found"];
        }
        
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
    
    /**
     * Get public user profile information
     *
     * @param int $user_id ID of user to retrieve
     * @return array|false Associative array of public user data or false if not found
     */
    public function get_user($user_id) {
        $sql = "SELECT id, name, email, user_type, location, profile_image, bio, phone, date_joined 
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
    
    /**
     * Get the current user's ID from session
     *
     * @return int|null User ID if logged in, null otherwise
     */
    public function get_id() {
        // If user is loaded in the session, we can get it from $_SESSION['user_id']
        if (isset($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        }
        return null;
    }
    
    /**
     * Get a list of all farmers with basic information
     *
     * @param int $limit Maximum number of farmers to return
     * @param int $offset Number of farmers to skip (for pagination)
     * @return array Array of farmer data
     */
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
    
    /**
     * Get a paginated list of all users (admin only)
     *
     * @param int $limit Maximum number of users to return
     * @param int $offset Number of users to skip (for pagination)
     * @return array Array of user data
     */
    public function get_all_users($limit = 20, $offset = 0) {
        $sql = "SELECT id, name, email, user_type, location, date_joined, is_active 
                FROM users ORDER BY date_joined DESC 
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
    
    /**
     * Update a user's active status (admin only)
     *
     * @param int $user_id ID of user to update
     * @param bool $status New status (true for active, false for suspended)
     * @return array Associative array with success status or error message
     * @throws Exception If update fails
     */
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
    
    /**
     * Get counts of users grouped by user type
     *
     * @return array Associative array with user type as key and count as value
     */
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
