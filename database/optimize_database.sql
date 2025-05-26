-- AgroSmart Market Database Optimization Script
-- Enhances database structure for improved scalability with larger datasets

-- Ensure we're using the correct database
-- USE agrosmart_market;

-- ----------------------------------------------------------------
-- STEP 1: Add indexes to improve query performance
-- ----------------------------------------------------------------

-- Add indexes to users table
ALTER TABLE `users` 
  ADD INDEX `idx_user_type` (`user_type`),
  ADD INDEX `idx_email_verified` (`email_verified`),
  ADD INDEX `idx_location` (`location`),
  ADD INDEX `idx_created_at` (`created_at`);

-- Add indexes to products table
ALTER TABLE `products` 
  ADD INDEX `idx_products_farmer` (`farmer_id`),
  ADD INDEX `idx_products_category` (`category`),
  ADD INDEX `idx_products_created` (`created_at`),
  ADD INDEX `idx_products_price` (`price`),
  ADD INDEX `idx_products_stock` (`stock`),
  ADD INDEX `idx_products_active` (`is_active`),
  ADD FULLTEXT INDEX `ftx_products_search` (`name`, `description`);

-- Add indexes to orders table
ALTER TABLE `orders` 
  ADD INDEX `idx_orders_buyer` (`buyer_id`),
  ADD INDEX `idx_orders_status` (`status`),
  ADD INDEX `idx_orders_payment_status` (`payment_status`),
  ADD INDEX `idx_orders_created` (`created_at`);

-- Add indexes to order_items table
ALTER TABLE `order_items` 
  ADD INDEX `idx_order_items_product` (`product_id`),
  ADD INDEX `idx_order_items_farmer` (`farmer_id`),
  ADD INDEX `idx_order_items_status` (`status`);

-- Add indexes to messages table
ALTER TABLE `messages` 
  ADD INDEX `idx_messages_sender` (`sender_id`),
  ADD INDEX `idx_messages_receiver` (`receiver_id`),
  ADD INDEX `idx_messages_product` (`related_product_id`),
  ADD INDEX `idx_messages_is_read` (`is_read`),
  ADD INDEX `idx_messages_created` (`created_at`);

-- Add indexes to reviews table
ALTER TABLE `reviews` 
  ADD INDEX `idx_reviews_product` (`product_id`),
  ADD INDEX `idx_reviews_buyer` (`buyer_id`),
  ADD INDEX `idx_reviews_rating` (`rating`),
  ADD INDEX `idx_reviews_created` (`created_at`);

-- Add indexes to cart_items table
ALTER TABLE `cart_items` 
  ADD INDEX `idx_cart_buyer` (`buyer_id`),
  ADD INDEX `idx_cart_product` (`product_id`),
  ADD INDEX `idx_cart_created` (`created_at`);

-- ----------------------------------------------------------------
-- STEP 2: Add partitioning for large tables
-- ----------------------------------------------------------------

-- Add PARTITION BY RANGE based on created_at for orders table
-- This requires converting the created_at column to DATETIME instead of TIMESTAMP
-- Note: This requires removing foreign keys first, then adding them back

-- Uncomment these lines to implement partitioning (requires adjusting foreign keys first)
/*
ALTER TABLE `orders` 
  MODIFY COLUMN `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE `orders` 
  PARTITION BY RANGE (YEAR(created_at)) (
    PARTITION p_2023 VALUES LESS THAN (2024),
    PARTITION p_2024 VALUES LESS THAN (2025),
    PARTITION p_2025 VALUES LESS THAN (2026),
    PARTITION p_future VALUES LESS THAN MAXVALUE
  );
*/

-- ----------------------------------------------------------------
-- STEP 3: Create tables for better data separation
-- ----------------------------------------------------------------

