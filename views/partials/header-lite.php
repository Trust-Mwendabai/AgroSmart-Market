<!DOCTYPE html>
<html lang="<?php echo isset($_SESSION['language']) ? $_SESSION['language'] : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>AgroSmart Market</title>
    
    <!-- Meta tags for better SEO and sharing -->
    <meta name="description" content="AgroSmart Market - Connecting farmers and buyers in Zambia">
    <meta property="og:title" content="<?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>AgroSmart Market">
    <meta property="og:description" content="AgroSmart Market - Connecting farmers and buyers in Zambia">
    <meta property="og:type" content="website">
    
    <!-- CSRF token for form security -->
    <meta name="csrf-token" content="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">
    
    <!-- Core CSS -->
    <link rel="stylesheet" href="public/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="public/css/enhanced-dashboard.css">
    
    <!-- High contrast mode support -->
    <?php if (isset($_COOKIE['high_contrast']) && $_COOKIE['high_contrast'] === 'true'): ?>
    <link rel="stylesheet" href="public/css/high-contrast.css">
    <?php endif; ?>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="public/img/favicon.ico" type="image/x-icon">
    
    <style>
        /* Critical CSS - inline for faster loading */
        body {
            padding-top: 56px;
            background-color: #f8f9fa;
        }
        
        .navbar-brand img {
            max-height: 40px;
        }
        
        .navbar-light {
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .high-contrast {
            background-color: #000;
            color: #fff;
        }
        
        .high-contrast .card {
            background-color: #222;
            color: #fff;
        }
        
        .connection-status {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 50px;
            font-size: 0.75rem;
            margin-left: 0.5rem;
        }
        
        .connection-status.online {
            background-color: #d4edda;
            color: #155724;
        }
        
        .connection-status.offline {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #FF5722;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
    </style>
</head>
<body class="<?php echo isset($_COOKIE['high_contrast']) && $_COOKIE['high_contrast'] === 'true' ? 'high-contrast' : ''; ?>">
    <!-- Screen reader announcer -->
    <div id="screen-reader-announcer" class="sr-only" aria-live="polite" aria-atomic="true"></div>
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="public/img/logo.png" alt="AgroSmart Market">
            </a>
            
            <div class="d-flex align-items-center order-lg-last">
                <!-- Cart Icon -->
                <?php if (isset($_SESSION['user_id']) && (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'buyer')): ?>
                <a href="cart.php" class="nav-link d-flex align-items-center me-3 position-relative">
                    <i class="fas fa-shopping-cart fa-lg"></i>
                    <?php
                    $cart_count = 0;
                    if (isset($_SESSION['cart']['items']) && is_array($_SESSION['cart']['items'])) {
                        $cart_count = count($_SESSION['cart']['items']);
                    }
                    ?>
                    <?php if ($cart_count > 0): ?>
                    <span class="cart-count" id="cart-count"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                    <span class="d-none d-md-inline ms-2">Cart</span>
                </a>
                <?php endif; ?>
                
                <!-- Connection Status -->
                <span id="connection-status" class="connection-status online d-none d-md-inline-flex">
                    <i class="fas fa-wifi me-1"></i> Online
                </span>
                
                <!-- Toggle button for mobile -->
                <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i> Home
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="marketplace.php">
                            <i class="fas fa-store me-1"></i> Marketplace
                        </a>
                    </li>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'buyer'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="buyer-dashboard.php">
                                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                            </a>
                        </li>
                        <?php elseif (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'farmer'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="farmer-dashboard.php">
                                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                            </a>
                        </li>
                        <?php elseif (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'agent'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="agent-dashboard.php">
                                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i> Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">
                                <i class="fas fa-user-plus me-1"></i> Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="py-4">
        <!-- Content will be inserted here -->
