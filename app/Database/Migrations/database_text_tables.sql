-- Database tables for text copying functionality
-- Run these SQL commands to create the necessary tables

-- Table for storing uploaded texts
CREATE TABLE IF NOT EXISTS `tbl_texts_uploaded` (
    `text_id` int(11) NOT NULL AUTO_INCREMENT,
    `text_uuid` varchar(20) NOT NULL,
    `text_session_id` varchar(20) NOT NULL,
    `text_dev_id` varchar(100) DEFAULT NULL,
    `text_title` varchar(255) DEFAULT NULL,
    `text_content` longtext NOT NULL,
    `text_source` varchar(50) NOT NULL DEFAULT 'Browser Text',
    `text_created_at` datetime NOT NULL,
    `text_count` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`text_id`),
    UNIQUE KEY `text_uuid` (`text_uuid`),
    KEY `text_session_id` (`text_session_id`),
    KEY `text_dev_id` (`text_dev_id`),
    KEY `text_created_at` (`text_created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table for storing deleted texts (archive)
CREATE TABLE IF NOT EXISTS `tbl_texts_uploaded_deleted` (
    `text_id` int(11) NOT NULL,
    `text_uuid` varchar(20) NOT NULL,
    `text_session_id` varchar(20) NOT NULL,
    `text_dev_id` varchar(100) DEFAULT NULL,
    `text_title` varchar(255) DEFAULT NULL,
    `text_content` longtext NOT NULL,
    `text_source` varchar(50) NOT NULL DEFAULT 'Browser Text',
    `text_created_at` datetime NOT NULL,
    `text_count` int(11) NOT NULL DEFAULT 0,
    `deleted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`text_id`),
    KEY `text_uuid` (`text_uuid`),
    KEY `deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
