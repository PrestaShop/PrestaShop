SET SESSION sql_mode = '';
SET NAMES 'utf8';

CREATE TABLE `PREFIX_accessory` (
  `id_product_1` int(10) unsigned NOT NULL,
  `id_product_2` int(10) unsigned NOT NULL,
  KEY `accessory_product` (`id_product_1`,`id_product_2`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;


CREATE TABLE `PREFIX_tag` (
  `id_tag` int(10) unsigned NOT NULL auto_increment,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id_tag`),
  KEY `tag_name` (`name`),
  KEY `id_lang` (`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_tag_count` (
  `id_group` int(10) unsigned NOT NULL DEFAULT 0,
  `id_tag` int(10) unsigned NOT NULL DEFAULT 0,
  `id_lang` int(10) unsigned NOT NULL DEFAULT 0,
  `id_shop` int(11) unsigned NOT NULL DEFAULT 0,
  `counter` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_group`, `id_tag`),
  KEY (`id_group`, `id_lang`, `id_shop`, `counter`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;


CREATE TABLE `PREFIX_memcached_servers` (
`id_memcached_server` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`ip` VARCHAR( 254 ) NOT NULL ,
`port` INT(11) UNSIGNED NOT NULL ,
`weight` INT(11) UNSIGNED NOT NULL
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;




CREATE TABLE `PREFIX_group_shop` (
`id_group` INT( 11 ) UNSIGNED NOT NULL,
`id_shop` INT( 11 ) UNSIGNED NOT NULL,
	PRIMARY KEY (`id_group`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;




CREATE TABLE `PREFIX_tab_module_preference` (
  `id_tab_module_preference` int(11) NOT NULL auto_increment,
  `id_employee` int(11) NOT NULL,
  `id_tab` int(11) NOT NULL,
  `module` varchar(255) NOT NULL,
  PRIMARY KEY (`id_tab_module_preference`),
  UNIQUE KEY `employee_module` (`id_employee`, `id_tab`, `module`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;
