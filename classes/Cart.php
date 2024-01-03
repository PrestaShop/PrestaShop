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
use PrestaShop\PrestaShop\Adapter\AddressFactory;
use PrestaShop\PrestaShop\Adapter\Cache\CacheAdapter;
use PrestaShop\PrestaShop\Adapter\Customer\CustomerDataProvider;
use PrestaShop\PrestaShop\Adapter\Database;
use PrestaShop\PrestaShop\Adapter\Group\GroupDataProvider;
use PrestaShop\PrestaShop\Adapter\Product\PriceCalculator;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Core\Cart\Calculator;
use PrestaShop\PrestaShop\Core\Cart\CartRow;
use PrestaShop\PrestaShop\Core\Cart\CartRuleData;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;

class CartCore extends ObjectModel
{
    /** @var int|null */
    public $id;

    public $id_shop_group;

    public $id_shop;

    /** @var int|null Customer delivery address ID */
    public $id_address_delivery;

    /** @var int|null Customer invoicing address ID */
    public $id_address_invoice;

    /** @var int Customer currency ID */
    public $id_currency;

    /** @var int Customer ID */
    public $id_customer;

    /** @var int Guest ID */
    public $id_guest;

    /** @var int Language ID */
    public $id_lang;

    /** @var bool True if the customer wants a recycled package */
    public $recyclable = false;

    /** @var bool True if the customer wants a gift wrapping */
    public $gift = false;

    /** @var string Gift message if specified */
    public $gift_message;

    /**
     * @deprecated since 9.0.0 - This functionality was disabled. Attribute will be completely removed
     * in the next major. There is no replacement, all clients should have the same experience.
     *
     * @var bool Mobile Theme */
    public $mobile_theme = false;

    /** @var string Object creation date */
    public $date_add;

    /** @var string secure_key */
    public $secure_key;

    /** @var int Carrier ID */
    public $id_carrier = 0;

    /** @var string Object last modification date */
    public $date_upd;

    public $checkedTos = false;
    public $pictures;
    public $textFields;

    public $delivery_option;

    /** @var bool Allow to seperate order in multiple package in order to recieve as soon as possible the available products */
    public $allow_seperated_package = false;

    protected static $_nbProducts = [];
    protected static $_isVirtualCart = [];

    protected $_products = null;
    protected static $_totalWeight = [];
    protected $_taxCalculationMethod = PS_TAX_EXC;
    protected static $_carriers = null;
    protected static $_taxes_rate = null;
    protected static $_attributesLists = [];

    /** @var Customer|null */
    protected static $_customer = null;

    protected static $cacheDeliveryOption = [];
    protected static $cacheNbPackages = [];
    protected static $cachePackageList = [];
    protected static $cacheDeliveryOptionList = [];

    /**
     * @deprecated Since 9.0 and will be removed in 10.0
     */
    protected static $cacheMultiAddressDelivery = [];

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'cart',
        'primary' => 'id_cart',
        'fields' => [
            'id_shop_group' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_address_delivery' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_address_invoice' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_carrier' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_currency' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_customer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_guest' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_lang' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'recyclable' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'gift' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'gift_message' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'],
            'mobile_theme' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'delivery_option' => ['type' => self::TYPE_STRING],
            'secure_key' => ['type' => self::TYPE_STRING, 'size' => 32],
            'allow_seperated_package' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    /** @var array Web service parameters */
    protected $webserviceParameters = [
        'fields' => [
            'id_address_delivery' => ['xlink_resource' => 'addresses'],
            'id_address_invoice' => ['xlink_resource' => 'addresses'],
            'id_currency' => ['xlink_resource' => 'currencies'],
            'id_customer' => ['xlink_resource' => 'customers'],
            'id_guest' => ['xlink_resource' => 'guests'],
            'id_lang' => ['xlink_resource' => 'languages'],
        ],
        'associations' => [
            'cart_rows' => [
                'resource' => 'cart_row',
                'virtual_entity' => true,
                'fields' => [
                    'id_product' => ['required' => true, 'xlink_resource' => 'products'],
                    'id_product_attribute' => ['required' => true, 'xlink_resource' => 'combinations'],
                    'id_address_delivery' => ['required' => true, 'xlink_resource' => 'addresses'],
                    'id_customization' => ['required' => false, 'xlink_resource' => 'customizations'],
                    'quantity' => ['required' => true],
                ],
            ],
        ],
    ];

    protected $configuration;

    protected $addressFactory;

    protected $shouldSplitGiftProductsQuantity = false;

    protected $shouldExcludeGiftsDiscount = false;

    public const ONLY_PRODUCTS = 1;
    public const ONLY_DISCOUNTS = 2;
    public const BOTH = 3;
    public const BOTH_WITHOUT_SHIPPING = 4;
    public const ONLY_SHIPPING = 5;
    public const ONLY_WRAPPING = 6;
    public const ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING = 8;

    private const DEFAULT_ATTRIBUTES_KEYS = ['attributes' => '', 'attributes_small' => ''];

    /**
     * CartCore constructor.
     *
     * @param int|null $id Cart ID
     *                     null = new Cart
     * @param int|null $idLang Language ID
     *                         null = Language ID of current Context
     */
    public function __construct($id = null, $idLang = null)
    {
        parent::__construct($id);

        if (null !== $idLang) {
            $this->id_lang = (int) (Language::getLanguage($idLang) !== false) ? $idLang : Configuration::get('PS_LANG_DEFAULT');
        }

        if ($this->id_customer) {
            if (isset(Context::getContext()->customer) && Context::getContext()->customer->id == $this->id_customer) {
                $customer = Context::getContext()->customer;
            } else {
                $customer = new Customer((int) $this->id_customer);
            }

            Cart::$_customer = $customer;

            if ((!$this->secure_key || $this->secure_key == '-1') && $customer->secure_key) {
                $this->secure_key = $customer->secure_key;
                $this->save();
            }
        }

        $this->setTaxCalculationMethod();

        $this->configuration = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Core\\ConfigurationInterface');
        $this->addressFactory = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\AddressFactory');
    }

    public static function resetStaticCache()
    {
        static::$_nbProducts = [];
        static::$_isVirtualCart = [];
        static::$_totalWeight = [];
        static::$_carriers = null;
        static::$_taxes_rate = null;
        static::$_attributesLists = [];
        static::$_customer = null;
        static::$cacheDeliveryOption = [];
        static::$cacheNbPackages = [];
        static::$cachePackageList = [];
        static::$cacheDeliveryOptionList = [];
        static::$cacheMultiAddressDelivery = [];
    }

    /**
     * Set Tax calculation method.
     */
    public function setTaxCalculationMethod()
    {
        $this->_taxCalculationMethod = Group::getPriceDisplayMethod(Group::getCurrent()->id);
    }

    /**
     * Adds current Cart as a new Object to the database.
     *
     * @param bool $autoDate Automatically set `date_upd` and `date_add` columns
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Whether the Cart has been successfully added
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function add($autoDate = true, $nullValues = false)
    {
        if (!$this->id_lang) {
            $this->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        }
        if (!$this->id_shop) {
            $this->id_shop = Context::getContext()->shop->id;
        }
        if (!$this->id_shop_group) {
            $this->id_shop_group = Context::getContext()->shop->id_shop_group;
        }

        $return = parent::add($autoDate, $nullValues);
        Hook::exec('actionCartSave', ['cart' => $this]);

        return $return;
    }

    /**
     * Updates the current object in the database.
     *
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Whether the Cart has been successfully updated
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function update($nullValues = false)
    {
        if (isset(self::$_nbProducts[$this->id])) {
            unset(self::$_nbProducts[$this->id]);
        }

        if (isset(self::$_totalWeight[$this->id])) {
            unset(self::$_totalWeight[$this->id]);
        }

        $this->_products = null;
        $return = parent::update($nullValues);
        Hook::exec('actionCartSave', ['cart' => $this]);

        return $return;
    }

    /**
     * Update the Address ID of the Cart.
     *
     * @param int $id_address Current Address ID to change
     * @param int $id_address_new New Address ID
     */
    public function updateAddressId($id_address, $id_address_new)
    {
        $to_update = false;
        if (!isset($this->id_address_invoice) || $this->id_address_invoice == $id_address) {
            $to_update = true;
            $this->id_address_invoice = $id_address_new;
        }
        if (!isset($this->id_address_delivery) || $this->id_address_delivery == $id_address) {
            $to_update = true;
            $this->id_address_delivery = $id_address_new;
        }
        if ($to_update) {
            $this->update();
        }

        Hook::exec('actionUpdateCartAddress', ['cart' => $this, 'oldAddressId' => (int) $id_address, 'newAddressId' => (int) $id_address_new]);
    }

    /**
     * Update the Delivery Address ID of the Cart.
     *
     * @param int $currentAddressId Current Address ID to change
     * @param int $newAddressId New Address ID
     */
    public function updateDeliveryAddressId(int $currentAddressId, int $newAddressId)
    {
        if (!isset($this->id_address_delivery) || (int) $this->id_address_delivery === $currentAddressId) {
            $this->id_address_delivery = $newAddressId;
            $this->update();
        }

        Hook::exec('actionUpdateCartAddress', ['cart' => $this, 'oldAddressId' => $currentAddressId, 'newAddressId' => $newAddressId]);
    }

    /**
     * Deletes current Cart from the database.
     *
     * @return bool True if delete was successful
     *
     * @throws PrestaShopException
     */
    public function delete()
    {
        if ($this->orderExists()) { //NOT delete a cart which is associated with an order
            return false;
        }

        // Get all file customization fields from customized_data table and delete the physical file
        $uploaded_files = Db::getInstance()->executeS(
            'SELECT cd.`value`
            FROM `' . _DB_PREFIX_ . 'customized_data` cd
            INNER JOIN `' . _DB_PREFIX_ . 'customization` c ON (cd.`id_customization`= c.`id_customization`)
            WHERE cd.`type`= ' . (int) Product::CUSTOMIZE_FILE . ' AND c.`id_cart`=' . (int) $this->id
        );

        foreach ($uploaded_files as $must_unlink) {
            unlink(_PS_UPLOAD_DIR_ . $must_unlink['value'] . '_small');
            unlink(_PS_UPLOAD_DIR_ . $must_unlink['value']);
        }

        // Delete all related customized data
        Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'customized_data`
            WHERE `id_customization` IN (
                SELECT `id_customization`
                FROM `' . _DB_PREFIX_ . 'customization`
                WHERE `id_cart`=' . (int) $this->id . '
            )'
        );

        // Delete all customization entries (1 customization can have multiple customized_data)
        Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'customization`
            WHERE `id_cart` = ' . (int) $this->id
        );

