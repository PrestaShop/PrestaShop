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

class MondialRelay extends Module
{
	const INSTALL_SQL_FILE = 'mrInstall.sql';
	
	private $_postErrors;
	
	public function __construct()
	{
		$this->name		= 'mondialrelay';
		$this->tab		= 'shipping_logistics';
		$this->version	= '1.3';

		parent::__construct();

		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('Mondial Relay');
		$this->description = $this->l('Deliver in Relay points');
		
		if (!defined('_MR_CSS_'))
			define('_MR_CSS_', dirname(__FILE__) . '/style.css');
		if (Module::isInstalled('mondialrelay') AND !Configuration::get('MONDIAL_RELAY_1_4'))
		{
			$this->update_v1_4();
			Configuration::updateValue('MONDIAL_RELAY_1_4', 1);
		}
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

		if (!$this->registerHook('shipping') OR
			!$this->registerHook('extraCarrier') OR
			!$this->registerHook('processCarrier') OR
			!$this->registerHook('orderDetail') OR
			!$this->registerHook('updateCarrier') OR
			!$this->registerHook('orderDetailDisplayed'))
			return false;
		
		if (!file_exists(_PS_MODULE_DIR_. '/mondialrelay/' . self::INSTALL_SQL_FILE))
			return false;
		elseif(!$sql = file_get_contents(_PS_MODULE_DIR_. '/mondialrelay/' . self::INSTALL_SQL_FILE))
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

			Db::getInstance()->Execute('INSERT INTO ' . _DB_PREFIX_ . 'tab 
				(id_parent, class_name, position, module) 
				VALUES(3, "AdminMondialRelay",  "'.(int)($pos).'", "mondialrelay")');	 	

			$id_tab = Db::getInstance()->Insert_ID();		
			
			$languages = Language::getLanguages();
			foreach ($languages AS $language)
				Db::getInstance()->Execute('
				INSERT INTO ' . _DB_PREFIX_ . 'tab_lang 
				(id_lang, id_tab, name) 
				VALUES("'.(int)($language['id_lang']).'", "'.(int)($id_tab).'", "Mondial Relay")');

			$profiles = Profile::getProfiles(Configuration::get('PS_LANG_DEFAULT'));
			foreach ($profiles as $profile)
				Db::getInstance()->Execute('INSERT INTO ' . _DB_PREFIX_ . 'access 
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
			if (isset($id_tab) AND !empty($id_tab))
			{	
				Db::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'tab WHERE id_tab = '.(int)($id_tab));
				Db::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'tab_lang WHERE id_tab = '.(int)($id_tab));
				Db::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'access WHERE id_tab = '.(int)($id_tab));
			}
		}

