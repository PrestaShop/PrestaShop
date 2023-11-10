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

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductSettings;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Ean13;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Isbn;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Reference;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Upc;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;

class ProductCore extends ObjectModel
{
    /**
     * @var string Tax name
     *
     * @deprecated Since 1.4
     */
    public $tax_name;

    /** @var float Tax rate */
    public $tax_rate;

    /** @var int Manufacturer identifier */
    public $id_manufacturer;

    /** @var int Supplier identifier */
    public $id_supplier;

    /** @var int default Category identifier */
    public $id_category_default;

    /** @var int default Shop identifier */
    public $id_shop_default;

    /** @var string Manufacturer name */
    public $manufacturer_name;

    /** @var string Supplier name */
    public $supplier_name;

    /** @var string|array Name or array of names by id_lang */
    public $name;

    /** @var string|array Long description or array of long description by id_lang */
    public $description;

    /** @var string|array Short description or array of short description by id_lang */
    public $description_short;

    /**
     * @deprecated since 1.7.8 and will be removed in future version.
     * @see StockAvailable::$quantity instead
     *
     * @var int Quantity available
     */
    public $quantity = 0;

    /** @var int Minimal quantity for add to cart */
    public $minimal_quantity = 1;

    /** @var int|null Low stock for mail alert */
    public $low_stock_threshold = null;

    /** @var bool Low stock mail alert activated */
    public $low_stock_alert = false;

    /** @var string|array Text when in stock or array of text by id_lang */
    public $available_now;

    /** @var string|array Text when not in stock but available to order or array of text by id_lang */
    public $available_later;

    /** @var float|null Price */
    public $price = 0;

    /** @var array|int|null Will be filled by reference by priceCalculation() */
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

    /** @var float|null price for product's unity */
    public $unit_price = 0;

    /** @var float price for product's unity ratio */
    public $unit_price_ratio = 0;

    /** @var float|null Ecotax */
    public $ecotax = 0;

    /** @var string Reference */
    public $reference;

    /**
     * @var string Supplier Reference
     *
     * @deprecated since 1.7.7.0
     */
    public $supplier_reference;

    /**
     * @deprecated since 1.7.8
     * @see StockAvailable::$location instead
     *
     * @var string Location
     */
    public $location = '';

    /** @var string|float Width in default width unit */
    public $width = 0;

    /** @var string|float Height in default height unit */
    public $height = 0;

    /** @var string|float Depth in default depth unit */
    public $depth = 0;

    /** @var string|float Weight in default weight unit */
    public $weight = 0;

    /** @var string Ean-13 barcode */
    public $ean13;

    /** @var string ISBN */
    public $isbn;

    /** @var string Upc barcode */
    public $upc;

    /** @var string MPN */
    public $mpn;

    /** @var string|string[] Friendly URL or array of friendly URL by id_lang */
    public $link_rewrite;

    /** @var string|array Meta description or array of meta description by id_lang */
    public $meta_description;

    /**
     * @deprecated
     */
    public $meta_keywords;

    /** @var string|array Meta title or array of meta title by id_lang */
    public $meta_title;

    /**
     * @var mixed
     *
     * @deprecated Unused
     */
    public $quantity_discount = 0;

    /** @var bool|int Product customization */
    public $customizable;

    /** @var bool|null Product is new */
    public $new = null;

    /** @var int Number of uploadable files (concerning customizable products) */
    public $uploadable_files;

    /** @var int Number of text fields */
    public $text_fields;

    /** @var bool Product status */
    public $active = true;

    /**
     * @var string Redirection type
     *
     * @see RedirectType
     */
    public $redirect_type = RedirectType::TYPE_DEFAULT;

    /**
     * @var int Product identifier or Category identifier depends on redirect_type
     */
    public $id_type_redirected = 0;

    /** @var bool Product available for order */
    public $available_for_order = true;

    /** @var string Available for order date in mysql format Y-m-d */
    public $available_date = DateTimeUtil::NULL_DATE;

    /** @var bool Will the condition select should be visible for this product ? */
    public $show_condition = false;

    /** @var string Enumerated (enum) product condition (new, used, refurbished) */
    public $condition;

    /** @var bool Show price of Product */
    public $show_price = true;

    /** @var bool is the product indexed in the search index? */
    public $indexed = false;

    /** @var string ENUM('both', 'catalog', 'search', 'none') front office visibility */
    public $visibility;

    /** @var string Object creation date in mysql format Y-m-d H:i:s */
    public $date_add;

    /** @var string Object last modification date in mysql format Y-m-d H:i:s */
    public $date_upd;

    /** @var array Tags data */
    public $tags;

    /** @var int temporary or saved object */
    public $state = self::STATE_SAVED;

    /**
     * @var float Base price of the product
     *
     * @deprecated 1.6.0.13
     */
    public $base_price;

    /**
     * @var int|null TaxRulesGroup identifier
     */
    public $id_tax_rules_group;

    /**
     * @var int
     *          We keep this variable for retrocompatibility for themes
     *
     * @deprecated 1.5.0
     */
    public $id_color_default = 0;

    /**
     * @deprecated since 1.7.8 and will be removed in future version.
     * This property was only relevant to advanced stock management and that feature is not maintained anymore.
     *
     * @var bool Tells if the product uses the advanced stock management
     */
    public $advanced_stock_management = false;

    /**
     * @deprecated since 1.7.8 and will be removed in future version.
     * @see StockAvailable::$out_of_stock instead
     *
     * @var int
     *          - O Deny orders
     *          - 1 Allow orders
     *          - 2 Use global setting
     */
    public $out_of_stock = OutOfStockType::OUT_OF_STOCK_DEFAULT;

    /**
     * @deprecated since 1.7.8 and will be removed in future version.
     * This property was only relevant to advanced stock management and that feature is not maintained anymore.
     *
     * @var bool|null
     */
    public $depends_on_stock = false;

    /**
     * @var bool
     */
    public $isFullyLoaded = false;

    /**
     * @var bool
     */
    public $cache_is_pack;

    /**
     * @var bool
     */
    public $cache_has_attachments;

    /**
     * @var bool
     */
    public $is_virtual;

    /**
     * @var int
     */
    public $id_pack_product_attribute;

    /**
     * @var int
     */
    public $cache_default_attribute;

    /**
     * @var string|string[] If product is populated, this property contain the rewrite link of the default category
     */
    public $category;

    /**
     * @var int tell the type of stock management to apply on the pack
     */
    public $pack_stock_type = PackStockType::STOCK_TYPE_DEFAULT;

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
     * @var string[]
     */
    public $delivery_in_stock;

    /**
     * Delivery out-stock information.
     *
     * Long description for delivery out-stock product information.
     *
     * @var string[]
     */
    public $delivery_out_stock;

    /**
     * @var bool|null
     */
    public $customization_required;
    /**
     * @var int|null
     */
    public $pack_quantity;

    /**
     * For now default value remains undefined, to keep compatibility with page v1 and former products.
     * But once the v2 is merged the default value should be ProductType::TYPE_STANDARD
     *
     * @var string
     */
    public $product_type = ProductType::TYPE_UNDEFINED;

    /**
     * @var int
     */
    public $id_product;

    /**
     * @var int|null
     */
    public static $_taxCalculationMethod = null;

    /** @var array Price cache */
    protected static $_prices = [];

    /** @var array */
    protected static $_pricesLevel2 = [];

    /** @var array */
    protected static $_incat = [];

    /** @var array */
    protected static $_combinations = [];

    /**
     * Associations between the ids of base combinations and their duplicates.
     * Used for duplicating specific prices when duplicating a product.
     *
     * @var array
     */
    protected static $_combination_associations = [];

    /**
     * @deprecated Since 1.5.6.1
     *
     * @var array
     */
    protected static $_cart_quantity = [];

    /**
     * @deprecated Since 1.5.0.9
     *
     * @var array
     */
    protected static $_tax_rules_group = [];

    /** @var array */
    protected static $_cacheFeatures = [];

    /** @var array */
    protected static $_frontFeaturesCache = [];

    /** @var array */
    protected static $productPropertiesCache = [];

    /**
     * @deprecated Since 1.5.0.1 Unused
     *
     * @var array cache stock data in getStock() method
     */
    protected static $cacheStock = [];

    /** @var int|null */
    protected static $psEcotaxTaxRulesGroupId = null;

    /**
     * Product can be temporary saved in database
     */
    public const STATE_TEMP = 0;
    public const STATE_SAVED = 1;

