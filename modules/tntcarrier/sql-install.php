<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/


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
