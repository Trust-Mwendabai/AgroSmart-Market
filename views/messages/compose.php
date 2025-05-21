<?php
// Set the page title
$page_title = 'Compose Message - AgroSmart Market';

// Include header
include_once dirname(__DIR__) . '/../includes/header.php';
?>

<!-- Summernote text editor CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">

<style>
        
    </style>

    <!-- Main Content -->
    <div class="container py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Compose New Message</h2>
            <a href="message.php" class="btn btn-outline-secondary">
                <i class="fas fa-chevron-left me-2"></i>Back to Inbox
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
                    <a href="message.php?action=compose" class="list-group-item list-group-item-action active">
                        <i class="fas fa-pen me-2"></i>Compose
                    </a>
                </div>
            </div>
            
            <div class="col-md-9">
                <!-- Compose Form -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">New Message</h5>
                    </div>
                    <div class="card-body">
                        <form action="message.php?action=send" method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            
                            <!-- Pre-selected recipient for product inquiries -->
                            <?php if (isset($_GET['to']) && isset($_GET['product_id'])): ?>
                                <input type="hidden" name="receiver_id" value="<?php echo htmlspecialchars($_GET['to']); ?>">
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($_GET['product_id']); ?>">
                                
                                <div class="mb-3">
                                    <label for="receiver" class="form-label">To:</label>
                                    <input type="text" class="form-control" id="receiver" value="<?php echo htmlspecialchars($receiver_name); ?>" disabled>
                                    <div class="form-text">Sending to product owner.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="product" class="form-label">Regarding Product:</label>
                                    <input type="text" class="form-control" id="product" value="<?php echo htmlspecialchars($product_name); ?>" disabled>
                                </div>
                            <?php else: ?>
                                <div class="mb-3">
                                    <label for="receiver_id" class="form-label">To:</label>
                                    <select class="form-select" id="receiver_id" name="receiver_id" required>
                                        <option value="">-- Select Recipient --</option>
                                        <?php if (isset($users) && !empty($users)): ?>
                                            <optgroup label="Farmers">
                                                <?php foreach ($users as $user): ?>
                                                    <?php if ($user['user_type'] === 'farmer' && $user['id'] != $_SESSION['user_id']): ?>
                                                        <option value="<?php echo $user['id']; ?>"><?php echo $user['name']; ?></option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </optgroup>
                                            <optgroup label="Buyers">
                                                <?php foreach ($users as $user): ?>
                                                    <?php if ($user['user_type'] === 'buyer' && $user['id'] != $_SESSION['user_id']): ?>
                                                        <option value="<?php echo $user['id']; ?>"><?php echo $user['name']; ?></option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject:</label>
                                <input type="text" class="form-control" id="subject" name="subject" required
                                       value="<?php echo isset($product_name) ? 'Inquiry about ' . htmlspecialchars($product_name) : ''; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="message" class="form-label">Message:</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required><?php 
                                    if (isset($product_name)) {
                                        echo "Hi, I'm interested in your product \"" . htmlspecialchars($product_name) . "\". ";
                                        if (isset($product_price)) {
                                            echo "I see it's listed for " . htmlspecialchars($product_price) . ". ";
                                        }
                                        echo "Please let me know if it's still available and if you would consider...\n\nThank you.";
                                    }
                                ?></textarea>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i>Send Message
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="window.location='message.php'">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </button>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="save-draft">
                                        <i class="fas fa-save me-1"></i>Save Draft
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
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
    
    <!-- Bootstrap & jQuery JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize Summernote editor
            $('#message').summernote({
                placeholder: 'Type your message here...',
                tabsize: 2,
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']]
                ]
            });
            
            // Save draft functionality
            $('#save-draft').click(function() {
                // Get form data
                const receiver_id = $('#receiver_id').val();
                const subject = $('#subject').val();
                const message = $('#message').summernote('code');
                
                // Save to localStorage
                const draft = {
                    receiver_id: receiver_id,
                    subject: subject,
                    message: message,
                    timestamp: new Date().toISOString()
                };
                
                localStorage.setItem('message_draft', JSON.stringify(draft));
                
                // Show feedback
                alert('Draft saved successfully!');
            });
            
            // Check for saved drafts on page load
            const savedDraft = localStorage.getItem('message_draft');
            if (savedDraft && !$('#subject').val()) {
                const draft = JSON.parse(savedDraft);
                
                // Ask if user wants to load the draft
                if (confirm('You have a saved draft from ' + new Date(draft.timestamp).toLocaleString() + '. Would you like to load it?')) {
                    if (draft.receiver_id) $('#receiver_id').val(draft.receiver_id);
                    if (draft.subject) $('#subject').val(draft.subject);
                    if (draft.message) $('#message').summernote('code', draft.message);
                }
            }
        });
    </script>
</body>
</html>
