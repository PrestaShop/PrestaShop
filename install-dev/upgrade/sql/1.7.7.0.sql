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
DELETE FROM `PREFIX_configuration` WHERE name = 'PS_PRICE_DISPLAY_PRECISION';

/* Set optin field value to 0 in employee table */
ALTER TABLE `PREFIX_employee` MODIFY COLUMN `optin` tinyint(1) unsigned DEFAULT NULL;
