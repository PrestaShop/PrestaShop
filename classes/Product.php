<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * @deprecated 1.5.0.1
 */
define('_CUSTOMIZE_FILE_', 0);
/*
 * @deprecated 1.5.0.1
 */
define('_CUSTOMIZE_TEXTFIELD_', 1);

use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Core\Product\ProductInterface;

class ProductCore extends ObjectModel
{
    /** @var string Tax name */
    public $tax_name;

    /** @var string Tax rate */
    public $tax_rate;

    /** @var int Manufacturer id */
    public $id_manufacturer;

    /** @var int Supplier id */
    public $id_supplier;

    /** @var int default Category id */
    public $id_category_default;

    /** @var int default Shop id */
    public $id_shop_default;

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

    /** @var int Quantity available */
    public $quantity = 0;

    /** @var int Minimal quantity for add to cart */
    public $minimal_quantity = 1;

    /** @var int|null Low stock for mail alert */
    public $low_stock_threshold = null;

    /** @var bool Low stock mail alert activated */
    public $low_stock_alert = false;

    /** @var string available_now */
    public $available_now;

    /** @var string available_later */
    public $available_later;

    /** @var float Price in euros */
    public $price = 0;

    public $specificPrice = 0;

    /** @var float Additional shipping cost */
    public $additional_shipping_cost = 0;

    /** @var float Wholesale Price in euros */
    public $wholesale_price = 0;

    /** @var bool on_sale */
    public $on_sale = false;

    /** @var bool online_only */
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

    /**
     * @var string Supplier Reference
     *
     * @deprecated since 1.7.7.0
     */
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

    /** @var string ISBN */
    public $isbn;

    /** @var string Upc barcode */
    public $upc;

    /** @var string MPN */
    public $mpn;

    /** @var string Friendly URL */
    public $link_rewrite;

    /** @var string Meta tag description */
    public $meta_description;

    /** @var string Meta tag keywords */
    public $meta_keywords;

    /** @var string Meta tag title */
    public $meta_title;

    /** @var bool Product statuts */
    public $quantity_discount = 0;

    /** @var bool Product customization */
    public $customizable;

    /** @var bool Product is new */
    public $new = null;

    /** @var int Number of uploadable files (concerning customizable products) */
    public $uploadable_files;

    /** @var int Number of text fields */
    public $text_fields;

    /** @var bool Product statuts */
    public $active = true;

    /** @var bool Product statuts */
    public $redirect_type = '';

    /** @var bool Product statuts */
    public $id_type_redirected = 0;

    /** @var bool Product available for order */
    public $available_for_order = true;

    /** @var string Object available order date */
    public $available_date = '0000-00-00';

    /** @var bool Will the condition select should be visible for this product ? */
    public $show_condition = false;

    /** @var string Enumerated (enum) product condition (new, used, refurbished) */
    public $condition;

    /** @var bool Show price of Product */
    public $show_price = true;

    /** @var bool is the product indexed in the search index? */
    public $indexed = 0;

    /** @var string ENUM('both', 'catalog', 'search', 'none') front office visibility */
    public $visibility;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /** @var array Tags */
    public $tags;

    /** @var int temporary or saved object */
    public $state = self::STATE_SAVED;

    /**
     * @var float Base price of the product
     *
     * @deprecated 1.6.0.13
     */
    public $base_price;

    public $id_tax_rules_group = 1;

    /**
     * We keep this variable for retrocompatibility for themes.
     *
     * @deprecated 1.5.0
     */
    public $id_color_default = 0;

    /**
     * @since 1.5.0
     *
     * @var bool Tells if the product uses the advanced stock management
     */
    public $advanced_stock_management = 0;
    public $out_of_stock;
    public $depends_on_stock;

    public $isFullyLoaded = false;

    public $cache_is_pack;
    public $cache_has_attachments;
    public $is_virtual;
    public $id_pack_product_attribute;
    public $cache_default_attribute;

    /**
     * @var string If product is populated, this property contain the rewrite link of the default category
     */
    public $category;

    /**
     * @var int tell the type of stock management to apply on the pack
     */
    public $pack_stock_type = Pack::STOCK_TYPE_DEFAULT;

    /**
     * Type of delivery time.
     *
     * Choose which parameters use for give information delivery.
     * 0 - none
     * 1 - use default information
     * 2 - use product information
     *
     * @var int
     */
    public $additional_delivery_times = 1;

    /**
     * Delivery in-stock information.
     *
     * Long description for delivery in-stock product information.
     *
     * @var string
     */
    public $delivery_in_stock;

    /**
     * Delivery out-stock information.
     *
     * Long description for delivery out-stock product information.
     *
     * @var string
     */
    public $delivery_out_stock;

    public static $_taxCalculationMethod = null;
    protected static $_prices = [];
    protected static $_pricesLevel2 = [];
    protected static $_incat = [];

    /**
     * @since 1.5.6.1
     *
     * @var array is deprecated since 1.5.6.1
     */
    protected static $_cart_quantity = [];

    protected static $_tax_rules_group = [];
    protected static $_cacheFeatures = [];
    protected static $_frontFeaturesCache = [];
    protected static $productPropertiesCache = [];

    /** @var array cache stock data in getStock() method */
    protected static $cacheStock = [];

    const STATE_TEMP = 0;
    const STATE_SAVED = 1;

    public static $definition = [
        'table' => 'product',
        'primary' => 'id_product',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => [
            /* Classic fields */
            'id_shop_default' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_manufacturer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_supplier' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'reference' => ['type' => self::TYPE_STRING, 'validate' => 'isReference', 'size' => 64],
            'supplier_reference' => ['type' => self::TYPE_STRING, 'validate' => 'isReference', 'size' => 64],
            'location' => ['type' => self::TYPE_STRING, 'validate' => 'isReference', 'size' => 64],
            'width' => ['type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'],
            'height' => ['type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'],
            'depth' => ['type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'],
            'weight' => ['type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'],
            'quantity_discount' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'ean13' => ['type' => self::TYPE_STRING, 'validate' => 'isEan13', 'size' => 13],
            'isbn' => ['type' => self::TYPE_STRING, 'validate' => 'isIsbn', 'size' => 32],
            'upc' => ['type' => self::TYPE_STRING, 'validate' => 'isUpc', 'size' => 12],
            'mpn' => ['type' => self::TYPE_STRING, 'validate' => 'isMpn', 'size' => 40],
            'cache_is_pack' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'cache_has_attachments' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'is_virtual' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'state' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'additional_delivery_times' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'delivery_in_stock' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'size' => 255,
            ],
            'delivery_out_stock' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'size' => 255,
            ],

