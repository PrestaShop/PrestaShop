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

/**
 * @since 1.5.0
 */
class SupplierOrderDetailCore extends ObjectModel
{
	/**
	 * @var int Supplier order
	 */
	public $id_supplier_order;

	/**
	 * @var int Product ordered
	 */
	public $id_product;

	/**
	 * @var int Product attribute ordered
	 */
	public $id_product_attribute;

	/**
	 * @var int Currency used to buy this particular product
	 */
	public $id_currency;

	/**
	 * @var float Exchange rate between $id_currency and SupplierOrder::$id_ref_currency, at the time
	 */
	public $exchange_rate;

	/**
	 * @var float Unit price without discount, without tax
	 */
	public $unit_price_te;

	/**
	 * @var int Quantity ordered
	 */
	public $quantity;

	/**
	 * @var float This defines the price of the product, considering the number of units to buy.
	 * ($unit_price_te * $quantity), without discount, without tax
	 */
	public $price_te;

	/**
	 * @var float Supplier discount rate for a given product
	 */
	public $discount_rate;

	/**
	 * @var float Supplier discount value (($discount_rate / 100) * $price_te), without tax
	 */
	public $discount_value_te;

	/**
	 * @var float ($price_te - $discount_value_te), with discount, without tax
	 */
	public $price_with_discount_te;

	/**
	 * @var int Tax rate for the given product
	 */
	public $tax_rate;

	/**
	 * @var float Tax value for the given product
	 */
	public $tax_value;

	/**
	 * @var float ($price_with_discount_te + $tax_value)
	 */
	public $price_ti;

	/**
	 * @var float Tax value of the given product after applying the global order discount (i.e. if SupplierOrder::discount_rate is set)
	 */
	public $tax_value_with_order_discount;

	/**
	 * @var float This is like $price_with_discount_te, considering the global order discount.
	 * (i.e. if SupplierOrder::discount_rate is set)
	 */
	public $price_with_order_discount_te;

	protected $fieldsRequired = array(
		'id_supplier_order',
		'id_product',
		'id_product_attribute',
		'id_currency',
		'excange_rate',
		'unit_price_te',
		'quantity',
		'price_te',
		'discount_rate',
		'discount_value_te',
		'price_with_discount_te',
		'tax_rate',
		'tax_value',
		'price_ti',
		'tax_value_with_order_discount',
		'price_with_order_discount_te'
	);

	protected $fieldsValidate = array(
		'id_supplier_order' => 'isUnsignedId',
		'id_product' => 'isUnsignedId',
		'id_product_attribute' => 'isUnsignedId',
		'id_currency' => 'isUnsignedId',
		'exchange_rate' => 'isFloat',
		'unit_price_te' => 'isPrice',
		'quantity' => 'isUnsignedInt',
		'price_te' => 'isPrice',
		'discount_rate' => 'isFloat',
		'discount_value_te' => 'isPrice',
		'price_with_discount_te' => 'isPrice',
		'tax_rate' => 'isFloat',
		'tax_value' => 'isPrice',
		'price_ti' => 'isPrice',
		'tax_value_with_order_discount' => 'isFloat',
		'price_with_order_discount_te' => 'isPrice',
	);

	/**
	 * @var string Database table name
	 */
	protected $table = 'supplier_order_detail';

	/**
	 * @var string Database ID name
	 */
	protected $identifier = 'id_supplier_order_detail';

	public function getFields()
	{
		$this->validateFields();

		$fields['id_supplier_order'] = (int)$this->id_supplier;
		$fields['id_product'] = (int)$this->id_employee;
		$fields['id_product_attribute'] = (int)$this->id_warehouse;
		$fields['id_currency'] = (int)$this->id_currency;
		$fields['exchange_rate'] = (float)$this->exchange_rate;
		$fields['unit_price_te'] = (float)$this->unit_price_te;
		$fields['quantity'] = (int)$this->id_state;
		$fields['price_te'] = (float)$this->price_te;
		$fields['discount_rate'] = (float)$this->discount_rate;
		$fields['discount_value_te'] = (float)$this->discount_value_te;
		$fields['price_with_discount_te'] = (float)$this->price_with_discount_te;
		$fields['tax_rate'] = (float)$this->tax_rate;
		$fields['tax_value'] = (float)$this->tax_value;
		$fields['price_ti'] = (float)$this->price_ti;
		$fields['tax_value_with_order_discount'] = (float)$this->tax_value_with_order_discount;
		$fields['price_with_order_discount_te'] = (float)$this->price_with_order_discount_te;

		return $fields;
	}
}