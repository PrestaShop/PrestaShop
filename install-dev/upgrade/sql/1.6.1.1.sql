SET NAMES 'utf8';

ALTER TABLE `PREFIX_customer_message` CHANGE `message` `message` MEDIUMTEXT NOT NULL;

ALTER TABLE  `PREFIX_order_detail` ADD  `original_wholesale_price` DECIMAL( 20, 6 ) NOT NULL DEFAULT  '0.000000';
