-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 07/05/2025
-- Versão do servidor: 10.11.10-MariaDB
-- Versão do PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `clients` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(255) NOT NULL,
    `username` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `profile_picture` LONGBLOB DEFAULT NULL,
    `verified` TINYINT(1) NOT NULL DEFAULT 0,
    `verification_code` VARCHAR(6) DEFAULT NULL,
    `verification_expires` DATETIME DEFAULT NULL,
    `verification_attempts` TINYINT(4) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`),
    UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `clients` (`id`, `email`, `username`, `verified`) VALUES
(1, 'admin@exemplo.com', 'admin', 1);

CREATE TABLE `users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expires_at` TIMESTAMP NULL DEFAULT NULL,
    `hwid` VARCHAR(255) DEFAULT NULL,
    `banned` TINYINT(1) NOT NULL DEFAULT 0,
    `banned_reason` VARCHAR(255) DEFAULT NULL,
    `paused` TINYINT(4) DEFAULT 0,
    `max_devices` INT(11) NOT NULL DEFAULT 1,
    `used_devices` INT(11) NOT NULL DEFAULT 0,
    `created_by` VARCHAR(255) NOT NULL COMMENT 'Discord ID do criador',
    `hwid_reset_count` INT(11) DEFAULT 0,
    `product_id` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`),
    KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `username`, `password`, `created_by`, `product_id`) VALUES
(1, 'admin_user', '1', 'admin_default', 1);

CREATE TABLE `products` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `products` (`id`, `name`, `description`) VALUES
(1, 'Produto Padrão', 'Produto padrão para testes');

-
CREATE TABLE `settings` (
    `name` VARCHAR(50) NOT NULL,
    `value` VARCHAR(50) DEFAULT NULL,
    PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `settings` (`name`, `value`) VALUES
('cheat_api_status', 'on');

CREATE TABLE `support_tickets` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `client_id` INT(11) NOT NULL,
    `admin_id` INT(11) DEFAULT NULL,
    `status` ENUM('open','in_progress','closed') DEFAULT 'open',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `support_tickets` (`id`, `client_id`, `status`) VALUES
(1, 1, 'open');

CREATE TABLE `support_messages` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `ticket_id` INT(11) NOT NULL,
    `sender_id` INT(11) NOT NULL,
    `sender_type` ENUM('client','admin') NOT NULL,
    `message` TEXT NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `support_messages` (`id`, `ticket_id`, `sender_id`, `sender_type`, `message`) VALUES
(1, 1, 1, 'client', 'Mensagem de teste');

CREATE TABLE `license_keys` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `key_code` VARCHAR(19) NOT NULL,
    `duration_days` INT(11) NOT NULL,
    `max_uses` INT(11) NOT NULL,
    `used_uses` INT(11) DEFAULT 0,
    `key_type` ENUM('standard','premium') DEFAULT 'standard',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `product_id` INT(11) NOT NULL,
    `redeemed_by` INT(11) DEFAULT NULL,
    `redeemed_at` DATETIME DEFAULT NULL,
    `is_redeemed` TINYINT(1) DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `key_code` (`key_code`),
    KEY `product_id` (`product_id`),
    KEY `redeemed_by` (`redeemed_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `license_keys` (`id`, `key_code`, `duration_days`, `max_uses`, `key_type`, `product_id`) VALUES
(1, 'TEST-KEY-1234-5678', 30, 1, 'standard', 1);

CREATE TABLE `user_devices` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `hwid` VARCHAR(255) NOT NULL,
    `last_login` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_user_hwid` (`username`,`hwid`),
    CONSTRAINT `user_devices_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `users`
    ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);


ALTER TABLE `support_tickets`
    ADD CONSTRAINT `support_tickets_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`);


ALTER TABLE `support_messages`
    ADD CONSTRAINT `support_messages_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`);


ALTER TABLE `license_keys`
    ADD CONSTRAINT `license_keys_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
    ADD CONSTRAINT `license_keys_ibfk_2` FOREIGN KEY (`redeemed_by`) REFERENCES `clients` (`id`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;