-- vehicles_table.sql
-- Creates the vehicles table for driver vehicle management

CREATE TABLE IF NOT EXISTS vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    driver_id INT NOT NULL,
    make VARCHAR(100) NOT NULL,
    model VARCHAR(100) NOT NULL,
    year INT NOT NULL,
    license_plate VARCHAR(50) NOT NULL UNIQUE,
    color VARCHAR(50) NOT NULL,
    vehicle_type ENUM('sedan', 'suv', 'van', 'luxury', 'minibus') NOT NULL,
    
    -- Vehicle photos
    front_photo VARCHAR(255),
    back_photo VARCHAR(255),
    side_photo VARCHAR(255),
    
    -- Verification status
    verification_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    verified_by INT NULL, -- admin/moderator user id who verified
    verified_at TIMESTAMP NULL,
    rejection_reason TEXT NULL,
    
    -- Metadata
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key constraints
    FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes
    INDEX idx_driver_id (driver_id),
    INDEX idx_verification_status (verification_status),
    INDEX idx_license_plate (license_plate)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;