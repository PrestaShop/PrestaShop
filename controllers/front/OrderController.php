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

class OrderControllerCore extends ParentOrderController
{
	public $step;

	/**
	 * Initialize order controller
	 * @see FrontController::init()
	 */
	public function init()
	{
		global $orderTotal;

		parent::init();

		$this->step = (int)(Tools::getValue('step'));
		if (!$this->nbProducts)
			$this->step = -1;		

		// If some products have disappear
		if (!$this->context->cart->checkQuantities())
		{
			$this->step = 0;
			$this->errors[] = Tools::displayError('An item in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.');
		}

		// Check minimal amount
		$currency = Currency::getCurrency((int)$this->context->cart->id_currency);

		$orderTotal = $this->context->cart->getOrderTotal();
		$minimal_purchase = Tools::convertPrice((float)Configuration::get('PS_PURCHASE_MINIMUM'), $currency);
		if ($this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS) < $minimal_purchase && $this->step > 0)
		{
			$this->step = 0;
			$this->errors[] = sprintf(
				Tools::displayError('A minimum purchase total of %s is required in order to validate your order.'),
				Tools::displayPrice($minimal_purchase, $currency)
			);
		}
		if (!$this->context->customer->isLogged(true) && in_array($this->step, array(1, 2, 3)))
		{
			$back_url = $this->context->link->getPageLink('order', true, (int)$this->context->language->id, array('step' => $this->step, 'multi-shipping' => (int)Tools::getValue('multi-shipping')));
			$params = array('multi-shipping' => (int)Tools::getValue('multi-shipping'), 'display_guest_checkout' => (int)Configuration::get('PS_GUEST_CHECKOUT_ENABLED'), 'back' => $back_url);
			Tools::redirect($this->context->link->getPageLink('authentication', true, (int)$this->context->language->id, $params));
		}

		if (Tools::getValue('multi-shipping') == 1)
			$this->context->smarty->assign('multi_shipping', true);
		else
			$this->context->smarty->assign('multi_shipping', false);

