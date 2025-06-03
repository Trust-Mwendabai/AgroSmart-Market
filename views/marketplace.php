<!-- Marketplace View Template -->
<!-- Note: The variables $products, $categories, $page, etc. are passed in from the main marketplace.php file -->

<!-- Marketplace Header -->
<div class="bg-gradient-primary text-white py-5 mb-4">
    <div class="container">
        <?php if (isset($_GET['cart_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2 fa-lg"></i>
                <div>
                    <strong>Success!</strong> Product added to your cart.
                    <a href="cart.php" class="alert-link ms-2">View Cart <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge bg-white text-primary mb-2">Fresh Local Produce</span>
                <h1 class="display-4 fw-bold mb-2 text-white">Farm to Table Marketplace</h1>
                <p class="lead mb-4 text-white-75">Connect directly with local farmers for fresh, quality produce</p>
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white-75">Home</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Marketplace</li>
                    </ol>
                </nav>
            </div>
            <div class="col-lg-5">
                <div class="d-flex flex-wrap justify-content-lg-end gap-2 mt-3 mt-lg-0">
                    <a href="#products-section" class="btn btn-light btn-lg">
                        <i class="fas fa-shopping-basket me-2"></i>Browse Products
                    </a>
                    <a href="#filter-section" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-filter me-2"></i>Filter Options
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Base Styles */
:root {
    --primary-color: #28a745;
    --primary-hover: #218838;
    --secondary-color: #6c757d;
    --light-gray: #f8f9fa;
    --border-color: #e9ecef;
    --box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.03);
    --transition: all 0.3s ease-in-out;
}

/* Header and General Styling */
.bg-gradient-primary {
    background: linear-gradient(135deg, #1e7e34 0%, #28a745 100%);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

/* Cards and Containers */
.card {
    border: none;
    transition: var(--transition);
    overflow: hidden;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.075) !important;
}

/* Product Cards */
.product-card {
    border: 1px solid var(--border-color) !important;
    transition: var(--transition);
    height: 100%;
    display: flex;
    flex-direction: column;
    margin: 0;
    padding: 0;
    border-radius: 0.5rem;
    overflow: hidden;
}

.product-card:hover {
    border-color: var(--primary-color) !important;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
}

.product-image-container {
    position: relative;
    overflow: hidden;
    background: #f8f9fa;
    transition: var(--transition);
    height: 200px; /* Fixed height for all images */
}

.product-image-container img {
    transition: transform 0.3s ease;
    object-fit: cover;
    width: 100%;
    height: 100%;
    object-position: center;
}

/* Ensure consistent card body height */
.product-card-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 1.25rem;
}

.product-actions {
    margin-top: auto;
    padding-top: 1rem;
}

.product-card:hover .product-image-container img {
    transform: scale(1.05);
}

/* Buttons */
.btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    transition: var(--transition);
}

.btn-primary:hover {
    background-color: var(--primary-hover);
    border-color: var(--primary-hover);
    transform: translateY(-1px);
}

.btn-outline-primary {
    color: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-outline-primary:hover {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

/* Badges */
.badge {
    font-weight: 500;
    padding: 0.35em 0.65em;
    border-radius: 0.25rem;
}

.badge.bg-primary {
    background-color: var(--primary-color) !important;
}

/* Pagination */
.pagination .page-link {
    color: var(--primary-color);
    border: 1px solid var(--border-color);
    margin: 0 3px;
    border-radius: 4px !important;
    min-width: 38px;
    text-align: center;
}

.pagination .page-item.active .page-link {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

/* Filter Sidebar */
.sticky-sidebar {
    position: sticky;
    top: 20px;
    z-index: 1000;
    background: white;
}

/* Form Controls */
.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
}

/* Responsive Adjustments */
@media (max-width: 991.98px) {
    .card-body {
        padding: 1rem;
    }
    
    .sticky-sidebar {
        position: static;
        margin-bottom: 1.5rem;
    }
    
    .product-image-container {
        height: 180px !important;
    }
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.product-card {
    animation: fadeIn 0.5s ease-out forwards;
    opacity: 0;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--primary-hover);
}

.text-white-75 {
    color: rgba(255, 255, 255, 0.85) !important;
}

.breadcrumb-item + .breadcrumb-item::before {
    color: rgba(255, 255, 255, 0.5);
}

.breadcrumb-item a {
    color: rgba(255, 255, 255, 0.85) !important;
    text-decoration: none;
}

.breadcrumb-item a:hover {
    color: #ffffff !important;
}

.breadcrumb-item.active {
    color: #ffffff !important;
}

.badge.bg-white {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style>

<div class="container py-4">
    <!-- Featured Categories -->
    <section class="mb-5" data-aos="fade-up">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-3 mb-md-0">Shop by Category</h2>
            <a href="categories.php" class="btn btn-outline-primary btn-sm">View All Categories <i class="fas fa-arrow-right ms-1"></i></a>
            <a href="#filter-section" class="text-decoration-none">View All <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            <?php
            // Enhanced category images with better presentation
            $category_images = [
                [
                    'name' => 'Vegetables', 
                    'slug' => 'Vegetables', 
                    'image' => 'public/images/categories/vegetables.jpg', 
                    'icon' => 'fa-carrot',
                    'color' => 'success'
                ],
                [
                    'name' => 'Fruits', 
                    'slug' => 'Fruits', 
                    'image' => 'public/images/categories/fruits.jpg', 
                    'icon' => 'fa-apple-alt',
                    'color' => 'danger'
                ],
                [
                    'name' => 'Grains', 
                    'slug' => 'Grains', 
                    'image' => 'public/images/categories/grains.jpg', 
                    'icon' => 'fa-wheat-awn',
                    'color' => 'warning'
                ],
                [
                    'name' => 'Dairy & Eggs', 
                    'slug' => 'Dairy+%26+Eggs', 
                    'image' => 'public/images/categories/dairy.jpg', 
                    'icon' => 'fa-egg',
                    'color' => 'info'
                ]
            ];
            
            foreach ($category_images as $category_item): 
            ?>
            <div class="col-6 col-md-3 mb-4">
                <a href="?category=<?php echo $category_item['slug']; ?>" class="text-decoration-none">
                    <div class="category-card h-100">
                        <div class="position-relative category-img-container bg-light rounded">
                            <div class="image-placeholder">
                                <i class="fas <?php echo $category_item['icon']; ?> fa-3x text-<?php echo $category_item['color']; ?>"></i>
                            </div>
                            <div class="category-overlay">
                                <div class="d-flex align-items-center">
                                    <span class="category-icon me-2 bg-<?php echo $category_item['color']; ?>">
                                        <i class="fas <?php echo $category_item['icon']; ?>"></i>
                                    </span>
                                    <h3 class="text-white fw-bold mb-0"><?php echo $category_item['name']; ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <div class="row g-4" id="main-content">
        <!-- Filter Sidebar - Simplified and Modern Design -->
        <div class="col-lg-3" id="filter-section" data-aos="fade-right">
            <div class="card border-0 shadow-sm rounded-3 sticky-sidebar">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold fs-6"><i class="fas fa-filter me-2 text-primary"></i>Filter Products</h5>
                    <a href="marketplace.php" class="text-decoration-none text-muted small"><i class="fas fa-sync-alt me-1"></i> Reset</a>
                </div>
                <div class="card-body">
                    <form action="marketplace.php" method="get" id="search-form">
                        <!-- Search Products -->
                        <div class="mb-4">
                            <div class="input-group">
                                <input type="text" name="search" id="search-input" class="form-control" 
                                       placeholder="Search products..." 
                                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                        
                        <!-- Categories -->
                        <div class="mb-4">
                            <label class="fw-bold mb-2">Categories</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category); ?>" 
                                            <?php echo (isset($_GET['category']) && $_GET['category'] == $category) ? 'selected' : ''; ?>>
                                        <?php echo ucfirst(htmlspecialchars($category)); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Price Range - Simplified -->
                        <div class="mb-4">
                            <label class="fw-bold mb-2">Price Range</label>
                            <?php 
                            // Get min and max prices from the product model
                            $price_range = $product_model->get_price_range();
                            $min_price = $price_range['min_price'];
                            $max_price = $price_range['max_price'];
                            
                            // Use filter values if set, otherwise use defaults
                            $current_min = isset($_GET['min_price']) ? floatval($_GET['min_price']) : $min_price;
                            $current_max = isset($_GET['max_price']) ? floatval($_GET['max_price']) : $max_price;
                            ?>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="badge bg-primary d-inline-flex align-items-center">
                                            <i class="fas fa-leaf me-1"></i> Organic
                                        </span>
                                        <input type="number" name="min_price" class="form-control" 
                                               value="<?php echo $current_min; ?>" 
                                               min="<?php echo $min_price; ?>">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Max</span>
                                        <input type="number" name="max_price" class="form-control" 
                                               value="<?php echo $current_max; ?>" 
                                               max="<?php echo $max_price; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sort By - Simplified -->
                        <div class="mb-4">
                            <label class="fw-bold mb-2">Sort By</label>
                            <select class="form-select" name="sort_by">
                                <option value="newest" <?php echo (!isset($_GET['sort_by']) || $_GET['sort_by'] == 'newest') ? 'selected' : ''; ?>>Newest First</option>
                                <option value="price_low" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_high" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
                            </select>
                        </div>
                        
                        <!-- Filter Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="col-lg-9" id="products-section">
            <!-- Sort and View Options -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 bg-white p-3 rounded-3 shadow-sm" data-aos="fade-up">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <h4 class="fw-bold mb-0 fs-5 me-3">
                        <span class="text-primary"><?php echo count($products); ?></span> Products Found
                        <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                            <span class="fs-5 text-muted">for "<?php echo htmlspecialchars($_GET['search']); ?>"</span>
                        <?php endif; ?>
                    </h4>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center">
                        <label for="sortOrder" class="form-label mb-0 me-2">Sort by:</label>
                        <select class="form-select form-select-sm" id="sortOrder" name="sort">
                            <option value="newest" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'newest') ? 'selected' : ''; ?>>Newest First</option>
                            <option value="price_low" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_high" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
                        </select>
                    </div>
                    <div class="btn-group shadow-sm" role="group" aria-label="View options">
                        <button type="button" class="btn btn-light active px-3" id="gridViewBtn">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button type="button" class="btn btn-light px-3" id="listViewBtn">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($products)): ?>
                <!-- Grid View (Default) -->
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4" id="gridView">
                    <?php foreach ($products as $index => $product): ?>
                        <div class="col" data-aos="fade-up" data-aos-duration="400">
                            <div class="card h-100 product-card position-relative">
                                <!-- Product Image -->
                                <div class="product-image-container" style="height: 200px; background: #f8f9fa; border-bottom: 1px solid var(--border-color); position: relative; overflow: hidden;">
                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                         class="product-image w-100 h-100" 
                                         style="object-fit: cover; object-position: center;">
                                    
                                    <!-- Category Badge -->
                                    <div class="position-absolute bottom-0 start-0 m-2">
                                        <span class="badge bg-primary text-white">
                                            <?php echo htmlspecialchars($product['category']); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Product Info -->
                                <div class="product-card-body">
                                    <h5 class="product-title">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </h5>
                                    
                                    <div class="product-price">
                                        K<?php echo number_format($product['price'], 2); ?>
                                    </div>
                                    
                                    <div class="product-meta">
                                        <span>
                                            <i class="fas fa-box me-1"></i>
                                            <?php echo $product['stock']; ?> in stock
                                        </span>
                                        <span>
                                            <i class="fas fa-store me-1"></i>
                                            <?php echo htmlspecialchars($product['farmer_name']); ?>
                                        </span>
                                    </div>
                                    
                                    <p class="product-description">
                                        <?php echo htmlspecialchars($product['description']); ?>
                                    </p>
                                    
                                    <div class="product-actions">
                                        <form action="cart.php" method="POST" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                            <input type="hidden" name="action" value="add">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button class="btn btn-sm btn-primary flex-grow-1" onclick="addToCart(<?php echo $product['id']; ?>, 1)" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                                                <i class="fas fa-<?php echo $product['stock'] > 0 ? 'shopping-cart' : 'times'; ?> me-1"></i> 
                                                <?php echo $product['stock'] > 0 ? 'Add to Cart' : 'Out of Stock'; ?>
                                            </button>
                                        </form>
                                        <button class="btn btn-favorite" title="Add to Wishlist">
                                            <i class="far fa-heart"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- List View (Hidden by default) -->
                <div class="row g-4 d-none" id="listView">
                    <?php foreach ($products as $product): ?>
                        <div class="col-12" data-aos="fade-up" data-aos-duration="400">
                            <div class="card product-card border-0 shadow-sm rounded-3 h-100">
                                <div class="row g-0 h-100">
                                    <!-- Product Image -->
                                    <div class="col-md-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="mb-0 fw-bold text-truncate" style="max-width: 70%;">
                                                <?php echo htmlspecialchars($product['name']); ?>
                                            </h5>
                                            <div class="text-end">
                                                <?php if (isset($product['original_price']) && $product['original_price'] > $product['price']): ?>
                                                    <small class="text-decoration-line-through text-muted d-block">K<?php echo number_format($product['original_price'], 2); ?></small>
                                                <?php endif; ?>
                                                <span class="text-primary fw-bold">K<?php echo number_format($product['price'], 2); ?></span>
                                                <?php if (!empty($product['unit'])): ?>
                                                    <small class="text-muted d-block">/<?php echo htmlspecialchars($product['unit']); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="product-image-container h-100">
                                            <?php 
                                            // Product image display logic similar to main products
                                            if (!empty($product['image']) && file_exists('public/uploads/products/' . $product['image'])): 
                                            ?>
                                            <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                 class="product-image">
                                            
                                            <?php if (isset($product['is_organic']) && $product['is_organic']): ?>
                                                <div class="product-badge organic-badge">
                                                    <i class="fas fa-leaf me-1"></i> Organic
                                                </div>
                                            <?php endif; ?>
                                            <?php endif; // Close the file_exists condition ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Product Info -->
                                    <div class="col-md-9">
                                        <div class="product-card-body h-100 d-flex flex-column p-3">
                                            <?php if (isset($product['rating'])): ?>
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="star-rating me-2">
                                                    <?php
                                                    $fullStars = floor($product['rating']);
                                                    $hasHalfStar = $product['rating'] - $fullStars >= 0.5;
                                                    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                                                    
                                                    for ($i = 0; $i < $fullStars; $i++) {
                                                        echo '<i class="fas fa-star text-warning"></i>';
                                                    }
                                                    if ($hasHalfStar) {
                                                        echo '<i class="fas fa-star-half-alt text-warning"></i>';
                                                    }
                                                    for ($i = 0; $i < $emptyStars; $i++) {
                                                        echo '<i class="far fa-star text-warning"></i>';
                                                    }
                                                    ?>
                                                </div>
                                                <small class="text-muted">(<?php echo isset($product['review_count']) ? $product['review_count'] : 0; ?>)</small>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <div class="mb-2">
                                                <h5 class="product-title h6 fw-bold mb-1">
                                                    <?php echo htmlspecialchars($product['name']); ?>
                                                </h5>
                                                <div class="product-badge category-badge d-inline-block mb-2">
                                                    <?php echo htmlspecialchars($product['category']); ?>
                                                </div>
                                            </div>
                                            
                                            <p class="text-muted small mb-3 flex-grow-1">
                                                <?php echo htmlspecialchars(mb_substr($product['description'], 0, 150)) . (mb_strlen($product['description']) > 150 ? '...' : ''); ?>
                                            </p>
                                            
                                            <div class="mt-auto pt-2 border-top">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <?php if (isset($product['original_price']) && $product['original_price'] > $product['price']): ?>
                                                            <small class="text-decoration-line-through text-muted d-block">K<?php echo number_format($product['original_price'], 2); ?></small>
                                                        <?php endif; ?>
                                                        <span class="text-primary fw-bold">K<?php echo number_format($product['price'], 2); ?></span>
                                                        <?php if (isset($product['unit']) && $product['unit']): ?>
                                                            <small class="text-muted">/<?php echo htmlspecialchars($product['unit']); ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <a href="product-details.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye me-1"></i> View
                                                        </a>
                                                        <form action="cart.php" method="POST" class="d-inline">
                                                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                                            <input type="hidden" name="action" value="add">
                                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                            <input type="hidden" name="quantity" value="1">
                                                            <button type="submit" class="btn btn-sm btn-primary" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                                                                <i class="fas fa-<?php echo $product['stock'] > 0 ? 'shopping-cart' : 'times'; ?> me-1"></i> 
                                                                <?php echo $product['stock'] > 0 ? 'Add to Cart' : 'Out of Stock'; ?>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <nav aria-label="Product pagination" class="mt-5 pt-3">
                    <ul class="pagination justify-content-center flex-wrap">
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['location']) ? '&location=' . $_GET['location'] : ''; ?><?php echo isset($_GET['min_price']) ? '&min_price=' . $_GET['min_price'] : ''; ?><?php echo isset($_GET['max_price']) ? '&max_price=' . $_GET['max_price'] : ''; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= ceil($product_model->count_products() / $limit); $i++): ?>
                            <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['location']) ? '&location=' . $_GET['location'] : ''; ?><?php echo isset($_GET['min_price']) ? '&min_price=' . $_GET['min_price'] : ''; ?><?php echo isset($_GET['max_price']) ? '&max_price=' . $_GET['max_price'] : ''; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo $page >= ceil($product_model->count_products() / $limit) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['location']) ? '&location=' . $_GET['location'] : ''; ?><?php echo isset($_GET['min_price']) ? '&min_price=' . $_GET['min_price'] : ''; ?><?php echo isset($_GET['max_price']) ? '&max_price=' . $_GET['max_price'] : ''; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php else: ?>
                <div class="text-center py-5" data-aos="fade-up">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No products found</h4>
                    <p class="text-muted">Try adjusting your search or filter criteria</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Recently Viewed Products -->
