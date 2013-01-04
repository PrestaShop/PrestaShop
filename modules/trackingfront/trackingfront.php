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

class TrackingFront extends Module
{
	public function __construct()
	{
		$this->name = 'trackingfront';
		$this->tab = 'shipping_logistics';
		$this->version = 1.0;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Tracking - Front office');
		$this->description = $this->l('Enable your affiliates to access their own statistics.');
	}
	
	public function postProcess()
	{
		if (Tools::isSubmit('ajaxProductFilter'))
		{
			$fakeEmployee = new Employee();
			$fakeEmployee->stats_date_from = $this->context->cookie->stats_date_from;
			$fakeEmployee->stats_date_to = $this->context->cookie->stats_date_to;
			$result = Db::getInstance()->getRow('
			SELECT `id_referrer`
			FROM `'._DB_PREFIX_.'referrer`
			WHERE `id_referrer` = '.(int)(Tools::getValue('id_referrer')).' AND `passwd` = \''.pSQL(Tools::getValue('token')).'\'');
			if (isset($result['id_referrer']) ? $result['id_referrer'] : false)
				Referrer::getAjaxProduct((int)(Tools::getValue('id_referrer')), (int)(Tools::getValue('id_product')), $fakeEmployee);
		}
		elseif (Tools::isSubmit('logout_tracking'))
		{
			unset($this->context->cookie->tracking_id);
			unset($this->context->cookie->tracking_passwd);
			Tools::redirect('modules/trackingfront/stats.php');
		}
		elseif (Tools::isSubmit('submitLoginTracking'))
		{
			$errors = array();
			$login = trim(Tools::getValue('login'));
			$passwd = trim(Tools::getValue('passwd'));
			if (empty($login))
				$errors[] = $this->l('login is required');
			elseif (!Validate::isGenericName($login))
				$errors[] = $this->l('invalid login');
			elseif (empty($passwd))
				$errors[] = $this->l('password is required');
			elseif (!Validate::isPasswd($passwd,1))
				$errors[] = $this->l('invalid password');
			else
			{
				$passwd = Tools::encrypt($passwd);
				$result = Db::getInstance()->getRow('
				SELECT `id_referrer`
				FROM `'._DB_PREFIX_.'referrer`
				WHERE `name` = \''.pSQL($login).'\' AND `passwd` = \''.pSQL($passwd).'\'');
				if (!isset($result['id_referrer']) OR !($tracking_id = (int)($result['id_referrer'])))
					$errors[] = $this->l('authentication failed');
				else
				{
					$this->context->cookie->tracking_id = $tracking_id;
					$this->context->cookie->tracking_passwd = $passwd;
					Tools::redirect('modules/trackingfront/stats.php');
				}
			}
			$this->smarty->assign('errors', $errors);
		}

		if (Tools::isSubmit('submitDatePicker'))
		{
			$this->context->cookie->stats_date_from = Tools::getValue('datepickerFrom');
			$this->context->cookie->stats_date_to = Tools::getValue('datepickerTo');
		}
		if (Tools::isSubmit('submitDateDay'))
		{
			$from = date('Y-m-d');
			$to = date('Y-m-d');
		}
		if (Tools::isSubmit('submitDateDayPrev'))
		{
			$yesterday = time() - 60*60*24;
			$from = date('Y-m-d', $yesterday);
			$to = date('Y-m-d', $yesterday);
		}
		if (Tools::isSubmit('submitDateMonth'))
		{
			$from = date('Y-m-01');
			$to = date('Y-m-t');
		}
		if (Tools::isSubmit('submitDateMonthPrev'))
		{
			$m = (date('m') == 1 ? 12 : date('m') - 1);
			$y = ($m == 12 ? date('Y') - 1 : date('Y'));
			$from = $y.'-'.$m.'-01';
			$to = $y.'-'.$m.date('-t', mktime(12, 0, 0, $m, 15, $y));
		}
		if (Tools::isSubmit('submitDateYear'))
		{
			$from = date('Y-01-01');
			$to = date('Y-12-31');
		}
		if (Tools::isSubmit('submitDateYearPrev'))
		{
			$from = (date('Y') - 1).date('-01-01');
			$to = (date('Y') - 1).date('-12-31');
		}
	}
	
	public function isLogged()
	{
		if (!$this->context->cookie->tracking_id OR !$this->context->cookie->tracking_passwd)
			return false;
		$result = Db::getInstance()->getRow('
		SELECT `id_referrer`
		FROM `'._DB_PREFIX_.'referrer`
		WHERE `id_referrer` = '.(int)($this->context->cookie->tracking_id).' AND `passwd` = \''.pSQL($this->context->cookie->tracking_passwd).'\'');
		return isset($result['id_referrer']) ? $result['id_referrer'] : false;
	}
		
