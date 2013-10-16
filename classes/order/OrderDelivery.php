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
	
	public $delivery_number;

	public static $definition = array(
		'table' => 'order_delivery',
		'primary' => 'delivery_id',
		'fields' => array(
			'id_order' => 					array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_order_invoice' => 					array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_shop' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'delivery_number' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'date_add' => 			array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
			'shipped' => 			array('type' => self::TYPE_INT),
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

	public function getNextSlipNr($order)
	{
		$nr = Db::getInstance()->executeS('SELECT MAX(`delivery_number`) as delivery_number
		FROM `'._DB_PREFIX_.'order_delivery`
		WHERE `id_order` = ' . (int)$order->id . ' AND `id_shop` = ' . (int)$order->id_shop );
		$nr = $nr[0]['delivery_number'];

		$shipped = 0;
		if($nr != "") {
			$shipped = Db::getInstance()->executeS('
			SELECT `shipped`
			FROM `'._DB_PREFIX_.'order_delivery`
			WHERE `delivery_number` = ' . $nr . ' AND `id_order` = ' . (int)$order->id . ' AND `id_shop` = ' . (int)$order->id_shop );
			$shipped = $shipped[0]['shipped'];
		}

		if($nr == "")
			$nr = 1; // if no number was found, then change to default 1

		if($shipped == 1)
			$nr++; // if maximum delivery nr is marked as shipped, then we need to increase the delivery nr, so that a new delivery is created.

		return $nr;
	}
	
	public function getNrFromId($id) {
		$nr = Db::getInstance()->executeS('
		SELECT delivery_number
		FROM `'._DB_PREFIX_.'order_delivery` ody
		WHERE ody.`delivery_id` = ' . $id
		);
		return $nr[0]['delivery_number'];
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
	 * Retrive id nr for order if delivery_number is matched
	 * 
	 * @since 
	 * @param $delivery_number
	 * @return delivery_id
	 */
	public function getIdFromNr($delivery_number,$Id_order,$id_shop)
	{
		$sql = '
		SELECT delivery_id
		FROM `' . _DB_PREFIX_ . 'order_delivery` ody
		WHERE ody.`id_order` = ' . (int)$id_order . ' AND ody.`id_shop` = ' . (int)$id_shop . ' AND ody.`delivery_number` = ' . $delivery_number;
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
	
	/**
	 * This method allows to generate first invoice of the current order
	 */
	public function setInvoice($order,$use_existing_payment=false)
	{
			$order_invoice = new OrderInvoice();
			$order_invoice->id_order = $this->id;
			$order_invoice->number = Configuration::get('PS_INVOICE_START_NUMBER', null, null, $this->id_shop);
			// If invoice start number has been set, you clean the value of this configuration
			if ($order_invoice->number)
				Configuration::updateValue('PS_INVOICE_START_NUMBER', false, false, null, $this->id_shop);
			else
				$order_invoice->number = Order::getLastInvoiceNumber() + 1;

			$invoice_address = new Address((int)$this->id_address_invoice);
			$carrier = new Carrier((int)$this->id_carrier);
			$tax_calculator = $carrier->getTaxCalculator($invoice_address);
			
			// TODO: This needs to be rewritten to use order_delivery_detail's
			// on OrdersController line 1607 is addProduct. It uses an cart to create the invoice.
			// that might actually be an better idea.
			$order_invoice->total_discount_tax_excl = $this->total_discounts_tax_excl;
			$order_invoice->total_discount_tax_incl = $this->total_discounts_tax_incl;
			$order_invoice->total_paid_tax_excl = $this->total_paid_tax_excl;
			$order_invoice->total_paid_tax_incl = $this->total_paid_tax_incl;
			$order_invoice->total_products = $this->total_products;
			$order_invoice->total_products_wt = $this->total_products_wt;
			$order_invoice->total_shipping_tax_excl = $this->total_shipping_tax_excl;
			$order_invoice->total_shipping_tax_incl = $this->total_shipping_tax_incl;
			$order_invoice->shipping_tax_computation_method = $tax_calculator->computation_method;
			$order_invoice->total_wrapping_tax_excl = $this->total_wrapping_tax_excl;
			$order_invoice->total_wrapping_tax_incl = $this->total_wrapping_tax_incl;

			// Save Order invoice
			$order_invoice->add();

			$order_invoice->saveCarrierTaxCalculator($tax_calculator->getTaxesAmount($order_invoice->total_shipping_tax_excl));

			// Update order_carrier
			$id_order_carrier = Db::getInstance()->getValue('
				SELECT `id_order_carrier`
				FROM `'._DB_PREFIX_.'order_carrier`
				WHERE `id_order` = '.(int)$order_invoice->id_order.'
				AND (`id_order_invoice` IS NULL OR `id_order_invoice` = 0)');
			
			if ($id_order_carrier)
			{
				$order_carrier = new OrderCarrier($id_order_carrier);
				$order_carrier->id_order_invoice = (int)$order_invoice->id;
				$order_carrier->update();
			}

			// Update order detail
			Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'order_detail`
				SET `id_order_invoice` = '.(int)$order_invoice->id.'
				WHERE `id_order` = '.(int)$order_invoice->id_order);

			// Update order payment
			if ($use_existing_payment)
			{
				$id_order_payments = Db::getInstance()->executeS('
					SELECT DISTINCT op.id_order_payment 
					FROM `'._DB_PREFIX_.'order_payment` op
					INNER JOIN `'._DB_PREFIX_.'orders` o ON (o.reference = op.order_reference)
					LEFT JOIN `'._DB_PREFIX_.'order_invoice_payment` oip ON (oip.id_order_payment = op.id_order_payment)					
					WHERE (oip.id_order != '.(int)$order_invoice->id_order.' OR oip.id_order IS NULL) AND o.id_order = '.(int)$order_invoice->id_order);

				if (count($id_order_payments))
				{
					foreach ($id_order_payments as $order_payment)
						Db::getInstance()->execute('
							INSERT INTO `'._DB_PREFIX_.'order_invoice_payment`
							SET
								`id_order_invoice` = '.(int)$order_invoice->id.',
								`id_order_payment` = '.(int)$order_payment['id_order_payment'].',
								`id_order` = '.(int)$order_invoice->id_order);
					// Clear cache
					Cache::clean('order_invoice_paid_*');
				}
			}

			// Update order cart rule
			Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'order_cart_rule`
				SET `id_order_invoice` = '.(int)$order_invoice->id.'
				WHERE `id_order` = '.(int)$order_invoice->id_order);

			// Keep it for backward compatibility, to remove on 1.6 version
			$this->invoice_date = $order_invoice->date_add;
			$this->invoice_number = $order_invoice->number;
			$this->update();
	}
	
	public function createDelivery($delivery_number,$order,$product_id,$product_attribute_id,$qty,$auto_detail = true)
	{
		// First create an invoice.
			
		//Now create delivery
		Db::getInstance()->insert('order_delivery',array(
		'id_order' => (int)$order->id,
		'id_shop' => (int)$order->id_shop,
		'id_order_invoice' => '',
		'delivery_number' => $delivery_number,
		'date_add' => date("Y-m-d h:i:s")
		) );
		$delivery_id = Db::getInstance()->Insert_ID();
		/*
			Here we should add an function to create an invoice.
		*/
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

	public function setPartiallyShipped($delivery_id)
	{
		Db::getInstance()->update('order_delivery',array('shipped' => 1 ), '`delivery_id` = ' . $delivery_id ); // set delivery id as shipped
	}

	public function getShippedByNr($delivery_number,$id_order)
	{
		$ship = Db::getInstance()->executeS(
		'SELECT `shipped`
		FROM `'._DB_PREFIX_.'order_delivery`
		WHERE `delivery_number` = ' . $delivery_number . ' AND `id_order` = ' . $id_order);
		if($ship)
			return $ship[0]['shipped'];
	}

	public function getDeliveryDate($delivery_number,$id_order)
	{
		$date = Db::getInstance()->executeS(
		'SELECT `delivery_date`
		FROM `'._DB_PREFIX_.'order_delivery`
		WHERE `delivery_number` = ' . $delivery_number . ' AND `id_order` = ' . $id_order);
		if($date)
			return $date[0]['delivery_date'];
	}

}
