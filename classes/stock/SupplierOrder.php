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
class SupplierOrderCore extends ObjectModel
{
	/**
	 * @var int Supplier
	 */
	public $id_supplier;

	/**
	 * @var int Employee who initiated the order
	 */
	public $id_employee;

	/**
	 * @var int Warehouse where products will be delivered
	 */
	public $id_warehouse;

	/**
	 * @var int State of the order
	 */
	public $id_state;

	/**
	 * @var int Currency used for the order
	 */
	public $id_currency;

	/**
	 * @var int Currency used by default in main global configuration (i.e. by default for all shops)
	 */
	public $id_ref_currency;

	/**
	 * @var string Reference of the order
	 */
	public $reference;

	/**
	 * @var string Date when added
	 */
	public $date_add;

	/**
	 * @var string Date when updated
	 */
	public $date_upd;

	/**
	 * @var string Expected delivery date
	 */
	public $date_delivery_expected;

	/**
	 * @var float Total price without tax
	 */
	public $total_te;

	/**
	 * @var float Total price after discount, without tax
	 */
	public $total_with_discount_te;

	/**
	 * @var float Total price with tax
	 */
	public $total_ti;

	/**
	 * @var float Supplier discount rate (for the whole order)
	 */
	public $discount_rate;

	/**
	 * @var float Supplier discount value without tax (for the whole order)
	 */
	public $discount_value_te;

	protected $fieldsRequired = array(
		'id_supplier',
		'id_employee',
		'id_warehouse',
		'id_state',
		'id_currency',
		'id_ref_currency',
		'reference',
		'date_add',
		'date_upd',
		'date_delivery_expected',
		'total_te',
		'total_with_discount_te',
		'total_ti',
		'discount_rate',
		'discount_value_te'
	);

	protected $fieldsValidate = array(
		'id_supplier' => 'isUnsignedId',
		'id_employee' => 'isUnsignedId',
		'id_warehouse' => 'isUnsignedId',
		'id_state' => 'isUnsignedId',
		'id_currency' => 'isUnsignedId',
		'id_ref_currency' => 'isUnsignedId',
		'reference' => 'isGenericName',
		'date_add' => 'isDate',
		'date_upd' => 'isDate',
		'date_delivery_expected' => 'isDate',
		'total_te' => 'isPrice',
		'total_with_discount_te' => 'isPrice',
		'total_ti' => 'isPrice',
		'discount_rate' => 'isFloat',
		'discount_value_te' => 'isPrice'
	);

	/**
	 * @var string Database table name
	 */
	protected $table = 'supplier_order';

	/**
	 * @var string Database ID name
	 */
	protected $identifier = 'id_supplier_order';

	public function getFields()
	{
		$this->validateFields();

		$fields['id_supplier'] = (int)$this->id_supplier;
		$fields['id_employee'] = (int)$this->id_employee;
		$fields['id_warehouse'] = (int)$this->id_warehouse;
		$fields['id_state'] = (int)$this->id_state;
		$fields['id_currency'] = (int)$this->id_currency;
		$fields['id_ref_currency'] = (int)$this->id_ref_currency;
		$fields['reference'] = pSQL($this->reference);
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);
		$fields['date_delivery_expected'] = pSQL($this->delivery_date_expected);
		$fields['total_te'] = (float)$this->total_te;
		$fields['total_with_discount_te'] = (float)$this->total_with_discount_te;
		$fields['total_ti'] = (float)$this->total_ti;
		$fields['discount_rate'] = (float)$this->order_discount_rate;
		$fields['discount_value'] = (float)$this->order_discount_value;

		return $fields;
	}
}