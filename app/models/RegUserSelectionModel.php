<?php
class RegUserSelectionModel {
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function getGuideToASpotId($spotId){
        try {
            error_log("getGuideToASpotId called with spotId: $spotId");
            
            // Get all guides for this spot with their details
            $query = "SELECT 
                        u.id as userId,
                        u.fullname,
                        u.phone,
                        u.email,
                        u.profile_photo,
                        u.dob,
                        pd.bio,
                        pd.languages,
                        pd.averageRating,
                        pd.tlVerified,
                        gl.id as locationId,
                        gl.baseCharge,
                        gl.chargeType,
                        gl.minGroupSize,
                        gl.maxGroupSize,
                        gl.description as guideDescription,
                        gl.isActive,
                        TIMESTAMPDIFF(YEAR, u.dob, CURDATE()) as age
                      FROM guide_locations gl
                      INNER JOIN users u ON gl.guideId = u.id
                      INNER JOIN profile_details pd ON u.id = pd.userId
                      WHERE gl.spotId = :spotId 
                      AND gl.isActive = 1
                      AND u.account_type = 'guide'
                      ORDER BY pd.averageRating DESC, gl.baseCharge ASC";
            
            $this->db->query($query);
            $this->db->bind(':spotId', $spotId);
            $guides = $this->db->resultSet();
            
            error_log("Found " . count($guides) . " guides for spotId: $spotId");
            
            // Structure guides into filter categories
            $allGuides = [];
            $highRatedGuides = [];
            $verifiedGuides = [];
            $budgetFriendlyGuides = [];
            
            foreach ($guides as $guide) {
                $guideData = (object)[
                    'userId' => $guide->userId,
                    'fullname' => $guide->fullname,
                    'phone' => $guide->phone,
                    'email' => $guide->email,
                    'profile_photo' => $guide->profile_photo,
                    'bio' => $guide->bio,
                    'languages' => $guide->languages,
                    'averageRating' => (float)$guide->averageRating,
                    'age' => (int)$guide->age,
                    'tlVerified' => (bool)$guide->tlVerified,
                    'dlVerified' => (bool)$guide->tlVerified, // Using tlVerified as dlVerified
                    'locationId' => $guide->locationId,
                    'baseCharge' => (float)$guide->baseCharge,
                    'chargeType' => $guide->chargeType,
                    'minGroupSize' => (int)$guide->minGroupSize,
                    'maxGroupSize' => (int)$guide->maxGroupSize,
                    'guideDescription' => $guide->guideDescription,
                    'isActive' => (int)$guide->isActive
                ];
                
                $allGuides[] = $guideData;
                
                // Filter into categories
                if ($guide->averageRating >= 4.0) {
                    $highRatedGuides[] = $guideData;
                }
                
                if ($guide->tlVerified) {
                    $verifiedGuides[] = $guideData;
                }
                
                if ($guide->baseCharge <= 30.00) {
                    $budgetFriendlyGuides[] = $guideData;
                }
            }
            
            // Structure the response for the view
            $mainFilters = [
                'all' => [
                    'name' => 'All Guides',
                    'count' => count($allGuides),
                    'accounts' => $allGuides
                ],
                'high_rated' => [
                    'name' => 'Top Rated (4.0+)',
                    'count' => count($highRatedGuides),
                    'accounts' => $highRatedGuides
                ],
                'verified' => [
                    'name' => 'Verified Guides',
                    'count' => count($verifiedGuides),
                    'accounts' => $verifiedGuides
                ],
                'budget_friendly' => [
                    'name' => 'Budget Friendly',
                    'count' => count($budgetFriendlyGuides),
                    'accounts' => $budgetFriendlyGuides
                ]
            ];
            
            return $mainFilters;
            
        } catch (Exception $e) {
            error_log("Error getting guides for spot: " . $e->getMessage());
            return [
                'all' => ['name' => 'All Guides', 'count' => 0, 'accounts' => []]
            ];
        }
    }

