<?php
// Check authentication
require_once '../../config/helpers.php';
require_once '../../config/session.php';
requireAdmin();

// Load controller
require_once '../../controllers/admin/categories.php';

$pageTitle = 'Categories';

// Initialize controller
$controller = new CategoriesController();

// Handle add/edit/toggle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add' && !empty($_POST['category_name'])) {
            $success = $controller->addCategory($_POST['category_name']);
            if ($success) {
                header('Location: categories.php?msg=added');
            } else {
                header('Location: categories.php?msg=error&error=' . urlencode($controller->getLastError()));
            }
            exit();
        } elseif ($_POST['action'] === 'toggle' && !empty($_POST['category_id'])) {
            $success = $controller->toggleCategory($_POST['category_id']);
            if ($success) {
                header('Location: categories.php?msg=updated');
            } else {
                header('Location: categories.php?msg=error&error=' . urlencode($controller->getLastError()));
            }
            exit();
        }
    }
}

// Get all categories from controller
$categories = $controller->getAllCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin Panel</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="admin-layout">
        <?php include '../components/admin/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include '../components/admin/header.php'; ?>
            
            <div class="admin-content">
                <!-- Page Header -->
                <div class="page-header">
                    <div>
                        <h2 class="page-title-large">Categories</h2>
                        <p class="page-subtitle">Manage item categories</p>
                    </div>
                    <div class="page-actions">
                        <button type="button" class="btn-primary" onclick="openModal('addModal')">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Add Category
                        </button>
                    </div>
                </div>

                <!-- Categories Table -->
                <div class="admin-table-container">
                    <div class="table-wrapper">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Category Name</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $cat): ?>
                                    <tr>
                                        <td><span class="text-mono">#<?php echo $cat->category_id; ?></span></td>
                                        <td>
                                            <div class="table-item-title">
                                                <?php echo htmlspecialchars($cat->category_name); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($cat->is_active): ?>
                                                <span class="badge-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="table-date">
                                                <?php echo date('M d, Y', strtotime($cat->created_at)); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="toggle">
                                                <input type="hidden" name="category_id" value="<?php echo $cat->category_id; ?>">
                                                <button type="submit" class="btn-secondary-sm">
                                                    <?php echo $cat->is_active ? 'Deactivate' : 'Activate'; ?>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../components/modals/add_category_modal.php'; ?>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
