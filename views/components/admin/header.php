<header class="admin-header">
    <div class="header-left">
        <h2 class="page-title"><?php echo $pageTitle ?? 'Admin Panel'; ?></h2>
    </div>
    
    <div class="header-center">
        <div class="header-search">
            <span class="search-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
            </span>
            <input type="text" placeholder="Search..." class="search-input" id="globalSearch">
        </div>
    </div>
    
    <div class="header-right">
        <div class="header-actions">
            <button class="header-btn" title="Notifications">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                <span class="notification-badge">3</span>
            </button>
            
            <div class="profile-dropdown">
                <button class="header-btn profile-btn">
                    <div class="profile-avatar-sm"><?php echo strtoupper(substr($_SESSION['full_name'] ?? 'A', 0, 1)); ?></div>
                </button>
            </div>
            
            <a href="../../logout.php" class="header-btn logout-btn" title="Logout">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
            </a>
        </div>
    </div>
</header>
