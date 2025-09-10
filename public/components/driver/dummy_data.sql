-- Create database
CREATE DATABASE IF NOT EXISTS sri_lanka_travel;
USE sri_lanka_travel;

-- Create drivers table
CREATE TABLE IF NOT EXISTS drivers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    image_url VARCHAR(255),
    rating DECIMAL(2,1) DEFAULT 0.0,
    total_reviews INT DEFAULT 0,
    description TEXT,
    is_trending BOOLEAN DEFAULT FALSE,
    is_licensed BOOLEAN DEFAULT TRUE,
    is_tourist_guide BOOLEAN DEFAULT FALSE,
    badge_type ENUM('top-rated', 'most-booked', 'none') DEFAULT 'none',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert dummy data for trending drivers
INSERT INTO drivers (name, image_url, rating, total_reviews, description, is_trending, badge_type) VALUES
('John Doe', 'http://localhost:3845/assets/85d14703779dbe008f621b5c9aa61934f86a364c.png', 4.9, 124, 'Experienced driver with a comfortable sedan. Knows all the best routes and hidden gems. Fluent in English.', TRUE, 'top-rated'),
('Jane Smith', 'http://localhost:3845/assets/d84cc254159082188feff2d34dfc6e9238320fe8.png', 4.8, 86, 'Friendly and reliable driver with a spacious van, perfect for families or large groups. Safety is my priority.', TRUE, 'top-rated'),
('Kumar Fernando', 'http://localhost:3845/assets/dc44eecc642e9be7da61c724fd4b46f41efac2cd.png', 5.0, 210, 'Your local guide on wheels! I\'ll not only drive you but also share stories about our beautiful country.', TRUE, 'most-booked'),
('Saman Perera', 'http://localhost:3845/assets/05290308ee16b71bd5e5c1bb8463f0e3cb324862.png', 4.7, 95, 'Punctual and professional driver with a modern, air-conditioned car for your ultimate comfort.', TRUE, 'none'),
('Nimal Silva', 'http://localhost:3845/assets/fcc7a2a5ab3d9e00eb348299ffb0b305a88f7e4b.png', 4.9, 158, 'Passionate about travel and culture. I offer customized tours to make your journey unforgettable.', TRUE, 'none'),
('Rohan Jayasuriya', 'http://localhost:3845/assets/5505e7f18c3390dbecb39dafd7e71849786c4ad6.png', 4.8, 112, 'Wildlife enthusiast and experienced safari driver. Let\'s explore the national parks together!', TRUE, 'none'),
('Alex Johnson', 'components/driver/images/driver-jane-smith-123.png', 4.9, 145, 'Adventure specialist with luxury 4WD vehicle. Expert in mountain trails and off-road destinations. Perfect for thrill seekers!', TRUE, 'most-booked');

-- Insert additional dummy data for other sections
INSERT INTO drivers (name, image_url, rating, total_reviews, description, is_licensed, is_tourist_guide) VALUES
('Maria Rodriguez', 'images/driver-jane-smith-198.png', 4.7, 156, 'Multilingual guide specializing in cultural tours. Drives a luxury SUV and knows the best local restaurants and shopping spots.', TRUE, TRUE),
('Chaminda Lakmal', 'http://localhost:3845/assets/85d14703779dbe008f621b5c9aa61934f86a364c.png', 4.6, 78, 'Expert in hill country tours, especially Ella and Nuwara Eliya. Great photographer who can capture your best moments.', TRUE, TRUE),
('Priya Mendis', 'http://localhost:3845/assets/d84cc254159082188feff2d34dfc6e9238320fe8.png', 4.8, 142, 'Specialist in cultural heritage sites. Fluent in multiple languages including German and French.', TRUE, TRUE),
('Asanka Perera', 'http://localhost:3845/assets/dc44eecc642e9be7da61c724fd4b46f41efac2cd.png', 4.5, 67, 'Coastal region expert with deep knowledge of southern beaches and marine life. Great for whale watching tours.', TRUE, TRUE),
('Mahesh Bandara', 'http://localhost:3845/assets/05290308ee16b71bd5e5c1bb8463f0e3cb324862.png', 4.7, 189, 'Ancient cities specialist. Perfect guide for exploring Anuradhapura, Polonnaruwa, and Sigiriya.', TRUE, FALSE),
('Sanduni Wijeratne', 'http://localhost:3845/assets/fcc7a2a5ab3d9e00eb348299ffb0b305a88f7e4b.png', 4.9, 201, 'Tea plantation tours expert. Knows every tea factory in Nuwara Eliya and has connections with local tea masters.', TRUE, TRUE);

-- Update some records to have higher review counts for "Reviewed Drivers" section
UPDATE drivers SET total_reviews = 250 WHERE name = 'Kumar Fernando';
UPDATE drivers SET total_reviews = 230 WHERE name = 'Sanduni Wijeratne';
UPDATE drivers SET total_reviews = 189 WHERE name = 'Mahesh Bandara';
UPDATE drivers SET total_reviews = 167 WHERE name = 'Nimal Silva';

-- Make Chaminda Lakmal a trending driver
UPDATE drivers SET is_trending = TRUE, badge_type = 'top-rated' WHERE name = 'Chaminda Lakmal';
