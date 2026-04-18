<?php
class Admin extends Controller
{
    private $userModel;
    private $adminModel;
    public function __construct()
    {
        $this->userModel = $this->model("UserModel");
        $this->adminModel = $this->model("AdminModel");
    }

    public function index()
    {
        header('Location: ' . URL_ROOT . '/admin/dashboard');
        exit;
    }

    public function dashboard()
    {
        ob_start();
        $this->view('Admin/dashboard');
        $html = ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/dashboard/dashboard.css',
            'js'   => URL_ROOT . '/public/js/admin/dashboard/dashboard.js'
        ];

        $unEncodedResponse = [
            'tabId' => 'dashboard',
            'loadingContent' => $loadingContent
        ];

        $this->view('UserTemplates/adminDash', $unEncodedResponse);
    }

    public function moderator()
    {
        ob_start();
        $this->view('Admin/Moderator/moderator');
        $html =  ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/moderator/moderator.css',
            'js'   => URL_ROOT . '/public/js/admin/moderator/moderator.js'
        ];

        $unEncodedResponse = [
            'tabId' => 'moderator',
            'loadingContent' => $loadingContent
        ];

        $this->view('UserTemplates/adminDash', $unEncodedResponse);
    }



    public function verification()
    {
        ob_start();
        $this->view('Admin/verification/verification');
        $html =  ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/verification/verification.css',
            'js'   => URL_ROOT . '/public/js/admin/verification/verification.js'
        ];

        $unEncodedResponse = [
            'tabId' => 'verification',
            'loadingContent' => $loadingContent
        ];

        $this->view('UserTemplates/adminDash', $unEncodedResponse);
    }

    public function subtabAccountVerification()
    {
        ob_start();
        $this->view('Admin/verification/subtabAccountVerification');
        $html = ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/verification/subtabAccountVerification.css',
            'js'   => URL_ROOT . '/public/js/admin/verification/subtabAccountVerification.js'
        ];

        $unEncodedResponse = [
            'ok' => true,
            'loadingContent' => $loadingContent
        ];

        echo json_encode($unEncodedResponse);
    }

    public function subtabTLicenseVerification()
    {
        ob_start();
        $this->view('Admin/verification/subtabTLicenseVerification');
        $html = ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/verification/subtabTLicenseVerification.css',
            'js'   => URL_ROOT . '/public/js/admin/verification/subtabTLicenseVerification.js'
        ];

        $unEncodedResponse = [
            'ok' => true,
            'loadingContent' => $loadingContent
        ];

        echo json_encode($unEncodedResponse);
    }

    public function subtabVehicleVerification()
    {
        ob_start();
        $this->view('Admin/verification/subtabVehicleVerification');
        $html = ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/verification/subtabVehicleVerification.css',
            'js'   => URL_ROOT . '/public/js/admin/verification/subtabVehicleVerification.js'
        ];

        $unEncodedResponse = [
            'ok' => true,
            'loadingContent' => $loadingContent
        ];

        echo json_encode($unEncodedResponse);
    }

    // Account Verification Methods
    public function getAccounts($status)
    {
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
            $accounts = $this->adminModel->getAccountsByStatus($dbStatus);
            error_log("get_accounts: status=$status, dbStatus=$dbStatus, found " . count($accounts) . " accounts");
            echo json_encode([
                'success' => true,
                'accounts' => $accounts
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching accounts: ' . $e->getMessage()]);
        }
    }

    public function getUserDetails($userId)
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("getUserDetails called for userId: " . $userId);

        if (!$userId || !is_numeric($userId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            return;
        }

        try {
            $user = $this->adminModel->getUserDetailsForVerification($userId);

            if (!$user) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'User not found']);
                return;
            }

            echo json_encode([
                'success' => true,
                'user' => $user
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching user details: ' . $e->getMessage()]);
        }
    }

    public function verifyAccount($userId)
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("verifyAccount called for userId: " . $userId);

        if (!$userId || !is_numeric($userId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            return;
        }

        // Get current user (admin) ID from session
        $adminId = getSession('user_id');

        if (!$adminId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            $result = $this->adminModel->verifyAccount($userId, $adminId);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Account verified successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Failed to verify account']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when verifying account: ' . $e->getMessage()]);
        }
    }

    public function rejectAccount($userId)
    {
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

        // Get current user (admin) ID from session
        $adminId = getSession('user_id');

        if (!$adminId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            $result = $this->adminModel->rejectAccount($userId, $adminId, $reason);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Account rejected']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Failed to reject account']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when rejecting account: ' . $e->getMessage()]);
        }
    }

    public function revokeVerification($userId)
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("revokeVerification called for userId: " . $userId);

        if (!$userId || !is_numeric($userId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            return;
        }

        // Get current user (admin) ID from session
        $adminId = getSession('user_id');

        if (!$adminId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            $result = $this->adminModel->revokeVerification($userId, $adminId);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Verification revoked successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Failed to revoke verification']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when revoking verification: ' . $e->getMessage()]);
        }
    }

    public function revokeRejection($userId)
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("revokeRejection called for userId: " . $userId);

        if (!$userId || !is_numeric($userId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            return;
        }

        // Get current user (admin) ID from session
        $adminId = getSession('user_id');

        if (!$adminId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            $result = $this->adminModel->revokeRejection($userId, $adminId);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Rejection revoked successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Failed to revoke rejection']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when revoking rejection: ' . $e->getMessage()]);
        }
    }

    // Tourist License Verification Methods
    public function getPendingLicenses()
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("getPendingLicenses called");

        try {
            $licenses = $this->adminModel->getPendingLicenses();
            error_log("getPendingLicenses: found " . count($licenses) . " pending licenses");
            echo json_encode([
                'success' => true,
                'licenses' => $licenses
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching pending licenses: ' . $e->getMessage()]);
        }
    }

    public function getVerifiedLicenses()
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("getVerifiedLicenses called");

        try {
            $licenses = $this->adminModel->getVerifiedLicenses();
            error_log("getVerifiedLicenses: found " . count($licenses) . " verified licenses");
            echo json_encode([
                'success' => true,
                'licenses' => $licenses
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching verified licenses: ' . $e->getMessage()]);
        }
    }

    public function getRejectedLicenses()
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("getRejectedLicenses called");

        try {
            $licenses = $this->adminModel->getRejectedLicenses();
            error_log("getRejectedLicenses: found " . count($licenses) . " rejected licenses");
            echo json_encode([
                'success' => true,
                'licenses' => $licenses
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching rejected licenses: ' . $e->getMessage()]);
        }
    }

    public function getLicenseDetails($userId)
    {
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
            $license = $this->adminModel->getLicenseDetails($userId);

            if (!$license) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'License not found']);
                return;
            }

            echo json_encode([
                'success' => true,
                'license' => $license
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching license details: ' . $e->getMessage()]);
        }
    }

    public function verifyLicense($userId)
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("verifyLicense called for userId: " . $userId);

        if (!$userId || !is_numeric($userId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            return;
        }

        // Get current user (admin) ID from session
        $adminId = getSession('user_id');

        if (!$adminId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            $result = $this->adminModel->verifyLicense($userId, $adminId);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'License verified successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Failed to verify license']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when verifying license: ' . $e->getMessage()]);
        }
    }

    public function rejectLicense($userId)
    {
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

        // Get current user (admin) ID from session
        $adminId = getSession('user_id');

        if (!$adminId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            $result = $this->adminModel->rejectLicense($userId, $adminId, $reason);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'License rejected']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Failed to reject license']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when rejecting license: ' . $e->getMessage()]);
        }
    }

    public function revokeLicenseVerification($userId)
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("revokeLicenseVerification called for userId: " . $userId);

        if (!$userId || !is_numeric($userId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            return;
        }

        // Get current user (admin) ID from session
        $adminId = getSession('user_id');

        if (!$adminId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            $result = $this->adminModel->revokeLicenseVerification($userId, $adminId);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'License verification revoked successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Failed to revoke license verification']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when revoking license verification: ' . $e->getMessage()]);
        }
    }

    public function revokeLicenseRejection($userId)
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("revokeLicenseRejection called for userId: " . $userId);

        if (!$userId || !is_numeric($userId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            return;
        }

        // Get current user (admin) ID from session
        $adminId = getSession('user_id');

        if (!$adminId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            $result = $this->adminModel->revokeLicenseRejection($userId, $adminId);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'License rejection revoked successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Failed to revoke license rejection']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when revoking license rejection: ' . $e->getMessage()]);
        }
    }

    // Vehicle Verification Methods
    public function getPendingVehicles()
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("getPendingVehicles called");

        try {
            $vehicles = $this->adminModel->getPendingVehicles();
            error_log("getPendingVehicles: found " . count($vehicles) . " pending vehicles");
            echo json_encode([
                'success' => true,
                'vehicles' => $vehicles
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching pending vehicles: ' . $e->getMessage()]);
        }
    }

    public function getVerifiedVehicles()
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("getVerifiedVehicles called");

        try {
            $vehicles = $this->adminModel->getVerifiedVehicles();
            error_log("getVerifiedVehicles: found " . count($vehicles) . " verified vehicles");
            echo json_encode([
                'success' => true,
                'vehicles' => $vehicles
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching verified vehicles: ' . $e->getMessage()]);
        }
    }

    public function getRejectedVehicles()
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("getRejectedVehicles called");

        try {
            $vehicles = $this->adminModel->getRejectedVehicles();
            error_log("getRejectedVehicles: found " . count($vehicles) . " rejected vehicles");
            echo json_encode([
                'success' => true,
                'vehicles' => $vehicles
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching rejected vehicles: ' . $e->getMessage()]);
        }
    }

    public function verifyVehicle($vehicleId)
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("verifyVehicle called for vehicleId: " . $vehicleId);

        if (!$vehicleId || !is_numeric($vehicleId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid vehicle ID']);
            return;
        }

        // Get current user (admin) ID from session
        $adminId = getSession('user_id');

        if (!$adminId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            $result = $this->adminModel->verifyVehicle($vehicleId, $adminId);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Vehicle verified successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Failed to verify vehicle']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when verifying vehicle: ' . $e->getMessage()]);
        }
    }

    public function rejectVehicle($vehicleId)
    {
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

        // Get current user (admin) ID from session
        $adminId = getSession('user_id');

        if (!$adminId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            $result = $this->adminModel->rejectVehicle($vehicleId, $adminId, $reason);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Vehicle rejected']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Failed to reject vehicle']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when rejecting vehicle: ' . $e->getMessage()]);
        }
    }

    public function revokeVehicleVerification($vehicleId)
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("revokeVehicleVerification called for vehicleId: " . $vehicleId);

        if (!$vehicleId || !is_numeric($vehicleId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid vehicle ID']);
            return;
        }

        // Get current user (admin) ID from session
        $adminId = getSession('user_id');

        if (!$adminId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            $result = $this->adminModel->revokeVehicleVerification($vehicleId, $adminId);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Vehicle verification revoked successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Failed to revoke vehicle verification']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when revoking vehicle verification: ' . $e->getMessage()]);
        }
    }

    public function revokeVehicleRejection($vehicleId)
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("revokeVehicleRejection called for vehicleId: " . $vehicleId);

        if (!$vehicleId || !is_numeric($vehicleId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid vehicle ID']);
            return;
        }

        // Get current user (admin) ID from session
        $adminId = getSession('user_id');

        if (!$adminId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            $result = $this->adminModel->revokeVehicleRejection($vehicleId, $adminId);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Vehicle rejection revoked successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Failed to revoke vehicle rejection']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when revoking vehicle rejection: ' . $e->getMessage()]);
        }
    }

    public function getVehicleDetails($vehicleId)
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("getVehicleDetails called for vehicleId: " . $vehicleId);

        if (!$vehicleId || !is_numeric($vehicleId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid vehicle ID']);
            return;
        }

        try {
            $vehicle = $this->adminModel->getVehicleDetails($vehicleId);

            if ($vehicle) {
                echo json_encode(['success' => true, 'vehicle' => $vehicle]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Vehicle not found']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching vehicle details: ' . $e->getMessage()]);
        }
    }

    public function mainFilterNameSubmit()
    {

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $input = json_decode(file_get_contents('php://input'), true);
            $adminId = getSession('user_id');

            if (!$input) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
                return;
            }

            $required_fields = ['mainFilterName'];
            foreach ($required_fields as $field) {
                if (empty($input[$field])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
                    return;
                }
            }

            $insertingData = [
                'adminId' => $adminId,
                'mainFilterName' => $input['mainFilterName']
            ];

            try {
                if ($this->adminModel->addMainFilterName($insertingData)) {
                    echo json_encode(['success' => true, 'message' => 'Added main filter name the database successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to add the main filter name to the database.']);
                }
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred']);
            }
        }
    }

    public function mainFilterEdit()
    {

        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == "PUT") {

            $input = json_decode(file_get_contents('php://input'), true);
            $adminId = getSession('user_id');

            if (!$adminId || empty($input['mainFilterId']) || empty($input['mainFilterName'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid request']);
                return;
            }

            // Validate ownership
            /*
                $existingTrip = $this->adminModel->getTripById($input['trip_id'], $userId);
                
                if (!$existingTrip) {
                    echo json_encode(['success' => false, 'message' => 'Trip not found']);
                    return;
                }
                    */
            // Update trip
            $updateData = [
                'mainFilterId' => $input['mainFilterId'],
                'mainFilterName' => $input['mainFilterName'],
                'adminId' => $adminId
            ];

            if ($this->adminModel->updateMainFilterName($updateData)) {
                echo json_encode(['success' => true, 'message' => 'Main Filter name updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Main Filter Failed to update']);
            }
        }
    }

    public function deleteMainFilter()
    {

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

        if ($this->adminModel->deleteMainFilterName($input['mainFilterId'])) {
            echo json_encode(['success' => true, 'message' => 'Main Filter deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete Main Filter']);
        }
    }

    public function subFilterNameSubmit()
    {

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $input = json_decode(file_get_contents('php://input'), true);
            $adminId = getSession('user_id');

            if (!$input) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid JSON data in subFilterNameSubmit']);
                return;
            }

            $required_fields = ['mainFilterId', 'subFilterName'];
            foreach ($required_fields as $field) {
                if (empty($input[$field])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required in subFilterNameSubmit']);
                    return;
                }
            }

            $insertingData = [
                'adminId' => $adminId,
                'mainFilterId' => $input['mainFilterId'],
                'subFilterName' => $input['subFilterName']
            ];

            try {
                if ($this->adminModel->addSubFilterName($insertingData)) {
                    echo json_encode(['success' => true, 'message' => 'Added sub filter name the database successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to add the sub filter name to the database.']);
                }
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred']);
            }
        }
    }

    public function subFilterEdit()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] == "PUT") {

            $input = json_decode(file_get_contents('php://input'), true);
            $adminId = getSession('user_id');

            if (!$adminId || empty($input['subFilterId']) || empty($input['subFilterName'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid request']);
                return;
            }

            // Validate ownership
            /*
                $existingTrip = $this->adminModel->getTripById($input['trip_id'], $userId);
                
                if (!$existingTrip) {
                    echo json_encode(['success' => false, 'message' => 'Trip not found']);
                    return;
                }
                    */
            // Update trip
            $updateData = [
                'subFilterId' => $input['subFilterId'],
                'subFilterName' => $input['subFilterName'],
                'adminId' => $adminId
            ];

            if ($this->adminModel->updateSubFilterName($updateData)) {
                echo json_encode(['success' => true, 'message' => 'SubFilter name updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'SubFilter Failed to update']);
            }
        }
    }

    public function deleteSubFilter()
    {

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

        if ($this->adminModel->deleteSubFilterName($input['subFilterId'])) {
            echo json_encode(['success' => true, 'message' => 'Sub Filter deleted successfully']);
            return;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete Sub Filter']);
            return;
        }
    }

    public function getAllFilters()
    {

        header('Content-Type: application/json');

        $allFilters = $this->adminModel->loadAllFilters();

        echo json_encode([
            'success' => true,
            'allFilters' => $allFilters
        ]);
        return;
    }

    public function getTravelSpotByName()
    {

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input['name']) {
                echo json_encode(['success' => false, 'travelSpots' => null, 'message' => 'no searching name']);
                return;
            } else {
                $allMatchingTravelSpots = $this->adminModel->searchTravelSpotByName($input['name']);
                echo json_encode([
                    'success' => true,
                    'travelSpots' => $allMatchingTravelSpots,
                    'message' => 'searched successfully'
                ]);
                return;
            }
        } else {
            echo json_encode([
                'success' => false,
                'travelSpots' => '',
                'message' => 'invalid method'
            ]);
            return;
        }
    }

    public function addTravelSpot()
    {

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $userId = getSession('user_id');

            // When sending FormData, data comes through $_POST, not php://input
            $input = $_POST;

            error_log("Recieved Size: " . print_r($_SERVER['CONTENT_LENGTH'], true));
            error_log("Received input: " . print_r($input, true));

            if (empty($input)) {
                echo json_encode(['success' => false, 'message' => 'No form data received']);
                return;
            }

            // For FormData, subFilters, nearbySpots, and itinerary are JSON strings that need to be decoded
            $input['subFilters'] = !empty($input['subFilters']) ? json_decode($input['subFilters'], true) : [];
            $input['nearbySpots'] = !empty($input['nearbySpots']) ? json_decode($input['nearbySpots'], true) : [];
            $input['itinerary'] = !empty($input['itinerary']) ? json_decode($input['itinerary'], true) : [];

            $required_fields =  [
                'spotName',
                'overview',
                'province',
                'district',
                'bestTimeFrom',
                'bestTimeTo',
                'visitingDurationMax',
                'ticketPriceLocal',
                'ticketPriceForeigner',
                'openingHours',
                'ticketDetails',
                'parkingDetails',
                'accessibility',
                'facilities',
                'travelerTips',
                'subFilters',
                'nearbySpots',
                'itinerary'
            ];

            foreach ($required_fields as $field) {
                if (empty($input[$field])) {
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

            try {
                $insertedSpotId = $this->adminModel->addTravelSpotsTable($spotsTableInsertingData);
                if ($insertedSpotId) {
                    try {
                        $subFilters = $input['subFilters'];
                        error_log("subFilters: " . json_encode($subFilters));
                        foreach ($subFilters as $subFilterId) {
                            $this->adminModel->addTravelSpotSubFilters($insertedSpotId, $subFilterId);
                        }

                        $nearbySpots = $input['nearbySpots'];
                        error_log("nearbySpots: " . json_encode($nearbySpots));
                        foreach ($nearbySpots as $nearbySpotId) {
                            $this->adminModel->addTravelSpotNearbySpots($insertedSpotId, $nearbySpotId);
                        }

                        $itinerary = $input['itinerary'];
                        error_log("itinerary: " . json_encode($itinerary));
                        foreach ($itinerary as $location) {
                            $this->adminModel->addTravelSpotItinerary($insertedSpotId, $location['name'], $location['lat'], $location['lng']);
                        }

                        // Upload only the photos that were actually provided
                        foreach ($uploadedPhotos as $photoKey) {
                            $extension = pathinfo($_FILES[$photoKey]['name'], PATHINFO_EXTENSION);
                            $newName = $photoKey . '_' . uniqid('spot_', true) . '.' . $extension;
                            $uploadDir = ROOT_PATH . '/public/uploads/travelSpots/' . $insertedSpotId . '/'; // Directory path
                            $databasePath = '/travelSpots/' . $insertedSpotId . '/' . $newName; // Full path for database
                            $fullFilePath = $uploadDir . $newName; // Full file path for upload

                            //error_log("Received photo input: " . print_r($_FILES[$photoKey]['name'], true));
                            error_log('new name :' . print_r($newName, true));

                            if (!is_dir($uploadDir)) {
                                mkdir($uploadDir, 0755, true);
                            }
                            move_uploaded_file($_FILES[$photoKey]['tmp_name'], $fullFilePath);

                            if ($this->adminModel->addTravelSpotPhotos($insertedSpotId, $databasePath)) {
                                error_log('travel spot photo added to the database successfully.');
                            };
                        }

                        $contributionData = [
                            'spotId' => $insertedSpotId,
                            'adminId' => $userId
                        ];
                        //Adding contributer who put up the data
                        $this->adminModel->addContributorData($contributionData);
                    } catch (PDOException $e) {
                        http_response_code(500);
                        echo json_encode(['success' => false, 'message' => "Database error occurred while travel spot data {$e->getMessage()}"]);
                        return;
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to add data to the travel_spots table.']);
                    return;
                }
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error occurred' . $e->getMessage()]);
                return;
            }

            // Success response 
            echo json_encode([
                'success' => true,
                'message' => 'Travel spot added successfully',
                'spotId' => $insertedSpotId
            ]);
            return;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'invalid method'
            ]);
            return;
        }
    }

    public function getTravelSpotCardData()
    {
        header('Content-Type: application/json');

        try {
            $travelSpotCardData = $this->adminModel->loadTravelSpotCardData();
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'travelSpotCardData' => NULL, 'message' => 'Database error occurred when getting travel Spot card Data' . $e->getMessage()]);
            return;
        }

        echo json_encode([
            'success' => true,
            'travelSpotCardData' => $travelSpotCardData,
            'message' => 'Travel Spot Card data loaded Successfully.'
        ]);
        return;
    }

    public function getTravelSpotData($travelSpotId)
    {

        header('Content-Type: application/json');

        error_log(" requested travel spot id : " . $travelSpotId);

        try {
            $travelSpotData = $this->adminModel->loadTravelSpotData($travelSpotId);
            if ($travelSpotData) {
                echo json_encode([
                    'success' => true,
                    'travelSpotData' => $travelSpotData,
                    'message' => 'Travel Spot data loaded Successfully.'
                ]);
                return;
            } else {
                echo json_encode([
                    'success' => false,
                    'travelSpotData' => NULL,
                    'message' => 'No Travel Spot data found for the given ID.'
                ]);
                return;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'travelSpotData' => NULL, 'message' => 'Database error occurred when getting travel Spot Data(not a card full travel spot)' . $e->getMessage()]);
            return;
        }
    }

    public function editTravelSpot()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $userId = getSession('user_id');
            $input = $_POST;

            error_log("Received input for editTravelSpot: " . print_r($input, true));

            if (empty($input)) {
                echo json_encode(['success' => false, 'message' => 'No form data received or spotId missing']);
                return;
            }

            // For FormData, subFilters, nearbySpots, and itinerary are JSON strings that need to be decoded
            $input['subFilters'] = !empty($input['subFilters']) ? json_decode($input['subFilters'], true) : [];
            $input['nearbySpots'] = !empty($input['nearbySpots']) ? json_decode($input['nearbySpots'], true) : [];
            $input['itinerary'] = !empty($input['itinerary']) ? json_decode($input['itinerary'], true) : [];

            $required_fields =  [
                'spotName',
                'overview',
                'province',
                'district',
                'bestTimeFrom',
                'bestTimeTo',
                'visitingDurationMax',
                'ticketPriceLocal',
                'ticketPriceForeigner',
                'openingHours',
                'ticketDetails',
                'parkingDetails',
                'accessibility',
                'facilities',
                'travelerTips',
                'subFilters',
                'nearbySpots',
                'itinerary'
            ];

            foreach ($required_fields as $field) {
                if (empty($input[$field])) {
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

            try {
                $insertedSpotId = $this->adminModel->updateTravelSpotsTable($spotsTableInsertingData);
                if ($insertedSpotId) {
                    try {
                        $subFilters = $input['subFilters'];
                        error_log("subFilters: " . json_encode($subFilters));

                        $this->adminModel->deleteTravelSpotSubFilters($insertedSpotId);
                        foreach ($subFilters as $subFilterId) {
                            $this->adminModel->addTravelSpotSubFilters($insertedSpotId, $subFilterId);
                        }

                        $nearbySpots = $input['nearbySpots'];
                        error_log("nearbySpots: " . json_encode($nearbySpots));
                        $this->adminModel->deleteTravelSpotNearbySpots($insertedSpotId);
                        foreach ($nearbySpots as $nearbySpotId) {
                            $this->adminModel->addTravelSpotNearbySpots($insertedSpotId, $nearbySpotId);
                        }

                        $itinerary = $input['itinerary'];
                        error_log("itinerary: " . json_encode($itinerary));

                        $this->adminModel->deleteTravelSpotItinerary($insertedSpotId);
                        foreach ($itinerary as $location) {
                            $this->adminModel->addTravelSpotItinerary($insertedSpotId, $location['name'], $location['lat'], $location['lng']);
                        }

                        // Only delete and re-upload if new photos are provided
                        if (count($uploadedPhotos) > 0) {
                            $this->adminModel->deleteTravelSpotPhotos($insertedSpotId);

                            foreach ($uploadedPhotos as $photoKey) {
                                $extension = pathinfo($_FILES[$photoKey]['name'], PATHINFO_EXTENSION);
                                $newName = $photoKey . '_' . uniqid('spot_', true) . '.' . $extension;
                                $uploadDir = ROOT_PATH . '/public/uploads/travelSpots/' . $insertedSpotId . '/'; // Directory path
                                $databasePath = '/travelSpots/' . $insertedSpotId . '/' . $newName; // Full path for database
                                $fullFilePath = $uploadDir . $newName; // Full file path for upload

                                error_log("Received photo input: " . print_r($_FILES[$photoKey]['name'], true));

                                if (!is_dir($uploadDir)) {
                                    mkdir($uploadDir, 0755, true);
                                }
                                move_uploaded_file($_FILES[$photoKey]['tmp_name'], $fullFilePath);
                                $this->adminModel->addTravelSpotPhotos($insertedSpotId, $databasePath);
                            }
                        } else {
                            error_log("No new photos uploaded, keeping existing photos.");
                        }

                        $contributionData = [
                            'spotId' => $insertedSpotId,
                            'moderatorId' => $userId
                        ];

                        //Updating contributor who put up the data
                        $this->adminModel->updateContributorData($contributionData);
                    } catch (PDOException $e) {
                        http_response_code(500);
                        echo json_encode(['success' => false, 'message' => "Database error occurred while editing travel spot data {$e->getMessage()}"]);
                        return;
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to add data to the travel_spots table.']);
                    return;
                }
            } catch (PDOException $e) {
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
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'invalid method '
            ]);
            return;
        }
    }

    public function deleteTravelSpot()
    {

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

        try {
            if ($this->adminModel->eraseTravelSpot($input['spotId'])) {
                echo json_encode(['success' => true, 'message' => 'Travel spot deleted successfully']);
                return;
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete travel spot']);
                return;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => "Database error occurred {$e->getMessage()}"]);
            return;
        }
    }

    public function oversight()
    {

        ob_start();
        $this->view('Admin/oversight/oversight');
        $html =  ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/oversight/oversight.css',
            'js'   => URL_ROOT . '/public/js/admin/oversight/oversight.js'
        ];

        $unEncodedResponse = [
            'tabId' => 'oversight',
            'loadingContent' => $loadingContent
        ];

        $this->view('UserTemplates/adminDash', $unEncodedResponse);
    }

    public function subtabMonitorReviews()
    {

        ob_start();
        $this->view('Admin/oversight/subtabMonitorReviews');
        $html = ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/oversight/subtabMonitorReviews.css',
            'js'   => URL_ROOT . '/public/js/admin/oversight/subtabMonitorReviews.js'
        ];

        $unEncodedResponse = [
            'ok' => true,
            'loadingContent' => $loadingContent
        ];

        echo json_encode($unEncodedResponse);
    }

    // Review Monitoring Methods
    public function reviews()
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("reviews called");

        try {
            $reviews = $this->adminModel->getAllReviews();
            $stats = $this->adminModel->getReviewStats();
            error_log("reviews: found " . count($reviews) . " reviews");
            echo json_encode([
                'success' => true,
                'reviews' => $reviews,
                'stats' => $stats
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching reviews: ' . $e->getMessage()]);
        }
    }

    public function deleteReview($reviewId)
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("deleteReview called for reviewId: " . $reviewId);

        if (!$reviewId || !is_numeric($reviewId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid review ID']);
            return;
        }

        // Get current user (admin) ID from session
        $adminId = getSession('user_id');

        if (!$adminId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            $result = $this->adminModel->deleteReview($reviewId);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Review deleted successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Failed to delete review']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when deleting review: ' . $e->getMessage()]);
        }
    }

    public function transaction()
    {

        ob_start();
        $this->view('Admin/transaction/transaction');
        $html =  ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/transaction/transaction.css',
            'js'   => URL_ROOT . '/public/js/admin/transaction/transaction.js'
        ];

        $unEncodedResponse = [
            'tabId' => 'transaction',
            'loadingContent' => $loadingContent
        ];

        $this->view('UserTemplates/adminDash', $unEncodedResponse);
    }

    public function subtabTravelerPayments()
    {

        ob_start();
        $this->view('Admin/transaction/subtabTravelerPayments');
        $html = ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/transaction/subtabTravelerPayments.css',
            'js'   => URL_ROOT . '/public/js/admin/transaction/subtabTravelerPayments.js'
        ];

        $unEncodedResponse = [
            'ok' => true,
            'loadingContent' => $loadingContent
        ];

        echo json_encode($unEncodedResponse);
    }

    // Traveler Payments API Methods
    public function getCompletedPayments()
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("getCompletedPayments called");

        try {
            $payments = $this->adminModel->getCompletedPayments();
            error_log("getCompletedPayments: found " . count($payments) . " completed payments");
            echo json_encode([
                'success' => true,
                'payments' => $payments
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching completed payments: ' . $e->getMessage()]);
        }
    }

    public function getCancelledPayments()
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("getCancelledPayments called");

        try {
            $payments = $this->adminModel->getCancelledPayments();
            error_log("getCancelledPayments: found " . count($payments) . " cancelled payments");
            echo json_encode([
                'success' => true,
                'payments' => $payments
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching cancelled payments: ' . $e->getMessage()]);
        }
    }

    public function getRefundedPayments()
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("getRefundedPayments called");

        try {
            $payments = $this->adminModel->getRefundedPayments();
            error_log("getRefundedPayments: found " . count($payments) . " refunded payments");
            echo json_encode([
                'success' => true,
                'payments' => $payments
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching refunded payments: ' . $e->getMessage()]);
        }
    }

    public function getPaymentDetails($paymentId)
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("getPaymentDetails called for paymentId: " . $paymentId);

        if (!$paymentId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Payment ID is required']);
            return;
        }

        try {
            $payment = $this->adminModel->getPaymentDetails($paymentId);

            if (!$payment) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Payment not found']);
                return;
            }

            echo json_encode([
                'success' => true,
                'payment' => $payment
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching payment details: ' . $e->getMessage()]);
        }
    }

    public function payDriver($driverPaymentId)
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("payDriver called for driverPaymentId: " . $driverPaymentId);

        if (!$driverPaymentId || !is_numeric($driverPaymentId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid driver payment ID']);
            return;
        }

        // Get current user (admin) ID from session
        $adminId = getSession('user_id');

        if (!$adminId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            $result = $this->adminModel->payDriver($driverPaymentId, $adminId);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Driver payment processed successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Failed to process driver payment']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when processing driver payment: ' . $e->getMessage()]);
        }
    }

    public function payGuide($guidePaymentId)
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("payGuide called for guidePaymentId: " . $guidePaymentId);

        if (!$guidePaymentId || !is_numeric($guidePaymentId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid guide payment ID']);
            return;
        }

        // Get current user (admin) ID from session
        $adminId = getSession('user_id');

        if (!$adminId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            $result = $this->adminModel->payGuide($guidePaymentId, $adminId);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Guide payment processed successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Failed to process guide payment']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when processing guide payment: ' . $e->getMessage()]);
        }
    }

    public function refundPayment($paymentId)
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("refundPayment called for paymentId: " . $paymentId);

        if (!$paymentId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Payment ID is required']);
            return;
        }

        // Get current user (admin) ID from session
        $adminId = getSession('user_id');

        if (!$adminId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        // Get refund reason from POST data
        $input = json_decode(file_get_contents('php://input'), true);
        $reason = $input['reason'] ?? '';

        if (empty($reason)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Refund reason is required']);
            return;
        }

        try {
            $result = $this->adminModel->refundPayment($paymentId, $adminId, $reason);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Payment refunded successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Failed to refund payment']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when refunding payment: ' . $e->getMessage()]);
        }
    }

    public function getPaymentStats()
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("getPaymentStats called");

        try {
            $stats = $this->adminModel->getPaymentStats();

            if ($stats) {
                echo json_encode([
                    'success' => true,
                    'stats' => $stats
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to retrieve payment statistics']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching payment stats: ' . $e->getMessage()]);
        }
    }

    public function subtabDriverPayout()
    {
        ob_start();
        $this->view('Admin/transaction/subtabDriverPayout');
        $html = ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/transaction/subtabDriverPayout.css',
            'js'   => URL_ROOT . '/public/js/admin/transaction/subtabDriverPayout.js'
        ];

        $unEncodedResponse = [
            'ok' => true,
            'loadingContent' => $loadingContent
        ];

        echo json_encode($unEncodedResponse);
    }

    // Driver Payout API Methods
    public function getCompletedDriverPayouts()
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("getCompletedDriverPayouts called");

        try {
            $payouts = $this->adminModel->getCompletedDriverPayouts();
            error_log("getCompletedDriverPayouts: found " . count($payouts) . " completed payouts");
            echo json_encode([
                'success' => true,
                'payouts' => $payouts
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching completed driver payouts: ' . $e->getMessage()]);
        }
    }

    public function getPendingDriverPayouts()
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("getPendingDriverPayouts called");

        try {
            $payouts = $this->adminModel->getPendingDriverPayouts();
            error_log("getPendingDriverPayouts: found " . count($payouts) . " pending payouts");
            echo json_encode([
                'success' => true,
                'payouts' => $payouts
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching pending driver payouts: ' . $e->getMessage()]);
        }
    }

    public function getCancelledDriverPayouts()
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("getCancelledDriverPayouts called");

        try {
            $payouts = $this->adminModel->getCancelledDriverPayouts();
            error_log("getCancelledDriverPayouts: found " . count($payouts) . " cancelled payouts");
            echo json_encode([
                'success' => true,
                'payouts' => $payouts
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching cancelled driver payouts: ' . $e->getMessage()]);
        }
    }

    public function getDriverPayoutDetails($payoutId)
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("getDriverPayoutDetails called for payoutId: " . $payoutId);

        if (!$payoutId || !is_numeric($payoutId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid payout ID']);
            return;
        }

        try {
            $payout = $this->adminModel->getDriverPayoutDetails($payoutId);

            if (!$payout) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Payout not found']);
                return;
            }

            echo json_encode([
                'success' => true,
                'payout' => $payout
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching driver payout details: ' . $e->getMessage()]);
        }
    }

    public function processDriverPayout($driverPaymentId)
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("processDriverPayout called for driverPaymentId: " . $driverPaymentId);

        if (!$driverPaymentId || !is_numeric($driverPaymentId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid driver payment ID']);
            return;
        }

        // Get current user (admin) ID from session
        $adminId = getSession('user_id');

        if (!$adminId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            $result = $this->adminModel->processDriverPayout($driverPaymentId, $adminId);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Driver payout processed successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Failed to process driver payout']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when processing driver payout: ' . $e->getMessage()]);
        }
    }

    public function refundDriverPayout($driverPaymentId)
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("refundDriverPayout called for driverPaymentId: " . $driverPaymentId);

        if (!$driverPaymentId || !is_numeric($driverPaymentId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid driver payment ID']);
            return;
        }

        // Get current user (admin) ID from session
        $adminId = getSession('user_id');

        if (!$adminId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        // Get refund reason from POST data
        $input = json_decode(file_get_contents('php://input'), true);
        $reason = $input['reason'] ?? '';

        if (empty($reason)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Refund reason is required']);
            return;
        }

        try {
            $result = $this->adminModel->refundDriverPayout($driverPaymentId, $adminId, $reason);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Driver payout refunded successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Failed to refund driver payout']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when refunding driver payout: ' . $e->getMessage()]);
        }
    }

    public function getDriverPayoutStats()
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("getDriverPayoutStats called");

        try {
            $stats = $this->adminModel->getDriverPayoutStats();

            if ($stats) {
                echo json_encode([
                    'success' => true,
                    'stats' => $stats
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to retrieve driver payout statistics']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching driver payout stats: ' . $e->getMessage()]);
        }
    }

    // Guide Payout API Methods
    public function getCompletedGuidePayouts()
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("getCompletedGuidePayouts called");

        try {
            $payouts = $this->adminModel->getCompletedGuidePayouts();
            error_log("getCompletedGuidePayouts: found " . count($payouts) . " completed payouts");
            echo json_encode([
                'success' => true,
                'payouts' => $payouts
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching completed guide payouts: ' . $e->getMessage()]);
        }
    }

    public function getPendingGuidePayouts()
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("getPendingGuidePayouts called");

        try {
            $payouts = $this->adminModel->getPendingGuidePayouts();
            error_log("getPendingGuidePayouts: found " . count($payouts) . " pending payouts");
            echo json_encode([
                'success' => true,
                'payouts' => $payouts
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching pending guide payouts: ' . $e->getMessage()]);
        }
    }

    public function getCancelledGuidePayouts()
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("getCancelledGuidePayouts called");

        try {
            $payouts = $this->adminModel->getCancelledGuidePayouts();
            error_log("getCancelledGuidePayouts: found " . count($payouts) . " cancelled payouts");
            echo json_encode([
                'success' => true,
                'payouts' => $payouts
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching cancelled guide payouts: ' . $e->getMessage()]);
        }
    }

    public function getGuidePayoutDetails($payoutId)
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("getGuidePayoutDetails called for payoutId: " . $payoutId);

        if (!$payoutId || !is_numeric($payoutId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid payout ID']);
            return;
        }

        try {
            $payout = $this->adminModel->getGuidePayoutDetails($payoutId);

            if (!$payout) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Payout not found']);
                return;
            }

            echo json_encode([
                'success' => true,
                'payout' => $payout
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching guide payout details: ' . $e->getMessage()]);
        }
    }

    public function processGuidePayout($guidePaymentId)
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("processGuidePayout called for guidePaymentId: " . $guidePaymentId);

        if (!$guidePaymentId || !is_numeric($guidePaymentId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid guide payment ID']);
            return;
        }

        // Get current user (admin) ID from session
        $adminId = getSession('user_id');

        if (!$adminId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            $result = $this->adminModel->processGuidePayout($guidePaymentId, $adminId);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Guide payout processed successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Failed to process guide payout']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when processing guide payout: ' . $e->getMessage()]);
        }
    }

    public function refundGuidePayout($guidePaymentId)
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("refundGuidePayout called for guidePaymentId: " . $guidePaymentId);

        if (!$guidePaymentId || !is_numeric($guidePaymentId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid guide payment ID']);
            return;
        }

        // Get current user (admin) ID from session
        $adminId = getSession('user_id');

        if (!$adminId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        // Get refund reason from POST data
        $input = json_decode(file_get_contents('php://input'), true);
        $reason = $input['reason'] ?? '';

        if (empty($reason)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Refund reason is required']);
            return;
        }

        try {
            $result = $this->adminModel->refundGuidePayout($guidePaymentId, $adminId, $reason);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Guide payout refunded successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Failed to refund guide payout']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when refunding guide payout: ' . $e->getMessage()]);
        }
    }

    public function getGuidePayoutStats()
    {
        header('Content-Type: application/json');

        // Log to PHP error log
        error_log("getGuidePayoutStats called");

        try {
            $stats = $this->adminModel->getGuidePayoutStats();

            if ($stats) {
                echo json_encode([
                    'success' => true,
                    'stats' => $stats
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to retrieve guide payout statistics']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching guide payout stats: ' . $e->getMessage()]);
        }
    }

    public function subtabGuidePayout()
    {
        ob_start();
        $this->view('Admin/transaction/subtabGuidePayout');
        $html = ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/transaction/subtabGuidePayout.css',
            'js'   => URL_ROOT . '/public/js/admin/transaction/subtabGuidePayout.js'
        ];

        $unEncodedResponse = [
            'ok' => true,
            'loadingContent' => $loadingContent
        ];

        echo json_encode($unEncodedResponse);
    }

    public function subtabSetServiceCharge()
    {
        ob_start();
        $this->view('Admin/transaction/subtabSetServiceCharge');
        $html = ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/transaction/subtabSetServiceCharge.css',
            'js'   => URL_ROOT . '/public/js/admin/transaction/subtabSetServiceCharge.js'
        ];

        $unEncodedResponse = [
            'ok' => true,
            'loadingContent' => $loadingContent
        ];

        echo json_encode($unEncodedResponse);
    }

    // Service Charge Management API Methods
    public function getCurrentServiceCharges()
    {
        header('Content-Type: application/json');

        try {
            $charges = $this->adminModel->getCurrentServiceCharges();

            echo json_encode([
                'success' => true,
                'charges' => $charges
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
        }
    }

    public function setServiceCharges()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $adminId = getSession('user_id');
        if (!$adminId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
            return;
        }

        // Validate required fields
        $requiredFields = ['driverBookingCharge', 'guideBookingCharge', 'siteServiceCharge'];
        foreach ($requiredFields as $field) {
            if (!isset($input[$field]) || $input[$field] < 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => "Invalid or missing field: $field"]);
                return;
            }
        }

        // Validate site service charge percentage
        if ($input['siteServiceCharge'] > 100) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Site service charge cannot exceed 100%']);
            return;
        }

        try {
            $result = $this->adminModel->setServiceCharges($input, $adminId);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Service charges updated successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to update service charges']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
        }
    }

    public function getServiceChargeHistory()
    {
        header('Content-Type: application/json');

        try {
            $history = $this->adminModel->getServiceChargeHistory();

            // Map the raw database field names to consistent API field names
            $mappedHistory = array_map(function ($entry) {
                return [
                    'chargeId' => $entry->chargeId,
                    'moderatorId' => $entry->moderatorId,
                    'driverBookingCharge' => (float)$entry->dBookCharge,
                    'guideBookingCharge' => (float)$entry->gBookCharge,
                    'siteServiceCharge' => (float)$entry->siteServiceCharge,
                    'isActive' => (bool)$entry->isActive,
                    'notes' => $entry->notes,
                    'createdAt' => $entry->createdAt,
                    'updatedAt' => $entry->updatedAt,
                    'moderatorName' => $entry->moderatorName,
                    'moderatorEmail' => $entry->moderatorEmail
                ];
            }, $history);

            echo json_encode([
                'success' => true,
                'history' => $mappedHistory
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
        }
    }

    //Analytics Tab Methods

    public function analytics()
    {
        ob_start();
        $this->view('Admin/analytics/analytics');
        $html =  ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/analytics/analytics.css',
            'js'   => URL_ROOT . '/public/js/admin/analytics/analytics.js'
        ];

        $unEncodedResponse = [
            'tabId' => 'analytics',
            'loadingContent' => $loadingContent
        ];

        $this->view('UserTemplates/adminDash', $unEncodedResponse);
    }

    public function subtabEarningsBreakdown()
    {
        ob_start();
        $this->view('Admin/analytics/subtabEarningsBreakdown');
        $html = ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/analytics/subtabEarningsBreakdown.css',
            'js'   => URL_ROOT . '/public/js/admin/analytics/subtabEarningsBreakdown.js'
        ];

        $unEncodedResponse = [
            'ok' => true,
            'loadingContent' => $loadingContent
        ];

        echo json_encode($unEncodedResponse);
    }

    // Earnings Analytics API Methods
    public function getEarningsMetrics()
    {
        header('Content-Type: application/json');

        try {
            $timeRange = $_GET['timeRange'] ?? '30days';
            $viewType = $_GET['viewType'] ?? 'daily';

            // Calculate date range
            $dateRange = $this->calculateDateRange($timeRange);

            // Get metrics from database
            $metrics = $this->adminModel->getEarningsMetrics($dateRange['start'], $dateRange['end'], $viewType);

            // Calculate percentage changes (comparing with previous period)
            $previousRange = $this->calculatePreviousDateRange($timeRange);
            $previousMetrics = $this->adminModel->getEarningsMetrics($previousRange['start'], $previousRange['end'], $viewType);

            $changes = $this->calculatePercentageChanges($metrics, $previousMetrics);

            $response = [
                'success' => true,
                'metrics' => array_merge($metrics, $changes)
            ];

            echo json_encode($response);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch earnings metrics: ' . $e->getMessage()
            ]);
        }
    }

    public function getRevenueTrend()
    {
        header('Content-Type: application/json');

        try {
            $timeRange = $_GET['timeRange'] ?? '30days';
            $viewType = $_GET['viewType'] ?? 'daily';

            $dateRange = $this->calculateDateRange($timeRange);

            $trend = $this->adminModel->getRevenueTrend($dateRange['start'], $dateRange['end'], $viewType);

            echo json_encode([
                'success' => true,
                'trend' => $trend
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch revenue trend: ' . $e->getMessage()
            ]);
        }
    }

    public function getRevenueBreakdown()
    {
        header('Content-Type: application/json');

        try {
            $timeRange = $_GET['timeRange'] ?? '30days';

            $dateRange = $this->calculateDateRange($timeRange);

            $breakdown = $this->adminModel->getRevenueBreakdown($dateRange['start'], $dateRange['end']);

            echo json_encode([
                'success' => true,
                'breakdown' => $breakdown
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch revenue breakdown: ' . $e->getMessage()
            ]);
        }
    }

    public function getProfitMarginTrend()
    {
        header('Content-Type: application/json');

        try {
            $timeRange = $_GET['timeRange'] ?? '30days';
            $viewType = $_GET['viewType'] ?? 'daily';

            $dateRange = $this->calculateDateRange($timeRange);

            $trend = $this->adminModel->getProfitMarginTrend($dateRange['start'], $dateRange['end'], $viewType);

            echo json_encode([
                'success' => true,
                'trend' => $trend
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch profit margin trend: ' . $e->getMessage()
            ]);
        }
    }

    public function getEarningsTable()
    {
        header('Content-Type: application/json');

        try {
            $page = (int)($_GET['page'] ?? 1);
            $limit = (int)($_GET['limit'] ?? 25);
            $timeRange = $_GET['timeRange'] ?? '30days';
            $status = $_GET['status'] ?? 'all';

            $dateRange = $this->calculateDateRange($timeRange);
            $offset = ($page - 1) * $limit;

            $result = $this->adminModel->getEarningsTable($dateRange['start'], $dateRange['end'], $limit, $offset, $status);

            echo json_encode([
                'success' => true,
                'earnings' => $result['earnings'],
                'total' => $result['total']
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch earnings table: ' . $e->getMessage()
            ]);
        }
    }

    // Payout Analysis API Methods
    public function getPayoutAnalysisMetrics()
    {
        header('Content-Type: application/json');

        try {
            $timeRange = $_GET['timeRange'] ?? '30days';
            $dateRange = $this->calculateDateRange($timeRange);

            $metrics = $this->adminModel->getPayoutAnalysisMetrics($dateRange['start'], $dateRange['end']);

            echo json_encode([
                'success' => true,
                'metrics' => $metrics
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch payout analysis metrics: ' . $e->getMessage()
            ]);
        }
    }

    public function getPayoutTrendData()
    {
        header('Content-Type: application/json');

        try {
            $timeRange = $_GET['timeRange'] ?? '30days';
            $viewType = $_GET['viewType'] ?? 'daily';
            $dateRange = $this->calculateDateRange($timeRange);

            $trend = $this->adminModel->getPayoutTrendData($dateRange['start'], $dateRange['end'], $viewType);

            echo json_encode([
                'success' => true,
                'trend' => $trend
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch payout trend data: ' . $e->getMessage()
            ]);
        }
    }

    public function getPayoutTypeBreakdown()
    {
        header('Content-Type: application/json');

        try {
            $timeRange = $_GET['timeRange'] ?? '30days';
            $dateRange = $this->calculateDateRange($timeRange);

            $breakdown = $this->adminModel->getPayoutTypeBreakdown($dateRange['start'], $dateRange['end']);

            echo json_encode([
                'success' => true,
                'breakdown' => $breakdown
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch payout type breakdown: ' . $e->getMessage()
            ]);
        }
    }

    public function getUserPayoutAnalysis()
    {
        header('Content-Type: application/json');

        try {
            $userId = (int)($_GET['userId'] ?? 0);
            $timeRange = $_GET['timeRange'] ?? '30days';
            $viewType = $_GET['viewType'] ?? 'daily';

            if ($userId <= 0) {
                throw new Exception('Valid user ID is required');
            }

            $profile = $this->adminModel->getPayoutUserProfile($userId);
            if (!$profile) {
                throw new Exception('Driver or guide not found for the given user ID');
            }

            $dateRange = $this->calculateDateRange($timeRange);
            $summary = $this->adminModel->getUserPayoutSummary($userId, $profile->account_type, $dateRange['start'], $dateRange['end']);
            $trend = $this->adminModel->getUserPayoutTrendData($userId, $profile->account_type, $dateRange['start'], $dateRange['end'], $viewType);
            $recentPayouts = $this->adminModel->getUserRecentPayouts($userId, $profile->account_type, 10);

            echo json_encode([
                'success' => true,
                'profile' => $profile,
                'summary' => $summary,
                'trend' => $trend,
                'recentPayouts' => $recentPayouts
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch user payout analysis: ' . $e->getMessage()
            ]);
        }
    }

    public function getTopEarners()
    {
        header('Content-Type: application/json');

        try {
            $timeRange = $_GET['timeRange'] ?? '30days';
            $limit = (int)($_GET['limit'] ?? 5);
            $dateRange = $this->calculateDateRange($timeRange);

            $topEarners = $this->adminModel->getTopEarners($dateRange['start'], $dateRange['end'], $limit);

            echo json_encode([
                'success' => true,
                'topEarners' => $topEarners
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch top earners: ' . $e->getMessage()
            ]);
        }
    }

    private function calculateDateRange($timeRange)
    {
        $now = new DateTime();
        $end = $now->format('Y-m-d H:i:s');

        switch ($timeRange) {
            case '7days':
                $start = $now->modify('-7 days')->format('Y-m-d 00:00:00');
                break;
            case '30days':
                $start = $now->modify('-30 days')->format('Y-m-d 00:00:00');
                break;
            case '90days':
                $start = $now->modify('-90 days')->format('Y-m-d 00:00:00');
                break;
            case '1year':
                $start = $now->modify('-1 year')->format('Y-m-d 00:00:00');
                break;
            case 'all':
                $start = '1970-01-01 00:00:00';
                break;
            default:
                $start = $now->modify('-30 days')->format('Y-m-d 00:00:00');
        }

        return ['start' => $start, 'end' => $end];
    }

    private function calculatePreviousDateRange($timeRange)
    {
        $now = new DateTime();

        switch ($timeRange) {
            case '7days':
                $end = $now->modify('-7 days')->format('Y-m-d 23:59:59');
                $start = $now->modify('-7 days')->format('Y-m-d 00:00:00');
                break;
            case '30days':
                $end = $now->modify('-30 days')->format('Y-m-d 23:59:59');
                $start = $now->modify('-30 days')->format('Y-m-d 00:00:00');
                break;
            case '90days':
                $end = $now->modify('-90 days')->format('Y-m-d 23:59:59');
                $start = $now->modify('-90 days')->format('Y-m-d 00:00:00');
                break;
            case '1year':
                $end = $now->modify('-1 year')->format('Y-m-d 23:59:59');
                $start = $now->modify('-1 year')->format('Y-m-d 00:00:00');
                break;
            default:
                $end = $now->modify('-30 days')->format('Y-m-d 23:59:59');
                $start = $now->modify('-30 days')->format('Y-m-d 00:00:00');
        }

        return ['start' => $start, 'end' => $end];
    }

    private function calculatePercentageChanges($current, $previous)
    {
        $changes = [];

        $fields = ['totalRevenue', 'siteProfit', 'driverRevenue', 'guideRevenue'];

        foreach ($fields as $field) {
            $currentValue = $current[$field] ?? 0;
            $previousValue = $previous[$field] ?? 0;

            if ($previousValue == 0) {
                $change = $currentValue > 0 ? 100 : 0;
            } else {
                $change = (($currentValue - $previousValue) / $previousValue) * 100;
            }

            $changes[$field . 'Change'] = round($change, 2);
        }

        return $changes;
    }

    public function subtabPayoutAnalysis()
    {

        ob_start();
        $this->view('Admin/analytics/subtabPayoutAnalysis');
        $html = ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/analytics/subtabPayoutAnalysis.css',
            'js'   => URL_ROOT . '/public/js/admin/analytics/subtabPayoutAnalysis.js'
        ];

        $unEncodedResponse = [
            'ok' => true,
            'loadingContent' => $loadingContent
        ];

        echo json_encode($unEncodedResponse);
    }

    public function subtabUserAnalysis()
    {
        ob_start();
        $this->view('Admin/analytics/subtabUserAnalysis');
        $html = ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/analytics/subtabUserAnalysis.css',
            'js'   => URL_ROOT . '/public/js/admin/analytics/subtabUserAnalysis.js'
        ];

        $unEncodedResponse = [
            'ok' => true,
            'loadingContent' => $loadingContent
        ];

        echo json_encode($unEncodedResponse);
    }

    // User Analysis API Methods
    public function getUserBaseStats()
    {
        header('Content-Type: application/json');

        try {
            $stats = $this->adminModel->getUserBaseStats();

            echo json_encode([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching user base stats: ' . $e->getMessage()]);
        }
    }

    public function getVerificationStats()
    {
        header('Content-Type: application/json');

        try {
            $stats = $this->adminModel->getVerificationStats();

            echo json_encode([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching verification stats: ' . $e->getMessage()]);
        }
    }

    public function getRegistrationTrend()
    {
        header('Content-Type: application/json');

        try {
            $timeRange = $_GET['timeRange'] ?? '30days';
            $viewType = $_GET['viewType'] ?? 'daily';

            $dateRange = $this->calculateDateRange($timeRange);

            $trend = $this->adminModel->getRegistrationTrend($dateRange['start'], $dateRange['end'], $viewType);

            echo json_encode([
                'success' => true,
                'trend' => $trend
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching registration trend: ' . $e->getMessage()]);
        }
    }

    public function getLicenseStats()
    {
        header('Content-Type: application/json');

        try {
            $stats = $this->adminModel->getLicenseStats();

            echo json_encode([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching license stats: ' . $e->getMessage()]);
        }
    }

    public function getVehicleVerificationStats()
    {
        header('Content-Type: application/json');

        try {
            $stats = $this->adminModel->getVehicleVerificationStats();

            echo json_encode([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching vehicle verification stats: ' . $e->getMessage()]);
        }
    }

    public function getVehicleVerificationTrend()
    {
        header('Content-Type: application/json');

        try {
            $timeRange = $_GET['timeRange'] ?? '30days';
            $viewType = $_GET['viewType'] ?? 'daily';

            $dateRange = $this->calculateDateRange($timeRange);

            $trend = $this->adminModel->getVehicleVerificationTrend($dateRange['start'], $dateRange['end'], $viewType);

            echo json_encode([
                'success' => true,
                'trend' => $trend
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching vehicle verification trend: ' . $e->getMessage()]);
        }
    }

    public function getVehicleVerificationStatusBreakdown()
    {
        header('Content-Type: application/json');

        try {
            $breakdown = $this->adminModel->getVehicleVerificationStatusBreakdown();

            echo json_encode([
                'success' => true,
                'breakdown' => $breakdown
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching vehicle verification breakdown: ' . $e->getMessage()]);
        }
    }

    public function getFleetStats()
    {
        header('Content-Type: application/json');

        try {
            $stats = $this->adminModel->getFleetStats();

            echo json_encode([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching fleet stats: ' . $e->getMessage()]);
        }
    }

    public function getVehicleBookingTrend()
    {
        header('Content-Type: application/json');

        try {
            $timeRange = $_GET['timeRange'] ?? '30days';
            $viewType = $_GET['viewType'] ?? 'daily';

            $dateRange = $this->calculateDateRange($timeRange);

            $trend = $this->adminModel->getVehicleBookingTrend($dateRange['start'], $dateRange['end'], $viewType);

            echo json_encode([
                'success' => true,
                'trend' => $trend
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching vehicle booking trend: ' . $e->getMessage()]);
        }
    }

    public function getDriverOwnershipStats()
    {
        header('Content-Type: application/json');

        try {
            $stats = $this->adminModel->getDriverOwnershipStats();

            echo json_encode([
                'success' => true,
                'ownership' => $stats
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred when fetching driver ownership stats: ' . $e->getMessage()]);
        }
    }

    public function subtabVehicleAnalysis()
    {
        ob_start();
        $this->view('Admin/analytics/subtabVehicleAnalysis');
        $html = ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/analytics/subtabVehicleAnalysis.css',
            'js'   => URL_ROOT . '/public/js/admin/analytics/subtabVehicleAnalysis.js'
        ];

        $unEncodedResponse = [
            'ok' => true,
            'loadingContent' => $loadingContent
        ];

        echo json_encode($unEncodedResponse);
    }

    public function userInfo()
    {
        ob_start();
        $this->view('Admin/userInfo/userInfo');
        $html =  ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/userInfo/userInfo.css',
            'js'   => URL_ROOT . '/public/js/admin/userInfo/userInfo.js'
        ];

        $unEncodedResponse = [
            'tabId' => 'userInfo',
            'loadingContent' => $loadingContent
        ];

        $this->view('UserTemplates/adminDash', $unEncodedResponse);
    }

    public function subtabProfileInfo()
    {

        ob_start();
        $this->view('Admin/userInfo/subtabProfileInfo');
        $html = ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/userInfo/subtabProfileInfo.css',
            'js'   => URL_ROOT . '/public/js/admin/userInfo/subtabProfileInfo.js'
        ];

        $unEncodedResponse = [
            'ok' => true,
            'loadingContent' => $loadingContent
        ];

        echo json_encode($unEncodedResponse);
    }

    public function subtabVehicleInfo()
    {

        ob_start();
        $this->view('Admin/userInfo/subtabVehicleInfo');
        $html = ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/userInfo/subtabVehicleInfo.css',
            'js'   => URL_ROOT . '/public/js/admin/userInfo/subtabVehicleInfo.js'
        ];

        $unEncodedResponse = [
            'ok' => true,
            'loadingContent' => $loadingContent
        ];

        echo json_encode($unEncodedResponse);
    }

    public function subtabTravelSpotInfo()
    {

        ob_start();
        $this->view('Admin/userInfo/subtabTravelSpotInfo');
        $html = ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/userInfo/subtabTravelSpotInfo.css',
            'js'   => URL_ROOT . '/public/js/admin/userInfo/subtabTravelSpotInfo.js'
        ];

        $unEncodedResponse = [
            'ok' => true,
            'loadingContent' => $loadingContent
        ];

        echo json_encode($unEncodedResponse);
    }

    // User Info Methods
    public function getUserProfileInfo($userId)
    {
        header('Content-Type: application/json');

        if (!$userId || !is_numeric($userId)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid user ID'
            ]);
            return;
        }

        try {
            $user = $this->adminModel->getUserProfileInfoForModerator((int)$userId);

            if (!$user) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'User not found'
                ]);
                return;
            }

            echo json_encode([
                'success' => true,
                'user' => $user
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database error occurred while fetching user profile information'
            ]);
        }
    }

    public function getVehicleInfo($vehicleId)
    {
        header('Content-Type: application/json');

        if (!$vehicleId || !is_numeric($vehicleId)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid vehicle ID'
            ]);
            return;
        }

        try {
            $vehicle = $this->adminModel->getVehicleInfoForModerator((int)$vehicleId);

            if (!$vehicle) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Vehicle not found'
                ]);
                return;
            }

            echo json_encode([
                'success' => true,
                'vehicle' => $vehicle
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database error occurred while fetching vehicle information'
            ]);
        }
    }

    public function getTravelSpotGuides()
    {
        header('Content-Type: application/json');

        $search = trim($_GET['search'] ?? '');

        if ($search === '') {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Travel spot name or ID is required'
            ]);
            return;
        }

        try {
            $results = $this->adminModel->getGuidesByTravelSpotSearchForModerator($search);

            echo json_encode([
                'success' => true,
                'results' => $results,
                'count' => count($results)
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database error occurred while fetching travel spot guide information'
            ]);
        }
    }

    public function tripInfo()
    {
        ob_start();
        $this->view('Admin/tripInfo/tripInfo');
        $html =  ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/tripInfo/tripInfo.css',
            'js'   => URL_ROOT . '/public/js/admin/tripInfo/tripInfo.js'
        ];

        $unEncodedResponse = [
            'tabId' => 'tripInfo',
            'loadingContent' => $loadingContent
        ];

        $this->view('UserTemplates/adminDash', $unEncodedResponse);
    }

    public function subtabTripLogs()
    {
        ob_start();
        $this->view('Admin/tripInfo/subtabTripLogs');
        $html = ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/tripInfo/subtabTripLogs.css',
            'js'   => URL_ROOT . '/public/js/admin/tripInfo/subtabTripLogs.js'
        ];

        $unEncodedResponse = [
            'ok' => true,
            'loadingContent' => $loadingContent
        ];

        echo json_encode($unEncodedResponse);
    }

    public function getTripLogs()
    {
        header('Content-Type: application/json');

        try {
            $trips = $this->adminModel->getTripLogsForModerator();

            echo json_encode([
                'success' => true,
                'trips' => $trips,
                'count' => count($trips)
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database error occurred while fetching trip logs'
            ]);
        }
    }

    public function subtabTripControl()
    {
        ob_start();
        $this->view('Admin/tripInfo/subtabTripControl');
        $html = ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/tripInfo/subtabTripControl.css',
            'js'   => URL_ROOT . '/public/js/admin/tripInfo/subtabTripControl.js'
        ];

        $unEncodedResponse = [
            'ok' => true,
            'loadingContent' => $loadingContent
        ];

        echo json_encode($unEncodedResponse);
    }

    public function getTripControlTrip($tripId)
    {
        header('Content-Type: application/json');

        if (!$tripId || !is_numeric($tripId)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Valid trip ID is required'
            ]);
            return;
        }

        try {
            $trip = $this->adminModel->getTripControlTripById((int)$tripId);

            if (!$trip) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Trip not found'
                ]);
                return;
            }

            echo json_encode([
                'success' => true,
                'trip' => $trip
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database error occurred while fetching trip control details'
            ]);
        }
    }

    public function getTripControlDriverCandidates($tripId)
    {
        header('Content-Type: application/json');

        if (!$tripId || !is_numeric($tripId)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Valid trip ID is required'
            ]);
            return;
        }

        try {
            $candidates = $this->adminModel->getTripControlDriverCandidates((int)$tripId);

            echo json_encode([
                'success' => true,
                'candidates' => $candidates,
                'count' => count($candidates)
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database error occurred while fetching driver candidates'
            ]);
        }
    }

    public function getTripControlGuideCandidates($tripId, $eventId)
    {
        header('Content-Type: application/json');

        if (!$tripId || !is_numeric($tripId) || !$eventId || !is_numeric($eventId)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Valid trip ID and event ID are required'
            ]);
            return;
        }

        try {
            $candidates = $this->adminModel->getTripControlGuideCandidates((int)$tripId, (int)$eventId);

            echo json_encode([
                'success' => true,
                'candidates' => $candidates,
                'count' => count($candidates)
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database error occurred while fetching guide candidates'
            ]);
        }
    }

    public function replaceTripDriver()
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $tripId = isset($input['tripId']) ? (int)$input['tripId'] : 0;
        $driverId = isset($input['driverId']) ? (int)$input['driverId'] : 0;
        $forceAssign = !empty($input['forceAssign']);

        if ($tripId <= 0 || $driverId <= 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Valid trip ID and driver ID are required'
            ]);
            return;
        }

        try {
            $result = $this->adminModel->replaceTripDriverForModerator($tripId, $driverId, $forceAssign);

            if (!empty($result['success'])) {
                echo json_encode($result);
            } else {
                http_response_code(400);
                echo json_encode($result);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database error occurred while replacing trip driver'
            ]);
        }
    }

    public function replaceTripGuide()
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $tripId = isset($input['tripId']) ? (int)$input['tripId'] : 0;
        $eventId = isset($input['eventId']) ? (int)$input['eventId'] : 0;
        $guideId = isset($input['guideId']) ? (int)$input['guideId'] : 0;
        $forceAssign = !empty($input['forceAssign']);

        if ($tripId <= 0 || $eventId <= 0 || $guideId <= 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Valid trip ID, event ID, and guide ID are required'
            ]);
            return;
        }

        try {
            $result = $this->adminModel->replaceTripGuideForModerator($tripId, $eventId, $guideId, $forceAssign);

            if (!empty($result['success'])) {
                echo json_encode($result);
            } else {
                http_response_code(400);
                echo json_encode($result);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database error occurred while replacing event guide'
            ]);
        }
    }

    public function getAllGuidesForModerator()
    {
        try {
            $result = $this->adminModel->getAllGuidesForModerator();

            if ($result['success']) {
                echo json_encode($result);
            } else {
                http_response_code(400);
                echo json_encode($result);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch guides'
            ]);
        }
    }

    public function removeTripGuide()
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $tripId = isset($input['tripId']) ? (int)$input['tripId'] : 0;
        $eventId = isset($input['eventId']) ? (int)$input['eventId'] : 0;

        if ($tripId <= 0 || $eventId <= 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Valid trip ID and event ID are required'
            ]);
            return;
        }

        try {
            $result = $this->adminModel->removeTripGuideForModerator($tripId, $eventId);

            if (!empty($result['success'])) {
                echo json_encode($result);
            } else {
                http_response_code(400);
                echo json_encode($result);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database error occurred while removing event guide'
            ]);
        }
    }

    // ==================== USER PROBLEMS CENTER ====================

    public function subtabHelpdesk()
    {
        ob_start();
        $this->view('Admin/oversight/subtabHelpdesk');
        $html = ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/oversight/subtabHelpdesk.css',
            'js'   => URL_ROOT . '/public/js/admin/oversight/subtabHelpdesk.js'
        ];

        $unEncodedResponse = [
            'ok' => 'oversight',
            'loadingContent' => $loadingContent
        ];

        echo json_encode($unEncodedResponse);
    }

    public function problems()
    {

        ob_start();
        $this->view('Admin/quickProblems/quickProblems');
        $html = ob_get_clean();

        $loadingContent = [
            'html' => $html,
            'css'  => URL_ROOT . '/public/css/admin/quickProblems/quickProblems.css',
            'js'   => URL_ROOT . '/public/js/admin/quickProblems/quickProblems.js'
        ];

        $unEncodedResponse = [
            'tabId' => 'qproblem',
            'loadingContent' => $loadingContent
        ];

        $this->view('UserTemplates/adminDash', $unEncodedResponse);
    }

    public function getProblems()
    {

        header('Content-Type: application/json');

        $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

        try {
            $problems = $this->adminModel->getAllProblems($filter);
            $counts = $this->adminModel->getProblemCounts();

            echo json_encode([
                'success' => true,
                'problems' => $problems,
                'counts' => $counts
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred']);
        }
    }

    public function completeProblem()
    {

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid method']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $moderatorId = getSession('user_id');

        if (!$moderatorId || empty($input['problemId'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        try {
            if ($this->adminModel->completeProblem($input['problemId'], $moderatorId)) {
                echo json_encode(['success' => true, 'message' => 'Problem marked as completed']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update problem']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred']);
        }
    }

    public function deleteProblem()
    {

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            echo json_encode(['success' => false, 'message' => 'Invalid method']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $moderatorId = getSession('user_id');

        if (!$moderatorId || empty($input['problemId'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        try {
            if ($this->adminModel->deleteProblem($input['problemId'])) {
                echo json_encode(['success' => true, 'message' => 'Problem deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete problem']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred']);
        }
    }

    public function submitProblem()
    {

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid method']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $userId = getSession('user_id');

        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'You must be logged in to submit a problem']);
            return;
        }

        if (empty($input['subject']) || empty($input['message'])) {
            echo json_encode(['success' => false, 'message' => 'Subject and message are required']);
            return;
        }

        $data = [
            'userId' => $userId,
            'subject' => htmlspecialchars($input['subject']),
            'message' => htmlspecialchars($input['message'])
        ];

        try {
            if ($this->adminModel->submitUserProblem($data)) {
                echo json_encode(['success' => true, 'message' => 'Your problem has been submitted successfully! Our team will look into it.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to submit problem']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error occurred']);
        }
    }



    public function addModerator()
    {
        // Check if user is admin
        if (!isLoggedIn() || getSession('user_account_type') !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);

            // Validate required fields
            $required_fields = ['fullname', 'email', 'password', 'phone', 'language', 'dob', 'gender', 'address', 'account_type'];
            foreach ($required_fields as $field) {
                if (empty($input[$field])) {
                    echo json_encode(['success' => false, 'message' => "Field $field is required"]);
                    return;
                }
            }

            // Validate account type
            if (!in_array($input['account_type'], ['site_moderator', 'business_manager'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid account type']);
                return;
            }

            // Validate date of birth - cannot be in the future
            $dobDate = new DateTime($input['dob']);
            $today = new DateTime();
            if ($dobDate > $today) {
                echo json_encode(['success' => false, 'message' => 'Date of birth cannot be in the future']);
                return;
            }

            // Validate minimum age (optional - set to 18 years)
            $minAgeDate = new DateTime();
            $minAgeDate->sub(new DateInterval('P18Y')); // 18 years ago
            if ($dobDate > $minAgeDate) {
                echo json_encode(['success' => false, 'message' => 'Moderator must be at least 18 years old']);
                return;
            }

            // Check if email already exists
            if ($this->userModel->findUserByEmail($input['email'])) {
                echo json_encode(['success' => false, 'message' => 'Email already exists']);
                return;
            }

            // Validate password strength
            $password = (string)($input['password'] ?? '');
            if (strlen($password) < 8) {
                echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long']);
                return;
            }

            if (!preg_match('/[A-Z]/', $password)) {
                echo json_encode(['success' => false, 'message' => 'Password must contain at least one uppercase letter']);
                return;
            }

            if (!preg_match('/[a-z]/', $password)) {
                echo json_encode(['success' => false, 'message' => 'Password must contain at least one lowercase letter']);
                return;
            }

            if (!preg_match('/\d/', $password)) {
                echo json_encode(['success' => false, 'message' => 'Password must contain at least one number']);
                return;
            }

            // Hash password
            $input['password'] = password_hash($input['password'], PASSWORD_DEFAULT);

            // Add moderator
            $result = $this->userModel->addModerator($input);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Moderator added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add moderator']);
            }
        }
    }

    public function getModerators()
    {
        // Check if user is admin
        if (!isLoggedIn() || getSession('user_account_type') !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        $moderators = $this->userModel->getModerators();
        echo json_encode(['success' => true, 'moderators' => $moderators]);
    }

    public function getModerator()
    {
        // Check if user is admin
        if (!isLoggedIn() || getSession('user_account_type') !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $moderatorId = $_GET['id'] ?? null;

            if (empty($moderatorId)) {
                echo json_encode(['success' => false, 'message' => 'Moderator ID is required']);
                return;
            }

            $moderator = $this->userModel->getModeratorById($moderatorId);

            if ($moderator) {
                echo json_encode(['success' => true, 'moderator' => $moderator]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Moderator not found']);
            }
        }
    }

    public function updateModerator()
    {
        // Check if user is admin
        if (!isLoggedIn() || getSession('user_account_type') !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);

            // Validate required fields
            $required_fields = ['id', 'fullname', 'email', 'phone', 'language', 'dob', 'gender', 'address', 'account_type'];
            foreach ($required_fields as $field) {
                if (empty($input[$field])) {
                    echo json_encode(['success' => false, 'message' => "Field $field is required"]);
                    return;
                }
            }

            // Validate account type
            if (!in_array($input['account_type'], ['site_moderator', 'business_manager'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid account type']);
                return;
            }

            // Validate date of birth - cannot be in the future
            $dobDate = new DateTime($input['dob']);
            $today = new DateTime();
            if ($dobDate > $today) {
                echo json_encode(['success' => false, 'message' => 'Date of birth cannot be in the future']);
                return;
            }

            // Validate minimum age (optional - set to 18 years)
            $minAgeDate = new DateTime();
            $minAgeDate->sub(new DateInterval('P18Y')); // 18 years ago
            if ($dobDate > $minAgeDate) {
                echo json_encode(['success' => false, 'message' => 'Moderator must be at least 18 years old']);
                return;
            }

            // Check if email already exists for other users
            $existingUser = $this->userModel->findUserByEmail($input['email']);
            if ($existingUser && $existingUser->id != $input['id']) {
                echo json_encode(['success' => false, 'message' => 'Email already exists']);
                return;
            }

            // Update moderator
            $result = $this->userModel->updateModerator($input);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Moderator updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update moderator']);
            }
        }
    }

    public function deleteModerator()
    {
        // Check if user is admin
        if (!isLoggedIn() || getSession('user_account_type') !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id'])) {
                echo json_encode(['success' => false, 'message' => 'Moderator ID is required']);
                return;
            }

            $result = $this->userModel->deleteModerator($input['id']);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Moderator deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete moderator']);
            }
        }
    }

    public function getItinerary()
    {
        // Check if user is admin
        if (!isLoggedIn() || getSession('user_account_type') !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['itinerary_number'])) {
                echo json_encode(['success' => false, 'message' => 'Itinerary number is required']);
                return;
            }

            $itineraryNumber = trim($input['itinerary_number']);

            // Get itinerary details including guide and driver information
            $itinerary = $this->userModel->getItineraryDetails($itineraryNumber);

            if ($itinerary) {
                echo json_encode([
                    'success' => true,
                    'data' => $itinerary
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Itinerary not found'
                ]);
            }
        }
    }
}

// `/controller/method/parameters
