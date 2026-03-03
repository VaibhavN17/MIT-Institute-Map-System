// assets/js/main.js

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Sticky Navbar
    const navbar = document.querySelector('.navbar');
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // 2. Mobile Menu Toggle
    // (Assuming we add an element with .mobile-toggle and .nav-links)
    const mobileToggle = document.querySelector('.mobile-toggle');
    const navLinks = document.querySelector('.nav-links');
    
    if (mobileToggle) {
        mobileToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            // Change icon from bars to times or vice versa if using fontawesome
        });
    }

    // 3. Scroll Animations (Fade-in on scroll)
    const fadeElements = document.querySelectorAll('.fade-in');
    
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.15
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target); // Only animate once
            }
        });
    }, observerOptions);

    fadeElements.forEach(el => {
        observer.observe(el);
    });

    // 4. Animated Counters
    const counters = document.querySelectorAll('.counter');
    let hasCounted = false;

    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !hasCounted) {
                counters.forEach(counter => {
                    const updateCount = () => {
                        const target = +counter.getAttribute('data-target');
                        const count = +counter.innerText;
                        
                        // Lower inc to slow and higher to speed up
                        const inc = target / 50;
                        
                        if (count < target) {
                            counter.innerText = Math.ceil(count + inc);
                            setTimeout(updateCount, 30);
                        } else {
                            counter.innerText = target;
                        }
                    };
                    updateCount();
                });
                hasCounted = true;
            }
        });
    }, observerOptions);

    const highlightsSection = document.querySelector('.highlights');
    if (highlightsSection) {
        counterObserver.observe(highlightsSection);
    }
});
