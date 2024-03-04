-- Adminer 4.8.1 MySQL 10.4.32-MariaDB dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `cart_item`;
CREATE TABLE `cart_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F0FE2527A76ED395` (`user_id`),
  KEY `IDX_F0FE2527BA8E87C4` (`food_id`),
  CONSTRAINT `FK_F0FE2527A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_F0FE2527BA8E87C4` FOREIGN KEY (`food_id`) REFERENCES `food` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cart_item` (`id`, `user_id`, `food_id`, `quantity`) VALUES
(6,	21,	24,	4);

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `featured` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `category` (`id`, `name`, `image`, `featured`, `active`) VALUES
(8,	'Category 0',	NULL,	1,	1),
(9,	'Category 1',	NULL,	1,	1),
(10,	'Category 2',	NULL,	1,	1),
(11,	'Category 3',	NULL,	1,	1),
(12,	'Category 4',	NULL,	1,	1);

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20240220084532',	'2024-02-20 09:45:40',	115),
('DoctrineMigrations\\Version20240224221313',	'2024-02-24 23:13:37',	139),
('DoctrineMigrations\\Version20240224222217',	'2024-02-24 23:22:25',	125),
('DoctrineMigrations\\Version20240301191433',	'2024-03-01 20:14:53',	164),
('DoctrineMigrations\\Version20240301203052',	'2024-03-01 21:31:00',	12),
('DoctrineMigrations\\Version20240301203511',	'2024-03-01 21:35:18',	8),
('DoctrineMigrations\\Version20240302141227',	'2024-03-02 15:12:34',	53);

DROP TABLE IF EXISTS `food`;
CREATE TABLE `food` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `price` double NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `featured` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D43829F712469DE2` (`category_id`),
  CONSTRAINT `FK_D43829F712469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `food` (`id`, `category_id`, `name`, `description`, `price`, `image`, `featured`, `active`) VALUES
(18,	12,	'Food 0',	'Description 0',	52,	'food0.jpg',	1,	1),
(19,	12,	'Food 1',	'Description 1',	24,	'food1.jpg',	1,	1),
(20,	12,	'Food 2',	'Description 2',	72,	'food2.jpg',	1,	1),
(21,	8,	'Food 3',	'Description 3',	88,	'food3.jpg',	1,	1),
(22,	10,	'Food 4',	'Description 4',	37,	'food4.jpg',	1,	1),
(23,	12,	'Food 5',	'Description 5',	96,	'food5.jpg',	1,	1),
(24,	11,	'Food 6',	'Description 6',	51,	'food6.jpg',	1,	1),
(25,	11,	'Food 7',	'Description 7',	92,	'food7.jpg',	1,	1),
(26,	11,	'Food 8',	'Description 8',	74,	'food8.jpg',	1,	1),
(27,	9,	'Food 9',	'Description 9',	29,	'food9.jpg',	1,	1),
(28,	11,	'CarotCake',	'Le bon gâteau aux carottes',	6,	'IMG-20240129-WA0001-65dc6629812b1.jpg',	1,	1);

DROP TABLE IF EXISTS `order`;
CREATE TABLE `order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `status` varchar(255) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `total_items` int(11) NOT NULL,
  `total_price` double NOT NULL,
  `address` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F5299398A76ED395` (`user_id`),
  CONSTRAINT `FK_F5299398A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `order` (`id`, `user_id`, `created_at`, `status`, `note`, `total_items`, `total_price`, `address`, `country`, `city`, `zip`) VALUES
(1,	21,	'2024-03-01 21:49:54',	'delivered',	'Déposer à Alfred',	18,	778,	'Manoir Wayne',	'USA',	'Gotham',	'12345'),
(2,	21,	'2024-03-02 03:06:17',	'pending',	NULL,	4,	204,	'Avenue du Jour',	'France',	'Cergy',	'95800');

