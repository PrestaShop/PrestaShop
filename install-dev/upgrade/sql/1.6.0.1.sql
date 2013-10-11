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

UPDATE `PREFIX_tab` SET class_name = 'AdminDashboard', id_parent = 0, active = 1, module = "" WHERE class_name = 'AdminHome';

INSERT INTO `PREFIX_module` (`name`, `active`, `version`)
VALUES ('dashactivity', '1', '0.1'), ('dashtrends', '1', '0.1'), ('dashproducts', '1', '0.1');

INSERT INTO `PREFIX_module_access` (`id_profile`, `id_module`, `view`, `configure`) (
	SELECT p.id_profile, m.id_module, 1, 1 FROM `PREFIX_module` m, `PREFIX_profile` p WHERE m.name IN ('dashactivity', 'dashtrends', 'dashproducts')
);

INSERT INTO `PREFIX_module_shop` (`id_module`, `id_shop`) (
	SELECT m.id_module, s.id_shop FROM `PREFIX_module` m, `PREFIX_shop` s WHERE m.name IN ('dashactivity', 'dashtrends', 'dashproducts')
);

INSERT INTO `PREFIX_hook` (`name`, `title`, `position`, `live_edit`) VALUES
('dashboardZoneOne', 'dashboardZoneOne', 1, 0),
('dashboardZoneTwo', 'dashboardZoneTwo', 1, 0),
('dashboardData', 'dashboardData', 0, 0);

INSERT INTO `PREFIX_hook_module` (`id_module`, `id_shop`, `id_hook`, `position`) (
	SELECT m.id_module, s.id_shop, h.id_hook, 0 FROM `PREFIX_module` m, `PREFIX_shop` s, `PREFIX_hook` h WHERE m.name IN ('dashactivity', 'dashtrends', 'dashproducts') AND h.name IN ('dashboardData')
);
INSERT INTO `PREFIX_hook_module` (`id_module`, `id_shop`, `id_hook`, `position`) (
	SELECT m.id_module, s.id_shop, h.id_hook, 1 FROM `PREFIX_module` m, `PREFIX_shop` s, `PREFIX_hook` h WHERE m.name IN ('dashactivity') AND h.name IN ('dashboardZoneOne')
);
INSERT INTO `PREFIX_hook_module` (`id_module`, `id_shop`, `id_hook`, `position`) (
	SELECT m.id_module, s.id_shop, h.id_hook, 1 FROM `PREFIX_module` m, `PREFIX_shop` s, `PREFIX_hook` h WHERE m.name IN ('dashtrends') AND h.name IN ('dashboardZoneTwo')
);
INSERT INTO `PREFIX_hook_module` (`id_module`, `id_shop`, `id_hook`, `position`) (
	SELECT m.id_module, s.id_shop, h.id_hook, 2 FROM `PREFIX_module` m, `PREFIX_shop` s, `PREFIX_hook` h WHERE m.name IN ('dashproducts') AND h.name IN ('dashboardZoneTwo')
);

/* PHP:update_order_messages(); */;

ALTER TABLE  `PREFIX_employee` ADD  `stats_compare_from` DATE NULL DEFAULT NULL AFTER  `stats_date_to` , ADD  `stats_compare_to` DATE NULL DEFAULT NULL AFTER  `stats_compare_from`