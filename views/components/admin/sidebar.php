<aside class="admin-sidebar">
    <div class="sidebar-header">
        <h3><i class="fas fa-shield-alt"></i> Admin Panel</h3>
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <li class="<?php echo ($page ?? '') === 'admin_dashboard' ? 'active' : ''; ?>">
                <a href="index.php?page=admin_dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <li class="nav-divider">Claims Management</li>
            
            <li class="<?php echo ($page ?? '') === 'admin_claims' ? 'active' : ''; ?>">
                <a href="index.php?page=admin_claims">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Pending Claims</span>
                    <?php
                    // TODO: Get pending claims count
                    $pending_count = 0;
                    if ($pending_count > 0):
                    ?>
                        <span class="badge badge-warning"><?php echo $pending_count; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <li class="nav-divider">Content Management</li>
            
            <li class="<?php echo ($page ?? '') === 'admin_posts' ? 'active' : ''; ?>">
                <a href="index.php?page=admin_posts">
                    <i class="fas fa-list"></i>
                    <span>All Posts</span>
                </a>
            </li>
            
            <li class="<?php echo ($page ?? '') === 'admin_reports' ? 'active' : ''; ?>">
                <a href="index.php?page=admin_reports">
                    <i class="fas fa-flag"></i>
                    <span>Reports</span>
                    <?php
                    // TODO: Get open reports count
                    $reports_count = 0;
                    if ($reports_count > 0):
                    ?>
                        <span class="badge badge-danger"><?php echo $reports_count; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <li class="nav-divider">Master Data</li>
            
            <li class="<?php echo ($page ?? '') === 'admin_categories' ? 'active' : ''; ?>">
                <a href="index.php?page=admin_categories">
                    <i class="fas fa-tags"></i>
                    <span>Categories</span>
                </a>
            </li>
            
            <li class="<?php echo ($page ?? '') === 'admin_locations' ? 'active' : ''; ?>">
                <a href="index.php?page=admin_locations">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Locations</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
