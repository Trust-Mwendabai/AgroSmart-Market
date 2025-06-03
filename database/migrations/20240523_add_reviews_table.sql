-- Migration: Add reviews table
-- This migration adds the reviews table to support product ratings and comments

CREATE TABLE IF NOT EXISTS `reviews` (
  `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT(11) NOT NULL,
  `buyer_id` INT(11) NOT NULL,
  `rating` INT(1) NOT NULL CHECK (rating BETWEEN 1 AND 5),
  `comment` TEXT,
  `date_posted` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`buyer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_review` (`product_id`, `buyer_id`) -- Ensure one review per buyer per product
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create indexes for better performance
CREATE INDEX `idx_reviews_product` ON `reviews` (`product_id`);
CREATE INDEX `idx_reviews_buyer` ON `reviews` (`buyer_id`);
