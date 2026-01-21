<?php
require_once __DIR__ . '/../../api/base.php';
require_once __DIR__ . '/../../controllers/admin/posts.php';

// Check admin authentication
$session = checkAdmin();

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

$posts = new PostsController();

// Handle GET requests (list posts)
if ($method === 'GET') {
    try {
        // Get filters and pagination from query params
        $filters = [
            'type' => $_GET['type'] ?? '',
            'status' => $_GET['status'] ?? '',
            'date' => $_GET['date'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 20;
        
        // Get posts
        $result = $posts->getAllPosts($filters, $page, $perPage);
        
        jsonSuccess($result, 'Posts loaded successfully');
        
    } catch (Exception $e) {
        jsonError('Failed to load posts: ' . $e->getMessage(), 500);
    }
}

// Handle POST requests (hide/unhide/delete post)
elseif ($method === 'POST') {
    try {
        $input = getJsonInput();
        
        // Validate required fields
        $errors = validateRequired($input, ['itemId', 'action']);
        if (!empty($errors)) {
            jsonError('Validation failed', 400, $errors);
        }
        
        $itemId = (int)$input['itemId'];
        $action = sanitize($input['action']);
        $reason = sanitize($input['reason'] ?? '');
        
        // Perform action
        if ($action === 'hide') {
            $success = $posts->hidePost($itemId, $reason);
            if ($success) {
                jsonSuccess([], 'Post hidden successfully');
            } else {
                jsonError('Failed to hide post: ' . $posts->getLastError(), 500);
            }
        } 
        elseif ($action === 'unhide') {
            $success = $posts->unhidePost($itemId);
            if ($success) {
                jsonSuccess([], 'Post unhidden successfully');
            } else {
                jsonError('Failed to unhide post: ' . $posts->getLastError(), 500);
            }
        } 
        elseif ($action === 'markReturned') {
            $success = $posts->markAsReturned($itemId);
            if ($success) {
                jsonSuccess([], 'Item marked as returned successfully');
            } else {
                jsonError('Failed to mark item as returned: ' . $posts->getLastError(), 500);
            }
        }
        elseif ($action === 'delete') {
            $success = $posts->deletePost($itemId);
            if ($success) {
                jsonSuccess([], 'Post deleted successfully');
            } else {
                jsonError('Failed to delete post: ' . $posts->getLastError(), 500);
            }
        } 
        else {
            jsonError('Invalid action', 400);
        }
        
    } catch (Exception $e) {
        jsonError('Failed to process post action: ' . $e->getMessage(), 500);
    }
}

// Handle unsupported methods
else {
    jsonError('Method not allowed', 405);
}
