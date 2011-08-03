<?php

	// Init
	$sql = array();

	// Create Order Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_order` (
			  `id_ebay_order` int(16) NOT NULL AUTO_INCREMENT,
			  `id_order_ref` varchar(128) NOT NULL,
			  `id_order` int(16) NOT NULL,
			  UNIQUE(`id_order_ref`),
			  PRIMARY KEY  (`id_ebay_order`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

	// Create Sync History Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_sync_history` (
			  `id_ebay_sync_history` int(16) NOT NULL AUTO_INCREMENT,
			  `is_manual` tinyint(1) NOT NULL,
			  `datetime` datetime NOT NULL,
			  PRIMARY KEY  (`id_ebay_sync_history`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


	// Create Sync History Product Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_sync_history_product` (
			  `id_ebay_sync_history_product` int(16) NOT NULL AUTO_INCREMENT,
			  `id_ebay_sync_history` int(16),
			  `id_product` int(16),
			  KEY (`id_ebay_sync_history`),
			  PRIMARY KEY  (`id_ebay_sync_history_product`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

