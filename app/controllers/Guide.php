<?php
    class Guide extends Controller{

        private $guideModel;
        public function __construct() {
            $this->guideModel = $this->model('GuideModel');
        }

        public function index() {
            $unEncodedResponse = [
                'tabId'=>'dashboard',
                'loadingContent'=>null
            ];
            $this->view('UserTemplates/guideDash', $unEncodedResponse);
        }

        public function schedule() {
             ob_start();
            $this->view('Guide/schedule/schedule');
            $fullcontent = ob_get_clean();

            $html = $fullcontent;
            $css = URL_ROOT.'/public/css/guide/schedule/schedule.css';
            $js = URL_ROOT.'/public/js/guide/schedule/schedule.js';

            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];

            $unEncodedResponse = [
                'tabId'=>'schedule',
                'loadingContent'=>$loadingContent
            ];
            $this->view('UserTemplates/guideDash', $unEncodedResponse);
        }

        public function requests() {
            ob_start();
            $this->view('Guide/requests/requests');
            $fullcontent = ob_get_clean();

            $html = $fullcontent;
            $css = URL_ROOT.'/public/css/guide/requests/requests.css';
            $js = URL_ROOT.'/public/js/guide/requests/requests.js';

            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];

            $unEncodedResponse = [
                'tabId'=>'requests',
                'loadingContent'=>$loadingContent
            ];
            $this->view('UserTemplates/guideDash', $unEncodedResponse);
        }

        public function subtabAvailabilityCalendar(){
            ob_start();
            $this->view('Guide/schedule/subtabAvailabilityCalendar');
            $fullcontent = ob_get_clean();

            $html = $fullcontent;
            $css = URL_ROOT.'/public/css/guide/schedule/subtabAvailabilityCalendar.css';
            $js = URL_ROOT.'/public/js/guide/schedule/subtabAvailabilityCalendar.js';

            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];

            $unEncodedResponse = [
                'ok' => true,
                'loadingContent'=>$loadingContent
            ];
            echo json_encode($unEncodedResponse);
        }

        public function guideProfile() {

            ob_start();
            $this->view('Guide/profile/guideProfile');
            $fullcontent = ob_get_clean();

            $html = $fullcontent;
            $css = URL_ROOT.'/public/css/guide/profile/guideProfile.css';
            $js = URL_ROOT.'/public/js/guide/profile/guideProfile.js';

            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];

            $unEncodedResponse = [
                'tabId'=>'my-profile',
                'loadingContent'=>$loadingContent
            ];
            $this->view('UserTemplates/guideDash', $unEncodedResponse);
        }

        public function selectGuideSpot() {
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

            $this->view('UserTemplates/guideDash', $unEncodedResponse);
        }

        public function retrieveBasicGuideInfo() {
            
            header('Content-Type: application/json');
            
            // Log to PHP error log instead of output
            error_log("retrieveBasicGuideInfo called f");

            $userId = getSession('user_id');

            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            try {
                $driverInfo = $this->guideModel->getBasicGuideInfo($userId);
                echo json_encode(['success' => true, 'driverInfo' => $driverInfo]);

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when retrieving driver info: ' . $e->getMessage()]);
            }
        }

        public function editGuidePersonalInfo() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');

            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            // Read FormData from POST request
            $input = $_POST;

            error_log("editDriverPersonalInfo called with input: " . print_r($input, true));

            if (empty($input)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid input data']);
                return;
            }

            try {
                $updateSuccess = $this->guideModel->updateGuidePersonalInfo($userId, $input);

                if ($updateSuccess) {
                    // Update session data if full name changed
                    if (isset($input['fullName'])) {
                        setSession('user_fullname', $input['fullName']);
                    }

                    echo json_encode(['success' => true, 'message' => 'Driver personal info updated successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to update driver personal info']);
                }

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when updating driver info: ' . $e->getMessage()]);
            }
        }

        public function guideSpots() {
            ob_start();
            $this->view('Guide/guideSpots/guideSpots');
            $fullcontent = ob_get_clean();

            $html = $fullcontent;
            $css = URL_ROOT.'/public/css/guide/guideSpots/guideSpots.css';
            $js = URL_ROOT.'/public/js/guide/guideSpots/guideSpots.js';

            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];

            $unEncodedResponse = [
                'tabId'=>'guideSpots',
                'loadingContent'=>$loadingContent
            ];
            $this->view('UserTemplates/guideDash', $unEncodedResponse);
        }

        public function getTravelSpotCardDataBySpotId($spotId){

            header('Content-Type: application/json');

            try{
                $travelSpotCardData = $this->guideModel->loadTravelSpotCardDataBySpotId($spotId);
                    
            }catch(PDOException $e){
                http_response_code(500);
                echo json_encode(['success' => false, 'travelSpotCardData'=>NULL, 'message' => 'Database error occurred when getting travel Spot card Data by spot id'.$e->getMessage()]);
                return;
            }

            echo json_encode([
                    'success' => true,
                    'travelSpotCardData' => $travelSpotCardData,
                    'message' => 'Travel Spot Card data loaded Successfully.'
            ]);
            return;
        }

        public function addGuideSpot() {
            header('Content-Type: application/json');
            
            $userId = getSession('user_id');

            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data || !isset($data['spotId'], $data['baseCharge'], $data['chargeType'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                return;
            }

            try {
                $result = $this->guideModel->addGuideSpot($userId, $data);
                if ($result['success']) {
                    echo json_encode(['success' => true, 'message' => $result['message']]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => $result['message']]);
                }
            } catch (Exception $e) {
                error_log("Error in addGuideSpot: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Internal server error']);
            }
        }

        public function getGuideSpots() {
            header('Content-Type: application/json');
            
            $userId = getSession('user_id');

            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            try {
                $spots = $this->guideModel->getGuideLocations($userId);
                echo json_encode(['success' => true, 'spots' => $spots]);
            } catch (Exception $e) {
                error_log("Error in getGuideSpots: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Internal server error']);
            }
        }

        public function toggleGuideSpot() {
            header('Content-Type: application/json');
            
            $userId = getSession('user_id');

            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            $spotId = $_POST['spotId'] ?? null;
            $isActive = $_POST['isActive'] ?? null;

            if (!$spotId || $isActive === null) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Spot ID and active status are required']);
                return;
            }

            // Convert to boolean
            if (is_string($isActive)) {
                $isActive = $isActive === 'true' || $isActive === '1';
            }

            try {
                $result = $this->guideModel->toggleGuideSpotStatus($userId, $spotId, $isActive);
                if ($result['success']) {
                    echo json_encode(['success' => true, 'message' => $result['message']]);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => $result['message']]);
                }
            } catch (Exception $e) {
                error_log("Error in toggleGuideSpot: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Internal server error']);
            }
        }

        public function updateGuideSpot() {
            header('Content-Type: application/json');
            
            $userId = getSession('user_id');

            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
                return;
            }

            if (!isset($input['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing id field']);
                return;
            }

            $spotId = $input['id'];
            $data = [
                'baseCharge' => $input['baseCharge'] ?? 0,
                'chargeType' => $input['chargeType'] ?? 'per_person',
                'minGroupSize' => $input['minGroupSize'] ?? 1,
                'maxGroupSize' => $input['maxGroupSize'] ?? 20,
                'description' => $input['description'] ?? '',
                'photoPath' => $input['photoPath'] ?? null
            ];

            // Validation
            if (!is_numeric($data['baseCharge']) || $data['baseCharge'] <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid base charge']);
                return;
            }

            if (!is_numeric($data['minGroupSize']) || !is_numeric($data['maxGroupSize']) || 
                $data['minGroupSize'] > $data['maxGroupSize']) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid group size range']);
                return;
            }

            try {
                $result = $this->guideModel->updateGuideSpot($userId, $spotId, $data);
                if ($result['success']) {
                    echo json_encode(['success' => true, 'message' => $result['message']]);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => $result['message']]);
                }
            } catch (Exception $e) {
                error_log("Error in updateGuideSpot: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Internal server error']);
            }
        }

        public function deleteGuideSpot() {
            header('Content-Type: application/json');
            
            $userId = getSession('user_id');

            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Spot ID is required']);
                return;
            }

            $spotId = $input['id'];

            try {
                $result = $this->guideModel->deleteGuideSpot($userId, $spotId);
                if ($result['success']) {
                    echo json_encode(['success' => true, 'message' => $result['message']]);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => $result['message']]);
                }
            } catch (Exception $e) {
                error_log("Error in deleteGuideSpot: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Internal server error']);
            }
        }

        // -----------------------------------------------------------------------
        // Guide Unavailability endpoints
        // -----------------------------------------------------------------------

        public function getGuideUnavailability() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            try {
                $result = $this->guideModel->getGuideUnavailability($userId);
                echo json_encode($result);
            } catch (Exception $e) {
                error_log("Error in getGuideUnavailability: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Internal server error']);
            }
        }

        public function addGuideUnavailability() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);

            error_log("addGuideUnavailability called with input controller: " . print_r($input, true));

            if (!$input || !isset($input['slots']) || !is_array($input['slots']) || count($input['slots']) === 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'No slots provided']);
                return;
            }

            // Validate each slot
            $today   = date('Y-m-d');
            $maxDate = date('Y-m-d', strtotime('+90 days'));

            foreach ($input['slots'] as $index => $slot) {
                if (empty($slot['unavailableDate']) || empty($slot['startTime']) || empty($slot['endTime'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => "Slot at index $index is missing required fields (unavailableDate, startTime, endTime)"]);
                    return;
                }

                if ($slot['unavailableDate'] < $today || $slot['unavailableDate'] > $maxDate) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => "Slot at index $index has a date outside the allowed 90-day window"]);
                    return;
                }

                if ($slot['startTime'] >= $slot['endTime']) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => "Slot at index $index: start time must be before end time"]);
                    return;
                }

                $reason = $slot['reason'] ?? 'personal';
                if (!in_array($reason, ['personal', 'booked'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => "Slot at index $index has an invalid reason"]);
                    return;
                }

                if ($reason === 'personal' && empty(trim($slot['personalReason'] ?? ''))) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => "Slot at index $index: personal reason is required"]);
                    return;
                }
            }

            try {
                $result = $this->guideModel->addGuideUnavailabilitySlots($userId, $input['slots']);
                if ($result['success']) {
                    echo json_encode(['success' => true, 'message' => $result['message']]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => $result['message']]);
                }
            } catch (Exception $e) {
                error_log("Error in addGuideUnavailability: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Internal server error']);
            }
        }

        public function removeGuideUnavailabilitySlot() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input || !isset($input['slotId'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Slot ID is required']);
                return;
            }

            $slotId = (int)$input['slotId'];
            if ($slotId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid slot ID']);
                return;
            }

            try {
                $result = $this->guideModel->removeGuideUnavailabilitySlot($userId, $slotId);
                if ($result['success']) {
                    echo json_encode(['success' => true, 'message' => $result['message']]);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => $result['message']]);
                }
            } catch (Exception $e) {
                error_log("Error in removeGuideUnavailabilitySlot: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Internal server error']);
            }
        }

        public function getMyRequests() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            try {
                $requests = $this->guideModel->getRequestsByGuide($userId);

                echo json_encode(['success' => true, 'requests' => $requests]);
            } catch (Exception $e) {
                error_log('Error in getMyRequests: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to load requests']);
            }
        }

        public function acceptRequest($requestId = null) {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            if (!$requestId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Request ID is required']);
                return;
            }

            try {
                $result = $this->guideModel->updateGuideRequestStatus($userId, $requestId, 'accepted');
                if ($result['success']) {
                    echo json_encode(['success' => true, 'message' => 'Request accepted']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => $result['message']]);
                }
            } catch (Exception $e) {
                error_log('Error in acceptRequest: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to accept request']);
            }
        }

        public function rejectRequest($requestId = null) {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            if (!$requestId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Request ID is required']);
                return;
            }

            // Read rejection reason if provided (not stored currently, but logged)
            $input = json_decode(file_get_contents('php://input'), true);
            $reason = $input['reason'] ?? null;
            if ($reason) {
                error_log("Request $requestId rejected by guide $userId. Reason: " . $reason);
            }

            try {
                $result = $this->guideModel->updateGuideRequestStatus($userId, $requestId, 'rejected');
                if ($result['success']) {
                    echo json_encode(['success' => true, 'message' => 'Request rejected']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => $result['message']]);
                }
            } catch (Exception $e) {
                error_log('Error in rejectRequest: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to reject request']);
            }
        }

        public function getTripItinerary($tripId = null) {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            if (!$tripId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Trip ID is required']);
                return;
            }

            try {
                $itinerary = $this->guideModel->getTripItinerary($tripId);
                
                if ($itinerary) {
                    echo json_encode(['success' => true, 'itinerary' => $itinerary]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Trip not found']);
                }
            } catch (Exception $e) {
                error_log('Error in getTripItinerary: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to load trip itinerary']);
            }
        }

        public function earnings() {
            ob_start();
            $this->view('Guide/earnings/earnings');
            $fullcontent = ob_get_clean();

            $html = $fullcontent;
            $css = URL_ROOT.'/public/css/guide/earnings/earnings.css';
            $js = URL_ROOT.'/public/js/guide/earnings/earnings.js';

            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];

            $unEncodedResponse = [
                'tabId'=>'earnings',
                'loadingContent'=>$loadingContent
            ];
            $this->view('UserTemplates/guideDash', $unEncodedResponse);
        }

        public function getEarningsSummary() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            try {
                $summary = $this->guideModel->getEarningsSummary($userId);
                echo json_encode(['success' => true, 'summary' => $summary]);
            } catch (Exception $e) {
                error_log('Error in Guide/getEarningsSummary: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to load earnings summary']);
            }
        }

        public function getEarningsByStatus($status = null) {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            $allowed = ['pending', 'paid', 'refunded'];
            if (!$status || !in_array($status, $allowed)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid earnings status']);
                return;
            }

            try {
                $earnings = $this->guideModel->getEarningsByStatus($userId, $status);
                echo json_encode(['success' => true, 'earnings' => $earnings]);
            } catch (Exception $e) {
                error_log('Error in Guide/getEarningsByStatus: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to load earnings']);
            }
        }

        public function getMonthlyEarnings() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            try {
                $monthly = $this->guideModel->getMonthlyEarnings($userId);
                echo json_encode(['success' => true, 'monthly' => $monthly]);
            } catch (Exception $e) {
                error_log('Error in Guide/getMonthlyEarnings: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to load monthly earnings']);
            }
        }
    }

    // `/controller/method/parameters
?>

