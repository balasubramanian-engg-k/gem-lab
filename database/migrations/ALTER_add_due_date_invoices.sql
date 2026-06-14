-- Add due_date column to invoices table (next to remarks)
-- Run this if you prefer to alter the table manually instead of using Laravel migrations.

ALTER TABLE invoices ADD COLUMN due_date DATE NULL AFTER remarks;
