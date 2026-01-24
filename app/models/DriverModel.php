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

    public function addVehicle($driverId, $vehicleData) {
        try {
            error_log("addVehicle called with driverId: $driverId, data: " . print_r($vehicleData, true));
            
            $query = "INSERT INTO vehicles (driverId, make, model, year, color, licensePlate, seatingCapacity, childSeats, fuelEfficiency, description, frontViewPhoto, backViewPhoto, sideViewPhoto, interiorPhoto1, interiorPhoto2, interiorPhoto3) VALUES (:driverId, :make, :model, :year, :color, :licensePlate, :seatingCapacity, :childSeats, :fuelEfficiency, :description, :frontViewPhoto, :backViewPhoto, :sideViewPhoto, :interiorPhoto1, :interiorPhoto2, :interiorPhoto3)";

            $this->db->query($query);

            $this->db->bind(':driverId', $driverId);
            $this->db->bind(':make', $vehicleData['make']);
            $this->db->bind(':model', $vehicleData['model']);
            $this->db->bind(':year', $vehicleData['year']);
            $this->db->bind(':color', $vehicleData['color']);
            $this->db->bind(':licensePlate', $vehicleData['licensePlate']);
            $this->db->bind(':seatingCapacity', $vehicleData['seatingCapacity']);
            $this->db->bind(':childSeats', $vehicleData['childSeats']);
            $this->db->bind(':fuelEfficiency', $vehicleData['fuelEfficiency']);
            $this->db->bind(':description', $vehicleData['description']);
            $this->db->bind(':frontViewPhoto', $vehicleData['frontViewPhoto'] ?? null);
            $this->db->bind(':backViewPhoto', $vehicleData['backViewPhoto'] ?? null);
            $this->db->bind(':sideViewPhoto', $vehicleData['sideViewPhoto'] ?? null);
            $this->db->bind(':interiorPhoto1', $vehicleData['interiorPhoto1'] ?? null);
            $this->db->bind(':interiorPhoto2', $vehicleData['interiorPhoto2'] ?? null);
            $this->db->bind(':interiorPhoto3', $vehicleData['interiorPhoto3'] ?? null);

            $result = $this->db->execute();
            error_log("Database execute result: " . ($result ? 'true' : 'false'));

            return $result;

        } catch (Exception $e) {
            error_log("Error adding vehicle: " . $e->getMessage());
            return false;
        }
    }

    public function getDriverVehicles($driverId) {
        try {
            $query = "SELECT 
                        vehicleId as id,
                        make,
                        model,
                        year,
                        color,
                        licensePlate as license_plate,
                        seatingCapacity as seating_capacity,
                        childSeats as child_seats,
                        fuelEfficiency as fuel_efficiency,
                        description,
                        frontViewPhoto,
                        backViewPhoto,
                        sideViewPhoto,
                        interiorPhoto1,
                        interiorPhoto2,
                        interiorPhoto3,
                        status,
                        availability,
                        isApproved,
                        createdAt as created_at,
                        updatedAt as updated_at
                      FROM vehicles 
                      WHERE driverId = :driverId 
                      ORDER BY createdAt DESC";

            $this->db->query($query);
            $this->db->bind(':driverId', $driverId);
            $result = $this->db->resultSet();

            // Process the results to format photos and status
            $vehicles = [];
            foreach ($result as $vehicle) {
                // Determine status based on isApproved
                $status = $vehicle->isApproved ? 'approved' : 'pending';
                
                // Build photos array
                $photos = [];
                if ($vehicle->frontViewPhoto) {
                    $photos[] = ['type' => 'front', 'url' => $vehicle->frontViewPhoto];
                }
                if ($vehicle->backViewPhoto) {
                    $photos[] = ['type' => 'back', 'url' => $vehicle->backViewPhoto];
                }
                if ($vehicle->sideViewPhoto) {
                    $photos[] = ['type' => 'side', 'url' => $vehicle->sideViewPhoto];
                }
                if ($vehicle->interiorPhoto1) {
                    $photos[] = ['type' => 'interior1', 'url' => $vehicle->interiorPhoto1];
                }
                if ($vehicle->interiorPhoto2) {
                    $photos[] = ['type' => 'interior2', 'url' => $vehicle->interiorPhoto2];
                }
                if ($vehicle->interiorPhoto3) {
                    $photos[] = ['type' => 'interior3', 'url' => $vehicle->interiorPhoto3];
                }

                $vehicles[] = [
                    'id' => $vehicle->id,
                    'make' => $vehicle->make,
                    'model' => $vehicle->model,
                    'year' => $vehicle->year,
                    'license_plate' => $vehicle->license_plate,
                    'color' => $vehicle->color,
                    'seating_capacity' => $vehicle->seating_capacity,
                    'child_seats' => $vehicle->child_seats,
                    'fuel_efficiency' => $vehicle->fuel_efficiency,
                    'description' => $vehicle->description,
                    'status' => $status,
                    'is_active' => $vehicle->status, // status field in DB represents active/inactive
                    'in_use' => !$vehicle->availability, // availability field represents in-use status
                    'photos' => $photos,
                    'created_at' => $vehicle->created_at
                ];
            }

            return $vehicles;

        } catch (Exception $e) {
            error_log("Error getting driver vehicles: " . $e->getMessage());
            return [];
        }
    }

    public function deleteVehicle($driverId, $vehicleId) {
        try {
            // First check if the vehicle belongs to the driver and is available for deletion
            $query = "SELECT availability FROM vehicles WHERE vehicleId = :vehicleId AND driverId = :driverId";
            $this->db->query($query);
            $this->db->bind(':vehicleId', $vehicleId);
            $this->db->bind(':driverId', $driverId);
            $result = $this->db->single();

            if (!$result) {
                return ['success' => false, 'message' => 'Vehicle not found or does not belong to you'];
            }

            if ($result->availability == 0) {
                return ['success' => false, 'message' => 'Cannot delete vehicle that is currently in use'];
            }

            // Delete the vehicle
            $deleteQuery = "DELETE FROM vehicles WHERE vehicleId = :vehicleId AND driverId = :driverId";
            $this->db->query($deleteQuery);
            $this->db->bind(':vehicleId', $vehicleId);
            $this->db->bind(':driverId', $driverId);

            $result = $this->db->execute();

            if ($result) {
                return ['success' => true, 'message' => 'Vehicle deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to delete vehicle'];
            }

        } catch (Exception $e) {
            error_log("Error deleting vehicle: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    public function toggleVehicleStatus($driverId, $vehicleId, $isActive) {
        try {
            // First check if the vehicle belongs to the driver and is approved
            $query = "SELECT isApproved, availability FROM vehicles WHERE vehicleId = :vehicleId AND driverId = :driverId";
            $this->db->query($query);
            $this->db->bind(':vehicleId', $vehicleId);
            $this->db->bind(':driverId', $driverId);
            $result = $this->db->single();

            if (!$result) {
                return ['success' => false, 'message' => 'Vehicle not found or does not belong to you'];
            }

            if (!$result->isApproved) {
                return ['success' => false, 'message' => 'Only approved vehicles can be activated/deactivated'];
            }

            // Convert boolean to integer for database (1 for active, 0 for inactive)
            $statusValue = $isActive ? 1 : 0;

            // Update the vehicle status
            $updateQuery = "UPDATE vehicles SET status = :status WHERE vehicleId = :vehicleId AND driverId = :driverId";
            $this->db->query($updateQuery);
            $this->db->bind(':status', $statusValue);
            $this->db->bind(':vehicleId', $vehicleId);
            $this->db->bind(':driverId', $driverId);

            $result = $this->db->execute();

            if ($result) {
                $statusText = $isActive ? 'active' : 'inactive';
                return ['success' => true, 'message' => "Vehicle marked as $statusText"];
            } else {
                return ['success' => false, 'message' => 'Failed to update vehicle status'];
            }

        } catch (Exception $e) {
            error_log("Error toggling vehicle status: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }
}