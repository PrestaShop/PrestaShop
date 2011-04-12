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

ControllerFactory::includeController('ParentOrderController');

class OrderControllerCore extends ParentOrderController
{
	public $step;

	public function init()
	{
		parent::init();

		$this->step = (int)(Tools::getValue('step'));
		if (!$this->nbProducts)
			$this->step = -1;
	}

	public function preProcess()
	{
		global $isVirtualCart, $orderTotal;

		parent::preProcess();

		/* If some products have disappear */
		if (!self::$cart->checkQuantities())
		{
			$this->step = 0;
			$this->errors[] = Tools::displayError('An item in your cart is no longer available for this quantity, you cannot proceed with your order.');
		}

		/* Check minimal amount */
		$currency = Currency::getCurrency((int)self::$cart->id_currency);

		$orderTotal = self::$cart->getOrderTotal();
		$minimalPurchase = Tools::convertPrice((float)Configuration::get('PS_PURCHASE_MINIMUM'), $currency);

		if ($orderTotal < $minimalPurchase)
		{
			$this->step = 0;
			$this->errors[] = Tools::displayError('A minimum purchase total of').' '.Tools::displayPrice($minimalPurchase, $currency).
			' '.Tools::displayError('is required in order to validate your order.');
		}

		if (!self::$cookie->isLogged(true) AND in_array($this->step, array(1, 2, 3)))
			Tools::redirect('authentication.php?back=order.php?step='.$this->step);

		if ($this->nbProducts)
			self::$smarty->assign('virtual_cart', $isVirtualCart);
	}

	public function displayHeader()
	{
		if (!Tools::getValue('ajax'))
			parent::displayHeader();
	}

	public function process()
	{
		parent::process();

		/* 4 steps to the order */
		switch ((int)$this->step)
		{
			case -1;
				self::$smarty->assign('empty', 1);
				break;
			case 1:
				$this->_assignAddress();
				break;
			case 2:
				if(Tools::isSubmit('processAddress'))
					$this->processAddress();
				$this->autoStep();
				$this->_assignCarrier();
				break;
			case 3:
				if(Tools::isSubmit('processCarrier'))
					$this->processCarrier();
				$this->autoStep();
				/* Bypass payment step if total is 0 */
				if (($id_order = $this->_checkFreeOrder()) AND $id_order)
				{
					if (self::$cookie->is_guest)
					{
						$email = self::$cookie->email;
						self::$cookie->logout(); // If guest we clear the cookie for security reason
						Tools::redirect('guest-tracking.php?id_order='.(int)$id_order.'&email='.urlencode($email));
					}
					else
						Tools::redirect('history.php');
				}
				$this->_assignPayment();
				break;
			default:
				$this->_assignSummaryInformations();
				break;
		}
	}

	public function displayContent()
	{
		parent::displayContent();

		switch ((int)$this->step)
		{
			case -1:
				self::$smarty->display(_PS_THEME_DIR_.'shopping-cart.tpl');
				break;
			case 1:
				self::$smarty->display(_PS_THEME_DIR_.'order-address.tpl');
				break;
			case 2:
				self::$smarty->display(_PS_THEME_DIR_.'order-carrier.tpl');
				break;
			case 3:
				self::$smarty->display(_PS_THEME_DIR_.'order-payment.tpl');
				break;
			default:
				self::$smarty->display(_PS_THEME_DIR_.'shopping-cart.tpl');
				break;
		}
	}

	public function displayFooter()
	{
		if (!Tools::getValue('ajax'))
			parent::displayFooter();
	}

	/* Order process controller */
	public function autoStep()
	{
		global $isVirtualCart;

		if ($this->step >= 2 AND (!self::$cart->id_address_delivery OR !self::$cart->id_address_invoice))
			Tools::redirect('order.php?step=1');
		$delivery = new Address((int)(self::$cart->id_address_delivery));
		$invoice = new Address((int)(self::$cart->id_address_invoice));
		if ($delivery->deleted OR $invoice->deleted)
		{
			if ($delivery->deleted)
				unset(self::$cart->id_address_delivery);
			if ($invoice->deleted)
				unset(self::$cart->id_address_invoice);
			Tools::redirect('order.php?step=1');
		}
		elseif ($this->step >= 3 AND !self::$cart->id_carrier AND !$isVirtualCart)
			Tools::redirect('order.php?step=2');
	}

	/*
	 * Manage address
	 */
	public function processAddress()
	{
		if (!Tools::isSubmit('id_address_delivery') OR !Address::isCountryActiveById((int)Tools::getValue('id_address_delivery')))
			$this->errors[] = Tools::displayError('This address is not in a valid area.');
		else
		{
			self::$cart->id_address_delivery = (int)(Tools::getValue('id_address_delivery'));
			self::$cart->id_address_invoice = Tools::isSubmit('same') ? self::$cart->id_address_delivery : (int)(Tools::getValue('id_address_invoice'));
			if (!self::$cart->update())
				$this->errors[] = Tools::displayError('An error occurred while updating your cart.');

			if (Tools::isSubmit('message'))
				$this->_updateMessage(Tools::getValue('message'));
		}
		if (sizeof($this->errors))
		{
			if (Tools::getValue('ajax'))
				die('{"hasError" : true, "errors" : ["'.implode('\',\'', $this->errors).'"]}');
			$this->step = 1;
		}
		if (Tools::getValue('ajax'))
			die(true);
	}

	/* Carrier step */
	protected function processCarrier()
	{
		global $orderTotal;

		parent::_processCarrier();

		if (sizeof($this->errors))
		{
			self::$smarty->assign('errors', $this->errors);
			$this->_assignCarrier();
			$this->step = 2;
			$this->displayContent();
			include(dirname(__FILE__).'/../footer.php');
			exit;
		}
		$orderTotal = self::$cart->getOrderTotal();
	}

	/* Address step */
	protected function _assignAddress()
	{
		parent::_assignAddress();

		self::$smarty->assign('cart', self::$cart);
		if (self::$cookie->is_guest)
			Tools::redirect('order.php?step=2');
	}

	/* Carrier step */
	protected function _assignCarrier()
	{
		global $defaultCountry;

		if (isset(self::$cookie->id_customer))
			$customer = new Customer((int)(self::$cookie->id_customer));
		else
			die(Tools::displayError('Fatal error: No customer'));
		// Assign carrier
		parent::_assignCarrier();
		// Assign wrapping and TOS
		$this->_assignWrappingAndTOS();

		self::$smarty->assign('is_guest' ,(isset(self::$cookie->is_guest) ? self::$cookie->is_guest : 0));
	}

	/* Payment step */
	protected function _assignPayment()
	{
		global $orderTotal;

		// Redirect instead of displaying payment modules if any module are grefted on
		Hook::backBeforePayment('order.php?step=3');
		
		/* We may need to display an order summary */
		self::$smarty->assign(self::$cart->getSummaryDetails());
		self::$smarty->assign(array(
			'total_price' => (float)($orderTotal),
			'taxes_enabled' => (int)(Configuration::get('PS_TAX'))
		));
		self::$cookie->checkedTOS = '1';
		
		parent::_assignPayment();
	}
}

