<?php
// app/models/UserModel.php

class UserModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function emailExists($email) {
        $this->db->query('SELECT id FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        return $this->db->rowCount() > 0;
    }

    public function create($data) {
        $query = "INSERT INTO users (
            account_type, fullname, language, dob, gender, phone, secondary_phone,
            address, email, password, profile_photo, driver_data, guide_tourist_data
        ) VALUES (
            :account_type, :fullname, :language, :dob, :gender, :phone, :secondary_phone,
            :address, :email, :password, :profile_photo, :driver_data, :guide_tourist_data
        )";

        $this->db->query($query);
        $this->db->bind(':account_type', $data['account_type']);
        $this->db->bind(':fullname', $data['fullname']);
        $this->db->bind(':language', $data['language']);
        $this->db->bind(':dob', $data['dob']);
        $this->db->bind(':gender', $data['gender']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':secondary_phone', $data['secondary_phone'] ?? null);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':profile_photo', $data['profile_photo']);
        $this->db->bind(':driver_data', $data['driver_data'] ?? null);
        $this->db->bind(':guide_tourist_data', $data['guide_tourist_data'] ?? null);

        return $this->db->execute();
    }

    public function findByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    public function findById($id) {
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function updateLastLogin($userId) {
        $this->db->query('UPDATE users SET last_login = NOW() WHERE id = :id');
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }

    //updating remember me token
    public function updateRememberToken($userId, $tokenHash, $expiresAt) {
        
        /*
        $this->db->query('UPDATE users SET remember_token = :token WHERE id = :id');
        $this->db->bind(':token', $token);
        $this->db->bind(':id', $userId);
        return $this->db->execute();
        */

        $this->db->query("INSERT INTO user_remember_tokens (user_id, token_hash, expires_at) VALUES (:user_id, :token_hash, :expires_at)");
        $this->db->bind(':user_id',$userId);
        $this->db->bind(':token_hash',$tokenHash);
        $this->db->bind(':expires_at',$expiresAt);
        $this->db->execute();
    }

    //find the remember me token from the database
    public function findByRememberToken($token) {

        $this->db->query("SELECT user_id, token_hash FROM user_remember_tokens WHERE expires_at > NOW()");
        $tokens = $this->db->resultSet();
        
        foreach($tokens as $stored_token){
            if(password_verify($token,$stored_token->token_hash)){
                return $stored_token->user_id;
            }
        }
        return false;
    }

    public function deleteRememberToken($token){
        $this->db->query("SELECT id, user_id, token_hash FROM user_remember_tokens");
        $tokens = $this->db->resultSet();
        
        foreach($tokens as $stored_token){
            if(password_verify($token,$stored_token->token_hash)){
                $this->db->query("DELETE FROM user_remember_tokens WHERE id = :id");
                $this->db->bind(':id',$stored_token->id);
                $this->db->execute();
                break;
            }
        }
    }

    // Moderator management methods
    public function findUserByEmail($email) {
        return $this->findByEmail($email);
    }

    public function addModerator($data) {
        $query = "INSERT INTO users (
            account_type, fullname, language, dob, gender, phone, secondary_phone,
            address, email, password
        ) VALUES (
            :account_type, :fullname, :language, :dob, :gender, :phone, :secondary_phone,
            :address, :email, :password
        )";

        $this->db->query($query);
        $this->db->bind(':account_type', $data['account_type']);
        $this->db->bind(':fullname', $data['fullname']);
        $this->db->bind(':language', $data['language']);
        $this->db->bind(':dob', $data['dob']);
        $this->db->bind(':gender', $data['gender']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':secondary_phone', $data['secondary_phone'] ?? null);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);

        return $this->db->execute();
    }

    public function getModerators() {
        $this->db->query('SELECT id, account_type, fullname, email, phone, secondary_phone, address, 
                         language, dob, gender, created_at, last_login 
                         FROM users 
                         WHERE account_type IN ("site_moderator", "business_manager") 
                         ORDER BY created_at DESC');
        return $this->db->resultSet();
    }

    public function getModeratorById($id) {
        $this->db->query('SELECT id, account_type, fullname, email, phone, secondary_phone, address, 
                         language, dob, gender, created_at, last_login 
                         FROM users 
                         WHERE id = :id AND account_type IN ("site_moderator", "business_manager")');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function updateModerator($data) {
        
        $query = "UPDATE users SET 
                  account_type = :account_type,
                  fullname = :fullname,
                  email = :email,
                  phone = :phone,
                  secondary_phone = :secondary_phone,
                  language = :language,
                  gender = :gender,
                  dob = :dob,
                  address = :address,
                  updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id AND account_type IN ('site_moderator', 'business_manager')";

        $this->db->query($query);
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':account_type', $data['account_type']);
        $this->db->bind(':fullname', $data['fullname']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':secondary_phone', $data['secondary_phone'] ?? null);
        $this->db->bind(':language', $data['language']);
        $this->db->bind(':gender', $data['gender']);
        $this->db->bind(':dob', $data['dob']);
        $this->db->bind(':address', $data['address']);

        return $this->db->execute();
    }

    public function deleteModerator($id) {
        // First check if the user is actually a moderator
        $this->db->query('SELECT account_type FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        $user = $this->db->single();
        
        if (!$user || !in_array($user->account_type, ['site_moderator', 'business_manager'])) {
            return false;
        }

        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // User account management methods
    public function updateUser($data) {
        $query = "UPDATE users SET 
                  fullname = :fullname,
                  email = :email,
                  phone = :phone,
                  secondary_phone = :secondary_phone,
                  language = :language,
                  gender = :gender,
                  dob = :dob,
                  address = :address,
                  updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";

        $this->db->query($query);
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':fullname', $data['fullname']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':secondary_phone', $data['secondary_phone'] ?? null);
        $this->db->bind(':language', $data['language']);
        $this->db->bind(':gender', $data['gender']);
        $this->db->bind(':dob', $data['dob']);
        $this->db->bind(':address', $data['address']);

        return $this->db->execute();
    }

    public function updateProfilePhoto($userId, $profilePhoto) {
        $this->db->query('UPDATE users SET profile_photo = :profile_photo, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        $this->db->bind(':profile_photo', $profilePhoto);
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }

    public function getItineraryDetails($itineraryNumber) {
        // For demonstration purposes, I'm creating a mock query structure
        // In a real application, you would have actual itinerary, guide_assignments, and driver_assignments tables
        
        // Mock data structure - replace with actual database query
        $mockItineraries = [
            'ITN-001' => [
                'itinerary_number' => 'ITN-001',
                'traveller_name' => 'Vihanga Perera',
                'traveller_email' => 'vihanga@email.com',
                'duration' => '7 days',
                'destinations' => 'Colombo, Kandy, Galle',
                'total_cost' => 'Rs. 375,000',
                'status' => 'Confirmed',
                'created_date' => '2024-01-15',
                'guide_id' => 'G001',
                'guide_name' => 'Sewmini Fernando',
                'guide_phone' => '+94 71 234 5678',
                'guide_email' => 'sewmini@email.com',
                'guide_languages' => 'English, Sinhala',
                'driver_id' => 'D001',
                'driver_name' => 'Akila Ponnamperuma',
                'driver_phone' => '+94 77 123 4567',
                'driver_email' => 'akila@email.com',
                'driver_license' => 'B1234567',
                'vehicle_number' => 'CAB-1234'
            ],
            'ITN-002' => [
                'itinerary_number' => 'ITN-002',
                'traveller_name' => 'Ridama Silva',
                'traveller_email' => 'ridama@email.com',
                'duration' => '5 days',
                'destinations' => 'Sigiriya, Dambulla, Anuradhapura',
                'total_cost' => 'Rs. 280,000',
                'status' => 'Pending',
                'created_date' => '2024-01-18',
                'guide_id' => 'G002',
                'guide_name' => 'Chamara Jayasinghe',
                'guide_phone' => '+94 70 987 6543',
                'guide_email' => 'chamara@email.com',
                'guide_languages' => 'English, Sinhala, Tamil',
                'driver_id' => 'D002',
                'driver_name' => 'Nuwan Bandara',
                'driver_phone' => '+94 76 555 4321',
                'driver_email' => 'nuwan@email.com',
                'driver_license' => 'B7654321',
                'vehicle_number' => 'CAB-5678'
            ],
            'ITN-003' => [
                'itinerary_number' => 'ITN-003',
                'traveller_name' => 'Pevindi Rajapaksa',
                'traveller_email' => 'pevindi@email.com',
                'duration' => '3 days',
                'destinations' => 'Ella, Nuwara Eliya',
                'total_cost' => 'Rs. 150,000',
                'status' => 'Completed',
                'created_date' => '2024-01-10',
                'guide_id' => 'G003',
                'guide_name' => 'Sanduni Wickramasinghe',
                'guide_phone' => '+94 72 111 2222',
                'guide_email' => 'sanduni@email.com',
                'guide_languages' => 'English, Sinhala',
                'driver_id' => 'D003',
                'driver_name' => 'Kasun Perera',
                'driver_phone' => '+94 75 333 4444',
                'driver_email' => 'kasun@email.com',
                'driver_license' => 'B1111222',
                'vehicle_number' => 'CAB-9999'
            ]
        ];

        // Return the mock data if itinerary exists
        if (isset($mockItineraries[$itineraryNumber])) {
            return $mockItineraries[$itineraryNumber];
        }

        // In a real implementation, you would use something like this:
        /*
        $query = "SELECT 
                    i.itinerary_number,
                    i.traveller_name,
                    i.traveller_email,
                    i.duration,
                    i.destinations,
                    i.total_cost,
                    i.status,
                    i.created_date,
                    g.id as guide_id,
                    g.fullname as guide_name,
                    g.phone as guide_phone,
                    g.email as guide_email,
                    g.guide_tourist_data as guide_languages,
                    d.id as driver_id,
                    d.fullname as driver_name,
                    d.phone as driver_phone,
                    d.email as driver_email,
                    d.driver_data as driver_license,
                    v.vehicle_number
                  FROM itineraries i
                  LEFT JOIN guide_assignments ga ON i.id = ga.itinerary_id
                  LEFT JOIN users g ON ga.guide_id = g.id AND g.account_type = 'guide'
                  LEFT JOIN driver_assignments da ON i.id = da.itinerary_id
                  LEFT JOIN users d ON da.driver_id = d.id AND d.account_type = 'driver'
                  LEFT JOIN vehicles v ON da.vehicle_id = v.id
                  WHERE i.itinerary_number = :itinerary_number";
        
        $this->db->query($query);
        $this->db->bind(':itinerary_number', $itineraryNumber);
        return $this->db->single();
        */

        return null;
    }

    public function getDashboardStatistics() {
        $stats = [];
        
        // Count total trips (completed)
        $this->db->query("SELECT COUNT(*) as total FROM created_trips WHERE status = 'completed'");
        $result = $this->db->single();
        $stats['trips_completed'] = $result ? $result->total : 0;
        
        // Count total users (all account types except admin)
        $this->db->query("SELECT COUNT(*) as total FROM users WHERE account_type != 'admin'");
        $result = $this->db->single();
        $stats['total_users'] = $result ? $result->total : 0;
        
        // Count total bookings/trips (all statuses)
        $this->db->query("SELECT COUNT(*) as total FROM created_trips");
        $result = $this->db->single();
        $stats['total_bookings'] = $result ? $result->total : 0;
        
        // Calculate total earnings from completed transactions
        $this->db->query("SELECT SUM(amount) as total FROM transactions WHERE transaction_status = 'completed'");
        $result = $this->db->single();
        $stats['total_earnings'] = $result && $result->total ? $result->total : 0;
        
        return $stats;
    }

    public function getRecentNotifications($limit = 5) {
        $this->db->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT :limit");
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

}