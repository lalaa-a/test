<?php
    class Driver extends Controller{
        /*
        private $userModel;
        public function __construct() {
            $this->userModel = $this->model('M_users');
        }
        */

        public function index() {
            $this->view('Driver/driverDash');
        }

        public function dashboard(){
            $this->view('ControlPanels/driverControlPanel2');
        }

        public function tours(){
            $this->view('ControlPanels/driverControlPanel2');
        }
    }


    // `/controller/method/parameters
?>

