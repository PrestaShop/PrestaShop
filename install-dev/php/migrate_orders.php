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

function migrate_orders()
{
	if (!defined('PS_TAX_EXC'))
		define('PS_TAX_EXC', 1);

	if (!defined('PS_TAX_INC'))
		define('PS_TAX_INC', 0);

	$sql = 'SELECT *
			FROM `'._DB_PREFIX_.'orders`';

	$orders = Db::getInstance()->executeS($sql);

	foreach ($orders as $order)
	{
		$sum_total_products = 0;
		$sum_tax_amount = 0;
		$default_group_id = getCustomerDefaultGroup((int)$order['id_customer']);
		$price_display_method = getPriceDisplayMethod((int)$default_group_id);

		$order_details_list = Db::getInstance()->executeS('
						SELECT *
						FROM `'._DB_PREFIX_.'order_detail` od
						LEFT JOIN `'._DB_PREFIX_.'product` p
						ON p.id_product = od.product_id
						WHERE od.`id_order` = '.(int)($order['id_order']));

		foreach ($order_details_list as $order_details)
		{
			// we don't want to erase order_details data in order to create the insert query
			$products = setProductPrices($order_details, $price_display_method);

			$tax_rate = 1 + ((float)$products['tax_rate'] / 100);
			$reduction_amount_tax_incl = (float)$products['reduction_amount'];

			// cart::getTaxesAverageUsed equivalent
			$sum_total_products += $products['total_wt'];
			$sum_tax_amount += $products['total_wt'] - $products['total_price'];

			$sql = 'UPDATE `'._DB_PREFIX_.'order_detail`
					SET `reduction_amount_tax_incl` = '.$reduction_amount_tax_incl.',
						`reduction_amount_tax_excl` = '.(float)Tools::ps_round($reduction_amount_tax_incl / $tax_rate, 2).',
						`total_price_tax_incl` = '.(float)$products['total_wt'].',
						`total_price_tax_excl` = '.(float)$products['total_price'].',
						`unit_price_tax_incl` = '.(float)$products['product_price_wt'].',
						`unit_price_tax_excl` = '.(float)$products['product_price'].'
					WHERE `id_order_detail` = '.(int)$products['id_order_detail'];

			Db::getInstance()->execute($sql);
		}

		$average_tax_used = 1;
		if ($sum_total_products > 0)
			$average_tax_used +=  ($sum_tax_amount / $sum_total_products) * 0.01;

		// this was done like that previously
		$wrapping_tax_rate = 1 + ((float)Configuration::get('PS_GIFT_WRAPPING_TAX') / 100);
		$carrier_tax_rate = 1 + ((float)$order['carrier_tax_rate'] / 100);

		$total_discount_tax_excl = $order['total_discounts'] / $average_tax_used;

		$sql = 'UPDATE `'._DB_PREFIX_.'orders`
				SET `total_discount_tax_incl` = '.(float)$order['total_discounts'].',
					`total_discount_tax_excl` = '.(float)$total_discount_tax_excl.',
					`total_paid_tax_incl` = '.(float)$order['total_paid'].',
					`total_paid_tax_excl` = '.(float)$order['total_paid'].',
					`total_shipping_tax_incl` = '.(float)$order['total_shipping'].',
					`total_shipping_tax_excl` = '.(float)($order['total_shipping'] / $carrier_tax_rate).',
					`total_wrapping_tax_incl` = '.(float)$order['total_wrapping'].',
					`total_wrapping_tax_excl` = '.((float)$order['total_wrapping'] / $wrapping_tax_rate).'
				WHERE `id_order` = '.(int)$order['id_order'];

		Db::getInstance()->execute($sql);

		unset($order);
	}
}

function getCustomerDefaultGroup($id_customer)
{
    Db::getInstance()->getValue('SELECT `id_default_group` FROM `'._DB_PREFIX_.'customer` WHERE `id_customer` = '.(int)$id_customer);
}

function getPriceDisplayMethod($id_group)
{
    return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `price_display_method`
			FROM `'._DB_PREFIX_.'group`
			WHERE `id_group` = '.(int)$id_group);
}

function setProductPrices($row, $tax_calculation_method)
{
    if ($tax_calculation_method == PS_TAX_EXC)
        $row['product_price'] = Tools::ps_round($row['product_price'], 2);
    else
        $row['product_price_wt'] = Tools::ps_round($row['product_price'] * (1 + $row['tax_rate'] / 100), 2);

    $group_reduction = 1;
    if ($row['group_reduction'] > 0)
        $group_reduction =  1 - $row['group_reduction'] / 100;

    if ($row['reduction_percent'] != 0)
    {
        if ($tax_calculation_method == PS_TAX_EXC)
            $row['product_price'] = ($row['product_price'] - $row['product_price'] * ($row['reduction_percent'] * 0.01));
        else
        {
            $reduction = Tools::ps_round($row['product_price_wt'] * ($row['reduction_percent'] * 0.01), 2);
            $row['product_price_wt'] = Tools::ps_round(($row['product_price_wt'] - $reduction), 2);
        }
    }

    if ($row['reduction_amount'] != 0)
    {
        if ($tax_calculation_method == PS_TAX_EXC)
            $row['product_price'] = ($row['product_price'] - ($row['reduction_amount'] / (1 + $row['tax_rate'] / 100)));
        else
            $row['product_price_wt'] = Tools::ps_round(($row['product_price_wt'] - $row['reduction_amount']), 2);
    }

    if ($row['group_reduction'] > 0)
    {
        if ($tax_calculation_method == PS_TAX_EXC)
            $row['product_price'] = $row['product_price'] * $group_reduction;
        else
            $row['product_price_wt'] = Tools::ps_round($row['product_price_wt'] * $group_reduction , 2);
    }

    if (($row['reduction_percent'] OR $row['reduction_amount'] OR $row['group_reduction']) AND $tax_calculation_method == PS_TAX_EXC)
        $row['product_price'] = Tools::ps_round($row['product_price'], 2);

    if ($tax_calculation_method == PS_TAX_EXC)
        $row['product_price_wt'] = Tools::ps_round($row['product_price'] * (1 + ($row['tax_rate'] * 0.01)), 2) + Tools::ps_round($row['ecotax'] * (1 + $row['ecotax_tax_rate'] / 100), 2);
    else
    {
        $row['product_price_wt_but_ecotax'] = $row['product_price_wt'];
        $row['product_price_wt'] = Tools::ps_round($row['product_price_wt'] + $row['ecotax'] * (1 + $row['ecotax_tax_rate'] / 100), 2);
    }

    $row['total_wt'] = $row['product_quantity'] * $row['product_price_wt'];
    $row['total_price'] = $row['product_quantity'] * $row['product_price'];

    return $row;
}


