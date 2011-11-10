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
 * Represents quantities available
 * It is either synchronized with Stock or manualy set by the seller
 *
 * @since 1.5.0
 */
class StockAvailableCore extends ObjectModel
{
	public $id_product;
	public $id_product_attribute;
	public $id_shop;
	public $quantity = 0;
	public $depends_on_stock = 0;

	/*
	 * @var bool it was previously in Product class
	 */
	public $out_of_stock = 0;

	protected $fieldsRequired = array(
		'id_product',
		'id_product_attribute',
		'quantity',
		'depends_on_stock',
		'out_of_stock'
	);

	protected $fieldsSize = array();

	protected $fieldsValidate = array(
		'id_product' => 'isUnsignedId',
		'id_product_attribute' => 'isUnsignedId',
		'id_shop' => 'isUnsignedId',
		'quantity' => 'isInt',
		'depends_on_stock' => 'isBool',
		'out_of_stock' => 'isInt'
	);

	protected $table = 'stock_available';
	protected $identifier = 'id_stock_available';

	public function getFields()
	{
		if (!$this->id_shop)
			$this->id_shop = Context::getContext()->shop->getID(true);

		$this->validateFields();
		$fields['id_product'] = (int)$this->id_product;
		$fields['id_product_attribute'] = (int)$this->id_product_attribute;
		$fields['id_shop'] = (int)$this->id_shop;
		$fields['quantity'] = (int)$this->quantity;
		$fields['depends_on_stock'] = (bool)$this->depends_on_stock;
		$fields['out_of_stock'] = (bool)$this->out_of_stock;
		return $fields;
	}

	/**
	 * For a given {id_product, id_product_attribute and id_shop}, gets the stock id associated
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute default null
	 * @param int $id_shop
	 * @return int
	 */
	public static function getIdStockAvailable($id_product, $id_product_attribute = null, $id_shop = null)
	{
		if (is_null($id_shop))
			$id_shop = Context::getContext()->shop->getID(true);

		$query = new DbQuery();
		$query->select('id_stock_available');
		$query->from('stock_available');
		$query->where('id_product = '.(int)$id_product);
		if (!is_null($id_product_attribute))
			$query->where('id_product_attribute = '.(int)$id_product_attribute);
		$query->where('id_shop = '.(int)$id_shop);

		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}

	/**
	 * For a given id_product, synchronizes StockAvailable::quantity with Stock::usable_quantity
	 *
	 * @param int $id_product
	 */
	public static function synchronize($id_product)
	{
		// used var in algorithm
		$ids_warehouse = array();
		$ids_product_attribute = array();

		// builds query to get all warehouses/shops
		$query = new DbQuery();
		$query->select('id_warehouse, id_shop');
		$query->from('warehouse_shop');
		$query->orderBy('id_shop');

		// queries to get warehouse ids grouped by shops
		foreach (Db::getInstance()->executeS($query) as $row)
			$ids_warehouse[$row['id_shop']][] = $row['id_warehouse'];

		// gets all product attributes ids
		foreach (Product::getProductAttributesIds($id_product) as $id_product_attribute)
			$ids_product_attribute[] = $id_product_attribute['id_product_attribute'];

		// loops on ids_warehouse to synchronize
		foreach ($ids_warehouse as $id_shop => $warehouses)
		{
			$total_quantity = 0;

			// if there are no product attributes
			if (empty($ids_product_attribute))
				$total_quantity = StockManagerFactory::getManager()->getProductRealQuantities($id_product, null, $warehouses, true);

			// else loops on id_product_attribute and to get $total_quantity
			foreach ($ids_product_attribute as $id_product_attribute)
			{
				$quantity = StockManagerFactory::getManager()->getProductRealQuantities($id_product, $id_product_attribute, $warehouses, true);

				$query = array(
					'table' => 'stock_available',
					'data' => array('quantity' => $quantity),
					'type' => 'UPDATE',
					'where' => 'id_product = '.(int)$id_product.' AND id_product_attribute = '.(int)$id_product_attribute.' AND id_shop = '.(int)$id_shop
				);

				Db::getInstance()->autoExecute($query['table'], $query['data'], $query['type'], $query['where']);

				$total_quantity += $quantity;
			}

			$query = array(
				'table' => 'stock_available',
				'data' => array('quantity' => $total_quantity),
				'type' => 'UPDATE',
				'where' => 'id_product = '.(int)$id_product.' AND id_product_attribute = 0 AND id_shop = '.(int)$id_shop
			);

			// saves quantities
			Db::getInstance()->autoExecute($query['table'], $query['data'], $query['type'], $query['where']);
		}
	}

