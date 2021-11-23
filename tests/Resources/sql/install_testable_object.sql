/* Testable object */
CREATE TABLE IF NOT EXISTS `PREFIX_testable_object` (
  `id_testable_object` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quantity` int(10) unsigned NOT NULL DEFAULT '0',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_testable_object`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4 COLLATION;

/* Localized Testable object */
CREATE TABLE IF NOT EXISTS `PREFIX_testable_object_lang` (
  `id_testable_object` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `id_shop` int(10) unsigned NOT NULL DEFAULT '1',
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id_testable_object`, `id_shop`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4 COLLATION;

/* Association between a Testable object and a shop */
CREATE TABLE IF NOT EXISTS `PREFIX_testable_object_shop` (
  `id_testable_object` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_shop` INT(11) UNSIGNED NOT NULL,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_testable_object`, `id_shop`),
  KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4 COLLATION;

TRUNCATE `PREFIX_testable_object_shop`;
TRUNCATE `PREFIX_testable_object_lang`;
TRUNCATE `PREFIX_testable_object`;
