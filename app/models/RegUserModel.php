<?php
require_once __DIR__ . '/../helpers/trip_status_helper.php';

class RegUserModel {
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function getUserTrips($userId){

        $this->syncUserTripStatusesByConfirmations((int)$userId);
        $this->syncUserTripStatusesByDate((int)$userId);

        $query = 'SELECT tripId, userId, tripTitle , description, numberOfPeople, startDate, endDate, status, createdAt, updatedAt FROM created_trips WHERE userId = :userId ORDER BY createdAt DESC';

        $this->db->query($query);
        $this->db->bind(':userId',$userId);
        return $this->db->resultSet();
    }

    public function addCreatedTrip($insertingData){

        $query = 'INSERT INTO created_trips ( userId, tripTitle, description, numberOfPeople, startDate, endDate, status ) VALUES ( :userId, :tripTitle, :description, :numberOfPeople, :startDate, :endDate, :status)';
        
        $this->db->query($query);
        $this->db->bind(':userId',$insertingData['userId']);
        $this->db->bind(':tripTitle',$insertingData['tripTitle']);
        $this->db->bind(':description',$insertingData['description']);
        $this->db->bind(':numberOfPeople',$insertingData['numberOfPeople']);
        $this->db->bind(':startDate',$insertingData['startDate']);
        $this->db->bind(':endDate',$insertingData['endDate']);
        $this->db->bind(':status',$insertingData['status']);

        return $this->db->execute();
    }

    public function updateCreatedTrip($updatingData){
        //error_log('addCreatedTrip called with data: ' . print_r($updatingData, true));
        
        $query = "UPDATE created_trips SET 
                    tripTitle = :tripTitle,
                    description = :description,
                    numberOfPeople = :numberOfPeople,
                    startDate = :startDate,
                    endDate = :endDate,
                    updatedAt = CURRENT_TIMESTAMP
                    WHERE tripId = :tripId";
        
        $this->db->query($query);
        $this->db->bind(':tripId', $updatingData['tripId']);
        $this->db->bind(':tripTitle', $updatingData['tripTitle']);
        $this->db->bind(':description', $updatingData['description']);
        $this->db->bind(':numberOfPeople', $updatingData['numberOfPeople']);
        $this->db->bind(':startDate', $updatingData['startDate']);
        $this->db->bind(':endDate', $updatingData['endDate']);

        return $this->db->execute();

        //methana id kiyanne tripId eka 
    }

    public function deleteCreatedTrip($tripId) {
        $this->db->query('DELETE FROM created_trips WHERE tripId = :tripId');
        $this->db->bind(':tripId', $tripId);
        return $this->db->execute();
    }

    public function getTripById($tripId) {
        $this->db->query('SELECT * FROM created_trips WHERE tripId = :tripId');
        $this->db->bind(':tripId', $tripId);
        return $this->db->single();
    }

