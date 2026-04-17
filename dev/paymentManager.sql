
CREATE TABLE trip_payments (

    wholePaymentId INT PRIMARY KEY AUTO_INCREMENT,
    tripId INT NOT NULL, -- tripId of the traveller that done by the traveller
    travellerId INT NOT NULL , --traveller Id who done the payement 
    
    paymentStatus ENUM('pending', 'completed','cancelled','refunded') NOT NULL DEFAULT 'pending',
    totalCharge DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Total charge, dCharge+gCharge+siteCharge', --total charge done by the traveller

    transactionId VARCHAR(255) NULL COMMENT 'Gateway transaction ID',

    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign keys
    FOREIGN KEY (tripId) REFERENCES created_trips(tripId) ON DELETE CASCADE,
    FOREIGN KEY (travellerId) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Indexes
    INDEX idx_traveller (travellerId),
    INDEX idx_trip (tripId)
    
) ENGINE=InnoDB;

-- above trip_payments table paymentStatus explained:- pending = drivers and guides confirmed the trip but traveller didn't payed yet, completed = traveller done the payment, cancelled = trip cancelled by the traveller before payment,refunded =  trip cancelled after payment by the traveler after requesting and refunded by the site.
-- refund happen when traveller contact moderator then moderator can refund manually. when refunding it happen at once to but it marked in relevenat driver_payments and guide_payments.

CREATE TABLE driver_payments (

    driverPaymentId INT PRIMARY KEY AUTO_INCREMENT,

    wholePaymentId INT NOT NULL,
    driverId INT NOT NULL,
    
    driverCharge INT NOT NULL COMMENT 'driver charge',
    
    pDoneTraveller BOOLEAN DEFAULT FALSE COMMENT 'Payment done by the traveller',
    pDoneSite BOOLEAN DEFAULT FALSE COMMENT 'Payment done by the site/platform',
    pDateTraveller TIMESTAMP NULL COMMENT 'The date that payment done by the traveller',
    pDateSite TIMESTAMP NULL COMMENT 'the date that completed by the site/platform for the driver',
    
    refunded BOOLEAN DEFAULT FALSE COMMENT 'Whether payment was refunded',
    refundDate TIMESTAMP NULL COMMENT 'date that refunded by the site',
    refundAmount DECIMAL(10,2) NULL COMMENT 'Amount refunded',
    refundReason TEXT NULL COMMENT 'Reason for refund',
    
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign keys
    FOREIGN KEY (wholePaymentId) REFERENCES trip_payments(wholePaymentId) ON DELETE CASCADE,
    FOREIGN KEY (driverId) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Indexes
    INDEX idx_driver (driverId),
    INDEX idx_payment_status (pDoneTraveller, pDoneSite),
    INDEX idx_refunded (refunded),
    INDEX idx_payment_date_traveller (pDateTraveller),
    INDEX idx_payment_date_site (pDateSite)
    
) ENGINE=InnoDB;


--this contain the guide payments that done by the traveller
CREATE TABLE guide_payments (

    guidePaymentId INT PRIMARY KEY AUTO_INCREMENT,

    wholePaymentId INT NOT NULL,
    guideId INT NOT NULL,
    
    guideCharge INT NOT NULL COMMENT 'guide charge',
    
    pDoneTraveller BOOLEAN DEFAULT FALSE COMMENT 'Payment done by the traveller',
    pDoneSite BOOLEAN DEFAULT FALSE COMMENT 'Payment done by the site/platform',
    pDateTraveller TIMESTAMP NULL COMMENT 'When payment was completed by the traveller',
    pDateSite TIMESTAMP NULL COMMENT 'When payment was completed by the site/platform for the guide',
    
    refunded BOOLEAN DEFAULT FALSE COMMENT 'Whether payment was refunded by the site',
    refundDate TIMESTAMP NULL COMMENT 'The refunded date from the site',
    refundAmount DECIMAL(10,2) NULL COMMENT 'Amount refunded', 
    refundReason TEXT NULL COMMENT 'Reason for refund',
    
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign keys
    FOREIGN KEY (wholePaymentId) REFERENCES trip_payments(wholePaymentId) ON DELETE CASCADE,
    FOREIGN KEY (guideId) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Indexes
    INDEX idx_guide (guideId),
    INDEX idx_payment_status (pDoneTraveller, pDoneSite),
    INDEX idx_refunded (refunded),
    INDEX idx_payment_date_traveller (pDateTraveller),
    INDEX idx_payment_date_site (pDateSite)
    
) ENGINE=InnoDB;

CREATE TABLE site_service_charges (

    chargeId INT PRIMARY KEY AUTO_INCREMENT,
    moderatorId INT NOT NULL COMMENT 'Moderator who set these charges',
    
    dBookCharge DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Driver booking charge site keeps (fixed amount)',
    gBookCharge DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Guide booking charge site keeps (fixed amount)',
    siteServiceCharge DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Site service charge (percentage, e.g., 10.00 for 10%)',
    
    isActive BOOLEAN DEFAULT TRUE COMMENT 'Whether these charges are currently active',
    
    notes TEXT NULL COMMENT 'Additional notes about this charge structure',
    
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (moderatorId) REFERENCES users(id) ON DELETE RESTRICT,
    
    -- Indexes
    INDEX idx_moderator (moderatorId),
    INDEX idx_active (isActive),
    INDEX idx_effective_date (createdAt)
    
) ENGINE=InnoDB;

