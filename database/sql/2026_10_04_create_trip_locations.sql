-- Driver location captured when a trip, route plan or trip expense is saved,
-- or the trip is submitted. Rows without coordinates record why (denied,
-- unavailable, timeout, unsupported). Safe on live data: new table only.
CREATE TABLE IF NOT EXISTS `trip_locations` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `route_id` INT(11) NOT NULL,
    `event` VARCHAR(30) NOT NULL,
    `ref_id` INT(11) NULL DEFAULT NULL,
    `latitude` DECIMAL(10,7) NULL DEFAULT NULL,
    `longitude` DECIMAL(10,7) NULL DEFAULT NULL,
    `accuracy_m` INT(11) NULL DEFAULT NULL,
    `status` VARCHAR(20) NOT NULL,
    `recorded_by` INT(11) NOT NULL,
    `created_at` DATETIME NULL DEFAULT NULL,
    `updated_at` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `route_id` (`route_id`),
    KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
