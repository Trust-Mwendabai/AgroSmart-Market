<?php
/**
 * Report Model
 * 
 * Handles all reporting, analytics, and data export functionality for the AgroSmart Market
 */
class Report {
    private $conn;
    
    /**
     * Constructor
     * 
     * @param mysqli $db Database connection
     */
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
                COUNT(o.id) AS order_count,
                SUM(o.quantity) AS quantity_purchased,
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
     * Get comparative market analysis
     * Compares a farmer's performance against the market average
     * 
     * @param int $farmer_id The ID of the farmer
     * @return array Returns comparative market analysis
     */
    public function get_comparative_market_analysis($farmer_id) {
        // Get farmer's average prices by category
        $sql_farmer = "SELECT 
                p.category,
                AVG(p.price) AS avg_price,
                COUNT(p.id) AS product_count,
                SUM((SELECT COUNT(*) FROM orders WHERE product_id = p.id)) AS order_count,
                AVG((SELECT AVG(rating) FROM reviews r WHERE r.product_id = p.id)) AS avg_rating
                FROM products p
                WHERE p.farmer_id = ?
                GROUP BY p.category";
        
        $stmt_farmer = mysqli_prepare($this->conn, $sql_farmer);
        mysqli_stmt_bind_param($stmt_farmer, "i", $farmer_id);
        mysqli_stmt_execute($stmt_farmer);
        $result_farmer = mysqli_stmt_get_result($stmt_farmer);
        
        // Get market average prices by category
        $sql_market = "SELECT 
                p.category,
                AVG(p.price) AS avg_price,
                COUNT(p.id) AS product_count,
                SUM((SELECT COUNT(*) FROM orders WHERE product_id = p.id)) AS order_count,
                AVG((SELECT AVG(rating) FROM reviews r WHERE r.product_id = p.id)) AS avg_rating
                FROM products p
                WHERE p.farmer_id != ?
                GROUP BY p.category";
        
        $stmt_market = mysqli_prepare($this->conn, $sql_market);
        mysqli_stmt_bind_param($stmt_market, "i", $farmer_id);
        mysqli_stmt_execute($stmt_market);
        $result_market = mysqli_stmt_get_result($stmt_market);
        
        // Process and combine the results
        $farmer_data = [];
        while ($row = mysqli_fetch_assoc($result_farmer)) {
            $farmer_data[$row['category']] = $row;
        }
        
        $market_data = [];
        while ($row = mysqli_fetch_assoc($result_market)) {
            $market_data[$row['category']] = $row;
        }
        
        // Compare and build the analysis
        $analysis = [];
        foreach ($farmer_data as $category => $farmer_metrics) {
            $comparison = [
                'category' => $category,
                'farmer' => $farmer_metrics,
                'market' => $market_data[$category] ?? [
                    'avg_price' => 0,
                    'product_count' => 0,
                    'order_count' => 0,
                    'avg_rating' => 0
                ],
                'price_difference' => 0,
                'price_difference_percent' => 0,
                'rating_difference' => 0
            ];
            
            // Calculate differences
            if (isset($market_data[$category]) && $market_data[$category]['avg_price'] > 0) {
                $comparison['price_difference'] = $farmer_metrics['avg_price'] - $market_data[$category]['avg_price'];
                $comparison['price_difference_percent'] = ($comparison['price_difference'] / $market_data[$category]['avg_price']) * 100;
                $comparison['rating_difference'] = $farmer_metrics['avg_rating'] - $market_data[$category]['avg_rating'];
            }
            
            $analysis[] = $comparison;
        }
        
        return $analysis;
    }
    
