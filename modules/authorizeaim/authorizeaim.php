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

class authorizeAIM extends PaymentModule
{
	public function __construct()
	{
		$this->name = 'authorizeaim';
		$this->tab = 'payments_gateways';
		$this->version = '1.0';
		$this->author = 'PrestaShop';
		$this->limited_countries = array('us');

        parent::__construct();

        $this->displayName = 'Authorize.net AIM (Advanced Integration Method)';
        $this->description = $this->l('Receive payment with Authorize.net');
	}

	public function install()
	{
		return (parent::install() AND $this->registerHook('orderConfirmation') AND $this->registerHook('payment') AND Configuration::updateValue('AUTHORIZE_AIM_DEMO', 1));
	}

	public function uninstall()
	{
		Configuration::deleteByName('AUTHORIZE_AIM_LOGIN_ID');
		Configuration::deleteByName('AUTHORIZE_AIM_KEY');
		Configuration::deleteByName('AUTHORIZE_AIM_DEMO');
		Configuration::deleteByName('AUTHORIZE_AIM_CARD_VISA');
		Configuration::deleteByName('AUTHORIZE_AIM_CARD_MASTERCARD');
		Configuration::deleteByName('AUTHORIZE_AIM_CARD_DISCOVER');
		Configuration::deleteByName('AUTHORIZE_AIM_CARD_AX');

		return parent::uninstall();
	}

	public function hookOrderConfirmation($params)
	{
		global $smarty; 

		if ($params['objOrder']->module != $this->name) 
			return;

		if ($params['objOrder']->getCurrentState() != _PS_OS_ERROR_) 
			$smarty->assign(array('status' => 'ok', 'id_order' => intval($params['objOrder']->id)));
		else
			$smarty->assign('status', 'failed');

		return $this->display(__FILE__, 'hookorderconfirmation.tpl'); 
	}

