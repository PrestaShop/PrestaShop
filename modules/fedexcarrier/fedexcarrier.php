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


class FedexCarrier extends CarrierModule
{
	public  $id_carrier;

	private $_html = '';
	private $_postErrors = array();
	private $_webserviceTestResult = '';
	private $_webserviceError = '';
	private $_fieldsList = array();
	private $_pickupTypeList = array();
	private $_packagingTypeList = array();
	private $_serviceTypeList = array();
	private $_dimensionUnit = '';
	private $_weightUnit = '';
	private $_dimensionUnitList = array('CM' => 'CM', 'IN' => 'IN', 'CMS' => 'CM', 'INC' => 'IN');
	private $_weightUnitList = array('KG' => 'KGS', 'KGS' => 'KGS', 'LBS' => 'LBS', 'LB' => 'LBS');
	private $_moduleName = 'fedexcarrier';

	public function __construct()
	{
		global $cookie;

		$this->name = 'fedexcarrier';
		$this->tab = 'shipping_logistics';
		$this->version = '0.9c';
		$this->author = 'PrestaShop';
		$this->limited_countries = array('us');

		parent::__construct ();

		$this->displayName = $this->l('Fedex Carrier');
		$this->description = $this->l('Offer your customers, different delivery methods with Fedex');

		if (self::isInstalled($this->name))
		{
			// Loading Var
			$warning = array();
			$this->loadingVar();

			// Check Class Soap availibility
			if (!extension_loaded('soap'))
				$warning[] = "'".$this->l('Class Soap')."', ";

			// Check Configuration Values
			foreach ($this->_fieldsList as $keyConfiguration => $name)
				if (!Configuration::get($keyConfiguration) && !empty($name))
					$warning[] = '\''.$name.'\' ';

			// Checking Unit
			$this->_dimensionUnit = $this->_dimensionUnitList[strtoupper(Configuration::get('PS_DIMENSION_UNIT'))];
			$this->_weightUnit = $this->_weightUnitList[strtoupper(Configuration::get('PS_WEIGHT_UNIT'))];
			if (!$this->_weightUnit || !$this->_weightUnitList[$this->_weightUnit])
				$warning[] = $this->l('\'Weight Unit (LB or KG).\'').' ';
			if (!$this->_dimensionUnit || !$this->_dimensionUnitList[$this->_dimensionUnit])
				$warning[] = $this->l('\'Dimension Unit (CM or IN).\'').' ';

			// Generate Warnings
			if (count($warning))
				$this->warning .= implode(' , ',$warning).$this->l('must be configured to use this module correctly').' ';
		}
	}

	public function loadingVar()
	{
		// Loading Fields List
		$this->_fieldsList = array(
			'FEDEX_CARRIER_ACCOUNT' => $this->l('Fedex account'),
			'FEDEX_CARRIER_METER' => $this->l('Fedex meter'),
			'FEDEX_CARRIER_PASSWORD' => $this->l('Fedex password'),
			'FEDEX_CARRIER_API_KEY' => $this->l('Fedex API Key'),
			'FEDEX_CARRIER_PICKUP_TYPE' => $this->l('Fedex default pickup type'),
			'FEDEX_CARRIER_PACKAGING_TYPE' => $this->l('Fedex default packaging type'),
			'FEDEX_CARRIER_ADDRESS1' => '',
			'FEDEX_CARRIER_ADDRESS2' => '',
			'FEDEX_CARRIER_POSTAL_CODE' => '',
			'FEDEX_CARRIER_CITY' => '',
			'FEDEX_CARRIER_STATE' => '',
			'FEDEX_CARRIER_COUNTRY' => '',
		);

		// Loading pickup type list			
		$this->_pickupTypeList = array(
			'BUSINESS_SERVICE_CENTER' => $this->l('Business service center'),
			'DROP_BOX' => $this->l('Drop box'),
			'REGULAR_PICKUP' => $this->l('Regular pickup'),
			'REQUEST_COURIER' => $this->l('Request courier'),
			'STATION' => $this->l('Station')
		);

		// Loading packaging type list			
		$this->_packagingTypeList = array(
			'FEDEX_10KG_BOX' => $this->l('Fedex 10Kg Box'),
			'FEDEX_25KG_BOX' => $this->l('Fedex 25Kg Box'),
			'FEDEX_BOX' => $this->l('Fedex Box'),
			'FEDEX_ENVELOPE' => $this->l('Fedex Envelope'),
			'FEDEX_PAK' => $this->l('Fedex Pak'),
			'FEDEX_TUBE' => $this->l('Fedex Tube'),
			'YOUR_PACKAGING' => $this->l('Your Packaging'),
		);

		// Loading service type list
		$this->_serviceTypeList = array(
			'EUROPE_FIRST_INTERNATIONAL_PRIORITY' => $this->l('Europe first international priority'),
			'FEDEX_1_DAY_FREIGHT' => $this->l('Fedex 1 day freight'),
			'FEDEX_2_DAY' => $this->l('Fedex 2 day'),
			'FEDEX_2_DAY_FREIGHT' => $this->l('Fedex 2 day freight'),
			'FEDEX_3_DAY_FREIGHT' => $this->l('Fedex 3 day freight'),
			'FEDEX_EXPRESS_SAVER' => $this->l('Fedex express saver'),
			'FEDEX_FREIGHT' => $this->l('Fedex freight'),
			'FEDEX_GROUND' => $this->l('Fedex ground'),
			'FEDEX_NATIONAL_FREIGHT' => $this->l('Fedex national freight'),
			'FIRST_OVERNIGHT' => $this->l('First overnight'),
			'GROUND_HOME_DELIVERY' => $this->l('Ground home delivery'),
			'INTERNATIONAL_ECONOMY' => $this->l('International economy'),
			'INTERNATIONAL_ECONOMY_FREIGHT' => $this->l('International economy freight'),
			'INTERNATIONAL_FIRST' => $this->l('International first'),
			'INTERNATIONAL_GROUND' => $this->l('International ground'),
			'INTERNATIONAL_PRIORITY' => $this->l('International priority'),
			'INTERNATIONAL_PRIORITY_FREIGHT' => $this->l('International priority freight'),
			'PRIORITY_OVERNIGHT' => $this->l('Priority overnight'),
			'SMART_POST' => $this->l('Smart post'),
			'STANDARD_OVERNIGHT' => $this->l('Standard overnight')
		);
	}



	/*
	** Install / Uninstall Methods
	**
	*/

	public function install()
	{
		global $cookie;

		// Install SQL
		include(dirname(__FILE__).'/sql-install.php');
		foreach ($sql as $s)
			if (!Db::getInstance()->Execute($s))
				return false;

		// Install Carriers
		$this->installCarriers();

		// Install Module
		if (!parent::install() OR !$this->registerHook('updateCarrier'))
			return false;

		return true;
	}

	public function uninstall()
	{
		global $cookie;

		// Uninstall Carriers
		Db::getInstance()->autoExecute(_DB_PREFIX_.'carrier', array('deleted' => 1), 'UPDATE', '`external_module_name` = \'fedexcarrier\' OR `id_carrier` IN (SELECT DISTINCT(`id_carrier`) FROM `'._DB_PREFIX_.'fedex_rate_service_code`)');

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

	public function installCarriers()
	{
		// Unactive all FEDEX Carriers
		Db::getInstance()->autoExecute(_DB_PREFIX_.'fedex_rate_service_code', array('active' => 0), 'UPDATE');

		// Get all services availables
		$rateServiceList = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'fedex_rate_service_code`');
		foreach ($rateServiceList as $rateService)
			if (!$rateService['id_carrier'])
			{
				$config = array(
					'name' => $rateService['service'],
					'id_tax_rules_group' => 0,
					'active' => true,
					'deleted' => 0,
					'shipping_handling' => false,
					'range_behavior' => 0,
					'delay' => array('fr' => $rateService['service'], 'en' => $rateService['service']),
					'id_zone' => 1,
					'is_module' => true,
					'shipping_external' => true,
					'external_module_name' => $this->_moduleName,
					'need_range' => true
				);
				$id_carrier = $this->installExternalCarrier($config);
				Db::getInstance()->autoExecute(_DB_PREFIX_.'fedex_rate_service_code', array('id_carrier' => (int)($id_carrier), 'id_carrier_history' => (int)($id_carrier)), 'UPDATE', '`id_fedex_rate_service_code` = '.(int)($rateService['id_fedex_rate_service_code']));
			}
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
				Db::getInstance()->autoExecuteWithNullValues(_DB_PREFIX_.'delivery', array('id_carrier' => (int)($carrier->id), 'id_range_price' => (int)($rangePrice->id), 'id_range_weight' => NULL, 'id_zone' => (int)($zone['id_zone']), 'price' => '0'), 'INSERT');
				Db::getInstance()->autoExecuteWithNullValues(_DB_PREFIX_.'delivery', array('id_carrier' => (int)($carrier->id), 'id_range_price' => NULL, 'id_range_weight' => (int)($rangeWeight->id), 'id_zone' => (int)($zone['id_zone']), 'price' => '0'), 'INSERT');
			}

			// Copy Logo
			if (!copy(dirname(__FILE__).'/carrier.jpg', _PS_SHIP_IMG_DIR_.'/'.(int)$carrier->id.'.jpg'))
				return false;

