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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class OrderDetailCore extends ObjectModel
{
	/** @var integer */
	public $id_order_detail;
	
	/** @var integer */
	public $id_order;
	
	/** @var integer */
	public $product_id;
	
	/** @var integer */
	public $product_attribute_id;

	/** @var string */
	public $product_name;

	/** @var integer */
	public $product_quantity;

	/** @var integer */
	public $product_quantity_in_stock;
	
	/** @var integer */
	public $product_quantity_return;
	
	/** @var integer */
	public $product_quantity_refunded;

	/** @var integer */
	public $product_quantity_reinjected;

	/** @var float */
	public $product_price;
	
	/** @var float */
	public $reduction_percent;
	
	/** @var float */
	public $reduction_amount;
	
	/** @var float */
	public $group_reduction;
	
	/** @var float */
	public $product_quantity_discount;
	
	/** @var string */
	public $product_ean13;
	
	/** @var string */
	public $product_upc;
	
	/** @var string */
	public $product_reference;
	
	/** @var string */
	public $product_supplier_reference;
	
	/** @var float */
	public $product_weight;
	
	/** @var string */
	public $tax_name;

	/** @var float */
	public $tax_rate;
	
	/** @var float */
	public $ecotax;

	/** @var float */
	public $ecotax_tax_rate;
	
	/** @var integer */
	public $discount_quantity_applied;

	/** @var string */
	public $download_hash;
	
	/** @var integer */
	public $download_nb;
	
	/** @var date */
	public $download_deadline;

	protected $tables = array ('order_detail');

	protected	$fieldsRequired = array ('id_order', 'product_name', 'product_quantity', 'product_price', 'tax_rate');

	protected	$fieldsValidate = array (
	'id_order' => 'isUnsignedId',
	'product_id' => 'isUnsignedId',
	'product_attribute_id' => 'isUnsignedId',
	'product_name' => 'isGenericName',
	'product_quantity' => 'isInt',
	'product_quantity_in_stock' => 'isInt',
	'product_quantity_return' => 'isUnsignedInt',
	'product_quantity_refunded' => 'isUnsignedInt',
	'product_quantity_reinjected' => 'isUnsignedInt',
	'product_price' => 'isPrice',
	'reduction_percent' => 'isFloat',
	'reduction_amount' => 'isPrice',
	'group_reduction' => 'isFloat',
	'product_quantity_discount' => 'isFloat',
	'product_ean13' => 'isEan13',
	'product_upc' => 'isUpc',
	'product_reference' => 'isReference',
	'product_supplier_reference' => 'isReference',
	'product_weight' => 'isFloat',
	'tax_name' => 'isGenericName',
	'tax_rate' => 'isFloat',
	'ecotax' => 'isFloat',
	'ecotax_tax_rate' => 'isFloat',
	'download_nb' => 'isInt',
	);
	
	protected 	$table = 'order_detail';
	protected 	$identifier = 'id_order_detail';
	
	protected	$webserviceParameters = array(
	'fields' => array (
		'id_order' => array('xlink_resource' => 'orders'),
		'product_id' => array('xlink_resource' => 'products'),
		'product_attribute_id' => array('xlink_resource' => 'product_attributes'),
		'product_quantity_reinjected' => array(),
		'group_reduction' => array(),
		'discount_quantity_applied' => array(),
		'download_hash' => array(),
		'download_deadline' => array()
		)
	);
	
	
	public function getFields()
	{
		parent::validateFields();

		$fields['id_order'] = (int)($this->id_order);
		$fields['product_id'] = (int)($this->product_id);
		$fields['product_attribute_id'] = (int)($this->product_attribute_id);
		$fields['product_name'] = pSQL($this->product_name);
		$fields['product_quantity'] = (int)($this->product_quantity);
		$fields['product_quantity_in_stock'] = (int)($this->product_quantity_in_stock);
		$fields['product_quantity_return'] = (int)($this->product_quantity_return);
		$fields['product_quantity_refunded'] = (int)($this->product_quantity_refunded);
		$fields['product_quantity_reinjected'] = (int)($this->product_quantity_reinjected);
		$fields['product_price'] = (float)($this->product_price);
		$fields['reduction_percent'] = (float)($this->reduction_percent);
		$fields['reduction_amount'] = (float)($this->reduction_amount);
		$fields['group_reduction'] = (float)($this->group_reduction);
		$fields['product_quantity_discount'] = (float)($this->product_quantity_discount);
		$fields['product_ean13'] = pSQL($this->product_ean13);
		$fields['product_upc'] = pSQL($this->product_upc);
		$fields['product_reference'] = pSQL($this->product_reference);
		$fields['product_supplier_reference'] = pSQL($this->product_reference);
		$fields['product_weight'] = (float)($this->product_weight);
		$fields['tax_name'] = pSQL($this->tax_name);
		$fields['tax_rate'] = (float)($this->tax_rate);
		$fields['ecotax'] = (float)($this->ecotax);
		$fields['ecotax_tax_rate'] = (float)($this->ecotax_tax_rate);
		$fields['download_hash'] = pSQL($this->download_hash);
		$fields['download_nb'] = (int)($this->download_nb);
		$fields['download_deadline'] = pSQL($this->download_deadline);
		
		return $fields;
	}	

	static public function getDownloadFromHash($hash)
	{
		if ($hash == '') return false;
		$sql = 'SELECT *
		FROM `'._DB_PREFIX_.'order_detail` od
		LEFT JOIN `'._DB_PREFIX_.'product_download` pd ON (od.`product_id`=pd.`id_product`)
		WHERE od.`download_hash` = \''.pSQL(strval($hash)).'\'
		AND pd.`active` = 1';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
	}

	static public function incrementDownload($id_order_detail, $increment=1)
	{
		$sql = 'UPDATE `'._DB_PREFIX_.'order_detail`
			SET `download_nb` = `download_nb` + '.(int)($increment).'
			WHERE `id_order_detail`= '.(int)($id_order_detail).'
			LIMIT 1';
		return Db::getInstance()->Execute($sql);
	}

}


