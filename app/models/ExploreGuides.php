<?php
    class ExploreGuides {
        private $db;
        
        public function __construct() {
            $this->db = new Database();
        }

        // Get trending guides
        public function getTrendingGuides($limit = null) {
            $sql = "SELECT * FROM guides WHERE is_trending = 1 ORDER BY rating DESC, total_reviews DESC";
            
            if ($limit !== null) {
                $sql .= " LIMIT :limit";
                $this->db->query($sql);
                $this->db->bind(':limit', $limit);
            } else {
                $this->db->query($sql);
            }
            
            return $this->db->resultSet();
        }

        // Get all guides
        public function getAllGuides($limit = null) {
            $sql = "SELECT * FROM guides WHERE is_licensed = 1 ORDER BY rating DESC";
            
            if ($limit !== null) {
                $sql .= " LIMIT :limit";
                $this->db->query($sql);
                $this->db->bind(':limit', $limit);
            } else {
                $this->db->query($sql);
            }
            
            return $this->db->resultSet();
        }

        // Get licensed guides
        public function getLicensedGuides() {
            $this->db->query('SELECT * FROM guides WHERE is_licensed = 1 ORDER BY rating DESC');
            return $this->db->resultSet();
        }

        // Get reviewed guides
        public function getReviewedGuides($limit = null) {
            $sql = "SELECT * FROM guides WHERE total_reviews > 50 ORDER BY total_reviews DESC";
            
            if ($limit !== null) {
                $sql .= " LIMIT :limit";
                $this->db->query($sql);
                $this->db->bind(':limit', $limit);
            } else {
                $this->db->query($sql);
            }
            
            return $this->db->resultSet();
        }

        // Get tourist guides
        public function getTouristGuides($limit = null) {
            $sql = "SELECT * FROM guides WHERE is_tourist_guide = 1 ORDER BY rating DESC";
            
            if ($limit !== null) {
                $sql .= " LIMIT :limit";
                $this->db->query($sql);
                $this->db->bind(':limit', $limit);
            } else {
                $this->db->query($sql);
            }
            
            return $this->db->resultSet();
        }

        // Get guide by ID
        public function getGuideById($id) {
            $this->db->query('SELECT * FROM guides WHERE id = :id');
            $this->db->bind(':id', $id);
            return $this->db->single();
        }

        // Add new guide
        public function addGuide($data) {
            $this->db->query('INSERT INTO guides (name, email, phone, description, specialization, rating, total_reviews, price_per_day, image_url, is_licensed, is_trending, is_tourist_guide, badge_type) VALUES (:name, :email, :phone, :description, :specialization, :rating, :total_reviews, :price_per_day, :image_url, :is_licensed, :is_trending, :is_tourist_guide, :badge_type)');
            
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':phone', $data['phone']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':specialization', $data['specialization']);
            $this->db->bind(':rating', $data['rating']);
            $this->db->bind(':total_reviews', $data['total_reviews']);
            $this->db->bind(':price_per_day', $data['price_per_day']);
            $this->db->bind(':image_url', $data['image_url']);
            $this->db->bind(':is_licensed', $data['is_licensed']);
            $this->db->bind(':is_trending', $data['is_trending']);
            $this->db->bind(':is_tourist_guide', $data['is_tourist_guide']);
            $this->db->bind(':badge_type', $data['badge_type']);
            
            return $this->db->execute();
        }

        // Update guide
        public function updateGuide($data) {
            $this->db->query('UPDATE guides SET name = :name, email = :email, phone = :phone, description = :description, specialization = :specialization, rating = :rating, total_reviews = :total_reviews, price_per_day = :price_per_day, image_url = :image_url, is_licensed = :is_licensed, is_trending = :is_trending, is_tourist_guide = :is_tourist_guide, badge_type = :badge_type WHERE id = :id');
            
            $this->db->bind(':id', $data['id']);
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':phone', $data['phone']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':specialization', $data['specialization']);
            $this->db->bind(':rating', $data['rating']);
            $this->db->bind(':total_reviews', $data['total_reviews']);
            $this->db->bind(':price_per_day', $data['price_per_day']);
            $this->db->bind(':image_url', $data['image_url']);
            $this->db->bind(':is_licensed', $data['is_licensed']);
            $this->db->bind(':is_trending', $data['is_trending']);
            $this->db->bind(':is_tourist_guide', $data['is_tourist_guide']);
            $this->db->bind(':badge_type', $data['badge_type']);
            
            return $this->db->execute();
        }

        // Delete guide
        public function deleteGuide($id) {
            $this->db->query('DELETE FROM guides WHERE id = :id');
            $this->db->bind(':id', $id);
            return $this->db->execute();
        }
    }
?>
