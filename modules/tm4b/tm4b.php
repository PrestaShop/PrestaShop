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

class Tm4b extends Module
{
	private $_html = '';
	private $_postErrors = array();
	private $_postSucess;
	
	private $_data;
	private $_password;
	private $_user;
	private $_originator;
	private $_route;
	private $_simulation;
	private $_new_order_numbers;

	private	$_alert_new_order_active;
	private	$_alert_update_quantity_active;
	private	$_daily_report_active;
	
	const __TM4B_LOWBALANCE__ = '20';
	const __TM4B_NUMBER_DELIMITOR__ = ',';

	static private $_tpl_sms_files				= array(
		'name' => array(
			'new_orders' => 'sms_new_order',
			'out_of_stock' => 'sms_out_of_stock'
			),
		'ext' => array(
			'new_orders' => '.txt',
			'out_of_stock' => '.txt'
			)
		);
	
	public function __construct()
	{
		$this->name = 'tm4b';
		$this->displayName = 'SMS Tm4b';
		$this->description = $this->l('Sends an SMS for each new order');
		$this->tab = 'administration';
		$this->version = 1.1;
		$this->author = 'PrestaShop';
		
		parent::__construct();
		
		if ($this->id)
		{
			$this->_data = array('shopname' => Configuration::get('PS_SHOP_NAME'));
			
			/* Get config vars */		 
			$this->_password = Configuration::get('TM4B_PASSWORD');
			$this->_user = Configuration::get('TM4B_USER');
			$this->_originator = Configuration::get('TM4B_ORIGINATOR');
			$this->_route = Configuration::get('TM4B_ROUTE');
			$this->_simulation = Configuration::get('TM4B_SIM');
			$this->_new_order_numbers =  Configuration::get('TM4B_NEW_ORDER_NUMBERS');
			
			$this->_alert_new_order_active = Configuration::get('TM4B_ALERT_NO_ACTIVE');
			$this->_alert_update_quantity_active = Configuration::get('TM4B_ALERT_UQ_ACTIVE');
			$this->_daily_report_active = Configuration::get('TM4B_DAILY_REPORT_ACTIVE');
		}
		
		$this->displayName = 'SMS Tm4b';
		$this->description = $this->l('Sends an SMS for each new order');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your info?');
	}
   
	public function	install()
	{
		if (!parent::install() OR
			!$this->registerHook('newOrder') OR
			!$this->registerHook('updateQuantity'))
			return false;
			
		Configuration::updateValue('TM4B_SIM', 1);
		Configuration::updateValue('TM4B_ALERT_NO_ACTIVE', 1);
		Configuration::updateValue('TM4B_ALERT_UQ_ACTIVE', 1);
		Configuration::updateValue('TM4B_DAILY_REPORT_ACTIVE', 0);
		return true;
	}
	
	public function uninstall()
	{
		Configuration::deleteByName('TM4B_PASSWORD');
		Configuration::deleteByName('TM4B_USER');
		Configuration::deleteByName('TM4B_ORIGINATOR');
		Configuration::deleteByName('TM4B_ROUTE');
		Configuration::deleteByName('TM4B_SIM');
		Configuration::deleteByName('TM4B_NEW_ORDER_NUMBERS');
		Configuration::deleteByName('TM4B_ALERT_NO_ACTIVE');
		Configuration::deleteByName('TM4B_ALERT_UQ_ACTIVE');
		Configuration::deleteByName('TM4B_DAILY_REPORT_ACTIVE');
		Configuration::deleteByName('TM4B_LAST_REPORT');
		
		return parent::uninstall();
	}
	
	private function _getTplBody($tpl_file, $vars = array())
	{
		$iso = Language::getIsoById((int)(Configuration::get('PS_LANG_DEFAULT')));
		$file = dirname(__FILE__).'/mails/'.$iso.'/'.$tpl_file;
		if (!file_exists($file))
			die($file);
		$tpl = file($file);
		$template = str_replace(array_keys($vars), array_values($vars), $tpl);
		return (implode("\n", $template));
	}
	
