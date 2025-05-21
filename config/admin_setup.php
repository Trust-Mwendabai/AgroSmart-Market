<?php
require_once 'database.php';

// Create admins table
$sql = "CREATE TABLE IF NOT EXISTS admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin') NOT NULL DEFAULT 'admin',
    status BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql)) {
    echo "Admins table created successfully<br>";
} else {
    echo "Error creating admins table: " . $conn->error . "<br>";
}

// Check if default admin exists
$stmt = $conn->prepare("SELECT id FROM admins WHERE email = ?");
$stmt->bind_param("s", "admin@agrosmart.com");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Create default admin user
    $name = "System Administrator";
    $email = "admin@agrosmart.com";
    $password = password_hash("admin123", PASSWORD_DEFAULT); // Default password: admin123
    $role = "super_admin";

    $stmt = $conn->prepare("INSERT INTO admins (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        echo "Default admin user created successfully<br>";
        echo "Email: admin@agrosmart.com<br>";
        echo "Password: admin123<br>";
        echo "<strong>Please change the default password after first login!</strong><br>";
    } else {
        echo "Error creating default admin user: " . $stmt->error . "<br>";
    }
} else {
    echo "Default admin user already exists<br>";
}

// Add admin_id to users table if it doesn't exist
$sql = "ALTER TABLE users ADD COLUMN IF NOT EXISTS admin_id INT NULL,
    ADD FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL";
    
if ($conn->query($sql)) {
    echo "Admin reference added to users table successfully<br>";
} else {
    echo "Error adding admin reference to users table: " . $conn->error . "<br>";
}

// Add admin_id to products table if it doesn't exist
$sql = "ALTER TABLE products ADD COLUMN IF NOT EXISTS admin_id INT NULL,
    ADD FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL";
    
if ($conn->query($sql)) {
    echo "Admin reference added to products table successfully<br>";
} else {
    echo "Error adding admin reference to products table: " . $conn->error . "<br>";
}

// Add admin_id to orders table if it doesn't exist
$sql = "ALTER TABLE orders ADD COLUMN IF NOT EXISTS admin_id INT NULL,
    ADD FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL";
    
if ($conn->query($sql)) {
    echo "Admin reference added to orders table successfully<br>";
} else {
    echo "Error adding admin reference to orders table: " . $conn->error . "<br>";
}

echo "<br>Setup completed. You can now <a href='../admin/login.php'>login to the admin panel</a>.";
?> 