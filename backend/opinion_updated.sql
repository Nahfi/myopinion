-- ============================================================
-- Opinion App — Updated SQL Schema
-- Changes: 
--   1. comments.status column (pending / active / rejected)
--   2. reaction_types table (dynamic emoji reactions)  
--   3. reaction.reaction_type_id (links to reaction_types)
--   4. UNIQUE constraint on reaction (one per user/post)
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ─── users ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
  `id`         int NOT NULL AUTO_INCREMENT,
  `name`       varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username`   varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email`      varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password`   varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role`       enum('user','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` VALUES
(1,'SuperAdmin','admin','admin@example.com','$2y$10$Ib2HWVsIbGfYpB/ZdDzZWed5tQskS4zEXwwNyvjyZvhZ5WQs7ejFW','admin','2025-05-21 14:38:22'),
(2,'bappe','bappe','bappe@example.com','$2y$10$Ib2HWVsIbGfYpB/ZdDzZWed5tQskS4zEXwwNyvjyZvhZ5WQs7ejFW','user','2025-05-22 13:42:06');

-- ─── posts ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `posts` (
  `id`         int NOT NULL AUTO_INCREMENT,
  `title`      varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content`    text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_url`  varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id`    int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `posts` VALUES
(3,'sdf','sfsdfs','/uploads/684536bd446f2_Screenshot from 2025-06-07 01-20-51.png',1,'2025-06-08 07:07:41'),
(7,'fsdfsdf','sdfsdfsdfsdfsd','/uploads/68453d7b9a06e_Screenshot from 2025-06-07 22-22-42.png',1,'2025-06-08 07:36:27'),
(8,'sdfsd','fsdfsfsdfsdf','',1,'2025-06-08 07:36:35');

-- ─── reaction_types (NEW) ────────────────────────────────────
CREATE TABLE IF NOT EXISTS `reaction_types` (
  `id`         int NOT NULL AUTO_INCREMENT,
  `name`       varchar(50)  COLLATE utf8mb4_unicode_ci NOT NULL,
  `emoji`      varchar(10)  COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active`  tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `reaction_types` VALUES
(1,'Like','👍',1,1,NOW()),
(2,'Love','❤️',1,2,NOW()),
(3,'Haha','😂',1,3,NOW()),
(4,'Wow','😮',1,4,NOW()),
(5,'Sad','😢',1,5,NOW()),
(6,'Angry','😡',1,6,NOW());

-- ─── reaction (UPDATED — added reaction_type_id + unique key) 
CREATE TABLE IF NOT EXISTS `reaction` (
  `id`               int NOT NULL AUTO_INCREMENT,
  `user_id`          int NOT NULL,
  `post_id`          int NOT NULL,
  `reaction_type_id` int NOT NULL DEFAULT 1,
  `created_at`       datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_post` (`user_id`,`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- If you already have the reaction table, run this instead:
-- ALTER TABLE `reaction` ADD COLUMN `reaction_type_id` int NOT NULL DEFAULT 1;
-- ALTER TABLE `reaction` ADD UNIQUE KEY `unique_user_post` (`user_id`,`post_id`);

-- ─── comments (UPDATED — added status column) ────────────────
CREATE TABLE IF NOT EXISTS `comments` (
  `id`         int NOT NULL AUTO_INCREMENT,
  `post_id`    int NOT NULL,
  `user_id`    int NOT NULL,
  `content`    text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status`     enum('pending','active','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- If you already have the comments table, run this instead:
-- ALTER TABLE `comments` ADD COLUMN `status` ENUM('pending','active','rejected') NOT NULL DEFAULT 'pending' AFTER `content`;

INSERT INTO `comments` VALUES
(1,2,1,'sdfsdf','active','2025-06-08 07:18:11'),
(2,3,1,'hello','active','2025-06-08 07:34:21'),
(3,7,1,'this is my comment','active','2025-06-08 07:40:34'),
(4,8,2,'this is my comment','pending','2025-06-08 07:41:06'),
(5,8,2,'sdfsdfsd','pending','2025-06-08 07:54:18'),
(6,3,2,'sdfsdfsdfs','active','2025-06-08 08:00:52'),
(7,3,2,'dsfsdfsdf','active','2025-06-08 08:01:46'),
(8,3,2,'sdfsdfsf','pending','2025-06-08 17:29:46');

-- ─── token_blacklist ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `token_blacklist` (
  `id`             int NOT NULL AUTO_INCREMENT,
  `token`          text COLLATE utf8mb4_unicode_ci NOT NULL,
  `blacklisted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
