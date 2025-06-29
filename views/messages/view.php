<?php
// Check if this file has already been included to prevent duplication
if (defined('MESSAGE_VIEW_INCLUDED')) {
    return;
}
define('MESSAGE_VIEW_INCLUDED', true);

// Debug: Check if $message_item is set and its type
if (!isset($message_item)) {
    die('Error: $message_item is not defined');
}

// Set the page title
$page_title = 'Message Details - AgroSmart Market';

// Include header - only once
include_once dirname(__DIR__) . '/../views/partials/header.php';

// Message view specific styles
?>
<style>
    .message-header {
        border-bottom: 1px solid rgba(0,0,0,0.1);
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .message-content {
        line-height: 1.6;
        color: #333;
    }
    
    .message-attachment {
        background-color: rgba(76, 175, 80, 0.1);
        border-radius: 8px;
    }
    
    .message-thread-item {
        border-left: 3px solid #4CAF50;
        padding-left: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .message-sender-info {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .sender-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 1rem;
        background-color: #e9ecef;
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

<!-- Main Content -->
    <div class="container py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Message Details</h2>
            <div>
                <?php if ($message_item['sender_id'] != $_SESSION['user_id']): ?>
                    <a href="message.php?action=compose&reply_to=<?php echo $message_item['id']; ?>" class="btn btn-outline-primary me-2">
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
                        <?php if ($message_item['sender_id'] != $_SESSION['user_id']): ?>
                            <a href="message.php?action=compose&reply_to=<?php echo $message_item['id']; ?>" class="list-group-item list-group-item-action">
                                <i class="fas fa-reply me-2"></i>Reply
                            </a>
                        <?php endif; ?>
                        
                        <?php if (isset($message_item['related_product_id']) && !empty($message_item['related_product_id'])): ?>
                            <a href="../products/view.php?id=<?php echo $message_item['related_product_id']; ?>" class="list-group-item list-group-item-action">
                                <i class="fas fa-shopping-basket me-2"></i>View Product
                            </a>
                        <?php endif; ?>
                        
                        <?php 
                        $other_user_id = ($message_item['sender_id'] == $_SESSION['user_id']) ? $message_item['receiver_id'] : $message_item['sender_id'];
                        ?>
                        <a href="message.php?action=compose&to=<?php echo $other_user_id; ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-comment me-2"></i>New Conversation
                        </a>
                        
                        <a href="message.php?action=delete&id=<?php echo $message_item['id']; ?>" 
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
                        <h5 class="card-title mb-1"><?php echo htmlspecialchars($message_item['subject']); ?></h5>
                        <div class="text-muted small">
                            <span class="me-3">
                                <i class="far fa-calendar-alt me-1"></i><?php echo date('F j, Y', strtotime($message_item['date_sent'])); ?>
                            </span>
                            <span>
                                <i class="far fa-clock me-1"></i><?php echo date('g:i A', strtotime($message_item['date_sent'])); ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Message User Info -->
                        <div class="d-flex align-items-center mb-4">
                            <!-- User Image -->
                            <div class="me-3">
                                <?php 
                                $sender_image = !empty($message_item['sender_image']) ? $message_item['sender_image'] : '';
                                $sender_name = $message_item['sender_name'];
                                $sender_type = $message_item['sender_type'];
                                
                                if ($message_item['sender_id'] == $_SESSION['user_id']) {
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
                                <p class="mb-0"><strong><?php echo $label; ?></strong> <?php if ($message_item['sender_id'] != $_SESSION['user_id']) echo $sender_name; ?></p>
                                <?php if ($message_item['sender_id'] != $_SESSION['user_id']): ?>
                                    <p class="mb-0 text-muted small">
                                        <?php echo ucfirst($sender_type); ?>
                                        <?php if (isset($message_item['sender_location']) && !empty($message_item['sender_location'])): ?>
                                            <span class="mx-1">•</span> <?php echo $message_item['sender_location']; ?>
                                        <?php endif; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Recipient Info (for sent messages) -->
                            <?php if ($message_item['sender_id'] == $_SESSION['user_id']): ?>
                                <div class="ms-auto text-end">
                                    <p class="mb-0"><strong>To:</strong> <?php echo $message_item['receiver_name']; ?></p>
                                    <p class="mb-0 text-muted small">
                                        <?php echo ucfirst($message_item['receiver_type']); ?>
                                        <?php if (isset($message_item['receiver_location']) && !empty($message_item['receiver_location'])): ?>
                                            <span class="mx-1">•</span> <?php echo $message_item['receiver_location']; ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Message Content -->
                        <div class="message-content">
                            <?php echo $message_item['message']; ?>
                            
                            <?php if (isset($message_item['related_product_id']) && !empty($message_item['related_product_id'])): ?>
                                <div class="mt-4 pt-3 border-top">
                                    <p class="mb-2"><strong>Related Product:</strong></p>
                                    <div class="card">
                                        <div class="row g-0">
                                            <div class="col-md-3">
                                                <?php if (!empty($product_data['image'])): ?>
                                                    <img src="../public/uploads/products/<?php echo $product_data['image']; ?>" class="img-fluid rounded-start" alt="<?php echo $product_data['name']; ?>">
                                                <?php else: ?>
                                                    <div class="bg-light d-flex align-items-center justify-content-center h-100 p-3">
                                                        <i class="fas fa-image fa-3x text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="card-body py-2">
                                                    <h5 class="card-title"><?php echo $product_data['name'] ?? 'Product'; ?></h5>
                                                    <?php if (isset($product_data['description'])): ?>
                                                        <p class="card-text text-muted"><?php echo substr($product_data['description'], 0, 100) . (strlen($product_data['description']) > 100 ? '...' : ''); ?></p>
                                                    <?php endif; ?>
                                                    <p class="card-text">
                                                        <?php if (isset($product_data['price'])): ?>
                                                            <span class="text-muted small">ZMW <?php echo number_format($product_data['price'], 2); ?></span>
                                                            <span class="mx-2">|</span>
                                                        <?php endif; ?>
                                                        <?php if (isset($product_data['stock'])): ?>
                                                            <strong>Stock:</strong> <?php echo $product_data['stock']; ?> available
                                                        <?php endif; ?>
                                                    </p>
                                                    <a href="product.php?id=<?php echo $message->related_product_id; ?>" class="btn btn-sm btn-outline-primary">View Product</a>
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
                                <?php if ($message_item['sender_id'] != $_SESSION['user_id']): ?>
                                    <button type="button" class="btn btn-primary" onclick="window.location='message.php?action=compose&reply_to=<?php echo $message_item['id']; ?>'">
                                        <i class="fas fa-reply me-2"></i>Reply
                                    </button>
                                <?php endif; ?>
                                
                                <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                                    <i class="fas fa-chevron-left me-2"></i>Back
                                </button>
                            </div>
                            
                            <div>
                                <button type="button" class="btn btn-outline-danger" onclick="if(confirm('Are you sure you want to delete this message?')) window.location='message.php?action=delete&id=<?php echo $message_item['id']; ?>'">
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
                                                <h6 class="mb-1"><?php echo htmlspecialchars($message->product_name ?? 'Related Product'); ?></h6>
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
