<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

@ini_set('max_execution_time', 0);
/** No max line limit since the lines can be more than 4096. Performance impact is not significant. */
define('MAX_LINE_SIZE', 0);

/** Used for validatefields diying without user friendly error or not */
define('UNFRIENDLY_ERROR', false);

/** this value set the number of columns visible on each page */
define('MAX_COLUMNS', 6);

/** correct Mac error on eof */
@ini_set('auto_detect_line_endings', '1');

class AdminImportControllerCore extends AdminController
{
	public static $column_mask;

	public $entities = array();

	public $available_fields = array();

	public $required_fields = array('name');

	public $cache_image_deleted = array();

	public static $default_values = array();

	public static $validators = array(
		'active' => array('AdminImportController', 'getBoolean'),
		'tax_rate' => array('AdminImportController', 'getPrice'),
		 /** Tax excluded */
		'price_tex' => array('AdminImportController', 'getPrice'),
		 /** Tax included */
		'price_tin' => array('AdminImportController', 'getPrice'),
		'reduction_price' => array('AdminImportController', 'getPrice'),
		'reduction_percent' => array('AdminImportController', 'getPrice'),
		'wholesale_price' => array('AdminImportController', 'getPrice'),
		'ecotax' => array('AdminImportController', 'getPrice'),
		'name' => array('AdminImportController', 'createMultiLangField'),
		'description' => array('AdminImportController', 'createMultiLangField'),
		'description_short' => array('AdminImportController', 'createMultiLangField'),
		'meta_title' => array('AdminImportController', 'createMultiLangField'),
		'meta_keywords' => array('AdminImportController', 'createMultiLangField'),
		'meta_description' => array('AdminImportController', 'createMultiLangField'),
		'link_rewrite' => array('AdminImportController', 'createMultiLangField'),
		'available_now' => array('AdminImportController', 'createMultiLangField'),
		'available_later' => array('AdminImportController', 'createMultiLangField'),
		'category' => array('AdminImportController', 'split'),
		'online_only' => array('AdminImportController', 'getBoolean')
	);

	public $separator;
	public $multiple_value_separator;

