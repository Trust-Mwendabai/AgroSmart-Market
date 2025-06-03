-- Migration: Add is_active column to products table
-- This migration adds the is_active column to the products table if it doesn't exist

-- Check if the column exists before adding it
SET @dbname = DATABASE();
SET @tablename = 'products';
SET @columnname = 'is_active';
SET @preparedStatement = (SELECT IF(
  EXISTS(
    SELECT * FROM INFORMATION_SCHEMA.COLUMNS
    WHERE (TABLE_SCHEMA = @dbname)
    AND (TABLE_NAME = @tablename)
    AND (COLUMN_NAME = @columnname)
  ),
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' TINYINT(1) NOT NULL DEFAULT 1 COMMENT "Flag to indicate if the product is active (1) or inactive (0)";')
));

PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Create index for better performance
CREATE INDEX `idx_products_is_active` ON `products` (`is_active`);
