<?php
    //require_once '../app/helpers/session_helper.php';
    
    class Dashboard extends Controller {
        
        public function __construct() {
            // Require login for all dashboard methods
            requireLogin();
        }
        
        public function index() {
            $user = getLoggedInUser();
            $accountType = $user['account_type'] ?? '';

            // Redirect to role-specific dashboard
            switch ($accountType) {
                case 'admin':
                    header('Location: ' . URL_ROOT . '/dashboard/admin');
                    exit();
                case 'driver':
                    header('Location: ' . URL_ROOT . '/dashboard/driver');
                    exit();
                case 'guide':
                    header('Location: ' . URL_ROOT . '/dashboard/guide');
                    exit();
                case 'site_moderator':
                    header('Location: ' . URL_ROOT . '/dashboard/siteModerator');
                    exit();
                case 'business_manager':
                    header('Location: ' . URL_ROOT . '/dashboard/businessManager');
                    exit();
                case 'tourist':
                    header('Location: ' . URL_ROOT . '/RegUser/home');
                    exit();
                default:
                    header('Location: ' . URL_ROOT . '/Home');
                    exit();
            }
        }

        // Admin specific dashboard
        public function admin() {
            $user = getLoggedInUser();
            
            // Check if user is actually a guide
            if ($user['account_type'] !== 'admin') {
                header('Location: ' . URL_ROOT . '/dashboard');
                exit();
            }
            
            $data = [
                'title' => 'Admin Dashboard',
                'user' => $user
            ];
            
            $this->view('Admin/adminDash', $data);
        }

        // SiteModerator specific dashboard
        public function siteModerator() {
            $user = getLoggedInUser();
            
            // Check if user is actually a guide
            if ($user['account_type'] !== 'site_moderator') {
                header('Location: ' . URL_ROOT . '/dashboard');
                exit();
            }
            
            $data = [
                'title' => 'siteModerator Dashboard',
                'user' => $user
            ];
            
            $this->view('SiteModerator/SiteModeratorDash', $data);
        }

        // Business Manager specific dashboard
        public function businessManager() {
            $user = getLoggedInUser();
            
            // Check if user is actually a business manager
            if ($user['account_type'] !== 'business_manager') {
                header('Location: ' . URL_ROOT . '/dashboard');
                exit();
            }
            
            // Load the business manager model
            $buisManagerModel = $this->model('buisManagerModel');

            // When transaction_status is Completed, ensure that data is in payouts table
            $buisManagerModel->syncCompletedTransactionsToPayouts();
            // When traveller_side_g_requests status = 'accepted' and created_trips status = 'completed', sync to transactions and payouts
            $buisManagerModel->syncAcceptedGuideRequestsToTransactionsAndPayouts();
            
            // Get all data from the model
            $transactions   = $buisManagerModel->getAllTransactions();
            $payouts        = $buisManagerModel->getAllPayouts();
            try {
                $trips = $buisManagerModel->getAllTrips();
            } catch (\Throwable $e) {
                $trips = [];
            }
            $tripEvents     = $buisManagerModel->getAllTripEvents();
            $refundRequests = $buisManagerModel->getAllRefundRequests();
            $kpiStats       = $buisManagerModel->getKpiStats();
            $transactionsToday   = $buisManagerModel->getTransactionsToday();
            $transactionsLast7   = $buisManagerModel->getTransactionsLast7Days();
            $tripsToday     = $buisManagerModel->getTripsToday();
            $tripsLast7     = $buisManagerModel->getTripsLast7Days();
            $ongoingTrips   = $buisManagerModel->getOngoingTrips();
            $upcomingTrips  = $buisManagerModel->getUpcomingTrips();
            $drivers        = $buisManagerModel->getAllDrivers();
            $guides         = $buisManagerModel->getAllGuides();

            $driverGuideTransactions = [];

            // For each completed trip, create virtual transactions for driver and guide
            foreach ($trips as $trip) {
                $status = strtolower($trip->status ?? '');
                if ($status !== 'completed') {
                    continue;
                }

                $durationDays = (int)($trip->duration ?? 0);
                if ($durationDays < 0) {
                    $durationDays = 0;
                }

                // Driver payment transaction - starts as Pending until Business Manager processes
                if (!empty($trip->driverID)) {
                    $newId = $buisManagerModel->insertTransaction(
                        $trip->id,
                        $trip->driverID,
                        $durationDays * $buisManagerModel->getDriverDayPayment($trip->driverID),
                        'Driver Payment',
                        $trip->start_date ?? date('Y-m-d'),
                        date('H:i:s'),
                        'Pending',
                        'Process'
                    );
                    if ($newId) {
                        $driverRatePerDay = $buisManagerModel->getDriverDayPayment($trip->driverID);
                        $t = new \stdClass();
                        $t->transactionID      = $newId;
                        $t->userID             = $trip->driverID;
                        $t->userName           = $buisManagerModel->getUserNameById($trip->driverID);
                        $t->amount             = $durationDays * $driverRatePerDay;
                        $t->type               = 'Driver Payment';
                        $t->transactionDate    = $trip->start_date ?? date('Y-m-d');
                        $t->transactionTime    = date('H:i:s');
                        $t->transaction_status = 'Pending';
                        $t->actions            = 'Process';
                        $driverGuideTransactions[] = $t;
                    }
                }

                // Guide payment transaction - starts as Pending until Business Manager processes
                if (!empty($trip->guideID)) {
                    $newId = $buisManagerModel->insertTransaction(
                        $trip->id,
                        $trip->guideID,
                        $durationDays * $buisManagerModel->getGuideDayPayment($trip->guideID),
                        'Guide Payment',
                        $trip->start_date ?? date('Y-m-d'),
                        date('H:i:s'),
                        'Pending',
                        'Process'
                    );
                    if ($newId) {
                        $guideRatePerDay = $buisManagerModel->getGuideDayPayment($trip->guideID);
                        $t = new \stdClass();
                        $t->transactionID      = $newId;
                        $t->userID             = $trip->guideID;
                        $t->userName           = $buisManagerModel->getUserNameById($trip->guideID);
                        $t->amount             = $durationDays * $guideRatePerDay;
                        $t->type               = 'Guide Payment';
                        $t->transactionDate    = $trip->start_date ?? date('Y-m-d');
                        $t->transactionTime    = date('H:i:s');
                        $t->transaction_status = 'Pending';
                        $t->actions            = 'Process';
                        $driverGuideTransactions[] = $t;
                    }
                }
            }

            // Merge real transactions with generated driver/guide payments
            $allTransactions = array_merge($transactions, $driverGuideTransactions);
            
            $data = [
                'title'               => 'Business Manager Dashboard',
                'user'                => $user,
                'transactions'        => $allTransactions,
                'payouts'             => $payouts,
                'trips'               => $trips,
                'tripEvents'          => $tripEvents,
                'refundRequests'      => $refundRequests,
                'kpiStats'            => $kpiStats,
                'transactionsToday'   => $transactionsToday,
                'transactionsLast7'    => $transactionsLast7,
                'tripsToday'          => $tripsToday,
                'tripsLast7'          => $tripsLast7,
                'ongoingTrips'        => $ongoingTrips,
                'upcomingTrips'       => $upcomingTrips,
                'drivers'             => $drivers,
                'guides'              => $guides,
            ];
            
            $this->view('BuisManager/buisDash', $data);
        }
        
        // Driver specific dashboard
        public function driver() {
            $user = getLoggedInUser();
            
            // Check if user is actually a driver
            if ($user['account_type'] !== 'driver') {
                header('Location: ' . URL_ROOT . '/dashboard');
                exit();
            }
            
            $data = [
                'title' => 'Driver Dashboard',
                'user' => $user
            ];
            
            $this->view('Driver/driverDash', $data);
        }
        
        // Process transaction: update status to Completed and insert into payouts table
        public function processTransaction($transactionId = null) {
            $user = getLoggedInUser();
            if ($user['account_type'] !== 'business_manager') {
                header('Location: ' . URL_ROOT . '/dashboard');
                exit();
            }

            if (empty($transactionId)) {
                header('Location: ' . URL_ROOT . '/dashboard/businessManager');
                exit();
            }

            $buisManagerModel = $this->model('buisManagerModel');
            $result = $buisManagerModel->processTransaction($transactionId);

            header('Location: ' . URL_ROOT . '/dashboard/businessManager');
            exit();
        }
        
        // Guide specific dashboard
        public function guide() {
            $user = getLoggedInUser();
            
            // Check if user is actually a guide
            if ($user['account_type'] !== 'guide') {
                header('Location: ' . URL_ROOT . '/dashboard');
                exit();
            }
            
            $data = [
                'title' => 'Guide Dashboard',
                'user' => $user
            ];
            
            $this->view('Guide/guideDash', $data);
        }
    }
?>