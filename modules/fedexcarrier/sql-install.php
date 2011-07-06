<?php

	// Init
	$sql = array();

	// Create Service Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fedex_rate_service_code` (
			  `id_fedex_rate_service_code` int(10) NOT NULL AUTO_INCREMENT,
			  `id_carrier` int(10) NOT NULL,
			  `id_carrier_history` text NOT NULL,
			  `code` varchar(64) NOT NULL,
			  `service` varchar(255) NOT NULL,
			  `active` tinyint(1) NOT NULL,
			  UNIQUE(`code`, `service`),
			  PRIMARY KEY  (`id_fedex_rate_service_code`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

	// Insert Service in database
	$sql[] = "INSERT INTO `"._DB_PREFIX_."fedex_rate_service_code` (`id_carrier`, `id_carrier_history`, `code`, `service`, `active`) VALUES
			('0', '', 'EUROPE_FIRST_INTERNATIONAL_PRIORITY', 'Europe first international priority', '0'),
			('0', '', 'FEDEX_1_DAY_FREIGHT', 'Fedex 1 day freight', '0'),
			('0', '', 'FEDEX_2_DAY', 'Fedex 2 day', '0'),
			('0', '', 'FEDEX_2_DAY_FREIGHT', 'Fedex 2 day freight', '0'),
			('0', '', 'FEDEX_3_DAY_FREIGHT', 'Fedex 3 day freight', '0'),
			('0', '', 'FEDEX_EXPRESS_SAVER', 'Fedex express saver', '0'),
			('0', '', 'FEDEX_FREIGHT', 'Fedex freight', '0'),
			('0', '', 'FEDEX_GROUND', 'Fedex ground', '0'),
			('0', '', 'FEDEX_NATIONAL_FREIGHT', 'Fedex national freight', '0'),
			('0', '', 'FIRST_OVERNIGHT', 'First overnight', '0'),
			('0', '', 'GROUND_HOME_DELIVERY', 'Ground home delivery', '0'),
			('0', '', 'INTERNATIONAL_ECONOMY', 'International economy', '0'),
			('0', '', 'INTERNATIONAL_ECONOMY_FREIGHT', 'International economy freight', '0'),
			('0', '', 'INTERNATIONAL_FIRST', 'International first', '0'),
			('0', '', 'INTERNATIONAL_GROUND', 'International ground', '0'),
			('0', '', 'INTERNATIONAL_PRIORITY', 'International priority', '0'),
			('0', '', 'INTERNATIONAL_PRIORITY_FREIGHT', 'International priority freight', '0'),
			('0', '', 'PRIORITY_OVERNIGHT', 'Priority overnight', '0'),
			('0', '', 'SMART_POST', 'Smart post', '0'),
			('0', '', 'STANDARD_OVERNIGHT', 'Standard overnight', '0');";

	// Create Cache Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fedex_cache` (
			  `id_fedex_cache` int(10) NOT NULL AUTO_INCREMENT,
			  `id_cart` int(10) NOT NULL,
			  `id_carrier` int(10) NOT NULL,
			  `hash` varchar(32) NOT NULL,
			  `id_currency` int(10) NOT NULL,
			  `total_charges` double(10,2) NOT NULL,
			  `is_available` tinyint(1) NOT NULL,
			  `date_add` datetime NOT NULL,
			  `date_upd` datetime NOT NULL,
			  PRIMARY KEY  (`id_fedex_cache`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

	// Create Test Cache Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fedex_cache_test` (
			`id_fedex_cache_test` int(10) NOT NULL AUTO_INCREMENT,
			`hash` varchar(1024) NOT NULL,
			`result` text NOT NULL,
			`date_add` datetime NOT NULL,
			`date_upd` datetime NOT NULL,
			PRIMARY KEY  (`id_fedex_cache_test`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

	// Create Config Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fedex_rate_config` (
			`id_fedex_rate_config` int(10) NOT NULL AUTO_INCREMENT,
			`id_product` int(10) NOT NULL,
			`id_category` int(10) NOT NULL,
			`id_currency` int(10) NOT NULL,
			`pickup_type_code` varchar(64) NOT NULL,
			`packaging_type_code` varchar(64) NOT NULL,
			`additional_charges` double(6,2) NOT NULL,
			`date_add` datetime NOT NULL,
			`date_upd` datetime NOT NULL,
			PRIMARY KEY  (`id_fedex_rate_config`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

	// Create Config (Service) Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fedex_rate_config_service` (
			`id_fedex_rate_config_service` int(10) NOT NULL AUTO_INCREMENT,
			`id_fedex_rate_service_code` int(10) NOT NULL,
			`id_fedex_rate_config` int(10) NOT NULL,
			`date_add` datetime NOT NULL,
			`date_upd` datetime NOT NULL,
			PRIMARY KEY  (`id_fedex_rate_config_service`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

?>
