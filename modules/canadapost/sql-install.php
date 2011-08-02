<?php

	// Init
	$sql = array();

	// Create Service Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'cp_rate_service_code` (
			  `id_cp_rate_service_code` int(10) NOT NULL AUTO_INCREMENT,
			  `id_carrier` int(10) NOT NULL,
			  `id_carrier_history` text NOT NULL,
			  `code` varchar(64) NOT NULL,
			  `service` varchar(255) NOT NULL,
			  `delay` varchar(255) NOT NULL,
			  `active` tinyint(1) NOT NULL,
			  UNIQUE(`code`, `service`),
			  PRIMARY KEY  (`id_cp_rate_service_code`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

	// Insert Service in database
	$sql[] = "INSERT INTO `"._DB_PREFIX_."cp_rate_service_code` (`id_carrier`, `id_carrier_history`, `code`, `service`, `delay`, `active`) VALUES
			('0', '', 'PRIORITY_COURIER', 'Priority Courier', 'Priority Courier: 2 days', '0'),
			('0', '', 'REGULAR', 'Regular', 'Regular: 4 days', '0'),
			('0', '', 'XPRESSPOST', 'Xpresspost', 'Xpresspost: 1 day', '0'),
			('0', '', 'EXPEDITED', 'Expedited', 'Expedited: 1 day', '0'),
			('0', '', 'PRIORITY_WORLDWIDE_USA', 'Priority Worldwide USA', 'Priority Worldwide USA', '0'),
			('0', '', 'Xpresspost_USA', 'Xpresspost USA', 'Xpresspost USA', '0'),
			('0', '', 'Expedited_US_Business', 'Expedited US Business', 'Expedited US Business', '0'),
			('0', '', 'Small_Packets_Air', 'Small Packets Air', 'Small Packets Air', '0'),
			('0', '', 'Small_Packets_Surface', 'Small Packets Surface', 'Small Packets Surface', '0'),
			('0', '', 'U.S.A_Letter-post', 'U.S.A Letter-post', 'U.S.A Letter-post', '0'),
			('0', '', 'Priority_Worldwide INTL', 'Priority Worldwide INTL', 'Priority Worldwide INTL', '0'),
			('0', '', 'XPressPost_International', 'XPressPost International', 'XPressPost International', '0'),
			('0', '', 'Parcel_Surface', 'Parcel Surface', 'Parcel Surface', '0'),
			('0', '', 'Small_Packets Surface', 'Small Packets Surface', 'Small Packets Surface', '0'),
			('0', '', 'INTL_Letter-post', 'INTL Letter-post', 'INTL Letter-post', '0');";

	// Create Cache Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'cp_cache` (
			  `id_cp_cache` int(10) NOT NULL AUTO_INCREMENT,
			  `id_cart` int(10) NOT NULL,
			  `id_carrier` int(10) NOT NULL,
			  `hash` varchar(32) NOT NULL,
			  `id_currency` int(10) NOT NULL,
			  `total_charges` double(10,2) NOT NULL,
			  `is_available` tinyint(1) NOT NULL,
			  `date_add` datetime NOT NULL,
			  `date_upd` datetime NOT NULL,
			  PRIMARY KEY  (`id_cp_cache`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

	// Create Test Cache Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'cp_cache_test` (
			`id_cp_cache_test` int(10) NOT NULL AUTO_INCREMENT,
			`hash` varchar(1024) NOT NULL,
			`result` text NOT NULL,
			`date_add` datetime NOT NULL,
			`date_upd` datetime NOT NULL,
			PRIMARY KEY  (`id_cp_cache_test`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

	// Create Config Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'cp_rate_config` (
			`id_cp_rate_config` int(10) NOT NULL AUTO_INCREMENT,
			`id_product` int(10) NOT NULL,
			`id_category` int(10) NOT NULL,
			`id_currency` int(10) NOT NULL,
			`additional_charges` double(6,2) NOT NULL,
			`date_add` datetime NOT NULL,
			`date_upd` datetime NOT NULL,
			PRIMARY KEY  (`id_cp_rate_config`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

	// Create Config (Service) Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'cp_rate_config_service` (
			`id_cp_rate_config_service` int(10) NOT NULL AUTO_INCREMENT,
			`id_cp_rate_service_code` int(10) NOT NULL,
			`id_cp_rate_config` int(10) NOT NULL,
			`date_add` datetime NOT NULL,
			`date_upd` datetime NOT NULL,
			PRIMARY KEY  (`id_cp_rate_config_service`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