        // Delete products, delete cart rules
        if (!Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'cart_cart_rule` WHERE `id_cart` = ' . (int) $this->id)
            || !Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'cart_product` WHERE `id_cart` = ' . (int) $this->id)) {
            return false;
        }

        return parent::delete();
    }

    /**
     * Returns the average Tax rate for all products in the cart, as a multiplier.
     *
     * The arguments are optional and only serve as return values in case caller needs the details.
     *
     * @param float|null $cartAmountTaxExcluded If the reference is given, it will be updated with the
     *                                          total amount in the Cart excluding Taxes
     * @param float|null $cartAmountTaxIncluded If the reference is given, it will be updated with the
     *                                          total amount in the Cart including Taxes
     *
     * @return float Average Tax Rate on Products (eg. 0.2 for 20% average rate)
     */
    public function getAverageProductsTaxRate(&$cartAmountTaxExcluded = null, &$cartAmountTaxIncluded = null)
    {
        $cartAmountTaxIncluded = $this->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $cartAmountTaxExcluded = $this->getOrderTotal(false, Cart::ONLY_PRODUCTS);

        $cart_vat_amount = $cartAmountTaxIncluded - $cartAmountTaxExcluded;

        if ($cart_vat_amount == 0 || $cartAmountTaxExcluded == 0) {
            return 0;
        } else {
            return Tools::ps_round($cart_vat_amount / $cartAmountTaxExcluded, Tax::TAX_DEFAULT_PRECISION);
        }
    }

    /**
     * Get Cart Rules.
     *
     * @param int $filter Filter enum:
     *                    - FILTER_ACTION_ALL
     *                    - FILTER_ACTION_SHIPPING
     *                    - FILTER_ACTION_REDUCTION
     *                    - FILTER_ACTION_GIFT
     *                    - FILTER_ACTION_ALL_NOCAP
     * @param bool $autoAdd automaticaly adds cart ruls without code to cart
     * @param bool $useOrderPrices
     *
     * @return array|false|mysqli_result|PDOStatement|resource|null Database result
     */
    public function getCartRules($filter = CartRule::FILTER_ACTION_ALL, $autoAdd = true, $useOrderPrices = false)
    {
        // Define virtual context to prevent case where the cart is not the in the global context
        $virtual_context = Context::getContext()->cloneContext();
        /* @phpstan-ignore-next-line */
        $virtual_context->cart = $this;

        // If the cart has not been saved, then there can't be any cart rule applied
        if (!CartRule::isFeatureActive() || !$this->id) {
            return [];
        }
        if ($autoAdd) {
            CartRule::autoAddToCart($virtual_context, $useOrderPrices);
        }

        $cache_key = 'Cart::getCartRules_' . $this->id . '-' . $filter;
        if (!Cache::isStored($cache_key)) {
            $result = Db::getInstance()->executeS(
                'SELECT cr.*, crl.`id_lang`, crl.`name`, cd.`id_cart`
                FROM `' . _DB_PREFIX_ . 'cart_cart_rule` cd
                LEFT JOIN `' . _DB_PREFIX_ . 'cart_rule` cr ON cd.`id_cart_rule` = cr.`id_cart_rule`
                LEFT JOIN `' . _DB_PREFIX_ . 'cart_rule_lang` crl ON (
                    cd.`id_cart_rule` = crl.`id_cart_rule`
                    AND crl.id_lang = ' . (int) $this->getAssociatedLanguage()->getId() . '
                )
                WHERE `id_cart` = ' . (int) $this->id . '
                ' . ($filter == CartRule::FILTER_ACTION_SHIPPING ? 'AND free_shipping = 1' : '') . '
                ' . ($filter == CartRule::FILTER_ACTION_GIFT ? 'AND gift_product != 0' : '') . '
                ' . ($filter == CartRule::FILTER_ACTION_REDUCTION ? 'AND (reduction_percent != 0 OR reduction_amount != 0)' : '')
                . ' ORDER by cr.priority ASC, cr.gift_product DESC'
            );
            Cache::store($cache_key, $result);
        } else {
            $result = Cache::retrieve($cache_key);
        }

        // Define virtual context to prevent case where the cart is not the in the global context
        $virtual_context = Context::getContext()->cloneContext();
        /* @phpstan-ignore-next-line */
        $virtual_context->cart = $this;

        // set base cart total values, they will be updated and used for percentage cart rules (because percentage cart rules
        // are applied to the cart total's value after previously applied cart rules)
        $virtual_context->virtualTotalTaxExcluded = $virtual_context->cart->getOrderTotal(false, self::ONLY_PRODUCTS);
        if (!Configuration::get('PS_TAX')) {
            $virtual_context->virtualTotalTaxIncluded = $virtual_context->virtualTotalTaxExcluded;
        } else {
            $virtual_context->virtualTotalTaxIncluded = $virtual_context->cart->getOrderTotal(true, self::ONLY_PRODUCTS);
        }

        foreach ($result as &$row) {
            $row['obj'] = new CartRule($row['id_cart_rule'], (int) $this->id_lang);
            $row['value_real'] = $row['obj']->getContextualValue(true, $virtual_context, $filter);
            $row['value_tax_exc'] = $row['obj']->getContextualValue(false, $virtual_context, $filter);
            // Retro compatibility < 1.5.0.2
            $row['id_discount'] = $row['id_cart_rule'];
            $row['description'] = $row['obj']->description;
        }

        return $result;
    }

    /**
     * Get cart discounts.
     */
    public function getDiscounts()
    {
        return CartRule::getCustomerHighlightedDiscounts($this->id_lang, $this->id_customer, $this);
    }

    /**
     * Return the CartRule IDs in the Cart.
     *
     * @param int $filter Filter enum:
     *                    - FILTER_ACTION_ALL
     *                    - FILTER_ACTION_SHIPPING
     *                    - FILTER_ACTION_REDUCTION
     *                    - FILTER_ACTION_GIFT
     *                    - FILTER_ACTION_ALL_NOCAP
     *
     * @return array
     *
     * @throws PrestaShopDatabaseException
     */
    public function getOrderedCartRulesIds($filter = CartRule::FILTER_ACTION_ALL)
    {
        $cache_key = 'Cart::getOrderedCartRulesIds_' . $this->id . '-' . $filter . '-ids';
        if (!Cache::isStored($cache_key)) {
            $result = Db::getInstance()->executeS(
                'SELECT cr.`id_cart_rule`
                FROM `' . _DB_PREFIX_ . 'cart_cart_rule` cd
                LEFT JOIN `' . _DB_PREFIX_ . 'cart_rule` cr ON cd.`id_cart_rule` = cr.`id_cart_rule`
                LEFT JOIN `' . _DB_PREFIX_ . 'cart_rule_lang` crl ON (
                    cd.`id_cart_rule` = crl.`id_cart_rule`
                    AND crl.id_lang = ' . (int) $this->getAssociatedLanguage()->getId() . '
                )
                WHERE `id_cart` = ' . (int) $this->id . '
                ' . ($filter == CartRule::FILTER_ACTION_SHIPPING ? 'AND free_shipping = 1' : '') . '
                ' . ($filter == CartRule::FILTER_ACTION_GIFT ? 'AND gift_product != 0' : '') . '
                ' . ($filter == CartRule::FILTER_ACTION_REDUCTION ? 'AND (reduction_percent != 0 OR reduction_amount != 0)' : '')
                . ' ORDER BY cr.priority ASC, cr.gift_product DESC'
            );
            Cache::store($cache_key, $result);
        } else {
            $result = Cache::retrieve($cache_key);
        }

        return $result;
    }

    /**
     * Get amount of Customer Discounts.
     *
     * @param int $id_cart_rule CartRule ID
     *
     * @return int Amount of Customer Discounts
     * @todo: What are customer discounts? Isn't this just a PriceRule and shouldn't this method be renamed instead?
     */
    public function getDiscountsCustomer($id_cart_rule)
    {
        if (!CartRule::isFeatureActive()) {
            return 0;
        }
        $cache_id = 'Cart::getDiscountsCustomer_' . (int) $this->id . '-' . (int) $id_cart_rule;
        if (!Cache::isStored($cache_id)) {
            $result = (int) Db::getInstance()->getValue('
                SELECT COUNT(*)
                FROM `' . _DB_PREFIX_ . 'cart_cart_rule`
                WHERE `id_cart_rule` = ' . (int) $id_cart_rule . ' AND `id_cart` = ' . (int) $this->id);
            Cache::store($cache_id, $result);

            return $result;
        }

        return Cache::retrieve($cache_id);
    }

    /**
     * Get last Product in Cart.
     *
     * @return bool|mixed Database result
     */
    public function getLastProduct()
    {
        $sql = '
            SELECT `id_product`, `id_product_attribute`, id_shop
            FROM `' . _DB_PREFIX_ . 'cart_product`
            WHERE `id_cart` = ' . (int) $this->id . '
            ORDER BY `date_add` DESC';

        $result = Db::getInstance()->getRow($sql);
        if ($result && isset($result['id_product']) && $result['id_product']) {
            foreach ($this->getProducts(false, false, null, false) as $product) {
                if ($result['id_product'] == $product['id_product']
                    && (
                        !$result['id_product_attribute']
                        || $result['id_product_attribute'] == $product['id_product_attribute']
                    )) {
                    return $product;
                }
            }
        }

        return false;
    }

    /**
     * Return cart products.
     *
     * @param bool $refresh
     * @param bool|int $id_product
     * @param int|null $id_country
     * @param bool $fullInfos
     * @param bool $keepOrderPrices When true use the Order saved prices instead of the most recent ones from catalog (if Order exists)
     *
     * @return array Products
     */
    public function getProducts($refresh = false, $id_product = false, $id_country = null, $fullInfos = true, bool $keepOrderPrices = false)
    {
        if (!$this->id) {
            return [];
        }
        // Product cache must be strictly compared to NULL, or else an empty cart will add dozens of queries
        if ($this->_products !== null && !$refresh) {
            // Return product row with specified ID if it exists
            if (is_int($id_product)) {
                foreach ($this->_products as $product) {
                    if ($product['id_product'] == $id_product) {
                        return [$product];
                    }
                }

                return [];
            }

            return $this->_products;
        }

        // Build query
        $sql = new DbQuery();

        // Build SELECT
        $sql->select('cp.`id_product_attribute`, cp.`id_product`, cp.`quantity` AS cart_quantity, cp.id_shop, cp.`id_customization`, pl.`name`, p.`is_virtual`,
                        pl.`description_short`, pl.`available_now`, pl.`available_later`, product_shop.`id_category_default`, p.`id_supplier`,
                        p.`id_manufacturer`, m.`name` AS manufacturer_name, product_shop.`on_sale`, product_shop.`ecotax`, product_shop.`additional_shipping_cost`,
                        product_shop.`available_for_order`, product_shop.`show_price`, product_shop.`price`, product_shop.`active`, product_shop.`unity`, product_shop.`unit_price`,
                        stock.`quantity` AS quantity_available, p.`width`, p.`height`, p.`depth`, stock.`out_of_stock`, p.`weight`,
                        p.`available_date`, p.`date_add`, p.`date_upd`, IFNULL(stock.quantity, 0) as quantity, pl.`link_rewrite`, cl.`link_rewrite` AS category,
                        CONCAT(LPAD(cp.`id_product`, 10, 0), LPAD(IFNULL(cp.`id_product_attribute`, 0), 10, 0), IFNULL(cp.`id_customization`, 0)) AS unique_id,
                        ps.product_supplier_reference supplier_reference');

        // Build FROM
        $sql->from('cart_product', 'cp');

        // Build JOIN
        $sql->leftJoin('product', 'p', 'p.`id_product` = cp.`id_product`');
        $sql->innerJoin('product_shop', 'product_shop', '(product_shop.`id_shop` = cp.`id_shop` AND product_shop.`id_product` = p.`id_product`)');
        $sql->leftJoin(
            'product_lang',
            'pl',
            'p.`id_product` = pl.`id_product`
            AND pl.`id_lang` = ' . (int) $this->getAssociatedLanguage()->getId() . Shop::addSqlRestrictionOnLang('pl', 'cp.id_shop')
        );

        $sql->leftJoin(
            'category_lang',
            'cl',
            'product_shop.`id_category_default` = cl.`id_category`
            AND cl.`id_lang` = ' . (int) $this->getAssociatedLanguage()->getId() . Shop::addSqlRestrictionOnLang('cl', 'cp.id_shop')
        );

        $sql->leftJoin('product_supplier', 'ps', 'ps.`id_product` = cp.`id_product` AND ps.`id_product_attribute` = cp.`id_product_attribute` AND ps.`id_supplier` = p.`id_supplier`');
        $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');

        // @todo test if everything is ok, then refactorise call of this method
        $sql->join(Product::sqlStock('cp', 'cp'));

        // Build WHERE clauses
        $sql->where('cp.`id_cart` = ' . (int) $this->id);
        if ($id_product) {
            $sql->where('cp.`id_product` = ' . (int) $id_product);
        }
        $sql->where('p.`id_product` IS NOT NULL');

        // Build ORDER BY
        $sql->orderBy('cp.`date_add`, cp.`id_product`, cp.`id_product_attribute` ASC');

        if (Customization::isFeatureActive()) {
            $sql->select('cu.`id_customization`, cu.`quantity` AS customization_quantity');
            $sql->leftJoin(
                'customization',
                'cu',
                'p.`id_product` = cu.`id_product` AND cp.`id_product_attribute` = cu.`id_product_attribute` AND cp.`id_customization` = cu.`id_customization` AND cu.`id_cart` = ' . (int) $this->id
            );
            $sql->groupBy('cp.`id_product_attribute`, cp.`id_product`, cp.`id_shop`, cp.`id_customization`');
        } else {
            $sql->select('NULL AS customization_quantity, NULL AS id_customization');
        }

        if (Combination::isFeatureActive()) {
            $sql->select('
                product_attribute_shop.`price` AS price_attribute,
                product_attribute_shop.`ecotax` AS ecotax_attr,
                IF (IFNULL(pa.`reference`, \'\') = \'\', p.`reference`, pa.`reference`) AS reference,
                (p.`weight`+ IFNULL(product_attribute_shop.`weight`, pa.`weight`)) weight_attribute,
                IF (IFNULL(pa.`ean13`, \'\') = \'\', p.`ean13`, pa.`ean13`) AS ean13,
                IF (IFNULL(pa.`isbn`, \'\') = \'\', p.`isbn`, pa.`isbn`) AS isbn,
                IF (IFNULL(pa.`upc`, \'\') = \'\', p.`upc`, pa.`upc`) AS upc,
                IF (IFNULL(pa.`mpn`, \'\') = \'\', p.`mpn`, pa.`mpn`) AS mpn,
                IFNULL(product_attribute_shop.`minimal_quantity`, product_shop.`minimal_quantity`) as minimal_quantity,
                IF(product_attribute_shop.wholesale_price > 0,  product_attribute_shop.wholesale_price, product_shop.`wholesale_price`) wholesale_price
            ');

            $sql->leftJoin('product_attribute', 'pa', 'pa.`id_product_attribute` = cp.`id_product_attribute`');
            $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', '(product_attribute_shop.`id_shop` = cp.`id_shop` AND product_attribute_shop.`id_product_attribute` = pa.`id_product_attribute`)');
        } else {
            $sql->select(
                'p.`reference` AS reference, p.`ean13`, p.`isbn`,
                p.`upc` AS upc, p.`mpn` AS mpn, product_shop.`minimal_quantity` AS minimal_quantity, product_shop.`wholesale_price` wholesale_price'
            );
        }

        $sql->select('image_shop.`id_image` id_image, il.`legend`');
        $sql->leftJoin('image_shop', 'image_shop', 'image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $this->id_shop);
        $sql->leftJoin('image_lang', 'il', 'il.`id_image` = image_shop.`id_image` AND il.`id_lang` = ' . (int) $this->getAssociatedLanguage()->getId());

        /** @var array<string, mixed>|false $products */
        $products = Db::getInstance()->executeS($sql);

        // Reset the cache before the following return, or else an empty cart will add dozens of queries
        $products_ids = [];
        $pa_ids = [];
        if (is_iterable($products)) {
            foreach ($products as $key => $product) {
                $products_ids[] = $product['id_product'];
                $pa_ids[] = $product['id_product_attribute'];
                $specific_price = SpecificPrice::getSpecificPrice($product['id_product'], $this->id_shop, $this->id_currency, $id_country, $this->id_shop_group, $product['cart_quantity'], $product['id_product_attribute'], $this->id_customer, $this->id);
                if ($specific_price) {
                    $reduction_type_row = ['reduction_type' => $specific_price['reduction_type']];
                } else {
                    $reduction_type_row = ['reduction_type' => 0];
                }

                $products[$key] = array_merge($product, $reduction_type_row);
            }
        }
        // Thus you can avoid one query per product, because there will be only one query for all the products of the cart
        Product::cacheProductsFeatures($products_ids);
        Cart::cacheSomeAttributesLists($pa_ids, (int) $this->getAssociatedLanguage()->getId());

        if (empty($products)) {
            $this->_products = [];

            return [];
        }

        if ($fullInfos) {
            $cart_shop_context = Context::getContext()->cloneContext();

            $givenAwayProductsIds = [];

            if ($this->shouldSplitGiftProductsQuantity && $refresh) {
                $gifts = $this->getCartRules(CartRule::FILTER_ACTION_GIFT, false);
                if (count($gifts) > 0) {
                    foreach ($gifts as $gift) {
                        foreach ($products as $rowIndex => $product) {
                            if (!array_key_exists('is_gift', $products[$rowIndex])) {
                                $products[$rowIndex]['is_gift'] = false;
                            }

                            if (
                                $product['id_product'] == $gift['gift_product'] &&
                                $product['id_product_attribute'] == $gift['gift_product_attribute'] &&
                                empty($product['id_customization'])
                            ) {
                                $product['is_gift'] = true;
                                $products[$rowIndex] = $product;
                            }
                        }

                        $index = $gift['gift_product'] . '-' . $gift['gift_product_attribute'];
                        if (!array_key_exists($index, $givenAwayProductsIds)) {
                            $givenAwayProductsIds[$index] = 1;
                        } else {
                            ++$givenAwayProductsIds[$index];
                        }
                    }
                }
            }

            $this->_products = [];

            foreach ($products as &$product) {
                if (!array_key_exists('is_gift', $product)) {
                    $product['is_gift'] = false;
                }

                $props = Product::getProductProperties((int) $this->id_lang, $product);
                $product['reduction'] = $props['reduction'];
                $product['reduction_without_tax'] = $props['reduction_without_tax'];
                $product['price_without_reduction'] = $props['price_without_reduction'];
                $product['specific_prices'] = $props['specific_prices'];
                $product['unit_price_ratio'] = $props['unit_price_ratio'];
                $product['unit_price'] = $product['unit_price_tax_excluded'] = $props['unit_price_tax_excluded'];
                $product['unit_price_tax_included'] = $props['unit_price_tax_included'];
                unset($props);

                $givenAwayQuantity = 0;
                $giftIndex = $product['id_product'] . '-' . $product['id_product_attribute'];
                if ($product['is_gift'] && array_key_exists($giftIndex, $givenAwayProductsIds)) {
                    $givenAwayQuantity = $givenAwayProductsIds[$giftIndex];
                }

                if (!$product['is_gift'] || (int) $product['cart_quantity'] === $givenAwayQuantity) {
                    $product = $this->applyProductCalculations($product, $cart_shop_context, null, $keepOrderPrices);
                } else {
                    // Separate products given away from those manually added to cart
                    $this->_products[] = $this->applyProductCalculations($product, $cart_shop_context, $givenAwayQuantity, $keepOrderPrices);
                    unset($product['is_gift']);
                    $product = $this->applyProductCalculations(
                        $product,
                        $cart_shop_context,
                        $product['cart_quantity'] - $givenAwayQuantity,
                        $keepOrderPrices
                    );
                }

                $this->_products[] = $product;
            }
        } else {
            $this->_products = $products;
        }

        return $this->_products;
    }

    /**
     * @param array $row
     * @param Context $shopContext
     * @param int|null $productQuantity
     * @param bool $keepOrderPrices When true use the Order saved prices instead of the most recent ones from catalog (if Order exists)
     *
     * @return mixed
     */
    protected function applyProductCalculations($row, $shopContext, $productQuantity = null, bool $keepOrderPrices = false)
    {
        if (null === $productQuantity) {
            $productQuantity = (int) $row['cart_quantity'];
        }

        if (isset($row['ecotax_attr']) && $row['ecotax_attr'] > 0) {
            $row['ecotax'] = (float) $row['ecotax_attr'];
        }

        $row['stock_quantity'] = (int) $row['quantity'];
        // for compatibility with 1.2 themes
        $row['quantity'] = $productQuantity;

        // get the customization weight impact
        $customization_weight = Customization::getCustomizationWeight($row['id_customization']);

        if (isset($row['id_product_attribute']) && (int) $row['id_product_attribute'] && isset($row['weight_attribute'])) {
            $row['weight_attribute'] += $customization_weight;
            $row['weight'] = (float) $row['weight_attribute'];
        } else {
            $row['weight'] += $customization_weight;
        }

        if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
            $address_id = (int) $this->id_address_invoice;
        } else {
            $address_id = (int) $this->id_address_delivery;
        }
        if (!Address::addressExists($address_id, true)) {
            $address_id = null;
        }

        if ($shopContext->shop->id != $row['id_shop']) {
            $shopContext->shop = new Shop((int) $row['id_shop']);
        }

        $specific_price_output = null;
        // Specify the orderId if needed so that Product::getPriceStatic returns the prices saved in OrderDetails
        $orderId = null;
        if ($keepOrderPrices) {
            $orderId = Order::getIdByCartId($this->id);
            $orderId = (int) $orderId ?: null;
        }

        if (!empty($orderId)) {
            $orderPrices = $this->getOrderPrices($row, $orderId, $productQuantity, $address_id, $shopContext, $specific_price_output);
            $row = array_merge($row, $orderPrices);
        } else {
            $cartPrices = $this->getCartPrices($row, $productQuantity, $address_id, $shopContext, $specific_price_output);
            $row = array_merge($row, $cartPrices);
        }

        switch (Configuration::get('PS_ROUND_TYPE')) {
            case Order::ROUND_TOTAL:
                $row['total'] = $row['price_with_reduction_without_tax'] * $productQuantity;
                $row['total_wt'] = $row['price_with_reduction'] * $productQuantity;

                break;
            case Order::ROUND_LINE:
                $row['total'] = Tools::ps_round(
                    $row['price_with_reduction_without_tax'] * $productQuantity,
                    Context::getContext()->getComputingPrecision()
                );
                $row['total_wt'] = Tools::ps_round(
                    $row['price_with_reduction'] * $productQuantity,
                    Context::getContext()->getComputingPrecision()
                );

                break;

            case Order::ROUND_ITEM:
            default:
                $row['total'] = Tools::ps_round(
                    $row['price_with_reduction_without_tax'],
                    Context::getContext()->getComputingPrecision()
                ) * $productQuantity;
                $row['total_wt'] = Tools::ps_round(
                    $row['price_with_reduction'],
                    Context::getContext()->getComputingPrecision()
                ) * $productQuantity;

                break;
        }

        // Update unit price in case cart reductions happened
        $row['unit_price'] = $row['unit_price_tax_excluded'] = $row['unit_price_ratio'] != 0 ? $row['price_with_reduction_without_tax'] / $row['unit_price_ratio'] : 0.0;
        $row['unit_price_tax_included'] = $row['unit_price_ratio'] != 0 ? $row['price_with_reduction'] / $row['unit_price_ratio'] : 0.0;

        $row['price_wt'] = $row['price_with_reduction'];
        $row['description_short'] = Tools::nl2br($row['description_short']);

        // check if a image associated with the attribute exists
        if ($row['id_product_attribute']) {
            $row2 = Image::getBestImageAttribute($row['id_shop'], $this->getAssociatedLanguage()->getId(), $row['id_product'], $row['id_product_attribute']);
            if ($row2) {
                $row = array_merge($row, $row2);
            }
        }

        $row['reduction_applies'] = ($specific_price_output && (float) $specific_price_output['reduction']);
        $row['quantity_discount_applies'] = ($specific_price_output && $productQuantity >= (int) $specific_price_output['from_quantity']);
        $row['id_image'] = Product::defineProductImage($row, $this->getAssociatedLanguage()->getId());
        $row['allow_oosp'] = Product::isAvailableWhenOutOfStock($row['out_of_stock']);
        $row['features'] = Product::getFeaturesStatic((int) $row['id_product']);

        $productAttributeKey = $row['id_product_attribute'] . '-' . $this->getAssociatedLanguage()->getId();
        $row = array_merge(
            $row,
            self::$_attributesLists[$productAttributeKey] ?? self::DEFAULT_ATTRIBUTES_KEYS
        );

        return Product::getTaxesInformations($row, $shopContext);
    }

    /**
     * @param array $productRow
     * @param int $productQuantity
     * @param int|null $addressId Customer's address id (for tax calculation)
     * @param Context $shopContext
     * @param array|false|null $specificPriceOutput
     *
     * @return array
     */
    private function getCartPrices(
        array $productRow,
        int $productQuantity,
        ?int $addressId,
        Context $shopContext,
        &$specificPriceOutput
    ): array {
        $cartPrices = [];
        $cartPrices['price_without_reduction'] = $this->getCartPriceFromCatalog(
            (int) $productRow['id_product'],
            isset($productRow['id_product_attribute']) ? (int) $productRow['id_product_attribute'] : null,
            (int) $productRow['id_customization'],
            true,
            false,
            true,
            $productQuantity,
            $addressId,
            $shopContext,
            $specificPriceOutput
        );

        $cartPrices['price_without_reduction_without_tax'] = $this->getCartPriceFromCatalog(
            (int) $productRow['id_product'],
            isset($productRow['id_product_attribute']) ? (int) $productRow['id_product_attribute'] : null,
            (int) $productRow['id_customization'],
            false,
            false,
            true,
            $productQuantity,
            $addressId,
            $shopContext,
            $specificPriceOutput
        );

        $cartPrices['price_with_reduction'] = $this->getCartPriceFromCatalog(
            (int) $productRow['id_product'],
            isset($productRow['id_product_attribute']) ? (int) $productRow['id_product_attribute'] : null,
            (int) $productRow['id_customization'],
            true,
            true,
            true,
            $productQuantity,
            $addressId,
            $shopContext,
            $specificPriceOutput
        );

        $cartPrices['price'] = $cartPrices['price_with_reduction_without_tax'] = $this->getCartPriceFromCatalog(
            (int) $productRow['id_product'],
            isset($productRow['id_product_attribute']) ? (int) $productRow['id_product_attribute'] : null,
            (int) $productRow['id_customization'],
            false,
            true,
            true,
            $productQuantity,
            $addressId,
            $shopContext,
            $specificPriceOutput
        );

        return $cartPrices;
    }

    /**
     * @param int $productId
     * @param int $combinationId
     * @param int $customizationId
     * @param bool $withTaxes
     * @param bool $useReduction
     * @param bool $withEcoTax
     * @param int $productQuantity
     * @param int|null $addressId Customer's address id (for tax calculation)
     * @param Context $shopContext
     * @param array|false|null $specificPriceOutput
     *
     * @return float|null
     */
    private function getCartPriceFromCatalog(
        int $productId,
        int $combinationId,
        int $customizationId,
        bool $withTaxes,
        bool $useReduction,
        bool $withEcoTax,
        int $productQuantity,
        ?int $addressId,
        Context $shopContext,
        &$specificPriceOutput
    ): ?float {
        return Product::getPriceStatic(
            $productId,
            $withTaxes,
            $combinationId,
            6,
            null,
            false,
            $useReduction,
            $productQuantity,
            false,
            (int) $this->id_customer ? (int) $this->id_customer : null,
            (int) $this->id,
            $addressId,
            $specificPriceOutput,
            $withEcoTax,
            true,
            $shopContext,
            true,
            $customizationId
        );
    }

    /**
     * @param array $productRow
     * @param int $orderId
     * @param int $productQuantity
     * @param int|null $addressId Customer's address id (for tax calculation)
     * @param Context $shopContext
     * @param array|false|null $specificPriceOutput
     *
     * @return array
     */
    private function getOrderPrices(
        array $productRow,
        int $orderId,
        int $productQuantity,
        ?int $addressId,
        Context $shopContext,
        &$specificPriceOutput
    ): array {
        $orderPrices = [];
        $orderPrices['price_without_reduction'] = Product::getPriceFromOrder(
            $orderId,
            (int) $productRow['id_product'],
            isset($productRow['id_product_attribute']) ? (int) $productRow['id_product_attribute'] : 0,
            true,
            false,
            true
        );

        $orderPrices['price_without_reduction_without_tax'] = Product::getPriceFromOrder(
            $orderId,
            (int) $productRow['id_product'],
            isset($productRow['id_product_attribute']) ? (int) $productRow['id_product_attribute'] : 0,
            false,
            false,
            true
        );

        $orderPrices['price_with_reduction'] = Product::getPriceFromOrder(
            $orderId,
            (int) $productRow['id_product'],
            isset($productRow['id_product_attribute']) ? (int) $productRow['id_product_attribute'] : 0,
            true,
            true,
            true
        );

        $orderPrices['price'] = $orderPrices['price_with_reduction_without_tax'] = Product::getPriceFromOrder(
            $orderId,
            (int) $productRow['id_product'],
            isset($productRow['id_product_attribute']) ? (int) $productRow['id_product_attribute'] : 0,
            false,
            true,
            true
        );

        // If the product price was not found in the order, use cart prices as fallback
        if (false !== array_search(null, $orderPrices)) {
            $cartPrices = $this->getCartPrices(
                $productRow,
                $productQuantity,
                $addressId,
                $shopContext,
                $specificPriceOutput
            );
            foreach ($orderPrices as $orderPrice => $value) {
                if (null === $value) {
                    $orderPrices[$orderPrice] = $cartPrices[$orderPrice];
                }
            }
        }

        return $orderPrices;
    }

    public static function cacheSomeAttributesLists($ipa_list, $id_lang)
    {
        if (!Combination::isFeatureActive()) {
            return;
        }

        $pa_implode = [];
        $separator = Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR');

        if ($separator === '-') {
            // Add a space before the dash between attributes
            $separator = ' -';
        }

        foreach ($ipa_list as $id_product_attribute) {
            if ((int) $id_product_attribute && !array_key_exists($id_product_attribute . '-' . $id_lang, self::$_attributesLists)) {
                $pa_implode[] = (int) $id_product_attribute;
                self::$_attributesLists[(int) $id_product_attribute . '-' . $id_lang] = self::DEFAULT_ATTRIBUTES_KEYS;
            }
        }

        if (!count($pa_implode)) {
            return;
        }

        $result = Db::getInstance()->executeS(
            'SELECT pac.`id_product_attribute`, agl.`public_name` AS public_group_name, al.`name` AS attribute_name
            FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (
                a.`id_attribute` = al.`id_attribute`
                AND al.`id_lang` = ' . (int) $id_lang . '
            )
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (
                ag.`id_attribute_group` = agl.`id_attribute_group`
                AND agl.`id_lang` = ' . (int) $id_lang . '
            )
            WHERE pac.`id_product_attribute` IN (' . implode(',', $pa_implode) . ')
            ORDER BY ag.`position` ASC, a.`position` ASC'
        );

        $colon = Context::getContext()->getTranslator()->trans(': ', [], 'Shop.Pdf');
        foreach ($result as $row) {
            $key = $row['id_product_attribute'] . '-' . $id_lang;
            self::$_attributesLists[$key]['attributes'] .= $row['public_group_name'] . $colon . $row['attribute_name'] . $separator . ' ';
            self::$_attributesLists[$key]['attributes_small'] .= $row['attribute_name'] . $separator . ' ';
        }

        foreach ($pa_implode as $id_product_attribute) {
            self::$_attributesLists[$id_product_attribute . '-' . $id_lang]['attributes'] = rtrim(
                self::$_attributesLists[$id_product_attribute . '-' . $id_lang]['attributes'],
                $separator . ' '
            );

            self::$_attributesLists[$id_product_attribute . '-' . $id_lang]['attributes_small'] = rtrim(
                self::$_attributesLists[$id_product_attribute . '-' . $id_lang]['attributes_small'],
                $separator . ' '
            );
        }
    }

    /**
     * Check if Addresses in the Cart are still valid and update with the next valid Address ID found.
     *
     * @return bool Whether the Addresses have been succesfully checked and upated
     */
    public function checkAndUpdateAddresses()
    {
        $needUpdate = false;
        foreach (['invoice', 'delivery'] as $type) {
            $addr = 'id_address_' . $type;
            if ($this->{$addr} != 0
                && !Address::isValid($this->{$addr})) {
                $this->{$addr} = 0;
                $needUpdate = true;
            }
        }

        if ($needUpdate && $this->id) {
            return $this->update();
        }

        return true;
    }

    /**
     * Return cart products quantity.
     *
     * @result integer Products quantity
     */
    public function nbProducts()
    {
        if (!$this->id) {
            return 0;
        }

        return Cart::getNbProducts($this->id);
    }

    /**
     * Get number of products in cart
     * This is the total amount of products, not just the types.
     *
     * @param int $id Cart ID
     *
     * @return mixed
     */
    public static function getNbProducts($id)
    {
        // Must be strictly compared to NULL, or else an empty cart will bypass the cache and add dozens of queries
        if (isset(self::$_nbProducts[$id]) && self::$_nbProducts[$id] !== null) {
            return self::$_nbProducts[$id];
        }

        self::$_nbProducts[$id] = (int) Db::getInstance()->getValue(
            'SELECT SUM(`quantity`)
            FROM `' . _DB_PREFIX_ . 'cart_product`
            WHERE `id_cart` = ' . (int) $id
        );

        return self::$_nbProducts[$id];
    }

    /**
     * Add a CartRule to the Cart.
     *
     * @param int $id_cart_rule CartRule ID
     * @param bool $useOrderPrices
     *
     * @return bool Whether the CartRule has been successfully added
     */
    public function addCartRule($id_cart_rule, bool $useOrderPrices = false)
    {
        // You can't add a cart rule that does not exist
        $cartRule = new CartRule($id_cart_rule, Context::getContext()->language->id);

        if (!Validate::isLoadedObject($cartRule)) {
            return false;
        }

        if (Db::getInstance()->getValue('SELECT id_cart_rule FROM ' . _DB_PREFIX_ . 'cart_cart_rule WHERE id_cart_rule = ' . (int) $id_cart_rule . ' AND id_cart = ' . (int) $this->id)) {
            return false;
        }

        // Add the cart rule to the cart
        if (!Db::getInstance()->insert('cart_cart_rule', [
            'id_cart_rule' => (int) $id_cart_rule,
            'id_cart' => (int) $this->id,
        ])) {
            return false;
        }

        Cache::clean('Cart::getCartRules_' . $this->id . '-' . CartRule::FILTER_ACTION_ALL);
        Cache::clean('Cart::getCartRules_' . $this->id . '-' . CartRule::FILTER_ACTION_SHIPPING);
        Cache::clean('Cart::getCartRules_' . $this->id . '-' . CartRule::FILTER_ACTION_REDUCTION);
        Cache::clean('Cart::getCartRules_' . $this->id . '-' . CartRule::FILTER_ACTION_GIFT);

        Cache::clean('Cart::getOrderedCartRulesIds_' . $this->id . '-' . CartRule::FILTER_ACTION_ALL . '-ids');
        Cache::clean('Cart::getOrderedCartRulesIds_' . $this->id . '-' . CartRule::FILTER_ACTION_SHIPPING . '-ids');
        Cache::clean('Cart::getOrderedCartRulesIds_' . $this->id . '-' . CartRule::FILTER_ACTION_REDUCTION . '-ids');
        Cache::clean('Cart::getOrderedCartRulesIds_' . $this->id . '-' . CartRule::FILTER_ACTION_GIFT . '-ids');
        Cache::clean('getContextualValue_*');

        if ((int) $cartRule->gift_product) {
            $this->updateQty(
                1,
                $cartRule->gift_product,
                $cartRule->gift_product_attribute,
                false,
                'up',
                0,
                null,
                false,
                false,
                true,
                $useOrderPrices
            );
        }

        return true;
    }

    /**
     * Check if the Cart contains the given Product (Attribute).
     *
     * @param int $idProduct Product ID
     * @param int $idProductAttribute ProductAttribute ID
     * @param int|bool $idCustomization Customization ID
     * @param int $idAddressDelivery Delivery Address ID
     *
     * @return array quantity index     : number of product in cart without counting those of pack in cart
     *               deep_quantity index: number of product in cart counting those of pack in cart
     */
    public function getProductQuantity($idProduct, $idProductAttribute = 0, $idCustomization = 0, $idAddressDelivery = 0)
    {
        $defaultPackStockType = Configuration::get('PS_PACK_STOCK_TYPE');
        $packStockTypesAllowed = [
            Pack::STOCK_TYPE_PRODUCTS_ONLY,
            Pack::STOCK_TYPE_PACK_BOTH,
        ];
        $packStockTypesDefaultSupported = (int) in_array($defaultPackStockType, $packStockTypesAllowed);
        // We need to SUM up cp.`quantity` because multiple rows could be returned when id_customization filtering is skipped.
        $firstUnionSql = 'SELECT SUM(cp.`quantity`) as first_level_quantity, 0 as pack_quantity
          FROM `' . _DB_PREFIX_ . 'cart_product` cp';
        $secondUnionSql = 'SELECT 0 as first_level_quantity, SUM(cp.`quantity` * p.`quantity`) as pack_quantity
          FROM `' . _DB_PREFIX_ . 'cart_product` cp' .
            ' JOIN `' . _DB_PREFIX_ . 'pack` p ON cp.`id_product` = p.`id_product_pack`' .
            ' JOIN `' . _DB_PREFIX_ . 'product` pr ON p.`id_product_pack` = pr.`id_product`';

        if ($idCustomization) {
            $customizationJoin = '
                LEFT JOIN `' . _DB_PREFIX_ . 'customization` c ON (
                    c.`id_product` = cp.`id_product`
                    AND c.`id_product_attribute` = cp.`id_product_attribute`
                )';
            $firstUnionSql .= $customizationJoin;
            $secondUnionSql .= $customizationJoin;
        }
        // Ignore customizations if $idCustomization is set to false
        // This is necessary to get products with or without customizations
        $commonWhere = '
            WHERE cp.`id_product_attribute` = ' . (int) $idProductAttribute . '
              ' . ($idCustomization !== false ? ' AND cp.`id_customization` = ' . (int) $idCustomization : '') . '
            AND cp.`id_cart` = ' . (int) $this->id;

        if ($idCustomization) {
            $commonWhere .= ' AND c.`id_customization` = ' . (int) $idCustomization;
        }
        $firstUnionSql .= $commonWhere;
        $firstUnionSql .= ' AND cp.`id_product` = ' . (int) $idProduct;
        $secondUnionSql .= $commonWhere;
        $secondUnionSql .= ' AND p.`id_product_item` = ' . (int) $idProduct;
        $secondUnionSql .= ' AND (pr.`pack_stock_type` IN (' . implode(',', $packStockTypesAllowed) . ') OR (
            pr.`pack_stock_type` = ' . Pack::STOCK_TYPE_DEFAULT . '
            AND ' . $packStockTypesDefaultSupported . ' = 1
        ))';

        // Construct the final SQL that will join the results of these two queries
        $parentSql = 'SELECT
            COALESCE(SUM(first_level_quantity) + SUM(pack_quantity), 0) as deep_quantity,
            COALESCE(SUM(first_level_quantity), 0) as quantity
          FROM (' . $firstUnionSql . ' UNION ' . $secondUnionSql . ') as q';

        return Db::getInstance()->getRow($parentSql);
    }

    /**
     * Update Product quantity.
     *
     * @param int $quantity Quantity to add (or substract)
     * @param int $id_product Product ID
     * @param int|null $id_product_attribute Attribute ID if needed
     * @param int|false $id_customization Customization ID
     * @param string $operator Indicate if quantity must be increased or decreased
     * @param int $id_address_delivery Delivery Address ID - unused
     * @param Shop|null $shop
     * @param bool $auto_add_cart_rule
     * @param bool $skipAvailabilityCheckOutOfStock
     * @param bool $preserveGiftRemoval
     * @param bool $useOrderPrices
     *
     * @return bool|int Whether the quantity has been successfully updated
     */
    public function updateQty(
        $quantity,
        $id_product,
        $id_product_attribute = null,
        $id_customization = false,
        $operator = 'up',
        $id_address_delivery = 0,
        Shop $shop = null,
        $auto_add_cart_rule = true,
        $skipAvailabilityCheckOutOfStock = false,
        bool $preserveGiftRemoval = true,
        bool $useOrderPrices = false
    ) {
        if (!$shop) {
            $shop = Context::getContext()->shop;
        }

        $quantity = (int) $quantity;
        $id_product = (int) $id_product;
        $id_product_attribute = (int) $id_product_attribute;
        $id_customization = (int) $id_customization;
        $product = new Product($id_product, false, (int) Configuration::get('PS_LANG_DEFAULT'), $shop->id);

        if ($id_product_attribute) {
            $combination = new Combination((int) $id_product_attribute);
            if ($combination->id_product != $id_product) {
                return false;
            }
        }

        /* If we have a product combination, the minimal quantity is set with the one of this combination */
        if (!empty($id_product_attribute)) {
            $minimal_quantity = (int) ProductAttribute::getAttributeMinimalQty($id_product_attribute);
        } else {
            $minimal_quantity = (int) $product->minimal_quantity;
        }

        if (!Validate::isLoadedObject($product)) {
            die(Tools::displayError(sprintf('Product with ID "%s" could not be loaded.', $id_product)));
        }

        if (isset(self::$_nbProducts[$this->id])) {
            unset(self::$_nbProducts[$this->id]);
        }

        if (isset(self::$_totalWeight[$this->id])) {
            unset(self::$_totalWeight[$this->id]);
        }

        $data = [
            'cart' => $this,
            'product' => $product,
            'id_product_attribute' => $id_product_attribute,
            'id_customization' => $id_customization,
            'quantity' => $quantity,
            'operator' => $operator,
            'id_address_delivery' => (int) $this->id_address_delivery,
            'shop' => $shop,
            'auto_add_cart_rule' => $auto_add_cart_rule,
        ];

        Hook::exec('actionCartUpdateQuantityBefore', $data);

        if ((int) $quantity <= 0) {
            return $this->deleteProduct($id_product, $id_product_attribute, (int) $id_customization, 0, $preserveGiftRemoval, $useOrderPrices);
        }

        if (!$product->available_for_order
            || (
                Configuration::isCatalogMode()
                && !defined('_PS_ADMIN_DIR_')
            )
        ) {
            return false;
        }

        /* Check if the product is already in the cart */
        $cartProductQuantity = $this->getProductQuantity(
            $id_product,
            $id_product_attribute,
            (int) $id_customization
        );

        /* Update quantity if product already exist */
        if (!empty($cartProductQuantity['quantity'])) {
            $productQuantity = Product::getQuantity($id_product, $id_product_attribute, null, $this, false);
            $availableOutOfStock = Product::isAvailableWhenOutOfStock(StockAvailable::outOfStock($product->id));

            if ($operator == 'up') {
                $updateQuantity = '+ ' . $quantity;
                $newProductQuantity = $productQuantity - $quantity;

                if ($newProductQuantity < 0 && !$availableOutOfStock && !$skipAvailabilityCheckOutOfStock) {
                    return false;
                }
            } elseif ($operator == 'down') {
                $cartFirstLevelProductQuantity = $this->getProductQuantity(
                    (int) $id_product,
                    (int) $id_product_attribute,
                    $id_customization
                );
                $updateQuantity = '- ' . $quantity;

                if ($cartFirstLevelProductQuantity['quantity'] <= 1
                    || $cartProductQuantity['quantity'] - $quantity <= 0
                ) {
                    return $this->deleteProduct((int) $id_product, (int) $id_product_attribute, (int) $id_customization, 0, $preserveGiftRemoval, $useOrderPrices);
                }
            } else {
                return false;
            }

            Db::getInstance()->execute(
                'UPDATE `' . _DB_PREFIX_ . 'cart_product`
                    SET `quantity` = `quantity` ' . $updateQuantity . '
                    WHERE `id_product` = ' . (int) $id_product .
                ' AND `id_customization` = ' . (int) $id_customization .
                (!empty($id_product_attribute) ? ' AND `id_product_attribute` = ' . (int) $id_product_attribute : '') . '
                    AND `id_cart` = ' . (int) $this->id . '
                    LIMIT 1'
            );
        } elseif ($operator == 'up') {
            /* Add product to the cart */

            $sql = 'SELECT stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity
                        FROM ' . _DB_PREFIX_ . 'product p
                        ' . Product::sqlStock('p', $id_product_attribute, true, $shop) . '
                        WHERE p.id_product = ' . $id_product;

            $result2 = Db::getInstance()->getRow($sql);

            // Quantity for product pack
            if (Pack::isPack($id_product)) {
                $result2['quantity'] = Pack::getQuantity($id_product, $id_product_attribute, null, $this, false);
            }

            if (isset($result2['out_of_stock']) && !Product::isAvailableWhenOutOfStock((int) $result2['out_of_stock']) && !$skipAvailabilityCheckOutOfStock) {
                if ((int) $quantity > $result2['quantity']) {
                    return false;
                }
            }

            if ((int) $quantity < $minimal_quantity) {
                return -1;
            }

            $result_add = Db::getInstance()->insert('cart_product', [
                'id_product' => (int) $id_product,
                'id_product_attribute' => (int) $id_product_attribute,
                'id_cart' => (int) $this->id,
                'id_address_delivery' => 0,
                'id_shop' => $shop->id,
                'quantity' => (int) $quantity,
                'date_add' => date('Y-m-d H:i:s'),
                'id_customization' => (int) $id_customization,
            ]);

            if ((int) $id_customization) {
                $result_add &= Db::getInstance()->update('customization', [
                    'id_product_attribute' => $id_product_attribute,
                    'id_address_delivery' => 0,
                    'in_cart' => 1,
                ], '`id_customization` = ' . $id_customization);
            }

            if (!$result_add) {
                return false;
            }
        }

        // refresh cache of self::_products
        $this->_products = $this->getProducts(true);
        $this->update();
        $context = Context::getContext()->cloneContext();
        /* @phpstan-ignore-next-line */
        $context->cart = $this;
        Cache::clean('getContextualValue_*');
        CartRule::autoRemoveFromCart(null, $useOrderPrices);
        if ($auto_add_cart_rule) {
            CartRule::autoAddToCart($context, $useOrderPrices);
        }

        return true;
    }

    /**
     * Add customized data to database. If a customization already exists for the given data, it the given field will be
     * replaced in the customization.
     *
     * @param int $id_product Product ID
     * @param int $id_product_attribute ProductAttribute ID
     * @param int $index Customization field identifier as id_customization_field in table customization_field
     * @param int $type Customization type can be Product::CUSTOMIZE_FILE or Product::CUSTOMIZE_TEXTFIELD
     * @param string $value Customization value
     * @param int $quantity Quantity value
     * @param bool $returnId if true - returns the customization record id
     *
     * @return bool|int
     */
    public function _addCustomization($id_product, $id_product_attribute, $index, $type, $value, $quantity, $returnId = false)
    {
        // Check if there already is a customization for this cart, but not added to cart
        $exising_customization = Db::getInstance()->executeS(
            'SELECT cu.`id_customization`, cd.`index`, cd.`value`, cd.`type` FROM `' . _DB_PREFIX_ . 'customization` cu
            LEFT JOIN `' . _DB_PREFIX_ . 'customized_data` cd
            ON cu.`id_customization` = cd.`id_customization`
            WHERE cu.id_cart = ' . (int) $this->id . '
            AND cu.id_product = ' . (int) $id_product . '
            AND in_cart = 0'
        );

        // If we find some, we check if the field we are adding is already in the customizations
        // If it is, we will remove it
        // We will also get the customization ID so we can assign it correctly
        if ($exising_customization) {
            // If the customization field is alreay filled, delete it
            foreach ($exising_customization as $customization) {
                if ($customization['type'] == $type && $customization['index'] == $index) {
                    Db::getInstance()->execute('
                        DELETE FROM `' . _DB_PREFIX_ . 'customized_data`
                        WHERE id_customization = ' . (int) $customization['id_customization'] . '
                        AND type = ' . (int) $customization['type'] . '
                        AND `index` = ' . (int) $customization['index']);
                    if ($type == Product::CUSTOMIZE_FILE) {
                        @unlink(_PS_UPLOAD_DIR_ . $customization['value']);
                        @unlink(_PS_UPLOAD_DIR_ . $customization['value'] . '_small');
                    }

                    break;
                }
            }
            $id_customization = $exising_customization[0]['id_customization'];
        } else {
            // Otherwise, insert new customization entry
            Db::getInstance()->execute(
                'INSERT INTO `' . _DB_PREFIX_ . 'customization` (`id_cart`, `id_product`, `id_product_attribute`)
                VALUES (' . (int) $this->id . ', ' . (int) $id_product . ', ' . (int) $id_product_attribute . ')'
            );
            $id_customization = Db::getInstance()->Insert_ID();
        }

        // And finally, insert the customized field
        $query = 'INSERT INTO `' . _DB_PREFIX_ . 'customized_data` (`id_customization`, `type`, `index`, `value`)
            VALUES (' . (int) $id_customization . ', ' . (int) $type . ', ' . (int) $index . ', \'' . pSQL($value) . '\')';

        if (!Db::getInstance()->execute($query)) {
            return false;
        }

        return $returnId ? (int) $id_customization : true;
    }

    /**
     * Check if order has already been placed for this cart. Usually used to check if we can delete this cart.
     *
     * @return bool Indicates if the Order exists
     */
    public function orderExists()
    {
        return (bool) Db::getInstance()->getValue(
            'SELECT count(*) FROM `' . _DB_PREFIX_ . 'orders` WHERE `id_cart` = ' . (int) $this->id,
            false
        );
    }

    /**
     * Remove the CartRule from the Cart.
     *
     * @param int $id_cart_rule CartRule ID
     * @param bool $useOrderPrices
     *
     * @return bool Whether the Cart rule has been successfully removed
     */
    public function removeCartRule($id_cart_rule, bool $useOrderPrices = false)
    {
        Cache::clean('Cart::getCartRules_' . $this->id . '-' . CartRule::FILTER_ACTION_ALL);
        Cache::clean('Cart::getCartRules_' . $this->id . '-' . CartRule::FILTER_ACTION_SHIPPING);
        Cache::clean('Cart::getCartRules_' . $this->id . '-' . CartRule::FILTER_ACTION_REDUCTION);
        Cache::clean('Cart::getCartRules_' . $this->id . '-' . CartRule::FILTER_ACTION_GIFT);

        Cache::clean('Cart::getCartRules_' . $this->id . '-' . CartRule::FILTER_ACTION_ALL . '-ids');
        Cache::clean('Cart::getCartRules_' . $this->id . '-' . CartRule::FILTER_ACTION_SHIPPING . '-ids');
        Cache::clean('Cart::getCartRules_' . $this->id . '-' . CartRule::FILTER_ACTION_REDUCTION . '-ids');
        Cache::clean('Cart::getCartRules_' . $this->id . '-' . CartRule::FILTER_ACTION_GIFT . '-ids');

        $result = Db::getInstance()->delete('cart_cart_rule', '`id_cart_rule` = ' . (int) $id_cart_rule . ' AND `id_cart` = ' . (int) $this->id, 1);

        $cart_rule = new CartRule($id_cart_rule, (int) Configuration::get('PS_LANG_DEFAULT'));
        if ((bool) $result && (int) $cart_rule->gift_product) {
            $this->updateQty(1, $cart_rule->gift_product, $cart_rule->gift_product_attribute, false, 'down', 0, null, false, false, true, $useOrderPrices);
        }

        return $result;
    }

    /**
     * Delete a product from the cart.
     *
     * @param int $id_product Product ID
     * @param int|null $id_product_attribute Attribute ID if needed
     * @param int $id_customization Customization id
     * @param int $id_address_delivery Delivery Address id - unused
     * @param bool $preserveGiftsRemoval If true gift are not removed so product is still in cart
     * @param bool $useOrderPrices If true, will use order prices to re-calculate cartRules after the product is deleted
     *
     * @return bool Whether the product has been successfully deleted
     */
    public function deleteProduct(
        $id_product,
        $id_product_attribute = 0,
        $id_customization = 0,
        $id_address_delivery = 0,
        bool $preserveGiftsRemoval = true,
        bool $useOrderPrices = false
    ) {
        if (isset(self::$_nbProducts[$this->id])) {
            unset(self::$_nbProducts[$this->id]);
        }

        if (isset(self::$_totalWeight[$this->id])) {
            unset(self::$_totalWeight[$this->id]);
        }

        // First, if we are deleting a product with customization, we delete it from the database
        if ((int) $id_customization) {
            if (!$this->_deleteCustomization((int) $id_customization)) {
                return false;
            }
        }

        /* Get customization quantity */
        $result = Db::getInstance()->getRow('
            SELECT SUM(`quantity`) AS \'quantity\'
            FROM `' . _DB_PREFIX_ . 'customization`
            WHERE `id_cart` = ' . (int) $this->id . '
            AND `id_product` = ' . (int) $id_product . '
            AND `id_customization` = ' . (int) $id_customization . '
            AND `id_product_attribute` = ' . (int) $id_product_attribute);

        if ($result === false) {
            return false;
        }

        // Now, we must check if there are any products added as gifts in the cart and keep them.
        // We do this only for products without customization, because we can't have a customized
        // product added as a gift
        $preservedGifts = [];
        $giftKey = (int) $id_product . '-' . (int) $id_product_attribute;
        if ($preserveGiftsRemoval && empty($id_customization)) {
            // We check the cart and see if there are any gifts added
            $preservedGifts = $this->getProductsGifts($id_product, $id_product_attribute);

            // If yes, we do not delete the product, but change it's quantity to the number of gifts that are in cart,
            // so they remain. We must specifically target the product ID, combination ID and customization ID.
            // If we didn't use these conditions, we would set all cart rows with this product ID to $preservedGifts[$giftKey].
            if (isset($preservedGifts[$giftKey]) && $preservedGifts[$giftKey] > 0) {
                return Db::getInstance()->execute(
                    'UPDATE `' . _DB_PREFIX_ . 'cart_product`
                    SET `quantity` = ' . (int) $preservedGifts[$giftKey] . '
                    WHERE `id_cart` = ' . (int) $this->id . '
                    AND `id_product` = ' . (int) $id_product . '
                    AND `id_product_attribute` = ' . (int) $id_product_attribute . '
                    AND `id_customization` = 0'
                );
            }
        }

        /* Product deletion */
        $result = Db::getInstance()->execute('
        DELETE FROM `' . _DB_PREFIX_ . 'cart_product`
        WHERE `id_product` = ' . (int) $id_product . '
        AND `id_customization` = ' . (int) $id_customization .
            (null !== $id_product_attribute ? ' AND `id_product_attribute` = ' . (int) $id_product_attribute : '') . '
        AND `id_cart` = ' . (int) $this->id);

        if ($result) {
            $return = $this->update();
            // refresh cache of self::_products
            $this->_products = $this->getProducts(true);
            if (!isset($preservedGifts[$giftKey]) || $preservedGifts[$giftKey] <= 0) {
                CartRule::autoRemoveFromCart(null, $useOrderPrices);
                CartRule::autoAddToCart(null, $useOrderPrices);
            }

            return $return;
        }

        return false;
    }

    /**
     * @param int $id_product
     * @param int $id_product_attribute
     *
     * @return array
     */
    protected function getProductsGifts($id_product, $id_product_attribute)
    {
        $id_product_attribute = (int) $id_product_attribute;

        $gifts = array_filter($this->getProductsWithSeparatedGifts(), function ($product) {
            return array_key_exists('is_gift', $product) && $product['is_gift'];
        });

        $preservedGifts = [$id_product . '-' . $id_product_attribute => 0];

        foreach ($gifts as $gift) {
            if (
                (int) $gift['id_product_attribute'] === $id_product_attribute
                && (int) $gift['id_product'] === $id_product
            ) {
                ++$preservedGifts[$id_product . '-' . $id_product_attribute];
            }
        }

        return $preservedGifts;
    }

    /**
     * Delete a complete customization from the Cart. If the Customization is a Picture,
     * then the Image is also deleted.
     *
     * @param int $id_customization Customization Id
     *
     * @return bool Indicates if the Customization was successfully deleted
     */
    protected function _deleteCustomization($id_customization)
    {
        $result = true;
        // Try to find the given customization
        $customization = Db::getInstance()->getRow('SELECT *
            FROM `' . _DB_PREFIX_ . 'customization`
            WHERE `id_customization` = ' . (int) $id_customization);

        if ($customization) {
            // If found, let's delete all customized fields for the given customization
            $cust_data = Db::getInstance()->getRow('SELECT *
                FROM `' . _DB_PREFIX_ . 'customized_data`
                WHERE `id_customization` = ' . (int) $id_customization);

            // Delete customization physical file if necessary
            if (isset($cust_data['type']) && $cust_data['type'] == Product::CUSTOMIZE_FILE) {
                $result &= file_exists(_PS_UPLOAD_DIR_ . $cust_data['value']) ? @unlink(_PS_UPLOAD_DIR_ . $cust_data['value']) : true;
                $result &= file_exists(_PS_UPLOAD_DIR_ . $cust_data['value'] . '_small') ? @unlink(_PS_UPLOAD_DIR_ . $cust_data['value'] . '_small') : true;
            }

            $result &= Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'customized_data`
                WHERE `id_customization` = ' . (int) $id_customization
            );

            if (!$result) {
                return false;
            }

            // And finally delete the customization itself
            return Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'customization`
                WHERE `id_customization` = ' . (int) $id_customization
            );
        }

        return true;
    }

    /**
     * Get formatted total amount in Cart.
     *
     * @param int $id_cart Cart ID
     * @param bool $use_tax_display Whether the tax should be displayed
     * @param int $type Type enum:
     *                  - ONLY_PRODUCTS
     *                  - ONLY_DISCOUNTS
     *                  - BOTH
     *                  - BOTH_WITHOUT_SHIPPING
     *                  - ONLY_SHIPPING
     *                  - ONLY_WRAPPING
     *
     * @return string Formatted amount in Cart
     */
    public static function getTotalCart($id_cart, $use_tax_display = false, $type = Cart::BOTH)
    {
        $cart = new Cart($id_cart);
        if (!Validate::isLoadedObject($cart)) {
            die(Tools::displayError(sprintf('Cart with ID "%s" could not be loaded.', $id_cart)));
        }

        $with_taxes = $use_tax_display ? $cart->_taxCalculationMethod != PS_TAX_EXC : true;

        return Context::getContext()->getCurrentLocale()->formatPrice(
            $cart->getOrderTotal($with_taxes, $type),
            Currency::getIsoCodeById((int) $cart->id_currency)
        );
    }

    /**
     * Get total in Cart using a tax calculation method.
     *
     * @param int $id_cart Cart ID
     *
     * @return string Formatted total amount in Cart
     * @todo: What is this?
     */
    public static function getOrderTotalUsingTaxCalculationMethod($id_cart)
    {
        return Cart::getTotalCart($id_cart, true);
    }

    /**
     * This function returns the total cart amount.
     *
     * @param bool $withTaxes With or without taxes
     * @param int $type Total type enum
     *                  - Cart::ONLY_PRODUCTS
     *                  - Cart::ONLY_DISCOUNTS
     *                  - Cart::BOTH
     *                  - Cart::BOTH_WITHOUT_SHIPPING
     *                  - Cart::ONLY_SHIPPING
     *                  - Cart::ONLY_WRAPPING
     *                  - Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING
     * @param array $products
     * @param int $id_carrier
     * @param bool $use_cache @deprecated
     * @param bool $keepOrderPrices When true use the Order saved prices instead of the most recent ones from catalog (if Order exists)
     *
     * @return float Order total
     *
     * @throws \Exception
     */
    public function getOrderTotal(
        $withTaxes = true,
        $type = Cart::BOTH,
        $products = null,
        $id_carrier = null,
        $use_cache = false,
        bool $keepOrderPrices = false
    ) {
        if ((int) $id_carrier <= 0) {
            $id_carrier = null;
        }

        // check type
        $type = (int) $type;
        $allowedTypes = [
            Cart::ONLY_PRODUCTS,
            Cart::ONLY_DISCOUNTS,
            Cart::BOTH,
            Cart::BOTH_WITHOUT_SHIPPING,
            Cart::ONLY_SHIPPING,
            Cart::ONLY_WRAPPING,
            Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING,
        ];
        if (!in_array($type, $allowedTypes)) {
            throw new \Exception('Invalid calculation type: ' . $type);
        }

        // EARLY RETURNS

        // if cart rules are not used
        if ($type == Cart::ONLY_DISCOUNTS && !CartRule::isFeatureActive()) {
            return 0;
        }
        // no shipping cost if is a cart with only virtuals products
        $virtual = $this->isVirtualCart();
        if ($virtual && $type == Cart::ONLY_SHIPPING) {
            return 0;
        }
        if ($virtual && $type == Cart::BOTH) {
            $type = Cart::BOTH_WITHOUT_SHIPPING;
        }

        // filter products
        if (null === $products) {
            $products = $this->getProducts(false, false, null, true, $keepOrderPrices);
        }

        if ($type == Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING) {
            foreach ($products as $key => $product) {
                if (!empty($product['is_virtual'])) {
                    unset($products[$key]);
                }
            }
            $type = Cart::ONLY_PRODUCTS;
        }

        if ($type == Cart::ONLY_PRODUCTS) {
            foreach ($products as $key => $product) {
                if (!empty($product['is_gift'])) {
                    unset($products[$key]);
                }
            }
        }

        if (!Configuration::get('PS_TAX')) {
            $withTaxes = false;
        }

        // CART CALCULATION
        $cartRules = [];
        if (in_array($type, [Cart::BOTH, Cart::BOTH_WITHOUT_SHIPPING, Cart::ONLY_DISCOUNTS])) {
            $cartRules = $this->getTotalCalculationCartRules($type, $type == Cart::BOTH);
        }

        $computePrecision = Context::getContext()->getComputingPrecision();
        $calculator = $this->newCalculator($products, $cartRules, $id_carrier, $computePrecision, $keepOrderPrices);
        switch ($type) {
            case Cart::ONLY_SHIPPING:
                $calculator->calculateRows();
                $calculator->calculateFees();
                $amount = $calculator->getFees()->getInitialShippingFees();

                break;
            case Cart::ONLY_WRAPPING:
                $calculator->calculateRows();
                $calculator->calculateFees();
                $amount = $calculator->getFees()->getInitialWrappingFees();

                break;
            case Cart::BOTH:
                $calculator->processCalculation();
                $amount = $calculator->getTotal();

                break;
            case Cart::BOTH_WITHOUT_SHIPPING:
                $calculator->calculateRows();
                // dont process free shipping to avoid calculation loop (and maximum nested functions !)
                $calculator->calculateCartRulesWithoutFreeShipping();
                $amount = $calculator->getTotal(true);
                break;
            case Cart::ONLY_PRODUCTS:
                $calculator->calculateRows();
                $amount = $calculator->getRowTotal();

                break;
            case Cart::ONLY_DISCOUNTS:
                $calculator->processCalculation();
                $amount = $calculator->getDiscountTotal();

                break;
            default:
                throw new \Exception('unknown cart calculation type : ' . $type);
        }

        // TAXES ?

        $value = $withTaxes ? $amount->getTaxIncluded() : $amount->getTaxExcluded();

        // ROUND AND RETURN

        return Tools::ps_round($value, $computePrecision);
    }

    /**
     * get the populated cart calculator.
     *
     * @param array $products list of products to calculate on
     * @param array $cartRules list of cart rules to apply
     * @param int $id_carrier carrier id (fees calculation)
     * @param int|null $computePrecision
     * @param bool $keepOrderPrices When true use the Order saved prices instead of the most recent ones from catalog (if Order exists)
     *
     * @return \PrestaShop\PrestaShop\Core\Cart\Calculator
     */
    public function newCalculator($products, $cartRules, $id_carrier, $computePrecision = null, bool $keepOrderPrices = false)
    {
        $orderId = null;
        if ($keepOrderPrices) {
            $orderId = Order::getIdByCartId($this->id);
            $orderId = (int) $orderId ?: null;
        }
        $calculator = new Calculator(
            $this,
            $id_carrier,
            $computePrecision,
            $orderId
        );

        /** @var PriceCalculator $priceCalculator */
        $priceCalculator = ServiceLocator::get(PriceCalculator::class);

        // set cart rows (products)
        $useEcotax = $this->configuration->get('PS_USE_ECOTAX');
        $precision = Context::getContext()->getComputingPrecision();
        $configRoundType = $this->configuration->get('PS_ROUND_TYPE');
        $roundTypes = [
            Order::ROUND_TOTAL => CartRow::ROUND_MODE_TOTAL,
            Order::ROUND_LINE => CartRow::ROUND_MODE_LINE,
            Order::ROUND_ITEM => CartRow::ROUND_MODE_ITEM,
        ];
        if (isset($roundTypes[$configRoundType])) {
            $roundType = $roundTypes[$configRoundType];
        } else {
            $roundType = CartRow::ROUND_MODE_ITEM;
        }

        foreach ($products as $product) {
            $cartRow = new CartRow(
                $product,
                $priceCalculator,
                new AddressFactory(),
                new CustomerDataProvider(),
                new CacheAdapter(),
                new GroupDataProvider(),
                new Database(),
                $useEcotax,
                $precision,
                $roundType,
                $orderId
            );
            $calculator->addCartRow($cartRow);
        }

        // set cart rules
        foreach ($cartRules as $cartRule) {
            $calculator->addCartRule(new CartRuleData($cartRule));
        }

        return $calculator;
    }

    /**
     * @return float
     */
    public function getDiscountSubtotalWithoutGifts($withTaxes = true)
    {
        $discountSubtotal = $this->excludeGiftsDiscountFromTotal()
            ->getOrderTotal($withTaxes, self::ONLY_DISCOUNTS);
        $this->includeGiftsDiscountInTotal();

        return $discountSubtotal;
    }

    /**
     * @param array $products
     *
     * @return array
     */
    protected function countProductLines($products)
    {
        $productsLines = [];
        array_map(function ($product) use (&$productsLines) {
            $productIndex = $product['id_product'] . '-' . $product['id_product_attribute'];

            if (!array_key_exists($productIndex, $productsLines)) {
                $productsLines[$product['id_product'] . '-' . $product['id_product_attribute']] = 1;
            } else {
                ++$productsLines[$product['id_product'] . '-' . $product['id_product_attribute']];
            }
        }, $products);

        return $productsLines;
    }

    /**
     * @param array $products - not used anymore
     *
     * @return int
     */
    protected function getDeliveryAddressId($products = null)
    {
        return $this->id_address_delivery;
    }

    /**
     * @param int $type
     * @param bool $withShipping
     *
     * @return array
     */
    protected function getTotalCalculationCartRules($type, $withShipping)
    {
        if ($withShipping || $type == Cart::ONLY_DISCOUNTS) {
            $cartRules = $this->getCartRules(CartRule::FILTER_ACTION_ALL, false);
        } else {
            $cartRules = $this->getCartRules(CartRule::FILTER_ACTION_REDUCTION, false);
            // Cart Rules array are merged manually in order to avoid doubles
            foreach ($this->getCartRules(CartRule::FILTER_ACTION_GIFT, false) as $cartRuleCandidate) {
                $alreadyAddedCartRule = false;
                foreach ($cartRules as $cartRule) {
                    if ($cartRuleCandidate['id_cart_rule'] == $cartRule['id_cart_rule']) {
                        $alreadyAddedCartRule = true;
                    }
                }

                if (!$alreadyAddedCartRule) {
                    $cartRules[] = $cartRuleCandidate;
                }
            }
        }

        return $cartRules;
    }

    /**
     * @param bool $withTaxes
     * @param array $product
     * @param Context|null $virtualContext
     *
     * @return int
     */
    protected function findTaxRulesGroupId($withTaxes, $product, $virtualContext)
    {
        if ($withTaxes) {
            $taxRulesGroupId = Product::getIdTaxRulesGroupByIdProduct((int) $product['id_product'], $virtualContext);

            $addressId = $this->getProductAddressId();
            $address = $this->addressFactory->findOrCreate($addressId, true);

            // Refresh cache and execute tax manager factory hook
            TaxManagerFactory::getManager($address, $taxRulesGroupId)->getTaxCalculator();
        } else {
            $taxRulesGroupId = 0;
        }

        return $taxRulesGroupId;
    }

    /**
     * @param array $product - not used anymore
     *
     * @return int|null
     */
    public function getProductAddressId($product = null)
    {
        $taxAddressType = $this->configuration->get('PS_TAX_ADDRESS_TYPE');
        if ($taxAddressType == 'id_address_invoice') {
            $addressId = (int) $this->id_address_invoice;
        } else {
            $addressId = (int) $this->id_address_delivery;
        }

        // Get delivery address of the product from the cart
        if (!$this->addressFactory->addressExists($addressId, true)) {
            $addressId = null;
        }

        return $addressId;
    }

    /**
     * Returns the tax address id according to the shop's configuration
     *
     * @return int
     */
    public function getTaxAddressId()
    {
        $taxAddressType = $this->configuration->get('PS_TAX_ADDRESS_TYPE');
        if (Validate::isLoadedObject($this) && !empty($taxAddressType)) {
            $addressId = $this->$taxAddressType;
        } else {
            $addressId = $this->id_address_delivery;
        }

        return $addressId;
    }

    /**
     * @param bool $withTaxes
     * @param int $type
     *
     * @return float|int
     */
    protected function calculateWrappingFees($withTaxes, $type)
    {
        // Wrapping Fees
        $wrapping_fees = 0;

        // With PS_ATCP_SHIPWRAP on the gift wrapping cost computation calls getOrderTotal
        // with $type === Cart::ONLY_PRODUCTS, so the flag below prevents an infinite recursion.
        $includeGiftWrapping = (!$this->configuration->get('PS_ATCP_SHIPWRAP') || $type !== Cart::ONLY_PRODUCTS);
        $computePrecision = Context::getContext()->getComputingPrecision();

        if ($this->gift && $includeGiftWrapping) {
            $wrapping_fees = Tools::convertPrice(
                Tools::ps_round(
                    $this->getGiftWrappingPrice($withTaxes),
                    $computePrecision
                ),
                Currency::getCurrencyInstance((int) $this->id_currency)
            );
        }

        return $wrapping_fees;
    }

    /**
     * Get the gift wrapping price.
     *
     * @param bool $with_taxes With or without taxes
     *
     * @return float wrapping price
     */
    public function getGiftWrappingPrice($with_taxes = true, $id_address = null)
    {
        static $address = [];

        // Check if cart is empty, or if the current cart contains at least a real product (not virtual)
        if (!$this->hasProducts() || !$this->hasRealProducts()) {
            return 0;
        }

        $wrapping_fees = (float) Configuration::get('PS_GIFT_WRAPPING_PRICE');

        if ($wrapping_fees <= 0) {
            return $wrapping_fees;
        }

        if ($with_taxes) {
            if (Configuration::get('PS_ATCP_SHIPWRAP')) {
                // With PS_ATCP_SHIPWRAP, wrapping fee is by default tax included
                // so nothing to do here.
            } else {
                if (!isset($address[$this->id])) {
                    if ($id_address === null) {
                        $id_address = (int) $this->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
                    }

                    try {
                        $address[$this->id] = Address::initialize($id_address);
                    } catch (Exception $e) {
                        $address[$this->id] = new Address();
                        $address[$this->id]->id_country = Configuration::get('PS_COUNTRY_DEFAULT');
                    }
                }

                $tax_manager = TaxManagerFactory::getManager($address[$this->id], (int) Configuration::get('PS_GIFT_WRAPPING_TAX_RULES_GROUP'));
                $tax_calculator = $tax_manager->getTaxCalculator();
                $wrapping_fees = $tax_calculator->addTaxes($wrapping_fees);
            }
        } elseif (Configuration::get('PS_ATCP_SHIPWRAP')) {
            // With PS_ATCP_SHIPWRAP, wrapping fee is by default tax included, so we convert it
            // when asked for the pre tax price.
            $wrapping_fees = Tools::ps_round(
                $wrapping_fees / (1 + $this->getAverageProductsTaxRate()),
                Context::getContext()->getComputingPrecision()
            );
        }

        return $wrapping_fees;
    }

    /**
     * Get the number of packages.
     *
     * @return int number of packages
     */
    public function getNbOfPackages()
    {
        if (!isset(static::$cacheNbPackages[$this->id])) {
            static::$cacheNbPackages[$this->id] = 0;
            foreach ($this->getPackageList() as $by_address) {
                static::$cacheNbPackages[$this->id] += count($by_address);
            }
        }

        return static::$cacheNbPackages[$this->id];
    }

    /**
     * Get products grouped by package and by addresses to be sent individualy (one package = one shipping cost).
     *
     * @return array array(
     *               0 => array( // First address
     *               0 => array(  // First package
     *               'product_list' => array(...),
     *               'carrier_list' => array(...),
     *               ),
     *               ),
     *               );
     *
     * @todo Add avaibility check
     */
    public function getPackageList($flush = false)
    {
        // Resolve cache key, we will get the info from cache if present and we are not forcing a refresh
        $cache_key = (int) $this->id . '_' . (int) $this->id_address_delivery;
        if (isset(static::$cachePackageList[$cache_key]) && static::$cachePackageList[$cache_key] !== false && !$flush) {
            return static::$cachePackageList[$cache_key];
        }

        // Load products, hard refresh if needed
        $product_list = $this->getProducts($flush);

        // Step 1 - We assign some basic information (load their carriers) to products and separate them by their stock quantities.
        $grouped_by_stock = [
            'in_stock' => [],
            'out_of_stock' => [],
        ];

        foreach ($product_list as &$product) {
            // Assign delivery address if missing, for compatibility
            $product['id_address_delivery'] = (int) $this->id_address_delivery;

            // Get product's carriers - the product can have some specific limitations
            $product['carrier_list'] = Carrier::getAvailableCarrierList(
                new Product($product['id_product']),
                0,
                (int) $this->id_address_delivery,
                null,
                $this
            );

            // Apply fallback if no carrier is found
            if (empty($product['carrier_list'])) {
                $product['carrier_list'] = [0 => 0];
            }

            // If "send in-stock items first" is enabled and properly implemented sometime in the future, we separate products by stock
            if (!$this->allow_seperated_package) {
                $stockGroupKey = 'in_stock';
            } else {
                $product['in_stock'] = StockAvailable::getQuantityAvailableByProduct($product['id_product'], $product['id_product_attribute']) > 0;
                $stockGroupKey = $product['in_stock'] ? 'in_stock' : 'out_of_stock';
                $product_quantity_in_stock = StockAvailable::getQuantityAvailableByProduct($product['id_product'], $product['id_product_attribute']);
                if ($product['in_stock'] && $product['cart_quantity'] > $product_quantity_in_stock) {
                    $out_stock_part = $product['cart_quantity'] - $product_quantity_in_stock;
                    $product_bis = $product;
                    $product_bis['cart_quantity'] = $out_stock_part;
                    $product_bis['in_stock'] = 0;
                    $product['cart_quantity'] -= $out_stock_part;
                    $grouped_by_stock['out_of_stock'][] = $product_bis;
                }
            }

            $grouped_by_stock[$stockGroupKey][] = $product;
        }
        unset($product);
        // Now we have them in two groups, those in stock and those not in stock.

        // Step 2 - We divide those two groups once more into groups by their carriers.
        $grouped_by_carriers = [
            'in_stock' => [],
            'out_of_stock' => [],
        ];
        foreach ($grouped_by_stock as $key => $product_list) {
            foreach ($product_list as $product) {
                // We construct unique key by combining IDs of their carriers
                $package_carriers_key = implode(',', $product['carrier_list']);

                // Initialize our array if it's the first product with this combination of these carriers
                if (!isset($grouped_by_carriers[$key][$package_carriers_key])) {
                    $grouped_by_carriers[$key][$package_carriers_key] = [
                        'product_list' => [],
                        'carrier_list' => $product['carrier_list'],
                    ];
                }

                // Add this product to this carrier combination group
                $grouped_by_carriers[$key][$package_carriers_key]['product_list'][] = $product;
            }
        }
        // Now we have them in two groups, those in stock and those not in stock, then grouped by their common carriers.

        /*
         * Step 3 - merge product from grouped_by_carriers into $package to minimize the number of package.
         * Example:
         * Product A can be sent with carriers A and B
         * Product B can be sent with carriers A and C
         * Resulting package will be 1 with carrier A
         */
        $package_list = [
            'in_stock' => [],
            'out_of_stock' => [],
        ];

        // Count occurance of each carriers to minimize the number of packages
        $carrier_count = [];
        foreach ($grouped_by_carriers as $key => $products_grouped_by_carriers) {
            foreach ($products_grouped_by_carriers as $data) {
                foreach ($data['carrier_list'] as $id_carrier) {
                    if (!isset($carrier_count[$id_carrier])) {
                        $carrier_count[$id_carrier] = 0;
                    }
                    ++$carrier_count[$id_carrier];
                }
            }
        }
        arsort($carrier_count);

        foreach ($grouped_by_carriers as $key => $products_grouped_by_carriers) {
            foreach ($products_grouped_by_carriers as $data) {
                foreach ($carrier_count as $id_carrier => $rate) {
                    if (array_key_exists($id_carrier, $data['carrier_list'])) {
                        if (!isset($package_list[$key][$id_carrier])) {
                            $package_list[$key][$id_carrier] = [
                                'carrier_list' => $data['carrier_list'],
                                'product_list' => [],
                            ];
                        }
                        $package_list[$key][$id_carrier]['carrier_list'] =
                            array_intersect($package_list[$key][$id_carrier]['carrier_list'], $data['carrier_list']);
                        $package_list[$key][$id_carrier]['product_list'] =
                            array_merge($package_list[$key][$id_carrier]['product_list'], $data['product_list']);

                        break;
                    }
                }
            }
        }

        // Step 4 - Reduce depth of $package_list
        $final_package_list = [];
        foreach ($package_list as $products_grouped_by_carriers) {
            foreach ($products_grouped_by_carriers as $data) {
                $final_package_list[(int) $this->id_address_delivery][] = [
                    'product_list' => $data['product_list'],
                    'carrier_list' => $data['carrier_list'],
                    'warehouse_list' => [0 => 0], // For backward compatibility - not used
                    'id_warehouse' => 0, // For backward compatibility - not used
                ];
            }
        }

        static::$cachePackageList[$cache_key] = $final_package_list;

        return $final_package_list;
    }

    /**
     * @deprecated Since 9.0 and will be removed in 10.0
     */
    public function getPackageIdWarehouse($package, $id_carrier = null)
    {
        @trigger_error(sprintf(
            '%s is deprecated since 9.0 and will be removed in 10.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        return 0;
    }

    /**
     * Get all deliveries options available for the current cart.
     *
     * @param Country $default_country
     * @param bool $flush Force flushing cache
     *
     * @return array array(
     *               0 => array( // First address
     *               '12,' => array(  // First delivery option available for this address
     *               carrier_list => array(
     *               12 => array( // First carrier for this option
     *               'instance' => Carrier Object,
     *               'logo' => <url to the carriers logo>,
     *               'price_with_tax' => 12.4,
     *               'price_without_tax' => 12.4,
     *               'package_list' => array(
     *               1,
     *               3,
     *               ),
     *               ),
     *               ),
     *               is_best_grade => true, // Does this option have the biggest grade (quick shipping) for this shipping address
     *               is_best_price => true, // Does this option have the lower price for this shipping address
     *               unique_carrier => true, // Does this option use a unique carrier
     *               total_price_with_tax => 12.5,
     *               total_price_without_tax => 12.5,
     *               position => 5, // Average of the carrier position
     *               ),
     *               ),
     *               );
     *               If there are no carriers available for an address, return an empty  array
     */
    public function getDeliveryOptionList(Country $default_country = null, $flush = false)
    {
        if (isset(static::$cacheDeliveryOptionList[$this->id]) && !$flush) {
            return static::$cacheDeliveryOptionList[$this->id];
        }

        $delivery_option_list = [];
        $carriers_price = [];
        $carrier_collection = [];
        $package_list = $this->getPackageList($flush);

        // Foreach addresses
        foreach ($package_list as $id_address => $packages) {
            // Initialize vars
            $delivery_option_list[$id_address] = [];
            $carriers_price[$id_address] = [];
            $common_carriers = null;
            $best_price_carriers = [];
            $best_grade_carriers = [];
            $carriers_instance = [];

            // Get country
            if ($id_address) {
                $address = new Address($id_address);
                $country = new Country($address->id_country);
            } else {
                $country = $default_country;
            }

            // Foreach packages, get the carriers with best price, best position and best grade
            foreach ($packages as $id_package => $package) {
                // No carriers available
                if (count($packages) == 1 && count($package['carrier_list']) == 1 && current($package['carrier_list']) == 0) {
                    $cache[$this->id] = [];

                    return $cache[$this->id];
                }

                $carriers_price[$id_address][$id_package] = [];

                // Get all common carriers for each packages to the same address
                if (null === $common_carriers) {
                    $common_carriers = $package['carrier_list'];
                } else {
                    $common_carriers = array_intersect($common_carriers, $package['carrier_list']);
                }

                $best_price = null;
                $best_price_carrier = null;
                $best_grade = null;
                $best_grade_carrier = null;

                // Foreach carriers of the package, calculate his price, check if it the best price, position and grade
                foreach ($package['carrier_list'] as $id_carrier) {
                    if (!isset($carriers_instance[$id_carrier])) {
                        $carriers_instance[$id_carrier] = new Carrier($id_carrier);
                    }

                    $price_with_tax = $this->getPackageShippingCost((int) $id_carrier, true, $country, $package['product_list']);
                    $price_without_tax = $this->getPackageShippingCost((int) $id_carrier, false, $country, $package['product_list']);
                    if (null === $best_price || $price_with_tax < $best_price) {
                        $best_price = $price_with_tax;
                        $best_price_carrier = $id_carrier;
                    }
                    $carriers_price[$id_address][$id_package][$id_carrier] = [
                        'without_tax' => $price_without_tax,
                        'with_tax' => $price_with_tax,
                    ];

                    $grade = $carriers_instance[$id_carrier]->grade;
                    if (null === $best_grade || $grade > $best_grade) {
                        $best_grade = $grade;
                        $best_grade_carrier = $id_carrier;
                    }
                }

                $best_price_carriers[$id_package] = $best_price_carrier;
                $best_grade_carriers[$id_package] = $best_grade_carrier;
            }

            // Reset $best_price_carrier, it's now an array
            $best_price_carrier = [];
            $key = '';

            // Get the delivery option with the lower price
            foreach ($best_price_carriers as $id_package => $id_carrier) {
                $key .= $id_carrier . ',';
                if (!isset($best_price_carrier[$id_carrier])) {
                    $best_price_carrier[$id_carrier] = [
                        'price_with_tax' => 0,
                        'price_without_tax' => 0,
                        'package_list' => [],
                        'product_list' => [],
                    ];
                }
                $best_price_carrier[$id_carrier]['price_with_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'];
                $best_price_carrier[$id_carrier]['price_without_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'];
                $best_price_carrier[$id_carrier]['package_list'][] = $id_package;
                $best_price_carrier[$id_carrier]['product_list'] = array_merge($best_price_carrier[$id_carrier]['product_list'], $packages[$id_package]['product_list']);
                $best_price_carrier[$id_carrier]['instance'] = $carriers_instance[$id_carrier];
                $real_best_price = !isset($real_best_price) || $real_best_price > $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'] ?
                    $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'] : $real_best_price;
                $real_best_price_wt = !isset($real_best_price_wt) || $real_best_price_wt > $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'] ?
                    $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'] : $real_best_price_wt;
            }

            // Add the delivery option with best price as best price
            $delivery_option_list[$id_address][$key] = [
                'carrier_list' => $best_price_carrier,
                'is_best_price' => true,
                'is_best_grade' => false,
                'unique_carrier' => (count($best_price_carrier) <= 1),
            ];

            // Reset $best_grade_carrier, it's now an array
            $best_grade_carrier = [];
            $key = '';

            // Get the delivery option with the best grade
            foreach ($best_grade_carriers as $id_package => $id_carrier) {
                $key .= $id_carrier . ',';
                if (!isset($best_grade_carrier[$id_carrier])) {
                    $best_grade_carrier[$id_carrier] = [
                        'price_with_tax' => 0,
                        'price_without_tax' => 0,
                        'package_list' => [],
                        'product_list' => [],
                    ];
                }
                $best_grade_carrier[$id_carrier]['price_with_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'];
                $best_grade_carrier[$id_carrier]['price_without_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'];
                $best_grade_carrier[$id_carrier]['package_list'][] = $id_package;
                $best_grade_carrier[$id_carrier]['product_list'] = array_merge($best_grade_carrier[$id_carrier]['product_list'], $packages[$id_package]['product_list']);
                $best_grade_carrier[$id_carrier]['instance'] = $carriers_instance[$id_carrier];
            }

            // Add the delivery option with best grade as best grade
            if (!isset($delivery_option_list[$id_address][$key])) {
                $delivery_option_list[$id_address][$key] = [
                    'carrier_list' => $best_grade_carrier,
                    'is_best_price' => false,
                    'unique_carrier' => (count($best_grade_carrier) <= 1),
                ];
            }
            $delivery_option_list[$id_address][$key]['is_best_grade'] = true;

            // Get all delivery options with a unique carrier
            foreach ($common_carriers as $id_carrier) {
                $key = '';
                $package_list = [];
                $product_list = [];
                $price_with_tax = 0;
                $price_without_tax = 0;

                foreach ($packages as $id_package => $package) {
                    $key .= $id_carrier . ',';
                    $price_with_tax += $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'];
                    $price_without_tax += $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'];
                    $package_list[] = $id_package;
                    $product_list = array_merge($product_list, $package['product_list']);
                }

                if (!isset($delivery_option_list[$id_address][$key])) {
                    $delivery_option_list[$id_address][$key] = [
                        'is_best_price' => false,
                        'is_best_grade' => false,
                        'unique_carrier' => true,
                        'carrier_list' => [
                            $id_carrier => [
                                'price_with_tax' => $price_with_tax,
                                'price_without_tax' => $price_without_tax,
                                'instance' => $carriers_instance[$id_carrier],
                                'package_list' => $package_list,
                                'product_list' => $product_list,
                            ],
                        ],
                    ];
                } else {
                    $delivery_option_list[$id_address][$key]['unique_carrier'] = (count($delivery_option_list[$id_address][$key]['carrier_list']) <= 1);
                }
            }
        }

        $cart_rules = CartRule::getCustomerCartRules(
            (int) Context::getContext()->cookie->id_lang,
            (int) Context::getContext()->cookie->id_customer,
            true,
            true,
            false,
            $this,
            true
        );

        $result = false;
        if ($this->id) {
            $result = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'cart_cart_rule WHERE id_cart = ' . (int) $this->id);
        }

        $cart_rules_in_cart = [];

        if (is_array($result)) {
            foreach ($result as $row) {
                $cart_rules_in_cart[] = $row['id_cart_rule'];
            }
        }

        $total_products_wt = $this->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $total_products = $this->getOrderTotal(false, Cart::ONLY_PRODUCTS);

        $free_carriers_rules = [];

        $context = Context::getContext();
        foreach ($cart_rules as $cart_rule) {
            $total_price = $cart_rule['minimum_amount_tax'] ? $total_products_wt : $total_products;
            $total_price += ($cart_rule['minimum_amount_tax'] && $cart_rule['minimum_amount_shipping'] && isset($real_best_price)) ? $real_best_price : 0;
            $total_price += (!$cart_rule['minimum_amount_tax'] && $cart_rule['minimum_amount_shipping'] && isset($real_best_price_wt)) ? $real_best_price_wt : 0;
            if ($cart_rule['free_shipping'] && $cart_rule['carrier_restriction']
                && in_array($cart_rule['id_cart_rule'], $cart_rules_in_cart)
                && $cart_rule['minimum_amount'] <= $total_price) {
                $cr = new CartRule((int) $cart_rule['id_cart_rule']);
                if (Validate::isLoadedObject($cr) &&
                    $cr->checkValidity($context, in_array((int) $cart_rule['id_cart_rule'], $cart_rules_in_cart), false, false)) {
                    $carriers = $cr->getAssociatedRestrictions('carrier', true, false);
                    if (is_array($carriers) && count($carriers) && isset($carriers['selected'])) {
                        foreach ($carriers['selected'] as $carrier) {
                            if (isset($carrier['id_carrier']) && $carrier['id_carrier']) {
                                $free_carriers_rules[] = (int) $carrier['id_carrier'];
                            }
                        }
                    }
                }
            }
        }

        // For each delivery options :
        //    - Set the carrier list
        //    - Calculate the price
        //    - Calculate the average position
        foreach ($delivery_option_list as $id_address => $delivery_option) {
            foreach ($delivery_option as $key => $value) {
                $total_price_with_tax = 0;
                $total_price_without_tax = 0;
                $total_price_without_tax_with_rules = 0;
                $position = 0;
                foreach ($value['carrier_list'] as $id_carrier => $data) {
                    $total_price_with_tax += $data['price_with_tax'];
                    $total_price_without_tax += $data['price_without_tax'];
                    $total_price_without_tax_with_rules = (in_array($id_carrier, $free_carriers_rules)) ? 0 : $total_price_without_tax;

                    if (!isset($carrier_collection[$id_carrier])) {
                        $carrier_collection[$id_carrier] = new Carrier($id_carrier);
                    }
                    $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['instance'] = $carrier_collection[$id_carrier];

                    if (file_exists(_PS_SHIP_IMG_DIR_ . $id_carrier . '.jpg')) {
                        $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['logo'] = _THEME_SHIP_DIR_ . $id_carrier . '.jpg';
                    } else {
                        $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['logo'] = false;
                    }

                    $position += $carrier_collection[$id_carrier]->position;
                }
                $delivery_option_list[$id_address][$key]['total_price_with_tax'] = $total_price_with_tax;
                $delivery_option_list[$id_address][$key]['total_price_without_tax'] = $total_price_without_tax;
                $delivery_option_list[$id_address][$key]['is_free'] = !$total_price_without_tax_with_rules ? true : false;
                $delivery_option_list[$id_address][$key]['position'] = $position / count($value['carrier_list']);
            }
        }

        // Sort delivery option list
        foreach ($delivery_option_list as &$array) {
            uasort($array, ['Cart', 'sortDeliveryOptionList']);
        }

        Hook::exec(
            'actionFilterDeliveryOptionList',
            [
                'delivery_option_list' => &$delivery_option_list,
            ]
        );

        static::$cacheDeliveryOptionList[$this->id] = $delivery_option_list;

        return static::$cacheDeliveryOptionList[$this->id];
    }

    /**
     * Sort list of option delivery by parameters define in the BO.
     *
     * @param array $option1
     * @param array $option2
     *
     * @return int -1 if $option 1 must be placed before and 1 if the $option1 must be placed after the $option2
     */
    public static function sortDeliveryOptionList($option1, $option2)
    {
        static $order_by_price = null;
        static $order_way = null;
        if (null === $order_by_price) {
            $order_by_price = !Configuration::get('PS_CARRIER_DEFAULT_SORT');
        }
        if (null === $order_way) {
            $order_way = Configuration::get('PS_CARRIER_DEFAULT_ORDER') ? 1 : -1;
        }

        if ($order_by_price) {
            return $option1['total_price_with_tax'] < $option2['total_price_with_tax'] ? $order_way : -$order_way;
        }

        return $option1['position'] < $option2['position'] ? $order_way : -$order_way;
    }

    /**
     * Is the Carrier selected.
     *
     * @param int $id_carrier Carrier ID
     * @param int $id_address Address ID
     *
     * @return bool Indicated if the carrier is selected
     */
    public function carrierIsSelected($id_carrier, $id_address)
    {
        $delivery_option = $this->getDeliveryOption();
        $delivery_option_list = $this->getDeliveryOptionList();

        if (!isset($delivery_option[$id_address])) {
            return false;
        }

        if (!isset($delivery_option_list[$id_address][$delivery_option[$id_address]])) {
            return false;
        }

        if (!in_array($id_carrier, array_keys($delivery_option_list[$id_address][$delivery_option[$id_address]]['carrier_list']))) {
            return false;
        }

        return true;
    }

    /**
     * Simulate output of selected Carrier.
     *
     * @param bool $use_cache Use cache
     *
     * @return int Intified Cart output
     *
     * @deprecated Since 9.0 and will be removed in 10.0
     */
    public function simulateCarrierSelectedOutput($use_cache = true)
    {
        $delivery_option = $this->getDeliveryOption(null, false, $use_cache);

        if (count($delivery_option) > 1 || empty($delivery_option)) {
            return 0;
        }

        return (int) Cart::intifier(reset($delivery_option));
    }

    /**
     * Translate a string option_delivery identifier ('24,3,') in a int (3240002000).
     *
     * The  option_delivery identifier is a list of integers separated by a ','.
     * This method replace the delimiter by a sequence of '0'.
     * The size of this sequence is fixed by the first digit of the return
     *
     * @return string Intified value
     *
     * @deprecated Since 9.0 and will be removed in 10.0
     */
    public static function intifier($string, $delimiter = ',')
    {
        $elm = explode($delimiter, $string);
        $max = max($elm);

        return strlen($max) . implode(str_repeat('0', strlen($max) + 1), $elm);
    }

    /**
     * Translate an int option_delivery identifier (3240002000) in a string ('24,3,').
     *
     * @deprecated Since 9.0 and will be removed in 10.0
     */
    public static function desintifier($int, $delimiter = ',')
    {
        /** @var positive-int $delimiter_len */
        $delimiter_len = intval($int[0]);
        $int = strrev(substr($int, 1));
        $elm = explode(str_repeat('0', $delimiter_len + 1), $int);

        return strrev(implode($delimiter, $elm));
    }

    /**
     * Does the Cart use multiple Addresses?
     *
     * @return bool Indicates if the Cart uses multiple Addresses
     *
     * @deprecated Since 9.0 and will be removed in 10.0
     */
    public function isMultiAddressDelivery()
    {
        @trigger_error(sprintf(
            '%s is deprecated since 9.0 and will be removed in 10.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        return false;
    }

    /**
     * Get all delivery Addresses object for the current Cart.
     *
     * @deprecated Since 9.0 and will be removed in 10.0
     */
    public function getAddressCollection()
    {
        @trigger_error(sprintf(
            '%s is deprecated since 9.0 and will be removed in 10.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        if ((int) $this->id_address_delivery != 0) {
            return [(int) $this->id_address_delivery => new Address((int) $this->id_address_delivery)];
        }

        return [];
    }

    /**
     * Set the delivery option and Carrier ID, if there is only one Carrier.
     *
     * @param array $delivery_option Delivery option array
     */
    public function setDeliveryOption($delivery_option = null)
    {
        if (empty($delivery_option)) {
            $this->delivery_option = '';
            $this->id_carrier = 0;

            return;
        }
        Cache::clean('getContextualValue_*');
        $delivery_option_list = $this->getDeliveryOptionList(null, true);

        foreach ($delivery_option_list as $id_address => $options) {
            if (!isset($delivery_option[$id_address])) {
                foreach ($options as $key => $option) {
                    if ($option['is_best_price']) {
                        $delivery_option[$id_address] = $key;

                        break;
                    }
                }
            }
        }

        if (count($delivery_option) == 1) {
            $this->id_carrier = $this->getIdCarrierFromDeliveryOption($delivery_option);
        }

        $this->delivery_option = json_encode($delivery_option);

        // update auto cart rules
        CartRule::autoRemoveFromCart();
        CartRule::autoAddToCart();
    }

    /**
     * Get Carrier ID from Delivery Option.
     *
     * @param array $delivery_option Delivery options array
     *
     * @return int|mixed Carrier ID
     */
    protected function getIdCarrierFromDeliveryOption($delivery_option)
    {
        $delivery_option_list = $this->getDeliveryOptionList();
        foreach ($delivery_option as $key => $value) {
            if (isset($delivery_option_list[$key][$value])) {
                if (count($delivery_option_list[$key][$value]['carrier_list']) == 1) {
                    return current(array_keys($delivery_option_list[$key][$value]['carrier_list']));
                }
            }
        }

        return 0;
    }

    /**
     * Get the delivery option selected, or if no delivery option was selected,
     * the cheapest option for each address.
     *
     * @param Country|null $default_country Default country
     * @param bool $dontAutoSelectOptions Do not auto select delivery option
     * @param bool $use_cache Use cache
     *
     * @return array|false Delivery option
     */
    public function getDeliveryOption($default_country = null, $dontAutoSelectOptions = false, $use_cache = true)
    {
        $cache_id = (int) (is_object($default_country) ? $default_country->id : 0) . '-' . (int) $dontAutoSelectOptions;
        if (isset(static::$cacheDeliveryOption[$cache_id]) && $use_cache) {
            return static::$cacheDeliveryOption[$cache_id];
        }

        $delivery_option_list = $this->getDeliveryOptionList($default_country);

        // The delivery option was selected
        if (isset($this->delivery_option) && $this->delivery_option != '') {
            $delivery_option = json_decode($this->delivery_option, true);
            $validated = true;

            if (is_array($delivery_option)) {
                foreach ($delivery_option as $id_address => $key) {
                    if (!isset($delivery_option_list[$id_address][$key])) {
                        $validated = false;

                        break;
                    }
                }

                if ($validated) {
                    static::$cacheDeliveryOption[$cache_id] = $delivery_option;

                    return $delivery_option;
                }
            }
        }

        if ($dontAutoSelectOptions) {
            return false;
        }

        // No delivery option selected or delivery option selected is not valid, get the better for all options
        $delivery_option = [];
        foreach ($delivery_option_list as $id_address => $options) {
            foreach ($options as $key => $option) {
                if (Configuration::get('PS_CARRIER_DEFAULT') == -1 && $option['is_best_price']) {
                    $delivery_option[$id_address] = $key;

                    break;
                } elseif (Configuration::get('PS_CARRIER_DEFAULT') == -2 && $option['is_best_grade']) {
                    $delivery_option[$id_address] = $key;

                    break;
                } elseif ($option['unique_carrier'] && in_array(Configuration::get('PS_CARRIER_DEFAULT'), array_keys($option['carrier_list']))) {
                    $delivery_option[$id_address] = $key;

                    break;
                }
            }

            reset($options);
            if (!isset($delivery_option[$id_address])) {
                $delivery_option[$id_address] = key($options);
            }
        }

        static::$cacheDeliveryOption[$cache_id] = $delivery_option;

        return $delivery_option;
    }

    /**
     * Return shipping total for the cart.
     *
     * @param array|null $delivery_option Array of the delivery option for each address
     * @param bool $use_tax Use taxes
     * @param Country|null $default_country Default Country
     *
     * @return float Shipping total
     */
    public function getTotalShippingCost($delivery_option = null, $use_tax = true, Country $default_country = null)
    {
        if (isset(Context::getContext()->cookie->id_country)) {
            $default_country = new Country((int) Context::getContext()->cookie->id_country);
        }
        if (null === $delivery_option) {
            $delivery_option = $this->getDeliveryOption($default_country, false, false);
        }

        $_total_shipping = [
            'with_tax' => 0,
            'without_tax' => 0,
        ];
        $delivery_option_list = $this->getDeliveryOptionList($default_country);
        foreach ($delivery_option as $id_address => $key) {
            if (!isset($delivery_option_list[$id_address]) || !isset($delivery_option_list[$id_address][$key])) {
                continue;
            }

            $_total_shipping['with_tax'] += $delivery_option_list[$id_address][$key]['total_price_with_tax'];
            $_total_shipping['without_tax'] += $delivery_option_list[$id_address][$key]['total_price_without_tax'];
        }

        return ($use_tax) ? $_total_shipping['with_tax'] : $_total_shipping['without_tax'];
    }

    /**
     * Return shipping total of a specific carriers for the cart.
     *
     * @param int $id_carrier Carrier ID
     * @param array $delivery_option Array of the delivery option for each address
     * @param bool $useTax Use Taxes
     * @param Country|null $default_country Default Country
     * @param array|null $delivery_option Delivery options array
     *
     * @return float Shipping total
     */
    public function getCarrierCost($id_carrier, $useTax = true, Country $default_country = null, $delivery_option = null)
    {
        if (null === $delivery_option) {
            $delivery_option = $this->getDeliveryOption($default_country);
        }

        $total_shipping = 0;
        $delivery_option_list = $this->getDeliveryOptionList();

        foreach ($delivery_option as $id_address => $key) {
            if (!isset($delivery_option_list[$id_address]) || !isset($delivery_option_list[$id_address][$key])) {
                continue;
            }
            if (isset($delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier])) {
                if ($useTax) {
                    $total_shipping += $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['price_with_tax'];
                } else {
                    $total_shipping += $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['price_without_tax'];
                }
            }
        }

        return $total_shipping;
    }

    /**
     * Return package shipping cost.
     *
     * @param int $id_carrier Carrier ID (default : current carrier)
     * @param bool $use_tax
     * @param Country|null $default_country
     * @param array|null $product_list list of product concerned by the shipping.
     *                                 If null, all the product of the cart are used to calculate the shipping cost
     * @param int|null $id_zone Zone ID
     * @param bool $keepOrderPrices When true use the Order saved prices instead of the most recent ones from catalog (if Order exists)
     *
     * @return float|bool Shipping total, false if not possible to ship with the given carrier
     */
    public function getPackageShippingCost(
        $id_carrier = null,
        $use_tax = true,
        Country $default_country = null,
        $product_list = null,
        $id_zone = null,
        bool $keepOrderPrices = false
    ) {
        $shippingCost = $this->getPackageShippingCostValue(
            $id_carrier,
            $use_tax,
            $default_country,
            $product_list,
            $id_zone,
            $keepOrderPrices
        );

        Hook::exec(
            'actionCartGetPackageShippingCost',
            [
                'cart' => $this,
                'id_carrier' => $id_carrier,
                'use_tax' => $use_tax,
                'default_country' => $default_country,
                'product_list' => $product_list,
                'id_zone' => $id_zone,
                'keepOrderPrices' => $keepOrderPrices,
                'shippingCost' => &$shippingCost,
            ]
        );

        return $shippingCost;
    }

    /**
     * Return calculated package shipping cost.
     *
     * @param int $id_carrier Carrier ID (default : current carrier)
     * @param bool $use_tax
     * @param Country|null $default_country
     * @param array|null $product_list list of product concerned by the shipping.
     *                                 If null, all the product of the cart are used to calculate the shipping cost
     * @param int|null $id_zone Zone ID
     * @param bool $keepOrderPrices When true use the Order saved prices instead of the most recent ones from catalog (if Order exists)
     *
     * @return float|bool Shipping total, false if not possible to ship with the given carrier
     */
    protected function getPackageShippingCostValue(
        $id_carrier = null,
        $use_tax = true,
        Country $default_country = null,
        $product_list = null,
        $id_zone = null,
        bool $keepOrderPrices = false
    ) {
        if ($this->isVirtualCart()) {
            return 0;
        }

        if (!$default_country) {
            $default_country = Context::getContext()->country;
        }

        if (null === $product_list) {
            $products = $this->getProducts(false, false, null, true, $keepOrderPrices);
        } else {
            foreach ($product_list as $key => $value) {
                if ($value['is_virtual'] == 1) {
                    unset($product_list[$key]);
                }
            }
            $products = $product_list;
        }

        if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
            $address_id = (int) $this->id_address_invoice;
        } else {
            $address_id = (int) $this->id_address_delivery;
        }
        if (!Address::addressExists($address_id, true)) {
            $address_id = null;
        }

        if (null === $id_carrier && !empty($this->id_carrier)) {
            $id_carrier = (int) $this->id_carrier;
        }

        $cache_id = 'getPackageShippingCost_' . (int) $this->id . '_' . (int) $address_id . '_' . (int) $id_carrier . '_' . (int) $use_tax . '_' . (int) $default_country->id . '_' . (int) $id_zone;
        if ($products) {
            foreach ($products as $product) {
                $cache_id .= '_' . (int) $product['id_product'] . '_' . (int) $product['id_product_attribute'];
            }
        }

        if (Cache::isStored($cache_id)) {
            return Cache::retrieve($cache_id);
        }

        // Order total in default currency without fees
        $order_total = $this->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING, $product_list, $id_carrier, false, $keepOrderPrices);

        // Start with shipping cost at 0
        $shipping_cost = 0;
        // If no product added, return 0
        if (!count($products)) {
            Cache::store($cache_id, $shipping_cost);

            return $shipping_cost;
        }

        if (!isset($id_zone)) {
            // Get id zone
            if (isset($this->id_address_delivery)
                && $this->id_address_delivery
                && Customer::customerHasAddress($this->id_customer, $this->id_address_delivery)
            ) {
                $id_zone = Address::getZoneById((int) $this->id_address_delivery);
            } else {
                if (!Validate::isLoadedObject($default_country)) {
                    $default_country = new Country(
                        (int) Configuration::get('PS_COUNTRY_DEFAULT'),
                        (int) Configuration::get('PS_LANG_DEFAULT')
                    );
                }

                $id_zone = (int) $default_country->id_zone;
            }
        }

        if ($id_carrier && !$this->isCarrierInRange((int) $id_carrier, (int) $id_zone)) {
            $id_carrier = '';
        }

        if (empty($id_carrier) && $this->isCarrierInRange((int) Configuration::get('PS_CARRIER_DEFAULT'), (int) $id_zone)) {
            $id_carrier = (int) Configuration::get('PS_CARRIER_DEFAULT');
        }

        if (empty($id_carrier)) {
            if ((int) $this->id_customer) {
                $customer = new Customer((int) $this->id_customer);
                $result = Carrier::getCarriers((int) Configuration::get('PS_LANG_DEFAULT'), true, false, (int) $id_zone, $customer->getGroups());
                unset($customer);
            } else {
                $result = Carrier::getCarriers((int) Configuration::get('PS_LANG_DEFAULT'), true, false, (int) $id_zone);
            }

            foreach ($result as $k => $row) {
                if ($row['id_carrier'] == Configuration::get('PS_CARRIER_DEFAULT')) {
                    continue;
                }

                if (!isset(self::$_carriers[$row['id_carrier']])) {
                    self::$_carriers[$row['id_carrier']] = new Carrier((int) $row['id_carrier']);
                }

                /** @var Carrier $carrier */
                $carrier = self::$_carriers[$row['id_carrier']];

                $shipping_method = $carrier->getShippingMethod();
                // Get only carriers that are compliant with shipping method
                if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && $carrier->getMaxDeliveryPriceByWeight((int) $id_zone) === false)
                    || ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && $carrier->getMaxDeliveryPriceByPrice((int) $id_zone) === false)) {
                    unset($result[$k]);

                    continue;
                }

                // If out-of-range behavior carrier is set on "Desactivate carrier"
                if ($row['range_behavior']) {
                    $check_delivery_price_by_weight = Carrier::checkDeliveryPriceByWeight($row['id_carrier'], $this->getTotalWeight(), (int) $id_zone);

                    $check_delivery_price_by_price = Carrier::checkDeliveryPriceByPrice($row['id_carrier'], $order_total, (int) $id_zone, (int) $this->id_currency);

                    // Get only carriers that have a range compatible with cart
                    if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && $check_delivery_price_by_weight === false)
                        || ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && $check_delivery_price_by_price === false)) {
                        unset($result[$k]);

                        continue;
                    }
                }

                if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
                    $shipping = $carrier->getDeliveryPriceByWeight($this->getTotalWeight($product_list), (int) $id_zone);
                } else {
                    $shipping = $carrier->getDeliveryPriceByPrice($order_total, (int) $id_zone, (int) $this->id_currency);
                }

                if (!isset($min_shipping_price)) {
                    $min_shipping_price = $shipping;
                }

                if ($shipping <= $min_shipping_price) {
                    $id_carrier = (int) $row['id_carrier'];
                    $min_shipping_price = $shipping;
                }
            }
        }

        if (empty($id_carrier)) {
            $id_carrier = Configuration::get('PS_CARRIER_DEFAULT');
        }

        if (!isset(self::$_carriers[$id_carrier])) {
            self::$_carriers[$id_carrier] = new Carrier((int) $id_carrier, (int) Configuration::get('PS_LANG_DEFAULT'));
        }

        $carrier = self::$_carriers[$id_carrier];

        // No valid Carrier or $id_carrier <= 0 ?
        if (!Validate::isLoadedObject($carrier)) {
            Cache::store($cache_id, 0);

            return 0;
        }
        $shipping_method = $carrier->getShippingMethod();

        if (!$carrier->active) {
            Cache::store($cache_id, $shipping_cost);

            return $shipping_cost;
        }

        // Free fees if free carrier
        if ($carrier->is_free == 1) {
            Cache::store($cache_id, 0);

            return 0;
        }

        // Select carrier tax
        if ($use_tax && Configuration::get('PS_TAX')) {
            $address = Address::initialize((int) $address_id);

            if (Configuration::get('PS_ATCP_SHIPWRAP')) {
                // With PS_ATCP_SHIPWRAP, pre-tax price is deduced
                // from post tax price, so no $carrier_tax here
                // even though it sounds weird.
                $carrier_tax = 0;
            } else {
                $carrier_tax = $carrier->getTaxesRate($address);
            }
        }

        $configuration = Configuration::getMultiple([
            'PS_SHIPPING_FREE_PRICE',
            'PS_SHIPPING_HANDLING',
            'PS_SHIPPING_METHOD',
            'PS_SHIPPING_FREE_WEIGHT',
        ]);

        // Free fees
        $free_fees_price = 0;
        if (isset($configuration['PS_SHIPPING_FREE_PRICE'])) {
            $free_fees_price = Tools::convertPrice((float) $configuration['PS_SHIPPING_FREE_PRICE'], Currency::getCurrencyInstance((int) $this->id_currency));
        }
        $orderTotalwithDiscounts = $this->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING, null, null, false);
        if ($orderTotalwithDiscounts >= (float) ($free_fees_price) && (float) ($free_fees_price) > 0) {
            $shipping_cost = $this->getPackageShippingCostFromModule($carrier, $shipping_cost, $products);
            Cache::store($cache_id, $shipping_cost);

            return $shipping_cost;
        }

        if (isset($configuration['PS_SHIPPING_FREE_WEIGHT'])
            && $this->getTotalWeight() >= (float) $configuration['PS_SHIPPING_FREE_WEIGHT']
            && (float) $configuration['PS_SHIPPING_FREE_WEIGHT'] > 0) {
            $shipping_cost = $this->getPackageShippingCostFromModule($carrier, $shipping_cost, $products);
            Cache::store($cache_id, $shipping_cost);

            return $shipping_cost;
        }

        // Get shipping cost using correct method
        if ($carrier->range_behavior) {
            if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && Carrier::checkDeliveryPriceByWeight($carrier->id, $this->getTotalWeight(), (int) $id_zone) === false)
                || (
                    $shipping_method == Carrier::SHIPPING_METHOD_PRICE && Carrier::checkDeliveryPriceByPrice($carrier->id, $order_total, $id_zone, (int) $this->id_currency) === false
                )) {
                $shipping_cost += 0;
            } else {
                if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
                    $shipping_cost += $carrier->getDeliveryPriceByWeight($this->getTotalWeight($product_list), $id_zone);
                } else { // by price
                    $shipping_cost += $carrier->getDeliveryPriceByPrice($order_total, $id_zone, (int) $this->id_currency);
                }
            }
        } else {
            if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
                $shipping_cost += $carrier->getDeliveryPriceByWeight($this->getTotalWeight($product_list), $id_zone);
            } else {
                $shipping_cost += $carrier->getDeliveryPriceByPrice($order_total, $id_zone, (int) $this->id_currency);
            }
        }
        // Adding handling charges
        if (isset($configuration['PS_SHIPPING_HANDLING']) && $carrier->shipping_handling) {
            $shipping_cost += (float) $configuration['PS_SHIPPING_HANDLING'];
        }

        // Additional Shipping Cost per product
        foreach ($products as $product) {
            if (!$product['is_virtual']) {
                $shipping_cost += $product['additional_shipping_cost'] * $product['cart_quantity'];
            }
        }

        $shipping_cost = Tools::convertPrice($shipping_cost, Currency::getCurrencyInstance((int) $this->id_currency));

        //get external shipping cost from module
        $shipping_cost = $this->getPackageShippingCostFromModule($carrier, $shipping_cost, $products);
        if ($shipping_cost === false) {
            Cache::store($cache_id, false);

            return false;
        }

        if (Configuration::get('PS_ATCP_SHIPWRAP')) {
            if (!$use_tax) {
                // With PS_ATCP_SHIPWRAP, we deduce the pre-tax price from the post-tax
                // price. This is on purpose and required in Germany.
                $shipping_cost /= (1 + $this->getAverageProductsTaxRate());
            }
        } else {
            // Apply tax
            if ($use_tax && isset($carrier_tax)) {
                $shipping_cost *= 1 + ($carrier_tax / 100);
            }
        }

        $shipping_cost = (float) Tools::ps_round((float) $shipping_cost, Context::getContext()->getComputingPrecision());
        Cache::store($cache_id, $shipping_cost);

        return $shipping_cost;
    }

    /**
     * Ask the module the package shipping cost.
     *
     * If a carrier has been linked to a carrier module, we call it order to review the shipping costs.
     *
     * @param Carrier $carrier The concerned carrier (Your module may have several carriers)
     * @param float $shipping_cost The calculated shipping cost from the core, regarding package dimension and cart total
     * @param array $products The list of products
     *
     * @return bool|float The package price for the module (0 if free, false is disabled)
     */
    protected function getPackageShippingCostFromModule(Carrier $carrier, $shipping_cost, $products)
    {
        if (!$carrier->shipping_external) {
            return $shipping_cost;
        }

        /** @var CarrierModule $module */
        $module = Module::getInstanceByName($carrier->external_module_name);

        if (!Validate::isLoadedObject($module)) {
            return false;
        }

        if ($module->id_carrier) {
            $module->id_carrier = $carrier->id;
        }

        if (!$carrier->need_range) {
            return $module->getOrderShippingCostExternal($this);
        }

        if (method_exists($module, 'getPackageShippingCost')) {
            $shipping_cost = $module->getPackageShippingCost($this, $shipping_cost, $products);
        } else {
            $shipping_cost = $module->getOrderShippingCost($this, $shipping_cost);
        }

        return $shipping_cost;
    }

    /**
     * Return total Cart weight.
     *
     * @return float Total Cart weight
     */
    public function getTotalWeight($products = null)
    {
        if (null !== $products) {
            $total_weight = 0;
            foreach ($products as $product) {
                $total_weight += ($product['weight_attribute'] ?? $product['weight']) * $product['cart_quantity'];
            }

            return $total_weight;
        }

        if (!isset(self::$_totalWeight[$this->id])) {
            $this->updateProductWeight($this->id);
        }

        return self::$_totalWeight[(int) $this->id];
    }

    /**
     * Calculates and caches total weight for all products in cart with given ID.
     *
     * @param int $productId
     */
    protected function updateProductWeight($productId)
    {
        $productId = (int) $productId;

        // First, products with combinations
        if (Combination::isFeatureActive()) {
            $weight_product_with_attribute = Db::getInstance()->getValue('
                SELECT SUM((p.`weight` + pa.`weight`) * cp.`quantity`) as nb
                FROM `' . _DB_PREFIX_ . 'cart_product` cp
                LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (cp.`id_product` = p.`id_product`)
                LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa
                ON (cp.`id_product_attribute` = pa.`id_product_attribute`)
                WHERE (cp.`id_product_attribute` IS NOT NULL AND cp.`id_product_attribute` != 0)
                AND cp.`id_cart` = ' . $productId);
        } else {
            $weight_product_with_attribute = 0;
        }

        // Then the regular product
        $weight_product_without_attribute = Db::getInstance()->getValue('
            SELECT SUM(p.`weight` * cp.`quantity`) as nb
            FROM `' . _DB_PREFIX_ . 'cart_product` cp
            LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (cp.`id_product` = p.`id_product`)
            WHERE (cp.`id_product_attribute` IS NULL OR cp.`id_product_attribute` = 0)
            AND cp.`id_cart` = ' . $productId);

        // Finally, we need to add all customizations, because they can also add some weight
        $weight_cart_customizations = Db::getInstance()->getValue('
            SELECT SUM(cd.`weight` * c.`quantity`) FROM `' . _DB_PREFIX_ . 'customization` c
            LEFT JOIN `' . _DB_PREFIX_ . 'customized_data` cd ON (c.`id_customization` = cd.`id_customization`)
            WHERE c.`in_cart` = 1 AND c.`id_cart` = ' . $productId);

        self::$_totalWeight[$productId] = round(
            (float) $weight_product_with_attribute +
            (float) $weight_product_without_attribute +
            (float) $weight_cart_customizations,
            6
        );
    }

    /**
     * Return useful information about the cart for display purpose.
     * Products are splitted between paid ones and gift
     * Gift price and shipping (if shipping is free) are removed from Discounts
     * Any cart data modification for display purpose is made here.
     *
     * @return array Cart details
     */
    public function getSummaryDetails($id_lang = null, $refresh = false)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        $summary = $this->getRawSummaryDetails($id_lang, (bool) $refresh);

        return $this->alterSummaryForDisplay($summary, (bool) $refresh);
    }

    /**
     * Returns useful raw information about the cart.
     * Products, Discounts, Prices ... are returned in an array without any modification.
     *
     * @param int $id_lang
     * @param bool $refresh
     *
     * @return array Cart details
     *
     * @throws PrestaShopException
     * @throws LocalizationException
     */
    public function getRawSummaryDetails(int $id_lang, bool $refresh = false): array
    {
        $context = Context::getContext();

        $delivery = new Address((int) $this->id_address_delivery);
        $invoice = new Address((int) $this->id_address_invoice);

        // New layout system with personalization fields
        $formatted_addresses = [
            'delivery' => AddressFormat::getFormattedLayoutData($delivery),
            'invoice' => AddressFormat::getFormattedLayoutData($invoice),
        ];

        $base_total_tax_inc = $this->getOrderTotal(true);
        $base_total_tax_exc = $this->getOrderTotal(false);

        $total_tax = $base_total_tax_inc - $base_total_tax_exc;

        if ($total_tax < 0) {
            $total_tax = 0;
        }

        $products = $this->getProducts($refresh);

        foreach ($products as $key => &$product) {
            $product['price_without_quantity_discount'] = Product::getPriceStatic(
                $product['id_product'],
                !Product::getTaxCalculationMethod(),
                $product['id_product_attribute'],
                6,
                null,
                false,
                false
            );

            if ($product['reduction_type'] == 'amount') {
                $reduction = (!Product::getTaxCalculationMethod() ? (float) $product['price_wt'] : (float) $product['price']) - (float) $product['price_without_quantity_discount'];
                $product['reduction_formatted'] = Tools::getContextLocale($context)->formatPrice($reduction, $context->currency->iso_code);
            }
        }

        $total_shipping = $this->getTotalShippingCost();

        $summary = [
            'delivery' => $delivery,
            'delivery_state' => State::getNameById($delivery->id_state),
            'invoice' => $invoice,
            'invoice_state' => State::getNameById($invoice->id_state),
            'formattedAddresses' => $formatted_addresses,
            'products' => array_values($products),
            'discounts' => array_values($this->getCartRules()),
            'is_virtual_cart' => (int) $this->isVirtualCart(),
            'total_discounts' => $this->getOrderTotal(true, Cart::ONLY_DISCOUNTS),
            'total_discounts_tax_exc' => $this->getOrderTotal(false, Cart::ONLY_DISCOUNTS),
            'total_wrapping' => $this->getOrderTotal(true, Cart::ONLY_WRAPPING),
            'total_wrapping_tax_exc' => $this->getOrderTotal(false, Cart::ONLY_WRAPPING),
            'total_shipping' => $total_shipping,
            'total_shipping_tax_exc' => $this->getTotalShippingCost(null, false),
            'total_products_wt' => $this->getOrderTotal(true, Cart::ONLY_PRODUCTS),
            'total_products' => $this->getOrderTotal(false, Cart::ONLY_PRODUCTS),
            'total_price' => $base_total_tax_inc,
            'total_tax' => $total_tax,
            'total_price_without_tax' => $base_total_tax_exc,
            'is_multi_address_delivery' => false,
            'free_ship' => !$total_shipping,
            'carrier' => new Carrier($this->id_carrier, $id_lang),
        ];

        // An array [module_name => module_output] will be returned
        $hook = Hook::exec('actionCartSummary', $summary, null, true);
        if (is_array($hook)) {
            $summary = array_merge($summary, (array) array_shift($hook));
        }

        return $summary;
    }

    /**
     * Check if product quantities in Cart are available.
     *
     * @param bool $returnProductOnFailure Return the first found product with not enough quantity
     *
     * @return bool|array If all products are in stock: true; if not: either false or an array
     *                    containing the first found product which is not in stock in the
     *                    requested amount
     */
    public function checkQuantities($returnProductOnFailure = false)
    {
        if (Configuration::isCatalogMode() && !defined('_PS_ADMIN_DIR_')) {
            return false;
        }

        foreach ($this->getProducts() as $product) {
            if (
                !$product['active'] ||
                !$product['available_for_order'] ||
                (!$product['allow_oosp'] && $product['stock_quantity'] < $product['cart_quantity'])
            ) {
                return $returnProductOnFailure ? $product : false;
            }

            if (!$product['allow_oosp']) {
                $productQuantity = Product::getQuantity(
                    $product['id_product'],
                    $product['id_product_attribute'],
                    null,
                    $this,
                    false
                );
                if ($productQuantity < 0) {
                    return $returnProductOnFailure ? $product : false;
                }
            }
        }

        return true;
    }

    /**
     * Check if the product can be accessed by the Customer.
     *
     * @return bool Indicates if the Customer in the current Cart has access
     */
    public function checkProductsAccess()
    {
        if (Configuration::isCatalogMode()) {
            return true;
        }

        foreach ($this->getProducts() as $product) {
            if (!Product::checkAccessStatic($product['id_product'], $this->id_customer)) {
                return $product['id_product'];
            }
        }

        return false;
    }

    /**
     * Last abandoned Cart.
     *
     * @param int $id_customer Customer ID
     *
     * @return bool|int Last abandoned Cart ID
     *                  false if not found
     */
    public static function lastNoneOrderedCart($id_customer)
    {
        $sql = 'SELECT c.`id_cart`
                FROM ' . _DB_PREFIX_ . 'cart c
                WHERE NOT EXISTS (SELECT 1 FROM ' . _DB_PREFIX_ . 'orders o WHERE o.`id_cart` = c.`id_cart`
                                    AND o.`id_customer` = ' . (int) $id_customer . ')
                AND c.`id_customer` = ' . (int) $id_customer . '
                AND c.`id_cart` = (SELECT `id_cart` FROM `' . _DB_PREFIX_ . 'cart` c2 WHERE c2.`id_customer` = ' . (int) $id_customer . ' ORDER BY `id_cart` DESC LIMIT 1)
                AND c.`id_guest` != 0
                    ' . Shop::addSqlRestriction(Shop::SHARE_ORDER, 'c') . '
                ORDER BY c.`date_upd` DESC';

        if (!$id_cart = Db::getInstance()->getValue($sql)) {
            return false;
        }

        return (int) $id_cart;
    }

    /**
     * Check if cart contains only virtual products.
     *
     * @return bool true if is a virtual cart or false
     */
    public function isVirtualCart()
    {
        if (!ProductDownload::isFeatureActive()) {
            return false;
        }

        if (!isset(self::$_isVirtualCart[$this->id])) {
            if (!$this->hasProducts()) {
                $isVirtual = false;
            } else {
                $isVirtual = !$this->hasRealProducts();
            }

            self::$_isVirtualCart[$this->id] = $isVirtual;
        }

        return self::$_isVirtualCart[$this->id];
    }

    /**
     * Check if there's a product in the cart.
     *
     * @return bool
     */
    public function hasProducts()
    {
        return (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT 1 FROM ' . _DB_PREFIX_ . 'cart_product cp ' .
            'INNER JOIN ' . _DB_PREFIX_ . 'product p
                ON (p.id_product = cp.id_product) ' .
            'INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps
                ON (ps.id_shop = cp.id_shop AND ps.id_product = p.id_product) ' .
            'WHERE cp.id_cart=' . (int) $this->id
        );
    }

    /**
     * Return true if the current cart contains a real product.
     *
     * @return bool
     */
    public function hasRealProducts()
    {
        // Check for non-virtual products which are not packs
        $sql = 'SELECT 1 FROM %scart_product cp
            INNER JOIN %sproduct p ON (p.id_product = cp.id_product AND cache_is_pack = 0 and p.is_virtual = 0)
            INNER JOIN %sproduct_shop ps ON (ps.id_shop = cp.id_shop AND ps.id_product = p.id_product)
            WHERE cp.id_cart=%d';
        $sql = sprintf($sql, _DB_PREFIX_, _DB_PREFIX_, _DB_PREFIX_, $this->id);
        if ((bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql)) {
            return true;
        }

        // Check for non-virtual products which are in packs
        $sql = 'SELECT 1 FROM %scart_product cp
            INNER JOIN %spack pa ON (pa.id_product_pack = cp.id_product)
            INNER JOIN %sproduct p ON (p.id_product = pa.id_product_item AND p.is_virtual = 0)
            INNER JOIN %sproduct_shop ps ON (ps.id_shop = cp.id_shop AND ps.id_product = p.id_product)
            WHERE cp.id_cart=%d';
        $sql = sprintf($sql, _DB_PREFIX_, _DB_PREFIX_, _DB_PREFIX_, _DB_PREFIX_, $this->id);

        return (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    /**
     * Build cart object from provided id_order.
     *
     * @param int $id_order
     *
     * @return Cart|bool
     */
    public static function getCartByOrderId($id_order)
    {
        if ($id_cart = Cart::getCartIdByOrderId($id_order)) {
            return new Cart((int) $id_cart);
        }

        return false;
    }

    /**
     * Get Cart ID by Order ID.
     *
     * @param int $id_order Order ID
     *
     * @return int|bool Cart ID, false if not found
     */
    public static function getCartIdByOrderId($id_order)
    {
        $result = Db::getInstance()->getRow('SELECT `id_cart` FROM ' . _DB_PREFIX_ . 'orders WHERE `id_order` = ' . (int) $id_order);
        if (empty($result) || !array_key_exists('id_cart', $result)) {
            return false;
        }

        return $result['id_cart'];
    }

    /**
     * Add customer's text.
     *
     * @param int $id_product Product ID
     * @param int $index Customization field identifier as id_customization_field in table customization_field
     * @param int $type Customization type can be Product::CUSTOMIZE_FILE or Product::CUSTOMIZE_TEXTFIELD
     * @param string $text_value
     * @param bool $returnCustomizationId if true - returns the customizationId
     *
     * @return bool Always true
     */
    public function addTextFieldToProduct($id_product, $index, $type, $text_value, $returnCustomizationId = false)
    {
        return $this->_addCustomization(
            $id_product,
            0,
            $index,
            $type,
            $text_value,
            0,
            $returnCustomizationId
        );
    }

    /**
     * Add customer's pictures.
     *
     * @param int $id_product Product ID
     * @param int $index Customization field identifier as id_customization_field in table customization_field
     * @param int $type Customization type can be Product::CUSTOMIZE_FILE or Product::CUSTOMIZE_TEXTFIELD
     * @param string $file Filename
     * @param bool $returnCustomizationId if true - returns the customizationId
     *
     * @return bool Always true
     */
    public function addPictureToProduct($id_product, $index, $type, $file, $returnCustomizationId = false)
    {
        return $this->_addCustomization(
            $id_product,
            0,
            $index,
            $type,
            $file,
            0,
            $returnCustomizationId
        );
    }

    /**
     * Deletes a customization field. Only for customizations not added to cart yet.
     *
     * @param int $id_product Product ID
     * @param int $index Customization field identifier as id_customization_field in table customization_field
     *
     * @return bool
     */
    public function deleteCustomizationToProduct($id_product, $index)
    {
        // Try to find a customization for our cart, the given product, customization field that hasn't been added to cart yet
        $cust_data = Db::getInstance()->getRow(
            'SELECT cu.`id_customization`, cd.`index`, cd.`value`, cd.`type` FROM `' . _DB_PREFIX_ . 'customization` cu
            LEFT JOIN `' . _DB_PREFIX_ . 'customized_data` cd
            ON cu.`id_customization` = cd.`id_customization`
            WHERE cu.`id_cart` = ' . (int) $this->id . '
            AND cu.`id_product` = ' . (int) $id_product . '
            AND `index` = ' . (int) $index . '
            AND `in_cart` = 0'
        );

        if (!$cust_data) {
            return true;
        }

        $result = true;

        // Delete customization picture if necessary
        if ($cust_data['type'] == Product::CUSTOMIZE_FILE) {
            $result = !file_exists(_PS_UPLOAD_DIR_ . $cust_data['value']) || @unlink(_PS_UPLOAD_DIR_ . $cust_data['value']);
            $result = !($result && file_exists(_PS_UPLOAD_DIR_ . $cust_data['value'] . '_small')) || @unlink(_PS_UPLOAD_DIR_ . $cust_data['value'] . '_small');
        }

        // Delete the field that was requested for removal
        $result = $result && Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'customized_data`
            WHERE `id_customization` = ' . (int) $cust_data['id_customization'] . '
            AND `index` = ' . (int) $index
        );

        // And check if there are any more remaining fields for that customization
        $hasRemainingCustomData = Db::getInstance()->getValue(
            'SELECT 1 FROM `' . _DB_PREFIX_ . 'customized_data`
            WHERE `id_customization` = ' . (int) $cust_data['id_customization']
        );

        // If not, we will delete the whole customization, it will create a new one when customer customizes the product again
        if (!$hasRemainingCustomData) {
            $result = $result && Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'customization`
            WHERE `id_customization` = ' . (int) $cust_data['id_customization']
            );
        }

        return $result;
    }

    /**
     * Return customizations in this cart for a specified product.
     *
     * @param int $id_product Product ID
     * @param int|null $type Only return customization of this type, can be Product::CUSTOMIZE_FILE or Product::CUSTOMIZE_TEXTFIELD
     * @param bool $not_in_cart Only return customizations that are not in the cart already
     *
     * @return array Result from DB
     */
    public function getProductCustomization($id_product, $type = null, $not_in_cart = false)
    {
        if (!Customization::isFeatureActive()) {
            return [];
        }

        // If cart is not set, return nothing to prevent loading of other users data.
        // There should never be a customization with zero id_cart, but just to be sure.
        if (0 === (int) $this->id) {
            return [];
        }

        $result = Db::getInstance()->executeS(
            'SELECT cu.id_customization, cd.index, cd.value, cd.type, cu.in_cart, cu.quantity
            FROM `' . _DB_PREFIX_ . 'customization` cu
            LEFT JOIN `' . _DB_PREFIX_ . 'customized_data` cd ON (cu.`id_customization` = cd.`id_customization`)
            WHERE cu.id_cart = ' . (int) $this->id . '
            AND cu.id_product = ' . (int) $id_product .
            ($type === Product::CUSTOMIZE_FILE ? ' AND type = ' . (int) Product::CUSTOMIZE_FILE : '') .
            ($type === Product::CUSTOMIZE_TEXTFIELD ? ' AND type = ' . (int) Product::CUSTOMIZE_TEXTFIELD : '') .
            ($not_in_cart ? ' AND in_cart = 0' : '')
        );

        return $result;
    }

    /**
     * Get Carts by Customer ID.
     *
     * @param int $id_customer Customer ID
     * @param bool $with_order Only return Carts that have been converted into an Order
     *
     * @return array|false|mysqli_result|PDOStatement|resource|null DB result
     */
    public static function getCustomerCarts($id_customer, $with_order = true)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT *
        FROM ' . _DB_PREFIX_ . 'cart c
        WHERE c.`id_customer` = ' . (int) $id_customer . '
        ' . (!$with_order ? 'AND NOT EXISTS (SELECT 1 FROM ' . _DB_PREFIX_ . 'orders o WHERE o.`id_cart` = c.`id_cart`)' : '') . '
        ORDER BY c.`date_add` DESC');
    }

    /**
     * Duplicate this Cart in the database. This is mainly used by the "reorder" feature. Customer can go to his my account zone
     * and quickly create a new cart by using his previous order.
     *
     * @return array|bool Duplicated cart, with success bool
     */
    public function duplicate()
    {
        if (!Validate::isLoadedObject($this)) {
            return false;
        }

        $cart = new Cart($this->id);
        $cart->id = null;
        $cart->id_shop = $this->id_shop;
        $cart->id_shop_group = $this->id_shop_group;

        // If the original addresses no longer exist or are deleted, we will treat it like a new cart in this regard
        if (!Customer::customerHasAddress((int) $cart->id_customer, (int) $cart->id_address_delivery)) {
            $cart->id_address_delivery = (int) Address::getFirstCustomerAddressId((int) $cart->id_customer);
        }

        if (!Customer::customerHasAddress((int) $cart->id_customer, (int) $cart->id_address_invoice)) {
            $cart->id_address_invoice = (int) Address::getFirstCustomerAddressId((int) $cart->id_customer);
        }

        if ($cart->id_customer) {
            $cart->secure_key = Cart::$_customer->secure_key;
        }

        $cart->add();

        if (!Validate::isLoadedObject($cart)) {
            return false;
        }

        $success = true;
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'cart_product` WHERE `id_cart` = ' . (int) $this->id);

        $orderId = Order::getIdByCartId((int) $this->id);
        $product_gift = [];
        if ($orderId) {
            $product_gift = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT cr.`gift_product`, cr.`gift_product_attribute` FROM `' . _DB_PREFIX_ . 'cart_rule` cr LEFT JOIN `' . _DB_PREFIX_ . 'order_cart_rule` ocr ON (ocr.`id_order` = ' . (int) $orderId . ') WHERE ocr.`deleted` = 0 AND ocr.`id_cart_rule` = cr.`id_cart_rule`');
        }

        // Customized products: duplicate customizations before products so that we get new id_customizations
        $customs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT *
            FROM ' . _DB_PREFIX_ . 'customization c
            LEFT JOIN ' . _DB_PREFIX_ . 'customized_data cd ON cd.id_customization = c.id_customization
            WHERE c.id_cart = ' . (int) $this->id
        );

        // Get datas from customization table
        $customs_by_id = [];
        foreach ($customs as $custom) {
            if (!isset($customs_by_id[$custom['id_customization']])) {
                $customs_by_id[$custom['id_customization']] = [
                    'id_product_attribute' => $custom['id_product_attribute'],
                    'id_product' => $custom['id_product'],
                ];
            }
        }

        // Insert new customizations
        $custom_ids = [];
        foreach ($customs_by_id as $customization_id => $val) {
            Db::getInstance()->execute(
                'INSERT INTO `' . _DB_PREFIX_ . 'customization` (id_cart, id_product_attribute, id_product, `id_address_delivery`, `in_cart`)
                VALUES(' . (int) $cart->id . ', ' . (int) $val['id_product_attribute'] . ', ' . (int) $val['id_product'] . ', 0, 1)'
            );
            $custom_ids[$customization_id] = Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();
        }

        // Insert customized_data
        if (count($customs)) {
            $first = true;
            $sql_custom_data = 'INSERT INTO ' . _DB_PREFIX_ . 'customized_data (`id_customization`, `type`, `index`, `value`, `id_module`, `price`, `weight`) VALUES ';
            foreach ($customs as $custom) {
                if (!$first) {
                    $sql_custom_data .= ',';
                } else {
                    $first = false;
                }

                $customized_value = $custom['value'];

                if ((int) $custom['type'] == Product::CUSTOMIZE_FILE) {
                    $customized_value = md5(uniqid((string) mt_rand(0, mt_getrandmax()), true));
                    Tools::copy(_PS_UPLOAD_DIR_ . $custom['value'], _PS_UPLOAD_DIR_ . $customized_value);
                    Tools::copy(_PS_UPLOAD_DIR_ . $custom['value'] . '_small', _PS_UPLOAD_DIR_ . $customized_value . '_small');
                }

                $sql_custom_data .= '(' . (int) $custom_ids[$custom['id_customization']] . ', ' . (int) $custom['type'] . ', ' .
                    (int) $custom['index'] . ', \'' . pSQL($customized_value) . '\', ' .
                    (int) $custom['id_module'] . ', ' . (float) $custom['price'] . ', ' . (float) $custom['weight'] . ')';
            }
            Db::getInstance()->execute($sql_custom_data);
        }

        foreach ($products as $product) {
            foreach ($product_gift as $gift) {
                if (isset($gift['gift_product'], $gift['gift_product_attribute']) && (int) $gift['gift_product'] == (int) $product['id_product'] && (int) $gift['gift_product_attribute'] == (int) $product['id_product_attribute']) {
                    $product['quantity'] = (int) $product['quantity'] - 1;
                }
            }

            $id_customization = (int) $product['id_customization'];

            $success &= $cart->updateQty(
                (int) $product['quantity'],
                (int) $product['id_product'],
                (int) $product['id_product_attribute'],
                isset($custom_ids[$id_customization]) ? (int) $custom_ids[$id_customization] : 0,
                'up',
                0,
                new Shop((int) $cart->id_shop),
                false,
                false
            );
        }

        return ['cart' => $cart, 'success' => $success];
    }

    /**
     * Get Cart rows from DB for the webservice.
     *
     * @return array|false|mysqli_result|PDOStatement|resource|null DB result
     */
    public function getWsCartRows()
    {
        return Db::getInstance()->executeS(
            'SELECT id_product, id_product_attribute, quantity, id_address_delivery, id_customization
            FROM `' . _DB_PREFIX_ . 'cart_product`
            WHERE id_cart = ' . (int) $this->id . ' AND id_shop = ' . (int) Context::getContext()->shop->id
        );
    }

    /**
     * Insert cart rows from webservice.
     *
     * @param array $values Values from webservice
     *
     * @return bool Whether the values have been successfully inserted
     * @todo: This function always returns true, make it depend on actual result of DB query
     */
    public function setWsCartRows($values)
    {
        if ($this->deleteAssociations()) {
            $query = 'INSERT INTO `' . _DB_PREFIX_ . 'cart_product`(`id_cart`, `id_product`, `id_product_attribute`, `id_address_delivery`, `id_customization`, `quantity`, `date_add`, `id_shop`) VALUES ';

            foreach ($values as $value) {
                $query .= '(' . (int) $this->id . ', ' . (int) $value['id_product'] . ', ' .
                    (isset($value['id_product_attribute']) ? (int) $value['id_product_attribute'] : 'NULL') . ', ' .
                    '0, ' .
                    (isset($value['id_customization']) ? (int) $value['id_customization'] : 0) . ', ' .
                    (int) $value['quantity'] . ', NOW(), ' . (int) Context::getContext()->shop->id . '),';
            }

            Db::getInstance()->execute(rtrim($query, ','));
        }

        return true;
    }

    /**
     * Set delivery Address of a Product in the Cart.
     *
     * @param int $id_product Product ID
     * @param int $id_product_attribute Product Attribute ID
     * @param int $old_id_address_delivery Old delivery Address ID
     * @param int $new_id_address_delivery New delivery Address ID
     *
     * @return bool Whether the delivery Address of the product in the Cart has been successfully updated
     *
     * @deprecated Since 9.0 and will be removed in 10.0
     */
    public function setProductAddressDelivery($id_product, $id_product_attribute, $old_id_address_delivery, $new_id_address_delivery)
    {
        @trigger_error(sprintf(
            '%s is deprecated since 9.0 and will be removed in 10.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        return true;
    }

    /**
     * Set customized data of a product.
     *
     * @param Product $product Referenced Product object
     * @param array $customized_datas Customized data
     */
    public function setProductCustomizedDatas(&$product, $customized_datas)
    {
        $product['customizedDatas'] = null;
        if (isset($customized_datas[$product['id_product']][$product['id_product_attribute']])) {
            $product['customizedDatas'] = $customized_datas[$product['id_product']][$product['id_product_attribute']];
        }
    }

    /**
     * Duplicate Product.
     *
     * @param int $id_product Product ID
     * @param int $id_product_attribute Product Attribute ID
     * @param int $id_address_delivery Delivery Address ID
     * @param int $new_id_address_delivery New Delivery Address ID
     * @param int $quantity Quantity value
     * @param bool $keep_quantity Keep the quantity, do not reset if true
     *
     * @return bool Whether the product has been successfully duplicated
     *
     * @deprecated Since 9.0 and will be removed in 10.0, product cannot be in the cart twice.
     */
    public function duplicateProduct(
        $id_product,
        $id_product_attribute,
        $id_address_delivery,
        $new_id_address_delivery,
        $quantity = 1,
        $keep_quantity = false
    ) {
        @trigger_error(sprintf(
            '%s is deprecated since 9.0 and will be removed in 10.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        return false;
    }

    /**
     * Update products cart address delivery with the address delivery of the cart.
     *
     * @deprecated Since 9.0 and will be removed in 10.0
     */
    public function setNoMultishipping()
    {
        @trigger_error(sprintf(
            '%s is deprecated since 9.0 and will be removed in 10.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        return;
    }

    /**
     * Set an address to all products on the cart without address delivery.
     *
     * @deprecated Since 9.0 and will be removed in 10.0
     */
    public function autosetProductAddress()
    {
        @trigger_error(sprintf(
            '%s is deprecated since 9.0 and will be removed in 10.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        return;
    }

    public function deleteAssociations()
    {
        return Db::getInstance()->execute('
                DELETE FROM `' . _DB_PREFIX_ . 'cart_product`
                WHERE `id_cart` = ' . (int) $this->id) !== false;
    }

    /**
     * isCarrierInRange.
     *
     * Check if the specified carrier is in range
     *
     * @id_carrier int
     * @id_zone int
     */
    public function isCarrierInRange($id_carrier, $id_zone)
    {
        $carrier = new Carrier((int) $id_carrier, (int) Configuration::get('PS_LANG_DEFAULT'));
        $shipping_method = $carrier->getShippingMethod();
        if (!$carrier->range_behavior) {
            return true;
        }

        if ($shipping_method == Carrier::SHIPPING_METHOD_FREE) {
            return true;
        }

        $check_delivery_price_by_weight = Carrier::checkDeliveryPriceByWeight(
            (int) $id_carrier,
            $this->getTotalWeight(),
            $id_zone
        );
        if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && $check_delivery_price_by_weight !== false) {
            return true;
        }

        $check_delivery_price_by_price = Carrier::checkDeliveryPriceByPrice(
            (int) $id_carrier,
            $this->getOrderTotal(
                true,
                Cart::BOTH_WITHOUT_SHIPPING
            ),
            $id_zone,
            (int) $this->id_currency
        );
        if ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && $check_delivery_price_by_price !== false) {
            return true;
        }

        return false;
    }

    /**
     * Is the Cart from a guest?
     *
     * @param int $id_cart Cart ID
     *
     * @return bool True if the Cart has been made by a guest Customer
     */
    public static function isGuestCartByCartId($id_cart)
    {
        if (!(int) $id_cart) {
            return false;
        }

        return (bool) Db::getInstance()->getValue('
            SELECT `is_guest`
            FROM `' . _DB_PREFIX_ . 'customer` cu
            LEFT JOIN `' . _DB_PREFIX_ . 'cart` ca ON (ca.`id_customer` = cu.`id_customer`)
            WHERE ca.`id_cart` = ' . (int) $id_cart);
    }

    /**
     * Checks if all products of the cart are still available in the current state. They might have been converted to another
     * type of product since then, ordering disabled or deactivated.
     *
     * @return bool false if one of the product not publicly orderable anymore
     */
    public function checkAllProductsAreStillAvailableInThisState()
    {
        foreach ($this->getProducts(false, false, null, false) as $product) {
            $currentProduct = new Product();
            $currentProduct->hydrate($product);

            // Check if the product combinations state is still valid
            if ($currentProduct->hasAttributes() && $product['id_product_attribute'] === '0') {
                return false;
            }

            // Check if product is still active and possible to order
            if (!$product['active'] || !$product['available_for_order']) {
                return false;
            }
        }

        return true;
    }

    /**
     * Are all products of the Cart in stock?
     *
     * @param bool $ignoreVirtual Ignore virtual products
     *
     * @since 1.5.0
     *
     * @return bool False if not all products in the cart are in stock
     */
    public function isAllProductsInStock($ignoreVirtual = false)
    {
        $productOutOfStock = 0;
        $productInStock = 0;

        foreach ($this->getProducts(false, false, null, false) as $product) {
            if ($ignoreVirtual && $product['is_virtual']) {
                continue;
            }
            $idProductAttribute = !empty($product['id_product_attribute']) ? $product['id_product_attribute'] : null;
            $availableOutOfStock = Product::isAvailableWhenOutOfStock($product['out_of_stock']);
            $productQuantity = Product::getQuantity(
                $product['id_product'],
                $idProductAttribute,
                null,
                $this,
                false
            );

            if ($productQuantity < 0 && !$availableOutOfStock) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks that all products in cart have minimal required quantities
     *
     * @return bool
     */
    public function checkAllProductsHaveMinimalQuantities()
    {
        $productList = $this->getProducts(true);
        foreach ($productList as $product) {
            if ($product['minimal_quantity'] > $product['cart_quantity']) {
                return false;
            }
        }

        return true;
    }

    /**
     * Set flag to split lines of products given away and also manually added to cart.
     */
    protected function splitGiftsProductsQuantity()
    {
        $this->shouldSplitGiftProductsQuantity = true;
        $this->_products = null;

        return $this;
    }

    /**
     * Set flag to merge lines of products given away and also manually added to cart.
     */
    protected function mergeGiftsProductsQuantity()
    {
        $this->shouldSplitGiftProductsQuantity = false;
        $this->_products = null;

        return $this;
    }

    protected function excludeGiftsDiscountFromTotal()
    {
        $this->shouldExcludeGiftsDiscount = true;
        $this->_products = null;

        return $this;
    }

    protected function includeGiftsDiscountInTotal()
    {
        $this->shouldExcludeGiftsDiscount = false;
        $this->_products = null;

        return $this;
    }

    /**
     * Get products with gifts and manually added occurrences separated.
     *
     * @return array|null
     */
    public function getProductsWithSeparatedGifts()
    {
        $products = $this->splitGiftsProductsQuantity()
            ->getProducts($refresh = true);
        $this->mergeGiftsProductsQuantity();

        return $products;
    }

    /**
     * @return Country
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getTaxCountry(): Country
    {
        $taxAddressType = Configuration::get('PS_TAX_ADDRESS_TYPE');
        $taxAddressId = property_exists($this, $taxAddressType) ? $this->{$taxAddressType} : $this->id_address_delivery;
        $taxAddress = new Address($taxAddressId);

        return new Country($taxAddress->id_country);
    }

    /**
     * Alter raw cart details to adapt to display use case.
     *
     * @param array $summary
     * @param bool $refresh
     *
     * @return array
     */
    private function alterSummaryForDisplay(array $summary, bool $refresh = false): array
    {
        $context = Context::getContext();
        $currency = new Currency($this->id_currency);

        $gift_products = [];
        $products = $summary['products'];
        $cart_rules = $summary['discounts'];
        $total_shipping = $summary['total_shipping'];
        $total_discounts = $summary['total_discounts'];
        $total_discounts_tax_exc = $summary['total_discounts_tax_exc'];
        $total_shipping_tax_exc = $summary['total_shipping_tax_exc'];
        $total_products = $summary['total_products'];
        $total_products_wt = $summary['total_products_wt'];

        // The cart content is altered for display
        foreach ($cart_rules as &$cart_rule) {
            // If the cart rule is automatic (without any code) and include free shipping, it should not be displayed as a cart rule but only set the shipping cost to 0
            if ($cart_rule['free_shipping'] && (empty($cart_rule['code']) || preg_match('/^' . CartRule::BO_ORDER_CODE_PREFIX . '[0-9]+/', $cart_rule['code']))) {
                $cart_rule['value_real'] -= $total_shipping;
                $cart_rule['value_tax_exc'] -= $total_shipping_tax_exc;
                $cart_rule['value_real'] = Tools::ps_round($cart_rule['value_real'], (int) $context->currency->decimals * Context::getContext()->getComputingPrecision());
                $cart_rule['value_tax_exc'] = Tools::ps_round($cart_rule['value_tax_exc'], (int) $context->currency->decimals * Context::getContext()->getComputingPrecision());
                if ($total_discounts > $cart_rule['value_real']) {
                    $total_discounts -= $total_shipping;
                }
                if ($total_discounts_tax_exc > $cart_rule['value_tax_exc']) {
                    $total_discounts_tax_exc -= $total_shipping_tax_exc;
                }

                // Update total shipping
                $total_shipping = 0;
                $total_shipping_tax_exc = 0;
            }

            if ($cart_rule['gift_product']) {
                foreach ($products as $key => &$product) {
                    if (empty($product['is_gift']) && $product['id_product'] == $cart_rule['gift_product'] && $product['id_product_attribute'] == $cart_rule['gift_product_attribute']) {
                        // Update total products
                        $total_products_wt = Tools::ps_round($total_products_wt - $product['price_wt'], (int) $context->currency->decimals * Context::getContext()->getComputingPrecision());
                        $total_products = Tools::ps_round($total_products - $product['price'], (int) $context->currency->decimals * Context::getContext()->getComputingPrecision());

                        // Update total discounts
                        $total_discounts = Tools::ps_round($total_discounts - $product['price_wt'], (int) $context->currency->decimals * Context::getContext()->getComputingPrecision());
                        $total_discounts_tax_exc = Tools::ps_round($total_discounts_tax_exc - $product['price'], (int) $context->currency->decimals * Context::getContext()->getComputingPrecision());

                        // Update cart rule value
                        $cart_rule['value_real'] = Tools::ps_round($cart_rule['value_real'] - $product['price_wt'], (int) $context->currency->decimals * Context::getContext()->getComputingPrecision());
                        $cart_rule['value_tax_exc'] = Tools::ps_round($cart_rule['value_tax_exc'] - $product['price'], (int) $context->currency->decimals * Context::getContext()->getComputingPrecision());

                        // Update product quantity
                        $product['total_wt'] = Tools::ps_round($product['total_wt'] - $product['price_wt'], (int) $currency->decimals * Context::getContext()->getComputingPrecision());
                        $product['total'] = Tools::ps_round($product['total'] - $product['price'], (int) $currency->decimals * Context::getContext()->getComputingPrecision());
                        --$product['cart_quantity'];

                        if (!$product['cart_quantity']) {
                            unset($products[$key]);
                        }

                        // Add a new product line
                        $gift_product = $product;
                        $gift_product['cart_quantity'] = 1;
                        $gift_product['price'] = 0;
                        $gift_product['price_wt'] = 0;
                        $gift_product['total_wt'] = 0;
                        $gift_product['total'] = 0;
                        $gift_product['is_gift'] = true;
                        $gift_products[] = $gift_product;

                        break; // One gift product per cart rule
                    }
                }
            }
        }

        foreach ($cart_rules as $key => &$cart_rule) {
            if (((float) $cart_rule['value_real'] == 0 && (int) $cart_rule['free_shipping'] == 0)) {
                unset($cart_rules[$key]);
            }
        }

        $summary['discounts'] = $cart_rules;
        $summary['total_shipping'] = $total_shipping;
        $summary['total_discounts'] = $total_discounts;
        $summary['total_discounts_tax_exc'] = $total_discounts_tax_exc;
        $summary['total_shipping_tax_exc'] = $total_shipping_tax_exc;
        $summary['total_products'] = $total_products;
        $summary['total_products_wt'] = $total_products_wt;
        $summary['products'] = $products;
        $summary['gift_products'] = $gift_products;

        return $summary;
    }

    /**
     * @return float
     */
    public function getCartTotalPrice()
    {
        $summary = $this->getSummaryDetails();

        $id_order = (int) Order::getIdByCartId($this->id);
        $order = new Order($id_order);

        if (Validate::isLoadedObject($order)) {
            $taxCalculationMethod = $order->getTaxCalculationMethod();
        } else {
            $taxCalculationMethod = Group::getPriceDisplayMethod(Group::getCurrent()->id);
        }

        return $taxCalculationMethod == PS_TAX_EXC ?
            $summary['total_price_without_tax'] :
            $summary['total_price'];
    }

    /**
     * Returns quantities in cart of given product ID, not taking combinations or customizations into consideration.
     *
     * @param int $idProduct Product ID
     *
     * @return array quantity index     : number of product in cart without counting those of pack in cart
     *               deep_quantity index: number of product in cart counting those of pack in cart
     */
    public function getProductQuantityInAllVariants($idProduct)
    {
        // We will build 2 separate queries and merge their results together
        // First query selects the standalone quantity of the product
        $firstUnionSql = 'SELECT
          SUM(cp.`quantity`) as standalone_quantity,
          0 as pack_quantity
          FROM `' . _DB_PREFIX_ . 'cart_product` cp
          WHERE cp.`id_cart` = ' . (int) $this->id . ' AND cp.`id_product` = ' . (int) $idProduct;

        // Second query selects quantity of this products in packs
        $secondUnionSql = 'SELECT
          0 as standalone_quantity,
          SUM(cp.`quantity` * p.`quantity`) as pack_quantity
          FROM `' . _DB_PREFIX_ . 'cart_product` cp
          INNER JOIN `' . _DB_PREFIX_ . 'pack` p ON cp.`id_product` = p.`id_product_pack`
          WHERE cp.`id_cart` = ' . (int) $this->id . ' AND p.`id_product_item` = ' . (int) $idProduct;

        // Construct the final SQL that will join the results of these two queries
        $parentSql = 'SELECT
            COALESCE(SUM(pack_quantity), 0) as pack_quantity,
            COALESCE(SUM(standalone_quantity), 0) as standalone_quantity
          FROM (' . $firstUnionSql . ' UNION ' . $secondUnionSql . ') as q';

        return Db::getInstance()->getRow($parentSql);
    }
}
