<?php
// Suppress all errors for clean JSON output
error_reporting(0);
ini_set('display_errors', 0);

class VehicleController extends controller {
    private $vehicleModel;
    
    public function __construct() {
        // Start session first to avoid any session warnings
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Prevent any output before JSON
        ob_start();
        
        // Ensure all required constants are defined
        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', dirname(dirname(__DIR__)));
        }
        
        try {
            $this->vehicleModel = $this->model('Vehicle');
        } catch (Exception $e) {
            error_log('Failed to load Vehicle model: ' . $e->getMessage());
            // Continue anyway, we'll handle the error in individual methods
        }
    }
    
    // Test method to verify controller is working
    public function test() {
        header('Content-Type: application/json');
        ob_clean(); // Clear any previous output
        echo json_encode([
            'success' => true, 
            'message' => 'VehicleController is working', 
            'session' => $_SESSION,
            'session_status' => session_status(),
            'user_logged_in' => isset($_SESSION['user_id']),
            'user_account_type' => $_SESSION['user_account_type'] ?? 'not set'
        ]);
        exit;
    }
    
    // Debug method to test add without files
    public function addTest() {
        header('Content-Type: application/json');
        ob_clean(); // Clear any previous output
        
        // Check session
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'No session found']);
            exit;
        }
        
        if ($_SESSION['user_account_type'] !== 'driver') {
            echo json_encode(['success' => false, 'message' => 'Not a driver account']);
            exit;
        }
        
        echo json_encode(['success' => true, 'message' => 'Session is valid', 'user_id' => $_SESSION['user_id']]);
        exit;
    }
    
    // Add a new vehicle (AJAX endpoint)
    public function add() {
        try {
            // Set proper JSON header
            header('Content-Type: application/json');
            ob_clean(); // Clear any previous output
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Invalid request method']);
                exit;
            }
            
            // Check if user is logged in and is a driver
            if (!isset($_SESSION['user_id']) || $_SESSION['user_account_type'] !== 'driver') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
                exit;
            }
            
            // Sanitize input data
            $vehicleData = [
                'driver_id' => $_SESSION['user_id'],
                'make' => trim($_POST['make']),
                'model' => trim($_POST['model']),
                'year' => intval($_POST['year']),
                'license_plate' => strtoupper(trim($_POST['license_plate'])),
                'color' => trim($_POST['color']),
                'vehicle_type' => $_POST['vehicle_type'],
                'seat_count' => intval($_POST['seat_count']),
                'daily_rate' => floatval($_POST['daily_rate'])
            ];
            
            // Validate required fields
            if (empty($vehicleData['make']) || empty($vehicleData['model']) || 
                empty($vehicleData['license_plate']) || empty($vehicleData['color']) || 
                empty($vehicleData['vehicle_type']) || $vehicleData['year'] < 1900 ||
                $vehicleData['seat_count'] < 1 || $vehicleData['daily_rate'] <= 0) {
                echo json_encode(['success' => false, 'message' => 'All fields are required, year must be valid, seats must be at least 1, and daily rate must be greater than 0']);
                exit;
            }
            
            // Check if license plate already exists
            if ($this->vehicleModel->checkLicensePlateExists($vehicleData['license_plate'])) {
                echo json_encode(['success' => false, 'message' => 'License plate already exists']);
                exit;
            }
            
            // Handle file uploads
            $uploadDir = ROOT_PATH.'/public/uploads/vehicles';
            $webPath = 'uploads/vehicles/'; // Web-accessible path
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $uploadedFiles = [];
            $requiredPhotos = ['front_photo', 'back_photo', 'side_photo'];
            
            foreach ($requiredPhotos as $photoField) {
                if (!isset($_FILES[$photoField]) || $_FILES[$photoField]['error'] !== UPLOAD_ERR_OK) {
                    echo json_encode(['success' => false, 'message' => 'All vehicle photos are required']);
                    exit;
                }
                
                $file = $_FILES[$photoField];
                $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                
                if (!in_array($file['type'], $allowedTypes)) {
                    echo json_encode(['success' => false, 'message' => 'Only JPG and PNG images are allowed']);
                    exit;
                }
                
                if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
                    echo json_encode(['success' => false, 'message' => 'Image size must be less than 5MB']);
                    exit;
                }
                
                $fileName = uniqid() . '_' . $photoField . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
                $filePath = $uploadDir .'/'. $fileName;
                $webFilePath = $webPath . $fileName; // Store web-accessible path
                
                if (move_uploaded_file($file['tmp_name'], $filePath)) {
                    $uploadedFiles[$photoField] = $webFilePath; // Store web path in database
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to upload ' . $photoField]);
                    exit;
                }
            }
            
            // Add uploaded file paths to vehicle data
            $vehicleData = array_merge($vehicleData, $uploadedFiles);
            
            // Add vehicle to database
            if ($this->vehicleModel->addVehicle($vehicleData)) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Vehicle added successfully! It will be verified by admin soon.',
                    'vehicle' => $vehicleData
                ]);
                exit;
            } else {
                // Clean up uploaded files if database insertion fails
                foreach ($uploadedFiles as $filePath) {
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                echo json_encode(['success' => false, 'message' => 'Failed to add vehicle to database']);
                exit;
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
            exit;
        }
    }
    
    // Test update method without authentication for debugging
    public function updateTest() {
        try {
            header('Content-Type: application/json');
            ob_clean();
            
            echo json_encode([
                'success' => true,
                'message' => 'Update test working',
                'method' => $_SERVER['REQUEST_METHOD'],
                'post_data' => $_POST,
                'files_data' => $_FILES,
                'raw_input' => file_get_contents('php://input')
            ]);
            exit;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            exit;
        }
    }
    
    // Update existing vehicle (AJAX endpoint)
    public function update() {
        // Enable error reporting for debugging
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        try {
            // Set proper JSON header
            header('Content-Type: application/json');
            ob_clean(); // Clear any previous output
            
            // Debug the actual request method
            error_log('VehicleController update: REQUEST_METHOD = ' . $_SERVER['REQUEST_METHOD']);
            error_log('VehicleController update: POST data = ' . print_r($_POST, true));
            error_log('VehicleController update: FILES data = ' . print_r($_FILES, true));
            
            // Check request method - the issue might be that form data is treated differently
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Invalid request method. Expected POST, got ' . $_SERVER['REQUEST_METHOD'],
                    'debug' => [
                        'method' => $_SERVER['REQUEST_METHOD'],
                        'post_empty' => empty($_POST),
                        'files_empty' => empty($_FILES),
                        'request_uri' => $_SERVER['REQUEST_URI'] ?? 'not set'
                    ]
                ]);
                exit;
            }
            
            // Check if user is logged in and is a driver
            if (!isset($_SESSION['user_id']) || $_SESSION['user_account_type'] !== 'driver') {
                // Additional debugging
                error_log('VehicleController update: Session check failed');
                error_log('VehicleController update: user_id = ' . ($_SESSION['user_id'] ?? 'not set'));
                error_log('VehicleController update: account_type = ' . ($_SESSION['user_account_type'] ?? 'not set'));
                
                echo json_encode([
                    'success' => false, 
                    'message' => 'Unauthorized access - session issue',
                    'debug' => [
                        'session_started' => session_status() === PHP_SESSION_ACTIVE,
                        'user_id' => $_SESSION['user_id'] ?? 'not set',
                        'account_type' => $_SESSION['user_account_type'] ?? 'not set',
                        'session_data' => $_SESSION
                    ]
                ]);
                exit;
            }
            
            // Validate vehicle ID
            if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
                echo json_encode(['success' => false, 'message' => 'Valid vehicle ID is required']);
                exit;
            }
            
            $vehicleId = intval($_POST['id']);
            
            // Check if this vehicle belongs to the current user
            $existingVehicle = $this->vehicleModel->getVehicleById($vehicleId);
            if (!$existingVehicle || $existingVehicle->driver_id != $_SESSION['user_id']) {
                echo json_encode(['success' => false, 'message' => 'Vehicle not found or access denied']);
                exit;
            }
            
            // Sanitize input data
            $vehicleData = [
                'id' => $vehicleId,
                'make' => trim($_POST['make']),
                'model' => trim($_POST['model']),
                'year' => intval($_POST['year']),
                'license_plate' => strtoupper(trim($_POST['license_plate'])),
                'color' => trim($_POST['color']),
                'vehicle_type' => $_POST['vehicle_type'],
                'seat_count' => intval($_POST['seat_count']),
                'daily_rate' => floatval($_POST['daily_rate'])
            ];
            
            // Validate required fields
            if (empty($vehicleData['make']) || empty($vehicleData['model']) || 
                empty($vehicleData['license_plate']) || empty($vehicleData['color']) || 
                empty($vehicleData['vehicle_type']) || $vehicleData['year'] < 1900 ||
                $vehicleData['seat_count'] < 1 || $vehicleData['daily_rate'] <= 0) {
                echo json_encode(['success' => false, 'message' => 'All fields are required, year must be valid, seats must be at least 1, and daily rate must be greater than 0']);
                exit;
            }
            
            // Check if license plate already exists (for other vehicles)
            if ($this->vehicleModel->checkLicensePlateExistsExcluding($vehicleData['license_plate'], $vehicleId)) {
                echo json_encode(['success' => false, 'message' => 'License plate already exists for another vehicle']);
                exit;
            }
            
            // Handle file uploads (optional for updates)
            $uploadDir = 'public/uploads/vehicles/';
            $webPath = 'uploads/vehicles/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $uploadedFiles = [];
            $photoFields = ['front_photo', 'back_photo', 'side_photo'];
            
            foreach ($photoFields as $photoField) {
                if (isset($_FILES[$photoField]) && $_FILES[$photoField]['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES[$photoField];
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                    
                    if (!in_array($file['type'], $allowedTypes)) {
                        echo json_encode(['success' => false, 'message' => 'Only JPG and PNG images are allowed']);
                        exit;
                    }
                    
                    if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
                        echo json_encode(['success' => false, 'message' => 'Image size must be less than 5MB']);
                        exit;
                    }
                    
                    $fileName = uniqid() . '_' . $photoField . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filePath = $uploadDir . $fileName;
                    $webFilePath = $webPath . $fileName;
                    
                    if (move_uploaded_file($file['tmp_name'], $filePath)) {
                        $uploadedFiles[$photoField] = $webFilePath;
                        
                        // Delete old photo if it exists
                        $oldPhotoPath = $existingVehicle->$photoField;
                        if ($oldPhotoPath && file_exists('public/' . $oldPhotoPath)) {
                            unlink('public/' . $oldPhotoPath);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to upload ' . $photoField]);
                        exit;
                    }
                }
            }
            
            // Add uploaded file paths to vehicle data (only if new files were uploaded)
            $vehicleData = array_merge($vehicleData, $uploadedFiles);
            
            // Update vehicle in database
            if ($this->vehicleModel->updateVehicle($vehicleData)) {
                // Send notification to admin about vehicle update
                $this->notifyAdminVehicleUpdate($vehicleId, $_SESSION['user_id']);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Vehicle updated successfully! Admin has been notified for re-approval.',
                    'vehicle' => $vehicleData
                ]);
                exit;
            } else {
                // Clean up uploaded files if database update fails
                foreach ($uploadedFiles as $filePath) {
                    if (file_exists($uploadDir . basename($filePath))) {
                        unlink($uploadDir . basename($filePath));
                    }
                }
                echo json_encode(['success' => false, 'message' => 'Failed to update vehicle in database']);
                exit;
            }
        } catch (Exception $e) {
            error_log('VehicleController update exception: ' . $e->getMessage());
            error_log('VehicleController update stack trace: ' . $e->getTraceAsString());
            echo json_encode([
                'success' => false, 
                'message' => 'Server error: ' . $e->getMessage(),
                'debug' => [
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine(),
                    'error_trace' => $e->getTraceAsString()
                ]
            ]);
            exit;
        } catch (Error $e) {
            error_log('VehicleController update fatal error: ' . $e->getMessage());
            echo json_encode([
                'success' => false, 
                'message' => 'Fatal error: ' . $e->getMessage(),
                'debug' => [
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine()
                ]
            ]);
            exit;
        }
    }
    
    // Get driver's vehicles
    public function getMyVehicles() {
        header('Content-Type: application/json');
        ob_clean(); // Clear any previous output
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_account_type'] !== 'driver') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            exit;
        }
        
        $vehicles = $this->vehicleModel->getVehiclesByDriverId($_SESSION['user_id']);
        echo json_encode(['success' => true, 'vehicles' => $vehicles]);
        exit;
    }
    
    // Delete vehicle
    public function delete($vehicleId = null) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $vehicleId) {
            if (!isset($_SESSION['user_id']) || $_SESSION['user_account_type'] !== 'driver') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
                return;
            }
            
            // Get existing vehicle to verify ownership and get file paths
            $existingVehicle = $this->vehicleModel->getVehicleById($vehicleId);
            if (!$existingVehicle || $existingVehicle->driver_id != $_SESSION['user_id']) {
                echo json_encode(['success' => false, 'message' => 'Vehicle not found or access denied']);
                return;
            }
            
            if ($this->vehicleModel->deleteVehicle($vehicleId, $_SESSION['user_id'])) {
                // Clean up uploaded files
                $photoFields = ['front_photo', 'back_photo', 'side_photo'];
                foreach ($photoFields as $field) {
                    if (!empty($existingVehicle->$field) && file_exists($existingVehicle->$field)) {
                        unlink($existingVehicle->$field);
                    }
                }
                echo json_encode(['success' => true, 'message' => 'Vehicle deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete vehicle']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
        }
    }
    
    // Get vehicle stats for driver dashboard
    public function getStats() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id']) || $_SESSION['user_account_type'] !== 'driver') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            return;
        }
        
        $stats = $this->vehicleModel->getVehicleCountByDriverId($_SESSION['user_id']);
        
        echo json_encode(['success' => true, 'stats' => $stats]);
    }
    
    // Admin functions
    
    // Get pending vehicles for verification
    public function getPendingVerification() {
        header('Content-Type: application/json');
        ob_clean();
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_account_type'], ['admin', 'site_moderator'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            exit;
        }
        
        $vehicles = $this->vehicleModel->getPendingVehicles();
        echo json_encode(['success' => true, 'vehicles' => $vehicles]);
        exit;
    }
    
    // Get vehicle details (for admin verification or driver editing)
    public function getDetails($vehicleId = null) {
        header('Content-Type: application/json');
        ob_clean();
        
        if (!isset($_SESSION['user_id'])) {
            // Temporary testing bypass - remove in production
            $_SESSION['user_id'] = 2; // Driver ID from database
            $_SESSION['user_account_type'] = 'driver';
            $_SESSION['user_email'] = 'tecadonsolutions@gmail.com';
            $_SESSION['user_fullname'] = 'akila';
        }
        
        if (!$vehicleId) {
            echo json_encode(['success' => false, 'message' => 'Vehicle ID required']);
            exit;
        }
        
        $vehicle = $this->vehicleModel->getVehicleById($vehicleId);
        if (!$vehicle) {
            echo json_encode(['success' => false, 'message' => 'Vehicle not found']);
            exit;
        }
        
        // Convert object to array if needed
        $vehicleData = is_object($vehicle) ? (array) $vehicle : $vehicle;
        
        // Check access permissions
        $userType = $_SESSION['user_account_type'];
        $userId = $_SESSION['user_id'];
        
        if ($userType === 'driver') {
            // Drivers can only access their own vehicles
            if ($vehicleData['driver_id'] != $userId) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized access - not your vehicle']);
                exit;
            }
        } elseif (!in_array($userType, ['admin', 'site_moderator'])) {
            // Only drivers, admins, and site moderators can access vehicle details
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            exit;
        }
        
        echo json_encode(['success' => true, 'vehicle' => $vehicleData]);
        exit;
    }
    
    // Verify vehicle (approve/reject)
    public function verify($vehicleId = null) {
        header('Content-Type: application/json');
        ob_clean();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $vehicleId) {
            if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_account_type'], ['admin', 'site_moderator'])) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
                exit;
            }
            
            $status = $_POST['status']; // 'approved' or 'rejected'
            $rejectionReason = isset($_POST['rejection_reason']) ? trim($_POST['rejection_reason']) : null;
            
            if (!in_array($status, ['approved', 'rejected'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid status']);
                exit;
            }
            
            if ($status === 'rejected' && empty($rejectionReason)) {
                echo json_encode(['success' => false, 'message' => 'Rejection reason is required']);
                exit;
            }
            
            if ($this->vehicleModel->updateVerificationStatus($vehicleId, $status, $_SESSION['user_id'], $rejectionReason)) {
                $message = $status === 'approved' ? 'Vehicle approved successfully' : 'Vehicle rejected successfully';
                echo json_encode(['success' => true, 'message' => $message]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update verification status']);
            }
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }
    }
    
    // Notify admin when vehicle is updated and needs re-approval
    private function notifyAdminVehicleUpdate($vehicleId, $driverId) {
        try {
            // Check if ROOT_PATH is defined
            if (!defined('ROOT_PATH')) {
                error_log('ROOT_PATH not defined, using fallback');
                define('ROOT_PATH', dirname(dirname(__DIR__)));
            }
            
            // Get vehicle details
            $vehicle = $this->vehicleModel->getVehicleById($vehicleId);
            if (!$vehicle) {
                error_log('Vehicle not found for notification: ' . $vehicleId);
                return false;
            }
            
            // Create notification record (you might want to create a notifications table)
            // For now, we'll log it or create a simple file-based notification
            $notificationData = [
                'type' => 'vehicle_update',
                'vehicle_id' => $vehicleId,
                'driver_id' => $driverId,
                'driver_name' => $vehicle->driver_name ?? 'Unknown',
                'vehicle_info' => $vehicle->make . ' ' . $vehicle->model . ' (' . $vehicle->license_plate . ')',
                'message' => 'Vehicle updated and requires re-approval',
                'timestamp' => date('Y-m-d H:i:s'),
                'status' => 'pending'
            ];
            
            // You can implement this as needed - database table, email, etc.
            // For now, we'll create a simple notification file
            $notificationFile = ROOT_PATH . '/app/data/vehicle_notifications.json';
            $notifications = [];
            
            if (file_exists($notificationFile)) {
                $notifications = json_decode(file_get_contents($notificationFile), true) ?? [];
            }
            
            $notifications[] = $notificationData;
            
            // Keep only last 100 notifications
            if (count($notifications) > 100) {
                $notifications = array_slice($notifications, -100);
            }
            
            // Create directory if it doesn't exist
            $dataDir = ROOT_PATH . '/app/data';
            if (!file_exists($dataDir)) {
                mkdir($dataDir, 0755, true);
            }
            
            file_put_contents($notificationFile, json_encode($notifications, JSON_PRETTY_PRINT));
            return true;
        } catch (Exception $e) {
            error_log('Failed to notify admin: ' . $e->getMessage());
            return false;
        }
    }
    
    // Get admin notifications
    public function getAdminNotifications() {
        header('Content-Type: application/json');
        ob_clean();
        
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_account_type'], ['admin', 'site_moderator'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            exit;
        }
        
        try {
            $notificationFile = ROOT_PATH . '/app/data/vehicle_notifications.json';
            $notifications = [];
            
            if (file_exists($notificationFile)) {
                $notifications = json_decode(file_get_contents($notificationFile), true) ?? [];
                // Get only pending notifications, most recent first
                $notifications = array_filter($notifications, function($n) {
                    return $n['status'] === 'pending';
                });
                $notifications = array_reverse($notifications);
            }
            
            echo json_encode(['success' => true, 'notifications' => array_values($notifications)]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to load notifications']);
        }
        exit;
    }
    
    // Mark notification as read
    public function markNotificationRead($notificationIndex = null) {
        header('Content-Type: application/json');
        ob_clean();
        
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_account_type'], ['admin', 'site_moderator'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            exit;
        }
        
        if ($notificationIndex === null || !is_numeric($notificationIndex)) {
            echo json_encode(['success' => false, 'message' => 'Invalid notification index']);
            exit;
        }
        
        try {
            $notificationFile = ROOT_PATH . '/app/data/vehicle_notifications.json';
            if (file_exists($notificationFile)) {
                $notifications = json_decode(file_get_contents($notificationFile), true) ?? [];
                if (isset($notifications[$notificationIndex])) {
                    $notifications[$notificationIndex]['status'] = 'read';
                    $notifications[$notificationIndex]['read_at'] = date('Y-m-d H:i:s');
                    file_put_contents($notificationFile, json_encode($notifications, JSON_PRETTY_PRINT));
                    echo json_encode(['success' => true, 'message' => 'Notification marked as read']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Notification not found']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'No notifications found']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to update notification']);
        }
        exit;
    }
}
?>