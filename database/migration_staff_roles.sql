-- Migration: Add Staff Role System
-- This migration updates the users table to support ADMINISTRATOR, MODERATOR, and STAFF roles
-- Date: 2026-01-21

-- Step 1: Add new columns for staff system
ALTER TABLE users
ADD COLUMN username VARCHAR(50) NULL UNIQUE AFTER email,
ADD COLUMN is_staff TINYINT(1) DEFAULT 0 AFTER role;

-- Step 2: Modify role enum to include new staff roles
ALTER TABLE users
MODIFY COLUMN role ENUM('STUDENT', 'ADMIN', 'ADMINISTRATOR', 'MODERATOR', 'STAFF') NOT NULL DEFAULT 'STUDENT';

-- Step 3: Update existing ADMIN users to ADMINISTRATOR
UPDATE users 
SET role = 'ADMINISTRATOR', is_staff = 1 
WHERE role = 'ADMIN';

-- Step 4: Add index for username and is_staff
ALTER TABLE users
ADD INDEX idx_username (username),
ADD INDEX idx_is_staff (is_staff);

-- Step 5: Create admin_users view for easier staff management
CREATE OR REPLACE VIEW admin_users AS
SELECT 
    user_id,
    full_name,
    email,
    username,
    role,
    account_status,
    is_staff,
    created_at,
    updated_at
FROM users
WHERE is_staff = 1 AND role IN ('ADMINISTRATOR', 'MODERATOR', 'STAFF');

-- Step 6: Add comments to document the role system
ALTER TABLE users COMMENT = 'User accounts with role-based access control. Roles: STUDENT (public), ADMINISTRATOR (full access), MODERATOR (can manage staff), STAFF (limited admin access)';
