<?php
/**
 * Visual Product Card Component
 * 
 * This component displays a product with visual indicators for:
 * - Stock status (using color coding)
 * - Organic status (with badge)
 * - Farmer information
 * - Simple action buttons with icons
 */

// Default image if product image is not available
$default_image = 'public/img/default-product.jpg';

// Stock status indicators
$stock_status = 'out-of-stock';
$stock_label = __('out_of_stock', 'Out of stock');

if ($product['stock'] > 10) {
    $stock_status = 'in-stock';
    $stock_label = __('in_stock', 'In stock');
} elseif ($product['stock'] > 0) {
    $stock_status = 'low-stock';
    $stock_label = __('low_stock', 'Low stock');
}

// Product image
$product_image = !empty($product['image']) 
    ? 'public/uploads/products/' . $product['image'] 
    : $default_image;

// Get farmer information if available
$farmer_name = '';

if (isset($product['farmer_id']) && !empty($product['farmer_id'])) {
    // Query to get farmer information
    $farmer_sql = "SELECT name FROM users WHERE id = ?";
    $stmt = $conn->prepare($farmer_sql);
    $stmt->bind_param("i", $product['farmer_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $farmer_name = $row['name'];
    }
}
?>

<div class="product-card">
    <!-- Product Image Section -->
    <div class="product-image">
        <img src="<?php echo htmlspecialchars($product_image); ?>" 
             alt="<?php echo htmlspecialchars($product['name']); ?>"
             onerror="this.src='<?php echo $default_image; ?>'">
             
        <!-- Stock Status Indicator -->
        <div class="product-stock-status <?php echo $stock_status; ?>" 
             title="<?php echo $stock_label; ?>"
             data-tooltip="<?php echo $stock_label; ?>"></div>
             
        <!-- Organic Badge (if applicable) -->
        <?php if (isset($product['is_organic']) && $product['is_organic']): ?>
        <div class="product-badge badge-organic">
            <i class="fas fa-leaf me-1"></i> <?php echo __('organic', 'Organic'); ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Product Info Section -->
    <div class="product-info">
        <!-- Farmer Information (if available) -->
        <?php if (!empty($farmer_name)): ?>
        <div class="product-farmer">
            <div class="farmer-avatar-placeholder">
                <?php echo strtoupper(substr($farmer_name, 0, 1)); ?>
            </div>
            <div class="farmer-name"><?php echo htmlspecialchars($farmer_name); ?></div>
        </div>
        <?php endif; ?>
        
        <!-- Product Title -->
        <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
        
        <!-- Product Price -->
        <div class="product-price">
            K<?php echo number_format($product['price'], 2); ?>
        </div>
        
        <!-- Product Unit -->
        <div class="product-unit">
            <?php echo __('per_unit', 'per'); ?> <?php echo htmlspecialchars($product['unit'] ?? 'kg'); ?>
        </div>
        
        <!-- Product Actions -->
        <div class="product-actions">
            <?php if (isset($product['stock']) && $product['stock'] > 0): ?>
            <button type="button" class="btn-add-cart" 
                    onclick="addToCart(<?php echo $product['id']; ?>)" 
                    <?php echo (isset($product['stock']) && $product['stock'] <= 0) ? 'disabled' : ''; ?>>
                <i class="fas fa-cart-plus"></i> <?php echo __('add_to_cart', 'Add to cart'); ?>
            </button>
            <?php else: ?>
            <button type="button" class="btn-add-cart" disabled>
                <i class="fas fa-times"></i> <?php echo __('out_of_stock', 'Out of stock'); ?>
            </button>
            <?php endif; ?>
            
            <a href="product.php?action=view&id=<?php echo $product['id']; ?>" class="btn-view" 
               title="<?php echo __('view_details', 'View'); ?>"
               aria-label="<?php echo __('view_details', 'View'); ?> <?php echo htmlspecialchars($product['name']); ?>">
                <i class="fas fa-eye"></i>
            </a>
        </div>
    </div>
</div>
