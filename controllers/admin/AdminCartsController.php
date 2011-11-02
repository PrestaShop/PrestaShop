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
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminCartsController extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'cart';
		$this->className = 'Cart';
		$this->lang = false;
		$this->requiredDatabase = true;

		$this->addRowAction('view');
		$this->addRowAction('delete');

		$this->_select = 'CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) `customer`, a.id_cart total, ca.name carrier, o.id_order, g.id_guest';
		$this->_join = 'LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer = a.id_customer)
		LEFT JOIN '._DB_PREFIX_.'currency cu ON (cu.id_currency = a.id_currency)
		LEFT JOIN '._DB_PREFIX_.'carrier ca ON (ca.id_carrier = a.id_carrier)
		LEFT JOIN '._DB_PREFIX_.'orders o ON (o.id_cart = a.id_cart)
		LEFT JOIN `'._DB_PREFIX_.'guest` g ON (a.id_guest = g.id_guest AND TIME_TO_SEC(TIMEDIFF(NOW(), c.`date_add`)) < 900)';

 		$this->fieldsDisplay = array(
			'id_cart' => array(
				'title' => $this->l('ID'), 
				'align' => 'center', 
				'width' => 25
			),
			'id_order' => array(
				'title' => $this->l('ID Order'), 
				'align' => 'center', 'width' => 25
			),
			'customer' => array(
				'title' => $this->l('Customer'), 
				'width' => 80, 
				'filter_key' => 'c!lastname'
			),
			'total' => array(
				'title' => $this->l('Total'), 
				'callback' => 'getOrderTotalUsingTaxCalculationMethod', 
				'orderby' => false, 
				'search' => false, 
				'width' => 50, 
				'align' => 'right', 
				'prefix' => '<b>', 
				'suffix' => '</b>', 
				'currency' => true
			),
			'carrier' => array(
				'title' => $this->l('Carrier'), 
				'width' => 25, 
				'align' => 'center', 
				'callback' => 'replaceZeroByShopName', 
				'filter_key' => 'ca!name'
			),
			'date_add' => array(
				'title' => $this->l('Date'), 
				'width' => 90, 
				'align' => 'right', 
				'type' => 'datetime', 
				'filter_key' => 'a!date_add'
			),
			'id_guest' => array(
				'title' => $this->l('Online'), 
				'width' => 25, 
				'align' => 'center', 
				'type' => 'bool', 
				'filter_key' => 'guest', 
				'tmpTableFilter' => true, 
				'icon' => array(0 => 'blank.gif', 1 => 'tab-customers.gif')
			)
		);
 		$this->shopLinkType = 'shop';
 		
		parent::__construct();
	}
	
	public function viewCart()
	{
		if (!($cart = $this->loadObject(true)))
			return;
		$customer = new Customer($cart->id_customer);
		$products = $cart->getProducts();
		$customized_datas = Product::getAllCustomizedDatas((int)($cart->id));
		Product::addCustomizationPrice($products, $customized_datas);
		$summary = $cart->getSummaryDetails();
		$currency = new Currency($cart->id_currency);

		/* Display order information */
		$id_order = (int)(Order::getOrderByCartId($cart->id));
		$order = new Order($id_order);

		if ($order->getTaxCalculationMethod() == PS_TAX_EXC)
		{
			$total_products = $summary['total_products'];
			$total_discounts = $summary['total_discounts_tax_exc'];
			$total_wrapping = $summary['total_wrapping_tax_exc'];
			$total_price = $summary['total_price_without_tax'];$total_shipping = $summary['total_shipping_tax_exc'];
		} 
		else
		{
			$total_products = $summary['total_products_wt'];
			$total_discounts = $summary['total_discounts'];
			$total_wrapping = $summary['total_wrapping'];
			$total_price = $summary['total_price'];
			$total_shipping = $summary['total_shipping'];
		}
		foreach ($products as $k => &$product)
		{
			if ($order->getTaxCalculationMethod() == PS_TAX_EXC)
			{
				$product['product_price'] = $product['price'];
				$product['product_total'] = $product['total'];
			}
			else
			{
				$product['product_price'] = $product['price_wt'];
				$product['product_total'] = $product['total_wt'];
			}
			$image = array();
			if (isset($product['id_product_attribute']) AND (int)($product['id_product_attribute']))
				$image = Db::getInstance()->getRow('SELECT id_image
																FROM '._DB_PREFIX_.'product_attribute_image
																WHERE id_product_attribute = '.(int)($product['id_product_attribute']));
		 	if (!isset($image['id_image']))
				$image = Db::getInstance()->getRow('SELECT id_image
																FROM '._DB_PREFIX_.'image
																WHERE id_product = '.(int)($product['id_product']).' AND cover = 1');

			$productObj = new Product($product['id_product']);
			$product['qty_in_stock'] = StockAvailable::getStockAvailableForProduct($product['id_product'], isset($product['id_product_attribute']) ? $product['id_product_attribute'] : null, (int)$order->id_shop);
			
			$imageProduct = new Image($image['id_image']);
			$product['image'] = (isset($image['id_image']) ? cacheImage(_PS_IMG_DIR_.'p/'.$imageProduct->getExistingImgPath().'.jpg', 'product_mini_'.(int)($product['id_product']).(isset($product['id_product_attribute']) ? '_'.(int)($product['id_product_attribute']) : '').'.jpg', 45, 'jpg') : '--');
		}
		$this->context->smarty->assign(array('products' => $products,
												'discounts' => $cart->getDiscounts(),
												'order' => $order,
												'cart' => $cart,
												'currency' => $currency,
												'customer' => $customer,
												'customer_stats' => $customer->getStats(),
												'total_products' => $total_products,
												'total_discounts' => $total_discounts,
												'total_wrapping' => $total_wrapping,
												'total_price' => $total_price,
												'total_shipping' => $total_shipping,
												'customized_datas' => $customized_datas
												));
	}
	
	public function ajaxPreProcess()
	{
		$id_customer = (int)Tools::getValue('id_customer');
		$customer = new Customer((int)$id_customer);
		$this->context->customer = $customer;
		$id_cart = (int)Tools::getValue('id_cart');
		if (!$id_cart)
			$id_cart = $customer->getLastCart();
		$this->context->cart = new Cart((int)$id_cart);
		if (!$this->context->cart->id_customer)
			$this->context->cart->id_customer = $id_customer;
		if ($this->context->cart->OrderExists())
			return;
		if (!$this->context->cart->id_shop)
			$this->context->cart->id_shop = (int)$this->context->shop->id;
		if (!$this->context->cart->id_lang)
			$this->context->cart->id_lang = (($id_lang = (int)Tools::getValue('id_lang')) ? $id_lang : Configuration::get('PS_LANG_DEFAULT'));
		if (!$this->context->cart->id_currency)
			$this->context->cart->id_currency = (($id_currency = (int)Tools::getValue('id_currency')) ? $id_currency : Configuration::get('PS_CURRENCY_DEFAULT'));
		
		$addresses = $customer->getAddresses((int)$this->context->cart->id_lang);
		$id_address_delivery = (int)Tools::getValue('id_address_delivery');
		$id_address_invoice = (int)Tools::getValue('id_address_delivery');

		if (!$this->context->cart->id_address_invoice && isset($addresses[0]))
			$this->context->cart->id_address_invoice = (int)$addresses[0]['id_address'];
		elseif ($id_address_invoice)
			$this->context->cart->id_address_invoice = (int)$id_address_invoice;
		if (!$this->context->cart->id_address_delivery && isset($addresses[0]))
			$this->context->cart->id_address_delivery = $addresses[0]['id_address'];
		elseif ($id_address_delivery)
			$this->context->cart->id_address_delivery = (int)$id_address_delivery;
		$this->context->cart->save();
		$currency = new Currency((int)$this->context->cart->id_currency);
		$this->context->currency = $currency;

	}

	public function ajaxProcessDeleteProduct()
	{
		$errors = array();
		if (!$id_product = (int)Tools::getValue('id_product') OR !$id_product_attribute = (int)Tools::getValue('id_product_attribute'))
			$errors[] = Tools::displayError('Invalid product');
		if (sizeof($errors))
			die(Tools::jsonEncode($errors));
		
		if ($this->context->cart->deleteProduct($id_product, $id_product_attribute))
			echo Tools::jsonEncode($this->ajaxReturnVars());
	}

	public function ajaxProcessUpdateQty()
	{
		$errors = array();
		if (!$this->context->cart->id)
			return;
		if ($this->context->cart->OrderExists())
			$errors[] = Tools::displayErrors('An order already placed with this cart');
		elseif (!$id_product = (int)Tools::getValue('id_product') OR (!$product = new Product((int)$id_product, true, $this->context->language->id)))
			$errors[] = Tools::displayError('Invalid product');
		elseif (!$qty = Tools::getValue('qty') OR $qty == 0)
			$errors[] = Tools::displayError('Invalid quantity');
		if (($id_product_attribute = Tools::getValue('id_product_attribute')) != 0)
		{ 
			if(!Product::isAvailableWhenOutOfStock($product->out_of_stock) && !Attribute::checkAttributeQty((int)$id_product_attribute, (int)$qty))
				$errors[] = Tools::displayError('There is not enough product in stock');
		}
		else 
			if(!$product->checkQty((int)$qty))
				$errors[] = Tools::displayError('There is not enough product in stock');
		if (!$id_customization = (int)Tools::getValue('id_customization', 0) AND !$product->hasAllRequiredCustomizableFields())
			$errors[] = Tools::displayError('Please fill in all required fields');
		$this->context->cart->save();
		if (!count($errors))
		{
			if (!($qty_upd = $this->context->cart->updateQty($qty, $id_product, (int)$id_product_attribute, (int)$id_customization, 'up')))
				$errors[] = Tools::displayError('You already have the maximum quantity available for this product.');			
		}

		echo Tools::jsonEncode(array_merge($this->ajaxReturnVars(), array('errors' => $errors)));
			
	}
	
	public function ajaxProcessUpdateCarrier()
	{
		if(Validate::isBool(($recyclable = (int)Tools::getValue('recyclable'))))
			$this->context->cart->recyclable = $recyclable;
		if(Validate::isBool(($gift = (int)Tools::getValue('gift'))))
			$this->context->cart->gift = $gift;
		if(Validate::isMessage(($gift_message = pSQL(Tools::getValue('gift_message')))))
			$this->context->cart->gift_message = $gift_message;
		$this->context->cart->save();
		echo Tools::jsonEncode($this->ajaxReturnVars());
	}
	
	public function ajaxProcessUpdateCurrency()
	{
		$currency = new Currency((int)Tools::getValue('id_currency'));
		if (Validate::isLoadedObject($currency) && !$currency->deleted && $currency->active)
		{
			$this->context->cart->id_currency = (int)$currency->id;
			$this->context->cart->save();
		}
		echo Tools::jsonEncode($this->ajaxReturnVars());
	}
	public function ajaxProcessUpdateLang()
	{
		$lang = new Language((int)Tools::getValue('id_lang'));
		if (Validate::isLoadedObject($lang) && $lang->active)
		{
			$this->context->cart->id_lang = (int)$lang->id;
			$this->context->cart->save();
		}
		echo Tools::jsonEncode($this->ajaxReturnVars());
	}
	public function ajaxProcessDuplicateOrder()
	{
		$errors = array();
		if (!$id_order = Tools::getValue('id_order'))
			$errors[] = Tools::displayErrors('Invalid order');
		$cart = Cart::getCartByOrderId($id_order);
		$new_cart = $cart->duplicate();
		if (!$new_cart OR !Validate::isLoadedObject($new_cart['cart']))
			$errors[] = Tools::displayError('The order cannot be renewed');
		elseif (!$new_cart['success'])
			$errors[] = Tools::displayError('The order cannot be renewed');
		else
		{
			$this->context->cart = $new_cart['cart'];
			echo Tools::jsonEncode($this->ajaxReturnVars());
		}
		
	}
	
	public function ajaxProcessDeleteDiscount()
	{
		if ($this->context->cart->deleteDiscount((int)Tools::getValue('id_voucher')))
			echo Tools::jsonEncode($this->ajaxReturnVars());
	}
	
	public function ajaxProcessAddVoucher()
	{
		$errors = array();
		$customer = new Customer((int)$this->context->cart->id_customer);
		
		if (!$id_discount = Tools::getValue('id_voucher') OR !$discount = new Discount((int)$id_discount))
			$errors[] = Tools::displayError('Invalid voucher');
		elseif ($err = $this->context->cart->checkDiscountValidity($discount, $this->context->cart->getDiscounts(), $this->context->cart->getOrderTotal(), $this->context->cart->getProducts(), true, $customer))
			$errors[] = $err;
		if (!sizeof($errors))
			if (!$this->context->cart->addDiscount((int)$discount->id))
				$errors[] = Tools::displayError('Can\'t add the voucher');
		echo Tools::jsonEncode(array_merge($this->ajaxReturnVars(), array('errors' => $errors)));
	}
	
	public function ajaxProcessUpdateAddresses()
	{
		if (($id_address_delivery = (int)Tools::getValue('id_address_delivery')) && $address_delivery = new Address((int)$id_address_delivery) && $address_delivery->id_customer = $this->context->cart->id_customer)
			$this->context->cart->id_address_delivery =  (int)$address_delivery->id;
		
		if (($id_address_invoice = (int)Tools::getValue('id_address_invoice')) && $address_invoice = new Address((int)$id_address_invoice) && $address_invoice->id_customer = $this->context->cart->id_customer)
			$this->context->cart->id_address_invoice =  (int)$address_invoice->id;
		$this->context->cart->save();
		
		echo Tools::jsonEncode($this->ajaxReturnVars());
	}

	protected function getCartSummary()
	{
		$summary = $this->context->cart->getSummaryDetails();
		$currency = new Currency((int)$this->context->cart->id_currency);
		if (count($summary['products']))
			foreach ($summary['products'] AS &$product)
			{
				$product['price'] = str_replace($currency->sign, '', Tools::displayPrice(Tools::convertPrice($product['price'], $currency), $currency));
				$product['total'] = str_replace($currency->sign, '', Tools::displayPrice(Tools::convertPrice($product['total'], $currency), $currency));
				$product['image_link'] = $this->context->link->getImageLink($product['link_rewrite'], $product['id_image'], 'small');
				if(!isset($product['attributes_small']))
					$product['attributes_small'] = '';
			}
		if (count($summary['discounts']))
			foreach ($summary['discounts'] AS &$voucher)
				$voucher['value_real'] = Tools::displayPrice($voucher['value_real'], $currency);
		$summary['total_products'] = str_replace($currency->sign, '', Tools::displayPrice(Tools::convertPrice($summary['total_products'], $currency), $currency));
		$summary['total_discounts_tax_exc'] = str_replace($currency->sign, '', Tools::displayPrice(Tools::convertPrice($summary['total_discounts_tax_exc'], $currency), $currency));
		$summary['total_shipping_tax_exc'] = str_replace($currency->sign, '', Tools::displayPrice(Tools::convertPrice($summary['total_shipping_tax_exc'], $currency), $currency));
		$summary['total_tax'] = str_replace($currency->sign, '', Tools::displayPrice(Tools::convertPrice($summary['total_tax'], $currency), $currency));
		$summary['total_price_without_tax'] = str_replace($currency->sign, '', Tools::displayPrice(Tools::convertPrice($summary['total_price_without_tax'], $currency), $currency));
		$summary['total_price'] = str_replace($currency->sign, '', Tools::displayPrice(Tools::convertPrice($summary['total_price'], $currency), $currency));
				
		return $summary;
	}
	
	protected function getAvailableCarriers()
	{
		$customer = new Customer((int)$this->context->cart->id_customer);
		return Carrier::getCarriersForOrder(Address::getZoneById($this->context->cart->id_address_delivery), $customer->getGroups());
	}
		
	public function displayAjaxSearchCarts()
	{
		$id_customer = (int)Tools::getValue('id_customer');
		$carts = Cart::getCustomerCarts((int)$id_customer);
		$orders = Order::getCustomerOrders((int)$id_customer);
		$customer = new Customer((int)$id_customer);
		
		if (count($carts))
			foreach ($carts AS $key => &$cart)
			{
				$cartObj = new Cart((int)$cart['id_cart']);
				if (!Validate::isLoadedObject($cartObj) || $cartObj->OrderExists())
					unset($carts[$key]);
				$currency = new Currency((int)($cart['id_currency']));
				$cart['total_price'] = Tools::displayPrice(Tools::convertPrice($cartObj->getOrderTotal(), $currency), $currency);
			}
		if (count($orders))
			foreach ($orders AS &$order)
				$order['total_paid_real'] = Tools::displayPrice(Tools::convertPrice($order['total_paid_real'], $currency), $currency);
		if ($orders || $carts)
			$to_return = array_merge($this->ajaxReturnVars(), 
											array('carts' => $carts,
													 'orders' => $orders,
													 'found' => true));
		else
			$to_return = array_merge($this->ajaxReturnVars(), array('found' => false));

		echo Tools::jsonEncode($to_return);
	}
	
	public function ajaxReturnVars()
	{

		$id_cart = (int)$this->context->cart->id;
		return array('summary' => $this->getCartSummary(),
						'carriers' => $this->getAvailableCarriers(),
						'cart' => $this->context->cart,
						'carriers' => $this->getAvailableCarriers(),
						'addresses' => $this->context->customer->getAddresses((int)$this->context->cart->id_lang),
						'id_cart' => $id_cart,
						'link_order' => $this->context->link->getPageLink('order', false, (int)$this->context->cart->id_lang, 'step=3&recover_cart='.$id_cart.'&token_cart='.md5(_COOKIE_KEY_.'recover_cart_'.$id_cart)));
	}
	
	public function displayAjaxGetSummary()
	{
		echo Tools::jsonEncode($this->ajaxReturnVars());
	}
	
	public static function getOrderTotalUsingTaxCalculationMethod($id_cart)
	{
		return Cart::getTotalCart($id_cart, true);
	}
	
	public static function replaceZeroByShopName($echo, $tr)
	{
		return ($echo == '0' ? Configuration::get('PS_SHOP_NAME') : $echo);
	}
}
