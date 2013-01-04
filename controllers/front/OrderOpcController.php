<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class OrderOpcControllerCore extends ParentOrderController
{
	public $php_self = 'order-opc';
	public $isLogged;

	/**
	 * Initialize order opc controller
	 * @see FrontController::init()
	 */
	public function init()
	{
		parent::init();

		if ($this->nbProducts)
			$this->context->smarty->assign('virtual_cart', false);
		
		$this->context->smarty->assign('is_multi_address_delivery', $this->context->cart->isMultiAddressDelivery() || ((int)Tools::getValue('multi-shipping') == 1));
		$this->context->smarty->assign('open_multishipping_fancybox', (int)Tools::getValue('multi-shipping') == 1);
		
		if ($this->context->cart->nbProducts())
		{
			if (Tools::isSubmit('ajax'))
			{
				if (Tools::isSubmit('method'))
				{
					switch (Tools::getValue('method'))
					{
						case 'updateMessage':
							if (Tools::isSubmit('message'))
							{
								$txtMessage = urldecode(Tools::getValue('message'));
								$this->_updateMessage($txtMessage);
								if (count($this->errors))
									die('{"hasError" : true, "errors" : ["'.implode('\',\'', $this->errors).'"]}');
								die(true);
							}
							break;

						case 'updateCarrierAndGetPayments':
							if ((Tools::isSubmit('delivery_option') || Tools::isSubmit('id_carrier')) && Tools::isSubmit('recyclable') && Tools::isSubmit('gift') && Tools::isSubmit('gift_message'))
							{
								$this->_assignWrappingAndTOS();
								if ($this->_processCarrier())
								{
									$carriers = $this->context->cart->simulateCarriersOutput();
									$return = array_merge(array(
										'HOOK_TOP_PAYMENT' => Hook::exec('displayPaymentTop'),
										'HOOK_PAYMENT' => $this->_getPaymentMethods(),
										'carrier_data' => $this->_getCarrierList(),
										'HOOK_BEFORECARRIER' => Hook::exec('displayBeforeCarrier', array('carriers' => $carriers))
										),
										$this->getFormatedSummaryDetail()
									);
									Cart::addExtraCarriers($return);
									die(Tools::jsonEncode($return));
								}
								else
									$this->errors[] = Tools::displayError('Error occurred while updating cart.');
								if (count($this->errors))
									die('{"hasError" : true, "errors" : ["'.implode('\',\'', $this->errors).'"]}');
								exit;
							}
							break;

						case 'updateTOSStatusAndGetPayments':
							if (Tools::isSubmit('checked'))
							{
								$this->context->cookie->checkedTOS = (int)(Tools::getValue('checked'));
								die(Tools::jsonEncode(array(
									'HOOK_TOP_PAYMENT' => Hook::exec('displayPaymentTop'),
									'HOOK_PAYMENT' => $this->_getPaymentMethods()
								)));
							}
							break;

						case 'getCarrierList':
							die(Tools::jsonEncode($this->_getCarrierList()));
							break;

						case 'editCustomer':
							if (!$this->isLogged)
								exit;
							if (Tools::getValue('years'))
								$this->context->customer->birthday = (int)Tools::getValue('years').'-'.(int)Tools::getValue('months').'-'.(int)Tools::getValue('days');
							$_POST['lastname'] = $_POST['customer_lastname'];
							$_POST['firstname'] = $_POST['customer_firstname'];
							$this->errors = $this->context->customer->validateController();
							$this->context->customer->newsletter = (int)Tools::isSubmit('newsletter');
							$this->context->customer->optin = (int)Tools::isSubmit('optin');
							$return = array(
								'hasError' => !empty($this->errors),
								'errors' => $this->errors,
								'id_customer' => (int)$this->context->customer->id,
								'token' => Tools::getToken(false)
							);
							if (!count($this->errors))
								$return['isSaved'] = (bool)$this->context->customer->update();
							else
								$return['isSaved'] = false;
							die(Tools::jsonEncode($return));
							break;

						case 'getAddressBlockAndCarriersAndPayments':
							if ($this->context->customer->isLogged())
							{
								// check if customer have addresses
								if (!Customer::getAddressesTotalById($this->context->customer->id))
									die(Tools::jsonEncode(array('no_address' => 1)));
								if (file_exists(_PS_MODULE_DIR_.'blockuserinfo/blockuserinfo.php'))
								{
									include_once(_PS_MODULE_DIR_.'blockuserinfo/blockuserinfo.php');
									$blockUserInfo = new BlockUserInfo();
								}
								$this->context->smarty->assign('isVirtualCart', $this->context->cart->isVirtualCart());
								$this->_processAddressFormat();
								$this->_assignAddress();
								// Wrapping fees
								$wrapping_fees = $this->context->cart->getGiftWrappingPrice(false);
								$wrapping_fees_tax_inc = $wrapping_fees = $this->context->cart->getGiftWrappingPrice();
								$return = array_merge(array(
									'order_opc_adress' => $this->context->smarty->fetch(_PS_THEME_DIR_.'order-address.tpl'),
									'block_user_info' => (isset($blockUserInfo) ? $blockUserInfo->hookTop(array()) : ''),
									'carrier_data' => $this->_getCarrierList(),
									'HOOK_TOP_PAYMENT' => Hook::exec('displayPaymentTop'),
									'HOOK_PAYMENT' => $this->_getPaymentMethods(),
									'no_address' => 0,
									'gift_price' => Tools::displayPrice(Tools::convertPrice(Product::getTaxCalculationMethod() == 1 ? $wrapping_fees : $wrapping_fees_tax_inc, new Currency((int)($this->context->cookie->id_currency))))
									),
									$this->getFormatedSummaryDetail()
								);
								die(Tools::jsonEncode($return));
							}
							die(Tools::displayError());
							break;

						case 'makeFreeOrder':
							/* Bypass payment step if total is 0 */
							if (($id_order = $this->_checkFreeOrder()) && $id_order)
							{
								$order = new Order((int)$id_order);
								$email = $this->context->customer->email;
								if ($this->context->customer->is_guest)
									$this->context->customer->logout(); // If guest we clear the cookie for security reason
								die('freeorder:'.$order->reference.':'.$email);
							}
							exit;
							break;

						case 'updateAddressesSelected':
							if ($this->context->customer->isLogged(true))
							{
								$address_delivery = new Address((int)(Tools::getValue('id_address_delivery')));
								$this->context->smarty->assign('isVirtualCart', $this->context->cart->isVirtualCart());
								$address_invoice = ((int)(Tools::getValue('id_address_delivery')) == (int)(Tools::getValue('id_address_invoice')) ? $address_delivery : new Address((int)(Tools::getValue('id_address_invoice'))));
								if ($address_delivery->id_customer != $this->context->customer->id || $address_invoice->id_customer != $this->context->customer->id)
									$this->errors[] = Tools::displayError('This address is not yours.');
								elseif (!Address::isCountryActiveById((int)(Tools::getValue('id_address_delivery'))))
									$this->errors[] = Tools::displayError('This address is not in a valid area.');
								elseif (!Validate::isLoadedObject($address_delivery) || !Validate::isLoadedObject($address_invoice) || $address_invoice->deleted || $address_delivery->deleted)
									$this->errors[] = Tools::displayError('This address is invalid.');
								else
								{
									$this->context->cart->id_address_delivery = (int)(Tools::getValue('id_address_delivery'));
									$this->context->cart->id_address_invoice = Tools::isSubmit('same') ? $this->context->cart->id_address_delivery : (int)(Tools::getValue('id_address_invoice'));
									if (!$this->context->cart->update())
										$this->errors[] = Tools::displayError('An error occurred while updating your cart.');

									// Address has changed, so we check if the cart rules still apply
									CartRule::autoRemoveFromCart($this->context);
									CartRule::autoAddToCart($this->context);
		
									if (!$this->context->cart->isMultiAddressDelivery())
										$this->context->cart->setNoMultishipping(); // As the cart is no multishipping, set each delivery address lines with the main delivery address

									if (!count($this->errors))
									{
										$result = $this->_getCarrierList();
										// Wrapping fees
										$wrapping_fees = $this->context->cart->getGiftWrappingPrice(false);
										$wrapping_fees_tax_inc = $wrapping_fees = $this->context->cart->getGiftWrappingPrice();
										$result = array_merge($result, array(
											'HOOK_TOP_PAYMENT' => Hook::exec('displayPaymentTop'),
											'HOOK_PAYMENT' => $this->_getPaymentMethods(),
											'gift_price' => Tools::displayPrice(Tools::convertPrice(Product::getTaxCalculationMethod() == 1 ? $wrapping_fees : $wrapping_fees_tax_inc, new Currency((int)($this->context->cookie->id_currency)))),
											'carrier_data' => $this->_getCarrierList()),
											$this->getFormatedSummaryDetail()
										);
										die(Tools::jsonEncode($result));
									}
								}
								if (count($this->errors))
									die(Tools::jsonEncode(array(
										'hasError' => true,
										'errors' => $this->errors
									)));
							}
							die(Tools::displayError());
							break;

						case 'multishipping':
							$this->_assignSummaryInformations();
							$this->context->smarty->assign('product_list', $this->context->cart->getProducts());
							
							if ($this->context->customer->id)
								$this->context->smarty->assign('address_list', $this->context->customer->getAddresses($this->context->language->id));
							else
								$this->context->smarty->assign('address_list', array());
							$this->setTemplate(_PS_THEME_DIR_.'order-address-multishipping-products.tpl');
							$this->display();
							die();
							break;

						case 'cartReload':
							$this->_assignSummaryInformations();
							if ($this->context->customer->id)
								$this->context->smarty->assign('address_list', $this->context->customer->getAddresses($this->context->language->id));
							else
								$this->context->smarty->assign('address_list', array());
							$this->context->smarty->assign('opc', true);
							$this->setTemplate(_PS_THEME_DIR_.'shopping-cart.tpl');
							$this->display();
							die();
							break;

						case 'noMultiAddressDelivery':
							$this->context->cart->setNoMultishipping();
							die();
							break;

						default:
							throw new PrestaShopException('Unknown method "'.Tools::getValue('method').'"');
					}
				}
				else
					throw new PrestaShopException('Method is not defined');
			}
		}
		elseif (Tools::isSubmit('ajax'))
			throw new PrestaShopException('Method is not defined');
	}

	public function setMedia()
	{
		parent::setMedia();

		if ($this->context->getMobileDevice() == false)
		{
			// Adding CSS style sheet
			$this->addCSS(_THEME_CSS_DIR_.'order-opc.css');
			// Adding JS files
			$this->addJS(_THEME_JS_DIR_.'order-opc.js');
			$this->addJqueryPlugin('scrollTo');
		}
		else
			$this->addJS(_THEME_MOBILE_JS_DIR_.'opc.js');
		$this->addJS(_THEME_JS_DIR_.'tools/statesManagement.js');
	}

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		// SHOPPING CART
		$this->_assignSummaryInformations();
		// WRAPPING AND TOS
		$this->_assignWrappingAndTOS();

		$selectedCountry = (int)(Configuration::get('PS_COUNTRY_DEFAULT'));

		if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES'))
			$countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
		else
			$countries = Country::getCountries($this->context->language->id, true);
		
		// If a rule offer free-shipping, force hidding shipping prices
		$free_shipping = false;
		foreach ($this->context->cart->getCartRules() as $rule)
			if ($rule['free_shipping'])
			{
				$free_shipping = true;
				break;
			}

		$this->context->smarty->assign(array(
			'free_shipping' => $free_shipping,
			'isGuest' => isset($this->context->cookie->is_guest) ? $this->context->cookie->is_guest : 0,
			'countries' => $countries,
			'sl_country' => isset($selectedCountry) ? $selectedCountry : 0,
			'PS_GUEST_CHECKOUT_ENABLED' => Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
			'errorCarrier' => Tools::displayError('You must choose a carrier before', false),
			'errorTOS' => Tools::displayError('You must accept the Terms of Service before', false),
			'isPaymentStep' => (bool)(isset($_GET['isPaymentStep']) && $_GET['isPaymentStep']),
			'genders' => Gender::getGenders(),
		));
		/* Call a hook to display more information on form */
		self::$smarty->assign(array(
			'HOOK_CREATE_ACCOUNT_FORM' => Hook::exec('displayCustomerAccountForm'),
			'HOOK_CREATE_ACCOUNT_TOP' => Hook::exec('displayCustomerAccountFormTop')
		));
		$years = Tools::dateYears();
		$months = Tools::dateMonths();
		$days = Tools::dateDays();
		$this->context->smarty->assign(array(
			'years' => $years,
			'months' => $months,
			'days' => $days,
		));

		/* Load guest informations */
		if ($this->isLogged && $this->context->cookie->is_guest)
			$this->context->smarty->assign('guestInformations', $this->_getGuestInformations());

		if ($this->isLogged)
			$this->_assignAddress(); // ADDRESS
		// CARRIER
		$this->_assignCarrier();
		// PAYMENT
		$this->_assignPayment();
		Tools::safePostVars();

		$this->context->smarty->assign('newsletter', (int)Module::getInstanceByName('blocknewsletter')->active);

		$this->_processAddressFormat();
		$this->setTemplate(_PS_THEME_DIR_.'order-opc.tpl');
	}

	protected function _getGuestInformations()
	{
		$customer = $this->context->customer;
		$address_delivery = new Address($this->context->cart->id_address_delivery);

		if ($customer->birthday)
			$birthday = explode('-', $customer->birthday);
		else
			$birthday = array('0', '0', '0');

		return array(
			'id_customer' => (int)$customer->id,
			'email' => Tools::htmlentitiesUTF8($customer->email),
			'customer_lastname' => Tools::htmlentitiesUTF8($customer->lastname),
			'customer_firstname' => Tools::htmlentitiesUTF8($customer->firstname),
			'newsletter' => (int)$customer->newsletter,
			'optin' => (int)$customer->optin,
			'id_address_delivery' => (int)$this->context->cart->id_address_delivery,
			'company' => Tools::htmlentitiesUTF8($address_delivery->company),
			'lastname' => Tools::htmlentitiesUTF8($address_delivery->lastname),
			'firstname' => Tools::htmlentitiesUTF8($address_delivery->firstname),
			'vat_number' => Tools::htmlentitiesUTF8($address_delivery->vat_number),
			'dni' => Tools::htmlentitiesUTF8($address_delivery->dni),
			'address1' => Tools::htmlentitiesUTF8($address_delivery->address1),
			'postcode' => Tools::htmlentitiesUTF8($address_delivery->postcode),
			'city' => Tools::htmlentitiesUTF8($address_delivery->city),
			'phone' => Tools::htmlentitiesUTF8($address_delivery->phone),
			'phone_mobile' => Tools::htmlentitiesUTF8($address_delivery->phone_mobile),
			'id_country' => (int)($address_delivery->id_country),
			'id_state' => (int)($address_delivery->id_state),
			'id_gender' => (int)$customer->id_gender,
			'sl_year' => $birthday[0],
			'sl_month' => $birthday[1],
			'sl_day' => $birthday[2]
		);
	}

	protected function _assignCarrier()
	{
		if (!$this->isLogged)
		{
			$carriers = $this->context->cart->simulateCarriersOutput();
			$this->context->smarty->assign(array(
				'HOOK_EXTRACARRIER' => null,
				'HOOK_EXTRACARRIER_ADDR' => null,
				'HOOK_BEFORECARRIER' => Hook::exec('displayBeforeCarrier', array(
					'carriers' => $carriers,
					'checked' => $this->context->cart->simulateCarrierSelectedOutput(),
					'delivery_option_list' => $this->context->cart->getDeliveryOptionList(),
					'delivery_option' => $this->context->cart->getDeliveryOption(null, true)
				))
			));
		}
		else
			parent::_assignCarrier();
	}

	protected function _assignPayment()
	{
		$this->context->smarty->assign(array(
			'HOOK_TOP_PAYMENT' => ($this->isLogged ? Hook::exec('displayPaymentTop') : ''),
			'HOOK_PAYMENT' => $this->_getPaymentMethods()
		));
	}

	protected function _getPaymentMethods()
	{
		if (!$this->isLogged)
			return '<p class="warning">'.Tools::displayError('Please sign in to see payment methods').'</p>';
		if ($this->context->cart->OrderExists())
			return '<p class="warning">'.Tools::displayError('Error: this order has already been validated').'</p>';
		if (!$this->context->cart->id_customer || !Customer::customerIdExistsStatic($this->context->cart->id_customer) || Customer::isBanned($this->context->cart->id_customer))
			return '<p class="warning">'.Tools::displayError('Error: no customer').'</p>';
		$address_delivery = new Address($this->context->cart->id_address_delivery);
		$address_invoice = ($this->context->cart->id_address_delivery == $this->context->cart->id_address_invoice ? $address_delivery : new Address($this->context->cart->id_address_invoice));
		if (!$this->context->cart->id_address_delivery || !$this->context->cart->id_address_invoice || !Validate::isLoadedObject($address_delivery) || !Validate::isLoadedObject($address_invoice) || $address_invoice->deleted || $address_delivery->deleted)
			return '<p class="warning">'.Tools::displayError('Error: please choose an address').'</p>';
		if (count($this->context->cart->getDeliveryOptionList()) == 0 && !$this->context->cart->isVirtualCart())
		{
			if ($this->context->cart->isMultiAddressDelivery())
				return '<p class="warning">'.Tools::displayError('Error: There are no carriers available that deliver to some of your addresses').'</p>';
			else
				return '<p class="warning">'.Tools::displayError('Error: There are no carriers available that deliver to this address').'</p>';
		}
		if (!$this->context->cart->getDeliveryOption(null, false) && !$this->context->cart->isVirtualCart())
			return '<p class="warning">'.Tools::displayError('Error: please choose a carrier').'</p>';
		if (!$this->context->cart->id_currency)
			return '<p class="warning">'.Tools::displayError('Error: no currency has been selected').'</p>';
		if (!$this->context->cookie->checkedTOS && Configuration::get('PS_CONDITIONS'))
			return '<p class="warning">'.Tools::displayError('Please accept the Terms of Service').'</p>';
		
		/* If some products have disappear */
		if (!$this->context->cart->checkQuantities())
			return '<p class="warning">'.Tools::displayError('An item in your cart is no longer available, you cannot proceed with your order.').'</p>';

		/* Check minimal amount */
		$currency = Currency::getCurrency((int)$this->context->cart->id_currency);

		$minimalPurchase = Tools::convertPrice((float)Configuration::get('PS_PURCHASE_MINIMUM'), $currency);
		if ($this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS) < $minimalPurchase)
			return '<p class="warning">'.sprintf(
				Tools::displayError('A minimum purchase total of %d is required in order to validate your order.'),
				Tools::displayPrice($minimalPurchase, $currency)
			).'</p>';

		/* Bypass payment step if total is 0 */
		if ($this->context->cart->getOrderTotal() <= 0)
			return '<p class="center"><input type="button" class="exclusive_large" name="confirmOrder" id="confirmOrder" value="'.Tools::displayError('I confirm my order').'" onclick="confirmFreeOrder();" /></p>';

		$return = Hook::exec('displayPayment');
		if (!$return)
			return '<p class="warning">'.Tools::displayError('No payment method is available').'</p>';
		return $return;
	}

	protected function _getCarrierList()
	{
		$address_delivery = new Address($this->context->cart->id_address_delivery);
		
		$cms = new CMS(Configuration::get('PS_CONDITIONS_CMS_ID'), $this->context->language->id);
		$link_conditions = $this->context->link->getCMSLink($cms, $cms->link_rewrite, true);
		if (!strpos($link_conditions, '?'))
			$link_conditions .= '?content_only=1';
		else
			$link_conditions .= '&content_only=1';
		
		// If a rule offer free-shipping, force hidding shipping prices
		$free_shipping = false;
		foreach ($this->context->cart->getCartRules() as $rule)
			if ($rule['free_shipping'])
			{
				$free_shipping = true;
				break;
			}
		
		$carriers = $this->context->cart->simulateCarriersOutput();
		$delivery_option = $this->context->cart->getDeliveryOption(null, false, false);

		$wrapping_fees = $this->context->cart->getGiftWrappingPrice(false);
		$wrapping_fees_tax_inc = $wrapping_fees = $this->context->cart->getGiftWrappingPrice();

		$vars = array(
			'free_shipping' => $free_shipping,
			'checkedTOS' => (int)($this->context->cookie->checkedTOS),
			'recyclablePackAllowed' => (int)(Configuration::get('PS_RECYCLABLE_PACK')),
			'giftAllowed' => (int)(Configuration::get('PS_GIFT_WRAPPING')),
			'cms_id' => (int)(Configuration::get('PS_CONDITIONS_CMS_ID')),
			'conditions' => (int)(Configuration::get('PS_CONDITIONS')),
			'link_conditions' => $link_conditions,
			'recyclable' => (int)($this->context->cart->recyclable),
			'gift_wrapping_price' => (float)$wrapping_fees,
			'total_wrapping_cost' => Tools::convertPrice($wrapping_fees_tax_inc, $this->context->currency),
			'total_wrapping_tax_exc_cost' => Tools::convertPrice($wrapping_fees, $this->context->currency),
			'delivery_option_list' => $this->context->cart->getDeliveryOptionList(),
			'carriers' => $carriers,
			'checked' => $this->context->cart->simulateCarrierSelectedOutput(),
			'delivery_option' => $delivery_option,
			'address_collection' => $this->context->cart->getAddressCollection(),
			'opc' => true,
			'HOOK_BEFORECARRIER' => Hook::exec('displayBeforeCarrier', array(
				'carriers' => $carriers,
				'delivery_option_list' => $this->context->cart->getDeliveryOptionList(),
				'delivery_option' => $delivery_option
			))
		);
		
		Cart::addExtraCarriers($vars);
		
		$this->context->smarty->assign($vars);

		if (!Address::isCountryActiveById((int)($this->context->cart->id_address_delivery)) && $this->context->cart->id_address_delivery != 0)
			$this->errors[] = Tools::displayError('This address is not in a valid area.');
		elseif ((!Validate::isLoadedObject($address_delivery) || $address_delivery->deleted) && $this->context->cart->id_address_delivery != 0)
			$this->errors[] = Tools::displayError('This address is invalid.');
		else
		{
			$result = array(
				'HOOK_BEFORECARRIER' => Hook::exec('displayBeforeCarrier', array(
					'carriers' => $carriers,
					'delivery_option_list' => $this->context->cart->getDeliveryOptionList(),
					'delivery_option' => $this->context->cart->getDeliveryOption(null, true)
				)),
				'carrier_block' => $this->context->smarty->fetch(_PS_THEME_DIR_.'order-carrier.tpl')
			);
			Cart::addExtraCarriers($result);
			return $result;
		}
		if (count($this->errors))
			return array(
				'hasError' => true,
				'errors' => $this->errors,
				'carrier_block' => $this->context->smarty->fetch(_PS_THEME_DIR_.'order-carrier.tpl')
			);
	}

	protected function _processAddressFormat()
	{
		$selectedCountry = (int)(Configuration::get('PS_COUNTRY_DEFAULT'));

		$address_delivery = new Address((int)$this->context->cart->id_address_delivery);
		$address_invoice = new Address((int)$this->context->cart->id_address_invoice);

		$inv_adr_fields = AddressFormat::getOrderedAddressFields((int)$address_delivery->id_country, false, true);
		$dlv_adr_fields = AddressFormat::getOrderedAddressFields((int)$address_invoice->id_country, false, true);

		$inv_all_fields = array();
		$dlv_all_fields = array();

		foreach (array('inv', 'dlv') as $adr_type)
		{
			foreach (${$adr_type.'_adr_fields'} as $fields_line)
				foreach (explode(' ', $fields_line) as $field_item)
					${$adr_type.'_all_fields'}[] = trim($field_item);

			$this->context->smarty->assign($adr_type.'_adr_fields', ${$adr_type.'_adr_fields'});
			$this->context->smarty->assign($adr_type.'_all_fields', ${$adr_type.'_all_fields'});
		}
	}
	
	protected function getFormatedSummaryDetail()
	{
		$result = array('summary' => $this->context->cart->getSummaryDetails(),
							'customizedDatas' => Product::getAllCustomizedDatas($this->context->cart->id, null, true)
						);
		foreach ($result['summary']['products'] as $key => &$product)
		{
			$product['quantity_without_customization'] = $product['quantity'];
			if ($result['customizedDatas'])
			{
				foreach ($result['customizedDatas'][(int)$product['id_product']][(int)$product['id_product_attribute']] as $addresses)
					foreach ($addresses as $customization)
						$product['quantity_without_customization'] -= (int)$customization['quantity'];
			}
		}
		
		if ($result['customizedDatas'])
			Product::addCustomizationPrice($result['summary']['products'], $result['customizedDatas']);
		return $result;
	}
	
}

