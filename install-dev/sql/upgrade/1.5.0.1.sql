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