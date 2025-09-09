<?php
    class User extends Controller{
        /*
        private $userModel;
        public function __construct() {
            $this->userModel = $this->model('M_users');
        }
        */

        public function trips() {
            $this->view('Trips');
        }

        public function plannedTrip() {
            $this->view('PlannedTrip/PlannedTrip');
        }

        public function plannedTripSaves(){
            $this->view('PlannedTrip/PlannedTripSaves');
        }

        public function plannedTripItinerary(){
            $this->view('PlannedTrip/PlannedTripItinerary');
        }

    }


    // `/controller/method/parameters
?>

