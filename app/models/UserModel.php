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

    public function updateRememberToken($userId, $token) {
        $this->db->query('UPDATE users SET remember_token = :token WHERE id = :id');
        $this->db->bind(':token', $token);
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }

    public function findByRememberToken($token) {
        $this->db->query('SELECT * FROM users WHERE remember_token = :token');
        $this->db->bind(':token', $token);
        return $this->db->single();
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

}