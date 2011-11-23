<?php
/*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function add_order_carrier()
{
	$orders = Db::getInstance()->executeS('
	SELECT DISTINCT o.`id_order`, o.`shipping_number`, o.`id_carrier`,  od.`id_order_invoice`, od.`product_weight`, o.`total_shipping_tax_incl`, o.`total_shipping_tax_excl`, o.`date_add`
	FROM `'._DB_PREFIX_.'orders` o
	LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON (od.`id_order` = o.`id_order`)
	WHERE 1');
	if (count($orders) && is_array($orders))
	{
		$i = 0;
		$sql = 'INSERT INTO `'._DB_PREFIX_.'order_carrier` (`id_order`, `id_carrier`, `id_order_invoice`, `weight`, `shipping_cost_tax_excl`, `shipping_cost_tax_incl`, `date_add`) VALUES ';
		foreach ($orders as $order)
			$sql .= '('.(int)$order['id_order'].', '.(int)$order['id_carrier'].', '.(int)$order['id_order_invoice'].', '.(float)$order['product_weight'].', '.(float)$order['total_shipping_tax_excl'].', '.(float)$order['total_shipping_tax_incl'].', "'.pSQL($order['date_add']).'"),';
		// removing last comma to avoid SQL error
		$sql = substr($sql, 0, strlen($sql) - 1);
		Db::getInstance()->execute($sql);
	}
}