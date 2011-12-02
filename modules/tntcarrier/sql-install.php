<?php

	// Init
	$sql = array();
		
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'tnt_carrier_option` (
			  `id_option` int(10) NOT NULL AUTO_INCREMENT,
			  `option` varchar(5) DEFAULT NULL,
			  `id_carrier` int(10) DEFAULT NULL,
			  `additionnal_charges` double(6,2) DEFAULT NULL,
			  PRIMARY KEY  (`id_option`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
	
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'tnt_carrier_cache_service` (
				`id_card` int(11) NOT NULL,
				`code` varchar(5) NOT NULL,
				`date` datetime NOT NULL,
				`zipcode` varchar(10) DEFAULT NULL
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
	
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'tnt_carrier_drop_off` (
			  `id_cart` int(10) NOT NULL,
			  `code` varchar(10) DEFAULT NULL,
			  `name` text DEFAULT NULL,
			  `address` text DEFAULT NULL,
			  `zipcode` varchar(10) DEFAULT NULL,
			  `city` text DEFAULT NULL
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
		
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'tnt_carrier_shipping_number` (
				`id_order` int(10) NOT NULL,
				`shipping_number` varchar(32) NOT NULL
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
	
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'tnt_carrier_weight` (
			  `id_weight` int(10) NOT NULL AUTO_INCREMENT,
			  `weight_min` double(6,2) DEFAULT NULL,
			  `weight_max` double(6,2) DEFAULT NULL,
			  `additionnal_charges` double(6,2) DEFAULT NULL,
			  PRIMARY KEY  (`id_weight`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
?>
