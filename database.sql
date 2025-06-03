-- AgroSmart Market Database Schema
-- -----------------------------------------------------
-- This script creates the complete database structure 
-- and populates it with essential data for the AgroSmart Market platform

-- Create database (uncomment to create new database)
-- CREATE DATABASE IF NOT EXISTS agrosmart_market;
-- USE agrosmart_market;

-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `user_type` ENUM('admin', 'farmer', 'buyer') NOT NULL,
  `location` VARCHAR(100),
  `profile_image` VARCHAR(255),
  `bio` TEXT,
  `phone` VARCHAR(20),
  `date_joined` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `email_verified` TINYINT(1) DEFAULT 0,
  `verification_token` VARCHAR(255),
  `last_login` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `products`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `farmer_id` INT(11) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `price` DECIMAL(10,2) NOT NULL,
  `image` VARCHAR(255),
  `category` VARCHAR(50),
  `stock` INT(11) NOT NULL DEFAULT 0,
  `location` VARCHAR(100),
  `is_organic` TINYINT(1) DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `date_added` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`farmer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `orders`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `buyer_id` INT(11) NOT NULL,
  `farmer_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `quantity` INT(11) NOT NULL,
  `total_price` DECIMAL(10,2) NOT NULL,
  `status` ENUM('pending', 'accepted', 'rejected', 'completed') NOT NULL DEFAULT 'pending',
  `date_ordered` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `date_updated` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`buyer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`farmer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `messages`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `messages` (
  `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `sender_id` INT(11) NOT NULL,
  `receiver_id` INT(11) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `date_sent` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `related_product_id` INT(11) NULL,
  FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`receiver_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`related_product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `reviews`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT(11) NOT NULL,
  `buyer_id` INT(11) NOT NULL,
  `rating` INT(1) NOT NULL CHECK (rating BETWEEN 1 AND 5),
  `comment` TEXT,
  `date_posted` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`buyer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Create required indexes for better performance
-- -----------------------------------------------------
CREATE INDEX `idx_products_farmer` ON `products` (`farmer_id`);
CREATE INDEX `idx_products_category` ON `products` (`category`);
CREATE INDEX `idx_orders_buyer` ON `orders` (`buyer_id`);
CREATE INDEX `idx_orders_farmer` ON `orders` (`farmer_id`);
CREATE INDEX `idx_orders_product` ON `orders` (`product_id`);
CREATE INDEX `idx_messages_sender` ON `messages` (`sender_id`);
CREATE INDEX `idx_messages_receiver` ON `messages` (`receiver_id`);
CREATE INDEX `idx_messages_is_read` ON `messages` (`is_read`);
CREATE INDEX `idx_reviews_product` ON `reviews` (`product_id`);
CREATE INDEX `idx_reviews_buyer` ON `reviews` (`buyer_id`);
