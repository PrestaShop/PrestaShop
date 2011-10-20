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

	protected $fieldsRequired = array(
		'id_warehouse',
		'id_product',
		'id_product_attribute',
		'physical_quantity',
		'usable_quantity',
		'price_te',
	);

	protected $fieldsSize = array();

	protected $fieldsValidate = array(
		'id_warehouse' => 'isUnsignedId',
		'id_product' => 'isUnsignedId',
		'id_product_attribute' => 'isUnsignedId',
		'physical_quantity' => 'isUnsignedInt',
		'usable_quantity' => 'isInt',
		'price_te' => 'isPrice',
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
		$fields['price_te'] = (float)round($this->price_te, 6);
		return $fields;
	}
	
	/**
	 * For a given id_product and id_product_attribute update the quantity in stock
	 */
	public static function updateQuantity($id_product, $id_product_attribute, $delta_quantity, $id_warehouse, $id_order = null)
	{
		$warehouse = new Warehouse($id_warehouse);
		// Update quantity of the pack products
		if (Pack::isPack($id_product))
		{
			$products_pack = Pack::getItems((int)($product['id_product']), (int)(Configuration::get('PS_LANG_DEFAULT')));
			foreach ($products_pack as $product_pack)
			{
				$pack_id_product_attribute = Product::getDefaultAttribute($tab_product_pack['id_product'], 1);
				self::updateQuantity($product_pack->id, $pack_id_product_attribute, $product_pack->pack_quantity * $delta_quantity, $id_warehouse, $id_order);
			}
		}
		if ($delta_quantity > 0)
			$id_stock_mvt_reason = Configuration::get('PS_STOCK_MVT_INC_REASON_DEFAULT');
		else
			$id_stock_mvt_reason = Configuration::get('PS_STOCK_MVT_DEC_REASON_DEFAULT');
		
		StockManagerFactory::getManager()
			->removeProduct($id_product, $id_product_attribute, $warehouse, $delta_quantity, $id_stock_mvt_reason, true, (int)$id_order);
	}
}