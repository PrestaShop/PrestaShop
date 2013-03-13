<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class Cheque extends PaymentModule
{
	private $_html = '';
	private $_postErrors = array();

	public $chequeName;
	public $address;
	public $extra_mail_vars;

	public function __construct()
	{
		$this->name = 'cheque';
		$this->tab = 'payments_gateways';
		$this->version = '2.3';
		$this->author = 'PrestaShop';

		$this->currencies = true;
		$this->currencies_mode = 'checkbox';

		$config = Configuration::getMultiple(array('CHEQUE_NAME', 'CHEQUE_ADDRESS'));
		if (isset($config['CHEQUE_NAME']))
			$this->chequeName = $config['CHEQUE_NAME'];
		if (isset($config['CHEQUE_ADDRESS']))
			$this->address = $config['CHEQUE_ADDRESS'];

		parent::__construct();

		$this->displayName = $this->l('Payments by check');
		$this->description = $this->l('This module allows you to accept payments by check.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete these details?');

		if ((!isset($this->chequeName) || !isset($this->address) || empty($this->chequeName) || empty($this->address)))
			$this->warning = $this->l('"To the order of" and "address" must be configured before using this module.');
		if (!count(Currency::checkPaymentCurrencies($this->id)))
			$this->warning = $this->l('No currency has been set for this module');
	
		$this->extra_mail_vars = array(
											'{cheque_name}' => Configuration::get('CHEQUE_NAME'),
											'{cheque_address}' => Configuration::get('CHEQUE_ADDRESS'),
											'{cheque_address_html}' => str_replace("\n", '<br />', Configuration::get('CHEQUE_ADDRESS'))
											);
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('payment') || !$this->registerHook('paymentReturn'))
			return false;
		return true;
	}

	public function uninstall()
	{
		if (!Configuration::deleteByName('CHEQUE_NAME') || !Configuration::deleteByName('CHEQUE_ADDRESS') || !parent::uninstall())
			return false;
		return true;
	}

	private function _postValidation()
	{
		if (Tools::isSubmit('btnSubmit'))
		{
			if (!Tools::getValue('name'))
				$this->_postErrors[] = $this->l('\'The "To the order of" field is required.');
			elseif (!Tools::getValue('address'))
				$this->_postErrors[] = $this->l('Address is required.');
		}
	}

	private function _postProcess()
	{
		if (Tools::isSubmit('btnSubmit'))
		{
			Configuration::updateValue('CHEQUE_NAME', Tools::getValue('name'));
			Configuration::updateValue('CHEQUE_ADDRESS', Tools::getValue('address'));
		}
		$this->_html .= '<div class="conf confirm"> '.$this->l('Settings updated').'</div>';
	}

	private function _displayCheque()
	{
		$this->_html .= '<img src="../modules/cheque/cheque.jpg" style="float:left; margin-right:15px;"><b>'.$this->l('This module allows you to accept payments by check.').'</b><br /><br />
		'.$this->l('If the client chooses this payment method, the order status will change to "Waiting for payment."').'<br />
		'.$this->l('You will need to manually confirm the order as soon as you receive a check.').'<br /><br /><br />';
	}

	private function _displayForm()
	{
		$this->_html .=
		'<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset>
			<legend><img src="../img/admin/contact.gif" />'.$this->l('Contact details').'</legend>
				<table border="0" width="500" cellpadding="0" cellspacing="0" id="form">
					<tr><td colspan="2">'.$this->l('Please specify the name and address required for the customer to send payment.').'.<br /><br /></td></tr>
					<tr><td width="130" style="height: 35px;">'.$this->l('To the order of').'</td><td><input type="text" name="name" value="'.Tools::htmlentitiesUTF8(Tools::getValue('name', $this->chequeName)).'" style="width: 300px;" /></td></tr>
					<tr>
						<td width="130" style="vertical-align: top;">'.$this->l('Address').'</td>
						<td><textarea name="address" rows="3" cols="53">'.Tools::htmlentitiesUTF8(Tools::getValue('address', $this->address)).'</textarea></td>
					</tr>
					<tr><td colspan="2" align="center"><br /><input class="button" name="btnSubmit" value="'.$this->l('Update settings').'" type="submit" /></td></tr>
				</table>
			</fieldset>
		</form>';
	}

	public function getContent()
	{
		$this->_html = '<h2>'.$this->displayName.'</h2>';

		if (Tools::isSubmit('btnSubmit'))
		{
			$this->_postValidation();
			if (!count($this->_postErrors))
				$this->_postProcess();
			else
				foreach ($this->_postErrors as $err)
					$this->_html .= '<div class="alert error">'.$err.'</div>';
		}
		else
			$this->_html .= '<br />';

		$this->_displayCheque();
		$this->_displayForm();

		return $this->_html;
	}

	public function hookPayment($params)
	{
		if (!$this->active)
			return;
		if (!$this->checkCurrency($params['cart']))
			return;

		$this->smarty->assign(array(
			'this_path' => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));
		return $this->display(__FILE__, 'payment.tpl');
	}

	public function hookPaymentReturn($params)
	{
		if (!$this->active)
			return;

		$state = $params['objOrder']->getCurrentState();
		if ($state == Configuration::get('PS_OS_CHEQUE') || $state == Configuration::get('PS_OS_OUTOFSTOCK'))
		{
			$this->smarty->assign(array(
				'total_to_pay' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
				'chequeName' => $this->chequeName,
				'chequeAddress' => Tools::nl2br($this->address),
				'status' => 'ok',
				'id_order' => $params['objOrder']->id
			));
			if (isset($params['objOrder']->reference) && !empty($params['objOrder']->reference))
				$this->smarty->assign('reference', $params['objOrder']->reference);
		}
		else
			$this->smarty->assign('status', 'failed');
		return $this->display(__FILE__, 'payment_return.tpl');
	}

	public function checkCurrency($cart)
	{
		$currency_order = new Currency((int)($cart->id_currency));
		$currencies_module = $this->getCurrency((int)$cart->id_currency);

		if (is_array($currencies_module))
			foreach ($currencies_module as $currency_module)
				if ($currency_order->id == $currency_module['id_currency'])
					return true;
		return false;
	}
}
