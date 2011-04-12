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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class BankWire extends PaymentModule
{
	private $_html = '';
	private $_postErrors = array();

	public  $details;
	public  $owner;
	public	$address;

	public function __construct()
	{
		$this->name = 'bankwire';
		$this->tab = 'payments_gateways';
		$this->version = '0.5';
		$this->author = 'PrestaShop';
		
		$this->currencies = true;
		$this->currencies_mode = 'checkbox';

		$config = Configuration::getMultiple(array('BANK_WIRE_DETAILS', 'BANK_WIRE_OWNER', 'BANK_WIRE_ADDRESS'));
		if (isset($config['BANK_WIRE_OWNER']))
			$this->owner = $config['BANK_WIRE_OWNER'];
		if (isset($config['BANK_WIRE_DETAILS']))
			$this->details = $config['BANK_WIRE_DETAILS'];
		if (isset($config['BANK_WIRE_ADDRESS']))
			$this->address = $config['BANK_WIRE_ADDRESS'];

		parent::__construct();

		$this->displayName = $this->l('Bank Wire');
		$this->description = $this->l('Accept payments by bank wire.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details?');
		if (!isset($this->owner) OR !isset($this->details) OR !isset($this->address))
			$this->warning = $this->l('Account owner and details must be configured in order to use this module correctly.');
		if (!sizeof(Currency::checkPaymentCurrencies($this->id)))
			$this->warning = $this->l('No currency set for this module');
	}

	public function install()
	{
		if (!parent::install() OR !$this->registerHook('payment') OR !$this->registerHook('paymentReturn'))
			return false;
		return true;
	}

	public function uninstall()
	{
		if (!Configuration::deleteByName('BANK_WIRE_DETAILS')
				OR !Configuration::deleteByName('BANK_WIRE_OWNER')
				OR !Configuration::deleteByName('BANK_WIRE_ADDRESS')
				OR !parent::uninstall())
			return false;
		return true;
	}

	private function _postValidation()
	{
		if (isset($_POST['btnSubmit']))
		{
			if (empty($_POST['details']))
				$this->_postErrors[] = $this->l('Account details are required.');
			elseif (empty($_POST['owner']))
				$this->_postErrors[] = $this->l('Account owner is required.');
		}
	}

	private function _postProcess()
	{
		if (isset($_POST['btnSubmit']))
		{
			Configuration::updateValue('BANK_WIRE_DETAILS', $_POST['details']);
			Configuration::updateValue('BANK_WIRE_OWNER', $_POST['owner']);
			Configuration::updateValue('BANK_WIRE_ADDRESS', $_POST['address']);
		}
		$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('ok').'" /> '.$this->l('Settings updated').'</div>';
	}

	private function _displayBankWire()
	{
		$this->_html .= '<img src="../modules/bankwire/bankwire.jpg" style="float:left; margin-right:15px;"><b>'.$this->l('This module allows you to accept payments by bank wire.').'</b><br /><br />
		'.$this->l('If the client chooses this payment mode, the order will change its status into a \'Waiting for payment\' status.').'<br />
		'.$this->l('Therefore, you must manually confirm the order as soon as you receive the wire.').'<br /><br /><br />';
	}

	private function _displayForm()
	{
		$this->_html .=
		'<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<fieldset>
			<legend><img src="../img/admin/contact.gif" />'.$this->l('Contact details').'</legend>
				<table border="0" width="500" cellpadding="0" cellspacing="0" id="form">
					<tr><td colspan="2">'.$this->l('Please specify the bank wire account details for customers').'.<br /><br /></td></tr>
					<tr><td width="130" style="height: 35px;">'.$this->l('Account owner').'</td><td><input type="text" name="owner" value="'.htmlentities(Tools::getValue('owner', $this->owner), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" /></td></tr>
					<tr>
						<td width="130" style="vertical-align: top;">'.$this->l('Details').'</td>
						<td style="padding-bottom:15px;">
							<textarea name="details" rows="4" cols="53">'.htmlentities(Tools::getValue('details', $this->details), ENT_COMPAT, 'UTF-8').'</textarea>
							<p>'.$this->l('Such as bank branch, IBAN number, BIC, etc.').'</p>
						</td>
					</tr>
					<tr>
						<td width="130" style="vertical-align: top;">'.$this->l('Bank address').'</td>
						<td style="padding-bottom:15px;">
							<textarea name="address" rows="4" cols="53">'.htmlentities(Tools::getValue('address', $this->address), ENT_COMPAT, 'UTF-8').'</textarea>
						</td>
					</tr>
					<tr><td colspan="2" align="center"><input class="button" name="btnSubmit" value="'.$this->l('Update settings').'" type="submit" /></td></tr>
				</table>
			</fieldset>
		</form>';
	}

	public function getContent()
	{
		$this->_html = '<h2>'.$this->displayName.'</h2>';

		if (!empty($_POST))
		{
			$this->_postValidation();
			if (!sizeof($this->_postErrors))
				$this->_postProcess();
			else
				foreach ($this->_postErrors AS $err)
					$this->_html .= '<div class="alert error">'. $err .'</div>';
		}
		else
			$this->_html .= '<br />';

		$this->_displayBankWire();
		$this->_displayForm();

		return $this->_html;
	}

	public function execPayment($cart)
	{
		if (!$this->active)
			return ;
		if (!$this->_checkCurrency($cart))
			Tools::redirectLink(__PS_BASE_URI__.'order.php');

		global $cookie, $smarty;

		$smarty->assign(array(
			'nbProducts' => $cart->nbProducts(),
			'cust_currency' => $cart->id_currency,
			'currencies' => $this->getCurrency((int)$cart->id_currency),
			'total' => $cart->getOrderTotal(true, Cart::BOTH),
			'this_path' => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));

		return $this->display(__FILE__, 'payment_execution.tpl');
	}

	public function hookPayment($params)
	{
		if (!$this->active)
			return ;
		if (!$this->_checkCurrency($params['cart']))
			return ;

		global $smarty;

		$smarty->assign(array(
			'this_path' => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));
		return $this->display(__FILE__, 'payment.tpl');
	}

	public function hookPaymentReturn($params)
	{
		if (!$this->active)
			return ;

		global $smarty;
		$state = $params['objOrder']->getCurrentState();
		if ($state == _PS_OS_BANKWIRE_ OR $state == _PS_OS_OUTOFSTOCK_)
			$smarty->assign(array(
				'total_to_pay' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false, false),
				'bankwireDetails' => nl2br2($this->details),
				'bankwireAddress' => nl2br2($this->address),
				'bankwireOwner' => $this->owner,
				'status' => 'ok',
				'id_order' => $params['objOrder']->id
			));
		else
			$smarty->assign('status', 'failed');
		return $this->display(__FILE__, 'payment_return.tpl');
	}
	
	private function _checkCurrency($cart)
	{
		$currency_order = new Currency((int)($cart->id_currency));
		$currencies_module = $this->getCurrency((int)$cart->id_currency);
		$currency_default = Configuration::get('PS_CURRENCY_DEFAULT');
		
		if (is_array($currencies_module))
			foreach ($currencies_module AS $currency_module)
				if ($currency_order->id == $currency_module['id_currency'])
					return true;
	}
}
