<?php
// Include database connection
$conn = require_once 'database.php';

// Create login_logs table
$sql = "CREATE TABLE IF NOT EXISTS `login_logs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `user_agent` TEXT NOT NULL,
  `status` ENUM('success', 'failed') NOT NULL,
  `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

// Execute query
if (mysqli_query($conn, $sql)) {
    echo "Login logs table created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
echo "<br>You can now <a href='../admin/login.php'>return to the login page</a>";
?>
