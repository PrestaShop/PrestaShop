SET NAMES 'utf8';

ALTER TABLE `PREFIX_address` CHANGE  `outstanding_allow_amount` `outstanding_allow_amount` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000';

/* PHP:blocknewsletter1530(); */;

/* PHP:block_category_1521(); */;

UPDATE `PREFIX_order_state` SET `delivery` = 0 WHERE `id_order_state` = 3 ;