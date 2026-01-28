-- Update guide_locations table for storing guiding locations (updated structure)
-- First, modify the existing table to add visit_hours and remove unwanted fields

-- Add visit_hours column if it doesn't exist
ALTER TABLE guide_locations 
ADD COLUMN IF NOT EXISTS visit_hours DECIMAL(3,1) NOT NULL DEFAULT 1.0 COMMENT 'How many hours to complete the visit';

-- If you want to drop columns (be careful with existing data):
-- ALTER TABLE guide_locations DROP COLUMN IF EXISTS country;
-- ALTER TABLE guide_locations DROP COLUMN IF EXISTS location_photo;
-- ALTER TABLE guide_locations DROP COLUMN IF EXISTS verification_status;
-- ALTER TABLE guide_locations DROP COLUMN IF EXISTS verified_by;
-- ALTER TABLE guide_locations DROP COLUMN IF EXISTS verified_at;
-- ALTER TABLE guide_locations DROP COLUMN IF EXISTS rejection_reason;
-- ALTER TABLE guide_locations DROP COLUMN IF EXISTS is_active;
