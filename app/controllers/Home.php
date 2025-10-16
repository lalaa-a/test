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
    }


    // `/controller/method/parameters
?>

