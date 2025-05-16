<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Message - AgroSmart Market</title>
    
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
        }
        
        .navbar {
            background-color: var(--primary-color);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .user-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }
        
        .message-content {
            background-color: #fff;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
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
                    <?php if (is_farmer()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="product.php">My Products</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="order.php">Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="message.php">Messages</a>
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
    <div class="container py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Message Details</h2>
            <div>
                <?php if ($message['sender_id'] != $_SESSION['user_id']): ?>
                    <a href="message.php?action=compose&reply_to=<?php echo $message['id']; ?>" class="btn btn-outline-primary me-2">
                        <i class="fas fa-reply me-2"></i>Reply
                    </a>
                <?php endif; ?>
                
                <a href="message.php" class="btn btn-outline-secondary">
                    <i class="fas fa-chevron-left me-2"></i>Back to Inbox
                </a>
            </div>
        </div>
        
        <!-- Alerts -->
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
        
        <!-- Message Navigation -->
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="list-group">
                    <a href="message.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-inbox me-2"></i>Inbox
                        <?php 
                        // Count unread messages
                        // We already have the Message model from the controller
                        // No need to require it again
                        $unread_count = $message->count_unread_messages($_SESSION['user_id']);
                        
                        if ($unread_count > 0): 
                        ?>
                            <span class="badge bg-danger float-end"><?php echo $unread_count; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="message.php?action=sent" class="list-group-item list-group-item-action">
                        <i class="fas fa-paper-plane me-2"></i>Sent
                    </a>
                    <a href="message.php?action=compose" class="list-group-item list-group-item-action">
                        <i class="fas fa-pen me-2"></i>Compose
                    </a>
                </div>
                
                <!-- Message Actions -->
                <div class="card mt-3">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">Actions</h6>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php if ($message['sender_id'] != $_SESSION['user_id']): ?>
                            <a href="message.php?action=compose&reply_to=<?php echo $message['id']; ?>" class="list-group-item list-group-item-action">
                                <i class="fas fa-reply me-2"></i>Reply
                            </a>
                        <?php endif; ?>
                        
                        <?php if (isset($message['related_product_id']) && !empty($message['related_product_id'])): ?>
                            <a href="../products/view.php?id=<?php echo $message['related_product_id']; ?>" class="list-group-item list-group-item-action">
                                <i class="fas fa-shopping-basket me-2"></i>View Product
                            </a>
                        <?php endif; ?>
                        
                        <?php 
                        $other_user_id = ($message['sender_id'] == $_SESSION['user_id']) ? $message['receiver_id'] : $message['sender_id'];
                        ?>
                        <a href="message.php?action=compose&to=<?php echo $other_user_id; ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-comment me-2"></i>New Conversation
                        </a>
                        
                        <a href="message.php?action=delete&id=<?php echo $message['id']; ?>" 
                           class="list-group-item list-group-item-action text-danger"
                           onclick="return confirm('Are you sure you want to delete this message?');">
                            <i class="fas fa-trash-alt me-2"></i>Delete
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-9">
                <!-- Message Details -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-1"><?php echo htmlspecialchars($message['subject']); ?></h5>
                        <div class="text-muted small">
                            <span class="me-3">
                                <i class="far fa-calendar-alt me-1"></i><?php echo date('F j, Y', strtotime($message['date_sent'])); ?>
                            </span>
                            <span>
                                <i class="far fa-clock me-1"></i><?php echo date('g:i A', strtotime($message['date_sent'])); ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Message User Info -->
                        <div class="d-flex align-items-center mb-4">
                            <!-- User Image -->
                            <div class="me-3">
                                <?php 
                                $sender_image = !empty($message['sender_image']) ? $message['sender_image'] : '';
                                $sender_name = $message['sender_name'];
                                $sender_type = $message['sender_type'];
                                
                                if ($message['sender_id'] == $_SESSION['user_id']) {
                                    $label = "From: You";
                                } else {
                                    $label = "From:";
                                }
                                ?>
                                
                                <?php if (!empty($sender_image)): ?>
                                    <img src="../public/uploads/<?php echo $sender_image; ?>" class="rounded-circle user-img" alt="User">
                                <?php else: ?>
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center user-img">
                                        <?php echo strtoupper(substr($sender_name, 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- User Info -->
                            <div>
                                <p class="mb-0"><strong><?php echo $label; ?></strong> <?php if ($message['sender_id'] != $_SESSION['user_id']) echo $sender_name; ?></p>
                                <?php if ($message['sender_id'] != $_SESSION['user_id']): ?>
                                    <p class="mb-0 text-muted small">
                                        <?php echo ucfirst($sender_type); ?>
                                        <?php if (isset($message['sender_location']) && !empty($message['sender_location'])): ?>
                                            <span class="mx-1">•</span> <?php echo $message['sender_location']; ?>
                                        <?php endif; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Recipient Info (for sent messages) -->
                            <?php if ($message['sender_id'] == $_SESSION['user_id']): ?>
                                <div class="ms-auto text-end">
                                    <p class="mb-0"><strong>To:</strong> <?php echo $message['receiver_name']; ?></p>
                                    <p class="mb-0 text-muted small">
                                        <?php echo ucfirst($message['receiver_type']); ?>
                                        <?php if (isset($message['receiver_location']) && !empty($message['receiver_location'])): ?>
                                            <span class="mx-1">•</span> <?php echo $message['receiver_location']; ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Message Content -->
                        <div class="message-content">
                            <?php echo $message['message']; ?>
                            
                            <?php if (isset($message['related_product_id']) && !empty($message['related_product_id'])): ?>
                                <div class="mt-4 pt-3 border-top">
                                    <p class="mb-2"><strong>Related Product:</strong></p>
                                    <div class="card">
                                        <div class="row g-0">
                                            <div class="col-md-3">
                                                <?php if (!empty($product['image'])): ?>
                                                    <img src="../public/uploads/products/<?php echo $product['image']; ?>" class="img-fluid rounded-start" alt="<?php echo $product['name']; ?>">
                                                <?php else: ?>
                                                    <div class="bg-light d-flex align-items-center justify-content-center h-100 p-3">
                                                        <i class="fas fa-image fa-3x text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="card-body py-2">
                                                    <h5 class="card-title"><?php echo $product['name']; ?></h5>
                                                    <p class="card-text text-muted"><?php echo substr($product['description'], 0, 100) . (strlen($product['description']) > 100 ? '...' : ''); ?></p>
                                                    <p class="card-text">
                                                        <strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?>
                                                        <span class="mx-2">|</span>
                                                        <strong>Stock:</strong> <?php echo $product['stock']; ?> available
                                                    </p>
                                                    <a href="../products/view.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">View Product</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <?php if ($message['sender_id'] != $_SESSION['user_id']): ?>
                                    <button type="button" class="btn btn-primary" onclick="window.location='message.php?action=compose&reply_to=<?php echo $message['id']; ?>'">
                                        <i class="fas fa-reply me-2"></i>Reply
                                    </button>
                                <?php endif; ?>
                                
                                <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                                    <i class="fas fa-chevron-left me-2"></i>Back
                                </button>
                            </div>
                            
                            <div>
                                <button type="button" class="btn btn-outline-danger" onclick="if(confirm('Are you sure you want to delete this message?')) window.location='message.php?action=delete&id=<?php echo $message['id']; ?>'">
                                    <i class="fas fa-trash-alt me-2"></i>Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Previous Messages -->
                <?php if (!empty($previous_messages)): ?>
                    <div class="card mt-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Previous Conversation</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                <?php foreach ($previous_messages as $prev_msg): ?>
                                    <a href="message.php?action=view&id=<?php echo $prev_msg['id']; ?>" class="list-group-item list-group-item-action">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($prev_msg['subject']); ?></h6>
                                                <p class="mb-1 text-muted small">
                                                    <?php 
                                                    if ($prev_msg['sender_id'] == $_SESSION['user_id']) {
                                                        echo "You";
                                                    } else {
                                                        echo htmlspecialchars($prev_msg['sender_name']);
                                                    }
                                                    ?> 
                                                    <i class="fas fa-long-arrow-alt-right mx-1"></i> 
                                                    <?php 
                                                    if ($prev_msg['receiver_id'] == $_SESSION['user_id']) {
                                                        echo "You";
                                                    } else {
                                                        echo htmlspecialchars($prev_msg['receiver_name']);
                                                    }
                                                    ?>
                                                </p>
                                            </div>
                                            <small class="text-muted"><?php echo date('M d, g:i a', strtotime($prev_msg['date_sent'])); ?></small>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-leaf me-2"></i>AgroSmart Market</h5>
                    <p class="small">Connecting farmers directly with buyers</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="small">&copy; <?php echo date('Y'); ?> AgroSmart Market. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