    /**
     * Get inventory analytics
     * 
     * @param int $farmer_id The ID of the farmer
     * @return array Returns inventory analytics
     */
    public function get_inventory_analytics($farmer_id) {
        $sql = "SELECT 
                p.id,
                p.name,
                p.category,
                p.stock,
                p.price,
                (SELECT SUM(quantity) FROM orders WHERE product_id = p.id) AS total_sold,
                (SELECT MAX(date_ordered) FROM orders WHERE product_id = p.id) AS last_ordered,
                (SELECT AVG(quantity) FROM orders WHERE product_id = p.id) AS avg_order_quantity
                FROM products p
                WHERE p.farmer_id = ?
                ORDER BY p.stock ASC";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $farmer_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $inventory = [];
        $low_stock = [];
        $out_of_stock = [];
        $overstocked = [];
        $total_inventory_value = 0;
        
        while ($row = mysqli_fetch_assoc($result)) {
            // Calculate days since last order
            $row['days_since_last_order'] = $row['last_ordered'] 
                ? floor((time() - strtotime($row['last_ordered'])) / 86400) 
                : null;
            
            // Calculate daily sales rate (if possible)
            if ($row['total_sold'] && $row['days_since_last_order']) {
                $days_on_sale = max(1, $row['days_since_last_order']);
                $row['daily_sales_rate'] = $row['total_sold'] / $days_on_sale;
                $row['estimated_days_until_stockout'] = $row['stock'] > 0 
                    ? ceil($row['stock'] / max(0.1, $row['daily_sales_rate'])) 
                    : 0;
            } else {
                $row['daily_sales_rate'] = 0;
                $row['estimated_days_until_stockout'] = null;
            }
            
            // Calculate inventory value
            $row['inventory_value'] = $row['stock'] * $row['price'];
            $total_inventory_value += $row['inventory_value'];
            
            // Categorize inventory
            if ($row['stock'] <= 0) {
                $out_of_stock[] = $row;
            } elseif ($row['estimated_days_until_stockout'] !== null && $row['estimated_days_until_stockout'] < 7) {
                $low_stock[] = $row;
            } elseif ($row['estimated_days_until_stockout'] !== null && $row['estimated_days_until_stockout'] > 90 && $row['inventory_value'] > 100) {
                $overstocked[] = $row;
            }
            
            $inventory[] = $row;
        }
        
        return [
            'inventory' => $inventory,
            'low_stock' => $low_stock,
            'out_of_stock' => $out_of_stock,
            'overstocked' => $overstocked,
            'total_inventory_value' => $total_inventory_value,
            'inventory_distribution' => $this->calculate_inventory_distribution($inventory)
        ];
    }
    
    /**
     * Calculate inventory distribution by category
     * 
     * @param array $inventory Inventory data
     * @return array Category distribution
     */
    private function calculate_inventory_distribution($inventory) {
        $distribution = [];
        $total_value = 0;
        
        // Calculate total value first
        foreach ($inventory as $item) {
            $total_value += $item['inventory_value'];
        }
        
        // Group by category
        foreach ($inventory as $item) {
            $category = $item['category'];
            
            if (!isset($distribution[$category])) {
                $distribution[$category] = [
                    'category' => $category,
                    'item_count' => 0,
                    'total_stock' => 0,
                    'total_value' => 0,
                    'percentage' => 0
                ];
            }
            
            $distribution[$category]['item_count']++;
            $distribution[$category]['total_stock'] += $item['stock'];
            $distribution[$category]['total_value'] += $item['inventory_value'];
        }
        
        // Calculate percentages
        foreach ($distribution as &$category) {
            $category['percentage'] = $total_value > 0 
                ? ($category['total_value'] / $total_value) * 100 
                : 0;
        }
        
        return array_values($distribution);
    }
    
