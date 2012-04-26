<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'shipwire_stock` (
		`id_stock` int(10) unsigned NOT NULL,
		`id_shop` int(10) unsigned NOT NULL,
		`id_group_shop` int(10) unsigned NOT NULL,
		`code` varchar(255) NULL,
		`quantity` int(10) unsigned default 0,
		`good` int(10) unsigned default 0,
		`pending` int(10) unsigned default 0,
		`backordered` int(10) unsigned default 0,
		`reserved` int(10) unsigned default 0,
		`shipping` int(10) unsigned default 0,
		`shipped` int(10) unsigned default 0,
		`consuming` int(10) unsigned default 0,
		`consumed` int(10) unsigned default 0,
		`creating` int(10) unsigned default 0,
		`created` int(10) unsigned default 0,
		`available_date` date,
		`shipped_last_day` int(10) unsigned default 0,
		`shipped_last_week` int(10) unsigned default 0,
		`shipped_last_4_weeks` int(10) unsigned default 0,
		`ordered_last_day` int(10) unsigned default 0,
		`ordered_last_week` int(10) unsigned default 0,
		`ordered_last_4_weeks` int(10) unsigned default 0,
		PRIMARY KEY (`id_stock`, `id_shop`),
		UNIQUE (`id_stock`))
		ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'shipwire_order` (
		`id_order` int(10) unsigned NOT NULL,
		`id_shop` int(10) unsigned NOT NULL,
		`id_group_shop` int(10) unsigned NOT NULL,
		`transaction_ref` varchar(255) NULL,
		`order_ref` varchar(255) NULL,
		`tracking_number` varchar(35) NULL,
		`status` varchar(32) NULL,
		`shipped` varchar(32) NULL,
		`shipper` varchar(255) NULL,
		`shipDate` varchar(32) NULL,
		`expectedDeliveryDate` varchar(32) NULL,
		`href` varchar(255) NULL,
		`shipperFullName` varchar(255) NULL,
		PRIMARY KEY (`id_order`, `id_shop`),
		UNIQUE (`id_order`))
		ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'shipwire_log` (
		`id_order` int(10) unsigned NOT NULL,
		`id_shop` int(10) unsigned NOT NULL,
		`id_group_shop` int(10) unsigned NOT NULL,
		`transaction_ref` varchar(255) NULL,
		`date_added` datetime,
		PRIMARY KEY (`id_order`, `id_shop`),
		UNIQUE (`id_order`, `transaction_ref`))
		ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';