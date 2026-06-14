-- Vault Stock: raw SQL to create table and initial row
-- Run this if you prefer to apply manually instead of using Laravel migration.

-- Create table
CREATE TABLE vault_stock (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    amount DECIMAL(15, 3) NOT NULL DEFAULT 0 COMMENT 'Stock in vault (grams)',
    used_stock_offset DECIMAL(15, 3) NOT NULL DEFAULT 0 COMMENT 'Cumulative add-stock applied to reduce displayed used stock',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Insert single row (we update this row when user sets "Stock in Vault")
INSERT INTO vault_stock (amount, used_stock_offset, created_at, updated_at) VALUES (0, 0, NOW(), NOW());

-- If table already exists without used_stock_offset, run:
-- ALTER TABLE vault_stock ADD COLUMN used_stock_offset DECIMAL(15, 3) NOT NULL DEFAULT 0 COMMENT 'Cumulative add-stock applied to reduce displayed used stock' AFTER amount;
