<?php
    class UserController extends Controller {
        private $userModel;
        
        public function __construct() {
            $this->userModel = $this->model('User');
        }

        public function index() {
            $users = $this->userModel->getUsers();
            $data = [
                'users' => $users,
                'title' => 'Users'
            ];
            $this->view('user/index', $data);
        }

        public function profile($id = null) {
            if ($id) {
                $user = $this->userModel->getUserById($id);
                $data = [
                    'user' => $user,
                    'title' => 'User Profile'
                ];
                $this->view('user/profile', $data);
            } else {
                header('Location: /test/user');
                exit();
            }
        }

        public function trips() {
            $this->view('user/trips');
        }
    }
?>
