-- Driver Android app. Safe on live data: adds optional columns and new tables only.

-- 1) App login tokens (Laravel Sanctum). Skipped if it already exists.
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tokenable_type` VARCHAR(255) NOT NULL,
    `tokenable_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `token` VARCHAR(64) NOT NULL,
    `abilities` TEXT NULL,
    `last_used_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
    KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`, `tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2) Location points from the app: the phone's own time (points recorded
--    offline arrive later), speed, and where the point came from.
ALTER TABLE `trip_locations`
    ADD COLUMN `recorded_at` DATETIME NULL DEFAULT NULL,
    ADD COLUMN `speed_kmh` DECIMAL(6,1) NULL DEFAULT NULL,
    ADD COLUMN `source` VARCHAR(10) NULL DEFAULT NULL;

-- 3) Entries sent by the app with a unique id, so a retry after a dropped
--    connection never saves the same trip / route plan / expense twice.
CREATE TABLE IF NOT EXISTS `app_sync_requests` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `uuid` VARCHAR(64) NOT NULL,
    `type` VARCHAR(30) NOT NULL,
    `ref_id` INT(11) NOT NULL,
    `created_at` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_uuid` (`user_id`, `uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 4) Older Sanctum tables lack this column (needed by the installed Sanctum).
--    Run LAST: if phpMyAdmin says "Duplicate column name 'expires_at'",
--    the column already exists and everything above has been applied.
ALTER TABLE `personal_access_tokens` ADD COLUMN `expires_at` TIMESTAMP NULL DEFAULT NULL AFTER `last_used_at`;
