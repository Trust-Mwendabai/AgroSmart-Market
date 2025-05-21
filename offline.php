<?php
// Set page title
$page_title = 'You are offline';
$is_offline_page = true;

// Include header if available
if (file_exists('views/partials/header-lite.php')) {
    include 'views/partials/header-lite.php';
} else {
    // Fallback minimal header if the standard header isn't cached
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline - AgroSmart Market</title>
    <link rel="stylesheet" href="public/css/bootstrap.min.css">
    <link rel="stylesheet" href="public/css/enhanced-dashboard.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .offline-container {
            max-width: 600px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .offline-icon {
            font-size: 60px;
            color: #6c757d;
            margin-bottom: 20px;
        }
        .offline-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .offline-message {
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 25px;
        }
        .offline-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        @media (min-width: 576px) {
            .offline-actions {
                flex-direction: row;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
<?php } ?>

<div class="container">
    <div class="offline-container">
        <div class="offline-icon">
            <img src="public/img/offline-banner.svg" alt="Offline" style="max-width: 200px;">
        </div>
        <h1 class="offline-title">You are currently offline</h1>
        <p class="offline-message">
            We can't connect to the internet right now. Don't worry - you can still access some features and
            any changes you make will be saved and synced when you're back online.
        </p>
        
        <div class="offline-actions">
            <button class="btn btn-primary" onclick="goBack()">
                <i class="fas fa-arrow-left me-2"></i>Go Back
            </button>
            <button class="btn btn-outline-secondary" onclick="tryReconnect()">
                <i class="fas fa-sync me-2"></i>Try Again
            </button>
        </div>
        
        <div class="mt-4">
            <h5>Available Offline:</h5>
            <div class="d-flex flex-wrap justify-content-center gap-3 mt-3">
                <a href="index.php" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-home me-1"></i> Home
                </a>
                <a href="marketplace.php" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-store me-1"></i> Marketplace
                </a>
                <a href="buyer-dashboard.php" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                </a>
                <a href="cart.php" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-shopping-cart me-1"></i> Cart
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function goBack() {
    window.history.back();
}

function tryReconnect() {
    window.location.reload();
}

// Check connection status periodically
setInterval(function() {
    if (navigator.onLine) {
        document.querySelector('.offline-message').innerHTML = 
            'You\'re back online! <span class="text-success">Reconnected</span>';
    }
}, 3000);
</script>

<?php
// Include footer if available
if (file_exists('views/partials/footer-lite.php')) {
    include 'views/partials/footer-lite.php';
} else {
    // Fallback minimal footer
?>
<script src="public/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php } ?>
