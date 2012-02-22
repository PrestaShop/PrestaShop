SET NAMES 'utf8';

/* PHP:add_unknown_gender(); */;

ALTER TABLE `PREFIX_cart_rule` ADD `gift_product_attribute` int(10) unsigned NOT NULL default 0 AFTER `gift_product`;

UPDATE `PREFIX_product` set is_virtual = 1 WHERE id_product IN (SELECT id_product FROM `PREFIX_product_download` WHERE active = 1);
