<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 8971 $
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
					$this->l('SupplyOrders'),
					$this->l('SupplyOrdersDetails'),
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
						'help' => $this->l('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, default shop will be used'),
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
						'help' => $this->l('A category root is where a category tree can begin. This is used with multistore')
						),
					'description' => array('label' => $this->l('Description')),
					'meta_title' => array('label' => $this->l('Meta-title')),
					'meta_keywords' => array('label' => $this->l('Meta-keywords')),
					'meta_description' => array('label' => $this->l('Meta-description')),
					'link_rewrite' => array('label' => $this->l('URL rewritten')),
					'image' => array('label' => $this->l('Image URL')),
					'shop' => array(
						'label' => $this->l('ID / Name of shop'),
						'help' => $this->l('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, default shop will be used'),
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
					'price_tex' => array('label' => $this->l('Price tax excl.')),
					'price_tin' => array('label' => $this->l('Price tax incl.')),
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
					'meta_title' => array('label' => $this->l('Meta-title')),
					'meta_keywords' => array('label' => $this->l('Meta-keywords')),
					'meta_description' => array('label' => $this->l('Meta-description')),
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
						'help' => $this->l('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, default shop will be used'),
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
					'email' => array('label' => $this->l('E-mail *')),
					'passwd' => array('label' => $this->l('Password *')),
					'birthday' => array('label' => $this->l('Birthday (yyyy-mm-dd)')),
					'lastname' => array('label' => $this->l('Lastname *')),
					'firstname' => array('label' => $this->l('Firstname *')),
					'newsletter' => array('label' => $this->l('Newsletter (0/1)')),
					'optin' => array('label' => $this->l('Opt-in (0/1)')),
					'id_shop' => array(
						'label' => $this->l('ID / Name of shop'),
						'help' => $this->l('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, default shop will be used'),
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
					'customer_email' => array('label' => $this->l('Customer e-mail')),
					'id_customer' => array('label' => $this->l('Customer ID')),
					'manufacturer' => array('label' => $this->l('Manufacturer')),
					'supplier' => array('label' => $this->l('Supplier')),
					'company' => array('label' => $this->l('Company')),
					'lastname' => array('label' => $this->l('Lastname *')),
					'firstname' => array('label' => $this->l('Firstname *')),
					'address1' => array('label' => $this->l('Address 1 *')),
					'address2' => array('label' => $this->l('Address 2')),
					'postcode' => array('label' => $this->l('Postcode*/ Zipcode*')),
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
					'meta_title' => array('label' => $this->l('Meta-title')),
					'meta_keywords' => array('label' => $this->l('Meta-keywords')),
					'meta_description' => array('label' => $this->l('Meta-description')),
					'shop' => array(
						'label' => $this->l('ID / Name of group shop'),
						'help' => $this->l('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, default shop will be used'),
					),
				);

				self::$default_values = array(
					'shop' => Shop::getGroupFromShop(Configuration::get('PS_SHOP_DEFAULT')),
				);
			break;
			// @since 1.5.0
			case $this->entities[$this->l('SupplyOrders')]:
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
			case $this->entities[$this->l('SupplyOrdersDetails')]:
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
			$this->displayWarning($this->l('directory import on admin directory must be writable (CHMOD 755 / 777)'));

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

		$this->tpl_form_vars = array(
			'module_confirmation' => (Tools::getValue('import')) && (isset($this->warnings) && !count($this->warnings)),
			'path_import' => _PS_ADMIN_DIR_.'/import/',
			'entities' => $this->entities,
			'entity' => Tools::getValue('entity'),
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
				$this->errors[] = $this->l('You must upload a file for go to the next step');
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

		if (is_null(Tools::getValue('multiple_value_separator')) || trim(Tools::getValue('multiple_value_separator')) == '')
			$separator = ',';
		else
			$separator = Tools::getValue('multiple_value_separator');

		$temp = tmpfile();
		fwrite($temp, $field);
		rewind($temp);
		$tab = fgetcsv($temp, MAX_LINE_SIZE, $separator);
		fclose($temp);
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
			if (!empty($infos))
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
		if (@copy($url, $tmpfile))
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
				$this->errors[] = Tools::displayError('The ID category cannot be the same as the ID Root category, nor the ID Home category');
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
				if (isset($cat_moved[$category->parent]))
					$category->parent = $cat_moved[$category->parent];
				$category->id_parent = $category->parent;
			}
			elseif (isset($category->parent) && is_string($category->parent))
			{
				$category_parent = Category::searchByName($default_language_id, $category->parent, true);
				if ($category_parent['id_category'])
				{
					$category->id_parent = (int)$category_parent['id_category'];
					$category->level_depth = (int)$category_parent['level_depth'] + 1;
				}
				else
				{
					$category_to_create = new Category();
					$category_to_create->name = AdminImportController::createMultiLangField($category->parent);
					$category_to_create->active = 1;
					$category_link_rewrite = Tools::link_rewrite($category_to_create->name[$default_language_id]);
					$category_to_create->link_rewrite = $category_link_rewrite;
					$category_to_create->id_parent = Configuration::get('PS_HOME_CATEGORY'); // Default parent is home for unknown category to create
					if (($field_error = $category_to_create->validateFields(UNFRIENDLY_ERROR, true)) === true &&
						($lang_field_error = $category_to_create->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $category_to_create->add())
						$category->id_parent = $category_to_create->id;
					else
					{
						$this->errors[] = sprintf(
							Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
							$category_to_create->name[$default_language_id],
							(isset($category_to_create->id) ? $category_to_create->id : 'null')
						);
						$this->errors[] = ($field_error !== true ? $field_error : '').($lang_field_error !== true ? $lang_field_error : '').
							Db::getInstance()->getMsgError();
					}
				}
			}
			if (isset($category->link_rewrite) && !empty($category->link_rewrite[$default_language_id]))
				$valid_link = Validate::isLinkRewrite($category->link_rewrite[$default_language_id]);
			else
				$valid_link = false;

			if (!Shop::isFeatureActive())
				$category->id_shop_default = 1;
			else
				$category->id_shop_default = (int)Context::getContext()->shop->id;

			$bak = $category->link_rewrite[$default_language_id];
			if ((isset($category->link_rewrite) && empty($category->link_rewrite[$default_language_id])) || !$valid_link)
			{
				$category->link_rewrite = Tools::link_rewrite($category->name[$default_language_id]);
				if ($category->link_rewrite == '')
				{
					$category->link_rewrite = 'friendly-url-autogeneration-failed';
					$this->warnings[] = sprintf(Tools::displayError('URL rewriting failed to auto-generate a friendly URL for: %s'), $category->name[$default_language_id]);
				}
				$category->link_rewrite = AdminImportController::createMultiLangField($category->link_rewrite);
			}

			if (!$valid_link)
				$this->warnings[] = sprintf(
					Tools::displayError('Rewrite link for %1$s (ID: %2$s) was re-written as %3$s.'),
					$bak,
					(isset($info['id']) ? $info['id'] : 'null'),
					$category->link_rewrite[$default_language_id]
				);
			$res = false;
			if (($field_error = $category->validateFields(UNFRIENDLY_ERROR, true)) === true &&
				($lang_field_error = $category->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && empty($this->errors))
			{
				$category_already_created = Category::searchByNameAndParentCategoryId(
					$default_language_id,
					$category->name[$default_language_id],
					$category->id_parent
				);

				// If category already in base, get id category back
				if ($category_already_created['id_category'])
				{
					$cat_moved[$category->id] = (int)$category_already_created['id_category'];
					$category->id =	(int)$category_already_created['id_category'];
				}

				/* No automatic nTree regeneration for import */
				$category->doNotRegenerateNTree = true;

				// If id category AND id category already in base, trying to update
				$categories_home_root = array(Configuration::get('PS_ROOT_CATEGORY'), Configuration::get('PS_HOME_CATEGORY'));
				if ($category->id && $category->categoryExists($category->id) && !in_array($category->id, $categories_home_root))
					$res = $category->update();
				if ($category->id == Configuration::get('PS_ROOT_CATEGORY'))
					$this->errors[] = Tools::displayError('Root category cannot be modified');
				// If no id_category or update failed
				if (!$res)
					$res = $category->add();
			}
			//copying images of categories
			if (isset($category->image) && !empty($category->image))
				if (!(AdminImportController::copyImg($category->id, null, $category->image, 'categories')))
					$this->warnings[] = $category->image.' '.Tools::displayError('cannot be copied');
			// If both failed, mysql error
			if (!$res)
			{
				$this->errors[] = sprintf(
					Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
					$info['name'],
					(isset($info['id']) ? $info['id'] : 'null')
				);
				$error_tmp = ($field_error !== true ? $field_error : '').($lang_field_error !== true ? $lang_field_error : '').Db::getInstance()->getMsgError();
				if ($error_tmp != '')
					$this->errors[] = $error_tmp;
			}
			else
			{
				// Associate category to shop
				if (Shop::isFeatureActive())
				{
					Db::getInstance()->execute('
						DELETE FROM '._DB_PREFIX_.'category_shop
						WHERE id_category = '.(int)$category->id
					);

					if (!Shop::isFeatureActive())
						$info['shop'] = 1;
					elseif (!isset($info['shop']) || empty($info['shop']))
						$info['shop'] = implode($this->multiple_value_separator, Shop::getContextListShopID());

					// Get shops for each attributes
					$info['shop'] = explode($this->multiple_value_separator, $info['shop']);

					foreach ($info['shop'] as $shop)
						if (!is_numeric($shop))
							$category->addShop(Shop::getIdByName($shop));
						else
							$category->addShop($shop);
				}
			}
		}

		/* Import has finished, we can regenerate the categories nested tree */
		Category::regenerateEntireNtree();

		$this->closeCsvFile($handle);
	}

	public function productImport()
	{
		$this->receiveTab();
		$handle = $this->openCsvFile();
		$default_language_id = (int)Configuration::get('PS_LANG_DEFAULT');
		AdminImportController::setLocale();
		for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator); $current_line++)
		{
			if (Tools::getValue('convert'))
				$line = $this->utf8EncodeArray($line);
			$info = AdminImportController::getMaskedRow($line);

			if (Tools::getValue('forceIDs') && isset($info['id']) && (int)$info['id'])
				$product = new Product((int)$info['id']);
			else
			{
				if (array_key_exists('id', $info) && (int)$info['id'] && Product::existsInDatabase((int)$info['id'], 'product'))
					$product = new Product((int)$info['id']);
				else
					$product = new Product();
			}

			if (array_key_exists('id', $info) && (int)$info['id'] && Product::existsInDatabase((int)$info['id'], 'product'))
			{
				$product->loadStockData();
				$category_data = Product::getProductCategories((int)$product->id);
				foreach ($category_data as $tmp)
					$product->category[] = $tmp;
			}

			AdminImportController::setEntityDefaultValues($product);
			AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $product);

			if (!Shop::isFeatureActive())
				$product->shop = 1;
			elseif (!isset($product->shop) || empty($product->shop))
				$product->shop = implode($this->multiple_value_separator, Shop::getContextListShopID());

			if (!Shop::isFeatureActive())
				$product->id_shop_default = 1;
			else
				$product->id_shop_default = (int)Context::getContext()->shop->id;

			// link product to shops
			$product->id_shop_list = array();
			foreach (explode($this->multiple_value_separator, $product->shop) as $shop)
				if (!is_numeric($shop))
					$product->id_shop_list[] = Shop::getIdByName($shop);
				else
					$product->id_shop_list[] = $shop;

			if ((int)$product->id_tax_rules_group != 0)
			{
				if (Validate::isLoadedObject(new TaxRulesGroup($product->id_tax_rules_group)))
				{
					$address = $this->context->shop->getAddress();
					$tax_manager = TaxManagerFactory::getManager($address, $product->id_tax_rules_group);
					$product_tax_calculator = $tax_manager->getTaxCalculator();
					$product->tax_rate = $product_tax_calculator->getTotalRate();
				}
				else
					$this->addProductWarning(
						'id_tax_rules_group',
						$product->id_tax_rules_group,
						Tools::displayError('Invalid tax rule group ID, you first need a group with this ID.')
					);
			}
			if (isset($product->manufacturer) && is_numeric($product->manufacturer) && Manufacturer::manufacturerExists((int)$product->manufacturer))
				$product->id_manufacturer = (int)$product->manufacturer;
			else if (isset($product->manufacturer) && is_string($product->manufacturer) && !empty($product->manufacturer))
			{
				if ($manufacturer = Manufacturer::getIdByName($product->manufacturer))
					$product->id_manufacturer = (int)$manufacturer;
				else
				{
					$manufacturer = new Manufacturer();
					$manufacturer->name = $product->manufacturer;
					if (($field_error = $manufacturer->validateFields(UNFRIENDLY_ERROR, true)) === true &&
						($lang_field_error = $manufacturer->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $manufacturer->add())
						$product->id_manufacturer = (int)$manufacturer->id;
					else
					{
						$this->errors[] = sprintf(
							Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
							$manufacturer->name,
							(isset($manufacturer->id) ? $manufacturer->id : 'null')
						);
						$this->errors[] = ($field_error !== true ? $field_error : '').($lang_field_error !== true ? $lang_field_error : '').
							Db::getInstance()->getMsgError();
					}
				}
			}

			if (isset($product->supplier) && is_numeric($product->supplier) && Supplier::supplierExists((int)$product->supplier))
				$product->id_supplier = (int)$product->supplier;
			else if (isset($product->supplier) && is_string($product->supplier) && !empty($product->supplier))
			{
				if ($supplier = Supplier::getIdByName($product->supplier))
					$product->id_supplier = (int)$supplier;
				else
				{
					$supplier = new Supplier();
					$supplier->name = $product->supplier;
					$supplier->active = true;

					if (($field_error = $supplier->validateFields(UNFRIENDLY_ERROR, true)) === true &&
						($lang_field_error = $supplier->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $supplier->add())
					{
						$product->id_supplier = (int)$supplier->id;
						$supplier->associateTo($product->id_shop_list);
					}
					else
					{
						$this->errors[] = sprintf(
							Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
							$supplier->name,
							(isset($supplier->id) ? $supplier->id : 'null')
						);
						$this->errors[] = ($field_error !== true ? $field_error : '').($lang_field_error !== true ? $lang_field_error : '').
							Db::getInstance()->getMsgError();
					}
				}
			}

			if (isset($product->price_tex) && !isset($product->price_tin))
				$product->price = $product->price_tex;
			else if (isset($product->price_tin) && !isset($product->price_tex))
			{
				$product->price = $product->price_tin;
				// If a tax is already included in price, withdraw it from price
				if ($product->tax_rate)
					$product->price = (float)number_format($product->price / (1 + $product->tax_rate / 100), 6, '.', '');
			}
			else if (isset($product->price_tin) && isset($product->price_tex))
				$product->price = $product->price_tex;

			if (isset($product->category) && is_array($product->category) && count($product->category))
			{
				$product->id_category = array(); // Reset default values array
				foreach ($product->category as $value)
				{
					if (is_numeric($value))
					{
						if (Category::categoryExists((int)$value))
							$product->id_category[] = (int)$value;
						else
						{
							$category_to_create = new Category();
							$category_to_create->id = (int)$value;
							$category_to_create->name = AdminImportController::createMultiLangField($value);
							$category_to_create->active = 1;
							$category_to_create->id_parent = Configuration::get('PS_HOME_CATEGORY'); // Default parent is home for unknown category to create
							$category_link_rewrite = Tools::link_rewrite($category_to_create->name[$default_language_id]);
							$category_to_create->link_rewrite = AdminImportController::createMultiLangField($category_link_rewrite);
							if (($field_error = $category_to_create->validateFields(UNFRIENDLY_ERROR, true)) === true &&
								($lang_field_error = $category_to_create->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $category_to_create->add())
								$product->id_category[] = (int)$category_to_create->id;
							else
							{
								$this->errors[] = sprintf(
									Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
									$category_to_create->name[$default_language_id],
									(isset($category_to_create->id) ? $category_to_create->id : 'null')
								);
								$this->errors[] = ($field_error !== true ? $field_error : '').($lang_field_error !== true ? $lang_field_error : '').
									Db::getInstance()->getMsgError();
							}
						}
					}
					else if (is_string($value) && !empty($value))
					{
						$category = Category::searchByName($default_language_id, trim($value), true);
						if ($category['id_category'])
							$product->id_category[] = (int)$category['id_category'];
						else
						{
							$category_to_create = new Category();
							if (!Shop::isFeatureActive())
								$category_to_create->id_shop_default = 1;
							else
								$category_to_create->id_shop_default = (int)Context::getContext()->shop->id;
							$category_to_create->name = AdminImportController::createMultiLangField(trim($value));
							$category_to_create->active = 1;
							$category_to_create->id_parent = (int)Configuration::get('PS_HOME_CATEGORY'); // Default parent is home for unknown category to create
							$category_link_rewrite = Tools::link_rewrite($category_to_create->name[$default_language_id]);
							$category_to_create->link_rewrite = AdminImportController::createMultiLangField($category_link_rewrite);
							if (($field_error = $category_to_create->validateFields(UNFRIENDLY_ERROR, true)) === true &&
								($lang_field_error = $category_to_create->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $category_to_create->add())
								$product->id_category[] = (int)$category_to_create->id;
							else
							{
								$this->errors[] = sprintf(
									Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
									$category_to_create->name[$default_language_id],
									(isset($category_to_create->id) ? $category_to_create->id : 'null')
								);
								$this->errors[] = ($field_error !== true ? $field_error : '').($lang_field_error !== true ? $lang_field_error : '').
									Db::getInstance()->getMsgError();
							}
						}
					}
				}
			}

			$product->id_category_default = isset($product->id_category[0]) ? (int)$product->id_category[0] : '';

			$link_rewrite = (is_array($product->link_rewrite) && count($product->link_rewrite)) ? trim($product->link_rewrite[$default_language_id]) : '';

			$valid_link = Validate::isLinkRewrite($link_rewrite);

			if ((isset($product->link_rewrite[$default_language_id]) && empty($product->link_rewrite[$default_language_id])) || !$valid_link)
			{
				$link_rewrite = Tools::link_rewrite($product->name[$default_language_id]);
				if ($link_rewrite == '')
					$link_rewrite = 'friendly-url-autogeneration-failed';
			}

			if (!$valid_link)
				$this->warnings[] = sprintf(
					Tools::displayError('Rewrite link for %1$s (ID: %2$s) was re-written as %3$s.'),
					$link_rewrite,
					(isset($info['id']) ? $info['id'] : 'null'),
					$link_rewrite
				);

			$product->link_rewrite = AdminImportController::createMultiLangField($link_rewrite);

			// replace the value of separator by coma
			if ($this->multiple_value_separator != ',')
				foreach ($product->meta_keywords as &$meta_keyword)
					if (!empty($meta_keyword))
						$meta_keyword = str_replace($this->multiple_value_separator, ',', $meta_keyword);

			$res = false;
			$field_error = $product->validateFields(UNFRIENDLY_ERROR, true);
			$lang_field_error = $product->validateFieldsLang(UNFRIENDLY_ERROR, true);
			if ($field_error === true && $lang_field_error === true)
			{
				// check quantity
				if ($product->quantity == null)
					$product->quantity = 0;

				// If match ref is specified && ref product && ref product already in base, trying to update
				if (Tools::getValue('match_ref') == 1 && $product->reference && $product->existsRefInDatabase($product->reference))
				{
					$datas = Db::getInstance()->getRow('
						SELECT product_shop.`date_add`, p.`id_product`
						FROM `'._DB_PREFIX_.'product` p
						'.Shop::addSqlAssociation('product', 'p').'
						WHERE p.`reference` = "'.$product->reference.'"
					');
					$product->id = (int)$datas['id_product'];
					$product->date_add = pSQL($datas['date_add']);
					$res = $product->update();
				} // Else If id product && id product already in base, trying to update
				else if ($product->id && Product::existsInDatabase((int)$product->id, 'product'))
				{
					$datas = Db::getInstance()->getRow('
						SELECT product_shop.`date_add`
						FROM `'._DB_PREFIX_.'product` p
						'.Shop::addSqlAssociation('product', 'p').'
						WHERE p.`id_product` = '.(int)$product->id);
					$product->date_add = pSQL($datas['date_add']);
					$res = $product->update();
				}
				// If no id_product or update failed
				if (!$res)
				{
					if (isset($product->date_add) && $product->date_add != '')
						$res = $product->add(false);
					else
						$res = $product->add();
				}
			}

			$shops = array();
			$product_shop = explode($this->multiple_value_separator, $product->shop);
			foreach ($product_shop as $shop)
			{
				$shop = trim($shop);
				if (!is_numeric($shop))
					$shop = ShopGroup::getIdByName($shop);
				$shops[] = $shop;
			}
			if (empty($shops))
				$shops = Shop::getContextListShopID();
			// If both failed, mysql error
			if (!$res)
			{
				$this->errors[] = sprintf(
					Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
					$info['name'],
					(isset($info['id']) ? $info['id'] : 'null')
				);
				$this->errors[] = ($field_error !== true ? $field_error : '').($lang_field_error !== true ? $lang_field_error : '').
					Db::getInstance()->getMsgError();

			}
			else
			{
				// Product supplier
				if (isset($product->id_supplier) && isset($product->supplier_reference))
				{
					$id_product_supplier = ProductSupplier::getIdByProductAndSupplier((int)$product->id, 0, (int)$product->id_supplier);
					if ($id_product_supplier)
						$product_supplier = new ProductSupplier((int)$id_product_supplier);
					else
						$product_supplier = new ProductSupplier();

					$product_supplier->id_product = $product->id;
					$product_supplier->id_product_attribute = 0;
					$product_supplier->id_supplier = $product->id_supplier;
					$product_supplier->product_supplier_price_te = $product->wholesale_price;
					$product_supplier->product_supplier_reference = $product->supplier_reference;
					$product_supplier->save();
				}

				// SpecificPrice (only the basic reduction feature is supported by the import)
				if ((isset($info['reduction_price']) && $info['reduction_price'] > 0) || (isset($info['reduction_percent']) && $info['reduction_percent'] > 0))
				{
					$specific_price = new SpecificPrice();
					$specific_price->id_product = (int)$product->id;
					// @todo multishop specific price import
					$specific_price->id_shop = $this->context->shop->id;
					$specific_price->id_currency = 0;
					$specific_price->id_country = 0;
					$specific_price->id_group = 0;
					$specific_price->price = -1;
					$specific_price->id_customer = 0;
					$specific_price->from_quantity = 1;
					$specific_price->reduction = (isset($info['reduction_price']) && $info['reduction_price']) ? $info['reduction_price'] : $info['reduction_percent'] / 100;
					$specific_price->reduction_type = (isset($info['reduction_price']) && $info['reduction_price']) ? 'amount' : 'percentage';
					$specific_price->from = (isset($info['reduction_from']) && Validate::isDate($info['reduction_from'])) ? $info['reduction_from'] : '0000-00-00 00:00:00';
					$specific_price->to = (isset($info['reduction_to']) && Validate::isDate($info['reduction_to']))  ? $info['reduction_to'] : '0000-00-00 00:00:00';
					if (!$specific_price->add())
						$this->addProductWarning($info['name'], $product->id, $this->l('Discount is invalid'));
				}

				if (isset($product->tags) && !empty($product->tags))
				{
					// Delete tags for this id product, for no duplicating error
					Tag::deleteTagsForProduct($product->id);

					if (!is_array($product->tags))
					{
						$product->tags = AdminImportController::createMultiLangField($product->tags);
						foreach ($product->tags as $key => $tags)
						{
							$is_tag_added = Tag::addTags($key, $product->id, $tags, $this->multiple_value_separator);
							if (!$is_tag_added)
							{
								$this->addProductWarning($info['name'], $product->id, $this->l('Tags list is invalid'));
								break;
							}
						}
					}
					else
					{
						foreach ($product->tags as $key => $tags)
						{
							$str = '';
							foreach ($tags as $one_tag)
								$str .= $one_tag.$this->multiple_value_separator;
							$str = rtrim($str, $this->multiple_value_separator);

							$is_tag_added = Tag::addTags($key, $product->id, $str, $this->multiple_value_separator);
							if (!$is_tag_added)
							{
								$this->addProductWarning($info['name'], $product->id, 'Invalid tag(s) ('.$str.')');
								break;
							}
						}
					}
				}
				//delete existing images if "delete_existing_images" is set to 1
				if (isset($product->delete_existing_images))
					if ((bool)$product->delete_existing_images)
						$product->deleteImages();
				else if (isset($product->image) && is_array($product->image) && count($product->image))
					$product->deleteImages();

				if (isset($product->image) && is_array($product->image) && count($product->image))
				{
					$product_has_images = (bool)Image::getImages($this->context->language->id, (int)$product->id);
					foreach ($product->image as $key => $url)
					{
						$url = trim($url);
						$error = false;
						if (!empty($url))
						{
							$url = str_replace(' ', '%20', $url);

							$image = new Image();
							$image->id_product = (int)$product->id;
							$image->position = Image::getHighestPosition($product->id) + 1;
							$image->cover = (!$key && !$product_has_images) ? true : false;
							// file_exists doesn't work with HTTP protocol
							if (@fopen($url, 'r') == false)
								$error = true;
							else if (($field_error = $image->validateFields(UNFRIENDLY_ERROR, true)) === true &&
								($lang_field_error = $image->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $image->add())
							{
								// associate image to selected shops
								$image->associateTo($shops);
								if (!AdminImportController::copyImg($product->id, $image->id, $url))
								{
									$image->delete();
									$this->warnings[] = sprintf(Tools::displayError('Error copying image: %s'), $url);
								}
							}
							else
								$error = true;
						}
						else
							$error = true;

						if ($error)
							$this->warnings[] = sprintf(Tools::displayError('Product n°%1$d: the picture cannot be saved: %2$s'), $image->id_product, $url);
					}
				}
				if (isset($product->id_category))
					$product->updateCategories(array_map('intval', $product->id_category));

				// Features import
				$features = get_object_vars($product);

				if (isset($features['features']) && !empty($features['features']))
					foreach (explode($this->multiple_value_separator, $features['features']) as $single_feature)
					{
						$tab_feature = explode(':', $single_feature);
						$feature_name = trim($tab_feature[0]);
						$feature_value = trim($tab_feature[1]);
						$position = isset($tab_feature[2]) ? $tab_feature[2]: false;
						$id_feature = Feature::addFeatureImport($feature_name, $position);
						$id_feature_value = FeatureValue::addFeatureValueImport($id_feature, $feature_value);
						Product::addFeatureProductImport($product->id, $id_feature, $id_feature_value);
					}
				// clean feature positions to avoid conflict
				Feature::cleanPositions();
			}

			// stock available
			if (Shop::isFeatureActive())
			{
				foreach ($shops as $shop)
					StockAvailable::setQuantity((int)$product->id, 0, $product->quantity, (int)$shop);
			}
			else
				StockAvailable::setQuantity((int)$product->id, 0, $product->quantity, $this->context->shop->id);

		}

		if (Configuration::get('PS_SEARCH_INDEXATION'))
			Search::indexation(true);

		$this->closeCsvFile($handle);
	}

	public function attributeImport()
	{
		$default_language = Configuration::get('PS_LANG_DEFAULT');

		$groups = array();
		foreach (AttributeGroup::getAttributesGroups($default_language) as $group)
			$groups[$group['name']] = (int)$group['id_attribute_group'];

		$attributes = array();
		foreach (Attribute::getAttributes($default_language) as $attribute)
			$attributes[$attribute['attribute_group'].'_'.$attribute['name']] = (int)$attribute['id_attribute'];

		$this->receiveTab();
		$handle = $this->openCsvFile();
		AdminImportController::setLocale();
		for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator); $current_line++)
		{
			if (count($line) == 1 && empty($line[0]))
				continue;

			if (Tools::getValue('convert'))
				$line = $this->utf8EncodeArray($line);
			$info = AdminImportController::getMaskedRow($line);
			$info = array_map('trim', $info);

			AdminImportController::setDefaultValues($info);

			if (!Shop::isFeatureActive())
				$info['shop'] = 1;
			elseif (!isset($info['shop']) || empty($info['shop']))
				$info['shop'] = implode($this->multiple_value_separator, Shop::getContextListShopID());

			// Get shops for each attributes
			$info['shop'] = explode($this->multiple_value_separator, $info['shop']);
				
			$id_shop_list = array();
			foreach ($info['shop'] as $shop)
				if (!is_numeric($shop))
					$id_shop_list[] = Shop::getIdByName($shop);
				else
					$id_shop_list[] = $shop;

			$product = new Product((int)$info['id_product'], false, $default_language);
			$id_image = null;

			//delete existing images if "delete_existing_images" is set to 1
			if (array_key_exists('delete_existing_images', $info) && $info['delete_existing_images'] && !isset($this->cache_image_deleted[(int)$product->id]))
			{
				$product->deleteImages();
				$this->cache_image_deleted[(int)$product->id] = true;
			}

			if (isset($info['image_url']) && $info['image_url'])
			{
				$product_has_images = (bool)Image::getImages($this->context->language->id, $product->id);

				$url = $info['image_url'];
				$image = new Image();
				$image->id_product = (int)$product->id;
				$image->position = Image::getHighestPosition($product->id) + 1;
				$image->cover = (!$product_has_images) ? true : false;

				$field_error = $image->validateFields(UNFRIENDLY_ERROR, true);
				$lang_field_error = $image->validateFieldsLang(UNFRIENDLY_ERROR, true);

				if ($field_error === true && $lang_field_error === true && $image->add())
				{
					$image->associateTo($id_shop_list);
					if (!AdminImportController::copyImg($product->id, $image->id, $url))
					{
						$this->warnings[] = sprintf(Tools::displayError('Error copying image: %s'), $url);
						$image->delete();
					}
					else
						$id_image = array($image->id);
				}
				else
				{
					$this->warnings[] = sprintf(
						Tools::displayError('%s cannot be saved'),
						(isset($image->id_product) ? ' ('.$image->id_product.')' : '')
					);
					$this->errors[] = ($field_error !== true ? $field_error : '').($lang_field_error !== true ? $lang_field_error : '').mysql_error();
				}
			}
			elseif (isset($info['image_position']) && $info['image_position'])
			{
				$images = $product->getImages($default_language);

				if ($images)
					foreach ($images as $row)
						if ($row['position'] == (int)$info['image_position'])
						{
							$id_image = array($row['id_image']);
							break;
						}
				if (!$id_image)
					$this->warnings[] = sprintf(
						Tools::displayError('No image found for combination with id_product = %s and image position = %s.'),
						$product->id,
						(int)$info['image_position']
					);
			}

			$id_attribute_group = 0;
			// groups
			$groups_attributes = array();
			foreach (explode($this->multiple_value_separator, $info['group']) as $key => $group)
			{
				$tab_group = explode(':', $group);
				$group = trim($tab_group[0]);
				if (!isset($tab_group[1]))
					$type = 'select';
				else
				$type = trim($tab_group[1]);

				// sets group
				$groups_attributes[$key]['group'] = $group;

				// if position is filled
				if (isset($tab_group[2]))
					$position = trim($tab_group[2]);
				else
					$position = false;

				if (!isset($groups[$group]))
				{
					$obj = new AttributeGroup();
					$obj->is_color_group = false;
					$obj->group_type = pSQL($type);
					$obj->name[$default_language] = $group;
					$obj->public_name[$default_language] = $group;
					$obj->position = (!$position) ? AttributeGroup::getHigherPosition() + 1 : $position;

					if (($field_error = $obj->validateFields(UNFRIENDLY_ERROR, true)) === true &&
						($lang_field_error = $obj->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true)
					{
						$obj->add();
						$obj->associateTo($id_shop_list);
						$groups[$group] = $obj->id;
					}
					else
						$this->errors[] = ($field_error !== true ? $field_error : '').($lang_field_error !== true ? $lang_field_error : '');

					// fils groups attributes
					$id_attribute_group = $obj->id;
					$groups_attributes[$key]['id'] = $id_attribute_group;
				}
				else // alreay exists
				{
					$id_attribute_group = $groups[$group];
					$groups_attributes[$key]['id'] = $id_attribute_group;
				}
			}

			// inits attribute
			$id_product_attribute = 0;
			$id_product_attribute_update = false;
			$attributes_to_add = array();

			// for each attribute
			foreach (explode($this->multiple_value_separator, $info['attribute']) as $key => $attribute)
			{
				$tab_attribute = explode(':', $attribute);
				$attribute = trim($tab_attribute[0]);
				// if position is filled
				if (isset($tab_attribute[1]))
					$position = trim($tab_attribute[1]);
				else
					$position = false;

				if (isset($groups_attributes[$key]))
				{
					$group = $groups_attributes[$key]['group'];
					if (!isset($attributes[$group.'_'.$attribute]) && count($groups_attributes[$key]) == 2)
					{
						$id_attribute_group = $groups_attributes[$key]['id'];
						$obj = new Attribute();
						// sets the proper id (corresponding to the right key)
						$obj->id_attribute_group = $groups_attributes[$key]['id'];
						$obj->name[$default_language] = str_replace('\n', '', str_replace('\r', '', $attribute));
						$obj->position = (!$position) ? Attribute::getHigherPosition($groups[$group]) + 1 : $position;

						if (($field_error = $obj->validateFields(UNFRIENDLY_ERROR, true)) === true &&
							($lang_field_error = $obj->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true)
						{
							$obj->add();
							$obj->associateTo($id_shop_list);
							$attributes[$group.'_'.$attribute] = $obj->id;
						}
						else
							$this->errors[] = ($field_error !== true ? $field_error : '').($lang_field_error !== true ? $lang_field_error : '');
					}

					$info['minimal_quantity'] = isset($info['minimal_quantity']) && $info['minimal_quantity'] ? (int)$info['minimal_quantity'] : 1;

					$info['wholesale_price'] = str_replace(',', '.', $info['wholesale_price']);
					$info['price'] = str_replace(',', '.', $info['price']);
					$info['ecotax'] = str_replace(',', '.', $info['ecotax']);
					$info['weight'] = str_replace(',', '.', $info['weight']);

					// if a reference is specified for this product, get the associate id_product_attribute to UPDATE
					if (isset($info['reference']) && !empty($info['reference']))
					{
						$id_product_attribute = Combination::getIdByReference($product->id, strval($info['reference']));

						// updates the attribute
						if ($id_product_attribute)
						{
							// gets all the combinations of this product
							$attribute_combinations = $product->getAttributeCombinations($default_language);
							foreach ($attribute_combinations as $attribute_combination)
							{
								if ($id_product_attribute && in_array($id_product_attribute, $attribute_combination))
								{
									$product->updateAttribute(
										$id_product_attribute,
										(float)$info['wholesale_price'],
										(float)$info['price'],
										(float)$info['weight'],
										0,
										(float)$info['ecotax'],
										$id_image,
										strval($info['reference']),
										strval($info['ean13']),
										(int)$info['default_on'],
										0,
										strval($info['upc']),
										(int)$info['minimal_quantity'],
										0,
										null,
										$id_shop_list
									);

									$id_product_attribute_update = true;
								}
							}
						}
					}

					// if no attribute reference is specified, creates a new one
					if (!$id_product_attribute)
					{
						$id_product_attribute = $product->addCombinationEntity(
							(float)$info['wholesale_price'],
							(float)$info['price'],
							(float)$info['weight'],
							0,
							(float)$info['ecotax'],
							(int)$info['quantity'],
							$id_image,
							strval($info['reference']),
							0,
							strval($info['ean13']),
							(int)$info['default_on'],
							0,
							strval($info['upc']),
							(int)$info['minimal_quantity'],
							$id_shop_list
						);
					}

					// fills our attributes array, in order to add the attributes to the product_attribute afterwards
					$attributes_to_add[] = (int)$attributes[$group.'_'.$attribute];

					// after insertion, we clean attribute position and group attribute position
					$obj = new Attribute();
					$obj->cleanPositions((int)$id_attribute_group, false);
					AttributeGroup::cleanPositions();
				}
			}

			$product->checkDefaultAttributes();
			if (!$product->cache_default_attribute)
						Product::updateDefaultAttribute($product->id);
			if ($id_product_attribute)
			{
				// now adds the attributes in the attribute_combination table
				if ($id_product_attribute_update)
				{
					Db::getInstance()->execute('
						DELETE FROM '._DB_PREFIX_.'product_attribute_combination
						WHERE id_product_attribute = '.(int)$id_product_attribute);
				}

				foreach ($attributes_to_add as $attribute_to_add)
				{
					Db::getInstance()->execute('
						INSERT IGNORE INTO '._DB_PREFIX_.'product_attribute_combination (id_attribute, id_product_attribute)
						VALUES ('.(int)$attribute_to_add.','.(int)$id_product_attribute.')');
				}

				StockAvailable::setQuantity($product->id, $id_product_attribute, (int)$info['quantity']);
			}
		}

		$this->closeCsvFile($handle);
	}

	public function customerImport()
	{
		$customer_exist = false;
		$this->receiveTab();
		$handle = $this->openCsvFile();
		AdminImportController::setLocale();
		for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator); $current_line++)
		{
			if (Tools::getValue('convert'))
				$line = $this->utf8EncodeArray($line);
			$info = AdminImportController::getMaskedRow($line);

			AdminImportController::setDefaultValues($info);

			if (Tools::getValue('forceIDs') && isset($info['id']) && (int)$info['id'])
				$customer = new Customer((int)$info['id']);
			else
			{
				if (array_key_exists('id', $info) && (int)$info['id'] && Customer::customerIdExistsStatic((int)$info['id']))
					$customer = new Customer((int)$info['id']);
				else
					$customer = new Customer();
			}

			if (array_key_exists('id', $info) && (int)$info['id'] && Customer::customerIdExistsStatic((int)$info['id']))
			{
				$current_id_customer = $customer->id;
				$current_id_shop = $customer->id_shop;
				$current_id_shop_group = $customer->id_shop_group;
				$customer_exist = true;
				$customer_groups = $customer->getGroups();
				$addresses = $customer->getAddresses((int)Configuration::get('PS_LANG_DEFAULT'));
				foreach ($customer_groups as $key => $group)
					if ($group == $customer->id_default_group)
						unset($customer_groups[$key]);
			}

			AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $customer);

			if ($customer->passwd)
				$customer->passwd = Tools::encrypt($customer->passwd);

			$id_shop_list = explode($this->multiple_value_separator, $customer->id_shop);
			$customers_shop = array();
			$customers_shop['shared'] = array();
			$default_shop = new Shop((int)Configuration::get('PS_SHOP_DEFAULT'));
			if (Shop::isFeatureActive() && $id_shop_list)
			{
				foreach ($id_shop_list as $id_shop)
				{
					$shop = new Shop((int)$id_shop);
					$group_shop = $shop->getGroup();
					if ($group_shop->share_customer)
					{
						if (!in_array($group_shop->id, $customers_shop['shared']))
							$customers_shop['shared'][(int)$id_shop] = $group_shop->id;
					}
					else
						$customers_shop[(int)$id_shop] = $group_shop->id;
				}
			}
			else
			{
				$default_shop = new Shop((int)Configuration::get('PS_SHOP_DEFAULT'));
				$default_shop->getGroup();
				$customers_shop[$default_shop->id] = $default_shop->getGroup()->id;
			}

			//set temporally for validate field
			$customer->id_shop = $default_shop->id;
			$customer->id_shop_group = $default_shop->getGroup()->id;

			$res = true;
			if (($field_error = $customer->validateFields(UNFRIENDLY_ERROR, true)) === true &&
				($lang_field_error = $customer->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true)
			{
				foreach ($customers_shop as $id_shop => $id_group)
				{
					if ($id_shop == 'shared')
					{
						foreach ($id_group as $key => $id)
						{
							$customer->id_shop = (int)$key;
							$customer->id_shop_group = (int)$id;
							if ($customer_exist && ($current_id_shop_group == $id || in_array($current_id_shop, ShopGroup::getShopsFromGroup($id))))
							{
								$customer->id = $current_id_customer;
								$res &= $customer->update();
							}

							else
							{
								$res &= $customer->add();
								if (isset($customer_groups))
									$customer->addGroups($customer_groups);
								if (isset($addresses))
									foreach ($addresses as $address)
									{
										$address['id_customer'] = $customer->id;
										unset($address['country'], $address['state'], $address['state_iso'], $address['id_address']	);
										Db::getInstance()->insert('address', $address);
									}
							}
						}
					}
					else
					{
						$customer->id_shop = $id_shop;
						$customer->id_shop_group = $id_group;
						if ($customer_exist && $id_shop == $current_id_shop)
						{
							$customer->id = $current_id_customer;
							$res &= $customer->update();
						}
						else
						{
							$res &= $customer->add();
							if (isset($customer_groups))
									$customer->addGroups($customer_groups);
							if (isset($addresses))
								foreach ($addresses as $address)
								{
									$address['id_customer'] = $customer->id;
									unset($address['country'], $address['state'], $address['state_iso'], $address['id_address']);
									Db::getInstance()->insert('address', $address);
								}
						}
					}
				}
			}
			$customer_exist = false;
			if (!$res)
			{
				$this->errors[] = sprintf(
					Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
					$info['email'],
					(isset($info['id']) ? $info['id'] : 'null')
				);
				$this->errors[] = ($field_error !== true ? $field_error : '').($lang_field_error !== true ? $lang_field_error : '').
					Db::getInstance()->getMsgError();
			}
		}
		$this->closeCsvFile($handle);
	}

	public function addressImport()
	{
		$this->receiveTab();
		$default_language_id = (int)Configuration::get('PS_LANG_DEFAULT');
		$handle = $this->openCsvFile();
		AdminImportController::setLocale();
		for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator); $current_line++)
		{
			if (Tools::getValue('convert'))
				$line = $this->utf8EncodeArray($line);
			$info = AdminImportController::getMaskedRow($line);

			AdminImportController::setDefaultValues($info);
			$address = new Address();
			AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $address);

			if (isset($address->country) && is_numeric($address->country))
			{
				if (Country::getNameById(Configuration::get('PS_LANG_DEFAULT'), (int)$address->country))
					$address->id_country = (int)$address->country;
			}
			else if (isset($address->country) && is_string($address->country) && !empty($address->country))
			{
				if ($id_country = Country::getIdByName(null, $address->country))
					$address->id_country = (int)$id_country;
				else
				{
					$country = new Country();
					$country->active = 1;
					$country->name = AdminImportController::createMultiLangField($address->country);
					$country->id_zone = 0; // Default zone for country to create
					$country->iso_code = strtoupper(substr($address->country, 0, 2)); // Default iso for country to create
					$country->contains_states = 0; // Default value for country to create
					$lang_field_error = $country->validateFieldsLang(UNFRIENDLY_ERROR, true);
					if (($field_error = $country->validateFields(UNFRIENDLY_ERROR, true)) === true &&
						($lang_field_error = $country->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $country->add())
						$address->id_country = (int)$country->id;
					else
					{
						$this->errors[] = sprintf(Tools::displayError('%s cannot be saved'), $country->name[$default_language_id]);
						$this->errors[] = ($field_error !== true ? $field_error : '').($lang_field_error !== true ? $lang_field_error : '').
							Db::getInstance()->getMsgError();
					}
				}
			}

			if (isset($address->state) && is_numeric($address->state))
			{
				if (State::getNameById((int)$address->state))
					$address->id_state = (int)$address->state;
			}
			else if (isset($address->state) && is_string($address->state) && !empty($address->state))
			{
				if ($id_state = State::getIdByName($address->state))
					$address->id_state = (int)$id_state;
				else
				{
					$state = new State();
					$state->active = 1;
					$state->name = $address->state;
					$state->id_country = isset($country->id) ? (int)$country->id : 0;
					$state->id_zone = 0; // Default zone for state to create
					$state->iso_code = strtoupper(substr($address->state, 0, 2)); // Default iso for state to create
					$state->tax_behavior = 0;
					if (($field_error = $state->validateFields(UNFRIENDLY_ERROR, true)) === true &&
						($lang_field_error = $state->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $state->add())
						$address->id_state = (int)$state->id;
					else
					{
						$this->errors[] = sprintf(Tools::displayError('%s cannot be saved'), $state->name);
						$this->errors[] = ($field_error !== true ? $field_error : '').($lang_field_error !== true ? $lang_field_error : '').
							Db::getInstance()->getMsgError();
					}
				}
			}

			if (isset($address->customer_email) && !empty($address->customer_email))
			{
				if (Validate::isEmail($address->customer_email))
				{
					// a customer could exists in different shop
					$customer_list = Customer::getCustomersByEmail($address->customer_email);

					if (count($customer_list) == 0)
						$this->errors[] = sprintf(
							Tools::displayError('%1$s does not exist in database %2$s (ID: %3$s) cannot be saved'),
							Db::getInstance()->getMsgError(),
							$address->customer_email,
							(isset($info['id']) ? $info['id'] : 'null')
						);
				}
				else
				{
					$this->errors[] = sprintf(Tools::displayError('"%s": Is not a valid e-mail address'), $address->customer_email);
					continue;
				}
			}
			elseif (isset($address->id_customer) && !empty($address->id_customer))
			{
				if (Customer::customerIdExistsStatic((int)$address->id_customer))
				{
					$customer = new Customer((int)$address->id_customer);

					// a customer could exists in different shop
					$customer_list = Customer::getCustomersByEmail($customer->email);

					if (count($customer_list) == 0)
						$this->errors[] = sprintf(
							Tools::displayError('%1$s does not exist in database %2$s (ID: %3$s) cannot be saved'),
							Db::getInstance()->getMsgError(),
							$customer->email,
							(int)$address->customer_id
						);
				}
				else
					$this->errors[] = sprintf(Tools::displayError('The customer ID n.%d does not exist in database (ID: %d) cannot be saved'), $address->customer_id);
			}
			else
			{
				$customer_list = array();
				$address->id_customer = 0;
			}

			if (isset($address->manufacturer) && is_numeric($address->manufacturer) && Manufacturer::manufacturerExists((int)$address->manufacturer))
				$address->id_manufacturer = (int)$address->manufacturer;
			else if (isset($address->manufacturer) && is_string($address->manufacturer) && !empty($address->manufacturer))
			{
				$manufacturer = new Manufacturer();
				$manufacturer->name = $address->manufacturer;
				if (($field_error = $manufacturer->validateFields(UNFRIENDLY_ERROR, true)) === true &&
					($lang_field_error = $manufacturer->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $manufacturer->add())
					$address->id_manufacturer = (int)$manufacturer->id;
				else
				{
					$this->errors[] = Db::getInstance()->getMsgError().' '.sprintf(
						Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
						$manufacturer->name,
						(isset($manufacturer->id) ? $manufacturer->id : 'null')
					);
					$this->errors[] = ($field_error !== true ? $field_error : '').($lang_field_error !== true ? $lang_field_error : '').
						Db::getInstance()->getMsgError();
				}
			}

			if (isset($address->supplier) && is_numeric($address->supplier) && Supplier::supplierExists((int)$address->supplier))
				$address->id_supplier = (int)$address->supplier;
			else if (isset($address->supplier) && is_string($address->supplier) && !empty($address->supplier))
			{
				$supplier = new Supplier();
				$supplier->name = $address->supplier;
				if (($field_error = $supplier->validateFields(UNFRIENDLY_ERROR, true)) === true &&
					($lang_field_error = $supplier->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $supplier->add())
					$address->id_supplier = (int)$supplier->id;
				else
				{
					$this->errors[] = Db::getInstance()->getMsgError().' '.sprintf(
						Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
						$supplier->name,
						(isset($supplier->id) ? $supplier->id : 'null')
					);
					$this->errors[] = ($field_error !== true ? $field_error : '').($lang_field_error !== true ? $lang_field_error : '').
						Db::getInstance()->getMsgError();
				}
			}

			$res = false;
			if (($field_error = $address->validateFields(UNFRIENDLY_ERROR, true)) === true &&
				($lang_field_error = $address->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true)
			{
				if (isset($customer_list) && count($customer_list) > 0)
				{
					$filter_list = array();
					foreach ($customer_list as $customer)
					{
						if (in_array($customer['id_customer'], $filter_list))
							continue;

						$filter_list[] = $customer['id_customer'];

						unset($address->id);
						$address->id_customer = $customer['id_customer'];
						$res = $address->add();

						if (!$res)
							$this->errors[] = sprintf(
								Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
								$info['alias'],
								(isset($info['id']) ? $info['id'] : 'null')
							);
					}
				}
				else
				{
					if ($address->id && $address->addressExists($address->id))
						$res = $address->update();
					if (!$res)
						$res = $address->add();
				}
			}
			if (!$res)
			{
				$this->errors[] = sprintf(
					Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
					$info['alias'],
					(isset($info['id']) ? $info['id'] : 'null')
				);
				$this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
					Db::getInstance()->getMsgError();
			}
		}
		$this->closeCsvFile($handle);
	}

	public function manufacturerImport()
	{
		$this->receiveTab();
		$handle = $this->openCsvFile();
		AdminImportController::setLocale();
		for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator); $current_line++)
		{
			if (Tools::getValue('convert'))
				$line = $this->utf8EncodeArray($line);
			$info = AdminImportController::getMaskedRow($line);

			AdminImportController::setDefaultValues($info);

			if (Tools::getValue('forceIDs') && isset($info['id']) && (int)$info['id'])
				$manufacturer = new Manufacturer((int)$info['id']);
			else
			{
				if (array_key_exists('id', $info) && (int)$info['id'] && Manufacturer::existsInDatabase((int)$info['id'], 'manufacturer'))
					$manufacturer = new Manufacturer((int)$info['id']);
				else
					$manufacturer = new Manufacturer();
			}

			AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $manufacturer);

			$res = false;
			if (($field_error = $manufacturer->validateFields(UNFRIENDLY_ERROR, true)) === true &&
				($lang_field_error = $manufacturer->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true)
			{
				if ($manufacturer->id && $manufacturer->manufacturerExists($manufacturer->id))
					$res = $manufacturer->update();
				if (!$res)
					$res = $manufacturer->add();

				if ($res)
				{
					// Associate supplier to group shop
					if (Shop::isFeatureActive() && $manufacturer->shop)
					{
						Db::getInstance()->execute('
							DELETE FROM '._DB_PREFIX_.'manufacturer_shop
							WHERE id_manufacturer = '.(int)$manufacturer->id
						);
						$manufacturer->shop = explode($this->multiple_value_separator, $manufacturer->shop);
						$shops = array();
						foreach ($manufacturer->shop as $shop)
						{
							$shop = trim($shop);
							if (!is_numeric($shop))
								$shop = ShopGroup::getIdByName($shop);
							$shops[] = $shop;
						}
						$manufacturer->associateTo($shops);
					}
				}
			}

			if (!$res)
			{
				$this->errors[] = Db::getInstance()->getMsgError().' '.sprintf(
					Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
					$info['name'],
					(isset($info['id']) ? $info['id'] : 'null')
				);
				$this->errors[] = ($field_error !== true ? $field_error : '').($lang_field_error !== true ? $lang_field_error : '').
					Db::getInstance()->getMsgError();
			}
		}
		$this->closeCsvFile($handle);
	}

	public function supplierImport()
	{
		$this->receiveTab();
		$handle = $this->openCsvFile();
		AdminImportController::setLocale();
		for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator); $current_line++)
		{
			if (Tools::getValue('convert'))
				$line = $this->utf8EncodeArray($line);
			$info = AdminImportController::getMaskedRow($line);

			AdminImportController::setDefaultValues($info);

			if (Tools::getValue('forceIDs') && isset($info['id']) && (int)$info['id'])
				$supplier = new Supplier((int)$info['id']);
			else
			{
				if (array_key_exists('id', $info) && (int)$info['id'] && Supplier::existsInDatabase((int)$info['id'], 'supplier'))
					$supplier = new Supplier((int)$info['id']);
				else
					$supplier = new Supplier();
			}


			AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $supplier);
			if (($field_error = $supplier->validateFields(UNFRIENDLY_ERROR, true)) === true &&
				($lang_field_error = $supplier->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true)
			{
				$res = false;
				if ($supplier->id && $supplier->supplierExists($supplier->id))
					$res = $supplier->update();
				if (!$res)
					$res = $supplier->add();

				if (!$res)
					$this->errors[] = Db::getInstance()->getMsgError().' '.sprintf(
						Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
						$info['name'],
						(isset($info['id']) ? $info['id'] : 'null')
					);
				else
				{
					// Associate supplier to group shop
					if (Shop::isFeatureActive() && $supplier->shop)
					{
						Db::getInstance()->execute('
							DELETE FROM '._DB_PREFIX_.'supplier_shop
							WHERE id_supplier = '.(int)$supplier->id
						);
						$supplier->shop = explode($this->multiple_value_separator, $supplier->shop);
						$shops = array();
						foreach ($supplier->shop as $shop)
						{
							$shop = trim($shop);
							if (!is_numeric($shop))
								$shop = ShopGroup::getIdByName($shop);
							$shops[] = $shop;
						}
						$supplier->associateTo($shops);
					}
				}
			}
			else
			{
				$this->errors[] = $this->l('Supplier is invalid').' ('.$supplier->name.')';
				$this->errors[] = ($field_error !== true ? $field_error : '').($lang_field_error !== true ? $lang_field_error : '');
			}
		}
		$this->closeCsvFile($handle);
	}

	/**
	 * @since 1.5.0
	 */
	public function supplyOrdersImport()
	{
		// opens CSV & sets locale
		$this->receiveTab();
		$handle = $this->openCsvFile();
		AdminImportController::setLocale();

		// main loop, for each supply orders to import
		for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator); ++$current_line)
		{
			// if convert requested
			if (Tools::getValue('convert'))
				$line = $this->utf8EncodeArray($line);
			$info = AdminImportController::getMaskedRow($line);

			// sets default values if needed
			AdminImportController::setDefaultValues($info);

			// if an id is set, instanciates a supply order with this id if possible
			if (array_key_exists('id', $info) && (int)$info['id'] && SupplyOrder::exists((int)$info['id']))
				$supply_order = new SupplyOrder((int)$info['id']);
			// if a reference is set, instanciates a supply order with this reference if possible
			else if (array_key_exists('reference', $info) && $info['reference'] && SupplyOrder::exists(pSQL($info['reference'])))
				$supply_order = SupplyOrder::getSupplyOrderByReference(pSQL($info['reference']));
			else // new supply order
				$supply_order = new SupplyOrder();

			// gets parameters
			$id_supplier = (int)$info['id_supplier'];
			$id_lang = (int)$info['id_lang'];
			$id_warehouse = (int)$info['id_warehouse'];
			$id_currency = (int)$info['id_currency'];
			$reference = pSQL($info['reference']);
			$date_delivery_expected = pSQL($info['date_delivery_expected']);
			$discount_rate = (float)$info['discount_rate'];
			$is_template = (bool)$info['is_template'];

			// checks parameters
			if (!Supplier::supplierExists($id_supplier))
				$this->errors[] = sprintf($this->l('Supplier ID (%d) is not valid (at line %d).'), $id_supplier, $current_line + 1);
			if (!Language::getLanguage($id_lang))
				$this->errors[] = sprintf($this->l('Lang ID (%d) is not valid (at line %d).'), $id_lang, $current_line + 1);
			if (!Warehouse::exists($id_warehouse))
				$this->errors[] = sprintf($this->l('Warehouse ID (%d) is not valid (at line %d).'), $id_warehouse, $current_line + 1);
			if (!Currency::getCurrency($id_currency))
				$this->errors[] = sprintf($this->l('Currency ID (%d) is not valid (at line %d).'), $id_currency, $current_line + 1);
			if (empty($supply_order->reference) && SupplyOrder::exists($reference))
				$this->errors[] = sprintf($this->l('Reference (%s) already exists (at line %d).'), $reference, $current_line + 1);
			if (!empty($supply_order->reference) && ($supply_order->reference != $reference && SupplyOrder::exists($reference)))
				$this->errors[] = sprintf($this->l('Reference (%s) already exists (at line %d).'), $reference, $current_line + 1);
			if (!Validate::isDateFormat($date_delivery_expected))
				$this->errors[] = sprintf($this->l('Date (%s) is not valid (at line %d). Format: %s.'), $date_delivery_expected,
										   $current_line + 1, $this->l('YYYY-MM-DD'));
			else if (new DateTime($date_delivery_expected) <= new DateTime('yesterday'))
				$this->errors[] = sprintf($this->l('Date (%s) cannot be in the past (at line %d). Format: %s.'), $date_delivery_expected,
										   $current_line + 1, $this->l('YYYY-MM-DD'));
			if ($discount_rate < 0 || $discount_rate > 100)
				$this->errors[] = sprintf($this->l('Discount rate (%d) is not valid (at line %d). %s.'), $discount_rate,
										   $current_line + 1, $this->l('Format: between 0 and 100'));
			if ($supply_order->id > 0 && !$supply_order->isEditable())
				$this->errors[] = sprintf($this->l('Supply Order (%d) is not editable (at line %d).'), $supply_order->id, $current_line + 1);

			// if no errors, sets supply order
			if (empty($this->errors))
			{
				// adds parameters
				$info['id_ref_currency'] = (int)Currency::getDefaultCurrency()->id;
				$info['supplier_name'] = pSQL(Supplier::getNameById($id_supplier));
				if ($supply_order->id > 0)
				{
					$info['id_supply_order_state'] = (int)$supply_order->id_supply_order_state;
					$info['id'] = (int)$supply_order->id;
				}
				else
					$info['id_supply_order_state'] = 1;

				// sets parameters
				AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $supply_order);

				// updatesd($supply_order);
				$res = true;
				if ($supply_order->id > 0)
					$res &= $supply_order->update();
				else
					$res &= $supply_order->add();

				// errors
				if (!$res)
					$this->errors[] = sprintf($this->l('Supply Order could not be saved (at line %d).'), $current_line + 1);
			}
		}

		// closes
		$this->closeCsvFile($handle);
	}

	public function supplyOrdersDetailsImport()
	{
		// opens CSV & sets locale
		$this->receiveTab();
		$handle = $this->openCsvFile();
		AdminImportController::setLocale();

		$products = array();
		$reset = true;
		// main loop, for each supply orders details to import
		for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator); ++$current_line)
		{
			// if convert requested
			if (Tools::getValue('convert'))
				$line = $this->utf8EncodeArray($line);
			$info = AdminImportController::getMaskedRow($line);

			// sets default values if needed
			AdminImportController::setDefaultValues($info);

			// gets the supply order
			if (array_key_exists('supply_order_reference', $info) && pSQL($info['supply_order_reference']) && SupplyOrder::exists(pSQL($info['supply_order_reference'])))
				$supply_order = SupplyOrder::getSupplyOrderByReference(pSQL($info['supply_order_reference']));
			else
				$this->errors[] = sprintf($this->l('Supply Order (%s) could not be loaded (at line %d).'), (int)$info['supply_order_reference'], $current_line + 1);

			if (empty($this->errors))
			{
				// sets parameters
				$id_product = (int)$info['id_product'];
				if (!$info['id_product_attribute'])
					$info['id_product_attribute'] = 0;
				$id_product_attribute = (int)$info['id_product_attribute'];
				$unit_price_te = (float)$info['unit_price_te'];
				$quantity_expected = (int)$info['quantity_expected'];
				$discount_rate = (float)$info['discount_rate'];
				$tax_rate = (float)$info['tax_rate'];

				// checks if one product is there only once
				if (isset($product['id_product']))
				{
					if ($product['id_product'] == $id_product_attribute)
						$this->errors[] = sprintf($this->l('Product (%d/%D) cannot be added twice (at line %d).'), $id_product,
							$id_product_attribute, $current_line + 1);
					else
						$product['id_product'] = $id_product_attribute;
				}
				else
					$product['id_product'] = 0;

				// checks parameters
				if (false === ($supplier_reference = ProductSupplier::getProductSupplierReference($id_product, $id_product_attribute, $supply_order->id_supplier)))
					$this->errors[] = sprintf($this->l('Product (%d/%d) is not available for this order (at line %d).'), $id_product,
						$id_product_attribute, $current_line + 1);
				if ($unit_price_te < 0)
					$this->errors[] = sprintf($this->l('Unit Price (tax excl.) (%d) is not valid (at line %d).'), $unit_price_te, $current_line + 1);
				if ($quantity_expected < 0)
					$this->errors[] = sprintf($this->l('Quantity Expected (%d) is not valid (at line %d).'), $quantity_expected, $current_line + 1);
				if ($discount_rate < 0 || $discount_rate > 100)
				$this->errors[] = sprintf($this->l('Discount rate (%d) is not valid (at line %d). %s.'), $discount_rate,
										   $current_line + 1, $this->l('Format: between 0 and 100'));
				if ($tax_rate < 0 || $tax_rate > 100)
				$this->errors[] = sprintf($this->l('Quantity Expected (%d) is not valid (at line %d).'), $tax_rate,
										   $current_line + 1, $this->l('Format: between 0 and 100'));

				// if no errors, sets supply order details
				if (empty($this->errors))
				{
					// resets order if needed
					if ($reset)
					{
						$supply_order->resetProducts();
						$reset = false;
					}

					// creates new product
					$supply_order_detail = new SupplyOrderDetail();
					AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $supply_order_detail);

					// sets parameters
					$supply_order_detail->id_supply_order = $supply_order->id;
					$currency = new Currency($supply_order->id_ref_currency);
					$supply_order_detail->id_currency = $currency->id;
					$supply_order_detail->exchange_rate = $currency->conversion_rate;
					$supply_order_detail->supplier_reference = $supplier_reference;
					$supply_order_detail->name = Product::getProductName($id_product, $id_product_attribute, $supply_order->id_lang);

					// gets ean13 / ref / upc
					$query = new DbQuery();
					$query->select('
						IFNULL(pa.reference, IFNULL(p.reference, \'\')) as reference,
						IFNULL(pa.ean13, IFNULL(p.ean13, \'\')) as ean13,
						IFNULL(pa.upc, IFNULL(p.upc, \'\')) as upc
					');
					$query->from('product', 'p');
					$query->leftJoin('product_attribute', 'pa', 'pa.id_product = p.id_product AND id_product_attribute = '.(int)$id_product_attribute);
					$query->where('p.id_product = '.(int)$id_product);
					$query->where('p.is_virtual = 0 AND p.cache_is_pack = 0');
					$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
					$product_infos = $res['0'];

					$supply_order_detail->reference = $product_infos['reference'];
					$supply_order_detail->ean13 = $product_infos['ean13'];
					$supply_order_detail->upc = $product_infos['upc'];

					$supply_order_detail->add();
					$supply_order->update();
					unset($supply_order_detail);

				}
			}
		}

		// closes
		$this->closeCsvFile($handle);
	}

	public function utf8EncodeArray($array)
	{
		if (is_array($array))
			foreach ($array as $key => $value)
				$array[$key] = utf8_encode($value);
		else
			$array = utf8_encode($array);

		return $array;
	}

	protected function getNbrColumn($handle, $glue)
	{
		$tmp = fgetcsv($handle, MAX_LINE_SIZE, $glue);
		AdminImportController::rewindBomAware($handle);
		return count($tmp);
	}

	protected static function usortFiles($a, $b)
	{
		$a = strrev(substr(strrev($a), 0, 14));
		$b = strrev(substr(strrev($b), 0, 14));

		if ($a == $b)
			return 0;

		return ($a < $b) ? 1 : -1;
	}

	protected function openCsvFile()
	{
		 $handle = fopen(_PS_ADMIN_DIR_.'/import/'.strval(preg_replace('/\.{2,}/', '.', Tools::getValue('csv'))), 'r');

		if (!$handle)
			$this->errors[] = Tools::displayError('Cannot read the .CSV file');

		AdminImportController::rewindBomAware($handle);

		for ($i = 0; $i < (int)Tools::getValue('skip'); ++$i)
			$line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator);
		return $handle;
	}

	protected function closeCsvFile($handle)
	{
		fclose($handle);
	}

	protected function truncateTables($case)
	{
		switch ((int)$case)
		{
			case $this->entities[$this->l('Categories')]:
				Db::getInstance()->execute('
					DELETE FROM `'._DB_PREFIX_.'category`
					WHERE id_category NOT IN ('.(int)Configuration::get('PS_HOME_CATEGORY').
					', '.(int)Configuration::get('PS_ROOT_CATEGORY').')');
				Db::getInstance()->execute('
					DELETE FROM `'._DB_PREFIX_.'category_lang`
					WHERE id_category NOT IN ('.(int)Configuration::get('PS_HOME_CATEGORY').
					', '.(int)Configuration::get('PS_ROOT_CATEGORY').')');
				Db::getInstance()->execute('
					DELETE FROM `'._DB_PREFIX_.'category_shop`
					WHERE `id_category` NOT IN ('.(int)Configuration::get('PS_HOME_CATEGORY').
					', '.(int)Configuration::get('PS_ROOT_CATEGORY').')');
				Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'category` AUTO_INCREMENT = 3');
				foreach (scandir(_PS_CAT_IMG_DIR_) as $d)
					if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d))
						unlink(_PS_CAT_IMG_DIR_.$d);
				break;
			case $this->entities[$this->l('Products')]:
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_shop');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'feature_product');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_lang');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'category_product');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_tag');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'image');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'image_lang');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'image_shop');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'specific_price');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'specific_price_priority');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_carrier');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'cart_product');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'compare_product');
				if (count(Db::getInstance()->executeS('SHOW TABLES LIKE \''._DB_PREFIX_.'favorite_product\' '))) //check if table exist
					Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'favorite_product');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attachment');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_country_tax');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_download');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_group_reduction_cache');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_sale');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_supplier');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'scene_products');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'warehouse_product_location');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'stock');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'stock_available');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'stock_mvt');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'customization');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'customization_field');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'supply_order_detail');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_impact');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute`');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_shop`');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_combination`');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_image`');
				Image::deleteAllImages(_PS_PROD_IMG_DIR_);
				if (!file_exists(_PS_PROD_IMG_DIR_))
					mkdir(_PS_PROD_IMG_DIR_);
				break;
			case $this->entities[$this->l('Combinations')]:
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute`');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_impact');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_lang`');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_group`');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_group_lang`');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_group_shop`');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_shop`');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute`');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_shop`');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_combination`');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_image`');
				Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'stock_available` WHERE id_product_attribute=0');
				break;
			case $this->entities[$this->l('Customers')]:
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'customer');
				break;
			case $this->entities[$this->l('Addresses')]:
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'address');
				break;
			case $this->entities[$this->l('Manufacturers')]:
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'manufacturer');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'manufacturer_lang');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'manufacturer_shop');
				foreach (scandir(_PS_MANU_IMG_DIR_) as $d)
					if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d))
						unlink(_PS_MANU_IMG_DIR_.$d);
				break;
			case $this->entities[$this->l('Suppliers')]:
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'supplier');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'supplier_lang');
				Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'supplier_shop');
				foreach (scandir(_PS_SUPP_IMG_DIR_) as $d)
					if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d))
						unlink(_PS_SUPP_IMG_DIR_.$d);
				break;
		}
		Image::clearTmpDir();
		return true;
	}

	public function postProcess()
	{
		/* PrestaShop demo mode */
		if (_PS_MODE_DEMO_)
		{
			$this->errors[] = Tools::displayError('This functionality has been disabled.');
			return;
		}
		/* PrestaShop demo mode*/

		if (Tools::isSubmit('submitFileUpload'))
		{
			if (isset($_FILES['file']) && !empty($_FILES['file']['error']))
			{
				switch ($_FILES['file']['error'])
				{
					case UPLOAD_ERR_INI_SIZE:
						$this->errors[] = Tools::displayError('The uploaded file exceeds the upload_max_filesize directive in php.ini. If your server configuration allows it, you may add a directive in your .htaccess.');
						break;
					case UPLOAD_ERR_FORM_SIZE:
						$this->errors[] = Tools::displayError('The uploaded file exceeds the post_max_size directive in php.ini.
							If your server configuration allows it, you may add a directive in your .htaccess, for example:')
						.'<br/><a href="'.$this->context->link->getAdminLink('AdminMeta').'" >
						<code>php_value post_max_size 20M</code> '.
						Tools::displayError('(click to open "Generators" page)').'</a>';
						break;
					break;
					case UPLOAD_ERR_PARTIAL:
						$this->errors[] = Tools::displayError('The uploaded file was only partially uploaded.');
						break;
					break;
					case UPLOAD_ERR_NO_FILE:
						$this->errors[] = Tools::displayError('No file was uploaded');
						break;
					break;
				}
			}
			else if (!file_exists($_FILES['file']['tmp_name']) ||
				!@move_uploaded_file($_FILES['file']['tmp_name'], _PS_ADMIN_DIR_.'/import/'.date('Ymdhis').'-'.$_FILES['file']['name']))
				$this->errors[] = $this->l('an error occurred while uploading and copying file');
			else
				Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getValue('token').'&conf=18');
		}
		else if (Tools::getValue('import'))
		{
			// Check if the CSV file exist
			if (Tools::getValue('csv'))
			{
				// If i am a superadmin, i can truncate table
				if (((Shop::isFeatureActive() && $this->context->employee->isSuperAdmin()) || !Shop::isFeatureActive()) && Tools::getValue('truncate'))
					$this->truncateTables((int)Tools::getValue('entity'));

				switch ((int)Tools::getValue('entity'))
				{
					case $this->entities[$this->l('Categories')]:
						$this->categoryImport();
						break;
					case $this->entities[$this->l('Products')]:
						$this->productImport();
						break;
					case $this->entities[$this->l('Customers')]:
						$this->customerImport();
						break;
					case $this->entities[$this->l('Addresses')]:
						$this->addressImport();
						break;
					case $this->entities[$this->l('Combinations')]:
						$this->attributeImport();
						break;
					case $this->entities[$this->l('Manufacturers')]:
						$this->manufacturerImport();
						break;
					case $this->entities[$this->l('Suppliers')]:
						$this->supplierImport();
						break;
					// @since 1.5.0
					case $this->entities[$this->l('SupplyOrders')]:
						if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'))
							$this->supplyOrdersImport();
						break;
					// @since 1.5.0
					case $this->entities[$this->l('SupplyOrdersDetails')]:
						if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'))
							$this->supplyOrdersDetailsImport();
						break;
					default:
						$this->errors[] = $this->l('Please select what you would like to import');
				}
			}
			else
				$this->errors[] = $this->l('You must upload a file for go to the next step');
		}

		parent::postProcess();
	}

	public static function setLocale()
	{
		$iso_lang  = trim(Tools::getValue('iso_lang'));
		setlocale(LC_COLLATE, strtolower($iso_lang).'_'.strtoupper($iso_lang).'.UTF-8');
		setlocale(LC_CTYPE, strtolower($iso_lang).'_'.strtoupper($iso_lang).'.UTF-8');
	}

	protected function addProductWarning($product_name, $product_id = null, $message = '')
	{
		$this->warnings[] = $product_name.(isset($product_id) ? ' (ID '.$product_id.')' : '').' '.Tools::displayError($message);
	}

	public function ajaxProcessSaveImportMatchs()
	{
		if ($this->tabAccess['edit'] === '1')
		{
			$match = implode('|', Tools::getValue('type_value'));
			Db::getInstance()->execute('INSERT INTO  `'._DB_PREFIX_.'import_match` (
										`id_import_match` ,
										`name` ,
										`match`,
										`skip`
										)
										VALUES (
										NULL ,
										\''.pSQL(Tools::getValue('newImportMatchs')).'\',
										\''.pSQL($match).'\',
										\''.pSQL(Tools::getValue('skip')).'\'
										)');

			die('{"id" : "'.Db::getInstance()->Insert_ID().'"}');
		}
	}

	public function ajaxProcessLoadImportMatchs()
	{
		if ($this->tabAccess['edit'] === '1')
		{
			$return = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'import_match` WHERE `id_import_match` = '.(int)Tools::getValue('idImportMatchs'));
			die('{"id" : "'.$return[0]['id_import_match'].'", "matchs" : "'.$return[0]['match'].'", "skip" : "'.$return[0]['skip'].'"}');
		}
	}

	public function ajaxProcessDeleteImportMatchs()
	{
		if ($this->tabAccess['edit'] === '1')
		{
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'import_match` WHERE `id_import_match` = '.(int)Tools::getValue('idImportMatchs'));
			die;
		}
	}
}
