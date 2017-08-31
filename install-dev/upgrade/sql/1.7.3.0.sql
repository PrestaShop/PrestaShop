SET SESSION sql_mode = '';
SET NAMES 'utf8';

UPDATE `PREFIX_tab` SET `position` = 0 WHERE `class_name` = 'AdminZones' AND `position` = '1';
UPDATE `PREFIX_tab` SET `position` = 1 WHERE `class_name` = 'AdminCountries' AND `position` = '0';

/* PHP:ps_1730_move_some_aeuc_configuration_to_core(); */;

ALTER TABLE `PREFIX_product` ADD `low_stock` INT(10) NULL DEFAULT NULL AFTER `minimal_quantity`;
ALTER TABLE `PREFIX_product_shop` ADD `low_stock` INT(10) NULL DEFAULT NULL AFTER `minimal_quantity`;

ALTER TABLE `PREFIX_product_attribute` ADD `low_stock` INT(10) NULL DEFAULT NULL AFTER `minimal_quantity`;
ALTER TABLE `PREFIX_product_attribute_shop` ADD `low_stock` INT(10) NULL DEFAULT NULL AFTER `minimal_quantity`;
