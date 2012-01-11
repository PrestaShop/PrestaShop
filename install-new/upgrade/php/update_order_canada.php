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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function update_order_canada()
	{
	$sql ='SHOW TABLES LIKE \''._DB_PREFIX_.'order_tax\'';
	$table = Db::getInstance()->ExecuteS($sql);

	if (!count($table))
	{
		Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'order_tax` (
		  `id_order` int(11) NOT NULL,
		  `tax_name` varchar(40) NOT NULL,
		  `tax_rate` decimal(6,3) NOT NULL,
		  `amount` decimal(20,6) NOT NULL
		) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8');


		$address_field = Configuration::get('PS_TAX_ADDRESS_TYPE');
		$sql = 'SELECT `id_order`
					FROM `'._DB_PREFIX_.'orders` o
					LEFT JOIN `'._DB_PREFIX_.'address` a ON (a.`id_address` = o.`'.bqSQL($address_field).'`)
					LEFT JOIN `'._DB_PREFIX_.'country` c ON (c.`id_country` = a.`id_country`)
					WHERE c.`iso_code` = "CA"';

		$id_order_list = Db::getInstance()->ExecuteS($sql);

		$values = '';
		foreach ($id_order_list as $id_order)
		{
			$amount = array();
			$id_order = $id_order['id_order'];
			$order = new Order((int)$id_order);
			if (!Validate::isLoadedObject($order))
				continue;

			$products = $order->getProducts();
			foreach ($products as $product)
			{
				if (!array_key_exists($product['tax_name'], $amount))
					$amount[$product['tax_name']] = array('amount' => 0, 'rate' => $product['tax_rate']);

				if ($order->getTaxCalculationMethod() == PS_TAX_EXC)
				{
					$total_product = $product['product_price'] * $product['product_quantity'];
					$amount_tmp = Tools::ps_round($total_product * ($product['tax_rate'] / 100), 2);
					$amount[$product['tax_name']]['amount'] += Tools::ps_round($total_product * ($product['tax_rate'] / 100), 2);
				}
				else
				{
					$total_product = $product['product_price'] * $product['product_quantity'];
					$amount_tmp = Tools::ps_round($total_product - ($total_product / (1 + ($product['tax_rate'] / 100))), 2);
					$amount[$product['tax_name']]['amount'] += Tools::ps_round($total_product - ($total_product / (1 + ($product['tax_rate'] / 100))), 2);
				}
			}

			foreach ($amount as $tax_name => $tax_infos)
					$values .= '('.(int)$order->id.', \''.pSQL($tax_name).'\', \''.pSQL($tax_infos['rate']).'\', '.(float)$tax_infos['amount'].'),';
			unset($order);
		}

		if (!empty($values))
		{
			$values = rtrim($values, ",");

			Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'order_tax` (id_order, tax_name, tax_rate, amount)
			VALUES '.$values);
		}
	}
}

