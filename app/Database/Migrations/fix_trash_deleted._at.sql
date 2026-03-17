-- Fix for trash page: Add deleted_at column to tbl_files_uploaded_deleted table
-- Run this SQL query in your database to fix the "Unknown column 'deleted_at'" error

-- Add deleted_at column to files deleted table
ALTER TABLE `tbl_files_uploaded_deleted`
    ADD COLUMN `deleted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `up_file_height`;

-- Update existing records to have deleted_at set to created_at (since we don't know when they were actually deleted)
UPDATE `tbl_files_uploaded_deleted`
SET `deleted_at` = `up_file_Created_at`
WHERE `deleted_at` IS NULL OR `deleted_at` = '0000-00-00 00:00:00';

-- Verify the column was added
DESCRIBE `tbl_files_uploaded_deleted`;