	public function __construct()
	{
		$this->entities = array(
			$this->l('Categories'),
			$this->l('Products'),
			$this->l('Combinations'),
			$this->l('Customers'),
			$this->l('Addresses'),
			$this->l('Manufacturers'),
			$this->l('Suppliers'),
		);

		// @since 1.5.0
		if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'))
		{
			$this->entities = array_merge(
				$this->entities,
				array(
					$this->l('Supply Orders'),
					$this->l('Supply Order Details'),
				)
			);
		}

		$this->entities = array_flip($this->entities);

		switch ((int)Tools::getValue('entity'))
		{
			case $this->entities[$this->l('Combinations')]:
				$this->required_fields = array(
					'id_product',
					'group',
					'attribute'
				);

				$this->available_fields = array(
					'no' => array('label' => $this->l('Ignore this column')),
					'id_product' => array('label' => $this->l('Product ID').'*'),
					'group' => array(
						'label' => $this->l('Attribute (Name:Type:Position)').'*'
					),
					'attribute' => array(
						'label' => $this->l('Value (Value:Position)').'*'
					),
					'supplier_reference' => array('label' => $this->l('Supplier reference')),
					'reference' => array('label' => $this->l('Reference')),
					'ean13' => array('label' => $this->l('EAN13')),
					'upc' => array('label' => $this->l('UPC')),
					'wholesale_price' => array('label' => $this->l('Wholesale price')),
					'price' => array('label' => $this->l('Impact on price')),
					'ecotax' => array('label' => $this->l('Ecotax')),
					'quantity' => array('label' => $this->l('Quantity')),
					'minimal_quantity' => array('label' => $this->l('Minimal quantity')),
					'weight' => array('label' => $this->l('Impact on weight')),
					'default_on' => array('label' => $this->l('Default (0 = No, 1 = Yes)')),
					'image_position' => array(
						'label' => $this->l('Image position')
					),
					'image_url' => array('label' => $this->l('Image URL')),
					'delete_existing_images' => array(
						'label' => $this->l('Delete existing images (0 = No, 1 = Yes)')
					),
					'shop' => array(
						'label' => $this->l('ID / Name of shop'),
						'help' => $this->l('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.'),
					)
				);

				self::$default_values = array(
					'reference' => '',
					'supplier_reference' => '',
					'ean13' => '',
					'upc' => '',
					'wholesale_price' => 0,
					'price' => 0,
					'ecotax' => 0,
					'quantity' => 0,
					'minimal_quantity' => 1,
					'weight' => 0,
					'default_on' => 0,
				);
			break;

			case $this->entities[$this->l('Categories')]:
				$this->available_fields = array(
					'no' => array('label' => $this->l('Ignore this column')),
					'id' => array('label' => $this->l('ID')),
					'active' => array('label' => $this->l('Active (0/1)')),
					'name' => array('label' => $this->l('Name *')),
					'parent' => array('label' => $this->l('Parent category')),
					'is_root_category' => array(
						'label' => $this->l('Root category (0/1)'),
						'help' => $this->l('A category root is where a category tree can begin. This is used with multistore.')
						),
					'description' => array('label' => $this->l('Description')),
					'meta_title' => array('label' => $this->l('Meta title')),
					'meta_keywords' => array('label' => $this->l('Meta keywords')),
					'meta_description' => array('label' => $this->l('Meta description')),
					'link_rewrite' => array('label' => $this->l('URL rewritten')),
					'image' => array('label' => $this->l('Image URL')),
					'shop' => array(
						'label' => $this->l('ID / Name of shop'),
						'help' => $this->l('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.'),
					),
				);

				self::$default_values = array(
					'active' => '1',
					'parent' => Configuration::get('PS_HOME_CATEGORY'),
					'link_rewrite' => ''
				);
			break;

			case $this->entities[$this->l('Products')]:
				self::$validators['image'] = array(
					'AdminImportController',
					'split'
				);

				$this->available_fields = array(
					'no' => array('label' => $this->l('Ignore this column')),
					'id' => array('label' => $this->l('ID')),
					'active' => array('label' => $this->l('Active (0/1)')),
					'name' => array('label' => $this->l('Name *')),
					'category' => array('label' => $this->l('Categories (x,y,z...)')),
					'price_tex' => array('label' => $this->l('Price tax excluded')),
					'price_tin' => array('label' => $this->l('Price tax included')),
					'id_tax_rules_group' => array('label' => $this->l('Tax rules ID')),
					'wholesale_price' => array('label' => $this->l('Wholesale price')),
					'on_sale' => array('label' => $this->l('On sale (0/1)')),
					'reduction_price' => array('label' => $this->l('Discount amount')),
					'reduction_percent' => array('label' => $this->l('Discount percent')),
					'reduction_from' => array('label' => $this->l('Discount from (yyyy-mm-dd)')),
					'reduction_to' => array('label' => $this->l('Discount to (yyyy-mm-dd)')),
					'reference' => array('label' => $this->l('Reference #')),
					'supplier_reference' => array('label' => $this->l('Supplier reference #')),
					'supplier' => array('label' => $this->l('Supplier')),
					'manufacturer' => array('label' => $this->l('Manufacturer')),
					'ean13' => array('label' => $this->l('EAN13')),
					'upc' => array('label' => $this->l('UPC')),
					'ecotax' => array('label' => $this->l('Ecotax')),
					'weight' => array('label' => $this->l('Weight')),
					'quantity' => array('label' => $this->l('Quantity')),
					'description_short' => array('label' => $this->l('Short description')),
					'description' => array('label' => $this->l('Description')),
					'tags' => array('label' => $this->l('Tags (x,y,z...)')),
					'meta_title' => array('label' => $this->l('Meta title')),
					'meta_keywords' => array('label' => $this->l('Meta keywords')),
					'meta_description' => array('label' => $this->l('Meta description')),
					'link_rewrite' => array('label' => $this->l('URL rewritten')),
					'available_now' => array('label' => $this->l('Text when in stock')),
					'available_later' => array('label' => $this->l('Text when backorder allowed')),
					'available_for_order' => array('label' => $this->l('Available for order (0 = No, 1 = Yes)')),
					'date_add' => array('label' => $this->l('Product creation date')),
					'show_price' => array('label' => $this->l('Show price (0 = No, 1 = Yes)')),
					'image' => array('label' => $this->l('Image URLs (x,y,z...)')),
					'delete_existing_images' => array(
						'label' => $this->l('Delete existing images (0 = No, 1 = Yes)')
					),
					'features' => array('label' => $this->l('Feature(Name:Value:Position)')),
					'online_only' => array('label' => $this->l('Available online only (0 = No, 1 = Yes)')),
					'condition' => array('label' => $this->l('Condition')),
					'shop' => array(
						'label' => $this->l('ID / Name of shop'),
						'help' => $this->l('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.'),
					)
				);

				self::$default_values = array(
					'id_category' => array((int)Configuration::get('PS_HOME_CATEGORY')),
					'id_category_default' => (int)Configuration::get('PS_HOME_CATEGORY'),
					'active' => '1',
					'quantity' => 0,
					'price' => 0,
					'id_tax_rules_group' => 0,
					'description_short' => array((int)Configuration::get('PS_LANG_DEFAULT') => ''),
					'link_rewrite' => array((int)Configuration::get('PS_LANG_DEFAULT') => ''),
					'online_only' => 0,
					'condition' => 'new',
					'date_add' => date('Y-m-d H:i:s'),
					'condition' => 'new',
				);
			break;

			case $this->entities[$this->l('Customers')]:
				//Overwrite required_fields AS only email is required whereas other entities
				$this->required_fields = array('email', 'passwd', 'lastname', 'firstname');

				$this->available_fields = array(
					'no' => array('label' => $this->l('Ignore this column')),
					'id' => array('label' => $this->l('ID')),
					'active' => array('label' => $this->l('Active  (0/1)')),
					'id_gender' => array('label' => $this->l('Titles ID (Mr = 1, Ms = 2, else 0)')),
					'email' => array('label' => $this->l('Email *')),
					'passwd' => array('label' => $this->l('Password *')),
					'birthday' => array('label' => $this->l('Birthday (yyyy-mm-dd)')),
					'lastname' => array('label' => $this->l('Last Name *')),
					'firstname' => array('label' => $this->l('First Name *')),
					'newsletter' => array('label' => $this->l('Newsletter (0/1)')),
					'optin' => array('label' => $this->l('Opt-in (0/1)')),
					'id_shop' => array(
						'label' => $this->l('ID / Name of shop'),
						'help' => $this->l('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.'),
					),
				);

				self::$default_values = array(
					'active' => '1',
					'id_shop' => Configuration::get('PS_SHOP_DEFAULT'),
				);
			break;

			case $this->entities[$this->l('Addresses')]:
				//Overwrite required_fields
				$this->required_fields = array(
					'lastname',
					'firstname',
					'address1',
					'postcode',
					'country',
					'customer_email',
					'city'
				);

				$this->available_fields = array(
					'no' => array('label' => $this->l('Ignore this column')),
					'id' => array('label' => $this->l('ID')),
					'alias' => array('label' => $this->l('Alias *')),
					'active' => array('label' => $this->l('Active  (0/1)')),
					'customer_email' => array('label' => $this->l('Customer email')),
					'id_customer' => array('label' => $this->l('Customer ID:')),
					'manufacturer' => array('label' => $this->l('Manufacturer')),
					'supplier' => array('label' => $this->l('Supplier')),
					'company' => array('label' => $this->l('Company')),
					'lastname' => array('label' => $this->l('Last Name *')),
					'firstname' => array('label' => $this->l('First Name *')),
					'address1' => array('label' => $this->l('Address 1 *')),
					'address2' => array('label' => $this->l('Address 2')),
					'postcode' => array('label' => $this->l('Postal code / Zipcode*')),
					'city' => array('label' => $this->l('City *')),
					'country' => array('label' => $this->l('Country *')),
					'state' => array('label' => $this->l('State')),
					'other' => array('label' => $this->l('Other')),
					'phone' => array('label' => $this->l('Phone')),
					'phone_mobile' => array('label' => $this->l('Mobile Phone')),
					'vat_number' => array('label' => $this->l('VAT number')),
				);

				self::$default_values = array(
					'alias' => 'Alias',
					'postcode' => 'X'
				);
			break;

			case $this->entities[$this->l('Manufacturers')]:
			case $this->entities[$this->l('Suppliers')]:
				//Overwrite validators AS name is not MultiLangField
				self::$validators = array(
					'description' => array('AdminImportController', 'createMultiLangField'),
					'short_description' => array('AdminImportController', 'createMultiLangField'),
					'meta_title' => array('AdminImportController', 'createMultiLangField'),
					'meta_keywords' => array('AdminImportController', 'createMultiLangField'),
					'meta_description' => array('AdminImportController', 'createMultiLangField'),
				);

				$this->available_fields = array(
					'no' => array('label' => $this->l('Ignore this column')),
					'id' => array('label' => $this->l('ID')),
					'active' => array('label' => $this->l('Active (0/1)')),
					'name' => array('label' => $this->l('Name *')),
					'description' => array('label' => $this->l('Description')),
					'short_description' => array('label' => $this->l('Short description')),
					'meta_title' => array('label' => $this->l('Meta title')),
					'meta_keywords' => array('label' => $this->l('Meta keywords')),
					'meta_description' => array('label' => $this->l('Meta description')),
					'shop' => array(
						'label' => $this->l('ID / Name of group shop'),
						'help' => $this->l('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.'),
					),
				);

				self::$default_values = array(
					'shop' => Shop::getGroupFromShop(Configuration::get('PS_SHOP_DEFAULT')),
				);
			break;
			// @since 1.5.0
			case $this->entities[$this->l('Supply Orders')]:
				if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'))
				{
					// required fields
					$this->required_fields = array(
						'id_supplier',
						'id_warehouse',
						'reference',
						'date_delivery_expected',
					);
					// available fields
					$this->available_fields = array(
						'no' => array('label' => $this->l('Ignore this column')),
						'id' => array('label' => $this->l('ID')),
						'id_supplier' => array('label' => $this->l('Supplier ID *')),
						'id_lang' => array('label' => $this->l('Lang ID')),
						'id_warehouse' => array('label' => $this->l('Warehouse ID *')),
						'id_currency' => array('label' => $this->l('Currency ID *')),
						'reference' => array('label' => $this->l('Supply Order Reference *')),
						'date_delivery_expected' => array('label' => $this->l('Delivery Date (Y-M-D)*')),
						'discount_rate' => array('label' => $this->l('Discount Rate')),
						'is_template' => array('label' => $this->l('Template')),
					);
					// default values
					self::$default_values = array(
						'id_lang' => (int)Configuration::get('PS_LANG_DEFAULT'),
						'id_currency' => Currency::getDefaultCurrency()->id,
						'discount_rate' => '0',
						'is_template' => '0',
					);
				}
			break;
			// @since 1.5.0
			case $this->entities[$this->l('Supply Order Details')]:
				if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'))
				{
					// required fields
					$this->required_fields = array(
						'supply_order_reference',
						'id_product',
						'unit_price_te',
						'quantity_expected',
					);
					// available fields
					$this->available_fields = array(
						'no' => array('label' => $this->l('Ignore this column')),
						'supply_order_reference' => array('label' => $this->l('Supply Order Reference *')),
						'id_product' => array('label' => $this->l('Product ID *')),
						'id_product_attribute' => array('label' => $this->l('Product Attribute ID')),
						'unit_price_te' => array('label' => $this->l('Unit Price (tax excl.)*')),
						'quantity_expected' => array('label' => $this->l('Quantity Expected *')),
						'discount_rate' => array('label' => $this->l('Discount Rate')),
						'tax_rate' => array('label' => $this->l('Tax Rate')),
					);
					// default values
					self::$default_values = array(
						'discount_rate' => '0',
						'tax_rate' => '0',
					);
				}
		}

		$this->separator = strval(trim(Tools::getValue('separator', ';')));

		if (is_null(Tools::getValue('multiple_value_separator')) || trim(Tools::getValue('multiple_value_separator')) == '')
			$this->multiple_value_separator = ',';
		else
			$this->multiple_value_separator = Tools::getValue('multiple_value_separator');

		parent::__construct();
	}

