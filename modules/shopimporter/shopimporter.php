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

class shopimporter extends ImportModule
{
	public $supportedImports = array();

	public function __construct()
	{
		$this->name = 'shopimporter';
		$this->tab = 'migration_tools';
		$this->version = '1.0';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;
		$this->is_configurable = 1;

		parent::__construct ();

		$this->displayName = $this->l('Shop Importer');
		$this->description = $this->l('This module allows you to import your shop from another system into Prestashop.');
		$this->supportedImports = array(
									'language' => array('methodName' => 'getLangagues',
														'name' => $this->l('Language'),
														'className' => 'Language',
														'label' => $this->l('Import Languages'),
														'table' => 'lang',
														'identifier' => 'id_lang',
														'alterTable' => array('id_lang' => 'int(10)'),
														'info' => $this->l('New languages will automatically add translations.'),
														'defaultId' => 'PS_LANG_DEFAULT'
														),
									'currency' => array('methodName' => 'getCurrencies',
														'name' => $this->l('Currency'),
														'className' => 'Currency',
														'label' => $this->l('Import Currencies'),
														'table' => 'currency',
														'identifier' => 'id_currency',
														'alterTable' => array('id_currency' => 'int(10)'),
														'delete' => true,
														'defaultId' => 'PS_CURRENCY_DEFAULT'
														),
							/*
		'tax' => array('methodName' => 'getTaxes',
														'name' => $this->l('Taxes'),
														'className' => 'Tax',
														'label' => $this->l('Import Taxes'),
														'table' => 'tax',
														'identifier' => 'id_tax',
														'alterTable' => array('id_tax' => 'int(10)'),
														'delete' => true
														),
									'tax_rule' => array('methodName' => 'getTaxes rules',
														'name' => $this->l('Taxes rules'),
														'className' => 'TaxRule',
														'label' => $this->l('Import Taxes Rules'),
														'table' => 'tax_rules_group',
														'identifier' => 'id_tax',
														'alterTable' => array('id_tax_rules_group' => 'int(10)'),
														'delete' => true
														),
*/
									'zone' => array('methodName' => 'getZones',
														'name' => $this->l('Zone'),
														'className' => 'Zone',
														'label' => $this->l('Import Zones'),
														'table' => 'zone',
														'identifier' => 'id_zone',
														'alterTable' => array('id_zone' => 'int(10)'),
														'delete' => true
														),
									'country' => array('methodName' => 'getCountries',
														'name' => $this->l('Country'),
														'className' => 'Country',
														'label' => $this->l('Import Countries'),
														'table' => 'country',
														'identifier' => 'id_country',
														'foreign_key' => array('id_zone', 'id_currency'),
														'alterTable' => array('id_country' => 'int(10)'),
														'delete' => true,
														'defaultId' => 'PS_COUNTRY_DEFAULT'
														),
									'state' => array('methodName' => 'getStates',
														'name' => $this->l('State'),
														'className' => 'State',
														'label' => $this->l('Import States'),
														'table' => 'state',
														'identifier' => 'id_state',
														'foreign_key' => array('id_zone', 'id_country'),
														'alterTable' => array('id_state' => 'int(10)'),
														'delete' => true
														),
									'group' => array('methodName' => 'getGroups',
														'name' => $this->l('Group'),
														'className' => 'Group',
														'label' => $this->l('Import Groups'),
														'table' => 'group',
														'identifier' => 'id_group',
														'alterTable' => array('id_group' => 'int(10)'),
														'delete' => true,
														),
									'customer' => array('methodName' => 'getCustomers',
														'name' => $this->l('Customer'),
														'className' => 'Customer',
														'label' => $this->l('Import Customers'),
														'table' => 'customer',
														'identifier' => 'id_customer',
														'foreign_key' => array('id_group'),
														'alterTable' => array('id_customer' => 'int(10)', 'passwd' => 'varchar(100)'),
														'delete' => true,
														'association' => array(
															array(
																'table' => 'customer_group',
																'fields' => array('id_customer', 'id_group'),
																'matchTable' => array('customer', 'group'),
																)
															)
														),
									'address' => array('methodName' => 'getAddresses',
														'name' => $this->l('Address'),
														'className' => 'Address',
														'label' => $this->l('Import Addresses'),
														'table' => 'address',
														'identifier' => 'id_address',
														'foreign_key' => array('id_country', 'id_state', 'id_customer'),
														'alterTable' => array('id_address' => 'int(10)'),
														'delete' => true
														),
									'manufacturer' => array('methodName' => 'getManufacturers',
														'name' => $this->l('Manufacturer'),
														'className' => 'Manufacturer',
														'label' => $this->l('Import Manufacturers'),
														'table' => 'manufacturer',
														'identifier' => 'id_manufacturer',
														'delete' => true,
														'alterTable' => array('id_manufacturer' => 'int(10)'),
														'hasImage' => true
														),
									'supplier' => array('methodName' => 'getSuppliers',
														'name' => $this->l('Supplier'),
														'className' => 'Supplier',
														'label' => $this->l('Import Suppliers'),
														'table' => 'supplier',
														'identifier' => 'id_supplier',
														'delete' => true,
														'alterTable' => array('id_supplier' => 'int(10)'),
														'hasImage' => true
														),
									'category' => array('methodName' => 'getCategories',
														'name' => $this->l('Category'),
														'className' => 'Category',
														'label' => $this->l('Import Categories'),
														'table' => 'category',
														'identifier' => 'id_category',
														'alterTable' => array('id_category' => 'int(10)'),
														'delete' => true,
														'hasImage' => true,
														),
									'attributegroup' => array('methodName' => 'getAttributesGroups',
														'name' => $this->l('AttributeGroup'),
														'className' => 'AttributeGroup',
														'label' => $this->l('Import Attributes Groups'),
														'table' => 'attribute_group',
														'identifier' => 'id_attribute_group',
														'alterTable' => array('id_attribute_group' => 'int(10)'),
														'delete' => true
														),
									'attribute' => array('methodName' => 'getAttributes',
														'name' => $this->l('Attribute'),
														'className' => 'Attribute',
														'label' => $this->l('Import Attributes'),
														'table' => 'attribute',
														'identifier' => 'id_attribute',
														'alterTable' => array('id_attribute' => 'int(10)'),
														'foreign_key' => array('id_attribute_group'),
														'delete' => true
														),
									'product' => array('methodName' => 'getProducts',
														'name' => $this->l('Product'),
														'className' => 'Product',
														'label' => $this->l('Import Products'),
														'table' => 'product',
														'identifier' => 'id_product',
														'alterTable' => array('id_product' => 'int(10)'),
														'foreign_key' => array('id_category', 'id_manufacturer'),
														'delete' => true,
														'association' => array(
															array(
																'table' => 'category_product',
																'fields' => array('id_category', 'id_product'),
																'matchTable' =>  array('category', 'product')
																)
															),
														'hasImage' => true
														),
									'combination' => array('methodName' => 'getProductsCombination',
														'name' => $this->l('Combination'),
														'className' => 'Combination',
														'label' => $this->l('Import Products Combinations'),
														'table' => 'product_attribute',
														'identifier' => 'id_product_attribute',
														'alterTable' => array('id_product_attribute' => 'int(10)', 'id_product' => 'int(10)'),
														'foreign_key' => array('id_product'),
														'delete' => false,
														'association' => array(
															array(
																'table' => 'product_attribute_combination',
																'fields' => array('id_attribute', 'id_product_attribute'),
																'matchTable' =>  array('attribute', 'product_attribute')
																)
															)
														),
									'orderstate' => array('methodName' => 'getOrdersStates',
													 'name' => $this->l('Orders States'),
													 'className' => 'OrderState',
													 'label' => $this->l('Import Orders States'),
													 'table' => 'order_state',
													 'identifier' => 'id_order_state',
													 'alterTable' => array('id_order_state' => 'int(10)')
													 ),
									'cart' => array('methodName' => 'getOrders',
													 'name' => $this->l('Order'),
													 'className' => 'Cart',
													 'label' => $this->l('Import Orders'),
													 'table' => 'cart',
													 'identifier' => 'id_cart',
													 'foreign_key' => array('id_address_delivery', 'id_address_invoice', 'id_customer'),
													 'alterTable' => array('id_cart' => 'int(10)')
													 ),
									'orderhistory' => array('methodName' => 'getOrdersHistory',
													 'name' => $this->l('Order history'),
													 'className' => 'Order history',
													 'label' => $this->l('Import Order History'),
													 'table' => 'order_history',
													 'identifier' => 'id_order_history',
													 'foreign_key' => array('id_order', 'id_order_state'),
													 'alterTable' => array('id_order_history' => 'int(10)'),
													 'hidden' => true
													 ),
									'order' => array('methodName' => 'getOrders',
													 'name' => $this->l('Order'),
													 'className' => 'Order',
													 'label' => $this->l('Import Order History'),
													 'table' => 'orders',
													 'identifier' => 'id_order',
													 'alterTable' => array('id_order' => 'int(10)'),
													 'hidden' => true
													 )
									);
	}

