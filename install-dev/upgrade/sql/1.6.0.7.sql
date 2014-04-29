SET NAMES 'utf8';

ALTER TABLE `PREFIX_customer_message` CHANGE `ip_address` `ip_address` VARCHAR( 16 ) NULL DEFAULT NULL;

UPDATE `PREFIX_theme` SET product_per_page = '12' WHERE `product_per_page` = 0;

ALTER TABLE  `PREFIX_order_slip_detail` CHANGE `amount_tax_excl` `amount_tax_excl` DECIMAL(20, 6) NULL DEFAULT NULL;

ALTER TABLE  `PREFIX_order_slip_detail` CHANGE  `amount_tax_incl`  `amount_tax_incl` DECIMAL( 20, 6 ) NULL DEFAULT NULL;

ALTER TABLE `PREFIX_order_slip` ADD `total_products_tax_excl` DECIMAL(20, 6) NULL AFTER `id_order`, ADD `total_products_tax_incl` DECIMAL(20, 6) NULL AFTER `total_products_tax_excl`,ADD `total_shipping_tax_excl` DECIMAL(20, 6) NULL AFTER `total_products_tax_incl`, ADD `total_shipping_tax_incl` DECIMAL(20, 6) NULL AFTER `total_shipping_tax_excl`;
ALTER TABLE `PREFIX_order_slip_detail` ADD `unit_price_tax_excl` DECIMAL(20, 6) NULL AFTER `product_quantity`, ADD `unit_price_tax_incl` DECIMAL(20, 6) NULL AFTER  `unit_price_tax_excl`, ADD `total_price_tax_excl` DECIMAL(20, 6) NULL AFTER `unit_price_tax_incl`, ADD `total_price_tax_incl` DECIMAL(20, 6) NULL AFTER `total_price_tax_excl`;

CREATE TABLE IF NOT EXISTS `PREFIX_order_slip_detail_tax` (
  `id_order_slip_detail` int(11) NOT NULL,
  `id_tax` int(11) NOT NULL,
  `unit_amount` decimal(16,6) NOT NULL DEFAULT '0.000000',
  `total_amount` decimal(16,6) NOT NULL DEFAULT '0.000000',
  KEY (`id_order_slip_detail`),
  KEY `id_tax` (`id_tax`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

ALTER TABLE  `PREFIX_tax_rules_group` ADD `deleted` TINYINT(1) UNSIGNED NOT NULL, ADD `date_add` DATETIME NOT NULL, ADD `date_upd` DATETIME NOT NULL;
ALTER TABLE  `PREFIX_order_detail` ADD `id_tax_rules_group` INT(11) UNSIGNED NOT NULL AFTER  `product_weight`, ADD INDEX `id_tax_rules_group` (`id_tax_rules_group`)

