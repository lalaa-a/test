<?php
    class User {
        private $db;
        
        public function __construct() {
            // Initialize the database connection
            $this->db = new Database();
        }

        // Add user methods here
        public function getUsers() {
            $this->db->query('SELECT * FROM users');
            return $this->db->resultSet();
        }

        public function getUserById($id) {
            $this->db->query('SELECT * FROM users WHERE id = :id');
            $this->db->bind(':id', $id);
            return $this->db->single();
        }
    }
?>
