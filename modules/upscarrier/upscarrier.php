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

class UpsCarrier extends CarrierModule
{
	public  $id_carrier;

	private $_html = '';
	private $_postErrors = array();
	private $_webserviceError = '';
	private $_rateServiceGroupList = array();
	private $_fieldsList = array();
	private $_pickupTypeList = array();
	private $_packagingTypeList = array();
	private $_dimensionUnit = '';
	private $_weightUnit = '';
	private $_dimensionUnitList = array('CM' => 'CM', 'IN' => 'IN', 'CMS' => 'CM', 'INC' => 'IN');
	private $_weightUnitList = array('KG' => 'KGS', 'KGS' => 'KGS', 'LBS' => 'LBS', 'LB' => 'LBS');
	private $_moduleName = 'upscarrier';

	public function __construct()
	{
		global $cookie;

		$this->name = 'upscarrier';
		$this->tab = 'shipping_logistics';
		$this->version = '1.0';
		$this->author = 'PrestaShop';
		$this->limited_countries = array('us');

		parent::__construct ();

		$this->displayName = $this->l('UPS Carrier');
		$this->description = $this->l('Offer your customers, different delivery methods with UPS');

		if (self::isInstalled($this->name))
		{
			// Loading Var
			$warning = array();
			$this->loadingVar();

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
				$this->warning .= implode(' , ',$warning).$this->l('must be configured to use this module correctly.').' ';
		}
	}

