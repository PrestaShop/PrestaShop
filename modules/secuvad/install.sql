CREATE TABLE IF NOT EXISTS `PREFIX_secuvad_assoc_category` 
(
  	`id_secuvad_assoc_category` int NOT NULL auto_increment,
	`id_category` int default NULL,
	`category_id` int default NULL,
	PRIMARY KEY  (`id_secuvad_assoc_category`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_secuvad_assoc_payment`
(
	`id_secuvad_assoc_payment` int NOT NULL auto_increment,
	`id_module` int default NULL,
	`code` varchar(45) default NULL,
	PRIMARY KEY  (`id_secuvad_assoc_payment`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_secuvad_assoc_transport`
(
	`id_secuvad_assoc_transport` int NOT NULL auto_increment,
	`id_carrier` int default NULL,
	`transport_id` int default NULL,
	`transport_delay_id` int default NULL,
	PRIMARY KEY  (`id_secuvad_assoc_transport`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_secuvad_category`
(
	`category_id` int NOT NULL default '0',
	`category_name` varchar(255) default NULL,
	`sort_num` int default NULL,
	`id_lang` int default '1',
	PRIMARY KEY  (`category_id`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_secuvad_logs`
(
	`id_secuvad_logs` int NOT NULL auto_increment,
	`message` text default NULL,
	`date` timestamp NOT NULL default CURRENT_TIMESTAMP,
	PRIMARY KEY  (`id_secuvad_logs`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_secuvad_order`
(
	`id_secuvad_order` int NOT NULL,
	`secuvad_status` tinyint NOT NULL default '0',
	`is_fraud` tinyint NOT NULL default '0',
	`score` int unsigned default NULL,
	`error` varchar(150) NOT NULL,
	`advice` varchar(45) NOT NULL,
	`ip` varchar(16) default NULL,
	`ip_time` timestamp NULL default NULL,
	PRIMARY KEY  (`id_secuvad_order`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_secuvad_payment`
(
	`code` varchar(45) NOT NULL default '',
	`name` varchar(255) default NULL,
	`id_lang` int(11) NOT NULL,
	PRIMARY KEY  USING BTREE (`code`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_secuvad_transport`
(
	`transport_id` int NOT NULL,
	`transport_name` varchar(150) character set latin1 default NULL,
	`id_lang` int(11) NOT NULL,
	PRIMARY KEY  USING BTREE (`transport_id`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_secuvad_transport_delay`
(
	`transport_delay_id` int NOT NULL,
	`transport_delay_name` varchar(45) character set latin1 default NULL,
	`id_lang` int(11) NOT NULL,
	PRIMARY KEY  USING BTREE (`transport_delay_id`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_payment_cc` (
  `id_payment_cc` int(11) NOT NULL AUTO_INCREMENT,
  `id_order` int(10) unsigned DEFAULT NULL,
  `id_currency` int(10) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_id` varchar(254) DEFAULT NULL,
  `card_number` varchar(254) DEFAULT NULL,
  `card_brand` varchar(254) DEFAULT NULL,
  `card_expiration` char(7) DEFAULT NULL,
  `card_holder` varchar(254) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_payment_cc`),
  KEY `id_order` (`id_order`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
