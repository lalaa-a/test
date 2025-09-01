<?php
    class M_users {
        private $db;
        public function __construct() {
            // Initialize the database connection
            $this->db = new Database();
        }
    }
?>