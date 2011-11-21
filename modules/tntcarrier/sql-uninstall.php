<?php

	// Init
	$sql = array();
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'tnt_carrier_option`;';
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'tnt_carrier_weight`;';
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'tnt_carrier_drop_off`;';
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'tnt_carrier_shipping_number`;';
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'tnt_carrier_cache_service`;';

?>
