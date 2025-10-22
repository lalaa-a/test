<?php
    class GuideController extends Controller {
        private $guideModel;
        
        public function __construct() {
            requireLogin();
            $this->guideModel = $this->model('ExploreGuides');
        }

        // Main guide page
        public function index() {
            $trendingGuides = $this->guideModel->getTrendingGuides();
            $licensedGuides = $this->guideModel->getLicensedGuides();
            $reviewedGuides = $this->guideModel->getReviewedGuides(6);
            $touristGuides = $this->guideModel->getTouristGuides(6);
            
            $data = [
                'trendingGuides' => $trendingGuides,
                'licensedGuides' => $licensedGuides,
                'reviewedGuides' => $reviewedGuides,
                'touristGuides' => $touristGuides,
                'title' => 'Plan Your Way Through Sri-Lanka\'s Wonders - Expert Guides'
            ];
            $this->view('Explore/guide/index', $data);
        }

        public function guideDetail() {
            requireLogin();
            $this->view('Explore/guide/guideDetail');
        }

        // Licensed guides page
        public function licensed() {
            $licensedGuides = $this->guideModel->getLicensedGuides();
            $data = [
                'guides' => $licensedGuides,
                'title' => 'Licensed Guides'
            ];
            $this->view('Explore/guide/licensed', $data);
        }

        // Reviewed guides page
        public function reviewed() {
            $reviewedGuides = $this->guideModel->getReviewedGuides();
            $data = [
                'guides' => $reviewedGuides,
                'title' => 'Reviewed Guides'
            ];
            $this->view('Explore/guide/reviewed', $data);
        }

        // Tourist guides page
        public function tourist() {
            $touristGuides = $this->guideModel->getTouristGuides();
            $data = [
                'guides' => $touristGuides,
                'title' => 'Tourist Guides'
            ];
            $this->view('Explore/guide/tourist', $data);
        }
    }
?>