-- Create a product_images table to separate images from product data
-- This allows for multiple images per product
CREATE TABLE IF NOT EXISTS `product_images` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `product_id` INT NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `thumbnail_path` VARCHAR(255) NULL,
  `medium_path` VARCHAR(255) NULL,
  `large_path` VARCHAR(255) NULL,
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_product_images_product`
    FOREIGN KEY (`product_id`)
    REFERENCES `products` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create an index on product_id for fast lookups
ALTER TABLE `product_images` 
  ADD INDEX `idx_product_images_product` (`product_id`),
  ADD INDEX `idx_product_images_primary` (`is_primary`);

-- Create a product_inventory table to track inventory changes
CREATE TABLE IF NOT EXISTS `product_inventory` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `product_id` INT NOT NULL,
  `quantity_change` INT NOT NULL,
  `reason` ENUM('initial', 'order', 'adjustment', 'return', 'expiry') NOT NULL,
  `reference_id` INT NULL, -- Order ID or other reference
  `notes` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_inventory_product`
    FOREIGN KEY (`product_id`)
    REFERENCES `products` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_user`
    FOREIGN KEY (`created_by`)
    REFERENCES `users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add indexes to product_inventory table
ALTER TABLE `product_inventory` 
  ADD INDEX `idx_inventory_product` (`product_id`),
  ADD INDEX `idx_inventory_reason` (`reason`),
  ADD INDEX `idx_inventory_reference` (`reference_id`),
  ADD INDEX `idx_inventory_created` (`created_at`);

-- Create a user_activity table to track user actions (for analytics)
CREATE TABLE IF NOT EXISTS `user_activity` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NULL,
  `session_id` VARCHAR(100) NULL,
  `activity_type` VARCHAR(50) NOT NULL,
  `activity_data` JSON NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_activity_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add indexes to user_activity table
ALTER TABLE `user_activity` 
  ADD INDEX `idx_activity_user` (`user_id`),
  ADD INDEX `idx_activity_session` (`session_id`),
  ADD INDEX `idx_activity_type` (`activity_type`),
  ADD INDEX `idx_activity_created` (`created_at`);

-- Create a product_categories table for better category management
CREATE TABLE IF NOT EXISTS `product_categories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `slug` VARCHAR(50) NOT NULL,
  `description` TEXT NULL,
  `image` VARCHAR(255) NULL,
  `parent_id` INT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_category_slug` (`slug`),
  CONSTRAINT `fk_category_parent`
    FOREIGN KEY (`parent_id`)
    REFERENCES `product_categories` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add indexes to product_categories table
ALTER TABLE `product_categories` 
  ADD INDEX `idx_category_parent` (`parent_id`),
  ADD INDEX `idx_category_active` (`is_active`),
  ADD INDEX `idx_category_sort` (`sort_order`);

-- ----------------------------------------------------------------
-- STEP 4: Add caching tables for performance-critical data
-- ----------------------------------------------------------------

