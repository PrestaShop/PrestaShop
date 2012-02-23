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
	/** @var int identifier of the current product */
	public $id_product;

	/** @var int identifier of product attribute if necessary */
	public $id_product_attribute;

	/** @var int the shop associated to the current product and corresponding quantity */
	public $id_shop;

	/** @var int the group shop associated to the current product and corresponding quantity */
	public $id_group_shop;

	/** @var int the quantity available for sale */
	public $quantity = 0;

	/** @var bool determine if the available stock value depends on physical stock */
	public $depends_on_stock = 0;

	/** @var bool determine if a product is out of stock - it was previously in Product class */
	public $out_of_stock = 0;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'stock_available',
		'primary' => 'id_stock_available',
		'fields' => array(
			'id_product' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_product_attribute' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_shop' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_group_shop' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'quantity' => 				array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
			'depends_on_stock' => 		array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
			'out_of_stock' => 			array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
		),
	);

	/**
	 * @see ObjectModel::$webserviceParameters
	 */
 	protected $webserviceParameters = array(
 		'fields' => array(
 			'id_product' => array('xlink_resource' => 'products'),
 			'id_product_attribute' => array('xlink_resource' => 'combinations'),
 			'id_shop' => array('xlink_resource' => 'shops'),
 			'id_group_shop' => array('xlink_resource' => 'shop_groups'),
 		),
 		'hidden_fields' => array(
 		),
 	);

	/**
	 * For a given {id_product, id_product_attribute and id_shop}, gets the stock available id associated
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute Optional
	 * @param int $id_shop Optional
	 * @return int
	 */
	public static function getStockAvailableIdByProductId($id_product, $id_product_attribute = null, $id_shop = null)
	{
		$query = new DbQuery();
		$query->select('id_stock_available');
		$query->from('stock_available');
		$query->where('id_product = '.(int)$id_product);

		if (!is_null($id_product_attribute))
			$query->where('id_product_attribute = '.(int)$id_product_attribute);

		$query = StockAvailable::addSqlShopRestriction($query, $id_shop);
		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}

	/**
	 * For a given id_product, synchronizes StockAvailable::quantity with Stock::usable_quantity
	 *
	 * @param int $id_product
	 */
	public static function synchronize($id_product)
	{
		// gets warehouse ids grouped by shops
		$ids_warehouse = Warehouse::getWarehousesGroupedByShops();

		// gets all product attributes ids
		$ids_product_attribute = array();
		foreach (Product::getProductAttributesIds($id_product) as $id_product_attribute)
			$ids_product_attribute[] = $id_product_attribute['id_product_attribute'];

		$manager = StockManagerFactory::getManager();
		// loops on $ids_warehouse to synchronize quantities
		foreach ($ids_warehouse as $id_shop => $warehouses)
		{
			// first, checks if the product depends on stock for the given shop $id_shop
			if (StockAvailable::dependsOnStock($id_product, $id_shop))
			{
				// init quantity
				$product_quantity = 0;

				// if it's a simple product
				if (empty($ids_product_attribute))
					$product_quantity = $manager->getProductRealQuantities($id_product, null, $warehouses, true);

				// else this product has attributes, hence loops on $ids_product_attribute
				foreach ($ids_product_attribute as $id_product_attribute)
				{
					$quantity = $manager->getProductRealQuantities($id_product, $id_product_attribute, $warehouses, true);
					$query = array(
						'table' => 'stock_available',
						'data' => array('quantity' => $quantity),
						'where' => 'id_product = '.(int)$id_product.' AND id_product_attribute = '.(int)$id_product_attribute.
						StockAvailable::addSqlShopRestriction(null, $id_shop)
					);
					Db::getInstance()->update($query['table'], $query['data'], $query['where']);

					$product_quantity += $quantity;
				}

				// updates
				// if $id_product has attributes, it also updates the sum for all attributes
				$query = array(
					'table' => 'stock_available',
					'data' => array('quantity' => $product_quantity),
					'where' => 'id_product = '.(int)$id_product.' AND id_product_attribute = 0'.
					StockAvailable::addSqlShopRestriction(null, $id_shop)
				);
				Db::getInstance()->update($query['table'], $query['data'], $query['where']);
			}
		}

		// In case there are no warehouses, removes product from StockAvailable
		if (count($ids_warehouse) == 0)
		{
			StockAvailable::removeProductFromStockAvailable($id_product);
			foreach ($ids_product_attribute as $id_product_attribute)
				StockAvailable::removeProductFromStockAvailable($id_product, $id_product_attribute);
		}
	}

	/**
	 * For a given id_product, sets if stock available depends on stock
	 *
	 * @param int $id_product
	 * @param int $depends_on_stock Optional : true by default
	 * @param int $id_shop Optional : gets context by default
	 */
	public static function setProductDependsOnStock($id_product, $depends_on_stock = true, $id_shop = null)
	{
		if (is_null($id_shop))
			$id_shop = Context::getContext()->shop->id;

		$existing_id = StockAvailable::getStockAvailableIdByProductId((int)$id_product, 0, (int)$id_shop);

		if ($existing_id > 0)
		{
			Db::getInstance()->update(
				'stock_available',
				array('depends_on_stock' => (int)(bool)$depends_on_stock),
				'id_product = '.(int)$id_product.
				StockAvailable::addSqlShopRestriction(null, $id_shop)
			);
		}
		else
		{
			$params = array(
				'depends_on_stock' => (int)(bool)$depends_on_stock,
				'id_product' => (int)$id_product,
				'id_product_attribute' => 0
			);

			StockAvailable::addSqlShopParams($params, $id_shop);

			Db::getInstance()->insert('stock_available', $params);
		}

		// depends on stock.. hence synchronizes
		if ($depends_on_stock)
			StockAvailable::synchronize($id_product);
	}

	/**
	 * For a given id_product, sets if product is available out of stocks
	 *
	 * @param int $id_product
	 * @param int $out_of_stock Optional false by default
	 * @param int $id_shop Optional gets context by default
	 */
	public static function setProductOutOfStock($id_product, $out_of_stock = false, $id_shop = null)
	{
		if (is_null($id_shop))
			$id_shop = Context::getContext()->shop->id;

		$existing_id = StockAvailable::getStockAvailableIdByProductId((int)$id_product, 0, (int)$id_shop);

		if ($existing_id > 0)
		{
			Db::getInstance()->update(
				'stock_available',
				array('out_of_stock' => (int)$out_of_stock),
				'id_product = '.(int)$id_product.
				StockAvailable::addSqlShopRestriction(null, $id_shop)
			);
		}
		else
		{
			$params = array(
				'out_of_stock' => (int)$out_of_stock,
				'id_product' => (int)$id_product,
				'id_product_attribute' => 0
			);

			StockAvailable::addSqlShopParams($params, $id_shop);

			Db::getInstance()->insert('stock_available', $params);
		}
	}

	/**
	 * For a given id_product and id_product_attribute, gets its stock available
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute Optional
	 * @param int $id_shop Optional : gets context by default
	 * @return int Quantity
	 */
	public static function getQuantityAvailableByProduct($id_product = null, $id_product_attribute = null, $id_shop = null)
	{
		if (is_null($id_shop))
			$id_shop = Context::getContext()->shop->id;

		// if null, it's a product without attributes
		if (is_null($id_product_attribute))
			$id_product_attribute = 0;

		$query = new DbQuery();
		$query->select('SUM(quantity)');
		$query->from('stock_available');

		// if null, it's a product without attributes
		if (!is_null($id_product))
			$query->where('id_product = '.(int)$id_product);

		$query->where('id_product_attribute = '.(int)$id_product_attribute);

		$query = StockAvailable::addSqlShopRestriction($query, $id_shop);

		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}

	/**
	 * Upgrades total_quantity_available after having saved
	 * @see ObjectModel::add()
	 */
	public function add($autodate = true, $null_values = false)
	{
		if (!parent::add($autodate, $null_values))
			return false;
		$this->postSave();
	}

	/**
	 * Upgrades total_quantity_available after having update
	 * @see ObjectModel::update()
	 */
	public function update($null_values = false)
	{
		if (!parent::update($null_values))
			return false;
		$this->postSave();
	}

	/**
	 * Upgrades total_quantity_available after having saved
	 * @see StockAvailableCore::update()
	 * @see StockAvailableCore::add()
	 */
	public function postSave()
	{
		if ($this->id_product_attribute == 0)
			return true;

		$total_quantity = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT SUM(quantity) as quantity
			FROM '._DB_PREFIX_.'stock_available
			WHERE id_product = '.(int)$this->id_product.'
			AND id_product_attribute <> 0 '.
			StockAvailable::addSqlShopRestriction(null, $this->id_shop)
		);

		$this->setQuantity($this->id_product, 0, $total_quantity, $this->id_shop);

		return true;
	}

	/**
	 * For a given id_product and id_product_attribute updates the quantity available
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute Optional
	 * @param int $delta_quantity The delta quantity to update
	 * @param int $id_shop Optional
	 */
	public static function updateQuantity($id_product, $id_product_attribute, $delta_quantity, $id_shop = null)
	{
		$id_stock_available = StockAvailable::getStockAvailableIdByProductId($id_product, $id_product_attribute, $id_shop);

		if (!$id_stock_available)
			return false;

		// Update quantity of the pack products
		if (Pack::isPack($id_product))
		{
			$products_pack = Pack::getItems($id_product, (int)Configuration::get('PS_LANG_DEFAULT'));
			foreach ($products_pack as $product_pack)
			{
				$pack_id_product_attribute = Product::getDefaultAttribute($product_pack->id, 1);
				StockAvailable::updateQuantity($product_pack->id, $pack_id_product_attribute, $product_pack->pack_quantity * $delta_quantity, $id_shop);
			}
		}

		$stock_available = new StockAvailable($id_stock_available);
		$stock_available->quantity = $stock_available->quantity + $delta_quantity;
		$stock_available->update();

		$id_lang = Context::getContext()->language->id;
		$product = new Product($id_product, true, $id_lang, $id_shop, Context::getContext());

		if ($id_product_attribute != 0)
			Hook::exec('actionUpdateQuantity', array('product' => $product, 'attribute_id' => $id_product_attribute));
		else
			Hook::exec('actionProductUpdate', array('product' => $product));
	}


	/**
	 * For a given id_product and id_product_attribute sets the quantity available
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute Optional
	 * @param int $delta_quantity The delta quantity to update
	 * @param int $id_shop Optional
	 */
	public static function setQuantity($id_product, $id_product_attribute, $quantity, $id_shop = null)
	{
		$context = Context::getContext();

		// if there is no $id_shop, gets the context one
		if (is_null($id_shop))
			$id_shop = (int)$context->shop->id;

		$id_lang = Context::getContext()->language->id;
		$depends_on_stock = StockAvailable::dependsOnStock($id_product);

		//Try to set available quantitiy if product does not depend on physical stock
		if (!$depends_on_stock)
		{
			$id_stock_available = (int)StockAvailable::getStockAvailableIdByProductId($id_product, $id_product_attribute, $id_shop);
			
			$product = new Product($id_product, true, $id_lang, $id_shop, $context);
			Hook::exec('actionUpdateQuantity', array('product' => $product, 'attribute_id' => $id_product_attribute));

			if ($id_stock_available)
			{
				$stock_available = new StockAvailable($id_stock_available);
				$stock_available->quantity = (int)$quantity;
				$stock_available->update();
			}
			else
			{
				$out_of_stock = StockAvailable::outOfStock($id_product, $id_shop);

				$stock_available = new StockAvailable();
				$stock_available->out_of_stock = (int)$out_of_stock;
				$stock_available->id_product = (int)$id_product;
				$stock_available->id_product_attribute = (int)$id_product_attribute;
				$stock_available->quantity = (int)$quantity;

				// if we are in group_shop context
				if (Shop::getContext() == Shop::CONTEXT_GROUP)
				{
					$group_shop = $context->shop->getGroup();

					// if quantities are shared between shops of the group
					if ($group_shop->share_stock)
					{
						$stock_available->id_shop = 0;
						$stock_available->id_group_shop = (int)$group_shop->id;
					}
				}
				else
				{
					$stock_available->id_shop = $id_shop;
					$stock_available->id_group_shop = 0;
				}

				$stock_available->add();

			}
		}
		else
		{
			$product = new Product($id_product, true, $id_lang, $id_shop, $context);

			if ($id_product_attribute != 0)
				Hook::exec('actionUpdateQuantity', array('product' => $product, 'attribute_id' => $id_product_attribute));
			else
				Hook::exec('actionProductUpdate', array('product' => $product));
		}
		
	}

	/**
	 * Removes a given product from the stock available
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute Optional
	 * @param int $id_shop Optional
	 */
	public static function removeProductFromStockAvailable($id_product, $id_product_attribute = null, $id_shop = null)
	{
		return Db::getInstance()->execute('
			DELETE FROM '._DB_PREFIX_.'stock_available
			WHERE id_product = '.(int)$id_product.
			($id_product_attribute ? ' AND id_product_attribute = '.(int)$id_product_attribute : '').
			StockAvailable::addSqlShopRestriction(null, $id_shop)
		);
	}

	/**
	 * Removes all product quantities from all a group of shops
	 * If stocks are shared, remoe all old available quantities for all shops of the group
	 * Else remove all available quantities for the current group
	 *
	 * @param GroupShop $group_shop the GroupShop object
	 */
	public static function resetProductFromStockAvailableByGroupShop($group_shop)
	{
		if ($group_shop->share_stock)
		{
			$shop_list = Shop::getIdShopsByIdGroupShop($group_shop->id);

			if (count($shop_list) > 0)
			{
				$id_shops_list = implode(', ', $shop_list);

				return Db::getInstance()->execute('
					DELETE FROM '._DB_PREFIX_.'stock_available
					WHERE id_shop IN ('.$id_shops_list.')'
				);
			}
		}
		else
		{
			return Db::getInstance()->execute('
				DELETE FROM '._DB_PREFIX_.'stock_available
				WHERE id_group_shop = '.$group_shop->id
			);
		}
	}

	/**
	 * For a given product, tells if it depends on the physical (usable) stock
	 *
	 * @param int $id_product
	 * @param int $id_shop Optional : gets context if null @see Context::getContext()
	 * @return bool : depends on stock @see $depends_on_stock
	 */
	public static function dependsOnStock($id_product, $id_shop = null)
	{
		if (is_null($id_shop))
			$id_shop = Context::getContext()->shop->id;

		$query = new DbQuery();
		$query->select('depends_on_stock');
		$query->from('stock_available');
		$query->where('id_product = '.(int)$id_product);
		$query->where('id_product_attribute = 0');

		$query = StockAvailable::addSqlShopRestriction($query, $id_shop);

		return (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}

	/**
	 * For a given product, get its "out of stock" flag
	 *
	 * @param int $id_product
	 * @param int $id_shop Optional : gets context if null @see Context::getContext()
	 * @return bool : depends on stock @see $depends_on_stock
	 */
	public static function outOfStock($id_product, $id_shop = null)
	{
		if (is_null($id_shop))
			$id_shop = Context::getContext()->shop->id;

		$query = new DbQuery();
		$query->select('out_of_stock');
		$query->from('stock_available');
		$query->where('id_product = '.(int)$id_product);
		$query->where('id_product_attribute = 0');

		$query = StockAvailable::addSqlShopRestriction($query, $id_shop);

		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}

	/**
	 * Add an sql restriction for shops fields - specific to StockAvailable
	 *
	 * @param DbQuery $query Reference to the query object
	 * @param int $id_shop Optional : The shop ID
	 * @param string $alias Optional : The current table alias
	 *
	 * @return mixed the DbQuery object or the sql restriction string
	 */
	public static function addSqlShopRestriction(DbQuery $sql = null, $id_shop = null, $alias = null)
	{
		$context = Context::getContext();

		if (!empty($alias))
			$alias .= '.';

		// if there is no $id_shop, gets the context one
		if (is_null($id_shop))
			$id_shop = $context->shop->id;

		// if we are in group_shop context
		$group_shop = $context->shop->getGroup();

		// if quantities are shared between shops of the group
		if ($group_shop->share_stock)
		{
			if (is_object($sql))
			{
				$sql->where(pSQL($alias).'id_group_shop = '.(int)$group_shop->id);
				$sql->where(pSQL($alias).'id_shop = 0');
			}
			else
			{
				$sql = ' AND '.pSQL($alias).'id_group_shop = '.(int)$group_shop->id.' ';
				$sql .= ' AND '.pSQL($alias).'id_shop = 0 ';
			}
		}
		// else if we are in group context
		else if (Shop::getContext() == Shop::CONTEXT_GROUP)
		{
			if (is_object($sql))
				$sql->where(pSQL($alias).'id_shop IN ('.implode(', ', Shop::getShops(true, $group_shop->id, true)).')');
			else
				$sql = ' AND '.pSQL($alias).'id_shop IN ('.implode(', ', Shop::getShops(true, $group_shop->id, true)).') ';
		}
		// if no group specific restriction, set simple shop restriction
		else
		{
			if (is_object($sql))
				$sql->where(pSQL($alias).'id_shop = '.(int)$id_shop);
			else
				$sql = ' AND '.pSQL($alias).'id_shop = '.(int)$id_shop.' ';
		}

		return $sql;
	}

	/**
	 * Add sql params for shops fields - specific to StockAvailable
	 *
	 * @param array $params Reference to the params array
	 * @param int $id_shop Optional : The shop ID
	 *
	 */
	public static function addSqlShopParams(&$params, $id_shop = null)
	{
		$context = Context::getContext();
		$group_ok = false;

		// if there is no $id_shop, gets the context one
		if (is_null($id_shop))
			$id_shop = $context->shop->id;

		$group_shop = $context->shop->getGroup();

		// if quantities are shared between shops of the group
		if ($group_shop->share_stock)
		{
			$params['id_group_shop'] = (int)$group_shop->id;
			$params['id_shop'] = 0;

			$group_ok = true;
		}
		else
			$params['id_group_shop'] = 0;

		// if no group specific restriction, set simple shop restriction
		if (!$group_ok)
			$params['id_shop'] = (int)$id_shop;
	}
}