    public function filterGuidesBySpot($spotId, $filters) {
        try {
            error_log("filterGuidesBySpot called with spotId: $spotId and filters: " . json_encode($filters));
            
            // Build base query
            $query = "SELECT 
                        u.id as userId,
                        u.fullname,
                        u.phone,
                        u.email,
                        u.profile_photo,
                        u.dob,
                        pd.bio,
                        pd.languages,
                        pd.averageRating,
                        pd.tlVerified,
                        pd.dlVerified,
                        gl.id as locationId,
                        gl.baseCharge,
                        gl.chargeType,
                        gl.minGroupSize,
                        gl.maxGroupSize,
                        gl.description as guideDescription,
                        gl.isActive,
                        TIMESTAMPDIFF(YEAR, u.dob, CURDATE()) as age
                      FROM guide_locations gl
                      INNER JOIN users u ON gl.guideId = u.id
                      INNER JOIN profile_details pd ON u.id = pd.userId
                      WHERE gl.spotId = :spotId 
                      AND gl.isActive = 1
                      AND u.account_type = 'guide'";
            
            $bindings = [':spotId' => $spotId];
            
            // Apply filters
            if (isset($filters['rating']) && $filters['rating'] > 0) {
                $query .= " AND pd.averageRating >= :rating";
                $bindings[':rating'] = (float)$filters['rating'];
            }
            
            if (isset($filters['verified']) && $filters['verified']) {
                $query .= " AND (pd.tlVerified = 1)";
            }
            
            if (isset($filters['available']) && $filters['available']) {
                $query .= " AND gl.isActive = 1";
            }
            
            if (isset($filters['minPrice']) && $filters['minPrice'] > 0) {
                $query .= " AND gl.baseCharge >= :minPrice";
                $bindings[':minPrice'] = (float)$filters['minPrice'];
            }
            
            if (isset($filters['maxPrice']) && $filters['maxPrice'] > 0) {
                $query .= " AND gl.baseCharge <= :maxPrice";
                $bindings[':maxPrice'] = (float)$filters['maxPrice'];
                error_log("Price filter applied: maxPrice = " . $filters['maxPrice']);
            } else {
                error_log("No price filter applied. maxPrice value: " . ($filters['maxPrice'] ?? 'not set'));
            }
            
            if (isset($filters['minAge']) && $filters['minAge'] > 0) {
                $query .= " AND TIMESTAMPDIFF(YEAR, u.dob, CURDATE()) >= :minAge";
                $bindings[':minAge'] = (int)$filters['minAge'];
            }
            
            if (isset($filters['maxAge']) && $filters['maxAge'] > 0) {
                $query .= " AND TIMESTAMPDIFF(YEAR, u.dob, CURDATE()) <= :maxAge";
                $bindings[':maxAge'] = (int)$filters['maxAge'];
            }
            
            if (isset($filters['chargeType']) && !empty($filters['chargeType']) && $filters['chargeType'] !== 'all') {
                $query .= " AND gl.chargeType = :chargeType";
                $bindings[':chargeType'] = $filters['chargeType'];
            }
            
            if (isset($filters['minGroupSize']) && $filters['minGroupSize'] > 0) {
                $query .= " AND gl.minGroupSize <= :minGroupSize";
                $bindings[':minGroupSize'] = (int)$filters['minGroupSize'];
            }
            
            if (isset($filters['maxGroupSize']) && $filters['maxGroupSize'] > 0) {
                $query .= " AND gl.maxGroupSize >= :maxGroupSize";
                $bindings[':maxGroupSize'] = (int)$filters['maxGroupSize'];
            }
            
            // Handle languages filter
            if (isset($filters['languages']) && is_array($filters['languages']) && !empty($filters['languages'])) {
                $languageConditions = [];
                foreach ($filters['languages'] as $index => $language) {
                    $placeholder = ":language_$index";
                    $languageConditions[] = "pd.languages LIKE $placeholder";
                    $bindings[$placeholder] = "%$language%";
                }
                if (!empty($languageConditions)) {
                    $query .= " AND (" . implode(' OR ', $languageConditions) . ")";
                }
            }
            
            $query .= " ORDER BY pd.averageRating DESC, gl.baseCharge ASC";
            
            $this->db->query($query);
            foreach ($bindings as $key => $value) {
                $this->db->bind($key, $value);
            }
            
            $guides = $this->db->resultSet();
            
            // Structure results
            $filteredGuides = [];
            foreach ($guides as $guide) {
                $filteredGuides[] = (object)[
                    'userId' => $guide->userId,
                    'fullname' => $guide->fullname,
                    'phone' => $guide->phone,
                    'email' => $guide->email,
                    'profile_photo' => $guide->profile_photo,
                    'bio' => $guide->bio,
                    'languages' => $guide->languages,
                    'averageRating' => (float)$guide->averageRating,
                    'age' => (int)$guide->age,
                    'tlVerified' => (bool)$guide->tlVerified,
                    'dlVerified' => (bool)$guide->dlVerified,
                    'locationId' => $guide->locationId,
                    'baseCharge' => (float)$guide->baseCharge,
                    'chargeType' => $guide->chargeType,
                    'minGroupSize' => (int)$guide->minGroupSize,
                    'maxGroupSize' => (int)$guide->maxGroupSize,
                    'guideDescription' => $guide->guideDescription,
                    'isActive' => (int)$guide->isActive
                ];
            }
            
            return $filteredGuides;
            
        } catch (Exception $e) {
            error_log("Error filtering guides by spot: " . $e->getMessage());
            throw $e;
        }
    }

