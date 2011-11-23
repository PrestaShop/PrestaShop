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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

abstract class PaymentModuleCore extends Module
{
	/** @var integer Current order's id */
	public	$currentOrder;
	public	$currencies = true;
	public	$currencies_mode = 'checkbox';

	public function install()
	{
		if (!parent::install())
			return false;

		// Insert currencies availability
		if ($this->currencies_mode == 'checkbox')
		{
			if (!Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'module_currency` (id_module, id_currency)
			SELECT '.(int)($this->id).', id_currency FROM `'._DB_PREFIX_.'currency` WHERE deleted = 0'))
				return false;
		}
		elseif ($this->currencies_mode == 'radio')
		{
			if (!Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'module_currency` (id_module, id_currency)
			VALUES ('.(int)($this->id).', -2)'))
				return false;
		}
		else
			Tools::displayError('No currency mode for payment module');

		// Insert countries availability
		$return = Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'module_country` (id_module, id_country)
		SELECT '.(int)($this->id).', id_country FROM `'._DB_PREFIX_.'country` WHERE active = 1');
		// Insert group availability
		$return &= Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'module_group` (id_module, id_group)
		SELECT '.(int)($this->id).', id_group FROM `'._DB_PREFIX_.'group`');

		return $return;
	}

	public function uninstall()
	{
		if (!Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'module_country` WHERE id_module = '.(int)($this->id))
			OR !Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'module_currency` WHERE id_module = '.(int)($this->id))
			OR !Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'module_group` WHERE id_module = '.(int)($this->id)))
			return false;
		return parent::uninstall();
	}

	/**
	* Validate an order in database
	* Function called from a payment module
	*
	* @param integer $id_cart Value
	* @param integer $id_order_state Value
	* @param float $amountPaid Amount really paid by customer (in the default currency)
	* @param string $paymentMethod Payment method (eg. 'Credit card')
	* @param string $message Message to attach to order
	*/
	public function validateOrder($id_cart, $id_order_state, $amountPaid, $paymentMethod = 'Unknown',
		$message = NULL, $extraVars = array(), $currency_special = NULL, $dont_touch_amount = false,
		$secure_key = false, Shop $shop = null)
	{
		$cart = new Cart((int)($id_cart));

		if (!$shop)
			$shop = Context::getContext()->shop;
		// Does order already exists ?
		if (Validate::isLoadedObject($cart) AND $cart->OrderExists() == false)
		{
			if ($secure_key !== false AND $secure_key != $cart->secure_key)
				die(Tools::displayError());

			// For each package, generate an order
			$delivery_option_list = $cart->getDeliveryOptionList();
			$package_list = $cart->getPackageList();
			$cart_delivery_option = unserialize($cart->delivery_option);
			
			// If some delivery options are not defined, or not valid, use the first valid option
			foreach ($delivery_option_list as $id_address => $package)
				if (!isset($cart_delivery_option[$id_address]) || !array_key_exists($cart_delivery_option[$id_address], $package))
					foreach ($package as $key => $val)
					{
						$cart_delivery_option[$id_address] = $key;
						break;
					}

			$order_list = array();
			$order_detail_list = array();
			$reference = Order::generateReference();
			$this->currentOrderReference = $reference;

			$id_currency = $currency_special ? (int)($currency_special) : (int)($cart->id_currency);
			$currency = new Currency($id_currency);

			$this->context->cart->order_reference = $reference;

			$orderCreationFailed = false;
			$cart_total_paid = (float)Tools::ps_round((float)($cart->getOrderTotal(true, Cart::BOTH)), 2);

			if ($cart->orderExists())
			{
				$errorMessage = Tools::displayError('An order has already been placed using this cart.');
				Logger::addLog($errorMessage, 4, '0000001', 'Cart', intval($cart->id));
				die($errorMessage);
			}

			foreach ($cart_delivery_option as $id_address => $key_carriers)
				foreach ($delivery_option_list[$id_address][$key_carriers]['carrier_list'] as $id_carrier => $data)
					foreach ($data['package_list'] as $id_package)
					{
						$product_list = $package_list[$id_address][$id_package]['product_list'];
						$carrier = new Carrier($id_carrier, $cart->id_lang);
						$order = new Order();
						$order->id_carrier = (int)$carrier->id;
						$order->id_customer = (int)($cart->id_customer);
						$order->id_address_invoice = (int)($cart->id_address_invoice);
						$order->id_address_delivery = (int)$id_address;
						$order->id_currency = $id_currency;
						$order->id_lang = (int)($cart->id_lang);
						$order->id_warehouse = $package_list[$id_address][$id_package]['id_warehouse'];
						$order->id_cart = (int)($cart->id);
						$order->reference = $reference;

						$order->id_shop = (int)($shop->getID() ? $shop->getID() : $cart->id_shop);
						$order->id_group_shop = (int)($shop->getID() ? $shop->getGroupID() : $cart->id_group_shop);

						$customer = new Customer((int)($order->id_customer));
						$order->secure_key = ($secure_key ? pSQL($secure_key) : pSQL($customer->secure_key));
						$order->payment = $paymentMethod;
						if (isset($this->name))
							$order->module = $this->name;
						$order->recyclable = $cart->recyclable;
						$order->gift = (int)($cart->gift);
						$order->gift_message = $cart->gift_message;
						$order->conversion_rate = $currency->conversion_rate;
						$amountPaid = !$dont_touch_amount ? Tools::ps_round((float)($amountPaid), 2) : $amountPaid;
						$order->total_paid_real = $amountPaid;
						$order->total_products = (float)$cart->getOrderTotal(false, Cart::ONLY_PRODUCTS, $product_list, $id_carrier);
						$order->total_products_wt = (float)$cart->getOrderTotal(true, Cart::ONLY_PRODUCTS, $product_list, $id_carrier);

						$order->total_discounts = (float)abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS, $product_list, $id_carrier));
						$order->total_discounts_tax_excl = (float)abs($cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS, $product_list, $id_carrier));
						$order->total_discounts_tax_incl = (float)abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS, $product_list, $id_carrier));

						$order->total_shipping = (float)$cart->getPackageShippingCost((int)$id_carrier, true, null, $product_list, $id_carrier);
						$order->total_shipping_tax_excl = (float)$cart->getPackageShippingCost((int)$id_carrier, false, null, $product_list, $id_carrier);
						$order->total_shipping_tax_incl = (float)$cart->getPackageShippingCost((int)$id_carrier, true, null, $product_list, $id_carrier);

						if (Validate::isLoadedObject($carrier))
							$order->carrier_tax_rate = $carrier->getTaxesRate(new Address($cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));

						$order->total_wrapping = (float)abs($cart->getOrderTotal(true, Cart::ONLY_WRAPPING, $product_list, $id_carrier));
						$order->total_wrapping_tax_excl = (float)abs($cart->getOrderTotal(false, Cart::ONLY_WRAPPING, $product_list, $id_carrier));
						$order->total_wrapping_tax_incl = (float)abs($cart->getOrderTotal(true, Cart::ONLY_WRAPPING, $product_list, $id_carrier));

						$order->total_paid = (float)Tools::ps_round((float)($cart->getOrderTotal(true, Cart::BOTH, $product_list, $id_carrier)), 2);
						$order->total_paid_tax_excl = (float)Tools::ps_round((float)($cart->getOrderTotal(false, Cart::BOTH, $product_list, $id_carrier)), 2);
						$order->total_paid_tax_incl = (float)Tools::ps_round((float)($cart->getOrderTotal(true, Cart::BOTH, $product_list, $id_carrier)), 2);

						$order->invoice_date = '0000-00-00 00:00:00';
						$order->delivery_date = '0000-00-00 00:00:00';
						// Amount paid by customer is not the right one -> Status = payment error
						// We don't use the following condition to avoid the float precision issues : http://www.php.net/manual/en/language.types.float.php
						// if ($order->total_paid != $order->total_paid_real)
						// We use number_format in order to compare two string
						if (number_format($cart_total_paid, 2) != number_format($order->total_paid_real, 2))
							$id_order_state = Configuration::get('PS_OS_ERROR');

						// Creating order
						$result = $order->add();

						$order_list[] = $order;

						// Insert new Order detail list using cart for the current order
						$order_detail = new OrderDetail(null, null, $this->context);
						$order_detail->createList($order, $cart, $id_order_state, $product_list);
						$order_detail_list[] = $order_detail;

						// Adding an entry in order_carrier table
						Db::getInstance()->execute('
						INSERT INTO `'._DB_PREFIX_.'order_carrier` (`id_order`, `id_carrier`, `weight`, `shipping_cost_tax_excl`, `shipping_cost_tax_incl`, `date_add`) VALUES
						('.(int)$order->id.', '.(int)$carrier->id.', '.(float)$order->getTotalWeight().', '.(float)$order->total_shipping_tax_excl.', '.(float)$order->total_shipping_tax_incl.', NOW())');
					}
			// Register Payment
			if (!$order->addOrderPayment($amountPaid))
			{
				$errorMessage = Tools::displayError('Can\'t save payment');
				Logger::addLog($errorMessage, 4, '0000003', 'Order', intval($order->id));
				die($errorMessage);
			}

			// Next !
			foreach ($order_detail_list as $key => $order_detail)
			{
				$order = $order_list[$key];
				if (!$orderCreationFailed AND isset($order->id))
				{
					if (!$secure_key)
						$message .= $this->l('Warning : the secure key is empty, check your payment account before validation');
					// Optional message to attach to this order
					if (isset($message) AND !empty($message))
					{
						$msg = new Message();
						$message = strip_tags($message, '<br>');
						if (Validate::isCleanHtml($message))
						{
							$msg->message = $message;
							$msg->id_order = intval($order->id);
							$msg->private = 1;
							$msg->add();
						}
					}

					// Insert new Order detail list using cart for the current order
					//$orderDetail = new OrderDetail(null, null, $this->context);
					//$orderDetail->createList($order, $cart, $id_order_state);

					//$this->addPCC($order->id, $order->id_currency, $amountPaid);

					// Construct order detail table for the email
					$productsList = '';
					$products = $cart->getProducts();
					foreach ($products AS $key => $product)
					{
						$price = Product::getPriceStatic((int)($product['id_product']), false, ($product['id_product_attribute'] ? (int)($product['id_product_attribute']) : NULL), 6, NULL, false, true, $product['cart_quantity'], false, (int)($order->id_customer), (int)($order->id_cart), (int)($order->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
						$price_wt = Product::getPriceStatic((int)($product['id_product']), true, ($product['id_product_attribute'] ? (int)($product['id_product_attribute']) : NULL), 2, NULL, false, true, $product['cart_quantity'], false, (int)($order->id_customer), (int)($order->id_cart), (int)($order->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));

						$customizationQuantity = 0;
						if (isset($customizedDatas[$product['id_product']][$product['id_product_attribute']]))
						{
							$customizationText = '';
							foreach ($customizedDatas[$product['id_product']][$product['id_product_attribute']] AS $customization)
							{
								if (isset($customization['datas'][Product::CUSTOMIZE_TEXTFIELD]))
									foreach ($customization['datas'][Product::CUSTOMIZE_TEXTFIELD] AS $text)
										$customizationText .= $text['name'].':'.' '.$text['value'].'<br />';

								if (isset($customization['datas'][Product::CUSTOMIZE_FILE]))
									$customizationText .= sizeof($customization['datas'][Product::CUSTOMIZE_FILE]) .' '. Tools::displayError('image(s)').'<br />';

								$customizationText .= '---<br />';
							}

							$customizationText = rtrim($customizationText, '---<br />');

							$customizationQuantity = (int)($product['customizationQuantityTotal']);
							$productsList .=
							'<tr style="background-color: '.($key % 2 ? '#DDE2E6' : '#EBECEE').';">
								<td style="padding: 0.6em 0.4em;">'.$product['reference'].'</td>
								<td style="padding: 0.6em 0.4em;"><strong>'.$product['name'].(isset($product['attributes']) ? ' - '.$product['attributes'] : '').' - '.$this->l('Customized').(!empty($customizationText) ? ' - '.$customizationText : '').'</strong></td>
								<td style="padding: 0.6em 0.4em; text-align: right;">'.Tools::displayPrice(Product::getTaxCalculationMethod() == PS_TAX_EXC ? $price : $price_wt, $currency, false).'</td>
								<td style="padding: 0.6em 0.4em; text-align: center;">'.$customizationQuantity.'</td>
								<td style="padding: 0.6em 0.4em; text-align: right;">'.Tools::displayPrice($customizationQuantity * (Product::getTaxCalculationMethod() == PS_TAX_EXC ? $price : $price_wt), $currency, false).'</td>
							</tr>';
						}

						if (!$customizationQuantity OR (int)$product['cart_quantity'] > $customizationQuantity)
							$productsList .=
							'<tr style="background-color: '.($key % 2 ? '#DDE2E6' : '#EBECEE').';">
								<td style="padding: 0.6em 0.4em;">'.$product['reference'].'</td>
								<td style="padding: 0.6em 0.4em;"><strong>'.$product['name'].(isset($product['attributes']) ? ' - '.$product['attributes'] : '').'</strong></td>
								<td style="padding: 0.6em 0.4em; text-align: right;">'.Tools::displayPrice(Product::getTaxCalculationMethod() == PS_TAX_EXC ? $price : $price_wt, $currency, false).'</td>
								<td style="padding: 0.6em 0.4em; text-align: center;">'.((int)($product['cart_quantity']) - $customizationQuantity).'</td>
								<td style="padding: 0.6em 0.4em; text-align: right;">'.Tools::displayPrice(((int)($product['cart_quantity']) - $customizationQuantity) * (Product::getTaxCalculationMethod() == PS_TAX_EXC ? $price : $price_wt), $currency, false).'</td>
							</tr>';
					} // end foreach ($products)

					$cartRulesList = '';
					$result = $cart->getCartRules();
					$cartRules = ObjectModel::hydrateCollection('CartRule', $result, (int)$order->id_lang);
					foreach ($cartRules as $cartRule)
					{
						$value = $cartRule->getContextualValue(true);
						// Todo : has not been tested because order processing wasn't functionnal
						if ($value > $order->total_products_wt && $cartRule->partial_use == 1 && $cartRule->reduction_amount > 0)
						{
							$voucher = clone $cartRule;
							unset($voucher->id);
							$voucher->code = empty($voucher->code) ? substr(md5($order->id.'-'.$order->id_customer.'-'.$cartRule->id), 0, 16) : $voucher->code.'-2';
							$voucher->reduction_amount = $value - $order->total_products_wt;
							$voucher->id_customer = $order->id_customer;
							$voucher->quantity = 1;
							if ($voucher->add())
							{
								CartRule::copyConditions($cartRule->id, $voucher->id);
								$params['{voucher_amount}'] = Tools::displayPrice($voucher->reduction_amount, $currency, false);
								$params['{voucher_num}'] = $voucher->code;
								$params['{firstname}'] = $customer->firstname;
								$params['{lastname}'] = $customer->lastname;
								$params['{id_order}'] = $order->id;
								Mail::Send((int)$order->id_lang, 'voucher', Mail::l('New voucher regarding your order #').$order->id, $params, $customer->email, $customer->firstname.' '.$customer->lastname);
							}
						}

						$order->addCartRule($cartRule->id, $cartRule->name, $value);
						if ($id_order_state != Configuration::get('PS_OS_ERROR') AND $id_order_state != Configuration::get('PS_OS_CANCELED'))
							$cartRule->quantity = $cartRule->quantity - 1;
						$cartRule->update();

						$cartRulesList .= '
						<tr style="background-color:#EBECEE;">
							<td colspan="4" style="padding:0.6em 0.4em;text-align:right">'.$this->l('Voucher name:').' '.$cartRule->name.'</td>
							<td style="padding:0.6em 0.4em;text-align:right">'.($value != 0.00 ? '-' : '').Tools::displayPrice($value, $currency, false).'</td>
						</tr>';
					}

					// Specify order id for message
					$oldMessage = Message::getMessageByCartId((int)($cart->id));
					if ($oldMessage)
					{
						$message = new Message((int)$oldMessage['id_message']);
						$message->id_order = (int)$order->id;
						$message->update();
					}

					// Hook validate order
					$orderStatus = new OrderState((int)$id_order_state, (int)$order->id_lang);
					if (Validate::isLoadedObject($orderStatus))
					{
						Hook::exec('newOrder', array('cart' => $cart, 'order' => $order, 'customer' => $customer, 'currency' => $currency, 'orderStatus' => $orderStatus));
						foreach ($cart->getProducts() AS $product)
							if ($orderStatus->logable)
								ProductSale::addProductSale((int)$product['id_product'], (int)$product['cart_quantity']);
					}

					if (Configuration::get('PS_STOCK_MANAGEMENT') && $order_detail->getStockState())
					{
						$history = new OrderHistory();
						$history->id_order = (int)$order->id;
						$history->changeIdOrderState(Configuration::get('PS_OS_OUTOFSTOCK'), (int)$order->id);
						$history->addWithemail();
					}

					// Set order state in order history ONLY even if the "out of stock" status has not been yet reached
					// So you migth have two order states
					$new_history = new OrderHistory();
					$new_history->id_order = (int)$order->id;
					$new_history->changeIdOrderState((int)$id_order_state, (int)$order->id);
					$new_history->addWithemail(true, $extraVars);

					unset($order_detail);

					// Order is reloaded because the status just changed
					$order = new Order($order->id);

					// Send an e-mail to customer (one order = one email)
					if ($id_order_state != Configuration::get('PS_OS_ERROR') AND $id_order_state != Configuration::get('PS_OS_CANCELED') AND $customer->id)
					{
						$invoice = new Address((int)($order->id_address_invoice));
						$delivery = new Address((int)($order->id_address_delivery));
						$delivery_state = $delivery->id_state ? new State((int)($delivery->id_state)) : false;
						$invoice_state = $invoice->id_state ? new State((int)($invoice->id_state)) : false;

						$data = array(
						'{firstname}' => $customer->firstname,
						'{lastname}' => $customer->lastname,
						'{email}' => $customer->email,
						'{delivery_block_txt}' => $this->_getFormatedAddress($delivery, "\n"),
						'{invoice_block_txt}' => $this->_getFormatedAddress($invoice, "\n"),
						'{delivery_block_html}' => $this->_getFormatedAddress($delivery, "<br />",
							array(
								'firstname'	=> '<span style="color:#DB3484; font-weight:bold;">%s</span>',
								'lastname'	=> '<span style="color:#DB3484; font-weight:bold;">%s</span>')),
							'{invoice_block_html}' => $this->_getFormatedAddress($invoice, "<br />",
							array(
								'firstname'	=> '<span style="color:#DB3484; font-weight:bold;">%s</span>',
								'lastname'	=> '<span style="color:#DB3484; font-weight:bold;">%s</span>')),
						'{delivery_company}' => $delivery->company,
						'{delivery_firstname}' => $delivery->firstname,
						'{delivery_lastname}' => $delivery->lastname,
						'{delivery_address1}' => $delivery->address1,
						'{delivery_address2}' => $delivery->address2,
						'{delivery_city}' => $delivery->city,
						'{delivery_postal_code}' => $delivery->postcode,
						'{delivery_country}' => $delivery->country,
						'{delivery_state}' => $delivery->id_state ? $delivery_state->name : '',
						'{delivery_phone}' => ($delivery->phone) ? $delivery->phone : $delivery->phone_mobile,
						'{delivery_other}' => $delivery->other,
						'{invoice_company}' => $invoice->company,
						'{invoice_vat_number}' => $invoice->vat_number,
						'{invoice_firstname}' => $invoice->firstname,
						'{invoice_lastname}' => $invoice->lastname,
						'{invoice_address2}' => $invoice->address2,
						'{invoice_address1}' => $invoice->address1,
						'{invoice_city}' => $invoice->city,
						'{invoice_postal_code}' => $invoice->postcode,
						'{invoice_country}' => $invoice->country,
						'{invoice_state}' => $invoice->id_state ? $invoice_state->name : '',
						'{invoice_phone}' => ($invoice->phone) ? $invoice->phone : $invoice->phone_mobile,
						'{invoice_other}' => $invoice->other,
						'{order_name}' => sprintf("#%06d", (int)($order->id)),
						'{date}' => Tools::displayDate(date('Y-m-d H:i:s'), (int)($order->id_lang), 1),
						'{carrier}' => $carrier->name,
						'{payment}' => Tools::substr($order->payment, 0, 32),
						'{products}' => $productsList,
						'{discounts}' => $cartRulesList,
						'{total_paid}' => Tools::displayPrice($order->total_paid, $currency, false),
						'{total_products}' => Tools::displayPrice($order->total_paid - $order->total_shipping - $order->total_wrapping + $order->total_discounts, $currency, false),
						'{total_discounts}' => Tools::displayPrice($order->total_discounts, $currency, false),
						'{total_shipping}' => Tools::displayPrice($order->total_shipping, $currency, false),
						'{total_wrapping}' => Tools::displayPrice($order->total_wrapping, $currency, false));

						if (is_array($extraVars))
							$data = array_merge($data, $extraVars);

						// Join PDF invoice
						if ((int)(Configuration::get('PS_INVOICE')) AND Validate::isLoadedObject($orderStatus) AND $orderStatus->invoice AND $order->invoice_number)
						{
							$fileAttachment['content'] = PDF::invoice($order, 'S');
							$fileAttachment['name'] = Configuration::get('PS_INVOICE_PREFIX', (int)($order->id_lang)).sprintf('%06d', $order->invoice_number).'.pdf';
							$fileAttachment['mime'] = 'application/pdf';
						}
						else
							$fileAttachment = NULL;

						if (Validate::isEmail($customer->email))
							Mail::Send((int)$order->id_lang, 'order_conf', Mail::l('Order confirmation', (int)$order->id_lang), $data, $customer->email, $customer->firstname.' '.$customer->lastname, NULL, NULL, $fileAttachment);
					}
				}
				else
				{
					$errorMessage = Tools::displayError('Order creation failed');
					Logger::addLog($errorMessage, 4, '0000002', 'Cart', intval($order->id_cart));
					die($errorMessage);
				}
			}
			// Use the last order as currentOrder
			$this->currentOrder = (int)$order->id;
			return true;
		}
		else
		{
			$errorMessage = Tools::displayError('Cart can\'t be loaded or an order has already been placed using this cart');
			Logger::addLog($errorMessage, 4, '0000001', 'Cart', intval($cart->id));
			die($errorMessage);
		}
	}

	/**
	 * @param Object Address $the_address that needs to be txt formated
	 * @return String the txt formated address block
	 */
	private function _getTxtFormatedAddress($the_address)
	{
		$out = '';
		$adr_fields = AddressFormat::getOrderedAddressFields($the_address->id_country, false, true);
		$r_values = array();
		foreach($adr_fields as $fields_line)
		{
			$tmp_values = array();
			foreach (explode(' ', $fields_line) as $field_item)
			{
				$field_item = trim($field_item);
				$tmp_values[] = $the_address->{$field_item};
			}
			$r_values[] = implode(' ', $tmp_values);
		}

		$out = implode("\n", $r_values);
		return $out;
	}

	/**
	 * @param Object Address $the_address that needs to be txt formated
	 * @return String the txt formated address block
	 */

	private function _getFormatedAddress(Address $the_address, $line_sep, $fields_style = array())
	{
		return AddressFormat::generateAddress($the_address, array('avoid' => array()), $line_sep, ' ', $fields_style);
	}

	/**
	 * @param int $id_currency : this parameter is optionnal but on 1.5 version of Prestashop, it will be REQUIRED
	 * @return Currency
	 */
	public function getCurrency($current_id_currency = NULL)
	{
		if (!(int)$current_id_currency)
			$current_id_currency = Context::getContext()->currency->id;

		if (!$this->currencies)
			return false;
		if ($this->currencies_mode == 'checkbox')
		{
			$currencies = Currency::getPaymentCurrencies($this->id);
			return $currencies;
		}
		elseif ($this->currencies_mode == 'radio')
		{
			$currencies = Currency::getPaymentCurrenciesSpecial($this->id);
			$currency = $currencies['id_currency'];
			if ($currency == -1)
				$id_currency = (int)$current_id_currency;
			elseif ($currency == -2)
				$id_currency = (int)(Configuration::get('PS_CURRENCY_DEFAULT'));
			else
				$id_currency = $currency;
		}
		if (!isset($id_currency) OR empty($id_currency))
			return false;
		return (new Currency($id_currency));
	}

	/**
	 * Allows specified payment modules to be used by a specific currency
	 *
	 * @since 1.4.5
	 * @param int $id_currency
	 * @param array $id_module_list
	 * @return boolean
	 */
	public static function addCurrencyPermissions($id_currency, array $id_module_list = array())
	{
		$values = '';
		if (count($id_module_list) == 0)
		{
			// fetch all installed module ids
			$modules = PaymentModuleCore::getInstalledPaymentModules();
			foreach ($modules as $module)
				$id_module_list[] = $module['id_module'];
		}

		foreach ($id_module_list as $id_module)
			$values .= '('.(int)$id_module.','.(int)$id_currency.'),';

		if (!empty($values))
		{
			return Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'module_currency` (`id_module`, `id_currency`)
			VALUES '.rtrim($values, ',')
			);
		}

		return true;
	}

	/**
	 * List all installed and active payment modules
	 * @see Module::getPaymentModules() if you need a list of module related to the user context
	 *
	 * @since 1.4.5
	 * @return array module informations
	 */
	public static function getInstalledPaymentModules()
	{
		$hookPayment = 'Payment';
		if (Db::getInstance()->getValue('SELECT `id_hook` FROM `'._DB_PREFIX_.'hook` WHERE `name` = \'displayPayment\''))
			$hookPayment = 'displayPayment';

		return Db::getInstance()->executeS('
		SELECT DISTINCT m.`id_module`, h.`id_hook`, m.`name`, hm.`position`
		FROM `'._DB_PREFIX_.'module` m
		LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
		LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
		WHERE h.`name` = \''.pSQL($hookPayment).'\'
		AND m.`active` = 1
		');
	}


	public static function preCall($moduleName)
	{
		if (!parent::preCall($moduleName))
			return false;

		if (($moduleInstance = Module::getInstanceByName($moduleName)))
			if (!$moduleInstance->currencies OR ($moduleInstance->currencies AND sizeof(Currency::checkPaymentCurrencies($moduleInstance->id))))
				return true;

		return false;
	}

}

