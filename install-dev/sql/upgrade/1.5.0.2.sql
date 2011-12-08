SET NAMES 'utf8';

INSERT INTO `PREFIX_access` (`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`) VALUES ('1', '108', '1', '1', '1', '1');
INSERT INTO `PREFIX_access` (`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`) VALUES ('2', '108', '1', '1', '1', '1');
INSERT INTO `PREFIX_access` (`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`) VALUES ('3', '108', '1', '1', '1', '1');
INSERT INTO `PREFIX_access` (`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`) VALUES ('4', '108', '0', '0', '0', '0');
INSERT INTO `PREFIX_access` (`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`) VALUES ('5', '108', '0', '0', '0', '0');

CREATE TABLE IF NOT EXISTS `PREFIX_specific_price_rule` (
	`id_specific_price_rule` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`id_shop` int(11) unsigned NOT NULL DEFAULT '1',
	`id_currency` int(10) unsigned NOT NULL,
	`id_country` int(10) unsigned NOT NULL,
	`id_group` int(10) unsigned NOT NULL,
	`from_quantity` mediumint(8) unsigned NOT NULL,
	`price` DECIMAL(20,6),
	`reduction` decimal(20,6) NOT NULL,
	`reduction_type` enum('amount','percentage') NOT NULL,
	`from` datetime NOT NULL,
	`to` datetime NOT NULL,
	PRIMARY KEY (`id_specific_price_rule`),
	KEY `id_product` (`id_shop`,`id_currency`,`id_country`,`id_group`,`from_quantity`,`from`,`to`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_specific_price_rule_condition_group` (
	`id_specific_price_rule_condition_group` INT(11) UNSIGNED NOT NULL,
	`id_specific_price_rule` INT(11) UNSIGNED NOT NULL,
	PRIMARY KEY ( `id_specific_price_rule_condition_group`, `id_specific_price_rule` )
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_specific_price_rule_condition` (
	`id_specific_price_rule_condition` INT(11) UNSIGNED NOT NULL,
	`id_specific_price_rule_condition_group` INT(11) UNSIGNED NOT NULL,
	`type` VARCHAR(255) NOT NULL,
	`value` VARCHAR(255) NOT NULL,
PRIMARY KEY (`id_specific_price_rule_condition`),
INDEX (`id_specific_price_rule_condition_group`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_specific_price` ADD `id_specific_price_rule` INT(11) UNSIGNED NOT NULL AFTER `id_specific_price`, ADD INDEX (`id_specific_price_rule`);
/* PHP:add_new_tab(AdminSpecificPriceRule, es:Catalog price rules|it:Catalog price rules|en:Catalog price rules|de:Catalog price rules|fr:Règles de prix catalogue,  1); */;

ALTER TABLE `PREFIX_orders` DROP COLUMN `id_warehouse`;
ALTER TABLE `PREFIX_order_detail` ADD COLUMN `id_warehouse` int(10) unsigned DEFAULT 0 AFTER `id_order_invoice`;
ALTER TABLE `PREFIX_suplier` ADD COLUMN `id_address` int(10) unsigned NOT NULL AFTER `id_supplier`;
ALTER TABLE `PREFIX_address` ADD COLUMN `id_warehouse` int(10) unsigned NOT NULL DEFAULT 0 AFTER `id_supplier`;
