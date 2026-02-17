-- Create trips table (required by BuisManagerModel, payouts FK, trip_events FK)
-- Run this in database: tripingoo

CREATE TABLE IF NOT EXISTS trips (
    id INT PRIMARY KEY AUTO_INCREMENT,
    userID INT NULL COMMENT 'Traveler/customer user id',
    driverID INT NULL COMMENT 'Driver user id',
    guideID INT NULL COMMENT 'Guide user id',
    start_date DATE NULL,
    duration INT NULL COMMENT 'Number of days',
    revenue DECIMAL(10,2) NULL DEFAULT 0.00,
    status VARCHAR(50) NULL DEFAULT 'Pending' COMMENT 'e.g. Pending, Completed, Ongoing, Cancelled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_trips_user FOREIGN KEY (userID) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_trips_driver FOREIGN KEY (driverID) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_trips_guide FOREIGN KEY (guideID) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_trips_status (status),
    INDEX idx_trips_start_date (start_date)
);
