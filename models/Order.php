<?php
/**
 * Order Model
 * 
 * Handles all order-related database operations including order creation, status updates,
 * and order retrieval for the AgroSmart Market platform.
 * 
 * @package Models
 */
class Order {
    /**
     * @var mysqli Database connection
     */
    private $conn;
    
    /**
     * @var array Valid order statuses
     */
    private const ORDER_STATUSES = [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled'
    ];
    
    /**
     * Order constructor.
     *
     * @param mysqli $db Database connection
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Create a new order
     *
     * @param int $buyer_id ID of the buyer placing the order
     * @param int $farmer_id ID of the farmer selling the product
     * @param int $product_id ID of the product being ordered
     * @param int $quantity Quantity of the product to order
     * @return array Result with success status and order ID or error message
     * @throws Exception If order creation fails or product is unavailable
     */
    public function create_order($buyer_id, $farmer_id, $product_id, $quantity) {
        // Check if product exists and has enough stock
        $product = $this->check_product_availability($product_id, $quantity);
        if (isset($product['error'])) {
            return $product;
        }
        
        // Create the order
        $sql = "INSERT INTO orders (buyer_id, farmer_id, product_id, quantity) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiii", $buyer_id, $farmer_id, $product_id, $quantity);
        
        if (mysqli_stmt_execute($stmt)) {
            // Update product stock
            $this->update_product_stock($product_id, $product['stock'] - $quantity);
            
            return [
                "success" => true,
                "order_id" => mysqli_insert_id($this->conn)
            ];
        } else {
            return ["error" => "Failed to create order: " . mysqli_error($this->conn)];
        }
    }
    
    /**
     * Check if a product is available in the requested quantity
     *
     * @param int $product_id ID of the product to check
     * @param int $quantity Quantity to check availability for
     * @return array|bool Product data if available, error array if not
     */
    private function check_product_availability($product_id, $quantity) {
        $sql = "SELECT id, stock, farmer_id FROM products WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 1) {
            $product = mysqli_fetch_assoc($result);
            if ($product['stock'] < $quantity) {
                return ["error" => "Not enough stock available"];
            }
            return $product;
        }
        
        return ["error" => "Product not found"];
    }
    
    /**
     * Update the stock level of a product
     *
     * @param int $product_id ID of the product to update
     * @param int $new_stock New stock quantity
     * @return bool True if update was successful
     */
    private function update_product_stock($product_id, $new_stock) {
        $sql = "UPDATE products SET stock = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $new_stock, $product_id);
        mysqli_stmt_execute($stmt);
    }
    
    /**
     * Update the status of an order
     *
     * @param int $order_id ID of the order to update
     * @param string $status New status (must be one of: pending, confirmed, shipped, delivered, cancelled)
     * @param int $user_id ID of the user requesting the update
     * @param bool $is_farmer Whether the user is a farmer (default: false)
     * @return array Result with success status or error message
     * @throws Exception If status update fails or user doesn't have permission
     */
    public function update_status($order_id, $status, $user_id, $is_farmer = false) {
        // Check if user has permission to update this order
        if (!$this->can_update_order($order_id, $user_id, $is_farmer)) {
            return ["error" => "You don't have permission to update this order"];
        }
        
        $allowed_statuses = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $allowed_statuses)) {
            return ["error" => "Invalid status"];
        }
        
        $sql = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
        
        if (mysqli_stmt_execute($stmt)) {
            // If order is cancelled, restore product stock
            if ($status === 'cancelled') {
                $this->restore_stock_on_cancel($order_id);
            }
            return ["success" => true];
        } else {
            return ["error" => "Failed to update order status: " . mysqli_error($this->conn)];
        }
    }
    
    /**
     * Check if a user has permission to update an order
     *
     * @param int $order_id ID of the order to check
     * @param int $user_id ID of the user to check
     * @param bool $is_farmer Whether the user is a farmer
     * @return bool True if user can update the order, false otherwise
     */
    private function can_update_order($order_id, $user_id, $is_farmer) {
        $field = $is_farmer ? "farmer_id" : "buyer_id";
        $sql = "SELECT id FROM orders WHERE id = ? AND $field = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        return mysqli_stmt_num_rows($stmt) > 0;
    }
    
    /**
     * Restore product stock when an order is cancelled
     *
     * @param int $order_id ID of the cancelled order
     * @return void
     */
    private function restore_stock_on_cancel($order_id) {
        $sql = "SELECT product_id, quantity FROM orders WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($order = mysqli_fetch_assoc($result)) {
            $product_id = $order['product_id'];
            $quantity = $order['quantity'];
            
            // Get current stock
            $prod_sql = "SELECT stock FROM products WHERE id = ?";
            $prod_stmt = mysqli_prepare($this->conn, $prod_sql);
            mysqli_stmt_bind_param($prod_stmt, "i", $product_id);
            mysqli_stmt_execute($prod_stmt);
            $prod_result = mysqli_stmt_get_result($prod_stmt);
            
            if ($product = mysqli_fetch_assoc($prod_result)) {
                $new_stock = $product['stock'] + $quantity;
                $this->update_product_stock($product_id, $new_stock);
            }
        }
    }
    
    /**
     * Get order details by ID
     *
     * @param int $order_id ID of the order to retrieve
     * @return array|false Associative array of order data or false if not found
     */
    public function get_order($order_id) {
        $sql = "SELECT o.*, p.name as product_name, p.price, p.image as product_image,
                bf.name as buyer_name, bf.email as buyer_email,
                fr.name as farmer_name, fr.email as farmer_email
                FROM orders o
                JOIN products p ON o.product_id = p.id
                JOIN users bf ON o.buyer_id = bf.id
                JOIN users fr ON o.farmer_id = fr.id
                WHERE o.id = ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 1) {
            return mysqli_fetch_assoc($result);
        }
        
        return false;
    }
    
    /**
     * Get all orders placed by a specific buyer
     *
     * @param int $buyer_id ID of the buyer
     * @return array List of orders with product and farmer information
     */
    public function get_buyer_orders($buyer_id) {
        $sql = "SELECT o.*, p.name as product_name, p.price, p.image as product_image,
                u.name as farmer_name
                FROM orders o
                JOIN products p ON o.product_id = p.id
                JOIN users u ON o.farmer_id = u.id
                WHERE o.buyer_id = ?
                ORDER BY o.date_ordered DESC";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $buyer_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $orders = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
        
        return $orders;
    }
    
    /**
     * Get all orders received by a specific farmer
     *
     * @param int $farmer_id ID of the farmer
     * @return array List of orders with product and buyer information
     */
    public function get_farmer_orders($farmer_id) {
        $sql = "SELECT o.*, p.name as product_name, p.price, p.image as product_image,
                u.name as buyer_name
                FROM orders o
                JOIN products p ON o.product_id = p.id
                JOIN users u ON o.buyer_id = u.id
                WHERE o.farmer_id = ?
                ORDER BY o.date_ordered DESC";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $farmer_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $orders = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
        
        return $orders;
    }
    
    /**
     * Get a paginated list of all orders (admin only)
     *
     * @param int $limit Maximum number of orders to return (default: 20)
     * @param int $offset Number of orders to skip (for pagination) (default: 0)
     * @return array List of orders with product, buyer, and farmer information
     */
    public function get_all_orders($limit = 20, $offset = 0) {
        $sql = "SELECT o.*, p.name as product_name,
                bf.name as buyer_name,
                fr.name as farmer_name
                FROM orders o
                JOIN products p ON o.product_id = p.id
                JOIN users bf ON o.buyer_id = bf.id
                JOIN users fr ON o.farmer_id = fr.id
                ORDER BY o.date_ordered DESC
                LIMIT ? OFFSET ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $orders = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
        
        return $orders;
    }
    
    /**
     * Get counts of orders grouped by status
     * 
     * @return array Associative array with status as key and count as value
     */
    public function count_orders_by_status() {
        $sql = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
        $result = mysqli_query($this->conn, $sql);
        
        $counts = [
            'pending' => 0,
            'confirmed' => 0,
            'shipped' => 0,
            'delivered' => 0,
            'cancelled' => 0
        ];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $counts[$row['status']] = $row['count'];
        }
        
        return $counts;
    }
    
    /**
     * Count the number of orders received by a farmer
     * 
     * @param int $farmer_id The farmer's ID
     * @return int Number of orders
     */
    public function count_farmer_orders($farmer_id) {
        $sql = "SELECT COUNT(*) as count FROM orders WHERE farmer_id = ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $farmer_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            return (int)$row['count'];
        }
        
        return 0;
    }
    
    /**
     * Count the number of orders made by a buyer
     * 
     * @param int $buyer_id The buyer's ID
     * @return int Number of orders
     */
    public function count_buyer_orders($buyer_id) {
        $sql = "SELECT COUNT(*) as count FROM orders WHERE buyer_id = ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $buyer_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            return (int)$row['count'];
        }
        
        return 0;
    }
    
    /**
     * Calculate the total amount spent by a buyer
     * 
     * @param int $buyer_id The buyer's ID
     * @return float Total amount spent
     */
    public function get_buyer_total_spent($buyer_id) {
        $sql = "SELECT SUM(p.price * o.quantity) as total 
                FROM orders o 
                JOIN products p ON o.product_id = p.id 
                WHERE o.buyer_id = ? AND o.status != 'cancelled'";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $buyer_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            return (float)($row['total'] ?: 0);
        }
        
        return 0;
    }
}
?>
