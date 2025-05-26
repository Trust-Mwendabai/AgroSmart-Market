/**
 * Mobile Dashboard Optimizations
 * Enhances mobile experience for the AgroSmart Market dashboard
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize mobile optimizations
    initMobileDashboard();
    
    // Add pull-to-refresh capability on mobile
    setupPullToRefresh();
    
    // Setup touch interactions for dashboard cards
    setupTouchInteractions();
    
    // Add mobile quick action buttons
    addMobileQuickActions();
});

/**
 * Initialize mobile dashboard optimizations
 */
function initMobileDashboard() {
    // Add mobile-specific classes to elements
    if (window.innerWidth < 768) {
        // Add table-mobile-optimized class to tables
        document.querySelectorAll('.dashboard-card .table').forEach(table => {
            table.classList.add('table-mobile-optimized');
        });
        
        // Make favorite products display as a grid on mobile
        const quickOrderContainer = document.querySelector('.quick-order-favorites');
        if (quickOrderContainer) {
            quickOrderContainer.classList.add('favorites-grid');
        }
        
        // Add mobile classes to status badges
        document.querySelectorAll('.badge').forEach(badge => {
            badge.classList.add('status-badge');
        });
    }
    
    // Add placeholder image loading for product images
    document.querySelectorAll('.product-thumb img').forEach(img => {
        if (!img.parentElement.querySelector('.image-placeholder')) {
            const placeholder = document.createElement('div');
            placeholder.className = 'image-placeholder';
            placeholder.innerHTML = '<i class="fas fa-image"></i>';
            img.parentElement.insertBefore(placeholder, img);
        }
        
        // Set up lazy loading for product images
        const originalSrc = img.getAttribute('src');
        if (originalSrc && !originalSrc.includes('default-product.jpg')) {
            img.setAttribute('data-src', originalSrc);
            img.removeAttribute('src');
            img.classList.add('progressive-image');
        }
    });
}

/**
 * Setup pull-to-refresh functionality for mobile
 */
function setupPullToRefresh() {
    // Only enable on mobile devices
    if (window.innerWidth >= 768) return;
    
    // Create refresh indicator
    const refreshIndicator = document.createElement('div');
    refreshIndicator.className = 'dashboard-refresh-indicator';
    refreshIndicator.innerHTML = '<i class="fas fa-sync-alt fa-spin me-2"></i> Refreshing...';
    
    // Add to the top of the dashboard container
    const dashboardContainer = document.querySelector('.dashboard-container');
    if (dashboardContainer) {
        dashboardContainer.insertBefore(refreshIndicator, dashboardContainer.firstChild);
    }
    
    // Variables to track touch position
    let startY = 0;
    let currentY = 0;
    let refreshTriggered = false;
    
    // Add touch start event
    document.addEventListener('touchstart', function(e) {
        // Only trigger if at the top of the page
        if (window.scrollY === 0) {
            startY = e.touches[0].clientY;
        }
    }, { passive: true });
    
    // Add touch move event
    document.addEventListener('touchmove', function(e) {
        if (startY > 0) {
            currentY = e.touches[0].clientY;
            const pullDistance = currentY - startY;
            
            // If pulling down
            if (pullDistance > 0 && pullDistance < 100) {
                refreshIndicator.style.opacity = pullDistance / 100;
            }
            
            // If pulled enough to trigger refresh
            if (pullDistance > 70 && !refreshTriggered) {
                refreshTriggered = true;
                refreshIndicator.style.opacity = 1;
                refreshIndicator.innerHTML = '<i class="fas fa-sync-alt fa-spin me-2"></i> Releasing will refresh...';
            }
        }
    }, { passive: true });
    
    // Add touch end event
    document.addEventListener('touchend', function() {
        if (refreshTriggered) {
            // Show fully while refreshing
            refreshIndicator.style.opacity = 1;
            refreshIndicator.innerHTML = '<i class="fas fa-sync-alt fa-spin me-2"></i> Refreshing...';
            
            // Reload the page after a short delay
            setTimeout(function() {
                window.location.reload();
            }, 1000);
        } else {
            // Reset if not triggered
            refreshIndicator.style.opacity = 0;
        }
        
        // Reset variables
        startY = 0;
        currentY = 0;
        refreshTriggered = false;
    }, { passive: true });
}

