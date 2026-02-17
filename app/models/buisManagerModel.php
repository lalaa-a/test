<?php 

class BuisManagerModel {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Business manager does not show data from transactions table.
    public function getAllTransactions() {
        return [];
    }

    // Get all planned trips from created_trips (trips table does not exist).
    public function getAllTrips() {
        $this->db->query("
            SELECT
                tripId AS id,
                userId AS userID,
                NULL AS driverID,
                NULL AS guideID,
                startDate AS start_date,
                DATEDIFF(endDate, startDate) AS duration,
                0 AS revenue,
                status
            FROM created_trips
            ORDER BY startDate DESC
        ");
        return $this->db->resultSet();
    }

    // Get all trip events
    public function getAllTripEvents() {
        $this->db->query("
            SELECT
                te.eventId,
                te.tripId,
                te.userId,
                te.eventDate,
                te.startTime,
                te.endTime,
                te.eventType,
                te.eventStatus,
                te.travelSpotId,
                te.locationName,
                te.latitude,
                te.longitude,
                te.description,
                te.created_at,
                te.updated_at
            FROM trip_events te
            ORDER BY te.eventDate DESC
        ");

        return $this->db->resultSet();
    }

    // Business manager does not show data from payouts table.
    public function getAllPayouts() {
        return [];
    }

    // Trips starting today (from created_trips).
    public function getTripsToday() {
        $this->db->query("SELECT tripId, userId, tripTitle, description, startDate, endDate, status, createdAt, updatedAt, numberOfPeople
            FROM created_trips
            WHERE startDate = CURDATE()
            ORDER BY createdAt DESC");
        return $this->db->resultSet();
    }

    // Trips starting in the last 7 days (from created_trips).
    public function getTripsLast7Days() {
        $this->db->query("SELECT tripId, userId, tripTitle, description, startDate, endDate, status, createdAt, updatedAt, numberOfPeople
            FROM created_trips
            WHERE startDate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            ORDER BY startDate DESC, createdAt DESC");
        return $this->db->resultSet();
    }

    // Trips with status 'ongoing' (from created_trips).
    public function getOngoingTrips() {
        $this->db->query("SELECT tripId, userId, tripTitle, description, startDate, endDate, status, createdAt, updatedAt, numberOfPeople
            FROM created_trips
            WHERE status = 'ongoing'
            ORDER BY startDate DESC, createdAt DESC");
        return $this->db->resultSet();
    }

    // Trips with startDate in the future or status 'scheduled' (from created_trips).
    public function getUpcomingTrips() {
        $this->db->query("SELECT tripId, userId, tripTitle, description, startDate, endDate, status, createdAt, updatedAt, numberOfPeople
            FROM created_trips
            WHERE (startDate > CURDATE() OR status = 'scheduled')
            AND status != 'completed'
            ORDER BY startDate ASC, createdAt DESC");
        return $this->db->resultSet();
    }

    // Drivers: users with account_type = 'driver'. No drivers table.
    public function getAllDrivers() {
        $this->db->query("SELECT id, id AS userID, fullname, email, phone
            FROM users
            WHERE account_type = 'driver'
            ORDER BY fullname ASC");
        $rows = $this->db->resultSet();
        foreach ($rows as $r) {
            $r->day_payment = 0;
            $r->hourly_rate = 0;
            $r->total_revenue = 0;
            $r->status = 'active';
        }
        return $rows;
    }

    // Guides: users with account_type = 'guide'. No guides table.
    public function getAllGuides() {
        $this->db->query("SELECT id, fullname, email, phone
            FROM users
            WHERE account_type = 'guide'
            ORDER BY fullname ASC");
        $rows = $this->db->resultSet();
        foreach ($rows as $r) {
            $r->travel_spot = '—';
            $r->base_charge = 0;
            $r->total_revenue = 0;
            $r->status = 'active';
        }
        return $rows;
    }

    // Get a user's full name by ID (used for driver/guide payments)
    public function getUserNameById($userId) {
        $this->db->query("SELECT fullname FROM users WHERE id = :id");
        $this->db->bind(':id', $userId);
        $row = $this->db->single();
        return $row ? $row->fullname : 'Unknown User';
    }

    // No drivers table; driver payment not used for business manager (no data from transactions).
    public function getDriverDayPayment($driverId) {
        return 0.0;
    }

    // Guide payment is event-based only (totalCharge in traveller_side_g_requests), not per-day or per-hour.
    // Return 0 so trip-based guide transactions are not created from duration * rate; they come from syncAcceptedGuideRequestsToTransactionsAndPayouts.
    public function getGuideDayPayment($userId) {
        return 0.0;
    }


    public function getTransactionById($transactionId) {
        $this->db->query("SELECT * FROM transactions WHERE transactionID = :transactionId");
        $this->db->bind(':transactionId', $transactionId);
        return $this->db->single();
    }

    // Business manager does not show data from transactions table.
    public function getAllRefundRequests() {
        return [];
    }

    // KPI stats for dashboard: revenue, trips_count, drivers_count, refunds_count.
    public function getKpiStats() {
        $kpi = [
            'revenue'       => 0,
            'trips_count'  => 0,
            'drivers_count' => 0,
            'refunds_count' => 0
        ];

        // Business manager does not use data from transactions table.
        $kpi['revenue'] = 0;

        $this->db->query("SELECT COUNT(*) AS cnt FROM created_trips");
        $row = $this->db->single();
        if ($row && isset($row->cnt)) {
            $kpi['trips_count'] = (int)$row->cnt;
        }

        $this->db->query("SELECT COUNT(*) AS cnt FROM users WHERE account_type = 'driver'");
        $row = $this->db->single();
        if ($row && isset($row->cnt)) {
            $kpi['drivers_count'] = (int)$row->cnt;
        }

        // Business manager does not use data from transactions table.
        $kpi['refunds_count'] = 0;

        return $kpi;
    }

    // Business manager does not show data from transactions table.
    public function getTransactionsToday() {
        return [];
    }

    // Business manager does not show data from transactions table.
    public function getTransactionsLast7Days() {
        return [];
    }

    public function updateTransactionStatus($transactionId, $status) {
        $this->db->query("UPDATE transactions SET transaction_status = :status WHERE transactionID = :transactionId");
        $this->db->bind(':status', $status);
        $this->db->bind(':transactionId', $transactionId);
        return $this->db->execute();
    }

    // Insert a new transaction; transactionID is auto-increment in DB. Returns new transactionID or false.
    public function insertTransaction($tripID, $userID, $amount, $type, $transactionDate, $transactionTime, $transaction_status, $actions) {
        // Avoid duplicate: same trip + user + type
        $this->db->query("SELECT 1 FROM transactions WHERE tripID = :tripID AND userID = :userID AND type = :type LIMIT 1");
        $this->db->bind(':tripID', (int) $tripID);
        $this->db->bind(':userID', $userID);
        $this->db->bind(':type', $type);
        if ($this->db->single()) {
            return false;
        }

        $this->db->query("INSERT INTO transactions 
            (tripID, userID, amount, type, transactionDate, transactionTime, transaction_status, actions) 
            VALUES 
            (:tripID, :userID, :amount, :type, :transactionDate, :transactionTime, :transaction_status, :actions)");

        $this->db->bind(':tripID', (int) $tripID);
        $this->db->bind(':userID', $userID);
        $this->db->bind(':amount', $amount);
        $this->db->bind(':type', $type);
        $this->db->bind(':transactionDate', $transactionDate);
        $this->db->bind(':transactionTime', $transactionTime);
        $this->db->bind(':transaction_status', $transaction_status);
        $this->db->bind(':actions', $actions);

        if (!$this->db->execute()) {
            return false;
        }
        return (int) $this->db->lastInsertId();
    }
    

    // Insert a new payout when transaction status is updated to Completed.
    // Use INSERT IGNORE if table has UNIQUE(tripID, userID) to avoid duplicate rows.
    public function insertPayout($userID, $tripID, $service_type, $earnings, $commission, $net_payout, $status, $actions, $payout_date, $payout_time) {
        $this->db->query("INSERT IGNORE INTO payouts (userID, tripID, service_type, earnings, commission, net_payout, status, actions, payout_date, payout_time) 
                         VALUES (:userID, :tripID, :service_type, :earnings, :commission, :net_payout, :status, :actions, :payout_date, :payout_time)");
        $this->db->bind(':userID', $userID);
        $this->db->bind(':tripID', $tripID);
        $this->db->bind(':service_type', $service_type);
        $this->db->bind(':earnings', $earnings);
        $this->db->bind(':commission', $commission);
        $this->db->bind(':net_payout', $net_payout);
        $this->db->bind(':status', $status);
        $this->db->bind(':actions', $actions);
        $this->db->bind(':payout_date', $payout_date);
        $this->db->bind(':payout_time', $payout_time);
        return $this->db->execute();
    }

    // Check if payout already exists for this (trip, user). One payout per trip+user.
    public function payoutExistsForTransaction($tripID, $userID, $service_type) {
        $this->db->query("SELECT payoutID FROM payouts WHERE tripID = :tripID AND userID = :userID LIMIT 1");
        $this->db->bind(':tripID', $tripID);
        $this->db->bind(':userID', $userID);
        $row = $this->db->single();
        return $row !== false;
    }

    // Process transaction: update status to Completed and insert into payouts
    public function processTransaction($transactionId) {

        $transaction = $this->getTransactionById($transactionId);
    
        if (!$transaction) {
            return false;
        }
    
        if (strtolower($transaction->transaction_status) === 'completed') {
            return true;
        }
    
        // Get service type from type column
        $type = strtolower($transaction->type ?? '');

        if (strpos($type, 'driver') !== false) {
            $service_type = 'Driver Service';
        } elseif (strpos($type, 'guide') !== false) {
            $service_type = 'Guide Service';
        } else {
            return false;
        }
        
    
        $tripID = $transaction->tripID;
    
        // Update transaction status
        $this->updateTransactionStatus($transactionId, 'Completed');
    
        // Prevent duplicate payout
        if ($this->payoutExistsForTransaction($tripID, $transaction->userID, $service_type)) {
            return true;
        }
    
        $earnings = (float)$transaction->amount;
        $commission = round($earnings * 0.15, 2);
        $net_payout = round($earnings - $commission, 2);
    
        return $this->insertPayout(
            $transaction->userID,
            $tripID,
            $service_type,
            $earnings,
            $commission,
            $net_payout,
            'Completed',
            'Processed',
            date('Y-m-d'),
            date('H:i:s')
        );
    }
    
    

    // Get all Completed Driver/Guide Payment transactions (for syncing to payouts)
    public function getCompletedDriverGuideTransactions() {
        $this->db->query("SELECT * FROM transactions 
                          WHERE transaction_status = 'Completed' 
                          AND (type = 'Driver Payment' OR type = 'Guide Payment')");
        return $this->db->resultSet();
    }

    // Ensure every Completed driver/guide transaction has a payout row (run on dashboard load)
    public function syncCompletedTransactionsToPayouts() {
        $completed = $this->getCompletedDriverGuideTransactions();
        foreach ($completed as $t) {
            $tripID = (int)($t->tripID ?? 0);
            if ($tripID <= 0) {
                continue;
            }
            $service_type = (stripos($t->type ?? '', 'driver') !== false) ? 'driver' : 'guide';
            if ($this->payoutExistsForTransaction($tripID, $t->userID, $service_type)) {
                continue;
            }
            $earnings = (float)($t->amount ?? 0);
            $commission = round($earnings * 0.15, 2);
            $net_payout = round($earnings - $commission, 2);
            $payout_date = $t->transactionDate ?? date('Y-m-d');
            $payout_time = !empty($t->transactionTime) ? $t->transactionTime : date('H:i:s');
            $this->insertPayout(
                $t->userID,
                $tripID,
                $service_type,
                $earnings,
                $commission,
                $net_payout,
                'Completed',
                'Processed',
                $payout_date,
                $payout_time
            );
        }
    }

    // Sync accepted guide requests from traveller_side_g_requests (event payment in totalCharge) to transactions and payouts.
    // Guides have no hourly/day rate; payment is per event via totalCharge.
    public function syncAcceptedGuideRequestsToTransactionsAndPayouts() {
        $this->db->query("
            SELECT tripId, guideId,
                   SUM(totalCharge) AS totalCharge,
                   MIN(COALESCE(acceptedAt, requestedAt)) AS transactionDate
            FROM traveller_side_g_requests
            WHERE status = 'accepted'
            AND totalCharge IS NOT NULL
            GROUP BY tripId, guideId
        ");
        $rows = $this->db->resultSet();
        if (!$rows) {
            return;
        }
        foreach ($rows as $row) {
            $tripID = (int)$row->tripId;
            $guideId = (int)$row->guideId;
            $amount = (float)($row->totalCharge ?? 0);
            if ($tripID <= 0 || $guideId <= 0 || $amount <= 0) {
                continue;
            }
            $transactionDate = $row->transactionDate ?? date('Y-m-d');
            $transactionTime = date('H:i:s');
            $newId = $this->insertTransaction(
                $tripID,
                $guideId,
                $amount,
                'Guide Payment',
                $transactionDate,
                $transactionTime,
                'Pending',
                'Process'
            );
            if (!$newId) {
                continue;
            }
            if ($this->payoutExistsForTransaction($tripID, $guideId, 'Guide Service')) {
                continue;
            }
            $earnings = $amount;
            $commission = round($earnings * 0.15, 2);
            $net_payout = round($earnings - $commission, 2);
            $this->insertPayout(
                $guideId,
                $tripID,
                'Guide Service',
                $earnings,
                $commission,
                $net_payout,
                'Completed',
                'Processed',
                $transactionDate,
                $transactionTime
            );
        }
    }

    public function generateTransactionsForCompletedTrips() {

        $this->db->query("
            SELECT tripId AS id, userId AS userID, startDate AS start_date,
                   DATEDIFF(endDate, startDate) AS duration, NULL AS driverID, NULL AS guideID
            FROM created_trips
            WHERE status = 'completed'
        ");
        $completedTrips = $this->db->resultSet();
    
        foreach ($completedTrips as $trip) {
    
            $duration = (int)$trip->duration;
            if ($duration <= 0) {
                continue;
            }
    
            // ================= DRIVER PAYMENT =================
            if (!empty($trip->driverID)) {
    
                $this->db->query("
                    SELECT transactionID FROM transactions 
                    WHERE userID = :userID 
                    AND type = 'Driver Payment'
                    AND transactionDate = :date
                    LIMIT 1
                ");
                $this->db->bind(':tripID', $trip->id);
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
                $this->db->bind(':tripID', $trip->id);
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

