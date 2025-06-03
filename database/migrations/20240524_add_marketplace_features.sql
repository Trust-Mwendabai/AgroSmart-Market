-- Migration: Add Marketplace Features
-- Adds tables and columns to support the enhanced marketplace functionality

-- Enable foreign key checks temporarily to avoid issues
SET FOREIGN_KEY_CHECKS=0;

-- Add columns to products table
ALTER TABLE `products` 
ADD COLUMN `is_featured` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_active`,
ADD COLUMN `rating` DECIMAL(3,2) DEFAULT 0.00 AFTER `is_featured`,
ADD COLUMN `review_count` INT(11) DEFAULT 0 AFTER `rating`,
ADD COLUMN `sold_count` INT(11) DEFAULT 0 AFTER `review_count`,
ADD COLUMN `view_count` INT(11) DEFAULT 0 AFTER `sold_count`,
ADD COLUMN `weight` DECIMAL(10,2) DEFAULT NULL AFTER `stock`,
ADD COLUMN `unit` VARCHAR(20) DEFAULT 'kg' AFTER `weight`,
ADD COLUMN `min_order` INT(11) DEFAULT 1 COMMENT 'Minimum order quantity' AFTER `unit`,
ADD COLUMN `max_order` INT(11) DEFAULT NULL COMMENT 'Maximum order quantity' AFTER `min_order`,
ADD COLUMN `tags` VARCHAR(255) DEFAULT NULL AFTER `category`,
ADD COLUMN `expiry_date` DATE DEFAULT NULL AFTER `date_added`,
ADD COLUMN `harvest_date` DATE DEFAULT NULL AFTER `expiry_date`,
ADD FULLTEXT INDEX `idx_search` (`name`, `description`, `tags`);

-- Create product_images table for multiple product images
CREATE TABLE IF NOT EXISTS `product_images` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `alt_text` VARCHAR(255) DEFAULT NULL,
  `sort_order` INT(11) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create product_reviews table
CREATE TABLE IF NOT EXISTS `product_reviews` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `rating` TINYINT(1) NOT NULL,
  `title` VARCHAR(255) DEFAULT NULL,
  `comment` TEXT,
  `is_approved` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `fk_review_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_review_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create wishlists table
CREATE TABLE IF NOT EXISTS `wishlists` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_wishlist` (`user_id`, `product_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wishlist_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create product_views table for tracking product views
CREATE TABLE IF NOT EXISTS `product_views` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `user_id` INT(11) DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `viewed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_viewed_at` (`viewed_at`),
  CONSTRAINT `fk_view_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_view_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create product_categories table for better category management
CREATE TABLE IF NOT EXISTS `product_categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `slug` VARCHAR(60) NOT NULL,
  `description` TEXT,
  `parent_id` INT(11) DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `icon` VARCHAR(50) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `sort_order` INT(11) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_slug` (`slug`),
  KEY `idx_parent` (`parent_id`),
  CONSTRAINT `fk_category_parent` FOREIGN KEY (`parent_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default categories
INSERT IGNORE INTO `product_categories` (`name`, `slug`, `description`, `icon`, `is_active`, `sort_order`) VALUES
('Fruits', 'fruits', 'Fresh and seasonal fruits', 'fas fa-apple-alt', 1, 1),
('Vegetables', 'vegetables', 'Fresh and organic vegetables', 'fas fa-carrot', 1, 2),
('Grains', 'grains', 'Various types of grains', 'fas fa-wheat-awn', 1, 3),
('Tubers', 'tubers', 'Fresh tubers and root crops', 'fas fa-potato', 1, 4),
('Herbs & Spices', 'herbs-spices', 'Fresh herbs and spices', 'fas fa-leaf', 1, 5),
('Dairy', 'dairy', 'Fresh dairy products', 'fas fa-cheese', 1, 6);

-- Update products table to reference the new categories
ALTER TABLE `products` 
MODIFY COLUMN `category` INT(11) DEFAULT NULL,
ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL;

-- Create product_attributes table for product variations
CREATE TABLE IF NOT EXISTS `product_attributes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `attribute_name` VARCHAR(100) NOT NULL,
  `attribute_value` VARCHAR(255) NOT NULL,
  `price_adjustment` DECIMAL(10,2) DEFAULT 0.00,
  `stock` INT(11) DEFAULT NULL,
  `sku` VARCHAR(100) DEFAULT NULL,
  `barcode` VARCHAR(100) DEFAULT NULL,
  `weight` DECIMAL(10,2) DEFAULT NULL,
  `is_default` TINYINT(1) DEFAULT 0,
  `sort_order` INT(11) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `fk_attribute_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create product_related table for related products
