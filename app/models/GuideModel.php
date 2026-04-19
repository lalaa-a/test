<?php

class GuideModel {
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
            error_log('GuideModel hasColumn check failed for ' . $tableName . '.' . $columnName . ': ' . $e->getMessage());
            return false;
        }
    }

    public function getBasicGuideInfo($userId){

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

    public function loadTravelSpotCardDataBySpotId($spotId){

        error_log("Loading Travel Spot Card Data for ID: " . $spotId);
        $query = 'SELECT spotId, spotName, overview, averageRating, totalReviews FROM travel_spots WHERE spotId = :spotId';
        $this->db->query($query);
        $this->db->bind(':spotId', $spotId);

        $travelSpotMainData = $this->db->single();
        $travelSpotImageData = null;
        
        if ($travelSpotMainData) {
            // Get the first photo for this travel spot
            $queryImages = 'SELECT photoPath FROM travel_spots_photos WHERE spotId = :spotId ORDER BY createdAt ASC LIMIT 1';

            $this->db->query($queryImages);
            $this->db->bind(':spotId', $spotId);
            $travelSpotImageData = $this->db->single();
        } else {
            return null;
        }

        $resultData = [
            'spotId' => $travelSpotMainData->spotId ?? '',
            'spotName' => $travelSpotMainData->spotName ?? '',
            'overview' => $travelSpotMainData->overview ?? '',
            'averageRating' => $travelSpotMainData->averageRating ?? 0.00,
            'totalReviews' => $travelSpotMainData->totalReviews ?? 0,
            'photoPath' => $travelSpotImageData->photoPath ?? ''
        ];
        return $resultData;
    }
        
    public function updateGuidePersonalInfo($userId, $data) {
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

    public function addGuideSpot($guideId, $data) {
        try {
            error_log("addGuideSpot called with guideId: $guideId, data: " . print_r($data, true));
            
            $query = 'INSERT INTO guide_locations (guideId, spotId, baseCharge, chargeType, minGroupSize, maxGroupSize, description, photoPath, isActive, created_at) 
                      VALUES (:guideId, :spotId, :baseCharge, :chargeType, :minGroupSize, :maxGroupSize, :description, :photoPath, 0, NOW())';
            
            $this->db->query($query);
            $this->db->bind(':guideId', $guideId);
            $this->db->bind(':spotId', $data['spotId']);
            $this->db->bind(':baseCharge', $data['baseCharge']);
            $this->db->bind(':chargeType', $data['chargeType']);
            $this->db->bind(':minGroupSize', $data['minGroupSize']);
            $this->db->bind(':maxGroupSize', $data['maxGroupSize']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':photoPath', $data['photoPath'] ?? null);
            
            $result = $this->db->execute();
            error_log("Database execute result: " . ($result ? 'true' : 'false'));

            if ($result) {
                return ['success' => true, 'message' => 'Guide spot added successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to add guide spot'];
            }
        } catch (Exception $e) {
            error_log("Error adding guide spot: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to add guide spot'];
        }
    }

    public function toggleGuideSpotStatus($userId, $spotId, $isActive) {
        try {
            $query = "UPDATE guide_locations SET isActive = :isActive WHERE id = :spotId AND guideId = :userId";
            $this->db->query($query);
            $this->db->bind(':isActive', $isActive);
            $this->db->bind(':spotId', $spotId);
            $this->db->bind(':userId', $userId);
            
            $result = $this->db->execute();
            
            if ($result) {
                $status = $isActive ? 'activated' : 'deactivated';
                return ['success' => true, 'message' => "Guide spot $status successfully"];
            } else {
                return ['success' => false, 'message' => 'Guide spot not found or no changes made'];
            }
        } catch (Exception $e) {
            error_log("Error toggling guide spot status: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to toggle guide spot status'];
        }
    }

    public function getGuideLocations($guideId) {
        try {
            error_log("getGuideLocations called with guideId: $guideId");
            
            $query = "SELECT gl.id, gl.spotId, gl.baseCharge, gl.chargeType, gl.minGroupSize, gl.maxGroupSize, gl.description, gl.photoPath, gl.isActive, ts.spotName 
                      FROM guide_locations gl 
                      JOIN travel_spots ts ON gl.spotId = ts.spotId 
                      WHERE gl.guideId = :guideId 
                      ORDER BY gl.created_at DESC";
            
            $this->db->query($query);
            $this->db->bind(':guideId', $guideId);
            $results = $this->db->resultSet();
            
            error_log("Database result count: " . count($results));
            
            return $results;
        } catch (Exception $e) {
            error_log("Error getting guide locations: " . $e->getMessage());
            return [];
        }
    }

    public function updateGuideSpot($guideId, $spotId, $data) {
        try {
            error_log("updateGuideSpot called with guideId: $guideId, spotId: $spotId, data: " . print_r($data, true));
            
            $query = 'UPDATE guide_locations SET 
                      baseCharge = :baseCharge, 
                      chargeType = :chargeType, 
                      minGroupSize = :minGroupSize, 
                      maxGroupSize = :maxGroupSize, 
                      description = :description, 
                      photoPath = :photoPath 
                      WHERE guideId = :guideId AND id = :spotId';
            
            $this->db->query($query);
            $this->db->bind(':guideId', $guideId);
            $this->db->bind(':spotId', $spotId);
            $this->db->bind(':baseCharge', $data['baseCharge']);
            $this->db->bind(':chargeType', $data['chargeType']);
            $this->db->bind(':minGroupSize', $data['minGroupSize']);
            $this->db->bind(':maxGroupSize', $data['maxGroupSize']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':photoPath', $data['photoPath'] ?? null);
            
            $result = $this->db->execute();
            error_log("Database execute result: " . ($result ? 'true' : 'false'));

            if ($result) {
                return ['success' => true, 'message' => 'Guide spot updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update guide spot'];
            }
        } catch (Exception $e) {
            error_log("Error updating guide spot: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update guide spot'];
        }
    }

    public function deleteGuideSpot($guideId, $spotId) {
        try {
            error_log("deleteGuideSpot called with guideId: $guideId, spotId: $spotId");
            
            $query = 'DELETE FROM guide_locations WHERE guideId = :guideId AND id = :spotId';
            
            $this->db->query($query);
            $this->db->bind(':guideId', $guideId);
            $this->db->bind(':spotId', $spotId);
            
            $result = $this->db->execute();
            error_log("Database execute result: " . ($result ? 'true' : 'false'));

            if ($result) {
                return ['success' => true, 'message' => 'Guide spot deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to delete guide spot'];
            }
        } catch (Exception $e) {
            error_log("Error deleting guide spot: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to delete guide spot'];
        }
    }

    // -----------------------------------------------------------------------
    // Guide Unavailability methods
    // -----------------------------------------------------------------------

    public function getGuideUnavailability($guideId) {
        try {
            error_log("getGuideUnavailability called with guideId: $guideId");

            $today    = date('Y-m-d');
            $maxDate  = date('Y-m-d', strtotime('+90 days'));

            $query = 'SELECT id, guideId, unavailableDate, startTime, endTime,
                             reason, personalReason, tripId,
                             isRecurring, recurringDayOfWeek, recurringEndDate
                      FROM guide_unavailability
                      WHERE guideId = :guideId
                        AND unavailableDate >= :today
                        AND unavailableDate <= :maxDate
                      ORDER BY unavailableDate ASC, startTime ASC';

            $this->db->query($query);
            $this->db->bind(':guideId', $guideId);
            $this->db->bind(':today', $today);
            $this->db->bind(':maxDate', $maxDate);

            $rows = $this->db->resultSet();

            $slots = [];
            foreach ($rows as $row) {
                $slots[] = [
                    'id'                 => $row->id,
                    'unavailableDate'    => $row->unavailableDate,
                    'startTime'          => $row->startTime,
                    'endTime'            => $row->endTime,
                    'reason'             => $row->reason,
                    'personalReason'     => $row->personalReason,
                    'tripId'             => $row->tripId,
                    'isRecurring'        => (bool)$row->isRecurring,
                    'recurringDayOfWeek' => $row->recurringDayOfWeek,
                    'recurringEndDate'   => $row->recurringEndDate
                ];
            }

            return ['success' => true, 'unavailableSlots' => $slots];

        } catch (Exception $e) {
            error_log("Error getting guide unavailability: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to retrieve unavailability data'];
        }
    }

    public function addGuideUnavailabilitySlots($guideId, $slots) {
        try {
            error_log("addGuideUnavailabilitySlots called with guideId: $guideId, slots count: " . count($slots));

            $this->db->beginTransaction();

            $insertQuery = 'INSERT INTO guide_unavailability
                                (guideId, unavailableDate, startTime, endTime,
                                 reason, personalReason, tripId,
                                 isRecurring, recurringDayOfWeek, recurringEndDate)
                            VALUES
                                (:guideId, :unavailableDate, :startTime, :endTime,
                                 :reason, :personalReason, :tripId,
                                 :isRecurring, :recurringDayOfWeek, :recurringEndDate)';

            $insertedCount = 0;

            foreach ($slots as $slot) {
                $unavailableDate  = $slot['unavailableDate'];
                $startTime        = $slot['startTime'];
                $endTime          = $slot['endTime'];
                $reason           = in_array($slot['reason'], ['personal', 'booked']) ? $slot['reason'] : 'personal';
                $personalReason   = ($reason === 'personal') ? ($slot['personalReason'] ?? null) : null;
                $tripId           = ($reason === 'booked')   ? ($slot['tripId'] ?? null)          : null;
                $isRecurring      = !empty($slot['isRecurring']);
                $recurringEndDate = $isRecurring ? ($slot['recurringEndDate'] ?? null) : null;

                // Compute day-of-week 1-7 (Mon=1 … Sun=7) from the date
                $dow = (int)date('N', strtotime($unavailableDate)); // PHP date('N') = 1(Mon)-7(Sun)
                $recurringDayOfWeek = $isRecurring ? $dow : null;

                if ($isRecurring && $recurringEndDate) {
                    // Generate one row per matching weekday from $unavailableDate to $recurringEndDate
                    $cursor  = new DateTime($unavailableDate);
                    $endDt   = new DateTime($recurringEndDate);
                    $maxDate = new DateTime(date('Y-m-d', strtotime('+90 days')));
                    if ($endDt > $maxDate) {
                        $endDt = $maxDate;
                    }

                    while ($cursor <= $endDt) {
                        $dateStr = $cursor->format('Y-m-d');

                        $this->db->query($insertQuery);
                        $this->db->bind(':guideId',            $guideId);
                        $this->db->bind(':unavailableDate',    $dateStr);
                        $this->db->bind(':startTime',          $startTime);
                        $this->db->bind(':endTime',            $endTime);
                        $this->db->bind(':reason',             $reason);
                        $this->db->bind(':personalReason',     $personalReason);
                        $this->db->bind(':tripId',             $tripId);
                        $this->db->bind(':isRecurring',        1);
                        $this->db->bind(':recurringDayOfWeek', $recurringDayOfWeek);
                        $this->db->bind(':recurringEndDate',   $recurringEndDate);
                        $this->db->execute();
                        $insertedCount++;

                        // Advance by 7 days to the same weekday
                        $cursor->modify('+7 days');
                    }
                } else {
                    // Single one-off slot
                    $this->db->query($insertQuery);
                    $this->db->bind(':guideId',            $guideId);
                    $this->db->bind(':unavailableDate',    $unavailableDate);
                    $this->db->bind(':startTime',          $startTime);
                    $this->db->bind(':endTime',            $endTime);
                    $this->db->bind(':reason',             $reason);
                    $this->db->bind(':personalReason',     $personalReason);
                    $this->db->bind(':tripId',             $tripId);
                    $this->db->bind(':isRecurring',        0);
                    $this->db->bind(':recurringDayOfWeek', null);
                    $this->db->bind(':recurringEndDate',   null);
                    $this->db->execute();
                    $insertedCount++;
                }
            }

            $this->db->commit();
            error_log("addGuideUnavailabilitySlots: inserted $insertedCount rows");
            return ['success' => true, 'message' => "Added $insertedCount unavailability slot(s) successfully"];

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error adding guide unavailability slots: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to save unavailability slots'];
        }
    }

    public function removeGuideUnavailabilitySlot($guideId, $slotId) {
        try {
            error_log("removeGuideUnavailabilitySlot called with guideId: $guideId, slotId: $slotId");

            $query = 'DELETE FROM guide_unavailability WHERE id = :slotId AND guideId = :guideId';

            $this->db->query($query);
            $this->db->bind(':slotId',  $slotId);
            $this->db->bind(':guideId', $guideId);
            $result = $this->db->execute();

            if ($result && $this->db->rowCount() > 0) {
                return ['success' => true, 'message' => 'Unavailability slot removed successfully'];
            } else {
                return ['success' => false, 'message' => 'Slot not found or already removed'];
            }
        } catch (Exception $e) {
            error_log("Error removing guide unavailability slot: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to remove unavailability slot'];
        }
    }

    public function getRequestsByGuide($guideId) {
        try {
            // Fetch guide requests with tourist and spot information
            $query = "SELECT 
                        gsr.id,
                        gsr.userId,
                        gsr.tripId,
                        gsr.eventId,
                        gsr.travelSpotId,
                        gsr.guideId,
                        gsr.status,
                        gsr.guideFullName,
                        gsr.guideProfilePhoto,
                        gsr.guideAverageRating,
                        gsr.guideBio,
                        gsr.chargeType,
                        gsr.numberOfPeople,
                        gsr.totalCharge,
                        gsr.requestedAt,
                        gsr.respondedAt,
                        gsr.completedAt,
                        gsr.createdAt,
                        gsr.updatedAt,
                        u.fullname as userFullName,
                        u.profile_photo as userProfilePhoto,
                        u.averageRating as userAverageRating,
                        ts.spotName
                     FROM guide_side_g_requests gsr
                     LEFT JOIN vw_user_complete_profiles u ON gsr.userId = u.userId
                     LEFT JOIN travel_spots ts ON gsr.travelSpotId = ts.spotId
                     WHERE gsr.guideId = :guideId 
                     AND gsr.status IN ('pending', 'requested', 'accepted', 'rejected')
                     ORDER BY gsr.createdAt DESC";
            
            $this->db->query($query);
            $this->db->bind(':guideId', $guideId);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error fetching requests for guide $guideId: " . $e->getMessage());
            return [];
        }
    }

    public function getGuideRequestById($guideId, $requestId) {
        try {
            $query = "SELECT * FROM guide_side_g_requests 
                     WHERE guideId = :guideId AND id = :requestId LIMIT 1";
            $this->db->query($query);
            $this->db->bind(':guideId', $guideId);
            $this->db->bind(':requestId', $requestId);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error fetching request $requestId for guide $guideId: " . $e->getMessage());
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

    public function updateGuideRequestStatus($guideId, $requestId, $status) {
        try {
            // Ensure the request belongs to the guide
            $existing = $this->getGuideRequestById($guideId, $requestId);
            if (!$existing) {
                return ['success' => false, 'message' => 'Request not found or does not belong to you'];
            }

            $query = "UPDATE guide_side_g_requests 
                     SET status = :status, respondedAt = CURRENT_TIMESTAMP 
                     WHERE id = :requestId AND guideId = :guideId";
            $this->db->query($query);
            $this->db->bind(':status', $status);
            $this->db->bind(':requestId', $requestId);
            $this->db->bind(':guideId', $guideId);

            $res = $this->db->execute();
            if ($res) {
                try {
                    $syncQuery = "UPDATE traveller_side_g_requests
                                  SET status = :status,
                                      respondedAt = CURRENT_TIMESTAMP,
                                      updatedAt = CURRENT_TIMESTAMP
                                  WHERE tripId = :tripId
                                    AND eventId = :eventId
                                    AND userId = :userId
                                    AND guideId = :guideId
                                                                        AND status IN ('pending', 'requested', 'accepted', 'rejected')";
                    $this->db->query($syncQuery);
                    $this->db->bind(':status', $status);
                    $this->db->bind(':tripId', (int)$existing->tripId);
                    $this->db->bind(':eventId', (int)$existing->eventId);
                    $this->db->bind(':userId', (int)$existing->userId);
                    $this->db->bind(':guideId', (int)$guideId);
                    $this->db->execute();

                    if ($status === 'accepted') {
                        $this->promoteTripToScheduledIfReady((int)$existing->userId, (int)$existing->tripId);
                    } elseif ($status === 'rejected') {
                        $this->moveTripToPendingOnRejection((int)$existing->userId, (int)$existing->tripId);
                    }
                } catch (Exception $syncError) {
                    error_log("Guide request sync warning for request $requestId: " . $syncError->getMessage());
                }

                return ['success' => true, 'message' => 'Status updated'];
            }
            return ['success' => false, 'message' => 'Failed to update status'];
        } catch (Exception $e) {
            error_log("Error updating guide request status for $requestId: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

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
     * Guide Visits Methods
     */
    public function getGuideVisits($guideId) {
        try {
            $startPinSelect = $this->hasColumn('created_trips', 'startPin') ? 'ct.startPin AS startPin' : 'NULL AS startPin';
            $pinMatchSelect = $this->hasColumn('created_trips', 'pinMatch') ? 'ct.pinMatch AS pinMatch' : '0 AS pinMatch';

            $query = "SELECT
                        gat.acceptId,
                        gat.eventId,
                        gat.tripId,
                        gat.travelSpotId,
                        gat.guideId,
                        gat.guideFullName,
                        gat.guideProfilePhoto,
                        gat.guideAverageRating,
                        gat.guideBio,
                        gat.chargeType,
                        gat.numberOfPeople,
                        gat.totalCharge,
                        gat.doneStatus,
                        gat.paymentStatus,
                        gat.completedAt,
                        gat.createdAt,
                        gat.updatedAt,
                        ct.tripTitle,
                        ct.description,
                        ct.startDate,
                        ct.endDate,
                        ct.status AS tripStatus,
                        {$startPinSelect},
                        {$pinMatchSelect},
                        u.fullname AS rqUserName,
                        u.profile_photo AS rqUserProfilePhoto,
                        ts.spotName AS travelSpotName,
                        te.eventDate,
                        te.startTime,
                        te.endTime,
                        te.eventType,
                        te.eventStatus,
                        te.gDone
                      FROM guide_accepted_trips gat
                      LEFT JOIN created_trips ct ON gat.tripId = ct.tripId
                      LEFT JOIN users u ON ct.userId = u.id
                      LEFT JOIN trip_events te ON gat.eventId = te.eventId AND gat.tripId = te.tripId
                      LEFT JOIN travel_spots ts ON gat.travelSpotId = ts.spotId
                      WHERE gat.guideId = :guideId
                      ORDER BY gat.createdAt DESC";

            $this->db->query($query);
            $this->db->bind(':guideId', $guideId);
            $visits = $this->db->resultSet();

            $categorizedVisits = [
                'ongoing' => [],
                'upcoming' => [],
                'completed' => []
            ];

            $today = date('Y-m-d');

            foreach ($visits as $visit) {
                $visitArray = (array) $visit;
                $tripStatus = strtolower((string)($visit->tripStatus ?? ''));

                if ((int)($visit->doneStatus ?? 0) === 1 || $tripStatus === 'completed') {
                    $categorizedVisits['completed'][] = $visitArray;
                } elseif ($tripStatus === 'ongoing' || (string)($visit->startDate ?? '') === $today) {
                    $categorizedVisits['ongoing'][] = $visitArray;
                } else {
                    $categorizedVisits['upcoming'][] = $visitArray;
                }
            }

            return $categorizedVisits;
        } catch (Exception $e) {
            error_log("Error getting guide visits for guide $guideId: " . $e->getMessage());
            return [
                'ongoing' => [],
                'upcoming' => [],
                'completed' => []
            ];
        }
    }

    public function getVisitDetails($guideId, $acceptId) {
        try {
            $startPinSelect = $this->hasColumn('created_trips', 'startPin') ? 'ct.startPin AS startPin' : 'NULL AS startPin';
            $pinMatchSelect = $this->hasColumn('created_trips', 'pinMatch') ? 'ct.pinMatch AS pinMatch' : '0 AS pinMatch';

            $query = "SELECT
                        gat.acceptId,
                        gat.eventId,
                        gat.tripId,
                        gat.travelSpotId,
                        gat.guideId,
                        gat.guideFullName,
                        gat.guideProfilePhoto,
                        gat.guideAverageRating,
                        gat.guideBio,
                        gat.chargeType,
                        gat.numberOfPeople,
                        gat.totalCharge,
                        gat.doneStatus,
                        gat.paymentStatus,
                        gat.completedAt,
                        gat.createdAt,
                        gat.updatedAt,
                        ct.tripTitle,
                        ct.description,
                        ct.startDate,
                        ct.endDate,
                        ct.status AS tripStatus,
                        {$startPinSelect},
                        {$pinMatchSelect},
                        u.fullname AS rqUserName,
                        u.profile_photo AS rqUserProfilePhoto,
                        ts.spotName AS travelSpotName,
                        te.eventDate,
                        te.startTime,
                        te.endTime,
                        te.eventType,
                        te.eventStatus,
                        te.gDone
                      FROM guide_accepted_trips gat
                      LEFT JOIN created_trips ct ON gat.tripId = ct.tripId
                      LEFT JOIN users u ON ct.userId = u.id
                      LEFT JOIN trip_events te ON gat.eventId = te.eventId AND gat.tripId = te.tripId
                      LEFT JOIN travel_spots ts ON gat.travelSpotId = ts.spotId
                      WHERE gat.acceptId = :acceptId
                        AND gat.guideId = :guideId
                      LIMIT 1";

            $this->db->query($query);
            $this->db->bind(':acceptId', $acceptId);
            $this->db->bind(':guideId', $guideId);
            $visit = $this->db->single();

            if (!$visit) {
                return null;
            }

            return (array) $visit;
        } catch (Exception $e) {
            error_log("Error getting visit details for acceptId $acceptId: " . $e->getMessage());
            return null;
        }
    }

    public function getVisitEvents($guideId, $tripId) {
        try {
            $hasStartPin = $this->hasColumn('created_trips', 'startPin');
            $hasPinMatch = $this->hasColumn('created_trips', 'pinMatch');
            $startPinSelect = $hasStartPin ? 'ct.startPin' : "'' AS startPin";
            $pinMatchSelect = $hasPinMatch ? 'ct.pinMatch' : '0 AS pinMatch';
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

            $tripSelect = "SELECT ct.tripId,
                                  ct.status,
                                  {$startPinSelect},
                                  {$pinMatchSelect}
                           FROM created_trips ct
                           INNER JOIN guide_accepted_trips gat ON gat.tripId = ct.tripId
                           WHERE ct.tripId = :tripId
                             AND gat.guideId = :guideId
                           LIMIT 1";

            $this->db->query($tripSelect);
            $this->db->bind(':tripId', $tripId);
            $this->db->bind(':guideId', $guideId);
            $trip = $this->db->single();

            if (!$trip) {
                return ['success' => false, 'message' => 'Trip not found for this guide', 'events' => []];
            }

            $query = "SELECT DISTINCT
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
                        te.tDoneGuide,
                        ts.spotName,
                        ts.overview as spotDescription,
                        ts.averageRating
                      FROM trip_events te
                      INNER JOIN guide_accepted_trips gat ON gat.eventId = te.eventId AND gat.tripId = te.tripId
                      {$itineraryJoin}
                      LEFT JOIN travel_spots ts ON te.travelSpotId = ts.spotId
                      WHERE gat.tripId = :tripId
                        AND gat.guideId = :guideId
                      ORDER BY te.eventDate ASC, te.startTime ASC";

            $this->db->query($query);
            $this->db->bind(':tripId', $tripId);
            $this->db->bind(':guideId', $guideId);
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
            error_log("Error getting visit events for trip $tripId: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred', 'events' => []];
        }
    }

    public function markVisitEventComplete($guideId, $eventId) {
        try {
            $hasPinMatch = $this->hasColumn('created_trips', 'pinMatch');

            $eventMetaQuery = $hasPinMatch
                ? "SELECT te.eventId, te.tripId, te.gDone, ct.status, ct.pinMatch
                   FROM trip_events te
                   INNER JOIN guide_accepted_trips gat ON gat.eventId = te.eventId AND gat.tripId = te.tripId
                   INNER JOIN created_trips ct ON ct.tripId = te.tripId
                   WHERE te.eventId = :eventId
                     AND gat.guideId = :guideId
                   LIMIT 1"
                : "SELECT te.eventId, te.tripId, te.gDone, ct.status
                   FROM trip_events te
                   INNER JOIN guide_accepted_trips gat ON gat.eventId = te.eventId AND gat.tripId = te.tripId
                   INNER JOIN created_trips ct ON ct.tripId = te.tripId
                   WHERE te.eventId = :eventId
                     AND gat.guideId = :guideId
                   LIMIT 1";

            $this->db->query($eventMetaQuery);
            $this->db->bind(':eventId', $eventId);
            $this->db->bind(':guideId', $guideId);
            $eventMeta = $this->db->single();

            if (!$eventMeta) {
                return ['success' => false, 'message' => 'Event not found for this guide'];
            }

            if ((int)($eventMeta->gDone ?? 0) === 1) {
                return ['success' => true, 'message' => 'Event already marked as completed'];
            }

            if (strtolower((string)($eventMeta->status ?? '')) !== 'ongoing') {
                return ['success' => false, 'message' => 'Trip must be ongoing before marking events'];
            }

            if ($hasPinMatch && (int)($eventMeta->pinMatch ?? 0) !== 1) {
                return ['success' => false, 'message' => 'Traveller PIN must be matched before marking guide events'];
            }

            $query = "UPDATE trip_events
                      SET gDone = 1,
                          updated_at = CURRENT_TIMESTAMP
                      WHERE eventId = :eventId
                        AND tripId = :tripId";
            $this->db->query($query);
            $this->db->bind(':eventId', $eventId);
            $this->db->bind(':tripId', $eventMeta->tripId);

            if ($this->db->execute()) {
                return ['success' => true, 'message' => 'Event marked as completed'];
            }

            return ['success' => false, 'message' => 'Failed to mark event as completed'];
        } catch (Exception $e) {
            error_log("Error marking guide event $eventId as complete: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    public function getEarningsSummary($guideId) {
        try {
            $pendingQuery = "SELECT COUNT(*) AS pending_count, COALESCE(SUM(totalCharge), 0) AS pending_amount
                             FROM guide_accepted_trips
                             WHERE guideId = :guideId AND paymentStatus = 'pending'";
            $this->db->query($pendingQuery);
            $this->db->bind(':guideId', $guideId);
            $pending = $this->db->single();

                        $paidQuery = "SELECT COUNT(*) AS paid_count, COALESCE(SUM(guideCharge), 0) AS paid_amount
                                                    FROM guide_payments
                                                    WHERE guideId = :guideId
                                                        AND (COALESCE(pDoneTraveller, 0) = 1 OR COALESCE(pDoneSite, 0) = 1)
                                                        AND COALESCE(refunded, 0) = 0";
            $this->db->query($paidQuery);
            $this->db->bind(':guideId', $guideId);
            $paid = $this->db->single();

            $refundedQuery = "SELECT COUNT(*) AS refunded_count,
                                     COALESCE(SUM(COALESCE(refundAmount, guideCharge)), 0) AS refunded_amount
                              FROM guide_payments
                              WHERE guideId = :guideId AND refunded = 1";
            $this->db->query($refundedQuery);
            $this->db->bind(':guideId', $guideId);
            $refunded = $this->db->single();

            $pendingCount = (int)($pending->pending_count ?? 0);
            $pendingAmount = (float)($pending->pending_amount ?? 0);
            $paidCount = (int)($paid->paid_count ?? 0);
            $paidAmount = (float)($paid->paid_amount ?? 0);
            $refundedCount = (int)($refunded->refunded_count ?? 0);
            $refundedAmount = (float)($refunded->refunded_amount ?? 0);

            error_log("Earnings summary for guide $guideId - Pending: $pendingCount ($pendingAmount), Paid: $paidCount ($paidAmount), Refunded: $refundedCount ($refundedAmount)");

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
            error_log("Error getting guide earnings summary: " . $e->getMessage());
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

    public function getEarningsByStatus($guideId, $status) {
        try {
            if ($status === 'pending') {
                $query = "SELECT
                            gat.tripId,
                            COALESCE(gp.guideCharge, gat.totalCharge) AS guideCharge,
                            gat.totalCharge AS totalTripCharge,
                            gat.createdAt
                          FROM guide_accepted_trips gat
                          LEFT JOIN trip_payments tp ON gat.tripId = tp.tripId
                          LEFT JOIN guide_payments gp ON tp.wholePaymentId = gp.wholePaymentId AND gp.guideId = gat.guideId
                          WHERE gat.guideId = :guideId
                            AND gat.paymentStatus = 'pending'
                          ORDER BY gat.createdAt DESC";

                $this->db->query($query);
                $this->db->bind(':guideId', $guideId);
                return $this->db->resultSet();
            }

            if ($status === 'paid') {
                $query = "SELECT
                            COALESCE(tp.tripId, gat.tripId) AS tripId,
                            gp.guideCharge,
                            gp.pDoneSite,
                            gp.pDateTraveller,
                            gp.pDateSite,
                            gp.createdAt
                          FROM guide_payments gp
                          LEFT JOIN trip_payments tp ON gp.wholePaymentId = tp.wholePaymentId
                          LEFT JOIN guide_accepted_trips gat ON gat.tripId = tp.tripId AND gat.guideId = gp.guideId
                                                    WHERE gp.guideId = :guideId
                                                        AND (COALESCE(gp.pDoneTraveller, 0) = 1 OR COALESCE(gp.pDoneSite, 0) = 1)
                                                        AND COALESCE(gp.refunded, 0) = 0
                          ORDER BY COALESCE(gp.pDateTraveller, gp.createdAt) DESC";

                $this->db->query($query);
                $this->db->bind(':guideId', $guideId);
                return $this->db->resultSet();
            }

            if ($status === 'refunded') {
                $query = "SELECT
                            COALESCE(tp.tripId, gat.tripId) AS tripId,
                            gp.guideCharge,
                            gp.refundAmount,
                            gp.refundDate,
                            gp.refundReason,
                            gp.pDoneSite,
                            gp.createdAt
                          FROM guide_payments gp
                          LEFT JOIN trip_payments tp ON gp.wholePaymentId = tp.wholePaymentId
                          LEFT JOIN guide_accepted_trips gat ON gat.tripId = tp.tripId AND gat.guideId = gp.guideId
                          WHERE gp.guideId = :guideId
                            AND gp.refunded = 1
                          ORDER BY COALESCE(gp.refundDate, gp.createdAt) DESC";

                $this->db->query($query);
                $this->db->bind(':guideId', $guideId);
                return $this->db->resultSet();
            }

            return [];
        } catch (Exception $e) {
            error_log("Error getting guide earnings by status ({$status}): " . $e->getMessage());
            return [];
        }
    }

    public function getMonthlyEarnings($guideId) {
        try {
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
                                    COALESCE(SUM(totalCharge), 0) AS amount
                             FROM guide_accepted_trips
                             WHERE guideId = :guideId
                               AND paymentStatus = 'pending'
                               AND createdAt >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                             GROUP BY DATE_FORMAT(createdAt, '%Y-%m')";
            $this->db->query($pendingQuery);
            $this->db->bind(':guideId', $guideId);
            $pendingRows = $this->db->resultSet();

            foreach ($pendingRows as $row) {
                if (isset($months[$row->monthKey])) {
                    $months[$row->monthKey]['pending'] = (float)$row->amount;
                }
            }

            $paidQuery = "SELECT DATE_FORMAT(COALESCE(pDateTraveller, createdAt), '%Y-%m') AS monthKey,
                                 COALESCE(SUM(guideCharge), 0) AS amount
                          FROM guide_payments
                                                    WHERE guideId = :guideId
                                                        AND (COALESCE(pDoneTraveller, 0) = 1 OR COALESCE(pDoneSite, 0) = 1)
                                                        AND COALESCE(refunded, 0) = 0
                            AND COALESCE(pDateTraveller, createdAt) >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                          GROUP BY DATE_FORMAT(COALESCE(pDateTraveller, createdAt), '%Y-%m')";
            $this->db->query($paidQuery);
            $this->db->bind(':guideId', $guideId);
            $paidRows = $this->db->resultSet();

            foreach ($paidRows as $row) {
                if (isset($months[$row->monthKey])) {
                    $months[$row->monthKey]['paid'] = (float)$row->amount;
                }
            }

            $refundedQuery = "SELECT DATE_FORMAT(COALESCE(refundDate, createdAt), '%Y-%m') AS monthKey,
                                     COALESCE(SUM(COALESCE(refundAmount, guideCharge)), 0) AS amount
                              FROM guide_payments
                              WHERE guideId = :guideId
                                AND refunded = 1
                                AND COALESCE(refundDate, createdAt) >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                              GROUP BY DATE_FORMAT(COALESCE(refundDate, createdAt), '%Y-%m')";
            $this->db->query($refundedQuery);
            $this->db->bind(':guideId', $guideId);
            $refundedRows = $this->db->resultSet();

            foreach ($refundedRows as $row) {
                if (isset($months[$row->monthKey])) {
                    $months[$row->monthKey]['refunded'] = (float)$row->amount;
                }
            }

            return array_values($months);
        } catch (Exception $e) {
            error_log("Error getting guide monthly earnings: " . $e->getMessage());
            return [];
        }
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