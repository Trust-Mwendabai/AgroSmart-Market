<?php
/**
 * Product Model
 * 
 * Handles all product-related database operations including CRUD operations,
 * product search, filtering, and retrieval for the AgroSmart Market platform.
 * 
 * @package Models
 */
class Product {
    /**
     * @var mysqli Database connection
     */
    private $conn;
    
    /**
     * @var array Default product categories
     */
    private const DEFAULT_CATEGORIES = [
        'Vegetables', 'Fruits', 'Grains', 'Dairy', 'Meat', 'Poultry',
        'Seafood', 'Herbs', 'Spices', 'Nuts', 'Seeds', 'Other'
    ];
    
    /**
     * @var array Default sort options for product listings
     */
    private const SORT_OPTIONS = [
        'newest' => 'Newest First',
        'price_low' => 'Price: Low to High',
        'price_high' => 'Price: High to Low',
        'name_asc' => 'Name: A to Z',
        'name_desc' => 'Name: Z to A',
        'rating' => 'Highest Rated',
        'popularity' => 'Most Popular'
    ];
    
    /**
     * Product constructor.
     *
     * @param mysqli $db Database connection
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Add a new product to the marketplace
     *
     * @param int $farmer_id ID of the farmer adding the product
     * @param string $name Product name
     * @param string $description Product description
     * @param float $price Product price
     * @param string|null $image Path to product image (optional)
     * @param string $category Product category
     * @param int $stock Available stock quantity
     * @return array Result with success status and product ID or error message
     * @throws Exception If validation fails or database error occurs
     */
    public function add_product($farmer_id, $name, $description, $price, $image, $category, $stock) {
        // Check if farmer has completed their profile
        $sql = "SELECT nrc_number, literacy_level FROM users WHERE id = ? AND user_type = 'farmer'";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $farmer_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $farmer = mysqli_fetch_assoc($result);
        
        if (!$farmer || empty($farmer['nrc_number']) || empty($farmer['literacy_level'])) {
            return ["error" => "Please complete your profile before adding products"];
        }
        
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
    
    /**
     * Update an existing product
     *
     * @param int $product_id ID of the product to update
     * @param int $farmer_id ID of the farmer who owns the product
     * @param array $data Associative array of fields to update
     * @return array Result with success status or error message
     * @throws Exception If user doesn't have permission or validation fails
     */
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
    
    /**
     * Delete a product
     *
     * @param int $product_id ID of the product to delete
     * @param int $farmer_id ID of the farmer who owns the product
     * @return array Result with success status or error message
     * @throws Exception If user doesn't have permission or deletion fails
     */
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
    
    /**
     * Check if a user is the owner of a product
     *
     * @param int $product_id ID of the product to check
     * @param int $farmer_id ID of the farmer to verify
     * @return bool True if the user owns the product, false otherwise
     */
    private function is_product_owner($product_id, $farmer_id) {
        $sql = "SELECT id FROM products WHERE id = ? AND farmer_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $product_id, $farmer_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        return mysqli_stmt_num_rows($stmt) > 0;
    }
    
    /**
     * Get product details by ID
     *
     * @param int $product_id ID of the product to retrieve
     * @return array|false Associative array of product data or false if not found
     */
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
    
    /**
     * Get a paginated list of all products with optional filters
     *
     * @param int $limit Maximum number of products to return (default: 12)
     * @param int $offset Number of products to skip (for pagination) (default: 0)
     * @param array $filters Optional filters to apply (category, location, price range, etc.)
     * @return array List of products with farmer information
     */
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
                ORDER BY 
                    CASE 
                        WHEN p.image IS NOT NULL AND p.image != '' THEN 0 
                        ELSE 1 
                    END, 
                    p.date_added DESC
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
    
    /**
     * Get all products listed by a specific farmer
     *
     * @param int $farmer_id ID of the farmer
     * @return array List of products
     */
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
    
    /**
     * Alias for get_farmer_products to maintain backward compatibility
     * 
     * @param int $farmer_id ID of the farmer
     * @return array List of products
     * @see get_farmer_products()
     */
    public function get_products_by_farmer($farmer_id) {
        return $this->get_farmer_products($farmer_id);
    }
    
    /**
     * Get the total number of active products in the marketplace
     * 
     * @return int Total number of products
     */
    public function count_products() {
        $sql = "SELECT COUNT(*) as total FROM products";
        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
    
    /**
     * Get all unique product categories from the database
     * 
     * @return array List of category names
     */
    public function get_categories() {
        $sql = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != ''";
        $result = mysqli_query($this->conn, $sql);
        
        $categories = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row['category'];
        }
        
        return $categories;
    }
    
    /**
     * Get search suggestions for autocomplete
     * 
     * @param string $query Search query
     * @param int $limit Maximum number of suggestions to return
     * @return array Matching product suggestions
     */
    public function get_search_suggestions($query, $limit = 10) {
        // Sanitize the query
        $query = trim(mysqli_real_escape_string($this->conn, $query));
        
        if (empty($query) || strlen($query) < 2) {
            return [];
        }
        
        // Build query to search products by name, category, and description
        $sql = "SELECT 
                p.id, 
                p.name, 
                p.category, 
                p.price,
                p.image,
                CASE 
                    WHEN p.name LIKE ? THEN 100  -- Exact match in name
                    WHEN p.name LIKE ? THEN 90   -- Starts with query
                    WHEN p.category LIKE ? THEN 80  -- Match in category
                    WHEN p.description LIKE ? THEN 50  -- Match in description
                    ELSE 10  -- Other matches
                END as relevance
                FROM products p
                WHERE p.name LIKE ? OR p.name LIKE ? OR p.category LIKE ? OR p.description LIKE ?
                ORDER BY relevance DESC, p.name ASC
                LIMIT ?";
        
        // Prepare the statement
        $stmt = mysqli_prepare($this->conn, $sql);
        
        // Create search terms
        $exact_match = "%$query%";  // Anywhere in the name
        $starts_with = "$query%";   // Starts with query
        
        // Bind parameters
        mysqli_stmt_bind_param(
            $stmt, 
            "ssssssssi", 
            $exact_match, $starts_with, $exact_match, $exact_match, // For relevance scoring
            $exact_match, $starts_with, $exact_match, $exact_match,  // For WHERE clause
            $limit
        );
        
        // Execute the query
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        // Process results
        $suggestions = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // Add a highlight field for displaying in autocomplete
            $row['highlight'] = $this->highlight_match($row['name'], $query);
            $suggestions[] = $row;
        }
        
        return $suggestions;
    }
    
