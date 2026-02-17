CREATE TABLE payouts (
    payoutID INT AUTO_INCREMENT PRIMARY KEY,
    tripID INT NOT NULL,
    userID INT NOT NULL,

    earnings DECIMAL(10,2) NOT NULL,
    commission DECIMAL(10,2) NOT NULL,
    net_payout DECIMAL(10,2) NOT NULL,

    payout_status ENUM('Pending', 'Completed', 'Failed') DEFAULT 'Pending',

    payout_date DATE DEFAULT CURRENT_DATE,
    payout_time TIME DEFAULT CURRENT_TIME,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_payout_trip
        FOREIGN KEY (tripID) REFERENCES trips(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_payout_user
        FOREIGN KEY (userID) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
