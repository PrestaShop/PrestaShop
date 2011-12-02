<?php
// Avoid direct access to the file
require_once(_PS_MODULE_DIR_."/tntcarrier/classes/PackageTnt.php");
require_once(_PS_MODULE_DIR_."/tntcarrier/classes/TntWebService.php");
require_once(_PS_MODULE_DIR_."/tntcarrier/classes/OrderInfoTnt.php");
require_once(_PS_MODULE_DIR_."/tntcarrier/classes/serviceCache.php");

if (!defined('_PS_VERSION_'))
	exit;

class TntCarrier extends CarrierModule
{
	public  $id_carrier;

	private $_html = '';
	private $_postErrors = array();
	private $_moduleName = 'tntcarrier';
	private $_fieldsList = array();
	
	/*
	** Construct Method
	**
	*/

	public function __construct()
	{
		$this->name = 'tntcarrier';
		$this->tab = 'shipping_logistics';
		$this->version = '1.0';
		$this->author = 'PrestaShop';
		$this->limited_countries = array('fr');

		parent::__construct ();

		$this->displayName = $this->l('TNT Express');
		$this->description = $this->l('Offer your customers, different delivery methods with TNT');

		if (self::isInstalled($this->name))
		{	
			global $cookie;
			$warning = array();
			$this->loadingVar();
			$carriers = Carrier::getCarriers($cookie->id_lang, true, false, false, null, PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);

			foreach ($this->_fieldsList as $keyConfiguration => $name)
				if (!Configuration::get($keyConfiguration) && !empty($name))
					$warning[] = '\''.$name.'\' ';
					
			// Saving id carrier list
			$id_carrier_list = array();
			foreach($carriers as $carrier)
				$id_carrier_list[] .= $carrier['id_carrier'];
			
			if (count($warning))
				$this->warning .= implode(' , ',$warning).$this->l('must be configured to use this module correctly.').' ';
		}
	}

