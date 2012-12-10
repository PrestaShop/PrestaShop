SET NAMES 'utf8';

ALTER TABLE `PREFIX_address` CHANGE  `outstanding_allow_amount` `outstanding_allow_amount` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000';

/* PHP:blocknewsletter1530(); */;

/* PHP:block_category_1521(); */;

UPDATE `PREFIX_order_state` SET `delivery` = 0 WHERE `id_order_state` = 3;

ALTER TABLE  `PREFIX_product_shop` ADD `id_product_redirected` int(10) unsigned NOT NULL default '0' AFTER `active` , ADD `available_for_order` tinyint(1) NOT NULL default '1' AFTER `id_product_redirected`;

ALTER TABLE  `PREFIX_product` ADD `id_product_redirected` int(10) unsigned NOT NULL default '0' AFTER `active` , ADD `available_for_order` tinyint(1) NOT NULL default '1' AFTER `id_product_redirected`;

ALTER TABLE  `PREFIX_stock_available` DROP INDEX  `id_product_2` ,
ADD UNIQUE  `id_product_2` (  `id_product` ,  `id_product_attribute` ,  `id_shop` ,  `id_shop_group` );