	public function loadingVar()
	{
		// Loading Fields List
		$this->_fieldsList = array(
			'UPS_CARRIER_LOGIN' => $this->l('UPS Login'),
			'UPS_CARRIER_PASSWORD' => $this->l('UPS Password'),
			'UPS_CARRIER_SHIPPER_ID' => $this->l('MyUps ID'),
			'UPS_CARRIER_API_KEY' => $this->l('UPS API Key'),
			'UPS_CARRIER_PICKUP_TYPE' => $this->l('UPS Pickup Type'),
			'UPS_CARRIER_PACKAGING_TYPE' => $this->l('UPS Packaging Type'),
			'UPS_CARRIER_RATE_SERVICE_GROUP' => $this->l('UPS Rate Service Group'),
			'UPS_CARRIER_ADDRESS1' => '',
			'UPS_CARRIER_ADDRESS2' => '',
			'UPS_CARRIER_POSTAL_CODE' => '',
			'UPS_CARRIER_CITY' => '',
			'UPS_CARRIER_STATE' => '',
			'UPS_CARRIER_COUNTRY' => '',
		);

		// Loading Rate Service Group List
		$this->_rateServiceGroupList = array(
			'United States Domestic Shipments' => $this->l('United States Domestic Shipments'),
			'Shipments Originating in United States' => $this->l('Shipments Originating in United States'),
			'Shipments Originating in Puerto Rico' => $this->l('Shipments Originating in Puerto Rico'),
			'Shipments Originating in Canada' => $this->l('Shipments Originating in Canada'),
			'Shipments Originating in Mexico' => $this->l('Shipments Originating in Mexico'),
			'Polish Domestic Shipments' => $this->l('Polish Domestic Shipments'),
			'Shipments Originating in the European Union' => $this->l('Shipments Originating in the European Union'),
			'Shipments Originating in Other Countries' => $this->l('Shipments Originating in Other Countries')
		);

		// Loading Pickup Type List
		$this->_pickupTypeList = array(
			'01' => $this->l('Daily Pickup'),
			'03' => $this->l('Customer Counter'),
			'06' => $this->l('One Time Pickup'),
			'07' => $this->l('On Call Air'),
			'11' => $this->l('Suggested Retail Rates'),
			'19' => $this->l('Letter Center'),
			'20' => $this->l('Air Service Center')
		);

		// Loading Packaging Type List
		$this->_packagingTypeList = array(
			'00' => $this->l('UNKNOWN'),
			'01' => $this->l('UPS Letter'),
			'02' => $this->l('Package'),
			'03' => $this->l('Tube'),
			'04' => $this->l('Pak'),
			'21' => $this->l('Express Box'),
			'24' => $this->l('25KG Box'),
			'25' => $this->l('10KG Box'),
			'30' => $this->l('Pallet'),
			'2a' => $this->l('Small Express Box'),
			'2b' => $this->l('Medium Express Box'),
			'2c' => $this->l('Large Express Box')
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

		// Install Module
		if (!parent::install() OR !$this->registerHook('updateCarrier'))
			return false;

		return true;
	}

	public function uninstall()
	{
		global $cookie;

		// Uninstall Carriers
		Db::getInstance()->autoExecute(_DB_PREFIX_.'carrier', array('deleted' => 1), 'UPDATE', '`external_module_name` = \'upscarrier\' OR `id_carrier` IN (SELECT DISTINCT(`id_carrier`) FROM `'._DB_PREFIX_.'ups_rate_service_code`)');

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

	public function installCarriers($id_ups_rate_service_group)
	{
		// Unactive all UPS Carriers
		Db::getInstance()->autoExecute(_DB_PREFIX_.'ups_rate_service_code', array('active' => 0), 'UPDATE');

		// Get all services availables for this group
		$rateServiceList = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'ups_rate_service_code` WHERE `id_ups_rate_service_group` = '.(int)($id_ups_rate_service_group));
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
				Db::getInstance()->autoExecute(_DB_PREFIX_.'ups_rate_service_code', array('id_carrier' => (int)($id_carrier), 'id_carrier_history' => (int)($id_carrier)), 'UPDATE', '`id_ups_rate_service_code` = '.(int)($rateService['id_ups_rate_service_code']));
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
		$this->_html .= '<h2>' . $this->l('UPS Carrier').'</h2>';
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
		<legend><img src="'.$this->_path.'logo.gif" alt="" /> '.$this->l('UPS Module Status').'</legend>';

		$alert = array();
		if (!Configuration::get('UPS_CARRIER_RATE_SERVICE_GROUP'))
			$alert['rateServiceGroup'] = 1;
		if (Db::getInstance()->getValue('SELECT * FROM `'._DB_PREFIX_.'ups_rate_service_code` WHERE `id_ups_rate_service_group` = '.(int)Configuration::get('UPS_CARRIER_RATE_SERVICE_GROUP').' AND `active` = 1') < 1)
			$alert['deliveryServices'] = 1;
		if (!$this->webserviceTest())
			$alert['webserviceTest'] = 1;


		if (!count($alert))
			$this->_html .= '<img src="'._PS_IMG_.'admin/module_install.png" /><strong>'.$this->l('UPS Carrier is configured and online!').'</strong>';
		else
		{
			$this->_html .= '<img src="'._PS_IMG_.'admin/warn2.png" /><strong>'.$this->l('UPS Carrier is not configured yet, please:').'</strong>';
			$this->_html .= '<br />'.(isset($alert['rateServiceGroup']) ? '<img src="'._PS_IMG_.'admin/warn2.png" />' : '<img src="'._PS_IMG_.'admin/module_install.png" />').' 1) '.$this->l('Fill the "General Settings" form and choose your rate service group.');
			$this->_html .= '<br />'.(isset($alert['webserviceTest']) ? '<img src="'._PS_IMG_.'admin/warn2.png" />' : '<img src="'._PS_IMG_.'admin/module_install.png" />').' 2) '.$this->l('Webservice test connection').($this->_webserviceError ? ' : '.$this->_webserviceError : '');
			$this->_html .= '<br />'.(isset($alert['deliveryServices']) ? '<img src="'._PS_IMG_.'admin/warn2.png" />' : '<img src="'._PS_IMG_.'admin/module_install.png" />').' 3) '.$this->l('Select your available delivery service (you must validate your rate service group first).');
		}

		if (!is_callable('curl_exec'))
			$this->_html .= '<br /><br />'.$this->l('cURL Extension is not enabled, UPS module can work without cURL but it would be better to enable it.');

		$this->_html .= '</fieldset><div class="clear">&nbsp;</div>';
		$this->_html .= $this->_displayFormConfig();
	}

	private function _postValidation()
	{
		if (Tools::getValue('section') == 'general')
			$this->_postValidationGeneral();
		elseif (Tools::getValue('section') == 'category' && Configuration::get('UPS_CARRIER_RATE_SERVICE_GROUP'))
			$this->_postValidationCategory();
		elseif (Tools::getValue('section') == 'product' && Configuration::get('UPS_CARRIER_RATE_SERVICE_GROUP'))
			$this->_postValidationProduct();
	}

	private function _postProcess()
	{
		if (Tools::getValue('section') == 'general')
			$this->_postProcessGeneral();
		elseif (Tools::getValue('section') == 'category' && Configuration::get('UPS_CARRIER_RATE_SERVICE_GROUP'))
			$this->_postProcessCategory();
		elseif (Tools::getValue('section') == 'product' && Configuration::get('UPS_CARRIER_RATE_SERVICE_GROUP'))
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
				var country = $("#ups_carrier_country");
				country.change(function() {
					if ($("#ups_carrier_state_" + country.val()))
					{
						$(".stateInput.selected").removeClass("selected");
						if ($("#ups_carrier_state_" + country.val()).size())
							$("#ups_carrier_state_" + country.val()).addClass("selected");
						else
							$("#ups_carrier_state_none").addClass("selected");
					}
				});

				$("#configForm").submit(function() {
					$("#ups_carrier_state").val($(".stateInput.selected").val());
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
					<label>'.$this->l('Your UPS Login').' : </label>
					<div class="margin-form"><input type="text" size="20" name="ups_carrier_login" value="'.Tools::getValue('ups_carrier_login', Configuration::get('UPS_CARRIER_LOGIN')).'" /></div>
					<label>'.$this->l('Your UPS Password').' : </label>
					<div class="margin-form"><input type="text" size="20" name="ups_carrier_password" value="'.Tools::getValue('ups_carrier_password', Configuration::get('UPS_CARRIER_PASSWORD')).'" /></div>
					<label>'.$this->l('Your MyUps ID').' : </label>
					<div class="margin-form"><input type="text" size="20" name="ups_carrier_shipper_id" value="'.Tools::getValue('ups_carrier_shipper_id', Configuration::get('UPS_CARRIER_SHIPPER_ID')).'" /></div>
					<label>'.$this->l('Your UPS API Key').' : </label>
					<div class="margin-form">
						<input type="text" size="20" name="ups_carrier_api_key" value="'.Tools::getValue('ups_carrier_api_key', Configuration::get('UPS_CARRIER_API_KEY')).'" />
						<p><a href="https://www.ups.com/upsdeveloperkit" target="_blank">' . $this->l('Please click here to get your UPS API Key.') . '</a></p>
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
					<div class="margin-form"><input type="text" size="20" name="ups_carrier_address1" value="'.Tools::getValue('ups_carrier_address1', Configuration::get('UPS_CARRIER_ADDRESS1')).'" /></div>
					<label>'.$this->l('Your address line 2').' : </label>
					<div class="margin-form"><input type="text" size="20" name="ups_carrier_address2" value="'.Tools::getValue('ups_carrier_address2', Configuration::get('UPS_CARRIER_ADDRESS2')).'" /></div>
					<label>'.$this->l('Zip / Postal Code').' : </label>
					<div class="margin-form"><input type="text" size="20" name="ups_carrier_postal_code" value="'.Tools::getValue('ups_carrier_postal_code', Configuration::get('UPS_CARRIER_POSTAL_CODE')).'" /></div><br />
					<label>'.$this->l('Your City').' : </label>
					<div class="margin-form"><input type="text" size="20" name="ups_carrier_city" value="'.Tools::getValue('ups_carrier_city', Configuration::get('UPS_CARRIER_CITY')).'" /></div>
					<label>'.$this->l('Country').' : </label>
					<div class="margin-form">
						<select name="ups_carrier_country" id="ups_carrier_country">
							<option value="0">'.$this->l('Select a country ...').'</option>';
							$idcountries = array();
							foreach (Country::getCountries($cookie->id_lang) as $v)
							{
								$html .= '<option value="'.$v['id_country'].'" '.($v['id_country'] == (int)(Tools::getValue('ups_carrier_country', Configuration::get('UPS_CARRIER_COUNTRY'))) ? 'selected="selected"' : '').'>'.$v['name'].'</option>';
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
								$html .= '<select id="ups_carrier_state_'.$v['id_country'].'" class="stateInput">
									<option value="0">'.$this->l('Select a state ...').'</option>';
							}
							$html .= '<option value="'.$v['id_state'].'" '.($v['id_state'] == (int)(Tools::getValue('ups_carrier_state', Configuration::get('UPS_CARRIER_STATE'))) ? 'selected="selected"' : '').'>'.$v['name'].'</option>';		
							$id_country_current = $v['id_country'];
						}
						$html .= '</select><div id="ups_carrier_state_none" class="stateInput selected">'.$this->l('There is no state configuration for this country').'</div>
						<input type="hidden" id="ups_carrier_state" name="ups_carrier_state" value="s" />
					</div>
				</fieldset>

				<fieldset style="border: 0px;">
					<h4>'.$this->l('Service configuration').' :</h4>
					<label>'.$this->l('Rate service group').' : </label>
					<div class="margin-form">
						<select name="ups_carrier_rate_service_group">
							<option value="0">'.$this->l('Select a rate service group ...').'</option>';
							$idrateservicegroups = array();
							$rateServiceGroupList = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'ups_rate_service_group` ORDER BY `id_ups_rate_service_group`');
							foreach ($rateServiceGroupList as $v)
							{
								$html .= '<option value="'.$v['id_ups_rate_service_group'].'" '.($v['id_ups_rate_service_group'] == (int)(Tools::getValue('ups_carrier_rate_service_group', Configuration::get('UPS_CARRIER_RATE_SERVICE_GROUP'))) ? 'selected="selected"' : '').'>'.$this->_rateServiceGroupList[$v['name']].'</option>';
								$idrateservicegroups[] = $v['id_ups_rate_service_group'];
							}
						$html .= '</select>
						<p>' . $this->l('Shipments originating.') . '</p>
					</div>
					<label>'.$this->l('Pickup Type').' : </label>
						<div class="margin-form">
							<select name="ups_carrier_pickup_type">
								<option value="0">'.$this->l('Select a pickup type ...').'</option>';
								$idpickups = array();
								foreach($this->_pickupTypeList as $kpickup => $vpickup)
								{
									$html .= '<option value="'.$kpickup.'" '.($kpickup == (int)(Tools::getValue('ups_carrier_pickup_type', Configuration::get('UPS_CARRIER_PICKUP_TYPE'))) ? 'selected="selected"' : '').'>'.$vpickup.'</option>';
									$idpickups[] = $kpickup;
								}
					$html .= '</select>
					<p>' . $this->l('Select pickup type from within the list.') . '</p>
					</div>
					<label>'.$this->l('Packaging Type').' : </label>
						<div class="margin-form">
							<select name="ups_carrier_packaging_type">
								<option value="0">'.$this->l('Select a packaging type ...').'</option>';
								$idpackagings = array();
								foreach($this->_packagingTypeList as $kpackaging => $vpackaging)
								{
									$html .= '<option value="'.$kpackaging.'" '.($kpackaging == (Tools::getValue('ups_carrier_packaging_type', Configuration::get('UPS_CARRIER_PACKAGING_TYPE'))) ? 'selected="selected"' : '').'>'.$vpackaging.'</option>';
									$idpackagings[] = $kpackaging;
								}
					$html .= '</select>
					<p>' . $this->l('Select packaging type from within the list.') . '</p>
					</div>';

					if (Configuration::get('UPS_CARRIER_RATE_SERVICE_GROUP'))
					{
						$html .= '<label>'.$this->l('Delivery Service').' : </label>
							<div class="margin-form">';
								$rateServiceList = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'ups_rate_service_code` WHERE `id_ups_rate_service_group` = '.(int)Configuration::get('UPS_CARRIER_RATE_SERVICE_GROUP'));	
								foreach($rateServiceList as $rateService)
									$html .= '<input type="checkbox" name="service[]" value="'.$rateService['id_ups_rate_service_code'].'" '.(($rateService['active'] == 1) ? 'checked="checked"' : '').' /> '.$rateService['service'].' '.($this->webserviceTest($rateService['code']) ? '('.$this->l('Available').')' : '('.$this->l('Not available').')').'<br />';
						$html .= '
						<p>' . $this->l('Choose the delivery service which will be available for customers.') . '</p>
						</div>';
					}
				$html .= '</fieldset>
				
				<div class="margin-form"><input class="button" name="submitSave" type="submit"></div>
			</form>

			<script>
				var id_country = '.(int)(Tools::getValue('ups_carrier_country', Configuration::get('UPS_CARRIER_COUNTRY'))).';
				if ($("#ups_carrier_state_" + id_country))
				{
					$(".stateInput.selected").removeClass("selected");
					if ($("#ups_carrier_state_" + id_country).size())
						$("#ups_carrier_state_" + id_country).addClass("selected");
					else
						$("#ups_carrier_state_none").addClass("selected");
				}
			</script>';
		return $html;
	}

