<?php
    
    class ModeratorModel{

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

        public function addMainFilterName($filterData){

            $query = 'INSERT INTO travelspots_mainfilters ( mainFilterName, moderatorId ) VALUES ( :mainFilterName, :moderatorId)';
        
            $this->db->query($query);
            $this->db->bind(':mainFilterName',$filterData['mainFilterName']);
            $this->db->bind(':moderatorId',$filterData['moderatorId']);

            return $this->db->execute();
        }

        public function updateMainFilterName($updatingData){
            $query = "UPDATE travelspots_mainfilters SET 
                    mainFilterName = :mainFilterName,
                    moderatorId = :moderatorId,
                    updatedAt = CURRENT_TIMESTAMP
                    WHERE mainFilterId = :mainFilterId";
        
            $this->db->query($query);
            $this->db->bind(':mainFilterName', $updatingData['mainFilterName']);
            $this->db->bind(':moderatorId', $updatingData['moderatorId']);
            $this->db->bind(':mainFilterId', $updatingData['mainFilterId']);

            return $this->db->execute();
        }

        public function deleteMainFilterName($mainFilterId){
            $this->db->query('DELETE FROM travelspots_mainfilters WHERE mainFilterId = :mainFilterId');
            $this->db->bind(':mainFilterId', $mainFilterId);
            return $this->db->execute();
        }

    
        public function addSubFilterName($filterData){

            $query = 'INSERT INTO travelspots_subfilters (subFilterName,mainFilterId, moderatorId ) VALUES ( :subFilterName,:mainFilterId, :moderatorId)';
            $this->db->query($query);
            $this->db->bind(':subFilterName',$filterData['subFilterName']);
            $this->db->bind(':mainFilterId',$filterData['mainFilterId']);
            $this->db->bind(':moderatorId',$filterData['moderatorId']);

            return $this->db->execute();
        }

        public function updateSubFilterName($updatingData){
            $query = "UPDATE travelspots_subfilters SET 
                    subFilterName = :subFilterName,
                    moderatorId = :moderatorId,
                    updatedAt = CURRENT_TIMESTAMP
                    WHERE subFilterId = :subFilterId";
        
            $this->db->query($query);
            $this->db->bind(':subFilterName', $updatingData['subFilterName']);
            $this->db->bind(':moderatorId', $updatingData['moderatorId']);
            $this->db->bind(':subFilterId', $updatingData['subFilterId']);

            return $this->db->execute();
        }

        public function deleteSubFilterName($subFilterId){
            $this->db->query('DELETE FROM travelspots_subfilters WHERE subFilterId = :subFilterId');
            $this->db->bind(':subFilterId', $subFilterId);
            return $this->db->execute();
        }

        
        public function loadAllFilters(){

            $query = 'SELECT mainFilterId, mainFilterName, subFilterId , subFilterName FROM view_mainfilters_with_subfilters';
            $this->db->query($query);
            return $this->db->resultSet();
        }
        
        public function searchTravelSpotByName($name){

            $query = "SELECT spotId, spotName 
                      FROM travel_spots 
                      WHERE spotName LIKE CONCAT('%', :name, '%')
                      ORDER BY 
                        CASE
                            WHEN spotName LIKE CONCAT(:name, '%') THEN 1
                            WHEN spotName LIKE CONCAT('%', :name, '%') THEN 2
                            ELSE 3
                        END,
                        spotName";

            $this->db->query($query);
            $this->db->bind(':name', $name);
            return $this->db->resultSet();
        }

        public function addTravelSpotsTable($travelSpotData){

            $query = '  INSERT INTO travel_spots (spotName,overview, province,district,bestTimeFrom,bestTimeTo,visitingDurationMax,ticketPriceLocal,ticketPriceForeigner,openingHours,ticketDetails,parkingDetails,accessibility,facilities,travelerTips ) 
                        VALUES 
                        ( :spotName,:overview, :province, :district, :bestTimeFrom, :bestTimeTo, :visitingDurationMax, :ticketPriceLocal, :ticketPriceForeigner, :openingHours, :ticketDetails, :parkingDetails, :accessibility, :facilities, :travelerTips)';

            $this->db->query($query);
            $this->db->bind(':spotName',$travelSpotData['spotName']);
            $this->db->bind(':overview',$travelSpotData['overview']);
            $this->db->bind(':province',$travelSpotData['province']);
            $this->db->bind(':district',$travelSpotData['district']);
            $this->db->bind(':bestTimeFrom',$travelSpotData['bestTimeFrom']);
            $this->db->bind(':bestTimeTo',$travelSpotData['bestTimeTo']);
            $this->db->bind(':visitingDurationMax',$travelSpotData['visitingDurationMax']);
            $this->db->bind(':ticketPriceLocal',$travelSpotData['ticketPriceLocal']);
            $this->db->bind(':ticketPriceForeigner',$travelSpotData['ticketPriceForeigner']);
            $this->db->bind(':openingHours',$travelSpotData['openingHours']);
            $this->db->bind(':ticketDetails',$travelSpotData['ticketDetails']);
            $this->db->bind(':parkingDetails',$travelSpotData['parkingDetails']);
            $this->db->bind(':accessibility',$travelSpotData['accessibility']);
            $this->db->bind(':facilities',$travelSpotData['facilities']);
            $this->db->bind(':travelerTips',$travelSpotData['travelerTips']);
            $this->db->execute();
            return $this->db->lastInsertId();
        }

        public function addTravelSpotSubFilters($spotId, $subFilterId){
            $query = 'INSERT INTO travel_spots_subfilters ( spotId, subFilterId ) VALUES ( :spotId, :subFilterId )';
            $this->db->query($query);
            $this->db->bind(':spotId', $spotId);
            $this->db->bind(':subFilterId', $subFilterId);
            return $this->db->execute();
        }

        public function addTravelSpotNearbySpots($spotId, $nearbySpotId){
            $query = 'INSERT INTO travel_spots_nearbyspots ( sourceSpotId, nearbySpotId ) VALUES ( :spotId, :nearbySpotId )';
            $this->db->query($query);
            $this->db->bind(':spotId', $spotId);
            $this->db->bind(':nearbySpotId', $nearbySpotId);
            return $this->db->execute();
        }

        public function addTravelSpotItinerary($spotId, $locationName, $latitude, $longitude){
            $query = 'INSERT INTO travel_spots_itinerary ( spotId, pointName, latitude, longitude ) VALUES ( :spotId, :pointName, :latitude, :longitude )';
            $this->db->query($query);
            $this->db->bind(':spotId', $spotId);
            $this->db->bind(':pointName', $locationName);
            $this->db->bind(':latitude', $latitude);
            $this->db->bind(':longitude', $longitude);
            return $this->db->execute();
        }

        public function addTravelSpotPhotos($spotId, $photoPath){
            $query = 'INSERT INTO travel_spots_photos ( spotId, photoPath ) VALUES ( :spotId, :photoPath )';
            $this->db->query($query);
            $this->db->bind(':spotId', $spotId);
            $this->db->bind(':photoPath', $photoPath);
            return $this->db->execute();
        }

        public function addContributorData($contributionData){
            $query = 'INSERT INTO travel_spots_contributions ( spotId, moderatorId ) VALUES ( :spotId, :moderatorId )';
            $this->db->query($query);
            $this->db->bind(':spotId', $contributionData['spotId']);
            $this->db->bind(':moderatorId', $contributionData['moderatorId']);
            return $this->db->execute();
        }

        public function loadTravelSpotCardData(){

            error_log("Loading Travel Spot Card Data");
            $query = 'SELECT spotId, spotName, overview , averageRating, totalReviews, mainFilterId, mainFilterName, subFilterId, subFilterName, photoPath FROM travel_spot_card_data WHERE spotId IS NOT NULL';
            $this->db->query($query);
            return $this->db->resultSet();
        }

        public function loadTravelSpotData($travelSpotId){

            error_log("Loading Travel Spot Data for ID: " . $travelSpotId);

            $mainDetailsQuery = '  SELECT spotId, spotName, overview, province, district, bestTimeFrom, bestTimeTo, visitingDurationMax, ticketPriceLocal, 
                        ticketPriceForeigner, openingHours,ticketDetails, parkingDetails, accessibility, facilities, travelerTips, averageRating, totalReviews
                        FROM travel_spots 
                        WHERE spotId = :travelSpotId';
            
            $this->db->query($mainDetailsQuery);
            $this->db->bind(':travelSpotId', $travelSpotId);
            $mainDetails = $this->db->single();

            error_log('travel Spot Data '. print_r($mainDetails, true));

            $filtersQuery = ' SELECT 
                            tmf.mainFilterId,
                            tmf.mainFilterName,
                            tsf.subFilterId,
                            tsf.subFilterName
                            FROM travel_spots_subfilters tss
                            LEFT JOIN travelspots_subfilters tsf ON tss.subFilterId = tsf.subFilterId
                            LEFT JOIN travelspots_mainfilters tmf ON tsf.mainFilterId = tmf.mainFilterId
                            WHERE tss.spotId = :travelSpotId
                            ORDER BY tmf.mainFilterName, tsf.subFilterName';
            $this->db->query($filtersQuery);
            $this->db->bind(':travelSpotId', $travelSpotId);
            $filters = $this->db->resultSet();
            error_log('Filters '. print_r($filters, true));

            $photoPaths = 'SELECT photoId, photoPath FROM travel_spots_photos WHERE spotId = :travelSpotId ORDER BY photoId';
            $this->db->query($photoPaths);  
            $this->db->bind(':travelSpotId', $travelSpotId);
            $photos = $this->db->resultSet();

            error_log('Photos '. print_r($photos, true));


            $itineraryQuery = ' SELECT pointId, pointName, latitude, longitude 
                                FROM travel_spots_itinerary 
                                WHERE spotId = :travelSpotId 
                                ORDER BY pointId';
            $this->db->query($itineraryQuery);
            $this->db->bind(':travelSpotId', $travelSpotId);
            $itinerary = $this->db->resultSet();
            error_log('Itinerary data '. print_r($itinerary, true));

            $nearbySpotsQuery = 'SELECT 
                                    ts.spotId,
                                    ts.spotName
                                FROM travel_spots_nearbyspots tsn
                                INNER JOIN travel_spots ts ON tsn.nearbySpotId = ts.spotId
                                WHERE tsn.sourceSpotId = :travelSpotId
                                ORDER BY ts.spotName';

            $this->db->query($nearbySpotsQuery);
            $this->db->bind(':travelSpotId', $travelSpotId);
            $nearbySpots = $this->db->resultSet();
            error_log('nearby Spots '. print_r($nearbySpots, true));

            $structuredFilters = [];

            foreach($filters as $filter){
                $mainFilterId = $filter->mainFilterId;
                $mainFilterName = $filter->mainFilterName;
                $subFilterId = $filter->subFilterId;
                $subFilterName = $filter->subFilterName;

                if(!isset($structuredFilters[$mainFilterId])){
                    $structuredFilters[$mainFilterId] = [
                        'mainFilterName' => $mainFilterName,
                        'subFilters' => []
                    ];
                }

                array_push($structuredFilters[$mainFilterId]['subFilters'], [
                    'subFilterId' => $subFilterId,
                    'subFilterName' => $subFilterName
                ]);
            }
            error_log('stuctured filters '. print_r($structuredFilters, true));

            $travelSpotData = [
                'mainDetails' => $mainDetails,
                'filters' => $structuredFilters,
                'photos' => $photos,
                'itinerary' => $itinerary,
                'nearbySpots' => $nearbySpots
            ];
            error_log("Loaded Travel Spot Data: " . print_r($travelSpotData, true));
            return $travelSpotData;       
        }

        public function updateTravelSpotsTable($travelSpotData){

            $query = "  UPDATE travel_spots SET 
                        spotName = :spotName,
                        overview = :overview,
                        province = :province,
                        district = :district,
                        bestTimeFrom = :bestTimeFrom,
                        bestTimeTo = :bestTimeTo,
                        visitingDurationMax = :visitingDurationMax,
                        ticketPriceLocal = :ticketPriceLocal,
                        ticketPriceForeigner = :ticketPriceForeigner,
                        openingHours = :openingHours,
                        ticketDetails = :ticketDetails,
                        parkingDetails = :parkingDetails,
                        accessibility = :accessibility,
                        facilities = :facilities,
                        travelerTips = :travelerTips
                        WHERE spotId = :spotId";

            $this->db->query($query);
            $this->db->bind(':spotId',$travelSpotData['spotId']);
            $this->db->bind(':spotName',$travelSpotData['spotName']);
            $this->db->bind(':overview',$travelSpotData['overview']);
            $this->db->bind(':province',$travelSpotData['province']);
            $this->db->bind(':district',$travelSpotData['district']);
            $this->db->bind(':bestTimeFrom',$travelSpotData['bestTimeFrom']);
            $this->db->bind(':bestTimeTo',$travelSpotData['bestTimeTo']);
            $this->db->bind(':visitingDurationMax',$travelSpotData['visitingDurationMax']);
            $this->db->bind(':ticketPriceLocal',$travelSpotData['ticketPriceLocal']);
            $this->db->bind(':ticketPriceForeigner',$travelSpotData['ticketPriceForeigner']);
            $this->db->bind(':openingHours',$travelSpotData['openingHours']);
            $this->db->bind(':ticketDetails',$travelSpotData['ticketDetails']);
            $this->db->bind(':parkingDetails',$travelSpotData['parkingDetails']);
            $this->db->bind(':accessibility',$travelSpotData['accessibility']);
            $this->db->bind(':facilities',$travelSpotData['facilities']);
            $this->db->bind(':travelerTips',$travelSpotData['travelerTips']);
            $this->db->execute();
            return $travelSpotData['spotId'];
        }

        public function deleteTravelSpotPhotos($spotId){
            $this->db->query('DELETE FROM travel_spots_photos WHERE spotId = :spotId');
            $this->db->bind(':spotId', $spotId);
            return $this->db->execute();
        }

        public function deleteTravelSpotSubFilters($spotId){
            $this->db->query('DELETE FROM travel_spots_subfilters WHERE spotId = :spotId');
            $this->db->bind(':spotId', $spotId);
            return $this->db->execute();
        }

        public function deleteTravelSpotNearbySpots($spotId){
            $this->db->query('DELETE FROM travel_spots_nearbyspots WHERE sourceSpotId = :spotId');
            $this->db->bind(':spotId', $spotId);
            return $this->db->execute();
        }

        public function deleteTravelSpotItinerary($spotId){
            $this->db->query('DELETE FROM travel_spots_itinerary WHERE spotId = :spotId');
            $this->db->bind(':spotId', $spotId);
            return $this->db->execute();
        }   

        //delete travel spot main details
        public function eraseTravelSpot($spotId){
            $this->db->query('DELETE FROM travel_spots WHERE spotId = :spotId');
            $this->db->bind(':spotId', $spotId);
            return $this->db->execute();
        }

        public function updateContributorData($contributionData){
            $query = "UPDATE travel_spots_contributions SET 
                    moderatorId = :moderatorId
                    WHERE spotId = :spotId";
        
            $this->db->query($query);
            $this->db->bind(':moderatorId', $contributionData['moderatorId']);
            $this->db->bind(':spotId', $contributionData['spotId']);

            return $this->db->execute();
        }

        // Account Verification Methods
        public function getAccountsByStatus($status) {
            try {
                $query = "SELECT
                            userId AS id,
                            fullName AS name,
                            email,
                            CASE
                                WHEN accountType = 'guide' AND JSON_UNQUOTE(JSON_EXTRACT(guideTouristData, '$.nic_passport')) IS NOT NULL
                                THEN JSON_UNQUOTE(JSON_EXTRACT(guideTouristData, '$.nic_passport'))
                                WHEN accountType = 'driver' AND JSON_UNQUOTE(JSON_EXTRACT(driverData, '$.nic_passport')) IS NOT NULL
                                THEN JSON_UNQUOTE(JSON_EXTRACT(driverData, '$.nic_passport'))
                                ELSE 'Not provided'
                            END AS nic,
                            accountType AS account_type,
                            profilePhoto AS profile_photo,
                            accountCreatedAt AS created_at,
                            verificationStatus AS status,
                            verificationCreatedAt AS verification_created_at
                          FROM user_verifications_view
                          WHERE verificationStatus = :status
                          ORDER BY verificationCreatedAt DESC";

                $this->db->query($query);
                $this->db->bind(':status', $status);
                $results = $this->db->resultSet();
                error_log("getAccountsByStatus: status=$status, found " . count($results) . " accounts");
                return $results;

            } catch (PDOException $e) {
                error_log("Database error in getAccountsByStatus: " . $e->getMessage());
                return [];
            }
        }

        public function getUserDetailsForVerification($userId) {
            try {
                $query = "SELECT
                            userId AS id,
                            fullName AS name,
                            email,
                            phone,
                            secondaryPhone AS secondary_phone,
                            address,
                            accountType AS account_type,
                            profilePhoto AS profile_photo,
                            verified,
                            accountCreatedAt AS created_at,
                            driverData AS driver_data,
                            guideTouristData AS guide_tourist_data,
                            verificationStatus AS status,
                            rejectionReason,
                            reviewedAt,
                            expiryDate
                          FROM user_verifications_view
                          WHERE userId = :userId";

                $this->db->query($query);
                $this->db->bind(':userId', $userId);
                $user = $this->db->single();

                if ($user) {
                    // Process driver/guide data
                    if ($user->account_type === 'driver' && $user->driver_data) {
                        $driverData = json_decode($user->driver_data, true);
                        $user->license_number = $driverData['license_number'] ?? null;
                        $user->license_expire_date = $driverData['license_expire_date'] ?? null;
                        $user->nic_passport = $driverData['nic_passport'] ?? null;

                        // Extract NIC photos
                        $user->nic_front = $driverData['id_front'] ?? null;
                        $user->nic_back = $driverData['id_back'] ?? null;

                        // Extract license photos
                        $user->license_front = $driverData['license_front'] ?? null;
                        $user->license_back = $driverData['license_back'] ?? null;
                    }

                    if ($user->account_type === 'guide' && $user->guide_tourist_data) {
                        $guideData = json_decode($user->guide_tourist_data, true);
                        $user->nic_passport = $guideData['nic_passport'] ?? null;

                        // Extract NIC photos
                        $user->nic_front = $guideData['nic_front'] ?? null;
                        $user->nic_back = $guideData['nic_back'] ?? null;
                    }

                    // Remove raw JSON data
                    unset($user->driver_data);
                    unset($user->guide_tourist_data);
                }

                return $user;

            } catch (PDOException $e) {
                error_log("Database error in getUserDetailsForVerification: " . $e->getMessage());
                return null;
            }
        }

        public function verifyAccount($userId, $moderatorId) {
            try {
                // Update the account_verifications table
                $query = "UPDATE account_verifications
                          SET status = 'approved',
                              reviewedBy = :moderatorId,
                              reviewedAt = NOW(),
                              updatedAt = NOW()
                          WHERE userId = :userId AND status = 'pending'";

                $this->db->query($query);
                $this->db->bind(':userId', $userId);
                $this->db->bind(':moderatorId', $moderatorId);

                $result = $this->db->execute();

                if ($result) {
                    // Also update the users table verified status
                    $userQuery = "UPDATE users SET verified = 1, updated_at = NOW() WHERE id = :userId";
                    $this->db->query($userQuery);
                    $this->db->bind(':userId', $userId);
                    $this->db->execute();
                }

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in verifyAccount: " . $e->getMessage());
                return false;
            }
        }

        public function rejectAccount($userId, $moderatorId, $reason) {
            try {
                // Update the account_verifications table
                $query = "UPDATE account_verifications
                          SET status = 'rejected',
                              reviewedBy = :moderatorId,
                              reviewedAt = NOW(),
                              rejectionReason = :reason,
                              updatedAt = NOW()
                          WHERE userId = :userId AND status = 'pending'";

                $this->db->query($query);
                $this->db->bind(':userId', $userId);
                $this->db->bind(':moderatorId', $moderatorId);
                $this->db->bind(':reason', $reason);

                $result = $this->db->execute();

                if ($result) {
                    // Update the users table verified status to 0
                    $userQuery = "UPDATE users SET verified = 0, updated_at = NOW() WHERE id = :userId";
                    $this->db->query($userQuery);
                    $this->db->bind(':userId', $userId);
                    $this->db->execute();
                }

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in rejectAccount: " . $e->getMessage());
                return false;
            }
        }

        public function revokeVerification($userId, $moderatorId) {
            try {
                // Update the account_verifications table back to pending
                $query = "UPDATE account_verifications
                          SET status = 'pending',
                              reviewedBy = NULL,
                              reviewedAt = NULL,
                              rejectionReason = NULL,
                              updatedAt = NOW()
                          WHERE userId = :userId AND status = 'approved'";

                $this->db->query($query);
                $this->db->bind(':userId', $userId);

                $result = $this->db->execute();

                if ($result) {
                    // Update the users table verified status to 0
                    $userQuery = "UPDATE users SET verified = 0, updated_at = NOW() WHERE id = :userId";
                    $this->db->query($userQuery);
                    $this->db->bind(':userId', $userId);
                    $this->db->execute();
                }

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in revokeVerification: " . $e->getMessage());
                return false;
            }
        }

        public function revokeRejection($userId, $moderatorId) {
            try {
                // Update the account_verifications table back to pending
                $query = "UPDATE account_verifications
                          SET status = 'pending',
                              reviewedBy = NULL,
                              reviewedAt = NULL,
                              rejectionReason = NULL,
                              updatedAt = NOW()
                          WHERE userId = :userId AND status = 'rejected'";

                $this->db->query($query);
                $this->db->bind(':userId', $userId);

                $result = $this->db->execute();

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in revokeRejection: " . $e->getMessage());
                return false;
            }
        }

        // Tourist License Verification Methods
        public function getPendingLicenses() {
            try {
                $query = "SELECT
                            v.userId AS id,
                            p.fullname AS name,
                            p.email,
                            p.phone,
                            p.account_type,
                            p.profile_photo,
                            p.tLicenseNumber AS license_number,
                            p.tLicenseExpiryDate AS license_expire_date,
                            p.tLicensePhotoFront AS license_photo_front,
                            p.tLicensePhotoBack AS license_photo_back,
                            v.status,
                            v.createdAt AS created_at
                          FROM tlicense_verifications v
                          JOIN vw_user_complete_profiles p ON v.userId = p.userId
                          WHERE v.status = 'pending'
                          ORDER BY v.createdAt DESC";

                $this->db->query($query);
                $results = $this->db->resultSet();
                error_log("getPendingLicenses: found " . count($results) . " pending licenses");
                return $results;

            } catch (PDOException $e) {
                error_log("Database error in getPendingLicenses: " . $e->getMessage());
                return [];
            }
        }

        public function getVerifiedLicenses() {
            try {
                $query = "SELECT
                            v.userId AS id,
                            p.fullname AS name,
                            p.email,
                            p.phone,
                            p.account_type,
                            p.profile_photo,
                            p.tLicenseNumber AS license_number,
                            p.tLicenseExpiryDate AS license_expire_date,
                            p.tLicensePhotoFront AS license_photo_front,
                            p.tLicensePhotoBack AS license_photo_back,
                            v.status,
                            v.reviewedAt AS verified_at,
                            v.expiryDate AS verification_expiry
                          FROM tlicense_verifications v
                          JOIN vw_user_complete_profiles p ON v.userId = p.userId
                          WHERE v.status = 'approved'
                          ORDER BY v.reviewedAt DESC";

                $this->db->query($query);
                $results = $this->db->resultSet();
                error_log("getVerifiedLicenses: found " . count($results) . " verified licenses");
                return $results;

            } catch (PDOException $e) {
                error_log("Database error in getVerifiedLicenses: " . $e->getMessage());
                return [];
            }
        }

        public function getRejectedLicenses() {
            try {
                $query = "SELECT
                            v.userId AS id,
                            p.fullname AS name,
                            p.email,
                            p.phone,
                            p.account_type,
                            p.profile_photo,
                            p.tLicenseNumber AS license_number,
                            p.tLicenseExpiryDate AS license_expire_date,
                            p.tLicensePhotoFront AS license_photo_front,
                            p.tLicensePhotoBack AS license_photo_back,
                            v.status,
                            v.rejectionReason,
                            v.reviewedAt AS rejected_at
                          FROM tlicense_verifications v
                          JOIN vw_user_complete_profiles p ON v.userId = p.userId
                          WHERE v.status = 'rejected'
                          ORDER BY v.reviewedAt DESC";

                $this->db->query($query);
                $results = $this->db->resultSet();
                error_log("getRejectedLicenses: found " . count($results) . " rejected licenses");
                return $results;

            } catch (PDOException $e) {
                error_log("Database error in getRejectedLicenses: " . $e->getMessage());
                return [];
            }
        }

        public function getLicenseDetails($userId) {
            try {
                $userId = (int) $userId;
                $query = "SELECT
                            v.id,
                            v.userId,
                            p.fullname AS name,
                            p.email,
                            p.phone,
                            p.account_type,
                            p.tLicenseNumber AS license_number,
                            p.tLicenseExpiryDate AS license_expire_date,
                            p.tLicensePhotoFront AS license_photo_front,
                            p.tLicensePhotoBack AS license_photo_back,
                            p.profile_photo,
                            p.address,
                            v.status,
                            v.rejectionReason,
                            v.reviewedAt,
                            v.expiryDate AS verification_expiry,
                            v.createdAt AS submitted_at
                          FROM tlicense_verifications v
                          JOIN vw_user_complete_profiles p ON v.userId = p.userId
                          WHERE v.userId = :userId";

                $this->db->query($query);
                $this->db->bind(':userId', $userId);
                $license = $this->db->single();

                return $license;

            } catch (PDOException $e) {
                error_log("Database error in getLicenseDetails: " . $e->getMessage());
                return null;
            }
        }

        public function verifyLicense($userId, $moderatorId) {
            try {
                // Update the tlicense_verifications table
                $query = "UPDATE tlicense_verifications
                          SET status = 'approved',
                              reviewedBy = :moderatorId,
                              reviewedAt = NOW(),
                              expiryDate = (SELECT tLicenseExpiryDate FROM vw_user_complete_profiles WHERE userId = :userId),
                              updatedAt = NOW()
                          WHERE userId = :userId AND status = 'pending'";

                $this->db->query($query);
                $this->db->bind(':userId', $userId);
                $this->db->bind(':moderatorId', $moderatorId);

                $result = $this->db->execute();

                if ($result) {
                    // Update the profile_details table tlVerified status
                    $profileQuery = "UPDATE profile_details SET tlVerified = 1, updatedAt = NOW() WHERE userId = :userId";
                    $this->db->query($profileQuery);
                    $this->db->bind(':userId', $userId);
                    $this->db->execute();
                }

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in verifyLicense: " . $e->getMessage());
                return false;
            }
        }

        public function rejectLicense($userId, $moderatorId, $reason) {
            try {
                // Update the tlicense_verifications table
                $query = "UPDATE tlicense_verifications
                          SET status = 'rejected',
                              reviewedBy = :moderatorId,
                              reviewedAt = NOW(),
                              rejectionReason = :reason,
                              updatedAt = NOW()
                          WHERE userId = :userId AND status = 'pending'";

                $this->db->query($query);
                $this->db->bind(':userId', $userId);
                $this->db->bind(':moderatorId', $moderatorId);
                $this->db->bind(':reason', $reason);

                $result = $this->db->execute();

                if ($result) {
                    // Update the profile_details table tlVerified status to 0
                    $profileQuery = "UPDATE profile_details SET tlVerified = 0, updatedAt = NOW() WHERE userId = :userId";
                    $this->db->query($profileQuery);
                    $this->db->bind(':userId', $userId);
                    $this->db->execute();
                }

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in rejectLicense: " . $e->getMessage());
                return false;
            }
        }

        public function revokeLicenseVerification($userId, $moderatorId) {
            try {
                // Update the tlicense_verifications table back to pending
                $query = "UPDATE tlicense_verifications
                          SET status = 'pending',
                              reviewedBy = NULL,
                              reviewedAt = NULL,
                              expiryDate = NULL,
                              updatedAt = NOW()
                          WHERE userId = :userId AND status = 'approved'";

                $this->db->query($query);
                $this->db->bind(':userId', $userId);

                $result = $this->db->execute();

                if ($result) {
                    // Update the profile_details table tlVerified status to 0
                    $profileQuery = "UPDATE profile_details SET tlVerified = 0, updatedAt = NOW() WHERE userId = :userId";
                    $this->db->query($profileQuery);
                    $this->db->bind(':userId', $userId);
                    $this->db->execute();
                }

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in revokeLicenseVerification: " . $e->getMessage());
                return false;
            }
        }

        public function revokeLicenseRejection($userId, $moderatorId) {
            try {
                // Update the tlicense_verifications table back to pending
                $query = "UPDATE tlicense_verifications
                          SET status = 'pending',
                              reviewedBy = NULL,
                              reviewedAt = NULL,
                              rejectionReason = NULL,
                              updatedAt = NOW()
                          WHERE userId = :userId AND status = 'rejected'";

                $this->db->query($query);
                $this->db->bind(':userId', $userId);

                $result = $this->db->execute();

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in revokeLicenseRejection: " . $e->getMessage());
                return false;
            }
        }

        public function getPendingVehicles() {
            try {
                // Get vehicles that either:
                // 1. Have no verification record yet
                // 2. Have a verification record with status = 'pending'
                $query = "SELECT
                            dv.vehicleId AS id,
                            dv.driverName AS owner_name,
                            dv.driverEmail AS driver_email,
                            dv.driverPhone AS driver_phone,
                            CONCAT(dv.make, ' ', dv.model) AS vehicle_type,
                            dv.model,
                            dv.licensePlate AS registration_number,
                            dv.frontViewPhoto AS vehicle_photo,
                            dv.seatingCapacity,
                            dv.childSeats,
                            dv.vehicleDescription,
                            dv.frontViewPhoto,
                            dv.backViewPhoto,
                            dv.sideViewPhoto,
                            dv.interiorPhoto1,
                            dv.interiorPhoto2,
                            dv.interiorPhoto3,
                            dv.driverPhone,
                            dv.driverSecondaryPhone,
                            dv.driverEmail,
                            dv.vehicleCreatedAt AS created_at,
                            COALESCE(vv.createdAt, dv.vehicleCreatedAt) AS submission_date
                          FROM `driver_vehicles_view` dv
                          LEFT JOIN vehicle_verifications vv ON dv.vehicleId = vv.vehicleId
                          WHERE vv.id IS NULL OR vv.status = 'pending'
                          ORDER BY submission_date DESC";

                $this->db->query($query);
                $results = $this->db->resultSet();
                error_log("getPendingVehicles: found " . count($results) . " pending vehicles");
                return $results;

            } catch (PDOException $e) {
                error_log("Database error in getPendingVehicles: " . $e->getMessage());
                return [];
            }
        }

        public function getVerifiedVehicles() {
            try {
                $query = "SELECT
                            dv.vehicleId AS id,
                            dv.driverName AS owner_name,
                            dv.driverEmail AS driver_email,
                            dv.driverPhone AS driver_phone,
                            CONCAT(dv.make, ' ', dv.model) AS vehicle_type,
                            dv.model,
                            dv.licensePlate AS registration_number,
                            dv.frontViewPhoto AS vehicle_photo,
                            dv.seatingCapacity,
                            dv.childSeats,
                            dv.vehicleDescription,
                            dv.frontViewPhoto,
                            dv.backViewPhoto,
                            dv.sideViewPhoto,
                            dv.interiorPhoto1,
                            dv.interiorPhoto2,
                            dv.interiorPhoto3,
                            dv.vehicleCreatedAt AS created_at,
                            vv.status AS verification_status,
                            vv.reviewedAt AS reviewed_at
                          FROM `driver_vehicles_view` dv
                          INNER JOIN vehicle_verifications vv ON dv.vehicleId = vv.vehicleId
                          WHERE vv.status = 'approved'
                          ORDER BY vv.reviewedAt DESC";

                $this->db->query($query);
                $results = $this->db->resultSet();
                error_log("getVerifiedVehicles: found " . count($results) . " verified vehicles");
                return $results;

            } catch (PDOException $e) {
                error_log("Database error in getVerifiedVehicles: " . $e->getMessage());
                return [];
            }
        }

        public function getRejectedVehicles() {
            try {
                $query = "SELECT
                            dv.vehicleId AS id,
                            dv.driverName AS owner_name,
                            dv.driverEmail AS driver_email,
                            dv.driverPhone AS driver_phone,
                            CONCAT(dv.make, ' ', dv.model) AS vehicle_type,
                            dv.model,
                            dv.licensePlate AS registration_number,
                            dv.frontViewPhoto AS vehicle_photo,
                            dv.seatingCapacity,
                            dv.childSeats,
                            dv.vehicleDescription,
                            dv.frontViewPhoto,
                            dv.backViewPhoto,
                            dv.sideViewPhoto,
                            dv.interiorPhoto1,
                            dv.interiorPhoto2,
                            dv.interiorPhoto3,
                            dv.vehicleCreatedAt AS created_at,
                            vv.status AS verification_status,
                            vv.reviewedAt AS reviewed_at,
                            vv.rejectionReason
                          FROM `driver_vehicles_view` dv
                          LEFT JOIN vehicle_verifications vv ON dv.vehicleId = vv.vehicleId
                          WHERE vv.status = 'rejected'
                          ORDER BY vv.reviewedAt DESC";

                $this->db->query($query);
                $results = $this->db->resultSet();
                error_log("getRejectedVehicles: found " . count($results) . " rejected vehicles");
                return $results;

            } catch (PDOException $e) {
                error_log("Database error in getRejectedVehicles: " . $e->getMessage());
                return [];
            }
        }

        public function verifyVehicle($vehicleId, $moderatorId) {
            try {
                // First check if there's already a verification record
                $checkQuery = "SELECT id FROM vehicle_verifications WHERE vehicleId = :vehicleId";
                $this->db->query($checkQuery);
                $this->db->bind(':vehicleId', $vehicleId);
                $existing = $this->db->single();

                if ($existing) {
                    // Update existing record
                    $query = "UPDATE vehicle_verifications
                              SET status = 'approved',
                                  reviewedBy = :moderatorId,
                                  reviewedAt = NOW(),
                                  updatedAt = NOW()
                              WHERE vehicleId = :vehicleId AND status = 'pending'";
                } else {
                    // Insert new record
                    $query = "INSERT INTO vehicle_verifications (vehicleId, userId, status, reviewedBy, reviewedAt, createdAt, updatedAt)
                              VALUES (:vehicleId, (SELECT driverId FROM vehicles WHERE vehicleId = :vehicleId), 'approved', :moderatorId, NOW(), NOW(), NOW())";
                }

                $this->db->query($query);
                $this->db->bind(':vehicleId', $vehicleId);
                $this->db->bind(':moderatorId', $moderatorId);

                $result = $this->db->execute();

                if ($result) {
                    // Update the vehicles table isApproved status
                    $vehicleQuery = "UPDATE vehicles SET isApproved = 1, updatedAt = NOW() WHERE vehicleId = :vehicleId";
                    $this->db->query($vehicleQuery);
                    $this->db->bind(':vehicleId', $vehicleId);
                    $this->db->execute();
                }

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in verifyVehicle: " . $e->getMessage());
                return false;
            }
        }

        public function rejectVehicle($vehicleId, $moderatorId, $reason) {
            try {
                // First check if there's already a verification record
                $checkQuery = "SELECT id FROM vehicle_verifications WHERE vehicleId = :vehicleId";
                $this->db->query($checkQuery);
                $this->db->bind(':vehicleId', $vehicleId);
                $existing = $this->db->single();

                if ($existing) {
                    // Update existing record
                    $query = "UPDATE vehicle_verifications
                              SET status = 'rejected',
                                  reviewedBy = :moderatorId,
                                  reviewedAt = NOW(),
                                  rejectionReason = :reason,
                                  updatedAt = NOW()
                              WHERE vehicleId = :vehicleId AND status = 'pending'";
                } else {
                    // Insert new record
                    $query = "INSERT INTO vehicle_verifications (vehicleId, userId, status, reviewedBy, reviewedAt, rejectionReason, createdAt, updatedAt)
                              VALUES (:vehicleId, (SELECT driverId FROM vehicles WHERE vehicleId = :vehicleId), 'rejected', :moderatorId, NOW(), :reason, NOW(), NOW())";
                }

                $this->db->query($query);
                $this->db->bind(':vehicleId', $vehicleId);
                $this->db->bind(':moderatorId', $moderatorId);
                $this->db->bind(':reason', $reason);

                $result = $this->db->execute();

                if ($result) {
                    // Update the vehicles table isApproved status to 0
                    $vehicleQuery = "UPDATE vehicles SET isApproved = 0, updatedAt = NOW() WHERE vehicleId = :vehicleId";
                    $this->db->query($vehicleQuery);
                    $this->db->bind(':vehicleId', $vehicleId);
                    $this->db->execute();
                }

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in rejectVehicle: " . $e->getMessage());
                return false;
            }
        }

        public function revokeVehicleVerification($vehicleId, $moderatorId) {
            try {
                // Update the vehicle_verifications table to set status back to pending
                $query = "UPDATE vehicle_verifications
                          SET status = 'pending',
                              reviewedBy = NULL,
                              reviewedAt = NULL,
                              rejectionReason = NULL,
                              updatedAt = NOW()
                          WHERE vehicleId = :vehicleId AND status = 'approved'";

                $this->db->query($query);
                $this->db->bind(':vehicleId', $vehicleId);

                $result = $this->db->execute();

                if ($result) {
                    // Update the vehicles table isApproved status to 0 (pending)
                    $vehicleQuery = "UPDATE vehicles SET isApproved = 0, updatedAt = NOW() WHERE vehicleId = :vehicleId";
                    $this->db->query($vehicleQuery);
                    $this->db->bind(':vehicleId', $vehicleId);
                    $this->db->execute();
                }

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in revokeVehicleVerification: " . $e->getMessage());
                return false;
            }
        }

        public function revokeVehicleRejection($vehicleId, $moderatorId) {
            try {
                // Update the vehicle_verifications table to set status back to pending
                $query = "UPDATE vehicle_verifications
                          SET status = 'pending',
                              reviewedBy = NULL,
                              reviewedAt = NULL,
                              rejectionReason = NULL,
                              updatedAt = NOW()
                          WHERE vehicleId = :vehicleId AND status = 'rejected'";

                $this->db->query($query);
                $this->db->bind(':vehicleId', $vehicleId);

                $result = $this->db->execute();

                if ($result) {
                    // Update the vehicles table isApproved status to 0 (pending)
                    $vehicleQuery = "UPDATE vehicles SET isApproved = 0, updatedAt = NOW() WHERE vehicleId = :vehicleId";
                    $this->db->query($vehicleQuery);
                    $this->db->bind(':vehicleId', $vehicleId);
                    $this->db->execute();
                }

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in revokeVehicleRejection: " . $e->getMessage());
                return false;
            }
        }

        public function getVehicleDetails($vehicleId) {
            try {
                $query = "SELECT
                            dv.vehicleId AS id,
                            dv.driverName AS owner_name,
                            dv.driverEmail AS driver_email,
                            dv.driverPhone,
                            dv.driverSecondaryPhone,
                            dv.driverAddress,
                            dv.make,
                            CONCAT(dv.make, ' ', dv.model) AS vehicle_type,
                            dv.model,
                            dv.year,
                            dv.color,
                            dv.licensePlate AS registration_number,
                            dv.seatingCapacity,
                            dv.childSeats,
                            dv.fuelEfficiency,
                            dv.vehicleDescription,
                            dv.frontViewPhoto AS vehicle_photo,
                            dv.frontViewPhoto,
                            dv.backViewPhoto,
                            dv.sideViewPhoto,
                            dv.interiorPhoto1,
                            dv.interiorPhoto2,
                            dv.interiorPhoto3,
                            dv.vehicleCreatedAt AS created_at,
                            vv.status AS verification_status,
                            vv.reviewedAt AS reviewed_at,
                            vv.rejectionReason
                          FROM `driver_vehicles_view` dv
                          LEFT JOIN vehicle_verifications vv ON dv.vehicleId = vv.vehicleId
                          WHERE dv.vehicleId = :vehicleId";

                $this->db->query($query);
                $this->db->bind(':vehicleId', $vehicleId);
                $vehicle = $this->db->single();

                error_log("getVehicleDetails: vehicleId=" . $vehicleId . ", found=" . ($vehicle ? 'yes' : 'no'));
                return $vehicle;

            } catch (PDOException $e) {
                error_log("Database error in getVehicleDetails: " . $e->getMessage());
                return null;
            }
        }

        

        
    }
?>