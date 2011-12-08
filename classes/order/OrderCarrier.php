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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class OrderCarrierCore extends ObjectModel
{
	/** @var integer */
	public $id_order_carrier;

	/** @var integer */
	public $id_order;

	/** @var integer */
	public $id_carrier;

	/** @var integer */
	public $id_order_invoice;

	/** @var float */
	public $weight;

	/** @var float */
	public $shipping_cost_tax_excl;

	/** @var float */
	public $shipping_cost_tax_incl;

	/** @var integer */
	public $tracking_number;


	/** @var string Object creation date */
	public $date_add;

	protected $tables = array ('order_carrier');

	protected	$fieldsRequired = array ('id_order', 'id_carrier');
	protected	$fieldsValidate = array (
		'id_order_carrier' => 'isUnsignedId',
		'id_order' => 'isUnsignedId',
		'id_carrier' => 'isUnsignedId',
		'id_order_invoice' => 'isUnsignedId',
		'weight' => 'isFloat',
		'shipping_cost_tax_excl' => 'isFloat',
		'shipping_cost_tax_incl' => 'isFloat',
		'tracking_number' => 'isAnything');

	public static $definition = array(
		'table' => 'order_carrier',
		'primary' => 'id_order_carrier',
	);

	protected	$webserviceParameters = array(
		'fields' => array(
			'id_order' => array('xlink_resource' => 'orders'),
			'id_carrier' => array('xlink_resource' => 'carriers'),
		),
	);

	public function getFields()
	{
		$this->validateFields();

		$fields['id_order'] = (int)($this->id_order);
		$fields['id_carrier'] = (int)($this->id_carrier);
		$fields['id_order_invoice'] = (int)($this->id_order_invoice);
		$fields['weight'] = (float)($this->weight);
		$fields['shipping_cost_tax_excl'] = (float)($this->shipping_cost_tax_excl);
		$fields['shipping_cost_tax_incl'] = (float)($this->shipping_cost_tax_incl);
		$fields['tracking_number'] = pSQL(($this->tracking_number));
		$fields['date_add'] = pSQL($this->date_add);

		return $fields;
	}
}