SET SESSION sql_mode = '';
SET NAMES 'utf8';

ALTER TABLE `PREFIX_search_word` MODIFY `word` VARCHAR(30) NOT NULL;
INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_DISPLAY_MANUFACTURERS', '1', NOW(), NOW());

/* PHP:ps_1770_preset_tab_enabled(); */;

/* Add field MPN to tables */
ALTER TABLE `PREFIX_order_detail` ADD `product_mpn` VARCHAR(40) NULL AFTER `product_upc`;
ALTER TABLE `PREFIX_supply_order_detail` ADD `mpn` VARCHAR(40) NULL AFTER `upc`;
ALTER TABLE `PREFIX_stock` ADD `mpn` VARCHAR(40) NULL AFTER `upc`;
ALTER TABLE `PREFIX_product_attribute` ADD `mpn` VARCHAR(40) NULL AFTER `upc`;
ALTER TABLE `PREFIX_product` ADD `mpn` VARCHAR(40) NULL AFTER `upc`;

/* Delete price display precision configuration */
DELETE `PREFIX_configuration` WHERE name = 'PS_PRICE_DISPLAY_PRECISION';

/* Set optin field value to 0 in employee table */
ALTER TABLE `PREFIX_employee` MODIFY COLUMN `optin` tinyint(1) unsigned DEFAULT NULL;

/* Returned modules_perfs table for hook profiling */
CREATE TABLE `PREFIX_modules_perfs` (
  `id_modules_perfs` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `session` int(11) unsigned NOT NULL,
  `module` varchar(62) NOT NULL,
  `method` varchar(126) NOT NULL,
  `time_start` double unsigned NOT NULL,
  `time_end` double unsigned NOT NULL,
  `memory_start` int unsigned NOT NULL,
  `memory_end` int unsigned NOT NULL,
  PRIMARY KEY (`id_modules_perfs`),
  KEY (`session`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
