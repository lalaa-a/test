-- add the additional fields to add profile details
CREATE TABLE profile_details (
    profileId INT PRIMARY KEY AUTO_INCREMENT,  -- Added AUTO_INCREMENT
    userId INT NOT NULL UNIQUE,  -- Added UNIQUE constraint
    bio TEXT,
    languages VARCHAR(500),
    instaAccount VARCHAR(255),
    facebookAccount VARCHAR(255),
    dlVerified BOOLEAN DEFAULT FALSE,
    tlSubmitted BOOLEAN DEFAULT FALSE,
    tlVerified BOOLEAN DEFAULT FALSE,
    tLicenseNumber VARCHAR(100),
    tLicenseExpiryDate DATE,
    tLicensePhotoFront VARCHAR(255),
    tLicensePhotoBack VARCHAR(255),
    averageRating DECIMAL(3,2) DEFAULT 0.00,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- trigger to create profile details after a new user is inserted to the user table

DELIMITER $$

CREATE TRIGGER create_user_profile
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    INSERT INTO profile_details (
        userId,  
        bio,
        languages,
        instaAccount, 
        facebookAccount, 
        dlVerified,
        tlSubmitted,
        tlVerified,
        tLicenseNumber,
        tLicenseExpiryDate,
        tLicensePhotoFront,
        tLicensePhotoBack,
        averageRating
    ) VALUES (
        NEW.id, 
        'Welcome to my profile in tripingoo!',
        NULL,
        NULL, 
        NULL, 
        FALSE,
        FALSE,
        FALSE,
        NULL,
        NULL,
        NULL,
        NULL,
        0.00
    );
END$$

DELIMITER ;


-- view to get complete user profile information
CREATE OR REPLACE VIEW vw_user_complete_profiles AS
SELECT 
    u.id as userId,
    u.account_type,
    u.fullname,
    u.dob,
    u.gender,
    u.phone,
    u.secondary_phone,
    u.address,
    u.email,
    u.profile_photo,
    u.driver_data,
    u.guide_tourist_data,
    u.created_at as user_created_at,
    u.updated_at as user_updated_at,
    
    -- Profile details
    pd.profileId,
    pd.bio,
    pd.languages,
    pd.instaAccount,
    pd.facebookAccount,
    pd.dlVerified,
    pd.tlSubmitted,
    pd.tlVerified,
    pd.tLicenseNumber,
    pd.tLicenseExpiryDate,
    pd.tLicensePhotoFront,
    pd.tLicensePhotoBack,
    pd.averageRating,
    pd.createdAt as profile_created_at,
    pd.updatedAt as profile_updated_at,
    
    -- Calculated fields
    TIMESTAMPDIFF(YEAR, u.dob, CURDATE()) as age,
    CASE 
        WHEN pd.dlVerified = 1 THEN 'Verified'
        ELSE 'Not Verified'
    END as verification_status_text,
    
    -- Social links (if available)
    CASE 
        WHEN pd.instaAccount IS NOT NULL AND pd.facebookAccount IS NOT NULL THEN 'Both'
        WHEN pd.instaAccount IS NOT NULL THEN 'Instagram Only'
        WHEN pd.facebookAccount IS NOT NULL THEN 'Facebook Only'
        ELSE 'No Social Links'
    END as social_links_status
    
FROM users u
LEFT JOIN profile_details pd ON u.id = pd.userId
ORDER BY u.fullname;


CREATE TABLE cover_photos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    userId INT NOT NULL,
    photo_path VARCHAR(255) NOT NULL,
    photo_order TINYINT DEFAULT 0,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (userId) REFERENCES users(userId) ON DELETE CASCADE,
    INDEX idx_user_order (userId, photo_order)
);