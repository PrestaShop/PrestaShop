SET NAMES 'utf8';

ALTER TABLE `PREFIX_customer_message` CHANGE `message` `message` MEDIUMTEXT NOT NULL;
UPDATE `PREFIX_tax_rules_group` SET `date_add` = NOW(), `date_upd` = NOW() WHERE `date_add` = '0000-00-00 00:00:00';

ALTER TABLE  `PREFIX_order_detail` ADD  `original_wholesale_price` DECIMAL( 20, 6 ) NOT NULL DEFAULT  '0.000000';

ALTER TABLE `PREFIX_cart_rule` ADD `maximum_amount` decimal(17,2) NOT NULL DEFAULT '0';
ALTER TABLE `PREFIX_cart_rule` ADD `maximum_amount_tax` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `PREFIX_cart_rule` ADD `maximum_amount_currency` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `PREFIX_cart_rule` ADD `maximum_amount_shipping` tinyint(1) NOT NULL DEFAULT '0';