			// Return ID Carrier
			return (int)($carrier->id);
		}

		return false;
	}



	/*
	** Global Form Config Methods
	**
	*/

	public function getContent()
	{
		$this->_html .= '<h2>' . $this->l('FEDEX Carrier').'</h2>';
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
		$this->_html .= '<fieldset>
		<legend><img src="'.$this->_path.'logo.gif" alt="" /> '.$this->l('Fedex Module Status').'</legend>';

		$alert = array();
		$this->_webserviceTestResult = $this->webserviceTest();
		if (!Configuration::get('FEDEX_CARRIER_ACCOUNT'))
			$alert['generalSettings'] = 1;
		if (Db::getInstance()->getValue('SELECT * FROM `'._DB_PREFIX_.'fedex_rate_service_code` WHERE `active` = 1') < 1)
			$alert['deliveryServices'] = 1;
		if (!$this->_webserviceTestResult)
			$alert['webserviceTest'] = 1;
		if (!extension_loaded('soap'))
			$alert['soap'] = 1;


		if (!count($alert))
			$this->_html .= '<img src="'._PS_IMG_.'admin/module_install.png" /><strong>'.$this->l('FEDEX Carrier is configured and online!').'</strong>';
		else
		{
			$this->_html .= '<img src="'._PS_IMG_.'admin/warn2.png" /><strong>'.$this->l('FEDEX Carrier is not configured yet, you must:').'</strong>';
			$this->_html .= '<br />'.(isset($alert['generalSettings']) ? '<img src="'._PS_IMG_.'admin/warn2.png" />' : '<img src="'._PS_IMG_.'admin/module_install.png" />').' 1) '.$this->l('Fill the "General Settings" form');
			$this->_html .= '<br />'.(isset($alert['deliveryServices']) ? '<img src="'._PS_IMG_.'admin/warn2.png" />' : '<img src="'._PS_IMG_.'admin/module_install.png" />').' 2) '.$this->l('Select your available delivery service');
			$this->_html .= '<br />'.(isset($alert['webserviceTest']) ? '<img src="'._PS_IMG_.'admin/warn2.png" />' : '<img src="'._PS_IMG_.'admin/module_install.png" />').' 3) '.$this->l('Webservice test connection').($this->_webserviceError ? ' : '.$this->_webserviceError : '');
			$this->_html .= '<br />'.(isset($alert['soap']) ? '<img src="'._PS_IMG_.'admin/warn2.png" />' : '<img src="'._PS_IMG_.'admin/module_install.png" />').' 4) '.$this->l('Soap is enabled');
		}


		$this->_html .= '</fieldset><div class="clear">&nbsp;</div>';
		$this->_html .= $this->_displayFormConfig();
	}

	private function _postValidation()
	{
		if (Tools::getValue('section') == 'general')
			$this->_postValidationGeneral();
		elseif (Tools::getValue('section') == 'category')
			$this->_postValidationCategory();
		elseif (Tools::getValue('section') == 'product')
			$this->_postValidationProduct();
	}

	private function _postProcess()
	{
		if (Tools::getValue('section') == 'general')
			$this->_postProcessGeneral();
		elseif (Tools::getValue('section') == 'category')
			$this->_postProcessCategory();
		elseif (Tools::getValue('section') == 'product')
			$this->_postProcessProduct();
	}




	/*
	** General Form Config Methods
	**
	*/

	private function _displayFormConfig()
	{
		$html = '
		<ul id="menuTab">
				<li id="menuTab1" class="menuTabButton selected">1. '.$this->l('General Settings').'</li>
				<li id="menuTab2" class="menuTabButton">2. '.$this->l('Categories Settings').'</li>
				<li id="menuTab3" class="menuTabButton">3. '.$this->l('Products Settings').'</li>
				<li id="menuTab4" class="menuTabButton">4. '.$this->l('Help').'</li>
			</ul>
			<div id="tabList">
				<div id="menuTab1Sheet" class="tabItem selected">'.$this->_displayFormGeneral().'</div>
				<div id="menuTab2Sheet" class="tabItem">'.$this->_displayFormCategory().'</div>
				<div id="menuTab3Sheet" class="tabItem">'.$this->_displayFormProduct().'</div>
				<div id="menuTab4Sheet" class="tabItem">'.$this->_displayHelp().'</div>
			</div>
			<br clear="left" />
			<br />
			<style>
				#menuTab { float: left; padding: 0; margin: 0; text-align: left; }
				#menuTab li { text-align: left; float: left; display: inline; padding: 5px; padding-right: 10px; background: #EFEFEF; font-weight: bold; cursor: pointer; border-left: 1px solid #EFEFEF; border-right: 1px solid #EFEFEF; border-top: 1px solid #EFEFEF; }
				#menuTab li.menuTabButton.selected { background: #FFF6D3; border-left: 1px solid #CCCCCC; border-right: 1px solid #CCCCCC; border-top: 1px solid #CCCCCC; }
				#tabList { clear: left; }
				.tabItem { display: none; }
				.tabItem.selected { display: block; background: #FFFFF0; border: 1px solid #CCCCCC; padding: 10px; padding-top: 20px; }
			</style>
			<script>
				$(".menuTabButton").click(function () {
				  $(".menuTabButton.selected").removeClass("selected");
				  $(this).addClass("selected");
				  $(".tabItem.selected").removeClass("selected");
				  $("#" + this.id + "Sheet").addClass("selected");
				});
			</script>
		';
		if (isset($_GET['id_tab']))
			$html .= '<script>
				  $(".menuTabButton.selected").removeClass("selected");
				  $("#menuTab'.$_GET['id_tab'].'").addClass("selected");
				  $(".tabItem.selected").removeClass("selected");
				  $("#menuTab'.$_GET['id_tab'].'Sheet").addClass("selected");
			</script>';
		return $html;
	}

	private function _displayFormGeneral()
	{
		global $cookie;

		$html = '<script>
			$(document).ready(function() {
				var country = $("#fedex_carrier_country");
				country.change(function() {
					if ($("#fedex_carrier_state_" + country.val()))
					{
						$(".stateInput.selected").removeClass("selected");
						if ($("#fedex_carrier_state_" + country.val()).size())
							$("#fedex_carrier_state_" + country.val()).addClass("selected");
						else
							$("#fedex_carrier_state_none").addClass("selected");
					}
				});

				$("#configForm").submit(function() {
					$("#fedex_carrier_state").val($(".stateInput.selected").val());
				});
			});
			</script>
			<style>
				.stateInput { display: none; }
				.stateInput.selected { display: block; }
			</style>


			<form action="index.php?tab='.$_GET['tab'].'&configure='.$_GET['configure'].'&token='.$_GET['token'].'&tab_module='.$_GET['tab_module'].'&module_name='.$_GET['module_name'].'&id_tab=1&section=general" method="post" class="form" id="configForm">

				<fieldset style="border: 0px;">
					<h4>'.$this->l('General configuration').' :</h4>
					<label>'.$this->l('Your Fedex account').' : </label>
					<div class="margin-form"><input type="text" size="20" name="fedex_carrier_account" value="'.Tools::getValue('fedex_carrier_account', Configuration::get('FEDEX_CARRIER_ACCOUNT')).'" /></div>
					<label>'.$this->l('Your Fedex meter number').' : </label>
					<div class="margin-form"><input type="text" size="20" name="fedex_carrier_meter" value="'.Tools::getValue('fedex_carrier_meter', Configuration::get('FEDEX_CARRIER_METER')).'" /></div>
					<label>'.$this->l('Your Fedex password').' : </label>
					<div class="margin-form"><input type="text" size="20" name="fedex_carrier_password" value="'.Tools::getValue('fedex_carrier_password', Configuration::get('FEDEX_CARRIER_PASSWORD')).'" /></div>
					<label>'.$this->l('Your Fedex API Key').' : </label>
					<div class="margin-form">
						<input type="text" size="20" name="fedex_carrier_api_key" value="'.Tools::getValue('fedex_carrier_api_key', Configuration::get('FEDEX_CARRIER_API_KEY')).'" />
						<p><a href="http://www.fedex.com/webtools/" target="_blank">' . $this->l('Please click here to get your Fedex API Key.') . '</a></p>
					</div>
				</fieldset>

				<fieldset style="border: 0px;">
					<h4>'.$this->l('Localization configuration').' :</h4>
					<label>'.$this->l('Weight unit').' : </label>
					<div class="margin-form">
						<input type="text" size="20" name="ps_weight_unit" value="'.Tools::getValue('ps_weight_unit', Configuration::get('PS_WEIGHT_UNIT')).'" />
						<p>'.$this->l('The weight unit of your shop (eg. kg or lbs)').'</p>
					</div>
					<label>'.$this->l('Dimension unit').' : </label>
					<div class="margin-form">
						<input type="text" size="20" name="ps_dimension_unit" value="'.Tools::getValue('ps_dimension_unit', Configuration::get('PS_DIMENSION_UNIT')).'" />
						<p>'.$this->l('The dimension unit of your shop (eg. cm or in)').'</p>
					</div>
				</fieldset>

				<fieldset style="border: 0px;">
					<h4>'.$this->l('Address configuration').' :</h4>
					<label>'.$this->l('Your address line 1').' : </label>
					<div class="margin-form"><input type="text" size="20" name="fedex_carrier_address1" value="'.Tools::getValue('fedex_carrier_address1', Configuration::get('FEDEX_CARRIER_ADDRESS1')).'" /></div>
					<label>'.$this->l('Your address line 2').' : </label>
					<div class="margin-form"><input type="text" size="20" name="fedex_carrier_address2" value="'.Tools::getValue('fedex_carrier_address2', Configuration::get('FEDEX_CARRIER_ADDRESS2')).'" /></div>
					<label>'.$this->l('Zip / Postal Code').' : </label>
					<div class="margin-form"><input type="text" size="20" name="fedex_carrier_postal_code" value="'.Tools::getValue('fedex_carrier_postal_code', Configuration::get('FEDEX_CARRIER_POSTAL_CODE')).'" /></div><br />
					<label>'.$this->l('Your City').' : </label>
					<div class="margin-form"><input type="text" size="20" name="fedex_carrier_city" value="'.Tools::getValue('fedex_carrier_city', Configuration::get('FEDEX_CARRIER_CITY')).'" /></div>
					<label>'.$this->l('Country').' : </label>
					<div class="margin-form">
						<select name="fedex_carrier_country" id="fedex_carrier_country">
							<option value="0">'.$this->l('Select a country ...').'</option>';
							$idcountries = array();
							foreach (Country::getCountries($cookie->id_lang) as $v)
							{
								$html .= '<option value="'.$v['id_country'].'" '.($v['id_country'] == (int)(Tools::getValue('fedex_carrier_country', Configuration::get('FEDEX_CARRIER_COUNTRY'))) ? 'selected="selected"' : '').'>'.$v['name'].'</option>';
								$idcountries[] = $v['id_country'];
							}
						$html .= '</select>
						<p>' . $this->l('Select country from within the list.') . '</p>
					</div>
					<label>'.$this->l('State').' : </label>
					<div class="margin-form">';
						$id_country_current = 0;
						$statesList = Db::getInstance()->ExecuteS('
						SELECT `id_state`, `id_country`, `name`
						FROM `'._DB_PREFIX_.'state` WHERE `active` = 1
						ORDER BY `id_country`, `name` ASC');
						foreach ($statesList as $v)
						{
							if ($id_country_current != $v['id_country'])
							{
								if ($id_country_current != 0)
									$html .= '</select>';
								$html .= '<select id="fedex_carrier_state_'.$v['id_country'].'" class="stateInput">
									<option value="0">'.$this->l('Select a state ...').'</option>';
							}
							$html .= '<option value="'.$v['id_state'].'" '.($v['id_state'] == (int)(Tools::getValue('fedex_carrier_state', Configuration::get('FEDEX_CARRIER_STATE'))) ? 'selected="selected"' : '').'>'.$v['name'].'</option>';		
							$id_country_current = $v['id_country'];
						}
						$html .= '</select><div id="fedex_carrier_state_none" class="stateInput selected">'.$this->l('There is no state configuration for this country').'</div>
						<input type="hidden" id="fedex_carrier_state" name="fedex_carrier_state" value="s" />
					</div>
				</fieldset>

				<fieldset style="border: 0px;">
					<h4>'.$this->l('Service configuration').' :</h4>
					<label>'.$this->l('Default pickup type').' : </label>
						<div class="margin-form">
						<select name="fedex_carrier_pickup_type">
							<option value="0">'.$this->l('Select a default pickup type ...').'</option>';
							foreach($this->_pickupTypeList as $kpickup => $vpickup)
								$html .= '<option value="'.$kpickup.'" '.($kpickup == pSQL(Configuration::get('FEDEX_CARRIER_PICKUP_TYPE')) ? 'selected="selected"' : '').'>'.$vpickup.'</option>';
					$html .= '</select>
					</div>
					<label>'.$this->l('Default packaging type').' : </label>
						<div class="margin-form">
						<select name="fedex_carrier_packaging_type">
							<option value="0">'.$this->l('Select a default packaging type ...').'</option>';
							foreach($this->_packagingTypeList as $kpackaging => $vkpackaging)
								$html .= '<option value="'.$kpackaging.'" '.($kpackaging == pSQL(Configuration::get('FEDEX_CARRIER_PACKAGING_TYPE')) ? 'selected="selected"' : '').'>'.$vkpackaging.'</option>';
					$html .= '</select>
					</div>
					<label>'.$this->l('Delivery Service').' : </label>
					<div class="margin-form">';
						$rateServiceList = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'fedex_rate_service_code`');
						foreach($rateServiceList as $rateService)
							$html .= '<input type="checkbox" name="service[]" value="'.$rateService['id_fedex_rate_service_code'].'" '.(($rateService['active'] == 1) ? 'checked="checked"' : '').' /> '.$rateService['service'].' '.($this->webserviceTest($rateService['code']) ? '('.$this->l('Available').')' : '('.$this->l('Not available').')').'<br />';
					$html .= '
					<p>' . $this->l('Choose the delivery service which will be available for customers.') . '</p>
					</div>
				</fieldset>
				
				<div class="margin-form"><input class="button" name="submitSave" type="submit"></div>
			</form>

			<script>
				var id_country = '.(int)(Tools::getValue('fedex_carrier_country', Configuration::get('FEDEX_CARRIER_COUNTRY'))).';
				if ($("#fedex_carrier_state_" + id_country))
				{
					$(".stateInput.selected").removeClass("selected");
					if ($("#fedex_carrier_state_" + id_country).size())
						$("#fedex_carrier_state_" + id_country).addClass("selected");
					else
						$("#fedex_carrier_state_none").addClass("selected");
				}
			</script>';
		return $html;
	}

	private function _postValidationGeneral()
	{
		// Check configuration values
		if (Tools::getValue('fedex_carrier_account') == NULL)
			$this->_postErrors[]  = $this->l('Your Fedex account is not specified');
		elseif (Tools::getValue('fedex_carrier_meter') == NULL)
			$this->_postErrors[]  = $this->l('Your Fedex meter is not specified');
		elseif (Tools::getValue('fedex_carrier_password') == NULL)
			$this->_postErrors[]  = $this->l('Your Fedex password is not specified');
		elseif (Tools::getValue('fedex_carrier_api_key') == NULL)
			$this->_postErrors[]  = $this->l('Your Fedex API Key is not specified');
		elseif (Tools::getValue('fedex_carrier_pickup_type') == NULL OR Tools::getValue('fedex_carrier_pickup_type') == '0')
			$this->_postErrors[]  = $this->l('Your pickup type is not specified');
		elseif (Tools::getValue('fedex_carrier_packaging_type') == NULL OR Tools::getValue('fedex_carrier_packaging_type') == '0')
			$this->_postErrors[]  = $this->l('Your packaging type is not specified');
		elseif (Tools::getValue('fedex_carrier_postal_code') == NULL)
			$this->_postErrors[]  = $this->l('Your Zip / Postal code is not specified');
		elseif (Tools::getValue('fedex_carrier_city') == NULL)
			$this->_postErrors[]  = $this->l('Your city is not specified');
		elseif (Tools::getValue('fedex_carrier_country') == NULL OR Tools::getValue('fedex_carrier_country') == 0)
			$this->_postErrors[]  = $this->l('Your country is not specified');



		// Check fedex webservice availibity
		if (!$this->_postErrors)
		{
			// Unactive all FEDEX Carriers
			Db::getInstance()->autoExecute(_DB_PREFIX_.'fedex_rate_service_code', array('active' => 0), 'UPDATE');

			// If no errors appear, the carrier is being activated, else, the carrier is being deactivated
			if (!$this->_postErrors)
			{
				// Get available services
				$serviceSelected = Tools::getValue('service');

				// Active available carrier
				if ($serviceSelected)
					foreach ($serviceSelected as $ss)
					{
						$id_carrier = Db::getInstance()->getValue('SELECT `id_carrier` FROM `'._DB_PREFIX_.'fedex_rate_service_code` WHERE `id_fedex_rate_service_code` = '.(int)($ss));
						Db::getInstance()->autoExecute(_DB_PREFIX_.'fedex_rate_service_code', array('active' => 1), 'UPDATE', '`id_fedex_rate_service_code` = '.(int)($ss));
					}
			}

			// All new configurations values are saved to be sure to test webservices with it
			Configuration::updateValue('FEDEX_CARRIER_ACCOUNT', Tools::getValue('fedex_carrier_account'));
			Configuration::updateValue('FEDEX_CARRIER_METER', Tools::getValue('fedex_carrier_meter'));
			Configuration::updateValue('FEDEX_CARRIER_PASSWORD', Tools::getValue('fedex_carrier_password'));
			Configuration::updateValue('FEDEX_CARRIER_API_KEY', Tools::getValue('fedex_carrier_api_key'));
			Configuration::updateValue('FEDEX_CARRIER_PICKUP_TYPE', Tools::getValue('fedex_carrier_pickup_type'));
			Configuration::updateValue('FEDEX_CARRIER_PACKAGING_TYPE', Tools::getValue('fedex_carrier_packaging_type'));
			Configuration::updateValue('FEDEX_CARRIER_ADDRESS1', Tools::getValue('fedex_carrier_address1'));
			Configuration::updateValue('FEDEX_CARRIER_ADDRESS2', Tools::getValue('fedex_carrier_address2'));
			Configuration::updateValue('FEDEX_CARRIER_POSTAL_CODE', Tools::getValue('fedex_carrier_postal_code'));
			Configuration::updateValue('FEDEX_CARRIER_CITY', Tools::getValue('fedex_carrier_city'));
			Configuration::updateValue('FEDEX_CARRIER_STATE', Tools::getValue('fedex_carrier_state'));
			Configuration::updateValue('FEDEX_CARRIER_COUNTRY', Tools::getValue('fedex_carrier_country'));
			Configuration::updateValue('PS_WEIGHT_UNIT', $this->_weightUnitList[strtoupper(Tools::getValue('ps_weight_unit'))]);
			Configuration::updateValue('PS_DIMENSION_UNIT', $this->_dimensionUnitList[strtoupper(Tools::getValue('ps_dimension_unit'))]);
			if (isset($this->_weightUnitList[strtoupper(Tools::getValue('ps_weight_unit'))]))
				$this->_weightUnit = $this->_weightUnitList[strtoupper(Tools::getValue('ps_weight_unit'))];
			if (isset($this->_dimensionUnitList[strtoupper(Tools::getValue('ps_dimension_unit'))]))
				$this->_dimensionUnit = $this->_dimensionUnitList[strtoupper(Tools::getValue('ps_dimension_unit'))];
			if (!$this->webserviceTest())
				$this->_postErrors[]  = $this->l('Prestashop could not connect to FEDEX webservices').' :<br />'.($this->_webserviceError ? $this->_webserviceError : $this->l('No error description found'));
		}
	}

	private function _postProcessGeneral()
	{
		// Saving new configurations
		if (Configuration::updateValue('FEDEX_CARRIER_ACCOUNT', Tools::getValue('fedex_carrier_account')) AND
			Configuration::updateValue('FEDEX_CARRIER_METER', Tools::getValue('fedex_carrier_meter')) AND
			Configuration::updateValue('FEDEX_CARRIER_PASSWORD', Tools::getValue('fedex_carrier_password')) AND
			Configuration::updateValue('FEDEX_CARRIER_API_KEY', Tools::getValue('fedex_carrier_api_key')) AND
			Configuration::updateValue('FEDEX_CARRIER_PICKUP_TYPE', Tools::getValue('fedex_carrier_pickup_type')) AND
			Configuration::updateValue('FEDEX_CARRIER_PACKAGING_TYPE', Tools::getValue('fedex_carrier_packaging_type')) AND
			Configuration::updateValue('FEDEX_CARRIER_POSTAL_CODE', Tools::getValue('fedex_carrier_postal_code')) AND
			Configuration::updateValue('FEDEX_CARRIER_CITY', Tools::getValue('fedex_carrier_city')) AND
			Configuration::updateValue('FEDEX_CARRIER_STATE', Tools::getValue('fedex_carrier_state')) AND
			Configuration::updateValue('FEDEX_CARRIER_COUNTRY', Tools::getValue('fedex_carrier_country')) AND
			Configuration::updateValue('PS_WEIGHT_UNIT', $this->_weightUnitList[strtoupper(Tools::getValue('ps_weight_unit'))]) AND
			Configuration::updateValue('PS_DIMENSION_UNIT', $this->_dimensionUnitList[strtoupper(Tools::getValue('ps_dimension_unit'))]))
			$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
		else
			$this->_html .= $this->displayErrors($this->l('Settings failed'));
	}



	/*
	** Category Form Config Methods
	**
	*/

	private function _getPathInTab($id_category)
	{
		global $cookie;

		$category = Db::getInstance()->getRow('
		SELECT id_category, level_depth, nleft, nright
		FROM '._DB_PREFIX_.'category
		WHERE id_category = '.(int)$id_category);

		if (isset($category['id_category']))
		{
			$categories = Db::getInstance()->ExecuteS('
			SELECT c.id_category, cl.name, cl.link_rewrite
			FROM '._DB_PREFIX_.'category c
			LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = c.id_category)
			WHERE c.nleft <= '.(int)$category['nleft'].' AND c.nright >= '.(int)$category['nright'].' AND cl.id_lang = '.(int)($cookie->id_lang).'
			ORDER BY c.level_depth ASC
			LIMIT '.(int)($category['level_depth'] + 1));

			$n = 1;
			$pathTab = array();
			$nCategories = (int)sizeof($categories);
			foreach ($categories AS $category)
				$pathTab[] = htmlentities($category['name'], ENT_NOQUOTES, 'UTF-8');
		
			return $pathTab;
		}
	}
	
	private function _getChildCategories($categories, $id, $path = array(), $pathAdd = '')
	{
		$html = '';
		if ($pathAdd != '')
			$path[] = $pathAdd;
		if (isset($categories[$id]))
			foreach ($categories[$id] as $idc => $cc)
			{
				$html .= '<option value="'.$cc['infos']['id_category'].'" '.($cc['infos']['id_category'] == (int)(Tools::getValue('id_category')) ? 'selected="selected"' : '').'>';
				if ($path)
					foreach ($path as $p)
						$html .= $p.' > ';
				$html .= $cc['infos']['name'];
				$html .= '</option>';
				$html .= $this->_getChildCategories($categories, $idc, $path, $cc['infos']['name']);
			}
		return $html;
	}

	private function _isPostCheck($id_fedex_rate_service_code)
	{
		$services = Tools::getValue('service');
		if ($services)
			foreach ($services as $s)
				if ($s == $id_fedex_rate_service_code)
					return 1;
		return 0;
	}
	
	private function _displayFormCategory()
	{
		global $cookie;

		// Check if the module is configured
		if (!$this->_webserviceTestResult)
			return '<p><b>'.$this->l('You have to configure "General Settings" tab before using this tab.').'</b></p><br />';

		// Display header
		$html = '<p><b>'.$this->l('In this tab, you can set a specific configuration for each category.').'</b></p><br />
		<table class="table tableDnD" cellpadding="0" cellspacing="0" width="90%">
			<thead>
				<tr class="nodrag nodrop">
					<th>'.$this->l('ID Config').'</th>
					<th>'.$this->l('Category').'</th>
					<th>'.$this->l('Pickup type').'</th>
					<th>'.$this->l('Packaging type').'</th>
					<th>'.$this->l('Additional charges').'</th>
					<th>'.$this->l('Services').'</th>
					<th>'.$this->l('Actions').'</th>
				</tr>
			</thead>
			<tbody>';

		// Loading config list
		$configCategoryList = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'fedex_rate_config` WHERE `id_category` > 0');
		if (!$configCategoryList)
			$html .= '<tr><td colspan="6">'.$this->l('There is no specific FEDEX configuration for categories at this point.').'</td></tr>';
		foreach ($configCategoryList as $k => $c)
		{
			// Category Path
			$path = '';
			$pathTab = $this->_getPathInTab($c['id_category']);
			foreach ($pathTab as $p)
			{
				if (!empty($path)) { $path .= ' > '; }
				$path .= $p;
			}

			// Loading config currency
			$configCurrency = new Currency($c['id_currency']);

			// Loading services attached to this config
			$services = '';
			$servicesTab = Db::getInstance()->ExecuteS('
			SELECT ursc.`service`
			FROM `'._DB_PREFIX_.'fedex_rate_config_service` urcs
			LEFT JOIN `'._DB_PREFIX_.'fedex_rate_service_code` ursc ON (ursc.`id_fedex_rate_service_code` = urcs.`id_fedex_rate_service_code`)
			WHERE urcs.`id_fedex_rate_config` = '.(int)$c['id_fedex_rate_config']);
			foreach ($servicesTab as $s)
				$services .= $s['service'].'<br />';

			// Display line
			$alt = 0;
			if ($k % 2 != 0)
				$alt = ' class="alt_row"';
			$html .= '
				<tr'.$alt.'>
					<td>'.$c['id_fedex_rate_config'].'</td>
					<td>'.$path.'</td>
					<td>'.(isset($this->_pickupTypeList[$c['pickup_type_code']]) ? $this->_pickupTypeList[$c['pickup_type_code']] : '-').'</td>
					<td>'.(isset($this->_packagingTypeList[$c['packaging_type_code']]) ? $this->_packagingTypeList[$c['packaging_type_code']] : '-').'</td>
					<td>'.$c['additional_charges'].' '.$configCurrency->sign.'</td>
					<td>'.$services.'</td>
					<td>
						<a href="index.php?tab='.$_GET['tab'].'&configure='.$_GET['configure'].'&token='.$_GET['token'].'&tab_module='.$_GET['tab_module'].'&module_name='.$_GET['module_name'].'&id_tab=2&section=category&action=edit&id_fedex_rate_config='.(int)($c['id_fedex_rate_config']).'" style="float: left;">
							<img src="'._PS_IMG_.'admin/edit.gif" />
						</a>
						<form action="index.php?tab='.$_GET['tab'].'&configure='.$_GET['configure'].'&token='.$_GET['token'].'&tab_module='.$_GET['tab_module'].'&module_name='.$_GET['module_name'].'&id_tab=2&section=category&action=delete&id_fedex_rate_config='.(int)($c['id_fedex_rate_config']).'&id_category='.(int)($c['id_category']).'" method="post" class="form" style="float: left;">
							<input name="submitSave" type="image" src="'._PS_IMG_.'admin/delete.gif" OnClick="return confirm(\''.$this->l('Are you sure you want to delete this specific FEDEX configuration for this category ?').'\');" />
						</form>
					</td>
				</tr>';
		}

		$html .= '
			</tbody>
		</table><br /><br />';

		// Add or Edit Category Configuration
		if (Tools::getValue('action') == 'edit' && Tools::getValue('section') == 'category')
		{
			// Loading config
			$configSelected = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'fedex_rate_config` WHERE `id_fedex_rate_config` = '.(int)(Tools::getValue('id_fedex_rate_config')));
			
			// Category Path
			$path = '';
			$pathTab = $this->_getPathInTab($configSelected['id_category']);
			foreach ($pathTab as $p)
			{
				if (!empty($path)) { $path .= ' > '; }
				$path .= $p;
			}

			$html .= '<p align="center"><b>'.$this->l('Update a rule').' (<a href="index.php?tab='.$_GET['tab'].'&configure='.$_GET['configure'].'&token='.$_GET['token'].'&tab_module='.$_GET['tab_module'].'&module_name='.$_GET['module_name'].'&id_tab=2&section=category&action=add">'.$this->l('Add a rule').' ?</a>)</b></p>
					<form action="index.php?tab='.$_GET['tab'].'&configure='.$_GET['configure'].'&token='.$_GET['token'].'&tab_module='.$_GET['tab_module'].'&module_name='.$_GET['module_name'].'&id_tab=2&section=category&action=edit&id_fedex_rate_config='.(int)(Tools::getValue('id_fedex_rate_config')).'" method="post" class="form">
						<label>'.$this->l('Category').' :</label>
						<div class="margin-form" style="padding: 0.2em 0.5em 0 0; font-size: 12px;">'.$path.' <input type="hidden" name="id_category" value="'.(int)($configSelected['id_category']).'" /></div><br clear="left" />
						<label>'.$this->l('Pickup Type').' : </label>
							<div class="margin-form">
								<select name="pickup_type_code">
									<option value="0">'.$this->l('Select a pickup type ...').'</option>';
									foreach($this->_pickupTypeList as $kpickup => $vpickup)
										$html .= '<option value="'.$kpickup.'" '.($kpickup == pSQL(Tools::getValue('pickup_type_code', $configSelected['pickup_type_code'])) ? 'selected="selected"' : '').'>'.$vpickup.'</option>';
						$html .= '</select>
						</div>
						<label>'.$this->l('Packaging Type').' : </label>
							<div class="margin-form">
								<select name="packaging_type_code">
									<option value="0">'.$this->l('Select a packaging type ...').'</option>';
									foreach($this->_packagingTypeList as $kpackaging => $vpackaging)
										$html .= '<option value="'.$kpackaging.'" '.($kpackaging == pSQL(Tools::getValue('packaging_type_code', $configSelected['packaging_type_code'])) ? 'selected="selected"' : '').'>'.$vpackaging.'</option>';
						$html .= '</select>
						</div>
						<label>'.$this->l('Additional charges').' : </label>
						<div class="margin-form"><input type="text" size="20" name="additional_charges" value="'.Tools::getValue('additional_charges', $configSelected['additional_charges']).'" /></div><br />
						<label>'.$this->l('Delivery Service').' : </label>
							<div class="margin-form">';
								$rateServiceList = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'fedex_rate_service_code`');
								foreach($rateServiceList as $rateService)
								{
									$configServiceSelected = Db::getInstance()->getValue('SELECT `id_fedex_rate_service_code` FROM `'._DB_PREFIX_.'fedex_rate_config_service` WHERE `id_fedex_rate_config` = '.(int)(Tools::getValue('id_fedex_rate_config')).' AND `id_fedex_rate_service_code` = '.(int)($rateService['id_fedex_rate_service_code']));
									$html .= '<input type="checkbox" name="service[]" value="'.$rateService['id_fedex_rate_service_code'].'" '.(($this->_isPostCheck($rateService['id_fedex_rate_service_code']) == 1 || $configServiceSelected > 0) ? 'checked="checked"' : '').' /> '.$rateService['service'].'<br />';
								}
						$html .= '
						<p>' . $this->l('Choose the delivery service which will be available for customers.') . '</p>
						</div>
						<div class="margin-form"><input class="button" name="submitSave" type="submit"></div>
					</form>';
		}
		else
		{
			$html .= '<p align="center"><b>'.$this->l('Add a rule').'</b></p>
					<form action="index.php?tab='.$_GET['tab'].'&configure='.$_GET['configure'].'&token='.$_GET['token'].'&tab_module='.$_GET['tab_module'].'&module_name='.$_GET['module_name'].'&id_tab=2&section=category&action=add" method="post" class="form">
						<label>'.$this->l('Category').' : </label>
						<div class="margin-form">
							<select name="id_category">
								<option value="0">'.$this->l('Select a category ...').'</option>
								'.$this->_getChildCategories(Category::getCategories($cookie->id_lang), 0).'
							</select>
						</div>
						<label>'.$this->l('Pickup Type').' : </label>
							<div class="margin-form">
								<select name="pickup_type_code">
									<option value="0">'.$this->l('Select a pickup type ...').'</option>';
									foreach($this->_pickupTypeList as $kpickup => $vpickup)
										$html .= '<option value="'.$kpickup.'" '.($kpickup === pSQL(Tools::getValue('pickup_type_code')) ? 'selected="selected"' : '').'>'.$vpickup.'</option>';
						$html .= '</select>
						</div>
						<label>'.$this->l('Packaging Type').' : </label>
							<div class="margin-form">
								<select name="packaging_type_code">
									<option value="0">'.$this->l('Select a packaging type ...').'</option>';
									foreach($this->_packagingTypeList as $kpackaging => $vpackaging)
										$html .= '<option value="'.$kpackaging.'" '.($kpackaging == pSQL(Tools::getValue('packaging_type_code')) ? 'selected="selected"' : '').'>'.$vpackaging.'</option>';
						$html .= '</select>
						</div>
						<label>'.$this->l('Additional charges').' : </label>
						<div class="margin-form"><input type="text" size="20" name="additional_charges" value="'.Tools::getValue('additional_charges').'" /></div><br />
						<label>'.$this->l('Delivery Service').' : </label>
							<div class="margin-form">';
								$rateServiceList = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'fedex_rate_service_code`');
								foreach($rateServiceList as $rateService)
									$html .= '<input type="checkbox" name="service[]" value="'.$rateService['id_fedex_rate_service_code'].'" '.(($this->_isPostCheck($rateService['id_fedex_rate_service_code']) == 1) ? 'checked="checked"' : '').' /> '.$rateService['service'].'<br />';
						$html .= '
						<p>' . $this->l('Choose the delivery service which will be available for customers.') . '</p>
						</div>
						<div class="margin-form"><input class="button" name="submitSave" type="submit"></div>
					</form>';
		}

		return $html;
	}

	private function _postValidationCategory()
	{
		// Check post values
		if (Tools::getValue('id_category') == NULL)
			$this->_postErrors[]  = $this->l('You have to select a category.');

		if (!$this->_postErrors)
		{
			$id_fedex_rate_config = Db::getInstance()->getValue('SELECT `id_fedex_rate_config` FROM `'._DB_PREFIX_.'fedex_rate_config` WHERE `id_category` = '.(int)Tools::getValue('id_category'));

			// Check if a config does not exist in Add case
			if ($id_fedex_rate_config > 0 && Tools::getValue('action') == 'add')
				$this->_postErrors[]  = $this->l('This category already has a specific FEDEX configuration.');

			// Check if a config exists and if the IDs config correspond in Upd case
			if (Tools::getValue('action') == 'edit' && (!isset($id_fedex_rate_config) || $id_fedex_rate_config != Tools::getValue('id_fedex_rate_config')))
				$this->_postErrors[]  = $this->l('An error occurred, please try again.');

			// Check if a config exists in Delete case
			if (Tools::getValue('action') == 'delete' && !isset($id_fedex_rate_config))
				$this->_postErrors[]  = $this->l('An error occurred, please try again.');
		}
	}

	private function _postProcessCategory()
	{
		// Init Var
		$date = date('Y-m-d H:i:s');
		$services = Tools::getValue('service');

		// Add Script
		if (Tools::getValue('action') == 'add')
		{
			$addTab = array(
				'id_product' => 0,
				'id_category' => (int)(Tools::getValue('id_category')),
				'id_currency' => (int)(Configuration::get('PS_CURRENCY_DEFAULT')),
				'pickup_type_code' => pSQL(Tools::getValue('pickup_type_code')),
				'packaging_type_code' => pSQL(Tools::getValue('packaging_type_code')),
				'additional_charges' => pSQL(Tools::getValue('additional_charges')),
				'date_add' => pSQL($date),
				'date_upd' => pSQL($date)
			);
			Db::getInstance()->autoExecute(_DB_PREFIX_.'fedex_rate_config', $addTab, 'INSERT');
			$id_fedex_rate_config = Db::getInstance()->Insert_ID();
			foreach ($services as $s)
			{
				$addTab = array('id_fedex_rate_service_code' => pSQL($s), 'id_fedex_rate_config' => (int)$id_fedex_rate_config, 'date_add' => pSQL($date), 'date_upd' => pSQL($date));
				Db::getInstance()->autoExecute(_DB_PREFIX_.'fedex_rate_config_service', $addTab, 'INSERT');
			}

			// Display Results
			if ($id_fedex_rate_config > 0)
				$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
			else
				$this->_html .= $this->displayErrors($this->l('Settings failed'));
		}

		// Update Script
		if (Tools::getValue('action') == 'edit' && Tools::getValue('id_fedex_rate_config'))
		{
			$updTab = array(
				'id_currency' => (int)(Configuration::get('PS_CURRENCY_DEFAULT')),
				'pickup_type_code' => pSQL(Tools::getValue('pickup_type_code')),
				'packaging_type_code' => pSQL(Tools::getValue('packaging_type_code')),
				'additional_charges' => pSQL(Tools::getValue('additional_charges')),
				'date_upd' => pSQL($date)
			);
			$result = Db::getInstance()->autoExecute(_DB_PREFIX_.'fedex_rate_config', $updTab, 'UPDATE', '`id_fedex_rate_config` = '.(int)Tools::getValue('id_fedex_rate_config'));
			Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'fedex_rate_config_service` WHERE `id_fedex_rate_config` = '.(int)Tools::getValue('id_fedex_rate_config'));
			foreach ($services as $s)
			{
				$addTab = array('id_fedex_rate_service_code' => pSQL($s), 'id_fedex_rate_config' => (int)Tools::getValue('id_fedex_rate_config'), 'date_add' => pSQL($date), 'date_upd' => pSQL($date));
				Db::getInstance()->autoExecute(_DB_PREFIX_.'fedex_rate_config_service', $addTab, 'INSERT');
			}

			// Display Results
			if ($result)
				$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
			else
				$this->_html .= $this->displayErrors($this->l('Settings failed'));
		}

		// Delete Script
		if (Tools::getValue('action') == 'delete' && Tools::getValue('id_fedex_rate_config'))
		{
			$result1 = Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'fedex_rate_config` WHERE `id_fedex_rate_config` = '.(int)Tools::getValue('id_fedex_rate_config'));
			$result2 = Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'fedex_rate_config_service` WHERE `id_fedex_rate_config` = '.(int)Tools::getValue('id_fedex_rate_config'));

			// Display Results
			if ($result1)
				$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
			else
				$this->_html .= $this->displayErrors($this->l('Settings failed'));			
		}
	}



	/*
	** Product Form Config Methods
	**
	*/

	private function _displayFormProduct()
	{
		global $cookie;

		// Check if the module is configured
		if (!$this->_webserviceTestResult)
			return '<p><b>'.$this->l('You have to configure "General Settings" tab before using this tab.').'</b></p><br />';

		// Display header
		$html = '<p><b>'.$this->l('In this tab, you can set a specific configuration for each product.').'</b></p><br />
		<table class="table tableDnD" cellpadding="0" cellspacing="0" width="90%">
			<thead>
				<tr class="nodrag nodrop">
					<th>'.$this->l('ID Config').'</th>
					<th>'.$this->l('Product').'</th>
					<th>'.$this->l('Pickup type').'</th>
					<th>'.$this->l('Packaging type').'</th>
					<th>'.$this->l('Additional charges').'</th>
					<th>'.$this->l('Services').'</th>
					<th>'.$this->l('Actions').'</th>
				</tr>
			</thead>
			<tbody>';

		// Loading config list
		$configProductList = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'fedex_rate_config` WHERE `id_product` > 0');
		if (!$configProductList)
			$html .= '<tr><td colspan="6">'.$this->l('There is no specific FEDEX configuration for products at this point.').'</td></tr>';
		foreach ($configProductList as $k => $c)
		{
			// Loading Product
			$product = new Product((int)$c['id_product'], false, (int)$cookie->id_lang);

			// Loading config currency
			$configCurrency = new Currency($c['id_currency']);

			// Loading services attached to this config
			$services = '';
			$servicesTab = Db::getInstance()->ExecuteS('
			SELECT ursc.`service`
			FROM `'._DB_PREFIX_.'fedex_rate_config_service` urcs
			LEFT JOIN `'._DB_PREFIX_.'fedex_rate_service_code` ursc ON (ursc.`id_fedex_rate_service_code` = urcs.`id_fedex_rate_service_code`)
			WHERE urcs.`id_fedex_rate_config` = '.(int)$c['id_fedex_rate_config']);
			foreach ($servicesTab as $s)
				$services .= $s['service'].'<br />';

			// Display line
			$alt = 0;
			if ($k % 2 != 0)
				$alt = ' class="alt_row"';
			$html .= '
				<tr'.$alt.'>
					<td>'.$c['id_fedex_rate_config'].'</td>
					<td>'.$product->name.'</td>
					<td>'.(isset($this->_pickupTypeList[$c['pickup_type_code']]) ? $this->_pickupTypeList[$c['pickup_type_code']] : '-').'</td>
					<td>'.(isset($this->_packagingTypeList[$c['packaging_type_code']]) ? $this->_packagingTypeList[$c['packaging_type_code']] : '-').'</td>
					<td>'.$c['additional_charges'].' '.$configCurrency->sign.'</td>
					<td>'.$services.'</td>
					<td>
						<a href="index.php?tab='.$_GET['tab'].'&configure='.$_GET['configure'].'&token='.$_GET['token'].'&tab_module='.$_GET['tab_module'].'&module_name='.$_GET['module_name'].'&id_tab=3&section=product&action=edit&id_fedex_rate_config='.(int)($c['id_fedex_rate_config']).'" style="float: left;">
							<img src="'._PS_IMG_.'admin/edit.gif" />
						</a>
						<form action="index.php?tab='.$_GET['tab'].'&configure='.$_GET['configure'].'&token='.$_GET['token'].'&tab_module='.$_GET['tab_module'].'&module_name='.$_GET['module_name'].'&id_tab=3&section=product&action=delete&id_fedex_rate_config='.(int)($c['id_fedex_rate_config']).'&id_product='.(int)($c['id_product']).'" method="post" class="form" style="float: left;">
							<input name="submitSave" type="image" src="'._PS_IMG_.'admin/delete.gif" OnClick="return confirm(\''.$this->l('Are you sure you want to delete this specific FEDEX configuration for this product ?').'\');" />
						</form>
					</td>
				</tr>';
		}

		$html .= '
			</tbody>
		</table><br /><br />';

		// Add or Edit Product Configuration
		if (Tools::getValue('action') == 'edit' && Tools::getValue('section') == 'product')
		{
			// Loading config
			$configSelected = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'fedex_rate_config` WHERE `id_fedex_rate_config` = '.(int)(Tools::getValue('id_fedex_rate_config')));
			$product = new Product((int)$configSelected['id_product'], false, (int)$cookie->id_lang);

			$html .= '<p align="center"><b>'.$this->l('Update a rule').' (<a href="index.php?tab='.$_GET['tab'].'&configure='.$_GET['configure'].'&token='.$_GET['token'].'&tab_module='.$_GET['tab_module'].'&module_name='.$_GET['module_name'].'&id_tab=3&section=product&action=add">'.$this->l('Add a rule').' ?</a>)</b></p>
					<form action="index.php?tab='.$_GET['tab'].'&configure='.$_GET['configure'].'&token='.$_GET['token'].'&tab_module='.$_GET['tab_module'].'&module_name='.$_GET['module_name'].'&id_tab=3&section=product&action=edit&id_fedex_rate_config='.(int)(Tools::getValue('id_fedex_rate_config')).'" method="post" class="form">
						<label>'.$this->l('Product').' :</label>
						<div class="margin-form" style="padding: 0.2em 0.5em 0 0; font-size: 12px;">'.$product->name.' <input type="hidden" name="id_product" value="'.(int)($configSelected['id_product']).'" /></div><br clear="left" />
						<label>'.$this->l('Pickup Type').' : </label>
							<div class="margin-form">
								<select name="pickup_type_code">
									<option value="0">'.$this->l('Select a pickup type ...').'</option>';
									foreach($this->_pickupTypeList as $kpickup => $vpickup)
										$html .= '<option value="'.$kpickup.'" '.($kpickup === pSQL(Tools::getValue('pickup_type_code', $configSelected['pickup_type_code'])) ? 'selected="selected"' : '').'>'.$vpickup.'</option>';
						$html .= '</select>
						</div>
						<label>'.$this->l('Packaging Type').' : </label>
							<div class="margin-form">
								<select name="packaging_type_code">
									<option value="0">'.$this->l('Select a packaging type ...').'</option>';
									foreach($this->_packagingTypeList as $kpackaging => $vpackaging)
										$html .= '<option value="'.$kpackaging.'" '.($kpackaging == pSQL(Tools::getValue('packaging_type_code', $configSelected['packaging_type_code'])) ? 'selected="selected"' : '').'>'.$vpackaging.'</option>';
						$html .= '</select>
						</div>
						<label>'.$this->l('Additional charges').' : </label>
						<div class="margin-form"><input type="text" size="20" name="additional_charges" value="'.Tools::getValue('additional_charges', $configSelected['additional_charges']).'" /></div><br />
						<label>'.$this->l('Delivery Service').' : </label>
							<div class="margin-form">';
								$rateServiceList = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'fedex_rate_service_code`');
								foreach($rateServiceList as $rateService)
								{
									$configServiceSelected = Db::getInstance()->getValue('SELECT `id_fedex_rate_service_code` FROM `'._DB_PREFIX_.'fedex_rate_config_service` WHERE `id_fedex_rate_config` = '.(int)(Tools::getValue('id_fedex_rate_config')).' AND `id_fedex_rate_service_code` = '.(int)($rateService['id_fedex_rate_service_code']));
									$html .= '<input type="checkbox" name="service[]" value="'.$rateService['id_fedex_rate_service_code'].'" '.(($this->_isPostCheck($rateService['id_fedex_rate_service_code']) == 1 || $configServiceSelected > 0) ? 'checked="checked"' : '').' /> '.$rateService['service'].'<br />';
								}
						$html .= '
						<p>' . $this->l('Choose the delivery service which will be available for customers.') . '</p>
						</div>
						<div class="margin-form"><input class="button" name="submitSave" type="submit"></div>
					</form>';
		}
		else
		{
			$html .= '<p align="center"><b>'.$this->l('Add a rule').'</b></p>
					<form action="index.php?tab='.$_GET['tab'].'&configure='.$_GET['configure'].'&token='.$_GET['token'].'&tab_module='.$_GET['tab_module'].'&module_name='.$_GET['module_name'].'&id_tab=3&section=product&action=add" method="post" class="form">
						<label>'.$this->l('Product').' : </label>
						<div class="margin-form">
							<select name="id_product">
								<option value="0">'.$this->l('Select a product ...').'</option>';
						$productsList = Db::getInstance()->ExecuteS('
						SELECT pl.* FROM `'._DB_PREFIX_.'product` p
						LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = 2)
						WHERE p.`active` = 1
						ORDER BY pl.`name`');
						foreach ($productsList as $product)
							$html .= '<option value="'.$product['id_product'].'" '.($product['id_product'] == (int)(Tools::getValue('id_product')) ? 'selected="selected"' : '').'>'.$product['name'].'</option>';
						$html .= '</select>
						</div>
						<label>'.$this->l('Pickup Type').' : </label>
							<div class="margin-form">
								<select name="pickup_type_code">
									<option value="0">'.$this->l('Select a pickup type ...').'</option>';
									foreach($this->_pickupTypeList as $kpickup => $vpickup)
										$html .= '<option value="'.$kpickup.'" '.($kpickup === pSQL(Tools::getValue('pickup_type_code')) ? 'selected="selected"' : '').'>'.$vpickup.'</option>';
						$html .= '</select>
						</div>
						<label>'.$this->l('Packaging Type').' : </label>
							<div class="margin-form">
								<select name="packaging_type_code">
									<option value="0">'.$this->l('Select a packaging type ...').'</option>';
									foreach($this->_packagingTypeList as $kpackaging => $vpackaging)
										$html .= '<option value="'.$kpackaging.'" '.($kpackaging == pSQL(Tools::getValue('packaging_type_code')) ? 'selected="selected"' : '').'>'.$vpackaging.'</option>';
						$html .= '</select>
						</div>
						<label>'.$this->l('Additional charges').' : </label>
						<div class="margin-form"><input type="text" size="20" name="additional_charges" value="'.Tools::getValue('additional_charges').'" /></div><br />
						<label>'.$this->l('Delivery Service').' : </label>
							<div class="margin-form">';
								$rateServiceList = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'fedex_rate_service_code`');
								foreach($rateServiceList as $rateService)
									$html .= '<input type="checkbox" name="service[]" value="'.$rateService['id_fedex_rate_service_code'].'" '.(($this->_isPostCheck($rateService['id_fedex_rate_service_code']) == 1) ? 'checked="checked"' : '').' /> '.$rateService['service'].'<br />';
						$html .= '
						<p>' . $this->l('Choose the delivery service which will be available for customers.') . '</p>
						</div>
						<div class="margin-form"><input class="button" name="submitSave" type="submit"></div>
					</form>';
		}

		return $html;
	}
	
	private function _postValidationProduct()
	{
		// Check post values
		if (Tools::getValue('id_product') == NULL)
			$this->_postErrors[]  = $this->l('You have to select a product.');

		if (!$this->_postErrors)
		{
			$id_fedex_rate_config = Db::getInstance()->getValue('SELECT `id_fedex_rate_config` FROM `'._DB_PREFIX_.'fedex_rate_config` WHERE `id_product` = '.(int)Tools::getValue('id_product'));

			// Check if a config does not exist in Add case
			if ($id_fedex_rate_config > 0 && Tools::getValue('action') == 'add')
				$this->_postErrors[]  = $this->l('This product already has a specific FEDEX configuration.');

			// Check if a config exists and if the IDs config correspond in Upd case
			if (Tools::getValue('action') == 'edit' && (!isset($id_fedex_rate_config) || $id_fedex_rate_config != Tools::getValue('id_fedex_rate_config')))
				$this->_postErrors[]  = $this->l('An error occurred, please try again.');

			// Check if a config exists in Delete case
			if (Tools::getValue('action') == 'delete' && !isset($id_fedex_rate_config))
				$this->_postErrors[]  = $this->l('An error occurred, please try again.');
		}
	}
	
	private function _postProcessProduct()
	{
		// Init Var
		$date = date('Y-m-d H:i:s');
		$services = Tools::getValue('service');

		// Add Script
		if (Tools::getValue('action') == 'add')
		{
			$addTab = array(
				'id_product' => (int)(Tools::getValue('id_product')),
				'id_category' => 0,
				'id_currency' => (int)(Configuration::get('PS_CURRENCY_DEFAULT')),
				'pickup_type_code' => pSQL(Tools::getValue('pickup_type_code')),
				'packaging_type_code' => pSQL(Tools::getValue('packaging_type_code')),
				'additional_charges' => pSQL(Tools::getValue('additional_charges')),
				'date_add' => pSQL($date),
				'date_upd' => pSQL($date)
			);
			Db::getInstance()->autoExecute(_DB_PREFIX_.'fedex_rate_config', $addTab, 'INSERT');
			$id_fedex_rate_config = Db::getInstance()->Insert_ID();
			foreach ($services as $s)
			{
				$addTab = array('id_fedex_rate_service_code' => pSQL($s), 'id_fedex_rate_config' => (int)$id_fedex_rate_config, 'date_add' => pSQL($date), 'date_upd' => pSQL($date));
				Db::getInstance()->autoExecute(_DB_PREFIX_.'fedex_rate_config_service', $addTab, 'INSERT');
			}

			// Display Results
			if ($id_fedex_rate_config > 0)
				$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
			else
				$this->_html .= $this->displayErrors($this->l('Settings failed'));
		}

		// Update Script
		if (Tools::getValue('action') == 'edit' && Tools::getValue('id_fedex_rate_config'))
		{
			$updTab = array(
				'id_currency' => (int)(Configuration::get('PS_CURRENCY_DEFAULT')),
				'pickup_type_code' => pSQL(Tools::getValue('pickup_type_code')),
				'packaging_type_code' => pSQL(Tools::getValue('packaging_type_code')),
				'additional_charges' => pSQL(Tools::getValue('additional_charges')),
				'date_upd' => pSQL($date)
			);
			$result = Db::getInstance()->autoExecute(_DB_PREFIX_.'fedex_rate_config', $updTab, 'UPDATE', '`id_fedex_rate_config` = '.(int)Tools::getValue('id_fedex_rate_config'));
			Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'fedex_rate_config_service` WHERE `id_fedex_rate_config` = '.(int)Tools::getValue('id_fedex_rate_config'));
			foreach ($services as $s)
			{
				$addTab = array('id_fedex_rate_service_code' => pSQL($s), 'id_fedex_rate_config' => (int)Tools::getValue('id_fedex_rate_config'), 'date_add' => pSQL($date), 'date_upd' => pSQL($date));
				Db::getInstance()->autoExecute(_DB_PREFIX_.'fedex_rate_config_service', $addTab, 'INSERT');
			}

			// Display Results
			if ($result)
				$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
			else
				$this->_html .= $this->displayErrors($this->l('Settings failed'));
		}

		// Delete Script
		if (Tools::getValue('action') == 'delete' && Tools::getValue('id_fedex_rate_config'))
		{
			$result1 = Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'fedex_rate_config` WHERE `id_fedex_rate_config` = '.(int)Tools::getValue('id_fedex_rate_config'));
			$result2 = Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'fedex_rate_config_service` WHERE `id_fedex_rate_config` = '.(int)Tools::getValue('id_fedex_rate_config'));

			// Display Results
			if ($result1)
				$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
			else
				$this->_html .= $this->displayErrors($this->l('Settings failed'));			
		}
	}



	/*
	** Help Config Methods
	**
	*/

	private function _displayHelp()
	{
		return '<p><b>'.$this->l('Welcome to the PrestaShop FEDEX Module configurator.').'</b></p>
		<p>'.$this->l('This section will help you to understand how to configure this module correctly.').'</p>
		<br />
		<p><b><u>1. '.$this->l('General Settings').'</u></b></p>
		<p>'.$this->l('See below for the description of each field :').'</p>
		<p><b>'.$this->l('Your FEDEX Account').' :</b> '.$this->l('You must subscribe to FEDEX website at this address').' <a href="http://www.fedex.com/webtools/" target="_blank">http://www.fedex.com/webtools/</a></p>
		<p><b>'.$this->l('Zip / Postal Code').' :</b> '.$this->l('This field must be the Zip / Postal code of your package starting point.').'</p>
		<p><b>'.$this->l('Country').' :</b> '.$this->l('This field must be the country of your package starting point.').'</p>
		<p><b>'.$this->l('Pickup Type').' :</b> '.$this->l('This field corresponds to the default pickup type (when there is no specific configuration for the product or the category product).').'</p>
		<p><b>'.$this->l('Delivery Service').' :</b> '.$this->l('These checkboxes correspond to the delivery services you want to be available (when there is no specific configuration for the product or the category product).').'</p>
		<br />
		<p><b><u>2. '.$this->l('Categories Settings').'</u></b></p>
		<p>'.$this->l('This section allows you to define a specific FEDEX configuration for each product category (such as Packaging Type and Additional charges).').'</p>
		<br />
		<p><b><u>3. '.$this->l('Products Settings').'</u></b></p>
		<p>'.$this->l('This section allows you to define a specific FEDEX configuration for each product (such as Packaging Type and Additional charges).').'</p>
		<br />
		';
	}

	public function hookupdateCarrier($params)
	{
		if ((int)($params['id_carrier']) != (int)($params['carrier']->id))
		{
			$serviceSelected = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'fedex_rate_service_code` WHERE `id_carrier` = '.(int)$params['id_carrier']);
			$update = array('id_carrier' => (int)($params['carrier']->id), 'id_carrier_history' => pSQL($serviceSelected['id_carrier_history'].'|'.(int)($params['carrier']->id)));
			Db::getInstance()->autoExecute(_DB_PREFIX_.'fedex_rate_service_code', $update, 'UPDATE', '`id_carrier` = '.(int)$params['id_carrier']);
		}
	}

	public function displayInfoByCart()
	{
	}



	/*
	** Front Methods
	**
	*/

	public function getCookieCurrencyRate($id_currency_origin)
	{
		global $cookie;
		$conversionRate = 1;
		if ($cookie->id_currency != $id_currency_origin)
		{
			$currencyOrigin = new Currency((int)$id_currency_origin);
			$conversionRate /= $currencyOrigin->conversion_rate;
			$currencySelect = new Currency((int)$cookie->id_currency);
			$conversionRate *= $currencySelect->conversion_rate;
		}
		return $conversionRate;
	}
	
	public function getOrderShippingCostHash($wsParams)
	{
		$paramHash = '';
		$productHash = '';
		foreach ($wsParams['products'] as $product)
		{
			if (!empty($productHash))
				$productHash .= '|';
			$productHash .= $product['id_product'].':'.$product['id_product_attribute'].':'.$product['cart_quantity'];
		}
		foreach ($wsParams as $k => $v)
			if ($k != 'products')
			$paramHash .= '/'.$v;
		return md5($productHash.$paramHash);
	}

	public function getOrderShippingCostCache($wsParams)
	{
		// Get Cache
		$row = Db::getInstance()->getRow("
		SELECT * FROM `"._DB_PREFIX_."fedex_cache`
		WHERE `id_cart` = ".(int)($wsParams['id_cart'])."
		AND `id_carrier` = ".(int)($this->id_carrier)."
		AND `hash` = '".pSQL($wsParams['hash'])."'");

		if ($row['id_currency'])
		{
			// Check Currency Rate And Calcul
			$conversionRate = $this->getCookieCurrencyRate($row['id_currency']);
			$row['total_charges'] = $row['total_charges'] * $conversionRate;

			// Return Cache
			return $row;
		}
		
		return false;
	}

	public function saveOrderShippingCostCache($wsParams, $wscost)
	{
		global $cookie;
		$is_available = 1;
		if (!$wscost)
			$is_available = 0;
		$date = date('Y-m-d H:i:s');
		$insert = array(
			'id_cart' => (int)($wsParams['id_cart']),
			'id_carrier' => (int)($this->id_carrier),
			'hash' => pSQL($wsParams['hash']),
			'id_currency' => (int)($cookie->id_currency),
			'total_charges' => pSQL($wscost),
			'is_available' => (int)($is_available),
			'date_add' => pSQL($date),
			'date_upd' => pSQL($date)
		);
		Db::getInstance()->autoExecute(_DB_PREFIX_.'fedex_cache', $insert, 'INSERT');
	}

	public function loadShippingCostConfig($product)
	{
		// Init var
		$config = array();
	
		// Check if there is a specific product configuration
		if ($product['id_product'] > 0)
		{
			$productConfiguration = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'fedex_rate_config` WHERE `id_product` = '.(int)($product['id_product']));
			if ($productConfiguration['id_fedex_rate_config'])
			{
				$servicesConfiguration = Db::getInstance()->ExecuteS('
				SELECT urcs.*, ursc.`id_carrier`
				FROM `'._DB_PREFIX_.'fedex_rate_config_service` urcs
				LEFT JOIN `'._DB_PREFIX_.'fedex_rate_service_code` ursc ON (ursc.`id_fedex_rate_service_code` = urcs.`id_fedex_rate_service_code`)
				WHERE `id_fedex_rate_config` = '.(int)($productConfiguration['id_fedex_rate_config']));
				foreach ($servicesConfiguration as $service)
					$productConfiguration['services'][$service['id_fedex_rate_service_code']] = $service;
				return $productConfiguration;
			}
		}

		// Check if there is a specific category configuration
		if ($product['id_category_default'] > 0)
		{
			$categoryConfiguration = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'fedex_rate_config` WHERE `id_category` = '.(int)($product['id_category_default']));
			if ($categoryConfiguration['id_fedex_rate_config'])
			{
				$servicesConfiguration = Db::getInstance()->ExecuteS('
				SELECT urcs.*, ursc.`id_carrier`
				FROM `'._DB_PREFIX_.'fedex_rate_config_service` urcs
				LEFT JOIN `'._DB_PREFIX_.'fedex_rate_service_code` ursc ON (ursc.`id_fedex_rate_service_code` = urcs.`id_fedex_rate_service_code`)
				WHERE `id_fedex_rate_config` = '.(int)($categoryConfiguration['id_fedex_rate_config']));
				foreach ($servicesConfiguration as $service)
					$categoryConfiguration['services'][$service['id_fedex_rate_service_code']] = $service;
				return $categoryConfiguration;
			}
		}

		// Return general config
		$config['pickup_type_code'] = Configuration::get('FEDEX_CARRIER_PICKUP_TYPE');
		$servicesConfiguration = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'fedex_rate_service_code` WHERE `active` = 1');
		foreach ($servicesConfiguration as $service)
			$config['services'][$service['id_fedex_rate_service_code']] = $service;
		return $config;
	}

	public function getWebserviceShippingCost($wsParams)
	{
		// Init var
		$cost = 0;

		// Getting shipping cost for each product
		foreach ($wsParams['products'] as $product)
		{
			// Load specific configuration
			$config = $this->loadShippingCostConfig($product);

			// Get service in adequation with carrier and check if available
			$serviceSelected = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'fedex_rate_service_code` WHERE `id_carrier` = '.(int)($this->id_carrier));
			if (!isset($config['services'][$serviceSelected['id_fedex_rate_service_code']]))
				return false;

			// Load param product
			$wsParams['service'] = $serviceSelected['code'];
			for ($qty = 0; $qty < $product['quantity']; $qty++)
				$wsParams['package_list'][] = array(
					'width' => ($product['width'] ? $product['width'] : 1),
					'height' => ($product['height'] ? $product['height'] : 1),
					'depth' => ($product['depth'] ? $product['depth'] : 1),
					'weight' => ($product['weight'] ? $product['weight'] : 1),
					'pickup_type' => (isset($config['pickup_type_code']) ? $config['pickup_type_code'] : Configuration::get('FEDEX_CARRIER_PICKUP_TYPE')),
				);

			// If Additional charges
			if (isset($config['id_currency']) && isset($config['additional_charges']))
			{
				$conversionRate = 1;
				$conversionRate = $this->getCookieCurrencyRate((int)($config['id_currency']));
				$cost += ($config['additional_charges'] * $conversionRate);
			}
		}


		// If webservice return a cost, we add it, else, we return the original shipping cost
		$result = $this->getFedexShippingCost($wsParams);
		if ($result['connect'] && $result['cost'] > 0)
			return ($cost + $result['cost']);
		return false;
	}

	public function getOrderShippingCost($params, $shipping_cost)
	{	
		// Init var
		$address = new Address($params->id_address_delivery);
		$recipient_country = Db::getInstance()->getRow('SELECT `iso_code` FROM `'._DB_PREFIX_.'country` WHERE `id_country` = '.(int)($address->id_country));
		$recipient_state = Db::getInstance()->getRow('SELECT `iso_code` FROM `'._DB_PREFIX_.'state` WHERE `id_state` = '.(int)($address->id_state));
		$shipper_country = Db::getInstance()->getRow('SELECT `iso_code` FROM `'._DB_PREFIX_.'country` WHERE `id_country` = '.(int)(Configuration::get('FEDEX_CARRIER_COUNTRY')));
		$shipper_state = Db::getInstance()->getRow('SELECT `iso_code` FROM `'._DB_PREFIX_.'state` WHERE `id_state` = '.(int)(Configuration::get('FEDEX_CARRIER_STATE')));
		$products = $params->getProducts();

		// Webservices Params
		$wsParams = array(
			'id_cart' => $params->id,
			'id_address_delivery' => $params->id_address_delivery,
			'recipient_address1' => $address->address1,
			'recipient_address2' => $address->address2,
			'recipient_postalcode' => $address->postcode,
			'recipient_city' => $address->city,
			'recipient_country_iso' => $recipient_country['iso_code'],
			'recipient_state_iso' => $recipient_state['iso_code'],
			'shipper_address1' => Configuration::get('FEDEX_CARRIER_ADDRESS1'),
			'shipper_address2' => Configuration::get('FEDEX_CARRIER_ADDRESS2'),
			'shipper_postalcode' => Configuration::get('FEDEX_CARRIER_POSTAL_CODE'),
			'shipper_city' => Configuration::get('FEDEX_CARRIER_CITY'),
			'shipper_country_iso' => $shipper_country['iso_code'],
			'shipper_state_iso' => $shipper_state['iso_code'],
			'products' => $params->getProducts()
		);
		$wsParams['hash'] = $this->getOrderShippingCostHash($wsParams);

		// Check cache
		$cache = $this->getOrderShippingCostCache($wsParams);
		if ($cache['id_fedex_cache'] > 0)
		{
			if ($cache['is_available'] == 0)
				return false;
			if ($cache['total_charges'])
				return $cache['total_charges'];
		}

		// Get Webservices Cost and Cache it
		$wscost = $this->getWebserviceShippingCost($wsParams);
		$this->saveOrderShippingCostCache($wsParams, $wscost);

		if ($wscost > 0)
			return $wscost + $shipping_cost;
		return false;
	}

	public function getOrderShippingCostExternal($params)
	{
		return $this->getOrderShippingCost($params, 23);
	}



	/*
	** Webservices Methods
	**
	*/

	public function webserviceTest($service = '')
	{
		// Check config
		if (!Configuration::get('FEDEX_CARRIER_API_KEY'))
			return false;

		// Check if class Soap is available
		if (!extension_loaded('soap'))
			return false;

		// Getting module directory
		$dir = dirname(__FILE__);
		if (preg_match('/classes/i', $dir))
			$dir .= '/../modules/fedexcarrier/';

		// Enable Php Soap
		ini_set("soap.wsdl_cache_enabled", "0");
		$client = new SoapClient($dir.'/RateService_v9.wsdl', array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

		// Country / State
		$shipper_country = Db::getInstance()->getRow('SELECT `iso_code` FROM `'._DB_PREFIX_.'country` WHERE `id_country` = '.(int)(Configuration::get('FEDEX_CARRIER_COUNTRY')));
		$shipper_state = Db::getInstance()->getRow('SELECT `iso_code` FROM `'._DB_PREFIX_.'state` WHERE `id_state` = '.(int)(Configuration::get('FEDEX_CARRIER_STATE')));

		// Generating soap request
		$request['WebAuthenticationDetail']['UserCredential'] = array('Key' => Configuration::get('FEDEX_CARRIER_API_KEY'), 'Password' => Configuration::get('FEDEX_CARRIER_PASSWORD')); 
		$request['ClientDetail'] = array('AccountNumber' => Configuration::get('FEDEX_CARRIER_ACCOUNT'), 'MeterNumber' => Configuration::get('FEDEX_CARRIER_METER'));
		$request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Rate Available Services Request v9 using PHP ***');
		$request['Version'] = array('ServiceId' => 'crs', 'Major' => '9', 'Intermediate' => '0', 'Minor' => '0');
		$request['ReturnTransitAndCommit'] = true;
		$request['RequestedShipment']['DropoffType'] = Configuration::get('FEDEX_CARRIER_PICKUP_TYPE'); // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
		$request['RequestedShipment']['ShipTimestamp'] = date('c');
		//$request['RequestedShipment']['ServiceType'] = 'PRIORITY_OVERNIGHT'; // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
		$request['RequestedShipment']['PackagingType'] = Configuration::get('FEDEX_CARRIER_PACKAGING_TYPE'); // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...

		// Service Type and Packaging Type are not passed in the request
		$request['RequestedShipment']['Shipper']['Address'] = array('StreetLines' => Configuration::get('FEDEX_CARRIER_ADDRESS1'), 'City' => Configuration::get('FEDEX_CARRIER_CITY'), 'StateOrProvinceCode' => $shipper_state['iso_code'], 'PostalCode' => Configuration::get('FEDEX_CARRIER_POSTAL_CODE'), 'CountryCode' => $shipper_country['iso_code']);
		$request['RequestedShipment']['Recipient']['Address'] = array('StreetLines' => Configuration::get('FEDEX_CARRIER_ADDRESS1'), 'City' => Configuration::get('FEDEX_CARRIER_CITY'), 'StateOrProvinceCode' => $shipper_state['iso_code'], 'PostalCode' => Configuration::get('FEDEX_CARRIER_POSTAL_CODE'), 'CountryCode' => $shipper_country['iso_code']);
		$request['RequestedShipment']['ShippingChargesPayment'] = array('PaymentType' => 'SENDER', 'Payor' => array('AccountNumber' => Configuration::get('FEDEX_CARRIER_ACCOUNT'), 'CountryCode' => 'US'));
		$request['RequestedShipment']['RateRequestTypes'] = 'ACCOUNT'; 
		$request['RequestedShipment']['RateRequestTypes'] = 'LIST'; 
		$request['RequestedShipment']['PackageCount'] = '2';
		$request['RequestedShipment']['PackageDetail'] = 'INDIVIDUAL_PACKAGES';
		$request['RequestedShipment']['RequestedPackageLineItems'] = array('0' => array('Weight' => array('Value' => 2.0, 'Units' => 'LB'), 'Dimensions' => array('Length' => 10, 'Width' => 10, 'Height' => 3, 'Units' => 'IN')));


		// Unit or Large Test
		if (!empty($service))
			$servicesList = array(array('code' => $service));
		else
			$servicesList = Db::getInstance()->ExecuteS('SELECT `code` FROM `'._DB_PREFIX_.'fedex_rate_service_code`');


		// Testing Service
		foreach ($servicesList as $service)
		{
			// Sending Request
			$request['RequestedShipment']['ServiceType'] = $service['code'];
			$requestHash = $request;
			$requestHash['RequestedShipment']['ShipTimestamp'] = 'none';

			$resultTabTmp = Db::getInstance()->getValue('SELECT `result` FROM `'._DB_PREFIX_.'fedex_cache_test` WHERE `hash` = \''.pSQL(md5(var_export($requestHash, true))).'\'');
			if ($resultTabTmp)
				$resultTab = unserialize($resultTabTmp);
			else
				$resultTab = $client->getRates($request);

			// Cache test result
			if (empty($resultTabTmp) && isset($resultTab->HighestSeverity) && ($resultTab->HighestSeverity == 'SUCCESS' OR $resultTab->HighestSeverity == 'ERROR'))
				Db::getInstance()->autoExecute(_DB_PREFIX_.'fedex_cache_test', array('hash' => pSQL(md5(var_export($requestHash, true))), 'result' => pSQL(serialize($resultTab)), 'date_add' => pSQL(date('Y-m-d H:i:s')), 'date_upd' => pSQL(date('Y-m-d H:i:s'))), 'INSERT');

			// Return results
			if (isset($resultTab->HighestSeverity) && $resultTab->HighestSeverity == 'SUCCESS')
				return true;

			if (isset($resultTab->HighestSeverity) && $resultTab->HighestSeverity == 'ERROR')
				$this->_webserviceError = $this->l('Error').' '.(isset($resultTab->Notifications->Code) ? $resultTab->Notifications->Code : '').' : '.(isset($resultTab->Notifications->Message) ? $resultTab->Notifications->Message : '');
			else
			{
				$this->_webserviceError = $this->l('Fedex Webservice seems to be down, please wait a few minutes and try again.');
				return false;
			}
		}

		return false;
	}

	public function getFedexShippingCost($wsParams)
	{
		// Check config
		if (!Configuration::get('FEDEX_CARRIER_API_KEY'))
			return array('connect' => false, 'cost' => 0);

		// Check if class Soap is available
		if (!extension_loaded('soap'))
			return array('connect' => false, 'cost' => 0);

		// Getting module directory
		$dir = dirname(__FILE__);
		if (preg_match('/classes/i', $dir))
			$dir .= '/../modules/fedexcarrier/';

		// Enable Php Soap
		ini_set("soap.wsdl_cache_enabled", "0");
		$client = new SoapClient($dir.'/RateService_v9.wsdl', array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

		// Country / State
		$shipper_country = Db::getInstance()->getRow('SELECT `iso_code` FROM `'._DB_PREFIX_.'country` WHERE `id_country` = '.(int)(Configuration::get('FEDEX_CARRIER_COUNTRY')));
		$shipper_state = Db::getInstance()->getRow('SELECT `iso_code` FROM `'._DB_PREFIX_.'state` WHERE `id_state` = '.(int)(Configuration::get('FEDEX_CARRIER_STATE')));

		// Generating soap request
		$request['WebAuthenticationDetail']['UserCredential'] = array('Key' => Configuration::get('FEDEX_CARRIER_API_KEY'), 'Password' => Configuration::get('FEDEX_CARRIER_PASSWORD')); 
		$request['ClientDetail'] = array('AccountNumber' => Configuration::get('FEDEX_CARRIER_ACCOUNT'), 'MeterNumber' => Configuration::get('FEDEX_CARRIER_METER'));
		$request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Rate Available Services Request v9 using PHP ***');
		$request['Version'] = array('ServiceId' => 'crs', 'Major' => '9', 'Intermediate' => '0', 'Minor' => '0');
		$request['ReturnTransitAndCommit'] = true;
		$request['RequestedShipment']['DropoffType'] = Configuration::get('FEDEX_CARRIER_PICKUP_TYPE'); // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
		$request['RequestedShipment']['ShipTimestamp'] = date('c');
		$request['RequestedShipment']['ServiceType'] = $wsParams['service']; // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
		$request['RequestedShipment']['PackagingType'] = Configuration::get('FEDEX_CARRIER_PACKAGING_TYPE'); // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...

		// Service Type and Packaging Type are not passed in the request
		$request['RequestedShipment']['Shipper']['Address'] = array('StreetLines' => $wsParams['shipper_address1'], 'City' => $wsParams['shipper_city'], 'StateOrProvinceCode' => $wsParams['shipper_state_iso'], 'PostalCode' => $wsParams['shipper_postalcode'], 'CountryCode' => $wsParams['shipper_country_iso']);
		$request['RequestedShipment']['Recipient']['Address'] = array('StreetLines' => $wsParams['recipient_address1'], 'City' => $wsParams['recipient_city'], 'StateOrProvinceCode' => $wsParams['recipient_state_iso'], 'PostalCode' => $wsParams['recipient_postalcode'], 'CountryCode' => $wsParams['recipient_country_iso']);
		$request['RequestedShipment']['ShippingChargesPayment'] = array('PaymentType' => 'SENDER', 'Payor' => array('AccountNumber' => Configuration::get('FEDEX_CARRIER_ACCOUNT'), 'CountryCode' => 'US'));
		$request['RequestedShipment']['RateRequestTypes'] = 'ACCOUNT'; 
		$request['RequestedShipment']['RateRequestTypes'] = 'LIST'; 
		$request['RequestedShipment']['PackageCount'] = '2';
		$request['RequestedShipment']['PackageDetail'] = 'INDIVIDUAL_PACKAGES';
		foreach ($wsParams['package_list'] as $p)
			$request['RequestedShipment']['RequestedPackageLineItems'][] = array('Weight' => array('Value' => $p['weight'], 'Units' => substr($this->_weightUnit, 0, 2)), 'Dimensions' => array('Length' => $p['depth'], 'Width' => $p['width'], 'Height' => $p['height'], 'Units' => $this->_dimensionUnit));


		// Get Rates
		$resultTab = $client->getRates($request);


		// Check currency
		$conversionRate = 1;
		if (isset($resultTab->RateReplyDetails->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Currency))
		{
			$id_currency_return = Db::getInstance()->getValue('SELECT `id_currency` FROM `'._DB_PREFIX_.'currency` WHERE `iso_code` = \''.pSQL($resultTab->RateReplyDetails->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Currency).'\'');
			$conversionRate = $this->getCookieCurrencyRate($id_currency_return);
		}

		// Return results
		if (isset($resultTab->HighestSeverity) && $resultTab->HighestSeverity == 'SUCCESS')
			return array('connect' => true, 'cost' => number_format($resultTab->RateReplyDetails->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,'.',',') * $conversionRate);

		if (isset($resultTab->HighestSeverity) && $resultTab->HighestSeverity == 'ERROR')
			$this->_webserviceError = $this->l('Error').' '.(isset($resultTab->Notifications->Code) ? $resultTab->Notifications->Code : '').' : '.(isset($resultTab->Notifications->Message) ? $resultTab->Notifications->Message : '');
		else
			$this->_webserviceError = $this->l('Fedex Webservice seems to be down, please wait a few minutes and try again.');

		return array('connect' => false, 'cost' => 0);
	}

}

?>
