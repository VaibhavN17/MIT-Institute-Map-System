<?php
// dashboard.php
require_once 'includes/config.php';
require_once 'includes/auth.php';

requireLogin();

$page = $_GET['page'] ?? 'home';
$username = htmlspecialchars($_SESSION['username']);
$role = htmlspecialchars($_SESSION['role']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> | Dashboard</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
            --topbar-height: 60px;
            --primary: #0F172A;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --accent: #06B6D4;
            --bg: #F8FAFC;
            --text-main: #334155;
            --text-light: #94A3B8;
            --white: #ffffff;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            color: var(--white);
            transition: var(--transition);
            z-index: 1000;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-header {
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            padding: 0 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo-text {
            font-family: 'Poppins', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            margin-left: 10px;
            white-space: nowrap;
            transition: opacity 0.3s;
        }

        .logo-text span {
            color: var(--accent);
        }

        .sidebar.collapsed .logo-text {
            opacity: 0;
            pointer-events: none;
            display: none;
        }

        .toggle-btn {
            font-size: 1.2rem;
            cursor: pointer;
            color: var(--text-light);
            transition: color 0.3s;
            margin-left: auto;
        }
        
        .sidebar.collapsed .toggle-btn {
            margin: 0 auto;
        }

        .toggle-btn:hover {
            color: var(--white);
        }

        .sidebar-menu {
            flex-grow: 1;
            padding: 1rem 0;
            overflow-y: auto;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-menu::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--text-light);
            text-decoration: none;
            transition: var(--transition);
            border-left: 3px solid transparent;
            margin-bottom: 0.25rem;
            white-space: nowrap;
        }

        .menu-item:hover, .menu-item.active {
            background-color: var(--sidebar-hover);
            color: var(--white);
            border-left-color: var(--accent);
        }

        .menu-item.active {
            background-color: rgba(6, 182, 212, 0.1);
        }

        .menu-icon {
            min-width: 40px;
            font-size: 1.1rem;
            text-align: center;
        }

        .menu-text {
            transition: opacity 0.3s;
        }

        .sidebar.collapsed .menu-text {
            opacity: 0;
            pointer-events: none;
            display: none;
        }

        /* Tooltip behavior when collapsed */
        .sidebar.collapsed .menu-item {
            position: relative;
        }
        
        .sidebar.collapsed .menu-item:hover::after {
            content: attr(data-title);
            position: absolute;
            left: calc(var(--sidebar-collapsed-width) + 10px);
            background: var(--primary);
            color: var(--white);
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            font-size: 0.85rem;
            white-space: nowrap;
            box-shadow: var(--shadow);
            z-index: 1001;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* Main Content wrapper */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .sidebar.collapsed ~ .main-wrapper {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Top Navbar */
        .topbar {
            height: var(--topbar-height);
            background-color: var(--white);
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .page-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--primary);
            font-size: 1.1rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--primary);
        }

        .user-role {
            font-size: 0.8rem;
            color: var(--text-light);
            text-transform: capitalize;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--accent);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

        /* Main Content Area */
        .content-area {
            padding: 2rem;
            flex-grow: 1;
            animation: fadeUp 0.5s ease;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Card styles for general dashboard content */
        .dashboard-card {
            background: var(--white);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.mobile-open {
                transform: translateX(0);
                width: var(--sidebar-width);
            }
            .main-wrapper {
                margin-left: 0 !important;
            }
            .mobile-toggle-btn {
                display: block !important;
                margin-right: 15px;
                cursor: pointer;
            }
            .desktop-toggle-btn {
                display: none;
            }
        }

        .mobile-toggle-btn {
            display: none;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .stat-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: rgba(6, 182, 212, 0.1);
            color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .stat-info h4 {
            color: var(--text-light);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .stat-info h2 {
            color: var(--primary);
            font-size: 1.8rem;
            margin-top: 0.2rem;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-bars desktop-toggle-btn toggle-btn" id="toggleBtn"></i>
            <div class="logo-text">MIT <span>IMS</span></div>
        </div>
        
        <div class="sidebar-menu">
            <a href="dashboard.php?page=home" class="menu-item <?php echo $page === 'home' ? 'active' : ''; ?>" data-title="Dashboard">
                <div class="menu-icon"><i class="fas fa-home"></i></div>
                <div class="menu-text">Dashboard</div>
            </a>
            <a href="dashboard.php?page=map" class="menu-item <?php echo $page === 'map' ? 'active' : ''; ?>" data-title="Campus Map">
                <div class="menu-icon"><i class="fas fa-map-marked-alt"></i></div>
                <div class="menu-text">Campus Map</div>
            </a>
            <a href="dashboard.php?page=route" class="menu-item <?php echo $page === 'route' ? 'active' : ''; ?>" data-title="Find Route">
                <div class="menu-icon"><i class="fas fa-directions"></i></div>
                <div class="menu-text">Find Route</div>
            </a>
            
            <?php if ($role === 'admin'): ?>
            <a href="dashboard.php?page=admin" class="menu-item <?php echo $page === 'admin' ? 'active' : ''; ?>" data-title="Admin Panel">
                <div class="menu-icon"><i class="fas fa-tools"></i></div>
                <div class="menu-text">Admin Panel</div>
            </a>
            <?php endif; ?>

            <a href="logout.php" class="menu-item" data-title="Logout">
                <div class="menu-icon"><i class="fas fa-sign-out-alt"></i></div>
                <div class="menu-text">Logout</div>
            </a>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="main-wrapper">
        <!-- Top Navigation -->
        <header class="topbar">
            <div style="display: flex; align-items: center;">
                <i class="fas fa-bars mobile-toggle-btn" id="mobileToggleBtn"></i>
                <div class="page-title">
                    <?php 
                        $titles = [
                            'home' => 'Dashboard Overview',
                            'map' => 'Interactive Campus Map',
                            'route' => 'Route Navigation',
                            'admin' => 'Administrator Panel'
                        ];
                        echo $titles[$page] ?? 'Dashboard';
                    ?>
                </div>
            </div>

            <div class="user-profile">
                <div class="user-info">
                    <div class="user-name"><?php echo $username; ?></div>
                    <div class="user-role"><?php echo $role; ?></div>
                </div>
                <div class="avatar">
                    <?php echo strtoupper(substr($username, 0, 1)); ?>
                </div>
            </div>
        </header>

        <!-- Dynamic Content Area -->
        <main class="content-area">
            <?php
                $allowed_pages = ['home', 'map', 'route', 'admin'];
                $page = in_array($page, $allowed_pages) ? $page : 'home';

                if ($page === 'home') {
                    // Include default dashboard overview directly
                    echo '
                    <div class="stat-grid">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-building"></i></div>
                            <div class="stat-info">
                                <h4>Total Facilities</h4>
                                <h2>120+</h2>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="color: #10b981; background: rgba(16, 185, 129, 0.1);"><i class="fas fa-route"></i></div>
                            <div class="stat-info">
                                <h4>Mapped Paths</h4>
                                <h2>350</h2>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="color: #f59e0b; background: rgba(245, 158, 11, 0.1);"><i class="fas fa-map-pin"></i></div>
                            <div class="stat-info">
                                <h4>Saved Locations</h4>
                                <h2>15</h2>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card" style="margin-top: 2rem;">
                        <h3 style="margin-bottom: 1rem; color: var(--primary);">Welcome to the new Institute Map System</h3>
                        <p style="color: var(--text-light); line-height: 1.6;">
                            Navigate our extensive campus with ease. Use the sidebar to explore the interactive campus map, find the shortest routes between buildings, or manage your profile. Start by exploring the <a href="dashboard.php?page=map" style="color: var(--accent); font-weight: 500;">Campus Map</a>.
                        </p>
                    </div>
                    ';
                } else if ($page === 'map') {
                    include 'map.php';
                } else if ($page === 'route') {
                    include 'route_navigation.php';
                } else if ($page === 'admin' && $role === 'admin') {
                    include 'admin.php';
                } else {
                    echo "<p>Page not found or unauthorized.</p>";
                }
            ?>
        </main>
    </div>

    <script>
        // Sidebar Toggle Logic
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleBtn');
        const mobileToggleBtn = document.getElementById('mobileToggleBtn');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
        });

        mobileToggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('mobile-open');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && 
                !sidebar.contains(e.target) && 
                e.target !== mobileToggleBtn) {
                sidebar.classList.remove('mobile-open');
            }
        });
    </script>
</body>
</html>
