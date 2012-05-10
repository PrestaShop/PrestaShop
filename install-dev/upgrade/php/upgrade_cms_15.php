<?php

/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
function upgrade_cms_15()
{
	$res = true;

	// note : cms_shop table is required and independant of blockcms module
	$res &= Db::getInstance()->execute('CREATE TABLE `'._DB_PREFIX_.'_cms_shop` (
`id_cms` INT( 11 ) UNSIGNED NOT NULL,
`id_shop` INT( 11 ) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id_cms`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;');
	$res &= Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'_cms_shop` (id_shop, id_cms) (SELECT 1, id_cms FROM '._DB_PREFIX_.'_cms)');
	
	// cms_block table is blockcms module dependant. Don't update table that does not exists
	$id_module_cms = Db::getInstance()->getValue('SELECT id_module from `'._DB_PREFIX_.'_module where name="blockcms"`');
	if (!$id_module_cms)
		return $res;
	$res &= Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'._cms_block` ADD `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT "1" AFTER `id_cms_block`');

	return $res;
}
