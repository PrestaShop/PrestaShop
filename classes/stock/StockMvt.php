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

class StockMvtCore extends ObjectModel
{
	public $id;

	/**
	 * @var string The creation date of the movement
	 */
	public $date_add;

	/**
	 * @var int The employee id, responsible of the movement
	 */
	public $id_employee;

	/**
	 * @since 1.5.0
	 * @var string The first name of the employee responsible of the movement
	 */
	public $employee_firstname;

	/**
	 * @since 1.5.0
	 * @var string The last name of the employee responsible of the movement
	 */
	public $employee_lastname;

	/**
	 * @since 1.5.0
	 * @var int The stock id on wtich the movement is applied
	 */
	public $id_stock;

	/**
	 * @since 1.5.0
	 * @var int the quantity of product with is moved
	 */
	public $physical_quantity;

	/**
	 * @var int id of the movement reason assoiated to the movement
	 */
	public $id_stock_mvt_reason;

	/**
	 * @var int Used when the movement is due to a customer order
	 */
	public $id_order = null;

	/**
	 * @since 1.5.0
	 * @var int detrmine if the movement is a positive or negative operation
	 */
	public $sign;

	/**
	 * @since 1.5.0
	 * @var int Used when the movement is due to a supplier order
	 */
	public $id_supplier_order = null;

	/**
	 * @since 1.5.0
	 * @var float Last value of the weighted-average method
	 */
	public $last_wa = null;

	/**
	 * @since 1.5.0
	 * @var float Current value of the weighted-average method
	 */
	public $current_wa = null;

	/**
	 * @since 1.5.0
	 * @var float The unit price without tax of the product associated to the movement
	 */
	public $price_te;

	/**
	 * @since 1.5.0
	 * @var int Refers to an other id_stock_mvt : used for LIFO/FIFO implementation in StockManager
	 */
	public $referer;

	/**
	 * @deprecated since 1.5.0
	 * @deprecated stock movement will not be updated anymore
	 */
	public $date_upd;

	/**
	 * @deprecated since 1.5.0
	 * @see physical_quantity
	 * @var int
	 */
	public $quantity;

	public static $definition = array(
		'table' => 'stock_mvt',
		'primary' => 'id_stock_mvt',
	);

 	protected $fieldsRequired = array(
 		'date_add',
 		'id_employee',
 		'id_stock',
 		'physical_quantity',
 		'id_stock_mvt_reason',
 		'sign',
 		'price_te'
 	);

 	protected $fieldsValidate = array(
 		'date_add' => 'isDate',
 		'id_employee' => 'isUnsignedId',
 		'employee_firstname' => 'isName',
 		'employee_lastname' => 'isName',
 		'id_stock' => 'isUnsignedId',
 		'physical_quantity' => 'isUnsignedInt',
 	 	'id_stock_mvt_reason' => 'isUnsignedId',
 		'id_order' => 'isUnsignedId',
 		'sign' => 'isInt',
 		'last_wa' => 'isPrice',
 		'current_wa' => 'isPrice',
 		'price_te' => 'isPrice',
 		'referer' => 'isUnsignedId'
 	);

	protected $webserviceParameters = array(
		'objectsNodeName' => 'stock_movements',
		'objectNodeName' => 'stock_movement',
		'fields' => array(
			'id_employee' => array('xlink_resource'=> 'employees'),
			'id_stock' => array('xlink_resource'=> 'stock'),
			'id_stock_mvt_reason' => array('xlink_resource'=> 'stock_movement_reasons'),
			'id_order' => array('xlink_resource'=> 'orders')
		),
	);

	public function getFields()
	{
		$this->validateFields();
		$fields['date_add'] = pSQL($this->date_add);
		$fields['id_employee'] = (int)$this->id_employee;
		$fields['employee_lastname'] = pSQL($this->employee_lastname);
		$fields['employee_firstname'] = pSQL(Tools::ucfirst($this->employee_firstname));
		$fields['id_stock'] = (int)$this->id_stock;
		$fields['physical_quantity'] = (int)$this->physical_quantity;
		$fields['id_stock_mvt_reason'] = (int)$this->id_stock_mvt_reason;
		$fields['id_order'] = (int)$this->id_order;
		$fields['sign'] = (int)$this->sign;
		$fields['last_wa'] = (float)Tools::ps_round($this->last_wa, 6);
		$fields['current_wa'] = (float)Tools::ps_round($this->current_wa, 6);
		$fields['price_te'] = (float)Tools::ps_round($this->price_te, 6);
		$fields['referer'] = (int)$this->referer;
		return $fields;
	}

	/**
	 * @deprecated since 1.5.0
	 *
	 * This method no longer exists, and have no equivalent because of the missing movements have to be handle by inventories on real stock.
	 */
	public static function addMissingMvt($id_employee)
	{
		Tools::displayAsDeprecated();
	}
}