	public function renderForm()
	{
		if (!is_writable(_PS_ADMIN_DIR_.'/import/'))
			$this->displayWarning($this->l('Directory import on admin directory must be writable (CHMOD 755 / 777)'));

		if (isset($this->warnings) && count($this->warnings))
		{
			$warnings = array();
			foreach ($this->warnings as $warning)
				$warnings[] = $warning;
		}

		$files_to_import = scandir(_PS_ADMIN_DIR_.'/import/');
		uasort($files_to_import, array('AdminImportController', 'usortFiles'));
		foreach ($files_to_import as $k => &$filename)
			//exclude .  ..  .svn and index.php and all hidden files
			if (preg_match('/^\..*|index\.php/i', $filename))
				unset($files_to_import[$k]);
		unset($filename);

		$this->fields_form = array('');

		$this->toolbar_scroll = false;
		$this->toolbar_btn = array();

		// adds fancybox
		$this->addCSS(_PS_CSS_DIR_.'jquery.fancybox-1.3.4.css', 'screen');
		$this->addJqueryPlugin(array('fancybox'));

		$entity_selected = 0;
		if (isset($this->entities[$this->l(Tools::ucfirst(Tools::getValue('import_type')))]))
		{
			$entity_selected = $this->entities[$this->l(Tools::ucfirst(Tools::getValue('import_type')))];
			$this->context->cookie->entity_selected = $entity_selected;
		}
		elseif(isset($this->context->cookie->entity_selected))
			$entity_selected = (int)$this->context->cookie->entity_selected;

		$this->tpl_form_vars = array(
			'module_confirmation' => (Tools::getValue('import')) && (isset($this->warnings) && !count($this->warnings)),
			'path_import' => _PS_ADMIN_DIR_.'/import/',
			'entities' => $this->entities,
			'entity_selected' => $entity_selected,
			'files_to_import' => $files_to_import,
			'languages' => Language::getLanguages(false),
			'id_language' => $this->context->language->id,
			'available_fields' => $this->getAvailableFields(),
			'truncateAuthorized' => (Shop::isFeatureActive() && $this->context->employee->isSuperAdmin()) || !Shop::isFeatureActive(),
			'PS_ADVANCED_STOCK_MANAGEMENT' => Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'),
		);

		return parent::renderForm();
	}

