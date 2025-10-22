<?php
    class Guide extends Controller{
        /*
        private $userModel;
        public function __construct() {
            $this->userModel = $this->model('M_users');
        }
        */

        public function index() {
            $this->view('Guide/guideDash');
        }

    }


    // `/controller/method/parameters
?>

