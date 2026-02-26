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

    // Completed trips: created_trips where status = 'completed'. Traveller = name from users.
    public function getCompletedTrips() {
        $this->db->query("SELECT ct.tripId, ct.userId, u.fullname AS travellerName, ct.tripTitle, ct.description, ct.startDate, ct.endDate, ct.status, ct.createdAt, ct.updatedAt, ct.numberOfPeople
            FROM created_trips ct
            LEFT JOIN users u ON u.id = ct.userId
            WHERE ct.status = 'completed'
            ORDER BY ct.endDate DESC, ct.createdAt DESC");
        return $this->db->resultSet();
    }

    // Trips starting today (from created_trips). Traveller = name from users.
    public function getTripsToday() {
        $this->db->query("SELECT ct.tripId, ct.userId, u.fullname AS travellerName, ct.tripTitle, ct.description, ct.startDate, ct.endDate, ct.status, ct.createdAt, ct.updatedAt, ct.numberOfPeople
            FROM created_trips ct
            LEFT JOIN users u ON u.id = ct.userId
            WHERE ct.startDate = CURDATE()
            ORDER BY ct.createdAt DESC");
        return $this->db->resultSet();
    }

    // Trips starting in the last 7 days (from created_trips). Traveller = name from users.
    public function getTripsLast7Days() {
        $this->db->query("SELECT ct.tripId, ct.userId, u.fullname AS travellerName, ct.tripTitle, ct.description, ct.startDate, ct.endDate, ct.status, ct.createdAt, ct.updatedAt, ct.numberOfPeople
            FROM created_trips ct
            LEFT JOIN users u ON u.id = ct.userId
            WHERE ct.startDate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            ORDER BY ct.startDate DESC, ct.createdAt DESC");
        return $this->db->resultSet();
    }

    // Trips with status 'ongoing' (from created_trips). Traveller = name from users.
    public function getOngoingTrips() {
        $this->db->query("SELECT ct.tripId, ct.userId, u.fullname AS travellerName, ct.tripTitle, ct.description, ct.startDate, ct.endDate, ct.status, ct.createdAt, ct.updatedAt, ct.numberOfPeople
            FROM created_trips ct
            LEFT JOIN users u ON u.id = ct.userId
            WHERE ct.status = 'ongoing'
            ORDER BY ct.startDate DESC, ct.createdAt DESC");
        return $this->db->resultSet();
    }

    // Scheduled trips: created_trips where status = 'pending'. Traveller = name from users.
    public function getUpcomingTrips() {
        $this->db->query("SELECT ct.tripId, ct.userId, u.fullname AS travellerName, ct.tripTitle, ct.description, ct.startDate, ct.endDate, ct.status, ct.createdAt, ct.updatedAt, ct.numberOfPeople
            FROM created_trips ct
            LEFT JOIN users u ON u.id = ct.userId
            WHERE ct.status = 'pending'
            ORDER BY ct.startDate ASC, ct.createdAt DESC");
        return $this->db->resultSet();
    }

    // Drivers: from users + transactions. Last month = bookings and revenue in previous calendar month (e.g. Jan when today is Feb 21).
    public function getAllDrivers() {
        $this->db->query("
            SELECT
                u.id,
                u.id AS userID,
                u.fullname,
                u.email,
                u.phone,
                COALESCE(agg.total_bookings_last_month, 0) AS total_bookings_last_month,
                COALESCE(agg.total_revenue, 0) AS total_revenue,
                'active' AS status
            FROM users u
            LEFT JOIN (
                SELECT
                    t.userID,
                    COUNT(*) AS total_bookings_last_month,
                    SUM(t.amount) AS total_revenue
                FROM transactions t
                WHERE t.type = 'Driver Payment'
                  AND t.transactionDate >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01')
                  AND t.transactionDate <= LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                GROUP BY t.userID
            ) agg ON agg.userID = u.id
            WHERE u.account_type = 'driver'
            ORDER BY u.fullname ASC
        ");
        return $this->db->resultSet();
    }

    // Guides: from users + transactions. Last month = bookings and revenue in previous calendar month.
    public function getAllGuides() {
        $this->db->query("
            SELECT
                u.id,
                u.fullname,
                u.email,
                u.phone,
                COALESCE(agg.total_bookings_last_month, 0) AS total_bookings_last_month,
                COALESCE(agg.total_revenue, 0) AS total_revenue,
                'active' AS status
            FROM users u
            LEFT JOIN (
                SELECT
                    t.userID,
                    COUNT(*) AS total_bookings_last_month,
                    SUM(t.amount) AS total_revenue
                FROM transactions t
                WHERE t.type = 'Guide Payment'
                  AND t.transactionDate >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01')
                  AND t.transactionDate <= LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                GROUP BY t.userID
            ) agg ON agg.userID = u.id
            WHERE u.account_type = 'guide'
            ORDER BY u.fullname ASC
        ");
        return $this->db->resultSet();
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

    /**
     * Get all refund requests from refund_requests table.
     * Joins users for traveller name and created_trips for trip start date.
     * Automatic refund amount calculation based on cancellation timing:
     * - Before 48 hours before trip start → 80% refund
     * - 48–24 hours before trip start → 50% refund
     * - Less than 24 hours before trip start → No refund (0%)
     */
    public function getAllRefundRequests() {
        try {
            $this->db->query("
                SELECT rr.id AS request_id,
                       rr.traveller_id,
                       rr.trip_id AS booking_id,
                       rr.booking_amount,
                       CASE
                         WHEN ct.startDate IS NULL OR rr.requested_date IS NULL THEN COALESCE(rr.refund_amount, 0)
                         WHEN TIMESTAMPDIFF(HOUR, rr.requested_date, CONCAT(ct.startDate, ' 00:00:00')) >= 48 THEN ROUND(rr.booking_amount * 0.80, 2)
                         WHEN TIMESTAMPDIFF(HOUR, rr.requested_date, CONCAT(ct.startDate, ' 00:00:00')) >= 24 THEN ROUND(rr.booking_amount * 0.50, 2)
                         ELSE 0
                       END AS refund_amount,
                       rr.requested_date AS request_date,
                       rr.status,
                       rr.reviewed_by,
                       reviewer.fullname AS reviewed_by_name,
                       rr.reviewed_date,
                       rr.reason,
                       u.fullname AS user_name
                FROM refund_requests rr
                LEFT JOIN users u ON u.id = rr.traveller_id
                LEFT JOIN users reviewer ON reviewer.id = rr.reviewed_by
                LEFT JOIN created_trips ct ON ct.tripId = rr.trip_id
                ORDER BY rr.requested_date DESC
            ");
            return $this->db->resultSet();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Approve or reject a refund request. Updates status, reviewed_by (manager id), reviewed_date.
     */
    public function updateRefundRequestStatus($refundId, $status, $reviewedByUserId) {
        $refundId = (int) $refundId;
        $status = in_array(strtolower($status), ['approved', 'rejected'], true) ? $status : null;
        if ($refundId <= 0 || $status === null) {
            return false;
        }
        $this->db->query("
            UPDATE refund_requests
            SET status = :status,
                reviewed_by = :reviewed_by,
                reviewed_date = NOW()
            WHERE id = :id
        ");
        $this->db->bind(':status', $status);
        $this->db->bind(':reviewed_by', $reviewedByUserId ? (string) $reviewedByUserId : null);
        $this->db->bind(':id', $refundId);
        return $this->db->execute();
    }

    /**
     * Calculate refund amount based on cancellation timing vs trip start date.
     * Rules: >=48h before → 80%, 24–48h → 50%, <24h → 0%.
     * Use when inserting/updating refund requests.
     */
    public function calculateRefundAmount($bookingAmount, $requestedDate, $tripStartDate) {
        if (empty($bookingAmount) || empty($requestedDate) || empty($tripStartDate)) {
            return 0;
        }
        $tripStart = strtotime($tripStartDate);
        $requested = strtotime($requestedDate);
        $hoursBefore = ($tripStart - $requested) / 3600;
        $amount = (float) $bookingAmount;
        if ($hoursBefore >= 48) {
            return round($amount * 0.80, 2);
        }
        if ($hoursBefore >= 24) {
            return round($amount * 0.50, 2);
        }
        return 0;
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

        try {
            $this->db->query("SELECT COUNT(*) AS cnt FROM refund_requests WHERE LOWER(TRIM(status)) = 'pending'");
            $row = $this->db->single();
            if ($row && isset($row->cnt)) {
                $kpi['refunds_count'] = (int)$row->cnt;
            }
        } catch (\Throwable $e) {
            $kpi['refunds_count'] = 0;
        }

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

    // ---------- Commission Management ----------

    /**
     * Get commission overview for summary cards: total commission this month, current rates, last updated.
     */
    public function getCommissionOverview() {
        $overview = [
            'total_commission_this_month' => 0,
            'guide_rate'                  => 15,
            'driver_rate'                 => 12,
            'last_updated'                => null,
        ];
        $this->db->query("
            SELECT COALESCE(SUM(commission), 0) AS total
            FROM payouts
            WHERE payout_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
              AND payout_date <= LAST_DAY(CURDATE())
        ");
        $row = $this->db->single();
        if ($row && isset($row->total)) {
            $overview['total_commission_this_month'] = (float) $row->total;
        }
        $this->db->query("SELECT role, rate, updated_at FROM commission_rates WHERE role IN ('guide', 'driver')");
        $rates = $this->db->resultSet();
        $lastDate = null;
        foreach ($rates as $r) {
            if ($r->role === 'guide') {
                $overview['guide_rate'] = (float) $r->rate;
            }
            if ($r->role === 'driver') {
                $overview['driver_rate'] = (float) $r->rate;
            }
            if (!empty($r->updated_at) && ($lastDate === null || strtotime($r->updated_at) > strtotime($lastDate))) {
                $lastDate = $r->updated_at;
            }
        }
        $overview['last_updated'] = $lastDate;
        return $overview;
    }

    /**
     * Get current commission rates with last updated and updated by name (for rate settings table).
     */
    public function getCommissionRates() {
        $this->db->query("
            SELECT cr.role, cr.rate, cr.updated_at, cr.updated_by, u.fullname AS updated_by_name
            FROM commission_rates cr
            LEFT JOIN users u ON u.id = cr.updated_by
            WHERE cr.role IN ('guide', 'driver')
            ORDER BY cr.role
        ");
        $rows = $this->db->resultSet();
        if (empty($rows)) {
            return [
                (object)['role' => 'guide', 'rate' => 15, 'updated_at' => null, 'updated_by' => null, 'updated_by_name' => null],
                (object)['role' => 'driver', 'rate' => 12, 'updated_at' => null, 'updated_by' => null, 'updated_by_name' => null],
            ];
        }
        return $rows;
    }

    /**
     * Get full commission change history for auditing.
     */
    public function getCommissionHistory($limit = 100) {
        $this->db->query("
            SELECT h.role, h.old_rate, h.new_rate, h.change_date, h.effective_from, h.reason,
                   u.fullname AS changed_by_name
            FROM commission_rate_history h
            LEFT JOIN users u ON u.id = h.changed_by
            ORDER BY h.change_date DESC
            LIMIT " . (int) $limit . "
        ");
        return $this->db->resultSet();
    }

    /**
     * Update commission rate and record in history. Returns true on success.
     */
    public function updateCommissionRate($role, $newRate, $effectiveFrom, $reason, $updatedByUserId) {
        $role = in_array($role, ['guide', 'driver'], true) ? $role : null;
        if ($role === null) {
            return false;
        }
        $newRate = max(0, min(100, (float) $newRate));
        $effectiveFrom = date('Y-m-d', strtotime($effectiveFrom));
        $reason = trim($reason);

        $this->db->query("SELECT rate FROM commission_rates WHERE role = :role LIMIT 1");
        $this->db->bind(':role', $role);
        $current = $this->db->single();
        $oldRate = $current ? (float) $current->rate : 0;

        $this->db->query("
            INSERT INTO commission_rate_history (role, old_rate, new_rate, changed_by, effective_from, reason)
            VALUES (:role, :old_rate, :new_rate, :changed_by, :effective_from, :reason)
        ");
        $this->db->bind(':role', $role);
        $this->db->bind(':old_rate', $oldRate);
        $this->db->bind(':new_rate', $newRate);
        $this->db->bind(':changed_by', $updatedByUserId ?: null);
        $this->db->bind(':effective_from', $effectiveFrom);
        $this->db->bind(':reason', $reason ?: null);
        if (!$this->db->execute()) {
            return false;
        }

        $this->db->query("
            UPDATE commission_rates SET rate = :rate, updated_at = NOW(), updated_by = :updated_by WHERE role = :role
        ");
        $this->db->bind(':rate', $newRate);
        $this->db->bind(':updated_by', $updatedByUserId ?: null);
        $this->db->bind(':role', $role);
        return $this->db->execute();
    }

    /**
     * Guide earnings breakdown: Guide Name, Total Revenue, Commission %, Commission Amount.
     * Filters: month (Y-m), dateFrom, dateTo, guideId (optional). Date filters apply to payout_date in JOIN.
     */
    public function getGuideEarningsBreakdown($month = null, $dateFrom = null, $dateTo = null, $guideId = null) {
        $joinExtra = ' AND p.payout_date IS NOT NULL';
        if ($month) {
            $joinExtra .= ' AND p.payout_date >= :month_from AND p.payout_date <= :month_to';
        }
        if ($dateFrom) {
            $joinExtra .= ' AND p.payout_date >= :date_from';
        }
        if ($dateTo) {
            $joinExtra .= ' AND p.payout_date <= :date_to';
        }
        $sql = "
            SELECT u.id, u.fullname AS guide_name,
                   COALESCE(SUM(p.earnings), 0) AS total_revenue,
                   COALESCE(SUM(p.commission), 0) AS commission_amount,
                   CASE WHEN SUM(p.earnings) > 0 THEN ROUND(100 * SUM(p.commission) / SUM(p.earnings), 2) ELSE 0 END AS commission_pct
            FROM users u
            LEFT JOIN payouts p ON p.userID = u.id
                AND (LOWER(TRIM(COALESCE(p.service_type,''))) LIKE '%guide%')
                " . $joinExtra . "
            WHERE u.account_type = 'guide'
        ";
        $params = [];
        if ($month) {
            $params['month_from'] = $month . '-01';
            $params['month_to'] = date('Y-m-t', strtotime($params['month_from']));
        }
        if ($dateFrom) {
            $params['date_from'] = $dateFrom;
        }
        if ($dateTo) {
            $params['date_to'] = $dateTo;
        }
        if ($guideId) {
            $sql .= " AND u.id = :guide_id";
            $params['guide_id'] = (int) $guideId;
        }
        $sql .= " GROUP BY u.id, u.fullname ORDER BY total_revenue DESC";
        $this->db->query($sql);
        foreach ($params as $k => $v) {
            $this->db->bind(':' . $k, $v);
        }
        return $this->db->resultSet();
    }

    /**
     * Driver earnings breakdown: Driver Name, Total Revenue, Commission %, Commission Amount.
     */
    public function getDriverEarningsBreakdown($month = null, $dateFrom = null, $dateTo = null, $driverId = null) {
        $joinExtra = ' AND p.payout_date IS NOT NULL';
        if ($month) {
            $joinExtra .= ' AND p.payout_date >= :month_from AND p.payout_date <= :month_to';
        }
        if ($dateFrom) {
            $joinExtra .= ' AND p.payout_date >= :date_from';
        }
        if ($dateTo) {
            $joinExtra .= ' AND p.payout_date <= :date_to';
        }
        $sql = "
            SELECT u.id, u.fullname AS driver_name,
                   COALESCE(SUM(p.earnings), 0) AS total_revenue,
                   COALESCE(SUM(p.commission), 0) AS commission_amount,
                   CASE WHEN SUM(p.earnings) > 0 THEN ROUND(100 * SUM(p.commission) / SUM(p.earnings), 2) ELSE 0 END AS commission_pct
            FROM users u
            LEFT JOIN payouts p ON p.userID = u.id
                AND (LOWER(TRIM(COALESCE(p.service_type,''))) LIKE '%driver%')
                " . $joinExtra . "
            WHERE u.account_type = 'driver'
        ";
        $params = [];
        if ($month) {
            $params['month_from'] = $month . '-01';
            $params['month_to'] = date('Y-m-t', strtotime($params['month_from']));
        }
        if ($dateFrom) {
            $params['date_from'] = $dateFrom;
        }
        if ($dateTo) {
            $params['date_to'] = $dateTo;
        }
        if ($driverId) {
            $sql .= " AND u.id = :driver_id";
            $params['driver_id'] = (int) $driverId;
        }
        $sql .= " GROUP BY u.id, u.fullname ORDER BY total_revenue DESC";
        $this->db->query($sql);
        foreach ($params as $k => $v) {
            $this->db->bind(':' . $k, $v);
        }
        return $this->db->resultSet();
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