CREATE TABLE IF NOT EXISTS `product_related` (
  `product_id` INT(11) NOT NULL,
  `related_product_id` INT(11) NOT NULL,
  `sort_order` INT(11) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`product_id`, `related_product_id`),
  KEY `idx_related` (`related_product_id`),
  CONSTRAINT `fk_related_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_related_related` FOREIGN KEY (`related_product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create product_discounts table for special offers
CREATE TABLE IF NOT EXISTS `product_discounts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `discount_type` ENUM('percentage', 'fixed') NOT NULL,
  `discount_value` DECIMAL(10,2) NOT NULL,
  `start_date` DATETIME NOT NULL,
  `end_date` DATETIME NOT NULL,
  `min_quantity` INT(11) DEFAULT 1,
  `max_quantity` INT(11) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_dates` (`start_date`, `end_date`),
  CONSTRAINT `fk_discount_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create indexes for better performance
CREATE INDEX `idx_product_price` ON `products` (`price`);
CREATE INDEX `idx_product_stock` ON `products` (`stock`);
CREATE INDEX `idx_product_organic` ON `products` (`is_organic`);
CREATE INDEX `idx_product_active` ON `products` (`is_active`);
CREATE INDEX `idx_product_featured` ON `products` (`is_featured`);
CREATE INDEX `idx_product_rating` ON `products` (`rating`);
CREATE INDEX `idx_product_sold` ON `products` (`sold_count`);
CREATE INDEX `idx_product_date` ON `products` (`date_added`);

-- Create a trigger to update product rating when a new review is added
DELIMITER //
CREATE TRIGGER `after_review_insert`
AFTER INSERT ON `product_reviews`
FOR EACH ROW
BEGIN
    DECLARE avg_rating DECIMAL(3,2);
    DECLARE review_count INT;
    
    -- Calculate average rating
    SELECT AVG(rating), COUNT(*) 
    INTO avg_rating, review_count
    FROM product_reviews 
    WHERE product_id = NEW.product_id AND is_approved = 1;
    
    -- Update product with new rating and review count
    UPDATE products 
    SET 
        rating = COALESCE(avg_rating, 0),
        review_count = review_count
    WHERE id = NEW.product_id;
END//

-- Create a trigger to update product rating when a review is updated
CREATE TRIGGER `after_review_update`
AFTER UPDATE ON `product_reviews`
FOR EACH ROW
BEGIN
    DECLARE avg_rating DECIMAL(3,2);
    DECLARE review_count INT;
    
    -- Only proceed if rating or approval status changed
    IF OLD.rating != NEW.rating OR OLD.is_approved != NEW.is_approved THEN
        -- Calculate average rating
        SELECT AVG(rating), COUNT(*) 
        INTO avg_rating, review_count
        FROM product_reviews 
        WHERE product_id = NEW.product_id AND is_approved = 1;
        
        -- Update product with new rating and review count
        UPDATE products 
        SET 
            rating = COALESCE(avg_rating, 0),
            review_count = review_count
        WHERE id = NEW.product_id;
    END IF;
END//

-- Create a trigger to update product rating when a review is deleted
CREATE TRIGGER `after_review_delete`
AFTER DELETE ON `product_reviews`
FOR EACH ROW
BEGIN
    DECLARE avg_rating DECIMAL(3,2);
    DECLARE review_count INT;
    
    -- Calculate average rating
    SELECT AVG(rating), COUNT(*) 
    INTO avg_rating, review_count
    FROM product_reviews 
    WHERE product_id = OLD.product_id AND is_approved = 1;
    
    -- Update product with new rating and review count
    UPDATE products 
    SET 
        rating = COALESCE(avg_rating, 0),
        review_count = review_count
    WHERE id = OLD.product_id;
END//

-- Create a trigger to update view count
CREATE TRIGGER `after_product_view`
AFTER INSERT ON `product_views`
FOR EACH ROW
BEGIN
    UPDATE products 
    SET view_count = view_count + 1 
    WHERE id = NEW.product_id;
END//

DELIMITER ;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS=1;
