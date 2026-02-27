<?php
// Check authentication
require_once '../../config/helpers.php';
require_once '../../config/session.php';
requireAdmin();

// Load controller
require_once '../../controllers/admin/locations.php';

$pageTitle = 'Locations';

// Initialize controller
$controller = new LocationsController();

// Handle add/edit/toggle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add' && !empty($_POST['location_name'])) {
            $success = $controller->addLocation($_POST['location_name']);
            if ($success) {
                set_flash('success', 'Location added successfully!');
                header('Location: locations.php');
            } else {
                set_flash('error', $controller->getLastError() ?: 'Failed to add location');
                header('Location: locations.php');
            }
            exit();
        } elseif ($_POST['action'] === 'edit' && !empty($_POST['location_id']) && !empty($_POST['location_name'])) {
            $success = $controller->updateLocation($_POST['location_id'], $_POST['location_name']);
            if ($success) {
                set_flash('success', 'Location updated successfully!');
                header('Location: locations.php');
            } else {
                set_flash('error', $controller->getLastError() ?: 'Failed to update location');
                header('Location: locations.php');
            }
            exit();
        } elseif ($_POST['action'] === 'toggle' && !empty($_POST['location_id'])) {
            $success = $controller->toggleLocation($_POST['location_id']);
            if ($success) {
                set_flash('success', 'Location status updated successfully!');
                header('Location: locations.php');
            } else {
                set_flash('error', $controller->getLastError() ?: 'Failed to update location status');
                header('Location: locations.php');
            }
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
                <?php include '../components/common/flash_message.php'; ?>
                
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

                <!-- Filters & Search -->
                <?php
                $filterConfig = [
                    'searchPlaceholder' => 'Search locations...',
                    'filters' => [
                        [
                            'id' => 'filter-status',
                            'options' => [
                                '' => 'All Status',
                                '1' => 'Active',
                                '0' => 'Inactive'
                            ]
                        ]
                    ],
                    'showDateFilter' => false
                ];
                include '../components/admin/filter_search.php';
                ?>

                <!-- Locations Table -->
                <div class="admin-table-container">
                    <div class="table-wrapper" id="table-wrapper">
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
                            <tbody id="locations-tbody">
                                <?php foreach ($locations as $loc): ?>
                                    <tr class="location-row" data-location-name="<?php echo strtolower(htmlspecialchars($loc->location_name)); ?>" data-status="<?php echo $loc->is_active ? '1' : '0'; ?>">
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
                                            <div class="table-actions">
                                                <button 
                                                    type="button" 
                                                    class="btn-secondary-sm" 
                                                    onclick="openEditModal(<?php echo $loc->location_id; ?>, '<?php echo htmlspecialchars($loc->location_name, ENT_QUOTES); ?>')"
                                                >
                                                    Edit
                                                </button>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="toggle">
                                                    <input type="hidden" name="location_id" value="<?php echo $loc->location_id; ?>">
                                                    <button type="submit" class="btn-secondary-sm">
                                                        <?php echo $loc->is_active ? 'Deactivate' : 'Activate'; ?>
                                                    </button>
                                                </form>
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
                        <h3 class="empty-title">No Locations Found</h3>
                        <p class="empty-text">No locations match your search criteria.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../components/modals/add_location_modal.php'; ?>

    <!-- Edit Location Modal -->
    <div id="editModal" class="modal-backdrop">
        <div class="modal-dialog">
            <div class="modal-content-claim">
                <button class="modal-close-button" onclick="closeEditModal()">
                    <i class="fas fa-times"></i>
                </button>
                
                <h2 class="modal-heading">Edit Location</h2>
                <p class="modal-subtext">Update location information</p>
                
                <form method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" id="edit_location_id" name="location_id">
                    <div class="form-group">
                        <label for="edit_location_name" class="form-label">Location Name <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            id="edit_location_name" 
                            name="location_name" 
                            class="form-control" 
                            placeholder="Enter location name"
                            required
                        >
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Location</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/admin/locations.js"></script>
</body>
</html>
