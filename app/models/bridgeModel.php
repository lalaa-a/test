<?php
    class bridgeModel {
        private $db;
        
        public function __construct() {
            $this->db = new Database();
        }

        public function bridge($data){

            if($data['account_type']=="driver"){
                    
                $this->db->query("INSERT INTO drivers (name, image_url, description) VALUES (:name, :image_url, :description)");
                        
                // Bind only the parameters you're explicitly setting
                //$this->db->bind(':id', $user->id);
                $this->db->bind(':name', $data['fullname']);
                $this->db->bind(':image_url', $data['profile_photo']);
                $this->db->bind(':description', "Good driver with 2 years of experience");
                
                $this->db->execute();
            }

            elseif($data['account_type']=="guide"){

                $this->db->query("INSERT INTO guides (name, email, phone, description, specialization, image_url) VALUES (:name, :email, :phone, :description, :specialization, :image_url)");
                        
                // Bind only the parameters you're explicitly setting
                //$this->db->bind(':id', $user->id);
                $this->db->bind(':name', $data['fullname']);
                $this->db->bind(':email', $data['email']);
                $this->db->bind(':phone', $data['phone']);
                $this->db->bind(':description', "Good driver with 2 years of experience");
                $this->db->bind(':specialization', "Ancient Runes");
                $this->db->bind(':image_url', $data['profile_photo']);
                
                $this->db->execute();
            }
        }

    }
?>
