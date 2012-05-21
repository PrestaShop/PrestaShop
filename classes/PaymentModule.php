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
			SELECT '.(int)$this->id.', id_currency FROM `'._DB_PREFIX_.'currency` WHERE deleted = 0'))
				return false;
		}
		elseif ($this->currencies_mode == 'radio')
		{
			if (!Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'module_currency` (id_module, id_currency)
			VALUES ('.(int)$this->id.', -2)'))
				return false;
		}
		else
			Tools::displayError('No currency mode for payment module');

		// Insert countries availability
		$return = Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'module_country` (id_module, id_country)
		SELECT '.(int)$this->id.', id_country FROM `'._DB_PREFIX_.'country` WHERE active = 1');

		return $return;
	}

	public function uninstall()
	{
		if (!Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'module_country` WHERE id_module = '.(int)$this->id)
			|| !Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'module_currency` WHERE id_module = '.(int)$this->id)
			|| !Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'module_group` WHERE id_module = '.(int)$this->id))
			return false;
		return parent::uninstall();
	}

	/**
	* Validate an order in database
	* Function called from a payment module
	*
	* @param integer $id_cart Value
	* @param integer $id_order_state Value
	* @param float $amount_paid Amount really paid by customer (in the default currency)
	* @param string $payment_method Payment method (eg. 'Credit card')
	* @param string $message Message to attach to order
	*/
	public function validateOrder($id_cart, $id_order_state, $amount_paid, $payment_method = 'Unknown',
		$message = null, $extra_vars = array(), $currency_special = null, $dont_touch_amount = false,
		$secure_key = false, Shop $shop = null)
	{
		$cart = new Cart($id_cart);
		$this->context->cart = $cart;

		if (!$shop)
			$shop = Context::getContext()->shop;
		// Does order already exists ?
		if (Validate::isLoadedObject($cart) && $cart->OrderExists() == false)
		{
			if ($secure_key !== false && $secure_key != $cart->secure_key)
				die(Tools::displayError());

			// For each package, generate an order
			$delivery_option_list = $cart->getDeliveryOptionList();
			$package_list = $cart->getPackageList();
			$cart_delivery_option = $cart->getDeliveryOption();

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

			$id_currency = $currency_special ? (int)$currency_special : (int)$cart->id_currency;
			$currency = new Currency($id_currency);

			$cart->order_reference = $reference;

			$order_creation_failed = false;
			$cart_total_paid = (float)Tools::ps_round((float)$cart->getOrderTotal(true, Cart::BOTH), 2);

			if ($cart->orderExists())
			{
				$error = Tools::displayError('An order has already been placed using this cart.');
				Logger::addLog($error, 4, '0000001', 'Cart', intval($cart->id));
				die($error);
			}

			$order_status = new OrderState((int)$id_order_state, (int)$cart->id_lang);
			if (!Validate::isLoadedObject($order_status))
				throw new PrestaShopException('Can\'t load Order state status');

			foreach ($cart_delivery_option as $id_address => $key_carriers)
				foreach ($delivery_option_list[$id_address][$key_carriers]['carrier_list'] as $id_carrier => $data)
					foreach ($data['package_list'] as $id_package)
					{
						$package_list[$id_address][$id_package]['id_carrier'] = $id_carrier;
					}
			
			foreach ($package_list as $id_address => $packageByAddress)
				foreach ($packageByAddress as $id_package => $package)
				{
					$product_list = $package['product_list'];
					$order = new Order();
					
					$carrier = null;
					if (!$cart->isVirtualCart() && isset($package['id_carrier']))
					{
						$carrier = new Carrier($package['id_carrier'], $cart->id_lang);
						$order->id_carrier = (int)$carrier->id;
					}
					else
						$order->id_carrier = 0;
					
					$order->id_customer = (int)$cart->id_customer;
					$order->id_address_invoice = (int)$cart->id_address_invoice;
					$order->id_address_delivery = (int)$id_address;
					$order->id_currency = $id_currency;
					$order->id_lang = (int)$cart->id_lang;
					$order->id_cart = (int)$cart->id;
					$order->reference = $reference;

					$order->id_shop = (int)(Shop::getContext() == Shop::CONTEXT_SHOP ? $shop->id : $cart->id_shop);
					$order->id_shop_group = (int)(Shop::getContext() == Shop::CONTEXT_SHOP ? $shop->id_shop_group : $cart->id_shop_group);

					$customer = new Customer($order->id_customer);
					$order->secure_key = ($secure_key ? pSQL($secure_key) : pSQL($customer->secure_key));
					$order->payment = $payment_method;
					if (isset($this->name))
						$order->module = $this->name;
					$order->recyclable = $cart->recyclable;
					$order->gift = (int)$cart->gift;
					$order->gift_message = $cart->gift_message;
					$order->conversion_rate = $currency->conversion_rate;
					$amount_paid = !$dont_touch_amount ? Tools::ps_round((float)$amount_paid, 2) : $amount_paid;
					$order->total_paid_real = 0;
					$order->total_products = (float)$cart->getOrderTotal(false, Cart::ONLY_PRODUCTS, $product_list, $carrier->id);
					$order->total_products_wt = (float)$cart->getOrderTotal(true, Cart::ONLY_PRODUCTS, $product_list, $carrier->id);

					$order->total_discounts = (float)abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS, $product_list, $carrier->id));
					$order->total_discounts_tax_excl = (float)abs($cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS, $product_list, $carrier->id));
					$order->total_discounts_tax_incl = (float)abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS, $product_list, $carrier->id));

					$order->total_shipping = (float)$cart->getPackageShippingCost((int)$carrier->id, true, null, $product_list);
					$order->total_shipping_tax_excl = (float)$cart->getPackageShippingCost((int)$carrier->id, false, null, $product_list);
					$order->total_shipping_tax_incl = (float)$cart->getPackageShippingCost((int)$carrier->id, true, null, $product_list);

					if (!is_null($carrier) && Validate::isLoadedObject($carrier))
						$order->carrier_tax_rate = $carrier->getTaxesRate(new Address($cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));

					$order->total_wrapping = (float)abs($cart->getOrderTotal(true, Cart::ONLY_WRAPPING, $product_list, $carrier->id));
					$order->total_wrapping_tax_excl = (float)abs($cart->getOrderTotal(false, Cart::ONLY_WRAPPING, $product_list, $carrier->id));
					$order->total_wrapping_tax_incl = (float)abs($cart->getOrderTotal(true, Cart::ONLY_WRAPPING, $product_list, $carrier->id));

					$order->total_paid = (float)Tools::ps_round((float)$cart->getOrderTotal(true, Cart::BOTH, $product_list, $carrier->id), 2);
					$order->total_paid_tax_excl = (float)Tools::ps_round((float)$cart->getOrderTotal(false, Cart::BOTH, $product_list, $carrier->id), 2);
					$order->total_paid_tax_incl = (float)Tools::ps_round((float)$cart->getOrderTotal(true, Cart::BOTH, $product_list, $carrier->id), 2);

					$order->invoice_date = '0000-00-00 00:00:00';
					$order->delivery_date = '0000-00-00 00:00:00';

					// Creating order
					$result = $order->add();

					// Register Payment only if the order status validate the order
					if ($result && $order_status->logable)
					{
						if (!$order->addOrderPayment($amount_paid))
							throw new PrestaShopException('Can\'t save Order Payment');
					}

					// Amount paid by customer is not the right one -> Status = payment error
					// We don't use the following condition to avoid the float precision issues : http://www.php.net/manual/en/language.types.float.php
					// if ($order->total_paid != $order->total_paid_real)
					// We use number_format in order to compare two string
					if ($order_status->logable && number_format($cart_total_paid, 2) != number_format($order->total_paid_real, 2))
						$id_order_state = Configuration::get('PS_OS_ERROR');

					$order_list[] = $order;

					// Insert new Order detail list using cart for the current order
					$order_detail = new OrderDetail(null, null, $this->context);
					$order_detail->createList($order, $cart, $id_order_state, $product_list, 0, true, $package_list[$id_address][$id_package]['id_warehouse']);
					$order_detail_list[] = $order_detail;

					// Adding an entry in order_carrier table
					if (!is_null($carrier))
					{
						$order_carrier = new OrderCarrier();
						$order_carrier->id_order = (int)$order->id;
						$order_carrier->id_carrier = (int)$carrier->id;
						$order_carrier->weight = (float)$order->getTotalWeight();
						$order_carrier->shipping_cost_tax_excl = (float)$order->total_shipping_tax_excl;
						$order_carrier->shipping_cost_tax_incl = (float)$order->total_shipping_tax_incl;
						$order_carrier->add();
					}
				}

			// Next !
			$only_one_gift = false;
			$cart_rule_used = array();
			foreach ($order_detail_list as $key => $order_detail)
			{
				$order = $order_list[$key];
				if (!$order_creation_failed & isset($order->id))
				{
					if (!$secure_key)
						$message .= '<br />'.Tools::displayError('Warning: the secure key is empty, check your payment account before validation');
					// Optional message to attach to this order
					if (isset($message) & !empty($message))
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

					// Construct order detail table for the email
					$products_list = '';
					$virtual_product = true;
					$products = $cart->getProducts();
					foreach ($products as $key => $product)
					{
						$price = Product::getPriceStatic((int)$product['id_product'], false, ($product['id_product_attribute'] ? (int)$product['id_product_attribute'] : null), 6, null, false, true, $product['cart_quantity'], false, (int)$order->id_customer, (int)$order->id_cart, (int)$order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
						$price_wt = Product::getPriceStatic((int)$product['id_product'], true, ($product['id_product_attribute'] ? (int)$product['id_product_attribute'] : null), 2, null, false, true, $product['cart_quantity'], false, (int)$order->id_customer, (int)$order->id_cart, (int)$order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});

						$customization_quantity = 0;
						if (isset($customized_datas[$product['id_product']][$product['id_product_attribute']]))
						{
							$customization_text = '';
							foreach ($customized_datas[$product['id_product']][$product['id_product_attribute']] as $customization)
							{
								if (isset($customization['datas'][Product::CUSTOMIZE_TEXTFIELD]))
									foreach ($customization['datas'][Product::CUSTOMIZE_TEXTFIELD] as $text)
										$customization_text .= $text['name'].': '.$text['value'].'<br />';

								if (isset($customization['datas'][Product::CUSTOMIZE_FILE]))
									$customization_text .= sprintf(Tools::displayError('%d image(s)'), count($customization['datas'][Product::CUSTOMIZE_FILE])).'<br />';

								$customization_text .= '---<br />';
							}

							$customization_text = rtrim($customization_text, '---<br />');

							$customization_quantity = (int)$product['customizationQuantityTotal'];
							$products_list .=
							'<tr style="background-color: '.($key % 2 ? '#DDE2E6' : '#EBECEE').';">
								<td style="padding: 0.6em 0.4em;">'.$product['reference'].'</td>
								<td style="padding: 0.6em 0.4em;"><strong>'.$product['name'].(isset($product['attributes']) ? ' - '.$product['attributes'] : '').' - '.$this->l('Customized').(!empty($customization_text) ? ' - '.$customization_text : '').'</strong></td>
								<td style="padding: 0.6em 0.4em; text-align: right;">'.Tools::displayPrice(Product::getTaxCalculationMethod() == PS_TAX_EXC ?  Tools::ps_round($price, 2) : $price_wt, $currency, false).'</td>
								<td style="padding: 0.6em 0.4em; text-align: center;">'.$customization_quantity.'</td>
								<td style="padding: 0.6em 0.4em; text-align: right;">'.Tools::displayPrice($customization_quantity * (Product::getTaxCalculationMethod() == PS_TAX_EXC ? Tools::ps_round($price, 2) : $price_wt), $currency, false).'</td>
							</tr>';
						}

						if (!$customization_quantity || (int)$product['cart_quantity'] > $customization_quantity)
							$products_list .=
							'<tr style="background-color: '.($key % 2 ? '#DDE2E6' : '#EBECEE').';">
								<td style="padding: 0.6em 0.4em;">'.$product['reference'].'</td>
								<td style="padding: 0.6em 0.4em;"><strong>'.$product['name'].(isset($product['attributes']) ? ' - '.$product['attributes'] : '').'</strong></td>
								<td style="padding: 0.6em 0.4em; text-align: right;">'.Tools::displayPrice(Product::getTaxCalculationMethod() == PS_TAX_EXC ? Tools::ps_round($price, 2) : $price_wt, $currency, false).'</td>
								<td style="padding: 0.6em 0.4em; text-align: center;">'.((int)$product['cart_quantity'] - $customization_quantity).'</td>
								<td style="padding: 0.6em 0.4em; text-align: right;">'.Tools::displayPrice(((int)$product['cart_quantity'] - $customization_quantity) * (Product::getTaxCalculationMethod() == PS_TAX_EXC ? Tools::ps_round($price, 2) : $price_wt), $currency, false).'</td>
							</tr>';

						// Check if is not a virutal product for the displaying of shipping
						if (!$product['is_virtual'])
							$virtual_product &= false;

					} // end foreach ($products)

					$cart_rules_list = '';
					$cart_rules = $cart->getCartRules();
					$total_cart_rules = array('tax_incl' => 0, 'tax_excl' => 0);
					foreach ($cart_rules as $cart_rule)
					{
						$values = array('tax_incl' => 0, 'tax_excl' => 0);

						// If the cart is split in multiple orders, the cart rule must be split too
						if (count($order_list) > 1)
						{
							// If the cart rule is a fee gift, then add the free gift value only if the gift is in this order
							if ((int)$cart_rule['obj']->gift_product && !$only_one_gift)
							{
								$in_order = (bool)Db::getInstance()->getValue('
								SELECT product_id
								FROM '._DB_PREFIX_.'order_detail
								WHERE product_id = '.(int)$cart_rule['obj']->gift_product.'
								AND product_attribute_id = '.(int)$cart_rule['obj']->gift_product_attribute.'
								AND id_order = '.(int)$order->id);

								if ($in_order)
								{
									$values['tax_incl'] += $cart_rule['obj']->getContextualValue(true, null, CartRule::FILTER_ACTION_GIFT);
									$values['tax_excl'] += $cart_rule['obj']->getContextualValue(false, null, CartRule::FILTER_ACTION_GIFT);
									$only_one_gift = true;
								}
							}

							// If the cart rule offers free shipping, add the shipping cost
							if ($cart_rule['obj']->free_shipping)
							{
								$values['tax_incl'] += $order->total_shipping_tax_incl;
								$values['tax_excl'] += $order->total_shipping_tax_excl;
							}

							// If the cart rule offers a reduction, the amount is prorated
							if ($cart_rule['obj']->reduction_amount || $cart_rule['obj']->reduction_percent)
							{
								$prorata = $order->total_paid_tax_incl / $cart_total_paid;
								$values['tax_incl'] += Tools::ps_round($prorata * $cart_rule['obj']->getContextualValue(true, null, CartRule::FILTER_ACTION_REDUCTION), 2);
								$values['tax_excl'] += Tools::ps_round($prorata * $cart_rule['obj']->getContextualValue(false, null, CartRule::FILTER_ACTION_REDUCTION), 2);
							}
						}
						else
						{
							$values = array(
								'tax_incl' => $cart_rule['obj']->getContextualValue(true),
								'tax_excl' => $cart_rule['obj']->getContextualValue(false)
							);
						}

						// If the reduction is not applicable to this order (in a multi-shipping case), then try the next
						if (!$values['tax_excl'])
							continue;

						$total_cart_rules['tax_incl'] += $values['tax_incl'];
						$total_cart_rules['tax_excl'] += $values['tax_excl'];


						/* IF
						** - This is not multi-shipping
						** - The value of the voucher is greater than the total of the order
						** - Partial use is allowed
						** - This is an "amount" reduction, not a reduction in % or a gift
						** THEN
						** The voucher is cloned with a new value corresponding to the remainder
						*/
						if (count($order_list) == 1 && $values['tax_incl'] > $order->total_products_wt && $cart_rule['obj']->partial_use == 1 && $cart_rule['obj']->reduction_amount > 0)
						{
							// Create a new voucher from the original
							$voucher = new CartRule($cart_rule['obj']->id); // We need to instantiate the CartRule without lang parameter to allow saving it
							unset($voucher->id);

							// Set a new voucher code
							$voucher->code = empty($voucher->code) ? substr(md5($order->id.'-'.$order->id_customer.'-'.$cart_rule['obj']->id), 0, 16) : $voucher->code.'-2';
							if (preg_match('/\-([0-9]{1,2})\-([0-9]{1,2})$/', $voucher->code, $matches) && $matches[1] == $matches[2])
								$voucher->code = preg_replace('/'.$matches[0].'$/', '-'.(intval($matches[1]) + 1), $voucher->code);

							// Set the new voucher value
							if ($voucher->reduction_tax)
								$voucher->reduction_amount = $values['tax_incl'] - $order->total_products_wt;
							else
								$voucher->reduction_amount = $values['tax_excl'] - $order->total_products;

							$voucher->id_customer = $order->id_customer;
							$voucher->quantity = 1;
							if ($voucher->add())
							{
								// If the voucher has conditions, they are now copied to the new voucher
								CartRule::copyConditions($cart_rule['obj']->id, $voucher->id);

								$params = array(
									'{voucher_amount}' => Tools::displayPrice($voucher->reduction_amount, $currency, false),
									'{voucher_num}' => $voucher->code,
									'{firstname}' => $customer->firstname,
									'{lastname}' => $customer->lastname,
									'{id_order}' => $order->reference
								);
								Mail::Send((int)$order->id_lang, 'voucher', Mail::l('New voucher regarding your order ', (int)$order->id_lang).$order->reference, $params, $customer->email, $customer->firstname.' '.$customer->lastname);
							}
						}

						$order->addCartRule($cart_rule['obj']->id, $cart_rule['obj']->name, $values);

						$order->total_discounts = $order->total_discounts_tax_incl = round($total_cart_rules['tax_incl'], 9);
						$order->total_discounts_tax_excl = round($total_cart_rules['tax_excl'], 9);
						$order->update();

						if ($id_order_state != Configuration::get('PS_OS_ERROR') && $id_order_state != Configuration::get('PS_OS_CANCELED') && !in_array($cart_rule['obj']->id, $cart_rule_used))
						{
							$cart_rule_used[] = $cart_rule['obj']->id;

							// Create a new instance of Cart Rule without id_lang, in order to update its quantity
							$cart_rule_to_update = new CartRule($cart_rule['obj']->id);
							$cart_rule_to_update->quantity = max(0, $cart_rule_to_update->quantity - 1);
							$cart_rule_to_update->update();
						}

						$cart_rules_list .= '
						<tr style="background-color:#EBECEE;">
							<td colspan="4" style="padding:0.6em 0.4em;text-align:right">'.$this->l('Voucher name:').' '.$cart_rule['obj']->name.'</td>
							<td style="padding:0.6em 0.4em;text-align:right">'.($values['tax_incl'] != 0.00 ? '-' : '').Tools::displayPrice($values['tax_incl'], $currency, false).'</td>
						</tr>';
					}

					// Specify order id for message
					$old_message = Message::getMessageByCartId((int)$cart->id);
					if ($old_message)
					{
						$message = new Message((int)$old_message['id_message']);
						$message->id_order = (int)$order->id;
						$message->update();
					}

					// Hook validate order
					Hook::exec('actionValidateOrder', array(
						'cart' => $cart,
						'order' => $order,
						'customer' => $customer,
						'currency' => $currency,
						'orderStatus' => $order_status
					));

					foreach ($cart->getProducts() as $product)
						if ($order_status->logable)
							ProductSale::addProductSale((int)$product['id_product'], (int)$product['cart_quantity']);

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
					$new_history->addWithemail(true, $extra_vars);

					unset($order_detail);

					// Order is reloaded because the status just changed
					$order = new Order($order->id);

					// Send an e-mail to customer (one order = one email)
					if ($id_order_state != Configuration::get('PS_OS_ERROR') && $id_order_state != Configuration::get('PS_OS_CANCELED') && $customer->id)
					{
						$invoice = new Address($order->id_address_invoice);
						$delivery = new Address($order->id_address_delivery);
						$delivery_state = $delivery->id_state ? new State($delivery->id_state) : false;
						$invoice_state = $invoice->id_state ? new State($invoice->id_state) : false;

						$data = array(
						'{firstname}' => $customer->firstname,
						'{lastname}' => $customer->lastname,
						'{email}' => $customer->email,
						'{delivery_block_txt}' => $this->_getFormatedAddress($delivery, "\n"),
						'{invoice_block_txt}' => $this->_getFormatedAddress($invoice, "\n"),
						'{delivery_block_html}' => $this->_getFormatedAddress($delivery, '<br />', array(
							'firstname'	=> '<span style="color:#DB3484; font-weight:bold;">%s</span>',
							'lastname'	=> '<span style="color:#DB3484; font-weight:bold;">%s</span>'
						)),
						'{invoice_block_html}' => $this->_getFormatedAddress($invoice, '<br />', array(
								'firstname'	=> '<span style="color:#DB3484; font-weight:bold;">%s</span>',
								'lastname'	=> '<span style="color:#DB3484; font-weight:bold;">%s</span>'
						)),
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
						'{order_name}' => sprintf('#%06d', (int)$order->id),
						'{date}' => Tools::displayDate(date('Y-m-d H:i:s'), (int)$order->id_lang, 1),
						'{carrier}' => $virtual_product ? $this->l('No carrier') : $carrier->name,
						'{payment}' => Tools::substr($order->payment, 0, 32),
						'{products}' => $products_list,
						'{discounts}' => $cart_rules_list,
						'{total_paid}' => Tools::displayPrice($order->total_paid, $currency, false),
						'{total_products}' => Tools::displayPrice($order->total_paid - $order->total_shipping - $order->total_wrapping + $order->total_discounts, $currency, false),
						'{total_discounts}' => Tools::displayPrice($order->total_discounts, $currency, false),
						'{total_shipping}' => Tools::displayPrice($order->total_shipping, $currency, false),
						'{total_wrapping}' => Tools::displayPrice($order->total_wrapping, $currency, false));

						if (is_array($extra_vars))
							$data = array_merge($data, $extra_vars);

						// Join PDF invoice
						if ((int)Configuration::get('PS_INVOICE') && $order_status->invoice && $order->invoice_number)
						{
							$pdf = new PDF($order->getInvoicesCollection(), PDF::TEMPLATE_INVOICE, $this->context->smarty);
							$file_attachement['content'] = $pdf->render(false);
							$file_attachement['name'] = Configuration::get('PS_INVOICE_PREFIX', (int)$order->id_lang).sprintf('%06d', $order->invoice_number).'.pdf';
							$file_attachement['mime'] = 'application/pdf';
						}
						else
							$file_attachement = null;

						if (Validate::isEmail($customer->email))
							Mail::Send(
								(int)$order->id_lang,
								'order_conf',
								Mail::l('Order confirmation', (int)$order->id_lang),
								$data,
								$customer->email,
								$customer->firstname.' '.$customer->lastname,
								null,
								null,
								$file_attachement
							);
					}

					// updates stock in shops
					if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'))
					{
						$product_list = $order->getProducts();
						foreach ($product_list as $product)
						{
							// if the available quantities depends on the physical stock
							if (StockAvailable::dependsOnStock($product['product_id']))
							{
								// synchronizes
								StockAvailable::synchronize($product['product_id']);
							}
						}
					}
				}
				else
				{
					$error = Tools::displayError('Order creation failed');
					Logger::addLog($error, 4, '0000002', 'Cart', intval($order->id_cart));
					die($error);
				}
			}
			// Use the last order as currentOrder
			$this->currentOrder = (int)$order->id;
			return true;
		}
		else
		{
			$error = Tools::displayError('Cart cannot be loaded or an order has already been placed using this cart');
			Logger::addLog($error, 4, '0000001', 'Cart', intval($cart->id));
			die($error);
		}
	}

	/**
	 * @param Object Address $the_address that needs to be txt formated
	 * @return String the txt formated address block
	 */
	protected function _getTxtFormatedAddress($the_address)
	{
		$adr_fields = AddressFormat::getOrderedAddressFields($the_address->id_country, false, true);
		$r_values = array();
		foreach ($adr_fields as $fields_line)
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

	protected function _getFormatedAddress(Address $the_address, $line_sep, $fields_style = array())
	{
		return AddressFormat::generateAddress($the_address, array('avoid' => array()), $line_sep, ' ', $fields_style);
	}

	/**
	 * @param int $id_currency : this parameter is optionnal but on 1.5 version of Prestashop, it will be REQUIRED
	 * @return Currency
	 */
	public function getCurrency($current_id_currency = null)
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
				$id_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
			else
				$id_currency = $currency;
		}
		if (!isset($id_currency) || empty($id_currency))
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
		$hook_payment = 'Payment';
		if (Db::getInstance()->getValue('SELECT `id_hook` FROM `'._DB_PREFIX_.'hook` WHERE `name` = \'displayPayment\''))
			$hook_payment = 'displayPayment';

		return Db::getInstance()->executeS('
			SELECT DISTINCT m.`id_module`, h.`id_hook`, m.`name`, hm.`position`
			FROM `'._DB_PREFIX_.'module` m
			LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
			LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
			WHERE h.`name` = \''.pSQL($hook_payment).'\'
			AND m.`active` = 1
		');
	}


	public static function preCall($module_name)
	{
		if (!parent::preCall($module_name))
			return false;

		if (($module_instance = Module::getInstanceByName($module_name)))
			if (!$module_instance->currencies || ($module_instance->currencies && count(Currency::checkPaymentCurrencies($module_instance->id))))
				return true;

		return false;
	}

}

