<?php
class RegUserModel {
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function getUserTrips($userId){

        $query = 'SELECT tripId, userId, tripTitle , description, startDate, endDate, status, createdAt, updatedAt FROM created_trips WHERE userId = :userId ORDER BY createdAt DESC';

        $this->db->query($query);
        $this->db->bind(':userId',$userId);
        return $this->db->resultSet();
    }

    public function addCreatedTrip($insertingData){

        $query = 'INSERT INTO created_trips ( userId, tripTitle, description, startDate, endDate, status ) VALUES ( :userId, :tripTitle, :description, :startDate, :endDate, :status)';
        
        $this->db->query($query);
        $this->db->bind(':userId',$insertingData['userId']);
        $this->db->bind(':tripTitle',$insertingData['tripTitle']);
        $this->db->bind(':description',$insertingData['description']);
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
                    startDate = :startDate,
                    endDate = :endDate,
                    updatedAt = CURRENT_TIMESTAMP
                    WHERE tripId = :tripId";
        
        $this->db->query($query);
        $this->db->bind(':tripId', $updatingData['tripId']);
        $this->db->bind(':tripTitle', $updatingData['tripTitle']);
        $this->db->bind(':description', $updatingData['description']);
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

            return $this->db->execute();

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

            return $this->db->execute();
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
        $this->db->query('SELECT * FROM trip_events WHERE userId = :userId AND tripId = :tripId AND eventDate = :eventDate ORDER BY CAST(startTime AS TIME) ASC');
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
    
}