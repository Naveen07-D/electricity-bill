CREATE DATABASE IF NOT EXISTS electricity_bill;
USE electricity_bill;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'employee', 'consumer') NOT NULL,
    first_login BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE consumers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_number VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(10) NOT NULL,
    service_type ENUM('Household', 'Commercial', 'Industrial') NOT NULL,
    area_code VARCHAR(10),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    is_active BOOLEAN DEFAULT TRUE,
    user_id INT UNIQUE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE service_applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    mobile VARCHAR(10) NOT NULL,
    address TEXT NOT NULL,
    pincode VARCHAR(10) NOT NULL,
    document_proof VARCHAR(255),
    service_category ENUM('Household', 'Commercial', 'Industrial') NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    is_verified BOOLEAN DEFAULT FALSE,
    rejection_reason TEXT,
    service_number VARCHAR(20) UNIQUE,
    meter_number VARCHAR(20),
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE employee_applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    mobile VARCHAR(10) NOT NULL,
    address TEXT NOT NULL,
    document_proof VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    rejection_reason TEXT,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE bills (
    id INT PRIMARY KEY AUTO_INCREMENT,
    consumer_id INT NOT NULL,
    previous_reading DECIMAL(10,2) NOT NULL,
    current_reading DECIMAL(10,2) NOT NULL,
    units DECIMAL(10,2) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    previous_pending DECIMAL(10,2) DEFAULT 0,
    fine DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    bill_date DATE NOT NULL,
    due_date DATE NOT NULL,
    status ENUM('paid', 'unpaid') DEFAULT 'unpaid',
    generated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (consumer_id) REFERENCES consumers(id) ON DELETE CASCADE,
    FOREIGN KEY (generated_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    consumer_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (consumer_id) REFERENCES consumers(id) ON DELETE CASCADE
);

INSERT INTO users (username, password, role, first_login) VALUES 
('admin', '$2y$10$ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopq', 'admin', FALSE),
('emp001', '$2y$10$ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopq', 'employee', FALSE);

INSERT INTO users (username, password, role, first_login) VALUES 
('60000001', '$2y$10$ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopq', 'consumer', FALSE),
('70000001', '$2y$10$ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopq', 'consumer', FALSE);

INSERT INTO consumers (service_number, name, address, phone, service_type, area_code, status, is_active, user_id) VALUES 
('60000001', 'John Doe', '123 Main Street, Chennai', '9876543210', 'Household', '600001', 'approved', TRUE, 3),
('70000001', 'ABC Corporation', '456 Business Park, Mumbai', '8765432109', 'Commercial', '400001', 'approved', TRUE, 4);

INSERT INTO bills (consumer_id, previous_reading, current_reading, units, amount, previous_pending, fine, total_amount, bill_date, due_date, status, generated_by) VALUES 
(1, 100.50, 150.75, 50.25, 125.63, 0, 0, 125.63, '2024-01-01', '2024-01-16', 'paid', 2),
(1, 150.75, 210.25, 59.50, 148.75, 0, 150, 298.75, '2024-02-01', '2024-02-16', 'unpaid', 2),
(2, 500.00, 650.50, 150.50, 677.25, 0, 0, 677.25, '2024-01-01', '2024-01-16', 'unpaid', 2);

INSERT INTO notifications (consumer_id, message, is_read) VALUES 
(1, 'Your electricity bill has been generated. Please check your dashboard.', FALSE),
(1, 'Your bill payment was successful. Thank you!', TRUE),
(2, 'New electricity bill generated. Please check your dashboard.', FALSE);
