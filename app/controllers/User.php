<?php
    require_once '../app/helpers/session_helper.php';
    require_once '../app/helpers/upload_helper.php';
    require_once '../app/helpers/mail_helper.php';

    // Load Composer's autoloader
    

    class User extends Controller{
        
        private $userModel;
        public function __construct() {
            $this->userModel = $this->model('UserModel');
        }
            
        
        public function trips() {
            requireLogin();
            $this->view('Trips');
        }
        

        public function plannedTrip() {
            requireLogin();
            $this->view('PlannedTrip/PlannedTrip');
        }

        public function plannedTripSaves(){
            requireLogin();
            $this->view('PlannedTrip/PlannedTripSaves');
        }

        public function plannedTripItinerary(){
            requireLogin();
            $this->view('PlannedTrip/PlannedTripItinerary');
        }

        public function allDestinations() {
            requireLogin();
            $this->view('Explore/destinations/allDestinations');
        }

        public function cultureHeritage() {
            requireLogin();
            $this->view('Explore/destinations/cultureHeritage');
        }

        public function natureAdventure() {
            requireLogin();
            $this->view('Explore/destinations/natureAndAdventure');
        }

        public function relaxationLeisure() {
            requireLogin();
            $this->view('Explore/destinations/relaxationLeisure');
        }

        public function entertainmentActivities() {
            requireLogin();
            $this->view('Explore/destinations/entertainmentActivities');
        }

        public function destDetails() {
            requireLogin();
            $this->view('Explore/destinations/destDetails');
        }

        public function packages() {
            requireLogin();
            $this->view('Explore/packages');
        }

        public function trending() {
            requireLogin();
            $this->view('Explore/trending');
        }

        public function aboutUs() {
            $this->view('aboutUs');
        }

        //For register a user
        public function register() {
            // Redirect if already logged in
            redirectIfLoggedIn();
            
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                header('Content-Type: application/json');

                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
                    return;
                }

                // Sanitize inputs
                $accountType = trim($_POST['account_type'] ?? '');
                $fullname = trim($_POST['fullname'] ?? '');
                $language = trim($_POST['language'] ?? '');
                $dob = trim($_POST['dob'] ?? '');
                $gender = trim($_POST['gender'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                $secondaryPhone = trim($_POST['secondary_phone'] ?? '');
                $address = trim($_POST['address'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';

                // Validation
                if (!in_array($accountType, ['driver', 'guide', 'tourist'])) {
                    echo json_encode(['success' => false, 'message' => 'Invalid account type']);
                    return;
                }

                if (empty($fullname) || empty($language) || empty($dob) || empty($gender) || empty($phone) || empty($address) || empty($email) || empty($password)) {
                    echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
                    return;
                }

                if (strlen($password) < 8) {
                    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long']);
                    return;
                }

                if ($password !== $confirmPassword) {
                    echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
                    return;
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
                    return;
                }

                if ($this->userModel->emailExists($email)) {
                    echo json_encode(['success' => false, 'message' => 'Email already registered']);
                    return;
                }

                // Check if email is verified
                if (!isset($_SESSION['email_verified']) || $_SESSION['email_verified'] !== $email) {
                    echo json_encode(['success' => false, 'message' => 'Email verification required']);
                    return;
                }

                // Upload profile photo
                $profilePhoto = uploadFile($_FILES['profile_photo'] ?? null,'/signup/profile','profile');
                if (!$profilePhoto) {
                    echo json_encode(['success' => false, 'message' => 'Profile photo is required and must be a valid image (JPG/PNG, max 5MB)']);
                    return;
                }

                // Handle account-type-specific data
                $driverData = null;
                $guideTouristData = null;

                if ($accountType === 'driver') {
                    $licenseNumber = trim($_POST['license_number'] ?? '');
                    $licenseExpireDate = trim($_POST['license_expire_date'] ?? '');

                    if ( empty($licenseNumber) || empty($licenseExpireDate)) {
                        echo json_encode(['success' => false, 'message' => 'All driver fields are required']);
                        return;
                    }

                    $licenseFront = uploadFile($_FILES['licenseFront'] ?? null,'/signup/license','license_front',);
                    $licenseBack = uploadFile($_FILES['licenseBack'] ?? null, '/signup/license','license_back',);
                    $idFront = uploadFile($_FILES['idFront'] ?? null, '/signup/nic','nic_front_driver');
                    $idBack = uploadFile($_FILES['idBack'] ?? null, '/signup/nic','nic_back_driver');
                    if (!$licenseFront || !$licenseBack || !$idFront || !$idBack) {
                        echo json_encode(['success' => false, 'message' => 'All driver documents are required']);
                        return;
                    }

                    $driverData = json_encode([
                        'license_number' => $licenseNumber,
                        'license_expire_date' => $licenseExpireDate,
                        'license_front' => $licenseFront,
                        'license_back' => $licenseBack,
                        'id_front' => $idFront,
                        'id_back' => $idBack
                    ]);
                } else {
                    // Guide or Tourist
                    $nicPassport = trim($_POST['nic_passport'] ?? '');
                    if (empty($nicPassport)) {
                        echo json_encode(['success' => false, 'message' => 'NIC/Passport is required']);
                        return;
                    }

                    $nicFront = uploadFile($_FILES['nic_front'] ?? null, '/signup/nic','nic_front');
                    $nicBack = uploadFile($_FILES['nic_back'] ?? null, '/signup/nic','nic_back');

                    if (!$nicFront || !$nicBack) {
                        echo json_encode(['success' => false, 'message' => 'Both NIC/Passport images are required']);
                        return;
                    }

                    $guideTouristData = json_encode([
                        'nic_passport' => $nicPassport,
                        'nic_front' => $nicFront,
                        'nic_back' => $nicBack
                    ]);
                }

                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Prepare data for model
                $userData = [
                    'account_type' => $accountType,
                    'fullname' => htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8'),
                    'language' => $language,
                    'dob' => $dob,
                    'gender' => $gender,
                    'phone' => $phone,
                    'secondary_phone' => $secondaryPhone,
                    'address' => htmlspecialchars($address, ENT_QUOTES, 'UTF-8'),
                    'email' => $email,
                    'password' => $hashedPassword,
                    'profile_photo' => $profilePhoto,
                    'driver_data' => $driverData,
                    'guide_tourist_data' => $guideTouristData
                ];

                // Double-check email existence right before insert
                if ($this->userModel->emailExists($email)) {
                    echo json_encode(['success' => false, 'message' => 'This email is already registered. Please use a different email address.']);
                    return;
                }

                try {
                    if ($this->userModel->create($userData)) {
                        unset($_SESSION['email_verified']); // Clear email verification after successful registration
                        echo json_encode(['success' => true, 'message' => 'Account created successfully']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to create account. Please try again.']);
                    }
                } catch (PDOException $e) {
                    // Check if it's a duplicate entry error
                    if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'email') !== false) {
                        echo json_encode(['success' => false, 'message' => 'This email is already registered. Please use a different email address.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Database error occurred. Please try again.']);
                    }
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred. Please try again.']);
                }
            }
                

            else{
                // Load view
                $this->view('Users/register');
            }
        }

        public function sendOTP() {
            header('Content-Type: application/json');
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $email = trim($input['email'] ?? '');

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Valid email is required']);
                return;
            }

            // Check if email already exists
            if ($this->userModel->emailExists($email)) {
                echo json_encode(['success' => false, 'message' => 'This email is already registered']);
                return;
            }

            // Generate 6-digit OTP
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Store OTP in session with expiration (10 minutes)
            $_SESSION['otp'] = [
                'code' => $otp,
                'email' => $email,
                'expires' => time() + (10 * 60) // 10 minutes
            ];

            // Send OTP email using helper function
            $result = sendOTPEmail($email, $otp);
            
            echo json_encode($result);
        }

        public function verifyOTP() {
            header('Content-Type: application/json');
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $email = trim($input['email'] ?? '');
            $otp = trim($input['otp'] ?? '');

            if (empty($email) || empty($otp)) {
                echo json_encode(['success' => false, 'message' => 'Email and OTP are required']);
                return;
            }

            // Check if OTP exists in session
            if (!isset($_SESSION['otp']) || 
                $_SESSION['otp']['email'] !== $email || 
                $_SESSION['otp']['code'] !== $otp) {
                echo json_encode(['success' => false, 'message' => 'Invalid OTP']);
                return;
            }

            // Check if OTP is expired
            if (time() > $_SESSION['otp']['expires']) {
                unset($_SESSION['otp']);
                echo json_encode(['success' => false, 'message' => 'OTP has expired. Please request a new one.']);
                return;
            }

            // OTP is valid
            $_SESSION['email_verified'] = $email;
            unset($_SESSION['otp']); // Clear the OTP after successful verification
            
            echo json_encode(['success' => true, 'message' => 'Email verified successfully']);
        }

        //For login a user
        public function login() {
            // Redirect if already logged in
            redirectIfLoggedIn();
            
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                
                header('Content-Type: application/json');

                // Sanitize inputs
                $email = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';
                $remember = isset($_POST['remember']) && $_POST['remember'] === 'on';

                // Basic validation
                if (empty($email) || empty($password)) {
                    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
                    return;
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
                    return;
                }

                if (strlen($password) < 8) {
                    echo json_encode(['success' => false, 'message' => 'Invalid password length']);
                    return;
                }

                try {
                    // Find user by email
                    $user = $this->userModel->findByEmail($email);
                    
                    if (!$user) {
                        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
                        return;
                    }

                    // Verify password
                    if (!password_verify($password, $user->password)) {
                        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
                        return;
                    }

                    // Set user session
                    $userData = [
                        'id' => $user->id,
                        'email' => $user->email,
                        'fullname' => $user->fullname,
                        'account_type' => $user->account_type,
                        'profile_photo' => $user->profile_photo
                    ];
                    
                    setUserSession($userData);

                    // Handle remember me
                    if ($remember) {
                        /*
                        $rememberToken = bin2hex(random_bytes(32));
                        $this->userModel->updateRememberToken($user->id, $rememberToken);*/

                        setRememberMeCookie($userData, 2592000); // 30 days
                    }

                    // Update last login
                    $this->userModel->updateLastLogin($user->id);

                    // Regenerate session ID for security
                    regenerateSession();

                    echo json_encode([
                        'success' => true, 
                        'message' => 'Login successful! Redirecting...',
                        'user' => [
                            'account_type' => $user->account_type,
                            'fullname' => $user->fullname,
                            'profile_photo' => $user->profile_photo
                        ],
                        'redirect_url' => $this->getRedirectUrl($user->account_type)
                    ]);

                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'An error occurred during login. Please try again.']);
                }

            } else {
                // Load login view
                $this->view('Users/login');
            }
        }

        //For logout a user
        public function logout() {
        
            // Clear remember me cookie
            clearRememberMeCookie();
            
            // Clear user session
            clearUserSession();
            
            // Destroy session
            destroySession();
            
            // Set success message
            setFlash('success', 'You have been logged out successfully');
            
            // Redirect to login page
            header('Location: ' . URL_ROOT . '/user/login');
            exit();
        }

        //Check session (for AJAX calls)
        public function checkSession() {
            header('Content-Type: application/json');
            
            if (isLoggedIn()) {
                $user = getLoggedInUser();
                echo json_encode([
                    'success' => true,
                    'logged_in' => true,
                    'user' => $user
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'logged_in' => false
                ]);
            }
        }

        // Account info page
        public function account() {
            // Check if user is logged in
            if (!isLoggedIn()) {
                header('Location: ' . URL_ROOT . '/User/login');
                exit();
            }

            // Get current user data from database
            $userId = getSession('user_id');
            $user = $this->userModel->findById($userId);
            
            if (!$user) {
                setFlash('error', 'User not found');
                header('Location: ' . URL_ROOT . '/User/login');
                exit();
            }

            $data = [
                'user' => $user,
                'title' => 'My Account'
            ];

            $this->view('Users/account', $data);
        }

        // Update user account information
        public function updateAccount() {
            header('Content-Type: application/json');
            
            // Check if user is logged in
            if (!isLoggedIn()) {
                echo json_encode(['success' => false, 'message' => 'Please log in to continue']);
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                $userId = getSession('user_id');
                
                // Validate required fields
                $required_fields = ['fullname', 'email', 'phone', 'language', 'gender', 'dob', 'address'];
                foreach ($required_fields as $field) {
                    if (empty($input[$field])) {
                        echo json_encode(['success' => false, 'message' => "Field $field is required"]);
                        return;
                    }
                }

                // Validate email format
                if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
                    return;
                }

                // Check if email is already taken by another user
                $existingUser = $this->userModel->findByEmail($input['email']);
                if ($existingUser && $existingUser->id != $userId) {
                    echo json_encode(['success' => false, 'message' => 'Email already exists']);
                    return;
                }

                // Prepare update data
                $updateData = [
                    'id' => $userId,
                    'fullname' => htmlspecialchars(trim($input['fullname']), ENT_QUOTES, 'UTF-8'),
                    'email' => trim($input['email']),
                    'phone' => trim($input['phone']),
                    'secondary_phone' => trim($input['secondary_phone'] ?? ''),
                    'language' => $input['language'],
                    'gender' => $input['gender'],
                    'dob' => $input['dob'],
                    'address' => htmlspecialchars(trim($input['address']), ENT_QUOTES, 'UTF-8')
                ];

                try {
                    $result = $this->userModel->updateUser($updateData);
                    
                    if ($result) {
                        // Update session data
                        setSession('user_fullname', $updateData['fullname']);
                        setSession('user_email', $updateData['email']);
                        
                        echo json_encode(['success' => true, 'message' => 'Account updated successfully']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to update account']);
                    }
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'An error occurred while updating your account']);
                }
            }
        }

        // Update profile photo
        public function updateProfilePhoto() {
            header('Content-Type: application/json');
            
            // Check if user is logged in
            if (!isLoggedIn()) {
                echo json_encode(['success' => false, 'message' => 'Please log in to continue']);
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $userId = getSession('user_id');
                
                // Check if file was uploaded
                if (!isset($_FILES['profile_photo']) || $_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK) {
                    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
                    return;
                }

                // Upload new profile photo
                $profilePhoto = uploadFile($_FILES['profile_photo'], 'profile','/signup/profile');
                if (!$profilePhoto) {
                    echo json_encode(['success' => false, 'message' => 'Failed to upload image. Please ensure it\'s a valid JPG/PNG file under 5MB']);
                    return;
                }

                try {
                    // Update profile photo in database
                    $result = $this->userModel->updateProfilePhoto($userId, $profilePhoto);
                    
                    if ($result) {
                        // Update session data
                        setSession('user_profile_photo', $profilePhoto);
                        
                        $newPhotoUrl = URL_ROOT . '/uploads/' . $profilePhoto;
                        echo json_encode([
                            'success' => true, 
                            'message' => 'Profile photo updated successfully',
                            'new_photo_url' => $newPhotoUrl
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to update profile photo']);
                    }
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'An error occurred while updating your photo']);
                }
            }
        }

        // Get redirect URL based on account type
        private function getRedirectUrl($accountType) {
            switch ($accountType) {
                case 'admin':
                    return URL_ROOT . '/dashboard/admin';
                case 'driver':
                    return URL_ROOT . '/dashboard/driver';
                case 'guide':
                    return URL_ROOT . '/dashboard/guide';
                case 'tourist':
                    return URL_ROOT . '/RegUser/home';
                case 'site_moderator':
                    return URL_ROOT . '/dashboard/siteModerator';
                case 'business_manager':
                    return URL_ROOT . '/dashboard/businessManager';
                default:
                    return URL_ROOT . '/Home';
            }
        }
}



