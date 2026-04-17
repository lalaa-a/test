-- vehicle_pricing_table.sql
-- Creates the vehicle_pricing table for driver vehicle pricing management

CREATE TABLE IF NOT EXISTS vehicle_pricing (
    pricingId INT AUTO_INCREMENT PRIMARY KEY,
    vehicleId INT NOT NULL,
    driverId INT NOT NULL,
    vehicleChargePerKm DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    driverChargePerKm DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    vehicleChargePerDay DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    driverChargePerDay DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    minimumKm DECIMAL(5,2) DEFAULT 0.00,
    minimumDays DECIMAL(3,1) DEFAULT 1.0,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Foreign key constraints
    FOREIGN KEY (vehicleId) REFERENCES vehicles(vehicleId) ON DELETE CASCADE,
    FOREIGN KEY (driverId) REFERENCES users(id) ON DELETE CASCADE,

    -- Indexes
    INDEX idx_vehicle_id (vehicleId),
    UNIQUE KEY unique_vehicle_pricing (vehicleId)
) ENGINE=InnoDB;