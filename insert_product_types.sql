-- SQL Query to insert product types into product_types table

INSERT INTO `product_types` (`name`, `created_at`, `updated_at`) VALUES
('Gents Ring', NOW(), NOW()),
('Ladies Ring', NOW(), NOW()),
('Navarathna Ring', NOW(), NOW()),
('Refill Ring', NOW(), NOW()),
('Others', NOW(), NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();
