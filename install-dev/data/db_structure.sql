SET SESSION sql_mode = '';
SET NAMES 'utf8';

CREATE TABLE `PREFIX_accessory` (
  `id_product_1` int(10) unsigned NOT NULL,
  `id_product_2` int(10) unsigned NOT NULL,
  KEY `accessory_product` (`id_product_1`,`id_product_2`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;



CREATE TABLE `PREFIX_message` (
  `id_message` int(10) unsigned NOT NULL auto_increment,
  `id_cart` int(10) unsigned DEFAULT NULL,
  `id_customer` int(10) unsigned NOT NULL,
  `id_employee` int(10) unsigned DEFAULT NULL,
  `id_order` int(10) unsigned NOT NULL,
  `message` text NOT NULL,
  `private` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_message`),
  KEY `message_order` (`id_order`),
  KEY `id_cart` (`id_cart`),
  KEY `id_customer` (`id_customer`),
  KEY `id_employee` (`id_employee`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_message_readed` (
  `id_message` int(10) unsigned NOT NULL,
  `id_employee` int(10) unsigned NOT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_message`,`id_employee`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_meta` (
  `id_meta` int(10) unsigned NOT NULL auto_increment,
  `page` varchar(64) NOT NULL,
	`configurable` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_meta`),
  UNIQUE KEY `page` (`page`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_meta_lang` (
  `id_meta` int(10) unsigned NOT NULL,
   `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `id_lang` int(10) unsigned NOT NULL,
  `title` varchar(128) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `url_rewrite` varchar(254) NOT NULL,
  PRIMARY KEY (`id_meta`, `id_shop`, `id_lang`),
  KEY `id_shop` (`id_shop`),
  KEY `id_lang` (`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;


CREATE TABLE `PREFIX_operating_system` (
  `id_operating_system` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id_operating_system`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;


CREATE TABLE `PREFIX_profile` (
  `id_profile` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY (`id_profile`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_profile_lang` (
  `id_lang` int(10) unsigned NOT NULL,
  `id_profile` int(10) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id_profile`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_quick_access` (
  `id_quick_access` int(10) unsigned NOT NULL auto_increment,
  `new_window` tinyint(1) NOT NULL DEFAULT '0',
  `link` varchar(255) NOT NULL,
  PRIMARY KEY (`id_quick_access`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_quick_access_lang` (
  `id_quick_access` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id_quick_access`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_range_price` (
  `id_range_price` int(10) unsigned NOT NULL auto_increment,
  `id_carrier` int(10) unsigned NOT NULL,
  `delimiter1` decimal(20,6) NOT NULL,
  `delimiter2` decimal(20,6) NOT NULL,
  PRIMARY KEY (`id_range_price`),
  UNIQUE KEY `id_carrier` (`id_carrier`,`delimiter1`,`delimiter2`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_range_weight` (
  `id_range_weight` int(10) unsigned NOT NULL auto_increment,
  `id_carrier` int(10) unsigned NOT NULL,
  `delimiter1` decimal(20,6) NOT NULL,
  `delimiter2` decimal(20,6) NOT NULL,
  PRIMARY KEY (`id_range_weight`),
  UNIQUE KEY `id_carrier` (`id_carrier`,`delimiter1`,`delimiter2`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;


CREATE TABLE IF NOT EXISTS `PREFIX_request_sql` (
  `id_request_sql` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `sql` text NOT NULL,
  PRIMARY KEY (`id_request_sql`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_search_engine` (
  `id_search_engine` int(10) unsigned NOT NULL auto_increment,
  `server` varchar(64) NOT NULL,
  `getvar` varchar(16) NOT NULL,
  PRIMARY KEY (`id_search_engine`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_search_index` (
  `id_product` int(11) unsigned NOT NULL,
  `id_word` int(11) unsigned NOT NULL,
  `weight` smallint(4) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_word`, `id_product`),
  KEY `id_product` (`id_product`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_search_word` (
  `id_word` int(10) unsigned NOT NULL auto_increment,
  `id_shop` int(11) unsigned NOT NULL DEFAULT 1,
  `id_lang` int(10) unsigned NOT NULL,
  `word` varchar(15) NOT NULL,
  PRIMARY KEY (`id_word`),
  UNIQUE KEY `id_lang` (`id_lang`,`id_shop`, `word`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;


CREATE TABLE `PREFIX_state` (
  `id_state` int(10) unsigned NOT NULL auto_increment,
  `id_country` int(11) unsigned NOT NULL,
  `id_zone` int(11) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `iso_code` varchar(7) NOT NULL,
  `tax_behavior` smallint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_state`),
  KEY `id_country` (`id_country`),
  KEY `name` (`name`),
  KEY `id_zone` (`id_zone`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;


CREATE TABLE `PREFIX_supplier` (
  `id_supplier` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_supplier`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_supplier_lang` (
  `id_supplier` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `description` text,
  `meta_title` varchar(128) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_supplier`,`id_lang`)
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


CREATE TABLE `PREFIX_store` (
  `id_store` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_country` int(10) unsigned NOT NULL,
  `id_state` int(10) unsigned DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `address1` varchar(128) NOT NULL,
  `address2` varchar(128) DEFAULT NULL,
  `city` varchar(64) NOT NULL,
  `postcode` varchar(12) NOT NULL,
  `latitude` decimal(13,8) DEFAULT NULL,
  `longitude` decimal(13,8) DEFAULT NULL,
  `hours` text,
  `phone` varchar(16) DEFAULT NULL,
  `fax` varchar(16) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `note` text,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_store`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_required_field` (
  `id_required_field` int(11) NOT NULL AUTO_INCREMENT,
  `object_name` varchar(32) NOT NULL,
  `field_name` varchar(32) NOT NULL,
  PRIMARY KEY (`id_required_field`),
  KEY `object_name` (`object_name`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_memcached_servers` (
`id_memcached_server` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`ip` VARCHAR( 254 ) NOT NULL ,
`port` INT(11) UNSIGNED NOT NULL ,
`weight` INT(11) UNSIGNED NOT NULL
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;



CREATE TABLE IF NOT EXISTS `PREFIX_shop_url` (
  `id_shop_url` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_shop` int(11) unsigned NOT NULL,
  `domain` varchar(150) NOT NULL,
  `domain_ssl` varchar(150) NOT NULL,
  `physical_uri` varchar(64) NOT NULL,
  `virtual_uri` varchar(64) NOT NULL,
  `main` TINYINT(1) NOT NULL,
  `active` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id_shop_url`),
  KEY `id_shop` (`id_shop`, `main`),
  UNIQUE KEY `full_shop_url` (`domain`, `physical_uri`, `virtual_uri`),
  UNIQUE KEY `full_shop_url_ssl` (`domain_ssl`, `physical_uri`, `virtual_uri`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;




CREATE TABLE `PREFIX_group_shop` (
`id_group` INT( 11 ) UNSIGNED NOT NULL,
`id_shop` INT( 11 ) UNSIGNED NOT NULL,
	PRIMARY KEY (`id_group`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;



CREATE TABLE `PREFIX_supplier_shop` (
`id_supplier` INT( 11 ) UNSIGNED NOT NULL,
`id_shop` INT( 11 ) UNSIGNED NOT NULL,
PRIMARY KEY (`id_supplier`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_store_shop` (
`id_store` INT( 11 ) UNSIGNED NOT NULL ,
`id_shop` INT( 11 ) UNSIGNED NOT NULL,
PRIMARY KEY (`id_store`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;


CREATE TABLE `PREFIX_stock_mvt_reason` (
  `id_stock_mvt_reason` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sign` tinyint(1) NOT NULL DEFAULT 1,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_stock_mvt_reason`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_stock_mvt_reason_lang` (
  `id_stock_mvt_reason` INT(11) UNSIGNED NOT NULL,
  `id_lang` INT(11) UNSIGNED NOT NULL,
  `name` VARCHAR(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id_stock_mvt_reason`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_stock` (
`id_stock` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`id_warehouse` INT(11) UNSIGNED NOT NULL,
`id_product` INT(11) UNSIGNED NOT NULL,
`id_product_attribute` INT(11) UNSIGNED NOT NULL,
`reference`  VARCHAR(32) NOT NULL,
`ean13`  VARCHAR(13) DEFAULT NULL,
`isbn`  VARCHAR(32) DEFAULT NULL,
`upc`  VARCHAR(12) DEFAULT NULL,
`physical_quantity` INT(11) UNSIGNED NOT NULL,
`usable_quantity` INT(11) UNSIGNED NOT NULL,
`price_te` DECIMAL(20,6) DEFAULT '0.000000',
  PRIMARY KEY (`id_stock`),
  KEY `id_warehouse` (`id_warehouse`),
  KEY `id_product` (`id_product`),
  KEY `id_product_attribute` (`id_product_attribute`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE `PREFIX_stock_available` (
`id_stock_available` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`id_product` INT(11) UNSIGNED NOT NULL,
`id_product_attribute` INT(11) UNSIGNED NOT NULL,
`id_shop` INT(11) UNSIGNED NOT NULL,
`id_shop_group` INT(11) UNSIGNED NOT NULL,
`quantity` INT(10) NOT NULL DEFAULT '0',
`physical_quantity` INT(11) NOT NULL DEFAULT '0',
`reserved_quantity` INT(11) NOT NULL DEFAULT '0',
`depends_on_stock` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
`out_of_stock` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_stock_available`),
  KEY `id_shop` (`id_shop`),
  KEY `id_shop_group` (`id_shop_group`),
  KEY `id_product` (`id_product`),
  KEY `id_product_attribute` (`id_product_attribute`),
  UNIQUE `product_sqlstock` (`id_product` , `id_product_attribute` , `id_shop`, `id_shop_group`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE IF NOT EXISTS `PREFIX_risk` (
  `id_risk` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `percent` tinyint(3) NOT NULL,
  `color` varchar(32) NULL,
  PRIMARY KEY (`id_risk`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

CREATE TABLE IF NOT EXISTS `PREFIX_risk_lang` (
  `id_risk` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id_risk`,`id_lang`),
  KEY `id_risk` (`id_risk`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;


CREATE TABLE `PREFIX_tab_module_preference` (
  `id_tab_module_preference` int(11) NOT NULL auto_increment,
  `id_employee` int(11) NOT NULL,
  `id_tab` int(11) NOT NULL,
  `module` varchar(255) NOT NULL,
  PRIMARY KEY (`id_tab_module_preference`),
  UNIQUE KEY `employee_module` (`id_employee`, `id_tab`, `module`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 COLLATION;
