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
   
}