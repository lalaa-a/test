-- Add child_seats column to vehicles table
-- Run this to update existing vehicles table structure

ALTER TABLE vehicles
ADD COLUMN child_seats INT NOT NULL DEFAULT 0 AFTER seatingCapacity;

-- Update the index if needed (no index needed for child_seats as it's not frequently queried)