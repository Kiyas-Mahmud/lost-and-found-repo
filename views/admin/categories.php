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
        } elseif ($_POST['action'] === 'edit' && !empty($_POST['category_id']) && !empty($_POST['category_name'])) {
            $success = $controller->updateCategory($_POST['category_id'], $_POST['category_name']);
            if ($success) {
                header('Location: categories.php?msg=updated');
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

// Get filters from GET parameters
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Get all categories from controller with filters
$categories = $controller->getAllCategories($searchTerm, $statusFilter);
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
                        <button type="button" class="btn-primary" onclick="openAddModal()">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Add Category
                        </button>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-<?php echo $_GET['msg'] === 'error' ? 'danger' : 'success'; ?>">
                        <?php 
                        if ($_GET['msg'] === 'added') echo 'Category added successfully!';
                        elseif ($_GET['msg'] === 'updated') echo 'Category updated successfully!';
                        elseif ($_GET['msg'] === 'error') echo 'Error: ' . htmlspecialchars($_GET['error'] ?? 'Unknown error');
                        ?>
                    </div>
                <?php endif; ?>

                <!-- Filters & Search -->
                <div class="admin-table-container">
                    <?php
                    $filterConfig = [
                        'searchPlaceholder' => 'Search by category name...',
                        'filters' => [
                            [
                                'id' => 'filter-status',
                                'options' => [
                                    '' => 'All Status',
                                    'active' => 'Active',
                                    'inactive' => 'Inactive'
                                ]
                            ]
                        ],
                        'showDateFilter' => false
                    ];
                    include '../components/admin/filter_search.php';
                    ?>

                    <!-- Categories Table -->
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
                                <?php if (empty($categories)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <div class="empty-state">
                                                <p>No categories found</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
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
                                                <div class="table-actions">
                                                    <button 
                                                        type="button" 
                                                        class="btn-secondary-sm" 
                                                        onclick="openEditModal(<?php echo $cat->category_id; ?>, '<?php echo htmlspecialchars($cat->category_name, ENT_QUOTES); ?>')"
                                                    >
                                                        Edit
                                                    </button>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="action" value="toggle">
                                                        <input type="hidden" name="category_id" value="<?php echo $cat->category_id; ?>">
                                                        <button type="submit" class="btn-secondary-sm">
                                                            <?php echo $cat->is_active ? 'Deactivate' : 'Activate'; ?>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div id="addModal" class="modal-backdrop">
        <div class="modal-dialog">
            <div class="modal-content-claim">
                <button class="modal-close-button" onclick="closeAddModal()">
                    <i class="fas fa-times"></i>
                </button>
                
                <h2 class="modal-heading">Add New Category</h2>
                <p class="modal-subtext">Create a new category for organizing items</p>
                
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label for="category_name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            id="category_name" 
                            name="category_name" 
                            class="form-control" 
                            placeholder="e.g., Electronics, Books, Accessories"
                            required
                        >
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editModal" class="modal-backdrop">
        <div class="modal-dialog">
            <div class="modal-content-claim">
                <button class="modal-close-button" onclick="closeEditModal()">
                    <i class="fas fa-times"></i>
                </button>
                
                <h2 class="modal-heading">Edit Category</h2>
                <p class="modal-subtext">Update category information</p>
                
                <form method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" id="edit_category_id" name="category_id">
                    <div class="form-group">
                        <label for="edit_category_name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            id="edit_category_name" 
                            name="category_name" 
                            class="form-control" 
                            placeholder="Enter category name"
                            required
                        >
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/admin/categories.js"></script>
    <script>
        // Auto-hide success/error messages after 5 seconds
        <?php if (isset($_GET['msg'])): ?>
            setTimeout(function() {
                const alert = document.querySelector('.alert');
                if (alert) {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }
            }, 5000);
        <?php endif; ?>
    </script>
</body>
</html>
