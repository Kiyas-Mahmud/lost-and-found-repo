<?php
require_once '../config/session.php';
requireAdmin();

$userName = $_SESSION['full_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Lost & Found</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <nav class="dashboard-nav">
            <div class="nav-brand">
                <h1 class="auth-logo">L&F Admin</h1>
            </div>
            <div class="nav-links">
                <a href="dashboard.php" class="nav-link active">Dashboard</a>
                <a href="users.php" class="nav-link">Manage Users</a>
                <a href="items.php" class="nav-link">Manage Items</a>
                <a href="reports.php" class="nav-link">Reports</a>
                <a href="../logout.php" class="nav-link">Logout</a>
            </div>
        </nav>

        <main class="dashboard-main">
            <div class="welcome-section">
                <h2>Admin Dashboard</h2>
                <p>Welcome back, <?php echo htmlspecialchars($userName); ?></p>
            </div>

            <div class="dashboard-grid">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-info">
                        <h3>0</h3>
                        <p>Total Users</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">📦</div>
                    <div class="stat-info">
                        <h3>0</h3>
                        <p>Total Items</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">✅</div>
                    <div class="stat-info">
                        <h3>0</h3>
                        <p>Resolved Items</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">⚠️</div>
                    <div class="stat-info">
                        <h3>0</h3>
                        <p>Pending Reports</p>
                    </div>
                </div>
            </div>

            <div class="quick-actions">
                <h3>System Overview</h3>
                <div class="action-buttons">
                    <a href="users.php" class="btn-primary">👥 Manage Users</a>
                    <a href="items.php" class="btn-secondary">📦 View All Items</a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
