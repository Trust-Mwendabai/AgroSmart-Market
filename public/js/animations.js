/**
 * AgroSmart Market - Custom Animations
 * Advanced animations and interactive effects for the landing page
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize scroll-triggered animations
    initScrollAnimations();
    
    // Initialize counting animations
    initCounters();
    
    // Initialize hover effects
    initHoverEffects();
    
    // Initialize background animations
    initBackgroundAnimations();
    
    // Initialize testimonial carousel
    initTestimonialCarousel();
});

/**
 * Initialize scroll-triggered animations using Intersection Observer
 */
function initScrollAnimations() {
    // Elements to animate on scroll
    const animatedElements = document.querySelectorAll('.scroll-animate');
    
    if (animatedElements.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const delay = el.dataset.delay || 0;
                    const animation = el.dataset.animation || 'fadeIn';
                    
                    setTimeout(() => {
                        el.classList.add('animate__animated', `animate__${animation}`);
                        el.style.opacity = 1;
                    }, delay);
                    
                    // Unobserve after animation
                    observer.unobserve(el);
                }
            });
        }, {
            threshold: 0.2
        });
        
        animatedElements.forEach(el => {
            el.style.opacity = 0;
            observer.observe(el);
        });
    }
}

/**
 * Initialize animated counters for statistics
 */
function initCounters() {
    const counters = document.querySelectorAll('.counter-animate');
    
    if (counters.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;
                    const target = parseInt(counter.dataset.count);
                    const duration = parseInt(counter.dataset.duration) || 2000;
                    let start = 0;
                    const step = timestamp => {
                        if (!start) start = timestamp;
                        const progress = Math.min((timestamp - start) / duration, 1);
                        const currentCount = Math.floor(progress * target);
                        counter.innerText = currentCount;
                        
                        if (progress < 1) {
                            window.requestAnimationFrame(step);
                        } else {
                            counter.innerText = target;
                            // Add the plus symbol if specified
                            if (counter.dataset.suffix) {
                                counter.innerText += counter.dataset.suffix;
                            }
                        }
                    };
                    
                    window.requestAnimationFrame(step);
                    observer.unobserve(counter);
                }
            });
        }, {
            threshold: 0.5
        });
        
        counters.forEach(counter => {
            observer.observe(counter);
        });
    }
}

/**
 * Initialize hover effects for interactive elements
 */
function initHoverEffects() {
    // Card hover effects
    const hoverCards = document.querySelectorAll('.hover-effect');
    
    hoverCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.classList.add('card-hover-active');
        });
        
        card.addEventListener('mouseleave', function() {
            this.classList.remove('card-hover-active');
        });
    });
    
    // Button hover effects
    const hoverButtons = document.querySelectorAll('.btn-hover-effect');
    
    hoverButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.classList.add('btn-pulse');
        });
        
        button.addEventListener('mouseleave', function() {
            this.classList.remove('btn-pulse');
        });
    });
    
    // Feature hover effects
    const featureItems = document.querySelectorAll('.feature-item');
    
    featureItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.querySelector('.feature-icon').classList.add('animate__animated', 'animate__heartBeat');
        });
        
        item.addEventListener('mouseleave', function() {
            const icon = this.querySelector('.feature-icon');
            icon.classList.remove('animate__animated', 'animate__heartBeat');
        });
    });
}

/**
 * Initialize background animations for visual appeal
 */
function initBackgroundAnimations() {
    // Create animated particles
    const particleContainers = document.querySelectorAll('.particles-bg');
    
    particleContainers.forEach(container => {
        const particleCount = 20;
        
        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.classList.add('particle');
            
            // Random position
            const posX = Math.random() * 100;
            const posY = Math.random() * 100;
            
            // Random size
            const size = Math.random() * 10 + 5;
            
            // Random animation duration
            const duration = Math.random() * 20 + 10;
            
            // Random opacity
            const opacity = Math.random() * 0.5 + 0.1;
            
            // Apply styles
            particle.style.left = `${posX}%`;
            particle.style.top = `${posY}%`;
            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;
            particle.style.opacity = opacity;
            particle.style.animationDuration = `${duration}s`;
            particle.style.animationDelay = `${Math.random() * 5}s`;
            
            container.appendChild(particle);
        }
    });
    
    // Parallax effect on scroll
    window.addEventListener('scroll', function() {
        const parallaxElements = document.querySelectorAll('.parallax');
        const scrollPosition = window.pageYOffset;
        
        parallaxElements.forEach(element => {
            const speed = element.dataset.speed || 0.5;
            element.style.transform = `translateY(${scrollPosition * speed}px)`;
        });
    });
}

/**
 * Initialize testimonial carousel with automatic sliding
 */
function initTestimonialCarousel() {
    const testimonialIndicators = document.querySelectorAll('.testimonial-indicator');
    let currentSlide = 0;
    const slideCount = testimonialIndicators.length;
    
    if (slideCount > 0) {
        // Change slide every 5 seconds
        setInterval(() => {
            // Remove active class from current indicator
            testimonialIndicators[currentSlide].classList.remove('active');
            
            // Update current slide
            currentSlide = (currentSlide + 1) % slideCount;
            
            // Add active class to new indicator
            testimonialIndicators[currentSlide].classList.add('active');
            
            // Update testimonial cards (if we had multiple sets)
            updateTestimonialVisibility(currentSlide);
        }, 5000);
        
        // Add click event to indicators
        testimonialIndicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                // Remove active class from all indicators
                testimonialIndicators.forEach(ind => ind.classList.remove('active'));
                
                // Add active class to clicked indicator
                indicator.classList.add('active');
                
                // Update current slide
                currentSlide = index;
                
                // Update testimonial cards
                updateTestimonialVisibility(currentSlide);
            });
        });
    }
}

/**
 * Update testimonial visibility based on selected slide
 */
function updateTestimonialVisibility(slideIndex) {
    // This would be used if we had multiple sets of testimonials to cycle through
    // For now, it's just a placeholder for future enhancement
}

/**
 * Create animated typing effect for specified elements
 */
function createTypingEffect(element, text, speed = 50) {
    let i = 0;
    element.innerHTML = '';
    
    function type() {
        if (i < text.length) {
            element.innerHTML += text.charAt(i);
            i++;
            setTimeout(type, speed);
        }
    }
    
    type();
}

// Initialize cursor effects for interactive elements
document.addEventListener('mousemove', function(e) {
    const customCursors = document.querySelectorAll('.custom-cursor-area');
    
    customCursors.forEach(area => {
        const cursor = area.querySelector('.custom-cursor');
        if (cursor) {
            // Check if mouse is inside the area
            const rect = area.getBoundingClientRect();
            if (
                e.clientX >= rect.left &&
                e.clientX <= rect.right &&
                e.clientY >= rect.top &&
                e.clientY <= rect.bottom
            ) {
                cursor.style.opacity = 1;
                cursor.style.transform = `translate(${e.clientX - rect.left - 10}px, ${e.clientY - rect.top - 10}px)`;
            } else {
                cursor.style.opacity = 0;
            }
        }
    });
});
