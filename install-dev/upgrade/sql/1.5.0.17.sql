SET NAMES 'utf8';

ALTER TABLE `PREFIX_order_detail_tax` CHANGE `unit_amount` `unit_amount` DECIMAL(16, 6) NOT NULL DEFAULT '0.000000';
ALTER TABLE `PREFIX_order_detail_tax` CHANGE `total_amount` `unit_amount` DECIMAL(16, 6) NOT NULL DEFAULT '0.000000';

ALTER TABLE `PREFIX_customer_message` ADD `read` tinyint(1) NOT NULL default '0' AFTER `private`;