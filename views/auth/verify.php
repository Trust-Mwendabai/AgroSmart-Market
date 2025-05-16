<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - AgroSmart Market</title>
    
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
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .verify-container {
            max-width: 500px;
            width: 100%;
            padding: 20px;
        }
        
        .verify-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .verify-box i {
            font-size: 50px;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="logo">
            <i class="fas fa-leaf me-2"></i>AgroSmart Market
        </div>
        
        <div class="verify-box">
            <?php if (isset($error) && !empty($error)): ?>
                <i class="fas fa-times-circle text-danger"></i>
                <h2 class="mb-4">Verification Failed</h2>
                <div class="alert alert-danger"><?php echo $error; ?></div>
                <p>Please try again or contact support if the problem persists.</p>
            <?php elseif (isset($success) && !empty($success)): ?>
                <i class="fas fa-check-circle"></i>
                <h2 class="mb-4">Email Verified!</h2>
                <div class="alert alert-success"><?php echo $success; ?></div>
                <p>You can now log in to your account and start using AgroSmart Market.</p>
            <?php else: ?>
                <i class="fas fa-envelope"></i>
                <h2 class="mb-4">Verify Your Email</h2>
                <p>Please check your inbox for a verification link. Click on the link to verify your email and activate your account.</p>
                <p>If you haven't received the email, check your spam folder or request a new verification link.</p>
            <?php endif; ?>
            
            <div class="mt-4">
                <a href="auth.php?action=login" class="btn btn-primary">Go to Login</a>
                <a href="../index.php" class="btn btn-outline-secondary ms-2">Back to Home</a>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
