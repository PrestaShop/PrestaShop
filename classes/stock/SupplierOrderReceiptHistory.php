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
class SupplierOrderReceiptHistoryCore extends ObjectModel
{
	/**
	 * @var int Detail of the supplier order
	 */
	public $id_supplier_order_detail;

	/**
	 * @var int Employee
	 */
	public $id_employee;

	/**
	 * @var int State
	 */
	public $id_supplier_order_state;

	/**
	 * @var int Quantity delivered
	 */
	public $quantity;

	/**
	 * @var string Date of delivery
	 */
	public $date_add;

	protected $fieldsRequired = array(
		'id_supplier_order_detail',
		'id_supplier_order_state',
		'id_employee',
		'quantity'
	);

	protected $fieldsValidate = array(
		'id_supplier_order_detail' => 'isUnsignedId',
		'id_supplier_order_state' => 'isUnsignedId',
		'id_employee' => 'isUnsignedId',
		'quantity' => 'isUnsignedInt',
		'date_add' => 'isDate'
	);

	/**
	 * @var string Database table name
	 */
	protected $table = 'supplier_order_receipt_history';

	/**
	 * @var string Database ID name
	 */
	protected $identifier = 'id_supplier_order_receipt_history';

	public function getFields()
	{
		$this->validateFields();

		$fields['id_supplier_order_detail'] = (int)$this->id_supplier_order_detail;
		$fields['id_supplier_order_state'] = (int)$this->id_supplier_order_state;
		$fields['id_employee'] = (int)$this->id_employee;
		$fields['quantity'] = (int)$this->quantity;

		$fields['date_add'] = pSQL($this->date_add);

		return $fields;
	}
}