	public function renderView()
	{
		$this->addJS(_PS_JS_DIR_.'adminImport.js');

		$handle = $this->openCsvFile();
		$nb_column = $this->getNbrColumn($handle, $this->separator);
		$nb_table = ceil($nb_column / MAX_COLUMNS);

		$res = array();
		foreach ($this->required_fields as $elem)
			$res[] = '\''.$elem.'\'';

		$data = array();
		for ($i = 0; $i < $nb_table; $i++)
			$data[$i] = $this->generateContentTable($i, $nb_column, $handle, $this->separator);

		if ($entity_selected = (int)Tools::getValue('entity'))
			$this->context->cookie->entity_selected = $entity_selected;

		$this->tpl_view_vars = array(
			'import_matchs' => Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'import_match'),
			'fields_value' => array(
				'csv' => Tools::getValue('csv'),
				'convert' => Tools::getValue('convert'),
				'entity' => (int)Tools::getValue('entity'),
				'iso_lang' => Tools::getValue('iso_lang'),
				'truncate' => Tools::getValue('truncate'),
				'forceIDs' => Tools::getValue('forceIDs'),
				'match_ref' => Tools::getValue('match_ref'),
				'separator' => $this->separator,
				'multiple_value_separator' => $this->multiple_value_separator
			),
			'nb_table' => $nb_table,
			'nb_column' => $nb_column,
			'res' => implode(',', $res),
			'max_columns' => MAX_COLUMNS,
			'no_pre_select' => array('price_tin', 'feature'),
			'available_fields' => $this->available_fields,
			'data' => $data
		);

