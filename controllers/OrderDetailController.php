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

class OrderDetailControllerCore extends FrontController
{
	public function __construct()
	{
		$this->auth = true;
		$this->authRedirection = 'history.php';
		$this->ssl = true;

		parent::__construct();

		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
	}

	public function preProcess()
	{
		parent::preProcess();

		if (Tools::isSubmit('submitMessage'))
		{
			$idOrder = (int)(Tools::getValue('id_order'));
			$msgText = htmlentities(Tools::getValue('msgText'), ENT_COMPAT, 'UTF-8');

			if (!$idOrder OR !Validate::isUnsignedId($idOrder))
				$this->errors[] = Tools::displayError('Order is no longer valid');
			elseif (empty($msgText))
				$this->errors[] = Tools::displayError('Message cannot be blank');
			elseif (!Validate::isMessage($msgText))
				$this->errors[] = Tools::displayError('Message is invalid (HTML is not allowed)');
			if(!sizeof($this->errors))
			{
				$order = new Order((int)($idOrder));
				if (Validate::isLoadedObject($order) AND $order->id_customer == self::$cookie->id_customer)
				{
					$message = new Message();
					$message->id_customer = (int)(self::$cookie->id_customer);
					$message->message = $msgText;
					$message->id_order = (int)($idOrder);
					$message->private = false;
					$message->add();
					if (!Configuration::get('PS_MAIL_EMAIL_MESSAGE'))
						$to = strval(Configuration::get('PS_SHOP_EMAIL'));
					else
					{
						$to = new Contact((int)(Configuration::get('PS_MAIL_EMAIL_MESSAGE')));
						$to = strval($to->email);
					}
					$toName = strval(Configuration::get('PS_SHOP_NAME'));
					$customer = new Customer((int)(self::$cookie->id_customer));
					if (Validate::isLoadedObject($customer))
						Mail::Send((int)(self::$cookie->id_lang), 'order_customer_comment', Mail::l('Message from a customer'),
						array(
						'{lastname}' => $customer->lastname,
						'{firstname}' => $customer->firstname,
						'{email}' => $customer->email,
						'{id_order}' => (int)($message->id_order),
						'{message}' => $message->message),
						$to, $toName, $customer->email, $customer->firstname.' '.$customer->lastname);
					if (Tools::getValue('ajax') != 'true')
						Tools::redirect('order-detail.php?id_order='.(int)($idOrder));
				}
				else
				{
					$this->errors[] = Tools::displayError('Order not found');
				}
			}
		}

		if (!$id_order = (int)(Tools::getValue('id_order')) OR !Validate::isUnsignedId($id_order))
			$this->errors[] = Tools::displayError('Order ID required');
		else
		{
			$order = new Order($id_order);
			if (Validate::isLoadedObject($order) AND $order->id_customer == self::$cookie->id_customer)
			{
				$id_order_state = (int)($order->getCurrentState());
				$carrier = new Carrier((int)($order->id_carrier), (int)($order->id_lang));
				$addressInvoice = new Address((int)($order->id_address_invoice));
				$addressDelivery = new Address((int)($order->id_address_delivery));
				if ($order->total_discounts > 0)
					self::$smarty->assign('total_old', (float)($order->total_paid - $order->total_discounts));
				$products = $order->getProducts();

				$customizedDatas = Product::getAllCustomizedDatas((int)($order->id_cart));
				Product::addCustomizationPrice($products, $customizedDatas);

				$customer = new Customer($order->id_customer);

				self::$smarty->assign(array(
					'shop_name' => strval(Configuration::get('PS_SHOP_NAME')),
					'order' => $order,
					'return_allowed' => (int)($order->isReturnable()),
					'currency' => new Currency($order->id_currency),
					'order_state' => (int)($id_order_state),
					'invoiceAllowed' => (int)(Configuration::get('PS_INVOICE')),
					'invoice' => (OrderState::invoiceAvailable((int)($id_order_state)) AND $order->invoice_number),
					'order_history' => $order->getHistory((int)(self::$cookie->id_lang), false, true),
					'products' => $products,
					'discounts' => $order->getDiscounts(),
					'carrier' => $carrier,
					'address_invoice' => $addressInvoice,
					'invoiceState' => (Validate::isLoadedObject($addressInvoice) AND $addressInvoice->id_state) ? new State((int)($addressInvoice->id_state)) : false,
					'address_delivery' => $addressDelivery,
					'deliveryState' => (Validate::isLoadedObject($addressDelivery) AND $addressDelivery->id_state) ? new State((int)($addressDelivery->id_state)) : false,
					'is_guest' => false,
					'messages' => Message::getMessagesByOrderId((int)($order->id)),
					'CUSTOMIZE_FILE' => _CUSTOMIZE_FILE_,
					'CUSTOMIZE_TEXTFIELD' => _CUSTOMIZE_TEXTFIELD_,
					'use_tax' => Configuration::get('PS_TAX'),
					'group_use_tax' => (Group::getPriceDisplayMethod($customer->id_default_group) == PS_TAX_INC),
					'customizedDatas' => $customizedDatas));
				if ($carrier->url AND $order->shipping_number)
					self::$smarty->assign('followup', str_replace('@', $order->shipping_number, $carrier->url));
				self::$smarty->assign('HOOK_ORDERDETAILDISPLAYED', Module::hookExec('orderDetailDisplayed', array('order' => $order)));
				Module::hookExec('OrderDetail', array('carrier' => $carrier, 'order' => $order));
			}
			else
				$this->errors[] = Tools::displayError('Cannot find this order');
		}
	}

	public function displayHeader()
	{
		if (Tools::getValue('ajax') != 'true')
			parent::displayHeader();
	}

	public function displayContent()
	{
		parent::displayContent();
		self::$smarty->display(_PS_THEME_DIR_.'order-detail.tpl');
	}

	public function displayFooter()
	{
		if (Tools::getValue('ajax') != 'true')
			parent::displayFooter();
	}
}

