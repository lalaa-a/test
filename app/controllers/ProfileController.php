<?php 
    class ProfileController extends Controller {

        private $profileControllerModel;
        private $userModel;

        public function __construct() {
            $this->profileControllerModel = $this->model('ProfileControllerModel');
            $this->userModel = $this->model('UserModel');
        }

        public function changeProfilePhoto() {
            header('Content-Type: application/json');
            
            // Check if user is logged in
            if (!isLoggedIn()) {
                echo json_encode(['success' => false, 'message' => 'Please log in to continue']);
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $userId = getSession('user_id');
                
                // Check if file was uploaded
                if (!isset($_FILES['profilePhoto']) || $_FILES['profilePhoto']['error'] !== UPLOAD_ERR_OK) {
                    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
                    return;
                }

                // Upload new profile photo
                $profilePhoto = uploadFile($_FILES['profilePhoto'],'/signup/profile','profile');
                if (!$profilePhoto) {
                    echo json_encode(['success' => false, 'message' => 'Failed to upload image. Please ensure it\'s a valid JPG/PNG file under 5MB']);
                    return;
                }

                try {
                    // Update profile photo in database
                    $result = $this->profileControllerModel->updateProfilePhoto($userId, $profilePhoto);
                    
                    if ($result) {
                        // Update session data
                        setSession('user_profile_photo', $profilePhoto);
                        
                        $newPhotoUrl = URL_ROOT . '/uploads/' . $profilePhoto;
                        echo json_encode([
                            'success' => true, 
                            'message' => 'Profile photo updated successfully',
                            'photoPath' => $newPhotoUrl
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to update profile photo']);
                    }
                } catch (Exception $e) {
                    error_log("Exception in changeProfilePhoto: " . $e->getMessage());
                    echo json_encode(['success' => false, 'message' => 'An error occurred while updating your photo']);
                }
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
                $photos = $this->profileControllerModel->getCoverPhotos($userId);
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
                        $deletedCount = $this->profileControllerModel->deleteCoverPhotos($deletedPhotoIds);
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
                    $saveSuccess = $this->profileControllerModel->saveCoverPhotos($userId, $uploadedPhotos);
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

                $updateSuccess = $this->profileControllerModel->submitTouristLicense($userId, $updateData);

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

                $updateSuccess = $this->profileControllerModel->updateTouristLicense($userId, $updateData);

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

        public function sendEmailChangeOTP() {
            header('Content-Type: application/json');

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $newEmail = trim($input['newEmail'] ?? '');
            $currentEmail = trim($input['currentEmail'] ?? '');
            $password = $input['password'] ?? '';

            // Validate inputs
            if (empty($newEmail) || empty($currentEmail) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'All fields are required']);
                return;
            }

            if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Invalid new email format']);
                return;
            }

            // Get current user
            $userId = getSession('user_id');
            $user = $this->userModel->findById($userId);

            if (!$user) {
                echo json_encode(['success' => false, 'message' => 'User not found']);
                return;
            }

            // Verify current email matches
            if ($user->email !== $currentEmail) {
                echo json_encode(['success' => false, 'message' => 'Current email does not match']);
                return;
            }

            // Verify password
            if (!password_verify($password, $user->password)) {
                echo json_encode(['success' => false, 'message' => 'Invalid password']);
                return;
            }

            // Check if new email is already taken
            if ($this->userModel->emailExists($newEmail)) {
                echo json_encode(['success' => false, 'message' => 'This email is already registered']);
                return;
            }

            // Generate 6-digit OTP
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // Store OTP in session with expiration (10 minutes)
            $_SESSION['email_change_otp'] = [
                'code' => $otp,
                'new_email' => $newEmail,
                'user_id' => $userId,
                'expires' => time() + (10 * 60) // 10 minutes
            ];

            // Send OTP email using helper function
            $result = sendOTPEmail($newEmail, $otp);

            echo json_encode($result);
        }

        public function verifyEmailChangeOTP() {
            header('Content-Type: application/json');

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $otp = trim($input['otp'] ?? '');

            if (empty($otp)) {
                echo json_encode(['success' => false, 'message' => 'OTP is required']);
                return;
            }

            // Check if OTP exists in session
            if (!isset($_SESSION['email_change_otp'])) {
                echo json_encode(['success' => false, 'message' => 'No email change request found']);
                return;
            }

            $otpData = $_SESSION['email_change_otp'];

            // Check if OTP matches
            if ($otpData['code'] !== $otp) {
                echo json_encode(['success' => false, 'message' => 'Invalid OTP']);
                return;
            }

            // Check if OTP is expired
            if (time() > $otpData['expires']) {
                unset($_SESSION['email_change_otp']);
                echo json_encode(['success' => false, 'message' => 'OTP has expired. Please request a new one.']);
                return;
            }

            // Update email in database
            try {
                $updateData = [
                    'id' => $otpData['user_id'],
                    'email' => $otpData['new_email']
                ];
                
                // Get current user data to preserve other fields
                $currentUser = $this->userModel->findById($otpData['user_id']);
                if ($currentUser) {
                    $updateData = array_merge([
                        'fullname' => $currentUser->fullname,
                        'phone' => $currentUser->phone,
                        'secondary_phone' => $currentUser->secondary_phone,
                        'language' => $currentUser->language,
                        'gender' => $currentUser->gender,
                        'dob' => $currentUser->dob,
                        'address' => $currentUser->address
                    ], $updateData);
                }

                $result = $this->userModel->updateUser($updateData);

                if ($result) {
                    // Update session data
                    setSession('user_email', $otpData['new_email']);

                    // Clear OTP from session
                    unset($_SESSION['email_change_otp']);

                    echo json_encode([
                        'success' => true,
                        'message' => 'Email updated successfully',
                        'new_email' => $otpData['new_email']
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update email']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'An error occurred while updating your email']);
            }
        }
    }

?>