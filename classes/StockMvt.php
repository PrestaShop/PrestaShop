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
*  @version  Release: $Revision: 7307 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class StockMvtCore extends ObjectModel
{
	public		$id;

	/**
	 * @since 1.5.0 id_stock replace old id_product and id_product_attribute fields in this table
	 * @var int
	 */
	public		$id_stock;

	public 		$id_order = NULL;
	public 		$id_employee = NULL;
	public 		$quantity;
	public 		$id_stock_mvt_reason;

	public		$date_add;
	public		$date_upd;

	protected	$table = 'stock_mvt';
	protected 	$identifier = 'id_stock_mvt';

 	protected 	$fieldsRequired = array('id_stock', 'id_stock_mvt_reason', 'quantity');
 	protected 	$fieldsValidate = array(
 					'id_stock' => 'isUnsignedId',
 					'id_order' => 'isUnsignedId',
 					'id_employee' => 'isUnsignedId',
 					'quantity' => 'isInt',
 					'id_stock_mvt_reason' => 'isUnsignedId',
 				);

	protected	$webserviceParameters = array(
		'objectsNodeName' => 'stock_movements',
		'objectNodeName' => 'stock_movement',
		'fields' => array(
			'id_product' => array('xlink_resource'=> 'products'),
			'id_product_attribute' => array('xlink_resource'=> 'product_option_values'),
			'id_order' => array('xlink_resource'=> 'orders'),
			'id_employee' => array('xlink_resource'=> 'employees'),
			'id_stock_mvt_reason' => array('xlink_resource'=> 'stock_movement_reasons'),
		),
	);

	public function getFields()
	{
		parent::validateFields();
		$fields['id_stock'] = (int)$this->id_stock;
		$fields['id_order'] = (int)$this->id_order;
		$fields['id_employee'] = (int)$this->id_employee;
		$fields['id_stock_mvt_reason'] = (int)$this->id_stock_mvt_reason;
		$fields['quantity'] = (int)$this->quantity;
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);
		return $fields;
	}

	/**
	 * Add missing stock movement (compare the quantity of a products with all movements : if there is a difference, create a stock movement to correct it)
	 * 
	 * @param int $id_employee
	 */
	public static function addMissingMvt($id_employee)
	{
		// Search missing stock movement on products without attributes
		$sql = 'SELECT s.id_stock, (s.quantity - SUM(IFNULL(sm.quantity, 0))) AS qty
				FROM '._DB_PREFIX_.'product p
				INNER JOIN '._DB_PREFIX_.'stock s ON s.id_product = p.id_product
				LEFT JOIN '._DB_PREFIX_.'stock_mvt sm ON s.id_stock = sm.id_stock
				WHERE (
					SELECT COUNT(*) FROM '._DB_PREFIX_.'stock s2
					WHERE s2.id_product = p.id_product
						AND s2.id_product_attribute > 0
				) = 0
					'.Shop::sqlSharedStock('s').'
				GROUP BY s.id_product, s.id_shop
				HAVING qty <> 0';
		$products_without_attributes = Db::getInstance()->ExecuteS($sql);

		// Search missing stock movement on products with attributes
		$sql = 'SELECT s.id_stock, (s.quantity - SUM(IFNULL(sm.quantity, 0))) AS qty
				FROM '._DB_PREFIX_.'product_attribute pa
				INNER JOIN '._DB_PREFIX_.'stock s ON s.id_product = pa.id_product AND s.id_product_attribute = pa.id_product_attribute
				LEFT JOIN '._DB_PREFIX_.'stock_mvt sm ON s.id_stock = sm.id_stock
				WHERE s.id_product_attribute > 0
					AND (
						SELECT COUNT(*) FROM '._DB_PREFIX_.'stock s2
						WHERE s2.id_product = pa.id_product
							AND s2.id_product_attribute > 0
					) > 0
				GROUP BY s.id_product_attribute
				HAVING qty <> 0';
		$products_with_attributes = Db::getInstance()->ExecuteS($sql);

		// Add missing stock movements
		$products = array_merge($products_without_attributes, $products_with_attributes);
		if ($products)
			foreach ($products AS $product)
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