	/**
	 * For a given id_product, sets if stock available depends on stock
	 *
	 * @param int $depends_on_stock
	 * @param int $id_product
	 * @param int $id_shop
	 */
	public static function setProductDependsOnStock($depends_on_stock, $id_product, $id_shop = null)
	{
		if (is_null($id_shop))
			$id_shop = Context::getContext()->shop->getID(true);

		Db::getInstance()->autoExecute(
			_DB_PREFIX_.'stock_available',
			array('depends_on_stock' => (bool)$depends_on_stock),
			'UPDATE',
			'id_product = '.(int)$id_product.' AND id_shop = '.(int)$id_shop
		);
		if ($depends_on_stock)
			StockAvailable::synchronize($id_product);
	}

	/**
	 * For a given id_product, sets if product is available out of stocks
	 *
	 * @param int $out_of_stock
	 * @param int $id_product
	 * @param int $id_shop
	 */
	public static function setProductOutOfStock($out_of_stock, $id_product, $id_shop = null)
	{
		if (is_null($id_shop))
			$id_shop = Context::getContext()->shop->getID(true);

		Db::getInstance()->autoExecute(
			'stock_available',
			array('out_of_stock' => (int)$out_of_stock),
			'UPDATE',
			'id_product = '.(int)$id_product.' AND id_shop = '.(int)$id_shop
		);
	}

	/**
	 * For a given id_product and id_product_attribute, gets stock available
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute
	 * @param int $id_shop
	 * @return int
	 */
	public static function getStockAvailableForProduct($id_product, $id_product_attribute = null, $id_shop = null)
	{
		if (is_null($id_shop))
			$id_shop = Context::getContext()->shop->getID(true);

		$query = new DbQuery();
		$query->select('quantity');
		$query->from('stock_available');
		$query->where('id_product = '.(int)$id_product);
		$query->where('id_product_attribute = '.(int)$id_product_attribute);
		$query->where('id_shop = '.(int)$id_shop);
		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}

	/**
	 * Upgrades total_quantity_available after having saved
	 * @see ObjectModel::save()
	 */
	public function save($null_values = false, $autodate = true)
	{
		if (!parent::save($null_values, $autodate))
			return false;

		if ($this->id_product_attribute == 0)
			return true;

		$id_stock_available = StockAvailable::getIdStockAvailable($this->id_product, 0, $this->id_shop);

		$total_quantity = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT SUM(quantity)
			FROM '._DB_PREFIX_.'stock_available
			WHERE id_product = '.(int)$this->id_product.' AND id_shop = '.(int)$this->id_shop.' AND id_product_attribute <> 0');

		if (!$id_stock_available)
		{
			return (int)Db::getInstance()->execute('
				INSERT '._DB_PREFIX_.'stock_available
				SET id_product = '.(int)$this->id_product.', id_product_attribute = 0, id_shop = '.(int)$this->id_shop.', quantity = '.(int)$total_quantity);
		}
		else
		{
			return (int)Db::getInstance()->execute('
				UPDATE '._DB_PREFIX_.'stock_available
				SET quantity = '.(int)$total_quantity.'
				WHERE id_stock_available = '.(int)$id_stock_available);
		}
	}

	/**
	 * For a given id_product and id_product_attribute update the quantity available
	 */
	public static function updateQuantity($id_product, $id_product_attribute, $delta_quantity, $id_shop = null)
	{
		$id_stock = self::getIdStockAvailable($id_product, $id_product_attribute, $id_shop);

		if (!$id_stock)
			return false;

		// Update quantity of the pack products
		if (Pack::isPack($id_product))
		{
			$products_pack = Pack::getItems((int)($product['id_product']), (int)(Configuration::get('PS_LANG_DEFAULT')));
			foreach ($products_pack as $product_pack)
			{
				$pack_id_product_attribute = Product::getDefaultAttribute($tab_product_pack['id_product'], 1);
				self::updateQuantity($product_pack->id, $pack_id_product_attribute, $product_pack->pack_quantity * $delta_quantity, $id_shop);
			}
		}

		$stock_available = new StockAvailable($id_stock);
		$stock_available->quantity = $stock_available->quantity + $delta_quantity;
		$stock_available->save();
	}
}
