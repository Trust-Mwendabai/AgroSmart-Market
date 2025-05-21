<?php
/**
 * Visual Filters Component
 * 
 * Provides an enhanced filtering interface with visual elements
 * to make it more accessible for users with limited technical experience
 */

// Get current filter values
$current_search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$current_category = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : '';
$current_location = isset($_GET['location']) ? htmlspecialchars($_GET['location']) : '';
$current_min_price = isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : '';
$current_max_price = isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : '';

// Determine the price range for the slider
$min_product_price = 0;
$max_product_price = 1000; // Default max price

// Try to get actual min/max prices from database if available
try {
    $price_sql = "SELECT MIN(price) as min_price, MAX(price) as max_price FROM products";
    $result = $conn->query($price_sql);
    if ($result && $row = $result->fetch_assoc()) {
        $min_product_price = floor($row['min_price']);
        $max_product_price = ceil($row['max_price']);
    }
} catch (Exception $e) {
    // Silently fail and use defaults
}
?>

<div class="filter-card">
    <div class="card-header">
        <h5><i class="fas fa-search"></i> <?php echo __('search', 'Search'); ?></h5>
    </div>
    <div class="card-body">
        <form action="marketplace.php" method="GET" id="searchForm">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="<?php echo __('search_products', 'Search products...'); ?>" 
                       name="search" value="<?php echo $current_search; ?>"
                       aria-label="<?php echo __('search', 'Search'); ?>">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            
            <!-- Hidden fields to preserve other filters -->
            <?php if (!empty($current_category)): ?>
            <input type="hidden" name="category" value="<?php echo $current_category; ?>">
            <?php endif; ?>
            
            <?php if (!empty($current_location)): ?>
            <input type="hidden" name="location" value="<?php echo $current_location; ?>">
            <?php endif; ?>
            
            <?php if (!empty($current_min_price)): ?>
            <input type="hidden" name="min_price" value="<?php echo $current_min_price; ?>">
            <?php endif; ?>
            
            <?php if (!empty($current_max_price)): ?>
            <input type="hidden" name="max_price" value="<?php echo $current_max_price; ?>">
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Visual Categories Filter -->
<div class="filter-card">
    <div class="card-header">
        <h5><i class="fas fa-th-large"></i> <?php echo __('categories', 'Categories'); ?></h5>
    </div>
    <div class="card-body">
        <?php include 'visual-categories.php'; ?>
    </div>
</div>

<!-- Location Filter with Visual Map -->
<div class="filter-card">
    <div class="card-header">
        <h5><i class="fas fa-map-marker-alt"></i> <?php echo __('location', 'Location'); ?></h5>
    </div>
    <div class="card-body">
        <form action="marketplace.php" method="GET" id="locationForm">
            <div class="input-group mb-3">
                <span class="input-group-text">
                    <i class="fas fa-map-marker-alt"></i>
                </span>
                <input type="text" class="form-control" placeholder="<?php echo __('enter_location', 'Enter location...'); ?>" 
                       name="location" value="<?php echo $current_location; ?>"
                       aria-label="<?php echo __('location', 'Location'); ?>">
            </div>
            
            <!-- Top Locations Quick Selection -->
            <div class="d-flex flex-wrap gap-2 mt-2">
                <?php
                $popular_locations = ['Lusaka', 'Ndola', 'Kitwe', 'Livingstone', 'Chipata'];
                foreach ($popular_locations as $location):
                    $active = ($current_location == $location) ? 'active' : '';
                ?>
                <a href="javascript:void(0);" onclick="selectLocation('<?php echo $location; ?>')" 
                   class="badge rounded-pill bg-light text-dark p-2 <?php echo $active; ?>">
                    <i class="fas fa-map-marker-alt me-1"></i> <?php echo $location; ?>
                </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Hidden fields to preserve other filters -->
            <?php if (!empty($current_search)): ?>
            <input type="hidden" name="search" value="<?php echo $current_search; ?>">
            <?php endif; ?>
            
            <?php if (!empty($current_category)): ?>
            <input type="hidden" name="category" value="<?php echo $current_category; ?>">
            <?php endif; ?>
            
            <?php if (!empty($current_min_price)): ?>
            <input type="hidden" name="min_price" value="<?php echo $current_min_price; ?>">
            <?php endif; ?>
            
            <?php if (!empty($current_max_price)): ?>
            <input type="hidden" name="max_price" value="<?php echo $current_max_price; ?>">
            <?php endif; ?>
        </form>
        
        <script>
            function selectLocation(location) {
                document.querySelector('#locationForm input[name="location"]').value = location;
                document.getElementById('locationForm').submit();
            }
        </script>
    </div>
