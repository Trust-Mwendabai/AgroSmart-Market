<?php
// Include database connection
$conn = require_once 'database.php';

// Create revenue_transactions table
$sql = "CREATE TABLE IF NOT EXISTS `revenue_transactions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `amount` DECIMAL(10,2) NOT NULL, -- Amount in Zambian Kwacha (K)
  `stream_type` ENUM('commission', 'ad', 'premium_listing', 'transport_fee', 'subscription') NOT NULL,
  `description` TEXT NULL,
  `reference_id` INT NULL, -- Reference to order, product, etc.
  `transaction_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_stream_type` (`stream_type`),
  INDEX `idx_transaction_date` (`transaction_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

// Execute query
if (mysqli_query($conn, $sql)) {
    echo "Revenue transactions table created successfully";
    
    // Insert sample data if the table is empty
    $check = mysqli_query($conn, "SELECT COUNT(*) as count FROM revenue_transactions");
    $row = mysqli_fetch_assoc($check);
    
    if ($row['count'] == 0) {
        // Sample data - Insert various revenue streams with sample data for the current year
        $sample_data = [
            // Commissions (1% of order value)
            ['amount' => 250.00, 'stream_type' => 'commission', 'description' => 'Order commission - 1%', 'date_offset' => '-10 days'],
            ['amount' => 180.50, 'stream_type' => 'commission', 'description' => 'Order commission - 1%', 'date_offset' => '-20 days'],
            ['amount' => 320.75, 'stream_type' => 'commission', 'description' => 'Order commission - 1%', 'date_offset' => '-1 month'],
            ['amount' => 410.25, 'stream_type' => 'commission', 'description' => 'Order commission - 1%', 'date_offset' => '-2 months'],
            ['amount' => 275.00, 'stream_type' => 'commission', 'description' => 'Order commission - 1%', 'date_offset' => '-3 months'],
            
            // In-app Ads
            ['amount' => 1500.00, 'stream_type' => 'ad', 'description' => 'Featured banner ad - NGO', 'date_offset' => '-15 days'],
            ['amount' => 1200.00, 'stream_type' => 'ad', 'description' => 'Sidebar ad - Agricultural supplier', 'date_offset' => '-1 month'],
            ['amount' => 1800.00, 'stream_type' => 'ad', 'description' => 'Full page ad - Government program', 'date_offset' => '-2 months'],
            
            // Premium Listings
            ['amount' => 350.00, 'stream_type' => 'premium_listing', 'description' => 'Featured farmer listing - 1 month', 'date_offset' => '-5 days'],
            ['amount' => 350.00, 'stream_type' => 'premium_listing', 'description' => 'Featured farmer listing - 1 month', 'date_offset' => '-1 month'],
            ['amount' => 350.00, 'stream_type' => 'premium_listing', 'description' => 'Featured farmer listing - 1 month', 'date_offset' => '-2 months'],
            ['amount' => 1000.00, 'stream_type' => 'premium_listing', 'description' => 'Premium product placement - 3 months', 'date_offset' => '-1 month'],
            
            // Transport Facilitation Fees
            ['amount' => 450.00, 'stream_type' => 'transport_fee', 'description' => 'Transport arrangement fee - Large order', 'date_offset' => '-7 days'],
            ['amount' => 300.00, 'stream_type' => 'transport_fee', 'description' => 'Transport arrangement fee - Medium order', 'date_offset' => '-1 month'],
            ['amount' => 200.00, 'stream_type' => 'transport_fee', 'description' => 'Transport arrangement fee - Small order', 'date_offset' => '-2 months'],
            
            // Buyer Subscription Plans
            ['amount' => 750.00, 'stream_type' => 'subscription', 'description' => 'Premium buyer subscription - Monthly', 'date_offset' => '-10 days'],
            ['amount' => 750.00, 'stream_type' => 'subscription', 'description' => 'Premium buyer subscription - Monthly', 'date_offset' => '-1 month'],
            ['amount' => 750.00, 'stream_type' => 'subscription', 'description' => 'Premium buyer subscription - Monthly', 'date_offset' => '-2 months'],
            ['amount' => 2000.00, 'stream_type' => 'subscription', 'description' => 'Business buyer subscription - Quarterly', 'date_offset' => '-1 month']
        ];
        
        foreach ($sample_data as $item) {
            $amount = $item['amount'];
            $stream_type = $item['stream_type'];
            $description = $item['description'];
            $date = date('Y-m-d H:i:s', strtotime($item['date_offset']));
            
            $insert_sql = "INSERT INTO revenue_transactions (amount, stream_type, description, transaction_date) 
                           VALUES ('$amount', '$stream_type', '$description', '$date')";
            mysqli_query($conn, $insert_sql);
        }
        
        echo "<br>Sample revenue data inserted successfully";
    }
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
echo "<br>You can now <a href='../admin/dashboard.php'>return to the dashboard</a>";
?>
