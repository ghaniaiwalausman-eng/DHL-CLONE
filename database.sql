-- sql/database.sql
CREATE DATABASE IF NOT EXISTS dhl_clone;
USE dhl_clone;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Shipments table
CREATE TABLE IF NOT EXISTS shipments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tracking_number VARCHAR(50) UNIQUE NOT NULL,
    user_id INT,
    status VARCHAR(50) DEFAULT 'Processing',
    origin VARCHAR(255) NOT NULL,
    destination VARCHAR(255) NOT NULL,
    current_location VARCHAR(255),
    weight DECIMAL(10,2),
    estimated_delivery DATE,
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Tracking history table
CREATE TABLE IF NOT EXISTS tracking_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shipment_id INT NOT NULL,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    location VARCHAR(255) NOT NULL,
    status VARCHAR(100) NOT NULL,
    FOREIGN KEY (shipment_id) REFERENCES shipments(id) ON DELETE CASCADE
);

-- Insert sample data
INSERT INTO users (name, email, password) VALUES 
('John Doe', 'john@example.com', '$2y$10$YourHashedPasswordHere'); -- password: password123

INSERT INTO shipments (tracking_number, status, origin, destination, current_location, estimated_delivery) VALUES
('DHL123456', 'In Transit', 'New York, USA', 'London, UK', 'Atlantic Ocean', DATE_ADD(CURDATE(), INTERVAL 5 DAY)),
('DHL789012', 'Delivered', 'Berlin, Germany', 'Paris, France', 'Paris, France', DATE_ADD(CURDATE(), INTERVAL -2 DAY)),
('DHL345678', 'Out for Delivery', 'Tokyo, Japan', 'Sydney, Australia', 'Brisbane, Australia', DATE_ADD(CURDATE(), INTERVAL 1 DAY));

-- Insert tracking history
INSERT INTO tracking_history (shipment_id, date, location, status) VALUES
(1, DATE_SUB(NOW(), INTERVAL 1 DAY), 'New York, USA', 'Departed'),
(1, DATE_SUB(NOW(), INTERVAL 2 DAY), 'New York, USA', 'Processed'),
(1, DATE_SUB(NOW(), INTERVAL 3 DAY), 'Chicago, USA', 'In Transit');
