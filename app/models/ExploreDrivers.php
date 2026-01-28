<?php
    class ExploreDrivers {
        private $db;
        
        public function __construct() {
            $this->db = new Database();
        }

        // Get trending drivers
        public function getTrendingDrivers($limit = null) {
            $sql = "SELECT * FROM drivers WHERE is_trending = 1 ORDER BY rating DESC, total_reviews DESC";
            
            if ($limit !== null) {
                $sql .= " LIMIT :limit";
                $this->db->query($sql);
                $this->db->bind(':limit', $limit);
            } else {
                $this->db->query($sql);
            }
            
            return $this->db->resultSet();
        }

        // Get all drivers
        public function getAllDrivers($limit = null) {
            $sql = "SELECT * FROM drivers WHERE is_licensed = 1 ORDER BY rating DESC";
            
            if ($limit !== null) {
                $sql .= " LIMIT :limit";
                $this->db->query($sql);
                $this->db->bind(':limit', $limit);
            } else {
                $this->db->query($sql);
            }
            
            return $this->db->resultSet();
        }

        // Get licensed drivers
        public function getLicensedDrivers() {
            $this->db->query('SELECT * FROM drivers WHERE is_licensed = 1 ORDER BY rating DESC');
            return $this->db->resultSet();
        }

        // Get reviewed drivers
        public function getReviewedDrivers($limit = null) {
            $sql = "SELECT * FROM drivers WHERE total_reviews > 50 ORDER BY total_reviews DESC";
            
            if ($limit !== null) {
                $sql .= " LIMIT :limit";
                $this->db->query($sql);
                $this->db->bind(':limit', $limit);
            } else {
                $this->db->query($sql);
            }
            
            return $this->db->resultSet();
        }

        // Get tourist drivers
        public function getTouristDrivers($limit = null) {
            $sql = "SELECT * FROM drivers WHERE is_tourist_guide = 1 ORDER BY rating DESC";
            
            if ($limit !== null) {
                $sql .= " LIMIT :limit";
                $this->db->query($sql);
                $this->db->bind(':limit', $limit);
            } else {
                $this->db->query($sql);
            }
            
            return $this->db->resultSet();
        }

        // Get driver by ID
        public function getDriverById($id) {
            $this->db->query('SELECT * FROM drivers WHERE id = :id');
            $this->db->bind(':id', $id);
            return $this->db->single();
        }

        // Add a new trending driver
        public function addTrendingDriver($driverData) {
            $this->db->query('INSERT INTO drivers (name, description, rating, total_reviews, is_trending, image_url, is_licensed, is_tourist_guide) VALUES (:name, :description, :rating, :total_reviews, :is_trending, :image_url, :is_licensed, :is_tourist_guide)');
            
            $this->db->bind(':name', $driverData['name']);
            $this->db->bind(':description', $driverData['description']);
            $this->db->bind(':rating', $driverData['rating']);
            $this->db->bind(':total_reviews', $driverData['total_reviews']);
            $this->db->bind(':is_trending', $driverData['is_trending']);
            $this->db->bind(':image_url', $driverData['image_url']);
            $this->db->bind(':is_licensed', $driverData['is_licensed']);
            $this->db->bind(':is_tourist_guide', $driverData['is_tourist_guide']);
            
            return $this->db->execute();
        }
    }
?>