    /**
     * @var array Contains object definition
     *
     * @see ObjectModel::$definition
     */
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
            'reference' => ['type' => self::TYPE_STRING, 'validate' => 'isReference', 'size' => Reference::MAX_LENGTH],
            'supplier_reference' => ['type' => self::TYPE_STRING, 'validate' => 'isReference', 'size' => 64],
            'location' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'width' => ['type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'],
            'height' => ['type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'],
            'depth' => ['type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'],
            'weight' => ['type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'],
            'quantity_discount' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'ean13' => ['type' => self::TYPE_STRING, 'validate' => 'isEan13', 'size' => Ean13::MAX_LENGTH],
            'isbn' => ['type' => self::TYPE_STRING, 'validate' => 'isIsbn', 'size' => Isbn::MAX_LENGTH],
            'upc' => ['type' => self::TYPE_STRING, 'validate' => 'isUpc', 'size' => Upc::MAX_LENGTH],
            'mpn' => ['type' => self::TYPE_STRING, 'validate' => 'isMpn', 'size' => ProductSettings::MAX_MPN_LENGTH],
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
            'product_type' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                // For now undefined value is still allowed, in 179 we should use ProductType::AVAILABLE_TYPES here
                'values' => [
                    ProductType::TYPE_STANDARD,
                    ProductType::TYPE_PACK,
                    ProductType::TYPE_VIRTUAL,
                    ProductType::TYPE_COMBINATIONS,
                    ProductType::TYPE_UNDEFINED,
                ],
                // This default value should be replaced with ProductType::TYPE_STANDARD in 179 when the v2 page is fully migrated
                'default' => ProductType::TYPE_UNDEFINED,
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
            'unit_price' => ['type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice'],
            /*
             * Only the DB field is deprecated because unit_price is the new reference, we need to keep the class field though
             * @deprecated in 8.0 this DB column will be removed in a future version
             */
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
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => false, 'size' => ProductSettings::MAX_NAME_LENGTH],
            'description' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
            'description_short' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
            'available_now' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => ProductSettings::MAX_AVAILABLE_NOW_LABEL_LENGTH],
            'available_later' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'IsGenericName', 'size' => ProductSettings::MAX_AVAILABLE_LATER_LABEL_LENGTH],
        ],
        'associations' => [
            'manufacturer' => ['type' => self::HAS_ONE],
            'supplier' => ['type' => self::HAS_ONE],
            'default_category' => ['type' => self::HAS_ONE, 'field' => 'id_category_default', 'object' => 'Category'],
            'tax_rules_group' => ['type' => self::HAS_ONE],
            'categories' => ['type' => self::HAS_MANY, 'field' => 'id_category', 'object' => 'Category', 'association' => 'category_product'],
            'stock_availables' => ['type' => self::HAS_MANY, 'field' => 'id_stock_available', 'object' => 'StockAvailable', 'association' => 'stock_availables'],
            'attachments' => ['type' => self::HAS_MANY, 'field' => 'id_attachment', 'object' => 'Attachment', 'association' => 'product_attachment'],
        ],
    ];

    /** @var array */
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
            'tags' => [
                'resource' => 'tag',
                'fields' => [
                    'id' => ['required' => true],
                ],
            ],
            'stock_availables' => [
                'resource' => 'stock_available',
                'fields' => [
                    'id' => ['required' => true],
                    'id_product_attribute' => ['required' => true],
                ],
                'setter' => false,
            ],
            'attachments' => [
                'resource' => 'attachment',
                'api' => 'attachments',
                'fields' => [
                    'id' => ['required' => true],
                ],
            ],
            'accessories' => [
                'resource' => 'product',
                'api' => 'products',
                'fields' => [
                    'id' => [
                        'required' => true,
                        'xlink_resource' => 'products',
                    ],
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

    public const CUSTOMIZE_FILE = 0;
    public const CUSTOMIZE_TEXTFIELD = 1;

    /**
     * Note:  prefix is "PTYPE" because TYPE_ is used in ObjectModel (definition).
     */
    public const PTYPE_SIMPLE = 0;
    public const PTYPE_PACK = 1;
    public const PTYPE_VIRTUAL = 2;

    /**
     * @param int|null $id_product Product identifier
     * @param bool $full Load with price, tax rate, manufacturer name, supplier name, tags, stocks...
     * @param int|null $id_lang Language identifier
     * @param int|null $id_shop Shop identifier
     * @param Context|null $context Context to use for retrieve cart
     */
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
            $this->tags = Tag::getProductTags((int) $this->id);

            $this->loadStockData();
        }

        $ecotaxEnabled = (bool) Configuration::get('PS_USE_ECOTAX');
        $this->fillUnitRatio($ecotaxEnabled);

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
        if (null === $this->update_fields || !empty($this->update_fields['unity'])) {
            $fields['unity'] = pSQL($this->unity);
        }

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function add($autodate = true, $null_values = false)
    {
        if ($this->is_virtual) {
            $this->product_type = ProductType::TYPE_VIRTUAL;
        }

        if (!parent::add($autodate, $null_values)) {
            return false;
        }

        $id_shop_list = Shop::getContextListShopID();
        if (count($this->id_shop_list)) {
            $id_shop_list = $this->id_shop_list;
        }

        if ($this->getType() == Product::PTYPE_VIRTUAL) {
            foreach ($id_shop_list as $value) {
                StockAvailable::setProductOutOfStock((int) $this->id, OutOfStockType::OUT_OF_STOCK_AVAILABLE, $value);
            }

            if ($this->active && !Configuration::get('PS_VIRTUAL_PROD_FEATURE_ACTIVE')) {
                Configuration::updateGlobalValue('PS_VIRTUAL_PROD_FEATURE_ACTIVE', '1');
            }
        } else {
            foreach ($id_shop_list as $value) {
                StockAvailable::setProductOutOfStock((int) $this->id, OutOfStockType::OUT_OF_STOCK_DEFAULT, $value);
            }
        }

        $this->setGroupReduction();
        $this->updateUnitRatio();

        Hook::exec('actionProductSave', ['id_product' => (int) $this->id, 'product' => $this]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function update($null_values = false)
    {
        if ($this->is_virtual) {
            $this->product_type = ProductType::TYPE_VIRTUAL;
        }

        $return = parent::update($null_values);
        $this->setGroupReduction();
        $this->updateUnitRatio();

        Hook::exec('actionProductSave', ['id_product' => (int) $this->id, 'product' => $this]);
        Hook::exec('actionProductUpdate', ['id_product' => (int) $this->id, 'product' => $this]);
        if ($this->getType() == Product::PTYPE_VIRTUAL && $this->active && !Configuration::get('PS_VIRTUAL_PROD_FEATURE_ACTIVE')) {
            Configuration::updateGlobalValue('PS_VIRTUAL_PROD_FEATURE_ACTIVE', '1');
        }

        return $return;
    }

    /**
     * Unit price ratio is not edited anymore, the reference is handled via the unit_price field which is now saved
     * in the DB we kept unit_price_ratio in the DB for backward compatibility but shouldn't be written anymore so
     * it is automatically updated when product is saved
     */
    protected function updateUnitRatio(): void
    {
        $ecotaxEnabled = (bool) Configuration::get('PS_USE_ECOTAX');
        $this->fillUnitRatio($ecotaxEnabled);
        if ($ecotaxEnabled) {
            Db::getInstance()->execute(sprintf(
                'UPDATE %sproduct SET `unit_price_ratio` = IF (`unit_price` != 0, (`price` + `ecotax`) / `unit_price`, 0) WHERE `id_product` = %d;',
                _DB_PREFIX_,
                $this->id
            ));
            Db::getInstance()->execute(sprintf(
                'UPDATE %sproduct_shop SET `unit_price_ratio` = IF (`unit_price` != 0, (`price` + `ecotax`) / `unit_price`, 0) WHERE `id_product` = %d;',
                _DB_PREFIX_,
                $this->id
            ));
        } else {
            Db::getInstance()->execute(sprintf(
                'UPDATE %sproduct SET `unit_price_ratio` = IF (`unit_price` != 0, `price` / `unit_price`, 0) WHERE `id_product` = %d;',
                _DB_PREFIX_,
                $this->id
            ));
            Db::getInstance()->execute(sprintf(
                'UPDATE %sproduct_shop SET `unit_price_ratio` = IF (`unit_price` != 0, `price` / `unit_price`, 0) WHERE `id_product` = %d;',
                _DB_PREFIX_,
                $this->id
            ));
        }
    }

    /**
     * Unit price ratio is not edited anymore, the reference is handled via the unit_price field which is now saved
     * in the DB we kept unit_price_ratio in the DB for backward compatibility but but the DB value should not be used
     * any more since it is deprecated so the object field is computed automatically.
     */
    protected function fillUnitRatio(bool $ecotaxEnabled): void
    {
        // Update instance field
        $unitPrice = new DecimalNumber((string) ($this->unit_price ?: 0));
        $price = new DecimalNumber((string) ($this->price ?: 0));
        if ($ecotaxEnabled) {
            $price = $price->plus(new DecimalNumber((string) ($this->ecotax ?: 0)));
        }
        if ($unitPrice->isGreaterThanZero()) {
            $this->unit_price_ratio = (float) (string) $price->dividedBy($unitPrice);
        }
    }

    /**
     * Init computation of price display method (i.e. price should be including tax or not) for a customer.
     * If customer Id passed as null then this compute price display method with according of current group.
     * Otherwise a price display method will compute with according of a customer address (i.e. country).
     *
     * @see Group::getPriceDisplayMethod()
     *
     * @param int|null $id_customer Customer identifier
     */
    public static function initPricesComputation($id_customer = null)
    {
        if ((int) $id_customer > 0) {
            $customer = new Customer((int) $id_customer);
            if (!Validate::isLoadedObject($customer)) {
                die(Tools::displayError(sprintf('Customer with ID "%s" could not be loaded.', $id_customer)));
            }
            self::$_taxCalculationMethod = Group::getPriceDisplayMethod((int) $customer->id_default_group);
            $cur_cart = Context::getContext()->cart;
            $id_address = 0;
            if (Validate::isLoadedObject($cur_cart)) {
                $id_address = (int) $cur_cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
            }
            $address_infos = Address::getCountryAndState($id_address);

            if (
                self::$_taxCalculationMethod != PS_TAX_EXC
                && !empty($address_infos['vat_number'])
                && $address_infos['id_country'] != Configuration::get('VATNUMBER_COUNTRY')
                && Configuration::get('VATNUMBER_MANAGEMENT')
            ) {
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
     * @param int|null $id_customer Customer identifier
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
     * @param bool $way Up (1) or Down (0)
     * @param int $position
     *
     * @return bool Update result
     */
    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS(
            'SELECT cp.`id_product`, cp.`position`, cp.`id_category`
            FROM `' . _DB_PREFIX_ . 'category_product` cp
            WHERE cp.`id_category` = ' . (int) Tools::getValue('id_category', 1) . '
            ORDER BY cp.`position` ASC'
        )) {
            return false;
        }

        foreach ($res as $product) {
            if ((int) $product['id_product'] == (int) $this->id) {
                $moved_product = $product;
            }
        }

        if (!isset($moved_product)) {
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
     * @param int $id_category Category identifier
     * @param int $position
     *
     * @return bool
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
     * @param int $id_product Product ID
     * @param int $minimum_quantity Minimal quantity there should be in stock of the combination
     * @param bool $reset Force reload new values and not use cache
     *
     * @return int Attributes list
     */
    public static function getDefaultAttribute($id_product, $minimum_quantity = 0, $reset = false)
    {
        if (!Combination::isFeatureActive()) {
            return 0;
        }

        // If we should start fresh OR we did not want default combination for this product yet, we initialize cache
        if ($reset || !isset(static::$_combinations[$id_product])) {
            static::$_combinations[$id_product] = [];
        }

        // If we already have a value for this product and minimal quantity, we retrieve it from cache.
        if (isset(static::$_combinations[$id_product][$minimum_quantity])) {
            return static::$_combinations[$id_product][$minimum_quantity];
        }

        // First attempt - check if the product even has some attributes
        $sql = 'SELECT product_attribute_shop.id_product_attribute
                FROM ' . _DB_PREFIX_ . 'product_attribute pa
                ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                WHERE pa.id_product = ' . (int) $id_product;

        // If none are found, we exit right away
        $result_no_filter = (int) Db::getInstance()->getValue($sql);
        if (!$result_no_filter) {
            static::$_combinations[$id_product][$minimum_quantity] = 0;

            return 0;
        }

        // Try to check if the default combination matches our minimum_quantity quantity condition, if yes - win
        $sql = 'SELECT product_attribute_shop.id_product_attribute
                FROM ' . _DB_PREFIX_ . 'product_attribute pa
                ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                ' . ($minimum_quantity > 0 ? Product::sqlStock('pa', 'pa') : '') .
                ' WHERE product_attribute_shop.default_on = 1 '
                . ($minimum_quantity > 0 ? ' AND IFNULL(stock.quantity, 0) >= ' . (int) $minimum_quantity : '') .
                ' AND pa.id_product = ' . (int) $id_product;
        $result = (int) Db::getInstance()->getValue($sql);

        // If not, check if there is ANY combination matching minimum_quantity, if yes - not perfect, but works
        if (!$result) {
            $sql = 'SELECT product_attribute_shop.id_product_attribute
                    FROM ' . _DB_PREFIX_ . 'product_attribute pa
                    ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                    ' . ($minimum_quantity > 0 ? Product::sqlStock('pa', 'pa') : '') .
                    ' WHERE pa.id_product = ' . (int) $id_product
                    . ($minimum_quantity > 0 ? ' AND IFNULL(stock.quantity, 0) >= ' . (int) $minimum_quantity : '');

            $result = (int) Db::getInstance()->getValue($sql);
        }

        // If still nothing, we will return the default combination
        if (!$result) {
            $sql = 'SELECT product_attribute_shop.id_product_attribute
                    FROM ' . _DB_PREFIX_ . 'product_attribute pa
                    ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                    WHERE product_attribute_shop.`default_on` = 1
                    AND pa.id_product = ' . (int) $id_product;

            $result = (int) Db::getInstance()->getValue($sql);
        }

        // If for some reason (database inconsistency) we still did not find any combination, we use the fallback one
        if (!$result) {
            $result = $result_no_filter;
        }

        // Cache it for next time and return it
        static::$_combinations[$id_product][$minimum_quantity] = $result;

        return $result;
    }

    /**
     * @param string $available_date Date in mysql format Y-m-d
     *
     * @return bool
     */
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
     * @param int $id_product Product identifier
     * @param int|null $id_product_attribute Attribute identifier
     *
     * @return string|null
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

    /**
     * @param int $id_product Product identifier
     * @param bool $is_virtual
     */
    public static function updateIsVirtual($id_product, $is_virtual = true)
    {
        $isVirtual = (bool) $is_virtual;
        $updateData = [
            'is_virtual' => $isVirtual,
        ];

        // We only update the type if we are sure it is virtual
        if ($isVirtual) {
            $updateData['product_type'] = ProductType::TYPE_VIRTUAL;
        }

        Db::getInstance()->update('product', $updateData, 'id_product = ' . (int) $id_product);
    }

    /**
     * @see ObjectModel::resetStaticCache()
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
        static::$_combinations = [];
        static::$psEcotaxTaxRulesGroupId = null;
        Cache::clean('Product::*');
    }

    /**
     * {@inheritdoc}
     */
    public function validateField($field, $value, $id_lang = null, $skip = [], $human_errors = false)
    {
        if ($field == 'description_short') {
            // The legacy validation is basic, so the idea here is to adapt the allowed limit so that it takes into
            // account the difference between the raw text and the html text (since actually the limit is only about
            // the raw text) This is a bit ugly the real validation should only be performed by TinyMceMaxLengthValidator
            // but we have to deal with this for now.
            $limit = (int) Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT');
            if ($limit <= 0) {
                $limit = 800;
            }

            $replaceArray = [
                "\n",
                "\r",
                "\n\r",
                "\r\n",
            ];
            $str = $value ? str_replace($replaceArray, [''], strip_tags($value)) : '';
            $size_without_html = iconv_strlen($str);
            $size_with_html = Tools::strlen($value);
            $adaptedLimit = $limit + $size_with_html - $size_without_html;
            $this->def['fields']['description_short']['size'] = $adaptedLimit;
        }

        return parent::validateField($field, $value, $id_lang, $skip, $human_errors);
    }

    /**
     * {@inheritdoc}
     */
    public function delete()
    {
        $result = parent::delete();

        // Removes the product from StockAvailable, for the current shop
        $id_shop_list = count($this->id_shop_list) ? $this->id_shop_list : Shop::getContextListShopID();
        if (!empty($id_shop_list)) {
            foreach ($id_shop_list as $shopId) {
                StockAvailable::removeProductFromStockAvailable($this->id, null, $shopId);
            }
        } else {
            StockAvailable::removeProductFromStockAvailable($this->id);
        }

        // If there are still entries in product_shop, don't remove completely the product
        if ($this->hasMultishopEntries()) {
            $this->updateDefaultShop();

            return true;
        }

        Hook::exec('actionProductDelete', ['id_product' => (int) $this->id, 'product' => $this]);
        if (
            !$result ||
            !$this->deleteProductAttributes() ||
            !$this->deleteImages() ||
            !GroupReduction::deleteProductReduction($this->id) ||
            !$this->deleteCategories(false) ||
            !$this->deleteProductFeatures() ||
            !$this->deleteTags() ||
            !$this->deleteCartProducts() ||
            !$this->deleteAttachments(false) ||
            !$this->deleteCustomization() ||
            !SpecificPrice::deleteByProductId((int) $this->id) ||
            !$this->deletePack() ||
            !$this->deleteProductSale() ||
            !$this->deleteSearchIndexes() ||
            !$this->deleteAccessories() ||
            !$this->deleteCarrierRestrictions() ||
            !$this->deleteFromAccessories() ||
            !$this->deleteFromSupplier() ||
            !$this->deleteDownload() ||
            !$this->deleteFromCartRules()
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param array $products Product identifiers
     *
     * @return bool|int
     */
    public function deleteSelection(array $products)
    {
        $return = 1;

        // Deleting products can be quite long on a cheap server. Let's say 1.5 seconds by product (I've seen it!).
        $count = count($products);
        if ((int) (ini_get('max_execution_time')) < round($count * 1.5)) {
            ini_set('max_execution_time', (string) round($count * 1.5));
        }

        foreach ($products as $id_product) {
            $product = new Product((int) $id_product);
            $return &= $product->delete();
        }

        return $return;
    }

    /**
     * @return bool
     */
    public function deleteFromCartRules()
    {
        CartRule::cleanProductRuleIntegrity('products', $this->id);

        return true;
    }

    /**
     * @return bool
     */
    public function deleteFromSupplier()
    {
        return Db::getInstance()->delete('product_supplier', 'id_product = ' . (int) $this->id);
    }

    /**
     * addToCategories add this product to the category/ies if not exists.
     *
     * @param int|int[] $categories id_category or array of id_category
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
     * @param int[] $categories Categories list to index product into
     * @param bool $keeping_current_pos (deprecated, no more used)
     *
     * @return bool Update/insertion result
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
     * @param int $id_category Category identifier
     * @param bool $clean_positions
     *
     * @return bool
     */
    public function deleteCategory($id_category, $clean_positions = true)
    {
        $result = Db::getInstance()->executeS(
            'SELECT `id_category`, `position`
            FROM `' . _DB_PREFIX_ . 'category_product`
            WHERE `id_product` = ' . (int) $this->id . '
            AND id_category = ' . (int) $id_category
        );

        $return = Db::getInstance()->delete('category_product', 'id_product = ' . (int) $this->id . ' AND id_category = ' . (int) $id_category);
        if ($clean_positions === true) {
            foreach ($result as $row) {
                static::cleanPositions((int) $row['id_category'], (int) $row['position']);
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
     * @return bool Deletion result
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
                $return &= static::cleanPositions((int) $row['id_category'], (int) $row['position']);
            }
        }

        Cache::clean('Product::getProductCategories_' . (int) $this->id);

        return $return;
    }

    /**
     * Delete products tags entries.
     *
     * @return bool Deletion result
     */
    public function deleteTags()
    {
        return Tag::deleteTagsForProduct((int) $this->id);
    }

    /**
     * Delete product from cart.
     *
     * @return bool Deletion result
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
     * @param int $id_lang Language identifier
     * @param int $start Start number
     * @param int $limit Number of products to return
     * @param string $order_by Field for ordering
     * @param string $order_way Way for ordering (ASC or DESC)
     * @param int|false $id_category Category identifier
     * @param bool $only_active
     * @param Context|null $context
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
            die(Tools::displayError('Invalid sorting parameters provided.'));
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

    /**
     * @param int $id_lang Language identifier
     * @param Context|null $context
     *
     * @return array
     */
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

    /**
     * @return bool
     */
    public function isNew()
    {
        $nbDaysNewProduct = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        if (!Validate::isUnsignedInt($nbDaysNewProduct)) {
            $nbDaysNewProduct = 20;
        }

        $query = 'SELECT COUNT(p.id_product)
            FROM `' . _DB_PREFIX_ . 'product` p
            ' . Shop::addSqlAssociation('product', 'p') . '
            WHERE p.id_product = ' . (int) $this->id . '
            AND DATEDIFF("' . date('Y-m-d') . ' 00:00:00", product_shop.`date_add`) < ' . $nbDaysNewProduct;

        return (bool) Db::getInstance()->getValue($query, false);
    }

    /**
     * @param int[] $attributes_list Attribute identifier(s)
     * @param int|false $current_product_attribute Attribute identifier
     * @param Context|null $context
     * @param bool $all_shops
     * @param bool $return_id
     *
     * @return bool|int|string Attribute exist or Attribute identifier if return_id = true
     */
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
        if (empty($result)) {
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
     * @deprecated since 1.5.0
     * @see StockAvailable if you want to manage available quantities for sale on your shop(s)
     * @see ProductSupplier for manage supplier reference(s)
     *
     * @param float $price Additional price
     * @param float $weight Additional weight
     * @param float $unit_impact
     * @param float $ecotax Additional ecotax
     * @param int $quantity
     * @param int[] $id_images Image ids
     * @param string $reference Reference
     * @param int $id_supplier Supplier identifier
     * @param string $ean13
     * @param bool $default Is default attribute for product
     * @param string $location
     * @param string $upc
     * @param int $minimal_quantity
     * @param string $isbn
     * @param int|null $low_stock_threshold Low stock for mail alert
     * @param bool $low_stock_alert Low stock mail alert activated
     * @param string|null $mpn
     *
     * @return int|false Attribute identifier if success, false if it fail
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

    /**
     * @param array $combinations
     * @param array $attributes
     * @param bool $resetExistingCombination
     *
     * @return bool
     */
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
                $obj->available_now = '';
                $obj->available_later = '';
            }

            foreach ($combination as $field => $value) {
                $obj->$field = $value;
            }

            $obj->default_on = false;
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

    /**
     * @param array<int> $combinations
     * @param int $langId
     *
     * @return array
     */
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
     * @param float $wholesale_price
     * @param float $price Additional price
     * @param float $weight Additional weight
     * @param float $unit_impact
     * @param float $ecotax Additional ecotax
     * @param int $quantity deprecated
     * @param int[] $id_images Image ids
     * @param string $reference Reference
     * @param int $id_supplier Supplier identifier
     * @param string $ean13
     * @param bool $default Is default attribute for product
     * @param string|null $location
     * @param string|null $upc
     * @param int $minimal_quantity
     * @param array $id_shop_list
     * @param string|null $available_date Date in mysql format Y-m-d
     * @param string $isbn
     * @param int|null $low_stock_threshold Low stock for mail alert
     * @param bool $low_stock_alert Low stock mail alert activated
     * @param string|null $mpn
     * @param string[]|string $available_now Combination available now labels
     * @param string[]|string $available_later Combination available later labels
     *
     * @return int|false Attribute identifier if success, false if it fail
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
        $mpn = null,
        $available_now = [],
        $available_later = []
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
            $mpn,
            $available_now,
            $available_later
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
     * Delete all default attributes for product.
     *
     * @return bool
     */
    public function deleteDefaultAttributes()
    {
        return ObjectModel::updateMultishopTable('Combination', [
            'default_on' => null,
        ], 'a.`id_product` = ' . (int) $this->id);
    }

    /**
     * @param int $id_product_attribute Attribute identifier
     *
     * @return bool
     */
    public function setDefaultAttribute($id_product_attribute)
    {
        // We only update the type when we know it has combinations
        if (!empty($id_product_attribute)) {
            $this->product_type = ProductType::TYPE_COMBINATIONS;
        }

        $result = ObjectModel::updateMultishopTable('Combination', [
            'default_on' => 1,
        ], 'a.`id_product` = ' . (int) $this->id . ' AND a.`id_product_attribute` = ' . (int) $id_product_attribute);

        $result = $result && ObjectModel::updateMultishopTable('product', [
            'cache_default_attribute' => (int) $id_product_attribute,
            'product_type' => $this->product_type,
        ], 'a.`id_product` = ' . (int) $this->id);

        $this->cache_default_attribute = (int) $id_product_attribute;

        return $result;
    }

    /**
     * @param int $id_product Product identifier
     *
     * @return int|false Default Attribute identifier if success, false if it false
     */
    public static function updateDefaultAttribute($id_product)
    {
        $id_default_attribute = (int) Product::getDefaultAttribute($id_product, 0, true);

        $result = Db::getInstance()->update('product_shop', [
            'cache_default_attribute' => $id_default_attribute,
        ], 'id_product = ' . (int) $id_product . Shop::addSqlRestriction());

        // We only update the type when we know it has combinations
        $updateData = [
            'cache_default_attribute' => $id_default_attribute,
        ];
        if (!empty($id_default_attribute)) {
            $updateData['product_type'] = ProductType::TYPE_COMBINATIONS;
        }
        $result &= Db::getInstance()->update('product', $updateData, 'id_product = ' . (int) $id_product);

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
     *
     * @param int $id_product_attribute Attribute identifier
     * @param float $wholesale_price
     * @param float $price Additional price
     * @param float $weight Additional weight
     * @param float $unit
     * @param float $ecotax Additional ecotax
     * @param int[] $id_images Image ids
     * @param string $reference
     * @param int $id_supplier Supplier identifier
     * @param string $ean13
     * @param bool $default Is default attribute for product
     * @param string $location
     * @param string $upc
     * @param int $minimal_quantity
     * @param string $available_date Date in mysql format Y-m-d
     * @param string $isbn
     * @param int|null $low_stock_threshold Low stock for mail alert
     * @param bool $low_stock_alert Low stock mail alert activated
     * @param string|null $mpn
     * @param string[]|string|null $available_now Combination available now labels
     * @param string[]|string|null $available_later Combination available later labels
     *
     * @return bool
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
        $mpn = null,
        $available_now = null,
        $available_later = null
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
            $mpn = null,
            $available_now,
            $available_later
        );
        $this->addSupplierReference($id_supplier, $id_product_attribute);

        return $return;
    }

    /**
     * Sets or updates Supplier Reference.
     *
     * @param int $id_supplier Supplier identifier
     * @param int $id_product_attribute Attribute identifier
     * @param string|null $supplier_reference
     * @param float|null $price
     * @param int|null $id_currency Currency identifier
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
     * @param float $unit Additional unit price
     * @param float $ecotax Additional ecotax
     * @param int[] $id_images Image identifiers
     * @param string $reference Reference
     * @param string $ean13 Ean-13 barcode
     * @param bool $default Is default attribute for product
     * @param string|null $location
     * @param string $upc Upc barcode
     * @param int|null $minimal_quantity Minimal quantity
     * @param string|null $available_date Date in mysql format Y-m-d
     * @param bool $update_all_fields
     * @param int[] $id_shop_list
     * @param string $isbn ISBN reference
     * @param int|null $low_stock_threshold Low stock for mail alert
     * @param bool $low_stock_alert Low stock mail alert activated
     * @param string $mpn MPN
     * @param string[]|string|null $available_now Combination available now labels
     * @param string[]|string|null $available_later Combination available later labels
     *
     * @return bool Update result
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
        $mpn = null,
        $available_now = null,
        $available_later = null
    ) {
        $combination = new Combination($id_product_attribute);

        if (!$update_all_fields) {
            $fieldsToUpdate = [
                'price' => null !== $price,
                'wholesale_price' => null !== $wholesale_price,
                'ecotax' => null !== $ecotax,
                'weight' => null !== $weight,
                'unit_price_impact' => null !== $unit,
                'default_on' => null !== $default,
                'minimal_quantity' => null !== $minimal_quantity,
                'reference' => null !== $reference,
                'ean13' => null !== $ean13,
                'upc' => null !== $upc,
                'isbn' => null !== $isbn,
                'mpn' => null !== $mpn,
                'available_date' => null !== $available_date,
                'low_stock_threshold' => null !== $low_stock_threshold,
                'low_stock_alert' => null !== $low_stock_alert,
                'id_shop_list' => !empty($id_shop_list),
                'available_now' => is_string($available_now),
                'available_later' => is_string($available_later),
            ];
            // Labels can be passed into this function both as array and string, as does the object model itself.
            // If these values are passed as strings, they will be updated in all languages of the object.
            if (is_array($available_now)) {
                foreach ($available_now as $id_lang => $value) {
                    $fieldsToUpdate['available_now'][$id_lang] = true;
                }
            }
            if (is_array($available_later)) {
                foreach ($available_later as $id_lang => $value) {
                    $fieldsToUpdate['available_later'][$id_lang] = true;
                }
            }

            $combination->setFieldsToUpdate($fieldsToUpdate);
        }

        $price = (float) str_replace(',', '.', (string) $price);
        $weight = (float) str_replace(',', '.', (string) $weight);

        $combination->price = $price;
        $combination->wholesale_price = (float) $wholesale_price;
        $combination->ecotax = (float) $ecotax;
        $combination->weight = $weight;
        $combination->unit_price_impact = (float) $unit;
        $combination->reference = pSQL($reference);
        $combination->ean13 = pSQL($ean13);
        $combination->isbn = pSQL($isbn);
        $combination->upc = pSQL($upc);
        $combination->mpn = pSQL($mpn);
        $combination->default_on = (bool) $default;
        $combination->minimal_quantity = (int) $minimal_quantity;
        $combination->low_stock_threshold = empty($low_stock_threshold) && '0' != $low_stock_threshold ? null : (int) $low_stock_threshold;
        $combination->low_stock_alert = !empty($low_stock_alert);
        $combination->available_date = $available_date ? pSQL($available_date) : '0000-00-00';
        $combination->available_now = $available_now;
        $combination->available_later = $available_later;

        if (!empty($id_shop_list)) {
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

        Hook::exec('actionProductAttributeUpdate', ['id_product_attribute' => (int) $id_product_attribute]);

        return true;
    }

    /**
     * Add a product attribute.
     *
     * @since 1.5.0.1
     *
     * @param float $price Additional price
     * @param float $weight Additional weight
     * @param float $unit_impact Additional unit price
     * @param float $ecotax Additional ecotax
     * @param int[] $id_images Image ids
     * @param string $reference Reference
     * @param string $ean13 Ean-13 barcode
     * @param bool $default Is default attribute for product
     * @param string $location Location
     * @param string|null $upc
     * @param int $minimal_quantity Minimal quantity to add to cart
     * @param int[] $id_shop_list
     * @param string|null $available_date Date in mysql format Y-m-d
     * @param int $quantity
     * @param string $isbn ISBN reference
     * @param int|null $low_stock_threshold Low stock for mail alert
     * @param bool $low_stock_alert Low stock mail alert activated
     * @param string|null $mpn
     * @param string[]|string $available_now Combination available now labels
     * @param string[]|string $available_later Combination available later labels
     *
     * @return int|false|void Attribute identifier if success, false if failed to add Combination, void if Product identifier not set
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
        $mpn = null,
        $available_now = [],
        $available_later = []
    ) {
        if (!$this->id) {
            return;
        }

        $price = (float) str_replace(',', '.', (string) $price);
        $weight = (float) str_replace(',', '.', (string) $weight);

        $combination = new Combination();
        $combination->id_product = (int) $this->id;
        $combination->price = $price;
        $combination->ecotax = (float) $ecotax;
        $combination->weight = (float) $weight;
        $combination->unit_price_impact = (float) $unit_impact;
        $combination->reference = pSQL($reference);
        $combination->ean13 = pSQL($ean13);
        $combination->isbn = pSQL($isbn);
        $combination->upc = pSQL($upc);
        $combination->mpn = pSQL($mpn);
        $combination->default_on = (bool) $default;
        $combination->minimal_quantity = (int) $minimal_quantity;
        $combination->low_stock_threshold = empty($low_stock_threshold) && '0' != $low_stock_threshold ? null : (int) $low_stock_threshold;
        $combination->low_stock_alert = !empty($low_stock_alert);
        $combination->available_date = $available_date;
        $combination->available_now = $available_now;
        $combination->available_later = $available_later;

        if (count($id_shop_list)) {
            $combination->id_shop_list = array_unique($id_shop_list);
        }

        $combination->add();

        if (!$combination->id) {
            return false;
        }

        $total_quantity = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT SUM(quantity) as quantity
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
        $this->product_type = ProductType::TYPE_COMBINATIONS;

        if (!empty($id_images)) {
            $combination->setImages($id_images);
        }

        return (int) $combination->id;
    }

    /**
     * Delete product attributes.
     *
     * @return bool Deletion result
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

        return $result;
    }

    /**
     * Delete product features.
     *
     * @return bool Deletion result
     */
    public function deleteProductFeatures()
    {
        SpecificPriceRule::applyAllRules([(int) $this->id]);

        return $this->deleteFeatures();
    }

    /**
     * @param int $id_product Product identifier
     *
     * @return bool
     */
    public static function updateCacheAttachment($id_product)
    {
        $value = (bool) Db::getInstance()->getValue(
            'SELECT id_attachment
            FROM ' . _DB_PREFIX_ . 'product_attachment
            WHERE id_product=' . (int) $id_product
        );

        return Db::getInstance()->update(
            'product',
            ['cache_has_attachments' => (int) $value],
            'id_product = ' . (int) $id_product
        );
    }

    /**
     * Delete product attachments.
     *
     * @param bool $update_attachment_cache If set to true attachment cache will be updated
     *
     * @return bool Deletion result
     */
    public function deleteAttachments($update_attachment_cache = true)
    {
        $res = Db::getInstance()->execute(
            '
            DELETE FROM `' . _DB_PREFIX_ . 'product_attachment`
            WHERE `id_product` = ' . (int) $this->id
        );

        if ((bool) $update_attachment_cache === true) {
            Product::updateCacheAttachment((int) $this->id);
        }

        return $res;
    }

    /**
     * Delete product customizations.
     *
     * @return bool Deletion result
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
     * @return bool Deletion result
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
     * @return bool Deletion result
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
     * @return bool Deletion result
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
     * Delete a product attributes combination.
     *
     * @param int $id_product_attribute Attribute identifier
     *
     * @return bool Deletion result
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
     *
     * @return bool
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
     * @param int $id_lang Language identifier
     * @param string $attribute_value_separator
     * @param string $attribute_separator
     *
     * @return bool|array Product attributes combinations
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
                GROUP BY pa.`id_product_attribute`
                ORDER BY pa.`id_product_attribute`');

        if (!$combinations) {
            return false;
        }

        $combinations = array_column($combinations, null, 'id_product_attribute');

        $combinationIds = array_keys($combinations);

        $lang = Db::getInstance()->executeS('SELECT pac.id_product_attribute, GROUP_CONCAT(agl.`name`, \'' . pSQL($attribute_value_separator) . '\',al.`name` ORDER BY agl.`id_attribute_group` SEPARATOR \'' . pSQL($attribute_separator) . '\') as attribute_designation
                FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $id_lang . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $id_lang . ')
                WHERE pac.id_product_attribute IN (' . implode(',', $combinationIds) . ')
                GROUP BY pac.id_product_attribute
                ORDER BY pac.id_product_attribute');

        foreach ($lang as $row) {
            $combinations[$row['id_product_attribute']]['attribute_designation'] = $row['attribute_designation'];
        }

        $computingPrecision = Context::getContext()->getComputingPrecision();
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

            $ecotax = (float) $combinations[$key]['ecotax'] ?: 0;
            $combinations[$key]['ecotax_tax_excluded'] = $ecotax;
            $combinations[$key]['ecotax_tax_included'] = Tools::ps_round($ecotax * (1 + Tax::getProductEcotaxRate() / 100), $computingPrecision);
        }

        return $combinations;
    }

    /**
     * Get all available product attributes combinations.
     *
     * @param int|null $id_lang Language identifier
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

        $sql = 'SELECT pa.*, product_attribute_shop.*, ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, al.`name` AS attribute_name, ' .
            'a.`id_attribute`, stock.location ' .
            'FROM `' . _DB_PREFIX_ . 'product_attribute` pa ' .
            Shop::addSqlAssociation('product_attribute', 'pa') . ' ' .
            'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute` ' .
            'LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute` ' .
            'LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group` ' .
            'LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $id_lang . ') ' .
            'LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $id_lang . ') ' .
            'LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` stock ON (stock.id_product = pa.id_product AND stock.id_product_attribute = IFNULL(pa.`id_product_attribute`, 0)) ' .
            'WHERE pa.`id_product` = ' . (int) $this->id . ' ' .
            'GROUP BY pa.`id_product_attribute`' . ($groupByIdAttributeGroup ? ', ag.`id_attribute_group` ' : '') .
            'ORDER BY pa.`id_product_attribute`';

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
     * @param int $id_product_attribute Attribute identifier
     * @param int $id_lang Language identifier
     * @param bool $groupByIdAttributeGroup
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

        $computingPrecision = Context::getContext()->getComputingPrecision();
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

            $ecotax = (float) $res[$key]['ecotax'] ?: 0;
            $res[$key]['ecotax_tax_excluded'] = $ecotax;
            $res[$key]['ecotax_tax_included'] = Tools::ps_round($ecotax * (1 + Tax::getProductEcotaxRate() / 100), $computingPrecision);
        }

        return $res;
    }

    /**
     * @param int $id_lang Language identifier
     *
     * @return array|false
     */
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

    /**
     * @param int $id_product_attribute Attribute identifier
     * @param int $id_lang Language identifier
     *
     * @return array|false
     */
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

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT COUNT(*)
            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
            WHERE pa.`id_product` = ' . (int) $this->id
        );
    }

    /**
     * Get new products.
     *
     * @param int $id_lang Language identifier
     * @param int $page_number Start from
     * @param int $nb_products Number of products to return
     * @param bool $count
     * @param string|null $order_by
     * @param string|null $order_way
     * @param Context|null $context
     *
     * @return array|int|false New products, total of product if $count is true, false if it fail
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
            die(Tools::displayError('Invalid sorting parameters provided.'));
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
                    AND DATEDIFF(product_shop.`date_add`, DATE_SUB("' . $now . '", INTERVAL ' . $nb_days_new_product . ' DAY)) > 0
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
        $sql->where('DATEDIFF(product_shop.`date_add`,
            DATE_SUB(
                "' . $now . '",
                INTERVAL ' . $nb_days_new_product . ' DAY
            )
        ) > 0');
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql->where('EXISTS(SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp
            JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '=' . (int) Group::getCurrent()->id) . ')
            WHERE cp.`id_product` = p.`id_product`)');
        }

        if ($order_by !== 'price') {
            $sql->orderBy((isset($order_by_prefix) ? pSQL($order_by_prefix) . '.' : '') . '`' . pSQL($order_by) . '` ' . pSQL($order_way));
            $sql->limit($nb_products, (int) (($page_number - 1) * $nb_products));
        }

        if (Combination::isFeatureActive()) {
            $sql->select('product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute');
            $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', 'p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int) $context->shop->id);
        }
        $sql->join(Product::sqlStock('p', 0));

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if (!$result) {
            return false;
        }

        if ($order_by === 'price') {
            Tools::orderbyPrice($result, $order_way);
            $result = array_slice($result, (int) (($page_number - 1) * $nb_products), (int) $nb_products);
        }
        $products_ids = [];
        foreach ($result as $row) {
            $products_ids[] = $row['id_product'];
        }
        // Thus you can avoid one query per product, because there will be only one query for all the products of the cart
        Product::cacheFrontFeatures($products_ids, $id_lang);

        return Product::getProductsProperties((int) $id_lang, $result);
    }

    /**
     * @param string $beginning Date in mysql format Y-m-d
     * @param string $ending Date in mysql format Y-m-d
     * @param Context|null $context
     * @param bool $with_combination
     *
     * @return array
     */
    protected static function _getProductIdByDate($beginning, $ending, Context $context = null, $with_combination = false)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $id_address = $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
        $ids = Address::getCountryAndState($id_address);
        $id_country = isset($ids['id_country']) ? (int) $ids['id_country'] : (int) Configuration::get('PS_COUNTRY_DEFAULT');

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
     * @param int $id_lang Language identifier
     * @param string|false $beginning Date in mysql format Y-m-d
     * @param string|false $ending Date in mysql format Y-m-d
     * @param Context|null $context
     *
     * @return array|false Special
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
     * @param int $id_lang Language identifier
     * @param int $page_number Start from
     * @param int $nb_products Number of products to return
     * @param bool $count Only in order to get total number
     * @param string|null $order_by
     * @param string|null $order_way
     * @param string|false $beginning Date in mysql format Y-m-d
     * @param string|false $ending Date in mysql format Y-m-d
     * @param Context|null $context
     *
     * @return array|int|false
     */
    public static function getPricesDrop(
        $id_lang,
        $page_number = 0,
        $nb_products = 10,
        bool $count = false,
        $order_by = null,
        $order_way = null,
        $beginning = false,
        $ending = false,
        Context $context = null
    ) {
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
            die(Tools::displayError('Invalid sorting parameters provided.'));
        }
        $current_date = date('Y-m-d H:i:00');
        $ids_product = Product::_getProductIdByDate((!$beginning ? $current_date : $beginning), (!$ending ? $current_date : $ending), $context);

        $tab_id_product = [];

        foreach ($ids_product as $product) {
            if (is_array($product)) {
                $productId = (int) $product['id_product'];
            } else {
                $productId = (int) $product;
            }
            if (!in_array($productId, $tab_id_product)) {
                $tab_id_product[] = $productId;
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
            $count = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT COUNT(DISTINCT p.`id_product`)
            FROM `' . _DB_PREFIX_ . 'product` p
            ' . Shop::addSqlAssociation('product', 'p') . '
            WHERE product_shop.`active` = 1
            AND product_shop.`show_price` = 1
            ' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
            ' . ((!$beginning && !$ending) ? 'AND p.`id_product` IN(' . ((is_array($tab_id_product) && count($tab_id_product)) ? implode(', ', $tab_id_product) : 0) . ')' : '') . '
            ' . $sql_groups);

            return $count === false ? $count : (int) $count;
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
        ' . $sql_groups;

        if ($order_by != 'price') {
            $sql .= '
				ORDER BY ' . (isset($order_by_prefix) ? pSQL($order_by_prefix) . '.' : '') . pSQL($order_by) . ' ' . pSQL($order_way) . '
				LIMIT ' . (int) (($page_number - 1) * $nb_products) . ', ' . (int) $nb_products;
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if (!$result) {
            return false;
        }

        if ($order_by === 'price') {
            Tools::orderbyPrice($result, $order_way);
            $result = array_slice($result, (int) (($page_number - 1) * $nb_products), (int) $nb_products);
        }

        return Product::getProductsProperties($id_lang, $result);
    }

    /**
     * getProductCategories return an array of categories which this product belongs to.
     *
     * @param int|string $id_product Product identifier
     *
     * @return array Category identifiers
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

    /**
     * @param int|string $id_product Product identifier
     * @param int|null $id_lang Language identifier
     *
     * @return array
     */
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
     *
     * @return array
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
     *
     * @param int[] $carrier_list
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
     * @param int $id_lang Language identifier
     * @param Context|null $context
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
     * @param int $id_product Product identifier
     * @param Context|null $context
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
     * @param int $id_product Product identifier
     * @param bool $usetax With taxes or not (optional)
     * @param int|null $id_product_attribute Attribute identifier (optional).
     *                                       If set to false, do not apply the combination price impact.
     *                                       NULL does apply the default combination price impact
     * @param int $decimals Number of decimals (optional)
     * @param int|null $divisor Useful when paying many time without fees (optional)
     * @param bool $only_reduc Returns only the reduction amount
     * @param bool $usereduc Set if the returned amount will include reduction
     * @param int $quantity Required for quantity discount application (default value: 1)
     * @param bool $force_associated_tax DEPRECATED - NOT USED Force to apply the associated tax.
     *                                   Only works when the parameter $usetax is true
     * @param int|null $id_customer Customer identifier (for customer group reduction)
     * @param int|null $id_cart Cart identifier Required when the cookie is not accessible
     *                          (e.g., inside a payment module, a cron task...)
     * @param int|null $id_address Address identifier of Customer. Required for price (tax included)
     *                             calculation regarding the guest localization
     * @param array|null $specific_price_output If a specific price applies regarding the previous parameters,
     *                                          this variable is filled with the corresponding SpecificPrice data
     * @param bool $with_ecotax insert ecotax in price output
     * @param bool $use_group_reduction
     * @param Context $context
     * @param bool $use_customer_price
     * @param int|null $id_customization Customization identifier
     *
     * @return float|null Product price
     */
    public static function getPriceStatic(
        $id_product,
        bool $usetax = true,
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

        if (!Validate::isUnsignedId($id_product)) {
            die(Tools::displayError('Product ID is invalid.'));
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
                die(Tools::displayError('If no employee is assigned in the context, cart ID must be provided to this method.'));
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

        $id_currency = Validate::isLoadedObject($context->currency) ? (int) $context->currency->id : Currency::getDefaultCurrencyId();

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

        if (
            $usetax != false
            && !empty($address->vat_number)
            && $address->id_country != Configuration::get('VATNUMBER_COUNTRY')
            && Configuration::get('VATNUMBER_MANAGEMENT')
        ) {
            $usetax = false;
        }

        if (null === $id_customer && Validate::isLoadedObject($context->customer)) {
            $id_customer = $context->customer->id;
        }

        return Product::priceCalculation(
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
    }

    /**
     * Price calculation / Get product price.
     *
     * @param int $id_shop Shop identifier
     * @param int $id_product Product identifier
     * @param int|null $id_product_attribute Attribute identifier
     * @param int $id_country Country identifier
     * @param int $id_state State identifier
     * @param string $zipcode
     * @param int $id_currency Currency identifier
     * @param int $id_group Group identifier
     * @param int $quantity Quantity Required for Specific prices : quantity discount application
     * @param bool $use_tax with (1) or without (0) tax
     * @param int $decimals Number of decimals returned
     * @param bool $only_reduc Returns only the reduction amount
     * @param bool $use_reduc Set if the returned amount will include reduction
     * @param bool $with_ecotax insert ecotax in price output
     * @param array|null $specific_price If a specific price applies regarding the previous parameters,
     *                                   this variable is filled with the corresponding SpecificPrice data
     * @param bool $use_group_reduction
     * @param int $id_customer Customer identifier
     * @param bool $use_customer_price
     * @param int $id_cart Cart identifier
     * @param int $real_quantity
     * @param int $id_customization Customization identifier
     *
     * @return float|null Product price, void if not found in cache $_pricesLevel2
     */
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
        // We need to check the cache for this price AND attribute, if absent the whole product cache needs update
        // This can happen if the cache was filled before the combination was created for example
        if (!isset(self::$_pricesLevel2[$cache_id_2][(int) $id_product_attribute])) {
            $sql = new DbQuery();
            $sql->select('product_shop.`price`, product_shop.`ecotax`');
            $sql->from('product', 'p');
            $sql->innerJoin('product_shop', 'product_shop', '(product_shop.id_product=p.id_product AND product_shop.id_shop = ' . (int) $id_shop . ')');
            $sql->where('p.`id_product` = ' . (int) $id_product);
            if (Combination::isFeatureActive()) {
                $sql->select('IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute, product_attribute_shop.`price` AS attribute_price, product_attribute_shop.default_on, product_attribute_shop.`ecotax` AS attribute_ecotax');
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
                        'attribute_price' => $row['attribute_price'] ?? null,
                        'attribute_ecotax' => $row['attribute_ecotax'] ?? null,
                    ];
                    self::$_pricesLevel2[$cache_id_2][(int) $row['id_product_attribute']] = $array_tmp;

                    if (isset($row['default_on']) && $row['default_on'] == 1) {
                        self::$_pricesLevel2[$cache_id_2][0] = $array_tmp;
                    }
                }
            }
        }

        if (!isset(self::$_pricesLevel2[$cache_id_2][(int) $id_product_attribute])) {
            return null;
        }

        $result = self::$_pricesLevel2[$cache_id_2][(int) $id_product_attribute];

        if (!$specific_price || $specific_price['price'] < 0) {
            $price = (float) $result['price'];
        } else {
            $price = (float) $specific_price['price'];
        }
        // convert only if the specific price currency is different from the default currency
        if (
            !$specific_price ||
            !(
                $specific_price['price'] >= 0 &&
                $specific_price['id_currency'] &&
                $id_currency === $specific_price['id_currency']
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
                if (self::$psEcotaxTaxRulesGroupId === null) {
                    self::$psEcotaxTaxRulesGroupId = (int) Configuration::get('PS_ECOTAX_TAX_RULES_GROUP_ID');
                }
                // reinit the tax manager for ecotax handling
                $tax_manager = TaxManagerFactory::getManager(
                    $address,
                    self::$psEcotaxTaxRulesGroupId
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

        Hook::exec('actionProductPriceCalculation', [
            'id_shop' => $id_shop,
            'id_product' => $id_product,
            'id_product_attribute' => $id_product_attribute,
            'id_customization' => $id_customization,
            'id_country' => $id_country,
            'id_state' => $id_state,
            'zip_code' => $zipcode,
            'id_currency' => $id_currency,
            'id_group' => $id_group,
            'id_cart' => $id_cart,
            'id_customer' => $id_customer,
            'use_customer_price' => $use_customer_price,
            'quantity' => $quantity,
            'real_quantity' => $real_quantity,
            'use_tax' => $use_tax,
            'decimals' => $decimals,
            'only_reduc' => $only_reduc,
            'use_reduc' => $use_reduc,
            'with_ecotax' => $with_ecotax,
            'specific_price' => &$specific_price,
            'use_group_reduction' => $use_group_reduction,
            'address' => $address,
            'context' => $context,
            'specific_price_reduction' => &$specific_price_reduction,
            'price' => &$price,
        ]);
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

    /**
     * @param int $orderId
     * @param int $productId
     * @param int $combinationId
     * @param bool $withTaxes
     * @param bool $useReduction
     * @param bool $withEcoTax
     *
     * @return float|null
     *
     * @throws PrestaShopDatabaseException
     */
    public static function getPriceFromOrder(
        int $orderId,
        int $productId,
        int $combinationId,
        bool $withTaxes,
        bool $useReduction,
        bool $withEcoTax
    ): ?float {
        $sql = new DbQuery();
        $sql->select('od.*, t.rate AS tax_rate');
        $sql->from('order_detail', 'od');
        $sql->where('od.`id_order` = ' . $orderId);
        $sql->where('od.`product_id` = ' . $productId);
        if (Combination::isFeatureActive()) {
            $sql->where('od.`product_attribute_id` = ' . $combinationId);
        }
        $sql->leftJoin('order_detail_tax', 'odt', 'odt.id_order_detail = od.id_order_detail');
        $sql->leftJoin('tax', 't', 't.id_tax = odt.id_tax');
        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if (!is_array($res) || empty($res)) {
            return null;
        }

        $orderDetail = $res[0];
        if ($useReduction) {
            // If we want price with reduction it is already the one stored in OrderDetail
            $price = $withTaxes ? $orderDetail['unit_price_tax_incl'] : $orderDetail['unit_price_tax_excl'];
        } else {
            // Without reduction we use the original product price to compute the original price
            $tax_rate = $withTaxes ? (1 + ($orderDetail['tax_rate'] / 100)) : 1;
            $price = $orderDetail['original_product_price'] * $tax_rate;
        }
        if (!$withEcoTax) {
            // Remove the ecotax as the order detail contains already ecotax in the price
            $price -= ($withTaxes ? $orderDetail['ecotax'] * (1 + $orderDetail['ecotax_tax_rate']) : $orderDetail['ecotax']);
        }

        return $price;
    }

    /**
     * @param float $price
     * @param Currency|false $currency
     * @param Context|null $context
     *
     * @return string
     */
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

    /**
     * @param int $id_product Product identifier
     * @param int $quantity
     * @param Context|null $context
     *
     * @return bool
     */
    public static function isDiscounted($id_product, $quantity = 1, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $id_group = $context->customer->id_default_group;
        $cart_quantity = !$context->cart ? 0 : Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT SUM(`quantity`)
            FROM `' . _DB_PREFIX_ . 'cart_product`
            WHERE `id_product` = ' . (int) $id_product . ' AND `id_cart` = ' . (int) $context->cart->id
        );
        $quantity = $cart_quantity ? $cart_quantity : $quantity;

        $id_currency = (int) $context->currency->id;
        $ids = Address::getCountryAndState((int) $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
        $id_country = (int) ($ids['id_country'] ?? Configuration::get('PS_COUNTRY_DEFAULT'));

        return (bool) SpecificPrice::getSpecificPrice((int) $id_product, $context->shop->id, $id_currency, $id_country, $id_group, $quantity, null, 0, 0, $quantity);
    }

    /**
     * Get product price
     * Same as static function getPriceStatic, no need to specify product id.
     *
     * @param bool $tax With taxes or not (optional)
     * @param int|null $id_product_attribute Attribute identifier
     * @param int $decimals Number of decimals
     * @param int|null $divisor Util when paying many time without fees
     * @param bool $only_reduc
     * @param bool $usereduc
     * @param int $quantity
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

    /**
     * @param bool $tax With taxes or not (optional)
     * @param int|null $id_product_attribute Attribute identifier
     * @param int $decimals Number of decimals
     * @param null $divisor Util when paying many time without fees
     * @param bool $only_reduc
     * @param bool $usereduc
     * @param int $quantity
     *
     * @return float
     */
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

    /**
     * @return int
     */
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

    /**
     * @return int
     */
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

    /**
     * @param bool $notax With taxes or not (optional)
     * @param int|null $id_product_attribute Attribute identifier
     * @param int $decimals Number of decimals
     *
     * @return float
     */
    public function getPriceWithoutReduct($notax = false, $id_product_attribute = null, $decimals = 6)
    {
        return Product::getPriceStatic((int) $this->id, !$notax, $id_product_attribute, $decimals, null, false, false);
    }

    /**
     * Display price with right format and currency.
     *
     * @param array $params Params
     * @param object $smarty Smarty object (DEPRECATED)
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
     * @param object $smarty Smarty object (DEPRECATED)
     *
     * @return string Ambigous <string, mixed, Ambigous <number, string>>
     */
    public static function convertPriceWithCurrency($params, &$smarty)
    {
        $currency = $params['currency'];
        $currency = is_object($currency) ? $currency->iso_code : Currency::getIsoCodeById((int) $currency);

        return Tools::getContextLocale(Context::getContext())->formatPrice($params['price'], $currency);
    }

    /**
     * @param array $params
     * @param object $smarty Smarty object (DEPRECATED)
     *
     * @return string
     */
    public static function displayWtPrice($params, &$smarty)
    {
        return Tools::getContextLocale(Context::getContext())->formatPrice($params['p'], Context::getContext()->currency->iso_code);
    }

    /**
     * Display WT price with currency.
     *
     * @param array $params
     * @param object $smarty Smarty object (DEPRECATED)
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
     * @param int $idProduct Product identifier
     * @param int|null $idProductAttribute Product attribute id (optional)
     * @param bool|null $cacheIsPack
     * @param CartCore|null $cart
     * @param int|bool|null $idCustomization Product customization id (optional)
     *
     * @return int Available quantities
     */
    public static function getQuantity(
        $idProduct,
        $idProductAttribute = null,
        $cacheIsPack = null,
        CartCore $cart = null,
        $idCustomization = null
    ) {
        // pack usecase: Pack::getQuantity() returns the pack quantity after cart quantities have been removed from stock
        if (Pack::isPack((int) $idProduct)) {
            return Pack::getQuantity($idProduct, $idProductAttribute, $cacheIsPack, $cart, $idCustomization);
        }
        $availableQuantity = StockAvailable::getQuantityAvailableByProduct($idProduct, $idProductAttribute);
        $nbProductInCart = 0;

        // we don't substract products in cart if the cart is already attached to an order, since stock quantity
        // has already been updated, this is only useful when the order has not yet been created
        if ($cart && empty(Order::getByCartId($cart->id))) {
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
     * @param string $product_alias Alias of product table
     * @param string|int|null $product_attribute If string : alias of PA table ; if int : value of PA ; if null : nothing about PA
     * @param bool $inner_join LEFT JOIN or INNER JOIN
     * @param Shop|null $shop
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
     * @param int $out_of_stock
     *                          - O Deny orders
     *                          - 1 Allow orders
     *                          - 2 Use global setting
     *
     * @return bool|int Returns false is Stock Management is disabled, or the (int) configuration if it's enabled
     */
    public static function isAvailableWhenOutOfStock($out_of_stock)
    {
        /** @TODO 1.5.0 Update of STOCK_MANAGEMENT & ORDER_OUT_OF_STOCK */
        $ps_stock_management = Configuration::get('PS_STOCK_MANAGEMENT');

        if (!$ps_stock_management) {
            return true;
        }

        $ps_order_out_of_stock = Configuration::get('PS_ORDER_OUT_OF_STOCK');

        return (int) $out_of_stock === OutOfStockType::OUT_OF_STOCK_DEFAULT ? (int) $ps_order_out_of_stock : (int) $out_of_stock;
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
     *
     * @return bool
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

    /**
     * @param array $products
     * @param bool $have_stock DEPRECATED
     *
     * @return array|false
     */
    public static function getAttributesColorList(array $products, $have_stock = true)
    {
        if ($have_stock !== true) {
            Tools::displayParameterAsDeprecated('have_stock');
        }

        if (!count($products)) {
            return [];
        }

        $id_lang = Context::getContext()->language->id;

        $check_stock = !Configuration::get('PS_DISP_UNAVAILABLE_ATTR');
        if (!$res = Db::getInstance()->executeS(
            'SELECT pa.`id_product`, a.`color`, pac.`id_product_attribute`, ' . ($check_stock ? 'SUM(IF(stock.`quantity` > 0, 1, 0))' : '0') . ' qty, a.`id_attribute`, al.`name`, IF(color = "", a.id_attribute, color) group_by
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
        )) {
            return false;
        }

        $colors = [];
        /** @var array{id_product: int, id_attribute: int, id_product_attribute: int, color: string, texture: string, name: string,} $row */
        foreach ($res as $row) {
            $row['texture'] = '';

            if (@filemtime(_PS_COL_IMG_DIR_ . $row['id_attribute'] . '.jpg')) {
                $row['texture'] = _THEME_COL_DIR_ . $row['id_attribute'] . '.jpg';
            } elseif (Tools::isEmpty($row['color'])) {
                continue;
            }

            $colors[(int) $row['id_product']][] = [
                'id_product_attribute' => (int) $row['id_product_attribute'],
                'color' => $row['color'],
                'texture' => $row['texture'],
                'id_product' => $row['id_product'],
                'name' => $row['name'],
                'id_attribute' => $row['id_attribute'],
            ];
        }

        return $colors;
    }

    /**
     * Get all available attribute groups.
     *
     * @param int $id_lang Language identifier
     * @param int $id_product_attribute Combination id to get the groups for
     *
     * @return array Attribute groups
     */
    public function getAttributesGroups($id_lang, $id_product_attribute = null)
    {
        if (!Combination::isFeatureActive()) {
            return [];
        }
        $sql = 'SELECT ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, agl.`public_name` AS public_group_name,
                    a.`id_attribute`, al.`name` AS attribute_name, a.`color` AS attribute_color, product_attribute_shop.`id_product_attribute`,
                    IFNULL(stock.quantity, 0) as quantity, product_attribute_shop.`price`, product_attribute_shop.`ecotax`, product_attribute_shop.`weight`,
                    product_attribute_shop.`default_on`, pa.`reference`, pa.`ean13`, pa.`mpn`, pa.`upc`, pa.`isbn`, product_attribute_shop.`unit_price_impact`,
                    product_attribute_shop.`minimal_quantity`, product_attribute_shop.`available_date`, ag.`group_type`,
                    pal.`available_now`, pal.`available_later`
                FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                ' . Product::sqlStock('pa', 'pa') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_lang` pal
                    ON (
                        pa.`id_product_attribute` = pal.`id_product_attribute` AND
                        pal.`id_lang` = ' . (int) Context::getContext()->language->id . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON (a.`id_attribute` = pac.`id_attribute`)
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON (ag.`id_attribute_group` = a.`id_attribute_group`)
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute`)
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group`)
                ' . Shop::addSqlAssociation('attribute', 'a') . '
                WHERE pa.`id_product` = ' . (int) $this->id . '
                    AND al.`id_lang` = ' . (int) $id_lang . '
                    AND agl.`id_lang` = ' . (int) $id_lang . '
                ';

        if ($id_product_attribute !== null) {
            $sql .= ' AND product_attribute_shop.`id_product_attribute` = ' . (int) $id_product_attribute . ' ';
        }

        $sql .= 'GROUP BY id_attribute_group, id_product_attribute
                ORDER BY ag.`position` ASC, a.`position` ASC, agl.`name` ASC';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Delete product accessories.
     * Wrapper to static method deleteAccessories($product_id).
     *
     * @return bool Deletion result
     */
    public function deleteAccessories()
    {
        return Db::getInstance()->delete('accessory', 'id_product_1 = ' . (int) $this->id);
    }

    /**
     * Delete product carrier restriction.
     *
     * @return bool Deletion result
     */
    public function deleteCarrierRestrictions()
    {
        $all_shops = Context::getContext()->shop->getContext() == Shop::CONTEXT_ALL ? true : false;

        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'product_carrier`
            WHERE `id_product` = ' . (int) $this->id . (!$all_shops ? ' AND `id_shop` = ' . (int) Context::getContext()->shop->id : '')
        );
    }

    /**
     * Delete product from other products accessories.
     *
     * @return bool Deletion result
     */
    public function deleteFromAccessories()
    {
        return Db::getInstance()->delete('accessory', 'id_product_2 = ' . (int) $this->id);
    }

    /**
     * Get product accessories (only names).
     *
     * @param int $id_lang Language identifier
     * @param int $id_product Product identifier
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
     * @param int $id_lang Language identifier
     * @param bool $active
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

    /**
     * @param int $accessory_id Product identifier
     *
     * @return array
     */
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
     * @param array<int> $accessories_id Accessories ids
     * @param int $product_id Product identifier
     *
     * @return void
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
     *
     * @param int $id_value Feature identifier
     * @param int $lang Language identifier
     * @param string $cust Text of custom value
     *
     * @return bool
     */
    public function addFeaturesCustomToDB($id_value, $lang, $cust)
    {
        $row = ['id_feature_value' => (int) $id_value, 'id_lang' => (int) $lang, 'value' => pSQL($cust)];

        return Db::getInstance()->insert('feature_value_lang', $row);
    }

    /**
     * @param int $id_feature Feature identifier
     * @param int $id_value FeatureValue identifier
     * @param int $cust 1 = use a custom value, 0 = use $id_value
     *
     * @return int|string|void FeatureValue identifier or void if it fail
     */
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

    /**
     * @param int $id_product Product identifier
     * @param int $id_feature Feature identifier
     * @param int $id_feature_value FeatureValue identifier
     *
     * @return bool
     */
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

    /**
     * @param int $id_product Product identifier
     *
     * @return array
     */
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

    /**
     * @param int[] $product_ids
     */
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

    /**
     * @param int[] $product_ids Product identifier(s)
     * @param int $id_lang Language identifier
     */
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
     * @param int $id_lang Language identifier
     * @param string $query Search query
     * @param Context|null $context Deprecated, obsolete parameter not used anymore
     * @param int|null $limit
     *
     * @return array|false Matching products
     */
    public static function searchByName($id_lang, $query, Context $context = null, $limit = null)
    {
        if ($context !== null) {
            Tools::displayParameterAsDeprecated('context');
        }
        $sql = new DbQuery();
        $sql->select('p.`id_product`, pl.`name`, p.`ean13`, p.`isbn`, p.`upc`, p.`mpn`, p.`active`, p.`reference`, m.`name` AS manufacturer_name, stock.`quantity`, p.`customizable`');
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
        /** @var array{id_product: int} $row */
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
     * @param int $id_product_old Old Product identifier
     * @param int $id_product_new New Product identifier
     *
     * @return array|false
     */
    public static function duplicateAttributes($id_product_old, $id_product_new)
    {
        $return = true;
        $combination_images = [];

        $result = Db::getInstance()->executeS(
            'SELECT pa.*, product_attribute_shop.*
            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
            WHERE pa.`id_product` = ' . (int) $id_product_old
        );
        $combinations = [];
        $product_supplier_keys = [];

        /** @var array{id_product_attribute: int, id_shop: int} $row */
        foreach ($result as $row) {
            $id_product_attribute_old = (int) $row['id_product_attribute'];
            $result2 = [];
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
            self::$_combination_associations[$id_product_attribute_old] = $id_product_attribute_new;

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
                if (isset($context_old, $context_shop_id_old)) {
                    Shop::setContext($context_old, $context_shop_id_old);
                }
            }

            //Copy suppliers
            $result3 = Db::getInstance()->executeS('
            SELECT *
            FROM `' . _DB_PREFIX_ . 'product_supplier`
            WHERE `id_product_attribute` = ' . (int) $id_product_attribute_old . '
            AND `id_product` = ' . (int) $id_product_old);

            foreach ($result3 as $row3) {
                $current_supplier_key = $id_product_new . '_' . $id_product_attribute_new . '_' . $row3['id_supplier'];

                if (in_array($current_supplier_key, $product_supplier_keys)) {
                    continue;
                }

                $product_supplier_keys[] = $current_supplier_key;

                unset($row3['id_product_supplier']);
                $row3['id_product'] = $id_product_new;
                $row3['id_product_attribute'] = $id_product_attribute_new;
                $return &= Db::getInstance()->insert('product_supplier', $row3);
            }
        }

        return !$return ? false : $combination_images;
    }

    /**
     * Get product attribute image associations.
     *
     * @param int $id_product_attribute Attribute identifier
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

    /**
     * @param int $id_product_old Old Product identifier
     * @param int $id_product_new New Product identifier
     *
     * @return bool|int
     */
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

    /**
     * @param int $id_product_old Old Product identifier
     * @param int $id_product_new New Product identifier
     *
     * @return bool
     */
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

    /**
     * @param int $id_product_old Old Product identifier
     * @param int $id_product_new New Product identifier
     *
     * @return bool
     */
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
     * @param int $id_product_old Old Product identifier
     * @param int $id_product_new New Product identifier
     *
     * @return bool
     */
    public static function duplicatePrices($id_product_old, $id_product_new)
    {
        $query = new DbQuery();
        $query->select('price, unit_price, id_shop');
        $query->from('product_shop');
        $query->where('`id_product` = ' . (int) $id_product_old);
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query->build());
        if (!empty($results)) {
            foreach ($results as $result) {
                if (!Db::getInstance()->update(
                    'product_shop',
                    ['price' => pSQL($result['price']), 'unit_price' => pSQL($result['unit_price'])],
                    'id_product=' . (int) $id_product_new . ' AND id_shop = ' . (int) $result['id_shop']
                )) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param int $id_product_old Old Product identifier
     * @param int $id_product_new New Product identifier
     *
     * @return bool
     */
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

    /**
     * @param int $id_product_old Old Product identifier
     * @param int $id_product_new New Product identifier
     *
     * @return bool
     */
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
     * @param int $id_product_old Old Product identifier
     * @param int $id_product_new New Product identifier
     *
     * @return bool
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

    /**
     * @param int $product_id Product identifier
     * @param int|null $id_shop Shop identifier
     *
     * @return array|false
     */
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
            AND `is_deleted` = 0
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

    /**
     * @param int $old_product_id Old Product identifier
     * @param int $product_id New Product identifier
     *
     * @return bool
     */
    public static function duplicateSpecificPrices($old_product_id, $product_id)
    {
        foreach (SpecificPrice::getIdsByProductId((int) $old_product_id) as $data) {
            $specific_price = new SpecificPrice((int) $data['id_specific_price']);
            if (!$specific_price->duplicate((int) $product_id, self::$_combination_associations)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param int $old_product_id Old Product identifier
     * @param int $product_id New Product identifier
     *
     * @return bool
     */
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

            if (
                !Db::getInstance()->insert('customization_field', $customization_field)
                || !$customization_field_id = Db::getInstance()->Insert_ID()
            ) {
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
     * @param int $id_product_old Old Product identifier
     * @param int $id_product_new New Product identifier
     *
     * @return bool
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
     * Adds carriers from old product onto a newly duplicated product.
     *
     * @param int $oldProductId Old Product identifier
     * @param int $newProductId New Product identifier
     *
     * @return bool
     */
    public static function duplicateCarriers(int $oldProductId, int $newProductId): bool
    {
        //@todo: this will copy carriers from all shops. todo - Handle multishop according context & specifications.
        $oldProductCarriers = Db::getInstance()->executeS(
            'SELECT *
            FROM `' . _DB_PREFIX_ . 'product_carrier`
            WHERE `id_product` = ' . (int) $oldProductId
        );

        foreach ($oldProductCarriers as $row) {
            $row['id_product'] = $newProductId;
            if (!Db::getInstance()->insert('product_carrier', $row)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Associates attachments from old product onto a newly duplicated product.
     *
     * @param int $oldProductId Old Product identifier
     * @param int $newProductId New Product identifier
     *
     * @return bool
     */
    public static function duplicateAttachmentAssociation(int $oldProductId, int $newProductId): bool
    {
        $oldProductAttachments = Db::getInstance()->executeS(
            'SELECT *
            FROM `' . _DB_PREFIX_ . 'product_attachment`
            WHERE `id_product` = ' . (int) $oldProductId
        );

        foreach ($oldProductAttachments as $row) {
            $row['id_product'] = $newProductId;
            if (!Db::getInstance()->insert('product_attachment', $row)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the link of the product page of this product.
     *
     * @param Context|null $context
     *
     * @return string
     */
    public function getLink(Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        return $context->link->getProductLink($this);
    }

    /**
     * @param int $id_lang Language identifier
     *
     * @return string
     */
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

    /**
     * @param array $row
     * @param int $id_lang Language identifier
     *
     * @return string
     */
    public static function defineProductImage($row, $id_lang)
    {
        if (!empty($row['id_image'])) {
            return $row['id_image'];
        }

        return Language::getIsoById((int) $id_lang) . '-default';
    }

    /**
     * @param int $id_lang Language identifier
     * @param array $row
     * @param Context|null $context
     *
     * @return array|false
     */
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
        if (
            Combination::isFeatureActive() &&
            $id_product_attribute === null &&
            (
                (isset($row['cache_default_attribute']) && ($ipa_default = $row['cache_default_attribute']) !== null)
                || ($ipa_default = Product::getDefaultAttribute($row['id_product'], (int) !$row['allow_oosp']))
            )
        ) {
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

        // Get manufacturer name if missing
        if (empty($row['manufacturer_name'])) {
            // Assign empty value
            $row['manufacturer_name'] = null;

            // If we have manufacturer ID, we wil try to load it's name and assign it
            if (!empty($row['id_manufacturer'])) {
                $manufacturerName = Manufacturer::getNameById((int) $row['id_manufacturer']);
                if (!empty($manufacturerName)) {
                    $row['manufacturer_name'] = $manufacturerName;
                }
            }
        }

        if (isset($row['quantity_wanted'])) {
            // 'quantity_wanted' may very well be zero even if set
            $quantity = max((int) $row['minimal_quantity'], (int) $row['quantity_wanted']);
        } elseif (isset($row['cart_quantity'])) {
            $quantity = max((int) $row['minimal_quantity'], (int) $row['cart_quantity']);
        } else {
            $quantity = (int) $row['minimal_quantity'];
        }

        // We save value in $priceTaxExcluded and $priceTaxIncluded before they may be rounded
        $row['price_tax_exc'] = $priceTaxExcluded = Product::getPriceStatic(
            (int) $row['id_product'],
            false,
            $id_product_attribute,
            (self::$_taxCalculationMethod == PS_TAX_EXC ? Context::getContext()->getComputingPrecision() : 6),
            null,
            false,
            true,
            $quantity
        );

        if (self::$_taxCalculationMethod == PS_TAX_EXC) {
            $row['price_tax_exc'] = Tools::ps_round($priceTaxExcluded, Context::getContext()->getComputingPrecision());
            $row['price'] = $priceTaxIncluded = Product::getPriceStatic(
                (int) $row['id_product'],
                true,
                $id_product_attribute,
                6,
                null,
                false,
                true,
                $quantity
            );
            $row['price_without_reduction'] = $row['price_without_reduction_without_tax'] = Product::getPriceStatic(
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
            $priceTaxIncluded = Product::getPriceStatic(
                (int) $row['id_product'],
                true,
                $id_product_attribute,
                6,
                null,
                false,
                true,
                $quantity
            );
            $row['price'] = Tools::ps_round($priceTaxIncluded, Context::getContext()->getComputingPrecision());
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

        /* Get quantity of the base product.
         * For products without combinations - self explanatory.
         * For products with combinations - this value is a SUM of quantities of all combinations.
         * You have 2 black shirts + 2 white shirts = $quantity 4.
         */
        $row['quantity'] = Product::getQuantity(
            (int) $row['id_product'],
            0,
            isset($row['cache_is_pack']) ? $row['cache_is_pack'] : null,
            $context->cart,
            false
        );

        $row['quantity_all_versions'] = $row['quantity'];

        // If we have some combination ID specified, we will return more precise stock and date for this combination
        if ($row['id_product_attribute']) {
            $row['quantity'] = Product::getQuantity(
                (int) $row['id_product'],
                $id_product_attribute,
                isset($row['cache_is_pack']) ? $row['cache_is_pack'] : null,
                $context->cart,
                false
            );

            $row['available_date'] = Product::getAvailableDate(
                (int) $row['id_product'],
                $id_product_attribute
            );
        }

        /*
         * Loading of files attached to product. This is using cache_has_attachments property which needs to be managed
         * every time a file is changed. It can sometimes lead to database inconsistency.
         *
         * It would be better to lazy load it in ProductLazyArray so we can just always take the live data
         * if needed and would not need to take care about cache_has_attachments.
         */
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

        if (!isset($row['attributes'])) {
            $attributes = Product::getAttributesParams($row['id_product'], $row['id_product_attribute']);

            foreach ($attributes as $attribute) {
                $row['attributes'][$attribute['id_attribute_group']] = $attribute;
            }
        }

        $row = Product::getTaxesInformations($row, $context);

        $row['ecotax_rate'] = (float) Tax::getProductEcotaxRate($context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});

        Hook::exec('actionGetProductPropertiesAfter', [
            'id_lang' => $id_lang,
            'product' => &$row,
            'context' => $context,
        ]);

        // Always recompute unit prices based on initial ratio so that discounts are applied on unit price as well
        $unitPriceRatio = self::computeUnitPriceRatio($row, $id_product_attribute, $quantity, $context);
        $row['unit_price_ratio'] = $unitPriceRatio;
        $row['unit_price_tax_excluded'] = $unitPriceRatio != 0 ? $priceTaxExcluded / $unitPriceRatio : 0.0;
        $row['unit_price_tax_included'] = $unitPriceRatio != 0 ? $priceTaxIncluded / $unitPriceRatio : 0.0;

        Hook::exec('actionGetProductPropertiesAfterUnitPrice', [
            'id_lang' => $id_lang,
            'product' => &$row,
            'context' => $context,
        ]);

        self::$productPropertiesCache[$cache_key] = $row;

        return self::$productPropertiesCache[$cache_key];
    }

    /**
     * Compute unit price ratio based on the saved unit price, we make sure that quantities, currency rates and
     * combination impact are taken into account.
     *
     * @param array $productRow
     * @param int $combinationId
     * @param int $quantity
     * @param Context $context
     *
     * @return float
     */
    private static function computeUnitPriceRatio(array $productRow, int $combinationId, int $quantity, Context $context): float
    {
        $baseUnitPrice = 0.0;
        if (isset($productRow['unit_price'])) {
            // Unit price is supposed to be in DB and accessible in the row
            $baseUnitPrice = (float) $productRow['unit_price'];
        }

        // Then if combination has an impact we apply it on unit price
        if ($combinationId) {
            $combination = new Combination($combinationId);
            if (0 != $combination->unit_price_impact && 0 != $baseUnitPrice) {
                $baseUnitPrice = $baseUnitPrice + $combination->unit_price_impact;
            }
        }

        // Finally, we apply the currency rate
        $defaultCurrencyId = Currency::getDefaultCurrencyId();
        $currencyId = Validate::isLoadedObject($context->currency) ? (int) $context->currency->id : $defaultCurrencyId;
        if ($currencyId !== $defaultCurrencyId) {
            $baseUnitPrice = Tools::convertPrice($baseUnitPrice, $currencyId);
        }

        if ($baseUnitPrice == 0) {
            return 0;
        }

        // Compute price ratio based on initial product price and initial unit price (without taxes, group discount, cart rules)
        $noSpecificPrice = null;
        $baseProductPrice = Product::getPriceStatic(
            (int) $productRow['id_product'],
            false,
            $combinationId,
            6,
            null,
            false,
            false,
            $quantity,
            false,
            null,
            null,
            null,
            $noSpecificPrice,
            true,
            false
        );

        return $baseProductPrice / $baseUnitPrice;
    }

    /**
     * @param array $row
     * @param Context|null $context
     *
     * @return array
     */
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

    /**
     * @param int $id_lang Language identifier
     * @param array $query_result
     *
     * @return array
     */
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
     * @param int $id_lang Language identifier
     * @param int $id_product Product identifier
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
                SELECT name, value, pf.id_feature, f.position, fvl.id_feature_value
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

    /**
     * @param int $id_lang Language identifier
     *
     * @return array
     */
    public function getFrontFeatures($id_lang)
    {
        return Product::getFrontFeaturesStatic($id_lang, $this->id);
    }

    /**
     * @param int $id_lang Language identifier
     * @param int $id_product Product identifier
     *
     * @return array
     */
    public static function getAttachmentsStatic($id_lang, $id_product)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT *
        FROM ' . _DB_PREFIX_ . 'product_attachment pa
        LEFT JOIN ' . _DB_PREFIX_ . 'attachment a ON a.id_attachment = pa.id_attachment
        LEFT JOIN ' . _DB_PREFIX_ . 'attachment_lang al ON (a.id_attachment = al.id_attachment AND al.id_lang = ' . (int) $id_lang . ')
        WHERE pa.id_product = ' . (int) $id_product);
    }

    /**
     * @return int[]
     *
     * @throws PrestaShopDatabaseException
     */
    public function getAssociatedAttachmentIds(): array
    {
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT id_attachment
            FROM ' . _DB_PREFIX_ . 'product_attachment
            WHERE id_product = ' . (int) $this->id
        );

        if (!$results) {
            return [];
        }

        return array_map(function (array $result): int {
            return (int) $result['id_attachment'];
        }, $results);
    }

    /**
     * @param int $id_lang Language identifier
     *
     * @return array
     */
    public function getAttachments($id_lang)
    {
        return Product::getAttachmentsStatic($id_lang, $this->id);
    }

    /**
     * Customization management
     *
     * @param int $id_cart Cart identifier
     * @param int|null $id_lang Language identifier
     * @param bool $only_in_cart
     * @param int|null $id_shop Shop identifier
     * @param int|null $id_customization Customization identifier
     *
     * @return array|false
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

        // Load cart object to get delivery address ID
        $cart = new Cart((int) $id_cart);

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
            SELECT cd.`id_customization`, c.`id_product`, cfl.`id_customization_field`, c.`id_product_attribute`,
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
                // Hook called only for the module concerned
                // When a module saves a customization programmatically, it should add its ID in the `id_module` column
                $row['value'] = Hook::exec('displayCustomization', ['customization' => $row], (int) $row['id_module']);
            }
            $customized_datas[(int) $row['id_product']][(int) $row['id_product_attribute']][(int) $cart->id_address_delivery][(int) $row['id_customization']]['datas'][(int) $row['type']][] = $row;
        }

        if (!$result = Db::getInstance()->executeS(
            'SELECT `id_product`, `id_product_attribute`, `id_customization`, `quantity`, `quantity_refunded`, `quantity_returned`
            FROM `' . _DB_PREFIX_ . 'customization`
            WHERE `id_cart` = ' . (int) $id_cart .
            ((int) $id_customization ? ' AND `id_customization` = ' . (int) $id_customization : '') .
            ($only_in_cart ? ' AND `in_cart` = 1' : '')
        )) {
            return false;
        }

        foreach ($result as $row) {
            $customized_datas[(int) $row['id_product']][(int) $row['id_product_attribute']][(int) $cart->id_address_delivery][(int) $row['id_customization']]['quantity'] = (int) $row['quantity'];
            $customized_datas[(int) $row['id_product']][(int) $row['id_product_attribute']][(int) $cart->id_address_delivery][(int) $row['id_customization']]['quantity_refunded'] = (int) $row['quantity_refunded'];
            $customized_datas[(int) $row['id_product']][(int) $row['id_product_attribute']][(int) $cart->id_address_delivery][(int) $row['id_customization']]['quantity_returned'] = (int) $row['quantity_returned'];
            $customized_datas[(int) $row['id_product']][(int) $row['id_product_attribute']][(int) $cart->id_address_delivery][(int) $row['id_customization']]['id_customization'] = (int) $row['id_customization'];
        }

        return $customized_datas;
    }

    /**
     * @deprecated since 9.0.0, the customization price impact is already included in Product::getPriceStatic.
     *
     * @param array $products
     * @param array $customized_datas
     */
    public static function addCustomizationPrice(&$products, &$customized_datas)
    {
        @trigger_error(
            sprintf(
                '%s is deprecated since version 9.0.0. The customization price impact is already included in Product::getPriceStatic.',
                __METHOD__
            ),
            E_USER_DEPRECATED
        );

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

    /**
     * @deprecated since 9.0.0, the customization price impact is already included in Product::getPriceStatic.
     *
     * Add customization price for a single product
     *
     * @param array $product Product data
     * @param array $customized_datas Customized data
     */
    public static function addProductCustomizationPrice(&$product, &$customized_datas)
    {
        @trigger_error(
            sprintf(
                '%s is deprecated since version 9.0.0. The customization price impact is already included in Product::getPriceStatic.',
                __METHOD__
            ),
            E_USER_DEPRECATED
        );

        if (!$customized_datas) {
            return;
        }

        $products = [$product];
        self::addCustomizationPrice($products, $customized_datas);
        $product = $products[0];
    }

    /**
     * Customization fields label management
     *
     * @param string $field
     * @param string $value
     *
     * @return array|false
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

    /**
     * @return bool
     */
    protected function _deleteOldLabels()
    {
        $max = [
            Product::CUSTOMIZE_FILE => (int) $this->uploadable_files,
            Product::CUSTOMIZE_TEXTFIELD => (int) $this->text_fields,
        ];

        /* Get customization field ids */
        if (($result = Db::getInstance()->executeS(
            'SELECT `id_customization_field`, `type`
            FROM `' . _DB_PREFIX_ . 'customization_field`
            WHERE `id_product` = ' . (int) $this->id . '
            ORDER BY `id_customization_field`'
        )) === false) {
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
        if (
            $extra_file > 0 &&
            count($customization_fields[Product::CUSTOMIZE_FILE]) - $extra_file >= 0 &&
            (!Db::getInstance()->execute(
                'DELETE `' . _DB_PREFIX_ . 'customization_field`,`' . _DB_PREFIX_ . 'customization_field_lang`
                FROM `' . _DB_PREFIX_ . 'customization_field` JOIN `' . _DB_PREFIX_ . 'customization_field_lang`
                WHERE `' . _DB_PREFIX_ . 'customization_field`.`id_product` = ' . (int) $this->id . '
                AND `' . _DB_PREFIX_ . 'customization_field`.`type` = ' . Product::CUSTOMIZE_FILE . '
                AND `' . _DB_PREFIX_ . 'customization_field_lang`.`id_customization_field` = `' . _DB_PREFIX_ . 'customization_field`.`id_customization_field`
                AND `' . _DB_PREFIX_ . 'customization_field`.`id_customization_field` >= ' . (int) $customization_fields[Product::CUSTOMIZE_FILE][count($customization_fields[Product::CUSTOMIZE_FILE]) - $extra_file]
            ))
        ) {
            return false;
        }

        if (
            $extra_text > 0 &&
            count($customization_fields[Product::CUSTOMIZE_TEXTFIELD]) - $extra_text >= 0 &&
            (!Db::getInstance()->execute(
                'DELETE `' . _DB_PREFIX_ . 'customization_field`,`' . _DB_PREFIX_ . 'customization_field_lang`
                FROM `' . _DB_PREFIX_ . 'customization_field` JOIN `' . _DB_PREFIX_ . 'customization_field_lang`
                WHERE `' . _DB_PREFIX_ . 'customization_field`.`id_product` = ' . (int) $this->id . '
                AND `' . _DB_PREFIX_ . 'customization_field`.`type` = ' . Product::CUSTOMIZE_TEXTFIELD . '
                AND `' . _DB_PREFIX_ . 'customization_field_lang`.`id_customization_field` = `' . _DB_PREFIX_ . 'customization_field`.`id_customization_field`
                AND `' . _DB_PREFIX_ . 'customization_field`.`id_customization_field` >= ' . (int) $customization_fields[Product::CUSTOMIZE_TEXTFIELD][count($customization_fields[Product::CUSTOMIZE_TEXTFIELD]) - $extra_text]
            ))
        ) {
            return false;
        }

        // Refresh cache of feature detachable
        Configuration::updateGlobalValue('PS_CUSTOMIZATION_FEATURE_ACTIVE', Customization::isCurrentlyUsed());

        return true;
    }

    /**
     * @param array $languages An array of language data
     * @param int $type Product::CUSTOMIZE_FILE or Product::CUSTOMIZE_TEXTFIELD
     *
     * @return bool
     */
    protected function _createLabel($languages, $type)
    {
        // Label insertion
        if (
            !Db::getInstance()->execute('
                INSERT INTO `' . _DB_PREFIX_ . 'customization_field` (`id_product`, `type`, `required`)
                VALUES (' . (int) $this->id . ', ' . (int) $type . ', 0)')
            || !$id_customization_field = (int) Db::getInstance()->Insert_ID()
        ) {
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

    /**
     * @param int $uploadable_files
     * @param int $text_fields
     *
     * @return bool
     */
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

    /**
     * @return bool
     */
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

    /**
     * @param int|false $id_lang Language identifier
     * @param int|null $id_shop Shop identifier
     *
     * @return array|bool
     */
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

        if (!$result = Db::getInstance()->executeS(
            'SELECT cf.`id_customization_field`, cf.`type`, cf.`required`, cfl.`name`, cfl.`id_lang`
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
     * @throws PrestaShopDatabaseException
     */
    public function hasActivatedRequiredCustomizableFields()
    {
        if (!Customization::isFeatureActive()) {
            return false;
        }

        return (bool) Db::getInstance()->executeS(
            'SELECT 1
            FROM `' . _DB_PREFIX_ . 'customization_field`
            WHERE `id_product` = ' . (int) $this->id . '
            AND `required` = 1
            AND `is_deleted` = 0'
        );
    }

    /**
     * @return array
     */
    public function getCustomizationFieldIds()
    {
        if (!Customization::isFeatureActive()) {
            return [];
        }

        return Db::getInstance()->executeS(
            'SELECT `id_customization_field`, `type`, `required`
            FROM `' . _DB_PREFIX_ . 'customization_field`
            WHERE `id_product` = ' . (int) $this->id
        );
    }

    /**
     * @return array
     */
    public function getNonDeletedCustomizationFieldIds()
    {
        if (!Customization::isFeatureActive()) {
            return [];
        }

        $results = Db::getInstance()->executeS(
            'SELECT `id_customization_field`
            FROM `' . _DB_PREFIX_ . 'customization_field`
            WHERE `is_deleted` = 0
            AND `id_product` = ' . (int) $this->id
        );

        return array_map(function ($result) {
            return (int) $result['id_customization_field'];
        }, $results);
    }

    /**
     * @param int $fieldType |null
     *
     * @return int
     *
     * @throws PrestaShopDatabaseException
     */
    public function countCustomizationFields(?int $fieldType = null): int
    {
        $query = 'SELECT COUNT(`id_customization_field`) as customizations_count
            FROM `' . _DB_PREFIX_ . 'customization_field`
            WHERE `is_deleted` = 0
            AND `id_product` = ' . (int) $this->id;

        if (null !== $fieldType) {
            $query .= sprintf(' AND type = %d', $fieldType);
        }

        $results = Db::getInstance()->executeS($query);

        if (empty($results)) {
            return 0;
        }

        return (int) reset($results)['customizations_count'];
    }

    /**
     * @return array
     */
    public function getRequiredCustomizableFields()
    {
        if (!Customization::isFeatureActive()) {
            return [];
        }

        return Product::getRequiredCustomizableFieldsStatic($this->id);
    }

    /**
     * @param int $id Product identifier
     *
     * @return array
     */
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

    /**
     * @param Context|null $context
     *
     * @return bool
     */
    public function hasAllRequiredCustomizableFields(Context $context = null)
    {
        if (!Customization::isFeatureActive()) {
            return true;
        }
        if (!$context) {
            $context = Context::getContext();
        }

        $fields = $context->cart->getProductCustomization($this->id, null, true);
        $required_fields = $this->getRequiredCustomizableFields();

        $fields_present = [];
        foreach ($fields as $field) {
            $fields_present[] = ['id_customization_field' => $field['index'], 'type' => $field['type']];
        }

        foreach ($required_fields as $required_field) {
            if (!in_array($required_field, $fields_present)) {
                return false;
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
        $sql = 'SELECT id_product FROM `' . _DB_PREFIX_ . 'product` WHERE state=' . Product::STATE_TEMP . ' AND date_upd < NOW() - INTERVAL 1 DAY';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);
    }

    /**
     * Checks if the product is in at least one of the submited categories.
     *
     * @param int $id_product Product identifier
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

    /**
     * @return string
     */
    public function getNoPackPrice()
    {
        $context = Context::getContext();

        return Tools::getContextLocale($context)->formatPrice(Pack::noPackPrice((int) $this->id), $context->currency->iso_code);
    }

    /**
     * @param int $id_customer Customer identifier
     *
     * @return bool
     */
    public function checkAccess($id_customer)
    {
        return Product::checkAccessStatic((int) $this->id, (int) $id_customer);
    }

    /**
     * @param int $id_product Product identifier
     * @param int|bool $id_customer Customer identifier
     *
     * @return bool
     */
    public static function checkAccessStatic($id_product, $id_customer)
    {
        // If group feature is disabled in performance configuration, we don't check anything and allow access
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
     * @return int TaxRulesGroup identifier
     */
    public function getIdTaxRulesGroup()
    {
        return $this->id_tax_rules_group;
    }

    /**
     * @param int $id_product Product identifier
     * @param Context|null $context
     *
     * @return int TaxRulesGroup identifier
     */
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
     * @param array $product_features Feature data
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
     * @return int Default Attribute identifier
     */
    public function getWsDefaultCombination()
    {
        return Product::getDefaultAttribute($this->id);
    }

    /**
     * Webservice setter : set virtual field default combination.
     *
     * @param int $id_combination Default Attribute identifier
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
                $pos = 1;
                if (array_key_exists((int) $id, $positions_lookup)) {
                    $pos = (int) $positions_lookup[(int) $id];
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
     * @param array $accessories product ids
     *
     * @return bool
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
     * @param array $combinations combination ids
     *
     * @return bool
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
        $all = Db::getInstance()->executeS(
            'SELECT pa.`id_product_attribute` as id
            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
            ' . Shop::addSqlAssociation('product_attribute', 'pa')
        );
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
        $result = Db::getInstance()->executeS(
            'SELECT DISTINCT pac.id_attribute as id
            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON (pac.id_product_attribute = pa.id_product_attribute)
            WHERE pa.id_product = ' . (int) $this->id
        );

        return $result;
    }

    /**
     * Webservice getter : get virtual field position in category.
     *
     * @return int|string
     */
    public function getWsPositionInCategory()
    {
        $result = Db::getInstance()->executeS(
            'SELECT `position`
            FROM `' . _DB_PREFIX_ . 'category_product`
            WHERE `id_category` = ' . (int) $this->id_category_default . '
            AND `id_product` = ' . (int) $this->id
        );
        if (count($result) > 0) {
            return $result[0]['position'];
        }

        return '';
    }

    /**
     * Webservice setter : set virtual field position in category.
     *
     * @param int $position
     *
     * @return bool
     */
    public function setWsPositionInCategory($position)
    {
        if ($position <= 0) {
            WebserviceRequest::getInstance()->setError(
                500,
                $this->trans(
                    'You cannot set 0 or a negative position, the minimum is 1.',
                    [],
                    'Admin.Catalog.Notification'
                ),
                134
            );

            return false;
        }

        $result = Db::getInstance()->executeS(
            'SELECT `id_product` ' .
            'FROM `' . _DB_PREFIX_ . 'category_product` ' .
            'WHERE `id_category` = ' . (int) $this->id_category_default . '  ' .
            'ORDER BY `position`'
        );

        if ($position > count($result)) {
            WebserviceRequest::getInstance()->setError(
                500,
                $this->trans(
                    'You cannot set a position greater than the total number of products in the category, starting at 1.',
                    [],
                    'Admin.Catalog.Notification'
                ),
                135
            );

            return false;
        }

        // result is indexed by recordset order and not position. positions start at index 1 so we need an empty element
        array_unshift($result, null);
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
     * @return int|string|null
     */
    public function getCoverWs()
    {
        $result = $this->getCover($this->id);

        return $result ? $result['id_image'] : null;
    }

    /**
     * Webservice setter : set virtual field id_default_image in category.
     *
     * @param int $id_image
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

    /**
     * Webservice getter : Get StockAvailable identifier and Attribute identifier
     *
     * @return array
     */
    public function getWsStockAvailables()
    {
        return Db::getInstance()->executeS('SELECT `id_stock_available` id, `id_product_attribute`
            FROM `' . _DB_PREFIX_ . 'stock_available`
            WHERE `id_product`=' . (int) $this->id . StockAvailable::addSqlShopRestriction());
    }

    /**
     * Webservice getter: Get product attachments ids of current product for association
     *
     * @return array<int, array{id: string}>
     */
    public function getWsAttachments(): array
    {
        return Db::getInstance()->executeS(
            'SELECT a.`id_attachment` AS id ' .
            'FROM `' . _DB_PREFIX_ . 'product_attachment` pa ' .
            'INNER JOIN `' . _DB_PREFIX_ . 'attachment` a ON (pa.id_attachment = a.id_attachment) ' .
            Shop::addSqlAssociation('attachment', 'a') . ' ' .
            'WHERE pa.`id_product` = ' . (int) $this->id
        );
    }

    /**
     * Webservice setter: set product attachments ids of current product for association
     *
     * @param array<array{id: int|string}> $attachments ids
     */
    public function setWsAttachments(array $attachments): bool
    {
        $this->deleteAttachments(true);
        foreach ($attachments as $attachment) {
            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'product_attachment`
    				(`id_product`, `id_attachment`) VALUES (' . (int) $this->id . ', ' . (int) $attachment['id'] . ')');
        }
        Product::updateCacheAttachment((int) $this->id);

        return true;
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
     * @param array $tag_ids Tag identifiers
     *
     * @return bool
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
     * @return bool Deletion result
     */
    public function deleteWsTags()
    {
        return Db::getInstance()->delete('product_tag', 'id_product = ' . (int) $this->id);
    }

    /**
     * @return string
     */
    public function getWsManufacturerName()
    {
        return Manufacturer::getNameById((int) $this->id_manufacturer);
    }

    /**
     * @return bool
     */
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
     * @param string $reference Product reference
     *
     * @return bool
     */
    public function existsRefInDatabase($reference)
    {
        $row = Db::getInstance()->getRow('
        SELECT `reference`
        FROM `' . _DB_PREFIX_ . 'product` p
        WHERE p.reference = "' . pSQL($reference) . '"', false);

        return isset($row['reference']);
    }

    /**
     * Get all product attributes ids.
     *
     * @since 1.5.0
     *
     * @param int $id_product Product identifier
     * @param bool $shop_only
     *
     * @return array Attribute identifiers list
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
     * @param int $id_product Product identifier
     * @param int $id_product_attribute Attribute identifier
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
            SELECT a.`id_attribute`, a.`id_attribute_group`, al.`name`, agl.`name` as `group`,
            pa.`reference`, pa.`ean13`, pa.`isbn`, pa.`upc`, pa.`mpn`,
            pal.`available_now`, pal.`available_later`
            FROM `' . _DB_PREFIX_ . 'attribute` a
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al
                ON (al.`id_attribute` = a.`id_attribute` AND al.`id_lang` = ' . (int) $id_lang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                ON (pac.`id_attribute` = a.`id_attribute`)
            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa
                ON (pa.`id_product_attribute` = pac.`id_product_attribute`)
            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_lang` pal
                ON (pal.`id_product_attribute` = pac.`id_product_attribute` AND pal.`id_lang` = ' . (int) $id_lang . ')
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
     * @param int $id_product Product identifier
     *
     * @return array
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
        $attributes = self::getProductAttributesIds($this->id, true);

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
     * @param int $idProduct Product identifier
     * @param int|int[] $idAttributes Attribute identifier(s)
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
            'SELECT pac.`id_product_attribute`
                FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                INNER JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON pa.id_product_attribute = pac.id_product_attribute
                WHERE pa.id_product = ' . $idProduct . '
                AND pac.id_attribute IN (' . $idAttributesImploded . ')
                GROUP BY pac.`id_product_attribute`
                HAVING COUNT(pa.id_product) = ' . count($idAttributes)
        );

        if ($idProductAttribute === false && $findBest) {
            //find the best possible combination
            //first we order $idAttributes by the group position
            $orderred = [];
            $result = Db::getInstance()->executeS(
                'SELECT a.`id_attribute`
                FROM `' . _DB_PREFIX_ . 'attribute` a
                INNER JOIN `' . _DB_PREFIX_ . 'attribute_group` g ON a.`id_attribute_group` = g.`id_attribute_group`
                WHERE a.`id_attribute` IN (' . $idAttributesImploded . ')
                ORDER BY g.`position` ASC'
            );

            foreach ($result as $row) {
                $orderred[] = $row['id_attribute'];
            }

            while ($idProductAttribute === false && count($orderred) > 1) {
                array_pop($orderred);
                $idProductAttribute = Db::getInstance()->getValue(
                    'SELECT pac.`id_product_attribute`
                    FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                    INNER JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON pa.id_product_attribute = pac.id_product_attribute
                    WHERE pa.id_product = ' . (int) $idProduct . '
                    AND pac.id_attribute IN (' . implode(',', array_map('intval', $orderred)) . ')
                    GROUP BY pac.id_product_attribute
                    HAVING COUNT(pa.id_product) = ' . count($orderred)
                );
            }
        }

        if (empty($idProductAttribute)) {
            throw new PrestaShopObjectNotFoundException('Cannot retrieve the id_product_attribute');
        }

        return (int) $idProductAttribute;
    }

    /**
     * Get the combination url anchor of the product.
     *
     * @param int $id_product_attribute Attribute identifier
     * @param bool $with_id
     *
     * @return string
     */
    public function getAnchor($id_product_attribute, $with_id = false)
    {
        $attributes = Product::getAttributesParams($this->id, $id_product_attribute);
        $anchor = '#';
        $sep = Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR');
        $replace = $sep === '_' ? '-' : '_';
        foreach ($attributes as &$attr) {
            $group = str_replace($sep, $replace, Tools::str2url((string) $attr['group']));
            $name = str_replace($sep, $replace, Tools::str2url((string) $attr['name']));
            $anchor .= '/' . ($with_id ? (int) $attr['id_attribute'] . $sep : '') . $group . $sep . $name;
        }

        return $anchor;
    }

    /**
     * Gets the name of a given product, in the given lang.
     *
     * @since 1.5.0
     *
     * @param int $id_product Product identifier
     * @param int|null $id_product_attribute Attribute identifier
     * @param int|null $id_lang Language identifier
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

    /**
     * @param bool $autodate
     * @param bool $null_values
     *
     * @return bool
     */
    public function addWs($autodate = true, $null_values = false)
    {
        $success = $this->add($autodate, $null_values);
        if ($success && Configuration::get('PS_SEARCH_INDEXATION')) {
            Search::indexation(false, $this->id);
        }

        return $success;
    }

    /**
     * @param bool $null_values
     *
     * @return bool
     */
    public function updateWs($null_values = false)
    {
        if (null === $this->price) {
            $this->price = Product::getPriceStatic((int) $this->id, false, null, 6, null, false, true, 1, false, null, null, null, $this->specificPrice);
        }

        if (null === $this->unit_price_ratio) {
            $this->unit_price_ratio = ($this->unit_price != 0 ? $this->price / $this->unit_price : 0);
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
     * @param int $id_product Product identifier
     * @param int $id_product_attribute Attribute identifier
     * @param int $id_warehouse Warehouse identifier - not used anymore
     * @param int|null $id_shop Shop identifier
     *
     * @return int real_quantity
     *
     * @deprecated Since 9.0 and will be removed in 10.0 - use StockAvailable::getQuantityAvailableByProduct directly
     */
    public static function getRealQuantity($id_product, $id_product_attribute = 0, $id_warehouse = 0, $id_shop = null)
    {
        @trigger_error(sprintf(
            '%s is deprecated since 9.0 and will be removed in 10.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        return StockAvailable::getQuantityAvailableByProduct($id_product, $id_product_attribute, $id_shop);
    }

    /**
     * For a given product, tells if it uses the advanced stock management.
     *
     * @since 1.5.0
     *
     * @param int $id_product Product identifier
     *
     * @return bool
     *
     * @deprecated Since 9.0 and will be removed in 10.0
     */
    public static function usesAdvancedStockManagement($id_product)
    {
        @trigger_error(sprintf(
            '%s is deprecated since 9.0 and will be removed in 10.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        return false;
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
     * @param int|null $id_lang Language identifier
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
        $this->location = StockAvailable::getLocation($this->id) ?: '';
    }

    /**
     * Get the default category id according to the shop.
     *
     * @return int
     */
    public function getDefaultCategory(): int
    {
        $defaultCategory = Db::getInstance()->getValue(
            'SELECT product_shop.`id_category_default`
            FROM `' . _DB_PREFIX_ . 'product` p
            ' . Shop::addSqlAssociation('product', 'p') . '
            WHERE p.`id_product` = ' . (int) $this->id
        );

        return (int) ($defaultCategory ?? Context::getContext()->shop->id_category);
    }

    /**
     * Get Advanced Stock Management status for this product
     *
     * @return bool 0 for disabled, 1 for enabled
     */
    public function useAdvancedStockManagement()
    {
        @trigger_error(sprintf(
            '%s is deprecated since 9.0 and will be removed in 10.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        return false;
    }

    /**
     * Set Advanced Stock Management status for this product
     *
     * @param bool $value false for disabled, true for enabled
     */
    public function setAdvancedStockManagement($value)
    {
        @trigger_error(sprintf(
            '%s is deprecated since 9.0 and will be removed in 10.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        return;
    }

    /**
     * Get Shop identifiers
     *
     * @param int $id_product Product identifier
     *
     * @return array
     */
    public static function getShopsByProduct($id_product)
    {
        return Db::getInstance()->executeS(
            'SELECT `id_shop`
            FROM `' . _DB_PREFIX_ . 'product_shop`
            WHERE `id_product` = ' . (int) $id_product
        );
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
        /** @var ProductDownload $product_download */
        foreach ($collection_download as $product_download) {
            $result &= $product_download->delete($product_download->checkFile());
        }

        return $result;
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

    /**
     * @return bool
     */
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

    /**
     * @return string TaxRulesGroup identifier most used
     */
    public static function getIdTaxRulesGroupMostUsed()
    {
        return Db::getInstance()->getValue(
            'SELECT id_tax_rules_group
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
     * @return int|string Product identifier
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
     * @return int|string Product identifier
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

    /**
     * @return string simple, pack, virtual
     */
    public function getWsType()
    {
        $type_information = [
            Product::PTYPE_SIMPLE => 'simple',
            Product::PTYPE_PACK => 'pack',
            Product::PTYPE_VIRTUAL => 'virtual',
        ];

        return $type_information[$this->getType()];
    }

    /**
     * Create the link rewrite if not exists or invalid on product creation
     *
     * @return bool
     */
    public function modifierWsLinkRewrite()
    {
        if (empty($this->link_rewrite)) {
            $this->link_rewrite = [];
        }

        foreach ($this->name as $id_lang => $name) {
            if (empty($this->link_rewrite[$id_lang])) {
                $this->link_rewrite[$id_lang] = Tools::str2url($name);
            } elseif (!Validate::isLinkRewrite($this->link_rewrite[$id_lang])) {
                $this->link_rewrite[$id_lang] = Tools::str2url($this->link_rewrite[$id_lang]);
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function getWsProductBundle()
    {
        return Db::getInstance()->executeS('SELECT id_product_item as id, id_product_attribute_item as id_product_attribute, quantity FROM ' . _DB_PREFIX_ . 'pack WHERE id_product_pack = ' . (int) $this->id);
    }

    /**
     * @param string $type_str simple, pack, virtual
     *
     * @return bool
     */
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
        $this->product_type = $this->getDynamicProductType();

        return true;
    }

    /**
     * @param array $items
     *
     * @return bool
     */
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

    /**
     * @param int $id_attribute Attribute identifier
     * @param int $id_shop Shop identifier
     *
     * @return string Attribute identifier
     */
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

    /**
     * @param int $id_product Product identifier
     * @param int $pack_stock_type value of Pack stock type, see constants defined in Pack class
     *
     * @return bool
     */
    public static function setPackStockType($id_product, $pack_stock_type)
    {
        return Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'product p
        ' . Shop::addSqlAssociation('product', 'p') . ' SET product_shop.pack_stock_type = ' . (int) $pack_stock_type . ' WHERE p.`id_product` = ' . (int) $id_product);
    }

    /**
     * Gets a list of IDs from a list of IDs/Refs. The result will avoid duplicates, and checks if given IDs/Refs exists in DB.
     * Useful when a product list should be checked before a bulk operation on them (Only 1 query => performances).
     *
     * @param int|string|int[]|string[] $ids_or_refs Product identifier(s) or reference(s)
     *
     * @return array|false Product identifiers, without duplicate and only existing ones
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
     * @return string|false category, product, false if unknown redirect_type
     */
    public function getRedirectType()
    {
        switch ($this->redirect_type) {
            case RedirectType::TYPE_CATEGORY_PERMANENT:
            case RedirectType::TYPE_CATEGORY_TEMPORARY:
                return 'category';

            case RedirectType::TYPE_PRODUCT_PERMANENT:
            case RedirectType::TYPE_PRODUCT_TEMPORARY:
                return 'product';
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
        $updateQuery = 'UPDATE `' . _DB_PREFIX_ . 'customization_field` cf
            SET cf.`is_deleted` = 1
            WHERE
            cf.`id_product` = ' . (int) $this->id . '
            AND cf.`is_deleted` = 0 ';

        if (is_array($customizationIds) && !empty($customizationIds)) {
            $updateQuery .= 'AND cf.`id_customization_field` NOT IN (' . implode(',', array_map('intval', $customizationIds)) . ')';
        }

        $return = Db::getInstance()->execute($updateQuery);

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

    /**
     * Get Product ecotax
     *
     * @param int $precision
     * @param bool $include_tax
     * @param bool $formated
     *
     * @return string|float
     */
    public function getEcotax($precision = null, $include_tax = true, $formated = false)
    {
        $context = Context::getContext();
        $currency = $context->currency;
        $precision = $precision ?? $currency->precision;
        $ecotax_rate = $include_tax ? (float) Tax::getProductEcotaxRate() : 0;
        $ecotax = Tools::ps_round(
            (float) $this->ecotax * (1 + $ecotax_rate / 100),
            $precision,
            null
        );

        return $formated ? $context->getCurrentLocale()->formatPrice($ecotax, $currency->iso_code) : $ecotax;
    }

    /**
     * @return string
     */
    public function getProductType(): string
    {
        // Default value is the one saved, but in case it is not set we use dynamic definition
        if (!empty($this->product_type) && in_array($this->product_type, ProductType::AVAILABLE_TYPES)) {
            return $this->product_type;
        }

        return $this->getDynamicProductType();
    }

    /**
     * Returns product type based on existing associations without taking the saved value
     * in database into account.
     *
     * @return string
     */
    public function getDynamicProductType(): string
    {
        if ($this->is_virtual) {
            return ProductType::TYPE_VIRTUAL;
        } elseif (Pack::isPack($this->id)) {
            return ProductType::TYPE_PACK;
        } elseif ($this->hasCombinations()) {
            return ProductType::TYPE_COMBINATIONS;
        }

        return ProductType::TYPE_STANDARD;
    }

    /**
     * Checks if product is still associated to its default shop, if not update with the first association found.
     */
    protected function updateDefaultShop(): void
    {
        $hasDefaultShopAssociation = Db::getInstance()->getValue(
            'SELECT COUNT(p.id_product) FROM `' . _DB_PREFIX_ . 'product_shop` ps
                     LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON p.`id_product` = ps.`id_product` AND ps.`id_shop` = p.`id_shop_default`
                     WHERE p.`id_product` = ' . (int) $this->id
        );

        if (!$hasDefaultShopAssociation) {
            // Update default shop if needed, use the first associated shop (based on its ID) as the default fallback
            $firstAssociatedShop = (int) Db::getInstance()->getValue('SELECT ps.`id_shop` AS id_shop FROM `' . _DB_PREFIX_ . 'product_shop` ps WHERE ps.`id_product` = ' . (int) $this->id . ' ORDER BY ps.`id_shop` ASC');
            Db::getInstance()->update('product', [
                'id_shop_default' => $firstAssociatedShop,
            ], 'id_product = ' . (int) $this->id);
            $this->id_shop_default = $firstAssociatedShop;
        }
    }
}
