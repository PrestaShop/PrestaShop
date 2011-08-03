<?php

	// Init
	$sql = array();
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'ebay_category`;';	
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'ebay_category_configuration`;';
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'ebay_product`;';	
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'ebay_order`;';
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'ebay_sync_history`;';
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'ebay_sync_history_product`;';


