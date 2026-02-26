<?php
    class BuisManager extends Controller{
        private $buisManagerModel;
        
        public function __construct() {
            $this->buisManagerModel = $this->model('buisManagerModel');
        }

        public function index() {

            $this->buisManagerModel->generateTransactionsForCompletedTrips();

            // Get base data from the model (Trip Management from created_trips)
            $transactions   = $this->buisManagerModel->getAllTransactions();
            $trips         = $this->buisManagerModel->getAllTrips();
            $payouts       = $this->buisManagerModel->getAllPayouts();
            $tripsToday    = $this->buisManagerModel->getCompletedTrips();   // completed: endDate < today
            $ongoingTrips  = $this->buisManagerModel->getOngoingTrips();
            $upcomingTrips = $this->buisManagerModel->getUpcomingTrips();

            $drivers = $this->buisManagerModel->getAllDrivers();
            $guides  = $this->buisManagerModel->getAllGuides();
            $kpiStats = $this->buisManagerModel->getKpiStats();

            // Commission Management data
            $commissionOverview = [];
            $commissionRates = [];
            $commissionHistory = [];
            $guideEarnings = [];
            $driverEarnings = [];
            try {
                $commissionOverview = $this->buisManagerModel->getCommissionOverview();
                $commissionRates = $this->buisManagerModel->getCommissionRates();
                $commissionHistory = $this->buisManagerModel->getCommissionHistory();
                $useDateRange = !empty($_GET['commission_date_from']) && !empty($_GET['commission_date_to']);
                $month = $useDateRange ? null : ($_GET['commission_month'] ?? date('Y-m'));
                $dateFrom = $useDateRange ? $_GET['commission_date_from'] : null;
                $dateTo = $useDateRange ? $_GET['commission_date_to'] : null;
                $guideId = !empty($_GET['commission_guide_id']) ? (int)$_GET['commission_guide_id'] : null;
                $driverId = !empty($_GET['commission_driver_id']) ? (int)$_GET['commission_driver_id'] : null;
                $guideEarnings = $this->buisManagerModel->getGuideEarningsBreakdown($month, $dateFrom, $dateTo, $guideId);
                $driverEarnings = $this->buisManagerModel->getDriverEarningsBreakdown($month, $dateFrom, $dateTo, $driverId);
            } catch (Exception $e) {
                // Tables may not exist yet; run dev/commission_rates.sql
            }

            $data = [
                'transactions'        => $transactions,
                'trips'               => $trips,
                'payouts'             => $payouts,
                'tripsToday'          => $tripsToday,
                'ongoingTrips'        => $ongoingTrips,
                'upcomingTrips'       => $upcomingTrips,
                'drivers'             => $drivers,
                'guides'              => $guides,
                'kpiStats'            => $kpiStats,
                'commissionOverview'  => $commissionOverview,
                'commissionRates'     => $commissionRates,
                'commissionHistory'   => $commissionHistory,
                'guideEarnings'       => $guideEarnings,
                'driverEarnings'     => $driverEarnings,
            ];
            
            $this->view('BuisManager/buisDash', $data);
        }

        /**
         * Update commission rate (POST). Expects: role, new_rate, effective_from, reason.
         * Returns JSON for AJAX or redirects back with message.
         */
        public function updateCommission() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                } else {
                    header('Location: ' . URL_ROOT . '/BuisManager');
                }
                return;
            }
            $role = trim($_POST['role'] ?? '');
            $newRate = isset($_POST['new_rate']) ? (float)$_POST['new_rate'] : null;
            $effectiveFrom = trim($_POST['effective_from'] ?? '');
            $reason = trim($_POST['reason'] ?? '');
            $user = getLoggedInUser();
            $userId = isset($user['id']) ? (int)$user['id'] : null;

            if ($role === '' || $newRate === null || $effectiveFrom === '') {
                $msg = 'Missing role, new rate, or effective from date.';
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $msg]);
                } else {
                    $_SESSION['commission_message'] = $msg;
                    header('Location: ' . URL_ROOT . '/BuisManager');
                }
                return;
            }

            $ok = $this->buisManagerModel->updateCommissionRate($role, $newRate, $effectiveFrom, $reason, $userId);
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => $ok, 'message' => $ok ? 'Commission rate updated.' : 'Update failed.']);
            } else {
                $_SESSION['commission_message'] = $ok ? 'Commission rate updated.' : 'Update failed.';
                header('Location: ' . URL_ROOT . '/BuisManager');
            }
        }

        public function generateTransactionsForCompletedTrips() {

            $this->db->query("
                SELECT * FROM trips 
                WHERE status = 'Completed'
            ");
            $completedTrips = $this->db->resultSet();
        
            foreach ($completedTrips as $trip) {
        
                $duration = (int)$trip->duration;
                if ($duration <= 0) {
                    continue;
                }
        
                // ================= DRIVER PAYMENT =================
                if (!empty($trip->driverID)) {
        
                    // Check if already exists
                    $this->db->query("
                        SELECT transactionID FROM transactions 
                        WHERE userID = :userID 
                        AND type = 'Driver Payment'
                        AND transactionDate = :date
                        LIMIT 1
                    ");
                    $this->db->bind(':userID', $trip->driverID);
                    $this->db->bind(':date', $trip->start_date);
                    $exists = $this->db->single();
        
                    if (!$exists) {
        
                        $driverRate = $this->getDriverDayPayment($trip->driverID);
                        $amount = $duration * $driverRate;
        
                        $this->db->query("
                            INSERT INTO transactions 
                            (userID, amount, type, transactionDate, transactionTime, transaction_status, actions)
                            VALUES
                            (:userID, :amount, 'Driver Payment', :date, :time, 'Pending', 'Process')
                        ");
        
                        $this->db->bind(':userID', $trip->driverID);
                        $this->db->bind(':amount', $amount);
                        $this->db->bind(':date', $trip->start_date);
                        $this->db->bind(':time', date('H:i:s'));
                        $this->db->execute();
                    }
                }
        
                // ================= GUIDE PAYMENT =================
                if (!empty($trip->guideID)) {
        
                    $this->db->query("
                        SELECT transactionID FROM transactions 
                        WHERE userID = :userID 
                        AND type = 'Guide Payment'
                        AND transactionDate = :date
                        LIMIT 1
                    ");
                    $this->db->bind(':userID', $trip->guideID);
                    $this->db->bind(':date', $trip->start_date);
                    $exists = $this->db->single();
        
                    if (!$exists) {
        
                        $guideRate = $this->getGuideDayPayment($trip->guideID);
                        $amount = $duration * $guideRate;
        
                        $this->db->query("
                            INSERT INTO transactions 
                            (userID, amount, type, transactionDate, transactionTime, transaction_status, actions)
                            VALUES
                            (:userID, :amount, 'Guide Payment', :date, :time, 'Pending', 'Process')
                        ");
        
                        $this->db->bind(':userID', $trip->guideID);
                        $this->db->bind(':amount', $amount);
                        $this->db->bind(':date', $trip->start_date);
                        $this->db->bind(':time', date('H:i:s'));
                        $this->db->execute();
                    }
                }
            }
        }
        
    }


    // `/controller/method/parameters
?>

