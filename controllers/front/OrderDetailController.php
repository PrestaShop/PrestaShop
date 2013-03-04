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

class OrderDetailControllerCore extends FrontController
{
	public $auth = true;
	public $authRedirection = 'history';
	public $ssl = true;

	/**
	 * Initialize order detail controller
	 * @see FrontController::init()
	 */
	public function init()
	{
		parent::init();
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
	}

	/**
	 * Start forms process
	 * @see FrontController::postProcess()
	 */
	public function postProcess()
	{
		if (Tools::isSubmit('submitMessage'))
		{
			$idOrder = (int)(Tools::getValue('id_order'));
			$msgText = Tools::getValue('msgText');

			if (!$idOrder || !Validate::isUnsignedId($idOrder))
				$this->errors[] = Tools::displayError('Order is no longer valid');
			elseif (empty($msgText))
				$this->errors[] = Tools::displayError('Message cannot be blank');
			elseif (!Validate::isMessage($msgText))
				$this->errors[] = Tools::displayError('Message is invalid (HTML is not allowed)');
			if (!count($this->errors))
			{
				$order = new Order($idOrder);
				if (Validate::isLoadedObject($order) && $order->id_customer == $this->context->customer->id)
				{
					//check if a thread already exist
					$id_customer_thread = CustomerThread::getIdCustomerThreadByEmailAndIdOrder($this->context->customer->email, $order->id);

					$cm = new CustomerMessage();
					if (!$id_customer_thread)
					{
						$ct = new CustomerThread();
						$ct->id_contact = 0;
						$ct->id_customer = (int)$order->id_customer;
						$ct->id_shop = (int)$this->context->shop->id;
						if ($id_product = (int)Tools::getValue('id_product') && $order->orderContainProduct((int)$id_product))
							$ct->id_product = $id_product;
						$ct->id_order = (int)$order->id;
						$ct->id_lang = (int)$this->context->language->id;
						$ct->email = $this->context->customer->email;
						$ct->status = 'open';
						$ct->token = Tools::passwdGen(12);
						$ct->add();
					}
					else
						$ct = new CustomerThread((int)$id_customer_thread);
					$cm->id_customer_thread = $ct->id;
					$cm->message = $msgText;
					$cm->ip_address = ip2long($_SERVER['REMOTE_ADDR']);
					$cm->add();

					if (!Configuration::get('PS_MAIL_EMAIL_MESSAGE'))
						$to = strval(Configuration::get('PS_SHOP_EMAIL'));
					else
					{
						$to = new Contact((int)(Configuration::get('PS_MAIL_EMAIL_MESSAGE')));
						$to = strval($to->email);
					}
					$toName = strval(Configuration::get('PS_SHOP_NAME'));
					$customer = $this->context->customer;

					if (Validate::isLoadedObject($customer))
						Mail::Send($this->context->language->id, 'order_customer_comment', Mail::l('Message from a customer'),
						array(
							'{lastname}' => $customer->lastname,
							'{firstname}' => $customer->firstname,
							'{email}' => $customer->email,
							'{id_order}' => (int)($order->id),
							'{order_name}' => $order->getUniqReference(),
							'{message}' => Tools::nl2br($msgText)
						),
						$to, $toName, $customer->email, $customer->firstname.' '.$customer->lastname);

					if (Tools::getValue('ajax') != 'true')
						Tools::redirect('index.php?controller=order-detail&id_order='.(int)$idOrder);

				}
				else
					$this->errors[] = Tools::displayError('Order not found');
			}
		}
	}

