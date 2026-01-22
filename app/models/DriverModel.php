<?php

class DriverModel {
    private $db;

    public function __construct(){
        $this->db = new Database();
    }


    public function getBasicDriverInfo($userId){

        error_log("getBasicDriverInfo called with userId: " . print_r($userId, true));

        $query = 'SELECT fullName, dob, phone, secondary_phone, address, email, profile_photo, driver_data, bio, languages, instaAccount, facebookAccount, dlVerified, tlSubmitted, tlVerified, tLicenseNumber, tLicenseExpiryDate, tLicensePhotoFront, tLicensePhotoBack, averageRating, user_created_at FROM vw_user_complete_profiles WHERE userId = :userId';

        $this->db->query($query);
        $this->db->bind(':userId',$userId);
        $result = $this->db->single();

        //error_log("data we got from DB: " . json_encode($result));

        if (!$result) {
            return null;
        }

        // Decode driver_data JSON if it exists
        $driverData = null;
        if (!empty($result->driver_data)) {
            $driverData = json_decode($result->driver_data, true);
        }

        // Build the structured response
        $driverInfo = [
            'fullName' => $result->fullname ?? '',
            'phone' => $result->phone ?? '',
            'secondaryPhone' => $result->secondary_phone ?? '',
            'dateOfBirth' => $result->dob ?? '',
            'address' => $result->address ?? '',
            'bio' => $result->bio ?? '',
            'instagram' => $result->instaAccount ?? '',
            'facebook' => $result->facebookAccount ?? '',
            'languages' => $result->languages ?? '',
            'email' => $result->email ?? '',
            'profilePhoto' => $result->profile_photo ?? '',
            'dLicenseStatus' => $result->dlVerified ?? '',
            'licenseNumber' => $driverData['license_number'] ?? '',
            'licenseExpiry' => $driverData['license_expire_date'] ?? '',
            'driverLicenseFrontPhoto' => $driverData['license_front'] ?? '',
            'driverLicenseBackPhoto' => $driverData['license_back'] ?? '',
            'tLicenseSubmitted' => $result->tlSubmitted ?? '',
            'tLicenseStatus' => $result->tlVerified ?? '',
            'touristLicenseNumber' => $result->tLicenseNumber ?? '',
            'touristLicenseExpiry' => $result->tLicenseExpiryDate ?? '',
            'touristLicenseFrontPhoto' => $result->tLicensePhotoFront ?? '',
            'touristLicenseBackPhoto' => $result->tLicensePhotoBack ?? '',
            'averageRating' => $result->averageRating ?? 0.00,
            'userCreatedAt' => $result->user_created_at ?? ''
        ];

        error_log("Structured driver info: " . json_encode($driverInfo));

        return $driverInfo;
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
    
    public function updateDriverPersonalInfo($userId, $data) {
        try {
            // Update users table fields
            $usersQuery = 'UPDATE users SET
                            fullname = :fullName,
                            dob = :dob,
                            phone = :phone,
                            secondary_phone = :secondaryPhone,
                            address = :address
                          WHERE id = :userId';

            $this->db->query($usersQuery);
            $this->db->bind(':fullName', $data['fullName']);
            $this->db->bind(':dob', $data['dateOfBirth']);
            $this->db->bind(':phone', $data['phone']);
            $this->db->bind(':secondaryPhone', $data['secondaryPhone']);
            $this->db->bind(':address', $data['address']);
            $this->db->bind(':userId', $userId);

            $usersResult = $this->db->execute();

            // Update profile_details table fields
            $profileQuery = 'UPDATE profile_details SET
                            bio = :bio,
                            instaAccount = :instagram,
                            facebookAccount = :facebook,
                            languages = :languages
                          WHERE userId = :userId';

            $this->db->query($profileQuery);
            $this->db->bind(':bio', $data['bio']);
            $this->db->bind(':instagram', $data['instagram']);
            $this->db->bind(':facebook', $data['facebook']);
            $this->db->bind(':languages', $data['languages']);
            $this->db->bind(':userId', $userId);

            $profileResult = $this->db->execute();

            // Return true if both updates succeed
            return $usersResult && $profileResult;

        } catch (Exception $e) {
            error_log("Error updating driver personal info: " . $e->getMessage());
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