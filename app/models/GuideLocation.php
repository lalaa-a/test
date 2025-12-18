<?php

class GuideLocation {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Add a new guiding location
    public function addLocation($guide_id, $location_name, $city, $visit_hours, $rate_per_hour, $description = '') {
        $this->db->query('INSERT INTO guide_locations (guide_id, location_name, city, visit_hours, rate_per_hour, description) 
                         VALUES (:guide_id, :location_name, :city, :visit_hours, :rate_per_hour, :description)');
        
        $this->db->bind(':guide_id', $guide_id);
        $this->db->bind(':location_name', $location_name);
        $this->db->bind(':city', $city);
        $this->db->bind(':visit_hours', $visit_hours);
        $this->db->bind(':rate_per_hour', $rate_per_hour);
        $this->db->bind(':description', $description);
        
        return $this->db->execute();
    }
    
    // Get all locations for a specific guide
    public function getLocationsByGuide($guide_id) {
        $this->db->query('SELECT * FROM guide_locations WHERE guide_id = :guide_id ORDER BY created_at DESC');
        $this->db->bind(':guide_id', $guide_id);
        
        return $this->db->resultSet();
    }
    
    // Get total location count for a guide
    public function getLocationCount($guide_id) {
        $this->db->query('SELECT COUNT(*) as total_locations FROM guide_locations WHERE guide_id = :guide_id');
        $this->db->bind(':guide_id', $guide_id);
        
        $result = $this->db->single();
        return $result ? $result->total_locations : 0;
    }
    
    // Get a specific location
    public function getLocationById($id) {
        $this->db->query('SELECT * FROM guide_locations WHERE id = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }
    
    // Update a location
    public function updateLocation($id, $location_name, $city, $visit_hours, $rate_per_hour, $description) {
        $this->db->query('UPDATE guide_locations 
                         SET location_name = :location_name, city = :city, visit_hours = :visit_hours, rate_per_hour = :rate_per_hour, description = :description 
                         WHERE id = :id');
        
        $this->db->bind(':id', $id);
        $this->db->bind(':location_name', $location_name);
        $this->db->bind(':city', $city);
        $this->db->bind(':visit_hours', $visit_hours);
        $this->db->bind(':rate_per_hour', $rate_per_hour);
        $this->db->bind(':description', $description);
        
        return $this->db->execute();
    }
    
    // Delete a location
    public function deleteLocation($id) {
        $this->db->query('DELETE FROM guide_locations WHERE id = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }
    
    // Check if a location belongs to a specific guide
    public function isOwner($location_id, $guide_id) {
        $this->db->query('SELECT id FROM guide_locations WHERE id = :location_id AND guide_id = :guide_id');
        $this->db->bind(':location_id', $location_id);
        $this->db->bind(':guide_id', $guide_id);
        
        return $this->db->single() ? true : false;
    }
}

?>