DROP TABLE IF EXISTS `order_detail`;
CREATE TABLE `order_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `unit_price` double NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_ED896F46FCDAEAAA` (`order_id_id`),
  KEY `IDX_ED896F46BA8E87C4` (`food_id`),
  CONSTRAINT `FK_ED896F46BA8E87C4` FOREIGN KEY (`food_id`) REFERENCES `food` (`id`),
  CONSTRAINT `FK_ED896F46FCDAEAAA` FOREIGN KEY (`order_id_id`) REFERENCES `order` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `order_detail` (`id`, `order_id_id`, `food_id`, `unit_price`, `quantity`) VALUES
(1,	1,	22,	37,	10),
(2,	1,	24,	51,	8),
(3,	2,	24,	51,	4);

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `first_name`, `last_name`, `phone_number`) VALUES
(16,	'hermione0.potter@hotmail.com',	'[\"ROLE_USER\"]',	'$2y$13$Q2f8VNQbHV5DZX8pphaikOwBeKfzBtyHQsmLtQfFCJhIQflXwXRpO',	'',	'',	''),
(17,	'frodo1.greenleaf@yahoo.com',	'[\"ROLE_USER\"]',	'$2y$13$n28elo4pFrq7zfewRWHCQeKyc04uHt2FD3UHvjlMTse6TeHlD5EZO',	'',	'',	''),
(18,	'harry2.parker@outlook.com',	'[\"ROLE_USER\"]',	'$2y$13$5tyoL6hKSb7uLzJ8QVpJ9.1WuVFpDk/4y3q5zUMqMnzwBO/Sx.Rfe',	'',	'',	''),
(19,	'gimli3.grey@gmail.com',	'[\"ROLE_USER\"]',	'$2y$13$GqPKDNQCMMoWqEpR6XYvUulU.iw57TH/xPTx9.5Y.QcpjdkstKt2y',	'',	'',	''),
(20,	'sam4.gandalf@hotmail.com',	'[\"ROLE_USER\"]',	'$2y$13$ArzxlfFl1.t6kkwDmdvShey19yCeP1kwFQH1kUof0QNjxcXNhNprS',	'',	'',	''),
(21,	'0.parker@yahoo.com',	'[\"ROLE_ADMIN\"]',	'$2y$13$W61.q2I5IkLAWLszhcUKXewjfQynd0DldUeE2/kUBkLAIbfF1okuK',	'',	'',	''),
(22,	'1.grey@gmail.com',	'[\"ROLE_ADMIN\"]',	'$2y$13$mQrQGOKX7Vg4K1a52CpfQ.U/nq6e8I91Qa9u0LMWG/fjcVZvNLnJe',	'',	'',	''),
(23,	'2.greenleaf@outlook.com',	'[\"ROLE_ADMIN\"]',	'$2y$13$SRMpWF5q33y3cf9H2L3L6eeVHCPq9CAoHnDwXbr7FeqDvOyvFPY3C',	'',	'',	''),
(24,	'3.grey@gmail.com',	'[\"ROLE_ADMIN\"]',	'$2y$13$X2zXi0ScUpRGiIUVamE7WOGuoHPVP0Qq9RJIHSLnEMg2jHOJYa9Ie',	'',	'',	''),
(25,	'4.granger@gmail.com',	'[\"ROLE_ADMIN\"]',	'$2y$13$4aODO3jt9HNi.9qQysVOouphdgmKozF.nPwPds2rwa8dlZbcLypYW',	'',	'',	''),
(26,	'clarkkent@dailyplanet.com',	'[\"ROLE_USER\"]',	'$2y$13$oF.KvN6IVuQv1JaHQASGO.gpxsUTuh5nVhouSTyhBZoqpjc5ayCuy',	'Clark',	'Kent',	'0123456789'),
(27,	'brucew@wayne.com',	'[\"ROLE_ADMIN\"]',	'$2y$13$UatgTeUkTTPrBYEfcKO4GumRg8yg/WmLRS0fJ5rOdD2oOMT/S2GHi',	'Bruce',	'Wayne',	'0342456373');

-- 2024-03-02 21:19:16
