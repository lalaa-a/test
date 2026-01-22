<?php

    class Driver extends Controller{
        
        private $driverModel;
        public function __construct() {
            $this->driverModel = $this->model('DriverModel');
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

        public function getCoverPhotos() {
            header('Content-Type: application/json');
            
            $userId = getSession('user_id');
            
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }
            
            try {
                $photos = $this->driverModel->getCoverPhotos($userId);
                echo json_encode(['success' => true, 'photos' => $photos]);
            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
        }

        public function saveCoverPhotos() {
            header('Content-Type: application/json');
            
            $userId = getSession('user_id');
            
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }
            
            // Log incoming data
            error_log("saveCoverPhotos called for userId: " . $userId);
            error_log("FILES received: " . print_r($_FILES, true));
            error_log("POST received: " . print_r($_POST, true));
            
            try {
                $uploadedPhotos = [];
                $deletedCount = 0;
                $specPath = 'drivers/' . $userId . '/cover_photos';
                $uploadDir = ROOT_PATH.'/public/uploads/'.$specPath;
                
                // Handle deletions first
                if (isset($_POST['deletedPhotos'])) {
                    $deletedPhotoIds = json_decode($_POST['deletedPhotos'], true);
                    if (is_array($deletedPhotoIds) && !empty($deletedPhotoIds)) {
                        error_log("Deleting photos: " . implode(', ', $deletedPhotoIds));
                        $deletedCount = $this->driverModel->deleteCoverPhotos($deletedPhotoIds);
                        error_log("Deleted $deletedCount photo(s)");
                    }
                }
                
                // Create directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                    error_log("Created directory: " . $uploadDir);
                }
                
                // Process each uploaded file
                for ($i = 1; $i <= 10; $i++) {
                    $fileKey = 'photo' . $i;
                    
                    if (isset($_FILES[$fileKey])) {
                        error_log("Processing $fileKey: error=" . $_FILES[$fileKey]['error']);
                        
                        if ($_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
                            $file = $_FILES[$fileKey];
                            
                            // Validate file type
                            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                            if (!in_array($file['type'], $allowedTypes)) {
                                error_log("File type not allowed: " . $file['type']);
                                continue;
                            }
                            
                            // Validate file size (5MB max)
                            if ($file['size'] > 5 * 1024 * 1024) {
                                error_log("File too large: " . $file['size']);
                                continue;
                            }
                            
                            // Generate unique filename
                            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                            $filename = 'cover_' . time() . '_' . $i . '.' . $extension;
                            $filepath = $uploadDir . '/' . $filename;
                            
                            // Move uploaded file
                            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                                // Store with photo_order as key (i-1 because slots are 1-10, but order is 0-9)
                                $uploadedPhotos[$i - 1] = $specPath . '/' . $filename;
                                error_log("Successfully uploaded to slot " . ($i - 1) . ": " . $filename);
                            } else {
                                error_log("Failed to move file: " . $file['tmp_name'] . " to " . $filepath);
                            }
                        }
                    }
                }
                
                error_log("Total photos uploaded: " . count($uploadedPhotos));
                
                // Save new photos to database if any
                if (!empty($uploadedPhotos)) {
                    $saveSuccess = $this->driverModel->saveCoverPhotos($userId, $uploadedPhotos);
                    if (!$saveSuccess) {
                        http_response_code(500);
                        echo json_encode(['success' => false, 'message' => 'Failed to save photos to database']);
                        return;
                    }
                }
                
                // Return success with counts
                echo json_encode([
                    'success' => true, 
                    'message' => 'Changes saved successfully',
                    'uploaded' => count($uploadedPhotos),
                    'deleted' => $deletedCount
                ]);
                
            } catch(Exception $e) {
                error_log("Exception in saveCoverPhotos: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }


        public function submitTLicense() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');

            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            // Log incoming data
            error_log("submitTLicense called for userId: " . $userId);
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
                $fileFields = ['tLicensePhotoFront', 'tLicensePhotoBack'];
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
                            $filename = 'tourist_license_' . $fieldName . '_' . time() . '.' . $extension;
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

                // Update profile_details table
                $updateData = [
                    'tLicenseNumber' => $_POST['touristLicenseNumber'] ?? '',
                    'tLicenseExpiryDate' => $_POST['touristLicenseExpiry'] ?? '',
                    'tlSubmitted' => true
                ];

                // Add file paths if uploaded
                if (isset($uploadedFiles['tLicensePhotoFront'])) {
                    $updateData['tLicensePhotoFront'] = $uploadedFiles['tLicensePhotoFront'];
                }
                if (isset($uploadedFiles['tLicensePhotoBack'])) {
                    $updateData['tLicensePhotoBack'] = $uploadedFiles['tLicensePhotoBack'];
                }

                $updateSuccess = $this->driverModel->submitTouristLicense($userId, $updateData);

                if ($updateSuccess) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Tourist license submitted successfully for review'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to update tourist license information']);
                }

            } catch (Exception $e) {
                error_log("Exception in submitTLicense: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
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

        public function editTouristLicense() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');

            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            // Log incoming data
            error_log("editTouristLicense called for userId: " . $userId);
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
                $fileFields = ['tLicensePhotoFront', 'tLicensePhotoBack'];
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
                            $filename = 'tourist_license_' . $fieldName . '_' . time() . '.' . $extension;
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

                // Update profile_details table (don't set tlSubmitted since it's already submitted)
                $updateData = [
                    'tLicenseNumber' => $_POST['touristLicenseNumber'] ?? '',
                    'tLicenseExpiryDate' => $_POST['touristLicenseExpiry'] ?? ''
                ];

                // Add file paths if uploaded
                if (isset($uploadedFiles['tLicensePhotoFront'])) {
                    $updateData['tLicensePhotoFront'] = $uploadedFiles['tLicensePhotoFront'];
                }
                if (isset($uploadedFiles['tLicensePhotoBack'])) {
                    $updateData['tLicensePhotoBack'] = $uploadedFiles['tLicensePhotoBack'];
                }

                $updateSuccess = $this->driverModel->updateTouristLicense($userId, $updateData);

                if ($updateSuccess) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Tourist license updated successfully'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to update tourist license information']);
                }

            } catch (Exception $e) {
                error_log("Exception in editTouristLicense: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }

    }


    // `/controller/method/parameters
?>

