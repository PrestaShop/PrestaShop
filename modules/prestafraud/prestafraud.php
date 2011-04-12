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
*  International Registred Trademark & Property of PrestaShop SA
*/


class PrestaFraud extends Module
{
	
	private $_html;
	public $_errors;
	private static $_trustUrl = 'http://trust.prestashop.com/';
	
	private $_activities;
	private $_payment_types;
	
	public function __construct()
	{
		$this->name = 'prestafraud';
	 	$this->tab = 'payment_security';
		$this->version = '0.99';
		$this->author = 'PrestaShop';
	
		parent::__construct();

		$this->displayName = 'PrestaShop Security';
		$this->description = 'Protect your store from fraudulent payments';
		
		$this->_activities = array(0 => $this->l('-- Please choose your main activity --'),
											1 => $this->l('Adult'),
											2 => $this->l('Animals and Pets'),
											3 => $this->l('Art and Culture'),
											4 => $this->l('Babies'),
											5 => $this->l('Beauty and Personal Care'),
											6 => $this->l('Cars'),
											7 => $this->l('Computer Hardware and Software'),
											8 => $this->l('Download'),
											9 => $this->l('Fashion and accessories'),
											10 => $this->l('Flowers, Gifts and Crafts'),
											11 => $this->l('Food and beverage'),
											12 => $this->l('HiFi, Photo and Video'),
											13 => $this->l('Home and Garden'),
											14 => $this->l('Home Appliances'),
											15 => $this->l('Jewelry'),
											16 => $this->l('Mobile and Telecom'),
											17 => $this->l('Services'),
											18 => $this->l('Shoes and accessories'),
											19 => $this->l('Sport and Entertainment'),
											20 => $this->l('Travel'));
		
		$this->_payment_types = array(0 => $this->l('Cheque'),
												1 => $this->l('Bankwire'),
												2 => $this->l('Credit card'),
												3 => $this->l('Credit card multiple'),
												4 => $this->l('Prepaid account (MoneyBookers, PayPal...)'));

	}
	
	public function install()
	{
		if (!parent::install() OR
						!$this->registerHook('updatecarrier') OR
						!$this->registerHook('newOrder') OR
						!$this->registerHook('adminOrder') OR
						!$this->registerHook('cart'))
			return false;
						
		if (!$sql = file_get_contents(dirname(__FILE__).'/install.sql'))
			die(Tools::displayError('File install.sql is not readable'));
		$sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
		$sql = preg_split("/;\s*[\r\n]+/", $sql);

		foreach ($sql as $query)
			if ($query AND sizeof($query) AND !Db::getInstance()->Execute(trim($query)))
				return false;
		return true;
	}
	
	public function uninstall()
	{
		parent::uninstall();
	}
	
	public function getContent()
	{
		$this->postProcess();
		$this->_displayConfiguration();
		
		return $this->_html;
	}
	
