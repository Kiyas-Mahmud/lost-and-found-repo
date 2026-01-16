<?php
require_once __DIR__ . '/../../api/base.php';
require_once __DIR__ . '/../../controllers/admin/categories.php';

// Check admin authentication
$session = checkAdmin();

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

$categories = new CategoriesController();

// Handle GET requests (list all categories)
if ($method === 'GET') {
    try {
        $result = $categories->getAllCategories();
        jsonSuccess($result, 'Categories loaded successfully');
        
    } catch (Exception $e) {
        jsonError('Failed to load categories: ' . $e->getMessage(), 500);
    }
}

// Handle POST requests (add/toggle/delete category)
elseif ($method === 'POST') {
    try {
        $input = getJsonInput();
        
        // Validate required fields
        $errors = validateRequired($input, ['action']);
        if (!empty($errors)) {
            jsonError('Validation failed', 400, $errors);
        }
        
        $action = sanitize($input['action']);
        
        // Perform action
        if ($action === 'add') {
            // Validate category name
            if (empty($input['categoryName'])) {
                jsonError('Category name is required', 400);
            }
            
            $categoryName = sanitize($input['categoryName']);
            $success = $categories->addCategory($categoryName);
            
            if ($success) {
                jsonSuccess([], 'Category added successfully');
            } else {
                jsonError('Failed to add category: ' . $categories->getLastError(), 500);
            }
        } 
        elseif ($action === 'toggle') {
            // Validate category ID
            if (empty($input['categoryId'])) {
                jsonError('Category ID is required', 400);
            }
            
            $categoryId = (int)$input['categoryId'];
            $success = $categories->toggleCategory($categoryId);
            
            if ($success) {
                jsonSuccess([], 'Category status updated successfully');
            } else {
                jsonError('Failed to update category: ' . $categories->getLastError(), 500);
            }
        } 
        elseif ($action === 'delete') {
            // Validate category ID
            if (empty($input['categoryId'])) {
                jsonError('Category ID is required', 400);
            }
            
            $categoryId = (int)$input['categoryId'];
            $success = $categories->deleteCategory($categoryId);
            
            if ($success) {
                jsonSuccess([], 'Category deleted successfully');
            } else {
                jsonError('Failed to delete category: ' . $categories->getLastError(), 500);
            }
        } 
        else {
            jsonError('Invalid action', 400);
        }
        
    } catch (Exception $e) {
        jsonError('Failed to process category action: ' . $e->getMessage(), 500);
    }
}

// Handle unsupported methods
else {
    jsonError('Method not allowed', 405);
}
