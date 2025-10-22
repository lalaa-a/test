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
    }

    // `/controller/method/parameters
?>

