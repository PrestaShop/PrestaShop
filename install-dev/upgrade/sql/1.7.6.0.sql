SET SESSION sql_mode = '';
SET NAMES 'utf8';

ALTER TABLE `PREFIX_currency` ADD `numeric_iso_code` varchar(3) NOT NULL DEFAULT '0' AFTER `iso_code`;
ALTER TABLE `PREFIX_currency` ADD `precision` int(2) NOT NULL DEFAULT 6 AFTER `numeric_iso_code`;
ALTER TABLE `PREFIX_currency` ADD KEY `currency_iso_code` (`iso_code`);

/* Localized currency information */
CREATE TABLE `PREFIX_currency_lang` (
    `id_currency` int(10) unsigned NOT NULL,
    `id_lang` int(10) unsigned NOT NULL,
    `name` varchar(255) NOT NULL,
    `symbol` varchar(255) NOT NULL,
    PRIMARY KEY (`id_currency`,`id_lang`)
  ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

/* PHP:ps_1760_copy_data_from_currency_to_currency_lang(); */;

ALTER TABLE `PREFIX_admin_filter`
	ADD `filter_id` VARCHAR (255) DEFAULT '' NOT NULL AFTER `shop`,
  DROP INDEX IF EXISTS `admin_filter_search_idx`,
  DROP INDEX IF EXISTS `search_idx`,
	ADD UNIQUE INDEX `admin_filter_search_id_idx` (`employee`, `shop`, `controller`, `action`, `filter_id`)
;

/* Module Manager tab should be the first tab in Modules Tab */
UPDATE `PREFIX_tab` SET `position` = 0 WHERE `class_name` = 'AdminModulesSf' AND `position`= 1;
UPDATE `PREFIX_tab` SET `position` = 1 WHERE `class_name` = 'AdminParentModulesCatalog' AND `position`= 0;

/* Add field MPN to tables */
ALTER TABLE `PREFIX_order_detail` ADD `product_mpn` VARCHAR(32) NULL AFTER `product_upc`;
ALTER TABLE `PREFIX_supply_order_detail` ADD `mpn` VARCHAR(32) NULL AFTER `upc`;
ALTER TABLE `PREFIX_stock` ADD `mpn` VARCHAR(32) NULL AFTER `upc`;
ALTER TABLE `PREFIX_product_attribute` ADD `mpn` VARCHAR(32) NULL AFTER `upc`;
ALTER TABLE `PREFIX_product` ADD `mpn` VARCHAR(32) NULL AFTER `upc`;