		return parent::renderView();
	}

	public function initToolbar()
	{
		switch ($this->display)
		{
			case 'import':
				// Default cancel button - like old back link
				$back = Tools::safeOutput(Tools::getValue('back', ''));
				if (empty($back))
					$back = self::$currentIndex.'&token='.$this->token;

				$this->toolbar_btn['cancel'] = array(
					'href' => $back,
					'desc' => $this->l('Cancel')
				);
				// Default save button - action dynamically handled in javascript
				$this->toolbar_btn['save-import'] = array(
					'href' => '#',
					'desc' => $this->l('Import .CSV data')
				);
				break;
		}
	}

	protected function generateContentTable($current_table, $nb_column, $handle, $glue)
	{
		$html = '<table id="table'.$current_table.'" style="display: none;" class="table" cellspacing="0" cellpadding="0">
					<tr>';

		// Header
		for ($i = 0; $i < $nb_column; $i++)
			if (MAX_COLUMNS * (int)$current_table <= $i && (int)$i < MAX_COLUMNS * ((int)$current_table + 1))
				$html .= '<th style="width: '.(900 / MAX_COLUMNS).'px; vertical-align: top; padding: 4px">
							<select onchange="askFeatureName(this, '.$i.');"
								style="width: '.(900 / MAX_COLUMNS).'px;"
								id="type_value['.$i.']"
								name="type_value['.$i.']"
								class="type_value">
								'.$this->getTypeValuesOptions($i).'
							</select>
							<div id="features_'.$i.'" style="display: none;">
								<input style="width: 90px" type="text" name="" id="feature_name_'.$i.'">
								<input type="button" value="ok" onclick="replaceFeature($(\'#feature_name_'.$i.'\').attr(\'name\'), '.$i.');">
							</div>
						</th>';
		$html .= '</tr>';

		AdminImportController::setLocale();
		for ($current_line = 0; $current_line < 10 && $line = fgetcsv($handle, MAX_LINE_SIZE, $glue); $current_line++)
		{
			/* UTF-8 conversion */
			if (Tools::getValue('convert'))
				$line = $this->utf8EncodeArray($line);
			$html .= '<tr id="table_'.$current_table.'_line_'.$current_line.'" style="padding: 4px">';
			foreach ($line as $nb_c => $column)
				if ((MAX_COLUMNS * (int)$current_table <= $nb_c) && ((int)$nb_c < MAX_COLUMNS * ((int)$current_table + 1)))
					$html .= '<td>'.htmlentities(substr($column, 0, 200), ENT_QUOTES, 'UTF-8').'</td>';
			$html .= '</tr>';
		}
		$html .= '</table>';
		AdminImportController::rewindBomAware($handle);
		return $html;
	}

	public function init()
	{
		parent::init();
		if (Tools::isSubmit('submitImportFile'))
			$this->display = 'import';
	}

	public function initContent()
	{
		// toolbar (save, cancel, new, ..)
		$this->initToolbar();
		if ($this->display == 'import')
			if (Tools::getValue('csv'))
				$this->content .= $this->renderView();
			else
			{
				$this->errors[] = $this->l('You must upload a file in order to proceed to the next step');
				$this->content .= $this->renderForm();
			}
		else
			$this->content .= $this->renderForm();

		$this->context->smarty->assign(array(
			'content' => $this->content,
			'url_post' => self::$currentIndex.'&token='.$this->token,
		));
	}

	protected static function rewindBomAware($handle)
	{
		// A rewind wrapper that skip BOM signature wrongly
		rewind($handle);
		if (($bom = fread($handle, 3)) != "\xEF\xBB\xBF")
			rewind($handle);
	}

	protected static function getBoolean($field)
	{
		return (boolean)$field;
	}

	protected static function getPrice($field)
	{
		$field = ((float)str_replace(',', '.', $field));
		$field = ((float)str_replace('%', '', $field));
		return $field;
	}

	protected static function split($field)
	{
		if (empty($field))
			return array();

		$separator = Tools::getValue('multiple_value_separator');
		if (is_null($separator) || trim($separator) == '')
			$separator = ',';

		do $uniqid_path = _PS_UPLOAD_DIR_.uniqid(); while (file_exists($uniqid_path));
		file_put_contents($uniqid_path, $field);
		$tab = '';
		if (!empty($uniqid_path))
		{
			$fd = fopen($uniqid_path, 'r');
			$tab = fgetcsv($fd, MAX_LINE_SIZE, $separator);
			fclose($fd);
			unlink($uniqid_path);
		}

		if (empty($tab) || (!is_array($tab)))
			return array();
		return $tab;
	}

	protected static function createMultiLangField($field)
	{
		$languages = Language::getLanguages(false);
		$res = array();
		foreach ($languages as $lang)
			$res[$lang['id_lang']] = $field;
		return $res;
	}

	protected function getTypeValuesOptions($nb_c)
	{
		$i = 0;
		$no_pre_select = array('price_tin', 'feature');

		$options = '';
		foreach ($this->available_fields as $k => $field)
		{
			$options .= '<option value="'.$k.'"';
			if ($k === 'price_tin')
				++$nb_c;
			if ($i === ($nb_c + 1) && (!in_array($k, $no_pre_select)))
				$options .= ' selected="selected"';
			$options .= '>'.$field['label'].'</option>';
			++$i;
		}
		return $options;
	}

	/*
	* Return fields to be display AS piece of advise
	*
	* @param $in_array boolean
	* @return string or return array
	*/
	public function getAvailableFields($in_array = false)
	{
		$i = 0;
		$fields = array();
		$keys = array_keys($this->available_fields);
		array_shift($keys);
		foreach ($this->available_fields as $k => $field)
		{
			if ($k === 'no')
				continue;
			if ($k === 'price_tin')
				$fields[$i - 1] = '<div>'.$this->available_fields[$keys[$i - 1]]['label'].' '.$this->l('or').' '.$field['label'].'<span style="margin-left:16px"></span></div>';
			else
			{
				if (isset($field['help']))
					$html = '&nbsp;<a href="#" class="info_import" title="'.$this->l('Info').'|'.$field['help'].'"><img src="'._PS_ADMIN_IMG_.'information.png"></a>';
				else
					$html = '<span style="margin-left:16px"></span>';
				$fields[] = '<div>'.$field['label'].$html.'</div>';
			}
			++$i;
		}
		if ($in_array)
			return $fields;
		else
			return implode("\n\r", $fields);
	}

	protected function receiveTab()
	{
		$type_value = Tools::getValue('type_value') ? Tools::getValue('type_value') : array();
		foreach ($type_value as $nb => $type)
			if ($type != 'no')
				self::$column_mask[$type] = $nb;
	}

	public static function getMaskedRow($row)
	{
		$res = array();
		if (is_array(self::$column_mask))
			foreach (self::$column_mask as $type => $nb)
				$res[$type] = isset($row[$nb]) ? $row[$nb] : null;

		if (Tools::getValue('forceIds')) // if you choose to force table before import the column id is remove from the CSV file.
			unset($res['id']);

		return $res;
	}

	protected static function setDefaultValues(&$info)
	{
		foreach (self::$default_values as $k => $v)
			if (!isset($info[$k]) || $info[$k] == '')
				$info[$k] = $v;
	}

	protected static function setEntityDefaultValues(&$entity)
	{
		$members = get_object_vars($entity);
		foreach (self::$default_values as $k => $v)
			if ((array_key_exists($k, $members) && $entity->$k === null) || !array_key_exists($k, $members))
				$entity->$k = $v;
	}

	protected static function fillInfo($infos, $key, &$entity)
	{
		$infos = trim($infos);
		if (isset(self::$validators[$key][1]) && self::$validators[$key][1] == 'createMultiLangField' && Tools::getValue('iso_lang'))
		{
			$id_lang = Language::getIdByIso(Tools::getValue('iso_lang'));
			$tmp = call_user_func(self::$validators[$key], $infos);
			foreach ($tmp as $id_lang_tmp => $value)
				if (empty($entity->{$key}[$id_lang_tmp]) || $id_lang_tmp == $id_lang)
					$entity->{$key}[$id_lang_tmp] = $value;
		}
		else
			if (!empty($infos) || $infos == '0') // ($infos == '0') => if you want to disable a product by using "0" in active because empty('0') return true 
				$entity->{$key} = isset(self::$validators[$key]) ? call_user_func(self::$validators[$key], $infos) : $infos;

		return true;
	}

	public static function arrayWalk(&$array, $funcname, &$user_data = false)
	{
		if (!is_callable($funcname)) return false;

		foreach ($array as $k => $row)
			if (!call_user_func_array($funcname, array($row, $k, $user_data)))
				return false;
		return true;
	}

	/**
	 * copyImg copy an image located in $url and save it in a path
	 * according to $entity->$id_entity .
	 * $id_image is used if we need to add a watermark
	 *
	 * @param int $id_entity id of product or category (set in entity)
	 * @param int $id_image (default null) id of the image if watermark enabled.
	 * @param string $url path or url to use
	 * @param string entity 'products' or 'categories'
	 * @return void
	 */
	protected static function copyImg($id_entity, $id_image = null, $url, $entity = 'products')
	{
		$tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
		$watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));

		switch ($entity)
		{
			default:
			case 'products':
				$image_obj = new Image($id_image);
				$path = $image_obj->getPathForCreation();
			break;
			case 'categories':
				$path = _PS_CAT_IMG_DIR_.(int)$id_entity;
			break;
		}
		$url = str_replace(' ', '%20', trim($url));

		// Evaluate the memory required to resize the image: if it's too much, you can't resize it.
		if (!ImageManager::checkImageMemoryLimit($url))
			return false;

		// 'file_exists' doesn't work on distant file, and getimagesize make the import slower.
		// Just hide the warning, the traitment will be the same.
		if (Tools::copy($url, $tmpfile))
		{
			ImageManager::resize($tmpfile, $path.'.jpg');
			$images_types = ImageType::getImagesTypes($entity);
			foreach ($images_types as $image_type)
				ImageManager::resize($tmpfile, $path.'-'.stripslashes($image_type['name']).'.jpg', $image_type['width'], $image_type['height']);

			if (in_array($image_type['id_image_type'], $watermark_types))
				Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_entity));
		}
		else
		{
			unlink($tmpfile);
			return false;
		}
		unlink($tmpfile);
		return true;
	}

	public function categoryImport()
	{
		$cat_moved = array();

		$this->receiveTab();
		$handle = $this->openCsvFile();
		$default_language_id = (int)Configuration::get('PS_LANG_DEFAULT');
		AdminImportController::setLocale();
		for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator); $current_line++)
		{
			if (Tools::getValue('convert'))
				$line = $this->utf8EncodeArray($line);
			$info = AdminImportController::getMaskedRow($line);

			$tab_categ = array(Configuration::get('PS_HOME_CATEGORY'), Configuration::get('PS_ROOT_CATEGORY'));
			if (isset($info['id']) && in_array((int)$info['id'], $tab_categ))
			{
				$this->errors[] = Tools::displayError('The ID category cannot be the same as the ID Root category or the ID Home category.');
				continue;
			}
			AdminImportController::setDefaultValues($info);

			if (Tools::getValue('forceIDs') && isset($info['id']) && (int)$info['id'])
				$category = new Category((int)$info['id']);
			else
			{
				if (isset($info['id']) && (int)$info['id'] && Category::existsInDatabase((int)$info['id'], 'category'))
					$category = new Category((int)$info['id']);
				else
					$category = new Category();
			}

			AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $category);

			if (isset($category->parent) && is_numeric($category->parent))
			{
				if (isset($cat_moved[$category->parent