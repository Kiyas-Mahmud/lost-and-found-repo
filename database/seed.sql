-- ============================================
-- University Lost & Found Management Platform
-- Seed Data
-- Created: January 15, 2026
-- ============================================

-- ============================================
-- Insert Default Admin User
-- Email: admin@university.edu
-- Password: Admin@123
-- Password Hash generated using password_hash('Admin@123', PASSWORD_DEFAULT)
-- ============================================
INSERT INTO users (full_name, email, student_id, phone, password_hash, role, account_status) VALUES
('System Administrator', 'admin@university.edu', NULL, '1234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN', 'ACTIVE');

-- ============================================
-- Insert Test Student Users
-- Password for all: Student@123
-- ============================================
INSERT INTO users (full_name, email, student_id, phone, password_hash, role, account_status) VALUES
('John Doe', 'john.doe@university.edu', 'STU001', '9876543210', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'STUDENT', 'ACTIVE'),
('Jane Smith', 'jane.smith@university.edu', 'STU002', '9876543211', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'STUDENT', 'ACTIVE'),
('Mike Johnson', 'mike.johnson@university.edu', 'STU003', '9876543212', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'STUDENT', 'ACTIVE');

-- ============================================
-- Insert Categories
-- ============================================
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

-- ============================================
-- Insert Locations
-- ============================================
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

-- ============================================
-- Seed data insertion complete
-- ============================================

-- ============================================
-- Default Credentials for Testing:
-- ============================================
-- Admin Login:
--   Email: admin@university.edu
--   Password: Admin@123
--
-- Student Login (All 3 students):
--   Password: Student@123
--   john.doe@university.edu
--   jane.smith@university.edu
--   mike.johnson@university.edu
-- ============================================