		if ($this->context->customer->id)
			$this->context->smarty->assign('address_list', $this->context->customer->getAddresses($this->context->language->id));
		else
			$this->context->smarty->assign('address_list', array());
	}

	public function postProcess()
	{
		// Update carrier selected on preProccess in order to fix a bug of
		// block cart when it's hooked on leftcolumn
		if ($this->step == 3 && Tools::isSubmit('processCarrier'))
			$this->processCarrier();
	}

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		global $isVirtualCart;

		parent::initContent();

		if (Tools::isSubmit('ajax') && Tools::getValue('method') == 'updateExtraCarrier')
		{
			// Change virtualy the currents delivery options
			$delivery_option = $this->context->cart->getDeliveryOption();
			$delivery_option[(int)Tools::getValue('id_address')] = Tools::getValue('id_delivery_option');
			$this->context->cart->setDeliveryOption($delivery_option);
			$this->context->cart->save();
			$return = array(
				'content' => Hook::exec(
					'displayCarrierList',
					array(
						'address' => new Address((int)Tools::getValue('id_address'))
					)
				)
			);
			die(Tools::jsonEncode($return));
		}

		if ($this->nbProducts)
			$this->context->smarty->assign('virtual_cart', $isVirtualCart);

		// 4 steps to the order
		switch ((int)$this->step)
		{
			case -1;
				$this->context->smarty->assign('empty', 1);
				$this->setTemplate(_PS_THEME_DIR_.'shopping-cart.tpl');
			break;

			case 1:
				$this->_assignAddress();
				$this->processAddressFormat();
				if (Tools::getValue('multi-shipping') == 1)
				{
					$this->_assignSummaryInformations();
					$this->context->smarty->assign('product_list', $this->context->cart->getProducts());
					$this->setTemplate(_PS_THEME_DIR_.'order-address-multishipping.tpl');
				}
				else
					$this->setTemplate(_PS_THEME_DIR_.'order-address.tpl');
			break;

			case 2:
				if (Tools::isSubmit('processAddress'))
					$this->processAddress();
				$this->autoStep();
				$this->_assignCarrier();
				$this->setTemplate(_PS_THEME_DIR_.'order-carrier.tpl');
			break;

			case 3:
				// Check that the conditions (so active) were accepted by the customer
				$cgv = Tools::getValue('cgv') || $this->context->cookie->check_cgv;
				if (Configuration::get('PS_CONDITIONS') && (!Validate::isBool($cgv) || $cgv == false))
					Tools::redirect('index.php?controller=order&step=2');
				Context::getContext()->cookie->check_cgv = true;

				// Check the delivery option is setted
				if (!$this->context->cart->isVirtualCart())
				{
					if (!Tools::getValue('delivery_option') && !Tools::getValue('id_carrier') && !$this->context->cart->delivery_option && !$this->context->cart->id_carrier)
						Tools::redirect('index.php?controller=order&step=2');
					elseif (!Tools::getValue('id_carrier') && !$this->context->cart->id_carrier)
					{
						foreach (Tools::getValue('delivery_option') as $delivery_option)
							if (empty($delivery_option))
								Tools::redirect('index.php?controller=order&step=2');
					}
				}

				$this->autoStep();

				// Bypass payment step if total is 0
				if (($id_order = $this->_checkFreeOrder()) && $id_order)
				{
					if ($this->context->customer->is_guest)
					{
						$order = new Order((int)$id_order);
						$email = $this->context->customer->email;
						$this->context->customer->mylogout(); // If guest we clear the cookie for security reason
						Tools::redirect('index.php?controller=guest-tracking&id_order='.urlencode($order->reference).'&email='.urlencode($email));
					}
					else
						Tools::redirect('index.php?controller=history');
				}
				$this->_assignPayment();
				// assign some informations to display cart
				$this->_assignSummaryInformations();
				$this->setTemplate(_PS_THEME_DIR_.'order-payment.tpl');
			break;

			default:
				$this->_assignSummaryInformations();
				$this->setTemplate(_PS_THEME_DIR_.'shopping-cart.tpl');
			break;
		}

		$this->context->smarty->assign(array(
			'currencySign' => $this->context->currency->sign,
			'currencyRate' => $this->context->currency->conversion_rate,
			'currencyFormat' => $this->context->currency->format,
			'currencyBlank' => $this->context->currency->blank,
		));
	}

	protected function processAddressFormat()
	{
		$addressDelivery = new Address((int)$this->context->cart->id_address_delivery);
		$addressInvoice = new Address((int)$this->context->cart->id_address_invoice);

		$invoiceAddressFields = AddressFormat::getOrderedAddressFields($addressInvoice->id_country, false, true);
		$deliveryAddressFields = AddressFormat::getOrderedAddressFields($addressDelivery->id_country, false, true);

		$this->context->smarty->assign(array(
			'inv_adr_fields' => $invoiceAddressFields,
			'dlv_adr_fields' => $deliveryAddressFields));
	}

	/**
	 * Order process controller
	 */
	public function autoStep()
	{
		global $isVirtualCart;

		if ($this->step >= 2 && (!$this->context->cart->id_address_delivery || !$this->context->cart->id_address_invoice))
			Tools::redirect('index.php?controller=order&step=1');

		if ($this->step > 2 && !$isVirtualCart && count($this->context->cart->getDeliveryOptionList()) == 0)
			Tools::redirect('index.php?controller=order&step=2');

		$delivery = new Address((int)$this->context->cart->id_address_delivery);
		$invoice = new Address((int)$this->context->cart->id_address_invoice);

		if ($delivery->deleted || $invoice->deleted)
		{
			if ($delivery->deleted)
				unset($this->context->cart->id_address_delivery);
			if ($invoice->deleted)
				unset($this->context->cart->id_address_invoice);
			Tools::redirect('index.php?controller=order&step=1');
		}
	}

	/**
	 * Manage address
	 */
	public function processAddress()
	{
		if (!Tools::getValue('multi-shipping'))
			$this->context->cart->setNoMultishipping();
		
		$same = Tools::isSubmit('same');
		if(!Tools::getValue('id_address_invoice', false) && !$same)
			$same = true;

		if (!Customer::customerHasAddress($this->context->customer->id, (int)Tools::getValue('id_address_delivery'))
			|| (!$same && Tools::getValue('id_address_delivery') != Tools::getValue('id_address_invoice')
				&& !Customer::customerHasAddress($this->context->customer->id, (int)Tools::getValue('id_address_invoice'))))
			$this->errors[] = Tools::displayError('Invalid address', !Tools::getValue('ajax'));
		else
		{
			$this->context->cart->id_address_delivery = (int)Tools::getValue('id_address_delivery');
			$this->context->cart->id_address_invoice = $same ? $this->context->cart->id_address_delivery : (int)Tools::getValue('id_address_invoice');
			
			CartRule::autoRemoveFromCart($this->context);
			CartRule::autoAddToCart($this->context);
			
			if (!$this->context->cart->update())
				$this->errors[] = Tools::displayError('An error occurred while updating your cart.', !Tools::getValue('ajax'));

			if (!$this->context->cart->isMultiAddressDelivery())
				$this->context->cart->setNoMultishipping(); // If there is only one delivery address, set each delivery address lines with the main delivery address

			if (Tools::isSubmit('message'))
				$this->_updateMessage(Tools::getValue('message'));
						
			// Add checking for all addresses
			$address_without_carriers = $this->context->cart->getDeliveryAddressesWithoutCarriers();
			if (count($address_without_carriers) && !$this->context->cart->isVirtualCart())
			{
				if (count($address_without_carriers) > 1)
					$this->errors[] = sprintf(Tools::displayError('There are no carriers that deliver to some addresses you selected.', !Tools::getValue('ajax')));
				elseif ($this->context->cart->isMultiAddressDelivery())
					$this->errors[] = sprintf(Tools::displayError('There are no carriers that deliver to one of the address you selected.', !Tools::getValue('ajax')));
				else
					$this->errors[] = sprintf(Tools::displayError('There are no carriers that deliver to the address you selected.', !Tools::getValue('ajax')));
			}
		}
		
		if ($this->errors)
		{
			if (Tools::getValue('ajax'))
				die('{"hasError" : true, "errors" : ["'.implode('\',\'', $this->errors).'"]}');
			$this->step = 1;
		}

		if ($this->ajax)
			die(true);
	}

	/**
	 * Carrier step
	 */
	protected function processCarrier()
	{
		global $orderTotal;
		parent::_processCarrier();

		if (count($this->errors))
		{
			$this->context->smarty->assign('errors', $this->errors);
			$this->_assignCarrier();
			$this->step = 2;
			$this->displayContent();
			include(dirname(__FILE__).'/../footer.php');
			exit;
		}
		$orderTotal = $this->context->cart->getOrderTotal();
	}

	/**
	 * Address step
	 */
	protected function _assignAddress()
	{
		parent::_assignAddress();

		if (Tools::getValue('multi-shipping'))
			$this->context->cart->autosetProductAddress();

		$this->context->smarty->assign('cart', $this->context->cart);

	}

	/**
	 * Carrier step
	 */
	protected function _assignCarrier()
	{
		if (!isset($this->context->customer->id))
			die(Tools::displayError('Fatal error: No customer'));
		// Assign carrier
		parent::_assignCarrier();
		// Assign wrapping and TOS
		$this->_assignWrappingAndTOS();

		$this->context->smarty->assign(
			array(
				'is_guest' => (isset($this->context->customer->is_guest) ? $this->context->customer->is_guest : 0)
			));
	}

	/**
	 * Payment step
	 */
	protected function _assignPayment()
	{
		global $orderTotal;

		// Redirect instead of displaying payment modules if any module are grefted on
		Hook::exec('displayBeforePayment', array('module' => 'order.php?step=3'));

		/* We may need to display an order summary */
		$this->context->smarty->assign($this->context->cart->getSummaryDetails());
		$this->context->smarty->assign(array(
			'total_price' => (float)($orderTotal),
			'taxes_enabled' => (int)(Configuration::get('PS_TAX'))
		));
		$this->context->cart->checkedTOS = '1';

		parent::_assignPayment();
	}
}

