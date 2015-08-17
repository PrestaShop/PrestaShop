SET NAMES 'utf8';

ALTER TABLE `PREFIX_product` ADD `show_condition` TINYINT(1) NOT NULL DEFAULT '1' AFTER `available_date`;
ALTER TABLE `PREFIX_product_shop` ADD `show_condition` TINYINT(1) NOT NULL DEFAULT '1' AFTER `available_date`;
