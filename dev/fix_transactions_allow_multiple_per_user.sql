-- Allow multiple transactions per user (required for driver/guide payments).
-- Run this in phpMyAdmin or MySQL if you get: Duplicate entry 'X' for key 'userID'

ALTER TABLE transactions DROP INDEX userID;