	public function hookNewOrder($params)
	{
		include_once (dirname(__FILE__).'/classes/Tm4bSms.php');
		
		if ( !(int)($this->_alert_new_order_active) OR empty($this->_user) OR empty($this->_password)
			OR empty($this->_new_order_numbers))
			return ;
		$order = $params['order'];
		$customer = $params['customer'];
		$currency = $params['currency'];
		
		$templateVars = array(
		'{firstname}' => utf8_decode($customer->firstname),
		'{lastname}' => utf8_decode($customer->lastname),
		'{order_name}' => sprintf("%06d", $order->id),
		'{shop_name}' => Configuration::get('PS_SHOP_NAME'),
		'{payment}' => $order->payment,
		'{total_paid}' => $order->total_paid,
		'{currency}' => $currency->sign);

		$body = $this->_getTplBody(self::$_tpl_sms_files['name']['new_orders'].self::$_tpl_sms_files['ext']['new_orders'], $templateVars);
		
		$sms = new Tm4bSms($this->_user, $this->_password, $this->_route, $this->_originator);
		$sms->msg = $body;
		$numbers = explode(self::__TM4B_NUMBER_DELIMITOR__, $this->_new_order_numbers);
		foreach ($numbers as $number)
			if ($number != '')
				$sms->addRecipient($number);
		$sms->Send($this->_simulation);
	}	

	public function hookUpdateQuantity($params)
	{
		if (!(int)($this->_alert_update_quantity_active) OR empty($this->_new_order_numbers))
			return ;
	
		$product = $params['product'];
		$order = $params['order'];
		
		$qty = (int)($params['product']['quantity_attribute'] ? $params['product']['quantity_attribute'] : $params['product']['stock_quantity']) - (int)($params['product']['quantity']);
		if ($qty <= (int)(Configuration::get('PS_LAST_QTIES')))
		{
			$templateVars = array(
			'{last_qty}' => (int)(Configuration::get('PS_LAST_QTIES')),
			'{qty}' => $qty,
			'{product}' => strval($params['product']['name']));

			$body = $this->_getTplBody(self::$_tpl_sms_files['name']['out_of_stock'].self::$_tpl_sms_files['ext']['out_of_stock'], $templateVars);
		}
		
		if (!empty($body))
		{
			$sms = new Tm4bSms($this->_user, $this->_password, $this->_route, $this->_originator);
			$sms->msg = $body;
			$numbers = explode(self::__TM4B_NUMBER_DELIMITOR__, $this->_new_order_numbers);
			foreach ($numbers as $number)
				if ($number != '')
					$sms->addRecipient($number);
			$sms->Send($this->_simulation);
		}
	}

	public function getContent()
	{
		include_once (dirname(__FILE__).'/classes/Tm4bSms.php');
		$this->_html = '<h2>'.$this->displayName.'</h2>';

		if (!empty($_POST))
		{
			if (isset($_POST['btnTestSms']))
			{
				if (!empty($this->_user) AND !empty($this->_password) AND !empty($_POST['test_number']) AND is_numeric($_POST['test_number']))
				{
					$sms = new Tm4bSms($this->_user, $this->_password, $this->_route, $this->_originator);
					$sms->msg = 'Test SMS for your PrestaShop website';
					$sms->addRecipient($_POST['test_number']);
					$ret = $sms->Send($this->_simulation);
					if ($sms->isSent())
						$this->_html .= $this->displayConfirmation($this->l('Message sent'));
					else
						$this->_html .= $this->displayError($this->l('Error while sending message'));
				}
				else
					$this->_html .= $this->displayError($this->l('Login and phone number'));
			}
			else
			{
				$this->_postValidation();
				if (!sizeof($this->_postErrors))
					$this->_postProcess();
				else
					foreach ($this->_postErrors AS $err)
						$this->_html .= $this->displayError($err);
			}
		}
		
		$this->_displayTm4b();
		$this->_displayForm();

		return $this->_html;
	}
	
	private function _displayTm4b()
	{
		include_once (dirname(__FILE__).'/classes/Tm4bSms.php');
		
		$testsms_txt = 'Send';
		
		$this->_html .= '
		<fieldset><legend><img src="'.$this->_path.'informations.gif" alt="" title="" /> '.$this->l('Information').'</legend>
			<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
				<label>'.$this->l('Send test SMS:').'</label>
				<div class="margin-form"><input onclick="this.value=\'\'" type="text" style="margin-bottom:10px;" name="test_number" size="30" value="'.
				((isset($_POST) AND isset($_POST['test_number'])) ? $_POST['test_number'] : $this->l('Enter your phone number')).'">
				<input class="button" name="btnTestSms" value="'.$testsms_txt.'" type="submit" style="margin-bottom:10px;" /><br />'.$this->l('ex: 33642424242').'</div>';
				if (!empty($this->_user) AND !empty($this->_password))
				{
					$sms = new Tm4bSms($this->_user, $this->_password, $this->_route, $this->_originator);
					$credits = $sms->CheckCredits();
					$color = ($credits < self::__TM4B_LOWBALANCE__ ? '#900' : '#080');
					$this->_html .= '<label>'.$this->l('SMS credits:').'</label>
					<div class="margin-form" style="color:#000000; font-size:12px;">'.$this->l('You have').' <span style="font-weight: bold; color: '.$color.';">'.$credits.'</span> '.$this->l('credits').'</div>';
				}
		$this->_html .= '
			</form>
		</fieldset><br />';
	}
	
