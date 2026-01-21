<?php
// Check authentication
require_once '../../config/helpers.php';
require_once '../../config/session.php';
requireAdmin();

// STAFF cannot access this page
if ($_SESSION['role'] === 'STAFF') {
    set_flash('error', 'Access denied.');
    header('Location: ../dashboard.php');
    exit();
}

// Load controller
require_once '../../controllers/admin/settings.php';

$pageTitle = 'Add Staff';
$controller = new SettingsController();
$currentUserRole = $_SESSION['role'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';
    
    // Validation
    $errors = [];
    
    if (empty($fullName)) {
        $errors[] = 'Full name is required';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }
    
    if (empty($username) || strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters';
    }
    
    if (empty($password) || strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
    }
    
    if (empty($role) || !in_array($role, ['ADMINISTRATOR', 'MODERATOR', 'STAFF'])) {
        $errors[] = 'Please select a valid role';
    }
    
    // Moderators can only add STAFF
    if ($currentUserRole === 'MODERATOR' && $role !== 'STAFF') {
        $errors[] = 'Moderators can only add STAFF members';
    }
    
    if (empty($errors)) {
        $success = $controller->addStaff($fullName, $email, $username, $password, $role, $currentUserRole);
        
        if ($success) {
            set_flash('success', ucfirst(strtolower($role)) . ' member added successfully!');
            header('Location: add-staff.php');
            exit();
        } else {
            set_flash('error', $controller->getLastError() ?: 'Failed to add staff member');
        }
    } else {
        set_flash('error', implode('<br>', $errors));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin Panel</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="admin-layout">
        <?php include '../components/admin/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include '../components/admin/header.php'; ?>
            
            <div class="admin-content">
                <?php include '../components/common/flash_message.php'; ?>
                
                <!-- Page Header -->
                <div class="page-header">
                    <div class="page-header-left">
                        <a href="settings.php" class="btn-back">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="19" y1="12" x2="5" y2="12"/>
                                <polyline points="12 19 5 12 12 5"/>
                            </svg>
                        </a>
                        <div>
                            <h2 class="page-title-large">Add Staff Member</h2>
                            <p class="page-subtitle">
                            <?php if ($currentUserRole === 'ADMINISTRATOR'): ?>
                                Create new administrator, moderator, or staff account
                            <?php else: ?>
                                Create new staff account
                            <?php endif; ?>
                        </p>
                        </div>
                    </div>
                </div>

                <!-- Add Staff Form -->
                <div class="admin-table-container">
                    <div class="form-container" style="max-width: 900px;">
                        <form method="POST" action="" class="admin-form" id="addStaffForm" onsubmit="return validateForm()">
                            <div class="form-section">
                                <h3 class="form-section-title">Staff Information</h3>
                                
                                <div class="form-grid-2">
                                    <div class="form-group">
                                        <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input 
                                            type="text" 
                                            id="full_name" 
                                            name="full_name" 
                                            class="form-control" 
                                            placeholder="Enter full name"
                                            required
                                            minlength="3"
                                        >
                                        <small class="form-error" id="error_full_name"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input 
                                            type="email" 
                                            id="email" 
                                            name="email" 
                                            class="form-control" 
                                            placeholder="Enter email address"
                                            required
                                        >
                                        <small class="form-error" id="error_email"></small>
                                    </div>
                                </div>

                                <div class="form-grid-2">
                                    <div class="form-group">
                                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                        <input 
                                            type="text" 
                                            id="username" 
                                            name="username" 
                                            class="form-control" 
                                            placeholder="Enter username (min. 3 characters)"
                                            required
                                            minlength="3"
                                            pattern="[a-zA-Z0-9_]+"
                                        >
                                        <small class="form-error" id="error_username"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                        <select id="role" name="role" class="form-control" required>
                                            <option value="">Select Role</option>
                                            <?php if ($currentUserRole === 'ADMINISTRATOR'): ?>
                                                <option value="ADMINISTRATOR">Administrator</option>
                                                <option value="MODERATOR">Moderator</option>
                                            <?php endif; ?>
                                            <option value="STAFF">Staff</option>
                                        </select>
                                        <small class="form-error" id="error_role"></small>
                                    </div>
                                </div>

                                <div class="form-text-info">
                                    <?php if ($currentUserRole === 'ADMINISTRATOR'): ?>
                                        <strong>Administrator:</strong> Full access to all features including staff management<br>
                                        <strong>Moderator:</strong> Can add staff members but cannot manage admins/moderators<br>
                                    <?php endif; ?>
                                    <strong>Staff:</strong> Limited admin access, cannot manage other users
                                </div>
                            </div>

                            <div class="form-section">
                                <h3 class="form-section-title">Account Security</h3>
                                
                                <div class="form-grid-2">
                                    <div class="form-group">
                                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                        <input 
                                            type="password" 
                                            id="password" 
                                            name="password" 
                                            class="form-control" 
                                            placeholder="Enter password (min. 6 characters)"
                                            required
                                            minlength="6"
                                        >
                                        <small class="form-error" id="error_password"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                        <input 
                                            type="password" 
                                            id="confirm_password" 
                                            name="confirm_password" 
                                            class="form-control" 
                                            placeholder="Confirm password"
                                            required
                                        >
                                        <small class="form-error" id="error_confirm_password"></small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <a href="settings.php" class="btn-secondary">Cancel</a>
                                <button type="submit" class="btn-primary">Add Staff Member</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script>
        function validateForm() {
            let isValid = true;
            
            // Clear previous errors
            document.querySelectorAll('.form-error').forEach(el => el.textContent = '');
            document.querySelectorAll('.form-control').forEach(el => el.classList.remove('error'));
            
            // Full Name validation
            const fullName = document.getElementById('full_name');
            if (fullName.value.trim().length < 3) {
                showError('full_name', 'Full name must be at least 3 characters');
                isValid = false;
            }
            
            // Email validation
            const email = document.getElementById('email');
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email.value)) {
                showError('email', 'Please enter a valid email address');
                isValid = false;
            }
            
            // Username validation
            const username = document.getElementById('username');
            const usernamePattern = /^[a-zA-Z0-9_]{3,}$/;
            if (!usernamePattern.test(username.value)) {
                showError('username', 'Username must be at least 3 characters (letters, numbers, underscore only)');
                isValid = false;
            }
            
            // Role validation
            const role = document.getElementById('role');
            if (!role.value) {
                showError('role', 'Please select a role');
                isValid = false;
            }
            
            // Password validation
            const password = document.getElementById('password');
            if (password.value.length < 6) {
                showError('password', 'Password must be at least 6 characters');
                isValid = false;
            }
            
            // Confirm Password validation
            const confirmPassword = document.getElementById('confirm_password');
            if (password.value !== confirmPassword.value) {
                showError('confirm_password', 'Passwords do not match');
                isValid = false;
            }
            
            return isValid;
        }
        
        function showError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorEl = document.getElementById('error_' + fieldId);
            field.classList.add('error');
            errorEl.textContent = message;
        }
        
        // Real-time validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            if (this.value && this.value !== password) {
                showError('confirm_password', 'Passwords do not match');
            } else {
                document.getElementById('error_confirm_password').textContent = '';
                this.classList.remove('error');
            }
        });
    </script>
</body>
</html>
