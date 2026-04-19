<?php
    
    class ModeratorModel{

        private $db;
        public function __construct(){
            $this->db = new Database();
        }


        public function submitUserProblem($data){
            $query = "INSERT INTO user_problems (userId, subject, message, status) VALUES (:userId, :subject, :message, 'pending')";
            $this->db->query($query);
            $this->db->bind(':userId', $data['userId']);
            $this->db->bind(':subject', $data['subject']);
            $this->db->bind(':message', $data['message']);
            return $this->db->execute();
        }


        public function getAllProblems($filter = 'all'){
            $query = "SELECT 
                        up.problemId,
                        up.userId,
                        up.subject,
                        up.message,
                        up.status,
                        up.completedBy,
                        up.completedAt,
                        up.createdAt,
                        u.fullname,
                        u.email,
                        u.phone,
                        u.account_type,
                        u.profile_photo,
                        mod_user.fullname AS completedByName
                    FROM user_problems up
                    INNER JOIN users u ON up.userId = u.id
                    LEFT JOIN users mod_user ON up.completedBy = mod_user.id";
            
            if($filter === 'pending'){
                $query .= " WHERE up.status = 'pending'";
            } elseif($filter === 'in_progress'){
                $query .= " WHERE up.status = 'in_progress'";
            } elseif($filter === 'completed'){
                $query .= " WHERE up.status = 'completed'";
            }

            $query .= " ORDER BY 
                        CASE up.status 
                            WHEN 'pending' THEN 1 
                            WHEN 'in_progress' THEN 2 
                            WHEN 'completed' THEN 3 
                        END, 
                        up.createdAt DESC";

            $this->db->query($query);
            return $this->db->resultSet();
        }

        public function completeProblem($problemId, $moderatorId){
            $query = "UPDATE user_problems SET 
                    status = 'completed',
                    completedBy = :moderatorId,
                    completedAt = CURRENT_TIMESTAMP
                    WHERE problemId = :problemId";
            
            $this->db->query($query);
            $this->db->bind(':problemId', $problemId);
            $this->db->bind(':moderatorId', $moderatorId);
            return $this->db->execute();
        }

        public function getProblemCounts(){
            $query = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
                    FROM user_problems";
            $this->db->query($query);
            return $this->db->single();
        }

        public function deleteProblem($problemId){
            $this->db->query('DELETE FROM user_problems WHERE problemId = :problemId');
            $this->db->bind(':problemId', $problemId);
            return $this->db->execute();
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
            $query = 'SELECT
                        ts.spotId,
                        ts.spotName,
                        ts.overview,
                        ts.province,
                        ts.district,
                        ts.bestTimeFrom,
                        ts.bestTimeTo,
                        ts.visitingDurationMin,
                        ts.visitingDurationMax,
                        ts.ticketPriceLocal,
                        ts.ticketPriceForeigner,
                        ts.averageRating,
                        ts.totalReviews,
                        tmf.mainFilterId,
                        tmf.mainFilterName,
                        tsf.subFilterId,
                        tsf.subFilterName,
                        tsp.photoPath
                    FROM travel_spots ts
                    LEFT JOIN travel_spots_subfilters tss ON ts.spotId = tss.spotId
                    LEFT JOIN travelspots_subfilters tsf ON tss.subFilterId = tsf.subFilterId
                    LEFT JOIN travelspots_mainfilters tmf ON tsf.mainFilterId = tmf.mainFilterId
                    LEFT JOIN travel_spots_photos tsp ON ts.spotId = tsp.spotId
                    WHERE ts.spotId IS NOT NULL
                    ORDER BY ts.spotId ASC, tsp.photoId ASC';
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

        public function searchTravelSpotsForPackages($name){

            $query = "SELECT 
                        ts.spotId,
                        ts.spotName,
                        ts.province,
                        ts.district,
                        ts.overview,
                        tsp.photoPath
                    FROM travel_spots ts
                    LEFT JOIN (
                        SELECT tsp1.spotId, tsp1.photoPath
                        FROM travel_spots_photos tsp1
                        INNER JOIN (
                            SELECT spotId, MIN(photoId) AS minPhotoId
                            FROM travel_spots_photos
                            GROUP BY spotId
                        ) tsp2 ON tsp1.photoId = tsp2.minPhotoId
                    ) tsp ON tsp.spotId = ts.spotId
                    WHERE ts.spotName LIKE CONCAT('%', :name, '%')
                    ORDER BY 
                        CASE
                            WHEN ts.spotName LIKE CONCAT(:name, '%') THEN 1
                            ELSE 2
                        END,
                        ts.spotName ASC
                    LIMIT 30";

            $this->db->query($query);
            $this->db->bind(':name', $name);
            return $this->db->resultSet();
        }

        public function createTravelPackage($packageData){

            $query = "INSERT INTO travel_packages (
                        packageName,
                        overview,
                        packageDetails,
                        durationDays,
                        estimatedPriceLkr,
                        status,
                        moderatorId
                    ) VALUES (
                        :packageName,
                        :overview,
                        :packageDetails,
                        :durationDays,
                        :estimatedPriceLkr,
                        :status,
                        :moderatorId
                    )";

            $this->db->query($query);
            $this->db->bind(':packageName', $packageData['packageName']);
            $this->db->bind(':overview', $packageData['overview']);
            $this->db->bind(':packageDetails', $packageData['packageDetails']);
            $this->db->bind(':durationDays', (int) $packageData['durationDays']);
            $this->db->bind(':estimatedPriceLkr', $packageData['estimatedPriceLkr']);
            $this->db->bind(':status', $packageData['status']);
            $this->db->bind(':moderatorId', (int) $packageData['moderatorId']);

            $this->db->execute();
            return (int) $this->db->lastInsertId();
        }

        public function addTravelPackageSpot($packageId, $spotId, $dayNumber, $visitOrder, $spotNote = null){

            $query = "INSERT INTO travel_package_spots (
                        packageId,
                        spotId,
                        dayNumber,
                        visitOrder,
                        spotNote
                    ) VALUES (
                        :packageId,
                        :spotId,
                        :dayNumber,
                        :visitOrder,
                        :spotNote
                    )";

            $this->db->query($query);
            $this->db->bind(':packageId', (int) $packageId);
            $this->db->bind(':spotId', (int) $spotId);
            $this->db->bind(':dayNumber', (int) $dayNumber);
            $this->db->bind(':visitOrder', (int) $visitOrder);
            $this->db->bind(':spotNote', $spotNote);
            return $this->db->execute();
        }

        public function addTravelPackagePhoto($packageId, $photoPath, $photoOrder){

            $query = "INSERT INTO travel_package_photos (packageId, photoPath, photoOrder)
                    VALUES (:packageId, :photoPath, :photoOrder)";

            $this->db->query($query);
            $this->db->bind(':packageId', (int) $packageId);
            $this->db->bind(':photoPath', $photoPath);
            $this->db->bind(':photoOrder', (int) $photoOrder);
            return $this->db->execute();
        }

        public function createTravelPackageWithRelations($packageData, $selectedSpots, $photoPaths = []){
            try {
                $this->db->beginTransaction();

                $packageId = $this->createTravelPackage($packageData);

                foreach ($selectedSpots as $index => $spot) {
                    if (empty($spot['spotId'])) {
                        throw new Exception('Selected spot is missing spotId');
                    }

                    $dayNumber = !empty($spot['dayNumber']) ? (int) $spot['dayNumber'] : 1;
                    $visitOrder = !empty($spot['visitOrder']) ? (int) $spot['visitOrder'] : ($index + 1);
                    $spotNote = isset($spot['spotNote']) && $spot['spotNote'] !== ''
                        ? trim((string) $spot['spotNote'])
                        : null;

                    $this->addTravelPackageSpot($packageId, (int) $spot['spotId'], $dayNumber, $visitOrder, $spotNote);
                }

                foreach ($photoPaths as $index => $photoPath) {
                    $this->addTravelPackagePhoto($packageId, $photoPath, $index + 1);
                }

                $this->db->commit();
                return $packageId;

            } catch (Exception $e) {
                $this->db->rollback();
                error_log('createTravelPackageWithRelations failed: ' . $e->getMessage());
                return false;
            }
        }

        public function loadTravelPackageCardData(){

            $query = "SELECT
                        tp.packageId,
                        tp.packageName,
                        tp.overview,
                        tp.packageDetails,
                        tp.durationDays,
                        tp.estimatedPriceLkr,
                        tp.status,
                        tp.createdAt,
                        cover.photoPath AS coverPhotoPath,
                        IFNULL(spotMeta.spotCount, 0) AS spotCount,
                        IFNULL(spotMeta.spotNames, '') AS spotNames
                    FROM travel_packages tp
                    LEFT JOIN (
                        SELECT tpp1.packageId, tpp1.photoPath
                        FROM travel_package_photos tpp1
                        INNER JOIN (
                            SELECT packageId, MIN(packagePhotoId) AS firstPhotoId
                            FROM travel_package_photos
                            GROUP BY packageId
                        ) tpp2 ON tpp1.packagePhotoId = tpp2.firstPhotoId
                    ) cover ON cover.packageId = tp.packageId
                    LEFT JOIN (
                        SELECT
                            tps.packageId,
                            COUNT(*) AS spotCount,
                            GROUP_CONCAT(ts.spotName ORDER BY tps.dayNumber ASC, tps.visitOrder ASC SEPARATOR ', ') AS spotNames
                        FROM travel_package_spots tps
                        INNER JOIN travel_spots ts ON ts.spotId = tps.spotId
                        GROUP BY tps.packageId
                    ) spotMeta ON spotMeta.packageId = tp.packageId
                    ORDER BY tp.createdAt DESC";

            $this->db->query($query);
            return $this->db->resultSet();
        }

        public function loadTravelPackageData($packageId){

            $mainQuery = "SELECT
                            tp.packageId,
                            tp.packageName,
                            tp.overview,
                            tp.packageDetails,
                            tp.durationDays,
                            tp.estimatedPriceLkr,
                            tp.status,
                            tp.moderatorId,
                            tp.createdAt,
                            tp.updatedAt
                        FROM travel_packages tp
                        WHERE tp.packageId = :packageId";

            $this->db->query($mainQuery);
            $this->db->bind(':packageId', (int) $packageId);
            $mainDetails = $this->db->single();

            if (!$mainDetails) {
                return null;
            }

            $photoQuery = "SELECT packagePhotoId, photoPath, photoOrder
                        FROM travel_package_photos
                        WHERE packageId = :packageId
                        ORDER BY photoOrder ASC, packagePhotoId ASC";

            $this->db->query($photoQuery);
            $this->db->bind(':packageId', (int) $packageId);
            $photos = $this->db->resultSet();

            $spotsQuery = "SELECT
                            tps.packageSpotId,
                            tps.spotId,
                            tps.dayNumber,
                            tps.visitOrder,
                            tps.spotNote,
                            ts.spotName,
                            ts.province,
                            ts.district,
                            ts.overview,
                            spotCover.photoPath AS spotPhotoPath
                        FROM travel_package_spots tps
                        INNER JOIN travel_spots ts ON ts.spotId = tps.spotId
                        LEFT JOIN (
                            SELECT tsp1.spotId, tsp1.photoPath
                            FROM travel_spots_photos tsp1
                            INNER JOIN (
                                SELECT spotId, MIN(photoId) AS minPhotoId
                                FROM travel_spots_photos
                                GROUP BY spotId
                            ) tsp2 ON tsp1.photoId = tsp2.minPhotoId
                        ) spotCover ON spotCover.spotId = ts.spotId
                        WHERE tps.packageId = :packageId
                        ORDER BY tps.dayNumber ASC, tps.visitOrder ASC";

            $this->db->query($spotsQuery);
            $this->db->bind(':packageId', (int) $packageId);
            $spots = $this->db->resultSet();

            return [
                'mainDetails' => $mainDetails,
                'photos' => $photos,
                'spots' => $spots
            ];
        }

        public function deleteTravelPackage($packageId){

            $this->db->query('DELETE FROM travel_package_spots WHERE packageId = :packageId');
            $this->db->bind(':packageId', (int) $packageId);
            $this->db->execute();

            $this->db->query('DELETE FROM travel_package_photos WHERE packageId = :packageId');
            $this->db->bind(':packageId', (int) $packageId);
            $this->db->execute();

            $this->db->query('DELETE FROM travel_packages WHERE packageId = :packageId');
            $this->db->bind(':packageId', (int) $packageId);
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

        // Review Monitoring Methods
        public function getAllReviews() {
            try {
                $query = "SELECT
                            tr.reviewId,
                            tr.travellerId,
                            tr.guideDriverId,
                            tr.reviewText,
                            tr.rating,
                            tr.createdAt,
                            u.fullname AS reviewerName,
                            u.email AS reviewerEmail,
                            u.profile_photo AS reviewerPhoto,
                            CASE
                                WHEN tr.guideDriverId IS NOT NULL THEN
                                    (SELECT fullname FROM users WHERE id = tr.guideDriverId)
                                ELSE 'Unknown'
                            END AS targetName,
                            CASE
                                WHEN tr.guideDriverId IS NOT NULL THEN
                                    (SELECT profile_photo FROM users WHERE id = tr.guideDriverId)
                                ELSE NULL
                            END AS targetPhoto,
                            CASE
                                WHEN tr.guideDriverId IS NOT NULL THEN
                                    (SELECT account_type FROM users WHERE id = tr.guideDriverId)
                                ELSE NULL
                            END AS account_type
                          FROM traveller_reviews tr
                          JOIN users u ON tr.travellerId = u.id
                          ORDER BY tr.createdAt DESC";

                $this->db->query($query);
                $results = $this->db->resultSet();
                error_log("getAllReviews: found " . count($results) . " reviews");
                return $results;

            } catch (PDOException $e) {
                error_log("Database error in getAllReviews: " . $e->getMessage());
                return [];
            }
        }

        public function getReviewStats() {
            try {
                $query = "SELECT
                            COUNT(*) as total,
                            AVG(rating) as average,
                            COUNT(CASE WHEN rating <= 2.0 THEN 1 END) as lowRated,
                            COUNT(CASE WHEN createdAt >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as recent
                          FROM traveller_reviews";

                $this->db->query($query);
                $stats = $this->db->single();

                return [
                    'total' => (int) ($stats->total ?? 0),
                    'average' => round((float) ($stats->average ?? 0), 1),
                    'lowRated' => (int) ($stats->lowRated ?? 0),
                    'recent' => (int) ($stats->recent ?? 0)
                ];

            } catch (PDOException $e) {
                error_log("Database error in getReviewStats: " . $e->getMessage());
                return ['total' => 0, 'average' => 0.0, 'lowRated' => 0, 'recent' => 0];
            }
        }

        public function deleteReview($reviewId) {
            try {
                $this->db->query("DELETE FROM traveller_reviews WHERE reviewId = :reviewId");
                $this->db->bind(':reviewId', $reviewId);
                $result = $this->db->execute();
                error_log("deleteReview: reviewId=$reviewId, deleted=" . ($result ? 'yes' : 'no'));
                return $result;

            } catch (PDOException $e) {
                error_log("Database error in deleteReview: " . $e->getMessage());
                return false;
            }
        }

        // Traveler Payments Methods
        public function getCompletedPayments() {
            try {
                $query = "SELECT
                            tp.wholePaymentId AS id,
                            tp.tripId,
                            tp.totalCharge AS amount,
                            tp.createdAt AS paymentDate,
                            u.fullname AS travelerName,
                            u.email AS travelerEmail,
                            CASE
                                WHEN dp.driverId IS NOT NULL THEN d.fullname
                                WHEN gp.guideId IS NOT NULL THEN g.fullname
                                ELSE 'Unknown'
                            END AS providerName,
                            CASE
                                WHEN dp.driverId IS NOT NULL THEN 'Driver'
                                WHEN gp.guideId IS NOT NULL THEN 'Guide'
                                ELSE 'Unknown'
                            END AS providerType,
                            dp.pDoneTraveller,
                            dp.pDoneSite,
                            dp.pDateSite,
                            gp.pDoneTraveller AS g_pDoneTraveller,
                            gp.pDoneSite AS g_pDoneSite,
                            gp.pDateSite AS g_pDateSite
                          FROM trip_payments tp
                          JOIN users u ON tp.travellerId = u.id
                          LEFT JOIN driver_payments dp ON tp.wholePaymentId = dp.wholePaymentId
                          LEFT JOIN guide_payments gp ON tp.wholePaymentId = gp.wholePaymentId
                          LEFT JOIN users d ON dp.driverId = d.id
                          LEFT JOIN users g ON gp.guideId = g.id
                          WHERE tp.paymentStatus = 'completed'
                          ORDER BY tp.createdAt DESC";

                $this->db->query($query);
                $results = $this->db->resultSet();
                error_log("getCompletedPayments: found " . count($results) . " completed payments");
                return $results;

            } catch (PDOException $e) {
                error_log("Database error in getCompletedPayments: " . $e->getMessage());
                return [];
            }
        }

        public function getCancelledPayments() {
            try {
                $query = "SELECT
                            tp.wholePaymentId AS id,
                            tp.tripId,
                            tp.totalCharge AS amount,
                            tp.createdAt AS paymentDate,
                            u.fullname AS travelerName,
                            u.email AS travelerEmail,
                            CASE
                                WHEN dp.driverId IS NOT NULL THEN d.fullname
                                WHEN gp.guideId IS NOT NULL THEN g.fullname
                                ELSE 'Unknown'
                            END AS providerName,
                            CASE
                                WHEN dp.driverId IS NOT NULL THEN 'Driver'
                                WHEN gp.guideId IS NOT NULL THEN 'Guide'
                                ELSE 'Unknown'
                            END AS providerType
                          FROM trip_payments tp
                          JOIN users u ON tp.travellerId = u.id
                          LEFT JOIN driver_payments dp ON tp.wholePaymentId = dp.wholePaymentId
                          LEFT JOIN guide_payments gp ON tp.wholePaymentId = gp.wholePaymentId
                          LEFT JOIN users d ON dp.driverId = d.id
                          LEFT JOIN users g ON gp.guideId = g.id
                          WHERE tp.paymentStatus = 'cancelled'
                          ORDER BY tp.createdAt DESC";

                $this->db->query($query);
                $results = $this->db->resultSet();
                error_log("getCancelledPayments: found " . count($results) . " cancelled payments");
                return $results;

            } catch (PDOException $e) {
                error_log("Database error in getCancelledPayments: " . $e->getMessage());
                return [];
            }
        }

        public function getRefundedPayments() {
            try {
                $query = "SELECT
                            tp.wholePaymentId AS id,
                            tp.tripId,
                            COALESCE(dp.refundAmount, gp.refundAmount) AS refundAmount,
                            COALESCE(dp.refundDate, gp.refundDate) AS refundDate,
                            COALESCE(dp.refundReason, gp.refundReason) AS refundReason,
                            u.fullname AS travelerName,
                            u.email AS travelerEmail,
                            CASE
                                WHEN dp.driverId IS NOT NULL THEN d.fullname
                                WHEN gp.guideId IS NOT NULL THEN g.fullname
                                ELSE 'Unknown'
                            END AS providerName,
                            CASE
                                WHEN dp.driverId IS NOT NULL THEN 'Driver'
                                WHEN gp.guideId IS NOT NULL THEN 'Guide'
                                ELSE 'Unknown'
                            END AS providerType
                          FROM trip_payments tp
                          JOIN users u ON tp.travellerId = u.id
                          LEFT JOIN driver_payments dp ON tp.wholePaymentId = dp.wholePaymentId AND dp.refunded = 1
                          LEFT JOIN guide_payments gp ON tp.wholePaymentId = gp.wholePaymentId AND gp.refunded = 1
                          LEFT JOIN users d ON dp.driverId = d.id
                          LEFT JOIN users g ON gp.guideId = g.id
                          WHERE tp.paymentStatus = 'refunded'
                          ORDER BY COALESCE(dp.refundDate, gp.refundDate) DESC";

                $this->db->query($query);
                $results = $this->db->resultSet();
                error_log("getRefundedPayments: found " . count($results) . " refunded payments");
                return $results;

            } catch (PDOException $e) {
                error_log("Database error in getRefundedPayments: " . $e->getMessage());
                return [];
            }
        }

        public function getPaymentDetails($paymentId) {
            try {
                $query = "SELECT
                            tp.wholePaymentId,
                            tp.tripId,
                            tp.travellerId,
                            tp.paymentStatus,
                            tp.totalCharge,
                            tp.transactionId,
                            tp.createdAt,
                            tp.updatedAt,
                            u.fullname AS travelerName,
                            u.email AS travelerEmail,
                            u.phone AS travelerPhone,
                            -- Driver payment details
                            dp.driverPaymentId,
                            dp.driverId,
                            dp.driverCharge,
                            dp.pDoneTraveller AS driver_pDoneTraveller,
                            dp.pDoneSite AS driver_pDoneSite,
                            dp.pDateTraveller AS driver_pDateTraveller,
                            dp.pDateSite AS driver_pDateSite,
                            dp.refunded AS driver_refunded,
                            dp.refundDate AS driver_refundDate,
                            dp.refundAmount AS driver_refundAmount,
                            dp.refundReason AS driver_refundReason,
                            d.fullname AS driverName,
                            d.email AS driverEmail,
                            -- Guide payment details
                            gp.guidePaymentId,
                            gp.guideId,
                            gp.guideCharge,
                            gp.pDoneTraveller AS guide_pDoneTraveller,
                            gp.pDoneSite AS guide_pDoneSite,
                            gp.pDateTraveller AS guide_pDateTraveller,
                            gp.pDateSite AS guide_pDateSite,
                            gp.refunded AS guide_refunded,
                            gp.refundDate AS guide_refundDate,
                            gp.refundAmount AS guide_refundAmount,
                            gp.refundReason AS guide_refundReason,
                            g.fullname AS guideName,
                            g.email AS guideEmail
                          FROM trip_payments tp
                          JOIN users u ON tp.travellerId = u.id
                          LEFT JOIN driver_payments dp ON tp.wholePaymentId = dp.wholePaymentId
                          LEFT JOIN guide_payments gp ON tp.wholePaymentId = gp.wholePaymentId
                          LEFT JOIN users d ON dp.driverId = d.id
                          LEFT JOIN users g ON gp.guideId = g.id
                          WHERE tp.wholePaymentId = :paymentId";

                $this->db->query($query);
                $this->db->bind(':paymentId', $paymentId);
                $payment = $this->db->single();

                if ($payment) {
                    error_log("getPaymentDetails: found payment details for paymentId=" . $paymentId);
                } else {
                    error_log("getPaymentDetails: no payment found for paymentId=" . $paymentId);
                }

                return $payment;

            } catch (PDOException $e) {
                error_log("Database error in getPaymentDetails: " . $e->getMessage());
                return null;
            }
        }

        public function payDriver($driverPaymentId, $moderatorId) {
            try {
                $query = "UPDATE driver_payments
                          SET pDoneSite = 1,
                              pDateSite = NOW(),
                              updatedAt = NOW()
                          WHERE driverPaymentId = :driverPaymentId AND pDoneSite = 0";

                $this->db->query($query);
                $this->db->bind(':driverPaymentId', $driverPaymentId);

                $result = $this->db->execute();
                error_log("payDriver: driverPaymentId=$driverPaymentId, result=" . ($result ? 'success' : 'failed'));

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in payDriver: " . $e->getMessage());
                return false;
            }
        }

        public function payGuide($guidePaymentId, $moderatorId) {
            try {
                $query = "UPDATE guide_payments
                          SET pDoneSite = 1,
                              pDateSite = NOW(),
                              updatedAt = NOW()
                          WHERE guidePaymentId = :guidePaymentId AND pDoneSite = 0";

                $this->db->query($query);
                $this->db->bind(':guidePaymentId', $guidePaymentId);

                $result = $this->db->execute();
                error_log("payGuide: guidePaymentId=$guidePaymentId, result=" . ($result ? 'success' : 'failed'));

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in payGuide: " . $e->getMessage());
                return false;
            }
        }

        public function refundPayment($paymentId, $moderatorId, $reason) {
            try {
                // First update the trip_payments table
                $query = "UPDATE trip_payments
                          SET paymentStatus = 'refunded',
                              updatedAt = NOW()
                          WHERE wholePaymentId = :paymentId";

                $this->db->query($query);
                $this->db->bind(':paymentId', $paymentId);
                $result1 = $this->db->execute();

                // Then update driver_payments if exists
                $driverQuery = "UPDATE driver_payments
                               SET refunded = 1,
                                   refundDate = NOW(),
                                   refundAmount = driverCharge,
                                   refundReason = :reason,
                                   updatedAt = NOW()
                               WHERE wholePaymentId = :paymentId";

                $this->db->query($driverQuery);
                $this->db->bind(':paymentId', $paymentId);
                $this->db->bind(':reason', $reason);
                $result2 = $this->db->execute();

                // Then update guide_payments if exists
                $guideQuery = "UPDATE guide_payments
                              SET refunded = 1,
                                  refundDate = NOW(),
                                  refundAmount = guideCharge,
                                  refundReason = :reason,
                                  updatedAt = NOW()
                              WHERE wholePaymentId = :paymentId";

                $this->db->query($guideQuery);
                $this->db->bind(':paymentId', $paymentId);
                $this->db->bind(':reason', $reason);
                $result3 = $this->db->execute();

                $overallResult = $result1 && ($result2 || $result3);
                error_log("refundPayment: paymentId=$paymentId, result=" . ($overallResult ? 'success' : 'failed'));

                return $overallResult;

            } catch (PDOException $e) {
                error_log("Database error in refundPayment: " . $e->getMessage());
                return false;
            }
        }

        public function getPaymentStats() {
            try {
                $query = "SELECT
                            COUNT(CASE WHEN paymentStatus = 'completed' THEN 1 END) AS completed_count,
                            COUNT(CASE WHEN paymentStatus = 'cancelled' THEN 1 END) AS cancelled_count,
                            COUNT(CASE WHEN paymentStatus = 'refunded' THEN 1 END) AS refunded_count,
                            COALESCE(SUM(CASE WHEN paymentStatus = 'completed' THEN totalCharge END), 0) AS total_revenue
                          FROM trip_payments";

                $this->db->query($query);
                $stats = $this->db->single();

                error_log("getPaymentStats: completed=" . $stats->completed_count . ", cancelled=" . $stats->cancelled_count . ", refunded=" . $stats->refunded_count . ", revenue=" . $stats->total_revenue);

                return $stats;

            } catch (PDOException $e) {
                error_log("Database error in getPaymentStats: " . $e->getMessage());
                return null;
            }
        }

        // Driver Payout Methods
        public function getCompletedDriverPayouts() {
            try {
                $query = "SELECT
                            dp.driverPaymentId AS id,
                            tp.tripId,
                            dp.driverCharge AS amount,
                            dp.pDateSite AS paymentDate,
                            tp.transactionId,
                            u.fullname AS driverName,
                            u.email AS driverEmail,
                            u.phone AS driverPhone,
                            dp.pDoneSite,
                            dp.createdAt
                          FROM driver_payments dp
                          JOIN trip_payments tp ON dp.wholePaymentId = tp.wholePaymentId
                          JOIN users u ON dp.driverId = u.id
                          WHERE dp.pDoneSite = 1 AND dp.refunded = 0
                          ORDER BY dp.pDateSite DESC";

                $this->db->query($query);
                $results = $this->db->resultSet();
                error_log("getCompletedDriverPayouts: found " . count($results) . " completed payouts");
                return $results;

            } catch (PDOException $e) {
                error_log("Database error in getCompletedDriverPayouts: " . $e->getMessage());
                return [];
            }
        }

        public function getPendingDriverPayouts() {
            try {
                $query = "SELECT
                            dp.driverPaymentId AS id,
                            tp.tripId,
                            dp.driverCharge AS amount,
                            dp.createdAt AS paymentDate,
                            tp.transactionId,
                            u.fullname AS driverName,
                            u.email AS driverEmail,
                            u.phone AS driverPhone,
                            dp.pDoneSite,
                            dp.createdAt
                          FROM driver_payments dp
                          JOIN trip_payments tp ON dp.wholePaymentId = tp.wholePaymentId
                          JOIN users u ON dp.driverId = u.id
                          WHERE dp.pDoneSite = 0 AND dp.refunded = 0
                          ORDER BY dp.createdAt DESC";

                $this->db->query($query);
                $results = $this->db->resultSet();
                error_log("getPendingDriverPayouts: found " . count($results) . " pending payouts");
                return $results;

            } catch (PDOException $e) {
                error_log("Database error in getPendingDriverPayouts: " . $e->getMessage());
                return [];
            }
        }

        public function getCancelledDriverPayouts() {
            try {
                $query = "SELECT
                            dp.driverPaymentId AS id,
                            tp.tripId,
                            dp.refundAmount AS amount,
                            dp.refundDate AS paymentDate,
                            tp.transactionId,
                            dp.refundReason AS reason,
                            u.fullname AS driverName,
                            u.email AS driverEmail,
                            u.phone AS driverPhone,
                            dp.refunded,
                            dp.createdAt
                          FROM driver_payments dp
                          JOIN trip_payments tp ON dp.wholePaymentId = tp.wholePaymentId
                          JOIN users u ON dp.driverId = u.id
                          WHERE dp.refunded = 1
                          ORDER BY dp.refundDate DESC";

                $this->db->query($query);
                $results = $this->db->resultSet();
                error_log("getCancelledDriverPayouts: found " . count($results) . " cancelled payouts");
                return $results;

            } catch (PDOException $e) {
                error_log("Database error in getCancelledDriverPayouts: " . $e->getMessage());
                return [];
            }
        }

        public function getDriverPayoutDetails($payoutId) {
            try {
                $query = "SELECT
                            dp.driverPaymentId,
                            tp.tripId,
                            dp.driverId,
                            dp.driverCharge,
                            dp.pDoneSite,
                            dp.pDateSite,
                            dp.refunded,
                            dp.refundDate,
                            dp.refundAmount,
                            dp.refundReason,
                            tp.transactionId,
                            dp.createdAt,
                            dp.updatedAt,
                            u.fullname AS driverName,
                            u.email AS driverEmail,
                            u.phone AS driverPhone,
                            u.address AS driverAddress
                          FROM driver_payments dp
                          JOIN trip_payments tp ON dp.wholePaymentId = tp.wholePaymentId
                          JOIN users u ON dp.driverId = u.id
                          WHERE dp.driverPaymentId = :payoutId";

                $this->db->query($query);
                $this->db->bind(':payoutId', $payoutId);
                $payout = $this->db->single();

                if ($payout) {
                    error_log("getDriverPayoutDetails: found payout details for payoutId=" . $payoutId);
                } else {
                    error_log("getDriverPayoutDetails: no payout found for payoutId=" . $payoutId);
                }

                return $payout;

            } catch (PDOException $e) {
                error_log("Database error in getDriverPayoutDetails: " . $e->getMessage());
                return null;
            }
        }

        public function processDriverPayout($driverPaymentId, $moderatorId) {
            try {
                $query = "UPDATE driver_payments
                          SET pDoneSite = 1,
                              pDateSite = NOW(),
                              updatedAt = NOW()
                          WHERE driverPaymentId = :driverPaymentId AND pDoneSite = 0";

                $this->db->query($query);
                $this->db->bind(':driverPaymentId', $driverPaymentId);

                $result = $this->db->execute();
                error_log("processDriverPayout: driverPaymentId=$driverPaymentId, result=" . ($result ? 'success' : 'failed'));

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in processDriverPayout: " . $e->getMessage());
                return false;
            }
        }

        public function refundDriverPayout($driverPaymentId, $moderatorId, $reason) {
            try {
                $query = "UPDATE driver_payments
                          SET refunded = 1,
                              refundDate = NOW(),
                              refundAmount = driverCharge,
                              refundReason = :reason,
                              updatedAt = NOW()
                          WHERE driverPaymentId = :driverPaymentId AND refunded = 0";

                $this->db->query($query);
                $this->db->bind(':driverPaymentId', $driverPaymentId);
                $this->db->bind(':reason', $reason);

                $result = $this->db->execute();
                error_log("refundDriverPayout: driverPaymentId=$driverPaymentId, result=" . ($result ? 'success' : 'failed'));

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in refundDriverPayout: " . $e->getMessage());
                return false;
            }
        }

        public function getDriverPayoutStats() {
            try {
                $query = "SELECT
                            COUNT(CASE WHEN pDoneSite = 1 AND refunded = 0 THEN 1 END) AS completed_count,
                            COUNT(CASE WHEN pDoneSite = 0 AND refunded = 0 THEN 1 END) AS pending_count,
                            COUNT(CASE WHEN refunded = 1 THEN 1 END) AS cancelled_count,
                            COALESCE(SUM(CASE WHEN pDoneSite = 1 AND refunded = 0 THEN driverCharge END), 0) AS total_payout_amount
                          FROM driver_payments";

                $this->db->query($query);
                $stats = $this->db->single();

                error_log("getDriverPayoutStats: completed=" . $stats->completed_count . ", pending=" . $stats->pending_count . ", cancelled=" . $stats->cancelled_count . ", total=" . $stats->total_payout_amount);

                return $stats;

            } catch (PDOException $e) {
                error_log("Database error in getDriverPayoutStats: " . $e->getMessage());
                return null;
            }
        }

        // Guide Payout Methods
        public function getCompletedGuidePayouts() {
            try {
                $query = "SELECT
                            gp.guidePaymentId AS id,
                            tp.tripId,
                            gp.guideCharge AS amount,
                            gp.pDateSite AS paymentDate,
                            tp.transactionId,
                            u.fullname AS guideName,
                            u.email AS guideEmail,
                            u.phone AS guidePhone,
                            gp.pDoneSite,
                            gp.createdAt
                          FROM guide_payments gp
                          JOIN trip_payments tp ON gp.wholePaymentId = tp.wholePaymentId
                          JOIN users u ON gp.guideId = u.id
                          WHERE gp.pDoneSite = 1 AND gp.refunded = 0
                          ORDER BY gp.pDateSite DESC";

                $this->db->query($query);
                $results = $this->db->resultSet();
                error_log("getCompletedGuidePayouts: found " . count($results) . " completed payouts");
                return $results;

            } catch (PDOException $e) {
                error_log("Database error in getCompletedGuidePayouts: " . $e->getMessage());
                return [];
            }
        }

        public function getPendingGuidePayouts() {
            try {
                $query = "SELECT
                            gp.guidePaymentId AS id,
                            tp.tripId,
                            gp.guideCharge AS amount,
                            gp.createdAt AS paymentDate,
                            tp.transactionId,
                            u.fullname AS guideName,
                            u.email AS guideEmail,
                            u.phone AS guidePhone,
                            gp.pDoneSite,
                            gp.createdAt
                          FROM guide_payments gp
                          JOIN trip_payments tp ON gp.wholePaymentId = tp.wholePaymentId
                          JOIN users u ON gp.guideId = u.id
                          WHERE gp.pDoneSite = 0 AND gp.refunded = 0
                          ORDER BY gp.createdAt DESC";

                $this->db->query($query);
                $results = $this->db->resultSet();
                error_log("getPendingGuidePayouts: found " . count($results) . " pending payouts");
                return $results;

            } catch (PDOException $e) {
                error_log("Database error in getPendingGuidePayouts: " . $e->getMessage());
                return [];
            }
        }

        public function getCancelledGuidePayouts() {
            try {
                $query = "SELECT
                            gp.guidePaymentId AS id,
                            tp.tripId,
                            gp.refundAmount AS amount,
                            gp.refundDate AS paymentDate,
                            tp.transactionId,
                            gp.refundReason AS reason,
                            u.fullname AS guideName,
                            u.email AS guideEmail,
                            u.phone AS guidePhone,
                            gp.refunded,
                            gp.createdAt
                          FROM guide_payments gp
                          JOIN trip_payments tp ON gp.wholePaymentId = tp.wholePaymentId
                          JOIN users u ON gp.guideId = u.id
                          WHERE gp.refunded = 1
                          ORDER BY gp.refundDate DESC";

                $this->db->query($query);
                $results = $this->db->resultSet();
                error_log("getCancelledGuidePayouts: found " . count($results) . " cancelled payouts");
                return $results;

            } catch (PDOException $e) {
                error_log("Database error in getCancelledGuidePayouts: " . $e->getMessage());
                return [];
            }
        }

        public function getGuidePayoutDetails($payoutId) {
            try {
                $query = "SELECT
                            gp.guidePaymentId,
                            tp.tripId,
                            gp.guideId,
                            gp.guideCharge,
                            gp.pDoneSite,
                            gp.pDateSite,
                            gp.refunded,
                            gp.refundDate,
                            gp.refundAmount,
                            gp.refundReason,
                            tp.transactionId,
                            gp.createdAt,
                            gp.updatedAt,
                            u.fullname AS guideName,
                            u.email AS guideEmail,
                            u.phone AS guidePhone,
                            u.address AS guideAddress
                          FROM guide_payments gp
                          JOIN trip_payments tp ON gp.wholePaymentId = tp.wholePaymentId
                          JOIN users u ON gp.guideId = u.id
                          WHERE gp.guidePaymentId = :payoutId";

                $this->db->query($query);
                $this->db->bind(':payoutId', $payoutId);
                $payout = $this->db->single();

                if ($payout) {
                    error_log("getGuidePayoutDetails: found payout details for payoutId=" . $payoutId);
                } else {
                    error_log("getGuidePayoutDetails: no payout found for payoutId=" . $payoutId);
                }

                return $payout;

            } catch (PDOException $e) {
                error_log("Database error in getGuidePayoutDetails: " . $e->getMessage());
                return null;
            }
        }

        public function processGuidePayout($guidePaymentId, $moderatorId) {
            try {
                $query = "UPDATE guide_payments
                          SET pDoneSite = 1,
                              pDateSite = NOW(),
                              updatedAt = NOW()
                          WHERE guidePaymentId = :guidePaymentId AND pDoneSite = 0";

                $this->db->query($query);
                $this->db->bind(':guidePaymentId', $guidePaymentId);

                $result = $this->db->execute();
                error_log("processGuidePayout: guidePaymentId=$guidePaymentId, result=" . ($result ? 'success' : 'failed'));

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in processGuidePayout: " . $e->getMessage());
                return false;
            }
        }

        public function refundGuidePayout($guidePaymentId, $moderatorId, $reason) {
            try {
                $query = "UPDATE guide_payments
                          SET refunded = 1,
                              refundDate = NOW(),
                              refundAmount = guideCharge,
                              refundReason = :reason,
                              updatedAt = NOW()
                          WHERE guidePaymentId = :guidePaymentId AND refunded = 0";

                $this->db->query($query);
                $this->db->bind(':guidePaymentId', $guidePaymentId);
                $this->db->bind(':reason', $reason);

                $result = $this->db->execute();
                error_log("refundGuidePayout: guidePaymentId=$guidePaymentId, result=" . ($result ? 'success' : 'failed'));

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in refundGuidePayout: " . $e->getMessage());
                return false;
            }
        }

        public function getGuidePayoutStats() {
            try {
                $query = "SELECT
                            COUNT(CASE WHEN pDoneSite = 1 AND refunded = 0 THEN 1 END) AS completed_count,
                            COUNT(CASE WHEN pDoneSite = 0 AND refunded = 0 THEN 1 END) AS pending_count,
                            COUNT(CASE WHEN refunded = 1 THEN 1 END) AS cancelled_count,
                            COALESCE(SUM(CASE WHEN pDoneSite = 1 AND refunded = 0 THEN guideCharge END), 0) AS total_payout_amount
                          FROM guide_payments";

                $this->db->query($query);
                $stats = $this->db->single();

                error_log("getGuidePayoutStats: completed=" . $stats->completed_count . ", pending=" . $stats->pending_count . ", cancelled=" . $stats->cancelled_count . ", total=" . $stats->total_payout_amount);

                return $stats;

            } catch (PDOException $e) {
                error_log("Database error in getGuidePayoutStats: " . $e->getMessage());
                return null;
            }
        }

        // Service Charge Management Methods
        public function getCurrentServiceCharges() {
            try {
                $query = "SELECT
                            chargeId,
                            moderatorId,
                            dBookCharge,
                            gBookCharge,
                            siteServiceCharge,
                            isActive,
                            notes,
                            createdAt,
                            updatedAt
                          FROM site_service_charges
                          WHERE isActive = 1
                          ORDER BY createdAt DESC
                          LIMIT 1";

                $this->db->query($query);
                $result = $this->db->single();

                if ($result) {
                    return [
                        'chargeId' => $result->chargeId,
                        'moderatorId' => $result->moderatorId,
                        'driverBookingCharge' => (float)$result->dBookCharge,
                        'guideBookingCharge' => (float)$result->gBookCharge,
                        'siteServiceCharge' => (float)$result->siteServiceCharge,
                        'isActive' => (bool)$result->isActive,
                        'notes' => $result->notes,
                        'lastUpdated' => $result->updatedAt
                    ];
                } else {
                    // Return default values if no active charges found
                    return [
                        'chargeId' => null,
                        'moderatorId' => null,
                        'driverBookingCharge' => 0,
                        'guideBookingCharge' => 0,
                        'siteServiceCharge' => 0,
                        'isActive' => false,
                        'notes' => null,
                        'lastUpdated' => null
                    ];
                }

            } catch (PDOException $e) {
                error_log("Database error in getCurrentServiceCharges: " . $e->getMessage());
                return [
                    'chargeId' => null,
                    'moderatorId' => null,
                    'driverBookingCharge' => 0,
                    'guideBookingCharge' => 0,
                    'siteServiceCharge' => 0,
                    'isActive' => false,
                    'notes' => null,
                    'lastUpdated' => null
                ];
            }
        }

        public function setServiceCharges($chargeData, $moderatorId) {
            try {
                // First, set all existing charges to inactive
                $deactivateQuery = "UPDATE site_service_charges SET isActive = 0 WHERE isActive = 1";
                $this->db->query($deactivateQuery);
                $this->db->execute();

                // Then insert the new charges
                $insertQuery = "INSERT INTO site_service_charges (
                    moderatorId,
                    dBookCharge,
                    gBookCharge,
                    siteServiceCharge,
                    isActive,
                    notes,
                    createdAt,
                    updatedAt
                ) VALUES (
                    :moderatorId,
                    :driverBookingCharge,
                    :guideBookingCharge,
                    :siteServiceCharge,
                    1,
                    :notes,
                    NOW(),
                    NOW()
                )";

                $this->db->query($insertQuery);
                $this->db->bind(':moderatorId', $moderatorId);
                $this->db->bind(':driverBookingCharge', $chargeData['driverBookingCharge']);
                $this->db->bind(':guideBookingCharge', $chargeData['guideBookingCharge']);
                $this->db->bind(':siteServiceCharge', $chargeData['siteServiceCharge']);
                $this->db->bind(':notes', $chargeData['chargeNotes'] ?: null);

                $result = $this->db->execute();
                error_log("setServiceCharges: result=" . ($result ? 'success' : 'failed'));

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in setServiceCharges: " . $e->getMessage());
                return false;
            }
        }

        public function getServiceChargeHistory() {
            try {
                $query = "SELECT
                            ssc.chargeId,
                            ssc.moderatorId,
                            ssc.dBookCharge,
                            ssc.gBookCharge,
                            ssc.siteServiceCharge,
                            ssc.isActive,
                            ssc.notes,
                            ssc.createdAt,
                            ssc.updatedAt,
                            u.fullname AS moderatorName,
                            u.email AS moderatorEmail
                          FROM site_service_charges ssc
                          LEFT JOIN users u ON ssc.moderatorId = u.id
                          ORDER BY ssc.createdAt DESC";

                $this->db->query($query);
                $results = $this->db->resultSet();
                error_log("getServiceChargeHistory: found " . count($results) . " charge records");

                return $results;

            } catch (PDOException $e) {
                error_log("Database error in getServiceChargeHistory: " . $e->getMessage());
                return [];
            }
        }

        public function getServiceChargeById($chargeId) {
            try {
                $query = "SELECT
                            ssc.chargeId,
                            ssc.moderatorId,
                            ssc.dBookCharge,
                            ssc.gBookCharge,
                            ssc.siteServiceCharge,
                            ssc.isActive,
                            ssc.notes,
                            ssc.createdAt,
                            ssc.updatedAt,
                            u.fullname AS moderatorName,
                            u.email AS moderatorEmail
                          FROM site_service_charges ssc
                          LEFT JOIN users u ON ssc.moderatorId = u.id
                          WHERE ssc.chargeId = :chargeId";

                $this->db->query($query);
                $this->db->bind(':chargeId', $chargeId);
                $result = $this->db->single();

                if ($result) {
                    error_log("getServiceChargeById: found charge with ID " . $chargeId);
                } else {
                    error_log("getServiceChargeById: no charge found with ID " . $chargeId);
                }

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in getServiceChargeById: " . $e->getMessage());
                return null;
            }
        }

        public function updateServiceCharge($chargeId, $chargeData, $moderatorId) {
            try {
                $query = "UPDATE site_service_charges SET
                            dBookCharge = :driverBookingCharge,
                            gBookCharge = :guideBookingCharge,
                            siteServiceCharge = :siteServiceCharge,
                            notes = :notes,
                            updatedAt = NOW()
                          WHERE chargeId = :chargeId";

                $this->db->query($query);
                $this->db->bind(':chargeId', $chargeId);
                $this->db->bind(':driverBookingCharge', $chargeData['driverBookingCharge']);
                $this->db->bind(':guideBookingCharge', $chargeData['guideBookingCharge']);
                $this->db->bind(':siteServiceCharge', $chargeData['siteServiceCharge']);
                $this->db->bind(':notes', $chargeData['chargeNotes'] ?: null);

                $result = $this->db->execute();
                error_log("updateServiceCharge: chargeId=$chargeId, result=" . ($result ? 'success' : 'failed'));

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in updateServiceCharge: " . $e->getMessage());
                return false;
            }
        }

        public function deactivateServiceCharge($chargeId, $moderatorId) {
            try {
                $query = "UPDATE site_service_charges SET
                            isActive = 0,
                            updatedAt = NOW()
                          WHERE chargeId = :chargeId";

                $this->db->query($query);
                $this->db->bind(':chargeId', $chargeId);

                $result = $this->db->execute();
                error_log("deactivateServiceCharge: chargeId=$chargeId, result=" . ($result ? 'success' : 'failed'));

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in deactivateServiceCharge: " . $e->getMessage());
                return false;
            }
        }

        // Earnings Analytics Methods
        public function getEarningsMetrics($startDate, $endDate, $viewType) {
            try {
                // Get the latest active site service charges
                $siteCharges = $this->getLatestSiteServiceCharges();

                // Calculate total revenue from trip_payments
                $totalRevenueQuery = "SELECT SUM(tp.totalCharge) as totalRevenue
                                    FROM trip_payments tp
                                    WHERE tp.createdAt BETWEEN :startDate AND :endDate
                                    AND tp.paymentStatus = 'completed'";

                $this->db->query($totalRevenueQuery);
                $this->db->bind(':startDate', $startDate);
                $this->db->bind(':endDate', $endDate);
                $totalRevenueResult = $this->db->single();
                $totalRevenue = (float)($totalRevenueResult->totalRevenue ?? 0);

                // Calculate site profit based on actual service charges
                $siteProfit = $this->calculateSiteProfit($startDate, $endDate, $siteCharges);

                // Calculate driver revenue from driver_payments
                $driverRevenueQuery = "SELECT SUM(dp.driverCharge) as driverRevenue
                                     FROM driver_payments dp
                                     INNER JOIN trip_payments tp ON dp.wholePaymentId = tp.wholePaymentId
                                     WHERE tp.createdAt BETWEEN :startDate AND :endDate
                                     AND tp.paymentStatus = 'completed'
                                     AND dp.pDoneTraveller = TRUE";

                $this->db->query($driverRevenueQuery);
                $this->db->bind(':startDate', $startDate);
                $this->db->bind(':endDate', $endDate);
                $driverRevenueResult = $this->db->single();
                $driverRevenue = (float)($driverRevenueResult->driverRevenue ?? 0);

                // Calculate guide revenue from guide_payments
                $guideRevenueQuery = "SELECT SUM(gp.guideCharge) as guideRevenue
                                    FROM guide_payments gp
                                    INNER JOIN trip_payments tp ON gp.wholePaymentId = tp.wholePaymentId
                                    WHERE tp.createdAt BETWEEN :startDate AND :endDate
                                    AND tp.paymentStatus = 'completed'
                                    AND gp.pDoneTraveller = TRUE";

                $this->db->query($guideRevenueQuery);
                $this->db->bind(':startDate', $startDate);
                $this->db->bind(':endDate', $endDate);
                $guideRevenueResult = $this->db->single();
                $guideRevenue = (float)($guideRevenueResult->guideRevenue ?? 0);

                return [
                    'totalRevenue' => $totalRevenue,
                    'siteProfit' => $siteProfit,
                    'driverRevenue' => $driverRevenue,
                    'guideRevenue' => $guideRevenue
                ];

            } catch (PDOException $e) {
                error_log("Database error in getEarningsMetrics: " . $e->getMessage());
                return [
                    'totalRevenue' => 0,
                    'siteProfit' => 0,
                    'driverRevenue' => 0,
                    'guideRevenue' => 0
                ];
            }
        }

        public function getRevenueTrend($startDate, $endDate, $viewType) {
            try {
                $groupBy = $this->getGroupByClause($viewType);
                $dateFormat = $this->getDateFormat($viewType);

                // Get the latest active site service charges
                $siteCharges = $this->getLatestSiteServiceCharges();

                // Get total revenue trend
                $revenueQuery = "SELECT
                    DATE_FORMAT(tp.createdAt, '$dateFormat') as period,
                    SUM(tp.totalCharge) as totalRevenue
                FROM trip_payments tp
                WHERE tp.createdAt BETWEEN :startDate AND :endDate
                AND tp.paymentStatus = 'completed'
                GROUP BY period
                ORDER BY period ASC";

                $this->db->query($revenueQuery);
                $this->db->bind(':startDate', $startDate);
                $this->db->bind(':endDate', $endDate);
                $revenueResults = $this->db->resultSet();

                // Get driver revenue trend
                $driverQuery = "SELECT
                    DATE_FORMAT(tp.createdAt, '$dateFormat') as period,
                    SUM(dp.driverCharge) as driverRevenue
                FROM driver_payments dp
                INNER JOIN trip_payments tp ON dp.wholePaymentId = tp.wholePaymentId
                WHERE tp.createdAt BETWEEN :startDate AND :endDate
                AND tp.paymentStatus = 'completed'
                AND dp.pDoneTraveller = TRUE
                GROUP BY period
                ORDER BY period ASC";

                $this->db->query($driverQuery);
                $this->db->bind(':startDate', $startDate);
                $this->db->bind(':endDate', $endDate);
                $driverResults = $this->db->resultSet();

                // Get guide revenue trend
                $guideQuery = "SELECT
                    DATE_FORMAT(tp.createdAt, '$dateFormat') as period,
                    SUM(gp.guideCharge) as guideRevenue
                FROM guide_payments gp
                INNER JOIN trip_payments tp ON gp.wholePaymentId = tp.wholePaymentId
                WHERE tp.createdAt BETWEEN :startDate AND :endDate
                AND tp.paymentStatus = 'completed'
                AND gp.pDoneTraveller = TRUE
                GROUP BY period
                ORDER BY period ASC";

                $this->db->query($guideQuery);
                $this->db->bind(':startDate', $startDate);
                $this->db->bind(':endDate', $endDate);
                $guideResults = $this->db->resultSet();

                // Get site profit trend by calculating for each period
                $siteProfitQuery = "SELECT
                    DATE_FORMAT(tp.createdAt, '$dateFormat') as period,
                    tp.wholePaymentId,
                    tp.totalCharge,
                    CASE WHEN dp.driverPaymentId IS NOT NULL THEN 1 ELSE 0 END as hasDriver,
                    CASE WHEN gp.guidePaymentId IS NOT NULL THEN 1 ELSE 0 END as hasGuide
                FROM trip_payments tp
                LEFT JOIN driver_payments dp ON tp.wholePaymentId = dp.wholePaymentId AND dp.pDoneTraveller = TRUE
                LEFT JOIN guide_payments gp ON tp.wholePaymentId = gp.wholePaymentId AND gp.pDoneTraveller = TRUE
                WHERE tp.createdAt BETWEEN :startDate AND :endDate
                AND tp.paymentStatus = 'completed'
                ORDER BY period ASC";

                $this->db->query($siteProfitQuery);
                $this->db->bind(':startDate', $startDate);
                $this->db->bind(':endDate', $endDate);
                $siteProfitRaw = $this->db->resultSet();

                // Calculate site profit for each period
                $siteProfitMap = [];
                foreach ($siteProfitRaw as $row) {
                    $period = $row->period;
                    if (!isset($siteProfitMap[$period])) {
                        $siteProfitMap[$period] = 0;
                    }

                    // Calculate site profit for this trip
                    $tripProfit = 0;

                    // Add fixed charges
                    if ($row->hasDriver) {
                        $tripProfit += $siteCharges['dBookCharge'];
                    }
                    if ($row->hasGuide) {
                        $tripProfit += $siteCharges['gBookCharge'];
                    }

                    // Add percentage charge
                    $tripProfit += $row->totalCharge * ($siteCharges['siteServiceCharge'] / 100);

                    $siteProfitMap[$period] += $tripProfit;
                }

                // Combine results
                $periods = [];
                $totalRevenue = [];
                $driverRevenue = [];
                $guideRevenue = [];
                $siteProfit = [];

                // Get all unique periods
                $allPeriods = array_unique(array_merge(
                    array_column($revenueResults, 'period'),
                    array_column($driverResults, 'period'),
                    array_column($guideResults, 'period'),
                    array_keys($siteProfitMap)
                ));
                sort($allPeriods);

                // Create maps for quick lookup
                $revenueMap = [];
                $driverMap = [];
                $guideMap = [];

                foreach ($revenueResults as $row) {
                    $revenueMap[$row->period] = (float)$row->totalRevenue;
                }
                foreach ($driverResults as $row) {
                    $driverMap[$row->period] = (float)$row->driverRevenue;
                }
                foreach ($guideResults as $row) {
                    $guideMap[$row->period] = (float)$row->guideRevenue;
                }

                foreach ($allPeriods as $period) {
                    $periods[] = $period;
                    $totalRevenue[] = $revenueMap[$period] ?? 0;
                    $driverRevenue[] = $driverMap[$period] ?? 0;
                    $guideRevenue[] = $guideMap[$period] ?? 0;
                    $siteProfit[] = $siteProfitMap[$period] ?? 0;
                }

                return [
                    'labels' => $periods,
                    'totalRevenue' => $totalRevenue,
                    'siteProfit' => $siteProfit,
                    'driverRevenue' => $driverRevenue,
                    'guideRevenue' => $guideRevenue
                ];

            } catch (PDOException $e) {
                error_log("Database error in getRevenueTrend: " . $e->getMessage());
                return [
                    'labels' => [],
                    'totalRevenue' => [],
                    'siteProfit' => [],
                    'driverRevenue' => [],
                    'guideRevenue' => []
                ];
            }
        }

        public function getRevenueBreakdown($startDate, $endDate) {
            try {
                // Get the latest active site service charges
                $siteCharges = $this->getLatestSiteServiceCharges();

                // Calculate site profit based on actual service charges
                $siteProfit = $this->calculateSiteProfit($startDate, $endDate, $siteCharges);

                // Calculate driver revenue
                $driverRevenueQuery = "SELECT SUM(dp.driverCharge) as driverRevenue
                                     FROM driver_payments dp
                                     INNER JOIN trip_payments tp ON dp.wholePaymentId = tp.wholePaymentId
                                     WHERE tp.createdAt BETWEEN :startDate AND :endDate
                                     AND tp.paymentStatus = 'completed'
                                     AND dp.pDoneTraveller = TRUE";

                $this->db->query($driverRevenueQuery);
                $this->db->bind(':startDate', $startDate);
                $this->db->bind(':endDate', $endDate);
                $driverRevenueResult = $this->db->single();
                $driverRevenue = (float)($driverRevenueResult->driverRevenue ?? 0);

                // Calculate guide revenue
                $guideRevenueQuery = "SELECT SUM(gp.guideCharge) as guideRevenue
                                    FROM guide_payments gp
                                    INNER JOIN trip_payments tp ON gp.wholePaymentId = tp.wholePaymentId
                                    WHERE tp.createdAt BETWEEN :startDate AND :endDate
                                    AND tp.paymentStatus = 'completed'
                                    AND gp.pDoneTraveller = TRUE";

                $this->db->query($guideRevenueQuery);
                $this->db->bind(':startDate', $startDate);
                $this->db->bind(':endDate', $endDate);
                $guideRevenueResult = $this->db->single();
                $guideRevenue = (float)($guideRevenueResult->guideRevenue ?? 0);

                return [
                    'siteProfit' => $siteProfit,
                    'driverRevenue' => $driverRevenue,
                    'guideRevenue' => $guideRevenue
                ];

            } catch (PDOException $e) {
                error_log("Database error in getRevenueBreakdown: " . $e->getMessage());
                return [
                    'siteProfit' => 0,
                    'driverRevenue' => 0,
                    'guideRevenue' => 0
                ];
            }
        }

        public function getEarningsTable($startDate, $endDate, $limit, $offset, $status = 'all') {
            try {
                $statusCondition = $status !== 'all' ? "AND tp.paymentStatus = :status" : "";

                // Get the latest active site service charges
                $siteCharges = $this->getLatestSiteServiceCharges();

                $query = "SELECT
                    tp.tripId,
                    tp.totalCharge as totalRevenue,
                    COALESCE(dp.driverCharge, 0) as driverCharge,
                    COALESCE(gp.guideCharge, 0) as guideCharge,
                    CASE
                        WHEN dp.driverPaymentId IS NOT NULL AND gp.guidePaymentId IS NOT NULL THEN
                            {$siteCharges['dBookCharge']} + {$siteCharges['gBookCharge']} + (tp.totalCharge * {$siteCharges['siteServiceCharge']} / 100)
                        WHEN dp.driverPaymentId IS NOT NULL THEN
                            {$siteCharges['dBookCharge']} + (tp.totalCharge * {$siteCharges['siteServiceCharge']} / 100)
                        WHEN gp.guidePaymentId IS NOT NULL THEN
                            {$siteCharges['gBookCharge']} + (tp.totalCharge * {$siteCharges['siteServiceCharge']} / 100)
                        ELSE
                            (tp.totalCharge * {$siteCharges['siteServiceCharge']} / 100)
                    END as siteCharge,
                    CASE
                        WHEN dp.driverPaymentId IS NOT NULL AND gp.guidePaymentId IS NOT NULL THEN
                            {$siteCharges['dBookCharge']} + {$siteCharges['gBookCharge']} + (tp.totalCharge * {$siteCharges['siteServiceCharge']} / 100)
                        WHEN dp.driverPaymentId IS NOT NULL THEN
                            {$siteCharges['dBookCharge']} + (tp.totalCharge * {$siteCharges['siteServiceCharge']} / 100)
                        WHEN gp.guidePaymentId IS NOT NULL THEN
                            {$siteCharges['gBookCharge']} + (tp.totalCharge * {$siteCharges['siteServiceCharge']} / 100)
                        ELSE
                            (tp.totalCharge * {$siteCharges['siteServiceCharge']} / 100)
                    END as siteProfit,
                    tp.paymentStatus as status,
                    tp.createdAt,
                    ct.tripTitle
                FROM trip_payments tp
                LEFT JOIN created_trips ct ON tp.tripId = ct.tripId
                LEFT JOIN (
                    SELECT wholePaymentId, SUM(driverCharge) as driverCharge, MIN(driverPaymentId) as driverPaymentId
                    FROM driver_payments
                    WHERE pDoneTraveller = TRUE
                    GROUP BY wholePaymentId
                ) dp ON tp.wholePaymentId = dp.wholePaymentId
                LEFT JOIN (
                    SELECT wholePaymentId, SUM(guideCharge) as guideCharge, MIN(guidePaymentId) as guidePaymentId
                    FROM guide_payments
                    WHERE pDoneTraveller = TRUE
                    GROUP BY wholePaymentId
                ) gp ON tp.wholePaymentId = gp.wholePaymentId
                WHERE tp.createdAt BETWEEN :startDate AND :endDate
                $statusCondition
                ORDER BY tp.createdAt DESC
                LIMIT :limit OFFSET :offset";

                $this->db->query($query);
                $this->db->bind(':startDate', $startDate);
                $this->db->bind(':endDate', $endDate);
                $this->db->bind(':limit', $limit);
                $this->db->bind(':offset', $offset);

                if ($status !== 'all') {
                    $this->db->bind(':status', $status);
                }

                $earnings = $this->db->resultSet();

                // Get total count for pagination
                $countQuery = "SELECT COUNT(*) as total FROM trip_payments tp
                              WHERE tp.createdAt BETWEEN :startDate AND :endDate
                              $statusCondition";

                $this->db->query($countQuery);
                $this->db->bind(':startDate', $startDate);
                $this->db->bind(':endDate', $endDate);

                if ($status !== 'all') {
                    $this->db->bind(':status', $status);
                }

                $totalResult = $this->db->single();
                $total = $totalResult->total ?? 0;

                // Format the earnings data
                $formattedEarnings = [];
                foreach ($earnings as $earning) {
                    $formattedEarnings[] = [
                        'tripId' => $earning->tripId,
                        'tripTitle' => $earning->tripTitle ?? 'N/A',
                        'totalRevenue' => (float)$earning->totalRevenue,
                        'driverCharge' => (float)$earning->driverCharge,
                        'guideCharge' => (float)$earning->guideCharge,
                        'siteCharge' => (float)$earning->siteCharge,
                        'siteProfit' => (float)$earning->siteProfit,
                        'status' => $earning->status,
                        'createdAt' => $earning->createdAt
                    ];
                }

                return [
                    'earnings' => $formattedEarnings,
                    'total' => $total
                ];

            } catch (PDOException $e) {
                error_log("Database error in getEarningsTable: " . $e->getMessage());
                return [
                    'earnings' => [],
                    'total' => 0
                ];
            }
        }

        public function getPayoutAnalysisMetrics($startDate, $endDate) {
            try {
                $query = "SELECT
                            COALESCE((
                                SELECT SUM(dp.driverCharge)
                                FROM driver_payments dp
                                WHERE dp.pDoneSite = 1
                                AND dp.refunded = 0
                                AND COALESCE(dp.pDateSite, dp.updatedAt, dp.createdAt) BETWEEN :startDate AND :endDate
                            ), 0) AS driverPaid,
                            COALESCE((
                                SELECT SUM(gp.guideCharge)
                                FROM guide_payments gp
                                WHERE gp.pDoneSite = 1
                                AND gp.refunded = 0
                                AND COALESCE(gp.pDateSite, gp.updatedAt, gp.createdAt) BETWEEN :startDate AND :endDate
                            ), 0) AS guidePaid,
                            COALESCE((
                                SELECT SUM(COALESCE(dp.refundAmount, dp.driverCharge))
                                FROM driver_payments dp
                                WHERE dp.refunded = 1
                                AND COALESCE(dp.refundDate, dp.updatedAt) BETWEEN :startDate AND :endDate
                            ), 0) +
                            COALESCE((
                                SELECT SUM(COALESCE(gp.refundAmount, gp.guideCharge))
                                FROM guide_payments gp
                                WHERE gp.refunded = 1
                                AND COALESCE(gp.refundDate, gp.updatedAt) BETWEEN :startDate AND :endDate
                            ), 0) AS refundedAmount,
                            COALESCE((
                                SELECT COUNT(*)
                                FROM driver_payments dp
                                WHERE dp.pDoneSite = 0
                                AND dp.refunded = 0
                                AND dp.createdAt BETWEEN :startDate AND :endDate
                            ), 0) +
                            COALESCE((
                                SELECT COUNT(*)
                                FROM guide_payments gp
                                WHERE gp.pDoneSite = 0
                                AND gp.refunded = 0
                                AND gp.createdAt BETWEEN :startDate AND :endDate
                            ), 0) AS pendingCount";

                $this->db->query($query);
                $this->db->bind(':startDate', $startDate);
                $this->db->bind(':endDate', $endDate);

                $result = $this->db->single();

                $driverPaid = (float)($result->driverPaid ?? 0);
                $guidePaid = (float)($result->guidePaid ?? 0);
                $refundedAmount = (float)($result->refundedAmount ?? 0);

                return [
                    'totalPaid' => $driverPaid + $guidePaid,
                    'driverPaid' => $driverPaid,
                    'guidePaid' => $guidePaid,
                    'refundedAmount' => $refundedAmount,
                    'pendingCount' => (int)($result->pendingCount ?? 0)
                ];

            } catch (PDOException $e) {
                error_log('Database error in getPayoutAnalysisMetrics: ' . $e->getMessage());
                return [
                    'totalPaid' => 0,
                    'driverPaid' => 0,
                    'guidePaid' => 0,
                    'refundedAmount' => 0,
                    'pendingCount' => 0
                ];
            }
        }

        public function getPayoutTrendData($startDate, $endDate, $viewType) {
            try {
                $dateFormat = $this->getDateFormat($viewType);

                                $query = "SELECT period,
                                                                 SUM(driverPaid) AS driverPaid,
                                                                 SUM(guidePaid) AS guidePaid
                          FROM (
                                                            SELECT DATE_FORMAT(COALESCE(dp.pDateSite, dp.updatedAt, dp.createdAt), '$dateFormat') AS period,
                                     SUM(dp.driverCharge) AS driverPaid,
                                     0 AS guidePaid
                              FROM driver_payments dp
                              WHERE dp.pDoneSite = 1
                                AND dp.refunded = 0
                                                                AND COALESCE(dp.pDateSite, dp.updatedAt, dp.createdAt) BETWEEN :startDate AND :endDate
                                                            GROUP BY DATE_FORMAT(COALESCE(dp.pDateSite, dp.updatedAt, dp.createdAt), '$dateFormat')

                              UNION ALL

                                                            SELECT DATE_FORMAT(COALESCE(gp.pDateSite, gp.updatedAt, gp.createdAt), '$dateFormat') AS period,
                                     0 AS driverPaid,
                                     SUM(gp.guideCharge) AS guidePaid
                              FROM guide_payments gp
                              WHERE gp.pDoneSite = 1
                                AND gp.refunded = 0
                                                                AND COALESCE(gp.pDateSite, gp.updatedAt, gp.createdAt) BETWEEN :startDate AND :endDate
                                                            GROUP BY DATE_FORMAT(COALESCE(gp.pDateSite, gp.updatedAt, gp.createdAt), '$dateFormat')
                          ) payout_union
                          GROUP BY period
                          ORDER BY period ASC";

                $this->db->query($query);
                $this->db->bind(':startDate', $startDate);
                $this->db->bind(':endDate', $endDate);
                $rows = $this->db->resultSet();

                $labels = [];
                $driverPaid = [];
                $guidePaid = [];
                $totalPaid = [];

                foreach ($rows as $row) {
                    $driverValue = (float)$row->driverPaid;
                    $guideValue = (float)$row->guidePaid;

                    $labels[] = $row->period;
                    $driverPaid[] = $driverValue;
                    $guidePaid[] = $guideValue;
                    $totalPaid[] = $driverValue + $guideValue;
                }

                return [
                    'labels' => $labels,
                    'driverPaid' => $driverPaid,
                    'guidePaid' => $guidePaid,
                    'totalPaid' => $totalPaid
                ];

            } catch (PDOException $e) {
                error_log('Database error in getPayoutTrendData: ' . $e->getMessage());
                return [
                    'labels' => [],
                    'driverPaid' => [],
                    'guidePaid' => [],
                    'totalPaid' => []
                ];
            }
        }

        public function getPayoutTypeBreakdown($startDate, $endDate) {
            try {
                $metrics = $this->getPayoutAnalysisMetrics($startDate, $endDate);

                return [
                    'driverPaid' => (float)$metrics['driverPaid'],
                    'guidePaid' => (float)$metrics['guidePaid'],
                    'refundedAmount' => (float)$metrics['refundedAmount']
                ];

            } catch (PDOException $e) {
                error_log('Database error in getPayoutTypeBreakdown: ' . $e->getMessage());
                return [
                    'driverPaid' => 0,
                    'guidePaid' => 0,
                    'refundedAmount' => 0
                ];
            }
        }

        public function getPayoutUserProfile($userId) {
            try {
                $query = "SELECT id, fullname, email, phone, account_type, profile_photo
                          FROM users
                          WHERE id = :userId
                            AND account_type IN ('driver', 'guide')";

                $this->db->query($query);
                $this->db->bind(':userId', $userId);

                return $this->db->single();

            } catch (PDOException $e) {
                error_log('Database error in getPayoutUserProfile: ' . $e->getMessage());
                return null;
            }
        }

        public function getUserPayoutSummary($userId, $accountType, $startDate, $endDate) {
            try {
                $tableName = $accountType === 'driver' ? 'driver_payments' : 'guide_payments';
                $idField = $accountType === 'driver' ? 'driverId' : 'guideId';
                $chargeField = $accountType === 'driver' ? 'driverCharge' : 'guideCharge';

                $query = "SELECT
                            COALESCE(SUM(CASE
                                WHEN pDoneSite = 1 AND refunded = 0 AND COALESCE(pDateSite, updatedAt, createdAt) BETWEEN :startDate AND :endDate
                                THEN $chargeField END), 0) AS totalPaid,
                            COUNT(CASE
                                WHEN pDoneSite = 1 AND refunded = 0 AND COALESCE(pDateSite, updatedAt, createdAt) BETWEEN :startDate AND :endDate
                                THEN 1 END) AS completedCount,
                            COUNT(CASE
                                WHEN pDoneSite = 0 AND refunded = 0 AND createdAt BETWEEN :startDate AND :endDate
                                THEN 1 END) AS pendingCount,
                            COALESCE(SUM(CASE
                                WHEN refunded = 1 AND COALESCE(refundDate, updatedAt) BETWEEN :startDate AND :endDate
                                THEN COALESCE(refundAmount, $chargeField) END), 0) AS refundedAmount,
                            COUNT(CASE
                                WHEN refunded = 1 AND COALESCE(refundDate, updatedAt) BETWEEN :startDate AND :endDate
                                THEN 1 END) AS refundedCount
                          FROM $tableName
                          WHERE $idField = :userId";

                $this->db->query($query);
                $this->db->bind(':startDate', $startDate);
                $this->db->bind(':endDate', $endDate);
                $this->db->bind(':userId', $userId);

                $result = $this->db->single();

                return [
                    'totalPaid' => (float)($result->totalPaid ?? 0),
                    'completedCount' => (int)($result->completedCount ?? 0),
                    'pendingCount' => (int)($result->pendingCount ?? 0),
                    'refundedAmount' => (float)($result->refundedAmount ?? 0),
                    'refundedCount' => (int)($result->refundedCount ?? 0)
                ];

            } catch (PDOException $e) {
                error_log('Database error in getUserPayoutSummary: ' . $e->getMessage());
                return [
                    'totalPaid' => 0,
                    'completedCount' => 0,
                    'pendingCount' => 0,
                    'refundedAmount' => 0,
                    'refundedCount' => 0
                ];
            }
        }

        public function getUserPayoutTrendData($userId, $accountType, $startDate, $endDate, $viewType) {
            try {
                $tableName = $accountType === 'driver' ? 'driver_payments' : 'guide_payments';
                $idField = $accountType === 'driver' ? 'driverId' : 'guideId';
                $chargeField = $accountType === 'driver' ? 'driverCharge' : 'guideCharge';
                $dateFormat = $this->getDateFormat($viewType);

                                $query = "SELECT
                                                        DATE_FORMAT(COALESCE(pDateSite, updatedAt, createdAt), '$dateFormat') AS period,
                                                        SUM($chargeField) AS paidAmount
                                                    FROM $tableName
                                                    WHERE $idField = :userId
                                                        AND pDoneSite = 1
                                                        AND refunded = 0
                                                        AND COALESCE(pDateSite, updatedAt, createdAt) BETWEEN :startDate AND :endDate
                                                    GROUP BY DATE_FORMAT(COALESCE(pDateSite, updatedAt, createdAt), '$dateFormat')
                                                    ORDER BY period ASC";

                $this->db->query($query);
                $this->db->bind(':userId', $userId);
                $this->db->bind(':startDate', $startDate);
                $this->db->bind(':endDate', $endDate);

                $rows = $this->db->resultSet();
                $labels = [];
                $paidAmounts = [];

                foreach ($rows as $row) {
                    $labels[] = $row->period;
                    $paidAmounts[] = (float)$row->paidAmount;
                }

                return [
                    'labels' => $labels,
                    'paidAmounts' => $paidAmounts
                ];

            } catch (PDOException $e) {
                error_log('Database error in getUserPayoutTrendData: ' . $e->getMessage());
                return [
                    'labels' => [],
                    'paidAmounts' => []
                ];
            }
        }

        public function getUserRecentPayouts($userId, $accountType, $limit = 10) {
            try {
                $limit = (int)$limit;
                if ($limit <= 0) {
                    $limit = 10;
                }

                $tableName = $accountType === 'driver' ? 'driver_payments' : 'guide_payments';
                $idField = $accountType === 'driver' ? 'driverId' : 'guideId';
                $chargeField = $accountType === 'driver' ? 'driverCharge' : 'guideCharge';
                $paymentIdField = $accountType === 'driver' ? 'driverPaymentId' : 'guidePaymentId';

                $query = "SELECT
                            p.$paymentIdField AS payoutId,
                            tp.tripId,
                            CASE
                                WHEN p.refunded = 1 THEN COALESCE(p.refundAmount, p.$chargeField)
                                ELSE p.$chargeField
                            END AS amount,
                            CASE
                                WHEN p.refunded = 1 THEN COALESCE(p.refundDate, p.updatedAt)
                                WHEN p.pDoneSite = 1 THEN COALESCE(p.pDateSite, p.updatedAt, p.createdAt)
                                ELSE p.createdAt
                            END AS payoutDate,
                            CASE
                                WHEN p.refunded = 1 THEN 'refunded'
                                WHEN p.pDoneSite = 1 THEN 'completed'
                                ELSE 'pending'
                            END AS payoutStatus,
                            tp.transactionId
                          FROM $tableName p
                          JOIN trip_payments tp ON p.wholePaymentId = tp.wholePaymentId
                          WHERE p.$idField = :userId
                          ORDER BY p.createdAt DESC
                          LIMIT $limit";

                $this->db->query($query);
                $this->db->bind(':userId', $userId);

                return $this->db->resultSet();

            } catch (PDOException $e) {
                error_log('Database error in getUserRecentPayouts: ' . $e->getMessage());
                return [];
            }
        }

        private function getGroupByClause($viewType) {
            switch ($viewType) {
                case 'daily':
                    return 'DATE(tp.createdAt)';
                case 'weekly':
                    return 'YEARWEEK(tp.createdAt)';
                case 'monthly':
                    return 'DATE_FORMAT(tp.createdAt, "%Y-%m")';
                default:
                    return 'DATE(tp.createdAt)';
            }
        }

        private function getDateFormat($viewType) {
            switch ($viewType) {
                case 'daily':
                    return '%Y-%m-%d';
                case 'weekly':
                    return '%Y-%U';
                case 'monthly':
                    return '%Y-%m';
                default:
                    return '%Y-%m-%d';
            }
        }

        private function getLatestSiteServiceCharges() {
            try {
                $query = "SELECT dBookCharge, gBookCharge, siteServiceCharge
                         FROM site_service_charges
                         WHERE isActive = TRUE
                         ORDER BY createdAt DESC
                         LIMIT 1";

                $this->db->query($query);
                $result = $this->db->single();

                if ($result) {
                    return [
                        'dBookCharge' => (float)$result->dBookCharge,
                        'gBookCharge' => (float)$result->gBookCharge,
                        'siteServiceCharge' => (float)$result->siteServiceCharge
                    ];
                } else {
                    // Return default values if no active charges are set
                    return [
                        'dBookCharge' => 0.00,
                        'gBookCharge' => 0.00,
                        'siteServiceCharge' => 10.00 // 10% default
                    ];
                }

            } catch (PDOException $e) {
                error_log("Database error in getLatestSiteServiceCharges: " . $e->getMessage());
                // Return default values on error
                return [
                    'dBookCharge' => 0.00,
                    'gBookCharge' => 0.00,
                    'siteServiceCharge' => 10.00
                ];
            }
        }

        public function getProfitMarginTrend($startDate, $endDate, $viewType) {
            try {
                $groupBy = $this->getGroupByClause($viewType);
                $dateFormat = $this->getDateFormat($viewType);

                // Get the latest active site service charges
                $siteCharges = $this->getLatestSiteServiceCharges();

                // Get revenue and profit data for each period
                $query = "SELECT
                    DATE_FORMAT(tp.createdAt, '$dateFormat') as period,
                    SUM(tp.totalCharge) as totalRevenue,
                    tp.wholePaymentId,
                    CASE WHEN dp.driverPaymentId IS NOT NULL THEN 1 ELSE 0 END as hasDriver,
                    CASE WHEN gp.guidePaymentId IS NOT NULL THEN 1 ELSE 0 END as hasGuide
                FROM trip_payments tp
                LEFT JOIN driver_payments dp ON tp.wholePaymentId = dp.wholePaymentId AND dp.pDoneTraveller = TRUE
                LEFT JOIN guide_payments gp ON tp.wholePaymentId = gp.wholePaymentId AND gp.pDoneTraveller = TRUE
                WHERE tp.createdAt BETWEEN :startDate AND :endDate
                AND tp.paymentStatus = 'completed'
                GROUP BY period, tp.wholePaymentId, hasDriver, hasGuide
                ORDER BY period ASC";

                $this->db->query($query);
                $this->db->bind(':startDate', $startDate);
                $this->db->bind(':endDate', $endDate);
                $rawData = $this->db->resultSet();

                // Calculate profit margin for each period
                $periodData = [];
                foreach ($rawData as $row) {
                    $period = $row->period;
                    if (!isset($periodData[$period])) {
                        $periodData[$period] = [
                            'revenue' => 0,
                            'profit' => 0
                        ];
                    }

                    $periodData[$period]['revenue'] += (float)$row->totalRevenue;

                    // Calculate profit for this trip
                    $tripProfit = 0;
                    if ($row->hasDriver) {
                        $tripProfit += $siteCharges['dBookCharge'];
                    }
                    if ($row->hasGuide) {
                        $tripProfit += $siteCharges['gBookCharge'];
                    }
                    $tripProfit += $row->totalRevenue * ($siteCharges['siteServiceCharge'] / 100);

                    $periodData[$period]['profit'] += $tripProfit;
                }

                // Calculate profit margins
                $periods = [];
                $profitMargins = [];

                foreach ($periodData as $period => $data) {
                    $periods[] = $period;
                    $margin = $data['revenue'] > 0 ? ($data['profit'] / $data['revenue']) * 100 : 0;
                    $profitMargins[] = round($margin, 2);
                }

                return [
                    'labels' => $periods,
                    'profitMargins' => $profitMargins
                ];

            } catch (PDOException $e) {
                error_log("Database error in getProfitMarginTrend: " . $e->getMessage());
                return [
                    'labels' => [],
                    'profitMargins' => []
                ];
            }
        }

        private function calculateSiteProfit($startDate, $endDate, $siteCharges) {
            try {
                $query = "SELECT
                    tp.totalCharge,
                    CASE WHEN dp.driverPaymentId IS NOT NULL THEN 1 ELSE 0 END as hasDriver,
                    CASE WHEN gp.guidePaymentId IS NOT NULL THEN 1 ELSE 0 END as hasGuide
                FROM trip_payments tp
                LEFT JOIN driver_payments dp ON tp.wholePaymentId = dp.wholePaymentId AND dp.pDoneTraveller = TRUE
                LEFT JOIN guide_payments gp ON tp.wholePaymentId = gp.wholePaymentId AND gp.pDoneTraveller = TRUE
                WHERE tp.createdAt BETWEEN :startDate AND :endDate
                AND tp.paymentStatus = 'completed'";

                $this->db->query($query);
                $this->db->bind(':startDate', $startDate);
                $this->db->bind(':endDate', $endDate);

                $trips = $this->db->resultSet();
                $totalSiteProfit = 0;

                foreach ($trips as $trip) {
                    $tripProfit = 0;

                    // Add fixed charges
                    if ($trip->hasDriver) {
                        $tripProfit += $siteCharges['dBookCharge'];
                    }
                    if ($trip->hasGuide) {
                        $tripProfit += $siteCharges['gBookCharge'];
                    }

                    // Add percentage charge
                    $tripProfit += $trip->totalCharge * ($siteCharges['siteServiceCharge'] / 100);

                    $totalSiteProfit += $tripProfit;
                }

                return $totalSiteProfit;

            } catch (PDOException $e) {
                error_log("Database error in calculateSiteProfit: " . $e->getMessage());
                return 0;
            }
        }

        public function getTopEarners($startDate, $endDate, $limit = 5) {
            try {
                $limit = (int)$limit;
                if ($limit <= 0) {
                    $limit = 5;
                }

                // Get top drivers
                $driverQuery = "SELECT
                    u.id,
                    u.fullname AS name,
                    'driver' AS account_type,
                    COALESCE(SUM(dp.driverCharge), 0) AS total_earned,
                    COUNT(dp.driverPaymentId) AS total_payments
                FROM users u
                LEFT JOIN driver_payments dp ON u.id = dp.driverId
                    AND dp.pDoneSite = 1
                    AND dp.refunded = 0
                    AND COALESCE(dp.pDateSite, dp.updatedAt, dp.createdAt) BETWEEN :startDate AND :endDate
                WHERE u.account_type = 'driver'
                GROUP BY u.id, u.fullname
                HAVING total_earned > 0
                ORDER BY total_earned DESC
                LIMIT $limit";

                $this->db->query($driverQuery);
                $this->db->bind(':startDate', $startDate);
                $this->db->bind(':endDate', $endDate);
                $drivers = $this->db->resultSet();

                // Get top guides
                $guideQuery = "SELECT
                    u.id,
                    u.fullname AS name,
                    'guide' AS account_type,
                    COALESCE(SUM(gp.guideCharge), 0) AS total_earned,
                    COUNT(gp.guidePaymentId) AS total_payments
                FROM users u
                LEFT JOIN guide_payments gp ON u.id = gp.guideId
                    AND gp.pDoneSite = 1
                    AND gp.refunded = 0
                    AND COALESCE(gp.pDateSite, gp.updatedAt, gp.createdAt) BETWEEN :startDate AND :endDate
                WHERE u.account_type = 'guide'
                GROUP BY u.id, u.fullname
                HAVING total_earned > 0
                ORDER BY total_earned DESC
                LIMIT $limit";

                $this->db->query($guideQuery);
                $this->db->bind(':startDate', $startDate);
                $this->db->bind(':endDate', $endDate);
                $guides = $this->db->resultSet();

                // Combine and sort by total_earned
                $allEarners = array_merge($drivers, $guides);
                usort($allEarners, function($a, $b) {
                    return $b->total_earned <=> $a->total_earned;
                });

                // Return top earners (up to limit)
                return array_slice($allEarners, 0, $limit);

            } catch (PDOException $e) {
                error_log('Database error in getTopEarners: ' . $e->getMessage());
                return [];
            }
        }

        // User Analysis Methods
        public function getUserBaseStats() {
            try {
                // Get total users by account type
                $query = "SELECT
                            COUNT(CASE WHEN account_type = 'tourist' THEN 1 END) as regUsers,
                            COUNT(CASE WHEN account_type = 'driver' THEN 1 END) as drivers,
                            COUNT(CASE WHEN account_type = 'guide' THEN 1 END) as guides,
                            COUNT(CASE WHEN account_type = 'buisManager' THEN 1 END) as buisManagers,
                            COUNT(CASE WHEN account_type = 'moderator' THEN 1 END) as moderators,
                            COUNT(CASE WHEN account_type = 'siteModerator' THEN 1 END) as siteModerators,
                            COUNT(*) as totalUsers
                          FROM users";

                $this->db->query($query);
                $result = $this->db->single();

                return [
                    'regUsers' => (int)$result->regUsers,
                    'drivers' => (int)$result->drivers,
                    'guides' => (int)$result->guides,
                    'buisManagers' => (int)$result->buisManagers,
                    'moderators' => (int)$result->moderators,
                    'siteModerators' => (int)$result->siteModerators,
                    'totalUsers' => (int)$result->totalUsers
                ];

            } catch (PDOException $e) {
                error_log("Database error in getUserBaseStats: " . $e->getMessage());
                return [
                    'regUsers' => 0,
                    'drivers' => 0,
                    'guides' => 0,
                    'buisManagers' => 0,
                    'moderators' => 0,
                    'siteModerators' => 0,
                    'totalUsers' => 0
                ];
            }
        }

        public function getVerificationStats() {
            try {
                // Get verification status counts for all users
                $query = "SELECT
                            COUNT(CASE WHEN av.status = 'approved' THEN 1 END) as verified,
                            COUNT(CASE WHEN av.status = 'pending' THEN 1 END) as pending,
                            COUNT(CASE WHEN av.status = 'rejected' THEN 1 END) as rejected,
                            COUNT(CASE WHEN av.status IS NULL THEN 1 END) as notApplied,
                            COUNT(*) as total
                          FROM users u
                          LEFT JOIN account_verifications av ON u.id = av.userId";

                $this->db->query($query);
                $result = $this->db->single();

                // Get verification status counts for drivers only
                $driverQuery = "SELECT
                                COUNT(CASE WHEN av.status = 'approved' THEN 1 END) as verified,
                                COUNT(CASE WHEN av.status = 'pending' THEN 1 END) as pending,
                                COUNT(CASE WHEN av.status = 'rejected' THEN 1 END) as rejected,
                                COUNT(CASE WHEN av.status IS NULL THEN 1 END) as notApplied
                              FROM users u
                              LEFT JOIN account_verifications av ON u.id = av.userId
                              WHERE u.account_type = 'driver'";

                $this->db->query($driverQuery);
                $driverResult = $this->db->single();

                // Get verification status counts for guides only
                $guideQuery = "SELECT
                               COUNT(CASE WHEN av.status = 'approved' THEN 1 END) as verified,
                               COUNT(CASE WHEN av.status = 'pending' THEN 1 END) as pending,
                               COUNT(CASE WHEN av.status = 'rejected' THEN 1 END) as rejected,
                               COUNT(CASE WHEN av.status IS NULL THEN 1 END) as notApplied
                             FROM users u
                             LEFT JOIN account_verifications av ON u.id = av.userId
                             WHERE u.account_type = 'guide'";

                $this->db->query($guideQuery);
                $guideResult = $this->db->single();

                return [
                    'overall' => [
                        'verified' => (int)$result->verified,
                        'pending' => (int)$result->pending,
                        'rejected' => (int)$result->rejected,
                        'notApplied' => (int)$result->notApplied,
                        'total' => (int)$result->total
                    ],
                    'drivers' => [
                        'verified' => (int)$driverResult->verified,
                        'pending' => (int)$driverResult->pending,
                        'rejected' => (int)$driverResult->rejected,
                        'notApplied' => (int)$driverResult->notApplied
                    ],
                    'guides' => [
                        'verified' => (int)$guideResult->verified,
                        'pending' => (int)$guideResult->pending,
                        'rejected' => (int)$guideResult->rejected,
                        'notApplied' => (int)$guideResult->notApplied
                    ]
                ];

            } catch (PDOException $e) {
                error_log("Database error in getVerificationStats: " . $e->getMessage());
                return [
                    'overall' => [
                        'verified' => 0,
                        'pending' => 0,
                        'rejected' => 0,
                        'notApplied' => 0,
                        'total' => 0
                    ],
                    'drivers' => [
                        'verified' => 0,
                        'pending' => 0,
                        'rejected' => 0,
                        'notApplied' => 0
                    ],
                    'guides' => [
                        'verified' => 0,
                        'pending' => 0,
                        'rejected' => 0,
                        'notApplied' => 0
                    ]
                ];
            }
        }

        public function getRegistrationTrend($startDate, $endDate, $viewType) {
            try {
                $dateFormat = $this->getDateFormat($viewType);

                $query = "SELECT
                            DATE_FORMAT(created_at, '$dateFormat') as period,
                                                        SUM(CASE WHEN account_type = 'driver' THEN 1 ELSE 0 END) as drivers,
                                                        SUM(CASE WHEN account_type = 'guide' THEN 1 ELSE 0 END) as guides,
                                                        SUM(CASE WHEN account_type = 'tourist' THEN 1 ELSE 0 END) as tourists,
                                                        COUNT(*) as total
                          FROM users
                          WHERE created_at BETWEEN :startDate AND :endDate
                          GROUP BY DATE_FORMAT(created_at, '$dateFormat')
                          ORDER BY period ASC";
 
                $this->db->query($query);
                $this->db->bind(':startDate', $startDate);
                $this->db->bind(':endDate', $endDate);

                $rows = $this->db->resultSet();
                $labels = [];
                $drivers = [];
                $guides = [];
                $tourists = [];
                $totals = [];

                foreach ($rows as $row) {
                    $labels[] = $row->period;
                    $drivers[] = (int)$row->drivers;
                    $guides[] = (int)$row->guides;
                    $tourists[] = (int)$row->tourists;
                    $totals[] = (int)$row->total;
                }

                return [
                    'labels' => $labels,
                    'drivers' => $drivers,
                    'guides' => $guides,
                    'tourists' => $tourists,
                    'totals' => $totals
                ];

            } catch (PDOException $e) {
                error_log("Database error in getRegistrationTrend: " . $e->getMessage());
                return [
                    'labels' => [],
                    'drivers' => [],
                    'guides' => [],
                    'tourists' => [],
                    'totals' => []
                ];
            }
        }

        public function getLicenseStats() {
            try {
                // Get tourist license verification status counts for all users
                $query = "SELECT
                            COUNT(CASE WHEN tlv.status = 'approved' THEN 1 END) as verified,
                            COUNT(CASE WHEN tlv.status = 'pending' THEN 1 END) as pending,
                            COUNT(CASE WHEN tlv.status = 'rejected' THEN 1 END) as rejected,
                            COUNT(CASE WHEN tlv.status IS NULL THEN 1 END) as notApplied,
                            COUNT(*) as total
                          FROM users u
                          LEFT JOIN tlicense_verifications tlv ON u.id = tlv.userId";

                $this->db->query($query);
                $result = $this->db->single();

                // Get license status counts for drivers only
                $driverQuery = "SELECT
                                COUNT(CASE WHEN tlv.status = 'approved' THEN 1 END) as verified,
                                COUNT(CASE WHEN tlv.status = 'pending' THEN 1 END) as pending,
                                COUNT(CASE WHEN tlv.status = 'rejected' THEN 1 END) as rejected,
                                COUNT(CASE WHEN tlv.status IS NULL THEN 1 END) as notApplied
                              FROM users u
                              LEFT JOIN tlicense_verifications tlv ON u.id = tlv.userId
                              WHERE u.account_type = 'driver'";

                $this->db->query($driverQuery);
                $driverResult = $this->db->single();

                // Get license status counts for guides only
                $guideQuery = "SELECT
                               COUNT(CASE WHEN tlv.status = 'approved' THEN 1 END) as verified,
                               COUNT(CASE WHEN tlv.status = 'pending' THEN 1 END) as pending,
                               COUNT(CASE WHEN tlv.status = 'rejected' THEN 1 END) as rejected,
                               COUNT(CASE WHEN tlv.status IS NULL THEN 1 END) as notApplied
                             FROM users u
                             LEFT JOIN tlicense_verifications tlv ON u.id = tlv.userId
                             WHERE u.account_type = 'guide'";

                $this->db->query($guideQuery);
                $guideResult = $this->db->single();

                return [
                    'overall' => [
                        'verified' => (int)$result->verified,
                        'pending' => (int)$result->pending,
                        'rejected' => (int)$result->rejected,
                        'notApplied' => (int)$result->notApplied,
                        'total' => (int)$result->total
                    ],
                    'drivers' => [
                        'verified' => (int)$driverResult->verified,
                        'pending' => (int)$driverResult->pending,
                        'rejected' => (int)$driverResult->rejected,
                        'notApplied' => (int)$driverResult->notApplied
                    ],
                    'guides' => [
                        'verified' => (int)$guideResult->verified,
                        'pending' => (int)$guideResult->pending,
                        'rejected' => (int)$guideResult->rejected,
                        'notApplied' => (int)$guideResult->notApplied
                    ]
                ];

            } catch (PDOException $e) {
                error_log("Database error in getLicenseStats: " . $e->getMessage());
                return [
                    'overall' => [
                        'verified' => 0,
                        'pending' => 0,
                        'rejected' => 0,
                        'notApplied' => 0,
                        'total' => 0
                    ],
                    'drivers' => [
                        'verified' => 0,
                        'pending' => 0,
                        'rejected' => 0,
                        'notApplied' => 0
                    ],
                    'guides' => [
                        'verified' => 0,
                        'pending' => 0,
                        'rejected' => 0,
                        'notApplied' => 0
                    ]
                ];
            }
        }

        public function getVehicleVerificationStats() {
            try {
                // Get vehicle verification status counts
                $query = "SELECT
                            COUNT(CASE WHEN vv.status = 'approved' THEN 1 END) as verified,
                            COUNT(CASE WHEN vv.status = 'pending' THEN 1 END) as pending,
                            COUNT(CASE WHEN vv.status = 'rejected' THEN 1 END) as rejected,
                            COUNT(*) as total
                          FROM vehicle_verifications vv
                          INNER JOIN users u ON vv.userId = u.id
                          WHERE u.account_type = 'driver'";

                $this->db->query($query);
                $result = $this->db->single();

                return [
                    'totalVehicles' => (int)$result->total,
                    'verifiedVehicles' => (int)$result->verified,
                    'pendingVerifications' => (int)$result->pending,
                    'rejectedVehicles' => (int)$result->rejected
                ];

            } catch (PDOException $e) {
                error_log("Database error in getVehicleVerificationStats: " . $e->getMessage());
                return [
                    'totalVehicles' => 0,
                    'verifiedVehicles' => 0,
                    'pendingVerifications' => 0,
                    'rejectedVehicles' => 0
                ];
            }
        }

        public function getVehicleVerificationTrend($startDate, $endDate, $viewType) {
            try {
                $dateFormat = $this->getDateFormat($viewType);

                $query = "SELECT
                            DATE_FORMAT(vv.createdAt, '$dateFormat') as period,
                            COUNT(CASE WHEN vv.status = 'approved' THEN 1 END) as approved,
                            COUNT(CASE WHEN vv.status = 'pending' THEN 1 END) as pending,
                            COUNT(CASE WHEN vv.status = 'rejected' THEN 1 END) as rejected,
                            COUNT(*) as total
                          FROM vehicle_verifications vv
                          INNER JOIN users u ON vv.userId = u.id
                          WHERE u.account_type = 'driver'
                            AND vv.createdAt BETWEEN :startDate AND :endDate
                          GROUP BY DATE_FORMAT(vv.createdAt, '$dateFormat')
                          ORDER BY period ASC";

                $this->db->query($query);
                $this->db->bind(':startDate', $startDate);
                $this->db->bind(':endDate', $endDate);

                $rows = $this->db->resultSet();
                $labels = [];
                $approved = [];
                $pending = [];
                $rejected = [];
                $totals = [];

                foreach ($rows as $row) {
                    $labels[] = $row->period;
                    $approved[] = (int)$row->approved;
                    $pending[] = (int)$row->pending;
                    $rejected[] = (int)$row->rejected;
                    $totals[] = (int)$row->total;
                }

                return [
                    'labels' => $labels,
                    'approved' => $approved,
                    'pending' => $pending,
                    'rejected' => $rejected,
                    'totals' => $totals
                ];

            } catch (PDOException $e) {
                error_log("Database error in getVehicleVerificationTrend: " . $e->getMessage());
                return [
                    'labels' => [],
                    'approved' => [],
                    'pending' => [],
                    'rejected' => [],
                    'totals' => []
                ];
            }
        }

        public function getVehicleVerificationStatusBreakdown() {
            try {
                // Get vehicle verification status breakdown
                $query = "SELECT
                            vv.status,
                            COUNT(*) as count
                          FROM vehicle_verifications vv
                          INNER JOIN users u ON vv.userId = u.id
                          WHERE u.account_type = 'driver'
                          GROUP BY vv.status";

                $this->db->query($query);
                $rows = $this->db->resultSet();

                $breakdown = [
                    'approved' => 0,
                    'pending' => 0,
                    'rejected' => 0
                ];

                foreach ($rows as $row) {
                    $breakdown[$row->status] = (int)$row->count;
                }

                return $breakdown;

            } catch (PDOException $e) {
                error_log("Database error in getVehicleVerificationStatusBreakdown: " . $e->getMessage());
                return [
                    'approved' => 0,
                    'pending' => 0,
                    'rejected' => 0
                ];
            }
        }

        public function getFleetStats() {
            try {
                // Get overall fleet statistics
                $totalQuery = "SELECT
                            COUNT(*) as totalVehicles,
                            COUNT(CASE WHEN availability = 1 THEN 1 END) as availableVehicles,
                            COUNT(CASE WHEN isApproved = 1 THEN 1 END) as approvedVehicles,
                            AVG(seatingCapacity) as avgSeatingCapacity
                          FROM vehicles";

                $this->db->query($totalQuery);
                $totals = $this->db->single();

                // Get vehicle model distribution
                $modelQuery = "SELECT
                            model,
                            COUNT(*) as count
                          FROM vehicles
                          WHERE model IS NOT NULL AND model != ''
                          GROUP BY model
                          ORDER BY count DESC";

                $this->db->query($modelQuery);
                $models = $this->db->resultSet();

                $vehicleTypes = [];
                foreach ($models as $model) {
                    $vehicleTypes[$model->model] = (int)$model->count;
                }

                $stats = [
                    'totalBookings' => (int)($totals->totalVehicles ?? 0), // Reusing field name for compatibility
                    'activeBookings' => (int)($totals->availableVehicles ?? 0),
                    'completedTrips' => (int)($totals->approvedVehicles ?? 0),
                    'avgTripDistance' => round((float)($totals->avgSeatingCapacity ?? 0), 1),
                    'totalRevenue' => 0, // Not applicable for fleet stats
                    'vehicleTypes' => $vehicleTypes
                ];

                return $stats;

            } catch (PDOException $e) {
                error_log("Database error in getFleetStats: " . $e->getMessage());
                return [
                    'totalBookings' => 0,
                    'activeBookings' => 0,
                    'completedTrips' => 0,
                    'avgTripDistance' => 0,
                    'totalRevenue' => 0,
                    'vehicleTypes' => []
                ];
            }
        }

        public function getVehicleBookingTrend($startDate, $endDate, $viewType) {
            try {
                $dateFormat = $this->getDateFormat($viewType);

                $query = "SELECT
                            DATE_FORMAT(createdAt, '$dateFormat') as period,
                            COUNT(*) as bookings
                          FROM driver_accept_trips
                          WHERE createdAt BETWEEN :startDate AND :endDate
                          GROUP BY DATE_FORMAT(createdAt, '$dateFormat')
                          ORDER BY period ASC";

                $this->db->query($query);
                $this->db->bind(':startDate', $startDate);
                $this->db->bind(':endDate', $endDate);

                $rows = $this->db->resultSet();
                $labels = [];
                $bookings = [];

                foreach ($rows as $row) {
                    $labels[] = $row->period;
                    $bookings[] = (int)$row->bookings;
                }

                return [
                    'labels' => $labels,
                    'bookings' => $bookings
                ];

            } catch (PDOException $e) {
                error_log("Database error in getVehicleBookingTrend: " . $e->getMessage());
                return [
                    'labels' => [],
                    'bookings' => []
                ];
            }
        }

        public function getDriverOwnershipStats() {
            try {
                // Get distribution of vehicles per driver
                $query = "SELECT
                            CASE
                                WHEN vehicleCount = 1 THEN '1 Vehicle'
                                WHEN vehicleCount = 2 THEN '2 Vehicles'
                                WHEN vehicleCount >= 3 THEN '3+ Vehicles'
                                ELSE 'Unknown'
                            END as category,
                            COUNT(*) as driverCount
                          FROM (
                              SELECT driverId, COUNT(*) as vehicleCount
                              FROM vehicles
                              GROUP BY driverId
                          ) driver_vehicle_counts
                          GROUP BY category
                          ORDER BY 
                              CASE category
                                  WHEN '1 Vehicle' THEN 1
                                  WHEN '2 Vehicles' THEN 2
                                  WHEN '3+ Vehicles' THEN 3
                                  ELSE 4
                              END";

                $this->db->query($query);
                $rows = $this->db->resultSet();

                $ownership = [
                    '1 Vehicle' => 0,
                    '2 Vehicles' => 0,
                    '3+ Vehicles' => 0
                ];

                foreach ($rows as $row) {
                    $ownership[$row->category] = (int)$row->driverCount;
                }

                return $ownership;

            } catch (PDOException $e) {
                error_log("Database error in getDriverOwnershipStats: " . $e->getMessage());
                return [
                    '1 Vehicle' => 0,
                    '2 Vehicles' => 0,
                    '3+ Vehicles' => 0
                ];
            }
        }

        // User Info Methods
        public function getUserProfileInfoForModerator($userId) {
            try {
                $query = "SELECT
                            u.id,
                            u.account_type,
                            u.fullname,
                            u.language,
                            u.dob,
                            u.gender,
                            u.phone,
                            u.secondary_phone,
                            u.address,
                            u.email,
                            u.profile_photo,
                            u.currency_code,
                            u.last_login,
                            u.created_at,
                            u.updated_at,
                            pd.profileId,
                            pd.bio,
                            pd.languages,
                            pd.instaAccount,
                            pd.facebookAccount,
                            pd.dlVerified,
                            pd.tlSubmitted,
                            pd.tlVerified,
                            pd.tLicenseNumber,
                            pd.tLicenseExpiryDate,
                            pd.averageRating
                          FROM users u
                          LEFT JOIN profile_details pd ON pd.userId = u.id
                          WHERE u.id = :userId
                          LIMIT 1";

                $this->db->query($query);
                $this->db->bind(':userId', $userId);
                $user = $this->db->single();

                if (!$user) {
                    return null;
                }

                $user->reviews = $this->getUserReceivedTravellerReviewsForModerator($userId);
                $user->reviewsCount = count($user->reviews);

                if ($user->account_type === 'driver') {
                    $user->driverVehicles = $this->getDriverVehiclesForModerator($userId);
                } else {
                    $user->driverVehicles = [];
                }

                if ($user->account_type === 'guide') {
                    $user->guideLocations = $this->getGuideLocationsForModerator($userId);
                } else {
                    $user->guideLocations = [];
                }

                return $user;

            } catch (PDOException $e) {
                error_log('Database error in getUserProfileInfoForModerator: ' . $e->getMessage());
                return null;
            }
        }

        public function getUserReceivedTravellerReviewsForModerator($userId) {
            try {
                $query = "SELECT
                            tr.reviewId,
                            tr.travellerId,
                            tr.guideDriverId,
                            tr.reviewText,
                            tr.rating,
                            tr.createdAt,
                            tr.updatedAt,
                            u.fullname AS reviewerName,
                            u.email AS reviewerEmail,
                            u.profile_photo AS reviewerPhoto
                          FROM traveller_reviews tr
                          INNER JOIN users u ON u.id = tr.travellerId
                          WHERE tr.guideDriverId = :userId
                          ORDER BY tr.createdAt DESC";

                $this->db->query($query);
                $this->db->bind(':userId', $userId);
                return $this->db->resultSet();

            } catch (PDOException $e) {
                error_log('Database error in getUserReceivedTravellerReviewsForModerator: ' . $e->getMessage());
                return [];
            }
        }

        public function getDriverVehiclesForModerator($driverId) {
            try {
                $query = "SELECT
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
                            v.status,
                            v.availability,
                            v.isApproved,
                            v.createdAt,
                            v.updatedAt,
                            vp.vehicleChargePerKm,
                            vp.driverChargePerKm,
                            vp.vehicleChargePerDay,
                            vp.driverChargePerDay,
                            vp.minimumKm,
                            vp.minimumDays,
                            vp.updatedAt AS pricingUpdatedAt
                          FROM vehicles v
                          LEFT JOIN vehicle_pricing vp ON vp.pricingId = (
                              SELECT vp2.pricingId
                              FROM vehicle_pricing vp2
                              WHERE vp2.vehicleId = v.vehicleId
                              ORDER BY vp2.createdAt DESC, vp2.pricingId DESC
                              LIMIT 1
                          )
                          WHERE v.driverId = :driverId
                          ORDER BY v.createdAt DESC";

                $this->db->query($query);
                $this->db->bind(':driverId', $driverId);
                return $this->db->resultSet();

            } catch (PDOException $e) {
                error_log('Database error in getDriverVehiclesForModerator: ' . $e->getMessage());
                return [];
            }
        }

        public function getGuideLocationsForModerator($guideId) {
            try {
                $query = "SELECT
                            gl.id,
                            gl.guideId,
                            gl.spotId,
                            gl.baseCharge,
                            gl.chargeType,
                            gl.minGroupSize,
                            gl.maxGroupSize,
                            gl.description,
                            gl.photoPath,
                            gl.isActive,
                            gl.created_at,
                            gl.updated_at,
                            ts.spotName
                          FROM guide_locations gl
                          LEFT JOIN travel_spots ts ON ts.spotId = gl.spotId
                          WHERE gl.guideId = :guideId
                          ORDER BY gl.created_at DESC";

                $this->db->query($query);
                $this->db->bind(':guideId', $guideId);
                return $this->db->resultSet();

            } catch (PDOException $e) {
                error_log('Database error in getGuideLocationsForModerator: ' . $e->getMessage());
                return [];
            }
        }

        public function getVehicleInfoForModerator($vehicleId) {
            try {
                $query = "SELECT
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
                            v.status,
                            v.availability,
                            v.isApproved,
                            v.createdAt,
                            v.updatedAt,
                            u.id AS ownerId,
                            u.fullname AS ownerName,
                            u.email AS ownerEmail,
                            u.phone AS ownerPhone,
                            u.secondary_phone AS ownerSecondaryPhone,
                            u.address AS ownerAddress,
                            u.profile_photo AS ownerPhoto,
                            u.account_type AS ownerAccountType,
                            u.last_login AS ownerLastLogin,
                            vv.status AS verificationStatus,
                            vv.reviewedBy,
                            vv.reviewedAt,
                            vv.expiryDate,
                            vv.rejectionReason,
                            reviewer.fullname AS reviewerName,
                            vp.vehicleChargePerKm,
                            vp.driverChargePerKm,
                            vp.vehicleChargePerDay,
                            vp.driverChargePerDay,
                            vp.minimumKm,
                            vp.minimumDays,
                            vp.createdAt AS pricingCreatedAt,
                            vp.updatedAt AS pricingUpdatedAt
                          FROM vehicles v
                          INNER JOIN users u ON u.id = v.driverId
                          LEFT JOIN vehicle_verifications vv ON vv.id = (
                              SELECT vv2.id
                              FROM vehicle_verifications vv2
                              WHERE vv2.vehicleId = v.vehicleId
                              ORDER BY vv2.createdAt DESC, vv2.id DESC
                              LIMIT 1
                          )
                          LEFT JOIN users reviewer ON reviewer.id = vv.reviewedBy
                          LEFT JOIN vehicle_pricing vp ON vp.pricingId = (
                              SELECT vp2.pricingId
                              FROM vehicle_pricing vp2
                              WHERE vp2.vehicleId = v.vehicleId
                              ORDER BY vp2.createdAt DESC, vp2.pricingId DESC
                              LIMIT 1
                          )
                          WHERE v.vehicleId = :vehicleId
                          LIMIT 1";

                $this->db->query($query);
                $this->db->bind(':vehicleId', $vehicleId);
                return $this->db->single();

            } catch (PDOException $e) {
                error_log('Database error in getVehicleInfoForModerator: ' . $e->getMessage());
                return null;
            }
        }

        public function getGuidesByTravelSpotSearchForModerator($search) {
            try {
                $isNumericSearch = is_numeric($search);

                if ($isNumericSearch) {
                    $query = "SELECT
                                ts.spotId,
                                ts.spotName,
                                ts.overview,
                                ts.province,
                                ts.district,
                                ts.averageRating,
                                ts.totalReviews,
                                gl.id AS guideLocationId,
                                gl.guideId,
                                gl.baseCharge,
                                gl.chargeType,
                                gl.minGroupSize,
                                gl.maxGroupSize,
                                gl.description AS guideLocationDescription,
                                gl.photoPath AS guideLocationPhoto,
                                gl.isActive,
                                gl.created_at,
                                gl.updated_at,
                                u.fullname AS guideName,
                                u.email AS guideEmail,
                                u.phone AS guidePhone,
                                u.secondary_phone AS guideSecondaryPhone,
                                u.language AS guideLanguage,
                                u.profile_photo AS guideProfilePhoto
                              FROM guide_locations gl
                              INNER JOIN travel_spots ts ON ts.spotId = gl.spotId
                              INNER JOIN users u ON u.id = gl.guideId
                              WHERE ts.spotId = :spotId
                                AND u.account_type = 'guide'
                              ORDER BY ts.spotName, gl.isActive DESC, u.fullname";

                    $this->db->query($query);
                    $this->db->bind(':spotId', (int)$search);
                } else {
                    $query = "SELECT
                                ts.spotId,
                                ts.spotName,
                                ts.overview,
                                ts.province,
                                ts.district,
                                ts.averageRating,
                                ts.totalReviews,
                                gl.id AS guideLocationId,
                                gl.guideId,
                                gl.baseCharge,
                                gl.chargeType,
                                gl.minGroupSize,
                                gl.maxGroupSize,
                                gl.description AS guideLocationDescription,
                                gl.photoPath AS guideLocationPhoto,
                                gl.isActive,
                                gl.created_at,
                                gl.updated_at,
                                u.fullname AS guideName,
                                u.email AS guideEmail,
                                u.phone AS guidePhone,
                                u.secondary_phone AS guideSecondaryPhone,
                                u.language AS guideLanguage,
                                u.profile_photo AS guideProfilePhoto
                              FROM guide_locations gl
                              INNER JOIN travel_spots ts ON ts.spotId = gl.spotId
                              INNER JOIN users u ON u.id = gl.guideId
                              WHERE ts.spotName LIKE CONCAT('%', :spotName, '%')
                                AND u.account_type = 'guide'
                              ORDER BY ts.spotName, gl.isActive DESC, u.fullname";

                    $this->db->query($query);
                    $this->db->bind(':spotName', $search);
                }

                return $this->db->resultSet();

            } catch (PDOException $e) {
                error_log('Database error in getGuidesByTravelSpotSearchForModerator: ' . $e->getMessage());
                return [];
            }
        }

        public function getTripLogsForModerator() {
            try {
                $statuses = ['awPayment', 'scheduled', 'ongoing', 'completed', 'cancelled'];

                $tripQuery = "SELECT
                                ct.tripId,
                                ct.userId,
                                ct.tripTitle,
                                ct.description,
                                ct.startDate,
                                ct.endDate,
                                ct.status,
                                ct.numberOfPeople,
                                ct.startPin,
                                ct.createdAt,
                                ct.updatedAt,
                                u.fullname AS travellerName,
                                u.email AS travellerEmail
                              FROM created_trips ct
                              LEFT JOIN users u ON u.id = ct.userId
                              WHERE ct.status IN ('awPayment', 'scheduled', 'ongoing', 'completed', 'cancelled')
                              ORDER BY ct.createdAt DESC";

                $this->db->query($tripQuery);
                $trips = $this->db->resultSet();

                if (!$trips || count($trips) === 0) {
                    return [];
                }

                $tripIds = [];
                $tripMap = [];

                foreach ($trips as $trip) {
                    $trip->drivers = [];
                    $trip->guides = [];
                    $tripIds[] = (int)$trip->tripId;
                    $tripMap[(int)$trip->tripId] = $trip;
                }

                $placeholders = [];
                foreach ($tripIds as $index => $id) {
                    $placeholders[] = ':trip' . $index;
                }
                $inClause = implode(',', $placeholders);

                // Drivers assigned to each trip
                $driverQuery = "SELECT
                                    dr.tripId,
                                    dr.driverId,
                                    dr.driverName,
                                    dr.driverProfilePhoto,
                                    dr.driverRating,
                                    dr.vehicleId,
                                    dr.vehicleModel,
                                    dr.vehicleType,
                                    dr.vehiclePhoto,
                                    dr.vehicleCapacity,
                                    dr.childSeats,
                                    dr.requestStatus,
                                    dr.chargeType,
                                    dr.totalKm,
                                    dr.totalAmount,
                                    dr.requestedAt,
                                    dr.respondedAt,
                                    dr.completedAt
                                FROM traveller_side_d_requests dr
                                WHERE dr.tripId IN ($inClause)
                                  AND dr.driverId IS NOT NULL
                                  AND dr.requestStatus IN ('requested', 'accepted', 'completed', 'ongoing', 'scheduled')
                                ORDER BY dr.tripId, dr.requestedAt DESC";

                $this->db->query($driverQuery);
                foreach ($tripIds as $index => $id) {
                    $this->db->bind(':trip' . $index, $id);
                }
                $drivers = $this->db->resultSet();

                foreach ($drivers as $driver) {
                    $tripId = (int)$driver->tripId;
                    if (isset($tripMap[$tripId])) {
                        $tripMap[$tripId]->drivers[] = $driver;
                    }
                }

                // Guides assigned to each event (eventId/placeId)
                $guideQuery = "SELECT
                                    gr.tripId,
                                    gr.eventId,
                                    gr.travelSpotId,
                                    gr.guideId,
                                    gr.status AS requestStatus,
                                    gr.guideFullName,
                                    gr.guideProfilePhoto,
                                    gr.guideAverageRating,
                                    gr.chargeType,
                                    gr.numberOfPeople,
                                    gr.totalCharge,
                                    gr.requestedAt,
                                    gr.respondedAt,
                                    gr.acceptedAt,
                                    gr.completedAt,
                                    ts.spotName
                                FROM traveller_side_g_requests gr
                                LEFT JOIN travel_spots ts ON ts.spotId = gr.travelSpotId
                                WHERE gr.tripId IN ($inClause)
                                  AND gr.guideId IS NOT NULL
                                  AND gr.status IN ('requested', 'accepted', 'completed', 'ongoing', 'scheduled')
                                ORDER BY gr.tripId, gr.eventId ASC";

                $this->db->query($guideQuery);
                foreach ($tripIds as $index => $id) {
                    $this->db->bind(':trip' . $index, $id);
                }
                $guides = $this->db->resultSet();

                foreach ($guides as $guide) {
                    $tripId = (int)$guide->tripId;
                    if (isset($tripMap[$tripId])) {
                        $tripMap[$tripId]->guides[] = $guide;
                    }
                }

                return array_values($tripMap);

            } catch (PDOException $e) {
                error_log('Database error in getTripLogsForModerator: ' . $e->getMessage());
                return [];
            }
        }

        public function getTripControlTripById($tripId) {
            try {
                $tripQuery = "SELECT
                                ct.tripId,
                                ct.userId,
                                ct.tripTitle,
                                ct.description,
                                ct.startDate,
                                ct.endDate,
                                ct.status,
                                ct.numberOfPeople,
                                u.fullname AS travellerName,
                                u.email AS travellerEmail,
                                u.phone AS travellerPhone
                              FROM created_trips ct
                              LEFT JOIN users u ON u.id = ct.userId
                              WHERE ct.tripId = :tripId
                              LIMIT 1";

                $this->db->query($tripQuery);
                $this->db->bind(':tripId', (int)$tripId);
                $trip = $this->db->single();

                if (!$trip) {
                    return null;
                }

                $trip->currentDriver = $this->getCurrentTripDriver($tripId);

                $eventQuery = "SELECT
                                te.eventId,
                                te.tripId,
                                te.eventDate,
                                te.startTime,
                                te.endTime,
                                te.eventType,
                                te.eventStatus,
                                te.travelSpotId,
                                te.locationName,
                                te.description,
                                ts.spotName
                              FROM trip_events te
                              LEFT JOIN travel_spots ts ON ts.spotId = te.travelSpotId
                              WHERE te.tripId = :tripId
                              ORDER BY te.eventDate ASC, CAST(te.startTime AS TIME) ASC, te.eventId ASC";

                $this->db->query($eventQuery);
                $this->db->bind(':tripId', (int)$tripId);
                $events = $this->db->resultSet();

                foreach ($events as $event) {
                    $event->currentGuide = $this->getCurrentEventGuide($tripId, $event->eventId);
                }

                $trip->events = $events;

                return $trip;

            } catch (PDOException $e) {
                error_log('Database error in getTripControlTripById: ' . $e->getMessage());
                return null;
            }
        }

        public function getTripControlDriverCandidates($tripId) {
            try {
                $trip = $this->getTripControlTripById($tripId);
                if (!$trip) {
                    return [];
                }

                $currentDriverId = $trip->currentDriver ? (int)$trip->currentDriver->driverId : 0;

                $query = "SELECT
                            u.id AS driverId,
                            u.fullname AS driverName,
                            u.email AS driverEmail,
                            u.phone AS driverPhone,
                            u.profile_photo AS driverProfilePhoto,
                            pd.averageRating,
                            pd.bio,
                            lv.vehicleId,
                            lv.model AS vehicleModel,
                            lv.make AS vehicleType,
                            lv.year AS vehicleYear,
                            lv.seatingCapacity AS vehicleCapacity,
                            lv.childSeats,
                            lv.frontViewPhoto AS vehiclePhoto,
                            COALESCE(vp.vehicleChargePerDay, 0) + COALESCE(vp.driverChargePerDay, 0) AS totalChargePerDay,
                            (
                                SELECT COUNT(*)
                                FROM driver_unavailable_dates dud
                                WHERE dud.driverId = u.id
                                  AND dud.unavailableDate BETWEEN :startDate AND :endDate
                                  AND (dud.tripId IS NULL OR dud.tripId <> :tripIdForAvailability)
                            ) AS conflictCount,
                            (
                                SELECT MIN(dud2.unavailableDate)
                                FROM driver_unavailable_dates dud2
                                WHERE dud2.driverId = u.id
                                  AND dud2.unavailableDate BETWEEN :startDate2 AND :endDate2
                                  AND (dud2.tripId IS NULL OR dud2.tripId <> :tripIdForAvailability2)
                            ) AS firstConflictDate
                          FROM users u
                          LEFT JOIN profile_details pd ON pd.userId = u.id
                          INNER JOIN (
                            SELECT v1.*
                            FROM vehicles v1
                            INNER JOIN (
                                SELECT driverId, MAX(vehicleId) AS latestVehicleId
                                FROM vehicles
                                GROUP BY driverId
                            ) vm ON vm.latestVehicleId = v1.vehicleId
                          ) lv ON lv.driverId = u.id
                          LEFT JOIN vehicle_pricing vp ON vp.vehicleId = lv.vehicleId
                          WHERE u.account_type = 'driver'
                          ORDER BY conflictCount ASC, pd.averageRating DESC, u.fullname ASC";

                $this->db->query($query);
                $this->db->bind(':startDate', $trip->startDate);
                $this->db->bind(':endDate', $trip->endDate);
                $this->db->bind(':tripIdForAvailability', (int)$tripId);
                $this->db->bind(':startDate2', $trip->startDate);
                $this->db->bind(':endDate2', $trip->endDate);
                $this->db->bind(':tripIdForAvailability2', (int)$tripId);

                $rows = $this->db->resultSet();
                $candidates = [];

                foreach ($rows as $row) {
                    $isAvailable = ((int)$row->conflictCount) === 0;
                    $capacityOk = (int)$row->vehicleCapacity >= (int)$trip->numberOfPeople;
                    $candidates[] = [
                        'driverId' => (int)$row->driverId,
                        'driverName' => $row->driverName,
                        'driverEmail' => $row->driverEmail,
                        'driverPhone' => $row->driverPhone,
                        'driverProfilePhoto' => $row->driverProfilePhoto,
                        'averageRating' => (float)($row->averageRating ?? 0),
                        'bio' => $row->bio,
                        'vehicleId' => (int)$row->vehicleId,
                        'vehicleModel' => $row->vehicleModel,
                        'vehicleType' => $row->vehicleType,
                        'vehicleYear' => $row->vehicleYear,
                        'vehicleCapacity' => (int)$row->vehicleCapacity,
                        'childSeats' => (int)($row->childSeats ?? 0),
                        'vehiclePhoto' => $row->vehiclePhoto,
                        'totalChargePerDay' => (float)($row->totalChargePerDay ?? 0),
                        'isAvailable' => $isAvailable,
                        'capacityOk' => $capacityOk,
                        'canAssignWithoutForce' => $isAvailable && $capacityOk,
                        'firstConflictDate' => $row->firstConflictDate,
                        'isCurrentAssigned' => ((int)$row->driverId === $currentDriverId)
                    ];
                }

                return $candidates;

            } catch (PDOException $e) {
                error_log('Database error in getTripControlDriverCandidates: ' . $e->getMessage());
                return [];
            }
        }

        public function getTripControlGuideCandidates($tripId, $eventId) {
            try {
                $event = $this->getTripEventSlotForControl($tripId, $eventId);
                if (!$event) {
                    return [];
                }

                $currentGuide = $this->getCurrentEventGuide($tripId, $eventId);
                $currentGuideId = $currentGuide ? (int)$currentGuide->guideId : 0;

                $eventStartTime = $event->startTime ? $event->startTime : '00:00:00';
                $eventEndTime = $event->endTime ? $event->endTime : '23:59:59';

                $query = "SELECT
                            u.id AS guideId,
                            u.fullname AS guideName,
                            u.email AS guideEmail,
                            u.phone AS guidePhone,
                            u.profile_photo AS guideProfilePhoto,
                            pd.averageRating,
                            pd.bio,
                            gl.id AS guideLocationId,
                            gl.baseCharge,
                            gl.chargeType,
                            gl.minGroupSize,
                            gl.maxGroupSize,
                            (
                                SELECT COUNT(*)
                                FROM guide_unavailability gu
                                WHERE gu.guideId = u.id
                                  AND gu.unavailableDate = :eventDate
                                  AND (gu.tripId IS NULL OR gu.tripId <> :tripId)
                                  AND (
                                        gu.startTime IS NULL
                                        OR gu.endTime IS NULL
                                        OR (:eventEndTime > gu.startTime AND :eventStartTime < gu.endTime)
                                      )
                            ) AS conflictCount
                          FROM users u
                          LEFT JOIN profile_details pd ON pd.userId = u.id
                          LEFT JOIN guide_locations gl
                                 ON gl.guideId = u.id
                                AND gl.spotId = :travelSpotId
                                AND gl.isActive = 1
                          WHERE u.account_type = 'guide'
                          ORDER BY conflictCount ASC, (gl.id IS NULL) ASC, pd.averageRating DESC, u.fullname ASC";

                $this->db->query($query);
                $this->db->bind(':eventDate', $event->eventDate);
                $this->db->bind(':tripId', (int)$tripId);
                $this->db->bind(':eventEndTime', $eventEndTime);
                $this->db->bind(':eventStartTime', $eventStartTime);
                $this->db->bind(':travelSpotId', $event->travelSpotId ? (int)$event->travelSpotId : 0);

                $rows = $this->db->resultSet();
                $candidates = [];

                foreach ($rows as $row) {
                    $isAvailable = ((int)$row->conflictCount) === 0;
                    $candidates[] = [
                        'guideId' => (int)$row->guideId,
                        'guideName' => $row->guideName,
                        'guideEmail' => $row->guideEmail,
                        'guidePhone' => $row->guidePhone,
                        'guideProfilePhoto' => $row->guideProfilePhoto,
                        'averageRating' => (float)($row->averageRating ?? 0),
                        'bio' => $row->bio,
                        'baseCharge' => $row->baseCharge !== null ? (float)$row->baseCharge : null,
                        'chargeType' => $row->chargeType,
                        'hasSpotExperience' => !empty($row->guideLocationId),
                        'isAvailable' => $isAvailable,
                        'canAssignWithoutForce' => $isAvailable,
                        'isCurrentAssigned' => ((int)$row->guideId === $currentGuideId)
                    ];
                }

                return $candidates;

            } catch (PDOException $e) {
                error_log('Database error in getTripControlGuideCandidates: ' . $e->getMessage());
                return [];
            }
        }

        private function hasAllProviderConfirmationsForTrip($userId, $tripId) {
            $this->db->query("SELECT
                    COUNT(*) AS totalRequests,
                    SUM(CASE WHEN requestStatus = 'accepted' THEN 1 ELSE 0 END) AS acceptedRequests
                FROM traveller_side_d_requests
                                WHERE tripId = :tripId
                                    AND rqUserId = :userId
                                    AND requestStatus IN ('pending', 'requested', 'accepted')");
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
                                    AND status IN ('pending', 'requested', 'accepted')");
            $this->db->bind(':tripId', (int)$tripId);
            $this->db->bind(':userId', (int)$userId);
            $guideStats = $this->db->single();

            $totalGuides = isset($guideStats->totalRequests) ? (int)$guideStats->totalRequests : 0;
            $acceptedGuides = isset($guideStats->acceptedRequests) ? (int)$guideStats->acceptedRequests : 0;

            return $acceptedGuides >= $totalGuides;
        }

        private function promoteTripToScheduledIfFullyConfirmed($userId, $tripId) {
            $this->db->query("SELECT status
                             FROM created_trips
                             WHERE tripId = :tripId AND userId = :userId
                             LIMIT 1");
            $this->db->bind(':tripId', (int)$tripId);
            $this->db->bind(':userId', (int)$userId);
            $trip = $this->db->single();

            if (!$trip || $trip->status !== 'wConfirmation') {
                return false;
            }

            if (!$this->hasAllProviderConfirmationsForTrip($userId, $tripId)) {
                return false;
            }

            $this->db->query("UPDATE created_trips
                             SET status = 'scheduled', updatedAt = NOW()
                             WHERE tripId = :tripId
                               AND userId = :userId
                               AND status = 'wConfirmation'");
            $this->db->bind(':tripId', (int)$tripId);
            $this->db->bind(':userId', (int)$userId);

            return $this->db->execute();
        }

        public function replaceTripDriverForModerator($tripId, $driverId, $forceAssign = false) {
            try {
                $trip = $this->getTripControlTripById($tripId);
                if (!$trip) {
                    return ['success' => false, 'message' => 'Trip not found'];
                }

                $driver = $this->getDriverCandidateByIdForControl($driverId);
                if (!$driver) {
                    return ['success' => false, 'message' => 'Selected driver not found'];
                }

                $availability = $this->isDriverAvailableForTrip($driverId, $tripId, $trip->startDate, $trip->endDate);
                $capacityOk = (int)$driver->vehicleCapacity >= (int)$trip->numberOfPeople;

                if ((!$availability['isAvailable'] || !$capacityOk) && !$forceAssign) {
                    return [
                        'success' => false,
                        'message' => !$availability['isAvailable']
                            ? 'Driver is unavailable for one or more trip dates. Use manual assign to override.'
                            : 'Driver vehicle capacity is below trip group size. Use manual assign to override.'
                    ];
                }

                $this->db->beginTransaction();

                $deactivateQuery = "UPDATE traveller_side_d_requests
                                    SET requestStatus = 'rejected', respondedAt = NOW(), updatedAt = NOW()
                                    WHERE tripId = :tripId
                                      AND driverId IS NOT NULL
                                      AND requestStatus IN ('requested', 'accepted', 'ongoing', 'completed', 'scheduled')";

                $this->db->query($deactivateQuery);
                $this->db->bind(':tripId', (int)$tripId);
                $this->db->execute();

                $insertQuery = "INSERT INTO traveller_side_d_requests (
                                    tripId,
                                    rqUserId,
                                    driverId,
                                    driverName,
                                    driverProfilePhoto,
                                    driverRating,
                                    verifyStatus,
                                    vehicleId,
                                    vehicleModel,
                                    vehicleYear,
                                    vehicleType,
                                    vehiclePhoto,
                                    vehicleCapacity,
                                    childSeats,
                                    requestStatus,
                                    chargeType,
                                    totalAmount,
                                    requestedAt,
                                    respondedAt,
                                    createdAt,
                                    updatedAt
                                ) VALUES (
                                    :tripId,
                                    :rqUserId,
                                    :driverId,
                                    :driverName,
                                    :driverProfilePhoto,
                                    :driverRating,
                                    :verifyStatus,
                                    :vehicleId,
                                    :vehicleModel,
                                    :vehicleYear,
                                    :vehicleType,
                                    :vehiclePhoto,
                                    :vehicleCapacity,
                                    :childSeats,
                                    'accepted',
                                    'perDay',
                                    :totalAmount,
                                    NOW(),
                                    NOW(),
                                    NOW(),
                                    NOW()
                                )";

                $this->db->query($insertQuery);
                $this->db->bind(':tripId', (int)$tripId);
                $this->db->bind(':rqUserId', (int)$trip->userId);
                $this->db->bind(':driverId', (int)$driverId);
                $this->db->bind(':driverName', $driver->driverName);
                $this->db->bind(':driverProfilePhoto', $driver->driverProfilePhoto);
                $this->db->bind(':driverRating', (float)($driver->averageRating ?? 0));
                $this->db->bind(':verifyStatus', 1);
                $this->db->bind(':vehicleId', (int)$driver->vehicleId);
                $this->db->bind(':vehicleModel', $driver->vehicleModel ?: 'Unknown');
                $this->db->bind(':vehicleYear', $driver->vehicleYear ?: null);
                $this->db->bind(':vehicleType', $driver->vehicleType ?: 'Unknown');
                $this->db->bind(':vehiclePhoto', $driver->vehiclePhoto);
                $this->db->bind(':vehicleCapacity', (int)$driver->vehicleCapacity);
                $this->db->bind(':childSeats', (int)($driver->childSeats ?? 0));
                $this->db->bind(':totalAmount', (float)($driver->totalChargePerDay ?? 0));
                $this->db->execute();

                $this->db->query("DELETE FROM driver_unavailable_dates WHERE tripId = :tripId AND reason = 'booked'");
                $this->db->bind(':tripId', (int)$tripId);
                $this->db->execute();

                $start = new DateTime($trip->startDate);
                $end = new DateTime($trip->endDate);
                if ($end < $start) {
                    $end = clone $start;
                }

                $cursor = clone $start;
                while ($cursor <= $end) {
                    $insertDateQuery = "INSERT INTO driver_unavailable_dates (driverId, unavailableDate, reason, tripId, createdAt, updatedAt)
                                        VALUES (:driverId, :unavailableDate, 'booked', :tripId, NOW(), NOW())";
                    $this->db->query($insertDateQuery);
                    $this->db->bind(':driverId', (int)$driverId);
                    $this->db->bind(':unavailableDate', $cursor->format('Y-m-d'));
                    $this->db->bind(':tripId', (int)$tripId);
                    $this->db->execute();

                    $cursor->modify('+1 day');
                }

                $this->promoteTripToScheduledIfFullyConfirmed((int)$trip->userId, (int)$tripId);

                $this->db->commit();

                return ['success' => true, 'message' => 'Driver replaced successfully'];

            } catch (Exception $e) {
                $this->db->rollback();
                error_log('Database error in replaceTripDriverForModerator: ' . $e->getMessage());
                return ['success' => false, 'message' => 'Failed to replace trip driver'];
            }
        }

        public function replaceTripGuideForModerator($tripId, $eventId, $guideId, $forceAssign = false) {
            try {
                $trip = $this->getTripControlTripById($tripId);
                if (!$trip) {
                    return ['success' => false, 'message' => 'Trip not found'];
                }

                $event = $this->getTripEventSlotForControl($tripId, $eventId);
                if (!$event) {
                    return ['success' => false, 'message' => 'Trip event not found'];
                }

                // If event doesn't have travelSpotId, try to get one from guide's locations
                $actualTravelSpotId = $event->travelSpotId;
                if (!$actualTravelSpotId) {
                    $guideLocationQuery = "SELECT spotId FROM guide_locations
                                          WHERE guideId = :guideId AND isActive = 1
                                          ORDER BY created_at ASC LIMIT 1";
                    $this->db->query($guideLocationQuery);
                    $this->db->bind(':guideId', (int)$guideId);
                    $guideLocation = $this->db->single();
                    $actualTravelSpotId = $guideLocation ? $guideLocation->spotId : null;
                }

                // If still no travelSpotId, use the first available travel spot as fallback
                if (!$actualTravelSpotId) {
                    $fallbackQuery = "SELECT spotId FROM travel_spots ORDER BY spotId ASC LIMIT 1";
                    $this->db->query($fallbackQuery);
                    $fallbackSpot = $this->db->single();
                    $actualTravelSpotId = $fallbackSpot ? $fallbackSpot->spotId : null;
                }

                if (!$actualTravelSpotId) {
                    return ['success' => false, 'message' => 'Cannot assign guide: no valid travel spot found'];
                }

                $guide = $this->getGuideCandidateByIdForControl($guideId, $actualTravelSpotId);
                if (!$guide) {
                    return ['success' => false, 'message' => 'Selected guide not found'];
                }

                $availability = $this->isGuideAvailableForEvent(
                    $guideId,
                    $tripId,
                    $event->eventDate,
                    $event->startTime ?: '00:00:00',
                    $event->endTime ?: '23:59:59'
                );

                if (!$availability['isAvailable'] && !$forceAssign) {
                    return [
                        'success' => false,
                        'message' => 'Guide is unavailable for this event slot. Use manual assign to override.'
                    ];
                }

                $this->db->beginTransaction();

                $currentGuide = $this->getCurrentEventGuide($tripId, $eventId);

                $deactivateQuery = "UPDATE traveller_side_g_requests
                                    SET status = 'rejected', respondedAt = NOW(), updatedAt = NOW()
                                    WHERE tripId = :tripId
                                      AND eventId = :eventId
                                      AND guideId IS NOT NULL
                                      AND status IN ('requested', 'accepted', 'ongoing', 'completed', 'scheduled')";

                $this->db->query($deactivateQuery);
                $this->db->bind(':tripId', (int)$tripId);
                $this->db->bind(':eventId', (int)$eventId);
                $this->db->execute();

                $insertQuery = "INSERT INTO traveller_side_g_requests (
                                    userId,
                                    tripId,
                                    eventId,
                                    travelSpotId,
                                    guideId,
                                    status,
                                    guideFullName,
                                    guideProfilePhoto,
                                    guideAverageRating,
                                    guideBio,
                                    chargeType,
                                    numberOfPeople,
                                    totalCharge,
                                    requestedAt,
                                    respondedAt,
                                    acceptedAt,
                                    createdAt,
                                    updatedAt
                                ) VALUES (
                                    :userId,
                                    :tripId,
                                    :eventId,
                                    :travelSpotId,
                                    :guideId,
                                    'accepted',
                                    :guideFullName,
                                    :guideProfilePhoto,
                                    :guideAverageRating,
                                    :guideBio,
                                    :chargeType,
                                    :numberOfPeople,
                                    :totalCharge,
                                    NOW(),
                                    NOW(),
                                    NOW(),
                                    NOW(),
                                    NOW()
                                )";

                $this->db->query($insertQuery);
                $this->db->bind(':userId', (int)$trip->userId);
                $this->db->bind(':tripId', (int)$tripId);
                $this->db->bind(':eventId', (int)$eventId);
                $this->db->bind(':travelSpotId', (int)$actualTravelSpotId);
                $this->db->bind(':guideId', (int)$guideId);
                $this->db->bind(':guideFullName', $guide->guideName);
                $this->db->bind(':guideProfilePhoto', $guide->guideProfilePhoto);
                $this->db->bind(':guideAverageRating', (float)($guide->averageRating ?? 0));
                $this->db->bind(':guideBio', $guide->bio);
                $this->db->bind(':chargeType', $guide->chargeType ?: 'whole_trip');
                $this->db->bind(':numberOfPeople', (int)$trip->numberOfPeople);
                $this->db->bind(':totalCharge', $guide->baseCharge !== null ? (float)$guide->baseCharge : null);
                $this->db->execute();

                if ($currentGuide && !empty($currentGuide->guideId)) {
                    $deleteOldBooking = "DELETE FROM guide_unavailability
                                         WHERE guideId = :oldGuideId
                                           AND tripId = :tripId
                                           AND reason = 'booked'
                                           AND unavailableDate = :eventDate
                                           AND (
                                               startTime IS NULL OR endTime IS NULL
                                               OR (:eventEnd > startTime AND :eventStart < endTime)
                                           )";

                    $this->db->query($deleteOldBooking);
                    $this->db->bind(':oldGuideId', (int)$currentGuide->guideId);
                    $this->db->bind(':tripId', (int)$tripId);
                    $this->db->bind(':eventDate', $event->eventDate);
                    $this->db->bind(':eventEnd', $event->endTime ?: '23:59:59');
                    $this->db->bind(':eventStart', $event->startTime ?: '00:00:00');
                    $this->db->execute();
                }

                $insertBooking = "INSERT INTO guide_unavailability (
                                    guideId,
                                    unavailableDate,
                                    startTime,
                                    endTime,
                                    reason,
                                    tripId,
                                    isRecurring,
                                    recurringDayOfWeek,
                                    recurringEndDate,
                                    createdAt,
                                    updatedAt
                                ) VALUES (
                                    :guideId,
                                    :eventDate,
                                    :startTime,
                                    :endTime,
                                    'booked',
                                    :tripId,
                                    0,
                                    NULL,
                                    NULL,
                                    NOW(),
                                    NOW()
                                )";

                $this->db->query($insertBooking);
                $this->db->bind(':guideId', (int)$guideId);
                $this->db->bind(':eventDate', $event->eventDate);
                $this->db->bind(':startTime', $event->startTime ?: '00:00:00');
                $this->db->bind(':endTime', $event->endTime ?: '23:59:59');
                $this->db->bind(':tripId', (int)$tripId);
                $this->db->execute();

                $this->promoteTripToScheduledIfFullyConfirmed((int)$trip->userId, (int)$tripId);

                $this->db->commit();

                return ['success' => true, 'message' => 'Guide replaced successfully'];

            } catch (Exception $e) {
                $this->db->rollback();
                error_log('Database error in replaceTripGuideForModerator: ' . $e->getMessage());
                return ['success' => false, 'message' => 'Failed to replace event guide'];
            }
        }

        public function removeTripGuideForModerator($tripId, $eventId) {
            try {
                $trip = $this->getTripControlTripById($tripId);
                if (!$trip) {
                    return ['success' => false, 'message' => 'Trip not found'];
                }

                $event = $this->getTripEventSlotForControl($tripId, $eventId);
                if (!$event) {
                    return ['success' => false, 'message' => 'Trip event not found'];
                }

                $this->db->beginTransaction();

                // Get current guide to remove from unavailability
                $currentGuide = $this->getCurrentEventGuide($tripId, $eventId);

                // Remove guide assignment from traveller_side_g_requests
                $deleteQuery = "DELETE FROM traveller_side_g_requests
                               WHERE tripId = :tripId
                                 AND eventId = :eventId
                                 AND guideId IS NOT NULL";

                $this->db->query($deleteQuery);
                $this->db->bind(':tripId', (int)$tripId);
                $this->db->bind(':eventId', (int)$eventId);
                $this->db->execute();

                // Remove guide unavailability if exists
                if ($currentGuide && !empty($currentGuide->guideId)) {
                    $deleteBooking = "DELETE FROM guide_unavailability
                                     WHERE guideId = :guideId
                                       AND tripId = :tripId
                                       AND unavailableDate = :eventDate
                                       AND (
                                           startTime IS NULL OR endTime IS NULL
                                           OR (:eventEnd > startTime AND :eventStart < endTime)
                                       )";

                    $this->db->query($deleteBooking);
                    $this->db->bind(':guideId', (int)$currentGuide->guideId);
                    $this->db->bind(':tripId', (int)$tripId);
                    $this->db->bind(':eventDate', $event->eventDate);
                    $this->db->bind(':eventEnd', $event->endTime ?: '23:59:59');
                    $this->db->bind(':eventStart', $event->startTime ?: '00:00:00');
                    $this->db->execute();
                }

                $this->db->commit();

                return ['success' => true, 'message' => 'Guide removed from event successfully'];

            } catch (Exception $e) {
                $this->db->rollback();
                error_log('Database error in removeTripGuideForModerator: ' . $e->getMessage());
                return ['success' => false, 'message' => 'Failed to remove event guide'];
            }
        }

        private function getCurrentTripDriver($tripId) {
            $query = "SELECT
                        dr.driverId,
                        dr.driverName,
                        dr.driverProfilePhoto,
                        dr.driverRating,
                        dr.vehicleId,
                        dr.vehicleModel,
                        dr.vehicleType,
                        dr.vehicleCapacity,
                        dr.requestStatus,
                        dr.totalAmount
                      FROM traveller_side_d_requests dr
                      WHERE dr.tripId = :tripId
                        AND dr.driverId IS NOT NULL
                        AND dr.requestStatus IN ('requested', 'accepted', 'ongoing', 'completed', 'scheduled')
                      ORDER BY dr.updatedAt DESC, dr.requestId DESC
                      LIMIT 1";

            $this->db->query($query);
            $this->db->bind(':tripId', (int)$tripId);
            return $this->db->single();
        }

        private function getCurrentEventGuide($tripId, $eventId) {
            $query = "SELECT
                        gr.guideId,
                        gr.guideFullName,
                        gr.guideProfilePhoto,
                        gr.guideAverageRating,
                        gr.status,
                        gr.travelSpotId,
                        ts.spotName
                      FROM traveller_side_g_requests gr
                      LEFT JOIN travel_spots ts ON ts.spotId = gr.travelSpotId
                      WHERE gr.tripId = :tripId
                        AND gr.eventId = :eventId
                        AND gr.guideId IS NOT NULL
                        AND gr.status IN ('requested', 'accepted', 'ongoing', 'completed', 'scheduled')
                      ORDER BY gr.updatedAt DESC, gr.id DESC
                      LIMIT 1";

            $this->db->query($query);
            $this->db->bind(':tripId', (int)$tripId);
            $this->db->bind(':eventId', (int)$eventId);
            return $this->db->single();
        }

        private function getTripEventSlotForControl($tripId, $eventId) {
            $query = "SELECT
                        te.eventId,
                        te.tripId,
                        te.eventDate,
                        te.startTime,
                        te.endTime,
                        te.travelSpotId,
                        te.eventType,
                        te.eventStatus,
                        te.locationName,
                        ts.spotName
                      FROM trip_events te
                      LEFT JOIN travel_spots ts ON ts.spotId = te.travelSpotId
                      WHERE te.tripId = :tripId
                        AND te.eventId = :eventId
                      LIMIT 1";

            $this->db->query($query);
            $this->db->bind(':tripId', (int)$tripId);
            $this->db->bind(':eventId', (int)$eventId);
            return $this->db->single();
        }

        private function getDriverCandidateByIdForControl($driverId) {
            $query = "SELECT
                        u.id AS driverId,
                        u.fullname AS driverName,
                        u.profile_photo AS driverProfilePhoto,
                        pd.averageRating,
                        lv.vehicleId,
                        lv.model AS vehicleModel,
                        lv.make AS vehicleType,
                        lv.year AS vehicleYear,
                        lv.seatingCapacity AS vehicleCapacity,
                        lv.childSeats,
                        lv.frontViewPhoto AS vehiclePhoto,
                        COALESCE(vp.vehicleChargePerDay, 0) + COALESCE(vp.driverChargePerDay, 0) AS totalChargePerDay
                      FROM users u
                      LEFT JOIN profile_details pd ON pd.userId = u.id
                      INNER JOIN (
                        SELECT v1.*
                        FROM vehicles v1
                        INNER JOIN (
                            SELECT driverId, MAX(vehicleId) AS latestVehicleId
                            FROM vehicles
                            GROUP BY driverId
                        ) vm ON vm.latestVehicleId = v1.vehicleId
                      ) lv ON lv.driverId = u.id
                      LEFT JOIN vehicle_pricing vp ON vp.vehicleId = lv.vehicleId
                      WHERE u.account_type = 'driver'
                        AND u.id = :driverId
                      LIMIT 1";

            $this->db->query($query);
            $this->db->bind(':driverId', (int)$driverId);
            return $this->db->single();
        }

        private function getGuideCandidateByIdForControl($guideId, $travelSpotId) {
            $query = "SELECT
                        u.id AS guideId,
                        u.fullname AS guideName,
                        u.profile_photo AS guideProfilePhoto,
                        pd.averageRating,
                        pd.bio,
                        gl.baseCharge,
                        gl.chargeType
                      FROM users u
                      LEFT JOIN profile_details pd ON pd.userId = u.id
                      LEFT JOIN guide_locations gl
                             ON gl.guideId = u.id
                            AND gl.spotId = :travelSpotId
                            AND gl.isActive = 1
                      WHERE u.account_type = 'guide'
                        AND u.id = :guideId
                      LIMIT 1";

            $this->db->query($query);
            $this->db->bind(':travelSpotId', $travelSpotId ? (int)$travelSpotId : 0);
            $this->db->bind(':guideId', (int)$guideId);
            return $this->db->single();
        }

        private function isDriverAvailableForTrip($driverId, $tripId, $startDate, $endDate) {
            $query = "SELECT COUNT(*) AS conflicts
                      FROM driver_unavailable_dates
                      WHERE driverId = :driverId
                        AND unavailableDate BETWEEN :startDate AND :endDate
                        AND (tripId IS NULL OR tripId <> :tripId)";

            $this->db->query($query);
            $this->db->bind(':driverId', (int)$driverId);
            $this->db->bind(':startDate', $startDate);
            $this->db->bind(':endDate', $endDate);
            $this->db->bind(':tripId', (int)$tripId);
            $result = $this->db->single();

            return [
                'isAvailable' => ((int)($result->conflicts ?? 0) === 0),
                'conflicts' => (int)($result->conflicts ?? 0)
            ];
        }

        private function isGuideAvailableForEvent($guideId, $tripId, $eventDate, $eventStartTime, $eventEndTime) {
            $query = "SELECT COUNT(*) AS conflicts
                      FROM guide_unavailability gu
                      WHERE gu.guideId = :guideId
                        AND gu.unavailableDate = :eventDate
                        AND (gu.tripId IS NULL OR gu.tripId <> :tripId)
                        AND (
                            gu.startTime IS NULL OR gu.endTime IS NULL
                            OR (:eventEndTime > gu.startTime AND :eventStartTime < gu.endTime)
                        )";

            $this->db->query($query);
            $this->db->bind(':guideId', (int)$guideId);
            $this->db->bind(':eventDate', $eventDate);
            $this->db->bind(':tripId', (int)$tripId);
            $this->db->bind(':eventEndTime', $eventEndTime ?: '23:59:59');
            $this->db->bind(':eventStartTime', $eventStartTime ?: '00:00:00');
            $result = $this->db->single();

            return [
                'isAvailable' => ((int)($result->conflicts ?? 0) === 0),
                'conflicts' => (int)($result->conflicts ?? 0)
            ];
        }

        public function getAllGuidesForModerator() {
            try {
                $query = "SELECT
                            u.id as guideId,
                            u.id as userId,
                            u.fullname as firstName,
                            '' as lastName,
                            u.email,
                            pd.bio as experience,
                            pd.languages,
                            '' as specialties,
                            0 as hourlyRate,
                            CASE WHEN u.verified = 1 THEN 'active' ELSE 'inactive' END as status,
                            u.created_at as createdAt
                          FROM users u
                          LEFT JOIN profile_details pd ON u.id = pd.userId
                          WHERE u.account_type = 'guide'
                          AND u.verified = 1
                          ORDER BY u.id ASC";

                $this->db->query($query);
                $guides = $this->db->resultSet();

                return [
                    'success' => true,
                    'guides' => $guides
                ];
            } catch (Exception $e) {
                error_log('Database error in getAllGuidesForModerator: ' . $e->getMessage());
                return [
                    'success' => false,
                    'message' => 'Failed to fetch guides: ' . $e->getMessage()
                ];
            }
        }

        // Complain Handling Methods
        public function getAllComplaintsGrouped() {
            try {
                $query = "SELECT
                            up.problemId,
                            up.userId,
                            up.subject,
                            up.message,
                            up.status,
                            up.completedBy,
                            up.completedAt,
                            up.createdAt,
                            up.updatedAt,
                            u.fullname AS userName,
                            u.email AS userEmail,
                            u.phone AS userPhone,
                            u.account_type AS userAccountType,
                            u.profile_photo AS userAvatar,
                            COALESCE(moderator.fullname, moderator.email, CONCAT('Moderator #', moderator.id), 'Unknown') AS handlerName
                          FROM user_problems up
                          JOIN users u ON up.userId = u.id
                          LEFT JOIN users moderator ON up.completedBy = moderator.id
                          ORDER BY
                            CASE up.status
                                WHEN 'pending' THEN 1
                                WHEN 'in_progress' THEN 2
                                WHEN 'completed' THEN 3
                            END,
                            up.createdAt DESC";

                $this->db->query($query);
                $complaints = $this->db->resultSet();

                // Group complaints by status
                $grouped = [
                    'pending' => [],
                    'in_progress' => [],
                    'completed' => []
                ];

                foreach ($complaints as $complaint) {
                    $grouped[$complaint->status][] = $complaint;
                }

                return $grouped;

            } catch (PDOException $e) {
                error_log("Database error in getAllComplaintsGrouped: " . $e->getMessage());
                return [
                    'pending' => [],
                    'in_progress' => [],
                    'completed' => []
                ];
            }
        }

        public function getComplaintDetails($problemId) {
            try {
                $query = "SELECT
                            up.problemId,
                            up.userId,
                            up.subject,
                            up.message,
                            up.status,
                            up.completedBy,
                            up.completedAt,
                            up.createdAt,
                            up.updatedAt,
                            u.fullname AS userName,
                            u.email AS userEmail,
                            u.phone AS userPhone,
                            u.secondary_phone AS userSecondaryPhone,
                            u.address AS userAddress,
                            u.account_type AS userAccountType,
                            u.profile_photo AS userAvatar,
                            u.language AS userLanguage,
                            u.dob AS userDob,
                            u.gender AS userGender,
                            u.last_login AS userLastLogin,
                            COALESCE(moderator.fullname, moderator.email, CONCAT('Moderator #', moderator.id), 'Unknown') AS handlerName,
                            moderator.email AS handlerEmail
                          FROM user_problems up
                          JOIN users u ON up.userId = u.id
                          LEFT JOIN users moderator ON up.completedBy = moderator.id
                          WHERE up.problemId = :problemId";

                $this->db->query($query);
                $this->db->bind(':problemId', $problemId);
                $complaint = $this->db->single();

                if ($complaint) {
                    error_log("getComplaintDetails: found complaint details for problemId=" . $problemId);
                } else {
                    error_log("getComplaintDetails: no complaint found for problemId=" . $problemId);
                }

                return $complaint;

            } catch (PDOException $e) {
                error_log("Database error in getComplaintDetails: " . $e->getMessage());
                return null;
            }
        }

        public function startComplaintHandling($problemId, $moderatorId) {
            try {
                $query = "UPDATE user_problems
                          SET status = 'in_progress',
                              completedBy = :moderatorId,
                              updatedAt = NOW()
                          WHERE problemId = :problemId AND status = 'pending'";

                $this->db->query($query);
                $this->db->bind(':problemId', $problemId);
                $this->db->bind(':moderatorId', $moderatorId);

                $result = $this->db->execute();
                error_log("startComplaintHandling: problemId=$problemId, moderatorId=$moderatorId, result=" . ($result ? 'success' : 'failed'));

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in startComplaintHandling: " . $e->getMessage());
                return false;
            }
        }

        public function markComplaintCompleted($problemId, $moderatorId) {
            try {
                $query = "UPDATE user_problems
                          SET status = 'completed',
                              completedBy = :moderatorId,
                              completedAt = NOW(),
                              updatedAt = NOW()
                          WHERE problemId = :problemId AND status = 'in_progress'";

                $this->db->query($query);
                $this->db->bind(':problemId', $problemId);
                $this->db->bind(':moderatorId', $moderatorId);

                $result = $this->db->execute();
                error_log("markComplaintCompleted: problemId=$problemId, moderatorId=$moderatorId, result=" . ($result ? 'success' : 'failed'));

                return $result;

            } catch (PDOException $e) {
                error_log("Database error in markComplaintCompleted: " . $e->getMessage());
                return false;
            }
        }

    }
?>