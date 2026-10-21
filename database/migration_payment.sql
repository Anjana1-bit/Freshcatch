-- Select the FreshCatch database in phpMyAdmin, then run this once.
-- It preserves every existing record.
ALTER TABLE orders
  ADD COLUMN payment_method ENUM('UPI_DEMO','COD') NOT NULL DEFAULT 'COD' AFTER total,
  ADD COLUMN payment_status ENUM('pending','paid','failed') NOT NULL DEFAULT 'pending' AFTER payment_method;
