<?php
    class Home extends Controller{
        /*
        private $userModel;
        public function __construct() {
            $this->userModel = $this->model('M_users');
        }
        */

        // index page (first loading page landing page)
        public function index() {
            redirectIfLoggedIn();
            $this->view('landing');
        }


        //testing functions meewa haduwe na

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

