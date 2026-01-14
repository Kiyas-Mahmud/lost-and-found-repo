<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <h1 class="sidebar-logo">L&F</h1>
        <span class="sidebar-subtitle">Admin Panel</span>
    </div>
    
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="sidebar-link <?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>">
            <span class="sidebar-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"/>
                    <rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/>
                    <rect x="3" y="14" width="7" height="7"/>
                </svg>
            </span>
            <span class="sidebar-text">Dashboard</span>
        </a>
        
        <a href="pending_claims.php" class="sidebar-link <?php echo ($currentPage == 'pending_claims.php') ? 'active' : ''; ?>">
            <span class="sidebar-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </span>
            <span class="sidebar-text">Pending Claims</span>
            <?php
            // Placeholder for pending count badge
            $pendingCount = 0;
            if ($pendingCount > 0): ?>
                <span class="sidebar-badge"><?php echo $pendingCount; ?></span>
            <?php endif; ?>
        </a>
        
        <a href="posts.php" class="sidebar-link <?php echo ($currentPage == 'posts.php') ? 'active' : ''; ?>">
            <span class="sidebar-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                </svg>
            </span>
            <span class="sidebar-text">All Posts</span>
        </a>
        
        <a href="reports.php" class="sidebar-link <?php echo ($currentPage == 'reports.php') ? 'active' : ''; ?>">
            <span class="sidebar-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </span>
            <span class="sidebar-text">Reports</span>
        </a>
        
        <a href="categories.php" class="sidebar-link <?php echo ($currentPage == 'categories.php') ? 'active' : ''; ?>">
            <span class="sidebar-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                    <line x1="7" y1="7" x2="7.01" y2="7"/>
                </svg>
            </span>
            <span class="sidebar-text">Categories</span>
        </a>
        
        <a href="locations.php" class="sidebar-link <?php echo ($currentPage == 'locations.php') ? 'active' : ''; ?>">
            <span class="sidebar-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
            </span>
            <span class="sidebar-text">Locations</span>
        </a>
    </nav>
    
    <div class="sidebar-footer">
        <div class="admin-profile">
            <div class="profile-avatar"><?php echo strtoupper(substr($_SESSION['full_name'] ?? 'A', 0, 1)); ?></div>
            <div class="profile-info">
                <div class="profile-name"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Admin'); ?></div>
                <div class="profile-role">Administrator</div>
            </div>
        </div>
        <a href="../../logout.php" class="sidebar-logout">
            <span class="sidebar-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
            </span>
            <span class="sidebar-text">Logout</span>
        </a>
    </div>
</aside>

<button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="3" y1="12" x2="21" y2="12"/>
        <line x1="3" y1="6" x2="21" y2="6"/>
        <line x1="3" y1="18" x2="21" y2="18"/>
    </svg>
</button>
