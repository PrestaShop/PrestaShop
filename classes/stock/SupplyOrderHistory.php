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
*  @version  Release: $Revision: 9659 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class SupplyOrderHistoryCore extends ObjectModel
{
	/**
	 * @var int Supply order
	 */
	public $id_supply_order;

	/**
	 * @var int Employee
	 */
	public $id_employee;

	/**
	 * @var string The first name of the employee responsible of the movement
	 */
	public $employee_firstname;

	/**
	 * @var string The last name of the employee responsible of the movement
	 */
	public $employee_lastname;

	/**
	 * @var int State
	 */
	public $id_state;

	/**
	 * @var string Date
	 */
	public $date_add;

	

	

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'supply_order_history',
		'primary' => 'id_supply_order_history',
		'fields' => array(
			'id_supply_order' => array('type' => 'FILL_ME', 'validate' => 'isUnsignedId', 'required' => true),
			'id_employee' => array('type' => 'FILL_ME', 'validate' => 'isUnsignedId', 'required' => true),
			'employee_firstname' => array('type' => 'FILL_ME', 'validate' => 'isName'),
			'employee_lastname' => array('type' => 'FILL_ME', 'validate' => 'isName'),
			'id_state' => array('type' => 'FILL_ME', 'validate' => 'isUnsignedId', 'required' => true),
			'date_add' => array('type' => 'FILL_ME', 'validate' => 'isDate', 'required' => true),
		),
	);


	public function getFields()
	{
		$this->validateFields();

		$fields['id_supply_order'] = (int)$this->id_supply_order;
		$fields['id_employee'] = (int)$this->id_employee;
		$fields['employee_lastname'] = pSQL($this->employee_lastname);
		$fields['employee_firstname'] = pSQL(Tools::ucfirst($this->employee_firstname));
		$fields['id_state'] = (int)$this->id_state;
		$fields['date_add'] = pSQL($this->date_add);

		return $fields;
	}
}