    /**
     * Export report data as CSV
     * 
     * @param array $data The data to export
     * @param array $headers The CSV headers (column names)
     * @return string CSV formatted data
     */
    public function export_csv($data, $headers = []) {
        if (empty($data)) {
            return '';
        }
        
        // If no headers provided, use the keys from the first data row
        if (empty($headers)) {
            $headers = array_keys($data[0]);
        }
        
        // Start output buffer
        ob_start();
        
        // Create a file pointer
        $output = fopen('php://output', 'w');
        
        // Add BOM for proper UTF-8 encoding in Excel
        fputs($output, "\xEF\xBB\xBF");
        
        // Add the headers
        fputcsv($output, $headers);
        
        // Add the data rows
        foreach ($data as $row) {
            $csv_row = [];
            foreach ($headers as $header) {
                $csv_row[] = isset($row[$header]) ? $row[$header] : '';
            }
            fputcsv($output, $csv_row);
        }
        
        // Close the file pointer
        fclose($output);
        
        // Get the contents of the output buffer
        $csv = ob_get_clean();
        
        return $csv;
    }
    
    /**
     * Generate a PDF report
     * Note: Requires mPDF or similar library to be installed
     * 
     * @param string $title Report title
     * @param array $data Report data
     * @param string $template Template name (optional)
     * @return string PDF content
     */
    public function export_pdf($title, $data, $template = 'default') {
        // This is a simplified implementation
        // In a real implementation, you would use a PDF library like mPDF
        
        // Build a simple HTML report
        $html = '<!DOCTYPE html>
';
        $html .= '<html><head><title>' . htmlspecialchars($title) . '</title>';
        $html .= '<style>
';
        $html .= 'body { font-family: Arial, sans-serif; margin: 30px; }
';
        $html .= 'h1 { color: #1a8d4a; }
';
        $html .= 'table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
';
        $html .= 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
';
        $html .= 'th { background-color: #f2f2f2; }
';
        $html .= 'tr:nth-child(even) { background-color: #f9f9f9; }
';
        $html .= '.report-date { color: #666; margin-bottom: 20px; }
';
        $html .= '.report-footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
';
        $html .= '</style></head><body>';
        
        // Add report header
        $html .= '<h1>' . htmlspecialchars($title) . '</h1>';
        $html .= '<div class="report-date">Generated on: ' . date('Y-m-d H:i:s') . '</div>';
        
        // Process data based on its structure
        if (is_array($data)) {
            foreach ($data as $section_title => $section_data) {
                if (is_string($section_title)) {
                    $html .= '<h2>' . htmlspecialchars($section_title) . '</h2>';
                }
                
                if (is_array($section_data) && !empty($section_data)) {
                    // Check if this is a simple array or an array of arrays
                    if (isset($section_data[0]) && is_array($section_data[0])) {
                        // Table representation
                        $html .= '<table>';
                        
                        // Table headers
                        $html .= '<tr>';
                        foreach (array_keys($section_data[0]) as $header) {
                            $html .= '<th>' . htmlspecialchars($header) . '</th>';
                        }
                        $html .= '</tr>';
                        
                        // Table rows
                        foreach ($section_data as $row) {
                            $html .= '<tr>';
                            foreach ($row as $value) {
                                $html .= '<td>' . htmlspecialchars($value) . '</td>';
                            }
                            $html .= '</tr>';
                        }
                        
                        $html .= '</table>';
                    } else {
                        // Single row as a definition list
                        $html .= '<dl>';
                        foreach ($section_data as $key => $value) {
                            $html .= '<dt>' . htmlspecialchars($key) . '</dt>';
                            $html .= '<dd>' . htmlspecialchars($value) . '</dd>';
                        }
                        $html .= '</dl>';
                    }
                }
            }
        }
        
        // Add report footer
        $html .= '<div class="report-footer">AgroSmart Market &copy; ' . date('Y') . '</div>';
        $html .= '</body></html>';
        
        // In a real implementation, you would convert this HTML to PDF
        // For now, we'll just return the HTML
        return $html;
        
        /*
        // Example code for using mPDF to generate PDF (not included in this implementation)
        require_once 'vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 20,
            'margin_bottom' => 20
        ]);
        $mpdf->WriteHTML($html);
        return $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
        */
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
