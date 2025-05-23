<?php
// Session is already started in the main file
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/User.php';

// Get current user information
$user = new User($conn);
$current_user = null;
if (isset($_SESSION['user_id'])) {
    $current_user = $user->get_user_by_id($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroSmart Market - <?php echo isset($page_title) ? $page_title : (isset($title) ? $title : 'Home'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        }
        
        .navbar {
            background-color: var(--primary-color) !important;
            color: white;
        }
        
        .navbar-dark .navbar-nav .nav-link, 
        .navbar-dark .navbar-brand {
            color: white;
        }
        
        .navbar-dark .navbar-nav .nav-link:hover, 
        .navbar-dark .navbar-brand:hover {
            color: var(--secondary-color);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #3d8b40;
            border-color: #3d8b40;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/AgroSmart Market/index.php">
                <i class="fas fa-leaf text-white me-2"></i>AgroSmart Market
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/AgroSmart Market/index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/AgroSmart Market/marketplace.php">Marketplace</a>
                    </li>
                    <?php if ($current_user && $current_user['user_type'] === 'farmer'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/AgroSmart Market/product.php">My Products</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($current_user): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/AgroSmart Market/order.php">Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/AgroSmart Market/message.php">Messages</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/AgroSmart Market/reports.php"><i class="fas fa-chart-line me-1"></i>Reports</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if ($current_user): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <?php 
                                $profile_image = !empty($current_user['profile_image']) ? 
                                    '/AgroSmart Market/images/profiles/' . $current_user['profile_image'] : 
                                    '/AgroSmart Market/images/default-profile.png';
                                ?>
                                <img src="<?php echo $profile_image; ?>" 
                                     alt="Profile" 
                                     class="rounded-circle" 
                                     style="width: 30px; height: 30px; object-fit: cover; margin-right: 5px;">
                                <?php echo htmlspecialchars($current_user['name']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/AgroSmart Market/profile.php">Profile</a></li>
                                <li><a class="dropdown-item" href="/AgroSmart Market/logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/AgroSmart Market/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/AgroSmart Market/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
    </div>
