SET NAMES 'utf8';

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES('PS_DASHBOARD_USE_PUSH', '0', NOW(), NOW());
UPDATE `PREFIX_configuration` SET `value` = 'graphnvd3' WHERE `name` = 'PS_STATS_RENDER';

ALTER TABLE `PREFIX_employee` CHANGE `bo_show_screencast` `bo_menu` TINYINT(1) NOT NULL DEFAULT '1';
UPDATE `PREFIX_employee` SET bo_menu = 1;

CREATE TABLE `PREFIX_configuration_kpi` (
  `id_configuration_kpi` int(10) unsigned NOT NULL auto_increment,
  `id_shop_group` INT(11) UNSIGNED DEFAULT NULL,
  `id_shop` INT(11) UNSIGNED DEFAULT NULL,
  `name` varchar(32) NOT NULL,
  `value` text,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_configuration_kpi`),
  KEY `name` (`name`),
  KEY `id_shop` (`id_shop`),
  KEY `id_shop_group` (`id_shop_group`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_configuration_kpi_lang` (
  `id_configuration_kpi` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `value` text,
  `date_upd` datetime default NULL,
  PRIMARY KEY (`id_configuration_kpi`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

/* PHP:ps1600_add_missing_index(); */;

UPDATE `PREFIX_configuration` SET `value`='-' WHERE `name` = 'PS_ATTRIBUTE_ANCHOR_SEPARATOR';

UPDATE `PREFIX_tab` SET class_name = 'AdminDashboard' WHERE class_name = 'AdminHome';
