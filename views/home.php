<!-- Hero Section with Parallax Effect -->
<section class="hero position-relative overflow-hidden">
    <!-- Using CSS for the background instead of relying on the image -->
    <div class="hero-bg-image"></div>
    <div class="container position-relative z-index-1 h-100 d-flex align-items-center">
        <div class="row align-items-center py-5">
            <div class="col-lg-6" data-aos="fade-right">
                <h1 class="display-3 fw-bold text-white mb-3 hero-animated">Farm Fresh,<br>Direct to You</h1>
                <p class="lead text-white mb-4 hero-animated">AgroSmart Market connects farmers directly with buyers, eliminating middlemen and creating better prices for everyone.</p>
                <div class="d-flex flex-wrap gap-3 hero-animated">
                    <a href="auth.php?action=register" class="btn btn-primary btn-lg px-4 py-3">
                        <i class="fas fa-user-plus me-2"></i>Join as Farmer
                    </a>
                    <a href="marketplace.php" class="btn btn-light btn-lg px-4 py-3">
                        <i class="fas fa-shopping-basket me-2"></i>Start Shopping
                    </a>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block" data-aos="fade-left" data-aos-delay="200">
                <div class="hero-image img-fluid">
                    <!-- CSS Placeholder will display here -->
                </div>
            </div>
        </div>
    </div>
    <div class="hero-overlay"></div>
    <div class="hero-wave">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#ffffff" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,154.7C384,149,480,107,576,117.3C672,128,768,192,864,202.7C960,213,1056,171,1152,149.3C1248,128,1344,128,1392,128L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-5">
    <div class="container py-4">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6 text-center">
                <h2 class="fw-bold mb-3" data-aos="fade-up">How It Works</h2>
                <p class="text-muted" data-aos="fade-up" data-aos-delay="100">Simple steps to connect farmers with buyers and create a sustainable agricultural marketplace</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-box text-center h-100">
                    <div class="feature-icon mx-auto">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3 class="h4 mb-3">Create an Account</h3>
                    <p class="text-muted mb-0">Register as a farmer to sell your products or as a buyer to purchase fresh produce directly from farms.</p>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-box text-center h-100">
                    <div class="feature-icon mx-auto">
                        <i class="fas fa-store"></i>
                    </div>
                    <h3 class="h4 mb-3">List or Shop</h3>
                    <p class="text-muted mb-0">Farmers can list their products with details and photos. Buyers can browse, search, and filter to find what they need.</p>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-box text-center h-100">
                    <div class="feature-icon mx-auto">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3 class="h4 mb-3">Connect & Trade</h3>
                    <p class="text-muted mb-0">Communicate directly through our platform, arrange orders, and build relationships with local farmers.</p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5" data-aos="fade-up" data-aos-delay="400">
            <a href="marketplace.php" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-basket me-2"></i>Explore the Marketplace
            </a>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="row justify-content-center mb-2">
            <div class="col-lg-6 text-center">
                <h2 class="fw-bold mb-3" data-aos="fade-up">Featured Products</h2>
                <p class="text-muted mb-5" data-aos="fade-up" data-aos-delay="100">Explore our handpicked selection of fresh farm produce</p>
            </div>
        </div>
        
        <div class="row g-4 mb-4">
            <?php if (!empty($latest_products)): ?>
                <?php foreach ($latest_products as $index => $product): ?>
                    <div class="col-md-6 col-lg-3 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                        <div class="product-card h-100">
                            <div class="img-container">
                                <div class="product-image">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="public/uploads/products/<?php echo $product['image']; ?>" class="product-img" alt="<?php echo $product['name']; ?>" onerror="this.style.display='none';this.parentNode.classList.add('category-<?php echo strtolower(str_replace(' ', '_', $product['category'])); ?>')">
                                    <?php else: ?>
                                        <!-- CSS-based placeholder will show when no image is available -->
                                        <div class="category-<?php echo strtolower(str_replace(' ', '_', $product['category'])); ?> img-placeholder" style="height:200px;">
                                            <?php echo $product['name']; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
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
            <?php else: ?>
                <div class="col-12" data-aos="fade-up">
                    <div class="alert alert-info">
                        <h4 class="alert-heading">No products available yet!</h4>
                        <p>Check back soon as farmers add their fresh produce to our marketplace.</p>
                        <hr>
                        <p class="mb-0">Are you a farmer? <a href="auth.php?action=register" class="alert-link">Register now</a> to start selling your products!</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4" data-aos="fade-up">
            <a href="marketplace.php" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-store me-2"></i>Browse All Products
            </a>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5 bg-white testimonials-section">
    <div class="container py-4">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6 text-center">
                <h2 class="fw-bold mb-3" data-aos="fade-up">What Our Users Say</h2>
                <p class="text-muted" data-aos="fade-up" data-aos-delay="100">Hear from the farmers and buyers who are part of our growing community</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="testimonial-card">
                    <div class="testimonial-quote"><i class="fas fa-quote-left"></i></div>
                    <div class="testimonial-content">
                        <div class="mb-3 testimonial-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"AgroSmart Market has completely changed how I sell my produce. I'm earning more and building relationships with repeat customers."</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="https://randomuser.me/api/portraits/men/41.jpg" class="testimonial-avatar" alt="John Mutale">
                        <div class="testimonial-info">
                            <h5 class="testimonial-name">John Mutale</h5>
                            <p class="testimonial-role">Vegetable Farmer</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="testimonial-card">
                    <div class="testimonial-quote"><i class="fas fa-quote-left"></i></div>
                    <div class="testimonial-content">
                        <div class="mb-3 testimonial-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"I love knowing exactly where my food comes from. The platform makes it easy to find local farmers and support sustainable agriculture."</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" class="testimonial-avatar" alt="Mary Banda">
                        <div class="testimonial-info">
                            <h5 class="testimonial-name">Mary Banda</h5>
                            <p class="testimonial-role">Restaurant Owner</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="testimonial-card">
                    <div class="testimonial-quote"><i class="fas fa-quote-left"></i></div>
                    <div class="testimonial-content">
                        <div class="mb-3 testimonial-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <p class="testimonial-text">"I've expanded my market reach significantly through this platform. The ordering system is efficient and the communication tools are excellent."</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" class="testimonial-avatar" alt="David Tembo">
                        <div class="testimonial-info">
                            <h5 class="testimonial-name">David Tembo</h5>
                            <p class="testimonial-role">Fruit Grower</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5" data-aos="fade-up" data-aos-delay="300">
            <a href="testimonials.php" class="btn btn-outline-primary">
                <i class="fas fa-comments me-2"></i>Read More Stories
            </a>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="py-5 bg-white about-section">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="about-image-wrapper position-relative">
                    <!-- No image required - using CSS placeholder -->
                    <div class="about-image-accent" data-aos="fade-up" data-aos-delay="300"></div>
                    <div class="about-stats-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <h3 class="stats-number">500+</h3>
                                <p class="stats-text">Farmers Joined</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="ps-lg-4">
                    <span class="badge bg-primary px-3 py-2 mb-3">Our Story</span>
                    <h2 class="fw-bold mb-4">About AgroSmart Market</h2>
                    <p class="lead text-muted mb-4">We're revolutionizing agricultural commerce.</p>
                    
                    <div class="mb-4">
                        <p>AgroSmart Market was created to solve the challenges faced by small and medium-scale farmers who struggle to find reliable markets for their produce. Our platform eliminates middlemen, reduces waste, and ensures fair prices for both farmers and buyers.</p>
                        <p>By connecting farmers directly with consumers, restaurants, and retailers, we're building a more sustainable and transparent food system.</p>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="about-feature">
                                <i class="fas fa-leaf"></i>
                                <h5>Sustainable Farming</h5>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="about-feature">
                                <i class="fas fa-hand-holding-usd"></i>
                                <h5>Fair Pricing</h5>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="about-feature">
                                <i class="fas fa-truck"></i>
                                <h5>Direct Delivery</h5>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="about-feature">
                                <i class="fas fa-seedling"></i>
                                <h5>Fresh Produce</h5>
                            </div>
                        </div>
                    </div>
                    
                    <a href="about.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-info-circle me-2"></i>Learn More About Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary text-white position-relative overflow-hidden cta-section">
    <!-- Using CSS for background instead of image -->
    <div class="position-absolute top-0 start-0 w-100 h-100 cta-bg-overlay"></div>
    <div class="container py-5 position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center" data-aos="fade-up">
                <h2 class="display-5 fw-bold mb-4">Ready to Join Our Growing Community?</h2>
                <p class="lead mb-4">Create an account today and start buying or selling fresh farm products directly.</p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="auth.php?action=register&type=farmer" class="btn btn-light btn-lg px-4 py-3">
                        <i class="fas fa-tractor me-2"></i>Join as a Farmer
                    </a>
                    <a href="auth.php?action=register&type=buyer" class="btn btn-outline-light btn-lg px-4 py-3">
                        <i class="fas fa-shopping-basket me-2"></i>Join as a Buyer
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Floating Elements Animation -->
    <div class="floating-elements">
        <div class="floating-element" style="top: 20%; left: 10%;"><i class="fas fa-apple-alt"></i></div>
        <div class="floating-element" style="top: 70%; left: 15%;"><i class="fas fa-carrot"></i></div>
        <div class="floating-element" style="top: 30%; right: 10%;"><i class="fas fa-lemon"></i></div>
        <div class="floating-element" style="top: 80%; right: 15%;"><i class="fas fa-pepper-hot"></i></div>
    </div>
</section>

<!-- Scroll to Top Button -->
<div id="scroll-to-top" class="scroll-to-top">
    <i class="fas fa-arrow-up"></i>
</div>
