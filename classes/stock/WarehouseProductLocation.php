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
*  @version  Release: $Revision: 7310 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
/**
 * @since 1.5.0
 */
class WarehouseProductLocationCore extends ObjectModel
{
	/**
	 * @var integer product ID
	 * */
	public $id_product;

	/**
	 * @var integer product attribute ID
	 * */
	public $id_product_attribute;

	/**
	 * @var integer the warehouse ID
	 * */
	public $id_warehouse;

	/**
	 * @var string The location of the product
	 * */
	public $location;

 	protected $fieldsRequired = array('id_product', 'id_product_attribute', 'id_warehouse');
 	protected $fieldsSize = array('location' => 64);
 	protected $fieldsValidate = array(
 		'location' => 'isReference',
 		'id_product' => 'isUnsignedId',
 		'id_product_attribute' => 'isUnsignedId',
 		'id_warehouse' => 'isUnsignedId',
 	);

	protected $table = 'warehouse_product_location';
	protected $identifier = 'id_warehouse_product_location';

	public function getFields()
	{
		$this->validateFields();

		$fields['id_product'] = (int)$this->id_product;
		$fields['id_product_attribute'] = (int)$this->id_product_attribute;
		$fields['id_warehouse'] = (int)$this->id_warehouse;
		$fields['location'] = pSQL($this->location);

		return $fields;
	}

	/**
	 * For a given product and warehouse, get the location
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute
	 * @param int $id_warehouse
	 * @return array
	 */
	public static function getProductLocation($id_product, $id_product_attribute, $id_warehouse)
	{
		// build query
		$query = new DbQuery();
		$query->select('wpl.location');
		$query->from('warehouse_product_location wpl');
		$query->where('wpl.id_product = '.(int)$id_product.'
			AND wpl.id_product_attribute = '.(int)$id_product_attribute.'
			AND wpl.id_warehouse = '.(int)$id_warehouse
		);

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}

	/**
	 * For a given product and warehouse, get the WarehouseProductLocation corresponding ID
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute
	 * @param int $id_supplier
	 * @return array
	 */
	public static function getIdByProductAndWarehouse($id_product, $id_product_attribute, $id_warehouse)
	{
		// build query
		$query = new DbQuery();
		$query->select('wpl.id_warehouse_product_location');
		$query->from('warehouse_product_location wpl');
		$query->where('wpl.id_product = '.(int)$id_product.'
			AND wpl.id_product_attribute = '.(int)$id_product_attribute.'
			AND wpl.id_warehouse = '.(int)$id_warehouse
		);

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}

	/**
	 * For a given product, retrieves its warehouses
	 *
	 * @param int $id_product
	 * @param int $id_lang
	 * @return array
	 */
	public static function getCollection($id_product)
	{
		// build query
		$query = new DbQuery();
		$query->select('*');
		$query->from('warehouse_product_location wpl');
		$query->where('wpl.id_product = '.(int)$id_product);

		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

		return ObjectModel::hydrateCollection('WarehouseProductLocation', $results);
	}
}