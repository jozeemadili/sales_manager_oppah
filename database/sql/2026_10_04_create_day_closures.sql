-- Mbao "End Day": one row per closing. While a Closed row's locked_until is in
-- the future, Mbao users are view-only. Safe on live data: new table only.
CREATE TABLE IF NOT EXISTS `day_closures` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `store_id` INT(11) NOT NULL,
    `business_date` DATE NOT NULL,
    `closed_by` INT(11) NOT NULL,
    `closed_at` DATETIME NOT NULL,
    `locked_until` DATETIME NOT NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'Closed',
    `reopened_by` INT(11) NULL DEFAULT NULL,
    `reopened_at` DATETIME NULL DEFAULT NULL,
    `snapshot` LONGTEXT NULL,
    `created_at` DATETIME NULL DEFAULT NULL,
    `updated_at` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `store_lock` (`store_id`, `status`, `locked_until`),
    KEY `business_date` (`business_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
