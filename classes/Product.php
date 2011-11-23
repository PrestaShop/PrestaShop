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
*  @version  Release: $Revision: 7506 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @deprecated 1.5.0.1
 */
define('_CUSTOMIZE_FILE_', 0);
/**
 * @deprecated 1.5.0.1
 */
define('_CUSTOMIZE_TEXTFIELD_', 1);

class ProductCore extends ObjectModel
{
	/** @var string Tax name */
	public $tax_name;

	/** @var string Tax rate */
	public $tax_rate;

	/** @var string Tax rules group */
	public $id_tax_rules_group;

	/** @var integer Manufacturer id */
	public $id_manufacturer;

	/** @var integer Supplier id */
	public $id_supplier;

	/** @var integer default Category id */
	public $id_category_default;

	/** @var string Manufacturer name */
	public $manufacturer_name;

	/** @var string Supplier name */
	public $supplier_name;

	/** @var string Name */
	public $name;

	/** @var string Long description */
	public $description;

	/** @var string Short description */
	public $description_short;

	/** @var integer Quantity available */
	public $quantity = 0;

	/** @var integer Minimal quantity for add to cart */
	public $minimal_quantity = 1;

	/** @var string available_now */
	public $available_now;

	/** @var string available_later */
	public $available_later;

	/** @var float Price in euros */
	public $price = 0;

	/** @var float Additional shipping cost */
	public $additional_shipping_cost = 0;

	/** @var float Wholesale Price in euros */
	public $wholesale_price = 0;

	/** @var boolean on_sale */
	public $on_sale = false;

	/** @var boolean online_only */
	public $online_only = false;

	/** @var string unity */
	public $unity = null;

		/** @var float price for product's unity */
	public $unit_price;

		/** @var float price for product's unity ratio */
	public $unit_price_ratio = 0;

	/** @var float Ecotax */
	public $ecotax = 0;

	/** @var string Reference */
	public $reference;

	/** @var string Supplier Reference */
	public $supplier_reference;

	/** @var string Location */
	public $location;

	/** @var string Width in default width unit */
	public $width = 0;

	/** @var string Height in default height unit */
	public $height = 0;

	/** @var string Depth in default depth unit */
	public $depth = 0;

	/** @var string Weight in default weight unit */
	public $weight = 0;

	/** @var string Ean-13 barcode */
	public $ean13;

	/** @var string Upc barcode */
	public $upc;

	/** @var string Friendly URL */
	public $link_rewrite;

	/** @var string Meta tag description */
	public $meta_description;

	/** @var string Meta tag keywords */
	public $meta_keywords;

	/** @var string Meta tag title */
	public $meta_title;

	/** @var boolean Product statuts */
	public $quantity_discount = 0;

	/** @var boolean Product customization */
	public $customizable;

	/** @var boolean Product is new */
	public $new = null;

	/** @var integer Number of uploadable files (concerning customizable products) */
	public $uploadable_files;

	/** @var interger Number of text fields */
	public $text_fields;

	/** @var boolean Product statuts */
	public $active = 1;

	/** @var boolean Product available for order */
	public $available_for_order = 1;

	/** @var string Object available order date */
	public $available_date;

	/** @var enum Product condition (new, used, refurbished) */
	public $condition;

	/** @var boolean Show price of Product */
	public $show_price = 1;

	public $indexed = 0;

	/** @var string Object creation date */
	public $date_add;

	/** @var string Object last modification date */
	public $date_upd;

	/*** @var array Tags */
	public $tags;

	public $isFullyLoaded = false;

	protected $langMultiShop = true;

	public $cache_is_pack;
	public $cache_has_attachments;
	public $is_virtual;
	public $cache_default_attribute;

	public static $_taxCalculationMethod = PS_TAX_EXC;
	protected static $_prices = array();
	protected static $_pricesLevel2 = array();
	protected static $_incat = array();
	protected static $_cart_quantity = array();
	protected static $_tax_rules_group = array();
	protected static $_cacheFeatures = array();
	protected static $_frontFeaturesCache = array();
	protected static $producPropertiesCache = array();

	/** @var array cache stock data in getStock() method */
	protected static $cacheStock = array();

	/** @var array tables */
	protected $tables = array ('product', 'product_lang');

	protected $fieldsRequired = array('price'); // quantity removed
	protected $fieldsSize = array('reference' => 32, 'supplier_reference' => 32, 'location' => 64, 'ean13' => 13, 'upc' => 12, 'unity' => 10);
	protected $fieldsValidate = array(
		'id_tax_rules_group' => 'isUnsignedId',
		'id_manufacturer' => 'isUnsignedId',
		'id_supplier' => 'isUnsignedId',
		'id_category_default' => 'isUnsignedId',
		'minimal_quantity' => 'isUnsignedInt',
		'price' => 'isPrice',
		'additional_shipping_cost' => 'isPrice',
		'wholesale_price' => 'isPrice',
		'on_sale' => 'isBool',
		'online_only' => 'isBool',
		'ecotax' => 'isPrice',
		'unit_price' => 'isPrice',
		'unity' => 'isString',
		'reference' => 'isReference',
		'supplier_reference' => 'isReference',
		'location' => 'isReference',
		'width' => 'isUnsignedFloat',
		'height' => 'isUnsignedFloat',
		'depth' => 'isUnsignedFloat',
		'weight' => 'isUnsignedFloat',
		'quantity_discount' => 'isBool',
		'customizable' => 'isUnsignedInt',
		'uploadable_files' => 'isUnsignedInt',
		'text_fields' => 'isUnsignedInt',
		'active' => 'isBool',
		'available_for_order' => 'isBool',
		'available_date' => 'isDateFormat',
		'condition' => 'isGenericName',
		'show_price' => 'isBool',
		'ean13' => 'isEan13',
		'upc' => 'isUpc',
		'indexed' => 'isBool',
		'cache_is_pack' => 'isBool',
		'is_virtual' => 'isBool',
		'cache_has_attachments' => 'isBool'
	);
	protected $fieldsRequiredLang = array('link_rewrite', 'name');
	/* Description short is limited to 800 chars (but can be configured in Preferences Tab), but without html, so it can't be generic */
	protected $fieldsSizeLang = array('meta_description' => 255, 'meta_keywords' => 255,
		'meta_title' => 128, 'link_rewrite' => 128, 'name' => 128, 'available_now' => 255, 'available_later' => 255);
	protected $fieldsValidateLang = array(
		'meta_description' => 'isGenericName', 'meta_keywords' => 'isGenericName',
		'meta_title' => 'isGenericName', 'link_rewrite' => 'isLinkRewrite', 'name' => 'isCatalogName',
		'description' => 'isString', 'description_short' => 'isString', 'available_now' => 'isGenericName', 'available_later' => 'IsGenericName');

	protected $table = 'product';
	protected $identifier = 'id_product';

	protected $webserviceParameters = array(
		'objectMethods' => array('add' => 'addWs', 'update' => 'updateWs'),
		'objectNodeNames' => 'products',
		'fields' => array(
			'id_manufacturer' => array('xlink_resource' => 'manufacturers'),
			'id_supplier' => array('xlink_resource' => 'suppliers'),
			'id_category_default' => array('xlink_resource' => 'categories'),
			'new' => array(),
			'cache_default_attribute' => array(),
			'id_default_image' => array('getter' => 'getCoverWs', 'setter' => 'setCoverWs', 'xlink_resource' => array('resourceName' => 'images', 'subResourceName' => 'products')),
			'id_default_combination' => array('getter' => 'getWsDefaultCombination', 'setter' => 'setWsDefaultCombination', 'xlink_resource' => array('resourceName' => 'combinations')),
			'position_in_category' => array('getter' => 'getWsPositionInCategory', 'setter' => false),
			'manufacturer_name' => array('getter' => 'getWsManufacturerName', 'setter' => false),
			),
		'associations' => array(
			'categories' => array('resource' => 'category', 'fields' => array(
				'id' => array('required' => true),
			)),
			'images' => array('resource' => 'image', 'fields' => array('id' => array())
			),
			'combinations' => array('resource' => 'combinations', 'fields' => array(
				'id' => array('required' => true),
			)),
			'product_option_values' => array('resource' => 'product_options_values', 'fields' => array(
				'id' => array('required' => true),
			)),
			'product_features' => array('resource' => 'product_feature',
				'fields' => array(
					'id' => array('required' => true),
					'id_feature_value' => array('required' => true, 'xlink_resource' => 'product_feature_values'),
			)),
			'tags' => array('resource' => 'tag',
				'fields' => array(
					'id' => array('required' => true),
			)),
		),
	);

	const CUSTOMIZE_FILE = 0;
	const CUSTOMIZE_TEXTFIELD = 1;

