<?php

if (!defined('_PS_VERSION_'))
	exit;

require_once(_PS_MODULE_DIR_ . "dejala/dejalaconfig.php");
require_once(_PS_MODULE_DIR_ . "dejala/dejalautils.php");
require_once(_PS_MODULE_DIR_ . "dejala/dejalacarrierutils.php");
require_once(_PS_MODULE_DIR_ . "dejala/dejalacart.php");
require_once(_PS_MODULE_DIR_ . "dejala/calendarutils.php");


/**
 * This module enables the interractions with dejala.com carrier services
 **/
class Dejala extends CarrierModule
{
	const INSTALL_SQL_FILE = 'install.sql';
	public $DEJALA_DEBUG = FALSE;
	public $dejalaConfig;
	public $id_lang ;
	private $wday_labels ;
	private static $INSTANCE = NULL ;
	private $useAlphaPDFInvoicePatcher = true ;
	private $useBetaOrderDetailPatch = true ;

	public static function getInstance()
	{
		if (!self::$INSTANCE)
		{
			self::$INSTANCE = new Dejala();
		}
		return self::$INSTANCE;
	}

	public function __construct()
	{
		global $cookie ;

		//TODO Iso code of countries where the module can be used, if none module available for all countries
		$this->limited_countries = array('fr');
		$this->name = 'dejala';
		$this->tab = 'shipping_logistics';
		$this->version = 1.4;
		$this->internal_version = '1.4';
		$this->id_lang = (!isset($cookie) OR !is_object($cookie)) ? (int)(Configuration::get('PS_LANG_DEFAULT')) : (int)($cookie->id_lang);
		$this->wday_labels = array($this->l('Sunday'), $this->l('Monday'), $this->l('Tuesday'), $this->l('Wednesday'), $this->l('Thursday'), $this->l('Friday'), $this->l('Saturday'));

		parent::__construct();

		// The parent construct is required for translations
		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('Dejala.com : Courier delivery');
		$this->description = $this->l('Lets Dejala.com handle your deliveries by courier');

		// load configuration only if installed
		if ($this->id)
		{
			if (true !== extension_loaded('curl'))
			{
				$this->warning = $this->l('The Dejala module requires php extension cURL to function properly. Please install the php extension "cURL"');
			}

			$this->dejalaConfig = new DejalaConfig();
			$this->dejalaConfig->loadConfig();

			// Update table schema
			if (!isset($this->dejalaConfig->internal_version) || $this->dejalaConfig->internal_version < $this->internal_version)
			{
				$this->unregisterHook('cart') ;
				$res = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'dejala_cart` LIMIT 1') ;
				if ($res)
				{
					if (!array_key_exists('cart_date_upd', $res[0]))
					{
						Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'dejala_cart` ADD COLUMN cart_date_upd DATETIME DEFAULT 0;');
					}
					if (!array_key_exists('delivery_price', $res[0]))
					{
						Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'dejala_cart` ADD COLUMN delivery_price FLOAT DEFAULT NULL;');
					}
				}
				$this->dejalaConfig->internal_version = $this->internal_version ;
				$this->dejalaConfig->saveConfig() ;
			}
		}
	}

	/**
	 * install dejala module
	 */
	public function install()
	{
		if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
			return (false);
		elseif (!$sql = file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
			return (false);

		$sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
		$sql = preg_split("/;\s*[\r\n]+/",$sql);

		foreach ($sql as $query)
			if (!empty($query))
			{
				if (!Db::getInstance()->Execute(trim($query)))
				return (false);
			}

		if (parent::install() == false
		OR $this->registerHook('updateOrderStatus') == false
		OR $this->registerHook('extraCarrier') == false
		OR $this->registerHook('processCarrier') == false
		OR $this->registerHook('orderDetailDisplayed') == false
		OR $this->registerHook('PDFInvoice') == false
		)
			return (false);


		$this->dejalaConfig = new DejalaConfig();

		if (!$this->dejalaConfig->saveConfig())
			return (false);

		// Cleanup pre-existing Dejala Carriers (in case the module had previously been tested and not removed cleanly)
		while (!is_null($carrier = DejalaCarrierUtils::getCarrierByName($this->name, true))) {
			$carrier->deleted = 1 ;
			if (!$carrier->update()) return false;
		}

		DejalaCarrierUtils::createDejalaCarrier($this->dejalaConfig) ;

		return (true);
	}

	public function uninstall()
	{

		// If Dejala is default carrier, try to set another one as default
		$defaultCarrier = new Carrier((int)Configuration::get('PS_CARRIER_DEFAULT')) ;
		if (DejalaCarrierUtils::getCarrierName($defaultCarrier) == $this->name) {
			$defaultCarrier = DejalaCarrierUtils::getFirstActiveCarrierWithNameNotBeing($this->name) ;
			if (Validate::isLoadedObject($defaultCarrier)) {
				Configuration::updateValue('PS_CARRIER_DEFAULT', $defaultCarrier->id_carrier) ;
			}
		}

		while (!is_null($carrier = DejalaCarrierUtils::getCarrierByName($this->name, true))) {
			$carrier->deleted = 1 ;
			if (!$carrier->update()) return false;
		}

		$this->dejalaConfig->uninstall();
		if (!parent::uninstall() OR
		!$this->unregisterHook('updateOrderStatus') OR
		!$this->unregisterHook('extraCarrier') OR
		!$this->unregisterHook('processCarrier') OR
		!$this->unregisterHook('orderDetailDisplayed') OR
		!$this->registerHook('PDFInvoice')
		)
			return false;

		return true;
	}


	/**
	 * Data validation for module configuration
	 **/
	public function _postValidation()
	{
		$errors = array();

		$method = Tools::getValue('method');
		if ($method == 'signin')
		{
			if (empty($_POST['login']))
				$errors[] = $this->l('login is required.');
			if (empty($_POST['password']))
				$errors[] = $this->l('password is required.');
			if (empty($_POST['country']))
				$errors[] = $this->l('country is required.');
		}
		elseif ($method == 'register')
		{
			if (empty($_POST['login']))
				$errors[] = $this->l('login is required.');
			if (empty($_POST['login']) OR !Validate::isEmail($_POST['login']))
				$errors[] = $this->l('login must be a valid e-mail address.');
			if (empty($_POST['password']))
				$errors[] = $this->l('password is required.');
			if (empty($_POST['store_name']))
				$errors[] = $this->l('Shop name is required.');
			if (empty($_POST['country']))
				$errors[] = $this->l('country is required.');
		}
		elseif ($method == 'products')
		{
			$products = array();
			$djlUtil = new DejalaUtils();
			$responseArray = $djlUtil->getStoreProducts($this->dejalaConfig, $products);
			if ('200' != $responseArray['status'])
			$products = array();
			foreach ($_POST as $key=>$value)
			{
				if (0 === strpos($key, 'margin_'))
				{
					$this->mylog( "key=" . substr($key, 7) );
					$productID = (int)(substr($key, 7));
					if ( is_null($_POST[$key]) || (0 == strlen($_POST[$key])) )
					$_POST[$key] = 0;
					$_POST[$key] = str_replace(',', '.', $_POST[$key]);
					$_POST[$key] = str_replace(' ', '', $_POST[$key]);

					if (!Validate::isFloat($_POST[$key]))
					{
						$errors[] = $value . ' ' . $this->l('is not a valid margin.');
					}
					$margin = (float)($_POST[$key]);
					foreach ($products as $l_product)
					{
						if ((int)$l_product['id'] == (int)$productID)
						{
							$product = (int)$l_product;
							break;
						}
					}
					if ($product)
					{
						$vat_factor = (1+ ($product['vat'] / 100));
						$public_price = round($product['price']*$vat_factor, 2);
						$public_price = round($public_price + $margin, 2);
						if ($public_price < 0)
							$errors[] = $value . ' ' . $this->l('is not a valid margin.');
					}
				}
			}
		}
		return ($errors);
	}

	/**
	 * Module configuration request processing
	 **/
	public function _postProcess()
	{
		global $smarty;

		$errors = array();
		$method = Tools::getValue('method');
		if ($method == 'signin')
		{
			$djlUtil = new DejalaUtils();
			$this->dejalaConfig->mode = 'TEST';
			$this->dejalaConfig->login = Tools::getValue('login');
			$this->dejalaConfig->password = Tools::getValue('password');
			$this->dejalaConfig->country = Tools::getValue('country');
			$this->dejalaConfig->serviceURL = str_replace('.fr', '.'.$this->dejalaConfig->country, $this->dejalaConfig->serviceURL);
			$this->dejalaConfig->sandboxServiceURL = str_replace('.fr', '.'.$this->dejalaConfig->country, $this->dejalaConfig->sandboxServiceURL);
			$storeAttr = array();
			$response = $djlUtil->ping($this->dejalaConfig, 'TEST');
			if ($response['status'] == 200)
			{
				$this->dejalaConfig->saveConfig();
			}
			else
			{
				if ($response['status'] == 401)
					$errors[] = $this->l('An error occurred while authenticating your account on Dejala.com. Your credentials were not recognized.');
				else
					$errors[] = $this->l('Unable to process the action.') . '(' . $response['status'] . ')';

				$this->dejalaConfig->login = null;
				$this->dejalaConfig->password = null;
			}
		}
		elseif ($method == 'register')
		{
			$djlUtil = new DejalaUtils();
			$this->dejalaConfig->mode = 'TEST';
			$this->dejalaConfig->login = Tools::getValue('login');
			$this->dejalaConfig->password = Tools::getValue('password');
			$this->dejalaConfig->country = Tools::getValue('country');
			$this->dejalaConfig->serviceURL = str_replace('.fr', '.'.$this->dejalaConfig->country, $this->dejalaConfig->serviceURL);
			$this->dejalaConfig->sandboxServiceURL = str_replace('.fr', '.'.$this->dejalaConfig->country, $this->dejalaConfig->sandboxServiceURL);
			$this->dejalaConfig->storeUrl = Dejala::getHttpHost(true, true) ;
			$response = $djlUtil->createInstantStore($this->dejalaConfig, Tools::getValue('store_name'));

			if ($response['status'] == 201)
				$this->dejalaConfig->saveConfig();
			elseif ($response['status'] == 409)
				$errors[] = $this->l('Please choose another login');
			elseif ($response['status'] == 403)
				$errors[] = $this->l('Dejala Server cannot be reached by your Prestashop server. This is most likely due to a limit set by your hosting provider. Please contact their technical support and ask if your server is authorized to initiate outbound HTTP connections.');
			else
				$errors[] = $this->l('Unable to process the action.') . '(' . $response['status'] . ')';
			$this->dejalaConfig->loadConfig();
		}
		elseif ($method == 'location')
		{
			$djlUtil = new DejalaUtils();
			$response = $djlUtil->setStoreLocation($this->dejalaConfig, $_POST);
			if ($response['status'] != 200)
			$errors[] = $this->l('An error occurred while updating location');
		}
		elseif ($method == 'contact')
		{
			$djlUtil = new DejalaUtils();
			$response = $djlUtil->setStoreContacts($this->dejalaConfig, $_POST);
			if ($response['status'] != 200)
			$errors[] = $this->l('An error occurred while updating contacts');
		}
		elseif ($method == 'processes')
		{
			$djlUtil = new DejalaUtils();
			$response = $djlUtil->setStoreProcesses($this->dejalaConfig, $_POST);
			if ($response['status'] != 200)
			$errors[] = $this->l('An error occurred while updating processes');
		}
		elseif ($method == 'products') {
			$djlUtil = new DejalaUtils();
			$response = $djlUtil->setStoreProducts($this->dejalaConfig, $_POST);
			if ($response['status'] != 200)
			$errors[] = $this->l('An error occurred while updating products');
		}
		elseif ($method == 'technical_options')
		{
			$maxStatuses = (int)$_POST['status_max'];
			if ($maxStatuses > 30)
				$maxStatuses = 30;
			$selectedTriggers=array();
			for ($i = 0; $i < $maxStatuses; $i++)
			{
				$l_val = Tools::getValue('status_'.$i);
				if ($l_val)
					$selectedTriggers[] = $l_val;
			}
			$trigerringStatuses = implode(',', $selectedTriggers);
			$this->dejalaConfig->trigerringStatuses = htmlentities($trigerringStatuses, ENT_COMPAT, 'UTF-8');
			$this->dejalaConfig->saveConfig();
			$this->dejalaConfig->loadConfig();
		}
		elseif ($method == 'delivery_options')
		{
			$djlUtil = new DejalaUtils();
			$response = $djlUtil->setStoreCalendar($this->dejalaConfig, $_POST);
			if ($response['status'] != 200)
			$errors[] = $this->l('An error occurred while updating products');

			$m_attributes['nb_days_displayed'] = htmlentities(Tools::getValue('nb_days'), ENT_COMPAT, 'UTF-8');
			$m_attributes['delivery_delay'] = htmlentities(Tools::getValue('delivery_delay'), ENT_COMPAT, 'UTF-8');
			$m_attributes['delivery_partial'] = htmlentities(Tools::getValue('delivery_partial'), ENT_COMPAT, 'UTF-8');

			$response = $djlUtil->setStoreAttributes($this->dejalaConfig, $m_attributes);
			if ($response['status'] != 200)
			$errors[] = $this->l('An error occurred while updating products');

		}
		elseif ($method == 'golive')
		{
			$djlUtil = new DejalaUtils();
			$response = $djlUtil->goLive($this->dejalaConfig, $_POST);
		}
		elseif ($method == 'switchMode')
		{
			$l_mode = Tools::getValue('mode');
			if ( ('PROD' == $l_mode) || ('TEST' == $l_mode) )
			{
				$this->dejalaConfig->mode = $l_mode;
				$this->dejalaConfig->saveConfig();
			}
		}
		elseif ($method == 'switchActive')
		{

			$l_active = Tools::getValue('visibility_status');

			if (($l_active == "visible") || ($l_active == "invisible"))
			{
				$this->dejalaConfig->visibility_status = $l_active;
				$this->dejalaConfig->saveConfig();
			}
			if ($l_active == "visible_limited")
			{
				$l_active_list = Tools::getValue('visible_users_list');
				if ($l_active_list == "")
				{
					$this->dejalaConfig->visible_users_list = "";
					$this->dejalaConfig->saveConfig();

					$errors[] = $this->l('You must provide at least one email address to restrict Dejala\'s visibility.');
				}
				else
				{
					$this->dejalaConfig->visibility_status = $l_active;
					$this->dejalaConfig->visible_users_list = $l_active_list;
					$this->dejalaConfig->saveConfig();
				}
			}
		}
		else
			$errors[] = $this->l('Unable to process the action.');

		return ($errors);
	}

	public function getContent()
	{
		global $smarty;

		$smarty->assign('country', $this->dejalaConfig->country);
		$output = $this->display(__FILE__, 'dejala_header.tpl');
		if (!empty($_POST))
		{
			$errors = $this->_postValidation();
			if (!count($errors))
			$errors = $this->_postProcess();
			if (count($errors))
			foreach ($errors AS $err)
			$output .= '<div class="alert error">'. $err .'</div>';
			else
			{
				$method = Tools::getValue('method');
				$output .= '<div class="conf confirm">
				<img src="../img/admin/ok.gif" alt="" title="" />
				'.$this->l('Settings updated').(($method == 'signin' OR $method == 'register' OR $method == 'golive') ? '<img src="http://www.prestashop.com/modules/dejala.png?pspid='.urlencode($this->dejalaConfig->login).'&mode='.($this->dejalaConfig->mode == 'TEST' ? 0 : 1).'" style="float:right" />' : '').'
				</div>';
			}
		}
		$output = $output . $this->displayForm();
		return ($output);
	}


	public function displayForm()
	{
		global $smarty, $cookie;

		$errors = array();
		$outputMain = '';
		$smarty->assign("djl_mode", $this->dejalaConfig->mode);
		$smarty->assign("disabled", '');
		if ($this->dejalaConfig->mode == 'PROD')
		$smarty->assign("disabled", 'disabled="disabled"');
		if (true !== extension_loaded('curl'))
		{
			$errors[] = $this->l('This module requires php extension cURL to function properly. Please install the php extension "cURL" first.');
			$smarty->assign("disabled", 'disabled="disabled"');
		}

		$registered = TRUE;
		if ((0 == strlen($this->dejalaConfig->login)) || (0 == strlen($this->dejalaConfig->password)))
			$registered= FALSE;

		if ($registered)
		{
			$djlUtil = new DejalaUtils();
			$responsePing = $djlUtil->ping($this->dejalaConfig, $this->dejalaConfig->mode);
			if (200 != $responsePing['status'])
			{
				if (401 == $responsePing['status'])
				$errors[] = $this->l('An error occurred while authenticating your account on Dejala.com. Your credentials were not recognized.');
				else
				$errors[] = $this->l('An error occurred while authenticating your account on Dejala.com. This may be due to a temporary network or platform problem. Please try again later or contact Dejala.com');
				unset($_GET['cat']);
				$registered= FALSE;
			}
		}

		$smarty->assign("registered", $registered?"1":"0");


		if (!isset($_GET['cat']) || ($_GET['cat']==='home') || ($_GET['cat']===''))
		$currentTab="home";
		else
		$currentTab=$_GET['cat'];

		$smarty->assign("currentTab", $currentTab);
		$smarty->assign("moduleConfigURL", 'index.php?tab=AdminModules&configure=dejala&token='.$_GET['token']);
		$smarty->assign("formAction", Tools::safeOutput($_SERVER['REQUEST_URI']));
		$outputMenu = $this->display(__FILE__, 'dejala_menu.tpl');

		if ($currentTab === 'home')
		{
			$smarty->assign("login", html_entity_decode(Configuration::get('PS_SHOP_EMAIL'), ENT_COMPAT, 'UTF-8'));
			if ($registered)
			{
				$smarty->assign("visibility_status", $this->dejalaConfig->visibility_status);
				$smarty->assign("visible_users_list", $this->dejalaConfig->visible_users_list);
				$smarty->assign("store_login", html_entity_decode($this->dejalaConfig->login, ENT_COMPAT, 'UTF-8'));
				$smartifyErrors = $this->smartyfyStoreAttributes();
				if (isset($smartifyErrors) && count($smartifyErrors))
				$errors = $smartifyErrors;
			}
			else
			{
				$shopName = Configuration::get('PS_SHOP_NAME');
				if (strlen($shopName) >= 15)
				$shopName = substr($shopName, 0, 15);
				$smarty->assign("store_name", html_entity_decode($shopName, ENT_COMPAT, 'UTF-8'));
			}
			$outputMain = $this->display(__FILE__, 'dejala_home.tpl');
		}
		elseif ($currentTab==='contacts')
		{
			$contacts = array();
			$djlUtil = new DejalaUtils();
			$responseArray = $djlUtil->getStoreContacts($this->dejalaConfig, $contacts);
			if ('200' == $responseArray['status'])
			{
				foreach ($contacts as $contactName=>$contactData) {
					foreach ($contactData as $key=>$value) {
						$smarty->assign($contactName.'_'.$key, $value);
					}
				}
			}
			$outputMain = $this->display(__FILE__, 'dejala_contacts.tpl');
		}
		elseif ($currentTab === 'location')
		{
			$location = array();
			$djlUtil = new DejalaUtils();
			$responseArray = $djlUtil->getStoreLocation($this->dejalaConfig, $location);
			if ('200' == $responseArray['status'])
			{
				foreach ($location as $key=>$value)
					$smarty->assign($key, $value);
				$outputMain = $this->display(__FILE__, 'dejala_location.tpl');
			}
		}
		elseif ($currentTab==='processes')
		{
			$processes = array();
			$djlUtil = new DejalaUtils();
			$responseArray = $djlUtil->getStoreProcesses($this->dejalaConfig, $processes);
			if ('200' == $responseArray['status'])
			{
				foreach ($processes as $key=>$value)
					$smarty->assign($key, $value);

				$outputMain = $this->display(__FILE__, 'dejala_processes.tpl');
			}
		}
		elseif ($currentTab==='prices')
		{
			$products = array();
			$djlUtil = new DejalaUtils();
			$responseArray = $djlUtil->getStoreProducts($this->dejalaConfig, $products);
			if ('200' == $responseArray['status'])
			{
				//price = price_HT*(inv_vat)
				foreach ($products as &$product)
				{
					$vat_factor = (1+ ($product['vat'] / 100));
					$product['price_notax'] = number_format($product['price'], 2, '.', '');
					$product['price'] = number_format(round($product['price']*$vat_factor, 2), 2, '.', '');
					$product['public_price'] = number_format(round($product['price'] + $product['margin'], 2), 2, '.', '');
					$product['public_price_notax'] = number_format(round($product['public_price']/$vat_factor, 2), 2, '.', '');
				}
				$smarty->assign('products', $products);
				$outputMain = $this->display(__FILE__, 'dejala_products.tpl');
			}
		}
		elseif ($currentTab === 'accounting')
		{
			$smartifyErrors = $this->smartyfyStoreAttributes();
			if (isset($smartifyErrors) && count($smartifyErrors))
			$errors = $smartifyErrors;

			$djlUtil = new DejalaUtils();
			$deliveries = array();
			$responseArray = $djlUtil->getStoreDeliveries($this->dejalaConfig, $deliveries);

			if ('200'==$responseArray['status'])
			{
				foreach ($deliveries as &$delivery)
				{
					$delivery['creation_date'] = date('d/m/Y', $delivery['creation_utc']);
					$delivery['creation_time'] = date('H\hi', $delivery['creation_utc']);
					if (isset($delivery['shipping_start_utc']))
					{
						$delivery['shipping_date'] = date('d/m/Y', $delivery['shipping_start_utc']);
						$delivery['shipping_start'] = date('H\hi', $delivery['shipping_start_utc']);
						$delivery['shipping_stop'] = date('H\hi', (int)($delivery['shipping_start_utc']) + 3600*(int)($delivery['timelimit']) );
					}
					else
					{
						$delivery['shipping_date'] = '';
						$delivery['shipping_start'] = '';
						$delivery['shipping_stop'] = '';
					}

					if (isset($delivery['delivery_utc']))
					{
						$delivery['delivery_date'] = date('d/m/Y', $delivery['delivery_utc']);
						$delivery['delivery_time'] = date('H\hi', $delivery['delivery_utc']);
					}
				}
				$smarty->assign('formAction', __PS_BASE_URI__ . 'modules/' . $this->name . '/deliveries_csv.php');
				$smarty->assign('defaultDateFrom', date('01/m/Y'));
				$smarty->assign('defaultDateTo', date('d/m/Y'));
				$smarty->assign('deliveries', $deliveries);
				$outputMain = $this->display(__FILE__, 'dejala_deliveries.tpl');
			}
		}
		elseif ($currentTab==='delivery_options')
		{
			$outputMain = $this->displayDeliveryOptions();
		}
		elseif ($_GET['cat']==='technical_options')
		{
			$states = $this->getOrderStates();
			$triggers = explode(',', $this->dejalaConfig->trigerringStatuses);
			$orderStatuses = array();
			foreach ($states as $status)
			{
				$m_status['id'] = (int)$status['id_order_state'];
				$m_status['label'] = $status['name'];
				if (in_array($status['id_order_state'], $triggers))
				$m_status['checked'] = '1';
				else
				$m_status['checked'] = '0';
				$orderStatuses[] = $m_status;
			}
			$smarty->assign('statuses', $orderStatuses);

			$smarty->assign('trigerringStatuses', $this->dejalaConfig->trigerringStatuses);
			$outputMain = $this->display(__FILE__, 'dejala_technical_options.tpl');
		}

		$outputErr = '';
		if (count($errors))
		foreach ($errors AS $err)
		$outputErr .= '<div class="alert error">'. $err .'</div>';

		$output = $outputErr;
		$output = $output . $outputMenu;
		$output = $output . $outputMain;
		$output = $output . $this->display(__FILE__, 'dejala_footer.tpl');
		return $output;
	}


	// put in smarty context store attributes
	function smartyfyStoreAttributes()
	{
		global $smarty;

		$errors = array();
		$djlUtil = new DejalaUtils();
		$storeAttrs = array();
		$response = $djlUtil->getStoreAttributes($this->dejalaConfig, $storeAttrs);
		if (200 != $response['status'])
		$errors[] = $this->l('An error occurred while getting store, please try again later or contact Dejala.com');
		else
		{
			$smarty->assign("account_balance", $storeAttrs['account_balance']);
			$smarty->assign("store_name", $storeAttrs['name']);

			// Check if account exists in production
			$responsePing = $djlUtil->ping($this->dejalaConfig, 'PROD');
			if ('200' == $responsePing['status']) {
				$smarty->assign('isLiveReady', '1');
				$smarty->assign('isLiveRequested', '0');
			}
			else {
				$smarty->assign('isLiveReady', '0');
				if (isset($storeAttrs['attributes']) && isset($storeAttrs['attributes']['request_live']) && ($storeAttrs['attributes']['request_live']=='true'))
				$smarty->assign('isLiveRequested', '1');
				else
				$smarty->assign('isLiveRequested', '0');
			}
		}
		return ($errors);
	}


	function getOrderStates(){
		global $cookie;

		$states = OrderState::getOrderStates($this->id_lang);
		return ($states);
	}

	function displayDeliveryOptions(){
		global $smarty;

		/*
		 Au moment du choix du créneau
		 Pour déterminer le créneau de départ proposé :
		 - Aller sur le prochain créneau libre
		 - Ajouter le délai de traitement de la commande
		 - Aller sur le prochain créneau libre

		 - Le marchand configure l ouverture de sa boutique en weedkay (hStart-hStop) + exception (date fermeture) tous produits confondus
		 On fait le min au moment de l afichage des creneaux dispo
		 => trouver une slideBar avec deux curseurs
		 */
		$output = '';
		$djlUtil = new DejalaUtils();
		$response = $djlUtil->getStoreAttributes($this->dejalaConfig, $store);
		if ($response['status'] == 200)
		{
			$smarty->assign('nb_days', $store['attributes']['nb_days_displayed']);
			$smarty->assign('delivery_delay', $store['attributes']['delivery_delay']);
			if (isset($store['attributes']['delivery_partial']))
			$smarty->assign('delivery_partial', $store['attributes']['delivery_partial']);
		}

		$wday_selected = array(1, 1, 1, 1, 1, 1, 1);

		//		$smarty->assign('timetable_css', _MODULE_DIR_.$this->name.'/timetable.css');
		//		$smarty->assign("timetable_js", _MODULE_DIR_.$this->name.'/timetable.js');
		$smarty->assign("weekdayLabels", $this->wday_labels);
		$smarty->assign("weekdaySelected", $wday_selected);


		$calendar = array();
		$response = $djlUtil->getStoreCalendar($this->dejalaConfig, $calendar);
		if ($response['status'] == 200)
		{
			$smarty->assign("calendar", $calendar);
			$smarty->assign("timetableTpl", dirname(__FILE__)."/dejala_picking_timetable.tpl");
		}
		$output = $output . $this->display(__FILE__, 'dejala_delivery_options.tpl');

		return ($output);
	}

	/**
	 * Retourne FALSE si un des produits du cart n'est pas en stock, retourne FALSE sinon
	 **/
	function isCartOutOfStock($cart)
	{
		$products = $cart->getProducts();
		foreach ($products as $product)
		{
			$this->mylog('product:');
			$this->mylog($this->logValue($product, 1));

			$orderedQuantity = (_PS_VERSION_ < "1.3.0.1" ? (int)($product['quantity']) : (int)($product['cart_quantity']));
			$productQuantity = (int)($product['stock_quantity']);
			if ( ($productQuantity < $orderedQuantity) || ($productQuantity <= 0) )
			return (TRUE);
		}
		return (FALSE);
	}

	/**
	 ** Affiche le transporteur Dejala.com dans la liste des transporteurs sur le Front Office
	 */
	public function hookExtraCarrier($params)
	{
		global $smarty, $defaultCountry;

		$cart = $params['cart'];
		$cookie = $params['cookie'];

		$this->hooklog("ExtraCarrier", $params);

		// Check if Dejala should be visible
		if ($this->dejalaConfig->visibility_status == "invisible")
			return ;

		if (($this->dejalaConfig->visibility_status == "visible_limited") && ((int)($cookie->id_customer) > 0))
		{
			$customer = new Customer((int)($cookie->id_customer));
			if (!in_array($customer->email, preg_split("/[\s,]+/", $this->dejalaConfig->visible_users_list)))
				return ;
		}

		//		$djlUtil = new DejalaUtils();
		//		$responseGetStore = $djlUtil->getStoreAttributes($this->dejalaConfig, $store);
		//		if ($responseGetStore['status']!='200')
		//			return ;
		//
		$isCartOutOfStock = $this->isCartOutOfStock($cart) ;
		$this->mylog('isCartOutOfStock=' . $isCartOutOfStock . '');

		$electedProduct = $this->getDejalaProduct($cart) ;
		$this->mylog("product$=" . $this->logValue($electedProduct,1));

		if (is_null($electedProduct)) return "" ;

		$acceptPartial = false;
		if (isset($electedProduct['store_attributes']) && isset($electedProduct['store_attributes']['delivery_partial']) && ($electedProduct['store_attributes']['delivery_partial'] == '1'))
		$acceptPartial = true;

		if ($isCartOutOfStock && !$acceptPartial)
			return ;

		$djlCarrier = DejalaCarrierUtils::getCarrierByName($this->name) ;

		$this->mylog("electedCarrier=" . $this->logValue($djlCarrier,1));

		if ($djlCarrier == null)
			return null ;

		$dates = $electedProduct['computed_calendar']['entries'] ;

		// Adjust labels
		foreach ($dates as $idx => $date) {
			$dates[$idx]['label'] = $this->wday_labels[date('w', $date['ts'])] . " " . date("j", $date['ts']);
		}

		$this->mylog("date$=" . $this->logValue($dates,1));
		$smarty->assign('nb_days', sizeof($dates));
		$smarty->assign('dates', $dates);
		for ($i=0; $i < 24; $i++) {
			$endHour = (($i+$electedProduct['timelimit'])%24);
			if ($endHour == 0)
			$endHour = 24;
			$hourLabels[] = $i . 'h-' . $endHour . 'h';
		}
		$smarty->assign('hourLabels', $hourLabels);

		$smarty->assign('timetable_css', _MODULE_DIR_.$this->name.'/timetable.css');

		$this->mylog("electedCarrier->id=" . $this->logValue($djlCarrier->id));
		$mCarrier = $djlCarrier;
		$row['id_carrier'] = (int)($djlCarrier->id);
		$row['name'] = $this->l('Dejala.com');
		$row['price'] = $cart->getOrderShippingCost($djlCarrier->id);
		$row['price_tax_exc'] = $cart->getOrderShippingCost($djlCarrier->id, false);
		$row['img'] = _MODULE_DIR_.$this->name.'/dejala_carrier.gif';

		if ($isCartOutOfStock) {
			$row['info'] = $this->l('I will select my shipping date when my product is available.');
		}
		else {
			$row['info'] = $this->l('When you want... Dispatch rider') . ', ' . $electedProduct['timelimit'].'H' ;
		}

		$resultsArray[] = $row;

		$smarty->assign('djlCarriers', $resultsArray);
		$smarty->assign('djlCarrierChecked', (isset($cart->id_carrier) && $cart->id_carrier == $djlCarrier->id ? $djlCarrier->id : -1)) ;
		$smarty->assign('product', $electedProduct);


		$djlCart = new DejalaCart($cart->id);
		$setDefaultDate = TRUE;

		$smarty->assign("deliveryDateSelected", $dates[0]['value']);
		$smarty->assign("deliveryHourSelected", $dates[0]['start_hour']);

		if ($djlCart && isset($djlCart->shipping_date) && !empty($djlCart->shipping_date))
		{
			$mShippingDate = $djlCart->shipping_date;
			$this->mylog("shipping_date=" . $this->logValue($mShippingDate));
			$m_hour = date("H", $mShippingDate);
			$deliveryDateSelected = date("Y/m/d", $mShippingDate);
			$this->mylog("shipping_date=" . $this->logValue($deliveryDateSelected));
			$smarty->assign("deliveryDateSelected", $deliveryDateSelected);
			$smarty->assign("deliveryHourSelected", $m_hour);
			$setDefaultDate = FALSE;
		}

//		$smarty->assign('one_page_checkout', (Configuration::get('PS_ORDER_PROCESS_TYPE') ? Configuration::get('PS_ORDER_PROCESS_TYPE') : 0)) ;

		$smarty->assign("isCartOutOfStock", $isCartOutOfStock);

		$buffer = $this->display(__FILE__, 'dejala_carrier.tpl');
		$buffer = $buffer . $this->display(__FILE__, 'dejala_timetable.tpl');

		return $buffer;

	}

	public function displayInfoByCart($id_cart)
	{
		$this->hooklog("displayInfoByCart", $id_cart);
		$this->myLog("POST=" . $this->logValue($_POST));

		$this->myLog('dejala_action=' . Tools::getValue('dejala_action') );
		if (Tools::getValue('dejala_action')=='order')
		{
			$this->myLog('inside - id_cart=' . $id_cart);
			$mOrderId = (int)Order::getOrderByCartId($id_cart);
			$mOrder = new Order($mOrderId);
			$this->placeOrder($mOrder);
		}
		$djlCart = new DejalaCart($id_cart);
		if ($djlCart && isset($djlCart->id_dejala_product) && isset($djlCart->shipping_date))
		{
			$mDejalaProductID = $djlCart->id_dejala_product;
			$mShippingDate = $djlCart->shipping_date;
			echo '<h4 style="color:red;">';
			if ($djlCart->mode !== 'PROD')
				echo 'MODE : TEST<br/>';

			if (!empty($mShippingDate) && ($mShippingDate != 0))
				echo $this->l('Shipping date selected') . ' : ' .date('d/m/Y',$mShippingDate). ', ' . $this->l('starting at') . ' : ' .date('H\hi', $mShippingDate) .'<br/>';
			else
				echo $this->l('Shipping date not yet selected by the customer') .'<br/>';

			if ( ($djlCart->id_delivery) && Validate::isUnsignedId($djlCart->id_delivery) )
			{
				$l_delivery = array();
				$l_delivery['id'] = $djlCart->id_delivery;
				$djlUtil = new DejalaUtils();
				$response = $djlUtil->getDelivery($this->dejalaConfig, $l_delivery, $djlCart->mode);
				if ($response['status'] == 200)
				{
					if ($l_delivery && $l_delivery['status'] && $l_delivery['status']['labels'] && $l_delivery['status']['labels'][Language::getIsoById((int)$this->id_lang)])
					echo $this->l('Order') . ' ' . $l_delivery['status']['labels'][Language::getIsoById((int)$this->id_lang)].'<br/>';
					else
					echo $this->l('Order sent to Dejala') . '<br/>';
				}
			}
			else
			{
				$_html = '';
				$_html .= '<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">';
				$_html .= '<input type="hidden" name="dejala_action" value="order">';
				$_html .= '<input type="submit" value="Commander la course">';
				$_html .= '</form><br />';
				echo $_html . '';
			}

			echo '</h4>';

		}
	}

	public static function wtf($var, $arrayOfObjectsToHide=null, $fontSize=11)
	{
		$text = print_r($var, true);

		if (is_array($arrayOfObjectsToHide))
		{
			foreach ($arrayOfObjectsToHide as $objectName)
			{

				$searchPattern = '#('.$objectName.' Object\n(\s+)\().*?\n\2\)\n#s';
				$replace = "$1<span style=\"color: #FF9900;\">--&gt; HIDDEN - courtesy of wtf() &lt;--</span>)";
				$text = preg_replace($searchPattern, $replace, $text);
			}
		}

		// color code objects
		$text = preg_replace('#(\w+)(\s+Object\s+\()#s', '<span style="color: #079700;">$1</span>$2', $text);
		// color code object properties
		$text = preg_replace('#\[(\w+)\:(public|private|protected)\]#', '[<span style="color: #000099;">$1</span>:<span style="color: #009999;">$2</span>]', $text);

		echo '<pre style="font-size: '.$fontSize.'px; line-height: '.$fontSize.'px;text-align:left;">'.$text.'</pre>';
	}

	public function hookProcessCarrier($params)
	{
		// FO: Temporary. Necessary to go around the product's cart re-instanciation bug.
		global $cart ;

		$cartParams = $params['cart'];
		$this->hooklog("processCarrier", $params) ;
		// Process the cart for storage in dejala_cart.
		$errors = array();
		$dejalaCarrierID = (int)Tools::getValue('dejala_id_carrier');
		$carrierID = (int)Tools::getValue('id_carrier');
		$dejalaProductID = (int)Tools::getValue('dejala_id_product');

		if ( !empty($dejalaCarrierID) && !empty($carrierID) && ((int)($dejalaCarrierID) == (int)($carrierID)) )
		{
			$id_cart = (int)($cartParams->id);

			$product = $this->getDejalaProduct($cartParams, $dejalaProductID) ;

			$timelimit = 10;
			if (isset($product['timelimit']))
			$timelimit = (int)($product['timelimit']);

			/* manage shipping preferences */
			$date_shipping = 'NULL';
			if (isset($_POST['shipping_day']) AND !empty($_POST['shipping_day']) AND (10 <= strlen($_POST['shipping_day'])) )
			{
				$shippingHour = (int)($_POST['shipping_hour']);
				$shipping_day = $_POST['shipping_day'];
				$ship_year = (int)(substr($shipping_day, 0, 4));
				$ship_month = (int)(substr($shipping_day, 5, 2));
				$ship_day = (int)(substr($shipping_day, 8, 2));
				$shippingTime = mktime($shippingHour, 0, 0, $ship_month, $ship_day, $ship_year);
				// check that delivery date is in the future (5 min delay)
				if ($shippingTime > time() - 5 * 60)
				$date_shipping = $shippingTime;
			}

			$djlCart = $this->getDejalaCart($cartParams->id) ;
			$djlCart->shipping_date = $date_shipping;
			$djlCart->id_dejala_product = $dejalaProductID;
			$djlCart->id_delivery = NULL;
			$djlCart->mode = $this->dejalaConfig->mode;

			// Dirty cheat as the cart updates itself right after this.
			// This saves one full round trip to the server.
			$djlCart->cart_date_upd = date('Y-m-d H:i:s') ;
			$djlCart->save() ;
		}

		// FO: VERY DIRTY HACK.... Re-assign the global cart to what it was before.
		$cart = $cartParams ;
	}

	/**
	 * Keep as a precaution if a hook is still registered
	 */
	public function hookCart($param) {
		return ;
	}

	// Still in development
	public function hookOrderDetailDisplayed($param)
	{
		if (!$this->useBetaOrderDetailPatch) return "" ;

		$this->hooklog("OrderDetailDisplayed", $param);
		$retStr = "" ;
		$djlCart = new DejalaCart($param['order']->id_cart);
		if (Validate::isLoadedObject($djlCart) && isset($djlCart->id_dejala_product) && isset($djlCart->shipping_date) && !empty($djlCart->shipping_date)) {
		$retStr .= "<div class=\"table_block clear\">
					<table class=\"std\">
					<thead>
						<tr>
							<th class=\"first_item\">Dejala</th>
						</tr>
					</thead>
					<tbody>
					<tr class=\"first_item item\">
						<td>" ;
			$mShippingDate = $djlCart->shipping_date;

			if (!empty($mShippingDate)) {
				$retStr .= $this->l('Shipping date selected') . ' : ' .date('d/m/Y',$mShippingDate). ', ' . $this->l('starting at') . ' : ' .date('H\hi', $mShippingDate) ;
			}
			else {
				$retStr .= $this->l('Shipping date not yet selected by the customer') ;
			}
			$retStr .= "			</td>
								</tr>
							</tbody>
						</table>
					</div>" ;
		}
		return $retStr ;
	}

	/**
	 * Called when an order's status changes.
	 **/
	public function hookUpdateOrderStatus($params)
	{
		$this->hooklog("hookUpdateOrderStatus", $params);
		// class OrderState
		$newOrderStatus = $params["newOrderStatus"];
		$currentOrderStatusID = $newOrderStatus->id;
		$this->mylog("newOrderStatus=" . $this->logValue($newOrderStatus));
		$this->mylog("found currentOrderStatusID=" . $currentOrderStatusID);

		$triggeringStatusList = html_entity_decode(Configuration::get('DJL_TRIGERRING_STATUSES'), ENT_COMPAT, 'UTF-8');
		$this->mylog("triggeringStatusList=" . $triggeringStatusList);
		$triggeringStatuses = explode(",", $triggeringStatusList);
		$orderID = $params["id_order"];

		if ((NULL !== $orderID) && (TRUE === in_array($currentOrderStatusID, $triggeringStatuses)))
		{
			$mOrder = new Order($orderID);
			$this->placeOrder($mOrder);
		}
	}


	public function placeOrder($mOrder) {
		$orderID = (int)$mOrder->id;
		$this->myLog("placeOrder()");
		$this->myLog("mOrder->id_carrier=".$mOrder->id_carrier);
		$mCarrier = new Carrier($mOrder->id_carrier);
		$this->myLog("mCarrier->name=".$mCarrier->name);
		if ($mCarrier->name != $this->name)
		return ;
		$this->myLog("placeOrder()");

		$cartId = $mOrder->id_cart;
		$djlCart = $this->getDejalaCart($cartId);
		$this->myLog("djlCart->id_delivery=" . $djlCart->id_delivery);

		if (!$djlCart->id_delivery)
		{
			$this->myLog("id_delivery is not filled");
			$delivery = array();
			$this->getInfoFromOrder($orderID, $delivery);
			$this->mylog("Sending delivery=" . $this->logValue($delivery));
			$djlUtil = new DejalaUtils();
			$response = $djlUtil->orderDelivery($this->dejalaConfig, $delivery, $djlCart->mode);
			$statusCode = $response['status'];

			$this->mylog("send orderID=" . $orderID);
			$this->mylog("sendOrder status_code=" . $statusCode);
			$this->mylog("sendOrder response=" . $response['response']);
			$this->mylog("sendOrder delivery=" . $this->logValue($delivery, 1));
			// update status after sending...
			if ("201" === $statusCode)
			{
				$this->mylog("updating dejala cart cart_id=" . $cartId);
				if (Validate::isUnsignedId($delivery['id']))
				{
					$this->mylog("updating dejala cart id_delivery=" . $delivery['id']);
					$djlCart->id_delivery = $delivery['id'];
					$djlCart->update();
				}

				if (is_null($mOrder->shipping_number) || (0 === strlen($mOrder->shipping_number)))
				{
					$this->myLog('setting Order->shipping_number to ' . $delivery['tracking_number']);
					$mOrder->shipping_number = $delivery['tracking_number'];
					$mOrder->save();
				}

				$this->myLog("OK -  Order sent to dejala.com");
			}
			else
			{
				// Do nothing : Keep previous status
				$this->myLog("NOK - Problem sending Order to dejala.com");
			}
		}
	}

	public function getInfoFromOrder($orderID, &$delivery)
	{
		$mOrder = new Order((int)$orderID);
		if (NULL !== $mOrder)
		{
			$mDeliveryAddress = new Address($mOrder->id_address_delivery);
			if (NULL !== $mDeliveryAddress)
			{
				// receiver address information
				$delivery["receiver_firstname"]=$mDeliveryAddress->firstname;
				$delivery["receiver_name"]=$mDeliveryAddress->lastname;
				if ($mDeliveryAddress->company)
				$delivery["receiver_company"]=$mDeliveryAddress->company;
				$delivery["receiver_address"]=$mDeliveryAddress->address1;
				if ($mDeliveryAddress->address2)
				$delivery["receiver_address2"]=$mDeliveryAddress->address2;
				$delivery["receiver_zipcode"]=$mDeliveryAddress->postcode;
				$delivery["receiver_city"]=$mDeliveryAddress->city;
				if ($mDeliveryAddress->phone_mobile)
				$delivery["receiver_cellphone"]=$mDeliveryAddress->phone_mobile;
				if ($mDeliveryAddress->phone)
				$delivery["receiver_phone"]=$mDeliveryAddress->phone;
				if ($mDeliveryAddress->other)
				$delivery["receiver_comments"]=$mDeliveryAddress->other;
			}
			$delivery["packet_reference"]=$mOrder->id;


			$id_cart = (int)$mOrder->id_cart;
			/* set weight */
			$cart = new Cart($id_cart);
			$delivery['weight'] = Configuration::get('PS_WEIGHT_UNIT') == 'g' ? (float)($cart->getTotalWeight() / 1000)  : (float)($cart->getTotalWeight());

			/* set dejalaProductID and sender_availability = shipping date */
			$djlCart = new DejalaCart($id_cart);
			if (!is_null($djlCart) && !is_null($djlCart->id))
			{
				$mDejalaProductID = (int)$djlCart->id_dejala_product;
				$delivery["product_id"] = (int)($mDejalaProductID);
				$mShippingDate = $djlCart->shipping_date;
				if ( is_null($mShippingDate) || empty($mShippingDate) )
					$mShippingDate = 0;
				$delivery["shipping_start_utc"]=$mShippingDate;
			}
		}
		return ($delivery);
	}


	public function getOrderShippingCostExternal($cart)
	{
		return $this->getOrderShippingCost($cart, 0);
	}

	public function getOrderShippingCost($cart, $shipping_cost)
	{
		$this->hooklog("getOrderShippingCost", null);
		$price = $this->getDejalaProductPrice($cart) ;
		if ($price < 0) {
			$price = $shipping_cost ;
		}
		$this->mylog('returning : ' . $price);
		return $price ;
	}


	private function getDejalaCart($cartId) {
		return DejalaCart::getInstance($cartId) ;
	}

	private function getDejalaProductPrice($cart)
	{
		$djlCart = $this->getDejalaCart($cart->id) ;
		if (isset($djlCart->delivery_price) && $cart->date_upd <= $djlCart->cart_date_upd)
			return $djlCart->delivery_price ;
		$product = $this->getDejalaProduct($cart) ;
		if (is_null($product)) {
			return -1 ;
		}
		return $product["price"] ;
	}

	private function getDejalaProduct($cart, $productId = -1)
	{
		//		echo "Date : " . $cart->date_upd . "<br>" ;
		$djlCart = $this->getDejalaCart($cart->id) ;

		if (isset($djlCart->delivery_price) && $cart->date_upd <= $djlCart->cart_date_upd && isset($djlCart->product))
			if ($productId >= 0 && $djlCart->product["id"] == $productId)
				return $djlCart->product ;

		$djlUtil = new DejalaUtils();
		$responseGetStore = $djlUtil->getStoreAttributes($this->dejalaConfig, $store);
		if ($responseGetStore['status']!='200') return ;

		$isCartOutOfStock = '0';
		if ($this->isCartOutOfStock($cart))
		$isCartOutOfStock = '1';
		$this->mylog('isCartOutOfStock=' . $isCartOutOfStock . '');

		$acceptPartial = true;
		if (!isset($store['attributes']) || !isset($store['attributes']['delivery_partial']) || ($store['attributes']['delivery_partial'] != '1'))
		$acceptPartial = false;
		if ( ($isCartOutOfStock == '1') && !$acceptPartial)
			return ;

		$address = new Address($cart->id_address_delivery) ;

		// ask dejala.com for a quotation
		$quotation["receiver_name"] = $address->lastname;
		$quotation["receiver_firstname"] = $address->firstname;
		$quotation["receiver_company"] = $address->company;
		$quotation["receiver_address"] = $address->address1;
		$quotation["receiver_address2"] = $address->address2;
		$quotation["receiver_zipcode"] = $address->postcode;
		$quotation["receiver_city"] = $address->city;
		$quotation["receiver_phone"] = $address->phone;
		$quotation["receiver_phone_mobile"] = $address->phone_mobile;
		$quotation["receiver_comments"] = $address->other;
		$quotation["timelimit"] = 10;
		$quotation["weight"] = Configuration::get('PS_WEIGHT_UNIT') == 'g' ? (float)($cart->getTotalWeight() / 1000)  : (float)($cart->getTotalWeight());

		$quotation["price"] = $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING) ;
		$quotation["module_version"] = $this->internal_version ;
		$quotation["platform"] = "PS " . _PS_VERSION_ ;

		$this->mylog("asking for quotation=" . $this->logValue($quotation,1));

		$products = array();
		$storeAttributes = array();
		$responseArray = $djlUtil->getStoreQuotation($this->dejalaConfig, $quotation, $products, $storeAttributes);
		if ($responseArray['status']!='200') {
			$djlCart->delivery_price = -1 ;
			$djlCart->cart_date_upd = $cart->date_upd ;
			$djlCart->save() ;
			return null;
		}
		$this->mylog("found quotation=" . $this->logValue($responseArray['response'],1));

		$electedProduct = NULL;
		foreach ($products as $key=>$product)
			if ( is_null($electedProduct) || ((int)($electedProduct['priority']) > (int)($key)) )
				$electedProduct = $product;

		if (is_null($electedProduct)) {
			$djlCart->delivery_price = -1 ;
			$djlCart->cart_date_upd = $cart->date_upd ;
			$djlCart->save() ;
			return null;
		}

		$djlCart->id_dejala_product = (int)$electedProduct["id"];
		$djlCart->id_delivery = NULL;
		$djlCart->mode = $this->dejalaConfig->mode;

		$vat_factor = (1+ ($electedProduct['vat'] / 100));
		$priceTTC = round(($electedProduct['price']*$vat_factor) + $electedProduct['margin'], 2);
		$priceHT = round($priceTTC/$vat_factor, 5);
		$productPrice = $priceHT;

		$djlCart->delivery_price = $priceHT ;
		$djlCart->cart_date_upd = $cart->date_upd ;
		$djlCart->product = $electedProduct ;

		$djlCart->save() ;
		$electedProduct['store_attributes'] = $storeAttributes ;
		return $electedProduct ;

	}

	public function hookPDFInvoice($params) {
		if (!$this->useAlphaPDFInvoicePatcher) return ;
		$order = new Order($params['id_order']) ;
		$djlCart = new DejalaCart($order->id_cart);
		if (Validate::isLoadedObject($djlCart) && isset($djlCart->id_dejala_product) && isset($djlCart->shipping_date) && !empty($djlCart->shipping_date)) {
			$deliveryEndDate = "" ;

			// TODO : Store the delivery timelimit in the dejalacart table at order time and use it here instead of querying something that might have changed.
			$djlUtil = new DejalaUtils();
			$responseArray = $djlUtil->getStoreProductByID($this->dejalaConfig, $djlCart->id_dejala_product, $products);
			if ('200' == $responseArray['status'] && is_array($products)) {
				$deliveryEndDate = date('H\h', $djlCart->shipping_date + (int)$products['timelimit']*3600) ;
			}

			$params['pdf']->pages[1] = str_replace($this->name . ")", $this->name . " \(" . date('d/m/Y',$djlCart->shipping_date). ', ' . date('H\h', $djlCart->shipping_date) . "-" . $deliveryEndDate . "\))", $params['pdf']->pages[1]) ;
		}
	}

	public function mylog($msg) {
		if ($this->DEJALA_DEBUG) {
			require_once(dirname(__FILE__) . "/MyLogUtils.php");
			$myFile = dirname(__FILE__) . "/logFile.txt";
			MyLogUtils::myLog($myFile, $msg);
		}
	}

	// get a string of a value for Log purposes
	public function logValue($mvalue, $lvl=0) {
		if (!$this->DEJALA_DEBUG)
		return ("");
		require_once(dirname(__FILE__) . "/MyLogUtils.php");
		return (MyLogUtils::logValue($mvalue, $lvl));
	}


	public function hooklog($hookname, $params) {
		$this->mylog("Hook : " . $hookname);
		$this->mylog("\r\nparams" . $this->logValue($params), 1);
	}

	// Stolen from PS 1.3 for backwards compatibility in PS 1.2.5
	public static function getHttpHost($http = false, $entities = false)
	{
		$host = (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']);
		if ($entities)
		$host = htmlspecialchars($host, ENT_COMPAT, 'UTF-8');
		if ($http)
		$host = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$host;
		return $host;
	}

//	public function hookLeftColumn($param) {
//		return '<div style="color: red;">hookLeftColumn</div>' ;
//	}
//	public function hookRightColumn($param) {
//		return '<div style="color: red;">hookRightColumn</div>' ;
//	}
//	public function hookHome($param) {
//		return '<div style="color: red;">hookHome</div>' ;
//	}
//	public function hookHeader($param) {
//		return '<div style="color: red;">hookHeader</div>' ;
//	}
//	public function hookTop($param) {
//		return '<div style="color: red;">hookTop</div>' ;
//	}
//	public function hookProductActions($param) {
//		return '<div style="color: red;">hookProductActions</div>' ;
//	}
//	public function hookProductFooter($param) {
//		return '<div style="color: red;">hookProductFooter</div>' ;
//	}
}

