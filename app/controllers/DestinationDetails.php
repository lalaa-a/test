<?php
    class DestinationDetails extends Controller{
        public function index($id = null) {
            // Get destination data based on ID
            $destinationData = $this->getDestinationData($id);
            $this->view('DestinationDetails', $destinationData);
        }
        
        private function getDestinationData($id) {
            // Sample destination data - in a real app, this would come from a database
            $destinations = [
                1 => [
                    'id' => 1,
                    'name' => 'Sigiriya Rock Fortress',
                    'location' => 'Matale',
                    'rating' => 4.8,
                    'review_count' => 450,
                    'image' => 'assets/sigiriya.jpg',
                    'description' => 'Ancient rock fortress and palace ruins in central Sri Lanka, dating back to the 5th century.',
                    'category' => 'Culture & Heritage'
                ],
                2 => [
                    'id' => 2,
                    'name' => 'Galle Dutch Fort',
                    'location' => 'Galle',
                    'rating' => 4.8,
                    'review_count' => 450,
                    'image' => 'assets/galle.jpg',
                    'description' => 'Historic fortified city built by Portuguese colonists in the 16th century.',
                    'category' => 'Culture & Heritage'
                ],
                3 => [
                    'id' => 3,
                    'name' => 'Secret Beach Mirissa',
                    'location' => 'Matara',
                    'rating' => 4.8,
                    'review_count' => 450,
                    'image' => 'assets/mirissa.jpg',
                    'description' => 'Hidden beach paradise with pristine waters and stunning coastal views.',
                    'category' => 'Relaxation & Leisure'
                ]
            ];
            
            return isset($destinations[$id]) ? $destinations[$id] : $destinations[1]; // Default to first destination if ID not found
        }
    }
?>
