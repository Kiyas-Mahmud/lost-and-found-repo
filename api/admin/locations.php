<?php
require_once __DIR__ . '/../../api/base.php';
require_once __DIR__ . '/../../controllers/admin/locations.php';

// Check admin authentication
$session = checkAdmin();

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

$locations = new LocationsController();

// Handle GET requests (list all locations)
if ($method === 'GET') {
    try {
        $result = $locations->getAllLocations();
        jsonSuccess($result, 'Locations loaded successfully');
        
    } catch (Exception $e) {
        jsonError('Failed to load locations: ' . $e->getMessage(), 500);
    }
}

// Handle POST requests (add/toggle/delete location)
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
            // Validate location name
            if (empty($input['locationName'])) {
                jsonError('Location name is required', 400);
            }
            
            $locationName = sanitize($input['locationName']);
            $success = $locations->addLocation($locationName);
            
            if ($success) {
                jsonSuccess([], 'Location added successfully');
            } else {
                jsonError('Failed to add location: ' . $locations->getLastError(), 500);
            }
        } 
        elseif ($action === 'toggle') {
            // Validate location ID
            if (empty($input['locationId'])) {
                jsonError('Location ID is required', 400);
            }
            
            $locationId = (int)$input['locationId'];
            $success = $locations->toggleLocation($locationId);
            
            if ($success) {
                jsonSuccess([], 'Location status updated successfully');
            } else {
                jsonError('Failed to update location: ' . $locations->getLastError(), 500);
            }
        } 
        elseif ($action === 'delete') {
            // Validate location ID
            if (empty($input['locationId'])) {
                jsonError('Location ID is required', 400);
            }
            
            $locationId = (int)$input['locationId'];
            $success = $locations->deleteLocation($locationId);
            
            if ($success) {
                jsonSuccess([], 'Location deleted successfully');
            } else {
                jsonError('Failed to delete location: ' . $locations->getLastError(), 500);
            }
        } 
        else {
            jsonError('Invalid action', 400);
        }
        
    } catch (Exception $e) {
        jsonError('Failed to process location action: ' . $e->getMessage(), 500);
    }
}

// Handle unsupported methods
else {
    jsonError('Method not allowed', 405);
}
