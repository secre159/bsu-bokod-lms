-- Fix missing database objects

-- 1. Create suggested_books table
CREATE TABLE IF NOT EXISTS `suggested_books` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `isbn` varchar(50) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `suggested_by` varchar(100) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(20) DEFAULT 'Pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Add archived column to faculty table if it doesn't exist
ALTER TABLE `faculty` ADD COLUMN IF NOT EXISTS `archived` tinyint(1) NOT NULL DEFAULT 0;

-- 3. Create archived_subject table if not exists (from earlier requirement)
CREATE TABLE IF NOT EXISTS `archived_subject` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `date_archived` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Add created_at to calibre_books_archive if not exists (from earlier requirement)
ALTER TABLE `calibre_books_archive` ADD COLUMN IF NOT EXISTS `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP;
