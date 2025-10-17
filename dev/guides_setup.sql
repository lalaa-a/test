-- Create guides table for Sri Lanka travel website
CREATE TABLE IF NOT EXISTS `guides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `description` text,
  `specialization` varchar(255) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT 4.0,
  `total_reviews` int(11) DEFAULT 0,
  `price_per_day` decimal(8,2) DEFAULT 50.00,
  `image_url` varchar(500) DEFAULT NULL,
  `is_licensed` tinyint(1) DEFAULT 1,
  `is_trending` tinyint(1) DEFAULT 0,
  `is_tourist_guide` tinyint(1) DEFAULT 0,
  `badge_type` enum('top-rated','most-booked','none') DEFAULT 'none',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_licensed` (`is_licensed`),
  KEY `idx_trending` (`is_trending`),
  KEY `idx_tourist` (`is_tourist_guide`),
  KEY `idx_rating` (`rating`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample guide data
INSERT INTO `guides` (`name`, `email`, `phone`, `description`, `specialization`, `rating`, `total_reviews`, `price_per_day`, `image_url`, `is_licensed`, `is_trending`, `is_tourist_guide`, `badge_type`) VALUES
('Saman Perera', 'saman@guides.lk', '+94771234567', 'Expert cultural tour guide with 15 years experience. Specialized in ancient temples and historical sites across Sri Lanka.', 'Cultural Tours', 4.8, 156, 75.00, '/test/public/components/driver/images/driver-saman-perera-84.png', 1, 1, 1, 'top-rated'),
('Ruwan Silva', 'ruwan@guides.lk', '+94772345678', 'Wildlife safari expert and nature enthusiast. Perfect guide for national parks and wildlife photography tours.', 'Wildlife Safari', 4.9, 203, 85.00, '/test/public/components/driver/images/driver-jane-smith-123.png', 1, 1, 1, 'most-booked'),
('Nimal Fernando', 'nimal@guides.lk', '+94773456789', 'Hill country specialist with deep knowledge of tea plantations, scenic routes, and mountain adventures.', 'Hill Country', 4.7, 89, 70.00, '/test/public/components/driver/images/driver-john-doe-112.png', 1, 0, 1, 'none'),
('Chaminda Rathnayake', 'chaminda@guides.lk', '+94774567890', 'Coastal tour expert specializing in southern beaches, fishing villages, and marine life experiences.', 'Beach Tours', 4.6, 112, 65.00, '/test/public/components/driver/images/driver-saman-perera-84.png', 1, 0, 1, 'none'),
('Kasun Mendis', 'kasun@guides.lk', '+94775678901', 'All-island tour guide with expertise in comprehensive Sri Lankan cultural and natural heritage tours.', 'Cultural Tours', 4.8, 176, 80.00, '/test/public/components/driver/images/driver-jane-smith-123.png', 1, 1, 1, 'top-rated'),
('Lakshan Perera', 'lakshan@guides.lk', '+94776789012', 'Adventure sports specialist and mountain guide. Expert in hiking, rock climbing, and extreme sports tours.', 'Adventure Sports', 4.7, 134, 90.00, '/test/public/components/driver/images/driver-john-doe-112.png', 1, 0, 1, 'none'),
('Tharanga Wickramasinghe', 'tharanga@guides.lk', '+94777890123', 'Licensed historical tour guide specializing in ancient sites and archaeological wonders.', 'Historical Sites', 4.9, 124, 75.00, 'http://localhost:3845/assets/37f8b714fb00ccdfb9d77f2dfe226e0af80a59c9.png', 1, 0, 0, 'none'),
('Kumari Jayawardene', 'kumari@guides.lk', '+94778901234', 'Expert cultural guide with deep knowledge of Sri Lankan heritage and traditions.', 'Cultural Tours', 4.9, 98, 70.00, 'http://localhost:3845/assets/162e029f04353c600e484a62c5a5d18625d8e524.png', 1, 0, 0, 'none'),
('Ravi Gunasekara', 'ravi@guides.lk', '+94779012345', 'Wildlife expert and nature guide with extensive knowledge of Sri Lankan fauna and flora.', 'Wildlife Safari', 5.0, 210, 95.00, 'http://localhost:3845/assets/fae394ceb9cdbaf029e13c0b4d17726dad1b4291.png', 1, 1, 0, 'top-rated'),
('Malini Senanayake', 'malini@guides.lk', '+94770123456', 'Highly reviewed cultural guide with excellent customer feedback and deep local knowledge.', 'Cultural Tours', 4.9, 156, 65.00, 'http://localhost:3845/assets/162e029f04353c600e484a62c5a5d18625d8e524.png', 1, 0, 0, 'none'),
('Pradeep Bandara', 'pradeep@guides.lk', '+94771234560', 'Top-reviewed adventure guide with outstanding service and safety record.', 'Adventure Sports', 4.8, 145, 85.00, 'http://localhost:3845/assets/37f8b714fb00ccdfb9d77f2dfe226e0af80a59c9.png', 1, 0, 0, 'none'),
('Sanduni Rajapaksa', 'sanduni@guides.lk', '+94772345601', 'Exceptional wildlife guide with perfect reviews and unmatched expertise in nature tours.', 'Wildlife Safari', 5.0, 189, 90.00, 'http://localhost:3845/assets/fae394ceb9cdbaf029e13c0b4d17726dad1b4291.png', 1, 0, 1, 'most-booked');
