-- Travel package schema for moderator-created reusable packages
-- Run this file once in your MySQL database.

CREATE TABLE IF NOT EXISTS travel_packages (
    packageId INT AUTO_INCREMENT PRIMARY KEY,
    packageName VARCHAR(180) NOT NULL,
    overview TEXT NOT NULL,
    packageDetails TEXT NULL,
    durationDays INT NOT NULL DEFAULT 1,
    estimatedPriceLkr DECIMAL(12, 2) NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    moderatorId INT NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_travel_packages_status (status),
    INDEX idx_travel_packages_name (packageName),
    INDEX idx_travel_packages_moderator (moderatorId),
    CONSTRAINT fk_travel_packages_moderator
        FOREIGN KEY (moderatorId) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS travel_package_spots (
    packageSpotId INT AUTO_INCREMENT PRIMARY KEY,
    packageId INT NOT NULL,
    spotId INT NOT NULL,
    dayNumber INT NOT NULL DEFAULT 1,
    visitOrder INT NOT NULL DEFAULT 1,
    spotNote TEXT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_package_day_order (packageId, dayNumber, visitOrder),
    INDEX idx_package_spots_package (packageId),
    INDEX idx_package_spots_spot (spotId),
    CONSTRAINT fk_package_spots_package
        FOREIGN KEY (packageId) REFERENCES travel_packages(packageId)
        ON DELETE CASCADE,
    CONSTRAINT fk_package_spots_spot
        FOREIGN KEY (spotId) REFERENCES travel_spots(spotId)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS travel_package_photos (
    packagePhotoId INT AUTO_INCREMENT PRIMARY KEY,
    packageId INT NOT NULL,
    photoPath VARCHAR(500) NOT NULL,
    photoOrder INT NOT NULL DEFAULT 1,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_package_photos_package (packageId),
    CONSTRAINT fk_package_photos_package
        FOREIGN KEY (packageId) REFERENCES travel_packages(packageId)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Example inserts to store one package and attach spots/photos.
START TRANSACTION;

INSERT INTO travel_packages (
    packageName,
    overview,
    packageDetails,
    durationDays,
    estimatedPriceLkr,
    status,
    moderatorId
)
VALUES (
    'Southern Explorer',
    'A curated multi-day journey covering southern highlights.',
    'Includes beach mornings, heritage visits, and sunset stops.',
    3,
    28500.00,
    'active',
    1
);

SET @newPackageId = LAST_INSERT_ID();

INSERT INTO travel_package_spots (packageId, spotId, dayNumber, visitOrder, spotNote)
VALUES
    (@newPackageId, 1, 1, 1, 'Morning arrival and city walk'),
    (@newPackageId, 4, 1, 2, 'Sunset viewpoint visit'),
    (@newPackageId, 8, 2, 1, 'Nature trail and lunch stop');

INSERT INTO travel_package_photos (packageId, photoPath, photoOrder)
VALUES
    (@newPackageId, '/travelPackages/sample/package-cover.jpg', 1),
    (@newPackageId, '/travelPackages/sample/package-secondary.jpg', 2);

COMMIT;