	public function displayAjax()
	{
		$this->display();
	}

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		if (!($id_order = (int)Tools::getValue('id_order')) || !Validate::isUnsignedId($id_order))
			$this->errors[] = Tools::displayError('Order ID required');
		else
		{
			$order = new Order($id_order);
			if (Validate::isLoadedObject($order) && $order->id_customer == $this->context->customer->id)
			{
				$id_order_state = (int)($order->getCurrentState());
				$carrier = new Carrier((int)($order->id_carrier), (int)($order->id_lang));
				$addressInvoice = new Address((int)($order->id_address_invoice));
				$addressDelivery = new Address((int)($order->id_address_delivery));

				$inv_adr_fields = AddressFormat::getOrderedAddressFields($addressInvoice->id_country);
				$dlv_adr_fields = AddressFormat::getOrderedAddressFields($addressDelivery->id_country);

				$invoiceAddressFormatedValues = AddressFormat::getFormattedAddressFieldsValues($addressInvoice, $inv_adr_fields);
				$deliveryAddressFormatedValues = AddressFormat::getFormattedAddressFieldsValues($addressDelivery, $dlv_adr_fields);

				if ($order->total_discounts > 0)
					$this->context->smarty->assign('total_old', (float)($order->total_paid - $order->total_discounts));
				$products = $order->getProducts();

				/* DEPRECATED: customizedDatas @since 1.5 */
				$customizedDatas = Product::getAllCustomizedDatas((int)($order->id_cart));
				Product::addCustomizationPrice($products, $customizedDatas);

				OrderReturn::addReturnedQuantity($products, $order->id);

				$customer = new Customer($order->id_customer);

				$this->context->smarty->assign(array(
					'shop_name' => strval(Configuration::get('PS_SHOP_NAME')),
					'order' => $order,
					'return_allowed' => (int)($order->isReturnable()),
					'currency' => new Currency($order->id_currency),
					'order_state' => (int)($id_order_state),
					'invoiceAllowed' => (int)(Configuration::get('PS_INVOICE')),
					'invoice' => (OrderState::invoiceAvailable($id_order_state) && count($order->getInvoicesCollection())),
					'order_history' => $order->getHistory($this->context->language->id, false, true),
					'products' => $products,
					'discounts' => $order->getCartRules(),
					'carrier' => $carrier,
					'address_invoice' => $addressInvoice,
					'invoiceState' => (Validate::isLoadedObject($addressInvoice) && $addressInvoice->id_state) ? new State($addressInvoice->id_state) : false,
					'address_delivery' => $addressDelivery,
					'inv_adr_fields' => $inv_adr_fields,
					'dlv_adr_fields' => $dlv_adr_fields,
					'invoiceAddressFormatedValues' => $invoiceAddressFormatedValues,
					'deliveryAddressFormatedValues' => $deliveryAddressFormatedValues,
					'deliveryState' => (Validate::isLoadedObject($addressDelivery) && $addressDelivery->id_state) ? new State($addressDelivery->id_state) : false,
					'is_guest' => false,
					'messages' => CustomerMessage::getMessagesByOrderId((int)($order->id), false),
					'CUSTOMIZE_FILE' => Product::CUSTOMIZE_FILE,
					'CUSTOMIZE_TEXTFIELD' => Product::CUSTOMIZE_TEXTFIELD,
					'isRecyclable' => Configuration::get('PS_RECYCLABLE_PACK'),
					'use_tax' => Configuration::get('PS_TAX'),
					'group_use_tax' => (Group::getPriceDisplayMethod($customer->id_default_group) == PS_TAX_INC),
					/* DEPRECATED: customizedDatas @since 1.5 */
					'customizedDatas' => $customizedDatas
					/* DEPRECATED: customizedDatas @since 1.5 */
				));

				if ($carrier->url && $order->shipping_number)
					$this->context->smarty->assign('followup', str_replace('@', $order->shipping_number, $carrier->url));
				$this->context->smarty->assign('HOOK_ORDERDETAILDISPLAYED', Hook::exec('displayOrderDetail', array('order' => $order)));
				Hook::exec('actionOrderDetail', array('carrier' => $carrier, 'order' => $order));

				unset($carrier, $addressInvoice, $addressDelivery);
			}
			else
				$this->errors[] = Tools::displayError('Cannot find this order');
			unset($order);
		}

		$this->setTemplate(_PS_THEME_DIR_.'order-detail.tpl');
	}

	public function setMedia()
	{
		if (Tools::getValue('ajax') != 'true')
		{
			parent::setMedia();
			$this->addCSS(_THEME_CSS_DIR_.'history.css');
			$this->addCSS(_THEME_CSS_DIR_.'addresses.css');
		}
	}
}

