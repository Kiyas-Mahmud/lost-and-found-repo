<?php
/**
 * Admin Settings Controller
 * Handles staff management and profile operations
 */

require_once __DIR__ . '/../../config/db.php';

class SettingsController {
    private $db;
    private $lastError = null;
    
    public function __construct() {
        $this->db = get_db_connection();
    }
    
    public function getLastError() {
        return $this->lastError;
    }
    
    /**
     * Get all staff members (ADMINISTRATOR, MODERATOR, STAFF)
     */
    public function getAllStaff($search = '', $roleFilter = '') {
        $query = "
            SELECT 
                user_id,
                full_name,
                email,
                username,
                role,
                account_status,
                created_at
            FROM users
            WHERE is_staff = 1
        ";
        
        $params = [];
        
        // Apply search filter
        if (!empty($search)) {
            $query .= " AND (full_name LIKE :search OR email LIKE :search OR username LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        
        // Apply role filter
        if (!empty($roleFilter)) {
            $query .= " AND role = :role";
            $params[':role'] = $roleFilter;
        }
        
        $query .= " ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Get staff member by ID
     */
    public function getStaffById($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    user_id,
                    full_name,
                    email,
                    username,
                    role,
                    account_status
                FROM users 
                WHERE user_id = :id AND is_staff = 1
            ");
            $stmt->execute([':id' => $userId]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return null;
        }
    }
    
    /**
     * Add new staff member
     * Administrators can add MODERATOR or STAFF
     * Moderators can only add STAFF
     */
    public function addStaff($fullName, $email, $username, $password, $role, $currentUserRole) {
        try {
            // Permission check
            if ($currentUserRole === 'MODERATOR' && $role !== 'STAFF') {
                $this->lastError = 'Moderators can only add STAFF members';
                return false;
            }
            
            if (!in_array($role, ['ADMINISTRATOR', 'MODERATOR', 'STAFF'])) {
                $this->lastError = 'Invalid role specified';
                return false;
            }
            
            // Check if email already exists
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            if ($stmt->fetchColumn() > 0) {
                $this->lastError = 'Email already exists';
                return false;
            }
            
            // Check if username already exists
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            if ($stmt->fetchColumn() > 0) {
                $this->lastError = 'Username already exists';
                return false;
            }
            
            // Hash password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new staff member
            $stmt = $this->db->prepare("
                INSERT INTO users (full_name, email, username, password_hash, role, is_staff, account_status) 
                VALUES (:name, :email, :username, :password, :role, 1, 'ACTIVE')
            ");
            
            $result = $stmt->execute([
                ':name' => $fullName,
                ':email' => $email,
                ':username' => $username,
                ':password' => $passwordHash,
                ':role' => $role
            ]);
            
            return $result;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }
    
    /**
     * Update staff member
     */
    public function updateStaff($userId, $fullName, $username, $role, $password = null) {
        try {
            // Check if username is already taken by another user
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = :username AND user_id != :id");
            $stmt->execute([':username' => $username, ':id' => $userId]);
            if ($stmt->fetchColumn() > 0) {
                $this->lastError = 'Username already exists';
                return false;
            }
            
            // Build update query
            if (!empty($password)) {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $this->db->prepare("
                    UPDATE users 
                    SET full_name = :name, username = :username, role = :role, password_hash = :password
                    WHERE user_id = :id AND is_staff = 1
                ");
                $params = [
                    ':name' => $fullName,
                    ':username' => $username,
                    ':role' => $role,
                    ':password' => $passwordHash,
                    ':id' => $userId
                ];
            } else {
                $stmt = $this->db->prepare("
                    UPDATE users 
                    SET full_name = :name, username = :username, role = :role
                    WHERE user_id = :id AND is_staff = 1
                ");
                $params = [
                    ':name' => $fullName,
                    ':username' => $username,
                    ':role' => $role,
                    ':id' => $userId
                ];
            }
            
            return $stmt->execute($params);
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }
    
    /**
     * Delete staff member
     */
    public function deleteStaff($userId) {
        try {
            // Don't allow deleting yourself
            if ($userId == $_SESSION['user_id']) {
                $this->lastError = 'Cannot delete your own account';
                return false;
            }
            
            // Delete staff member
            $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = :id AND is_staff = 1");
            return $stmt->execute([':id' => $userId]);
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }
    
    /**
     * Update own profile
     */
    public function updateProfile($userId, $fullName, $email, $currentPassword = null, $newPassword = null) {
        try {
            // Check if email is already taken by another user
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email AND user_id != :id");
            $stmt->execute([':email' => $email, ':id' => $userId]);
            if ($stmt->fetchColumn() > 0) {
                $this->lastError = 'Email already exists';
                return false;
            }
            
            // If changing password, verify current password
            if (!empty($newPassword)) {
                if (empty($currentPassword)) {
                    $this->lastError = 'Current password is required to set a new password';
                    return false;
                }
                
                $stmt = $this->db->prepare("SELECT password_hash FROM users WHERE user_id = :id");
                $stmt->execute([':id' => $userId]);
                $user = $stmt->fetch(PDO::FETCH_OBJ);
                
                if (!password_verify($currentPassword, $user->password_hash)) {
                    $this->lastError = 'Current password is incorrect';
                    return false;
                }
                
                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $this->db->prepare("
                    UPDATE users 
                    SET full_name = :name, email = :email, password_hash = :password
                    WHERE user_id = :id
                ");
                $params = [
                    ':name' => $fullName,
                    ':email' => $email,
                    ':password' => $newPasswordHash,
                    ':id' => $userId
                ];
            } else {
                $stmt = $this->db->prepare("
                    UPDATE users 
                    SET full_name = :name, email = :email
                    WHERE user_id = :id
                ");
                $params = [
                    ':name' => $fullName,
                    ':email' => $email,
                    ':id' => $userId
                ];
            }
            
            $result = $stmt->execute($params);
            
            // Update session data
            if ($result) {
                $_SESSION['full_name'] = $fullName;
                $_SESSION['email'] = $email;
            }
            
            return $result;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }
}
