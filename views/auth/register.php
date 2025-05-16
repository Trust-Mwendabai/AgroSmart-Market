<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - AgroSmart Market</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #4CAF50;
            --secondary-color: #FFC107;
            --dark-color: #333;
            --light-color: #f4f4f4;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-color);
            padding: 40px 0;
        }
        
        .register-container {
            max-width: 600px;
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        
        .register-form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .register-form h2 {
            text-align: center;
            margin-bottom: 20px;
            color: var(--primary-color);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            width: 100%;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .user-type-btn {
            padding: 10px;
            text-align: center;
            border: 2px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .user-type-btn:hover {
            border-color: var(--primary-color);
        }
        
        .user-type-btn.active {
            border-color: var(--primary-color);
            background-color: rgba(76, 175, 80, 0.1);
        }
        
        .user-type-btn i {
            font-size: 24px;
            margin-bottom: 10px;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <i class="fas fa-leaf me-2"></i>AgroSmart Market
        </div>
        
        <div class="register-form">
            <h2>Create an Account</h2>
            
            <?php if (isset($error) && !empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (isset($success) && !empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form action="auth.php?action=register" method="POST">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                
                <div class="mb-4">
                    <label class="form-label">I want to register as:</label>
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="user-type-btn active" id="farmerBtn" onclick="selectUserType('farmer')">
                                <i class="fas fa-tractor d-block"></i>
                                <h5>Farmer</h5>
                                <p class="mb-0 text-muted small">I want to sell agricultural products</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="user-type-btn" id="buyerBtn" onclick="selectUserType('buyer')">
                                <i class="fas fa-shopping-basket d-block"></i>
                                <h5>Buyer</h5>
                                <p class="mb-0 text-muted small">I want to purchase agricultural products</p>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="user_type" id="userType" value="farmer">
                </div>
                
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                    <div class="form-text">We'll never share your email with anyone else.</div>
                </div>
                
                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location">
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div class="form-text">Password must be at least 6 characters long.</div>
                </div>
                
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="terms" required>
                    <label class="form-check-label" for="terms">I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> and <a href="#" class="text-decoration-none">Privacy Policy</a></label>
                </div>
                
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
            
            <hr>
            
            <div class="text-center">
                <p>Already have an account? <a href="auth.php?action=login" class="text-decoration-none">Login</a></p>
                <a href="../index.php" class="text-decoration-none"><i class="fas fa-arrow-left me-1"></i>Back to Home</a>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function selectUserType(type) {
            document.getElementById('userType').value = type;
            
            if (type === 'farmer') {
                document.getElementById('farmerBtn').classList.add('active');
                document.getElementById('buyerBtn').classList.remove('active');
            } else {
                document.getElementById('buyerBtn').classList.add('active');
                document.getElementById('farmerBtn').classList.remove('active');
            }
        }
    </script>
</body>
</html>
