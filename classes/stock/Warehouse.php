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
 * Holds Stock
 *
 * @since 1.5.0
 */
class WarehouseCore extends ObjectModel
{
	public $id;
	public $id_address;
	public $reference;
	public $name;
	public $id_employee;
	public $id_currency;

	/**
	 * Describes the way a Warehouse is managed
	 * @var enum WA|LIFO|FIFO
	 */
	public $management_type;

	protected $fieldsRequired = array(
		'id_address',
		'reference',
		'name',
		'id_employee',
		'management_type',
		'id_currency'
	);

	protected $fieldsSize = array(
		'stock_management' => 32,
		'reference' => 45,
		'name' => 45
	);

	protected $fieldsValidate = array(
		'id_address' => 'isUnsignedId',
		'reference' => 'isString',
		'name' => 'isString',
		'id_employee' => 'isUnsignedId',
		'management_type' => 'isStockManagement',
		'id_currency' => 'isUnsignedId'
	);

	protected $table = 'warehouse';
	protected $identifier = 'id_warehouse';

	public function getFields()
	{
		$this->validateFields();
		$fields['id_address'] = (int)$this->id_address;
		$fields['reference'] = $this->reference;
		$fields['name'] = pSQL($this->name);
		$fields['id_employee'] = (int)$this->id_employee;
		$fields['management_type'] = pSQL($this->management_type);
		$fields['id_currency'] = (int)$this->id_currency;
		return $fields;
	}

	/**
	 * Gets the shops associated to the current warehouse
	 *
	 * @return array ids
	 */
	public function getShops()
	{
		$ids_shop = array();

		$query = new DbQuery();
		$query->select('ws.id_shop');
		$query->from('warehouse_shop ws');
		$query->where($this->identifier.' = '.(int)$this->id);

		$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

		foreach ($res as $shops)
			foreach ($shops as $shop)
				$ids_shop[] = $shop;

		return $ids_shop;
	}

	/**
	 * Sets the shops associated to the current warehouse
	 *
	 * @param array $ids_shop
	 */
	public function setShops($ids_shop)
	{
		$row_to_insert = array();
		foreach ($ids_shop as $id_shop)
			$row_to_insert[] = array($this->identifier => $this->id, 'id_shop' => (int)$id_shop);

		Db::getInstance()->execute('
			DELETE FROM '._DB_PREFIX_.'warehouse_shop
			WHERE '.$this->identifier.' = '.(int)$this->id);

		Db::getInstance()->autoExecute(_DB_PREFIX_.'warehouse_shop', $row_to_insert, 'INSERT');
	}

	/**
	 * Gets the carriers associated to the current warehouse
	 *
	 * @return array ids
	 */
	public function getCarriers()
	{
		$ids_carrier = array();

		$query = new DbQuery();
		$query->select('wc.id_carrier');
		$query->from('warehouse_carrier wc');
		$query->where($this->identifier.' = '.(int)$this->id);

		$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

		foreach ($res as $carriers)
			foreach ($carriers as $carrier)
				$ids_carrier[] = $carrier;

		return $ids_carrier;
	}

	/**
	 * Sets the carriers associated to the current warehouse
	 *
	 * @param array $ids_carriers
	 */
	public function setCarriers($ids_carriers)
	{
		$row_to_insert = array();
		foreach ($ids_carriers as $id_carrier)
			$row_to_insert[] = array($this->identifier => $this->id, 'id_carrier' => (int)$id_carrier);

		Db::getInstance()->execute('
			DELETE FROM '._DB_PREFIX_.'warehouse_carrier
			WHERE '.$this->identifier.' = '.(int)$this->id);

		Db::getInstance()->autoExecute(_DB_PREFIX_.'warehouse_carrier', $row_to_insert, 'INSERT');
	}

	/**
	 * Checks if a warehouse is empty - holds no stock
	 *
	 * @return bool
	 */
	public function isEmpty()
	{
		$query = new DbQuery();
		$query->select('SUM(s.physical_quantity)');
		$query->from('stock s');
		$query->where($this->identifier.' = '.(int)$this->id);
		return (Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query) == 0);
	}

	/**
	 * Checks if the given warehouse exists
	 *
	 * @param int $id_warehouse
	 * @return bool
	 */
	public static function exists($id_warehouse)
	{
		$query = new DbQuery();
		$query->select('id_warehouse');
		$query->from('warehouse');
		$query->where('id_warehouse = '.(int)$id_warehouse);
		return (Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query));
	}

	/**
	 * For a given {product, product attribute} sets its location in the given warehouse
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute
	 * @param int $id_warehouse
	 * @param string $location
	 * @return bool
	 */
	public static function setProductLocation($id_product, $id_product_attribute, $id_warehouse, $location)
	{
		Db::getInstance()->execute('
			DELETE FROM '._DB_PREFIX_.'`warehouse_product_location` wpl
			WHERE wpl.id_product = '.(int)$id_product.'
			AND wpl.id_product_attribute = '.(int)$id_product_attribute.'
			AND wpl.id_warehouse = '.(int)$id_warehouse);

		$query = '
			UPDATE '._DB_PREFIX_.'warehouse_product_location
			SET location = '.pSQL($location).'
			WHERE id_product = '.(int)$id_product.'
			AND id_product_attribute = '.(int)$id_product_attribute.'
			AND id_warehouse = '.(int)$id_warehouse;

		return (Db::getInstance()->execute($query));
	}

	/**
	 * For a given {product, product attribute} gets its location in the given warehouse
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute
	 * @param int $id_warehouse
	 * @return string
	 */
	public static function getProductLocation($id_product, $id_product_attribute, $id_warehouse)
	{
		$query = new DbQuery();
		$query->select('location');
		$query->from('warehouse_product_location');
		$query->where('id_warehouse = '.(int)$id_warehouse);
		$query->where('id_product = '.(int)$id_product);
		$query->where('id_product_attribute = '.(int)$id_product_attribute);

		return (Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query));
	}
}
