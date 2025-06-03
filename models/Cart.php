<?php
class Cart {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
        $this->initialize_cart();
    }
    
    // Initialize cart in session if it doesn't exist
    private function initialize_cart() {
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [
                'items' => [],
                'total_quantity' => 0,
                'total_price' => 0.00
            ];
        }
        
        // Ensure all required cart elements exist
        if (!isset($_SESSION['cart']['items']) || !is_array($_SESSION['cart']['items'])) {
            $_SESSION['cart']['items'] = [];
        }
        
        if (!isset($_SESSION['cart']['total_quantity'])) {
            $_SESSION['cart']['total_quantity'] = 0;
        }
        
        if (!isset($_SESSION['cart']['total_price'])) {
            $_SESSION['cart']['total_price'] = 0.00;
        }
    }
    
    // Get cart contents
    public function get_cart() {
        return $_SESSION['cart'];
    }
    
    // Get cart count
    public function get_cart_count() {
        return $_SESSION['cart']['total_quantity'] ?? 0;
    }
    
    // Get cart total price
    public function get_cart_total() {
        return $_SESSION['cart']['total_price'] ?? 0.00;
    }
    
    // Get cart item details including product information
    public function get_cart_items_with_details() {
        $items = [];
        
        if (empty($_SESSION['cart']['items'])) {
            return $items;
        }
        
        foreach ($_SESSION['cart']['items'] as $product_id => $item) {
            // Get product details
            $sql = "SELECT p.*, u.name as farmer_name 
                    FROM products p 
                    JOIN users u ON p.farmer_id = u.id 
                    WHERE p.id = ?";
            
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $product_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if ($product = mysqli_fetch_assoc($result)) {
                $items[] = array_merge($product, [
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['quantity'] * $product['price']
                ]);
            }
        }
        
        return $items;
    }
    
    // Add item to cart
    public function add_item($product_id, $quantity = 1) {
        // Check if product exists and has stock
        $sql = "SELECT * FROM products WHERE id = ? AND stock >= ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $product_id, $quantity);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 0) {
            return ["error" => "Product not available or insufficient stock"];
        }
        
        $product = mysqli_fetch_assoc($result);
        
        // Check if product already in cart
        if (isset($_SESSION['cart']['items'][$product_id])) {
            // Check if we have enough stock for the requested quantity
            $new_quantity = $_SESSION['cart']['items'][$product_id]['quantity'] + $quantity;
            
            if ($new_quantity > $product['stock']) {
                return ["error" => "Not enough stock available"];
            }
            
            $_SESSION['cart']['items'][$product_id]['quantity'] = $new_quantity;
        } else {
            // Add new item to cart
            $_SESSION['cart']['items'][$product_id] = [
                'quantity' => $quantity,
                'price' => $product['price']
            ];
        }
        
        // Update cart totals
        $this->update_cart_totals();
        
        return ["success" => true, "message" => "Product added to cart"];
    }
    
    // Update item quantity in cart
    public function update_item($product_id, $quantity) {
        // Validate product exists in cart
        if (!isset($_SESSION['cart']['items'][$product_id])) {
            return ["error" => "Product not found in cart"];
        }
        
        // If quantity is 0 or negative, remove item from cart
        if ($quantity <= 0) {
            return $this->remove_item($product_id);
        }
        
        // Check if we have enough stock
        $sql = "SELECT stock FROM products WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($product = mysqli_fetch_assoc($result)) {
            if ($quantity > $product['stock']) {
                return ["error" => "Not enough stock available", "available" => $product['stock']];
            }
            
            // Update quantity
            $_SESSION['cart']['items'][$product_id]['quantity'] = $quantity;
            
            // Update cart totals
            $this->update_cart_totals();
            
            return ["success" => true, "message" => "Cart updated"];
        }
        
        return ["error" => "Product not found"];
    }
    
    // Remove item from cart
    public function remove_item($product_id) {
        if (isset($_SESSION['cart']['items'][$product_id])) {
            unset($_SESSION['cart']['items'][$product_id]);
            $this->update_cart_totals();
            return ["success" => true, "message" => "Item removed from cart"];
        }
        
        return ["error" => "Product not found in cart"];
    }
    
    // Clear cart
    public function clear_cart() {
        $_SESSION['cart'] = [
            'items' => [],
            'total_quantity' => 0,
            'total_price' => 0.00
        ];
        
        return ["success" => true, "message" => "Cart cleared"];
    }
    
    // Update cart totals
    private function update_cart_totals() {
        $total_quantity = 0;
        $total_price = 0.00;
        
        foreach ($_SESSION['cart']['items'] as $item) {
            $total_quantity += $item['quantity'];
            $total_price += $item['quantity'] * $item['price'];
        }
        
        $_SESSION['cart']['total_quantity'] = $total_quantity;
        $_SESSION['cart']['total_price'] = $total_price;
    }
    
    // Convert cart to order
    public function checkout($buyer_id) {
        // Check if cart is empty
        if (empty($_SESSION['cart']['items'])) {
            return ["error" => "Your cart is empty"];
        }
        
        // Get database connection for transaction
        $this->conn->begin_transaction();
        
        try {
            $order_ids = [];
            
            // Process each item in cart
            foreach ($_SESSION['cart']['items'] as $product_id => $item) {
                // Get product details
                $sql = "SELECT farmer_id, stock FROM products WHERE id = ? FOR UPDATE";
                $stmt = mysqli_prepare($this->conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $product_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if ($product = mysqli_fetch_assoc($result)) {
                    // Check stock again (might have changed)
                    if ($product['stock'] < $item['quantity']) {
                        throw new Exception("Not enough stock available for product ID: $product_id");
                    }
                    
                    // Create order
                    $sql = "INSERT INTO orders (buyer_id, farmer_id, product_id, quantity, price) 
                            VALUES (?, ?, ?, ?, ?)";
                    $stmt = mysqli_prepare($this->conn, $sql);
                    mysqli_stmt_bind_param($stmt, "iiiid", $buyer_id, $product['farmer_id'], $product_id, $item['quantity'], $item['price']);
                    mysqli_stmt_execute($stmt);
                    
                    $order_ids[] = mysqli_insert_id($this->conn);
                    
                    // Update product stock
                    $new_stock = $product['stock'] - $item['quantity'];
                    $sql = "UPDATE products SET stock = ? WHERE id = ?";
                    $stmt = mysqli_prepare($this->conn, $sql);
                    mysqli_stmt_bind_param($stmt, "ii", $new_stock, $product_id);
                    mysqli_stmt_execute($stmt);
                } else {
                    throw new Exception("Product not found: $product_id");
                }
            }
            
            // Commit transaction
            $this->conn->commit();
            
            // Clear cart after successful checkout
            $this->clear_cart();
            
            return [
                "success" => true, 
                "message" => "Checkout successful",
                "order_ids" => $order_ids
            ];
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollback();
            return ["error" => $e->getMessage()];
        }
    }
}
?>
