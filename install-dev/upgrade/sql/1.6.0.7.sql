SET NAMES 'utf8';

ALTER TABLE `PREFIX_customer_message` CHANGE `ip_address` `ip_address` VARCHAR( 16 ) NULL DEFAULT NULL;

UPDATE `PREFIX_theme` SET product_per_page = '12' WHERE `product_per_page` = 0;