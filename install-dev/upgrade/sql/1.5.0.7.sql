SET NAMES 'utf8';

/* PHP:add_unknown_gender(); */;

ALTER TABLE `PREFIX_cart_rule` ADD `gift_product_attribute` int(10) unsigned NOT NULL default 0 AFTER `gift_product`;

UPDATE `PREFIX_product` set is_virtual = 1 WHERE id_product IN (SELECT id_product FROM `PREFIX_product_download` WHERE active = 1);

ALTER TABLE `PREFIX_employee` ADD `bo_width` int(10) unsigned NOT NULL DEFAULT 0 AFTER `bo_theme`;



CREATE TABLE `PREFIX_product_tax_rules_group_shop` (
	`id_product` INT(11) UNSIGNED NOT NULL,
	`id_tax_rules_group` INT(11) UNSIGNED NOT NULL,
	`id_shop` INT( 11 ) UNSIGNED NOT NULL,
	PRIMARY KEY ( `id_product`, `id_tax_rules_group`, `id_shop` )
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_product_tax_rules_group_shop` (`id_product`, `id_tax_rules_group`, `id_shop`)
	(SELECT `id_product`, `id_tax_rules_group`, `id_shop` FROM `PREFIX_product`, `PREFIX_shop`);
ALTER TABLE `PREFIX_product` DROP `id_tax_rules_group`;

CREATE TABLE `PREFIX_carrier_tax_rules_group_shop` (
	`id_carrier` int( 11 ) unsigned NOT NULL,
	`id_tax_rules_group` int(11) unsigned NOT NULL,
	`id_shop` int(11) unsigned NOT NULL,
	PRIMARY KEY (`id_carrier`, `id_tax_rules_group`, `id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_carrier_tax_rules_group_shop` (`id_carrier`, `id_tax_rules_group`, `id_shop`)
	(SELECT `id_carrier`, `id_tax_rules_group`, `id_shop` FROM `PREFIX_carrier`, `PREFIX_shop`);
ALTER TABLE `PREFIX_carrier` DROP `id_tax_rules_group`;

ALTER TABLE `PREFIX_customer` ADD `account_number` VARCHAR(128) NOT NULL AFTER `birthday` 

ALTER TABLE  `PREFIX_product_shop` ADD  `id_category_default` INT( 11 ) UNSIGNED DEFAULT NULL;
