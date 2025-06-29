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
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="public/css/style.css">
    
    <!-- Navbar Scroll Effect JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.querySelector('.navbar');
        
        // Ensure navbar has correct background at all times
        if (!navbar.classList.contains('navbar-dark')) {
            navbar.classList.add('navbar-dark');
        }
        
        // Make sure background color is applied immediately
        navbar.style.backgroundColor = '#1a8d4a';
        
        // Function to update navbar on scroll
        function updateNavbar() {
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
                // Ensure background color is always set, even when scrolled to top
                navbar.style.backgroundColor = '#1a8d4a';
            }
        }
        
        // Add scroll event listener with passive option for better performance
        window.addEventListener('scroll', updateNavbar, { passive: true });
        
        // Call once on page load to set initial state
        updateNavbar();
    });
    </script>
    
    <!-- Image Placeholder CSS -->
    <link rel="stylesheet" href="public/css/image-placeholders.css">
    
    <!-- Enhanced Landing Page CSS -->
    <link rel="stylesheet" href="public/css/enhanced-landing.css">
    
    <!-- Hero v2 CSS -->
    <link rel="stylesheet" href="public/css/hero-v2.css">
    
    <!-- Enhanced Sections CSS with Animations -->
    <link rel="stylesheet" href="public/css/enhanced-sections.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="public/css/animations-custom.css">
    
    <!-- Modern Footer CSS -->
    <link rel="stylesheet" href="public/css/footer.css">
    
    <!-- Cart CSS -->
    <link rel="stylesheet" href="public/css/cart.css">
    
    <!-- Additional Inline Styles -->
    <style>
        /* Any page-specific styles can go here */
        #toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
        }
        
        /* Enhanced Navbar Styling */
        .navbar {
            transition: all 0.3s ease-in-out;
            padding-top: 15px;
            padding-bottom: 15px;
        }
        
        .navbar.navbar-scrolled {
            padding-top: 10px;
            padding-bottom: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            background-color: #156e3a !important; /* Darker when scrolled */
        }
        
        /* Add a subtle indicator for active nav items */
        .navbar-dark .navbar-nav .nav-link.active {
            position: relative;
        }
        
        .navbar-dark .navbar-nav .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 25%;
            width: 50%;
            height: 2px;
            background-color: #ffffff;
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
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo is_logged_in() && is_admin() ? 'admin/dashboard.php' : 'index.php'; ?>">
                <i class="fas fa-leaf me-2"></i>AgroSmart Market
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php
                // Determine the current page for navigation highlighting
                $current_page = basename($_SERVER['SCRIPT_NAME']);
                ?>
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="index.php">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'marketplace.php') ? 'active' : ''; ?>" href="marketplace.php">
                            <i class="fas fa-store me-1"></i>Marketplace
                        </a>
                    </li>
                    <?php if (is_logged_in()): ?>
                        <?php if (is_farmer()): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($current_page == 'product.php' && isset($_GET['action']) && $_GET['action'] == 'manage') ? 'active' : ''; ?>" href="product.php?action=manage">
                                    <i class="fas fa-boxes me-1"></i>My Products
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'order.php') ? 'active' : ''; ?>" href="order.php">
                                <i class="fas fa-shopping-basket me-1"></i>Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'message.php') ? 'active' : ''; ?>" href="message.php">
                                <i class="fas fa-envelope me-1"></i>Messages
                                <?php 
                                // Get unread message count
                                if (is_logged_in() && isset($_SESSION['user_id'])) {
                                    // Use absolute path with dirname() to ensure path is correct regardless of inclusion context
                                    require_once dirname(dirname(dirname(__FILE__))) . '/models/Message.php';
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
                            <a class="nav-link <?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>" href="reports.php">
                                <i class="fas fa-chart-line me-1"></i>Reports
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                <!-- Shopping Cart Dropdown -->
                <?php
                // Initialize cart variables
                $cart_count = $_SESSION['cart']['total_quantity'] ?? 0;
                $cart_total = $_SESSION['cart']['total_price'] ?? 0;
                $cart_items = $_SESSION['cart']['items'] ?? [];
                ?>
                
                <!-- Cart Dropdown -->
                <div class="dropdown me-3">
                    <button class="btn btn-success position-relative dropdown-toggle" type="button" id="cartDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-shopping-cart"></i>
                        <?php if ($cart_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark cart-count-badge">
                                <?php echo $cart_count; ?>
                            </span>
                        <?php endif; ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="cartDropdown" style="min-width: 320px;">
                        <div class="mini-cart-items">
                            <?php 
                            $cart_items = [];
                            $cart_total = 0;
                            
                            if (isset($_SESSION['cart']['items']) && !empty($_SESSION['cart']['items'])) {
                                $cart_items = $_SESSION['cart']['items'];
                                $cart_total = $_SESSION['cart']['total_price'] ?? 0;
                                
                                if (count($cart_items) > 0) {
                                    $count = 0;
                                    foreach (array_slice($cart_items, 0, 3) as $item) {
                                        $count++;
                                        ?>
                                        <div class="p-3 border-bottom">
                                            <div class="d-flex">
                                                <div style="width: 60px; height: 60px; overflow: hidden;" class="me-3">
                                                    <img src="<?php echo !empty($item['image']) ? 'public/uploads/' . $item['image'] : 'assets/img/placeholder-product.png'; ?>" 
                                                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                         class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                                        <strong>K<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    
                                    if (count($cart_items) > 3) {
                                        ?>
                                        <div class="p-2 text-center border-top">
                                            <small class="text-muted">+<?php echo count($cart_items) - 3; ?> more item<?php echo (count($cart_items) - 3) > 1 ? 's' : ''; ?> in cart</small>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <div class="p-3 border-top bg-light">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong>Total:</strong>
                                            <strong>K<?php echo number_format($cart_total, 2); ?></strong>
                                        </div>
                                        <a href="cart.php" class="btn btn-primary w-100 btn-sm">View Cart</a>
                                    </div>
                                    <?php
                                }
                            } else {
                                ?>
                                <div class="p-3 text-center">
                                    <i class="fas fa-shopping-cart fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">Your cart is empty</p>
                                    <a href="marketplace.php" class="btn btn-outline-primary btn-sm mt-2">Start Shopping</a>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                
                <ul class="navbar-nav">
                    <?php if (is_logged_in()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User'; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="navbarDropdown">
                                <?php if (is_farmer()): ?>
                                <li><a class="dropdown-item" href="farmer-dashboard.php"><i class="fas fa-tractor me-2 text-success"></i>Farmer Dashboard</a></li>
                                <?php elseif (is_buyer()): ?>
                                <li><a class="dropdown-item" href="buyer-dashboard.php"><i class="fas fa-shopping-bag me-2 text-primary"></i>Buyer Dashboard</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2 text-secondary"></i>My Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="auth.php?action=logout"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'auth.php' && isset($_GET['action']) && $_GET['action'] == 'login') ? 'active' : ''; ?>" href="auth.php?action=login">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'auth.php' && isset($_GET['action']) && $_GET['action'] == 'register') ? 'active' : ''; ?>" href="auth.php?action=register">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
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
