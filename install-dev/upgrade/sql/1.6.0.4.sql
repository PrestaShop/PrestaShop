SET NAMES 'utf8';

INSERT IGNORE INTO `PREFIX_meta` (`id_meta`, `page`, `configurable`) VALUES (NULL, 'index', '0'), (NULL, 'product', '0'), (NULL, 'category', '0'), (NULL, 'cms', '0');

ALTER TABLE `PREFIX_employee` ADD `optin` tinyint(1) unsigned NOT NULL default '1' AFTER `active`;

ALTER IGNORE TABLE `PREFIX_meta` ADD UNIQUE (`page`);

UPDATE `PREFIX_orders` SET module = 'free_order' WHERE total_paid = 0 AND module LIKE '';

UPDATE `PREFIX_tab` SET `position` = '1' WHERE `id_tab` =48;
UPDATE `PREFIX_tab` SET `position` = '0' WHERE `id_tab` =49;

ALTER TABLE `PREFIX_theme` CHANGE `product_per_page` `product_per_page` INT( 10 ) UNSIGNED NOT NULL DEFAULT '1';

ALTER TABLE  `PREFIX_customer_message` ADD  `date_upd` DATETIME NOT NULL AFTER  `date_add`;

ALTER TABLE  `PREFIX_employee` ADD  `preselect_date_range` VARCHAR( 32 ) NULL DEFAULT NULL AFTER  `stats_compare_option`;

/* PHP:ps1604_update_employee_date(); */;