		if (!Configuration::deleteByName('MONDIAL_RELAY_1_4') OR
			!Configuration::deleteByName('MONDIAL_RELAY_INSTALL_UPDATE') OR
			!Configuration::deleteByName('MONDIAL_RELAY_SECURE_KEY') OR
			!Configuration::deleteByName('MONDIAL_RELAY_ORDER_STATE') OR
			!Configuration::deleteByName('MR_GOOGLE_MAP') OR
			!Configuration::deleteByName('MR_ENSEIGNE_WEBSERVICE') OR
			!Configuration::deleteByName('MR_CODE_MARQUE') OR
			!Configuration::deleteByName('MR_KEY_WEBSERVICE') OR
			!Configuration::deleteByName('MR_WEIGHT_COEF') OR
			!Db::getInstance()->Execute('UPDATE  '._DB_PREFIX_ .'carrier  set `active` = 0, `deleted` = 1 WHERE `external_module_name` = "mondialrelay"') OR
			!Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_ .'mr_historique, '._DB_PREFIX_ .'mr_method, '._DB_PREFIX_ .'mr_selected'))
			return false;
		return true;
	}
	
	private function update_v1_4()
	{
		Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'carrier` SET `shipping_external` = 0, `need_range` = 1, `external_module_name` = "mondialrelay", `shipping_method` = 1 WHERE `id_carrier` IN (SELECT `id_mr_method` FROM `'._DB_PREFIX_.'mr_method`)');
		return true;
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
		elseif (Tools::isSubmit('submitMethod'))
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
		elseif (Tools::isSubmit('submit_order_state'))
		{
			if (!Validate::isBool(Tools::getValue('mr_google_key')))
				$this->_postErrors[] = $this->l('Invalid google key');
			if (!Validate::isUnsignedInt(Tools::getValue('id_order_state')))
				$this->_postErrors[] = $this->l('Invalid order state');
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
		elseif (isset($_POST['submitShipping']) AND $_POST['submitShipping'])
			self::mrUpdate('shipping', $_POST, array());
		elseif (isset($_POST['submitMethod']) AND $_POST['submitMethod'])
			self::mrUpdate('addShipping', $setArray, $keyArray);
		elseif (isset($_POST['submit_order_state']) AND $_POST['submit_order_state'])
		{
			Configuration::updateValue('MONDIAL_RELAY_ORDER_STATE', Tools::getValue('id_order_state'));
			Configuration::updateValue('MR_GOOGLE_MAP', Tools::getValue('mr_google_key'));
			if (!Tools::isSubmit('updatesuccess'))
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
		
		if (is_array($carriers) AND count($carriers))
		{
			foreach ($carriers as $key => $carrier)
				if ($carrier['name'] == '0')
					$carriers[$key]['name'] = Configuration::get('PS_SHOP_NAME');
		}
		else
			$carriers = array();

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
		elseif ($carrier->url AND $order->shipping_number)
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
	
	public function hookProcessCarrier($params)
	{
		include_once(_PS_MODULE_DIR_.'/mondialrelay/MondialRelayClass.php');
		
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
			elseif (!Configuration::get('PS_ORDER_PROCESS_TYPE'))
			{
				
				if (empty($_POST['MR_Selected_Num_'.$cart->id_carrier])) // Case error : the customer didn't choose a 'relais' but selected Relais Colis TNT as a carrier 
					Tools::redirect('order.php?step=2&mr_null');
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
		if ($new_carrier->external_module_name == 'mondialrelay')
		{
			$mr_data = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'mr_method` WHERE `id_carrier` = '.(int)($params['id_carrier']));
			Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'mr_method` (mr_Name, mr_Pays_list, mr_ModeCol, mr_ModeLiv, mr_ModeAss, id_carrier)
										VALUES ("'.pSQL($mr_data['mr_Name']).'", "'.pSQL($mr_data['mr_Pays_list']).'", "'.pSQL($mr_data['mr_ModeCol']).'", "'.pSQL($mr_data['mr_ModeLiv']).'", "'.pSQL($mr_data['mr_ModeAss']).'", '.(int)($new_carrier->id).')');
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
					$id_zone = (int)($defaultCountry->id_zone);
				if ((Configuration::get('PS_SHIPPING_METHOD') AND (!Carrier::checkDeliveryPriceByWeight($row['id_carrier'], $cart->getTotalWeight(), $id_zone))) OR
					(!Configuration::get('PS_SHIPPING_METHOD') AND (!Carrier::checkDeliveryPriceByPrice($row['id_carrier'], $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING), $id_zone, $cart->id_currency))))
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
			include_once(_PS_MODULE_DIR_.'/mondialrelay/page_iso.php');

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
							'carriersextra' => $resultsArray));
			$nbcarriers = $nbcarriers + $i;
			return $this->display(__FILE__, 'mondialrelay.tpl');
		}
	}
	
	public function getContent()
	{	
		global $cookie;
		$error = null;
		
		if (isset($_GET['updatesuccess']))
			$this->_html .= '<div class="conf confirm"><img src="'._PS_ADMIN_IMG_.'/ok.gif" /> '.$this->l('Settings updated').'</div>';
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

		$this->_html .= '<h2>'.$this->l('Configure Mondial Relay Rate Module').'</h2>'.
		'<style> . "\n" . ' . file_get_contents(_MR_CSS_) . "\n" . '</style>
		<fieldset>
			<legend><img src="../modules/mondialrelay/logo.gif" />'.$this->l('To create a Mondial Relay carrier').'</legend>
		- '.$this->l('Enter and save your Mondial Relay account settings').'<br />
		- '.$this->l('Create a Carrier').'<br />
		- '.$this->l('Define a price for your carrier on').' <a href="index.php?tab=AdminCarriers&token='.Tools::getAdminToken('AdminCarriers'.(int)(Tab::getIdFromClassName('AdminCarriers')).(int)($cookie->id_employee)).'" class="green">'.$this->l('The Carrier page').'</a><br />
		- '.$this->l('To generate labels, you must have a valid and registered address of your store on your').' <a href="index.php?tab=AdminContact&token='.Tools::getAdminToken('AdminContact'.(int)(Tab::getIdFromClassName('AdminContact')).(int)($cookie->id_employee)).'" class="green">'.$this->l('contact page').'</a><br />
		- '.$this->l('Go to the front office').'<br /><br class="clear" />
		<p>'.$this->l('URL Cron Task:').' '.Tools::getHttpHost(true, true)._MODULE_DIR_.$this->name.'/cron.php?secure_key='.Configuration::get('MONDIAL_RELAY_SECURE_KEY').'</p></fieldset>
		<br class="clear" />'.self::settingsForm().self::settingsstateorderForm().self::addMethodForm().self::shippingForm().
		'<br class="clear" />';

		return $this->_html;
	}
	
	public function mrDelete($id)
	{
		$id = Db::getInstance()->getValue('SELECT `id_carrier` FROM `'._DB_PREFIX_ .'mr_method` WHERE `id_mr_method` = "'.(int)($id).'"');
		Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_ .'carrier` SET `active` = 0, `deleted` = 1 WHERE `id_carrier` = "'.(int)($id).'"');
		$this->_html .= '<div class="conf confirm"><img src="'._PS_ADMIN_IMG_.'/ok.gif" /> '.$this->l('Delete successful').'</div>';
	}
	
	public function mrUpdate($type, Array $array, Array $keyArray)
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
		elseif ($type == 'shipping')
		{
			array_pop($array);
			foreach ($array AS $Key => $value)
			{
				$key    = explode(',', $Key);
				$id = Db::getInstance()->getValue('SELECT `id_carrier` FROM `'._DB_PREFIX_ .'mr_method` WHERE `id_mr_method` = "'.(int)($key[0]).'"');
				Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'carrier SET active = "'.(int)($value).'" WHERE `id_carrier` = "'.(int)($id).'"');
			}
		}
		elseif ($type == 'addShipping') 
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

			Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'carrier` (`id_tax_rules_group`, `url`, `name`, `active`, `is_module`, `range_behavior`, `shipping_external`, `need_range`, `external_module_name`, `shipping_method`)
									VALUES("0", NULL, "'.pSQL($array[0]).'", "1", "1", "1", "0", "1", "mondialrelay", "1")');

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
			
			Tools::redirectAdmin('index.php?tab=AdminModules&configure=mondialrelay&updatesuccess&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)));
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
			<fieldset class="shippingList addMethodForm">
				<legend><img src="../modules/mondialrelay/logo.gif" alt="" />'.$this->l('Add a Shipping Method').'</legend>
				<ol>
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
					<li class="mrSubmit">
						<input type="submit" name="submitMethod" value="' . $this->l('Add a Shipping Method') . '" class="button" />
					</li>
					<li>
						<sup><sup>*</sup> ' . $this->l('Required') . '</sup>
					</li>
				</ol>
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
		JOIN `'._DB_PREFIX_.'carrier` c ON (c.`id_carrier` = m.`id_carrier`)
		WHERE c.`deleted` = 0');
			
		$output = '
		<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
			<fieldset class="shippingList">
				<legend><img src="../modules/mondialrelay/logo.gif" />'.$this->l('Shipping Method\'s list').'</legend>
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
	
	public function settingsstateorderForm()
	{
		global $cookie;
		$this->orderState = Configuration::get('MONDIAL_RELAY_ORDER_STATE');
	    $output = '';
		$output .= '<form action="'.$_SERVER['REQUEST_URI'].'" method="post" class="form">';
		$output .= '<fieldset><legend><img src="../modules/mondialrelay/logo.gif" />'.$this->l('Settings').'</legend>';
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
			<script type="text/javascript" >var url_appel="";</script>
			<script type="text/javascript" src="../modules/mondialrelay/kit_mondialrelay/js/ressources_MR.js"></script>
			<form action="' . $_SERVER['REQUEST_URI'] . '" method="post" >
				<fieldset class="settingsList">
					<legend><img src="../modules/mondialrelay/logo.gif" />'.$this->l('Mondial Relay Account Settings').'</legend>
					<ol>
						<li>
							<label style="float:none;" for="mr_Enseigne_WebService" class="mrLabel">' . $this->l('Webservice Enseigne:') . '<sup>*</sup></label>
							<input style="float:right;" id="mr_Enseigne_WebService" class="mrInput" type="text" name="mr_Enseigne_WebService" value="' .
							(Tools::getValue('mr_Enseigne_WebService') ? Tools::getValue('mr_Enseigne_WebService') : Configuration::get('MR_ENSEIGNE_WEBSERVICE')) . '"/>
						</li>
						<li>
							<label style="float:none;" for="mr_code_marque" class="mrLabel">' . $this->l('Code marque:') . '<sup>*</sup></label>
							<input style="float:right;" id="mr_code_marque" class="mrInput" type="text" name="mr_code_marque" value="' .
							(Tools::getValue('mr_code_marque') ? Tools::getValue('mr_code_marque') : Configuration::get('MR_CODE_MARQUE')) . '"/>
						</li>
						<li>
							<label style="float:none;" for="mr_Key_WebService" class="mrLabel">' . $this->l('Webservice Key:') . '<sup>*</sup></label>
							<input style="float:right;" id="mr_Key_WebService" class="mrInput" type="text" name="mr_Key_WebService" value="' .
							(Tools::getValue('mr_Key_WebService') ? Tools::getValue('mr_Key_WebService') : Configuration::get('MR_KEY_WEBSERVICE')) . '"/>
						</li>
						<li>
							<label style="float:none;" for="mr_Langage" class="mrLabel">' . $this->l('Etiquette\'s Language:') . '<sup>*</sup></label>
							<select style="float:right;" id="mr_Langage" name="mr_Langage" value="'.
							(Tools::getValue('mr_Langage') ? Tools::getValue('mr_Langage') : Configuration::get('MR_LANGUAGE')).'" >';
		$languages = Language::getLanguages();
		foreach ($languages as $language)
			$output .= '<option value="'.strtoupper($language['iso_code']).'" '.(strtoupper($language['iso_code']) == Configuration::get('MR_LANGUAGE') ? 'selected="selected"' : '').'>'.$language['name'].'</option>';
							
				$output .= '</select>
						</li>
						<li>
							<label style="float:none;" for="mr_weight_coef" class="mrLabel">' . $this->l('Weight Coefficient:') . '<sup>*</sup></label>
							<input id="mr_weight_coef" class="mrInput" type="text" name="mr_weight_coef" value="' . 
							(Tools::getValue('mr_weight_coef') ? Tools::getValue('mr_weight_coef') : Configuration::get('MR_WEIGHT_COEF')) . '"  style="width:45px;"/> ' . 
							$this->l('grammes = 1 ') . Configuration::get('PS_WEIGHT_UNIT').'
						</li>						
						<li class="MRSubmit">
							<input type="submit" name="submitMR" value="' . $this->l('Update Settings') . '" class="button" />
						</li>
						<li>
							<sup><sup>*</sup> ' . $this->l('Required') . '</sup>
						</li>
					</ol>
				</fieldset>
			</form>';

		return $output;
	}

	public function displayInfoByCart($id_cart)
	{
		$simpleresul = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'mr_selected where id_cart='.(int)($id_cart));
	
		if (trim($simpleresul[0]['exp_number']) != '0') 
			@$sortie .= $this->l('Nb expedition:').$simpleresul[0]['exp_number']."<br>";
		if (trim($simpleresul[0]['url_etiquette']) != '0') 
			@$sortie .= "<a href='".$simpleresul[0]['url_etiquette']."' target='etiquette".$simpleresul[0]['url_etiquette']."'>".$this->l('Label URL')."</a><br>";
		if (trim($simpleresul[0]['url_suivi']) != '0')
			@$sortie .= "<a href='".$simpleresul[0]['url_suivi']."' target='suivi".$simpleresul[0]['exp_number']."'>".$this->l('Follow-up URL')."</a><br>";
		if (trim($simpleresul[0]['MR_Selected_Num']) != '')
			@$sortie .= $this->l('Nb Point Relay :').$simpleresul[0]['MR_Selected_Num']."<br>";
		if (trim($simpleresul[0]['MR_Selected_LgAdr1']) != '')
			@$sortie .= $simpleresul[0]['MR_Selected_LgAdr1']."<br>";
		if (trim($simpleresul[0]['MR_Selected_LgAdr2']) != '')
			@$sortie .= $simpleresul[0]['MR_Selected_LgAdr2']."<br>";
		if (trim($simpleresul[0]['MR_Selected_LgAdr3']) != '')
			@$sortie .= $simpleresul[0]['MR_Selected_LgAdr3']."<br>"; 
		if (trim($simpleresul[0]['MR_Selected_LgAdr4']) != '')
			@$sortie .= $simpleresul[0]['MR_Selected_LgAdr4']."<br>"; 
		if (trim($simpleresul[0]['MR_Selected_CP']) != '')
			@$sortie .= $simpleresul[0]['MR_Selected_CP']." ";
		if (trim($simpleresul[0]['MR_Selected_Ville']) != '')
			@$sortie .= $simpleresul[0]['MR_Selected_Ville']."<br>";
		if (trim($simpleresul[0]['MR_Selected_Pays']) != '')
			@$sortie .= $simpleresul[0]['MR_Selected_Pays']."<br>";
		return '<p>'.$sortie.'</p>';
	}

	public function get_followup($shipping_number)
	{
	    $query    = 'SELECT url_suivi FROM '._DB_PREFIX_ .'mr_selected where  id_mr_selected=\''.(int)($shipping_number).'\';';
		$settings = Db::getInstance()->ExecuteS($query);
        return $settings[0]['url_suivi'];
	}


	public function set_carrier($key,$value,$id_carrier)
	{
		if($key == 'name')
			$key = 'mr_Name';
		return Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'mr_method SET '.pSQL($key).'="'.pSQL($value).'" WHERE id_carrier=\''.(int)($id_carrier).'\' ; ');
	}

	public function getL($key)
	{
		$trad = array(
			'List of recognized orders' => $this->l('List of recognized orders'),
			'Order number' => $this->l('Order number'),
			'Send e-mail to' => $this->l('Send e-mail to'),
			'Print A4 Label' => $this->l('Print A4 Label'),
			'Print A5 Label' => $this->l('Print A5 Label'),
			'return' => $this->l('return'),
			'All orders which have the state' => $this->l('All orders which have the state'),
			'Change configuration' => $this->l('Change configuration'),
			'No orders with this state.' => $this->l('No orders with this state.'),
			'Order ID' => $this->l('Order ID'),
			'Customer' => $this->l('Customer'),
			'Total price' => $this->l('Total price'),
			'Total shipping' => $this->l('Total shipping'),
			'Date' => $this->l('Date'),
			'Weight (in grams)' => $this->l('Weight (in grams)'),
			'Selected' => $this->l('Selected'),
			'All' => $this->l('All'),
			'None' => $this->l('None'),
			'MR_Selected_Num' => $this->l('MR_Selected_Num'),
			'MR_Selected_Pays' => $this->l('MR_Selected_Pays'),
			'exp_number' => $this->l('exp_number'),
			'Detail' => $this->l('Detail'),
			'View' => $this->l('View'),
			'Generate' => $this->l('Generate'),
			'Label creation history' => $this->l('Label creation history'),
			'Orders ID' => $this->l('Orders ID'),
			'Exps num' => $this->l('Exps num'),
			'Delete selected history' => $this->l('Delete selected history'),
			'Closed' => $this->l('Closed'),
			'Monday' => $this->l('Monday'),
			'Tuesday' => $this->l('Tuesday'),
			'Wednesday' => $this->l('Wednesday'),
			'Thursday' => $this->l('Thursday'),
			'Friday' => $this->l('Friday'),
			'Saturday' => $this->l('Saturday'),
			'Sunday' => $this->l('Sunday'),
			'Select this Relay Point' => $this->l('Select this Relay Point'),
			'To generate sticks, you must have register a correct address of your store on' => $this->l('To generate labels, you must have registered a correct address of your store on'),
			'To generate labels, you must have registered a correct address of your store on' => $this->l('To generate labels, you must have registered a correct address of your store on'),
			'The contact page' => $this->l('The contact page'),
			'Settings updated succesfull' => $this->l('Settings updated'),
			'Settings updated' => $this->l('Settings updated'),
			'Empty address : Are you sure you have set a valid address on the contact page?' => $this->l('Empty address : Are you sure you have set a valid address on the contact page?')
		);
		return (array_key_exists($key, $trad)) ? $trad[$key] : $key;
	}
}
