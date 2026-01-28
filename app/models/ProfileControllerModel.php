<?php
    class ProfileControllerModel{
        private $db;

        public function __construct(){
            $this->db = new Database;
        }

        public function updateProfilePhoto($userId, $profilePhoto) {
            $this->db->query('UPDATE users SET profile_photo = :profile_photo, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
            $this->db->bind(':profile_photo', $profilePhoto);
            $this->db->bind(':id', $userId);
            return $this->db->execute();
        }

        public function getCoverPhotos($userId) {
            $query = 'SELECT id, photo_path, photo_order FROM cover_photos 
                    WHERE userId = :userId AND is_active = 1 
                    ORDER BY photo_order ASC';
            
            $this->db->query($query);
            $this->db->bind(':userId', $userId);
            $results = $this->db->resultSet();
            
            return $results ? $results : [];
        }

        public function deleteCoverPhotos($photoIds) {
            try {
                if (empty($photoIds)) {
                    return 0;
                }
                
                error_log("deleteCoverPhotos called with IDs: " . implode(', ', $photoIds));
                
                // Create named placeholders for IN clause
                $placeholders = [];
                foreach ($photoIds as $index => $id) {
                    $placeholders[] = ':id' . $index;
                }
                $placeholdersStr = implode(',', $placeholders);
                
                $query = "DELETE FROM cover_photos WHERE id IN ($placeholdersStr)";
                error_log("Delete query: " . $query);
                
                $this->db->query($query);
                
                // Bind each photo ID with named parameter
                foreach ($photoIds as $index => $photoId) {
                    $this->db->bind(':id' . $index, $photoId);
                    error_log("Binding :id{$index} = {$photoId}");
                }
                
                $result = $this->db->execute();
                $affectedRows = $this->db->rowCount();
                
                error_log("Delete executed. Result: " . ($result ? 'true' : 'false') . ", Affected rows: " . $affectedRows);
                
                // Return number of actually affected rows
                return $affectedRows;
                
            } catch (Exception $e) {
                error_log("Error deleting cover photos: " . $e->getMessage());
                return 0;
            }
        }

        public function saveCoverPhotos($userId, $photos) {
            try {
                // For each new photo, replace the existing one at that photo_order if it exists
                foreach ($photos as $index => $photoPath) {
                    // Delete existing photo at this position
                    $deleteQuery = 'DELETE FROM cover_photos WHERE userId = :userId AND photo_order = :photo_order';
                    $this->db->query($deleteQuery);
                    $this->db->bind(':userId', $userId);
                    $this->db->bind(':photo_order', $index);
                    $this->db->execute();
                    
                    // Insert new photo
                    $insertQuery = 'INSERT INTO cover_photos (userId, photo_path, photo_order, is_active) 
                                VALUES (:userId, :photo_path, :photo_order, 1)';
                    $this->db->query($insertQuery);
                    $this->db->bind(':userId', $userId);
                    $this->db->bind(':photo_path', $photoPath);
                    $this->db->bind(':photo_order', $index);
                    $this->db->execute();
                }
                
                return true;
                
            } catch (Exception $e) {
                error_log("Error saving cover photos: " . $e->getMessage());
                return false;
            }
        }

        public function submitTouristLicense($userId, $data) {
            try {
                // Build the update query dynamically based on provided data
                $setParts = [];
                $bindData = [];
                
                foreach ($data as $field => $value) {
                    $setParts[] = "$field = :$field";
                    $bindData[":$field"] = $value;
                }
                
                $setClause = implode(', ', $setParts);
                $bindData[':userId'] = $userId;
                
                $query = "UPDATE profile_details SET $setClause WHERE userId = :userId";
                
                error_log("submitTouristLicense query: " . $query);
                error_log("submitTouristLicense data: " . json_encode($bindData));
                
                $this->db->query($query);
                
                // Bind all parameters
                foreach ($bindData as $param => $value) {
                    $this->db->bind($param, $value);
                }
                
                $result = $this->db->execute();
                
                error_log("submitTouristLicense executed. Result: " . ($result ? 'true' : 'false'));
                
                return $result;
                
            } catch (Exception $e) {
                error_log("Error submitting tourist license: " . $e->getMessage());
                return false;
            }
        }

    public function updateDriverLicense($userId, $driverData) {
        try {
            // First, get the current driver_data
            $query = "SELECT driver_data FROM users WHERE id = :userId";
            $this->db->query($query);
            $this->db->bind(':userId', $userId);
            $result = $this->db->single();

            $currentDriverData = [];
            if ($result && !empty($result->driver_data)) {
                $currentDriverData = json_decode($result->driver_data, true);
                if (!is_array($currentDriverData)) {
                    $currentDriverData = [];
                }
            }

            // Merge with new data
            $updatedDriverData = array_merge($currentDriverData, $driverData);

            // Update the driver_data field
            $query = "UPDATE users SET driver_data = :driverData WHERE id = :userId";
            $this->db->query($query);
            $this->db->bind(':driverData', json_encode($updatedDriverData));
            $this->db->bind(':userId', $userId);

            $result = $this->db->execute();

            error_log("updateDriverLicense executed. Result: " . ($result ? 'true' : 'false'));
            error_log("Updated driver_data: " . json_encode($updatedDriverData));

            return $result;

        } catch (Exception $e) {
            error_log("Error updating driver license: " . $e->getMessage());
            return false;
        }
    }

    public function updateTouristLicense($userId, $data) {
        try {
            // Build the update query dynamically based on provided data
            $setParts = [];
            $bindData = [];

            foreach ($data as $field => $value) {
                $setParts[] = "$field = :$field";
                $bindData[":$field"] = $value;
            }

            $setClause = implode(', ', $setParts);
            $bindData[':userId'] = $userId;

            $query = "UPDATE profile_details SET $setClause WHERE userId = :userId";

            error_log("updateTouristLicense query: " . $query);
            error_log("updateTouristLicense data: " . json_encode($bindData));

            $this->db->query($query);

            // Bind all parameters
            foreach ($bindData as $param => $value) {
                $this->db->bind($param, $value);
            }

            $result = $this->db->execute();

            error_log("updateTouristLicense executed. Result: " . ($result ? 'true' : 'false'));

            return $result;

        } catch (Exception $e) {
            error_log("Error updating tourist license: " . $e->getMessage());
            return false;
        }
    }
}
