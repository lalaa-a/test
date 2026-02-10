<?php

    class moderator extends Controller{

        private $moderatorModel;
        private $userModel;
        public function __construct(){
            $this->moderatorModel = $this->model("ModeratorModel");
            $this->userModel = $this->model("UserModel");
        }

        public function index(){
            $unEncodedResponse = [
                'tabId'=>'dashboard',
                'loadingContent'=>null
            ];
            $this->view('UserTemplates/moderatorDash',$unEncodedResponse);
        }

        public function support(){
            ob_start();
            $this->view('Moderator/support/support');
            $html = ob_get_clean();

            $loadingContent = [
                'html' => $html,
                'css'  => URL_ROOT.'/public/css/moderator/support/support.css',
                'js'   => URL_ROOT.'/public/js/moderator/support/support.js'
            ];

            $unEncodedResponse = [
                'tabId' => 'support',
                'loadingContent' => $loadingContent
            ];

            $this->view('UserTemplates/moderatorDash', $unEncodedResponse);
        }

        public function dashboard(){
            $this->view('UserTemplates/moderatorDash');
        }
        
        public function verification(){

            $this->view('UserTemplates/moderatorDash');
        }

        public function content(){

            ob_start();
            $this->view('Moderator/content/content');
            $html =  ob_get_clean();

            $loadingContent = [
                'html' => $html,
                'css'  => URL_ROOT.'/public/css/moderator/content/content.css',
                'js'   => URL_ROOT.'/public/js/moderator/content/content.js'
            ];

            $unEncodedResponse = [
                'tabId' => 'content',
                'loadingContent' => $loadingContent
            ];

            $this->view('UserTemplates/moderatorDash',$unEncodedResponse);
        }

        //  content->filters subtab
        public function subtabFilters(){

            ob_start();
            $this->view('Moderator/content/contentFilters');
            $html = ob_get_clean();

            $loadingContent = [
                'html' => $html,
                'css'  => URL_ROOT.'/public/css/moderator/content/subtabFilters.css',
                'js'   => URL_ROOT.'/public/js/moderator/content/subtabFilters.js'
            ];

            $unEncodedResponse = [
                'ok' => true,
                'loadingContent'=>$loadingContent
            ];

            echo json_encode($unEncodedResponse);
        }

        //  content->travelSpots subtab
        public function subtabTravelSpots(){

            ob_start();
            $this->view('Moderator/content/contentTravelSpots');
            $html = ob_get_clean();

            $loadingContent = [
                'html' => $html,
                'css'  => URL_ROOT.'/public/css/moderator/content/subtabTravelSpots.css',
                'js'   => URL_ROOT.'/public/js/moderator/content/subtabTravelSpots.js'
            ];

            $unEncodedResponse = [
                'ok' => true,
                'loadingContent'=>$loadingContent
            ];

            echo json_encode($unEncodedResponse);
            
        }

        public function mainFilterNameSubmit(){

            header('Content-Type: application/json');

            if( $_SERVER['REQUEST_METHOD'] == "POST" ) {

                $input = json_decode(file_get_contents('php://input'),true);
                $moderatorId = getSession('user_id');

                if(!$input){
                    http_response_code(400);
                    echo json_encode(['success'=>false, 'message'=>'Invalid JSON data']);
                    return;
                }

                $required_fields = ['mainFilterName'];
                foreach($required_fields as $field) {
                    if(empty($input[$field])){
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);  
                        return; 
                    }    
                }

                $insertingData = [
                    'moderatorId' => $moderatorId,
                    'mainFilterName' => $input['mainFilterName']
                ];

                try{
                    if($this->moderatorModel->addMainFilterName($insertingData)){
                        echo json_encode(['success' => true, 'message' => 'Added main filter name the database successfully']);
                    }
                    else{
                        echo json_encode(['success' => false, 'message' => 'Failed to add the main filter name to the database.']);
                    }

                } catch(PDOException $e) {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
                }
            }
        }

        public function mainFilterEdit(){

            header('Content-Type: application/json');
            if( $_SERVER['REQUEST_METHOD'] == "PUT" ) {

                $input = json_decode(file_get_contents('php://input'),true);
                $moderatorId = getSession('user_id');
            
                if (!$moderatorId || empty($input['mainFilterId']) || empty($input['mainFilterName']) ){
                    echo json_encode(['success' => false, 'message' => 'Invalid request']);
                    return;
                }

                // Validate ownership
                /*
                $existingTrip = $this->moderatorModel->getTripById($input['trip_id'], $userId);
                
                if (!$existingTrip) {
                    echo json_encode(['success' => false, 'message' => 'Trip not found']);
                    return;
                }
                    */
                // Update trip
                $updateData = [
                    'mainFilterId' => $input['mainFilterId'],
                    'mainFilterName' => $input['mainFilterName'],
                    'moderatorId' => $moderatorId
                ];

                if ($this->moderatorModel->updateMainFilterName($updateData)) {
                    echo json_encode(['success' => true, 'message' => 'Main Filter name updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Main Filter Failed to update']);
                }
            }
        }

        public function deleteMainFilter(){

            header('Content-Type: application/json');
            
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                echo json_encode(['success' => false, 'message' => 'Invalid method']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $userId = getSession('user_id');
            
            if (!$userId || empty($input['mainFilterId'])) {
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

            if ($this->moderatorModel->deleteMainFilterName($input['mainFilterId'])) {
                echo json_encode(['success' => true, 'message' => 'Main Filter deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete Main Filter']);
            }

        }

        public function subFilterNameSubmit(){

            header('Content-Type: application/json');

            if( $_SERVER['REQUEST_METHOD'] == "POST" ) {

                $input = json_decode(file_get_contents('php://input'),true);
                $moderatorId = getSession('user_id');

                if(!$input){
                    http_response_code(400);
                    echo json_encode(['success'=>false, 'message'=>'Invalid JSON data in subFilterNameSubmit']);
                    return;
                }

                $required_fields = ['mainFilterId','subFilterName'];
                foreach($required_fields as $field) {
                    if(empty($input[$field])){
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required in subFilterNameSubmit']);  
                        return; 
                    }    
                }

                $insertingData = [
                    'moderatorId' => $moderatorId,
                    'mainFilterId' => $input['mainFilterId'],
                    'subFilterName' => $input['subFilterName']
                ];

                try{
                    if($this->moderatorModel->addSubFilterName($insertingData)){
                        echo json_encode(['success' => true, 'message' => 'Added main filter name the database successfully']);
                    }
                    else{
                        echo json_encode(['success' => false, 'message' => 'Failed to add the main filter name to the database.']);
                    }

                } catch(PDOException $e) {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
                }
            }
        }

        public function subFilterEdit(){
            header('Content-Type: application/json');

            if( $_SERVER['REQUEST_METHOD'] == "PUT" ) {

                $input = json_decode(file_get_contents('php://input'),true);
                $moderatorId = getSession('user_id');
            
                if (!$moderatorId || empty($input['subFilterId']) || empty($input['subFilterName']) ){
                    echo json_encode(['success' => false, 'message' => 'Invalid request']);
                    return;
                }

                // Validate ownership
                /*
                $existingTrip = $this->moderatorModel->getTripById($input['trip_id'], $userId);
                
                if (!$existingTrip) {
                    echo json_encode(['success' => false, 'message' => 'Trip not found']);
                    return;
                }
                    */
                // Update trip
                $updateData = [
                    'subFilterId' => $input['subFilterId'],
                    'subFilterName' => $input['subFilterName'],
                    'moderatorId' => $moderatorId
                ];

                if ($this->moderatorModel->updateSubFilterName($updateData)) {
                    echo json_encode(['success' => true, 'message' => 'SubFilter name updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'SubFilter Failed to update']);
                }
            }
        }

        public function deleteSubFilter(){

            header('Content-Type: application/json');
            
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                echo json_encode(['success' => false, 'message' => 'Invalid method']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $userId = getSession('user_id');
            
            if (!$userId || empty($input['subFilterId'])) {
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

            if ($this->moderatorModel->deleteSubFilterName($input['subFilterId'])) {
                echo json_encode(['success' => true, 'message' => 'Main Filter deleted successfully']);
                return;
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete Main Filter']);
                return;
            }
        }

        public function getAllFilters(){

            header('Content-Type: application/json');

            $allFilters = $this->moderatorModel->loadAllFilters();

            echo json_encode([
                'success' => true,
                'allFilters' => $allFilters
            ]);
            return;
        }

        public function getTravelSpotByName(){

            header('Content-Type: application/json');

            if ($_SERVER['REQUEST_METHOD'] == 'POST'){
                
                $input = json_decode(file_get_contents('php://input'),true);

                if(!$input['name']){
                    echo json_encode([ 'success' => false,'travelSpots' => null, 'message'=>'no searching name' ]);
                    return;
                }

                else{
                    $allMatchingTravelSpots = $this->moderatorModel->searchTravelSpotByName($input['name']);
                    echo json_encode([
                        'success' => true,
                        'travelSpots' => $allMatchingTravelSpots,
                        'message' => 'searched successfully'
                    ]);
                    return;
                }

            } else{
                echo json_encode([
                        'success' => false,
                        'travelSpots' => '',
                        'message' => 'invalid method'
                ]);
                return;
            }
        }

        public function addTravelSpot(){

            header('Content-Type: application/json');

            if ($_SERVER['REQUEST_METHOD'] == 'POST'){
                
                $userId = getSession('user_id');

                // When sending FormData, data comes through $_POST, not php://input
                $input = $_POST;

                error_log("Recieved Size: " . print_r($_SERVER['CONTENT_LENGTH'], true));
                error_log("Received input: " . print_r($input, true));

                if(empty($input)){
                    echo json_encode([ 'success' => false, 'message'=>'No form data received' ]);
                    return;
                }

                // For FormData, subFilters, nearbySpots, and itinerary are JSON strings that need to be decoded
                $input['subFilters'] = !empty($input['subFilters']) ? json_decode($input['subFilters'], true) : [];
                $input['nearbySpots'] = !empty($input['nearbySpots']) ? json_decode($input['nearbySpots'], true) : [];
                $input['itinerary'] = !empty($input['itinerary']) ? json_decode($input['itinerary'], true) : [];

                $required_fields =  [    
                                        'spotName','overview','province', 'district', 'bestTimeFrom', 'bestTimeTo', 'visitingDurationMax', 'ticketPriceLocal', 'ticketPriceForeigner', 'openingHours', 'ticketDetails', 
                                        'parkingDetails', 'accessibility', 'facilities', 'travelerTips','subFilters', 'nearbySpots', 'itinerary'
                                    ];

                foreach($required_fields as $field) {
                    if(empty($input[$field])){
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);  
                        return; 
                    }    
                }

                $photos = ['photo1', 'photo2', 'photo3', 'photo4'];
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];

                foreach ($photos as $photoKey) {
                    if (!isset($_FILES[$photoKey])) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $photoKey)) . ' is required']);  
                        return;
                    }   

                    $file = $_FILES[$photoKey];

                    if ($file['size'] > 1.5 * 1024 * 1024) { // 5MB
                        echo json_encode(['success' => false, 'message' => 'File size exeeded 5MB !']);  
                        return;
                    }

                    error_log(ini_get('post_max_size'));
                    error_log($file['size']);

                    error_log("photo names: " . print_r($file['tmp_name'], true));
                    
                    $mimeType = mime_content_type($file['tmp_name']);

                    if (!in_array($mimeType, $allowedTypes)) {
                        echo json_encode(['success' => false, 'message' => 'File type is not matching !']);  
                        return;
                    }
                }

                $spotsTableInsertingData = [
                                    'spotName' => $input['spotName'],
                                    'overview' => $input['overview'],
                                    'province' => $input['province'],
                                    'district' => $input['district'],
                                    'bestTimeFrom' => $input['bestTimeFrom'],
                                    'bestTimeTo' => $input['bestTimeTo'],
                                    'visitingDurationMax' => $input['visitingDurationMax'],
                                    'ticketPriceLocal' => $input['ticketPriceLocal'],
                                    'ticketPriceForeigner' => $input['ticketPriceForeigner'],
                                    'openingHours' => $input['openingHours'],
                                    'ticketDetails' => $input['ticketDetails'],
                                    'parkingDetails' => $input['parkingDetails'],
                                    'accessibility' => $input['accessibility'],
                                    'facilities' => $input['facilities'],
                                    'travelerTips' => $input['travelerTips']
                ];

                error_log("Data: " . json_encode($spotsTableInsertingData));

                try{
                    $insertedSpotId = $this->moderatorModel->addTravelSpotsTable($spotsTableInsertingData);
                    if($insertedSpotId){
                        try{
                            $subFilters = $input['subFilters'];
                            error_log("subFilters: " . json_encode($subFilters));
                            foreach($subFilters as $subFilterId){
                                $this->moderatorModel->addTravelSpotSubFilters($insertedSpotId, $subFilterId);
                            }

                            $nearbySpots = $input['nearbySpots'];
                            error_log("nearbySpots: " . json_encode($nearbySpots));
                            foreach($nearbySpots as $nearbySpotId){
                                $this->moderatorModel->addTravelSpotNearbySpots($insertedSpotId, $nearbySpotId);
                            }

                            $itinerary = $input['itinerary'];
                            error_log("itinerary: " . json_encode($itinerary));
                            foreach($itinerary as $location){
                                $this->moderatorModel->addTravelSpotItinerary($insertedSpotId, $location['name'],$location['lat'], $location['lng']);
                            }

                            foreach($photos as $photoKey){
                                $extension = pathinfo($_FILES[$photoKey]['name'], PATHINFO_EXTENSION);
                                $newName = $photoKey . '_' . uniqid('spot_', true).'.'. $extension;

                                //error_log("Received photo input: " . print_r($_FILES[$photoKey]['name'], true));
                                error_log('new name :'. print_r($newName,true) );

                                $uploadDir = ROOT_PATH.'/public/uploads/travelSpots';
                                if (!is_dir($uploadDir)) {
                                    mkdir($uploadDir, 0755, true);
                                }
                                move_uploaded_file($_FILES[$photoKey]['tmp_name'], $uploadDir . $newName);
                                
                                if($this->moderatorModel->addTravelSpotPhotos($insertedSpotId, $newName)){
                                    error_log('travel spot photo added to the database successfully.');
                                };
                            }

                            $contributionData = [
                                'spotId' => $insertedSpotId,
                                'moderatorId' => $userId
                            ];
                            //Adding contributer who put up the data
                            $this->moderatorModel->addContributorData($contributionData);
                        
                        } catch(PDOException $e) {
                            http_response_code(500);
                            echo json_encode(['success' => false, 'message' => "Database error occurred while travel spot data {$e->getMessage()}"]);
                            return;
                        }
                    }
                    else{
                        echo json_encode(['success' => false, 'message' => 'Failed to add data to the travel_spots table.']);
                        return;
                    }

                } catch(PDOException $e) {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
                    return;
                }

                // Success response 
                echo json_encode([
                    'success' => true,
                    'message' => 'Travel spot added successfully',
                    'spotId' => $insertedSpotId
                ]);
                return;
                
            } else{
                echo json_encode([
                        'success' => false,
                        'message' => 'invalid method'
                ]);
                return;
            }
        }

        public function getTravelSpotCardData(){
            header('Content-Type: application/json');

            try{
                $travelSpotCardData = $this->moderatorModel->loadTravelSpotCardData();
                
            }catch(PDOException $e){
                http_response_code(500);
                echo json_encode(['success' => false, 'travelSpotCardData'=>NULL, 'message' => 'Database error occurred when getting travel Spot card Data'.$e->getMessage()]);
                return;
            }

            echo json_encode([
                'success' => true,
                'travelSpotCardData' => $travelSpotCardData,
                'message' => 'Travel Spot Card data loaded Successfully.'
            ]);
            return;
        }  
        
        public function getTravelSpotData($travelSpotId){

            header('Content-Type: application/json');

            error_log(" requested travel spot id : " . $travelSpotId);

            try{
                $travelSpotData = $this->moderatorModel->loadTravelSpotData($travelSpotId);
                if($travelSpotData){
                    echo json_encode([
                        'success' => true,
                        'travelSpotData' => $travelSpotData,
                        'message' => 'Travel Spot data loaded Successfully.'
                    ]);
                    return;

                } else{
                    echo json_encode([
                        'success' => false,
                        'travelSpotData' => NULL,
                        'message' => 'No Travel Spot data found for the given ID.'
                    ]);
                    return;
                }

            } catch(PDOException $e){
                http_response_code(500);
                echo json_encode(['success' => false, 'travelSpotData'=>NULL, 'message' => 'Database error occurred when getting travel Spot Data(not a card full travel spot)']);
                return;
            }
        }

        public function editTravelSpot(){
            header('Content-Type: application/json');

            if ($_SERVER['REQUEST_METHOD'] == 'POST'){
                
                $userId = getSession('user_id');
                $input = $_POST;

                error_log("Received input for editTravelSpot: " . print_r($input, true));

                if(empty($input)){
                    echo json_encode([ 'success' => false, 'message'=>'No form data received or spotId missing' ]);
                    return;
                }

                // For FormData, subFilters, nearbySpots, and itinerary are JSON strings that need to be decoded
                $input['subFilters'] = !empty($input['subFilters']) ? json_decode($input['subFilters'], true) : [];
                $input['nearbySpots'] = !empty($input['nearbySpots']) ? json_decode($input['nearbySpots'], true) : [];
                $input['itinerary'] = !empty($input['itinerary']) ? json_decode($input['itinerary'], true) : [];

                $required_fields =  [    
                                        'spotName','overview','province', 'district', 'bestTimeFrom', 'bestTimeTo', 'visitingDurationMax', 'ticketPriceLocal', 'ticketPriceForeigner', 'openingHours', 'ticketDetails', 
                                        'parkingDetails', 'accessibility', 'facilities', 'travelerTips','subFilters', 'nearbySpots', 'itinerary'
                                    ];

                foreach($required_fields as $field) {
                    if(empty($input[$field])){
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);  
                        return; 
                    }    
                }

                $photos = ['photo1', 'photo2', 'photo3', 'photo4'];
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];

                foreach ($photos as $photoKey) {
                    if (!isset($_FILES[$photoKey])) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $photoKey)) . ' is required']);  
                        return;
                    }   

                    $file = $_FILES[$photoKey];

                    if ($file['size'] > 5 * 1024 * 1024) { // 5MB
                        echo json_encode(['success' => false, 'message' => 'File size exceeded 5MB !']);  
                        return;
                    }

                    $mimeType = mime_content_type($file['tmp_name']);

                    if (!in_array($mimeType, $allowedTypes)) {
                        echo json_encode(['success' => false, 'message' => 'File type is not matching !']);  
                        return;
                    }
                }

                $spotsTableInsertingData = [
                                    'spotId' => $input['spotId'],
                                    'spotName' => $input['spotName'],
                                    'overview' => $input['overview'],
                                    'province' => $input['province'],
                                    'district' => $input['district'],
                                    'bestTimeFrom' => $input['bestTimeFrom'],
                                    'bestTimeTo' => $input['bestTimeTo'],
                                    'visitingDurationMax' => $input['visitingDurationMax'],
                                    'ticketPriceLocal' => $input['ticketPriceLocal'],
                                    'ticketPriceForeigner' => $input['ticketPriceForeigner'],
                                    'openingHours' => $input['openingHours'],
                                    'ticketDetails' => $input['ticketDetails'],
                                    'parkingDetails' => $input['parkingDetails'],
                                    'accessibility' => $input['accessibility'],
                                    'facilities' => $input['facilities'],
                                    'travelerTips' => $input['travelerTips']
                ];

                error_log("Data: " . json_encode($spotsTableInsertingData));

               try{
                    $insertedSpotId = $this->moderatorModel->updateTravelSpotsTable($spotsTableInsertingData);
                    if($insertedSpotId){
                        try{
                            $subFilters = $input['subFilters'];
                            error_log("subFilters: " . json_encode($subFilters));

                            $this->moderatorModel->deleteTravelSpotSubFilters($insertedSpotId);
                            foreach($subFilters as $subFilterId){
                                $this->moderatorModel->addTravelSpotSubFilters($insertedSpotId, $subFilterId);
                            }

                            $nearbySpots = $input['nearbySpots'];
                            error_log("nearbySpots: " . json_encode($nearbySpots));
                            $this->moderatorModel->deleteTravelSpotNearbySpots($insertedSpotId);
                            foreach($nearbySpots as $nearbySpotId){
                                $this->moderatorModel->addTravelSpotNearbySpots($insertedSpotId, $nearbySpotId);
                            }

                            $itinerary = $input['itinerary'];
                            error_log("itinerary: " . json_encode($itinerary));

                            $this->moderatorModel->deleteTravelSpotItinerary($insertedSpotId);
                            foreach($itinerary as $location){
                                $this->moderatorModel->addTravelSpotItinerary($insertedSpotId, $location['name'],$location['lat'], $location['lng']);
                            }

                            $this->moderatorModel->deleteTravelSpotPhotos($insertedSpotId);

                            foreach($photos as $photoKey){
                                $extension = pathinfo($_FILES[$photoKey]['name'], PATHINFO_EXTENSION);
                                $newName = $photoKey . '_' . uniqid('spot_', true) . '.' . $extension;

                                error_log("Received photo input: " . print_r($_FILES[$photoKey]['name'], true));

                                $uploadDir = ROOT_PATH.'/public/uploads/travelSpots';
                                if (!is_dir($uploadDir)) {
                                    mkdir($uploadDir, 0755, true);
                                }
                                move_uploaded_file($_FILES[$photoKey]['tmp_name'], $uploadDir . '/' . $newName);
                                $this->moderatorModel->addTravelSpotPhotos($insertedSpotId, $newName);
                            }

                            $contributionData = [
                                'spotId' => $insertedSpotId,
                                'moderatorId' => $userId
                            ];

                            //Updating contributor who put up the data
                            $this->moderatorModel->updateContributorData($contributionData);
                        
                        } catch(PDOException $e) {
                            http_response_code(500);
                            echo json_encode(['success' => false, 'message' => "Database error occurred while editing travel spot data {$e->getMessage()}"]);
                            return;
                        }
                    }
                    else{
                        echo json_encode(['success' => false, 'message' => 'Failed to add data to the travel_spots table.']);
                        return;
                    }

                } catch(PDOException $e) {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => "Database error occurred {$e->getMessage()}"]);
                    return;
                }

                // Success response 
                echo json_encode([
                    'success' => true,
                    'message' => 'Travel spot added successfully',
                    'spotId' => $insertedSpotId
                ]);
                return;
                
            } else{
                echo json_encode([
                        'success' => false,
                        'message' => 'invalid method '
                ]);
                return;
            }
        }

        public function deleteTravelSpot(){

            error_log('delete travel spot called');
            header('Content-Type: application/json');
            
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                echo json_encode(['success' => false, 'message' => 'Invalid method']);
                return;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $userId = getSession('user_id');

            if (!$userId || empty($input['spotId'])) {
                echo json_encode(['success' => false, 'message' => 'userId or deleting spotID not found']);
                return;
            }

            error_log(print_r($input, true));

            try{
                if ($this->moderatorModel->eraseTravelSpot($input['spotId'])) {
                    echo json_encode(['success' => true, 'message' => 'Travel spot deleted successfully']);
                    return;
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete travel spot']);
                    return;
                }

            } catch(PDOException $e){
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => "Database error occurred {$e->getMessage()}"]);
                return;
            }
        }
    }

?>