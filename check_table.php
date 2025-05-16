<?php
// Simple script to check the structure of the messages table
require_once 'config/database.php';

$table_name = "messages";
$result = mysqli_query($conn, "DESCRIBE $table_name");

if (!$result) {
    echo "Error: " . mysqli_error($conn);
    exit;
}

echo "<h2>Structure of $table_name table:</h2>";
echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

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
?>
