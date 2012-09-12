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
*  @version  Release: $Revision: 13573 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function p15017_add_id_shop_to_primary_key()
{
	// The former primary keys where set on id_object and id_lang. They must now be set on id_shop too.
	foreach (array('product', 'category', 'meta', 'carrier') as $table)
	{
		Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.$table.'_lang` DROP PRIMARY KEY');
		Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.$table.'_lang` ADD PRIMARY KEY (`id_'.$table.'`, `id_shop`, `id_lang`)');
	}
	
	return true;
}