<?php if (isset($_SESSION['recently_viewed']) && count($_SESSION['recently_viewed']) > 0): ?>
<div class="container mb-5" data-aos="fade-up">
    <h3 class="fw-bold mb-4">Recently Viewed</h3>
    <div class="row">
        <?php 
        // Get most recent 4 viewed products
        $viewed_ids = array_slice(array_reverse($_SESSION['recently_viewed']), 0, 4);
        $viewed_products = [];
        
        foreach ($viewed_ids as $viewed_id) {
            $viewed_product = $product_model->get_product($viewed_id);
            if ($viewed_product) {
                $viewed_products[] = $viewed_product;
            }
        }
        
        foreach ($viewed_products as $product): 
        ?>
        <div class="col-md-3 mb-3">
            <div class="card h-100 product-card-compact">
                <div class="product-image-container-small">
                    <?php 
                    // Product image display logic similar to main products
                    if (!empty($product['image']) && file_exists('public/uploads/products/' . $product['image'])): 
                    ?>
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="card-img-top" 
                         onerror="this.src='public/images/default-product.jpg'">
                    <?php else: 
                        $category_img = 'public/images/categories/' . strtolower($product['category']) . '.jpg';
                        if (file_exists($category_img)): 
                    ?>
                    <img src="<?php echo $category_img; ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="card-img-top" 
                         onerror="this.src='public/images/default-product.jpg'">
                    <?php else: ?>
                    <img src="public/images/default-product.jpg" alt="<?php echo htmlspecialchars($product['name']); ?>" class="card-img-top">
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <h6 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h6>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><?php echo format_price($product['price']); ?></span>
                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Quick View Modal -->
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickViewModalLabel">Product Quick View</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 product-image-container" id="quickview-image">
                        <!-- Product image will be loaded here -->
                    </div>
                    <div class="col-md-6" id="quickview-content">
                        <!-- Product details will be loaded here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" class="btn btn-primary" id="quickview-view-details">View Details</a>
                <button type="button" class="btn btn-success" id="quickview-add-to-cart" data-product-id="">Add to Cart</button>
            </div>
        </div>
    </div>
