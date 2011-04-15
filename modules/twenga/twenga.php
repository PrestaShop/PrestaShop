<?php
/**
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
 *  @copyright 2007-2011 PrestaShop SA : 6 rue lacepede, 75005 PARIS
 *  @version  Release: $Revision: 1.4 $
 *  @license	http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 **/

/**
 * Twenga module allow to use the Twenga API to :
 * 1. subscribe to their Ready to Sell engine,
 * 2. activate a tracking for order process if user has been used twenga engine,
 * 3. submit a xml feed of shop products to Twenga. 
 * @author Nans Pellicari - Prestashop
 * @version 1.3
 */

class Twenga extends PaymentModule
{
	/**
	 * path to load each needed files
	 * @var string
	 */
	private static $base_dir;
	
	/**
	 * Url path to access of module file.
	 * @var string
	 */
	private static $base_path;
	/**
	 * @var TwengaObj
	 */
	private static $obj_twenga;
	
	/**
	 * @var PrestashopStats
	 */
	private static $obj_ps_stats;
	
	/**
	 * @var string url used for the subscription to Twenga and prestashop
	 */ 
	private $site_url;
	
	/**
	 * @var string url to acces of the product list for Twenga
	 */
	private $feed_url;
	
	/**
	 * @var string url returned by Twenga API
	 */
	private $inscription_url;
	
	/**
	 * @var string used for displaying html
	 */
	private $_html;
	
	/**
	 * @var string
	 */
	private $current_index;
	
	/**
	 * @var string
	 */
	private $token;
	
	/**
	 * Countries where Twenga works.
	 * need to be in lowercase
	 * @var array
	 */
	public $limited_countries = array('fr', 'de', 'gb', 'uk', 'it', 'es');

	private $_currentIsoCodeCountry = NULL;
	
	/**
	 * The current country iso code for the shop.
	 * @var string
	 */
	private static $shop_country;

	public function __construct()
	{
		// Basic vars
		global $currentIndex;
		$this->current_index = $currentIndex;
		$this->token = Tools::getValue('token');
	 	$this->name = 'twenga';
	 	$this->tab = 'smart_shopping';
	 	$this->version = '1.5';
		
	 	parent::__construct();
	
		$this->displayName = $this->l('Twenga API');
		$this->description = $this->l('Module role: export of your products on Twenga and installation of the sales tracking brought by Twenga (requires to sign up to Twenga)');

		// For Twenga subscription
		$protocol = 'http://';
		if (isset($_SERVER['https']) && $_SERVER['https'] != 'off')
			$protocol = 'http://';
		$this->site_url = Tools::htmlentitiesutf8($protocol.$_SERVER['HTTP_HOST'].__PS_BASE_URI__);
		self::$base_dir = _PS_ROOT_DIR_.'/modules/twenga/';
		self::$base_path = $this->site_url.'/modules/twenga/';
		$this->feed_url = self::$base_path.'export.php';
		
		self::$shop_country = Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT'));
		
		require_once realpath(self::$base_dir.'/lib/PrestashopStats.php');
		require_once realpath(self::$base_dir.'/lib/TwengaObj.php');
		
		// set the base dir to load files needed for the TwengaObj class 
		TwengaObj::$base_dir = self::$base_dir.'/lib';
		
		TwengaObj::setTranslationObject($this);
		TwengaException::setTranslationObject($this);
		if (!in_array(strtolower(self::$shop_country), $this->limited_countries))
		{
			$this->warning = $this->l('Twenga module works only in specific countries (iso code list:').' '.implode(', ',$this->limited_countries).').';
			return false;
		}
		// instanciate (just once) the TwengaObj and PrestashopStats
		if (self::$obj_twenga === NULL)
			self::$obj_twenga = new TwengaObj();
		if (self::$obj_ps_stats === NULL)
			self::$obj_ps_stats = new PrestashopStats($this->site_url);
		$this->_initCurrentIsoCodeCountry();
	}

	/**
	 * For uninstall just need to delete the Merchant Login.
	 * @return bool see parent class.
	 */
	public function uninstall()
	{
		if (!parent::uninstall()
		OR !self::$obj_twenga->deleteMerchantLogin())
			return false;
		return true;
	}

