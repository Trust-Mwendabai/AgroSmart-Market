<?php
/**
 * AgroSmart Market - Fix Messages Table
 * 
 * This script adds the missing related_product_id column to the messages table
 */

// Include database connection
require_once 'config/database.php';

// Check if column exists
$check_query = "SHOW COLUMNS FROM messages LIKE 'related_product_id'";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) == 0) {
    // Column doesn't exist, add it
    $alter_query = "ALTER TABLE messages ADD COLUMN related_product_id INT NULL DEFAULT NULL, 
                   ADD CONSTRAINT fk_product FOREIGN KEY (related_product_id) 
                   REFERENCES products(id) ON DELETE SET NULL";
    
    if (mysqli_query($conn, $alter_query)) {
        echo "<div style='padding: 20px; background-color: #d4edda; color: #155724; border-radius: 5px;'>";
        echo "<h2>Success!</h2>";
        echo "<p>Added 'related_product_id' column to the messages table.</p>";
        echo "</div>";
    } else {
        echo "<div style='padding: 20px; background-color: #f8d7da; color: #721c24; border-radius: 5px;'>";
        echo "<h2>Error</h2>";
        echo "<p>Failed to add column: " . mysqli_error($conn) . "</p>";
        
        // Try without the foreign key constraint in case that's causing issues
        $alter_simple = "ALTER TABLE messages ADD COLUMN related_product_id INT NULL DEFAULT NULL";
        if (mysqli_query($conn, $alter_simple)) {
            echo "<p>Added the column without foreign key constraint.</p>";
        }
        
        echo "</div>";
    }
} else {
    echo "<div style='padding: 20px; background-color: #cce5ff; color: #004085; border-radius: 5px;'>";
    echo "<h2>Column Already Exists</h2>";
    echo "<p>The 'related_product_id' column already exists in the messages table.</p>";
    echo "</div>";
}

// Show current table structure
$result = mysqli_query($conn, "DESCRIBE messages");

echo "<h3>Current structure of messages table:</h3>";
echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr style='background-color: #f2f2f2;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . $row['Default'] . "</td>";
    echo "<td>" . $row['Extra'] . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<p style='margin-top: 20px;'><a href='index.php' style='padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Back to Homepage</a></p>";
?>
