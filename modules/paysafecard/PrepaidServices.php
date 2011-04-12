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
$module_name = 'paysafecard';

include(_PS_MODULE_DIR_.'/'.$module_name.'/Disposition.php');
include(_PS_MODULE_DIR_.'/'.$module_name.'/PrepaidServicesAPI.php');

abstract class PrepaidServices extends PaymentModule
{
	protected $max_amount = 1000;
	protected $max_amount_currency = 'EUR';
	protected $default_language = 'en';

	protected $prefix = '';

	protected $environments = array('P' => 'Production',
								 'T' => 'Test');

	protected $business_types = array('I' => 'Intangible',
									'T' => 'Tangible');

	// abstract
	protected $supported_languages;
	protected $allowed_currencies;
	protected $supported_currencies;
	protected $payment_url;
	protected $prepaid_api_configuration;
    protected $register_url;

	public function __construct()
	{
		parent::__construct();

		if ($this->active AND !extension_loaded('curl'))
			$this->warning = $this->getL('curl_required');
	}

	public function install()
	{
		$ps_ct_immediat_payment = Configuration::get($this->prefix.'IMMEDIAT_PAYMENT') ? Configuration::get($this->prefix.'IMMEDIAT_PAYMENT') : '1';
		$ps_ct_salt = Configuration::get($this->prefix.'SALT') ? Configuration::get($this->prefix.'SALT') : strtoupper(Tools::passwdGen(8));
		$ps_ct_business_type = Configuration::get($this->prefix.'BUSINESS_TYPE') ? Configuration::get($this->prefix.'BUSINESS_TYPE') : 'I';
		$ps_ct_environment = Configuration::get($this->prefix.'ENVIRONMENT') ? Configuration::get($this->prefix.'ENVIRONMENT') : 'T';

		return parent::install() AND Disposition::createTable() AND $this->_createOrderState() AND
				$this->registerHook('payment') AND $this->registerHook('paymentReturn') AND $this->registerHook('adminOrder')
				AND	Configuration::updateValue($this->prefix.'IMMEDIAT_PAYMENT', $ps_ct_immediat_payment)
				AND Configuration::updateValue($this->prefix.'SALT', $ps_ct_salt)
				AND Configuration::updateValue($this->prefix.'BUSINESS_TYPE', $ps_ct_business_type)
				AND Configuration::updateValue($this->prefix.'ENVIRONMENT', $ps_ct_environment);
	}

	private function _createOrderState()
	{

		if (Configuration::get($this->prefix.'ORDER_STATE_ID') && Configuration::get($this->prefix.'ORDER_STATE_PART_ID')) return true;

		// Awaiting payment
		$os = new OrderState();
		$os->name = array('1' => 'Awaiting '.$this->displayName.' payment',
						  '2' => 'En attente du paiement par '.$this->displayName,
						  '3' => 'En espera de pago por '.$this->displayName);

		$os->invoice = false;
		$os->color = 'lightblue';
		$os->logable = true;

		if ($os->save())
		{
			Configuration::updateValue($this->prefix.'ORDER_STATE_ID', $os->id);

			copy(_PS_MODULE_DIR_.$this->name.'/logo.gif',_PS_IMG_DIR_.'os/'.$os->id.'.gif');
		} else
			return false;

		// Partially paid
		$os1 = new OrderState();
		$os1->name = array('1' => 'Partially paid by '.$this->displayName,
						  '2' => 'Payé partiellement via '.$this->displayName,
						  '3' => 'Pagado parcialmente con '.$this->displayName);

		$os1->invoice = false;
		$os1->color = 'lightblue';
		$os1->logable = true;

		if ($os1->save())
		{
			Configuration::updateValue($this->prefix.'ORDER_STATE_PART_ID', $os1->id);
			copy(_PS_MODULE_DIR_.$this->name.'/logo.gif',_PS_IMG_DIR_.'os/'.$os1->id.'.gif');
			return true;
		}

		return false;
	}

