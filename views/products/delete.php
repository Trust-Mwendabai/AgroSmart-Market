<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Product - AgroSmart Market</title>
    
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
            flex-direction: column;
        }
        
        .navbar {
            background-color: var(--primary-color);
        }
        
        .content-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .delete-card {
            max-width: 500px;
            width: 100%;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-leaf me-2"></i>AgroSmart Market
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../marketplace.php">Marketplace</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="product.php">My Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="order.php">Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="message.php">Messages</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i><?php echo $_SESSION['user_name']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="../dashboard.php">Dashboard</a></li>
                            <li><a class="dropdown-item" href="../profile.php">My Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="auth.php?action=logout">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="content-container">
        <div class="delete-card">
            <?php if (isset($error) && !empty($error)): ?>
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-exclamation-circle me-2"></i>Error</h5>
                    </div>
                    <div class="card-body text-center">
                        <p class="mb-4"><?php echo $error; ?></p>
                        <a href="product.php" class="btn btn-primary">Back to Products</a>
                    </div>
                </div>
            <?php elseif (isset($success) && !empty($success)): ?>
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Success</h5>
                    </div>
                    <div class="card-body text-center">
                        <p class="mb-4"><?php echo $success; ?></p>
                        <div class="text-center">
                            <i class="fas fa-check-circle text-success fa-5x mb-3"></i>
                            <h4>Product Deleted Successfully</h4>
                            <p>The product has been removed from your inventory.</p>
                            <a href="product.php" class="btn btn-primary mt-3">Back to Products</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card border-warning">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Delete Product</h5>
                    </div>
                    <div class="card-body text-center">
                        <i class="fas fa-trash-alt text-danger fa-5x mb-3"></i>
                        <h4>Are you sure?</h4>
                        <p>You are about to delete this product. This action cannot be undone.</p>
                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <a href="product.php" class="btn btn-secondary">Cancel</a>
                            <a href="product.php?action=delete&id=<?php echo $_GET['id']; ?>&confirm=yes" class="btn btn-danger">Yes, Delete</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="bg-dark text-white py-3 mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <span class="small"><i class="fas fa-leaf me-2"></i>AgroSmart Market</span>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="small">&copy; <?php echo date('Y'); ?> AgroSmart Market. All rights reserved.</span>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
