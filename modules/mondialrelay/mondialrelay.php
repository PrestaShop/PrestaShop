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

if (!defined('_PS_VERSION_'))
	exit;

require_once(_PS_MODULE_DIR_.'mondialrelay/classes/MondialRelayClass.php');
require_once(_PS_MODULE_DIR_.'mondialrelay/classes/MRTools.php');

class MondialRelay extends Module
{
	const INSTALL_SQL_FILE = 'mrInstall.sql';

	private $_postErrors;

	public static $modulePath = '';
	public static $moduleURL = '';
	static public $MRFrontToken = '';
	static public $MRBackToken = '';

	// Added for 1.3 compatibility
	const ONLY_PRODUCTS = 1;
	const ONLY_DISCOUNTS = 2;
	const BOTH = 3;
	const BOTH_WITHOUT_SHIPPING = 4;
	const ONLY_SHIPPING = 5;
	const ONLY_WRAPPING = 6;
	const ONLY_PRODUCTS_WITHOUT_SHIPPING = 7;

	// SQL FILTER ORDER
	const NO_FILTER = 0;
	const WITHOUT_HOME_DELIVERY = 1;

	public function __construct()
	{
		$this->name		= 'mondialrelay';
		$this->tab		= 'shipping_logistics';
		$this->version	= '1.7.9';

		parent::__construct();

		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('Mondial Relay');
		$this->description = $this->l('Deliver in Relay points');

		self::initModuleAccess();

		// Call everytime to prevent the changement of the module by a recent one
		$this->_updateProcess();
	}