</div>

<!-- Price Range Filter with Visual Slider -->
<div class="filter-card">
    <div class="card-header">
        <h5><i class="fas fa-money-bill"></i> <?php echo __('price_range', 'Price Range'); ?></h5>
    </div>
    <div class="card-body">
        <form action="marketplace.php" method="GET" id="priceForm">
            <div class="price-slider-container">
                <input type="range" class="form-range" min="<?php echo $min_product_price; ?>" 
                       max="<?php echo $max_product_price; ?>" step="10" id="priceSlider">
                
                <div class="price-range-values mt-3">
                    <div class="row">
                        <div class="col-6">
                            <label for="min_price"><?php echo __('min_price', 'Min Price'); ?></label>
                            <div class="input-group">
                                <span class="input-group-text">K</span>
                                <input type="number" class="form-control" id="min_price" name="min_price" 
                                       value="<?php echo empty($current_min_price) ? $min_product_price : $current_min_price; ?>"
                                       min="<?php echo $min_product_price; ?>" max="<?php echo $max_product_price; ?>">
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="max_price"><?php echo __('max_price', 'Max Price'); ?></label>
                            <div class="input-group">
                                <span class="input-group-text">K</span>
                                <input type="number" class="form-control" id="max_price" name="max_price" 
                                       value="<?php echo empty($current_max_price) ? $max_product_price : $current_max_price; ?>"
                                       min="<?php echo $min_product_price; ?>" max="<?php echo $max_product_price; ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hidden fields to preserve other filters -->
            <?php if (!empty($current_search)): ?>
            <input type="hidden" name="search" value="<?php echo $current_search; ?>">
            <?php endif; ?>
            
            <?php if (!empty($current_category)): ?>
            <input type="hidden" name="category" value="<?php echo $current_category; ?>">
            <?php endif; ?>
            
            <?php if (!empty($current_location)): ?>
            <input type="hidden" name="location" value="<?php echo $current_location; ?>">
            <?php endif; ?>
            
            <div class="mt-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-2"></i><?php echo __('apply_filters', 'Apply Filters'); ?>
                </button>
            </div>
        </form>
        
        <div class="mt-2">
            <a href="marketplace.php" class="btn btn-outline-secondary w-100">
                <i class="fas fa-times me-2"></i><?php echo __('clear_filters', 'Clear Filters'); ?>
            </a>
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const priceSlider = document.getElementById('priceSlider');
                const minPrice = document.getElementById('min_price');
                const maxPrice = document.getElementById('max_price');
                
                if (priceSlider && minPrice && maxPrice) {
                    // Initialize slider value based on current max price
                    priceSlider.value = maxPrice.value;
                    
                    // Update max price when slider changes
                    priceSlider.addEventListener('input', function() {
                        maxPrice.value = this.value;
                    });
                    
                    // Update slider when max price changes
                    maxPrice.addEventListener('input', function() {
                        priceSlider.value = this.value;
                    });
                    
                    // Ensure min price is less than max price
                    minPrice.addEventListener('change', function() {
                        if (parseInt(this.value) > parseInt(maxPrice.value)) {
                            this.value = maxPrice.value;
                        }
                    });
                    
                    // Ensure max price is greater than min price
                    maxPrice.addEventListener('change', function() {
                        if (parseInt(this.value) < parseInt(minPrice.value)) {
                            this.value = minPrice.value;
                        }
                    });
                }
            });
        </script>
    </div>
</div>

<!-- Filter Actions (Mobile-Only) -->
<div class="filter-card d-md-none">
    <div class="card-body">
        <button type="button" class="btn btn-primary w-100 mb-2" onclick="document.getElementById('priceForm').submit();">
            <i class="fas fa-filter me-2"></i><?php echo __('apply_filters', 'Apply Filters'); ?>
        </button>
        <a href="marketplace.php" class="btn btn-outline-secondary w-100">
            <i class="fas fa-times me-2"></i><?php echo __('clear_filters', 'Clear Filters'); ?>
        </a>
    </div>
</div>
