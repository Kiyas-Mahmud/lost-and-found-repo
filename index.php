<?php
/**
 * University Lost & Found Management Platform
 * Main Entry Point & Router
 */

// Start session for authentication
session_start();

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base paths
define('BASE_PATH', __DIR__);
define('VIEWS_PATH', BASE_PATH . '/views');
define('CONTROLLERS_PATH', BASE_PATH . '/controllers');
define('MODELS_PATH', BASE_PATH . '/models');
define('UPLOADS_PATH', BASE_PATH . '/uploads');

// Include database configuration
require_once BASE_PATH . '/config/db.php';

// Helper functions
require_once BASE_PATH . '/config/helpers.php';

/**
 * Simple Router
 * Handles URL routing based on 'page' parameter
 */
function route() {
    // Get the requested page
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
    $action = isset($_GET['action']) ? $_GET['action'] : 'index';
    
    // Route mapping
    $routes = [
        // Public routes
        'home' => ['controller' => 'home', 'layout' => 'public'],
        'browse' => ['view' => 'views/student/browse.php'],
        'item_details' => ['view' => 'views/student/item_details.php'],
        'login' => ['controller' => 'auth', 'action' => 'login', 'layout' => 'public'],
        'register' => ['controller' => 'auth', 'action' => 'register', 'layout' => 'public'],
        'logout' => ['controller' => 'auth', 'action' => 'logout', 'layout' => 'public'],
        
        // Student routes
        'student_dashboard' => ['view' => 'views/student/dashboard.php'],
        'dashboard' => ['view' => 'views/student/dashboard.php'],
        'post_lost' => ['view' => 'views/student/post_lost.php'],
        'post_found' => ['view' => 'views/student/post_found.php'],
        'my_posts' => ['view' => 'views/student/my_posts.php'],
        'my_claims' => ['view' => 'views/student/my_claims.php'],
        'claim_item' => ['controller' => 'student/claim', 'action' => 'claim', 'layout' => 'student', 'auth' => 'student'],
        'notifications' => ['controller' => 'student/notification', 'layout' => 'student', 'auth' => 'student'],
        
        // Admin routes
        'admin_dashboard' => ['controller' => 'admin/dashboard', 'layout' => 'admin', 'auth' => 'admin'],
        'admin_claims' => ['controller' => 'admin/claim', 'action' => 'pending', 'layout' => 'admin', 'auth' => 'admin'],
        'admin_review_claim' => ['controller' => 'admin/claim', 'action' => 'review', 'layout' => 'admin', 'auth' => 'admin'],
        'admin_posts' => ['controller' => 'admin/post', 'layout' => 'admin', 'auth' => 'admin'],
        'admin_reports' => ['controller' => 'admin/report', 'layout' => 'admin', 'auth' => 'admin'],
        'admin_categories' => ['controller' => 'admin/master_data', 'action' => 'categories', 'layout' => 'admin', 'auth' => 'admin'],
        'admin_locations' => ['controller' => 'admin/master_data', 'action' => 'locations', 'layout' => 'admin', 'auth' => 'admin'],
    ];
    
    // Check if route exists
    if (!isset($routes[$page])) {
        // 404 - Page not found
        http_response_code(404);
        include VIEWS_PATH . '/pages/public/404.php';
        return;
    }
    
    $route = $routes[$page];
    
    // Check authentication if required
    if (isset($route['auth'])) {
        if ($route['auth'] === 'student') {
            check_student_auth();
        } elseif ($route['auth'] === 'admin') {
            check_admin_auth();
        }
    }
    
    // Check if route has direct view file
    if (isset($route['view'])) {
        $view_file = BASE_PATH . '/' . $route['view'];
        if (file_exists($view_file)) {
            include $view_file;
        } else {
            http_response_code(404);
            echo "View not found: " . htmlspecialchars($route['view']);
        }
        return;
    }
    
    // Set controller and action
    $controller = $route['controller'];
    $action = isset($route['action']) ? $route['action'] : $action;
    $layout = $route['layout'];
    
    // Load controller
    $controller_file = CONTROLLERS_PATH . '/' . $controller . '.php';
    
    if (file_exists($controller_file)) {
        // Include controller
        require_once $controller_file;
        
        // Call action function if it exists
        $action_function = $action;
        if (function_exists($action_function)) {
            $action_function();
        } else {
            // Default action
            index();
        }
    } else {
        // Controller not found
        http_response_code(404);
        echo "Controller not found: " . htmlspecialchars($controller);
    }
}

/**
 * Load layout wrapper
 */
function load_layout($layout, $content_file, $data = []) {
    // Extract data for views
    extract($data);
    
    // Start output buffering for content
    ob_start();
    include $content_file;
    $content = ob_get_clean();
    
    // Load layout
    $layout_file = VIEWS_PATH . '/layouts/' . $layout . '_layout.php';
    if (file_exists($layout_file)) {
        include $layout_file;
    } else {
        echo $content; // Fallback: output content directly
    }
}

// Execute router
route();
