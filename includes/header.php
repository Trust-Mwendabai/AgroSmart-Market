<?php
/**
 * Redirect to the standard header file
 * 
 * This file exists to maintain backward compatibility with any files that might include it.
 * All pages should use the standard header file from views/partials/header.php.
 */

// Include the standard header file
include_once dirname(__DIR__) . '/views/partials/header.php';
?>
        
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
