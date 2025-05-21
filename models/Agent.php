<?php
class Agent {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Get agent profile by ID
    public function get_agent_by_id($agent_id) {
        $sql = "SELECT u.*, a.organization, a.service_area, a.verification_status, 
                       a.bio, a.service_type, a.languages_spoken
                FROM users u
                LEFT JOIN agent_profiles a ON u.id = a.user_id
                WHERE u.id = ? AND u.user_type = 'agent'";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $agent_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 1) {
            return mysqli_fetch_assoc($result);
        }
        
        return null;
    }
    
    // Register a farmer through an agent
    public function register_farmer($farmer_data, $agent_id) {
        // Start transaction
        mysqli_begin_transaction($this->conn);
        
        try {
            // Create user account for farmer
            $sql = "INSERT INTO users (name, email, phone, location, password, user_type, registered_by) 
                    VALUES (?, ?, ?, ?, ?, 'farmer', ?)";
            
            // Generate random password if not provided
            if (empty($farmer_data['password'])) {
                $password = substr(md5(rand()), 0, 8); // Simple random password
                $farmer_data['password'] = password_hash($password, PASSWORD_DEFAULT);
                $farmer_data['generated_password'] = $password; // Store for later use
            } else {
                $farmer_data['password'] = password_hash($farmer_data['password'], PASSWORD_DEFAULT);
            }
            
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param(
                $stmt, 
                "sssssi", 
                $farmer_data['name'],
                $farmer_data['email'],
                $farmer_data['phone'],
                $farmer_data['location'],
                $farmer_data['password'],
                $agent_id
            );
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to create farmer account: " . mysqli_error($this->conn));
            }
            
            $farmer_id = mysqli_insert_id($this->conn);
            
            // Create farmer profile
            $sql = "INSERT INTO farmer_profiles (
                        user_id, farm_size, farming_type, years_farming, bio, 
                        gps_coordinates, storage_facility, verified
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, 0)";
            
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param(
                $stmt, 
                "issssss", 
                $farmer_id,
                $farmer_data['farm_size'] ?? '',
                $farmer_data['farming_type'] ?? '',
                $farmer_data['years_farming'] ?? 0,
                $farmer_data['bio'] ?? '',
                $farmer_data['gps_coordinates'] ?? '',
                $farmer_data['storage_facility'] ?? ''
            );
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to create farmer profile: " . mysqli_error($this->conn));
            }
            
            // Log the registration
            $sql = "INSERT INTO registration_logs (farmer_id, agent_id, registration_date) VALUES (?, ?, NOW())";
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $farmer_id, $agent_id);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to log registration: " . mysqli_error($this->conn));
            }
            
            // Commit transaction
            mysqli_commit($this->conn);
            
            return [
                "success" => true, 
                "farmer_id" => $farmer_id, 
                "message" => "Farmer registered successfully",
                "generated_password" => $farmer_data['generated_password'] ?? null
            ];
            
        } catch (Exception $e) {
            // Rollback transaction on error
            mysqli_rollback($this->conn);
            return ["error" => $e->getMessage()];
        }
    }
    
    // Get all farmers registered by an agent
    public function get_registered_farmers($agent_id, $limit = 20, $offset = 0) {
        $sql = "SELECT u.id, u.name, u.email, u.phone, u.location, u.image, u.created_at,
                       f.farm_size, f.farming_type, f.years_farming, f.verified,
                       (SELECT COUNT(*) FROM products WHERE farmer_id = u.id) as product_count
                FROM users u
                LEFT JOIN farmer_profiles f ON u.id = f.user_id
                WHERE u.registered_by = ? AND u.user_type = 'farmer'
                ORDER BY u.created_at DESC
                LIMIT ? OFFSET ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $agent_id, $limit, $offset);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $farmers = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $farmers[] = $row;
        }
        
        return $farmers;
    }
    
    // Count farmers registered by an agent
    public function count_registered_farmers($agent_id) {
        $sql = "SELECT COUNT(*) as total 
                FROM users 
                WHERE registered_by = ? AND user_type = 'farmer'";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $agent_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        return $row['total'];
    }
    
    // Add a product for a farmer
    public function add_product_for_farmer($product_data, $farmer_id, $agent_id) {
        // Check if agent registered this farmer
        $sql = "SELECT * FROM users WHERE id = ? AND registered_by = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $farmer_id, $agent_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 0) {
            return ["error" => "You are not authorized to add products for this farmer"];
        }
        
        // Insert product
        $sql = "INSERT INTO products (
                    name, description, price, stock, category_id,
                    unit, image, farmer_id, added_by, is_organic,
                    harvest_date, expiry_date
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param(
            $stmt, 
            "ssdiisississ", 
            $product_data['name'],
            $product_data['description'],
            $product_data['price'],
            $product_data['stock'],
            $product_data['category_id'],
            $product_data['unit'],
            $product_data['image'],
            $farmer_id,
            $agent_id,
            $product_data['is_organic'] ? 1 : 0,
            $product_data['harvest_date'],
            $product_data['expiry_date']
        );
        
        if (mysqli_stmt_execute($stmt)) {
            $product_id = mysqli_insert_id($this->conn);
            
            // Log the product addition
            $sql = "INSERT INTO product_logs (product_id, agent_id, farmer_id, action, action_date) 
                    VALUES (?, ?, ?, 'add', NOW())";
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "iii", $product_id, $agent_id, $farmer_id);
            mysqli_stmt_execute($stmt);
            
            return ["success" => true, "product_id" => $product_id, "message" => "Product added successfully"];
        } else {
            return ["error" => "Failed to add product: " . mysqli_error($this->conn)];
        }
    }
    
    // Get agent activity metrics
    public function get_activity_metrics($agent_id) {
        $metrics = [];
        
        // Farmers registered this month
        $sql = "SELECT COUNT(*) as total FROM users 
                WHERE registered_by = ? AND user_type = 'farmer' 
                AND MONTH(created_at) = MONTH(CURRENT_DATE()) 
                AND YEAR(created_at) = YEAR(CURRENT_DATE())";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $agent_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $metrics['farmers_this_month'] = mysqli_fetch_assoc($result)['total'];
        
        // Products added this month
        $sql = "SELECT COUNT(*) as total FROM products 
                WHERE added_by = ? 
                AND MONTH(created_at) = MONTH(CURRENT_DATE()) 
                AND YEAR(created_at) = YEAR(CURRENT_DATE())";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $agent_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $metrics['products_this_month'] = mysqli_fetch_assoc($result)['total'];
        
        // Orders facilitated (from farmers registered by this agent)
        $sql = "SELECT COUNT(*) as total FROM orders o
                JOIN users f ON o.farmer_id = f.id
                WHERE f.registered_by = ?
                AND MONTH(o.created_at) = MONTH(CURRENT_DATE()) 
                AND YEAR(o.created_at) = YEAR(CURRENT_DATE())";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $agent_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $metrics['orders_this_month'] = mysqli_fetch_assoc($result)['total'];
        
        // Total sales value
        $sql = "SELECT SUM(o.total_amount) as total FROM orders o
                JOIN users f ON o.farmer_id = f.id
                WHERE f.registered_by = ?
                AND MONTH(o.created_at) = MONTH(CURRENT_DATE()) 
                AND YEAR(o.created_at) = YEAR(CURRENT_DATE())";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $agent_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $metrics['sales_value_this_month'] = mysqli_fetch_assoc($result)['total'] ?? 0;
        
        return $metrics;
    }
}
?>
