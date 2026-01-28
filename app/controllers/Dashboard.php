<?php
    //require_once '../app/helpers/session_helper.php';
    
    class Dashboard extends Controller {
        
        public function __construct() {
            // Require login for all dashboard methods
            requireLogin();
        }
        
        public function index() {
            $user = getLoggedInUser();
            
            $data = [
                'title' => 'Dashboard',
                'user' => $user
            ];
            
            $this->view('dashboard/index', $data);
        }

        // Admin specific dashboard
        public function admin() {
            $user = getLoggedInUser();
            
            // Check if user is actually a guide
            if ($user['account_type'] !== 'admin') {
                header('Location: ' . URL_ROOT . '/dashboard');
                exit();
            }
            
            $data = [
                'title' => 'Admin Dashboard',
                'user' => $user
            ];
            
            $this->view('Admin/adminDash', $data);
        }

        // SiteModerator specific dashboard
        public function siteModerator() {
            $user = getLoggedInUser();
            
            // Check if user is actually a guide
            if ($user['account_type'] !== 'site_moderator') {
                header('Location: ' . URL_ROOT . '/dashboard');
                exit();
            }
            
            $data = [
                'title' => 'siteModerator Dashboard',
                'user' => $user
            ];
            
            $this->view('SiteModerator/SiteModeratorDash', $data);
        }

        // Business Manager specific dashboard
        public function businessManager() {
            $user = getLoggedInUser();
            
            // Check if user is actually a guide
            if ($user['account_type'] !== 'business_manager') {
                header('Location: ' . URL_ROOT . '/dashboard');
                exit();
            }
            
            $data = [
                'title' => 'siteModerator Dashboard',
                'user' => $user
            ];
            
            $this->view('BuisManager/buisDash', $data);
        }
        
        // Driver specific dashboard
        public function driver() {
            $user = getLoggedInUser();
            
            // Check if user is actually a driver
            if ($user['account_type'] !== 'driver') {
                header('Location: ' . URL_ROOT . '/dashboard');
                exit();
            }
            
            $data = [
                'title' => 'Driver Dashboard',
                'user' => $user
            ];
            
            $this->view('Driver/driverDash', $data);
        }
        
        // Guide specific dashboard
        public function guide() {
            $user = getLoggedInUser();
            
            // Check if user is actually a guide
            if ($user['account_type'] !== 'guide') {
                header('Location: ' . URL_ROOT . '/dashboard');
                exit();
            }
            
            $data = [
                'title' => 'Guide Dashboard',
                'user' => $user
            ];
            
            $this->view('Guide/guideDash', $data);
        }
    }
?>