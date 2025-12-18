-- Update guide_locations table to add rate_per_hour column
-- This script adds the rate_per_hour field to store guide pricing per hour for each location

-- Add rate_per_hour column to guide_locations table
ALTER TABLE guide_locations 
ADD COLUMN rate_per_hour DECIMAL(10,2) NOT NULL DEFAULT 0.00 
COMMENT 'Hourly rate charged by guide for this location in LKR';

-- Update any existing records to have a default rate (you can change this value as needed)
UPDATE guide_locations 
SET rate_per_hour = 25.00 
WHERE rate_per_hour = 0.00;

-- Add index for better performance when filtering by rate
CREATE INDEX idx_guide_locations_rate ON guide_locations(rate_per_hour);