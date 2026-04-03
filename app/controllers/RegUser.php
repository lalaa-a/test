<?php

require_once '../app/helpers/session_helper.php';
require_once '../app/helpers/travel_spot_helper.php';
require_once '../app/helpers/currency_helper.php';

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

        public function driverVisibleProfile($driverId) {
            
            ob_start();
            $this->view('Driver/profile/driverVisibleProfile');
            $fullcontent = ob_get_clean();

            $html = $fullcontent;
            $css = URL_ROOT.'/public/css/driver/profile/driverVisibleProfile.css';
            $js = URL_ROOT.'/public/js/driver/profile/driverVisibleProfile.js';

            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js,
                'data' => ['driverId' => $driverId]
            ];

            $unEncodedResponse = [
                'tabId'=>'profile',
                'loadingContent'=>$loadingContent
            ];

            $this->view('UserTemplates/travellerDash', $unEncodedResponse);
        }

        public function drivers() {
            
            $profileControllerModel = $this->model('ProfileControllerModel');
            $driversWithMainFilters = $profileControllerModel->getAccountsByMainFilters('driver');
            
            $data = [
                'mainFilters' => $driversWithMainFilters
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

            $profileControllerModel = $this->model('ProfileControllerModel');
            $driversWithMainFilters = $profileControllerModel->getAccountsByMainFilters('guide');
            
            $data = [
                'mainFilters' => $driversWithMainFilters
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

                $primary_required_fields = ['eventDate','eventType','eventStatus'];
                
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
                    $eventId = $this->regUserModel->addEventData($insertingData);
                    if($eventId){
                        echo json_encode(['success' => true, 'message' => 'Added event data to the database successfully', 'eventId' => $eventId]);
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

        public function saveGuideRequest() {
            header('Content-Type: application/json');
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Invalid method']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            error_log("saveGuideRequest received data: " . print_r($input, true));
            
            $userId = getSession('user_id');
            
            if (!$userId || !$input) {
                error_log("saveGuideRequest validation failed - userId: $userId, input: " . ($input ? 'yes' : 'no'));
                echo json_encode(['success' => false, 'message' => 'Invalid request']);
                return;
            }

            // Add userId to the data
            $input['userId'] = $userId;

            try {
                if ($this->regUserModel->saveGuideRequest($input)) {
                    error_log("saveGuideRequest success");
                    echo json_encode(['success' => true, 'message' => 'Guide request saved successfully']);
                } else {
                    error_log("saveGuideRequest failed - model returned false");
                    echo json_encode(['success' => false, 'message' => 'Failed to save guide request']);
                }
            } catch (PDOException $e) {
                error_log("saveGuideRequest error: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
        }

        public function getGuideRequestByEventId($eventId) {
            header('Content-Type: application/json');
            
            try {
                $guideRequest = $this->regUserModel->getGuideRequestByEventId($eventId);
                
                if ($guideRequest) {
                    echo json_encode(['success' => true, 'guideRequest' => $guideRequest]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No guide request found']);
                }
            } catch (PDOException $e) {
                error_log("getGuideRequestByEventId error: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
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
                    echo json_encode(['success' => true, 'message' => 'Event card updated successfully', 'eventId' => $eventId]);
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

        public function getEventCoordinates($tripId, $eventDate){

            header('Content-Type: application/json');

            $date = new DateTime($eventDate);
            $eventDate = $date->format('Y-m-d');

            $userId = getSession('user_id');

            try{
                $coordinates = $this->regUserModel->getEventCoordinates($userId, $tripId, $eventDate);
                
                error_log("Event coordinates for date " . $eventDate . ": " . print_r($coordinates, true));

                echo json_encode([
                    'success' => true,
                    'coordinates' => $coordinates
                ]);

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when retrieving event coordinates: '.$e->getMessage()]);
                return;
            }
        }

        public function getAllTripCoordinates($tripId){

            header('Content-Type: application/json');

            $userId = getSession('user_id');

            try{
                $coordinates = $this->regUserModel->getAllTripCoordinates($userId, $tripId);
                
                error_log("All trip coordinates: " . print_r($coordinates, true));

                echo json_encode([
                    'success' => true,
                    'coordinates' => $coordinates
                ]);

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when retrieving all trip coordinates: '.$e->getMessage()]);
                return;
            }
        }

        public function getAllTripEvents($tripId){

            header('Content-Type: application/json');

            $userId = getSession('user_id');

            try{
                $events = $this->regUserModel->getAllTripEvents($userId, $tripId);
                
                echo json_encode([
                    'success' => true,
                    'events' => $events
                ]);

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when retrieving all trip events: '.$e->getMessage()]);
                return;
            }
        }

        public function getDriverRequests($tripId) {
            header('Content-Type: application/json');
            
            $tripId = (int) $tripId;
            $drivers = $this->regUserModel->getDriverRequestsForTrip($tripId);
            
            // Transform the driver data to match frontend format with segment index
            $driversWithSegments = [];
            foreach ($drivers as $index => $driver) {
                $driversWithSegments[(string)$index] = [
                    'userId' => (int) $driver->driverId,
                    'fullName' => $driver->driverName,
                    'profilePhoto' => $driver->driverProfilePhoto,
                    'averageRating' => (float) $driver->driverRating,
                    'verified' => (bool) $driver->verifyStatus,
                    'vehicleId' => (int) $driver->vehicleId,
                    'make' => $driver->vehicleType,
                    'model' => $driver->vehicleModel,
                    'year' => (int) $driver->vehicleYear,
                    'vehicleType' => $driver->vehicleType,
                    'vehiclePhoto' => $driver->vehiclePhoto,
                    'seatingCapacity' => (int) $driver->vehicleCapacity,
                    'childSeats' => (int) $driver->childSeats,
                    'totalChargePerDay' => (float) $driver->totalAmount,
                    'currencySymbol' => '$',
                    'currency' => 'USD',
                    'requestStatus' => $driver->requestStatus
                ];
            }
            
            echo json_encode([
                'success' => true,
                'drivers' => $driversWithSegments
            ]);
        }

        public function confirmTrip($tripId){

            header('Content-Type: application/json');

            $userId = getSession('user_id');

            try{
                $input = json_decode(file_get_contents('php://input'), true);
                $selectedDrivers = $input['selectedDrivers'] ?? [];

                $result = $this->regUserModel->confirmTrip($userId, $tripId);
                
                if($result){
                    // Save driver requests if any drivers selected
                    if (!empty($selectedDrivers)) {
                        $requestsResult = $this->regUserModel->saveDriverRequests($userId, $tripId, $selectedDrivers);
                        if (!$requestsResult) {
                            echo json_encode([
                                'success' => false,
                                'message' => 'Trip confirmed but failed to save driver requests'
                            ]);
                            return;
                        }
                    }

                    echo json_encode([
                        'success' => true,
                        'message' => 'Trip confirmed successfully'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to confirm trip'
                    ]);
                }

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when confirming trip: '.$e->getMessage()]);
                return;
            }
        }

        public function getTripStartEndEvents($tripId){
            header('Content-Type: application/json');
            
            try {
                $userId = getSession('user_id');
                $events = $this->regUserModel->getTripStartEndEvents($userId, $tripId);
                
                echo json_encode([
                    'success' => true,
                    'events' => $events
                ]);
            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error: '.$e->getMessage()]);
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

        public function guidesSelect($spotId){
            error_log("guidesSelect called with spotId: $spotId");
            
            $regUserSelectionModel = $this->model('RegUserSelectionModel');
            $guidesWithFilters = $regUserSelectionModel->getGuideToASpotId($spotId);
            
            // Get user currency from session
            $userCurrency = getUserCurrency();
            $currencySymbol = getCurrencySymbol($userCurrency);
            
            $data = [
                'mainFilters' => $guidesWithFilters,
                'spotId' => $spotId,
                'userCurrency' => $userCurrency,
                'currencySymbol' => $currencySymbol
            ];

            ob_start();
            $this->view('Trips/addOns/guides/guidesSelect', $data);
            $html = ob_get_clean();

            $css = URL_ROOT.'/public/css/regUser/trips/addOns/guides/guidesSelect.css';
            $js = URL_ROOT.'/public/js/regUser/trips/addOns/guides/guidesSelect.js';
            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];

            $unEncodedResponse = [
                'tabId'=>'guidesSelect',
                'loadingContent'=>$loadingContent
            ];

            $this->view('UserTemplates/travellerDash', $unEncodedResponse);
        }

        public function getGuidesData($spotId) {
            header('Content-Type: application/json');
            
            try {
                error_log("getGuidesData called with spotId: $spotId");
                
                $regUserSelectionModel = $this->model('RegUserSelectionModel');
                $guidesWithFilters = $regUserSelectionModel->getGuideToASpotId($spotId);
                
                // Return just the guides data as JSON
                $guidesData = $guidesWithFilters['all']['accounts'] ?? [];
                
                // Get user currency
                $userCurrency = getUserCurrency();
                $currencySymbol = getCurrencySymbol($userCurrency);
                
                // Convert prices for each guide
                foreach ($guidesData as $guide) {
                    $converted = convertCharge($guide->baseCharge, $userCurrency);
                    $guide->convertedCharge = $converted['amount'];
                    $guide->currency = $userCurrency;
                    $guide->currencySymbol = $currencySymbol;
                    $guide->formattedCharge = $converted['formatted'];
                }
                
                error_log("Found " . count($guidesData) . " guides for API response");
                
                echo json_encode([
                    'success' => true,
                    'guides' => array_values($guidesData),
                    'count' => count($guidesData),
                    'currency' => $userCurrency,
                    'currencySymbol' => $currencySymbol
                ]);
                
            } catch (Exception $e) {
                error_log("getGuidesData error: " . $e->getMessage());
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to fetch guides data: ' . $e->getMessage()
                ]);
            }
        }

        public function filterGuides($spotId) {
            header('Content-Type: application/json');
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !is_array($input)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid request data']);
                return;
            }

            try {
                $regUserSelectionModel = $this->model('RegUserSelectionModel');
                $filteredGuides = $regUserSelectionModel->filterGuidesBySpot($spotId, $input);
                
                echo json_encode([
                    'success' => true, 
                    'guides' => $filteredGuides,
                    'count' => count($filteredGuides),
                    'message' => 'Guides filtered successfully'
                ]);
                
            } catch (Exception $e) {
                error_log("Error filtering guides: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Internal server error']);
            }
        }

        // ============================
        // DRIVER SELECTION METHODS
        // ============================

        public function driversSelect($tripId){
            error_log("driversSelect called with tripId: $tripId");
            
            $regUserSelectionModel = $this->model('RegUserSelectionModel');
            $driversWithFilters = $regUserSelectionModel->getDriversForTrip($tripId);
            
            // Get user currency from session
            $userCurrency = getUserCurrency();
            $currencySymbol = getCurrencySymbol($userCurrency);
            
            $data = [
                'mainFilters' => $driversWithFilters,
                'tripId' => $tripId,
                'userCurrency' => $userCurrency,
                'currencySymbol' => $currencySymbol
            ];

            ob_start();
            $this->view('Trips/addOns/drivers/driversSelect', $data);
            $html = ob_get_clean();

            $css = URL_ROOT.'/public/css/regUser/trips/addOns/drivers/driverSelect.css';
            $js = URL_ROOT.'/public/js/regUser/trips/addOns/drivers/driverSelect.js';
            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];

            $unEncodedResponse = [
                'tabId'=>'driversSelect',
                'loadingContent'=>$loadingContent
            ];

            $this->view('UserTemplates/travellerDash', $unEncodedResponse);
        }

        public function getDriversData($tripId) {
            header('Content-Type: application/json');
            
            try {
                error_log("getDriversData called with tripId: $tripId");
                
                $regUserSelectionModel = $this->model('RegUserSelectionModel');
                $driversWithFilters = $regUserSelectionModel->getDriversForTrip($tripId);
                
                // Return just the drivers data as JSON
                $driversData = $driversWithFilters['all']['accounts'] ?? [];
                
                // Get user currency
                $userCurrency = getUserCurrency();
                $currencySymbol = getCurrencySymbol($userCurrency);
                
                // Convert prices for each driver
                foreach ($driversData as $driver) {
                    $converted = convertCharge($driver->totalChargePerDay, $userCurrency);
                    $driver->convertedChargePerDay = $converted['amount'];
                    $driver->currency = $userCurrency;
                    $driver->currencySymbol = $currencySymbol;
                    $driver->formattedChargePerDay = $converted['formatted'];
                    
                    $convertedKm = convertCharge($driver->totalChargePerKm, $userCurrency);
                    $driver->convertedChargePerKm = $convertedKm['amount'];
                    $driver->formattedChargePerKm = $convertedKm['formatted'];
                }
                
                error_log("Found " . count($driversData) . " drivers for API response");
                
                echo json_encode([
                    'success' => true,
                    'drivers' => array_values($driversData),
                    'count' => count($driversData),
                    'currency' => $userCurrency,
                    'currencySymbol' => $currencySymbol
                ]);
                
            } catch (Exception $e) {
                error_log("getDriversData error: " . $e->getMessage());
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to fetch drivers data: ' . $e->getMessage()
                ]);
            }
        }

        public function filterDrivers($tripId) {
            header('Content-Type: application/json');
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !is_array($input)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid request data']);
                return;
            }

            try {
                $regUserSelectionModel = $this->model('RegUserSelectionModel');
                $filteredDrivers = $regUserSelectionModel->filterDriversByTrip($tripId, $input);
                
                // Get user currency
                $userCurrency = getUserCurrency();
                $currencySymbol = getCurrencySymbol($userCurrency);
                
                // Convert prices for each driver
                foreach ($filteredDrivers as $driver) {
                    $converted = convertCharge($driver->totalChargePerDay, $userCurrency);
                    $driver->convertedChargePerDay = $converted['amount'];
                    $driver->currency = $userCurrency;
                    $driver->currencySymbol = $currencySymbol;
                    $driver->formattedChargePerDay = $converted['formatted'];
                    
                    $convertedKm = convertCharge($driver->totalChargePerKm, $userCurrency);
                    $driver->convertedChargePerKm = $convertedKm['amount'];
                    $driver->formattedChargePerKm = $convertedKm['formatted'];
                }
                
                echo json_encode([
                    'success' => true, 
                    'drivers' => $filteredDrivers,
                    'count' => count($filteredDrivers),
                    'message' => 'Drivers filtered successfully'
                ]);
                
            } catch (Exception $e) {
                error_log("Error filtering drivers: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Internal server error']);
            }
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

                $required_fields = ['tripTitle','description','numberOfPeople','startDate', 'endDate'];
                foreach($required_fields as $field) {
                    if(empty($input[$field])){
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);  
                        return; 
                    }    
                }

                // Validate numberOfPeople
                if (!is_numeric($input['numberOfPeople']) || $input['numberOfPeople'] < 1) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Number of people must be at least 1']);
                    return;
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
                    'numberOfPeople' => (int)$input['numberOfPeople'],
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
                'numberOfPeople' => (int)$input['numberOfPeople'],
                'startDate' => $input['startDate'],
                'endDate' => $input['endDate']
            ];

            if ($this->regUserModel->updateCreatedTrip($updateData)) {
                echo json_encode(['success' => true, 'message' => 'Trip updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update trip']);
            }
        }

        public function getTripDetails($tripId) {
            header('Content-Type: application/json');
            
            try {
                $userId = getSession('user_id');
                
                if (!$userId) {
                    echo json_encode(['success' => false, 'message' => 'User not logged in']);
                    return;
                }
                
                // Get trip details
                $trip = $this->regUserModel->getTripById($tripId);
                
                if (!$trip) {
                    echo json_encode(['success' => false, 'message' => 'Trip not found']);
                    return;
                }
                
                // Verify ownership
                if ($trip->userId != $userId) {
                    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                    return;
                }
                
                echo json_encode([
                    'success' => true,
                    'trip' => [
                        'tripId' => $trip->tripId,
                        'userId' => $trip->userId,
                        'tripTitle' => $trip->tripTitle,
                        'description' => $trip->description,
                        'numberOfPeople' => (int)$trip->numberOfPeople,
                        'startDate' => $trip->startDate,
                        'endDate' => $trip->endDate,
                        'status' => $trip->status
                    ]
                ]);
                
            } catch (Exception $e) {
                error_log("getTripDetails error: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Error fetching trip details']);
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

        public function getDriverCoverPhotos($driverId) {
            header('Content-Type: application/json');

            if (!$driverId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Driver ID is required']);
                return;
            }

            try {
                $driverModel = $this->model('DriverModel');
                $photos = $driverModel->getDriverCoverPhotos($driverId);
                echo json_encode(['success' => true, 'photos' => $photos]);
            } catch (Exception $e) {
                error_log('Error getting driver cover photos: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to load cover photos']);
            }
        }

        public function getDriverReviews($driverId) {
            header('Content-Type: application/json');

            if (!$driverId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Driver ID is required']);
                return;
            }

            try {
                $profileControllerModel = $this->model('ProfileControllerModel');
                $reviews = $profileControllerModel->getDriverReviews($driverId);
                echo json_encode(['success' => true, 'reviews' => $reviews]);
            } catch (Exception $e) {
                error_log('Error getting driver reviews: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to load reviews']);
            }
        }

        public function submitReview() {
            header('Content-Type: application/json');

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                return;
            }

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not authenticated']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $driverId = $input['driverId'] ?? null;
            $reviewText = trim($input['comment'] ?? '');

            if (!$driverId || empty($reviewText)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Driver ID and review text are required']);
                return;
            }

            try {
                $profileControllerModel = $this->model('ProfileControllerModel');
                $result = $profileControllerModel->submitReview($userId, $driverId, $reviewText);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Review submitted successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to submit review']);
                }
            } catch (Exception $e) {
                error_log('Error submitting review: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to submit review']);
            }
        }

        public function getDriverRatings($driverId) {
            header('Content-Type: application/json');

            if (!$driverId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Driver ID is required']);
                return;
            }

            try {
                $profileControllerModel = $this->model('ProfileControllerModel');
                $ratings = $profileControllerModel->getDriverRatings($driverId);
                echo json_encode(['success' => true, 'ratings' => $ratings]);
            } catch (Exception $e) {
                error_log('Error getting driver ratings: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to load ratings']);
            }
        }

        public function submitRating() {
            header('Content-Type: application/json');

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                return;
            }

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not authenticated']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $driverId = $input['driverId'] ?? null;
            $rating = $input['rating'] ?? null;

            if (!$driverId || !$rating || !is_numeric($rating) || $rating < 1 || $rating > 5) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Driver ID and valid rating (1-5) are required']);
                return;
            }

            try {
                $profileControllerModel = $this->model('ProfileControllerModel');
                $result = $profileControllerModel->submitRating($userId, $driverId, $rating);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Rating submitted successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to submit rating']);
                }
            } catch (Exception $e) {
                error_log('Error submitting rating: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to submit rating']);
            }
        }

    }





