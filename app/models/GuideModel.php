<?php

class GuideModel {
    private $db;

    public function __construct(){
        $this->db = new Database();
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
            $query = "SELECT te.eventId, te.tripId, te.userId, te.eventDate, te.startTime, te.endTime, te.eventType, te.eventStatus, 
                             te.travelSpotId, te.locationName, te.latitude, te.longitude, te.description, te.created_at, te.updated_at,
                             ts.spotName, ts.overview as spotDescription, ts.averageRating
                     FROM trip_events te
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
}