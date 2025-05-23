<!-- Simple Marketplace Header -->
<div class="bg-light py-4 mb-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="fw-bold mb-1">Marketplace</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Marketplace</li>
                    </ol>
                </nav>
            </div>
            <div class="col-lg-6">
                <div class="d-flex justify-content-lg-end mt-3 mt-lg-0">
                    <a href="#products-section" class="btn btn-primary me-2">
                        <i class="fas fa-shopping-basket me-2"></i>Browse Products
                    </a>
                    <a href="#filter-section" class="btn btn-outline-primary">
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
            // Category images from reliable sources
            $category_images = [
                ['name' => 'Vegetables', 'slug' => 'Vegetables', 'image' => 'https://cdn.pixabay.com/photo/2015/05/30/01/18/vegetables-790022_1280.jpg', 'delay' => 100],
                ['name' => 'Fruits', 'slug' => 'Fruits', 'image' => 'https://cdn.pixabay.com/photo/2017/05/11/19/44/fresh-fruits-2305192_1280.jpg', 'delay' => 200],
                ['name' => 'Grains', 'slug' => 'Grains', 'image' => 'https://cdn.pixabay.com/photo/2014/12/11/02/55/corn-563759_1280.jpg', 'delay' => 300],
                ['name' => 'Dairy & Eggs', 'slug' => 'Dairy+%26+Eggs', 'image' => 'https://cdn.pixabay.com/photo/2017/07/05/15/41/milk-2474993_1280.jpg', 'delay' => 400]
            ];
            
            foreach ($category_images as $category_item): 
            ?>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="<?php echo $category_item['delay']; ?>">
                <a href="?category=<?php echo $category_item['slug']; ?>" class="text-decoration-none">
                    <div class="card h-100 border-0 rounded-4 overflow-hidden shadow-sm">
                        <div class="position-relative">
                            <img src="<?php echo $category_item['image']; ?>" class="card-img-top category-img" alt="<?php echo $category_item['name']; ?>" onerror="this.src='public/images/default-product.jpg'">
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center category-overlay">
                                <h3 class="text-white fw-bold mb-0"><?php echo $category_item['name']; ?></h3>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <div class="row" id="main-content">
        <!-- Filter Sidebar - Made Sticky -->
        <div class="col-lg-3 mb-4" id="filter-section" data-aos="fade-right">
            <div class="card border-0 shadow-sm rounded-4 sticky-sidebar">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-sliders-h me-2 text-primary"></i>Filters</h5>
                </div>
                <div class="card-body">
                    <form action="marketplace.php" method="GET">
                        <!-- Search -->
                        <div class="mb-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        </div>
                        
                        <!-- Category Filter -->
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category; ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $category) ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($category); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Location Filter -->
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location"
                                   value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>">
                        </div>
                        
                        <!-- Price Range -->
                        <div class="mb-3">
                            <label class="form-label">Price Range</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" class="form-control" placeholder="Min" name="min_price" min="0"
                                           value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control" placeholder="Max" name="max_price" min="0"
                                           value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        <a href="marketplace.php" class="btn btn-outline-secondary w-100 mt-2">Clear Filters</a>
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
                </div>
            </div>
            
            <?php if (!empty($products)): ?>
                <!-- Grid View (Default) -->
                <div class="row g-4" id="gridView">
                    <?php foreach ($products as $index => $product): ?>
                        <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                            <div class="product-card h-100">
                                <div class="img-container">
                                    <?php if (!empty($product['image']) && file_exists('public/uploads/products/' . $product['image'])): ?>
                                        <img src="public/uploads/products/<?php echo $product['image']; ?>" class="product-img" alt="<?php echo $product['name']; ?>" onerror="this.src='public/images/default-product.jpg'">
                                    <?php elseif(!empty($product['category'])): ?>
                                        <?php 
                                        $category_img_path = 'public/images/' . strtolower(str_replace(' ', '_', $product['category'])) . '.jpg';
                                        $category_online_imgs = [
                                            'vegetables' => 'https://cdn.pixabay.com/photo/2015/05/30/01/18/vegetables-790022_1280.jpg',
                                            'fruits' => 'https://cdn.pixabay.com/photo/2017/05/11/19/44/fresh-fruits-2305192_1280.jpg',
                                            'grains' => 'https://cdn.pixabay.com/photo/2014/12/11/02/55/corn-563759_1280.jpg',
                                            'dairy' => 'https://cdn.pixabay.com/photo/2017/07/05/15/41/milk-2474993_1280.jpg'
                                        ];
                                        $category_key = strtolower(str_replace(' ', '_', $product['category']));
                                        $img_src = file_exists($category_img_path) ? $category_img_path : 
                                                  (isset($category_online_imgs[$category_key]) ? $category_online_imgs[$category_key] : 'public/images/default-product.jpg');
                                        ?>
                                        <img src="<?php echo $img_src; ?>" class="product-img" alt="<?php echo $product['name']; ?>" onerror="this.src='public/images/default-product.jpg'">
                                    <?php else: ?>
                                        <img src="public/images/default-product.jpg" class="product-img" alt="<?php echo $product['name']; ?>">
                                    <?php endif; ?>
                                    
                                    <?php if (isset($product['is_organic']) && $product['is_organic']): ?>
                                        <span class="organic-badge card-badge">Organic</span>
                                    <?php endif; ?>
                                    
                                    <div class="product-overlay">
                                        <a href="product.php?action=view&id=<?php echo $product['id']; ?>" class="overlay-btn">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (isset($product['stock']) && $product['stock'] > 0): ?>
                                            <form action="cart.php" method="POST" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                                <input type="hidden" name="action" value="add">
                                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="overlay-btn" title="Add to Cart">
                                                    <i class="fas fa-cart-plus"></i>
                                                </button>
                                            </form>
                                            <a href="message.php?action=compose&to=<?php echo $product['farmer_id']; ?>&product_id=<?php echo $product['id']; ?>" class="overlay-btn">
                                                <i class="fas fa-comment"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="card-body">
                                    <span class="product-category"><?php echo $product['category']; ?></span>
                                    <h5 class="product-title"><?php echo $product['name']; ?></h5>
                                    <p class="card-text text-truncate"><?php echo $product['description']; ?></p>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="product-price">
                                            <?php echo format_price($product['price']); ?>
                                        </div>
                                        <?php if (isset($product['stock']) && $product['stock'] > 0): ?>
                                            <form action="cart.php" method="POST">
                                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                                <input type="hidden" name="action" value="add">
                                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-sm btn-primary" title="Add to Cart">
                                                    <i class="fas fa-cart-plus"></i> Add
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Out of Stock</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?php echo $product['location']; ?></span>
                                    </div>
                                    
                                    <div class="product-meta">
                                        <div class="farmer-info">
                                            <div class="farmer-avatar-placeholder"><?php echo strtoupper(substr($product['farmer_name'], 0, 1)); ?></div>
                                            <span class="farmer-name"><?php echo $product['farmer_name']; ?></span>
                                        </div>
                                        
                                        <div>
                                            <span class="badge bg-light text-dark">
                                                <?php echo $product['stock']; ?> in stock
                                            </span>
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
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="text-muted">
                                                    <i class="fas fa-user me-1"></i><?php echo $product['farmer_name']; ?>
                                                </small>
                                                <small class="text-muted ms-3">
                                                    <i class="fas fa-map-marker-alt me-1"></i><?php echo $product['farmer_location']; ?>
                                                </small>
                                            </div>
                                            <div>
                                                <?php if (!empty($product['category'])): ?>
                                                    <span class="badge badge-category"><?php echo $product['category']; ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="mt-3">
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