            /* Shop fields */
            'id_category_default' => ['type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedId'],
            'id_tax_rules_group' => ['type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedId'],
            'on_sale' => ['type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'],
            'online_only' => ['type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'],
            'ecotax' => ['type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice'],
            'minimal_quantity' => ['type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedInt'],
            'low_stock_threshold' => ['type' => self::TYPE_INT, 'shop' => true, 'allow_null' => true, 'validate' => 'isInt'],
            'low_stock_alert' => ['type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'],
            'price' => ['type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice', 'required' => true],
            'wholesale_price' => ['type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice'],
            'unity' => ['type' => self::TYPE_STRING, 'shop' => true, 'validate' => 'isString'],
            'unit_price_ratio' => ['type' => self::TYPE_FLOAT, 'shop' => true],
            'additional_shipping_cost' => ['type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice'],
            'customizable' => ['type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedInt'],
            'text_fields' => ['type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedInt'],
            'uploadable_files' => ['type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedInt'],
            'active' => ['type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'],
            'redirect_type' => ['type' => self::TYPE_STRING, 'shop' => true, 'validate' => 'isString'],
            'id_type_redirected' => ['type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedId'],
            'available_for_order' => ['type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'],
            'available_date' => ['type' => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDateFormat'],
            'show_condition' => ['type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'],
            'condition' => ['type' => self::TYPE_STRING, 'shop' => true, 'validate' => 'isGenericName', 'values' => ['new', 'used', 'refurbished'], 'default' => 'new'],
            'show_price' => ['type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'],
            'indexed' => ['type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'],
            'visibility' => ['type' => self::TYPE_STRING, 'shop' => true, 'validate' => 'isProductVisibility', 'values' => ['both', 'catalog', 'search', 'none'], 'default' => 'both'],
            'cache_default_attribute' => ['type' => self::TYPE_INT, 'shop' => true],
            'advanced_stock_management' => ['type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'],
            'date_add' => ['type' => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDate'],
            'pack_stock_type' => ['type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedInt'],

            /* Lang fields */
            'meta_description' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 512],
            'meta_keywords' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255],
            'meta_title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255],
            'link_rewrite' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isLinkRewrite',
                'required' => false,
                'size' => 128,
                'ws_modifier' => [
                    'http_method' => WebserviceRequest::HTTP_POST,
                    'modifier' => 'modifierWsLinkRewrite',
                ],
            ],
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => false, 'size' => 128],
            'description' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
            'description_short' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
            'available_now' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255],
            'available_later' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'IsGenericName', 'size' => 255],
        ],
        'associations' => [
            'manufacturer' => ['type' => self::HAS_ONE],
            'supplier' => ['type' => self::HAS_ONE],
            'default_category' => ['type' => self::HAS_ONE, 'field' => 'id_category_default', 'object' => 'Category'],
            'tax_rules_group' => ['type' => self::HAS_ONE],
            'categories' => ['type' => self::HAS_MANY, 'field' => 'id_category', 'object' => 'Category', 'association' => 'category_product'],
            'stock_availables' => ['type' => self::HAS_MANY, 'field' => 'id_stock_available', 'object' => 'StockAvailable', 'association' => 'stock_availables'],
        ],
    ];

    protected $webserviceParameters = [
        'objectMethods' => [
            'add' => 'addWs',
            'update' => 'updateWs',
        ],
        'objectNodeNames' => 'products',
        'fields' => [
            'id_manufacturer' => [
                'xlink_resource' => 'manufacturers',
            ],
            'id_supplier' => [
                'xlink_resource' => 'suppliers',
            ],
            'id_category_default' => [
                'xlink_resource' => 'categories',
            ],
            'new' => [],
            'cache_default_attribute' => [],
            'id_default_image' => [
                'getter' => 'getCoverWs',
                'setter' => 'setCoverWs',
                'xlink_resource' => [
                    'resourceName' => 'images',
                    'subResourceName' => 'products',
                ],
            ],
            'id_default_combination' => [
                'getter' => 'getWsDefaultCombination',
                'setter' => 'setWsDefaultCombination',
                'xlink_resource' => [
                    'resourceName' => 'combinations',
                ],
            ],
            'id_tax_rules_group' => [
                'xlink_resource' => [
                    'resourceName' => 'tax_rule_groups',
                ],
            ],
            'position_in_category' => [
                'getter' => 'getWsPositionInCategory',
                'setter' => 'setWsPositionInCategory',
            ],
            'manufacturer_name' => [
                'getter' => 'getWsManufacturerName',
                'setter' => false,
            ],
            'quantity' => [
                'getter' => false,
                'setter' => false,
            ],
            'type' => [
                'getter' => 'getWsType',
                'setter' => 'setWsType',
            ],
        ],
        'associations' => [
            'categories' => [
                'resource' => 'category',
                'fields' => [
                    'id' => ['required' => true],
                ],
            ],
            'images' => [
                'resource' => 'image',
                'fields' => ['id' => []],
            ],
            'combinations' => [
                'resource' => 'combination',
                'fields' => [
                    'id' => ['required' => true],
                ],
            ],
            'product_option_values' => [
                'resource' => 'product_option_value',
                'fields' => [
                    'id' => ['required' => true],
                ],
            ],
            'product_features' => [
                'resource' => 'product_feature',
                'fields' => [
                    'id' => ['required' => true],
                    'id_feature_value' => [
                        'required' => true,
                        'xlink_resource' => 'product_feature_values',
                    ],
                ],
            ],
            'tags' => ['resource' => 'tag',
                'fields' => [
                    'id' => ['required' => true],
                ], ],
            'stock_availables' => ['resource' => 'stock_available',
                'fields' => [
                    'id' => ['required' => true],
                    'id_product_attribute' => ['required' => true],
                ],
                'setter' => false,
            ],
            'accessories' => [
                'resource' => 'product',
                'api' => 'products',
                'fields' => [
                    'id' => [
                        'required' => true,
                        'xlink_resource' => 'product', ],
                ],
            ],
            'product_bundle' => [
                'resource' => 'product',
                'api' => 'products',
                'fields' => [
                    'id' => ['required' => true],
                    'id_product_attribute' => [],
                    'quantity' => [],
                ],
            ],
        ],
    ];

    const CUSTOMIZE_FILE = 0;
    const CUSTOMIZE_TEXTFIELD = 1;

    /**
     * Note:  prefix is "PTYPE" because TYPE_ is used in ObjectModel (definition).
     */
    const PTYPE_SIMPLE = 0;
    const PTYPE_PACK = 1;
    const PTYPE_VIRTUAL = 2;

    public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, Context $context = null)
    {
        parent::__construct($id_product, $id_lang, $id_shop);
        if ($full && $this->id) {
            if (!$context) {
                $context = Context::getContext();
            }

            $this->isFullyLoaded = $full;
            $this->tax_name = 'deprecated'; // The applicable tax may be BOTH the product one AND the state one (moreover this variable is some deadcode)
            $this->manufacturer_name = Manufacturer::getNameById((int) $this->id_manufacturer);
            $this->supplier_name = Supplier::getNameById((int) $this->id_supplier);
            $address = null;
            if (is_object($context->cart) && $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')} != null) {
                $address = $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
            }

            $this->tax_rate = $this->getTaxesRate(new Address($address));

            $this->new = $this->isNew();

            // Keep base price
            $this->base_price = $this->price;

            $this->price = Product::getPriceStatic((int) $this->id, false, null, 6, null, false, true, 1, false, null, null, null, $this->specificPrice);
            $this->unit_price = ($this->unit_price_ratio != 0 ? $this->price / $this->unit_price_ratio : 0);
            $this->tags = Tag::getProductTags((int) $this->id);

            $this->loadStockData();
        }

        if ($this->id_category_default) {
            $this->category = Category::getLinkRewrite((int) $this->id_category_default, (int) $id_lang);
        }
    }

    /**
     * @see ObjectModel::getFieldsShop()
     *
     * @return array
     */
    public function getFieldsShop()
    {
        $fields = parent::getFieldsShop();
        if (null === $this->update_fields || (!empty($this->update_fields['price']) && !empty($this->update_fields['unit_price']))) {
            $fields['unit_price_ratio'] = (float) $this->unit_price > 0 ? $this->price / $this->unit_price : 0;
        }
        $fields['unity'] = pSQL($this->unity);

        return $fields;
    }

    public function add($autodate = true, $null_values = false)
    {
        if (!parent::add($autodate, $null_values)) {
            return false;
        }

        $id_shop_list = Shop::getContextListShopID();
        if ($this->getType() == Product::PTYPE_VIRTUAL) {
            foreach ($id_shop_list as $value) {
                StockAvailable::setProductOutOfStock((int) $this->id, 1, $value);
            }

            if ($this->active && !Configuration::get('PS_VIRTUAL_PROD_FEATURE_ACTIVE')) {
                Configuration::updateGlobalValue('PS_VIRTUAL_PROD_FEATURE_ACTIVE', '1');
            }
        } else {
            foreach ($id_shop_list as $value) {
                StockAvailable::setProductOutOfStock((int) $this->id, 2, $value);
            }
        }

        $this->setGroupReduction();
        Hook::exec('actionProductSave', ['id_product' => (int) $this->id, 'product' => $this]);

        return true;
    }

    public function update($null_values = false)
    {
        $return = parent::update($null_values);
        $this->setGroupReduction();

        // Sync stock Reference, EAN13, MPN and UPC
        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && StockAvailable::dependsOnStock($this->id, Context::getContext()->shop->id)) {
            Db::getInstance()->update('stock', [
                'reference' => pSQL($this->reference),
                'ean13' => pSQL($this->ean13),
                'isbn' => pSQL($this->isbn),
                'upc' => pSQL($this->upc),
                'mpn' => pSQL($this->mpn),
            ], 'id_product = ' . (int) $this->id . ' AND id_product_attribute = 0');
        }

        Hook::exec('actionProductSave', ['id_product' => (int) $this->id, 'product' => $this]);
        Hook::exec('actionProductUpdate', ['id_product' => (int) $this->id, 'product' => $this]);
        if ($this->getType() == Product::PTYPE_VIRTUAL && $this->active && !Configuration::get('PS_VIRTUAL_PROD_FEATURE_ACTIVE')) {
            Configuration::updateGlobalValue('PS_VIRTUAL_PROD_FEATURE_ACTIVE', '1');
        }

        return $return;
    }

    /**
     * Init computation of price display method (i.e. price should be including tax or not) for a customer.
     * If customer Id passed as null then this compute price display method with according of current group.
     * Otherwise a price display method will compute with according of a customer address (i.e. country).
     *
     * @see Group::getPriceDisplayMethod()
     *
     * @param int|null $id_customer
     */
    public static function initPricesComputation($id_customer = null)
    {
        if ((int) $id_customer > 0) {
            $customer = new Customer((int) $id_customer);
            if (!Validate::isLoadedObject($customer)) {
                die(Tools::displayError());
            }
            self::$_taxCalculationMethod = Group::getPriceDisplayMethod((int) $customer->id_default_group);
            $cur_cart = Context::getContext()->cart;
            $id_address = 0;
            if (Validate::isLoadedObject($cur_cart)) {
                $id_address = (int) $cur_cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
            }
            $address_infos = Address::getCountryAndState($id_address);

            if (self::$_taxCalculationMethod != PS_TAX_EXC
                && !empty($address_infos['vat_number'])
                && $address_infos['id_country'] != Configuration::get('VATNUMBER_COUNTRY')
                && Configuration::get('VATNUMBER_MANAGEMENT')) {
                self::$_taxCalculationMethod = PS_TAX_EXC;
            }
        } else {
            self::$_taxCalculationMethod = Group::getPriceDisplayMethod(Group::getCurrent()->id);
        }
    }

    /**
     * Returns price display method for a customer (i.e. price should be including tax or not).
     *
     * @see initPricesComputation()
     *
     * @param int|null $id_customer
     *
     * @return int Returns 0 (PS_TAX_INC) if tax should be included, otherwise 1 (PS_TAX_EXC) - tax should be excluded
     */
    public static function getTaxCalculationMethod($id_customer = null)
    {
        if (self::$_taxCalculationMethod === null || $id_customer !== null) {
            Product::initPricesComputation($id_customer);
        }

        return (int) self::$_taxCalculationMethod;
    }

    /**
     * Move a product inside its category.
     *
     * @param bool $way Up (1)  or Down (0)
     * @param int $position
     *                      return boolean Update result
     */
    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS('
            SELECT cp.`id_product`, cp.`position`, cp.`id_category`
            FROM `' . _DB_PREFIX_ . 'category_product` cp
            WHERE cp.`id_category` = ' . (int) Tools::getValue('id_category', 1) . '
            ORDER BY cp.`position` ASC')
            ) {
            return false;
        }

        foreach ($res as $product) {
            if ((int) $product['id_product'] == (int) $this->id) {
                $moved_product = $product;
            }
        }

        if (!isset($moved_product) || !isset($position)) {
            return false;
        }

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        $result = (
            Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'category_product` cp
            INNER JOIN `' . _DB_PREFIX_ . 'product` p ON (p.`id_product` = cp.`id_product`)
            ' . Shop::addSqlAssociation('product', 'p') . '
            SET cp.`position`= `position` ' . ($way ? '- 1' : '+ 1') . ',
            p.`date_upd` = "' . date('Y-m-d H:i:s') . '", product_shop.`date_upd` = "' . date('Y-m-d H:i:s') . '"
            WHERE cp.`position`
            ' . ($way
                ? '> ' . (int) $moved_product['position'] . ' AND `position` <= ' . (int) $position
                : '< ' . (int) $moved_product['position'] . ' AND `position` >= ' . (int) $position) . '
            AND `id_category`=' . (int) $moved_product['id_category'])
        && Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'category_product` cp
            INNER JOIN `' . _DB_PREFIX_ . 'product` p ON (p.`id_product` = cp.`id_product`)
            ' . Shop::addSqlAssociation('product', 'p') . '
            SET cp.`position` = ' . (int) $position . ',
            p.`date_upd` = "' . date('Y-m-d H:i:s') . '", product_shop.`date_upd` = "' . date('Y-m-d H:i:s') . '"
            WHERE cp.`id_product` = ' . (int) $moved_product['id_product'] . '
            AND cp.`id_category`=' . (int) $moved_product['id_category'])
        );
        Hook::exec('actionProductUpdate', ['id_product' => (int) $this->id, 'product' => $this]);

        return $result;
    }

    /**
     * Reorder product position in category $id_category.
     * Call it after deleting a product from a category.
     *
     * @param int $id_category
     */
    public static function cleanPositions($id_category, $position = 0)
    {
        $return = true;

        if (!(int) $position) {
            $result = Db::getInstance()->executeS('
                SELECT `id_product`
                FROM `' . _DB_PREFIX_ . 'category_product`
                WHERE `id_category` = ' . (int) $id_category . '
                ORDER BY `position`
            ');
            $total = count($result);

            for ($i = 0; $i < $total; ++$i) {
                $return &= Db::getInstance()->update(
                    'category_product',
                    ['position' => $i],
                    '`id_category` = ' . (int) $id_category . ' AND `id_product` = ' . (int) $result[$i]['id_product']
                );
                $return &= Db::getInstance()->execute(
                    'UPDATE `' . _DB_PREFIX_ . 'product` p' . Shop::addSqlAssociation('product', 'p') . '
                    SET p.`date_upd` = "' . date('Y-m-d H:i:s') . '", product_shop.`date_upd` = "' . date('Y-m-d H:i:s') . '"
                    WHERE p.`id_product` = ' . (int) $result[$i]['id_product']
                );
            }
        } else {
            $result = Db::getInstance()->executeS('
                SELECT `id_product`
                FROM `' . _DB_PREFIX_ . 'category_product`
                WHERE `id_category` = ' . (int) $id_category . ' AND `position` > ' . (int) $position . '
                ORDER BY `position`
            ');
            $total = count($result);
            $return &= Db::getInstance()->update(
                'category_product',
                ['position' => ['type' => 'sql', 'value' => '`position`-1']],
                '`id_category` = ' . (int) $id_category . ' AND `position` > ' . (int) $position
            );

            for ($i = 0; $i < $total; ++$i) {
                $return &= Db::getInstance()->execute(
                    'UPDATE `' . _DB_PREFIX_ . 'product` p' . Shop::addSqlAssociation('product', 'p') . '
                    SET p.`date_upd` = "' . date('Y-m-d H:i:s') . '", product_shop.`date_upd` = "' . date('Y-m-d H:i:s') . '"
                    WHERE p.`id_product` = ' . (int) $result[$i]['id_product']
                );
            }
        }

        return $return;
    }

    /**
     * Get the default attribute for a product.
     *
     * @return int Attributes list
     */
    public static function getDefaultAttribute($id_product, $minimum_quantity = 0, $reset = false)
    {
        static $combinations = [];

        if (!Combination::isFeatureActive()) {
            return 0;
        }

        if ($reset && isset($combinations[$id_product])) {
            unset($combinations[$id_product]);
        }

        if (!isset($combinations[$id_product])) {
            $combinations[$id_product] = [];
        }
        if (isset($combinations[$id_product][$minimum_quantity])) {
            return $combinations[$id_product][$minimum_quantity];
        }

        $sql = 'SELECT product_attribute_shop.id_product_attribute
                FROM ' . _DB_PREFIX_ . 'product_attribute pa
                ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                WHERE pa.id_product = ' . (int) $id_product;

        $result_no_filter = Db::getInstance()->getValue($sql);
        if (!$result_no_filter) {
            $combinations[$id_product][$minimum_quantity] = 0;

            return 0;
        }

        $sql = 'SELECT product_attribute_shop.id_product_attribute
                FROM ' . _DB_PREFIX_ . 'product_attribute pa
                ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                ' . ($minimum_quantity > 0 ? Product::sqlStock('pa', 'pa') : '') .
                ' WHERE product_attribute_shop.default_on = 1 '
                . ($minimum_quantity > 0 ? ' AND IFNULL(stock.quantity, 0) >= ' . (int) $minimum_quantity : '') .
                ' AND pa.id_product = ' . (int) $id_product;
        $result = Db::getInstance()->getValue($sql);

        if (!$result) {
            $sql = 'SELECT product_attribute_shop.id_product_attribute
                    FROM ' . _DB_PREFIX_ . 'product_attribute pa
                    ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                    ' . ($minimum_quantity > 0 ? Product::sqlStock('pa', 'pa') : '') .
                    ' WHERE pa.id_product = ' . (int) $id_product
                    . ($minimum_quantity > 0 ? ' AND IFNULL(stock.quantity, 0) >= ' . (int) $minimum_quantity : '');

            $result = Db::getInstance()->getValue($sql);
        }

        if (!$result) {
            $sql = 'SELECT product_attribute_shop.id_product_attribute
                    FROM ' . _DB_PREFIX_ . 'product_attribute pa
                    ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                    WHERE product_attribute_shop.`default_on` = 1
                    AND pa.id_product = ' . (int) $id_product;

            $result = Db::getInstance()->getValue($sql);
        }

        if (!$result) {
            $result = $result_no_filter;
        }

        $combinations[$id_product][$minimum_quantity] = $result;

        return $result;
    }

    public function setAvailableDate($available_date = '0000-00-00')
    {
        if (Validate::isDateFormat($available_date) && $this->available_date != $available_date) {
            $this->available_date = $available_date;

            return $this->update();
        }

        return false;
    }

    /**
     * For a given id_product and id_product_attribute, return available date.
     *
     * @param int $id_product
     * @param int $id_product_attribute Optional
     *
     * @return string/null
     */
    public static function getAvailableDate($id_product, $id_product_attribute = null)
    {
        $sql = 'SELECT';

        if ($id_product_attribute === null) {
            $sql .= ' p.`available_date`';
        } else {
            $sql .= ' pa.`available_date`';
        }

        $sql .= ' FROM `' . _DB_PREFIX_ . 'product` p';

        if ($id_product_attribute !== null) {
            $sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON (pa.`id_product` = p.`id_product`)';
        }

        $sql .= Shop::addSqlAssociation('product', 'p');

        if ($id_product_attribute !== null) {
            $sql .= Shop::addSqlAssociation('product_attribute', 'pa');
        }

        $sql .= ' WHERE p.`id_product` = ' . (int) $id_product;

        if ($id_product_attribute !== null) {
            $sql .= ' AND pa.`id_product` = ' . (int) $id_product . ' AND pa.`id_product_attribute` = ' . (int) $id_product_attribute;
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

        if ($result == '0000-00-00') {
            $result = null;
        }

        return $result;
    }

    public static function updateIsVirtual($id_product, $is_virtual = true)
    {
        Db::getInstance()->update('product', [
            'is_virtual' => (bool) $is_virtual,
        ], 'id_product = ' . (int) $id_product);
    }

    /**
     * @see ObjectModel::validateField()
     *
     * reset static cache (eg unit testing purpose).
     */
    public static function resetStaticCache()
    {
        static::$loaded_classes = [];
        static::$productPropertiesCache = [];
        static::$_cacheFeatures = [];
        static::$_frontFeaturesCache = [];
        static::$_prices = [];
        static::$_pricesLevel2 = [];
        static::$_incat = [];
    }

    /**
     * @see ObjectModel::validateField()
     */
    public function validateField($field, $value, $id_lang = null, $skip = [], $human_errors = false)
    {
        if ($field == 'description_short') {
            $limit = (int) Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT');
            if ($limit <= 0) {
                $limit = 800;
            }

            $size_without_html = Tools::strlen(strip_tags($value));
            $size_with_html = Tools::strlen($value);
            $this->def['fields']['description_short']['size'] = $limit + $size_with_html - $size_without_html;
        }

        return parent::validateField($field, $value, $id_lang, $skip, $human_errors);
    }

    public function toggleStatus()
    {
        //test if the product is active and if redirect_type is empty string and set default value to id_type_redirected & redirect_type
        //  /!\ after parent::toggleStatus() active will be false, that why we set 404 by default :p
        if ($this->active) {
            //case where active will be false after parent::toggleStatus()
            $this->id_type_redirected = 0;
            $this->redirect_type = ProductInterface::REDIRECT_TYPE_CATEGORY_MOVED_PERMANENTLY;
        } else {
            //case where active will be true after parent::toggleStatus()
            $this->id_type_redirected = 0;
            $this->redirect_type = '';
        }

        return parent::toggleStatus();
    }

    public function delete()
    {
        /*
         * @since 1.5.0
         * It is NOT possible to delete a product if there are currently:
         * - physical stock for this product
         * - supply order(s) for this product
         */
        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $this->advanced_stock_management) {
            $stock_manager = StockManagerFactory::getManager();
            $physical_quantity = $stock_manager->getProductPhysicalQuantities($this->id, 0);
            $real_quantity = $stock_manager->getProductRealQuantities($this->id, 0);
            if ($physical_quantity > 0) {
                return false;
            }
            if ($real_quantity > $physical_quantity) {
                return false;
            }

            $warehouse_product_locations = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Core\\Foundation\\Database\\EntityManager')->getRepository('WarehouseProductLocation')->findByIdProduct($this->id);
            foreach ($warehouse_product_locations as $warehouse_product_location) {
                $warehouse_product_location->delete();
            }

            $stocks = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Core\\Foundation\\Database\\EntityManager')->getRepository('Stock')->findByIdProduct($this->id);
            foreach ($stocks as $stock) {
                $stock->delete();
            }
        }
        $result = parent::delete();

        // Removes the product from StockAvailable, for the current shop
        StockAvailable::removeProductFromStockAvailable($this->id);
        $result &= ($this->deleteProductAttributes() && $this->deleteImages());
        // If there are still entries in product_shop, don't remove completely the product
        if ($this->hasMultishopEntries()) {
            return true;
        }

        Hook::exec('actionProductDelete', ['id_product' => (int) $this->id, 'product' => $this]);
        if (!$result ||
            !GroupReduction::deleteProductReduction($this->id) ||
            !$this->deleteCategories(true) ||
            !$this->deleteProductFeatures() ||
            !$this->deleteTags() ||
            !$this->deleteCartProducts() ||
            !$this->deleteAttributesImpacts() ||
            !$this->deleteAttachments(false) ||
            !$this->deleteCustomization() ||
            !SpecificPrice::deleteByProductId((int) $this->id) ||
            !$this->deletePack() ||
            !$this->deleteProductSale() ||
            !$this->deleteSearchIndexes() ||
            !$this->deleteAccessories() ||
            !$this->deleteFromAccessories() ||
            !$this->deleteFromSupplier() ||
            !$this->deleteDownload() ||
            !$this->deleteFromCartRules()) {
            return false;
        }

        return true;
    }

    public function deleteSelection($products)
    {
        $return = 1;
        if (is_array($products) && ($count = count($products))) {
            // Deleting products can be quite long on a cheap server. Let's say 1.5 seconds by product (I've seen it!).
            if ((int) (ini_get('max_execution_time')) < round($count * 1.5)) {
                ini_set('max_execution_time', round($count * 1.5));
            }

            foreach ($products as $id_product) {
                $product = new Product((int) $id_product);
                $return &= $product->delete();
            }
        }

        return $return;
    }

    public function deleteFromCartRules()
    {
        CartRule::cleanProductRuleIntegrity('products', $this->id);

        return true;
    }

    public function deleteFromSupplier()
    {
        return Db::getInstance()->delete('product_supplier', 'id_product = ' . (int) $this->id);
    }

    /**
     * addToCategories add this product to the category/ies if not exists.
     *
     * @param mixed $categories id_category or array of id_category
     *
     * @return bool true if succeed
     */
    public function addToCategories($categories = [])
    {
        if (empty($categories)) {
            return false;
        }

        if (!is_array($categories)) {
            $categories = [$categories];
        }

        if (!count($categories)) {
            return false;
        }

        $categories = array_map('intval', $categories);

        $current_categories = $this->getCategories();
        $current_categories = array_map('intval', $current_categories);

        // for new categ, put product at last position
        $res_categ_new_pos = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT id_category, MAX(position)+1 newPos
            FROM `' . _DB_PREFIX_ . 'category_product`
            WHERE `id_category` IN(' . implode(',', $categories) . ')
            GROUP BY id_category');
        foreach ($res_categ_new_pos as $array) {
            $new_categories[(int) $array['id_category']] = (int) $array['newPos'];
        }

        $new_categ_pos = [];
        // The first position must be 1 instead of 0
        foreach ($categories as $id_category) {
            $new_categ_pos[$id_category] = isset($new_categories[$id_category]) ? $new_categories[$id_category] : 1;
        }

        $product_cats = [];

        foreach ($categories as $new_id_categ) {
            if (!in_array($new_id_categ, $current_categories)) {
                $product_cats[] = [
                    'id_category' => (int) $new_id_categ,
                    'id_product' => (int) $this->id,
                    'position' => (int) $new_categ_pos[$new_id_categ],
                ];
            }
        }

        Db::getInstance()->insert('category_product', $product_cats);

        Cache::clean('Product::getProductCategories_' . (int) $this->id);

        return true;
    }

    /**
     * Update categories to index product into.
     *
     * @param string $productCategories Categories list to index product into
     * @param bool $keeping_current_pos (deprecated, no more used)
     *
     * @return array Update/insertion result
     */
    public function updateCategories($categories, $keeping_current_pos = false)
    {
        if (empty($categories)) {
            return false;
        }

        $result = Db::getInstance()->executeS(
            '
            SELECT c.`id_category`
            FROM `' . _DB_PREFIX_ . 'category_product` cp
            LEFT JOIN `' . _DB_PREFIX_ . 'category` c ON (c.`id_category` = cp.`id_category`)
            ' . Shop::addSqlAssociation('category', 'c', true, null, true) . '
            WHERE cp.`id_category` NOT IN (' . implode(',', array_map('intval', $categories)) . ')
            AND cp.id_product = ' . (int) $this->id
        );

        // if none are found, it's an error
        if (!is_array($result)) {
            return false;
        }

        foreach ($result as $categ_to_delete) {
            $this->deleteCategory($categ_to_delete['id_category']);
        }

        if (!$this->addToCategories($categories)) {
            return false;
        }

        SpecificPriceRule::applyAllRules([(int) $this->id]);

        Cache::clean('Product::getProductCategories_' . (int) $this->id);

        return true;
    }

    /**
     * deleteCategory delete this product from the category $id_category.
     *
     * @param mixed $id_category
     * @param mixed $clean_positions
     *
     * @return bool
     */
    public function deleteCategory($id_category, $clean_positions = true)
    {
        $result = Db::getInstance()->executeS(
            'SELECT `id_category`, `position`
            FROM `' . _DB_PREFIX_ . 'category_product`
            WHERE `id_product` = ' . (int) $this->id . '
            AND id_category = ' . (int) $id_category . ''
        );

        $return = Db::getInstance()->delete('category_product', 'id_product = ' . (int) $this->id . ' AND id_category = ' . (int) $id_category);
        if ($clean_positions === true) {
            foreach ($result as $row) {
                self::cleanPositions((int) $row['id_category'], (int) $row['position']);
            }
        }

        SpecificPriceRule::applyAllRules([(int) $this->id]);

        Cache::clean('Product::getProductCategories_' . (int) $this->id);

        return $return;
    }

    /**
     * Delete all association to category where product is indexed.
     *
     * @param bool $clean_positions clean category positions after deletion
     *
     * @return array Deletion result
     */
    public function deleteCategories($clean_positions = false)
    {
        if ($clean_positions === true) {
            $result = Db::getInstance()->executeS(
                'SELECT `id_category`, `position`
                FROM `' . _DB_PREFIX_ . 'category_product`
                WHERE `id_product` = ' . (int) $this->id
            );
        }

        $return = Db::getInstance()->delete('category_product', 'id_product = ' . (int) $this->id);
        if ($clean_positions === true && is_array($result)) {
            foreach ($result as $row) {
                $return &= self::cleanPositions((int) $row['id_category'], (int) $row['position']);
            }
        }

        Cache::clean('Product::getProductCategories_' . (int) $this->id);

        return $return;
    }

    /**
     * Delete products tags entries.
     *
     * @return array Deletion result
     */
    public function deleteTags()
    {
        return Tag::deleteTagsForProduct((int) $this->id);
    }

    /**
     * Delete product from cart.
     *
     * @return array Deletion result
     */
    public function deleteCartProducts()
    {
        return Db::getInstance()->delete('cart_product', 'id_product = ' . (int) $this->id);
    }

    /**
     * Delete product images from database.
     *
     * @return bool success
     */
    public function deleteImages()
    {
        $result = Db::getInstance()->executeS(
            '
            SELECT `id_image`
            FROM `' . _DB_PREFIX_ . 'image`
            WHERE `id_product` = ' . (int) $this->id
        );

        $status = true;
        if ($result) {
            foreach ($result as $row) {
                $image = new Image($row['id_image']);
                $status &= $image->delete();
            }
        }

        return $status;
    }

    /**
     * Get all available products.
     *
     * @param int $id_lang Language id
     * @param int $start Start number
     * @param int $limit Number of products to return
     * @param string $order_by Field for ordering
     * @param string $order_way Way for ordering (ASC or DESC)
     *
     * @return array Products details
     */
    public static function getProducts(
        $id_lang,
        $start,
        $limit,
        $order_by,
        $order_way,
        $id_category = false,
        $only_active = false,
        Context $context = null
    ) {
        if (!$context) {
            $context = Context::getContext();
        }

        $front = true;
        if (!in_array($context->controller->controller_type, ['front', 'modulefront'])) {
            $front = false;
        }

        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'p';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        } elseif ($order_by == 'position') {
            $order_by_prefix = 'c';
        }

        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by_prefix = $order_by[0];
            $order_by = $order_by[1];
        }
        $sql = 'SELECT p.*, product_shop.*, pl.* , m.`name` AS manufacturer_name, s.`name` AS supplier_name
                FROM `' . _DB_PREFIX_ . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product` ' . Shop::addSqlRestrictionOnLang('pl') . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
                LEFT JOIN `' . _DB_PREFIX_ . 'supplier` s ON (s.`id_supplier` = p.`id_supplier`)' .
                ($id_category ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_product` c ON (c.`id_product` = p.`id_product`)' : '') . '
                WHERE pl.`id_lang` = ' . (int) $id_lang .
                    ($id_category ? ' AND c.`id_category` = ' . (int) $id_category : '') .
                    ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') .
                    ($only_active ? ' AND product_shop.`active` = 1' : '') . '
                ORDER BY ' . (isset($order_by_prefix) ? pSQL($order_by_prefix) . '.' : '') . '`' . pSQL($order_by) . '` ' . pSQL($order_way) .
                ($limit > 0 ? ' LIMIT ' . (int) $start . ',' . (int) $limit : '');
        $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if ($order_by == 'price') {
            Tools::orderbyPrice($rq, $order_way);
        }

        foreach ($rq as &$row) {
            $row = Product::getTaxesInformations($row);
        }

        return $rq;
    }

    public static function getSimpleProducts($id_lang, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $front = true;
        if (!in_array($context->controller->controller_type, ['front', 'modulefront'])) {
            $front = false;
        }

        $sql = 'SELECT p.`id_product`, pl.`name`
                FROM `' . _DB_PREFIX_ . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product` ' . Shop::addSqlRestrictionOnLang('pl') . ')
                WHERE pl.`id_lang` = ' . (int) $id_lang . '
                ' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
                ORDER BY pl.`name`';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function isNew()
    {
        $result = Db::getInstance()->executeS('
            SELECT p.id_product
            FROM `' . _DB_PREFIX_ . 'product` p
            ' . Shop::addSqlAssociation('product', 'p') . '
            WHERE p.id_product = ' . (int) $this->id . '
            AND DATEDIFF(
                product_shop.`date_add`,
                DATE_SUB(
                    "' . date('Y-m-d') . ' 00:00:00",
                    INTERVAL ' . (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY
                )
            ) > 0
        ', true, false);

        return count($result) > 0;
    }

    public function productAttributeExists($attributes_list, $current_product_attribute = false, Context $context = null, $all_shops = false, $return_id = false)
    {
        if (!Combination::isFeatureActive()) {
            return false;
        }
        if ($context === null) {
            $context = Context::getContext();
        }
        $result = Db::getInstance()->executeS(
            'SELECT pac.`id_attribute`, pac.`id_product_attribute`
            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
            JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` pas ON (pas.id_product_attribute = pa.id_product_attribute)
            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
            WHERE 1 ' . (!$all_shops ? ' AND pas.id_shop =' . (int) $context->shop->id : '') . ' AND pa.`id_product` = ' . (int) $this->id .
            ($all_shops ? ' GROUP BY pac.id_attribute, pac.id_product_attribute ' : '')
        );

        /* If something's wrong */
        if (!$result || empty($result)) {
            return false;
        }
        /* Product attributes simulation */
        $product_attributes = [];
        foreach ($result as $product_attribute) {
            $product_attributes[$product_attribute['id_product_attribute']][] = $product_attribute['id_attribute'];
        }
        /* Checking product's attribute existence */
        foreach ($product_attributes as $key => $product_attribute) {
            if (count($product_attribute) == count($attributes_list)) {
                $diff = false;
                for ($i = 0; $diff == false && isset($product_attribute[$i]); ++$i) {
                    if (!in_array($product_attribute[$i], $attributes_list) || $key == $current_product_attribute) {
                        $diff = true;
                    }
                }
                if (!$diff) {
                    if ($return_id) {
                        return $key;
                    }

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * addProductAttribute is deprecated.
     *
     * The quantity params now set StockAvailable for the current shop with the specified quantity
     * The supplier_reference params now set the supplier reference of the default supplier of the product if possible
     *
     * @see StockManager if you want to manage real stock
     * @see StockAvailable if you want to manage available quantities for sale on your shop(s)
     * @see ProductSupplier for manage supplier reference(s)
     * @deprecated since 1.5.0
     */
    public function addProductAttribute(
        $price,
        $weight,
        $unit_impact,
        $ecotax,
        $quantity,
        $id_images,
        $reference,
        $id_supplier,
        $ean13,
        $default,
        $location,
        $upc,
        $minimal_quantity,
        $isbn,
        $low_stock_threshold = null,
        $low_stock_alert = false,
        $mpn = null
    ) {
        Tools::displayAsDeprecated();

        $id_product_attribute = $this->addAttribute(
            $price,
            $weight,
            $unit_impact,
            $ecotax,
            $id_images,
            $reference,
            $ean13,
            $default,
            $location,
            $upc,
            $minimal_quantity,
            [],
            null,
            0,
            $isbn,
            $low_stock_threshold,
            $low_stock_alert,
            $mpn
        );

        if (!$id_product_attribute) {
            return false;
        }

        StockAvailable::setQuantity($this->id, $id_product_attribute, $quantity);
        //Try to set the default supplier reference
        $this->addSupplierReference($id_supplier, $id_product_attribute);

        return $id_product_attribute;
    }

    public function generateMultipleCombinations($combinations, $attributes, $resetExistingCombination = true)
    {
        $res = true;
        foreach ($combinations as $key => $combination) {
            $id_combination = (int) $this->productAttributeExists($attributes[$key], false, null, true, true);
            if ($id_combination && !$resetExistingCombination) {
                continue;
            }

            $obj = new Combination($id_combination);

            if ($id_combination) {
                $obj->minimal_quantity = 1;
                $obj->available_date = '0000-00-00';
            }

            foreach ($combination as $field => $value) {
                $obj->$field = $value;
            }

            $obj->default_on = 0;
            $this->setAvailableDate();

            $obj->save();

            if (!$id_combination) {
                $attribute_list = [];
                foreach ($attributes[$key] as $id_attribute) {
                    $attribute_list[] = [
                        'id_product_attribute' => (int) $obj->id,
                        'id_attribute' => (int) $id_attribute,
                    ];
                }
                $res &= Db::getInstance()->insert('product_attribute_combination', $attribute_list);
            }
        }

        return $res;
    }

    public function sortCombinationByAttributePosition($combinations, $langId)
    {
        $attributes = [];
        foreach ($combinations as $combinationId) {
            $attributeCombination = $this->getAttributeCombinationsById($combinationId, $langId);
            $attributes[$attributeCombination[0]['position']][$combinationId] = $attributeCombination[0];
        }

        ksort($attributes);

        return $attributes;
    }

    /**
     * @param int $quantity DEPRECATED
     * @param string $supplier_reference DEPRECATED
     */
    public function addCombinationEntity(
        $wholesale_price,
        $price,
        $weight,
        $unit_impact,
        $ecotax,
        $quantity,
        $id_images,
        $reference,
        $id_supplier,
        $ean13,
        $default,
        $location = null,
        $upc = null,
        $minimal_quantity = 1,
        array $id_shop_list = [],
        $available_date = null,
        $isbn = '',
        $low_stock_threshold = null,
        $low_stock_alert = false,
        $mpn = null
    ) {
        $id_product_attribute = $this->addAttribute(
            $price,
            $weight,
            $unit_impact,
            $ecotax,
            $id_images,
            $reference,
            $ean13,
            $default,
            $location,
            $upc,
            $minimal_quantity,
            $id_shop_list,
            $available_date,
            0,
            $isbn,
            $low_stock_threshold,
            $low_stock_alert,
            $mpn
        );
        $this->addSupplierReference($id_supplier, $id_product_attribute);
        $result = ObjectModel::updateMultishopTable('Combination', [
            'wholesale_price' => (float) $wholesale_price,
        ], 'a.id_product_attribute = ' . (int) $id_product_attribute);

        if (!$id_product_attribute || !$result) {
            return false;
        }

        return $id_product_attribute;
    }

    /**
     * @deprecated 1.5.5.0
     *
     * @param $attributes
     * @param bool $set_default
     *
     * @return array
     */
    public function addProductAttributeMultiple($attributes, $set_default = true)
    {
        Tools::displayAsDeprecated();
        $return = [];
        $default_value = 1;
        foreach ($attributes as &$attribute) {
            $obj = new Combination();
            foreach ($attribute as $key => $value) {
                $obj->$key = $value;
            }

            if ($set_default) {
                $obj->default_on = $default_value;
                $default_value = 0;
                // if we add a combination for this shop and this product does not use the combination feature in other shop,
                // we clone the default combination in every shop linked to this product
                if (!$this->hasAttributesInOtherShops()) {
                    $id_shop_list_array = Product::getShopsByProduct($this->id);
                    $id_shop_list = [];
                    foreach ($id_shop_list_array as $array_shop) {
                        $id_shop_list[] = $array_shop['id_shop'];
                    }
                    $obj->id_shop_list = $id_shop_list;
                }
            }
            $obj->add();
            $return[] = $obj->id;
        }

        return $return;
    }

    /**
     * Del all default attributes for product.
     */
    public function deleteDefaultAttributes()
    {
        return ObjectModel::updateMultishopTable('Combination', [
            'default_on' => null,
        ], 'a.`id_product` = ' . (int) $this->id);
    }

    public function setDefaultAttribute($id_product_attribute)
    {
        $result = ObjectModel::updateMultishopTable('Combination', [
            'default_on' => 1,
        ], 'a.`id_product` = ' . (int) $this->id . ' AND a.`id_product_attribute` = ' . (int) $id_product_attribute);

        $result &= ObjectModel::updateMultishopTable('product', [
            'cache_default_attribute' => (int) $id_product_attribute,
        ], 'a.`id_product` = ' . (int) $this->id);
        $this->cache_default_attribute = (int) $id_product_attribute;

        return $result;
    }

    public static function updateDefaultAttribute($id_product)
    {
        $id_default_attribute = (int) Product::getDefaultAttribute($id_product, 0, true);

        $result = Db::getInstance()->update('product_shop', [
            'cache_default_attribute' => $id_default_attribute,
        ], 'id_product = ' . (int) $id_product . Shop::addSqlRestriction());

        $result &= Db::getInstance()->update('product', [
            'cache_default_attribute' => $id_default_attribute,
        ], 'id_product = ' . (int) $id_product);

        if ($result && $id_default_attribute) {
            return $id_default_attribute;
        } else {
            return $result;
        }
    }

    /**
     * Update a product attribute.
     *
     * @deprecated since 1.5
     * @see updateAttribute() to use instead
     * @see ProductSupplier for manage supplier reference(s)
     */
    public function updateProductAttribute(
        $id_product_attribute,
        $wholesale_price,
        $price,
        $weight,
        $unit,
        $ecotax,
        $id_images,
        $reference,
        $id_supplier,
        $ean13,
        $default,
        $location,
        $upc,
        $minimal_quantity,
        $available_date,
        $isbn = '',
        $low_stock_threshold = null,
        $low_stock_alert = false,
        $mpn = null
    ) {
        Tools::displayAsDeprecated('Use updateAttribute() instead');

        $return = $this->updateAttribute(
            $id_product_attribute,
            $wholesale_price,
            $price,
            $weight,
            $unit,
            $ecotax,
            $id_images,
            $reference,
            $ean13,
            $default,
            $location = null,
            $upc = null,
            $minimal_quantity,
            $available_date,
            true,
            [],
            $isbn,
            $low_stock_threshold,
            $low_stock_alert,
            $mpn = null
        );
        $this->addSupplierReference($id_supplier, $id_product_attribute);

        return $return;
    }

    /**
     * Sets or updates Supplier Reference.
     *
     * @param int $id_supplier
     * @param int $id_product_attribute
     * @param string $supplier_reference
     * @param float $price
     * @param int $id_currency
     */
    public function addSupplierReference($id_supplier, $id_product_attribute, $supplier_reference = null, $price = null, $id_currency = null)
    {
        //in some case we need to add price without supplier reference
        if ($supplier_reference === null) {
            $supplier_reference = '';
        }

        //Try to set the default supplier reference
        if (($id_supplier > 0) && ($this->id > 0)) {
            $id_product_supplier = (int) ProductSupplier::getIdByProductAndSupplier($this->id, $id_product_attribute, $id_supplier);

            $product_supplier = new ProductSupplier($id_product_supplier);

            if (!$id_product_supplier) {
                $product_supplier->id_product = (int) $this->id;
                $product_supplier->id_product_attribute = (int) $id_product_attribute;
                $product_supplier->id_supplier = (int) $id_supplier;
            }

            $product_supplier->product_supplier_reference = pSQL($supplier_reference);
            $product_supplier->product_supplier_price_te = null !== $price ? (float) $price : (float) $product_supplier->product_supplier_price_te;
            $product_supplier->id_currency = null !== $id_currency ? (int) $id_currency : (int) $product_supplier->id_currency;
            $product_supplier->save();
        }
    }

    /**
     * Update a product attribute.
     *
     * @param int $id_product_attribute Product attribute id
     * @param float $wholesale_price Wholesale price
     * @param float $price Additional price
     * @param float $weight Additional weight
     * @param float $unit
     * @param float $ecotax Additional ecotax
     * @param int $id_image Image id
     * @param string $reference Reference
     * @param string $ean13 Ean-13 barcode
     * @param int $default Default On
     * @param string $upc Upc barcode
     * @param string $minimal_quantity Minimal quantity
     * @param string $isbn ISBN reference
     * @param int|null $low_stock_threshold Low stock alert
     * @param bool $low_stock_alert send email on low stock
     * @param string $mpn MPN
     *
     * @return array Update result
     */
    public function updateAttribute(
        $id_product_attribute,
        $wholesale_price,
        $price,
        $weight,
        $unit,
        $ecotax,
        $id_images,
        $reference,
        $ean13,
        $default,
        $location = null,
        $upc = null,
        $minimal_quantity = null,
        $available_date = null,
        $update_all_fields = true,
        array $id_shop_list = [],
        $isbn = '',
        $low_stock_threshold = null,
        $low_stock_alert = false,
        $mpn = null
    ) {
        $combination = new Combination($id_product_attribute);

        if (!$update_all_fields) {
            $combination->setFieldsToUpdate([
                'price' => null !== $price,
                'wholesale_price' => null !== $wholesale_price,
                'ecotax' => null !== $ecotax,
                'weight' => null !== $weight,
                'unit_price_impact' => null !== $unit,
                'default_on' => null !== $default,
                'minimal_quantity' => null !== $minimal_quantity,
                'available_date' => null !== $available_date,
            ]);
        }

        $price = str_replace(',', '.', $price);
        $weight = str_replace(',', '.', $weight);

        $combination->price = (float) $price;
        $combination->wholesale_price = (float) $wholesale_price;
        $combination->ecotax = (float) $ecotax;
        $combination->weight = (float) $weight;
        $combination->unit_price_impact = (float) $unit;
        $combination->reference = pSQL($reference);
        $combination->location = pSQL($location);
        $combination->ean13 = pSQL($ean13);
        $combination->isbn = pSQL($isbn);
        $combination->upc = pSQL($upc);
        $combination->mpn = pSQL($mpn);
        $combination->default_on = (int) $default;
        $combination->minimal_quantity = (int) $minimal_quantity;
        $combination->low_stock_threshold = empty($low_stock_threshold) && '0' != $low_stock_threshold ? null : (int) $low_stock_threshold;
        $combination->low_stock_alert = !empty($low_stock_alert);
        $combination->available_date = $available_date ? pSQL($available_date) : '0000-00-00';

        if (count($id_shop_list)) {
            $combination->id_shop_list = $id_shop_list;
        }

        $combination->save();

        if (is_array($id_images) && count($id_images)) {
            $combination->setImages($id_images);
        }

        $id_default_attribute = (int) Product::updateDefaultAttribute($this->id);
        if ($id_default_attribute) {
            $this->cache_default_attribute = $id_default_attribute;
        }

        // Sync stock Reference, EAN13, ISBN, MPN and UPC for this attribute
        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && StockAvailable::dependsOnStock($this->id, Context::getContext()->shop->id)) {
            Db::getInstance()->update('stock', [
                'reference' => pSQL($reference),
                'ean13' => pSQL($ean13),
                'isbn' => pSQL($isbn),
                'upc' => pSQL($upc),
                'mpn' => pSQL($mpn),
            ], 'id_product = ' . $this->id . ' AND id_product_attribute = ' . (int) $id_product_attribute);
        }

        Hook::exec('actionProductAttributeUpdate', ['id_product_attribute' => (int) $id_product_attribute]);
        Tools::clearColorListCache($this->id);

        return true;
    }

    /**
     * Add a product attribute.
     *
     * @since 1.5.0.1
     *
     * @param float $price Additional price
     * @param float $weight Additional weight
     * @param float $ecotax Additional ecotax
     * @param int $id_images Image ids
     * @param string $reference Reference
     * @param string $location Location
     * @param string $ean13 Ean-13 barcode
     * @param bool $default Is default attribute for product
     * @param int $minimal_quantity Minimal quantity to add to cart
     * @param string $isbn ISBN reference
     * @param int|null $low_stock Low stock alert
     *
     * @return mixed $id_product_attribute or false
     */
    public function addAttribute(
        $price,
        $weight,
        $unit_impact,
        $ecotax,
        $id_images,
        $reference,
        $ean13,
        $default,
        $location = null,
        $upc = null,
        $minimal_quantity = 1,
        array $id_shop_list = [],
        $available_date = null,
        $quantity = 0,
        $isbn = '',
        $low_stock_threshold = null,
        $low_stock_alert = false,
        $mpn = null
    ) {
        if (!$this->id) {
            return;
        }

        $price = str_replace(',', '.', $price);
        $weight = str_replace(',', '.', $weight);

        $combination = new Combination();
        $combination->id_product = (int) $this->id;
        $combination->price = (float) $price;
        $combination->ecotax = (float) $ecotax;
        $combination->quantity = (int) $quantity;
        $combination->weight = (float) $weight;
        $combination->unit_price_impact = (float) $unit_impact;
        $combination->reference = pSQL($reference);
        $combination->location = pSQL($location);
        $combination->ean13 = pSQL($ean13);
        $combination->isbn = pSQL($isbn);
        $combination->upc = pSQL($upc);
        $combination->mpn = pSQL($mpn);
        $combination->default_on = (int) $default;
        $combination->minimal_quantity = (int) $minimal_quantity;
        $combination->low_stock_threshold = empty($low_stock_threshold) && '0' != $low_stock_threshold ? null : (int) $low_stock_threshold;
        $combination->low_stock_alert = !empty($low_stock_alert);
        $combination->available_date = $available_date;

        if (count($id_shop_list)) {
            $combination->id_shop_list = array_unique($id_shop_list);
        }

        $combination->add();

        if (!$combination->id) {
            return false;
        }

        $total_quantity = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            '
            SELECT SUM(quantity) as quantity
            FROM ' . _DB_PREFIX_ . 'stock_available
            WHERE id_product = ' . (int) $this->id . '
            AND id_product_attribute <> 0 '
        );

        if (!$total_quantity) {
            Db::getInstance()->update('stock_available', ['quantity' => 0], '`id_product` = ' . $this->id);
        }

        $id_default_attribute = Product::updateDefaultAttribute($this->id);

        if ($id_default_attribute) {
            $this->cache_default_attribute = $id_default_attribute;
            if (!$combination->available_date) {
                $this->setAvailableDate();
            }
        }

        if (!empty($id_images)) {
            $combination->setImages($id_images);
        }

        Tools::clearColorListCache($this->id);

        if (Configuration::get('PS_DEFAULT_WAREHOUSE_NEW_PRODUCT') != 0 && Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
            $warehouse_location_entity = new WarehouseProductLocation();
            $warehouse_location_entity->id_product = $this->id;
            $warehouse_location_entity->id_product_attribute = (int) $combination->id;
            $warehouse_location_entity->id_warehouse = Configuration::get('PS_DEFAULT_WAREHOUSE_NEW_PRODUCT');
            $warehouse_location_entity->location = pSQL('');
            $warehouse_location_entity->save();
        }

        return (int) $combination->id;
    }

    /**
     * @deprecated since 1.5.0
     */
    public function updateQuantityProductWithAttributeQuantity()
    {
        Tools::displayAsDeprecated();

        return Db::getInstance()->execute('
        UPDATE `' . _DB_PREFIX_ . 'product`
        SET `quantity` = IFNULL(
        (
            SELECT SUM(`quantity`)
            FROM `' . _DB_PREFIX_ . 'product_attribute`
            WHERE `id_product` = ' . (int) $this->id . '
        ), \'0\')
        WHERE `id_product` = ' . (int) $this->id);
    }

    /**
     * Delete product attributes.
     *
     * @return array Deletion result
     */
    public function deleteProductAttributes()
    {
        Hook::exec('actionProductAttributeDelete', ['id_product_attribute' => 0, 'id_product' => (int) $this->id, 'deleteAllAttributes' => true]);

        $result = true;
        $combinations = new PrestaShopCollection('Combination');
        $combinations->where('id_product', '=', $this->id);
        foreach ($combinations as $combination) {
            $result &= $combination->delete();
        }
        SpecificPriceRule::applyAllRules([(int) $this->id]);
        Tools::clearColorListCache($this->id);

        return $result;
    }

    /**
     * Delete product attributes impacts.
     *
     * @return bool
     */
    public function deleteAttributesImpacts()
    {
        return Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'attribute_impact`
            WHERE `id_product` = ' . (int) $this->id
        );
    }

    /**
     * Delete product features.
     *
     * @return array Deletion result
     */
    public function deleteProductFeatures()
    {
        SpecificPriceRule::applyAllRules([(int) $this->id]);

        return $this->deleteFeatures();
    }

    public static function updateCacheAttachment($id_product)
    {
        $value = (bool) Db::getInstance()->getValue('
                                SELECT id_attachment
                                FROM ' . _DB_PREFIX_ . 'product_attachment
                                WHERE id_product=' . (int) $id_product);

        return Db::getInstance()->update(
                        'product',
                        ['cache_has_attachments' => (int) $value],
                        'id_product = ' . (int) $id_product
                    );
    }

    /**
     * Delete product attachments.
     *
     * @param bool $update_cache If set to true attachment cache will be updated
     *
     * @return array Deletion result
     */
    public function deleteAttachments($update_attachment_cache = true)
    {
        $res = Db::getInstance()->execute(
            '
            DELETE FROM `' . _DB_PREFIX_ . 'product_attachment`
            WHERE `id_product` = ' . (int) $this->id
        );

        if (isset($update_attachment_cache) && (bool) $update_attachment_cache === true) {
            Product::updateCacheAttachment((int) $this->id);
        }

        return $res;
    }

    /**
     * Delete product customizations.
     *
     * @return array Deletion result
     */
    public function deleteCustomization()
    {
        return
            Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'customization_field`
                WHERE `id_product` = ' . (int) $this->id
            )
            &&
            Db::getInstance()->execute(
                'DELETE `' . _DB_PREFIX_ . 'customization_field_lang` FROM `' . _DB_PREFIX_ . 'customization_field_lang` LEFT JOIN `' . _DB_PREFIX_ . 'customization_field`
                ON (' . _DB_PREFIX_ . 'customization_field.id_customization_field = ' . _DB_PREFIX_ . 'customization_field_lang.id_customization_field)
                WHERE ' . _DB_PREFIX_ . 'customization_field.id_customization_field IS NULL'
            );
    }

    /**
     * Delete product pack details.
     *
     * @return array Deletion result
     */
    public function deletePack()
    {
        return Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'pack`
            WHERE `id_product_pack` = ' . (int) $this->id . '
            OR `id_product_item` = ' . (int) $this->id
        );
    }

    /**
     * Delete product sales.
     *
     * @return array Deletion result
     */
    public function deleteProductSale()
    {
        return Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'product_sale`
            WHERE `id_product` = ' . (int) $this->id
        );
    }

    /**
     * Delete product indexed words.
     *
     * @return array Deletion result
     */
    public function deleteSearchIndexes()
    {
        return
            Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'search_index`
                WHERE `id_product` = ' . (int) $this->id
            ) &&
            Db::getInstance()->execute(
                'DELETE sw FROM `' . _DB_PREFIX_ . 'search_word` sw
                LEFT JOIN `' . _DB_PREFIX_ . 'search_index` si ON (sw.id_word=si.id_word)
                WHERE si.id_word IS NULL;'
            );
    }

    /**
     * Add a product attributes combinaison.
     *
     * @param int $id_product_attribute Product attribute id
     * @param array $attributes Attributes to forge combinaison
     *
     * @return array Insertion result
     *
     * @deprecated since 1.5.0.7
     */
    public function addAttributeCombinaison($id_product_attribute, $attributes)
    {
        Tools::displayAsDeprecated();
        if (!is_array($attributes)) {
            die(Tools::displayError());
        }
        if (!count($attributes)) {
            return false;
        }

        $combination = new Combination((int) $id_product_attribute);

        return $combination->setAttributes($attributes);
    }

    /**
     * @deprecated 1.5.5.0
     *
     * @param $id_attributes
     * @param $combinations
     *
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     */
    public function addAttributeCombinationMultiple($id_attributes, $combinations)
    {
        Tools::displayAsDeprecated();
        $attributes_list = [];
        foreach ($id_attributes as $nb => $id_product_attribute) {
            if (isset($combinations[$nb])) {
                foreach ($combinations[$nb] as $id_attribute) {
                    $attributes_list[] = [
                        'id_product_attribute' => (int) $id_product_attribute,
                        'id_attribute' => (int) $id_attribute,
                    ];
                }
            }
        }

        return Db::getInstance()->insert('product_attribute_combination', $attributes_list);
    }

    /**
     * Delete a product attributes combination.
     *
     * @param int $id_product_attribute Product attribute id
     *
     * @return array Deletion result
     */
    public function deleteAttributeCombination($id_product_attribute)
    {
        if (!$this->id || !$id_product_attribute || !is_numeric($id_product_attribute)) {
            return false;
        }

        Hook::exec(
            'deleteProductAttribute',
            [
                'id_product_attribute' => $id_product_attribute,
                'id_product' => $this->id,
                'deleteAllAttributes' => false,
            ]
        );

        $combination = new Combination($id_product_attribute);
        $res = $combination->delete();
        SpecificPriceRule::applyAllRules([(int) $this->id]);

        return $res;
    }

    /**
     * Delete features.
     */
    public function deleteFeatures()
    {
        $all_shops = Context::getContext()->shop->getContext() == Shop::CONTEXT_ALL ? true : false;

        // List products features
        $features = Db::getInstance()->executeS(
            '
            SELECT p.*, f.*
            FROM `' . _DB_PREFIX_ . 'feature_product` as p
            LEFT JOIN `' . _DB_PREFIX_ . 'feature_value` as f ON (f.`id_feature_value` = p.`id_feature_value`)
            ' . (!$all_shops ? 'LEFT JOIN `' . _DB_PREFIX_ . 'feature_shop` fs ON (f.`id_feature` = fs.`id_feature`)' : null) . '
            WHERE `id_product` = ' . (int) $this->id
                . (!$all_shops ? ' AND fs.`id_shop` = ' . (int) Context::getContext()->shop->id : '')
        );

        foreach ($features as $tab) {
            // Delete product custom features
            if ($tab['custom']) {
                Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'feature_value` WHERE `id_feature_value` = ' . (int) $tab['id_feature_value']);
                Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'feature_value_lang` WHERE `id_feature_value` = ' . (int) $tab['id_feature_value']);
            }
        }
        // Delete product features
        $result = Db::getInstance()->execute('
            DELETE `' . _DB_PREFIX_ . 'feature_product` FROM `' . _DB_PREFIX_ . 'feature_product`
            WHERE `id_product` = ' . (int) $this->id . (!$all_shops ? '
                AND `id_feature` IN (
                    SELECT `id_feature`
                    FROM `' . _DB_PREFIX_ . 'feature_shop`
                    WHERE `id_shop` = ' . (int) Context::getContext()->shop->id . '
                )' : ''));

        SpecificPriceRule::applyAllRules([(int) $this->id]);

        return $result;
    }

    /**
     * Get all available product attributes resume.
     *
     * @param int $id_lang Language id
     *
     * @return array Product attributes combinations
     */
    public function getAttributesResume($id_lang, $attribute_value_separator = ' - ', $attribute_separator = ', ')
    {
        if (!Combination::isFeatureActive()) {
            return [];
        }

        $combinations = Db::getInstance()->executeS('SELECT pa.*, product_attribute_shop.*
                FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                WHERE pa.`id_product` = ' . (int) $this->id . '
                GROUP BY pa.`id_product_attribute`');

        if (!$combinations) {
            return false;
        }

        $product_attributes = [];
        foreach ($combinations as $combination) {
            $product_attributes[] = (int) $combination['id_product_attribute'];
        }

        $lang = Db::getInstance()->executeS('SELECT pac.id_product_attribute, GROUP_CONCAT(agl.`name`, \'' . pSQL($attribute_value_separator) . '\',al.`name` ORDER BY agl.`id_attribute_group` SEPARATOR \'' . pSQL($attribute_separator) . '\') as attribute_designation
                FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $id_lang . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $id_lang . ')
                WHERE pac.id_product_attribute IN (' . implode(',', $product_attributes) . ')
                GROUP BY pac.id_product_attribute');

        foreach ($lang as $k => $row) {
            $combinations[$k]['attribute_designation'] = $row['attribute_designation'];
        }

        //Get quantity of each variations
        foreach ($combinations as $key => $row) {
            $cache_key = $row['id_product'] . '_' . $row['id_product_attribute'] . '_quantity';

            if (!Cache::isStored($cache_key)) {
                $result = StockAvailable::getQuantityAvailableByProduct($row['id_product'], $row['id_product_attribute']);
                Cache::store(
                    $cache_key,
                    $result
                );
                $combinations[$key]['quantity'] = $result;
            } else {
                $combinations[$key]['quantity'] = Cache::retrieve($cache_key);
            }
        }

        return $combinations;
    }

    /**
     * Get all available product attributes combinations.
     *
     * @param int $id_lang Language id
     * @param bool $groupByIdAttributeGroup
     *
     * @return array Product attributes combinations
     */
    public function getAttributeCombinations($id_lang = null, $groupByIdAttributeGroup = true)
    {
        if (!Combination::isFeatureActive()) {
            return [];
        }
        if (null === $id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $sql = 'SELECT pa.*, product_attribute_shop.*, ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, al.`name` AS attribute_name,
                    a.`id_attribute`
                FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $id_lang . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $id_lang . ')
                WHERE pa.`id_product` = ' . (int) $this->id . '
                GROUP BY pa.`id_product_attribute`' . ($groupByIdAttributeGroup ? ',ag.`id_attribute_group`' : '') . '
                ORDER BY pa.`id_product_attribute`';

        $res = Db::getInstance()->executeS($sql);

        //Get quantity of each variations
        foreach ($res as $key => $row) {
            $cache_key = $row['id_product'] . '_' . $row['id_product_attribute'] . '_quantity';

            if (!Cache::isStored($cache_key)) {
                Cache::store(
                    $cache_key,
                    StockAvailable::getQuantityAvailableByProduct($row['id_product'], $row['id_product_attribute'])
                );
            }

            $res[$key]['quantity'] = Cache::retrieve($cache_key);
        }

        return $res;
    }

    /**
     * Get product attribute combination by id_product_attribute.
     *
     * @param int $id_product_attribute
     * @param int $id_lang Language id
     *
     * @return array Product attribute combination by id_product_attribute
     */
    public function getAttributeCombinationsById($id_product_attribute, $id_lang, $groupByIdAttributeGroup = true)
    {
        if (!Combination::isFeatureActive()) {
            return [];
        }
        $sql = 'SELECT pa.*, product_attribute_shop.*, ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, al.`name` AS attribute_name,
                    a.`id_attribute`, a.`position`
                FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $id_lang . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $id_lang . ')
                WHERE pa.`id_product` = ' . (int) $this->id . '
                AND pa.`id_product_attribute` = ' . (int) $id_product_attribute . '
                GROUP BY pa.`id_product_attribute`' . ($groupByIdAttributeGroup ? ',ag.`id_attribute_group`' : '') . '
                ORDER BY pa.`id_product_attribute`';

        $res = Db::getInstance()->executeS($sql);

        //Get quantity of each variations
        foreach ($res as $key => $row) {
            $cache_key = $row['id_product'] . '_' . $row['id_product_attribute'] . '_quantity';

            if (!Cache::isStored($cache_key)) {
                $result = StockAvailable::getQuantityAvailableByProduct($row['id_product'], $row['id_product_attribute']);
                Cache::store(
                    $cache_key,
                    $result
                );
                $res[$key]['quantity'] = $result;
            } else {
                $res[$key]['quantity'] = Cache::retrieve($cache_key);
            }
        }

        return $res;
    }

    public function getCombinationImages($id_lang)
    {
        if (!Combination::isFeatureActive()) {
            return false;
        }

        $product_attributes = Db::getInstance()->executeS(
            'SELECT `id_product_attribute`
            FROM `' . _DB_PREFIX_ . 'product_attribute`
            WHERE `id_product` = ' . (int) $this->id
        );

        if (!$product_attributes) {
            return false;
        }

        $ids = [];

        foreach ($product_attributes as $product_attribute) {
            $ids[] = (int) $product_attribute['id_product_attribute'];
        }

        $result = Db::getInstance()->executeS(
            '
            SELECT pai.`id_image`, pai.`id_product_attribute`, il.`legend`
            FROM `' . _DB_PREFIX_ . 'product_attribute_image` pai
            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (il.`id_image` = pai.`id_image`)
            LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_image` = pai.`id_image`)
            WHERE pai.`id_product_attribute` IN (' . implode(', ', $ids) . ') AND il.`id_lang` = ' . (int) $id_lang . ' ORDER by i.`position`'
        );

        if (!$result) {
            return false;
        }

        $images = [];

        foreach ($result as $row) {
            $images[$row['id_product_attribute']][] = $row;
        }

        return $images;
    }

    public static function getCombinationImageById($id_product_attribute, $id_lang)
    {
        if (!Combination::isFeatureActive() || !$id_product_attribute) {
            return false;
        }

        $result = Db::getInstance()->executeS(
            '
            SELECT pai.`id_image`, pai.`id_product_attribute`, il.`legend`
            FROM `' . _DB_PREFIX_ . 'product_attribute_image` pai
            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (il.`id_image` = pai.`id_image`)
            LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_image` = pai.`id_image`)
            WHERE pai.`id_product_attribute` = ' . (int) $id_product_attribute . ' AND il.`id_lang` = ' . (int) $id_lang . ' ORDER by i.`position` LIMIT 1'
        );

        if (!$result) {
            return false;
        }

        return $result[0];
    }

    /**
     * Check if product has attributes combinations.
     *
     * @return int Attributes combinations number
     */
    public function hasAttributes()
    {
        if (!Combination::isFeatureActive()) {
            return 0;
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            '
            SELECT COUNT(*)
            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
            WHERE pa.`id_product` = ' . (int) $this->id
        );
    }

    /**
     * Get new products.
     *
     * @param int $id_lang Language id
     * @param int $pageNumber Start from (optional)
     * @param int $nbProducts Number of products to return (optional)
     *
     * @return array New products
     */
    public static function getNewProducts($id_lang, $page_number = 0, $nb_products = 10, $count = false, $order_by = null, $order_way = null, Context $context = null)
    {
        $now = date('Y-m-d') . ' 00:00:00';
        if (!$context) {
            $context = Context::getContext();
        }

        $front = true;
        if (!in_array($context->controller->controller_type, ['front', 'modulefront'])) {
            $front = false;
        }

        if ($page_number < 1) {
            $page_number = 1;
        }
        if ($nb_products < 1) {
            $nb_products = 10;
        }
        if (empty($order_by) || $order_by == 'position') {
            $order_by = 'date_add';
        }
        if (empty($order_way)) {
            $order_way = 'DESC';
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'product_shop';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        }
        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }

        $sql_groups = '';
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql_groups = ' AND EXISTS(SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp
            JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '=' . (int) Group::getCurrent()->id) . ')
            WHERE cp.`id_product` = p.`id_product`)';
        }

        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by_prefix = $order_by[0];
            $order_by = $order_by[1];
        }

        $nb_days_new_product = (int) Configuration::get('PS_NB_DAYS_NEW_PRODUCT');

        if ($count) {
            $sql = 'SELECT COUNT(p.`id_product`) AS nb
                    FROM `' . _DB_PREFIX_ . 'product` p
                    ' . Shop::addSqlAssociation('product', 'p') . '
                    WHERE product_shop.`active` = 1
                    AND product_shop.`date_add` > "' . date('Y-m-d', strtotime('-' . $nb_days_new_product . ' DAY')) . '"
                    ' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
                    ' . $sql_groups;

            return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        }
        $sql = new DbQuery();
        $sql->select(
            'p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
            pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`, image_shop.`id_image` id_image, il.`legend`, m.`name` AS manufacturer_name,
            (DATEDIFF(product_shop.`date_add`,
                DATE_SUB(
                    "' . $now . '",
                    INTERVAL ' . $nb_days_new_product . ' DAY
                )
            ) > 0) as new'
        );

        $sql->from('product', 'p');
        $sql->join(Shop::addSqlAssociation('product', 'p'));
        $sql->leftJoin(
            'product_lang',
            'pl',
            '
            p.`id_product` = pl.`id_product`
            AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl')
        );
        $sql->leftJoin('image_shop', 'image_shop', 'image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $context->shop->id);
        $sql->leftJoin('image_lang', 'il', 'image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang);
        $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');

        $sql->where('product_shop.`active` = 1');
        if ($front) {
            $sql->where('product_shop.`visibility` IN ("both", "catalog")');
        }
        $sql->where('product_shop.`date_add` > "' . date('Y-m-d', strtotime('-' . $nb_days_new_product . ' DAY')) . '"');
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql->where('EXISTS(SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp
            JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '=' . (int) Group::getCurrent()->id) . ')
            WHERE cp.`id_product` = p.`id_product`)');
        }

        $sql->orderBy((isset($order_by_prefix) ? pSQL($order_by_prefix) . '.' : '') . '`' . pSQL($order_by) . '` ' . pSQL($order_way));
        $sql->limit($nb_products, (int) (($page_number - 1) * $nb_products));

        if (Combination::isFeatureActive()) {
            $sql->select('product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute');
            $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', 'p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int) $context->shop->id);
        }
        $sql->join(Product::sqlStock('p', 0));

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if (!$result) {
            return false;
        }

        if ($order_by == 'price') {
            Tools::orderbyPrice($result, $order_way);
        }
        $products_ids = [];
        foreach ($result as $row) {
            $products_ids[] = $row['id_product'];
        }
        // Thus you can avoid one query per product, because there will be only one query for all the products of the cart
        Product::cacheFrontFeatures($products_ids, $id_lang);

        return Product::getProductsProperties((int) $id_lang, $result);
    }

    protected static function _getProductIdByDate($beginning, $ending, Context $context = null, $with_combination = false)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $id_address = $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
        $ids = Address::getCountryAndState($id_address);
        $id_country = $ids['id_country'] ? (int) $ids['id_country'] : (int) Configuration::get('PS_COUNTRY_DEFAULT');

        return SpecificPrice::getProductIdByDate(
            $context->shop->id,
            $context->currency->id,
            $id_country,
            $context->customer->id_default_group,
            $beginning,
            $ending,
            0,
            $with_combination
        );
    }

    /**
     * Get a random special.
     *
     * @param int $id_lang Language id
     *
     * @return array Special
     */
    public static function getRandomSpecial($id_lang, $beginning = false, $ending = false, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $front = true;
        if (!in_array($context->controller->controller_type, ['front', 'modulefront'])) {
            $front = false;
        }

        $current_date = date('Y-m-d H:i:00');
        $product_reductions = Product::_getProductIdByDate((!$beginning ? $current_date : $beginning), (!$ending ? $current_date : $ending), $context, true);

        if ($product_reductions) {
            $ids_products = '';
            foreach ($product_reductions as $product_reduction) {
                $ids_products .= '(' . (int) $product_reduction['id_product'] . ',' . ($product_reduction['id_product_attribute'] ? (int) $product_reduction['id_product_attribute'] : '0') . '),';
            }

            $ids_products = rtrim($ids_products, ',');
            Db::getInstance()->execute('CREATE TEMPORARY TABLE `' . _DB_PREFIX_ . 'product_reductions` (id_product INT UNSIGNED NOT NULL DEFAULT 0, id_product_attribute INT UNSIGNED NOT NULL DEFAULT 0) ENGINE=MEMORY', false);
            if ($ids_products) {
                Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'product_reductions` VALUES ' . $ids_products, false);
            }

            $groups = FrontController::getCurrentCustomerGroups();
            $sql_groups = ' AND EXISTS(SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp
            JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '=' . (int) Group::getCurrent()->id) . ')
            WHERE cp.`id_product` = p.`id_product`)';

            // Please keep 2 distinct queries because RAND() is an awful way to achieve this result
            $sql = 'SELECT product_shop.id_product, IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute
                    FROM
                    `' . _DB_PREFIX_ . 'product_reductions` pr,
                    `' . _DB_PREFIX_ . 'product` p
                    ' . Shop::addSqlAssociation('product', 'p') . '
                    LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
                        ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int) $context->shop->id . ')
                    WHERE p.id_product=pr.id_product AND (pr.id_product_attribute = 0 OR product_attribute_shop.id_product_attribute = pr.id_product_attribute) AND product_shop.`active` = 1
                        ' . $sql_groups . '
                    ' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
                    ORDER BY RAND()';

            $result = Db::getInstance()->getRow($sql);

            Db::getInstance()->execute('DROP TEMPORARY TABLE `' . _DB_PREFIX_ . 'product_reductions`', false);

            if (!$id_product = $result['id_product']) {
                return false;
            }

            // no group by needed : there's only one attribute with cover=1 for a given id_product + shop
            $sql = 'SELECT p.*, product_shop.*, stock.`out_of_stock` out_of_stock, pl.`description`, pl.`description_short`,
                        pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`,
                        p.`ean13`, p.`isbn`, p.`upc`, p.`mpn`, image_shop.`id_image` id_image, il.`legend`,
                        DATEDIFF(product_shop.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
                        INTERVAL ' . (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . '
                            DAY)) > 0 AS new
                    FROM `' . _DB_PREFIX_ . 'product` p
                    LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
                        p.`id_product` = pl.`id_product`
                        AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl') . '
                    )
                    ' . Shop::addSqlAssociation('product', 'p') . '
                    LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
                        ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $context->shop->id . ')
                    LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang . ')
                    ' . Product::sqlStock('p', 0) . '
                    WHERE p.id_product = ' . (int) $id_product;

            $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
            if (!$row) {
                return false;
            }

            $row['id_product_attribute'] = (int) $result['id_product_attribute'];

            return Product::getProductProperties($id_lang, $row);
        } else {
            return false;
        }
    }

    /**
     * Get prices drop.
     *
     * @param int $id_lang Language id
     * @param int $pageNumber Start from (optional)
     * @param int $nbProducts Number of products to return (optional)
     * @param bool $count Only in order to get total number (optional)
     *
     * @return array Prices drop
     */
    public static function getPricesDrop(
        $id_lang,
        $page_number = 0,
        $nb_products = 10,
        $count = false,
        $order_by = null,
        $order_way = null,
        $beginning = false,
        $ending = false,
        Context $context = null
    ) {
        if (!Validate::isBool($count)) {
            die(Tools::displayError());
        }

        if (!$context) {
            $context = Context::getContext();
        }
        if ($page_number < 1) {
            $page_number = 1;
        }
        if ($nb_products < 1) {
            $nb_products = 10;
        }
        if (empty($order_by) || $order_by == 'position') {
            $order_by = 'price';
        }
        if (empty($order_way)) {
            $order_way = 'DESC';
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'product_shop';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        }
        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }
        $current_date = date('Y-m-d H:i:00');
        $ids_product = Product::_getProductIdByDate((!$beginning ? $current_date : $beginning), (!$ending ? $current_date : $ending), $context);

        $tab_id_product = [];
        foreach ($ids_product as $product) {
            if (is_array($product)) {
                $tab_id_product[] = (int) $product['id_product'];
            } else {
                $tab_id_product[] = (int) $product;
            }
        }

        $front = true;
        if (!in_array($context->controller->controller_type, ['front', 'modulefront'])) {
            $front = false;
        }

        $sql_groups = '';
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql_groups = ' AND EXISTS(SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp
            JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '=' . (int) Group::getCurrent()->id) . ')
            WHERE cp.`id_product` = p.`id_product`)';
        }

        if ($count) {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT COUNT(DISTINCT p.`id_product`)
            FROM `' . _DB_PREFIX_ . 'product` p
            ' . Shop::addSqlAssociation('product', 'p') . '
            WHERE product_shop.`active` = 1
            AND product_shop.`show_price` = 1
            ' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
            ' . ((!$beginning && !$ending) ? 'AND p.`id_product` IN(' . ((is_array($tab_id_product) && count($tab_id_product)) ? implode(', ', $tab_id_product) : 0) . ')' : '') . '
            ' . $sql_groups);
        }

        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by = pSQL($order_by[0]) . '.`' . pSQL($order_by[1]) . '`';
        }

        $sql = '
        SELECT
            p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description`, pl.`description_short`, pl.`available_now`, pl.`available_later`,
            IFNULL(product_attribute_shop.id_product_attribute, 0) id_product_attribute,
            pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`,
            pl.`name`, image_shop.`id_image` id_image, il.`legend`, m.`name` AS manufacturer_name,
            DATEDIFF(
                p.`date_add`,
                DATE_SUB(
                    "' . date('Y-m-d') . ' 00:00:00",
                    INTERVAL ' . (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY
                )
            ) > 0 AS new
        FROM `' . _DB_PREFIX_ . 'product` p
        ' . Shop::addSqlAssociation('product', 'p') . '
        LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
            ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int) $context->shop->id . ')
        ' . Product::sqlStock('p', 0, false, $context->shop) . '
        LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
            p.`id_product` = pl.`id_product`
            AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl') . '
        )
        LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
            ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $context->shop->id . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
        WHERE product_shop.`active` = 1
        AND product_shop.`show_price` = 1
        ' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
        ' . ((!$beginning && !$ending) ? ' AND p.`id_product` IN (' . ((is_array($tab_id_product) && count($tab_id_product)) ? implode(', ', $tab_id_product) : 0) . ')' : '') . '
        ' . $sql_groups . '
        ORDER BY ' . (isset($order_by_prefix) ? pSQL($order_by_prefix) . '.' : '') . pSQL($order_by) . ' ' . pSQL($order_way) . '
        LIMIT ' . (int) (($page_number - 1) * $nb_products) . ', ' . (int) $nb_products;

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if (!$result) {
            return false;
        }

        if ($order_by == 'price') {
            Tools::orderbyPrice($result, $order_way);
        }

        return Product::getProductsProperties($id_lang, $result);
    }

    /**
     * getProductCategories return an array of categories which this product belongs to.
     *
     * @return array of categories
     */
    public static function getProductCategories($id_product = '')
    {
        $cache_id = 'Product::getProductCategories_' . (int) $id_product;
        if (!Cache::isStored($cache_id)) {
            $ret = [];

            $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                '
                SELECT `id_category` FROM `' . _DB_PREFIX_ . 'category_product`
                WHERE `id_product` = ' . (int) $id_product
            );

            if ($row) {
                foreach ($row as $val) {
                    $ret[] = $val['id_category'];
                }
            }
            Cache::store($cache_id, $ret);

            return $ret;
        }

        return Cache::retrieve($cache_id);
    }

    public static function getProductCategoriesFull($id_product = '', $id_lang = null)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $ret = [];
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
            SELECT cp.`id_category`, cl.`name`, cl.`link_rewrite` FROM `' . _DB_PREFIX_ . 'category_product` cp
            LEFT JOIN `' . _DB_PREFIX_ . 'category` c ON (c.id_category = cp.id_category)
            LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (cp.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . ')
            ' . Shop::addSqlAssociation('category', 'c') . '
            WHERE cp.`id_product` = ' . (int) $id_product . '
                AND cl.`id_lang` = ' . (int) $id_lang
        );

        foreach ($row as $val) {
            $ret[$val['id_category']] = $val;
        }

        return $ret;
    }

    /**
     * getCategories return an array of categories which this product belongs to.
     *
     * @return array of categories
     */
    public function getCategories()
    {
        return Product::getProductCategories($this->id);
    }

    /**
     * Gets carriers assigned to the product.
     */
    public function getCarriers()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT c.*
            FROM `' . _DB_PREFIX_ . 'product_carrier` pc
            INNER JOIN `' . _DB_PREFIX_ . 'carrier` c
                ON (c.`id_reference` = pc.`id_carrier_reference` AND c.`deleted` = 0)
            WHERE pc.`id_product` = ' . (int) $this->id . '
                AND pc.`id_shop` = ' . (int) $this->id_shop);
    }

    /**
     * Sets carriers assigned to the product.
     */
    public function setCarriers($carrier_list)
    {
        $data = [];

        foreach ($carrier_list as $carrier) {
            $data[] = [
                'id_product' => (int) $this->id,
                'id_carrier_reference' => (int) $carrier,
                'id_shop' => (int) $this->id_shop,
            ];
        }
        Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'product_carrier`
            WHERE id_product = ' . (int) $this->id . '
            AND id_shop = ' . (int) $this->id_shop
        );

        $unique_array = [];
        foreach ($data as $sub_array) {
            if (!in_array($sub_array, $unique_array)) {
                $unique_array[] = $sub_array;
            }
        }

        if (count($unique_array)) {
            Db::getInstance()->insert('product_carrier', $unique_array, false, true, Db::INSERT_IGNORE);
        }
    }

    /**
     * Get product images and legends.
     *
     * @param int $id_lang Language id for multilingual legends
     *
     * @return array Product images and legends
     */
    public function getImages($id_lang, Context $context = null)
    {
        return Db::getInstance()->executeS(
            '
            SELECT image_shop.`cover`, i.`id_image`, il.`legend`, i.`position`
            FROM `' . _DB_PREFIX_ . 'image` i
            ' . Shop::addSqlAssociation('image', 'i') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang . ')
            WHERE i.`id_product` = ' . (int) $this->id . '
            ORDER BY `position`'
        );
    }

    /**
     * Get product cover image.
     *
     * @return array Product cover image
     */
    public static function getCover($id_product, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        $cache_id = 'Product::getCover_' . (int) $id_product . '-' . (int) $context->shop->id;
        if (!Cache::isStored($cache_id)) {
            $sql = 'SELECT image_shop.`id_image`
                    FROM `' . _DB_PREFIX_ . 'image` i
                    ' . Shop::addSqlAssociation('image', 'i') . '
                    WHERE i.`id_product` = ' . (int) $id_product . '
                    AND image_shop.`cover` = 1';
            $result = Db::getInstance()->getRow($sql);
            Cache::store($cache_id, $result);

            return $result;
        }

        return Cache::retrieve($cache_id);
    }

    /**
     * Returns product price.
     *
     * @param int $id_product Product id
     * @param bool $usetax With taxes or not (optional)
     * @param int|null $id_product_attribute product attribute id (optional).
     *                                       If set to false, do not apply the combination price impact.
     *                                       NULL does apply the default combination price impact
     * @param int $decimals Number of decimals (optional)
     * @param int|null $divisor Useful when paying many time without fees (optional)
     * @param bool $only_reduc Returns only the reduction amount
     * @param bool $usereduc Set if the returned amount will include reduction
     * @param int $quantity Required for quantity discount application (default value: 1)
     * @param bool $force_associated_tax DEPRECATED - NOT USED Force to apply the associated tax.
     *                                   Only works when the parameter $usetax is true
     * @param int|null $id_customer Customer ID (for customer group reduction)
     * @param int|null $id_cart Cart ID. Required when the cookie is not accessible
     *                          (e.g., inside a payment module, a cron task...)
     * @param int|null $id_address Customer address ID. Required for price (tax included)
     *                             calculation regarding the guest localization
     * @param null $specific_price_output If a specific price applies regarding the previous parameters,
     *                                    this variable is filled with the corresponding SpecificPrice object
     * @param bool $with_ecotax insert ecotax in price output
     * @param bool $use_group_reduction
     * @param Context $context
     * @param bool $use_customer_price
     *
     * @return float Product price
     */
    public static function getPriceStatic(
        $id_product,
        $usetax = true,
        $id_product_attribute = null,
        $decimals = 6,
        $divisor = null,
        $only_reduc = false,
        $usereduc = true,
        $quantity = 1,
        $force_associated_tax = false,
        $id_customer = null,
        $id_cart = null,
        $id_address = null,
        &$specific_price_output = null,
        $with_ecotax = true,
        $use_group_reduction = true,
        Context $context = null,
        $use_customer_price = true,
        $id_customization = null
    ) {
        if (!$context) {
            $context = Context::getContext();
        }

        $cur_cart = $context->cart;

        if ($divisor !== null) {
            Tools::displayParameterAsDeprecated('divisor');
        }

        if (!Validate::isBool($usetax) || !Validate::isUnsignedId($id_product)) {
            die(Tools::displayError());
        }

        // Initializations
        $id_group = null;
        if ($id_customer) {
            $id_group = Customer::getDefaultGroupId((int) $id_customer);
        }
        if (!$id_group) {
            $id_group = (int) Group::getCurrent()->id;
        }

        // If there is cart in context or if the specified id_cart is different from the context cart id
        if (!is_object($cur_cart) || (Validate::isUnsignedInt($id_cart) && $id_cart && $cur_cart->id != $id_cart)) {
            /*
            * When a user (e.g., guest, customer, Google...) is on PrestaShop, he has already its cart as the global (see /init.php)
            * When a non-user calls directly this method (e.g., payment module...) is on PrestaShop, he does not have already it BUT knows the cart ID
            * When called from the back office, cart ID can be inexistant
            */
            if (!$id_cart && !isset($context->employee)) {
                die(Tools::displayError());
            }
            $cur_cart = new Cart($id_cart);
            // Store cart in context to avoid multiple instantiations in BO
            if (!Validate::isLoadedObject($context->cart)) {
                $context->cart = $cur_cart;
            }
        }

        $cart_quantity = 0;
        if ((int) $id_cart) {
            $cache_id = 'Product::getPriceStatic_' . (int) $id_product . '-' . (int) $id_cart;
            if (!Cache::isStored($cache_id) || ($cart_quantity = Cache::retrieve($cache_id) != (int) $quantity)) {
                $sql = 'SELECT SUM(`quantity`)
                FROM `' . _DB_PREFIX_ . 'cart_product`
                WHERE `id_product` = ' . (int) $id_product . '
                AND `id_cart` = ' . (int) $id_cart;
                $cart_quantity = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                Cache::store($cache_id, $cart_quantity);
            } else {
                $cart_quantity = Cache::retrieve($cache_id);
            }
        }

        $id_currency = Validate::isLoadedObject($context->currency) ? (int) $context->currency->id : (int) Configuration::get('PS_CURRENCY_DEFAULT');

        if (!$id_address && Validate::isLoadedObject($cur_cart)) {
            $id_address = $cur_cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
        }

        // retrieve address informations
        $address = Address::initialize($id_address, true);
        $id_country = (int) $address->id_country;
        $id_state = (int) $address->id_state;
        $zipcode = $address->postcode;

        if (Tax::excludeTaxeOption()) {
            $usetax = false;
        }

        if ($usetax != false
            && !empty($address->vat_number)
            && $address->id_country != Configuration::get('VATNUMBER_COUNTRY')
            && Configuration::get('VATNUMBER_MANAGEMENT')) {
            $usetax = false;
        }

        if (null === $id_customer && Validate::isLoadedObject($context->customer)) {
            $id_customer = $context->customer->id;
        }

        $return = Product::priceCalculation(
            $context->shop->id,
            $id_product,
            $id_product_attribute,
            $id_country,
            $id_state,
            $zipcode,
            $id_currency,
            $id_group,
            $quantity,
            $usetax,
            $decimals,
            $only_reduc,
            $usereduc,
            $with_ecotax,
            $specific_price_output,
            $use_group_reduction,
            $id_customer,
            $use_customer_price,
            $id_cart,
            $cart_quantity,
            $id_customization
        );

        return $return;
    }

    /**
     * Price calculation / Get product price.
     *
     * @param int $id_shop Shop id
     * @param int $id_product Product id
     * @param int $id_product_attribute Product attribute id
     * @param int $id_country Country id
     * @param int $id_state State id
     * @param string $zipcode
     * @param int $id_currency Currency id
     * @param int $id_group Group id
     * @param int $quantity Quantity Required for Specific prices : quantity discount application
     * @param bool $use_tax with (1) or without (0) tax
     * @param int $decimals Number of decimals returned
     * @param bool $only_reduc Returns only the reduction amount
     * @param bool $use_reduc Set if the returned amount will include reduction
     * @param bool $with_ecotax insert ecotax in price output
     * @param null $specific_price If a specific price applies regarding the previous parameters,
     *                             this variable is filled with the corresponding SpecificPrice object
     * @param bool $use_group_reduction
     * @param int $id_customer
     * @param bool $use_customer_price
     * @param int $id_cart
     * @param int $real_quantity
     *
     * @return float Product price
     **/
    public static function priceCalculation(
        $id_shop,
        $id_product,
        $id_product_attribute,
        $id_country,
        $id_state,
        $zipcode,
        $id_currency,
        $id_group,
        $quantity,
        $use_tax,
        $decimals,
        $only_reduc,
        $use_reduc,
        $with_ecotax,
        &$specific_price,
        $use_group_reduction,
        $id_customer = 0,
        $use_customer_price = true,
        $id_cart = 0,
        $real_quantity = 0,
        $id_customization = 0
    ) {
        static $address = null;
        static $context = null;

        if ($context == null) {
            $context = Context::getContext()->cloneContext();
        }

        if ($address === null) {
            if (is_object($context->cart) && $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')} != null) {
                $id_address = $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
                $address = new Address($id_address);
            } else {
                $address = new Address();
            }
        }

        if ($id_shop !== null && $context->shop->id != (int) $id_shop) {
            $context->shop = new Shop((int) $id_shop);
        }

        if (!$use_customer_price) {
            $id_customer = 0;
        }

        if ($id_product_attribute === null) {
            $id_product_attribute = Product::getDefaultAttribute($id_product);
        }

        $cache_id = (int) $id_product . '-' . (int) $id_shop . '-' . (int) $id_currency . '-' . (int) $id_country . '-' . $id_state . '-' . $zipcode . '-' . (int) $id_group .
            '-' . (int) $quantity . '-' . (int) $id_product_attribute . '-' . (int) $id_customization .
            '-' . (int) $with_ecotax . '-' . (int) $id_customer . '-' . (int) $use_group_reduction . '-' . (int) $id_cart . '-' . (int) $real_quantity .
            '-' . ($only_reduc ? '1' : '0') . '-' . ($use_reduc ? '1' : '0') . '-' . ($use_tax ? '1' : '0') . '-' . (int) $decimals;

        // reference parameter is filled before any returns
        $specific_price = SpecificPrice::getSpecificPrice(
            (int) $id_product,
            $id_shop,
            $id_currency,
            $id_country,
            $id_group,
            $quantity,
            $id_product_attribute,
            $id_customer,
            $id_cart,
            $real_quantity
        );

        if (isset(self::$_prices[$cache_id])) {
            return self::$_prices[$cache_id];
        }

        // fetch price & attribute price
        $cache_id_2 = $id_product . '-' . $id_shop;
        if (!isset(self::$_pricesLevel2[$cache_id_2])) {
            $sql = new DbQuery();
            $sql->select('product_shop.`price`, product_shop.`ecotax`');
            $sql->from('product', 'p');
            $sql->innerJoin('product_shop', 'product_shop', '(product_shop.id_product=p.id_product AND product_shop.id_shop = ' . (int) $id_shop . ')');
            $sql->where('p.`id_product` = ' . (int) $id_product);
            if (Combination::isFeatureActive()) {
                $sql->select('IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute, product_attribute_shop.`price` AS attribute_price, product_attribute_shop.default_on');
                $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', '(product_attribute_shop.id_product = p.id_product AND product_attribute_shop.id_shop = ' . (int) $id_shop . ')');
            } else {
                $sql->select('0 as id_product_attribute');
            }

            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

            if (is_array($res) && count($res)) {
                foreach ($res as $row) {
                    $array_tmp = [
                        'price' => $row['price'],
                        'ecotax' => $row['ecotax'],
                        'attribute_price' => (isset($row['attribute_price']) ? $row['attribute_price'] : null),
                    ];
                    self::$_pricesLevel2[$cache_id_2][(int) $row['id_product_attribute']] = $array_tmp;

                    if (isset($row['default_on']) && $row['default_on'] == 1) {
                        self::$_pricesLevel2[$cache_id_2][0] = $array_tmp;
                    }
                }
            }
        }

        if (!isset(self::$_pricesLevel2[$cache_id_2][(int) $id_product_attribute])) {
            return;
        }

        $result = self::$_pricesLevel2[$cache_id_2][(int) $id_product_attribute];

        if (!$specific_price || $specific_price['price'] < 0) {
            $price = (float) $result['price'];
        } else {
            $price = (float) $specific_price['price'];
        }
        // convert only if the specific price is in the default currency (id_currency = 0)
        if (
            !$specific_price ||
            !(
                $specific_price['price'] >= 0 &&
                $specific_price['id_currency'] &&
                $id_currency !== $specific_price['id_currency']
            )
        ) {
            $price = Tools::convertPrice($price, $id_currency);

            if (isset($specific_price['price']) && $specific_price['price'] >= 0) {
                $specific_price['price'] = $price;
            }
        }

        // Attribute price
        if (is_array($result) && (!$specific_price || !$specific_price['id_product_attribute'] || $specific_price['price'] < 0)) {
            $attribute_price = Tools::convertPrice($result['attribute_price'] !== null ? (float) $result['attribute_price'] : 0, $id_currency);
            // If you want the default combination, please use NULL value instead
            if ($id_product_attribute !== false) {
                $price += $attribute_price;
            }
        }

        // Customization price
        if ((int) $id_customization) {
            $price += Tools::convertPrice(Customization::getCustomizationPrice($id_customization), $id_currency);
        }

        // Tax
        $address->id_country = $id_country;
        $address->id_state = $id_state;
        $address->postcode = $zipcode;

        $tax_manager = TaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct((int) $id_product, $context));
        $product_tax_calculator = $tax_manager->getTaxCalculator();

        // Add Tax
        if ($use_tax) {
            $price = $product_tax_calculator->addTaxes($price);
        }

        // Eco Tax
        if (($result['ecotax'] || isset($result['attribute_ecotax'])) && $with_ecotax) {
            $ecotax = $result['ecotax'];
            if (isset($result['attribute_ecotax']) && $result['attribute_ecotax'] > 0) {
                $ecotax = $result['attribute_ecotax'];
            }

            if ($id_currency) {
                $ecotax = Tools::convertPrice($ecotax, $id_currency);
            }
            if ($use_tax) {
                static $psEcotaxTaxRulesGroupId = null;
                if ($psEcotaxTaxRulesGroupId === null) {
                    $psEcotaxTaxRulesGroupId = (int) Configuration::get('PS_ECOTAX_TAX_RULES_GROUP_ID');
                }
                // reinit the tax manager for ecotax handling
                $tax_manager = TaxManagerFactory::getManager(
                    $address,
                    $psEcotaxTaxRulesGroupId
                );
                $ecotax_tax_calculator = $tax_manager->getTaxCalculator();
                $price += $ecotax_tax_calculator->addTaxes($ecotax);
            } else {
                $price += $ecotax;
            }
        }

        // Reduction
        $specific_price_reduction = 0;
        if (($only_reduc || $use_reduc) && $specific_price) {
            if ($specific_price['reduction_type'] == 'amount') {
                $reduction_amount = $specific_price['reduction'];

                if (!$specific_price['id_currency']) {
                    $reduction_amount = Tools::convertPrice($reduction_amount, $id_currency);
                }

                $specific_price_reduction = $reduction_amount;

                // Adjust taxes if required

                if (!$use_tax && $specific_price['reduction_tax']) {
                    $specific_price_reduction = $product_tax_calculator->removeTaxes($specific_price_reduction);
                }
                if ($use_tax && !$specific_price['reduction_tax']) {
                    $specific_price_reduction = $product_tax_calculator->addTaxes($specific_price_reduction);
                }
            } else {
                $specific_price_reduction = $price * $specific_price['reduction'];
            }
        }

        if ($use_reduc) {
            $price -= $specific_price_reduction;
        }

        // Group reduction
        if ($use_group_reduction) {
            $reduction_from_category = GroupReduction::getValueForProduct($id_product, $id_group);
            if ($reduction_from_category !== false) {
                $group_reduction = $price * (float) $reduction_from_category;
            } else { // apply group reduction if there is no group reduction for this category
                $group_reduction = (($reduc = Group::getReductionByIdGroup($id_group)) != 0) ? ($price * $reduc / 100) : 0;
            }

            $price -= $group_reduction;
        }

        if ($only_reduc) {
            return Tools::ps_round($specific_price_reduction, $decimals);
        }

        $price = Tools::ps_round($price, $decimals);

        if ($price < 0) {
            $price = 0;
        }

        self::$_prices[$cache_id] = $price;

        return self::$_prices[$cache_id];
    }

    public static function convertAndFormatPrice($price, $currency = false, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        if (!$currency) {
            $currency = $context->currency;
        }

        return $context->getCurrentLocale()->formatPrice(Tools::convertPrice($price, $currency), $currency->iso_code);
    }

    public static function isDiscounted($id_product, $quantity = 1, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $id_group = $context->customer->id_default_group;
        $cart_quantity = !$context->cart ? 0 : Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            '
            SELECT SUM(`quantity`)
            FROM `' . _DB_PREFIX_ . 'cart_product`
            WHERE `id_product` = ' . (int) $id_product . ' AND `id_cart` = ' . (int) $context->cart->id
        );
        $quantity = $cart_quantity ? $cart_quantity : $quantity;

        $id_currency = (int) $context->currency->id;
        $ids = Address::getCountryAndState((int) $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
        $id_country = $ids['id_country'] ? (int) $ids['id_country'] : (int) Configuration::get('PS_COUNTRY_DEFAULT');

        return (bool) SpecificPrice::getSpecificPrice((int) $id_product, $context->shop->id, $id_currency, $id_country, $id_group, $quantity, null, 0, 0, $quantity);
    }

    /**
     * Get product price
     * Same as static function getPriceStatic, no need to specify product id.
     *
     * @param bool $tax With taxes or not (optional)
     * @param int $id_product_attribute Product attribute id (optional)
     * @param int $decimals Number of decimals (optional)
     * @param int $divisor Util when paying many time without fees (optional)
     *
     * @return float Product price in euros
     */
    public function getPrice(
        $tax = true,
        $id_product_attribute = null,
        $decimals = 6,
        $divisor = null,
        $only_reduc = false,
        $usereduc = true,
        $quantity = 1
    ) {
        return Product::getPriceStatic((int) $this->id, $tax, $id_product_attribute, $decimals, $divisor, $only_reduc, $usereduc, $quantity);
    }

    public function getPublicPrice(
        $tax = true,
        $id_product_attribute = null,
        $decimals = 6,
        $divisor = null,
        $only_reduc = false,
        $usereduc = true,
        $quantity = 1
    ) {
        $specific_price_output = null;

        return Product::getPriceStatic(
            (int) $this->id,
            $tax,
            $id_product_attribute,
            $decimals,
            $divisor,
            $only_reduc,
            $usereduc,
            $quantity,
            false,
            null,
            null,
            null,
            $specific_price_output,
            true,
            true,
            null,
            false
        );
    }

    public function getIdProductAttributeMostExpensive()
    {
        if (!Combination::isFeatureActive()) {
            return 0;
        }

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
        SELECT pa.`id_product_attribute`
        FROM `' . _DB_PREFIX_ . 'product_attribute` pa
        ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
        WHERE pa.`id_product` = ' . (int) $this->id . '
        ORDER BY product_attribute_shop.`price` DESC');
    }

    public function getDefaultIdProductAttribute()
    {
        if (!Combination::isFeatureActive()) {
            return 0;
        }

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            '
            SELECT pa.`id_product_attribute`
            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
            WHERE pa.`id_product` = ' . (int) $this->id . '
            AND product_attribute_shop.default_on = 1'
        );
    }

    public function getPriceWithoutReduct($notax = false, $id_product_attribute = null, $decimals = 6)
    {
        return Product::getPriceStatic((int) $this->id, !$notax, $id_product_attribute, $decimals, null, false, false);
    }

    /**
     * Display price with right format and currency.
     *
     * @param array $params Params
     * @param $smarty Smarty object
     *
     * @return string Price with right format and currency
     */
    public static function convertPrice($params, &$smarty)
    {
        return Context::getContext()->getCurrentLocale()->formatPrice($params['price'], Context::getContext()->currency->iso_code);
    }

    /**
     * Convert price with currency.
     *
     * @param array $params
     * @param object $smarty DEPRECATED
     *
     * @return string Ambigous <string, mixed, Ambigous <number, string>>
     */
    public static function convertPriceWithCurrency($params, &$smarty)
    {
        $currency = $params['currency'];
        $currency = is_object($currency) ? $currency->iso_code : Currency::getIsoCodeById((int) $currency);

        return Tools::getContextLocale(Context::getContext())->formatPrice($params['price'], $currency);
    }

    public static function displayWtPrice($params, &$smarty)
    {
        return Tools::getContextLocale(Context::getContext())->formatPrice($params['p'], Context::getContext()->currency->iso_code);
    }

    /**
     * Display WT price with currency.
     *
     * @param array $params
     * @param Smarty $smarty DEPRECATED
     *
     * @return string Ambigous <string, mixed, Ambigous <number, string>>
     */
    public static function displayWtPriceWithCurrency($params, &$smarty)
    {
        $currency = $params['currency'];
        $currency = is_object($currency) ? $currency->iso_code : Currency::getIsoCodeById((int) $currency);

        return !is_null($params['price']) ? Tools::getContextLocale(Context::getContext())->formatPrice($params['price'], $currency) : null;
    }

    /**
     * Get available product quantities (this method already have decreased products in cart).
     *
     * @param int $idProduct Product id
     * @param int $idProductAttribute Product attribute id (optional)
     * @param bool|null $cacheIsPack
     * @param Cart|null $cart
     * @param int $idCustomization Product customization id (optional)
     *
     * @return int Available quantities
     */
    public static function getQuantity(
        $idProduct,
        $idProductAttribute = null,
        $cacheIsPack = null,
        Cart $cart = null,
        $idCustomization = null
    ) {
        // pack usecase: Pack::getQuantity() returns the pack quantity after cart quantities have been removed from stock
        if (Pack::isPack((int) $idProduct)) {
            return Pack::getQuantity($idProduct, $idProductAttribute, $cacheIsPack, $cart, $idCustomization);
        }
        $availableQuantity = StockAvailable::getQuantityAvailableByProduct($idProduct, $idProductAttribute);
        $nbProductInCart = 0;

        if (!empty($cart)) {
            $cartProduct = $cart->getProductQuantity($idProduct, $idProductAttribute, $idCustomization);

            if (!empty($cartProduct['deep_quantity'])) {
                $nbProductInCart = $cartProduct['deep_quantity'];
            }
        }

        // @since 1.5.0
        return $availableQuantity - $nbProductInCart;
    }

    /**
     * Create JOIN query with 'stock_available' table.
     *
     * @param string $productAlias Alias of product table
     * @param string|int $productAttribute If string : alias of PA table ; if int : value of PA ; if null : nothing about PA
     * @param bool $innerJoin LEFT JOIN or INNER JOIN
     * @param Shop $shop
     *
     * @return string
     */
    public static function sqlStock($product_alias, $product_attribute = null, $inner_join = false, Shop $shop = null)
    {
        $id_shop = ($shop !== null ? (int) $shop->id : null);
        $sql = (($inner_join) ? ' INNER ' : ' LEFT ')
            . 'JOIN ' . _DB_PREFIX_ . 'stock_available stock
            ON (stock.id_product = `' . bqSQL($product_alias) . '`.id_product';

        if (null !== $product_attribute) {
            if (!Combination::isFeatureActive()) {
                $sql .= ' AND stock.id_product_attribute = 0';
            } elseif (is_numeric($product_attribute)) {
                $sql .= ' AND stock.id_product_attribute = ' . $product_attribute;
            } elseif (is_string($product_attribute)) {
                $sql .= ' AND stock.id_product_attribute = IFNULL(`' . bqSQL($product_attribute) . '`.id_product_attribute, 0)';
            }
        }

        $sql .= StockAvailable::addSqlShopRestriction(null, $id_shop, 'stock') . ' )';

        return $sql;
    }

    /**
     * @deprecated since 1.5.0
     *
     * It's not possible to use this method with new stockManager and stockAvailable features
     * Now this method do nothing
     * @see StockManager if you want to manage real stock
     * @see StockAvailable if you want to manage available quantities for sale on your shop(s)
     * @deprecated 1.5.3.0
     *
     * @return false
     */
    public static function updateQuantity()
    {
        Tools::displayAsDeprecated();

        return false;
    }

    /**
     * @deprecated since 1.5.0
     *
     * It's not possible to use this method with new stockManager and stockAvailable features
     * Now this method do nothing
     * @deprecated 1.5.3.0
     * @see StockManager if you want to manage real stock
     * @see StockAvailable if you want to manage available quantities for sale on your shop(s)
     *
     * @return false
     */
    public static function reinjectQuantities()
    {
        Tools::displayAsDeprecated();

        return false;
    }

    public static function isAvailableWhenOutOfStock($out_of_stock)
    {
        /** @TODO 1.5.0 Update of STOCK_MANAGEMENT & ORDER_OUT_OF_STOCK */
        $ps_stock_management = Configuration::get('PS_STOCK_MANAGEMENT');

        if (!$ps_stock_management) {
            return true;
        }

        $ps_order_out_of_stock = Configuration::get('PS_ORDER_OUT_OF_STOCK');

        return (int) $out_of_stock == 2 ? (int) $ps_order_out_of_stock : (int) $out_of_stock;
    }

    /**
     * Check product availability.
     *
     * @param int $qty Quantity desired
     *
     * @return bool True if product is available with this quantity, false otherwise
     */
    public function checkQty($qty)
    {
        if ($this->isAvailableWhenOutOfStock(StockAvailable::outOfStock($this->id))) {
            return true;
        }
        $id_product_attribute = isset($this->id_product_attribute) ? $this->id_product_attribute : null;
        $availableQuantity = StockAvailable::getQuantityAvailableByProduct($this->id, $id_product_attribute);

        return $qty <= $availableQuantity;
    }

    /**
     * Check if there is no default attribute and create it if not.
     */
    public function checkDefaultAttributes()
    {
        if (!$this->id) {
            return false;
        }

        if (Db::getInstance()->getValue('SELECT COUNT(*)
                FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                WHERE product_attribute_shop.`default_on` = 1
                AND pa.`id_product` = ' . (int) $this->id) > Shop::getTotalShops(true)) {
            Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'product_attribute_shop product_attribute_shop, ' . _DB_PREFIX_ . 'product_attribute pa
                    SET product_attribute_shop.default_on=NULL, pa.default_on = NULL
                    WHERE product_attribute_shop.id_product_attribute=pa.id_product_attribute AND pa.id_product=' . (int) $this->id
                    . Shop::addSqlRestriction(false, 'product_attribute_shop'));
        }

        $row = Db::getInstance()->getRow(
            '
            SELECT pa.id_product
            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
            WHERE product_attribute_shop.`default_on` = 1
                AND pa.`id_product` = ' . (int) $this->id
        );
        if ($row) {
            return true;
        }

        $mini = Db::getInstance()->getRow(
            '
        SELECT MIN(pa.id_product_attribute) as `id_attr`
        FROM `' . _DB_PREFIX_ . 'product_attribute` pa
            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
            WHERE pa.`id_product` = ' . (int) $this->id
        );
        if (!$mini) {
            return false;
        }

        if (!ObjectModel::updateMultishopTable('Combination', ['default_on' => 1], 'a.id_product_attribute = ' . (int) $mini['id_attr'])) {
            return false;
        }

        return true;
    }

    public static function getAttributesColorList(array $products, $have_stock = true)
    {
        if (!count($products)) {
            return [];
        }

        $id_lang = Context::getContext()->language->id;

        $check_stock = !Configuration::get('PS_DISP_UNAVAILABLE_ATTR');
        if (!$res = Db::getInstance()->executeS(
            '
            SELECT pa.`id_product`, a.`color`, pac.`id_product_attribute`, ' . ($check_stock ? 'SUM(IF(stock.`quantity` > 0, 1, 0))' : '0') . ' qty, a.`id_attribute`, al.`name`, IF(color = "", a.id_attribute, color) group_by
            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
            ' . Shop::addSqlAssociation('product_attribute', 'pa') .
            ($check_stock ? Product::sqlStock('pa', 'pa') : '') . '
            JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON (pac.`id_product_attribute` = product_attribute_shop.`id_product_attribute`)
            JOIN `' . _DB_PREFIX_ . 'attribute` a ON (a.`id_attribute` = pac.`id_attribute`)
            JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $id_lang . ')
            JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON (a.id_attribute_group = ag.`id_attribute_group`)
            WHERE pa.`id_product` IN (' . implode(',', array_map('intval', $products)) . ') AND ag.`is_color_group` = 1
            GROUP BY pa.`id_product`, a.`id_attribute`, `group_by`
            ' . ($check_stock ? 'HAVING qty > 0' : '') . '
            ORDER BY a.`position` ASC;'
            )
        ) {
            return false;
        }

        $colors = [];
        foreach ($res as $row) {
            $row['texture'] = '';

            if (@filemtime(_PS_COL_IMG_DIR_ . $row['id_attribute'] . '.jpg')) {
                $row['texture'] = _THEME_COL_DIR_ . $row['id_attribute'] . '.jpg';
            } elseif (Tools::isEmpty($row['color'])) {
                continue;
            }

            $colors[(int) $row['id_product']][] = ['id_product_attribute' => (int) $row['id_product_attribute'], 'color' => $row['color'], 'texture' => $row['texture'], 'id_product' => $row['id_product'], 'name' => $row['name'], 'id_attribute' => $row['id_attribute']];
        }

        return $colors;
    }

    /**
     * Get all available attribute groups.
     *
     * @param int $id_lang Language id
     *
     * @return array Attribute groups
     */
    public function getAttributesGroups($id_lang)
    {
        if (!Combination::isFeatureActive()) {
            return [];
        }
        $sql = 'SELECT ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, agl.`public_name` AS public_group_name,
                    a.`id_attribute`, al.`name` AS attribute_name, a.`color` AS attribute_color, product_attribute_shop.`id_product_attribute`,
                    IFNULL(stock.quantity, 0) as quantity, product_attribute_shop.`price`, product_attribute_shop.`ecotax`, product_attribute_shop.`weight`,
                    product_attribute_shop.`default_on`, pa.`reference`, product_attribute_shop.`unit_price_impact`,
                    product_attribute_shop.`minimal_quantity`, product_attribute_shop.`available_date`, ag.`group_type`
                FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                ' . Product::sqlStock('pa', 'pa') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON (a.`id_attribute` = pac.`id_attribute`)
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON (ag.`id_attribute_group` = a.`id_attribute_group`)
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute`)
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group`)
                ' . Shop::addSqlAssociation('attribute', 'a') . '
                WHERE pa.`id_product` = ' . (int) $this->id . '
                    AND al.`id_lang` = ' . (int) $id_lang . '
                    AND agl.`id_lang` = ' . (int) $id_lang . '
                GROUP BY id_attribute_group, id_product_attribute
                ORDER BY ag.`position` ASC, a.`position` ASC, agl.`name` ASC';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Delete product accessories.
     * Wrapper to static method deleteAccessories($product_id).
     *
     * @return mixed Deletion result
     */
    public function deleteAccessories()
    {
        return Db::getInstance()->delete('accessory', 'id_product_1 = ' . (int) $this->id);
    }

    /**
     * Delete product from other products accessories.
     *
     * @return mixed Deletion result
     */
    public function deleteFromAccessories()
    {
        return Db::getInstance()->delete('accessory', 'id_product_2 = ' . (int) $this->id);
    }

    /**
     * Get product accessories (only names).
     *
     * @param int $id_lang Language id
     * @param int $id_product Product id
     *
     * @return array Product accessories
     */
    public static function getAccessoriesLight($id_lang, $id_product)
    {
        return Db::getInstance()->executeS(
            '
            SELECT p.`id_product`, p.`reference`, pl.`name`
            FROM `' . _DB_PREFIX_ . 'accessory`
            LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (p.`id_product`= `id_product_2`)
            ' . Shop::addSqlAssociation('product', 'p') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
                p.`id_product` = pl.`id_product`
                AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl') . '
            )
            WHERE `id_product_1` = ' . (int) $id_product
        );
    }

    /**
     * Get product accessories.
     *
     * @param int $id_lang Language id
     *
     * @return array Product accessories
     */
    public function getAccessories($id_lang, $active = true)
    {
        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description`, pl.`description_short`, pl.`link_rewrite`,
                    pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`,
                    image_shop.`id_image` id_image, il.`legend`, m.`name` as manufacturer_name, cl.`name` AS category_default, IFNULL(product_attribute_shop.id_product_attribute, 0) id_product_attribute,
                    DATEDIFF(
                        p.`date_add`,
                        DATE_SUB(
                            "' . date('Y-m-d') . ' 00:00:00",
                            INTERVAL ' . (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY
                        )
                    ) > 0 AS new
                FROM `' . _DB_PREFIX_ . 'accessory`
                LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON p.`id_product` = `id_product_2`
                ' . Shop::addSqlAssociation('product', 'p') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
                    ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int) $this->id_shop . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
                    p.`id_product` = pl.`id_product`
                    AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl') . '
                )
                LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (
                    product_shop.`id_category_default` = cl.`id_category`
                    AND cl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('cl') . '
                )
                LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
                    ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $this->id_shop . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (p.`id_manufacturer`= m.`id_manufacturer`)
                ' . Product::sqlStock('p', 0) . '
                WHERE `id_product_1` = ' . (int) $this->id .
                ($active ? ' AND product_shop.`active` = 1 AND product_shop.`visibility` != \'none\'' : '') . '
                GROUP BY product_shop.id_product';

        if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
            return [];
        }

        foreach ($result as $k => &$row) {
            if (!Product::checkAccessStatic((int) $row['id_product'], false)) {
                unset($result[$k]);

                continue;
            } else {
                $row['id_product_attribute'] = Product::getDefaultAttribute((int) $row['id_product']);
            }
        }

        return $this->getProductsProperties($id_lang, $result);
    }

    public static function getAccessoryById($accessory_id)
    {
        return Db::getInstance()->getRow('SELECT `id_product`, `name` FROM `' . _DB_PREFIX_ . 'product_lang` WHERE `id_product` = ' . (int) $accessory_id);
    }

    /**
     * Link accessories with product
     * Wrapper to static method changeAccessories($accessories_id, $product_id).
     *
     * @param array $accessories_id Accessories ids
     */
    public function changeAccessories($accessories_id)
    {
        self::changeAccessoriesForProduct($accessories_id, $this->id);
    }

    /**
     * Link accessories with product. No need to inflate a full Product (better performances).
     *
     * @param array $accessories_id Accessories ids
     * @param int the product ID to link accessories on
     */
    public static function changeAccessoriesForProduct($accessories_id, $product_id)
    {
        foreach ($accessories_id as $id_product_2) {
            Db::getInstance()->insert('accessory', [
                'id_product_1' => (int) $product_id,
                'id_product_2' => (int) $id_product_2,
            ]);
        }
    }

    /**
     * Add new feature to product.
     */
    public function addFeaturesCustomToDB($id_value, $lang, $cust)
    {
        $row = ['id_feature_value' => (int) $id_value, 'id_lang' => (int) $lang, 'value' => pSQL($cust)];

        return Db::getInstance()->insert('feature_value_lang', $row);
    }

    public function addFeaturesToDB($id_feature, $id_value, $cust = 0)
    {
        if ($cust) {
            $row = ['id_feature' => (int) $id_feature, 'custom' => 1];
            Db::getInstance()->insert('feature_value', $row);
            $id_value = Db::getInstance()->Insert_ID();
        }
        $row = ['id_feature' => (int) $id_feature, 'id_product' => (int) $this->id, 'id_feature_value' => (int) $id_value];
        Db::getInstance()->insert('feature_product', $row);
        SpecificPriceRule::applyAllRules([(int) $this->id]);
        if ($id_value) {
            return $id_value;
        }
    }

    public static function addFeatureProductImport($id_product, $id_feature, $id_feature_value)
    {
        return Db::getInstance()->execute(
            '
            INSERT INTO `' . _DB_PREFIX_ . 'feature_product` (`id_feature`, `id_product`, `id_feature_value`)
            VALUES (' . (int) $id_feature . ', ' . (int) $id_product . ', ' . (int) $id_feature_value . ')
            ON DUPLICATE KEY UPDATE `id_feature_value` = ' . (int) $id_feature_value
        );
    }

    /**
     * Select all features for the object.
     *
     * @return array Array with feature product's data
     */
    public function getFeatures()
    {
        return Product::getFeaturesStatic((int) $this->id);
    }

    public static function getFeaturesStatic($id_product)
    {
        if (!Feature::isFeatureActive()) {
            return [];
        }
        if (!array_key_exists($id_product, self::$_cacheFeatures)) {
            self::$_cacheFeatures[$id_product] = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                '
                SELECT fp.id_feature, fp.id_product, fp.id_feature_value, custom
                FROM `' . _DB_PREFIX_ . 'feature_product` fp
                LEFT JOIN `' . _DB_PREFIX_ . 'feature_value` fv ON (fp.id_feature_value = fv.id_feature_value)
                WHERE `id_product` = ' . (int) $id_product
            );
        }

        return self::$_cacheFeatures[$id_product];
    }

    public static function cacheProductsFeatures($product_ids)
    {
        if (!Feature::isFeatureActive()) {
            return;
        }

        $product_implode = [];
        foreach ($product_ids as $id_product) {
            if ((int) $id_product && !array_key_exists($id_product, self::$_cacheFeatures)) {
                $product_implode[] = (int) $id_product;
            }
        }
        if (!count($product_implode)) {
            return;
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT id_feature, id_product, id_feature_value
        FROM `' . _DB_PREFIX_ . 'feature_product`
        WHERE `id_product` IN (' . implode(',', $product_implode) . ')');
        foreach ($result as $row) {
            if (!array_key_exists($row['id_product'], self::$_cacheFeatures)) {
                self::$_cacheFeatures[$row['id_product']] = [];
            }
            self::$_cacheFeatures[$row['id_product']][] = $row;
        }
    }

    public static function cacheFrontFeatures($product_ids, $id_lang)
    {
        if (!Feature::isFeatureActive()) {
            return;
        }

        $product_implode = [];
        foreach ($product_ids as $id_product) {
            if ((int) $id_product && !array_key_exists($id_product . '-' . $id_lang, self::$_cacheFeatures)) {
                $product_implode[] = (int) $id_product;
            }
        }
        if (!count($product_implode)) {
            return;
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT id_product, name, value, pf.id_feature
        FROM ' . _DB_PREFIX_ . 'feature_product pf
        LEFT JOIN ' . _DB_PREFIX_ . 'feature_lang fl ON (fl.id_feature = pf.id_feature AND fl.id_lang = ' . (int) $id_lang . ')
        LEFT JOIN ' . _DB_PREFIX_ . 'feature_value_lang fvl ON (fvl.id_feature_value = pf.id_feature_value AND fvl.id_lang = ' . (int) $id_lang . ')
        LEFT JOIN ' . _DB_PREFIX_ . 'feature f ON (f.id_feature = pf.id_feature)
        ' . Shop::addSqlAssociation('feature', 'f') . '
        WHERE `id_product` IN (' . implode(',', $product_implode) . ')
        ORDER BY f.position ASC');

        foreach ($result as $row) {
            if (!array_key_exists($row['id_product'] . '-' . $id_lang, self::$_frontFeaturesCache)) {
                self::$_frontFeaturesCache[$row['id_product'] . '-' . $id_lang] = [];
            }
            if (!isset(self::$_frontFeaturesCache[$row['id_product'] . '-' . $id_lang][$row['id_feature']])) {
                self::$_frontFeaturesCache[$row['id_product'] . '-' . $id_lang][$row['id_feature']] = $row;
            }
        }
    }

    /**
     * Admin panel product search.
     *
     * @param int $id_lang Language id
     * @param string $query Search query
     *
     * @return array Matching products
     */
    public static function searchByName($id_lang, $query, Context $context = null, $limit = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $sql = new DbQuery();
        $sql->select('p.`id_product`, pl.`name`, p.`ean13`, p.`isbn`, p.`upc`, p.`mpn`, p.`active`, p.`reference`, m.`name` AS manufacturer_name, stock.`quantity`, product_shop.advanced_stock_management, p.`customizable`');
        $sql->from('product', 'p');
        $sql->join(Shop::addSqlAssociation('product', 'p'));
        $sql->leftJoin(
            'product_lang',
            'pl',
            'p.`id_product` = pl.`id_product`
            AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl')
        );
        $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');

        $where = 'pl.`name` LIKE \'%' . pSQL($query) . '%\'
        OR p.`ean13` LIKE \'%' . pSQL($query) . '%\'
        OR p.`isbn` LIKE \'%' . pSQL($query) . '%\'
        OR p.`upc` LIKE \'%' . pSQL($query) . '%\'
        OR p.`mpn` LIKE \'%' . pSQL($query) . '%\'
        OR p.`reference` LIKE \'%' . pSQL($query) . '%\'
        OR p.`supplier_reference` LIKE \'%' . pSQL($query) . '%\'
        OR EXISTS(SELECT * FROM `' . _DB_PREFIX_ . 'product_supplier` sp WHERE sp.`id_product` = p.`id_product` AND `product_supplier_reference` LIKE \'%' . pSQL($query) . '%\')';

        $sql->orderBy('pl.`name` ASC');

        if ($limit) {
            $sql->limit($limit);
        }

        if (Combination::isFeatureActive()) {
            $where .= ' OR EXISTS(SELECT * FROM `' . _DB_PREFIX_ . 'product_attribute` `pa` WHERE pa.`id_product` = p.`id_product` AND (pa.`reference` LIKE \'%' . pSQL($query) . '%\'
            OR pa.`supplier_reference` LIKE \'%' . pSQL($query) . '%\'
            OR pa.`ean13` LIKE \'%' . pSQL($query) . '%\'
            OR pa.`isbn` LIKE \'%' . pSQL($query) . '%\'
            OR pa.`mpn` LIKE \'%' . pSQL($query) . '%\'
            OR pa.`upc` LIKE \'%' . pSQL($query) . '%\'))';
        }
        $sql->where($where);
        $sql->join(Product::sqlStock('p', 0));

        $result = Db::getInstance()->executeS($sql);

        if (!$result) {
            return false;
        }

        $results_array = [];
        foreach ($result as $row) {
            $row['price_tax_incl'] = Product::getPriceStatic($row['id_product'], true, null, 2);
            $row['price_tax_excl'] = Product::getPriceStatic($row['id_product'], false, null, 2);
            $results_array[] = $row;
        }

        return $results_array;
    }

    /**
     * Duplicate attributes when duplicating a product.
     *
     * @param int $id_product_old Old product id
     * @param int $id_product_new New product id
     */
    public static function duplicateAttributes($id_product_old, $id_product_new)
    {
        $return = true;
        $combination_images = [];

        $result = Db::getInstance()->executeS(
            '
        SELECT pa.*, product_attribute_shop.*
            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
            WHERE pa.`id_product` = ' . (int) $id_product_old
        );
        $combinations = [];

        foreach ($result as $row) {
            $id_product_attribute_old = (int) $row['id_product_attribute'];
            if (!isset($combinations[$id_product_attribute_old])) {
                $id_combination = null;
                $id_shop = null;
                $result2 = Db::getInstance()->executeS(
                    '
                SELECT *
                FROM `' . _DB_PREFIX_ . 'product_attribute_combination`
                    WHERE `id_product_attribute` = ' . $id_product_attribute_old
                );
            } else {
                $id_combination = (int) $combinations[$id_product_attribute_old];
                $id_shop = (int) $row['id_shop'];
                $context_old = Shop::getContext();
                $context_shop_id_old = Shop::getContextShopID();
                Shop::setContext(Shop::CONTEXT_SHOP, $id_shop);
            }

            $row['id_product'] = $id_product_new;
            unset($row['id_product_attribute']);

            $combination = new Combination($id_combination, null, $id_shop);
            foreach ($row as $k => $v) {
                $combination->$k = $v;
            }
            $return &= $combination->save();

            $id_product_attribute_new = (int) $combination->id;

            if ($result_images = Product::_getAttributeImageAssociations($id_product_attribute_old)) {
                $combination_images['old'][$id_product_attribute_old] = $result_images;
                $combination_images['new'][$id_product_attribute_new] = $result_images;
            }

            if (!isset($combinations[$id_product_attribute_old])) {
                $combinations[$id_product_attribute_old] = (int) $id_product_attribute_new;
                foreach ($result2 as $row2) {
                    $row2['id_product_attribute'] = $id_product_attribute_new;
                    $return &= Db::getInstance()->insert('product_attribute_combination', $row2);
                }
            } else {
                Shop::setContext($context_old, $context_shop_id_old);
            }

            //Copy suppliers
            $result3 = Db::getInstance()->executeS('
            SELECT *
            FROM `' . _DB_PREFIX_ . 'product_supplier`
            WHERE `id_product_attribute` = ' . (int) $id_product_attribute_old . '
            AND `id_product` = ' . (int) $id_product_old);

            foreach ($result3 as $row3) {
                unset($row3['id_product_supplier']);
                $row3['id_product'] = $id_product_new;
                $row3['id_product_attribute'] = $id_product_attribute_new;
                $return &= Db::getInstance()->insert('product_supplier', $row3);
            }
        }

        $impacts = self::getAttributesImpacts($id_product_old);

        if (is_array($impacts) && count($impacts)) {
            $impact_sql = 'INSERT INTO `' . _DB_PREFIX_ . 'attribute_impact` (`id_product`, `id_attribute`, `weight`, `price`) VALUES ';

            foreach ($impacts as $id_attribute => $impact) {
                $impact_sql .= '(' . (int) $id_product_new . ', ' . (int) $id_attribute . ', ' . (float) $impacts[$id_attribute]['weight'] . ', '
                    . (float) $impacts[$id_attribute]['price'] . '),';
            }

            $impact_sql = substr_replace($impact_sql, '', -1);
            $impact_sql .= ' ON DUPLICATE KEY UPDATE `price` = VALUES(price), `weight` = VALUES(weight)';

            Db::getInstance()->execute($impact_sql);
        }

        return !$return ? false : $combination_images;
    }

    public static function getAttributesImpacts($id_product)
    {
        $return = [];
        $result = Db::getInstance()->executeS(
            'SELECT ai.`id_attribute`, ai.`price`, ai.`weight`
            FROM `' . _DB_PREFIX_ . 'attribute_impact` ai
            WHERE ai.`id_product` = ' . (int) $id_product
        );

        if (!$result) {
            return [];
        }
        foreach ($result as $impact) {
            $return[$impact['id_attribute']]['price'] = (float) $impact['price'];
            $return[$impact['id_attribute']]['weight'] = (float) $impact['weight'];
        }

        return $return;
    }

    /**
     * Get product attribute image associations.
     *
     * @param int $id_product_attribute
     *
     * @return array
     */
    public static function _getAttributeImageAssociations($id_product_attribute)
    {
        $combination_images = [];
        $data = Db::getInstance()->executeS('
            SELECT `id_image`
            FROM `' . _DB_PREFIX_ . 'product_attribute_image`
            WHERE `id_product_attribute` = ' . (int) $id_product_attribute);
        foreach ($data as $row) {
            $combination_images[] = (int) $row['id_image'];
        }

        return $combination_images;
    }

    public static function duplicateAccessories($id_product_old, $id_product_new)
    {
        $return = true;

        $result = Db::getInstance()->executeS('
        SELECT *
        FROM `' . _DB_PREFIX_ . 'accessory`
        WHERE `id_product_1` = ' . (int) $id_product_old);
        foreach ($result as $row) {
            $data = [
                'id_product_1' => (int) $id_product_new,
                'id_product_2' => (int) $row['id_product_2'],
            ];
            $return &= Db::getInstance()->insert('accessory', $data);
        }

        return $return;
    }

    public static function duplicateTags($id_product_old, $id_product_new)
    {
        $tags = Db::getInstance()->executeS('SELECT `id_tag`, `id_lang` FROM `' . _DB_PREFIX_ . 'product_tag` WHERE `id_product` = ' . (int) $id_product_old);
        if (!Db::getInstance()->numRows()) {
            return true;
        }

        $data = [];
        foreach ($tags as $tag) {
            $data[] = [
                'id_product' => (int) $id_product_new,
                'id_tag' => (int) $tag['id_tag'],
                'id_lang' => (int) $tag['id_lang'],
            ];
        }

        return Db::getInstance()->insert('product_tag', $data);
    }

    public static function duplicateTaxes($id_product_old, $id_product_new)
    {
        $query = new DbQuery();
        $query->select('id_tax_rules_group, id_shop');
        $query->from('product_shop');
        $query->where('`id_product` = ' . (int) $id_product_old);

        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query->build());

        if (!empty($results)) {
            foreach ($results as $result) {
                if (!Db::getInstance()->update(
                    'product_shop',
                    ['id_tax_rules_group' => (int) $result['id_tax_rules_group']],
                    'id_product=' . (int) $id_product_new . ' AND id_shop = ' . (int) $result['id_shop']
                )) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Duplicate prices when duplicating a product.
     *
     * @param int $id_product_old Old product id
     * @param int $id_product_new New product id
     */
    public static function duplicatePrices($id_product_old, $id_product_new)
    {
        $query = new DbQuery();
        $query->select('price, unit_price_ratio, id_shop');
        $query->from('product_shop');
        $query->where('`id_product` = ' . (int) $id_product_old);
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query->build());
        if (!empty($results)) {
            foreach ($results as $result) {
                if (!Db::getInstance()->update(
                    'product_shop',
                    ['price' => pSQL($result['price']), 'unit_price_ratio' => pSQL($result['unit_price_ratio'])],
                    'id_product=' . (int) $id_product_new . ' AND id_shop = ' . (int) $result['id_shop']
                )) {
                    return false;
                }
            }
        }

        return true;
    }

    public static function duplicateDownload($id_product_old, $id_product_new)
    {
        $sql = 'SELECT `display_filename`, `filename`, `date_add`, `date_expiration`, `nb_days_accessible`, `nb_downloadable`, `active`, `is_shareable`
                FROM `' . _DB_PREFIX_ . 'product_download`
                WHERE `id_product` = ' . (int) $id_product_old;
        $results = Db::getInstance()->executeS($sql);
        if (!$results) {
            return true;
        }

        $data = [];
        foreach ($results as $row) {
            $new_filename = ProductDownload::getNewFilename();
            copy(_PS_DOWNLOAD_DIR_ . $row['filename'], _PS_DOWNLOAD_DIR_ . $new_filename);

            $data[] = [
                'id_product' => (int) $id_product_new,
                'display_filename' => pSQL($row['display_filename']),
                'filename' => pSQL($new_filename),
                'date_expiration' => pSQL($row['date_expiration']),
                'nb_days_accessible' => (int) $row['nb_days_accessible'],
                'nb_downloadable' => (int) $row['nb_downloadable'],
                'active' => (int) $row['active'],
                'is_shareable' => (int) $row['is_shareable'],
                'date_add' => date('Y-m-d H:i:s'),
            ];
        }

        return Db::getInstance()->insert('product_download', $data);
    }

    public static function duplicateAttachments($id_product_old, $id_product_new)
    {
        // Get all ids attachments of the old product
        $sql = 'SELECT `id_attachment` FROM `' . _DB_PREFIX_ . 'product_attachment` WHERE `id_product` = ' . (int) $id_product_old;
        $results = Db::getInstance()->executeS($sql);

        if (!$results) {
            return true;
        }

        $data = [];

        // Prepare data of table product_attachment
        foreach ($results as $row) {
            $data[] = [
                'id_product' => (int) $id_product_new,
                'id_attachment' => (int) $row['id_attachment'],
            ];
        }

        // Duplicate product attachement
        $res = Db::getInstance()->insert('product_attachment', $data);
        Product::updateCacheAttachment((int) $id_product_new);

        return $res;
    }

    /**
     * Duplicate features when duplicating a product.
     *
     * @param int $id_product_old Old product id
     * @param int $id_product_old New product id
     */
    public static function duplicateFeatures($id_product_old, $id_product_new)
    {
        $return = true;

        $result = Db::getInstance()->executeS('
        SELECT *
        FROM `' . _DB_PREFIX_ . 'feature_product`
        WHERE `id_product` = ' . (int) $id_product_old);
        foreach ($result as $row) {
            $result2 = Db::getInstance()->getRow('
            SELECT *
            FROM `' . _DB_PREFIX_ . 'feature_value`
            WHERE `id_feature_value` = ' . (int) $row['id_feature_value']);
            // Custom feature value, need to duplicate it
            if ($result2['custom']) {
                $old_id_feature_value = $result2['id_feature_value'];
                unset($result2['id_feature_value']);
                $return &= Db::getInstance()->insert('feature_value', $result2);
                $max_fv = Db::getInstance()->getRow('
                    SELECT MAX(`id_feature_value`) AS nb
                    FROM `' . _DB_PREFIX_ . 'feature_value`');
                $new_id_feature_value = $max_fv['nb'];

                foreach (Language::getIDs(false) as $id_lang) {
                    $result3 = Db::getInstance()->getRow('
                    SELECT *
                    FROM `' . _DB_PREFIX_ . 'feature_value_lang`
                    WHERE `id_feature_value` = ' . (int) $old_id_feature_value . '
                    AND `id_lang` = ' . (int) $id_lang);

                    if ($result3) {
                        $result3['id_feature_value'] = (int) $new_id_feature_value;
                        $result3['value'] = pSQL($result3['value']);
                        $return &= Db::getInstance()->insert('feature_value_lang', $result3);
                    }
                }
                $row['id_feature_value'] = $new_id_feature_value;
            }

            $row['id_product'] = (int) $id_product_new;
            $return &= Db::getInstance()->insert('feature_product', $row);
        }

        return $return;
    }

    protected static function _getCustomizationFieldsNLabels($product_id, $id_shop = null)
    {
        if (!Customization::isFeatureActive()) {
            return false;
        }

        if (Shop::isFeatureActive() && !$id_shop) {
            $id_shop = (int) Context::getContext()->shop->id;
        }

        $customizations = [];
        if (($customizations['fields'] = Db::getInstance()->executeS('
            SELECT `id_customization_field`, `type`, `required`
            FROM `' . _DB_PREFIX_ . 'customization_field`
            WHERE `id_product` = ' . (int) $product_id . '
            ORDER BY `id_customization_field`')) === false) {
            return false;
        }

        if (empty($customizations['fields'])) {
            return [];
        }

        $customization_field_ids = [];
        foreach ($customizations['fields'] as $customization_field) {
            $customization_field_ids[] = (int) $customization_field['id_customization_field'];
        }

        if (($customization_labels = Db::getInstance()->executeS('
            SELECT `id_customization_field`, `id_lang`, `id_shop`, `name`
            FROM `' . _DB_PREFIX_ . 'customization_field_lang`
            WHERE `id_customization_field` IN (' . implode(', ', $customization_field_ids) . ')' . ($id_shop ? ' AND `id_shop` = ' . (int) $id_shop : '') . '
            ORDER BY `id_customization_field`')) === false) {
            return false;
        }

        foreach ($customization_labels as $customization_label) {
            $customizations['labels'][$customization_label['id_customization_field']][] = $customization_label;
        }

        return $customizations;
    }

    public static function duplicateSpecificPrices($old_product_id, $product_id)
    {
        foreach (SpecificPrice::getIdsByProductId((int) $old_product_id) as $data) {
            $specific_price = new SpecificPrice((int) $data['id_specific_price']);
            if (!$specific_price->duplicate((int) $product_id)) {
                return false;
            }
        }

        return true;
    }

    public static function duplicateCustomizationFields($old_product_id, $product_id)
    {
        // If customization is not activated, return success
        if (!Customization::isFeatureActive()) {
            return true;
        }
        if (($customizations = Product::_getCustomizationFieldsNLabels($old_product_id)) === false) {
            return false;
        }
        if (empty($customizations)) {
            return true;
        }
        foreach ($customizations['fields'] as $customization_field) {
            /* The new datas concern the new product */
            $customization_field['id_product'] = (int) $product_id;
            $old_customization_field_id = (int) $customization_field['id_customization_field'];

            unset($customization_field['id_customization_field']);

            if (!Db::getInstance()->insert('customization_field', $customization_field)
                || !$customization_field_id = Db::getInstance()->Insert_ID()) {
                return false;
            }

            if (isset($customizations['labels'])) {
                foreach ($customizations['labels'][$old_customization_field_id] as $customization_label) {
                    $data = [
                        'id_customization_field' => (int) $customization_field_id,
                        'id_lang' => (int) $customization_label['id_lang'],
                        'id_shop' => (int) $customization_label['id_shop'],
                        'name' => pSQL($customization_label['name']),
                    ];

                    if (!Db::getInstance()->insert('customization_field_lang', $data)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Adds suppliers from old product onto a newly duplicated product.
     *
     * @param int $id_product_old
     * @param int $id_product_new
     */
    public static function duplicateSuppliers($id_product_old, $id_product_new)
    {
        $result = Db::getInstance()->executeS('
        SELECT *
        FROM `' . _DB_PREFIX_ . 'product_supplier`
        WHERE `id_product` = ' . (int) $id_product_old . ' AND `id_product_attribute` = 0');

        foreach ($result as $row) {
            unset($row['id_product_supplier']);
            $row['id_product'] = $id_product_new;
            if (!Db::getInstance()->insert('product_supplier', $row)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the link of the product page of this product.
     */
    public function getLink(Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        return $context->link->getProductLink($this);
    }

    public function getTags($id_lang)
    {
        if (!$this->isFullyLoaded && null === $this->tags) {
            $this->tags = Tag::getProductTags($this->id);
        }

        if (!($this->tags && array_key_exists($id_lang, $this->tags))) {
            return '';
        }

        $result = '';
        foreach ($this->tags[$id_lang] as $tag_name) {
            $result .= $tag_name . ', ';
        }

        return rtrim($result, ', ');
    }

    public static function defineProductImage($row, $id_lang)
    {
        if (isset($row['id_image']) && $row['id_image']) {
            return $row['id_product'] . '-' . $row['id_image'];
        }

        return Language::getIsoById((int) $id_lang) . '-default';
    }

    public static function getProductProperties($id_lang, $row, Context $context = null)
    {
        Hook::exec('actionGetProductPropertiesBefore', [
            'id_lang' => $id_lang,
            'product' => &$row,
            'context' => $context,
        ]);

        if (!$row['id_product']) {
            return false;
        }

        if ($context == null) {
            $context = Context::getContext();
        }

        $id_product_attribute = $row['id_product_attribute'] = (!empty($row['id_product_attribute']) ? (int) $row['id_product_attribute'] : null);

        // Product::getDefaultAttribute is only called if id_product_attribute is missing from the SQL query at the origin of it:
        // consider adding it in order to avoid unnecessary queries
        $row['allow_oosp'] = Product::isAvailableWhenOutOfStock($row['out_of_stock']);
        if (Combination::isFeatureActive() && $id_product_attribute === null
            && ((isset($row['cache_default_attribute']) && ($ipa_default = $row['cache_default_attribute']) !== null)
                || ($ipa_default = Product::getDefaultAttribute($row['id_product'], !$row['allow_oosp'])))) {
            $id_product_attribute = $row['id_product_attribute'] = $ipa_default;
        }
        if (!Combination::isFeatureActive() || !isset($row['id_product_attribute'])) {
            $id_product_attribute = $row['id_product_attribute'] = 0;
        }

        // Tax
        $usetax = !Tax::excludeTaxeOption();

        $cache_key = $row['id_product'] . '-' . $id_product_attribute . '-' . $id_lang . '-' . (int) $usetax;
        if (isset($row['id_product_pack'])) {
            $cache_key .= '-pack' . $row['id_product_pack'];
        }

        if (isset(self::$productPropertiesCache[$cache_key])) {
            return array_merge($row, self::$productPropertiesCache[$cache_key]);
        }

        // Datas
        $row['category'] = Category::getLinkRewrite((int) $row['id_category_default'], (int) $id_lang);
        $row['category_name'] = Db::getInstance()->getValue('SELECT name FROM ' . _DB_PREFIX_ . 'category_lang WHERE id_shop = ' . (int) $context->shop->id . ' AND id_lang = ' . (int) $id_lang . ' AND id_category = ' . (int) $row['id_category_default']);
        $row['link'] = $context->link->getProductLink((int) $row['id_product'], $row['link_rewrite'], $row['category'], $row['ean13']);

        $row['attribute_price'] = 0;
        if ($id_product_attribute) {
            $row['attribute_price'] = (float) Combination::getPrice($id_product_attribute);
        }

        if (isset($row['quantity_wanted'])) {
            // 'quantity_wanted' may very well be zero even if set
            $quantity = max((int) $row['minimal_quantity'], (int) $row['quantity_wanted']);
        } elseif (isset($row['cart_quantity'])) {
            $quantity = max((int) $row['minimal_quantity'], (int) $row['cart_quantity']);
        } else {
            $quantity = (int) $row['minimal_quantity'];
        }

        $row['price_tax_exc'] = Product::getPriceStatic(
            (int) $row['id_product'],
            false,
            $id_product_attribute,
            (self::$_taxCalculationMethod == PS_TAX_EXC ? 2 : 6),
            null,
            false,
            true,
            $quantity
        );

        if (self::$_taxCalculationMethod == PS_TAX_EXC) {
            $row['price_tax_exc'] = Tools::ps_round($row['price_tax_exc'], 2);
            $row['price'] = Product::getPriceStatic(
                (int) $row['id_product'],
                true,
                $id_product_attribute,
                6,
                null,
                false,
                true,
                $quantity
            );
            $row['price_without_reduction'] =
            $row['price_without_reduction_without_tax'] = Product::getPriceStatic(
                (int) $row['id_product'],
                false,
                $id_product_attribute,
                2,
                null,
                false,
                false,
                $quantity
            );
        } else {
            $row['price'] = Tools::ps_round(
                Product::getPriceStatic(
                    (int) $row['id_product'],
                    true,
                    $id_product_attribute,
                    6,
                    null,
                    false,
                    true,
                    $quantity
                ),
                Context::getContext()->getComputingPrecision()
            );
            $row['price_without_reduction'] = Product::getPriceStatic(
                (int) $row['id_product'],
                true,
                $id_product_attribute,
                6,
                null,
                false,
                false,
                $quantity
            );
            $row['price_without_reduction_without_tax'] = Product::getPriceStatic(
                (int) $row['id_product'],
                false,
                $id_product_attribute,
                6,
                null,
                false,
                false,
                $quantity
            );
        }

        $row['reduction'] = Product::getPriceStatic(
            (int) $row['id_product'],
            (bool) $usetax,
            $id_product_attribute,
            6,
            null,
            true,
            true,
            $quantity,
            true,
            null,
            null,
            null,
            $specific_prices
        );

        $row['reduction_without_tax'] = Product::getPriceStatic(
            (int) $row['id_product'],
            false,
            $id_product_attribute,
            6,
            null,
            true,
            true,
            $quantity,
            true,
            null,
            null,
            null,
            $specific_prices
        );

        $row['specific_prices'] = $specific_prices;

        $row['quantity'] = Product::getQuantity(
            (int) $row['id_product'],
            0,
            isset($row['cache_is_pack']) ? $row['cache_is_pack'] : null,
            $context->cart
        );

        $row['quantity_all_versions'] = $row['quantity'];

        if ($row['id_product_attribute']) {
            $row['quantity'] = Product::getQuantity(
                (int) $row['id_product'],
                $id_product_attribute,
                isset($row['cache_is_pack']) ? $row['cache_is_pack'] : null,
                $context->cart
            );

            $row['available_date'] = Product::getAvailableDate(
                (int) $row['id_product'],
                $id_product_attribute
            );
        }

        $row['id_image'] = Product::defineProductImage($row, $id_lang);
        $row['features'] = Product::getFrontFeaturesStatic((int) $id_lang, $row['id_product']);

        $row['attachments'] = [];
        if (!isset($row['cache_has_attachments']) || $row['cache_has_attachments']) {
            $row['attachments'] = Product::getAttachmentsStatic((int) $id_lang, $row['id_product']);
        }

        $row['virtual'] = ((!isset($row['is_virtual']) || $row['is_virtual']) ? 1 : 0);

        // Pack management
        $row['pack'] = (!isset($row['cache_is_pack']) ? Pack::isPack($row['id_product']) : (int) $row['cache_is_pack']);
        $row['packItems'] = $row['pack'] ? Pack::getItemTable($row['id_product'], $id_lang) : [];
        $row['nopackprice'] = $row['pack'] ? Pack::noPackPrice($row['id_product']) : 0;

        if ($row['pack'] && !Pack::isInStock($row['id_product'], $quantity, $context->cart)) {
            $row['quantity'] = 0;
        }

        $row['customization_required'] = false;
        if (isset($row['customizable']) && $row['customizable'] && Customization::isFeatureActive()) {
            if (count(Product::getRequiredCustomizableFieldsStatic((int) $row['id_product']))) {
                $row['customization_required'] = true;
            }
        }

        $attributes = Product::getAttributesParams($row['id_product'], $row['id_product_attribute']);

        foreach ($attributes as $attribute) {
            $row['attributes'][$attribute['id_attribute_group']] = $attribute;
        }

        $row = Product::getTaxesInformations($row, $context);

        $row['ecotax_rate'] = (float) Tax::getProductEcotaxRate($context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});

        if (!isset($row['cover_image_id'])) {
            $cover = static::getCover($row['id_product']);
            if (isset($cover['id_image'])) {
                $row['cover_image_id'] = $cover['id_image'];
            }
        }

        Hook::exec('actionGetProductPropertiesAfter', [
            'id_lang' => $id_lang,
            'product' => &$row,
            'context' => $context,
        ]);

        $combination = new Combination($id_product_attribute);

        if (0 != $combination->unit_price_impact && 0 != $row['unit_price_ratio']) {
            $unitPrice = ($row['price_tax_exc'] / $row['unit_price_ratio']) + $combination->unit_price_impact;
            $row['unit_price_ratio'] = $row['price_tax_exc'] / $unitPrice;
        }

        $row['unit_price'] = ($row['unit_price_ratio'] != 0 ? $row['price'] / $row['unit_price_ratio'] : 0);

        self::$productPropertiesCache[$cache_key] = $row;

        return self::$productPropertiesCache[$cache_key];
    }

    public static function getTaxesInformations($row, Context $context = null)
    {
        static $address = null;

        if ($context === null) {
            $context = Context::getContext();
        }
        if ($address === null) {
            $address = new Address();
        }

        $address->id_country = (int) $context->country->id;
        $address->id_state = 0;
        $address->postcode = 0;

        $tax_manager = TaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct((int) $row['id_product'], $context));
        $row['rate'] = $tax_manager->getTaxCalculator()->getTotalRate();
        $row['tax_name'] = $tax_manager->getTaxCalculator()->getTaxesName();

        return $row;
    }

    public static function getProductsProperties($id_lang, $query_result)
    {
        $results_array = [];

        if (is_array($query_result)) {
            foreach ($query_result as $row) {
                if ($row2 = Product::getProductProperties($id_lang, $row)) {
                    $results_array[] = $row2;
                }
            }
        }

        return $results_array;
    }

    /**
     * Select all features for a given language
     *
     * @param $id_lang Language id
     *
     * @return array Array with feature's data
     */
    public static function getFrontFeaturesStatic($id_lang, $id_product)
    {
        if (!Feature::isFeatureActive()) {
            return [];
        }
        if (!array_key_exists($id_product . '-' . $id_lang, self::$_frontFeaturesCache)) {
            self::$_frontFeaturesCache[$id_product . '-' . $id_lang] = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                '
                SELECT name, value, pf.id_feature, f.position
                FROM ' . _DB_PREFIX_ . 'feature_product pf
                LEFT JOIN ' . _DB_PREFIX_ . 'feature_lang fl ON (fl.id_feature = pf.id_feature AND fl.id_lang = ' . (int) $id_lang . ')
                LEFT JOIN ' . _DB_PREFIX_ . 'feature_value_lang fvl ON (fvl.id_feature_value = pf.id_feature_value AND fvl.id_lang = ' . (int) $id_lang . ')
                LEFT JOIN ' . _DB_PREFIX_ . 'feature f ON (f.id_feature = pf.id_feature AND fl.id_lang = ' . (int) $id_lang . ')
                ' . Shop::addSqlAssociation('feature', 'f') . '
                WHERE pf.id_product = ' . (int) $id_product . '
                ORDER BY f.position ASC'
            );
        }

        return self::$_frontFeaturesCache[$id_product . '-' . $id_lang];
    }

    public function getFrontFeatures($id_lang)
    {
        return Product::getFrontFeaturesStatic($id_lang, $this->id);
    }

    public static function getAttachmentsStatic($id_lang, $id_product)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT *
        FROM ' . _DB_PREFIX_ . 'product_attachment pa
        LEFT JOIN ' . _DB_PREFIX_ . 'attachment a ON a.id_attachment = pa.id_attachment
        LEFT JOIN ' . _DB_PREFIX_ . 'attachment_lang al ON (a.id_attachment = al.id_attachment AND al.id_lang = ' . (int) $id_lang . ')
        WHERE pa.id_product = ' . (int) $id_product);
    }

    public function getAttachments($id_lang)
    {
        return Product::getAttachmentsStatic($id_lang, $this->id);
    }

    /*
    ** Customization management
    */

    public static function getAllCustomizedDatas($id_cart, $id_lang = null, $only_in_cart = true, $id_shop = null, $id_customization = null)
    {
        if (!Customization::isFeatureActive()) {
            return false;
        }

        // No need to query if there isn't any real cart!
        if (!$id_cart) {
            return false;
        }

        if ($id_customization === 0) {
            // Backward compatibility: check if there are no products in cart with specific `id_customization` before returning false
            $product_customizations = (int) Db::getInstance()->getValue('
                SELECT COUNT(`id_customization`) FROM `' . _DB_PREFIX_ . 'cart_product`
                WHERE `id_cart` = ' . (int) $id_cart .
                ' AND `id_customization` != 0');
            if ($product_customizations) {
                return false;
            }
        }

        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        if (Shop::isFeatureActive() && !$id_shop) {
            $id_shop = (int) Context::getContext()->shop->id;
        }

        if (!$result = Db::getInstance()->executeS('
            SELECT cd.`id_customization`, c.`id_address_delivery`, c.`id_product`, cfl.`id_customization_field`, c.`id_product_attribute`,
                cd.`type`, cd.`index`, cd.`value`, cd.`id_module`, cfl.`name`
            FROM `' . _DB_PREFIX_ . 'customized_data` cd
            NATURAL JOIN `' . _DB_PREFIX_ . 'customization` c
            LEFT JOIN `' . _DB_PREFIX_ . 'customization_field_lang` cfl ON (cfl.id_customization_field = cd.`index` AND id_lang = ' . (int) $id_lang .
                ($id_shop ? ' AND cfl.`id_shop` = ' . (int) $id_shop : '') . ')
            WHERE c.`id_cart` = ' . (int) $id_cart .
            ($only_in_cart ? ' AND c.`in_cart` = 1' : '') .
            ((int) $id_customization ? ' AND cd.`id_customization` = ' . (int) $id_customization : '') . '
            ORDER BY `id_product`, `id_product_attribute`, `type`, `index`')) {
            return false;
        }

        $customized_datas = [];

        foreach ($result as $row) {
            if ((int) $row['id_module'] && (int) $row['type'] == Product::CUSTOMIZE_TEXTFIELD) {
                // Hook displayCustomization: Call only the module in question
                // When a module saves a customization programmatically, it should add its ID in the `id_module` column
                $row['value'] = Hook::exec('displayCustomization', ['customization' => $row], (int) $row['id_module']);
            }
            $customized_datas[(int) $row['id_product']][(int) $row['id_product_attribute']][(int) $row['id_address_delivery']][(int) $row['id_customization']]['datas'][(int) $row['type']][] = $row;
        }

        if (!$result = Db::getInstance()->executeS(
            'SELECT `id_product`, `id_product_attribute`, `id_customization`, `id_address_delivery`, `quantity`, `quantity_refunded`, `quantity_returned`
            FROM `' . _DB_PREFIX_ . 'customization`
            WHERE `id_cart` = ' . (int) $id_cart .
            ((int) $id_customization ? ' AND `id_customization` = ' . (int) $id_customization : '') .
            ($only_in_cart ? ' AND `in_cart` = 1' : '')
        )) {
            return false;
        }

        foreach ($result as $row) {
            $customized_datas[(int) $row['id_product']][(int) $row['id_product_attribute']][(int) $row['id_address_delivery']][(int) $row['id_customization']]['quantity'] = (int) $row['quantity'];
            $customized_datas[(int) $row['id_product']][(int) $row['id_product_attribute']][(int) $row['id_address_delivery']][(int) $row['id_customization']]['quantity_refunded'] = (int) $row['quantity_refunded'];
            $customized_datas[(int) $row['id_product']][(int) $row['id_product_attribute']][(int) $row['id_address_delivery']][(int) $row['id_customization']]['quantity_returned'] = (int) $row['quantity_returned'];
            $customized_datas[(int) $row['id_product']][(int) $row['id_product_attribute']][(int) $row['id_address_delivery']][(int) $row['id_customization']]['id_customization'] = (int) $row['id_customization'];
        }

        return $customized_datas;
    }

    public static function addCustomizationPrice(&$products, &$customized_datas)
    {
        if (!$customized_datas) {
            return;
        }

        foreach ($products as &$product_update) {
            if (!Customization::isFeatureActive()) {
                $product_update['customizationQuantityTotal'] = 0;
                $product_update['customizationQuantityRefunded'] = 0;
                $product_update['customizationQuantityReturned'] = 0;
            } else {
                $customization_quantity = 0;
                $customization_quantity_refunded = 0;
                $customization_quantity_returned = 0;

                /* Compatibility */
                $product_id = isset($product_update['id_product']) ? (int) $product_update['id_product'] : (int) $product_update['product_id'];
                $product_attribute_id = isset($product_update['id_product_attribute']) ? (int) $product_update['id_product_attribute'] : (int) $product_update['product_attribute_id'];
                $id_address_delivery = (int) $product_update['id_address_delivery'];
                $product_quantity = isset($product_update['cart_quantity']) ? (int) $product_update['cart_quantity'] : (int) $product_update['product_quantity'];
                $price = isset($product_update['price']) ? $product_update['price'] : $product_update['product_price'];
                if (isset($product_update['price_wt']) && $product_update['price_wt']) {
                    $price_wt = $product_update['price_wt'];
                } else {
                    $price_wt = $price * (1 + ((isset($product_update['tax_rate']) ? $product_update['tax_rate'] : $product_update['rate']) * 0.01));
                }

                if (!isset($customized_datas[$product_id][$product_attribute_id][$id_address_delivery])) {
                    $id_address_delivery = 0;
                }
                if (isset($customized_datas[$product_id][$product_attribute_id][$id_address_delivery])) {
                    foreach ($customized_datas[$product_id][$product_attribute_id][$id_address_delivery] as $customization) {
                        if ((int) $product_update['id_customization'] && $customization['id_customization'] != $product_update['id_customization']) {
                            continue;
                        }
                        $customization_quantity += (int) $customization['quantity'];
                        $customization_quantity_refunded += (int) $customization['quantity_refunded'];
                        $customization_quantity_returned += (int) $customization['quantity_returned'];
                    }
                }

                $product_update['customizationQuantityTotal'] = $customization_quantity;
                $product_update['customizationQuantityRefunded'] = $customization_quantity_refunded;
                $product_update['customizationQuantityReturned'] = $customization_quantity_returned;

                if ($customization_quantity) {
                    $product_update['total_wt'] = $price_wt * ($product_quantity - $customization_quantity);
                    $product_update['total_customization_wt'] = $price_wt * $customization_quantity;
                    $product_update['total'] = $price * ($product_quantity - $customization_quantity);
                    $product_update['total_customization'] = $price * $customization_quantity;
                }
            }
        }
    }

    /*
    ** Add customization price for a single product
    */
    public static function addProductCustomizationPrice(&$product, &$customized_datas)
    {
        if (!$customized_datas) {
            return;
        }

        $products = [$product];
        self::addCustomizationPrice($products, $customized_datas);
        $product = $products[0];
    }

    /*
    ** Customization fields' label management
    */

    protected function _checkLabelField($field, $value)
    {
        if (!Validate::isLabel($value)) {
            return false;
        }
        $tmp = explode('_', $field);
        if (count($tmp) < 4) {
            return false;
        }

        return $tmp;
    }

    protected function _deleteOldLabels()
    {
        $max = [
            Product::CUSTOMIZE_FILE => (int) $this->uploadable_files,
            Product::CUSTOMIZE_TEXTFIELD => (int) $this->text_fields,
        ];

        /* Get customization field ids */
        if ((
            $result = Db::getInstance()->executeS(
            'SELECT `id_customization_field`, `type`
            FROM `' . _DB_PREFIX_ . 'customization_field`
            WHERE `id_product` = ' . (int) $this->id . '
            ORDER BY `id_customization_field`'
            )
        ) === false) {
            return false;
        }

        if (empty($result)) {
            return true;
        }

        $customization_fields = [
            Product::CUSTOMIZE_FILE => [],
            Product::CUSTOMIZE_TEXTFIELD => [],
        ];

        foreach ($result as $row) {
            $customization_fields[(int) $row['type']][] = (int) $row['id_customization_field'];
        }

        $extra_file = count($customization_fields[Product::CUSTOMIZE_FILE]) - $max[Product::CUSTOMIZE_FILE];
        $extra_text = count($customization_fields[Product::CUSTOMIZE_TEXTFIELD]) - $max[Product::CUSTOMIZE_TEXTFIELD];

        /* If too much inside the database, deletion */
        if ($extra_file > 0 && count($customization_fields[Product::CUSTOMIZE_FILE]) - $extra_file >= 0 &&
        (!Db::getInstance()->execute(
            'DELETE `' . _DB_PREFIX_ . 'customization_field`,`' . _DB_PREFIX_ . 'customization_field_lang`
            FROM `' . _DB_PREFIX_ . 'customization_field` JOIN `' . _DB_PREFIX_ . 'customization_field_lang`
            WHERE `' . _DB_PREFIX_ . 'customization_field`.`id_product` = ' . (int) $this->id . '
            AND `' . _DB_PREFIX_ . 'customization_field`.`type` = ' . Product::CUSTOMIZE_FILE . '
            AND `' . _DB_PREFIX_ . 'customization_field_lang`.`id_customization_field` = `' . _DB_PREFIX_ . 'customization_field`.`id_customization_field`
            AND `' . _DB_PREFIX_ . 'customization_field`.`id_customization_field` >= ' . (int) $customization_fields[Product::CUSTOMIZE_FILE][count($customization_fields[Product::CUSTOMIZE_FILE]) - $extra_file]
        ))) {
            return false;
        }

        if ($extra_text > 0 && count($customization_fields[Product::CUSTOMIZE_TEXTFIELD]) - $extra_text >= 0 &&
        (!Db::getInstance()->execute(
            'DELETE `' . _DB_PREFIX_ . 'customization_field`,`' . _DB_PREFIX_ . 'customization_field_lang`
            FROM `' . _DB_PREFIX_ . 'customization_field` JOIN `' . _DB_PREFIX_ . 'customization_field_lang`
            WHERE `' . _DB_PREFIX_ . 'customization_field`.`id_product` = ' . (int) $this->id . '
            AND `' . _DB_PREFIX_ . 'customization_field`.`type` = ' . Product::CUSTOMIZE_TEXTFIELD . '
            AND `' . _DB_PREFIX_ . 'customization_field_lang`.`id_customization_field` = `' . _DB_PREFIX_ . 'customization_field`.`id_customization_field`
            AND `' . _DB_PREFIX_ . 'customization_field`.`id_customization_field` >= ' . (int) $customization_fields[Product::CUSTOMIZE_TEXTFIELD][count($customization_fields[Product::CUSTOMIZE_TEXTFIELD]) - $extra_text]
        ))) {
            return false;
        }

        // Refresh cache of feature detachable
        Configuration::updateGlobalValue('PS_CUSTOMIZATION_FEATURE_ACTIVE', Customization::isCurrentlyUsed());

        return true;
    }

    protected function _createLabel($languages, $type)
    {
        // Label insertion
        if (!Db::getInstance()->execute('
            INSERT INTO `' . _DB_PREFIX_ . 'customization_field` (`id_product`, `type`, `required`)
            VALUES (' . (int) $this->id . ', ' . (int) $type . ', 0)') ||
            !$id_customization_field = (int) Db::getInstance()->Insert_ID()) {
            return false;
        }

        // Multilingual label name creation
        $values = '';

        foreach ($languages as $language) {
            foreach (Shop::getContextListShopID() as $id_shop) {
                $values .= '(' . (int) $id_customization_field . ', ' . (int) $language['id_lang'] . ', ' . (int) $id_shop . ',\'\'), ';
            }
        }

        $values = rtrim($values, ', ');
        if (!Db::getInstance()->execute('
            INSERT INTO `' . _DB_PREFIX_ . 'customization_field_lang` (`id_customization_field`, `id_lang`, `id_shop`, `name`)
            VALUES ' . $values)) {
            return false;
        }

        // Set cache of feature detachable to true
        Configuration::updateGlobalValue('PS_CUSTOMIZATION_FEATURE_ACTIVE', '1');

        return true;
    }

    public function createLabels($uploadable_files, $text_fields)
    {
        $languages = Language::getLanguages();
        if ((int) $uploadable_files > 0) {
            for ($i = 0; $i < (int) $uploadable_files; ++$i) {
                if (!$this->_createLabel($languages, Product::CUSTOMIZE_FILE)) {
                    return false;
                }
            }
        }

        if ((int) $text_fields > 0) {
            for ($i = 0; $i < (int) $text_fields; ++$i) {
                if (!$this->_createLabel($languages, Product::CUSTOMIZE_TEXTFIELD)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function updateLabels()
    {
        $has_required_fields = 0;
        foreach ($_POST as $field => $value) {
            /* Label update */
            if (strncmp($field, 'label_', 6) == 0) {
                if (!$tmp = $this->_checkLabelField($field, $value)) {
                    return false;
                }
                /* Multilingual label name update */
                if (Shop::isFeatureActive()) {
                    foreach (Shop::getContextListShopID() as $id_shop) {
                        if (!Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'customization_field_lang`
                        (`id_customization_field`, `id_lang`, `id_shop`, `name`) VALUES (' . (int) $tmp[2] . ', ' . (int) $tmp[3] . ', ' . (int) $id_shop . ', \'' . pSQL($value) . '\')
                        ON DUPLICATE KEY UPDATE `name` = \'' . pSQL($value) . '\'')) {
                            return false;
                        }
                    }
                } elseif (!Db::getInstance()->execute('
                    INSERT INTO `' . _DB_PREFIX_ . 'customization_field_lang`
                    (`id_customization_field`, `id_lang`, `name`) VALUES (' . (int) $tmp[2] . ', ' . (int) $tmp[3] . ', \'' . pSQL($value) . '\')
                    ON DUPLICATE KEY UPDATE `name` = \'' . pSQL($value) . '\'')) {
                    return false;
                }

                $is_required = isset($_POST['require_' . (int) $tmp[1] . '_' . (int) $tmp[2]]) ? 1 : 0;
                $has_required_fields |= $is_required;
                /* Require option update */
                if (!Db::getInstance()->execute(
                    'UPDATE `' . _DB_PREFIX_ . 'customization_field`
                    SET `required` = ' . (int) $is_required . '
                    WHERE `id_customization_field` = ' . (int) $tmp[2]
                )) {
                    return false;
                }
            }
        }

        if ($has_required_fields && !ObjectModel::updateMultishopTable('product', ['customizable' => 2], 'a.id_product = ' . (int) $this->id)) {
            return false;
        }

        if (!$this->_deleteOldLabels()) {
            return false;
        }

        return true;
    }

    public function getCustomizationFields($id_lang = false, $id_shop = null)
    {
        if (!Customization::isFeatureActive()) {
            return false;
        }

        if (Shop::isFeatureActive() && !$id_shop) {
            $id_shop = (int) Context::getContext()->shop->id;
        }

        // Hide the modules fields in the front-office
        // When a module adds a customization programmatically, it should set the `is_module` to 1
        $context = Context::getContext();
        $front = isset($context->controller->controller_type) && in_array($context->controller->controller_type, ['front']);

        if (!$result = Db::getInstance()->executeS('
            SELECT cf.`id_customization_field`, cf.`type`, cf.`required`, cfl.`name`, cfl.`id_lang`
            FROM `' . _DB_PREFIX_ . 'customization_field` cf
            NATURAL JOIN `' . _DB_PREFIX_ . 'customization_field_lang` cfl
            WHERE cf.`id_product` = ' . (int) $this->id . ($id_lang ? ' AND cfl.`id_lang` = ' . (int) $id_lang : '') .
            ($id_shop ? ' AND cfl.`id_shop` = ' . (int) $id_shop : '') .
            ($front ? ' AND !cf.`is_module`' : '') . '
            AND cf.`is_deleted` = 0
            ORDER BY cf.`id_customization_field`')
        ) {
            return false;
        }

        if ($id_lang) {
            return $result;
        }

        $customization_fields = [];
        foreach ($result as $row) {
            $customization_fields[(int) $row['type']][(int) $row['id_customization_field']][(int) $row['id_lang']] = $row;
        }

        return $customization_fields;
    }

    /**
     * check if product has an activated and required customizationFields.
     *
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     */
    public function hasActivatedRequiredCustomizableFields()
    {
        if (!Customization::isFeatureActive()) {
            return false;
        }

        return (bool) Db::getInstance()->executeS(
            '
            SELECT 1
            FROM `' . _DB_PREFIX_ . 'customization_field`
            WHERE `id_product` = ' . (int) $this->id . '
            AND `required` = 1
            AND `is_deleted` = 0'
        );
    }

    public function getCustomizationFieldIds()
    {
        if (!Customization::isFeatureActive()) {
            return [];
        }

        return Db::getInstance()->executeS('
            SELECT `id_customization_field`, `type`, `required`
            FROM `' . _DB_PREFIX_ . 'customization_field`
            WHERE `id_product` = ' . (int) $this->id);
    }

    public function getRequiredCustomizableFields()
    {
        if (!Customization::isFeatureActive()) {
            return [];
        }

        return Product::getRequiredCustomizableFieldsStatic($this->id);
    }

    public static function getRequiredCustomizableFieldsStatic($id)
    {
        if (!$id || !Customization::isFeatureActive()) {
            return [];
        }

        return Db::getInstance()->executeS(
            '
            SELECT `id_customization_field`, `type`
            FROM `' . _DB_PREFIX_ . 'customization_field`
            WHERE `id_product` = ' . (int) $id . '
            AND `required` = 1 AND `is_deleted` = 0'
        );
    }

    public function hasAllRequiredCustomizableFields(Context $context = null)
    {
        if (!Customization::isFeatureActive()) {
            return true;
        }
        if (!$context) {
            $context = Context::getContext();
        }

        $fields = $context->cart->getProductCustomization($this->id, null, true);
        if (($required_fields = $this->getRequiredCustomizableFields()) === false) {
            return false;
        }

        $fields_present = [];
        foreach ($fields as $field) {
            $fields_present[] = ['id_customization_field' => $field['index'], 'type' => $field['type']];
        }

        if (is_array($required_fields) && count($required_fields)) {
            foreach ($required_fields as $required_field) {
                if (!in_array($required_field, $fields_present)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Return the list of old temp products.
     *
     * @return array
     */
    public static function getOldTempProducts()
    {
        $sql = 'SELECT id_product FROM `' . _DB_PREFIX_ . 'product` WHERE state=' . \Product::STATE_TEMP . ' AND date_upd < NOW() - INTERVAL 1 DAY';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);
    }

    /**
     * Checks if the product is in at least one of the submited categories.
     *
     * @param int $id_product
     * @param array $categories array of category arrays
     *
     * @return bool is the product in at least one category
     */
    public static function idIsOnCategoryId($id_product, $categories)
    {
        if (!((int) $id_product > 0) || !is_array($categories) || empty($categories)) {
            return false;
        }
        $sql = 'SELECT id_product FROM `' . _DB_PREFIX_ . 'category_product` WHERE `id_product` = ' . (int) $id_product . ' AND `id_category` IN (';
        foreach ($categories as $category) {
            $sql .= (int) $category['id_category'] . ',';
        }
        $sql = rtrim($sql, ',') . ')';

        $hash = md5($sql);
        if (!isset(self::$_incat[$hash])) {
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql)) {
                return false;
            }
            self::$_incat[$hash] = (Db::getInstance(_PS_USE_SQL_SLAVE_)->numRows() > 0 ? true : false);
        }

        return self::$_incat[$hash];
    }

    public function getNoPackPrice()
    {
        $context = Context::getContext();

        return Tools::getContextLocale($context)->formatPrice(Pack::noPackPrice((int) $this->id), $context->currency->iso_code);
    }

    public function checkAccess($id_customer)
    {
        return Product::checkAccessStatic((int) $this->id, (int) $id_customer);
    }

    public static function checkAccessStatic($id_product, $id_customer)
    {
        if (!Group::isFeatureActive()) {
            return true;
        }

        $cache_id = 'Product::checkAccess_' . (int) $id_product . '-' . (int) $id_customer . (!$id_customer ? '-' . (int) Group::getCurrent()->id : '');
        if (!Cache::isStored($cache_id)) {
            if (!$id_customer) {
                $result = (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
                SELECT ctg.`id_group`
                FROM `' . _DB_PREFIX_ . 'category_product` cp
                INNER JOIN `' . _DB_PREFIX_ . 'category_group` ctg ON (ctg.`id_category` = cp.`id_category`)
                WHERE cp.`id_product` = ' . (int) $id_product . ' AND ctg.`id_group` = ' . (int) Group::getCurrent()->id);
            } else {
                $result = (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
                SELECT cg.`id_group`
                FROM `' . _DB_PREFIX_ . 'category_product` cp
                INNER JOIN `' . _DB_PREFIX_ . 'category_group` ctg ON (ctg.`id_category` = cp.`id_category`)
                INNER JOIN `' . _DB_PREFIX_ . 'customer_group` cg ON (cg.`id_group` = ctg.`id_group`)
                WHERE cp.`id_product` = ' . (int) $id_product . ' AND cg.`id_customer` = ' . (int) $id_customer);
            }

            Cache::store($cache_id, $result);

            return $result;
        }

        return Cache::retrieve($cache_id);
    }

    /**
     * Add a stock movement for current product.
     *
     * Since 1.5, this method only permit to add/remove available quantities of the current product in the current shop
     *
     * @see StockManager if you want to manage real stock
     * @see StockAvailable if you want to manage available quantities for sale on your shop(s)
     * @deprecated since 1.5.0
     *
     * @param int $quantity
     * @param int $id_reason - useless
     * @param int $id_product_attribute
     * @param int $id_order - DEPRECATED
     * @param int $id_employee - DEPRECATED
     *
     * @return bool
     */
    public function addStockMvt($quantity, $id_reason, $id_product_attribute = null, $id_order = null, $id_employee = null)
    {
        if (!$this->id || !$id_reason) {
            return false;
        }

        if ($id_product_attribute == null) {
            $id_product_attribute = 0;
        }

        $reason = new StockMvtReason((int) $id_reason);
        if (!Validate::isLoadedObject($reason)) {
            return false;
        }

        $quantity = abs((int) $quantity) * $reason->sign;

        return StockAvailable::updateQuantity($this->id, $id_product_attribute, $quantity);
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
            FROM `' . _DB_PREFIX_ . 'stock_mvt` sm
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
                sm.id_product = pl.id_product
                AND pl.id_lang = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl') . '
            )
            LEFT JOIN `' . _DB_PREFIX_ . 'stock_mvt_reason_lang` mrl ON (
                sm.id_stock_mvt_reason = mrl.id_stock_mvt_reason
                AND mrl.id_lang = ' . (int) $id_lang . '
            )
            LEFT JOIN `' . _DB_PREFIX_ . 'employee` e ON (
                e.id_employee = sm.id_employee
            )
            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON (
                pac.id_product_attribute = sm.id_product_attribute
            )
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (
                al.id_attribute = pac.id_attribute
                AND al.id_lang = ' . (int) $id_lang . '
            )
            WHERE sm.id_product=' . (int) $this->id . '
            GROUP BY sm.id_stock_mvt
        ');
    }

    public static function getUrlRewriteInformations($id_product)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT pl.`id_lang`, pl.`link_rewrite`, p.`ean13`, cl.`link_rewrite` AS category_rewrite
            FROM `' . _DB_PREFIX_ . 'product` p
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product`' . Shop::addSqlRestrictionOnLang('pl') . ')
            ' . Shop::addSqlAssociation('product', 'p') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'lang` l ON (pl.`id_lang` = l.`id_lang`)
            LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (cl.`id_category` = product_shop.`id_category_default`  AND cl.`id_lang` = pl.`id_lang`' . Shop::addSqlRestrictionOnLang('cl') . ')
            WHERE p.`id_product` = ' . (int) $id_product . '
            AND l.`active` = 1
        ');
    }

    public function getIdTaxRulesGroup()
    {
        return $this->id_tax_rules_group;
    }

    public static function getIdTaxRulesGroupByIdProduct($id_product, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        $key = 'product_id_tax_rules_group_' . (int) $id_product . '_' . (int) $context->shop->id;
        if (!Cache::isStored($key)) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
                            SELECT `id_tax_rules_group`
                            FROM `' . _DB_PREFIX_ . 'product_shop`
                            WHERE `id_product` = ' . (int) $id_product . ' AND id_shop=' . (int) $context->shop->id);
            Cache::store($key, (int) $result);

            return (int) $result;
        }

        return Cache::retrieve($key);
    }

    /**
     * Returns tax rate.
     *
     * @param Address|null $address
     *
     * @return float The total taxes rate applied to the product
     */
    public function getTaxesRate(Address $address = null)
    {
        if (!$address || !$address->id_country) {
            $address = Address::initialize();
        }

        $tax_manager = TaxManagerFactory::getManager($address, $this->id_tax_rules_group);
        $tax_calculator = $tax_manager->getTaxCalculator();

        return $tax_calculator->getTotalRate();
    }

    /**
     * Webservice getter : get product features association.
     *
     * @return array
     */
    public function getWsProductFeatures()
    {
        $rows = $this->getFeatures();
        foreach ($rows as $keyrow => $row) {
            foreach ($row as $keyfeature => $feature) {
                if ($keyfeature == 'id_feature') {
                    $rows[$keyrow]['id'] = $feature;
                    unset($rows[$keyrow]['id_feature']);
                }
                unset(
                    $rows[$keyrow]['id_product'],
                    $rows[$keyrow]['custom']
                );
            }
            asort($rows[$keyrow]);
        }

        return $rows;
    }

    /**
     * Webservice setter : set product features association.
     *
     * @param $product_features Product Feature ids
     *
     * @return bool
     */
    public function setWsProductFeatures($product_features)
    {
        Db::getInstance()->execute(
            '
            DELETE FROM `' . _DB_PREFIX_ . 'feature_product`
            WHERE `id_product` = ' . (int) $this->id
        );
        foreach ($product_features as $product_feature) {
            $this->addFeaturesToDB($product_feature['id'], $product_feature['id_feature_value']);
        }

        return true;
    }

    /**
     * Webservice getter : get virtual field default combination.
     *
     * @return int
     */
    public function getWsDefaultCombination()
    {
        return Product::getDefaultAttribute($this->id);
    }

    /**
     * Webservice setter : set virtual field default combination.
     *
     * @param int $id_combination id default combination
     *
     * @return bool
     */
    public function setWsDefaultCombination($id_combination)
    {
        $this->deleteDefaultAttributes();

        return $this->setDefaultAttribute((int) $id_combination);
    }

    /**
     * Webservice getter : get category ids of current product for association.
     *
     * @return array
     */
    public function getWsCategories()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT cp.`id_category` AS id
            FROM `' . _DB_PREFIX_ . 'category_product` cp
            LEFT JOIN `' . _DB_PREFIX_ . 'category` c ON (c.id_category = cp.id_category)
            ' . Shop::addSqlAssociation('category', 'c') . '
            WHERE cp.`id_product` = ' . (int) $this->id
        );

        return $result;
    }

    /**
     * Webservice setter : set category ids of current product for association.
     *
     * @param array $category_ids category ids
     *
     * @return bool
     */
    public function setWsCategories($category_ids)
    {
        $ids = [];
        foreach ($category_ids as $value) {
            if ($value instanceof Category) {
                $ids[] = (int) $value->id;
            } elseif (is_array($value) && array_key_exists('id', $value)) {
                $ids[] = (int) $value['id'];
            } else {
                $ids[] = (int) $value;
            }
        }
        $ids = array_unique($ids);

        $positions = Db::getInstance()->executeS(
                'SELECT `id_category`, `position`
                FROM `' . _DB_PREFIX_ . 'category_product`
                WHERE `id_product` = ' . (int) $this->id
        );

        $max_positions = Db::getInstance()->executeS(
                'SELECT `id_category`, max(`position`) as maximum
                FROM `' . _DB_PREFIX_ . 'category_product`
                GROUP BY id_category'
        );

        $positions_lookup = [];
        $max_position_lookup = [];

        foreach ($positions as $row) {
            $positions_lookup[(int) $row['id_category']] = (int) $row['position'];
        }
        foreach ($max_positions as $row) {
            $max_position_lookup[(int) $row['id_category']] = (int) $row['maximum'];
        }

        $return = true;
        if ($this->deleteCategories() && !empty($ids)) {
            $sql_values = [];
            foreach ($ids as $id) {
                $pos = 0;
                if (array_key_exists((int) $id, $positions_lookup)) {
                    $pos = (int) $positions_lookup[(int) $id] + 1;
                } elseif (array_key_exists((int) $id, $max_position_lookup)) {
                    $pos = (int) $max_position_lookup[(int) $id] + 1;
                }

                $sql_values[] = '(' . (int) $id . ', ' . (int) $this->id . ', ' . $pos . ')';
            }

            $return = Db::getInstance()->execute(
                '
                INSERT INTO `' . _DB_PREFIX_ . 'category_product` (`id_category`, `id_product`, `position`)
                VALUES ' . implode(',', $sql_values)
            );
        }

        Hook::exec('actionProductUpdate', ['id_product' => (int) $this->id]);

        return $return;
    }

    /**
     * Webservice getter : get product accessories ids of current product for association.
     *
     * @return array
     */
    public function getWsAccessories()
    {
        $result = Db::getInstance()->executeS(
            'SELECT p.`id_product` AS id
            FROM `' . _DB_PREFIX_ . 'accessory` a
            LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (p.id_product = a.id_product_2)
            ' . Shop::addSqlAssociation('product', 'p') . '
            WHERE a.`id_product_1` = ' . (int) $this->id
        );

        return $result;
    }

    /**
     * Webservice setter : set product accessories ids of current product for association.
     *
     * @param $accessories product ids
     */
    public function setWsAccessories($accessories)
    {
        $this->deleteAccessories();
        foreach ($accessories as $accessory) {
            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'accessory` (`id_product_1`, `id_product_2`) VALUES (' . (int) $this->id . ', ' . (int) $accessory['id'] . ')');
        }

        return true;
    }

    /**
     * Webservice getter : get combination ids of current product for association.
     *
     * @return array
     */
    public function getWsCombinations()
    {
        $result = Db::getInstance()->executeS(
            'SELECT pa.`id_product_attribute` as id
            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
            WHERE pa.`id_product` = ' . (int) $this->id
        );

        return $result;
    }

    /**
     * Webservice setter : set combination ids of current product for association.
     *
     * @param $combinations combination ids
     */
    public function setWsCombinations($combinations)
    {
        // No hook exec
        $ids_new = [];
        foreach ($combinations as $combination) {
            $ids_new[] = (int) $combination['id'];
        }

        $ids_orig = [];
        $original = Db::getInstance()->executeS(
            'SELECT pa.`id_product_attribute` as id
            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
            WHERE pa.`id_product` = ' . (int) $this->id
        );

        if (is_array($original)) {
            foreach ($original as $id) {
                $ids_orig[] = $id['id'];
            }
        }

        $all_ids = [];
        $all = Db::getInstance()->executeS('SELECT pa.`id_product_attribute` as id FROM `' . _DB_PREFIX_ . 'product_attribute` pa ' . Shop::addSqlAssociation('product_attribute', 'pa'));
        if (is_array($all)) {
            foreach ($all as $id) {
                $all_ids[] = $id['id'];
            }
        }

        $to_add = [];
        foreach ($ids_new as $id) {
            if (!in_array($id, $ids_orig)) {
                $to_add[] = $id;
            }
        }

        $to_delete = [];
        foreach ($ids_orig as $id) {
            if (!in_array($id, $ids_new)) {
                $to_delete[] = $id;
            }
        }

        // Delete rows
        if (count($to_delete) > 0) {
            foreach ($to_delete as $id) {
                $combination = new Combination($id);
                $combination->delete();
            }
        }

        foreach ($to_add as $id) {
            // Update id_product if exists else create
            if (in_array($id, $all_ids)) {
                Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'product_attribute` SET id_product = ' . (int) $this->id . ' WHERE id_product_attribute=' . $id);
            } else {
                Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'product_attribute` (`id_product`) VALUES (' . (int) $this->id . ')');
            }
        }

        return true;
    }

    /**
     * Webservice getter : get product option ids of current product for association.
     *
     * @return array
     */
    public function getWsProductOptionValues()
    {
        $result = Db::getInstance()->executeS('SELECT DISTINCT pac.id_attribute as id
            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON (pac.id_product_attribute = pa.id_product_attribute)
            WHERE pa.id_product = ' . (int) $this->id);

        return $result;
    }

    /**
     * Webservice getter : get virtual field position in category.
     *
     * @return int
     */
    public function getWsPositionInCategory()
    {
        $result = Db::getInstance()->executeS('SELECT position
            FROM `' . _DB_PREFIX_ . 'category_product`
            WHERE id_category = ' . (int) $this->id_category_default . '
            AND id_product = ' . (int) $this->id);
        if (count($result) > 0) {
            return $result[0]['position'];
        }

        return '';
    }

    /**
     * Webservice setter : set virtual field position in category.
     *
     * @return bool
     */
    public function setWsPositionInCategory($position)
    {
        if ($position < 0) {
            WebserviceRequest::getInstance()->setError(500, $this->trans('You cannot set a negative position, the minimum for a position is 0.', [], 'Admin.Catalog.Notification'), 134);
        }
        $result = Db::getInstance()->executeS('
            SELECT `id_product`
            FROM `' . _DB_PREFIX_ . 'category_product`
            WHERE `id_category` = ' . (int) $this->id_category_default . '
            ORDER BY `position`
        ');
        if (($position > 0) && ($position + 1 > count($result))) {
            WebserviceRequest::getInstance()->setError(500, $this->trans('You cannot set a position greater than the total number of products in the category, minus 1 (position numbering starts at 0).', [], 'Admin.Catalog.Notification'), 135);
        }

        foreach ($result as &$value) {
            $value = $value['id_product'];
        }
        $current_position = $this->getWsPositionInCategory();

        if ($current_position && isset($result[$current_position])) {
            $save = $result[$current_position];
            unset($result[$current_position]);
            array_splice($result, (int) $position, 0, $save);
        }

        foreach ($result as $position => $id_product) {
            Db::getInstance()->update('category_product', [
                'position' => $position,
            ], '`id_category` = ' . (int) $this->id_category_default . ' AND `id_product` = ' . (int) $id_product);
        }

        return true;
    }

    /**
     * Webservice getter : get virtual field id_default_image in category.
     *
     * @return int
     */
    public function getCoverWs()
    {
        $result = $this->getCover($this->id);

        return $result['id_image'];
    }

    /**
     * Webservice setter : set virtual field id_default_image in category.
     *
     * @return bool
     */
    public function setCoverWs($id_image)
    {
        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'image_shop` image_shop, `' . _DB_PREFIX_ . 'image` i
            SET image_shop.`cover` = NULL
            WHERE i.`id_product` = ' . (int) $this->id . ' AND i.id_image = image_shop.id_image
            AND image_shop.id_shop=' . (int) Context::getContext()->shop->id);

        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'image_shop`
            SET `cover` = 1 WHERE `id_image` = ' . (int) $id_image);

        return true;
    }

    /**
     * Webservice getter : get image ids of current product for association.
     *
     * @return array
     */
    public function getWsImages()
    {
        return Db::getInstance()->executeS('
            SELECT i.`id_image` as id
            FROM `' . _DB_PREFIX_ . 'image` i
            ' . Shop::addSqlAssociation('image', 'i') . '
            WHERE i.`id_product` = ' . (int) $this->id . '
            ORDER BY i.`position`');
    }

    public function getWsStockAvailables()
    {
        return Db::getInstance()->executeS('SELECT `id_stock_available` id, `id_product_attribute`
            FROM `' . _DB_PREFIX_ . 'stock_available`
            WHERE `id_product`=' . (int) $this->id . StockAvailable::addSqlShopRestriction());
    }

    public function getWsTags()
    {
        return Db::getInstance()->executeS('
            SELECT `id_tag` as id
            FROM `' . _DB_PREFIX_ . 'product_tag`
            WHERE `id_product` = ' . (int) $this->id);
    }

    /**
     * Webservice setter : set tag ids of current product for association.
     *
     * @param $tag_ids tag ids
     */
    public function setWsTags($tag_ids)
    {
        $ids = [];
        foreach ($tag_ids as $value) {
            $ids[] = $value['id'];
        }
        if ($this->deleteWsTags()) {
            if ($ids) {
                $sql_values = [];
                $ids = array_map('intval', $ids);
                foreach ($ids as $position => $id) {
                    $id_lang = Db::getInstance()->getValue('SELECT `id_lang` FROM `' . _DB_PREFIX_ . 'tag` WHERE `id_tag`=' . (int) $id);
                    $sql_values[] = '(' . (int) $this->id . ', ' . (int) $id . ', ' . (int) $id_lang . ')';
                }
                $result = Db::getInstance()->execute(
                    '
                    INSERT INTO `' . _DB_PREFIX_ . 'product_tag` (`id_product`, `id_tag`, `id_lang`)
                    VALUES ' . implode(',', $sql_values)
                );

                return $result;
            }
        }

        return true;
    }

    /**
     * Delete products tags entries without delete tags for webservice usage.
     *
     * @return array Deletion result
     */
    public function deleteWsTags()
    {
        return Db::getInstance()->delete('product_tag', 'id_product = ' . (int) $this->id);
    }

    public function getWsManufacturerName()
    {
        return Manufacturer::getNameById((int) $this->id_manufacturer);
    }

    public static function resetEcoTax()
    {
        return ObjectModel::updateMultishopTable('product', [
            'ecotax' => 0,
        ]);
    }

    /**
     * Set Group reduction if needed.
     */
    public function setGroupReduction()
    {
        return GroupReduction::setProductReduction($this->id);
    }

    /**
     * Checks if reference exists.
     *
     * @return bool
     */
    public function existsRefInDatabase($reference)
    {
        $row = Db::getInstance()->getRow('
        SELECT `reference`
        FROM `' . _DB_PREFIX_ . 'product` p
        WHERE p.reference = "' . pSQL($reference) . '"');

        return isset($row['reference']);
    }

    /**
     * Get all product attributes ids.
     *
     * @since 1.5.0
     *
     * @param int $id_product the id of the product
     *
     * @return array product attribute id list
     */
    public static function getProductAttributesIds($id_product, $shop_only = false)
    {
        return Db::getInstance()->executeS('
        SELECT pa.id_product_attribute
        FROM `' . _DB_PREFIX_ . 'product_attribute` pa' .
        ($shop_only ? Shop::addSqlAssociation('product_attribute', 'pa') : '') . '
        WHERE pa.`id_product` = ' . (int) $id_product);
    }

    /**
     * Get label by lang and value by lang too.
     *
     * @param int $id_product
     * @param int $product_attribute_id
     *
     * @return array
     */
    public static function getAttributesParams($id_product, $id_product_attribute)
    {
        if ($id_product_attribute == 0) {
            return [];
        }
        $id_lang = (int) Context::getContext()->language->id;
        $cache_id = 'Product::getAttributesParams_' . (int) $id_product . '-' . (int) $id_product_attribute . '-' . (int) $id_lang;

        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->executeS('
            SELECT a.`id_attribute`, a.`id_attribute_group`, al.`name`, agl.`name` as `group`, pa.`reference`, pa.`ean13`, pa.`isbn`, pa.`upc`, pa.`mpn`
            FROM `' . _DB_PREFIX_ . 'attribute` a
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al
                ON (al.`id_attribute` = a.`id_attribute` AND al.`id_lang` = ' . (int) $id_lang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                ON (pac.`id_attribute` = a.`id_attribute`)
            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa
                ON (pa.`id_product_attribute` = pac.`id_product_attribute`)
            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl
                ON (a.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $id_lang . ')
            WHERE pa.`id_product` = ' . (int) $id_product . '
                AND pac.`id_product_attribute` = ' . (int) $id_product_attribute . '
                AND agl.`id_lang` = ' . (int) $id_lang);
            Cache::store($cache_id, $result);
        } else {
            $result = Cache::retrieve($cache_id);
        }

        return $result;
    }

    /**
     * @param int $id_product
     */
    public static function getAttributesInformationsByProduct($id_product)
    {
        $result = Db::getInstance()->executeS('
        SELECT DISTINCT a.`id_attribute`, a.`id_attribute_group`, al.`name` as `attribute`, agl.`name` as `group`,pa.`reference`, pa.`ean13`, pa.`isbn`, pa.`upc`, pa.`mpn`
        FROM `' . _DB_PREFIX_ . 'attribute` a
        LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al
            ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) Context::getContext()->language->id . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl
            ON (a.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) Context::getContext()->language->id . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac
            ON (a.`id_attribute` = pac.`id_attribute`)
        LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa
            ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
        ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
        ' . Shop::addSqlAssociation('attribute', 'pac') . '
        WHERE pa.`id_product` = ' . (int) $id_product);

        return $result;
    }

    /**
     * @return bool
     */
    public function hasCombinations()
    {
        if (null === $this->id || 0 >= $this->id) {
            return false;
        }
        $attributes = self::getAttributesInformationsByProduct($this->id);

        return !empty($attributes);
    }

    /**
     * Get an id_product_attribute by an id_product and one or more
     * id_attribute.
     *
     * e.g: id_product 8 with id_attribute 4 (size medium) and
     * id_attribute 5 (color blue) returns id_product_attribute 9 which
     * is the dress size medium and color blue.
     *
     * @param int $idProduct
     * @param int|int[] $idAttributes
     * @param bool $findBest
     *
     * @return int
     *
     * @throws PrestaShopException
     */
    public static function getIdProductAttributeByIdAttributes($idProduct, $idAttributes, $findBest = false)
    {
        $idProduct = (int) $idProduct;

        if (!is_array($idAttributes) && is_numeric($idAttributes)) {
            $idAttributes = [(int) $idAttributes];
        }

        if (!is_array($idAttributes) || empty($idAttributes)) {
            throw new PrestaShopException(sprintf('Invalid parameter $idAttributes with value: "%s"', print_r($idAttributes, true)));
        }

        $idAttributesImploded = implode(',', array_map('intval', $idAttributes));
        $idProductAttribute = Db::getInstance()->getValue(
            '
            SELECT
                pac.`id_product_attribute`
            FROM
                `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                INNER JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON pa.id_product_attribute = pac.id_product_attribute
            WHERE
                pa.id_product = ' . $idProduct . '
                AND pac.id_attribute IN (' . $idAttributesImploded . ')
            GROUP BY
                pac.`id_product_attribute`
            HAVING
                COUNT(pa.id_product) = ' . count($idAttributes)
        );

        if ($idProductAttribute === false && $findBest) {
            //find the best possible combination
            //first we order $idAttributes by the group position
            $orderred = [];
            $result = Db::getInstance()->executeS(
                '
                SELECT
                    a.`id_attribute`
                FROM
                    `' . _DB_PREFIX_ . 'attribute` a
                    INNER JOIN `' . _DB_PREFIX_ . 'attribute_group` g ON a.`id_attribute_group` = g.`id_attribute_group`
                WHERE
                    a.`id_attribute` IN (' . $idAttributesImploded . ')
                ORDER BY
                    g.`position` ASC'
            );

            foreach ($result as $row) {
                $orderred[] = $row['id_attribute'];
            }

            while ($idProductAttribute === false && count($orderred) > 1) {
                array_pop($orderred);
                $idProductAttribute = Db::getInstance()->getValue(
                    '
                    SELECT
                        pac.`id_product_attribute`
                    FROM
                        `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                        INNER JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON pa.id_product_attribute = pac.id_product_attribute
                    WHERE
                        pa.id_product = ' . (int) $idProduct . '
                        AND pac.id_attribute IN (' . implode(',', array_map('intval', $orderred)) . ')
                    GROUP BY
                        pac.id_product_attribute
                    HAVING
                        COUNT(pa.id_product) = ' . count($orderred)
                );
            }
        }

        if (empty($idProductAttribute)) {
            throw new PrestaShopObjectNotFoundException('Can not retrieve the id_product_attribute');
        }

        return $idProductAttribute;
    }

    /**
     * @deprecated 1.7.3.1
     * @see Product::getIdProductAttributeByIdAttributes()
     */
    public static function getIdProductAttributesByIdAttributes($id_product, $id_attributes, $find_best = false)
    {
        return self::getIdProductAttributeByIdAttributes($id_product, $id_attributes, $find_best);
    }

    /**
     * Get the combination url anchor of the product.
     *
     * @param int $id_product_attribute
     *
     * @return string
     */
    public function getAnchor($id_product_attribute, $with_id = false)
    {
        $attributes = Product::getAttributesParams($this->id, $id_product_attribute);
        $anchor = '#';
        $sep = Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR');
        foreach ($attributes as &$a) {
            foreach ($a as &$b) {
                $b = str_replace($sep, '_', Tools::link_rewrite($b));
            }
            $anchor .= '/' . ($with_id && isset($a['id_attribute']) && $a['id_attribute'] ? (int) $a['id_attribute'] . $sep : '') . $a['group'] . $sep . $a['name'];
        }

        return $anchor;
    }

    /**
     * Gets the name of a given product, in the given lang.
     *
     * @since 1.5.0
     *
     * @param int $id_product
     * @param int $id_product_attribute Optional
     * @param int $id_lang Optional
     *
     * @return string
     */
    public static function getProductName($id_product, $id_product_attribute = null, $id_lang = null)
    {
        // use the lang in the context if $id_lang is not defined
        if (!$id_lang) {
            $id_lang = (int) Context::getContext()->language->id;
        }

        // creates the query object
        $query = new DbQuery();

        // selects different names, if it is a combination
        if ($id_product_attribute) {
            $query->select('IFNULL(CONCAT(pl.name, \' : \', GROUP_CONCAT(DISTINCT agl.`name`, \' - \', al.name SEPARATOR \', \')),pl.name) as name');
        } else {
            $query->select('DISTINCT pl.name as name');
        }

        // adds joins & where clauses for combinations
        if ($id_product_attribute) {
            $query->from('product_attribute', 'pa');
            $query->join(Shop::addSqlAssociation('product_attribute', 'pa'));
            $query->innerJoin('product_lang', 'pl', 'pl.id_product = pa.id_product AND pl.id_lang = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl'));
            $query->leftJoin('product_attribute_combination', 'pac', 'pac.id_product_attribute = pa.id_product_attribute');
            $query->leftJoin('attribute', 'atr', 'atr.id_attribute = pac.id_attribute');
            $query->leftJoin('attribute_lang', 'al', 'al.id_attribute = atr.id_attribute AND al.id_lang = ' . (int) $id_lang);
            $query->leftJoin('attribute_group_lang', 'agl', 'agl.id_attribute_group = atr.id_attribute_group AND agl.id_lang = ' . (int) $id_lang);
            $query->where('pa.id_product = ' . (int) $id_product . ' AND pa.id_product_attribute = ' . (int) $id_product_attribute);
        } else {
            // or just adds a 'where' clause for a simple product

            $query->from('product_lang', 'pl');
            $query->where('pl.id_product = ' . (int) $id_product);
            $query->where('pl.id_lang = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl'));
        }

        return Db::getInstance()->getValue($query);
    }

    public function addWs($autodate = true, $null_values = false)
    {
        $success = $this->add($autodate, $null_values);
        if ($success && Configuration::get('PS_SEARCH_INDEXATION')) {
            Search::indexation(false, $this->id);
        }

        return $success;
    }

    public function updateWs($null_values = false)
    {
        if (null === $this->price) {
            $this->price = Product::getPriceStatic((int) $this->id, false, null, 6, null, false, true, 1, false, null, null, null, $this->specificPrice);
        }

        if (null === $this->unit_price) {
            $this->unit_price = ($this->unit_price_ratio != 0 ? $this->price / $this->unit_price_ratio : 0);
        }

        $success = parent::update($null_values);
        if ($success && Configuration::get('PS_SEARCH_INDEXATION')) {
            Search::indexation(false, $this->id);
        }
        Hook::exec('actionProductUpdate', ['id_product' => (int) $this->id]);

        return $success;
    }

    /**
     * For a given product, returns its real quantity.
     *
     * @since 1.5.0
     *
     * @param int $id_product
     * @param int $id_product_attribute
     * @param int $id_warehouse
     * @param int $id_shop
     *
     * @return int real_quantity
     */
    public static function getRealQuantity($id_product, $id_product_attribute = 0, $id_warehouse = 0, $id_shop = null)
    {
        static $manager = null;

        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && null === $manager) {
            $manager = StockManagerFactory::getManager();
        }

        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && Product::usesAdvancedStockManagement($id_product) &&
            StockAvailable::dependsOnStock($id_product, $id_shop)) {
            return $manager->getProductRealQuantities($id_product, $id_product_attribute, $id_warehouse, true);
        } else {
            return StockAvailable::getQuantityAvailableByProduct($id_product, $id_product_attribute, $id_shop);
        }
    }

    /**
     * For a given product, tells if it uses the advanced stock management.
     *
     * @since 1.5.0
     *
     * @param int $id_product
     *
     * @return bool
     */
    public static function usesAdvancedStockManagement($id_product)
    {
        $query = new DbQuery();
        $query->select('product_shop.advanced_stock_management');
        $query->from('product', 'p');
        $query->join(Shop::addSqlAssociation('product', 'p'));
        $query->where('p.id_product = ' . (int) $id_product);

        return (bool) Db::getInstance()->getValue($query);
    }

    /**
     * This method allows to flush price cache.
     *
     * @since 1.5.0
     */
    public static function flushPriceCache()
    {
        self::$_prices = [];
        self::$_pricesLevel2 = [];
    }

    /**
     * Get list of parent categories.
     *
     * @since 1.5.0
     *
     * @param int $id_lang
     *
     * @return array
     */
    public function getParentCategories($id_lang = null)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $interval = Category::getInterval($this->id_category_default);
        $sql = new DbQuery();
        $sql->from('category', 'c');
        $sql->leftJoin('category_lang', 'cl', 'c.id_category = cl.id_category AND id_lang = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('cl'));
        $sql->where('c.nleft <= ' . (int) $interval['nleft'] . ' AND c.nright >= ' . (int) $interval['nright']);
        $sql->orderBy('c.nleft');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    /**
     * Fill the variables used for stock management.
     */
    public function loadStockData()
    {
        if (false === Validate::isLoadedObject($this)) {
            return;
        }

        // Default product quantity is available quantity to sell in current shop
        $this->quantity = StockAvailable::getQuantityAvailableByProduct($this->id, 0);
        $this->out_of_stock = StockAvailable::outOfStock($this->id);
        $this->depends_on_stock = StockAvailable::dependsOnStock($this->id);
        $this->location = StockAvailable::getLocation($this->id);

        if (Context::getContext()->shop->getContext() == Shop::CONTEXT_GROUP && Context::getContext()->shop->getContextShopGroup()->share_stock == 1) {
            $this->advanced_stock_management = $this->useAdvancedStockManagement();
        }
    }

    public function useAdvancedStockManagement()
    {
        return Db::getInstance()->getValue(
            '
                    SELECT `advanced_stock_management`
                    FROM ' . _DB_PREFIX_ . 'product_shop
                    WHERE id_product=' . (int) $this->id . Shop::addSqlRestriction()
                );
    }

    public function setAdvancedStockManagement($value)
    {
        $this->advanced_stock_management = (int) $value;
        if (Context::getContext()->shop->getContext() == Shop::CONTEXT_GROUP && Context::getContext()->shop->getContextShopGroup()->share_stock == 1) {
            Db::getInstance()->execute(
                '
                UPDATE `' . _DB_PREFIX_ . 'product_shop`
                SET `advanced_stock_management`=' . (int) $value . '
                WHERE id_product=' . (int) $this->id . Shop::addSqlRestriction()
            );
        } else {
            $this->setFieldsToUpdate(['advanced_stock_management' => true]);
            $this->save();
        }
    }

    /**
     * get the default category according to the shop.
     */
    public function getDefaultCategory()
    {
        $default_category = Db::getInstance()->getValue('
            SELECT product_shop.`id_category_default`
            FROM `' . _DB_PREFIX_ . 'product` p
            ' . Shop::addSqlAssociation('product', 'p') . '
            WHERE p.`id_product` = ' . (int) $this->id);

        if (!$default_category) {
            return ['id_category_default' => Context::getContext()->shop->id_category];
        } else {
            return $default_category;
        }
    }

    public static function getShopsByProduct($id_product)
    {
        return Db::getInstance()->executeS('
            SELECT `id_shop`
            FROM `' . _DB_PREFIX_ . 'product_shop`
            WHERE `id_product` = ' . (int) $id_product);
    }

    /**
     * Remove all downloadable files for product and its attributes.
     *
     * @return bool
     */
    public function deleteDownload()
    {
        $result = true;
        $collection_download = new PrestaShopCollection('ProductDownload');
        $collection_download->where('id_product', '=', $this->id);
        foreach ($collection_download as $product_download) {
            /* @var ProductDownload $product_download */
            $result &= $product_download->delete($product_download->checkFile());
        }

        return $result;
    }

    /**
     * @deprecated 1.5.0.10
     * @see Product::getAttributeCombinations()
     *
     * @param int $id_lang
     */
    public function getAttributeCombinaisons($id_lang)
    {
        Tools::displayAsDeprecated('Use Product::getAttributeCombinations($id_lang)');

        return $this->getAttributeCombinations($id_lang);
    }

    /**
     * @deprecated 1.5.0.10
     * @see Product::deleteAttributeCombination()
     *
     * @param int $id_product_attribute
     */
    public function deleteAttributeCombinaison($id_product_attribute)
    {
        Tools::displayAsDeprecated('Use Product::deleteAttributeCombination($id_product_attribute)');

        return $this->deleteAttributeCombination($id_product_attribute);
    }

    /**
     * Get the product type (simple, virtual, pack).
     *
     * @since in 1.5.0
     *
     * @return int
     */
    public function getType()
    {
        if (!$this->id) {
            return Product::PTYPE_SIMPLE;
        }
        if (Pack::isPack($this->id)) {
            return Product::PTYPE_PACK;
        }
        if ($this->is_virtual) {
            return Product::PTYPE_VIRTUAL;
        }

        return Product::PTYPE_SIMPLE;
    }

    public function hasAttributesInOtherShops()
    {
        return (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            '
            SELECT pa.id_product_attribute
            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` pas ON (pa.`id_product_attribute` = pas.`id_product_attribute`)
            WHERE pa.`id_product` = ' . (int) $this->id
        );
    }

    public static function getIdTaxRulesGroupMostUsed()
    {
        return Db::getInstance()->getValue(
            '
                    SELECT id_tax_rules_group
                    FROM (
                        SELECT COUNT(*) n, product_shop.id_tax_rules_group
                        FROM ' . _DB_PREFIX_ . 'product p
                        ' . Shop::addSqlAssociation('product', 'p') . '
                        JOIN ' . _DB_PREFIX_ . 'tax_rules_group trg ON (product_shop.id_tax_rules_group = trg.id_tax_rules_group)
                        WHERE trg.active = 1 AND trg.deleted = 0
                        GROUP BY product_shop.id_tax_rules_group
                        ORDER BY n DESC
                        LIMIT 1
                    ) most_used'
                );
    }

    /**
     * For a given ean13 reference, returns the corresponding id.
     *
     * @param string $ean13
     *
     * @return int id
     */
    public static function getIdByEan13($ean13)
    {
        if (empty($ean13)) {
            return 0;
        }

        if (!Validate::isEan13($ean13)) {
            return 0;
        }

        $query = new DbQuery();
        $query->select('p.id_product');
        $query->from('product', 'p');
        $query->where('p.ean13 = \'' . pSQL($ean13) . '\'');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * For a given reference, returns the corresponding id.
     *
     * @param string $reference
     *
     * @return int id
     */
    public static function getIdByReference($reference)
    {
        if (empty($reference)) {
            return 0;
        }

        if (!Validate::isReference($reference)) {
            return 0;
        }

        $query = new DbQuery();
        $query->select('p.id_product');
        $query->from('product', 'p');
        $query->where('p.reference = \'' . pSQL($reference) . '\'');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    public function getWsType()
    {
        $type_information = [
            Product::PTYPE_SIMPLE => 'simple',
            Product::PTYPE_PACK => 'pack',
            Product::PTYPE_VIRTUAL => 'virtual',
        ];

        return $type_information[$this->getType()];
    }

    /*
        Create the link rewrite if not exists or invalid on product creation
    */
    public function modifierWsLinkRewrite()
    {
        foreach ($this->name as $id_lang => $name) {
            if (empty($this->link_rewrite[$id_lang])) {
                $this->link_rewrite[$id_lang] = Tools::link_rewrite($name);
            } elseif (!Validate::isLinkRewrite($this->link_rewrite[$id_lang])) {
                $this->link_rewrite[$id_lang] = Tools::link_rewrite($this->link_rewrite[$id_lang]);
            }
        }

        return true;
    }

    public function getWsProductBundle()
    {
        return Db::getInstance()->executeS('SELECT id_product_item as id, id_product_attribute_item as id_product_attribute, quantity FROM ' . _DB_PREFIX_ . 'pack WHERE id_product_pack = ' . (int) $this->id);
    }

    public function setWsType($type_str)
    {
        $reverse_type_information = [
            'simple' => Product::PTYPE_SIMPLE,
            'pack' => Product::PTYPE_PACK,
            'virtual' => Product::PTYPE_VIRTUAL,
        ];

        if (!isset($reverse_type_information[$type_str])) {
            return false;
        }

        $type = $reverse_type_information[$type_str];

        if (Pack::isPack((int) $this->id) && $type != Product::PTYPE_PACK) {
            Pack::deleteItems($this->id);
        }

        $this->cache_is_pack = ($type == Product::PTYPE_PACK);
        $this->is_virtual = ($type == Product::PTYPE_VIRTUAL);

        return true;
    }

    public function setWsProductBundle($items)
    {
        if ($this->is_virtual) {
            return false;
        }

        Pack::deleteItems($this->id);

        foreach ($items as $item) {
            // Combination of a product is optional, and can be omitted.
            if (!isset($item['product_attribute_id'])) {
                $item['product_attribute_id'] = 0;
            }
            if ((int) $item['id'] > 0) {
                Pack::addItem($this->id, (int) $item['id'], (int) $item['quantity'], (int) $item['product_attribute_id']);
            }
        }

        return true;
    }

    public function isColorUnavailable($id_attribute, $id_shop)
    {
        return Db::getInstance()->getValue(
            '
            SELECT sa.id_product_attribute
            FROM ' . _DB_PREFIX_ . 'stock_available sa
            WHERE id_product=' . (int) $this->id . ' AND quantity <= 0
            ' . StockAvailable::addSqlShopRestriction(null, $id_shop, 'sa') . '
            AND EXISTS (
                SELECT 1
                FROM ' . _DB_PREFIX_ . 'product_attribute pa
                JOIN ' . _DB_PREFIX_ . 'product_attribute_shop product_attribute_shop
                    ON (product_attribute_shop.id_product_attribute = pa.id_product_attribute AND product_attribute_shop.id_shop=' . (int) $id_shop . ')
                JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac
                    ON (pac.id_product_attribute AND product_attribute_shop.id_product_attribute)
                WHERE sa.id_product_attribute = pa.id_product_attribute AND pa.id_product=' . (int) $this->id . ' AND pac.id_attribute=' . (int) $id_attribute . '
            )'
        );
    }

    public static function getColorsListCacheId($id_product, $full = true)
    {
        $cache_id = 'productlist_colors';
        if ($id_product) {
            $cache_id .= '|' . (int) $id_product;
        }

        if ($full) {
            $cache_id .= '|' . (int) Context::getContext()->shop->id . '|' . (int) Context::getContext()->cookie->id_lang;
        }

        return $cache_id;
    }

    public static function setPackStockType($id_product, $pack_stock_type)
    {
        return Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'product p
        ' . Shop::addSqlAssociation('product', 'p') . ' SET product_shop.pack_stock_type = ' . (int) $pack_stock_type . ' WHERE p.`id_product` = ' . (int) $id_product);
    }

    /**
     * Gets a list of IDs from a list of IDs/Refs. The result will avoid duplicates, and checks if given IDs/Refs exists in DB.
     * Useful when a product list should be checked before a bulk operation on them (Only 1 query => performances).
     *
     * @return array the IDs list, whithout duplicate and only existing ones
     */
    public static function getExistingIdsFromIdsOrRefs($ids_or_refs)
    {
        // separate IDs and Refs
        $ids = [];
        $refs = [];
        $whereStatements = [];
        foreach ((is_array($ids_or_refs) ? $ids_or_refs : [$ids_or_refs]) as $id_or_ref) {
            if (is_numeric($id_or_ref)) {
                $ids[] = (int) $id_or_ref;
            } elseif (is_string($id_or_ref)) {
                $refs[] = '\'' . pSQL($id_or_ref) . '\'';
            }
        }

        // construct WHERE statement with OR combination
        if (count($ids) > 0) {
            $whereStatements[] = ' p.id_product IN (' . implode(',', $ids) . ') ';
        }
        if (count($refs) > 0) {
            $whereStatements[] = ' p.reference IN (' . implode(',', $refs) . ') ';
        }
        if (!count($whereStatements)) {
            return false;
        }

        $results = Db::getInstance()->executeS('
        SELECT DISTINCT `id_product`
        FROM `' . _DB_PREFIX_ . 'product` p
        WHERE ' . implode(' OR ', $whereStatements));

        // simplify array since there is 1 useless dimension.
        // FIXME : find a better way to avoid this, directly in SQL?
        foreach ($results as $k => $v) {
            $results[$k] = (int) $v['id_product'];
        }

        return $results;
    }

    /**
     * Get object of redirect_type.
     *
     * @return bool|string
     */
    public function getRedirectType()
    {
        switch ($this->redirect_type) {
            case ProductInterface::REDIRECT_TYPE_CATEGORY_MOVED_PERMANENTLY:
            case ProductInterface::REDIRECT_TYPE_CATEGORY_FOUND:
                return 'category';

                break;

            case ProductInterface::REDIRECT_TYPE_PRODUCT_MOVED_PERMANENTLY:
            case ProductInterface::REDIRECT_TYPE_PRODUCT_FOUND:
                return 'product';

                break;
        }

        return false;
    }

    /**
     * Return an array of customization fields IDs.
     *
     * @return array|false
     */
    public function getUsedCustomizationFieldsIds()
    {
        return Db::getInstance()->executeS(
            'SELECT cd.`index` FROM `' . _DB_PREFIX_ . 'customized_data` cd
            LEFT JOIN `' . _DB_PREFIX_ . 'customization_field` cf ON cf.`id_customization_field` = cd.`index`
            WHERE cf.`id_product` = ' . (int) $this->id
        );
    }

    /**
     * Remove unused customization for the product.
     *
     * @param array $customizationIds - Array of customization fields IDs
     *
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     */
    public function deleteUnusedCustomizationFields($customizationIds)
    {
        $return = true;
        if (is_array($customizationIds) && !empty($customizationIds)) {
            $toDeleteIds = implode(',', $customizationIds);
            $return &= Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'customization_field` WHERE
            `id_product` = ' . (int) $this->id . ' AND `id_customization_field` IN (' . $toDeleteIds . ')');

            $return &= Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'customization_field_lang` WHERE
            `id_customization_field` IN (' . $toDeleteIds . ')');
        }

        if (!$return) {
            throw new PrestaShopDatabaseException('An error occurred while deletion the customization fields');
        }

        return $return;
    }

    /**
     * Update the customization fields to be deleted if not used.
     *
     * @param array $customizationIds - Array of excluded customization fields IDs
     *
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     */
    public function softDeleteCustomizationFields($customizationIds)
    {
        $return = true;
        $updateQuery = 'UPDATE `' . _DB_PREFIX_ . 'customization_field` cf
            SET cf.`is_deleted` = 1
            WHERE
            cf.`id_product` = ' . (int) $this->id . '
            AND cf.`is_deleted` = 0 ';

        if (is_array($customizationIds) && !empty($customizationIds)) {
            $updateQuery .= 'AND cf.`id_customization_field` NOT IN (' . implode(',', array_map('intval', $customizationIds)) . ')';
        }

        $return &= Db::getInstance()->execute($updateQuery);

        if (!$return) {
            throw new PrestaShopDatabaseException('An error occurred while soft deletion the customization fields');
        }

        return $return;
    }

    /**
     * Update default supplier data
     *
     * @param int $idSupplier
     * @param float $wholesalePrice
     * @param string $supplierReference
     *
     * @return bool
     */
    public function updateDefaultSupplierData(int $idSupplier, string $supplierReference, float $wholesalePrice): bool
    {
        if (!$this->id) {
            return false;
        }

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'product` ' .
             'SET ' .
             'id_supplier = %d, ' .
             'supplier_reference = "%s", ' .
             'wholesale_price = "%s" ' .
             'WHERE id_product = %d';

        return Db::getInstance()->execute(
            sprintf(
                $sql,
                $idSupplier,
                pSQL($supplierReference),
                $wholesalePrice,
                $this->id
            )
        );
    }
}