	private function _deleteOrderState()
	{
		DB::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'order_state` WHERE `id_order_state` = '.(int)(Configuration::get($this->prefix.'ORDER_STATE_ID')));
		DB::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'order_state_lang` WHERE `id_order_state` = '.(int)(Configuration::get($this->prefix.'ORDER_STATE_ID')));

		DB::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'order_state` WHERE `id_order_state` = '.(int)(Configuration::get($this->prefix.'ORDER_STATE_PART_ID')));
		DB::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'order_state_lang` WHERE `id_order_state` = '.(int)(Configuration::get($this->prefix.'ORDER_STATE_PART_ID')));

		return true;
	}

	public function getPaymentUrlBase()
	{
		return $this->payment_url[Configuration::get($this->prefix.'ENVIRONMENT')];
	}

	private function _getSupportedLanguageIsoById($id_lang)
	{
		$lang = Language::getIsoById((int)($id_lang));

		return in_array($lang, $this->supported_languages) ? $lang : $this->default_language;
	}

	private function _getRegisterLink($id_lang)
	{
		$lang = Language::getIsoById((int)($id_lang));
		return array_key_exists($lang, $this->register_url) ? $this->register_url[$lang] : $this->register_url[$this->default_language];
	}

	private function _getAllowedCurrencies()
	{
		if (empty($this->allowed_currencies))
			$this->allowed_currencies = DB::getInstance()->ExecuteS(
				'SELECT c.id_currency, c.iso_code, c.name, c.sign
				FROM '._DB_PREFIX_.'currency c
				WHERE c.deleted = 0
				AND iso_code IN (\''.implode('\',\'', $this->supported_currencies).'\')');

		return $this->allowed_currencies;
	}

	public function isCurrencyActive($currency_iso_code)
	{
		$mid = Configuration::get($this->prefix.'MERCHANT_ID_'.$currency_iso_code);
		return ($mid && file_exists($this->certificat_dir.$mid.'.pem') && Configuration::get($this->prefix.'KEYRING_PW_'.$currency_iso_code));
	}

	public function createDisposition($cart)
	{
		global $cookie;

		$currency = new Currency((int)($cart->id_currency));
		$language = $this->_getSupportedLanguageIsoById((int)($cookie->id_lang));
		$mid = Configuration::get($this->prefix.'MERCHANT_ID_'.$currency->iso_code);
		$mtid = $cart->id.'-'.time();
		$amount = number_format((float)($cart->getOrderTotal(true, Cart::BOTH)), 2, '.','');
		$currency_iso = $currency->iso_code;
		$business_type = Configuration::get($this->prefix.'BUSINESS_TYPE');
		$reporting_criteria = '';

		$hash = md5(Configuration::get($this->prefix.'SALT') + $amount + $currency_iso);

		$ok_url = Tools::getShopDomainSsl(true, true)._MODULE_DIR_.$this->name.'/payment.php?hash='.$hash;
		$nok_url = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'/order.php?step=3';

		list($return_code, $error_code, $message) = PrepaidServicesAPI::createDisposition($this->getAPIConfiguration($currency_iso), $mid, $mtid, $amount, $currency_iso, $ok_url, $nok_url, $business_type, $reporting_criteria);

		if ($return_code == 0)
		{
			Disposition::deleteByCartId((int)($cart->id)); // Avoid duplicate disposition (canceled orders in CT for example)
			Disposition::create((int)($cart->id), $mtid, $amount, $currency_iso);
			$message = $this->getPaymentUrlBase().'?currency='.$currency->iso_code.'&mid='.$mid.'&mtid='.$mtid.'&amount='.$amount.'&language='.$language;
		}

		return array('return_code' => $return_code, 'message' => $message);
	}

	public function getDispositionState($id_cart)
	{
		$disposition = Disposition::getByCartId((int)($id_cart));

		if (!array_key_exists('id_disposition', $disposition))
			die(Tools::displayError());

		return PrepaidServicesAPI::getSerialNumbers($this->getAPIConfiguration($disposition['currency']), Configuration::get($this->prefix.'MERCHANT_ID_'.$disposition['currency']), $disposition['mtid'], $disposition['currency']);
	}

	public function executeDebit($id_cart, $amount = NULL, $close_flag = 1)
	{
		$disposition = Disposition::getByCartId((int)($id_cart));
		if (!array_key_exists('id_disposition', $disposition))
			die(Tools::displayError());

		if (!isset($amount) || $amount === '')
			$amount = $disposition['amount'];

		$result = PrepaidServicesAPI::executeDebit($this->getAPIConfiguration($disposition['currency']), Configuration::get($this->prefix.'MERCHANT_ID_'.$disposition['currency']), $disposition['mtid'],  number_format($amount, 2, '.', ''), $disposition['currency'], $close_flag);

		if ($result[0] == 0)
		{
			if ($amount == $disposition['amount'] || $close_flag)
				Disposition::delete((int)($disposition['id_disposition']));
			else
				Disposition::updateAmount((int)($disposition['id_disposition']), (float)($amount));
		}

		return $result;
	}


	public function getContent()
	{
		$out = '<h2>'.$this->displayName.'</h2>';
		$err_req = false;

		// check requirements
		if (!extension_loaded('curl'))
		{
			$out .= $this->displayError($this->getL('curl_required'));
			$err_req = true;
		}


		if (!is_writable($this->certificat_dir))
		{
			$out .= $this->displayError($this->certificat_dir.' '.$this->getL('not_writable'));
			$err_req = true;
		}

		$id_currency = Currency::getIdByIsoCode($this->max_amount_currency);
		if (empty($id_currency))
		{
			$out .= $this->displayError($this->getL('currency_required').'['.$this->max_amount_currency.']');
			$err_req = true;
		}

		if (!$err_req && Tools::isSubmit('submitCtConfiguration'))
		{
			$errors = $this->_validateForm();

			if (empty($errors))
				$out .= $this->_postProcess();
			else
				$out .= $errors;
		}

		$out .= $this->_displayStyleAndJS();
		$out .= $this->_displayInfos();
		$out .= $this->_displayForm();

		return $out;
	}

	private function _displayStyleAndJS()
	{
		return '<script type="text/javascript" src="'._MODULE_DIR_.$this->name.'/prepaidservices.js" ></script>
				<style>
					.currencies_configuration { border: 2px solid #DFD5C3; border-collapse: collapse; }
					.currencies_configuration td { width: 200px; padding: 20px; border: 2px solid #DFD5C3 }
					.currency_label { font-weight: bold; }
					.currencies_label { font-weight: bold; padding: 0px; }
					#infos_cashticket { width: 420px; float: left}
				</style>';
	}

	private function _displayForm()
	{
		$business_type_options = '';
		foreach ($this->business_types AS $key => $value)
			$business_type_options .= '<option value="'.$key.'" '.(Configuration::get($this->prefix.'BUSINESS_TYPE') == $key ? 'selected' : '').'>'.$value.'</option>';

		$currencies_configuration = '<p class="currencies_label">'.$this->getL('configure_currency').'</p>
									 <p>'.$this->getL('payment_not_displayed').'</p>
									<table class="currencies_configuration">';

		$environment_radio = '';
		foreach ($this->environments AS $key => $value)
			$environment_radio .= '<input type="radio" id="ct_environment" name="ct_environment" value="'.Tools::htmlentitiesUTF8($key).'"  '.(Configuration::get($this->prefix.'ENVIRONMENT') == $key ? 'checked' : '').'>'. $value. '<br />';

		foreach ($this->_getAllowedCurrencies() AS $currency)
		{
			$currencies_configuration .= '
			<tr>
				<td class="currency_label">'.$this->getL('configuration_in').' '.$currency['name'].' '.$currency['sign'].'</td>
				<td>
					<label>'.$this->getL('merchant_id').'</label>
					<div class="margin-form">
						<input type="text" name="ct_merchant_id_'.$currency['iso_code'].'" value="'.Tools::htmlentitiesUTF8(Configuration::get($this->prefix.'MERCHANT_ID_'.$currency['iso_code'])).'"/>
					</div>
					<br />
						<label>'.$this->getL('keyring_certificate').'</label>
					<div class="margin-form">
						<input type="file" name="ct_keyring_certificate_'.$currency['iso_code'].'" />
					</div>
					<label>'.$this->getL('keyring_pw').'</label>
					<div class="margin-form">
						<input type="text" name="ct_keyring_pw_'.$currency['iso_code'].'" value="'.Tools::htmlentitiesUTF8(Configuration::get($this->prefix.'KEYRING_PW_'.$currency['iso_code'])).'"/>
					</div>
				</td>
			</tr>';
		}
		$currencies_configuration .= '</table>';

		return '<form enctype="multipart/form-data" action="'.$_SERVER['REQUEST_URI'].'" method="post">
					<fieldset>
					<legend><img src="../img/admin/cog.gif" alt="" />'.$this->getL('configuration').'</legend>
					<label>'.$this->getL('environment').'</label>
					<div class="margin-form">
						'.$environment_radio.'
					</div>
					<hr class="clear" />
					<label>'.$this->getL('business_type').'</label>
					<div class="margin-form">
						<select id="ct_business_type" name="ct_business_type" onchange="toggleImediatPayment()">
							'.$business_type_options.'
						</select>
					</div>
					<div id="imediat_payment">
					<label>'.$this->getL('immediat_payment').'</label>
					<div class="margin-form">
						<input type="checkbox" name="ct_immediat_payment" value="1" '.(Configuration::get($this->prefix.'IMMEDIAT_PAYMENT') ? 'checked' : '').' />
					</div>
					</div>
					<hr class="clear" />
					'.$currencies_configuration.'
					<br />
					<div>
						<input type="submit" value="'.$this->getL('update_configuration').'" name="submitCtConfiguration" class="button" />
					</div>
					</label>
					</fieldset>
				</form>';
	}

	private function _displayInfos()
	{
	    global $cookie;

		return '<fieldset id="infos_cashticket">
				<legend><img src="'._MODULE_DIR_.$this->name.'/img/payment-small.png" alt="" />'.$this->displayName.'</legend>
					<center><img src="'._MODULE_DIR_.$this->name.'/img/payment.png" alt=""  class="logo" /></center>
					'.$this->getL('introduction').'
					<br /><br />
					<a style="color: blue; text-decoration: underline" href="'.$this->_getRegisterLink((int)$cookie->id_lang).'">'.$this->getL('register').'</a>
				</fieldset>
				<div class="clear" /><br />';
	}

	private function _validateForm()
	{
		$errors = '';

		foreach ($this->_getAllowedCurrencies() AS $currency)
		{
			$mid = trim(Tools::getValue('ct_merchant_id_'.$currency['iso_code']));

			if (preg_match('/^[0-9]{10}$/', $mid))
			{
				$mid_certificat = $_FILES['ct_keyring_certificate_'.$currency['iso_code']];

				if (!$mid_certificat || $mid_certificat['error'] == 4 || $mid_certificat['error'] == 3)
				{
					if (!file_exists($this->certificat_dir.$mid.'.pem'))
						$errors .= $this->displayError($this->getL('certificate_required').' ['.$currency['iso_code'].']');
				}
				else
				{
					if ($mid_certificat['error'])
					{
						switch ($mid_certificat['error'])
						{
							case 3: // UPLOAD_ERR_PARTIAL
								$errors .= $this->displayError($this->getL('file_partialy_uploaded'));
								break;

							case 4: // UPLOAD_ERR_NO_FILE
								$errors .= $this->displayError($this->getL('file_empty'));
								break;
						}
					}

					if (substr($mid_certificat['name'], -4) != '.pem')
						$errors .= $this->displayError($this->getL('invalid_file').' ['.$currency['iso_code'].']');
				}
			}
			elseif (!empty($mid))
			{
				$errors .= $this->displayError($this->getL('invalid_merchant_id').' ['.$currency['iso_code'].']');
			}
		}

		if (!array_key_exists(Tools::getValue('ct_business_type'), $this->business_types))
			$errors .= $this->getL('invalid_business_type');

		if (!array_key_exists(Tools::getValue('ct_environment'), $this->environments))
			$errors .= $this->getL('invalid_environment');

		return $errors;
	}

	private function _acceptPayment($order, $disposition, $currency_sign, $amount = NULL)
	{
		$isCorrect = true;
		if (!$disposition)
			die(Tools::displayError());

		if (!isset($amount) || $amount === '')
			$amount = $disposition['amount'];

		$amount = number_format($amount, 2, '.', '');
		$close_flag = (int)($amount == $disposition['amount']);

		list($resultcode, $errorcode, $errormessage) = $this->executeDebit($disposition['id_cart'], $amount, $close_flag);

		$param = '';
		if ($resultcode != 0)
		{
			$message = $this->getL('payment_error').' '.$errormessage;
			$isCorrect = false;
		}
		else
			$message = $this->getL('payment_accepted') .'('.$amount.' '.$currency_sign.') '.($close_flag ? $this->getL('disposition_consumed') : '') ;

		$msg = new Message();
		$msg->message = $message;
		$msg->id_order = (int)($order->id);
		$msg->private = 1;
		$msg->add();

		if ($isCorrect)
		{
			if ($order->total_paid == $order->total_paid_real)
				$order->total_paid_real = $amount;
			else
				$order->total_paid_real += $amount;

			$os = _PS_OS_PAYMENT_;
			if ($order->total_paid != $order->total_paid_real)
				$os = Configuration::get($this->prefix.'ORDER_STATE_PART_ID');

			$history = new OrderHistory();
			$history->id_order = (int)($order->id);
			$history->changeIdOrderState($os, (int)($order->id));
			$history->save();

			$order->save();
		}

		return $isCorrect;
	}

	public function _releasePayment($order, $disposition)
	{
		if (!$disposition)
			die(Tools::displayError());

		list($resultcode, $errorcode, $errormessage) = $this->executeDebit($disposition['id_cart'], 0, 1);

		$param = '';
		if ($resultcode != 0)
		{
			$message = $this->getL('release_error').' '.$errormessage;
			$isCorrect = false;
		}
		else
			$message = $this->getL('payment_released');

		$msg = new Message();
		$msg->message = $message;
		$msg->id_order = (int)($order->id);
		$msg->private = 1;
		$msg->add();

		return $errorcode;
	}

	private function _postProcess()
	{
		Configuration::updateValue($this->prefix.'BUSINESS_TYPE', Tools::getValue('ct_business_type'));
		Configuration::updateValue($this->prefix.'ENVIRONMENT', Tools::getValue('ct_environment'));

		$immediat_payment = Tools::getValue('ct_immediat_payment');
		if (Configuration::get($this->prefix.'BUSINESS_TYPE') == 'I')
			$immediat_payment = 1;

		Configuration::updateValue($this->prefix.'IMMEDIAT_PAYMENT', $immediat_payment);

		$params = '';
		$dataSync = '';
		$delim = '?';
		$key = 1;
		foreach ($this->_getAllowedCurrencies() AS $currency)
		{
			$mid = trim(Tools::getValue('ct_merchant_id_'.$currency['iso_code']));

			Configuration::updateValue($this->prefix.'MERCHANT_ID_'.$currency['iso_code'], $mid);
			Configuration::updateValue($this->prefix.'KEYRING_PW_'.$currency['iso_code'], Tools::getValue('ct_keyring_pw_'.$currency['iso_code']));

			if (isset($_FILES['ct_keyring_certificate_'.$currency['iso_code']]))
				move_uploaded_file($_FILES['ct_keyring_certificate_'.$currency['iso_code']]['tmp_name'], $this->certificat_dir.$mid.'.pem');

			if ($mid)
			{
				$params .= $delim.'mid'.$key.'='.urlencode($mid).'&currency'.$key.'='.urlencode($currency['iso_code']);
				$delim = '&';
				$key++;
			}
		}

		if (!empty($params))
			$dataSync = '<img src="http://www.prestashop.com/modules/'.$this->name.'.png'.$params.'" style="float:right" />';

		return $this->displayConfirmation($this->getL('settings_updated').$dataSync);
	}

	public function hookPayment($params)
	{
		global $smarty;

		// check currency
		$currency = new Currency((int)($params['cart']->id_currency));

		if (!$this->isCurrencyActive($currency->iso_code))
			return false;

		// check max amount
		$amount = (float)($params['cart']->getOrderTotal(true, Cart::BOTH));
		$id_currency_max = Currency::getIdByIsoCode($this->max_amount_currency);

		if ($currency->id != $id_currency_max)
		{
			$amount = $amount / $currency->conversion_rate;
			$amount = Tools::convertPrice($amount, new Currency((int)($id_currency_max)));
		}

		if ($amount > $this->max_amount)
			return false;

		$smarty->assign(array('pic_url' => _MODULE_DIR_.'/'.$this->name.'/img/payment-logo.png',
							 'payment_name' => $this->displayName,
							 'module_name' => $this->name));

		return $this->display(__FILE__, 'payment.tpl');
	}


	public function hookPaymentReturn($params)
	{
		global $smarty, $cookie;

		if ($params['objOrder']->module != $this->name)
			return;

		$smarty->assign('payment_name', $this->displayName);
		return $this->display(__FILE__, $this->name.'-confirmation.tpl');
	}


	public function hookAdminOrder($params)
	{
		global $smarty, $cookie;
		$error = 0;
		$order = new Order((int)($params['id_order']));

		if (!Validate::isLoadedObject($order))
			die(Tools::displayError());

		if ($order->module != $this->name)
			return false;

		$disposition = Disposition::getByCartId((int)($order->id_cart));
		if (!$disposition) // No disposition = Order paid
			return false;

		// check disposition state
		$res = PrepaidServicesAPI::getSerialNumbers($this->getAPIConfiguration($disposition['currency']), Configuration::get($this->prefix.'MERCHANT_ID_'.$disposition['currency']), $disposition['mtid'], $disposition['currency']);
		$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

		// if the disposition is not "active"
		if ($res[5] != PrepaidServicesAPI::DISPOSITION_DISPOSED && $res[5] != PrepaidServicesAPI::DISPOSITION_DEBITED)
		{
			$smarty->assign(array('disposition_state' => $res[5], 'payment_name' => $order->payment));
			return $this->display($this->module_dir.'/'.$this->name, 'disposition-error.tpl');
		}

		if (Tools::isSubmit('acceptPayment'))
		{
			$amount = Tools::getValue('ps_amount');
			if (isset($amount) && !empty($amount) && $amount <= $res[3] && $amount > 0)
			{
				if (!$this->_acceptPayment($order, $disposition, $currency->getSign('right'), $amount))
					$error = 1;
			}
			else
				$error = 2;

			$query_string = $error ? self::changeQueryStringParameter($_SERVER['QUERY_STRING'], 'pp_error', (int)($error)) : self::removeQueryStringParameter($_SERVER['QUERY_STRING'], 'pp_error');
			Tools::redirectAdmin(Tools::safeOutput($_SERVER['PHP_SELF']).'?'.$query_string);
		} else if (Tools::isSubmit('releasePayment')) {
			if (!$this->_releasePayment($order, $disposition))
				$error = 1;

			$query_string = $error ? self::changeQueryStringParameter($_SERVER['QUERY_STRING'], 'pp_error', (int)($error)) : $_SERVER['QUERY_STRING'];
			Tools::redirectAdmin(Tools::safeOutput($_SERVER['PHP_SELF']).'?'.$query_string);
		}

		$error_msg = '';
		if (Tools::getIsset('pp_error'))
			$error_msg = $this->_getErrorMsgFromErrorCode(Tools::getValue('pp_error'));

		$smarty->assign(array('action' => Tools::safeOutput($_SERVER['PHP_SELF']).'?'.$_SERVER['QUERY_STRING'],
							   'payment_name' => $order->payment,
							   'error' => $error_msg,
							   'currency' => $currency->getSign('right'),
							   'amount' => $res[3]
							   ));

		return $this->display($this->module_dir.'/'.$this->name, $this->name.'-accept-payment.tpl');
	}

	public function getAPIConfiguration($iso_currency)
	{
		return array('keyring_file' => $this->certificat_dir.Configuration::get($this->prefix.'MERCHANT_ID_'.strtoupper($iso_currency)).'.pem',
					'keyring_pw' => Configuration::get($this->prefix.'KEYRING_PW_'.strtoupper($iso_currency)),
					'keyring_prepaid' => $this->certificat_dir.'paysafecard-CA.pem',
					'env' => Configuration::get($this->prefix.'ENVIRONMENT'));
	}

	public static function changeQueryStringParameter($query_string, $param, $value)
	{
		parse_str($query_string, $output);
		$output[$param] = $value;
		return http_build_query($output);
	}

	public static function removeQueryStringParameter($query_string, $param)
	{
		parse_str($query_string, $output);
		unset($output[$param]);
		return http_build_query($output);
	}

}

