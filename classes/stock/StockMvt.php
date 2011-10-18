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

class StockMvtCore extends ObjectModel
{
	public $id;
	public $date_add;
	public $id_employee;

	/**
	 * @since 1.5.0
	 * @var int
	 */
	public $id_stock;

	/**
	 * @since 1.5.0
	 * @var int
	 */
	public $physical_quantity;

	public $id_stock_mvt_reason;
	public $id_order = null;

	/**
	 * @since 1.5.0
	 * @var int
	 */
	public $sign;

	/**
	 * @since 1.5.0
	 * @var int Will be used when supplier order are implemented
	 */
	public $id_supplier_order = null;

	/**
	 * @since 1.5.0
	 * @var float Last value of the weighted-average method
	 */
	public $last_wa = null;

	/**
	 * @since 1.5.0
	 * @var float Current value of the weighted-average method
	 */
	public $current_wa = null;

	/**
	 * @since 1.5.0
	 * @var float
	 */
	public $price_te;

	/**
	 * @since 1.5.0
	 * @var int Refers to an other id_stock_mvt : used for LIFO/FIFO implementation in StockManager
	 */
	public $referer;

	/**
	 * @deprecated since 1.5.0
	 * @deprecated stock movement will not be updated anymore
	 */
	public $date_upd;

	/**
	 * @deprecated since 1.5.0
	 * @see physical_quantity
	 * @var int
	 */
	public $quantity;

	protected $table = 'stock_mvt';
	protected $identifier = 'id_stock_mvt';

 	protected $fieldsRequired = array(
 		'date_add',
 		'id_employee',
 		'id_stock',
 		'physical_quantity',
 		'id_stock_mvt_reason',
 		'sign',
 		'price_te'
 	);

 	protected $fieldsValidate = array(
 		'date_add' => 'isDate',
 		'id_employee' => 'isUnsignedId',
 		'id_stock' => 'isUnsignedId',
 		'physical_quantity' => 'isUnsignedInt',
 	 	'id_stock_mvt_reason' => 'isUnsignedId',
 		'id_order' => 'isUnsignedId',
 		'sign' => 'isInt',
 		'last_wa' => 'isPrice',
 		'current_wa' => 'isPrice',
 		'price_te' => 'isPrice',
 		'referer' => 'isUnsignedId'
 	);

	protected $webserviceParameters = array(
		'objectsNodeName' => 'stock_movements',
		'objectNodeName' => 'stock_movement',
		'fields' => array(
			'id_employee' => array('xlink_resource'=> 'employees'),
			'id_stock' => array('xlink_resource'=> 'stock'),
			'id_stock_mvt_reason' => array('xlink_resource'=> 'stock_movement_reasons'),
			'id_order' => array('xlink_resource'=> 'orders')
		),
	);

	public function getFields()
	{
		$this->validateFields();
		$fields['date_add'] = pSQL($this->date_add);
		$fields['id_employee'] = (int)$this->id_employee;
		$fields['id_stock'] = (int)$this->id_stock;
		$fields['physical_quantity'] = (int)$this->physical_quantity;
		$fields['id_stock_mvt_reason'] = (int)$this->id_stock_mvt_reason;
		$fields['id_order'] = (int)$this->id_order;
		$fields['sign'] = (int)$this->sign;
		$fields['last_wa'] = (float)round($this->last_wa, 6);
		$fields['current_wa'] = (float)round($this->current_wa, 6);
		$fields['price_te'] = (float)round($this->price_te, 6);
		$fields['referer'] = (int)$this->referer;
		return $fields;
	}

	/**
	 * @deprecated since 1.5.0
	 */
	public static function addMissingMvt($id_employee)
	{
		Tools::displayAsDeprecated();
		// Search missing stock movement on products without attributes
		$sql = 'SELECT s.id_stock, (stock.quantity - SUM(IFNULL(sm.quantity, 0))) AS qty
				FROM '._DB_PREFIX_.'product p
				'.Product::sqlStock('p', null, true).'
				LEFT JOIN '._DB_PREFIX_.'stock_mvt sm ON s.id_stock = sm.id_stock
				WHERE (
					SELECT COUNT(*) FROM '._DB_PREFIX_.'stock s2
					WHERE s2.id_product = p.id_product
						AND s2.id_product_attribute > 0
				) = 0
				GROUP BY s.id_product, s.id_shop
				HAVING qty <> 0';
		$products_without_attributes = Db::getInstance()->executeS($sql);

		// Search missing stock movement on products with attributes
		$sql = 'SELECT s.id_stock, (stock.quantity - SUM(IFNULL(sm.quantity, 0))) AS qty
				FROM '._DB_PREFIX_.'product_attribute pa
				'.Product::sqlStock('pa', 'pa', true).'
				LEFT JOIN '._DB_PREFIX_.'stock_mvt sm ON s.id_stock = sm.id_stock
				WHERE s.id_product_attribute > 0
					AND (
						SELECT COUNT(*) FROM '._DB_PREFIX_.'stock s2
						WHERE s2.id_product = pa.id_product
							AND s2.id_product_attribute > 0
					) > 0
				GROUP BY s.id_product_attribute
				HAVING qty <> 0';
		$products_with_attributes = Db::getInstance()->executeS($sql);

		// Add missing stock movements
		$products = array_merge($products_without_attributes, $products_with_attributes);
		if ($products)
			foreach ($products as $product)
			{
				$mvt = new StockMvt();
				$mvt->id_stock = $product['id_stock'];
				$mvt->id_employee = (int)$id_employee;
				$mvt->quantity = $product['qty'];
				$mvt->id_stock_mvt_reason = _STOCK_MOVEMENT_MISSING_REASON_;
				$mvt->add();
			}
	}
}