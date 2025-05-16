<?php
class Product {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Add new product
    public function add_product($farmer_id, $name, $description, $price, $image, $category, $stock) {
        $sql = "INSERT INTO products (farmer_id, name, description, price, image, category, stock) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "issdssi", $farmer_id, $name, $description, $price, $image, $category, $stock);
        
        if (mysqli_stmt_execute($stmt)) {
            return [
                "success" => true,
                "product_id" => mysqli_insert_id($this->conn)
            ];
        } else {
            return ["error" => "Failed to add product: " . mysqli_error($this->conn)];
        }
    }
    
    // Update product
    public function update_product($product_id, $farmer_id, $data) {
        // First check if product belongs to farmer
        if (!$this->is_product_owner($product_id, $farmer_id)) {
            return ["error" => "You don't have permission to edit this product"];
        }
        
        $allowed_fields = ['name', 'description', 'price', 'image', 'category', 'stock'];
        $updates = [];
        $types = "";
        $values = [];
        
        foreach ($data as $field => $value) {
            if (in_array($field, $allowed_fields)) {
                $updates[] = "$field = ?";
                if ($field === 'price' || $field === 'stock') {
                    $types .= ($field === 'price') ? "d" : "i";
                } else {
                    $types .= "s";
                }
                $values[] = $value;
            }
        }
        
        if (empty($updates)) {
            return ["error" => "No valid fields to update"];
        }
        
        $sql = "UPDATE products SET " . implode(", ", $updates) . " WHERE id = ? AND farmer_id = ?";
        $types .= "ii";
        $values[] = $product_id;
        $values[] = $farmer_id;
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$values);
        
        if (mysqli_stmt_execute($stmt)) {
            return ["success" => true];
        } else {
            return ["error" => "Product update failed: " . mysqli_error($this->conn)];
        }
    }
    
    // Delete product
    public function delete_product($product_id, $farmer_id) {
        // First check if product belongs to farmer
        if (!$this->is_product_owner($product_id, $farmer_id)) {
            return ["error" => "You don't have permission to delete this product"];
        }
        
        $sql = "DELETE FROM products WHERE id = ? AND farmer_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $product_id, $farmer_id);
        
        if (mysqli_stmt_execute($stmt)) {
            return ["success" => true];
        } else {
            return ["error" => "Failed to delete product: " . mysqli_error($this->conn)];
        }
    }
    
    // Check if user is product owner
    private function is_product_owner($product_id, $farmer_id) {
        $sql = "SELECT id FROM products WHERE id = ? AND farmer_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $product_id, $farmer_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        return mysqli_stmt_num_rows($stmt) > 0;
    }
    
    // Get product by ID
    public function get_product($product_id) {
        $sql = "SELECT p.*, u.name as farmer_name, u.location as farmer_location 
                FROM products p
                JOIN users u ON p.farmer_id = u.id
                WHERE p.id = ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 1) {
            return mysqli_fetch_assoc($result);
        }
        
        return false;
    }
    
    // Get all products
    public function get_all_products($limit = 12, $offset = 0, $filters = []) {
        $where_clauses = [];
        $types = "";
        $params = [];
        
        // Apply filters if provided
        if (!empty($filters)) {
            // Category filter
            if (isset($filters['category']) && $filters['category']) {
                $where_clauses[] = "p.category = ?";
                $types .= "s";
                $params[] = $filters['category'];
            }
            
            // Location filter
            if (isset($filters['location']) && $filters['location']) {
                $where_clauses[] = "u.location LIKE ?";
                $types .= "s";
                $params[] = "%" . $filters['location'] . "%";
            }
            
            // Price range filter
            if (isset($filters['min_price']) && $filters['min_price'] !== '') {
                $where_clauses[] = "p.price >= ?";
                $types .= "d";
                $params[] = $filters['min_price'];
            }
            
            if (isset($filters['max_price']) && $filters['max_price'] !== '') {
                $where_clauses[] = "p.price <= ?";
                $types .= "d";
                $params[] = $filters['max_price'];
            }
            
            // Search term
            if (isset($filters['search']) && $filters['search']) {
                $where_clauses[] = "(p.name LIKE ? OR p.description LIKE ?)";
                $types .= "ss";
                $search_term = "%" . $filters['search'] . "%";
                $params[] = $search_term;
                $params[] = $search_term;
            }
        }
        
        // Build WHERE clause
        $where_sql = "";
        if (!empty($where_clauses)) {
            $where_sql = "WHERE " . implode(" AND ", $where_clauses);
        }
        
        // Build query
        $sql = "SELECT p.*, u.name as farmer_name, u.location as farmer_location 
                FROM products p
                JOIN users u ON p.farmer_id = u.id
                $where_sql
                ORDER BY p.date_added DESC
                LIMIT ? OFFSET ?";
        
        $types .= "ii";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = mysqli_prepare($this->conn, $sql);
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
        
        return $products;
    }
    
    // Get products by farmer
    public function get_farmer_products($farmer_id) {
        $sql = "SELECT * FROM products WHERE farmer_id = ? ORDER BY date_added DESC";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $farmer_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
        
        return $products;
    }
    
    // Count total products
    public function count_products() {
        $sql = "SELECT COUNT(*) as total FROM products";
        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
    
    // Get product categories
    public function get_categories() {
        $sql = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != ''";
        $result = mysqli_query($this->conn, $sql);
        
        $categories = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row['category'];
        }
        
        return $categories;
    }
}
?>
