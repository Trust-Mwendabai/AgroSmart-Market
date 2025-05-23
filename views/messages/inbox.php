<?php
// Check if this file has already been included to prevent duplication
if (defined('MESSAGE_INBOX_INCLUDED')) {
    return;
}
define('MESSAGE_INBOX_INCLUDED', true);

// Set the page title
$page_title = 'Inbox - AgroSmart Market';

// Include header
include_once dirname(__DIR__) . '/../views/partials/header.php';
?>

<style>
    .message-card {
        transition: all 0.2s;
        border-left: 3px solid transparent;
    }
    
    .message-card:hover {
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .message-card.unread {
        background-color: rgba(76, 175, 80, 0.05);
        border-left: 3px solid var(--primary-color);
    }
    
    .sender-img {
        width: 40px;
        height: 40px;
        object-fit: cover;
    }
</style>

    <!-- Main Content -->
    <div class="container py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Message Inbox</h2>
            <a href="../message.php?action=compose" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>New Message
            </a>
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
                    <a href="../message.php" class="list-group-item list-group-item-action active">
                        <i class="fas fa-inbox me-2"></i>Inbox
                        <?php 
                        $unread_count = 0;
                        foreach ($messages as $msg) {
                            if (!$msg['is_read']) {
                                $unread_count++;
                            }
                        }
                        
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
            </div>
            
            <div class="col-md-9">
                <!-- Messages List -->
                <div class="card">
                    <div class="card-header bg-white">
                        <div class="row align-items-center">
                            <div class="col-6">
                                <h5 class="mb-0">Inbox</h5>
                            </div>
                            <div class="col-6 text-end">
                                <form class="d-inline-flex" action="message.php" method="GET">
                                    <input type="text" class="form-control form-control-sm me-2" name="search" placeholder="Search messages..." 
                                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($messages)): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($messages as $message): ?>
                                    <a href="message.php?action=view&id=<?php echo $message['id']; ?>" class="list-group-item list-group-item-action message-card <?php echo !$message['is_read'] ? 'unread' : ''; ?>">
                                        <div class="d-flex align-items-center">
                                            <!-- Sender Image -->
                                            <div class="me-3">
                                                <?php if (!empty($message['sender_image'])): ?>
                                                    <img src="../public/uploads/<?php echo $message['sender_image']; ?>" class="rounded-circle sender-img" alt="Sender">
                                                <?php else: ?>
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center sender-img">
                                                        <?php echo strtoupper(substr($message['sender_name'], 0, 1)); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Message Content -->
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="mb-1 <?php echo !$message['is_read'] ? 'fw-bold' : ''; ?>">
                                                        <?php echo $message['sender_name']; ?>
                                                        <?php if ($message['sender_type'] === 'farmer'): ?>
                                                            <span class="badge bg-success">Farmer</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-info">Buyer</span>
                                                        <?php endif; ?>
                                                    </h6>
                                                    <small class="text-muted"><?php echo date('M d, g:i a', strtotime($message['date_sent'])); ?></small>
                                                </div>
                                                <p class="mb-1 <?php echo !$message['is_read'] ? 'fw-bold' : ''; ?>"><?php echo $message['subject']; ?></p>
                                                <small class="text-muted"><?php echo substr(strip_tags($message['message']), 0, 100) . (strlen($message['message']) > 100 ? '...' : ''); ?></small>
                                                
                                                <?php if (!$message['is_read']): ?>
                                                    <span class="badge bg-primary float-end">New</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                <h4>Your Inbox is Empty</h4>
                                <p class="text-muted">You haven't received any messages yet.</p>
                                <a href="message.php?action=compose" class="btn btn-primary mt-2">Send a Message</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($messages) && count($messages) > 10): ?>
                        <div class="card-footer bg-white">
                            <nav>
                                <ul class="pagination justify-content-center mb-0">
                                    <li class="page-item disabled">
                                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                                    </li>
                                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">Next</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                </div>
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
