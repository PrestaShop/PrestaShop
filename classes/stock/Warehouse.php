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
	/** @var int identifier of the warehouse */
	public $id;

	/** @var int Id of the address associated to the warehouse */
	public $id_address;

	/** @var string Reference of the warehouse */
	public $reference;

	/** @var string Name of the warehouse */
	public $name;

	/** @var int Id of the employee who manages the warehouse */
	public $id_employee;

	/** @var int Id of the valuation currency of the warehouse */
	public $id_currency;

	/** @var boolean True if warehouse has been deleted (hence, no deletion in DB) */
	public $deleted = 0;

	/**
	 * Describes the way a Warehouse is managed
	 * @var enum WA|LIFO|FIFO
	 */
	public $management_type;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'warehouse',
		'primary' => 'id_warehouse',
		'fields' => array(
			'id_address' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'reference' => 			array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'size' => 45),
			'name' => 				array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 45),
			'id_employee' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'management_type' => 	array('type' => self::TYPE_STRING, 'validate' => 'isStockManagement', 'required' => true),
			'id_currency' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'deleted' => 			array('type' => self::TYPE_BOOL),
		),
	);

	/**
	 * @see ObjectModel::$webserviceParameters
	 */
 	protected $webserviceParameters = array(
 		'fields' => array(
 			'id_address' => array('xlink_resource' => 'addresses'),
 			'id_employee' => array('xlink_resource' => 'employees'),
 			'id_currency' => array('xlink_resource' => 'currencies'),
 			'valuation' => array('getter' => 'getWsStockValue', 'setter' => false),
 			'deleted' => array(),
 		),
 		'associations' => array(
			'stocks' => array(
				'resource' => 'stock',
				'fields' => array(
					'id' => array(),
				),
			),
			'carriers' => array(
				'resource' => 'carrier',
				'fields' => array(
					'id' => array(),
				),
			),
			'shops' => array(
				'resource' => 'shop',
				'fields' => array(
					'id' => array(),
					'name' => array(),
				),
			),
		),
 	);

	/**
	 * Gets the shops (id and name) associated to the current warehouse
	 *
	 * @return array
	 */
	public function getShops()
	{
		$query = new DbQuery();
		$query->select('ws.id_shop, s.name');
		$query->from('warehouse_shop', 'ws');
		$query->leftJoin('shop', 's', 's.id_shop = ws.id_shop');
		$query->where($this->def['primary'].' = '.(int)$this->id);

		$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
		return $res;
	}

	/**
	 * Sets the shops associated to the current warehouse
	 *
	 * @param array $ids_shop
	 */
	public function setShops($ids_shop)
	{
		if (!is_array($ids_shop))
			$ids_shop = array();

		$row_to_insert = array();
		foreach ($ids_shop as $id_shop)
			$row_to_insert[] = array($this->def['primary'] => $this->id, 'id_shop' => (int)$id_shop);

		Db::getInstance()->execute('
			DELETE FROM '._DB_PREFIX_.'warehouse_shop
			WHERE '.$this->def['primary'].' = '.(int)$this->id);

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
		$query->from('warehouse_carrier', 'wc');
		$query->where($this->def['primary'].' = '.(int)$this->id);

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
		if (!is_array($ids_carriers))
			$ids_carriers = array();

		$row_to_insert = array();
		foreach ($ids_carriers as $id_carrier)
			$row_to_insert[] = array($this->def['primary'] => $this->id, 'id_carrier' => (int)$id_carrier);

		Db::getInstance()->execute('
			DELETE FROM '._DB_PREFIX_.'warehouse_carrier
			WHERE '.$this->def['primary'].' = '.(int)$this->id);

		if ($row_to_insert)
			Db::getInstance()->autoExecute(_DB_PREFIX_.'warehouse_carrier', $row_to_insert, 'INSERT');
	}

	/**
	 * For a given carrier, removes it from the warehouse/carrier association
	 * If $id_warehouse is set, it only removes the carrier for this warehouse
	 *
	 * @param int $id_carrier
	 * @param int $id_warehouse optional
	 */
	public static function removeCarrier($id_carrier, $id_warehouse = null)
	{
		Db::getInstance()->execute('
			DELETE FROM '._DB_PREFIX_.'warehouse_carrier
			WHERE id_carrier = '.(int)$id_carrier.
			($id_warehouse ? ' AND id_warehouse = '.(int)$id_warehouse : ''));
	}

	/**
	 * Checks if a warehouse is empty - i.e. holds no stock
	 *
	 * @return bool
	 */
	public function isEmpty()
	{
		$query = new DbQuery();
		$query->select('SUM(s.physical_quantity)');
		$query->from('stock', 's');
		$query->where($this->def['primary'].' = '.(int)$this->id);
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
		$query->where('deleted = 0');
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
			WHERE wpl.`id_product` = '.(int)$id_product.'
			AND wpl.`id_product_attribute` = '.(int)$id_product_attribute.'
			AND wpl.`id_warehouse` = '.(int)$id_warehouse);

		$query = '
			UPDATE '._DB_PREFIX_.'`warehouse_product_location`
			SET `location` = \''.pSQL($location).'\'
			WHERE `id_product` = '.(int)$id_product.'
			AND `id_product_attribute` = '.(int)$id_product_attribute.'
			AND `id_warehouse` = '.(int)$id_warehouse;

		return (Db::getInstance()->execute($query));
	}

	/**
	 * Reset all product locations for this warehouse
	 */
	public function resetProductsLocations()
	{
		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'warehouse_product_location`
			WHERE `id_warehouse` = '.(int)$this->id);
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

	/**
	 * For a given {product, product attribute} gets warehouse list
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute
	 * @param int $id_shop
	 * @return array
	 */
	public static function getProductWarehouseList($id_product, $id_product_attribute = 0, $id_shop = null)
	{
		if (is_null($id_shop))
			$id_shop = Context::getContext()->shop->getID(true);

		$query = new DbQuery();
		$query->select('wpl.id_warehouse, CONCAT(w.reference, " - ", w.name) as name');
		$query->from('warehouse_product_location', 'wpl');
		$query->innerJoin('warehouse_shop', 'ws', 'ws.id_warehouse = wpl.id_warehouse AND id_shop = '.(int)$id_shop);
		$query->innerJoin('warehouse', 'w', 'ws.id_warehouse = w.id_warehouse');
		$query->where('id_product = '.(int)$id_product);
		$query->where('id_product_attribute = '.(int)$id_product_attribute);
		$query->where('w.deleted = 0');
		$query->groupBy('wpl.id_warehouse');

		return (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query));
	}

	/**
	 * Gets available warehouses
	 * It is possible via ignore_shop and id_shop to filter the list with shop id
	 *
	 * @param bool $ignore_shop false by default
	 * @param int $id_shop null by default
	 * @return array
	 */
	public static function getWarehouses($ignore_shop = false, $id_shop = null)
	{
		if (!$ignore_shop)
			if (is_null($id_shop))
				$id_shop = Context::getContext()->shop->getID(true);

		$query = new DbQuery();
		$query->select('w.id_warehouse, CONCAT(reference, \' - \', name) as name');
		$query->from('warehouse', 'w');
		$query->where('deleted = 0');
		$query->orderBy('reference ASC');
		if (!$ignore_shop)
			$query->innerJoin('warehouse_shop', 'ws', 'ws.id_warehouse = w.id_warehouse AND ws.id_shop = '.(int)$id_shop);

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
	}

	/**
	 * Gets ids of warehouses, grouped by ids of shops
	 *
	 * @return array
	 */
	public static function getWarehousesGroupedByShops()
	{
		$ids_warehouse = array();
		$query = new DbQuery();
		$query->select('id_warehouse, id_shop');
		$query->from('warehouse_shop');
		$query->orderBy('id_shop');

		// queries to get warehouse ids grouped by shops
		foreach (Db::getInstance()->executeS($query) as $row)
			$ids_warehouse[$row['id_shop']][] = $row['id_warehouse'];

		return $ids_warehouse;
	}

	/**
	 * Gets the number of products in the current warehouse
	 *
	 * @return int
	 */
	public function getNumberOfProducts()
	{
		$query = '
			SELECT COUNT(t.id_stock)
			FROM
				(
					SELECT s.id_stock
				 	FROM '._DB_PREFIX_.'stock s
				 	WHERE s.id_warehouse = '.(int)$this->id.'
				 	GROUP BY s.id_product, s.id_product_attribute
				 ) as t';

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}

	/**
	 * Gets the number of quantities - for all products - in the current warehouse
	 *
	 * @return int
	 */
	public function getQuantitiesOfProducts()
	{
		$query = '
			SELECT SUM(s.physical_quantity)
			FROM '._DB_PREFIX_.'stock s
			WHERE s.id_warehouse = '.(int)$this->id;

		$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

		return ($res ? $res : 0);
	}

	/**
	 * Gets the value of the stock in the current warehouse
	 *
	 * @return int
	 */
	public function getStockValue()
	{
		$query = new DbQuery();
		$query->select('SUM(s.`price_te`)');
		$query->from('stock', 's');
		$query->where('s.`id_warehouse` = '.(int)$this->id);

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}

	/**
	 * For a given employee, gets the warehouse(s) he manages
	 *
	 * @param int $id_employee
	 * @return array ids_warehouse
	 */
	public static function getWarehousesByEmployee($id_employee)
	{
		$query = new DbQuery();
		$query->select('w.id_warehouse');
		$query->from('warehouse', 'w');
		$query->where('w.id_employee = '.(int)$id_employee);

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
	}

	/**
	 * For a given product, returns the warehouses it is stored in
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute
	 * @return array
	 */
	public static function getWarehousesByProductId($id_product, $id_product_attribute = 0)
	{
		if (!$id_product && !$id_product_attribute)
			return array();

		$query = new DbQuery();
		$query->select('DISTINCT w.id_warehouse, CONCAT(w.reference, " - ", w.name) as name');
		$query->from('warehouse', 'w');
		$query->leftJoin('stock', 's', 's.id_warehouse = w.id_warehouse');
		if ($id_product)
			$query->where('s.id_product = '.(int)$id_product);
		if ($id_product_attribute)
			$query->where('s.id_product_attribute = '.(int)$id_product_attribute);
		$query->orderBy('w.reference ASC');

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
	}

	/**
	 * For a given $id_warehouse, returns its name
	 * @param int $id_warehouse
	 * @return string name
	 */
	public static function getWarehouseNameById($id_warehouse)
	{
		$query = new DbQuery();
		$query->select('name');
		$query->from('warehouse');
		$query->where('id_warehouse = '.(int)$id_warehouse);
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}

	/**
	 * Webservice : gets the value of the warehouse
	 * @return int
	 */
	public function getWsStockValue()
	{
		return $this->getStockValue();
	}

	/**
	 * Webservice : gets the ids stock associated to this warehouse
	 * @return array
	 */
	public function getWsStocks()
	{
		$query = new DbQuery();
		$query->select('s.id_stock as id');
		$query->from('stock', 's');
		$query->where('s.id_warehouse ='.(int)$this->id);

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
	}

	/**
	 * Webservice : gets the ids shops associated to this warehouse
	 * @return array
	 */
	public function getWsShops()
	{
		$query = new DbQuery();
		$query->select('ws.id_shop as id, s.name');
		$query->from('warehouse_shop', 'ws');
		$query->leftJoin('shop', 's', 's.id_shop = ws.id_shop');
		$query->where($this->def['primary'].' = '.(int)$this->id);

		$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
		return $res;
	}

	/**
	 * Webservice : gets the ids carriers associated to this warehouse
	 * @return array
	 */
	public function getWsCarriers()
	{
		$ids_carrier = array();

		$query = new DbQuery();
		$query->select('wc.id_carrier as id');
		$query->from('warehouse_carrier', 'wc');
		$query->where($this->def['primary'].' = '.(int)$this->id);

		$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

		foreach ($res as $carriers)
			foreach ($carriers as $carrier)
				$ids_carrier[] = $carrier;

		return $ids_carrier;
	}

}
