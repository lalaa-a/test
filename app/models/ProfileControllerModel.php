<?php
    class ProfileControllerModel{
        private $db;

        public function __construct(){
            $this->db = new Database;
        }

        //account types: 'driver', 'guide'
        public function getAccounts($accountType){
            $this->db->query('SELECT userId, account_type, fullname, dob, gender, phone, secondary_phone, address, email, profile_photo, driver_data, bio, languages, instaAccount, facebookAccount, dlVerified, tlVerified, averageRating, profile_created_at, profile_updated_at, age FROM vw_user_complete_profiles WHERE account_type = :account_type');
            $this->db->bind(':account_type', $accountType);
            $results = $this->db->resultSet();
            return $results ? $results : [];
        }

        public function getAccountsWithFilters($accountType = null, $filters = []) {
            // Base query
            $query = 'SELECT userId, account_type, fullname, dob, gender, phone, secondary_phone, address, email, profile_photo, driver_data, bio, languages, instaAccount, facebookAccount, dlVerified, tlVerified, averageRating, profile_created_at, profile_updated_at, age FROM vw_user_complete_profiles WHERE 1=1';

            $bindParams = [];

            // Add account type filter if specified
            if ($accountType) {
                $query .= ' AND account_type = :account_type';
                $bindParams[':account_type'] = $accountType;
            }

            // Apply additional filters
            if (!empty($filters)) {
                if (isset($filters['gender']) && $filters['gender'] !== 'all') {
                    $query .= ' AND gender = :gender';
                    $bindParams[':gender'] = $filters['gender'];
                }

                if (isset($filters['verification_status'])) {
                    switch ($filters['verification_status']) {
                        case 'verified':
                            $query .= ' AND ((account_type = "driver" AND dlVerified = 1) OR (account_type = "guide" AND tlVerified = 1) OR (account_type = "tourist" AND tlVerified = 1))';
                            break;
                        case 'unverified':
                            $query .= ' AND ((account_type = "driver" AND dlVerified = 0) OR (account_type = "guide" AND tlVerified = 0) OR (account_type = "tourist" AND tlVerified = 0))';
                            break;
                    }
                }

                if (isset($filters['rating_min'])) {
                    $query .= ' AND averageRating >= :rating_min';
                    $bindParams[':rating_min'] = $filters['rating_min'];
                }

                if (isset($filters['age_min'])) {
                    $query .= ' AND age >= :age_min';
                    $bindParams[':age_min'] = $filters['age_min'];
                }

                if (isset($filters['age_max'])) {
                    $query .= ' AND age <= :age_max';
                    $bindParams[':age_max'] = $filters['age_max'];
                }

                if (isset($filters['language']) && !empty($filters['language'])) {
                    $query .= ' AND languages LIKE :language';
                    $bindParams[':language'] = '%' . $filters['language'] . '%';
                }
            }

            $query .= ' ORDER BY profile_created_at DESC';

            $this->db->query($query);

            // Bind all parameters
            foreach ($bindParams as $param => $value) {
                $this->db->bind($param, $value);
            }

            $results = $this->db->resultSet();
            return $results ? $results : [];
        }

        public function getAccountsByMainFilters($accountType = null) {
            $accounts = $this->getAccountsWithFilters($accountType);

            $mainFilters = [
                'all' => [
                    'name' => 'All Accounts',
                    'accounts' => $accounts,
                    'count' => count($accounts)
                ],
                'verified' => [
                    'name' => 'Verified Accounts',
                    'accounts' => array_filter($accounts, function($account) {
                        if ($account->account_type === 'driver') {
                            return $account->dlVerified == 1;
                        } elseif ($account->account_type === 'guide' || $account->account_type === 'tourist') {
                            return $account->tlVerified == 1;
                        }
                        return false;
                    }),
                    'count' => 0
                ],
                'unverified' => [
                    'name' => 'Unverified Accounts',
                    'accounts' => array_filter($accounts, function($account) {
                        if ($account->account_type === 'driver') {
                            return $account->dlVerified == 0;
                        } elseif ($account->account_type === 'guide' || $account->account_type === 'tourist') {
                            return $account->tlVerified == 0;
                        }
                        return false;
                    }),
                    'count' => 0
                ],
                'high_rated' => [
                    'name' => 'High Rated (4.0+)',
                    'accounts' => array_filter($accounts, function($account) {
                        return $account->averageRating >= 4.0;
                    }),
                    'count' => 0
                ],
                'male' => [
                    'name' => 'Male',
                    'accounts' => array_filter($accounts, function($account) {
                        return strtolower($account->gender) === 'male';
                    }),
                    'count' => 0
                ],
                'female' => [
                    'name' => 'Female',
                    'accounts' => array_filter($accounts, function($account) {
                        return strtolower($account->gender) === 'female';
                    }),
                    'count' => 0
                ],
                'young_adults' => [
                    'name' => 'Young Adults (18-30)',
                    'accounts' => array_filter($accounts, function($account) {
                        return $account->age >= 18 && $account->age <= 30;
                    }),
                    'count' => 0
                ],
                'adults' => [
                    'name' => 'Adults (31-50)',
                    'accounts' => array_filter($accounts, function($account) {
                        return $account->age >= 31 && $account->age <= 50;
                    }),
                    'count' => 0
                ],
                'experienced' => [
                    'name' => 'Experienced (50+)',
                    'accounts' => array_filter($accounts, function($account) {
                        return $account->age > 50;
                    }),
                    'count' => 0
                ]
            ];

            // Calculate counts for filtered arrays
            foreach ($mainFilters as $key => &$filter) {
                if ($key !== 'all') {
                    $filter['accounts'] = array_values($filter['accounts']); // Reindex array
                    $filter['count'] = count($filter['accounts']);
                }
            }

            return $mainFilters;
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
                // First, update profile_details table as before
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

                $profileUpdateResult = $this->db->execute();

                error_log("submitTouristLicense profile update executed. Result: " . ($profileUpdateResult ? 'true' : 'false'));

                // Now insert into tlicense_verifications table
                $verificationQuery = "INSERT INTO tlicense_verifications (userId, status, expiryDate, createdAt, updatedAt) VALUES (:userId, 'pending', :expiryDate, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";

                $this->db->query($verificationQuery);
                $this->db->bind(':userId', $userId);
                $this->db->bind(':expiryDate', $data['tLicenseExpiryDate'] ?? null);

                $verificationInsertResult = $this->db->execute();

                error_log("submitTouristLicense verification insert executed. Result: " . ($verificationInsertResult ? 'true' : 'false'));

                // Return true only if both operations succeed
                return $profileUpdateResult && $verificationInsertResult;

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
            // Ensure tlVerified is set to 0 when updating license
            $data['tlVerified'] = 0;

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

            $profileUpdateResult = $this->db->execute();

            error_log("updateTouristLicense profile update executed. Result: " . ($profileUpdateResult ? 'true' : 'false'));

            // Update the tlicense_verifications table status to pending
            $verificationQuery = "UPDATE tlicense_verifications SET status = 'pending', updatedAt = CURRENT_TIMESTAMP WHERE userId = :userId";

            $this->db->query($verificationQuery);
            $this->db->bind(':userId', $userId);

            $verificationUpdateResult = $this->db->execute();

            error_log("updateTouristLicense verification update executed. Result: " . ($verificationUpdateResult ? 'true' : 'false'));

            // Return true only if both operations succeed
            return $profileUpdateResult && $verificationUpdateResult;

        } catch (Exception $e) {
            error_log("Error updating tourist license: " . $e->getMessage());
            return false;
        }
    }

    public function getDriverReviews($driverId) {
        $this->db->query("
            SELECT 
                tr.reviewId,
                tr.reviewText,
                tr.createdAt,
                u.fullname as travellerName,
                u.profile_photo as travellerPhoto
            FROM traveller_reviews tr
            JOIN users u ON tr.travellerId = u.id
            WHERE tr.guideDriverId = :driverId
            ORDER BY tr.createdAt DESC
        ");
        $this->db->bind(':driverId', $driverId);
        $results = $this->db->resultSet();
        return $results ? $results : [];
    }

    public function submitReview($travellerId, $driverId, $reviewText) {
        $this->db->query("
            INSERT INTO traveller_reviews (travellerId, guideDriverId, reviewText, createdAt, updatedAt) 
            VALUES (:travellerId, :driverId, :reviewText, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ");
        $this->db->bind(':travellerId', $travellerId);
        $this->db->bind(':driverId', $driverId);
        $this->db->bind(':reviewText', $reviewText);
        return $this->db->execute();
    }

    public function getDriverRatings($driverId) {
        $this->db->query("
            SELECT 
                spr.ratingId,
                spr.rating,
                spr.createdAt,
                u.fullname as travellerName,
                u.profile_photo as travellerPhoto
            FROM serviceProvider_ratings spr
            JOIN users u ON spr.travellerId = u.id
            WHERE spr.serviceProviderId = :driverId
            ORDER BY spr.createdAt DESC
        ");
        $this->db->bind(':driverId', $driverId);
        $results = $this->db->resultSet();
        return $results ? $results : [];
    }

    public function submitRating($travellerId, $driverId, $rating) {
        // Check if rating already exists for this traveller-driver pair
        $this->db->query("
            SELECT ratingId FROM serviceProvider_ratings 
            WHERE travellerId = :travellerId AND serviceProviderId = :driverId
        ");
        $this->db->bind(':travellerId', $travellerId);
        $this->db->bind(':driverId', $driverId);
        $existingRating = $this->db->single();

        if ($existingRating) {
            // Update existing rating
            $this->db->query("
                UPDATE serviceProvider_ratings 
                SET rating = :rating, updatedAt = CURRENT_TIMESTAMP 
                WHERE ratingId = :ratingId
            ");
            $this->db->bind(':rating', $rating);
            $this->db->bind(':ratingId', $existingRating->ratingId);
        } else {
            // Insert new rating
            $this->db->query("
                INSERT INTO serviceProvider_ratings (serviceProviderId, travellerId, rating, createdAt, updatedAt) 
                VALUES (:driverId, :travellerId, :rating, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
            ");
            $this->db->bind(':driverId', $driverId);
            $this->db->bind(':travellerId', $travellerId);
            $this->db->bind(':rating', $rating);
        }
        
        return $this->db->execute();
    }
}