	public function loadingVar()
	{
		// Loading Fields List
		$this->_fieldsList = array(
			'TNT_CARRIER_LOGIN' => $this->l('TNT Login'),
			'TNT_CARRIER_PASSWORD' => $this->l('TNT Password'),
			'TNT_CARRIER_NUMBER_ACCOUNT' => $this->l('TNT Number Account'),
			'TNT_CARRIER_SHIPPING_COMPANY' => '',
			'TNT_CARRIER_SHIPPING_LASTNAME' => '',
			'TNT_CARRIER_SHIPPING_FIRSTNAME' => '',
			'TNT_CARRIER_SHIPPING_ADDRESS1' => '',
			'TNT_CARRIER_SHIPPING_ADDRESS2' => '',
			'TNT_CARRIER_SHIPPING_ZIPCODE' => '',
			'TNT_CARRIER_SHIPPING_CITY' => '',
			'TNT_CARRIER_SHIPPING_EMAIL' => '',
			'TNT_CARRIER_SHIPPING_PHONE' => '',
			'TNT_CARRIER_SHIPPING_CLOSING' => '',
			'TNT_CARRIER_SHIPPING_DELIVERY' => '',
			'TNT_CARRIER_SHIPPING_COLLECT' => '',
			'TNT_CARRIER_SHIPPING_PEX' => '',
			'TNT_CARRIER_PRINT_STICKER' => '',
			'TNT_CARRIER_CORSE_OVERCOST' => ''
		);
		
		$option = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'tnt_carrier_option`');
		foreach($option as $k => $v)
		{
			$this->_fieldsList['TNT_CARRIER_'.$v['option'].'_ID'] = (float)($v['id_carrier']);
			$this->_fieldsList['TNT_CARRIER_'.$v['option'].'_OVERCOST'] = Configuration::get('TNT_CARRIER_'.$v['option'].'_OVERCOST');
		}
	}
	/*
	** Install / Uninstall Methods
	**
	*/

	public function install()
	{	
		// Install SQL
		include(dirname(__FILE__).'/sql-install.php');
		foreach ($sql as $s)
			if (!Db::getInstance()->Execute($s))
				return false;
		// Install Module
		if (!parent::install() OR !$this->registerHook('updateCarrier') OR !$this->registerHook('orderDetailDisplayed') OR !$this->registerHook('adminOrder') or !$this->registerHook('extraCarrier'))
			return false;
		if (file_exists('../modules/'.$this->_moduleName.'/serviceBase.xml'))
		{
			$serviceList = simplexml_load_file('../modules/'.$this->_moduleName.'/serviceBase.xml');
			if ($serviceList == false)
				return false;
		}
		foreach($serviceList as $k => $v)
		{
			$carrierConfig = array(
				'name' => $v->name,
				'id_tax_rules_group' => 0,
				'active' => true,
				'deleted' => true,
				'shipping_handling' => false,
				'range_behavior' => 0,
				'delay' => array('fr' => $v->descriptionfr, 'en' => $v->description),
				'id_zone' => 1,
				'is_module' => true,
				'shipping_external' => true,
				'external_module_name' => $this->_moduleName,
				'need_range' => true
			);
			$id_carrier = $this->installExternalCarrier($carrierConfig);
			Configuration::updateValue('TNT_CARRIER_'.$v->option.'_ID', (int)($id_carrier));
			Db::getInstance()->ExecuteS('INSERT INTO `'._DB_PREFIX_.'tnt_carrier_option` (`option`, `id_carrier`) VALUES ("'.$v->option.'", "'.(int)$id_carrier.'")');
		}
		return true;
	}
	
	public static function installExternalCarrier($config)
	{
		$carrier = new Carrier();
		$carrier->name = $config['name'];
		$carrier->id_tax_rules_group = $config['id_tax_rules_group'];
		$carrier->id_zone = $config['id_zone'];
		$carrier->active = $config['active'];
		$carrier->deleted = $config['deleted'];
		$carrier->delay = $config['delay'];
		$carrier->shipping_handling = $config['shipping_handling'];
		$carrier->range_behavior = $config['range_behavior'];
		$carrier->is_module = $config['is_module'];
		$carrier->shipping_external = $config['shipping_external'];
		$carrier->external_module_name = $config['external_module_name'];
		$carrier->need_range = $config['need_range'];

		$languages = Language::getLanguages(true);
		foreach ($languages as $language)
		{
			if ($language['iso_code'] == 'fr')
				$carrier->delay[(int)$language['id_lang']] = $config['delay'][$language['iso_code']];
			if ($language['iso_code'] == 'en')
				$carrier->delay[(int)$language['id_lang']] = $config['delay'][$language['iso_code']];
			if ($language['iso_code'] == Language::getIsoById(Configuration::get('PS_LANG_DEFAULT')))
				$carrier->delay[(int)$language['id_lang']] = $config['delay'][$language['iso_code']];
		}

		if ($carrier->add())
		{
			$groups = Group::getGroups(true);
			foreach ($groups as $group)
				Db::getInstance()->autoExecute(_DB_PREFIX_.'carrier_group', array('id_carrier' => (int)($carrier->id), 'id_group' => (int)($group['id_group'])), 'INSERT');

			$rangePrice = new RangePrice();
			$rangePrice->id_carrier = $carrier->id;
			$rangePrice->delimiter1 = '0';
			$rangePrice->delimiter2 = '10000';
			$rangePrice->add();

			$rangeWeight = new RangeWeight();
			$rangeWeight->id_carrier = $carrier->id;
			$rangeWeight->delimiter1 = '0';
			$rangeWeight->delimiter2 = '10000';
			$rangeWeight->add();

			$zones = Zone::getZones(true);
			foreach ($zones as $zone)
			{
				Db::getInstance()->autoExecute(_DB_PREFIX_.'carrier_zone', array('id_carrier' => (int)($carrier->id), 'id_zone' => (int)($zone['id_zone'])), 'INSERT');
				Db::getInstance()->autoExecuteWithNullValues(_DB_PREFIX_.'delivery', array('id_carrier' => (int)($carrier->id), 'id_range_price' => (int)($rangePrice->id), 'id_range_weight' => null, 'id_zone' => (int)($zone['id_zone']), 'price' => '0'), 'INSERT');
				Db::getInstance()->autoExecuteWithNullValues(_DB_PREFIX_.'delivery', array('id_carrier' => (int)($carrier->id), 'id_range_price' => null, 'id_range_weight' => (int)($rangeWeight->id), 'id_zone' => (int)($zone['id_zone']), 'price' => '0'), 'INSERT');
			}

			// Copy Logo
			if (!copy(dirname(__FILE__).'/carrier.jpg', _PS_SHIP_IMG_DIR_.'/'.(int)$carrier->id.'.jpg'))
				return false;

			// Return ID Carrier
			return (int)($carrier->id);
		}

		return false;
	}
	
	public function uninstall()
	{
		// Uninstall Carriers
		Db::getInstance()->autoExecute(_DB_PREFIX_.'carrier', array('deleted' => 1), 'UPDATE', '`external_module_name` = \'tntcarrier\'');
		// Uninstall Config
		foreach ($this->_fieldsList as $keyConfiguration => $name)
			if (!Configuration::deleteByName($keyConfiguration))
				return false;	
		// Uninstall SQL
		include(dirname(__FILE__).'/sql-uninstall.php');
		foreach ($sql as $s)
			if (!Db::getInstance()->Execute($s))
				return false;
		// Uninstall Module
		if (!parent::uninstall() OR !$this->unregisterHook('updateCarrier'))
			return false;
		return true;
	}

	/*
	** Form Config Methods
	**
	*/

	public function getContent()
	{
		$this->_html .= '<h2><a href="http://www.tnt.fr/"><img src="'.$this->_path.'logo.gif" alt="' . $this->l('TNT Carrier').'" /></a></h2>';
		if (!empty($_POST) AND Tools::isSubmit('submitSave'))
		{
			$this->_postValidation();
			if (!sizeof($this->_postErrors))
				$this->_postProcess();
			else
				foreach ($this->_postErrors AS $err)
					$this->_html .= '<div class="alert error"><img src="'._PS_IMG_.'admin/forbbiden.gif" alt="nok" />&nbsp;'.$err.'</div>';
		}
		$this->_displayForm();
		return $this->_html;
	}

	private function _displayForm()
	{
		global $smarty;
		
		$globalVar = array(
		'tab' => Tools::getValue('tab'),
		'configure' => Tools::getValue('configure'),
		'token' => Tools::getValue('token'),
		'tab_module' => Tools::getValue('tab_module'),
		'module_name' => Tools::getValue('module_name'));
		
		$smarty->assign('glob', $globalVar);
		
		$lang = array(
					'followParameters' => $this->l('The following parameters were provided to you by TNT'), 'registered' => $this->l('If you are not yet registered, click '), 'here' => $this->l('here'),
					'accountSetting' => $this->l('Account settings'), 'shippingSetting' => $this->l('Shipping Settings'), 'serviceSetting' => $this->l('Service Settings'),
					'accountTNT' => $this->l('Account TNT'), 'login' => $this->l('Login'), 'password' => $this->l('Password'), 'numberAccount' => $this->l('Number account'),
					'fillDataInTheForm' => $this->l('Fill Data in the form'), 'shipping' => $this->l('Shipping'), 'collect' => $this->l('Would you like TNT to pick up your package ?'), 'noDeposit' => $this->l('No (Deposit)'), 
					'yes' => $this->l('Yes'), 'chooseYourDepositoryLocation' => $this->l('Choose your depository location'), 'pexCode' => $this->l('Pex Code'), 'companyName' => $this->l('Company Name'),
					'lastName' => $this->l('Last name'), 'firstName' => $this->l('First name'), 'address1' => $this->l('Address line 1'), 'address2' => $this->l('Address line 2'), 'zip' => $this->l('Zip / Postal Code'), 'city' => $this->l('Your City'), 
					'email' => $this->l('Your Email Address'), 'phone' => $this->l('Your Phone Number'), 'closingTime' => $this->l('Your Closing Time'), 'saturdayDelivery' => $this->l('Saturday Delivery'), 'no' => $this->l('No'),
					'labelFormatPrinting' => $this->l('Label Format for printing (This Label will have to be sticked on the package)'), 'a4printing' => $this->l('A4 printing'), 'withoutPrintingLogoTNT' => $this->l('without printing the logo TNT'), 'withReversePrint' => $this->l('with a reverse print'), 'withoutPrintingLogoTNTWithReversePrint' => $this->l('without printing the logo TNT and with a reverse print'),
					'newService' => $this->l('New Service'), 'id' => $this->l('ID'), 'name' => $this->l('Name'), 'description' => $this->l('Description'), 'code' => $this->l('Code'), 'additionnalCharge' => $this->l('Additionnal charge (Euros)'), 'activated' => $this->l('Activated'), 'edit' => $this->l('edit'), 'delete' => $this->l('delete'), 'place' => $this->l('Place')
					);
		
		$smarty->assign('lang', $lang);
		
		$this->_html .= '<fieldset>
		<legend>'.$this->l('TNT Carrier Module Status').'</legend>';
		
		$alert = array();
		if (!Configuration::get('TNT_CARRIER_LOGIN') || !Configuration::get('TNT_CARRIER_PASSWORD') || !Configuration::get('TNT_CARRIER_NUMBER_ACCOUNT'))
			$alert['account'] = 1;
		if ( 
			!Configuration::get('TNT_CARRIER_SHIPPING_ADDRESS1') || 
			!Configuration::get('TNT_CARRIER_SHIPPING_ZIPCODE') || 
			!Configuration::get('TNT_CARRIER_SHIPPING_CITY') || 
			!Configuration::get('TNT_CARRIER_SHIPPING_EMAIL') ||
			!Configuration::get('TNT_CARRIER_SHIPPING_PHONE'))
			$alert['shipping'] = 1;
		if ((Db::getInstance()->getValue('SELECT * FROM `'._DB_PREFIX_.'carrier` WHERE `external_module_name` = "'.$this->_moduleName.'" AND deleted = "0"')) < 1)
			$alert['service'] = 1;
		if (!count($alert))
			$this->_html .= '<img src="'._PS_IMG_.'admin/module_install.png" /><strong>'.$this->l('TNT Carrier is configured and online!').'</strong>';
		else
		{
			$this->_html .= '<img src="'._PS_IMG_.'admin/warn2.png" /><strong>'.$this->l('TNT Carrier is not configured yet, please:').'</strong>';
			$this->_html .= '<br />'.(isset($alert['account']) ? '<img src="'._PS_IMG_.'admin/warn2.png" />' : '<img src="'._PS_IMG_.'admin/module_install.png" />').' '.$this->l('Make sure you have a tnt account.');
			$this->_html .= '<br />'.(isset($alert['shipping']) ? '<img src="'._PS_IMG_.'admin/warn2.png" />' : '<img src="'._PS_IMG_.'admin/module_install.png" />').' '.$this->l('Make sure you have a correct shipping address.');
			$this->_html .= '<br />'.(isset($alert['service']) ? '<img src="'._PS_IMG_.'admin/warn2.png" />' : '<img src="'._PS_IMG_.'admin/module_install.png" />').' '.$this->l('Make sure services are activated after a bill.');
		}

		$this->_html .= '</fieldset><div class="clear">&nbsp;</div>';
		$this->_html .= $this->_displayFormConfig();
	}
	
	private function _displayFormConfig()
	{
		global $smarty;
		$var = array('account' => $this->_displayFormAccount(), 'shipping' => $this->_displayFormShipping(), 'service' => $this->_displayService(),
					'country' => $this->_displayCountry('Corse'), 'info' => $this->_displayInfo('weight'));
		$smarty->assign('varMain', $var);
		$html = $this->display( __FILE__, 'tpl/main.tpl' );
		if (isset($_GET['id_tab']))
			$html .= '<script>
				  $(".menuTabButton.selected").removeClass("selected");
				  $("#menuTab'.Tools::getValue('id_tab').'").addClass("selected");
				  $(".tabItem.selected").removeClass("selected");
				  $("#menuTab'.Tools::getValue('id_tab').'Sheet").addClass("selected");
			</script>';
		return $html;
	}
	
	private function _displayFormAccount()
	{		
		global $smarty;
		$var = array('login' => Tools::getValue('tnt_carrier_login', Configuration::get('TNT_CARRIER_LOGIN')), 'password' => Tools::getValue('tnt_carrier_password', Configuration::get('TNT_CARRIER_PASSWORD')),
					'account' => Tools::getValue('tnt_carrier_number_account', Configuration::get('TNT_CARRIER_NUMBER_ACCOUNT')));
		$smarty->assign('varAccount', $var);
		return $this->display( __FILE__, 'tpl/accountForm.tpl' );
	}
	
	private function _displayFormShipping()
	{
		global $cookie, $smarty;
		
		$var = array('moduleName' => $this->_moduleName, 'collect' => Configuration::get('TNT_CARRIER_SHIPPING_COLLECT'), 'pex' => Configuration::get('TNT_CARRIER_SHIPPING_PEX'), 'company' => Configuration::get('TNT_CARRIER_SHIPPING_COMPANY'),
					'lastName' => Configuration::get('TNT_CARRIER_SHIPPING_LASTNAME'), 'firstName' => Configuration::get('TNT_CARRIER_SHIPPING_FIRSTNAME'), 'address1' => Configuration::get('TNT_CARRIER_SHIPPING_ADDRESS1'),
					'address2' => Configuration::get('TNT_CARRIER_SHIPPING_ADDRESS2'), 'zipCode' => Configuration::get('TNT_CARRIER_SHIPPING_ZIPCODE'), 'city' => Configuration::get('TNT_CARRIER_SHIPPING_CITY'), 'email' => Configuration::get('TNT_CARRIER_SHIPPING_EMAIL'),
					'phone' => Configuration::get('TNT_CARRIER_SHIPPING_PHONE'), 'closing' => Configuration::get('TNT_CARRIER_SHIPPING_CLOSING'), 'delivery' => Configuration::get('TNT_CARRIER_SHIPPING_DELIVERY'), 'sticker' => Configuration::get('TNT_CARRIER_PRINT_STICKER'));
		$smarty->assign('varShipping', $var);
		return $this->display( __FILE__, 'tpl/shippingForm.tpl' );
	}
	
	private function _displayService()
	{
		global $smarty;
		if (Tools::getValue('action') == 'del' && Tools::getValue('service') != '')
		{
				$id = Tools::getValue('service');
				Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'carrier` SET `deleted` = "1" WHERE `id_carrier` = '.(int)($id).'');
				$option = Db::getInstance()->ExecuteS('SELECT `option` FROM `'._DB_PREFIX_.'tnt_carrier_option` WHERE `id_carrier` = "'.(int)($id).'"');
				Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'tnt_carrier_option` WHERE `id_carrier` = '.(int)($id).'');
				Configuration::deleteByName('TNT_CARRIER_'.$option[0]['option'].'_ID');
				Configuration::deleteByName('TNT_CARRIER_'.$option[0]['option'].'_OVERCOST');
		}
		$irow = 0;
		$serviceList = Db::getInstance()->ExecuteS('SELECT c.deleted, c.name, cl.delay, o.option
													FROM `'._DB_PREFIX_.'carrier` c, `'._DB_PREFIX_.'carrier_lang` cl, `'._DB_PREFIX_.'tnt_carrier_option` o , `'._DB_PREFIX_.'lang` l
													WHERE c.external_module_name = "'.$this->_moduleName.'" AND c.id_carrier = cl.id_carrier AND cl.id_lang = l.id_lang AND l.iso_code = "'.Language::getIsoById(Configuration::get('PS_LANG_DEFAULT')).'" AND o.id_carrier = c.id_carrier');
		foreach ($serviceList as $k => $v)
			{
				$serviceList[$k]['optionId'] = Configuration::get('TNT_CARRIER_'.$v['option'].'_ID');
				$serviceList[$k]['optionOvercost'] = Configuration::get('TNT_CARRIER_'.$v['option'].'_OVERCOST');
			}
		
		$var = array('serviceList' => $serviceList,
					'action' => Tools::getValue('action'),
					'section' => Tools::getValue('section'),
					'form' => $this->_displayFormService(Tools::getValue('service')));
		$smarty->assign('varService', $var);
		return $this->display( __FILE__, 'tpl/service.tpl' );
	}
	
	private function _displayInfo($cat)
	{
		if (Tools::getValue('action') == 'del' && Tools::getValue($cat) != '')
		{
			$id = Tools::getValue($cat);
			Db::getInstance()->ExecuteS('DELETE FROM `'._DB_PREFIX_.'tnt_carrier_'.$cat.'` WHERE `id_'.$cat.'` = '.(int)$id.'');
		}
		
		$html = '
		<a href="index.php?tab='.Tools::getValue('tab').'&configure='.Tools::getValue('configure').'&token='.Tools::getValue('token').'&tab_module='.Tools::getValue('tab_module').'&module_name='.Tools::getValue('module_name').'&id_tab=3&section='.$cat.'&action=new">
		<img src="../img/admin/add.gif" alt="add"/> '.$this->l('New weight option').'</a></br><br/>
		<table class="table" cellspacing="0" cellpading="0">
			<tr>
				<th>'.$this->l('ID').'</th><th>'.$this->l('Weight Min').'</th><th>'.$this->l('Weight Max').'</th><th>'.$this->l('Additionnal charge (Euros)').'</th><th></th>
			</tr>';
		$List = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'tnt_carrier_'.$cat.'` ORDER BY `id_'.$cat.'`');
		$irow = 0;
		foreach ($List as $v)
		{
			$html .= '<tr '.($irow++ % 2 ? 'class="alt_row"' : '').'>
			<td>'.$v['id_'.$cat.''].'</td>
			<td>'.$v[''.$cat.'_min'].'</td>
			<td>'.$v[''.$cat.'_max'].'</td>
			<td>'.$v['additionnal_charges'].'</td>
			<td>
			<a href="index.php?tab='.Tools::getValue('tab').'&configure='.Tools::getValue('configure').'&token='.Tools::getValue('token').'&tab_module='.Tools::getValue('tab_module').'&module_name='.Tools::getValue('module_name').'&id_tab=3&section='.$cat.'&action=edit&'.$cat.'='.$v['id_'.$cat.''].'">
				<img src="../img/admin/edit.gif" alt="edit" title="'.$this->l('Edit').'"/></a>
				<a href="index.php?tab='.Tools::getValue('tab').'&configure='.Tools::getValue('configure').'&token='.Tools::getValue('token').'&tab_module='.Tools::getValue('tab_module').'&module_name='.Tools::getValue('module_name').'&id_tab=3&section='.$cat.'&action=del&'.$cat.'='.$v['id_'.$cat.''].'">
				<img src="../img/admin/delete.gif" alt="delete" title="'.$this->l('Delete').'"/></a></td>
			</tr>';
		}
		$html .= '
		</table><br/>
		<div id="divForm'.$cat.'Service">'.((Tools::getValue('action') == 'edit' || Tools::getValue('action') == 'new') && Tools::getValue('section') == $cat ? $this->_displayFormInfo($cat, Tools::getValue($cat)) : '').'</div>
		';
		
		return $html;
	}
	
	private function _displayCountry($country)
	{	
		global $smarty;
		
		$var = array(
		'country' => $country,
		'overcost' => Configuration::get('TNT_CARRIER_'.strtoupper($country).'_OVERCOST'),
		'action' => Tools::getValue('action'),
		'section' => Tools::getValue('section'),
		'getCountry' => Tools::getValue('country'),
		'form' => (Tools::getValue('country') != '' ? $this->_displayFormCountry(Tools::getValue('country')) : '')
		);
		$smarty->assign('varCountry', $var);
		return $this->display( __FILE__, 'tpl/country.tpl' );
	}
	
	private function _displayFormService($id = null)
	{
		global $smarty;
		$name = '';
		$description = '';
		$code = '';
		$charge = '';
		$display = '';
		
		if ($id != null)
		{			
			$service = Db::getInstance()->ExecuteS('SELECT c.deleted, c.name, l.delay, o.option, o.additionnal_charges
													FROM `'._DB_PREFIX_.'carrier` c, `'._DB_PREFIX_.'carrier_lang` l, `'._DB_PREFIX_.'tnt_carrier_option` o 
													WHERE c.id_carrier = "'.(int)$id.'" AND c.id_carrier = l.id_carrier AND l.id_lang = "1" AND o.id_carrier = c.id_carrier');
			if ($service != NULL)
			{
				$name = $service[0]['name'];
				$description = $service[0]['delay'];
				$code = $service[0]['option'];
				$charge = $service[0]['additionnal_charges'];
				$display = $service[0]['deleted'];
			}
		}
		$var = array('id' => $id,'name' => $name, 'description' => $description, 'code' => $code, 'charge' => $charge, 'display' => $display);
		$smarty->assign('varServiceForm', $var);
		return $this->display( __FILE__, 'tpl/serviceForm.tpl' );
	}
		
	private function _displayFormInfo($cat, $id = null)
	{
		$info_min = '';
		$info_max = '';
		$charge = '';
		
		if ($id != null)
		{
			$info = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'tnt_carrier_'.$cat.'` WHERE `id_'.$cat.'` = "'.$id.'"');
			$info_min = $info[0][$cat.'_min'];
			$info_max = $info[0][$cat.'_max'];
			$charge = $info[0]['additionnal_charges'];
		}
		
		$html = '
		<form action="index.php?tab='.Tools::getValue('tab').'&configure='.Tools::getValue('configure').'&token='.Tools::getValue('token').'&tab_module='.Tools::getValue('tab_module').'&module_name='.Tools::getValue('module_name').'&id_tab=3&section='.$cat.'&action=new" method="post" class="form" id="configForm'.$cat.'">
			'.($id != null ? '<input type="hidden" name="'.$cat.'_id" value="'.$id.'"/>' : '').'
			<table class="table" cellspacing="0" cellpadding="0">
				<tr>
					<th>'.$this->l('Weight min').'</th><th>'.$this->l('Weight max').'</th><th>'.$this->l('Additionnal charge').'</th><th></th>
				</tr>
				<tr>
					<td><input type="text" name="tnt_carrier_'.$cat.'_min" size="20" value="'.$info_min.'"/></td>
					<td><input type="text" name="tnt_carrier_'.$cat.'_max" size="20" value="'.$info_max.'"/></td>
					<td><input type="text" name="tnt_carrier_'.$cat.'_charge" size="10" value="'.$charge.'"/></td>
					<td><input class="button" name="submitSave" type="submit"></td>
				</tr>
			</table>
		</form>';
		
		return $html;
	}
	
	private function _displayFormCountry($country)
	{
		global $smarty;
		$var = array(
		'country' => $country,
		'overcost' => Configuration::get('TNT_CARRIER_'.strtoupper($country).'_OVERCOST')
		);
		$smarty->assign('varCountryForm', $var);
		return $this->display( __FILE__, 'tpl/countryForm.tpl' );
	}
	
	private function _postValidation()
	{
		if (Tools::getValue('section') == 'account')
			$this->_postValidationAccount();
		elseif (Tools::getValue('section') == 'shipping')
			$this->_postValidationShipping();
		elseif (Tools::getValue('section') == 'service')
			$this->_postValidationService();
		elseif (Tools::getValue('section') == 'weight')
			$this->_postValidationInfo(Tools::getValue('section'));
		elseif (Tools::getValue('section') == 'country')
			$this->_postValidationCountry();
	}

	private function _postProcess()
	{

	}
	
	private function _postValidationAccount()
	{
		$login = Tools::getValue('tnt_carrier_login');
		$password = Tools::getValue('tnt_carrier_password');
		$number = Tools::getValue('tnt_carrier_number_account');
		if (!$login || !$password || !$number)
			$this->_postErrors[] = $this->l("All the fields are required");
		Configuration::updateValue('TNT_CARRIER_LOGIN', $login);
		Configuration::updateValue('TNT_CARRIER_PASSWORD', $password);
		Configuration::updateValue('TNT_CARRIER_NUMBER_ACCOUNT', $number);
	}
	
	private function _postValidationShipping()
	{
		$collect = $email = Tools::getValue('tnt_carrier_shipping_collect');
		$company = Tools::getValue('tnt_carrier_shipping_company');
		$pex = Tools::getValue('tnt_carrier_shipping_pex');
		$lname = Tools::getValue('tnt_carrier_shipping_last_name');
		$fname = Tools::getValue('tnt_carrier_shipping_first_name');
		$address1 = Tools::getValue('tnt_carrier_shipping_address1');
		$address2 = Tools::getValue('tnt_carrier_shipping_address2');
		$postal_code = Tools::getValue('tnt_carrier_shipping_postal_code');
		$city = Tools::getValue('tnt_carrier_shipping_city');
		$email = Tools::getValue('tnt_carrier_shipping_email');
		$phone = Tools::getValue('tnt_carrier_shipping_phone');
		$closing = Tools::getValue('tnt_carrier_shipping_closing');
		$delivery = Tools::getValue('tnt_carrier_shipping_delivery');
		$print = Tools::getValue('tnt_carrier_print_sticker');
		
		if (!$collect && $pex == '')
			$this->_postErrors[] = $this->l("The pex code is missing");
		if ($collect && $company == '')
			$this->_postErrors[] = $this->l("Your company name is missing");
		if ($collect && !$lname)
			$this->_postErrors[] = $this->l("Your last name is missing");
		if ($collect && !$fname)
			$this->_postErrors[] = $this->l("Your first name is missing");
		if (!$address1)
			$this->_postErrors[] = $this->l("Your address is missing");
		if (!$postal_code)
			$this->_postErrors[] = $this->l("Your zip code is missing");
		if (!$email)
			$this->_postErrors[] = $this->l("Your email address is missing");
		if (!$phone)
			$this->_postErrors[] = $this->l("Your phone number is missing");
		if ($collect && $closing == '')
			$this->_postErrors[] = $this->l("Your closing time is missing");
		
		Configuration::updateValue('TNT_CARRIER_SHIPPING_COLLECT', $collect);
		Configuration::updateValue('TNT_CARRIER_SHIPPING_COMPANY', $company);
		Configuration::updateValue('TNT_CARRIER_SHIPPING_LASTNAME', $lname);
		Configuration::updateValue('TNT_CARRIER_SHIPPING_FIRSTNAME', $fname);
		Configuration::updateValue('TNT_CARRIER_SHIPPING_ADDRESS1', $address1);
		Configuration::updateValue('TNT_CARRIER_SHIPPING_ADDRESS2', $address2);
		Configuration::updateValue('TNT_CARRIER_SHIPPING_ZIPCODE', $postal_code);
		Configuration::updateValue('TNT_CARRIER_SHIPPING_CITY', $city);
		Configuration::updateValue('TNT_CARRIER_SHIPPING_EMAIL', $email);
		Configuration::updateValue('TNT_CARRIER_SHIPPING_PHONE', $phone);
		Configuration::updateValue('TNT_CARRIER_SHIPPING_CLOSING', $closing);
		Configuration::updateValue('TNT_CARRIER_SHIPPING_DELIVERY', $delivery);
		Configuration::updateValue('TNT_CARRIER_SHIPPING_PEX', $pex);
		Configuration::updateValue('TNT_CARRIER_PRINT_STICKER', $print);
	}
	
	private function _postValidationService()
	{
		if (Tools::getValue('action') == 'new' && Tools::getValue('service_id') != null )
			$this->_postValidationEditService();
		elseif (Tools::getValue('action') == 'new' && Tools::getValue('service_id') == null)
			$this->_postValidationNewService();
	}
	
	private function _postValidationInfo($cat)
	{
		if (Tools::getValue('action') == 'new' && Tools::getValue($cat.'_id') != null )
			$this->_postValidationEditInfo($cat);
		elseif (Tools::getValue('action') == 'new' && Tools::getValue($cat.'_id') == null)
			$this->_postValidationNewInfo($cat);
	}
	
	private function _postValidationNewService()
	{	
		$name = Tools::getValue('tnt_carrier_service_name');
		$description = Tools::getValue('tnt_carrier_service_description');
		$code = Tools::getValue('tnt_carrier_service_code');
		$charge = Tools::getValue('tnt_carrier_service_charge');
		$display = Tools::getValue('tnt_carrier_service_display');
		
		if ($name == '')
			$this->_postErrors[]  = $this->l('You have to give a name service');
		if ($code == '')
			$this->_postErrors[]  = $this->l('You have to give a code service');
		if ($description == '')
			$this->_postErrors[]  = $this->l('You have to give a description of the service');
		if ($display == '1')
			$delete = false;
		else
			$delete = true;
		
		if (!$this->_postErrors)
		{
			$carrierConfig = array(
				'name' => $name,
				'id_tax_rules_group' => 0,
				'active' => true,
				'deleted' => $delete,
				'shipping_handling' => false,
				'range_behavior' => 0,
				'delay' => array('fr' => $description, 'en' => $description, Language::getIsoById(Configuration::get('PS_LANG_DEFAULT')) => $description),
				'id_zone' => 1,
				'is_module' => true,
				'shipping_external' => true,
				'external_module_name' => $this->_moduleName,
				'need_range' => true
			);
			$id_carrier = $this->installExternalCarrier($carrierConfig);
		
			Db::getInstance()->autoExecute(_DB_PREFIX_.'tnt_carrier_option', 
										array('id_carrier' => (int)($id_carrier), 
										'option' => $code, 
										'additionnal_charges' => (float)($charge)),'INSERT');
			Configuration::updateValue('TNT_CARRIER_'.$code.'_ID', (int)($id_carrier));
			Configuration::updateValue('TNT_CARRIER_'.$code.'_OVERCOST', (float)($charge));
			$this->_fieldsList['TNT_CARRIER_'.$code.'_OVERCOST'] = (float)($charge);
			$this->_fieldsList['TNT_CARRIER_'.$code.'_ID'] = (float)($id_carrier);
			$this->_html .= $this->displayConfirmation($this->l('Service updated'));
		}
	}
	
	private function _postValidationEditService()
	{
		$id = Tools::getValue('service_id');
		$name = Tools::getValue('tnt_carrier_service_name');
		$description = Tools::getValue('tnt_carrier_service_description');
		$code = Tools::getValue('tnt_carrier_service_code');
		$charge = Tools::getValue('tnt_carrier_service_charge');
		$display = Tools::getValue('tnt_carrier_service_display');
		
		if ($name == '')
			$this->_postErrors[]  = $this->l('You have to give a name service');
		if ($code == '')
			$this->_postErrors[]  = $this->l('You have to give a code service');
		if ($description == '')
			$this->_postErrors[]  = $this->l('You have to give a description of the service');
		if ($display == '1')
			$display = '0';
		else	
			$display = '1';
		
		if (!$this->_postErrors)
		{
			Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'carrier` SET `name` = "'.$name.'", `deleted` = "'.(int)($display).'" WHERE `id_carrier` = '.(int)($id).'');
			Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'carrier_lang` SET `delay` = "'.$description.'" WHERE `id_carrier` = '.(int)($id).'');
			Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'tnt_carrier_option` SET `option` = "'.$code.'" WHERE `id_carrier` = '.(int)($id).'');
			Configuration::updateValue('TNT_CARRIER_'.$code.'_OVERCOST', (float)($charge));
			Configuration::updateValue('TNT_CARRIER_'.$code.'_ID', (int)($id));
			$this->_fieldsList['TNT_CARRIER_'.$code.'_OVERCOST'] = (float)($charge);
			$this->_fieldsList['TNT_CARRIER_'.$code.'_ID'] = (float)($id);
			$this->_html .= $this->displayConfirmation($this->l('Service updated'));
		}
	}
	
	private function _postValidationNewInfo($cat)
	{
		$info_min = Tools::getValue('tnt_carrier_'.$cat.'_min');
		$info_max = Tools::getValue('tnt_carrier_'.$cat.'_max');
		$charge = Tools::getValue('tnt_carrier_'.$cat.'_charge');
		Db::getInstance()->autoExecute(_DB_PREFIX_.'tnt_carrier_'.$cat.'', 
										array( 
										''.$cat.'_min' => (float)($info_min),
										''.$cat.'_max' => (float)($info_max),
										'additionnal_charges' => (float)($charge)),'INSERT');									
		$this->_html .= $this->displayConfirmation($this->l('Service updated'));
	}
	
	private function _postValidationEditInfo($cat)
	{
		$id = Tools::getValue($cat.'_id');
		$info_min = Tools::getValue('tnt_carrier_'.$cat.'_min');
		$info_max = Tools::getValue('tnt_carrier_'.$cat.'_max');
		$charge = Tools::getValue('tnt_carrier_'.$cat.'_charge');
		
		Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'tnt_carrier_'.$cat.'` 
									SET `'.$cat.'_min` = "'.(float)($info_min).'",
									`'.$cat.'_max` = "'.(float)($info_max).'",
									`additionnal_charges` = "'.(float)$charge.'"
									WHERE `id_'.$cat.'` = '.(int)($id).'');
									
		$this->_html .= $this->displayConfirmation($this->l('Service updated'));
	}
	
	private function _postValidationCountry()
	{
		$country = Tools::getValue('tnt_carrier_country');
		$overcost = Tools::getValue('tnt_carrier_'.$country.'_overcost');
		
		Configuration::updateValue('TNT_CARRIER_'.strtoupper($country).'_OVERCOST', $overcost);
	}
	
	/*
	** Hook update carrier
	**
	*/
	public function hookextraCarrier($params)
	{		
		global $smarty;
		$id_cart = $params['cart']->id;
		$smarty->assign('id_cart', $id_cart);
		return $this->display( __FILE__, 'tpl/relaisColis.tpl' );
	}
	
	public function hookadminOrder($params)
	{
		global $currentIndex, $smarty;
		$table = 'order';
		$token = Tools::getValue('token');
		$errorShipping = 0;
		
		$carrierName = Db::getInstance()->ExecuteS('SELECT c.external_module_name FROM `'._DB_PREFIX_.'carrier` as c, `'._DB_PREFIX_.'orders` as o WHERE c.id_carrier = o.id_carrier AND o.id_order = "'.(int)($params['id_order']).'"');
		if ($carrierName!= null && $carrierName[0]['external_module_name'] != $this->_moduleName)
			return false;
		if (!Configuration::get('TNT_CARRIER_LOGIN') || !Configuration::get('TNT_CARRIER_PASSWORD') || !Configuration::get('TNT_CARRIER_NUMBER_ACCOUNT'))
		{
			$var = array("error" => $this->l("You don't have a TNT account"),
						'shipping_numbers' => '',
						'sticker' => '');
			$smarty->assign('var', $var);
			return $this->display( __FILE__, 'tpl/shippingNumber.tpl' );
		}
		if (Configuration::get('TNT_CARRIER_SHIPPING_COLLECT'))
		{
			if (!Configuration::get('TNT_CARRIER_SHIPPING_COMPANY') || !Configuration::get('TNT_CARRIER_SHIPPING_ADDRESS1') || !Configuration::get('TNT_CARRIER_SHIPPING_ZIPCODE') || !Configuration::get('TNT_CARRIER_SHIPPING_CITY') || !Configuration::get('TNT_CARRIER_SHIPPING_EMAIL') 
				|| !Configuration::get('TNT_CARRIER_SHIPPING_PHONE') || !Configuration::get('TNT_CARRIER_SHIPPING_CLOSING'))
				$errorShipping = 1;
		}
		else
		{
			if (!Configuration::get('TNT_CARRIER_SHIPPING_PEX') || !Configuration::get('TNT_CARRIER_SHIPPING_ADDRESS1') || !Configuration::get('TNT_CARRIER_SHIPPING_ZIPCODE') || !Configuration::get('TNT_CARRIER_SHIPPING_CITY') || !Configuration::get('TNT_CARRIER_SHIPPING_EMAIL') 
				|| !Configuration::get('TNT_CARRIER_SHIPPING_PHONE'))
				$errorShipping = 1;
		}
		if ($errorShipping)
		{
			$var = array("error" => $this->l("You didn't give a collect address in the TNT module configuration"),
						'shipping_numbers' => '',
						'sticker' => '');
			$smarty->assign('var', $var);
			return $this->display( __FILE__, 'tpl/shippingNumber.tpl' );
		}
		
		$orderInfoTnt = new OrderInfoTnt((int)($params['id_order']));
		$info = $orderInfoTnt->getInfo();
		if (!is_array($info) && $info != false)
			{
				$var = array("error" => $info, "weight" => '',
					"weightHidden" => '1', "date" => '', "dateHidden" => '1', 'currentIndex' => $currentIndex, 'table' => $table, 'token' => $token);
				$smarty->assign('var', $var);
				return $this->display( __FILE__, 'tpl/formerror.tpl' );
			}
		$dataWeight = (int)(Tools::getValue('weightErrorOrder'));
		if ($dataWeight != 0)
			$info[1]['weight'][0] = $dataWeight;
		$pack = new PackageTnt((int)($params['id_order']));
		if ($info[0]['shipping_number'] == '' && $pack->getOrder()->hasBeenShipped())
		{
			$tntWebService = new TntWebService();
			try 
				{
					$package = $tntWebService->getPackage($info);
				} 
			catch( SoapFault $e ) 
				{
					if (strrpos($e->faultstring, "weight"))
						$weightError = 1;
					if (strrpos($e->faultstring, "shippingDate"))
						$dateError = date("Y-m-d");
					$error = $this->l("Problem : ") . $e->faultstring;
					$var = array("error" => $error, "weight" => (isset($weightError) ? $weightError : ''), "weightHidden" => (!isset($weightError) ? $info[1]['weight'] : ''),
								"date" => (isset($dateError) ? $dateError : ''), "dateHidden" => (!isset($dateError) ? $info[2]['delivery_date'] : ''),
								'currentIndex' => $currentIndex, 'table' => $table, 'token' => $token);
					$smarty->assign('var', $var);
					return $this->display( __FILE__, 'tpl/formerror.tpl' );
				}
			catch( Exception $e ) {
					$error = $this->l("Problem : failed");      
				}
			if (isset($package->Expedition->parcelResponses->parcelNumber))
				$pack->setShippingNumber($package->Expedition->parcelResponses->parcelNumber);
			else
				foreach ($package->Expedition->parcelResponses as $k => $v)
					$pack->setShippingNumber($v->parcelNumber);
			file_put_contents("../modules/".$this->_moduleName.'/pdf/'.$pack->getOrder()->shipping_number.'.pdf', $package->Expedition->PDFLabels);
		}
		if ($pack->getShippingNumber() != '')
		{
			$var = array(
				'lang_shippingNumber' => $this->l('The shipping number(s)'), 'lang_sticker' => $this->l('The sticker'), 'lang_expedition' => $this->l('Expedition'),
				'error' => '',
				'shipping_numbers' => $pack->getShippingNumber(),
                'sticker' => "../modules/".$this->_moduleName.'/pdf/'.$pack->getOrder()->shipping_number.'.pdf',
                'date' => $info[2]['delivery_date'],
				'place' => Configuration::get('TNT_CARRIER_SHIPPING_ADDRESS1')." ".Configuration::get('TNT_CARRIER_SHIPPING_ADDRESS2')."<br/>".Configuration::get('TNT_CARRIER_SHIPPING_ZIPCODE')." ".$this->putCityInNormeTnt(Configuration::get('TNT_CARRIER_SHIPPING_CITY')));
			$smarty->assign('var', $var);
			return $this->display( __FILE__, 'tpl/shippingNumber.tpl' );
		}
		return false;
	}
	
	public function hookorderDetailDisplayed($params)
	{
		global $smarty;
		
		$tab = $params['order']->getFields();
		$shipping_number = $tab['shipping_number'];
		$id_carrier = $tab['id_carrier'];
		$erreur = null;
		$follow = array();
		$carrierName = Db::getInstance()->ExecuteS('SELECT external_module_name FROM `'._DB_PREFIX_.'carrier` WHERE `id_carrier` = "'.(int)($id_carrier).'"');
		if ($carrierName != null && $carrierName[0]['external_module_name'] == $this->_moduleName && $shipping_number != '')
		{
			$pack = new PackageTnt($params['order']->id);
			$numbers = $pack->getShippingNumber();
			$smarty->assign('numbers', $numbers);
			return $this->display( __FILE__, 'tpl/waitingFollow.tpl' );
		}
	}
	
	public function hookupdateCarrier($params)
	{
	}

	/*
	** Front Methods
	**
	** If you set need_range at true when you created your carrier (in install method), the method called by the cart will be getOrderShippingCost
	** If not, the method called will be getOrderShippingCostExternal
	**
	** $params var contains the cart, the customer, the address
	** $shipping_cost var contains the price calculated by the range in carrier tab
	**
	*/
	
	public function getOrderShippingCost($params, $shipping_cost)
	{	
		if ((int)(Tools::getValue('step')) > 2)
			serviceCache::deleteServices($params->id);
		$product = $params->getProducts();
		$weight = 0;
		$add = 0;
		$id_customer = $params->id_customer;
		$date_exp = $params->date_upd;
		$id_adress_delivery = $params->id_address_delivery;
		
		foreach($product as $k => $v)
				$weight += (float)($v['weight']);
				
		if ((int)(Tools::getValue('step')) == 2)
		{
			if (!Configuration::get('TNT_CARRIER_LOGIN') || !Configuration::get('TNT_CARRIER_PASSWORD') || !Configuration::get('TNT_CARRIER_NUMBER_ACCOUNT'))
				return false;
			if (!Configuration::get('TNT_CARRIER_SHIPPING_ADDRESS1') || !Configuration::get('TNT_CARRIER_SHIPPING_ZIPCODE') || !Configuration::get('TNT_CARRIER_SHIPPING_CITY'))
				return false;
			$info = Db::getInstance()->ExecuteS('SELECT postcode, city, company FROM `'._DB_PREFIX_.'address` WHERE `id_address` = "'.(int)($id_adress_delivery).'"');
			
			$serviceCache = new serviceCache($params->id, $info[0]['postcode']);
			if (!$serviceCache->getFaisabilityAtThisTime())
			{
				$serviceCache->deletePreviousServices();
				$tntWebService = new TntWebService();
				if (date("N") == 6)
					$date_exp = date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")+2, date("Y")));
				elseif (date("N") == 7)
					$date_exp = date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
				try {
						$service = $tntWebService->faisabilite($date_exp, Configuration::get('TNT_CARRIER_SHIPPING_ZIPCODE'), $this->putCityInNormeTnt(Configuration::get('TNT_CARRIER_SHIPPING_CITY')), 
										$info[0]['postcode'], $this->putCityInNormeTnt($info[0]['city']), ($info[0]['company'] != '' ? "ENTERPRISE" : 'INDIVIDUAL'));
						$serviceRelais = $tntWebService->faisabilite($date_exp, Configuration::get('TNT_CARRIER_SHIPPING_ZIPCODE'),	$this->putCityInNormeTnt(Configuration::get('TNT_CARRIER_SHIPPING_CITY')), 
										$info[0]['postcode'], $this->putCityInNormeTnt($info[0]['city']), "DROPOFFPOINT");
					} 
				catch( SoapFault $e ) {
						$erreur = $this->l("Problem : ") . $e->faultstring;
					}
				catch( Exception $e ) {
						$erreur = $this->l("Problem : follow failed");
					}
				if (!isset($erreur))
					$serviceCache->putInCache($service, $serviceRelais);
			}
			$service = $serviceCache->getServices();
			if ($service != NULL)
				foreach ($service as $v)
					{
						if (Configuration::get('TNT_CARRIER_'.$v['code'].'_ID'))
							if (Configuration::get('TNT_CARRIER_'.$v['code'].'_ID') == $this->id_carrier)
								$priceCarrier = Configuration::get('TNT_CARRIER_'.$v['code'].'_OVERCOST');
					}
		}
		if (!isset($priceCarrier))
			{
				if (isset($params->id_carrier) && (int)($params->id_carrier) > 0)
				{
					if ($option = Db::getInstance()->ExecuteS('SELECT `option` FROM `'._DB_PREFIX_.'tnt_carrier_option` WHERE `id_carrier` = "'.(int)($params->id_carrier).'"'))
						$priceCarrier = Configuration::get('TNT_CARRIER_'.$option[0]['option'].'_OVERCOST');
				}
				else
					$priceCarrier = 0;
			}
		
		$weightLimit = Db::getInstance()->ExecuteS('SELECT additionnal_charges FROM `'._DB_PREFIX_.'tnt_carrier_weight` WHERE `weight_min` < "'.(float)($weight).'" AND `weight_max` > "'.(float)($weight).'"');
		$currency = Db::getInstance()->ExecuteS('SELECT conversion_rate FROM `'._DB_PREFIX_.'currency` WHERE `id_currency` = "'.(int)($params->id_currency).'"');
		if ($weightLimit != null)
			$add += (float)($weightLimit[0]['additionnal_charges']);
		if (substr($info[0]['postcode'], 0, 2) == "20")
			$add += (float)(Configuration::get('TNT_CARRIER_CORSE_OVERCOST'));
		
		if (isset($priceCarrier))
			return (($priceCarrier + $add) * $currency[0]['conversion_rate']);
		return false;
	}
	
	public function getOrderShippingCostExternal($params)
	{
		return getOrderShippingCost($params, null);
	}
	
	public function putCityInNormeTnt($city)
	{
		$city = iconv("utf-8", 'ASCII//TRANSLIT', $city);
		$city = mb_strtoupper($city, 'utf-8');
		$table = array('`' => '','\''=> '', '^' => '','À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B');
		$city = strtr($city, $table);
		$old = array("SAINT", "-");
		$new = array("ST", " ");
		return (str_replace($old, $new, $city));
	}
}