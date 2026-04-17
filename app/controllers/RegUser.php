<?php

require_once '../app/helpers/session_helper.php';
require_once '../app/helpers/travel_spot_helper.php';
require_once '../app/helpers/currency_helper.php';

    class RegUser extends Controller {

        private $regUserModel;
        public function __construct(){
            $this->regUserModel = $this->model('RegUserModel');
        }

        public function help() {
            ob_start();
            $this->view('Help/helpContent');
            $fullcontent = ob_get_clean();

            $html = $fullcontent;
            $css = URL_ROOT.'/public/css/helper/help.css';
            $js = URL_ROOT.'/public/js/helper/help.js';

            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];

            $unEncodedResponse = [
                'tabId'=>'help',
                'loadingContent'=>$loadingContent
            ];
            $this->view('UserTemplates/travellerDash', $unEncodedResponse);
        }


        public function support($subtabId = null){

            // Support subtab fetch route: /RegUser/support/{subtabId}
            if ($subtabId !== null) {
                if ($subtabId === 'subtabHelpdesk') {
                    $this->subtabHelpdesk();
                    return;
                }

                // Keep backward compatibility with historical typo from support view.
                if ($subtabId === 'subtabComplainAndFeedback' || $subtabId === 'complpainAndFeedback') {
                    $this->subtabComplainAndFeedback();
                    return;
                }

                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode([
                    'ok' => false,
                    'message' => 'Unknown support subtab'
                ]);
                return;
            }

            ob_start();
            $this->view('RegUser/support/support');
            $fullcontent = ob_get_clean();

            $html = $fullcontent;
            $css = URL_ROOT.'/public/css/RegUser/support/support.css';
            $js = URL_ROOT.'/public/js/RegUser/support/support.js';

            $loadingContent = [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];

            $unEncodedResponse = [
                'tabId'=>'support',
                'loadingContent'=>$loadingContent
            ];
            $this->view('UserTemplates/travellerDash', $unEncodedResponse);
        }

        public function subtabHelpdesk() {
            header('Content-Type: application/json');

            ob_start();
            $this->view('RegUser/support/subtabHelpdesk');
            $html = ob_get_clean();

            echo json_encode([
                'ok' => true,
                'loadingContent' => [
                    'html' => $html,
                    'css' => URL_ROOT.'/public/css/regUser/support/subtabHelpdesk.css',
                    'js' => URL_ROOT.'/public/js/regUser/support/subtabHelpdesk.js'
                ]
            ]);
        }

        public function subtabComplainAndFeedback() {
            header('Content-Type: application/json');

            ob_start();
            $this->view('RegUser/support/subtabComplainAndFeedback');
            $html = ob_get_clean();

            echo json_encode([
                'ok' => true,
                'loadingContent' => [
                    'html' => $html,
                    'css' => URL_ROOT.'/public/css/regUser/support/subtabComplainAndFeedback.css',
                    'js' => URL_ROOT.'/public/js/regUser/support/subtabComplainAndFeedback.js'
                ]
            ]);
        }

        public function submitProblem() {
            header('Content-Type: application/json');

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Invalid method']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $userId = getSession('user_id');

            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'You must be logged in to submit a problem']);
                return;
            }

            $subject = trim((string)($input['subject'] ?? ''));
            $message = trim((string)($input['message'] ?? ''));

            if ($subject === '' || $message === '') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Subject and message are required']);
                return;
            }

            $data = [
                'userId' => (int)$userId,
                'subject' => htmlspecialchars($subject, ENT_QUOTES, 'UTF-8'),
                'message' => htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
            ];

            try {
                if ($this->regUserModel->submitUserProblem($data)) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Your complaint/feedback was submitted successfully. Our team will review it soon.'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to submit complaint/feedback']);
                }
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred']);
            }
        }

        public function getUserProblemsByUserId() {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            $filter = isset($_GET['filter']) ? trim((string)$_GET['filter']) : 'all';

            try {
                $problems = $this->regUserModel->getUserProblemsByUserId((int)$userId, $filter);
                echo json_encode([
                    'success' => true,
                    'problems' => $problems
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred']);
            }
        }

        


        private function getProviderRevisionState($userId, $tripId) {
            if (!$userId || !$tripId) {
                return null;
            }

            return $this->regUserModel->getTripRevisionStateForUser((int)$userId, (int)$tripId);
        }

        private function canModifyTripAssignments($userId, $tripId) {
            $state = $this->getProviderRevisionState($userId, $tripId);
            if (!$state) {
                return false;
            }

            return ($state['tripStatus'] ?? null) === 'pending';
        }

        private function canModifyTripEvents($userId, $tripId) {
            $state = $this->getProviderRevisionState($userId, $tripId);
            if (!$state) {
                return false;
            }

            return empty($state['eventChangesLocked']);
        }

        private function getTripLockedMessage() {
            return 'Trip is waiting for confirmations, awaiting payment, or already scheduled. Driver and guide changes are locked.';
        }

        private function getEventLockedMessage() {
            return 'Trip events are locked after confirmation. Only rejected driver/guide requests can be changed.';
        }

        private function canConfirmTripNow($userId, $tripId) {
            $state = $this->getProviderRevisionState($userId, $tripId);
            if (!$state) {
                return false;
            }

            if (($state['tripStatus'] ?? null) !== 'pending') {
                return false;
            }

            return ((int)($state['rejectedDriverCount'] ?? 0) + (int)($state['rejectedGuideCount'] ?? 0)) === 0;
        }

        private function getPayHereConfig() {
            return [
                'merchantId' => defined('PAYHERE_MERCHANT_ID') ? PAYHERE_MERCHANT_ID : '1235176',
                'merchantSecret' => defined('PAYHERE_MERCHANT_SECRET') ? PAYHERE_MERCHANT_SECRET : 'MjYxNjc3NTk5NDEwNDYxNjg1MzA0MTcwOTc4NjA2MzU3Njk2ODk2NQ==',
                'currency' => defined('PAYHERE_CURRENCY') ? PAYHERE_CURRENCY : 'LKR',
                'checkoutUrl' => defined('PAYHERE_CHECKOUT_URL') ? PAYHERE_CHECKOUT_URL : 'https://sandbox.payhere.lk/pay/checkout'
            ];
        }

        private function buildPayHereCheckoutHash($merchantId, $merchantSecret, $orderId, $amount, $currency) {
            $formattedAmount = number_format((float)$amount, 2, '.', '');
            return strtoupper(md5(
                (string)$merchantId
                . (string)$orderId
                . $formattedAmount
                . (string)$currency
                . strtoupper(md5((string)$merchantSecret))
            ));
        }

        private function buildPayHereNotifyHash($merchantId, $merchantSecret, $orderId, $payhereAmount, $currency, $statusCode) {
            return strtoupper(md5(
                (string)$merchantId
                . (string)$orderId
                . (string)$payhereAmount
                . (string)$currency
                . (string)$statusCode
                . strtoupper(md5((string)$merchantSecret))
            ));
        }

        private function parseTripPaymentOrderId($orderId) {
            if (!is_string($orderId)) {
                return null;
            }

            if (preg_match('/^TRIPPAY_(\d+)_(\d+)_\d+$/', $orderId, $matches) !== 1) {
                return null;
            }

            return [
                'tripId' => (int)$matches[1],
                'wholePaymentId' => (int)$matches[2]
            ];
        }

        private function isLocalUrlRootEnvironment() {
            $host = '';

            if (defined('URL_ROOT')) {
                $parsedHost = parse_url((string)URL_ROOT, PHP_URL_HOST);
                if (is_string($parsedHost)) {
                    $host = strtolower(trim($parsedHost));
                }
            }

            if ($host === '') {
                $serverHost = strtolower((string)($_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? '')));
                $host = preg_replace('/:\\d+$/', '', trim($serverHost));
            }

            return in_array($host, ['localhost', '127.0.0.1', '::1'], true);
        }

        private function processTripPaymentGatewayPayload($payload, $allowLocalReturnFallback = false, $sessionUserId = null) {
            $config = $this->getPayHereConfig();

            $merchantId = trim((string)($payload['merchant_id'] ?? ''));
            $orderId = trim((string)($payload['order_id'] ?? ''));
            $payhereAmount = trim((string)($payload['payhere_amount'] ?? ''));
            $payhereCurrency = trim((string)($payload['payhere_currency'] ?? $config['currency']));
            $statusCodeRaw = trim((string)($payload['status_code'] ?? ''));
            $md5sig = strtoupper(trim((string)($payload['md5sig'] ?? '')));
            $transactionId = $payload['payment_id'] ?? ($payload['transaction_id'] ?? null);

            $orderRef = $this->parseTripPaymentOrderId($orderId);
            if (!$orderRef) {
                return [
                    'success' => false,
                    'httpCode' => 400,
                    'message' => 'INVALID ORDER ID'
                ];
            }

            $hasSignedGatewayPayload = ($merchantId !== '' && $payhereAmount !== '' && $statusCodeRaw !== '' && $md5sig !== '');

            if ($hasSignedGatewayPayload) {
                if ($merchantId !== (string)$config['merchantId']) {
                    return [
                        'success' => false,
                        'httpCode' => 400,
                        'message' => 'INVALID MERCHANT'
                    ];
                }

                $expectedSig = $this->buildPayHereNotifyHash(
                    $config['merchantId'],
                    $config['merchantSecret'],
                    $orderId,
                    $payhereAmount,
                    $payhereCurrency,
                    $statusCodeRaw
                );

                if ($expectedSig !== $md5sig) {
                    return [
                        'success' => false,
                        'httpCode' => 400,
                        'message' => 'INVALID SIGNATURE'
                    ];
                }

                $numericStatus = (int)$statusCodeRaw;
                if ($numericStatus === 2) {
                    $result = $this->regUserModel->completeTripPayment(
                        (int)$orderRef['tripId'],
                        (int)$orderRef['wholePaymentId'],
                        $transactionId
                    );

                    if (empty($result['success'])) {
                        return [
                            'success' => false,
                            'httpCode' => 500,
                            'message' => 'PAYMENT UPDATE FAILED'
                        ];
                    }

                    return [
                        'success' => true,
                        'message' => 'PAYMENT COMPLETED'
                    ];
                }

                if ($numericStatus === -1 || $numericStatus === 0) {
                    $this->regUserModel->cancelPendingTripPayment(
                        (int)$orderRef['tripId'],
                        (int)$orderRef['wholePaymentId']
                    );

                    return [
                        'success' => true,
                        'message' => 'PAYMENT CANCELLED'
                    ];
                }

                return [
                    'success' => true,
                    'message' => 'PAYMENT STATUS IGNORED'
                ];
            }

            if (!$allowLocalReturnFallback) {
                return [
                    'success' => false,
                    'httpCode' => 202,
                    'message' => 'AWAITING VERIFIED NOTIFY'
                ];
            }

            $sessionUserId = (int)$sessionUserId;
            if ($sessionUserId <= 0 || !$this->isLocalUrlRootEnvironment()) {
                return [
                    'success' => false,
                    'httpCode' => 202,
                    'message' => 'AWAITING VERIFIED NOTIFY'
                ];
            }

            if ((int)$orderRef['tripId'] <= 0 || (int)$orderRef['wholePaymentId'] <= 0) {
                return [
                    'success' => false,
                    'httpCode' => 409,
                    'message' => 'INVALID PAYMENT REFERENCE'
                ];
            }

            $summary = $this->regUserModel->getTripPaymentSummary($sessionUserId, (int)$orderRef['tripId']);
            if (empty($summary['success'])) {
                return [
                    'success' => false,
                    'httpCode' => 409,
                    'message' => 'PAYMENT SUMMARY UNAVAILABLE'
                ];
            }

            if ((int)($summary['wholePaymentId'] ?? 0) !== (int)$orderRef['wholePaymentId']) {
                return [
                    'success' => false,
                    'httpCode' => 409,
                    'message' => 'PAYMENT REFERENCE MISMATCH'
                ];
            }

            $tripStatus = (string)($summary['tripStatus'] ?? '');
            $paymentStatus = strtolower((string)($summary['paymentStatus'] ?? ''));

            if ($tripStatus === 'scheduled' || $paymentStatus === 'completed') {
                return [
                    'success' => true,
                    'message' => 'PAYMENT ALREADY COMPLETED'
                ];
            }

            if ($tripStatus !== 'awPayment' || $paymentStatus !== 'pending') {
                return [
                    'success' => false,
                    'httpCode' => 409,
                    'message' => 'PAYMENT NOT IN PENDING STATE'
                ];
            }

            $result = $this->regUserModel->completeTripPayment(
                (int)$orderRef['tripId'],
                (int)$orderRef['wholePaymentId'],
                $transactionId
            );

            if (empty($result['success'])) {
                return [
                    'success' => false,
                    'httpCode' => 500,
                    'message' => 'PAYMENT UPDATE FAILED'
                ];
            }

            return [
                'success' => true,
                'message' => 'PAYMENT COMPLETED VIA RETURN FALLBACK'
            ];
        }

        private function normalizeTimeValue($timeValue) {
            $timeValue = trim((string)$timeValue);

            if ($timeValue === '') {
                return null;
            }

            if (preg_match('/^\d{2}:\d{2}$/', $timeValue) === 1) {
                return $timeValue . ':00';
            }

            if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $timeValue) === 1) {
                return $timeValue;
            }

            return null;
        }

        private function normalizeEventTimes($eventStatus, $startTimeValue, $endTimeValue) {
            $status = strtolower(trim((string)$eventStatus));
            $startTime = $this->normalizeTimeValue($startTimeValue);
            $endTime = $this->normalizeTimeValue($endTimeValue);

            if ($status === 'start') {
                if (!$startTime) {
                    return ['success' => false, 'message' => 'Start time is required for start events'];
                }

                return [
                    'success' => true,
                    'startTime' => $startTime,
                    'endTime' => '23:59:00'
                ];
            }

            if ($status === 'intermediate') {
                if (!$startTime || !$endTime) {
                    return ['success' => false, 'message' => 'Both start and end times are required for intermediate events'];
                }

                $startEpoch = strtotime('1970-01-01 ' . $startTime);
                $endEpoch = strtotime('1970-01-01 ' . $endTime);

                if ($endEpoch < $startEpoch) {
                    return ['success' => false, 'message' => 'End time must be later than or equal to start time'];
                }

                return [
                    'success' => true,
                    'startTime' => $startTime,
                    'endTime' => $endTime
                ];
            }

            if ($status === 'end') {
                if (!$endTime) {
                    return ['success' => false, 'message' => 'End time is required for end events'];
                }

                return [
                    'success' => true,
                    'startTime' => $endTime,
                    'endTime' => $endTime
                ];
            }

            return ['success' => false, 'message' => 'Invalid event status'];
        }

        private function validateBoundaryEventUniqueness($userId, $tripId, $eventStatus, $excludeEventId = null) {
            if (!in_array($eventStatus, ['start', 'end'], true)) {
                return ['success' => true];
            }

            $allEvents = $this->regUserModel->getAllTripEvents((int)$userId, (int)$tripId);

            foreach ($allEvents as $event) {
                if ($excludeEventId !== null && (int)$event->eventId === (int)$excludeEventId) {
                    continue;
                }

                if (strtolower((string)$event->eventStatus) === $eventStatus) {
                    return [
                        'success' => false,
                        'message' => ucfirst($eventStatus) . ' event already exists for this trip. All other events should be intermediate.'
                    ];
                }
            }

            return ['success' => true];
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

                $primary_required_fields = ['tripId','eventDate','eventType','eventStatus'];
                
                foreach($primary_required_fields as $field) {
                    if(empty($input[$field])){
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);  
                        return; 
                    }    
                }

                if (!$this->canModifyTripEvents($userId, (int)$input['tripId'])) {
                    http_response_code(409);
                    echo json_encode(['success' => false, 'message' => $this->getEventLockedMessage()]);
                    return;
                }

                $input['eventStatus'] = strtolower(trim((string)$input['eventStatus']));
                if (!in_array($input['eventStatus'], ['start', 'intermediate', 'end'], true)) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid event status']);
                    return;
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

                $normalizedTimes = $this->normalizeEventTimes(
                    $input['eventStatus'],
                    $input['startTime'] ?? null,
                    $input['endTime'] ?? null
                );

                if (empty($normalizedTimes['success'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => $normalizedTimes['message']]);
                    return;
                }

                $input['startTime'] = $normalizedTimes['startTime'];
                $input['endTime'] = $normalizedTimes['endTime'];
                $input['tripId'] = (int)$input['tripId'];
                $input['userId'] = $userId;
                $insertingData = $input;

                try{
                    $statusCheck = $this->validateBoundaryEventUniqueness($userId, $input['tripId'], $input['eventStatus']);
                    if (empty($statusCheck['success'])) {
                        http_response_code(409);
                        echo json_encode(['success' => false, 'message' => $statusCheck['message']]);
                        return;
                    }

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

            if (empty($input['tripId'])) {
                echo json_encode(['success' => false, 'message' => 'Trip ID is required']);
                return;
            }

            $tripId = (int)$input['tripId'];
            $revisionState = $this->getProviderRevisionState($userId, $tripId);
            if (!$revisionState) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Trip not found or unauthorized']);
                return;
            }

            $tripStatus = $revisionState['tripStatus'] ?? null;
            $rejectedDriverCount = (int)($revisionState['rejectedDriverCount'] ?? 0);
            $rejectedGuideCount = (int)($revisionState['rejectedGuideCount'] ?? 0);

            if ($tripStatus !== 'pending') {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => $this->getTripLockedMessage()]);
                return;
            }

            if ($rejectedDriverCount > 0 && $rejectedGuideCount === 0) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'Only rejected driver requests can be changed at this stage.']);
                return;
            }

            // Add userId to the data
            $input['userId'] = $userId;

            try {
                $saveResult = $this->regUserModel->saveGuideRequest($input);
                if (!empty($saveResult['success'])) {
                    error_log("saveGuideRequest success");
                    $response = [
                        'success' => true,
                        'message' => $saveResult['message'] ?? 'Guide request saved successfully'
                    ];

                    if (array_key_exists('status', $saveResult)) {
                        $response['status'] = $saveResult['status'];
                    }
                    if (array_key_exists('chargeType', $saveResult)) {
                        $response['chargeType'] = $saveResult['chargeType'];
                    }
                    if (array_key_exists('numberOfPeople', $saveResult)) {
                        $response['numberOfPeople'] = $saveResult['numberOfPeople'];
                    }
                    if (array_key_exists('totalCharge', $saveResult)) {
                        $response['totalCharge'] = $saveResult['totalCharge'];
                    }

                    echo json_encode([
                        'success' => $response['success'],
                        'message' => $response['message'],
                        'status' => $response['status'] ?? null,
                        'chargeType' => $response['chargeType'] ?? null,
                        'numberOfPeople' => $response['numberOfPeople'] ?? null,
                        'totalCharge' => $response['totalCharge'] ?? null
                    ]);
                } else {
                    error_log("saveGuideRequest failed - model returned false");
                    echo json_encode([
                        'success' => false,
                        'message' => $saveResult['message'] ?? 'Failed to save guide request'
                    ]);
                }
            } catch (PDOException $e) {
                error_log("saveGuideRequest error: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
        }

        public function getGuideRequestByEventId($eventId) {
            header('Content-Type: application/json');

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Please log in']);
                return;
            }
            
            try {
                $guideRequest = $this->regUserModel->getGuideRequestByEventId($eventId, (int)$userId);
                
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

            if (!$this->canModifyTripEvents($userId, (int)$input['tripId'])) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => $this->getEventLockedMessage()]);
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

            if (!$this->canModifyTripEvents($userId, (int)$input['tripId'])) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => $this->getEventLockedMessage()]);
                return;
            }

            if (empty($input['eventType']) || empty($input['eventStatus'])) {
                echo json_encode(['success' => false, 'message' => 'Event type and status are required']);
                return;
            }

            $input['eventStatus'] = strtolower(trim((string)$input['eventStatus']));
            if (!in_array($input['eventStatus'], ['start', 'intermediate', 'end'], true)) {
                echo json_encode(['success' => false, 'message' => 'Invalid event status']);
                return;
            }

            if($input['eventType'] === 'location'){
                $location_required_fields = ['locationName','latitude','longitude','description'];
                foreach($location_required_fields as $field) {
                    if(empty($input[$field])){
                        echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required for location visit events']);
                        return;
                    }
                }
            } elseif($input['eventType'] === 'travelSpot'){
                if(empty($input['travelSpotId'])){
                    echo json_encode(['success' => false, 'message' => 'Spot ID is required for travel spot visit events']);
                    return;
                }
            }

            $normalizedTimes = $this->normalizeEventTimes(
                $input['eventStatus'],
                $input['startTime'] ?? null,
                $input['endTime'] ?? null
            );

            if (empty($normalizedTimes['success'])) {
                echo json_encode(['success' => false, 'message' => $normalizedTimes['message']]);
                return;
            }

            $input['startTime'] = $normalizedTimes['startTime'];
            $input['endTime'] = $normalizedTimes['endTime'];
            $input['tripId'] = (int)$input['tripId'];

            $updatingData = $input;
            $updatingData['eventId'] = $eventId;
            $updatingData['userId'] = $userId;

            try{
                $statusCheck = $this->validateBoundaryEventUniqueness($userId, $input['tripId'], $input['eventStatus'], $eventId);
                if (empty($statusCheck['success'])) {
                    http_response_code(409);
                    echo json_encode(['success' => false, 'message' => $statusCheck['message']]);
                    return;
                }

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

            $userId = getSession('user_id');
            if (!$userId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'User not logged in'
                ]);
                return;
            }
            
            $tripId = (int) $tripId;
            $drivers = $this->regUserModel->getDriverRequestsForTrip($userId, $tripId);
            $tripStatus = $this->regUserModel->getTripStatusForUser($userId, $tripId);

            if ($tripStatus === null) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Trip not found or unauthorized'
                ]);
                return;
            }
            
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
                'drivers' => $driversWithSegments,
                'tripStatus' => $tripStatus
            ]);
        }

        public function saveDriverRequests($tripId) {
            header('Content-Type: application/json');

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                return;
            }

            $userId = getSession('user_id');
            $input = json_decode(file_get_contents('php://input'), true);
            $selectedDrivers = $input['selectedDrivers'] ?? [];

            if (!$this->canModifyTripAssignments($userId, $tripId)) {
                http_response_code(409);
                echo json_encode([
                    'success' => false,
                    'message' => $this->getTripLockedMessage()
                ]);
                return;
            }

            $saveResult = $this->regUserModel->saveDriverRequests($userId, (int)$tripId, $selectedDrivers);

            if (!empty($saveResult['success'])) {
                echo json_encode([
                    'success' => true,
                    'message' => $saveResult['message'] ?? 'Driver requests saved successfully'
                ]);
                return;
            }

            echo json_encode([
                'success' => false,
                'message' => $saveResult['message'] ?? 'Failed to save driver requests'
            ]);
        }

        public function confirmTrip($tripId){

            header('Content-Type: application/json');

            $userId = getSession('user_id');

            try{
                $input = json_decode(file_get_contents('php://input'), true);
                $selectedDrivers = $input['selectedDrivers'] ?? [];

                if (!$this->canModifyTripAssignments($userId, $tripId)) {
                    echo json_encode([
                        'success' => false,
                        'message' => $this->getTripLockedMessage()
                    ]);
                    return;
                }

                // Save all selected drivers first while trip is still pending.
                $requestsResult = $this->regUserModel->saveDriverRequests($userId, $tripId, $selectedDrivers);
                if (empty($requestsResult['success'])) {
                    echo json_encode([
                        'success' => false,
                        'message' => $requestsResult['message'] ?? 'Failed to save driver requests before confirmation'
                    ]);
                    return;
                }

                if (!$this->canConfirmTripNow($userId, $tripId)) {
                    http_response_code(409);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Trip cannot be confirmed while rejected driver or guide requests exist. Please replace rejected requests first.'
                    ]);
                    return;
                }

                $result = $this->regUserModel->confirmTrip($userId, $tripId);

                if (!empty($result['success'])) {
                    $status = $result['status'] ?? 'wConfirmation';
                    $message = $status === 'awPayment'
                        ? 'Trip confirmed and all providers accepted. Trip is now awaiting payment.'
                        : ($status === 'scheduled'
                            ? 'Trip payment completed. Trip is now scheduled.'
                            : 'Trip confirmed. Waiting for driver and guide confirmations.');

                    echo json_encode([
                        'success' => true,
                        'message' => $message,
                        'status' => $status
                    ]);
                } else {
                    $status = $result['status'] ?? null;
                    $message = $result['message'] ?? (($status && $status !== 'pending')
                        ? $this->getTripLockedMessage()
                        : 'Failed to confirm trip');

                    echo json_encode([
                        'success' => false,
                        'message' => $message,
                        'status' => $status
                    ]);
                }

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when confirming trip: '.$e->getMessage()]);
                return;
            }
        }

        public function initiateTripPayment($tripId) {
            header('Content-Type: application/json');

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                return;
            }

            $userId = getSession('user_id');
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Please log in']);
                return;
            }

            $tripId = (int)$tripId;
            $summary = $this->regUserModel->getTripPaymentSummary((int)$userId, $tripId);

            if (empty($summary['success'])) {
                http_response_code(409);
                echo json_encode([
                    'success' => false,
                    'message' => $summary['message'] ?? 'Unable to prepare trip payment'
                ]);
                return;
            }

            if (($summary['tripStatus'] ?? null) !== 'awPayment') {
                $message = ($summary['tripStatus'] ?? null) === 'scheduled'
                    ? 'This trip is already scheduled and payment is complete.'
                    : 'Trip is not ready for payment yet.';

                http_response_code(409);
                echo json_encode([
                    'success' => false,
                    'message' => $message,
                    'status' => $summary['tripStatus'] ?? null
                ]);
                return;
            }

            if (($summary['paymentStatus'] ?? null) === 'completed') {
                http_response_code(409);
                echo json_encode([
                    'success' => false,
                    'message' => 'Payment has already been completed for this trip.'
                ]);
                return;
            }

            $config = $this->getPayHereConfig();
            $amount = number_format((float)($summary['totalCharge'] ?? 0), 2, '.', '');
            $wholePaymentId = (int)($summary['wholePaymentId'] ?? 0);
            $orderId = 'TRIPPAY_' . $tripId . '_' . $wholePaymentId . '_' . time();

            if ((float)$amount <= 0) {
                http_response_code(409);
                echo json_encode([
                    'success' => false,
                    'message' => 'Payable amount is invalid for this trip.'
                ]);
                return;
            }

            $traveller = $summary['traveller'] ?? [];
            $fullName = trim((string)($traveller['fullName'] ?? 'Traveller User'));
            $nameParts = preg_split('/\s+/', $fullName, 2);
            $firstName = $nameParts[0] ?? 'Traveller';
            $lastName = $nameParts[1] ?? 'User';
            $email = trim((string)($traveller['email'] ?? 'traveller@example.com'));
            $phone = trim((string)($traveller['phone'] ?? '0700000000'));
            $address = trim((string)($traveller['address'] ?? 'Sri Lanka'));

            $hash = $this->buildPayHereCheckoutHash(
                $config['merchantId'],
                $config['merchantSecret'],
                $orderId,
                $amount,
                $config['currency']
            );

            echo json_encode([
                'success' => true,
                'checkoutUrl' => $config['checkoutUrl'],
                'formFields' => [
                    'merchant_id' => (string)$config['merchantId'],
                    'return_url' => URL_ROOT . '/RegUser/tripPaymentReturn/' . $tripId,
                    'cancel_url' => URL_ROOT . '/RegUser/tripPaymentCancel/' . $tripId,
                    'notify_url' => URL_ROOT . '/RegUser/tripPaymentNotify',
                    'order_id' => $orderId,
                    'items' => 'Trip Payment #' . $tripId,
                    'currency' => (string)$config['currency'],
                    'amount' => $amount,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'phone' => $phone,
                    'address' => $address,
                    'city' => 'Colombo',
                    'country' => 'Sri Lanka',
                    'hash' => $hash
                ],
                'payment' => [
                    'tripId' => $tripId,
                    'wholePaymentId' => $wholePaymentId,
                    'totalCharge' => (float)($summary['totalCharge'] ?? 0),
                    'paymentStatus' => $summary['paymentStatus'] ?? 'pending'
                ]
            ]);
        }

        public function tripPaymentNotify() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo 'Method Not Allowed';
                return;
            }

            $result = $this->processTripPaymentGatewayPayload($_POST, false, null);
            if (empty($result['success'])) {
                http_response_code((int)($result['httpCode'] ?? 400));
                echo (string)($result['message'] ?? 'PAYMENT CALLBACK FAILED');
                return;
            }

            http_response_code(200);
            echo 'OK';
        }

        public function tripPaymentReturn($tripId) {
            $tripId = (int)$tripId;

            $orderId = isset($_GET['order_id']) ? (string)$_GET['order_id'] : '';
            $orderRef = $this->parseTripPaymentOrderId($orderId);
            if ($orderRef && (int)$orderRef['tripId'] === $tripId) {
                $result = $this->processTripPaymentGatewayPayload($_GET, true, getSession('user_id'));
                if (empty($result['success'])) {
                    error_log('tripPaymentReturn fallback skipped for trip ' . $tripId . ': ' . (string)($result['message'] ?? 'Unknown reason'));
                }
            }

            header('Location: ' . URL_ROOT . '/RegUser/tripEventList/' . $tripId . '?paymentState=return&paymentTripId=' . $tripId);
            exit;
        }

        public function tripPaymentCancel($tripId) {
            $tripId = (int)$tripId;
            $orderId = isset($_GET['order_id']) ? (string)$_GET['order_id'] : '';
            $orderRef = $this->parseTripPaymentOrderId($orderId);

            if ($orderRef && (int)$orderRef['tripId'] === $tripId) {
                $this->regUserModel->cancelPendingTripPayment(
                    (int)$orderRef['tripId'],
                    (int)$orderRef['wholePaymentId']
                );
            }

            header('Location: ' . URL_ROOT . '/RegUser/tripEventList/' . $tripId . '?paymentState=cancel&paymentTripId=' . $tripId);
            exit;
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

            $userId = getSession('user_id');
            $tripId = isset($_GET['tripId']) ? (int)$_GET['tripId'] : null;

            if ($tripId && !$this->canModifyTripAssignments($userId, $tripId)) {
                echo "<script>alert('" . $this->getTripLockedMessage() . "'); window.close();</script>";
                return;
            }
            
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

            $userId = getSession('user_id');
            $tripId = isset($_GET['tripId']) ? (int)$_GET['tripId'] : null;

            if ($tripId && !$this->canModifyTripAssignments($userId, $tripId)) {
                http_response_code(409);
                echo json_encode([
                    'success' => false,
                    'message' => $this->getTripLockedMessage()
                ]);
                return;
            }
            
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

            $userId = getSession('user_id');
            $tripId = isset($_GET['tripId']) ? (int)$_GET['tripId'] : null;

            if ($tripId && !$this->canModifyTripAssignments($userId, $tripId)) {
                http_response_code(409);
                echo json_encode([
                    'success' => false,
                    'message' => $this->getTripLockedMessage()
                ]);
                return;
            }
            
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

            $userId = getSession('user_id');
            if (!$this->canModifyTripAssignments($userId, $tripId)) {
                echo "<script>alert('" . $this->getTripLockedMessage() . "'); window.close();</script>";
                return;
            }
            
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

            $userId = getSession('user_id');
            if (!$this->canModifyTripAssignments($userId, $tripId)) {
                http_response_code(409);
                echo json_encode([
                    'success' => false,
                    'message' => $this->getTripLockedMessage()
                ]);
                return;
            }
            
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

            $userId = getSession('user_id');
            if (!$this->canModifyTripAssignments($userId, $tripId)) {
                http_response_code(409);
                echo json_encode([
                    'success' => false,
                    'message' => $this->getTripLockedMessage()
                ]);
                return;
            }
            
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

                $this->regUserModel->moveTripToPendingIfRejected((int)$userId, (int)$tripId);
                $this->regUserModel->promoteTripToScheduledIfReady((int)$userId, (int)$tripId);
                
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

                $revisionState = $this->getProviderRevisionState($userId, $tripId);
                $revisionMode = !empty($revisionState['revisionMode']);
                $rejectedDriverCount = (int)($revisionState['rejectedDriverCount'] ?? 0);
                $rejectedGuideCount = (int)($revisionState['rejectedGuideCount'] ?? 0);
                $eventChangesLocked = ($trip->status !== 'pending') || $revisionMode;

                $paymentSummary = null;
                if (in_array((string)$trip->status, ['awPayment', 'scheduled'], true)) {
                    $summaryResult = $this->regUserModel->getTripPaymentSummary((int)$userId, (int)$tripId);
                    if (!empty($summaryResult['success'])) {
                        $paymentSummary = [
                            'tripStatus' => (string)($summaryResult['tripStatus'] ?? $trip->status),
                            'paymentStatus' => (string)($summaryResult['paymentStatus'] ?? 'pending'),
                            'driverChargeTotal' => (float)($summaryResult['driverChargeTotal'] ?? 0),
                            'guideChargeTotal' => (float)($summaryResult['guideChargeTotal'] ?? 0),
                            'providerChargeTotal' => (float)($summaryResult['providerChargeTotal'] ?? 0),
                            'siteChargeTotal' => (float)($summaryResult['siteChargeTotal'] ?? 0),
                            'totalCharge' => (float)($summaryResult['totalCharge'] ?? 0),
                            'driverBookingCount' => (int)($summaryResult['driverBookingCount'] ?? 0),
                            'guideBookingCount' => (int)($summaryResult['guideBookingCount'] ?? 0),
                            'driverBookingUnitCharge' => (float)($summaryResult['driverBookingUnitCharge'] ?? 0),
                            'guideBookingUnitCharge' => (float)($summaryResult['guideBookingUnitCharge'] ?? 0),
                            'driverBookingSiteCharge' => (float)($summaryResult['driverBookingSiteCharge'] ?? 0),
                            'guideBookingSiteCharge' => (float)($summaryResult['guideBookingSiteCharge'] ?? 0),
                            'serviceChargeRate' => (float)($summaryResult['serviceChargeRate'] ?? 0),
                            'serviceChargeAmount' => (float)($summaryResult['serviceChargeAmount'] ?? 0)
                        ];
                    }
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
                        'status' => $trip->status,
                        'revisionMode' => $revisionMode,
                        'rejectedDriverCount' => $rejectedDriverCount,
                        'rejectedGuideCount' => $rejectedGuideCount,
                        'eventChangesLocked' => $eventChangesLocked,
                        'paymentSummary' => $paymentSummary
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
            $rating = $input['rating'] ?? null;

            if (!$driverId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Driver ID is required']);
                return;
            }

            // At least review text or rating must be provided
            if (empty($reviewText) && !$rating) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Review text or rating is required']);
                return;
            }

            try {
                $profileControllerModel = $this->model('ProfileControllerModel');
                $result = $profileControllerModel->submitReview($userId, $driverId, $reviewText, $rating);

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

        public function getUserProblems() {
            header('Content-Type: application/json');

            $filter = isset($_GET['filter']) ? trim((string)$_GET['filter']) : 'all';

            try {
                $problems = $this->regUserModel->getUserProblems($filter);
                echo json_encode([
                    'success' => true,
                    'problems' => $problems
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred']);
            }
        }

    }





