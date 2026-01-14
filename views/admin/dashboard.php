<?php
require_once '../../config/session.php';
require_once '../../config/db.php';
requireAdmin();

$pageTitle = 'Dashboard';

// Get statistics
$db = get_db_connection();

// Total Users
$stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'STUDENT'");
$totalUsers = $stmt->fetch(PDO::FETCH_OBJ)->count;

// Total Posts
$stmt = $db->query("SELECT COUNT(*) as count FROM items");
$totalPosts = $stmt->fetch(PDO::FETCH_OBJ)->count;

// Pending Claims
$stmt = $db->query("SELECT COUNT(*) as count FROM claims WHERE claim_status = 'PENDING'");
$pendingClaims = $stmt->fetch(PDO::FETCH_OBJ)->count;

// Open Reports
$stmt = $db->query("SELECT COUNT(*) as count FROM reports WHERE report_status = 'OPEN'");
$openReports = $stmt->fetch(PDO::FETCH_OBJ)->count;

// Hidden Posts
$stmt = $db->query("SELECT COUNT(*) as count FROM items WHERE current_status = 'HIDDEN'");
$hiddenPosts = $stmt->fetch(PDO::FETCH_OBJ)->count;

// Today's Activity
$stmt = $db->query("SELECT COUNT(*) as count FROM items WHERE DATE(created_at) = CURDATE()");
$todayActivity = $stmt->fetch(PDO::FETCH_OBJ)->count;

// Recent Activity (last 10)
$recentActivity = $db->query("
    SELECT 'post' as type, i.title as description, i.created_at as activity_time, 
           CONCAT(u.full_name, ' posted an item') as activity_text
    FROM items i
    JOIN users u ON i.posted_by = u.user_id
    ORDER BY i.created_at DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_OBJ);
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
                <!-- Statistics Grid -->
                <div class="stats-grid">
                    <?php 
                    include '../components/admin/stat_card.php';
                    renderStatCard('users', $totalUsers, 'Total Users', 'primary');
                    renderStatCard('posts', $totalPosts, 'Total Posts', 'info');
                    renderStatCard('claims', $pendingClaims, 'Pending Claims', 'warning');
                    renderStatCard('reports', $openReports, 'Open Reports', 'warning');
                    renderStatCard('hidden', $hiddenPosts, 'Hidden Posts', 'danger');
                    renderStatCard('activity', $todayActivity, "Today's Activity", 'success');
                    ?>
                </div>

                <!-- Recent Activity -->
                <div class="admin-table-container">
                    <div class="table-header">
                        <h3 class="table-title">Recent Activity</h3>
                    </div>
                    
                    <?php if (count($recentActivity) > 0): ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Activity</th>
                                    <th>Description</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentActivity as $activity): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($activity->activity_text); ?></td>
                                        <td><?php echo htmlspecialchars($activity->description); ?></td>
                                        <td><?php echo date('M d, Y h:i A', strtotime($activity->activity_time)); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">📭</div>
                            <h3 class="empty-title">No Recent Activity</h3>
                            <p class="empty-text">There's no recent activity to display.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions-container">
                    <div class="section-header">
                        <h3 class="section-title">Quick Actions</h3>
                    </div>
                    <div class="quick-actions-grid">
                        <a href="pending_claims.php" class="quick-action-card">
                            <div class="quick-action-icon pending">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                            </div>
                            <div class="quick-action-content">
                                <h4>Pending Claims</h4>
                                <p>Review and process claims</p>
                            </div>
                        </a>
                        
                        <a href="posts.php" class="quick-action-card">
                            <div class="quick-action-icon posts">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                                    <line x1="12" y1="22.08" x2="12" y2="12"/>
                                </svg>
                            </div>
                            <div class="quick-action-content">
                                <h4>Manage Posts</h4>
                                <p>View and moderate all items</p>
                            </div>
                        </a>
                        
                        <a href="reports.php" class="quick-action-card">
                            <div class="quick-action-icon reports">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                    <line x1="12" y1="9" x2="12" y2="13"/>
                                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                                </svg>
                            </div>
                            <div class="quick-action-content">
                                <h4>View Reports</h4>
                                <p>Handle flagged content</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
