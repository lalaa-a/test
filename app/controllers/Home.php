<?php
    class Home extends Controller{
        /*
        private $userModel;
        public function __construct() {
            $this->userModel = $this->model('M_users');
        }
        */

        public function index() {
            $this->view('Homepage');
        }

        public function trips() {
            $this->view('Explore/trending');
        }

        public function dest() {
            $this->view('Explore/packages');
        }

        public function drive() {
            $this->view('Explore/driver/driver_detail');
        }
    }


    // `/controller/method/parameters
?>

