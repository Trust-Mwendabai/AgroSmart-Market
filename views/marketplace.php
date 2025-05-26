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
                <span class="badge bg-light text-dark mb-2">Fresh Local Produce</span>
                <h1 class="display-4 fw-bold mb-2">Farm to Table Marketplace</h1>
                <p class="lead mb-4 opacity-75">Connect directly with local farmers for fresh, quality produce</p>
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
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

<div class="container py-5">
    <!-- Featured Categories -->
    <section class="mb-5" data-aos="fade-up">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Categories</h2>
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
    
    <div class="row" id="main-content">
        <!-- Filter Sidebar - Simplified and Modern Design -->
        <div class="col-lg-3 mb-4" id="filter-section" data-aos="fade-right">
            <div class="card border-0 shadow-sm rounded-3 sticky-sidebar">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-filter me-2 text-primary"></i>Filter Products</h5>
                    <a href="marketplace.php" class="text-decoration-none text-muted small">Reset</a>
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
                                        <span class="input-group-text">Min</span>
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
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="col-lg-9" id="products-section">
            <!-- Sort Options -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4" data-aos="fade-up">
                <div class="mb-3 mb-md-0">
                    <h4 class="fw-bold mb-0">
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
                            <option value="popularity" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'popularity') ? 'selected' : ''; ?>>Popularity</option>
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
                    <!-- Mobile filter toggle button -->
                    <button type="button" class="btn btn-outline-primary d-md-none ms-2" id="mobileFilterBtn">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
            
            <?php if (!empty($products)): ?>
                <!-- Grid View (Default) -->
                <div class="row g-4" id="gridView">
                    <?php foreach ($products as $index => $product): ?>
                        <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up" data-aos-duration="400">
                            <div class="card h-100 product-card border-0 shadow-sm rounded-3 position-relative">
                                <!-- Category Badge -->
                                <?php 
                                // Different icons based on product category
                                $icon = 'fa-seedling';
                                $color = 'success';
                                $bgColor = 'rgba(25, 135, 84, 0.1)';
                                $textColor = '#198754';
                                
                                // Set icon and color based on category
                                switch(strtolower($product['category'])) {
                                    case 'vegetables':
                                        $icon = 'fa-carrot';
                                        $color = 'success';
                                        $bgColor = 'rgba(25, 135, 84, 0.1)';
                                        $textColor = '#198754';
                                        break;
                                    case 'fruits':
                                        $icon = 'fa-apple-alt';
                                        $color = 'danger';
                                        $bgColor = 'rgba(220, 53, 69, 0.1)';
                                        $textColor = '#dc3545';
                                        break;
                                    case 'grains':
                                        $icon = 'fa-wheat-awn';
                                        $color = 'warning';
                                        $bgColor = 'rgba(255, 193, 7, 0.1)';
                                        $textColor = '#ffc107';
                                        break;
                                    case 'dairy':
                                    case 'dairy & eggs':
                                        $icon = 'fa-egg';
                                        $color = 'info';
                                        $bgColor = 'rgba(13, 202, 240, 0.1)';
                                        $textColor = '#0dcaf0';
                                        break;
                                    case 'meat':
                                    case 'poultry':
                                        $icon = 'fa-drumstick-bite';
                                        $color = 'danger';
                                        $bgColor = 'rgba(220, 53, 69, 0.1)';
                                        $textColor = '#dc3545';
                                        break;
                                    default:
                                        $icon = 'fa-seedling';
                                        $color = 'success';
                                        $bgColor = 'rgba(25, 135, 84, 0.1)';
                                        $textColor = '#198754';
                                }
                                ?>
                                <div class="position-absolute top-0 start-0 m-3 z-1">
                                    <span class="badge rounded-pill px-3 py-2" style="background-color: <?php echo $bgColor; ?>; color: <?php echo $textColor; ?>">
                                        <i class="fas <?php echo $icon; ?> me-1"></i> <?php echo htmlspecialchars($product['category']); ?>
                                    </span>
                                </div>
                                
                                <!-- Product Status Badge -->
                                <?php if ($product['stock'] <= 5 && $product['stock'] > 0): ?>
                                    <div class="position-absolute top-0 end-0 m-3 z-1">
                                        <span class="badge bg-warning text-dark">Limited Stock</span>
                                    </div>
                                <?php elseif ($product['stock'] <= 0): ?>
                                    <div class="position-absolute top-0 end-0 m-3 z-1">
                                        <span class="badge bg-danger">Out of Stock</span>
                                    </div>
                                <?php elseif (strtotime($product['date_added']) > strtotime('-7 days')): ?>
                                    <div class="position-absolute top-0 end-0 m-3 z-1">
                                        <span class="badge bg-success">New</span>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Product Image -->
                                <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 180px; background-color: <?php echo $bgColor; ?>">
                                    <i class="fas <?php echo $icon; ?> fa-4x" style="color: <?php echo $textColor; ?>"></i>
                                </div>
                                
                                <div class="card-body d-flex flex-column p-4">
                                    <!-- Product Title -->
                                    <h5 class="card-title fw-bold mb-1"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    
                                    <!-- Farmer Info -->
                                    <div class="mb-2">
                                        <small class="text-muted">By <span class="text-primary"><?php echo htmlspecialchars($product['farmer_name']); ?></span></small>
                                    </div>
                                    
                                    <!-- Description -->
                                    <p class="card-text text-muted mb-3"><?php echo substr(htmlspecialchars($product['description']), 0, 80) . (strlen($product['description']) > 80 ? '...' : ''); ?></p>
                                    
                                    <!-- Spacer to push the price and button to the bottom -->
                                    <div class="mt-auto">
                                        <!-- Price -->
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h4 class="mb-0 fw-bold text-primary">$<?php echo number_format($product['price'], 2); ?></h4>
                                            <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-secondary rounded-circle" title="View Details">
                                                <i class="fas fa-info"></i>
                                            </a>
                                        </div>
                                        
                                        <!-- Action Button -->
                                        <div class="d-grid">
                                            <?php if ($product['stock'] > 0): ?>
                                                <a href="cart.php?add=<?php echo $product['id']; ?>" class="btn btn-primary">
                                                    <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-secondary" disabled>
                                                    <i class="fas fa-exclamation-circle me-2"></i>Out of Stock
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- List View (Hidden by default) -->
                <div class="d-none" id="listView">
                    <?php foreach ($products as $product): ?>
                        <div class="card mb-3">
                            <div class="row g-0">
                                <div class="col-md-3">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="public/uploads/<?php echo $product['image']; ?>" class="img-fluid rounded-start h-100 w-100 object-fit-cover" alt="<?php echo $product['name']; ?>">
                                    <?php else: ?>
                                        <img src="images/<?php echo strtolower(str_replace(' ', '_', $product['category'])); ?>.jpg" class="img-fluid rounded-start h-100 w-100 object-fit-cover" alt="<?php echo $product['name']; ?>" onerror="this.src='images/default-product.jpg'">
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-9">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h5 class="card-title"><?php echo $product['name']; ?></h5>
                                            <span class="fw-bold text-primary"><?php echo format_price($product['price']); ?></span>
                                        </div>
                                        <p class="card-text"><?php echo substr($product['description'], 0, 150) . (strlen($product['description']) > 150 ? '...' : ''); ?></p>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="fw-bold price-display">
                                            ZMW <?php echo number_format($product['price'], 2); ?>
                                        </div>
                                        <div class="product-rating">
                                            <?php 
                                            // Display ratings if they exist
                                            $avg_rating = isset($product['avg_rating']) ? floatval($product['avg_rating']) : 0;
                                            $review_count = isset($product['review_count']) ? intval($product['review_count']) : 0;
                                            
                                            // Star rating display
                                            echo '<div class="rating-stars" title="' . number_format($avg_rating, 1) . ' out of 5 stars">';
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($i <= $avg_rating) {
                                                    echo '<i class="fas fa-star text-warning"></i>';
                                                } elseif ($i <= $avg_rating + 0.5) {
                                                    echo '<i class="fas fa-star-half-alt text-warning"></i>';
                                                } else {
                                                    echo '<i class="far fa-star text-warning"></i>';
                                                }
                                            }
                                            echo '</div>';
                                            
                                            // Display review count if any
                                            if ($review_count > 0) {
                                                echo '<small class="text-muted ms-2">(' . $review_count . ')</small>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <a href="controllers/product.php?action=view&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <nav aria-label="Product pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
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
                <div class="alert alert-info">
                    <h4 class="alert-heading">No products found!</h4>
                    <p>We couldn't find any products matching your criteria. Try adjusting your filters or check back later.</p>
                    <hr>
                    <p class="mb-0">
                        <a href="marketplace.php" class="btn btn-info">Clear All Filters</a>
                    </p>
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
                    <img src="public/uploads/products/<?php echo $product['image']; ?>" 
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
            // Refresh image loading when view changes
            if (window.refreshImageLoading) window.refreshImageLoading();
        });
        
        listViewBtn.addEventListener('click', function() {
            gridView.classList.add('d-none');
            listView.classList.remove('d-none');
            gridViewBtn.classList.remove('active');
            listViewBtn.classList.add('active');
            // Refresh image loading when view changes
            if (window.refreshImageLoading) window.refreshImageLoading();
        });
        
        // Mobile filter toggle
        const mobileFilterBtn = document.getElementById('mobileFilterBtn');
        const filterSection = document.getElementById('filter-section');
        
        if (mobileFilterBtn && filterSection) {
            mobileFilterBtn.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    filterSection.classList.toggle('d-none');
                    
                    // Change button icon based on filter visibility
                    const icon = this.querySelector('i');
                    if (filterSection.classList.contains('d-none')) {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-filter');
                        this.setAttribute('aria-label', 'Show filters');
                    } else {
                        icon.classList.remove('fa-filter');
                        icon.classList.add('fa-times');
                        this.setAttribute('aria-label', 'Hide filters');
                    }
                }
            });
            
            // Hide filters by default on mobile
            if (window.innerWidth < 768) {
                filterSection.classList.add('d-none');
            }
        }
        
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
