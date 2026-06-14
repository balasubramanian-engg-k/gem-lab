-- SQL Queries for Updating Existing Invoices with Product Types
-- Run these queries after creating the product_types table and before running the migration

-- Step 1: Insert existing product types from invoices into product_types table
-- (This will be done automatically by the migration, but here's the manual query if needed)
INSERT INTO product_types (name, created_at, updated_at)
SELECT DISTINCT product_type, NOW(), NOW()
FROM invoices
WHERE product_type IS NOT NULL 
  AND product_type != ''
  AND product_type NOT IN (SELECT name FROM product_types);

-- Step 2: Add product_type_id column to invoices table (if not already added by migration)
ALTER TABLE invoices 
ADD COLUMN product_type_id BIGINT UNSIGNED NULL 
AFTER wastage_making_certification_cost;

-- Step 3: Update invoices with product_type_id based on product_type name
UPDATE invoices i
INNER JOIN product_types pt ON i.product_type = pt.name
SET i.product_type_id = pt.id
WHERE i.product_type IS NOT NULL 
  AND i.product_type != '';

-- Step 4: After verifying the data is correct, drop the old product_type column
-- (This will be done by the migration, but here's the manual query)
-- ALTER TABLE invoices DROP COLUMN product_type;

-- Step 5: Add foreign key constraint (if not already added by migration)
ALTER TABLE invoices
ADD CONSTRAINT fk_invoices_product_type
FOREIGN KEY (product_type_id) REFERENCES product_types(id)
ON DELETE SET NULL;

-- Verification Query: Check if all invoices have been mapped correctly
SELECT 
    i.id,
    i.product_type AS old_product_type,
    i.product_type_id,
    pt.name AS new_product_type_name
FROM invoices i
LEFT JOIN product_types pt ON i.product_type_id = pt.id
WHERE i.product_type IS NOT NULL;
