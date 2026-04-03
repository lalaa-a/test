<?php

    class Moderator extends Controller{

        private $moderatorModel;
        public function __construct(){
            $this->moderatorModel = $this->model("ModeratorModel");
        }

        public function dashboard(){
            $this->view('UserTemplates/moderatorDash');
        }
        
        public function verification(){
            ob_start();
            $this->view('Moderator/verification/verification');
            $html =  ob_get_clean();

            $loadingContent = [
                'html' => $html,
                'css'  => URL_ROOT.'/public/css/moderator/verification/verification.css',
                'js'   => URL_ROOT.'/public/js/moderator/verification/verification.js'
            ];

            $unEncodedResponse = [
                'tabId' => 'verification',
                'loadingContent' => $loadingContent
            ];

            $this->view('UserTemplates/moderatorDash',$unEncodedResponse);
        }

        public function subtabAccountVerification(){
            ob_start();
            $this->view('Moderator/verification/subtabAccountVerification');
            $html = ob_get_clean();

            $loadingContent = [
                'html' => $html,
                'css'  => URL_ROOT.'/public/css/moderator/verification/subtabAccountVerification.css',
                'js'   => URL_ROOT.'/public/js/moderator/verification/subtabAccountVerification.js'
            ];

            $unEncodedResponse = [
                'ok' => true,
                'loadingContent'=>$loadingContent
            ];

            echo json_encode($unEncodedResponse);
        }

        public function subtabTLicenseVerification(){
            ob_start();
            $this->view('Moderator/verification/subtabTLicenseVerification');
            $html = ob_get_clean();

            $loadingContent = [
                'html' => $html,
                'css'  => URL_ROOT.'/public/css/moderator/verification/subtabTLicenseVerification.css',
                'js'   => URL_ROOT.'/public/js/moderator/verification/subtabTLicenseVerification.js'
            ];

            $unEncodedResponse = [
                'ok' => true,
                'loadingContent'=>$loadingContent
            ];

            echo json_encode($unEncodedResponse);
        }

        public function subtabVehicleVerification(){
            ob_start();
            $this->view('Moderator/verification/subtabVehicleVerification');
            $html = ob_get_clean();

            $loadingContent = [
                'html' => $html,
                'css'  => URL_ROOT.'/public/css/moderator/verification/subtabVehicleVerification.css',
                'js'   => URL_ROOT.'/public/js/moderator/verification/subtabVehicleVerification.js'
            ];

            $unEncodedResponse = [
                'ok' => true,
                'loadingContent'=>$loadingContent
            ];

            echo json_encode($unEncodedResponse);
        }

        // Account Verification Methods
        public function getAccounts($status) {
            header('Content-Type: application/json');

            // Log to PHP error log
            error_log("get_accounts called");

            $status = $status ?? 'pending';

            // Map frontend status to database status
            $statusMap = [
                'pending' => 'pending',
                'verified' => 'approved',
                'rejected' => 'rejected'
            ];

            $dbStatus = $statusMap[$status] ?? 'pending';

            if (!in_array($status, ['pending', 'verified', 'rejected'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid status parameter']);
                return;
            }

            try {
                $accounts = $this->moderatorModel->getAccountsByStatus($dbStatus);
                error_log("get_accounts: status=$status, dbStatus=$dbStatus, found " . count($accounts) . " accounts");
                echo json_encode([
                    'success' => true,
                    'accounts' => $accounts
                ]);

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching accounts: ' . $e->getMessage()]);
            }
        }

        public function getUserDetails($userId) {
            header('Content-Type: application/json');

            // Log to PHP error log
            error_log("getUserDetails called for userId: " . $userId);

            if (!$userId || !is_numeric($userId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
                return;
            }

            try {
                $user = $this->moderatorModel->getUserDetailsForVerification($userId);

                if (!$user) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'User not found']);
                    return;
                }

                echo json_encode([
                    'success' => true,
                    'user' => $user
                ]);

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching user details: ' . $e->getMessage()]);
            }
        }

        public function verifyAccount($userId) {
            header('Content-Type: application/json');

            // Log to PHP error log
            error_log("verifyAccount called for userId: " . $userId);

            if (!$userId || !is_numeric($userId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
                return;
            }

            // Get current user (moderator) ID from session
            $moderatorId = getSession('user_id');

            if (!$moderatorId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            try {
                $result = $this->moderatorModel->verifyAccount($userId, $moderatorId);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Account verified successfully']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Failed to verify account']);
                }

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when verifying account: ' . $e->getMessage()]);
            }
        }

        public function rejectAccount($userId) {
            header('Content-Type: application/json');

            // Log to PHP error log
            error_log("rejectAccount called for userId: " . $userId);

            if (!$userId || !is_numeric($userId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
                return;
            }

            // Get rejection reason from POST data
            $input = json_decode(file_get_contents('php://input'), true);
            $reason = $input['reason'] ?? '';

            if (empty($reason)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Rejection reason is required']);
                return;
            }

            // Get current user (moderator) ID from session
            $moderatorId = getSession('user_id');

            if (!$moderatorId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            try {
                $result = $this->moderatorModel->rejectAccount($userId, $moderatorId, $reason);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Account rejected']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Failed to reject account']);
                }

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when rejecting account: ' . $e->getMessage()]);
            }
        }

        public function revokeVerification($userId) {
            header('Content-Type: application/json');

            // Log to PHP error log
            error_log("revokeVerification called for userId: " . $userId);
            
            if (!$userId || !is_numeric($userId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
                return;
            }

            // Get current user (moderator) ID from session
            $moderatorId = getSession('user_id');

            if (!$moderatorId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            try {
                $result = $this->moderatorModel->revokeVerification($userId, $moderatorId);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Verification revoked successfully']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Failed to revoke verification']);
                }

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when revoking verification: ' . $e->getMessage()]);
            }
        }

        public function revokeRejection($userId) {
            header('Content-Type: application/json');

            // Log to PHP error log
            error_log("revokeRejection called for userId: " . $userId);
            
            if (!$userId || !is_numeric($userId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
                return;
            }

            // Get current user (moderator) ID from session
            $moderatorId = getSession('user_id');

            if (!$moderatorId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            try {
                $result = $this->moderatorModel->revokeRejection($userId, $moderatorId);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Rejection revoked successfully']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Failed to revoke rejection']);
                }

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when revoking rejection: ' . $e->getMessage()]);
            }
        }

        // Tourist License Verification Methods
        public function getPendingLicenses() {
            header('Content-Type: application/json');

            // Log to PHP error log
            error_log("getPendingLicenses called");

            try {
                $licenses = $this->moderatorModel->getPendingLicenses();
                error_log("getPendingLicenses: found " . count($licenses) . " pending licenses");
                echo json_encode([
                    'success' => true,
                    'licenses' => $licenses
                ]);

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching pending licenses: ' . $e->getMessage()]);
            }
        }

        public function getVerifiedLicenses() {
            header('Content-Type: application/json');

            // Log to PHP error log
            error_log("getVerifiedLicenses called");

            try {
                $licenses = $this->moderatorModel->getVerifiedLicenses();
                error_log("getVerifiedLicenses: found " . count($licenses) . " verified licenses");
                echo json_encode([
                    'success' => true,
                    'licenses' => $licenses
                ]);

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching verified licenses: ' . $e->getMessage()]);
            }
        }

        public function getRejectedLicenses() {
            header('Content-Type: application/json');

            // Log to PHP error log
            error_log("getRejectedLicenses called");

            try {
                $licenses = $this->moderatorModel->getRejectedLicenses();
                error_log("getRejectedLicenses: found " . count($licenses) . " rejected licenses");
                echo json_encode([
                    'success' => true,
                    'licenses' => $licenses
                ]);

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching rejected licenses: ' . $e->getMessage()]);
            }
        }

        public function getLicenseDetails($userId) {
            header('Content-Type: application/json');

            // Log to PHP error log
            error_log("getLicenseDetails called for userId: " . $userId);

            $userId = (int) $userId;

            if (!$userId || !is_numeric($userId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
                return;
            }

            try {
                $license = $this->moderatorModel->getLicenseDetails($userId);

                if (!$license) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'License not found']);
                    return;
                }

                echo json_encode([
                    'success' => true,
                    'license' => $license
                ]);

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching license details: ' . $e->getMessage()]);
            }
        }

        public function verifyLicense($userId) {
            header('Content-Type: application/json');

            // Log to PHP error log
            error_log("verifyLicense called for userId: " . $userId);

            if (!$userId || !is_numeric($userId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
                return;
            }

            // Get current user (moderator) ID from session
            $moderatorId = getSession('user_id');

            if (!$moderatorId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            try {
                $result = $this->moderatorModel->verifyLicense($userId, $moderatorId);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'License verified successfully']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Failed to verify license']);
                }

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when verifying license: ' . $e->getMessage()]);
            }
        }

        public function rejectLicense($userId) {
            header('Content-Type: application/json');

            // Log to PHP error log
            error_log("rejectLicense called for userId: " . $userId);

            if (!$userId || !is_numeric($userId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
                return;
            }

            // Get rejection reason from POST data
            $input = json_decode(file_get_contents('php://input'), true);
            $reason = $input['reason'] ?? '';

            if (empty($reason)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Rejection reason is required']);
                return;
            }

            // Get current user (moderator) ID from session
            $moderatorId = getSession('user_id');

            if (!$moderatorId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            try {
                $result = $this->moderatorModel->rejectLicense($userId, $moderatorId, $reason);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'License rejected']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Failed to reject license']);
                }

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when rejecting license: ' . $e->getMessage()]);
            }
        }

        public function revokeLicenseVerification($userId) {
            header('Content-Type: application/json');

            // Log to PHP error log
            error_log("revokeLicenseVerification called for userId: " . $userId);
            
            if (!$userId || !is_numeric($userId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
                return;
            }

            // Get current user (moderator) ID from session
            $moderatorId = getSession('user_id');

            if (!$moderatorId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            try {
                $result = $this->moderatorModel->revokeLicenseVerification($userId, $moderatorId);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'License verification revoked successfully']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Failed to revoke license verification']);
                }

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when revoking license verification: ' . $e->getMessage()]);
            }
        }

        public function revokeLicenseRejection($userId) {
            header('Content-Type: application/json');

            // Log to PHP error log
            error_log("revokeLicenseRejection called for userId: " . $userId);
            
            if (!$userId || !is_numeric($userId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
                return;
            }

            // Get current user (moderator) ID from session
            $moderatorId = getSession('user_id');

            if (!$moderatorId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            try {
                $result = $this->moderatorModel->revokeLicenseRejection($userId, $moderatorId);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'License rejection revoked successfully']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Failed to revoke license rejection']);
                }

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when revoking license rejection: ' . $e->getMessage()]);
            }
        }

        // Vehicle Verification Methods
        public function getPendingVehicles() {
            header('Content-Type: application/json');

            // Log to PHP error log
            error_log("getPendingVehicles called");

            try {
                $vehicles = $this->moderatorModel->getPendingVehicles();
                error_log("getPendingVehicles: found " . count($vehicles) . " pending vehicles");
                echo json_encode([
                    'success' => true,
                    'vehicles' => $vehicles
                ]);

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching pending vehicles: ' . $e->getMessage()]);
            }
        }

        public function getVerifiedVehicles() {
            header('Content-Type: application/json');

            // Log to PHP error log
            error_log("getVerifiedVehicles called");

            try {
                $vehicles = $this->moderatorModel->getVerifiedVehicles();
                error_log("getVerifiedVehicles: found " . count($vehicles) . " verified vehicles");
                echo json_encode([
                    'success' => true,
                    'vehicles' => $vehicles
                ]);

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching verified vehicles: ' . $e->getMessage()]);
            }
        }

        public function getRejectedVehicles() {
            header('Content-Type: application/json');

            // Log to PHP error log
            error_log("getRejectedVehicles called");

            try {
                $vehicles = $this->moderatorModel->getRejectedVehicles();
                error_log("getRejectedVehicles: found " . count($vehicles) . " rejected vehicles");
                echo json_encode([
                    'success' => true,
                    'vehicles' => $vehicles
                ]);

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching rejected vehicles: ' . $e->getMessage()]);
            }
        }

        public function verifyVehicle($vehicleId) {
            header('Content-Type: application/json');

            // Log to PHP error log
            error_log("verifyVehicle called for vehicleId: " . $vehicleId);

            if (!$vehicleId || !is_numeric($vehicleId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid vehicle ID']);
                return;
            }

            // Get current user (moderator) ID from session
            $moderatorId = getSession('user_id');

            if (!$moderatorId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            try {
                $result = $this->moderatorModel->verifyVehicle($vehicleId, $moderatorId);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Vehicle verified successfully']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Failed to verify vehicle']);
                }

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when verifying vehicle: ' . $e->getMessage()]);
            }
        }

        public function rejectVehicle($vehicleId) {
            header('Content-Type: application/json');

            // Log to PHP error log
            error_log("rejectVehicle called for vehicleId: " . $vehicleId);

            if (!$vehicleId || !is_numeric($vehicleId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid vehicle ID']);
                return;
            }

            // Get rejection reason from POST data
            $input = json_decode(file_get_contents('php://input'), true);
            $reason = $input['reason'] ?? '';

            if (empty($reason)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Rejection reason is required']);
                return;
            }

            // Get current user (moderator) ID from session
            $moderatorId = getSession('user_id');

            if (!$moderatorId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            try {
                $result = $this->moderatorModel->rejectVehicle($vehicleId, $moderatorId, $reason);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Vehicle rejected']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Failed to reject vehicle']);
                }

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when rejecting vehicle: ' . $e->getMessage()]);
            }
        }

        public function revokeVehicleVerification($vehicleId) {
            header('Content-Type: application/json');

            // Log to PHP error log
            error_log("revokeVehicleVerification called for vehicleId: " . $vehicleId);

            if (!$vehicleId || !is_numeric($vehicleId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid vehicle ID']);
                return;
            }

            // Get current user (moderator) ID from session
            $moderatorId = getSession('user_id');

            if (!$moderatorId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            try {
                $result = $this->moderatorModel->revokeVehicleVerification($vehicleId, $moderatorId);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Vehicle verification revoked successfully']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Failed to revoke vehicle verification']);
                }

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when revoking vehicle verification: ' . $e->getMessage()]);
            }
        }

        public function revokeVehicleRejection($vehicleId) {
            header('Content-Type: application/json');

            // Log to PHP error log
            error_log("revokeVehicleRejection called for vehicleId: " . $vehicleId);

            if (!$vehicleId || !is_numeric($vehicleId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid vehicle ID']);
                return;
            }

            // Get current user (moderator) ID from session
            $moderatorId = getSession('user_id');

            if (!$moderatorId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            try {
                $result = $this->moderatorModel->revokeVehicleRejection($vehicleId, $moderatorId);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Vehicle rejection revoked successfully']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Failed to revoke vehicle rejection']);
                }

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when revoking vehicle rejection: ' . $e->getMessage()]);
            }
        }

        public function getVehicleDetails($vehicleId) {
            header('Content-Type: application/json');

            // Log to PHP error log
            error_log("getVehicleDetails called for vehicleId: " . $vehicleId);

            if (!$vehicleId || !is_numeric($vehicleId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid vehicle ID']);
                return;
            }

            try {
                $vehicle = $this->moderatorModel->getVehicleDetails($vehicleId);

                if ($vehicle) {
                    echo json_encode(['success' => true, 'vehicle' => $vehicle]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Vehicle not found']);
                }

            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching vehicle details: ' . $e->getMessage()]);
            }
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

                // Support up to 10 photos, only photo1 is required
                $photos = ['photo1', 'photo2', 'photo3', 'photo4', 'photo5', 'photo6', 'photo7', 'photo8', 'photo9', 'photo10'];
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
                $uploadedPhotos = [];

                // Check that photo1 is required
                if (!isset($_FILES['photo1']) || $_FILES['photo1']['error'] === UPLOAD_ERR_NO_FILE) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Photo 1 (main photo) is required']);  
                    return;
                }

                // Validate all uploaded photos
                foreach ($photos as $photoKey) {
                    // Skip if photo not uploaded (only photo1 is required)
                    if (!isset($_FILES[$photoKey]) || $_FILES[$photoKey]['error'] === UPLOAD_ERR_NO_FILE) {
                        continue;
                    }

                    $file = $_FILES[$photoKey];

                    if ($file['size'] > 5 * 1024 * 1024) { // 5MB
                        echo json_encode(['success' => false, 'message' => 'File size exceeded 5MB for ' . $photoKey . '!']);  
                        return;
                    }

                    error_log(ini_get('post_max_size'));
                    error_log($file['size']);

                    error_log("photo names: " . print_r($file['tmp_name'], true));
                    
                    $mimeType = mime_content_type($file['tmp_name']);

                    if (!in_array($mimeType, $allowedTypes)) {
                        echo json_encode(['success' => false, 'message' => 'File type is not matching for ' . $photoKey . '!']);  
                        return;
                    }

                    // Store valid photo key for later processing
                    $uploadedPhotos[] = $photoKey;
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

                            // Upload only the photos that were actually provided
                            foreach($uploadedPhotos as $photoKey){
                                $extension = pathinfo($_FILES[$photoKey]['name'], PATHINFO_EXTENSION);
                                $newName = $photoKey . '_' . uniqid('spot_', true).'.'. $extension;
                                $uploadDir = ROOT_PATH.'/public/uploads/travelSpots/'.$insertedSpotId.'/'; // Directory path
                                $databasePath = '/travelSpots/'.$insertedSpotId.'/'.$newName; // Full path for database
                                $fullFilePath = $uploadDir . $newName; // Full file path for upload

                                //error_log("Received photo input: " . print_r($_FILES[$photoKey]['name'], true));
                                error_log('new name :'. print_r($newName,true) );

                                if (!is_dir($uploadDir)) {
                                    mkdir($uploadDir, 0755, true);
                                }
                                move_uploaded_file($_FILES[$photoKey]['tmp_name'], $fullFilePath);
                                
                                if($this->moderatorModel->addTravelSpotPhotos($insertedSpotId, $databasePath)){
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
                    echo json_encode(['success' => false, 'message' => 'Database error occurred'.$e->getMessage()]);
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
                echo json_encode(['success' => false, 'travelSpotData'=>NULL, 'message' => 'Database error occurred when getting travel Spot Data(not a card full travel spot)'.$e->getMessage()]);
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

                // Support up to 10 photos, all optional when editing
                $photos = ['photo1', 'photo2', 'photo3', 'photo4', 'photo5', 'photo6', 'photo7', 'photo8', 'photo9', 'photo10'];
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
                $uploadedPhotos = [];

                // Validate all uploaded photos (all optional when editing)
                foreach ($photos as $photoKey) {
                    // Skip if photo not uploaded
                    if (!isset($_FILES[$photoKey]) || $_FILES[$photoKey]['error'] === UPLOAD_ERR_NO_FILE) {
                        continue;
                    }

                    $file = $_FILES[$photoKey];

                    if ($file['size'] > 5 * 1024 * 1024) { // 5MB
                        echo json_encode(['success' => false, 'message' => 'File size exceeded 5MB for ' . $photoKey . '!']);  
                        return;
                    }

                    $mimeType = mime_content_type($file['tmp_name']);

                    if (!in_array($mimeType, $allowedTypes)) {
                        echo json_encode(['success' => false, 'message' => 'File type is not matching for ' . $photoKey . '!']);  
                        return;
                    }

                    // Store valid photo key for later processing
                    $uploadedPhotos[] = $photoKey;
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

                            // Only delete and re-upload if new photos are provided
                            if (count($uploadedPhotos) > 0) {
                                $this->moderatorModel->deleteTravelSpotPhotos($insertedSpotId);

                                foreach($uploadedPhotos as $photoKey){
                                    $extension = pathinfo($_FILES[$photoKey]['name'], PATHINFO_EXTENSION);
                                    $newName = $photoKey . '_' . uniqid('spot_', true) . '.' . $extension;
                                    $uploadDir = ROOT_PATH.'/public/uploads/travelSpots/'.$insertedSpotId.'/'; // Directory path
                                    $databasePath = '/travelSpots/'.$insertedSpotId.'/'.$newName; // Full path for database
                                    $fullFilePath = $uploadDir . $newName; // Full file path for upload

                                    error_log("Received photo input: " . print_r($_FILES[$photoKey]['name'], true));

                                    if (!is_dir($uploadDir)) {
                                        mkdir($uploadDir, 0755, true);
                                    }
                                    move_uploaded_file($_FILES[$photoKey]['tmp_name'], $fullFilePath);
                                    $this->moderatorModel->addTravelSpotPhotos($insertedSpotId, $databasePath);
                                }
                            } else {
                                error_log("No new photos uploaded, keeping existing photos.");
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