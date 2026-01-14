-- Seed Data for Lost & Found Platform

-- Default Admin User (admin@university.edu / Admin@123)
INSERT INTO users (full_name, email, student_id, phone, password_hash, role, account_status) VALUES
('System Administrator', 'admin@university.edu', NULL, '1234567890', '$2y$10$al630AZRuMEL8SNIVvSJ6OrZTE4vyuk7/6881/8K8RHgALKdGfDDu', 'ADMIN', 'ACTIVE');

-- Test Student Users (Password: Student@123)
INSERT INTO users (full_name, email, student_id, phone, password_hash, role, account_status) VALUES
('John Doe', 'john.doe@university.edu', 'STU001', '9876543210', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'STUDENT', 'ACTIVE'),
('Jane Smith', 'jane.smith@university.edu', 'STU002', '9876543211', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'STUDENT', 'ACTIVE'),
('Mike Johnson', 'mike.johnson@university.edu', 'STU003', '9876543212', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'STUDENT', 'ACTIVE');

-- Categories
INSERT INTO categories (category_name, is_active) VALUES
('ID Card', 1),
('Wallet', 1),
('Phone', 1),
('Keys', 1),
('Bag', 1),
('Books', 1),
('Electronics', 1),
('Clothing', 1),
('Jewelry', 1),
('Documents', 1),
('Other', 1);

-- Locations
INSERT INTO locations (location_name, is_active) VALUES
('Library', 1),
('Canteen', 1),
('Computer Lab', 1),
('Classroom Building A', 1),
('Classroom Building B', 1),
('Parking Lot', 1),
('Sports Complex', 1),
('Auditorium', 1),
('Hostel Block 1', 1),
('Hostel Block 2', 1),
('Garden Area', 1),
('Main Gate', 1),
('Other', 1);
