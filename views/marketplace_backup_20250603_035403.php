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
.bg-gradient-primary {
    background: linear-gradient(135deg, #1e7e34 0%, #28a745 100%);
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

.product-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important;
}

.product-image {
    transition: transform 0.3s ease-in-out;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.sticky-top {
    position: -webkit-sticky;
    position: sticky;
    z-index: 1020;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<div class="container py-4">
    <!-- Main Content Row -->
    <div class="row g-4" id="main-content">
        <!-- Filter & Categories Sidebar -->
        <div class="col-lg-3" id="filter-section">
            <div class="sticky-top" style="top: 20px;">
                <!-- Categories Section -->
                <div class="card border-0 shadow-sm rounded-3 mb-4" data-aos="fade-right">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-tags me-2 text-primary"></i>Categories</h5>
                    </div>
                    <div class="card-body p-3">
                        <div class="list-group list-group-flush">
                            <?php
                            $categories = [
                                ['name' => 'All Products', 'slug' => '', 'icon' => 'fa-box', 'count' => count($products)],
                                ['name' => 'Vegetables', 'slug' => 'Vegetables', 'icon' => 'fa-carrot', 'count' => 0],
                                ['name' => 'Fruits', 'slug' => 'Fruits', 'icon' => 'fa-apple-alt', 'count' => 0],
                                ['name' => 'Grains', 'slug' => 'Grains', 'icon' => 'fa-wheat-awn', 'count' => 0],
                                ['name' => 'Dairy', 'slug' => 'Dairy', 'icon' => 'fa-cheese', 'count' => 0],
                                ['name' => 'Meat', 'slug' => 'Meat', 'icon' => 'fa-drumstick-bite', 'count' => 0],
                                ['name' => 'Herbs', 'slug' => 'Herbs', 'icon' => 'fa-leaf', 'count' => 0],
                                ['name' => 'Tubers', 'slug' => 'Tubers', 'icon' => 'fa-potato', 'count' => 0],
                            ];
                            
                            foreach ($categories as $category):
                                $isActive = (isset($_GET['category']) && $_GET['category'] === $category['slug']) || 
                                           (!isset($_GET['category']) && $category['slug'] === '');
                            ?>
                            <a href="?category=<?php echo $category['slug']; ?>" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center rounded-3 mb-1 border-0 <?php echo $isActive ? 'bg-light text-primary fw-bold' : 'text-dark' ?>">
                                <div>
                                    <i class="fas <?php echo $category['icon']; ?> me-2"></i>
                                    <?php echo $category['name']; ?>
                                </div>
                                <span class="badge bg-secondary bg-opacity-10 text-dark rounded-pill"><?php echo $category['count']; ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Filter Section -->
                <div class="card border-0 shadow-sm rounded-3" data-aos="fade-right" data-aos-delay="100">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-filter me-2 text-primary"></i>Filters</h5>
                        <a href="marketplace.php" class="text-decoration-none text-muted small">Reset</a>
                    </div>
                    <div class="card-body p-3">
                        <form action="marketplace.php" method="get" id="search-form">
                            <!-- Search -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Search</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" class="form-control border-start-0" name="q" placeholder="Search products..." 
                                           value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                                </div>
                            </div>
                            
                            <!-- Price Range -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Price Range</label>
                                <div class="d-flex align-items-center">
                                    <input type="number" class="form-control" name="min_price" placeholder="Min" 
                                           value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>">
                                    <span class="mx-2">-</span>
                                    <input type="number" class="form-control" name="max_price" placeholder="Max" 
                                           value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">
                                </div>
                            </div>
                            
                            <!-- Organic Toggle -->
                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" role="switch" id="organicFilter" name="organic" 
                                       <?php echo (isset($_GET['organic']) && $_GET['organic'] == '1') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="organicFilter">Organic Only</label>
                            </div>
                            
                            <!-- Sort By -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Sort By</label>
                                <select class="form-select" name="sort">
                                    <option value="newest" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'newest') ? 'selected' : ''; ?>>Newest</option>
                                    <option value="price_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                                    <option value="price_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                                    <option value="name_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') ? 'selected' : ''; ?>>Name: A to Z</option>
                                    <option value="name_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name_desc') ? 'selected' : ''; ?>>Name: Z to A</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Products Section -->
        <div class="col-lg-9" id="products-section">
            <!-- Sort & View Options -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div class="mb-3 mb-md-0">
                    <h4 class="fw-bold mb-0">
                        <?php 
                        $filter_text = [];
                        if (isset($_GET['category']) && !empty($_GET['category'])) {
                            $filter_text[] = 'Category: ' . htmlspecialchars($_GET['category']);
                        }
                        if (isset($_GET['q']) && !empty($_GET['q'])) {
                            $filter_text[] = 'Search: ' . htmlspecialchars($_GET['q']);
                        }
                        echo !empty($filter_text) ? implode(' | ', $filter_text) : 'All Products';
                        ?>
                        <span class="text-muted fw-normal ms-2">(<?php echo count($products); ?> items)</span>
                    </h4>
                </div>
                <div class="d-flex align-items-center">
                    <div class="btn-group btn-group-sm me-3" role="group" aria-label="View options">
                        <button type="button" class="btn btn-outline-secondary active" id="gridViewBtn" data-view="grid">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="listViewBtn" data-view="list">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Sort By: 
                            <?php
                            $sort_options = [
                                'newest' => 'Newest',
                                'price_asc' => 'Price: Low to High',
                                'price_desc' => 'Price: High to Low',
                                'name_asc' => 'Name: A to Z',
                                'name_desc' => 'Name: Z to A'
                            ];
                            echo isset($_GET['sort']) && isset($sort_options[$_GET['sort']]) ? $sort_options[$_GET['sort']] : 'Newest';
                            ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="sortDropdown">
                            <?php foreach ($sort_options as $value => $label): ?>
                                <li>
                                    <a class="dropdown-item <?php echo (isset($_GET['sort']) && $_GET['sort'] == $value) ? 'active' : ''; ?>" 
                                       href="?<?php 
                                           $query = $_GET;
                                           $query['sort'] = $value;
                                           echo http_build_query($query);
                                       ?>">
                                        <?php echo $label; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($products)): ?>
                <!-- Grid View (Default) -->
                <div class="row g-4" id="productsGrid">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="card h-100 product-card border-0 shadow-sm rounded-3 position-relative">
                                <!-- Product Image -->
                                <div class="product-image-container" style="height: 200px; overflow: hidden;">
                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                         class="w-100 h-100" style="object-fit: cover;">
                                    
                                    <!-- Category Badge -->
                                    <?php if (!empty($product['category'])): ?>
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-primary bg-opacity-90 rounded-pill">
                                            <?php echo htmlspecialchars($product['category']); ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Product Info -->
                                <div class="card-body p-3">
                                    <h5 class="card-title mb-1 text-truncate" title="<?php echo htmlspecialchars($product['name']); ?>">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </h5>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="h5 mb-0 text-primary">
                                            K<?php echo number_format($product['price'], 2); ?>
                                        </div>
                                        <?php if ($product['is_organic']): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success">
                                                <i class="fas fa-leaf me-1"></i>Organic
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <p class="card-text text-muted small mb-3 line-clamp-2">
                                        <?php echo htmlspecialchars($product['description']); ?>
                                    </p>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small">
                                            <i class="fas fa-box me-1"></i> <?php echo $product['stock']; ?> in stock
                                        </span>
                                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if (isset($total_pages) && $total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php 
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $start_page + 4);
                        $start_page = max(1, $end_page - 4);
                        
                        if ($start_page > 1): ?>
                            <li class="page-item"><a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>">1</a></li>
                            <?php if ($start_page > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($end_page < $total_pages): ?>
                            <?php if ($end_page < $total_pages - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item"><a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $total_pages])); ?>"><?php echo $total_pages; ?></a></li>
                        <?php endif; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="text-center py-5">
                    <div class="py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No products found</h4>
                        <p class="text-muted">Try adjusting your search or filter criteria</p>
                        <a href="marketplace.php" class="btn btn-outline-primary mt-3">Clear all filters</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Recently Viewed Products -->
<?php if (isset($_SESSION['recently_viewed']) && count($_SESSION['recently_viewed']) > 0): ?>
<div class="bg-light py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">Recently Viewed</h3>
            <a href="#" class="text-decoration-none">View All <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            <?php 
            $recently_viewed = array_reverse(array_unique($_SESSION['recently_viewed']));
            $recent_products = array_filter($products, function($product) use ($recently_viewed) {
                return in_array($product['id'], $recently_viewed);
            });
            $recent_products = array_slice($recent_products, 0, 4);
            
            foreach ($recent_products as $product): 
            ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm rounded-3">
                        <div style="height: 120px; overflow: hidden;">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                 class="w-100 h-100" style="object-fit: cover;">
                        </div>
                        <div class="card-body p-3">
                            <h6 class="card-title mb-1">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </h6>
                            <p class="card-text text-primary fw-bold mb-0">
                                K<?php echo number_format($product['price'], 2); ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle between grid and list view
    const gridViewBtn = document.getElementById('gridViewBtn');
    const listViewBtn = document.getElementById('listViewBtn');
    const productsGrid = document.getElementById('productsGrid');
    
    if (gridViewBtn && listViewBtn && productsGrid) {
        gridViewBtn.addEventListener('click', function() {
            productsGrid.classList.remove('row-cols-1');
            productsGrid.classList.add('row-cols-1', 'row-cols-md-2', 'row-cols-lg-3', 'row-cols-xl-4');
            gridViewBtn.classList.add('active', 'btn-primary');
            gridViewBtn.classList.remove('btn-outline-secondary');
            listViewBtn.classList.remove('active', 'btn-primary');
            listViewBtn.classList.add('btn-outline-secondary');
        });
        
        listViewBtn.addEventListener('click', function() {
            productsGrid.classList.remove('row-cols-1', 'row-cols-md-2', 'row-cols-lg-3', 'row-cols-xl-4');
            productsGrid.classList.add('row-cols-1');
            listViewBtn.classList.add('active', 'btn-primary');
            listViewBtn.classList.remove('btn-outline-secondary');
            gridViewBtn.classList.remove('active', 'btn-primary');
            gridViewBtn.classList.add('btn-outline-secondary');
        });
    }
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Handle sort dropdown change
    const sortDropdown = document.getElementById('sort');
    if (sortDropdown) {
        sortDropdown.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', this.value);
            window.location.href = url.toString();
        });
    }
});
</script>
