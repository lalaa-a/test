-- Traveller-side guide requests table
-- Used for: when status='accepted' and created_trips.status='completed', data flows to transactions and payouts

CREATE TABLE IF NOT EXISTS traveller_side_g_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tripId INT NOT NULL COMMENT 'Links to created_trips.tripId',
    guideId INT NOT NULL COMMENT 'Guide user ID (users.id)',
    travellerId INT NULL COMMENT 'Traveller user ID',
    status VARCHAR(50) DEFAULT 'pending' COMMENT 'e.g. pending, accepted, rejected',
    amount DECIMAL(10,2) NULL COMMENT 'Guide earnings for this request',
    duration INT NULL COMMENT 'Hours or days for rate calculation',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tripId) REFERENCES created_trips(tripId) ON DELETE CASCADE,
    FOREIGN KEY (guideId) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_tripId (tripId)
);