	public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, Context $context = null)
	{
		parent::__construct($id_product, $id_lang, $id_shop);
		if (!$context)
			$context = Context::getContext();

		if ($full && $this->id)
		{
			$this->isFullyLoaded = $full;
			$this->tax_name = 'deprecated'; // The applicable tax may be BOTH the product one AND the state one (moreover this variable is some deadcode)
			$this->manufacturer_name = Manufacturer::getNameById((int)$this->id_manufacturer);
			$this->supplier_name = Supplier::getNameById((int)$this->id_supplier);
			self::$_tax_rules_group[$this->id] = $this->id_tax_rules_group;

			$address = null;
			if (is_object($context->cart) && $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')} != null)
				$address = $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};

			$this->tax_rate = $this->getTaxesRate(new Address($address));

			$this->new = $this->isNew();
			$this->price = Product::getPriceStatic((int)$this->id, false, null, 6, null, false, true, 1, false, null, null, null, $this->specificPrice);
			$this->unit_price = ($this->unit_price_ratio != 0  ? $this->price / $this->unit_price_ratio : 0);
			if ($this->id)
				$this->tags = Tag::getProductTags((int)$this->id);
		}

		// By default, the product quantity correspond to the available quantity to sell in the current shop
		$this->quantity = StockAvailable::getQuantityAvailableByProduct($id_product, 0, Context::getContext()->shop->getID());
		$this->out_of_stock = StockAvailable::outOfStock($this->id);
		$this->depends_on_stock = StockAvailable::dependsOnStock($this->id);

		if ($this->id_category_default)
			$this->category = Category::getLinkRewrite((int)$this->id_category_default, (int)$id_lang);
	}

	public function getFields()
	{
		$this->validateFields();
		if (isset($this->id))
			$fields['id_product'] = (int)$this->id;
		$fields['id_tax_rules_group'] = (int)$this->id_tax_rules_group;
		$fields['id_manufacturer'] = (int)$this->id_manufacturer;
		$fields['id_supplier'] = (int)$this->id_supplier;
		$fields['id_category_default'] = (int)$this->id_category_default;
		$fields['quantity'] = (int)$this->quantity;
		$fields['minimal_quantity'] = (int)$this->minimal_quantity;
		$fields['price'] = (float)$this->price;
		$fields['additional_shipping_cost'] = (float)$this->additional_shipping_cost;
		$fields['wholesale_price'] = (float)$this->wholesale_price;
		$fields['on_sale'] = (int)$this->on_sale;
		$fields['online_only'] = (int)$this->online_only;
		$fields['ecotax'] = (float)$this->ecotax;
		$fields['unity'] = pSQL($this->unity);
		$fields['unit_price_ratio'] = (float)$this->unit_price > 0 ? $this->price / $this->unit_price : 0;
		$fields['ean13'] = pSQL($this->ean13);
		$fields['upc'] = pSQL($this->upc);
		$fields['reference'] = pSQL($this->reference);
		$fields['supplier_reference'] = pSQL($this->supplier_reference);
		$fields['location'] = pSQL($this->location);
		$fields['width'] = (float)$this->width;
		$fields['height'] = (float)$this->height;
		$fields['depth'] = (float)$this->depth;
		$fields['weight'] = (float)$this->weight;
		$fields['quantity_discount'] = (int)$this->quantity_discount;
		$fields['customizable'] = (int)$this->customizable;
		$fields['uploadable_files'] = (int)$this->uploadable_files;
		$fields['text_fields'] = (int)$this->text_fields;
		$fields['active'] = (int)$this->active;
		$fields['available_for_order'] = (int)$this->available_for_order;
		$fields['available_date'] = pSQL($this->available_date);
		$fields['condition'] = pSQL($this->condition);
		$fields['show_price'] = (int)$this->show_price;
		$fields['indexed'] = 0; // Reset indexation every times
		$fields['cache_is_pack'] = (int)$this->cache_is_pack;
		$fields['cache_has_attachments'] = (int)$this->cache_has_attachments;
		$fields['is_virtual'] = (int)$this->is_virtual;
		$fields['cache_default_attribute'] = (int)$this->cache_default_attribute;
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);

		return $fields;
	}

	public function add($autodate = true, $nullValues = false)
	{
		if (!parent::add($autodate, $nullValues))
			return false;
		Hook::exec('afterSaveProduct', array('id_product' => $this->id));
		return true;
	}

	public function update($nullValues = false)
	{
		$return = parent::update($nullValues);
		Hook::exec('afterSaveProduct', array('id_product' => $this->id));
		return $return;
	}

	/**
	* Check then return multilingual fields for database interaction
	*
	* @return array Multilingual fields
	*/
	public function getTranslationsFieldsChild()
	{
		$this->validateFieldsLang();
		return $this->getTranslationsFields(array(
			'meta_description',
			'meta_keywords',
			'meta_title',
			'link_rewrite',
			'name',
			'available_now',
			'available_later',
			'description' => array('html' => true),
			'description_short' => array('html' => true),
		));
	}

	public static function initPricesComputation($id_customer = null)
	{
		if ($id_customer)
		{
			$customer = new Customer((int)$id_customer);
			if (!Validate::isLoadedObject($customer))
				die(Tools::displayError());
			self::$_taxCalculationMethod = Group::getPriceDisplayMethod((int)$customer->id_default_group);
		}
		else if (Validate::isLoadedObject(Context::getContext()->customer))
			self::$_taxCalculationMethod = Group::getPriceDisplayMethod(Context::getContext()->customer->id_default_group);
		else
			self::$_taxCalculationMethod = Group::getDefaultPriceDisplayMethod();
	}

	public static function getTaxCalculationMethod($id_customer = null)
	{
		if ($id_customer)
			self::initPricesComputation((int)$id_customer);
		return (int)self::$_taxCalculationMethod;
	}

	/**
	 * Move a product inside its category
	 * @param boolean $way Up (1)  or Down (0)
	 * @param integer $position
	 * return boolean Update result
	 */
	public function updatePosition($way, $position)
	{
		if (!$res = Db::getInstance()->executeS('
			SELECT cp.`id_product`, cp.`position`, cp.`id_category`
			FROM `'._DB_PREFIX_.'category_product` cp
			WHERE cp.`id_category` = '.(int)Tools::getValue('id_category', 1).'
			ORDER BY cp.`position` ASC'
		))
			return false;

		foreach ($res as $product)
			if ((int)$product['id_product'] == (int)$this->id)
				$movedProduct = $product;

		if (!isset($movedProduct) || !isset($position))
			return false;

		// < and > statements rather than BETWEEN operator
		// since BETWEEN is treated differently according to databases
		return (Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'category_product`
			SET `position`= `position` '.($way ? '- 1' : '+ 1').'
			WHERE `position`
			'.($way
				? '> '.(int)$movedProduct['position'].' AND `position` <= '.(int)$position
				: '< '.(int)$movedProduct['position'].' AND `position` >= '.(int)$position).'
			AND `id_category`='.(int)$movedProduct['id_category'])
		&& Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'category_product`
			SET `position` = '.(int)$position.'
			WHERE `id_product` = '.(int)$movedProduct['id_product'].'
			AND `id_category`='.(int)$movedProduct['id_category']));
	}

	/*
	 * Reorder product position in category $id_category.
	 * Call it after deleting a product from a category.
	 *
	 * @param int $id_category
	 */
	public static function cleanPositions($id_category)
	{
		$return = true;

		$result = Db::getInstance()->executeS('
		SELECT `id_product`
		FROM `'._DB_PREFIX_.'category_product`
		WHERE `id_category` = '.(int)$id_category.'
		ORDER BY `position`');
		$sizeof = count($result);

		for ($i = 0; $i < $sizeof; $i++)
			$return &= Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'category_product`
			SET `position` = '.(int)$i.'
			WHERE `id_category` = '.(int)$id_category.'
			AND `id_product` = '.(int)$result[$i]['id_product']);
		return $return;
	}

	/**
	* Get the default attribute for a product
	*
	* @return int Attributes list
	*/
	public static function getDefaultAttribute($id_product, $minimumQuantity = 0)
	{
		if (!Combination::isFeatureActive())
			return 0;

		$sql = 'SELECT pa.id_product_attribute
				FROM '._DB_PREFIX_.'product_attribute pa'
				.($minimumQuantity > 0 ? Product::sqlStock('pa', 'pa') : '').
				' WHERE pa.default_on = 1 '
				.($minimumQuantity > 0 ? ' AND stock.quantity >= '.(int)$minimumQuantity : '').
				' AND pa.id_product = '.(int)$id_product;
		$result = Db::getInstance()->getValue($sql);

		if (!$result)
		{
			$sql = 'SELECT pa.id_product_attribute
					FROM '._DB_PREFIX_.'product_attribute pa'
					.($minimumQuantity > 0 ? Product::sqlStock('pa', 'pa') : '').
					' WHERE pa.id_product = '.(int)$id_product
					.($minimumQuantity > 0 ? ' AND stock.quantity >= '.(int)$minimumQuantity : '');
			$result = Db::getInstance()->getValue($sql);
		}

		if (!$result)
		{
			$sql = 'SELECT id_product_attribute
					FROM '._DB_PREFIX_.'product_attribute
					WHERE `default_on` = 1
					AND id_product = '.(int)$id_product;
			$result = Db::getInstance()->getValue($sql);
		}

		if (!$result)
		{
			$sql = 'SELECT id_product_attribute
					FROM '._DB_PREFIX_.'product_attribute
					WHERE id_product = '.(int)$id_product;
			$result = Db::getInstance()->getValue($sql);
		}
		return $result;
	}

	public static function updateDefaultAttribute($id_product)
	{
		$id_product_attribute = self::getDefaultAttribute($id_product);
		Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product SET cache_default_attribute = '.(int)$id_product_attribute.' WHERE id_product = '.(int)$id_product.' LIMIT 1');
	}

	public static function updateIsVirtual($id_product)
	{
		Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product SET is_virtual = 1 WHERE id_product = '.(int)$id_product.' LIMIT 1');
	}

	public function validateFieldsLang($die = true, $errorReturn = false)
	{
		$limit = (int)Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT');
		if ($limit <= 0)
			$limit = 800;
		if (!is_array($this->description_short))
			$this->description_short = array();
		foreach ($this->description_short as $k => $value)
			if (Tools::strlen(strip_tags($value)) > $limit)
			{
				if ($die) die (Tools::displayError().' ('.get_class($this).'->description_short: length > '.$limit.' for language '.$k.')');
				return $errorReturn ? get_class($this).'->'.Tools::displayError('description_short: length >').' '.$limit.' '.Tools::displayError('for language').' '.$k : false;
			}
		return parent::validateFieldsLang($die, $errorReturn);
	}

	public function delete()
	{
		/*
		 * @since 1.5.0
		 * It is NOT possible to delete a product if there are currently:
		 * - physical stock for this product
		 * - supply order(s) for this product
		 */
		$stock_manager = StockManagerFactory::getManager();
		$physical_quantity = $stock_manager->getProductPhysicalQuantities($this->id, 0);
		$real_quantity = $stock_manager->getProductRealQuantities($this->id, 0);
		if ($physical_quantity > 0)
			return false;
		if ($real_quantity > $physical_quantity)
			return false;

		/*
		 * @since 1.5.0
		 * Removes the product from StockAvailable, for the current shop
		 */
		$context = Context::getContext();
		$id_shop = $context->shop->getID();
		StockAvailable::removeProductFromStockAvailable($this->id, null, $id_shop);

		if (!GroupReduction::deleteProductReduction($this->id))
			return false;

		Hook::exec('deleteProduct', array('product' => $this));

		if (!parent::delete() ||
			!$this->deleteCategories(true) ||
			!$this->deleteImages() ||
			!$this->deleteProductAttributes() ||
			!$this->deleteProductFeatures() ||
			!$this->deleteTags() ||
			!$this->deleteCartProducts() ||
			!$this->deleteAttributesImpacts() ||
			!$this->deleteAttachments() ||
			!$this->deleteCustomization() ||
			!SpecificPrice::deleteByProductId((int)$this->id) ||
			!$this->deletePack() ||
			!$this->deleteProductSale() ||
			!$this->deleteSceneProducts() ||
			!$this->deleteSearchIndexes() ||
			!$this->deleteAccessories() ||
			!$this->deleteFromAccessories())
		return false;

		if ($id = ProductDownload::getIdFromIdProduct($this->id))
			if ($product_download = new ProductDownload($id) && !$product_download->delete(true))
				return false;

		return true;
	}

	public function deleteSelection($products)
	{
		$return = 1;
		foreach ($products as $id_product)
		{
			$product = new Product((int)$id_product);
			$return &= $product->delete();
		}
		return $return;
	}

	/**
	 * addToCategories add this product to the category/ies if not exists.
	 *
	 * @param mixed $categories id_category or array of id_category
	 * @return boolean true if succeed
	 */
	public function addToCategories($categories = array())
	{
		if (empty($categories))
			return false;

		if (!is_array($categories))
			$categories = array($categories);

		if (!count($categories))
			return false;

		$currentCategories = $this->getCategories();

		// for new categ, put product at last position
		$resCategNewPos = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT id_category, MAX(position)+1 newPos
			FROM `'._DB_PREFIX_.'category_product`
			WHERE `id_category` IN('.implode(',', array_map('intval', $categories)).')
			GROUP BY id_category');
		foreach ($resCategNewPos as $array)
			$newCategories[$array['id_category']] = $array['newPos'];

		$newCategPos = array();
		foreach ($categories as $id_category)
			$newCategPos[$id_category] = isset($newCategories[$id_category])?$newCategories[$id_category]:0;

		$productCats = array();

		foreach ($categories as $newIdCateg)
			if (!in_array($newIdCateg, $currentCategories))
				$productCats[] = '('.$newIdCateg.', '.$this->id.', '.$newCategPos[$newIdCateg].')';
		if (count($productCats))
			return Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'category_product` (`id_category`, `id_product`, `position`)
			VALUES '.implode(',', $productCats));

		return true;
	}

	/**
	* Update categories to index product into
	*
	* @param string $productCategories Categories list to index product into
	* @param boolean keepingCurrentPos (deprecated, no more used)
	* @return array Update/insertion result
	*/
	public function updateCategories($categories, $keepingCurrentPos = false)
	{
		if (empty($categories))
			return false;

		// get max position in each categories
		$result = Db::getInstance()->executeS('SELECT `id_category`
				FROM `'._DB_PREFIX_.'category_product`
				WHERE `id_category` NOT IN('.implode(',', array_map('intval', $categories)).')
				AND id_product = '.$this->id.'');
		foreach ($result as $categToDelete)
			$this->deleteCategory($categToDelete['id_category']);
		// if none are found, it's an error
		if (!is_array($result))
			return (false);

		if (!$this->addToCategories($categories))
			return false;

		if (!$this->setGroupReduction())
			return false;

		return true;
	}

	/**
	 * deleteCategory delete this product from the category $id_category
	 *
	 * @param mixed $id_category
	 * @param mixed $cleanPositions
	 * @return boolean
	 */
	public function deleteCategory($id_category, $cleanPositions = true)
	{
		$result = Db::getInstance()->executeS('SELECT `id_category` FROM `'._DB_PREFIX_.'category_product` WHERE `id_product` = '.(int)$this->id.' AND id_category = '.(int)$id_category.'');
		$return = Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'category_product` WHERE `id_product` = '.(int)$this->id.' AND id_category = '.(int)$id_category.'');
		if ($cleanPositions === true)
			foreach ($result as $row)
				$this->cleanPositions((int)$row['id_category']);
		return $return;
	}

	/**
	* Delete all association to category where product is indexed
	*
	* @param boolean $cleanPositions clean category positions after deletion
	* @return array Deletion result
	*/
	public function deleteCategories($cleanPositions = false)
	{
		$return = Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'category_product` WHERE `id_product` = '.(int)$this->id);
		if ($cleanPositions === true)
		{
			$result = Db::getInstance()->executeS('SELECT `id_category` FROM `'._DB_PREFIX_.'category_product` WHERE `id_product` = '.(int)$this->id);
			foreach ($result as $row)
				$this->cleanPositions((int)$row['id_category']);
		}
		return $return;
	}

	/**
	* Delete products tags entries
	*
	* @return array Deletion result
	*/
	public function deleteTags()
	{
		return (Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_tag` WHERE `id_product` = '.(int)$this->id)
		&& Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'tag` WHERE `id_tag` NOT IN (SELECT `id_tag` FROM `'._DB_PREFIX_.'product_tag`)'));
	}

	/**
	* Delete product from cart
	*
	* @return array Deletion result
	*/
	public function deleteCartProducts()
	{
		return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_product` WHERE `id_product` = '.(int)$this->id);
	}

	/**
	* Delete product images from database
	*
	* @return bool success
	*/
	public function deleteImages()
	{
		$result = Db::getInstance()->executeS('
		SELECT `id_image`
		FROM `'._DB_PREFIX_.'image`
		WHERE `id_product` = '.(int)$this->id);

		$status = true;
		if ($result)
		foreach ($result as $row)
		{
			$image = new Image($row['id_image']);
			$status &= $image->delete();
		}
		return $status;
	}

	public static function getProductAttributePrice($id_product_attribute)
	{
		$rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `price`
		FROM `'._DB_PREFIX_.'product_attribute`
		WHERE `id_product_attribute` = '.(int)$id_product_attribute);
		return $rq['price'];
	}

	/**
	* Get all available products
	*
	* @param integer $id_lang Language id
	* @param integer $start Start number
	* @param integer $limit Number of products to return
	* @param string $orderBy Field for ordering
	* @param string $orderWay Way for ordering (ASC or DESC)
	* @return array Products details
	*/
	public static function getProducts($id_lang, $start, $limit, $orderBy, $orderWay, $id_category = false, $only_active = false, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();

		if (!Validate::isOrderBy($orderBy) || !Validate::isOrderWay($orderWay))
			die (Tools::displayError());
		if ($orderBy == 'id_product' ||	$orderBy == 'price' ||	$orderBy == 'date_add')
			$orderByPrefix = 'p';
		else if ($orderBy == 'name')
			$orderByPrefix = 'pl';
		else if ($orderBy == 'position')
			$orderByPrefix = 'c';

		$sql = 'SELECT p.*, pl.* , t.`rate` AS tax_rate, m.`name` AS manufacturer_name, s.`name` AS supplier_name
				FROM `'._DB_PREFIX_.'product` p
				'.$context->shop->addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.$context->shop->addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group`
		 		  AND tr.`id_country` = '.(int)Context::getContext()->country->id.'
		 		  AND tr.`id_state` = 0)
	  		 	LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)'.
				($id_category ? 'LEFT JOIN `'._DB_PREFIX_.'category_product` c ON (c.`id_product` = p.`id_product`)' : '').'
				WHERE pl.`id_lang` = '.(int)$id_lang.
					($id_category ? ' AND c.`id_category` = '.(int)$id_category : '').
					($only_active ? ' AND p.`active` = 1' : '').'
				ORDER BY '.(isset($orderByPrefix) ? pSQL($orderByPrefix).'.' : '').'`'.pSQL($orderBy).'` '.pSQL($orderWay).
				($limit > 0 ? ' LIMIT '.(int)$start.','.(int)$limit : '');
		$rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		if ($orderBy == 'price')
			Tools::orderbyPrice($rq, $orderWay);
		return ($rq);
	}

	public static function getSimpleProducts($id_lang, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();

		$sql = 'SELECT p.`id_product`, pl.`name`
				FROM `'._DB_PREFIX_.'product` p
				'.$context->shop->addSqlAssociation('product', 'p', false).'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.$context->shop->addSqlRestrictionOnLang('pl').')
				WHERE pl.`id_lang` = '.(int)$id_lang.'
				ORDER BY pl.`name`';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
	}

	public function isNew()
	{
		$result = Db::getInstance()->executeS('
			SELECT id_product FROM `'._DB_PREFIX_.'product` p
			WHERE 1
			AND id_product = '.(int)$this->id.'
			AND DATEDIFF(p.`date_add`,
			DATE_SUB(NOW(), INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY)) > 0
		');
		return count($result) > 0;
	}

	public function productAttributeExists($attributesList, $currentProductAttribute = false)
	{
		if (!Combination::isFeatureActive())
			return false;
		$result = Db::getInstance()->executeS('SELECT pac.`id_attribute`, pac.`id_product_attribute`
		FROM `'._DB_PREFIX_.'product_attribute` pa
		LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
		WHERE pa.`id_product` = '.(int)($this->id));
		/* If something's wrong */
		if (!$result || empty($result))
			return false;
		/* Product attributes simulation */
		$productAttributes = array();
		foreach ($result as $productAttribute)
			$productAttributes[$productAttribute['id_product_attribute']][] = $productAttribute['id_attribute'];
		/* Checking product's attribute existence */
		foreach ($productAttributes as $key => $productAttribute)
			if (count($productAttribute) == count($attributesList))
			{
				$diff = false;
				for ($i = 0; $diff == false && isset($productAttribute[$i]); $i++)
					if (!in_array($productAttribute[$i], $attributesList) || $key == $currentProductAttribute)
						$diff = true;
				if (!$diff)
					return true;
			}
		return false;
	}

	/**
	 * addProductAttribute is deprecated
	 *
	 * The quantity params now set StockAvailable for the current shop with the specified quantity
	 * The supplier_reference params now set the supplier reference of the default supplier of the product if possible
	 *
	 * @see StockManager if you want to manage real stock
	 * @see StockAvailable if you want to manage available quantities for sale on your shop(s)
	 * @see ProductSupplier for manage supplier reference(s)
	 *
	 * @deprecated
	 */
	public function addProductAttribute($price, $weight, $unit_impact, $ecotax, $quantity, $id_images, $reference,
		$supplier_reference = null, $ean13, $default, $location = null, $upc = null, $minimal_quantity = 1)
	{
		Tools::displayAsDeprecated();

		$id_product_attribute = $this->addAttribute(
			$price, $weight, $unit_impact, $ecotax, $id_images,
			$reference, $ean13, $default, $location, $upc, $minimal_quantity
		);

		if (!$id_product_attribute)
			return false;

		//Try to set available quantitiy if product quantity not depend on stock
		$depends_on_stock = StockAvailable::dependsOnStock($this->id);

		if (!$depends_on_stock)
			if (!StockAvailable::updateQuantity($this->id, $id_product_attribute, $quantity))
			{
				$stock_available = new StockAvailable();
				$stock_available->id_product = (int)$this->id;
				$stock_available->id_product_attribute = (int)$id_product_attribute;
				$stock_available->id_shop = (int)$context->shop->getID();
				$stock_available->quantity = (int)$quantity;
				$stock_available->out_of_stock = StockAvailable::outOfStock($this->id);
				$stock_available->depends_on_stock = 0;
				$stock_available->save();
			}

		//Try to set the default supplier reference
		if ($this->id_supplier > 0 && $supplier_reference != null)
		{
			$id_product_supplier = ProductSupplier::getIdByProductAndSupplier($this->id, $id_product_attribute, $this->id_supplier);

			if (empty($id_product_supplier))
			{
				//create new record
				$product_supplier_entity = new ProductSupplier();
				$product_supplier_entity->id_product = $this->id;
				$product_supplier_entity->id_product_attribute = $id_product_attribute;
				$product_supplier_entity->id_supplier = $this->id_supplier;
				$product_supplier_entity->product_supplier_reference = pSQL($supplier_reference);
				$product_supplier_entity->save();
			}
			else
			{
				$product_supplier = new ProductSupplier($id_product_supplier);
				$product_supplier->product_supplier_reference = pSql($supplier_reference);
				$product_supplier->update();
			}
		}

		return $id_product_attribute;
	}

	/**
	* Add a product attribute
	* @since 1.5.0.1
	*
	* @param float $price Additional price
	* @param float $weight Additional weight
	* @param float $ecotax Additional ecotax
	* @param integer $id_images Image ids
	* @param string $reference Reference
	* @param string $location Location
	* @param string $ean13 Ean-13 barcode
	* @param boolean $default Is default attribute for product
	* @param integer $minimal_quantity Minimal quantity to add to cart
	* @return mixed $id_product_attribute or false
	*/
	public function addAttribute($price, $weight, $unit_impact, $ecotax, $id_images, $reference, $ean13, $default, $location = null, $upc = null, $minimal_quantity = 1)
	{
		if (!$this->id)
			return;

		$price = str_replace(',', '.', $price);
		$weight = str_replace(',', '.', $weight);

		Db::getInstance()->AutoExecute(_DB_PREFIX_.'product_attribute', array(
			'id_product' => (int)$this->id,
			'price' => (float)$price,
			'ecotax' => (float)$ecotax,
			'quantity' => 0,
			'weight' => ($weight ? (float)$weight : 0),
			'unit_price_impact' => ($unit_impact ? (float)$unit_impact : 0),
			'reference' => pSQL($reference),
			'location' => pSQL($location),
			'ean13' => pSQL($ean13),
			'upc' => pSQL($upc),
			'default_on' => (int)$default,
			'minimal_quantity' => (int)$minimal_quantity,
		), 'INSERT');

		$id_product_attribute = Db::getInstance()->Insert_ID();

		Product::updateDefaultAttribute($this->id);
		if (!$id_product_attribute)
			return false;

		if (empty($id_images))
			return (int)$id_product_attribute;
		$query = 'INSERT INTO `'._DB_PREFIX_.'product_attribute_image` (`id_product_attribute`, `id_image`) VALUES ';
		foreach ($id_images as $id_image)
			$query .= '('.(int)$id_product_attribute.', '.(int)$id_image.'), ';
		$query = trim($query, ', ');
		if (!Db::getInstance()->execute($query))
			return false;
		return (int)$id_product_attribute;
	}

	/**
	* @param integer $quantity DEPRECATED
	* @param string $supplier_reference DEPRECATED
	*/
	public function addCombinationEntity($wholesale_price, $price, $weight, $unit_impact, $ecotax, $quantity,
		$id_images, $reference, $supplier_reference, $ean13, $default, $location = null, $upc = null, $minimal_quantity = 1)
	{
		$id_product_attribute = $this->addProductAttribute(
			$price, $weight, $unit_impact, $ecotax, $quantity, $id_images,
			$reference, $supplier_reference, $ean13, $default, $location, $upc, $minimal_quantity
		);

		$result = Db::getInstance()->execute(
			'UPDATE `'._DB_PREFIX_.'product_attribute`
			SET `wholesale_price` = '.(float)$wholesale_price.'
			WHERE `id_product_attribute` = '.(int)$id_product_attribute
		);

		if (!$id_product_attribute || !$result)
			return false;

		return $id_product_attribute;
	}

	public function addProductAttributeMultiple($attributes, $setDefault = true)
	{
		$values = array();
		$keys = array();
		$fields = array();
		$default_value = 1;
		foreach ($attributes as &$attribute)
			foreach ($attribute as $key => $value)
				if ($value != '')
					$fields[$key] = $key;

		foreach ($attributes as &$attribute)
		{
			$k = array();
			$v = array();
			foreach ($attribute as $key => $value)
			{
				if (in_array($key, $fields))
				{
					$k[] = '`'.$key.'`';
					$v[] = '\''.$value.'\'';
				}
			}
			if ($setDefault)
			{
				$k[] = '`default_on`';
				$v[] = '\''.$default_value.'\'';
				$default_value = 0;
			}
			$values[] = '('.implode(', ', $v).')';
			$keys[] = implode(', ', $k);
		}
		Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'product_attribute` ('.$keys[0].') VALUES '.implode(', ', $values));

		return (array_map(create_function('$elem', 'return $elem[\'id_product_attribute\'];'),
			Db::getInstance()->executeS('
			SELECT `id_product_attribute` FROM `'._DB_PREFIX_.'product_attribute` WHERE `id_product_attribute` >= '.(int)(Db::getInstance()->Insert_ID())
			)));
	}

	/**
	* Del all default attributes for product
	*/
	public function deleteDefaultAttributes()
	{
		return Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.'product_attribute`
		SET `default_on` = 0
		WHERE `id_product` = '.(int)($this->id));
	}

	public function setDefaultAttribute($id_product_attribute)
	{
		return Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.'product_attribute`
		SET `default_on` = 1
		WHERE `id_product` = '.(int)$this->id.'
		AND `id_product_attribute` = '.(int)$id_product_attribute) &&
		Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.'product`
		SET `cache_default_attribute` = '.(int)$id_product_attribute.'
		WHERE `id_product` = '.(int)$this->id.' LIMIT 1');
	}

	/**
	* Update a product attribute
	*
	* @deprecated since 1.5
	* @see updateAttribute() to use instead
	* @see ProductSupplier for manage supplier reference(s)
	*
	*/
	public function updateProductAttribute($id_product_attribute, $wholesale_price, $price, $weight, $unit, $ecotax,
		$id_images, $reference, $supplier_reference = null, $ean13, $default, $location = null, $upc = null, $minimal_quantity, $available_date)
	{
		Tools::displayAsDeprecated();

		$return = $this->updateAttribute(
			$id_product_attribute, $wholesale_price, $price, $weight, $unit, $ecotax,
			$id_images, $reference, $ean13, $default, $location = null, $upc = null, $minimal_quantity, $available_date
		);

		//Try to set the default supplier reference
		if ($this->id_supplier > 0 && $supplier_reference != null)
		{
			$id_product_supplier = ProductSupplier::getIdByProductAndSupplier($this->id, $id_product_attribute, $this->id_supplier);

			if (empty($id_product_supplier))
			{
				//create new record
				$product_supplier_entity = new ProductSupplier();
				$product_supplier_entity->id_product = $this->id;
				$product_supplier_entity->id_product_attribute = $id_product_attribute;
				$product_supplier_entity->id_supplier = $this->id_supplier;
				$product_supplier_entity->product_supplier_reference = pSQL($supplier_reference);
				$product_supplier_entity->save();
			}
			else
			{
				$product_supplier = new ProductSupplier($id_product_supplier);
				$product_supplier->product_supplier_reference = pSql($supplier_reference);
				$product_supplier->update();
			}
		}
	}

	/**
	* Update a product attribute
	*
	* @param integer $id_product_attribute Product attribute id
	* @param float $wholesale_price Wholesale price
	* @param float $price Additional price
	* @param float $weight Additional weight
	* @param float $unit
	* @param float $ecotax Additional ecotax
	* @param integer $id_image Image id
	* @param string $reference Reference
	* @param string $ean13 Ean-13 barcode
	* @param int $default Default On
	* @param string $upc Upc barcode
	* @param string $minimal_quantity Minimal quantity
	* @return array Update result
	*/
	public function updateAttribute($id_product_attribute, $wholesale_price, $price, $weight, $unit, $ecotax,
		$id_images, $reference, $ean13, $default, $location = null, $upc = null, $minimal_quantity, $available_date)
	{
		Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'product_attribute_combination`
		WHERE `id_product_attribute` = '.(int)$id_product_attribute);

		$price = str_replace(',', '.', $price);
		$weight = str_replace(',', '.', $weight);
		$data = array(
			'wholesale_price' => (float)$wholesale_price,
			'price' => (float)$price,
			'ecotax' => (float)$ecotax,
			'weight' => ($weight ? (float)$weight : 0),
			'unit_price_impact' => ($unit ? (float)$unit : 0),
			'reference' => pSQL($reference),
			'location' => pSQL($location),
			'ean13' => pSQL($ean13),
			'upc' => pSQL($upc),
			'default_on' => (int)$default,
			'minimal_quantity' => (int)$minimal_quantity,
			'available_date' => pSQL($available_date)
		);

		$res1 = Db::getInstance()->AutoExecute(
			_DB_PREFIX_.'product_attribute',
			$data,
			'UPDATE',
			'`id_product_attribute` = '.(int)$id_product_attribute
		);

		$res2 = Db::getInstance()->execute(
			'DELETE FROM `'._DB_PREFIX_.'product_attribute_image`
			WHERE `id_product_attribute` = '.(int)$id_product_attribute
		);

		if (!$res1 || !$res2)
			return false;

		//if ($quantity)
			Hook::exec('updateProductAttribute', array('id_product_attribute' => $id_product_attribute));

		Product::updateDefaultAttribute($this->id);

		if (empty($id_images))
			return true;

		$query = 'INSERT INTO `'._DB_PREFIX_.'product_attribute_image` (`id_product_attribute`, `id_image`) VALUES ';

		foreach ($id_images as $id_image)
			$query .= '('.(int)$id_product_attribute.', '.(int)$id_image.'), ';
		$query = trim($query, ', ');

		return Db::getInstance()->execute($query);
	}

	/**
	 * @deprecated since 1.5.0
	 */
	public function updateQuantityProductWithAttributeQuantity()
	{
		Tools::displayAsDeprecated();

		return Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.'product`
		SET `quantity` = IFNULL(
		(
			SELECT SUM(`quantity`)
			FROM `'._DB_PREFIX_.'product_attribute`
			WHERE `id_product` = '.(int)$this->id.'
		), \'0\')
		WHERE `id_product` = '.(int)$this->id);
	}
	/**
	* Delete product attributes
	*
	* @return array Deletion result
	*/
	public function deleteProductAttributes()
	{
		Hook::exec('deleteProductAttribute', array('id_product_attribute' => 0, 'id_product' => $this->id, 'deleteAllAttributes' => true));

		$result = Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'product_attribute_combination`
		WHERE `id_product_attribute` IN (SELECT `id_product_attribute` FROM `'._DB_PREFIX_.'product_attribute` WHERE `id_product` = '.(int)($this->id).')');
		$result2 = Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_attribute` WHERE `id_product` = '.(int)($this->id));
		$result3 = Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_download` WHERE `id_product` = '.(int)($this->id));

		return ($result & $result2 & $result3);
	}

	/**
	* Delete product attributes impacts
	*
	* @return Deletion result
	*/
	public function deleteAttributesImpacts()
	{
		return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'attribute_impact` WHERE `id_product` = '.(int)($this->id));
	}

	/**
	* Delete product features
	*
	* @return array Deletion result
	*/
	public function deleteProductFeatures()
	{
		return $this->deleteFeatures();
	}

	/**
	* Delete product attachments
	*
	* @return array Deletion result
	*/
	public function deleteAttachments()
	{
		return Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'product_attachment`
			WHERE `id_product` = '.(int)$this->id);
	}

	/**
	* Delete product customizations
	*
	* @return array Deletion result
	*/
	public function deleteCustomization()
	{
		return
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customization_field` WHERE `id_product` = '.(int)($this->id)) &&
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customization_field_lang`
			WHERE `id_customization_field` NOT IN (SELECT id_customization_field FROM `'._DB_PREFIX_.'customization_field`)');
	}

	/**
	* Delete product pack details
	*
	* @return array Deletion result
	*/
	public function deletePack()
	{
		return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'pack` WHERE `id_product_pack` = '.(int)($this->id).' OR `id_product_item` = '.(int)($this->id));
	}

	/**
	* Delete product sales
	*
	* @return array Deletion result
	*/
	public function deleteProductSale()
	{
		return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_sale` WHERE `id_product` = '.(int)($this->id));
	}

	/**
	* Delete product in its scenes
	*
	* @return array Deletion result
	*/
	public function deleteSceneProducts()
	{
		return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'scene_products` WHERE `id_product` = '.(int)($this->id));
	}

	/**
	* Delete product indexed words
	*
	* @return array Deletion result
	*/
	public function deleteSearchIndexes()
	{
		return
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'search_index` WHERE `id_product` = '.(int)($this->id)) &&
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'search_word` WHERE `id_word` NOT IN (SELECT id_word FROM `'._DB_PREFIX_.'search_index`)');
	}

	/**
	* Add a product attributes combinaison
	*
	* @param integer $id_product_attribute Product attribute id
	* @param array $attributes Attributes to forge combinaison
	* @return array Insertion result
	*/
	public function addAttributeCombinaison($id_product_attribute, $attributes)
	{
		if (!is_array($attributes))
			die(Tools::displayError());
		if (!count($attributes))
			return false;
		$attributesList = '';
		foreach ($attributes as $id_attribute)
			$attributesList .= '('.(int)$id_product_attribute.','.(int)$id_attribute.'),';
		$attributesList = rtrim($attributesList, ',');

		if (!Validate::isValuesList($attributesList))
			die(Tools::displayError());

		$result = Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'product_attribute_combination` (`id_product_attribute`, `id_attribute`) VALUES '.$attributesList);
		return $result;
	}

	public function addAttributeCombinationMultiple($id_attributes, $combinations)
	{
		$attributesList = '';
		foreach ($id_attributes as $nb => $id_product_attribute)
			if (isset($combinations[$nb]))
				foreach ($combinations[$nb] as $id_attribute)
					$attributesList .= '('.(int)($id_product_attribute).','.(int)($id_attribute).'),';
		$attributesList = rtrim($attributesList, ',');
		return Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'product_attribute_combination` (`id_product_attribute`, `id_attribute`) VALUES '.$attributesList);
	}

	/**
	* Delete a product attributes combinaison
	*
	* @param integer $id_product_attribute Product attribute id
	* @return array Deletion result
	*/
	public function deleteAttributeCombinaison($id_product_attribute)
	{
		if (!$this->id || !$id_product_attribute || !is_numeric($id_product_attribute))
			return false;

		Hook::exec('deleteProductAttribute', array('id_product_attribute' => $id_product_attribute, 'id_product' => $this->id, 'deleteAllAttributes' => false));

		$sql = 'DELETE FROM `'._DB_PREFIX_.'product_attribute`
				WHERE `id_product_attribute` = '.$id_product_attribute.'
					AND `id_product` = '.$this->id;
		$result = Db::getInstance()->execute($sql);

		$sql = 'DELETE FROM `'._DB_PREFIX_.'product_attribute_combination`
				WHERE `id_product_attribute` = '.$id_product_attribute;
		$result2 = Db::getInstance()->execute($sql);

		$sql = 'DELETE FROM `'._DB_PREFIX_.'cart_product`
				WHERE `id_product_attribute` = '.$id_product_attribute;
		$result3 = Db::getInstance()->execute($sql);
		return ($result && $result2 && $result3);
	}

	/**
	* Delete features
	*
	*/
	public function deleteFeatures()
	{
		// List products features
		$features = Db::getInstance()->executeS('
		SELECT p.*, f.*
		FROM `'._DB_PREFIX_.'feature_product` as p
		LEFT JOIN `'._DB_PREFIX_.'feature_value` as f ON (f.`id_feature_value` = p.`id_feature_value`)
		WHERE `id_product` = '.(int)($this->id));
		foreach ($features as $tab)
			// Delete product custom features
			if ($tab['custom'])
			{
				Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'feature_value`
				WHERE `id_feature_value` = '.(int)($tab['id_feature_value']));
				Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'feature_value_lang`
				WHERE `id_feature_value` = '.(int)($tab['id_feature_value']));
			}
		// Delete product features
		$result = Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'feature_product`
		WHERE `id_product` = '.(int)($this->id));
		return ($result);
	}

	/**
	* Get all available product attributes resume
	*
	* @param integer $id_lang Language id
	* @return array Product attributes combinaisons
	*/
	public function getAttributesResume($id_lang, $attribute_value_separator = ' - ', $attribute_separator = ', ')
	{
		if (!Combination::isFeatureActive())
			return array();
		$sql = 'SELECT pa.*, GROUP_CONCAT(agl.`name`, \''.pSQL($attribute_value_separator).'\', al.`name` SEPARATOR \''.pSQL($attribute_separator).'\') as attribute_designation, stock.quantity
				FROM `'._DB_PREFIX_.'product_attribute` pa
				LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
				LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
				LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
				LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang.')
				'.Product::sqlStock('pa', 'pa').'
				WHERE pa.`id_product` = '.(int)$this->id.'
				GROUP BY pa.`id_product_attribute`';
		return Db::getInstance()->executeS($sql);
	}

	/**
	* Get all available product attributes combinaisons
	*
	* @param integer $id_lang Language id
	* @return array Product attributes combinaisons
	*/
	public function getAttributeCombinaisons($id_lang)
	{
		if (!Combination::isFeatureActive())
			return array();
		$sql = 'SELECT pa.*, ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, al.`name` AS attribute_name, a.`id_attribute`, pa.`unit_price_impact`, stock.quantity
				FROM `'._DB_PREFIX_.'product_attribute` pa
				LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
				LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
				LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
				LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang.')
				'.Product::sqlStock('pa', 'pa').'
				WHERE pa.`id_product` = '.(int)$this->id.'
				ORDER BY pa.`id_product_attribute`';
		return Db::getInstance()->executeS($sql);
	}

	public function getCombinationImages($id_lang)
	{
		if (!Combination::isFeatureActive())
			return false;
		if (!$productAttributes = Db::getInstance()->executeS('SELECT `id_product_attribute` FROM `'._DB_PREFIX_.'product_attribute` WHERE `id_product` = '.(int)($this->id)))
			return false;
		$ids = array();
		foreach ($productAttributes as $productAttribute)
			$ids[] = (int)($productAttribute['id_product_attribute']);
		if (!$result = Db::getInstance()->executeS('
			SELECT pai.`id_image`, pai.`id_product_attribute`, il.`legend`
			FROM `'._DB_PREFIX_.'product_attribute_image` pai
			LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (il.`id_image` = pai.`id_image`)
			LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_image` = pai.`id_image`)
			WHERE pai.`id_product_attribute` IN ('.implode(', ', $ids).') AND il.`id_lang` = '.(int)($id_lang).' ORDER by i.`position`'))
			return false;
		$images = array();
		foreach ($result as $row)
			$images[$row['id_product_attribute']][] = $row;
		return $images;
	}

	/**
	* Check if product has attributes combinaisons
	*
	* @return integer Attributes combinaisons number
	*/
	public function hasAttributes()
	{
		if (!Combination::isFeatureActive())
			return 0;
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(`id_product_attribute`)
		FROM `'._DB_PREFIX_.'product_attribute`
		WHERE `id_product` = '.(int)($this->id));
	}

	/**
	* Get new products
	*
	* @param integer $id_lang Language id
	* @param integer $pageNumber Start from (optional)
	* @param integer $nbProducts Number of products to return (optional)
	* @return array New products
	*/
	public static function getNewProducts($id_lang, $pageNumber = 0, $nbProducts = 10, $count = false, $orderBy = null, $orderWay = null, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();

		if ($pageNumber < 0) $pageNumber = 0;
		if ($nbProducts < 1) $nbProducts = 10;
		if (empty($orderBy) || $orderBy == 'position') $orderBy = 'date_add';
		if (empty($orderWay)) $orderWay = 'DESC';
		if ($orderBy == 'id_product' || $orderBy == 'price' || $orderBy == 'date_add')
			$orderByPrefix = 'p';
		else if ($orderBy == 'name')
			$orderByPrefix = 'pl';
		if (!Validate::isOrderBy($orderBy) || !Validate::isOrderWay($orderWay))
			die(Tools::displayError());

		$groups = FrontController::getCurrentCustomerGroups();
		$sqlGroups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');

		if ($count)
		{
			$sql = 'SELECT COUNT(p.`id_product`) AS nb
					FROM `'._DB_PREFIX_.'product` p
					'.$context->shop->addSqlAssociation('product', 'p').'
					WHERE `active` = 1
					AND DATEDIFF(p.`date_add`,
					DATE_SUB(NOW(), INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY)) > 0
					AND p.`id_product` IN (
						SELECT cp.`id_product`
						FROM `'._DB_PREFIX_.'category_group` cg
						LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
						WHERE cg.`id_group` '.$sqlGroups.'
					)';
			return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
		}

		$sql = new DbQuery();
		$sql->select('p.*, sa.out_of_stock, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, p.`ean13`, p.`upc`,
						i.`id_image`, il.`legend`, t.`rate`, m.`name` AS manufacturer_name, DATEDIFF(p.`date_add`, DATE_SUB(NOW(),
						INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY)) > 0 AS new,
						(p.`price` * ((100 + (t.`rate`))/100)) AS orderprice');

		$sql->from('product p');
		$sql->join($context->shop->addSqlAssociation('product', 'p'));
		$sql->leftJoin('product_lang pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$id_lang.$context->shop->addSqlRestrictionOnLang('pl').')');
		$sql->leftJoin('image i ON (i.`id_product` = p.`id_product` AND i.`cover` = 1)');
		$sql->leftJoin('image_lang il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')');
		$sql->leftJoin('tax_rule tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)$context->country->id.' AND tr.`id_state` = 0)');
		$sql->leftJoin('tax t ON (t.`id_tax` = tr.`id_tax`)');
		$sql->leftJoin('manufacturer m ON (m.`id_manufacturer` = p.`id_manufacturer`)');
		$sql->leftJoin('stock_available sa ON (sa.`id_product` = p.`id_product` AND sa.id_product_attribute = 0)');

		$sql->where('p.`active` = 1');
		$sql->where('DATEDIFF(p.`date_add`, DATE_SUB(NOW(), INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY)) > 0');
		$sql->where('p.`id_product` IN (
				SELECT cp.`id_product`
				FROM `'._DB_PREFIX_.'category_group` cg
				LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
				WHERE cg.`id_group` '.$sqlGroups.')');

		$sql->orderBy((isset($orderByPrefix) ? pSQL($orderByPrefix).'.' : '').'`'.pSQL($orderBy).'` '.pSQL($orderWay));
		$sql->limit($nbProducts, $pageNumber * $nbProducts);

		if (Combination::isFeatureActive())
		{
			$sql->select('pa.id_product_attribute');
			$sql->leftOuterJoin('product_attribute pa ON (p.`id_product` = pa.`id_product` AND `default_on` = 1)');
		}

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

		if ($orderBy == 'price')
			Tools::orderbyPrice($result, $orderWay);
		if (!$result)
			return false;

		$productsIds = array();
		foreach ($result as $row)
			$productsIds[] = $row['id_product'];
		// Thus you can avoid one query per product, because there will be only one query for all the products of the cart
		Product::cacheFrontFeatures($productsIds, $id_lang);

		return Product::getProductsProperties((int)$id_lang, $result);
	}

	protected static function _getProductIdByDate($beginning, $ending, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();

		$id_address = $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
		$ids = Address::getCountryAndState($id_address);
		$id_country = (int)($ids['id_country'] ? $ids['id_country'] : Configuration::get('PS_COUNTRY_DEFAULT'));
		return SpecificPrice::getProductIdByDate($context->shop->getID(), $context->currency->id, $id_country, $context->customer->id_default_group, $beginning, $ending);
	}

	/**
	* Get a random special
	*
	* @param integer $id_lang Language id
	* @return array Special
	*/
	public static function getRandomSpecial($id_lang, $beginning = false, $ending = false, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();

		$currentDate = date('Y-m-d H:i:s');
		$ids_product = self::_getProductIdByDate((!$beginning ? $currentDate : $beginning), (!$ending ? $currentDate : $ending), $context);

		$groups = FrontController::getCurrentCustomerGroups();
		$sqlGroups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');

		// Please keep 2 distinct queries because RAND() is an awful way to achieve this result
		$sql = 'SELECT p.id_product
				FROM `'._DB_PREFIX_.'product` p
				'.$context->shop->addSqlAssociation('product', 'p').'
				WHERE p.`active` = 1
					'.(($ids_product) ? 'AND p.`id_product` IN ('.implode(', ', $ids_product).')' : '').'
					AND p.`id_product` IN (
						SELECT cp.`id_product`
						FROM `'._DB_PREFIX_.'category_group` cg
						LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
						WHERE cg.`id_group` '.$sqlGroups.'
					)
				ORDER BY RAND()';
		$id_product = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

		if (!$id_product)
			return false;

		$sql = 'SELECT p.*, sa.out_of_stock, sa.out_of_stock out_of_stock, pl.`description`, pl.`description_short`,
					pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`,
					p.`ean13`, p.`upc`, i.`id_image`, il.`legend`, t.`rate`
				FROM `'._DB_PREFIX_.'product` p
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$id_lang.$context->shop->addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product` AND i.`cover` = 1)
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group`
					AND tr.`id_country` = '.(int)Context::getContext()->country->id.'
					AND tr.`id_state` = 0)
				LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
				LEFT JOIN `'._DB_PREFIX_.'stock_available` sa ON sa.`id_product` = p.`id_product` AND sa.id_product_attribute = 0
				WHERE p.id_product = '.(int)$id_product;
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

		return Product::getProductProperties($id_lang, $row);
	}

	/**
	* Get prices drop
	*
	* @param integer $id_lang Language id
	* @param integer $pageNumber Start from (optional)
	* @param integer $nbProducts Number of products to return (optional)
	* @param boolean $count Only in order to get total number (optional)
	* @return array Prices drop
	*/
	public static function getPricesDrop($id_lang, $pageNumber = 0, $nbProducts = 10, $count = false, $orderBy = null, $orderWay = null, $beginning = false, $ending = false, Context $context = null)
	{
		if (!Validate::isBool($count))
			die(Tools::displayError());

		if (!$context) $context = Context::getContext();
		if ($pageNumber < 0) $pageNumber = 0;
		if ($nbProducts < 1) $nbProducts = 10;
		if (empty($orderBy) || $orderBy == 'position') $orderBy = 'price';
		if (empty($orderWay)) $orderWay = 'DESC';
		if ($orderBy == 'id_product' || $orderBy == 'price' || $orderBy == 'date_add')
			$orderByPrefix = 'p';
		else if ($orderBy == 'name')
			$orderByPrefix = 'pl';
		if (!Validate::isOrderBy($orderBy) || !Validate::isOrderWay($orderWay))
			die (Tools::displayError());
		$currentDate = date('Y-m-d H:i:s');
		$ids_product = self::_getProductIdByDate((!$beginning ? $currentDate : $beginning), (!$ending ? $currentDate : $ending), $context);

		$groups = FrontController::getCurrentCustomerGroups();
		$sqlGroups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');

		if ($count)
		{
			$sql = 'SELECT COUNT(DISTINCT p.`id_product`) AS nb
					FROM `'._DB_PREFIX_.'product` p
					'.$context->shop->addSqlAssociation('product', 'p').'
					WHERE p.`active` = 1
						AND p.`show_price` = 1
			'.((!$beginning && !$ending) ? ' AND p.`id_product` IN('.((is_array($ids_product) && count($ids_product)) ? implode(', ', array_map('intval', $ids_product)) : 0).')' : '').'
						AND p.`id_product` IN (
							SELECT cp.`id_product`
							FROM `'._DB_PREFIX_.'category_group` cg
							LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
							WHERE cg.`id_group` '.$sqlGroups.'
						)';
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
			return (int)($result['nb']);
		}

		$sql = 'SELECT p.*, sa.out_of_stock, stock.quantity, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`,
					pl.`name`, i.`id_image`, il.`legend`, t.`rate`, m.`name` AS manufacturer_name,
					DATEDIFF(p.`date_add`, DATE_SUB(NOW(), INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY)) > 0 AS new
				FROM `'._DB_PREFIX_.'product` p
				'.$context->shop->addSqlAssociation('product', 'p').'
				'.Product::sqlStock('p', 0, false, $context->shop).'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$id_lang.$context->shop->addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product` AND i.`cover` = 1)
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group`
					AND tr.`id_country` = '.(int)Context::getContext()->country->id.'
					AND tr.`id_state` = 0)
				LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'stock_available` sa ON (sa.`id_product` = p.`id_product` AND sa.id_product_attribute = 0)
				WHERE p.`active` = 1
					AND p.`show_price` = 1
					'.((!$beginning && !$ending) ? ' AND p.`id_product` IN ('.((is_array($ids_product) && count($ids_product)) ? implode(', ', $ids_product) : 0).')' : '').'
					AND p.`id_product` IN (
						SELECT cp.`id_product`
						FROM `'._DB_PREFIX_.'category_group` cg
						LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
						WHERE cg.`id_group` '.$sqlGroups.'
					)
				ORDER BY '.(isset($orderByPrefix) ? pSQL($orderByPrefix).'.' : '').'`'.pSQL($orderBy).'`'.' '.pSQL($orderWay).'
				LIMIT '.(int)($pageNumber * $nbProducts).', '.(int)($nbProducts);
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		if ($orderBy == 'price')
			Tools::orderbyPrice($result, $orderWay);
		if (!$result)
			return false;
		return Product::getProductsProperties($id_lang, $result);
	}


	/**
	 * getProductCategories return an array of categories which this product belongs to
	 *
	 * @return array of categories
	 */
	public static function getProductCategories($id_product = '')
	{
		$ret = array();
		if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT `id_category` FROM `'._DB_PREFIX_.'category_product`
		WHERE `id_product` = '.(int)$id_product))
			foreach ($row as $val)
				$ret[] = $val['id_category'];
		return $ret;
			}

	public static function getProductCategoriesFull($id_product = '', $id_lang)
	{
		$ret = array();
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT cp.`id_category`, cl.`name`, cl.`link_rewrite` FROM `'._DB_PREFIX_.'category_product` cp
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (cp.`id_category` = cl.`id_category`'.Context::getContext()->shop->addSqlRestrictionOnLang('cl').')
		WHERE cp.`id_product` = '.(int)$id_product.'
		AND cl.`id_lang` = '.(int)$id_lang);
		foreach ($row as $val)
			$ret[$val['id_category']] = $val;
		return $ret;
	}

	/**
	 * getCategories return an array of categories which this product belongs to
	 *
	 * @return array of categories
	 */
	public function getCategories()
	{
		return Product::getProductCategories($this->id);
	}

	/**
	 * Gets carriers assigned to the product
	 */
	public function getCarriers()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT c.*
			FROM `'._DB_PREFIX_.'product_carrier` pc
			INNER JOIN `'._DB_PREFIX_.'carrier` c
				ON (c.`id_reference` = pc.`id_carrier_reference` AND c.`deleted` = 0)
			WHERE pc.`id_product` = '.(int)$this->id.'
				AND pc.`id_shop` = '.(int)$this->id_shop);
	}

	/**
	 * Sets carriers assigned to the product
	 */
	public function setCarriers($carrier_list)
	{
		$data = array();
		foreach ($carrier_list as $carrier)
		{
			$data[] = array(
				'id_product' => (int)$this->id,
				'id_carrier_reference' => (int)$carrier,
				'id_shop' => (int)$this->id_shop
			);
		}
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_carrier` WHERE id_product = '.(int)$this->id.' AND id_shop = '.(int)$this->id_shop);
		Db::getInstance()->AutoExecute(_DB_PREFIX_.'product_carrier', $data, 'INSERT');
	}

	/**
	* Get product images and legends
	*
	* @param integer $id_lang Language id for multilingual legends
	* @return array Product images and legends
	*/
	public function	getImages($id_lang, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();

		$sql = 'SELECT i.`cover`, i.`id_image`, il.`legend`, i.`position`
				FROM `'._DB_PREFIX_.'image` i
				'.$context->shop->addSqlAssociation('image', 'i').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				WHERE i.`id_product` = '.(int)$this->id.'
				ORDER BY `position`';
		return Db::getInstance()->executeS($sql);
	}

	/**
	* Get product cover image
	*
	* @return array Product cover image
	*/
	public static function getCover($id_product, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();

		$sql = 'SELECT i.`id_image`
				FROM `'._DB_PREFIX_.'image` i
				'.$context->shop->addSqlAssociation('image', 'i').'
				WHERE i.`id_product` = '.(int)($id_product).'
				AND i.`cover` = 1';
		return Db::getInstance()->getRow($sql);
	}

	/**
	* Get product price
	*
	* @param integer $id_product Product id
	* @param boolean $usetax With taxes or not (optional)
	* @param integer $id_product_attribute Product attribute id (optional). If set to false, do not apply the combination price impact. NULL does apply the default combination price impact.
	* @param integer $decimals Number of decimals (optional)
	* @param integer $divisor Useful when paying many time without fees (optional)
	* @param boolean $only_reduc Returns only the reduction amount
	* @param boolean $usereduc Set if the returned amount will include reduction
	* @param integer $quantity Required for quantity discount application (default value: 1)
	* @param boolean $forceAssociatedTax DEPRECATED - NOT USED Force to apply the associated tax. Only works when the parameter $usetax is true
	* @param integer $id_customer Customer ID (for customer group reduction)
	* @param integer $id_cart Cart ID. Required when the cookie is not accessible (e.g., inside a payment module, a cron task...)
	* @param integer $id_address Customer address ID. Required for price (tax included) calculation regarding the guest localization
	* @param variable_reference $specificPriceOutput. If a specific price applies regarding the previous parameters, this variable is filled with the corresponding SpecificPrice object
	* @param boolean $with_ecotax insert ecotax in price output.
	* @return float Product price
	*/
	public static function getPriceStatic($id_product, $usetax = true, $id_product_attribute = null, $decimals = 6, $divisor = null,
		$only_reduc = false, $usereduc = true, $quantity = 1, $forceAssociatedTax = false, $id_customer = null, $id_cart = null,
		$id_address = null, &$specificPriceOutput = null, $with_ecotax = true, $use_groupReduction = true, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();

		$cur_cart = $context->cart;

		if (isset($divisor))
			Tools::displayParameterAsDeprecated('divisor');

		if (!Validate::isBool($usetax) || !Validate::isUnsignedId($id_product))
			die(Tools::displayError());
		// Initializations
		$id_group = (isset($context->customer) ? $context->customer->id_default_group : _PS_DEFAULT_CUSTOMER_GROUP_);
		if (!is_object($cur_cart) || (Validate::isUnsignedInt($id_cart) && $id_cart))
		{
			/*
			* When a user (e.g., guest, customer, Google...) is on PrestaShop, he has already its cart as the global (see /init.php)
			* When a non-user calls directly this method (e.g., payment module...) is on PrestaShop, he does not have already it BUT knows the cart ID
			* When called from the back office, cart ID can be inexistant
			*/
			if (!$id_cart && !isset($context->employee))
				die(Tools::displayError());
			$cur_cart = new Cart($id_cart);
		}

		$cart_quantity = 0;
		if ((int)($id_cart))
		{
			$condition = '';
			$cache_name = (int)($id_cart).'_'.(int)($id_product);

			if (Configuration::get('PS_QTY_DISCOUNT_ON_COMBINATION'))
			{
				$cache_name = (int)($id_cart).'_'.(int)($id_product).'_'.(int)($id_product_attribute);
				$condition = ' AND `id_product_attribute` = '.(int)($id_product_attribute);
			}

			if (!isset(self::$_cart_quantity[$cache_name]) || self::$_cart_quantity[$cache_name] != (int)($quantity))
			{
				self::$_cart_quantity[$cache_name] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT SUM(`quantity`)
				FROM `'._DB_PREFIX_.'cart_product`
				WHERE `id_product` = '.(int)($id_product).'
				AND `id_cart` = '.(int)($id_cart).' '.$condition
				);

				$cart_quantity = self::$_cart_quantity[$cache_name];
			}
		}
		$quantity = ($id_cart && $cart_quantity) ? $cart_quantity : $quantity;
		$id_currency = (int)(Validate::isLoadedObject($context->currency) ? $context->currency->id : Configuration::get('PS_CURRENCY_DEFAULT'));

		// retrieve address informations
		$id_country = (int)$context->country->id;
		$id_state = 0;
		$zipcode = 0;

		if (!$id_address)
			$id_address = $cur_cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};

		if ($id_address)
		{
			$address_infos = Address::getCountryAndState($id_address);
			if ($address_infos['id_country'])
			{
				$id_country = (int)($address_infos['id_country']);
				$id_state = (int)($address_infos['id_state']);
				$zipcode = $address_infos['postcode'];
			}
		}
		else if (isset($context->customer->geoloc_id_country))
		{
			$id_country = (int)$context->customer->geoloc_id_country;
			$id_state = (int)$context->customer->id_state;
			$zipcode = (int)$context->customer->postcode;
		}

		if (Tax::excludeTaxeOption())
			$usetax = false;

		if ($usetax != false && !empty($address_infos['vat_number']) && $address_infos['id_country'] != Configuration::get('VATNUMBER_COUNTRY') && Configuration::get('VATNUMBER_MANAGEMENT'))
			$usetax = false;

		return Product::priceCalculation($context->shop->getID(), $id_product, $id_product_attribute, $id_country, $id_state, $zipcode, $id_currency, $id_group, $quantity, $usetax, $decimals, $only_reduc,
		$usereduc, $with_ecotax, $specificPriceOutput, $use_groupReduction);
	}

	/**
	* Price calculation / Get product price
	*
	* @param integer $id_shop Shop id
	* @param integer $id_product Product id
	* @param integer $id_product_attribute Product attribute id
	* @param integer $id_country Country id
	* @param integer $id_state State id
	* @param integer $id_currency Currency id
	* @param integer $id_group Group id
	* @param integer $quantity Quantity Required for Specific prices : quantity discount application
	* @param boolean $use_tax with (1) or without (0) tax
	* @param integer $decimals Number of decimals returned
	* @param boolean $only_reduc Returns only the reduction amount
	* @param boolean $use_reduc Set if the returned amount will include reduction
	* @param boolean $with_ecotax insert ecotax in price output.
	* @param variable_reference $specific_price_output If a specific price applies regarding the previous parameters, this variable is filled with the corresponding SpecificPrice object
	*
	* @return float Product price
	**/
	public static function priceCalculation($id_shop, $id_product, $id_product_attribute, $id_country, $id_state, $zipcode, $id_currency, $id_group, $quantity,
		$use_tax, $decimals, $only_reduc, $use_reduc, $with_ecotax, &$specific_price, $use_groupReduction)
	{
		// Caching
		if ($id_product_attribute === null)
			$product_attribute_label = 'null';
		else
			$product_attribute_label = ($id_product_attribute === false ? 'false' : $id_product_attribute);
		$cacheId = $id_product.'-'.$id_shop.'-'.$id_currency.'-'.$id_country.'-'.$id_state.'-'.$zipcode.'-'.$id_group.
			'-'.$quantity.'-'.$product_attribute_label.'-'.($use_tax?'1':'0').'-'.$decimals.'-'.($only_reduc?'1':'0').'-'.($use_reduc?'1':'0').'-'.$with_ecotax;

		// reference parameter is filled before any returns
		$specific_price = SpecificPrice::getSpecificPrice((int)$id_product, $id_shop, $id_currency, $id_country, $id_group, $quantity, $id_product_attribute);
		if (isset(self::$_prices[$cacheId]))
			return self::$_prices[$cacheId];

		// fetch price & attribute price
		$cacheId2 = $id_product.'-'.$id_product_attribute;
		if (!isset(self::$_pricesLevel2[$cacheId2]))
		{
			$sql = new DbQuery();
			$sql->select('p.`price`, p.`ecotax`');
			$sql->from('product p');
			$sql->where('p.`id_product` = '.(int)$id_product);

			if (Combination::isFeatureActive())
			{
				if ($id_product_attribute)
				{
					$sql->select('pa.`price` AS attribute_price');
					$sql->leftJoin('product_attribute pa ON (pa.`id_product_attribute` = '.(int)($id_product_attribute).')');
				}
				else
					$sql->select('IFNULL((SELECT pa.price FROM `'._DB_PREFIX_.'product_attribute` pa WHERE id_product = '.(int)($id_product).' AND default_on = 1), 0) AS attribute_price');
			}

			self::$_pricesLevel2[$cacheId2] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
		}
		$result = self::$_pricesLevel2[$cacheId2];

		$price = (float)(!$specific_price || $specific_price['price'] == 0) ? $result['price'] : $specific_price['price'];
		// convert only if the specific price is in the default currency (id_currency = 0)
		if (!$specific_price || !($specific_price['price'] > 0 && $specific_price['id_currency']))
			$price = Tools::convertPrice($price, $id_currency);

		// Attribute price
		$attribute_price = Tools::convertPrice(array_key_exists('attribute_price', $result) ? (float)($result['attribute_price']) : 0, $id_currency);
		if ($id_product_attribute !== false) // If you want the default combination, please use NULL value instead
			$price += $attribute_price;

		// Tax
		$address = new Address();
		$address->id_country = $id_country;
		$address->id_state = $id_state;
		$address->postcode = $zipcode;

		$tax_manager = TaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct((int)$id_product));
		$product_tax_calculator = $tax_manager->getTaxCalculator();

		// Add Tax
		if ($use_tax)
			$price = $product_tax_calculator->addTaxes($price);
		$price = Tools::ps_round($price, $decimals);

		// Reduction
		$reduc = 0;
		if (($only_reduc || $use_reduc) && $specific_price)
		{
			if ($specific_price['reduction_type'] == 'amount')
			{
				$reduction_amount = $specific_price['reduction'];

				if (!$specific_price['id_currency'])
					$reduction_amount = Tools::convertPrice($reduction_amount, $id_currency);
				$reduc = Tools::ps_round(!$use_tax ? $product_tax_calculator->removeTaxes($reduction_amount) : $reduction_amount, $decimals);
			}
			else
				$reduc = Tools::ps_round($price * $specific_price['reduction'], $decimals);
		}

		if ($only_reduc)
			return $reduc;
		if ($use_reduc)
			$price -= $reduc;

		// Group reduction
		if ($use_groupReduction)
		{
		if ($reductionFromCategory = (float)(GroupReduction::getValueForProduct($id_product, $id_group)))
			$price -= $price * $reductionFromCategory;
		else // apply group reduction if there is no group reduction for this category
			$price *= ((100 - Group::getReductionByIdGroup($id_group)) / 100);
		}

		$price = Tools::ps_round($price, $decimals);
		// Eco Tax
		if (($result['ecotax'] || isset($result['attribute_ecotax'])) AND $with_ecotax)
		{
			$ecotax = $result['ecotax'];
			if (isset($result['attribute_ecotax']) && $result['attribute_ecotax'] > 0)
				$ecotax = $result['attribute_ecotax'];

			if ($id_currency)
				$ecotax = Tools::convertPrice($ecotax, $id_currency);
			if ($use_tax)
			{
				// reinit the tax manager for ecotax handling
				$tax_manager = TaxManagerFactory::getManager(
					$address,
					(int)Configuration::get('PS_ECOTAX_TAX_RULES_GROUP_ID')
				);
				$ecotax_tax_calculator = $tax_manager->getTaxCalculator();
				$price += $ecotax_tax_calculator->addTaxes($ecotax);
			}
			else
				$price += $ecotax;
		}
		$price = Tools::ps_round($price, $decimals);
		if ($price < 0)
			$price = 0;

		self::$_prices[$cacheId] = $price;
		return self::$_prices[$cacheId];
	}

	public static function convertAndFormatPrice($price, $currency = false, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();
		if (!$currency)
			$currency = $context->currency;
		return Tools::displayPrice(Tools::convertPrice($price, $currency), $currency);
	}

	public static function isDiscounted($id_product, $quantity = 1, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();

		$id_group = $context->customer->id_default_group;
		$cart_quantity = !$context->cart ? 0 : Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT SUM(`quantity`)
			FROM `'._DB_PREFIX_.'cart_product`
			WHERE `id_product` = '.(int)($id_product).' AND `id_cart` = '.(int)($context->cart->id)
		);
		$quantity = $cart_quantity ? $cart_quantity : $quantity;
		$id_currency = (int)$context->currency->id;
		$ids = Address::getCountryAndState((int)($context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
		$id_country = (int)($ids['id_country'] ? $ids['id_country'] : Configuration::get('PS_COUNTRY_DEFAULT'));
		return (bool)SpecificPrice::getSpecificPrice((int)$id_product, $context->shop->getID(), $id_currency, $id_country, $id_group, $quantity);
	}

	/**
	* Get product price
	* Same as static function getPriceStatic, no need to specify product id
	*
	* @param boolean $tax With taxes or not (optional)
	* @param integer $id_product_attribute Product attribute id (optional)
	* @param integer $decimals Number of decimals (optional)
	* @param integer $divisor Util when paying many time without fees (optional)
	* @return float Product price in euros
	*/
	public function getPrice($tax = true, $id_product_attribute = null, $decimals = 6, $divisor = null, $only_reduc = false, $usereduc = true, $quantity = 1)
	{
		return Product::getPriceStatic((int)($this->id), $tax, $id_product_attribute, $decimals, $divisor, $only_reduc, $usereduc, $quantity);
	}

	public function getIdProductAttributeMostExpensive()
	{
		if (!Combination::isFeatureActive())
			return 0;
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `id_product_attribute`
		FROM `'._DB_PREFIX_.'product_attribute`
		WHERE `id_product` = '.(int)($this->id).'
		ORDER BY `price` DESC');

		return (isset($row['id_product_attribute']) && $row['id_product_attribute']) ? (int)$row['id_product_attribute'] : 0;
	}

	public function getPriceWithoutReduct($notax = false, $id_product_attribute = false)
	{
		return Product::getPriceStatic((int)($this->id), !$notax, $id_product_attribute, 6, null, false, false);
	}

	/**
	* Display price with right format and currency
	*
	* @param array $params Params
	* @param $smarty Smarty object
	* @return string Price with right format and currency
	*/
	public static function convertPrice($params, &$smarty)
	{
		return Tools::displayPrice($params['price'], Context::getContext()->currency);
	}

	/**
	 * Convert price with currency
	 *
	 * @param array $params
	 * @param object $smarty DEPRECATED
	 * @return Ambigous <string, mixed, Ambigous <number, string>>
	 */
	public static function convertPriceWithCurrency($params, &$smarty)
	{
		if (!isset($params['convert']))
			$params['convert'] = true;
		return Tools::displayPrice($params['price'], $params['currency'], false);
	}

	public static function displayWtPrice($params, &$smarty)
	{
		return Tools::displayPrice($params['p'], Context::getContext()->currency);
	}

	/**
	 * Display WT price with currency
	 *
	 * @param array $params
	 * @param object DEPRECATED $smarty
	 * @return Ambigous <string, mixed, Ambigous <number, string>>
	 */
	public static function displayWtPriceWithCurrency($params, &$smarty)
	{
		return Tools::displayPrice($params['price'], $params['currency'], false);
	}

	/**
	* Get available product quantities
	*
	* @param integer $id_product Product id
	* @param integer $id_product_attribute Product attribute id (optional)
	* @return integer Available quantities
	*/
	public static function getQuantity($id_product, $id_product_attribute = null, $cache_is_pack = null)
	{
		$lang = Configuration::get('PS_LANG_DEFAULT');
		if (((int)$cache_is_pack || ($cache_is_pack === null && Pack::isPack((int)$id_product, (int)$lang))) && !Pack::isInStock((int)$id_product, (int)$lang))
			return 0;

		// @since 1.5.0
		return (StockAvailable::getQuantityAvailableByProduct($id_product, $id_product_attribute, Context::getContext()->shop->getID()));
	}

	/**
	 * Create JOIN query with 'stock_available' table
	 *
	 * @param string $productAlias Alias of product table
	 * @param string|int $productAttribute If string : alias of PA table ; if int : value of PA ; if null : nothing about PA
	 * @param bool $innerJoin LEFT JOIN or INNER JOIN
	 * @param Context $context
	 * @return string
	 */
	public static function sqlStock($productAlias, $productAttribute = 0, $innerJoin = false, Shop $shop = null, DbQuery $sql = null)
	{
		if (!$shop)
			$shop = Context::getContext()->shop;

		if ($sql)
		{
			// @todo remove this code when query builder is accepted or removed
			$method = ($innerJoin) ? 'innerJoin' : 'leftJoin';
			$sql->$method('stock_available stock ON stock.id_product = '.pSQL($productAlias).'.id_product');
			if (!is_null($productAttribute))
			{
				if (!Combination::isFeatureActive())
					$sql->where('stock.id_product_attribute = 0');
				else if (is_numeric($productAttribute))
					$sql->where('stock.id_product_attribute = '.$productAttribute);
				else if (is_string($productAttribute))
					$sql->where('stock.id_product_attribute = IFNULL('.pSQL($productAttribute).'.id_product_attribute, 0)');
			}
			$sql->where(ltrim($shop->addSqlRestriction(false, 'stock'), ' AND '));
		}
		else
		{
			$sql = (($innerJoin) ? ' INNER ' : ' LEFT ').'JOIN '._DB_PREFIX_.'stock_available stock ON stock.id_product = '.pSQL($productAlias).'.id_product';
			if (!is_null($productAttribute))
			{
				if (!Combination::isFeatureActive())
					$sql .= ' AND stock.id_product_attribute = 0';
				else if (is_numeric($productAttribute))
					$sql .= ' AND stock.id_product_attribute = '.$productAttribute;
				else if (is_string($productAttribute))
					$sql .= ' AND stock.id_product_attribute = IFNULL('.pSQL($productAttribute).'.id_product_attribute, 0)';
			}
			$sql .= $shop->addSqlRestriction(false, 'stock').' ';
		}

		return $sql;
	}

	/**
	 * @deprecated since 1.5.0
	 *
	 * It's not possible to use this method with new stockManager and stockAvailable features
	 * Now this method do nothing
	 *
	 * @see StockManager if you want to manage real stock
	 * @see StockAvailable if you want to manage available quantities for sale on your shop(s)
	 *
	 * @param array $product Array with ordered product (quantity, id_product_attribute if applicable)
	 * @return mixed Query result
	 */
	public static function updateQuantity($product, $id_order = null)
	{
		Tools::displayAsDeprecated();

		return false;
		/*
		if (!is_array($product))
			die (Tools::displayError());

		if (!Configuration::get('PS_STOCK_MANAGEMENT'))
			return true;

		if (Pack::isPack((int)($product['id_product'])))
		{
			$products_pack = Pack::getItems((int)($product['id_product']), (int)(Configuration::get('PS_LANG_DEFAULT')));
			foreach ($products_pack as $product_pack)
			{
				$tab_product_pack['id_product'] = (int)($product_pack->id);
				$tab_product_pack['id_product_attribute'] = self::getDefaultAttribute($tab_product_pack['id_product'], 1);
				$tab_product_pack['cart_quantity'] = (int)($product_pack->pack_quantity * $product['cart_quantity']);
				self::updateQuantity($tab_product_pack);
			}
		}

		$productObj = new Product((int)$product['id_product'], false, (int)Configuration::get('PS_LANG_DEFAULT'));
		return $productObj->addStockMvt(-(int)$product['cart_quantity'], (int)_STOCK_MOVEMENT_ORDER_REASON_, (int)$product['id_product_attribute'], (int)$id_order, null);
		*/
	}

	/**
	 * @deprecated since 1.5.0
	 *
	 * It's not possible to use this method with new stockManager and stockAvailable features
	 * Now this method do nothing
	 *
	 * @see StockManager if you want to manage real stock
	 * @see StockAvailable if you want to manage available quantities for sale on your shop(s)
	 *
	 */
	public static function reinjectQuantities(&$orderDetail, $quantity, Context $context = null)
	{
		Tools::displayAsDeprecated();

		return false;
		/*
		if (!$context)
			$context = Context::getContext();
		if (!Validate::isLoadedObject($orderDetail))
			die(Tools::displayError());

		if (Pack::isPack((int)($orderDetail->product_id)))
		{
			$products_pack = Pack::getItems((int)($orderDetail->product_id), (int)(Configuration::get('PS_LANG_DEFAULT')));
			foreach ($products_pack as $product_pack)
				if (!$product_pack->addStockMvt((int)($product_pack->pack_quantity * $quantity), _STOCK_MOVEMENT_ORDER_REASON_, (int)$product_pack->id_product_attribute, (int)$orderDetail->id_order, (int)$context->employee->id))
					return false;
		}

		$product = new Product((int)$orderDetail->product_id);
		if (!$product->addStockMvt((int)$quantity, _STOCK_MOVEMENT_ORDER_REASON_, (int)$orderDetail->product_attribute_id, (int)$orderDetail->id_order, (int)$context->employee->id))
			return false;

		$orderDetail->product_quantity_reinjected += (int)($quantity);
		return true;
		*/
	}

	public static function isAvailableWhenOutOfStock($out_of_stock)
	{
		// @TODO 1.5.0 Update of STOCK_MANAGEMENT & ORDER_OUT_OF_STOCK
		return !Configuration::get('PS_STOCK_MANAGEMENT') ? true : ((int)($out_of_stock) == 2 ? (int)(Configuration::get('PS_ORDER_OUT_OF_STOCK')) : (int)($out_of_stock));
	}

	/**
	 * Check product availability
	 *
	 * @param integer $qty Quantity desired
	 * @return boolean True if product is available with this quantity
	 */
	public function checkQty($qty)
	{
		if (Pack::isPack((int)$this->id) && !Pack::isInStock((int)$this->id))
			return false;

		if ($this->isAvailableWhenOutOfStock(StockAvailable::outOfStock($this->id)))
			return true;

		if (isset($this->id_product_attribute))
			$id_product_attribute = $this->id_product_attribute;
		else
			$id_product_attribute = 0;

		return ($qty <= StockAvailable::getQuantityAvailableByProduct($this->id, $id_product_attribute, Context::getContext()->shop->getID()));
	}

	/**
	 * Check if there is not a default attribute and create it not
	 */
	public function checkDefaultAttributes()
	{
		if (!$this->id)
			return false;

		$row = Db::getInstance()->getRow('
		SELECT id_product
		FROM `'._DB_PREFIX_.'product_attribute`
		WHERE `default_on` = 1 AND `id_product` = '.(int)($this->id));
		if ($row)
			return true;

		$mini = Db::getInstance()->getRow('
		SELECT MIN(pa.id_product_attribute) as `id_attr`
		FROM `'._DB_PREFIX_.'product_attribute` pa
		WHERE `id_product` = '.(int)($this->id));
		if (!$mini)
			return false;

		if (!Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'product_attribute`
			SET `default_on` = 1
			WHERE `id_product_attribute` = '.(int)($mini['id_attr'])))
			return false;
		return true;
	}

	/**
	 * Get all available attribute groups
	 *
	 * @param integer $id_lang Language id
	 * @return array Attribute groups
	 */
	public function getAttributesGroups($id_lang)
	{
		if (!Combination::isFeatureActive())
			return array();
		$sql = 'SELECT ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, agl.`public_name` AS public_group_name, a.`id_attribute`, al.`name` AS attribute_name,
					a.`color` AS attribute_color, pa.`id_product_attribute`, stock.quantity, pa.`price`, pa.`ecotax`, pa.`weight`, pa.`default_on`, pa.`reference`, pa.`unit_price_impact`,
					pa.`minimal_quantity`, pa.`available_date`, ag.`group_type`
				FROM `'._DB_PREFIX_.'product_attribute` pa
				'.Product::sqlStock('pa', 'pa').'
				LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
				LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
				LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
				LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON a.`id_attribute` = al.`id_attribute`
				LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON ag.`id_attribute_group` = agl.`id_attribute_group`
				WHERE pa.`id_product` = '.(int)($this->id).'
					AND al.`id_lang` = '.(int)($id_lang).'
					AND agl.`id_lang` = '.(int)($id_lang).'
				ORDER BY ag.`position` ASC, a.`position` ASC';
		return Db::getInstance()->executeS($sql);
	}

	/**
	 * Delete product accessories
	 *
	 * @return mixed Deletion result
	 */
	public function deleteAccessories()
	{
		return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'accessory` WHERE `id_product_1` = '.(int)($this->id));
	}

	/**
	 * Delete product from other products accessories
	 *
	 * @return mixed Deletion result
	 */
	public function deleteFromAccessories()
	{
		return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'accessory` WHERE `id_product_2` = '.(int)($this->id));
	}

	/**
	 * Get product accessories (only names)
	 *
	 * @param integer $id_lang Language id
	 * @param integer $id_product Product id
	 * @return array Product accessories
	 */
	public static function getAccessoriesLight($id_lang, $id_product, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();

		$sql = 'SELECT p.`id_product`, p.`reference`, pl.`name`
				FROM `'._DB_PREFIX_.'accessory`
				LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product`= `id_product_2`)
				'.$context->shop->addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$id_lang.$context->shop->addSqlRestrictionOnLang('pl').')
				WHERE `id_product_1` = '.(int)$id_product;
		return Db::getInstance()->executeS($sql);
	}

	/**
	 * Get product accessories
	 *
	 * @param integer $id_lang Language id
	 * @return array Product accessories
	 */
	public function getAccessories($id_lang, $active = true, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();

		$sql = 'SELECT p.*, sa.out_of_stock, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, p.`ean13`, p.`upc`,
					i.`id_image`, il.`legend`, t.`rate`, m.`name` as manufacturer_name, cl.`name` AS category_default, DATEDIFF(p.`date_add`, DATE_SUB(NOW(),
					INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY)) > 0 AS new
				FROM `'._DB_PREFIX_.'accessory`
				LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = `id_product_2`
				'.$context->shop->addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$id_lang.$context->shop->addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (p.`id_category_default` = cl.`id_category` AND cl.`id_lang` = '.(int)$id_lang.$context->shop->addSqlRestrictionOnLang('cl').')
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product` AND i.`cover` = 1)
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (p.`id_manufacturer`= m.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group`
					AND tr.`id_country` = '.(int)Context::getContext()->country->id.'
					AND tr.`id_state` = 0)
				LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
				LEFT JOIN `'._DB_PREFIX_.'stock_available` sa ON (sa.`id_product` = p.`id_product` AND sa.id_product_attribute = 0)
				WHERE `id_product_1` = '.(int)($this->id).
				($active ? ' AND p.`active` = 1' : '');
		if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql))
			return false;

		return $this->getProductsProperties($id_lang, $result);
	}

	public static function getAccessoryById($accessoryId)
	{
		return Db::getInstance()->getRow('SELECT `id_product`, `name` FROM `'._DB_PREFIX_.'product_lang` WHERE `id_product` = '.(int)$accessoryId);
	}

	/**
	 * Link accessories with product
	 *
	 * @param array $accessories_id Accessories ids
	 */
	public function changeAccessories($accessories_id)
	{
		foreach ($accessories_id as $id_product_2)
			Db::getInstance()->AutoExecute(_DB_PREFIX_.'accessory', array('id_product_1' => (int)($this->id), 'id_product_2' => (int)($id_product_2)), 'INSERT');
	}

	/**
	 * Add new feature to product
	 */
	public function addFeaturesCustomToDB($id_value, $lang, $cust)
	{
		$row = array('id_feature_value' => (int)($id_value), 'id_lang' => (int)($lang), 'value' => pSQL($cust));
		return Db::getInstance()->autoExecute(_DB_PREFIX_.'feature_value_lang', $row, 'INSERT');
	}

	public function addFeaturesToDB($id_feature, $id_value, $cust = 0)
	{
		if ($cust)
		{
			$row = array('id_feature' => (int)($id_feature), 'custom' => 1);
			Db::getInstance()->autoExecute(_DB_PREFIX_.'feature_value', $row, 'INSERT');
			$id_value = Db::getInstance()->Insert_ID();
		}
		$row = array('id_feature' => (int)($id_feature), 'id_product' => (int)($this->id), 'id_feature_value' => (int)($id_value));
		Db::getInstance()->autoExecute(_DB_PREFIX_.'feature_product', $row, 'INSERT');
		if ($id_value)
			return ($id_value);
	}

	public static function addFeatureProductImport($id_product, $id_feature, $id_feature_value)
	{
		return Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'feature_product` (`id_feature`, `id_product`, `id_feature_value`)
			VALUES ('.(int)($id_feature).', '.(int)($id_product).', '.(int)($id_feature_value).')
			ON DUPLICATE KEY UPDATE `id_feature_value` = '.(int)($id_feature_value)
		);
	}

	/**
	* Select all features for the object
	*
	* @return array Array with feature product's data
	*/
	public function getFeatures()
	{
		return self::getFeaturesStatic((int)$this->id);
	}

	public static function getFeaturesStatic($id_product)
	{
		if (!Feature::isFeatureActive())
			return array();
		if (!array_key_exists($id_product, self::$_cacheFeatures))
			self::$_cacheFeatures[$id_product] = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT id_feature, id_product, id_feature_value
			FROM `'._DB_PREFIX_.'feature_product`
			WHERE `id_product` = '.(int)$id_product);
		return self::$_cacheFeatures[$id_product];
	}

	public static function cacheProductsFeatures($productIds)
	{
		if (!Feature::isFeatureActive())
			return;

		$productImplode = array();
		foreach ($productIds as $id_product)
			if ((int)$id_product && !array_key_exists($id_product, self::$_cacheFeatures))
				$productImplode[] = (int)$id_product;
		if (!count($productImplode))
			return;

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT id_feature, id_product, id_feature_value
		FROM `'._DB_PREFIX_.'feature_product`
		WHERE `id_product` IN ('.implode($productImplode, ',').')');
		foreach ($result as $row)
		{
			if (!array_key_exists($row['id_product'], self::$_cacheFeatures))
				self::$_cacheFeatures[$row['id_product']] = array();
			self::$_cacheFeatures[$row['id_product']][] = $row;
		}
	}

	public static function cacheFrontFeatures($productIds, $id_lang)
	{
		if (!Feature::isFeatureActive())
			return;

		$productImplode = array();
		foreach ($productIds as $id_product)
			if ((int)$id_product && !array_key_exists($id_product.'-'.$id_lang, self::$_cacheFeatures))
				$productImplode[] = (int)$id_product;
		if (!count($productImplode))
			return;

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT id_product, name, value, pf.id_feature
		FROM '._DB_PREFIX_.'feature_product pf
		LEFT JOIN '._DB_PREFIX_.'feature_lang fl ON (fl.id_feature = pf.id_feature AND fl.id_lang = '.(int)$id_lang.')
		LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl ON (fvl.id_feature_value = pf.id_feature_value AND fvl.id_lang = '.(int)$id_lang.')
		WHERE `id_product` IN ('.implode($productImplode, ',').')');

		foreach ($result as $row)
		{
			if (!array_key_exists($row['id_product'].'-'.$id_lang, self::$_frontFeaturesCache))
				self::$_frontFeaturesCache[$row['id_product'].'-'.$id_lang] = array();
			self::$_frontFeaturesCache[$row['id_product'].'-'.$id_lang][] = $row;
		}
	}

	/**
	* Admin panel product search
	*
	* @param integer $id_lang Language id
	* @param string $query Search query
	* @return array Matching products
	*/
	public static function searchByName($id_lang, $query, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();

		$sql = new DbQuery();
		$sql->select('p.`id_product`, pl.`name`, p.`active`, p.`reference`, m.`name` AS manufacturer_name, stock.`quantity`');
		$sql->from('category_product cp');
		$sql->leftJoin('product p ON p.`id_product` = cp.`id_product`');
		$sql->join($context->shop->addSqlAssociation('product', 'p'));
		$sql->leftJoin('product_lang pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$id_lang.$context->shop->addSqlRestrictionOnLang('pl').')');
		$sql->leftJoin('manufacturer m ON m.`id_manufacturer` = p.`id_manufacturer`');

		$where = 'pl.`name` LIKE \'%'.pSQL($query).'%\' OR p.`reference` LIKE \'%'.pSQL($query).'%\' OR p.`supplier_reference` LIKE \'%'.pSQL($query).'%\'';
		$sql->groupBy('`id_product`');
		$sql->orderBy('pl.`name` ASC');

		if (Combination::isFeatureActive())
		{
			$sql->leftJoin('product_attribute pa ON pa.`id_product` = p.`id_product`');
			$where .= ' OR pa.`reference` LIKE \'%'.pSQL($query).'%\'';
		}
		$sql->where($where);
		Product::sqlStock('p', 'pa', false, $context->shop, $sql);

		$result = Db::getInstance()->executeS($sql);

		if (!$result)
			return false;

		$resultsArray = array();
		foreach ($result as $row)
		{
			$row['price_tax_incl'] = Product::getPriceStatic($row['id_product'], true, null, 2);
			$row['price_tax_excl'] = Product::getPriceStatic($row['id_product'], false, null, 2);
			$resultsArray[] = $row;
		}
		return $resultsArray;
	}

	/**
	* Duplicate attributes when duplicating a product
	*
	* @param integer $id_product_old Old product id
	* @param integer $id_product_new New product id
	*/
	public static function duplicateAttributes($id_product_old, $id_product_new)
	{
		$return = true;
		$combinationImages = array();

		$result = Db::getInstance()->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'product_attribute`
		WHERE `id_product` = '.(int)($id_product_old));
		foreach ($result as $row)
		{
			$id_product_attribute_old = (int)($row['id_product_attribute']);
			$result2 = Db::getInstance()->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'product_attribute_combination`
			WHERE `id_product_attribute` = '.$id_product_attribute_old);

			$row['id_product'] = $id_product_new;
			unset($row['id_product_attribute']);
			$return &= Db::getInstance()->AutoExecute(_DB_PREFIX_.'product_attribute', $row, 'INSERT');

			$id_product_attribute_new = (int)(Db::getInstance()->Insert_ID());
			if ($resultImages = self::_getAttributeImageAssociations($id_product_attribute_old))
			{
				$combinationImages['old'][$id_product_attribute_old] = $resultImages;
				$combinationImages['new'][$id_product_attribute_new] = $resultImages;
			}
			foreach ($result2 as $row2)
			{
				$row2['id_product_attribute'] = $id_product_attribute_new;
				$return &= Db::getInstance()->AutoExecute(_DB_PREFIX_.'product_attribute_combination', $row2, 'INSERT');
			}
		}
		return !$return ? false : $combinationImages;
	}

	/**
	* Get product attribute image associations
	* @param integer $id_product_attribute
	* @return array
	*/
	public static function _getAttributeImageAssociations($id_product_attribute)
	{
		$combinationImages = array();
		$data = Db::getInstance()->executeS('
			SELECT `id_image`
			FROM `'._DB_PREFIX_.'product_attribute_image`
			WHERE `id_product_attribute` = '.(int)($id_product_attribute));
		foreach ($data as $row)
			$combinationImages[] = (int)($row['id_image']);
		return $combinationImages;
	}

	public static function duplicateAccessories($id_product_old, $id_product_new)
	{
		$return = true;

		$result = Db::getInstance()->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'accessory`
		WHERE `id_product_1` = '.(int)($id_product_old));
		foreach ($result as $row)
		{
			$data = array(
				'id_product_1' => (int)($id_product_new),
				'id_product_2' => (int)($row['id_product_2']));
			$return &= Db::getInstance()->AutoExecute(_DB_PREFIX_.'accessory', $data, 'INSERT');
		}
		return $return;
	}

	public static function duplicateTags($id_product_old, $id_product_new)
	{
		$tags = Db::getInstance()->executeS('SELECT `id_tag` FROM `'._DB_PREFIX_.'product_tag` WHERE `id_product` = '.(int)($id_product_old));
		if (!Db::getInstance()->NumRows())
			return true;
		$query = 'INSERT INTO `'._DB_PREFIX_.'product_tag` (`id_product`, `id_tag`) VALUES';
		foreach ($tags as $tag)
			$query .= ' ('.(int)($id_product_new).', '.(int)($tag['id_tag']).'),';
		$query = rtrim($query, ',');
		return Db::getInstance()->execute($query);
	}

	public static function duplicateDownload($id_product_old, $id_product_new)
	{
		$sql = 'SELECT `display_filename`, `filename`, `date_add`, `date_expiration`, `nb_days_accessible`, `nb_downloadable`, `active`, `is_shareable`
				FROM `'._DB_PREFIX_.'product_download`
				WHERE `id_product` = '.(int)$id_product_old;
		$resource = Db::getInstance()->execute($sql);

		if (!Db::getInstance()->NumRows())
			return true;
		$query = 'INSERT INTO `'._DB_PREFIX_.'product_download`
			(`id_product`, `display_filename`, `filename`, `date_add`, `date_expiration`, `nb_days_accessible`, `nb_downloadable`, `active`, `is_shareable`)
			VALUES';
		while ($row = Db::getInstance()->nextRow($resource))
			$query .= ' ('.(int)($id_product_new).', \''.pSQL($row['display_filename']).'\', \''.pSQL($row['filename']).'\', \''.pSQL($row['date_add']).'\', \''.
				pSQL($row['date_expiration']).'\', '.(int)($row['nb_days_accessible']).', '.(int)($row['nb_downloadable']).', '.(int)($row['active']).'), '.(int)($row['is_shareable']).'),';
		$query = rtrim($query, ',');
		return Db::getInstance()->execute($query);
	}

	/**
	* Duplicate features when duplicating a product
	*
	* @param integer $id_product_old Old product id
	* @param integer $id_product_old New product id
	*/
	public static function duplicateFeatures($id_product_old, $id_product_new)
	{
		$return = true;

		$result = Db::getInstance()->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'feature_product`
		WHERE `id_product` = '.(int)($id_product_old));
		foreach ($result as $row)
		{
			$result2 = Db::getInstance()->getRow('
			SELECT *
			FROM `'._DB_PREFIX_.'feature_value`
			WHERE `id_feature_value` = '.(int)($row['id_feature_value']));
			// Custom feature value, need to duplicate it
			if ($result2['custom'])
			{
				$old_id_feature_value = $result2['id_feature_value'];
				unset($result2['id_feature_value']);
				$return &= Db::getInstance()->AutoExecute(_DB_PREFIX_.'feature_value', $result2, 'INSERT');
				$max_fv = Db::getInstance()->getRow('
					SELECT MAX(`id_feature_value`) AS nb
					FROM `'._DB_PREFIX_.'feature_value`');
				$new_id_feature_value = $max_fv['nb'];
				$languages = Language::getLanguages();
				foreach ($languages as $language)
				{
					$result3 = Db::getInstance()->getRow('
					SELECT *
					FROM `'._DB_PREFIX_.'feature_value_lang`
					WHERE `id_feature_value` = '.(int)($old_id_feature_value).'
					AND `id_lang` = '.(int)($language['id_lang']));
					$result3['id_feature_value'] = $new_id_feature_value;
					$return &= Db::getInstance()->AutoExecute(_DB_PREFIX_.'feature_value_lang', $result3, 'INSERT');
				}
				$row['id_feature_value'] = $new_id_feature_value;
			}
			$row['id_product'] = $id_product_new;
			$return &= Db::getInstance()->AutoExecute(_DB_PREFIX_.'feature_product', $row, 'INSERT');
		}
		return $return;
	}

	protected static function _getCustomizationFieldsNLabels($productId)
	{
		if (!Customization::isFeatureActive())
			return false;

		$customizations = array();
		if (($customizations['fields'] = Db::getInstance()->executeS('
			SELECT `id_customization_field`, `type`, `required`
			FROM `'._DB_PREFIX_.'customization_field`
			WHERE `id_product` = '.(int)($productId).'
			ORDER BY `id_customization_field`')) === false)
			return false;
		if (empty($customizations['fields']))
			return array();
		$customizationFieldIds = array();
		foreach ($customizations['fields'] as $customizationField)
			$customizationFieldIds[] = (int)($customizationField['id_customization_field']);
		if (($customizationLabels = Db::getInstance()->executeS('
			SELECT `id_customization_field`, `id_lang`, `name`
			FROM `'._DB_PREFIX_.'customization_field_lang`
			WHERE `id_customization_field` IN ('.implode(', ', $customizationFieldIds).')
			ORDER BY `id_customization_field`')) === false)
			return false;
		foreach ($customizationLabels as $customizationLabel)
			$customizations['labels'][$customizationLabel['id_customization_field']][] = $customizationLabel;
		return $customizations;
	}

	public static function duplicateSpecificPrices($oldProductId, $productId)
	{
		foreach (SpecificPrice::getIdsByProductId((int)($oldProductId)) as $data)
		{
			$specificPrice = new SpecificPrice((int)($data['id_specific_price']));
			if (!$specificPrice->duplicate((int)($productId)))
				return false;
		}
		return true;
	}

	public static function duplicateCustomizationFields($oldProductId, $productId)
	{
		// If customization is not activated, return success
		if (!Customization::isFeatureActive())
			return true;
		if (($customizations = self::_getCustomizationFieldsNLabels($oldProductId)) === false)
			return false;
		if (empty($customizations))
			return true;
		foreach ($customizations['fields'] as $customizationField)
		{
			/* The new datas concern the new product */
			$customizationField['id_product'] = (int)($productId);
			$oldCustomizationFieldId = (int)($customizationField['id_customization_field']);
			unset($customizationField['id_customization_field']);
			if (!Db::getInstance()->AutoExecute(_DB_PREFIX_.'customization_field', $customizationField, 'INSERT') || !$customizationFieldId = Db::getInstance()->Insert_ID())
				return false;
			if (isset($customizations['labels']))
			{
				$query = 'INSERT INTO `'._DB_PREFIX_.'customization_field_lang` (`id_customization_field`, `id_lang`, `name`) VALUES ';
				foreach ($customizations['labels'][$oldCustomizationFieldId] as $customizationLabel)
					$query .= '('.(int)($customizationFieldId).', '.(int)($customizationLabel['id_lang']).', \''.pSQL($customizationLabel['name']).'\'), ';
				$query = rtrim($query, ', ');
				if (!Db::getInstance()->execute($query))
					return false;
			}
		}
		return true;
	}

	/**
	* Get the link of the product page of this product
	*/
	public function getLink(Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();
		return $context->link->getProductLink($this);
	}

	public function getTags($id_lang)
	{
		if (!$this->isFullyLoaded && is_null($this->tags))
			$this->tags = Tag::getProductTags($this->id);

		if (!($this->tags && key_exists($id_lang, $this->tags)))
			return '';

		$result = '';
		foreach ($this->tags[$id_lang] as $tagName)
			$result .= $tagName.', ';
		return rtrim($result, ', ');
	}

	public static function defineProductImage($row, $id_lang)
	{
		if (isset($row['id_image']))
		if ($row['id_image'])
			return $row['id_product'].'-'.$row['id_image'];
		return Language::getIsoById((int)$id_lang).'-default';
	}

	public static function getProductProperties($id_lang, $row, Context $context = null)
	{
		if (!$row['id_product'])
			return false;
		$context = Context::getContext();

		// Product::getDefaultAttribute is only called if id_product_attribute is missing from the SQL query at the origin of it: consider adding it in order to avoid unnecessary queries
		$row['allow_oosp'] = Product::isAvailableWhenOutOfStock($row['out_of_stock']);
		if (Combination::isFeatureActive() && (!isset($row['id_product_attribute']) || !$row['id_product_attribute'])
			&& ((isset($row['cache_default_attribute']) && ($ipa_default = $row['cache_default_attribute']) !== null)
				|| ($ipa_default = Product::getDefaultAttribute($row['id_product'], !$row['allow_oosp']))))
			$row['id_product_attribute'] = $ipa_default;
		if (!Combination::isFeatureActive() || !isset($row['id_product_attribute']))
			$row['id_product_attribute'] = 0;

		// Tax
		$usetax = Tax::excludeTaxeOption();

		$cacheKey = $row['id_product'].'-'.$row['id_product_attribute'].'-'.$id_lang.'-'.(int)($usetax);
		if (array_key_exists($cacheKey, self::$producPropertiesCache))
			return self::$producPropertiesCache[$cacheKey];

		// Datas
		$row['category'] = Category::getLinkRewrite((int)$row['id_category_default'], (int)($id_lang));
		$row['link'] = $context->link->getProductLink((int)$row['id_product'], $row['link_rewrite'], $row['category'], $row['ean13']);
		$row['attribute_price'] = (isset($row['id_product_attribute']) && $row['id_product_attribute']) ? (float)(Product::getProductAttributePrice($row['id_product_attribute'])) : 0;
		$row['price_tax_exc'] = Product::getPriceStatic((int)$row['id_product'], false, ((isset($row['id_product_attribute']) && !empty($row['id_product_attribute'])) ? (int)($row['id_product_attribute']) : null), (self::$_taxCalculationMethod == PS_TAX_EXC ? 2 : 6));
		if (self::$_taxCalculationMethod == PS_TAX_EXC)
		{
			$row['price_tax_exc'] = Tools::ps_round($row['price_tax_exc'], 2);
			$row['price'] = Product::getPriceStatic((int)$row['id_product'], true, ((isset($row['id_product_attribute']) && !empty($row['id_product_attribute'])) ? (int)($row['id_product_attribute']) : null), 6);
			$row['price_without_reduction'] = Product::getPriceStatic((int)$row['id_product'], false, ((isset($row['id_product_attribute']) AND !empty($row['id_product_attribute'])) ? (int)($row['id_product_attribute']) : null), 2, null, false, false);
		}
		else
		{
			$row['price'] = Tools::ps_round(Product::getPriceStatic((int)$row['id_product'], true, ((isset($row['id_product_attribute']) && !empty($row['id_product_attribute'])) ? (int)($row['id_product_attribute']) : null), 2), 2);
			$row['price_without_reduction'] = Product::getPriceStatic((int)$row['id_product'], true, ((isset($row['id_product_attribute']) && !empty($row['id_product_attribute'])) ? (int)($row['id_product_attribute']) : null), 6, null, false, false);
		}

		$row['reduction'] = Product::getPriceStatic((int)($row['id_product']), (bool)$usetax, (int)($row['id_product_attribute']), 6, null, true, true, 1, true, null, null, null, $specific_prices);
		$row['specific_prices'] = $specific_prices;

		if ($row['id_product_attribute'])
		{
			$row['quantity_all_versions'] = $row['quantity'];
			$row['quantity'] = Product::getQuantity((int)$row['id_product'], $row['id_product_attribute'], isset($row['cache_is_pack']) ? $row['cache_is_pack'] : null);
		}
		$row['id_image'] = Product::defineProductImage($row, $id_lang);
		$row['features'] = Product::getFrontFeaturesStatic((int)$id_lang, $row['id_product']);
		$row['attachments'] = ((!isset($row['cache_has_attachments']) || $row['cache_has_attachments']) ? Product::getAttachmentsStatic((int)($id_lang), $row['id_product']) : array());
		$row['virtual'] = ((!isset($row['is_virtual']) || $row['is_virtual']) ? 1 : 0);

		// Pack management
		$row['pack'] = (!isset($row['cache_is_pack']) ? Pack::isPack($row['id_product']) : (int)$row['cache_is_pack']);
		$row['packItems'] = $row['pack'] ? Pack::getItemTable($row['id_product'], $id_lang) : array();
		$row['nopackprice'] = $row['pack'] ? Pack::noPackPrice($row['id_product']) : 0;
		if ($row['pack'] && !Pack::isInStock($row['id_product']))
			$row['quantity'] = 0;

		self::$producPropertiesCache[$cacheKey] = $row;
		return self::$producPropertiesCache[$cacheKey];
	}

	public static function getProductsProperties($id_lang, $query_result)
	{
		$resultsArray = array();
		if (is_array($query_result))
		foreach ($query_result as $row)
			if ($row2 = Product::getProductProperties($id_lang, $row))
				$resultsArray[] = $row2;
		return $resultsArray;
	}

	/*
	* Select all features for a given language
	*
	* @param $id_lang Language id
	* @return array Array with feature's data
	*/
	public static function getFrontFeaturesStatic($id_lang, $id_product)
	{
		if (!Feature::isFeatureActive())
			return array();
		if (!array_key_exists($id_product.'-'.$id_lang, self::$_frontFeaturesCache))
		{
			self::$_frontFeaturesCache[$id_product.'-'.$id_lang] = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT name, value, pf.id_feature
			FROM '._DB_PREFIX_.'feature_product pf
			LEFT JOIN '._DB_PREFIX_.'feature_lang fl ON (fl.id_feature = pf.id_feature AND fl.id_lang = '.(int)$id_lang.')
			LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl ON (fvl.id_feature_value = pf.id_feature_value AND fvl.id_lang = '.(int)$id_lang.')
			LEFT JOIN '._DB_PREFIX_.'feature f ON (f.id_feature = pf.id_feature AND fl.id_lang = '.(int)$id_lang.')
			WHERE pf.id_product = '.(int)$id_product.'
			ORDER BY f.position ASC');
		}
		return self::$_frontFeaturesCache[$id_product.'-'.$id_lang];
	}

	public function getFrontFeatures($id_lang)
	{
		return self::getFrontFeaturesStatic($id_lang, $this->id);
	}

	public static function getAttachmentsStatic($id_lang, $id_product)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *
		FROM '._DB_PREFIX_.'product_attachment pa
		LEFT JOIN '._DB_PREFIX_.'attachment a ON a.id_attachment = pa.id_attachment
		LEFT JOIN '._DB_PREFIX_.'attachment_lang al ON (a.id_attachment = al.id_attachment AND al.id_lang = '.(int)($id_lang).')
		WHERE pa.id_product = '.(int)($id_product));
	}

	public function getAttachments($id_lang)
	{
		return self::getAttachmentsStatic($id_lang, $this->id);
	}

	/*
	** Customization management
	*/

	public static function getAllCustomizedDatas($id_cart, $id_lang = null, $only_in_cart = true)
	{
		if (!Customization::isFeatureActive())
			return false;

		// No need to query if there isn't any real cart!
		if (!$id_cart)
			return false;
		if (!$id_lang)
			$id_lang = Context::getContext()->language->id;

		if (!$result = Db::getInstance()->executeS('
			SELECT cd.`id_customization`, c.`id_address_delivery`, c.`id_product`, cfl.`id_customization_field`, c.`id_product_attribute`, cd.`type`, cd.`index`, cd.`value`, cfl.`name`
			FROM `'._DB_PREFIX_.'customized_data` cd
			NATURAL JOIN `'._DB_PREFIX_.'customization` c
			LEFT JOIN `'._DB_PREFIX_.'customization_field_lang` cfl ON (cfl.id_customization_field = cd.`index` AND id_lang = '.(int)($id_lang).')
			WHERE c.`id_cart` = '.(int)$id_cart.
			($only_in_cart ? ' AND c.`in_cart` = 1' : '').'
			ORDER BY `id_product`, `id_product_attribute`, `type`, `index`'))
			return false;
		$customizedDatas = array();
		foreach ($result as $row)
			$customizedDatas[(int)($row['id_product'])][(int)($row['id_product_attribute'])][(int)($row['id_address_delivery'])][(int)($row['id_customization'])]['datas'][(int)($row['type'])][] = $row;
		if (!$result = Db::getInstance()->executeS('SELECT `id_product`, `id_product_attribute`, `id_customization`, `id_address_delivery`, `quantity`, `quantity_refunded`, `quantity_returned`
			FROM `'._DB_PREFIX_.'customization` WHERE `id_cart` = '.(int)($id_cart).($only_in_cart ? ' AND `in_cart` = 1' : '')))
			return false;
		foreach ($result as $row)
		{
			$customizedDatas[(int)($row['id_product'])][(int)($row['id_product_attribute'])][(int)($row['id_address_delivery'])][(int)($row['id_customization'])]['quantity'] = (int)($row['quantity']);
			$customizedDatas[(int)($row['id_product'])][(int)($row['id_product_attribute'])][(int)($row['id_address_delivery'])][(int)($row['id_customization'])]['quantity_refunded'] = (int)($row['quantity_refunded']);
			$customizedDatas[(int)($row['id_product'])][(int)($row['id_product_attribute'])][(int)($row['id_address_delivery'])][(int)($row['id_customization'])]['quantity_returned'] = (int)($row['quantity_returned']);
		}
		return $customizedDatas;
	}

	public static function addCustomizationPrice(&$products, &$customizedDatas)
	{
		if (!$customizedDatas)
			return;

		foreach ($products as &$productUpdate)
		{
			if (!Customization::isFeatureActive())
			{
				$productUpdate['customizationQuantityTotal'] = 0;
				$productUpdate['customizationQuantityRefunded'] = 0;
				$productUpdate['customizationQuantityReturned'] = 0;
			}
			else
			{
				$customizationQuantity = 0;
				$customizationQuantityRefunded = 0;
				$customizationQuantityReturned = 0;
				/* Compatibility */
				$productId = (int)(isset($productUpdate['id_product']) ? $productUpdate['id_product'] : $productUpdate['product_id']);
				$productAttributeId = (int)(isset($productUpdate['id_product_attribute']) ? $productUpdate['id_product_attribute'] : $productUpdate['product_attribute_id']);
				$id_address_delivery = (int)$productUpdate['id_address_delivery'];
				$productQuantity = (int)(isset($productUpdate['cart_quantity']) ? $productUpdate['cart_quantity'] : $productUpdate['product_quantity']);
				$price = isset($productUpdate['price']) ? $productUpdate['price'] : $productUpdate['product_price'];
				$priceWt = $price * (1 + ((isset($productUpdate['tax_rate']) ? $productUpdate['tax_rate'] : $productUpdate['rate']) * 0.01));
				if (isset($customizedDatas[$productId][$productAttributeId]))
					foreach ($customizedDatas[$productId][$productAttributeId][$id_address_delivery] as $customization)
					{
						$customizationQuantity += (int)$customization['quantity'];
						$customizationQuantityRefunded += (int)$customization['quantity_refunded'];
						$customizationQuantityReturned += (int)$customization['quantity_returned'];
					}
				$productUpdate['customizationQuantityTotal'] = $customizationQuantity;
				$productUpdate['customizationQuantityRefunded'] = $customizationQuantityRefunded;
				$productUpdate['customizationQuantityReturned'] = $customizationQuantityReturned;
				if ($customizationQuantity)
				{
					$productUpdate['total_wt'] = $priceWt * ($productQuantity - $customizationQuantity);
					$productUpdate['total_customization_wt'] = $priceWt * $customizationQuantity;
					$productUpdate['total'] = $price * ($productQuantity - $customizationQuantity);
					$productUpdate['total_customization'] = $price * $customizationQuantity;
				}
			}
		}
	}

	/*
	** Customization fields' label management
	*/

	protected function _checkLabelField($field, $value)
	{
		if (!Validate::isLabel($value))
			return false;
		$tmp = explode('_', $field);
		if (count($tmp) < 4)
			return false;
		return $tmp;
	}

	protected function _deleteOldLabels()
	{
		$max = array(Product::CUSTOMIZE_FILE => (int)Tools::getValue('uploadable_files'), Product::CUSTOMIZE_TEXTFIELD => (int)Tools::getValue('text_fields'));
		/* Get customization field ids */
		if (($result = Db::getInstance()->executeS('SELECT `id_customization_field`, `type` FROM `'._DB_PREFIX_.'customization_field` WHERE `id_product` = '.(int)$this->id.'
		ORDER BY `id_customization_field`')) === false)
			return false;
		if (empty($result))
			return true;
		$customizationFields = array(Product::CUSTOMIZE_FILE => array(), Product::CUSTOMIZE_TEXTFIELD => array());
		foreach ($result as $row)
			$customizationFields[(int)$row['type']][] = (int)$row['id_customization_field'];
		$extraFile = count($customizationFields[Product::CUSTOMIZE_FILE]) - $max[Product::CUSTOMIZE_FILE];
		$extraText = count($customizationFields[Product::CUSTOMIZE_TEXTFIELD]) - $max[Product::CUSTOMIZE_TEXTFIELD];

		/* If too much inside the database, deletion */
		if ($extraFile > 0 && count($customizationFields[Product::CUSTOMIZE_FILE]) - $extraFile >= 0 &&
		(!Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customization_field`
			WHERE `id_product` = '.(int)$this->id.' AND `type` = '.Product::CUSTOMIZE_FILE.'
			AND `id_customization_field` >= '.(int)$customizationFields[Product::CUSTOMIZE_FILE][count($customizationFields[Product::CUSTOMIZE_FILE]) - $extraFile])
		|| !Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customization_field_lang`
			WHERE `id_customization_field` NOT IN (SELECT `id_customization_field` FROM `'._DB_PREFIX_.'customization_field`)')))
			return false;

		if ($extraText > 0 && count($customizationFields[Product::CUSTOMIZE_TEXTFIELD]) - $extraText >= 0 &&
		(!Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customization_field`
			WHERE `id_product` = '.(int)$this->id.' AND `type` = '.Product::CUSTOMIZE_TEXTFIELD.'
			AND `id_customization_field` >= '.(int)$customizationFields[Product::CUSTOMIZE_TEXTFIELD][count($customizationFields[Product::CUSTOMIZE_TEXTFIELD]) - $extraText])
		|| !Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customization_field_lang`
			WHERE `id_customization_field` NOT IN (SELECT `id_customization_field` FROM `'._DB_PREFIX_.'customization_field`)')))
			return false;

		// Refresh cache of feature detachable
		Configuration::updateGlobalValue('PS_CUSTOMIZATION_FEATURE_ACTIVE', Customization::isCurrentlyUsed());

		return true;
	}

	protected function _createLabel(&$languages, $type)
	{
		// Label insertion
		if (!Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'customization_field` (`id_product`, `type`, `required`)
			VALUES ('.(int)$this->id.', '.(int)$type.', 0)') ||
			!$id_customization_field = (int)Db::getInstance()->Insert_ID())
			return false;

		// Multilingual label name creation
		$values = '';
		foreach ($languages as $language)
			$values .= '('.(int)$id_customization_field.', '.(int)$language['id_lang'].', \'\'), ';
		$values = rtrim($values, ', ');
		if (!Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'customization_field_lang` (`id_customization_field`, `id_lang`, `name`)
			VALUES '.$values))
			return false;

		// Set cache of feature detachable to true
		Configuration::updateGlobalValue('PS_CUSTOMIZATION_FEATURE_ACTIVE', '1');

		return true;
	}

	public function createLabels($uploadableFiles, $textFields)
	{
		$languages = Language::getLanguages();
		if ((int)$uploadableFiles > 0)
			for ($i = 0; $i < (int)$uploadableFiles; $i++)
				if (!$this->_createLabel($languages, Product::CUSTOMIZE_FILE))
					return false;
		if ((int)$textFields > 0)
			for ($i = 0; $i < (int)$textFields; $i++)
				if (!$this->_createLabel($languages, Product::CUSTOMIZE_TEXTFIELD))
					return false;
		return true;
	}

	public function updateLabels()
	{
		$hasRequiredFields = 0;
		foreach ($_POST as $field => $value)
			/* Label update */
			if (strncmp($field, 'label_', 6) == 0)
			{
				if (!$tmp = $this->_checkLabelField($field, $value))
					return false;
				/* Multilingual label name update */
				if (!Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'customization_field_lang`
					(`id_customization_field`, `id_lang`, `name`) VALUES ('.(int)$tmp[2].', '.(int)$tmp[3].', \''.pSQL($value).'\')
					ON DUPLICATE KEY UPDATE `name` = \''.pSQL($value).'\''))
					return false;
				$isRequired = isset($_POST['require_'.(int)$tmp[1].'_'.(int)$tmp[2]]) ? 1 : 0;
				$hasRequiredFields |= $isRequired;
				/* Require option update */
				if (!Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'customization_field` SET `required` = '.(int)$isRequired.' WHERE `id_customization_field` = '.(int)$tmp[2]))
					return false;
			}
		if ($hasRequiredFields && !Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'product` SET `customizable` = 2 WHERE `id_product` = '.(int)$this->id))
			return false;
		if (!$this->_deleteOldLabels())
			return false;
		return true;
	}

	public function getCustomizationFields($id_lang = false)
	{
		if (!Customization::isFeatureActive())
			return false;
		if (!$result = Db::getInstance()->executeS('
			SELECT cf.`id_customization_field`, cf.`type`, cf.`required`, cfl.`name`, cfl.`id_lang`
			FROM `'._DB_PREFIX_.'customization_field` cf
			NATURAL JOIN `'._DB_PREFIX_.'customization_field_lang` cfl
			WHERE cf.`id_product` = '.(int)$this->id.($id_lang ? ' AND cfl.`id_lang` = '.(int)$id_lang : '').'
			ORDER BY cf.`id_customization_field`'))
			return false;
		if ($id_lang)
			return $result;
		$customizationFields = array();
		foreach ($result as $row)
			$customizationFields[(int)$row['type']][(int)$row['id_customization_field']][(int)$row['id_lang']] = $row;
		return $customizationFields;
	}

	public function getCustomizationFieldIds()
	{
		if (!Customization::isFeatureActive())
			return array();
		return Db::getInstance()->executeS('
			SELECT `id_customization_field`, `type`
			FROM `'._DB_PREFIX_.'customization_field`
			WHERE `id_product` = '.(int)$this->id);
	}

	public function getRequiredCustomizableFields()
	{
		if (!Customization::isFeatureActive())
			return array();
		return Db::getInstance()->executeS('
			SELECT `id_customization_field`, `type`
			FROM `'._DB_PREFIX_.'customization_field`
			WHERE `id_product` = '.(int)$this->id.'
			AND `required` = 1'
		);
	}

	public function hasAllRequiredCustomizableFields(Context $context = null)
	{
		if (!Customization::isFeatureActive())
			return true;
		if (!$context)
			$context = Context::getContext();

		$fields = $context->cart->getProductCustomization($this->id, null, true);
		if (($requiredFields = $this->getRequiredCustomizableFields()) === false)
			return false;

		$fields_present = array();
		foreach ($fields as $field)
			$fields_present[] = array('id_customization_field' => $field['index'], 'type' => $field['type']);
		foreach ($requiredFields as $required_field)
			if (!in_array($required_field, $fields_present))
				return false;
		return true;
	}


	/**
	 * Checks if the product is in at least one of the submited categories
	 *
	 * @param int $id_product
	 * @param array $categories array of category arrays
	 * @return boolean is the product in at least one category
	 */
	public static function idIsOnCategoryId($id_product, $categories)
	{
		if (!((int)$id_product > 0) || !is_array($categories) || empty($categories))
			return false;
		$sql = 'SELECT id_product FROM `'._DB_PREFIX_.'category_product` WHERE `id_product`='.(int)$id_product.' AND `id_category` IN(';
		foreach ($categories as $category)
			$sql .= (int)$category['id_category'].',';
		$sql = rtrim($sql, ',').')';

		if (isset(self::$_incat[md5($sql)]))
			return self::$_incat[md5($sql)];

		if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql))
			return false;
		self::$_incat[md5($sql)] = (Db::getInstance(_PS_USE_SQL_SLAVE_)->NumRows() > 0 ? true : false);
		return self::$_incat[md5($sql)];
	}

	public function getNoPackPrice()
	{
		return Pack::noPackPrice($this->id);
	}

	public function checkAccess($id_customer)
	{
		if (!$id_customer)
			return (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT ctg.`id_group`
			FROM `'._DB_PREFIX_.'category_product` cp
			INNER JOIN `'._DB_PREFIX_.'category_group` ctg ON (ctg.`id_category` = cp.`id_category`)
			WHERE cp.`id_product` = '.(int)$this->id.' AND ctg.`id_group` = 1');
		else
			return (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT cg.`id_group`
			FROM `'._DB_PREFIX_.'category_product` cp
			INNER JOIN `'._DB_PREFIX_.'category_group` ctg ON (ctg.`id_category` = cp.`id_category`)
			INNER JOIN `'._DB_PREFIX_.'customer_group` cg ON (cg.`id_group` = ctg.`id_group`)
			WHERE cp.`id_product` = '.(int)$this->id.' AND cg.`id_customer` = '.(int)$id_customer);
	}


	/**
	 * Add a stock movement for current product
	 *
	 * Since 1.5, this method only permit to add/remove available quantities of the current product in the current shop
	 *
	 * @see StockManager if you want to manage real stock
	 * @see StockAvailable if you want to manage available quantities for sale on your shop(s)
	 *
	 * @deprecated since 1.5.0
	 *
	 * @param int $quantity
	 * @param int $id_reason - useless
	 * @param int $id_product_attribute
	 * @param int $id_order - useless
	 * @param int $id_employee - useless
	 * @return bool
	 */
	public function addStockMvt($quantity, $id_reason, $id_product_attribute = null, $id_order = null, $id_employee = null)
	{
		if (!$this->id)
			return;

		if ($id_product_attribute == null)
			$id_product_attribute = 0;

		$quantity = abs((int)$quantity) * $reason->sign;

		return StockAvailable::updateQuantity($this->id, $id_product_attribute, $quantity, Context::getContext()->shop->getID());
	}

	/**
	 * @deprecated since 1.5.0
	 */
	public function getStockMvts($id_lang)
	{
		Tools::displayAsDeprecated();

		return Db::getInstance()->executeS('
			SELECT sm.id_stock_mvt, sm.date_add, sm.quantity, sm.id_order,
			CONCAT(pl.name, \' \', GROUP_CONCAT(IFNULL(al.name, \'\'), \'\')) product_name, CONCAT(e.lastname, \' \', e.firstname) employee, mrl.name reason
			FROM `'._DB_PREFIX_.'stock_mvt` sm
			LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (sm.id_product = pl.id_product AND pl.id_lang = '.(int)$id_lang.Context::getContext()->shop->addSqlRestrictionOnLang('pl').')
			LEFT JOIN `'._DB_PREFIX_.'stock_mvt_reason_lang` mrl ON (sm.id_stock_mvt_reason = mrl.id_stock_mvt_reason AND mrl.id_lang = '.(int)$id_lang.')
			LEFT JOIN `'._DB_PREFIX_.'employee` e ON (e.id_employee = sm.id_employee)
			LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (pac.id_product_attribute = sm.id_product_attribute)
			LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (al.id_attribute = pac.id_attribute AND al.id_lang = '.(int)$id_lang.')
			WHERE sm.id_product='.(int)$this->id.'
			GROUP BY sm.id_stock_mvt
		');
	}

	public static function getUrlRewriteInformations($id_product)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT pl.`id_lang`, pl.`link_rewrite`, p.`ean13`, cl.`link_rewrite` AS category_rewrite
			FROM `'._DB_PREFIX_.'product` p
			LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product`'.Context::getContext()->shop->addSqlRestrictionOnLang('pl').')
			LEFT JOIN `'._DB_PREFIX_.'lang` l ON (pl.`id_lang` = l.`id_lang`)
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (cl.`id_category` = p.`id_category_default`  AND cl.`id_lang` = pl.`id_lang`'.Context::getContext()->shop->addSqlRestrictionOnLang('cl').')
			WHERE p.`id_product` = '.(int)$id_product.'
			AND l.`active` = 1
		');
	}

	public static function getIdTaxRulesGroupByIdProduct($id_product)
	{
		if (!isset(self::$_tax_rules_group[$id_product]))
		{
			$id_group = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `id_tax_rules_group`
			FROM `'._DB_PREFIX_.'product`
			WHERE `id_product` = '.(int)$id_product);
			self::$_tax_rules_group[$id_product] = $id_group;
		}
		return self::$_tax_rules_group[$id_product];
	}

	/**
	* @return the total taxes rate applied to the product
	*/
	public function getTaxesRate(Address $address = null)
	{
		if (!$address || !$address->id_country)
			$address = Address::initialize();

		$tax_manager = TaxManagerFactory::getManager($address, $this->id_tax_rules_group);
		$tax_calculator = $tax_manager->getTaxCalculator();

		return $tax_calculator->getTotalRate();
	}

	/**
	* Webservice getter : get product features association
	*
	* @return array
	*/
	public function getWsProductFeatures()
	{
		$rows = $this->getFeatures();
		foreach ($rows as $keyrow => $row)
		{
			foreach ($row as $keyfeature => $feature)
			{
				if ($keyfeature == 'id_feature')
				{
					$rows[$keyrow]['id'] = $feature;
					unset($rows[$keyrow]['id_feature']);
				}
				unset($rows[$keyrow]['id_product']);
			}
			asort($rows[$keyrow]);
		}
		return $rows;
	}

	/**
	* Webservice setter : set product features association
	*
	* @param $productFeatures Product Feature ids
	* @return boolean
	*/
	public function setWsProductFeatures($productFeatures)
	{
		$this->deleteProductFeatures();
		foreach ($productFeatures as $productFeature)
			$this->addFeaturesToDB($productFeature['id'], $productFeature['id_feature_value']);
		return true;
	}

	/**
	* Webservice getter : get virtual field default combination
	*
	* @return int
	*/
	public function getWsDefaultCombination()
	{
		return self::getDefaultAttribute($this->id);
	}

	/**
	* Webservice setter : set virtual field default combination
	*
	* @param $id_combination id default combination
	*/
	public function setWsDefaultCombination($id_combination)
	{
		$this->deleteDefaultAttributes();
		return $this->setDefaultAttribute((int)$id_combination);
	}

	/**
	* Webservice getter : get category ids of current product for association
	*
	* @return array
	*/
	public function getWsCategories()
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT `id_category` AS id FROM `'._DB_PREFIX_.'category_product` WHERE `id_product` = '.(int)$this->id);
		return $result;
	}

	/**
	* Webservice setter : set category ids of current product for association
	*
	* @param $category_ids category ids
	*/
	public function setWsCategories($category_ids)
	{
		$ids = array();
		foreach ($category_ids as $value)
			$ids[] = $value['id'];
		if ($this->deleteCategories())
		{
			if ($ids)
			{
				$sqlValues = '';
				$ids = array_map('intval', $ids);
				foreach ($ids as $position => $id)
					$sqlValues[] = '('.(int)$id.', '.(int)$this->id.', '.(int)$position.')';
				$result = Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'category_product` (`id_category`, `id_product`, `position`)
					VALUES '.implode(',', $sqlValues)
				);
				return $result;
			}
		}
		return true;
	}

	/**
	* Webservice getter : get combination ids of current product for association
	*
	* @return array
	*/
	public function getWsCombinations()
	{
		$result = Db::getInstance()->executeS('SELECT `id_product_attribute` as id FROM `'._DB_PREFIX_.'product_attribute` WHERE `id_product` = '.(int)$this->id);
		return $result;
	}

	/**
	* Webservice setter : set combination ids of current product for association
	*
	* @param $combinations combination ids
	*/
	public function setWsCombinations($combinations)
	{
		// No hook exec
		$ids_new = array();
		foreach ($combinations as $combination)
			$ids_new[] = (int)$combination['id'];

		$ids_orig = array();
		$original = Db::getInstance()->executeS('SELECT `id_product_attribute` as id FROM `'._DB_PREFIX_.'product_attribute` WHERE `id_product` = '.(int)$this->id);
		if (is_array($original))
			foreach ($original as $id)
				$ids_orig[] = $id['id'];

		$all_ids = array();
		$all = Db::getInstance()->executeS('SELECT `id_product_attribute` as id FROM `'._DB_PREFIX_.'product_attribute`');
		if (is_array($all))
			foreach ($all as $id)
				$all_ids[] = $id['id'];

		$toAdd = array();
		foreach ($ids_new as $id)
			if (!in_array($id, $ids_orig))
				$toAdd[] = $id;

		$toDelete = array();
		foreach ($ids_orig as $id)
			if (!in_array($id, $ids_new))
				$toDelete[] = $id;

		// Delete rows
		if (count($toDelete) > 0)
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_attribute` WHERE `id_product_attribute` IN ('.implode(',', $toDelete).')');

		foreach ($toAdd as $id)
		{
			// Update id_product if exists else create
			if (in_array($id, $all_ids))
				Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'product_attribute` SET id_product = '.(int)$this->id.' WHERE id_product_attribute='.$id);
			else
				Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'product_attribute` (`id_product`) VALUES ('.$this->id.')');
		}
		return true;
	}

	/**
	* Webservice getter : get product option ids of current product for association
	*
	* @return array
	*/
	public function getWsProductOptionValues()
	{
		$result = Db::getInstance()->executeS('SELECT DISTINCT pac.id_attribute as id
			FROM `'._DB_PREFIX_.'product_attribute` pa
			LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (pac.id_product_attribute = pa.id_product_attribute)
			WHERE pa.id_product = '.(int)$this->id);
		return $result;
	}

	/**
	* Webservice getter : get virtual field position in category
	*
	* @return int
	*/
	public function getWsPositionInCategory()
	{
		$result = Db::getInstance()->executeS('SELECT position
			FROM `'._DB_PREFIX_.'category_product`
			WHERE id_category = '.(int)$this->id_category_default.'
			AND id_product = '.(int)$this->id);
		if (count($result) > 0)
			return $result[0]['position'];
		return '';
	}

	/**
	* Webservice getter : get virtual field id_default_image in category
	*
	* @return int
	*/
	public function getCoverWs()
	{
		$result = $this->getCover($this->id);
		return $result['id_image'];
	}

	/**
	* Webservice setter : set virtual field id_default_image in category
	*
	* @return bool
	*/
	public function setCoverWs($id_image)
	{
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'image`
									SET `cover` = 0 WHERE `id_product` = '.(int)$this->id.'
									');
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'image`
									SET `cover` = 1 WHERE `id_product` = '.(int)$this->id.' AND `id_image` = '.(int)$id_image);
		return true;
	}

	/**
	* Webservice getter : get image ids of current product for association
	*
	* @return array
	*/
	public function	getWsImages()
	{
		return Db::getInstance()->executeS('
		SELECT `id_image` as id
		FROM `'._DB_PREFIX_.'image`
		WHERE `id_product` = '.(int)$this->id.'
		ORDER BY `position`');
	}

	public function getWsTags()
	{
		return Db::getInstance()->executeS('
		SELECT `id_tag` as id
		FROM `'._DB_PREFIX_.'product_tag`
		WHERE `id_product` = '.(int)$this->id);
	}


	public function getWsManufacturerName()
	{
		return Manufacturer::getNameById((int)$this->id_manufacturer);
	}

	public static function resetEcoTax()
	{
		Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.'product`
		SET ecotax = 0
		');
	}

	/**
	 * Set Group reduction if needed
	 */
	public function setGroupReduction()
	{
		$row = GroupReduction::getGroupByCategoryId($this->id_category_default);
		if (!$row) // Remove
		{
			if (!GroupReduction::deleteProductReduction($this->id))
				return false;
		}
		else if (!GroupReduction::setProductReduction($this->id, $row['id_group'], $this->id_category_default, (float)$row['reduction']))
				return false;
		return true;
	}

	/**
	 * Checks if reference exists
	 * @return boolean
	 */
	public function existsRefInDatabase($reference)
	{
		$row = Db::getInstance()->getRow('
		SELECT `reference`
		FROM `'._DB_PREFIX_.'product` p
		WHERE p.reference = "'.pSQL($reference).'"');

		return isset($row['reference']);
	}

	/**
	 * Get all product attributes ids
	 *
	 * @since 1.5.0
	 * @param int $id_product the id of the product
	 * @return array product attribute id list
	 */
	public static function getProductAttributesIds($id_product)
	{
		return Db::getInstance()->executeS('
		SELECT id_product_attribute
		FROM `'._DB_PREFIX_.'product_attribute`
		WHERE `id_product` = '.(int)$id_product);
	}

	/**
	 * Get label by lang and value by lang too
	 * @todo Remove existing module condition
	 * @param int $id_product
	 * @param int $product_attribute_id
	 * @return array
	 */
	public static function getAttributesParams($id_product, $id_product_attribute)
	{
		// if blocklayered module is installed we check if user has set custom attribute name
		if (Module::isInstalled('blocklayered'))
		{
			$nb_custom_values = Db::getInstance()->executeS('
			SELECT DISTINCT la.`id_attribute`, la.`url_name` as `name`
			FROM `'._DB_PREFIX_.'attribute` a
			LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac
				ON (a.`id_attribute` = pac.`id_attribute`)
			LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
			LEFT JOIN `'._DB_PREFIX_.'layered_indexable_attribute_lang_value` la
				ON (la.`id_attribute` = a.`id_attribute` AND la.`id_lang` = '.(int)Context::getContext()->language->id.')
			WHERE la.`url_name` IS NOT NULL
			AND pa.`id_product` = '.(int)$id_product);

			if (!empty($nb_custom_values))
			{
				$tab_id_attribute = array();
				foreach ($nb_custom_values as $attribute)
				{
					$tab_id_attribute[] = $attribute['id_attribute'];

					$group = Db::getInstance()->executeS('
					SELECT g.`id_attribute_group`, g.`url_name` as `group`
					FROM `'._DB_PREFIX_.'layered_indexable_attribute_group_lang_value` g
					LEFT JOIN `'._DB_PREFIX_.'attribute` a
						ON (a.`id_attribute_group` = g.`id_attribute_group`)
					WHERE a.`id_attribute` = '.(int)$attribute['id_attribute'].'
					AND g.`id_lang` = '.(int)Context::getContext()->language->id.'
					AND g.`url_name` IS NOT NULL');
					if (empty($group))
					{
						$group = Db::getInstance()->executeS('
						SELECT g.`id_attribute_group`, g.`name` as `group`
						FROM `'._DB_PREFIX_.'attribute_group_lang` g
						LEFT JOIN `'._DB_PREFIX_.'attribute` a
							ON (a.`id_attribute_group` = g.`id_attribute_group`)
						WHERE a.`id_attribute` = '.(int)$attribute['id_attribute'].'
						AND g.`id_lang` = '.(int)Context::getContext()->language->id.'
						AND g.`name` IS NOT NULL');
					}
					$result[] = array_merge($attribute, $group[0]);
				}
				$values_not_custom = Db::getInstance()->executeS('
				SELECT DISTINCT a.`id_attribute_group`, al.`name`, agl.`name` as `group`
				FROM `'._DB_PREFIX_.'attribute` a
				LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
					ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)Context::getContext()->language->id.')
				LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
					ON (a.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)Context::getContext()->language->id.')
				LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac
					ON (a.`id_attribute` = pac.`id_attribute`)
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
					ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
				WHERE pa.`id_product` = '.(int)$id_product.'
				AND a.`id_attribute` NOT IN('.implode(', ', $tab_id_attribute).')');
				$result = array_merge($values_not_custom, $result);
			}
			else
			{
				$result = Db::getInstance()->executeS('
				SELECT al.`name`, agl.`name` as `group`
				FROM `'._DB_PREFIX_.'attribute` a
				LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
					ON (al.`id_attribute` = a.`id_attribute` AND al.`id_lang` = '.(int)Context::getContext()->language->id.')
				LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac
					ON (pac.`id_attribute` = a.`id_attribute`)
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
					ON (pa.`id_product_attribute` = pac.`id_product_attribute`)
				LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
					ON (a.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)Context::getContext()->language->id.')
				WHERE pa.`id_product` = '.(int)$id_product.'
					AND pac.`id_product_attribute` = '.(int)$id_product_attribute.'
					AND agl.`id_lang` = '.(int)Context::getContext()->language->id);
			}
		}
		else
		{
			$result = Db::getInstance()->executeS('
			SELECT al.`name`, agl.`name` as `group`
			FROM `'._DB_PREFIX_.'attribute` a
			LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
				ON (al.`id_attribute` = a.`id_attribute` AND al.`id_lang` = '.(int)Context::getContext()->language->id.')
			LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac
				ON (pac.`id_attribute` = a.`id_attribute`)
			LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (pa.`id_product_attribute` = pac.`id_product_attribute`)
			LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
				ON (a.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)Context::getContext()->language->id.')
			WHERE pa.`id_product` = '.(int)$id_product.'
				AND pac.`id_product_attribute` = '.(int)$id_product_attribute.'
				AND agl.`id_lang` = '.(int)Context::getContext()->language->id);
		}
		return $result;
	}

	/**
	 * @todo Remove existing module condition
	 * @param int $id_product
	 */
	public static function getAttributesInformationsByProduct($id_product)
	{
		// if blocklayered module is installed we check if user has set custom attribute name
		if (Module::isInstalled('blocklayered'))
		{
			$nb_custom_values = Db::getInstance()->executeS('
			SELECT DISTINCT la.`id_attribute`, la.`url_name` as `attribute`
			FROM `'._DB_PREFIX_.'attribute` a
			LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac
				ON (a.`id_attribute` = pac.`id_attribute`)
			LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
			LEFT JOIN `'._DB_PREFIX_.'layered_indexable_attribute_lang_value` la
				ON (la.`id_attribute` = a.`id_attribute` AND la.`id_lang` = '.(int)Context::getContext()->language->id.')
			WHERE la.`url_name` IS NOT NULL
			AND pa.`id_product` = '.(int)$id_product);

			if (!empty($nb_custom_values))
			{
				$tab_id_attribute = array();
				foreach ($nb_custom_values as $attribute)
				{
					$tab_id_attribute[] = $attribute['id_attribute'];

					$group = Db::getInstance()->executeS('
					SELECT g.`id_attribute_group`, g.`url_name` as `group`
					FROM `'._DB_PREFIX_.'layered_indexable_attribute_group_lang_value` g
					LEFT JOIN `'._DB_PREFIX_.'attribute` a
						ON (a.`id_attribute_group` = g.`id_attribute_group`)
					WHERE a.`id_attribute` = '.(int)$attribute['id_attribute'].'
					AND g.`id_lang` = '.(int)Context::getContext()->language->id.'
					AND g.`url_name` IS NOT NULL');
					if (empty($group))
					{
						$group = Db::getInstance()->executeS('
						SELECT g.`id_attribute_group`, g.`name` as `group`
						FROM `'._DB_PREFIX_.'attribute_group_lang` g
						LEFT JOIN `'._DB_PREFIX_.'attribute` a
							ON (a.`id_attribute_group` = g.`id_attribute_group`)
						WHERE a.`id_attribute` = '.(int)$attribute['id_attribute'].'
						AND g.`id_lang` = '.(int)Context::getContext()->language->id.'
						AND g.`name` IS NOT NULL');
					}
					$result[] = array_merge($attribute, $group[0]);
				}
				$values_not_custom = Db::getInstance()->executeS('
				SELECT DISTINCT a.`id_attribute`, a.`id_attribute_group`, a.`id_attribute_group`, al.`name` as `attribute`, agl.`name` as `group`
				FROM `'._DB_PREFIX_.'attribute` a
				LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
					ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)Context::getContext()->language->id.')
				LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
					ON (a.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)Context::getContext()->language->id.')
				LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac
					ON (a.`id_attribute` = pac.`id_attribute`)
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
					ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
				WHERE pa.`id_product` = '.(int)$id_product.'
				AND a.`id_attribute` NOT IN('.implode(', ', $tab_id_attribute).')');
				$result = array_merge($values_not_custom, $result);
			}
			else
			{
				$result = Db::getInstance()->executeS('
				SELECT DISTINCT a.`id_attribute`, a.`id_attribute_group`, al.`name` as `attribute`, agl.`name` as `group`
				FROM `'._DB_PREFIX_.'attribute` a
				LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
					ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)Context::getContext()->language->id.')
				LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
					ON (a.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)Context::getContext()->language->id.')
				LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac
					ON (a.`id_attribute` = pac.`id_attribute`)
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
					ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
				WHERE pa.`id_product` = '.(int)$id_product);
			}
		}
		else
		{
			$result = Db::getInstance()->executeS('
			SELECT DISTINCT a.`id_attribute`, a.`id_attribute_group`, al.`name` as `attribute`, agl.`name` as `group`
			FROM `'._DB_PREFIX_.'attribute` a
			LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
				ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)Context::getContext()->language->id.')
			LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
				ON (a.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)Context::getContext()->language->id.')
			LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac
				ON (a.`id_attribute` = pac.`id_attribute`)
			LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
			WHERE pa.`id_product` = '.(int)$id_product);
		}
		return $result;
	}

	/**
	 * Get the combination url anchor of the product
	 *
	 * @param integer $id_product_attribute
	 * @return string
	 */
	public function getAnchor($id_product_attribute)
	{
		$attributes = Product::getAttributesParams($this->id, $id_product_attribute);
		$anchor = '#';
		foreach ($attributes as &$a)
		{
			foreach ($a as &$b)
				$b = str_replace('-', '_', Tools::link_rewrite($b));
			$anchor .= '/'.$a['group'].'-'.$a['name'];
		}
		return $anchor;
	}

	/**
	 * Gets the name of a given product, in the given lang
	 *
	 * @since 1.5.0
	 * @param int $id_product
	 * @param int $id_product_attribute Optional
	 * @param int $id_lang Optional
	 * @return string
	 */
	public static function getProductName($id_product, $id_product_attribute = null, $id_lang = null)
	{
		// use the lang in the context if $id_lang is not defined
		if (!$id_lang)
			$id_lang = (int)Context::getContext()->language->id;

		// creates the query object
		$query = new DbQuery();

		// selects different names, if it is a combination
		if ($id_product_attribute)
			$query->select('IFNULL(CONCAT(pl.name, \' : \', GROUP_CONCAT(DISTINCT agl.`name`, \' - \', al.name SEPARATOR \', \')),pl.name) as name');
		else
			$query->select('DISTINCT pl.name as name');

		// queries different tables if it is a combination
		if ($id_product_attribute)
			$query->from('product_attribute pa');
		else
			$query->from('product_lang pl');

		// adds joins & where clauses for combinations
		if ($id_product_attribute)
		{
			$query->innerJoin('product_lang pl ON (pl.id_product = pa.id_product AND pl.id_lang = '.(int)$id_lang.')');
			$query->leftJoin('product_attribute_combination pac ON (pac.id_product_attribute = pa.id_product_attribute)');
			$query->leftJoin('attribute atr ON (atr.id_attribute = pac.id_attribute)');
			$query->leftJoin('attribute_lang al ON (al.id_attribute = atr.id_attribute AND al.id_lang = '.(int)$id_lang.')');
			$query->leftJoin('attribute_group_lang agl ON (agl.id_attribute_group = atr.id_attribute_group AND agl.id_lang = '.(int)$id_lang.')');
			$query->where('pa.id_product = '.(int)$id_product.' AND pa.id_product_attribute = '.(int)$id_product_attribute);
		}
		else // or just adds a 'where' clause for a simple product
			$query->where('pl.id_product = '.(int)$id_product);

		return Db::getInstance()->getValue($query);
	}

	public function addWs($autodate = true, $nullValues = false)
	{
		$success = parent::add($autodate, $nullValues);
		if ($success)
			Search::indexation(false, $this->id);
		return $success;
	}

	public function updateWs($nullValues = false)
	{
		$success = parent::update($nullValues);
		if ($success)
			Search::indexation(false, $this->id);
		return $success;
	}
}
