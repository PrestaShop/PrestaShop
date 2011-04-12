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

class GCheckout extends PaymentModule
{
    function __construct()
    {
        $this->name = 'gcheckout';
        $this->tab = 'payments_gateways';
        $this->version = 1.1;
		$this->author = 'PrestaShop';
		
		$this->currencies = true;
		$this->currencies_mode = 'radio';

        parent::__construct();

        $this->displayName = $this->l('Google Checkout');
        $this->description = $this->l('Google Checkout API implementation');
		
		if (!sizeof(Currency::checkPaymentCurrencies($this->id)))
			$this->warning = $this->l('No currency set for this module');
    }

    function install()
    {		
        if (!parent::install() OR !$this->registerHook('payment') OR !$this->registerHook('paymentReturn') OR !Configuration::updateValue('GCHECKOUT_MERCHANT_ID', '822305931131113') 
		OR !Configuration::updateValue('GCHECKOUT_MERCHANT_KEY', '2Lv_osMomVIocnLK0aif3A') OR !Configuration::updateValue('GCHECKOUT_LOGS', '1') OR !Configuration::updateValue('GCHECKOUT_MODE', 'real')
		OR !Configuration::updateValue('GCHECKOUT_NO_SHIPPING', '0'))
			return false;
		return true;
    }

    function uninstall()
    {
        return (parent::uninstall() AND Configuration::deleteByName('GCHECKOUT_MERCHANT_ID') AND Configuration::deleteByName('GCHECKOUT_MERCHANT_KEY') AND
		Configuration::deleteByName('GCHECKOUT_MODE') AND Configuration::deleteByName('GCHECKOUT_LOGS') AND Configuration::deleteByName('GCHECKOUT_NO_SHIPPING'));
    }
	
	function getContent()
	{
		global $currentIndex, $cookie;
		
		if (Tools::isSubmit('submitGoogleCheckout'))
		{
			$errors = array();
			if (($merchant_id = Tools::getValue('gcheckout_merchant_id')) AND preg_match('/[0-9]{15}/', $merchant_id))
				Configuration::updateValue('GCHECKOUT_MERCHANT_ID', $merchant_id);
			else
				$errors[] = '<div class="warning warn"><h3>'.$this->l('Merchant ID seems to be wrong').'</h3></div>';
			if (($merchant_key = Tools::getValue('gcheckout_merchant_key')) AND preg_match('/[a-zA-Z0-9_-]{22}/', $merchant_key))
				Configuration::updateValue('GCHECKOUT_MERCHANT_KEY', $merchant_key);
			else
				$errors[] = '<div class="warning warn"><h3>'.$this->l('Merchant key seems to be wrong').'</h3></div>';
			if ($mode = (Tools::getValue('gcheckout_mode') == 'real' ? 'real' : 'sandbox'))
				Configuration::updateValue('GCHECKOUT_MODE', $mode);
			if (Tools::getValue('gcheckout_logs'))
				Configuration::updateValue('GCHECKOUT_LOGS', 1);
			else
				Configuration::updateValue('GCHECKOUT_LOGS', 0);
			if (!sizeof($errors))
				Tools::redirectAdmin($currentIndex.'&configure=gcheckout&token='.Tools::getValue('token').'&conf=4');
			foreach ($errors as $error)
				echo $error;
		}
		
		$html = '<h2>'.$this->displayName.'</h2>
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<fieldset>
			<legend><img src="'.__PS_BASE_URI__.'modules/gcheckout/logo.gif" />'.$this->l('Settings').'</legend>
				<p>'.$this->l('Use the sandbox to test out the module then you can use the real mode if no problems arise. Remember to change your merchant key and ID according to the mode.').'</p>
				<label>
					'.$this->l('Mode').'
				</label>
				<div class="margin-form">
					<select name="gcheckout_mode">
						<option value="real"'.(Configuration::get('GCHECKOUT_MODE') == 'real' ? ' selected="selected"' : '').'>'.$this->l('Real').'&nbsp;&nbsp;</option>
						<option value="sandbox"'.(Configuration::get('GCHECKOUT_MODE') == 'sandbox' ? ' selected="selected"' : '').'>'.$this->l('Sandbox').'&nbsp;&nbsp;</option>
					</select>
				</div>
				<p>'.$this->l('You can find these keys in your Google Checkout account > Settings > Integration. Sandbox and real mode both have these keys.').'</p>
				<label>
					'.$this->l('Merchant ID').'
				</label>
				<div class="margin-form">
					<input type="text" name="gcheckout_merchant_id" value="'.Tools::getValue('gcheckout_merchant_id', Configuration::get('GCHECKOUT_MERCHANT_ID')).'" />
				</div>
				<label>
					'.$this->l('Merchant Key').'
				</label>
				<div class="margin-form">
					<input type="text" name="gcheckout_merchant_key" value="'.Tools::getValue('gcheckout_merchant_key', Configuration::get('GCHECKOUT_MERCHANT_KEY')).'" />
				</div>
				<p>'.$this->l('If you click this box, buyers will be able to see the shipping fees you have setup in Google Checkout on the purchase page.').'</p>
				<label>
					'.$this->l('Use Google shipping fees').'
				</label>
				<div class="margin-form" style="margin-top:5px">
					<input type="checkbox" name="gcheckout_no_shipping"'.(Tools::getValue('gcheckout_no_shipping', Configuration::get('GCHECKOUT_NO_SHIPPING')) ? ' checked="checked"' : '').' />
				</div>
				<p>'.$this->l('You can log the server-to-server communication. The log files are').' '.__PS_BASE_URI__.'modules/gcheckout/googleerror.log '.$this->l('and').' '.__PS_BASE_URI__.'modules/gcheckout/googlemessage.log. '.$this->l('If activated, be sure to protect them by putting a .htaccess file in the same directory. If not, they will be readable by everyone.').'</p>				
				<label>
					'.$this->l('Logs').'
				</label>
				<div class="margin-form" style="margin-top:5px">
					<input type="checkbox" name="gcheckout_logs"'.(Tools::getValue('gcheckout_logs', Configuration::get('GCHECKOUT_LOGS')) ? ' checked="checked"' : '').' />
				</div>
				<div class="clear center"><input type="submit" name="submitGoogleCheckout" class="button" value="'.$this->l('   Save   ').'" /></div>
			</fieldset>
		</form>
		<br /><br />
		<fieldset>
			<legend><img src="../img/admin/warning.gif" />'.$this->l('Information').'</legend>
			<p>- '.$this->l('In order to use your Google Checkout module, you must configure your Google Checkout account (sandbox account as well as live account). Log in to Google Checkout then go to Settings > Integration. The API callback URL is:').'<br />
				<b>'.Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/gcheckout/validation.php</b>
			</p>
			<p>- '.$this->l('The callback method must be set to').' <b>XML</b>.</p>
			<p>- '.$this->l('Orders must be placed with the same currency as your seller account. Carts in other currencies will be converted if the customer chooses to pay with this module.').'<p>
		</fieldset>';
		
		return $html;
	}

