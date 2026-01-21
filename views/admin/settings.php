<?php
// Check authentication
require_once '../../config/helpers.php';
require_once '../../config/session.php';
requireAdmin();

$pageTitle = 'Settings';
$currentUserRole = $_SESSION['role'] ?? 'STAFF';

// Debug: Check current role
// Map old ADMIN role to ADMINISTRATOR for compatibility
if ($currentUserRole === 'ADMIN') {
    $currentUserRole = 'ADMINISTRATOR';
}

// Show debug info (remove in production)
// echo "<!-- Current Role: " . $currentUserRole . " -->";

$isAdministrator = ($currentUserRole === 'ADMINISTRATOR');
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
                    <div>
                        <h2 class="page-title-large">Settings</h2>
                        <p class="page-subtitle">Manage your account and staff members</p>
                    </div>
                </div>

                <!-- Settings Cards -->
                <div class="settings-grid">
                    <!-- Update Profile Card - Only for Administrator -->
                    <?php if ($isAdministrator): ?>
                    <div class="settings-card">
                        <div class="settings-card-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <h3 class="settings-card-title">Update Profile</h3>
                        <p class="settings-card-description">Update your personal information and password</p>
                        <a href="update-profile.php" class="btn-primary">
                            Manage Profile
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </a>
                    </div>
                    <?php endif; ?>

                    <!-- Add Staff Card -->
                    <div class="settings-card">
                        <div class="settings-card-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="8.5" cy="7" r="4"/>
                                <line x1="20" y1="8" x2="20" y2="14"/>
                                <line x1="23" y1="11" x2="17" y2="11"/>
                            </svg>
                        </div>
                        <h3 class="settings-card-title">Add Staff</h3>
                        <p class="settings-card-description">
                            <?php if ($currentUserRole === 'ADMINISTRATOR'): ?>
                                Add new moderators or staff members to the admin panel
                            <?php else: ?>
                                Add new staff members to the admin panel
                            <?php endif; ?>
                        </p>
                        <a href="add-staff.php" class="btn-primary">
                            Add Staff Member
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </a>
                    </div>

                    <!-- Staff List Card - Only for Administrator -->
                    <?php if ($isAdministrator): ?>
                    <div class="settings-card">
                        <div class="settings-card-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <h3 class="settings-card-title">Staff List</h3>
                        <p class="settings-card-description">View, edit, and manage all staff members and moderators</p>
                        <a href="staff-list.php" class="btn-primary">
                            View Staff List
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
