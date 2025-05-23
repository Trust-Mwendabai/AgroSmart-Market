<?php
// Create remember_tokens table for user authentication persistence

// Start session
session_start();

// Include database connection
$conn = require_once '../config/database.php';

// SQL to create remember_tokens table
$sql = "CREATE TABLE IF NOT EXISTS remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

// Execute query
if (mysqli_query($conn, $sql)) {
    echo "<div class='alert alert-success'>Table 'remember_tokens' created successfully</div>";
} else {
    echo "<div class='alert alert-danger'>Error creating table: " . mysqli_error($conn) . "</div>";
}

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Remember Tokens Table - AgroSmart Market</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h2>AgroSmart Market - Setup Database Table</h2>
            </div>
            <div class="card-body">
                <h4>Remember Tokens Table Setup</h4>
                <p>This script creates the remember_tokens table that is used for the "Remember Me" functionality during login.</p>
                <hr>
                <div class="mt-4">
                    <a href="../admin/login.php" class="btn btn-primary">Go to Admin Login</a>
                    <a href="../index.php" class="btn btn-outline-secondary ms-2">Return to Home</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
