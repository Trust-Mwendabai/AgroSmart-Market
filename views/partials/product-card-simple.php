<?php
/**
 * Simple Product Card Component
 * 
 * A simplified version of the product card for dashboard sections
 * Designed for better mobile experience and visual clarity
 */

// Default image if product image is not available
$default_image = 'public/img/default-product.jpg';

// Product image with fallback
$product_image = !empty($product['image']) 
    ? 'public/uploads/products/' . $product['image'] 
    : $default_image;

// Stock status indicators
$stock_status = 'out-of-stock';
$stock_label = __('out_of_stock', 'Out of stock');

if (isset($product['stock'])) {
    if ($product['stock'] > 10) {
        $stock_status = 'in-stock';
        $stock_label = __('in_stock', 'In stock');
    } elseif ($product['stock'] > 0) {
        $stock_status = 'low-stock';
        $stock_label = __('low_stock', 'Low stock');
    }
}

// Check if price exists
$price = isset($product['price']) ? $product['price'] : 0;
$unit = isset($product['unit']) ? $product['unit'] : 'kg';
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
        <!-- Product Title -->
        <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
        
        <!-- Product Price -->
        <div class="product-price">
            K<?php echo number_format($price, 2); ?>
        </div>
        
        <!-- Product Unit -->
        <div class="product-unit">
            <?php echo __('per_unit', 'per'); ?> <?php echo htmlspecialchars($unit); ?>
        </div>
        
        <!-- Product Actions -->
        <div class="product-actions">
            <?php if (!isset($product['stock']) || $product['stock'] > 0): ?>
            <button type="button" class="btn-add-cart" 
                    onclick="addToCart(<?php echo $product['id']; ?>)" 
                    data-product-id="<?php echo $product['id']; ?>">
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