/**
 * Setup touch interactions for dashboard cards
 */
function setupTouchInteractions() {
    // Add touch feedback to dashboard cards
    document.querySelectorAll('.dashboard-card').forEach(card => {
        // Add touch start feedback
        card.addEventListener('touchstart', function() {
            this.classList.add('touch-active');
        }, { passive: true });
        
        // Remove feedback on touch end
        card.addEventListener('touchend', function() {
            this.classList.remove('touch-active');
        }, { passive: true });
        
        // Remove feedback if touch moves away
        card.addEventListener('touchmove', function() {
            this.classList.remove('touch-active');
        }, { passive: true });
    });
    
    // Improve table rows on mobile
    if (window.innerWidth < 768) {
        document.querySelectorAll('.dashboard-card table tr').forEach(row => {
            row.style.cursor = 'pointer';
            
            // Add touch start feedback
            row.addEventListener('touchstart', function() {
                this.style.backgroundColor = 'rgba(0,0,0,0.05)';
            }, { passive: true });
            
            // Remove feedback on touch end
            row.addEventListener('touchend', function() {
                this.style.backgroundColor = '';
            }, { passive: true });
            
            // Remove feedback if touch moves away
            row.addEventListener('touchmove', function() {
                this.style.backgroundColor = '';
            }, { passive: true });
        });
    }
}

/**
 * Add mobile quick action buttons
 */
function addMobileQuickActions() {
    // Only add on mobile
    if (window.innerWidth >= 768) return;
    
    // Create container for quick actions
    const quickActions = document.createElement('div');
    quickActions.className = 'mobile-quick-actions';
    
    // Create marketplace button
    const marketplaceBtn = document.createElement('a');
    marketplaceBtn.href = 'marketplace.php';
    marketplaceBtn.className = 'mobile-action-button';
    marketplaceBtn.innerHTML = '<i class="fas fa-shopping-basket"></i>';
    marketplaceBtn.setAttribute('aria-label', 'Go to Marketplace');
    
    // Add button to container
    quickActions.appendChild(marketplaceBtn);
    
    // Add to body
    document.body.appendChild(quickActions);
    
    // Add button press animation
    marketplaceBtn.addEventListener('touchstart', function() {
        this.style.transform = 'scale(0.95)';
    }, { passive: true });
    
    marketplaceBtn.addEventListener('touchend', function() {
        this.style.transform = 'scale(1)';
    }, { passive: true });
}

/**
 * Fix table display on mobile
 */
function optimizeTablesForMobile() {
    if (window.innerWidth < 768) {
        document.querySelectorAll('.dashboard-card .table').forEach(table => {
            // Add mobile-specific styling
            table.classList.add('table-sm');
            
            // Find the status column (usually the last column)
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length > 0) {
                    // Add status indicator dot
                    const lastCell = cells[cells.length - 1];
                    if (lastCell.querySelector('.badge')) {
                        const statusBadge = lastCell.querySelector('.badge');
                        const statusColor = window.getComputedStyle(statusBadge).backgroundColor;
                        
                        // Add status indicator to first cell
                        const firstCell = cells[0];
                        const indicator = document.createElement('span');
                        indicator.className = 'status-indicator';
                        indicator.style.backgroundColor = statusColor;
                        indicator.style.width = '8px';
                        indicator.style.height = '8px';
                        indicator.style.borderRadius = '50%';
                        indicator.style.display = 'inline-block';
                        indicator.style.marginRight = '5px';
                        
                        firstCell.insertBefore(indicator, firstCell.firstChild);
                    }
                }
            });
        });
    }
}
