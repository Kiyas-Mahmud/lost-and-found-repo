<?php
// Check authentication - Only ADMINISTRATOR can access
require_once '../../config/helpers.php';
require_once '../../config/session.php';
requireAdmin();

$currentUserRole = $_SESSION['role'] ?? 'STAFF';
if ($currentUserRole === 'ADMIN') {
    $currentUserRole = 'ADMINISTRATOR';
}

if ($currentUserRole !== 'ADMINISTRATOR') {
    set_flash('error', 'Access denied. Only administrators can access this page.');
    header('Location: settings.php');
    exit();
}

// Load controller
require_once '../../controllers/admin/settings.php';

$pageTitle = 'Update Profile';
$controller = new SettingsController();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    $errors = [];
    
    if (empty($fullName)) {
        $errors[] = 'Full name is required';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }
    
    // If changing password
    if (!empty($newPassword)) {
        if (strlen($newPassword) < 6) {
            $errors[] = 'New password must be at least 6 characters';
        }
        if ($newPassword !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }
    }
    
    if (empty($errors)) {
        $success = $controller->updateProfile(
            $_SESSION['user_id'],
            $fullName,
            $email,
            $currentPassword,
            !empty($newPassword) ? $newPassword : null
        );
        
        if ($success) {
            set_flash('success', 'Profile updated successfully!');
            header('Location: update-profile.php');
            exit();
        } else {
            set_flash('error', $controller->getLastError() ?: 'Failed to update profile');
        }
    } else {
        set_flash('error', implode('<br>', $errors));
    }
}

// Get current user data
$userId = $_SESSION['user_id'];
$fullName = $_SESSION['full_name'] ?? '';
$email = $_SESSION['email'] ?? '';
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
                            <h2 class="page-title-large">Update Profile</h2>
                            <p class="page-subtitle">Update your personal information and password</p>
                        </div>
                    </div>
                </div>

                <!-- Update Profile Form -->
                <div class="admin-table-container">
                    <div class="form-container" style="max-width: 900px;">
                        <form method="POST" action="" class="admin-form">
                            <div class="form-section">
                                <h3 class="form-section-title">Personal Information</h3>
                                
                                <div class="form-grid-2">
                                    <div class="form-group">
                                        <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input 
                                            type="text" 
                                            id="full_name" 
                                            name="full_name" 
                                            class="form-control" 
                                            value="<?php echo htmlspecialchars($fullName); ?>"
                                            required
                                        >
                                    </div>

                                    <div class="form-group">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input 
                                            type="email" 
                                            id="email" 
                                            name="email" 
                                            class="form-control" 
                                            value="<?php echo htmlspecialchars($email); ?>"
                                            required
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h3 class="form-section-title">Change Password (Optional)</h3>
                                <p class="form-section-subtitle">Leave blank if you don't want to change your password</p>
                                
                                <div class="form-group">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input 
                                        type="password" 
                                        id="current_password" 
                                        name="current_password" 
                                        class="form-control" 
                                        placeholder="Enter current password"
                                    >
                                </div>

                                <div class="form-grid-2">
                                    <div class="form-group">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input 
                                            type="password" 
                                            id="new_password" 
                                            name="new_password" 
                                            class="form-control" 
                                            placeholder="Enter new password (min. 6 characters)"
                                        >
                                    </div>

                                    <div class="form-group">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input 
                                            type="password" 
                                            id="confirm_password" 
                                            name="confirm_password" 
                                            class="form-control" 
                                            placeholder="Confirm new password"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <a href="settings.php" class="btn-secondary">Cancel</a>
                                <button type="submit" class="btn-primary">Update Profile</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