    public function getTripStatusForUser($userId, $tripId) {
        $this->syncTripLifecycleForUser((int)$userId, (int)$tripId);

        $this->db->query('SELECT status FROM created_trips WHERE tripId = :tripId AND userId = :userId LIMIT 1');
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':userId', $userId);
        $trip = $this->db->single();
        return $trip ? $trip->status : null;
    }

    public function isTripPendingForUser($userId, $tripId) {
        return $this->getTripStatusForUser($userId, $tripId) === 'pending';
    }

    public function syncTripLifecycleForUser($userId, $tripId) {
        $userId = (int)$userId;
        $tripId = (int)$tripId;

        $status = $this->moveTripToPendingIfRejected($userId, $tripId);
        if ($status === null) {
            return null;
        }

        if ($status === 'wConfirmation') {
            $status = $this->promoteTripToScheduledIfReady($userId, $tripId);
        } elseif ($status === 'awPayment') {
            $this->ensurePendingTripPaymentRecords($userId, $tripId);
        }

        if ($status === 'scheduled') {
            $status = $this->promoteTripToOngoingIfToday($userId, $tripId);
        }

        return $status;
    }

    private function getTripRevisionStats($userId, $tripId) {
        $this->db->query("SELECT
                (
                    SELECT COUNT(*)
                    FROM traveller_side_d_requests
                    WHERE tripId = :tripId1
                      AND rqUserId = :userId1
                      AND requestStatus IN ('accepted', 'rejected')
                ) AS driverResolved,
                (
                    SELECT COUNT(*)
                    FROM traveller_side_g_requests
                    WHERE tripId = :tripId2
                      AND userId = :userId2
                      AND guideId IS NOT NULL
                      AND (
                          status IN ('accepted', 'rejected')
                          OR respondedAt IS NOT NULL
                          OR acceptedAt IS NOT NULL
                          OR completedAt IS NOT NULL
                      )
                ) AS guideResolved,
                                (
                                        SELECT COUNT(*)
                                        FROM traveller_side_t_requests
                                        WHERE tripId = :tripId5
                                            AND rqUserId = :userId5
                                            AND requestStatus IN ('accepted', 'rejected')
                                ) AS driverProviderResolved,
                                (
                                        SELECT COUNT(*)
                                        FROM guide_side_g_requests
                                        WHERE tripId = :tripId6
                                            AND userId = :userId6
                                            AND status IN ('accepted', 'rejected')
                                ) AS guideProviderResolved,
                (
                    SELECT COUNT(*)
                    FROM traveller_side_d_requests
                    WHERE tripId = :tripId3
                      AND rqUserId = :userId3
                      AND requestStatus = 'rejected'
                ) AS rejectedDrivers,
                (
                    SELECT COUNT(*)
                    FROM traveller_side_g_requests
                    WHERE tripId = :tripId4
                      AND userId = :userId4
                      AND guideId IS NOT NULL
                      AND status = 'rejected'
                ) AS rejectedGuides");

        $this->db->bind(':tripId1', $tripId);
        $this->db->bind(':userId1', $userId);
        $this->db->bind(':tripId2', $tripId);
        $this->db->bind(':userId2', $userId);
        $this->db->bind(':tripId3', $tripId);
        $this->db->bind(':userId3', $userId);
        $this->db->bind(':tripId4', $tripId);
        $this->db->bind(':userId4', $userId);
        $this->db->bind(':tripId5', $tripId);
        $this->db->bind(':userId5', $userId);
        $this->db->bind(':tripId6', $tripId);
        $this->db->bind(':userId6', $userId);

        $row = $this->db->single();

        return [
            'driverResolved' => isset($row->driverResolved) ? (int)$row->driverResolved : 0,
            'guideResolved' => isset($row->guideResolved) ? (int)$row->guideResolved : 0,
            'driverProviderResolved' => isset($row->driverProviderResolved) ? (int)$row->driverProviderResolved : 0,
            'guideProviderResolved' => isset($row->guideProviderResolved) ? (int)$row->guideProviderResolved : 0,
            'rejectedDrivers' => isset($row->rejectedDrivers) ? (int)$row->rejectedDrivers : 0,
            'rejectedGuides' => isset($row->rejectedGuides) ? (int)$row->rejectedGuides : 0,
        ];
    }

    private function isTripInRevisionMode($userId, $tripId) {
        $stats = $this->getTripRevisionStats($userId, $tripId);
        return (
            $stats['driverResolved']
            + $stats['guideResolved']
            + $stats['driverProviderResolved']
            + $stats['guideProviderResolved']
        ) > 0;
    }

    public function getTripRevisionStateForUser($userId, $tripId) {
        $tripStatus = $this->getTripStatusForUser($userId, $tripId);

        if ($tripStatus === null) {
            return null;
        }

        $stats = $this->getTripRevisionStats($userId, $tripId);
        $revisionMode = (
            $stats['driverResolved']
            + $stats['guideResolved']
            + $stats['driverProviderResolved']
            + $stats['guideProviderResolved']
        ) > 0;

        return [
            'tripStatus' => $tripStatus,
            'revisionMode' => $revisionMode,
            'rejectedDriverCount' => $stats['rejectedDrivers'],
            'rejectedGuideCount' => $stats['rejectedGuides'],
            'eventChangesLocked' => ($tripStatus !== 'pending') || $revisionMode
        ];
    }

    public function hasRejectedProviderResponseForUser($userId, $tripId) {
        return $this->hasRejectedProviderResponse($userId, $tripId);
    }

    private function hasRejectedProviderResponse($userId, $tripId) {
        $this->db->query("SELECT
                (
                    SELECT COUNT(*)
                    FROM traveller_side_d_requests
                    WHERE tripId = :tripId1
                      AND rqUserId = :userId1
                      AND requestStatus = 'rejected'
                ) AS rejectedDriverTraveller,
                (
                    SELECT COUNT(*)
                    FROM traveller_side_g_requests
                    WHERE tripId = :tripId2
                      AND userId = :userId2
                      AND guideId IS NOT NULL
                      AND status = 'rejected'
                ) AS rejectedGuideTraveller,
                (
                    SELECT COUNT(*)
                                        FROM traveller_side_t_requests t
                                        INNER JOIN traveller_side_d_requests d
                                                ON d.tripId = t.tripId
                                             AND d.rqUserId = t.rqUserId
                                             AND d.driverId = t.driverId
                                        WHERE t.tripId = :tripId3
                                            AND t.rqUserId = :userId3
                                            AND t.requestStatus = 'rejected'
                                            AND d.requestStatus IN ('pending', 'requested', 'accepted', 'rejected')
                                            AND (d.requestedAt IS NULL OR t.respondedAt IS NULL OR t.respondedAt >= d.requestedAt)
                ) AS rejectedDriverProvider,
                (
                    SELECT COUNT(*)
                                        FROM guide_side_g_requests g
                                        INNER JOIN traveller_side_g_requests tg
                                                ON tg.tripId = g.tripId
                                             AND tg.eventId = g.eventId
                                             AND tg.userId = g.userId
                                             AND ((tg.guideId = g.guideId) OR (tg.guideId IS NULL AND g.guideId IS NULL))
                                        WHERE g.tripId = :tripId4
                                            AND g.userId = :userId4
                                            AND g.status = 'rejected'
                                            AND tg.status IN ('pending', 'requested', 'accepted', 'rejected')
                                            AND (tg.requestedAt IS NULL OR g.respondedAt IS NULL OR g.respondedAt >= tg.requestedAt)
                ) AS rejectedGuideProvider");

        $this->db->bind(':tripId1', $tripId);
        $this->db->bind(':userId1', $userId);
        $this->db->bind(':tripId2', $tripId);
        $this->db->bind(':userId2', $userId);
        $this->db->bind(':tripId3', $tripId);
        $this->db->bind(':userId3', $userId);
        $this->db->bind(':tripId4', $tripId);
        $this->db->bind(':userId4', $userId);

        $row = $this->db->single();
        if (!$row) {
            return false;
        }

        $rejectedCount = (int)($row->rejectedDriverTraveller ?? 0)
            + (int)($row->rejectedGuideTraveller ?? 0)
            + (int)($row->rejectedDriverProvider ?? 0)
            + (int)($row->rejectedGuideProvider ?? 0);

        return $rejectedCount > 0;
    }

    public function moveTripToPendingIfRejected($userId, $tripId) {
        $this->db->query('SELECT status FROM created_trips WHERE tripId = :tripId AND userId = :userId LIMIT 1');
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':userId', $userId);
        $trip = $this->db->single();

        if (!$trip) {
            return null;
        }

        if (!in_array($trip->status, ['wConfirmation', 'awPayment', 'scheduled'], true)) {
            return $trip->status;
        }

        if (!$this->hasRejectedProviderResponse($userId, $tripId)) {
            return $trip->status;
        }

        $this->db->query('UPDATE created_trips
            SET status = :nextStatus,
                updatedAt = CURRENT_TIMESTAMP
            WHERE tripId = :tripId
              AND userId = :userId
              AND status IN (\'wConfirmation\', \'awPayment\', \'scheduled\')');
        $this->db->bind(':nextStatus', 'pending');
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':userId', $userId);

        if (!$this->db->execute()) {
            return $trip->status;
        }

        return 'pending';
    }

    private function getDriverConfirmationStats($userId, $tripId) {
        $this->db->query("SELECT
                COUNT(*) AS totalRequests,
                SUM(CASE WHEN requestStatus = 'accepted' THEN 1 ELSE 0 END) AS acceptedRequests
            FROM traveller_side_d_requests
                        WHERE tripId = :tripId
                            AND rqUserId = :userId
                            AND requestStatus IN ('pending', 'requested', 'accepted', 'rejected')");
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':userId', $userId);
        $row = $this->db->single();

        return [
            'total' => isset($row->totalRequests) ? (int)$row->totalRequests : 0,
            'accepted' => isset($row->acceptedRequests) ? (int)$row->acceptedRequests : 0
        ];
    }

    private function getGuideConfirmationStats($userId, $tripId) {
        $this->db->query("SELECT
                COUNT(*) AS totalRequests,
                SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) AS acceptedRequests
            FROM traveller_side_g_requests
            WHERE tripId = :tripId
              AND userId = :userId
                            AND guideId IS NOT NULL
                                                        AND status IN ('pending', 'requested', 'accepted', 'rejected')");
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':userId', $userId);
        $row = $this->db->single();

        return [
            'total' => isset($row->totalRequests) ? (int)$row->totalRequests : 0,
            'accepted' => isset($row->acceptedRequests) ? (int)$row->acceptedRequests : 0
        ];
    }

    private function hasAllProviderConfirmations($userId, $tripId) {
        if ($this->hasRejectedProviderResponse($userId, $tripId)) {
            return false;
        }

        $driverStats = $this->getDriverConfirmationStats($userId, $tripId);
        if ($driverStats['total'] === 0 || $driverStats['accepted'] < $driverStats['total']) {
            return false;
        }

        $guideStats = $this->getGuideConfirmationStats($userId, $tripId);
        return $guideStats['accepted'] >= $guideStats['total'];
    }

    public function promoteTripToScheduledIfReady($userId, $tripId) {
        $this->db->query('SELECT status FROM created_trips WHERE tripId = :tripId AND userId = :userId LIMIT 1');
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':userId', $userId);
        $trip = $this->db->single();

        if (!$trip) {
            return null;
        }

        if ($trip->status === 'awPayment') {
            $this->ensurePendingTripPaymentRecords((int)$userId, (int)$tripId);
            return 'awPayment';
        }

        if ($trip->status !== 'wConfirmation') {
            return $trip->status;
        }

        if (!$this->hasAllProviderConfirmations($userId, $tripId)) {
            return 'wConfirmation';
        }

        $this->db->query('UPDATE created_trips
            SET status = :nextStatus,
                updatedAt = CURRENT_TIMESTAMP
            WHERE tripId = :tripId
              AND userId = :userId
              AND status = :currentStatus');
        $this->db->bind(':nextStatus', 'awPayment');
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':userId', $userId);
        $this->db->bind(':currentStatus', 'wConfirmation');

        if (!$this->db->execute()) {
            return 'wConfirmation';
        }

        if ($this->db->rowCount() > 0) {
            $this->ensurePendingTripPaymentRecords((int)$userId, (int)$tripId);
            return 'awPayment';
        }

        $this->db->query('SELECT status FROM created_trips WHERE tripId = :tripId AND userId = :userId LIMIT 1');
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':userId', $userId);
        $updatedTrip = $this->db->single();

        if ($updatedTrip && $updatedTrip->status === 'awPayment') {
            $this->ensurePendingTripPaymentRecords((int)$userId, (int)$tripId);
        }

        return $updatedTrip ? $updatedTrip->status : null;
    }

    private function promoteTripToOngoingIfToday($userId, $tripId) {
        $this->db->query('SELECT status, startDate, endDate FROM created_trips WHERE tripId = :tripId AND userId = :userId LIMIT 1');
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':userId', $userId);
        $trip = $this->db->single();

        if (!$trip) {
            return null;
        }

        if (!shouldMoveTripToOngoingToday($trip->status, $trip->startDate, $trip->endDate)) {
            return $trip->status;
        }

        $this->db->query('UPDATE created_trips
            SET status = :nextStatus,
                updatedAt = CURRENT_TIMESTAMP
            WHERE tripId = :tripId
              AND userId = :userId
              AND status = :currentStatus');
        $this->db->bind(':nextStatus', 'ongoing');
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':userId', $userId);
        $this->db->bind(':currentStatus', 'scheduled');

        if ($this->db->execute() && $this->db->rowCount() > 0) {
            return 'ongoing';
        }

        $this->db->query('SELECT status FROM created_trips WHERE tripId = :tripId AND userId = :userId LIMIT 1');
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':userId', $userId);
        $updatedTrip = $this->db->single();

        return $updatedTrip ? $updatedTrip->status : $trip->status;
    }

    private function syncUserTripStatusesByDate($userId) {
        $this->db->query('SELECT tripId, status, startDate, endDate FROM created_trips WHERE userId = :userId AND status = :status');
        $this->db->bind(':userId', $userId);
        $this->db->bind(':status', 'scheduled');
        $trips = $this->db->resultSet();

        if (!$trips) {
            return;
        }

        foreach ($trips as $trip) {
            if (shouldMoveTripToOngoingToday($trip->status, $trip->startDate, $trip->endDate)) {
                $this->promoteTripToOngoingIfToday((int)$userId, (int)$trip->tripId);
            }
        }
    }

    private function syncUserTripStatusesByConfirmations($userId) {
        $this->db->query('SELECT tripId, status FROM created_trips WHERE userId = :userId AND status IN (\'wConfirmation\', \'awPayment\', \'scheduled\')');
        $this->db->bind(':userId', $userId);
        $trips = $this->db->resultSet();

        if (!$trips) {
            return;
        }

        foreach ($trips as $trip) {
            $tripId = (int)$trip->tripId;
            $status = $this->moveTripToPendingIfRejected((int)$userId, $tripId);
            if ($status === 'wConfirmation') {
                $this->promoteTripToScheduledIfReady((int)$userId, $tripId);
            } elseif ($status === 'awPayment') {
                $this->ensurePendingTripPaymentRecords((int)$userId, $tripId);
            }
        }
    }

    private function getTripDurationDays($startDate, $endDate) {
        try {
            $start = new DateTime((string)$startDate);
            $end = new DateTime((string)$endDate);
            if ($end < $start) {
                return 1;
            }

            return (int)$start->diff($end)->days + 1;
        } catch (Exception $e) {
            return 1;
        }
    }

    private function getLatestSiteServiceChargesForTripPayment() {
        $this->db->query("SELECT dBookCharge, gBookCharge, siteServiceCharge
                        FROM site_service_charges
                        WHERE isActive = TRUE
                        ORDER BY createdAt DESC
                        LIMIT 1");
        $row = $this->db->single();

        if (!$row) {
            return [
                'dBookCharge' => 0.00,
                'gBookCharge' => 0.00,
                'siteServiceCharge' => 0.00
            ];
        }

        return [
            'dBookCharge' => (float)$row->dBookCharge,
            'gBookCharge' => (float)$row->gBookCharge,
            'siteServiceCharge' => (float)$row->siteServiceCharge
        ];
    }

    private function collectAcceptedDriverCharges($userId, $tripId, $tripDurationDays) {
        $this->db->query("SELECT driverId, chargeType, totalAmount
                        FROM traveller_side_d_requests
                        WHERE tripId = :tripId
                          AND rqUserId = :userId
                          AND requestStatus = 'accepted'");
        $this->db->bind(':tripId', (int)$tripId);
        $this->db->bind(':userId', (int)$userId);
        $rows = $this->db->resultSet();

        $chargesByDriver = [];

        foreach ($rows as $row) {
            $driverId = (int)($row->driverId ?? 0);
            if ($driverId <= 0) {
                continue;
            }

            $normalizedChargeType = strtolower(str_replace(['-', ' '], '_', (string)($row->chargeType ?? '')));
            $charge = max(0.0, (float)($row->totalAmount ?? 0));

            if (in_array($normalizedChargeType, ['perday', 'per_day', 'daily', 'day'], true)) {
                $charge *= max(1, (int)$tripDurationDays);
            }

            $charge = round($charge, 2);
            if ($charge <= 0) {
                continue;
            }

            if (!isset($chargesByDriver[$driverId])) {
                $chargesByDriver[$driverId] = 0.0;
            }

            $chargesByDriver[$driverId] += $charge;
        }

        $result = [];
        foreach ($chargesByDriver as $driverId => $driverCharge) {
            $result[] = [
                'driverId' => (int)$driverId,
                'driverCharge' => round((float)$driverCharge, 2)
            ];
        }

        return $result;
    }

    private function collectAcceptedGuideCharges($userId, $tripId) {
        $this->db->query("SELECT guideId, totalCharge
                        FROM traveller_side_g_requests
                        WHERE tripId = :tripId
                          AND userId = :userId
                          AND guideId IS NOT NULL
                          AND status = 'accepted'");
        $this->db->bind(':tripId', (int)$tripId);
        $this->db->bind(':userId', (int)$userId);
        $rows = $this->db->resultSet();

        $chargesByGuide = [];

        foreach ($rows as $row) {
            $guideId = (int)($row->guideId ?? 0);
            if ($guideId <= 0) {
                continue;
            }

            $charge = round(max(0.0, (float)($row->totalCharge ?? 0)), 2);
            if ($charge <= 0) {
                continue;
            }

            if (!isset($chargesByGuide[$guideId])) {
                $chargesByGuide[$guideId] = 0.0;
            }

            $chargesByGuide[$guideId] += $charge;
        }

        $result = [];
        foreach ($chargesByGuide as $guideId => $guideCharge) {
            $result[] = [
                'guideId' => (int)$guideId,
                'guideCharge' => round((float)$guideCharge, 2)
            ];
        }

        return $result;
    }

    private function getLatestTripPaymentRow($userId, $tripId) {
        $this->db->query("SELECT wholePaymentId, paymentStatus, totalCharge, transactionId, createdAt
                        FROM trip_payments
                        WHERE tripId = :tripId
                          AND travellerId = :travellerId
                        ORDER BY wholePaymentId DESC
                        LIMIT 1");
        $this->db->bind(':tripId', (int)$tripId);
        $this->db->bind(':travellerId', (int)$userId);
        return $this->db->single();
    }

    public function ensurePendingTripPaymentRecords($userId, $tripId) {
        $userId = (int)$userId;
        $tripId = (int)$tripId;

        $this->db->query("SELECT tripId, userId, startDate, endDate, status
                        FROM created_trips
                        WHERE tripId = :tripId AND userId = :userId
                        LIMIT 1");
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':userId', $userId);
        $trip = $this->db->single();

        if (!$trip) {
            return [
                'success' => false,
                'message' => 'Trip not found'
            ];
        }

        if (!in_array((string)$trip->status, ['awPayment', 'scheduled'], true)) {
            return [
                'success' => false,
                'message' => 'Trip is not awaiting payment',
                'status' => (string)$trip->status
            ];
        }

        $existingPayment = $this->getLatestTripPaymentRow($userId, $tripId);
        if ($existingPayment && in_array((string)$existingPayment->paymentStatus, ['pending', 'completed'], true)) {
            return [
                'success' => true,
                'wholePaymentId' => (int)$existingPayment->wholePaymentId,
                'paymentStatus' => (string)$existingPayment->paymentStatus,
                'totalCharge' => (float)$existingPayment->totalCharge,
                'transactionId' => $existingPayment->transactionId ?? null
            ];
        }

        $tripDurationDays = $this->getTripDurationDays($trip->startDate, $trip->endDate);
        $driverCharges = $this->collectAcceptedDriverCharges($userId, $tripId, $tripDurationDays);
        $guideCharges = $this->collectAcceptedGuideCharges($userId, $tripId);

        $driverChargeTotal = 0.0;
        foreach ($driverCharges as $row) {
            $driverChargeTotal += (float)$row['driverCharge'];
        }

        $guideChargeTotal = 0.0;
        foreach ($guideCharges as $row) {
            $guideChargeTotal += (float)$row['guideCharge'];
        }

        $providerChargeTotal = round($driverChargeTotal + $guideChargeTotal, 2);
        $siteCharges = $this->getLatestSiteServiceChargesForTripPayment();

        $driverBookingSiteCharge = round(max(0.0, (float)$siteCharges['dBookCharge']) * count($driverCharges), 2);
        $guideBookingSiteCharge = round(max(0.0, (float)$siteCharges['gBookCharge']) * count($guideCharges), 2);
        $serviceChargeRate = max(0.0, (float)$siteCharges['siteServiceCharge']);
        $serviceChargeAmount = round($providerChargeTotal * ($serviceChargeRate / 100), 2);

        $siteChargeTotal = round($driverBookingSiteCharge + $guideBookingSiteCharge + $serviceChargeAmount, 2);
        $overallTotal = round($providerChargeTotal + $siteChargeTotal, 2);

        $this->db->beginTransaction();

        try {
            $this->db->query("INSERT INTO trip_payments
                            (tripId, travellerId, paymentStatus, totalCharge, transactionId)
                            VALUES
                            (:tripId, :travellerId, 'pending', :totalCharge, NULL)");
            $this->db->bind(':tripId', $tripId);
            $this->db->bind(':travellerId', $userId);
            $this->db->bind(':totalCharge', $overallTotal);

            if (!$this->db->execute()) {
                $this->db->rollback();
                return [
                    'success' => false,
                    'message' => 'Failed to create trip payment record'
                ];
            }

            $wholePaymentId = (int)$this->db->lastInsertId();

            foreach ($driverCharges as $row) {
                $driverChargeInt = (int)round((float)$row['driverCharge']);

                $this->db->query("INSERT INTO driver_payments
                                (wholePaymentId, driverId, driverCharge)
                                VALUES
                                (:wholePaymentId, :driverId, :driverCharge)");
                $this->db->bind(':wholePaymentId', $wholePaymentId);
                $this->db->bind(':driverId', (int)$row['driverId']);
                $this->db->bind(':driverCharge', $driverChargeInt);

                if (!$this->db->execute()) {
                    $this->db->rollback();
                    return [
                        'success' => false,
                        'message' => 'Failed to create driver payment record'
                    ];
                }
            }

            foreach ($guideCharges as $row) {
                $guideChargeInt = (int)round((float)$row['guideCharge']);

                $this->db->query("INSERT INTO guide_payments
                                (wholePaymentId, guideId, guideCharge)
                                VALUES
                                (:wholePaymentId, :guideId, :guideCharge)");
                $this->db->bind(':wholePaymentId', $wholePaymentId);
                $this->db->bind(':guideId', (int)$row['guideId']);
                $this->db->bind(':guideCharge', $guideChargeInt);

                if (!$this->db->execute()) {
                    $this->db->rollback();
                    return [
                        'success' => false,
                        'message' => 'Failed to create guide payment record'
                    ];
                }
            }

            $this->db->commit();

            return [
                'success' => true,
                'wholePaymentId' => $wholePaymentId,
                'paymentStatus' => 'pending',
                'totalCharge' => $overallTotal,
                'driverChargeTotal' => $driverChargeTotal,
                'guideChargeTotal' => $guideChargeTotal,
                'siteChargeTotal' => $siteChargeTotal,
                'driverBookingSiteCharge' => $driverBookingSiteCharge,
                'guideBookingSiteCharge' => $guideBookingSiteCharge,
                'serviceChargeRate' => $serviceChargeRate,
                'serviceChargeAmount' => $serviceChargeAmount
            ];
        } catch (Throwable $e) {
            $this->db->rollback();
            return [
                'success' => false,
                'message' => 'Failed to initialize pending payment records'
            ];
        }
    }

    public function getTripPaymentSummary($userId, $tripId) {
        $userId = (int)$userId;
        $tripId = (int)$tripId;

        $this->db->query("SELECT tripId, userId, tripTitle, status
                        FROM created_trips
                        WHERE tripId = :tripId
                          AND userId = :userId
                        LIMIT 1");
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':userId', $userId);
        $trip = $this->db->single();

        if (!$trip) {
            return [
                'success' => false,
                'message' => 'Trip not found'
            ];
        }

        if ((string)$trip->status === 'awPayment') {
            $initResult = $this->ensurePendingTripPaymentRecords($userId, $tripId);
            if (empty($initResult['success'])) {
                return $initResult;
            }
        }

        $paymentRow = $this->getLatestTripPaymentRow($userId, $tripId);
        if (!$paymentRow) {
            return [
                'success' => false,
                'message' => 'No payment record found for this trip'
            ];
        }

        $wholePaymentId = (int)$paymentRow->wholePaymentId;

        $this->db->query("SELECT COALESCE(SUM(driverCharge), 0) AS totalDriverCharge
                        FROM driver_payments
                        WHERE wholePaymentId = :wholePaymentId");
        $this->db->bind(':wholePaymentId', $wholePaymentId);
        $driverTotals = $this->db->single();

        $this->db->query("SELECT COALESCE(SUM(guideCharge), 0) AS totalGuideCharge
                        FROM guide_payments
                        WHERE wholePaymentId = :wholePaymentId");
        $this->db->bind(':wholePaymentId', $wholePaymentId);
        $guideTotals = $this->db->single();

        $this->db->query("SELECT COUNT(*) AS driverBookingCount
                FROM driver_payments
                WHERE wholePaymentId = :wholePaymentId");
        $this->db->bind(':wholePaymentId', $wholePaymentId);
        $driverCountRow = $this->db->single();

        $this->db->query("SELECT COUNT(*) AS guideBookingCount
                FROM guide_payments
                WHERE wholePaymentId = :wholePaymentId");
        $this->db->bind(':wholePaymentId', $wholePaymentId);
        $guideCountRow = $this->db->single();

        $driverChargeTotal = round((float)($driverTotals->totalDriverCharge ?? 0), 2);
        $guideChargeTotal = round((float)($guideTotals->totalGuideCharge ?? 0), 2);
        $providerChargeTotal = round($driverChargeTotal + $guideChargeTotal, 2);
        $overallTotal = round((float)$paymentRow->totalCharge, 2);
        $siteChargeTotal = round(max(0.0, $overallTotal - $providerChargeTotal), 2);

        $driverBookingCount = (int)($driverCountRow->driverBookingCount ?? 0);
        $guideBookingCount = (int)($guideCountRow->guideBookingCount ?? 0);

        $siteCharges = $this->getLatestSiteServiceChargesForTripPayment();
        $driverBookingUnitCharge = round(max(0.0, (float)$siteCharges['dBookCharge']), 2);
        $guideBookingUnitCharge = round(max(0.0, (float)$siteCharges['gBookCharge']), 2);
        $serviceChargeRate = round(max(0.0, (float)$siteCharges['siteServiceCharge']), 2);

        $driverBookingSiteCharge = round($driverBookingUnitCharge * $driverBookingCount, 2);
        $guideBookingSiteCharge = round($guideBookingUnitCharge * $guideBookingCount, 2);
        $serviceChargeAmount = round(max(0.0, $siteChargeTotal - $driverBookingSiteCharge - $guideBookingSiteCharge), 2);

        $this->db->query("SELECT fullname, email, phone, address
                        FROM users
                        WHERE id = :userId
                        LIMIT 1");
        $this->db->bind(':userId', $userId);
        $traveller = $this->db->single();

        return [
            'success' => true,
            'tripId' => $tripId,
            'tripTitle' => $trip->tripTitle ?? 'Trip Payment',
            'tripStatus' => (string)$trip->status,
            'wholePaymentId' => $wholePaymentId,
            'paymentStatus' => (string)$paymentRow->paymentStatus,
            'transactionId' => $paymentRow->transactionId ?? null,
            'totalCharge' => $overallTotal,
            'driverChargeTotal' => $driverChargeTotal,
            'guideChargeTotal' => $guideChargeTotal,
            'providerChargeTotal' => $providerChargeTotal,
            'siteChargeTotal' => $siteChargeTotal,
            'driverBookingCount' => $driverBookingCount,
            'guideBookingCount' => $guideBookingCount,
            'driverBookingUnitCharge' => $driverBookingUnitCharge,
            'guideBookingUnitCharge' => $guideBookingUnitCharge,
            'driverBookingSiteCharge' => $driverBookingSiteCharge,
            'guideBookingSiteCharge' => $guideBookingSiteCharge,
            'serviceChargeRate' => $serviceChargeRate,
            'serviceChargeAmount' => $serviceChargeAmount,
            'traveller' => [
                'fullName' => $traveller->fullname ?? '',
                'email' => $traveller->email ?? '',
                'phone' => $traveller->phone ?? '',
                'address' => $traveller->address ?? ''
            ]
        ];
    }

    public function completeTripPayment($tripId, $wholePaymentId, $transactionId = null) {
        $tripId = (int)$tripId;
        $wholePaymentId = (int)$wholePaymentId;

        $this->db->query("SELECT wholePaymentId, tripId, travellerId, paymentStatus
                        FROM trip_payments
                        WHERE wholePaymentId = :wholePaymentId
                          AND tripId = :tripId
                        LIMIT 1");
        $this->db->bind(':wholePaymentId', $wholePaymentId);
        $this->db->bind(':tripId', $tripId);
        $payment = $this->db->single();

        if (!$payment) {
            return [
                'success' => false,
                'message' => 'Payment record not found'
            ];
        }

        $paymentStatus = strtolower((string)($payment->paymentStatus ?? ''));
        if ($paymentStatus === 'completed') {
            return [
                'success' => true,
                'tripId' => $tripId,
                'wholePaymentId' => $wholePaymentId,
                'alreadyCompleted' => true
            ];
        }

        if ($paymentStatus !== 'pending') {
            return [
                'success' => false,
                'message' => 'Payment record is not pending'
            ];
        }

        $travellerId = (int)$payment->travellerId;

        $this->db->beginTransaction();

        try {
            $transactionIdValue = is_string($transactionId)
                ? trim($transactionId)
                : $transactionId;

            if ($transactionIdValue === null || $transactionIdValue === '') {
                $this->db->query("UPDATE trip_payments
                                SET paymentStatus = 'completed',
                                    updatedAt = CURRENT_TIMESTAMP
                                WHERE wholePaymentId = :wholePaymentId
                                  AND tripId = :tripId
                                  AND travellerId = :travellerId
                                  AND paymentStatus = 'pending'");
                $this->db->bind(':wholePaymentId', $wholePaymentId);
                $this->db->bind(':tripId', $tripId);
                $this->db->bind(':travellerId', $travellerId);
                $this->db->execute();
            } else {
                $this->db->query("UPDATE trip_payments
                                SET paymentStatus = 'completed',
                                    transactionId = :transactionId,
                                    updatedAt = CURRENT_TIMESTAMP
                                WHERE wholePaymentId = :wholePaymentId
                                  AND tripId = :tripId
                                  AND travellerId = :travellerId
                                                                    AND paymentStatus = 'pending'");
                $this->db->bind(':transactionId', $transactionIdValue);
                $this->db->bind(':wholePaymentId', $wholePaymentId);
                $this->db->bind(':tripId', $tripId);
                $this->db->bind(':travellerId', $travellerId);
                $this->db->execute();
            }

            $this->db->query("UPDATE driver_payments
                            SET pDoneTraveller = TRUE,
                                pDateTraveller = COALESCE(pDateTraveller, CURRENT_TIMESTAMP),
                                updatedAt = CURRENT_TIMESTAMP
                            WHERE wholePaymentId = :wholePaymentId");
            $this->db->bind(':wholePaymentId', $wholePaymentId);
            $this->db->execute();

            $this->db->query("UPDATE guide_payments
                            SET pDoneTraveller = TRUE,
                                pDateTraveller = COALESCE(pDateTraveller, CURRENT_TIMESTAMP),
                                updatedAt = CURRENT_TIMESTAMP
                            WHERE wholePaymentId = :wholePaymentId");
            $this->db->bind(':wholePaymentId', $wholePaymentId);
            $this->db->execute();

            $this->db->query("UPDATE created_trips
                            SET status = 'scheduled',
                                updatedAt = CURRENT_TIMESTAMP
                            WHERE tripId = :tripId
                              AND userId = :travellerId
                              AND status = 'awPayment'");
            $this->db->bind(':tripId', $tripId);
            $this->db->bind(':travellerId', $travellerId);
            $this->db->execute();

            $this->db->commit();

            return [
                'success' => true,
                'tripId' => $tripId,
                'wholePaymentId' => $wholePaymentId
            ];
        } catch (Throwable $e) {
            $this->db->rollback();
            error_log('completeTripPayment failed for trip ' . $tripId . ' wholePaymentId ' . $wholePaymentId . ': ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to complete trip payment'
            ];
        }
    }

    public function cancelPendingTripPayment($tripId, $wholePaymentId) {
        $this->db->query("UPDATE trip_payments
                        SET paymentStatus = 'cancelled',
                            updatedAt = CURRENT_TIMESTAMP
                        WHERE wholePaymentId = :wholePaymentId
                          AND tripId = :tripId
                          AND paymentStatus = 'pending'");
        $this->db->bind(':wholePaymentId', (int)$wholePaymentId);
        $this->db->bind(':tripId', (int)$tripId);

        return $this->db->execute();
    }

    public function addEventData($insertingData){

        if(isset($insertingData['locationName'])) {
            $query = 'INSERT INTO trip_events ( userId, tripId, eventDate, startTime, endTime, eventType, eventStatus, locationName, latitude, longitude, description ) VALUES ( :userId, :tripId, :eventDate, :startTime, :endTime, :eventType, :eventStatus, :locationName, :latitude, :longitude, :description )';
            
            $this->db->query($query);
            $this->db->bind(':userId',$insertingData['userId']);
            $this->db->bind(':tripId',$insertingData['tripId']);
            $this->db->bind(':eventDate',$insertingData['eventDate']);
            $this->db->bind(':startTime',$insertingData['startTime']);
            $this->db->bind(':endTime',$insertingData['endTime']);

            $this->db->bind(':eventType',$insertingData['eventType']);
            $this->db->bind(':eventStatus',$insertingData['eventStatus']);
            $this->db->bind(':locationName',$insertingData['locationName']);
            $this->db->bind(':latitude',$insertingData['latitude']);
            $this->db->bind(':longitude',$insertingData['longitude']);
            $this->db->bind(':description',$insertingData['description']);

            if($this->db->execute()) {
                return $this->db->lastInsertId();
            }
            return false;

        } else {
            $query = 'INSERT INTO trip_events ( userId, tripId, eventDate, startTime, endTime, eventType, eventStatus, travelSpotId ) VALUES ( :userId, :tripId, :eventDate, :startTime, :endTime, :eventType, :eventStatus, :travelSpotId )';

            $this->db->query($query);   
            $this->db->bind(':userId',$insertingData['userId']);
            $this->db->bind(':tripId',$insertingData['tripId']);
            $this->db->bind(':eventDate',$insertingData['eventDate']);
            $this->db->bind(':startTime',$insertingData['startTime']);
            $this->db->bind(':endTime',$insertingData['endTime']);
            $this->db->bind(':eventType',$insertingData['eventType']);
            $this->db->bind(':eventStatus',$insertingData['eventStatus']);
            $this->db->bind(':travelSpotId',$insertingData['travelSpotId']);

            if($this->db->execute()) {
                return $this->db->lastInsertId();
            }
            return false;
        }
        
    }
   
    public function updateEvent($updatingData){

        if($updatingData['eventType'] === 'travelSpot') {
                $query = "UPDATE trip_events SET 
                        startTime = :startTime,
                        endTime = :endTime,
                        eventType = :eventType,
                        eventStatus = :eventStatus,
                        travelSpotId = :travelSpotId
                        WHERE eventId = :eventId AND userId = :userId";

            $this->db->query($query);
            $this->db->bind(':eventId', $updatingData['eventId']);
            $this->db->bind(':userId', $updatingData['userId']);
            $this->db->bind(':startTime', $updatingData['startTime']);
                $this->db->bind(':endTime', $updatingData['endTime']);
            
            $this->db->bind(':eventType', $updatingData['eventType']);
            $this->db->bind(':eventStatus', $updatingData['eventStatus']);
            $this->db->bind(':travelSpotId', $updatingData['travelSpotId']);

            return $this->db->execute();
        } else {
            $query = "UPDATE trip_events SET 
                    startTime = :startTime,
                    endTime = :endTime,
                    eventType = :eventType,
                    eventStatus = :eventStatus,
                    locationName = :locationName,
                    latitude = :latitude,
                    longitude = :longitude,
                    description = :description
                    WHERE eventId = :eventId AND userId = :userId";
        
            $this->db->query($query);
            $this->db->bind(':eventId', $updatingData['eventId']);
            $this->db->bind(':userId', $updatingData['userId']);
            $this->db->bind(':startTime', $updatingData['startTime']);
            $this->db->bind(':endTime', $updatingData['endTime']);

            $this->db->bind(':eventType', $updatingData['eventType']);
            $this->db->bind(':eventStatus', $updatingData['eventStatus']);
            $this->db->bind(':locationName', $updatingData['locationName']);
            $this->db->bind(':latitude', $updatingData['latitude']);
            $this->db->bind(':longitude', $updatingData['longitude']);
            $this->db->bind(':description', $updatingData['description']);
                
            return $this->db->execute();
        }
        
    }

    public function getEventCardsByDate($userId,$tripId, $eventDate) {

        $query = "
                            SELECT *
                    FROM trip_events
                    WHERE userId = :userId 
                    AND tripId = :tripId 
                    AND eventDate = :eventDate
                    ORDER BY 
                    
                    CASE eventStatus
                        WHEN 'start' THEN 1
                        WHEN 'intermediate' THEN 2
                        WHEN 'end' THEN 3
                        ELSE 4
                    END,
                    -- Then within 'intermediate', order by startTime ASC (earliest first)
                    CASE 
                        WHEN eventStatus = 'intermediate' THEN startTime
                        ELSE NULL
                    END ASC,
                    -- Then order remaining events by startTime (optional)
                    startTime ASC
                            ";

        $this->db->query($query);
        $this->db->bind(':userId', $userId);
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':eventDate', $eventDate);
        return $this->db->resultSet();
    }

    public function deleteEvent($userId, $tripId, $eventId) {
        $this->db->query('DELETE FROM trip_events WHERE eventId = :eventId AND userId = :userId AND tripId = :tripId');
        $this->db->bind(':eventId', $eventId);
        $this->db->bind(':userId', $userId);
        $this->db->bind(':tripId', $tripId);
        return $this->db->execute();
    }

    public function getEventData($userId, $tripId, $eventId) {
        $this->db->query('SELECT * FROM trip_events WHERE eventId = :eventId AND userId = :userId AND tripId = :tripId');
        $this->db->bind(':eventId', $eventId);
        $this->db->bind(':userId', $userId);
        $this->db->bind(':tripId', $tripId);
        return $this->db->single();
    }

    public function getLastAddedEvent($userId, $tripId, $eventDate) {
        $this->db->query('SELECT *,
                            CASE
                                WHEN eventStatus = \'start\' THEN startTime
                                ELSE endTime
                            END AS effectiveEndTime
                        FROM trip_events 
                        WHERE userId = :userId 
                        AND tripId = :tripId 
                        AND eventDate = :eventDate 
                        ORDER BY CAST(
                            CASE
                                WHEN eventStatus = \'start\' THEN startTime
                                ELSE endTime
                            END AS TIME
                        ) DESC, eventId DESC LIMIT 1');
        
        $this->db->bind(':userId', $userId);
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':eventDate', $eventDate);
        
        $result = $this->db->single();
        return $result;
    }

    public function getAboveEventEndTime($userId, $tripId, $eventId, $eventDate) {
        $this->db->query('
                            SELECT te.* 
                            FROM trip_events te
                            WHERE te.userId = :userId 
                            AND te.tripId = :tripId 
                            AND te.eventId <> :eventId
                            AND te.eventDate = :eventDate
                            AND CAST(
                                CASE
                                    WHEN te.eventStatus = :startStatus1 THEN te.startTime
                                    ELSE te.endTime
                                END AS TIME
                            ) <= (
                                SELECT CAST(startTime AS TIME) 
                                FROM trip_events 
                                WHERE eventId = :eventId
                            )
                            ORDER BY CAST(
                                CASE
                                    WHEN te.eventStatus = :startStatus2 THEN te.startTime
                                    ELSE te.endTime
                                END AS TIME
                            ) DESC, te.eventId DESC
                            LIMIT 1' );
        
        $this->db->bind(':userId', $userId);
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':eventId', $eventId);
        $this->db->bind(':eventDate', $eventDate);
        $this->db->bind(':startStatus1', 'start');
        $this->db->bind(':startStatus2', 'start');

        $result = $this->db->single();
        return $result;
    } 

    public function getBelowEventStartTime($userId, $tripId, $eventId, $eventDate) {
        $this->db->query('
                            SELECT te.* 
                            FROM trip_events te
                            WHERE te.userId = :userId 
                            AND te.tripId = :tripId 
                            AND te.eventId <> :eventId
                            AND te.eventDate = :eventDate
                            AND CAST(te.startTime AS TIME) >= (
                                SELECT CAST(
                                    CASE
                                        WHEN eventStatus = :startStatus THEN startTime
                                        ELSE endTime
                                    END AS TIME)
                                FROM trip_events 
                                WHERE eventId = :eventId
                            )
                            ORDER BY CAST(te.startTime AS TIME) ASC, te.eventId ASC
                            LIMIT 1' );
        
        $this->db->bind(':userId', $userId);
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':eventId', $eventId);
        $this->db->bind(':eventDate', $eventDate);
        $this->db->bind(':startStatus', 'start');

        $result = $this->db->single();
        return $result;
    }

    public function getGuideToASpotId(){

    }

    public function saveGuideRequest($data) {
        if (empty($data['userId']) || empty($data['tripId']) || empty($data['eventId'])) {
            return [
                'success' => false,
                'message' => 'Invalid guide request payload'
            ];
        }

        $userId = (int)$data['userId'];
        $tripId = (int)$data['tripId'];
        $eventId = (int)$data['eventId'];

        if (!$this->isTripPendingForUser($userId, $tripId)) {
            return [
                'success' => false,
                'message' => 'Trip is waiting for confirmations, awaiting payment, or already scheduled. Driver and guide changes are locked.'
            ];
        }

        $revisionMode = $this->isTripInRevisionMode($userId, $tripId);

        // First check if a request already exists for this event.
                $this->db->query('SELECT id, status, guideId
                                                    FROM traveller_side_g_requests
                                                    WHERE eventId = :eventId
                                                        AND userId = :userId
                                                        AND tripId = :tripId
                                                    ORDER BY COALESCE(updatedAt, createdAt) DESC, id DESC
                                                    LIMIT 1');
        $this->db->bind(':eventId', $eventId);
        $this->db->bind(':userId', $userId);
        $this->db->bind(':tripId', $tripId);
        $existing = $this->db->single();

        $incomingGuideId = isset($data['guideId']) && $data['guideId'] !== '' ? (int)$data['guideId'] : null;
        $existingGuideId = ($existing && isset($existing->guideId) && $existing->guideId !== null) ? (int)$existing->guideId : null;
        $existingStatus = $existing ? strtolower((string)$existing->status) : null;

        if ($revisionMode) {
            if (!$existing || $existingStatus !== 'rejected') {
                // Accepted/non-rejected guide rows stay immutable during rejection revision.
                if (!($existing && $existingStatus === 'accepted' && $incomingGuideId !== null && $incomingGuideId === $existingGuideId)) {
                    return [
                        'success' => false,
                        'message' => 'Only the rejected guide request can be changed at this stage.'
                    ];
                }
            }
        }

        $statusToPersist = $data['status'] ?? 'notSelected';

        // Keep accepted status untouched when traveller keeps the same accepted guide.
        if ($existing && $existingStatus === 'accepted' && $incomingGuideId !== null && $incomingGuideId === $existingGuideId) {
            $statusToPersist = 'accepted';
        }

        // When revising a rejected guide request, always clear rejected state.
        if ($existing && $existingStatus === 'rejected') {
            $statusToPersist = $incomingGuideId !== null ? 'pending' : 'notSelected';
        }

        // Always enforce people-count based charging from source-of-truth trip data.
        $this->db->query('SELECT numberOfPeople FROM created_trips WHERE tripId = :tripId AND userId = :userId LIMIT 1');
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':userId', $userId);
        $tripMeta = $this->db->single();

        $tripPeopleCount = 1;
        if ($tripMeta && isset($tripMeta->numberOfPeople)) {
            $tripPeopleCount = max(1, (int)$tripMeta->numberOfPeople);
        } elseif (isset($data['numberOfPeople'])) {
            $tripPeopleCount = max(1, (int)$data['numberOfPeople']);
        }

        $rawChargeType = isset($data['chargeType']) && $data['chargeType'] !== ''
            ? trim((string)$data['chargeType'])
            : null;
        $chargeTypeNormalized = $rawChargeType !== null
            ? strtolower(str_replace(['-', ' '], '_', $rawChargeType))
            : null;

        $payloadPeopleCount = isset($data['numberOfPeople']) ? max(1, (int)$data['numberOfPeople']) : $tripPeopleCount;
        $payloadTotalCharge = isset($data['totalCharge']) ? (float)$data['totalCharge'] : 0.0;

        $numberOfPeopleToPersist = $tripPeopleCount;
        $totalChargeToPersist = 0.0;

        if ($incomingGuideId !== null) {
            if ($chargeTypeNormalized === 'per_person' || $chargeTypeNormalized === 'perperson') {
                $perPersonAmount = $payloadPeopleCount > 0
                    ? ($payloadTotalCharge / $payloadPeopleCount)
                    : $payloadTotalCharge;

                $totalChargeToPersist = round(max(0.0, $perPersonAmount) * $tripPeopleCount, 2);
            } else {
                $totalChargeToPersist = round(max(0.0, $payloadTotalCharge), 2);
            }
        }

        $stampRejectedHistory = ($existing && $existingStatus === 'rejected') ? 1 : 0;

        if ($existing) {
            // Update existing request row to preserve the same request ID.
            $query = 'UPDATE traveller_side_g_requests SET 
                      guideId = :guideId,
                      guideFullName = :guideFullName,
                      guideProfilePhoto = :guideProfilePhoto,
                      guideAverageRating = :guideAverageRating,
                      guideBio = :guideBio,
                      chargeType = :chargeType,
                      numberOfPeople = :numberOfPeople,
                      totalCharge = :totalCharge,
                      status = :status,
                      requestedAt = CASE WHEN :statusForRequestedAt IN (\'pending\', \'requested\') THEN CURRENT_TIMESTAMP ELSE requestedAt END,
                      respondedAt = CASE WHEN :stampRejectedHistory = 1 THEN COALESCE(respondedAt, CURRENT_TIMESTAMP) ELSE respondedAt END,
                      acceptedAt = CASE WHEN :statusForAcceptedAt IN (\'pending\', \'requested\', \'notSelected\') THEN NULL ELSE acceptedAt END,
                      completedAt = CASE WHEN :statusForCompletedAt IN (\'pending\', \'requested\', \'notSelected\') THEN NULL ELSE completedAt END,
                      updatedAt = CURRENT_TIMESTAMP
                      WHERE eventId = :eventId AND userId = :userId AND tripId = :tripId';

            $this->db->query($query);
            $this->db->bind(':eventId', $eventId);
            $this->db->bind(':userId', $userId);
            $this->db->bind(':tripId', $tripId);
        } else {
            $query = 'INSERT INTO traveller_side_g_requests 
                      (userId, tripId, eventId, travelSpotId, guideId, guideFullName, 
                       guideProfilePhoto, guideAverageRating, guideBio, chargeType, numberOfPeople, totalCharge, status) 
                      VALUES 
                      (:userId, :tripId, :eventId, :travelSpotId, :guideId, :guideFullName, 
                       :guideProfilePhoto, :guideAverageRating, :guideBio, :chargeType, :numberOfPeople, :totalCharge, :status)';

            $this->db->query($query);
            $this->db->bind(':userId', $userId);
            $this->db->bind(':tripId', $tripId);
            $this->db->bind(':eventId', $eventId);
            $this->db->bind(':travelSpotId', $data['travelSpotId']);
        }

        $this->db->bind(':guideId', $incomingGuideId);
        $this->db->bind(':guideFullName', $data['guideFullName'] ?? null);
        $this->db->bind(':guideProfilePhoto', $data['guideProfilePhoto'] ?? null);
        $this->db->bind(':guideAverageRating', $data['guideAverageRating'] ?? null);
        $this->db->bind(':guideBio', $data['guideBio'] ?? null);
        $this->db->bind(':chargeType', $rawChargeType);
        $this->db->bind(':numberOfPeople', $numberOfPeopleToPersist);
        $this->db->bind(':totalCharge', $totalChargeToPersist);
        $this->db->bind(':status', $statusToPersist);
        if ($existing) {
            $this->db->bind(':statusForRequestedAt', $statusToPersist);
            $this->db->bind(':statusForAcceptedAt', $statusToPersist);
            $this->db->bind(':statusForCompletedAt', $statusToPersist);
        }
        if ($existing) {
            $this->db->bind(':stampRejectedHistory', $stampRejectedHistory);
        }

        $saved = $this->db->execute();

        return [
            'success' => (bool)$saved,
            'message' => $saved ? 'Guide request saved successfully' : 'Failed to save guide request',
            'status' => $statusToPersist,
            'chargeType' => $rawChargeType,
            'numberOfPeople' => $numberOfPeopleToPersist,
            'totalCharge' => $totalChargeToPersist
        ];
    }

    public function getGuideRequestByEventId($eventId, $userId = null) {
        $query = 'SELECT *
                  FROM traveller_side_g_requests
                  WHERE eventId = :eventId';

        if ($userId !== null) {
            $query .= ' AND userId = :userId';
        }

        $query .= ' ORDER BY COALESCE(updatedAt, createdAt) DESC, id DESC LIMIT 1';

        $this->db->query($query);
        $this->db->bind(':eventId', $eventId);
        if ($userId !== null) {
            $this->db->bind(':userId', $userId);
        }
        return $this->db->single();
    }

    public function getEventCoordinates($userId, $tripId, $eventDate) {
        // Get all events for the date ordered by start time
        $this->db->query('SELECT eventId, eventType, travelSpotId, latitude, longitude, locationName, startTime 
                         FROM trip_events 
                         WHERE userId = :userId 
                         AND tripId = :tripId 
                         AND eventDate = :eventDate 
                         ORDER BY CAST(startTime AS TIME) ASC');
        
        $this->db->bind(':userId', $userId);
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':eventDate', $eventDate);
        
        $events = $this->db->resultSet();
        $coordinates = [];
        
        foreach ($events as $event) {
            if ($event->eventType === 'location') {
                // For location events, coordinates are in the trip_events table
                if ($event->latitude && $event->longitude) {
                    $coordinates[] = [
                        'eventId' => $event->eventId,
                        'eventType' => 'location',
                        'name' => $event->locationName,
                        'lat' => floatval($event->latitude),
                        'lng' => floatval($event->longitude)
                    ];
                }
            } elseif ($event->eventType === 'travelSpot' && $event->travelSpotId) {
                // For travel spots, get coordinates from itinerary
                $this->db->query('SELECT pointName, latitude, longitude, pointOrder 
                                 FROM travel_spots_itinerary 
                                 WHERE spotId = :spotId 
                                 ORDER BY pointOrder ASC');
                $this->db->bind(':spotId', $event->travelSpotId);
                
                $itineraryPoints = $this->db->resultSet();
                
                foreach ($itineraryPoints as $point) {
                    if ($point->latitude && $point->longitude) {
                        $coordinates[] = [
                            'eventId' => $event->eventId,
                            'eventType' => 'travelSpot',
                            'name' => $point->pointName,
                            'lat' => floatval($point->latitude),
                            'lng' => floatval($point->longitude)
                        ];
                    }
                }
            }
        }
        
        return $coordinates;
    }

    public function getAllTripCoordinates($userId, $tripId) {
        // Get all events for the trip ordered by date and time
        $this->db->query('SELECT eventId, eventType, travelSpotId, latitude, longitude, locationName, startTime, eventDate 
                         FROM trip_events 
                         WHERE userId = :userId 
                         AND tripId = :tripId 
                         ORDER BY eventDate ASC, CAST(startTime AS TIME) ASC');
        
        $this->db->bind(':userId', $userId);
        $this->db->bind(':tripId', $tripId);
        
        $events = $this->db->resultSet();
        $coordinates = [];
        
        foreach ($events as $event) {
            if ($event->eventType === 'location') {
                // For location events, coordinates are in the trip_events table
                if ($event->latitude && $event->longitude) {
                    $coordinates[] = [
                        'eventId' => $event->eventId,
                        'eventType' => 'location',
                        'name' => $event->locationName,
                        'lat' => floatval($event->latitude),
                        'lng' => floatval($event->longitude)
                    ];
                }
            } elseif ($event->eventType === 'travelSpot' && $event->travelSpotId) {
                // For travel spots, get coordinates from itinerary
                $this->db->query('SELECT pointName, latitude, longitude, pointOrder 
                                 FROM travel_spots_itinerary 
                                 WHERE spotId = :spotId 
                                 ORDER BY pointOrder ASC');
                $this->db->bind(':spotId', $event->travelSpotId);
                
                $itineraryPoints = $this->db->resultSet();
                
                foreach ($itineraryPoints as $point) {
                    if ($point->latitude && $point->longitude) {
                        $coordinates[] = [
                            'eventId' => $event->eventId,
                            'eventType' => 'travelSpot',
                            'name' => $point->pointName,
                            'lat' => floatval($point->latitude),
                            'lng' => floatval($point->longitude)
                        ];
                    }
                }
            }
        }
        
        return $coordinates;
    }

    public function getAllTripEvents($userId, $tripId) {
        $this->db->query("
                            SELECT *
                    FROM trip_events
                    WHERE userId = :userId 
                    AND tripId = :tripId 
                    ORDER BY 
                    CASE eventStatus
                        WHEN 'start' THEN 1
                        WHEN 'intermediate' THEN 2
                        WHEN 'end' THEN 3
                        ELSE 4
                    END,
                    -- Then within 'intermediate', order by startTime ASC (earliest first)
                    CASE 
                        WHEN eventStatus = 'intermediate' THEN startTime
                        ELSE NULL
                    END ASC,
                    -- Then order remaining events by startTime (optional)
                    startTime ASC
                            ");
        
        $this->db->bind(':userId', $userId);
        $this->db->bind(':tripId', $tripId);
        
        return $this->db->resultSet();
    }

    private function dispatchDriverRequestsToProvider($userId, $tripId, $dispatchTime) {
        $this->db->query("SELECT
                            requestId,
                            tripId,
                            rqUserId,
                            driverId,
                            driverName,
                            driverProfilePhoto,
                            driverRating,
                            verifyStatus,
                            vehicleId,
                            vehicleModel,
                            vehicleYear,
                            vehicleType,
                            vehiclePhoto,
                            vehicleCapacity,
                            childSeats,
                            requestStatus,
                            chargeType,
                            totalKm,
                            totalAmount,
                            requestedAt,
                            respondedAt,
                            completedAt
                        FROM traveller_side_d_requests
                        WHERE tripId = :tripId
                          AND rqUserId = :rqUserId
                          AND requestStatus IN ('pending', 'requested', 'accepted', 'rejected')
                        ORDER BY createdAt ASC, requestId ASC");
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':rqUserId', $userId);
        $travellerRows = $this->db->resultSet();

        $this->db->query("SELECT
                            requestId,
                            requestStatus
                        FROM traveller_side_t_requests
                        WHERE tripId = :tripId
                          AND rqUserId = :rqUserId
                          AND requestStatus IN ('pending', 'requested', 'accepted', 'rejected')
                        ORDER BY createdAt ASC, requestId ASC");
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':rqUserId', $userId);
        $existingProviderRows = $this->db->resultSet();

        foreach ($travellerRows as $index => $row) {
            $sourceStatus = strtolower((string)($row->requestStatus ?? 'pending'));
            $targetStatus = in_array($sourceStatus, ['pending', 'requested'], true) ? 'requested' : $sourceStatus;

            if (!in_array($targetStatus, ['requested', 'accepted', 'rejected', 'cancelled', 'completed'], true)) {
                $targetStatus = 'requested';
            }

            $targetRequestedAt = $targetStatus === 'requested'
                ? $dispatchTime
                : ($row->requestedAt ?? null);
            $targetRespondedAt = $targetStatus === 'requested'
                ? null
                : ($row->respondedAt ?? null);
            $targetCompletedAt = $targetStatus === 'requested'
                ? null
                : ($row->completedAt ?? null);

            if ($targetStatus === 'requested') {
                $this->db->query("UPDATE traveller_side_d_requests
                                SET requestStatus = :requestStatus,
                                    requestedAt = :requestedAt,
                                    respondedAt = NULL,
                                    completedAt = NULL,
                                    updatedAt = CURRENT_TIMESTAMP
                                WHERE requestId = :requestId
                                  AND tripId = :tripId
                                  AND rqUserId = :rqUserId");
                $this->db->bind(':requestStatus', 'requested');
                $this->db->bind(':requestedAt', $dispatchTime);
                $this->db->bind(':requestId', (int)$row->requestId);
                $this->db->bind(':tripId', $tripId);
                $this->db->bind(':rqUserId', $userId);

                if (!$this->db->execute()) {
                    return [
                        'success' => false,
                        'message' => 'Failed to update traveller driver request before dispatch'
                    ];
                }
            }

            $providerRow = $existingProviderRows[$index] ?? null;
            if ($providerRow) {
                $this->db->query("UPDATE traveller_side_t_requests SET
                                    tripId = :tripIdValue,
                                    rqUserId = :rqUserIdValue,
                                    driverId = :driverId,
                                    driverName = :driverName,
                                    driverProfilePhoto = :driverProfilePhoto,
                                    driverRating = :driverRating,
                                    verifyStatus = :verifyStatus,
                                    vehicleId = :vehicleId,
                                    vehicleModel = :vehicleModel,
                                    vehicleYear = :vehicleYear,
                                    vehicleType = :vehicleType,
                                    vehiclePhoto = :vehiclePhoto,
                                    vehicleCapacity = :vehicleCapacity,
                                    childSeats = :childSeats,
                                    requestStatus = :requestStatus,
                                    chargeType = :chargeType,
                                    totalKm = :totalKm,
                                    totalAmount = :totalAmount,
                                    requestedAt = :requestedAt,
                                    respondedAt = :respondedAt,
                                    completedAt = :completedAt,
                                    updatedAt = CURRENT_TIMESTAMP
                                WHERE requestId = :providerRequestId
                                  AND tripId = :tripIdScope
                                  AND rqUserId = :rqUserIdScope");

                $this->db->bind(':providerRequestId', (int)$providerRow->requestId);
                $this->db->bind(':tripIdScope', $tripId);
                $this->db->bind(':rqUserIdScope', $userId);
            } else {
                $this->db->query("INSERT INTO traveller_side_t_requests
                                (tripId, rqUserId, driverId, driverName, driverProfilePhoto, driverRating, verifyStatus,
                                 vehicleId, vehicleModel, vehicleYear, vehicleType, vehiclePhoto, vehicleCapacity, childSeats,
                                 requestStatus, chargeType, totalKm, totalAmount, requestedAt, respondedAt, completedAt)
                                VALUES
                                (:tripIdValue, :rqUserIdValue, :driverId, :driverName, :driverProfilePhoto, :driverRating, :verifyStatus,
                                 :vehicleId, :vehicleModel, :vehicleYear, :vehicleType, :vehiclePhoto, :vehicleCapacity, :childSeats,
                                 :requestStatus, :chargeType, :totalKm, :totalAmount, :requestedAt, :respondedAt, :completedAt)");
            }

            $this->db->bind(':tripIdValue', $tripId);
            $this->db->bind(':rqUserIdValue', $userId);
            $this->db->bind(':driverId', (int)$row->driverId);
            $this->db->bind(':driverName', $row->driverName ?? '');
            $this->db->bind(':driverProfilePhoto', $row->driverProfilePhoto ?? null);
            $this->db->bind(':driverRating', $row->driverRating ?? 0);
            $this->db->bind(':verifyStatus', !empty($row->verifyStatus) ? 1 : 0);
            $this->db->bind(':vehicleId', (int)$row->vehicleId);
            $this->db->bind(':vehicleModel', $row->vehicleModel ?? '');
            $this->db->bind(':vehicleYear', $row->vehicleYear ?? null);
            $this->db->bind(':vehicleType', $row->vehicleType ?? '');
            $this->db->bind(':vehiclePhoto', $row->vehiclePhoto ?? null);
            $this->db->bind(':vehicleCapacity', $row->vehicleCapacity ?? 0);
            $this->db->bind(':childSeats', $row->childSeats ?? 0);
            $this->db->bind(':requestStatus', $targetStatus);
            $this->db->bind(':chargeType', $row->chargeType ?? 'perDay');
            $this->db->bind(':totalKm', $row->totalKm ?? null);
            $this->db->bind(':totalAmount', $row->totalAmount ?? 0);
            $this->db->bind(':requestedAt', $targetRequestedAt);
            $this->db->bind(':respondedAt', $targetRespondedAt);
            $this->db->bind(':completedAt', $targetCompletedAt);

            if (!$this->db->execute()) {
                return [
                    'success' => false,
                    'message' => 'Failed to dispatch driver request to provider side'
                ];
            }
        }

        if (count($existingProviderRows) > count($travellerRows)) {
            $staleRows = array_slice($existingProviderRows, count($travellerRows));
            $staleIds = [];

            foreach ($staleRows as $staleRow) {
                $staleStatus = strtolower((string)($staleRow->requestStatus ?? ''));
                if (in_array($staleStatus, ['pending', 'requested'], true)) {
                    $staleIds[] = (int)$staleRow->requestId;
                }
            }

            if (!empty($staleIds)) {
                $idList = implode(',', array_map('intval', $staleIds));
                $this->db->query("DELETE FROM traveller_side_t_requests
                                WHERE tripId = :tripId
                                  AND rqUserId = :rqUserId
                                  AND requestId IN ($idList)");
                $this->db->bind(':tripId', $tripId);
                $this->db->bind(':rqUserId', $userId);

                if (!$this->db->execute()) {
                    return [
                        'success' => false,
                        'message' => 'Failed to clean stale provider-side driver requests'
                    ];
                }
            }
        }

        return [
            'success' => true
        ];
    }

    private function dispatchGuideRequestsToProvider($userId, $tripId, $dispatchTime) {
        $this->db->query("SELECT
                            id,
                            userId,
                            tripId,
                            eventId,
                            travelSpotId,
                            guideId,
                            status,
                            guideFullName,
                            guideProfilePhoto,
                            guideAverageRating,
                            guideBio,
                            chargeType,
                            numberOfPeople,
                            totalCharge,
                            requestedAt,
                            respondedAt,
                            completedAt
                        FROM traveller_side_g_requests
                        WHERE tripId = :tripId
                          AND userId = :userId
                          AND guideId IS NOT NULL
                          AND status IN ('pending', 'requested', 'accepted', 'rejected')
                        ORDER BY createdAt ASC, id ASC");
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':userId', $userId);
        $travellerRows = $this->db->resultSet();

        $this->db->query("SELECT
                            id,
                            eventId,
                            status
                        FROM guide_side_g_requests
                        WHERE tripId = :tripId
                          AND userId = :userId
                        ORDER BY createdAt ASC, id ASC");
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':userId', $userId);
        $existingProviderRows = $this->db->resultSet();

        $existingByEvent = [];
        foreach ($existingProviderRows as $providerRow) {
            $eventKey = (int)$providerRow->eventId;
            if (!isset($existingByEvent[$eventKey])) {
                $existingByEvent[$eventKey] = $providerRow;
            }
        }

        $activeEventIds = [];

        foreach ($travellerRows as $row) {
            $eventId = (int)$row->eventId;
            $activeEventIds[] = $eventId;

            $sourceStatus = strtolower((string)($row->status ?? 'pending'));
            $targetStatus = in_array($sourceStatus, ['pending', 'requested'], true) ? 'requested' : $sourceStatus;

            if (!in_array($targetStatus, ['requested', 'accepted', 'rejected', 'cancelled', 'completed'], true)) {
                $targetStatus = 'requested';
            }

            $targetRequestedAt = $targetStatus === 'requested'
                ? $dispatchTime
                : ($row->requestedAt ?? null);
            $targetRespondedAt = $targetStatus === 'requested'
                ? null
                : ($row->respondedAt ?? null);
            $targetCompletedAt = $targetStatus === 'requested'
                ? null
                : ($row->completedAt ?? null);

            if ($targetStatus === 'requested') {
                $this->db->query("UPDATE traveller_side_g_requests
                                SET status = :status,
                                    requestedAt = :requestedAt,
                                    respondedAt = NULL,
                                    acceptedAt = NULL,
                                    completedAt = NULL,
                                    updatedAt = CURRENT_TIMESTAMP
                                WHERE id = :id
                                  AND tripId = :tripId
                                  AND userId = :userId");
                $this->db->bind(':status', 'requested');
                $this->db->bind(':requestedAt', $dispatchTime);
                $this->db->bind(':id', (int)$row->id);
                $this->db->bind(':tripId', $tripId);
                $this->db->bind(':userId', $userId);

                if (!$this->db->execute()) {
                    return [
                        'success' => false,
                        'message' => 'Failed to update traveller guide request before dispatch'
                    ];
                }
            }

            $providerRow = $existingByEvent[$eventId] ?? null;
            if ($providerRow) {
                $this->db->query("UPDATE guide_side_g_requests SET
                                    userId = :userIdValue,
                                    tripId = :tripIdValue,
                                    eventId = :eventId,
                                    travelSpotId = :travelSpotId,
                                    guideId = :guideId,
                                    status = :status,
                                    guideFullName = :guideFullName,
                                    guideProfilePhoto = :guideProfilePhoto,
                                    guideAverageRating = :guideAverageRating,
                                    guideBio = :guideBio,
                                    chargeType = :chargeType,
                                    numberOfPeople = :numberOfPeople,
                                    totalCharge = :totalCharge,
                                    requestedAt = :requestedAt,
                                    respondedAt = :respondedAt,
                                    completedAt = :completedAt,
                                    updatedAt = CURRENT_TIMESTAMP
                                WHERE id = :providerId
                                  AND tripId = :tripIdScope
                                  AND userId = :userIdScope");

                $this->db->bind(':providerId', (int)$providerRow->id);
                $this->db->bind(':tripIdScope', $tripId);
                $this->db->bind(':userIdScope', $userId);
            } else {
                $this->db->query("INSERT INTO guide_side_g_requests
                                (userId, tripId, eventId, travelSpotId, guideId, status,
                                 guideFullName, guideProfilePhoto, guideAverageRating, guideBio,
                                 chargeType, numberOfPeople, totalCharge, requestedAt, respondedAt, completedAt)
                                VALUES
                                (:userIdValue, :tripIdValue, :eventId, :travelSpotId, :guideId, :status,
                                 :guideFullName, :guideProfilePhoto, :guideAverageRating, :guideBio,
                                 :chargeType, :numberOfPeople, :totalCharge, :requestedAt, :respondedAt, :completedAt)");
            }

            $this->db->bind(':userIdValue', $userId);
            $this->db->bind(':tripIdValue', $tripId);
            $this->db->bind(':eventId', $eventId);
            $this->db->bind(':travelSpotId', (int)$row->travelSpotId);
            $this->db->bind(':guideId', (int)$row->guideId);
            $this->db->bind(':status', $targetStatus);
            $this->db->bind(':guideFullName', $row->guideFullName ?? null);
            $this->db->bind(':guideProfilePhoto', $row->guideProfilePhoto ?? null);
            $this->db->bind(':guideAverageRating', $row->guideAverageRating ?? null);
            $this->db->bind(':guideBio', $row->guideBio ?? null);
            $this->db->bind(':chargeType', $row->chargeType ?? 'whole_trip');
            $this->db->bind(':numberOfPeople', $row->numberOfPeople ?? 1);
            $this->db->bind(':totalCharge', $row->totalCharge ?? 0);
            $this->db->bind(':requestedAt', $targetRequestedAt);
            $this->db->bind(':respondedAt', $targetRespondedAt);
            $this->db->bind(':completedAt', $targetCompletedAt);

            if (!$this->db->execute()) {
                return [
                    'success' => false,
                    'message' => 'Failed to dispatch guide request to provider side'
                ];
            }
        }

        if (empty($activeEventIds)) {
            $this->db->query("DELETE FROM guide_side_g_requests
                            WHERE tripId = :tripId
                              AND userId = :userId
                              AND status IN ('pending', 'requested')");
            $this->db->bind(':tripId', $tripId);
            $this->db->bind(':userId', $userId);

            if (!$this->db->execute()) {
                return [
                    'success' => false,
                    'message' => 'Failed to clean stale provider-side guide requests'
                ];
            }
        } else {
            $activeEventSet = array_fill_keys(array_map('intval', $activeEventIds), true);
            $staleGuideIds = [];

            foreach ($existingProviderRows as $providerRow) {
                $providerEventId = (int)$providerRow->eventId;
                $providerStatus = strtolower((string)($providerRow->status ?? ''));

                if (!isset($activeEventSet[$providerEventId]) && in_array($providerStatus, ['pending', 'requested'], true)) {
                    $staleGuideIds[] = (int)$providerRow->id;
                }
            }

            if (!empty($staleGuideIds)) {
                $idList = implode(',', array_map('intval', $staleGuideIds));
                $this->db->query("DELETE FROM guide_side_g_requests
                                WHERE tripId = :tripId
                                  AND userId = :userId
                                  AND id IN ($idList)");
                $this->db->bind(':tripId', $tripId);
                $this->db->bind(':userId', $userId);

                if (!$this->db->execute()) {
                    return [
                        'success' => false,
                        'message' => 'Failed to remove stale provider-side guide requests'
                    ];
                }
            }
        }

        return [
            'success' => true
        ];
    }

    public function confirmTrip($userId, $tripId) {
        $userId = (int)$userId;
        $tripId = (int)$tripId;

        if ($this->hasRejectedProviderResponse($userId, $tripId)) {
            return [
                'success' => false,
                'status' => 'pending',
                'message' => 'Trip cannot be confirmed while rejected driver or guide requests exist.'
            ];
        }

        $this->db->beginTransaction();

        try {
            $dispatchTime = date('Y-m-d H:i:s');

            $driverDispatch = $this->dispatchDriverRequestsToProvider($userId, $tripId, $dispatchTime);
            if (empty($driverDispatch['success'])) {
                $this->db->rollback();
                return [
                    'success' => false,
                    'status' => 'pending',
                    'message' => $driverDispatch['message'] ?? 'Failed to dispatch driver requests to provider side.'
                ];
            }

            $guideDispatch = $this->dispatchGuideRequestsToProvider($userId, $tripId, $dispatchTime);
            if (empty($guideDispatch['success'])) {
                $this->db->rollback();
                return [
                    'success' => false,
                    'status' => 'pending',
                    'message' => $guideDispatch['message'] ?? 'Failed to dispatch guide requests to provider side.'
                ];
            }

            $this->db->query('UPDATE created_trips 
                             SET status = :status, 
                                 updatedAt = CURRENT_TIMESTAMP 
                             WHERE tripId = :tripId 
                             AND userId = :userId
                             AND status = :currentStatus');
            
            $this->db->bind(':tripId', $tripId);
            $this->db->bind(':userId', $userId);
            $this->db->bind(':status', 'wConfirmation');
            $this->db->bind(':currentStatus', 'pending');
            
            if (!$this->db->execute()) {
                $this->db->rollback();
                return [
                    'success' => false,
                    'status' => null
                ];
            }

            if ($this->db->rowCount() === 0) {
                $this->db->query('SELECT status FROM created_trips WHERE tripId = :tripId AND userId = :userId LIMIT 1');
                $this->db->bind(':tripId', $tripId);
                $this->db->bind(':userId', $userId);
                $trip = $this->db->single();

                $this->db->rollback();
                return [
                    'success' => false,
                    'status' => $trip ? $trip->status : null
                ];
            }

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollback();
            return [
                'success' => false,
                'status' => null,
                'message' => 'Failed to confirm trip and dispatch provider requests.'
            ];
        }

        $status = $this->promoteTripToScheduledIfReady((int)$userId, (int)$tripId);

        return [
            'success' => true,
            'status' => $status ?: 'wConfirmation'
        ];
    }

    public function saveDriverRequests($userId, $tripId, $selectedDrivers) {
        if (!$this->isTripPendingForUser($userId, $tripId)) {
            return [
                'success' => false,
                'message' => 'Trip is waiting for confirmations, awaiting payment, or already scheduled. Driver and guide changes are locked.'
            ];
        }

        $normalizedDrivers = [];
        if (!empty($selectedDrivers) && is_array($selectedDrivers)) {
            ksort($selectedDrivers, SORT_NUMERIC);
            foreach ($selectedDrivers as $driverData) {
                if (!is_array($driverData) || empty($driverData['userId']) || empty($driverData['vehicleId'])) {
                    continue;
                }
                $normalizedDrivers[] = $driverData;
            }
        }

        $revisionMode = $this->isTripInRevisionMode((int)$userId, (int)$tripId);

        $this->db->beginTransaction();

        try {
            // Load existing requests so updates keep the same request IDs.
            $this->db->query('SELECT requestId, driverId, vehicleId, requestStatus FROM traveller_side_d_requests WHERE tripId = :tripId AND rqUserId = :rqUserId ORDER BY createdAt ASC, requestId ASC');
            $this->db->bind(':tripId', $tripId);
            $this->db->bind(':rqUserId', $userId);
            $existingRequests = $this->db->resultSet();

            if ($revisionMode) {
                if (empty($existingRequests)) {
                    $this->db->rollback();
                    return [
                        'success' => false,
                        'message' => 'No existing driver request found to revise.'
                    ];
                }

                if (count($normalizedDrivers) !== count($existingRequests)) {
                    $this->db->rollback();
                    return [
                        'success' => false,
                        'message' => 'Only the rejected driver request can be changed at this stage.'
                    ];
                }

                foreach ($existingRequests as $index => $row) {
                    $incoming = $normalizedDrivers[$index] ?? null;
                    if (!$incoming) {
                        $this->db->rollback();
                        return [
                            'success' => false,
                            'message' => 'Only the rejected driver request can be changed at this stage.'
                        ];
                    }

                    $currentStatus = strtolower((string)($row->requestStatus ?? ''));
                    if ($currentStatus !== 'rejected') {
                        $sameDriver = ((int)$incoming['userId']) === ((int)$row->driverId);
                        $sameVehicle = ((int)$incoming['vehicleId']) === ((int)$row->vehicleId);
                        if (!$sameDriver || !$sameVehicle) {
                            $this->db->rollback();
                            return [
                                'success' => false,
                                'message' => 'Only the rejected driver request can be changed at this stage.'
                            ];
                        }

                        // Keep accepted/requested rows as-is; only rejected rows can be replaced.
                        continue;
                    }

                    $now = date('Y-m-d H:i:s');
                    $this->db->query('UPDATE traveller_side_d_requests SET
                        driverId = :driverId,
                        driverName = :driverName,
                        driverProfilePhoto = :driverProfilePhoto,
                        driverRating = :driverRating,
                        verifyStatus = :verifyStatus,
                        vehicleId = :vehicleId,
                        vehicleModel = :vehicleModel,
                        vehicleYear = :vehicleYear,
                        vehicleType = :vehicleType,
                        vehiclePhoto = :vehiclePhoto,
                        vehicleCapacity = :vehicleCapacity,
                        childSeats = :childSeats,
                        requestStatus = :requestStatus,
                        chargeType = :chargeType,
                        totalAmount = :totalAmount,
                        requestedAt = :requestedAt,
                        respondedAt = NULL,
                        completedAt = NULL,
                        updatedAt = CURRENT_TIMESTAMP
                        WHERE requestId = :requestId AND tripId = :tripId AND rqUserId = :rqUserId');

                    $this->db->bind(':requestId', (int)$row->requestId);
                    $this->db->bind(':tripId', $tripId);
                    $this->db->bind(':rqUserId', $userId);
                    $this->db->bind(':driverId', (int)$incoming['userId']);
                    $this->db->bind(':driverName', $incoming['fullName'] ?? '');
                    $this->db->bind(':driverProfilePhoto', $incoming['profilePhoto'] ?? null);
                    $this->db->bind(':driverRating', $incoming['averageRating'] ?? 0);
                    $this->db->bind(':verifyStatus', !empty($incoming['verified']) ? 1 : 0);
                    $this->db->bind(':vehicleId', (int)$incoming['vehicleId']);
                    $this->db->bind(':vehicleModel', $incoming['model'] ?? '');
                    $this->db->bind(':vehicleYear', $incoming['year'] ?? null);
                    $this->db->bind(':vehicleType', $incoming['vehicleType'] ?? ($incoming['make'] ?? ''));
                    $this->db->bind(':vehiclePhoto', $incoming['vehiclePhoto'] ?? null);
                    $this->db->bind(':vehicleCapacity', $incoming['seatingCapacity'] ?? 0);
                    $this->db->bind(':childSeats', $incoming['childSeats'] ?? 0);
                    $this->db->bind(':requestStatus', 'pending');
                    $this->db->bind(':chargeType', $incoming['chargeType'] ?? 'perDay');
                    $this->db->bind(':totalAmount', $incoming['totalChargePerDay'] ?? 0);
                    $this->db->bind(':requestedAt', $now);

                    if (!$this->db->execute()) {
                        $this->db->rollback();
                        return [
                            'success' => false,
                            'message' => 'Failed to update rejected driver request'
                        ];
                    }
                }

                $this->db->commit();
                return [
                    'success' => true,
                    'message' => 'Rejected driver request updated successfully'
                ];
            }

            $existingIds = array_map(function ($row) {
                return (int)$row->requestId;
            }, $existingRequests ?: []);

            $now = date('Y-m-d H:i:s');

            foreach ($normalizedDrivers as $index => $driverData) {
                if (isset($existingIds[$index])) {
                    $this->db->query('UPDATE traveller_side_d_requests SET
                        driverId = :driverId,
                        driverName = :driverName,
                        driverProfilePhoto = :driverProfilePhoto,
                        driverRating = :driverRating,
                        verifyStatus = :verifyStatus,
                        vehicleId = :vehicleId,
                        vehicleModel = :vehicleModel,
                        vehicleYear = :vehicleYear,
                        vehicleType = :vehicleType,
                        vehiclePhoto = :vehiclePhoto,
                        vehicleCapacity = :vehicleCapacity,
                        childSeats = :childSeats,
                        requestStatus = :requestStatus,
                        chargeType = :chargeType,
                        totalAmount = :totalAmount,
                        requestedAt = :requestedAt,
                        respondedAt = NULL,
                        completedAt = NULL,
                        updatedAt = CURRENT_TIMESTAMP
                        WHERE requestId = :requestId AND tripId = :tripId AND rqUserId = :rqUserId');

                    $this->db->bind(':requestId', $existingIds[$index]);
                    $this->db->bind(':tripId', $tripId);
                    $this->db->bind(':rqUserId', $userId);
                } else {
                    $this->db->query('INSERT INTO traveller_side_d_requests
                        (tripId, rqUserId, driverId, driverName, driverProfilePhoto, driverRating, verifyStatus,
                         vehicleId, vehicleModel, vehicleYear, vehicleType, vehiclePhoto, vehicleCapacity, childSeats,
                         requestStatus, chargeType, totalAmount, requestedAt)
                        VALUES
                        (:tripId, :rqUserId, :driverId, :driverName, :driverProfilePhoto, :driverRating, :verifyStatus,
                         :vehicleId, :vehicleModel, :vehicleYear, :vehicleType, :vehiclePhoto, :vehicleCapacity, :childSeats,
                         :requestStatus, :chargeType, :totalAmount, :requestedAt)');

                    $this->db->bind(':tripId', $tripId);
                    $this->db->bind(':rqUserId', $userId);
                }

                $this->db->bind(':driverId', (int)$driverData['userId']);
                $this->db->bind(':driverName', $driverData['fullName'] ?? '');
                $this->db->bind(':driverProfilePhoto', $driverData['profilePhoto'] ?? null);
                $this->db->bind(':driverRating', $driverData['averageRating'] ?? 0);
                $this->db->bind(':verifyStatus', !empty($driverData['verified']) ? 1 : 0);
                $this->db->bind(':vehicleId', (int)$driverData['vehicleId']);
                $this->db->bind(':vehicleModel', $driverData['model'] ?? '');
                $this->db->bind(':vehicleYear', $driverData['year'] ?? null);
                $this->db->bind(':vehicleType', $driverData['vehicleType'] ?? ($driverData['make'] ?? ''));
                $this->db->bind(':vehiclePhoto', $driverData['vehiclePhoto'] ?? null);
                $this->db->bind(':vehicleCapacity', $driverData['seatingCapacity'] ?? 0);
                $this->db->bind(':childSeats', $driverData['childSeats'] ?? 0);
                $this->db->bind(':requestStatus', 'pending');
                $this->db->bind(':chargeType', $driverData['chargeType'] ?? 'perDay');
                $this->db->bind(':totalAmount', $driverData['totalChargePerDay'] ?? 0);
                $this->db->bind(':requestedAt', $now);

                if (!$this->db->execute()) {
                    $this->db->rollback();
                    return [
                        'success' => false,
                        'message' => 'Failed to save driver request'
                    ];
                }
            }

            // Remove stale rows when drivers were removed before confirmation.
            if (count($existingIds) > count($normalizedDrivers)) {
                $staleIds = array_slice($existingIds, count($normalizedDrivers));
                if (!empty($staleIds)) {
                    $idList = implode(',', array_map('intval', $staleIds));
                    $this->db->query("DELETE FROM traveller_side_d_requests WHERE tripId = :tripId AND rqUserId = :rqUserId AND requestId IN ($idList)");
                    $this->db->bind(':tripId', $tripId);
                    $this->db->bind(':rqUserId', $userId);
                    if (!$this->db->execute()) {
                        $this->db->rollback();
                        return [
                            'success' => false,
                            'message' => 'Failed to remove stale driver request'
                        ];
                    }
                }
            }

            $this->db->commit();
            return [
                'success' => true,
                'message' => 'Driver requests saved successfully'
            ];
        } catch (Throwable $e) {
            $this->db->rollback();
            return [
                'success' => false,
                'message' => 'Failed to save driver requests'
            ];
        }
    }

    public function getDriverRequestsForTrip($userId, $tripId) {
        $this->db->query('SELECT 
            requestId, driverId, driverName, driverProfilePhoto, driverRating, verifyStatus,
            vehicleId, vehicleModel, vehicleYear, vehicleType, vehiclePhoto, vehicleCapacity, childSeats,
            totalAmount, chargeType, requestStatus, requestedAt
            FROM traveller_side_d_requests
            WHERE tripId = :tripId AND rqUserId = :rqUserId
            ORDER BY createdAt ASC, requestId ASC');
        
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':rqUserId', $userId);
        $drivers = $this->db->resultSet();
        return $drivers ? $drivers : [];
    }

    public function getTripStartEndEvents($userId, $tripId) {
        // Get start and end events for the trip
        $this->db->query("SELECT 
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
            ts.spotName as travelSpotName,
            ts.overview as travelSpotOverview
        FROM trip_events te
        LEFT JOIN travel_spots ts ON te.travelSpotId = ts.spotId
        INNER JOIN created_trips ct ON te.tripId = ct.tripId
        WHERE te.tripId = :tripId 
        AND ct.userId = :userId
        AND te.eventStatus IN (:startStatus, :endStatus)
        ORDER BY FIELD(te.eventStatus, 'start', 'end')");
        
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':userId', $userId);
        $this->db->bind(':startStatus', 'start');
        $this->db->bind(':endStatus', 'end');
        
        $results = $this->db->resultSet();
        error_log("Results for getTripStartEndEvents: " . json_encode($results));
        return $results ? $results : [];
    }

    public function submitUserProblem($data) {
        $query = "INSERT INTO user_problems (userId, subject, message, status)
                  VALUES (:userId, :subject, :message, 'pending')";
        $this->db->query($query);
        $this->db->bind(':userId', (int)$data['userId']);
        $this->db->bind(':subject', (string)$data['subject']);
        $this->db->bind(':message', (string)$data['message']);

        return $this->db->execute();
    }

    public function getUserProblems($filter = 'all') {
        $query = "SELECT
                    up.problemId,
                    up.userId,
                    up.subject,
                    up.message,
                    up.status,
                    up.completedBy,
                    up.completedAt,
                    up.createdAt,
                    u.fullname,
                    u.email,
                    u.phone,
                    u.account_type,
                    u.profile_photo,
                    mod_user.fullname AS completedByName
                  FROM user_problems up
                  INNER JOIN users u ON up.userId = u.id
                  LEFT JOIN users mod_user ON up.completedBy = mod_user.id";

        if ($filter === 'pending') {
            $query .= " WHERE up.status = 'pending'";
        } elseif ($filter === 'in_progress') {
            $query .= " WHERE up.status = 'in_progress'";
        } elseif ($filter === 'completed') {
            $query .= " WHERE up.status = 'completed'";
        }

        $query .= " ORDER BY
                    CASE up.status
                        WHEN 'pending' THEN 1
                        WHEN 'in_progress' THEN 2
                        WHEN 'completed' THEN 3
                        ELSE 4
                    END,
                    up.createdAt DESC";

        $this->db->query($query);
        return $this->db->resultSet();
    }

    public function getUserProblemsByUserId($userId, $filter = 'all') {
        $query = "SELECT
                    up.problemId,
                    up.userId,
                    up.subject,
                    up.message,
                    up.status,
                    up.completedBy,
                    up.completedAt,
                    up.createdAt,
                    mod_user.fullname AS completedByName
                  FROM user_problems up
                  LEFT JOIN users mod_user ON up.completedBy = mod_user.id
                  WHERE up.userId = :userId";

        if ($filter === 'pending') {
            $query .= " AND up.status = 'pending'";
        } elseif ($filter === 'in_progress') {
            $query .= " AND up.status = 'in_progress'";
        } elseif ($filter === 'completed') {
            $query .= " AND up.status = 'completed'";
        }

        $query .= " ORDER BY up.createdAt DESC";

        $this->db->query($query);
        $this->db->bind(':userId', (int)$userId);

        return $this->db->resultSet();
    }
    
}