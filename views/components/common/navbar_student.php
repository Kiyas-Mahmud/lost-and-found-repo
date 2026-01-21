<?php
// Dynamic base path - work from anywhere
$nav_base = '';
if (strpos($_SERVER['PHP_SELF'], '/views/') !== false) {
    // We're in a views subdirectory, go up to root
    $nav_base = '../../';
} elseif (defined('BASE_PATH')) {
    // We're using the router
    $nav_base = '';
}

// Check if user is logged in
$is_user_logged_in = isset($_SESSION['user_id']);
// Check if we're on a student page
$is_student_page = isset($page) && in_array($page, ['dashboard', 'browse', 'my_posts', 'my_claims', 'my_reports', 'post_lost', 'post_found']);
?>
<nav class="navbar navbar-student">
    <div class="container">
        <div class="navbar-brand">
            <a href="<?php echo $nav_base; ?>index.php">
                <span class="logo-text">L&F</span>
            </a>
        </div>
        
        <div class="navbar-menu">
            <ul class="navbar-nav">
                <?php if (!$is_user_logged_in): ?>
                    <!-- Not Logged In Users -->
                    <li><a href="<?php echo $nav_base; ?>index.php" class="<?php echo ($page ?? '') === 'home' ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="<?php echo $nav_base; ?>views/student/browse.php" class="<?php echo ($page ?? '') === 'browse' ? 'active' : ''; ?>">Browse</a></li>
                    <li class="nav-btn-item"><a href="<?php echo $nav_base; ?>views/login.php" class="btn btn-outline">Login</a></li>
                    <li class="nav-btn-item"><a href="<?php echo $nav_base; ?>views/register.php" class="btn btn-primary">Register</a></li>
                
                <?php elseif ($is_student_page): ?>
                    <!-- Student Dashboard Pages - Full Navigation -->
                    <li><a href="<?php echo $nav_base; ?>views/student/browse.php" class="<?php echo ($page ?? '') === 'browse' ? 'active' : ''; ?>">Browse</a></li>
                    
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle">
                            Post Item <i class="fas fa-chevron-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo $nav_base; ?>views/student/post_lost.php"><i class="fas fa-exclamation-circle"></i> Post Lost Item</a></li>
                            <li><a href="<?php echo $nav_base; ?>views/student/post_found.php"><i class="fas fa-check-circle"></i> Post Found Item</a></li>
                        </ul>
                    </li>
                    
                    <li><a href="<?php echo $nav_base; ?>views/student/dashboard.php" class="<?php echo ($page ?? '') === 'dashboard' ? 'active' : ''; ?>">Dashboard</a></li>
                    <li><a href="<?php echo $nav_base; ?>views/student/my_posts.php" class="<?php echo ($page ?? '') === 'my_posts' ? 'active' : ''; ?>">My Posts</a></li>
                    
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle">
                            <i class="fas fa-user-circle"></i> <?php echo $_SESSION['full_name'] ?? 'User'; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a href="<?php echo $nav_base; ?>views/student/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                            <li><a href="<?php echo $nav_base; ?>views/student/profile.php"><i class="fas fa-user"></i> Profile</a></li>
                            <li><a href="<?php echo $nav_base; ?>views/student/my_claims.php"><i class="fas fa-hand-paper"></i> My Claims</a></li>
                            <li><a href="<?php echo $nav_base; ?>views/student/my_reports.php"><i class="fas fa-flag"></i> My Reports</a></li>
                            <li class="divider"></li>
                            <li><a href="<?php echo $nav_base; ?>logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>
                
                <?php else: ?>
                    <!-- Home/Landing Page After Login - Simple Navigation -->
                    <li><a href="<?php echo $nav_base; ?>index.php" class="<?php echo ($page ?? '') === 'home' ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="<?php echo $nav_base; ?>views/student/browse.php">Browse</a></li>
                    <li><a href="<?php echo $nav_base; ?>views/student/dashboard.php">Dashboard</a></li>
                    <li class="nav-btn-item"><a href="<?php echo $nav_base; ?>logout.php" class="btn btn-outline">Logout</a></li>
                <?php endif; ?>
            </ul>
        </div>
        
        <button class="navbar-toggle" id="navbarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</nav>
