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
                $specPath = '/drivers/' . $userId . '/licenses';
                $uploadDir = ROOT_PATH.'/public/uploads'.$specPath;

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

            // Help page within the dashboard
        public function help() {
            ob_start();
            $this->view('Help/helpContent');
            $fullcontent = ob_get_clean();

            $html = $fullcontent;
            $css = URL_ROOT.'/public/css/helper/help.css';
            $js = URL_ROOT.'/public/js/helper/help.js';

            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];

            $unEncodedResponse = [
                'tabId'=>'help',
                'loadingContent'=>$loadingContent
            ];
            $this->view('UserTemplates/driverDash', $unEncodedResponse);
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

        public function pricing() {
            ob_start();
            $this->view('Driver/pricing/pricing');
            $fullcontent = ob_get_clean();

            $html = $fullcontent;
            $css = URL_ROOT.'/public/css/driver/pricing/pricing.css';
            $js = URL_ROOT.'/public/js/driver/pricing/pricing.js';

            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];

            $unEncodedResponse = [
                'tabId'=>'pricing',
                'loadingContent'=>$loadingContent
            ];
            $this->view('UserTemplates/driverDash', $unEncodedResponse);
        }

        public function requests(){
            ob_start();
            $this->view('Driver/requests/requests');
            $fullcontent = ob_get_clean();

            $html = $fullcontent;
            $css = URL_ROOT.'/public/css/driver/requests/requests.css';
            $js = URL_ROOT.'/public/js/driver/requests/requests.js';

            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];

            $unEncodedResponse = [
                'tabId'=>'requests',
                'loadingContent'=>$loadingContent
            ];
            $this->view('UserTemplates/driverDash', $unEncodedResponse);  
        }

        /* Driver Requests API Endpoints */
        public function getMyRequests() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            try {
                $requests = $this->driverModel->getRequestsByDriver($userId);

                echo json_encode(['success' => true, 'requests' => $requests]);
            } catch (Exception $e) {
                error_log('Error in getMyRequests: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to load requests']);
            }
        }

        public function acceptRequest($requestId = null) {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            if (!$requestId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Request ID is required']);
                return;
            }

            try {
                $result = $this->driverModel->updateRequestStatus($userId, $requestId, 'accepted');
                if ($result['success']) {
                    echo json_encode(['success' => true, 'message' => 'Request accepted']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => $result['message']]);
                }
            } catch (Exception $e) {
                error_log('Error in acceptRequest: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to accept request']);
            }
        }

        public function rejectRequest($requestId = null) {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            if (!$requestId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Request ID is required']);
                return;
            }

            // Read rejection reason if provided (not stored currently, but logged)
            $input = json_decode(file_get_contents('php://input'), true);
            $reason = $input['reason'] ?? null;
            if ($reason) {
                error_log("Request $requestId rejected by driver $userId. Reason: " . $reason);
            }

            try {
                $result = $this->driverModel->updateRequestStatus($userId, $requestId, 'rejected');
                if ($result['success']) {
                    echo json_encode(['success' => true, 'message' => 'Request rejected']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => $result['message']]);
                }
            } catch (Exception $e) {
                error_log('Error in rejectRequest: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to reject request']);
            }
        }

        public function getTripItinerary($tripId = null) {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            if (!$tripId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Trip ID is required']);
                return;
            }

            try {
                $itinerary = $this->driverModel->getTripItinerary($tripId);
                
                if ($itinerary) {
                    echo json_encode(['success' => true, 'itinerary' => $itinerary]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Trip not found']);
                }
            } catch (Exception $e) {
                error_log('Error in getTripItinerary: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to load trip itinerary']);
            }
        }

        public function subtabTours(){
            ob_start();
            $this->view('Driver/schedule/subtabTours');
            $fullcontent = ob_get_clean();

            $html = $fullcontent;
            $css = URL_ROOT.'/public/css/driver/schedule/subtabTours.css';
            $js = URL_ROOT.'/public/js/driver/schedule/subtabTours.js';

            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];

            $unEncodedResponse = [
                'ok' => true,
                'loadingContent'=>$loadingContent
            ];
            echo json_encode($unEncodedResponse);
        }
        

        public function schedule(){
            ob_start();
            $this->view('Driver/schedule/schedule');
            $fullcontent = ob_get_clean();

            $html = $fullcontent;
            $css = URL_ROOT.'/public/css/driver/schedule/schedule.css';
            $js = URL_ROOT.'/public/js/driver/schedule/schedule.js';

            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];

            $unEncodedResponse = [
                'tabId'=>'schedule',
                'loadingContent'=>$loadingContent
            ];
            $this->view('UserTemplates/driverDash', $unEncodedResponse);
        }

        public function subtabAvailabilityCalendar(){
            ob_start();
            $this->view('Driver/schedule/subtabAvailabilityCalendar');
            $fullcontent = ob_get_clean();

            $html = $fullcontent;
            $css = URL_ROOT.'/public/css/driver/schedule/subtabAvailabilityCalendar.css';
            $js = URL_ROOT.'/public/js/driver/schedule/subtabAvailabilityCalendar.js';

            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];

            $unEncodedResponse = [
                'ok' => true,
                'loadingContent'=>$loadingContent
            ];
            echo json_encode($unEncodedResponse);
        }

        // Driver Availability Management Methods
        public function getDriverAvailability() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            try {
                // Get date range from request (default to next 60 days)
                $startDate = $_GET['startDate'] ?? date('Y-m-d');
                $endDate = $_GET['endDate'] ?? date('Y-m-d', strtotime('+60 days'));

                $unavailableDates = $this->driverModel->getDriverUnavailableDates($userId, $startDate, $endDate);

                // Format dates for frontend
                $formattedDates = [];
                foreach ($unavailableDates as $date) {
                    $formattedDates[] = [
                        'date' => $date->unavailableDate,
                        'reason' => $date->reason,
                        'personalReason' => $date->personalReason,
                        'tripId' => $date->tripId
                    ];
                }

                echo json_encode([
                    'success' => true,
                    'unavailableDates' => $formattedDates
                ]);

            } catch (Exception $e) {
                error_log("Error getting driver availability: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to load availability data']);
            }
        }

        public function addUnavailableDate() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            try {
                $input = json_decode(file_get_contents('php://input'), true);

                if (!$input || !isset($input['date'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Date is required']);
                    return;
                }

                $date = $input['date'];
                $personalReason = $input['personalReason'] ?? null;

                // Validate date format
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid date format']);
                    return;
                }

                // Check if date is in the past
                if (strtotime($date) < strtotime(date('Y-m-d'))) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Cannot set past dates as unavailable']);
                    return;
                }

                $success = $this->driverModel->addDriverUnavailableDate($userId, $date, 'personal', $personalReason);

                if ($success) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Date marked as unavailable successfully'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to save unavailable date']);
                }

            } catch (Exception $e) {
                error_log("Error adding unavailable date: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to add unavailable date']);
            }
        }

        public function removeUnavailableDate() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            try {
                $input = json_decode(file_get_contents('php://input'), true);

                if (!$input || !isset($input['date'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Date is required']);
                    return;
                }

                $date = $input['date'];

                // Validate date format
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid date format']);
                    return;
                }

                $success = $this->driverModel->removeDriverUnavailableDate($userId, $date);

                if ($success) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Date removed from unavailable list successfully'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to remove unavailable date']);
                }

            } catch (Exception $e) {
                error_log("Error removing unavailable date: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to remove unavailable date']);
            }
        }

        public function saveAvailabilityChanges() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            try {
                $input = json_decode(file_get_contents('php://input'), true);

                if (!$input || !isset($input['unavailableDates'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Unavailable dates data is required']);
                    return;
                }

                $unavailableDates = $input['unavailableDates'];

                // Validate each date entry
                foreach ($unavailableDates as $dateEntry) {
                    if (!isset($dateEntry['date']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateEntry['date'])) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'Invalid date format in unavailable dates']);
                        return;
                    }

                    // Check if date is in the past
                    if (strtotime($dateEntry['date']) < strtotime(date('Y-m-d'))) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'Cannot set past dates as unavailable']);
                        return;
                    }
                }

                $success = $this->driverModel->bulkUpdateDriverAvailability($userId, $unavailableDates);

                if ($success) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Availability changes saved successfully'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to save availability changes']);
                }

            } catch (Exception $e) {
                error_log("Error saving availability changes: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to save availability changes']);
            }
        }

        public function getVerifiedVehicles() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');

            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            try {
                $vehicles = $this->driverModel->getVerifiedVehiclesWithPricing($userId);
                echo json_encode(['success' => true, 'vehicles' => $vehicles]);
            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
            }
        }

        public function saveVehiclePricing() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');

            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input || !isset($input['vehicleId'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid input data']);
                return;
            }

            // Verify the vehicle belongs to the driver and is verified
            $vehicle = $this->driverModel->getVehicleById($input['vehicleId']);
            if (!$vehicle || $vehicle->driverId != $userId || $vehicle->status != 1) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Unauthorized or vehicle not verified']);
                return;
            }

            try {
                $pricingData = [
                    'vehicleId' => $input['vehicleId'],
                    'driverId' => $userId,
                    'vehicleChargePerKm' => $input['vehicleChargePerKm'] ?? 0,
                    'driverChargePerKm' => $input['driverChargePerKm'] ?? 0,
                    'vehicleChargePerDay' => $input['vehicleChargePerDay'] ?? 0,
                    'driverChargePerDay' => $input['driverChargePerDay'] ?? 0,
                    'minimumKm' => $input['minimumKm'] ?? 0,
                    'minimumDays' => $input['minimumDays'] ?? 1
                ];

                $success = $this->driverModel->saveVehiclePricing($pricingData);

                if ($success) {
                    echo json_encode(['success' => true, 'message' => 'Pricing saved successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to save pricing']);
                }
            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
            }
        }

        public function deleteVehiclePricing() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');

            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input || !isset($input['vehicleId'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid input data']);
                return;
            }

            // Verify the vehicle belongs to the driver and is verified
            $vehicle = $this->driverModel->getVehicleById($input['vehicleId']);
            if (!$vehicle || $vehicle->driverId != $userId || $vehicle->status != 1) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Unauthorized or vehicle not verified']);
                return;
            }

            try {
                $success = $this->driverModel->deleteVehiclePricing($input['vehicleId']);

                if ($success) {
                    echo json_encode(['success' => true, 'message' => 'Pricing deleted successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to delete pricing']);
                }
            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
            }
        }

        /* Driver Tours API Endpoints */
        public function getDriverTours() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            try {
                $tours = $this->driverModel->getDriverTours($userId);
                echo json_encode(['success' => true, 'tours' => $tours]);
            } catch (Exception $e) {
                error_log('Error in getDriverTours: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to load tours']);
            }
        }

        public function getTourDetails($acceptId = null) {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            if (!$acceptId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Accept ID is required']);
                return;
            }

            try {
                $tour = $this->driverModel->getTourDetails($acceptId);

                if ($tour) {
                    echo json_encode(['success' => true, 'tour' => $tour]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Tour not found']);
                }
            } catch (Exception $e) {
                error_log('Error in getTourDetails: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to load tour details']);
            }
        }

        public function startTrip() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input || !isset($input['tripId']) || !isset($input['pin'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Trip ID and PIN are required']);
                return;
            }

            try {
                $result = $this->driverModel->startTrip($input['tripId'], $input['pin']);

                if ($result['success']) {
                    echo json_encode(['success' => true, 'message' => $result['message']]);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => $result['message']]);
                }
            } catch (Exception $e) {
                error_log('Error in startTrip: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to start trip']);
            }
        }

        public function getTripEvents() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            $tripId = $_GET['tripId'] ?? null;

            if (!$tripId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Trip ID is required']);
                return;
            }

            try {
                $events = $this->driverModel->getTripEvents($tripId);
                echo json_encode(['success' => true, 'events' => $events]);
            } catch (Exception $e) {
                error_log('Error in getTripEvents: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to load trip events']);
            }
        }

        public function markEventComplete() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input || !isset($input['eventId'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Event ID is required']);
                return;
            }

            try {
                $result = $this->driverModel->markEventComplete($input['eventId']);

                if ($result['success']) {
                    echo json_encode(['success' => true, 'message' => $result['message']]);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => $result['message']]);
                }
            } catch (Exception $e) {
                error_log('Error in markEventComplete: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to mark event complete']);
            }
        }

        public function completeTrip() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input || !isset($input['tripId'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Trip ID is required']);
                return;
            }

            try {
                $result = $this->driverModel->completeTrip($input['tripId']);

                if ($result['success']) {
                    echo json_encode(['success' => true, 'message' => $result['message']]);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => $result['message']]);
                }
            } catch (Exception $e) {
                error_log('Error in completeTrip: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to complete trip']);
            }
        }

        public function getDriverProfile($driverId) {
            header('Content-Type: application/json');

            if (!$driverId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Driver ID is required']);
                return;
            }

            try {
                $profileData = $this->driverModel->getDriverProfileByDriverId($driverId);
                if (!$profileData) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Driver not found']);
                    return;
                }

                // Get stats for total reviews
                $stats = $this->driverModel->getDriverStats($driverId);

                // Calculate verification status
                $isVerified = ($profileData->dlVerified == 1 && $profileData->tlVerified == 1);

                // Prepare response data
                $profile = [
                    'userId' => $profileData->userId,
                    'profile_name' => $profileData->profile_name,
                    'phone' => $profileData->phone,
                    'email' => $profileData->email,
                    'profile_photo' => $profileData->profile_photo,
                    'bio' => $profileData->bio,
                    'languages' => $profileData->languages,
                    'instaAccount' => $profileData->instaAccount,
                    'facebookAccount' => $profileData->facebookAccount,
                    'dlVerified' => $profileData->dlVerified,
                    'tlVerified' => $profileData->tlVerified,
                    'tLicenseNumber' => $profileData->tLicenseNumber,
                    'tLicenseExpiryDate' => $profileData->tLicenseExpiryDate,
                    'averageRating' => $profileData->averageRating,
                    'totalReviews' => $stats['totalReviews'] ?? 0,
                    'isVerified' => $isVerified
                ];

                echo json_encode(['success' => true, 'profile' => $profile]);
            } catch (Exception $e) {
                error_log('Error getting driver profile: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to load driver profile']);
            }
        }

        public function getDriverCoverPhotos($driverId) {
            header('Content-Type: application/json');

            if (!$driverId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Driver ID is required']);
                return;
            }

            try {
                $photos = $this->driverModel->getDriverCoverPhotos($driverId);
                echo json_encode(['success' => true, 'photos' => $photos]);
            } catch (Exception $e) {
                error_log('Error getting driver cover photos: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to load cover photos']);
            }
        }

        public function getDriverVehicles($driverId) {
            header('Content-Type: application/json');

            if (!$driverId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Driver ID is required']);
                return;
            }

            try {
                $vehicles = $this->driverModel->getDriverVehiclesById($driverId);
                echo json_encode(['success' => true, 'vehicles' => $vehicles]);
            } catch (Exception $e) {
                error_log('Error getting driver vehicles: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to load vehicles']);
            }
        }

        public function getDriverReviews($driverId) {
            header('Content-Type: application/json');

            if (!$driverId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Driver ID is required']);
                return;
            }

            try {
                $reviews = $this->driverModel->getDriverReviews($driverId);
                echo json_encode(['success' => true, 'reviews' => $reviews]);
            } catch (Exception $e) {
                error_log('Error getting driver reviews: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to load reviews']);
            }
        }

        public function getDriverPricing($driverId) {
            header('Content-Type: application/json');

            if (!$driverId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Driver ID is required']);
                return;
            }

            try {
                $pricing = $this->driverModel->getVehiclePricing($driverId);
                echo json_encode(['success' => true, 'pricing' => $pricing]);
            } catch (Exception $e) {
                error_log('Error getting driver pricing: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to load pricing']);
            }
        }

        public function earnings(){
            ob_start();
            $this->view('Driver/earnings/earnings');
            $fullcontent = ob_get_clean();

            $html = $fullcontent;
            $css = URL_ROOT.'/public/css/driver/earnings/earnings.css';
            $js = URL_ROOT.'/public/js/driver/earnings/earnings.js';

            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];

            $unEncodedResponse = [
                'tabId'=>'earnings',
                'loadingContent'=>$loadingContent
            ];
            $this->view('UserTemplates/driverDash', $unEncodedResponse);
        }

        public function getEarningsSummary() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            try {
                $summary = $this->driverModel->getEarningsSummary($userId);
                echo json_encode(['success' => true, 'summary' => $summary]);
            } catch (Exception $e) {
                error_log('Error in getEarningsSummary: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to load earnings summary']);
            }
        }

        public function getEarningsByStatus($status = null) {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            $allowed = ['pending', 'paid', 'refunded'];
            if (!$status || !in_array($status, $allowed)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid earnings status']);
                return;
            }

            try {
                $earnings = $this->driverModel->getEarningsByStatus($userId, $status);
                echo json_encode(['success' => true, 'earnings' => $earnings]);
            } catch (Exception $e) {
                error_log('Error in getEarningsByStatus: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to load earnings']);
            }
        }

        public function getMonthlyEarnings() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            try {
                $monthly = $this->driverModel->getMonthlyEarnings($userId);
                echo json_encode(['success' => true, 'monthly' => $monthly]);
            } catch (Exception $e) {
                error_log('Error in getMonthlyEarnings: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to load monthly earnings']);
            }
        }
    }

    // `/controller/method/parameters
?>

