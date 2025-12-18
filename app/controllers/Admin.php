<?php
    class Admin extends Controller{
        private $userModel;
        
        public function __construct() {
            $this->userModel = $this->model('UserModel');
        }

        public function index() {
            // Check if user is admin
            if (!isLoggedIn() || getSession('user_account_type') !== 'admin') {
                header('Location: ' . URL_ROOT . '/User/login');
                exit();
            }
            $this->view('Admin/adminDash');
        }

        public function addModerator() {
            // Check if user is admin
            if (!isLoggedIn() || getSession('user_account_type') !== 'admin') {
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                // Validate required fields
                $required_fields = ['fullname', 'email', 'password', 'phone', 'language', 'dob', 'gender', 'address', 'account_type'];
                foreach ($required_fields as $field) {
                    if (empty($input[$field])) {
                        echo json_encode(['success' => false, 'message' => "Field $field is required"]);
                        return;
                    }
                }

                // Validate account type
                if (!in_array($input['account_type'], ['site_moderator', 'business_manager'])) {
                    echo json_encode(['success' => false, 'message' => 'Invalid account type']);
                    return;
                }

                // Validate date of birth - cannot be in the future
                $dobDate = new DateTime($input['dob']);
                $today = new DateTime();
                if ($dobDate > $today) {
                    echo json_encode(['success' => false, 'message' => 'Date of birth cannot be in the future']);
                    return;
                }

                // Validate minimum age (optional - set to 18 years)
                $minAgeDate = new DateTime();
                $minAgeDate->sub(new DateInterval('P18Y')); // 18 years ago
                if ($dobDate > $minAgeDate) {
                    echo json_encode(['success' => false, 'message' => 'Moderator must be at least 18 years old']);
                    return;
                }

                // Check if email already exists
                if ($this->userModel->findUserByEmail($input['email'])) {
                    echo json_encode(['success' => false, 'message' => 'Email already exists']);
                    return;
                }

                // Hash password
                $input['password'] = password_hash($input['password'], PASSWORD_DEFAULT);

                // Add moderator
                $result = $this->userModel->addModerator($input);
                
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Moderator added successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to add moderator']);
                }
            }
        }

        public function getModerators() {
            // Check if user is admin
            if (!isLoggedIn() || getSession('user_account_type') !== 'admin') {
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }

            $moderators = $this->userModel->getModerators();
            echo json_encode(['success' => true, 'moderators' => $moderators]);
        }

        public function getModerator() {
            // Check if user is admin
            if (!isLoggedIn() || getSession('user_account_type') !== 'admin') {
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $moderatorId = $_GET['id'] ?? null;
                
                if (empty($moderatorId)) {
                    echo json_encode(['success' => false, 'message' => 'Moderator ID is required']);
                    return;
                }

                $moderator = $this->userModel->getModeratorById($moderatorId);
                
                if ($moderator) {
                    echo json_encode(['success' => true, 'moderator' => $moderator]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Moderator not found']);
                }
            }
        }

        public function updateModerator() {
            // Check if user is admin
            if (!isLoggedIn() || getSession('user_account_type') !== 'admin') {
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                // Validate required fields
                $required_fields = ['id', 'fullname', 'email', 'phone', 'language', 'dob', 'gender', 'address', 'account_type'];
                foreach ($required_fields as $field) {
                    if (empty($input[$field])) {
                        echo json_encode(['success' => false, 'message' => "Field $field is required"]);
                        return;
                    }
                }

                // Validate account type
                if (!in_array($input['account_type'], ['site_moderator', 'business_manager'])) {
                    echo json_encode(['success' => false, 'message' => 'Invalid account type']);
                    return;
                }

                // Validate date of birth - cannot be in the future
                $dobDate = new DateTime($input['dob']);
                $today = new DateTime();
                if ($dobDate > $today) {
                    echo json_encode(['success' => false, 'message' => 'Date of birth cannot be in the future']);
                    return;
                }

                // Validate minimum age (optional - set to 18 years)
                $minAgeDate = new DateTime();
                $minAgeDate->sub(new DateInterval('P18Y')); // 18 years ago
                if ($dobDate > $minAgeDate) {
                    echo json_encode(['success' => false, 'message' => 'Moderator must be at least 18 years old']);
                    return;
                }

                // Check if email already exists for other users
                $existingUser = $this->userModel->findUserByEmail($input['email']);
                if ($existingUser && $existingUser->id != $input['id']) {
                    echo json_encode(['success' => false, 'message' => 'Email already exists']);
                    return;
                }

                // Update moderator
                $result = $this->userModel->updateModerator($input);
                
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Moderator updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update moderator']);
                }
            }
        }

        public function deleteModerator() {
            // Check if user is admin
            if (!isLoggedIn() || getSession('user_account_type') !== 'admin') {
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (empty($input['id'])) {
                    echo json_encode(['success' => false, 'message' => 'Moderator ID is required']);
                    return;
                }

                $result = $this->userModel->deleteModerator($input['id']);
                
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Moderator deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete moderator']);
                }
            }
        }

        public function getItinerary() {
            // Check if user is admin
            if (!isLoggedIn() || getSession('user_account_type') !== 'admin') {
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                header('Content-Type: application/json');
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (empty($input['itinerary_number'])) {
                    echo json_encode(['success' => false, 'message' => 'Itinerary number is required']);
                    return;
                }

                $itineraryNumber = trim($input['itinerary_number']);
                
                // Get itinerary details including guide and driver information
                $itinerary = $this->userModel->getItineraryDetails($itineraryNumber);
                
                if ($itinerary) {
                    echo json_encode([
                        'success' => true, 
                        'data' => $itinerary
                    ]);
                } else {
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Itinerary not found'
                    ]);
                }
            }
        }
    }

    // `/controller/method/parameters
?>

