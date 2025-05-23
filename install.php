<?php
/**
 * AgroSmart Market Installation Script
 * 
 * This script helps set up the AgroSmart Market database and initialize
 * the application on a new environment.
 */

// Start with a clean slate
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Buffer output for smooth rendering
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroSmart Market - Installation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #2ecc71;
            --secondary-color: #27ae60;
            --dark-color: #2c3e50;
        }
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        .install-container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .install-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .step {
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .step-success {
            background-color: rgba(46, 204, 113, 0.1);
            border-left: 4px solid #2ecc71;
        }
        .step-error {
            background-color: rgba(231, 76, 60, 0.1);
            border-left: 4px solid #e74c3c;
        }
        .step-pending {
            background-color: rgba(52, 152, 219, 0.1);
            border-left: 4px solid #3498db;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-header">
            <i class="fas fa-leaf fa-3x text-success mb-3"></i>
            <h1>AgroSmart Market Installation</h1>
            <p class="text-muted">Follow the steps below to set up your AgroSmart Market application</p>
        </div>
        
        <div class="install-body">
            <?php
            $config_file = 'config/database.php';
            $database_file = 'database.sql';
            $sample_data_file = 'setup_sample_data.php';
            $steps_completed = 0;
            $total_steps = 4;
            
            // Step 1: Check if config file exists
            echo '<div class="step ' . (file_exists($config_file) ? 'step-success' : 'step-error') . '">';
            echo '<h4><i class="fas ' . (file_exists($config_file) ? 'fa-check-circle text-success' : 'fa-times-circle text-danger') . '"></i> Step 1: Configuration Check</h4>';
            
            if (file_exists($config_file)) {
                echo '<p>Configuration file found.</p>';
                $steps_completed++;
                
                // Include database connection to test it
                require_once $config_file;
                
                // Step 2: Test database connection
                echo '</div><div class="step ' . (isset($conn) && $conn ? 'step-success' : 'step-error') . '">';
                echo '<h4><i class="fas ' . (isset($conn) && $conn ? 'fa-check-circle text-success' : 'fa-times-circle text-danger') . '"></i> Step 2: Database Connection</h4>';
                
                if (isset($conn) && $conn) {
                    echo '<p>Successfully connected to database.</p>';
                    $steps_completed++;
                    
                    // Step 3: Check if database is set up
                    $tables_exist = false;
                    $result = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
                    $tables_exist = mysqli_num_rows($result) > 0;
                    
                    echo '</div><div class="step ' . ($tables_exist ? 'step-success' : 'step-pending') . '">';
                    echo '<h4><i class="fas ' . ($tables_exist ? 'fa-check-circle text-success' : 'fa-exclamation-circle text-warning') . '"></i> Step 3: Database Structure</h4>';
                    
                    if ($tables_exist) {
                        echo '<p>Database tables already exist.</p>';
                        $steps_completed++;
                    } else {
                        echo '<p>Database tables need to be created.</p>';
                        
                        if (isset($_POST['setup_database']) && $_POST['setup_database'] == 'yes') {
                            // Import the SQL file
                            $sql_content = file_get_contents($database_file);
                            $sql_statements = explode(';', $sql_content);
                            
                            $success = true;
                            $error_message = '';
                            
                            foreach ($sql_statements as $statement) {
                                $statement = trim($statement);
                                if (!empty($statement)) {
                                    if (!mysqli_query($conn, $statement . ';')) {
                                        $success = false;
                                        $error_message = mysqli_error($conn);
                                        break;
                                    }
                                }
                            }
                            
                            if ($success) {
                                echo '<div class="alert alert-success">Database structure successfully created!</div>';
                                $steps_completed++;
                                $tables_exist = true;
                            } else {
                                echo '<div class="alert alert-danger">Error creating database structure: ' . $error_message . '</div>';
                            }
                        } else {
                            echo '<form method="post" class="mt-3">';
                            echo '<input type="hidden" name="setup_database" value="yes">';
                            echo '<button type="submit" class="btn btn-primary">Set Up Database Structure</button>';
                            echo '</form>';
                        }
                    }
                    
                    // Step 4: Sample Data
                    echo '</div><div class="step ' . ($tables_exist ? 'step-pending' : 'step-error') . '">';
                    echo '<h4><i class="fas fa-database"></i> Step 4: Sample Data</h4>';
                    
                    if ($tables_exist) {
                        if (isset($_POST['load_sample_data']) && $_POST['load_sample_data'] == 'yes') {
                            echo '<p>Redirecting to sample data setup...</p>';
                            echo '<script>window.location.href = "' . $sample_data_file . '";</script>';
                        } else {
                            echo '<p>You can optionally load sample data to help you get started.</p>';
                            echo '<form method="post" class="mt-3">';
                            echo '<input type="hidden" name="load_sample_data" value="yes">';
                            echo '<button type="submit" class="btn btn-success">Load Sample Data</button>';
                            echo '</form>';
                        }
                    } else {
                        echo '<p>Please set up the database structure first.</p>';
                    }
                } else {
                    echo '<p>Failed to connect to database. Please check your database credentials in the config file.</p>';
                    echo '<p>Make sure to update the database credentials in <code>' . $config_file . '</code>:</p>';
                    echo '<pre class="bg-light p-3">
$db_host = "localhost";
$db_user = "your_username";
$db_pass = "your_password";
$db_name = "agrosmart_market";
                    </pre>';
                }
            } else {
                echo '<p>Configuration file not found. Please make sure you have the proper config/database.php file.</p>';
                echo '<p>Create a file at <code>' . $config_file . '</code> with the following content:</p>';
                echo '<pre class="bg-light p-3">
&lt;?php
// Database connection parameters
$db_host = "localhost";
$db_user = "your_username";
$db_pass = "your_password";
$db_name = "agrosmart_market";

// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?&gt;
                </pre>';
            }
            echo '</div>';
            
            // Installation progress
            $progress_percentage = ($steps_completed / $total_steps) * 100;
            ?>
            
            <div class="mt-4">
                <h5>Installation Progress</h5>
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress_percentage; ?>%;" 
                         aria-valuenow="<?php echo $progress_percentage; ?>" aria-valuemin="0" aria-valuemax="100">
                        <?php echo round($progress_percentage); ?>%
                    </div>
                </div>
            </div>
            
            <?php if ($steps_completed == $total_steps): ?>
            <div class="alert alert-success mt-4">
                <h4><i class="fas fa-check-circle"></i> Installation Complete!</h4>
                <p>Your AgroSmart Market application is now set up and ready to use.</p>
                <a href="index.php" class="btn btn-success">Go to Homepage</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
ob_end_flush();
?>
