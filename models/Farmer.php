<?php
class Farmer {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Get farmer profile by ID
    public function get_farmer_by_id($farmer_id) {
        $sql = "SELECT u.*, f.farm_size, f.farming_type, f.years_farming, f.bio, 
                       f.profile_visibility, f.gps_coordinates, f.storage_facility,
                       f.preferred_contact_method, f.verified
                FROM users u
                LEFT JOIN farmer_profiles f ON u.id = f.user_id
                WHERE u.id = ? AND u.user_type = 'farmer'";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $farmer_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 1) {
            return mysqli_fetch_assoc($result);
        }
        
        return null;
    }
    
    // Create or update farmer profile
    public function update_profile($user_id, $data) {
        // Check if profile exists
        $sql = "SELECT * FROM farmer_profiles WHERE user_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 0) {
            // Create new profile
            $sql = "INSERT INTO farmer_profiles (
                        user_id, farm_size, farming_type, years_farming, bio, 
                        profile_visibility, gps_coordinates, storage_facility,
                        preferred_contact_method, verified
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param(
                $stmt, 
                "issisisssi", 
                $user_id, 
                $data['farm_size'], 
                $data['farming_type'], 
                $data['years_farming'], 
                $data['bio'], 
                $data['profile_visibility'], 
                $data['gps_coordinates'], 
                $data['storage_facility'], 
                $data['preferred_contact_method'], 
                $data['verified']
            );
            
            if (mysqli_stmt_execute($stmt)) {
                return ["success" => true, "message" => "Profile created successfully"];
            } else {
                return ["error" => "Failed to create profile: " . mysqli_error($this->conn)];
            }
        } else {
            // Update existing profile
            $updates = [];
            $types = "";
            $values = [];
            
            foreach ($data as $field => $value) {
                $updates[] = "$field = ?";
                
                if ($field === 'years_farming' || $field === 'verified') {
                    $types .= "i"; // Integer fields
                } else {
                    $types .= "s"; // String fields
                }
                
                $values[] = $value;
            }
            
            if (empty($updates)) {
                return ["error" => "No data to update"];
            }
            
            $updates_str = implode(", ", $updates);
            $sql = "UPDATE farmer_profiles SET $updates_str WHERE user_id = ?";
            
            $types .= "i"; // For user_id
            $values[] = $user_id;
            
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, $types, ...$values);
            
            if (mysqli_stmt_execute($stmt)) {
                return ["success" => true, "message" => "Profile updated successfully"];
            } else {
                return ["error" => "Failed to update profile: " . mysqli_error($this->conn)];
            }
        }
    }
    
    // Get all farmers with pagination
    public function get_all_farmers($limit = 10, $offset = 0, $filters = []) {
        $where_conditions = ["u.user_type = 'farmer'"];
        $params = [];
        $types = "";
        
        // Add filters if provided
        if (!empty($filters['location'])) {
            $where_conditions[] = "u.location LIKE ?";
            $params[] = "%" . $filters['location'] . "%";
            $types .= "s";
        }
        
        if (!empty($filters['farming_type'])) {
            $where_conditions[] = "f.farming_type = ?";
            $params[] = $filters['farming_type'];
            $types .= "s";
        }
        
        if (isset($filters['verified']) && is_bool($filters['verified'])) {
            $where_conditions[] = "f.verified = ?";
            $params[] = $filters['verified'] ? 1 : 0;
            $types .= "i";
        }
        
        if (!empty($filters['search'])) {
            $where_conditions[] = "(u.name LIKE ? OR u.description LIKE ?)";
            $params[] = "%" . $filters['search'] . "%";
            $params[] = "%" . $filters['search'] . "%";
            $types .= "ss";
        }
        
        $where_clause = implode(" AND ", $where_conditions);
        
        $sql = "SELECT u.id, u.name, u.email, u.phone, u.location, u.image, 
                       f.farm_size, f.farming_type, f.years_farming, f.verified,
                       (SELECT COUNT(*) FROM products p WHERE p.farmer_id = u.id) as product_count,
                       (SELECT AVG(rating) FROM farmer_ratings r WHERE r.farmer_id = u.id) as avg_rating
                FROM users u
                LEFT JOIN farmer_profiles f ON u.id = f.user_id
                WHERE $where_clause
                ORDER BY u.name ASC
                LIMIT ? OFFSET ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        
        // Add limit and offset parameters
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";
        
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $farmers = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $farmers[] = $row;
        }
        
        return $farmers;
    }
    
    // Count total farmers based on filters
    public function count_farmers($filters = []) {
        $where_conditions = ["u.user_type = 'farmer'"];
        $params = [];
        $types = "";
        
        // Add filters if provided (same as get_all_farmers)
        if (!empty($filters['location'])) {
            $where_conditions[] = "u.location LIKE ?";
            $params[] = "%" . $filters['location'] . "%";
            $types .= "s";
        }
        
        if (!empty($filters['farming_type'])) {
            $where_conditions[] = "f.farming_type = ?";
            $params[] = $filters['farming_type'];
            $types .= "s";
        }
        
        if (isset($filters['verified']) && is_bool($filters['verified'])) {
            $where_conditions[] = "f.verified = ?";
            $params[] = $filters['verified'] ? 1 : 0;
            $types .= "i";
        }
        
        if (!empty($filters['search'])) {
            $where_conditions[] = "(u.name LIKE ? OR u.description LIKE ?)";
            $params[] = "%" . $filters['search'] . "%";
            $params[] = "%" . $filters['search'] . "%";
            $types .= "ss";
        }
        
        $where_clause = implode(" AND ", $where_conditions);
        
        $sql = "SELECT COUNT(*) as total
                FROM users u
                LEFT JOIN farmer_profiles f ON u.id = f.user_id
                WHERE $where_clause";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        return $row['total'];
    }
    
    // Generate QR code for farmer profile
    public function generate_qr_code($farmer_id) {
        // Check if farmer exists
        $farmer = $this->get_farmer_by_id($farmer_id);
        
        if (!$farmer) {
            return ["error" => "Farmer not found"];
        }
        
        // Generate QR code data
        $qr_data = [
            'id' => $farmer_id,
            'name' => $farmer['name'],
            'location' => $farmer['location'],
            'phone' => $farmer['phone'],
            'url' => "https://" . $_SERVER['HTTP_HOST'] . "/farmer-profile.php?id=" . $farmer_id
        ];
        
        // Convert to JSON for QR code content
        $qr_content = json_encode($qr_data);
        
        // The filename will be handled by the QR code generator in the controller
        return ["success" => true, "qr_content" => $qr_content, "farmer" => $farmer];
    }
    
    // Add a rating for a farmer
    public function add_rating($farmer_id, $buyer_id, $rating, $comment = null) {
        // Check if farmer exists
        $farmer = $this->get_farmer_by_id($farmer_id);
        
        if (!$farmer) {
            return ["error" => "Farmer not found"];
        }
        
        // Check if buyer has ordered from this farmer before
        $sql = "SELECT COUNT(*) as orders FROM orders WHERE buyer_id = ? AND farmer_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $buyer_id, $farmer_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        if ($row['orders'] == 0) {
            return ["error" => "You must purchase from this farmer before leaving a rating"];
        }
        
        // Check for existing rating
        $sql = "SELECT * FROM farmer_ratings WHERE farmer_id = ? AND buyer_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $farmer_id, $buyer_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            // Update existing rating
            $sql = "UPDATE farmer_ratings SET rating = ?, comment = ?, updated_at = NOW() WHERE farmer_id = ? AND buyer_id = ?";
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "isii", $rating, $comment, $farmer_id, $buyer_id);
        } else {
            // Add new rating
            $sql = "INSERT INTO farmer_ratings (farmer_id, buyer_id, rating, comment) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "iiis", $farmer_id, $buyer_id, $rating, $comment);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            return ["success" => true, "message" => "Rating submitted successfully"];
        } else {
            return ["error" => "Failed to submit rating: " . mysqli_error($this->conn)];
        }
    }
    
    // Get ratings for a farmer
    public function get_farmer_ratings($farmer_id, $limit = 10, $offset = 0) {
        $sql = "SELECT r.*, u.name as buyer_name, u.image as buyer_image
                FROM farmer_ratings r
                JOIN users u ON r.buyer_id = u.id
                WHERE r.farmer_id = ?
                ORDER BY r.created_at DESC
                LIMIT ? OFFSET ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $farmer_id, $limit, $offset);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $ratings = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $ratings[] = $row;
        }
        
        return $ratings;
    }
}
?>
