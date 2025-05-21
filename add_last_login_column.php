<?php
require_once 'config/database.php';

// Add last_login column to users table
$sql = "ALTER TABLE users ADD COLUMN last_login TIMESTAMP NULL DEFAULT NULL AFTER date_registered";

if (mysqli_query($conn, $sql)) {
    echo "Successfully added last_login column to users table\n";
    
    // Also update the get_user_by_id method to include last_login
    $user_model_path = 'models/User.php';
    $user_model_content = file_get_contents($user_model_path);
    $updated_content = str_replace(
        'date_registered AS registration_date, 
                email_verified, is_active',
        'date_registered AS registration_date, 
                last_login, email_verified, is_active',
        $user_model_content
    );
    
    if (file_put_contents($user_model_path, $updated_content)) {
        echo "Successfully updated User model to include last_login in queries\n";
    } else {
        echo "Error updating User model\n";
    }
    
} else {
    echo "Error adding last_login column: " . mysqli_error($conn) . "\n";
}

// Close connection
mysqli_close($conn);
?>