	private function _initCurrentIsoCodeCountry()
	{
		global $cookie;

		$country = Db::getInstance()->ExecuteS('
			SELECT c.iso_code as iso
			FROM '._DB_PREFIX_.'country as c
			LEFT JOIN '._DB_PREFIX_.'country_lang as c_l
			ON c_l.id_country = c.id_country
			WHERE c_l.id_lang = '.$cookie->id_lang.' 
			AND c.id_country = '.	Configuration::get('PS_COUNTRY_DEFAULT'));

		if (isset($country[0]['iso']))
			$this->_currentIsoCodeCountry = $country[0]['iso'];
	}

	public function ajaxRequestType()
	{	
		if (isset($_POST) && isset($_POST['type']) && isset($_POST['base']))
		{
			$link = 'http://addons.prestashop.com/'.Language::getIsoById($_POST['id_lang']).
				'/2053-twenga-ready-to-sell.html';

			$type = (($_POST['type'] == 'desactive') ? $this->l('Disable') : 
				(($_POST['type'] == 'reset') ? $this->l('Reset') : 
				(($_POST['type'] == 'uninstall') ? $this->l('Uninstall') : $this->l('Delete'))));

			$url = $_POST['base'].'&token='.$_POST['token'].'&module_name='.
				$_POST['module_name'].'&tab_module='.$_POST['tab_module'].'&'.
				$_POST['type'].'='.$_POST['module_name'];
			
			$msg = '
				<style>
					#mainContent {
						border:1px solid #B0C4DE;
						background-color:#E2EBEE;
						-moz-border-radius:10px;
						-webkit-border-radius:10px;
						line-height:18px;
						font-size:14px; }

					#mainContent a { text-decoration:none; color:#268CCD;}
			</style>
			<div id="mainContent" >
				<p>'.$this->l('If you subscriber on Twenga, the activation of this module is mandatory.').
				'<br /><br />'.$this->l('In case of dysfunction, uninstall this module, install the newer version here and fill up again Twenga hashkey and login.').'
				<br /><br />'.$this->l('To unsubscribe or for any question, please contact Twenga on your account.').'
				<div style="margin: 10px 0 5px 0; font-size:14px; color:#FFF; text-align:center;">
				<b><a '.(($_POST['type'] == 'uninstall') ? 
				'onClick="$.fancybox.close(); window.location=\''.$url.'\' '. 
				$this->_getAjaxScript('send_mail.php', $_POST['type'], $url, false).'"' : ' ') . 
				'href="'.$url.'">'.$type.'</a></b>  - 
				<b><a href="'.$link.'">'.
				$this->l('Newer version').'</a></b> - 
				<b><a href="javacript:void(0);"i onclick="$.fancybox.close(); return false;">'.
				$this->l('Cancel').'</a></b>
				</div></p>';
			echo $msg;
		}
	}

	/*
	 ** Get the javascript code to fetch a distant file
	 ** href will be automatically split cause of its '&'
	 */
	private function _getAjaxScript($file, $type, $href, $displayMsg = true)
	{
		global $cookie;

		return '
				$.ajax({
						type: \'POST\',
						url: \''._MODULE_DIR_.'twenga/'.$file.'\',
						data: \'type='.$type.'&base='.$href.'&id_lang='.(int)$cookie->id_lang.'\',
						success: function(msg) {
							'.(($displayMsg) ? '
							$.fancybox(msg, {
								\'autoDimensions\'	: false,
								\'width\'						: 450,
								\'height\'					: \'auto\',
								\'transitionIn\'		: \'none\',
								\'transitionOut\'		: \'none\'	});'
								: '') . '
						}
		});
		return false;';
	}

	public function onclickOption($type, $href = false)
	{
		$content = '';

		switch($type)
		{
		case 'desactive':
			$content = $this->_getAjaxScript('infos_update.php', $type, $href);
			break;
		case 'reset':
			$content = $this->_getAjaxScript('infos_update.php', $type, $href);
			break;
		case 'delete':
			$content = $this->_getAjaxScript('infos_update.php', $type, $href);
			break;
		case 'uninstall':
			$content = $this->_getAjaxScript('infos_update.php', $type, $href);
		default:
		}
		return $content;
	}
	
	/**
	 * Method for beeing redirected to Twenga subscription
	 */
	private static function redirectTwengaSubscription($link)
	{
		echo '<script type="text/javascript" language="javascript">window.open("'.$link.'");</script>';
	}

	private function submitTwengaSubscription()
	{
		 unset($_POST['submitTwengaSubscription']);
		$params = array_filter($_POST);
		$return = '';
		try {
			$return = self::$obj_twenga->getSubscriptionLink($params);
			self::$obj_ps_stats->actSubscription();
			$this->inscription_url = $return['message'];
			self::redirectTwengaSubscription($this->inscription_url);
		} catch (TwengaFieldsException $e) {
			$this->_errors[] = $this->l('Params are not allowed (see details) : ').'<br />'.$e->getMessage();
		} catch (TwengaException $e) {
			$this->_errors[] = $this->l('Error occurred with the Twenga API method (see details) : ').'<br /> '.nl2br($e->getMessage());
		}
	}

	private function submitTwengaLogin()
	{
		if (!self::$obj_twenga->setHashkey($_POST['twenga_hashkey']))
			$this->_errors[] = $this->l('Your hashkey is invalid. Please check the e-mail already sent by Twenga.');
		if (!self::$obj_twenga->setUserName($_POST['twenga_user_name']))
			$this->_errors[] = $this->l('Your user name is invalid. Please check the e-mail already sent by Twenga.');
		if (!self::$obj_twenga->setPassword($_POST['twenga_password']))
			$this->_errors[] = $this->l('Your password is invalid. Please check the e-mail already sent by Twenga.');
		
		if(empty($this->_errors))
		{
			$bool_save = false; 
			try{
				$bool_save = self::$obj_twenga->saveMerchantLogin();
				self::$obj_ps_stats->validateSubscription();
			} catch (Exception $e) {
				$this->_errors[] = nl2br($e->getMessage());
			}
			if(!$bool_save)
				$this->_errors[] = $this->l('Authentication failed.')."<br />\n"
				.$this->l('Please review the e-mail sent by Twenga after subscription. If error still occurred, contact Twenga service.');
			else
				self::$obj_twenga->addFeed(array('feed_url' => $this->feed_url));
		}
	}

	private function submitTwengaActivateTracking()
	{
		$activate = false;
		
		// Use TwengaObj::siteActivate() method to activate tracking.
		try {
		   $activate = self::$obj_twenga->siteActivate();
		} catch (Exception $e) {
			$this->_errors[] = $e->getMessage();
		}
		if($activate)
		{
			$this->registerHook('payment');
			$this->registerHook('updateOrderStatus');
			$this->registerHook('cancelProduct');
		}
	}

	private function submitTwengaDisableTracking()
	{
		$return = Db::getInstance()->ExecuteS('SELECT `id_hook` FROM `'._DB_PREFIX_.'hook_module` WHERE `id_module` = \''.pSQL($this->id).'\'');
		foreach ($return as $hook)
		{
			$this->unregisterHook($hook['id_hook']);
		}
	}

	public function preProcess()
	{
		if(isset($_POST['submitTwengaSubscription']))
		{
		   $this->submitTwengaSubscription();
		}
		if(isset($_POST['submitTwengaLogin']))
		{
			$this->submitTwengaLogin();
		}
		if(isset($_POST['submitTwengaActivateTracking']))
		{
			$this->submitTwengaActivateTracking();
		}
		if(isset($_POST['submitTwengaDisableTracking']))
		{
			$this->submitTwengaDisableTracking();
		}
	}

	public function hookCancelProduct($params)
	{
		if((float)$params['order']->total_products_wt <= 0)
		{
			$cart = new Cart($params['order']->id_cart);
			$customer = new Customer($params['order']->id_customer);
			$params_to_twenga = array();
			// @todo delete or not ??
//			$params_to_twenga['order_id'] = (string)$params['order']->id;
//			$params_to_twenga['user_id'] = (string)$customer->id;
//			$params_to_twenga['cli_email'] = (string)$customer->email;
			$params_to_twenga['basket_id'] = (string)$params['order']->id_cart;
			try {
				if(self::$obj_twenga->orderExist($params_to_twenga))
				{
					$bool = self::$obj_twenga->orderCancel($params_to_twenga);
					self::$obj_ps_stats->cancelOrder();
				}
			} catch (Exception $e) {
				die($e->getMessage());
			}
		}
	}

	public function hookUpdateOrderStatus($params)
	{
		if( (int)$params['newOrderStatus']->unremovable === 1
		&& (int)$params['newOrderStatus']->logable === 1
		&& (int)$params['newOrderStatus']->delivery === 0)
		{
			$obj_order = new Order($params['id_order']);
			$customer = new Customer($obj_order->id_customer);
			$params_to_twenga = array();
			// @todo delete or not ??
//			$params_to_twenga['order_id'] = (int)$params['id_order'];
//			$params_to_twenga['user_id'] = (int)$customer->id;
//			$params_to_twenga['cli_email'] = (string)$customer->email;
			$params_to_twenga['basket_id'] = (int)$obj_order->id_cart;
			$bool = false;
			try {
				if(self::$obj_twenga->orderExist($params_to_twenga))
				{
					$cart = new Cart($params_to_twenga['basket_id']);
					$bool = self::$obj_twenga->orderValidate($params_to_twenga);
					self::$obj_ps_stats->validateOrder($obj_order->total_products_wt, $obj_order->total_paid);
				}
			} catch (Exception $e) {
				die($e->getMessage());
			}
		}
	}

	public function hookPayment($params)
	{
		$customer = new Customer($params['cart']->id_customer);
		$currency = new Currency($params['cart']->id_currency);
		$address = $customer->getAddresses($params['cart']->id_lang);
		$address = $address[0];
		
		// for 1.3 compatibility
		$type_both = 3;
		$type_only_shipping = 5;
		
		
		$tva = $params['cart']->getOrderTotal(true, $type_both)-$params['cart']->getOrderTotal(false, $type_both);
		$tax = ($tva * 100) / $params['cart']->getOrderTotal(true, $type_both);
		
		$params_to_twenga = array();
		// @todo delete or not ??
//		$params_to_twenga['user_id'] = $customer->id;
//		$params_to_twenga['cli_email'] = $customer->email;
		$params_to_twenga['total_ht'] = $params['cart']->getOrderTotal(false, $type_both);
		$params_to_twenga['basket_id'] = $params['cart']->id;
		$params_to_twenga['currency'] = $currency->iso_code;
		$params_to_twenga['total_ttc'] = $params['cart']->getOrderTotal(true, $type_both);
		$params_to_twenga['shipping'] = $params['cart']->getOrderTotal(true, $type_only_shipping);
		$params_to_twenga['tax'] = Tools::ps_round($tax, 2);
		$params_to_twenga['tva'] = $tva;
		$params_to_twenga['cli_firstname'] = $customer->firstname;
		$params_to_twenga['cli_lastname'] = $customer->lastname;
		$params_to_twenga['cli_city'] = $address['city'];
		$params_to_twenga['cli_state'] = $address['state'];
		$params_to_twenga['cli_country'] = $address['country'];
		$params_to_twenga['items'] = array();
		foreach ($params['cart']->getProducts() as $product)
		{
			$arr_item = array();
			if($product['total']!= '') $arr_item['total_ht'] = (float)$product['total'];
			if($product['cart_quantity'] != '') $arr_item['quantity'] = (int)$product['cart_quantity'];
			if($product['reference'] != '') $arr_item['sku'] = (string)$product['reference'];
			if($product['name'] != '') $arr_item['name'] = (string)$product['name'];
			if($product['category']) $arr_item['category_name'] = (string)$product['category'];
			$params_to_twenga['items'][] = $arr_item;
		}
		$params_to_twenga = array_filter($params_to_twenga);
		
		try {
			// twenga don't saved double orders with the same id, 
			// so don't need to use TwengaObj::orderExist() method.
			$tracking_code = self::$obj_twenga->getTrackingScript($params_to_twenga);
			return $tracking_code;
		} catch (TwengaFieldsException $e) {
			return $this->l('Error occurred when params passed in Twenga API').' : <br />'.$e->getMessage();
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	
	/*
	 ** Get the current country name used literaly
	 */
	static public function getCurrentCountryName()
	{
		global $cookie;

		$id_lang = ((isset($cookie->id_lang)) ? $cookie->id_lang : 
			((isset($_POST['id_lang'])) ? $_POST['id_lang'] : NULL));

		if ($id_lang === NULL)
			return 'Undefined id_lang';
		$country = Db::getInstance()->ExecuteS('
			SELECT c.name as name
			FROM '._DB_PREFIX_.'country_lang as c
			WHERE c.id_lang = '.$id_lang.' 
			AND c.id_country = '.	Configuration::get('PS_COUNTRY_DEFAULT'));
	
		if (!isset($country[0]['name']))
			$country[0]['name'] = 'Undefined';
		return $country[0]['name'];
	}

	/*
		 ** Check if the default country if available with the restricted ones
	 */
	private function _checkCurrentCountrie()
	{
		global $cookie;

		if (!in_array(strtolower($this->_currentIsoCodeCountry), $this->limited_countries))
		{
			$query = '
				SELECT c_l.name as name
				FROM '._DB_PREFIX_.'country_lang as c_l
				LEFT JOIN '._DB_PREFIX_.'country as c
				ON c_l.id_country = c.id_country
				WHERE c_l.id_lang = '.$cookie->id_lang.' 
				AND c.iso_code IN ('; 
			foreach($this->limited_countries as $iso)
				$query .= "'".strtoupper($iso)."', ";
			$query = rtrim($query, ', ').')';
			$countriesName = Db::getInstance()->ExecuteS($query);
			$htmlError = '
				<div class="error">
					<p>'.$this->l('Your default country is').' : '.Twenga::getCurrentCountryName().'</p>
					<p>'.$this->l('Please select one of these available countries approved by Twenga').' :</p>
					<ul>';
			foreach($countriesName as $c)
				$htmlError .= '<li>'.$c['name'].'</li>';
			$url = Tools::getShopDomain(true).$_SERVER['PHP_SELF'].'?tab=AdminCountries&token='.
				Tools::getAdminTokenLite('AdminCountries').'#Countries';
			$htmlError .= '
					</ul>
					'.$this->l('Follow this link to change the country').
					' : <a style="color:#0282dc;" href="'.$url.'">here</a>
				</div>';
			throw new Exception($htmlError);
		}
	}

	public function getContent()
	{
		try {
			$this->_checkCurrentCountrie();
		}
		catch (Exception $e)
		{
			return $e->getMessage();
		}
		// API can't be call if curl extension is not installed on PHP config.
		if (!extension_loaded('curl'))
		{
			$this->_errors[] = $this->l('Please activate the PHP extension \'curl\' to allow use of Twenga webservice library.');
			return $this->displayErrors();
		}
		$this->preProcess();
		$this->_html .= '<h2>'.$this->displayName.'</h2>';
		$this->_html .= $this->displayTwengaIntro();
		$this->_html .= $this->displayTwengaLogin();
		
		if((self::$obj_twenga->getHashKey() === NULL || self::$obj_twenga->getHashKey() === '')
		|| (self::$obj_twenga->getUserName() === NULL || self::$obj_twenga->getUserName() === '')
		|| (self::$obj_twenga->getPassword() === NULL || self::$obj_twenga->getPassword() === '')
		)
		{
			$this->_html .= $this->displaySubscription();
		}
		else
		{
			$this->_html .= $this->displayActivate();
		}
		return $this->displayErrors().$this->_html;
	}
	
	/**
	 * 
	 */
	public function displayTwengaIntro()
	{
		$errors = array();
		try {
			$return = self::$obj_twenga->getSubscriptionLink(array('site_url' => $this->site_url, 'feed_url' => $this->feed_url, 'country' => self::$shop_country));
			$this->inscription_url = $return['message'];
		} catch (TwengaFieldsException $e) {
			$errors[] = $e->getMessage();
		} catch (TwengaException $e) {
			$errors[] = $e->getMessage();
		}
		if (!empty($errors))
		{
			$str_error = $this->l('Errors occurred with the Twenga API subscription link:');
			$str_error .=  '<ol>'; 
			foreach ($errors as $error)
				$str_error .= '<li><em>'.$error.'</em></li>';
			$str_error .= '</ol>';
			$this->_errors[] = $str_error;
		}
		
		$defaultIsoCountry = strtolower($this->_currentIsoCodeCountry);
		if ($defaultIsoCountry == 'gb')
			$tarifs_link = 'https://rts.twenga.com/media/prices_uk.jpg';
		else
			$tarifs_link = 'https://rts.twenga.com/media/prices_'.$defaultIsoCountry.'.jpg';
		
		global $cookie;
		
		$isoUser = strtolower(Language::getIsoById(intval($cookie->id_lang)));

		$tarif_arr = array(950, 565);
		if (file_exists($tarifs_link))
			$tarif_arr = @getimagesize($tarifs_link);

		$str_return = '
			<script type="text/javascript">
			$().ready(function()
			{
				$("#twenga_tarif").click(function(e){
					e.preventDefault();
					window.open("'.$tarifs_link.'", "", "width='.$tarif_arr[0].', height='.$tarif_arr[1].', scrollbars=no, menubar=no, status=no" );
				});
			});
			</script>
			<fieldset>
				<legend><img src="../modules/'.$this->name.'/logo.gif" class="middle" /> '.$this->l('Get your products listed on Twenga.co.uk and follow the sales generated by Twenga').'</legend>'
				.'<div style="float:left; width:80px; height:100px">
					<img src="../modules/'.$this->name.'/logo_big.jpg" alt="" class="middle" style="margin: 20px 0 0 0;" />
				</div>'
				.'<p><strong>'.$this->l('You have already signed up to Twenga Ready to Sell ?').'</strong></p>'
				.'<ul>'
					.'<li>'.$this->l('Step 1: Retrieve your Twenga hashkey from your Twenga Ready to Sell account (tools>tracking>hashkey). Also click on "Test the tracking".');			
				$str_return .='
					<li>'.$this->l('Step 2: Come back here and paste it here in the field „hash key“. Click on Save and Activate.').'</li>
				</ul>
				<p><strong>'.$this->l('You want to sign up to Twenga Ready to Sell ?').'</strong></p>
				<p>'.$this->l('How to list your product on Twenga.co.uk:').'</p>
				<ul>'.'<li>'.$this->l('Step 1: Click on this link to sign up to Twenga Ready to Sell ');
		if ($this->inscription_url !== NULL)
			$str_return .= '&nbsp;<a href="'.$this->inscription_url.'" target="_blank" class="link">&raquo;'.$this->l('Subscribe').'&laquo;</a>'.'</li>';
		else
			$str_return .= '&nbsp;<em style="color:red;">'.$this->l('Error(s) occurred: please contact Twenga').'</em>';
			
				$str_return .='
					<li>'.$this->l('Step 2: Fill up Twenga sign up form').'</li>
					<li>'.$this->l('Step 3: When you got the Twenga hash-key by Email, come back here and paste it here in the field „hash key“. Click on Save and Activate.').'</li>
				</ul>';

				if ($isoUser == 'fr')
					$str_return .= '<p style="text-align:center;"><a href="#" title="'.$this->l('Increase your traffic - 500 offered clicks').'" onclick="$(\'#submitTwengaSubscription\').click(); return false;" ><img src="'.self::$base_path.'/bt_500_clicks.gif" width="364" height="45" /></a></p>';

				$str_return .= '
				<p>'.$this->l('Get your products listed on Twenga for free : only pay when you sell !').'</p>
				<p><a href="'.$tarifs_link.'" class="link" id="twenga_tarif">'.$this->l('Twenga Prices').'</a></p>
			</fieldset>
			
			<br />';
		return $str_return;
	}
	
	/**
	 * @return string html form for log to Twenga API.
	 */
	private function displayTwengaLogin()
	{
		global $cookie;

		$isoUser = strtolower(Language::getIsoById(intval($cookie->id_lang)));
		if ($isoUser == 'en')
			$lost_link = 'https://rts.twenga.co.uk/lost_password';
		else
			$lost_link = 'https://rts.twenga.'.$isoUser.'/lost_password';

		return '
		<form name="form_set_hashkey" action="" method="post">	
			<fieldset>
				<legend><img src="../modules/'.$this->name.'/logo.gif" class="middle" /> '.$this->l('Installation of Sales Tracking').'</legend>'
				.((self::$obj_twenga->getHashKey() === NULL || self::$obj_twenga->getHashKey() === '') ?
					'<p>'.$this->l('Enter here your Twenga Ready to Sell hashkey and Login / Pass').'</p>' :
					'<label>'.$this->l('Feed\'s url').' : </label><div class="margin-form">'.$this->feed_url.'</div><!-- .margin-form -->')
				.'<label>'.$this->l('HashKey').' <sup>*</sup> : </label>
				<div class="margin-form">
					<input type="text" size="38" maxlength="32" name="twenga_hashkey" value="'.self::$obj_twenga->getHashKey().'"/>&nbsp;
				</div><!-- .margin-form -->
				<label>'.$this->l('Login').' <sup>*</sup> : </label>
				<div class="margin-form">
					<input type="text" size="38" maxlength="32" name="twenga_user_name" value="'.self::$obj_twenga->getUserName().'"/>&nbsp;
				</div><!-- .margin-form -->
				<label>'.$this->l('Password').' <sup>*</sup> : </label>
				<div class="margin-form">
					<input type="password" size="38" maxlength="32" name="twenga_password" value="'.self::$obj_twenga->getPassword().'"/>&nbsp;
				</div><!-- .margin-form -->
				<div class="margin-form">' .$this->l('If you forgot your login, retrieve it back here').' <a href="'.$lost_link.'" target="_blank">'.$lost_link.'</a></div>'
			.'<input type="submit" value="'.$this->l('Save').'" name="submitTwengaLogin" class="button"/>
			</fieldset>
			</form><br />';
	}
	
	/**
	 * Subscription form need to be pre-filled.
	 * @return string the html form for subscription
	 */
	private function displaySubscription()
	{
		$site_name = Configuration::get('PS_SHOP_NAME');
		$employee = new Employee(1);
		$email = Configuration::get('PS_SHOP_EMAIL');
		$phone = (Configuration::get('PS_SHOP_PHONE') !== FALSE) ? Configuration::get('PS_SHOP_PHONE') : '';
		$legaltype = (Configuration::get('PS_SHOP_DETAILS') !== FALSE) ? Configuration::get('PS_SHOP_DETAILS') : '';
		$address = ((Configuration::get('PS_SHOP_ADDR1') !== FALSE) ? Configuration::get('PS_SHOP_ADDR1')."\n" : '').((Configuration::get('PS_SHOP_ADDR2') !== FALSE) ? Configuration::get('PS_SHOP_ADDR2') : '');
		$postal_code = (Configuration::get('PS_SHOP_CODE') !== FALSE) ? Configuration::get('PS_SHOP_CODE') : '';
		$city = (Configuration::get('PS_SHOP_CITY') !== FALSE) ? Configuration::get('PS_SHOP_CITY') : '';
	 	return '
	 	<form name="form_subscription_twenga" id="form_subscription_twenga" method="post" action="">
		<fieldset>
			<legend><img src="../modules/'.$this->name.'/logo.gif" class="middle" /> '.$this->l('This information pre-fills the Twenga subscription form.').'</legend>
			<p>'.$this->l('The following information will pre-fill your "Twenga Ready to Sell" subscription. Please check the validity of the values.').'</p><br />
			<label>'.$this->l('Site\'s url').' <sup>*</sup> : </label>
			<div class="margin-form">'
				.'<div class="simulate-disable-input" >'.$this->site_url.'</div>
				<input type="hidden" name="site_url" value="'.$this->site_url.'"/>'
				.'<p>'.$this->l('Site\'s url').'</p>
			</div><!-- .margin-form -->
			<label>'.$this->l('Feed\'s url').' <sup>*</sup> : </label>
			<div class="margin-form">
				<div class="simulate-disable-input" >'.$this->feed_url.'</div>
				<input type="hidden" name="feed_url" value="'.$this->feed_url.'"/>
				<p>'
					.$this->l('Feeds Url, the list of products in xml.').'<br />'
					.$this->l('Value is automatically filled, please do not change.')
				.'</p>
			</div><!-- .margin-form -->
			<label>'.$this->l('Country').' <sup>*</sup> : </label>
			<div class="margin-form">
				<div class="simulate-disable-input" >'.(self::$shop_country == 'GB' ? 'UK' : self::$shop_country).'</div>
				<input type="hidden" name="country" value="'.self::$shop_country.'"/>
				<p>'
				.$this->l('E-Merchant’s society country. Use the ISO_3166-1 Alpha-2 country code format.')
				.'<br />
				Ex : France => FR, Germany => DE<br />
				<a href="http://en.wikipedia.org/wiki/ISO_3166-1#Current_codes" target="_blank" >> related link</a><br />
				</p>
			</div><!-- .margin-form -->
			<label>'.$this->l('Site\'s name').' : </label>
			<div class="margin-form">
				<input type="text" name="site_name" value="'.$site_name.'">
				<p>'.$this->l('Site\'s name').'</p>
			</div><!-- .margin-form -->
			<label>'.$this->l('Last Name').' : </label>
			<div class="margin-form">
				<input type="text" name="lastname" value="'.$employee->lastname.'">
			</div><!-- .margin-form -->
			<label>'.$this->l('First Name').' : </label>
			<div class="margin-form">
				<input type="text" name="firstname" value="'.$employee->firstname.'">
			</div><!-- .margin-form -->
			<label>'.$this->l('E-mail').' : </label>
			<div class="margin-form">
				<input type="text" name="email" value="'.$email.'">
			</div><!-- .margin-form -->
			<label>'.$this->l('Phone').' : </label>
			<div class="margin-form">
				<input type="text" name="phone" value="'.$phone.'">
			</div><!-- .margin-form -->
			<label>'.$this->l('Organization name ').' : </label>
			<div class="margin-form">
				<input type="text" name="site_name" value="'.$site_name.'">
			</div><!-- .margin-form -->
			<label>'.$this->l('Legal type').' : </label>
			<div class="margin-form">
				<input type="text" name="legaltype" value="'.$legaltype.'">
				<p>'.$this->l('society legal type').'</p>
			</div><!-- .margin-form -->
			<label>'.$this->l('Address').' : </label>
			<div class="margin-form">
				<textarea name="address" >'.$address.'</textarea>
			</div><!-- .margin-form -->
			<label>'.$this->l('Zip/ Postal Code').' : </label>
			<div class="margin-form">
				<input type="text" name="postal_code" value="'.$postal_code.'">
			</div><!-- .margin-form -->
			<label>'.$this->l('City').' : </label>
			<div class="margin-form">
				<input type="text" name="city" value="'.$city.'">
			</div><!-- .margin-form -->
			<div style="text-align:center;margin:20px 0 0 0;" >
				<input type="submit" name="submitTwengaSubscription" id="submitTwengaSubscription" class="button" value="'.$this->l('Save').'" />
			</div>
		</fieldset>
		</form>';
	}
	
	/**
	 * @return string html form for activate or disable the Twenga tracking
	 */
	private function displayActivate()
	{
		$str = '
		<form name="form_twenga_activate" method="post" action="">
			<fieldset>
				<legend><img src="../modules/'.$this->name.'/logo.gif" class="middle" />%s</legend>
				%s <br />
				<div style="text-align:center;margin:20px 0 0 0;" >
					<input type="submit" name="%s" class="button" value="%s" />
				</div>
			</fieldset>
		</form>';
		
		if($this->isRegisteredInHook('payment')
		&& $this->isRegisteredInHook('updateOrderStatus')
		&& $this->isRegisteredInHook('cancelProduct'))
			$str = sprintf($str, $this->l('Disable Tracking'), $this->l('To disable tracking, click on the following button :'), 'submitTwengaDisableTracking', $this->l('Disable'));
		else
			$str = sprintf($str, $this->l('Activate Tracking'), $this->l('To activate tracking, click on the following button :'), 'submitTwengaActivateTracking', $this->l('Activate'));
		return $str;
	}
	
	/**
	 * Just set in one method the displaying error message in Prestashop back-office.
	 */
	private function displayErrors()
	{
		$string = '';
		if(!empty($this->_errors))
		{
			foreach ($this->_errors as $error)
			{
				$string .= $this->displayError($error);
			}
		}
		return $string;
	}
	
	/**
	 * Used by export.php to build the feed required by Twenga.
	 * See detailed comments in the body of the method
	 * @see Twenga::preparedValues() to see how needed tags for feed are filled
	 */
	public function buildXML()
	{
		// this check if the module is installed and if the site is registered at Twenga
		$bool_site_exists = true;
		if (self::$obj_twenga->getHashkey() === NULL)
		{
			$this->_errors[] = $this->l('The hash key must be set for used Twenga API.');
			$bool_site_exists = false;
		}
		if ($bool_site_exists)
		{
			try {
				$bool_site_exists = self::$obj_twenga->siteExist();
			} catch (Exception $e) {
				$this->_errors[] = $e->getMessage().$this->l('Some parameters missing, or the site doesn\'t exist');
				$bool_site_exists = false;
			}
		}
		if(!$bool_site_exists)
		{
			return $this->displayErrors();
		}
		
		// Now method build the XML
		$xmlstr = '<?xml version="1.0" encoding="utf-8"?><catalog></catalog>';
		$xml = new SimpleXMLElement($xmlstr);
		
		$parameters = Configuration::getMultiple(array('PS_REWRITING_SETTINGS', 'PS_LANG_DEFAULT', 'PS_SHIPPING_FREE_PRICE', 'PS_SHIPPING_HANDLING', 'PS_SHIPPING_METHOD', 'PS_SHIPPING_FREE_WEIGHT', 'PS_COUNTRY_DEFAULT'));
		$lang = (int)$parameters['PS_LANG_DEFAULT'];
		$language = new Language($lang);
		$carrier = new Carrier(Configuration::get('PS_CARRIER_DEFAULT'), $language->id);
		$defaultCountry = new Country(Configuration::get('PS_COUNTRY_DEFAULT'), $language->id);

		$link = new Link();
		
		$result = Db::getInstance()->ExecuteS('
		SELECT `id_product` FROM `'._DB_PREFIX_.'product` WHERE `active` = 1');
		
		foreach ($result AS $k => $row)
		{
			$product = new Product((int)$row['id_product']);
			
			if (Validate::isLoadedObject($product) AND $product->active)
			{
				// Check if product declinations exist.
				$combinations = $this->getCombinations($product, $lang);
				
				// Set an empty value even no combinations was found to make foreach usable.
				if(empty($combinations))
					$combinations[] = array();
				
				foreach ($combinations as $combination)
				{
					// prepared values before insert it in node structure.
					// In this way we can structure code with checking method and displaying method for more lisibility.
					$product_values = $this->preparedValues($product, $combination, $lang, $link, $carrier);
					
					// create the product node for each products and declinations
					$product_node = $xml->addChild('product', '');
					
					// required Fields
					$product_node->addChild('product_url', $product_values['product_url']);
					$product_node->addChild('designation', '<![CDATA['.$product_values['designation'].']]>');
					$product_node->addChild('price', $product_values['price']);
					$product_node->addChild('category', '<![CDATA['.$product_values['category'].']]>');
					$product_node->addChild('image_url', $product_values['image_url']);
					$product_node->addChild('description', '<![CDATA['.$product_values['description'].']]>');
					$product_node->addChild('brand', $product_values['brand']);
					
					// optionnals fields
					$product_node->addChild('merchant_id', $product_values['merchant_id']);
					$product_node->addChild('manufacturer_id', $product_values['manufacturer_id']);
					$product_node->addChild('shipping_cost', $product_values['shipping_cost']);
					$product_node->addChild('in_stock', $product_values['in_stock']);
					$product_node->addChild('stock_detail', $product_values['stock_detail']);
					$product_node->addChild('condition', $product_values['condition']);
					$product_node->addChild('upc_ean', $product_values['upc_ean']);
					$product_node->addChild('product_type', $product_values['product_type']);
					$product_node->addChild('isbn', $product_values['isbn']);
					$product_node->addChild('eco_tax', $product_values['eco_tax']);
				}
			}
		}
		$str_xml = $xml->asXML();
		$str_xml = str_replace(array('&lt;![CDATA[', ']]&gt;', '&#13;'), array('<![CDATA[', ']]>', ''), $str_xml);
		return $str_xml;
	}
	/**
	 * @param Product $product to get the product properties
	 * @param array $combination to get particular properties from a declination
	 * @param int $lang id lang to take all text in good language
	 * @param Link $link to set the link of the product and its images.
	 * @param Carrier $carrier not used now, but usable for next version, needed for calculate the shipping cost,
	 * 		  But for now it's not sure enough.
	 * @return array with good value for the XML.
	 */
	private function preparedValues(Product $product, array $combination, $lang, Link $link, Carrier $carrier)
	{
		$arr_return = array();
		$str_features = array();
		$model = array();
		$version = str_replace('.', '', _PS_VERSION_);
		
		// To build description and model tags.
		if(isset($combination['attributes']))
		{
			foreach ($combination['attributes'] as $attribut)
			{
				$str_features[] = $attribut['group_name'].' : '.$attribut['name'];
				$model[] = $attribut['name'];
			}
		}
		if(isset($combination['weight']) && (int)$combination['weight'] !== 0)
			$str_features[] = 'weight : '.$combination['weight'];
		else if ($product->weight !== 0)
			$str_features[] = 'weight : '.$product->weight;
		
		$features = $product->getFrontFeatures($lang);
		foreach ($features as $feature)
			$str_features[] = $feature['name'].' : '.$feature['value'];
		
		// Category tag
		$category = new Category((int)$product->id_category_default, $lang);
		$category_path = ((isset($category->id) AND $category->id) ? Tools::getFullPath((int)($category->id), $product->name[$lang]) : Tools::getFullPath((int)($product->id_category_default), $product->name[$lang]));
		$category_path = (Configuration::get('PS_NAVIGATION_PIPE') != false && Configuration::get('PS_NAVIGATION_PIPE') !== '>' ) ? str_replace(Configuration::get('PS_NAVIGATION_PIPE'), '>', $category_path) : $category_path;
		// image tag
		$id_image = (isset($combination['id_image'])) ? $combination['id_image'] : 0;
		if($id_image === 0)
		{
			$image = $product->getCover((int)$product->id);
			$id_image = $image['id_image'];
		}
		
		$quantity = Product::getQuantity($product->id, (isset($combination['id_combination']) ? $combination['id_combination'] : NULL));
		
		$condition = '';
		 if(strlen((string)$version) < 2)
			 $version = (string)$version.'0';
		if((int)substr($version, 0, 2) >= 14)
			$condition = (($product->condition === 'new') ? 0 : 1);
		
		$price = $product->getPrice(true, (isset($combination['id_combination']) ? $combination['id_combination'] : NULL), 2);
		$upc_ean = strlen((string)$product->ean13) == 13 ? $product->ean13 : '';
		
		$arr_return['product_url'] = $link->getProductLink((int)$product->id, $product->link_rewrite[$lang], $product->ean13, $lang);
		$arr_return['designation'] = Tools::htmlentitiesUTF8($product->name[$lang].' '.Manufacturer::getNameById($product->id_manufacturer).' '.implode(' ', $model));
		$arr_return['price'] = $price;
		$arr_return['category'] = Tools::htmlentitiesUTF8(strip_tags($category_path));
		$arr_return['image_url'] = $link->getImageLink($product->link_rewrite[$lang], $product->id.'-'.$id_image, 'large');
		
		// Must description added since Twenga-module v1.1
		$arr_return['description'] = is_array($product->description) ? strip_tags($product->description[$lang]) : strip_tags($product->description);
		$arr_return['description'] = trim($arr_return['description'].' '.strip_tags(implode(', ', $str_features)));
		$arr_return['description'] = Tools::htmlentitiesUTF8($arr_return['description']);
		$arr_return['brand'] = Manufacturer::getNameById($product->id_manufacturer);
		$arr_return['merchant_id'] = $product->id;
		$arr_return['manufacturer_id'] = $product->id_manufacturer;
		$arr_return['shipping_cost'] = 'NC';
		$arr_return['in_stock'] = $quantity > 0 ? 'Y' : 'N';
		$arr_return['stock_detail'] = $quantity;
		$arr_return['condition'] = $condition;
		$arr_return['upc_ean'] = $upc_ean;
		$arr_return['eco_tax'] = $product->ecotax;
		
		// for prestashop 1.4 and previous version these fields are not managed.
		// So default values are set.
		$arr_return['product_type'] = '1';
		$arr_return['isbn'] = '';
		
		return $arr_return;
	}
	/**
	 * @param Product $product
	 * @param int $lang id of a language
	 * @return array of a product declinations. 
	 */
	private function getCombinations(Product $product, $lang)
	{
		$attributesGroups = $product->getAttributesGroups((int)$lang);
		$combinations = array();
		if (Db::getInstance()->numRows())
		{
			$combinationImages = $product->getCombinationImages((int)$lang);
			foreach ($attributesGroups AS $k => $row)
			{
				$combinations[$row['id_product_attribute']]['id_combination'] = $row['id_product_attribute']; 
				$combinations[$row['id_product_attribute']]['attributes'][$row['id_attribute_group']] = array('name'=>$row['attribute_name'], 'group_name'=>$row['public_group_name'], 'id_attribute'=>(int)$row['id_attribute']);
				$combinations[$row['id_product_attribute']]['price'] = (float)($row['price']);
				$combinations[$row['id_product_attribute']]['ecotax'] = (float)($row['ecotax']);
				$combinations[$row['id_product_attribute']]['weight'] = (float)($row['weight']);
				$combinations[$row['id_product_attribute']]['quantity'] = (int)($row['quantity']);
				$combinations[$row['id_product_attribute']]['reference'] = $row['reference'];
				if (isset($row['unit_price_impact']))
					$combinations[$row['id_product_attribute']]['unit_impact'] = $row['unit_price_impact'];
				$combinations[$row['id_product_attribute']]['id_image'] = isset($combinationImages[$row['id_product_attribute']][0]['id_image']) ? $combinationImages[$row['id_product_attribute']][0]['id_image'] : -1;
			}
		}
		return $combinations;
	}
}
