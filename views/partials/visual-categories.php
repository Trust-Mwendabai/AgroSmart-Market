<?php
/**
 * Visual Categories Component
 * 
 * Displays product categories with visual icons to make it easier for 
 * users with limited technical experience to navigate
 */

// Map of categories to icons
$category_icons = [
    'Vegetables' => 'fa-carrot',
    'Fruits' => 'fa-apple-alt',
    'Grains' => 'fa-wheat',
    'Dairy' => 'fa-cheese',
    'Eggs' => 'fa-egg',
    'Meat' => 'fa-drumstick-bite',
    'Poultry' => 'fa-feather',
    'Fish' => 'fa-fish',
    'Nuts' => 'fa-seedling',
    'Honey' => 'fa-jar',
    'Tea' => 'fa-mug-hot',
    'Coffee' => 'fa-coffee',
    'Herbs' => 'fa-leaf',
    'Spices' => 'fa-mortar-pestle',
    'Other' => 'fa-shopping-basket'
];

// Get current selected category
$current_category = isset($_GET['category']) ? $_GET['category'] : '';
?>

<div class="category-buttons">
    <?php foreach ($categories as $category): 
        // Determine the icon to use
        $icon = isset($category_icons[$category]) ? $category_icons[$category] : 'fa-shopping-basket';
        
        // Check if this category is currently active
        $is_active = ($current_category == $category) ? 'active' : '';
    ?>
    <a href="?category=<?php echo urlencode($category); ?>" class="category-button <?php echo $is_active; ?>" 
       aria-label="<?php echo htmlspecialchars($category); ?> <?php echo __('products', 'products'); ?>">
        <span class="category-icon">
            <i class="fas <?php echo $icon; ?>"></i>
        </span>
        <span class="category-name"><?php echo htmlspecialchars($category); ?></span>
    </a>
    <?php endforeach; ?>
</div>
