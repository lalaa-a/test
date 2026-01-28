<?php

    class Driver extends Controller{
        
        private $driverModel;
        private $userModel;
        public function __construct() {
            $this->driverModel = $this->model('DriverModel');
            $this->userModel = $this->model('UserModel');
        }

        public function index() {

            $unEncodedResponse = [
                'tabId'=>'dashboard',
                'loadingContent'=>null
            ];
            $this->view('UserTemplates/driverDash', $unEncodedResponse);
        }

        public function driverProfile() {

            ob_start();
            $this->view('Driver/profile/driverProfile');
            $fullcontent = ob_get_clean();

            $html = $fullcontent;
            $css = URL_ROOT.'/public/css/driver/profile/driverProfile.css';
            $js = URL_ROOT.'/public/js/driver/profile/driverProfile.js';

            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];

            $unEncodedResponse = [
                'tabId'=>'my-profile',
                'loadingContent'=>$loadingContent
            ];
            $this->view('UserTemplates/driverDash', $unEncodedResponse);
        }

        public function vehicles() {

            ob_start();
            $this->view('Driver/vehicles/driverVehicles');
            $fullcontent = ob_get_clean();

            $html = $fullcontent;
            $css = URL_ROOT.'/public/css/driver/vehicles/driverVehicles.css';
            $js = URL_ROOT.'/public/js/driver/vehicles/driverVehicles.js';

            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];

            $unEncodedResponse = [
                'tabId'=>'vehicles',
                'loadingContent'=>$loadingContent
            ];
            $this->view('UserTemplates/driverDash', $unEncodedResponse);
        }

        public function retrieveBasicDriverInfo() {
            
            header('Content-Type: application/json');
            
            // Log to PHP error log instead of output
            error_log("retrieveBasicDriverInfo called f");

            $userId = getSession('user_id');

            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            try {
                $driverInfo = $this->driverModel->getBasicDriverInfo($userId);
                echo json_encode(['success' => true, 'driverInfo' => $driverInfo]);

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when retrieving driver info: ' . $e->getMessage()]);
            }
        }

        public function editDriverPersonalInfo() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');

            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            // Read FormData from POST request
            $input = $_POST;

            error_log("editDriverPersonalInfo called with input: " . print_r($input, true));

            if (empty($input)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid input data']);
                return;
            }

            try {
                $updateSuccess = $this->driverModel->updateDriverPersonalInfo($userId, $input);

                if ($updateSuccess) {
                    // Update session data if full name changed
                    if (isset($input['fullName'])) {
                        setSession('user_fullname', $input['fullName']);
                    }

                    echo json_encode(['success' => true, 'message' => 'Driver personal info updated successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to update driver personal info']);
                }

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when updating driver info: ' . $e->getMessage()]);
            }
        }

        public function editDriverLicense() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');

            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            // Log incoming data
            error_log("editDriverLicense called for userId: " . $userId);
            error_log("POST received: " . print_r($_POST, true));
            error_log("FILES received: " . print_r($_FILES, true));

            try {
                $uploadedFiles = [];
                $specPath = 'drivers/' . $userId . '/licenses';
                $uploadDir = ROOT_PATH.'/public/uploads/'.$specPath;

                // Create directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                    error_log("Created directory: " . $uploadDir);
                }

                // Process uploaded files
                $fileFields = ['licenseFront', 'licenseBack'];
                foreach ($fileFields as $fieldName) {
                    if (isset($_FILES[$fieldName])) {
                        error_log("Processing $fieldName: error=" . $_FILES[$fieldName]['error']);

                        if ($_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
                            $file = $_FILES[$fieldName];

                            // Validate file type
                            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                            if (!in_array($file['type'], $allowedTypes)) {
                                error_log("File type not allowed: " . $file['type']);
                                http_response_code(400);
                                echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPEG, JPG, and PNG are allowed.']);
                                return;
                            }

                            // Validate file size (5MB max)
                            if ($file['size'] > 5 * 1024 * 1024) {
                                error_log("File too large: " . $file['size']);
                                http_response_code(400);
                                echo json_encode(['success' => false, 'message' => 'File size must be less than 5MB.']);
                                return;
                            }

                            // Generate unique filename
                            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                            $filename = 'driver_license_' . $fieldName . '_' . time() . '.' . $extension;
                            $filepath = $uploadDir . '/' . $filename;

                            // Move uploaded file
                            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                                $uploadedFiles[$fieldName] = $specPath . '/' . $filename;
                                error_log("Successfully uploaded $fieldName to: " . $filename);
                            } else {
                                error_log("Failed to move file: " . $file['tmp_name'] . " to " . $filepath);
                                http_response_code(500);
                                echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file.']);
                                return;
                            }
                        }
                    }
                }

                // Prepare driver data update
                $driverData = [
                    'license_number' => $_POST['licenseNumber'] ?? '',
                    'license_expire_date' => $_POST['licenseExpiry'] ?? ''
                ];

                // Add file paths if uploaded
                if (isset($uploadedFiles['licenseFront'])) {
                    $driverData['license_front'] = $uploadedFiles['licenseFront'];
                }
                if (isset($uploadedFiles['licenseBack'])) {
                    $driverData['license_back'] = $uploadedFiles['licenseBack'];
                }

                // Update driver_data in users table
                $updateSuccess = $this->driverModel->updateDriverLicense($userId, $driverData);

                if ($updateSuccess) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Driver license updated successfully'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to update driver license information']);
                }

            } catch (Exception $e) {
                error_log("Exception in editDriverLicense: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }

        public function addVehicle() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');

            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            // Log incoming data
            error_log("addVehicle called for userId: " . $userId);
            error_log("POST received: " . print_r($_POST, true));
            error_log("FILES received: " . print_r($_FILES, true));

            try {
                $uploadedFiles = [];

                // Process uploaded files using upload_helper
                $fileFields = ['front', 'back', 'side', 'interior1', 'interior2', 'interior3'];
                foreach ($fileFields as $fieldName) {
                    if (isset($_FILES[$fieldName])) {
                        error_log("Processing $fieldName: error=" . $_FILES[$fieldName]['error']);

                        $uploadPath =  '/drivers/' . $userId. '/vehicles';
                        $prefix = 'vehicle_' . $fieldName;
                        
                        $uploadedPath = uploadFile($_FILES[$fieldName], $uploadPath, $prefix);
                        
                        if ($uploadedPath === false) {
                            error_log("Failed to upload file for $fieldName");
                            http_response_code(400);
                            echo json_encode(['success' => false, 'message' => 'Failed to upload file for ' . $fieldName . '. Please check file type (JPEG, JPG, PNG only) and size (max 5MB).']);
                            return;
                        }
                        
                        $uploadedFiles[$fieldName] = $uploadedPath;
                        error_log("Successfully uploaded $fieldName to: " . $uploadedPath);
                    }
                }

                // Prepare vehicle data
                $vehicleData = [
                    'make' => $_POST['vehicleMake'] ?? '',
                    'model' => $_POST['vehicleModel'] ?? '',
                    'year' => intval($_POST['vehicleYear'] ?? 0),
                    'color' => $_POST['vehicleColor'] ?? '',
                    'licensePlate' => $_POST['licensePlate'] ?? '',
                    'seatingCapacity' => intval($_POST['seatingCapacity'] ?? 4),
                    'childSeats' => intval($_POST['childSeats'] ?? 0),
                    'fuelEfficiency' => $_POST['fuelEfficiency'] ? floatval($_POST['fuelEfficiency']) : null,
                    'description' => $_POST['description'] ?? '',
                    'frontViewPhoto' => $uploadedFiles['front'] ?? null,
                    'backViewPhoto' => $uploadedFiles['back'] ?? null,
                    'sideViewPhoto' => $uploadedFiles['side'] ?? null,
                    'interiorPhoto1' => $uploadedFiles['interior1'] ?? null,
                    'interiorPhoto2' => $uploadedFiles['interior2'] ?? null,
                    'interiorPhoto3' => $uploadedFiles['interior3'] ?? null,
                ];

                error_log("Vehicle data prepared: " . print_r($vehicleData, true));

                // Add vehicle
                $addSuccess = $this->driverModel->addVehicle($userId, $vehicleData);

                if ($addSuccess) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Vehicle added successfully'
                    ]);
                } else {
                    error_log("addVehicle returned false");
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to add vehicle']);
                }

            } catch (Exception $e) {
                error_log("Exception in addVehicle: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }

        public function getVehicles() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            error_log("getVehicles called, userId: " . ($userId ?: 'null'));

            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            try {
                $vehicles = $this->driverModel->getDriverVehicles($userId);
                error_log("Found " . count($vehicles) . " vehicles for user $userId");
                echo json_encode([
                    'success' => true,
                    'vehicles' => $vehicles
                ]);

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when retrieving vehicles: ' . $e->getMessage()]);
            }
        }

        public function deleteVehicle() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');

            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            $vehicleId = $_POST['vehicleId'] ?? null;

            if (!$vehicleId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Vehicle ID is required']);
                return;
            }

            try {
                $result = $this->driverModel->deleteVehicle($userId, $vehicleId);
                
                if ($result['success']) {
                    echo json_encode([
                        'success' => true,
                        'message' => $result['message']
                    ]);
                } else {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => $result['message']
                    ]);
                }

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
            }
        }

        public function toggleVehicle() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');

            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            $vehicleId = $_POST['vehicleId'] ?? null;
            $isActive = $_POST['isActive'] ?? null;

            if (!$vehicleId || $isActive === null) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Vehicle ID and active status are required']);
                return;
            }

            // Validate isActive is boolean
            if (!is_bool($isActive) && !in_array($isActive, ['true', 'false', '1', '0'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Active status must be a boolean value']);
                return;
            }

            // Convert string to boolean if needed
            if (is_string($isActive)) {
                $isActive = $isActive === 'true' || $isActive === '1';
            }

            try {
                $result = $this->driverModel->toggleVehicleStatus($userId, $vehicleId, $isActive);
                
                if ($result['success']) {
                    echo json_encode([
                        'success' => true,
                        'message' => $result['message']
                    ]);
                } else {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => $result['message']
                    ]);
                }

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
            }
        }

        public function getVehicleStats() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');

            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            try {
                $vehicles = $this->driverModel->getDriverVehicles($userId);
                
                // Calculate stats
                $total = count($vehicles['approved']) + count($vehicles['pending']);
                $approved = count($vehicles['approved']);
                $pending = count($vehicles['pending']);
                $active = count(array_filter($vehicles['approved'], function($v) { return $v['is_active']; }));
                $inactive = $approved - $active;

                $stats = [
                    'total' => $total,
                    'approved' => $approved,
                    'pending' => $pending,
                    'active' => $active,
                    'inactive' => $inactive
                ];

                echo json_encode([
                    'success' => true,
                    'stats' => $stats
                ]);

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when retrieving stats: ' . $e->getMessage()]);
            }
        }
        

    }


    // `/controller/method/parameters
?>

