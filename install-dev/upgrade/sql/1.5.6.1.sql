ALTER TABLE `PREFIX_customer_message` CHANGE `ip_address` `ip_address` VARCHAR( 15 ) NULL DEFAULT NULL;

UPDATE `PREFIX_orders` SET conversion_rate = 1 WHERE conversion_rate = 0;
