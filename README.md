# MIT Institute Map System

Welcome to the **MIT Institute Map System**, a modern, animated, and interactive web application designed to help students, faculty, and visitors navigate the campus seamlessly.

## 🚀 Features

### 1. Public Portal
- **Modern Landing Page**: A visually stunning, responsive homepage with a sticky transparent-to-solid navbar and scroll-triggered fade animations.
- **Campus Highlights**: Animated counters displaying key statistics (Students, Departments, Labs, Faculty).
- **Vision & Mission**: Interactive cards with hover-elevation effects detailing the institute's goals.

### 2. Interactive Campus Map
- **SVG Map Integration**: Clickable, customizable SVG-based campus map.
- **Dynamic Tooltips**: Hover over campus buildings to see specific details and descriptions.
- **Pan & Zoom Controls**: Easily explore the map with mouse-wheel zoom and drag-to-pan functionality.

### 3. Route Navigation Engine
- **Shortest Path Calculation**: Uses **Dijkstra's Algorithm** (implemented purely in PHP) to calculate the shortest mathematical route between any two buildings.
- **Animated Directions**: Smoothly animates the calculated path drawing directly on the SVG map interface.

### 4. Admin Dashboard & Authentication
- **Secure Login**: Modern login UI with floating label inputs and password visibility toggles, backed by encrypted PHP sessions (`password_verify`).
- **Collapsible Sidebar**: Sleek dashboard layout tailored, responsive, and easy to navigate.
- **CRUD Operations**: Admin panel to add/delete buildings, define new paths/edges, and update the campus SVG diagram.

---

## 🛠️ Technology Stack
- **Frontend**: HTML5, Vanilla CSS3 (Custom properties, micro-animations, Flexbox/Grid), Vanilla JavaScript (Intersection APIs, DOM manipulation).
- **Backend**: Core PHP (REST-like structure, PDO Database connections).
- **Database**: MySQL over localhost (`institute_map_system`).

---

## ⚙️ Installation & Setup

Follow these steps to get the project up and running locally.

### Prerequisites
- A local server environment like XAMPP, WAMP, or MAMP installed on your machine.
- PHP >= 7.4
- MySQL

### Step 1: Clone the Repository
Clone the project into your local server's document root (e.g., `C:\xampp\htdocs\` or `/var/www/html/`).

```bash
git clone https://github.com/[Your-Username]/MIT-Institute-Map-System.git
cd MIT-Institute-Map-System
```

### Step 2: Database Setup
1. Open **phpMyAdmin** (usually `http://localhost/phpmyadmin`).
2. Create a new database named `institute_map_system`.
3. Import the `database/schema.sql` file provided in this repository to create the required tables and dummy data.

### Step 3: Run the Application
Start your Apache and MySQL servers. Open your browser and navigate to the project directory:

```
http://localhost/MIT-Institute-Map-System/
```
*(Note: Modify the URL depending on your folder name inside `htdocs`)*

### Default Admin Credentials
To access the Dashboard and Admin Panel, use the following credentials on the login page:
- **Username:** `admin`
- **Password:** `admin123`

---

## 📂 Folder Structure

```text
├── api/
│   └── get_route.php         # Dijkstra's Algorithm implementation
├── assets/
│   ├── css/style.css         # Global Styles & Animations
│   └── js/
│       ├── main.js           # Scroll behaviors, layout toggles
│       ├── map.js            # SVG Pan/Zoom interaction logic
│       └── routing.js        # API Fetch and SVG animation handling
├── database/
│   └── schema.sql            # MySQL Database schema & mock data
├── includes/
│   ├── auth.php              # Session handling & authentication
│   ├── config.php            # App and DB constants
│   └── db.php                # PDO Connection wrapper
├── index.php                 # Public Landing Page
├── login.php                 # Authentication UI
├── dashboard.php             # Main Dashboard Wrapper/Layout
├── map.php                   # SVG Map layout module
├── route_navigation.php      # Pathfinding UI module
├── admin.php                 # Admin Settings UI module
└── logout.php                # Session destroyer
```

---

## 📜 License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.