<nav class="navbar navbar-public">
    <div class="container">
        <div class="navbar-brand">
            <a href="index.php">
                <span class="logo-text">L&F</span>
            </a>
        </div>
        
        <div class="navbar-menu">
            <ul class="navbar-nav">
                <li><a href="index.php?page=home" class="<?php echo ($page ?? '') === 'home' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="index.php?page=browse" class="<?php echo ($page ?? '') === 'browse' ? 'active' : ''; ?>">Browse Items</a></li>
                
                <?php if (is_logged_in()): ?>
                    <?php if (is_student()): ?>
                        <li class="nav-link-item"><a href="index.php?page=student_dashboard">Dashboard</a></li>
                    <?php elseif (is_admin()): ?>
                        <li class="nav-link-item"><a href="index.php?page=admin_dashboard">Admin Panel</a></li>
                    <?php endif; ?>
                    <li class="nav-btn-item"><a href="logout.php" class="btn btn-outline">Logout</a></li>
                <?php else: ?>
                    <li class="nav-btn-item"><a href="index.php?page=login" class="btn btn-outline">Login</a></li>
                    <li class="nav-btn-item"><a href="index.php?page=register" class="btn btn-primary">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
        
        <button class="navbar-toggle" id="navbarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</nav>
