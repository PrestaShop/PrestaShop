<?php

	// Init
	$sql = array();

	// Create Service Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'usps_rate_service_code` (
			  `id_usps_rate_service_code` int(10) NOT NULL AUTO_INCREMENT,
			  `id_carrier` int(10) NOT NULL,
			  `id_carrier_history` text NOT NULL,
			  `code` varchar(64) NOT NULL,
			  `service` varchar(255) NOT NULL,
			  `active` tinyint(1) NOT NULL,
			  UNIQUE(`code`, `service`),
			  PRIMARY KEY  (`id_usps_rate_service_code`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

	// Insert Service in database
	$sql[] = "INSERT INTO `"._DB_PREFIX_."usps_rate_service_code` (`id_carrier`, `id_carrier_history`, `code`, `service`, `active`) VALUES
			('0', '', 'FIRST CLASS', 'First-Class Mail (2-3 days)', '0'),
			('0', '', 'FIRST CLASS COMMERCIAL', 'First-Class Mail (2-3 days)', '0'),
			('0', '', 'PRIORITY', 'Priority Mail (1-3 days)', '0'),
			('0', '', 'PRIORITY COMMERCIAL', 'Priority Mail (1-3 days)', '0'),
			('0', '', 'EXPRESS', 'Express Mail (1-2 days)', '0'),
			('0', '', 'EXPRESS COMMERCIAL', 'Express Mail (1-2 days)', '0'),
			('0', '', 'PARCEL', 'Parcel Post (2-9 days)', '0'),
			('0', '', 'MEDIA', 'Media Mail (2-9 days)', '0'),
			('0', '', 'LIBRARY', 'Library Mail (2-9 days)', '0');";

	// Create Cache Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'usps_cache` (
			  `id_usps_cache` int(10) NOT NULL AUTO_INCREMENT,
			  `id_cart` int(10) NOT NULL,
			  `id_carrier` int(10) NOT NULL,
			  `hash` varchar(32) NOT NULL,
			  `id_currency` int(10) NOT NULL,
			  `total_charges` double(10,2) NOT NULL,
			  `is_available` tinyint(1) NOT NULL,
			  `date_add` datetime NOT NULL,
			  `date_upd` datetime NOT NULL,
			  PRIMARY KEY  (`id_usps_cache`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

	// Create Test Cache Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'usps_cache_test` (
			`id_usps_cache_test` int(10) NOT NULL AUTO_INCREMENT,
			`hash` varchar(1024) NOT NULL,
			`result` text NOT NULL,
			`date_add` datetime NOT NULL,
			`date_upd` datetime NOT NULL,
			PRIMARY KEY  (`id_usps_cache_test`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

	// Create Config Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'usps_rate_config` (
			`id_usps_rate_config` int(10) NOT NULL AUTO_INCREMENT,
			`id_product` int(10) NOT NULL,
			`id_category` int(10) NOT NULL,
			`id_currency` int(10) NOT NULL,
			`packaging_type_code` varchar(64) NOT NULL,
			`packaging_size_code` varchar(64) NOT NULL,
			`machinable_code` varchar(64) NOT NULL,
			`additional_charges` double(6,2) NOT NULL,
			`date_add` datetime NOT NULL,
			`date_upd` datetime NOT NULL,
			PRIMARY KEY  (`id_usps_rate_config`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

	// Create Config (Service) Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'usps_rate_config_service` (
			`id_usps_rate_config_service` int(10) NOT NULL AUTO_INCREMENT,
			`id_usps_rate_service_code` int(10) NOT NULL,
			`id_usps_rate_config` int(10) NOT NULL,
			`date_add` datetime NOT NULL,
			`date_upd` datetime NOT NULL,
			PRIMARY KEY  (`id_usps_rate_config_service`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


