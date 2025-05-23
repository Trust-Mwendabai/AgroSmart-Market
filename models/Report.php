<?php
class Report {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get farmer transaction summary (sales stats)
     * 
     * @param int $farmer_id The ID of the farmer
     * @param string $period Optional period filter (all, month, year, custom)
     * @param string $start_date Optional start date for custom period (format: Y-m-d)
     * @param string $end_date Optional end date for custom period (format: Y-m-d)
     * @return array Returns statistics of farmer's sales
     */
    public function get_farmer_sales_summary($farmer_id, $period = 'all', $start_date = null, $end_date = null) {
        $where_clause = '';
        $types = 'i';
        $params = [$farmer_id];
        
        if ($period === 'month') {
            $where_clause = 'AND o.date_ordered >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';
        } elseif ($period === 'year') {
            $where_clause = 'AND o.date_ordered >= DATE_SUB(NOW(), INTERVAL 1 YEAR)';
        } elseif ($period === 'custom' && $start_date && $end_date) {
            $where_clause = 'AND o.date_ordered BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)';
            $types .= 'ss';
            $params[] = $start_date;
            $params[] = $end_date;
        }
        
        // Get total sales, orders, average order value
        $sql = "SELECT 
                COUNT(o.id) AS total_orders,
                IFNULL(SUM(o.quantity * p.price), 0) AS total_sales,
                IFNULL(AVG(o.quantity * p.price), 0) AS average_order_value,
                COUNT(DISTINCT o.buyer_id) AS unique_customers
                FROM orders o
                JOIN products p ON o.product_id = p.id
                WHERE p.farmer_id = ? $where_clause";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            return $row;
        }
        
