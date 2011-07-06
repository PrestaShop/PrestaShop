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
*  @version  Release: $Revision: 7310 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
if (!defined('_CAN_LOAD_FILES_'))
	exit;

require_once(_PS_MODULE_DIR_.'mondialrelay/classes/MondialRelayClass.php');

class MondialRelay extends Module
{
	const INSTALL_SQL_FILE = 'mrInstall.sql';
	
	private $_postErrors;
	
	static public $modulePath = '';
	static public $moduleURL = '';

	// Added for 1.3 compatibility
	const ONLY_PRODUCTS = 1;
	const ONLY_DISCOUNTS = 2;
	const BOTH = 3;
	const BOTH_WITHOUT_SHIPPING = 4;
	const ONLY_SHIPPING = 5;
	const ONLY_WRAPPING = 6;
	const ONLY_PRODUCTS_WITHOUT_SHIPPING = 7;

	public function __construct()
	{
		$this->name		= 'mondialrelay';
		$this->tab		= 'shipping_logistics';
		$this->version	= '1.6';

		parent::__construct();

		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('Mondial Relay');
		$this->description = $this->l('Deliver in Relay points');
		
		self::initModuleAccess();
		
		// Call everytime if the merchant make a replace file update
		$this->_updateProcess();
	}
	
	public function install()
	{
		global $cookie;
		
		$name = "shipping";
		$title = "Mondial Relay API";

		if (!parent::install())
			return false;

		Db::getInstance()->ExecuteS(
			'SELECT `name` 
			FROM `' . _DB_PREFIX_ . 'hook` 
			WHERE `name` = \''.$name.'\' 
			AND `title` = \''.$title.'\'');

		if (!Db::getInstance()->NumRows())
			Db::getInstance()->Execute('INSERT INTO ' . _DB_PREFIX_ . 'hook 
			(name, title, description, position) 
			VALUES(\''.$name.'\', \''.$title.'\', NULL, 0)');

		if (!$this->registerHookByVersion())
			return false;
		
		if ((!file_exists(self::$modulePath.self::INSTALL_SQL_FILE)) ||
			(!$sql = file_get_contents(self::$modulePath.self::INSTALL_SQL_FILE)))
			return false;

		$sql = str_replace('PREFIX_', _DB_PREFIX_, $sql);
		$sql = preg_split("/;\s*[\r\n]+/", $sql);
		foreach($sql AS $k => $query)
			if (!empty($query))
				Db::getInstance()->Execute(trim($query));

		$result = Db::getInstance()->getRow('
			SELECT id_tab  
			FROM `' . _DB_PREFIX_ . 'tab`
			WHERE class_name="AdminMondialRelay"');

		if (!$result)
		{
			/*tab install */

			$result = Db::getInstance()->getRow('
				SELECT position 
				FROM `' . _DB_PREFIX_ . 'tab` 
				WHERE `id_parent` = 3
				ORDER BY `'. _DB_PREFIX_ .'tab`.`position` DESC');

			$pos = (isset($result['position'])) ? $result['position'] + 1 : 0;

			Db::getInstance()->Execute('
				INSERT INTO ' . _DB_PREFIX_ . 'tab 
				(id_parent, class_name, position, module) 
				VALUES(3, "AdminMondialRelay",  "'.(int)($pos).'", "mondialrelay")');	 	

			$id_tab = Db::getInstance()->Insert_ID();		
			
			$languages = Language::getLanguages();
			foreach ($languages as $language)
				Db::getInstance()->Execute('
				INSERT INTO ' . _DB_PREFIX_ . 'tab_lang 
				(id_lang, id_tab, name) 
				VALUES("'.(int)($language['id_lang']).'", "'.(int)($id_tab).'", "Mondial Relay")');

			$profiles = Profile::getProfiles(Configuration::get('PS_LANG_DEFAULT'));
			foreach ($profiles as $profile)
				Db::getInstance()->Execute('
				INSERT INTO ' . _DB_PREFIX_ . 'access 
				(`id_profile`,`id_tab`,`view`,`add`,`edit`,`delete`)
				VALUES('.$profile['id_profile'].', '.(int)($id_tab).', 1, 1, 1, 1)');

			@copy(_PS_MODULE_DIR_.'mondialrelay/AdminMondialRelay.gif', _PS_IMG_DIR_.'t/AdminMondialRelay.gif');
		}	

		Configuration::updateValue('MONDIAL_RELAY_1_4', '1');
		Configuration::updateValue('MONDIAL_RELAY_INSTALL_UPDATE_1', 1);
		Configuration::updateValue('MONDIAL_RELAY_ORDER_STATE', 3);
		Configuration::updateValue('MONDIAL_RELAY_SECURE_KEY', md5(time().rand(0,10)));
		Configuration::updateValue('MR_GOOGLE_MAP', '1');
		Configuration::updateValue('MR_ENSEIGNE_WEBSERVICE', '');
		Configuration::updateValue('MR_CODE_MARQUE', '');
		Configuration::updateValue('MR_KEY_WEBSERVICE', '');
		Configuration::updateValue('MR_LANGUAGE', '');
		Configuration::updateValue('MR_WEIGHT_COEF', '');
		Configuration::updateValue('PS_MR_SHOP_NAME', Configuration::get('PS_SHOP_NAME'));
		return true;
	}
	
	/*
	** Register hook depending of the Prestashop version used
	*/
	private function registerHookByVersion()
	{
		if (_PS_VERSION_ >= '1.3' &&  
				(!$this->registerHook('shipping') ||
				!$this->registerHook('extraCarrier') ||
				!$this->registerHook('updateCarrier') ||
				!$this->registerHook('newOrder') ||
				!$this->registerHook('BackOfficeHeader')))
			return false;
			
		if (_PS_VERSION_ >= '1.4' &&
				(!$this->registerHook('processCarrier') ||
				!$this->registerHook('orderDetail') ||
				!$this->registerHook('orderDetailDisplayed')))
			return false;
		return true;
	}
	
	public function uninstall()
	{
		if (!parent::uninstall())
			return false;
		
	/* Tab uninstallation */
		$result = Db::getInstance()->getRow('
			SELECT id_tab  
			FROM `' . _DB_PREFIX_ . 'tab`
			WHERE class_name="AdminMondialRelay"');
		if ($result)
		{
			$id_tab = $result['id_tab'];
			if (isset($id_tab) && !empty($id_tab))
			{	
				Db::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'tab WHERE id_tab = '.(int)($id_tab));
				Db::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'tab_lang WHERE id_tab = '.(int)($id_tab));
				Db::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'access WHERE id_tab = '.(int)($id_tab));
			}
		}

		if (!Configuration::deleteByName('MONDIAL_RELAY_1_4') ||
				!Configuration::deleteByName('MONDIAL_RELAY_INSTALL_UPDATE') ||
				!Configuration::deleteByName('MONDIAL_RELAY_SECURE_KEY') ||
				!Configuration::deleteByName('MONDIAL_RELAY_ORDER_STATE') ||
				!Configuration::deleteByName('MR_GOOGLE_MAP') ||
				!Configuration::deleteByName('MR_ENSEIGNE_WEBSERVICE') ||
				!Configuration::deleteByName('MR_CODE_MARQUE') ||
				!Configuration::deleteByName('MR_KEY_WEBSERVICE') ||
				!Configuration::deleteByName('MR_WEIGHT_COEF') ||
				!Configuration::deleteByName('PS_MR_SHOP_NAME') || 
				!Db::getInstance()->Execute('
					DROP TABLE '._DB_PREFIX_ .'mr_historique, 
					'._DB_PREFIX_ .'mr_method, 
						'._DB_PREFIX_ .'mr_selected'))
			return false;
			
		if (_PS_VERSION_ >= '1.4' && 
				!Db::getInstance()->Execute('
					UPDATE  '._DB_PREFIX_ .'carrier  
					SET `active` = 0, `deleted` = 1 
					WHERE `external_module_name` = "mondialrelay"'))
			return false;
		else if (!Db::getInstance()->Execute('
					UPDATE  '._DB_PREFIX_ .'carrier  
					SET `active` = 0, `deleted` = 1 
					WHERE `name` = "mondialrelay"'))
			return false; 
			
		return true;
	}
	
	private function _updateProcess()
	{
		$this->_update_v1_4();
		$this->_update_v1_4_2();
	}
	
	private function _update_v1_4()
	{
		if (Module::isInstalled('mondialrelay') && 
			!Configuration::get('MONDIAL_RELAY_1_4'))
		{
			Configuration::updateValue('MONDIAL_RELAY_1_4', 1);
			Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'carrier` 
				SET 
					`shipping_external` = 0, 
					`need_range` = 1, 
					`external_module_name` = 
					"mondialrelay", 
					`shipping_method` = 1 
				WHERE `id_carrier` 
				IN (SELECT `id_mr_method` 
						FROM `'._DB_PREFIX_.'mr_method`)');
			return true;
		}
		return false;
	}
	
	private function _update_v1_4_2()
	{
		if (!$this->isRegisteredInHook('newOrder'))
			$this->registerHook('newOrder');
		if (!$this->isRegisteredInHook('BackOfficeHeader'))
			$this->registerHook('BackOfficeHeader');
	}
	
	/*
	** Init the access directory module for URL and file system
	** Allow a compatibility for Presta < 1.4
	*/
	static public function initModuleAccess()
	{
		self::$modulePath =	_PS_MODULE_DIR_. 'mondialrelay/';
	
		$protocol = (Configuration::get('PS_SSL_ENABLED') || (!empty($_SERVER['HTTPS']) 
			&& strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
		
		$endURL = __PS_BASE_URI__.'/modules/mondialrelay/';
	
		if (method_exists('Tools', 'getShopDomainSsl'))
			self::$moduleURL = $protocol.Tools::getShopDomainSsl().$endURL;
		else
			self::$moduleURL = $protocol.$_SERVER['HTTP_HOST'].$endURL;			
	}
	
	/*
	** Override a jQuery version included by another one us.
	** Allow a compatibility for Presta < 1.4
	*/
	static public function getJqueryCompatibility()
	{
		return '
			<script type="text/javascript">
				jq13 = jQuery.noConflict(true); 
			</script>
			<script type="text/javascript" src="'.self::$moduleURL.'/jquery-1.4.4.min.js"></script>';
	}
	
	public function hookNewOrder($params)
	{
		DB::getInstance()->Execute('
			UPDATE `'._DB_PREFIX_.'mr_selected`
			SET `id_order` = '.(int)$params['order']->id.'
			WHERE `id_cart` = '.(int)$params['cart']->id);
	}
	
	public function hookBackOfficeHeader()
	{
		$cssFilePath = $this->_path.'style.css';
		$jsFilePath= $this->_path.'mondialrelay.js';
		$mrtoken = sha1('mr'._COOKIE_KEY_.'mrAgain');
		
		return '
			<link type="text/css" rel="stylesheet" href="'.$cssFilePath.'" />
			<script type="text/javascript">
				var _PS_MR_MODULE_DIR_ = "'.self::$moduleURL.'";
				var mrtoken = "'.$mrtoken.'";
			</script>
			<script type="text/javascript" src="'.$jsFilePath.'"></script>';
	}
	
	private function _postValidation()
	{
		if (Tools::isSubmit('submitMR'))
		{
			if (Tools::getValue('mr_Enseigne_WebService') != '' AND !preg_match("#^[0-9A-Z]{2}[0-9A-Z ]{6}$#", Tools::getValue('mr_Enseigne_WebService')))
				$this->_postErrors[] = $this->l('Invalid Shop');
			if (Tools::getValue('mr_code_marque') != '' AND !preg_match("#^[0-9]{2}$#", Tools::getValue('mr_code_marque')))
				$this->_postErrors[] = $this->l('Invalid Mark code');
			if (Tools::getValue('mr_Key_WebService') != '' AND !preg_match("#^[0-9A-Za-z_\'., /\-]{2,32}$#", Tools::getValue('mr_Key_WebService')))
				$this->_postErrors[] = $this->l('Invalid Webservice Key');
			if (Tools::getValue('mr_Langage') != '' AND !preg_match("#^[A-Z]{2}$#", Tools::getValue('mr_Langage')))
				$this->_postErrors[] = $this->l('Invalid Language');
			if (!Tools::getValue('mr_weight_coef') OR !Validate::isInt(Tools::getValue('mr_weight_coef')))
				$this->_postErrors[] = $this->l('Invalid Weight Coefficient');
		}
		else if (Tools::isSubmit('submitMethod'))
		{
			if (Configuration::get('MR_ENSEIGNE_WEBSERVICE') == '' OR Configuration::get('MR_CODE_MARQUE') == '' OR
				Configuration::get('MR_KEY_WEBSERVICE') == '' OR Configuration::get('MR_LANGUAGE') == '')
				$this->_postErrors[] = $this->l('Please configure your Mondial Relay account settings before creating a carrier.');
			if (!preg_match("#^[0-9A-Za-z_\'., /\-]{2,32}$#", Tools::getValue('mr_Name')))
				$this->_postErrors[] = $this->l('Invalid carrier name');
			if (Tools::getValue('mr_ModeCol') != 'CCC')
				$this->_postErrors[] = $this->l('Invalid Col mode');
			if (!preg_match("#^REL|24R|ESP|DRI|LDS|LDR|LD1$#", Tools::getValue('mr_ModeLiv')))
				$this->_postErrors[] = $this->l('Invalid delivery mode');
			if (!Validate::isInt(Tools::getValue('mr_ModeAss')) OR Tools::getValue('mr_ModeAss') > 5 OR Tools::getValue('mr_ModeAss') < 0)
				$this->_postErrors[] = $this->l('Invalid Assurance mode');
			if (!Tools::getValue('mr_Pays_list'))
				$this->_postErrors[] = $this->l('You must choose at least one delivery country.');
		}
		else if (Tools::isSubmit('submit_order_state'))
		{
			if (!Validate::isBool(Tools::getValue('mr_google_key')))
				$this->_postErrors[] = $this->l('Invalid google key');
			if (!Validate::isUnsignedInt(Tools::getValue('id_order_state')))
				$this->_postErrors[] = $this->l('Invalid order state');
		}
		else if (Tools::isSubmit('PS_MRSubmitFieldPersonalization'))
		{
			$addr1 = Tools::getValue('Expe_ad1');
			if (!preg_match('#^[0-9A-Z_\-\'., /]{2,32}$#', strtoupper($addr1), $match))
				$this->_postErrors[] = $this->l('The Main address submited hasn\'t a good format');
		}
	}

	private function _postProcess()
	{
		foreach($_POST AS $key => $value)
		{
				$setArray[] = $value;
				$keyArray[] = pSQL($key);						
		}
		array_pop($setArray);
		array_pop($keyArray);
	
		if (isset($_POST['submitMR']) AND $_POST['submitMR'])
			self::mrUpdate('settings', $setArray, $keyArray);
		else if (isset($_POST['submitShipping']) AND $_POST['submitShipping'])
			self::mrUpdate('shipping', $_POST, array());
		else if (Tools::getValue('PS_MRSubmitFieldPersonalization'))
			$this->updateFieldsPersonalization();
		else if (isset($_POST['submitMethod']) AND $_POST['submitMethod'])
			self::mrUpdate('addShipping', $setArray, $keyArray);
		else if (isset($_POST['submit_order_state']) AND $_POST['submit_order_state'])
		{
			Configuration::updateValue('MONDIAL_RELAY_ORDER_STATE', Tools::getValue('id_order_state'));
			Configuration::updateValue('MR_GOOGLE_MAP', Tools::getValue('mr_google_key'));
			$this->_html .= '<div class="conf confirm"><img src="'._PS_ADMIN_IMG_.'/ok.gif" alt="" /> '.$this->l('Settings updated').'</div>';
		}
	}
	
	public function getmrth($id_lang, $active = false, $id_zone = false, $id_iso_code = false)
	{
		if (!Validate::isBool($active))
			die(Tools::displayError());

		$carriers = Db::getInstance()->ExecuteS('
			SELECT c.*, cl.delay
			FROM `'._DB_PREFIX_.'mr_method` m
			LEFT JOIN `'._DB_PREFIX_.'carrier` c ON (c.`id_carrier` = m.`id_carrier` and c.`deleted` = 0)
			LEFT JOIN `'._DB_PREFIX_.'carrier_lang` cl ON (c.`id_carrier` = cl.`id_carrier` AND cl.`id_lang` = '.(int)($id_lang).')
			LEFT JOIN `'._DB_PREFIX_.'carrier_zone` cz  ON (cz.`id_carrier` = c.`id_carrier`)'.
			($id_zone ? 'LEFT JOIN `'._DB_PREFIX_.'zone` z  ON (z.`id_zone` = '.(int)($id_zone).')' : '').'
			WHERE 1  '.
			($id_iso_code ? ' AND m.`mr_Pays_list` LIKE \'%'.pSQL($id_iso_code).'%\'' : '').
			($active ? ' AND c.`active` = 1' : '').
			($id_zone ? ' AND cz.`id_zone` = '.(int)($id_zone).'
			AND z.`active` = 1' : '').'
			GROUP BY c.`id_carrier`');
		
		if (!is_array($carriers))
			$carriers = array();
		foreach ($carriers as $key => $carrier)
			if ($carrier['name'] == '0')
				$carriers[$key]['name'] = Configuration::get('PS_SHOP_NAME');
		return $carriers;
	}
	
	public function hookOrderDetail($params)
	{
		global $smarty;
		
		$carrier = $params['carrier'];
		$order = $params['order'];
	
		if ($carrier->is_module AND $order->shipping_number)
	 	{
			$module = $carrier->external_module_name;
			include_once(_PS_MODULE_DIR_.$module.'/'.$module.'.php');
			$module_carrier = new $module();
			$smarty->assign('followup', $module_carrier->get_followup($order->shipping_number));
		}
		else if ($carrier->url AND $order->shipping_number)
			$smarty->assign('followup', str_replace('@', $order->shipping_number, $carrier->url));
	}
	
	public function hookOrderDetailDisplayed($params)
	{
		global $smarty;
	
		$res = Db::getInstance()->getRow('
		SELECT s.`MR_Selected_LgAdr1`, s.`MR_Selected_LgAdr2`, s.`MR_Selected_LgAdr3`, s.`MR_Selected_LgAdr4`, s.`MR_Selected_CP`, s.`MR_Selected_Ville`, s.`MR_Selected_Pays`, s.`MR_Selected_Num`
		FROM `'._DB_PREFIX_.'mr_selected` s
		WHERE s.`id_cart` = '.$params['order']->id_cart);
		if ((!$res) OR ($res['MR_Selected_Num'] == 'LD1') OR ($res['MR_Selected_Num'] == 'LDS'))
			return '';
		$smarty->assign('mr_addr', $res['MR_Selected_LgAdr1'].($res['MR_Selected_LgAdr1'] ? ' - ' : '').$res['MR_Selected_LgAdr2'].($res['MR_Selected_LgAdr2'] ? ' - ' : '').$res['MR_Selected_LgAdr3'].($res['MR_Selected_LgAdr3'] ? ' - ' : '').$res['MR_Selected_LgAdr4'].($res['MR_Selected_LgAdr4'] ? ' - ' : '').$res['MR_Selected_CP'].' '.$res['MR_Selected_Ville'].' - '.$res['MR_Selected_Pays']);
		return $this->display(__FILE__, 'orderDetail.tpl');
	}
	
	public function hookProcessCarrier($params, $redirect = true)
	{
		$cart = $params['cart'];
		$result_MR = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'mr_method` WHERE `id_carrier` = '.(int)($cart->id_carrier));
		if (count($result_MR) > 0) 
		{
			$mr_mode_liv = $result_MR[0]['mr_ModeLiv'];
			if ($mr_mode_liv == 'LDS' || $mr_mode_liv == 'LD1')
			{
				$deliveryAddressLDS = new Address((int)($cart->id_address_delivery));
				if (Validate::isLoadedObject($deliveryAddressLDS) AND ($deliveryAddressLDS->id_customer == $cart->id_customer))
     			{
	 				Db::getInstance()->delete(_DB_PREFIX_.'mr_selected','id_cart = "'.(int)($cart->id).'"');
					$mrselected = new MondialRelayClass();
					$mrselected->id_customer = $cart->id_customer;
					$mrselected->id_method = $result_MR[0]['id_mr_method'];
					$mrselected->id_cart = $cart->id;
					$mrselected->MR_Selected_Num = $mr_mode_liv;
					$mrselected->save();
	 			}
			}
			else if (!Configuration::get('PS_ORDER_PROCESS_TYPE'))
			{
				// Redirect is set to false in Presta 1.3 for compatibility 
				// when this method is called under an ajax process
				if (empty($_POST['MR_Selected_Num_'.$cart->id_carrier]) && $redirect) // Case error : the customer didn't choose a 'relais' but selected Relais Colis TNT as a carrier 
					Tools::redirect('index.php?controller=order&step=2&mr_null');
				else
				{
					Db::getInstance()->delete(_DB_PREFIX_.'mr_selected','id_cart = "'.(int)($cart->id).'"');
					$mrselected = new MondialRelayClass();
					$mrselected->id_customer = $cart->id_customer;
					$mrselected->id_method = $result_MR[0]['id_mr_method'];
					$mrselected->id_cart = $cart->id;
					$mrselected->MR_Selected_Num = $_POST['MR_Selected_Num_'.$cart->id_carrier];
					$mrselected->MR_Selected_LgAdr1 = $_POST['MR_Selected_LgAdr1_'.$cart->id_carrier];
					$mrselected->MR_Selected_LgAdr2 = $_POST['MR_Selected_LgAdr2_'.$cart->id_carrier];
					$mrselected->MR_Selected_LgAdr3 = $_POST['MR_Selected_LgAdr3_'.$cart->id_carrier];
					$mrselected->MR_Selected_LgAdr4 = $_POST['MR_Selected_LgAdr4_'.$cart->id_carrier];
					$mrselected->MR_Selected_CP = $_POST['MR_Selected_CP_'.$cart->id_carrier];
					$mrselected->MR_Selected_Ville = $_POST['MR_Selected_Ville_'.$cart->id_carrier];
					$mrselected->MR_Selected_Pays = $_POST['MR_Selected_Pays_'.$cart->id_carrier];
					$mrselected->save();
				}
			}
		}
	}
	
	public function hookupdateCarrier($params)
	{
		$new_carrier = $params['carrier'];
		// Depends of the Prestashop version, the matches key isn't the same
		if ((_PS_VERSION_ >= '1.4' && $new_carrier->external_module_name == 'mondialrelay') ||
				$new_carrier->name = 'mondialrelay')
		{
				$mr_data = Db::getInstance()->getRow('
					SELECT * 
					FROM `'._DB_PREFIX_.'mr_method` 
					WHERE `id_carrier` = '.(int)($params['id_carrier']));
				
				Db::getInstance()->Execute('
					INSERT INTO `'._DB_PREFIX_.'mr_method` 
					(mr_Name, mr_Pays_list, mr_ModeCol, mr_ModeLiv, mr_ModeAss, id_carrier)
					VALUES (
						"'.pSQL($mr_data['mr_Name']).'", 
						"'.pSQL($mr_data['mr_Pays_list']).'", 
						"'.pSQL($mr_data['mr_ModeCol']).'", 
						"'.pSQL($mr_data['mr_ModeLiv']).'", 
						"'.pSQL($mr_data['mr_ModeAss']).'", 
						'.(int)($new_carrier->id).')');
		}
	}
	
	public function hookextraCarrier($params)
	{	
		global $smarty, $cart, $cookie, $defaultCountry, $nbcarriers;

		if (Configuration::get('MR_ENSEIGNE_WEBSERVICE') == '' OR
			Configuration::get('MR_CODE_MARQUE') == '' OR
			Configuration::get('MR_KEY_WEBSERVICE') == '' OR
			Configuration::get('MR_LANGUAGE') == '')
			return '';

		$totalweight = Configuration::get('MR_WEIGHT_COEF') * $cart->getTotalWeight();
	
		if (Validate::isUnsignedInt($cart->id_carrier))
		{
			$carrier = new Carrier((int)($cart->id_carrier));
			if ($carrier->active AND !$carrier->deleted)
				$checked = (int)($cart->id_carrier);
		}
		if (!isset($checked) OR $checked == 0)
			$checked = (int)(Configuration::get('PS_CARRIER_DEFAULT'));

		$address = new Address((int)($cart->id_address_delivery));
		$id_zone = Address::getZoneById((int)($address->id));
		$country = new Country((int)($address->id_country));
	
		$query = self::getmrth((int)($cookie->id_lang), true, (int)($country->id_zone), $country->iso_code);

		$resultsArray = array();
		$i = 0;
		foreach ($query AS $k => $row)
		{
			$carrier = new Carrier((int)($row['id_carrier']));
			if ((Configuration::get('PS_SHIPPING_METHOD') AND $carrier->getMaxDeliveryPriceByWeight($id_zone) === false) OR
				(!Configuration::get('PS_SHIPPING_METHOD') AND $carrier->getMaxDeliveryPriceByPrice($id_zone) === false))
			{
				unset($result[$k]);
				continue ;
			}

			if ($row['range_behavior'])
			{
				// Get id zone
				if (isset($cart->id_address_delivery) AND $cart->id_address_delivery)
					$id_zone = Address::getZoneById((int)($cart->id_address_delivery));
				else
					$id_zone = (int)$defaultCountry->id_zone;
				if ((Configuration::get('PS_SHIPPING_METHOD') AND (!Carrier::checkDeliveryPriceByWeight($row['id_carrier'], $cart->getTotalWeight(), $id_zone))) OR
					(!Configuration::get('PS_SHIPPING_METHOD') AND (!Carrier::checkDeliveryPriceByPrice($row['id_carrier'], $cart->getOrderTotal(true, self::BOTH_WITHOUT_SHIPPING), $id_zone, $cart->id_currency))))
					{
						unset($result[$k]);
						continue ;
					}
			}

			$settings = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'mr_method` WHERE `id_carrier` = '.(int)($row['id_carrier']));
			$row['name'] = $settings[0]['mr_Name'];
			$row['col'] = $settings[0]['mr_ModeCol'];
			$row['liv'] = $settings[0]['mr_ModeLiv'];
			$row['ass'] = $settings[0]['mr_ModeAss'];
			$row['price'] = $cart->getOrderShippingCost((int)($row['id_carrier']));
			$row['img'] = file_exists(_PS_SHIP_IMG_DIR_.(int)($row['id_carrier']).'.jpg') ? _THEME_SHIP_DIR_.(int)($row['id_carrier']).'.jpg' : '';

			$resultsArray[] = $row;
			$i++;
	 	}

		if ($i > 0)
		{
			include_once(_PS_MODULE_DIR_.'mondialrelay/page_iso.php');

			$smarty->assign( array(
							'address_map' => $address->address1.', '.$address->postcode.', '.ote_accent($address->city).', '.$country->iso_code,
							'input_cp'  => $address->postcode,
							'input_ville'  => ote_accent($address->city),
							'input_pays'  => $country->iso_code,
							'input_poids'  => Configuration::get('MR_WEIGHT_COEF') * $cart->getTotalWeight(),
							'nbcarriers' => $nbcarriers,
							'checked' => (int)($checked),
							'google_api_key' => Configuration::get('MR_GOOGLE_MAP'),
							'one_page_checkout' => (Configuration::get('PS_ORDER_PROCESS_TYPE') ? Configuration::get('PS_ORDER_PROCESS_TYPE') : 0),
							'new_base_dir' => self::$moduleURL,
							'carriersextra' => $resultsArray));
			$nbcarriers = $nbcarriers + $i;
			return $this->display(__FILE__, 'mondialrelay.tpl');
		}
	}
	
	public function getContent()
	{	
		global $cookie;
		
		$error = null;
		
		$html = '';
		if (!empty($_POST))
		{
			$this->_postValidation();
			if (!sizeof($this->_postErrors))
				$this->_postProcess();
			else
			{
				$nbErrors = sizeof($this->_postErrors);
				$this->_html .= '<div class="alert error"><h3>'.$nbErrors.' '.($nbErrors > 1 ? $this->l('errors') : $this->l('error')).'</h3><ol>';
				foreach ($this->_postErrors AS $error)
					$this->_html .= '<li>'.$error.'</li>';
				$this->_html .= '</ol></div>';
			}
		}
		
		if (isset($_GET['delete_mr']) && !empty($_GET['delete_mr']))
			self::mrDelete((int)($_GET['delete_mr']));

		$this->_html .= '<h2>'.$this->l('Configure Mondial Relay Rate Module').'</h2>
		<fieldset>
			<legend>
				<img src="../modules/mondialrelay/images/logo.gif" />'.$this->l('To create a Mondial Relay carrier').
			'</legend>
		- '.$this->l('Enter and save your Mondial Relay account settings').'<br />
		- '.$this->l('Create a Carrier').'<br />
				- '.$this->l('Define a price for your carrier on').' 
					<a href="index.php?tab=AdminCarriers&token='.Tools::getAdminToken('AdminCarriers'.(int)(Tab::getIdFromClassName('AdminCarriers')).
					(int)($cookie->id_employee)).'" class="green">'.$this->l('The Carrier page').'</a><br />
				- '.$this->l('To generate labels, you must have a valid and registered address of your store on your').
					' <a href="index.php?tab=AdminContact&token='.Tools::getAdminToken('AdminContact'.(int)(Tab::getIdFromClassName('AdminContact')).
					(int)($cookie->id_employee)).'" class="green">'.$this->l('contact page').'</a><br />
				<p>
					'.$this->l('URL Cron Task:').' '.Tools::getHttpHost(true, true)._MODULE_DIR_.$this->name.'/cron.php?secure_key='.Configuration::get('MONDIAL_RELAY_SECURE_KEY').
				'</p>
		</fieldset>
		<br class="clear" />
		<div class="PS_MRFormType">'.
			$this->settingsForm().
		'</div>
		<div class="PS_MRFormType">'.
			$this->settingsstateorderForm().
		'</div>
		<div class="PS_MRFormType">'.
			$this->personalizeFormFields().
		'</div>
		<div class="PS_MRFormType">'.
			$this->addMethodForm().
			'</div>
		<div class="PS_MRFormType">'.
			$this->shippingForm().
			'</div><br class="clear" />';
		return $this->_html;
	}
	
	/*
	** Update the new defined fields of the merchant
	*/
	public function updateFieldsPersonalization()
	{
		Configuration::updateValue('PS_MR_SHOP_NAME', Tools::getValue('Expe_ad1'));
		$this->_html .= '<div class="conf confirm"><img src="'._PS_ADMIN_IMG_.'/ok.gif" alt="" /> '.$this->l('Settings updated').'</div>';		
	}
	
	public function mrDelete($id)
	{
		$id = Db::getInstance()->getValue('SELECT `id_carrier` FROM `'._DB_PREFIX_ .'mr_method` WHERE `id_mr_method` = "'.(int)($id).'"');
		Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_ .'carrier` SET `active` = 0, `deleted` = 1 WHERE `id_carrier` = "'.(int)($id).'"');
		$this->_html .= '<div class="conf confirm"><img src="'._PS_ADMIN_IMG_.'/ok.gif" /> '.$this->l('Delete successful').'</div>';
	}
	
	public function mrUpdate($type, $array, $keyArray)
	{
		global $cookie;
		
		if ($type == 'settings')
		{
			Configuration::updateValue('MR_ENSEIGNE_WEBSERVICE', $array[0]);
			Configuration::updateValue('MR_CODE_MARQUE', $array[1]);
			Configuration::updateValue('MR_KEY_WEBSERVICE', $array[2]);
			Configuration::updateValue('MR_LANGUAGE', $array[3]);
			Configuration::updateValue('MR_WEIGHT_COEF', $array[4]);
		}
		else if ($type == 'shipping')
		{
			array_pop($array);
			foreach ($array AS $Key => $value)
			{
				$key    = explode(',', $Key);
				$id = Db::getInstance()->getValue('SELECT `id_carrier` FROM `'._DB_PREFIX_ .'mr_method` WHERE `id_mr_method` = "'.(int)($key[0]).'"');
				Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'carrier SET active = "'.(int)($value).'" WHERE `id_carrier` = "'.(int)($id).'"');
			}
		}
		else if ($type == 'addShipping') 
		{
			$query = 'INSERT INTO ' .  _DB_PREFIX_ . 'mr_method (';

			for ($q = 0; $q <= count($keyArray) - 1; $q++)
			{	
				$end    = ($q == count($keyArray) - 1) ? '' : ', ';
				$query .= $keyArray[$q] . $end;
			}
			
			$query .= ') VALUES(';
			
			for ($j = 0; $j <= count($array) - 1; $j++)
			{
				$var = $array[$j];
				if (is_array($var))
					$var = implode(",", $var);
				$end    = ($j == count($array) - 1) ? '' : ', ';
				$query .= "'" . pSQL($var). "'" . $end;
			}
			$query .= ')';

			Db::getInstance()->Execute($query);
		
			$mainInsert = mysql_insert_id();
			$default = Db::getInstance()->ExecuteS("SELECT * FROM " . _DB_PREFIX_ . "configuration WHERE name = 'PS_CARRIER_DEFAULT'");
			$check   = Db::getInstance()->ExecuteS("SELECT * FROM " . _DB_PREFIX_ . "carrier");
			$checkD = array();

			foreach($check AS $Key)
			{
				foreach($Key AS $key => $value)
					if($key == "id_carrier")
						$checkD[] = $value;
			}

			// Added for 1.3 compatibility to match with the right key
			if (_PS_VERSION_ >= '1.4')
				Db::getInstance()->Execute('
					INSERT INTO `' . _DB_PREFIX_ . 'carrier` 
					(`id_tax_rules_group`, `url`, `name`, `active`, `is_module`, `range_behavior`, `shipping_external`, `need_range`, `external_module_name`, `shipping_method`)
									VALUES("0", NULL, "'.pSQL($array[0]).'", "1", "1", "1", "0", "1", "mondialrelay", "1")');
			else
				Db::getInstance()->Execute('
				INSERT INTO `' . _DB_PREFIX_ . 'carrier`
				(`url`, `name`, `active`, `is_module`, `range_behavior`)
				VALUES(NULL, "'.pSQL('mondialrelay').'", "1", "1", "1")');

			$get   = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'carrier` WHERE `id_carrier` = "' . mysql_insert_id() . '"');
			Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'mr_method` SET `id_carrier` = "' . (int)($get['id_carrier']) . '" WHERE `id_mr_method` = "' . pSQL($mainInsert) . '"');
			$weight_coef = Configuration::get('MR_WEIGHT_COEF');
			$range_weight = array('24R' => array(0, 20000 / $weight_coef), 'DRI' => array(20000 / $weight_coef, 130000 / $weight_coef), 'LD1' => array(0, 60000 / $weight_coef), 'LDS' => array(30000 / $weight_coef, 130000 / $weight_coef));
			Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'range_weight` (`id_carrier`, `delimiter1`, `delimiter2`)
										VALUES ('.(int)($get['id_carrier']).', '.$range_weight[$array[2]][0].', '.$range_weight[$array[2]][1].')');
			Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'range_price` (`id_carrier`, `delimiter1`, `delimiter2`) VALUES ('.(int)($get['id_carrier']).', 0.000000, 10000.000000)');
			$groups = Group::getGroups(Configuration::get('PS_LANG_DEFAULT'));
			foreach ($groups as $group)

				Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'carrier_group` (id_carrier, id_group) VALUES('.(int)($get['id_carrier']).', '.(int)($group['id_group']).')');
			
			$zones = Zone::getZones();
			foreach ($zones as $zone)
			{
				Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'carrier_zone` (id_carrier, id_zone) VALUES('.(int)($get['id_carrier']).', '.(int)($zone['id_zone']).')');
				$range_price_id = Db::getInstance()->getValue('SELECT id_range_price FROM ' . _DB_PREFIX_ . 'range_price WHERE id_carrier = "'.(int)($get['id_carrier']).'"');
				$range_weight_id = Db::getInstance()->getValue('SELECT id_range_weight FROM ' . _DB_PREFIX_ . 'range_weight WHERE id_carrier = "'.(int)($get['id_carrier']).'"');
				Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'delivery` (id_carrier, id_range_price, id_range_weight, id_zone, price) VALUES('.(int)($get['id_carrier']).', '.(int)($range_price_id).', NULL,'.(int)($zone['id_zone']).', 0.00)');
				Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'delivery` (id_carrier, id_range_price, id_range_weight, id_zone, price) VALUES('.(int)($get['id_carrier']).', NULL, '.(int)($range_weight_id).','.(int)($zone['id_zone']).', 0.00)');
			}
			
			if(!in_array($default[0]['value'], $checkD))
				$default = Db::getInstance()->ExecuteS("UPDATE " . _DB_PREFIX_ . "configuration SET value = '" . (int)($get['id_carrier']) . "' WHERE name = 'PS_CARRIER_DEFAULT'");
		}
		else 
			return false;

		$this->_html .= '<div class="conf confirm"><img src="'._PS_ADMIN_IMG_.'/ok.gif" /> '.$this->l('Settings updated').'<img src="http://www.prestashop.com/modules/mondialrelay.png?enseigne='.urlencode(Tools::getValue('mr_Enseigne_WebService')).'" style="float:right" /></div>';
		return true;
	}
	
	public function addMethodForm()
	{
		$zones = Db::getInstance()->ExecuteS("SELECT * FROM " . _DB_PREFIX_ . "zone WHERE active = 1");
		$output = '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post" >
			<fieldset>
				<legend><img src="../modules/mondialrelay/images/logo.gif" alt="" />'.$this->l('Add a Shipping Method').'</legend>
				<ul>
					<li class="PS_MRRequireFields">
						<sup>* ' . $this->l('Required') . '</sup>
					</li>
					<li>
						<label for="mr_Name" class="shipLabel">'.$this->l('Carrier\'s name').'<sup>*</sup></label>
						<input type="text" id="mr_Name" name="mr_Name" '.(Tools::getValue('mr_Name') ? 'value="'.Tools::getValue('mr_Name').'"' : '').'/>
					</li>
					<li>
						<label for="mr_ModeCol" class="shipLabel">'.$this->l('Collection Mode').'<sup>*</sup></label>
						<select name="mr_ModeCol" id="mr_ModeCol" style="width:200px">
							<option value="CCC" selected >CCC : '.$this->l('Collection at the store').'</option>
						</select> 
					</li>

					<li>
						<label for="mr_ModeLiv" class="shipLabel">'.$this->l('Delivery mode').'<sup>*</sup></label>
						<select name="mr_ModeLiv" id="mr_ModeLiv" style="width:200px">
						<option value="24R" selected >24R : '.$this->l('Delivery to a relay point').'</option>
						<option value="DRI" >DRI : '.$this->l('Colis Drive delivery').'</option>
						<option value="LD1" >LD1 : '.$this->l('Home delivery RDC (1 person)').'</option>
						<option value="LDS" >LDS : '.$this->l('Special Home delivery (2 persons)').'</option>
						</select>
					</li>
					
					<li>
						<label for="mr_ModeAss" class="shipLabel">'.$this->l('Insurance').'<sup>*</sup></label>
						<select name="mr_ModeAss" id="mr_ModeAss" style="width:200px">
						<option value="0" selected>0 : '.$this->l('No insurance').'</option>
						<option value="1">1 : '.$this->l('Complementary Insurance Lv1').'</option>
						<option value="2">2 : '.$this->l('Complementary Insurance Lv2').'</option>
						<option value="3">3 : '.$this->l('Complementary Insurance Lv3').'</option>
						<option value="4">4 : '.$this->l('Complementary Insurance Lv4').'</option>
						<option value="5">5 : '.$this->l('Complementary Insurance Lv5').'</option>
						</select>
					</li>

					<li>	
					<label for="mr_Pays_list" class="shipLabel">'.$this->l('Delivery countries:').'<sup>*</sup><br /><br />
					<span style="font-size:10px; width:200px;float:left; color:forestgreen">'.
					$this->l('You can choose several countries by pressing Ctrl while selecting countries').'</span>
					</label>	
						<select name="mr_Pays_list[]" id="mr_Pays_list" multiple size="5">
							<option value="FR">'.$this->l('France').'</option>
							<option value="BE">'.$this->l('Belgium').'</option>
							<option value="LU">'.$this->l('Luxembourg').'</option>
							<option value="ES">'.$this->l('Spain').'</option>
						</select>
					</li>			
					<li class="PS_MRSubmit">
						<input type="submit" name="submitMethod" value="' . $this->l('Add a Shipping Method') . '" class="button" />
					</li>
				</ul>
			</fieldset>
		</form>';
		
		return $output;
	}
	
	public function shippingForm()
	{
		global $cookie;

		$query = Db::getInstance()->ExecuteS('
		SELECT m.*
		FROM `'._DB_PREFIX_.'mr_method` m
			JOIN `'._DB_PREFIX_.'carrier` c 
			ON (c.`id_carrier` = m.`id_carrier`)
		WHERE c.`deleted` = 0');
			
		$output = '
		<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
			<fieldset class="shippingList">
				<legend><img src="../modules/mondialrelay/images/logo.gif" />'.$this->l('Shipping Method\'s list').'</legend>
				<ol>';
		if (!sizeof($query))
			$output .= '<li>'.$this->l('No shipping methods created').'</li>';
		foreach ($query AS $Options)
		{
			$output .= '
					<li>
						<a href="' . 'index.php?tab=AdminModules&configure=mondialrelay&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'&delete_mr=' . $Options['id_mr_method'] . '"><img src="../img/admin/disabled.gif" alt="Delete" title="Delete" /></a>' . str_replace('_', ' ', $Options['mr_Name']) . ' (' . $Options['mr_ModeCol'] . '-' . $Options['mr_ModeLiv'] . ' - ' . $Options['mr_ModeAss'] . ' : '.$Options['mr_Pays_list'].') 
						<a href="index.php?tab=AdminCarriers&id_carrier=' . (int)($Options['id_carrier']) . '&updatecarrier&token='.Tools::getAdminToken('AdminCarriers'.(int)(Tab::getIdFromClassName('AdminCarriers')).(int)($cookie->id_employee)).'">'.$this->l('Config Shipping.').'</a>	
					</li>';
		}
		$output .= ' 

				</ol>
			</fieldset>
		</form><br />
		';

		return $output;	
	}
	
	/*
	** Form to allow personalization fields sent for MondialRelay
	*/
	public function personalizeFormFields()
	{
		$form = '';
		$warn = '';
		
		// Load the Default value from the configuration
		$addr1 = (Configuration::get('PS_MR_SHOP_NAME')) ? 
			Configuration::get('PS_MR_SHOP_NAME') : 
			Configuration::get('PS_SHOP_NAME');
		
		// Check if a request exist and if errors occured, use the post variable
		if (Tools::isSubmit('PS_MRSubmitFieldPersonalization') && count($this->_postErrors))
			$addr1 = Tools::getValue('Expe_ad1');
			

		if (!Configuration::get('PS_MR_SHOP_NAME'))
			$warn .= '<div class="warn">'.
				$this->l('Its seems you updated Mondialrelay without use the uninstall / install method, 
				you have to set up this part to make working the generating ticket process').
				'</div>';			
		// Form
		$form = '<form action="'.$_SERVER['REQUEST_URI'].'" method="post" class="form">';
		$form .= '
			<fieldset class="PS_MRFormStyle">
				<legend>
					<img src="../modules/mondialrelay/images/logo.gif" />'.$this->l('Fields personalization').
			'</legend>'.
			$warn.'
			<label for="PS_MR_SHOP_NAME">'.$this->l('Main Address').'</label>
			<div class="margin-form">
				<input type="text" name="Expe_ad1" value="'.$addr1.'" /><br />
				<p>'.$this->l('The key used by Mondialrelay is').' <b>Expe_ad1</b> '.$this->l('and has this default value').'
			 	: <b>'.Configuration::get('PS_SHOP_NAME').'</b></p>
			</div>
		
		<div class="margin-form">
			<input type="submit" name="PS_MRSubmitFieldPersonalization"  value="' . $this->l('Save') . '" class="button" />
		</div>
		</form><br  />';
		return $form;
	}
	
	
	public function settingsstateorderForm()
	{
		global $cookie;
		
		$this->orderState = Configuration::get('MONDIAL_RELAY_ORDER_STATE');
	    $output = '';
		$output .= '<form action="'.$_SERVER['REQUEST_URI'].'" method="post" class="form">';
		$output .= '<fieldset><legend><img src="../modules/mondialrelay/images/logo.gif" />'.$this->l('Settings').'</legend>';
		$output .= '<label for="id_order_state">' . $this->l('Order state') . '</label>';
		$output .= '<div class="margin-form">';
		$output .= '<select id="id_order_state" name="id_order_state" style="width:250px">';

		$order_states = OrderState::getOrderStates((int)($cookie->id_lang));
		foreach ( $order_states as $order_state)
		{
			$output  .= '<option value="' . $order_state['id_order_state'] . '" style="background-color:' . $order_state['color'] . ';"';
			if ($this->orderState == $order_state['id_order_state'] ) $output  .= ' selected="selected"';
			$output  .= '>' . $order_state['name'] . '</option>';
		}
		$output .= '</select>';
		$output .= '<p>' . $this->l('Choose the order state for labels. You can manage the labels on').' ';
		$output .= '<a href="index.php?tab=AdminMondialRelay&token='.Tools::getAdminToken('AdminMondialRelay'.(int)(Tab::getIdFromClassName('AdminMondialRelay')).(int)($cookie->id_employee)).'" class="green">'.
		$this->l('the Mondial Relay administration page').'</a></p>';
		$output .= '</div>
		<div class="clear"></div>
		<label>'.$this->l('Google Map').' </label>
		<div class="margin-form">
			<input type="radio" name="mr_google_key" id="mr_google_key_on" value="1" '.(Configuration::get('MR_GOOGLE_MAP') ? 'checked="checked" ' : '').'/>
			<label class="t" for="mr_google_key_on"><img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Yes').'" /></label>
			<input type="radio" name="mr_google_key" id="mr_google_key_off" value="0" '.(!Configuration::get('MR_GOOGLE_MAP') ? 'checked="checked" ' : '').'/>
			<label class="t" for="mr_google_key_off"><img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('No').'" /></label>
			<p>'.$this->l('Displaying a google map on your Mondial Relay carrier may make carrier page loading slower.').'</p>
		</div>';
		$output .= '<div class="margin-form"><input type="submit" name="submit_order_state"  value="' . $this->l('Save') . '" class="button" /></div>';
		$output .= '</fieldset></form><br>';
		
		return $output;
    }

	
	public function settingsForm()
	{
		$output = '
			<form action="' . $_SERVER['REQUEST_URI'] . '" method="post" >
				<fieldset>
					<legend><img src="../modules/mondialrelay/images/logo.gif" />'.$this->l('Mondial Relay Account Settings').'</legend>
					<ul>
						<li class="PS_MRRequireFields">
							<sup>* ' . $this->l('Required') . '</sup>
						</li>
						<li>
							<label for="mr_Enseigne_WebService" class="mrLabel">' . $this->l('Webservice Enseigne:') . '<sup>*</sup></label>
							<input id="mr_Enseigne_WebService" class="mrInput" type="text" name="mr_Enseigne_WebService" value="' .
							(Tools::getValue('mr_Enseigne_WebService') ? Tools::getValue('mr_Enseigne_WebService') : Configuration::get('MR_ENSEIGNE_WEBSERVICE')) . '"/>
						</li>
						<li>
							<label for="mr_code_marque" class="mrLabel">' . $this->l('Code marque:') . '<sup>*</sup></label>
							<input id="mr_code_marque" class="mrInput" type="text" name="mr_code_marque" value="' .
							(Tools::getValue('mr_code_marque') ? Tools::getValue('mr_code_marque') : Configuration::get('MR_CODE_MARQUE')) . '"/>
						</li>
						<li>
							<label for="mr_Key_WebService" class="mrLabel">' . $this->l('Webservice Key:') . '<sup>*</sup></label>
							<input id="mr_Key_WebService" class="mrInput" type="text" name="mr_Key_WebService" value="' .
							(Tools::getValue('mr_Key_WebService') ? Tools::getValue('mr_Key_WebService') : Configuration::get('MR_KEY_WEBSERVICE')) . '"/>
						</li>
						<li>
							<label for="mr_Langage" class="mrLabel">' . $this->l('Etiquette\'s Language:') . '<sup>*</sup></label>
							<select id="mr_Langage" name="mr_Langage" value="'.
							(Tools::getValue('mr_Langage') ? Tools::getValue('mr_Langage') : Configuration::get('MR_LANGUAGE')).'" >';
		$languages = Language::getLanguages();
		foreach ($languages as $language)
			$output .= '<option value="'.strtoupper($language['iso_code']).'" '.(strtoupper($language['iso_code']) == Configuration::get('MR_LANGUAGE') ? 'selected="selected"' : '').'>'.$language['name'].'</option>';
							
				$output .= '</select>
						</li>
						<li>
							<label for="mr_weight_coef" class="mrLabel">' . $this->l('Weight Coefficient:') . '<sup>*</sup></label>
							<input class="mrInput" type="text" name="mr_weight_coef" value="' . 
							(Tools::getValue('mr_weight_coef') ? Tools::getValue('mr_weight_coef') : Configuration::get('MR_WEIGHT_COEF')) . '"  style="width:45px;"/>
							<span class="indication">(' . $this->l('grammes = 1 ') . Configuration::get('PS_WEIGHT_UNIT').')</span>
						</li>						
						<li class="PS_MRSubmit">
							<input type="submit" name="submitMR" value="' . $this->l('Update Settings') . '" class="button" />
						</li>
					</ul>
				</fieldset>
			</form>';

		return $output;
	}

	public function displayInfoByCart($id_cart)
	{
		$html = '<p>';
		$simpleresul = Db::getInstance()->ExecuteS('
			SELECT * FROM ' . _DB_PREFIX_ . 'mr_selected 
			WHERE id_cart='.(int)($id_cart));
	
		if (trim($simpleresul[0]['exp_number']) != '0') 
			$html .= $this->l('Nb expedition:').$simpleresul[0]['exp_number']."<br>";
		if (trim($simpleresul[0]['url_etiquette']) != '0') 
			$html .= "<a href='".$simpleresul[0]['url_etiquette']."' target='etiquette".$simpleresul[0]['url_etiquette']."'>".$this->l('Label URL')."</a><br>";
		if (trim($simpleresul[0]['url_suivi']) != '0')
			$html .= "<a href='".$simpleresul[0]['url_suivi']."' target='suivi".$simpleresul[0]['exp_number']."'>".$this->l('Follow-up URL')."</a><br>";
		if (trim($simpleresul[0]['MR_Selected_Num']) != '')
			$html .= $this->l('Nb Point Relay :').$simpleresul[0]['MR_Selected_Num']."<br>";
		if (trim($simpleresul[0]['MR_Selected_LgAdr1']) != '')
			$html .= $simpleresul[0]['MR_Selected_LgAdr1']."<br>";
		if (trim($simpleresul[0]['MR_Selected_LgAdr2']) != '')
			$html .= $simpleresul[0]['MR_Selected_LgAdr2']."<br>";
		if (trim($simpleresul[0]['MR_Selected_LgAdr3']) != '')
			$html .= $simpleresul[0]['MR_Selected_LgAdr3']."<br>"; 
		if (trim($simpleresul[0]['MR_Selected_LgAdr4']) != '')
			$html .= $simpleresul[0]['MR_Selected_LgAdr4']."<br>"; 
		if (trim($simpleresul[0]['MR_Selected_CP']) != '')
			$html .= $simpleresul[0]['MR_Selected_CP']." ";
		if (trim($simpleresul[0]['MR_Selected_Ville']) != '')
			$html .= $simpleresul[0]['MR_Selected_Ville']."<br>";
		if (trim($simpleresul[0]['MR_Selected_Pays']) != '')
			$html .= $simpleresul[0]['MR_Selected_Pays']."<br>";
		$html .= '</p>';
		return $html;
	}

	public function get_followup($shipping_number)
	{
	  $query = 'SELECT url_suivi 
	  	FROM '._DB_PREFIX_ .'mr_selected 
	  	WHERE id_mr_selected=\''.(int)($shipping_number).'\';';
	  	  
		$settings = Db::getInstance()->ExecuteS($query);
		
		return $settings[0]['url_suivi'];
	}

	public function set_carrier($key, $value, $id_carrier)
	{
		if ($key == 'name')
			$key = 'mr_Name';
			
		return Db::getInstance()->Execute('
			UPDATE ' . _DB_PREFIX_ . 'mr_method 
			SET '.pSQL($key).'="'.pSQL($value).'" 
			WHERE id_carrier=\''.(int)($id_carrier).'\' ; ');
	}
	
 	// Add for 1.3 compatibility and avoid duplicate code	
	static public function jsonEncode($result)
	{
		return (method_exists('Tools', 'jsonEncode')) ? 
			Tools::jsonEncode($result) : json_encode($result);
	}
	
	static public function ordersSQLQuery1_4($id_order_state)
	{
		return 'SELECT  o.`id_address_delivery` as id_address_delivery, 
							o.`id_order` as id_order, 
							o.`id_customer` as id_customer,
							o.`id_cart` as id_cart, 
							mrs.`id_mr_selected` as id_mr_selected,
							CONCAT(c.`firstname`, \' \', c.`lastname`) AS `customer`,
							o.`total_paid_real` as total, o.`total_shipping` as shipping,
							o.`date_add` as date, o.`id_currency` as id_currency, o.`id_lang` as id_lang,
							mrs.`MR_poids` as weight, mr.`mr_Name` as mr_Name, mrs.`MR_Selected_Num` as MR_Selected_Num,
							mrs.`MR_Selected_Pays` as MR_Selected_Pays, mrs.`exp_number` as exp_number,
							mr.`mr_ModeCol` as mr_ModeCol, mr.`mr_ModeLiv` as mr_ModeLiv, mr.`mr_ModeAss` as mr_ModeAss 
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'carrier` ca 
			ON (ca.`id_carrier` = o.`id_carrier` 
			AND ca.`external_module_name` = "mondialrelay")
			LEFT JOIN `'._DB_PREFIX_.'mr_selected` mrs 
			ON (mrs.`id_cart` = o.`id_cart`)
			LEFT JOIN `'._DB_PREFIX_.'mr_method` mr 
			ON (mr.`id_carrier` = ca.`id_carrier`)
			LEFT JOIN `'._DB_PREFIX_.'customer` c 
			ON (c.`id_customer` = o.`id_customer`)
			WHERE (
				SELECT moh.`id_order_state` 
				FROM `'._DB_PREFIX_.'order_history` moh 
				WHERE moh.`id_order` = o.`id_order` 
				ORDER BY moh.`date_add` DESC LIMIT 1) = '.(int)($id_order_state).' 
			AND ca.`external_module_name` = "mondialrelay"';
	}
		
	static public function ordersSQLQuery1_3($id_order_state)
	{
		return '
				SELECT  o.`id_address_delivery` as id_address_delivery, 
							o.`id_order` as id_order, 
							o.`id_customer` as id_customer,
							o.`id_cart` as id_cart, 
							mrs.`id_mr_selected` as id_mr_selected,
							CONCAT(c.`firstname`, \' \', c.`lastname`) AS `customer`,
							o.`total_paid_real` as total, o.`total_shipping` as shipping,
							o.`date_add` as date, o.`id_currency` as id_currency, o.`id_lang` as id_lang,
							mrs.`MR_poids` as weight, mr.`mr_Name` as mr_Name, mrs.`MR_Selected_Num` as MR_Selected_Num,
							mrs.`MR_Selected_Pays` as MR_Selected_Pays, mrs.`exp_number` as exp_number,
							mr.`mr_ModeCol` as mr_ModeCol, mr.`mr_ModeLiv` as mr_ModeLiv, mr.`mr_ModeAss` as mr_ModeAss 
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'carrier` ca 
			ON (ca.`id_carrier` = o.`id_carrier`)
			AND ca.`name` = "mondialrelay"
			LEFT JOIN `'._DB_PREFIX_.'mr_selected` mrs 
			ON (mrs.`id_cart` = o.`id_cart`)
			LEFT JOIN `'._DB_PREFIX_.'mr_method` mr 
			ON (mr.`id_carrier` = ca.`id_carrier`)
			LEFT JOIN `'._DB_PREFIX_.'customer` c 
			ON (c.`id_customer` = o.`id_customer`)
			WHERE (
				SELECT moh.`id_order_state` 
				FROM `'._DB_PREFIX_.'order_history` moh 
				WHERE moh.`id_order` = o.`id_order` 
				ORDER BY moh.`date_add` DESC LIMIT 1) = '.(int)($id_order_state).'
			AND ca.`name` = "mondialrelay"';
	}
	
	static public function getBaseOrdersSQLQuery($id_order_state)
	{
		if (_PS_VERSION_ >= '1.4')
			return self::ordersSQLQuery1_4($id_order_state);
		else
			return self::ordersSQLQuery1_3($id_order_state);
	}
	
	static public function getOrders($orderIdList = array())
	{
		$id_order_state = Configuration::get('MONDIAL_RELAY_ORDER_STATE');
		$sql = self::getBaseOrdersSQLQuery($id_order_state);
		
		if (count($orderIdList))
		{
			$sql .= ' AND o.id_order IN (';
			foreach ($orderIdList as $id_order)
				$sql .= (int)$id_order.', ';
			$sql = rtrim($sql, ', ').')';
		}
		$sql .= '
			GROUP BY o.`id_order`
			ORDER BY o.`date_add` ASC';
		return Db::getInstance()->ExecuteS($sql);
	}
	
	public function getErrorCodeDetail($code)
	{
		global $statCode;
		
		if (isset($statCode[$code]))
			return $statCode[$code];
		return $this->l('This error isn\'t referred : ') . $code;
	}
}
