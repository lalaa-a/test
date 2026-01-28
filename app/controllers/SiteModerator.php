<?php
    class SiteModerator extends Controller{
        /*
        private $userModel;
        public function __construct() {
            $this->userModel = $this->model('M_users');
        }
        */

        public function index() {
            $this->view('SiteModerator/SiteModeratorDash');
        }
    }


    // `/controller/method/parameters
?>