	public function install()
	{
		$name = "shipping";
		$title = "Mondial Relay API";

		if (!parent::install())
			return false;

		Db::getInstance()->executeS(
			'SELECT `name`
			FROM `' . _DB_PREFIX_ . 'hook`
			WHERE `name` = \''.$name.'\'
			AND `title` = \''.$title.'\'');

		if (!Db::getInstance()->NumRows())
			Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'hook
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
				Db::getInstance()->execute(trim($query));

		$result = Db::getInstance()->getRow('
			SELECT id_tab
			FROM `' . _DB_PREFIX_ . 'tab`
			WHERE class_name="AdminMondialRelay"');

		if (!$result)
		{
			// AdminOrders id_tab
			$id_parent = 3;

			/*tab install */
			$result = Db::getInstance()->getRow('
				SELECT position
				FROM `' . _DB_PREFIX_ . 'tab`
				WHERE `id_parent` = '.(int)$id_parent.'
				ORDER BY `'. _DB_PREFIX_ .'tab`.`position` DESC');

			$pos = (isset($result['position'])) ? $result['position'] + 1 : 0;

			Db::getInstance()->execute('
				INSERT INTO ' . _DB_PREFIX_ . 'tab
				(id_parent, class_name, position, module)
				VALUES('.(int)$id_parent.', "AdminMondialRelay",  "'.(int)($pos).'", "mondialrelay")');

			$id_tab = Db::getInstance()->Insert_ID();

			$languages = Language::getLanguages();
			foreach ($languages as $language)
				Db::getInstance()->execute('
				INSERT INTO ' . _DB_PREFIX_ . 'tab_lang
				(id_lang, id_tab, name)
				VALUES("'.(int)($language['id_lang']).'", "'.(int)($id_tab).'", "Mondial Relay")');

			$profiles = Profile::getProfiles(Configuration::get('PS_LANG_DEFAULT'));
			foreach ($profiles as $profile)
				Db::getInstance()->execute('
				INSERT INTO ' . _DB_PREFIX_ . 'access
				(`id_profile`,`id_tab`,`view`,`add`,`edit`,`delete`)
				VALUES('.$profile['id_profile'].', '.(int)($id_tab).', 1, 1, 1, 1)');

			@copy(_PS_MODULE_DIR_.'mondialrelay/AdminMondialRelay.gif', _PS_IMG_DIR_.'/AdminMondialRelay.gif');
		}

		// If module isn't installed, set default value
		if (!Configuration::get('MONDIAL_RELAY'))
		{
			Configuration::updateValue('MONDIAL_RELAY', $this->version);
			Configuration::updateValue('MONDIAL_RELAY_ORDER_STATE', 3);
			Configuration::updateValue('MONDIAL_RELAY_SECURE_KEY', md5(time().rand(0,10)));
			Configuration::updateValue('MR_GOOGLE_MAP', '1');
			Configuration::updateValue('MR_ENSEIGNE_WEBSERVICE', '');
			Configuration::updateValue('MR_CODE_MARQUE', '');
			Configuration::updateValue('MR_KEY_WEBSERVICE', '');
			Configuration::updateValue('MR_LANGUAGE', '');
			Configuration::updateValue('MR_WEIGHT_COEF', '');
		}
		else
		{
			// Reactive transport if database wasn't remove at the last uninstall
			Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'carrier` c, `'._DB_PREFIX_.'mr_method` m
					SET `deleted` = 0
					WHERE c.id_carrier = m.id_carrier');
			if (Configuration::get('MONDIAL_RELAY') < $this->version)
				;// TODO : ADD upgrade process depending of the last and new version
		}
		return true;
	}

	/*
	** Return the token depend of the type
	*/
	static public function getToken($type = 'front')
	{
		return ($type == 'front') ? self::$MRFrontToken : (($type == 'back') ?
			self::$MRBackToken : NULL);
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
				!$this->registerHook('BackOfficeHeader') ||
				!$this->registerHook('paymentTop')))
			return false;

		if (_PS_VERSION_ >= '1.4' &&
				(!$this->registerHook('processCarrier') ||
				!$this->registerHook('orderDetail') ||
				!$this->registerHook('orderDetailDisplayed') ||
				!$this->registerHook('paymentTop')))
			return false;
		return true;
	}

	public function uninstallCommonData()
	{
		// Tab uninstall
		$result = Db::getInstance()->getRow('
			SELECT id_tab
			FROM `' . _DB_PREFIX_ . 'tab`
			WHERE class_name="AdminMondialRelay"');

		if ($result)
		{
			$id_tab = $result['id_tab'];
			if (isset($id_tab) && !empty($id_tab))
			{
				Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'tab WHERE id_tab = '.(int)($id_tab));
				Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'tab_lang WHERE id_tab = '.(int)($id_tab));
				Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'access WHERE id_tab = '.(int)($id_tab));
			}
		}

		if (_PS_VERSION_ >= '1.4' &&
				!Db::getInstance()->execute('
					UPDATE  '._DB_PREFIX_ .'carrier
					SET `active` = 0, `deleted` = 1
					WHERE `external_module_name` = "mondialrelay"'))
			return false;
		else if (!Db::getInstance()->execute('
					UPDATE  '._DB_PREFIX_ .'carrier
					SET `active` = 0, `deleted` = 1
					WHERE `name` = "mondialrelay"'))
			return false;
		return true;
	}

	public function uninstall()
	{
		if (!parent::uninstall())
			return false;

		// Uninstall data that doesn't need to be keep
		if (!$this->uninstallCommonData())
			return false;

		if (Tools::getValue('keepDatabase'))
		{
			// Retro Compatibility for older Version than 1.7
			if (Configuration::get('MONDIAL_RELAY_1_4'))
			{
				Configuration::updateValue('MONDIAL_RELAY', '1.6');
				Configuration::deleteByName('MONDIAL_RELAY_1_4');
				Configuration::deleteByName('MONDIAL_RELAY_INSTALL_UPDATE_1');
			}
			return true;
		}

		// MondialRelay Configuration
		if (!Configuration::deleteByName('MONDIAL_RELAY') ||
				!Configuration::deleteByName('MONDIAL_RELAY_INSTALL_UPDATE') ||
				!Configuration::deleteByName('MONDIAL_RELAY_SECURE_KEY') ||
				!Configuration::deleteByName('MONDIAL_RELAY_ORDER_STATE') ||
				!Configuration::deleteByName('MR_GOOGLE_MAP') ||
				!Configuration::deleteByName('MR_ENSEIGNE_WEBSERVICE') ||
				!Configuration::deleteByName('MR_CODE_MARQUE') ||
				!Configuration::deleteByName('MR_KEY_WEBSERVICE') ||
				!Configuration::deleteByName('MR_WEIGHT_COEF'))
			return false;

		// Drop databases
		if (!Db::getInstance()->execute('
					DROP TABLE
					'._DB_PREFIX_ .'mr_historique,
					'._DB_PREFIX_ .'mr_method,
					'._DB_PREFIX_ .'mr_selected'))
			return false;
		else if (!Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'carrier SET `active` = 0, `deleted` = 1 WHERE `name` = "mondialrelay"'))
			return false;

		return true;
	}

	/*
	** UpdateProcess if merchant update the module without a
	** normal installation
	*/
	private function _updateProcess()
	{
		if (Module::isInstalled('mondialrelay') &&
			(($installedVersion = Configuration::get('MONDIAL_RELAY')) ||
			$installedVersion = Configuration::get('MONDIAL_RELAY_1_4'))
			&& $installedVersion < $this->version)
		{
			if ($installedVersion < '1.4')
		$this->_update_v1_4();
			if ($installedVersion < '1.4.2')
		$this->_update_v1_4_2();
	}

		// Process update done just try to update the new configuration value
		if (Configuration::get('MONDIAL_RELAY_1_4'))
		{
			Configuration::updateValue('MONDIAL_RELAY', $this->version);
			Configuration::deleteByName('MONDIAL_RELAY_1_4');
		}
	}

	/*
	** Use if the mechant was using Prestashop 1.3 and
	** now use 1.4 or more recent
	*/
	private function _update_v1_4()
	{
		Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'carrier`
			SET
				`shipping_external` = 0,
				`need_range` = 1,
				`external_module_name` =
				"mondialrelay",
				`shipping_method` = 1
			WHERE `id_carrier`
			IN (SELECT `id_carrier`
					FROM `'._DB_PREFIX_.'mr_method`)');
		}

	/*
	** Add new Hook for the last recent version >= 1.4.2
	*/
	private function _update_v1_4_2()
	{
		if (!$this->isRegisteredInHook('newOrder'))
			$this->registerHook('newOrder');
		if (!$this->isRegisteredInHook('BackOfficeHeader'))
			$this->registerHook('BackOfficeHeader');
	}

	/*
	** Get the content to ask for a backup of the database
	*/
	private function askForBackup($href)
	{
		return 'targetButton = \''.$href.'\';
			PS_MRGetUninstallDetail();';
	}

	/*
	** OnClick for input fields under the module list fields action
	*/
	public function onclickOption($type, $href = false)
	{
		$content = '';

		switch($type)
		{
			case 'desactive':
				break;
			case 'reset':
				break;
			case 'delete':
				break;
			case 'uninstall':
				$content = $this->askForBackup($href);
				break;
			default:
		}
		return $content;
	}

	/*
	** Init the access directory module for URL and file system
	** Allow a compatibility for Presta < 1.4
	*/
	public static function initModuleAccess()
	{
		self::$modulePath =	_PS_MODULE_DIR_. 'mondialrelay/';
		self::$MRFrontToken = sha1('mr'._COOKIE_KEY_.'Front');
		self::$MRBackToken = sha1('mr'._COOKIE_KEY_.'Back');

		$protocol = (Configuration::get('PS_SSL_ENABLED') || (!empty($_SERVER['HTTPS'])
			&& strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';

		$endURL = __PS_BASE_URI__.'modules/mondialrelay/';

		if (method_exists('Tools', 'getShopDomainSsl'))
			self::$moduleURL = $protocol.Tools::getShopDomainSsl().$endURL;
		else
			self::$moduleURL = $protocol.$_SERVER['HTTP_HOST'].$endURL;
	}

	/*
	** Override a jQuery version included by another one us.
	** Allow a compatibility for Presta < 1.4
	*/
	public static function getJqueryCompatibility($overloadCurrent = false)
	{
		// Store the last inclusion into a variable and include the new one
		if ($overloadCurrent)
			return '
				<script type="text/javascript">
					currentJquery = jQuery.noConflict(true);
				</script>
			<script type="text/javascript" src="'.self::$moduleURL.'js/jquery-1.6.4.min.js"></script>';

		return '
			<script type="text/javascript" src="'.self::$moduleURL.'js/jquery-1.6.4.min.js"></script>
			<script type="text/javascript">
				MRjQuery = jQuery.noConflict(true);
			</script>';
	}

	public function hookNewOrder($params)
	{
		DB::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'mr_selected`
			SET `id_order` = '.(int)$params['order']->id.'
			WHERE `id_cart` = '.(int)$params['cart']->id);
	}

	public function hookBackOfficeHeader()
	{
		$cssFilePath = $this->_path.'style.css';
		$jsFilePath= $this->_path.'mondialrelay.js';

		$ret = '<script type="text/javascript" src="'.$jsFilePath.'"></script>';
		if (Tools::getValue('tab') == 'AdminMondialRelay')
			$ret .= self::getJqueryCompatibility(true);

		$ret .= '
				<link type="text/css" rel="stylesheet" href="'.$cssFilePath.'" />
				<script type="text/javascript">
					var _PS_MR_MODULE_DIR_ = "'.self::$moduleURL.'";
					var mrtoken = "'.self::$MRBackToken.'";
				</script>';
		return $ret;
		return $ret;
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
		/*
		else if (Tools::isSubmit('PS_MRSubmitFieldPersonalization'))
		{
			$addr1 = Tools::getValue('Expe_ad1');
			if (!preg_match('#^[0-9A-Z_\-\'., /]{2,32}$#', strtoupper($addr1), $match))
				$this->_postErrors[] = $this->l('The Main address submited hasn\'t a good format');
		}*/
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
		/*elseif (Tools::getValue('PS_MRSubmitFieldPersonalization'))
			$this->updateFieldsPersonalization();*/
		elseif (isset($_POST['submitMethod']) AND $_POST['submitMethod'])
			self::mrUpdate('addShipping', $setArray, $keyArray);
		else if (isset($_POST['submit_order_state']) AND $_POST['submit_order_state'])
		{
			Configuration::updateValue('MONDIAL_RELAY_ORDER_STATE', Tools::getValue('id_order_state'));
			Configuration::updateValue('MR_GOOGLE_MAP', Tools::getValue('mr_google_key'));
			$this->_html .= '<div class="conf confirm"><img src="'._PS_ADMIN_IMG_.'/ok.gif" alt="" /> '.$this->l('Settings updated').'</div>';
		}
	}

	public function hookOrderDetail($params)
	{
		$carrier = $params['carrier'];
		$order = $params['order'];

		if ($carrier->is_module AND $order->shipping_number)
	 	{
			$module = $carrier->external_module_name;
			include_once(_PS_MODULE_DIR_.$module.'/'.$module.'.php');
			$module_carrier = new $module();
			$this->context->smarty->assign('followup', $module_carrier->get_followup($order->shipping_number));
		}
		else if ($carrier->url AND $order->shipping_number)
			$this->context->smarty->assign('followup', str_replace('@', $order->shipping_number, $carrier->url));
	}

	public function hookOrderDetailDisplayed($params)
	{
		$res = Db::getInstance()->getRow('
		SELECT s.`MR_Selected_LgAdr1`, s.`MR_Selected_LgAdr2`, s.`MR_Selected_LgAdr3`, s.`MR_Selected_LgAdr4`, s.`MR_Selected_CP`, s.`MR_Selected_Ville`, s.`MR_Selected_Pays`, s.`MR_Selected_Num`, s.`url_suivi`
		FROM `'._DB_PREFIX_.'mr_selected` s
		WHERE s.`id_cart` = '.$params['order']->id_cart);
		if ((!$res) OR ($res['MR_Selected_Num'] == 'LD1') OR ($res['MR_Selected_Num'] == 'LDS'))
			return '';
		$this->context->smarty->assign('mr_addr', $res['MR_Selected_LgAdr1'].($res['MR_Selected_LgAdr1'] ? ' - ' : '').$res['MR_Selected_LgAdr2'].($res['MR_Selected_LgAdr2'] ? ' - ' : '').$res['MR_Selected_LgAdr3'].($res['MR_Selected_LgAdr3'] ? ' - ' : '').$res['MR_Selected_LgAdr4'].($res['MR_Selected_LgAdr4'] ? ' - ' : '').$res['MR_Selected_CP'].' '.$res['MR_Selected_Ville'].' - '.$res['MR_Selected_Pays']);
		$smarty->assign('mr_url', $res['url_suivi']);
		return $this->display(__FILE__, 'orderDetail.tpl');
	}

	/*
	** No need anymore
	*/
	public function hookProcessCarrier($params)
	{
	}

	/*
	** Update the carrier id to use the new one if changed
	*/
	public function hookupdateCarrier($params)
	{
		if ((int)($params['id_carrier']) != (int)($params['carrier']->id))
    {
				Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'mr_method`
					(mr_Name, mr_Pays_list, mr_ModeCol, mr_ModeLiv, mr_ModeAss, id_carrier)
				(
					SELECT
						mr_Name,
						mr_Pays_list,
						mr_ModeCol,
						mr_ModeLiv,
						mr_ModeAss,
						"'.(int)$params['carrier']->id.'"
					FROM `'._DB_PREFIX_.'mr_method`
					WHERE id_carrier ='.(int)$params['id_carrier'].')');
		}
	}

	/*
	** Get a carrier list liable to the module
	*/
	public function _getCarriers()
	{
		// Query don't use the external_module_name to keep the
		// 1.3 compatibility
		$carriers = Db::getInstance()->executeS('
			SELECT
				c.id_carrier,
				c.range_behavior,
				m.id_mr_method,
				m.mr_ModeLiv,
				cl.delay
			FROM `'._DB_PREFIX_.'mr_method` m
			LEFT JOIN `'._DB_PREFIX_.'carrier` c
			ON c.`id_carrier` = m.`id_carrier`
			LEFT JOIN `'._DB_PREFIX_.'carrier_lang` cl
			ON c.`id_carrier` = cl.`id_carrier`
			WHERE  c.`deleted` = 0
			AND c.active = 1');

		if (!is_array($carriers))
			$carriers = array();
		return $carriers;
	}

	public function hookextraCarrier($params)
	{
		global $nbcarriers;

		if (Configuration::get('MR_ENSEIGNE_WEBSERVICE') == '' ||
			Configuration::get('MR_CODE_MARQUE') == '' ||
			Configuration::get('MR_KEY_WEBSERVICE') == '' ||
			Configuration::get('MR_LANGUAGE') == '')
			return '';

		$address = new Address($this->context->cart->id_address_delivery);
		$id_zone = Address::getZoneById((int)($address->id));
		$carriersList = self::_getCarriers();

		// Check if the defined carrier are ok
		foreach ($carriersList as $k => $row)
		{
			$carrier = new Carrier((int)($row['id_carrier']));
			if ((Configuration::get('PS_SHIPPING_METHOD') AND $carrier->getMaxDeliveryPriceByWeight($id_zone) === false) ||
				(!Configuration::get('PS_SHIPPING_METHOD') AND $carrier->getMaxDeliveryPriceByPrice($id_zone) === false))
				unset($carriersList[$k]);
			else if ($row['range_behavior'])
			{
				// Get id zone
				$id_zone = (isset($this->context->cart->id_address_delivery) AND $this->context->cart->id_address_delivery) ?
					Address::getZoneById((int)$this->context->cart->id_address_delivery) :
					(int)$this->context->country->id_zone;
				if ((Configuration::get('PS_SHIPPING_METHOD') AND (!Carrier::checkDeliveryPriceByWeight($row['id_carrier'], $this->context->cart->getTotalWeight(), $id_zone))) OR
					(!Configuration::get('PS_SHIPPING_METHOD') AND (!Carrier::checkDeliveryPriceByPrice($row['id_carrier'], $this->context->cart->getOrderTotal(true, self::BOTH_WITHOUT_SHIPPING), $id_zone, $this->context->cart->id_currency))))
						unset($carriersList[$k]);
			}
	 	}

	 	$preSelectedRelay = $this->getRelayPointSelected($params['cart']->id);
		$this->context->smarty->assign(array(
			'one_page_checkout' => (Configuration::get('PS_ORDER_PROCESS_TYPE') ? Configuration::get('PS_ORDER_PROCESS_TYPE') : 0),
			'new_base_dir' => self::$moduleURL,
			'MRToken' => self::$MRFrontToken,
			'carriersextra' => $carriersList,
			'preSelectedRelay' => isset($preSelectedRelay['MR_selected_num']) ? $preSelectedRelay['MR_selected_num'] : '',
			'jQueryOverload' => self::getJqueryCompatibility(false)
		));

		return $this->display(__FILE__, 'mondialrelay.tpl');
	}

	public function getContent()
	{
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

		<div class="MR_warn">
			<a style="color:#383838;text-decoration:underline" href="index.php?tab=AdminPerformance&token='.Tools::getAdminToken('AdminPerformance'.(int)(Tab::getIdFromClassName('AdminPerformance')).(int)($cookie->id_employee)).'">
					'.$this->l('Try to turn off the cache and put the force compilation to on').'
					</a> '.$this->l('if you have any problems with the module after an update').'.
		</div>
		<div class="MR_hint">
			'.$this->l('Have a look to the following HOW-TO to help you to configure the Mondial Relay module').'
			<b><a href="'.self::$moduleURL.'/docs/install.pdf"><img width="20" src="'.self::$moduleURL.'images/pdf_icon.jpg" /></a></b>
		</div>
		<br />
		<fieldset>
			<legend>
				<img src="../modules/mondialrelay/images/logo.gif" />'.$this->l('To create a Mondial Relay carrier').
			'</legend>
		- '.$this->l('Enter and save your Mondial Relay account settings').'<br />
				- '.$this->l('Create a Carrier using the form "add a carrier" below').'<br />
				- '.$this->l('Define a price for your carrier on').'
					<a href="index.php?tab=AdminCarriers&token='.Tools::getAdminToken('AdminCarriers'.(int)(Tab::getIdFromClassName('AdminCarriers')).
					(int)$this->context->employee->id).'" class="green">'.$this->l('The Carrier page').'</a><br />
				- '.$this->l('To generate labels, you must have a valid and registered address of your store on your').
					' <a href="index.php?tab=AdminContact&token='.Tools::getAdminToken('AdminContact'.(int)(Tab::getIdFromClassName('AdminContact')).
					(int)$this->context->employee->id).'" class="green">'.$this->l('contact page').'</a><br />
		</fieldset>
		<br class="clear" />
		<div class="PS_MRFormType">'.
			$this->settingsForm().
		'</div>
		<div class="PS_MRFormType">'.
			$this->settingsstateorderForm().
		'</div>
		<div class="PS_MRFormType">'.
			$this->advancedSettings().
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
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_ .'carrier` SET `active` = 0, `deleted` = 1 WHERE `id_carrier` = "'.(int)($id).'"');
		$this->_html .= '<div class="conf confirm"><img src="'._PS_ADMIN_IMG_.'/ok.gif" /> '.$this->l('Delete successful').'</div>';
	}

	public function mrUpdate($type, $array, $keyArray)
	{
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
				Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'carrier SET active = "'.(int)($value).'" WHERE `id_carrier` = "'.(int)($id).'"');
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

			Db::getInstance()->execute($query);

			$mainInsert = Db::getInstance()->Insert_ID();
			$default = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "configuration WHERE name = 'PS_CARRIER_DEFAULT'");
			$check   = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "carrier");
			$checkD = array();

			foreach($check AS $Key)
			{
				foreach($Key AS $key => $value)
					if($key == "id_carrier")
						$checkD[] = $value;
			}

			// Added for 1.3 compatibility to match with the right key
			if (_PS_VERSION_ >= '1.4')
				Db::getInstance()->execute('
					INSERT INTO `' . _DB_PREFIX_ . 'carrier`
					(`id_tax_rules_group`, `url`, `name`, `active`, `is_module`, `range_behavior`, `shipping_external`, `need_range`, `external_module_name`, `shipping_method`)
					VALUES("0", NULL, "'.pSQL($array[1]).'", "1", "1", "1", "0", "1", "mondialrelay", "1")');
			else
				Db::getInstance()->execute('
				INSERT INTO `' . _DB_PREFIX_ . 'carrier`
				(`url`, `name`, `active`, `is_module`, `range_behavior`)
				VALUES(NULL, "'.pSQL('mondialrelay').'", "1", "0", "1")');

			$get   = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'carrier` WHERE `id_carrier` = "' . Db::getInstance()->Insert_ID() . '"');
			Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'mr_method` SET `id_carrier` = "' . (int)($get['id_carrier']) . '" WHERE `id_mr_method` = "' . pSQL($mainInsert) . '"');
			$weight_coef = Configuration::get('MR_WEIGHT_COEF');
			$range_weight = array('24R' => array(0, 20000 / $weight_coef), 'DRI' => array(20000 / $weight_coef, 130000 / $weight_coef), 'LD1' => array(0, 60000 / $weight_coef), 'LDS' => array(30000 / $weight_coef, 130000 / $weight_coef));
			Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'range_weight` (`id_carrier`, `delimiter1`, `delimiter2`)
										VALUES ('.(int)($get['id_carrier']).', '.$range_weight[$array[2]][0].', '.$range_weight[$array[2]][1].')');
			Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'range_price` (`id_carrier`, `delimiter1`, `delimiter2`) VALUES ('.(int)($get['id_carrier']).', 0.000000, 10000.000000)');
			$groups = Group::getGroups(Configuration::get('PS_LANG_DEFAULT'));
			foreach ($groups as $group)

				Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'carrier_group` (id_carrier, id_group) VALUES('.(int)($get['id_carrier']).', '.(int)($group['id_group']).')');

			$zones = Zone::getZones();
			foreach ($zones as $zone)
			{
				Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'carrier_zone` (id_carrier, id_zone) VALUES('.(int)($get['id_carrier']).', '.(int)($zone['id_zone']).')');
				$range_price_id = Db::getInstance()->getValue('SELECT id_range_price FROM ' . _DB_PREFIX_ . 'range_price WHERE id_carrier = "'.(int)($get['id_carrier']).'"');
				$range_weight_id = Db::getInstance()->getValue('SELECT id_range_weight FROM ' . _DB_PREFIX_ . 'range_weight WHERE id_carrier = "'.(int)($get['id_carrier']).'"');
				Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'delivery` (id_carrier, id_range_price, id_range_weight, id_zone, price) VALUES('.(int)($get['id_carrier']).', '.(int)($range_price_id).', NULL,'.(int)($zone['id_zone']).', 0.00)');
				Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'delivery` (id_carrier, id_range_price, id_range_weight, id_zone, price) VALUES('.(int)($get['id_carrier']).', NULL, '.(int)($range_weight_id).','.(int)($zone['id_zone']).', 0.00)');
			}

			if(!in_array($default[0]['value'], $checkD))
				$default = Db::getInstance()->executeS("UPDATE " . _DB_PREFIX_ . "configuration SET value = '" . (int)($get['id_carrier']) . "' WHERE name = 'PS_CARRIER_DEFAULT'");
		}
		else
			return false;

