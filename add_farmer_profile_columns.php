<?php
require_once 'config/database.php';

// Add NRC number column for farmers
$sql = "ALTER TABLE users ADD COLUMN nrc_number VARCHAR(20) NULL DEFAULT NULL AFTER location";
if (!mysqli_query($conn, $sql)) {
    echo "Error adding nrc_number column: " . mysqli_error($conn) . "\n";
}

// Add literacy level column for farmers
$sql = "ALTER TABLE users ADD COLUMN literacy_level ENUM('Illiterate', 'Basic', 'Primary', 'Secondary', 'Tertiary') NULL DEFAULT NULL AFTER nrc_number";
if (!mysqli_query($conn, $sql)) {
    echo "Error adding literacy_level column: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
?>
