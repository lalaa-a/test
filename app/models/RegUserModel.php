<?php
class RegUserModel {
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function getUserTrips($userId){

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

    public function addEventData($insertingData){

        if(isset($insertingData['locationName'])) {
            
            $query = null;
            if(($insertingData['eventStatus'] === 'start')) {
                $query = 'INSERT INTO trip_events ( userId, tripId, eventDate, startTime, eventType, eventStatus, locationName, latitude, longitude, description ) VALUES ( :userId, :tripId, :eventDate, :startTime, :eventType, :eventStatus, :locationName, :latitude, :longitude, :description )';
            } 

            if($insertingData['eventStatus'] === 'intermediate') {
                $query = 'INSERT INTO trip_events ( userId, tripId, eventDate, startTime, endTime, eventType, eventStatus, locationName, latitude, longitude, description ) VALUES ( :userId, :tripId, :eventDate, :startTime, :endTime, :eventType, :eventStatus, :locationName, :latitude, :longitude, :description )';
            }

            if($insertingData['eventStatus'] === 'end') {
                $query = 'INSERT INTO trip_events ( userId, tripId, eventDate, endTime, eventType, eventStatus, locationName, latitude, longitude, description ) VALUES ( :userId, :tripId, :eventDate, :endTime, :eventType, :eventStatus, :locationName, :latitude, :longitude, :description )';
            }
            
            $this->db->query($query);
            $this->db->bind(':userId',$insertingData['userId']);
            $this->db->bind(':tripId',$insertingData['tripId']);
            $this->db->bind(':eventDate',$insertingData['eventDate']);

            if($insertingData['eventStatus'] === 'start') {
                $this->db->bind(':startTime',$insertingData['startTime']);
            }

            if($insertingData['eventStatus'] === 'intermediate') {
                $this->db->bind(':startTime',$insertingData['startTime']);
                $this->db->bind(':endTime',$insertingData['endTime']);
            }

            if($insertingData['eventStatus'] === 'end') {
                $this->db->bind(':endTime',$insertingData['endTime']);
            }

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
            //$query = 'INSERT INTO trip_events ( userId, tripId, eventDate, startTime, endTime, eventType, eventStatus, travelSpotId ) VALUES ( :userId, :tripId, :eventDate, :startTime, :endTime, :eventType, :eventStatus, :travelSpotId )';
            
            $query = null;
            if(($insertingData['eventStatus'] === 'start')) {
                $query = 'INSERT INTO trip_events ( userId, tripId, eventDate, startTime, eventType, eventStatus, travelSpotId ) VALUES ( :userId, :tripId, :eventDate, :startTime, :eventType, :eventStatus, :travelSpotId )';
            }
            
            if($insertingData['eventStatus'] === 'intermediate') {
                $query = 'INSERT INTO trip_events ( userId, tripId, eventDate, startTime, endTime, eventType, eventStatus, travelSpotId ) VALUES ( :userId, :tripId, :eventDate, :startTime, :endTime, :eventType, :eventStatus, :travelSpotId )';
            }

            if($insertingData['eventStatus'] === 'end') {
                $query = 'INSERT INTO trip_events ( userId, tripId, eventDate, endTime, eventType, eventStatus, travelSpotId ) VALUES ( :userId, :tripId, :eventDate, :endTime, :eventType, :eventStatus, :travelSpotId )';
            }

            $this->db->query($query);   
            $this->db->bind(':userId',$insertingData['userId']);
            $this->db->bind(':tripId',$insertingData['tripId']);
            $this->db->bind(':eventDate',$insertingData['eventDate']);
            
            if($insertingData['eventStatus'] === 'start') {
                $this->db->bind(':startTime',$insertingData['startTime']);
            }

            if($insertingData['eventStatus'] === 'intermediate') {
                $this->db->bind(':startTime',$insertingData['startTime']);
                $this->db->bind(':endTime',$insertingData['endTime']);
            }

            if($insertingData['eventStatus'] === 'end') {
                $this->db->bind(':endTime',$insertingData['endTime']);
            }
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

            if($updatingData['eventStatus'] === 'start') {
                $query = "UPDATE trip_events SET 
                        startTime = :startTime,
                        eventType = :eventType,
                        eventStatus = :eventStatus,
                        travelSpotId = :travelSpotId
                        WHERE eventId = :eventId AND userId = :userId";
            }

            if($updatingData['eventStatus'] === 'intermediate') {
                $query = "UPDATE trip_events SET 
                        startTime = :startTime,
                        endTime = :endTime,
                        eventType = :eventType,
                        eventStatus = :eventStatus,
                        travelSpotId = :travelSpotId
                        WHERE eventId = :eventId AND userId = :userId";
            }

            if($updatingData['eventStatus'] === 'end') {
                $query = "UPDATE trip_events SET 
                        endTime = :endTime,
                        eventType = :eventType,
                        eventStatus = :eventStatus,
                        travelSpotId = :travelSpotId
                        WHERE eventId = :eventId AND userId = :userId";
            }

            $this->db->query($query);
            $this->db->bind(':eventId', $updatingData['eventId']);
            $this->db->bind(':userId', $updatingData['userId']);
            $this->db->bind(':startTime', $updatingData['startTime']);
            
            if($updatingData['eventStatus'] === 'start'){
                $this->db->bind(':startTime', $updatingData['startTime']);
            }
            if($updatingData['eventStatus'] === 'intermediate'){
                $this->db->bind(':startTime', $updatingData['startTime']);
                $this->db->bind(':endTime', $updatingData['endTime']);
            }
            if($updatingData['eventStatus'] === 'end'){
                $this->db->bind(':endTime', $updatingData['endTime']);
            }
            
            $this->db->bind(':eventType', $updatingData['eventType']);
            $this->db->bind(':eventStatus', $updatingData['eventStatus']);
            $this->db->bind(':travelSpotId', $updatingData['travelSpotId']);

            return $this->db->execute();
        } else {

        if($updatingData['eventStatus'] === 'start'){
            $query = "UPDATE trip_events SET 
                    startTime = :startTime,
                    eventType = :eventType,
                    eventStatus = :eventStatus,
                    locationName = :locationName,
                    latitude = :latitude,
                    longitude = :longitude,
                    description = :description
                    WHERE eventId = :eventId AND userId = :userId";
        }

        if($updatingData['eventStatus'] === 'intermediate'){
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
        }
        if($updatingData['eventStatus'] === 'end'){
            $query = "UPDATE trip_events SET 
                    endTime = :endTime,
                    eventType = :eventType,
                    eventStatus = :eventStatus,
                    locationName = :locationName,
                    latitude = :latitude,
                    longitude = :longitude,
                    description = :description
                    WHERE eventId = :eventId AND userId = :userId";
        }
        
        
            $this->db->query($query);
            $this->db->bind(':eventId', $updatingData['eventId']);
            $this->db->bind(':userId', $updatingData['userId']);

            if($updatingData['eventStatus'] === 'start'){
                $this->db->bind(':startTime', $updatingData['startTime']);
            }
            if($updatingData['eventStatus'] === 'intermediate'){
                $this->db->bind(':startTime', $updatingData['startTime']);
                $this->db->bind(':endTime', $updatingData['endTime']);
            }
            if($updatingData['eventStatus'] === 'end'){
                $this->db->bind(':endTime', $updatingData['endTime']);
            }

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
        $this->db->query('SELECT * FROM trip_events 
                        WHERE userId = :userId 
                        AND tripId = :tripId 
                        AND eventDate = :eventDate 
                        ORDER BY endTime DESC LIMIT 1');
        
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
                            AND te.eventDate = :eventDate
                            AND CAST(te.endTime AS TIME) <= (
                                SELECT CAST(startTime AS TIME) 
                                FROM trip_events 
                                WHERE eventId = :eventId
                            )
                            ORDER BY CAST(te.endTime AS TIME) DESC, te.eventId DESC
                            LIMIT 1' );
        
        $this->db->bind(':userId', $userId);
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':eventId', $eventId);
        $this->db->bind(':eventDate', $eventDate);

        $result = $this->db->single();
        return $result;
    } 

    public function getBelowEventStartTime($userId, $tripId, $eventId, $eventDate) {
        $this->db->query('
                            SELECT te.* 
                            FROM trip_events te
                            WHERE te.userId = :userId 
                            AND te.tripId = :tripId 
                            AND te.eventDate = :eventDate
                            AND CAST(te.startTime AS TIME) >= (
                                SELECT CAST(endTime AS TIME) 
                                FROM trip_events 
                                WHERE eventId = :eventId
                            )
                            ORDER BY CAST(te.startTime AS TIME) ASC, te.eventId ASC
                            LIMIT 1' );
        
        $this->db->bind(':userId', $userId);
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':eventId', $eventId);
        $this->db->bind(':eventDate', $eventDate);

        $result = $this->db->single();
        return $result;
    }

    public function getGuideToASpotId(){

    }

    public function saveGuideRequest($data) {
        // First check if a request already exists for this event
        $this->db->query('SELECT id FROM traveller_side_g_requests WHERE eventId = :eventId');
        $this->db->bind(':eventId', $data['eventId']);
        $existing = $this->db->single();

        if ($existing) {
            // Update existing request
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
                      updatedAt = CURRENT_TIMESTAMP
                      WHERE eventId = :eventId';
            
            $this->db->query($query);
            $this->db->bind(':eventId', $data['eventId']);
        } else {
            // Insert new request
            $query = 'INSERT INTO traveller_side_g_requests 
                      (userId, tripId, eventId, travelSpotId, guideId, guideFullName, 
                       guideProfilePhoto, guideAverageRating, guideBio, chargeType, numberOfPeople, totalCharge, status) 
                      VALUES 
                      (:userId, :tripId, :eventId, :travelSpotId, :guideId, :guideFullName, 
                       :guideProfilePhoto, :guideAverageRating, :guideBio, :chargeType, :numberOfPeople, :totalCharge, :status)';
            
            $this->db->query($query);
            $this->db->bind(':userId', $data['userId']);
            $this->db->bind(':tripId', $data['tripId']);
            $this->db->bind(':eventId', $data['eventId']);
            $this->db->bind(':travelSpotId', $data['travelSpotId']);
        }

        // Common bindings
        $this->db->bind(':guideId', $data['guideId']);
        $this->db->bind(':guideFullName', $data['guideFullName']);
        $this->db->bind(':guideProfilePhoto', $data['guideProfilePhoto']);
        $this->db->bind(':guideAverageRating', $data['guideAverageRating']);
        $this->db->bind(':guideBio', $data['guideBio']);
        $this->db->bind(':chargeType', $data['chargeType'] ?? null);
        $this->db->bind(':numberOfPeople', $data['numberOfPeople'] ?? 1);
        $this->db->bind(':totalCharge', $data['totalCharge']);
        $this->db->bind(':status', $data['status']);

        return $this->db->execute();
    }

    public function getGuideRequestByEventId($eventId) {
        $this->db->query('SELECT * FROM traveller_side_g_requests WHERE eventId = :eventId');
        $this->db->bind(':eventId', $eventId);
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

    public function confirmTrip($userId, $tripId) {
        $this->db->query('UPDATE created_trips 
                         SET status = :status, 
                             updatedAt = CURRENT_TIMESTAMP 
                         WHERE tripId = :tripId 
                         AND userId = :userId');
        
        $this->db->bind(':tripId', $tripId);
        $this->db->bind(':userId', $userId);
        $this->db->bind(':status', 'pending');
        
        return $this->db->execute();
    }

    public function saveDriverRequests($userId, $tripId, $selectedDrivers) {
        if (empty($selectedDrivers)) {
            return true; // No drivers to save
        }

        $this->db->query('INSERT INTO traveller_side_d_requests 
            (tripId, rqUserId, driverId, driverName, driverProfilePhoto, driverRating, verifyStatus, 
             vehicleId, vehicleModel, vehicleYear, vehicleType, vehiclePhoto, vehicleCapacity, childSeats, 
             requestStatus, chargeType, totalAmount, requestedAt) 
            VALUES 
            (:tripId, :rqUserId, :driverId, :driverName, :driverProfilePhoto, :driverRating, :verifyStatus,
             :vehicleId, :vehicleModel, :vehicleYear, :vehicleType, :vehiclePhoto, :vehicleCapacity, :childSeats,
             :requestStatus, :chargeType, :totalAmount, :requestedAt)');

        foreach ($selectedDrivers as $segmentIndex => $driverData) {
            $this->db->bind(':tripId', $tripId);
            $this->db->bind(':rqUserId', $userId);
            $this->db->bind(':driverId', $driverData['userId']);
            $this->db->bind(':driverName', $driverData['fullName']);
            $this->db->bind(':driverProfilePhoto', $driverData['profilePhoto']);
            $this->db->bind(':driverRating', $driverData['averageRating']);
            $this->db->bind(':verifyStatus', $driverData['verified'] ? 1 : 0);
            $this->db->bind(':vehicleId', $driverData['vehicleId']);
            $this->db->bind(':vehicleModel', $driverData['model']);
            $this->db->bind(':vehicleYear', $driverData['year']);
            $this->db->bind(':vehicleType', $driverData['vehicleType']);
            $this->db->bind(':vehiclePhoto', $driverData['vehiclePhoto']);
            $this->db->bind(':vehicleCapacity', $driverData['seatingCapacity']);
            $this->db->bind(':childSeats', $driverData['childSeats']);
            $this->db->bind(':requestStatus', 'pending');
            $this->db->bind(':chargeType', 'perDay'); // Assuming perDay for now
            $this->db->bind(':totalAmount', $driverData['totalChargePerDay']);
            $this->db->bind(':requestedAt', date('Y-m-d H:i:s'));

            if (!$this->db->execute()) {
                return false; // Failed to insert
            }
        }

        return true;
    }

    public function getDriverRequestsForTrip($tripId) {
        $this->db->query('SELECT 
            requestId, driverId, driverName, driverProfilePhoto, driverRating, verifyStatus,
            vehicleId, vehicleModel, vehicleYear, vehicleType, vehiclePhoto, vehicleCapacity, childSeats,
            totalAmount, chargeType, requestStatus, requestedAt
            FROM traveller_side_d_requests
            WHERE tripId = :tripId
            ORDER BY createdAt DESC');
        
        $this->db->bind(':tripId', $tripId);
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
    
}