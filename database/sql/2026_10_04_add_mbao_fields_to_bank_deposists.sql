-- Mbao daily-balance bank deposits recorded from the Mbao dashboard.
-- Adds optional columns to the existing `bank_deposists` table (also used by
-- Logistics). Safe on live data: nullable columns only, existing rows untouched.
ALTER TABLE `bank_deposists`
    ADD COLUMN `account_number` VARCHAR(100) NULL DEFAULT NULL,
    ADD COLUMN `store_id` INT(11) NULL DEFAULT NULL,
    ADD COLUMN `source` VARCHAR(30) NULL DEFAULT NULL,
    ADD COLUMN `balance_date` DATE NULL DEFAULT NULL,
    ADD KEY `source_store_date` (`source`, `store_id`, `balance_date`);
