-- Database updates for file preview and management features

-- Add new columns to tbl_files_uploaded table
ALTER TABLE `tbl_files_uploaded`
    ADD COLUMN `up_file_thumbnail` varchar(255) DEFAULT NULL AFTER `up_file_Size`,
ADD COLUMN `up_file_tags` text DEFAULT NULL AFTER `up_file_thumbnail`,
ADD COLUMN `up_file_category` varchar(100) DEFAULT NULL AFTER `up_file_tags`,
ADD COLUMN `up_file_description` text DEFAULT NULL AFTER `up_file_category`,
ADD COLUMN `up_file_preview_available` tinyint(1) DEFAULT 0 AFTER `up_file_description`,
ADD COLUMN `up_file_width` int(11) DEFAULT NULL AFTER `up_file_preview_available`,
ADD COLUMN `up_file_height` int(11) DEFAULT NULL AFTER `up_file_width`;

-- Create file tags table for better tag management
CREATE TABLE IF NOT EXISTS `tbl_file_tags` (
    `tag_id` int(11) NOT NULL AUTO_INCREMENT,
    `tag_name` varchar(50) NOT NULL,
    `tag_color` varchar(7) DEFAULT '#007bff',
    `tag_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`tag_id`),
    UNIQUE KEY `tag_name` (`tag_name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create file categories table
CREATE TABLE IF NOT EXISTS `tbl_file_categories` (
    `category_id` int(11) NOT NULL AUTO_INCREMENT,
    `category_name` varchar(100) NOT NULL,
    `category_icon` varchar(50) DEFAULT 'mdi-folder',
    `category_color` varchar(7) DEFAULT '#6c757d',
    `category_description` text,
    `category_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`category_id`),
    UNIQUE KEY `category_name` (`category_name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default categories
INSERT IGNORE INTO `tbl_file_categories` (`category_name`, `category_icon`, `category_color`, `category_description`) VALUES
('Documents', 'mdi-file-document', '#007bff', 'PDF, Word, Excel, and other document files'),
('Images', 'mdi-image', '#28a745', 'Photos, graphics, and image files'),
('Videos', 'mdi-video', '#dc3545', 'Video files and movies'),
('Audio', 'mdi-music', '#ffc107', 'Music and audio files'),
('Archives', 'mdi-zip-box', '#6c757d', 'ZIP, RAR, and compressed files'),
('Code', 'mdi-code-tags', '#17a2b8', 'Source code and programming files'),
('Other', 'mdi-file', '#6c757d', 'Other file types');

-- Insert default tags
INSERT IGNORE INTO `tbl_file_tags` (`tag_name`, `tag_color`) VALUES
('Important', '#dc3545'),
('Work', '#007bff'),
('Personal', '#28a745'),
('Shared', '#ffc107'),
('Temporary', '#6c757d'),
('Backup', '#17a2b8');
