    </main>
    <!-- End Main Content -->
    
    <!-- Footer -->
    <footer class="py-4 mt-4 bg-white border-top">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4 mb-3 mb-md-0">
                    <img src="public/img/logo.png" alt="AgroSmart Market" class="footer-logo" style="max-height: 40px;">
                    <p class="text-muted small mt-2 mb-0">
                        Connecting farmers and buyers in Zambia
                    </p>
                </div>
                
                <div class="col-md-4 mb-3 mb-md-0 text-center">
                    <div class="d-flex justify-content-center align-items-center">
                        <!-- Accessibility Controls -->
                        <div class="form-check form-switch me-3">
                            <input class="form-check-input" type="checkbox" id="contrast-toggle" 
                                <?php echo isset($_COOKIE['high_contrast']) && $_COOKIE['high_contrast'] === 'true' ? 'checked' : ''; ?>>
                            <label class="form-check-label small" for="contrast-toggle">
                                <i class="fas fa-adjust me-1"></i> 
                                <span class="d-none d-sm-inline"><?php echo __('high_contrast', 'High Contrast'); ?></span>
                            </label>
                        </div>
                        
                        <div class="footer-divider mx-2"></div>
                        
                        <!-- Offline Mode Indicator -->
                        <div class="d-flex align-items-center">
                            <span id="connection-status-footer" class="connection-status online">
                                <i class="fas fa-wifi me-1"></i> 
                                <span class="d-none d-sm-inline">Online</span>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 text-md-end text-center">
                    <!-- Language Selector -->
                    <div class="language-toggle">
                        <div class="language-option <?php echo !isset($_SESSION['language']) || $_SESSION['language'] == 'en' ? 'active' : ''; ?>" data-lang="en">
                            <img src="public/img/flags/gb.png" alt="English" title="English">
                        </div>
                        <div class="language-option <?php echo isset($_SESSION['language']) && $_SESSION['language'] == 'bem' ? 'active' : ''; ?>" data-lang="bem">
                            <img src="public/img/flags/zm.png" alt="Bemba" title="Bemba">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-12 text-center">
                    <p class="text-muted small mb-0">
                        &copy; <?php echo date('Y'); ?> AgroSmart Market. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Hidden form for language switching -->
        <form id="language-form" action="change-language.php" method="POST" style="display: none;">
            <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">
            <input type="hidden" name="language" id="selected-language">
            <input type="hidden" name="redirect" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
        </form>
    </footer>
    
    <!-- Core JavaScript -->
    <script src="public/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/accessibility.js"></script>
    
    <!-- Service Worker Registration -->
    <script>
        // Register service worker for offline support
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    })
                    .catch(function(error) {
                        console.log('ServiceWorker registration failed: ', error);
                    });
            });
        }
    </script>
</body>
</html>