	private function _postValidationGeneral()
	{
		// Check configuration values
		if (Tools::getValue('ups_carrier_login') == NULL)
			$this->_postErrors[]  = $this->l('Your UPS login is not specified');
		elseif (Tools::getValue('ups_carrier_password') == NULL)
			$this->_postErrors[]  = $this->l('Your UPS password is not specified');
		elseif (Tools::getValue('ups_carrier_shipper_id') == NULL)
			$this->_postErrors[]  = $this->l('Your MyUps ID is not specified');
		elseif (Tools::getValue('ups_carrier_api_key') == NULL)
			$this->_postErrors[]  = $this->l('Your UPS API Key is not specified');
		elseif (Tools::getValue('ups_carrier_postal_code') == NULL)
			$this->_postErrors[]  = $this->l('Your Zip / Postal code is not specified');
		elseif (Tools::getValue('ups_carrier_city') == NULL)
			$this->_postErrors[]  = $this->l('Your city is not specified');
		elseif (Tools::getValue('ups_carrier_country') == NULL OR Tools::getValue('ups_carrier_country') == 0)
			$this->_postErrors[]  = $this->l('Your country is not specified');
		elseif (Tools::getValue('ups_carrier_pickup_type') == NULL OR Tools::getValue('ups_carrier_pickup_type') == 0)
			$this->_postErrors[]  = $this->l('Your pickup type is not specified');
		elseif (Tools::getValue('ups_carrier_packaging_type') == NULL OR Tools::getValue('ups_carrier_packaging_type') == 0)
			$this->_postErrors[]  = $this->l('Your packaging type is not specified');
		elseif (Tools::getValue('ups_carrier_rate_service_group') == NULL OR Tools::getValue('ups_carrier_rate_service_group') == 0)
			$this->_postErrors[]  = $this->l('Your rate service group is not specified');

		// Check ups webservice availibity
		if (!$this->_postErrors)
		{

			// Install Carriers (if not)
			Configuration::updateValue('UPS_CARRIER_RATE_SERVICE_GROUP', Tools::getValue('ups_carrier_rate_service_group'));
			$this->installCarriers((int)Tools::getValue('ups_carrier_rate_service_group'));

			// If no errors appear, the carrier is being activated, else, the carrier is being deactivated
			if (!$this->_postErrors)
			{
				// Get available services
				$serviceSelected = Tools::getValue('service');

				// Active available carrier
				if ($serviceSelected)
					foreach ($serviceSelected as $ss)
					{
						$id_carrier = Db::getInstance()->getValue('SELECT `id_carrier` FROM `'._DB_PREFIX_.'ups_rate_service_code` WHERE `id_ups_rate_service_code` = '.(int)($ss));
						Db::getInstance()->autoExecute(_DB_PREFIX_.'ups_rate_service_code', array('active' => 1), 'UPDATE', '`id_ups_rate_service_code` = '.(int)($ss));
					}
			}

			// All new configurations values are saved to be sure to test webservices with it
			Configuration::updateValue('UPS_CARRIER_LOGIN', Tools::getValue('ups_carrier_login'));
			Configuration::updateValue('UPS_CARRIER_PASSWORD', Tools::getValue('ups_carrier_password'));
			Configuration::updateValue('UPS_CARRIER_SHIPPER_ID', Tools::getValue('ups_carrier_shipper_id'));
			Configuration::updateValue('UPS_CARRIER_API_KEY', Tools::getValue('ups_carrier_api_key'));
			Configuration::updateValue('UPS_CARRIER_PICKUP_TYPE', Tools::getValue('ups_carrier_pickup_type'));
			Configuration::updateValue('UPS_CARRIER_PACKAGING_TYPE', Tools::getValue('ups_carrier_packaging_type'));
			Configuration::updateValue('UPS_CARRIER_ADDRESS1', Tools::getValue('ups_carrier_address1'));
			Configuration::updateValue('UPS_CARRIER_ADDRESS2', Tools::getValue('ups_carrier_address2'));
			Configuration::updateValue('UPS_CARRIER_POSTAL_CODE', Tools::getValue('ups_carrier_postal_code'));
			Configuration::updateValue('UPS_CARRIER_CITY', Tools::getValue('ups_carrier_city'));
			Configuration::updateValue('UPS_CARRIER_STATE', Tools::getValue('ups_carrier_state'));
			Configuration::updateValue('UPS_CARRIER_COUNTRY', Tools::getValue('ups_carrier_country'));
			Configuration::updateValue('PS_WEIGHT_UNIT', $this->_weightUnitList[strtoupper(Tools::getValue('ps_weight_unit'))]);
			Configuration::updateValue('PS_DIMENSION_UNIT', $this->_dimensionUnitList[strtoupper(Tools::getValue('ps_dimension_unit'))]);
			if (isset($this->_weightUnitList[strtoupper(Tools::getValue('ps_weight_unit'))]))
				$this->_weightUnit = $this->_weightUnitList[strtoupper(Tools::getValue('ps_weight_unit'))];
			if (isset($this->_dimensionUnitList[strtoupper(Tools::getValue('ps_dimension_unit'))]))
				$this->_dimensionUnit = $this->_dimensionUnitList[strtoupper(Tools::getValue('ps_dimension_unit'))];
			if (!$this->webserviceTest())
				$this->_postErrors[]  = $this->l('Prestashop could not connect to UPS webservices').' :<br />'.($this->_webserviceError ? $this->_webserviceError : $this->l('No error description found'));
		}
	}

