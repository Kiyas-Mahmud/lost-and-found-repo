<?php
// Check authentication - Only ADMINISTRATOR can access
require_once '../../config/helpers.php';
require_once '../../config/session.php';
requireAdmin();

if ($_SESSION['role'] !== 'ADMINISTRATOR') {
    set_flash('error', 'Access denied. Only administrators can access this page.');
    header('Location: settings.php');
    exit();
}

// Load controller
require_once '../../controllers/admin/settings.php';

$pageTitle = 'Staff List';
$controller = new SettingsController();

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $userId = $_POST['user_id'] ?? 0;
    if ($userId) {
        $success = $controller->deleteStaff($userId);
        if ($success) {
            set_flash('success', 'Staff member deleted successfully!');
        } else {
            set_flash('error', $controller->getLastError() ?: 'Failed to delete staff member');
        }
        header('Location: staff-list.php');
        exit();
    }
}

// Handle edit action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $userId = $_POST['user_id'] ?? 0;
    $fullName = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $errors = [];
    
    if (empty($fullName)) $errors[] = 'Full name is required';
    if (empty($username)) $errors[] = 'Username is required';
    if (empty($role)) $errors[] = 'Role is required';
    
    if (empty($errors)) {
        $success = $controller->updateStaff($userId, $fullName, $username, $role, !empty($password) ? $password : null);
        if ($success) {
            set_flash('success', 'Staff member updated successfully!');
            header('Location: staff-list.php');
            exit();
        } else {
            set_flash('error', $controller->getLastError() ?: 'Failed to update staff member');
        }
    } else {
        set_flash('error', implode('<br>', $errors));
    }
}

