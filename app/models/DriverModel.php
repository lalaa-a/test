<?php

class DriverModel {
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    private function hasColumn($tableName, $columnName) {
        try {
            $this->db->query('SELECT COUNT(*) AS total
                              FROM information_schema.COLUMNS
                              WHERE TABLE_SCHEMA = :tableSchema
                                AND TABLE_NAME = :tableName
                                AND COLUMN_NAME = :columnName');
            $this->db->bind(':tableSchema', DB_NAME);
            $this->db->bind(':tableName', $tableName);
            $this->db->bind(':columnName', $columnName);
            $row = $this->db->single();

            return $row && (int)$row->total > 0;
        } catch (Exception $e) {
            error_log('DriverModel hasColumn check failed for ' . $tableName . '.' . $columnName . ': ' . $e->getMessage());
            return false;
        }
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

    public function getVehiclePricing($driverId) {
        try {
            $query = "SELECT 
                        vp.pricingId,
                        vp.vehicleId,
                        vp.driverId,
                        vp.vehicleChargePerKm,
                        vp.driverChargePerKm,
                        vp.vehicleChargePerDay,
                        vp.driverChargePerDay,
                        vp.minimumKm,
                        vp.minimumDays,
                        vp.createdAt,
                        vp.updatedAt,
                        v.make,
                        v.model,
                        v.year,
                        v.licensePlate
                      FROM vehicle_pricing vp
                      INNER JOIN vehicles v ON vp.vehicleId = v.vehicleId
                      WHERE vp.driverId = :driverId
                      ORDER BY vp.createdAt DESC";

            $this->db->query($query);
            $this->db->bind(':driverId', $driverId);
            $result = $this->db->resultSet();

            // Process the results
            $pricing = [];
            foreach ($result as $price) {
                $pricing[] = [
                    'pricingId' => $price->pricingId,
                    'vehicleId' => $price->vehicleId,
                    'driverId' => $price->driverId,
                    'vehicleChargePerKm' => (float)$price->vehicleChargePerKm,
                    'driverChargePerKm' => (float)$price->driverChargePerKm,
                    'vehicleChargePerDay' => (float)$price->vehicleChargePerDay,
                    'driverChargePerDay' => (float)$price->driverChargePerDay,
                    'minimumKm' => (float)$price->minimumKm,
                    'minimumDays' => (float)$price->minimumDays,
                    'vehicleInfo' => [
                        'make' => $price->make,
                        'model' => $price->model,
                        'year' => $price->year,
                        'licensePlate' => $price->licensePlate
                    ],
                    'createdAt' => $price->createdAt,
                    'updatedAt' => $price->updatedAt
                ];
            }

            return $pricing;

        } catch (Exception $e) {
            error_log("Error getting vehicle pricing: " . $e->getMessage());
            return [];
        }
    }

    public function getVerifiedVehiclesWithPricing($driverId) {
        try {
            $query = "SELECT
                        v.vehicleId,
                        v.make,
                        v.model,
                        v.year,
                        v.licensePlate,
                        v.seatingCapacity,
                        v.availability,
                        vp.vehicleChargePerKm,
                        vp.driverChargePerKm,
                        vp.vehicleChargePerDay,
                        vp.driverChargePerDay,
                        vp.minimumKm,
                        vp.minimumDays
                      FROM vehicles v
                      LEFT JOIN vehicle_pricing vp ON v.vehicleId = vp.vehicleId
                      WHERE v.driverId = :driverId AND v.status = 1
                      ORDER BY v.createdAt DESC";

            $this->db->query($query);
            $this->db->bind(':driverId', $driverId);
            $result = $this->db->resultSet();

            $vehicles = [];
            foreach ($result as $row) {
                $vehicle = [
                    'vehicleId' => $row->vehicleId,
                    'make' => $row->make,
                    'model' => $row->model,
                    'year' => $row->year,
                    'licensePlate' => $row->licensePlate,
                    'seatingCapacity' => $row->seatingCapacity,
                    'availability' => $row->availability
                ];

                if ($row->vehicleChargePerKm !== null) {
                    $vehicle['pricing'] = [
                        'vehicleChargePerKm' => $row->vehicleChargePerKm,
                        'driverChargePerKm' => $row->driverChargePerKm,
                        'vehicleChargePerDay' => $row->vehicleChargePerDay,
                        'driverChargePerDay' => $row->driverChargePerDay,
                        'minimumKm' => $row->minimumKm,
                        'minimumDays' => $row->minimumDays
                    ];
                }

                $vehicles[] = $vehicle;
            }

            return $vehicles;
        } catch (Exception $e) {
            error_log("Error getting verified vehicles with pricing: " . $e->getMessage());
            return [];
        }
    }

    public function getVehicleById($vehicleId) {
        try {
            $query = "SELECT vehicleId, driverId, status FROM vehicles WHERE vehicleId = :vehicleId";
            $this->db->query($query);
            $this->db->bind(':vehicleId', $vehicleId);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error getting vehicle by ID: " . $e->getMessage());
            return null;
        }
    }

    public function saveVehiclePricing($pricingData) {
        try {
            // Check if pricing already exists
            $query = "SELECT pricingId FROM vehicle_pricing WHERE vehicleId = :vehicleId";
            $this->db->query($query);
            $this->db->bind(':vehicleId', $pricingData['vehicleId']);
            $existing = $this->db->single();

            if ($existing) {
                // Update existing pricing
                $query = "UPDATE vehicle_pricing SET
                            vehicleChargePerKm = :vehicleChargePerKm,
                            driverChargePerKm = :driverChargePerKm,
                            vehicleChargePerDay = :vehicleChargePerDay,
                            driverChargePerDay = :driverChargePerDay,
                            minimumKm = :minimumKm,
                            minimumDays = :minimumDays,
                            updatedAt = CURRENT_TIMESTAMP
                          WHERE vehicleId = :vehicleId";
            } else {
                // Insert new pricing
                $query = "INSERT INTO vehicle_pricing
                            (vehicleId, driverId, vehicleChargePerKm, driverChargePerKm, vehicleChargePerDay, driverChargePerDay, minimumKm, minimumDays)
                          VALUES
                            (:vehicleId, :driverId, :vehicleChargePerKm, :driverChargePerKm, :vehicleChargePerDay, :driverChargePerDay, :minimumKm, :minimumDays)";
            }

            $this->db->query($query);
            
            if ($existing) {
                // For UPDATE, only bind the fields being updated and the WHERE clause
                $this->db->bind(':vehicleChargePerKm', $pricingData['vehicleChargePerKm']);
                $this->db->bind(':driverChargePerKm', $pricingData['driverChargePerKm']);
                $this->db->bind(':vehicleChargePerDay', $pricingData['vehicleChargePerDay']);
                $this->db->bind(':driverChargePerDay', $pricingData['driverChargePerDay']);
                $this->db->bind(':minimumKm', $pricingData['minimumKm']);
                $this->db->bind(':minimumDays', $pricingData['minimumDays']);
                $this->db->bind(':vehicleId', $pricingData['vehicleId']);
            } else {
                // For INSERT, bind all fields
                $this->db->bind(':vehicleId', $pricingData['vehicleId']);
                $this->db->bind(':driverId', $pricingData['driverId']);
                $this->db->bind(':vehicleChargePerKm', $pricingData['vehicleChargePerKm']);
                $this->db->bind(':driverChargePerKm', $pricingData['driverChargePerKm']);
                $this->db->bind(':vehicleChargePerDay', $pricingData['vehicleChargePerDay']);
                $this->db->bind(':driverChargePerDay', $pricingData['driverChargePerDay']);
                $this->db->bind(':minimumKm', $pricingData['minimumKm']);
                $this->db->bind(':minimumDays', $pricingData['minimumDays']);
            }

            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error saving vehicle pricing: " . $e->getMessage());
            return false;
        }
    }

    public function deleteVehiclePricing($vehicleId) {
        try {
            $query = "DELETE FROM vehicle_pricing WHERE vehicleId = :vehicleId";
            $this->db->query($query);
            $this->db->bind(':vehicleId', $vehicleId);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error deleting vehicle pricing: " . $e->getMessage());
            return false;
        }
    }

    // Driver Availability Methods
    public function getDriverUnavailableDates($driverId, $startDate = null, $endDate = null) {
        try {
            $query = "SELECT id, unavailableDate, reason, personalReason, tripId, createdAt
                     FROM driver_unavailable_dates
                     WHERE driverId = :driverId";

            $params = [':driverId' => $driverId];

            if ($startDate && $endDate) {
                $query .= " AND unavailableDate BETWEEN :startDate AND :endDate";
                $params[':startDate'] = $startDate;
                $params[':endDate'] = $endDate;
            }

            $query .= " ORDER BY unavailableDate ASC";

            $this->db->query($query);

            foreach ($params as $param => $value) {
                $this->db->bind($param, $value);
            }

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error getting driver unavailable dates: " . $e->getMessage());
            return [];
        }
    }

    public function addDriverUnavailableDate($driverId, $date, $reason, $personalReason = null, $tripId = null) {
        try {
            // Check if date already exists for this driver
            $existing = $this->getDriverUnavailableDate($driverId, $date);
            if ($existing) {
                // Update existing record
                $query = "UPDATE driver_unavailable_dates
                         SET reason = :reason, personalReason = :personalReason, tripId = :tripId, updatedAt = CURRENT_TIMESTAMP
                         WHERE driverId = :driverId AND unavailableDate = :date";
            } else {
                // Insert new record
                $query = "INSERT INTO driver_unavailable_dates (driverId, unavailableDate, reason, personalReason, tripId)
                         VALUES (:driverId, :date, :reason, :personalReason, :tripId)";
            }

            $this->db->query($query);
            $this->db->bind(':driverId', $driverId);
            $this->db->bind(':date', $date);
            $this->db->bind(':reason', $reason);
            $this->db->bind(':personalReason', $personalReason);
            $this->db->bind(':tripId', $tripId);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error adding driver unavailable date: " . $e->getMessage());
            return false;
        }
    }

    public function removeDriverUnavailableDate($driverId, $date) {
        try {
            $query = "DELETE FROM driver_unavailable_dates
                     WHERE driverId = :driverId AND unavailableDate = :date AND reason = 'personal'";

            $this->db->query($query);
            $this->db->bind(':driverId', $driverId);
            $this->db->bind(':date', $date);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error removing driver unavailable date: " . $e->getMessage());
            return false;
        }
    }

    public function getDriverUnavailableDate($driverId, $date) {
        try {
            $query = "SELECT * FROM driver_unavailable_dates
                     WHERE driverId = :driverId AND unavailableDate = :date";

            $this->db->query($query);
            $this->db->bind(':driverId', $driverId);
            $this->db->bind(':date', $date);

            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error getting driver unavailable date: " . $e->getMessage());
            return null;
        }
    }

    public function bulkUpdateDriverAvailability($driverId, $unavailableDates) {
        try {
            $this->db->beginTransaction();

            // First, remove all existing personal unavailable dates for this driver
            $query = "DELETE FROM driver_unavailable_dates
                     WHERE driverId = :driverId AND reason = 'personal'";
            $this->db->query($query);
            $this->db->bind(':driverId', $driverId);
            $this->db->execute();

            // Then add the new unavailable dates
            if (!empty($unavailableDates)) {
                foreach ($unavailableDates as $dateInfo) {
                    $query = "INSERT INTO driver_unavailable_dates (driverId, unavailableDate, reason, personalReason)
                             VALUES (:driverId, :date, 'personal', :reason)";

                    $this->db->query($query);
                    $this->db->bind(':driverId', $driverId);
                    $this->db->bind(':date', $dateInfo['date']);
                    $this->db->bind(':reason', $dateInfo['reason'] ?? null);
                    $this->db->execute();
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error bulk updating driver availability: " . $e->getMessage());
            return false;
        }
    }

    public function isDriverAvailableOnDate($driverId, $date) {
        try {
            $query = "SELECT COUNT(*) as count FROM driver_unavailable_dates
                     WHERE driverId = :driverId AND unavailableDate = :date";

            $this->db->query($query);
            $this->db->bind(':driverId', $driverId);
            $this->db->bind(':date', $date);

            $result = $this->db->single();
            return $result->count == 0; // Available if no unavailable record exists
        } catch (Exception $e) {
            error_log("Error checking driver availability: " . $e->getMessage());
            return true; // Default to available on error
        }
    }

    /**
     * Requests related methods
     */
    public function getRequestsByDriver($driverId) {
        try {
            // Only fetch requests relevant to driver-facing sections (pending/requested, accepted, rejected)
            $query = "SELECT * FROM traveller_side_t_requests WHERE driverId = :driverId AND requestStatus IN ('requested','accepted','rejected') ORDER BY createdAt DESC";
            $this->db->query($query);
            $this->db->bind(':driverId', $driverId);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error fetching requests for driver $driverId: " . $e->getMessage());
            return [];
        }
    }

    public function getRequestById($driverId, $requestId) {
        try {
            $query = "SELECT * FROM traveller_side_t_requests WHERE driverId = :driverId AND requestId = :requestId LIMIT 1";
            $this->db->query($query);
            $this->db->bind(':driverId', $driverId);
            $this->db->bind(':requestId', $requestId);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error fetching request $requestId for driver $driverId: " . $e->getMessage());
            return null;
        }
    }

    private function hasAllProviderConfirmationsForTrip($userId, $tripId) {
        $this->db->query("SELECT
                COUNT(*) AS totalRequests,
                SUM(CASE WHEN requestStatus = 'accepted' THEN 1 ELSE 0 END) AS acceptedRequests
            FROM traveller_side_d_requests
            WHERE tripId = :tripId
              AND rqUserId = :userId
                            AND requestStatus IN ('pending', 'requested', 'accepted', 'rejected')");
        $this->db->bind(':tripId', (int)$tripId);
        $this->db->bind(':userId', (int)$userId);
        $driverStats = $this->db->single();

        $totalDrivers = isset($driverStats->totalRequests) ? (int)$driverStats->totalRequests : 0;
        $acceptedDrivers = isset($driverStats->acceptedRequests) ? (int)$driverStats->acceptedRequests : 0;

        if ($totalDrivers === 0 || $acceptedDrivers < $totalDrivers) {
            return false;
        }

        $this->db->query("SELECT
                COUNT(*) AS totalRequests,
                SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) AS acceptedRequests
            FROM traveller_side_g_requests
            WHERE tripId = :tripId
              AND userId = :userId
              AND guideId IS NOT NULL
                            AND status IN ('pending', 'requested', 'accepted', 'rejected')");
        $this->db->bind(':tripId', (int)$tripId);
        $this->db->bind(':userId', (int)$userId);
        $guideStats = $this->db->single();

        $totalGuides = isset($guideStats->totalRequests) ? (int)$guideStats->totalRequests : 0;
        $acceptedGuides = isset($guideStats->acceptedRequests) ? (int)$guideStats->acceptedRequests : 0;

        return $acceptedGuides >= $totalGuides;
    }

    private function promoteTripToScheduledIfReady($userId, $tripId) {
        $this->db->query("SELECT status
                         FROM created_trips
                         WHERE tripId = :tripId AND userId = :userId
                         LIMIT 1");
        $this->db->bind(':tripId', (int)$tripId);
        $this->db->bind(':userId', (int)$userId);
        $trip = $this->db->single();

        if (!$trip || $trip->status !== 'wConfirmation') {
            return;
        }

        if (!$this->hasAllProviderConfirmationsForTrip($userId, $tripId)) {
            return;
        }

        $this->db->query("UPDATE created_trips
                                                 SET status = 'awPayment', updatedAt = CURRENT_TIMESTAMP
                         WHERE tripId = :tripId
                           AND userId = :userId
                           AND status = 'wConfirmation'");
        $this->db->bind(':tripId', (int)$tripId);
        $this->db->bind(':userId', (int)$userId);
        $this->db->execute();
    }

    private function moveTripToPendingOnRejection($userId, $tripId) {
        $this->db->query("UPDATE created_trips
                         SET status = 'pending', updatedAt = CURRENT_TIMESTAMP
                         WHERE tripId = :tripId
                           AND userId = :userId
                                                                                                         AND status IN ('wConfirmation', 'awPayment', 'scheduled')");
        $this->db->bind(':tripId', (int)$tripId);
        $this->db->bind(':userId', (int)$userId);
        $this->db->execute();
    }

    public function updateRequestStatus($driverId, $requestId, $status) {
        try {
            // Ensure the request belongs to the driver
            $existing = $this->getRequestById($driverId, $requestId);
            if (!$existing) {
                return ['success' => false, 'message' => 'Request not found or does not belong to you'];
            }

            $query = "UPDATE traveller_side_t_requests SET requestStatus = :status, respondedAt = CURRENT_TIMESTAMP WHERE requestId = :requestId AND driverId = :driverId";
            $this->db->query($query);
            $this->db->bind(':status', $status);
            $this->db->bind(':requestId', $requestId);
            $this->db->bind(':driverId', $driverId);

            $res = $this->db->execute();
            if ($res) {
                try {
                    $syncQuery = "UPDATE traveller_side_d_requests
                                  SET requestStatus = :status,
                                      respondedAt = CURRENT_TIMESTAMP,
                                      updatedAt = CURRENT_TIMESTAMP
                                  WHERE tripId = :tripId
                                    AND rqUserId = :rqUserId
                                    AND driverId = :driverId
                                                                        AND requestStatus IN ('pending', 'requested', 'accepted', 'rejected')";
                    $this->db->query($syncQuery);
                    $this->db->bind(':status', $status);
                    $this->db->bind(':tripId', (int)$existing->tripId);
                    $this->db->bind(':rqUserId', (int)$existing->rqUserId);
                    $this->db->bind(':driverId', (int)$driverId);
                    $this->db->execute();

                    if ($status === 'accepted') {
                        $this->promoteTripToScheduledIfReady((int)$existing->rqUserId, (int)$existing->tripId);
                    } elseif ($status === 'rejected') {
                        $this->moveTripToPendingOnRejection((int)$existing->rqUserId, (int)$existing->tripId);
                    }
                } catch (Exception $syncError) {
                    error_log("Driver request sync warning for request $requestId: " . $syncError->getMessage());
                }

                return ['success' => true, 'message' => 'Status updated'];
            }
            return ['success' => false, 'message' => 'Failed to update status'];
        } catch (Exception $e) {
            error_log("Error updating request status for $requestId: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    public function getRequestStats($driverId) {
        try {
            $query = "SELECT requestStatus, COUNT(*) as cnt FROM traveller_side_t_requests WHERE driverId = :driverId GROUP BY requestStatus";
            $this->db->query($query);
            $this->db->bind(':driverId', $driverId);
            $rows = $this->db->resultSet();

            // Return stats only for the UI sections: pending (includes 'requested'), accepted, rejected
            $stats = [
                'pending' => 0,
                'accepted' => 0,
                'rejected' => 0
            ];

            foreach ($rows as $r) {
                $status = $r->requestStatus;
                $count = intval($r->cnt);
                if ($status === 'requested') {
                    $stats['pending'] += $count; // treat 'requested' as a pending variant
                } elseif (array_key_exists($status, $stats)) {
                    $stats[$status] = $count;
                }
            }

            return $stats;
        } catch (Exception $e) {
            error_log("Error getting request stats for driver $driverId: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Trip Itinerary Methods
     */
    public function getTripById($tripId) {
        try {
            $query = "SELECT tripId, userId, tripTitle, description, startDate, endDate, status, numberOfPeople, createdAt, updatedAt 
                     FROM created_trips 
                     WHERE tripId = :tripId 
                     LIMIT 1";
            $this->db->query($query);
            $this->db->bind(':tripId', $tripId);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error fetching trip $tripId: " . $e->getMessage());
            return null;
        }
    }

    public function getTripEventsByTripId($tripId) {
        try {
                        $itineraryTable = $this->getTravelSpotItineraryTableName();
                        $latitudeSelect = 'te.latitude';
                        $longitudeSelect = 'te.longitude';
                        $itineraryJoin = '';

                        if ($itineraryTable !== null) {
                                $latitudeSelect = 'COALESCE(tsi_first.latitude, te.latitude) AS latitude';
                                $longitudeSelect = 'COALESCE(tsi_first.longitude, te.longitude) AS longitude';
                                $itineraryJoin = "LEFT JOIN {$itineraryTable} tsi_first
                                                                     ON tsi_first.pointId = (
                                                                                SELECT tsi_sub.pointId
                                                                                FROM {$itineraryTable} tsi_sub
                                                                                WHERE tsi_sub.spotId = te.travelSpotId
                                                                                    AND tsi_sub.latitude IS NOT NULL
                                                                                    AND tsi_sub.longitude IS NOT NULL
                                                                                ORDER BY tsi_sub.pointOrder ASC, tsi_sub.pointId ASC
                                                                                LIMIT 1
                                                                     )";
                        }

            $query = "SELECT te.eventId, te.tripId, te.userId, te.eventDate, te.startTime, te.endTime, te.eventType, te.eventStatus, 
                                                         te.travelSpotId, te.locationName, {$latitudeSelect}, {$longitudeSelect}, te.description, te.created_at, te.updated_at,
                             ts.spotName, ts.overview as spotDescription, ts.averageRating
                     FROM trip_events te
                                         {$itineraryJoin}
                     LEFT JOIN travel_spots ts ON te.travelSpotId = ts.spotId
                     WHERE te.tripId = :tripId 
                     ORDER BY te.eventDate ASC, te.startTime ASC";
            $this->db->query($query);
            $this->db->bind(':tripId', $tripId);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error fetching trip events for trip $tripId: " . $e->getMessage());
            return [];
        }
    }

    public function getTripItinerary($tripId) {
        try {
            $trip = $this->getTripById($tripId);
            if (!$trip) {
                return null;
            }

            $events = $this->getTripEventsByTripId($tripId);

            // Format trip data for frontend
            return [
                'trip' => [
                    'tripId' => $trip->tripId,
                    'tripTitle' => $trip->tripTitle,
                    'description' => $trip->description,
                    'startDate' => $trip->startDate,
                    'endDate' => $trip->endDate,
                    'status' => $trip->status,
                    'numberOfPeople' => $trip->numberOfPeople,
                    'createdAt' => $trip->createdAt,
                    'updatedAt' => $trip->updatedAt
                ],
                'events' => $events
            ];
        } catch (Exception $e) {
            error_log("Error fetching trip itinerary for trip $tripId: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Driver Tours Methods
     */
    public function getDriverTours($driverId) {
        try {
            $acceptedTripsTable = $this->getAcceptedTripsTableName();
            $startPinSelect = $this->hasColumn('created_trips', 'startPin') ? 'ct.startPin AS startPin' : 'NULL AS startPin';
            $pinMatchSelect = $this->hasColumn('created_trips', 'pinMatch') ? 'ct.pinMatch AS pinMatch' : '0 AS pinMatch';

            $query = "SELECT
                        dat.acceptId,
                        dat.tripId,
                        dat.rqUserId,
                        dat.driverId,
                        dat.driverName,
                        dat.driverProfilePhoto,
                        dat.driverRating,
                        dat.verifyStatus,
                        dat.vehicleId,
                        dat.vehicleYear,
                        dat.vehicleType,
                        dat.vehiclePhoto,
                        dat.vehicleCapacity,
                        dat.childSeats,
                        dat.chargeType,
                        dat.totalKm,
                        dat.totalAmount,
                        dat.doneStatus,
                        dat.paymentStatus,
                        dat.completedAt,
                        dat.createdAt,
                        dat.updatedAt,
                        ct.tripTitle,
                        ct.description,
                        ct.startDate,
                        ct.endDate,
                        ct.status as tripStatus,
                                                {$startPinSelect},
                                                {$pinMatchSelect},
                        u.fullname as rqUserName,
                        u.profile_photo as rqUserProfilePhoto,
                        v.model as vehicleModel
                                            FROM {$acceptedTripsTable} dat
                      LEFT JOIN created_trips ct ON dat.tripId = ct.tripId
                      LEFT JOIN users u ON dat.rqUserId = u.id
                      LEFT JOIN vehicles v ON dat.vehicleId = v.vehicleId
                      WHERE dat.driverId = :driverId
                      ORDER BY dat.createdAt DESC";

            $this->db->query($query);
            $this->db->bind(':driverId', $driverId);
            $tours = $this->db->resultSet();

            // Categorize tours
            $categorizedTours = [
                'ongoing' => [],
                'upcoming' => [],
                'completed' => []
            ];

            $today = date('Y-m-d');

            foreach ($tours as $tour) {
                // Convert to array for easier manipulation
                $tourArray = (array) $tour;

                // Determine category
                if ($tour->doneStatus == 1) {
                    $categorizedTours['completed'][] = $tourArray;
                } elseif ($tour->tripStatus === 'ongoing' || $tour->startDate === $today) {
                    $categorizedTours['ongoing'][] = $tourArray;
                } else {
                    $categorizedTours['upcoming'][] = $tourArray;
                }
            }

            return $categorizedTours;

        } catch (Exception $e) {
            error_log("Error getting driver tours for driver $driverId: " . $e->getMessage());
            return [
                'ongoing' => [],
                'upcoming' => [],
                'completed' => []
            ];
        }
    }

    public function getTourDetails($driverId, $acceptId) {
        try {
            $acceptedTripsTable = $this->getAcceptedTripsTableName();
            $startPinSelect = $this->hasColumn('created_trips', 'startPin') ? 'ct.startPin AS startPin' : 'NULL AS startPin';
            $pinMatchSelect = $this->hasColumn('created_trips', 'pinMatch') ? 'ct.pinMatch AS pinMatch' : '0 AS pinMatch';

            $query = "SELECT
                        dat.acceptId,
                        dat.tripId,
                        dat.rqUserId,
                        dat.driverId,
                        dat.driverName,
                        dat.driverProfilePhoto,
                        dat.driverRating,
                        dat.verifyStatus,
                        dat.vehicleId,
                        dat.vehicleYear,
                        dat.vehicleType,
                        dat.vehiclePhoto,
                        dat.vehicleCapacity,
                        dat.childSeats,
                        dat.chargeType,
                        dat.totalKm,
                        dat.totalAmount,
                        dat.doneStatus,
                        dat.paymentStatus,
                        dat.completedAt,
                        dat.createdAt,
                        dat.updatedAt,
                        ct.tripTitle,
                        ct.description,
                        ct.startDate,
                        ct.endDate,
                        ct.status as tripStatus,
                                                {$startPinSelect},
                                                {$pinMatchSelect},
                        u.fullname as rqUserName,
                        u.profile_photo as rqUserProfilePhoto,
                        v.model as vehicleModel
                                            FROM {$acceptedTripsTable} dat
                      LEFT JOIN created_trips ct ON dat.tripId = ct.tripId
                      LEFT JOIN users u ON dat.rqUserId = u.id
                      LEFT JOIN vehicles v ON dat.vehicleId = v.vehicleId
                                            WHERE dat.acceptId = :acceptId
                                                AND dat.driverId = :driverId
                      LIMIT 1";

            $this->db->query($query);
            $this->db->bind(':acceptId', $acceptId);
                        $this->db->bind(':driverId', $driverId);
            $tour = $this->db->single();

            if (!$tour) {
                return null;
            }

            return (array) $tour;

        } catch (Exception $e) {
            error_log("Error getting tour details for acceptId $acceptId: " . $e->getMessage());
            return null;
        }
    }

    public function startTrip($driverId, $tripId, $pin) {
        try {
            $acceptedTripsTable = $this->getAcceptedTripsTableName();
            $hasStartPin = $this->hasColumn('created_trips', 'startPin');
            $hasPinMatch = $this->hasColumn('created_trips', 'pinMatch');

            if (!$hasStartPin) {
                return ['success' => false, 'message' => 'Trip start PIN is not configured in this environment'];
            }

            $query = "SELECT acceptId
                      FROM {$acceptedTripsTable}
                      WHERE tripId = :tripId
                        AND driverId = :driverId
                      LIMIT 1";

            $this->db->query($query);
            $this->db->bind(':tripId', $tripId);
            $this->db->bind(':driverId', $driverId);
            $acceptedTrip = $this->db->single();

            if (!$acceptedTrip) {
                return ['success' => false, 'message' => 'You are not assigned to this trip'];
            }

            $tripSelect = $hasPinMatch
                ? "SELECT startPin, pinMatch, status FROM created_trips WHERE tripId = :tripId LIMIT 1"
                : "SELECT startPin, status FROM created_trips WHERE tripId = :tripId LIMIT 1";

            // Verify PIN issued by traveller
            $this->db->query($tripSelect);
            $this->db->bind(':tripId', $tripId);
            $trip = $this->db->single();

            if (!$trip) {
                return ['success' => false, 'message' => 'Trip not found'];
            }

            $storedPin = trim((string)($trip->startPin ?? ''));
            if ($storedPin === '') {
                return ['success' => false, 'message' => 'Traveller has not generated a start PIN yet'];
            }

            if ($storedPin !== trim((string)$pin)) {
                return ['success' => false, 'message' => 'Invalid PIN'];
            }

            $setParts = [
                "status = 'ongoing'",
                'updatedAt = CURRENT_TIMESTAMP'
            ];

            if ($hasPinMatch) {
                $setParts[] = 'pinMatch = 1';
            }

            $updateQuery = 'UPDATE created_trips SET ' . implode(', ', $setParts) . ' WHERE tripId = :tripId';
            $this->db->query($updateQuery);
            $this->db->bind(':tripId', $tripId);

            if ($this->db->execute()) {
                return ['success' => true, 'message' => 'PIN matched and trip started successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to start trip'];
            }

        } catch (Exception $e) {
            error_log("Error starting trip $tripId: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

        public function getTripEvents($driverId, $tripId) {
        try {
                        $acceptedTripsTable = $this->getAcceptedTripsTableName();
                                                $itineraryTable = $this->getTravelSpotItineraryTableName();
                        $hasStartPin = $this->hasColumn('created_trips', 'startPin');
                        $hasPinMatch = $this->hasColumn('created_trips', 'pinMatch');
                        $startPinSelect = $hasStartPin ? 'ct.startPin' : "'' AS startPin";
                        $pinMatchSelect = $hasPinMatch ? 'ct.pinMatch' : '0 AS pinMatch';
                                                $latitudeSelect = 'te.latitude';
                                                $longitudeSelect = 'te.longitude';
                                                $itineraryJoin = '';

                                                if ($itineraryTable !== null) {
                                                                $latitudeSelect = 'COALESCE(tsi_first.latitude, te.latitude) AS latitude';
                                                                $longitudeSelect = 'COALESCE(tsi_first.longitude, te.longitude) AS longitude';
                                                                $itineraryJoin = "LEFT JOIN {$itineraryTable} tsi_first
                                                                                                                                     ON tsi_first.pointId = (
                                                                                                                                                SELECT tsi_sub.pointId
                                                                                                                                                FROM {$itineraryTable} tsi_sub
                                                                                                                                                WHERE tsi_sub.spotId = te.travelSpotId
                                                                                                                                                    AND tsi_sub.latitude IS NOT NULL
                                                                                                                                                    AND tsi_sub.longitude IS NOT NULL
                                                                                                                                                ORDER BY tsi_sub.pointOrder ASC, tsi_sub.pointId ASC
                                                                                                                                                LIMIT 1
                                                                                                                                     )";
                                                }

                        $tripSelect = "SELECT ct.tripId,
                                                                    ct.status,
                                                                    {$startPinSelect},
                                                                    {$pinMatchSelect}
                                                     FROM created_trips ct
                                                     INNER JOIN {$acceptedTripsTable} dat ON dat.tripId = ct.tripId
                                                     WHERE ct.tripId = :tripId
                                                         AND dat.driverId = :driverId
                                                     LIMIT 1";

                        $this->db->query($tripSelect);
                        $this->db->bind(':tripId', $tripId);
                        $this->db->bind(':driverId', $driverId);
                        $trip = $this->db->single();

                        if (!$trip) {
                                return ['success' => false, 'message' => 'Trip not found for this driver', 'events' => []];
                        }

            $query = "SELECT
                        te.eventId,
                        te.tripId,
                        te.eventDate,
                        te.startTime,
                        te.endTime,
                        te.eventType,
                        te.eventStatus,
                        te.travelSpotId,
                        te.locationName,
                                                {$latitudeSelect},
                                                {$longitudeSelect},
                        te.description,
                        te.dDone,
                        te.gDone,
                        te.tDoneDriver,
                        ts.spotName,
                        ts.overview as spotDescription,
                        ts.averageRating
                      FROM trip_events te
                                            {$itineraryJoin}
                      LEFT JOIN travel_spots ts ON te.travelSpotId = ts.spotId
                      WHERE te.tripId = :tripId
                      ORDER BY te.eventDate ASC, te.startTime ASC";

            $this->db->query($query);
            $this->db->bind(':tripId', $tripId);
            $events = $this->db->resultSet();

            $events = array_map(function($event) {
                return (array) $event;
            }, $events);

            $startPinValue = $hasStartPin ? trim((string)($trip->startPin ?? '')) : '';
            $pinMatched = $hasPinMatch ? (int)($trip->pinMatch ?? 0) === 1 : true;

            return [
                'success' => true,
                'events' => $events,
                'trip' => [
                    'tripId' => (int)($trip->tripId ?? $tripId),
                    'status' => (string)($trip->status ?? ''),
                    'startPinRequired' => $hasStartPin,
                    'hasStartPinValue' => $startPinValue !== '',
                    'pinMatched' => $pinMatched
                ],
                'featureFlags' => [
                    'hasStartPin' => $hasStartPin,
                    'hasPinMatch' => $hasPinMatch
                ]
            ];

        } catch (Exception $e) {
            error_log("Error getting trip events for trip $tripId: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred', 'events' => []];
        }
    }

    public function markEventComplete($driverId, $eventId) {
        try {
            $acceptedTripsTable = $this->getAcceptedTripsTableName();
            $hasPinMatch = $this->hasColumn('created_trips', 'pinMatch');

            $eventMetaQuery = $hasPinMatch
                ? "SELECT te.eventId, te.tripId, te.dDone, ct.status, ct.pinMatch
                   FROM trip_events te
                   INNER JOIN {$acceptedTripsTable} dat ON dat.tripId = te.tripId
                   INNER JOIN created_trips ct ON ct.tripId = te.tripId
                   WHERE te.eventId = :eventId
                     AND dat.driverId = :driverId
                   LIMIT 1"
                : "SELECT te.eventId, te.tripId, te.dDone, ct.status
                   FROM trip_events te
                   INNER JOIN {$acceptedTripsTable} dat ON dat.tripId = te.tripId
                   INNER JOIN created_trips ct ON ct.tripId = te.tripId
                   WHERE te.eventId = :eventId
                     AND dat.driverId = :driverId
                   LIMIT 1";

            $this->db->query($eventMetaQuery);
            $this->db->bind(':eventId', $eventId);
            $this->db->bind(':driverId', $driverId);
            $eventMeta = $this->db->single();

            if (!$eventMeta) {
                return ['success' => false, 'message' => 'Event not found for this driver'];
            }

            if ((int)($eventMeta->dDone ?? 0) === 1) {
                return ['success' => true, 'message' => 'Event already marked as completed'];
            }

            if (strtolower((string)($eventMeta->status ?? '')) !== 'ongoing') {
                return ['success' => false, 'message' => 'Trip must be ongoing before marking events'];
            }

            if ($hasPinMatch && (int)($eventMeta->pinMatch ?? 0) !== 1) {
                return ['success' => false, 'message' => 'Enter the correct traveller PIN and start the trip first'];
            }

            $query = "UPDATE trip_events
                      SET dDone = 1,
                          updated_at = CURRENT_TIMESTAMP
                      WHERE eventId = :eventId
                        AND tripId = :tripId";
            $this->db->query($query);
            $this->db->bind(':eventId', $eventId);
            $this->db->bind(':tripId', $eventMeta->tripId);

            if ($this->db->execute()) {
                return ['success' => true, 'message' => 'Event marked as completed'];
            } else {
                return ['success' => false, 'message' => 'Failed to mark event as completed'];
            }

        } catch (Exception $e) {
            error_log("Error marking event $eventId as complete: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    public function completeTrip($driverId, $tripId) {
        try {
            $acceptedTripsTable = $this->getAcceptedTripsTableName();

            $ownershipQuery = "SELECT acceptId
                               FROM {$acceptedTripsTable}
                               WHERE tripId = :tripId
                                 AND driverId = :driverId
                               LIMIT 1";
            $this->db->query($ownershipQuery);
            $this->db->bind(':tripId', $tripId);
            $this->db->bind(':driverId', $driverId);
            $acceptedTrip = $this->db->single();

            if (!$acceptedTrip) {
                return ['success' => false, 'message' => 'Trip not found for this driver'];
            }

            $pendingEventsQuery = "SELECT COUNT(*) AS pendingEvents
                                   FROM trip_events
                                   WHERE tripId = :tripId
                                     AND (dDone IS NULL OR dDone = 0)";
            $this->db->query($pendingEventsQuery);
            $this->db->bind(':tripId', $tripId);
            $pendingEventsRow = $this->db->single();
            if ($pendingEventsRow && (int)$pendingEventsRow->pendingEvents > 0) {
                return ['success' => false, 'message' => 'Complete all trip events before finishing the trip'];
            }

            // Start transaction
            $this->db->beginTransaction();

            // Update trip status to completed
            $query = "UPDATE created_trips SET status = 'completed', updatedAt = CURRENT_TIMESTAMP WHERE tripId = :tripId";
            $this->db->query($query);
            $this->db->bind(':tripId', $tripId);
            $this->db->execute();

                        // Update accepted trip record to mark as done
                        $query = "UPDATE {$acceptedTripsTable}
                                            SET doneStatus = 1,
                                                    completedAt = CURRENT_TIMESTAMP,
                                                    updatedAt = CURRENT_TIMESTAMP
                                            WHERE tripId = :tripId
                                                AND driverId = :driverId";
            $this->db->query($query);
            $this->db->bind(':tripId', $tripId);
                        $this->db->bind(':driverId', $driverId);
            $this->db->execute();

            $this->db->commit();

            return ['success' => true, 'message' => 'Trip completed successfully'];

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error completing trip $tripId: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }
    public function getDriverProfileByDriverId($driverId) {
        try {
            $query = 'SELECT 
                        u.id as userId,
                        u.fullname as profile_name,
                        u.phone,
                        u.email,
                        u.profile_photo,
                        p.bio,
                        p.languages,
                        p.instaAccount,
                        p.facebookAccount,
                        p.dlVerified,
                        p.tlSubmitted,
                        p.tlVerified,
                        p.tLicenseNumber,
                        p.tLicenseExpiryDate,
                        p.averageRating
                     FROM users u
                     JOIN profile_details p ON u.id = p.userId
                     WHERE u.id = :driverId';

            $this->db->query($query);
            $this->db->bind(':driverId', $driverId);
            $result = $this->db->single();

            if (!$result) {
                return null;
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error getting driver profile: " . $e->getMessage());
            return null;
        }
    }

    public function getDriverVehiclesById($driverId) {
        try {
            $query = 'SELECT 
                        v.vehicleId,
                        v.driverId,
                        v.make,
                        v.model,
                        v.year,
                        v.color,
                        v.licensePlate,
                        v.seatingCapacity,
                        v.childSeats,
                        v.fuelEfficiency,
                        v.description,
                        v.frontViewPhoto,
                        v.backViewPhoto,
                        v.sideViewPhoto,
                        v.interiorPhoto1,
                        v.interiorPhoto2,
                        v.interiorPhoto3,
                        v.availability,
                        v.isApproved,
                        vp.vehicleChargePerKm,
                        vp.driverChargePerKm,
                        vp.vehicleChargePerDay,
                        vp.driverChargePerDay,
                        vp.minimumKm,
                        vp.minimumDays
                     FROM vehicles v
                     LEFT JOIN vehicle_pricing vp ON v.vehicleId = vp.vehicleId
                     WHERE v.driverId = :driverId AND v.status = 1
                     ORDER BY v.createdAt DESC';

            $this->db->query($query);
            $this->db->bind(':driverId', $driverId);
            $results = $this->db->resultSet();

            return $results ?: [];
        } catch (Exception $e) {
            error_log("Error getting driver vehicles: " . $e->getMessage());
            return [];
        }
    }

    public function getDriverCoverPhotos($driverId) {
        try {
            $query = 'SELECT
                        cp.id as photoId,
                        cp.userId,
                        cp.photo_path,
                        cp.photo_order,
                        cp.uploaded_at as created_at
                     FROM cover_photos cp
                     WHERE cp.userId = :driverId AND cp.is_active = 1
                     ORDER BY cp.photo_order ASC, cp.uploaded_at DESC';

            $this->db->query($query);
            $this->db->bind(':driverId', $driverId);
            $results = $this->db->resultSet();

            return $results ?: [];
        } catch (Exception $e) {
            error_log("Error getting driver cover photos: " . $e->getMessage());
            return [];
        }
    }

    public function getDriverStats($driverId) {
        try {
            $query = 'SELECT 
                        COUNT(DISTINCT dat.tripId) as totalTrips,
                        COUNT(DISTINCT dr.reviewId) as totalReviews,
                        AVG(dr.rating) as averageRating
                     FROM users u
                     LEFT JOIN driver_accept_trips dat ON u.id = dat.driverId
                     LEFT JOIN driver_reviews dr ON u.id = dr.driverId
                     WHERE u.id = :driverId';

            $this->db->query($query);
            $this->db->bind(':driverId', $driverId);
            $result = $this->db->single();

            return $result ?: ['totalTrips' => 0, 'totalReviews' => 0, 'averageRating' => 0];
        } catch (Exception $e) {
            error_log("Error getting driver stats: " . $e->getMessage());
            return ['totalTrips' => 0, 'totalReviews' => 0, 'averageRating' => 0];
        }
    }

    public function getDriverReviews($driverId) {
        try {
            // For now, return empty array as reviews table may not exist yet
            // TODO: Implement when reviews table is created
            return [];
        } catch (Exception $e) {
            error_log("Error getting driver reviews: " . $e->getMessage());
            return [];
        }
    }

    private function getAcceptedTripsTableName() {
        $tables = ['driver_accepted_trips', 'driver_accept_trips'];

        foreach ($tables as $table) {
            $this->db->query("SHOW TABLES LIKE :tableName");
            $this->db->bind(':tableName', $table);
            $result = $this->db->single();

            if ($result) {
                return $table;
            }
        }

        return 'driver_accepted_trips';
    }

    private function getTravelSpotItineraryTableName() {
        $tables = ['travel_spot_ininerary', 'travel_spots_itinerary', 'travel_spot_itinerary'];

        foreach ($tables as $table) {
            $this->db->query("SHOW TABLES LIKE :tableName");
            $this->db->bind(':tableName', $table);
            $result = $this->db->single();

            if ($result) {
                return $table;
            }
        }

        return null;
    }

    public function getEarningsSummary($driverId) {
        try {
            $acceptedTable = $this->getAcceptedTripsTableName();

            $pendingQuery = "SELECT COUNT(*) AS pending_count, COALESCE(SUM(totalAmount), 0) AS pending_amount
                             FROM {$acceptedTable}
                             WHERE driverId = :driverId AND paymentStatus = 'pending'";
            $this->db->query($pendingQuery);
            $this->db->bind(':driverId', $driverId);
            $pending = $this->db->single();

            $paidQuery = "SELECT COUNT(*) AS paid_count, COALESCE(SUM(driverCharge), 0) AS paid_amount
                          FROM driver_payments
                          WHERE driverId = :driverId AND pDoneTraveller = 1 AND refunded = 0";
            $this->db->query($paidQuery);
            $this->db->bind(':driverId', $driverId);
            $paid = $this->db->single();

            $refundedQuery = "SELECT COUNT(*) AS refunded_count,
                                     COALESCE(SUM(COALESCE(refundAmount, driverCharge)), 0) AS refunded_amount
                              FROM driver_payments
                              WHERE driverId = :driverId AND refunded = 1";
            $this->db->query($refundedQuery);
            $this->db->bind(':driverId', $driverId);
            $refunded = $this->db->single();

            $pendingCount = (int)($pending->pending_count ?? 0);
            $pendingAmount = (float)($pending->pending_amount ?? 0);
            $paidCount = (int)($paid->paid_count ?? 0);
            $paidAmount = (float)($paid->paid_amount ?? 0);
            $refundedCount = (int)($refunded->refunded_count ?? 0);
            $refundedAmount = (float)($refunded->refunded_amount ?? 0);

            return [
                'pending_count' => $pendingCount,
                'pending_amount' => $pendingAmount,
                'paid_count' => $paidCount,
                'paid_amount' => $paidAmount,
                'refunded_count' => $refundedCount,
                'refunded_amount' => $refundedAmount,
                'total_earned' => $paidAmount + $refundedAmount
            ];
        } catch (Exception $e) {
            error_log("Error getting earnings summary: " . $e->getMessage());
            return [
                'pending_count' => 0,
                'pending_amount' => 0,
                'paid_count' => 0,
                'paid_amount' => 0,
                'refunded_count' => 0,
                'refunded_amount' => 0,
                'total_earned' => 0
            ];
        }
    }

    public function getEarningsByStatus($driverId, $status) {
        try {
            $acceptedTable = $this->getAcceptedTripsTableName();

            if ($status === 'pending') {
                $query = "SELECT
                            dat.tripId,
                            COALESCE(dp.driverCharge, dat.totalAmount) AS driverCharge,
                            dat.totalAmount AS totalTripCharge,
                            dat.createdAt
                          FROM {$acceptedTable} dat
                          LEFT JOIN trip_payments tp ON dat.tripId = tp.tripId
                          LEFT JOIN driver_payments dp ON tp.wholePaymentId = dp.wholePaymentId AND dp.driverId = dat.driverId
                          WHERE dat.driverId = :driverId
                            AND dat.paymentStatus = 'pending'
                          ORDER BY dat.createdAt DESC";

                $this->db->query($query);
                $this->db->bind(':driverId', $driverId);
                return $this->db->resultSet();
            }

            if ($status === 'paid') {
                $query = "SELECT
                            COALESCE(tp.tripId, dat.tripId) AS tripId,
                            dp.driverCharge,
                            dp.pDoneSite,
                            dp.pDateTraveller,
                            dp.pDateSite,
                            dp.createdAt
                          FROM driver_payments dp
                          LEFT JOIN trip_payments tp ON dp.wholePaymentId = tp.wholePaymentId
                          LEFT JOIN {$acceptedTable} dat ON dat.tripId = tp.tripId AND dat.driverId = dp.driverId
                          WHERE dp.driverId = :driverId
                            AND dp.pDoneTraveller = 1
                            AND dp.refunded = 0
                          ORDER BY COALESCE(dp.pDateTraveller, dp.createdAt) DESC";

                $this->db->query($query);
                $this->db->bind(':driverId', $driverId);
                return $this->db->resultSet();
            }

            if ($status === 'refunded') {
                $query = "SELECT
                            COALESCE(tp.tripId, dat.tripId) AS tripId,
                            dp.driverCharge,
                            dp.refundAmount,
                            dp.refundDate,
                            dp.refundReason,
                            dp.pDoneSite,
                            dp.createdAt
                          FROM driver_payments dp
                          LEFT JOIN trip_payments tp ON dp.wholePaymentId = tp.wholePaymentId
                          LEFT JOIN {$acceptedTable} dat ON dat.tripId = tp.tripId AND dat.driverId = dp.driverId
                          WHERE dp.driverId = :driverId
                            AND dp.refunded = 1
                          ORDER BY COALESCE(dp.refundDate, dp.createdAt) DESC";

                $this->db->query($query);
                $this->db->bind(':driverId', $driverId);
                return $this->db->resultSet();
            }

            return [];
        } catch (Exception $e) {
            error_log("Error getting earnings by status ({$status}): " . $e->getMessage());
            return [];
        }
    }

    public function getMonthlyEarnings($driverId) {
        try {
            $acceptedTable = $this->getAcceptedTripsTableName();
            $months = [];

            for ($i = 5; $i >= 0; $i--) {
                $monthKey = date('Y-m', strtotime("-{$i} months"));
                $months[$monthKey] = [
                    'monthKey' => $monthKey,
                    'monthLabel' => date('M Y', strtotime($monthKey . '-01')),
                    'paid' => 0,
                    'pending' => 0,
                    'refunded' => 0
                ];
            }

            $pendingQuery = "SELECT DATE_FORMAT(createdAt, '%Y-%m') AS monthKey,
                                    COALESCE(SUM(totalAmount), 0) AS amount
                             FROM {$acceptedTable}
                             WHERE driverId = :driverId
                               AND paymentStatus = 'pending'
                               AND createdAt >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                             GROUP BY DATE_FORMAT(createdAt, '%Y-%m')";
            $this->db->query($pendingQuery);
            $this->db->bind(':driverId', $driverId);
            $pendingRows = $this->db->resultSet();

            foreach ($pendingRows as $row) {
                if (isset($months[$row->monthKey])) {
                    $months[$row->monthKey]['pending'] = (float)$row->amount;
                }
            }

            $paidQuery = "SELECT DATE_FORMAT(COALESCE(pDateTraveller, createdAt), '%Y-%m') AS monthKey,
                                 COALESCE(SUM(driverCharge), 0) AS amount
                          FROM driver_payments
                          WHERE driverId = :driverId
                            AND pDoneTraveller = 1
                            AND refunded = 0
                            AND COALESCE(pDateTraveller, createdAt) >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                          GROUP BY DATE_FORMAT(COALESCE(pDateTraveller, createdAt), '%Y-%m')";
            $this->db->query($paidQuery);
            $this->db->bind(':driverId', $driverId);
            $paidRows = $this->db->resultSet();

            foreach ($paidRows as $row) {
                if (isset($months[$row->monthKey])) {
                    $months[$row->monthKey]['paid'] = (float)$row->amount;
                }
            }

            $refundedQuery = "SELECT DATE_FORMAT(COALESCE(refundDate, createdAt), '%Y-%m') AS monthKey,
                                     COALESCE(SUM(COALESCE(refundAmount, driverCharge)), 0) AS amount
                              FROM driver_payments
                              WHERE driverId = :driverId
                                AND refunded = 1
                                AND COALESCE(refundDate, createdAt) >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                              GROUP BY DATE_FORMAT(COALESCE(refundDate, createdAt), '%Y-%m')";
            $this->db->query($refundedQuery);
            $this->db->bind(':driverId', $driverId);
            $refundedRows = $this->db->resultSet();

            foreach ($refundedRows as $row) {
                if (isset($months[$row->monthKey])) {
                    $months[$row->monthKey]['refunded'] = (float)$row->amount;
                }
            }

            return array_values($months);
        } catch (Exception $e) {
            error_log("Error getting monthly earnings: " . $e->getMessage());
            return [];
        }
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
