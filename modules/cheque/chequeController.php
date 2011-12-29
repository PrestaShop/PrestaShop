<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class ModuleChequeController extends ModuleController
{
	/**
	 * @see FrontController::postProcess()
	 */
	public function postProcess()
	{
		$this->display_column_left = false;
		if ($this->process == 'validation')
			$this->processValidation();
	}

	/**
	 * Validate cheque payment
	 */
	public function processValidation()
	{
		$cart = $this->context->cart;

		if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
			Tools::redirect('index.php?controller=order&step=1');

		// Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
		$authorized = false;
		foreach (Module::getPaymentModules() as $module)
			if ($module['name'] == 'cheque')
			{
				$authorized = true;
				break;
			}

		if (!$authorized)
			die(Tools::displayError('This payment method is not available.'));

		$customer = new Customer($cart->id_customer);

		if (!Validate::isLoadedObject($customer))
			Tools::redirect('index.php?controller=order&step=1');

		$currency = Tools::isSubmit('currency_payement') ? new Currency(Tools::getValue('currency_payement')) : $this->context->currency;
		$total = (float)$cart->getOrderTotal(true, Cart::BOTH);

		$mailVars =	array(
			'{cheque_name}' => Configuration::get('CHEQUE_NAME'),
			'{cheque_address}' => Configuration::get('CHEQUE_ADDRESS'),
			'{cheque_address_html}' => str_replace("\n", '<br />', Configuration::get('CHEQUE_ADDRESS')));

		$this->module->validateOrder((int)$cart->id, Configuration::get('PS_OS_CHEQUE'), $total, $this->module->displayName, NULL, $mailVars, (int)$currency->id, false, $customer->secure_key);
		Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int)$cart->id.'&id_module='.(int)$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
	}

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		if ($this->process == 'payment')
			$this->assignPaymentExecution();
	}

	/**
	 * Assign cheque payment template
	 */
	public function assignPaymentExecution()
	{
		$cart = $this->context->cart;
		if (!$this->module->checkCurrency($cart))
			Tools::redirect('index.php?controller=order');

		$this->context->smarty->assign(array(
			'nbProducts' => $cart->nbProducts(),
			'cust_currency' => $cart->id_currency,
			'currencies' => $this->module->getCurrency((int)$cart->id_currency),
			'total' => $cart->getOrderTotal(true, Cart::BOTH),
			'isoCode' => $this->context->language->iso_code,
			'chequeName' => $this->module->chequeName,
			'chequeAddress' => Tools::nl2br($this->module->address),
			'cheque_path' => $this->module->getPathUri(),
			'cheque_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
		));

		$this->setTemplate('payment_execution.tpl');
	}
}
