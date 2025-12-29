<?php

require_once '../app/helpers/session_helper.php';
require_once '../app/helpers/travel_spot_helper.php';

    class RegUser extends Controller {

        private $regUserModel;
        public function __construct(){
            $this->regUserModel = $this->model('RegUserModel');
        }

        public function destinations() {

            $structCardData = [];

            $moderatorModel = $this->model('ModeratorModel');
            $travelSpotCardData = $moderatorModel->loadTravelSpotCardData();

            error_log(print_r(json_encode($travelSpotCardData),true));

            //structure the data that can use fro render the cards.
            foreach($travelSpotCardData as $item){

                $mainFilterId = $item -> mainFilterId;
                $spotId = $item -> spotId;
                
                if(!isset($structCardData[$mainFilterId])){
                    $structCardData[$mainFilterId] = [
                        "mainFilterName" => $item -> mainFilterName,
                        "travelSpots" => []
                    ];
                }
                
                if(!isset($structCardData[$mainFilterId]["travelSpots"][$spotId])){
                    $structCardData[$mainFilterId]["travelSpots"][$spotId] = [
                        "spotName"      => $item -> spotName,
                        "overview"      => $item -> overview,
                        "totalReviews"  => $item -> totalReviews,
                        "averageRating" => $item -> averageRating,
                        "subFilters"    => [],
                        "photoPaths"    => []
                    ];
                }
                
                $currentSpot = &$structCardData[$mainFilterId]["travelSpots"][$spotId];
                
                if(!isset($currentSpot["subFilters"][$item -> subFilterId])){
                    $currentSpot["subFilters"][$item -> subFilterId] = $item -> subFilterName ;
                }
                
                if(!in_array($item -> photoPath, $currentSpot["photoPaths"])){
                    $currentSpot["photoPaths"][] = $item -> photoPath;
                }
            }

            error_log("structured card data". print_r($structCardData,true));

            $cardData = [
                            "cardData" => $structCardData
            ];

            ob_start();
            $this->view('Explore/travelSpots/travelSpotsView',$cardData);
            $html = ob_get_clean();

            $css = URL_ROOT.'/public/css/regUser/travelSpots/travelSpotsView.css';
            $js = URL_ROOT.'/public/js/regUser/travelSpots/travelSpotsView.js';

            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];

            $unEncodedResponse = [
                'tabId'=>'destinations',
                'loadingContent'=>$loadingContent
            ];

            $this->view('UserTemplates/travellerDash', $unEncodedResponse);
        }

        public function drivers() {
            
            $driverModel = $this->model('ExploreDrivers');
            $trendingDrivers = $driverModel->getTrendingDrivers(4);
            $licensedDrivers = $driverModel->getLicensedDrivers(4);
            $reviewedDrivers = $driverModel->getReviewedDrivers(4);
            $touristDrivers = $driverModel->getTouristDrivers(4);
            
            $data = [
                'trendingDrivers' => $trendingDrivers,
                'licensedDrivers' => $licensedDrivers,
                'reviewedDrivers' => $reviewedDrivers,
                'touristDrivers' => $touristDrivers
            ];
            
            ob_start();
            $this->view('Explore/driver/index1', $data);
            $fullcontent = ob_get_clean();

            $html = extractSection($fullcontent,'HTML');
            $css = extractSection($fullcontent,'CSS');
            $js = extractSection($fullcontent,'JS');

            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];

            $unEncodedResponse = [
                'tabId'=>'drivers',
                'loadingContent'=>$loadingContent
            ];

            $this->view('UserTemplates/travellerDash', $unEncodedResponse);
        }

        public function guides() {
            $guideModel = $this->model('ExploreGuides');
            $trendingGuides = $guideModel->getTrendingGuides(5);
            $licensedGuides = $guideModel->getLicensedGuides(4);
            $reviewedGuides = $guideModel->getReviewedGuides(4);
            $touristGuides = $guideModel->getTouristGuides(4);
            
            $data = [
                'trendingGuides' => $trendingGuides,
                'licensedGuides' => $licensedGuides,
                'reviewedGuides' => $reviewedGuides,
                'touristGuides' => $touristGuides
            ];
            
            ob_start();
            $this->view('Explore/guide/index1', $data);
            $fullcontent = ob_get_clean();

            $html = extractSection($fullcontent,'HTML');
            $css = extractSection($fullcontent,'CSS');
            $js = extractSection($fullcontent,'JS');

            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];

            $unEncodedResponse = [
                'tabId'=>'guides',
                'loadingContent'=>$loadingContent
            ];

            $this->view('UserTemplates/travellerDash', $unEncodedResponse);
        }

        public function trips(){ 

            ob_start();
            $this->view("Trips/userTrip");
            $html = ob_get_clean();

            $css = URL_ROOT.'/public/css/regUser/trips/userTrip.css';
            $js  = URL_ROOT.'/public/js/regUser/trips/userTrip.js';

            $loadingContent = [
                'html' => $html,
                'css'  => $css,
                'js'   => $js
            ];

           $unEncodedResponse = [
                'tabId'=>'trips',
                'loadingContent'=>$loadingContent
            ];

            $this->view('UserTemplates/travellerDash', $unEncodedResponse);
        }

        public function retrieveSelectedSpot($spotId){
            error_log("working ". $spotId);
            $spotData = travelSpotDetails($spotId);
            if($spotData){
                echo json_encode([ 'success' => true,
                                    'spotData' => $spotData, 
                                    'message'=>'data recieved successfully..' 
                                ]);
            } else {
                echo json_encode([ 'success' => false,'spotData' => null, 'message'=>'no searching spot']);
            }

        }

        public function tripEventList($tripId){

            $basicTripDetails = $this->regUserModel->getTripById($tripId);
            ob_start();
            $data = [
                'basicTripDetails' => $basicTripDetails,
                ];
            
            $this->view("Trips/tripEventList",$data);
            $html = ob_get_clean();

            $css = URL_ROOT.'/public/css/regUser/trips/tripEventList.css';
            $js  = URL_ROOT.'/public/js/regUser/trips/tripEventList.js';

            $loadingContent = [
                'html' => $html,
                'css'  => $css,
                'js'   => $js
            ];

           $unEncodedResponse = [
                'tabId'=>'trips',
                'loadingContent'=>$loadingContent
            ];

            $this->view('UserTemplates/travellerDash', $unEncodedResponse);
        }

        public function selectTravelSpot(){

            $structCardData = [];

            $moderatorModel = $this->model('ModeratorModel');
            $travelSpotCardData = $moderatorModel->loadTravelSpotCardData();

            error_log(print_r(json_encode($travelSpotCardData),true));

            //structure the data that can use fro render the cards.
            foreach($travelSpotCardData as $item){

                $mainFilterId = $item -> mainFilterId;
                $spotId = $item -> spotId;
                
                if(!isset($structCardData[$mainFilterId])){
                    $structCardData[$mainFilterId] = [
                        "mainFilterName" => $item -> mainFilterName,
                        "travelSpots" => []
                    ];
                }
                
                if(!isset($structCardData[$mainFilterId]["travelSpots"][$spotId])){
                    $structCardData[$mainFilterId]["travelSpots"][$spotId] = [
                        "spotName"      => $item -> spotName,
                        "overview"      => $item -> overview,
                        "totalReviews"  => $item -> totalReviews,
                        "averageRating" => $item -> averageRating,
                        "subFilters"    => [],
                        "photoPaths"    => []
                    ];
                }
                
                $currentSpot = &$structCardData[$mainFilterId]["travelSpots"][$spotId];
                
                if(!isset($currentSpot["subFilters"][$item -> subFilterId])){
                    $currentSpot["subFilters"][$item -> subFilterId] = $item -> subFilterName ;
                }
                
                if(!in_array($item -> photoPath, $currentSpot["photoPaths"])){
                    $currentSpot["photoPaths"][] = $item -> photoPath;
                }
            }

            error_log("structured card data". print_r($structCardData,true));

            $cardData = [
                            "cardData" => $structCardData
            ];

            ob_start();
            $this->view('Trips/addOns/travelSpots/travelSpotsSelect',$cardData);
            $html = ob_get_clean();

            $css = URL_ROOT.'/public/css/regUser/trips/addOns/travelSpots/travelSpotsSelect.css';
            $js = URL_ROOT.'/public/js/regUser/trips/addOns/travelSpots/travelSpotsSelect.js';
            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];

            $unEncodedResponse = [
                'tabId'=>'destinations',
                'loadingContent'=>$loadingContent
            ];

            $this->view('UserTemplates/travellerDash', $unEncodedResponse);
        }

        public function home() {
            // Default dashboard content
            $loadingContent = [
                'html' => '<div class="welcome-message"><h2>Welcome to Admin Dashboard</h2><p>Select a tab from the sidebar to manage different sections.</p></div>',
                'css' => '.welcome-message { text-align: center; padding: 40px; } .welcome-message h2 { color: var(--primary); margin-bottom: 15px; }',
                'js' => 'console.log("Dashboard home loaded");'
            ];

           $unEncodedResponse = [
                'tabId'=>'home',
                'loadingContent'=>$loadingContent
            ];

            $this->view('UserTemplates/travellerDash', $unEncodedResponse);
        }

        public function packages() {
            // Placeholder for packages management
            $loadingContent = [
                'html' => '<div class="packages-section"><h2>Packages Management</h2><p>Package management functionality coming soon...</p></div>',
                'css' => '.packages-section { padding: 20px; }',
                'js' => 'console.log("Packages section loaded");'
            ];

            $unEncodedResponse = [
                'tabId'=>'packages',
                'loadingContent'=>$loadingContent
            ];

            $this->view('UserTemplates/travellerDash', $unEncodedResponse);
        }

        public function getUserTrips() {
            header('Content-Type: application/json');
            
            $userId = getSession('user_id');
            if (!$userId) {
                echo json_encode(['success' => false, 'message' => 'Please log in']);
                return;
            }

            $trips = $this->regUserModel->getUserTrips($userId);

            echo json_encode([
                'success' => true,
                'trips' => $trips
            ]);
        }

        public function createTrip(){

            // Set content type header
            header('Content-Type: application/json');

            if($_SERVER['REQUEST_METHOD'] == 'POST'){

                $input = json_decode(file_get_contents('php://input'),true);
                $userId = getSession('user_id');

                if(!$input){
                    http_response_code(400);
                    echo json_encode(['success'=>false, 'message'=>'Invalid JSON data']);
                    return;
                }

                $required_fields = ['tripTitle','description','startDate', 'endDate'];
                foreach($required_fields as $field) {
                    if(empty($input[$field])){
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);  
                        return; 
                    }    
                }

                $startDate = new DateTime($input['startDate']);
                $endDate = new DateTime($input['endDate']);
                $today = new DateTime();

                if( $startDate<$today){
                    http_response_code(400);
                    echo json_encode(['success'=>false,'message'=>'Start date cannot be in the past']);
                    return;
                }
                
                if ($endDate < $startDate) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'End date must be after start date']);
                    return;
                }

                //preparing inserting data
                $insertingData = [

                    'userId' => $userId,
                    'tripTitle' => $input['tripTitle'],
                    'description' => $input['description'],
                    'startDate' => $startDate->format('Y-m-d'),  // <--to the insertion to the database
                    'endDate' => $endDate->format('Y-m-d'),
                    'status' => 'pending'
            
                ];
                
                try{
                    if($this->regUserModel->addCreatedTrip($insertingData)){
                        echo json_encode(['success' => true, 'message' => 'Added to the database successfully']);
                    }
                    else{
                        echo json_encode(['success' => false, 'message' => 'Failed to add to the database.']);
                    }

                } catch(PDOException $e) {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
                }
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            }
        }

        public function updateTrip() {

            header('Content-Type: application/json');
            
            if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
                echo json_encode(['success' => false, 'message' => 'Invalid method']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $userId = getSession('user_id');
            
            if (!$userId || empty($input['tripId'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid request']);
                return;
            }

            // Validate ownership
            /*
            $existingTrip = $this->regUserModel->getTripById($input['trip_id'], $userId);
            
            if (!$existingTrip) {
                echo json_encode(['success' => false, 'message' => 'Trip not found']);
                return;
            }
                */

            // Update trip
            $updateData = [
                'tripId' => $input['tripId'],
                'tripTitle' => $input['tripTitle'],
                'description' => $input['description'],
                'startDate' => $input['startDate'],
                'endDate' => $input['endDate']
            ];

            if ($this->regUserModel->updateCreatedTrip($updateData)) {
                echo json_encode(['success' => true, 'message' => 'Trip updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update trip']);
            }
        }

        public function deleteTrip() {

            header('Content-Type: application/json');
            
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                echo json_encode(['success' => false, 'message' => 'Invalid method']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $userId = getSession('user_id');
            
            if (!$userId || empty($input['tripId'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid request']);
                return;
            }

            // Validate ownership
            /*
            $existingTrip = $this->getTripById($input['tripId'], $userId);
            
            if (!$existingTrip) {
                echo json_encode(['success' => false, 'message' => 'Trip not found']);
                return;
            }
                */

            if ($this->regUserModel->deleteCreatedTrip($input['tripId'])) {
                echo json_encode(['success' => true, 'message' => 'Trip deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete trip']);
            }
        }
        
    }





