<?php
/**
 * Database Configuration File (SAMPLE)
 * 
 * Instructions:
 * 1. Copy this file and rename it to 'database.php'
 * 2. Update the credentials with your database information
 * 3. Make sure your database exists before connecting
 */

// Database connection parameters
$db_host = "localhost";     // Database host (usually localhost)
$db_user = "root";          // Database username
$db_pass = "";              // Database password
$db_name = "agrosmart_market"; // Database name

// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Optional: Set character set
mysqli_set_charset($conn, "utf8mb4");

// Helper function to generate tokens
function generate_token($length = 32) {
    return bin2hex(random_bytes($length / 2));
}
?>