// Get all staff members
$staff = $controller->getAllStaff();
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
                <?php include '../components/common/flash_message.php'; ?>
                
                <!-- Page Header -->
                <div class="page-header">
                    <div>
                        <h2 class="page-title-large">Staff List</h2>
                        <p class="page-subtitle">Manage all staff members and moderators</p>
                    </div>
                    <div class="page-actions">
                        <a href="add-staff.php" class="btn-primary">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Add Staff Member
                        </a>
                    </div>
                </div>

                <!-- Filters & Search -->
                <?php
                $filterConfig = [
                    'searchPlaceholder' => 'Search by name, email, or username...',
                    'filters' => [
                        [
                            'id' => 'filter-role',
                            'options' => [
                                '' => 'All Roles',
                                'ADMINISTRATOR' => 'Administrator',
                                'MODERATOR' => 'Moderator',
                                'STAFF' => 'Staff'
                            ]
                        ]
                    ],
                    'showDateFilter' => false
                ];
                include '../components/admin/filter_search.php';
                ?>

                <div class="admin-table-container">
                    <div class="table-wrapper" id="table-wrapper">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="staff-tbody">
                                <?php foreach ($staff as $member): ?>
                                    <tr class="staff-row" 
                                        data-search="<?php echo strtolower($member->full_name . ' ' . $member->email . ' ' . ($member->username ?? '')); ?>"
                                        data-role="<?php echo $member->role; ?>">
                                        <td><span class="text-mono">#<?php echo $member->user_id; ?></span></td>
                                        <td>
                                            <div class="table-item-title">
                                                <?php echo htmlspecialchars($member->full_name); ?>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($member->email); ?></td>
                                        <td><?php echo htmlspecialchars($member->username ?? 'N/A'); ?></td>
                                        <td>
                                            <?php if ($member->role === 'ADMINISTRATOR'): ?>
                                                <span class="badge-success">Administrator</span>
                                            <?php elseif ($member->role === 'MODERATOR'): ?>
                                                <span class="badge-info">Moderator</span>
                                            <?php else: ?>
                                                <span class="badge-secondary">Staff</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($member->account_status === 'ACTIVE'): ?>
                                                <span class="badge-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge-secondary"><?php echo $member->account_status; ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="table-date">
                                                <?php echo date('M d, Y', strtotime($member->created_at)); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="table-actions">
                                                <?php if ($member->user_id != $_SESSION['user_id']): ?>
                                                    <button 
                                                        type="button" 
                                                        class="btn-secondary-sm" 
                                                        onclick="openEditModal(<?php echo $member->user_id; ?>, '<?php echo htmlspecialchars($member->full_name, ENT_QUOTES); ?>', '<?php echo htmlspecialchars($member->username ?? '', ENT_QUOTES); ?>', '<?php echo $member->role; ?>')"
                                                    >
                                                        Edit
                                                    </button>
                                                    <button 
                                                        type="button" 
                                                        class="btn-danger-sm" 
                                                        onclick="confirmDelete(<?php echo $member->user_id; ?>, '<?php echo htmlspecialchars($member->full_name, ENT_QUOTES); ?>')"
                                                    >
                                                        Delete
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-muted">Current User</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div class="empty-state" id="empty-state" style="display: none;">
                        <div class="empty-icon">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.35-4.35"/>
                            </svg>
                        </div>
                        <h3 class="empty-title">No Staff Found</h3>
                        <p class="empty-text">No staff members match your search criteria.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Staff Modal -->
    <div id="editModal" class="modal-backdrop">
        <div class="modal-dialog">
            <div class="modal-content-claim">
                <button class="modal-close-button" onclick="closeEditModal()">
                    <i class="fas fa-times"></i>
                </button>
                
                <h2 class="modal-heading">Edit Staff Member</h2>
                <p class="modal-subtext">Update staff member information</p>
                
                <form method="POST" id="editForm">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" id="edit_user_id" name="user_id">
                    
                    <div class="form-group">
                        <label for="edit_full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            id="edit_full_name" 
                            name="full_name" 
                            class="form-control" 
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="edit_username" class="form-label">Username <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            id="edit_username" 
                            name="username" 
                            class="form-control" 
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="edit_role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select id="edit_role" name="role" class="form-control" required>
                            <option value="ADMINISTRATOR">Administrator</option>
                            <option value="MODERATOR">Moderator</option>
                            <option value="STAFF">Staff</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_password" class="form-label">New Password (Optional)</label>
                        <input 
                            type="password" 
                            id="edit_password" 
                            name="password" 
                            class="form-control" 
                            placeholder="Leave blank to keep current password"
                        >
                    </div>

                    <div class="modal-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Staff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Form -->
    <form method="POST" id="deleteForm" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" id="delete_user_id" name="user_id">
    </form>

    <script src="../../assets/js/main.js"></script>
    <script>
        // Filter functionality
        const searchInput = document.getElementById('filter-search');
        const roleFilter = document.getElementById('filter-role');
        const clearBtn = document.getElementById('clear-filters');
        const staffRows = document.querySelectorAll('.staff-row');

        let searchTimeout;

        // Apply filters
        function applyFilters() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const roleValue = roleFilter.value;
            let visibleCount = 0;

            staffRows.forEach(row => {
                const searchText = row.dataset.search;
                const role = row.dataset.role;
                
                const matchesSearch = !searchTerm || searchText.includes(searchTerm);
                const matchesRole = !roleValue || role === roleValue;

                if (matchesSearch && matchesRole) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            updateEmptyState(visibleCount);
        }

        // Toggle clear button
        function toggleClearButton() {
            const hasFilters = searchInput.value || roleFilter.value;
            clearBtn.style.display = hasFilters ? 'inline-flex' : 'none';
        }

        // Update empty state
        function updateEmptyState(visibleCount) {
            const tableWrapper = document.getElementById('table-wrapper');
            const emptyState = document.getElementById('empty-state');
            
            if (visibleCount === 0) {
                tableWrapper.style.display = 'none';
                emptyState.style.display = 'flex';
            } else {
                tableWrapper.style.display = 'block';
                emptyState.style.display = 'none';
            }
        }

        // Search with debounce
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFilters();
                toggleClearButton();
            }, 300);
        });

        // Role filter
        roleFilter.addEventListener('change', () => {
            applyFilters();
            toggleClearButton();
        });

        // Clear filters
        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            roleFilter.value = '';
            applyFilters();
            toggleClearButton();
        });

        // Edit modal functions
        function openEditModal(userId, fullName, username, role) {
            document.getElementById('edit_user_id').value = userId;
            document.getElementById('edit_full_name').value = fullName;
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_role').value = role;
            document.getElementById('edit_password').value = '';
            const modal = document.getElementById('editModal');
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            const modal = document.getElementById('editModal');
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }

        // Delete confirmation
        function confirmDelete(userId, fullName) {
            if (confirm(`Are you sure you want to delete ${fullName}? This action cannot be undone.`)) {
                document.getElementById('delete_user_id').value = userId;
                document.getElementById('deleteForm').submit();
            }
        }

        // Close modal on backdrop click
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // Close modal on Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditModal();
            }
        });
    </script>
</body>
</html>
