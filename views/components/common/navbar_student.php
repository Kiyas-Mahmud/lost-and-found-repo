<nav class="navbar navbar-student">
    <div class="container">
        <div class="navbar-brand">
            <a href="index.php?page=student_dashboard">
                <i class="fas fa-search"></i>
                <span>Lost & Found</span>
            </a>
        </div>
        
        <div class="navbar-menu">
            <ul class="navbar-nav">
                <li><a href="index.php?page=browse" class="<?php echo ($page ?? '') === 'browse' ? 'active' : ''; ?>">Browse</a></li>
                
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle">
                        Post Item <i class="fas fa-chevron-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="index.php?page=post_lost"><i class="fas fa-exclamation-circle"></i> Post Lost Item</a></li>
                        <li><a href="index.php?page=post_found"><i class="fas fa-check-circle"></i> Post Found Item</a></li>
                    </ul>
                </li>
                
                <li><a href="index.php?page=student_dashboard" class="<?php echo ($page ?? '') === 'student_dashboard' ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="index.php?page=my_posts" class="<?php echo ($page ?? '') === 'my_posts' ? 'active' : ''; ?>">My Posts</a></li>
                <li><a href="index.php?page=my_claims" class="<?php echo ($page ?? '') === 'my_claims' ? 'active' : ''; ?>">My Claims</a></li>
                
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle">
                        <i class="fas fa-bell"></i>
                        <?php
                        // TODO: Get unread notification count
                        $unread_count = 0;
                        if ($unread_count > 0):
                        ?>
                            <span class="badge badge-danger"><?php echo $unread_count; ?></span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="index.php?page=notifications"><i class="fas fa-bell"></i> Notifications</a></li>
                    </ul>
                </li>
                
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle">
                        <i class="fas fa-user-circle"></i> <?php echo $_SESSION['full_name'] ?? 'User'; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="index.php?page=student_dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li class="divider"></li>
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