	private function _displayForm()
	{
				if (!isset($_POST['btnSubmit']))
		{
			if ($this->_user)
			{
				$_POST['user'] = $this->_user;
				$_POST['password'] = $this->_password;
				$_POST['route'] = $this->_route;
				$_POST['originator'] = $this->_originator;
				$_POST['simulation'] = $this->_simulation;
				$_POST['new_order_numbers'] = str_replace(self::__TM4B_NUMBER_DELIMITOR__, "\n", $this->_new_order_numbers);
				$_POST['alert_new_order'] = $this->_alert_new_order_active;
				$_POST['alert_update_quantity'] = $this->_alert_update_quantity_active;
				$_POST['daily_report'] = $this->_daily_report_active;
			}
		}
		
		$this->_html .= '<fieldset><legend><img src="'.$this->_path.'prefs.gif" alt="" title="" /> '.$this->l('Settings').'</legend>
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<label>'.$this->l('Username:').'</label>
			<div class="margin-form"><input type="text" name="user" value="'.(isset($_POST['user']) ? $_POST['user'] : '').'" /></div>
			<label>'.$this->l('Password:').'</label>
			<div class="margin-form"><input type="text" name="password" value="'.(isset($_POST['password']) ? $_POST['password'] : '').'" /></div>
			<label>'.$this->l('Relay:').'</label>
			<div class="margin-form"><select name="route">
				<option value="GD01" '.(isset($_POST['route']) ? ($_POST['route'] == 'GD01' ? 'selected="selected"' : '') : '').'>Global I</option>
				<option value="GD02" '.(isset($_POST['route']) ? ($_POST['route'] == 'GD02' ? 'selected="selected"' : '') : '').'>Global II</option>
				<option value="USS1" '.(isset($_POST['route']) ? ($_POST['route'] == 'USS1' ? 'selected="selected"' : '') : '').'>USA Direct</option>
				</select></div>
			<label>'.$this->l('SMS sender\'s phone #').'</label>
			<div class="margin-form"><input type="text" name="originator" value="'.(isset($_POST['originator']) ? $_POST['originator'] : '').'" style="margin-bottom:10px;" /><br />'.$this->l('ex: 33642424242').'</div>
			<label>'.$this->l('Mode:').'</label>
			<div class="margin-form"><input type="radio" name="simulation" value="1" style="vertical-align: middle;" '.( (isset($_POST['simulation']) AND $_POST['simulation'] == '1' ) ? 'checked' : '').' /> <span style="color: #900;">'.$this->l('Simulation').'</span>
			&nbsp;<input type="radio" name="simulation" value="0" style="vertical-align: middle;" '.( (!isset($_POST['simulation']) OR $_POST['simulation'] == '0') ? 'checked' : '').' /> <span style="color: #080;">'.$this->l('Production').'</div>
			<br />
			<label>'.$this->l('Alerts on new order:').'</label>
			<div class="margin-form"><div style="color:#000000; font-size:12px; margin-bottom:6px"><input type="checkbox" value="1" name="alert_new_order" '.( (isset($_POST['alert_new_order']) AND $_POST['alert_new_order'] == '1') ? 'checked' : '').' />&nbsp;'.$this->l('Yes').'</div>'.$this->l('Send SMS if a new order is made').'</div>
			<label class="clear">'.$this->l('Alerts on product quantity:').'</label>
			<div class="margin-form"><div style="color:#000000; font-size:12px; margin-bottom:6px"><input type="checkbox" value="1" name="alert_update_quantity" '.( (isset($_POST['alert_update_quantity']) AND $_POST['alert_update_quantity'] == '1') ? 'checked' : '').' />&nbsp;'.$this->l('Yes').'</div>'.$this->l('Send SMS if the stock of product is updated').'</div>
			<label class="clear">'.$this->l('Daily report:').'</label>
			<div class="margin-form"><div style="color:#000000; font-size:12px; margin-bottom:6px"><input type="checkbox" value="1" name="daily_report" '.( (isset($_POST['daily_report']) AND $_POST['daily_report'] == '1') ? 'checked' : '').' />&nbsp;'.$this->l('Yes').'</div>'.$this->l('Send a daily stats report - You must set a CRON to').' /modules/tm4b/cron.php</div>
			<br />
			<label>'.$this->l('SMS receiver\'s phone #').'</label>
			<div class="margin-form"><input type="text" name="new_order_numbers" size="30" value="'.(isset($_POST['new_order_numbers']) ? $_POST['new_order_numbers'] : '').'" style="margin-bottom:10px;" /><br />'.$this->l('ex: 33642424242').'</div>
			<br />
			<div class="margin-form"><input class="button" name="btnSubmit" value="'.$this->l('Update settings').'" type="submit" /></div>
		</form></fieldset>';
	}

	private function _postProcess()
	{
		Configuration::updateValue('TM4B_PASSWORD', $_POST['password']);
		Configuration::updateValue('TM4B_USER', $_POST['user']);
		Configuration::updateValue('TM4B_ORIGINATOR', $_POST['originator']);
		Configuration::updateValue('TM4B_ROUTE', $_POST['route']);
		Configuration::updateValue('TM4B_SIM', $_POST['simulation']);
		Configuration::updateValue('TM4B_ALERT_NO_ACTIVE', isset($_POST['alert_new_order']) ? 1 : 0);
		Configuration::updateValue('TM4B_ALERT_UQ_ACTIVE', isset($_POST['alert_update_quantity']) ? 1 : 0);
		Configuration::updateValue('TM4B_DAILY_REPORT_ACTIVE', isset($_POST['daily_report']) ? 1 : 0);

		$numbers = explode("\n", $_POST['new_order_numbers']);
		$this->_new_order_numbers = '';
		foreach ($numbers as $number)
		{
		  if (preg_match("/([0-9]+)/", $number, $regs))
			$this->_new_order_numbers .= $regs[1].self::__TM4B_NUMBER_DELIMITOR__;
		}
		Configuration::updateValue('TM4B_NEW_ORDER_NUMBERS', $this->_new_order_numbers);
		$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
	}

	private function _postValidation()
	{
		if (empty($_POST['user']))
			$this->_postErrors[] = $this->l('Username is mandatory');
		elseif (empty($_POST['password']))
			$this->_postErrors[] = $this->l('Password is mandatory');
		elseif (empty($_POST['route']) OR ($_POST['route'] != 'GD01' AND $_POST['route'] != 'GD02' AND $_POST['route'] != 'USS1'))
			$this->_postErrors[] = $this->l('Route is mandatory');
		elseif (empty($_POST['originator']))
			$this->_postErrors[] = $this->l('Origin is mandatory');
		elseif (!isset($_POST['simulation']) OR ($_POST['simulation'] != 0 AND $_POST['simulation'] != 1))
			$this->_postErrors[] = $this->l('Mode is mandatory');
		elseif (empty($_POST['new_order_numbers']))
			$this->_postErrors[] = $this->l('Please enter a phone number');
		elseif (preg_match('/([^0-9[:space:],])/', $_POST['new_order_numbers'], $regs))
			$this->_postErrors[]  = $this->l('Phone number invalid');
	}
	
	public function getStatsBody()
	{
		$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
		$currency->sign = $currency->iso_code;
		$query = '
		SELECT SUM(o.`total_paid_real`) as total_sales, COUNT(o.`total_paid_real`) as total_orders
		FROM `'._DB_PREFIX_.'orders` o
		WHERE (
			SELECT IF(os.`id_order_state` = 8, 0, 1)
			FROM `'._DB_PREFIX_.'orders` oo
			LEFT JOIN `'._DB_PREFIX_.'order_history` oh ON oh.`id_order` = oo.`id_order`
			LEFT JOIN `'._DB_PREFIX_.'order_state` os ON os.`id_order_state` = oh.`id_order_state`
			WHERE oo.`id_order` = o.`id_order`
			ORDER BY oh.`date_add` DESC, oh.`id_order_history` DESC
			LIMIT 1
		) = 1 ';
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query.'
		AND o.`date_add` >= DATE_SUB(\''.date('Y-m-d').' 20:00:00\', INTERVAL 1 DAY)
		AND o.`date_add` < \''.date('Y-m-d').' 20:00:00\'');
		$result2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query.'AND o.`date_add` LIKE \''.date('Y-m').'-%\'');
		
		return date('Y-m-d')."\n".
		$this->l('Orders:').' '.(int)($result['total_orders'])."\n".
		$this->l('Sales:').' '.Tools::displayPrice($result['total_sales'], $currency, true)."\n".
		'('.$this->l('Month:').' '.Tools::displayPrice($result2['total_sales'], $currency, true).')';
	}
}


