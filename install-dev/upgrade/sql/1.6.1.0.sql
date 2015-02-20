SET NAMES 'utf8';

UPDATE `PREFIX_configuration` SET `value` = CONCAT('#', `value`) WHERE `name` LIKE 'PS_%_PREFIX';

UPDATE `PREFIX_configuration_lang` SET `value` = CONCAT('#', `value`) WHERE `id_configuration` IN (SELECT `id_configuration` FROM `PREFIX_configuration` WHERE `name` LIKE 'PS_%_PREFIX');

ALTER TABLE `PREFIX_orders` CHANGE `invoice_number` `invoice_number` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0',
CHANGE `delivery_number` `delivery_number` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0';

/* taxes-patch */

ALTER TABLE `PREFIX_order_invoice`
CHANGE COLUMN `total_discount_tax_excl` `total_discount_tax_excl` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_discount_tax_incl` `total_discount_tax_incl` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_paid_tax_excl` `total_paid_tax_excl` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_paid_tax_incl` `total_paid_tax_incl` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_products` `total_products` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_products_wt` `total_products_wt` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_shipping_tax_excl` `total_shipping_tax_excl` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_shipping_tax_incl` `total_shipping_tax_incl` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_wrapping_tax_excl` `total_wrapping_tax_excl` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `total_wrapping_tax_incl` `total_wrapping_tax_incl` DECIMAL(20,6) NOT NULL DEFAULT '0.00' ;

ALTER TABLE `PREFIX_orders` ADD `round_type` TINYINT(1) NOT NULL DEFAULT '1' AFTER `round_mode`;
