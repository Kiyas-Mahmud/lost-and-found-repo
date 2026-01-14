<nav class="navbar navbar-admin">
    <div class="container">
        <div class="navbar-brand">
            <a href="index.php?page=admin_dashboard">
                <i class="fas fa-shield-alt"></i>
                <span>Admin Panel</span>
            </a>
        </div>
        
        <div class="navbar-menu">
            <ul class="navbar-nav">
                <li><a href="index.php?page=admin_dashboard" class="<?php echo ($page ?? '') === 'admin_dashboard' ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="index.php?page=admin_claims" class="<?php echo ($page ?? '') === 'admin_claims' ? 'active' : ''; ?>">Pending Claims</a></li>
                <li><a href="index.php?page=admin_posts" class="<?php echo ($page ?? '') === 'admin_posts' ? 'active' : ''; ?>">All Posts</a></li>
                <li><a href="index.php?page=admin_reports" class="<?php echo ($page ?? '') === 'admin_reports' ? 'active' : ''; ?>">Reports</a></li>
                
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle">
                        Master Data <i class="fas fa-chevron-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="index.php?page=admin_categories"><i class="fas fa-tags"></i> Categories</a></li>
                        <li><a href="index.php?page=admin_locations"><i class="fas fa-map-marker-alt"></i> Locations</a></li>
                    </ul>
                </li>
                
                <li><a href="index.php?page=browse" target="_blank" class="btn btn-sm btn-outline">View Site</a></li>
                
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle">
                        <i class="fas fa-user-shield"></i> <?php echo $_SESSION['full_name'] ?? 'Admin'; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="index.php?page=logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        
        <button class="navbar-toggle" id="navbarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</nav>
