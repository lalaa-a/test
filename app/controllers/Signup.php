<?php 
    class Signup extends Controller {
        protected $userModel;

        public function __construct() {
            // $this->userModel = $this->model('User');
        }

        public function index() {
            $this->view('signup/signup');
            // Code to display the signup form
        }


        public function handleSignup() {
            // Code to handle the signup form submission
        }
    }