	public function displayLogin()
	{
		return $this->display(__FILE__, 'login.tpl');
	}
	
	public function displayAccount()
	{
		if (!isset($this->context->cookie->stats_date_from))
			$this->context->cookie->stats_date_from = date('Y-m-01');
		if (!isset($this->context->cookie->stats_date_to))
			$this->context->cookie->stats_date_to = date('Y-m-t');
		Referrer::refreshCache(array(array('id_referrer' => (int)$this->context->cookie->tracking_id)));
		
		$referrer = new Referrer((int)($this->context->cookie->tracking_id));
		$this->smarty->assign('referrer', $referrer);
		$this->smarty->assign('datepickerFrom', $this->context->cookie->stats_date_from);
		$this->smarty->assign('datepickerTo', $this->context->cookie->stats_date_to);
		
		$displayTab = array(
			'uniqs' => $this->l('Unique visitors'),
			'visitors' => $this->l('Visitors'),
			'visits' => $this->l('Visits'),
			'pages' => $this->l('Pages viewed'),
			'registrations' => $this->l('Registrations'),
			'orders' => $this->l('Orders'),
			'base_fee' => $this->l('Base fee'),
			'percent_fee' => $this->l('Percent fee'),
			'click_fee' => $this->l('Click fee'),
			'sales' => $this->l('Sales'),
			'cart' => $this->l('Average cart'),
			'reg_rate' => $this->l('Registration rate'),
			'order_rate' => $this->l('Order rate'));
		$this->smarty->assign('displayTab', $displayTab);
		
		$products = Product::getSimpleProducts($this->context->language->id);
		$productsArray = array();
		foreach ($products as $product)
			$productsArray[] = $product['id_product'];
		
		$echo = '
		<script type="text/javascript">
			$("#datepickerFrom").datepicker({
				prevText:"",
				nextText:"",
				dateFormat:"yy-mm-dd"});
			$("#datepickerTo").datepicker({
				prevText:"",
				nextText:"",
				dateFormat:"yy-mm-dd"});
			
			function updateValues()
			{
				$.getJSON("stats.php",{ajaxProductFilter:1,id_referrer:'.$referrer->id.',token:"'.$this->context->cookie->tracking_passwd.'",id_product:0},
					function(j) {';
		foreach ($displayTab as $key => $value)
			$echo .= '$("#'.$key.'").html(j[0].'.$key.');';
		$echo .= '		}
				)
			}		

			var productIds = new Array(\''.implode('\',\'', $productsArray).'\');
			var referrerStatus = new Array();
			
			function newProductLine(id_referrer, result, color)
			{
				return \'\'+
				\'<tr id="trprid_\'+id_referrer+\'_\'+result.id_product+\'" style="background-color: rgb(\'+color+\', \'+color+\', \'+color+\');">\'+
				\'	<td align="center">\'+result.id_product+\'</td>\'+
				\'	<td>\'+result.product_name+\'</td>\'+
				\'	<td align="center">\'+result.uniqs+\'</td>\'+
				\'	<td align="center">\'+result.visits+\'</td>\'+
				\'	<td align="center">\'+result.pages+\'</td>\'+
				\'	<td align="center">\'+result.registrations+\'</td>\'+
				\'	<td align="center">\'+result.orders+\'</td>\'+
				\'	<td align="right">\'+result.sales+\'</td>\'+
				\'	<td align="right">\'+result.cart+\'</td>\'+
				\'	<td align="center">\'+result.reg_rate+\'</td>\'+
				\'	<td align="center">\'+result.order_rate+\'</td>\'+
				\'	<td align="center">\'+result.click_fee+\'</td>\'+
				\'	<td align="center">\'+result.base_fee+\'</td>\'+
				\'	<td align="center">\'+result.percent_fee+\'</td>\'+
				\'</tr>\';
			}
			
			function showProductLines()
			{
				var irow = 0;
				for (var i = 0; i < productIds.length; ++i)
					$.getJSON("stats.php",{ajaxProductFilter:1,token:"'.$this->context->cookie->tracking_passwd.'",id_referrer:'.$referrer->id.',id_product:productIds[i]},
						function(result) {
							var newLine = newProductLine('.$referrer->id.', result[0], (irow++%2 ? 204 : 238));
							$(newLine).hide().insertBefore($(\'#trid_dummy\')).fadeIn();
						}
					);
			}
		</script>';
		
		$echo2 = '
		<script type="text/javascript">
			updateValues();
			//showProductLines();
		</script>';
		
		return $this->display(__FILE__, 'header.tpl').$echo.$this->display(__FILE__, 'account.tpl').$echo2;
	}	
}

