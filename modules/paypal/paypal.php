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

define('_PAYPAL_INTEGRAL_', 0);
define('_PAYPAL_OPTION_PLUS_', 1);
define('_PAYPAL_INTEGRAL_EVOLUTION_', 2);

class PayPal extends PaymentModule
{
	private $_html = '';
	
	public function __construct()
	{
		$this->name = 'paypal';
		$this->tab = 'payments_gateways';
		$this->version = '2.4';
		
		$this->currencies = true;
		$this->currencies_mode = 'radio';

        parent::__construct();

        $this->_errors = array();
		$this->page = basename(__FILE__, '.php');
        $this->displayName = $this->l('PayPal');
        $this->description = $this->l('Accepts payments by credit cards (CB, Visa, MasterCard, Amex, Aurore, Cofinoga, 4 stars) with PayPal.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details?');
		if (Configuration::get('PAYPAL_BUSINESS') == 'paypal@prestashop.com')
			$this->warning = $this->l('You are currently using the default PayPal e-mail address, please enter your own e-mail address.');
		$this->_checkAndUpdateFromOldVersion();
		if (file_exists(_PS_ROOT_DIR_.'/modules/paypalapi/paypalapi.php') AND $this->active)
			$this->warning = $this->l('In order to REMOVE this warning, please uninstall and remove the PayPalAPI module.');

		global $cookie;
		$context = stream_context_create(array('http' => array('method'=>"GET", 'timeout' => 5)));
		$content = @file_get_contents('https://www.prestashop.com/partner/preactivation/preactivation-warnings.php?version=1.0&partner=paypal&iso_country='.Tools::strtolower(Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT'))).'&iso_lang='.Tools::strtolower(Language::getIsoById(intval($cookie->id_lang))).'&id_lang='.(int)$cookie->id_lang.'&email='.urlencode(Configuration::get('PS_SHOP_EMAIL')).'&security='.md5(Configuration::get('PS_SHOP_EMAIL')._COOKIE_IV_), false, $context);
		$content = explode('|', $content);
		if ($content[0] == 'OK')
		{
			if (!empty($this->warning))
				$this->warning .= ', ';
			$this->warning .= $content[1];
		}
	}
	
	public function install()
	{
		/* Install and register on hook */
		if (!parent::install()
			OR !$this->registerHook('payment')
			OR !$this->registerHook('paymentReturn')
			OR !$this->registerHook('shoppingCartExtra')
			OR !$this->registerHook('backBeforePayment')
			OR !$this->registerHook('paymentReturn')
			OR !$this->registerHook('rightColumn')
			OR !$this->registerHook('cancelProduct')
			OR !$this->registerHook('adminOrder'))
			return false;
		
		if (file_exists(_PS_ROOT_DIR_.'/modules/paypalapi/paypalapi.php') AND !Configuration::get('PAYPAL_NEW'))
		{
			include_once(_PS_ROOT_DIR_.'/modules/paypalapi/paypalapi.php');
			$paypalapi = new PaypalAPI();
			return $this->_checkAndUpdateFromOldVersion(true);
		}
		
		/* Set database */
		if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paypal_order` (
		  `id_order` int(10) unsigned NOT NULL,
		  `id_transaction` varchar(255) NOT NULL,
		  `payment_method` int(10) unsigned NOT NULL,
		  `payment_status` varchar(255) NOT NULL,
		  `capture` int(10) unsigned NOT NULL,
		  PRIMARY KEY (`id_order`)
		) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8'))
			return false;
		
		/* Set configuration */
		Configuration::updateValue('PAYPAL_SANDBOX', 1);
		Configuration::updateValue('PAYPAL_BUSINESS', 'paypal@prestashop.com');
		Configuration::updateValue('PAYPAL_HEADER', '');
		Configuration::updateValue('PAYPAL_API_USER', '');
		Configuration::updateValue('PAYPAL_API_PASSWORD', '');
		Configuration::updateValue('PAYPAL_API_SIGNATURE', '');
		Configuration::updateValue('PAYPAL_EXPRESS_CHECKOUT', 0);
		Configuration::updateValue('PAYPAL_CAPTURE', 0);
		Configuration::updateValue('PAYPAL_PAYMENT_METHOD', _PAYPAL_INTEGRAL_);
		Configuration::updateValue('PAYPAL_TEMPLATE', 'A');
		Configuration::updateValue('PAYPAL_NEW', 1);
		Configuration::updateValue('PAYPAL_DEBUG_MODE', 0);

		if (!Configuration::get('PAYPAL_OS_AUTHORIZATION'))
		{
			$orderState = new OrderState();
			$orderState->name = array();
			foreach (Language::getLanguages() AS $language)
			{
				if (strtolower($language['iso_code']) == 'fr')
					$orderState->name[$language['id_lang']] = 'Autorisation acceptée par PayPal';
				else
					$orderState->name[$language['id_lang']] = 'Authorization accepted from PayPal';
			}
			$orderState->send_email = false;
			$orderState->color = '#DDEEFF';
			$orderState->hidden = false;
			$orderState->delivery = false;
			$orderState->logable = true;
			$orderState->invoice = true;
			if ($orderState->add())
				copy(dirname(__FILE__).'/../../img/os/'._PS_OS_PAYPAL_.'.gif', dirname(__FILE__).'/../../img/os/'.(int)($orderState->id).'.gif');
			Configuration::updateValue('PAYPAL_OS_AUTHORIZATION', (int)($orderState->id));
		}
		
		return true;
	}
	
	public function uninstall()
	{
		/* Delete all configurations */
		Configuration::deleteByName('PAYPAL_SANDBOX');
		Configuration::deleteByName('PAYPAL_BUSINESS');
		Configuration::deleteByName('PAYPAL_HEADER');
		Configuration::deleteByName('PAYPAL_API_USER');
		Configuration::deleteByName('PAYPAL_API_PASSWORD');
		Configuration::deleteByName('PAYPAL_API_SIGNATURE');
		Configuration::deleteByName('PAYPAL_EXPRESS_CHECKOUT');
		Configuration::deleteByName('PAYPAL_PAYMENT_METHOD');
		Configuration::deleteByName('PAYPAL_TEMPLATE');
		Configuration::deleteByName('PAYPAL_CAPTURE');
		Configuration::deleteByName('PAYPAL_DEBUG_MODE');
		
		return parent::uninstall();
	}
	
	public function getContent()
	{
		$this->_html .= '<h2>'.$this->l('PayPal').'</h2>';	
	
		$this->_postProcess();
		$this->_setPayPalSubscription();
		if (file_exists(_PS_ROOT_DIR_.'/modules/paypalapi/paypalapi.php'))
			$this->_html .= '<div class="warning warn"><h3>'.$this->l('Please do not use, and remove PayPalAPI module.').'</h3></div>';
		$this->_setConfigurationForm();
		
		return $this->_html;
	}
	
	public function hookPayment($params)
	{
		global $smarty;
		
		if (!$this->active)
			return ;
		/*
		 * PAYMENT METHOD:
		 * 0: Integral
		 * 1: Option +
		 * 2: Integral Evolution
		 */
		if (Configuration::get('PAYPAL_PAYMENT_METHOD') == _PAYPAL_INTEGRAL_EVOLUTION_)
			return $this->display(__FILE__, 'integral_evolution/paypal.tpl');
		elseif (Configuration::get('PAYPAL_PAYMENT_METHOD') == _PAYPAL_INTEGRAL_ OR Configuration::get('PAYPAL_PAYMENT_METHOD') == _PAYPAL_OPTION_PLUS_)
		{
			if ($this->_isPayPalAPIAvailable())
			{
				$smarty->assign('integral', (Configuration::get('PAYPAL_PAYMENT_METHOD') == 0 ? 1 : 0));
				$smarty->assign('logo', _MODULE_DIR_.$this->name.'/paypal.gif');
				return $this->display(__FILE__, 'payment/payment.tpl');
			}
			else
				return $this->display(__FILE__, 'standard/paypal.tpl');
		}
		else
			die($this->l('No valid payment method selected'));
	}
	
	public function hookShoppingCartExtra($params)
	{
		global $cookie, $smarty;

		if (!$this->active)
			return ;

		if (Configuration::get('PAYPAL_EXPRESS_CHECKOUT') AND !$cookie->isLogged(true) AND $this->_isPayPalAPIAvailable())
		{
			$smarty->assign('logo', $this->getLogo(true));
			return $this->display(__FILE__, 'express/shopping_cart.tpl');
		}
	}
	
	public function hookPaymentReturn($params)
	{
		if (!$this->active)
			return ;

		return $this->display(__FILE__, 'confirmation.tpl');
	}
	
	public function hookRightColumn($params)
	{
		global $smarty, $cookie;
	 
		$smarty->assign('iso_code', Tools::strtolower(Language::getIsoById($cookie->id_lang ? (int)($cookie->id_lang) : 1)));
		$smarty->assign('logo', $this->getLogo(false, true));
		return $this->display(__FILE__, 'column.tpl');
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}
	
	public function hookBackBeforePayment($params)
	{
		if (!$this->active)
			return ;

		/* Only execute if you use PayPal API for payment */
		if (Configuration::get('PAYPAL_PAYMENT_METHOD') != _PAYPAL_INTEGRAL_EVOLUTION_ AND $this->_isPayPalAPIAvailable())
		{
			global $cookie;
	
			if ($params['module'] != $this->name)
				return false;
			if (!$token = $cookie->paypal_token)
				return false;
			if (!$payerID = $cookie->paypal_payer_id)
				return false;
			Tools::redirect('modules/paypal/express/submit.php?confirm=1&token='.$token.'&payerID='.$payerID);
		}
	}
	
	public function hookAdminOrder($params)
	{
		if (Tools::isSubmit('paypal'))
		{
			switch (Tools::getValue('paypal'))
			{
				case 'captureOk':
					$message = $this->l('Funds have been recovered.');
					break;
				case 'captureError':
					$message = $this->l('Recovery of funds request unsuccessful. Please see log message!');
					break;
				case 'validationOk':
					$message = $this->l('Validation successful. Please see log message!');
					break;
				case 'refundOk':
					$message = $this->l('Refund has been made.');
					break;
				case 'refundError':
					$message = $this->l('Refund request unsuccessful. Please see log message!');
					break;
			}
			if (isset($message) AND $message)
				$this->_html .= '
				<br />
				<div class="module_confirmation conf confirm" style="width: 400px;">
					<img src="'._PS_IMG_.'admin/ok.gif" alt="" title="" /> '.$message.'
				</div>
				';
		}
		
		if ($this->_needValidation((int)($params['id_order'])) AND $this->_isPayPalAPIAvailable())
		{
			$this->_html .= '
			<br />
			<fieldset style="width:400px;">
				<legend><img src="'._MODULE_DIR_.$this->name.'/logo.gif" alt="" /> '.$this->l('PayPal Validation').'</legend>
				<p><b>'.$this->l('Information:').'</b> '.(OrderHistory::getLastOrderState((int)($params['id_order']))->id == (int)(Configuration::get('PAYPAL_OS_AUTHORIZATION')) ? $this->l('Pending Capture - No shipping') : $this->l('Pending Payment - No shipping')).'</p>
				<form method="post" action="'.$_SERVER['REQUEST_URI'].'">
					<input type="hidden" name="id_order" value="'.$params['id_order'].'" />
					<p class="center"><input type="submit" class="button" name="submitPayPalValidation" value="'.$this->l('Get payment status').'" /></p>
				</form>
			';
			$this->_postProcess();
			$this->_html .= '</fieldset>
			';
		}
		
		if ($this->_needCapture((int)($params['id_order'])) AND $this->_isPayPalAPIAvailable())
		{
			$this->_html .= '
			<br />
			<fieldset style="width:400px;">
				<legend><img src="'._MODULE_DIR_.$this->name.'/logo.gif" alt="" /> '.$this->l('PayPal Capture').'</legend>
				<p><b>'.$this->l('Information:').'</b> '.$this->l('Funds ready to be captured before shipping.').'</p>
				<form method="post" action="'.$_SERVER['REQUEST_URI'].'">
					<input type="hidden" name="id_order" value="'.$params['id_order'].'" />
					<p class="center"><input type="submit" class="button" name="submitPayPalCapture" value="'.$this->l('Get the money.').'" /></p>
				</form>
			';
			$this->_postProcess();
			$this->_html .= '</fieldset>
			';
		}
		
		if ($this->_canRefund((int)($params['id_order'])) AND $this->_isPayPalAPIAvailable())
		{
			$this->_html .= '
			<br />
			<fieldset style="width:400px;">
				<legend><img src="'._MODULE_DIR_.$this->name.'/logo.gif" alt="" /> '.$this->l('PayPal Refund').'</legend>
				<p><b>'.$this->l('Information:').'</b> '.$this->l('Payment accepted').'</p>
				<p><b>'.$this->l('Information:').'</b> '.$this->l('When you refund a product, a partial refund is made unless you select "Generate a voucher".').'</p>
				<form method="post" action="'.$_SERVER['REQUEST_URI'].'">
					<input type="hidden" name="id_order" value="'.(int)($params['id_order']).'" />
					<p class="center"><input type="submit" class="button" name="submitPayPalRefund" value="'.$this->l('Refund total transaction').'" onclick="if(!confirm(\''.$this->l('Are you sure?').'\'))return false;" /></p>
				</form>
			';
			$this->_postProcess();
			$this->_html .= '</fieldset>
			';
		}
		
		return $this->_html;
	}
	
	public function hookCancelProduct($params)
	{
		if (Tools::isSubmit('generateDiscount'))
			return false;
		if (!$this->_isPayPalAPIAvailable())
			return false;
		if ($params['order']->module != $this->name)
			return false;
		if (!($order = $params['order']) OR !Validate::isLoadedObject($order))
			return false;
		if (!$order->hasBeenPaid())
			return false;
		if (!($order_detail = new OrderDetail((int)($params['id_order_detail']))) OR !Validate::isLoadedObject($order_detail))
			return false;

		$id_transaction = $this->_getTransactionId((int)($order->id));
		if (!$id_transaction)
			return false;
		
		$products = $order->getProducts();
		$amt = $products[(int)($order_detail->id)]['product_price_wt'] * (int)($_POST['cancelQuantity'][(int)($order_detail->id)]);
		
		$response = $this->_makeRefund($id_transaction, (float)($amt));
		$message = $this->l('Cancel products result:').'<br>';
		foreach ($response AS $k => $value)
			$message .= $k.': '.$value.'<br>';
		$this->_addNewPrivateMessage((int)($order->id), $message);
	}

	public function makePayPalAPIValidation($cookie, $cart, $id_currency, $payerID, $type)
	{
		global $cookie;

		if (!$this->active)
			return ;
		if (!$this->_isPayPalAPIAvailable())
			return ;

		// Filling-in vars
		$id_cart = (int)($cart->id);
		$currency = new Currency((int)($id_currency));
		$iso_currency = $currency->iso_code;
		$token = $cookie->paypal_token;
		$total = (float)($cart->getOrderTotal(true, Cart::BOTH));
		$paymentType = Configuration::get('PAYPAL_CAPTURE') == 1 ? 'Authorization' : 'Sale';
		$serverName = urlencode($_SERVER['SERVER_NAME']);
		$bn = ($type == 'express' ? 'ECS' : 'ECM');
		$notifyURL = urlencode(Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/paypal/ipn.php');

		// Getting address
		if (isset($cookie->id_cart) AND $cookie->id_cart)
			$cart = new Cart((int)($cookie->id_cart));
		if (isset($cart->id_address_delivery) AND $cart->id_address_delivery)
			$address = new Address((int)($cart->id_address_delivery));
		$requestAddress = '';
		if (Validate::isLoadedObject($address))
		{
			$country = new Country((int)($address->id_country));
			$state = new State((int)($address->id_state));
			$requestAddress = '&SHIPTONAME='.urlencode($address->company.' '.$address->firstname.' '.$address->lastname).'&SHIPTOSTREET='.urlencode($address->address1.' '.$address->address2).'&SHIPTOCITY='.urlencode($address->city).'&SHIPTOSTATE='.urlencode($state->iso_code).'&SHIPTOCOUNTRYCODE='.urlencode($country->iso_code).'&SHIPTOZIP='.urlencode($address->postcode);
		}

		// Making request
		$request='&TOKEN='.urlencode($token).'&PAYERID='.urlencode($payerID).'&PAYMENTACTION='.$paymentType.'&AMT='.$total.'&CURRENCYCODE='.$iso_currency.'&IPADDRESS='.$serverName.'&NOTIFYURL='.$notifyURL.'&BUTTONSOURCE=PRESTASHOP_'.$bn.$requestAddress ;

		// Calling PayPal API
		include_once(_PS_MODULE_DIR_.'paypal/api/paypallib.php');
		$ppAPI = new PaypalLib();
		$result = $ppAPI->makeCall($this->getAPIURL(), $this->getAPIScript(), 'DoExpressCheckoutPayment', $request);
		$this->_logs = array_merge($this->_logs, $ppAPI->getLogs());

		// Checking PayPal result
		if (!is_array($result) OR !sizeof($result))
			$this->displayPayPalAPIError($this->l('Authorization to PayPal failed.'), $this->_logs);
		elseif (!isset($result['ACK']) OR  strtoupper($result['ACK']) != 'SUCCESS')
			$this->displayPayPalAPIError($this->l('PayPal return error.'), $this->_logs);
		elseif (!isset($result['TOKEN']) OR $result['TOKEN'] != $cookie->paypal_token)
		{
			$logs[] = '<b>'.$ppExpress->l('Token given by PayPal is not the same as the cookie token', 'submit').'</b>';
			$ppExpress->displayPayPalAPIError($ppExpress->l('PayPal return error.', 'submit'), $logs);
		}

		// Making log
		$id_transaction = $result['TRANSACTIONID'];
		if (Configuration::get('PAYPAL_CAPTURE'))
			$this->_logs[] = $this->l('Authorization for deferred payment granted by PayPal.');
		else
			$this->_logs[] = $this->l('Order finished with PayPal!');
		$message = Tools::htmlentitiesUTF8(strip_tags(implode("\n", $this->_logs)));

		// Order status
		switch ($result['PAYMENTSTATUS'])
		{
			case 'Completed':
				$id_order_state = _PS_OS_PAYMENT_;
				break;
			case 'Pending':
				if ($result['PENDINGREASON'] != 'authorization')
					$id_order_state = _PS_OS_PAYPAL_;
				else
					$id_order_state = (int)(Configuration::get('PAYPAL_OS_AUTHORIZATION'));
				break;
			default:
				$id_order_state = _PS_OS_ERROR_;
		}

		// Call payment validation method
		$this->validateOrder($id_cart, $id_order_state, (float)($cart->getOrderTotal(true, Cart::BOTH)), $this->displayName, $message, array('transaction_id' => $id_transaction, 'payment_status' => $result['PAYMENTSTATUS'], 'pending_reason' => $result['PENDINGREASON']), $id_currency, false, $cart->secure_key);
		
		// Clean cookie
		unset($cookie->paypal_token);
		
		// Displaying output
		$order = new Order((int)($this->currentOrder));
		Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?id_cart='.(int)($id_cart).'&id_module='.(int)($this->id).'&id_order='.(int)($this->currentOrder).'&key='.$order->secure_key);
		
	}
	
	public function validateOrder($id_cart, $id_order_state, $amountPaid, $paymentMethod = 'Unknown', $message = NULL, $extraVars = array(), $currency_special = NULL, $dont_touch_amount = false, $secure_key = false)
	{
		if (!$this->active)
			return ;

		parent::validateOrder($id_cart, $id_order_state, $amountPaid, $paymentMethod, $message, $extraVars, $currency_special, $dont_touch_amount, $secure_key);
		if (array_key_exists('transaction_id', $extraVars) AND array_key_exists('payment_status', $extraVars))
			$this->_saveTransaction($id_cart, $extraVars);
	}
	
	public function getPayPalURL()
	{
		return 'www'.(Configuration::get('PAYPAL_SANDBOX') ? '.sandbox' : '').'.paypal.com';
	}
	
	public function getPaypalIntegralEvolutionUrl()
	{
		if (Configuration::get('PAYPAL_SANDBOX'))
			return 'https://'.$this->getPayPalURL().'/cgi-bin/acquiringweb';
		return 'https://securepayments.paypal.com/acquiringweb?cmd=_hosted-payment';
	}
	
	public function getPaypalStandardUrl()
	{
		return 'https://'.$this->getPayPalURL().'/cgi-bin/webscr';
	}
	
	public function getAPIURL()
	{
		return 'api-3t'.(Configuration::get('PAYPAL_SANDBOX') ? '.sandbox' : '').'.paypal.com';
	}
	
	public function getAPIScript()
	{
		return '/nvp';
	}
	
	public function getL($key)
	{
		$translations = array(
			'mc_gross' => $this->l('Paypal key \'mc_gross\' not specified, can\'t control amount paid.'),
			'payment_status' => $this->l('Paypal key \'payment_status\' not specified, can\'t control payment validity'),
			'payment' => $this->l('Payment: '),
			'custom' => $this->l('Paypal key \'custom\' not specified, cannot relay to cart'),
			'txn_id' => $this->l('Paypal key \'txn_id\' not specified, transaction unknown'),
			'mc_currency' => $this->l('Paypal key \'mc_currency\' not specified, currency unknown'),
			'cart' => $this->l('Cart not found'),
			'order' => $this->l('Order has already been placed'),
			'transaction' => $this->l('Paypal Transaction ID: '),
			'verified' => $this->l('The PayPal transaction could not be VERIFIED.'),
			'connect' => $this->l('Problem connecting to the PayPal server.'),
			'nomethod' => $this->l('No communications transport available.'),
			'socketmethod' => $this->l('Verification failure (using fsockopen). Returned: '),
			'curlmethod' => $this->l('Verification failure (using cURL). Returned: '),
			'curlmethodfailed' => $this->l('Connection using cURL failed'),
			'Please wait, redirecting to Paypal... Thanks.' => $this->l('Please wait, redirecting to Paypal... Thanks.'),
			'Cancel' => $this->l('Cancel'),
			'My cart' => $this->l('My cart'),
			'Return to shop' => $this->l('Return to shop'),
			'Paypal error: (invalid or undefined business account e-mail)' => $this->l('Paypal error: (invalid or undefined business account e-mail)'),
			'Paypal error: (invalid address or customer)' => $this->l('Paypal error: (invalid address or customer)')
		);
		return $translations[$key];
	}
	
	public function getLogo($ppExpress = false, $vertical = false)
	{
		global $cookie;

		if ($ppExpress)
		{
			$iso_code = Tools::strtoupper(Language::getIsoById($cookie->id_lang ? (int)($cookie->id_lang) : 1));
			$logo = array(
				'FR' => _MODULE_DIR_.$this->name.'/img/FR_pp_express.gif',
				'DE' => _MODULE_DIR_.$this->name.'/img/DE_pp_express.gif',
				'US' => _MODULE_DIR_.$this->name.'/img/US_pp_express.gif',
				'GB' => _MODULE_DIR_.$this->name.'/img/UK_pp_express.gif',
				'ES' => _MODULE_DIR_.$this->name.'/img/ES_pp_express.gif',
				'IT' => _MODULE_DIR_.$this->name.'/img/IT_pp_express.gif',
				'PL' => _MODULE_DIR_.$this->name.'/img/PL_pp_express.gif',
				'NL' => _MODULE_DIR_.$this->name.'/img/NL_pp_express.gif',
				'AU' => _MODULE_DIR_.$this->name.'/img/AU_pp_express.gif',
				'CA' => _MODULE_DIR_.$this->name.'/img/CA_pp_express.gif',
				'CN' => _MODULE_DIR_.$this->name.'/img/CN_pp_express.gif',
				'JP' => _MODULE_DIR_.$this->name.'/img/JP_pp_express.gif'
			);
			if (isset($logo[$iso_code]))
				return $logo[$iso_code];
			return $logo['US'];
		}

		if (Configuration::get('PAYPAL_PAYMENT_METHOD') == _PAYPAL_INTEGRAL_)
		{
			$country_code = $this->getCountryCode();
			$logo = array(
				'FR' => _MODULE_DIR_.$this->name.'/img/FR_pp_integral.gif',
				'DE' => _MODULE_DIR_.$this->name.'/img/DE_pp_integral.gif',
				'US' => _MODULE_DIR_.$this->name.'/img/US_pp_integral.gif',
				'GB' => _MODULE_DIR_.$this->name.'/img/UK_pp_integral.gif',
				'ES' => _MODULE_DIR_.$this->name.'/img/ES_pp_integral.gif',
				'IT' => _MODULE_DIR_.$this->name.'/img/IT_pp_integral.gif',
				'PL' => _MODULE_DIR_.$this->name.'/img/PL_pp_integral.gif',
				'NL' => _MODULE_DIR_.$this->name.'/img/NL_pp_integral.gif',
				'AU' => _MODULE_DIR_.$this->name.'/img/AU_pp_integral.gif',
				'CA' => _MODULE_DIR_.$this->name.'/img/CA_pp_integral.gif',
				'CN' => _MODULE_DIR_.$this->name.'/img/CN_pp_integral.gif',
				'JP' => _MODULE_DIR_.$this->name.'/img/JP_pp_integral.gif',
				'FR_vertical' => _MODULE_DIR_.$this->name.'/img/vertical_FR_large.png',
				'US_vertical' => _MODULE_DIR_.$this->name.'/img/vertical_US_large.png'
			);
			if (isset($logo[$country_code.($vertical ? '_vertical' : '')]))
				return $logo[$country_code.($vertical ? '_vertical' : '')];
			return ($vertical ? $logo['US_vertical'] : $logo['US']);
		}
		elseif (Configuration::get('PAYPAL_PAYMENT_METHOD') == _PAYPAL_INTEGRAL_EVOLUTION_)
			return _MODULE_DIR_.$this->name.'/img/integral_evolution'.($vertical ? '_vertical' : '').'.png';
		else
			return _MODULE_DIR_.$this->name.'/img/PayPal_mark_60x38.gif';
	}
	
	public function getCountryCode()
	{
		global $cookie;

		$cart = new Cart((int)($cookie->id_cart));
		$address = new Address((int)($cart->id_address_invoice));
		$country = new Country((int)($address->id_country));
		return $country->iso_code;
	}
	
	public function displayPayPalAPIError($message, $log = false)
	{
		global $cookie, $smarty;

		$send = true;
		// Sanitinize log
		foreach ($log AS $key => $string)
			if ($string == 'ACK -> Success')
				$send = false;
			elseif (substr($string, 0, 6) == 'METHOD')
			{
				$values = explode('&', $string);
				foreach ($values AS $key2 => $value)
				{
					$values2 = explode('=', $value);
					foreach ($values2 AS $key3 => $value2)
						if ($value2 == 'PWD' || $value2 == 'SIGNATURE')
							$values2[$key3 + 1] = '*********';
					$values[$key2] = implode('=', $values2);
				}
				$log[$key] = implode('&', $values);
			}

		include(dirname(__FILE__).'/../../header.php');
		$smarty->assign('message', $message);
		$smarty->assign('logs', $log);
		$data = array('{logs}' => implode('<br />', $log));
		if ($send)
			Mail::Send((int)($cookie->id_lang), 'error_reporting', Mail::l('Error reporting from your PayPal module'), $data, Configuration::get('PS_SHOP_EMAIL'), NULL, NULL, NULL, NULL, NULL, _PS_MODULE_DIR_.$this->name.'/mails/');
		echo $this->display(__FILE__, 'error.tpl');
		include_once(dirname(__FILE__).'/../../footer.php');
		die ;
	}
	
	private function _saveTransaction($id_cart, $extraVars)
	{
		$cart = new Cart((int)($id_cart));
		if (Validate::isLoadedObject($cart) AND $cart->OrderExists())
		{
			$id_order = Db::getInstance()->getValue('
			SELECT `id_order` 
			FROM `'._DB_PREFIX_.'orders` 
			WHERE `id_cart` = '.(int)($cart->id));
			
			Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'paypal_order` (`id_order`, `id_transaction`, `payment_method`, `payment_status`, `capture`) 
			VALUES ('.(int)($id_order).', \''.pSQL($extraVars['transaction_id']).'\', '.(int)(Configuration::get('PAYPAL_PAYMENT_METHOD')).', \''.pSQL($extraVars['payment_status']).((isset($extraVars['pending_reason']) AND $extraVars['pending_reason'] == 'authorization') ? '_authorization' : '').'\', '.(int)(Configuration::get('PAYPAL_CAPTURE')).')');
		}
	}
	
	private function _canRefund($id_order)
	{
		if (!(int)($id_order))
			return false;
		$paypal_order = Db::getInstance()->getRow('
		SELECT * 
		FROM `'._DB_PREFIX_.'paypal_order` 
		WHERE `id_order` = '.(int)($id_order));
		if (!is_array($paypal_order) OR !sizeof($paypal_order))
			return false;
		if ($paypal_order['payment_status'] != 'Completed' OR $paypal_order['capture'] != 0)
			return false;
		return true;
	}
	
	private function _needValidation($id_order)
	{
		if (!(int)($id_order))
			return false;
		$order = Db::getInstance()->getRow('
		SELECT `payment_method`, `payment_status` 
		FROM `'._DB_PREFIX_.'paypal_order` 
		WHERE `id_order` = '.(int)($id_order));
		if (!$order)
			return false;
		return $order['payment_status'] == 'Pending' AND $order['payment_method'] == _PAYPAL_INTEGRAL_EVOLUTION_;
	}
	
	private function _needCapture($id_order)
	{
		if (!(int)($id_order))
			return false;
		$result = Db::getInstance()->getRow('
		SELECT `payment_method`, `payment_status`, `capture`  
		FROM `'._DB_PREFIX_.'paypal_order` 
		WHERE `id_order` = '.(int)($id_order).' AND `capture` = 1');
		if (!isset($result['payment_method']))
			return false;
		if ($result['payment_status'] != 'Pending_authorization' AND $result['payment_status'] != 'Completed')
			return false;
		return true;
	}
	
	private function _setConfigurationForm()
	{
		$this->_html .= '
		<form method="post" action="'.$_SERVER['REQUEST_URI'].'">	
			<script type="text/javascript">
				var pos_select = '.(($tab = (int)(Tools::getValue('tabs'))) ? $tab : '0').';
			</script>
			<script type="text/javascript" src="'._PS_BASE_URL_._PS_JS_DIR_.'tabpane.js"></script>
			<link type="text/css" rel="stylesheet" href="'._PS_BASE_URL_._PS_CSS_DIR_.'tabpane.css" />
			<input type="hidden" name="tabs" id="tabs" value="0" />
			<div class="tab-pane" id="tab-pane-1" style="width:100%;">
				 <div class="tab-page" id="step1">
					<h4 class="tab">'.$this->l('Solution').'</h2>
					'.$this->_getSolutionTabHtml().'
				</div>
				<div class="tab-page" id="step2">
					<h4 class="tab">'.$this->l('Settings').'</h2>
					'.$this->_getSettingsTabHtml().'
				</div>
				<div class="tab-page" id="step3">
					<h4 class="tab">'.$this->l('Logos and personalization').'</h2>
					'.$this->_getPersonalizationsTabHtml().'
				</div>
			</div>
			<div class="clear"></div>
			<script type="text/javascript">
				function loadTab(id){}
				setupAllTabs();
			</script>
		</form>';
	}
	
	private function _getSolutionTabHtml()
	{
		$paymentMethod = (int)(Tools::getValue('payment_method', Configuration::get('PAYPAL_PAYMENT_METHOD')));
		$paypalExpress = (int)(Tools::isSubmit('paypal_express') ? 1 : Configuration::get('PAYPAL_EXPRESS_CHECKOUT'));
		$paypalDebug = (int)(Tools::isSubmit('paypal_debug') ? 1 : Configuration::get('PAYPAL_DEBUG_MODE'));
		
		return '
		<h2>'.$this->l('Solution').'</h2>
		<h3>'.$this->l('Choose a solution:').'</h3>
		<ul style="list-style-type:none;">
			<li><input type="radio" name="payment_method" id="payment_method_0" value="'._PAYPAL_INTEGRAL_.'" '.($paymentMethod == _PAYPAL_INTEGRAL_ ? 'checked="checked" ' : '').'/> <label for="payment_method_0" class="t"><b>'.$this->l('Payments by credit cards').'</b>: '.$this->l('CB , Visa, Mastercard and PayPal account').'</label></li>
			<li><input type="radio" name="payment_method" id="payment_method_2" value="'._PAYPAL_INTEGRAL_EVOLUTION_.'" '.($paymentMethod == _PAYPAL_INTEGRAL_EVOLUTION_ ? 'checked="checked" ' : '').'/> <label for="payment_method_2" class="t"><b>'.$this->l('Payments by cards + seller protection').'</b></label><sup>*</sup> '.$this->l('(PayPal Integral Evolution, monthly subscription)').'</li>
			<li><input type="radio" name="payment_method" id="payment_method_1" value="'._PAYPAL_OPTION_PLUS_.'" '.($paymentMethod == _PAYPAL_OPTION_PLUS_ ? 'checked="checked" ' : '').'/> <label for="payment_method_1" class="t">'.$this->l('Payments by').' <b>'.$this->l('PayPal account').'</b> '.$this->l('(PayPal Option+)').'</label></li>
		</ul>
		<p style="color:red;"><sup>*</sup> '.$this->l('Activation subject to conditions').', <a style="color:red;text-decoration:underline;" href="http://altfarm.mediaplex.com/ad/ck/3484-23403-8030-88?ID=PROCPRESTA" style="text-decoration:underline;" target="_blank">'.$this->l('click here to subscribe').'</a></p>
		<h3>'.$this->l('Option:').'</h3>
		<ul style="list-style-type:none;">
			<li><input type="checkbox" name="paypal_express" id="paypal_express" value="1" '.($paypalExpress ? 'checked="checked" ' : '').'/> <label for="paypal_express" class="t"><b>'.$this->l('PayPal Express : payment in 2 clicks').'</b> '.$this->l('with PayPal account directly from cart page').'</label></li>
			<li><input type="checkbox" name="paypal_debug" id="paypal_debug" value="1" '.($paypalDebug ? 'checked="checked" ' : '').'/> <label for="paypal_express" class="t"><b>'.$this->l('Debug only:').'</b> '.$this->l('Activate long log message').'</label></li>
		</ul>
		<p class="center"><input class="button" type="submit" name="submitPayPal" value="'.$this->l('Save settings').'" /></p>
		<div style="border:1px solid red;color:red;padding:15px;">
			<span style="font-weight:bold;text-decoration:underline;">'.$this->l('Important:').'</span>
			'.$this->l('To use any PayPal solution, you need to set up API parameters in the « Settings » Tab').'
		</div>';
	}
	
	private function _getSettingsTabHtml()
	{
		global $cookie;

		$lang = new Language((int)($cookie->id_lang));
		$sandboxMode = (int)(Tools::getValue('sandbox_mode', Configuration::get('PAYPAL_SANDBOX')));
		$paypalCapture = (int)(Tools::getValue('paypal_capture', Configuration::get('PAYPAL_CAPTURE')));
		
		$html = '
		<h2>'.$this->l('Settings').'</h2>
		<label>'.$this->l('Sandbox mode (tests)').':</label>
		<div class="margin-form" style="padding-top:2px;">
			<input type="radio" name="sandbox_mode" id="sandbox_mode_1" value="1" '.($sandboxMode ? 'checked="checked" ' : '').'/> <label for="sandbox_mode_1" class="t">'.$this->l('Active').'</label> 
			<input type="radio" name="sandbox_mode" id="sandbox_mode_0" value="0" style="margin-left:15px;" '.(!$sandboxMode ? 'checked="checked" ' : '').'/> <label for="sandbox_mode_0" class="t">'.$this->l('Inactive').'</label>
		</div>
		<div class="clear"></div>
		<label>'.$this->l('Payment type').':</label>
		<div class="margin-form" style="padding-top:2px;">
			<input type="radio" name="paypal_capture" id="paypal_capture_0" value="0" '.(!$paypalCapture ? 'checked="checked" ' : '').'/> <label for="paypal_capture_0" class="t">'.$this->l('Direct (sales)').'</label> 
			<input type="radio" name="paypal_capture" id="paypal_capture_1" value="1" style="margin-left:15px;" '.($paypalCapture ? 'checked="checked" ' : '').'/> <label for="paypal_capture_1" class="t">'.$this->l('Authorization / Manual Capture').' '.$this->l('(Payment shipping)').'</label>
		</div>
		<label>'.$this->l('PayPal account e-mail').':</label>
		<div class="margin-form">
			<input type="text" name="email_paypal" value="'.htmlentities(Tools::getValue('email_paypal', Configuration::get('PAYPAL_BUSINESS')), ENT_COMPAT, 'UTF-8').'" size="30" />
		</div>
		<br />
		<h3 style="clear:both;">'.$this->l('Activation of API calls').'</h3>
		<label>'.$this->l('API Username').':</label>
		<div class="margin-form">
			<input type="text" name="api_username" value="'.htmlentities(Tools::getValue('api_username', Configuration::get('PAYPAL_API_USER')), ENT_COMPAT, 'UTF-8').'" size="30" />
		</div>
		<div class="clear"></div>
		<label>'.$this->l('API Password').':</label>
		<div class="margin-form">
			<input type="password" name="api_password" value="'.htmlentities(Tools::getValue('api_password', Configuration::get('PAYPAL_API_PASSWORD')), ENT_COMPAT, 'UTF-8').'" />
			<p>'.$this->l('Leave blank if the password has not changed').'</p>
		</div>
		<label>'.$this->l('API Signature').':</label>
		<div class="margin-form">
			<input type="text" name="api_signature" value="'.htmlentities(Tools::getValue('api_signature', Configuration::get('PAYPAL_API_SIGNATURE')), ENT_COMPAT, 'UTF-8').'" size="40" />
		</div>
		';
		if ($lang->iso_code == 'fr')
			$html .= '<p><a style="color:blue;text-decoration:underline;" href="http://www.youtube.com/watch?v=P2OmzHzbpIA" target="_blank">'.$this->l('Click here to learn how to generate your API username, password and signature.').'</a></p>';
		elseif ($lang->iso_code == 'es')
			$html .= '<p><a style="color:blue;text-decoration:underline;" href="http://www.youtube.com/watch?v=5x_BXI4equo" target="_blank">'.$this->l('Click here to learn how to generate your API username, password and signature.').'</a></p>';
		else
			$html .= '<p><a style="color:blue;text-decoration:underline;" href="http://www.youtube.com/watch?v=ho1OefLKbM0" target="_blank">'.$this->l('Click here to learn how to generate your API username, password and signature.').'</a></p>';
		$html .= '<p class="center"><input class="button" type="submit" name="submitPayPal" value="'.$this->l('Save settings').'" /></p>';
		
		return $html;
	}
	
	private function _getPersonalizationsTabHtml()
	{
		global $cookie;

		$lang = new Language((int)($cookie->id_lang));
		$template_paypal = Tools::getValue('template_paypal', Configuration::get('PAYPAL_TEMPLATE'));
		
		return '
		<h2>'.$this->l('Logos and personalizations').'</h2>
		<label for="banner_url">'.$this->l('Banner image URL').':</label>
		<div class="margin-form">
			<input type="text" name="banner_url" value="'.htmlentities(Tools::getValue('banner_url', Configuration::get('PAYPAL_HEADER')), ENT_COMPAT, 'UTF-8').'" size="50" />
			<p>'.$this->l('The image should be hosted on a secure (https) server. Max: 750x90px.').'</p>
		</div>
		<label>'.$this->l('Template chosen for PayPal Integral Evolution').':</label>
		<div class="margin-form" style="padding-top:2px;">
			<input type="radio" name="template_paypal" id="template_paypal_a" value="A" '.($template_paypal == 'A' ? 'checked="checked" ' : '').'/> <label for="template_paypal_a" class="t">A</label> 
			<input type="radio" name="template_paypal" id="template_paypal_b" value="B" style="margin-left:10px;" '.($template_paypal == 'B' ? 'checked="checked" ' : '').'/> <label for="template_paypal_b" class="t">B</label>
			<input type="radio" name="template_paypal" id="template_paypal_c" value="C" style="margin-left:10px;" '.($template_paypal == 'C' ? 'checked="checked" ' : '').'/> <label for="template_paypal_c" class="t">C</label>
		</div>
		'.($lang->iso_code == 'fr' ? '<p style="clear:both;"><a style="color:blue;text-decoration:underline;" href="https://cms.paypal.com/cms_content/FR/fr_FR/files/developer/Paypal_Integral_Evolution_Personnalisation.pdf" target="_blank">'.$this->l('Click here to learn how to customize these templates').'</a></p>' : '').'
		<p class="center"><input class="button" type="submit" name="submitPayPal" value="'.$this->l('Save settings').'" /></p>';
	}
	
	private function _setPayPalSubscription()
	{
		$this->_html .= '
		<div style="float: right; width: 440px; height: 150px; border: dashed 1px #666; padding: 8px; margin-left: 12px;">
			<h2>'.$this->l('Opening your PayPal account').'</h2>
			<div style="clear: both;"></div>
			<p>'.$this->l('When opening your PayPal account by clicking on the following image, you are helping us significantly to improve the PrestaShop software:').'</p>
			<p style="text-align: center;"><a href="https://www.paypal.com/fr/mrb/pal=TWJHHUL9AEP9C"><img src="../modules/paypal/prestashop_paypal.png" alt="PrestaShop & PayPal" style="margin-top: 12px;" /></a></p>
			<div style="clear: right;"></div>
		</div>
		<img src="../modules/paypal/paypal.gif" style="float:left; margin-right:15px;" />
		<b>'.$this->l('This module allows you to accept payments by PayPal.').'</b><br /><br />
		'.$this->l('If the client chooses this payment mode, your PayPal account will be automatically credited.').'<br />
		'.$this->l('You need to configure your PayPal account before using this module.').'
		<div style="clear:both;">&nbsp;</div>';
	}
	
	private function _postProcess()
	{
		global $currentIndex, $cookie;
		
		if (Tools::isSubmit('submitPayPal'))
		{
			$template_available = array('A', 'B', 'C');
			if (!Validate::isUnsignedInt(Tools::getValue('payment_method')) OR (int)(Tools::getValue('payment_method')) > 2)
				$this->_errors[] = $this->l('Invalid solution');
			if (Tools::getValue('email_paypal') == NULL AND Tools::getValue('api_username') == NULL AND Tools::getValue('api_signature') == NULL)
				$this->_errors[] = $this->l('Indicate account information.');
			if (Tools::getValue('email_paypal') != NULL AND !Validate::isEmail(Tools::getValue('email_paypal')))
				$this->_errors[] = $this->l('E-mail invalid');
			if (Tools::getValue('banner_url') != NULL AND !Validate::isUrl(Tools::getValue('banner_url')))
				$this->_errors[] = $this->l('URL for banner is invalid');
			elseif (Tools::getValue('banner_url') != NULL AND strpos(Tools::getValue('banner_url'), 'https://') === false)	
				$this->_errors[] = $this->l('URL for banner must use HTTPS protocol');
			if (!in_array(Tools::getValue('template_paypal'), $template_available))
				$this->_errors[] = $this->l('PayPal template invalid.');
			if (Tools::getValue('paypal_capture') == 1  AND (Tools::getValue('api_username') == NULL OR Tools::getValue('api_signature') == NULL))
				$this->_errors[] = $this->l('Cannot use Authorization / capture without API Credentials.');
			if (Tools::getValue('payment_method') == _PAYPAL_INTEGRAL_EVOLUTION_  AND (Tools::getValue('api_username') == NULL OR Tools::getValue('api_signature') == NULL))
				$this->_errors[] = $this->l('Cannot use this solution without API Credentials.');
			if (Tools::isSubmit('paypal_express') AND (Tools::getValue('api_username') == NULL OR Tools::getValue('api_signature') == NULL))
				$this->_errors[] = $this->l('Cannot use PayPal Express without API Credentials.');
			
			if (!sizeof($this->_errors))
			{
				Configuration::updateValue('PAYPAL_SANDBOX', (int)(Tools::getValue('sandbox_mode')));
				Configuration::updateValue('PAYPAL_BUSINESS', trim(Tools::getValue('email_paypal')));
				Configuration::updateValue('PAYPAL_HEADER', Tools::getValue('banner_url'));
				Configuration::updateValue('PAYPAL_API_USER', trim(Tools::getValue('api_username')));
				Configuration::updateValue('PAYPAL_API_PASSWORD', Tools::getValue('api_password'));
				Configuration::updateValue('PAYPAL_API_SIGNATURE', trim(Tools::getValue('api_signature')));
				Configuration::updateValue('PAYPAL_EXPRESS_CHECKOUT', (int)(Tools::isSubmit('paypal_express')));
				Configuration::updateValue('PAYPAL_MODE_DEBUG', (int)(Tools::isSubmit('paypal_debug')));
				Configuration::updateValue('PAYPAL_CAPTURE', (int)(Tools::getValue('paypal_capture')));
				Configuration::updateValue('PAYPAL_PAYMENT_METHOD', (int)(Tools::getValue('payment_method')));
				Configuration::updateValue('PAYPAL_TEMPLATE', Tools::getValue('template_paypal'));
				if (Tools::getValue('payment_method') == _PAYPAL_INTEGRAL_EVOLUTION_)
					$method = 'Paypal Integrale Evolution';
				elseif (Tools::getValue('payment_method') == _PAYPAL_INTEGRAL_)
					$method = 'Paypal Integrale';
				elseif (Tools::getValue('payment_method') == _PAYPAL_OPTION_PLUS_)
					$method = 'Paypal Integrale';
				else
					$method = '';
				$this->_html = $this->displayConfirmation($this->l('Settings updated').'<img src="http://www.prestashop.com/modules/paypal.png?email='.urlencode(Tools::getValue('email_paypal')).'&mode='.(Tools::getValue('sandbox_mode') ? 0 : 1).'&method='.urlencode($method).'" style="float:right" />');
			}
			else
			{
				$error_msg = '';
				foreach ($this->_errors AS $error)
					$error_msg .= $error.'<br />';
				$this->_html = $this->displayError($error_msg);
			}
		}
		
		if (Tools::isSubmit('submitPayPalValidation'))
		{
			if (!($response = $this->_updatePaymentStatusOfOrder((int)(Tools::getValue('id_order')))) OR !sizeof($response))
				$this->_html .= '<p style="color:red;">'.$this->l('Error obtaining payment status.').'</p>';
			else
			{
				if ($response['ACK'] == 'Success')
				{
					if ($response['PAYMENTSTATUS'] == 'Completed' OR $response['PAYMENTSTATUS'] == 'Reversed' OR ($response['PAYMENTSTATUS'] == 'Pending' AND $response['PENDINGREASON'] == 'authorization'))
						Tools::redirectAdmin($currentIndex.'&id_order='.(int)(Tools::getValue('id_order')).'&vieworder&paypal=validationOk&token='.Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')).(int)($cookie->id_employee)));
					else
						$this->_html .= '<p><b>'.$this->l('Status').':</b> '.$response['PAYMENTSTATUS'].' ('.$this->l('Reason:').' '.$response['PENDINGREASON'].')</p>';
				}
				else
					$this->_html .= '<p style="color:red;">'.$this->l('Error from PayPal: ').$response['L_LONGMESSAGE0'].' (#'.$response['L_ERRORCODE0'].')</p>';
			}
		}
		
		if (Tools::isSubmit('submitPayPalCapture'))
		{
			if (!($response = $this->_doCapture((int)(Tools::getValue('id_order')))) OR !sizeof($response))
				$this->_html .= '<p style="color:red;">'.$this->l('Error when making capture request').'</p>';
			else
			{
				if ($response['ACK'] == 'Success')
				{
					if ($response['PAYMENTSTATUS'] == 'Completed')
						Tools::redirectAdmin($currentIndex.'&id_order='.(int)(Tools::getValue('id_order')).'&vieworder&paypal=captureOk&token='.Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')).(int)($cookie->id_employee)));
					else
						Tools::redirectAdmin($currentIndex.'&id_order='.(int)(Tools::getValue('id_order')).'&vieworder&paypal=captureError&token='.Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')).(int)($cookie->id_employee)));
				}
				else
					$this->_html .= '<p style="color:red;">'.$this->l('Error from PayPal: ').$response['L_LONGMESSAGE0'].' (#'.$response['L_ERRORCODE0'].')</p>';
			}
		}
		
		if (Tools::isSubmit('submitPayPalRefund'))
		{
			if (!($response = $this->_doTotalRefund((int)(Tools::getValue('id_order')))) OR !sizeof($response))
				$this->_html .= '<p style="color:red;">'.$this->l('Error when making refund request').'</p>';
			else
			{
				if ($response['ACK'] == 'Success')
				{
					if ($response['REFUNDTRANSACTIONID'] != '')
						Tools::redirectAdmin($currentIndex.'&id_order='.(int)(Tools::getValue('id_order')).'&vieworder&paypal=refundOk&token='.Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')).(int)($cookie->id_employee)));
					else
						Tools::redirectAdmin($currentIndex.'&id_order='.(int)(Tools::getValue('id_order')).'&vieworder&paypal=refundError&token='.Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')).(int)($cookie->id_employee)));
				}
				else
					$this->_html .= '<p style="color:red;">'.$this->l('Error from PayPal: ').$response['L_LONGMESSAGE0'].' (#'.$response['L_ERRORCODE0'].')</p>';
			}
		}
	}
	
	private function _getTransactionId($id_order)
	{
		if (!$id_order)
			return false;
		
		return Db::getInstance()->getValue('
		SELECT `id_transaction` 
		FROM `'._DB_PREFIX_.'paypal_order` 
		WHERE `id_order` = '.(int)($id_order));
	}
	
	private function _makeRefund($id_transaction, $amt = false)
	{
		include_once(_PS_MODULE_DIR_.'paypal/api/paypallib.php');
		
		if (!$this->_isPayPalAPIAvailable())
			die(Tools::displayError('Fatal Error: no API Credentials are available'));
		if (!$id_transaction)
			die(Tools::displayError('Fatal Error: id_transaction is null'));
		
		if (!$amt)
			$request = '&TRANSACTIONID='.urlencode($id_transaction).'&REFUNDTYPE=Full';
		else
		{
			$isoCurrency = Db::getInstance()->getValue('
			SELECT `iso_code`
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'currency` c ON (o.`id_currency` = c.`id_currency`)');
			$request = '&TRANSACTIONID='.urlencode($id_transaction).'&REFUNDTYPE=Partial&AMT='.(float)($amt).'&CURRENCYCODE='.urlencode(strtoupper($isoCurrency));
		}
		$paypalLib = new PaypalLib();
		return $paypalLib->makeCall($this->getAPIURL(), $this->getAPIScript(), 'RefundTransaction', $request);
	}
	
	private function _addNewPrivateMessage($id_order, $message)
	{
		if (!$id_order)
			return false;
		$msg = new Message();
		$message = strip_tags($message, '<br>');
		if (!Validate::isCleanHtml($message))
			$message = $this->l('Payment message is not valid, please check your module.');
		$msg->message = $message;
		$msg->id_order = (int)($id_order);
		$msg->private = 1;

		return $msg->add();
	}
	
	private function _doTotalRefund($id_order)
	{
		global $cookie;
		
		if (!$this->_isPayPalAPIAvailable())
			return false;
		if (!$id_order)
			return false;

		$id_transaction = $this->_getTransactionId((int)($id_order));
		if (!$id_transaction)
			return false;

		$order = new Order((int)($id_order));
		if (!Validate::isLoadedObject($order))
			return false;
		$products = $order->getProducts();
		// Amount for refund
		$amt = 0.00;
		foreach ($products AS $product)
			if ($product['product_quantity_refunded'] == 0)
				$amt += (float)($product['total_price']);
		$amt += (float)($order->total_shipping);
		// check if total or partial
		if ($order->total_products_wt == $amt)
			$response = $this->_makeRefund($id_transaction);
		else
			$response = $this->_makeRefund($id_transaction, (float)($amt));
		$message = $this->l('Refund operation result:').'<br>';
		foreach ($response AS $k => $value)
			$message .= $k.': '.$value.'<br>';
		if (array_key_exists('ACK', $response) AND $response['ACK'] == 'Success' AND $response['REFUNDTRANSACTIONID'] != '')
		{
			$message .= $this->l('PayPal refund successful!');
			if (!Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'paypal_order` SET `payment_status` = \'Refunded\' WHERE `id_order` = '.(int)($id_order)))
				die(Tools::displayError('Error when updating PayPal database'));
			$history = new OrderHistory();
			$history->id_order = (int)($id_order);
			$history->changeIdOrderState(_PS_OS_REFUND_, (int)($id_order));
			$history->addWithemail();
		}
		else
			$message .= $this->l('Transaction error!');
		$this->_addNewPrivateMessage((int)($id_order), $message);

		return $response;
	}
	
	private function _doCapture($id_order)
	{
		global $cookie;
		
		include_once(_PS_MODULE_DIR_.'paypal/api/paypallib.php');
		
		if (!$this->_isPayPalAPIAvailable())
			return false;
		if (!$id_order)
			return false;
		
		$id_transaction = $this->_getTransactionId((int)($id_order));
		if (!$id_transaction)
			return false;
		
		$order = new Order((int)($id_order));
		$currency = new Currency((int)($order->id_currency));
		$request = '&AUTHORIZATIONID='.urlencode($id_transaction).'&AMT='.(float)($order->total_paid).'&CURRENCYCODE='.$currency->iso_code.'&COMPLETETYPE=Complete';
		$paypalLib = new PaypalLib();
		$response = $paypalLib->makeCall($this->getAPIURL(), $this->getAPIScript(), 'DoCapture', $request);
		$message = $this->l('Capture operation result:').'<br>';
		foreach ($response AS $k => $value)
			$message .= $k.': '.$value.'<br>';
		if (array_key_exists('ACK', $response) AND $response['ACK'] == 'Success' AND $response['PAYMENTSTATUS'] == 'Completed')
		{
			$history = new OrderHistory();
			$history->id_order = (int)($id_order);
			$history->changeIdOrderState(_PS_OS_PAYMENT_, (int)($id_order));
			$history->addWithemail();
			$message .= $this->l('Order finished with PayPal!');
		}
		elseif (isset($response['PAYMENTSTATUS']))
			$message .= $this->l('Transaction error!');
		if (!Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'paypal_order` SET `capture` = 0, `payment_status` = \''.pSQL($response['PAYMENTSTATUS']).'\', `id_transaction` = \''.pSQL($response['TRANSACTIONID']).'\' WHERE `id_order` = '.(int)($id_order)))
			die(Tools::displayError('Error when updating PayPal database'));
		$this->_addNewPrivateMessage((int)($id_order), $message);

		return $response;
	}
	
	private function _updatePaymentStatusOfOrder($id_order)
	{
		global $cookie;
		
		include_once(_PS_MODULE_DIR_.'paypal/api/paypallib.php');
		
		if (!$this->_isPayPalAPIAvailable())
			return false;
		if (!$id_order)
			return false;

		$id_transaction = $this->_getTransactionId((int)($id_order));
		if (!$id_transaction)
			return false;
		
		$request = '&TRANSACTIONID='.urlencode($id_transaction);
		$paypalLib = new PaypalLib();
		$response = $paypalLib->makeCall($this->getAPIURL(), $this->getAPIScript(), 'GetTransactionDetails', $request);
		if (array_key_exists('ACK', $response))
		{
			if ($response['ACK'] == 'Success')
			{
				if (isset($response['PAYMENTSTATUS']))
				{
					if ($response['PAYMENTSTATUS'] == 'Completed')
					{
						$history = new OrderHistory();
						$history->id_order = (int)($id_order);
						$history->changeIdOrderState(_PS_OS_PAYMENT_, (int)($id_order));
						$history->addWithemail();
					}
					elseif ($response['PAYMENTSTATUS'] == 'Pending' AND $response['PENDINGREASON'] == 'authorization')
					{
						$history = new OrderHistory();
						$history->id_order = (int)($id_order);
						$history->changeIdOrderState((int)(Configuration::get('PAYPAL_OS_AUTHORIZATION')), (int)($id_order));
						$history->addWithemail();
					}
					elseif ($response['PAYMENTSTATUS'] == 'Reversed')
					{
						$history = new OrderHistory();
						$history->id_order = (int)($id_order);
						$history->changeIdOrderState(_PS_OS_ERROR_, (int)($id_order));
						$history->addWithemail();
					}
					if (!Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'paypal_order` SET `payment_status` = \''.pSQL($response['PAYMENTSTATUS']).($response['PENDINGREASON'] == 'authorization' ? '_authorization' : '').'\' WHERE `id_order` = '.(int)($id_order)))
						die(Tools::displayError('Error when updating PayPal database'));
				}
			}
			$message = $this->l('Verification status:').'<br>';
			foreach ($response AS $k => $value)
				$message .= $k.': '.$value.'<br>';
			$this->_addNewPrivateMessage((int)($id_order), $message);
			return $response;
		}
		return false;
	}
	
	private function _isPayPalAPIAvailable()
	{
		if (Configuration::get('PAYPAL_API_USER') != NULL AND Configuration::get('PAYPAL_API_PASSWORD') != NULL AND Configuration::get('PAYPAL_API_SIGNATURE') != NULL)
			return true;
		return false;
	}
	
	private function _checkAndUpdateFromOldVersion($install = false)
	{
		if (!Configuration::get('PAYPAL_NEW') AND ($this->active OR $install))
		{
			$ok = true;
			/* Check PayPal API */
			if (file_exists(_PS_ROOT_DIR_.'/modules/paypalapi/paypalapi.php'))
			{
				$confs = Configuration::getMultiple(array('PAYPAL_HEADER', 'PAYPAL_SANDBOX', 'PAYPAL_API_USER', 'PAYPAL_API_PASSWORD', 'PAYPAL_API_SIGNATURE', 'PAYPAL_EXPRESS_CHECKOUT'));
				include_once(_PS_ROOT_DIR_.'/modules/paypalapi/paypalapi.php');
				$paypalapi = new PayPalAPI();
				if ($paypalapi->active)
				{
					if (Configuration::get('PAYPAL_INTEGRAL') == 1)
						Configuration::updateValue('PAYPAL_PAYMENT_METHOD', _PAYPAL_INTEGRAL_);
					elseif (Configuration::get('PAYPAL_INTEGRAL') == 0)
						Configuration::updateValue('PAYPAL_PAYMENT_METHOD', _PAYPAL_OPTION_PLUS_);
					$paypalapi->uninstall();
					Configuration::loadConfiguration();
					foreach ($confs AS $key => $value)
						Configuration::updateValue($key, $value);
				}
			}
			/* Create Table */
			if (!Db::getInstance()->Execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paypal_order` (
			`id_order` int(10) unsigned NOT NULL auto_increment,
			`id_transaction` varchar(255) NOT NULL,
			PRIMARY KEY (`id_order`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
				$ok = false;
			if (!Db::getInstance()->Execute('
			ALTER TABLE `'._DB_PREFIX_.'paypal_order` ADD `payment_method` INT NOT NULL,
			ADD `payment_status` VARCHAR(255) NOT NULL,
			ADD `capture` INT NOT NULL'))
				$ok = false;

			/* Hook */
			$this->registerHook('cancelProduct');
			$this->registerHook('adminOrder');

			/* Create OrderState */
			if (!Configuration::get('PAYPAL_OS_AUTHORIZATION'))
			{
				$orderState = new OrderState();
				$orderState->name = array();
				foreach (Language::getLanguages() AS $language)
				{
					if (strtolower($language['iso_code']) == 'fr')
						$orderState->name[$language['id_lang']] = 'Autorisation acceptée par PayPal';
					else
						$orderState->name[$language['id_lang']] = 'Authorization accepted from PayPal';
				}
				$orderState->send_email = false;
				$orderState->color = '#DDEEFF';
				$orderState->hidden = false;
				$orderState->delivery = false;
				$orderState->logable = true;
				$orderState->invoice = true;
				if ($orderState->add())
					copy(_PS_ROOT_DIR_.'/img/os/'._PS_OS_PAYPAL_.'.gif', _PS_ROOT_DIR_.'/img/os/'.(int)($orderState->id).'.gif');
				Configuration::updateValue('PAYPAL_OS_AUTHORIZATION', (int)($orderState->id));
			}
			/* Delete unseless configuration */
			Configuration::deleteByName('PAYPAL_INTEGRAL');

			/* Add new Configurations */
			if (!Configuration::get('PAYPAL_PAYMENT_METHOD'))
				Configuration::updateValue('PAYPAL_PAYMENT_METHOD', _PAYPAL_INTEGRAL_);
			Configuration::updateValue('PAYPAL_CAPTURE', 0);
			Configuration::updateValue('PAYPAL_TEMPLATE', 'A');

			if ($ok)
				Configuration::updateValue('PAYPAL_NEW', 1);

			return $ok;
		}
		return false;
	}
	
	public function getOrder($id_transaction)
	{
		return Db::getInstance()->getValue('
		SELECT `id_order` 
		FROM `'._DB_PREFIX_.'paypal_order` 
		WHERE `id_transaction` = \''.pSQL($id_transaction).'\'
		');
	}
}
