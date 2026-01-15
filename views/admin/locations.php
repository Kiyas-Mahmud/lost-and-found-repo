<?php
// Load controller
require_once '../../controllers/admin/locations.php';

$pageTitle = 'Locations';

// Initialize controller
$controller = new LocationsController();

// Handle add/edit/toggle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add' && !empty($_POST['location_name'])) {
            $controller->addLocation($_POST['location_name']);
            header('Location: locations.php?msg=added');
            exit();
        } elseif ($_POST['action'] === 'toggle' && !empty($_POST['location_id'])) {
            $controller->toggleLocation($_POST['location_id']);
            header('Location: locations.php?msg=updated');
            exit();
        }
    }
}

// Get all locations from controller
$locations = $controller->getAllLocations();
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
                        <h2 class="page-title-large">Locations</h2>
                        <p class="page-subtitle">Manage campus locations</p>
                    </div>
                    <div class="page-actions">
                        <button type="button" class="btn-primary" onclick="openModal('addModal')">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Add Location
                        </button>
                    </div>
                </div>

                <!-- Locations Table -->
                <div class="admin-table-container">
                    <div class="table-wrapper">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Location Name</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($locations as $loc): ?>
                                    <tr>
                                        <td><span class="text-mono">#<?php echo $loc->location_id; ?></span></td>
                                        <td>
                                            <div class="table-item-title">
                                                <?php echo htmlspecialchars($loc->location_name); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($loc->is_active): ?>
                                                <span class="badge-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="table-date">
                                                <?php echo date('M d, Y', strtotime($loc->created_at)); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="toggle">
                                                <input type="hidden" name="location_id" value="<?php echo $loc->location_id; ?>">
                                                <button type="submit" class="btn-secondary-sm">
                                                    <?php echo $loc->is_active ? 'Deactivate' : 'Activate'; ?>
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

    <!-- Add Location Modal -->
    <div id="addModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Add New Location</h3>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="location_name">Location Name</label>
                        <input type="text" 
                               id="location_name" 
                               name="location_name" 
                               class="form-control" 
                               required
                               placeholder="Enter location name...">
                    </div>
                    <input type="hidden" name="action" value="add">
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn-cancel" onclick="closeModal('addModal')">Cancel</button>
                    <button type="submit" class="modal-btn-confirm">Add Location</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
