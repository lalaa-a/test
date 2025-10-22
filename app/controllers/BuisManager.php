<?php
    class BuisManager extends Controller{
        /*
        private $userModel;
        public function __construct() {
            $this->userModel = $this->model('M_users');
        }
        */

        public function index() {
            $this->view('BuisManager/buisDash');
        }
    }


    // `/controller/method/parameters
?>