	function hookPayment($params)
	{
		if (!$this->active)
			return;

		global $smarty;
		
		$smarty->assign('buttonText', $this->l('Pay with GoogleCheckout'));
		return $this->display(__FILE__, 'payment.tpl');
	}
	
    function hookPaymentReturn($params)
    {
		if (!$this->active)
			return;

		return $this->display(__FILE__, 'payment_return.tpl');
    }
    
    function preparePayment()
    {
    	global $smarty, $cart, $cookie;
    	
    	require_once(dirname(__FILE__).'/library/googlecart.php');
		require_once(dirname(__FILE__).'/library/googleitem.php');
		require_once(dirname(__FILE__).'/library/googleshipping.php');
		
		$currency = $this->getCurrency((int)$cart->id_currency);
		if ($cart->id_currency != $currency->id)
		{
			$cart->id_currency = (int)$currency->id;
			$cookie->id_currency = (int)$cart->id_currency;
			$cart->update();
			Tools::redirect('modules/'.$this->name.'/payment.php');
		}
		$googleCart = new GoogleCart(Configuration::get('GCHECKOUT_MERCHANT_ID'), Configuration::get('GCHECKOUT_MERCHANT_KEY'), Configuration::get('GCHECKOUT_MODE'), $currency->iso_code);
		foreach ($cart->getProducts() AS $product)
			$googleCart->AddItem(new GoogleItem(utf8_decode($product['name'].((isset($product['attributes']) AND !empty($product['attributes'])) ? ' - '.$product['attributes'] : '')), utf8_decode($product['description_short']), (int)$product['cart_quantity'], $product['price_wt'], strtoupper(Configuration::get('PS_WEIGHT_UNIT')), (float)$product['weight'])); 
		if ($wrapping = $cart->getOrderTotal(true, Cart::ONLY_WRAPPING))
			$googleCart->AddItem(new GoogleItem(utf8_decode($this->l('Wrapping')), '', 1, $wrapping));
		foreach ($cart->getDiscounts() AS $voucher)
			$googleCart->AddItem(new GoogleItem(utf8_decode($voucher['name']), utf8_decode($voucher['description']), 1, '-'.$voucher['value_real']));
		
		if (!Configuration::get('GCHECKOUT_NO_SHIPPING'))
			$googleCart->AddShipping(new GooglePickUp($this->l('Shipping costs'), $cart->getOrderShippingCost($cart->id_carrier)));

		$googleCart->SetEditCartUrl(Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'order.php');
		$googleCart->SetContinueShoppingUrl(Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'order-confirmation.php');
		$googleCart->SetRequestBuyerPhone(false);
		$googleCart->SetMerchantPrivateData($cart->id.'|'.$cart->secure_key);
		$smarty->assign(array(
			'googleCheckoutExtraForm' => $googleCart->CheckoutButtonCode($this->l('Pay with GoogleCheckout'), 'LARGE'),
			'total' => $cart->getOrderTotal()
		));
    }
}
