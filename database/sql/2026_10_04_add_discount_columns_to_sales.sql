-- Mbao Quick / Cash Sale: discount support on the `sales` table.
-- Mirrors the columns already present on `sales_tuli` (Hardware).
-- Safe to run on live data: adds nullable columns only, existing rows untouched.
ALTER TABLE `sales`
    ADD COLUMN `original_price` DOUBLE NULL DEFAULT NULL,
    ADD COLUMN `discount_percent` DOUBLE NULL DEFAULT NULL,
    ADD COLUMN `discount_amount` DOUBLE NULL DEFAULT NULL;
