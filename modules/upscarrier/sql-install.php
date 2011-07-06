<?php

	// Init
	$sql = array();

	// Create Service Group Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ups_rate_service_group` (
			`id_ups_rate_service_group` int(10) NOT NULL AUTO_INCREMENT,
			`name` varchar(255) NOT NULL,
			UNIQUE(`name`),
			PRIMARY KEY  (`id_ups_rate_service_group`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

	// Insert Service Group in Database
	$sql[] = "INSERT INTO `"._DB_PREFIX_."ups_rate_service_group` (`name`) VALUES
			('United States Domestic Shipments'),
			('Shipments Originating in United States'),
			('Shipments Originating in Puerto Rico'),
			('Shipments Originating in Canada'),
			('Shipments Originating in Mexico'),
			('Polish Domestic Shipments'),
			('Shipments Originating in the European Union'),
			('Shipments Originating in Other Countries');";

	// Create Service Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ups_rate_service_code` (
			  `id_ups_rate_service_code` int(10) NOT NULL AUTO_INCREMENT,
			  `id_ups_rate_service_group` int(10) NOT NULL,
			  `id_carrier` int(10) NOT NULL,
			  `id_carrier_history` text NOT NULL,
			  `code` varchar(16) NOT NULL,
			  `service` varchar(255) NOT NULL,
			  `active` tinyint(1) NOT NULL,
			  UNIQUE(`id_ups_rate_service_group`, `code`, `service`),
			  PRIMARY KEY  (`id_ups_rate_service_code`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

	// Insert Service in database
	$sql[] = "INSERT INTO `"._DB_PREFIX_."ups_rate_service_code` (`id_carrier`, `id_carrier_history`, `id_ups_rate_service_group`, `code`, `service`, `active`) VALUES
			('0', '', '1', '01', 'UPS Next Day Air', '0'),
			('0', '', '1', '02', 'UPS Second Day Air', '0'),
			('0', '', '1', '03', 'UPS Ground', '0'),
			('0', '', '1', '12', 'UPS Three-Day Select', '0'),
			('0', '', '1', '13', 'UPS Next Day Air Saver', '0'),
			('0', '', '1', '14', 'UPS Next Day Air Early A.M.', '0'),
			('0', '', '1', '59', 'UPS Second Day Air A.M.', '0'),
			('0', '', '1', '65', 'UPS Saver', '0'),
			('0', '', '2', '01', 'UPS Next Day Air', '0'),
			('0', '', '2', '02', 'UPS Second Day Air', '0'),
			('0', '', '2', '03', 'UPS Ground', '0'),
			('0', '', '2', '07', 'UPS Worldwide Express', '0'),
			('0', '', '2', '08', 'UPS Worldwide Expedited', '0'),
			('0', '', '2', '11', 'UPS Standard', '0'),
			('0', '', '2', '12', 'UPS Three-Day Select', '0'),
			('0', '', '2', '14', 'UPS Next Day Air Early A.M.', '0'),
			('0', '', '2', '54', 'UPS Worldwide Express Plus', '0'),
			('0', '', '2', '59', 'UPS Second Day Air A.M.', '0'),
			('0', '', '2', '65', 'UPS Saver', '0'),
			('0', '', '3', '01', 'UPS Next Day Air', '0'),
			('0', '', '3', '02', 'UPS Second Day Air', '0'),
			('0', '', '3', '03', 'UPS Ground', '0'),
			('0', '', '3', '07', 'UPS Worldwide Express', '0'),
			('0', '', '3', '08', 'UPS Worldwide Expedited', '0'),
			('0', '', '3', '14', 'UPS Next Day Air Early A.M.', '0'),
			('0', '', '3', '54', 'UPS Worldwide Express Plus', '0'),
			('0', '', '3', '65', 'UPS Saver', '0'),
			('0', '', '4', '01', 'UPS Express', '0'),
			('0', '', '4', '02', 'UPS Expedited', '0'),
			('0', '', '4', '07', 'UPS Worldwide Express', '0'),
			('0', '', '4', '08', 'UPS Worldwide Expedited', '0'),
			('0', '', '4', '11', 'UPS Standard', '0'),
			('0', '', '4', '12', 'UPS Three-Day Select', '0'),
			('0', '', '4', '13', 'UPS Saver', '0'),
			('0', '', '4', '14', 'UPS Express Early A.M.', '0'),
			('0', '', '4', '54', 'UPS Worldwide Express Plus', '0'),
			('0', '', '4', '65', 'UPS Saver', '0'),
			('0', '', '5', '07', 'UPS Express', '0'),
			('0', '', '5', '08', 'UPS Expedited', '0'),
			('0', '', '5', '54', 'UPS Express Plus', '0'),
			('0', '', '5', '65', 'UPS Saver', '0'),
			('0', '', '6', '07', 'UPS Express', '0'),
			('0', '', '6', '08', 'UPS Expedited', '0'),
			('0', '', '6', '11', 'UPS Standard', '0'),
			('0', '', '6', '54', 'UPS Worldwide Express Plus', '0'),
			('0', '', '6', '65', 'UPS Saver', '0'),
			('0', '', '6', '82', 'UPS Today Standard', '0'),
			('0', '', '6', '83', 'UPS Today Dedicated Courrier', '0'),
			('0', '', '6', '84', 'UPS Today Intercity', '0'),
			('0', '', '6', '85', 'UPS Today Express', '0'),
			('0', '', '6', '86', 'UPS Today Express Saver', '0'),
			('0', '', '7', '07', 'UPS Express', '0'),
			('0', '', '7', '08', 'UPS Expedited', '0'),
			('0', '', '7', '11', 'UPS Standard', '0'),
			('0', '', '7', '54', 'UPS Worldwide Express Plus', '0'),
			('0', '', '7', '65', 'UPS Saver', '0'),
			('0', '', '8', '07', 'UPS Express', '0'),
			('0', '', '8', '08', 'UPS Worldwide Expedited', '0'),
			('0', '', '8', '11', 'UPS Standard', '0'),
			('0', '', '8', '54', 'UPS Worldwide Express Plus', '0'),
			('0', '', '8', '65', 'UPS', '0');";

	// Create Cache Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ups_cache` (
			  `id_ups_cache` int(10) NOT NULL AUTO_INCREMENT,
			  `id_cart` int(10) NOT NULL,
			  `id_carrier` int(10) NOT NULL,
			  `hash` varchar(32) NOT NULL,
			  `id_currency` int(10) NOT NULL,
			  `total_charges` double(10,2) NOT NULL,
			  `is_available` tinyint(1) NOT NULL,
			  `date_add` datetime NOT NULL,
			  `date_upd` datetime NOT NULL,
			  PRIMARY KEY  (`id_ups_cache`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

	// Create Test Cache Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ups_cache_test` (
			`id_ups_cache_test` int(10) NOT NULL AUTO_INCREMENT,
			`hash` varchar(1024) NOT NULL,
			`result` text NOT NULL,
			`date_add` datetime NOT NULL,
			`date_upd` datetime NOT NULL,
			PRIMARY KEY  (`id_ups_cache_test`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

	// Create Config Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ups_rate_config` (
			`id_ups_rate_config` int(10) NOT NULL AUTO_INCREMENT,
			`id_product` int(10) NOT NULL,
			`id_category` int(10) NOT NULL,
			`id_currency` int(10) NOT NULL,
			`packaging_type_code` varchar(64) NOT NULL,
			`additionnal_charges` double(6,2) NOT NULL,
			`date_add` datetime NOT NULL,
			`date_upd` datetime NOT NULL,
			PRIMARY KEY  (`id_ups_rate_config`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

	// Create Config (Service) Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ups_rate_config_service` (
			`id_ups_rate_config_service` int(10) NOT NULL AUTO_INCREMENT,
			`id_ups_rate_service_code` int(10) NOT NULL,
			`id_ups_rate_config` int(10) NOT NULL,
			`date_add` datetime NOT NULL,
			`date_upd` datetime NOT NULL,
			PRIMARY KEY  (`id_ups_rate_config_service`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

?>
