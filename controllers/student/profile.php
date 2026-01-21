<?php
require_once __DIR__ . '/../../config/db.php';

class StudentProfileController {
    private $conn;
    
    public function __construct() {
        $this->conn = get_db_connection();
    }
    
    /**
     * Get student profile data
     */
    public function getProfileData($userId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT user_id, full_name, email, username, student_id, phone, 
                       role, account_status, created_at
                FROM users 
                WHERE user_id = ? AND role = 'STUDENT'
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get profile error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update student profile
     */
    public function updateProfile($userId, $data) {
        try {
            $username = trim($data['username'] ?? '');
            $email = trim($data['email'] ?? '');
            $current_password = $data['current_password'] ?? '';
            $new_password = $data['new_password'] ?? '';
            $confirm_password = $data['confirm_password'] ?? '';
            
            // Validation
            if (empty($username)) {
                return ['success' => false, 'message' => 'Username is required'];
            }
            
            if (empty($email)) {
                return ['success' => false, 'message' => 'Email is required'];
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Invalid email format'];
            }
            
            // Check if username is taken by another user
            if (!empty($username)) {
                $stmt = $this->conn->prepare("SELECT user_id FROM users WHERE username = ? AND user_id != ?");
                $stmt->execute([$username, $userId]);
                if ($stmt->fetch()) {
                    return ['success' => false, 'message' => 'Username is already taken'];
                }
            }
            
            // Check if email is taken by another user
            $stmt = $this->conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
            $stmt->execute([$email, $userId]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email is already registered'];
            }
            
            // If changing password, validate
            if (!empty($new_password)) {
                if (empty($current_password)) {
                    return ['success' => false, 'message' => 'Current password is required to change password'];
                }
                
                // Verify current password
                $stmt = $this->conn->prepare("SELECT password_hash FROM users WHERE user_id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!password_verify($current_password, $user['password_hash'])) {
                    return ['success' => false, 'message' => 'Current password is incorrect'];
                }
                
                if (strlen($new_password) < 6) {
                    return ['success' => false, 'message' => 'New password must be at least 6 characters'];
                }
                
                if ($new_password !== $confirm_password) {
                    return ['success' => false, 'message' => 'New passwords do not match'];
                }
                
                // Update with password
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $this->conn->prepare("
                    UPDATE users 
                    SET username = ?, email = ?, password_hash = ?, updated_at = NOW() 
                    WHERE user_id = ?
                ");
                $stmt->execute([$username, $email, $password_hash, $userId]);
            } else {
                // Update without password
                $stmt = $this->conn->prepare("
                    UPDATE users 
                    SET username = ?, email = ?, updated_at = NOW() 
                    WHERE user_id = ?
                ");
                $stmt->execute([$username, $email, $userId]);
            }
            
            // Update session data
            $_SESSION['email'] = $email;
            if (!empty($username)) {
                $_SESSION['username'] = $username;
            }
            
            return ['success' => true, 'message' => 'Profile updated successfully'];
            
        } catch (PDOException $e) {
            error_log("Update profile error: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while updating profile'];
        }
    }
}
