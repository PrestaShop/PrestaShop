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
 * Object to manage sto
 * 
 * @since 1.5.0
 */
class StockAvailableCore extends ObjectModel
{
	public $id_product;
	public $id_product_attribute;
	public $id_shop;
	public $quantity;
	public $depends_on_stock;
	/** @var integer Out of stock behavior */
	public $out_of_stock;

	protected $fieldsRequired = array(
		'id_product',
		'id_product_attribute',
		'id_shop',
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
		'depends_on_stocks' => 'isBool',
		'out_of_stock' => 'isBool'
	);

	protected $table = 'stock_available';
	protected $identifier = 'id_stock_available';

	public function getFields()
	{
		$this->validateFields();
		$fields['id_product'] = (int)$this->id_product;
		$fields['id_product_attribute'] = (int)$this->id_product_attribute;
		$fields['id_shop'] = (int)$this->id_shop;
		$fields['quantity'] = (int)$this->quantity;
		$fields['depends_on_stocks'] = (boolean)$this->depends_on_stocks;
		$fields['out_of_stock'] = (boolean)$this->out_of_stock;
		return $fields;
	}

	/**
	 * For a given id_product, id_product_attribute and id_shop, get the stock id
	 *
	 * @return int id_stock_available
	 */
	public static function getIdStockAvailable($id_product, $id_product_attribute = null, $id_shop)
	{
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
	 * For a given product, refresh stock available
	 */
	public static function refreshStockAvailable($id_product)
	{
		$query = new DbQuery();
		$query->select('id_wherehouse, id_shop');
		$query->from('warehouse_shop');
		$query->orderBy('id_shop');
		
		// Gat all warehouse id group by shop id
		$warehouse_id_list_list = array();
		foreach (Db::getInstance()->ExecuteS($query) as $row)
		{
			if (!isset($warehouse_id_list_list[$row['id_shop']]))
				$warehouse_id_list_list[$row['id_shop']] = array();
			$warehouse_id_list_list[$row['id_shop']][] = $row['id_warehouse'];
		}
		
		// Get all id product attribute
		$id_product_attribute_list = array();
		foreach (Product::getProductAttributesIds($id_product) as $id_product_attribute)
			$id_product_attribute_list[] = $id_product_attribute['id_product_attribute'];
		
		foreach ($warehouse_id_list_list as $id_shop => $warehouse_id_list)
		{
			$totalQuantity = 0;
			// Saving qunatity of all product combination
			foreach ($id_product_attribute_list as $id_product_attribute)
			{
				$quantity = StockManagerFactory::getManager()->getProductRealQuantities($id_product, $id_product_attribute, $warehouse_id_list, true);
				Db::getInstance()->autoExecute(
					'stock_available',
					array('quantity' => $quantity),
					'UPDATE',
					'id_product = '.(int)$id_product.' AND id_product_attribute = '.(int)$id_product_attribute.' AND id_shop = '.(int)$id_shop);
				$totalQuantity += $quantity;
			}
			
			if (empty($id_product_attribute_list))
				$totalQuantity = StockManagerFactory::getManager()->getProductRealQuantities($id_product, null, $warehouse_id_list, true);
			
			// Saving the total quantity of stock available for a product with all all of its combinations
			Db::getInstance()->autoExecute(
				'stock_available',
				array('quantity' => $totalQuantity),
				'UPDATE',
				'id_product = '.(int)$id_product.' AND id_product_attribute = 0 AND id_shop = '.(int)$id_shop);
			$totalQuantity += $quantity;
		}
	}
}