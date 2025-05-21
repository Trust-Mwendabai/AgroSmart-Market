<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'AgroSmart Market'; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="public/css/style.css">
    
    <!-- Image Placeholder CSS -->
    <link rel="stylesheet" href="public/css/image-placeholders.css">
    
    <!-- Enhanced Landing Page CSS -->
    <link rel="stylesheet" href="public/css/enhanced-landing.css">
    
    <!-- Hero v2 CSS -->
    <link rel="stylesheet" href="public/css/hero-v2.css">
    
    <!-- Additional Inline Styles -->
    <style>
        /* Any page-specific styles can go here */
        #toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
        }
        
        .scroll-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 999;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            cursor: pointer;
        }
        
        .scroll-to-top.show {
            opacity: 1;
            visibility: visible;
        }
        
        .search-results-container {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 0 0 0.5rem 0.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            z-index: 1000;
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo is_logged_in() && is_admin() ? 'admin/dashboard.php' : 'index.php'; ?>">
                <i class="fas fa-leaf me-2"></i>AgroSmart Market
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="marketplace.php">Marketplace</a>
                    </li>
                    <?php if (is_logged_in()): ?>
                        <?php if (is_farmer()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="product.php?action=manage">My Products</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="order.php">Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="message.php">
                                <i class="fas fa-envelope me-1"></i>Messages
                                <?php 
                                // Get unread message count
                                if (is_logged_in() && isset($_SESSION['user_id'])) {
                                    require_once 'models/Message.php';
                                    $message_model = new Message($conn);
                                    $unread_count = $message_model->count_unread_messages($_SESSION['user_id']);
                                    
                                    if ($unread_count > 0): 
                                ?>
                                    <span class="badge bg-danger rounded-pill ms-1"><?php echo $unread_count; ?></span>
                                <?php 
                                    endif; 
                                }
                                ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="reports.php"><i class="fas fa-chart-line me-1"></i>Reports</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <!-- Shopping Cart -->
                <?php
                // Initialize cart count
                $cart_count = 0;
                if (isset($_SESSION['cart']) && isset($_SESSION['cart']['total_quantity'])) {
                    $cart_count = $_SESSION['cart']['total_quantity'];
                }
                ?>
                <div class="d-flex align-items-center me-3">
                    <a href="cart.php" class="btn btn-outline-light position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <?php if ($cart_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
                                <?php echo $cart_count; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </div>
                
                <ul class="navbar-nav">
                    <?php if (is_logged_in()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i><?php echo $_SESSION['user_name']; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <?php if (is_farmer()): ?>
                                <li><a class="dropdown-item" href="farmer-dashboard.php">Farmer Dashboard</a></li>
                                <?php elseif (is_buyer()): ?>
                                <li><a class="dropdown-item" href="buyer-dashboard.php">Buyer Dashboard</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="auth.php?action=logout">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="auth.php?action=login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="auth.php?action=register">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container py-4">
        <!-- Display errors and success messages -->
        <?php if (isset($error) && !empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success) && !empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
