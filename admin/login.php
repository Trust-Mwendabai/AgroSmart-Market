<?php
session_start();
require_once '../config/database.php';
require_once '../config/utils.php';

// Generate CSRF token if not exists
if (!isset($_SESSION['admin_csrf_token'])) {
    $_SESSION['admin_csrf_token'] = bin2hex(random_bytes(32));
}

// Check if already logged in - with enhanced security check
if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin' && isset($_SESSION['admin_login_time'])) {
    // Check if session is still valid (within 12 hours)
    $session_lifetime = 12 * 60 * 60; // 12 hours in seconds
    if (time() - $_SESSION['admin_login_time'] < $session_lifetime) {
        // Redirect with a small delay to show success message if it's a fresh login
        if (isset($_SESSION['fresh_login']) && $_SESSION['fresh_login'] === true) {
            $_SESSION['fresh_login'] = false;
            $_SESSION['dashboard_message'] = 'Welcome back, ' . $_SESSION['user_name'] . '!';
        }
        header('Location: dashboard.php');
        exit();
    }
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['admin_csrf_token']) {
        $error = 'Security verification failed. Please try again.';
    } else {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $remember_me = isset($_POST['remember_me']) ? true : false;
        
        if (empty($email) || empty($password)) {
            $error = 'Please fill in all fields';
        } else {
            // Check for admin user in the users table with more secure query
            $stmt = mysqli_prepare($conn, "SELECT id, password, name, user_type, email_verified, is_active FROM users WHERE email = ? AND user_type = 'admin'");
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) === 1) {
                $admin = mysqli_fetch_assoc($result);
                
                // Check if account is active and verified
                if (!$admin['is_active']) {
                    $error = 'Your account has been deactivated. Please contact system administrator.';
                } elseif (!$admin['email_verified']) {
                    $error = 'Please verify your email before logging in.';
                } elseif (password_verify($password, $admin['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $admin['id'];
                    $_SESSION['user_type'] = $admin['user_type'];
                    $_SESSION['user_name'] = $admin['name'];
                    $_SESSION['admin_login_time'] = time();
                    $_SESSION['fresh_login'] = true;
                    $_SESSION['just_logged_in'] = true; // Flag for displaying welcome message
                    
                    // Remember me functionality
                    if ($remember_me) {
                        // Set a cookie that lasts for 30 days
                        $token = bin2hex(random_bytes(32));
                        $expiry = time() + (30 * 24 * 60 * 60); // 30 days
                        
                        // Store token in database (you may want to create a separate table for this)
                        $hashed_token = password_hash($token, PASSWORD_DEFAULT);
                        $delete_old = mysqli_prepare($conn, "DELETE FROM remember_tokens WHERE user_id = ?");
                        mysqli_stmt_bind_param($delete_old, "i", $admin['id']);
                        mysqli_stmt_execute($delete_old);
                        
                        $insert = mysqli_prepare($conn, "INSERT INTO remember_tokens (user_id, token, expires) VALUES (?, ?, ?)");
                        mysqli_stmt_bind_param($insert, "iss", $admin['id'], $hashed_token, date('Y-m-d H:i:s', $expiry));
                        mysqli_stmt_execute($insert);
                        
                        // Set the cookie
                        setcookie('admin_remember', $admin['id'] . ':' . $token, $expiry, '/', '', false, true);
                    }
                    
                    // Update last login timestamp
                    $update_sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
                    $update_stmt = mysqli_prepare($conn, $update_sql);
                    mysqli_stmt_bind_param($update_stmt, "i", $admin['id']);
                    mysqli_stmt_execute($update_stmt);
                    
                    // Log the successful login attempt for security auditing
                    try {
                        $ip = $_SERVER['REMOTE_ADDR'];
                        $user_agent = $_SERVER['HTTP_USER_AGENT'];
                        $log_sql = "INSERT INTO login_logs (user_id, ip_address, user_agent, status) VALUES (?, ?, ?, 'success')"; 
                        $log_stmt = mysqli_prepare($conn, $log_sql);
                        mysqli_stmt_bind_param($log_stmt, "iss", $admin['id'], $ip, $user_agent);
                        mysqli_stmt_execute($log_stmt);
                    } catch (Exception $e) {
                        // Silently log error but don't prevent login
                        error_log('Login log failed: ' . $e->getMessage());
                    }
                    
                    header('Location: dashboard.php');
                    exit();
                } else {
                    $error = 'Invalid password';
                    
                    // Log failed login attempt
                    try {
                        $ip = $_SERVER['REMOTE_ADDR'];
                        $user_agent = $_SERVER['HTTP_USER_AGENT'];
                        $log_sql = "INSERT INTO login_logs (user_id, ip_address, user_agent, status) VALUES (?, ?, ?, 'failed')"; 
                        $log_stmt = mysqli_prepare($conn, $log_sql);
                        mysqli_stmt_bind_param($log_stmt, "iss", $admin['id'], $ip, $user_agent);
                        mysqli_stmt_execute($log_stmt);
                    } catch (Exception $e) {
                        // Silently log error but don't prevent showing login error
                        error_log('Failed login log failed: ' . $e->getMessage());
                    }
                }
            } else {
                $error = 'No admin account found with this email';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - AgroSmart Market</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom Admin Styles -->
    <link rel="stylesheet" href="../public/css/style.css">
    <style>
        :root {
            --primary-color: #2ecc71;
            --secondary-color: #27ae60;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .admin-login-container {
            width: 100%;
            max-width: 450px;
            padding: 15px;
        }
        
        .admin-login-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: none;
        }
        
        .admin-login-header {
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }
        
        .admin-login-header h2 {
            margin-bottom: 0;
            font-weight: 600;
        }
        
        .admin-login-logo {
            margin-bottom: 15px;
            font-size: 40px;
        }
        
        .admin-login-body {
            padding: 30px;
        }
        
        .form-floating {
            margin-bottom: 20px;
        }
        
        .form-floating label {
            color: #6c757d;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(46, 204, 113, 0.25);
        }
        
        .input-group-text {
            background-color: var(--light-color);
            border-right: none;
        }
        
        .password-input {
            border-left: none;
        }
        
        .btn-admin-login {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            padding: 12px;
            font-weight: 500;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .btn-admin-login:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .back-to-site {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--dark-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .back-to-site:hover {
            color: var(--primary-color);
        }
        
        .alert {
            border-radius: 5px;
            border-left: 4px solid;
        }
        
        .alert-danger {
            border-left-color: #dc3545;
            background-color: rgba(220, 53, 69, 0.1);
        }
    </style>
</head>
<body>
    <div class="admin-login-container">
        <div class="admin-login-card card">
            <div class="admin-login-header">
                <div class="admin-login-logo">
                    <i class="fas fa-leaf"></i>
                </div>
                <h2>AgroSmart Admin</h2>
                <p class="mb-0">Control Panel Login</p>
            </div>
            
            <div class="admin-login-body">
                <?php if (isset($_SESSION['logout_message'])): ?>
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <div><?php echo $_SESSION['logout_message']; ?></div>
                    </div>
                    <?php unset($_SESSION['logout_message']); ?>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <div><?php echo $error; ?></div>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['admin_csrf_token']; ?>">
                    
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                        <label for="email"><i class="fas fa-envelope me-2"></i>Email address</label>
                        <div class="invalid-feedback">Please enter a valid email address.</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <div class="form-floating flex-grow-1">
                                <input type="password" class="form-control password-input" id="password" name="password" placeholder="Password" required>
                                <label for="password">Password</label>
                            </div>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">Please enter your password.</div>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="remember_me" name="remember_me">
                        <label class="form-check-label" for="remember_me">
                            Remember me for 30 days
                        </label>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-admin-login">
                            <i class="fas fa-sign-in-alt me-2"></i>Login to Dashboard
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <a href="../index.php" class="back-to-site">
            <i class="fas fa-arrow-left me-1"></i> Back to AgroSmart Market
        </a>
    </div>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>