    /**
     * Get minimum and maximum product prices
     * 
     * @return array Associative array with min_price and max_price
     */
    public function get_price_range() {
        $sql = "SELECT MIN(price) as min_price, MAX(price) as max_price FROM products";
        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);
        
        return $row ?: ['min_price' => 0, 'max_price' => 1000];
    }
    
    /**
     * Advanced product search with multiple filters and sorting options
     * 
     * @param array $filters Search filters
     * @param array $sort_options Sorting options
     * @param int $limit Maximum number of results
     * @param int $offset Pagination offset
     * @return array Matching products
     */
    public function advanced_search($filters = [], $sort_options = [], $limit = 12, $offset = 0) {
        $where_clauses = [];
        $types = "";
        $params = [];
        
        // Apply filters
        if (!empty($filters)) {
            // Category filter (can be multiple)
            if (isset($filters['categories']) && !empty($filters['categories'])) {
                $category_placeholders = [];
                foreach ($filters['categories'] as $category) {
                    $category_placeholders[] = "?";
                    $params[] = $category;
                    $types .= "s";
                }
                
                $where_clauses[] = "p.category IN (" . implode(",", $category_placeholders) . ")";
            } elseif (isset($filters['category']) && !empty($filters['category'])) {
                // Single category (backward compatibility)
                $where_clauses[] = "p.category = ?";
                $types .= "s";
                $params[] = $filters['category'];
            }
            
            // Location filter
            if (isset($filters['location']) && !empty($filters['location'])) {
                $where_clauses[] = "u.location LIKE ?";
                $types .= "s";
                $params[] = "%" . $filters['location'] . "%";
            }
            
            // Farmer filter
            if (isset($filters['farmer_id']) && !empty($filters['farmer_id'])) {
                $where_clauses[] = "p.farmer_id = ?";
                $types .= "i";
                $params[] = $filters['farmer_id'];
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
            
            // Rating filter
            if (isset($filters['min_rating']) && $filters['min_rating'] > 0) {
                $where_clauses[] = "(SELECT AVG(rating) FROM reviews WHERE product_id = p.id) >= ?";
                $types .= "d";
                $params[] = $filters['min_rating'];
            }
            
            // Stock availability filter
            if (isset($filters['in_stock']) && $filters['in_stock']) {
                $where_clauses[] = "p.stock > 0";
            }
            
            // Search term (with improved relevance)
            if (isset($filters['search']) && !empty($filters['search'])) {
                $search_term = "%" . $filters['search'] . "%";
                $where_clauses[] = "(p.name LIKE ? OR p.description LIKE ? OR p.category LIKE ?)";
                $types .= "sss";
                $params[] = $search_term;
                $params[] = $search_term;
                $params[] = $search_term;
            }
        }
        
        // Build WHERE clause
        $where_sql = "";
        if (!empty($where_clauses)) {
            $where_sql = "WHERE " . implode(" AND ", $where_clauses);
        }
        
        // Build ORDER BY clause
        $order_sql = "ORDER BY p.date_added DESC";
        
        if (!empty($sort_options) && isset($sort_options['sort_by'])) {
            switch ($sort_options['sort_by']) {
                case 'price_low':
                    $order_sql = "ORDER BY p.price ASC";
                    break;
                case 'price_high':
                    $order_sql = "ORDER BY p.price DESC";
                    break;
                case 'name_asc':
                    $order_sql = "ORDER BY p.name ASC";
                    break;
                case 'name_desc':
                    $order_sql = "ORDER BY p.name DESC";
                    break;
                case 'rating':
                    $order_sql = "ORDER BY (SELECT AVG(rating) FROM reviews WHERE product_id = p.id) DESC";
                    break;
                case 'popularity':
                    $order_sql = "ORDER BY 
                        (SELECT COUNT(*) FROM order_items WHERE product_id = p.id) DESC, 
                        p.date_added DESC";
                    break;
                case 'newest':
                default:
                    $order_sql = "ORDER BY p.date_added DESC";
                    break;
            }
        }
        
        // Build complete query
        $sql = "SELECT p.*, u.name as farmer_name, u.location as farmer_location,
                (SELECT AVG(rating) FROM reviews WHERE product_id = p.id) as avg_rating,
                (SELECT COUNT(*) FROM reviews WHERE product_id = p.id) as review_count
                FROM products p
                JOIN users u ON p.farmer_id = u.id
                $where_sql
                $order_sql
                LIMIT ? OFFSET ?";
        
        $types .= "ii";
        $params[] = $limit;
        $params[] = $offset;
        
        // Execute query
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
    
    /**
     * Count the total number of products matching the given filters
     * 
     * @param array $filters Search filters
     * @return int Total number of matching products
     */
    public function count_filtered_products($filters = []) {
        $where_clauses = [];
        $types = "";
        $params = [];
        
        // Apply the same filters as in advanced_search
        if (!empty($filters)) {
            // Category filter (can be multiple)
            if (isset($filters['categories']) && !empty($filters['categories'])) {
                $category_placeholders = [];
                foreach ($filters['categories'] as $category) {
                    $category_placeholders[] = "?";
                    $params[] = $category;
                    $types .= "s";
                }
                
                $where_clauses[] = "p.category IN (" . implode(",", $category_placeholders) . ")";
            } elseif (isset($filters['category']) && !empty($filters['category'])) {
                // Single category (backward compatibility)
                $where_clauses[] = "p.category = ?";
                $types .= "s";
                $params[] = $filters['category'];
            }
            
            // Location filter
            if (isset($filters['location']) && !empty($filters['location'])) {
                $where_clauses[] = "u.location LIKE ?";
                $types .= "s";
                $params[] = "%" . $filters['location'] . "%";
            }
            
            // Farmer filter
            if (isset($filters['farmer_id']) && !empty($filters['farmer_id'])) {
                $where_clauses[] = "p.farmer_id = ?";
                $types .= "i";
                $params[] = $filters['farmer_id'];
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
            
            // Rating filter
            if (isset($filters['min_rating']) && $filters['min_rating'] > 0) {
                $where_clauses[] = "(SELECT AVG(rating) FROM reviews WHERE product_id = p.id) >= ?";
                $types .= "d";
                $params[] = $filters['min_rating'];
            }
            
            // Stock availability filter
            if (isset($filters['in_stock']) && $filters['in_stock']) {
                $where_clauses[] = "p.stock > 0";
            }
            
            // Search term
            if (isset($filters['search']) && !empty($filters['search'])) {
                $search_term = "%" . $filters['search'] . "%";
                $where_clauses[] = "(p.name LIKE ? OR p.description LIKE ? OR p.category LIKE ?)";
                $types .= "sss";
                $params[] = $search_term;
                $params[] = $search_term;
                $params[] = $search_term;
            }
        }
        
        // Build WHERE clause
        $where_sql = "";
        if (!empty($where_clauses)) {
            $where_sql = "WHERE " . implode(" AND ", $where_clauses);
        }
        
        // Build count query
        $sql = "SELECT COUNT(*) as total FROM products p JOIN users u ON p.farmer_id = u.id $where_sql";
        
        // Execute query
        $stmt = mysqli_prepare($this->conn, $sql);
        
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        return $row['total'] ?? 0;
    }
    
    /**
     * Highlight the matching part of the text
     * 
     * @param string $text Text to highlight in
     * @param string $query Query to highlight
     * @return string Highlighted text
     */
    private function highlight_match($text, $query) {
        // Case-insensitive highlight
        $pattern = '/(' . preg_quote($query, '/') . ')/i';
        return preg_replace($pattern, '<strong>$1</strong>', $text);
    }
    
    /**
     * Count the number of products owned by a farmer
     * 
     * @param int $farmer_id The farmer's ID
     * @return int Number of products
     */
    public function count_farmer_products($farmer_id) {
        $sql = "SELECT COUNT(*) as count FROM products WHERE farmer_id = ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $farmer_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            return (int)$row['count'];
        }
        
        return 0;
    }
}
?>
