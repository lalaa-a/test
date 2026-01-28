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

        public function retrieveBasicGuideInfo() {
            
            header('Content-Type: application/json');
            
            // Log to PHP error log instead of output
            error_log("retrieveBasicDriverInfo called f");

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

        public function help() {
            ob_start();
            $this->view('UserTemplates/help');
            $html = ob_get_clean();

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

            $this->view('UserTemplates/guideDash', $unEncodedResponse);
        }
    }


    // `/controller/method/parameters
?>

