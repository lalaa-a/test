<?php
    class DriverController extends Controller {
        private $driverModel;
        
        public function __construct() {
            $this->driverModel = $this->model('Driver');
        }

        // Main driver page
        public function index() {
            $trendingDrivers = $this->driverModel->getTrendingDrivers();
            $licensedDrivers = $this->driverModel->getLicensedDrivers();
            $reviewedDrivers = $this->driverModel->getReviewedDrivers(6);
            $touristDrivers = $this->driverModel->getTouristDrivers(6);
            
            $data = [
                'trendingDrivers' => $trendingDrivers,
                'licensedDrivers' => $licensedDrivers,
                'reviewedDrivers' => $reviewedDrivers,
                'touristDrivers' => $touristDrivers,
                'title' => 'Plan Your Way Through Sri-Lanka\'s Wonders'
            ];
            $this->view('driver/index', $data);
        }

        // Licensed drivers page
        public function licensed() {
            $licensedDrivers = $this->driverModel->getLicensedDrivers();
            $data = [
                'drivers' => $licensedDrivers,
                'title' => 'Licensed Drivers'
            ];
            $this->view('driver/licensed', $data);
        }

        // Reviewed drivers page
        public function reviewed() {
            $reviewedDrivers = $this->driverModel->getReviewedDrivers();
            $data = [
                'drivers' => $reviewedDrivers,
                'title' => 'Reviewed Drivers'
            ];
            $this->view('driver/reviewed', $data);
        }

        // Tourist drivers page
        public function tourist() {
            $touristDrivers = $this->driverModel->getTouristDrivers();
            $data = [
                'drivers' => $touristDrivers,
                'title' => 'Tourist Drivers'
            ];
            $this->view('driver/tourist', $data);
        }
    }
?>
