<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function ps16010_price_decimals_update()
{
	if (defined('_PS_VERSION_'))
	{
		$success = true;
		$sql = array();
		$sql[] = array();
		$sql[] = array('table'=> 'orders', 'column' => 'total_discounts');
		$sql[] = array('table'=> 'orders', 'column' => 'total_discounts_tax_incl');
		$sql[] = array('table'=> 'orders', 'column' => 'total_discounts_tax_excl');
		$sql[] = array('table'=> 'orders', 'column' => 'total_paid');
		$sql[] = array('table'=> 'orders', 'column' => 'total_paid_tax_incl');
		$sql[] = array('table'=> 'orders', 'column' => 'total_paid_tax_excl');
		$sql[] = array('table'=> 'orders', 'column' => 'total_paid_real');
		$sql[] = array('table'=> 'orders', 'column' => 'total_products');
		$sql[] = array('table'=> 'orders', 'column' => 'total_products_wt');
		$sql[] = array('table'=> 'orders', 'column' => 'total_shipping');
		$sql[] = array('table'=> 'orders', 'column' => 'total_shipping_tax_incl');
		$sql[] = array('table'=> 'orders', 'column' => 'total_shipping_tax_excl');
		$sql[] = array('table'=> 'orders', 'column' => 'total_wrapping');
		$sql[] = array('table'=> 'orders', 'column' => 'total_wrapping_tax_incl');
		$sql[] = array('table'=> 'orders', 'column' => 'total_wrapping_tax_excl');
		$sql[] = array('table'=> 'order_detail', 'column' => 'product_price');	
		$sql[] = array('table'=> 'order_detail', 'column' => 'reduction_amount');
		$sql[] = array('table'=> 'order_detail', 'column' => 'reduction_amount_tax_incl');
		$sql[] = array('table'=> 'order_detail', 'column' => 'reduction_amount_tax_excl');
		$sql[] = array('table'=> 'order_detail', 'column' => 'product_quantity_discount');
		$sql[] = array('table'=> 'order_detail', 'column' => 'ecotax');
		$sql[] = array('table'=> 'order_detail', 'column' => 'total_price_tax_incl');
		$sql[] = array('table'=> 'order_detail', 'column' => 'unit_price_tax_excl');
		$sql[] = array('table'=> 'order_detail', 'column' => 'total_shipping_price_tax_incl');
		$sql[] = array('table'=> 'order_detail', 'column' => 'total_shipping_price_tax_excl');
		$sql[] = array('table'=> 'order_detail', 'column' => 'purchase_supplier_price');
		$sql[] = array('table'=> 'order_detail', 'column' => 'original_product_price');
		$sql[] = array('table'=> 'order_detail_tax', 'column' => 'unit_amount');
		$sql[] = array('table'=> 'order_detail_tax', 'column' => 'total_amount');
		$sql[] = array('table'=> 'order_invoice', 'column' => 'total_discount_tax_excl');
		$sql[] = array('table'=> 'order_invoice', 'column' => 'total_discount_tax_incl');
		$sql[] = array('table'=> 'order_invoice', 'column' => 'total_paid_tax_excl');
		$sql[] = array('table'=> 'order_invoice', 'column' => 'total_paid_tax_incl');
		$sql[] = array('table'=> 'order_invoice', 'column' => 'total_products');
		$sql[] = array('table'=> 'order_invoice', 'column' => 'total_products_wt');
		$sql[] = array('table'=> 'order_invoice', 'column' => 'total_shipping_tax_excl');
		$sql[] = array('table'=> 'order_invoice', 'column' => 'total_shipping_tax_incl');
		$sql[] = array('table'=> 'order_invoice', 'column' => 'total_wrapping_tax_excl');
		$sql[] = array('table'=> 'order_invoice', 'column' => 'total_wrapping_tax_incl');
		$sql[] = array('table'=> 'order_invoice_tax', 'column' => 'amount');
		$sql[] = array('table'=> 'product', 'column' => 'ecotax');
		$sql[] = array('table'=> 'product', 'column' => 'price');
		$sql[] = array('table'=> 'product', 'column' => 'wholesale_price');
		$sql[] = array('table'=> 'product', 'column' => 'unit_price_ratio');
		$sql[] = array('table'=> 'product_attribute', 'column' => 'wholesale_price');
		$sql[] = array('table'=> 'product_attribute', 'column' => 'price');
		$sql[] = array('table'=> 'product_attribute', 'column' => 'ecotax');
		$sql[] = array('table'=> 'product_attribute_shop', 'column' => 'wholesale_price');
		$sql[] = array('table'=> 'product_attribute_shop', 'column' => 'price');
		$sql[] = array('table'=> 'product_attribute_shop', 'column' => 'ecotax');
		$sql[] = array('table'=> 'product_shop', 'column' => 'ecotax');
		$sql[] = array('table'=> 'product_shop', 'column' => 'price');
		$sql[] = array('table'=> 'product_shop', 'column' => 'wholesale_price');
		$sql[] = array('table'=> 'product_shop', 'column' => 'unit_price_ratio');
		$sql[] = array('table'=> 'product_shop', 'column' => 'additional_shipping_cost');
		$sql[] = array('table'=> 'specific_price', 'column' => 'price');
		$sql[] = array('table'=> 'specific_price_rule', 'column' => 'price');
		$sql[] = array('table'=> 'specific_price_rule', 'column' => 'reduction');
		$sql[] = array('table'=> 'stock', 'column' => 'price_te');
		$sql[] = array('table'=> 'stock_mvt', 'column' => 'price_te');
		$sql[] = array('table'=> 'stock_mvt', 'column' => 'last_wa');
		$sql[] = array('table'=> 'stock_mvt', 'column' => 'current_wa');
		$sql[] = array('table'=> 'supply_order', 'column' => 'total_te');
		$sql[] = array('table'=> 'supply_order', 'column' => 'total_with_discount_te');
		$sql[] = array('table'=> 'supply_order', 'column' => 'total_tax');
		$sql[] = array('table'=> 'supply_order', 'column' => 'total_ti');
		$sql[] = array('table'=> 'supply_order', 'column' => 'discount_rate');
		$sql[] = array('table'=> 'supply_order', 'column' => 'discount_value_te');
		$sql[] = array('table'=> 'supply_order_detail', 'column' => 'unit_price_te');
		$sql[] = array('table'=> 'supply_order_detail', 'column' => 'price_te');
		$sql[] = array('table'=> 'supply_order_detail', 'column' => 'discount_rate');
		$sql[] = array('table'=> 'supply_order_detail', 'column' => 'discount_value_te');
		$sql[] = array('table'=> 'supply_order_detail', 'column' => 'price_with_discount_te');
		$sql[] = array('table'=> 'supply_order_detail', 'column' => 'tax_rate');
		$sql[] = array('table'=> 'supply_order_detail', 'column' => 'tax_value');
		$sql[] = array('table'=> 'supply_order_detail', 'column' => 'price_ti');
		$sql[] = array('table'=> 'supply_order_detail', 'column' => 'tax_value_with_order_discount');
		$sql[] = array('table'=> 'supply_order_detail', 'column' => 'price_with_order_discount_te');
		
		foreach($sql as $query){
			$success &= (bool)Db::getInstance()->query('ALTER TABLE `'._DB_PREFIX_.$query['table'].'` CHANGE `'.$query['column'].'`  `'.$query['column'].'` DECIMAL( 25, 10 ) NOT NULL DEFAULT  \'0.00\'');

		return $success;
	}
}