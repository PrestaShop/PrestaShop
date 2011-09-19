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
('PS_COMBINATION_FEATURE_ACTIVE', '1', NOW(), NOW());

ALTER TABLE `PREFIX_product` ADD `available_date` DATETIME NOT NULL AFTER `available_for_order`;

ALTER TABLE `PREFIX_product_attribute` ADD `available_date` DATETIME NOT NULL;

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
