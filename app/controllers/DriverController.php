<?php
    class DriverController extends Controller {
        private $driverModel;
        
        public function __construct() {
            requireLogin();
            $this->driverModel = $this->model('ExploreDrivers');
        }

        // Main driver page
        public function index() {
            $trendingDrivers1 = $this->driverModel->getTrendingDrivers();
            $licensedDrivers1 = $this->driverModel->getLicensedDrivers();
            $reviewedDrivers1 = $this->driverModel->getReviewedDrivers(6);
            $touristDrivers1 = $this->driverModel->getTouristDrivers(6);
            
            $data = [
                'trendingDrivers' => $trendingDrivers1,
                'licensedDrivers' => $licensedDrivers1,
                'reviewedDrivers' => $reviewedDrivers1,
                'touristDrivers' => $touristDrivers1,
                'title' => 'Plan Your Way Through Sri-Lanka\'s Wonders'
            ];
            $this->view('Explore/driver/index', $data);
        }

        // Licensed drivers page
        public function licensed() {
            $licensedDrivers = $this->driverModel->getLicensedDrivers();
            
            $data = [
                'drivers' => $licensedDrivers,
                'title' => 'Licensed Drivers'
            ];
            $this->view('Explore/driver/licensed', $data);
        }

        public function driverDetail() {
            requireLogin();
            $this->view('Explore/driver/driverDetail');
        }

        // Reviewed drivers page
        public function reviewed() {
            $reviewedDrivers = $this->driverModel->getReviewedDrivers();
            
            $data = [
                'drivers' => $reviewedDrivers,
                'title' => 'Reviewed Drivers'
            ];
            $this->view('Explore/driver/reviewed', $data);
        }

        // Tourist drivers page
        public function tourist() {
            $touristDrivers = $this->driverModel->getTouristDrivers();
            
            $data = [
                'drivers' => $touristDrivers,
                'title' => 'Tourist Drivers'
            ];
            $this->view('Explore/driver/tourist', $data);
        }
    }
?>