	private function _postProcessGeneral()
	{
		// Saving new configurations
		if (Configuration::updateValue('UPS_CARRIER_LOGIN', Tools::getValue('ups_carrier_login')) AND
			Configuration::updateValue('UPS_CARRIER_PASSWORD', Tools::getValue('ups_carrier_password')) AND
			Configuration::updateValue('UPS_CARRIER_SHIPPER_ID', Tools::getValue('ups_carrier_shipper_id')) AND
			Configuration::updateValue('UPS_CARRIER_API_KEY', Tools::getValue('ups_carrier_api_key')) AND
			Configuration::updateValue('UPS_CARRIER_PICKUP_TYPE', Tools::getValue('ups_carrier_pickup_type')) AND
			Configuration::updateValue('UPS_CARRIER_PACKAGING_TYPE', Tools::getValue('ups_carrier_packaging_type')) AND
			Configuration::updateValue('UPS_CARRIER_POSTAL_CODE', Tools::getValue('ups_carrier_postal_code')) AND
			Configuration::updateValue('UPS_CARRIER_CITY', Tools::getValue('ups_carrier_city')) AND
			Configuration::updateValue('UPS_CARRIER_STATE', Tools::getValue('ups_carrier_state')) AND
			Configuration::updateValue('UPS_CARRIER_COUNTRY', Tools::getValue('ups_carrier_country')) AND
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

	private function _isPostCheck($id_ups_rate_service_code)
	{
		$services = Tools::getValue('service');
		if ($services)
			foreach ($services as $s)
				if ($s == $id_ups_rate_service_code)
					return 1;
		return 0;
	}
	
	private function _displayFormCategory()
	{
		global $cookie;

		// Check if the module is configured
		if (!Configuration::get('UPS_CARRIER_RATE_SERVICE_GROUP'))
			return '<p><b>'.$this->l('You have to configure "General Settings" tab before using this tab.').'</b></p><br />';

		// Display header
		$html = '<p><b>'.$this->l('In this tab, you can set a specific configuration for each category.').'</b></p><br />
		<table class="table tableDnD" cellpadding="0" cellspacing="0" width="90%">
			<thead>
				<tr class="nodrag nodrop">
					<th>'.$this->l('ID Config').'</th>
					<th>'.$this->l('Category').'</th>
					<th>'.$this->l('Packaging type').'</th>
					<th>'.$this->l('Additional charges').'</th>
					<th>'.$this->l('Services').'</th>
					<th>'.$this->l('Actions').'</th>
				</tr>
			</thead>
			<tbody>';

		// Loading config list
		$configCategoryList = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'ups_rate_config` WHERE `id_category` > 0');
		if (!$configCategoryList)
			$html .= '<tr><td colspan="6">'.$this->l('There is no specific UPS configuration for categories at this point.').'</td></tr>';
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
			FROM `'._DB_PREFIX_.'ups_rate_config_service` urcs
			LEFT JOIN `'._DB_PREFIX_.'ups_rate_service_code` ursc ON (ursc.`id_ups_rate_service_code` = urcs.`id_ups_rate_service_code`)
			WHERE ursc.`id_ups_rate_service_group` = '.(int)(Configuration::get('UPS_CARRIER_RATE_SERVICE_GROUP')).'
			AND urcs.`id_ups_rate_config` = '.(int)$c['id_ups_rate_config']);
			foreach ($servicesTab as $s)
				$services .= $s['service'].'<br />';

			// Display line
			$alt = 0;
			if ($k % 2 != 0)
				$alt = ' class="alt_row"';
			$html .= '
				<tr'.$alt.'>
					<td>'.$c['id_ups_rate_config'].'</td>
					<td>'.$path.'</td>
					<td>'.(isset($this->_packagingTypeList[$c['packaging_type_code']]) ? $this->_packagingTypeList[$c['packaging_type_code']] : '-').'</td>
					<td>'.$c['additionnal_charges'].' '.$configCurrency->sign.'</td>
					<td>'.$services.'</td>
					<td>
						<a href="index.php?tab='.$_GET['tab'].'&configure='.$_GET['configure'].'&token='.$_GET['token'].'&tab_module='.$_GET['tab_module'].'&module_name='.$_GET['module_name'].'&id_tab=2&section=category&action=edit&id_ups_rate_config='.(int)($c['id_ups_rate_config']).'" style="float: left;">
							<img src="'._PS_IMG_.'admin/edit.gif" />
						</a>
						<form action="index.php?tab='.$_GET['tab'].'&configure='.$_GET['configure'].'&token='.$_GET['token'].'&tab_module='.$_GET['tab_module'].'&module_name='.$_GET['module_name'].'&id_tab=2&section=category&action=delete&id_ups_rate_config='.(int)($c['id_ups_rate_config']).'&id_category='.(int)($c['id_category']).'" method="post" class="form" style="float: left;">
							<input name="submitSave" type="image" src="'._PS_IMG_.'admin/delete.gif" OnClick="return confirm(\''.$this->l('Are you sure you want to delete this specific UPS configuration for this category ?').'\');" />
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
			$configSelected = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ups_rate_config` WHERE `id_ups_rate_config` = '.(int)(Tools::getValue('id_ups_rate_config')));
			
			// Category Path
			$path = '';
			$pathTab = $this->_getPathInTab($configSelected['id_category']);
			foreach ($pathTab as $p)
			{
				if (!empty($path)) { $path .= ' > '; }
				$path .= $p;
			}

			$html .= '<p align="center"><b>'.$this->l('Update a rule').' (<a href="index.php?tab='.$_GET['tab'].'&configure='.$_GET['configure'].'&token='.$_GET['token'].'&tab_module='.$_GET['tab_module'].'&module_name='.$_GET['module_name'].'&id_tab=2&section=category&action=add">'.$this->l('Add a rule').' ?</a>)</b></p>
					<form action="index.php?tab='.$_GET['tab'].'&configure='.$_GET['configure'].'&token='.$_GET['token'].'&tab_module='.$_GET['tab_module'].'&module_name='.$_GET['module_name'].'&id_tab=2&section=category&action=edit&id_ups_rate_config='.(int)(Tools::getValue('id_ups_rate_config')).'" method="post" class="form">
						<label>'.$this->l('Category').' :</label>
						<div class="margin-form" style="padding: 0.2em 0.5em 0 0; font-size: 12px;">'.$path.' <input type="hidden" name="id_category" value="'.(int)($configSelected['id_category']).'" /></div><br clear="left" />
						<label>'.$this->l('Packaging Type').' : </label>
							<div class="margin-form">
								<select name="packaging_type_code">
									<option value="0">'.$this->l('Select a packaging type ...').'</option>';
									foreach($this->_packagingTypeList as $kpackaging => $vpackaging)
										$html .= '<option value="'.$kpackaging.'" '.($kpackaging == pSQL(Tools::getValue('packaging_type_code', $configSelected['packaging_type_code'])) ? 'selected="selected"' : '').'>'.$vpackaging.'</option>';
						$html .= '</select>
						</div>
						<label>'.$this->l('Additional charges').' : </label>
						<div class="margin-form"><input type="text" size="20" name="additionnal_charges" value="'.Tools::getValue('additionnal_charges', $configSelected['additionnal_charges']).'" /></div><br />
						<label>'.$this->l('Delivery Service').' : </label>
							<div class="margin-form">';
								$rateServiceList = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'ups_rate_service_code` WHERE `id_ups_rate_service_group` = '.(int)Configuration::get('UPS_CARRIER_RATE_SERVICE_GROUP'));	
								foreach($rateServiceList as $rateService)
								{
									$configServiceSelected = Db::getInstance()->getValue('SELECT `id_ups_rate_service_code` FROM `'._DB_PREFIX_.'ups_rate_config_service` WHERE `id_ups_rate_config` = '.(int)(Tools::getValue('id_ups_rate_config')).' AND `id_ups_rate_service_code` = '.(int)($rateService['id_ups_rate_service_code']));
									$html .= '<input type="checkbox" name="service[]" value="'.$rateService['id_ups_rate_service_code'].'" '.(($this->_isPostCheck($rateService['id_ups_rate_service_code']) == 1 || $configServiceSelected > 0) ? 'checked="checked"' : '').' /> '.$rateService['service'].'<br />';
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
						<label>'.$this->l('Packaging Type').' : </label>
							<div class="margin-form">
								<select name="packaging_type_code">
									<option value="0">'.$this->l('Select a packaging type ...').'</option>';
									foreach($this->_packagingTypeList as $kpackaging => $vpackaging)
										$html .= '<option value="'.$kpackaging.'" '.($kpackaging === pSQL(Tools::getValue('packaging_type_code')) ? 'selected="selected"' : '').'>'.$vpackaging.'</option>';
						$html .= '</select>
						</div>
						<label>'.$this->l('Additional charges').' : </label>
						<div class="margin-form"><input type="text" size="20" name="additionnal_charges" value="'.Tools::getValue('additionnal_charges').'" /></div><br />
						<label>'.$this->l('Delivery Service').' : </label>
							<div class="margin-form">';
								$rateServiceList = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'ups_rate_service_code` WHERE `id_ups_rate_service_group` = '.(int)Configuration::get('UPS_CARRIER_RATE_SERVICE_GROUP'));	
								foreach($rateServiceList as $rateService)
									$html .= '<input type="checkbox" name="service[]" value="'.$rateService['id_ups_rate_service_code'].'" '.(($this->_isPostCheck($rateService['id_ups_rate_service_code']) == 1) ? 'checked="checked"' : '').' /> '.$rateService['service'].'<br />';
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
			$id_ups_rate_config = Db::getInstance()->getValue('SELECT `id_ups_rate_config` FROM `'._DB_PREFIX_.'ups_rate_config` WHERE `id_category` = '.(int)Tools::getValue('id_category'));

			// Check if a config does not exist in Add case
			if ($id_ups_rate_config > 0 && Tools::getValue('action') == 'add')
				$this->_postErrors[]  = $this->l('This category already has a specific UPS configuration.');

			// Check if a config exists and if the IDs config correspond in Upd case
			if (Tools::getValue('action') == 'edit' && (!isset($id_ups_rate_config) || $id_ups_rate_config != Tools::getValue('id_ups_rate_config')))
				$this->_postErrors[]  = $this->l('An error occurred, please try again.');

			// Check if a config exists in Delete case
			if (Tools::getValue('action') == 'delete' && !isset($id_ups_rate_config))
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
				'packaging_type_code' => pSQL(Tools::getValue('packaging_type_code')),
				'additionnal_charges' => pSQL(Tools::getValue('additionnal_charges')),
				'date_add' => pSQL($date),
				'date_upd' => pSQL($date)
			);
			Db::getInstance()->autoExecute(_DB_PREFIX_.'ups_rate_config', $addTab, 'INSERT');
			$id_ups_rate_config = Db::getInstance()->Insert_ID();
			foreach ($services as $s)
			{
				$addTab = array('id_ups_rate_service_code' => pSQL($s), 'id_ups_rate_config' => (int)$id_ups_rate_config, 'date_add' => pSQL($date), 'date_upd' => pSQL($date));
				Db::getInstance()->autoExecute(_DB_PREFIX_.'ups_rate_config_service', $addTab, 'INSERT');
			}

			// Display Results
			if ($id_ups_rate_config > 0)
				$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
			else
				$this->_html .= $this->displayErrors($this->l('Settings failed'));
		}

