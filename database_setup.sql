-- Create and use database
CREATE DATABASE IF NOT EXISTS healthcare_db;
USE healthcare_db;

-- Users table for authentication
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'doctor', 'receptionist') DEFAULT 'receptionist',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Patients table
CREATE TABLE patients (
    patient_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    join_date DATE NOT NULL,
    phone VARCHAR(15) NOT NULL,
    address TEXT
);

-- Visits table
CREATE TABLE visits (
    visit_id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    visit_date DATE NOT NULL,
    consultation_fee DECIMAL(10,2) DEFAULT 0.00,
    lab_fee DECIMAL(10,2) DEFAULT 0.00,
    follow_up_due DATE,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id)
);

-- Insert sample users (password: 'password123' for all)
INSERT INTO users (username, password, role) VALUES
('admin', '$2y$10$YourHashedPasswordHere', 'admin'),
('doctor1', '$2y$10$YourHashedPasswordHere', 'doctor'),
('reception1', '$2y$10$YourHashedPasswordHere', 'receptionist');

-- Insert 10 sample patients with various dates
INSERT INTO patients (name, dob, join_date, phone, address) VALUES
('John Smith', '1985-03-15', '2023-01-10', '555-0101', '123 Main St'),
('Sarah Johnson', '1990-07-22', '2023-02-15', '555-0102', '456 Oak Ave'),
('Michael Brown', '1978-11-08', '2023-03-20', '555-0103', '789 Pine Rd'),
('Emily Davis', '1995-01-30', '2023-04-05', '555-0104', '321 Elm St'),
('Robert Wilson', '1982-06-17', '2023-05-12', '555-0105', '654 Maple Dr'),
('Jennifer Taylor', '1964-09-25', '2023-06-18', '555-0106', '987 Cedar Ln'),
('David Martinez', '1974-12-03', '2023-07-22', '555-0107', '147 Birch Blvd'),
('Lisa Anderson', '1984-02-29', '2023-08-30', '555-0108', '258 Spruce Way'),
('James Thomas', '1998-08-19', '2024-01-05', '555-0109', '369 Walnut St'),
('Maria Garcia', '2000-02-28', '2024-02-14', '555-0110', '741 Cherry Ave');

-- Insert 20 sample visits with various dates
INSERT INTO visits (patient_id, visit_date, consultation_fee, lab_fee, follow_up_due) VALUES
-- Past year visits
(1, '2023-06-15', 50.00, 0.00, '2023-06-22'),
(2, '2023-07-20', 75.00, 50.00, '2023-07-27'),
(3, '2023-08-10', 60.00, 100.00, '2023-08-17'),
(4, '2023-09-05', 80.00, 0.00, '2023-09-12'),
(5, '2023-10-18', 55.00, 30.00, '2023-10-25'),

-- Recent visits
(1, DATE_SUB(CURDATE(), INTERVAL 20 DAY), 50.00, 0.00, DATE_SUB(CURDATE(), INTERVAL 13 DAY)),
(2, DATE_SUB(CURDATE(), INTERVAL 15 DAY), 45.00, 0.00, DATE_SUB(CURDATE(), INTERVAL 8 DAY)),
(3, DATE_SUB(CURDATE(), INTERVAL 10 DAY), 55.00, 25.00, DATE_SUB(CURDATE(), INTERVAL 3 DAY)),
(4, DATE_SUB(CURDATE(), INTERVAL 7 DAY), 60.00, 50.00, CURDATE()),
(5, DATE_SUB(CURDATE(), INTERVAL 5 DAY), 70.00, 75.00, DATE_ADD(CURDATE(), INTERVAL 2 DAY)),

-- Current and future follow-ups
(6, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 100.00, 150.00, DATE_ADD(CURDATE(), INTERVAL 4 DAY)),
(7, DATE_SUB(CURDATE(), INTERVAL 2 DAY), 75.00, 100.00, DATE_ADD(CURDATE(), INTERVAL 5 DAY)),
(8, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 120.00, 0.00, DATE_ADD(CURDATE(), INTERVAL 6 DAY)),
(9, CURDATE(), 150.00, 200.00, DATE_ADD(CURDATE(), INTERVAL 7 DAY)),
(10, CURDATE(), 80.00, 0.00, DATE_ADD(CURDATE(), INTERVAL 7 DAY)),

-- Additional visits for variety
(1, '2024-01-10', 100.00, 200.00, '2024-01-17'),
(2, '2024-02-14', 50.00, 0.00, '2024-02-21'),
(6, '2024-03-08', 65.00, 0.00, '2024-03-15'),
(7, '2024-04-15', 80.00, 100.00, '2024-04-22'),
(8, '2024-05-22', 90.00, 50.00, '2024-05-29');