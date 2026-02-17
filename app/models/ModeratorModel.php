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

    }
?>