		// Update Script
		if (Tools::getValue('action') == 'edit' && Tools::getValue('id_ups_rate_config'))
		{
			$updTab = array(
				'id_currency' => (int)(Configuration::get('PS_CURRENCY_DEFAULT')),
				'packaging_type_code' => pSQL(Tools::getValue('packaging_type_code')),
				'additionnal_charges' => pSQL(Tools::getValue('additionnal_charges')),
				'date_upd' => pSQL($date)
			);
			$result = Db::getInstance()->autoExecute(_DB_PREFIX_.'ups_rate_config', $updTab, 'UPDATE', '`id_ups_rate_config` = '.(int)Tools::getValue('id_ups_rate_config'));
			Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'ups_rate_config_service` WHERE `id_ups_rate_config` = '.(int)Tools::getValue('id_ups_rate_config'));
			foreach ($services as $s)
			{
				$addTab = array('id_ups_rate_service_code' => pSQL($s), 'id_ups_rate_config' => (int)Tools::getValue('id_ups_rate_config'), 'date_add' => pSQL($date), 'date_upd' => pSQL($date));
				Db::getInstance()->autoExecute(_DB_PREFIX_.'ups_rate_config_service', $addTab, 'INSERT');
			}

			// Display Results
			if ($result)
				$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
			else
				$this->_html .= $this->displayErrors($this->l('Settings failed'));
		}

		// Delete Script
		if (Tools::getValue('action') == 'delete' && Tools::getValue('id_ups_rate_config'))
		{
			$result1 = Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'ups_rate_config` WHERE `id_ups_rate_config` = '.(int)Tools::getValue('id_ups_rate_config'));
			$result2 = Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'ups_rate_config_service` WHERE `id_ups_rate_config` = '.(int)Tools::getValue('id_ups_rate_config'));

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
		if (!Configuration::get('UPS_CARRIER_RATE_SERVICE_GROUP'))
			return '<p><b>'.$this->l('You have to configure "General Settings" tab before using this tab.').'</b></p><br />';

		// Display header
		$html = '<p><b>'.$this->l('In this tab, you can set a specific configuration for each product.').'</b></p><br />
		<table class="table tableDnD" cellpadding="0" cellspacing="0" width="90%">
			<thead>
				<tr class="nodrag nodrop">
					<th>'.$this->l('ID Config').'</th>
					<th>'.$this->l('Product').'</th>
					<th>'.$this->l('Packaging type').'</th>
					<th>'.$this->l('Additional charges').'</th>
					<th>'.$this->l('Services').'</th>
					<th>'.$this->l('Actions').'</th>
				</tr>
			</thead>
			<tbody>';

		// Loading config list
		$configProductList = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'ups_rate_config` WHERE `id_product` > 0');
		if (!$configProductList)
			$html .= '<tr><td colspan="6">'.$this->l('There is no specific UPS configuration for products at this point.').'</td></tr>';
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
			FROM `'._DB_PREFIX_.'ups_rate_config_service` urcs
			LEFT JOIN `'._DB_PREFIX_.'ups_rate_service_code` ursc ON (ursc.`id_ups_rate_service_code` = urcs.`id_ups_rate_service_code`)
			WHERE ursc.`id_ups_rate_service_group` = '.(int)(Configuration::get('UPS_CARRIER_RATE_SERVICE_GROUP')).'
			AND urcs.`id_ups_rate_config` = '.(int)$c['id_ups_rate_config']);
			foreach ($servicesTab as $s)
				$services .= $s['service'].'<br />';

			// Display line
			$alt = 0;
			if ($k % 2 != 0)
				$alt = ' class="alt_row"';
			$html .= '
				<tr'.$alt.'>
					<td>'.$c['id_ups_rate_config'].'</td>
					<td>'.$product->name.'</td>
					<td>'.(isset($this->_packagingTypeList[$c['packaging_type_code']]) ? $this->_packagingTypeList[$c['packaging_type_code']] : '-').'</td>
					<td>'.$c['additionnal_charges'].' '.$configCurrency->sign.'</td>
					<td>'.$services.'</td>
					<td>
						<a href="index.php?tab='.$_GET['tab'].'&configure='.$_GET['configure'].'&token='.$_GET['token'].'&tab_module='.$_GET['tab_module'].'&module_name='.$_GET['module_name'].'&id_tab=3&section=product&action=edit&id_ups_rate_config='.(int)($c['id_ups_rate_config']).'" style="float: left;">
							<img src="'._PS_IMG_.'admin/edit.gif" />
						</a>
						<form action="index.php?tab='.$_GET['tab'].'&configure='.$_GET['configure'].'&token='.$_GET['token'].'&tab_module='.$_GET['tab_module'].'&module_name='.$_GET['module_name'].'&id_tab=3&section=product&action=delete&id_ups_rate_config='.(int)($c['id_ups_rate_config']).'&id_product='.(int)($c['id_product']).'" method="post" class="form" style="float: left;">
							<input name="submitSave" type="image" src="'._PS_IMG_.'admin/delete.gif" OnClick="return confirm(\''.$this->l('Are you sure you want to delete this specific UPS configuration for this product ?').'\');" />
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
			$configSelected = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ups_rate_config` WHERE `id_ups_rate_config` = '.(int)(Tools::getValue('id_ups_rate_config')));
			$product = new Product((int)$configSelected['id_product'], false, (int)$cookie->id_lang);

			$html .= '<p align="center"><b>'.$this->l('Update a rule').' (<a href="index.php?tab='.$_GET['tab'].'&configure='.$_GET['configure'].'&token='.$_GET['token'].'&tab_module='.$_GET['tab_module'].'&module_name='.$_GET['module_name'].'&id_tab=3&section=product&action=add">'.$this->l('Add a rule').' ?</a>)</b></p>
					<form action="index.php?tab='.$_GET['tab'].'&configure='.$_GET['configure'].'&token='.$_GET['token'].'&tab_module='.$_GET['tab_module'].'&module_name='.$_GET['module_name'].'&id_tab=3&section=product&action=edit&id_ups_rate_config='.(int)(Tools::getValue('id_ups_rate_config')).'" method="post" class="form">
						<label>'.$this->l('Product').' :</label>
						<div class="margin-form" style="padding: 0.2em 0.5em 0 0; font-size: 12px;">'.$product->name.' <input type="hidden" name="id_product" value="'.(int)($configSelected['id_product']).'" /></div><br clear="left" />
						<label>'.$this->l('Packaging Type').' : </label>
							<div class="margin-form">
								<select name="packaging_type_code">
									<option value="0">'.$this->l('Select a packaging type ...').'</option>';
									foreach($this->_packagingTypeList as $kpackaging => $vpackaging)
										$html .= '<option value="'.$kpackaging.'" '.($kpackaging == pSQL(Tools::getValue('packaging_type_code', $configSelected['packaging_type_code'])) ? 'selected="selected"' : '').'>'.$vpackaging.'</option>';
						$html .= '</select>
						</div>
						<label>'.$this->l('Additional charges').' : </label>
						<div class="margin-form"><input type="text" size="20" name="additionnal_charges" value="'.Tools::getValue('additionnal_charges', $configSelected['additionnal_charges']).'" /></div><br />
						<label>'.$this->l('Delivery Service').' : </label>
							<div class="margin-form">';
								$rateServiceList = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'ups_rate_service_code` WHERE `id_ups_rate_service_group` = '.(int)Configuration::get('UPS_CARRIER_RATE_SERVICE_GROUP'));	
								foreach($rateServiceList as $rateService)
								{
									$configServiceSelected = Db::getInstance()->getValue('SELECT `id_ups_rate_service_code` FROM `'._DB_PREFIX_.'ups_rate_config_service` WHERE `id_ups_rate_config` = '.(int)(Tools::getValue('id_ups_rate_config')).' AND `id_ups_rate_service_code` = '.(int)($rateService['id_ups_rate_service_code']));
									$html .= '<input type="checkbox" name="service[]" value="'.$rateService['id_ups_rate_service_code'].'" '.(($this->_isPostCheck($rateService['id_ups_rate_service_code']) == 1 || $configServiceSelected > 0) ? 'checked="checked"' : '').' /> '.$rateService['service'].'<br />';
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
						<label>'.$this->l('Packaging Type').' : </label>
							<div class="margin-form">
								<select name="packaging_type_code">
									<option value="0">'.$this->l('Select a packaging type ...').'</option>';
									foreach($this->_packagingTypeList as $kpackaging => $vpackaging)
										$html .= '<option value="'.$kpackaging.'" '.($kpackaging === pSQL(Tools::getValue('packaging_type_code')) ? 'selected="selected"' : '').'>'.$vpackaging.'</option>';
						$html .= '</select>
						</div>
						<label>'.$this->l('Additional charges').' : </label>
						<div class="margin-form"><input type="text" size="20" name="additionnal_charges" value="'.Tools::getValue('additionnal_charges').'" /></div><br />
						<label>'.$this->l('Delivery Service').' : </label>
							<div class="margin-form">';
								$rateServiceList = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'ups_rate_service_code` WHERE `id_ups_rate_service_group` = '.(int)Configuration::get('UPS_CARRIER_RATE_SERVICE_GROUP'));	
								foreach($rateServiceList as $rateService)
									$html .= '<input type="checkbox" name="service[]" value="'.$rateService['id_ups_rate_service_code'].'" '.(($this->_isPostCheck($rateService['id_ups_rate_service_code']) == 1) ? 'checked="checked"' : '').' /> '.$rateService['service'].'<br />';
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
			$id_ups_rate_config = Db::getInstance()->getValue('SELECT `id_ups_rate_config` FROM `'._DB_PREFIX_.'ups_rate_config` WHERE `id_product` = '.(int)Tools::getValue('id_product'));

			// Check if a config does not exist in Add case
			if ($id_ups_rate_config > 0 && Tools::getValue('action') == 'add')
				$this->_postErrors[]  = $this->l('This product already has a specific UPS configuration.');

			// Check if a config exists and if the IDs config correspond in Upd case
			if (Tools::getValue('action') == 'edit' && (!isset($id_ups_rate_config) || $id_ups_rate_config != Tools::getValue('id_ups_rate_config')))
				$this->_postErrors[]  = $this->l('An error occurred, please try again.');

			// Check if a config exists in Delete case
			if (Tools::getValue('action') == 'delete' && !isset($id_ups_rate_config))
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
				'packaging_type_code' => pSQL(Tools::getValue('packaging_type_code')),
				'additionnal_charges' => pSQL(Tools::getValue('additionnal_charges')),
				'date_add' => pSQL($date),
				'date_upd' => pSQL($date)
			);
			Db::getInstance()->autoExecute(_DB_PREFIX_.'ups_rate_config', $addTab, 'INSERT');
			$id_ups_rate_config = Db::getInstance()->Insert_ID();
			foreach ($services as $s)
			{
				$addTab = array('id_ups_rate_service_code' => pSQL($s), 'id_ups_rate_config' => (int)$id_ups_rate_config, 'date_add' => pSQL($date), 'date_upd' => pSQL($date));
				Db::getInstance()->autoExecute(_DB_PREFIX_.'ups_rate_config_service', $addTab, 'INSERT');
			}

			// Display Results
			if ($id_ups_rate_config > 0)
				$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
			else
				$this->_html .= $this->displayErrors($this->l('Settings failed'));
		}

		// Update Script
		if (Tools::getValue('action') == 'edit' && Tools::getValue('id_ups_rate_config'))
		{
			$updTab = array(
				'id_currency' => (int)(Configuration::get('PS_CURRENCY_DEFAULT')),
				'packaging_type_code' => pSQL(Tools::getValue('packaging_type_code')),
				'additionnal_charges' => pSQL(Tools::getValue('additionnal_charges')),
				'date_upd' => pSQL($date)
			);
			$result = Db::getInstance()->autoExecute(_DB_PREFIX_.'ups_rate_config', $updTab, 'UPDATE', '`id_ups_rate_config` = '.(int)Tools::getValue('id_ups_rate_config'));
			Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'ups_rate_config_service` WHERE `id_ups_rate_config` = '.(int)Tools::getValue('id_ups_rate_config'));
			foreach ($services as $s)
			{
				$addTab = array('id_ups_rate_service_code' => pSQL($s), 'id_ups_rate_config' => (int)Tools::getValue('id_ups_rate_config'), 'date_add' => pSQL($date), 'date_upd' => pSQL($date));
				Db::getInstance()->autoExecute(_DB_PREFIX_.'ups_rate_config_service', $addTab, 'INSERT');
			}

			// Display Results
			if ($result)
				$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
			else
				$this->_html .= $this->displayErrors($this->l('Settings failed'));
		}

		// Delete Script
		if (Tools::getValue('action') == 'delete' && Tools::getValue('id_ups_rate_config'))
		{
			$result1 = Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'ups_rate_config` WHERE `id_ups_rate_config` = '.(int)Tools::getValue('id_ups_rate_config'));
			$result2 = Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'ups_rate_config_service` WHERE `id_ups_rate_config` = '.(int)Tools::getValue('id_ups_rate_config'));

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
		return '<p><b>'.$this->l('Welcome to the PrestaShop UPS Module configurator!').'</b></p>
		<p>'.$this->l('This section will help you to understand how to configure this module correctly.').'</p>
		<br />
		<p><b><u>1. '.$this->l('General Settings').'</u></b></p>
		<p>'.$this->l('See below for the description of each field :').'</p>
		<p><b>'.$this->l('Your UPS Login, UPS Password, MyUps ID, UPS API Key').' :</b> '.$this->l('Please subscribe to the UPS website at this address.').' <a href="https://www.ups.com/upsdeveloperkit" target="_blank">https://www.ups.com/upsdeveloperkit</a></p>
		<p><b>'.$this->l('Zip / Postal Code').' :</b> '.$this->l('This field must be the Zip / Postal code of your package starting point.').'</p>
		<p><b>'.$this->l('Country').' :</b> '.$this->l('This field must be the country of your package starting point.').'</p>
		<p><b>'.$this->l('Rate service group').' :</b> '.$this->l('This field must be within the two fields above.').'</p>
		<p><b>'.$this->l('Pickup Type').' :</b> '.$this->l('This field corresponds to the pickup type you prefer with UPS.').'</p>
		<p><b>'.$this->l('Packaging Type').' :</b> '.$this->l('This field corresponds to the default packaging type (when there is no specific configuration for the product or the category product).').'</p>
		<p><b>'.$this->l('Delivery Service').' :</b> '.$this->l('These checkboxes correspond to the delivery services you want to be available (when there is no specific configuration for the product or the category product).').'</p>
		<br />
		<p><b><u>2. '.$this->l('Categories Settings').'</u></b></p>
		<p>'.$this->l('This section allows you to define a specific UPS configuration for each product category (such as Packaging Type and Additional charges).').'</p>
		<br />
		<p><b><u>3. '.$this->l('Products Settings').'</u></b></p>
		<p>'.$this->l('This section allows you to define a specific UPS configuration for each product (such as Packaging Type and Additional charges).').'</p>
		<br />
		';
	}

	public function hookupdateCarrier($params)
	{
		if ((int)($params['id_carrier']) != (int)($params['carrier']->id))
		{
			$serviceSelected = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ups_rate_service_code` WHERE `id_carrier` = '.(int)$params['id_carrier']);
			$update = array('id_carrier' => (int)($params['carrier']->id), 'id_carrier_history' => pSQL($serviceSelected['id_carrier_history'].'|'.(int)($params['carrier']->id)));
			Db::getInstance()->autoExecute(_DB_PREFIX_.'ups_rate_service_code', $update, 'UPDATE', '`id_carrier` = '.(int)$params['id_carrier']);
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
		SELECT * FROM `"._DB_PREFIX_."ups_cache`
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
		Db::getInstance()->autoExecute(_DB_PREFIX_.'ups_cache', $insert, 'INSERT');
	}

	public function loadShippingCostConfig($product)
	{
		// Init var
		$config = array();
	
		// Check if there is a specific product configuration
		if ($product['id_product'] > 0)
		{
			$productConfiguration = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ups_rate_config` WHERE `id_product` = '.(int)($product['id_product']));
			if ($productConfiguration['id_ups_rate_config'])
			{
				$servicesConfiguration = Db::getInstance()->ExecuteS('
				SELECT urcs.*, ursc.`id_carrier`
				FROM `'._DB_PREFIX_.'ups_rate_config_service` urcs
				LEFT JOIN `'._DB_PREFIX_.'ups_rate_service_code` ursc ON (ursc.`id_ups_rate_service_code` = urcs.`id_ups_rate_service_code`)
				WHERE `id_ups_rate_config` = '.(int)($productConfiguration['id_ups_rate_config']));
				foreach ($servicesConfiguration as $service)
					$productConfiguration['services'][$service['id_ups_rate_service_code']] = $service;
				return $productConfiguration;
			}
		}

		// Check if there is a specific category configuration
		if ($product['id_category_default'] > 0)
		{
			$categoryConfiguration = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ups_rate_config` WHERE `id_category` = '.(int)($product['id_category_default']));
			if ($categoryConfiguration['id_ups_rate_config'])
			{
				$servicesConfiguration = Db::getInstance()->ExecuteS('
				SELECT urcs.*, ursc.`id_carrier`
				FROM `'._DB_PREFIX_.'ups_rate_config_service` urcs
				LEFT JOIN `'._DB_PREFIX_.'ups_rate_service_code` ursc ON (ursc.`id_ups_rate_service_code` = urcs.`id_ups_rate_service_code`)
				WHERE `id_ups_rate_config` = '.(int)($categoryConfiguration['id_ups_rate_config']));
				foreach ($servicesConfiguration as $service)
					$categoryConfiguration['services'][$service['id_ups_rate_service_code']] = $service;
				return $categoryConfiguration;
			}
		}

		// Return general config
		$config['packaging_type_code'] = Configuration::get('UPS_CARRIER_PACKAGING_TYPE');
		$servicesConfiguration = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'ups_rate_service_code` WHERE `id_ups_rate_service_group` = '.(int)(Configuration::get('UPS_CARRIER_RATE_SERVICE_GROUP')).' AND `active` = 1');
		foreach ($servicesConfiguration as $service)
			$config['services'][$service['id_ups_rate_service_code']] = $service;
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
			$serviceSelected = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ups_rate_service_code` WHERE `id_carrier` = '.(int)($this->id_carrier));
			if (!isset($config['services'][$serviceSelected['id_ups_rate_service_code']]))
				return false;

			// Load param product
			$wsParams['service'] = $serviceSelected['code'];
			for ($qty = 0; $qty < $product['quantity']; $qty++)
				$wsParams['package_list'][] = array(
					'width' => ($product['width'] ? $product['width'] : 1),
					'height' => ($product['height'] ? $product['height'] : 1),
					'depth' => ($product['depth'] ? $product['depth'] : 1),
					'weight' => ($product['weight'] ? $product['weight'] : 1),
					'packaging_type' => ($config['packaging_type_code'] ? $config['packaging_type_code'] : Configuration::get('UPS_CARRIER_PACKAGING_TYPE')),
				);

			// If Additional charges
			if (isset($config['id_currency']) && isset($config['additionnal_charges']))
			{
				$conversionRate = 1;
				$conversionRate = $this->getCookieCurrencyRate((int)($config['id_currency']));
				$cost += ($config['additionnal_charges'] * $conversionRate);
			}
		}


		// If webservice return a cost, we add it, else, we return the original shipping cost
		$result = $this->getUpsShippingCost($wsParams);
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
		$shipper_country = Db::getInstance()->getRow('SELECT `iso_code` FROM `'._DB_PREFIX_.'country` WHERE `id_country` = '.(int)(Configuration::get('UPS_CARRIER_COUNTRY')));
		$shipper_state = Db::getInstance()->getRow('SELECT `iso_code` FROM `'._DB_PREFIX_.'state` WHERE `id_state` = '.(int)(Configuration::get('UPS_CARRIER_STATE')));
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
			'shipper_address1' => Configuration::get('UPS_CARRIER_ADDRESS1'),
			'shipper_address2' => Configuration::get('UPS_CARRIER_ADDRESS2'),
			'shipper_postalcode' => Configuration::get('UPS_CARRIER_POSTAL_CODE'),
			'shipper_city' => Configuration::get('UPS_CARRIER_CITY'),
			'shipper_country_iso' => $shipper_country['iso_code'],
			'shipper_state_iso' => $shipper_state['iso_code'],
			'pickup_type' => Configuration::get('UPS_CARRIER_PICKUP_TYPE'),
			'packaging_type' => Configuration::get('UPS_CARRIER_PACKAGING_TYPE'),
			'products' => $params->getProducts()
		);
		$wsParams['hash'] = $this->getOrderShippingCostHash($wsParams);

		// Check cache
		$cache = $this->getOrderShippingCostCache($wsParams);
		if ($cache['id_ups_cache'] > 0)
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

	public function parseXML($valTab)
	{
		$level = 0;
		$levelTab = array();
		$resultTab = array();
		foreach ($valTab as $tmp)
		{
			if ($tmp['level'] > $level)
				$levelTab[] = $tmp['tag'];
			elseif ($tmp['level'] < $level)
				array_pop($levelTab);
			elseif ($tmp['level'] == $level)
			{
				array_pop($levelTab);
				$levelTab[] = $tmp['tag'];
			}
			$level = $tmp['level'];

			if ($tmp['type'] == 'complete' && isset($tmp['value']))
				$this->recurseTab($resultTab, $levelTab, 0, $tmp['value']);
		}
		return $resultTab;
	}

	public function recurseTab(&$resultTab, $levelTab, $index, $value)
	{
		if (isset($levelTab[$index]))
			$this->recurseTab($resultTab[$levelTab[$index]], $levelTab, $index + 1, $value);
		else
			$resultTab = $value;
	}

	public function webserviceTest($service = '')
	{
		// Check API Key
		if (!Configuration::get('UPS_CARRIER_API_KEY'))
			return false;

		// Example Params for testing
		$shipper_country = Db::getInstance()->getRow('SELECT `iso_code` FROM `'._DB_PREFIX_.'country` WHERE `id_country` = '.(int)(Configuration::get('UPS_CARRIER_COUNTRY')));
		$shipper_state = Db::getInstance()->getRow('SELECT `iso_code` FROM `'._DB_PREFIX_.'state` WHERE `id_state` = '.(int)(Configuration::get('UPS_CARRIER_STATE')));
		$wsParams = array(
			'recipient_address1' => Configuration::get('UPS_CARRIER_ADDRESS1'),
			'recipient_address2' => Configuration::get('UPS_CARRIER_ADDRESS2'),
			'recipient_postalcode' => Configuration::get('UPS_CARRIER_POSTAL_CODE'),
			'recipient_city' => Configuration::get('UPS_CARRIER_CITY'),
			'recipient_country_iso' => $shipper_country['iso_code'],
			'recipient_state_iso' => $shipper_state['iso_code'],
			'shipper_address1' => Configuration::get('UPS_CARRIER_ADDRESS1'),
			'shipper_address2' => Configuration::get('UPS_CARRIER_ADDRESS2'),
			'shipper_postalcode' => Configuration::get('UPS_CARRIER_POSTAL_CODE'),
			'shipper_city' => Configuration::get('UPS_CARRIER_CITY'),
			'shipper_country_iso' => $shipper_country['iso_code'],
			'shipper_state_iso' => $shipper_state['iso_code'],
			'package_list' => array(
				array('width' => 10, 'height' => 3, 'depth' => 10, 'weight' => 2.0, 'packaging_type' => Configuration::get('UPS_CARRIER_PACKAGING_TYPE')),
				array('width' => 3, 'height' => 3, 'depth' => 3, 'weight' => 1.0, 'packaging_type' => Configuration::get('UPS_CARRIER_PACKAGING_TYPE')),
			),
		);

		// Unit or Large Test
		if (!empty($service))
			$servicesList = array(array('code' => $service));
		else
			$servicesList = Db::getInstance()->ExecuteS('SELECT `code` FROM `'._DB_PREFIX_.'ups_rate_service_code` WHERE `id_ups_rate_service_group` = '.(int)Configuration::get('UPS_CARRIER_RATE_SERVICE_GROUP'));	

		// Testing Service
		foreach ($servicesList as $service)
		{
			// Sending Request
			$wsParams['service'] = $service['code'];
			$resultTab = Db::getInstance()->getValue('SELECT `result` FROM `'._DB_PREFIX_.'ups_cache_test` WHERE `hash` = \''.pSQL(md5($this->getXml($wsParams))).'\'');
			if ($resultTab)
				$resultTab = unserialize($resultTab);
			else
				$resultTab = $this->sendRequest($wsParams);

			// Return results
			if (isset($resultTab['RATINGSERVICESELECTIONRESPONSE']['RESPONSE']['RESPONSESTATUSDESCRIPTION']) && $resultTab['RATINGSERVICESELECTIONRESPONSE']['RESPONSE']['RESPONSESTATUSDESCRIPTION'] == 'Success')
			{
				Db::getInstance()->autoExecute(_DB_PREFIX_.'ups_cache_test', array('hash' => pSQL(md5($this->getXml($wsParams))), 'result' => pSQL(serialize($resultTab)), 'date_add' => pSQL(date('Y-m-d H:i:s')), 'date_upd' => pSQL(date('Y-m-d H:i:s'))), 'INSERT');
				return true;
			}

			if (isset($resultTab['RATINGSERVICESELECTIONRESPONSE']['RESPONSE']['ERROR']['ERRORDESCRIPTION']))
				$this->_webserviceError = $this->l('Error').' '.$resultTab['RATINGSERVICESELECTIONRESPONSE']['RESPONSE']['ERROR']['ERRORCODE'].' : '.$resultTab['RATINGSERVICESELECTIONRESPONSE']['RESPONSE']['ERROR']['ERRORDESCRIPTION'];
			else
			{
				$this->_webserviceError = $this->l('UPS Webservice seems to be down, please wait a few minutes and try again');
				return false;
			}
		}

		return false;
	}

	public function getUpsShippingCost($wsParams)
	{
		// Check Arguments
		if (!$wsParams)
			return array('connect' => false, 'cost' => 0);

		// Sending Request
		$resultTab = $this->sendRequest($wsParams);

		// Check currency
		$conversionRate = 1;
		if (isset($resultTab['RATINGSERVICESELECTIONRESPONSE']['RATEDSHIPMENT']['TOTALCHARGES']['CURRENCYCODE']))
		{
			$id_currency_return = Db::getInstance()->getValue('SELECT `id_currency` FROM `'._DB_PREFIX_.'currency` WHERE `iso_code` = \''.pSQL($resultTab['RATINGSERVICESELECTIONRESPONSE']['RATEDSHIPMENT']['TOTALCHARGES']['CURRENCYCODE']).'\'');
			$conversionRate = $this->getCookieCurrencyRate($id_currency_return);
		}

		// Return results
		if (isset ($resultTab['RATINGSERVICESELECTIONRESPONSE']['RESPONSE']['RESPONSESTATUSDESCRIPTION']) && $resultTab['RATINGSERVICESELECTIONRESPONSE']['RESPONSE']['RESPONSESTATUSDESCRIPTION'] == 'Success')
			return array('connect' => true, 'cost' => $resultTab['RATINGSERVICESELECTIONRESPONSE']['RATEDSHIPMENT']['TOTALCHARGES']['MONETARYVALUE'] * $conversionRate);

		if (isset($resultTab['RATINGSERVICESELECTIONRESPONSE']['RESPONSE']['ERROR']['ERRORDESCRIPTION']))
			$this->_webserviceError = $resultTab['RATINGSERVICESELECTIONRESPONSE']['RESPONSE']['ERROR']['ERRORDESCRIPTION'];
		else
			$this->_webserviceError = $this->l('UPS Webservice seems to be down, please wait a few minutes and try again');

		return array('connect' => false, 'cost' => 0);
	}

	public function sendRequest($wsParams)
	{
		// POST Request
		$errno = $errstr = $result = '';
		$xml = $this->getXml($wsParams);

		if (is_callable('curl_exec'))
		{
			// Curl Request
			$ch = curl_init("https://www.ups.com/ups.app/xml/Rate");
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch,CURLOPT_POST,1);
			curl_setopt($ch,CURLOPT_TIMEOUT, 60);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
			$result = curl_exec ($ch);
		}
		else
		{
			// FsockOpen Request
			$timeout = 5;
			$fp = fsockopen("ssl://www.ups.com", "443", $errno, $errstr, $timeout); 
			if ($fp)
			{
				$request = "POST /ups.app/xml/Rate HTTP/1.1\r\n";
				$request .= "Host: www.ups.com\r\n";
				$request .= "Content-type: application/x-www-form-urlencoded\r\n";
				$request .= "Connection: Close\r\n";
				$request .= "Content-length: ".strlen($xml)."\r\n\r\n";
				$request .= $xml."\r\n\r\n";
				fwrite($fp, $request);

				stream_set_blocking($fp, TRUE);
				stream_set_timeout($fp,$timeout);
				$info = stream_get_meta_data($fp);

				$result = '';
				while ((!feof($fp)) && (!$info['timed_out']))
				{
					$result .= fgets($fp, 4096);
					$info = stream_get_meta_data($fp);
				}
				if ($info['timed_out'])
				{
					$this->_webserviceError = $this->l('UPS Webservice timed out.');
					return false;
				}
			}
			else
			{
				$this->_webserviceError = $this->l('Could not connect to UPS.com');
				return false;
			}
		}

		// Get xml from HTTP Result
		$data = strstr($result, '<?');
		$xml_parser = xml_parser_create();
		xml_parse_into_struct($xml_parser, $data, $valTab, $indexTab);
		xml_parser_free($xml_parser);

		// Parsing XML
		$resultTab = $this->parseXML($valTab);
		return $resultTab;
	}

	public function getXml($wsParams = array())
	{
		$xmlPackageList = '';
		$xmlPackageTemplate = @file_get_contents(dirname(__FILE__).'/xml-package.tpl');

		foreach ($wsParams['package_list'] as $p)
		{
			$search = array('[[PackagingTypeCode]]', '[[PackageWeight]]', '[[WeightUnit]]', '[[Width]]', '[[Height]]', '[[Length]]', '[[DimensionUnit]]');
			$replace = array($p['packaging_type'], $p['weight'], $this->_weightUnit, $p['width'], $p['height'], $p['depth'], $this->_dimensionUnit);
			$xmlPackageList .= str_replace($search, $replace, $xmlPackageTemplate);
		}
			

		$search = array(
			'[[AccessLicenseNumber]]',
			'[[UserId]]',
			'[[Password]]',
			'[[PickupTypeCode]]',
			'[[ShipperNumber]]',
			'[[ShipperAddressLine1]]',
			'[[ShipperAddressLine2]]',
			'[[ShipperCity]]',
			'[[ShipperPostalCode]]',
			'[[ShipperCountryCode]]',
			'[[ShipperStateCode]]',
			'[[ShipToAddressLine1]]',
			'[[ShipToAddressLine2]]',
			'[[ShipToCity]]',
			'[[ShipToPostalCode]]',
			'[[ShipToCountryCode]]',
			'[[ShipToStateCode]]',
			'[[ShipFromAddressLine1]]',
			'[[ShipFromAddressLine2]]',
			'[[ShipFromCity]]',
			'[[ShipFromPostalCode]]',
			'[[ShipFromCountryCode]]',
			'[[ShipFromStateCode]]',
			'[[Service]]',
			'[[PackageList]]'
		);

		$replace = array(
			Configuration::get('UPS_CARRIER_API_KEY'),
			Configuration::get('UPS_CARRIER_LOGIN'),
			Configuration::get('UPS_CARRIER_PASSWORD'),
			Configuration::get('UPS_CARRIER_PICKUP_TYPE'),
			Configuration::get('UPS_CARRIER_SHIPPER_ID'),
			$wsParams['shipper_address1'],
			$wsParams['shipper_address2'],
			$wsParams['shipper_city'],
			$wsParams['shipper_postalcode'],
			$wsParams['shipper_country_iso'],
			$wsParams['shipper_state_iso'],
			$wsParams['recipient_address1'],
			$wsParams['recipient_address2'],
			$wsParams['recipient_city'],
			$wsParams['recipient_postalcode'],
			$wsParams['recipient_country_iso'],
			$wsParams['recipient_state_iso'],
			$wsParams['shipper_address1'],
			$wsParams['shipper_address2'],
			$wsParams['shipper_city'],
			$wsParams['shipper_postalcode'],
			$wsParams['shipper_country_iso'],
			$wsParams['shipper_state_iso'],
			$wsParams['service'],
			$xmlPackageList
		);

		$xmlTemplate = @file_get_contents(dirname(__FILE__).'/xml.tpl');
		$xml = str_replace($search, $replace, $xmlTemplate);

		return $xml;
	}

}

?>

