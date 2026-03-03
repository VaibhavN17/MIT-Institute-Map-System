<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// If already logged in, maybe redirect to dashboard, or just let them see the public page.
// We'll just let them see the public page, but change Login to Dashboard.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> | Home</title>
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Sticky Navbar -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">MIT <span>IMS</span></a>
            
            <div class="mobile-toggle">
                <i class="fas fa-bars"></i>
            </div>

            <ul class="nav-links">
                <li><a href="#home" class="nav-item">Home</a></li>
                <li><a href="#about" class="nav-item">About</a></li>
                <li><a href="#vision" class="nav-item">Vision</a></li>
                <li><a href="#contact" class="nav-item">Contact</a></li>
                <li>
                    <?php if (isLoggedIn()): ?>
                        <a href="dashboard.php" class="login-btn">Dashboard</a>
                    <?php else: ?>
                        <a href="login.php" class="login-btn">Login</a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-content fade-in">
            <h1 class="hero-title">Welcome to MIT Institute Map System</h1>
            <p class="hero-subtitle">Navigate the campus with ease. Find departments, labs, and plan your shortest route across the university.</p>
            <a href="https://mityeola.com" target="_blank" class="btn btn-primary">Visit Official Website</a>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="section-padding">
        <div class="container">
            <div class="about-grid">
                <div class="about-img fade-in">
                    <!-- We use a placeholder image from Unsplash, or a solid color gradient if missing -->
                    <img src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Campus Image" style="width:100%; border-radius:10px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);">
                </div>
                <div class="about-text fade-in">
                    <h2 class="section-title" style="text-align: left; margin-bottom: 2rem;">About Our Campus</h2>
                    <p>Welcome to a sprawling campus that blends state-of-the-art infrastructure with lush green landscapes. Our institute has a legacy of excellence, providing students with world-class facilities to nurture innovation.</p>
                    <p>The Institute Map System is designed to help new students, faculty, and visitors effortlessly navigate our extensive grounds. From lecture halls to quiet study spots, the map is your guide.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Vision and Mission Section -->
    <section id="vision" class="vision-mission section-padding">
        <div class="container">
            <h2 class="section-title fade-in">Vision & Mission</h2>
            <div class="vm-grid">
                <div class="vm-card fade-in">
                    <div class="vm-icon"><i class="fas fa-eye"></i></div>
                    <h3>Our Vision</h3>
                    <p>To be an institute of academic excellence with total commitment to quality education and research, nurturing innovative leaders.</p>
                </div>
                <div class="vm-card fade-in" style="transition-delay: 0.2s;">
                    <div class="vm-icon"><i class="fas fa-rocket"></i></div>
                    <h3>Our Mission</h3>
                    <p>To impart education in a conducive environment, developing professionals with technological expertise and high ethical standards.</p>
                </div>
                <div class="vm-card fade-in" style="transition-delay: 0.4s;">
                    <div class="vm-icon"><i class="fas fa-bullseye"></i></div>
                    <h3>Our Goal</h3>
                    <p>To provide accessible, smart-campus tools that enhance the daily experience of all institute attendees and visitors.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Campus Highlights (Counters) -->
    <section class="highlights fade-in">
        <div class="container">
            <div class="counter-grid">
                <div class="counter-item">
                    <h3 class="counter" data-target="5000">0</h3>
                    <p>Students</p>
                </div>
                <div class="counter-item">
                    <h3 class="counter" data-target="15">0</h3>
                    <p>Departments</p>
                </div>
                <div class="counter-item">
                    <h3 class="counter" data-target="120">0</h3>
                    <p>Laboratories</p>
                </div>
                <div class="counter-item">
                    <h3 class="counter" data-target="350">0</h3>
                    <p>Faculty & Staff</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section-padding">
        <div class="container">
            <h2 class="section-title fade-in">Contact Us</h2>
            <div class="contact-grid">
                <div class="contact-info fade-in">
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt info-icon"></i>
                        <div>
                            <h4>Address</h4>
                            <p>123 University Avenue, Tech District, City 10001</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone-alt info-icon"></i>
                        <div>
                            <h4>Phone</h4>
                            <p>+1 (555) 123-4567</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-envelope info-icon"></i>
                        <div>
                            <h4>Email</h4>
                            <p>info@institute.edu</p>
                        </div>
                    </div>
                </div>
                <div class="map-container fade-in">
                    <!-- Embedded Google Map -->
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.1422937950147!2d-73.98731968459391!3d40.75889497932681!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25855c6480299%3A0x55194ec5a1ae072e!2sTimes%20Square!5e0!3m2!1sen!2sus!4v1683268412854!5m2!1sen!2sus" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Institute Map System. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
</body>
</html>
