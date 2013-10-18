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
	
	public $date_add;
	
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
	
	public function getInvoiceFromId($id) {
		$nr = Db::getInstance()->executeS('
		SELECT id_order_invoice
		FROM `'._DB_PREFIX_.'order_delivery` ody
		WHERE ody.`delivery_id` = ' . $id
		);
		return $nr[0]['id_order_invoice'];
	}

	public function getIds($id_order,$id_shop)
	{
		return Db::getInstance()->executeS('
		SELECT *
		FROM `' . _DB_PREFIX_ . 'order_delivery` ody
		WHERE ody.`id_order` = ' . $id_order . ' AND ody.`id_shop` = ' . $id_shop);
	}

	public function getProductQty($product_id,$product_attribute_id,$delivery_id)
	{
		$qty = 0;
		$qtyArray = Db::getInstance()->executeS('
		SELECT product_quantity
		FROM `'._DB_PREFIX_.'order_delivery_detail` odyd
		WHERE odyd.`product_id` = ' . $product_id .
		' AND odyd.`product_attribute_id` = ' . $product_attribute_id .
		' AND odyd.`delivery_id` = ' . $delivery_id);
		if(isset($qtyArray[0]) )
		{
			$qty = $qtyArray[0]["product_quantity"];
		}
		return $qty;
	}
	
	/**
	 * Retrive id nr for order if delivery_number is matched
	 * 
	 * @since 
	 * @param $delivery_number
	 * @return delivery_id
	 */
	public function getIdFromNr($delivery_number,$id_order,$id_shop)
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

	public function setInvoice($order,$product_id,$product_attribute_id,$qty,$id_order_invoice = 0,$delivery_number,$free_shipping = false)
	{
		// create the product
		$product = new Product($product_id, false, $order->id_lang);
		if (!Validate::isLoadedObject($product))
			die(Tools::jsonEncode(array(
				'result' => false,
				'error' => Tools::displayError('The product object cannot be loaded.')
			)));

		if (isset($product_attribute_id) && $product_attribute_id)
		{
			$combination = new Combination($product_attribute_id);
			if (!Validate::isLoadedObject($combination))
				die(Tools::jsonEncode(array(
				'result' => false,
				'error' => Tools::displayError('The combination object cannot be loaded.')
			)));
		}

		// Total method
		$total_method = Cart::BOTH_WITHOUT_SHIPPING;

		// Create new cart
		$cart = new Cart();
		$cart->id_shop_group = $order->id_shop_group;
		$cart->id_shop = $order->id_shop;
		$cart->id_customer = $order->id_customer;
		$cart->id_carrier = $order->id_carrier;
		$cart->id_address_delivery = $order->id_address_delivery;
		$cart->id_address_invoice = $order->id_address_invoice;
		$cart->id_currency = $order->id_currency;
		$cart->id_lang = $order->id_lang;
		$cart->secure_key = $order->secure_key;

		// Save new cart
		$cart->add();

		// Save context (in order to apply cart rule)
		$this->context->cart = $cart;
		$this->context->customer = new Customer($order->id_customer);

		// always add taxes even if there are not displayed to the customer
		$use_taxes = true;

		$initial_product_price_tax_incl = Product::getPriceStatic($product->id, $use_taxes, isset($combination) ? $combination->id : null, 2, null, false, true, 1,
			false, $order->id_customer, $cart->id, $order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)});

		// Add product to cart
		$update_quantity = $cart->updateQty($qty, $product->id, isset($product_attribute_id) ? $product_attribute_id : null,
			isset($combination) ? $combination->id : null, 'up', 0, new Shop($cart->id_shop));

			$order_invoice = new OrderInvoice($id_order_invoice);
			// Create new invoice
			if ($order_invoice->id == 0)
			{
				// If we create a new invoice, we calculate shipping cost
				$total_method = Cart::BOTH;

				$order_invoice->id_order = $order->id;
				if ($order_invoice->number)
					Configuration::updateValue('PS_INVOICE_START_NUMBER', false, false, null, $order->id_shop);
				else
					$order_invoice->number = Order::getLastInvoiceNumber() + 1;

				$invoice_address = new Address((int)$order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)});
				$carrier = new Carrier((int)$order->id_carrier);
				$tax_calculator = $carrier->getTaxCalculator($invoice_address);

				$order_invoice->total_paid_tax_excl = Tools::ps_round((float)$cart->getOrderTotal(false, $total_method), 2);
				$order_invoice->total_paid_tax_incl = Tools::ps_round((float)$cart->getOrderTotal($use_taxes, $total_method), 2);
				$order_invoice->total_products = (float)$cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
				$order_invoice->total_products_wt = (float)$cart->getOrderTotal($use_taxes, Cart::ONLY_PRODUCTS);
				if($free_shipping) {
					$order_invoice->total_shipping_tax_excl = 0;
					$order_invoice->total_shipping_tax_incl = 0;
				}
				else
				{
					$order_invoice->total_shipping_tax_excl = (float)$cart->getTotalShippingCost(null, false);
					$order_invoice->total_shipping_tax_incl = (float)$cart->getTotalShippingCost();
				}

				$order_invoice->total_wrapping_tax_excl = abs($cart->getOrderTotal(false, Cart::ONLY_WRAPPING));
				$order_invoice->total_wrapping_tax_incl = abs($cart->getOrderTotal($use_taxes, Cart::ONLY_WRAPPING));
				$order_invoice->shipping_tax_computation_method = (int)$tax_calculator->computation_method;
				$order_invoice->delivery_number = $delivery_number;

				$order_invoice->add();

				if(!$free_shipping) {
					$order_invoice->saveCarrierTaxCalculator($tax_calculator->getTaxesAmount($order_invoice->total_shipping_tax_excl));
				}

				return $order_invoice;
			}
			// Update current invoice
			else
			{
				$order_invoice->total_paid_tax_excl += Tools::ps_round((float)($cart->getOrderTotal(false, $total_method)), 2);
				$order_invoice->total_paid_tax_incl += Tools::ps_round((float)($cart->getOrderTotal($use_taxes, $total_method)), 2);
				$order_invoice->total_products += (float)$cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
				$order_invoice->total_products_wt += (float)$cart->getOrderTotal($use_taxes, Cart::ONLY_PRODUCTS);
				$order_invoice->update();
				return $order_invoice;
			}
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
		'SELECT `date_add`
		FROM `'._DB_PREFIX_.'order_delivery`
		WHERE `delivery_number` = ' . $delivery_number . ' AND `id_order` = ' . $id_order);
		if($date)
			return $date[0]['date_add'];
	}

	public function createDelivery($delivery_number,$order,$product_id,$product_attribute_id,$qty,$id_warehouse)
	{
		// First create/update invoice.
		$id_order_invoice = $order->invoice_number; // This should get the id for the default invoice
		if(Configuration::get('PS_ADS_INVOICE_DELIVERED')) {
			$free_shipping = false;
			if($delivery_number != 1)
			{
				$free_shipping = true;
			}
			$id_order_invoice = 0; // Reset it if we should create a new one
		}
		$order_invoice = $this->setInvoice($order,$product_id,$product_attribute_id,$qty,$id_order_invoice,$delivery_number,$free_shipping);

		//Now create delivery
		Db::getInstance()->insert('order_delivery',array(
			'id_order' => (int)$order->id,
			'id_shop' => (int)$order->id_shop,
			'id_order_invoice' => $order_invoice->number,
			'delivery_number' => $delivery_number,
			'date_add' => date("Y-m-d h:i:s"),
		) );
		$delivery_id = Db::getInstance()->Insert_ID();
		$this->createDeliveryDetail($order,$product_id,$product_attribute_id,$qty,$delivery_id,$id_warehouse,$order_invoice);
	}

	public function createDeliveryDetail($order,$product_id,$product_attribute_id,$qty,$delivery_id,$id_warehouse,$order_invoice = false)
	{
		if(!$order_invoice) {
			$id_order_invoice = $this->getInvoiceFromId($delivery_id);
			$order_invoice = new OrderInvoice($id_order_invoice);
		}

		// prepare for order detail
		// create the product
		$product = new Product($product_id, false, $order->id_lang);
		if (!Validate::isLoadedObject($product))
			die(Tools::jsonEncode(array(
				'result' => false,
				'error' => Tools::displayError('The product object cannot be loaded.')
			)));

		if (isset($product_attribute_id) && $product_attribute_id)
		{
			$combination = new Combination($product_attribute_id);
			if (!Validate::isLoadedObject($combination))
				die(Tools::jsonEncode(array(
				'result' => false,
				'error' => Tools::displayError('The combination object cannot be loaded.')
			)));
		}

		// Total method
		$total_method = Cart::BOTH_WITHOUT_SHIPPING;

		// Create new cart
		$cart = new Cart();
		$cart->id_shop_group = $order->id_shop_group;
		$cart->id_shop = $order->id_shop;
		$cart->id_customer = $order->id_customer;
		$cart->id_carrier = $order->id_carrier;
		$cart->id_address_delivery = $order->id_address_delivery;
		$cart->id_address_invoice = $order->id_address_invoice;
		$cart->id_currency = $order->id_currency;
		$cart->id_lang = $order->id_lang;
		$cart->secure_key = $order->secure_key;

		// Save new cart
		$cart->add();

		// Save context (in order to apply cart rule)
		$this->context->cart = $cart;
		$this->context->customer = new Customer($order->id_customer);

		// always add taxes even if there are not displayed to the customer
		$use_taxes = true;

		$initial_product_price_tax_incl = Product::getPriceStatic($product->id, $use_taxes, isset($combination) ? $combination->id : null, 2, null, false, true, 1,
			false, $order->id_customer, $cart->id, $order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)});

		// Add product to cart
		$update_quantity = $cart->updateQty($qty, $product->id, isset($product_attribute_id) ? $product_attribute_id : null,
			isset($combination) ? $combination->id : null, 'up', 0, new Shop($cart->id_shop));

			// Create Order Delivery detail information
			$order_delivery_detail = new OrderDeliveryDetail();
		$order_delivery_detail->createDetailList($order, $cart, $order->getCurrentOrderState(), $cart->getProducts(), (isset($order_invoice) ? $order_invoice->id : 0), $use_taxes,$id_warehouse,$delivery_id);

		Db::getInstance()->update('order_delivery',array('date_add' => date('Y-m-d H:i:s') ), '`delivery_id` = ' . $delivery_id ); // update delivery date when adding product
	}
	
	public function updateDeliveryDetail($order,$product_id,$product_attribute_id,$qty,$delivery_id,$id_warehouse)
	{
		$id_order_invoice = $this->getInvoiceFromId($delivery_id);
		$order_invoice = new OrderInvoice($id_order_invoice);

		// prepare for order detail
		// create the product
		$product = new Product($product_id, false, $order->id_lang);
		if (!Validate::isLoadedObject($product))
			die(Tools::jsonEncode(array(
				'result' => false,
				'error' => Tools::displayError('The product object cannot be loaded.')
			)));

		if (isset($product_attribute_id) && $product_attribute_id)
		{
			$combination = new Combination($product_attribute_id);
			if (!Validate::isLoadedObject($combination))
				die(Tools::jsonEncode(array(
				'result' => false,
				'error' => Tools::displayError('The combination object cannot be loaded.')
			)));
		}

		// always add taxes even if there are not displayed to the customer
		$use_taxes = true;
		
		// Total method
		$total_method = Cart::BOTH_WITHOUT_SHIPPING;

		// Create new cart
		$cart = new Cart();
		$cart->id_shop_group = $order->id_shop_group;
		$cart->id_shop = $order->id_shop;
		$cart->id_customer = $order->id_customer;
		$cart->id_carrier = $order->id_carrier;
		$cart->id_address_delivery = $order->id_address_delivery;
		$cart->id_address_invoice = $order->id_address_invoice;
		$cart->id_currency = $order->id_currency;
		$cart->id_lang = $order->id_lang;
		$cart->secure_key = $order->secure_key;

		// Save new cart
		$cart->add();

		// Save context (in order to apply cart rule)
		$this->context->cart = $cart;
		$this->context->customer = new Customer($order->id_customer);

		// always add taxes even if there are not displayed to the customer
		$use_taxes = true;

		$initial_product_price_tax_incl = Product::getPriceStatic($product->id, $use_taxes, isset($combination) ? $combination->id : null, 2, null, false, true, 1,
			false, $order->id_customer, $cart->id, $order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)});

		// Add product to cart
		$update_quantity = $cart->updateQty($qty, $product->id, isset($product_attribute_id) ? $product_attribute_id : null,
			isset($combination) ? $combination->id : null, 'up', 0, new Shop($cart->id_shop));

		// Create Order Delivery detail information
		$id_order_detail = $this->getOrderDetailId($product_id,$product_attribute_id,$delivery_id,$id_order_invoice,$order->id);
		
		$order_delivery_detail = new OrderDeliveryDetail($id_order_detail);
		
		$order_delivery_detail->updateDetailList($order, $cart, $order->getCurrentOrderState(), $cart->getProducts(), (isset($order_invoice) ? $order_invoice->id : 0), $use_taxes,$id_warehouse,$delivery_id);

		Db::getInstance()->update('order_delivery',array('date_add' => date('Y-m-d H:i:s') ), '`delivery_id` = ' . $delivery_id ); // update delivery date when adding product
	}
	
	function getOrderDetailId($product_id,$product_attribute_id,$delivery_id,$id_order_invoice,$id_order) {
		$qty = Db::getInstance()->executeS('
		SELECT id_order_detail
		FROM `'._DB_PREFIX_.'order_delivery_detail` odyd
		WHERE odyd.`product_id` = ' . $product_id .
		' AND odyd.`product_attribute_id` = ' . $product_attribute_id .
		' AND odyd.`delivery_id` = ' . $delivery_id .
		' AND odyd.`id_order_invoice` = ' . $id_order_invoice .
		' AND odyd.`id_order` = ' . $id_order
		);
		return $qty[0]["id_order_detail"];
	}

}
