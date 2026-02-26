-- Commission rates (current) and full change history for auditing
-- Run this on your database before using Commission Management.

-- Current commission rate per role (one row per role)
CREATE TABLE IF NOT EXISTS commission_rates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role VARCHAR(50) NOT NULL UNIQUE COMMENT 'guide or driver',
    rate DECIMAL(5,2) NOT NULL DEFAULT 15.00 COMMENT 'Commission percentage 0-100',
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT NULL COMMENT 'user id who last updated',
    CONSTRAINT fk_commission_rates_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Full history of every commission rate change (audit trail)
CREATE TABLE IF NOT EXISTS commission_rate_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role VARCHAR(50) NOT NULL,
    old_rate DECIMAL(5,2) NOT NULL,
    new_rate DECIMAL(5,2) NOT NULL,
    changed_by INT NULL COMMENT 'user id who made the change',
    change_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    effective_from DATE NOT NULL COMMENT 'date from which new rate applies',
    reason VARCHAR(500) NULL,
    CONSTRAINT fk_commission_history_changed_by FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Seed default rates (ignore if already exist)
INSERT IGNORE INTO commission_rates (role, rate, updated_at, updated_by)
VALUES ('guide', 15.00, NOW(), NULL), ('driver', 12.00, NOW(), NULL);
