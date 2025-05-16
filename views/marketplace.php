<!-- Hero Banner with Parallax Effect -->
<div class="hero position-relative overflow-hidden">
    <div class="hero-bg-image" style="background-image: url('public/images/marketplace-banner.jpg');"></div>
    <div class="container position-relative z-index-1 py-5">
        <div class="row align-items-center py-5">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold text-white hero-animated" data-aos="fade-up">Fresh From The Farm</h1>
                <p class="lead text-white mb-4 hero-animated" data-aos="fade-up" data-aos-delay="100">Connect directly with local farmers and buy fresh, seasonal produce at fair prices</p>
                <div class="d-flex gap-3 hero-animated" data-aos="fade-up" data-aos-delay="200">
                    <a href="#products-section" class="btn btn-primary btn-lg px-4 py-2">
                        <i class="fas fa-shopping-basket me-2"></i>Browse Products
                    </a>
                    <a href="#filter-section" class="btn btn-light btn-lg px-4 py-2">
                        <i class="fas fa-filter me-2"></i>Filter Options
                    </a>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block" data-aos="fade-left" data-aos-delay="300">
                <img src="public/images/farm-illustration.png" alt="Farm Illustration" class="img-fluid hero-image">
            </div>
        </div>
    </div>
    <div class="hero-overlay"></div>
    <div class="hero-wave">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#ffffff" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,154.7C384,149,480,107,576,117.3C672,128,768,192,864,202.7C960,213,1056,171,1152,149.3C1248,128,1344,128,1392,128L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>
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
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="100">
                <a href="?category=Vegetables" class="text-decoration-none">
                    <div class="card h-100 border-0 rounded-4 overflow-hidden">
                        <div class="position-relative">
                            <img src="public/images/vegetables-category.jpg" class="card-img-top" alt="Vegetables">
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.3)">
                                <h3 class="text-white fw-bold mb-0">Vegetables</h3>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="200">
                <a href="?category=Fruits" class="text-decoration-none">
                    <div class="card h-100 border-0 rounded-4 overflow-hidden">
                        <div class="position-relative">
                            <img src="public/images/fruits-category.jpg" class="card-img-top" alt="Fruits">
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.3)">
                                <h3 class="text-white fw-bold mb-0">Fruits</h3>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="300">
                <a href="?category=Grains" class="text-decoration-none">
                    <div class="card h-100 border-0 rounded-4 overflow-hidden">
                        <div class="position-relative">
                            <img src="public/images/grains-category.jpg" class="card-img-top" alt="Grains">
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.3)">
                                <h3 class="text-white fw-bold mb-0">Grains</h3>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="400">
                <a href="?category=Dairy+%26+Eggs" class="text-decoration-none">
                    <div class="card h-100 border-0 rounded-4 overflow-hidden">
                        <div class="position-relative">
                            <img src="public/images/dairy-category.jpg" class="card-img-top" alt="Dairy & Eggs">
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.3)">
                                <h3 class="text-white fw-bold mb-0">Dairy & Eggs</h3>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>
    
    <div class="row" id="main-content">
        <!-- Filter Sidebar -->
        <div class="col-lg-3 mb-4" id="filter-section" data-aos="fade-right">
            <div class="card border-0 shadow-sm rounded-4">
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
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="public/uploads/products/<?php echo $product['image']; ?>" class="product-img" alt="<?php echo $product['name']; ?>">
                                    <?php else: ?>
                                        <img src="public/images/<?php echo strtolower(str_replace(' ', '_', $product['category'])); ?>.jpg" class="product-img" alt="<?php echo $product['name']; ?>" onerror="this.src='public/images/default-product.jpg'">
                                    <?php endif; ?>
                                    
                                    <?php if (isset($product['is_organic']) && $product['is_organic']): ?>
                                        <span class="organic-badge card-badge">Organic</span>
                                    <?php endif; ?>
                                    
                                    <div class="product-overlay">
                                        <a href="product.php?action=view&id=<?php echo $product['id']; ?>" class="overlay-btn">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (is_logged_in() && is_buyer()): ?>
                                            <a href="order.php?action=create&product=<?php echo $product['id']; ?>" class="overlay-btn">
                                                <i class="fas fa-shopping-cart"></i>
                                            </a>
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
                                    <div class="product-price mb-3">
                                        <?php echo format_price($product['price']); ?>
                                    </div>
                                    <div class="product-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?php echo $product['location']; ?></span>
                                    </div>
                                    
                                    <div class="product-meta">
                                        <div class="farmer-info">
                                            <?php if (!empty($product['farmer_image'])): ?>
                                                <img src="public/uploads/<?php echo $product['farmer_image']; ?>" class="farmer-avatar" alt="Farmer">
                                            <?php else: ?>
                                                <div class="farmer-avatar bg-primary text-white d-flex align-items-center justify-content-center">
                                                    <?php echo strtoupper(substr($product['farmer_name'], 0, 1)); ?>
                                                </div>
                                            <?php endif; ?>
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
