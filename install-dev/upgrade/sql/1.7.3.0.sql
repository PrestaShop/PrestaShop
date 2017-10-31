SET SESSION sql_mode = '';
SET NAMES 'utf8';

UPDATE `PREFIX_tab` SET `position` = 0 WHERE `class_name` = 'AdminZones' AND `position` = '1';
UPDATE `PREFIX_tab` SET `position` = 1 WHERE `class_name` = 'AdminCountries' AND `position` = '0';

/* PHP:ps_1730_add_quick_access_evaluation_catalog(); */;

/* PHP:ps_1730_move_some_aeuc_configuration_to_core(); */;

ALTER TABLE `PREFIX_product` ADD `low_stock_threshold` INT(10) NULL DEFAULT NULL AFTER `minimal_quantity`;

ALTER TABLE `PREFIX_product` ADD `additional_delivery_times` tinyint(1) unsigned NOT NULL DEFAULT '1' AFTER `out_of_stock`;
ALTER TABLE `PREFIX_product_lang` ADD `delivery_in_stock` varchar(255) DEFAULT NULL;
ALTER TABLE `PREFIX_product_lang` ADD `delivery_out_stock` varchar(255) DEFAULT NULL;

ALTER TABLE `PREFIX_product_shop` ADD `low_stock_threshold` INT(10) NULL DEFAULT NULL AFTER `minimal_quantity`;

ALTER TABLE `PREFIX_product_attribute` ADD `low_stock_threshold` INT(10) NULL DEFAULT NULL AFTER `minimal_quantity`;
ALTER TABLE `PREFIX_product_attribute_shop` ADD `low_stock_threshold` INT(10) NULL DEFAULT NULL AFTER `minimal_quantity`;

ALTER TABLE `PREFIX_product` ADD `low_stock_alert` TINYINT(1) NOT NULL DEFAULT 0 AFTER `low_stock_threshold`;
ALTER TABLE `PREFIX_product_shop` ADD `low_stock_alert` TINYINT(1) NOT NULL DEFAULT 0 AFTER `low_stock_threshold`;

ALTER TABLE `PREFIX_product_attribute` ADD `low_stock_alert` TINYINT(1) NOT NULL DEFAULT 0 AFTER `low_stock_threshold`;
ALTER TABLE `PREFIX_product_attribute_shop` ADD `low_stock_alert` TINYINT(1) NOT NULL DEFAULT 0 AFTER `low_stock_threshold`;
ALTER TABLE `PREFIX_product_attribute_shop` ADD `low_stock_threshold` INT(10) NULL DEFAULT NULL AFTER `minimal_quantity`;

CREATE TABLE IF NOT EXISTS `PREFIX_store_lang` (
  `id_store` int(11) unsigned NOT NULL,
  `id_lang` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `address1` varchar(255) NOT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `hours` text,
  `note` text,
  PRIMARY KEY (`id_store`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

/* PHP:ps_1730_migrate_data_from_store_to_store_lang_and_clean_store(); */;

ALTER TABLE `PREFIX_store` DROP `name`, DROP `address1`, DROP `address2`, DROP `hours`, DROP `note`;

ALTER TABLE `PREFIX_feature_product` DROP PRIMARY KEY, ADD PRIMARY KEY (`id_feature`, `id_product`, `id_feature_value`);
