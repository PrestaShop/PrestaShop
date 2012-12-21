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

function add_column_order_state_deleted_if_not_exists()
{
	$res  = true;
	$column = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'order_state` LIKE "deleted"');

	if (empty($column))
		$res = Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'order_state` 
			ADD COLUMN `deleted` tinyint(1) UNSIGNED NOT NULL default "0" AFTER `paid`');
	if (!$res)
		return array('error' => Db::getInstance()->getNumberError(), 'msg' => Db::getInstance()->getMsgError());
	return true;
}
