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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class OrderDiscountCore extends ObjectModel
{
	/** @var integer */
	public $id_order_discount;
	
	/** @var integer */
	public $id_order;

	/** @var integer */
	public $id_discount;
	
	/** @var string */	
	public $name;

	/** @var integer */	
	public $value;

	protected $tables = array ('order_discount');

	protected	$fieldsRequired = array ('id_order', 'name', 'value');	
	protected	$fieldsValidate = array ('id_order' => 'isUnsignedId', 'name' => 'isGenericName', 'value' => 'isInt');

	/* MySQL does not allow 'order detail' for a table name */
	protected 	$table = 'order_discount';
	protected 	$identifier = 'id_order_discount';

	protected	$webserviceParameters = array(
		'fields' => array(
			'id_order' => array('xlink_resource' => 'orders'),
		),
	);
	
	public function getFields()
	{
		parent::validateFields();

		$fields['id_order'] = (int)($this->id_order);
		$fields['name'] = pSQL($this->name);
		$fields['value'] = (int)($this->value);
		
		return $fields;
	}	
}

