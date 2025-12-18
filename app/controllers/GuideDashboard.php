<?php
// Include session helper functions
require_once '../app/helpers/session_helper.php';

class GuideDashboard extends controller {
    private $guideLocationModel;
    
    public function __construct() {
        $this->guideLocationModel = $this->model('GuideLocation');
    }
    
    // Main dashboard view
    public function index() {
        // Check if user is logged in and is a guide
        if (!isLoggedIn() || $_SESSION['user_account_type'] !== 'guide') {
            header('Location: ' . URL_ROOT . '/user/login');
            return;
        }
        
        $guide_id = $_SESSION['user_id'];
        
        // Get guide's location data
        $locations = $this->guideLocationModel->getLocationsByGuide($guide_id);
        $locationCount = $this->guideLocationModel->getLocationCount($guide_id);
        
        $data = [
            'locations' => $locations,
            'location_count' => $locationCount,
            'guide_id' => $guide_id
        ];
        
        $this->view('Guide/guideDash', $data);
    }
    
    // Add new location via AJAX
    public function addLocation() {
        // Check if request is POST and user is logged in guide
        if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isLoggedIn() || $_SESSION['user_account_type'] !== 'guide') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            return;
        }
        
        $guide_id = $_SESSION['user_id'];
        
        // Validate input data
        $location_name = trim($_POST['location_name'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $visit_hours = floatval($_POST['visit_hours'] ?? 1.0);
        $rate_per_hour = floatval($_POST['rate_per_hour'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        
        // Validation
        $errors = [];
        
        if (empty($location_name)) {
            $errors[] = 'Location name is required';
        }
        
        if (empty($city)) {
            $errors[] = 'City is required';
        }
        
        if ($visit_hours <= 0 || $visit_hours > 99.9) {
            $errors[] = 'Visit hours must be between 0.1 and 99.9';
        }
        
        if ($rate_per_hour <= 0) {
            $errors[] = 'Rate per hour must be greater than 0';
        }
        
        if (!empty($errors)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
            return;
        }
        
        // Add location to database
        if ($this->guideLocationModel->addLocation($guide_id, $location_name, $city, $visit_hours, $rate_per_hour, $description)) {
            // Get updated location count
            $newLocationCount = $this->guideLocationModel->getLocationCount($guide_id);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'message' => 'Location added successfully',
                'location_count' => $newLocationCount
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to add location']);
        }
    }
    
    // Get locations for AJAX refresh
    public function getLocations() {
        if (!isLoggedIn() || $_SESSION['user_account_type'] !== 'guide') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        
        $guide_id = $_SESSION['user_id'];
        $locations = $this->guideLocationModel->getLocationsByGuide($guide_id);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'locations' => $locations]);
    }
    
    // Get location details for editing
    public function getLocationDetails($location_id) {
        if (!isLoggedIn() || $_SESSION['user_account_type'] !== 'guide') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        
        $guide_id = $_SESSION['user_id'];
        $location_id = intval($location_id);
        
        if ($location_id <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid location ID']);
            return;
        }
        
        // Check if location belongs to this guide
        if (!$this->guideLocationModel->isOwner($location_id, $guide_id)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Location not found']);
            return;
        }
        
        $location = $this->guideLocationModel->getLocationById($location_id);
        
        if ($location) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'location' => $location]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Location not found']);
        }
    }
    
    // Delete location
    public function deleteLocation() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isLoggedIn() || $_SESSION['user_account_type'] !== 'guide') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            return;
        }
        
        $guide_id = $_SESSION['user_id'];
        $location_id = intval($_POST['location_id'] ?? 0);
        
        if ($location_id <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid location ID']);
            return;
        }
        
        // Check if location belongs to this guide
        if (!$this->guideLocationModel->isOwner($location_id, $guide_id)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Location not found']);
            return;
        }
        
        if ($this->guideLocationModel->deleteLocation($location_id)) {
            $newLocationCount = $this->guideLocationModel->getLocationCount($guide_id);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'message' => 'Location deleted successfully',
                'location_count' => $newLocationCount
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to delete location']);
        }
    }
    
    // Update location
    public function updateLocation() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isLoggedIn() || $_SESSION['user_account_type'] !== 'guide') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            return;
        }
        
        $guide_id = $_SESSION['user_id'];
        $location_id = intval($_POST['location_id'] ?? 0);
        $location_name = trim($_POST['location_name'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $visit_hours = floatval($_POST['visit_hours'] ?? 1.0);
        $rate_per_hour = floatval($_POST['rate_per_hour'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        
        // Validation
        $errors = [];
        
        if ($location_id <= 0) {
            $errors[] = 'Invalid location ID';
        }
        
        if (empty($location_name)) {
            $errors[] = 'Location name is required';
        }
        
        if (empty($city)) {
            $errors[] = 'City is required';
        }
        
        if ($visit_hours <= 0 || $visit_hours > 99.9) {
            $errors[] = 'Visit hours must be between 0.1 and 99.9';
        }
        
        if ($rate_per_hour <= 0) {
            $errors[] = 'Rate per hour must be greater than 0';
        }
        
        if (!empty($errors)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
            return;
        }
        
        // Check ownership
        if (!$this->guideLocationModel->isOwner($location_id, $guide_id)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Location not found']);
            return;
        }
        
        if ($this->guideLocationModel->updateLocation($location_id, $location_name, $city, $visit_hours, $rate_per_hour, $description)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Location updated successfully']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to update location']);
        }
    }
}

?>
