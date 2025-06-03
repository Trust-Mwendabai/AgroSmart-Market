<?php
// List of files to remove (relative to the script location)
$filesToRemove = [
    // Temporary setup files
    'setup_product_images.php',
    'update_product_images.php',
    'rename_product_images.php',
    'process_rename.php',
    'remove_products.php',
    'setup_sample_images.php',
    'fix_images.php',
    'create_placeholder_images.php',
    
    // Database setup files (if already set up)
    'setup.php',
    'install.php',
    'update_db.php',
    // 'database.sql', // Uncomment this line after making a backup
    
    // Check and fix scripts
    'check_table.php',
    'check_users_table.php',
    'add_farmer_profile_columns.php',
    'add_farmer_profile_fields.php',
    'add_last_login_column.php',
    'fix_messages_table.php',
    
    // Dashboard files (uncomment if not needed)
    // 'agent-dashboard.php',
    // 'buyer-dashboard.php',
    // 'farmer-dashboard.php',
];

// Start HTML output
echo "<h1>File Cleanup Utility</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; line-height: 1.6; margin: 20px; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    .file-list { 
        background-color: #f8f9fa; 
        padding: 15px; 
        border-radius: 5px;
        margin: 20px 0;
    }
    button {
        padding: 8px 15px;
        margin: 5px;
        cursor: pointer;
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 4px;
    }
    button:hover {
        background-color: #c82333;
    }
</style>";

// Check if the form was submitted
if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
    $deleted = [];
    $errors = [];
    
    foreach ($_POST['files'] as $file) {
        if (file_exists($file)) {
            if (unlink($file)) {
                $deleted[] = $file;
            } else {
                $errors[] = "Could not delete: $file";
            }
        } else {
            $errors[] = "File not found: $file";
        }
    }
    
    // Show results
    echo "<div class='success'><h2>Cleanup Complete</h2>";
    
    if (!empty($deleted)) {
        echo "<p>Successfully deleted " . count($deleted) . " files:</p>";
        echo "<ul>";
        foreach ($deleted as $file) {
            echo "<li>$file</li>";
        }
        echo "</ul>";
    }
    
    if (!empty($errors)) {
        echo "<div class='warning'><h3>Errors:</h3><ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul></div>";
    }
    
    echo "<p><a href='cleanup_files.php'>Back to Cleanup</a> | <a href='index.php'>Go to Home</a></p>";
    
} else {
    // Show confirmation form
    $existingFiles = [];
    foreach ($filesToRemove as $file) {
        if (file_exists($file)) {
            $existingFiles[] = $file;
        }
    }
    
    if (empty($existingFiles)) {
        echo "<div class='success'>No cleanup needed - no temporary files found!</div>";
        echo "<p><a href='index.php'>Go to Home</a></p>";
    } else {
        echo "<div class='warning'><h2>⚠️ File Cleanup</h2>";
        echo "<p>The following files will be permanently deleted. This action cannot be undone.</p>";
        echo "<div class='file-list'><ul>";
        foreach ($existingFiles as $file) {
            echo "<li><input type='checkbox' name='files[]' value='$file' checked> $file</li>";
        }
        echo "</ul></div>";
        
        echo "<form method='post' onsubmit='return confirm(\"Are you sure you want to delete the selected files? This cannot be undone.\")'>";
        foreach ($existingFiles as $file) {
            echo "<input type='hidden' name='files[]' value='$file'>";
        }
        echo "<input type='hidden' name='confirm' value='yes'>";
        echo "<button type='submit'>Delete Selected Files</button>";
        echo " <a href='index.php' style='padding: 8px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px;'>Cancel</a>";
        echo "</form>";
        echo "</div>";
    }
}
?>

<h3>Note:</h3>
<ul>
    <li>This will only delete the files that exist in your installation.</li>
    <li>Always make a backup before running this cleanup.</li>
    <li>Some files like dashboards might be in use - uncheck them if you're not sure.</li>
</ul>