</div>

<!-- Scroll to Top Button -->
<div id="scroll-to-top" class="scroll-to-top">
    <i class="fas fa-arrow-up"></i>
</div>

<!-- Notification Toast Container -->
<div id="toast-container"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize AOS Animation Library
        AOS.init({
            // Disable animations on mobile for better performance
            disable: window.innerWidth < 768,
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });
        
        // Toggle between grid and list view
        const gridViewBtn = document.getElementById('gridViewBtn');
        const listViewBtn = document.getElementById('listViewBtn');
        const gridView = document.getElementById('gridView');
        const listView = document.getElementById('listView');
        
        gridViewBtn.addEventListener('click', function() {
            gridView.classList.remove('d-none');
            listView.classList.add('d-none');
            gridViewBtn.classList.add('active');
            listViewBtn.classList.remove('active');
        });
        
        listViewBtn.addEventListener('click', function() {
            gridView.classList.add('d-none');
            listView.classList.remove('d-none');
            gridViewBtn.classList.remove('active');
            listViewBtn.classList.add('active');
        });
        
        // Initialize sort order dropdown functionality
        const sortOrder = document.getElementById('sortOrder');
        if (sortOrder) {
            sortOrder.addEventListener('change', function() {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('sort', this.value);
                window.location.href = currentUrl.toString();
            });
        }
        
        // Initialize scroll to top button
        const scrollToTopBtn = document.getElementById('scroll-to-top');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                scrollToTopBtn.classList.add('show');
            } else {
                scrollToTopBtn.classList.remove('show');
            }
        });
        
        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Initialize image zoom on product cards
        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach(card => {
            const img = card.querySelector('.product-img');
            if (img) {
                img.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.1)';
                });
                
                img.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            }
        });
    });
</script>

<!-- Add this script reference to the marketplace.php main file instead -->
<!-- The additional_js variable is defined in the main marketplace.php file -->
<?php
// Add the search autocomplete JavaScript to additional_js if it doesn't already include it
if (!isset($additional_js) || strpos($additional_js, 'search-autocomplete.js') === false) {
    echo '<script src="public/js/search-autocomplete.js"></script>';
}
?>
