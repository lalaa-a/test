CREATE TABLE vehicles (
    vehicleId INT PRIMARY KEY AUTO_INCREMENT,
    driverId INT NOT NULL,
    make VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    color VARCHAR(30),
    licensePlate VARCHAR(20) NOT NULL UNIQUE,
    seatingCapacity INT DEFAULT 4,
    fuelEfficiency DECIMAL(5,2),
    description TEXT,
    frontViewPhoto VARCHAR(255), --photo paths
    backViewPhoto VARCHAR(255),
    sideViewPhoto VARCHAR(255),
    interiorPhoto1 VARCHAR(255),
    interiorPhoto2 VARCHAR(255),    
    interiorPhoto3 VARCHAR(255),
    status BOOLEAN DEFAULT TRUE, --active or inactive
    availability BOOLEAN DEFAULT TRUE, --inuse or not
    isApproved BOOLEAN DEFAULT FALSE,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key constraint
    FOREIGN KEY (driverId) REFERENCES users(id) ON DELETE CASCADE

) ENGINE=InnoDB;


-- Driver Availability Schema
CREATE TABLE driver_unavailable_dates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    driverId INT NOT NULL,
    unavailableDate DATE NOT NULL,
    reason ENUM('personal','booked'),
    personalReason TEXT, 
    tripId INT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (driverId) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (tripId) REFERENCES created_trips(tripId) ON DELETE SET NULL,

    INDEX idx_driver_id (driverId),
    INDEX idx_date (unavailableDate),
    INDEX idx_reason (reason)
)ENGINE=InnoDB;


-- Create traveller_requests table
CREATE TABLE traveller_side_t_requests (
    -- Primary identifiers
    requestId INT PRIMARY KEY AUTO_INCREMENT,
    tripId INT NOT NULL,
    rqUserId INT NOT NULL,  -- User who requested (traveller)
    driverId INT NOT NULL,
    
    -- Driver information (denormalized for quick access)
    driverName VARCHAR(255) NOT NULL,
    driverProfilePhoto VARCHAR(500),
    driverRating DECIMAL(3,2) DEFAULT 0.00,
    verifyStatus BOOLEAN DEFAULT FALSE,  -- TRUE = verified, FALSE = not verified
    
    -- Vehicle information
    vehicleId INT NOT NULL,
    vehicleModel VARCHAR(255) NOT NULL,
    vehicleYear INT,
    vehicleType VARCHAR(255) NOT NULL,
    vehiclePhoto VARCHAR(500),
    vehicleCapacity INT NOT NULL,
    childSeats INT DEFAULT 0,
    
    -- Request status
    requestStatus ENUM(
        'pending',      -- Initial state
        'requested',    -- Request sent to driver
        'accepted',     -- Driver accepted
        'rejected',     -- Driver rejected
        'cancelled',    -- Cancelled by either party
        'completed'     -- Trip completed
    ) DEFAULT 'pending',
    
    -- Pricing information
    chargeType ENUM('perDay', 'perKm') NOT NULL,
    totalKm DECIMAL(10,2) NULL,  -- Only applicable if chargeType = 'perKm'
    totalAmount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    
    -- Timestamps
    requestedAt TIMESTAMP NULL,
    respondedAt TIMESTAMP NULL,
    completedAt TIMESTAMP NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key constraints
    FOREIGN KEY (tripId) REFERENCES created_trips(tripId) ON DELETE CASCADE,
    
    FOREIGN KEY (driverId) REFERENCES users(id) ON DELETE CASCADE,
    
    FOREIGN KEY (rqUserId) REFERENCES users(id) ON DELETE CASCADE,
    
    FOREIGN KEY (vehicleId) REFERENCES vehicles(vehicleId) ON DELETE SET NULL,

    -- Indexes for performance
    INDEX idx_trip (tripId),
    INDEX idx_driver (driverId),
    INDEX idx_rquser (rqUserId),
    INDEX idx_vehicle (vehicleId),
    INDEX idx_request_status (requestStatus),
    INDEX idx_request_date (requestedAt),
    INDEX idx_composite_status_date (requestStatus, requestedAt DESC)
    
) ENGINE=InnoDB;


CREATE TABLE driver_accept_trips (

    acceptId INT PRIMARY KEY AUTO_INCREMENT,
    tripId INT NOT NULL,
    rqUserId INT NOT NULL,
    driverId INT NOT NULL,
    
    -- Driver information
    driverName VARCHAR(255) NOT NULL,
    driverProfilePhoto VARCHAR(500),
    driverRating DECIMAL(3,2) DEFAULT 0.00,
    verifyStatus BOOLEAN DEFAULT FALSE,
    
    -- Vehicle information
    vehicleId INT NOT NULL,
    vehicleYear INT,
    vehicleType VARCHAR(255) NOT NULL,
    vehiclePhoto VARCHAR(500),
    vehicleCapacity INT NOT NULL,
    childSeats INT DEFAULT 0,
    
    -- Pricing information
    chargeType ENUM('perDay', 'perKm') NOT NULL,
    totalKm DECIMAL(10,2) NULL,
    totalAmount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    
    -- Completion and payment status
    doneStatus BOOLEAN DEFAULT FALSE COMMENT 'Trip completed or not',
    paymentStatus ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending' COMMENT 'Payment status',
    
    -- Timestamps
    completedAt TIMESTAMP NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key constraints
    FOREIGN KEY (tripId) REFERENCES created_trips(tripId) ON DELETE CASCADE,
    FOREIGN KEY (rqUserId) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (driverId) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicleId) REFERENCES vehicles(vehicleId) ON DELETE CASCADE,
    
)ENGINE=InnoDB;