    // ============================
    // DRIVER SELECTION METHODS
    // ============================

    public function getDriversForTrip($tripId) {
        try {
            error_log("getDriversForTrip called with tripId: $tripId");
            
            // Get trip details
            $tripQuery = "SELECT numberOfPeople, startDate, endDate 
                         FROM created_trips 
                         WHERE tripId = :tripId";
            
            $this->db->query($tripQuery);
            $this->db->bind(':tripId', $tripId);
            $trip = $this->db->single();
            
            if (!$trip) {
                error_log("Trip not found: $tripId");
                return [
                    'all' => ['name' => 'All Drivers', 'count' => 0, 'accounts' => []]
                ];
            }
            
            error_log("Trip details - People: {$trip->numberOfPeople}, Start: {$trip->startDate}, End: {$trip->endDate}");
            
            // Get all drivers with their vehicles and pricing who:
            // 1. Have vehicle capacity >= numberOfPeople
            // 2. Are not unavailable during trip dates
            // 3. Vehicle is approved and active
            $query = "SELECT DISTINCT
                        u.id as userId,
                        u.fullname,
                        u.phone,
                        u.email,
                        u.profile_photo,
                        u.dob,
                        u.verified,
                        v.vehicleId,
                        v.make,
                        v.model,
                        v.year,
                        v.color,
                        v.seatingCapacity,
                        v.childSeats,
                        v.licensePlate,
                        v.frontViewPhoto,
                        v.description as vehicleDescription,
                        vp.vehicleChargePerKm,
                        vp.driverChargePerKm,
                        vp.vehicleChargePerDay,
                        vp.driverChargePerDay,
                        vp.minimumKm,
                        vp.minimumDays,
                        TIMESTAMPDIFF(YEAR, u.dob, CURDATE()) as age,
                        pd.bio,
                        pd.languages,
                        pd.averageRating,
                        pd.dlVerified
                      FROM users u
                      INNER JOIN vehicles v ON u.id = v.driverId
                      LEFT JOIN vehicle_pricing vp ON v.vehicleId = vp.vehicleId
                      LEFT JOIN profile_details pd ON u.id = pd.userId
                      WHERE u.account_type = 'driver'
                      AND v.isApproved = 1
                      AND v.status = 1
                      AND v.availability = 1
                      AND v.seatingCapacity >= :numberOfPeople
                      AND u.id NOT IN (
                          SELECT DISTINCT driverId 
                          FROM driver_unavailable_dates 
                          WHERE unavailableDate BETWEEN :startDate AND :endDate
                      )
                      ORDER BY pd.averageRating DESC, vp.vehicleChargePerDay ASC";
            
            $this->db->query($query);
            $this->db->bind(':numberOfPeople', $trip->numberOfPeople);
            $this->db->bind(':startDate', $trip->startDate);
            $this->db->bind(':endDate', $trip->endDate);
            $drivers = $this->db->resultSet();
            
            error_log("Found " . count($drivers) . " available drivers for tripId: $tripId");
            
            // Structure drivers into filter categories
            $allDrivers = [];
            $highRatedDrivers = [];
            $verifiedDrivers = [];
            $budgetFriendlyDrivers = [];
            
            foreach ($drivers as $driver) {
                $totalChargePerDay = (float)$driver->vehicleChargePerDay + (float)$driver->driverChargePerDay;
                
                $driverData = (object)[
                    'userId' => $driver->userId,
                    'fullname' => $driver->fullname,
                    'phone' => $driver->phone,
                    'email' => $driver->email,
                    'profilePhoto' => $driver->profile_photo,
                    'bio' => $driver->bio,
                    'languages' => $driver->languages,
                    'averageRating' => (float)($driver->averageRating ?? 0),
                    'age' => (int)$driver->age,
                    'dlVerified' => (bool)($driver->dlVerified ?? false),
                    'verified' => (bool)$driver->verified,
                    'vehicleId' => $driver->vehicleId,
                    'make' => $driver->make,
                    'model' => $driver->model,
                    'year' => $driver->year,
                    'color' => $driver->color,
                    'seatingCapacity' => (int)$driver->seatingCapacity,
                    'childSeats' => (int)$driver->childSeats,
                    'licensePlate' => $driver->licensePlate,
                    'vehiclePhoto' => $driver->frontViewPhoto,
                    'vehicleDescription' => $driver->vehicleDescription,
                    'vehicleChargePerKm' => (float)($driver->vehicleChargePerKm ?? 0),
                    'driverChargePerKm' => (float)($driver->driverChargePerKm ?? 0),
                    'vehicleChargePerDay' => (float)($driver->vehicleChargePerDay ?? 0),
                    'driverChargePerDay' => (float)($driver->driverChargePerDay ?? 0),
                    'totalChargePerKm' => (float)($driver->vehicleChargePerKm ?? 0) + (float)($driver->driverChargePerKm ?? 0),
                    'totalChargePerDay' => $totalChargePerDay,
                    'minimumKm' => (float)($driver->minimumKm ?? 0),
                    'minimumDays' => (float)($driver->minimumDays ?? 1)
                ];
                
                $allDrivers[] = $driverData;
                
                // Filter into categories
                if ($driver->averageRating >= 4.0) {
                    $highRatedDrivers[] = $driverData;
                }
                
                if ($driver->dlVerified || $driver->verified) {
                    $verifiedDrivers[] = $driverData;
                }
                
                if ($totalChargePerDay <= 100.00) {
                    $budgetFriendlyDrivers[] = $driverData;
                }
            }
            
            // Structure the response
            $mainFilters = [
                'all' => [
                    'name' => 'All Drivers',
                    'count' => count($allDrivers),
                    'accounts' => $allDrivers
                ],
                'high_rated' => [
                    'name' => 'Top Rated (4.0+)',
                    'count' => count($highRatedDrivers),
                    'accounts' => $highRatedDrivers
                ],
                'verified' => [
                    'name' => 'Verified Drivers',
                    'count' => count($verifiedDrivers),
                    'accounts' => $verifiedDrivers
                ],
                'budget_friendly' => [
                    'name' => 'Budget Friendly',
                    'count' => count($budgetFriendlyDrivers),
                    'accounts' => $budgetFriendlyDrivers
                ]
            ];
            
            return $mainFilters;
            
        } catch (Exception $e) {
            error_log("Error getting drivers for trip: " . $e->getMessage());
            return [
                'all' => ['name' => 'All Drivers', 'count' => 0, 'accounts' => []]
            ];
        }
    }

