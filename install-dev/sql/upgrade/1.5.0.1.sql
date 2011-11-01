SET NAMES 'utf8';

CREATE TABLE IF NOT EXISTS `PREFIX_module_access` (
  `id_profile` int(10) unsigned NOT NULL,
  `id_module` int(10) unsigned NOT NULL,
  `view` tinyint(1) NOT NULL,
  `configure` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_profile`,`id_module`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_module_access` (`id_profile`, `id_module`, `configure`, `view`) (
	SELECT id_profile, id_module, 0, 1
	FROM PREFIX_access a, PREFIX_module m
	WHERE id_tab = (SELECT `id_tab` FROM PREFIX_tab WHERE class_name = 'AdminModules' LIMIT 1)
	AND a.`view` = 0
);

INSERT INTO `PREFIX_module_access` (`id_profile`, `id_module`, `configure`, `view`) (
	SELECT id_profile, id_module, 1, 1
	FROM PREFIX_access a, PREFIX_module m
	WHERE id_tab = (SELECT `id_tab` FROM PREFIX_tab WHERE class_name = 'AdminModules' LIMIT 1)
	AND a.`view` = 1
);

UPDATE `PREFIX_tab` SET `class_name` = 'AdminThemes' WHERE `class_name` = 'AdminAppearance';

INSERT INTO `PREFIX_hook` (
`name` ,
`title` ,
`description` ,
`position` ,
`live_edit`
)
VALUES ('taxmanager', 'taxmanager', NULL , '1', '0');

ALTER TABLE `PREFIX_tax_rule`
	ADD `zipcode_from` INT NOT NULL AFTER `id_state` ,
	ADD `zipcode_to` INT NOT NULL AFTER `zipcode_from` ,
	ADD `behavior` INT NOT NULL AFTER `zipcode_to`,
	ADD `description` VARCHAR( 100 ) NOT NULL AFTER `id_tax`;

ALTER TABLE `PREFIX_tax_rule` DROP INDEX tax_rule;

INSERT INTO `PREFIX_tax_rule` (`id_tax_rules_group`, `id_country`, `id_state`, `id_tax`, `behavior`, `zipcode_from`, `zipcode_to`)
	SELECT r.`id_tax_rules_group`, r.`id_country`, r.`id_state`, r.`id_tax`, 0, z.`from_zip_code`, z.`to_zip_code`
	FROM `PREFIX_tax_rule` r INNER JOIN `PREFIX_county_zip_code` z ON (z.`id_county` = r.`id_county`);

UPDATE `PREFIX_tax_rule` SET `behavior` = GREATEST(`state_behavior`, `county_behavior`);

DELETE FROM `PREFIX_tax_rule`
WHERE `id_county` != 0
AND `zipcode_from` = 0;

ALTER TABLE `PREFIX_tax_rule`
  DROP `id_county`,
  DROP `state_behavior`,
  DROP `county_behavior`;

/* PHP:remove_tab(AdminCounty); */;
DROP TABLE `PREFIX_county_zip_code`;
DROP TABLE `PREFIX_county`;

ALTER TABLE `PREFIX_employee`
	ADD `id_last_order` tinyint(1) unsigned NOT NULL default '0',
	ADD `id_last_message` tinyint(1) unsigned NOT NULL default '0',
	ADD `id_last_customer` tinyint(1) unsigned NOT NULL default '0';

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
('PS_SHOW_NEW_ORDERS', '1', NOW(), NOW()),
('PS_SHOW_NEW_CUSTOMERS', '1', NOW(), NOW()),
('PS_SHOW_NEW_MESSAGES', '1', NOW(), NOW()),
('PS_FEATURE_FEATURE_ACTIVE', '1', NOW(), NOW()),
('PS_COMBINATION_FEATURE_ACTIVE', '1', NOW(), NOW()),
('PS_ADMINREFRESH_NOTIFICATION', '1', NOW(), NOW());

/* PHP:update_feature_detachable_cache(); */;

ALTER TABLE `PREFIX_product` ADD `available_date` DATE NOT NULL AFTER `available_for_order`;

ALTER TABLE `PREFIX_product_attribute` ADD `available_date` DATE NOT NULL;

/* Index was only used by deprecated function Image::positionImage() */
ALTER TABLE `PREFIX_image` DROP INDEX `product_position`;

CREATE TABLE IF NOT EXISTS `PREFIX_gender` (
  `id_gender` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_gender`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_gender_lang` (
  `id_gender` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id_gender`,`id_lang`),
  KEY `id_gender` (`id_gender`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_gender` (`id_gender`, `type`) VALUES
(1, 0),
(2, 1),
(3, 1);

INSERT INTO `PREFIX_gender_lang` (`id_gender`, `id_lang`, `name`) VALUES
(1, 1, 'Mr.'),
(1, 2, 'M.'),
(1, 3, 'Sr.'),
(1, 4, 'Herr'),
(1, 5, 'Sig.'),
(2, 1, 'Ms.'),
(2, 2, 'Mme'),
(2, 3, 'Sra.'),
(2, 4, 'Frau'),
(2, 5, 'Sig.ra'),
(3, 1, 'Miss'),
(3, 2, 'Melle'),
(3, 3, 'Miss'),
(3, 4, 'Miss'),
(3, 5, 'Miss');

DELETE FROM `PREFIX_configuration` WHERE `name` = 'PS_FORCE_SMARTY_2';

CREATE TABLE IF NOT EXISTS `PREFIX_order_detail_tax` (
`id_order_detail` INT NOT NULL ,
`id_tax` INT NOT NULL
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_tax` ADD `deleted` INT NOT NULL AFTER `active`;

/* PHP:update_order_detail_taxes(); */;

ALTER TABLE `PREFIX_order_detail`
  DROP `tax_name`,
  DROP `tax_rate`;

CREATE TABLE `PREFIX_customer_message_sync_imap` (
  `md5_header` varbinary(32) NOT NULL,
  KEY `md5_header_index` (`md5_header`(4))
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

ALTER TABLE  `PREFIX_customer_message` ADD  `private` TINYINT NOT NULL DEFAULT  '0' AFTER  `user_agent`;

/* PHP:add_new_tab(AdminGenders, fr:Genres|es:Genders|en:Genders|de:Genders|it:Genders, 2); */;

ALTER TABLE `PREFIX_attribute` ADD `position` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

/* PHP:add_attribute_position(); */;

ALTER TABLE `PREFIX_product_download` CHANGE `date_deposit` `date_add` DATETIME NOT NULL ;
ALTER TABLE `PREFIX_product_download` CHANGE `physically_filename` `filename` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `PREFIX_product_download` ADD `id_product_attribute` INT( 10 ) UNSIGNED NOT NULL AFTER `id_product` , ADD INDEX ( `id_product_attribute` );
ALTER TABLE `PREFIX_product_download` ADD `is_shareable` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `active`;

ALTER TABLE `PREFIX_attribute_group` ADD `position` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE  `PREFIX_attribute_group` ADD  `group_type` ENUM('select', 'radio', 'color') NOT NULL DEFAULT  'select';
UPDATE `PREFIX_attribute_group` SET  `group_type`='color' WHERE `is_color_group` = 1;
ALTER TABLE `PREFIX_product` DROP `id_color_default`;

/* PHP:add_group_attribute_position(); */;

ALTER TABLE `PREFIX_feature` ADD `position` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

/* PHP:add_feature_position(); */;

CREATE TABLE IF NOT EXISTS `PREFIX_request_sql` (
  `id_request_sql` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `sql` text NOT NULL,
  PRIMARY KEY (`id_request_sql`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

/* PHP:add_new_tab(AdminRequestSql, fr:SQL Manager|es:SQL Manager|en:SQL Manager|de:Wunsh|it:SQL Manager, 9); */;


ALTER TABLE `PREFIX_carrier` ADD COLUMN `id_reference` int(10)  NOT NULL AFTER `id_carrier`;
UPDATE `PREFIX_carrier` SET id_reference = id_carrier;

ALTER TABLE `PREFIX_product` ADD `is_virtual` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `cache_has_attachments`;

/* PHP:add_new_tab(AdminProducts, fr:Products|es:Products|en:Products|de:Products|it:Products, 1); */;
/* PHP:add_new_tab(AdminCategories, fr:Categories|es:Categories|en:Categories|de:Categories|it:Categories, 1); */;
/* PHP:add_new_tab(AdminStocks, fr:Stocks|es:Stocks|en:Stocks|de:Stocks|it:Stocks, 1); */;
/* PHP:add_default_restrictions_modules_groups(); */;



CREATE TABLE IF NOT EXISTS `PREFIX_employee_shop` (
`id_employee` INT( 11 ) UNSIGNED NOT NULL ,
`id_shop` INT( 11 ) UNSIGNED NOT NULL ,
PRIMARY KEY ( `id_employee` , `id_shop` )
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_employee_shop` (`id_employee`, `id_shop`) (SELECT `id_employee`, 1 FROM `PREFIX_employee`);

UPDATE `PREFIX_access` SET `view` = 0, `add` = 0, `edit` = 0, `delete` = 0 WHERE `id_tab` = 88 AND `id_profile` != 1;

INSERT INTO `PREFIX_profile` (`id_profile`) VALUES (5);

UPDATE `PREFIX_profile_lang` SET `id_profile` = 5 WHERE `id_profile` = 4;
UPDATE `PREFIX_profile_lang` SET `id_profile` = 4 WHERE `id_profile` = 3;
UPDATE `PREFIX_profile_lang` SET `id_profile` = 3 WHERE `id_profile` = 2;
UPDATE `PREFIX_profile_lang` SET `id_profile` = 2 WHERE `id_profile` = 1;

INSERT INTO `PREFIX_profile_lang` (`id_profile`, `id_lang`, `name`) VALUES (1, 1, 'SuperAdmin'),(1, 2, 'SuperAdmin'),(1, 3, 'SuperAdmin'),(1, 4, 'SuperAdmin'),(1, 5, 'SuperAdmin');

UPDATE `PREFIX_access` SET `id_profile` = 5 WHERE `id_profile` = 4;
UPDATE `PREFIX_access` SET `id_profile` = 4 WHERE `id_profile` = 3;
UPDATE `PREFIX_access` SET `id_profile` = 3 WHERE `id_profile` = 2;
UPDATE `PREFIX_access` SET `id_profile` = 2 WHERE `id_profile` = 1;

UPDATE `PREFIX_module_access` SET `id_profile` = 5 WHERE `id_profile` = 4;
UPDATE `PREFIX_module_access` SET `id_profile` = 4 WHERE `id_profile` = 3;
UPDATE `PREFIX_module_access` SET `id_profile` = 3 WHERE `id_profile` = 2;
UPDATE `PREFIX_module_access` SET `id_profile` = 2 WHERE `id_profile` = 1;

INSERT INTO `PREFIX_module_access` (`id_profile`, `id_module`, `configure`, `view`) (SELECT 1, `id_module`, 1, 1 FROM `PREFIX_module`);

ALTER TABLE `PREFIX_carrier` ADD `position` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

/* PHP:add_carrier_position();*/

ALTER TABLE `PREFIX_order_state` ADD COLUMN `shipped` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `delivery`;
UPDATE `PREFIX_order_state` SET `shipped` = 1 WHERE id_order_states IN (4, 5);


CREATE TABLE `PREFIX_cart_rule` (
	`id_cart_rule` int(10) unsigned NOT NULL auto_increment,
	`id_customer` int unsigned NOT NULL default 0,
	`date_from` datetime NOT NULL,
	`date_to` datetime NOT NULL,
	`description` text,
	`quantity` int(10) unsigned NOT NULL default 0,
	`quantity_per_user` int(10) unsigned NOT NULL default 0,
	`priority` int(10) unsigned NOT NULL default 1,
	`code` varchar(254) NOT NULL,
	`minimum_amount` decimal(17,2) NOT NULL default 0,
	`minimum_amount_tax` tinyint(1) NOT NULL default 0,
	`minimum_amount_currency` int unsigned NOT NULL default 0,
	`minimum_amount_shipping` tinyint(1) NOT NULL default 0,
	`country_restriction` tinyint(1) unsigned NOT NULL default 0,
	`carrier_restriction` tinyint(1) unsigned NOT NULL default 0,
	`group_restriction` tinyint(1) unsigned NOT NULL default 0,
	`cart_rule_restriction` tinyint(1) unsigned NOT NULL default 0,
	`product_restriction` tinyint(1) unsigned NOT NULL default 0,
	`free_shipping` tinyint(1) NOT NULL default 0,
	`reduction_percent` decimal(4,2) NOT NULL default 0,
	`reduction_amount` decimal(17,2) NOT NULL default 0,
	`reduction_tax` tinyint(1) unsigned NOT NULL default 0,
	`reduction_currency` int(10) unsigned NOT NULL default 0,
	`reduction_product` int(10) NOT NULL default 0,
	`gift_product` int(10) unsigned NOT NULL default 0,
	`active` tinyint(1) unsigned NOT NULL default 0,
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id_cart_rule`)
);

CREATE TABLE `PREFIX_cart_rule_lang` (
	`id_cart_rule` int(10) unsigned NOT NULL,
	`id_lang` int(10) unsigned NOT NULL,
	`name` varchar(254) NOT NULL,
	PRIMARY KEY  (`id_cart_rule`, `id_lang`)
);

CREATE TABLE `PREFIX_cart_rule_country` (
	`id_cart_rule` int(10) unsigned NOT NULL,
	`id_country` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`id_cart_rule`, `id_country`)
);

CREATE TABLE `PREFIX_cart_rule_group` (
	`id_cart_rule` int(10) unsigned NOT NULL,
	`id_group` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`id_cart_rule`, `id_group`)
);

CREATE TABLE `PREFIX_cart_rule_carrier` (
	`id_cart_rule` int(10) unsigned NOT NULL,
	`id_carrier` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`id_cart_rule`, `id_carrier`)
);

CREATE TABLE `PREFIX_cart_rule_combination` (
	`id_cart_rule_1` int(10) unsigned NOT NULL,
	`id_cart_rule_2` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`id_cart_rule_1`, `id_cart_rule_2`)
);

CREATE TABLE `PREFIX_cart_rule_product_rule` (
	`id_product_rule` int(10) unsigned NOT NULL auto_increment,
	`id_cart_rule` int(10) unsigned NOT NULL,
	`quantity` int(10) unsigned NOT NULL default 1,
	`type` ENUM('products', 'categories', 'attributes') NOT NULL,
	PRIMARY KEY  (`id_product_rule`)
);

CREATE TABLE `PREFIX_cart_rule_product_rule_value` (
	`id_product_rule` int(10) unsigned NOT NULL,
	`id_item` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`id_product_rule`, `id_item`)
);

ALTER TABLE `PREFIX_cart_discount` CHANGE `id_discount` `id_cart_rule` int(10) unsigned NOT NULL;
ALTER TABLE `PREFIX_order_discount` CHANGE `id_discount` `id_cart_rule` int(10) unsigned NOT NULL;
ALTER TABLE `PREFIX_order_discount` CHANGE `id_order_discount` `id_order_cart_rule` int(10) unsigned NOT NULL;

RENAME TABLE `PREFIX_order_discount` TO `PREFIX_order_cart_rule`;
RENAME TABLE `PREFIX_cart_discount` TO `PREFIX_cart_cart_rule`;

CREATE VIEW `PREFIX_order_discount` AS SELECT *, id_cart_rule as id_discount FROM `PREFIX_order_cart_rule`;
CREATE VIEW `PREFIX_cart_discount` AS SELECT *, id_cart_rule as id_discount FROM `PREFIX_cart_cart_rule`;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) (
	SELECT 'PS_CART_RULE_FEATURE_ACTIVE', `value`, NOW(), NOW() FROM `PREFIX_configuration` WHERE `name` = 'PS_DISCOUNT_FEATURE_ACTIVE' LIMIT 1
);

UPDATE `PREFIX_tab` SET class_name = 'AdminCartRules' WHERE class_name = 'AdminDiscounts';