        return [
            'total_orders' => 0,
            'total_sales' => 0,
            'average_order_value' => 0,
            'unique_customers' => 0
        ];
    }
    
    /**
     * Get farmer product sales breakdown
     * 
     * @param int $farmer_id The ID of the farmer
     * @param string $period Optional period filter (all, month, year, custom)
     * @param string $start_date Optional start date for custom period (format: Y-m-d)
     * @param string $end_date Optional end date for custom period (format: Y-m-d)
     * @return array Returns product sales data
     */
    public function get_farmer_product_sales($farmer_id, $period = 'all', $start_date = null, $end_date = null) {
        $where_clause = '';
        $types = 'i';
        $params = [$farmer_id];
        
        if ($period === 'month') {
            $where_clause = 'AND o.date_ordered >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';
        } elseif ($period === 'year') {
            $where_clause = 'AND o.date_ordered >= DATE_SUB(NOW(), INTERVAL 1 YEAR)';
        } elseif ($period === 'custom' && $start_date && $end_date) {
            $where_clause = 'AND o.date_ordered BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)';
            $types .= 'ss';
            $params[] = $start_date;
            $params[] = $end_date;
        }
        
        $sql = "SELECT 
                p.id, 
                p.name, 
                p.category,
                SUM(o.quantity) AS total_quantity_sold,
                SUM(o.quantity * p.price) AS total_revenue,
                COUNT(o.id) AS orders_count
                FROM orders o
                JOIN products p ON o.product_id = p.id
                WHERE p.farmer_id = ? $where_clause
                GROUP BY p.id
                ORDER BY total_revenue DESC";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
        
        return $products;
    }
    
    /**
     * Get farmer monthly sales trend
     * 
     * @param int $farmer_id The ID of the farmer
     * @param int $months Number of months to include
     * @return array Returns monthly sales data
     */
    public function get_farmer_monthly_trend($farmer_id, $months = 6) {
        $sql = "SELECT 
                DATE_FORMAT(o.date_ordered, '%Y-%m') AS month,
                COUNT(o.id) AS orders_count,
                SUM(o.quantity * p.price) AS total_sales
                FROM orders o
                JOIN products p ON o.product_id = p.id
                WHERE p.farmer_id = ? 
                AND o.date_ordered >= DATE_SUB(NOW(), INTERVAL ? MONTH)
                GROUP BY DATE_FORMAT(o.date_ordered, '%Y-%m')
                ORDER BY month ASC";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $farmer_id, $months);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $trend = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $trend[] = $row;
        }
        
        return $trend;
    }
    
    /**
     * Get buyer purchase summary
     * 
     * @param int $buyer_id The ID of the buyer
     * @param string $period Optional period filter (all, month, year, custom)
     * @param string $start_date Optional start date for custom period (format: Y-m-d)
     * @param string $end_date Optional end date for custom period (format: Y-m-d)
     * @return array Returns statistics of buyer's purchases
     */
    public function get_buyer_purchase_summary($buyer_id, $period = 'all', $start_date = null, $end_date = null) {
        $where_clause = '';
        $types = 'i';
        $params = [$buyer_id];
        
        if ($period === 'month') {
            $where_clause = 'AND o.date_ordered >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';
        } elseif ($period === 'year') {
            $where_clause = 'AND o.date_ordered >= DATE_SUB(NOW(), INTERVAL 1 YEAR)';
        } elseif ($period === 'custom' && $start_date && $end_date) {
            $where_clause = 'AND o.date_ordered BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)';
            $types .= 'ss';
            $params[] = $start_date;
            $params[] = $end_date;
        }
        
        // Get total purchases, orders count
        $sql = "SELECT 
                COUNT(o.id) AS total_orders,
                IFNULL(SUM(o.quantity * p.price), 0) AS total_spent,
                IFNULL(AVG(o.quantity * p.price), 0) AS average_order_value,
                COUNT(DISTINCT o.farmer_id) AS unique_farmers,
                COUNT(DISTINCT p.category) AS unique_categories
                FROM orders o
                JOIN products p ON o.product_id = p.id
                WHERE o.buyer_id = ? $where_clause";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            return $row;
        }
        
        return [
            'total_orders' => 0,
            'total_spent' => 0,
            'average_order_value' => 0,
            'unique_farmers' => 0,
            'unique_categories' => 0
        ];
    }
    
    /**
     * Get buyer category breakdown
     * 
     * @param int $buyer_id The ID of the buyer
     * @param string $period Optional period filter (all, month, year, custom)
     * @param string $start_date Optional start date for custom period (format: Y-m-d)
     * @param string $end_date Optional end date for custom period (format: Y-m-d)
     * @return array Returns category spending data
     */
    public function get_buyer_category_breakdown($buyer_id, $period = 'all', $start_date = null, $end_date = null) {
        $where_clause = '';
        $types = 'i';
        $params = [$buyer_id];
        
        if ($period === 'month') {
            $where_clause = 'AND o.date_ordered >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';
        } elseif ($period === 'year') {
            $where_clause = 'AND o.date_ordered >= DATE_SUB(NOW(), INTERVAL 1 YEAR)';
        } elseif ($period === 'custom' && $start_date && $end_date) {
            $where_clause = 'AND o.date_ordered BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)';
            $types .= 'ss';
            $params[] = $start_date;
            $params[] = $end_date;
        }
        
        $sql = "SELECT 
                p.category,
                COUNT(o.id) AS orders_count,
                SUM(o.quantity) AS total_quantity,
                SUM(o.quantity * p.price) AS total_spent
                FROM orders o
                JOIN products p ON o.product_id = p.id
                WHERE o.buyer_id = ? $where_clause
                GROUP BY p.category
                ORDER BY total_spent DESC";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $categories = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
        
        return $categories;
    }
    
    /**
     * Get transaction details
     * 
     * @param int $user_id The ID of the user
     * @param string $user_type The type of user (farmer or buyer)
     * @param string $period Optional period filter (all, month, year, custom)
     * @param int $limit Optional limit of records to retrieve
     * @param int $offset Optional offset for pagination
     * @param string $start_date Optional start date for custom period (format: Y-m-d)
     * @param string $end_date Optional end date for custom period (format: Y-m-d)
     * @return array Returns transaction details
     */
    public function get_transaction_details($user_id, $user_type, $period = 'all', $limit = 20, $offset = 0, $start_date = null, $end_date = null) {
        $where_clause = '';
        $params = [];
        $types = '';
        
        if ($user_type === 'farmer') {
            $where_clause = 'p.farmer_id = ?';
            $types = 'i';
            $params[] = $user_id;
        } else { // buyer
            $where_clause = 'o.buyer_id = ?';
            $types = 'i';
            $params[] = $user_id;
        }
        
        if ($period === 'month') {
            $where_clause .= ' AND o.date_ordered >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';
        } elseif ($period === 'year') {
            $where_clause .= ' AND o.date_ordered >= DATE_SUB(NOW(), INTERVAL 1 YEAR)';
        } elseif ($period === 'custom' && $start_date && $end_date) {
            $where_clause .= ' AND o.date_ordered BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)';
            $types .= 'ss';
            $params[] = $start_date;
            $params[] = $end_date;
        }
        
        if ($user_type === 'farmer') {
            $sql = "SELECT 
                    o.id, 
                    o.date_ordered, 
                    o.status,
                    p.name AS product_name,
                    p.category,
                    o.quantity,
                    p.price,
                    (o.quantity * p.price) AS total_amount,
                    u.name AS buyer_name,
                    u.location AS buyer_location
                    FROM orders o
                    JOIN products p ON o.product_id = p.id
                    JOIN users u ON o.buyer_id = u.id
                    WHERE $where_clause
                    ORDER BY o.date_ordered DESC
                    LIMIT ? OFFSET ?";
        } else {
            $sql = "SELECT 
                    o.id, 
                    o.date_ordered, 
                    o.status,
                    p.name AS product_name,
                    p.category,
                    o.quantity,
                    p.price,
                    (o.quantity * p.price) AS total_amount,
                    u.name AS farmer_name,
                    u.location AS farmer_location
                    FROM orders o
                    JOIN products p ON o.product_id = p.id
                    JOIN users u ON p.farmer_id = u.id
                    WHERE $where_clause
                    ORDER BY o.date_ordered DESC
                    LIMIT ? OFFSET ?";
        }
        
        // Add types for limit and offset parameters (both integers)
        $types .= 'ii';
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $transactions = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $transactions[] = $row;
        }
        
        return $transactions;
    }
    
    /**
     * Count total transactions
     * 
     * @param int $user_id The ID of the user
     * @param string $user_type The type of user (farmer or buyer)
     * @param string $period Optional period filter (all, month, year, custom)
     * @param string $start_date Optional start date for custom period (format: Y-m-d)
     * @param string $end_date Optional end date for custom period (format: Y-m-d)
     * @return int Returns count of transactions
     */
    public function count_transactions($user_id, $user_type, $period = 'all', $start_date = null, $end_date = null) {
        $where_clause = '';
        $params = [];
        $types = '';
        
        if ($user_type === 'farmer') {
            $where_clause = 'p.farmer_id = ?';
            $types = 'i';
            $params[] = $user_id;
        } else { // buyer
            $where_clause = 'o.buyer_id = ?';
            $types = 'i';
            $params[] = $user_id;
        }
        
        if ($period === 'month') {
            $where_clause .= ' AND o.date_ordered >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';
        } elseif ($period === 'year') {
            $where_clause .= ' AND o.date_ordered >= DATE_SUB(NOW(), INTERVAL 1 YEAR)';
        } elseif ($period === 'custom' && $start_date && $end_date) {
            $where_clause .= ' AND o.date_ordered BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)';
            $types .= 'ss';
            $params[] = $start_date;
            $params[] = $end_date;
        }
        
        $sql = "SELECT COUNT(*) as total
                FROM orders o
                JOIN products p ON o.product_id = p.id
                WHERE $where_clause";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            return $row['total'];
        }
        
        return 0;
    }
}
?>
