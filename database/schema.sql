-- database/schema.sql

-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS institute_map_system;
USE institute_map_system;

-- Table for Users (Admin and potentially others)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert a default admin user (password: admin123)
-- Hash generated using PHP's password_hash('admin123', PASSWORD_DEFAULT)
INSERT IGNORE INTO users (username, password_hash, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Table for Buildings/Locations on the map
CREATE TABLE IF NOT EXISTS buildings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    svg_id VARCHAR(50) NOT NULL UNIQUE COMMENT 'ID of the element in the SVG map',
    type VARCHAR(50) DEFAULT 'building' COMMENT 'e.g., department, lab, cafeteria',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert some dummy buildings (we'll need matching IDs in our SVG later)
INSERT IGNORE INTO buildings (name, description, svg_id, type) VALUES
('Main Administration', 'Administrative offices and Registrar', 'bldg-admin', 'administration'),
('Computer Science Dept', 'CS Department, Labs and Faculty offices', 'bldg-cs', 'department'),
('Central Library', 'Main campus library spanning 3 floors', 'bldg-library', 'facility'),
('Student Center', 'Cafeteria, recreation, and student union', 'bldg-student-center', 'facility');

-- Table for Paths between nodes (for routing calculations)
-- Nodes can be buildings or just intersection points (waypoints) on paths
CREATE TABLE IF NOT EXISTS nodes (
    id VARCHAR(50) PRIMARY KEY COMMENT 'Can match building svg_id or be a custom waypoint ID',
    name VARCHAR(100),
    x_coord FLOAT,
    y_coord FLOAT
);

INSERT IGNORE INTO nodes (id, name, x_coord, y_coord) VALUES
('bldg-admin', 'Main Administration', 100, 100),
('bldg-cs', 'Computer Science Dept', 300, 150),
('bldg-library', 'Central Library', 200, 300),
('bldg-student-center', 'Student Center', 400, 250),
('wp-1', 'Main Intersection', 200, 150),
('wp-2', 'Library Intersection', 250, 250);

CREATE TABLE IF NOT EXISTS edges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    source_node VARCHAR(50) NOT NULL,
    target_node VARCHAR(50) NOT NULL,
    distance FLOAT NOT NULL,
    FOREIGN KEY (source_node) REFERENCES nodes(id) ON DELETE CASCADE,
    FOREIGN KEY (target_node) REFERENCES nodes(id) ON DELETE CASCADE
);

-- Insert paths (undirected graph, so normally we insert both ways or handle in code)
-- We will handle bidirectional links in our Dijkstra algorithm later.
INSERT IGNORE INTO edges (source_node, target_node, distance) VALUES
('bldg-admin', 'wp-1', 100),
('wp-1', 'bldg-cs', 100),
('wp-1', 'wp-2', 111.8),
('wp-2', 'bldg-library', 70.71),
('wp-2', 'bldg-student-center', 150);