	public function install()
	{
		return parent::install();
	}

	public function uninstall()
	{
		return parent::uninstall();
	}

	public function getContent()
	{
		$exportModules = parent::getImportModulesOnDisk();
		//get installed module only
		foreach($exportModules as $key => $module)
			if ($module->name == $this->name || !(bool)$module->id)
				unset($exportModules[$key]);
		$html = '<script type="text/javascript">var globalAjaxShopImporterToken = "'.sha1(_COOKIE_KEY_.'ajaxShopImporter').'";</script>
		<script type="text/javascript" src="../modules/shopimporter/shopimporter.js"></script>
				<script src="'._PS_JS_DIR_.'jquery/jquery.scrollTo-1.4.2-min.js"></script>
				 <script type="text/javascript">
					var conf = new Array(); ';
		$i = 0;
		foreach($this->supportedImports as $import)
		{
			if(!array_key_exists('hidden', $import))
			$html .= 'conf['.$i.'] = new Array(\''.addslashes($import['methodName']).'\', \''.addslashes($import['label']).'\', \''.addslashes($import['className']).'\', \''.addslashes($import['name']).'\');';
			$i++;
		}

		$html .= '	var notExist = "'.$this->l('is not available in this module').'";
					var databaseOk = "'.$this->l('Connection to the database OK').'";
					var wsOk = "'.$this->l('Connection to the web service OK').'";
					var showErrors = "'.$this->l('Show errors').'";
					var testImport = "'.$this->l('Test import process').'";
					var runImport = "'.$this->l('Run Import').'";
					var importHasErrors = "'.$this->l('Errors occurred during import. For more details click on "Show errors".').'"
					var importFinish = "'.$this->l('Import is complete.').'"
					var truncateTable = "'.$this->l('Remove data').'"
					var oneThing = "'.$this->l('Please choose one thing to import').'"
				</script>
				<style>
					.margin-form{padding: 0px 0px 1em 120px;width:300px;}
					label{width: 170px;}
					.import{background-color: #CCCCCC;border: 1px solid gray;margin: 0px 0px 10px;padding: 10px 15px;line-height: 20px;}
				</style>
				<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" />'.$this->l('Import from another system').'</legend>
				<div class="warn" ><img src="../img/admin/warn2.png">
					'.$this->l('Before starting the import please backup your database. ').'
						<a href="index.php?tab=AdminBackup&token='.Tools::getAdminToken('AdminBackup'.(int)Tab::getIdFromClassName('AdminBackup').(int)$this->context->employee->id).'"">'.$this->l(' Click here to backup').'</a>
				</div>
				<br>
				<div style="float:right;width:450px" id="steps"></div>';
				if (sizeof($exportModules))
				{
				$html .= '
				<label>'.$this->l('Choose your import').' : </label>
				<div class="margin-form">
					<select name="import_module_name" id="import_module_name">
						<option value="0">---</option>';

		foreach($exportModules as $key => $module)
			(($module->name != $this->name AND $module->id) ? $html .= '<option value="'.$module->name.'">'.$module->displayName.'</option>' : '' );

		$html .= '</select><input type="submit" class="button" id="choose_module_name" value="'.$this->l('Choose').'">
				</div>';
				}
				else
					$html .= '<div class="warn" ><img src="../img/admin/warn2.png">'.$this->l('No import module installed').'</div>';
				$html .= '
					<div style="display:none;" class="error" id="connectionInformation"></div>
					<div id="config_connector"></div>
						<div class="margin-form">
						<input type="submit" name="displayOptions" id="displayOptions" class="button" value="'.$this->l('Next Step').'">
					</div>
					<hr>
					<div style="display:none" id="importOptions">
					<h2>'.$this->l('Import Options').'</h2>
					<div id="importOptionsYesNo">';
					foreach($this->supportedImports as $key => $import)
					{
						if(!array_key_exists('hidden', $import))
						$html .= '<label>'.$import['name'].' : </label>
									<div class="margin-form">
										<label class="t" for="'.$import['identifier'].'_on'.'"><img src="../img/admin/enabled.gif" alt="Yes" title="Yes"></label>
										<input type="radio" id="'.$import['identifier'].'_on'.'" name="'.$import['methodName'].'" class="'.$key.'" value="1" checked="checked">
										<label class="t" for="'.$import['identifier'].'_off'.'"><img src="../img/admin/disabled.gif" alt="No" title="No" style="margin-left: 10px;"></label>
										<input type="radio" id="'.$import['identifier'].'_off'.'" name="'.$import['methodName'].'" class="'.$key.'" value="0">
										'.(array_key_exists('delete', $import) ? '
										<label class="t"><img src="../img/admin/delete.gif" alt="Delete" title="Delete"></label>
										<input type="checkbox" class="truncateTable" id="'.$key.'" name="delete_'.$import['className'].'">' : '' ).
										(array_key_exists('hasImage', $import) ? '
										<label class="t"><img src="../img/admin/picture.gif" alt="Images" title="Images"></label>
										<input type="checkbox" class="importImages" id="'.$key.'" name="images_'.$import['className'].'">' : '' ).
										(array_key_exists('info', $import) ? '<p>'.$import['info'].'</p>' : '').'
									</div>';
					}
					$html .= '</div><hr>
					<h2>'.$this->l('Advanced Options').'</h2>
					<div class="warn" id="warnSkip" style="display:none"><img src="../img/admin/warn2.png">
					'.$this->l('This mode is dangerous').'
					</div>
					<label>'.$this->l('Import for every').' : </label>
							<div class="margin-form">
								<select name="nbr_import" id="nbr_import">
									<option value="10">10</option>
									<option value="50">50</option>
									<option value="100" selected="selected">100</option>
									<option value="200">200</option>
								</select>
								<p>'.$this->l('Select the number of occurrences for each query to import').'</p>
							</div>
					<label>'.$this->l('If errors occur').' : </label>
							<div class="margin-form">
								<label class="t"><img src="'.$this->_path.'img/stop.png"></label>
								<input type="radio" name="hasErrors" id="hasErrors" value="0" checked="checked">
								<label class="t">'.$this->l('Stop').'</label>
								<label class="t"><img src="'.$this->_path.'img/skip.png" style="margin-left: 10px;"></label>
								<input type="radio" name="hasErrors" id="hasErrors" value="1">
								<label class="t">'.$this->l('Skip').'</label>
								<label class="t"><img src="'.$this->_path.'img/force.gif" style="margin-left: 10px;"></label>
								<input type="radio" name="hasErrors" id="hasErrors" value="2">
								<label class="t">'.$this->l('Force').'</label>
								<p>'.$this->l('Stop: if there are errors with the data, import will not run.').'</p>
								<p>'.$this->l('Skip: if there are errors with the data, import will skip incorrect data.').'</p>
								<p>'.$this->l('Force: if there are errors with the data, import will replace incorrect data by generic data.').'</p>
							</div>
							<hr>
							<div style="display:none" id="specificOptions">
							<h2>'.$this->l('Specific Options').'</h2>
								<div style="display:none" class="error" id="specificOptionsErrors"></div>
								<div id="specificOptionsContent">
								</div>
							</div>
							<hr>
							<div class="margin-form">
							<input type="submit" class="button" name="checkAndSaveConfig" id="checkAndSaveConfig" value="'.$this->l('Next Step').'">
						</div>
					</div>
				</fieldset>';
		return $html;
	}

	public function genericImport($className, $fields, $save = false)
	{
		$return = '';
		$json = array();
		$errors = array();
		$json['hasError'] = false;
		$json['datas'] = array_values($fields);
		$languages = array();
		$defaultLanguage = '';
		$table = $this->supportedImports[strtolower($className)]['table'];

		$object = new $className();
		$rules = call_user_func(array($className, 'getValidationRules'), $className);

		if ((sizeof($rules['requiredLang']) || sizeof($rules['sizeLang']) || sizeof($rules['validateLang']) || Tools::isSubmit('syncLang') ||  Tools::isSubmit('syncCurrency')))
		{
			$moduleName = Tools::getValue('moduleName');
			if (Validate::isModuleName($moduleName) && Validate::file_exists('../../modules/'.$moduleName.'/'.$moduleName.'.php'))
			{
				require_once('../../modules/'.$moduleName.'/'.$moduleName.'.php');
				$importModule = new $moduleName();
				$importModule->server = Tools::getValue('server');
				$importModule->user = Tools::getValue('user');
				$importModule->passwd = Tools::getValue('password');
				$importModule->database = Tools::getValue('database');
				$importModule->prefix = Tools::getValue('prefix');
				$defaultLanguage = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
				$languages = $importModule->getLangagues(0);
				if (Tools::isSubmit('syncLang') || Tools::isSubmit('syncLangWS'))
				{
					$defaultIdLand = $importModule->getDefaultIdLang();
					$defaultLanguageImport = new Language(Language::getIdByIso($languages[$defaultIdLand]['iso_code']));
					if ($defaultLanguage->iso_code != $defaultLanguageImport->iso_code)
						$errors[] = $this->l('Default language doesn\'t match : ').'<br>'.Configuration::get('PS_SHOP_NAME').' : '.$defaultLanguage->name.' ≠
											'.$importModule->displayName.' : '.$defaultLanguageImport->name.'<br>'.$this->l('Please change default language in your configuration');
				}

				if (Tools::isSubmit('syncCurrency') || Tools::isSubmit('syncCurrencyWS'))
				{
					$defaultIdCurrency = $importModule->getDefaultIdCurrency();
					$currencies = $importModule->getCurrencies(0);
					if(!empty($currencies[$defaultIdCurrency]['iso_code']))
						$defaultCurrencyImport = new Currency((int)Currency::getIdByIsoCode($currencies[$defaultIdCurrency]['iso_code']));
					else
						$defaultCurrencyImport = new Currency((int)Currency::getIdByIsoCodeNum($currencies[$defaultIdCurrency]['iso_code_num']));

					$defaultCurrency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));
					if ($defaultCurrency->iso_code != $defaultCurrencyImport->iso_code)
						$errors[] = $this->l('Default currency doesn\'t match : ').'<br>'.Configuration::get('PS_SHOP_NAME').' : '.$defaultCurrency->name.' ≠ '.$importModule->displayName.' : '.$defaultCurrencyImport->name.'<br>'.$this->l('Please change default currency in your configuration');
				}
				if (!empty($errors))
					die('{"hasError" : true, "error" : '.Tools::jsonEncode($errors).'}');
			}
			else
				die('{"hasError" : true, "error" : ["FATAL ERROR"], "datas" : []}');
		}

		foreach($fields as $key => $field)
		{
			$id = $this->supportedImports[strtolower($className)]['identifier'];
			//remove wrong fields (ex : id_toto in Customer)
			foreach($field as $name => $value)
				if (!array_key_exists($name, get_object_vars($object)) AND ($name != $id) AND ($name != 'association') AND ($name != 'images') AND (strtolower($className) != 'cart'))
					unset($field[$name]);
			$return = $this->validateRules($rules, $field, $className, $languages, $defaultLanguage);
			$fields[$key] = $field;
			if (!empty($return))
			{
				//skip mode
				if (Tools::getValue('hasErrors') == 1)
					unset($fields[$key]);

				$errors[] = $return;
				array_unshift($errors[sizeof($errors)-1], $field[$id]);
			}
		}
		if (sizeof($errors) > 0)
		{
			$json['hasError'] = true;
			$json['error'] = $errors;
		}

		if ($save || Tools::isSubmit('syncLang') || Tools::isSubmit('syncLangWS'))
		{
			//add language if not exist in prestashop
			if ($className == 'Language')
			{
				if (Tools::isSubmit('syncLang') || Tools::isSubmit('syncLangWS'))
					$add = true;
				else
					$add = false;
				$errors = $this->checkAndAddLang($fields, $add);
			}
			elseif ($className == 'Cart')
			{
				$this->saveOrders($fields);
			}
			else
			{
				$return = $this->saveObject($className, $fields);
				$this->cleanPositions($table);
				//insert association
				if (array_key_exists('association', $this->supportedImports[strtolower($className)]))
					$this->insertAssociation(strtolower($className), $fields);
				if (!empty($return))
				{
					$json['hasError'] = true;
					$json['error'] = $return;
				}
			}
			if ($className == 'Category' AND (sizeof($fields) != (int)Tools::getValue('nbr_import')))
				$this->updateCat();
		}
		if (sizeof($errors) > 0 AND is_array($errors))
		{
			$json['hasError'] = true;
			$json['error'] = $errors;
		}
		die(Tools::jsonEncode($json));
	}
	public function genericImportWS($className, $fields, $save = false)
	{

		$return = '';
		$json = array();
		$errors = array();
		$json['hasError'] = false;
		$json['datas'] = array_values($fields);
		$languages = array();
		$defaultLanguage = '';
		$table = $this->supportedImports[strtolower($className)]['table'];

		$object = new $className();

		$rules = call_user_func(array($className, 'getValidationRules'), $className);


		if ((sizeof($rules['requiredLang']) || sizeof($rules['sizeLang']) || sizeof($rules['validateLang']) || Tools::isSubmit('syncLangWS') ||  Tools::isSubmit('syncCurrency')))
		{
			$moduleName = Tools::getValue('moduleName');
			if (Validate::isModuleName($moduleName) && file_exists('../../modules/'.$moduleName.'/'.$moduleName.'.php'))
			{

				require_once('../../modules/'.$moduleName.'/'.$moduleName.'.php');
				$importModule = new $moduleName();

				$defaultLanguage = new Language((int)Configuration::get('PS_LANG_DEFAULT'));

				$languages = $importModule->getLangagues();

				if (Tools::isSubmit('syncLangWS'))
				{
					$defaultIdLand = $importModule->getDefaultIdLang();
					$defaultLanguageImport = new Language(Language::getIdByIso($languages[$defaultIdLand]['iso_code']));
					if ($defaultLanguage->iso_code != $defaultLanguageImport->iso_code)
						$errors[] = $this->l('Default language doesn\'t match : ').'<br>'.Configuration::get('PS_SHOP_NAME').' : '.$defaultLanguage->name.' ≠
											'.$importModule->displayName.' : '.$defaultLanguageImport->name.'<br>'.$this->l('Please change default language in your configuration');
				}

				if (Tools::isSubmit('syncCurrency'))
				{
					$defaultIdCurrency = $importModule->getDefaultIdCurrency();
					$currencies = $importModule->getCurrencies();
					if (!empty($currencies[$defaultIdCurrency]['iso_code']))
						$defaultCurrencyImport = new Currency((int)Currency::getIdByIsoCode($currencies[$defaultIdCurrency]['iso_code']));
					else
						$defaultCurrencyImport = new Currency((int)Currency::getIdByIsoCodeNum($currencies[$defaultIdCurrency]['iso_code_num']));

					$defaultCurrency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));
					if ($defaultCurrency->iso_code != $defaultCurrencyImport->iso_code)
						$errors[] = $this->l('Default currency doesn\'t match : ').'<br>'.Configuration::get('PS_SHOP_NAME').' : '.$defaultCurrency->name.' ≠ '.$importModule->displayName.' : '.$defaultCurrencyImport->name.'<br>'.$this->l('Please change default currency in your configuration');
				}
				if (!empty($errors))
					die('{"hasError" : true, "error" : '.Tools::jsonEncode($errors).'}');
			}
			else
				die('{"hasError" : true, "error" : ["FATAL ERROR"], "datas" : []}');
		}

		foreach($fields as $key => $field)
		{
			$id = $this->supportedImports[strtolower($className)]['identifier'];
			//remove wrong fields (ex : id_toto in Customer)
			foreach($field as $name => $value)
				if (!array_key_exists($name, get_object_vars($object)) AND ($name != $id) AND ($name != 'association') AND ($name != 'images') AND (strtolower($className) != 'cart'))
					unset($field[$name]);
			$return = $this->validateRules($rules, $field, $className, $languages, $defaultLanguage);
			$fields[$key] = $field;
			if (!empty($return))
			{
				//skip mode
				if (Tools::getValue('hasErrors') == 1)
					unset($fields[$key]);

				$errors[] = $return;
				array_unshift($errors[sizeof($errors)-1], $field[$id]);
			}
		}
		if (sizeof($errors) > 0)
		{
			$json['hasError'] = true;
			$json['error'] = $errors;
		}
		if ($save || Tools::isSubmit('syncLang') || Tools::isSubmit('syncLangWS'))
		{
			//add language if not exist in prestashop
			if ($className == 'Language')
			{
				if (Tools::isSubmit('syncLang') || Tools::isSubmit('syncLangWS'))
					$add = true;
				else
					$add = false;
				$errors = $this->checkAndAddLang($fields, $add);
			}
			elseif ($className == 'Cart')
			{
				$this->saveOrders($fields);
			}
			else
			{
				$return = $this->saveObject($className, $fields);
				$this->cleanPositions($table);
				//insert association
				if (array_key_exists('association', $this->supportedImports[strtolower($className)]))
					$this->insertAssociation(strtolower($className), $fields);
				if (!empty($return))
				{
					$json['hasError'] = true;
					$json['error'] = $return;
				}
			}
			if ($className == 'Category' AND (sizeof($fields) != (int)Tools::getValue('nbr_import')))
				$this->updateCat();
		}
		if (sizeof($errors) > 0 AND is_array($errors))
		{
			$json['hasError'] = true;
			$json['error'] = $errors;
		}
		die(Tools::jsonEncode($json));
	}

	private function saveObject($className, $items)
	{
		$return = array();
		$table = $this->supportedImports[strtolower($className)]['table'];
		//creating temporary fields for identifiers matching and password
		if (array_key_exists('alterTable', $this->supportedImports[strtolower($className)]))
			$this->alterTable(strtolower($className));
		$matchIdLang = $this->getMatchIdLang(1);
		foreach($items as $item)
		{
			$object = new $className;
			$id = $item[$this->supportedImports[strtolower($className)]['identifier']];
			if (array_key_exists('foreign_key', $this->supportedImports[strtolower($className)]))
				$this->replaceForeignKey($item, $table);

			foreach($item as $key => $val)
			{
				if ($key == 'passwd')
				{
					$password = $val;
					$val = '------';
				}
				if (is_array($val) AND $key != 'images')
				{
					$tmp = array();
					foreach($matchIdLang as $k => $v)
						{
						if (array_key_exists($k, $val))
						{
							$tmp[$v] = $val[$k];
						}
				}

					$object->$key = $tmp;
				}
				else
					$object->$key = $val;
			}
			if (!$object->save(false, false))
				$return[] = array($item[$this->supportedImports[strtolower($className)]['identifier']], $this->l('An error occurred when adding the object'));
			else
			{
				$this->saveMatchId(strtolower($className), (int)$object->id, (int)$id);
				if ($className == 'Customer')
					Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'customer SET `passwd_'.bqSQL(Tools::getValue('moduleName')).'` = \''.pSQL($password).'\' WHERE id_customer = '.(int)$object->id);
				if (array_key_exists('hasImage', $this->supportedImports[strtolower($className)]) AND Tools::isSubmit('images_'.$className))
					$this->copyImg($item, $className);
			}
		}
		return $return;
	}

	private function saveOrders($items)
	{
		$this->saveObject('cart', $items);
		//import cart product
		$foreignKey = $this->getForeignKey('Cart', array('id_cart', 'id_product', 'id_product_attribute', 'id_customer','id_address_invoice', 'id_address_delivery'));
		foreach($items as &$item)
		{
			foreach($item['cart_products'] as $k => $cart_products)
			{
				foreach($cart_products as $key => $val)
					if (array_key_exists($key, $foreignKey))
						if (array_key_exists($val, $foreignKey[$key]))
							$item['cart_products'][$k][$key] = $foreignKey[$key][$val];
						else
							$item['cart_products'][$k][$key] = 0;
				Db::getInstance()->insert('cart_product', $item['cart_products'][$k]);
			}
			foreach($item['order_products'] as $k => $order_products)
			{
				foreach($order_products as $key => $val)
					if (array_key_exists($key, $foreignKey) || $key == 'product_id')
						//patch to correct a mistake naming column in the database
						if ($key == 'product_id')
							$item['order_products'][$k]['product_id'] =  $foreignKey['id_product'][$val];
						elseif (array_key_exists($val, $foreignKey[$key]))
							$item['order_products'][$k][$key] = $foreignKey[$key][$val];
						else
							$item['order_products'][$k][$key] = 0;
			}
		}
		//cart to order
		$this->cartToOrder($items, $foreignKey);
		$foreignKey = array_merge($this->getForeignKey('Cart', array('id_order', 'id_order_state')), $foreignKey);
		foreach($items as &$item)
		{
			//insert product in order detail
			foreach($item['order_products'] as $k => $order_products)
			{
				foreach($order_products as $key => $val)
					if (array_key_exists($key, $foreignKey))
						if (array_key_exists($val, $foreignKey[$key]))
							$item['order_products'][$k][$key] = $foreignKey[$key][$val];
						else
							$item['order_products'][$k][$key] = 0;
				Db::getInstance()->insert('order_detail', $item['order_products'][$k]);
			}
			//save order history
			foreach($item['order_history'] as $k => $order_history)
			{
				foreach($order_history as $key => $val)
					if (array_key_exists($key, $foreignKey))
						if (array_key_exists($val, $foreignKey[$key]))
							$item['order_history'][$k][$key] = $foreignKey[$key][$val];
						else
							$item['order_history'][$k][$key] = 0;

				Db::getInstance()->insert('order_history', $item['order_history'][$k]);
			}
		}
	}

	private function insertAssociation($table, $items)
	{
		foreach($this->supportedImports[$table]['association'] AS $association)
		{
			$associatFields = '';
			$associatFieldsName = implode('`, `', $association['fields']);
			$tableAssociation = $association['table'];
			$matchTable = $association['matchTable'];
			if (!empty($items))
			{
				$match = array();
				foreach($matchTable as $mTable)
				{
					$tmp = $this->getForeignKey($mTable, array('id_'.$mTable));
					if (array_key_exists('id_'.$mTable, $tmp))
						$match['id_'.$mTable] = $tmp['id_'.$mTable];
					else
						$match['id_'.$mTable] = $this->getDefaultId($table);
				}
				foreach($items AS $item)
					foreach($item AS $key => $val)
						if ($key == 'association' AND !empty($key))
							foreach($val[$tableAssociation] AS $k => $v)
							{
								$associatFields .= ' ('.((array_key_exists($k, $match[$association['fields'][0]])) ? (int)$match[$association['fields'][0]][$k] : '1').', ';
								$associatFields .= (int)$match[$association['fields'][1]][$v].'), ';
							}
				if ($associatFields != '')
					Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.bqSQL($tableAssociation).'` (`'.$associatFieldsName.'`) VALUES '.rtrim($associatFields, ', '));
			}
		}
	}

	private function saveMatchId($className, $psId, $matchId)
	{
		$table = $this->supportedImports[$className]['table'];
		$moduleName = Tools::getValue('moduleName');
		$identifier = $this->supportedImports[$className]['identifier'];
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.bqSQL($table).'` SET `'.bqSQL($identifier).'_'.bqSQL($moduleName).'` =  '.(int)$matchId.' WHERE `'.bqSQL($identifier).'` = '.(int)$psId);
	}

	private function getMatchId($className)
	{
		$table = $this->supportedImports[$className]['table'];
		$moduleName = Tools::getValue('moduleName');
		$identifier = $this->supportedImports[$className]['identifier'];
		$returns = Db::getInstance()->executeS('SELECT `'.bqSQL($identifier).'_'.bqSQL($moduleName).'`, `'.bqSQL($identifier).'` FROM `'._DB_PREFIX_.bqSQL($table).'` WHERE `'.bqSQL($identifier).'_'.bqSQL($moduleName).'` != 0 ');
		$match = array();
		foreach($returns as $return)
			$match[$return[$identifier.'_'.$moduleName]] = $return[$identifier];
		return $match;
	}

	private function getDefaultId($table)
	{
		$defaultId = 1;
		if (array_key_exists('defaultId', $this->supportedImports[strtolower($table)]))
				$defaultId = Configuration::get($this->supportedImports[strtolower($table)]['defaultId']);
		return $defaultId;
	}

	private function copyImg($item, $className)
	{
		require_once('../../images.inc.php');
		$identifier = $this->supportedImports[strtolower($className)]['identifier'];
		$matchId = $this->getMatchId(strtolower($className));
		$matchIdLang = $this->getMatchIdLang();
		switch($className)
		{
			default:
			case 'Product':
				$path = _PS_PROD_IMG_DIR_;
				$type = 'products';
			break;
			case 'Category':
				$path = _PS_CAT_IMG_DIR_;
				$type = 'categories';
			break;
			case 'Manufacturer':
				$path = _PS_MANU_IMG_DIR_;
				$type = 'manufacturers';
			break;
			case 'Supplier':
				$path = _PS_SUPP_IMG_DIR_;
				$type = 'suppliers';
			break;
		}
		$cover = 1;
		if (array_key_exists($item[$identifier], $matchId))
			if(array_key_exists('images', $item) && !is_null($item['images']))
			foreach($item['images'] as $key => $image)
			{
				$tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'import');
					if (@copy(str_replace(' ', '%20', $image), $tmpfile))
				{

					$imagesTypes = ImageType::getImagesTypes($type);
					ImageManager::resize($tmpfile, $path.(int)$matchId[$item[$identifier]].'.jpg');
					if ($className == 'Product')
					{
						$image = new Image();
						$image->id_product = (int)($matchId[$item[$identifier]]);
						$image->cover = $cover;
						$image->position = Image::getHighestPosition((int)$matchId[$item[$identifier]]) + 1;
						$legend = array();
						foreach($item['name'] as $key => $val)
							if (array_key_exists($key, $matchIdLang))
								$legend[$matchIdLang[$key]] = Tools::link_rewrite($val);
							else
								$legend[Configuration::get('PS_LANG_DEFAULT')] = Tools::link_rewrite($val);
						$image->legend = $legend;
						$image->add();
						ImageManager::resize($tmpfile, $path.(int)$matchId[$item[$identifier]].'-'.(int)$image->id.'.jpg');
						foreach ($imagesTypes AS $k => $imageType)
							ImageManager::resize($tmpfile, $path.(int)$matchId[$item[$identifier]].'-'.(int)$image->id.'-'.stripslashes($imageType['name']).'.jpg', $imageType['width'], $imageType['height']);
					}
					else
						foreach ($imagesTypes as $imageType)
							ImageManager::resize($tmpfile, $path.(int)$matchId[$item[$identifier]].'-'.stripslashes($imageType['name']).'.jpg', $imageType['width'], $imageType['height']);
				}
				else
					@unlink($tmpfile);
				@unlink($tmpfile);
				$cover = 0;
			}

	}

	private function replaceForeignKey(&$item, $table)
	{
		if ($table == 'product_attribute')
			$table2 = 'combination';
		else
			$table2 = $table;

		$foreingKey = $this->supportedImports[$table2]['foreign_key'];
		$foreingKeyValue = $this->getForeignKey($table, $foreingKey);
		foreach($foreingKey as $key)
		{
			if ($table == 'product' AND $key == 'id_category')
				$key2 = 'id_category_default';
			else if ($table == 'customer' AND $key == 'id_group')
				$key2 = 'id_default_group';
			else
				$key2 = $key;
			if (array_key_exists($key, $foreingKeyValue) && $item[$key2] != 0)
				$item[$key2] = (array_key_exists($item[$key2], $foreingKeyValue[$key]) ? $item[$key2] = $foreingKeyValue[$key][$item[$key2]] : $item[$key2] = 0);
			else
				$item[$key2] = $this->getDefaultId($table);
		}
	}

	private function alterTable($className)
	{
		$query ='';
		$queryTmp = '';
		$from = $this->supportedImports[$className]['table'];
		$result = array();
		$result = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.bqSQL($from).'`');
		if (!$result)
			$result = array();
		foreach ($this->supportedImports[$className]['alterTable'] AS $name => $type)
		{
			$moduleName = Tools::getValue('moduleName');
			Db::getInstance()->executeS("SHOW COLUMNS FROM `"._DB_PREFIX_.bqSQL($from)."` LIKE '".pSQL($name).'_'.pSQL($moduleName)."'");
			if (!Db::getInstance()->numRows() AND !array_key_exists($name.'_'.$moduleName, $result))
					$queryTmp .= ' ADD `'.$name.'_'.$moduleName.'` '.$type.' NOT NULL,';
		}
		if (!empty($queryTmp))
		{
			$query = 'ALTER TABLE  `'._DB_PREFIX_.bqSQL($from).'` ';
			$query .= rtrim($queryTmp, ',');
			Db::getInstance()->execute($query);
		}
	}

	private function updateCat()
	{
		$moduleName = Tools::getValue('moduleName');
		Db::getInstance()->execute('UPDATE
									'._DB_PREFIX_.'category c
									INNER JOIN
									'._DB_PREFIX_.'category c2
									ON
									c.id_parent = c2.`id_category_'.bqSQL($moduleName).'`
									SET
									c.id_parent = c2.id_category
									WHERE c.`id_category_'.bqSQL($moduleName).'` != 0');
		$category = new Category();
		$cats = $category->getSimpleCategories((int)Configuration::get('PS_LANG_DEFAULT'));
		foreach($cats as $cat)
		{
			$cat = new Category((int)$cat['id_category']);
			$cat->level_depth = $cat->calcLevelDepth();
			$cat->update();
		}
	}

	private function getForeignKey($className, $foreign_key = null)
	{
		$moduleName = Tools::getValue('moduleName');
		if (is_null($foreign_key))
			$foreign_key = $this->supportedImports[$className]['foreign_key'];
		$match = array();
		foreach($foreign_key AS $key)
		{
			if (in_array($key , array('id_address_invoice', 'id_address_delivery')))
				$key2 = 'id_address';
			elseif (in_array($key , array('id_category_default')))
				$key2 = 'id_category';
			else
				$key2 = $key;
			foreach($this->supportedImports AS $table => $conf)
				if ($conf['identifier'] == $key2)
					$from = $this->supportedImports[$table]['table'];
			$return = Db::getInstance()->executeS('SELECT `'.bqSQL($key2).'_'.bqSQL($moduleName).'`, `'.bqSQL($key2).'` FROM `'._DB_PREFIX_.bqSQL($from).'` WHERE `'.bqSQL($key2).'_'.bqSQL($moduleName).'` != 0');
			if (!empty($return))
				foreach($return AS $name => $val)
					$match[$key][$val[$key2.'_'.$moduleName]] = $val[$key2];
		}
		return $match;
	}

	private function getForeignKeyLang($table)
	{
		$id = $this->supportedImports[$table]['identifier'];
		$moduleName = Tools::getValue('moduleName');
		$return = Db::getInstance()->executeS('SELECT `'.bqSQL($id).'_'.bqSQL($moduleName).'`, `'.bqSQL($id).'` FROM `'._DB_PREFIX_.bqSQL($table).'` WHERE `'.bqSQL($id).'_'.bqSQL($moduleName).'` != 0');
		$match = array();
		foreach($return AS $name => $val)
				$match[$val[$id.'_'.$moduleName]] = $val[$id];
		return $match;
	}

	private function getMatchIdLang($order = 1)
	{
		$moduleName = Tools::getValue('moduleName');
		$return = Db::getInstance()->executeS('SELECT `id_lang`, `id_lang_'.bqSQL($moduleName).'` FROM `'._DB_PREFIX_.'lang'.'` WHERE `id_lang_'.bqSQL($moduleName).'` != 0');
		$match = array();
		foreach($return AS $name => $val)
			if ((bool)$order)
				$match[$val['id_lang_'.$moduleName]] = $val['id_lang'];
			else
				$match[$val['id_lang']] = $val['id_lang_'.$moduleName];
		return $match;
	}


	private function validateRules($rules, &$fields, $className, $languages, $defaultLanguage)
	{
		$returnErrors = array();
		$hasErrors = Tools::getValue('hasErrors');
		/* Checking for required fields */
		foreach ($rules['required'] AS $field)
			if (($value = $fields[$field]) == false AND (string)$value != '0')
				if ($hasErrors == 2)
				{
					if (array_key_exists($field, $rules['size']))
						$size = $rules['size'][$field];
					else
						$size = 1;
					$fields[$field] = $this->generateData($size, $rules['validate'][$field]);
				}
				else
					$returnErrors[] = sprintf(
						$this->l('The field %s is required.'),
						call_user_func(array($className, 'displayFieldName'), $field, $className)
					);

		/* Checking for maximum fields sizes */
		foreach ($rules['size'] AS $field => $maxLength)
			if (array_key_exists($field, $fields) AND $field != 'passwd')
				if ($fields[$field] !== false AND Tools::strlen($fields[$field]) > $maxLength)
					if ($hasErrors == 2)
						$fields[$field] = substr($fields[$field], 0, $maxLength);
					else
						$returnErrors[] = sprintf(
							$this->l('The field %1$s is too long (%2$d chars max).'),
							call_user_func(array($className, 'displayFieldName'), $field, $className),
							$maxLength
						);

		/* Checking for fields validity */
		foreach ($rules['validate'] AS $field => $function)
			if (array_key_exists($field, $fields))
				if (($value = $fields[$field]) !== false AND ($field != 'passwd'))
					if (!Validate::$function($value))
						if ($hasErrors == 2)
						{
							if (array_key_exists($field, $rules['size']))
								$size = $rules['size'][$field];
							else
								$size = 1;
							$fields[$field] = $this->generateData($size, $rules['validate'][$field]);
						}
						else
							$returnErrors[] = sprintf(
								$this->l('The field %s is invalid.'),
								call_user_func(array($className, 'displayFieldName'), $field, $className)
							);

		if ((sizeof($rules['requiredLang']) || sizeof($rules['sizeLang']) || sizeof($rules['validateLang'])))
		{
		$matchIdLang = $this->getMatchIdLang(0);
		/* Checking for multilingual required fields */
		foreach ($rules['requiredLang'] AS $fieldLang)
		{
			if (($empty = $fields[$fieldLang][$matchIdLang[$defaultLanguage->id]]) === false || empty($empty))
				if ($hasErrors == 2)
				{
					if (array_key_exists($fieldLang, $rules['sizeLang']))
						$size = $rules['sizeLang'][$fieldLang];
					else
						$size = 1;
					$fields[$fieldLang][$matchIdLang[$defaultLanguage->id]] = $this->generateData($size, $rules['validateLang'][$fieldLang]);
				}
				else
					$returnErrors[] = sprintf(
						$this->l('This field %1$s is required at least in %2$s'),
						call_user_func(array($className, 'displayFieldName'), $fieldLang, $className),
						$defaultLanguage->name
					);
		}
		/* Checking for maximum multilingual fields size */
		foreach ($rules['sizeLang'] AS $fieldLang => $maxLength)
			foreach ($languages AS $language)
				if (isset($fields[$fieldLang][$language['id_lang']]) && $fields[$fieldLang] !== false AND Tools::strlen($fields[$fieldLang][$language['id_lang']]) > $maxLength)
					if ($hasErrors == 2)
						$fields[$fieldLang] = substr($fields[$fieldLang], 0, $maxLength);
					else
						$returnErrors[] = sprintf(
							$this->l('This field %1$s (%2$s) is too long: %3$d chars max.'),
							call_user_func(array($className, 'displayFieldName'), $fieldLang, $className),
							$language['name'],
							$maxLength
						);
		foreach ($rules['validateLang'] AS $fieldLang => $function)
			{
			foreach ($languages AS $language)
			{
					if (array_key_exists($fieldLang, $fields) AND array_key_exists($language['id_lang'], $fields[$fieldLang]) AND ($value = $fields[$fieldLang][$language['id_lang']]) !== false AND !empty($value))
				{
					if (!Validate::$function($value))
						if ($hasErrors == 2)
						{
							if (array_key_exists($fieldLang, $rules['sizeLang']))
								$size = $rules['sizeLang'][$fieldLang];
							else
								$size = 1;
							$fields[$fieldLang][$language['id_lang']] = $this->generateData($size, $rules['validateLang'][$fieldLang]);
						}
						else
							$returnErrors[] = sprintf(
								$this->l('The field %1$s (%2$s) is invalid.'),
								call_user_func(array($className, 'displayFieldName'), $fieldLang, $className),
								$language['name']
							);
				}
			}
		}
		}
		return $returnErrors;
	}

	private function validateRulesWS($rules, &$fields, $className, $languages, $defaultLanguage)
	{
		$returnErrors = array();
		$hasErrors = Tools::getValue('hasErrors');
		/* Checking for required fields */
		foreach ($rules['required'] AS $field)
			if (($value = $fields[$field]) == false AND (string)$value != '0')
				if ($hasErrors == 2)
				{
					if (array_key_exists($field, $rules['size']))
						$size = $rules['size'][$field];
					else
						$size = 1;
					$fields[$field] = $this->generateData($size, $rules['validate'][$field]);
				}
				else
					$returnErrors[] = sprintf(
						$this->l('The field %s is required.'),
						call_user_func(array($className, 'displayFieldName'), $field, $className)
					);

		/* Checking for maximum fields sizes */
		foreach ($rules['size'] AS $field => $maxLength)
			if (array_key_exists($field, $fields) AND $field != 'passwd')
				if ($fields[$field] !== false AND Tools::strlen($fields[$field]) > $maxLength)
					if ($hasErrors == 2)
						$fields[$field] = substr($fields[$field], 0, $maxLength);
					else
						$returnErrors[] = sprintf(
							$this->l('The field %1$s is too long (%2$d chars max).'),
							call_user_func(array($className, 'displayFieldName'), $field, $className),
							$maxLength
						);

		/* Checking for fields validity */
		foreach ($rules['validate'] AS $field => $function)
			if (array_key_exists($field, $fields))
				if (($value = $fields[$field]) !== false AND ($field != 'passwd'))
					if (!Validate::$function($value))
						if ($hasErrors == 2)
						{
							if (array_key_exists($field, $rules['size']))
								$size = $rules['size'][$field];
							else
								$size = 1;
							$fields[$field] = $this->generateData($size, $rules['validate'][$field]);
						}
						else
							$returnErrors[] = sprintf(
								$this->l('The field %s is invalid.'),
								call_user_func(array($className, 'displayFieldName'), $field, $className)
							);

		return $returnErrors;
	}
	public function checkAndAddLang ($languages, $add = true)
	{
		$errors = '';
		$moduleName = Tools::getValue('moduleName');
		$this->alterTable('language');
		foreach($languages as $language)
		{
			$iso = $language['iso_code'];
			if (!Language::isInstalled($iso))
			{
				if ($add)
				{
					if (@fsockopen('www.prestashop.com', 80))
					{
						if ($lang_pack = Tools::jsonDecode(Tools::file_get_contents('http://www.prestashop.com/download/lang_packs/get_language_pack.php?version='._PS_VERSION_.'&iso_lang='.$iso)))
						{
							if ($content = Tools::file_get_contents('http://www.prestashop.com/download/lang_packs/gzip/'.$lang_pack->version.'/'.$iso.'.gzip'))
							{
								$file = _PS_TRANSLATIONS_DIR_.$iso.'.gzip';
								if (file_put_contents($file, $content))
								{
									require_once('../../tools/tar/Archive_Tar.php');
									$gz = new Archive_Tar($file, true);
									if ($gz->extract(_PS_TRANSLATIONS_DIR_.'../', false))
									{
										if (!Language::checkAndAddLanguage($iso))
											$errors[] = $this->l('Archive cannot be extracted.');
										else
										{
											$newId = Language::getIdByIso($iso);
											Db::getInstance()->execute('UPDATE  `'._DB_PREFIX_.'lang`
																		SET  `id_lang_'.bqSQL($moduleName).'` =  '.(int)$language['id_lang'].'
																		WHERE  `id_lang` = '.(int)$newId);
										}
									}
									$errors[] = $this->l('Archive cannot be extracted.');
								}
								else
									$errors[] = $this->l('Server does not have permissions for writing.');
							}
							else
								$errors[] = $this->l('Language not found');
						}
						else
							$errors[] = $this->l('archive cannot be downloaded from prestashop.com.');
					}
					else
						$errors[] = $this->l('archive cannot be downloaded from prestashop.com.');
				}
			}
			else
			{
				$newId = Language::getIdByIso($iso);
				Db::getInstance()->execute('UPDATE  `'._DB_PREFIX_.'lang`
											SET  `id_lang_'.bqSQL($moduleName).'` =  '.(int)$language['id_lang'].'
											WHERE  `id_lang` = '.(int)$newId);
			}
		}
	}

	public function truncateTable($table)
	{
		switch ($table)
		{
			case 'customer' :
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'customer');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'customer_group');
				break;
			case 'address' :
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'address');
				break;
			case 'country' :
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'state');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'country');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'country_lang');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'country');
			case 'group' :
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'customer_group');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'group_lang');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'group');
				break;
			case 'combination' :
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_shop');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_combination');
				break;
			case 'category' :
				Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'category` WHERE id_category != 1');
				Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'category_lang` WHERE id_category != 1');
				Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'category` AUTO_INCREMENT = 2 ');
				foreach (scandir(_PS_CAT_IMG_DIR_) AS $d)
						if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d))
							unlink(_PS_CAT_IMG_DIR_.$d);
				Image::clearTmpDir();
			break;
			case 'product' :
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_shop');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'feature_product');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_lang');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'category_product');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_tag');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'image');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'image_lang');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_shop');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_combination');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'specific_price');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'specific_price_priority');

				Image::deleteAllImages(_PS_PROD_IMG_DIR_);
				Image::clearTmpDir();
				@mkdir(_PS_PROD_IMG_DIR_);
				break;
			case 'manufacturer' :
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'manufacturer');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'manufacturer_lang');
				foreach (scandir(_PS_MANU_IMG_DIR_) AS $d)
					if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d))
						unlink(_PS_MANU_IMG_DIR_.$d);
				Image::clearTmpDir();
				break;
			case 'Suppliers' :
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'supplier');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'supplier_lang');
				foreach (scandir(_PS_SUPP_IMG_DIR_) AS $d)
					if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d))
						unlink(_PS_SUPP_IMG_DIR_.$d);
				Image::clearTmpDir();
				break;
			case 'attribute' :
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_lang');
				break;
			case 'attributegroup' :
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_group');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_group_lang');
				break;
			case 'currency' :
			case 'customer' :
			case 'zone' :
			case 'state' :
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.bqSQL($table).'`');
				break;
		}
		return true;
	}

	public function cleanPositions($table, $shopList = null)
	{
		if ($table == 'category')
		{
			//clean category position
			$cat = Category::getCategories(1, false, false);
			foreach($cat AS $i => $categ)
				Category::cleanPositions((int)($categ['id_category']));
		}
		if ($table == 'product')
		{
			//clean products position
			$cat = Category::getCategories(1, false, false);
			foreach($cat AS $i => $categ)
				Product::cleanPositions((int)($categ['id_category']));
		}
	}

	public function getDefaultIdLang()
	{
		return;
	}

	private function generateData($size = 1, $type)
	{
		$type = str_replace('is', '', $type);
		$dom = array('com', 'net', 'org', 'biz', 'info');
		$alphaNum = '0123456789abcdefghijklmnopqrstuvwxyz';
		$alpha = 'abcdefghijklmnopqrstuvwxyz';
		$num = '0123456789';
		$return = '';
		switch($type)
		{
			case 'CityName':
			case 'Name':
			case 'GenericName':
			case 'CatalogName':
			case 'Address':
			case 'LinkRewrite':
			case 'String':
				$a = mt_rand($size/2, $size);
				for ($i = 1; $i <= $a; $i++)
		 			$return .= substr($alpha, mt_rand(0, strlen($alpha)), 1);
			break;
			case 'LanguageIsoCode':
				for ($i = 1; $i <= 2; $i++)
		 			$return .= substr($alpha, mt_rand(0, strlen($alpha)), 1);
			break;
			case 'LanguageCode':
				for ($i = 1; $i <= 2; $i++)
				  $return .= substr($alphaNum, mt_rand(0, strlen($alphaNum)), 1);
				$return .= '-';
				for ($i = 1; $i <= 2; $i++)
				  $return .= substr($alphaNum, mt_rand(0, strlen($alphaNum)), 1);
			break;
			case 'Bool':
				$return .= mt_rand(0,1);
			break;
			case 'Int':
			case 'UnsignedId':
			case 'NumericIsoCode':
			case 'PhoneNumber':
			case 'PostCode':
				$a = mt_rand($size/2, $size);
				for ($i = 1; $i <= $a; $i++)
		 			$return .= substr($num, mt_rand(0, strlen($num)), 1);
			break;
			case 'Price':
			case 'Float':
				$a = mt_rand(4, 10);
				$b = mt_rand(4, 10);
				for ($i = 1; $i <= $a; $i++)
				  $return .= substr($alphaNum, mt_rand(0, strlen($alphaNum)), 1);
				$return .= '-';
				for ($i = 1; $i <= $b; $i++)
				  $return .= substr($alphaNum, mt_rand(0, strlen($alphaNum)), 1);
				$return .= '.'.$dom[mt_rand(0, (sizeof($dom)-1))];
			break;
			case 'ZipCodeFormat';
				$str = 'NLC -';
				$a = mt_rand($size/2, $size);
				for ($i = 1; $i <= $a; $i++)
		 			$return .= substr($str, mt_rand(0, strlen($str)), 1);
			break;
			case 'StateIsoCode';

			break;
			case 'Email':
				$a = mt_rand(4, 10);
				$b = mt_rand(4, 10);
				for ($i = 1; $i <= $a; $i++)
				  $return .= substr($alphaNum, mt_rand(0, strlen($alphaNum)), 1);
				$return .= '@';
				for ($i = 1; $i <= $b; $i++)
				  $return .= substr($alphaNum, mt_rand(0, strlen($alphaNum)), 1);
				$return .= '.'.$dom[mt_rand(0, (sizeof($dom)-1))];
			break;
			case 'Passwd':
				$a = mt_rand($size/2, $size);
				for ($i = 1; $i <= $a; $i++)
		 			$return .= substr($alphaNum, mt_rand(0, strlen($alphaNum)), 1);
			break;
			case 'BirthDate':
				$return .= '1970-01-01 00:00:00';
			break;
		}
		return $return;
	}

	private function cartToOrder($items, $foreignKey)
	{
		$this->alterTable('order');
		foreach($items as $item)
		{
			$order = new Order();
			$order->id_carrier = (int)$item['id_carrier'];
			$order->id_customer = (int)$foreignKey['id_customer'][$item['id_customer']];
			$order->id_address_invoice = (int)$foreignKey['id_address_invoice'][$item['id_address_invoice']];
			$order->id_address_delivery = (int)$foreignKey['id_address_delivery'][$item['id_address_delivery']];

			$vat_address = new Address((int)$foreignKey['id_address_delivery'][$item['id_address_delivery']]);
			$id_zone = Address::getZoneById((int)$vat_address->id);
			$order->id_currency = (int)$item['id_currency'];
			$order->id_lang = (int)$item['id_lang'];
			$order->id_cart = (int)$foreignKey['id_cart'][$item['id_cart']];

			$customer = new Customer((int)$order->id_customer);
			$order->secure_key = pSQL($customer->secure_key);
			if (!strlen(trim($item['payment'])))
				$order->payment = 'payment'.Tools::getValue('moduleName');
			else
				$order->payment = utf8_encode(html_entity_decode(strip_tags(Tools::substr($item['payment'], 0, 32))));
			if (isset($this->name))
				$order->module = $this->name;

			$carrier = new Carrier((int)$item['id_carrier']);

			$currency = new Currency($order->id_currency);
			$order->conversion_rate = !empty($currency->conversion_rate) ? $currency->conversion_rate : 1;
			$order->total_products = (float)$item['total_products'];
			$order->total_products_wt = (float)$item['total_products_wt'];
			$order->total_discounts = (float)$item['total_discounts'];
			$order->total_shipping = (float)$item['total_shipping'];
			$order->carrier_tax_rate = (float)$carrier->getTaxesRate(new Address((int)$item[Configuration::get('PS_TAX_ADDRESS_TYPE')]));
			$order->total_wrapping = (float)$item['total_wrapping'];
			$order->total_paid = (float)$item['total_paid'];
			$order->total_paid_real = (float)$item['total_paid_real'];
			$order->invoice_date = '0000-00-00 00:00:00';
			$order->delivery_date = '0000-00-00 00:00:00';
			if(array_key_exists('date_add', $item))
				$order->date_add = $item['date_add'];
			if(array_key_exists('date_upd', $item))
				$order->date_upd = $item['date_upd'];
			//test valid paid
			if($item['total_paid'] == $item['total_paid_real'])
				$order->valid = 1;
			else
				$order->valid = 0;
			$order->save(false, false);

			$this->saveMatchId('order', (int)$order->id, (int)$item['id_cart']);
		}
	}
}