		$this->_html .= '<div class="conf confirm"><img src="'._PS_ADMIN_IMG_.'/ok.gif" /> '.$this->l('Settings updated').'<img src="http://www.prestashop.com/modules/mondialrelay.png?enseigne='.urlencode(Tools::getValue('mr_Enseigne_WebService')).'" style="float:right" /></div>';
		return true;
	}

	public function addMethodForm()
	{
		$zones = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "zone WHERE active = 1");
		$output = '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post" >
			<input type="hidden" name="mr_ModeCol" value="CCC" />
			<fieldset>
				<legend><img src="../modules/mondialrelay/images/logo.gif" alt="" />'.$this->l('Add a Shipping Method').'</legend>
				<ul>
					<li class="PS_MRRequireFields">
						<sup>* ' . $this->l('Required') . '</sup>
					</li>
					<li>
						<label for="mr_Name" class="shipLabel">'.$this->l('Carrier\'s name').'<sup>*</sup></label>
						<input type="text" id="mr_Name" name="mr_Name" '.(Tools::getValue('mr_Name') ? 'value="'.Tools::safeOutput(Tools::getValue('mr_Name')).'"' : '').'/>
					</li>';
					/*<li>
						<label for="mr_ModeCol" class="shipLabel">'.$this->l('Collection Mode').'<sup>*</sup></label>
						<select name="mr_ModeCol" id="mr_ModeCol" style="width:200px">
							<option value="CCC" selected >CCC : '.$this->l('Collection at the store').'</option>
						</select>
					</li>*/

	$output .= '<li>
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
		$query = Db::getInstance()->executeS('
		SELECT m.*
		FROM `'._DB_PREFIX_.'mr_method` m
			JOIN `'._DB_PREFIX_.'carrier` c
			ON (c.`id_carrier` = m.`id_carrier`)
		WHERE c.`deleted` = 0');

		$output = '
		<form action="' . Tools::safeOutput($_SERVER['REQUEST_URI']) . '" method="post">
			<fieldset class="shippingList">
				<legend><img src="../modules/mondialrelay/images/logo.gif" />'.$this->l('Shipping Method\'s list').'</legend>
				<ul>';
		if (!sizeof($query))
			$output .= '<li>'.$this->l('No shipping methods created').'</li>';
		else
			foreach ($query AS $Options)
			{
				$output .= '
					<li>
						<a href="' . 'index.php?tab=AdminModules&configure=mondialrelay&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)$this->context->employee->id).'&delete_mr=' . $Options['id_mr_method'] . '"><img src="../img/admin/disabled.gif" alt="Delete" title="Delete" /></a>' . str_replace('_', ' ', $Options['mr_Name']) . ' (' . $Options['mr_ModeCol'] . '-' . $Options['mr_ModeLiv'] . ' - ' . $Options['mr_ModeAss'] . ' : '.$Options['mr_Pays_list'].')
						<div style="float:right;"><a href="index.php?tab=AdminCarriers&id_carrier=' . (int)($Options['id_carrier']) . '&updatecarrier&token='.Tools::getAdminToken('AdminCarriers'.(int)(Tab::getIdFromClassName('AdminCarriers')).(int)$this->context->employee->id).'"><b><u>'.$this->l('Config Shipping.').'</u></b></a></div>
					</li>';
			}
		$output .= '
				</ul>
			</fieldset>
		</form><br />
		';

		return $output;
	}

	/*
	** Display advanced settings form
	*/
	public function advancedSettings()
	{
		$form = '';

		$form .= '
			<fieldset class="PS_MRFormStyle">
				<legend>
					<img src="../modules/mondialrelay/images/logo.gif" />'.$this->l('Advanced Settings'). ' -
					<a href="javascript:void(0);" id="PS_MRDisplayPersonalizedOptions">
						<font style="color:#00b511;">'.$this->l('Click to display / hide the options').'</font>
					</a>'.
			'</legend>
			<div id="PS_MRAdvancedSettings">
				<p>'.
					$this->l('URL Cron Task:').' '.Tools::getHttpHost(true, true).
					_MODULE_DIR_.$this->name.'/cron.php?secure_key='.
					Configuration::get('MONDIAL_RELAY_SECURE_KEY').
				'</p>
			</div>
			</fieldset>';
		return $form;
	}

	/*
	** Form to allow personalization fields sent for MondialRelay
	** Not used anymore but still present if needed
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
			$addr1 = Tools::safeOutput(Tools::getValue('Expe_ad1'));


		if (!Configuration::get('PS_MR_SHOP_NAME'))
			$warn .= '<div class="warn">'.
				$this->l('Its seems you updated Mondialrelay without use the uninstall / install method, you have to set up this part to make working the generating ticket process').
				'</div>';
		// Form
		$form = '<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post" class="form">';
		$form .= '
			<fieldset class="PS_MRFormStyle">
				<legend>
					<img src="../modules/mondialrelay/images/logo.gif" />'.$this->l('Advanced Settings'). ' -
					<a href="javascript:void(0);" id="PS_MRDisplayPersonalizedOptions"><font style="color:#00b511;">'.$this->l('Click to display / hide the options').'</font>	</a>'.
			'</legend>'.
			$warn.'
			<div id="PS_MRPersonalizedFields">
				<div style="margin-bottom:20px;">
				- '.$this->l('This part allow to override the data sent at MondialRelay when you want to generate Ticket. Some fields are restricted by the length, or forbidden char').'.
				</div>
				<label for="PS_MR_SHOP_NAME">'.$this->l('Shop Name').'</label>
			<div class="margin-form">
				<input type="text" name="Expe_ad1" value="'.$addr1.'" /><br />
				<p>'.$this->l('The key used by Mondialrelay is').' <b>Expe_ad1</b> '.$this->l('and has this default value').'
			 	: <b>'.Configuration::get('PS_SHOP_NAME').'</b></p>
			</div>

		<div class="margin-form">
			<input type="submit" name="PS_MRSubmitFieldPersonalization"  value="' . $this->l('Save') . '" class="button" />
		</div>
			</div>
		</fieldset>
		</form><br  />';
		return $form;
	}


	public function settingsstateorderForm()
	{
		$this->orderState = Configuration::get('MONDIAL_RELAY_ORDER_STATE');
	    $output = '';
		$output .= '<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post" class="form">';
		$output .= '<fieldset><legend><img src="../modules/mondialrelay/images/logo.gif" />'.$this->l('Settings').'</legend>';
		$output .= '<label for="id_order_state">' . $this->l('Order state') . '</label>';
		$output .= '<div class="margin-form">';
		$output .= '<select id="id_order_state" name="id_order_state" style="width:250px">';

		$order_states = OrderState::getOrderStates($this->context->language->id);
		foreach ( $order_states as $order_state)
		{
			$output  .= '<option value="' . $order_state['id_order_state'] . '" style="background-color:' . $order_state['color'] . ';"';
			if ($this->orderState == $order_state['id_order_state'] ) $output  .= ' selected="selected"';
			$output  .= '>' . $order_state['name'] . '</option>';
		}
		$output .= '</select>';
		$output .= '<p>' . $this->l('Choose the order state for labels. You can manage the labels on').' ';
		$output .= '<a href="index.php?tab=AdminMondialRelay&token='.Tools::getAdminToken('AdminMondialRelay'.(int)(Tab::getIdFromClassName('AdminMondialRelay')).(int)$this->context->employee->id).'" class="green">'.
		$this->l('the Mondial Relay administration page').'</a></p>';
		$output .= '</div>
		<div class="clear"></div>';
		$output .= '<div class="margin-form"><input type="submit" name="submit_order_state"  value="' . $this->l('Save') . '" class="button" /></div>';
		$output .= '</fieldset></form><br>';

		return $output;
    }


	public function settingsForm()
	{
		$output = '
			<form action="' . Tools::safeOutput($_SERVER['REQUEST_URI']) . '" method="post" >
				<fieldset>
					<legend><img src="../modules/mondialrelay/images/logo.gif" />'.$this->l('Mondial Relay Account Settings').'</legend>
					<div>
						- '.$this->l('These parameters are provided by Mondial Relay once you subscribed to their service').'
					</div>
					<ul>
						<li class="PS_MRRequireFields">
							<sup>* ' . $this->l('Required') . '</sup>
						</li>
						<li>
							<label for="mr_Enseigne_WebService" class="mrLabel">' . $this->l('Webservice Enseigne:') . '<sup>*</sup></label>
							<input id="mr_Enseigne_WebService" class="mrInput" type="text" name="mr_Enseigne_WebService" value="' .
							(Tools::getValue('mr_Enseigne_WebService') ? Tools::safeOutput(Tools::getValue('mr_Enseigne_WebService')) : Configuration::get('MR_ENSEIGNE_WEBSERVICE')) . '"/>
						</li>
						<li>
							<label for="mr_code_marque" class="mrLabel">' . $this->l('Code marque:') . '<sup>*</sup></label>
							<input id="mr_code_marque" class="mrInput" type="text" name="mr_code_marque" value="' .
							(Tools::getValue('mr_code_marque') ? Tools::safeOutput(Tools::getValue('mr_code_marque')) : Configuration::get('MR_CODE_MARQUE')) . '"/>
						</li>
						<li>
							<label for="mr_Key_WebService" class="mrLabel">' . $this->l('Webservice Key:') . '<sup>*</sup></label>
							<input id="mr_Key_WebService" class="mrInput" type="text" name="mr_Key_WebService" value="' .
							(Tools::getValue('mr_Key_WebService') ? Tools::safeOutput(Tools::getValue('mr_Key_WebService')) : Configuration::get('MR_KEY_WEBSERVICE')) . '"/>
						</li>
						<li>
							<label for="mr_Langage" class="mrLabel">' . $this->l('Etiquette\'s Language:') . '<sup>*</sup></label>
							<select id="mr_Langage" name="mr_Langage" value="'.
							(Tools::getValue('mr_Langage') ? Tools::safeOutput(Tools::getValue('mr_Langage')) : Configuration::get('MR_LANGUAGE')).'" >';
		$languages = Language::getLanguages();
		foreach ($languages as $language)
			$output .= '<option value="'.strtoupper($language['iso_code']).'" '.(strtoupper($language['iso_code']) == Configuration::get('MR_LANGUAGE') ? 'selected="selected"' : '').'>'.$language['name'].'</option>';

				$output .= '</select>
						</li>
						<li>
							<label for="mr_weight_coef" class="mrLabel">' . $this->l('Weight Coefficient:') . '<sup>*</sup></label>
							<input class="mrInput" type="text" name="mr_weight_coef" value="' .
							(Tools::getValue('mr_weight_coef') ? Tools::safeOutput(Tools::getValue('mr_weight_coef')) : Configuration::get('MR_WEIGHT_COEF')) . '"  style="width:45px;"/>
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
		$simpleresul = Db::getInstance()->executeS('
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

		$settings = Db::getInstance()->executeS($query);

		return $settings[0]['url_suivi'];
	}

	public function set_carrier($key, $value, $id_carrier)
	{
		if ($key == 'name')
			$key = 'mr_Name';

		return Db::getInstance()->execute('
			UPDATE ' . _DB_PREFIX_ . 'mr_method
			SET '.pSQL($key).'="'.pSQL($value).'"
			WHERE id_carrier=\''.(int)($id_carrier).'\' ; ');
	}

 	// Add for 1.3 compatibility and avoid duplicate code
	public static function jsonEncode($result)
	{
		return (method_exists('Tools', 'jsonEncode')) ?
			Tools::jsonEncode($result) : json_encode($result);
	}

	public static function ordersSQLQuery1_4($id_order_state)
	{
		return 'SELECT  o.`id_address_delivery` as id_address_delivery,
							o.`id_order` as id_order,
							o.`id_customer` as id_customer,
							o.`id_cart` as id_cart,
							o.`id_lang` as id_lang,
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
			ON (mr.`id_mr_method` = mrs.`id_method`)
			LEFT JOIN `'._DB_PREFIX_.'customer` c
			ON (c.`id_customer` = o.`id_customer`)
			WHERE (
				SELECT moh.`id_order_state`
				FROM `'._DB_PREFIX_.'order_history` moh
				WHERE moh.`id_order` = o.`id_order`
				ORDER BY moh.`date_add` DESC LIMIT 1) = '.(int)($id_order_state);
	}

	public static function ordersSQLQuery1_3($id_order_state)
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
			ON (ca.`id_carrier` = o.`id_carrier`
			AND ca.`name` = "mondialrelay")
			LEFT JOIN `'._DB_PREFIX_.'mr_selected` mrs
			ON (mrs.`id_cart` = o.`id_cart`)
			LEFT JOIN `'._DB_PREFIX_.'mr_method` mr
			ON (mr.`id_mr_method` = mrs.`id_method`)
			LEFT JOIN `'._DB_PREFIX_.'customer` c
			ON (c.`id_customer` = o.`id_customer`)
			WHERE (
				SELECT moh.`id_order_state`
				FROM `'._DB_PREFIX_.'order_history` moh
				WHERE moh.`id_order` = o.`id_order`
				ORDER BY moh.`date_add` DESC LIMIT 1) = '.(int)($id_order_state);
	}

	public static function getBaseOrdersSQLQuery($id_order_state)
	{
		if (_PS_VERSION_ >= '1.4')
			return self::ordersSQLQuery1_4($id_order_state);
		else
			return self::ordersSQLQuery1_3($id_order_state);
	}

	public static function getOrders($orderIdList = array(), $filterEntries = self::NO_FILTER)
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
		switch($filterEntries)
		{
			case self::WITHOUT_HOME_DELIVERY:
				$sql .= 'AND mr.mr_ModeLiv != "LD1" AND mr.mr_ModeLiv != "LDS"';
				break;
			default:
				break;
		}
		$sql .= '
			GROUP BY o.`id_order`
			ORDER BY o.`date_add` ASC';
		return Db::getInstance()->executeS($sql);
	}

	public function getErrorCodeDetail($code)
	{
		global $statCode;

		if (isset($statCode[$code]))
			return $statCode[$code];
		return $this->l('This error isn\'t referred : ') . $code;
	}

	public function getRelayPointSelected($id_cart)
	{
		return Db::getInstance()->getRow('
			SELECT s.`MR_selected_num`
			FROM `'._DB_PREFIX_.'mr_selected` s
			WHERE s.`id_cart` = '.(int)$id_cart);
	}

	public function isMondialRelayCarrier($id_carrier)
	{
		return Db::getInstance()->getRow('
			SELECT `id_carrier`
			FROM `'._DB_PREFIX_.'mr_method`
			WHERE `id_carrier` = '.(int)$id_carrier);
	}

	public function hookpaymentTop($params)
 	{
 		if ($this->isMondialRelayCarrier($params['cart']->id_carrier) &&
  		!$this->getRelayPointSelected($params['cart']->id))
   	$params['cart']->id_carrier = 0;
 	}
}
