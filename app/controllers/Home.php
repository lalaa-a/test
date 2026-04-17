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
            $this->view('pay');
        }

        public function dest() {
            $this->view('landing');
        }

        public function drive() {
            $this->view('Explore/driver/driver_detail');
        }

        public function test() {
            $this->view('Admin/adminDash1');
        }

    }


    // `/controller/method/parameters
?>

