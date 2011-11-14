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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminOrdersControllerCore extends AdminController
{
	public function __construct()
	{
		$this->table = 'order';
	 	$this->className = 'Order';
	 	$this->lang = false;
		$this->edit = true;
	 	$this->addRowAction('view');

	 	$this->deleted = false;
	 	$this->colorOnBackground = true;
	 	$this->requiredDatabase = false;
	 	$this->context = Context::getContext();

	 	$this->_select = '
			a.id_order AS id_pdf,
			CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
			osl.`name` AS `osname`,
			os.`color`,
			IF((SELECT COUNT(so.id_order) FROM `'._DB_PREFIX_.'orders` so WHERE so.id_customer = a.id_customer) > 1, 0, 1) as new,
			(SELECT COUNT(od.`id_order`) FROM `'._DB_PREFIX_.'order_detail` od WHERE od.`id_order` = a.`id_order` GROUP BY `id_order`) AS product_number';
	 	$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)
	 	LEFT JOIN `'._DB_PREFIX_.'order_history` oh ON (oh.`id_order` = a.`id_order`)
		LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = oh.`id_order_state`)
		LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)$this->context->language->id.')';
		$this->_where = 'AND oh.`id_order_history` = (SELECT MAX(`id_order_history`) FROM `'._DB_PREFIX_.'order_history` moh WHERE moh.`id_order` = a.`id_order` GROUP BY moh.`id_order`)';
		$this->_orderWay = 'DESC';

		$statesArray = array();
		$states = OrderState::getOrderStates((int)$this->context->language->id);

		foreach ($states AS $state)
			$statesArray[$state['id_order_state']] = $state['name'];

		$this->fieldsDisplay = array(
		'id_order' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'new' => array('title' => $this->l('New'), 'width' => 25, 'align' => 'center', 'type' => 'bool', 'filter_key' => 'new', 'tmpTableFilter' => true, 'icon' => array(0 => 'blank.gif', 1 => 'news-new.gif'), 'orderby' => false),
		'customer' => array('title' => $this->l('Customer'), 'filter_key' => 'customer', 'tmpTableFilter' => true),
		'total_paid' => array('title' => $this->l('Total'), 'width' => 70, 'align' => 'right', 'prefix' => '<b>', 'suffix' => '</b>', 'type' => 'price', 'currency' => true),
		'payment' => array('title' => $this->l('Payment'), 'width' => 100),
		'osname' => array('title' => $this->l('Status'), 'width' => 230, 'type' => 'select', 'list' => $statesArray, 'filter_key' => 'os!id_order_state', 'filter_type' => 'int', 'width' => 200),
		'date_add' => array('title' => $this->l('Date'), 'width' => 35, 'align' => 'right', 'type' => 'datetime', 'filter_key' => 'a!date_add'),
		'id_pdf' => array('title' => $this->l('PDF'), 'width' => 35, 'align' => 'center', 'callback' => 'printPDFIcons', 'orderby' => false, 'search' => false));

 		$this->shopLinkType = 'shop';
 		$this->shopShareDatas = Shop::SHARE_ORDER;

		parent::__construct();
	}

	public function initForm()
	{
		parent::initForm();
		$this->addJqueryPlugin(array('autocomplete', 'fancybox', 'typewatch'));
		$cart = new Cart((int)Tools::getValue('id_cart'));
		$this->context->smarty->assign(array('recyclable_pack' => (int)Configuration::get('PS_RECYCLABLE_PACK'),
														'gift_wrapping' => (int)Configuration::get('PS_GIFT_WRAPPING'),
														'cart' => $cart,
														'currencies' => Currency::getCurrencies(),
														'langs' => Language::getLanguages(true, Context::getContext()->shop->id),
														'payment_modules' => PaymentModule::getInstalledPaymentModules(),
														'order_states' => OrderState::getOrderStates((int)Context::getContext()->cookie->id_lang)));
		$this->content .= $this->context->smarty->fetch('orders/form.tpl');
	}

	public function printPDFIcons($id_order, $tr)
	{
		$order = new Order($id_order);
		$orderState = OrderHistory::getLastOrderState($id_order);
		if (!Validate::isLoadedObject($orderState) OR !Validate::isLoadedObject($order))
			die(Tools::displayError('Invalid objects'));

		// Generate HTML code for printing Invoice Icon with link
		$content = '<span style="width:20px; margin-right:5px;">';
		if (($orderState->invoice && $order->invoice_number) && (int)$tr['product_number'])
			$content .= '<a href="pdf.php?id_order='.(int)$order->id.'&pdf"><img src="../img/admin/tab-invoice.gif" alt="invoice" /></a>';
		else
			$content .= '-';
		$content .= '</span>';

		// Generate HTML code for printing Delivery Icon with link
		$content .= '<span style="width:20px;">';
		if ($orderState->delivery && $order->delivery_number)
			$content .= '<a href="pdf.php?id_delivery='.(int)$order->delivery_number.'"><img src="../img/admin/delivery.gif" alt="delivery" /></a>';
		else
			$content .= '-';
		$content .= '</span>';

		return $content;
	}

	public function postProcess()
	{
		/* Update shipping number */
		if (Tools::isSubmit('submitShippingNumber') AND ($id_order = (int)(Tools::getValue('id_order'))) AND Validate::isLoadedObject($order = new Order($id_order)))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (!$order->hasBeenShipped())
					throw new PrestashopException('The shipping number can only be set once the order has been shipped.');
				$_GET['view'.$this->table] = true;

				$shipping_number = pSQL(Tools::getValue('shipping_number'));
				$order->shipping_number = $shipping_number;
				$order->update();
				if ($shipping_number)
				{
					global $_LANGMAIL;
					$customer = new Customer((int)($order->id_customer));
					$carrier = new Carrier((int)($order->id_carrier));
					if (!Validate::isLoadedObject($customer) OR !Validate::isLoadedObject($carrier))
						die(Tools::displayError());
					$templateVars = array(
						'{followup}' => str_replace('@', $order->shipping_number, $carrier->url),
						'{firstname}' => $customer->firstname,
						'{lastname}' => $customer->lastname,
						'{id_order}' => (int)($order->id)
					);
					@Mail::Send((int)($order->id_lang), 'in_transit', Mail::l('Package in transit'), $templateVars,
						$customer->email, $customer->firstname.' '.$customer->lastname, NULL, NULL, NULL, NULL,
						_PS_MAIL_DIR_, true);
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}

		/* Change order state, add a new entry in order history and send an e-mail to the customer if needed */
		elseif (Tools::isSubmit('submitState') AND ($id_order = (int)(Tools::getValue('id_order'))) AND Validate::isLoadedObject($order = new Order($id_order)))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				$_GET['view'.$this->table] = true;
				if (!$newOrderStatusId = (int)(Tools::getValue('id_order_state')))
					$this->_errors[] = Tools::displayError('Invalid new order status');
				else
				{
					$history = new OrderHistory();
					$history->id_order = (int)$id_order;
					$history->id_employee = (int)$this->context->employee->id;
					if (!(int)Tools::getValue('id_warehouse'))
						$this->_errors[] = Tools::displayError('An error occurred while changing the status.');
					else
					{
						$history->changeIdOrderState((int)($newOrderStatusId), (int)($id_order), (int)Tools::getValue('id_warehouse'));
						$order = new Order((int)$order->id);
						$carrier = new Carrier((int)($order->id_carrier), (int)($order->id_lang));
						$templateVars = array();
						if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') AND $order->shipping_number)
							$templateVars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));
						else if ($history->id_order_state == Configuration::get('PS_OS_CHEQUE'))
							$templateVars = array(
								'{cheque_name}' => (Configuration::get('CHEQUE_NAME') ? Configuration::get('CHEQUE_NAME') : ''),
								'{cheque_address_html}' => (Configuration::get('CHEQUE_ADDRESS') ? nl2br(Configuration::get('CHEQUE_ADDRESS')) : ''));
						elseif ($history->id_order_state == Configuration::get('PS_OS_BANKWIRE'))
							$templateVars = array(
								'{bankwire_owner}' => (Configuration::get('BANK_WIRE_OWNER') ? Configuration::get('BANK_WIRE_OWNER') : ''),
								'{bankwire_details}' => (Configuration::get('BANK_WIRE_DETAILS') ? nl2br(Configuration::get('BANK_WIRE_DETAILS')) : ''),
								'{bankwire_address}' => (Configuration::get('BANK_WIRE_ADDRESS') ? nl2br(Configuration::get('BANK_WIRE_ADDRESS')) : ''));
						if ($history->addWithemail(true, $templateVars))
							Tools::redirectAdmin(self::$currentIndex.'&id_order='.$id_order.'&vieworder'.'&token='.$this->token);
						$this->_errors[] = Tools::displayError('An error occurred while changing the status or was unable to send e-mail to the customer.');
					}
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}

		/* Add a new message for the current order and send an e-mail to the customer if needed */
		elseif (isset($_POST['submitMessage']))
		{
			$_GET['view'.$this->table] = true;
			if ($this->tabAccess['edit'] === '1')
			{
				if (!($id_order = (int)(Tools::getValue('id_order'))) OR !($id_customer = (int)(Tools::getValue('id_customer'))))
					$this->_errors[] = Tools::displayError('An error occurred before sending message');
				elseif (!Tools::getValue('message'))
					$this->_errors[] = Tools::displayError('Message cannot be blank');
				else
				{
					/* Get message rules and and check fields validity */
					$rules = call_user_func(array('Message', 'getValidationRules'), 'Message');
					foreach ($rules['required'] AS $field)
						if (($value = Tools::getValue($field)) == false AND (string)$value != '0')
							if (!Tools::getValue('id_'.$this->table) OR $field != 'passwd')
								$this->_errors[] = Tools::displayError('field').' <b>'.$field.'</b> '.Tools::displayError('is required.');
					foreach ($rules['size'] AS $field => $maxLength)
						if (Tools::getValue($field) AND Tools::strlen(Tools::getValue($field)) > $maxLength)
							$this->_errors[] = Tools::displayError('field').' <b>'.$field.'</b> '.Tools::displayError('is too long.').' ('.$maxLength.' '.Tools::displayError('chars max').')';
					foreach ($rules['validate'] AS $field => $function)
						if (Tools::getValue($field))
							if (!Validate::$function(htmlentities(Tools::getValue($field), ENT_COMPAT, 'UTF-8')))
								$this->_errors[] = Tools::displayError('field').' <b>'.$field.'</b> '.Tools::displayError('is invalid.');
					if (!sizeof($this->_errors))
					{
						$order = new Order((int)(Tools::getValue('id_order')));
						$customer = new Customer((int)$order->id_customer);
						//check if a thread already exist
						$id_customer_thread = CustomerThread::getIdCustomerThreadByEmailAndIdOrder($customer->email, $order->id);
						$cm = new CustomerMessage();
						if (!$id_customer_thread)
						{
							$ct = new CustomerThread();
							$ct->id_contact = 0;
							$ct->id_customer = (int)$order->id_customer;
							$ct->id_shop = (int)$this->context->shop->getId(true);
							$ct->id_order = (int)$order->id;
							$ct->id_lang = (int)$this->context->language->id;
							$ct->email = $customer->email;
							$ct->status = 'open';
							$ct->token = Tools::passwdGen(12);
							$ct->add();
						}
						else
							$ct = new CustomerThread((int)$id_customer_thread);
						$cm->id_customer_thread = $ct->id;
						$cm->id_employee = (int)$this->context->employee->id;
						$cm->message = htmlentities(Tools::getValue('message'), ENT_COMPAT, 'UTF-8');
						$cm->private = Tools::getValue('visibility');
						if (!$cm->add())
							$this->_errors[] = Tools::displayError('An error occurred while sending message.');
						elseif ($message->private)
							Tools::redirectAdmin($currentIndex.'&id_order='.$id_order.'&vieworder&conf=11'.'&token='.$this->token);
						elseif (Validate::isLoadedObject($customer = new Customer($id_customer)))
						{
							if (Validate::isLoadedObject($order))
							{
								$varsTpl = array(
												'{lastname}' => $customer->lastname,
												'{firstname}' => $customer->firstname,
												'{id_order}' => $order->id,
												'{message}' => (Configuration::get('PS_MAIL_TYPE') == 2 ? $cm->message : Tools::nl2br($cm->message))
												);
								if (@Mail::Send((int)($order->id_lang), 'order_merchant_comment',
									Mail::l('New message regarding your order'), $varsTpl, $customer->email,
									$customer->firstname.' '.$customer->lastname, NULL, NULL, NULL, NULL, _PS_MAIL_DIR_, true))
									Tools::redirectAdmin(self::$currentIndex.'&id_order='.$id_order.'&vieworder&conf=11'.'&token='.$this->token);
							}
						}
						$this->_errors[] = Tools::displayError('An error occurred while sending e-mail to customer.');
					}
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}

		/* Cancel product from order */
		elseif (Tools::isSubmit('cancelProduct') AND Validate::isLoadedObject($order = new Order((int)(Tools::getValue('id_order')))))
		{
		 	if ($this->tabAccess['delete'] === '1')
			{
				$productList = Tools::getValue('id_order_detail');
				$customizationList = Tools::getValue('id_customization');
				$qtyList = Tools::getValue('cancelQuantity');
				$customizationQtyList = Tools::getValue('cancelCustomizationQuantity');

				$full_product_list = $productList;
				$full_quantity_list = $qtyList;

				if ($customizationList)
				{
					foreach ($customizationList as $key => $id_order_detail)
					{
						$full_product_list[$id_order_detail] = $id_order_detail;
						$full_quantity_list[$id_order_detail] = $customizationQtyList[$key];
					}
				}

				if ($productList OR $customizationList)
				{
					if ($productList)
					{
						$id_cart = Cart::getCartIdByOrderId($order->id);
						$customization_quantities = Customization::countQuantityByCart($id_cart);

						foreach ($productList AS $key => $id_order_detail)
						{
							$qtyCancelProduct = abs($qtyList[$key]);
							if (!$qtyCancelProduct)
								$this->_errors[] = Tools::displayError('No quantity selected for product.');

							// check actionable quantity
							$order_detail = new OrderDetail($id_order_detail);
							$customization_quantity = 0;
							if (array_key_exists($order_detail->product_id, $customization_quantities) && array_key_exists($order_detail->product_attribute_id, $customization_quantities[$order_detail->product_id]))
								$customization_quantity =  (int) $customization_quantities[$order_detail->product_id][$order_detail->product_attribute_id];

							if (($order_detail->product_quantity - $customization_quantity - $order_detail->product_quantity_refunded - $order_detail->product_quantity_return) < $qtyCancelProduct)
								$this->_errors[] = Tools::displayError('Invalid quantity selected for product.');

						}
					}
					if ($customizationList)
					{
						$customization_quantities = Customization::retrieveQuantitiesFromIds(array_keys($customizationList));

						foreach ($customizationList AS $id_customization => $id_order_detail)
						{
							$qtyCancelProduct = abs($customizationQtyList[$id_customization]);
							$customization_quantity = $customization_quantities[$id_customization];

							if (!$qtyCancelProduct)
								$this->_errors[] = Tools::displayError('No quantity selected for product.');

							if ($qtyCancelProduct > ($customization_quantity['quantity'] - ($customization_quantity['quantity_refunded'] + $customization_quantity['quantity_returned'])))
								$this->_errors[] = Tools::displayError('Invalid quantity selected for product.');
						}
					}

					if (!sizeof($this->_errors) AND $productList)
						foreach ($productList AS $key => $id_order_detail)
						{
							$qtyCancelProduct = abs($qtyList[$key]);
							$orderDetail = new OrderDetail((int)($id_order_detail));

							// Reinject product
							if (!$order->hasBeenDelivered() OR ($order->hasBeenDelivered() AND Tools::isSubmit('reinjectQuantities')))
							{
								$reinjectableQuantity = (int)($orderDetail->product_quantity) - (int)($orderDetail->product_quantity_reinjected);
								$quantityToReinject = $qtyCancelProduct > $reinjectableQuantity ? $reinjectableQuantity : $qtyCancelProduct;
								if (!Product::reinjectQuantities($orderDetail, $quantityToReinject))
									$this->_errors[] = Tools::displayError('Cannot re-stock product').' <span class="bold">'.$orderDetail->product_name.'</span>';
								else
								{
									$updProductAttributeID = !empty($orderDetail->product_attribute_id) ? (int)($orderDetail->product_attribute_id) : NULL;
									$newProductQty = Product::getQuantity($orderDetail->product_id, $updProductAttributeID);
									$product = get_object_vars(new Product($orderDetail->product_id, false, $this->context->language->id, $order->id_shop));
									if (!empty($orderDetail->product_attribute_id))
									{
										$updProduct['quantity_attribute'] = (int)($newProductQty);
										$product['quantity_attribute'] = $updProduct['quantity_attribute'];
									}
									else
									{
										$updProduct['stock_quantity'] = (int)($newProductQty);
										$product['stock_quantity'] = $updProduct['stock_quantity'];
									}
									Hook::exec('updateQuantity', array('product' => $product, 'order' => $order));
								}
							}

							// Delete product
							if (!$order->deleteProduct($order, $orderDetail, $qtyCancelProduct))
								$this->_errors[] = Tools::displayError('An error occurred during deletion of the product.').' <span class="bold">'.$orderDetail->product_name.'</span>';
							Hook::exec('cancelProduct', array('order' => $order, 'id_order_detail' => $id_order_detail));
						}
					if (!sizeof($this->_errors) AND $customizationList)
						foreach ($customizationList AS $id_customization => $id_order_detail)
						{
							$orderDetail = new OrderDetail((int)($id_order_detail));
							$qtyCancelProduct = abs($customizationQtyList[$id_customization]);
							if (!$order->deleteCustomization($id_customization, $qtyCancelProduct, $orderDetail))
								$this->_errors[] = Tools::displayError('An error occurred during deletion of product customization.').' '.$id_customization;
						}
					// E-mail params
					if ((isset($_POST['generateCreditSlip']) OR isset($_POST['generateDiscount'])) AND !sizeof($this->_errors))
					{
						$customer = new Customer((int)($order->id_customer));
						$params['{lastname}'] = $customer->lastname;
						$params['{firstname}'] = $customer->firstname;
						$params['{id_order}'] = $order->id;
					}

					// Generate credit slip
					if (isset($_POST['generateCreditSlip']) AND !sizeof($this->_errors))
					{
						if (!OrderSlip::createOrderSlip($order, $full_product_list, $full_quantity_list, isset($_POST['shippingBack'])))
							$this->_errors[] = Tools::displayError('Cannot generate credit slip');
						else
						{
							Hook::exec('orderSlip', array('order' => $order, 'productList' => $full_product_list, 'qtyList' => $full_quantity_list));
							@Mail::Send((int)$order->id_lang, 'credit_slip', Mail::l('New credit slip regarding your order', $order->id_lang),
							$params, $customer->email, $customer->firstname.' '.$customer->lastname, NULL, NULL, NULL, NULL,
							_PS_MAIL_DIR_, true);
						}
					}

					// Generate voucher
					if (isset($_POST['generateDiscount']) AND !sizeof($this->_errors))
					{
						if (!$voucher = Discount::createOrderDiscount($order, $full_product_list, $full_quantity_list, $this->l('Credit Slip concerning the order #'), isset($_POST['shippingBack'])))
							$this->_errors[] = Tools::displayError('Cannot generate voucher');
						else
						{
							$currency = $this->context->currency;
							$params['{voucher_amount}'] = Tools::displayPrice($voucher->value, $currency, false);
							$params['{voucher_num}'] = $voucher->name;
							@Mail::Send((int)($order->id_lang), 'voucher', Mail::l('New voucher regarding your order'),
							$params, $customer->email, $customer->firstname.' '.$customer->lastname, NULL, NULL, NULL,
							NULL, _PS_MAIL_DIR_, true);
						}
					}
				}
				else
					$this->_errors[] = Tools::displayError('No product or quantity selected.');

				// Redirect if no errors
				if (!sizeof($this->_errors))
					Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=24&token='.$this->token);
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		elseif (isset($_GET['messageReaded']))
		{
			Message::markAsReaded($_GET['messageReaded'], $this->context->employee->id);
		}
		else if (Tools::isSubmit('setTransactionId') && ((int)Tools::getValue('id_order')))
		{
			$order = new Order((int)(Tools::getValue('id_order')));
			$pcc = new PaymentCC((int)Tools::getValue('id_payment_cc'));

			$pcc->id_order = $order->id;
			$pcc->transaction_id = (string)Tools::getValue('transaction_id');
			$pcc->id_currency = $order->id_currency;
			$pcc->amount = $order->total_paid;
			$pcc->save();

			unset($order, $pcc);
		}
		elseif (Tools::isSubmit('submitAddOrder') == 1 && ($id_cart = Tools::getValue('id_cart')) && ($module_name = pSQL(Tools::getValue('payment_module_name'))) && ($id_order_state = Tools::getValue('id_order_state')))
		{

			if ($this->tabAccess['edit'] === '1')
			{
				$payment_module = Module::getInstanceByName($module_name);
				$cart = new Cart((int)$id_cart);
				$payment_module->validateOrder((int)$cart->id, (int)$id_order_state, $cart->getOrderTotal(true, Cart::BOTH), $payment_module->displayName, $this->l(sprintf('Manual order - ID Employee :%1', (int)Context::getContext()->cookie->id_employee)));
				if($payment_module->currentOrder)
					Tools::redirectAdmin(self::$currentIndex.'&id_order='.$payment_module->currentOrder.'&vieworder'.'&token='.$this->token);
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to add here.');
		}
		parent::postProcess();
	}

	public function initView()
	{
		$order = $this->loadObject();
		if (!Validate::isLoadedObject($order))
			return;

		$customer = new Customer($order->id_customer);
		$carrier = new Carrier($order->id_carrier);
		$products = $order->getProducts();

		foreach ($products as &$product)
		{
			if ($product['image'] != null)
			{
				$name = 'product_mini_'.(int)$product['product_id'].(isset($product['product_attribute_id']) ? '_'.(int)$product['product_attribute_id'] : '').'.jpg';
				// generate image cache, only for back office
				$product['image_tag'] = cacheImage(_PS_IMG_DIR_.'p/'.$product['image']->getExistingImgPath().'.jpg', $name, 45, 'jpg');
				$product['image_size'] = getimagesize(_PS_TMP_IMG_DIR_.$name);
			}
		}

		// Carrier module call
		$carrier_module_call = null;
		if ($carrier->is_module)
		{
			$module = Module::getInstanceByName($carrier->external_module_name);
			if (method_exists($module, 'displayInfoByCart'))
				$carrier_module_call = call_user_func(array($module, 'displayInfoByCart'), $order->id_cart);
		}

		// Retrieve addresses informations
		$addressInvoice = new Address($order->id_address_invoice, $this->context->language->id);
		if (Validate::isLoadedObject($addressInvoice) AND $addressInvoice->id_state)
			$invoiceState = new State((int)($addressInvoice->id_state));

		if ($order->id_address_invoice == $order->id_address_delivery)
		{
			$addressDelivery = $addressInvoice;
			if (isset($invoiceState))
				$deliveryState = $invoiceState;
		}
		else
		{
			$addressDelivery = new Address($order->id_address_delivery, $this->context->language->id);
			if (Validate::isLoadedObject($addressDelivery) AND $addressDelivery->id_state)
				$deliveryState = new State((int)($addressDelivery->id_state));
		}

		// Smarty assign
		$this->context->smarty->assign(array(
			'order' => $order,
			'cart' => new Cart($order->id),
			'customer' => $customer,
			'addresses' => array(
				'delivery' => $addressDelivery,
				'deliveryState' => isset($deliveryState) ? $deliveryState : null,
				'invoice' => $addressInvoice,
				'invoiceState' => isset($invoiceState) ? $invoiceState : null
			),
			'customerStats' => $customer->getStats(),
			'products' => $products,
			'discounts' => $order->getCartRules(),
			'returns' => OrderReturn::getOrdersReturn($order->id_customer, $order->id),
			'slips' => OrderSlip::getOrdersSlip($order->id_customer, $order->id),
			'orderMessages' => OrderMessage::getOrderMessages($order->id_lang),
			'messages' => Message::getMessagesByOrderId($order->id, true),
			'carrier' => $carrier = new Carrier($order->id_carrier),
			'history' => $order->getHistory($this->context->language->id),
			'states' => OrderState::getOrderStates($this->context->language->id),
			'warehouse_list' => Warehouse::getWarehouseList(false, $order->id_shop),
			'sources' => ConnectionsSource::getOrderSources($order->id),
			'currentState' => OrderHistory::getLastOrderState($order->id),
			'currency' => new Currency($order->id_currency),
			'previousOrder' => $order->getPreviousOrderId(),
			'nextOrder' => $order->getNextOrderId(),
			'currentIndex' => self::$currentIndex,
			'carrierModuleCall' => $carrier_module_call,
			'iso_code_lang' => $this->context->language->iso_code,
			'id_lang' => $this->context->language->id,
			'paymentCCDetails' => PaymentCC::getByOrderId($order->id)
		));

		// Assign Hook
		$this->context->smarty->assign(array(
			'HOOK_INVOICE' => Hook::exec('invoice', array('id_order' => $order->id)),
			'HOOK_ADMIN_ORDER' => Hook::exec('adminOrder', array('id_order' => $order->id))
		));
	}
	public function ajaxProcessSearchCustomers()
	{
		if ($customers = Customer::searchByName(pSQL(Tools::getValue('customer_search'))))
			$to_return = array('customers' => $customers,
									'found' => true);
		else
			$to_return = array('found' => false);

		$this->content = Tools::jsonEncode($to_return);
	}

	public function ajaxProcessSearchProducts()
	{
		$currency = new Currency((int)Tools::getValue('id_currency'));
		if ($products = Product::searchByName((int)$this->context->language->id, pSQL(Tools::getValue('product_search'))))
		{
			foreach ($products AS &$product)
			{
				$product['price'] = Tools::displayPrice(Tools::convertPrice($product['price'], $currency), $currency);
				$productObj = new Product((int)$product['id_product'], false, (int)$this->context->language->id);
				$combinations = array();
				$attributes = $productObj->getAttributesGroups((int)$this->context->language->id);
				$product['qty_in_stock'] = StockAvailable::getStockAvailableForProduct((int)$product['id_product'], 0, (int)$this->context->shop->getID());
				foreach($attributes AS $attribute)
				{
					if (!isset($combinations[$attribute['id_product_attribute']]['attributes']))
						$combinations[$attribute['id_product_attribute']]['attributes'] = '';
					$combinations[$attribute['id_product_attribute']]['attributes'] .= $attribute['attribute_name'].' - ';
					$combinations[$attribute['id_product_attribute']]['id_product_attribute'] = $attribute['id_product_attribute'];
					$combinations[$attribute['id_product_attribute']]['default_on'] = $attribute['default_on'];
					if (!isset($combinations[$attribute['id_product_attribute']]['price']))
						$combinations[$attribute['id_product_attribute']]['price'] =  Tools::displayPrice(Tools::convertPrice(Product::getPriceStatic((int)$product['id_product'], true, $attribute['id_product_attribute']), $currency), $currency);
					if (!isset($combinations[$attribute['id_product_attribute']]['qty_in_stock']))
						$combinations[$attribute['id_product_attribute']]['qty_in_stock']= StockAvailable::getStockAvailableForProduct((int)$product['id_product'], $attribute['id_product_attribute'], (int)$this->context->shop->getID());
				}

				foreach ($combinations AS &$combination)
					$combination['attributes'] = rtrim($combination['attributes'], ' - ');
				$product['combinations'] = $combinations;
			}
			$to_return = array('products' => $products,
								'found' => true);
		}
		else
			$to_return = array('found' => false);

		$this->content = Tools::jsonEncode($to_return);
	}

	public function ajaxProcessSendMailValidateOrder()
	{
		$errors = array();
		$cart = new Cart((int)Tools::getValue('id_cart'));
		if (Validate::isLoadedObject($cart))
		{
			$customer = new Customer((int)$cart->id_customer);
			if (Validate::isLoadedObject($customer))
			{
				$mailVars = array('{order_link}' => Context::getContext()->link->getPageLink('order', false, (int)$cart->id_lang, 'step=3&recover_cart='.(int)$cart->id.'&token_cart='.md5(_COOKIE_KEY_.'recover_cart_'.(int)$cart->id)),
 																'{firstname}' => $customer->firstname,
																'{lastname}' => $customer->lastname,);
				if (Mail::Send((int)$cart->id_lang, 'backoffice_order', Mail::l('Process the payment of your order'), $mailVars, $customer->email, $customer->firstname.' '.$customer->lastname, NULL, NULL, NULL, NULL,_PS_MAIL_DIR_, true))
					die(Tools::jsonEncode(array('errors' => false, 'result' => $this->l('The mail was sent to your customer.'))));
			}
		}
		$this->content = Tools::jsonEncode(array('errors' => true, 'result' => $this->l('Error in sending the email to your customer.')));
	}
}