    public function filterDriversByTrip($tripId, $filters) {
        try {
            error_log("filterDriversByTrip called with tripId: $tripId and filters: " . json_encode($filters));
            
            // Get trip details
            $tripQuery = "SELECT numberOfPeople, startDate, endDate 
                         FROM created_trips 
                         WHERE tripId = :tripId";
            
            $this->db->query($tripQuery);
            $this->db->bind(':tripId', $tripId);
            $trip = $this->db->single();
            
            if (!$trip) {
                error_log("Trip not found: $tripId");
                return [];
            }
            
            // Build base query
            $query = "SELECT DISTINCT
                        u.id as userId,
                        u.fullname,
                        u.phone,
                        u.email,
                        u.profile_photo,
                        u.dob,
                        u.verified,
                        v.vehicleId,
                        v.make,
                        v.model,
                        v.year,
                        v.color,
                        v.seatingCapacity,
                        v.childSeats,
                        v.licensePlate,
                        v.frontViewPhoto,
                        v.description as vehicleDescription,
                        vp.vehicleChargePerKm,
                        vp.driverChargePerKm,
                        vp.vehicleChargePerDay,
                        vp.driverChargePerDay,
                        vp.minimumKm,
                        vp.minimumDays,
                        TIMESTAMPDIFF(YEAR, u.dob, CURDATE()) as age,
                        pd.bio,
                        pd.languages,
                        pd.averageRating,
                        pd.dlVerified
                      FROM users u
                      INNER JOIN vehicles v ON u.id = v.driverId
                      LEFT JOIN vehicle_pricing vp ON v.vehicleId = vp.vehicleId
                      LEFT JOIN profile_details pd ON u.id = pd.userId
                      WHERE u.account_type = 'driver'
                      AND v.isApproved = 1
                      AND v.status = 1
                      AND v.availability = 1
                      AND v.seatingCapacity >= :numberOfPeople
                      AND u.id NOT IN (
                          SELECT DISTINCT driverId 
                          FROM driver_unavailable_dates 
                          WHERE unavailableDate BETWEEN :startDate AND :endDate
                      )";
            
            $bindings = [
                ':numberOfPeople' => $trip->numberOfPeople,
                ':startDate' => $trip->startDate,
                ':endDate' => $trip->endDate
            ];
            
            // Apply filters
            if (isset($filters['rating']) && $filters['rating'] > 0) {
                $query .= " AND pd.averageRating >= :rating";
                $bindings[':rating'] = (float)$filters['rating'];
            }
            
            if (isset($filters['verified']) && $filters['verified']) {
                $query .= " AND (pd.dlVerified = 1 OR u.verified = 1)";
            }
            
            if (isset($filters['minPrice']) && $filters['minPrice'] > 0) {
                $query .= " AND (vp.vehicleChargePerDay + vp.driverChargePerDay) >= :minPrice";
                $bindings[':minPrice'] = (float)$filters['minPrice'];
            }
            
            if (isset($filters['maxPrice']) && $filters['maxPrice'] > 0) {
                $query .= " AND (vp.vehicleChargePerDay + vp.driverChargePerDay) <= :maxPrice";
                $bindings[':maxPrice'] = (float)$filters['maxPrice'];
            }
            
            if (isset($filters['minAge']) && $filters['minAge'] > 0) {
                $query .= " AND TIMESTAMPDIFF(YEAR, u.dob, CURDATE()) >= :minAge";
                $bindings[':minAge'] = (int)$filters['minAge'];
            }
            
            if (isset($filters['maxAge']) && $filters['maxAge'] > 0) {
                $query .= " AND TIMESTAMPDIFF(YEAR, u.dob, CURDATE()) <= :maxAge";
                $bindings[':maxAge'] = (int)$filters['maxAge'];
            }
            
            if (isset($filters['minSeatingCapacity']) && $filters['minSeatingCapacity'] > 0) {
                $query .= " AND v.seatingCapacity >= :minSeatingCapacity";
                $bindings[':minSeatingCapacity'] = (int)$filters['minSeatingCapacity'];
            }
            
            if (isset($filters['maxSeatingCapacity']) && $filters['maxSeatingCapacity'] > 0) {
                $query .= " AND v.seatingCapacity <= :maxSeatingCapacity";
                $bindings[':maxSeatingCapacity'] = (int)$filters['maxSeatingCapacity'];
            }
            
            if (isset($filters['childSeats']) && $filters['childSeats'] > 0) {
                $query .= " AND v.childSeats >= :childSeats";
                $bindings[':childSeats'] = (int)$filters['childSeats'];
            }
            
            if (isset($filters['vehicleType']) && !empty($filters['vehicleType']) && $filters['vehicleType'] !== 'all') {
                $query .= " AND v.make LIKE :vehicleType";
                $bindings[':vehicleType'] = "%" . $filters['vehicleType'] . "%";
            }
            
            // Handle languages filter
            if (isset($filters['languages']) && is_array($filters['languages']) && !empty($filters['languages'])) {
                $languageConditions = [];
                foreach ($filters['languages'] as $index => $language) {
                    $placeholder = ":language_$index";
                    $languageConditions[] = "pd.languages LIKE $placeholder";
                    $bindings[$placeholder] = "%$language%";
                }
                if (!empty($languageConditions)) {
                    $query .= " AND (" . implode(' OR ', $languageConditions) . ")";
                }
            }
            
            $query .= " ORDER BY pd.averageRating DESC, (vp.vehicleChargePerDay + vp.driverChargePerDay) ASC";
            
            $this->db->query($query);
            foreach ($bindings as $key => $value) {
                $this->db->bind($key, $value);
            }
            
            $drivers = $this->db->resultSet();
            
            // Structure results
            $filteredDrivers = [];
            foreach ($drivers as $driver) {
                $totalChargePerDay = (float)($driver->vehicleChargePerDay ?? 0) + (float)($driver->driverChargePerDay ?? 0);
                
                $filteredDrivers[] = (object)[
                    'userId' => $driver->userId,
                    'fullname' => $driver->fullname,
                    'phone' => $driver->phone,
                    'email' => $driver->email,
                    'profile_photo' => $driver->profile_photo,
                    'bio' => $driver->bio,
                    'languages' => $driver->languages,
                    'averageRating' => (float)($driver->averageRating ?? 0),
                    'age' => (int)$driver->age,
                    'dlVerified' => (bool)($driver->dlVerified ?? false),
                    'verified' => (bool)$driver->verified,
                    'vehicleId' => $driver->vehicleId,
                    'make' => $driver->make,
                    'model' => $driver->model,
                    'year' => $driver->year,
                    'color' => $driver->color,
                    'seatingCapacity' => (int)$driver->seatingCapacity,
                    'childSeats' => (int)$driver->childSeats,
                    'licensePlate' => $driver->licensePlate,
                    'vehiclePhoto' => $driver->frontViewPhoto,
                    'vehicleDescription' => $driver->vehicleDescription,
                    'vehicleChargePerKm' => (float)($driver->vehicleChargePerKm ?? 0),
                    'driverChargePerKm' => (float)($driver->driverChargePerKm ?? 0),
                    'vehicleChargePerDay' => (float)($driver->vehicleChargePerDay ?? 0),
                    'driverChargePerDay' => (float)($driver->driverChargePerDay ?? 0),
                    'totalChargePerKm' => (float)($driver->vehicleChargePerKm ?? 0) + (float)($driver->driverChargePerKm ?? 0),
                    'totalChargePerDay' => $totalChargePerDay,
                    'minimumKm' => (float)($driver->minimumKm ?? 0),
                    'minimumDays' => (float)($driver->minimumDays ?? 1)
                ];
            }
            
            return $filteredDrivers;
            
        } catch (Exception $e) {
            error_log("Error filtering drivers by trip: " . $e->getMessage());
            throw $e;
        }
    }
    
}



