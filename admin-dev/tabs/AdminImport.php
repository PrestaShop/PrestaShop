<?php
/*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include_once(PS_ADMIN_DIR.'/../images.inc.php');
@ini_set('max_execution_time', 0);
define('MAX_LINE_SIZE', 4096);

define('UNFRIENDLY_ERROR', false); // Used for validatefields diying without user friendly error or not

// this value set the number of columns visible on each page
define('MAX_COLUMNS', 6);
// correct Mac error on eof
@ini_set('auto_detect_line_endings', '1');

class AdminImport extends AdminTab
{
	public static $column_mask;

	public $entities = array();

	public $available_fields = array();

	public static $required_fields = array('name');

	public static $default_values = array();

	public $_warnings = array();

	public static $validators = array(
		'active' => array('AdminImport', 'getBoolean'),
		'tax_rate' => array('AdminImport', 'getPrice'),
		'price_tex' => array('AdminImport', 'getPrice'), // Tax excluded
		'price_tin' => array('AdminImport', 'getPrice'), // Tax included
		'reduction_price' => array('AdminImport', 'getPrice'),
		'reduction_percent' => array('AdminImport', 'getPrice'),
		'wholesale_price' => array('AdminImport', 'getPrice'),
		'ecotax' => array('AdminImport', 'getPrice'),
		'name' => array('AdminImport', 'createMultiLangField'),
		'description' => array('AdminImport', 'createMultiLangField'),
		'description_short' => array('AdminImport', 'createMultiLangField'),
		'meta_title' => array('AdminImport', 'createMultiLangField'),
		'meta_keywords' => array('AdminImport', 'createMultiLangField'),
		'meta_description' => array('AdminImport', 'createMultiLangField'),
		'link_rewrite' => array('AdminImport', 'createMultiLangField'),
		'available_now' => array('AdminImport', 'createMultiLangField'),
		'available_later' => array('AdminImport', 'createMultiLangField'),
		'category' => array('AdminImport', 'split'),
		'online_only' => array('AdminImport', 'getBoolean')
		);

	public function __construct()
	{
		$this->entities = array_flip(array($this->l('Categories'), $this->l('Products'), $this->l('Combinations'), $this->l('Customers'), $this->l('Addresses'), $this->l('Manufacturers'), $this->l('Suppliers')));

		switch ((int)(Tools::getValue('entity')))
		{
			case $this->entities[$this->l('Combinations')]:

				self::$required_fields = array('id_product', 'options');
				$this->available_fields = array(
					'no' => $this->l('Ignore this column'),
					'id_product' => $this->l('Product ID').'*',
					'options' => $this->l('Options (Group:Value)').'*',
					'reference' => $this->l('Reference'),
					'supplier_reference' => $this->l('Supplier reference'),
					'ean13' => $this->l('EAN13'),
					'upc' => $this->l('UPC'),
					'wholesale_price' => $this->l('Wholesale price'),
					'price' => $this->l('Price'),
					'ecotax' => $this->l('Ecotax'),
					'quantity' => $this->l('Quantity'),
					'weight' => $this->l('Weight'),
					'default_on' => $this->l('Default')
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
					'weight' => 0,
					'default_on' => 0
				);

				break;

			case $this->entities[$this->l('Categories')]:

				$this->available_fields = array(
				'no' => $this->l('Ignore this column'),
				'id' => $this->l('ID'),
				'active' => $this->l('Active (0/1)'),
				'name' => $this->l('Name *'),
				'parent' => $this->l('Parent category'),
				'description' => $this->l('Description'),
				'meta_title' => $this->l('Meta-title'),
				'meta_keywords' => $this->l('Meta-keywords'),
				'meta_description' => $this->l('Meta-description'),
				'link_rewrite' => $this->l('URL rewritten'),
				'image' => $this->l('Image URL'));

				self::$default_values = array('active' => '1', 'parent' => '1', 'link_rewrite' => '');

				break;

			case $this->entities[$this->l('Products')]:

				self::$validators['image'] = array('AdminImport', 'split');

				$this->available_fields = array(
				'no' => $this->l('Ignore this column'),
				'id' => $this->l('ID'),
				'active' => $this->l('Active (0/1)'),
				'name' => $this->l('Name *'),
				'category' => $this->l('Categories (x,y,z...)'),
				'price_tex' => $this->l('Price tax excl.'),
				'price_tin' => $this->l('Price tax incl.'),
				'id_tax_rules_group' => $this->l('Tax rules id'),
				'wholesale_price' => $this->l('Wholesale price'),
				'on_sale' => $this->l('On sale (0/1)'),
				'reduction_price' => $this->l('Discount amount'),
				'reduction_percent' => $this->l('Discount percent'),
				'reduction_from' => $this->l('Discount from (yyyy-mm-dd)'),
				'reduction_to' => $this->l('Discount to (yyyy-mm-dd)'),
				'reference' => $this->l('Reference #'),
				'supplier_reference' => $this->l('Supplier reference #'),
				'supplier' => $this->l('Supplier'),
				'manufacturer' => $this->l('Manufacturer'),
				'ean13' => $this->l('EAN13'),
				'upc' => $this->l('UPC'),
				'ecotax' => $this->l('Ecotax'),
				'weight' => $this->l('Weight'),
				'quantity' => $this->l('Quantity'),
				'description_short' => $this->l('Short description'),
				'description' => $this->l('Description'),
				'tags' => $this->l('Tags (x,y,z...)'),
				'meta_title' => $this->l('Meta-title'),
				'meta_keywords' => $this->l('Meta-keywords'),
				'meta_description' => $this->l('Meta-description'),
				'link_rewrite' => $this->l('URL rewritten'),
				'available_now' => $this->l('Text when in-stock'),
				'available_later' => $this->l('Text if back-order allowed'),
				'image' => $this->l('Image URLs (x,y,z...)'),
				'feature' => $this->l('Feature'),
				'online_only' => $this->l('Only available online'));

				self::$default_values = array(
				'id_category' => array(1),
				'id_category_default' => 1,
				'active' => '1',
				'quantity' => 0,
				'price' => 0,
				'id_tax_rules_group' => 0,
				'description_short' => array((int)(Configuration::get('PS_LANG_DEFAULT')) => ''),
				'link_rewrite' => array((int)(Configuration::get('PS_LANG_DEFAULT')) => ''),
				'online_only' => 0);

				break;

			case $this->entities[$this->l('Customers')]:

				//Overwrite required_fields AS only email is required whereas other entities
				self::$required_fields = array('email', 'passwd', 'lastname', 'firstname');

				$this->available_fields = array(
				'no' => $this->l('Ignore this column'),
				'id' => $this->l('ID'),
				'active' => $this->l('Active  (0/1)'),
				'id_gender' => $this->l('Gender ID (Mr = 1, Ms = 2, else 9)'),
				'email' => $this->l('E-mail *'),
				'passwd' => $this->l('Password *'),
				'birthday' => $this->l('Birthday (yyyy-mm-dd)'),
				'lastname' => $this->l('Lastname *'),
				'firstname' => $this->l('Firstname *'),
				'newsletter' => $this->l('Newsletter (0/1)'),
				'optin' => $this->l('Opt in (0/1)'));

				self::$default_values = array('active' => '1');

			break;
			case $this->entities[$this->l('Addresses')]:

				//Overwrite required_fields
				self::$required_fields = array('lastname', 'firstname', 'address1', 'postcode', 'country', 'city');

				$this->available_fields = array(
				'no' => $this->l('Ignore this column'),
				'id' => $this->l('ID'),
				'alias' => $this->l('Alias *'),
				'active' => $this->l('Active  (0/1)'),
				'customer_email' => $this->l('Customer e-mail'),
				'manufacturer' => $this->l('Manufacturer'),
				'supplier' => $this->l('Supplier'),
				'company' => $this->l('Company'),
				'lastname' => $this->l('Lastname *'),
				'firstname' => $this->l('Firstname *'),
				'address1' => $this->l('Address 1 *'),
				'address2' => $this->l('Address 2'),
				'postcode' => $this->l('Postcode*/ Zipcode*'),
				'city' => $this->l('City *'),
				'country' => $this->l('Country *'),
				'state' => $this->l('State'),
				'other' => $this->l('Other'),
				'phone' => $this->l('Phone'),
				'phone_mobile' => $this->l('Mobile Phone'),
				'vat_number' => $this->l('VAT number'));

				self::$default_values = array('alias' => 'Alias', 'postcode' => 'X');

			break;
			case $this->entities[$this->l('Manufacturers')]:
			case $this->entities[$this->l('Suppliers')]:

				//Overwrite validators AS name is not MultiLangField
				self::$validators = array(
				'description' => array('AdminImport', 'createMultiLangField'),
				'description_short' => array('AdminImport', 'createMultiLangField'),
				'meta_title' => array('AdminImport', 'createMultiLangField'),
				'meta_keywords' => array('AdminImport', 'createMultiLangField'),
				'meta_description' => array('AdminImport', 'createMultiLangField'));

				$this->available_fields = array(
				'no' => $this->l('Ignore this column'),
				'id' => $this->l('ID'),
				'active' => $this->l('Active (0/1)'),
				'name' => $this->l('Name *'),
				'description' => $this->l('Description'),
				'short_description' => $this->l('Short description'),
				'meta_title' => $this->l('Meta-title'),
				'meta_keywords' => $this->l('Meta-keywords'),
				'meta_description' => $this->l('Meta-description'));
			break;
		}
		parent::__construct();
	}

	private static function getBoolean($field)
	{
		return (boolean)$field;
	}

	private static function getPrice($field)
	{
		$field = ((float)(str_replace(',', '.', $field)));
		$field = ((float)(str_replace('%', '', $field)));
		return $field;
	}

	private static function split($field)
	{
		$separator = ((is_null(Tools::getValue('multiple_value_separator')) OR trim(Tools::getValue('multiple_value_separator')) == '' ) ? ',' : Tools::getValue('multiple_value_separator'));
		$tab = explode($separator, $field);
		$res = array_map('strval', $tab);
		$res = array_map('trim', $tab);
		return $tab;
	}

	private static function createMultiLangField($field)
	{
		$languages = Language::getLanguages(false);
		$res = array();
		foreach ($languages AS $lang)
			$res[$lang['id_lang']] = $field;
		return $res;
	}

	private function getTypeValuesOptions($nb_c)
	{
		$i = 0;
		$noPreSelect = array('price_tin', 'feature');

		$options = '';
		foreach ($this->available_fields AS $k => $field)
		{
			$options .= '<option value="'.$k.'"';
			if ($k === 'price_tin')
				++$nb_c;
			if ($i === ($nb_c + 1) AND (!in_array($k, $noPreSelect)))
				$options .= ' selected="selected"';
			$options .= '>'.$field.'</option>';
			++$i;
		}
		return $options;
	}

	/*
	* Return fields to be display AS piece of advise
	*
	* @param $inArray boolean
	* @return string or return array
	*/
	public function getAvailableFields($inArray = false)
	{
		$i = 0;
		$fields = array();
		foreach ($this->available_fields AS $k => $field)
		{
			if ($k === 'no')
				continue;
			if ($k === 'price_tin')
			{
				$fields[$i-1] = $fields[$i-1].' '.$this->l('or').' '.$field;
			}
			else
				$fields[] = $field;
			++$i;
		}
		if ($inArray)
			return $fields;
		else
			return implode("\n\r", $fields);
	}

	private function receiveTab()
	{
		$type_value = Tools::getValue('type_value') ? Tools::getValue('type_value') : array();
		foreach ($type_value AS $nb => $type)
			if ($type != 'no')
				self::$column_mask[$type] = $nb;
	}

	public static function getMaskedRow($row)
	{
		$res = array();
		foreach (self::$column_mask AS $type => $nb)
			$res[$type] = isset($row[$nb]) ? $row[$nb] : null;
		return $res;
	}

	private static function setDefaultValues(&$info)
	{
		foreach (self::$default_values AS $k => $v)
			if (!isset($info[$k]) OR $info[$k] == '')
				$info[$k] = $v;
	}

	private static function setEntityDefaultValues(&$entity)
	{
		$members = get_object_vars($entity);
		foreach (self::$default_values AS $k => $v)
			if ((array_key_exists($k, $members) AND $entity->$k === NULL) OR !array_key_exists($k, $members))
				$entity->$k = $v;
	}

	private static function fillInfo($infos, $key, &$entity)
	{
		if (isset(self::$validators[$key][1]) && self::$validators[$key][1] == 'createMultiLangField' && Tools::getValue('iso_lang'))
		{
			$id_lang = Language::getIdByIso(Tools::getValue('iso_lang'));
			$tmp = call_user_func(self::$validators[$key], $infos);
			foreach ($tmp as $id_lang_tmp => $value)
				if (empty($entity->{$key}[$id_lang_tmp]) OR $id_lang_tmp == $id_lang)
					$entity->{$key}[$id_lang_tmp] = $value;
		}
		else
			$entity->{$key} = isset(self::$validators[$key]) ? call_user_func(self::$validators[$key], $infos) : $infos;
		return true;
	}

	public static function fgetcsv($handle, $lenght, $delimiter)
	{
		if (feof($handle))
			return false;
		$line = fgets($handle, $lenght);
		if ($line === false)
			return false;
		$tmpTab = explode($delimiter, $line);

		foreach ($tmpTab AS &$row)
			if (preg_match ('/^".*"$/Uims',$row))
				$row = trim($row, '"');
		return $tmpTab;
	}

	static public function array_walk(&$array, $funcname, &$user_data = false)
	{
		if (!is_callable($funcname)) return false;

		foreach ($array AS $k => $row)
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
	private static function copyImg($id_entity, $id_image = NULL, $url, $entity = 'products')
	{
		$tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
		$watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));

		switch($entity)
		{
			default:
			case 'products':
				$path = _PS_PROD_IMG_DIR_.(int)($id_entity).'-'.(int)($id_image);
			break;
			case 'categories':
				$path = _PS_CAT_IMG_DIR_.(int)($id_entity);
			break;
		}

		if (copy(trim($url), $tmpfile))
		{
			imageResize($tmpfile, $path.'.jpg');
			$imagesTypes = ImageType::getImagesTypes($entity);
			foreach ($imagesTypes AS $k => $imageType)
				imageResize($tmpfile, $path.'-'.stripslashes($imageType['name']).'.jpg', $imageType['width'], $imageType['height']);
			if (in_array($imageType['id_image_type'], $watermark_types))
				Module::hookExec('watermark', array('id_image' => $id_image, 'id_product' => $id_entity));
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
		$catMoved = array();

		$this->receiveTab();
		$handle = $this->openCsvFile();
		$defaultLanguageId = (int)Configuration::get('PS_LANG_DEFAULT');
		self::setLocale();
		for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, Tools::getValue('separator')); $current_line++)
		{
			if (Tools::getValue('convert'))
				$this->utf8_encode_array($line);
			$info = self::getMaskedRow($line);

			self::setDefaultValues($info);
			$category = new Category();
			self::array_walk($info, array('AdminImport', 'fillInfo'), $category);

			if (isset($category->parent) AND is_numeric($category->parent))
			{
				if (isset($catMoved[$category->parent]))
					$category->parent = $catMoved[$category->parent];
				$category->id_parent = $category->parent;
			}
			elseif (isset($category->parent) AND is_string($category->parent))
			{
				$categoryParent = Category::searchByName($defaultLanguageId, $category->parent, true);
				if ($categoryParent['id_category'])
					$category->id_parent =	(int)($categoryParent['id_category']);
				else
				{
					$categoryToCreate= new Category();
					$categoryToCreate->name = self::createMultiLangField($category->parent);
					$categoryToCreate->active = 1;
					$categoryToCreate->id_parent = 1; // Default parent is home for unknown category to create
					if (($fieldError = $categoryToCreate->validateFields(UNFRIENDLY_ERROR, true)) === true AND ($langFieldError = $categoryToCreate->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true AND $categoryToCreate->add())
						$category->id_parent = $categoryToCreate->id;
					else
					{
						$this->_errors[] = $categoryToCreate->name[$defaultLanguageId].(isset($categoryToCreate->id) ? ' ('.$categoryToCreate->id.')' : '').' '.Tools::displayError('Cannot be saved');
						$this->_errors[] = ($fieldError !== true ? $fieldError : '').($langFieldError !== true ? $langFieldError : '').mysql_error();
					}
				}
			}
			if (isset($category->link_rewrite) AND !empty($category->link_rewrite[$defaultLanguageId]))
				$valid_link = Validate::isLinkRewrite($category->link_rewrite[$defaultLanguageId]);
			else
				$valid_link = false;

			$bak = $category->link_rewrite[$defaultLanguageId];
			if ((isset($category->link_rewrite) AND empty($category->link_rewrite[$defaultLanguageId])) OR !$valid_link)
			{
				$category->link_rewrite = Tools::link_rewrite($category->name[$defaultLanguageId]);
				if ($category->link_rewrite == '')
				{
					$category->link_rewrite = 'friendly-url-autogeneration-failed';
					$this->_warnings[] = Tools::displayError('URL rewriting failed to auto-generate a friendly URL for: ').$category->name[$defaultLanguageId];
				}
				$category->link_rewrite = self::createMultiLangField($category->link_rewrite);
			}

			if (!$valid_link)
				$this->_warnings[] = Tools::displayError('Rewrite link for').' '.$bak.(isset($info['id']) ? ' (ID '.$info['id'].') ' : '').' '.Tools::displayError('was re-written as').' '.$category->link_rewrite[$defaultLanguageId];
			$res = false;
			if (($fieldError = $category->validateFields(UNFRIENDLY_ERROR, true)) === true AND ($langFieldError = $category->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true)
			{
				$categoryAlreadyCreated = Category::searchByNameAndParentCategoryId($defaultLanguageId, $category->name[$defaultLanguageId], $category->id_parent);

				// If category already in base, get id category back
				if ($categoryAlreadyCreated['id_category'])
				{
					$catMoved[$category->id] = (int)($categoryAlreadyCreated['id_category']);
					$category->id =	(int)($categoryAlreadyCreated['id_category']);
				}

				/* No automatic nTree regeneration for import */
				$category->doNotRegenerateNTree = true;

				// If id category AND id category already in base, trying to update
				if ($category->id AND $category->categoryExists($category->id))
					$res = $category->update();

				// If no id_category or update failed
				if (!$res AND $res = $category->add())
					$category->addGroups(array(1));
			}
			//copying images of categories
			if (isset($category->image) AND !empty($category->image))
				if (!(self::copyImg($category->id, NULL, $category->image, 'categories')))
					$this->_warnings[] = $category->image.' '.Tools::displayError('Cannot be copied');
			// If both failed, mysql error
			if (!$res)
			{
				$this->_errors[] = $info['name'].(isset($info['id']) ? ' (ID '.$info['id'].')' : '').' '.Tools::displayError('Cannot be saved');
				$this->_errors[] = ($fieldError !== true ? $fieldError : '').($langFieldError !== true ? $langFieldError : '').mysql_error();
			}
		}

		/* Import has finished, we can regenerate the categories nested tree */
		Category::regenerateEntireNtree();

		$this->closeCsvFile($handle);
	}

	public function productImport()
	{
		global $cookie;
		$this->receiveTab();
		$handle = $this->openCsvFile();
		$defaultLanguageId = (int)(Configuration::get('PS_LANG_DEFAULT'));
		self::setLocale();
		for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, Tools::getValue('separator')); $current_line++)
		{
			if (Tools::getValue('convert'))
				$this->utf8_encode_array($line);
			$info = self::getMaskedRow($line);
			if (array_key_exists('id', $info) AND (int)($info['id']) AND Product::existsInDatabase((int)($info['id'])))
			{
				$product = new Product((int)($info['id']));
				$categoryData = Product::getIndexedCategories((int)($product->id));
				foreach ($categoryData as $tmp)
					$product->category[] = $tmp['id_category'];
			}
			else
				$product = new Product();
			self::setEntityDefaultValues($product);
			self::array_walk($info, array('AdminImport', 'fillInfo'), $product);
            $trg_id = (int)$product->id_tax_rules_group;

			if ($product->id_tax_rules_group == 0 || !Validate::isLoadedObject(new TaxRulesGroup($trg_id)))
				$this->_addProductWarning('id_tax_rules_group', $product->id_tax_rules_group, Tools::displayError('Invalid tax rule group ID, you first need a group with this ID.'));
			else
			{
			    $product->tax_rate = TaxRulesGroup::getTaxesRate((int)$product->id_tax_rules_group, Configuration::get('PS_COUNTRY_DEFAULT'), 0, 0);
				
			}
			

			if (isset($product->manufacturer) AND is_numeric($product->manufacturer) AND Manufacturer::manufacturerExists((int)($product->manufacturer)))
				$product->id_manufacturer = (int)($product->manufacturer);
			elseif (isset($product->manufacturer) AND is_string($product->manufacturer) AND !empty($product->manufacturer))
			{
				if ($manufacturer = Manufacturer::getIdByName($product->manufacturer))
					$product->id_manufacturer = (int)($manufacturer);
				else
				{
					$manufacturer = new Manufacturer();
					$manufacturer->name = $product->manufacturer;
					if (($fieldError = $manufacturer->validateFields(UNFRIENDLY_ERROR, true)) === true AND ($langFieldError = $manufacturer->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true AND $manufacturer->add())
						$product->id_manufacturer = (int)($manufacturer->id);
					else
					{
						$this->_errors[] = $manufacturer->name.(isset($manufacturer->id) ? ' ('.$manufacturer->id.')' : '').' '.Tools::displayError('Cannot be saved');
						$this->_errors[] = ($fieldError !== true ? $fieldError : '').($langFieldError !== true ? $langFieldError : '').mysql_error();
					}
				}
			}

			if (isset($product->supplier) AND is_numeric($product->supplier) AND Supplier::supplierExists((int)($product->supplier)))
				$product->id_supplier = (int)($product->supplier);
			elseif (isset($product->supplier) AND is_string($product->supplier) AND !empty($product->supplier))
			{
				if ($supplier = Supplier::getIdByName($product->supplier))
					$product->id_supplier = (int)($supplier);
				else
				{
					$supplier = new Supplier();
					$supplier->name = $product->supplier;
					if (($fieldError = $supplier->validateFields(UNFRIENDLY_ERROR, true)) === true AND ($langFieldError = $supplier->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true AND $supplier->add())
						$product->id_supplier = (int)($supplier->id);
					else
					{
						$this->_errors[] = $supplier->name.(isset($supplier->id) ? ' ('.$supplier->id.')' : '').' '.Tools::displayError('Cannot be saved');
						$this->_errors[] = ($fieldError !== true ? $fieldError : '').($langFieldError !== true ? $langFieldError : '').mysql_error();
					}
				}
			}

			if (isset($product->price_tex) AND !isset($product->price_tin))
				$product->price = $product->price_tex;
			elseif (isset($product->price_tin) AND !isset($product->price_tex))
			{
				$product->price = $product->price_tin;
				// If a tax is already included in price, withdraw it from price
				if ($product->tax_rate)
					$product->price = (float)(number_format($product->price / (1 + $product->tax_rate / 100), 6));
			}
			elseif (isset($product->price_tin) AND isset($product->price_tex))
				$product->price = $product->price_tex;

			if (isset($product->category) AND is_array($product->category) and sizeof($product->category))
			{
				$product->id_category = array(); // Reset default values array

				foreach ($product->category AS $value)
				{
					if (is_numeric($value))
					{
						if (Category::categoryExists((int)($value)))
							$product->id_category[] = (int)($value);
						else
						{
							$categoryToCreate= new Category();
							$categoryToCreate->id = (int)($value);
							$categoryToCreate->name = self::createMultiLangField($value);
							$categoryToCreate->active = 1;
							$categoryToCreate->id_parent = 1; // Default parent is home for unknown category to create
							if (($fieldError = $categoryToCreate->validateFields(UNFRIENDLY_ERROR, true)) === true AND ($langFieldError = $categoryToCreate->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true AND $categoryToCreate->add())
								$product->id_category[] = (int)($categoryToCreate->id);
							else
							{
								$this->_errors[] = $categoryToCreate->name[$defaultLanguageId].(isset($categoryToCreate->id) ? ' ('.$categoryToCreate->id.')' : '').' '.Tools::displayError('Cannot be saved');
								$this->_errors[] = ($fieldError !== true ? $fieldError : '').($langFieldError !== true ? $langFieldError : '').mysql_error();
							}
						}
					}
					elseif (is_string($value) AND !empty($value))
					{
						$category = Category::searchByName($defaultLanguageId, $value, true);
						if ($category['id_category'])
						{
							$product->id_category[] =	(int)($category['id_category']);
						}
						else
						{
							$categoryToCreate= new Category();
							$categoryToCreate->name = self::createMultiLangField($value);
							$categoryToCreate->active = 1;
							$categoryToCreate->id_parent = 1; // Default parent is home for unknown category to create
							if (($fieldError = $categoryToCreate->validateFields(UNFRIENDLY_ERROR, true)) === true AND ($langFieldError = $categoryToCreate->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true AND $categoryToCreate->add())
								$product->id_category[] = (int)($categoryToCreate->id);
							else
							{
								$this->_errors[] = $categoryToCreate->name[$defaultLanguageId].(isset($categoryToCreate->id) ? ' ('.$categoryToCreate->id.')' : '').' '.Tools::displayError('Cannot be saved');
								$this->_errors[] = ($fieldError !== true ? $fieldError : '').($langFieldError !== true ? $langFieldError : '').mysql_error();
							}
						}
					}
				}
			}

			$product->id_category_default = isset($product->id_category[0]) ? (int)($product->id_category[0]) : '';
			$link_rewrite = is_array($product->link_rewrite) ? $product->link_rewrite[$defaultLanguageId] : '';
			$valid_link = Validate::isLinkRewrite($link_rewrite);

			$bak = $product->link_rewrite;
			if ((isset($product->link_rewrite[$defaultLanguageId]) AND empty($product->link_rewrite[$defaultLanguageId])) OR !$valid_link)
			{
				$link_rewrite = Tools::link_rewrite($product->name[$defaultLanguageId]);
				if ($link_rewrite == '')
					$link_rewrite = 'friendly-url-autogeneration-failed';
			}
			if (!$valid_link)
				$this->_warnings[] = Tools::displayError('Rewrite link for'). ' '.$bak.(isset($info['id']) ? ' (ID '.$info['id'].') ' : '').' '.Tools::displayError('was re-written as').' '.$link_rewrite;

			$product->link_rewrite = self::createMultiLangField($link_rewrite);

			$res = false;
			$fieldError = $product->validateFields(UNFRIENDLY_ERROR, true);
			$langFieldError = $product->validateFieldsLang(UNFRIENDLY_ERROR, true);
			if ($fieldError === true AND $langFieldError === true)
			{
				// check quantity
				if ($product->quantity == NULL)
					$product->quantity = 0;
				// If id product AND id product already in base, trying to update
				if ($product->id AND Product::existsInDatabase((int)($product->id)))
				{

					$datas = Db::getInstance()->getRow('SELECT `date_add` FROM `'._DB_PREFIX_.'product` WHERE `id_product` = '.(int)($product->id));
					$product->date_add = pSQL($datas['date_add']);
					$res = $product->update();
				}
				// If no id_product or update failed
				if (!$res)
					$res = $product->add();
			}
			// If both failed, mysql error
			if (!$res)
			{
				$this->_errors[] = $info['name'].(isset($info['id']) ? ' (ID '.$info['id'].')' : '').' '.Tools::displayError('Cannot be saved');
				$this->_errors[] = ($fieldError !== true ? $fieldError : '').($langFieldError !== true ? $langFieldError : '').mysql_error();

			}
			else
			{
				// SpecificPrice (only the basic reduction feature is supported by the import)
				if (isset($info['reduction_price']) OR isset($info['reduction_percent']))
				{
					$specificPrice = new SpecificPrice();
					$specificPrice->id_product = (int)($product->id);
					$specificPrice->id_shop = (int)(Shop::getCurrentShop());
					$specificPrice->id_currency = 0;
					$specificPrice->id_country = 0;
					$specificPrice->id_group = 0;
					$specificPrice->price = 0.00;
					$specificPrice->from_quantity = 1;
					$specificPrice->reduction = (isset($info['reduction_price']) AND $info['reduction_price']) ? $info['reduction_price'] : $info['reduction_percent'] / 100;
					$specificPrice->reduction_type = (isset($info['reduction_price']) AND $info['reduction_price']) ? 'amount' : 'percentage';
					$specificPrice->from = (isset($info['reduction_from']) AND Validate::isDate($info['reduction_from'])) ? $info['reduction_from'] : '0000-00-00 00:00:00';
					$specificPrice->to = (isset($info['reduction_to']) AND Validate::isDate($info['reduction_to']))  ? $info['reduction_to'] : '0000-00-00 00:00:00';
					if (!$specificPrice->add())
						$this->_addProductWarning($info['name'], $product->id, $this->l('Discount is invalid'));
				}

				if (isset($product->tags) AND !empty($product->tags))
				{
					// Delete tags for this id product, for no duplicating error
					Tag::deleteTagsForProduct($product->id);

					$tag = new Tag();
					if (!is_array($product->tags))
					{
						$product->tags = self::createMultiLangField($product->tags);
						foreach($product->tags AS $key => $tags)
						{
							$isTagAdded = $tag->addTags($key, $product->id, $tags);
							if (!$isTagAdded)
							{
								$this->_addProductWarning($info['name'], $product->id, $this->l('Tags list').' '.$this->l('is invalid'));
								break;
							}
						}

					}
					else
					{
						foreach ($product->tags AS $key => $tags)
						{
							$str = '';
							foreach($tags AS $one_tag)
								$str .= $one_tag.',';
							$str = rtrim($str, ',');

							$isTagAdded = $tag->addTags($key, $product->id, $str);
							if (!$isTagAdded)
							{
								$this->_addProductWarning($info['name'], $product->id,'Invalid tag(s) ('.$str.')');
								break;
							}
						}
					}
				}

				if (isset($product->image) AND is_array($product->image) and sizeof($product->image))
				{
					$productHasImages = (bool)Image::getImages((int)($cookie->id_lang), (int)($product->id));
					foreach ($product->image AS $key => $url)
						if (!empty($url))
						{
							$image = new Image();
							$image->id_product = (int)($product->id);
							$image->position = Image::getHighestPosition($product->id) + 1;
							$image->cover = (!$key AND !$productHasImages) ? true : false;
							$image->legend = self::createMultiLangField($product->name[$defaultLanguageId]);
							if (($fieldError = $image->validateFields(UNFRIENDLY_ERROR, true)) === true AND ($langFieldError = $image->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true AND $image->add())
							{
								if (!self::copyImg($product->id, $image->id, $url))
									$this->_warnings[] = Tools::displayError('Error copying image: ').$url;
							}
							else
							{
								$this->_warnings[] = $image->legend[$defaultLanguageId].(isset($image->id_product) ? ' ('.$image->id_product.')' : '').' '.Tools::displayError('Cannot be saved');
								$this->_errors[] = ($fieldError !== true ? $fieldError : '').($langFieldError !== true ? $langFieldError : '').mysql_error();
							}
						}
				}
				if (isset($product->id_category))
					$product->updateCategories(array_map('intval', $product->id_category));

				$features = get_object_vars($product);
				foreach ($features AS $feature => $value)
					if (!strncmp($feature, '#F_', 3) AND Tools::strlen($product->{$feature}))
					{
						$feature_name = str_replace('#F_', '', $feature);
						$id_feature = Feature::addFeatureImport($feature_name);
						$id_feature_value = FeatureValue::addFeatureValueImport($id_feature, $product->{$feature});
						Product::addFeatureProductImport($product->id, $id_feature, $id_feature_value);
					}
			}
		}
		$this->closeCsvFile($handle);
	}

	public function attributeImport()
	{
		$defaultLanguage = Configuration::get('PS_LANG_DEFAULT');
		$groups = array();
		foreach (AttributeGroup::getAttributesGroups($defaultLanguage) AS $group)
			$groups[$group['name']] = (int)($group['id_attribute_group']);
		$attributes = array();
		foreach (Attribute::getAttributes($defaultLanguage) AS $attribute)
			$attributes[$attribute['attribute_group'].'_'.$attribute['name']] = (int)($attribute['id_attribute']);

		$this->receiveTab();
		$handle = $this->openCsvFile();
		$fsep = ((is_null(Tools::getValue('multiple_value_separator')) OR trim(Tools::getValue('multiple_value_separator')) == '' ) ? ',' : Tools::getValue('multiple_value_separator'));
		self::setLocale();
		for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, Tools::getValue('separator')); $current_line++)
		{
			if (Tools::getValue('convert'))
				$this->utf8_encode_array($line);
			$info = self::getMaskedRow($line);
			$info = array_map('trim', $info);

			self::setDefaultValues($info);
			$product = new Product((int)($info['id_product']), false, $defaultLanguage);
			$id_product_attribute = $product->addProductAttribute((float)($info['price']), (float)($info['weight']), 0, (float)($info['ecotax']), (int)($info['quantity']), null, strval($info['reference']), strval($info['supplier_reference']), strval($info['ean13']), (int)($info['default_on']), strval($info['upc']));
			foreach (explode($fsep, $info['options']) as $option)
			{
				list($group, $attribute) = array_map('trim', explode(':', $option));
				if (!isset($groups[$group]))
				{
					$obj = new AttributeGroup();
					$obj->is_color_group = false;
					$obj->name[$defaultLanguage] = $group;
					$obj->public_name[$defaultLanguage] = $group;
					if (($fieldError = $obj->validateFields(UNFRIENDLY_ERROR, true)) === true AND ($langFieldError = $obj->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true)
					{
						$obj->add();
						$groups[$group] = $obj->id;
					}
					else
						$this->_errors[] = ($fieldError !== true ? $fieldError : '').($langFieldError !== true ? $langFieldError : '');
				}
				if (!isset($attributes[$group.'_'.$attribute]))
				{
					$obj = new Attribute();
					$obj->id_attribute_group = $groups[$group];
					$obj->name[$defaultLanguage] = $attribute;
					if (($fieldError = $obj->validateFields(UNFRIENDLY_ERROR, true)) === true AND ($langFieldError = $obj->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true)
					{
						$obj->add();
						$attributes[$group.'_'.$attribute] = $obj->id;
					}
					else
						$this->_errors[] = ($fieldError !== true ? $fieldError : '').($langFieldError !== true ? $langFieldError : '');
				}
				Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'product_attribute_combination (id_attribute, id_product_attribute) VALUES ('.(int)($attributes[$group.'_'.$attribute]).','.(int)($id_product_attribute).')');
			}
		}
		$this->closeCsvFile($handle);
	}

	public function customerImport()
	{
		$this->receiveTab();
		$handle = $this->openCsvFile();
		self::setLocale();
		for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, Tools::getValue('separator')); $current_line++)
		{
			if (Tools::getValue('convert'))
				$this->utf8_encode_array($line);
			$info = self::getMaskedRow($line);

			self::setDefaultValues($info);
			$customer = new Customer();
			self::array_walk($info, array('AdminImport', 'fillInfo'), $customer);

			if ($customer->passwd)
				$customer->passwd = md5(_COOKIE_KEY_.$customer->passwd);

			$res = false;
			if (($fieldError = $customer->validateFields(UNFRIENDLY_ERROR, true)) === true AND ($langFieldError = $customer->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true)
			{
				if ($customer->id AND $customer->customerIdExists($customer->id))
					$res = $customer->update();
				if (!$res)
					$res = $customer->add();
				if ($res)
					$customer->addGroups(array(1));
			}
			if (!$res)
			{
				$this->_errors[] = $info['email'].(isset($info['id']) ? ' (ID '.$info['id'].')' : '').' '.Tools::displayError('Cannot be saved');
				$this->_errors[] = ($fieldError !== true ? $fieldError : ($langFieldError !== true ? $langFieldError : '')).mysql_error();
			}
		}
		$this->closeCsvFile($handle);
	}

	public function addressImport()
	{
		$this->receiveTab();
		$defaultLanguageId = (int)Configuration::get('PS_LANG_DEFAULT');
		$handle = $this->openCsvFile();
		self::setLocale();
		for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, Tools::getValue('separator')); $current_line++)
		{
			if (Tools::getValue('convert'))
				$this->utf8_encode_array($line);
			$info = self::getMaskedRow($line);

			self::setDefaultValues($info);
			$address = new Address();
			self::array_walk($info, array('AdminImport', 'fillInfo'), $address);

			if (isset($address->country) AND is_numeric($address->country))
			{
				if (Country::getNameById(Configuration::get('PS_LANG_DEFAULT'), (int)($address->country)))
					$address->id_country = (int)($address->country);
			}
			elseif(isset($address->country) AND is_string($address->country) AND !empty($address->country))
			{
				if ($id_country = Country::getIdByName(NULL, $address->country))
					$address->id_country = (int)($id_country);
				else
				{
					$country = new Country();
					$country->active = 1;
					$country->name = self::createMultiLangField($address->country);
					$country->id_zone = 0; // Default zone for country to create
					$country->iso_code = strtoupper(substr($address->country, 0, 2)); // Default iso for country to create
					$country->contains_states = 0; // Default value for country to create
					$langFieldError = $country->validateFieldsLang(UNFRIENDLY_ERROR, true);
					if (($fieldError = $country->validateFields(UNFRIENDLY_ERROR, true)) === true AND ($langFieldError = $country->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true AND $country->add())
						$address->id_country = (int)($country->id);
					else
					{
						$this->_errors[] = $country->name[$defaultLanguageId].' '.Tools::displayError('Cannot be saved');
						$this->_errors[] = ($fieldError !== true ? $fieldError : '').($langFieldError !== true ? $langFieldError : '').mysql_error();
					}
				}
			}

			if (isset($address->state) AND is_numeric($address->state))
			{
				if (State::getNameById((int)($address->state)))
					$address->id_state = (int)($address->state);
			}
			elseif(isset($address->state) AND is_string($address->state) AND !empty($address->state))
			{
				if ($id_state = State::getIdByName($address->state))
					$address->id_state = (int)($id_state);
				else
				{
					$state = new State();
					$state->active = 1;
					$state->name = $address->state;
					$state->id_country = isset($country->id) ? (int)($country->id) : 0;
					$state->id_zone = 0; // Default zone for state to create
					$state->iso_code = strtoupper(substr($address->state, 0, 2)); // Default iso for state to create
					$state->tax_behavior = 0;
					if (($fieldError = $state->validateFields(UNFRIENDLY_ERROR, true)) === true AND ($langFieldError = $state->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true AND $state->add())
						$address->id_state = (int)($state->id);
					else
					{
						$this->_errors[] = $state->name.' '.Tools::displayError('Cannot be saved');
						$this->_errors[] = ($fieldError !== true ? $fieldError : '').($langFieldError !== true ? $langFieldError : '').mysql_error();
					}
				}
			}

			if(isset($address->customer_email) and !empty($address->customer_email))
			{	
				if( Validate::isEmail($address->customer_email))
				{
					$customer = Customer::customerExists($address->customer_email, true);
					if ($customer)
						$address->id_customer = (int)($customer);
					else
						$this->_errors[] = mysql_error().' '.$address->customer_email.' '.Tools::displayError('does not exist in base').' '.(isset($info['id']) ? ' (ID '.$info['id'].')' : '').' '.Tools::displayError('Cannot be saved');
				}
				else
					$this->_errors[] = '"'.$address->customer_email.'" :' .Tools::displayError('Is not a valid Email');
			}

			if (isset($address->manufacturer) AND is_numeric($address->manufacturer) AND Manufacturer::manufacturerExists((int)($address->manufacturer)))
				$address->id_manufacturer = (int)($address->manufacturer);
			elseif (isset($address->manufacturer) AND is_string($address->manufacturer) AND !empty($address->manufacturer))
			{
				$manufacturer = new Manufacturer();
				$manufacturer->name = $address->manufacturer;
				if (($fieldError = $manufacturer->validateFields(UNFRIENDLY_ERROR, true)) === true AND ($langFieldError = $manufacturer->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true AND $manufacturer->add())
					$address->id_manufacturer = (int)($manufacturer->id);
				else
				{
					$this->_errors[] = mysql_error().' '.$manufacturer->name.(isset($manufacturer->id) ? ' ('.$manufacturer->id.')' : '').' '.Tools::displayError('Cannot be saved');
					$this->_errors[] = ($fieldError !== true ? $fieldError : '').($langFieldError !== true ? $langFieldError : '').mysql_error();
				}
			}

			if (isset($address->supplier) AND is_numeric($address->supplier) AND Supplier::supplierExists((int)($address->supplier)))
				$address->id_supplier = (int)($address->supplier);
			elseif (isset($address->supplier) AND is_string($address->supplier) AND !empty($address->supplier))
			{
				$supplier = new Supplier();
				$supplier->name = $address->supplier;
				if (($fieldError = $supplier->validateFields(UNFRIENDLY_ERROR, true)) === true AND ($langFieldError = $supplier->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true AND $supplier->add())
					$address->id_supplier = (int)($supplier->id);
				else
				{
					$this->_errors[] = mysql_error().' '.$supplier->name.(isset($supplier->id) ? ' ('.$supplier->id.')' : '').' '.Tools::displayError('Cannot be saved');
					$this->_errors[] = ($fieldError !== true ? $fieldError : '').($langFieldError !== true ? $langFieldError : '').mysql_error();
				}
			}

			$res = false;
			if (($fieldError = $address->validateFields(UNFRIENDLY_ERROR, true)) === true AND ($langFieldError = $address->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true)
			{
				if ($address->id AND $address->addressExists($address->id))
					$res = $address->update();
				if (!$res)
					$res = $address->add();
			}
			if (!$res)
			{
				$this->_errors[] = $info['alias'].(isset($info['id']) ? ' (ID '.$info['id'].')' : '').' '.Tools::displayError('Cannot be saved');
				$this->_errors[] = ($fieldError !== true ? $fieldError : '').($langFieldError !== true ? $langFieldError : '').mysql_error();
			}
		}
		$this->closeCsvFile($handle);
	}

	public function manufacturerImport()
	{
		$this->receiveTab();
		$handle = $this->openCsvFile();
		self::setLocale();
		for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, Tools::getValue('separator')); $current_line++)
		{
			if (Tools::getValue('convert'))
				$this->utf8_encode_array($line);
			$info = self::getMaskedRow($line);

			self::setDefaultValues($info);
			$manufacturer = new Manufacturer();
			self::array_walk($info, array('AdminImport', 'fillInfo'), $manufacturer);

			$res = false;
			if (($fieldError = $manufacturer->validateFields(UNFRIENDLY_ERROR, true)) === true AND ($langFieldError = $manufacturer->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true)
			{
				if ($manufacturer->id AND $manufacturer->manufacturerExists($manufacturer->id))
					$res = $manufacturer->update();
				if (!$res)
					$res = $manufacturer->add();
			}
			if (!$res)
			{
				$this->_errors[] = mysql_error().' '.$info['name'].(isset($info['id']) ? ' (ID '.$info['id'].')' : '').' '.Tools::displayError('Cannot be saved');
				$this->_errors[] = ($fieldError !== true ? $fieldError : '').($langFieldError !== true ? $langFieldError : '').mysql_error();
			}
		}
		$this->closeCsvFile($handle);
	}

	public function supplierImport()
	{
		$this->receiveTab();
		$handle = $this->openCsvFile();
		self::setLocale();
		for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, Tools::getValue('separator')); $current_line++)
		{
			if (Tools::getValue('convert'))
				$this->utf8_encode_array($line);
			$info = self::getMaskedRow($line);

			self::setDefaultValues($info);
			$supplier = new Supplier();
			self::array_walk($info, array('AdminImport', 'fillInfo'), $supplier);

			if (($fieldError = $supplier->validateFields(UNFRIENDLY_ERROR, true)) === true AND ($langFieldError = $supplier->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true)
			{
				$res = false;
				if ($supplier->id AND $supplier->supplierExists($supplier->id))
					$res = $supplier->update();
				if (!$res)
					$res = $supplier->add();
				if (!$res)
					$this->_errors[] = mysql_error().' '.$info['name'].(isset($info['id']) ? ' (ID '.$info['id'].')' : '').' '.Tools::displayError('Cannot be saved');
			}
			else
			{
				$this->_errors[] = $this->l('Supplier not valid').' ('.$supplier->name.')';
				$this->_errors[] = ($fieldError !== true ? $fieldError : '').($langFieldError !== true ? $langFieldError : '');
			}
		}
		$this->closeCsvFile($handle);
	}

	public function display()
	{
		if (!Tools::isSubmit('submitImportFile'))
			$this->displayForm();
	}

	public function displayForm($isMainTab = true)
	{
		global $currentIndex, $cookie;
		parent::displayForm();

		if ((Tools::getValue('import')) AND (isset($this->_warnings) AND !sizeof($this->_warnings)))
			echo '<div class="module_confirmation conf confirm"><img src="../img/admin/ok.gif" alt="" title="" style="margin-right:5px; float:left;" />'.$this->l('The .CSV file has been imported into your shop.').'</div>';

		if(!is_writable(PS_ADMIN_DIR.'/import/'))
			$this->displayWarning($this->l('directory import on admin directory must be writable (CHMOD 755 / 777)'));

		if(isset($this->_warnings) AND sizeof($this->_warnings))
		{
			$warnings = array();
			foreach ($this->_warnings as $warning)
				$warnings[] = $warning;
			$this->displayWarning($warnings);
		}

		echo '
		<fieldset class="width3">
			<legend><img src="../img/admin/import.gif" />'.$this->l('Upload').'</legend>
			<form action="'.$currentIndex.'&token='.$this->token.'" method="POST" enctype="multipart/form-data">
				<label class="clear">'.$this->l('Select a file').' </label>
				<div class="margin-form">
					<input name="file" type="file" /><br />'.$this->l('You can also upload your file by FTP and put it in').' '.realpath(dirname(__FILE__).'/../import/').'.
				</div>
				<div class="margin-form">
					<input type="submit" name="submitFileUpload" value="'.$this->l('Upload').'" class="button" />
				</div>
				<div class="margin-form">
					'.$this->l('Allowed files are only UTF-8 and iso-8859-1 encoded ones').'
				</div>
			</form>
		</fieldset>';

		$filesToImport = scandir(dirname(__FILE__).'/../import');
		uasort($filesToImport, array('AdminImport', '_usortFiles'));
		foreach ($filesToImport AS $k => &$filename)
			if (in_array($filename, array('.', '..', '.svn', '.htaccess', 'index.php')))
				unset($filesToImport[$k]);
		unset($filename);
		if (sizeof($filesToImport))
		{
			echo '
			<div class="space">
					<form id="preview_import" action="'.$currentIndex.'&token='.$this->token.'" method="post" style="display:inline" enctype="multipart/form-data" class="clear" onsubmit="if ($(\'#truncate\').get(0).checked) {if (confirm(\''.$this->l('Are you sure you want to delete', __CLASS__, true, false).'\' + \' \' + $(\'#entity > option:selected\').text().toLowerCase() + \''.$this->l('?', __CLASS__, true, false).'\')){this.submit();} else {return false;}}">
						<fieldset style="float: left; width: 650px">
							<legend><img src="../img/admin/import.gif" />'.$this->l('Import').'</legend>
							<label class="clear">'.$this->l('Select which entity to import:').' </label>
							<div class="margin-form">
								<select name="entity" id="entity">';
			foreach ($this->entities AS $entity => $i)
			{
				echo '<option value="'.$i.'"';
				if (Tools::getValue('entity') == $i)
					echo ' selected="selected" ';
				echo'>'.$entity.'</option>';
			}
			echo '				</select>
							</div>
							<label class="clear">'.$this->l('Select your .CSV file:').' </label>
							<div class="margin-form">
								<select name="csv">';
			foreach ($filesToImport AS $filename)
				echo '<option value="'.$filename.'">'.$filename.'</option>';
			echo '				</select> ('.sizeof($filesToImport).' '.(sizeof($filesToImport) > 1 ? $this->l('files available') : $this->l('file available')).')
							</div>
							<label class="clear">'.$this->l('Select language of the file (the locale must be installed):').' </label>
							<div class="margin-form">
								<select name="iso_lang">';
							foreach ($this->_languages AS $lang)
								echo '<option value="'.$lang['iso_code'].'" '.($lang['id_lang'] == $cookie->id_lang ? 'selected="selected"' : '').'>'.$lang['name'].'</option>';
							echo '</select></div><label for="convert" class="clear">'.$this->l('iso-8859-1 encoded file').' </label>
							<div class="margin-form">
								<input name="convert" id="convert" type="checkbox" style="margin-top: 6px;"/>
							</div>
							<label class="clear">'.$this->l('Field separator:').' </label>
							<div class="margin-form">
								<input type="text" size="2" value=";" name="separator"/>
								'.$this->l('e.g. ').'"1<span class="bold" style="color: red">;</span>Ipod<span class="bold" style="color: red">;</span>129.90<span class="bold" style="color: red">;</span>5"
							</div>
							<label class="clear">'.$this->l('Multiple value separator:').' </label>
							<div class="margin-form">
								<input type="text" size="2" value="," name="multiple_value_separator"/>
								'.$this->l('e.g. ').'"Ipod;red.jpg<span class="bold" style="color: red">,</span>blue.jpg<span class="bold" style="color: red">,</span>green.jpg;129.90"
							</div>
							<label for="truncate" class="clear">'.$this->l('Delete all').' <span id="entitie">'.$this->l('categories').'</span> '.$this->l('before import ?').' </label>
							<div class="margin-form">
								<input name="truncate" id="truncate" type="checkbox" style="margin-top: 6px;"/>
							</div>
							<div class="space margin-form">
								<input type="submit" name="submitImportFile" value="'.$this->l('Next step').'" class="button"/>
							</div>
							<div>
								'.$this->l('Note that the category import does not support categories of the same name').'
							</div>
						</fieldset>
					</form>
					<fieldset style="display: inline; float: right; margin-left: 20px;">
					<legend><img src="../img/admin/import.gif" />'.$this->l('Fields available').'</legend>
					<div id="availableFields" style="min-height: 218px; width: 200px; font-size: 10px;">'.nl2br($this->getAvailableFields()).'</div>
					</fieldset>
					<div class="clear" style="float:right; font-size:10px; padding-right: 120px;">
					'.$this->l('* Required Fields').'
					</div>
				</div>
				<div class="clear">&nbsp;</div>
				<script type="text/javascript">
					$("select#entity").change
					(
						function()
						{
							$("#entitie").html($("#entity > option:selected").text().toLowerCase());
							$.getJSON("'.dirname($currentIndex).'/ajax.php",{getAvailableFields:1, entity: $("#entity").val()},
										function(j)
										{
											var fields = "";
											$("#availableFields").empty();
											for (var i = 0; i < j.length; i++)
											fields += j[i].field + "<br />";
											$("#availableFields").html(fields);
										}
									)
						}
					);
				</script>';

				if (Tools::getValue('entity'))
					echo' <script type="text/javascript">$("select#entity").change();</script>';
		}
	}

	public function utf8_encode_array(&$array)
	{
	    if (is_array($array))
			self::array_walk($array, array(get_class($this), 'utf8_encode_array'));
		else
			$array = utf8_encode($array);
	}

	private function getNbrColumn($handle, $glue)
	{
		$tmp = fgetcsv($handle, MAX_LINE_SIZE, $glue);
		fseek($handle, 0);
		return sizeof($tmp);
	}

	private static function _usortFiles($a, $b)
	{
		$a = strrev(substr(strrev($a), 0, 14));
		$b = strrev(substr(strrev($b), 0, 14));

		if ($a == $b)
			return 0;

		return ($a < $b) ? 1 : -1;
	}

	private function openCsvFile()
	{
		 $handle = fopen(dirname(__FILE__).'/../import/'.strval(preg_replace('/\.{2,}/', '.',Tools::getValue('csv'))), 'r');

		/* No BOM allowed */
		$bom = fread($handle, 3);
		if ($bom != '\xEF\xBB\xBF')
			rewind($handle);

		if (!$handle)
			die(Tools::displayError('Cannot read the CSV file'));

		for ($i = 0; $i < (int)(Tools::getValue('skip')); ++$i)
			$line = fgetcsv($handle, MAX_LINE_SIZE, Tools::getValue('separator', ';'));
		return $handle;
	}

	private function closeCsvFile($handle)
	{
		fclose($handle);
	}

	private function generateContentTable($current_table, $nb_table, $nb_column, $handle, $glue)
	{
		echo '
		<table id="table'.$current_table.'" style="display: none;" class="table" cellspacing="0" cellpadding="0">
			<tr>';

		// Header
		for ($i = 0; $i < $nb_column; $i++)
			if (MAX_COLUMNS * (int)($current_table) <= $i AND (int)($i) < MAX_COLUMNS * ((int)($current_table) + 1))
				echo '
				<th style="width: '.(750 / MAX_COLUMNS).'px; vertical-align: top; padding: 4px">
					<select onchange="askFeatureName(this, '.$i.');" style="width: '.(750 / MAX_COLUMNS).'px;" id="type_value['.$i.']" name="type_value['.$i.']" class="type_value">
						'.$this->getTypeValuesOptions($i).'
					</select>
					<div id="features_'.$i.'" style="display: none;"><input style="width: 90px" type="text" name="" id="feature_name_'.$i.'"><input type="button" value="ok" onclick="replaceFeature($(\'#feature_name_'.$i.'\').attr(\'name\'), '.$i.');"></div>
				</th>';
		echo '
			</tr>';
		ob_flush();
		ob_clean();

		/* Datas */
		self::setLocale();
		for ($current_line = 0; $current_line < 10 AND $line = fgetcsv($handle, MAX_LINE_SIZE, $glue); $current_line++)
		{
			/* UTF-8 conversion */
			if (Tools::getValue('convert'))
				$this->utf8_encode_array($line);
			echo '<tr id="table_'.$current_table.'_line_'.$current_line.'" style="padding: 4px">';
			foreach ($line AS $nb_c => $column)
				if ((MAX_COLUMNS * (int)($current_table) <= $nb_c) AND ((int)($nb_c) < MAX_COLUMNS * ((int)($current_table) + 1)))
					echo '<td>'.htmlentities(substr($column, 0, 200)).'</td>';
			echo '</tr>';
		}
		echo '</table>';
		fseek($handle, 0);
	}

	public function displayCSV()
	{
		global $currentIndex;
		$importMatchs = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'import_match');
		
		echo '
		<script src="'._PS_JS_DIR_.'adminImport.js"></script>
		<script type="text/javascript">
			var errorEmpty = "'.$this->l('Please enter a name to save.').'"
		</script>
		<h2>'.$this->l('Your data').'</h2>'.'
		<div style="float:right">
		<b>'.$this->l('Save and load your matching configuration').' : </b><br><br>
		<input type="text" name="newImportMatchs" id="newImportMatchs"><a id="saveImportMatchs" class="button" href="#">'.$this->l('Save').'</a><br><br>
		<div id="selectDivImportMatchs" '.(!$importMatchs ? 'style="display:none"' : '' ).'>';
			echo '<select id="valueImportMatchs">';
			foreach($importMatchs as $match)
				echo '<option id="'.htmlentities($match['id_import_match'],ENT_NOQUOTES, 'UTF-8').'" value="'.htmlentities($match['match'],ENT_NOQUOTES, 'UTF-8').'">'.htmlentities($match['name'],ENT_NOQUOTES, 'UTF-8').'</option>';
			echo '
				</select>
				<a class="button" id="loadImportMatchs" href="#">'.$this->l('Load').'</a>
				<a class="button" id="deleteImportMatchs" href="#">'.$this->l('Delete').'</a>';
		
		echo '</div></div><h3>'.$this->l('Please set the value type of each column').'</h3>
		<div id="error_duplicate_type" class="warning warn" style="display:none;">
			<h3>'.$this->l('Columns cannot have the same value type').'</h3>
		</div>
		<div id="required_column" class="warning warn" style="display:none;">
			<h3>'.Tools::displayError('Column').' <span id="missing_column">&nbsp;</span> '.Tools::displayError('must be set').'</h3>
		</div>';

		$glue = Tools::getValue('separator', ';');
		$handle = $this->openCsvFile();
		$nb_column = $this->getNbrColumn($handle, $glue);
		$nb_table = ceil($nb_column / MAX_COLUMNS);

		$res = array();
		foreach (self::$required_fields AS $elem)
			$res[] = '\''.$elem.'\'';

		echo '
		<form action="'.$currentIndex.'&token='.$this->token.'" method="post" id="import_form" name="import_form">
			'.$this->l('Skip').' <input type="text" size="2" name="skip" value="0" /> '.$this->l('lines').'.
			<input type="hidden" name="csv" value="'.Tools::getValue('csv').'" />
			<input type="hidden" name="convert" value="'.Tools::getValue('convert').'" />
			<input type="hidden" name="entity" value="'.(int)(Tools::getValue('entity')).'" />
			<input type="hidden" name="iso_lang" value="'.Tools::getValue('iso_lang').'" />';
		if (Tools::getValue('truncate'))
			echo '<input type="hidden" name="truncate" value="1" />';
		echo '
			<input type="hidden" name="separator" value="'.strval(trim(Tools::getValue('separator'))).'">
			<input type="hidden" name="multiple_value_separator" value="'.strval(trim(Tools::getValue('multiple_value_separator'))).'">
			<script type="text/javascript">
				var current = 0;
				function showTable(nb)
				{
					getE(\'btn_left\').disabled = null;
					getE(\'btn_right\').disabled = null;
					if (nb <= 0)
					{
						nb = 0;
						getE(\'btn_left\').disabled = \'true\';
					}
					if (nb >= '.$nb_table.' - 1)
					{
						nb = '.$nb_table.' - 1;
						getE(\'btn_right\').disabled = \'true\';
					}
					toggle(getE(\'table\'+current), false);
					current = nb;
					toggle(getE(\'table\'+current), true);
				}
			</script>
			<div style="text-align:center; margin-bottom :20px;">
				<input name="import" type="submit" onclick="return (validateImportation(new Array('.implode(',', $res).')));" id="import" value="'.$this->l('Import CSV data').'" class="button" />
			</div>';
		ob_flush();
		echo '
			<table>
				<tr>
					<td valign="top" align="center"><input id="btn_left" value="'.$this->l('<<').'" type="button" class="button" onclick="showTable(current - 1)" /></td>
					<td align="left">';
		for ($i = 0; $i < $nb_table; $i++)
			$this->generateContentTable($i, $nb_table, $nb_column, $handle, $glue);
		echo '		</td>
					<td valign="top" align="center"><input id="btn_right" value="'.$this->l('>>').'" type="button" class="button" onclick="showTable(current + 1)" /></td>
				</tr>
			</table>
			<script type="text/javascript">showTable(current);</script>
			<div style="text-align:center; margin-top:10px;">
				<input name="import" type="submit" onclick="return (validateImportation(new Array('.implode(',', $res).')));" id="import" value="'.$this->l('Import CSV data').'" class="button" />
			</div>
		</form>';
	}

	private function truncateTables($case)
	{
		switch ((int)($case))
		{
			case $this->entities[$this->l('Categories')]:
				Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'category` WHERE id_category != 1');
				Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'category_lang` WHERE id_category != 1');
				Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'category` AUTO_INCREMENT = 2');
				foreach (scandir(_PS_CAT_IMG_DIR_) AS $d)
					if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d))
						unlink(_PS_CAT_IMG_DIR_.$d);
				break;
			case $this->entities[$this->l('Products')]:
				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'product');
				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'feature_product');
				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_lang');
				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'category_product');
				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_tag');
				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'image');
				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'image_lang');
				foreach (scandir(_PS_PROD_IMG_DIR_) AS $d)
					if (preg_match('/^[0-9]+(\-[0-9]+\-(.*))?\.jpg$/', $d) OR preg_match('/^[0-9]+\-[0-9]+\.jpg$/', $d))
						unlink(_PS_PROD_IMG_DIR_.$d);
				break;
			case $this->entities[$this->l('Customers')]:
				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'customer');
				break;
			case $this->entities[$this->l('Addresses')]:
				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'address');
				break;
			case $this->entities[$this->l('Combinations')]:
				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_impact');
				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute`');
				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_combination`');
				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_group`');
				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_group_lang`');
				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute`');
				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_lang`');
				break;
			case $this->entities[$this->l('Manufacturers')]:
				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'manufacturer');
				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'manufacturer_lang');
				foreach (scandir(_PS_MANU_IMG_DIR_) AS $d)
					if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d))
						unlink(_PS_MANU_IMG_DIR_.$d);
				break;
			case $this->entities[$this->l('Suppliers')]:
				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'supplier');
				Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'supplier_lang');
				foreach (scandir(_PS_SUPP_IMG_DIR_) AS $d)
					if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d))
						unlink(_PS_SUPP_IMG_DIR_.$d);
				break;
		}
		return true;
	}

	public function postProcess()
	{
		global $currentIndex;

		if (Tools::isSubmit('submitFileUpload'))
		{
			if (!isset($_FILES['file']['tmp_name']) OR empty($_FILES['file']['tmp_name']))
				$this->_errors[] = $this->l('no file selected');
			elseif (!file_exists($_FILES['file']['tmp_name']) OR !@rename($_FILES['file']['tmp_name'], dirname(__FILE__).'/../import/'.$_FILES['file']['name'].'.'.date('Ymdhis')))
				$this->_errors[] = $this->l('an error occurred while uploading and copying file');
			else
				Tools::redirectAdmin($currentIndex.'&token='.Tools::getValue('token').'&conf=18');
		}
		elseif (Tools::isSubmit('submitImportFile'))
			$this->displayCSV();
		elseif (Tools::getValue('import'))
		{
			if (Tools::getValue('truncate'))
				$this->truncateTables((int)(Tools::getValue('entity')));

			switch ((int)(Tools::getValue('entity')))
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
				default:
					$this->_errors[] = $this->l('no entity selected');
			}
		}

		parent::postProcess();
	}

	public static function setLocale()
	{
		$iso_lang  = trim(Tools::getValue('iso_lang'));
		setlocale(LC_COLLATE, strtolower($iso_lang).'_'.strtoupper($iso_lang).'.UTF-8');
		setlocale(LC_CTYPE, strtolower($iso_lang).'_'.strtoupper($iso_lang).'.UTF-8');
	}

	protected function _addProductWarning($product_name, $product_id = NULL, $message = '')
	{
		$this->_warnings[] = $product_name.(isset($product_id) ? ' (ID '.$product_id.')' : '').' '.Tools::displayError($message);
	}
}
