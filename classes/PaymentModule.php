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

include_once(dirname(__FILE__).'/../config/config.inc.php');

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
			if (!Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'module_currency` (id_module, id_currency)
			SELECT '.(int)($this->id).', id_currency FROM `'._DB_PREFIX_.'currency` WHERE deleted = 0'))
				return false;
		}
		elseif ($this->currencies_mode == 'radio')
		{
			if (!Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'module_currency` (id_module, id_currency)
			VALUES ('.(int)($this->id).', -2)'))
				return false;
		}
		else
			Tools::displayError('No currency mode for payment module');

		// Insert countries availability
		$return = Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'module_country` (id_module, id_country)
		SELECT '.(int)($this->id).', id_country FROM `'._DB_PREFIX_.'country` WHERE active = 1');
		// Insert group availability
		$return &= Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'module_group` (id_module, id_group)
		SELECT '.(int)($this->id).', id_group FROM `'._DB_PREFIX_.'group`');

		return $return;
	}

	public function uninstall()
	{
		if (!Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'module_country` WHERE id_module = '.(int)($this->id))
			OR !Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'module_currency` WHERE id_module = '.(int)($this->id))
			OR !Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'module_group` WHERE id_module = '.(int)($this->id)))
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
	public function validateOrder($id_cart, $id_order_state, $amountPaid, $paymentMethod = 'Unknown', $message = NULL, $extraVars = array(), $currency_special = NULL, $dont_touch_amount = false, $secure_key = false, $id_group_shop = false, $id_shop = false)
	{
		global $cart;
		
		$cart = new Cart((int)($id_cart));
		// Does order already exists ?
		if (Validate::isLoadedObject($cart) AND $cart->OrderExists() == false)
		{
			if ($secure_key !== false AND $secure_key != $cart->secure_key)
				die(Tools::displayError());

			// Copying data from cart
			$order = new Order();
			$order->id_carrier = (int)($cart->id_carrier);
			$order->id_customer = (int)($cart->id_customer);
			$order->id_address_invoice = (int)($cart->id_address_invoice);
			$order->id_address_delivery = (int)($cart->id_address_delivery);
			$vat_address = new Address((int)($order->id_address_delivery));
			$order->id_currency = ($currency_special ? (int)($currency_special) : (int)($cart->id_currency));
			$order->id_lang = (int)($cart->id_lang);
			$order->id_cart = (int)($cart->id);
			
			$order->id_group_shop = (int)($id_group_shop ? $id_group_shop : $cart->id_group_shop);
			$order->id_shop = (int)($id_shop ? $id_shop : $cart->id_shop);
			
			$customer = new Customer((int)($order->id_customer));
			$order->secure_key = ($secure_key ? pSQL($secure_key) : pSQL($customer->secure_key));
			$order->payment = $paymentMethod;
			if (isset($this->name))
				$order->module = $this->name;
			$order->recyclable = $cart->recyclable;
			$order->gift = (int)($cart->gift);
			$order->gift_message = $cart->gift_message;
			$currency = new Currency($order->id_currency);
			$order->conversion_rate = $currency->conversion_rate;
			$amountPaid = !$dont_touch_amount ? Tools::ps_round((float)($amountPaid), 2) : $amountPaid;
			$order->total_paid_real = $amountPaid;
			$order->total_products = (float)($cart->getOrderTotal(false, Cart::ONLY_PRODUCTS));
			$order->total_products_wt = (float)($cart->getOrderTotal(true, Cart::ONLY_PRODUCTS));
			$order->total_discounts = (float)(abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS)));
			$order->total_shipping = (float)($cart->getOrderShippingCost());
			$order->carrier_tax_rate = (float)Tax::getCarrierTaxRate($cart->id_carrier, (int)$cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
			$order->total_wrapping = (float)(abs($cart->getOrderTotal(true, Cart::ONLY_WRAPPING)));
			$order->total_paid = (float)(Tools::ps_round((float)($cart->getOrderTotal(true, Cart::BOTH)), 2));
			$order->invoice_date = '0000-00-00 00:00:00';
			$order->delivery_date = '0000-00-00 00:00:00';
			// Amount paid by customer is not the right one -> Status = payment error
			// We don't use the following condition to avoid the float precision issues : http://www.php.net/manual/en/language.types.float.php
			// if ($order->total_paid != $order->total_paid_real)
			// We use number_format in order to compare two string
			if (number_format($order->total_paid, 2) != number_format($order->total_paid_real, 2))
				$id_order_state = _PS_OS_ERROR_;
			// Creating order
			if ($cart->OrderExists() == false)
				$result = $order->add();
			else
			{
				$errorMessage = Tools::displayError('An order has already been placed using this cart.');
				Logger::addLog($errorMessage, 4, '0000001', 'Cart', intval($order->id_cart));
				die($errorMessage);
			}

			// Next !
			if ($result AND isset($order->id))
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

				// Insert products from cart into order_detail table
				$products = $cart->getProducts();
				$productsList = '';
				$db = Db::getInstance();
				$query = 'INSERT INTO `'._DB_PREFIX_.'order_detail`
					(`id_order`, `product_id`, `product_attribute_id`, `product_name`, `product_quantity`, `product_quantity_in_stock`, `product_price`, `reduction_percent`, `reduction_amount`, `group_reduction`, `product_quantity_discount`, `product_ean13`, `product_upc`, `product_reference`, `product_supplier_reference`, `product_weight`, `tax_name`, `tax_rate`, `ecotax`, `ecotax_tax_rate`, `discount_quantity_applied`, `download_deadline`, `download_hash`)
				VALUES ';

				$customizedDatas = Product::getAllCustomizedDatas((int)($order->id_cart));
				Product::addCustomizationPrice($products, $customizedDatas);
				$outOfStock = false;
				foreach ($products AS $key => $product)
				{
					$productQuantity = (int)(Product::getQuantity((int)($product['id_product']), ($product['id_product_attribute'] ? (int)($product['id_product_attribute']) : NULL)));
					$quantityInStock = ($productQuantity - (int)($product['cart_quantity']) < 0) ? $productQuantity : (int)($product['cart_quantity']);
					if ($id_order_state != _PS_OS_CANCELED_ AND $id_order_state != _PS_OS_ERROR_)
                                        {
						if (Product::updateQuantity($product, (int)$order->id))
							$product['stock_quantity'] -= $product['cart_quantity'];
						if ($product['stock_quantity'] < 0 && Configuration::get('PS_STOCK_MANAGEMENT'))
							$outOfStock = true;

						Product::updateDefaultAttribute($product['id_product']);
					}
					$price = Product::getPriceStatic((int)($product['id_product']), false, ($product['id_product_attribute'] ? (int)($product['id_product_attribute']) : NULL), 6, NULL, false, true, $product['cart_quantity'], false, (int)($order->id_customer), (int)($order->id_cart), (int)($order->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
					$price_wt = Product::getPriceStatic((int)($product['id_product']), true, ($product['id_product_attribute'] ? (int)($product['id_product_attribute']) : NULL), 2, NULL, false, true, $product['cart_quantity'], false, (int)($order->id_customer), (int)($order->id_cart), (int)($order->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
					// Add some informations for virtual products
					$deadline = '0000-00-00 00:00:00';
					$download_hash = NULL;
					if ($id_product_download = ProductDownload::getIdFromIdProduct((int)($product['id_product'])))
					{
						$productDownload = new ProductDownload((int)($id_product_download));
						$deadline = $productDownload->getDeadLine();
						$download_hash = $productDownload->getHash();
					}

					// Exclude VAT
					if (Tax::excludeTaxeOption())
					{
						$product['tax'] = 0;
						$product['rate'] = 0;
						$tax_rate = 0;
					}
					else
						$tax_rate = Tax::getProductTaxRate((int)($product['id_product']), $cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});

                    $ecotaxTaxRate = 0;
                    if (!empty($product['ecotax']))
                        $ecotaxTaxRate = Tax::getProductEcotaxRate($order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});

					$quantityDiscount = SpecificPrice::getQuantityDiscount((int)$product['id_product'], Shop::getCurrentShop(), (int)$cart->id_currency, (int)$vat_address->id_country, (int)$customer->id_default_group, (int)$product['cart_quantity']);
					$unitPrice = Product::getPriceStatic((int)$product['id_product'], true, ($product['id_product_attribute'] ? intval($product['id_product_attribute']) : NULL), 2, NULL, false, true, 1, false, (int)$order->id_customer, NULL, (int)$order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
					$quantityDiscountValue = $quantityDiscount ? ((Product::getTaxCalculationMethod((int)$order->id_customer) == PS_TAX_EXC ? Tools::ps_round($unitPrice, 2) : $unitPrice) - $quantityDiscount['price'] * (1 + $tax_rate / 100)) : 0.00;
					$query .= '('.(int)($order->id).',
						'.(int)($product['id_product']).',
						'.(isset($product['id_product_attribute']) ? (int)($product['id_product_attribute']) : 'NULL').',
						\''.pSQL($product['name'].((isset($product['attributes']) AND $product['attributes'] != NULL) ? ' - '.$product['attributes'] : '')).'\',
						'.(int)($product['cart_quantity']).',
						'.$quantityInStock.',
						'.(float)(Product::getPriceStatic((int)($product['id_product']), false, ($product['id_product_attribute'] ? (int)($product['id_product_attribute']) : NULL), (Product::getTaxCalculationMethod((int)($order->id_customer)) == PS_TAX_EXC ? 2 : 6), NULL, false, false, $product['cart_quantity'], false, (int)($order->id_customer), (int)($order->id_cart), (int)($order->{Configuration::get('PS_TAX_ADDRESS_TYPE')}), $specificPrice, FALSE)).',
						'.(float)(($specificPrice AND $specificPrice['reduction_type'] == 'percentage') ? $specificPrice['reduction'] * 100 : 0.00).',
						'.(float)(($specificPrice AND $specificPrice['reduction_type'] == 'amount') ? (!$specificPrice['id_currency'] ? Tools::convertPrice($specificPrice['reduction'], $order->id_currency) : $specificPrice['reduction']) : 0.00).',
						'.(float)(Group::getReduction((int)($order->id_customer))).',
						'.$quantityDiscountValue.',
						'.(empty($product['ean13']) ? 'NULL' : '\''.pSQL($product['ean13']).'\'').',
						'.(empty($product['upc']) ? 'NULL' : '\''.pSQL($product['upc']).'\'').',
						'.(empty($product['reference']) ? 'NULL' : '\''.pSQL($product['reference']).'\'').',
						'.(empty($product['supplier_reference']) ? 'NULL' : '\''.pSQL($product['supplier_reference']).'\'').',
						'.(float)($product['id_product_attribute'] ? $product['weight_attribute'] : $product['weight']).',
						\''.(empty($tax_rate) ? '' : pSQL($product['tax'])).'\',
						'.(float)($tax_rate).',
						'.(float)Tools::convertPrice(floatval($product['ecotax']), intval($order->id_currency)).',
						'.(float)$ecotaxTaxRate.',
						'.(($specificPrice AND $specificPrice['from_quantity'] > 1) ? 1 : 0).',
						\''.pSQL($deadline).'\',
						\''.pSQL($download_hash).'\'),';

					$customizationQuantity = 0;
					if (isset($customizedDatas[$product['id_product']][$product['id_product_attribute']]))
					{
						$customizationText = '';
						foreach ($customizedDatas[$product['id_product']][$product['id_product_attribute']] AS $customization)
						{
							if (isset($customization['datas'][_CUSTOMIZE_TEXTFIELD_]))
								foreach ($customization['datas'][_CUSTOMIZE_TEXTFIELD_] AS $text)
									$customizationText .= $text['name'].':'.' '.$text['value'].'<br />';
							
							if (isset($customization['datas'][_CUSTOMIZE_FILE_]))
								$customizationText .= sizeof($customization['datas'][_CUSTOMIZE_FILE_]) .' '. Tools::displayError('image(s)').'<br />';
								
							$customizationText .= '---<br />';							
						}
						
						$customizationText = rtrim($customizationText, '---<br />');
						
						$customizationQuantity = (int)($product['customizationQuantityTotal']);
						$productsList .=
						'<tr style="background-color: '.($key % 2 ? '#DDE2E6' : '#EBECEE').';">
							<td style="padding: 0.6em 0.4em;">'.$product['reference'].'</td>
							<td style="padding: 0.6em 0.4em;"><strong>'.$product['name'].(isset($product['attributes_small']) ? ' '.$product['attributes_small'] : '').' - '.$this->l('Customized').(!empty($customizationText) ? ' - '.$customizationText : '').'</strong></td>
							<td style="padding: 0.6em 0.4em; text-align: right;">'.Tools::displayPrice(Product::getTaxCalculationMethod() == PS_TAX_EXC ? $price : $price_wt, $currency, false).'</td>
							<td style="padding: 0.6em 0.4em; text-align: center;">'.$customizationQuantity.'</td>
							<td style="padding: 0.6em 0.4em; text-align: right;">'.Tools::displayPrice($customizationQuantity * (Product::getTaxCalculationMethod() == PS_TAX_EXC ? $price : $price_wt), $currency, false).'</td>
						</tr>';
					}

					if (!$customizationQuantity OR (int)$product['cart_quantity'] > $customizationQuantity)
						$productsList .=
						'<tr style="background-color: '.($key % 2 ? '#DDE2E6' : '#EBECEE').';">
							<td style="padding: 0.6em 0.4em;">'.$product['reference'].'</td>
							<td style="padding: 0.6em 0.4em;"><strong>'.$product['name'].(isset($product['attributes_small']) ? ' '.$product['attributes_small'] : '').'</strong></td>
							<td style="padding: 0.6em 0.4em; text-align: right;">'.Tools::displayPrice(Product::getTaxCalculationMethod() == PS_TAX_EXC ? $price : $price_wt, $currency, false).'</td>
							<td style="padding: 0.6em 0.4em; text-align: center;">'.((int)($product['cart_quantity']) - $customizationQuantity).'</td>
							<td style="padding: 0.6em 0.4em; text-align: right;">'.Tools::displayPrice(((int)($product['cart_quantity']) - $customizationQuantity) * (Product::getTaxCalculationMethod() == PS_TAX_EXC ? $price : $price_wt), $currency, false).'</td>
						</tr>';
				} // end foreach ($products)
				$query = rtrim($query, ',');
				$result = $db->Execute($query);

				// Insert discounts from cart into order_discount table
				$discounts = $cart->getDiscounts();
				$discountsList = '';
				$total_discount_value = 0;
				$shrunk = false;
				foreach ($discounts AS $discount)
				{
					$objDiscount = new Discount((int)$discount['id_discount'], $order->id_lang);
					$value = $objDiscount->getValue(sizeof($discounts), $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS), $order->total_shipping, $cart->id);
					if ($objDiscount->id_discount_type == 2 AND in_array($objDiscount->behavior_not_exhausted, array(1,2)))
						$shrunk = true;

					if ($shrunk AND ($total_discount_value + $value) > ($order->total_products + $order->total_shipping + $order->total_wrapping))
					{
						$amount_to_add = ($order->total_products + $order->total_shipping + $order->total_wrapping) - $total_discount_value;
						if ($objDiscount->id_discount_type == 2 AND $objDiscount->behavior_not_exhausted == 2)
						{
							$voucher = new Discount();
							foreach ($objDiscount AS $key => $discountValue)
								$voucher->$key = $discountValue;
							$voucher->name = 'VSRK'.(int)$order->id_customer.'O'.(int)$order->id;
							$voucher->value = (float)$value - $amount_to_add;
							$voucher->add();
							$params['{voucher_amount}'] = Tools::displayPrice($voucher->value, $currency, false);
							$params['{voucher_num}'] = $voucher->name;
							@Mail::Send((int)$order->id_lang, 'voucher', Mail::l('New voucher regarding your order #').$order->id, $params, $customer->email, $customer->firstname.' '.$customer->lastname);
						}
					}
					else
						$amount_to_add = $value;
					$order->addDiscount($objDiscount->id, $objDiscount->name, $amount_to_add);
					$total_discount_value += $amount_to_add;
					if ($id_order_state != _PS_OS_ERROR_ AND $id_order_state != _PS_OS_CANCELED_)
						$objDiscount->quantity = $objDiscount->quantity - 1;
					$objDiscount->update();

					$discountsList .=
					'<tr style="background-color:#EBECEE;">
							<td colspan="4" style="padding: 0.6em 0.4em; text-align: right;">'.$this->l('Voucher code:').' '.$objDiscount->name.'</td>
							<td style="padding: 0.6em 0.4em; text-align: right;">'.($value != 0.00 ? '-' : '').Tools::displayPrice($value, $currency, false).'</td>
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

				// Hook new order
				$orderStatus = new OrderState((int)$id_order_state, (int)$order->id_lang);
				if (Validate::isLoadedObject($orderStatus))
				{
					Hook::newOrder($cart, $order, $customer, $currency, $orderStatus);
					foreach ($cart->getProducts() AS $product)
						if ($orderStatus->logable)
							ProductSale::addProductSale((int)$product['id_product'], (int)$product['cart_quantity']);
				}

				if (isset($outOfStock) AND $outOfStock)
				{
					$history = new OrderHistory();
					$history->id_order = (int)$order->id;
					$history->changeIdOrderState(_PS_OS_OUTOFSTOCK_, (int)$order->id);
					$history->addWithemail();
				}

				// Set order state in order history ONLY even if the "out of stock" status has not been yet reached
				// So you migth have two order states
				$new_history = new OrderHistory();
				$new_history->id_order = (int)$order->id;
				$new_history->changeIdOrderState((int)$id_order_state, (int)$order->id);
				$new_history->addWithemail(true, $extraVars);

				// Order is reloaded because the status just changed
				$order = new Order($order->id);

				// Send an e-mail to customer
				if ($id_order_state != _PS_OS_ERROR_ AND $id_order_state != _PS_OS_CANCELED_ AND $customer->id)
				{
					$invoice = new Address((int)($order->id_address_invoice));
					$delivery = new Address((int)($order->id_address_delivery));
					$carrier = new Carrier((int)($order->id_carrier), $order->id_lang);
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
					'{discounts}' => $discountsList,
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
						Mail::Send((int)($order->id_lang), 'order_conf', Mail::l('Order confirmation'), $data, $customer->email, $customer->firstname.' '.$customer->lastname, NULL, NULL, $fileAttachment);
				}
				$this->currentOrder = (int)($order->id);
				return true;
			}
			else
			{
				$errorMessage = Tools::displayError('Order creation failed');
				Logger::addLog($errorMessage, 4, '0000002', 'Cart', intval($order->id_cart));
				die($errorMessage);
			}
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
		$adr_fields = AddressFormat::getOrderedAddressFields($the_address->id_country);
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
			global $cookie;

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
			{
				// not use $cookie if $current_id_currency is set
				if ((int)$current_id_currency)
					$id_currency = (int)$current_id_currency;
				else
					$id_currency = (int)($cookie->id_currency);
			}
			elseif ($currency == -2)
				$id_currency = (int)(Configuration::get('PS_CURRENCY_DEFAULT'));
			else
				$id_currency = $currency;
		}
		if (!isset($id_currency) OR empty($id_currency))
			return false;
		return (new Currency($id_currency));
	}
}

