CREATE TABLE travelspots_mainfilters ( 
    mainFilterId INT AUTO_INCREMENT PRIMARY KEY,
    mainFilterName VARCHAR(255) NOT NULL, 
    moderatorId INT NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (moderatorId) REFERENCES users(id),
)ENGINE=InnoDB;


CREATE TABLE travelspots_subfilters ( 
    subFilterId INT AUTO_INCREMENT PRIMARY KEY,
    subFilterName VARCHAR(255) NOT NULL,
    mainFilterId INT NOT NULL, 
    moderatorId INT NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (moderatorId) REFERENCES users(id),
    FOREIGN KEY (mainFilterId) REFERENCES travelspots_mainfilters(mainFilterId)
        ON DELETE CASCADE
        ON UPDATE CASCADE
)ENGINE=InnoDB;


// left join query
SELECT 
    mf.mainFilterId,
    mf.mainFilterName,
    sf.subFilterId,
    sf.subFilterName
FROM travelspots_mainfilters AS mf
LEFT JOIN travelspots_subfilters AS sf
    ON mf.mainFilterId = sf.mainFilterId
ORDER BY mf.mainFilterName, sf.subFilterName;


CREATE VIEW view_mainfilters_with_subfilters AS
SELECT 
    mf.mainFilterId,
    mf.mainFilterName,
    sf.subFilterId,
    sf.subFilterName
FROM travelspots_mainfilters AS mf
LEFT JOIN travelspots_subfilters AS sf
    ON mf.mainFilterId = sf.mainFilterId
ORDER BY mf.mainFilterName, sf.subFilterName;


--place adding form 
-- Main travel spots table
CREATE TABLE travel_spots (

    spotId INT PRIMARY KEY AUTO_INCREMENT,
    spotName VARCHAR(200) NOT NULL,
    overview TEXT,
    
    province ENUM('Western','Central', 'Southern','Northern','Eastern','North Western','North Central','Uva','Sabaragamuwa'),
    district ENUM('Colombo','Gampaha','Kalutara','Kandy','Matale','Nuwara Eliya','Galle','Matara','Hambantota','Jaffna','Kilinochchi','Mannar','Mullaitivu','Vavuniya','Ampara','Batticaloa','Trincomalee','Kurunegala','Puttalam','Anuradhapura','Polonnaruwa','Badulla','Monaragala','Ratnapura','Kegalle'),
    
    -- Timing info
    bestTimeFrom ENUM('January','February','March','April','May','June',
                       'July','August','September','October','November','December'),
    bestTimeTo ENUM('January','February','March','April','May','June',
                     'July','August','September','October','November','December'),
    visitingDurationMin INT, -- In hours
    visitingDurationMax INT, -- In hours
    
    -- Practical info
    ticketPriceLocal DECIMAL(10, 2),
    ticketPriceForeigner DECIMAL(10, 2),
    openingHours TEXT, 
    ticketDetails TEXT,
    parkingDetails TEXT,
    
    -- Accessibility & Facilities
    accessibility TEXT,
    facilities TEXT,
    travelerTips TEXT,
    
    -- Ratings & Popularity (optional for later)
    averageRating DECIMAL(3, 2) DEFAULT 0.00,
    totalReviews INT DEFAULT 0

)ENGINE=InnoDB;

--delete cascade eka kara
CREATE TABLE travel_spots_subfilters (
    id INT PRIMARY KEY AUTO_INCREMENT,
    spotId INT,
    subFilterId INT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (spotId) REFERENCES travel_spots(spotId),
    FOREIGN KEY (subFilterId) REFERENCES travelspots_subfilters(subFilterId)

)ENGINE=InnoDB;



--delete cascade eka kara
CREATE TABLE travel_spots_photos (
    photoId INT PRIMARY KEY AUTO_INCREMENT,
    spotId INT,
    photoPath VARCHAR(500) NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (spotId) REFERENCES travel_spots(spotId)
)ENGINE=InnoDB;

--delete cascade eka kara
CREATE TABLE travel_spots_itinerary (
    pointId INT PRIMARY KEY AUTO_INCREMENT,
    spotId INT,
    pointName VARCHAR(200),
    pointOrder INT DEFAULT 0,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (spotId) REFERENCES travel_spots(spotId)
)ENGINE=InnoDB;

-- to check the constraint name for spotId foreign key in travel_spots_itinerary table
SELECT CONSTRAINT_NAME 
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE TABLE_NAME = 'travel_spots_itinerary' 
  AND COLUMN_NAME = 'spotId' 
  AND REFERENCED_TABLE_NAME IS NOT NULL;

ALTER TABLE travel_spots_itinerary 
DROP FOREIGN KEY fk_spot_id;  -- Replace with actual constraint name



--added cascade delete only for sourceSpotId
-- Nearby attractions table
CREATE TABLE travel_spots_nearbyspots (

    id INT PRIMARY KEY AUTO_INCREMENT,
    sourceSpotId INT,
    nearbySpotId INT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sourceSpotId) REFERENCES travel_spots(spotId),
    FOREIGN KEY (nearbySpotId) REFERENCES travel_spots(spotId)
)ENGINE=InnoDB;

ALTER TABLE travel_spots_nearbyspots 
ADD CONSTRAINT fk_nearbyspots_nearby_restrict 
FOREIGN KEY (nearbySpotId) 
REFERENCES travel_spots(spotId) 
ON DELETE SET NULL; -- setting null automatically when the nearby spot is deleted

-- delete cascade eka kara
CREATE TABLE travel_spots_contributions (
    contributionId INT PRIMARY KEY AUTO_INCREMENT,
    spotId INT,
    moderatorId INT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
    
    FOREIGN KEY (spotId) REFERENCES travel_spots(spotId),
    FOREIGN KEY (moderatorId) REFERENCES users(id)
)ENGINE=InnoDB;

ALTER TABLE travel_spots_contributions 
ADD CONSTRAINT fk_contributions_moderator_restrict 
FOREIGN KEY (moderatorId) 
REFERENCES users(id) 
ON DELETE RESTRICT; -- prevent deletion of users with contributions

--meekata photos tika ganna eka add kara mn passe
-- Create a view for the complete data hierarchy
CREATE VIEW travel_spot_card_data AS
SELECT 
    tmf.mainFilterId,
    tmf.mainFilterName,
    tsf.subFilterId,
    tsf.subFilterName,
    ts.spotId,
    ts.spotName,
    ts.overview,
    ts.averageRating,
    ts.totalReviews,
    ts.district,
    ts.province,
    CASE 
        WHEN tsf.subFilterId IS NULL THEN 'MAIN_FILTER_WITHOUT_SUBFILTERS'
        WHEN ts.spotId IS NULL THEN 'SUBFILTER_WITHOUT_SPOTS'
        ELSE 'COMPLETE_CHAIN'
    END as status
FROM travelspots_mainfilters tmf
LEFT JOIN travelspots_subfilters tsf ON tmf.mainFilterId = tsf.mainFilterId
LEFT JOIN travel_spots_subfilters tss ON tsf.subFilterId = tss.subFilterId
LEFT JOIN travel_spots ts ON tss.spotId = ts.spotId
ORDER BY 
    tmf.mainFilterName,
    CASE WHEN tsf.subFilterName IS NULL THEN 1 ELSE 0 END,
    tsf.subFilterName,
    ts.spotName;