<?php
// Set page variable for navbar
$page = 'profile';

// Start session and check authentication
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/../..');
}
require_once BASE_PATH . '/config/session.php';
require_once BASE_PATH . '/config/helpers.php';
require_once BASE_PATH . '/controllers/student/profile.php';

// Require student authentication
requireStudent();

$userId = $_SESSION['user_id'];
$controller = new StudentProfileController();

// Handle form submission
$message = '';
$messageType = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->updateProfile($userId, $_POST);
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'error';
}

// Get profile data
$profile = $controller->getProfileData($userId);

if (!$profile) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Lost & Found</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include '../components/common/navbar_student.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <div class="form-container">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="page-header-left">
                        <a href="dashboard.php" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <div>
                            <h1><i class="fas fa-user"></i> My Profile</h1>
                            <p>View and update your profile information</p>
                        </div>
                    </div>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" id="profileForm" class="admin-form">
                    <!-- Profile Information Section -->
                    <div class="form-section">
                        <h2 class="form-section-title">Profile Information</h2>
                        <p class="form-section-subtitle">View your personal details</p>
                        
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" 
                                       value="<?php echo htmlspecialchars($profile['full_name']); ?>" 
                                       class="form-control" 
                                       readonly 
                                       style="background-color: #f3f4f6; cursor: not-allowed;">
                                <small class="form-text-muted">Full name cannot be changed</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Student ID</label>
                                <input type="text" 
                                       value="<?php echo htmlspecialchars($profile['student_id'] ?? 'Not provided'); ?>" 
                                       class="form-control" 
                                       readonly 
                                       style="background-color: #f3f4f6; cursor: not-allowed;">
                                <small class="form-text-muted">Student ID cannot be changed</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" 
                                       value="<?php echo htmlspecialchars($profile['phone'] ?? 'Not provided'); ?>" 
                                       class="form-control" 
                                       readonly 
                                       style="background-color: #f3f4f6; cursor: not-allowed;">
                                <small class="form-text-muted">Phone number cannot be changed</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Account Status</label>
                                <input type="text" 
                                       value="<?php echo htmlspecialchars($profile['account_status']); ?>" 
                                       class="form-control" 
                                       readonly 
                                       style="background-color: #f3f4f6; cursor: not-allowed;">
                            </div>
                        </div>
                    </div>

                    <!-- Editable Information Section -->
                    <div class="form-section">
                        <h2 class="form-section-title">Editable Information</h2>
                        <p class="form-section-subtitle">Update your username and email</p>
                        
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" 
                                       id="username" 
                                       name="username" 
                                       value="<?php echo htmlspecialchars($profile['username'] ?? ''); ?>" 
                                       class="form-control" 
                                       placeholder="Enter username">
                                <span class="form-error" id="username-error"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address <span class="required">*</span></label>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       value="<?php echo htmlspecialchars($profile['email']); ?>" 
                                       class="form-control" 
                                       placeholder="Enter email address" 
                                       required>
                                <span class="form-error" id="email-error"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Change Password Section -->
                    <div class="form-section">
                        <h2 class="form-section-title">Change Password</h2>
                        <p class="form-section-subtitle">Leave blank if you don't want to change your password</p>
                        
                        <div class="form-text-info">
                            <i class="fas fa-info-circle"></i>
                            <span>Password must be at least 6 characters long. You must provide your current password to change it.</span>
                        </div>
                        
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <input type="password" 
                                       id="current_password" 
                                       name="current_password" 
                                       class="form-control" 
                                       placeholder="Enter current password">
                                <span class="form-error" id="current_password-error"></span>
                            </div>
                            
                            <div class="form-group">
                                <!-- Empty space for grid layout -->
                            </div>
                            
                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" 
                                       id="new_password" 
                                       name="new_password" 
                                       class="form-control" 
                                       placeholder="Enter new password (min 6 characters)">
                                <span class="form-error" id="new_password-error"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password</label>
                                <input type="password" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       class="form-control" 
                                       placeholder="Re-enter new password">
                                <span class="form-error" id="confirm_password-error"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const profileForm = document.getElementById('profileForm');
        const usernameInput = document.getElementById('username');
        const emailInput = document.getElementById('email');
        const currentPasswordInput = document.getElementById('current_password');
        const newPasswordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');

        // Real-time validation
        emailInput.addEventListener('input', function() {
            if (this.value.trim()) clearError('email');
        });

        newPasswordInput.addEventListener('input', function() {
            if (this.value) clearError('new_password');
            if (confirmPasswordInput.value) {
                validatePasswordMatch();
            }
        });

        confirmPasswordInput.addEventListener('input', function() {
            if (this.value) clearError('confirm_password');
            validatePasswordMatch();
        });

        currentPasswordInput.addEventListener('input', function() {
            if (this.value) clearError('current_password');
        });

        // Form submission validation
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            let isValid = true;

            // Validate email
            const email = emailInput.value.trim();
            if (!email) {
                showError('email', 'Email is required');
                isValid = false;
            } else if (!isValidEmail(email)) {
                showError('email', 'Please enter a valid email address');
                isValid = false;
            }

            // Validate password change if any password field is filled
            const currentPassword = currentPasswordInput.value;
            const newPassword = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (newPassword || confirmPassword || currentPassword) {
                if (!currentPassword) {
                    showError('current_password', 'Current password is required to change password');
                    isValid = false;
                }

                if (!newPassword) {
                    showError('new_password', 'New password is required');
                    isValid = false;
                } else if (newPassword.length < 6) {
                    showError('new_password', 'Password must be at least 6 characters');
                    isValid = false;
                }

                if (!confirmPassword) {
                    showError('confirm_password', 'Please confirm your new password');
                    isValid = false;
                } else if (newPassword !== confirmPassword) {
                    showError('confirm_password', 'Passwords do not match');
                    isValid = false;
                }
            }

            if (isValid) {
                this.submit();
            }
        });

        function validatePasswordMatch() {
            const newPassword = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (newPassword && confirmPassword && newPassword !== confirmPassword) {
                showError('confirm_password', 'Passwords do not match');
                return false;
            }
            return true;
        }

        function showError(fieldId, message) {
            const input = document.getElementById(fieldId);
            const errorElement = document.getElementById(fieldId + '-error');
            
            input.classList.add('error');
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }

        function clearError(fieldId) {
            const input = document.getElementById(fieldId);
            const errorElement = document.getElementById(fieldId + '-error');
            
            input.classList.remove('error');
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }

        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }
    </script>
</body>
</html>
