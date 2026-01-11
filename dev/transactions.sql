-- Transactions table for tracking payments and earnings
CREATE TABLE IF NOT EXISTS transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    trip_id INT NOT NULL,
    user_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'LKR',
    transaction_type ENUM('booking', 'refund', 'commission') DEFAULT 'booking',
    payment_method VARCHAR(50),
    transaction_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (trip_id) REFERENCES created_trips(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add some sample transactions
INSERT INTO transactions (trip_id, user_id, amount, transaction_type, payment_method, transaction_status) VALUES
(1, 1, 75000.00, 'booking', 'credit_card', 'completed'),
(2, 1, 125000.00, 'booking', 'bank_transfer', 'completed'),
(3, 1, 95000.00, 'booking', 'credit_card', 'completed'),
(4, 1, 180000.00, 'booking', 'debit_card', 'completed'),
(5, 1, 210000.00, 'booking', 'credit_card', 'completed');
