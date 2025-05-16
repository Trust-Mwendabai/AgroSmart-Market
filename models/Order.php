<?php
class Order {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create a new order
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
    
    // Check product availability
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
    
    // Update product stock
    private function update_product_stock($product_id, $new_stock) {
        $sql = "UPDATE products SET stock = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $new_stock, $product_id);
        mysqli_stmt_execute($stmt);
    }
    
    // Update order status
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
    
    // Check if user can update order
    private function can_update_order($order_id, $user_id, $is_farmer) {
        $field = $is_farmer ? "farmer_id" : "buyer_id";
        $sql = "SELECT id FROM orders WHERE id = ? AND $field = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        return mysqli_stmt_num_rows($stmt) > 0;
    }
    
    // Restore stock when order is cancelled
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
    
    // Get order by ID
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
    
    // Get orders by buyer
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
    
    // Get orders by farmer
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
    
    // Get all orders (for admin)
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
    
    // Count orders by status
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
}
?>
