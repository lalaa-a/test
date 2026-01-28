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
