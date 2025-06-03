<?php
/**
 * Error View
 * 
 * Displays error messages to the user
 */

// Set the page title
$page_title = 'Error - AgroSmart Market';

// Include header
include_once 'partials/header.php';
?>

<main>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card border-danger mb-4">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Error</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger">
                            <?php echo isset($error) ? htmlspecialchars($error) : 'An unexpected error occurred. Please try again or contact support.'; ?>
                        </div>
                        <div class="text-center mt-4">
                            <a href="index.php" class="btn btn-primary me-2">
                                <i class="fas fa-home me-1"></i> Go to Homepage
                            </a>
                            <a href="javascript:history.back()" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Go Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
// Include footer
include_once 'partials/footer.php';
?>
