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

function migrate_orders()
{
	$res = true;
	if (!defined('PS_TAX_EXC'))
		 define('PS_TAX_EXC', 1);

	if (!defined('PS_TAX_INC'))
		 define('PS_TAX_INC', 0);

	$values_order_detail = array();
	// init insert order detail query
	$insert_order_detail = 'INSERT INTO `'._DB_PREFIX_.'order_detail_2`
	(`id_order_detail`, `id_order`, `id_order_invoice`, `id_warehouse`, `product_id`, `product_attribute_id`, 
		`product_name`, `product_quantity`, `product_quantity_in_stock`, `product_quantity_refunded`, `product_quantity_return`, 
		`product_quantity_reinjected`, `product_price`, `reduction_percent`, `reduction_amount`, `reduction_amount_tax_incl`, 
		`reduction_amount_tax_excl`, `group_reduction`, `product_quantity_discount`, `product_ean13`, `product_upc`, `product_reference`, 
		`product_supplier_reference`, `product_weight`, `tax_computation_method`, `tax_name`, `tax_rate`, `ecotax`, 
		`ecotax_tax_rate`, `discount_quantity_applied`, `download_hash`, `download_nb`, `download_deadline`, 
		`total_price_tax_incl`, `total_price_tax_excl`, `unit_price_tax_incl`, `unit_price_tax_excl`, 
		`total_shipping_price_tax_incl`, `total_shipping_price_tax_excl`, `purchase_supplier_price`, 
		`original_product_price`)	VALUES ';

	$values_order = array();
	// init insert order query
	$insert_order = 'INSERT INTO `'._DB_PREFIX_.'orders_2` (`id_order`, `reference`, `id_group_shop`, `id_shop`, `id_carrier`,
	`id_lang`, `id_customer`, `id_cart`, `id_currency`, `id_address_delivery`, `id_address_invoice`, `secure_key`, `payment`, 
	`conversion_rate`, `module`, `recyclable`, `gift`, `gift_message`, `shipping_number`, `total_discounts`, 
	`total_discounts_tax_incl`, `total_discounts_tax_excl`, `total_paid`, `total_paid_tax_incl`, `total_paid_tax_excl`,
	`total_paid_real`, `total_products`, `total_products_wt`, `total_shipping`, `total_shipping_tax_incl`, `total_shipping_tax_excl`,
	`carrier_tax_rate`, `total_wrapping`, `total_wrapping_tax_incl`, `total_wrapping_tax_excl`, `invoice_number`, `delivery_number`,
	`invoice_date`, `delivery_date`, `valid`, `date_add`, `date_upd`) VALUES ';

	// create temporary tables
	$res = mo_duplicateTables();
	if (!$res)
		return array('error' => true, 'msg' => 'unable to duplicate tables orders and order_detail');

	$order_res = Db::getInstance()->query(
			'SELECT *
			FROM `'._DB_PREFIX_.'orders`');

	$cpt = 0;
	$flush_limit = 1000;
	while ($order = Db::getInstance()->nextRow($order_res))
	{
		 $sum_total_products = 0;
		 $sum_tax_amount = 0;
		 $default_group_id = mo_getCustomerDefaultGroup((int)$order['id_customer']);
		 $price_display_method = mo_getPriceDisplayMethod((int)$default_group_id);

		 $order_details_list = Db::getInstance()->executeS('
		                 SELECT *
		                 FROM `'._DB_PREFIX_.'order_detail` od
		                 LEFT JOIN `'._DB_PREFIX_.'product` p
		                 ON p.id_product = od.product_id
		                 WHERE od.`id_order` = '.(int)($order['id_order']));

		 foreach ($order_details_list as $order_details)
		 {
		     // we don't want to erase order_details data in order to create the insert query
				$products = mo_setProductPrices($order_details, $price_display_method);
				$tax_rate = 1 + ((float)$products['tax_rate'] / 100);
				$reduction_amount_tax_incl = (float)$products['reduction_amount'];

				// cart::getTaxesAverageUsed equivalent
				$sum_total_products += $products['total_wt'];
				$sum_tax_amount += $products['total_wt'] - $products['total_price'];

				$order_details['reduction_amount_tax_incl']= $reduction_amount_tax_incl;
				$order_details['reduction_amount_tax_excl']= (float)mo_ps_round($reduction_amount_tax_incl / $tax_rate);
				$order_details['total_price_tax_incl']= (float)$products['total_wt'];
				$order_details['total_price_tax_excl']= (float)$products['total_price'];
				$order_details['unit_price_tax_incl']= (float)$products['product_price_wt'];
				$order_details['unit_price_tax_excl']= (float)$products['product_price'];
				$values_order_detail[] = '(\''.$order_details['id_order_detail'].'\', \''.$order_details['id_order'].'\', \''.$order_details['id_order_invoice'].'\', \''.$order_details['id_warehouse'].'\', \''.$order_details['product_id'].'\', \''.$order_details['product_attribute_id'].'\', \''.$order_details['product_name'].'\', \''.$order_details['product_quantity'].'\', \''.$order_details['product_quantity_in_stock'].'\', \''.$order_details['product_quantity_refunded'].'\', \''.$order_details['product_quantity_return'].'\', \''.$order_details['product_quantity_reinjected'].'\', \''.$order_details['product_price'].'\', \''.$order_details['reduction_percent'].'\', \''.$order_details['reduction_amount'].'\', \''.$order_details['reduction_amount_tax_incl'].'\', \''.$order_details['reduction_amount_tax_excl'].'\', \''.$order_details['group_reduction'].'\', \''.$order_details['product_quantity_discount'].'\', \''.$order_details['product_ean13'].'\', \''.$order_details['product_upc'].'\', \''.$order_details['product_reference'].'\', \''.$order_details['product_supplier_reference'].'\', \''.$order_details['product_weight'].'\', \''.$order_details['tax_computation_method'].'\', \''.$order_details['tax_name'].'\', \''.$order_details['tax_rate'].'\', \''.$order_details['ecotax'].'\', \''.$order_details['ecotax_tax_rate'].'\', \''.$order_details['discount_quantity_applied'].'\', \''.$order_details['download_hash'].'\', \''.$order_details['download_nb'].'\', \''.$order_details['download_deadline'].'\', \''.$order_details['total_price_tax_incl'].'\', \''.$order_details['total_price_tax_excl'].'\', \''.$order_details['unit_price_tax_incl'].'\', \''.$order_details['unit_price_tax_excl'].'\', \''.$order_details['total_shipping_price_tax_incl'].'\', \''.$order_details['total_shipping_price_tax_excl'].'\', \''.$order_details['purchase_supplier_price'].'\', \''.$order_details['original_product_price'].'\')';
		 }

		 $average_tax_used = 1;
		 if ($sum_total_products > 0)
		     $average_tax_used +=  ($sum_tax_amount / $sum_total_products) * 0.01;

		 // this was done like that previously
		  $wrapping_tax_rate = 1 + ((float)Db::getInstance()->getValue('SELECT value 
				FROM `'._DB_PREFIX_.'configuration`
				WHERE name = "PS_GIFT_WRAPPING_TAX"') / 100);
		 $carrier_tax_rate = 1 + ((float)$order['carrier_tax_rate'] / 100);

		 $total_discount_tax_excl = $order['total_discounts'] / $average_tax_used;

		$order['total_discounts_tax_incl'] = (float)$order['total_discounts'];
		$order['total_discounts_tax_excl'] = (float)$total_discount_tax_excl;
		$order['total_paid_tax_incl'] = (float)$order['total_paid'];
		$order['total_paid_tax_excl'] = (float)$order['total_paid'];
		$order['total_shipping_tax_incl'] = (float)$order['total_shipping'];
		$order['total_shipping_tax_excl'] = (float)($order['total_shipping'] / $carrier_tax_rate);
		$order['total_wrapping_tax_incl'] = (float)$order['total_wrapping'];
		$order['total_wrapping_tax_excl'] = ((float)$order['total_wrapping'] / $wrapping_tax_rate);
		$values_order[] = '(\''.$order['id_order'].'\', \''.$order['reference'].'\', \''.$order['id_group_shop'].'\', \''.$order['id_shop'].'\', \''.$order['id_carrier'].'\', \''.$order['id_lang'].'\', \''.$order['id_customer'].'\', \''.$order['id_cart'].'\', \''.$order['id_currency'].'\', \''.$order['id_address_delivery'].'\', \''.$order['id_address_invoice'].'\', \''.$order['secure_key'].'\', \''.$order['payment'].'\', \''.$order['conversion_rate'].'\', \''.$order['module'].'\', \''.$order['recyclable'].'\', \''.$order['gift'].'\', \''.$order['gift_message'].'\', \''.$order['shipping_number'].'\', \''.$order['total_discounts'].'\', \''.$order['total_discounts_tax_incl'].'\', \''.$order['total_discounts_tax_excl'].'\', \''.$order['total_paid'].'\', \''.$order['total_paid_tax_incl'].'\', \''.$order['total_paid_tax_excl'].'\', \''.$order['total_paid_real'].'\', \''.$order['total_products'].'\', \''.$order['total_products_wt'].'\', \''.$order['total_shipping'].'\', \''.$order['total_shipping_tax_incl'].'\', \''.$order['total_shipping_tax_excl'].'\', \''.$order['carrier_tax_rate'].'\', \''.$order['total_wrapping'].'\', \''.$order['total_wrapping_tax_incl'].'\', \''.$order['total_wrapping_tax_excl'].'\', \''.$order['invoice_number'].'\', \''.$order['delivery_number'].'\', \''.$order['invoice_date'].'\', \''.$order['delivery_date'].'\', \''.$order['valid'].'\', \''.$order['date_add'].'\', \''.$order['date_upd'].'\')';

		unset($order);
		$cpt++;

		if ($cpt >= $flush_limit)
		{
			$cpt = 0;
			$res &= Db::getInstance()->execute($insert_order_detail. implode(',', $values_order_detail));
			$res &= Db::getInstance()->execute($insert_order. implode(',', $values_order));
			if (!$res)
				return array('error' => true, 'msg' => 'error on insertion in temporary table order_detail / orders ');
			$values_order = array();
			$values_order_detail = array();
		}
	}

	if ($cpt> 0)
	{
		$res &= Db::getInstance()->execute($insert_order_detail. implode(',', $values_order_detail));
		$res &= Db::getInstance()->execute($insert_order. implode(',', $values_order));
		if (!$res)
			return array('error' => true, 'msg' => 'error on last insertion in temporary table order_detail / orders ');
	}

	$res &= mo_renameTables();
	if (!$res)
		return array('error' => true, 'msg' => 'unable to rename tables orders_2 and order_detail_2 to orders_2 and order_detail');

}


/**
 * mo_ps_round is a simplification of Tools::ps_round:
 * - round is always 2
 * - no call to Configuration class
 * 
 * @param mixed $val 
 * @return void
 */
function mo_ps_round($val)
{
	static $ps_price_round_mode;
	if (empty($ps_price_round_mode))
	{
		$ps_price_round_mode = Db::getInstance()->getValue('SELECT value 
			FROM `'._DB_PREFIX_.'configuration`
			WHERE name = "PS_PRICE_ROUND_MODE"');
	}

	switch ($ps_price_round_mode)
	{
		case 0:
			return ceil($val * 100)/100;
		case 1:
			return floor($val * 100)/100;
		default:
			return round($val, 2);
	}
}

function mo_duplicateTables()
{
	$res = true;
	$res &= Db::getInstance()->execute('CREATE TABLE 
		`'._DB_PREFIX_.'orders_2` LIKE `'._DB_PREFIX_.'orders`');
	$res &= Db::getInstance()->execute('CREATE TABLE 
		`'._DB_PREFIX_.'order_detail_2` LIKE `'._DB_PREFIX_.'order_detail`');
	return $res;
}

function mo_renameTables()
{
	$res = true;
	$res &= Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'orders`');
	$res &= Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'order_detail`');

	$res &= Db::getInstance()->execute('RENAME TABLE `'._DB_PREFIX_.'orders_2` TO `'._DB_PREFIX_.'orders`');
	$res &= Db::getInstance()->execute('RENAME TABLE `'._DB_PREFIX_.'order_detail_2` TO `'._DB_PREFIX_.'order_detail`');
	return $res;
}

function mo_getCustomerDefaultGroup($id_customer)
{
	static $cache;
	if (!isset($cache[$id_customer]))
		$cache[$id_customer] = Db::getInstance()->getValue('SELECT `id_default_group` FROM `'._DB_PREFIX_.'customer` WHERE `id_customer` = '.(int)$id_customer);

	return $cache[$id_customer];
}

function mo_getPriceDisplayMethod($id_group)
{
	static $cache;

	if (!isset($cache[$id_group]))
    $cache[$id_group] = Db::getInstance()->getValue('
			SELECT `price_display_method`
			FROM `'._DB_PREFIX_.'group`
			WHERE `id_group` = '.(int)$id_group);

	return $cache[$id_group];
}

function mo_setProductPrices($row, $tax_calculation_method)
{
    if ($tax_calculation_method == PS_TAX_EXC)
        $row['product_price'] = mo_ps_round($row['product_price']);
    else
        $row['product_price_wt'] = mo_ps_round($row['product_price'] * (1 + $row['tax_rate'] / 100));

    $group_reduction = 1;
    if ($row['group_reduction'] > 0)
        $group_reduction =  1 - $row['group_reduction'] / 100;

    if ($row['reduction_percent'] != 0)
    {
        if ($tax_calculation_method == PS_TAX_EXC)
            $row['product_price'] = ($row['product_price'] - $row['product_price'] * ($row['reduction_percent'] * 0.01));
        else
        {
            $reduction = mo_ps_round($row['product_price_wt'] * ($row['reduction_percent'] * 0.01));
            $row['product_price_wt'] = mo_ps_round(($row['product_price_wt'] - $reduction));
        }
    }

    if ($row['reduction_amount'] != 0)
    {
        if ($tax_calculation_method == PS_TAX_EXC)
            $row['product_price'] = ($row['product_price'] - ($row['reduction_amount'] / (1 + $row['tax_rate'] / 100)));
        else
            $row['product_price_wt'] = mo_ps_round(($row['product_price_wt'] - $row['reduction_amount']));
    }

    if ($row['group_reduction'] > 0)
    {
        if ($tax_calculation_method == PS_TAX_EXC)
            $row['product_price'] = $row['product_price'] * $group_reduction;
        else
            $row['product_price_wt'] = mo_ps_round($row['product_price_wt'] * $group_reduction);
    }

    if (($row['reduction_percent'] OR $row['reduction_amount'] OR $row['group_reduction']) AND $tax_calculation_method == PS_TAX_EXC)
        $row['product_price'] = mo_ps_round($row['product_price']);

    if ($tax_calculation_method == PS_TAX_EXC)
        $row['product_price_wt'] = mo_ps_round($row['product_price'] * (1 + ($row['tax_rate'] * 0.01))) + mo_ps_round($row['ecotax'] * (1 + $row['ecotax_tax_rate'] / 100));
    else
    {
        $row['product_price_wt_but_ecotax'] = $row['product_price_wt'];
        $row['product_price_wt'] = mo_ps_round($row['product_price_wt'] + $row['ecotax'] * (1 + $row['ecotax_tax_rate'] / 100));
    }

    $row['total_wt'] = $row['product_quantity'] * $row['product_price_wt'];
    $row['total_price'] = $row['product_quantity'] * $row['product_price'];

    return $row;
}

