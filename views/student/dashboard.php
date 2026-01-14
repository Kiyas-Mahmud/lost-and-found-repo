<?php
require_once '../../config/session.php';
requireLogin();

// Only allow students
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'STUDENT') {
    header('Location: ../login.php');
    exit;
}

$userName = $_SESSION['full_name'] ?? 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Lost & Found</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <nav class="dashboard-nav">
            <div class="nav-brand">
                <h1 class="auth-logo">L&F</h1>
            </div>
            <div class="nav-links">
                <a href="dashboard.php" class="nav-link active">Dashboard</a>
                <a href="browse.php" class="nav-link">Browse Items</a>
                <a href="my-items.php" class="nav-link">My Items</a>
                <a href="my-claims.php" class="nav-link">My Claims</a>
                <a href="../../logout.php" class="nav-link">Logout</a>
            </div>
        </nav>

        <main class="dashboard-main">
            <div class="welcome-section">
                <h2>Welcome back, <?php echo htmlspecialchars($userName); ?>!</h2>
                <p>Manage your lost and found items</p>
            </div>

            <div class="dashboard-grid">
                <div class="stat-card">
                    <div class="stat-icon">📦</div>
                    <div class="stat-info">
                        <h3>0</h3>
                        <p>My Posted Items</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">🔍</div>
                    <div class="stat-info">
                        <h3>0</h3>
                        <p>My Claims</p>
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
                    <div class="stat-icon">🔔</div>
                    <div class="stat-info">
                        <h3>0</h3>
                        <p>Notifications</p>
                    </div>
                </div>
            </div>

            <div class="quick-actions">
                <h3>Quick Actions</h3>
                <div class="action-buttons">
                    <a href="post-item.php" class="btn-primary">📝 Post Lost/Found Item</a>
                    <a href="browse.php" class="btn-secondary">🔍 Browse Items</a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
