<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class OrderDeliveryCore extends ObjectModel
{

	/** @var integer */
	public $id_order;
	
	/** @var integer */
	public $id_shop;
	
	/** @var integer */
	public $delivery_id;
	
	public $delivery_date;
	
	public $delivery_nr;

	public static $definition = array(
		'table' => 'order_delivery',
		'primary' => 'delivery_id',
		'fields' => array(
			'id_order' => 					array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_shop' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'delivery_nr' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'delivery_date' => 			array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
		),
	);

	public function __construct($id = null, $id_lang = null, $context = null)
	{
		$this->context = $context;
		$id_shop = null;
		if ($this->context != null && isset($this->context->shop))
			$id_shop = $this->context->shop->id;
		parent::__construct($id, $id_lang, $id_shop);

		if ($context == null)
			$context = Context::getContext();
		$this->context = $context->cloneContext();
	}

	public function getMaxNr($order)
	{
		$nr = Db::getInstance()->executeS('
		SELECT MAX(delivery_nr) as delivery_nr
		FROM `'._DB_PREFIX_.'order_delivery` ody
		WHERE ody.`id_order` = ' . (int)$order->id . ' AND ody.`id_shop` = ' . (int)$order->id_shop);
		$nr = $nr[0]['delivery_nr'];
		if($nr == "") { // if no number was found, then change to default 1
			$nr = 1;
		}
		return $nr;
	}
	
	public function getNrFromId($id) {
		$nr = Db::getInstance()->executeS('
		SELECT delivery_nr
		FROM `'._DB_PREFIX_.'order_delivery` ody
		WHERE ody.`delivery_id` = ' . $id
		);
		return $nr[0]['delivery_nr'];
	}

	public function getIds($order)
	{
		return Db::getInstance()->executeS('
		SELECT *
		FROM `' . _DB_PREFIX_ . 'order_delivery` ody
		WHERE ody.`id_order` = ' . (int)$order->id . ' AND ody.`id_shop` = ' . (int)$order->id_shop);
	}

	public function getProductQty($product_id,$product_attribute_id,$delivery_id)
	{
		$qty = Db::getInstance()->executeS('
		SELECT delivery_qty
		FROM `'._DB_PREFIX_.'order_delivery_detail` odyd
		WHERE odyd.`product_id` = ' . $product_id .
		' AND odyd.`product_attribute_id` = ' . $product_attribute_id .
		' AND odyd.`delivery_id` = ' . $delivery_id);
		return $qty[0]["delivery_qty"];
	}
	
	/**
	 * Retrive id nr for order if delivery_nr is matched
	 * 
	 * @since 
	 * @param $delivery_nr
	 * @return delivery_id
	 */
	public function getIdFromNr($delivery_nr,$order)
	{
		$sql = '
		SELECT delivery_id
		FROM `' . _DB_PREFIX_ . 'order_delivery` ody
		WHERE ody.`id_order` = ' . (int)$order->id . ' AND ody.`id_shop` = ' . (int)$order->id_shop . ' AND ody.`delivery_nr` = ' . $delivery_nr;
		$id = Db::getInstance()->executeS($sql);
		if(isset($id[0])) {
			return $id[0]["delivery_id"];
		}
		else
		{
			return false;
		}
	}
	
	public function updateQty($product_id,$product_attribute_id,$delivery_id,$new_qty) {
		Db::getInstance()->update('order_delivery_detail',array('delivery_qty' => $new_qty),
		'`product_id` = ' . $product_id . ' AND `product_attribute_id` = '. $product_attribute_id .' AND `delivery_id` = ' . $delivery_id);
		Db::getInstance()->update('order_delivery',array('delivery_date' => date('Y-m-d H:i:s') ), '`delivery_id` = ' . $delivery_id ); // update delivery date when adding product
	}
	
	
	
	public function createDelivery($delivery_nr,$order,$product_id,$product_attribute_id,$qty,$auto_detail = true)
	{
		Db::getInstance()->insert('order_delivery',array('id_order' => (int)$order->id,'id_shop' => (int)$order->id_shop,'delivery_nr' => $delivery_nr) );
		$delivery_id = Db::getInstance()->Insert_ID();
		if($auto_detail) {
			$this->createDeliveryDetail($delivery_id,$product_id,$product_attribute_id,$qty);
		} else {
			return $delivery_id;
		}
	}
	
	public function createDeliveryDetail($delivery_id,$product_id,$product_attribute_id,$qty)
	{
		Db::getInstance()->insert('order_delivery_detail', array('product_id' => $product_id, 'product_attribute_id' => $product_attribute_id, 'delivery_id' => $delivery_id, 'delivery_qty' => $qty) );
		Db::getInstance()->update('order_delivery',array('delivery_date' => date('Y-m-d H:i:s') ), '`delivery_id` = ' . $delivery_id ); // update delivery date when adding product
	}
}
