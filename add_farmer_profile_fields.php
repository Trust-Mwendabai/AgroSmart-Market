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

// Update the User model to include these new fields
$user_model_path = 'models/User.php';
$user_model_content = file_get_contents($user_model_path);

// Update get_user_by_id to include new fields
$updated_content = str_replace(
    'date_registered AS registration_date, 
                last_login, email_verified, is_active',
    'date_registered AS registration_date, 
                last_login, nrc_number, literacy_level, email_verified, is_active',
    $user_model_content
);

// Update update_profile method to handle new fields
$updated_content = str_replace(
    'public function update_profile($user_id, $data) {',
    'public function update_profile($user_id, $data) {
        // For farmers, ensure required fields are present
        if ($data["user_type"] === "farmer") {
            $required_fields = ["name", "email", "phone", "location", "nrc_number", "literacy_level"];
            foreach ($required_fields as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    return ["error" => "All farmer profile fields are required"];
                }
            }
        }
        
        // Prepare update query
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if ($key !== "user_type") { // Don\'t update user_type directly
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        $fields[] = "updated_at = NOW()";
        $fields = implode(", ", $fields);
        
        $sql = "UPDATE users SET " . $fields . " WHERE id = ?";
        $values[] = $user_id;
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, str_repeat("s", count($values)), ...$values);
        
        if (mysqli_stmt_execute($stmt)) {
            return ["success" => true];
        } else {
            return ["error" => "Update failed: " . mysqli_error($conn)];
        }
    }',
    $updated_content
);

if (file_put_contents($user_model_path, $updated_content)) {
    echo "Successfully updated User model\n";
} else {
    echo "Error updating User model\n";
}

mysqli_close($conn);
?>
