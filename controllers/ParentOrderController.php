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

/* Class FreeOrder to use PaymentModule (abstract class, cannot be instancied) */
class FreeOrder extends PaymentModule {}

class ParentOrderControllerCore extends FrontController
{
	public $nbProducts;
	
	public function __construct()
	{
		$this->ssl = true;
		parent::__construct();
		
		/* Disable some cache related bugs on the cart/order */
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	}
	
	public function init()
	{
		parent::init();
		$this->nbProducts = self::$cart->nbProducts();
	}
	
	public function preProcess()
	{
		global $isVirtualCart;
		
		parent::preProcess();
		
		// Redirect to the good order process
		if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 0 AND strpos($_SERVER['PHP_SELF'], 'order.php') === false)
			Tools::redirect('order.php');
		if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 1 AND strpos($_SERVER['PHP_SELF'], 'order-opc.php') === false)
		{
			if (isset($_GET['step']) AND $_GET['step'] == 3)
				Tools::redirect('order-opc.php?isPaymentStep=true');
			Tools::redirect('order-opc.php');
		}
		
		if (Configuration::get('PS_CATALOG_MODE'))
			$this->errors[] = Tools::displayError('This store has not accepted your new order.');
		
		if (Tools::isSubmit('submitReorder') AND $id_order = (int)Tools::getValue('id_order'))
		{
			$oldCart = new Cart(Order::getCartIdStatic((int)$id_order, (int)self::$cookie->id_customer));
			$duplication = $oldCart->duplicate();
			if (!$duplication OR !Validate::isLoadedObject($duplication['cart']))
				$this->errors[] = Tools::displayError('Sorry, we cannot renew your order.');
			elseif (!$duplication['success'])
				$this->errors[] = Tools::displayError('Missing items - we are unable renew your order');
			else
			{
				self::$cookie->id_cart = $duplication['cart']->id;
				self::$cookie->write();
				if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 1)
					Tools::redirect('order-opc.php');
				Tools::redirect('order.php');
			}
		}
		
		if ($this->nbProducts)
		{
			if (Tools::isSubmit('submitAddDiscount') AND Tools::getValue('discount_name'))
			{
				$discountName = Tools::getValue('discount_name');
				if (!Validate::isDiscountName($discountName))
					$this->errors[] = Tools::displayError('Voucher name invalid.');
				else
				{
					$discount = new Discount((int)(Discount::getIdByName($discountName)));
					if (Validate::isLoadedObject($discount))
					{
						if ($tmpError = self::$cart->checkDiscountValidity($discount, self::$cart->getDiscounts(), self::$cart->getOrderTotal(), self::$cart->getProducts(), true))
							$this->errors[] = $tmpError;
					}
					else
						$this->errors[] = Tools::displayError('Voucher name invalid.');
					if (!sizeof($this->errors))
					{
						self::$cart->addDiscount((int)($discount->id));
						Tools::redirect('order-opc.php');
					}
				}
				self::$smarty->assign(array(
					'errors' => $this->errors,
					'discount_name' => Tools::safeOutput($discountName)
				));
			}
			elseif (isset($_GET['deleteDiscount']) AND Validate::isUnsignedId($_GET['deleteDiscount']))
			{
				self::$cart->deleteDiscount((int)($_GET['deleteDiscount']));
				Tools::redirect('order-opc.php');
			}
			
			/* Is there only virtual product in cart */
			if ($isVirtualCart = self::$cart->isVirtualCart())
				$this->_setNoCarrier();
		}
		
		self::$smarty->assign('back', Tools::safeOutput(Tools::getValue('back')));
	}
	
	public function setMedia()
	{
		parent::setMedia();
		
		// Adding CSS style sheet
		Tools::addCSS(_THEME_CSS_DIR_.'addresses.css');
		Tools::addCSS(_PS_CSS_DIR_.'jquery.fancybox-1.3.4.css', 'screen');

		// Adding JS files
		Tools::addJS(_THEME_JS_DIR_.'tools.js');
		if ((Configuration::get('PS_ORDER_PROCESS_TYPE') == 0 AND Tools::getValue('step') == 1) OR Configuration::get('PS_ORDER_PROCESS_TYPE') == 1)
			Tools::addJS(_THEME_JS_DIR_.'order-address.js');
		Tools::addJS(_PS_JS_DIR_.'jquery/jquery.fancybox-1.3.4.js');
		if ((int)(Configuration::get('PS_BLOCK_CART_AJAX')) OR Configuration::get('PS_ORDER_PROCESS_TYPE') == 1)
		{
			Tools::addJS(_THEME_JS_DIR_.'cart-summary.js');
			Tools::addJS(_PS_JS_DIR_.'jquery/jquery-typewatch.pack.js');
		}
		
	}
	
	/**
	 * @return boolean
	 */
	protected function _checkFreeOrder()
	{
		if (self::$cart->getOrderTotal() <= 0)
		{
			$order = new FreeOrder();
			$order->free_order_class = true;
			$order->validateOrder((int)(self::$cart->id), _PS_OS_PAYMENT_, 0, Tools::displayError('Free order', false));
			return (int)Order::getOrderByCartId((int)self::$cart->id);
		}
		return false;
	}
	
	protected function _updateMessage($messageContent)
	{
		if ($messageContent)
		{
			if (!Validate::isMessage($messageContent))
    			$this->errors[] = Tools::displayError('Invalid message');
    		elseif ($oldMessage = Message::getMessageByCartId((int)(self::$cart->id)))
    		{
    			$message = new Message((int)($oldMessage['id_message']));
    			$message->message = htmlentities($messageContent, ENT_COMPAT, 'UTF-8');
    			$message->update();
    		}
    		else
    		{
    			$message = new Message();
    			$message->message = htmlentities($messageContent, ENT_COMPAT, 'UTF-8');
    			$message->id_cart = (int)(self::$cart->id);
    			$message->id_customer = (int)(self::$cart->id_customer);
    			$message->add();
    		}
    	}
    	else
    	{
    		if ($oldMessage = Message::getMessageByCartId((int)(self::$cart->id)))
    		{
    			$message = new Message((int)($oldMessage['id_message']));
    			$message->delete();
    		}
    	}
		return true;
	}
	
	protected function _processCarrier()
	{
		self::$cart->recyclable = (int)(Tools::getValue('recyclable'));
		self::$cart->gift = (int)(Tools::getValue('gift'));
		if ((int)(Tools::getValue('gift')))
		{
			if (!Validate::isMessage($_POST['gift_message']))
				$this->errors[] = Tools::displayError('Invalid gift message');
			else
				self::$cart->gift_message = strip_tags($_POST['gift_message']);
		}
		
		if (isset(self::$cookie->id_customer) AND self::$cookie->id_customer)
		{
			$address = new Address((int)(self::$cart->id_address_delivery));
			if (!($id_zone = Address::getZoneById($address->id)))
				$this->errors[] = Tools::displayError('No zone match with your address');
		}
		else
			$id_zone = Country::getIdZone((int)Configuration::get('PS_COUNTRY_DEFAULT'));
			
		if (Validate::isInt(Tools::getValue('id_carrier')) AND sizeof(Carrier::checkCarrierZone((int)(Tools::getValue('id_carrier')), (int)($id_zone))))
			self::$cart->id_carrier = (int)(Tools::getValue('id_carrier'));
		elseif (!self::$cart->isVirtualCart() AND (int)(Tools::getValue('id_carrier')) != 0)
			$this->errors[] = Tools::displayError('Invalid carrier or no carrier selected');
		
		Module::hookExec('processCarrier', array('cart' => self::$cart));
		
		return self::$cart->update();
	}
	
	protected function _assignSummaryInformations()
	{
		global $currency;
		
		if (file_exists(_PS_SHIP_IMG_DIR_.(int)(self::$cart->id_carrier).'.jpg'))
			self::$smarty->assign('carrierPicture', 1);
		$summary = self::$cart->getSummaryDetails();
		$customizedDatas = Product::getAllCustomizedDatas((int)(self::$cart->id));
		Product::addCustomizationPrice($summary['products'], $customizedDatas);

		if ($free_ship = Tools::convertPrice((float)(Configuration::get('PS_SHIPPING_FREE_PRICE')), new Currency((int)(self::$cart->id_currency))))
		{
			$discounts = self::$cart->getDiscounts();
			$total_free_ship =  $free_ship - ($summary['total_products_wt'] + $summary['total_discounts']);
			foreach ($discounts as $discount)
				if ($discount['id_discount_type'] == 3)
				{
					$total_free_ship = 0;
					break;
				}
			self::$smarty->assign('free_ship', $total_free_ship);
		}
		// for compatibility with 1.2 themes
		foreach($summary['products'] AS $key => $product)
			$summary['products'][$key]['quantity'] = $product['cart_quantity'];
		self::$smarty->assign($summary);
		self::$smarty->assign(array(
			'token_cart' => Tools::getToken(false),
			'isVirtualCart' => self::$cart->isVirtualCart(),
			'productNumber' => self::$cart->nbProducts(),
			'voucherAllowed' => Configuration::get('PS_VOUCHERS'),
			'shippingCost' => self::$cart->getOrderTotal(true, Cart::ONLY_SHIPPING),
			'shippingCostTaxExc' => self::$cart->getOrderTotal(false, Cart::ONLY_SHIPPING),
			'customizedDatas' => $customizedDatas,
			'CUSTOMIZE_FILE' => _CUSTOMIZE_FILE_,
			'CUSTOMIZE_TEXTFIELD' => _CUSTOMIZE_TEXTFIELD_,
			'lastProductAdded' => self::$cart->getLastProduct(),
			'displayVouchers' => Discount::getVouchersToCartDisplay((int)(self::$cookie->id_lang), (isset(self::$cookie->id_customer) ? (int)(self::$cookie->id_customer) : 0)),
			'currencySign' => $currency->sign,
			'currencyRate' => $currency->conversion_rate,
			'currencyFormat' => $currency->format,
			'currencyBlank' => $currency->blank));
		self::$smarty->assign(array(
			'HOOK_SHOPPING_CART' => Module::hookExec('shoppingCart', $summary),
			'HOOK_SHOPPING_CART_EXTRA' => Module::hookExec('shoppingCartExtra', $summary)
		));
	}
	
	protected function _assignAddress()
	{
		if (!Customer::getAddressesTotalById((int)(self::$cookie->id_customer)))
			Tools::redirect('address.php?back=order.php?step=1');
		$customer = new Customer((int)(self::$cookie->id_customer));
		if (Validate::isLoadedObject($customer))
		{
			/* Getting customer addresses */
			$customerAddresses = $customer->getAddresses((int)(self::$cookie->id_lang));
			self::$smarty->assign('addresses', $customerAddresses);

			/* Setting default addresses for cart */
			if ((!isset(self::$cart->id_address_delivery) OR empty(self::$cart->id_address_delivery)) AND sizeof($customerAddresses))
			{
				self::$cart->id_address_delivery = (int)($customerAddresses[0]['id_address']);
				$update = 1;
			}
			if ((!isset(self::$cart->id_address_invoice) OR empty(self::$cart->id_address_invoice)) AND sizeof($customerAddresses))
			{
				self::$cart->id_address_invoice = (int)($customerAddresses[0]['id_address']);
				$update = 1;
			}
			/* Update cart addresses only if needed */
			if (isset($update) AND $update)
				self::$cart->update();

			/* If delivery address is valid in cart, assign it to Smarty */
			if (isset(self::$cart->id_address_delivery))
			{
				$deliveryAddress = new Address((int)(self::$cart->id_address_delivery));
				if (Validate::isLoadedObject($deliveryAddress) AND ($deliveryAddress->id_customer == $customer->id))
					self::$smarty->assign('delivery', $deliveryAddress);
			}

			/* If invoice address is valid in cart, assign it to Smarty */
			if (isset(self::$cart->id_address_invoice))
			{
				$invoiceAddress = new Address((int)(self::$cart->id_address_invoice));
				if (Validate::isLoadedObject($invoiceAddress) AND ($invoiceAddress->id_customer == $customer->id))
					self::$smarty->assign('invoice', $invoiceAddress);
			}
		}
		if ($oldMessage = Message::getMessageByCartId((int)(self::$cart->id)))
			self::$smarty->assign('oldMessage', $oldMessage['message']);
	}
	
	protected function _assignCarrier()
	{
		$customer = new Customer((int)(self::$cookie->id_customer));
		$address = new Address((int)(self::$cart->id_address_delivery));
		$id_zone = Address::getZoneById((int)($address->id));
		$carriers = Carrier::getCarriersForOrder($id_zone, $customer->getGroups());
		
		$checked = 0;
		if (Validate::isUnsignedInt(self::$cart->id_carrier) AND self::$cart->id_carrier)
		{
			$carrier = new Carrier((int)(self::$cart->id_carrier));
			if ($carrier->active AND !$carrier->deleted)
				$checked = (int)(self::$cart->id_carrier);
		}
		self::$smarty->assign(array(
			'checked' => (int)($checked),
			'carriers' => $carriers,
			'default_carrier' => (int)(Configuration::get('PS_CARRIER_DEFAULT')),
			'HOOK_EXTRACARRIER' => Module::hookExec('extraCarrier', array('address' => $address)),
			'HOOK_BEFORECARRIER' => Module::hookExec('beforeCarrier', array('carriers' => $carriers))
		));
	}
	
	protected function _assignWrappingAndTOS()
	{
		// Wrapping fees
		$wrapping_fees = (float)(Configuration::get('PS_GIFT_WRAPPING_PRICE'));
		$wrapping_fees_tax = new Tax((int)(Configuration::get('PS_GIFT_WRAPPING_TAX')));
		$wrapping_fees_tax_inc = $wrapping_fees * (1 + (((float)($wrapping_fees_tax->rate) / 100)));
		
		// TOS
		$cms = new CMS((int)(Configuration::get('PS_CONDITIONS_CMS_ID')), (int)(self::$cookie->id_lang));
		$this->link_conditions = self::$link->getCMSLink($cms, $cms->link_rewrite, true);
		if (!strpos($this->link_conditions, '?'))
			$this->link_conditions .= '?content_only=1';
		else
			$this->link_conditions .= '&content_only=1';
		
		self::$smarty->assign(array(
			'checkedTOS' => (int)(self::$cookie->checkedTOS),
			'recyclablePackAllowed' => (int)(Configuration::get('PS_RECYCLABLE_PACK')),
			'giftAllowed' => (int)(Configuration::get('PS_GIFT_WRAPPING')),
			'cms_id' => (int)(Configuration::get('PS_CONDITIONS_CMS_ID')),
			'conditions' => (int)(Configuration::get('PS_CONDITIONS')),
			'link_conditions' => $this->link_conditions,
			'recyclable' => (int)(self::$cart->recyclable),
			'gift_wrapping_price' => (float)(Configuration::get('PS_GIFT_WRAPPING_PRICE')),
			'total_wrapping_cost' => Tools::convertPrice($wrapping_fees_tax_inc, new Currency((int)(self::$cookie->id_currency))),
			'total_wrapping_tax_exc_cost' => Tools::convertPrice($wrapping_fees, new Currency((int)(self::$cookie->id_currency)))));
	}
	
	protected function _assignPayment()
	{
		self::$smarty->assign(array(
		    'HOOK_TOP_PAYMENT' => Module::hookExec('paymentTop'),
			'HOOK_PAYMENT' => Module::hookExecPayment()
		));
	}
	
	/**
	 * Set id_carrier to 0 (no shipping price)
	 *
	 */
	protected function _setNoCarrier()
	{
		self::$cart->id_carrier = 0;
		self::$cart->update();
	}
}