	public function getContent()
	{
		if (Tools::isSubmit('submitModule'))
		{
			Configuration::updateValue('AUTHORIZE_AIM_LOGIN_ID', Tools::getvalue('authorizeaim_login_id'));
			Configuration::updateValue('AUTHORIZE_AIM_KEY', Tools::getvalue('authorizeaim_key'));
			Configuration::updateValue('AUTHORIZE_AIM_DEMO', Tools::getvalue('authorizeaim_demo_mode'));
			Configuration::updateValue('AUTHORIZE_AIM_CARD_VISA', Tools::getvalue('authorizeaim_card_visa'));
			Configuration::updateValue('AUTHORIZE_AIM_CARD_MASTERCARD', Tools::getvalue('authorizeaim_card_mastercard'));
			Configuration::updateValue('AUTHORIZE_AIM_CARD_DISCOVER', Tools::getvalue('authorizeaim_card_discover'));
			Configuration::updateValue('AUTHORIZE_AIM_CARD_AX', Tools::getvalue('authorizeaim_card_ax'));

			echo $this->displayConfirmation($this->l('Configuration updated'));
		}

		return '
		<h2>'.$this->displayName.'</h2>
		<fieldset><legend><img src="../modules/'.$this->name.'/logo.gif" alt="" /> '.$this->l('Help').'</legend>
			<a href="http://www.authorize.net/signupnow/" style="float: right;"><img src="../modules/'.$this->name.'/logo_authorize.png" alt="" /></a>
			<h3>'.$this->l('In your PrestaShop admin panel').'</h3>
			- '.$this->l('Fill the Login ID field with the one provided by Authorize.net').'<br />
			- '.$this->l('Fill the key field with the transaction key provided by Authorize.net').'<br />
			<span style="color: red;" >- '.$this->l('Warning: Your website must possess a SSL certificate to use the Authorize.net AIM payment system. You are responsible for the safety of your customers\' bank information. PrestaShop could not be blamed in case of security shortage on your website.').'</span><br />
			<br />
		</fieldset><br />
		<form action="'.Tools::htmlentitiesutf8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset class="width2">
				<legend><img src="../img/admin/contact.gif" alt="" />'.$this->l('Settings').'</legend>
				<label for="authorizeaim_login_id">'.$this->l('Login ID').'</label>
				<div class="margin-form"><input type="text" size="20" id="authorizeaim_login_id" name="authorizeaim_login_id" value="'.Configuration::get('AUTHORIZE_AIM_LOGIN_ID').'" /></div>
				<label for="authorizeaim_key">'.$this->l('Key').'</label>
				<div class="margin-form"><input type="text" size="20" id="authorizeaim_login_id" name="authorizeaim_key" value="'.Configuration::get('AUTHORIZE_AIM_KEY').'" /></div>
				<label for="authorizeaim_demo_mode">'.$this->l('Mode:').'</label>
				<div class="margin-form" id="authorizeaim_demo">
					<input type="radio" name="authorizeaim_demo_mode" value="0" style="vertical-align: middle;" '.(!Tools::getValue('authorizeaim_demo_mode', Configuration::get('AUTHORIZE_AIM_DEMO')) ? 'checked="checked"' : '').' />
					<span style="color: #080;">'.$this->l('Production').'</span>
					<input type="radio" name="authorizeaim_demo_mode" value="1" style="vertical-align: middle;" '.(Tools::getValue('authorizeaim_demo_mode', Configuration::get('AUTHORIZE_AIM_DEMO')) ? 'checked="checked"' : '').' />
					<span style="color: #900;">'.$this->l('Test').'</span>
				</div>
				<label for="authorizeaim_cards">'.$this->l('Cards:').'</label>
				<div class="margin-form" id="authorizeaim_cards">
					<input type="checkbox" name="authorizeaim_card_visa" '.(Configuration::get('AUTHORIZE_AIM_CARD_VISA') ? 'checked="checked"' : '').' />
						<img src="../modules/'.$this->name.'/cards/visa.gif" alt="visa" />
					<input type="checkbox" name="authorizeaim_card_mastercard" '.(Configuration::get('AUTHORIZE_AIM_CARD_MASTERCARD') ? 'checked="checked"' : '').' />
						<img src="../modules/'.$this->name.'/cards/mastercard.gif" alt="visa" />
					<input type="checkbox" name="authorizeaim_card_discover" '.(Configuration::get('AUTHORIZE_AIM_CARD_DISCOVER') ? 'checked="checked"' : '').' />
						<img src="../modules/'.$this->name.'/cards/discover.gif" alt="visa" />
					<input type="checkbox" name="authorizeaim_card_ax" '.(Configuration::get('AUTHORIZE_AIM_CARD_AX') ? 'checked="checked"' : '').' />
						<img src="../modules/'.$this->name.'/cards/ax.gif" alt="visa" />
				</div>
				<br /><center><input type="submit" name="submitModule" value="'.$this->l('Update settings').'" class="button" /></center>
			</fieldset>
		</form>';
	}

	public function hookPayment($params)
	{
		global $smarty;

		if (!empty($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) != 'off' AND Configuration::get('PS_SSL_ENABLED'))
		{
			$invoiceAddress = new Address((int)$params['cart']->id_address_invoice);

			$authorizeAIMParams = array();
			$authorizeAIMParams['x_login'] = Configuration::get('AUTHORIZE_AIM_LOGIN_ID');
			$authorizeAIMParams['x_tran_key'] = Configuration::get('AUTHORIZE_AIM_KEY');
			$authorizeAIMParams['x_version'] = '3.1';
			$authorizeAIMParams['x_delim_data'] = 'TRUE';
			$authorizeAIMParams['x_delim_char'] = '|';
			$authorizeAIMParams['x_relay_response'] = 'FALSE';
			$authorizeAIMParams['x_type'] = 'AUTH_CAPTURE';
			$authorizeAIMParams['x_method'] = 'CC';
			$authorizeAIMParams['x_test_request'] = Configuration::get('AUTHORIZE_AIM_DEMO');
			$authorizeAIMParams['x_invoice_num'] = (int)$params['cart']->id;
			$authorizeAIMParams['x_amount'] = number_format($params['cart']->getOrderTotal(true, 3), 2, '.', '');
			$authorizeAIMParams['x_address'] = $invoiceAddress->address1.' '.$invoiceAddress->address2;
			$authorizeAIMParams['x_zip'] = $invoiceAddress->postcode;
			$isFailed = Tools::getValue('aimerror');

			$cards = array();
			$cards['visa'] = Configuration::get('AUTHORIZE_AIM_CARD_VISA') == 'on' ? 1 : 0;
			$cards['mastercard'] = Configuration::get('AUTHORIZE_AIM_CARD_MASTERCARD') == 'on' ? 1 : 0;
			$cards['discover'] = Configuration::get('AUTHORIZE_AIM_CARD_DISCOVER') == 'on' ? 1 : 0;
			$cards['ax'] = Configuration::get('AUTHORIZE_AIM_CARD_AX') == 'on' ? 1 : 0;

			$smarty->assign('p', $authorizeAIMParams);
			$smarty->assign('cards', $cards);
			$smarty->assign('isFailed', $isFailed);

			return $this->display(__FILE__, 'authorizeaim.tpl');
		}
    }
}
?>
