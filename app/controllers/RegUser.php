<?php

require_once '../app/helpers/session_helper.php';
require_once '../app/helpers/travel_spot_helper.php';

    class RegUser extends Controller {

        private $regUserModel;
        public function __construct(){
            $this->regUserModel = $this->model('RegUserModel');
        }

        public function destinations() {

            requireLogin();
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
            $this->view('Explore/driver/allDrivers', $data);
            $fullcontent = ob_get_clean();

            $html = $fullcontent;
            $css = URL_ROOT.'/public/css/regUser/explore/drivers/allDrivers.css';
            $js = URL_ROOT.'/public/js/regUser/explore/drivers/allDrivers.js';

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
            $this->view('Explore/guide/allGuides', $data);
            $fullcontent = ob_get_clean();

            $html = $fullcontent;
            $css = URL_ROOT.'/public/css/regUser/explore/guides/allGuides.css';
            $js = URL_ROOT.'/public/js/regUser/explore/guides/allGuides.js';

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

        public function addEvent(){

            header('Content-Type: application/json');
            if( $_SERVER['REQUEST_METHOD'] == "POST" ) {

                $input = json_decode(file_get_contents('php://input'),true);
                $userId = getSession('user_id');

                if(!$input){
                    http_response_code(400);
                    echo json_encode(['success'=>false, 'message'=>'Invalid JSON data']);
                    return;
                }

                $primary_required_fields = ['eventDate','startTime','endTime','eventType','eventStatus'];
                
                foreach($primary_required_fields as $field) {
                    if(empty($input[$field])){
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);  
                        return; 
                    }    
                }

                if($input['eventType'] === 'location'){
                    $location_required_fields = ['locationName','latitude','longitude','description'];
                    foreach($location_required_fields as $field) {
                        if(empty($input[$field])){
                            http_response_code(400);
                            echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required for location visit events']);  
                            return; 
                        }    
                    }
                } elseif($input['eventType'] === 'travelSpot'){
                    if(empty($input['travelSpotId'])){
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'Spot ID is required for travel spot visit events']);  
                        return; 
                    }
                }

                $input['userId'] = $userId;
                $insertingData = $input;

                try{
                    if($this->regUserModel->addEventData($insertingData)){
                        echo json_encode(['success' => true, 'message' => 'Added event data to the database successfully']);
                    }
                    else{
                        echo json_encode(['success' => false, 'message' => 'Failed to add the event data to the database.']);
                    }

                } catch(PDOException $e) {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Database error occurred when inserting event data'.$e->getMessage()]);
                }
            }
        }

        public function deleteEvent(){

            header('Content-Type: application/json');

            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                echo json_encode(['success' => false, 'message' => 'Invalid method']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $userId = getSession('user_id');
            
            if (!$userId || empty($input['tripId']) || empty($input['eventId'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid request']);
                return;
            }

            try{
                if ($this->regUserModel->deleteEvent($userId, $input['tripId'], $input['eventId'])) {
                    echo json_encode(['success' => true, 'message' => 'Event card deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete event card']);
                }
            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when deleting event card'.$e->getMessage()]);
            }
        }

        public function editEvent($eventId){
            error_log("editEvent called for eventId: " . $eventId);
            header('Content-Type: application/json');

            if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
                echo json_encode(['success' => false, 'message' => 'Invalid method']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $userId = getSession('user_id');
            
            if (!$userId || empty($input['tripId']) || empty($eventId)) {
                echo json_encode(['success' => false, 'message' => 'Invalid request']);
                return;
            }

            $updatingData = $input;
            $updatingData['eventId'] = $eventId;
            $updatingData['userId'] = $userId;

            try{
                if ($this->regUserModel->updateEvent($updatingData)) {
                    echo json_encode(['success' => true, 'message' => 'Event card updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update event card']);
                }
            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when updating event card'.$e->getMessage()]);
            }
        }

        public function getEventCardsByDate($tripId, $eventDate){

            header('Content-Type: application/json');

            $date = new DateTime($eventDate);
            $eventDate = $date->format('Y-m-d');

            $userId = getSession('user_id');

            try{
                $eventCards = $this->regUserModel->getEventCardsByDate($userId, $tripId, $eventDate);
            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when retrieving event cards(getEventCardsByDate)'.$e->getMessage()]);
                return;

            }

            error_log("event cards for the date ". $eventDate . " are ". print_r($eventCards,true));

            echo json_encode([
                'success' => true,
                'eventCards' => $eventCards
            ]);

        }

        public function getLastAddedEvent($tripId, $eventDate){

            header('Content-Type: application/json');

            $date = new DateTime($eventDate);
            $eventDate = $date->format('Y-m-d');

            $userId = getSession('user_id');
            error_log("getting last added event for userId: ". $userId. " tripId: ". $tripId. " eventDate: ". $eventDate);

            try{
                $eventCard = $this->regUserModel->getLastAddedEvent($userId, $tripId, $eventDate);
                error_log("last added event card: ". print_r($eventCard,true));
            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when retrieving last added event(getLastAddedEvent)'.$e->getMessage()]);
                return;

            }

            if($eventCard){
                echo json_encode([
                    'success' => true,
                    'message' => "Last added event Got Successfully",
                    'eventCard' => $eventCard
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error getting Last added event card NO event card recieved',
                    'eventCard' => null
                ]);
            }

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

        public function retrieveEventData($tripId, $eventId){
            header('Content-Type: application/json');

            $userId = getSession('user_id');

            try{
                $eventData = $this->regUserModel->getEventData($userId, $tripId, $eventId);
            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'eventData' => null, 'message' => 'Database error occurred when retrieving event data(retrieveEventData)'.$e->getMessage()]);
                return;

            }

            if($eventData){
                echo json_encode([ 'success' => true,
                                    'eventData' => $eventData, 
                                    'message'=>'Event data recieved successfully..' 
                                ]);
            } else {
                echo json_encode([ 'success' => false,'eventData' => null, 'message'=>'No event found for the given ID']);
            }
        }

        public function retrieveAboveEventEndTime($tripId, $selectedEventId, $eventDate){
            header('Content-Type: application/json');
            $userId = getSession('user_id');
            try{
                $eventData = $this->regUserModel->getAboveEventEndTime($userId, $tripId, $selectedEventId, $eventDate);

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'eventData' => null, 'message' => 'Database error occurred when retrieving above event data(retrieveAboveEventData)'.$e->getMessage()]);
                return;
            }

            if($eventData){
                echo json_encode([ 'success' => true,
                                    'eventData' => $eventData, 
                                    'message'=>'Above event data recieved successfully..' 
                                ]);
            } else {
                echo json_encode([ 'success' => false,'eventData' => null, 'message'=>'No above event found for the given ID']);
            }
        }

        public function retrieveBelowEventStartTime($tripId, $selectedEventId, $eventDate){

            header('Content-Type: application/json');
            $userId = getSession('user_id');
            try{
                $eventData = $this->regUserModel->getBelowEventStartTime($userId, $tripId, $selectedEventId, $eventDate);

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'eventData' => null, 'message' => 'Database error occurred when retrieving below event data(retrieveBelowEventData)'.$e->getMessage()]);
                return;
            }

            if($eventData){
                echo json_encode([ 'success' => true,
                                    'eventData' => $eventData, 
                                    'message'=>'Below event data recieved successfully..' 
                                ]);
            } else {
                echo json_encode([ 'success' => false,'eventData' => null, 'message'=>'No below event found for the given ID']);
            }
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





