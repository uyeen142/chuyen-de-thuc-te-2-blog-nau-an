-- XÓA VÀ TẠO MỚI CƠ SỞ DỮ LIỆU
DROP DATABASE IF EXISTS `blog_nau_an`;
CREATE DATABASE `blog_nau_an` CHARACTER SET utf8mb4;
USE `blog_nau_an`;

-- TẠO BẢNG admins
CREATE TABLE `admins` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
);

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$gSHjVmYK.AahKUA7j.WHNOqFJJQe9vWgMt5LbzoO3fGTCFTJgRhym');

-- TẠO BẢNG posts
CREATE TABLE `posts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `thumbnail` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `overview` TEXT COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `category` VARCHAR(50) COLLATE utf8mb4_general_ci DEFAULT 'mon_an_man',
  PRIMARY KEY (`id`)
);

-- (Thêm dữ liệu bảng posts bên dưới nếu muốn, đã có trong file bạn gửi)

-- TẠO BẢNG ingredients
CREATE TABLE `ingredients` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `post_id` INT(11) DEFAULT NULL,
  `name` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `amount` VARCHAR(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  CONSTRAINT `ingredients_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
);

-- TẠO BẢNG steps
CREATE TABLE `steps` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `post_id` INT(11) DEFAULT NULL,
  `description` TEXT COLLATE utf8mb4_general_ci NOT NULL,
  `image` VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `step_order` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  CONSTRAINT `steps_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
);

-- TẠO BẢNG users
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` VARCHAR(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
);

CREATE TABLE favorite_posts (
  user_id INT NOT NULL,
  post_id INT NOT NULL,
  PRIMARY KEY (user_id, post_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- Thêm bảng notifications vào blog_nau_an (nếu chưa có)
CREATE TABLE `notifications` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `message` TEXT COLLATE utf8mb4_unicode_ci NOT NULL, -- Nội dung thông báo
    `link` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL, -- Liên kết khi click vào thông báo
    `is_read` TINYINT(1) NOT NULL DEFAULT 0, -- 0 = chưa đọc, 1 = đã đọc
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);
