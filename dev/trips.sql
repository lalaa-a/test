
CREATE TABLE created_trips (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    trip_title VARCHAR(255) NOT NULL,
    description TEXT,
    start_date DATE,
    end_date DATE,
    status ENUM('planning','pending','scheduled','completed','ongoing') DEFAULT 'planning',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE trip_events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tripId INT NOT NULL,
    userId INT NOT NULL,
    eventDate DATE NOT NULL,
    startTime TIME,
    endTime TIME,

    eventType ENUM('travelSpot', 'location') NOT NULL,
    eventStatus ENUM('start', 'intermediate', 'end') NOT NULL,
    
    travelSpotId INT,
    locationName VARCHAR(255),
    latitude DECIMAL(10, 8),  
    longitude DECIMAL(11, 8), 
    description TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (tripId) REFERENCES trips(id) ON DELETE CASCADE,
    FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (travelSpotId) REFERENCES travel_spots(spotId) ON DELETE SET NULL
);

-- Create indexes
CREATE INDEX idx_trip_events_tripId ON trip_events(tripId);
CREATE INDEX idx_trip_events_userId ON trip_events(userId);
CREATE INDEX idx_trip_events_date ON trip_events(eventDate);
CREATE INDEX idx_trip_events_type ON trip_events(eventType);
CREATE INDEX idx_trip_events_coords ON trip_events(latitude, longitude);

-- Create validation trigger
DELIMITER $$

CREATE TRIGGER trg_trip_events_validate_before_insert
BEFORE INSERT ON trip_events
FOR EACH ROW
BEGIN
    IF NEW.eventType = 'travelSpot' THEN
        IF NEW.travelSpotId IS NULL OR NEW.locationName IS NOT NULL OR NEW.latitude IS NOT NULL OR NEW.longitude IS NOT NULL THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'For eventType=travelSpot, travelSpotId must be set and location fields must be NULL';
        END IF;
    ELSEIF NEW.eventType = 'location' THEN
        IF NEW.travelSpotId IS NOT NULL OR NEW.locationName IS NULL OR NEW.latitude IS NULL OR NEW.longitude IS NULL THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'For eventType=location, location fields must be set and travelSpotId must be NULL';
        END IF;
    END IF;
END$$

CREATE TRIGGER trg_trip_events_validate_before_update
BEFORE UPDATE ON trip_events
FOR EACH ROW
BEGIN
    IF NEW.eventType = 'travelSpot' THEN
        IF NEW.travelSpotId IS NULL OR NEW.locationName IS NOT NULL OR NEW.latitude IS NOT NULL OR NEW.longitude IS NOT NULL THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'For eventType=travelSpot, travelSpotId must be set and location fields must be NULL';
        END IF;
    ELSEIF NEW.eventType = 'location' THEN
        IF NEW.travelSpotId IS NOT NULL OR NEW.locationName IS NULL OR NEW.latitude IS NULL OR NEW.longitude IS NULL THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'For eventType=location, location fields must be set and travelSpotId must be NULL';
        END IF;
    END IF;
END$$

DELIMITER ;