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
class SupplierOrderStateCore extends ObjectModel
{
	/**
	 * @var string Name of the state
	 */
	public $name;

	/**
	 * @var bool Tells if a delivery note can be issued (i.e. the order has been validated)
	 */
	public $delivery_note;

	/**
	 * @var bool Tells if the order is still editable by an employee
	 */
	public $editable;

	/**
	 * @var bool Tells if the the order has been delivered
	 */
	public $receipt_state;

	protected $fieldsValidate = array(
		'delivery_note' => 'isBool',
		'editable' => 'isBool',
		'receipt_state' => 'isBool'
	);

	protected $fieldsRequiredLang = array('name');
 	protected $fieldsSizeLang = array('name' => 128);
 	protected $fieldsValidateLang = array('name' => 'isGenericName');

	/**
	 * @var string Database table name
	 */
	protected $table = 'supplier_order_state';

	/**
	 * @var string Database ID name
	 */
	protected $identifier = 'id_supplier_order_state';

	public function getFields()
	{
		$this->validateFields();
		$fields['delivery_note'] = (int)$this->delivery_note;
		$fields['editable'] = (int)$this->editable;
		$fields['receipt_state'] = (int)$this->receipt_state;

		return $fields;
	}

	public function getTranslationsFieldsChild()
	{
		$this->validateFieldsLang();
		return $this->getTranslationsFields(array('name'));
	}
}