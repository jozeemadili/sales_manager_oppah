-- Mbao Daily Expenses: day-to-day running costs that are NOT tied to an
-- inventory (those stay in `expenses_records`).
-- Safe to run on live data: creates two new tables, touches nothing else.

CREATE TABLE IF NOT EXISTS `daily_expense_types` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(200) NOT NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'Active',
    `company_id` INT(11) NOT NULL,
    `created_by` INT(11) NOT NULL,
    `created_at` DATETIME NULL DEFAULT NULL,
    `updated_at` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `daily_expenses` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `expense_type_id` INT(11) NOT NULL,
    `amount` DOUBLE NOT NULL,
    `expense_date` DATE NOT NULL,
    `store_id` INT(11) NOT NULL,
    `description` VARCHAR(500) NULL DEFAULT NULL,
    `company_id` INT(11) NOT NULL,
    `recorded_by` INT(11) NOT NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'Active',
    `created_at` DATETIME NULL DEFAULT NULL,
    `updated_at` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `expense_date` (`expense_date`),
    KEY `store_id` (`store_id`),
    KEY `expense_type_id` (`expense_type_id`),
    KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
