
CREATE TABLE created_trips (
    tripId INT PRIMARY KEY AUTO_INCREMENT,
    userId INT NOT NULL,
    tripTitle VARCHAR(255) NOT NULL,
    description TEXT,
    startDate DATE,
    endDate DATE,
    status ENUM('planning','pending','scheduled','completed','ongoing') DEFAULT 'planning',
    numberOfPeople INT DEFAULT 1,
    createdt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE trip_events (
    eventId INT PRIMARY KEY AUTO_INCREMENT,
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


CREATE TABLE traveller_side_g_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    userId INT NOT NULL COMMENT 'Tourist who created the request',
    tripId INT NOT NULL COMMENT 'Reference to the trip',
    eventId INT NOT NULL COMMENT 'Reference to the trip event',
    travelSpotId INT NOT NULL COMMENT 'Reference to travel spot',
    guideId INT NULL COMMENT 'Selected guide (null if not selected)',
    
    status ENUM('notSelected', 'pending', 'requested', 'accepted', 'rejected', 'cancelled', 'completed') DEFAULT 'notSelected',
    
    -- Guide info snapshot
    guideFullName VARCHAR(255),
    guideProfilePhoto VARCHAR(255),
    guideAverageRating DECIMAL(3,2),
    guideBio TEXT,
    totalCharge DECIMAL(10,2) COMMENT 'Total charge for the guide service', 
    
    -- Timestamps for tracking
    requestedAt TIMESTAMP NULL COMMENT 'When request sent to guide',
    respondedAt TIMESTAMP NULL COMMENT 'When guide responded',
    acceptedAt TIMESTAMP NULL COMMENT 'When guide accepted',
    completedAt TIMESTAMP NULL COMMENT 'When guide service completed',
    
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign keys (updated to match your trip_events table)
    FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (tripId) REFERENCES created_trips(tripId) ON DELETE CASCADE,
    FOREIGN KEY (eventId) REFERENCES trip_events(eventId) ON DELETE CASCADE,
    FOREIGN KEY (travelSpotId) REFERENCES travel_spots(spotId) ON DELETE CASCADE,
    FOREIGN KEY (guideId) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes
    INDEX idx_userId (userId),
    INDEX idx_tripId (tripId),
    INDEX idx_eventId (eventId),
    INDEX idx_travelSpotId (travelSpotId),
    INDEX idx_guideId (guideId),
    INDEX idx_status (status),
    INDEX idx_createdAt (createdAt),
    INDEX idx_status_trip (status, tripId),
    INDEX idx_user_status (userId, status),
    INDEX idx_event_guide (eventId, guideId)
) ENGINE=InnoDB;


CREATE TABLE traveller_side_d_requests (
    requestId INT PRIMARY KEY AUTO_INCREMENT,
    tripId INT NOT NULL,
    rqUserId INT NOT NULL,
    driverId INT NOT NULL,
    driverName VARCHAR(255) NOT NULL,
    driverProfilePhoto VARCHAR(500) NULL,
    driverRating DECIMAL(3,2) DEFAULT 0.00,
    verifyStatus TINYINT(1) DEFAULT 0,
    vehicleId INT NOT NULL,
    vehicleModel VARCHAR(255) NOT NULL,
    vehicleYear INT NULL,
    vehicleType VARCHAR(255) NOT NULL,
    vehiclePhoto VARCHAR(500) NULL,
    vehicleCapacity INT NOT NULL,
    childSeats INT DEFAULT 0,
    requestStatus ENUM('pending', 'requested', 'accepted', 'rejected', 'cancelled', 'completed') DEFAULT 'pending',
    chargeType ENUM('perDay', 'perKm') NOT NULL,
    totalKm DECIMAL(10,2) NULL,
    totalAmount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    requestedAt TIMESTAMP NULL,
    respondedAt TIMESTAMP NULL,
    completedAt TIMESTAMP NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key constraints
    FOREIGN KEY (tripId) REFERENCES created_trips(tripId) ON DELETE CASCADE,
    FOREIGN KEY (rqUserId) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (driverId) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicleId) REFERENCES vehicles(vehicleId) ON DELETE CASCADE,
    
    -- Indexes
    INDEX idx_trip (tripId),
    INDEX idx_rquser (rqUserId),
    INDEX idx_driver (driverId),
    INDEX idx_vehicle (vehicleId),
    INDEX idx_request_status (requestStatus),
    INDEX idx_requested_at (requestedAt)
    
) ENGINE=InnoDB;