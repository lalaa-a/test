<?php
    class BuisManager extends Controller{
        private $buisManagerModel;
        
        public function __construct() {
            $this->buisManagerModel = $this->model('buisManagerModel');
        }

        public function index() {

            $this->buisManagerModel->generateTransactionsForCompletedTrips();

            // Get base data from the model
            $transactions = $this->buisManagerModel->getAllTransactions();
            $trips        = $this->buisManagerModel->getAllTrips();
            $payouts      = $this->buisManagerModel->getAllPayouts();

            
            $data = [
                'transactions' => $transactions,
                'trips'        => $trips,
                'payouts'      => $payouts,
            ];
            
            $this->view('BuisManager/buisDash', $data);
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

