    </main>

    <!-- Modern Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 footer-col">
                    <a href="index.php" class="footer-logo">
                        <i class="fas fa-leaf"></i>AgroSmart Market
                    </a>
                    <p>Connecting farmers directly with buyers across Zambia - making agricultural commerce simple, efficient, and profitable for everyone.</p>
                    <div class="social-icons">
                        <a href="#" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" title="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 footer-col">
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="index.php"><i class="fas fa-chevron-right"></i> Home</a></li>
                        <li><a href="marketplace.php"><i class="fas fa-chevron-right"></i> Marketplace</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> About Us</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Contact</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Blog</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 footer-col">
                    <h4>Farming Resources</h4>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Crop Calendar</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Farming Tips</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Market Prices</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Weather Updates</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Agricultural Loans</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 footer-col">
                    <h4>Contact Us</h4>
                    <ul class="contact-info">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>123 Farm Road, Lusaka, Zambia</span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <span>+260 97 123 4567</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span>info@agrosmartmarket.com</span>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <span>Monday - Friday: 8:00 AM - 5:00 PM</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="row">
                    <div class="col-md-6">
                        <p>&copy; <?php echo date('Y'); ?> AgroSmart Market. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p>
                            <a href="#" class="me-3">Privacy Policy</a>
                            <a href="#">Terms of Service</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (for additional functionality) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="public/js/main.js"></script>
    
    <!-- Vanilla Tilt for 3D hover effects -->
    <script src="public/js/vanilla-tilt.min.js"></script>
    
    <!-- Custom Scripts -->
    <script src="public/js/animations.js"></script>
    
    <!-- Initialize AOS animations -->
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });
        
        // Initialize custom tilt effects if vanilla-tilt.js is available
        if (typeof VanillaTilt !== 'undefined') {
            VanillaTilt.init(document.querySelectorAll('.tilt-card'), {
                max: 10,
                speed: 400,
                glare: true,
                'max-glare': 0.3
            });
        }
    </script>
    <script>
        // Enable Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Enhanced Hero Section Parallax Effect
        const heroSection = document.querySelector('.hero');
        if (heroSection) {
            window.addEventListener('scroll', function() {
                let scrollPosition = window.scrollY;
                if (scrollPosition < heroSection.offsetHeight) {
                    const bgImage = heroSection.querySelector('.hero-bg-image');
                    if (bgImage) {
                        bgImage.style.transform = `scale(1.05) translateY(${scrollPosition * 0.15}px)`;
                    }
                }
            });
        }
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
        
        // Add animation class to navbar on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (navbar) {
                if (window.scrollY > 50) {
                    navbar.classList.add('navbar-scrolled');
                } else {
                    navbar.classList.remove('navbar-scrolled');
                }
            }
        });
        
        // Image preview for product uploads
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result).show();
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
