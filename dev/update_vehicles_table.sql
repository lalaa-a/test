-- Add seat_count and daily_rate columns to vehicles table
-- Run this to update existing vehicles table structure

ALTER TABLE vehicles 
ADD COLUMN seat_count INT NOT NULL DEFAULT 5 AFTER vehicle_type,
ADD COLUMN daily_rate DECIMAL(10,2) NOT NULL DEFAULT 50.00 AFTER seat_count;

-- Update the index if needed
CREATE INDEX idx_daily_rate ON vehicles(daily_rate);