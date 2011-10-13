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
 * Represents the products kept in warehouses
 *
 * @since 1.5.0
 */
class StockCore extends ObjectModel
{
	public $id_warehouse;
	public $id_product;
	public $id_product_attribute;
	public $physical_quantity;
	public $usable_quantity;
	public $price_te;
	public $id_currency;

	protected $fieldsRequired = array(
		'id_warehouse',
		'id_product',
		'id_product_attribute',
		'physical_quantity',
		'usable_quantity',
		'price_te',
		'id_currency'
	);

	protected $fieldsSize = array();

	protected $fieldsValidate = array(
		'id_warehouse' => 'isUnsignedId',
		'id_product' => 'isUnsignedId',
		'id_product_attribute' => 'isUnsignedId',
		'physical_quantity' => 'isUnsignedInt',
		'usable_quantity' => 'isInt',
		'price_te' => 'isPrice',
		'id_currency' => 'isUnsignedInt'
	);

	protected $table = 'stock';
	protected $identifier = 'id_stock';

	public function getFields()
	{
		$this->validateFields();
		$fields['id_warehouse'] = (int)$this->id_warehouse;
		$fields['id_product'] = (int)$this->id_product;
		$fields['id_product_attribute'] = (int)$this->id_product_attribute;
		$fields['physical_quantity'] = (int)$this->physical_quantity;
		$fields['usable_quantity'] = (int)$this->usable_quantity;
		$fields['price_te'] = (float)$this->price_te;
		$fields['id_currency'] = (int)$this->id_currency;
		return $fields;
	}
}