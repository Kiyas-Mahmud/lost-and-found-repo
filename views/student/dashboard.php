<?php
// Set page variable for navbar
$page = 'dashboard';

// Start session and check authentication
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/../..');
}
require_once BASE_PATH . '/config/session.php';
require_once BASE_PATH . '/config/helpers.php';

// Require student authentication
requireStudent();

$userName = $_SESSION['full_name'] ?? 'Student';
$userId = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Lost & Found</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include '../components/common/navbar_student.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <!-- Welcome Header -->
            <div class="page-header">
                <div>
                    <h1><i class="fas fa-home"></i> Welcome Back, <?php echo htmlspecialchars($userName); ?>!</h1>
                    <p>Here's what's happening with your lost and found items today</p>
                </div>
                <a href="post_lost.php" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Post Item
                </a>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value" id="totalPosts">0</div>
                        <div class="stat-label">Total Posts</div>
                        <div class="stat-description">Items you've posted</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value" id="lostItems">0</div>
                        <div class="stat-label">Lost Items</div>
                        <div class="stat-description">Still searching for</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value" id="foundItems">0</div>
                        <div class="stat-label">Found Items</div>
                        <div class="stat-description">Waiting to be claimed</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-hand-paper"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value" id="myClaims">0</div>
                        <div class="stat-label">My Claims</div>
                        <div class="stat-description">Items you've claimed</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
                </div>
                <div class="card-body">
                    <div class="quick-actions-row">
                        <a href="browse.php" class="action-button">
                            <i class="fas fa-search"></i>
                            <span>Browse Items</span>
                        </a>
                        
                        <a href="post_lost.php" class="action-button">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Report Lost Item</span>
                        </a>
                        
                        <a href="post_found.php" class="action-button">
                            <i class="fas fa-hands-helping"></i>
                            <span>Post Found Item</span>
                        </a>
                        
                        <a href="my_posts.php" class="action-button">
                            <i class="fas fa-list-alt"></i>
                            <span>Manage Posts</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script>
        // Load dashboard stats
        document.addEventListener('DOMContentLoaded', async function() {
            try {
                const response = await fetch('../../api/student/dashboard.php');
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('totalPosts').textContent = data.data.myItems || 0;
                    document.getElementById('lostItems').textContent = data.data.lostItems || 0;
                    document.getElementById('foundItems').textContent = data.data.foundItems || 0;
                    document.getElementById('myClaims').textContent = data.data.totalClaims || 0;
                }
            } catch (error) {
                console.error('Error loading dashboard data:', error);
            }
        });
    </script>
</body>
</html>