-- Create a product_stats table for caching frequently accessed product statistics
CREATE TABLE IF NOT EXISTS `product_stats` (
  `product_id` INT NOT NULL,
  `view_count` INT NOT NULL DEFAULT 0,
  `order_count` INT NOT NULL DEFAULT 0,
  `review_count` INT NOT NULL DEFAULT 0,
  `avg_rating` DECIMAL(3,2) NOT NULL DEFAULT 0.0,
  `last_ordered_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`product_id`),
  CONSTRAINT `fk_stats_product`
    FOREIGN KEY (`product_id`)
    REFERENCES `products` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create a farmer_stats table for caching farmer performance data
CREATE TABLE IF NOT EXISTS `farmer_stats` (
  `farmer_id` INT NOT NULL,
  `product_count` INT NOT NULL DEFAULT 0,
  `total_sales` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `order_count` INT NOT NULL DEFAULT 0,
  `avg_rating` DECIMAL(3,2) NOT NULL DEFAULT 0.0,
  `last_active` TIMESTAMP NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`farmer_id`),
  CONSTRAINT `fk_stats_farmer`
    FOREIGN KEY (`farmer_id`)
    REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------
-- STEP 5: Add geographic data for better location-based searching
-- ----------------------------------------------------------------

-- Create a locations table for standardized location data
CREATE TABLE IF NOT EXISTS `locations` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `parent_id` INT NULL,
  `type` ENUM('country', 'province', 'district', 'city', 'area') NOT NULL,
  `latitude` DECIMAL(10,8) NULL,
  `longitude` DECIMAL(11,8) NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_location_parent`
    FOREIGN KEY (`parent_id`)
    REFERENCES `locations` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add spatial index for location-based searches
ALTER TABLE `locations` 
  ADD INDEX `idx_location_parent` (`parent_id`),
  ADD INDEX `idx_location_type` (`type`),
  ADD INDEX `idx_location_active` (`is_active`);

-- ----------------------------------------------------------------
-- STEP 6: Create procedures for data maintenance
-- ----------------------------------------------------------------

-- Procedure to update product statistics
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS `update_product_stats`(IN product_id_param INT)
BEGIN
  DECLARE view_count_val INT;
  DECLARE order_count_val INT;
  DECLARE review_count_val INT;
  DECLARE avg_rating_val DECIMAL(3,2);
  DECLARE last_ordered_val TIMESTAMP;
  
  -- Get view count (to be implemented with actual tracking)
  SET view_count_val = 0;
  
  -- Get order count
  SELECT COUNT(*), MAX(o.created_at)
  INTO order_count_val, last_ordered_val
  FROM order_items oi
  JOIN orders o ON oi.order_id = o.id
  WHERE oi.product_id = product_id_param;
  
  -- Get review stats
  SELECT COUNT(*), IFNULL(AVG(rating), 0)
  INTO review_count_val, avg_rating_val
  FROM reviews
  WHERE product_id = product_id_param;
  
  -- Update or insert stats
  INSERT INTO product_stats 
    (product_id, view_count, order_count, review_count, avg_rating, last_ordered_at)
  VALUES 
    (product_id_param, view_count_val, order_count_val, review_count_val, avg_rating_val, last_ordered_val)
  ON DUPLICATE KEY UPDATE
    view_count = view_count_val,
    order_count = order_count_val,
    review_count = review_count_val,
    avg_rating = avg_rating_val,
    last_ordered_at = last_ordered_val;
END //
DELIMITER ;

-- Procedure to update farmer statistics
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS `update_farmer_stats`(IN farmer_id_param INT)
BEGIN
  DECLARE product_count_val INT;
  DECLARE total_sales_val DECIMAL(12,2);
  DECLARE order_count_val INT;
  DECLARE avg_rating_val DECIMAL(3,2);
  DECLARE last_active_val TIMESTAMP;
  
  -- Get product count
  SELECT COUNT(*)
  INTO product_count_val
  FROM products
  WHERE farmer_id = farmer_id_param AND is_active = 1;
  
  -- Get sales stats
  SELECT COUNT(oi.id), IFNULL(SUM(oi.price * oi.quantity), 0)
  INTO order_count_val, total_sales_val
  FROM order_items oi
  WHERE oi.farmer_id = farmer_id_param;
  
  -- Get average rating across all products
  SELECT IFNULL(AVG(r.rating), 0)
  INTO avg_rating_val
  FROM reviews r
  JOIN products p ON r.product_id = p.id
  WHERE p.farmer_id = farmer_id_param;
  
  -- Get last active time (using most recent product update or order)
  SELECT GREATEST(
    IFNULL((SELECT MAX(updated_at) FROM products WHERE farmer_id = farmer_id_param), '1970-01-01'),
    IFNULL((SELECT MAX(o.created_at) FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE oi.farmer_id = farmer_id_param), '1970-01-01')
  ) INTO last_active_val;
  
  -- Update or insert stats
  INSERT INTO farmer_stats 
    (farmer_id, product_count, total_sales, order_count, avg_rating, last_active)
  VALUES 
    (farmer_id_param, product_count_val, total_sales_val, order_count_val, avg_rating_val, last_active_val)
  ON DUPLICATE KEY UPDATE
    product_count = product_count_val,
    total_sales = total_sales_val,
    order_count = order_count_val,
    avg_rating = avg_rating_val,
    last_active = last_active_val;
END //
DELIMITER ;

-- ----------------------------------------------------------------
-- STEP 7: Set up data archiving for historical data
-- ----------------------------------------------------------------

-- Create archive tables for historical data
CREATE TABLE IF NOT EXISTS `orders_archive` LIKE `orders`;
CREATE TABLE IF NOT EXISTS `order_items_archive` LIKE `order_items`;
CREATE TABLE IF NOT EXISTS `messages_archive` LIKE `messages`;

-- Procedure to archive old orders
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS `archive_old_orders`(IN months_old INT)
BEGIN
  DECLARE cutoff_date TIMESTAMP;
  SET cutoff_date = DATE_SUB(NOW(), INTERVAL months_old MONTH);
  
  -- Archive old orders
  INSERT INTO orders_archive
  SELECT * FROM orders
  WHERE created_at < cutoff_date
  AND status IN ('delivered', 'cancelled');
  
  -- Archive old order items
  INSERT INTO order_items_archive
  SELECT oi.* FROM order_items oi
  JOIN orders_archive oa ON oi.order_id = oa.id;
  
  -- Delete archived data from main tables
  DELETE oi FROM order_items oi
  JOIN orders_archive oa ON oi.order_id = oa.id;
  
  DELETE o FROM orders o
  WHERE o.id IN (SELECT id FROM orders_archive);
END //
DELIMITER ;

-- Procedure to archive old messages
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS `archive_old_messages`(IN months_old INT)
BEGIN
  DECLARE cutoff_date TIMESTAMP;
  SET cutoff_date = DATE_SUB(NOW(), INTERVAL months_old MONTH);
  
  -- Archive old messages
  INSERT INTO messages_archive
  SELECT * FROM messages
  WHERE created_at < cutoff_date;
  
  -- Delete archived messages from main table
  DELETE FROM messages
  WHERE id IN (SELECT id FROM messages_archive);
END //
DELIMITER ;

-- ----------------------------------------------------------------
-- STEP 8: Add events for automatic maintenance
-- ----------------------------------------------------------------

-- Enable event scheduler
SET GLOBAL event_scheduler = ON;

-- Create event to update product stats daily
DELIMITER //
CREATE EVENT IF NOT EXISTS `event_update_product_stats`
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
BEGIN
  DECLARE done INT DEFAULT FALSE;
  DECLARE prod_id INT;
  DECLARE cur CURSOR FOR SELECT id FROM products;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
  
  OPEN cur;
  
  read_loop: LOOP
    FETCH cur INTO prod_id;
    IF done THEN
      LEAVE read_loop;
    END IF;
    
    CALL update_product_stats(prod_id);
  END LOOP;
  
  CLOSE cur;
END //
DELIMITER ;

-- Create event to update farmer stats daily
DELIMITER //
CREATE EVENT IF NOT EXISTS `event_update_farmer_stats`
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
BEGIN
  DECLARE done INT DEFAULT FALSE;
  DECLARE farm_id INT;
  DECLARE cur CURSOR FOR SELECT id FROM users WHERE user_type = 'farmer';
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
  
  OPEN cur;
  
  read_loop: LOOP
    FETCH cur INTO farm_id;
    IF done THEN
      LEAVE read_loop;
    END IF;
    
    CALL update_farmer_stats(farm_id);
  END LOOP;
  
  CLOSE cur;
END //
DELIMITER ;

-- Create event to archive old data monthly
DELIMITER //
CREATE EVENT IF NOT EXISTS `event_archive_old_data`
ON SCHEDULE EVERY 1 MONTH
STARTS CURRENT_TIMESTAMP
DO
BEGIN
  -- Archive orders older than 12 months
  CALL archive_old_orders(12);
  
  -- Archive messages older than 6 months
  CALL archive_old_messages(6);
END //
DELIMITER ;
