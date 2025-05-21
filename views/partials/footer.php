    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h4><i class="fas fa-leaf me-2"></i>AgroSmart Market</h4>
                    <p>Connecting farmers directly with buyers - making agricultural commerce simple, efficient, and profitable for everyone.</p>
                </div>
                <div class="col-md-4">
                    <h4>Quick Links</h4>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white">Home</a></li>
                        <li><a href="marketplace.php" class="text-white">Marketplace</a></li>
                        <li><a href="#" class="text-white">About Us</a></li>
                        <li><a href="#" class="text-white">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h4>Contact Us</h4>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-map-marker-alt me-2"></i>123 Farm Road, Agriville</li>
                        <li><i class="fas fa-phone me-2"></i>(123) 456-7890</li>
                        <li><i class="fas fa-envelope me-2"></i>info@agrosmartmarket.com</li>
                    </ul>
                    <div class="social-icons mt-3">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <hr class="mt-4 mb-4" style="background-color: rgba(255,255,255,0.2);">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> AgroSmart Market. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        <a href="#" class="text-white me-3">Privacy Policy</a>
                        <a href="#" class="text-white">Terms of Service</a>
                    </p>
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
    
    <!-- Custom Scripts -->
    <script>
        // Initialize AOS animations
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });
        
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
