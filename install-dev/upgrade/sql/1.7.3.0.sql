SET SESSION sql_mode = '';
SET NAMES 'utf8';

UPDATE `PREFIX_tab` SET `position` = 0 WHERE `class_name` = 'AdminZones' AND `position` = '1';
UPDATE `PREFIX_tab` SET `position` = 1 WHERE `class_name` = 'AdminCountries' AND `position` = '0';

/* PHP:ps_1730_add_quick_access_evaluation_catalog(); */;

/* PHP:ps_1730_move_some_aeuc_configuration_to_core(); */;

ALTER TABLE `PREFIX_product` ADD `low_stock_threshold` INT(10) NULL DEFAULT NULL AFTER `minimal_quantity`;

ALTER TABLE `PREFIX_product` ADD `additional_delivery_times` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `out_of_stock`;
ALTER TABLE `PREFIX_product` ADD `delivery_in_stock` varchar(255) DEFAULT NULL AFTER `delivery_times`;
ALTER TABLE `PREFIX_product` ADD `delivery_out_stock` varchar(255) DEFAULT NULL AFTER `delivery_in_stock`;

ALTER TABLE `PREFIX_product_shop` ADD `low_stock_threshold` INT(10) NULL DEFAULT NULL AFTER `minimal_quantity`;

ALTER TABLE `PREFIX_product_attribute` ADD `low_stock_threshold` INT(10) NULL DEFAULT NULL AFTER `minimal_quantity`;
ALTER TABLE `PREFIX_product_attribute_shop` ADD `low_stock_threshold` INT(10) NULL DEFAULT NULL AFTER `minimal_quantity`;