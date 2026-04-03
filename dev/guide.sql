CREATE TABLE guide_locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guideId INT NOT NULL,
    spotId INT NOT NULL,
    baseCharge DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    chargeType ENUM('per_person', 'whole_trip') NOT NULL DEFAULT 'per_person',
    minGroupSize INT NOT NULL DEFAULT 1,
    maxGroupSize INT NOT NULL DEFAULT 20,
    description TEXT,
    photoPath VARCHAR(255),
    isActive TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (guideId) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (spotId) REFERENCES travel_spots(spotId) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Guide Unavailability Table (for time periods within a day)
CREATE TABLE guide_unavailability (
    id INT PRIMARY KEY AUTO_INCREMENT,
    guideId INT NOT NULL,
    unavailableDate DATE NOT NULL,           -- The specific date
    startTime TIME NOT NULL,                  -- Start of unavailable period
    endTime TIME NOT NULL,                    -- End of unavailable period
    reason ENUM('personal', 'booked'),
    personalReason TEXT,                       -- Custom reason if 'other'
    tripId INT,                                -- If booked for a trip
    isRecurring BOOLEAN DEFAULT FALSE,         -- For weekly recurring unavailability
    recurringDayOfWeek TINYINT NULL,           -- 1-7 (Monday=1, Sunday=7) if recurring
    recurringEndDate DATE NULL,                 -- If recurring, when it ends
    
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign keys
    FOREIGN KEY (guideId) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (tripId) REFERENCES created_trips(tripId) ON DELETE SET NULL,
    
    -- Ensure valid time range
    CONSTRAINT chk_valid_time CHECK (startTime < endTime),
    
    -- Indexes for performance
    INDEX idx_guide_date (guideId, unavailableDate),
    INDEX idx_guide_recurring (guideId, isRecurring, recurringDayOfWeek),
    INDEX idx_date_range (unavailableDate, startTime, endTime),
    INDEX idx_reason (reason)
) ENGINE=InnoDB;


CREATE TABLE guide_side_g_requests (

    id INT PRIMARY KEY AUTO_INCREMENT,
    userId int NOT NULL COMMENT 'Tourist who created the request',
    tripId int NOT NULL COMMENT 'Reference to the trip',
    eventId int NOT NULL COMMENT 'Reference to the trip event',
    travelSpotId int NOT NULL COMMENT 'Reference to travel spot',
    guideId int NULL COMMENT 'Selected guide (null if not selected)',
    status enum('notSelected', 'pending', 'requested', 'accepted', 'rejected', 'completed', 'cancelled') COLLATE utf8mb4_unicode_ci NULL DEFAULT 'notSelected',
    guideFullName varchar(255) COLLATE utf8mb4_unicode_ci NULL,
    guideProfilePhoto varchar(255) COLLATE utf8mb4_unicode_ci NULL,
    guideAverageRating decimal(3,2) NULL,
    guideBio text COLLATE utf8mb4_unicode_ci NULL,
    chargeType enum('per_person', 'whole_trip') NOT NULL DEFAULT 'whole_trip',
    numberOfPeople int NOT NULL DEFAULT 1,
    totalCharge decimal(10,2) NULL COMMENT 'Total charge for the guide service',
    requestedAt timestamp NULL COMMENT 'When request sent to guide',
    respondedAt timestamp NULL COMMENT 'When guide responded',
    completedAt timestamp NULL COMMENT 'When guide service completed',
    createdAt timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    updatedAt timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,


    -- Foreign keys (updated to match your trip_events table)
    FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (tripId) REFERENCES created_trips(tripId) ON DELETE CASCADE,
    FOREIGN KEY (eventId) REFERENCES trip_events(eventId) ON DELETE CASCADE,
    FOREIGN KEY (travelSpotId) REFERENCES travel_spots(spotId) ON DELETE CASCADE,
    FOREIGN KEY (guideId) REFERENCES users(id) ON DELETE SET NULL,

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