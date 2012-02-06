SET NAMES 'utf8';

ALTER TABLE `PREFIX_cart_rule` ADD `shop_restriction` tinyint(1) unsigned NOT NULL default 0 AFTER `product_restriction`;

CREATE TABLE `PREFIX_cart_rule_shop` (
	`id_cart_rule` int(10) unsigned NOT NULL,
	`id_shop` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`id_cart_rule`, `id_shop`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