	private function _displayConfiguration()
	{
		global $cookie;
		$this->_html .= '<script type="text/javascript">
									$(document).ready(function() {
										$(\'#submitCreateAccount\').unbind(\'click\').click(function() {
										if (!$(\'#terms_and_conditions\').attr(\'checked\'))
										{
											alert(\''.$this->l('Please accept the terms of service.').'\');
											return false;
										}
									});										
									});
								</script>
		<fieldset><legend>'.$this->l('PrestaShop Security configuration').'</legend>
			<div id="choose_account">
				<center>
				<form>
					<input type="radio" '.(!Configuration::get('PS_TRUST_SHOP_ID') ? 'checked="checked"' : '').' onclick="$(\'#create_account\').show(); $(\'#module_configuration\').hide();" id="trust_account_on" name="trust_account" value="0"/> <b>'.$this->l('My shop does not have a PrestaShop Security account yet').'</b>&nbsp;&nbsp;&nbsp;
					<input type="radio" '.(Configuration::get('PS_TRUST_SHOP_ID') ? 'checked="checked"' : '').' onclick="$(\'#create_account\').hide(); $(\'#module_configuration\').show();"  id="trust_account_off" name="trust_account" value="1" /> <b>'.$this->l('I already have an account').'</b>
				</form>
				</center>
			</div>
			<div class="clear">&nbsp;</div>
			<div id="create_account" '.(Configuration::get('PS_TRUST_SHOP_ID') ? 'style="display:none;"' : '').'>
				<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post" name="prestashop_trust" id="prestashop_trust">
					<label>'.$this->l('Your email:').'</label>
					<div class="margin-form">
						<input type="text" style="width:200px;" name="email" />
					</div>
					<label>'.$this->l('Shop Url:').'</label>
					<div class="margin-form">
						<input type="text" style="width:400px;" name="shop_url" value="http://www.'.Tools::getHttpHost().__PS_BASE_URI__.'"/>
					</div>
					<div class="margin-form">
						<input id="terms_and_conditions" type="checkbox" value="1" />'.$this->l('I agree with the terms of PrestaShop Security service and i adhere to them unconditionally.').'</label>
					</div>
					<div id="terms" class="margin-form">';
					$terms = file_get_contents(self::$_trustUrl.'terms.php?lang='.Language::getIsoById((int)$cookie->id_lang));
					$this->_html .= $terms;
					$this->_html .= '</div>
					<div class="margin-form">
						<input class="button" type="submit" id="submitCreateAccount" name="submitCreateAccount" value="'.$this->l('Create account').'"/>
					</div>
				</form>
				<div class="clear">&nbsp;</div>
			</div>
			<div id="module_configuration" '.(!Configuration::get('PS_TRUST_SHOP_ID') ? 'style="display:none;"' : '').'>
			<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post" name="prestashop_trust" id="prestashop_trust">
				<label>'.$this->l('Shop ID:').'</label>
				<div class="margin-form">
					<input type="text" style="width:150px"  name="shop_id" value="'.Configuration::get('PS_TRUST_SHOP_ID').'"/>
				</div>
				<label>'.$this->l('Shop KEY:').'</label>
				<div class="margin-form">
					<input type="text" style="width:300px" name="shop_key" value="'.Configuration::get('PS_TRUST_SHOP_KEY').'"/>
				</div>
				<div class="clear">&nbsp;</div>
				<label>'.$this->l('Shop activity:').'
				<div class="margin-form">
					<select name="shop_activity">';
				foreach ($this->_activities AS $k => $activity)
					$this->_html .= '<option value="'.$k.'" '.($k == Configuration::get('PS_SHOP_ACTIVITY') ? 'selected="selected"' : '').'>'.$activity.'</option>';
			$this->_html .= '</select>
				</div>';

		$carriers = Carrier::getCarriers((int)$cookie->id_lang, true);
		$trust_carriers_type = $this->_getPrestaTrustCarriersType();
		$configured_carriers = $this->_getConfiguredCarriers();
		
		$this->_html .= '
				<label>'.$this->l('Carriers:').'</label>
				<div class="margin-form">
					<table cellspacing="0" cellpadding="0" class="table">
						<thead><tr><th>'.$this->l('Carrier').'</th><th>'.$this->l('Carrier Type').'</th></tr></thead><tbody>';

		foreach ($carriers AS $carrier)
		{
			$this->_html .= '<tr><td>'.$carrier['name'].'</td><td><select name="carrier_'.$carrier['id_carrier'].'">
			<option value="0">'.$this->l('Choose a carrier type...').'</option>';
			foreach ($this->_getPrestaTrustCarriersType() AS $type => $name)
				$this->_html .= '<option value="'.$type.'"'.((isset($configured_carriers[$carrier['id_carrier']]) AND $type == $configured_carriers[$carrier['id_carrier']]) ? ' selected="selected"' : '').'>'.$name.'</option>';
			$this->_html .= '</select></td>';
		}
			$this->_html .= '</tbody></table></margin>
			</div>';
		$modules = Module::getModulesOnDisk();
		$configured_payments = $this->_getConfiguredPayments();

		$this->_html .= '
				<label>'.$this->l('Payments:').'</label>
				<div class="margin-form">
					<table cellspacing="0" cellpadding="0" class="table">
						<thead><tr><th>'.$this->l('Payment Module').'</th><th>'.$this->l('Payment Type').'</th></tr></thead><tbody>';
		
		foreach ($modules AS $module)
		{
			if (!method_exists($module, 'hookPayment') OR !$module->id)
				continue;
			$this->_html .= '<tr><td>'.$module->displayName.'</td><td><select name="paymentmodule_'.$module->id.'">
			<option value="0">'.$this->l('Choose a payment type...').'</option>';
			foreach ($this->_payment_types AS $type => $name)
				$this->_html .= '<option value="'.$type.'"'.((isset($configured_payments[$module->id]) AND $type == $configured_payments[$module->id]) ? ' selected="true"' : '').'>'.$name.'</option>';
			$this->_html .= '</select></td>';
		}
			$this->_html .= '</tbody></table></margin>
			</div>';
		$this->_html .= '<center><input type="submit" name="submitSettings" value="'.$this->l('Save').'" class="button" /></center>
		</form>
		</div>
		</fieldset>';
		return $this->_html;
	}
	
	public function postProcess()
	{
		if (Tools::isSubmit('submitSettings'))
		{
			if (isset($_POST['login']))
				Configuration::updateValue('PS_TRUST_EMAIL', $_POST['email']);
			if (isset($_POST['passwd']))
				Configuration::updateValue('PS_TRUST_PASSWD', $_POST['passwd']);
			if ($activity = Tools::getValue('shop_activity'))
				Configuration::updateValue('PS_SHOP_ACTIVITY', $activity);
			$carriers_configuration = array();
			$payments_configuration = array();
			foreach($_POST AS $field => $val)
			{
				if (preg_match('/^carrier_([0-9]+)/Ui', $field, $res))
					$carriers_configuration[$res[1]] = $val;
				elseif (preg_match('/^paymentmodule_([0-9]+)/Ui', $field, $pay_res))
					$payments_configuration[$pay_res[1]] = $val;	
			}

			$this->_setCarriersConfiguration($carriers_configuration);
			$this->_setPaymentsConfiguration($payments_configuration);
		}
		elseif (Tools::isSubmit('submitCreateAccount'))
			$this->_html .= $this->_createAccount();
		
		if (sizeof($this->_errors))
		{
			$err = '';
			foreach ($this->_errors AS $error)
				$err .= $error.'<br />';
			$this->_html .= $this->displayError($err); 
		}
	}
	
	private function _createAccount()
	{
		if (!$email = Tools::getValue('email') OR !Validate::isEmail($email))
			$this->_errors[] = $this->l('Email is invalid');
		if (!$shop_url = Tools::getValue('shop_url') OR !Validate::isAbsoluteUrl($shop_url))
			$this->_errors[] = $this->l('Shop URL is invalid');

		if (sizeof($this->_errors))
			return false;

		$root = new SimpleXMLElement("<?xml version=\"1.0\"?><fraud_monitor></fraud_monitor>");
		$xml = $root->addChild('create_account');
		$xml->addChild('email', $email);
		$xml->addChild('shop_url', $shop_url);
		$result = self::_pushDatas($root->asXml());
		$xml_result = simplexml_load_string($result);
		if (!(int)$xml_result->create_account->result)
		{
			$this->_errors[] = (string)$xml_result->create_account->errors;
			return false;
		}
		Configuration::updateValue('PS_TRUST_SHOP_ID', (string)$xml_result->create_account->shop_id);
		Configuration::updateValue('PS_TRUST_SHOP_KEY', (string)$xml_result->create_account->shop_key);

		$this->_html .= $this->displayConfirmation('Account successfull created');
	}
	
	public function hookUpdateCarrier($params)
	{
		$this->_updateConfiguredCarrier((int)$params['id_carrier'], (int)$params['carrier']->id);
	}
	
	public function hookNewOrder($params)
	{
		if (!Configuration::get('PS_SHOP_ENABLE') OR !Configuration::get('PS_TRUST_SHOP_ID') OR !Configuration::get('PS_TRUST_SHOP_KEY'))
			return;
		$customer = new Customer((int)$params['order']->id_customer);
		
		$address_delivery = new Address((int)$params['order']->id_address_delivery);
		$address_invoice = new Address((int)$params['order']->id_address_invoice);
		$root = new SimpleXMLElement("<?xml version=\"1.0\"?><trust></trust>"); 
		$xml = $root->addChild('new_order');
		$shop_configuration = $xml->addChild('shop');

		$default_country = new Country((int)Configuration::get('PS_COUNTRY_DEFAULT'));
		$default_currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));
		$shop_configuration->addChild('default_country', $default_country->iso_code);
		$shop_configuration->addChild('default_currency', $default_currency->iso_code);
		$shop_configuration->addChild('shop_id', Configuration::get('PS_TRUST_SHOP_ID'));
		$shop_configuration->addChild('shop_password', Configuration::get('PS_TRUST_SHOP_KEY'));
		
		if ($activity = Configuration::get('PS_SHOP_ACTIVITY'))
			$shop_configuration->addChild('shop_activity', $activity);
		$customer_infos = $xml->addChild('customer');
		$customer_infos->addChild('customer_id', $customer->id);
		$customer_infos->addChild('lastname', $customer->lastname);
		$customer_infos->addChild('firstname', $customer->firstname);
		$customer_infos->addChild('email', $customer->email);
		$customer_infos->addChild('is_guest', (int)$customer->is_guest);
		$customer_infos->addChild('birthday', $customer->birthday);
		
		$delivery = $xml->addChild('delivery');
		$delivery->addChild('lastname', $address_delivery->lastname);
		$delivery->addChild('firstname', $address_delivery->firstname);
		$delivery->addChild('company', $address_delivery->company);
		$delivery->addChild('dni', $address_delivery->dni);
		$delivery->addChild('address1', $address_delivery->address1);
		$delivery->addChild('address2', $address_delivery->address2);
		$delivery->addChild('phone', $address_delivery->phone);
		$delivery->addChild('phone_mobile', $address_delivery->phone_mobile);
		$delivery->addChild('city', $address_delivery->city);
		$delivery->addChild('postcode', $address_delivery->postcode);
		if ($address_delivery->id_state !== NULL OR $address_delivery->id_state != '')
		{
			$State = new State((int)$address_delivery->id_state);
			$delivery->addChild('state', $State->iso_code);
		}
		$delivery->addChild('country', Country::getIsoById((int)$address_delivery->id_country));

		$invoice = $xml->addChild('invoice');
		$invoice->addChild('lastname', $address_invoice->lastname);
		$invoice->addChild('firstname', $address_invoice->firstname);
		$invoice->addChild('company', $address_invoice->company);
		$invoice->addChild('dni', $address_invoice->dni);
		$invoice->addChild('address1', $address_invoice->address1);
		$invoice->addChild('address2', $address_invoice->address2);
		$invoice->addChild('phone', $address_invoice->phone);
		$invoice->addChild('phone_mobile', $address_invoice->phone_mobile);
		$invoice->addChild('city', $address_invoice->city);
		$invoice->addChild('postcode', $address_invoice->postcode);
		if ($address_invoice->id_state !== NULL OR $address_invoice->id_state != '')
		{
			$State = new State((int)$address_invoice->id_state);
			$invoice->addChild('state', $State->iso_code);
		}
		$invoice->addChild('country', Country::getIsoById((int)$address_invoice->id_country));
		
		$infos = $this->_getCustomerInfos($params['order']);
		$history = $xml->addChild('customer_history');
		$history->addChild('customer_date_last_order', $infos['customer_date_last_order']);
		$history->addChild('customer_orders_valid_count', (int)$infos['customer_orders_valid_count']);
		$history->addChild('customer_orders_valid_sum', (float)$infos['customer_orders_valid_sum']);
		$history->addChild('customer_orders_unvalid_count', (int)$infos['customer_orders_unvalid_count']);
		$history->addChild('customer_orders_unvalid_sum', (float)$infos['customer_orders_unvalid_sum']);
		$history->addChild('customer_ip_addresses_history', $infos['customer_ip_addresses_history']);
		
		$history->addChild('customer_date_add', $customer->date_add);

		$product_list = $params['order']->getProductsDetail();
		
		$order = $xml->addChild('order_detail');
		$order->addChild('order_id', (int)$params['order']->id);
		$order->addChild('order_amount', $params['order']->total_paid);
		$currency = new Currency((int)$params['order']->id_currency);
		$order->addChild('currency', $currency->iso_code);
		$products = $order->addChild('products');
		foreach ($product_list AS $p)
		{
			$products->addChild('name', $p['product_name']);
			$products->addChild('price', $p['product_price']);
			$products->addChild('quantity', $p['product_quantity']);
			$products->addChild('is_virtual', (int)!empty($p['download_hash']));
		}

		$sources = ConnectionsSource::getOrderSources($params['order']->id);
		$referers = array();
		if ($sources)
			foreach ($sources AS $source)
				$referers[] = $source['http_referer'];
		if (sizeof($referers))
			$order->addChild('order_referers', serialize($referers));
		
		$configured_payments = $this->_getConfiguredPayments();
		$paymentModule = Module::getInstanceByName($params['order']->module);
		$order->addChild('payment_name', $paymentModule->displayName);
		$order->addChild('payment_type', (int)$configured_payments[$paymentModule->id]);
		$order->addChild('order_date', $params['order']->date_add);
		$order->addChild('order_ip_address', $this->_getIpByCart($id_cart));

		$carrier = new Carrier((int)$params['order']->id_carrier);
		$carrier_infos = $order->addChild('carrier_infos');
		$carrier_infos->addChild('name', $carrier->name);
		$carriers_type = $this->_getConfiguredCarriers();

		$carrier_infos->addChild('type', $carriers_type[$carrier->id]);
		if (self::_pushDatas($root->asXml()) !== false)
			Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'prestafraud_orders (id_order) VALUES('.(int)$params['order']->id.')');
		return true;
	}
	
	public function hookCart($params)
	{
		if ($_SERVER['REMOTE_ADDR'] == '0.0.0.0' OR $_SERVER['REMOTE_ADDR'] == '' OR $_SERVER['REMOTE_ADDR'] === false OR $_SERVER['REMOTE_ADDR'] === '::1')
			return;
		if (!$params['cart']->id)
			return;
		$res = Db::getInstance()->getValue('
		SELECT `id_cart`
		FROM '._DB_PREFIX_.'prestafraud_carts
		WHERE id_cart='.(int)($params['cart']->id));
		if ($res)
			Db::getInstance()->Execute('
			UPDATE `'._DB_PREFIX_.'prestafraud_carts`
			SET `ip_address` = '.ip2long($_SERVER['REMOTE_ADDR']).', `date` = \''.pSQL(date('Y-m-d H:i:s')).'\'
			WHERE `id_cart` = '.(int)($params['cart']->id).' LIMIT 1');
		else
			Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'prestafraud_carts` (`id_cart`, `ip_address`, `date`)
			VALUES ('.(int)($params['cart']->id).', '.ip2long($_SERVER['REMOTE_ADDR']).',\''.date('Y-m-d H:i:s').'\')');
		return true;
	}
	
	private function _getCustomerInfos($order)
	{
		$last_order = Db::getInstance()->getValue('SELECT date_add
		FROM '._DB_PREFIX_.'orders
		WHERE id_customer='.(int)$order->id_customer.' AND id_order != '.(int)$order->id.'
		ORDER BY date_add DESC');

		$orders_valid = Db::getInstance()->getRow('
		SELECT COUNT(*) nb_valid, SUM(total_paid) sum_valid
		FROM '._DB_PREFIX_.'orders
		WHERE valid=1 AND id_order!='.(int)$order->id.' AND id_customer = '.(int)$order->id_customer);

		$orders_unvalid  = Db::getInstance()->getRow('
		SELECT COUNT(*) nb_unvalid, SUM(total_paid) sum_unvalid
		FROM '._DB_PREFIX_.'orders
		WHERE valid=0 AND id_order!='.(int)$order->id.' AND id_customer = '.(int)$order->id_customer);

		$ip_addresses = Db::getInstance()->ExecuteS('
		SELECT c.ip_address
		FROM '._DB_PREFIX_.'guest g
		LEFT JOIN '._DB_PREFIX_.'connections c ON (c.id_guest = g.id_guest)
		WHERE g.id_customer='.(int)$order->id_customer.'
		ORDER BY c.id_connections DESC');
		$address_list = array();
		foreach ($ip_addresses AS $ip)
			$address_list[] = $ip['ip_address'];

		return array(
			'customer_date_last_order' => $last_order,
			'customer_orders_valid_count' => $orders_valid['nb_valid'],
			'customer_orders_valid_sum' => $orders_valid['sum_valid'],
			'customer_orders_unvalid_count' => $orders_unvalid['nb_unvalid'],
			'customer_orders_unvalid_sum' => $orders_unvalid['sum_unvalid'],
			'customer_ip_addresses_history' => serialize($address_list)
		);	
	}
	
	private static function _getIpByCart($id_cart)
	{
		return long2ip(Db::getInstance()->getValue('
		SELECT `ip_address`
		FROM '._DB_PREFIX_.'prestafraud_carts
		WHERE id_cart = '.(int)$id_cart));
	}
	
	public function hookAdminOrder($params)
	{
		global $cookie;
		$id_order = Db::getInstance()->getValue('SELECT id_order FROM '._DB_PREFIX_.'prestafraud_orders WHERE id_order = '.(int)$params['id_order']);
		$this->_html .= '<br /><fieldset><legend>'.$this->l('PrestaShop Security').'</legend>';
		if (!$id_order)
			$this->_html .= $this->l('This order has not been sent to PrestaShop Security.');
		else
		{
			$scoring = $this->_getScoring((int)$id_order, $cookie->id_lang);
			$this->_html .= '<p><b>'.$this->l('Scoring:').'</b> '.($scoring['scoring'] < 0 ? $this->l('Unknown') : (float)$scoring['scoring']).'</p>
			<p><b>'.$this->l('Comment:').'</b> '.htmlentities($scoring['comment']).'</p>
			<p><center><a target="_BLANK" href="'.self::$_trustUrl.'fraud_report.php?shop_id='.Configuration::get('PS_TRUST_SHOP_ID').'&shop_key='.Configuration::get('PS_TRUST_SHOP_KEY').'&order_id='.$id_order.'">'.$this->l('Report this order as a fraud to PrestaShop').'</a></center></p>';
		}
		$this->_html .= '</fieldset>';
		return $this->_html;
	}
	
	
		
	private function _getScoring($id_order, $id_lang)
	{
		if (!$scoring = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'prestafraud_orders WHERE scoring IS NOT NULL AND id_order = '.(int)$id_order))
		{
			$root = new SimpleXMLElement("<?xml version=\"1.0\"?><trust></trust>"); 
			$xml = $root->addChild('get_scoring');
			$xml->addChild('shop_id', Configuration::get('PS_TRUST_SHOP_ID'));
			$xml->addChild('shop_password', Configuration::get('PS_TRUST_SHOP_KEY'));
			$xml->addChild('id_order', (int)$id_order);
			$xml->addChild('lang', Language::getIsoById((int)$id_lang));
			$result = self::_pushDatas($root->asXml());
			if (!$result)
				return false;
			$xml = simplexml_load_string($result);
			if ((int)$xml->check_scoring->status != -1)
				Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'prestafraud_orders SET scoring = '.(float)$xml->check_scoring->scoring.', comment = \''.pSQL($xml->check_scoring->comment).'\' WHERE id_order='.(int)$id_order);
			$scoring = 	array('scoring' => (float)$xml->check_scoring->scoring, 'comment' => (string)$xml->check_scoring->comment);
		}
		return $scoring;
	}
	
	private function _getPrestaTrustCarriersType()
	{
		return array(
		'1' => $this->l('Pick up in-store'),
		'2' => $this->l('Withdrawal point'),
		'3' => $this->l('Slow shipping more than 3 days'),
		'4' => $this->l('Shipping express'));
	}
	
	private function _getConfiguredCarriers()
	{
		$res =  Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'prestafraud_carrier');
		$carriers = array();
		foreach ($res AS $row)
			$carriers[$row['id_carrier']] = $row['id_prestafraud_carrier_type'];
		
		return $carriers;
	}
	
	private function _getConfiguredPayments()
	{
		$res =  Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'prestafraud_payment');
		$payments = array();
		foreach ($res AS $row)
			$payments[$row['id_module']] = $row['id_prestafraud_payment_type'];
		
		return $payments;
	}
	
	private function _setCarriersConfiguration($carriers)
	{
		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'prestafraud_carrier');
		foreach ($carriers AS $id_carrier => $id_carrier_type)
			Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'prestafraud_carrier (id_carrier, id_prestafraud_carrier_type) VALUES ('.(int)$id_carrier.', '.(int)$id_carrier_type.')');
	}
	
	private function _setPaymentsConfiguration($payments)
	{
		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'prestafraud_payment');
		foreach ($payments AS $id_module => $id_payment_type)
			Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'prestafraud_payment (id_module, id_prestafraud_payment_type) VALUES ('.(int)$id_module.', '.(int)$id_payment_type.')');
	}
	
	private function _updateConfiguredCarrier($old, $new)
	{
		return Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'prestafraud_carrier SET id_carrier='.(int)$new.' WHERE id_carrier='.(int)$old);
	}
	
	private static function _pushDatas($datas)
	{
		if (function_exists('curl_init'))
		{
			$ch = curl_init(self::$_trustUrl);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('xml' => $datas));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			$content = curl_exec($ch);
			curl_close($ch);
			return $content;
		}
		elseif (function_exists('file_get_contents'))
		{
			$context = stream_context_create(array('http' => array('timeout' => 5)));
			return file_get_contents(self::$_trustUrl.'?xml='.urlencode(str_replace("\r", "\n", '', $datas)), $context);
		}
		else
			return false;
	}
}
