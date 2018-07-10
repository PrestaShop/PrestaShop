<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\PrestaShop\Adapter\AddressFactory;
use PrestaShop\PrestaShop\Adapter\Cache\CacheAdapter;
use PrestaShop\PrestaShop\Adapter\Customer\CustomerDataProvider;
use PrestaShop\PrestaShop\Adapter\Group\GroupDataProvider;
use PrestaShop\PrestaShop\Adapter\Product\PriceCalculator;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Adapter\Database;
use PrestaShop\PrestaShop\Core\Cart\Calculator;
use PrestaShop\PrestaShop\Core\Cart\CartRow;
use PrestaShop\PrestaShop\Core\Cart\CartRuleData;

class CartCore extends ObjectModel
{
    public $id;

    public $id_shop_group;

    public $id_shop;

    /** @var int Customer delivery address ID */
    public $id_address_delivery;

    /** @var int Customer invoicing address ID */
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
    public $recyclable = 0;

    /** @var bool True if the customer wants a gift wrapping */
    public $gift = 0;

    /** @var string Gift message if specified */
    public $gift_message;

    /** @var bool Mobile Theme */
    public $mobile_theme;

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

    protected static $_nbProducts = array();
    protected static $_isVirtualCart = array();

    protected $_products = null;
    protected static $_totalWeight = array();
    protected $_taxCalculationMethod = PS_TAX_EXC;
    protected static $_carriers = null;
    protected static $_taxes_rate = null;
    protected static $_attributesLists = array();

    /** @var Customer|null */
    protected static $_customer = null;

    protected static $cacheDeliveryOption = array();
    protected static $cacheNbPackages = array();
    protected static $cachePackageList = array();
    protected static $cacheDeliveryOptionList = array();
    protected static $cacheMultiAddressDelivery = array();

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'cart',
        'primary' => 'id_cart',
        'fields' => array(
            'id_shop_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_address_delivery' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_address_invoice' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_carrier' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_currency' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_guest' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_lang' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'recyclable' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'gift' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'gift_message' => array('type' => self::TYPE_STRING, 'validate' => 'isMessage'),
            'mobile_theme' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'delivery_option' => array('type' => self::TYPE_STRING),
            'secure_key' => array('type' => self::TYPE_STRING, 'size' => 32),
            'allow_seperated_package' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /** @var array $webserviceParameters Web service parameters */
    protected $webserviceParameters = array(
        'fields' => array(
            'id_address_delivery' => array('xlink_resource' => 'addresses'),
            'id_address_invoice' => array('xlink_resource' => 'addresses'),
            'id_currency' => array('xlink_resource' => 'currencies'),
            'id_customer' => array('xlink_resource' => 'customers'),
            'id_guest' => array('xlink_resource' => 'guests'),
            'id_lang' => array('xlink_resource' => 'languages'),
        ),
        'associations' => array(
            'cart_rows' => array(
                'resource' => 'cart_row',
                'virtual_entity' => true,
                'fields' => array(
                    'id_product' => array('required' => true, 'xlink_resource' => 'products'),
                    'id_product_attribute' => array('required' => true, 'xlink_resource' => 'combinations'),
                    'id_address_delivery' => array('required' => true, 'xlink_resource' => 'addresses'),
                    'quantity' => array('required' => true),
                ),
            ),
        ),
    );

    protected $configuration;

    protected $addressFactory;

    protected $shouldSplitGiftProductsQuantity = false;

    protected $shouldExcludeGiftsDiscount = false;

    const ONLY_PRODUCTS = 1;
    const ONLY_DISCOUNTS = 2;
    const BOTH = 3;
    const BOTH_WITHOUT_SHIPPING = 4;
    const ONLY_SHIPPING = 5;
    const ONLY_WRAPPING = 6;

    /** @deprecated since 1.7 **/
    const ONLY_PRODUCTS_WITHOUT_SHIPPING = 7;
    const ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING = 8;

    /**
     * CartCore constructor.
     *
     * @param int|null $id      Cart ID
     *                          null = new Cart
     * @param int|null $idLang  Language ID
     *                          null = Language ID of current Context
     */
    public function __construct($id = null, $idLang = null)
    {
        parent::__construct($id);

        if (!is_null($idLang)) {
            $this->id_lang = (int)(Language::getLanguage($idLang) !== false) ? $idLang : Configuration::get('PS_LANG_DEFAULT');
        }

        if ($this->id_customer) {
            if (isset(Context::getContext()->customer) && Context::getContext()->customer->id == $this->id_customer) {
                $customer = Context::getContext()->customer;
            } else {
                $customer = new Customer((int)$this->id_customer);
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
        static::$_nbProducts      = array();
        static::$_isVirtualCart   = array();
        static::$_totalWeight     = array();
        static::$_carriers        = null;
        static::$_taxes_rate      = null;
        static::$_attributesLists = array();
        static::$_customer        = null;
        static::$cacheDeliveryOption = array();
        static::$cacheNbPackages = array();
        static::$cachePackageList = array();
        static::$cacheDeliveryOptionList = array();
        static::$cacheMultiAddressDelivery = array();
    }

    /**
     * Set Tax calculation method
     */
    public function setTaxCalculationMethod()
    {
        $this->_taxCalculationMethod = Group::getPriceDisplayMethod(Group::getCurrent()->id);
    }

    /**
     * Adds current Cart as a new Object to the database
     *
     * @param bool $autoDate   Automatically set `date_upd` and `date_add` columns
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Whether the Cart has been successfully added
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function add($autoDate = true, $nullValues = false)
    {
        if (!$this->id_lang) {
            $this->id_lang = Configuration::get('PS_LANG_DEFAULT');
        }
        if (!$this->id_shop) {
            $this->id_shop = Context::getContext()->shop->id;
        }

        $return = parent::add($autoDate, $nullValues);
        Hook::exec('actionCartSave');

        return $return;
    }

    /**
     * Updates the current object in the database
     *
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Whether the Cart has been successfully updated
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
        Hook::exec('actionCartSave');

        return $return;
    }

    /**
     * Update the Address ID of the Cart
     *
     * @param int $id_address     Current Address ID to change
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

        $sql = 'UPDATE `'._DB_PREFIX_.'cart_product`
        SET `id_address_delivery` = '.(int)$id_address_new.'
        WHERE  `id_cart` = '.(int)$this->id.'
            AND `id_address_delivery` = '.(int)$id_address;
        Db::getInstance()->execute($sql);

        $sql = 'UPDATE `'._DB_PREFIX_.'customization`
            SET `id_address_delivery` = '.(int)$id_address_new.'
            WHERE  `id_cart` = '.(int)$this->id.'
                AND `id_address_delivery` = '.(int)$id_address;
        Db::getInstance()->execute($sql);
    }

    /**
     * Deletes current Cart from the database
     *
     * @return bool True if delete was successful
     * @throws PrestaShopException
     */
    public function delete()
    {
        if ($this->OrderExists()) { //NOT delete a cart which is associated with an order
            return false;
        }

        $uploaded_files = Db::getInstance()->executeS(
            'SELECT cd.`value`
            FROM `'._DB_PREFIX_.'customized_data` cd
            INNER JOIN `'._DB_PREFIX_.'customization` c ON (cd.`id_customization`= c.`id_customization`)
            WHERE cd.`type`= 0 AND c.`id_cart`='.(int)$this->id
        );

        foreach ($uploaded_files as $must_unlink) {
            unlink(_PS_UPLOAD_DIR_.$must_unlink['value'].'_small');
            unlink(_PS_UPLOAD_DIR_.$must_unlink['value']);
        }

        Db::getInstance()->execute(
            'DELETE FROM `'._DB_PREFIX_.'customized_data`
            WHERE `id_customization` IN (
                SELECT `id_customization`
                FROM `'._DB_PREFIX_.'customization`
                WHERE `id_cart`='.(int)$this->id.'
            )'
        );

        Db::getInstance()->execute(
            'DELETE FROM `'._DB_PREFIX_.'customization`
            WHERE `id_cart` = '.(int)$this->id
        );

        if (!Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_cart_rule` WHERE `id_cart` = '.(int)$this->id)
            || !Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_product` WHERE `id_cart` = '.(int)$this->id)) {
            return false;
        }

        return parent::delete();
    }

    /**
     * Calculate average Tax rate in Cart
     *
     * @param mixed $cart Cart ID or Cart Object
     *
     * @return float Average Tax used in Cart
     */
    public static function getTaxesAverageUsed($cart)
    {
        if (!is_object($cart)) {
            $cart = new Cart((int)$cart);
        }
        if (!Validate::isLoadedObject($cart)) {
            die(Tools::displayError());
        }

        if (!Configuration::get('PS_TAX')) {
            return 0;
        }

        $products = $cart->getProducts();
        $total_products_average = 0;
        $ratio_tax = 0;

        if (!count($products)) {
            return 0;
        }

        foreach ($products as $product) {
            // products refer to the cart details

            if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
                $address_id = (int)$cart->id_address_invoice;
            } else {
                $address_id = (int)$product['id_address_delivery'];
            } // Get delivery address of the product from the cart
            if (!Address::addressExists($address_id)) {
                $address_id = null;
            }

            $total_products_average += $product['total_wt'];
            $ratio_tax += $product['total_wt'] * Tax::getProductTaxRate(
                    (int)$product['id_product'],
                    (int)$address_id
                );
        }

        if ($total_products_average > 0) {
            return $ratio_tax / $total_products_average;
        }

        return 0;
    }

    /**
     * The arguments are optional and only serve as return values in case caller needs the details.
     *
     * @param float|null $cartAmountTaxExcluded If the reference is given, it will be updated with the
     *                                          total amount in the Cart excluding Taxes
     * @param float|null $cartAmountTaxIncluded If the reference is given, it will be updated with the
     *                                          total amount in the Cart including Taxes
     *
     * @return float Average Tax Rate on Products
     */
    public function getAverageProductsTaxRate(&$cartAmountTaxExcluded = null, &$cartAmountTaxIncluded = null)
    {
        $cartAmountTaxIncluded = $this->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $cartAmountTaxExcluded = $this->getOrderTotal(false, Cart::ONLY_PRODUCTS);

        $cart_vat_amount = $cartAmountTaxIncluded - $cartAmountTaxExcluded;

        if ($cart_vat_amount == 0 || $cartAmountTaxExcluded == 0) {
            return 0;
        } else {
            return Tools::ps_round($cart_vat_amount / $cartAmountTaxExcluded, 3);
        }
    }

    /**
     * Get Cart Rules
     *
     *
     * @param int $filter Filter enum:
     *                    - FILTER_ACTION_ALL
     *                    - FILTER_ACTION_SHIPPING
     *                    - FILTER_ACTION_REDUCTION
     *                    - FILTER_ACTION_GIFT
     *                    - FILTER_ACTION_ALL_NOCAP
     * @param bool $autoAdd automaticaly adds cart ruls without code to cart
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource Database result
     */
    public function getCartRules($filter = CartRule::FILTER_ACTION_ALL, $autoAdd = true)
    {
        // Define virtual context to prevent case where the cart is not the in the global context
        $virtual_context = Context::getContext()->cloneContext();
        $virtual_context->cart = $this;

        // If the cart has not been saved, then there can't be any cart rule applied
        if (!CartRule::isFeatureActive() || !$this->id) {
            return array();
        }
        if ($autoAdd) {
            CartRule::autoAddToCart($virtual_context);
        }

        $cache_key = 'Cart::getCartRules_'.$this->id.'-'.$filter;
        if (!Cache::isStored($cache_key)) {
            $result = Db::getInstance()->executeS(
                'SELECT cr.*, crl.`id_lang`, crl.`name`, cd.`id_cart`
                FROM `'._DB_PREFIX_.'cart_cart_rule` cd
                LEFT JOIN `'._DB_PREFIX_.'cart_rule` cr ON cd.`id_cart_rule` = cr.`id_cart_rule`
                LEFT JOIN `'._DB_PREFIX_.'cart_rule_lang` crl ON (
                    cd.`id_cart_rule` = crl.`id_cart_rule`
                    AND crl.id_lang = '.(int)$this->id_lang.'
                )
                WHERE `id_cart` = '.(int)$this->id.'
                '.($filter == CartRule::FILTER_ACTION_SHIPPING ? 'AND free_shipping = 1' : '').'
                '.($filter == CartRule::FILTER_ACTION_GIFT ? 'AND gift_product != 0' : '').'
                '.($filter == CartRule::FILTER_ACTION_REDUCTION ? 'AND (reduction_percent != 0 OR reduction_amount != 0)' : '')
                .' ORDER by cr.priority ASC'
            );
            Cache::store($cache_key, $result);
        } else {
            $result = Cache::retrieve($cache_key);
        }

        // Define virtual context to prevent case where the cart is not the in the global context
        $virtual_context = Context::getContext()->cloneContext();
        $virtual_context->cart = $this;

        foreach ($result as &$row) {
            $row['obj'] = new CartRule($row['id_cart_rule'], (int)$this->id_lang);
            $row['value_real'] = $row['obj']->getContextualValue(true, $virtual_context, $filter);
            $row['value_tax_exc'] = $row['obj']->getContextualValue(false, $virtual_context, $filter);
            // Retro compatibility < 1.5.0.2
            $row['id_discount'] = $row['id_cart_rule'];
            $row['description'] = $row['name'];
        }

        return $result;
    }

    /**
     * Get cart discounts
     */
    public function getDiscounts()
    {
        return CartRule::getCustomerHighlightedDiscounts($this->id_lang, $this->id_customer, $this);
    }

    /**
     * Return the CartRule IDs in the Cart
     *
     * @param int $filter Filter enum:
     *                    - FILTER_ACTION_ALL
     *                    - FILTER_ACTION_SHIPPING
     *                    - FILTER_ACTION_REDUCTION
     *                    - FILTER_ACTION_GIFT
     *                    - FILTER_ACTION_ALL_NOCAP
     *
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getOrderedCartRulesIds($filter = CartRule::FILTER_ACTION_ALL)
    {
        $cache_key = 'Cart::getOrderedCartRulesIds_'.$this->id.'-'.$filter.'-ids';
        if (!Cache::isStored($cache_key)) {
            $result = Db::getInstance()->executeS(
                'SELECT cr.`id_cart_rule`
                FROM `'._DB_PREFIX_.'cart_cart_rule` cd
                LEFT JOIN `'._DB_PREFIX_.'cart_rule` cr ON cd.`id_cart_rule` = cr.`id_cart_rule`
                LEFT JOIN `'._DB_PREFIX_.'cart_rule_lang` crl ON (
                    cd.`id_cart_rule` = crl.`id_cart_rule`
                    AND crl.id_lang = '.(int)$this->id_lang.'
                )
                WHERE `id_cart` = '.(int)$this->id.'
                '.($filter == CartRule::FILTER_ACTION_SHIPPING ? 'AND free_shipping = 1' : '').'
                '.($filter == CartRule::FILTER_ACTION_GIFT ? 'AND gift_product != 0' : '').'
                '.($filter == CartRule::FILTER_ACTION_REDUCTION ? 'AND (reduction_percent != 0 OR reduction_amount != 0)' : '')
                .' ORDER BY cr.priority ASC'
            );
            Cache::store($cache_key, $result);
        } else {
            $result = Cache::retrieve($cache_key);
        }

        return $result;
    }

    /**
     * Get amount of Customer Discounts
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
        $cache_id = 'Cart::getDiscountsCustomer_'.(int)$this->id.'-'.(int)$id_cart_rule;
        if (!Cache::isStored($cache_id)) {
            $result = (int)Db::getInstance()->getValue('
                SELECT COUNT(*)
                FROM `'._DB_PREFIX_.'cart_cart_rule`
                WHERE `id_cart_rule` = '.(int)$id_cart_rule.' AND `id_cart` = '.(int)$this->id);
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * Get last Product in Cart
     *
     * @return bool|mixed Database result
     */
    public function getLastProduct()
    {
        $sql = '
            SELECT `id_product`, `id_product_attribute`, id_shop
            FROM `'._DB_PREFIX_.'cart_product`
            WHERE `id_cart` = '.(int)$this->id.'
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
     * Return cart products
     *
     * @param bool $refresh
     * @param bool $id_product
     * @param int  $id_country
     * @param bool $fullInfos
     *
     * @return array Products
     */
    public function getProducts($refresh = false, $id_product = false, $id_country = null, $fullInfos = true)
    {
        if (!$this->id) {
            return array();
        }
        // Product cache must be strictly compared to NULL, or else an empty cart will add dozens of queries
        if ($this->_products !== null && !$refresh) {
            // Return product row with specified ID if it exists
            if (is_int($id_product)) {
                foreach ($this->_products as $product) {
                    if ($product['id_product'] == $id_product) {
                        return array($product);
                    }
                }
                return array();
            }
            return $this->_products;
        }

        // Build query
        $sql = new DbQuery();

        // Build SELECT
        $sql->select('cp.`id_product_attribute`, cp.`id_product`, cp.`quantity` AS cart_quantity, cp.id_shop, cp.`id_customization`, pl.`name`, p.`is_virtual`,
                        pl.`description_short`, pl.`available_now`, pl.`available_later`, product_shop.`id_category_default`, p.`id_supplier`,
                        p.`id_manufacturer`, m.`name` AS manufacturer_name, product_shop.`on_sale`, product_shop.`ecotax`, product_shop.`additional_shipping_cost`,
                        product_shop.`available_for_order`, product_shop.`show_price`, product_shop.`price`, product_shop.`active`, product_shop.`unity`, product_shop.`unit_price_ratio`,
                        stock.`quantity` AS quantity_available, p.`width`, p.`height`, p.`depth`, stock.`out_of_stock`, p.`weight`,
                        p.`available_date`, p.`date_add`, p.`date_upd`, IFNULL(stock.quantity, 0) as quantity, pl.`link_rewrite`, cl.`link_rewrite` AS category,
                        CONCAT(LPAD(cp.`id_product`, 10, 0), LPAD(IFNULL(cp.`id_product_attribute`, 0), 10, 0), IFNULL(cp.`id_address_delivery`, 0), IFNULL(cp.`id_customization`, 0)) AS unique_id, cp.id_address_delivery,
                        product_shop.advanced_stock_management, ps.product_supplier_reference supplier_reference');

        // Build FROM
        $sql->from('cart_product', 'cp');

        // Build JOIN
        $sql->leftJoin('product', 'p', 'p.`id_product` = cp.`id_product`');
        $sql->innerJoin('product_shop', 'product_shop', '(product_shop.`id_shop` = cp.`id_shop` AND product_shop.`id_product` = p.`id_product`)');
        $sql->leftJoin(
            'product_lang',
            'pl',
            'p.`id_product` = pl.`id_product`
            AND pl.`id_lang` = '.(int)$this->id_lang.Shop::addSqlRestrictionOnLang('pl', 'cp.id_shop')
        );

        $sql->leftJoin(
            'category_lang',
            'cl',
            'product_shop.`id_category_default` = cl.`id_category`
            AND cl.`id_lang` = '.(int)$this->id_lang.Shop::addSqlRestrictionOnLang('cl', 'cp.id_shop')
        );

        $sql->leftJoin('product_supplier', 'ps', 'ps.`id_product` = cp.`id_product` AND ps.`id_product_attribute` = cp.`id_product_attribute` AND ps.`id_supplier` = p.`id_supplier`');
        $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');

        // @todo test if everything is ok, then refactorise call of this method
        $sql->join(Product::sqlStock('cp', 'cp'));

        // Build WHERE clauses
        $sql->where('cp.`id_cart` = '.(int)$this->id);
        if ($id_product) {
            $sql->where('cp.`id_product` = '.(int)$id_product);
        }
        $sql->where('p.`id_product` IS NOT NULL');

        // Build ORDER BY
        $sql->orderBy('cp.`date_add`, cp.`id_product`, cp.`id_product_attribute` ASC');

        if (Customization::isFeatureActive()) {
            $sql->select('cu.`id_customization`, cu.`quantity` AS customization_quantity');
            $sql->leftJoin(
                'customization',
                'cu',
                'p.`id_product` = cu.`id_product` AND cp.`id_product_attribute` = cu.`id_product_attribute` AND cp.`id_customization` = cu.`id_customization` AND cu.`id_cart` = '.(int)$this->id
            );
            $sql->groupBy('cp.`id_product_attribute`, cp.`id_product`, cp.`id_shop`, cp.`id_customization`');
        } else {
            $sql->select('NULL AS customization_quantity, NULL AS id_customization');
        }

        if (Combination::isFeatureActive()) {
            $sql->select('
                product_attribute_shop.`price` AS price_attribute, product_attribute_shop.`ecotax` AS ecotax_attr,
                IF (IFNULL(pa.`reference`, \'\') = \'\', p.`reference`, pa.`reference`) AS reference,
                (p.`weight`+ pa.`weight`) weight_attribute,
                IF (IFNULL(pa.`ean13`, \'\') = \'\', p.`ean13`, pa.`ean13`) AS ean13,
                IF (IFNULL(pa.`isbn`, \'\') = \'\', p.`isbn`, pa.`isbn`) AS isbn,
                IF (IFNULL(pa.`upc`, \'\') = \'\', p.`upc`, pa.`upc`) AS upc,
                IFNULL(product_attribute_shop.`minimal_quantity`, product_shop.`minimal_quantity`) as minimal_quantity,
                IF(product_attribute_shop.wholesale_price > 0,  product_attribute_shop.wholesale_price, product_shop.`wholesale_price`) wholesale_price
            ');

            $sql->leftJoin('product_attribute', 'pa', 'pa.`id_product_attribute` = cp.`id_product_attribute`');
            $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', '(product_attribute_shop.`id_shop` = cp.`id_shop` AND product_attribute_shop.`id_product_attribute` = pa.`id_product_attribute`)');
        } else {
            $sql->select(
                'p.`reference` AS reference, p.`ean13`, p.`isbn`,
                p.`upc` AS upc, product_shop.`minimal_quantity` AS minimal_quantity, product_shop.`wholesale_price` wholesale_price'
            );
        }

        $sql->select('image_shop.`id_image` id_image, il.`legend`');
        $sql->leftJoin('image_shop', 'image_shop', 'image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$this->id_shop);
        $sql->leftJoin('image_lang', 'il', 'il.`id_image` = image_shop.`id_image` AND il.`id_lang` = '.(int)$this->id_lang);

        $result = Db::getInstance()->executeS($sql);

        // Reset the cache before the following return, or else an empty cart will add dozens of queries
        $products_ids = array();
        $pa_ids = array();
        if ($result) {
            foreach ($result as $key => $row) {
                $products_ids[] = $row['id_product'];
                $pa_ids[] = $row['id_product_attribute'];
                $specific_price = SpecificPrice::getSpecificPrice($row['id_product'], $this->id_shop, $this->id_currency, $id_country, $this->id_shop_group, $row['cart_quantity'], $row['id_product_attribute'], $this->id_customer, $this->id);
                if ($specific_price) {
                    $reduction_type_row = array('reduction_type' => $specific_price['reduction_type']);
                } else {
                    $reduction_type_row = array('reduction_type' => 0);
                }

                $result[$key] = array_merge($row, $reduction_type_row);
            }
        }
        // Thus you can avoid one query per product, because there will be only one query for all the products of the cart
        Product::cacheProductsFeatures($products_ids);
        Cart::cacheSomeAttributesLists($pa_ids, $this->id_lang);

        $this->_products = array();
        if (empty($result)) {
            return array();
        }

        if ($fullInfos) {
            $ecotax_rate = (float)Tax::getProductEcotaxRate($this->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
            $apply_eco_tax = Product::$_taxCalculationMethod == PS_TAX_INC && (int)Configuration::get('PS_TAX');
            $cart_shop_context = Context::getContext()->cloneContext();

            $gifts = $this->getCartRules(CartRule::FILTER_ACTION_GIFT);
            $givenAwayProductsIds = array();

            if ($this->shouldSplitGiftProductsQuantity && count($gifts) > 0) {
                foreach ($gifts as $gift) {
                    foreach ($result as $rowIndex => $row) {
                        if (!array_key_exists('is_gift', $result[$rowIndex])) {
                            $result[$rowIndex]['is_gift'] = false;
                        }

                        if (
                            $row['id_product'] == $gift['gift_product'] &&
                            $row['id_product_attribute'] == $gift['gift_product_attribute']
                        ) {
                            $row['is_gift'] = true;
                            $result[$rowIndex] = $row;
                        }
                    }

                    $index = $gift['gift_product'] . '-' . $gift['gift_product_attribute'];
                    if (!array_key_exists($index, $givenAwayProductsIds)) {
                        $givenAwayProductsIds[$index] = 1;
                    } else {
                        $givenAwayProductsIds[$index]++;
                    }
                }
            }

            foreach ($result as &$row) {
                if (!array_key_exists('is_gift', $row)) {
                    $row['is_gift'] = false;
                }

                $additionalRow = Product::getProductProperties((int)$this->id_lang, $row);
                $row['reduction'] = $additionalRow['reduction'];
                $row['price_without_reduction'] = $additionalRow['price_without_reduction'];
                $row['specific_prices'] = $additionalRow['specific_prices'];
                unset($additionalRow);

                $givenAwayQuantity = 0;
                $giftIndex = $row['id_product'] . '-' . $row['id_product_attribute'];
                if ($row['is_gift'] && array_key_exists($giftIndex, $givenAwayProductsIds)) {
                    $givenAwayQuantity = $givenAwayProductsIds[$giftIndex];
                }

                if (!$row['is_gift'] || (int)$row['cart_quantity'] === $givenAwayQuantity) {
                    $row = $this->applyProductCalculations($row, $cart_shop_context);
                } else {
                    // Separate products given away from those manually added to cart
                    $this->_products[] = $this->applyProductCalculations($row, $cart_shop_context, $givenAwayQuantity);
                    unset($row['is_gift']);
                    $row = $this->applyProductCalculations(
                        $row,
                        $cart_shop_context,
                        $row['cart_quantity'] - $givenAwayQuantity
                    );
                }

                $this->_products[] = $row;
            }
        } else {
            $this->_products = $result;
        }

        return $this->_products;
    }

    /**
     * @param $row
     * @param $shopContext
     * @param $productQuantity
     * @return mixed
     */
    protected function applyProductCalculations($row, $shopContext, $productQuantity = null)
    {
        if (is_null($productQuantity)) {
            $productQuantity = (int)$row['cart_quantity'];
        }

        if (isset($row['ecotax_attr']) && $row['ecotax_attr'] > 0) {
            $row['ecotax'] = (float)$row['ecotax_attr'];
        }

        $row['stock_quantity'] = (int)$row['quantity'];
        // for compatibility with 1.2 themes
        $row['quantity'] = $productQuantity;

        // get the customization weight impact
        $customization_weight = Customization::getCustomizationWeight($row['id_customization']);

        if (isset($row['id_product_attribute']) && (int)$row['id_product_attribute'] && isset($row['weight_attribute'])) {
            $row['weight_attribute'] += $customization_weight;
            $row['weight'] = (float)$row['weight_attribute'];
        } else {
            $row['weight'] += $customization_weight;
        }

        if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
            $address_id = (int)$this->id_address_invoice;
        } else {
            $address_id = (int)$row['id_address_delivery'];
        }
        if (!Address::addressExists($address_id)) {
            $address_id = null;
        }

        if ($shopContext->shop->id != $row['id_shop']) {
            $shopContext->shop = new Shop((int)$row['id_shop']);
        }

        $address = Address::initialize($address_id, true);
        $id_tax_rules_group = Product::getIdTaxRulesGroupByIdProduct((int)$row['id_product'], $shopContext);
        $tax_calculator = TaxManagerFactory::getManager($address, $id_tax_rules_group)->getTaxCalculator();

        $specific_price_output = null;

        $row['price_without_reduction'] = Product::getPriceStatic(
            (int)$row['id_product'],
            true,
            isset($row['id_product_attribute']) ? (int)$row['id_product_attribute'] : null,
            6,
            null,
            false,
            false,
            $productQuantity,
            false,
            (int)$this->id_customer ? (int)$this->id_customer : null,
            (int)$this->id,
            $address_id,
            $specific_price_output,
            true,
            true,
            $shopContext,
            true,
            $row['id_customization']
        );

        $row['price_with_reduction'] = Product::getPriceStatic(
            (int)$row['id_product'],
            true,
            isset($row['id_product_attribute']) ? (int)$row['id_product_attribute'] : null,
            6,
            null,
            false,
            true,
            $productQuantity,
            false,
            (int)$this->id_customer ? (int)$this->id_customer : null,
            (int)$this->id,
            $address_id,
            $specific_price_output,
            true,
            true,
            $shopContext,
            true,
            $row['id_customization']
        );

        $row['price'] = $row['price_with_reduction_without_tax'] = Product::getPriceStatic(
            (int)$row['id_product'],
            false,
            isset($row['id_product_attribute']) ? (int)$row['id_product_attribute'] : null,
            6,
            null,
            false,
            true,
            $productQuantity,
            false,
            (int)$this->id_customer ? (int)$this->id_customer : null,
            (int)$this->id,
            $address_id,
            $specific_price_output,
            true,
            true,
            $shopContext,
            true,
            $row['id_customization']
        );

        switch (Configuration::get('PS_ROUND_TYPE')) {
            case Order::ROUND_TOTAL:
                $row['total'] = $row['price_with_reduction_without_tax'] * $productQuantity;
                $row['total_wt'] = $row['price_with_reduction'] * $productQuantity;
                break;
            case Order::ROUND_LINE:
                $row['total'] = Tools::ps_round(
                    $row['price_with_reduction_without_tax'] * $productQuantity,
                    _PS_PRICE_COMPUTE_PRECISION_
                );
                $row['total_wt'] = Tools::ps_round(
                    $row['price_with_reduction'] * $productQuantity,
                    _PS_PRICE_COMPUTE_PRECISION_
                );
                break;

            case Order::ROUND_ITEM:
            default:
                $row['total'] = Tools::ps_round(
                        $row['price_with_reduction_without_tax'],
                        _PS_PRICE_COMPUTE_PRECISION_
                    ) * $productQuantity;
                $row['total_wt'] = Tools::ps_round(
                        $row['price_with_reduction'],
                        _PS_PRICE_COMPUTE_PRECISION_
                    ) * $productQuantity;
                break;
        }

        $row['price_wt'] = $row['price_with_reduction'];
        $row['description_short'] = Tools::nl2br($row['description_short']);

        // check if a image associated with the attribute exists
        if ($row['id_product_attribute']) {
            $row2 = Image::getBestImageAttribute($row['id_shop'], $this->id_lang, $row['id_product'], $row['id_product_attribute']);
            if ($row2) {
                $row = array_merge($row, $row2);
            }
        }

        $row['reduction_applies'] = ($specific_price_output && (float)$specific_price_output['reduction']);
        $row['quantity_discount_applies'] = ($specific_price_output && $productQuantity >= (int)$specific_price_output['from_quantity']);
        $row['id_image'] = Product::defineProductImage($row, $this->id_lang);
        $row['allow_oosp'] = Product::isAvailableWhenOutOfStock($row['out_of_stock']);
        $row['features'] = Product::getFeaturesStatic((int)$row['id_product']);

        if (array_key_exists($row['id_product_attribute'] . '-' . $this->id_lang, self::$_attributesLists)) {
            $row = array_merge($row, self::$_attributesLists[$row['id_product_attribute'] . '-' . $this->id_lang]);
        }

        return Product::getTaxesInformations($row, $shopContext);
    }

    public static function cacheSomeAttributesLists($ipa_list, $id_lang)
    {
        if (!Combination::isFeatureActive()) {
            return;
        }

        $pa_implode = array();
        $separator = Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR');

        foreach ($ipa_list as $id_product_attribute) {
            if ((int)$id_product_attribute && !array_key_exists($id_product_attribute.'-'.$id_lang, self::$_attributesLists)) {
                $pa_implode[] = (int)$id_product_attribute;
                self::$_attributesLists[(int)$id_product_attribute.'-'.$id_lang] = array('attributes' => '', 'attributes_small' => '');
            }
        }

        if (!count($pa_implode)) {
            return;
        }

        $result = Db::getInstance()->executeS(
            'SELECT pac.`id_product_attribute`, agl.`public_name` AS public_group_name, al.`name` AS attribute_name
            FROM `'._DB_PREFIX_.'product_attribute_combination` pac
            LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
            LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
            LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (
                a.`id_attribute` = al.`id_attribute`
                AND al.`id_lang` = '.(int)$id_lang.'
            )
            LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (
                ag.`id_attribute_group` = agl.`id_attribute_group`
                AND agl.`id_lang` = '.(int)$id_lang.'
            )
            WHERE pac.`id_product_attribute` IN ('.implode(',', $pa_implode).')
            ORDER BY ag.`position` ASC, a.`position` ASC'
        );

        foreach ($result as $row) {
            self::$_attributesLists[$row['id_product_attribute'].'-'.$id_lang]['attributes'] .= $row['public_group_name'].' : '.$row['attribute_name'].$separator.' ';
            self::$_attributesLists[$row['id_product_attribute'].'-'.$id_lang]['attributes_small'] .= $row['attribute_name'].$separator.' ';
        }

        foreach ($pa_implode as $id_product_attribute) {
            self::$_attributesLists[$id_product_attribute.'-'.$id_lang]['attributes'] = rtrim(
                self::$_attributesLists[$id_product_attribute.'-'.$id_lang]['attributes'],
                $separator.' '
            );

            self::$_attributesLists[$id_product_attribute.'-'.$id_lang]['attributes_small'] = rtrim(
                self::$_attributesLists[$id_product_attribute.'-'.$id_lang]['attributes_small'],
                $separator.' '
            );
        }
    }

    /**
     * Check if Addresses in the Cart are still valid and update with the next valid Address ID found
     *
     * @return bool Whether the Addresses have been succesfully checked and upated
     */
    public function checkAndUpdateAddresses()
    {
        $needUpdate = false;
        foreach (array('invoice', 'delivery') as $type) {
            $addr = 'id_address_'.$type;
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
     * Return cart products quantity
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
     * This is the total amount of products, not just the types
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

        self::$_nbProducts[$id] = (int)Db::getInstance()->getValue(
            'SELECT SUM(`quantity`)
            FROM `'._DB_PREFIX_.'cart_product`
            WHERE `id_cart` = '.(int)$id
        );

        return self::$_nbProducts[$id];
    }

    /**
     * Add a CartRule to the Cart
     *
     * @param int $id_cart_rule CartRule ID
     *
     * @return bool Whether the CartRule has been successfully added
     */
    public function addCartRule($id_cart_rule)
    {
        // You can't add a cart rule that does not exist
        $cartRule = new CartRule($id_cart_rule, Context::getContext()->language->id);

        if (!Validate::isLoadedObject($cartRule)) {
            return false;
        }

        if (Db::getInstance()->getValue('SELECT id_cart_rule FROM '._DB_PREFIX_.'cart_cart_rule WHERE id_cart_rule = '.(int)$id_cart_rule.' AND id_cart = '.(int)$this->id)) {
            return false;
        }

        // Add the cart rule to the cart
        if (!Db::getInstance()->insert('cart_cart_rule', array(
            'id_cart_rule' => (int)$id_cart_rule,
            'id_cart' => (int)$this->id
        ))) {
            return false;
        }

        Cache::clean('Cart::getCartRules_'.$this->id.'-'.CartRule::FILTER_ACTION_ALL);
        Cache::clean('Cart::getCartRules_'.$this->id.'-'.CartRule::FILTER_ACTION_SHIPPING);
        Cache::clean('Cart::getCartRules_'.$this->id.'-'.CartRule::FILTER_ACTION_REDUCTION);
        Cache::clean('Cart::getCartRules_'.$this->id.'-'.CartRule::FILTER_ACTION_GIFT);

        Cache::clean('Cart::getOrderedCartRulesIds_'.$this->id.'-'.CartRule::FILTER_ACTION_ALL. '-ids');
        Cache::clean('Cart::getOrderedCartRulesIds_'.$this->id.'-'.CartRule::FILTER_ACTION_SHIPPING. '-ids');
        Cache::clean('Cart::getOrderedCartRulesIds_'.$this->id.'-'.CartRule::FILTER_ACTION_REDUCTION. '-ids');
        Cache::clean('Cart::getOrderedCartRulesIds_'.$this->id.'-'.CartRule::FILTER_ACTION_GIFT. '-ids');

        if ((int)$cartRule->gift_product) {
            $this->updateQty(1, $cartRule->gift_product, $cartRule->gift_product_attribute, false, 'up', 0, null, false);
        }

        return true;
    }

    /**
     * Check if the Cart contains the given Product (Attribute)
     *
     * @param int $idProduct Product ID
     * @param int $idProductAttribute ProductAttribute ID
     * @param int $idCustomization Customization ID
     * @param int $idAddressDelivery Delivery Address ID
     *
     * @return array quantity index     : number of product in cart without counting those of pack in cart
     *               deep_quantity index: number of product in cart counting those of pack in cart
     */
    public function getProductQuantity($idProduct, $idProductAttribute = 0, $idCustomization = 0, $idAddressDelivery = 0)
    {
        $productIsPack = Pack::isPack($idProduct);
        $defaultPackStockType = Configuration::get('PS_PACK_STOCK_TYPE');
        $packStockTypesAllowed = array(
            Pack::STOCK_TYPE_PRODUCTS_ONLY,
            Pack::STOCK_TYPE_PACK_BOTH
        );
        $packStockTypesDefaultSupported = (int) in_array($defaultPackStockType, $packStockTypesAllowed);
        $firstUnionSql = 'SELECT cp.`quantity` as first_level_quantity, 0 as pack_quantity
          FROM `'._DB_PREFIX_.'cart_product` cp';
        $secondUnionSql = 'SELECT 0 as first_level_quantity, cp.`quantity` * p.`quantity` as pack_quantity
          FROM `'._DB_PREFIX_.'cart_product` cp' .
            ' JOIN `'._DB_PREFIX_.'pack` p ON cp.`id_product` = p.`id_product_pack`' .
            ' JOIN `'._DB_PREFIX_.'product` pr ON p.`id_product_pack` = pr.`id_product`';

        if ($idCustomization) {
            $customizationJoin = '
                LEFT JOIN `'._DB_PREFIX_.'customization` c ON (
                    c.`id_product` = cp.`id_product`
                    AND c.`id_product_attribute` = cp.`id_product_attribute`
                )';
            $firstUnionSql .= $customizationJoin;
            $secondUnionSql .= $customizationJoin;
        }
        $commonWhere = '
            WHERE cp.`id_product_attribute` = '.(int)$idProductAttribute.'
            AND cp.`id_customization` = '.(int)$idCustomization.'
            AND cp.`id_cart` = '.(int)$this->id;

        if (Configuration::get('PS_ALLOW_MULTISHIPPING') && $this->isMultiAddressDelivery()) {
            $commonWhere .= ' AND cp.`id_address_delivery` = '.(int)$idAddressDelivery;
        }

        if ($idCustomization) {
            $commonWhere .= ' AND c.`id_customization` = '.(int)$idCustomization;
        }
        $firstUnionSql .=  $commonWhere;
        $firstUnionSql .= ' AND cp.`id_product` = ' . (int) $idProduct;
        $secondUnionSql .= $commonWhere;
        $secondUnionSql .= ' AND p.`id_product_item` = ' . (int) $idProduct;
        $secondUnionSql .= ' AND (pr.`pack_stock_type` IN (' . implode(',', $packStockTypesAllowed) . ') OR (
            pr.`pack_stock_type` = ' . Pack::STOCK_TYPE_DEFAULT . '
            AND ' . $packStockTypesDefaultSupported . ' = 1
        ))';
        $parentSql = 'SELECT
            COALESCE(SUM(first_level_quantity) + SUM(pack_quantity), 0) as deep_quantity,
            COALESCE(SUM(first_level_quantity), 0) as quantity
          FROM (' . $firstUnionSql . ' UNION ' . $secondUnionSql . ') as q';

        return Db::getInstance()->getRow($parentSql);
    }

    /**
     * Check if the Cart contains the given Product (Attribute)
     *
     * @deprecated 1.7.3.1
     * @see Cart::getProductQuantity()
     *
     * @param int $id_product Product ID
     * @param int $id_product_attribute ProductAttribute ID
     * @param int $id_customization Customization ID
     * @param int $id_address_delivery Delivery Address ID
     *
     * @return array|bool Whether the Cart contains the Product
     *                                Result comes directly from the database
     */
    public function containsProduct($id_product, $id_product_attribute = 0, $id_customization = 0, $id_address_delivery = 0)
    {
        $result = $this->getProductQuantity($id_product, $id_product_attribute, $id_customization, $id_address_delivery);

        if (empty($result['quantity'])) {
            return false;
        }

        return array('quantity' => $result['quantity']);
    }

    /**
     * Update Product quantity
     *
     * @param int    $quantity             Quantity to add (or substract)
     * @param int    $id_product           Product ID
     * @param int    $id_product_attribute Attribute ID if needed
     * @param string $operator             Indicate if quantity must be increased or decreased
     *
     * @return bool Whether the quantity has been succesfully updated
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
        $skipAvailabilityCheckOutOfStock = false
    ) {
        if (!$shop) {
            $shop = Context::getContext()->shop;
        }

        if (Context::getContext()->customer->id) {
            if ($id_address_delivery == 0 && (int)$this->id_address_delivery) { // The $id_address_delivery is null, use the cart delivery address
                $id_address_delivery = $this->id_address_delivery;
            } elseif ($id_address_delivery == 0) { // The $id_address_delivery is null, get the default customer address
                $id_address_delivery = (int)Address::getFirstCustomerAddressId((int)Context::getContext()->customer->id);
            } elseif (!Customer::customerHasAddress(Context::getContext()->customer->id, $id_address_delivery)) { // The $id_address_delivery must be linked with customer
                $id_address_delivery = 0;
            }
        }

        $quantity = (int)$quantity;
        $id_product = (int)$id_product;
        $id_product_attribute = (int)$id_product_attribute;
        $product = new Product($id_product, false, Configuration::get('PS_LANG_DEFAULT'), $shop->id);

        if ($id_product_attribute) {
            $combination = new Combination((int)$id_product_attribute);
            if ($combination->id_product != $id_product) {
                return false;
            }
        }

        /* If we have a product combination, the minimal quantity is set with the one of this combination */
        if (!empty($id_product_attribute)) {
            $minimal_quantity = (int)Attribute::getAttributeMinimalQty($id_product_attribute);
        } else {
            $minimal_quantity = (int)$product->minimal_quantity;
        }

        if (!Validate::isLoadedObject($product)) {
            die(Tools::displayError());
        }

        if (isset(self::$_nbProducts[$this->id])) {
            unset(self::$_nbProducts[$this->id]);
        }

        if (isset(self::$_totalWeight[$this->id])) {
            unset(self::$_totalWeight[$this->id]);
        }

        $data = array(
            'cart' => $this,
            'product' => $product,
            'id_product_attribute' => $id_product_attribute,
            'id_customization' => $id_customization,
            'quantity' => $quantity,
            'operator' => $operator,
            'id_address_delivery' => $id_address_delivery,
            'shop' => $shop,
            'auto_add_cart_rule' => $auto_add_cart_rule,
        );

        /* @deprecated deprecated since 1.6.1.1 */
        // Hook::exec('actionBeforeCartUpdateQty', $data);
        Hook::exec('actionCartUpdateQuantityBefore', $data);

        if ((int)$quantity <= 0) {
            return $this->deleteProduct($id_product, $id_product_attribute, (int)$id_customization);
        } elseif (!$product->available_for_order
                || (Configuration::isCatalogMode() && !defined('_PS_ADMIN_DIR_'))
        ) {
            return false;
        } else {
            /* Check if the product is already in the cart */
            $cartProductQuantity = $this->getProductQuantity($id_product, $id_product_attribute, (int)$id_customization, (int)$id_address_delivery);

            /* Update quantity if product already exist */
            if (!empty($cartProductQuantity['quantity'])) {
                $productQuantity = Product::getQuantity($id_product, $id_product_attribute, null, $this);
                $availableOutOfStock = Product::isAvailableWhenOutOfStock($product->out_of_stock);

                if ($operator == 'up') {
                    $updateQuantity = '+ ' . $quantity;
                    $newProductQuantity = $productQuantity - $quantity;

                    if ($newProductQuantity < 0 && !$availableOutOfStock && !$skipAvailabilityCheckOutOfStock) {
                        return false;
                    }
                } else if ($operator == 'down') {
                    $cartFirstLevelProductQuantity = $this->getProductQuantity((int) $id_product, (int) $id_product_attribute, $id_customization);
                    $updateQuantity = '- ' . $quantity;
                    $newProductQuantity = $productQuantity + $quantity;

                    if ($cartFirstLevelProductQuantity['quantity'] <= 1) {
                        return $this->deleteProduct((int)$id_product, (int)$id_product_attribute, (int)$id_customization);
                    }
                } else {
                    return false;
                }
                Db::getInstance()->execute(
                    'UPDATE `'._DB_PREFIX_.'cart_product`
                    SET `quantity` = `quantity` ' . $updateQuantity . '
                    WHERE `id_product` = '.(int)$id_product.
                    ' AND `id_customization` = '.(int)$id_customization.
                    (!empty($id_product_attribute) ? ' AND `id_product_attribute` = '.(int)$id_product_attribute : '').'
                    AND `id_cart` = '.(int)$this->id.(Configuration::get('PS_ALLOW_MULTISHIPPING') && $this->isMultiAddressDelivery() ? ' AND `id_address_delivery` = '.(int)$id_address_delivery : '').'
                    LIMIT 1'
                );
            } elseif ($operator == 'up') {
                /* Add product to the cart */

                $sql = 'SELECT stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity
                        FROM '._DB_PREFIX_.'product p
                        '.Product::sqlStock('p', $id_product_attribute, true, $shop).'
                        WHERE p.id_product = '.$id_product;

                $result2 = Db::getInstance()->getRow($sql);

                // Quantity for product pack
                if (Pack::isPack($id_product)) {
                    $result2['quantity'] = Pack::getQuantity($id_product, $id_product_attribute, null, $this);
                }

                if (!Product::isAvailableWhenOutOfStock((int)$result2['out_of_stock']) && !$skipAvailabilityCheckOutOfStock) {
                    if ((int)$quantity > $result2['quantity']) {
                        return false;
                    }
                }

                if ((int)$quantity < $minimal_quantity) {
                    return -1;
                }

                $result_add = Db::getInstance()->insert('cart_product', array(
                    'id_product' =>            (int)$id_product,
                    'id_product_attribute' =>    (int)$id_product_attribute,
                    'id_cart' =>                (int)$this->id,
                    'id_address_delivery' =>    (int)$id_address_delivery,
                    'id_shop' =>                $shop->id,
                    'quantity' =>                (int)$quantity,
                    'date_add' =>                date('Y-m-d H:i:s'),
                    'id_customization' =>       (int)$id_customization,
                ));

                if (!$result_add) {
                    return false;
                }
            }
        }

        // refresh cache of self::_products
        $this->_products = $this->getProducts(true);
        $this->update();
        $context = Context::getContext()->cloneContext();
        $context->cart = $this;
        Cache::clean('getContextualValue_*');
        if ($auto_add_cart_rule) {
            CartRule::autoAddToCart($context);
        }

        if ($product->customizable) {
            return $this->_updateCustomizationQuantity((int)$quantity, (int)$id_customization, (int)$id_product, (int)$id_product_attribute, (int)$id_address_delivery, $operator);
        } else {
            return true;
        }
    }

    /**
     * Customization management
     */
    protected function _updateCustomizationQuantity($quantity, $id_customization, $id_product, $id_product_attribute, $id_address_delivery, $operator = 'up')
    {
        // Link customization to product combination when it is first added to cart
        if (empty($id_customization)) {
            $customization = $this->getProductCustomization($id_product, null, true);
            foreach ($customization as $field) {
                if ($field['quantity'] == 0) {
                    Db::getInstance()->execute('
                    UPDATE `'._DB_PREFIX_.'customization`
                    SET `quantity` = '.(int)$quantity.',
                        `id_product_attribute` = '.(int)$id_product_attribute.',
                        `id_address_delivery` = '.(int)$id_address_delivery.',
                        `in_cart` = 1
                    WHERE `id_customization` = '.(int)$field['id_customization']);
                }
            }
        }

        /* Deletion */
        if (!empty($id_customization) && (int)$quantity < 1) {
            return $this->_deleteCustomization((int)$id_customization, (int)$id_product, (int)$id_product_attribute);
        }

        /* Quantity update */
        if (!empty($id_customization)) {
            $result = Db::getInstance()->getRow('SELECT `quantity` FROM `'._DB_PREFIX_.'customization` WHERE `id_customization` = '.(int)$id_customization);
            if ($result && Db::getInstance()->NumRows()) {
                if ($operator == 'down' && (int)$result['quantity'] - (int)$quantity < 1) {
                    return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customization` WHERE `id_customization` = '.(int)$id_customization);
                }

                return Db::getInstance()->execute('
                    UPDATE `'._DB_PREFIX_.'customization`
                    SET
                        `quantity` = `quantity` '.($operator == 'up' ? '+ ' : '- ').(int)$quantity.',
                        `id_product_attribute` = '.(int)$id_product_attribute.',
                        `id_address_delivery` = '.(int)$id_address_delivery.',
                        `in_cart` = 1
                    WHERE `id_customization` = '.(int)$id_customization);
            } else {
                Db::getInstance()->execute('
                    UPDATE `'._DB_PREFIX_.'customization`
                    SET `id_address_delivery` = '.(int)$id_address_delivery.',
                    `id_product_attribute` = '.(int)$id_product_attribute.',
                    `in_cart` = 1
                    WHERE `id_customization` = '.(int)$id_customization);
            }
        }
        // refresh cache of self::_products
        $this->_products = $this->getProducts(true);
        $this->update();
        return true;
    }

    /**
     * Add customization item to database
     *
     * @param int    $id_product           Product ID
     * @param int    $id_product_attribute ProductAttribute ID
     * @param int    $index                Index
     * @param int    $type                 Type enum
     *                                     - Product::CUSTOMIZE_FILE
     *                                     - Product::CUSTOMIZE_TEXTFIELD
     * @param string $field                Field
     * @param int    $quantity             Quantity
     *
     * @return bool Success
     */
    public function _addCustomization($id_product, $id_product_attribute, $index, $type, $field, $quantity)
    {
        $exising_customization = Db::getInstance()->executeS(
            'SELECT cu.`id_customization`, cd.`index`, cd.`value`, cd.`type` FROM `'._DB_PREFIX_.'customization` cu
            LEFT JOIN `'._DB_PREFIX_.'customized_data` cd
            ON cu.`id_customization` = cd.`id_customization`
            WHERE cu.id_cart = '.(int)$this->id.'
            AND cu.id_product = '.(int)$id_product.'
            AND in_cart = 0'
        );

        if ($exising_customization) {
            // If the customization field is alreay filled, delete it
            foreach ($exising_customization as $customization) {
                if ($customization['type'] == $type && $customization['index'] == $index) {
                    Db::getInstance()->execute('
                        DELETE FROM `'._DB_PREFIX_.'customized_data`
                        WHERE id_customization = '.(int)$customization['id_customization'].'
                        AND type = '.(int)$customization['type'].'
                        AND `index` = '.(int)$customization['index']);
                    if ($type == Product::CUSTOMIZE_FILE) {
                        @unlink(_PS_UPLOAD_DIR_.$customization['value']);
                        @unlink(_PS_UPLOAD_DIR_.$customization['value'].'_small');
                    }
                    break;
                }
            }
            $id_customization = $exising_customization[0]['id_customization'];
        } else {
            Db::getInstance()->execute(
                'INSERT INTO `'._DB_PREFIX_.'customization` (`id_cart`, `id_product`, `id_product_attribute`, `quantity`)
                VALUES ('.(int)$this->id.', '.(int)$id_product.', '.(int)$id_product_attribute.', '.(int)$quantity.')'
            );
            $id_customization = Db::getInstance()->Insert_ID();
        }

        $query = 'INSERT INTO `'._DB_PREFIX_.'customized_data` (`id_customization`, `type`, `index`, `value`)
            VALUES ('.(int)$id_customization.', '.(int)$type.', '.(int)$index.', \''.pSQL($field).'\')';

        if (!Db::getInstance()->execute($query)) {
            return false;
        }
        return true;
    }

    /**
     * Check if order has already been placed
     *
     * @return bool Indicates if the Order exists
     */
    public function orderExists()
    {
        $cache_id = 'Cart::orderExists_'.(int)$this->id;
        if (!Cache::isStored($cache_id)) {
            $result = (bool)Db::getInstance()->getValue('SELECT count(*) FROM `'._DB_PREFIX_.'orders` WHERE `id_cart` = '.(int)$this->id);
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * Remove the CartRule from the Cart
     *
     * @param int $id_cart_rule CartRule ID
     *
     * @return bool Whether the Cart rule has been successfully removed
     */
    public function removeCartRule($id_cart_rule)
    {
        Cache::clean('Cart::getCartRules_'.$this->id.'-'.CartRule::FILTER_ACTION_ALL);
        Cache::clean('Cart::getCartRules_'.$this->id.'-'.CartRule::FILTER_ACTION_SHIPPING);
        Cache::clean('Cart::getCartRules_'.$this->id.'-'.CartRule::FILTER_ACTION_REDUCTION);
        Cache::clean('Cart::getCartRules_'.$this->id.'-'.CartRule::FILTER_ACTION_GIFT);

        Cache::clean('Cart::getCartRules_'.$this->id.'-'.CartRule::FILTER_ACTION_ALL. '-ids');
        Cache::clean('Cart::getCartRules_'.$this->id.'-'.CartRule::FILTER_ACTION_SHIPPING. '-ids');
        Cache::clean('Cart::getCartRules_'.$this->id.'-'.CartRule::FILTER_ACTION_REDUCTION. '-ids');
        Cache::clean('Cart::getCartRules_'.$this->id.'-'.CartRule::FILTER_ACTION_GIFT. '-ids');

        $result = Db::getInstance()->delete('cart_cart_rule', '`id_cart_rule` = '.(int)$id_cart_rule.' AND `id_cart` = '.(int)$this->id, 1);

        $cart_rule = new CartRule($id_cart_rule, Configuration::get('PS_LANG_DEFAULT'));
        if ((int)$cart_rule->gift_product) {
            $this->updateQty(1, $cart_rule->gift_product, $cart_rule->gift_product_attribute, null, 'down', 0, null, false);
        }

        return $result;
    }

    /**
     * Delete a product from the cart
     *
     * @param int $id_product           Product ID
     * @param int $id_product_attribute Attribute ID if needed
     * @param int $id_customization     Customization id
     * @param int $id_address_delivery  Delivery Address id
     *
     * @return bool Whether the product has been successfully deleted
     */
    public function deleteProduct(
        $id_product,
        $id_product_attribute = null,
        $id_customization = null,
        $id_address_delivery = 0
    ) {
        if (isset(self::$_nbProducts[$this->id])) {
            unset(self::$_nbProducts[$this->id]);
        }

        if (isset(self::$_totalWeight[$this->id])) {
            unset(self::$_totalWeight[$this->id]);
        }

        if ((int)$id_customization) {
            if (!$this->_deleteCustomization((int)$id_customization, (int)$id_product, (int)$id_product_attribute, (int)$id_address_delivery)) {
                return false;
            }
        }

        /* Get customization quantity */
        $result = Db::getInstance()->getRow('
            SELECT SUM(`quantity`) AS \'quantity\'
            FROM `'._DB_PREFIX_.'customization`
            WHERE `id_cart` = '.(int)$this->id.'
            AND `id_product` = '.(int)$id_product.'
            AND `id_customization` = '.(int)$id_customization.'
            AND `id_product_attribute` = '.(int)$id_product_attribute);

        if ($result === false) {
            return false;
        }

        /* If the product still possesses customization it does not have to be deleted */
        if (Db::getInstance()->NumRows() && (int)$result['quantity']) {
            return Db::getInstance()->execute(
                'UPDATE `'._DB_PREFIX_.'cart_product`
                SET `quantity` = '.(int)$result['quantity'].'
                WHERE `id_cart` = '.(int)$this->id.'
                AND `id_product` = '.(int)$id_product.'
                AND `id_customization` = '.(int)$id_customization.
                ($id_product_attribute != null ? ' AND `id_product_attribute` = '.(int)$id_product_attribute : '')
            );
        }

        $preservedGifts = $this->getProductsGifts($id_product, $id_product_attribute);
        if ($preservedGifts[$id_product.'-'.$id_product_attribute] > 0) {
            return Db::getInstance()->execute(
                'UPDATE `'._DB_PREFIX_.'cart_product`
                SET `quantity` = '.(int)$preservedGifts[$id_product.'-'.$id_product_attribute].'
                WHERE `id_cart` = '.(int)$this->id.'
                AND `id_product` = '.(int)$id_product.
                ($id_product_attribute != null ? ' AND `id_product_attribute` = '.(int)$id_product_attribute : '')
            );
        }

        /* Product deletion */
        $result = Db::getInstance()->execute('
        DELETE FROM `'._DB_PREFIX_.'cart_product`
        WHERE `id_product` = '.(int)$id_product.'
        AND `id_customization` = '.(int)$id_customization.
            (!is_null($id_product_attribute) ? ' AND `id_product_attribute` = '.(int)$id_product_attribute : '').'
        AND `id_cart` = '.(int)$this->id.'
        '.((int)$id_address_delivery ? 'AND `id_address_delivery` = '.(int)$id_address_delivery : ''));

        if ($result) {
            $return = $this->update();
            // refresh cache of self::_products
            $this->_products = $this->getProducts(true);
            CartRule::autoRemoveFromCart();
            CartRule::autoAddToCart();

            return $return;
        }

        return false;
    }

    /**
     * @param $id_product
     * @param $id_product_attribute
     * @return array
     */
    protected function getProductsGifts($id_product, $id_product_attribute)
    {
        $id_product_attribute = (int) $id_product_attribute;

        $gifts = array_filter($this->getProductsWithSeparatedGifts(), function ($product) {
            return array_key_exists('is_gift', $product) && $product['is_gift'];
        });

        $preservedGifts = array($id_product . '-' . $id_product_attribute => 0);

        foreach ($gifts as $gift) {
            if (
                (int) $gift['id_product_attribute'] === $id_product_attribute
                && (int) $gift['id_product'] === $id_product
            ) {
                $preservedGifts[$id_product.'-'.$id_product_attribute]++;
            }
        }

        return $preservedGifts;
    }

    /**
     * Delete a Customization from the Cart. If the Customization is a Picture,
     * then the Image is also deleted
     *
     * @param int      $id_customization     Customization Id
     * @param null     $id_product           Unused
     * @param null     $id_product_attribute Unused
     * @param null|int $id_address_delivery  Unused
     *
     * @return bool Indicates if the Customization was successfully deleted
     * @todo: Remove unused parameters
     */
    protected function _deleteCustomization($id_customization, $id_product, $id_product_attribute, $id_address_delivery = 0)
    {
        $result = true;
        $customization = Db::getInstance()->getRow('SELECT *
            FROM `'._DB_PREFIX_.'customization`
            WHERE `id_customization` = '.(int)$id_customization);

        if ($customization) {
            $cust_data = Db::getInstance()->getRow('SELECT *
                FROM `'._DB_PREFIX_.'customized_data`
                WHERE `id_customization` = '.(int)$id_customization);

            // Delete customization picture if necessary
            if (isset($cust_data['type']) && $cust_data['type'] == 0) {
                $result &= (@unlink(_PS_UPLOAD_DIR_.$cust_data['value']) && @unlink(_PS_UPLOAD_DIR_.$cust_data['value'].'_small'));
            }

            $result &= Db::getInstance()->execute(
                'DELETE FROM `'._DB_PREFIX_.'customized_data`
                WHERE `id_customization` = '.(int)$id_customization
            );

            if (!$result) {
                return false;
            }

            return Db::getInstance()->execute(
                'DELETE FROM `'._DB_PREFIX_.'customization`
                WHERE `id_customization` = '.(int)$id_customization
            );
        }

        return true;
    }

    /**
     * Get formatted total amount in Cart
     *
     * @param  int    $id_cart Cart ID
     * @param bool $use_tax_display Whether the tax should be displayed
     * @param int  $type Type enum:
     *                   - ONLY_PRODUCTS
     *                   - ONLY_DISCOUNTS
     *                   - BOTH
     *                   - BOTH_WITHOUT_SHIPPING
     *                   - ONLY_SHIPPING
     *                   - ONLY_WRAPPING
     *
     * @return string Formatted amount in Cart
     */
    public static function getTotalCart($id_cart, $use_tax_display = false, $type = Cart::BOTH)
    {
        $cart = new Cart($id_cart);
        if (!Validate::isLoadedObject($cart)) {
            die(Tools::displayError());
        }

        $with_taxes = $use_tax_display ? $cart->_taxCalculationMethod != PS_TAX_EXC : true;
        return Tools::displayPrice($cart->getOrderTotal($with_taxes, $type), Currency::getCurrencyInstance((int)$cart->id_currency), false);
    }

    /**
     * Get total in Cart using a tax calculation method
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
     * This function returns the total cart amount
     *
     * @param bool  $withTaxes  With or without taxes
     * @param int   $type       Total type enum
     *                          - Cart::ONLY_PRODUCTS
     *                          - Cart::ONLY_DISCOUNTS
     *                          - Cart::BOTH
     *                          - Cart::BOTH_WITHOUT_SHIPPING
     *                          - Cart::ONLY_SHIPPING
     *                          - Cart::ONLY_WRAPPING
     *                          - Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING
     *                          - Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING
     * @param array $products
     * @param int   $id_carrier
     * @param bool  $use_cache @deprecated
     *
     * @return float Order total
     * @throws \Exception
     */
    public function getOrderTotal(
        $withTaxes = true,
        $type = Cart::BOTH,
        $products = null,
        $id_carrier = null,
        $use_cache = false
    ) {
        if ((int) $id_carrier <= 0) {
            $id_carrier = null;
        }

        // deprecated type
        if ($type == Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING) {
            $type = Cart::ONLY_PRODUCTS;
        }

        // check type
        $type = (int)$type;
        $allowedTypes = array(
            Cart::ONLY_PRODUCTS,
            Cart::ONLY_DISCOUNTS,
            Cart::BOTH,
            Cart::BOTH_WITHOUT_SHIPPING,
            Cart::ONLY_SHIPPING,
            Cart::ONLY_WRAPPING,
            Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING,
        );
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
        if (is_null($products)) {
            $products = $this->getProducts();
        }

        if ($type == Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING) {
            foreach ($products as $key => $product) {
                if ($product['is_virtual']) {
                    unset($products[$key]);
                }
            }
            $type = Cart::ONLY_PRODUCTS;
        }

        if (Tax::excludeTaxeOption()) {
            $withTaxes = false;
        }

        // CART CALCULATION
        $cartRules = array();
        if (in_array($type, [Cart::BOTH, Cart::ONLY_DISCOUNTS])) {
            $cartRules = $this->getCartRules();
        }
        $calculator = $this->newCalculator($products, $cartRules, $id_carrier);
        $computePrecision = $this->configuration->get('_PS_PRICE_COMPUTE_PRECISION_');
        switch ($type) {
            case Cart::ONLY_SHIPPING:
                $calculator->calculateRows();
                $calculator->calculateFees($computePrecision);
                $amount = $calculator->getFees()->getInitialShippingFees();
                break;
            case Cart::ONLY_WRAPPING:
                $calculator->calculateRows();
                $calculator->calculateFees($computePrecision);
                $amount = $calculator->getFees()->getInitialWrappingFees();
                break;
            case Cart::BOTH:
                $calculator->processCalculation($computePrecision);
                $amount = $calculator->getTotal();
                break;
            case Cart::BOTH_WITHOUT_SHIPPING:
            case Cart::ONLY_PRODUCTS:
                $calculator->calculateRows();
                $amount = $calculator->getRowTotal();
                break;
            case Cart::ONLY_DISCOUNTS:
                $calculator->processCalculation($computePrecision);
                $amount = $calculator->getDiscountTotal();
                break;
            default:
                throw new \Exception('unknown cart calculation type : ' . $type);
        }

        // TAXES ?

        $value = $withTaxes ? $amount->getTaxIncluded() : $amount->getTaxExcluded();

        // ROUND AND RETURN

        $compute_precision = $this->configuration->get('_PS_PRICE_COMPUTE_PRECISION_');
        return Tools::ps_round($value, $compute_precision);
    }


    /**
     * get the populated cart calculator
     *
     * @param array $products list of products to calculate on
     * @param array $cartRules list of cart rules to apply
     * @param int   $id_carrier carrier id (fees calculation)
     *
     * @return \PrestaShop\PrestaShop\Core\Cart\Calculator
     */
    private function newCalculator($products, $cartRules, $id_carrier)
    {
        $calculator = new Calculator($this, $id_carrier);

        /** @var PriceCalculator $priceCalculator */
        $priceCalculator = ServiceLocator::get(PriceCalculator::class);

        // set cart rows (products)
        $useEcotax = $this->configuration->get('PS_USE_ECOTAX');
        $precision = $this->configuration->get('_PS_PRICE_COMPUTE_PRECISION_');
        $configRoundType = $this->configuration->get('PS_ROUND_TYPE');
        $roundTypes = [
            Order::ROUND_TOTAL => CartRow::ROUND_MODE_TOTAL,
            Order::ROUND_LINE  => CartRow::ROUND_MODE_LINE,
            Order::ROUND_ITEM  => CartRow::ROUND_MODE_ITEM,
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
                new AddressFactory,
                new CustomerDataProvider,
                new CacheAdapter,
                new GroupDataProvider,
                new Database,
                $useEcotax,
                $precision,
                $roundType
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
    public function getDiscountSubtotalWithoutGifts()
    {
        $discountSubtotal = $this->excludeGiftsDiscountFromTotal()
            ->getOrderTotal(true, self::ONLY_DISCOUNTS);
        $this->includeGiftsDiscountInTotal();

        return $discountSubtotal;
    }

    /**
     * @param $products
     * @return array
     */
    protected function countProductLines($products)
    {
        $productsLines = array();
        array_map(function ($product) use (&$productsLines) {
            $productIndex = $product['id_product'] . '-' . $product['id_product_attribute'];

            if (!array_key_exists($productIndex, $productsLines)) {
                $productsLines[$product['id_product'] . '-' . $product['id_product_attribute']] = 1;
            } else {
                $productsLines[$product['id_product'] . '-' . $product['id_product_attribute']]++;
            }
        }, $products);

        return $productsLines;
    }
    /**
     * @param $products
     * @return array
     */
    protected function getDeliveryAddressId($products)
    {
        $addressDeliveryId = 0;
        if (isset($products[0])) {
            if (is_null($products)) {
                $addressDeliveryId = $this->id_address_delivery;
            } else {
                $addressDeliveryId = $products[0]['id_address_delivery'];
            };
        }

        return $addressDeliveryId;
    }

    /**
     * @param $type
     * @param $withShipping
     * @return array
     */
    protected function getTotalCalculationCartRules($type, $withShipping)
    {
        if ($withShipping || $type == Cart::ONLY_DISCOUNTS) {
            $cartRules = $this->getCartRules(CartRule::FILTER_ACTION_ALL);
        } else {
            $cartRules = $this->getCartRules(CartRule::FILTER_ACTION_REDUCTION);
            // Cart Rules array are merged manually in order to avoid doubles
            foreach ($this->getCartRules(CartRule::FILTER_ACTION_GIFT) as $cartRuleCandidate) {
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
     * @param $withTaxes
     * @param $product
     * @param $virtualContext
     * @return int
     */
    protected function findTaxRulesGroupId($withTaxes, $product, $virtualContext)
    {
        if ($withTaxes) {
            $taxRulesGroupId = Product::getIdTaxRulesGroupByIdProduct((int)$product['id_product'], $virtualContext);

            $addressId = $this->getProductAddressId($product);
            $address = $this->addressFactory->findOrCreate($addressId, true);

            // Refresh cache and execute tax manager factory hook
            TaxManagerFactory::getManager($address, $taxRulesGroupId)->getTaxCalculator();
        } else {
            $taxRulesGroupId = 0;
        }

        return $taxRulesGroupId;
    }

    /**
     * @param $product
     * @return int|null
     */
    public function getProductAddressId($product)
    {
        $taxAddressType = $this->configuration->get('PS_TAX_ADDRESS_TYPE');
        if ($taxAddressType == 'id_address_invoice') {
            $addressId = (int)$this->id_address_invoice;
        } else {
            $addressId = (int)$product['id_address_delivery'];
        }

        // Get delivery address of the product from the cart
        if (!$this->addressFactory->addressExists($addressId)) {
            $addressId = null;
        }

        return $addressId;
    }

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
     * @param $withTaxes
     * @param $type
     * @return float|int
     */
    protected function calculateWrappingFees($withTaxes, $type)
    {
        // Wrapping Fees
        $wrapping_fees = 0;

        // With PS_ATCP_SHIPWRAP on the gift wrapping cost computation calls getOrderTotal
        // with $type === Cart::ONLY_PRODUCTS, so the flag below prevents an infinite recursion.
        $includeGiftWrapping = (!$this->configuration->get('PS_ATCP_SHIPWRAP') || $type !== Cart::ONLY_PRODUCTS);
        $computePrecision = $this->configuration->get('_PS_PRICE_COMPUTE_PRECISION_');

        if ($this->gift && $includeGiftWrapping) {
            $wrapping_fees = Tools::convertPrice(
                Tools::ps_round(
                    $this->getGiftWrappingPrice($withTaxes),
                    $computePrecision
                ), Currency::getCurrencyInstance((int)$this->id_currency)
            );
        }

        return $wrapping_fees;
    }

    /**
     * Get the gift wrapping price
     *
     * @param bool $with_taxes With or without taxes
     *
     * @return float wrapping price
     */
    public function getGiftWrappingPrice($with_taxes = true, $id_address = null)
    {
        static $address = array();

        $wrapping_fees = (float)Configuration::get('PS_GIFT_WRAPPING_PRICE');

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
                        $id_address = (int)$this->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
                    }
                    try {
                        $address[$this->id] = Address::initialize($id_address);
                    } catch (Exception $e) {
                        $address[$this->id] = new Address();
                        $address[$this->id]->id_country = Configuration::get('PS_COUNTRY_DEFAULT');
                    }
                }

                $tax_manager = TaxManagerFactory::getManager($address[$this->id], (int)Configuration::get('PS_GIFT_WRAPPING_TAX_RULES_GROUP'));
                $tax_calculator = $tax_manager->getTaxCalculator();
                $wrapping_fees = $tax_calculator->addTaxes($wrapping_fees);
            }
        } elseif (Configuration::get('PS_ATCP_SHIPWRAP')) {
            // With PS_ATCP_SHIPWRAP, wrapping fee is by default tax included, so we convert it
            // when asked for the pre tax price.
            $wrapping_fees = Tools::ps_round(
                $wrapping_fees / (1 + $this->getAverageProductsTaxRate()),
                _PS_PRICE_COMPUTE_PRECISION_
            );
        }

        return $wrapping_fees;
    }

    /**
     * Get the number of packages
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
     *                   0 => array( // First address
     *                       0 => array(  // First package
     *                           'product_list' => array(...),
     *                           'carrier_list' => array(...),
     *                           'id_warehouse' => array(...),
     *                       ),
     *                   ),
     *               );
     * @todo Add avaibility check
     */
    public function getPackageList($flush = false)
    {
        $cache_key = (int)$this->id.'_'.(int)$this->id_address_delivery;
        if (isset(static::$cachePackageList[$cache_key]) && static::$cachePackageList[$cache_key] !== false && !$flush) {
            return static::$cachePackageList[$cache_key];
        }

        $product_list = $this->getProducts($flush);
        // Step 1 : Get product informations (warehouse_list and carrier_list), count warehouse
        // Determine the best warehouse to determine the packages
        // For that we count the number of time we can use a warehouse for a specific delivery address
        $warehouse_count_by_address = array();

        $stock_management_active = Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT');

        foreach ($product_list as &$product) {
            if ((int)$product['id_address_delivery'] == 0) {
                $product['id_address_delivery'] = (int)$this->id_address_delivery;
            }

            if (!isset($warehouse_count_by_address[$product['id_address_delivery']])) {
                $warehouse_count_by_address[$product['id_address_delivery']] = array();
            }

            $product['warehouse_list'] = array();

            if ($stock_management_active &&
                (int)$product['advanced_stock_management'] == 1) {
                $warehouse_list = Warehouse::getProductWarehouseList($product['id_product'], $product['id_product_attribute'], $this->id_shop);
                if (count($warehouse_list) == 0) {
                    $warehouse_list = Warehouse::getProductWarehouseList($product['id_product'], $product['id_product_attribute']);
                }
                // Does the product is in stock ?
                // If yes, get only warehouse where the product is in stock

                $warehouse_in_stock = array();
                $manager = StockManagerFactory::getManager();

                foreach ($warehouse_list as $key => $warehouse) {
                    $product_real_quantities = $manager->getProductRealQuantities(
                        $product['id_product'],
                        $product['id_product_attribute'],
                        array($warehouse['id_warehouse']),
                        true
                    );

                    if ($product_real_quantities > 0 || Pack::isPack((int)$product['id_product'])) {
                        $warehouse_in_stock[] = $warehouse;
                    }
                }

                if (!empty($warehouse_in_stock)) {
                    $warehouse_list = $warehouse_in_stock;
                    $product['in_stock'] = true;
                } else {
                    $product['in_stock'] = false;
                }
            } else {
                //simulate default warehouse
                $warehouse_list = array(0 => array('id_warehouse' => 0));
                $product['in_stock'] = StockAvailable::getQuantityAvailableByProduct($product['id_product'], $product['id_product_attribute']) > 0;
            }

            foreach ($warehouse_list as $warehouse) {
                $product['warehouse_list'][$warehouse['id_warehouse']] = $warehouse['id_warehouse'];
                if (!isset($warehouse_count_by_address[$product['id_address_delivery']][$warehouse['id_warehouse']])) {
                    $warehouse_count_by_address[$product['id_address_delivery']][$warehouse['id_warehouse']] = 0;
                }

                $warehouse_count_by_address[$product['id_address_delivery']][$warehouse['id_warehouse']]++;
            }
        }
        unset($product);

        arsort($warehouse_count_by_address);

        // Step 2 : Group product by warehouse
        $grouped_by_warehouse = array();

        foreach ($product_list as &$product) {
            if (!isset($grouped_by_warehouse[$product['id_address_delivery']])) {
                $grouped_by_warehouse[$product['id_address_delivery']] = array(
                    'in_stock' => array(),
                    'out_of_stock' => array(),
                );
            }

            $product['carrier_list'] = array();
            $id_warehouse = 0;
            foreach ($warehouse_count_by_address[$product['id_address_delivery']] as $id_war => $val) {
                if (array_key_exists((int)$id_war, $product['warehouse_list'])) {
                    $product['carrier_list'] = Tools::array_replace($product['carrier_list'], Carrier::getAvailableCarrierList(new Product($product['id_product']), $id_war, $product['id_address_delivery'], null, $this));
                    if (!$id_warehouse) {
                        $id_warehouse = (int)$id_war;
                    }
                }
            }

            if (!isset($grouped_by_warehouse[$product['id_address_delivery']]['in_stock'][$id_warehouse])) {
                $grouped_by_warehouse[$product['id_address_delivery']]['in_stock'][$id_warehouse] = array();
                $grouped_by_warehouse[$product['id_address_delivery']]['out_of_stock'][$id_warehouse] = array();
            }

            if (!$this->allow_seperated_package) {
                $key = 'in_stock';
            } else {
                $key = $product['in_stock'] ? 'in_stock' : 'out_of_stock';
                $product_quantity_in_stock = StockAvailable::getQuantityAvailableByProduct($product['id_product'], $product['id_product_attribute']);
                if ($product['in_stock'] && $product['cart_quantity'] > $product_quantity_in_stock) {
                    $out_stock_part = $product['cart_quantity'] - $product_quantity_in_stock;
                    $product_bis = $product;
                    $product_bis['cart_quantity'] = $out_stock_part;
                    $product_bis['in_stock'] = 0;
                    $product['cart_quantity'] -= $out_stock_part;
                    $grouped_by_warehouse[$product['id_address_delivery']]['out_of_stock'][$id_warehouse][] = $product_bis;
                }
            }

            if (empty($product['carrier_list'])) {
                $product['carrier_list'] = array(0 => 0);
            }

            $grouped_by_warehouse[$product['id_address_delivery']][$key][$id_warehouse][] = $product;
        }
        unset($product);

        // Step 3 : grouped product from grouped_by_warehouse by available carriers
        $grouped_by_carriers = array();
        foreach ($grouped_by_warehouse as $id_address_delivery => $products_in_stock_list) {
            if (!isset($grouped_by_carriers[$id_address_delivery])) {
                $grouped_by_carriers[$id_address_delivery] = array(
                    'in_stock' => array(),
                    'out_of_stock' => array(),
                );
            }
            foreach ($products_in_stock_list as $key => $warehouse_list) {
                if (!isset($grouped_by_carriers[$id_address_delivery][$key])) {
                    $grouped_by_carriers[$id_address_delivery][$key] = array();
                }
                foreach ($warehouse_list as $id_warehouse => $product_list) {
                    if (!isset($grouped_by_carriers[$id_address_delivery][$key][$id_warehouse])) {
                        $grouped_by_carriers[$id_address_delivery][$key][$id_warehouse] = array();
                    }
                    foreach ($product_list as $product) {
                        $package_carriers_key = implode(',', $product['carrier_list']);

                        if (!isset($grouped_by_carriers[$id_address_delivery][$key][$id_warehouse][$package_carriers_key])) {
                            $grouped_by_carriers[$id_address_delivery][$key][$id_warehouse][$package_carriers_key] = array(
                                'product_list' => array(),
                                'carrier_list' => $product['carrier_list'],
                                'warehouse_list' => $product['warehouse_list']
                            );
                        }

                        $grouped_by_carriers[$id_address_delivery][$key][$id_warehouse][$package_carriers_key]['product_list'][] = $product;
                    }
                }
            }
        }

        $package_list = array();
        // Step 4 : merge product from grouped_by_carriers into $package to minimize the number of package
        foreach ($grouped_by_carriers as $id_address_delivery => $products_in_stock_list) {
            if (!isset($package_list[$id_address_delivery])) {
                $package_list[$id_address_delivery] = array(
                    'in_stock' => array(),
                    'out_of_stock' => array(),
                );
            }

            foreach ($products_in_stock_list as $key => $warehouse_list) {
                if (!isset($package_list[$id_address_delivery][$key])) {
                    $package_list[$id_address_delivery][$key] = array();
                }
                // Count occurance of each carriers to minimize the number of packages
                $carrier_count = array();
                foreach ($warehouse_list as $id_warehouse => $products_grouped_by_carriers) {
                    foreach ($products_grouped_by_carriers as $data) {
                        foreach ($data['carrier_list'] as $id_carrier) {
                            if (!isset($carrier_count[$id_carrier])) {
                                $carrier_count[$id_carrier] = 0;
                            }
                            $carrier_count[$id_carrier]++;
                        }
                    }
                }
                arsort($carrier_count);
                foreach ($warehouse_list as $id_warehouse => $products_grouped_by_carriers) {
                    if (!isset($package_list[$id_address_delivery][$key][$id_warehouse])) {
                        $package_list[$id_address_delivery][$key][$id_warehouse] = array();
                    }
                    foreach ($products_grouped_by_carriers as $data) {
                        foreach ($carrier_count as $id_carrier => $rate) {
                            if (array_key_exists($id_carrier, $data['carrier_list'])) {
                                if (!isset($package_list[$id_address_delivery][$key][$id_warehouse][$id_carrier])) {
                                    $package_list[$id_address_delivery][$key][$id_warehouse][$id_carrier] = array(
                                        'carrier_list' => $data['carrier_list'],
                                        'warehouse_list' => $data['warehouse_list'],
                                        'product_list' => array(),
                                    );
                                }
                                $package_list[$id_address_delivery][$key][$id_warehouse][$id_carrier]['carrier_list'] =
                                    array_intersect($package_list[$id_address_delivery][$key][$id_warehouse][$id_carrier]['carrier_list'], $data['carrier_list']);
                                $package_list[$id_address_delivery][$key][$id_warehouse][$id_carrier]['product_list'] =
                                    array_merge($package_list[$id_address_delivery][$key][$id_warehouse][$id_carrier]['product_list'], $data['product_list']);

                                break;
                            }
                        }
                    }
                }
            }
        }

        // Step 5 : Reduce depth of $package_list
        $final_package_list = array();
        foreach ($package_list as $id_address_delivery => $products_in_stock_list) {
            if (!isset($final_package_list[$id_address_delivery])) {
                $final_package_list[$id_address_delivery] = array();
            }

            foreach ($products_in_stock_list as $key => $warehouse_list) {
                foreach ($warehouse_list as $id_warehouse => $products_grouped_by_carriers) {
                    foreach ($products_grouped_by_carriers as $data) {
                        $final_package_list[$id_address_delivery][] = array(
                            'product_list' => $data['product_list'],
                            'carrier_list' => $data['carrier_list'],
                            'warehouse_list' => $data['warehouse_list'],
                            'id_warehouse' => $id_warehouse,
                        );
                    }
                }
            }
        }

        static::$cachePackageList[$cache_key] = $final_package_list;

        return $final_package_list;
    }

    public function getPackageIdWarehouse($package, $id_carrier = null)
    {
        if ($id_carrier === null) {
            if (isset($package['id_carrier'])) {
                $id_carrier = (int)$package['id_carrier'];
            }
        }

        if ($id_carrier == null) {
            return $package['id_warehouse'];
        }

        foreach ($package['warehouse_list'] as $id_warehouse) {
            $warehouse = new Warehouse((int)$id_warehouse);
            $available_warehouse_carriers = $warehouse->getCarriers();
            if (in_array($id_carrier, $available_warehouse_carriers)) {
                return (int)$id_warehouse;
            }
        }
        return 0;
    }

    /**
     * Get all deliveries options available for the current cart
     * @param Country $default_country
     * @param bool $flush Force flushing cache
     *
     * @return array array(
     *                   0 => array( // First address
     *                       '12,' => array(  // First delivery option available for this address
     *                           carrier_list => array(
     *                               12 => array( // First carrier for this option
     *                                   'instance' => Carrier Object,
     *                                   'logo' => <url to the carriers logo>,
     *                                   'price_with_tax' => 12.4,
     *                                   'price_without_tax' => 12.4,
     *                                   'package_list' => array(
     *                                       1,
     *                                       3,
     *                                   ),
     *                               ),
     *                           ),
     *                           is_best_grade => true, // Does this option have the biggest grade (quick shipping) for this shipping address
     *                           is_best_price => true, // Does this option have the lower price for this shipping address
     *                           unique_carrier => true, // Does this option use a unique carrier
     *                           total_price_with_tax => 12.5,
     *                           total_price_without_tax => 12.5,
     *                           position => 5, // Average of the carrier position
     *                       ),
     *                   ),
     *               );
     *               If there are no carriers available for an address, return an empty  array
     */
    public function getDeliveryOptionList(Country $default_country = null, $flush = false)
    {
        if (isset(static::$cacheDeliveryOptionList[$this->id]) && !$flush) {
            return static::$cacheDeliveryOptionList[$this->id];
        }

        $delivery_option_list = array();
        $carriers_price = array();
        $carrier_collection = array();
        $package_list = $this->getPackageList($flush);

        // Foreach addresses
        foreach ($package_list as $id_address => $packages) {
            // Initialize vars
            $delivery_option_list[$id_address] = array();
            $carriers_price[$id_address] = array();
            $common_carriers = null;
            $best_price_carriers = array();
            $best_grade_carriers = array();
            $carriers_instance = array();

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
                    $cache[$this->id] = array();
                    return $cache[$this->id];
                }

                $carriers_price[$id_address][$id_package] = array();

                // Get all common carriers for each packages to the same address
                if (is_null($common_carriers)) {
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

                    $price_with_tax = $this->getPackageShippingCost((int)$id_carrier, true, $country, $package['product_list']);
                    $price_without_tax = $this->getPackageShippingCost((int)$id_carrier, false, $country, $package['product_list']);
                    if (is_null($best_price) || $price_with_tax < $best_price) {
                        $best_price = $price_with_tax;
                        $best_price_carrier = $id_carrier;
                    }
                    $carriers_price[$id_address][$id_package][$id_carrier] = array(
                        'without_tax' => $price_without_tax,
                        'with_tax' => $price_with_tax);

                    $grade = $carriers_instance[$id_carrier]->grade;
                    if (is_null($best_grade) || $grade > $best_grade) {
                        $best_grade = $grade;
                        $best_grade_carrier = $id_carrier;
                    }
                }

                $best_price_carriers[$id_package] = $best_price_carrier;
                $best_grade_carriers[$id_package] = $best_grade_carrier;
            }

            // Reset $best_price_carrier, it's now an array
            $best_price_carrier = array();
            $key = '';

            // Get the delivery option with the lower price
            foreach ($best_price_carriers as $id_package => $id_carrier) {
                $key .= $id_carrier.',';
                if (!isset($best_price_carrier[$id_carrier])) {
                    $best_price_carrier[$id_carrier] = array(
                        'price_with_tax' => 0,
                        'price_without_tax' => 0,
                        'package_list' => array(),
                        'product_list' => array(),
                    );
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
            $delivery_option_list[$id_address][$key] = array(
                'carrier_list' => $best_price_carrier,
                'is_best_price' => true,
                'is_best_grade' => false,
                'unique_carrier' => (count($best_price_carrier) <= 1)
            );

            // Reset $best_grade_carrier, it's now an array
            $best_grade_carrier = array();
            $key = '';

            // Get the delivery option with the best grade
            foreach ($best_grade_carriers as $id_package => $id_carrier) {
                $key .= $id_carrier.',';
                if (!isset($best_grade_carrier[$id_carrier])) {
                    $best_grade_carrier[$id_carrier] = array(
                        'price_with_tax' => 0,
                        'price_without_tax' => 0,
                        'package_list' => array(),
                        'product_list' => array(),
                    );
                }
                $best_grade_carrier[$id_carrier]['price_with_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'];
                $best_grade_carrier[$id_carrier]['price_without_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'];
                $best_grade_carrier[$id_carrier]['package_list'][] = $id_package;
                $best_grade_carrier[$id_carrier]['product_list'] = array_merge($best_grade_carrier[$id_carrier]['product_list'], $packages[$id_package]['product_list']);
                $best_grade_carrier[$id_carrier]['instance'] = $carriers_instance[$id_carrier];
            }

            // Add the delivery option with best grade as best grade
            if (!isset($delivery_option_list[$id_address][$key])) {
                $delivery_option_list[$id_address][$key] = array(
                    'carrier_list' => $best_grade_carrier,
                    'is_best_price' => false,
                    'unique_carrier' => (count($best_grade_carrier) <= 1)
                );
            }
            $delivery_option_list[$id_address][$key]['is_best_grade'] = true;

            // Get all delivery options with a unique carrier
            foreach ($common_carriers as $id_carrier) {
                $key = '';
                $package_list = array();
                $product_list = array();
                $price_with_tax = 0;
                $price_without_tax = 0;

                foreach ($packages as $id_package => $package) {
                    $key .= $id_carrier.',';
                    $price_with_tax += $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'];
                    $price_without_tax += $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'];
                    $package_list[] = $id_package;
                    $product_list = array_merge($product_list, $package['product_list']);
                }

                if (!isset($delivery_option_list[$id_address][$key])) {
                    $delivery_option_list[$id_address][$key] = array(
                        'is_best_price' => false,
                        'is_best_grade' => false,
                        'unique_carrier' => true,
                        'carrier_list' => array(
                            $id_carrier => array(
                                'price_with_tax' => $price_with_tax,
                                'price_without_tax' => $price_without_tax,
                                'instance' => $carriers_instance[$id_carrier],
                                'package_list' => $package_list,
                                'product_list' => $product_list,
                            )
                        )
                    );
                } else {
                    $delivery_option_list[$id_address][$key]['unique_carrier'] = (count($delivery_option_list[$id_address][$key]['carrier_list']) <= 1);
                }
            }
        }

        $cart_rules = CartRule::getCustomerCartRules(Context::getContext()->cookie->id_lang, Context::getContext()->cookie->id_customer, true, true, false, $this, true);

        $result = false;
        if ($this->id) {
            $result = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'cart_cart_rule WHERE id_cart = '.(int)$this->id);
        }

        $cart_rules_in_cart = array();

        if (is_array($result)) {
            foreach ($result as $row) {
                $cart_rules_in_cart[] = $row['id_cart_rule'];
            }
        }

        $total_products_wt = $this->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $total_products = $this->getOrderTotal(false, Cart::ONLY_PRODUCTS);

        $free_carriers_rules = array();

        $context = Context::getContext();
        foreach ($cart_rules as $cart_rule) {
            $total_price = $cart_rule['minimum_amount_tax'] ? $total_products_wt : $total_products;
            $total_price += $cart_rule['minimum_amount_tax'] && $cart_rule['minimum_amount_shipping'] ? $real_best_price : 0;
            $total_price += !$cart_rule['minimum_amount_tax'] && $cart_rule['minimum_amount_shipping'] ? $real_best_price_wt : 0;
            if ($cart_rule['free_shipping'] && $cart_rule['carrier_restriction']
                && in_array($cart_rule['id_cart_rule'], $cart_rules_in_cart)
                && $cart_rule['minimum_amount'] <= $total_price) {
                $cr = new CartRule((int)$cart_rule['id_cart_rule']);
                if (Validate::isLoadedObject($cr) &&
                    $cr->checkValidity($context, in_array((int)$cart_rule['id_cart_rule'], $cart_rules_in_cart), false, false)) {
                    $carriers = $cr->getAssociatedRestrictions('carrier', true, false);
                    if (is_array($carriers) && count($carriers) && isset($carriers['selected'])) {
                        foreach ($carriers['selected'] as $carrier) {
                            if (isset($carrier['id_carrier']) && $carrier['id_carrier']) {
                                $free_carriers_rules[] = (int)$carrier['id_carrier'];
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
                $position = 0;
                foreach ($value['carrier_list'] as $id_carrier => $data) {
                    $total_price_with_tax += $data['price_with_tax'];
                    $total_price_without_tax += $data['price_without_tax'];
                    $total_price_without_tax_with_rules = (in_array($id_carrier, $free_carriers_rules)) ? 0 : $total_price_without_tax;

                    if (!isset($carrier_collection[$id_carrier])) {
                        $carrier_collection[$id_carrier] = new Carrier($id_carrier);
                    }
                    $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['instance'] = $carrier_collection[$id_carrier];

                    if (file_exists(_PS_SHIP_IMG_DIR_.$id_carrier.'.jpg')) {
                        $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['logo'] = _THEME_SHIP_DIR_.$id_carrier.'.jpg';
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
            uasort($array, array('Cart', 'sortDeliveryOptionList'));
        }

        static::$cacheDeliveryOptionList[$this->id] = $delivery_option_list;
        return static::$cacheDeliveryOptionList[$this->id];
    }

    /**
     *
     * Sort list of option delivery by parameters define in the BO
     * @param $option1
     * @param $option2
     * @return int -1 if $option 1 must be placed before and 1 if the $option1 must be placed after the $option2
     */
    public static function sortDeliveryOptionList($option1, $option2)
    {
        static $order_by_price = null;
        static $order_way = null;
        if (is_null($order_by_price)) {
            $order_by_price = !Configuration::get('PS_CARRIER_DEFAULT_SORT');
        }
        if (is_null($order_way)) {
            $order_way = Configuration::get('PS_CARRIER_DEFAULT_ORDER');
        }

        if ($order_by_price) {
            if ($order_way) {
                return ($option1['total_price_with_tax'] < $option2['total_price_with_tax']) * 2 - 1;
            } else {
                // return -1 or 1
                return ($option1['total_price_with_tax'] >= $option2['total_price_with_tax']) * 2 - 1;
            }
        } elseif ($order_way) {
            // return -1 or 1
            return ($option1['position'] < $option2['position']) * 2 - 1;
        } else {
            // return -1 or 1
            return ($option1['position'] >= $option2['position']) * 2 - 1;
        }
    }

    /**
     * Is the Carrier selected
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
     * Get all deliveries options available for the current cart formatted like Carriers::getCarriersForOrder
     * This method was wrote for retrocompatibility with 1.4 theme
     * New theme need to use Cart::getDeliveryOptionList() to generate carriers option in the checkout process
     *
     * @since 1.5.0
     * @deprecated 1.7.0
     *
     * @param Country $default_country Default Country
     * @param bool    $flush           Force flushing cache
     *
     * @return array
     */
    public function simulateCarriersOutput(Country $default_country = null, $flush = false)
    {
        $delivery_option_list = $this->getDeliveryOptionList($default_country, $flush);

        // This method cannot work if there is multiple address delivery
        if (count($delivery_option_list) > 1 || empty($delivery_option_list)) {
            return array();
        }

        $carriers = array();
        foreach (reset($delivery_option_list) as $key => $option) {
            $price = $option['total_price_with_tax'];
            $price_tax_exc = $option['total_price_without_tax'];
            $name = $img = $delay = '';

            if ($option['unique_carrier']) {
                $carrier = reset($option['carrier_list']);
                if (isset($carrier['instance'])) {
                    $name = $carrier['instance']->name;
                    $delay = $carrier['instance']->delay;
                    $delay = isset($delay[Context::getContext()->language->id]) ?
                        $delay[Context::getContext()->language->id] : $delay[(int)Configuration::get('PS_LANG_DEFAULT')];
                }
                if (isset($carrier['logo'])) {
                    $img = $carrier['logo'];
                }
            } else {
                $nameList = array();
                foreach ($option['carrier_list'] as $carrier) {
                    $nameList[] = $carrier['instance']->name;
                }
                $name = join(' -', $nameList);
                $img = ''; // No images if multiple carriers
                $delay = '';
            }
            $carriers[] = array(
                'name' => $name,
                'img' => $img,
                'delay' => $delay,
                'price' => $price,
                'price_tax_exc' => $price_tax_exc,
                'id_carrier' => Cart::intifier($key), // Need to translate to an integer for retrocompatibility reason, in 1.4 template we used intval
                'is_module' => false,
            );
        }
        return $carriers;
    }

    /**
     * Simulate output of selected Carrier
     *
     * @param bool $use_cache Use cache
     *
     * @return int Intified Cart output
     */
    public function simulateCarrierSelectedOutput($use_cache = true)
    {
        $delivery_option = $this->getDeliveryOption(null, false, $use_cache);

        if (count($delivery_option) > 1 || empty($delivery_option)) {
            return 0;
        }

        return Cart::intifier(reset($delivery_option));
    }

    /**
     * Translate a string option_delivery identifier ('24,3,') in a int (3240002000)
     *
     * The  option_delivery identifier is a list of integers separated by a ','.
     * This method replace the delimiter by a sequence of '0'.
     * The size of this sequence is fixed by the first digit of the return
     *
     * @return int Intified value
     */
    public static function intifier($string, $delimiter = ',')
    {
        $elm = explode($delimiter, $string);
        $max = max($elm);

        return strlen($max).implode(str_repeat('0', strlen($max) + 1), $elm);
    }

    /**
     * Translate an int option_delivery identifier (3240002000) in a string ('24,3,')
     */
    public static function desintifier($int, $delimiter = ',')
    {
        $delimiter_len = $int[0];
        $int = strrev(substr($int, 1));
        $elm = explode(str_repeat('0', $delimiter_len + 1), $int);

        return strrev(implode($delimiter, $elm));
    }

    /**
     * Does the Cart use multiple Addresses?
     *
     * @return bool Indicates if the Cart uses multiple Addresses
     */
    public function isMultiAddressDelivery()
    {
        if (!isset(static::$cacheMultiAddressDelivery[$this->id])) {
            $sql = new DbQuery();
            $sql->select('count(distinct id_address_delivery)');
            $sql->from('cart_product', 'cp');
            $sql->where('id_cart = ' . (int) $this->id);
            static::$cacheMultiAddressDelivery[$this->id] = Db::getInstance()->getValue($sql) > 1;
        }

        return static::$cacheMultiAddressDelivery[$this->id];
    }

    /**
     * Get all delivery Addresses object for the current Cart
     */
    public function getAddressCollection()
    {
        $collection = array();
        $cache_id = 'Cart::getAddressCollection'.(int)$this->id;
        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->executeS(
                'SELECT DISTINCT `id_address_delivery`
                FROM `'._DB_PREFIX_.'cart_product`
                WHERE id_cart = '.(int)$this->id
            );
            Cache::store($cache_id, $result);
        } else {
            $result = Cache::retrieve($cache_id);
        }

        $result[] = array('id_address_delivery' => (int)$this->id_address_delivery);

        foreach ($result as $row) {
            if ((int)$row['id_address_delivery'] != 0) {
                $collection[(int)$row['id_address_delivery']] = new Address((int)$row['id_address_delivery']);
            }
        }
        return $collection;
    }

    /**
     * Set the delivery option and Carrier ID, if there is only one Carrier
     *
     * @param array $delivery_option Delivery option array
     */
    public function setDeliveryOption($delivery_option = null)
    {
        if (empty($delivery_option) || count($delivery_option) == 0) {
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
     * Get Carrier ID from Delivery Option
     *
     * @param array $delivery_option Delivery options array
     *
     * @return int|mixed Carrier ID
     */
    protected function getIdCarrierFromDeliveryOption($delivery_option)
    {
        $delivery_option_list = $this->getDeliveryOptionList();
        foreach ($delivery_option as $key => $value) {
            if (isset($delivery_option_list[$key]) && isset($delivery_option_list[$key][$value])) {
                if (count($delivery_option_list[$key][$value]['carrier_list']) == 1) {
                    return current(array_keys($delivery_option_list[$key][$value]['carrier_list']));
                }
            }
        }

        return 0;
    }

    /**
     * Get the delivery option selected, or if no delivery option was selected,
     * the cheapest option for each address
     *
     * @param Country|null $default_country       Default country
     * @param bool         $dontAutoSelectOptions Do not auto select delivery option
     * @param bool         $use_cache             Use cache
     *
     * @return array|bool|mixed Delivery option
     */
    public function getDeliveryOption($default_country = null, $dontAutoSelectOptions = false, $use_cache = true)
    {
        $cache_id = (int)(is_object($default_country) ? $default_country->id : 0).'-'.(int)$dontAutoSelectOptions;
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
        $delivery_option = array();
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
     * Return shipping total for the cart
     *
     * @param array|null   $delivery_option Array of the delivery option for each address
     * @param bool         $use_tax         Use taxes
     * @param Country|null $default_country Default Country
     *
     * @return float Shipping total
     */
    public function getTotalShippingCost($delivery_option = null, $use_tax = true, Country $default_country = null)
    {
        if (isset(Context::getContext()->cookie->id_country)) {
            $default_country = new Country(Context::getContext()->cookie->id_country);
        }
        if (is_null($delivery_option)) {
            $delivery_option = $this->getDeliveryOption($default_country, false, false);
        }

        $_total_shipping = array(
            'with_tax' => 0,
            'without_tax' => 0,
        );
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
     * Return shipping total of a specific carriers for the cart
     *
     * @param int          $id_carrier      Carrier ID
     * @param array        $delivery_option Array of the delivery option for each address
     * @param bool         $useTax          Use Taxes
     * @param Country|null $default_country Default Country
     * @param array|null   $delivery_option Delivery options array
     *
     * @return float Shipping total
     */
    public function getCarrierCost($id_carrier, $useTax = true, Country $default_country = null, $delivery_option = null)
    {
        if (is_null($delivery_option)) {
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
     * @deprecated 1.5.0, use Cart->getPackageShippingCost()
     */
    public function getOrderShippingCost($id_carrier = null, $use_tax = true, Country $default_country = null, $product_list = null)
    {
        Tools::displayAsDeprecated('Use Cart->getPackageShippingCost()');
        return $this->getPackageShippingCost((int)$id_carrier, $use_tax, $default_country, $product_list);
    }

    /**
     * Return package shipping cost
     *
     * @param int          $id_carrier      Carrier ID (default : current carrier)
     * @param bool         $use_tax
     * @param Country|null $default_country
     * @param array|null   $product_list    List of product concerned by the shipping.
     *                                      If null, all the product of the cart are used to calculate the shipping cost
     * @param int|null     $id_zone         Zone ID
     *
     * @return float|bool Shipping total, false if not possible to ship with the given carrier
     */
    public function getPackageShippingCost($id_carrier = null, $use_tax = true, Country $default_country = null, $product_list = null, $id_zone = null)
    {
        if ($this->isVirtualCart()) {
            return 0;
        }

        if (!$default_country) {
            $default_country = Context::getContext()->country;
        }

        if (!is_null($product_list)) {
            foreach ($product_list as $key => $value) {
                if ($value['is_virtual'] == 1) {
                    unset($product_list[$key]);
                }
            }
        }

        if (is_null($product_list)) {
            $products = $this->getProducts(false, false, null, false);
        } else {
            $products = $product_list;
        }

        if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
            $address_id = (int)$this->id_address_invoice;
        } elseif (is_array($product_list) && count($product_list)) {
            $prod = current($product_list);
            $address_id = (int)$prod['id_address_delivery'];
        } else {
            $address_id = null;
        }
        if (!Address::addressExists($address_id)) {
            $address_id = null;
        }

        if (is_null($id_carrier) && !empty($this->id_carrier)) {
            $id_carrier = (int)$this->id_carrier;
        }

        $cache_id = 'getPackageShippingCost_'.(int)$this->id.'_'.(int)$address_id.'_'.(int)$id_carrier.'_'.(int)$use_tax.'_'.(int)$default_country->id.'_'.(int)$id_zone;
        if ($products) {
            foreach ($products as $product) {
                $cache_id .= '_'.(int)$product['id_product'].'_'.(int)$product['id_product_attribute'];
            }
        }

        if (Cache::isStored($cache_id)) {
            return Cache::retrieve($cache_id);
        }

        // Order total in default currency without fees
        $order_total = $this->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING, $product_list);

        // Start with shipping cost at 0
        $shipping_cost = 0;
        // If no product added, return 0
        if (!count($products)) {
            Cache::store($cache_id, $shipping_cost);
            return $shipping_cost;
        }

        if (!isset($id_zone)) {
            // Get id zone
            if (!$this->isMultiAddressDelivery()
                && isset($this->id_address_delivery) // Be carefull, id_address_delivery is not usefull one 1.5
                && $this->id_address_delivery
                && Customer::customerHasAddress($this->id_customer, $this->id_address_delivery)
            ) {
                $id_zone = Address::getZoneById((int)$this->id_address_delivery);
            } else {
                if (!Validate::isLoadedObject($default_country)) {
                    $default_country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'), Configuration::get('PS_LANG_DEFAULT'));
                }

                $id_zone = (int)$default_country->id_zone;
            }
        }

        if ($id_carrier && !$this->isCarrierInRange((int)$id_carrier, (int)$id_zone)) {
            $id_carrier = '';
        }

        if (empty($id_carrier) && $this->isCarrierInRange((int)Configuration::get('PS_CARRIER_DEFAULT'), (int)$id_zone)) {
            $id_carrier = (int)Configuration::get('PS_CARRIER_DEFAULT');
        }

        $total_package_without_shipping_tax_inc = $this->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING, $product_list);
        if (empty($id_carrier)) {
            if ((int)$this->id_customer) {
                $customer = new Customer((int)$this->id_customer);
                $result = Carrier::getCarriers((int)Configuration::get('PS_LANG_DEFAULT'), true, false, (int)$id_zone, $customer->getGroups());
                unset($customer);
            } else {
                $result = Carrier::getCarriers((int)Configuration::get('PS_LANG_DEFAULT'), true, false, (int)$id_zone);
            }

            foreach ($result as $k => $row) {
                if ($row['id_carrier'] == Configuration::get('PS_CARRIER_DEFAULT')) {
                    continue;
                }

                if (!isset(self::$_carriers[$row['id_carrier']])) {
                    self::$_carriers[$row['id_carrier']] = new Carrier((int)$row['id_carrier']);
                }

                /** @var Carrier $carrier */
                $carrier = self::$_carriers[$row['id_carrier']];

                $shipping_method = $carrier->getShippingMethod();
                // Get only carriers that are compliant with shipping method
                if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && $carrier->getMaxDeliveryPriceByWeight((int)$id_zone) === false)
                    || ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && $carrier->getMaxDeliveryPriceByPrice((int)$id_zone) === false)) {
                    unset($result[$k]);
                    continue;
                }

                // If out-of-range behavior carrier is set on "Desactivate carrier"
                if ($row['range_behavior']) {
                    $check_delivery_price_by_weight = Carrier::checkDeliveryPriceByWeight($row['id_carrier'], $this->getTotalWeight(), (int)$id_zone);

                    $total_order = $total_package_without_shipping_tax_inc;
                    $check_delivery_price_by_price = Carrier::checkDeliveryPriceByPrice($row['id_carrier'], $total_order, (int)$id_zone, (int)$this->id_currency);

                    // Get only carriers that have a range compatible with cart
                    if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && !$check_delivery_price_by_weight)
                        || ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && !$check_delivery_price_by_price)) {
                        unset($result[$k]);
                        continue;
                    }
                }

                if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
                    $shipping = $carrier->getDeliveryPriceByWeight($this->getTotalWeight($product_list), (int)$id_zone);
                } else {
                    $shipping = $carrier->getDeliveryPriceByPrice($order_total, (int)$id_zone, (int)$this->id_currency);
                }

                if (!isset($min_shipping_price)) {
                    $min_shipping_price = $shipping;
                }

                if ($shipping <= $min_shipping_price) {
                    $id_carrier = (int)$row['id_carrier'];
                    $min_shipping_price = $shipping;
                }
            }
        }

        if (empty($id_carrier)) {
            $id_carrier = Configuration::get('PS_CARRIER_DEFAULT');
        }

        if (!isset(self::$_carriers[$id_carrier])) {
            self::$_carriers[$id_carrier] = new Carrier((int)$id_carrier, Configuration::get('PS_LANG_DEFAULT'));
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
        if ($use_tax && !Tax::excludeTaxeOption()) {
            $address = Address::initialize((int)$address_id);

            if (Configuration::get('PS_ATCP_SHIPWRAP')) {
                // With PS_ATCP_SHIPWRAP, pre-tax price is deduced
                // from post tax price, so no $carrier_tax here
                // even though it sounds weird.
                $carrier_tax = 0;
            } else {
                $carrier_tax = $carrier->getTaxesRate($address);
            }
        }

        $configuration = Configuration::getMultiple(array(
            'PS_SHIPPING_FREE_PRICE',
            'PS_SHIPPING_HANDLING',
            'PS_SHIPPING_METHOD',
            'PS_SHIPPING_FREE_WEIGHT'
        ));

        // Free fees
        $free_fees_price = 0;
        if (isset($configuration['PS_SHIPPING_FREE_PRICE'])) {
            $free_fees_price = Tools::convertPrice((float)$configuration['PS_SHIPPING_FREE_PRICE'], Currency::getCurrencyInstance((int)$this->id_currency));
        }
        $orderTotalwithDiscounts = $this->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING, null, null, false);
        if ($orderTotalwithDiscounts >= (float)($free_fees_price) && (float)($free_fees_price) > 0) {
            $shipping_cost = $this->getPackageShippingCostFromModule($carrier, $shipping_cost, $products);
            Cache::store($cache_id, $shipping_cost);
            return $shipping_cost;
        }

        if (isset($configuration['PS_SHIPPING_FREE_WEIGHT'])
            && $this->getTotalWeight() >= (float)$configuration['PS_SHIPPING_FREE_WEIGHT']
            && (float)$configuration['PS_SHIPPING_FREE_WEIGHT'] > 0) {
            $shipping_cost = $this->getPackageShippingCostFromModule($carrier, $shipping_cost, $products);
            Cache::store($cache_id, $shipping_cost);
            return $shipping_cost;
        }

        // Get shipping cost using correct method
        if ($carrier->range_behavior) {
            if (!isset($id_zone)) {
                // Get id zone
                if (isset($this->id_address_delivery)
                    && $this->id_address_delivery
                    && Customer::customerHasAddress($this->id_customer, $this->id_address_delivery)) {
                    $id_zone = Address::getZoneById((int)$this->id_address_delivery);
                } else {
                    $id_zone = (int)$default_country->id_zone;
                }
            }

            if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && !Carrier::checkDeliveryPriceByWeight($carrier->id, $this->getTotalWeight(), (int)$id_zone))
                || ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && !Carrier::checkDeliveryPriceByPrice($carrier->id, $total_package_without_shipping_tax_inc, $id_zone, (int)$this->id_currency)
                )) {
                $shipping_cost += 0;
            } else {
                if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
                    $shipping_cost += $carrier->getDeliveryPriceByWeight($this->getTotalWeight($product_list), $id_zone);
                } else { // by price
                    $shipping_cost += $carrier->getDeliveryPriceByPrice($order_total, $id_zone, (int)$this->id_currency);
                }
            }
        } else {
            if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
                $shipping_cost += $carrier->getDeliveryPriceByWeight($this->getTotalWeight($product_list), $id_zone);
            } else {
                $shipping_cost += $carrier->getDeliveryPriceByPrice($order_total, $id_zone, (int)$this->id_currency);
            }
        }
        // Adding handling charges
        if (isset($configuration['PS_SHIPPING_HANDLING']) && $carrier->shipping_handling) {
            $shipping_cost += (float)$configuration['PS_SHIPPING_HANDLING'];
        }

        // Additional Shipping Cost per product
        foreach ($products as $product) {
            if (!$product['is_virtual']) {
                $shipping_cost += $product['additional_shipping_cost'] * $product['cart_quantity'];
            }
        }

        $shipping_cost = Tools::convertPrice($shipping_cost, Currency::getCurrencyInstance((int)$this->id_currency));

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

        $shipping_cost = (float)Tools::ps_round((float)$shipping_cost, 2);
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
     * @return boolean|float The package price for the module (0 if free, false is disabled)
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

        if (property_exists($module, 'id_carrier')) {
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
     * Return total Cart weight
     *
     * @return float Total Cart weight
     */
    public function getTotalWeight($products = null)
    {
        if (!is_null($products)) {
            $total_weight = 0;
            foreach ($products as $product) {
                if (!isset($product['weight_attribute']) || is_null($product['weight_attribute'])) {
                    $total_weight += $product['weight'] * $product['cart_quantity'];
                } else {
                    $total_weight += $product['weight_attribute'] * $product['cart_quantity'];
                }
            }
            return $total_weight;
        }

        if (!isset(self::$_totalWeight[$this->id])) {
            $this->updateProductWeight($this->id);
        }

        return self::$_totalWeight[$this->id];
    }

    /**
     * @param int $productId
     */
    protected function updateProductWeight($productId)
    {
        $productId = (int) $productId;

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

        $weight_product_without_attribute = Db::getInstance()->getValue('
            SELECT SUM(p.`weight` * cp.`quantity`) as nb
            FROM `' . _DB_PREFIX_ . 'cart_product` cp
            LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (cp.`id_product` = p.`id_product`)
            WHERE (cp.`id_product_attribute` IS NULL OR cp.`id_product_attribute` = 0)
            AND cp.`id_cart` = ' . $productId);

        $weight_cart_customizations = Db::getInstance()->getValue('
            SELECT SUM(cd.`weight` * c.`quantity`) FROM `' . _DB_PREFIX_ . 'customization` c
            LEFT JOIN `' . _DB_PREFIX_ . 'customized_data` cd ON (c.`id_customization` = cd.`id_customization`)
            WHERE c.`in_cart` = 1 AND c.`id_cart` = ' . $productId);

        self::$_totalWeight[$productId] = round(
            (float)$weight_product_with_attribute +
            (float)$weight_product_without_attribute +
            (float)$weight_cart_customizations,
            6
        );
    }

    /**
     * @deprecated 1.5.0
     *
     * @param CartRule $obj
     *
     * @return bool|string
     */
    public function checkDiscountValidity($obj, $discounts, $order_total, $products, $check_cart_discount = false)
    {
        Tools::displayAsDeprecated();
        $context = Context::getContext()->cloneContext();
        $context->cart = $this;

        return $obj->checkValidity($context);
    }

    /**
     * Return useful information about the cart
     *
     * @return array Cart details
     */
    public function getSummaryDetails($id_lang = null, $refresh = false)
    {
        $context = Context::getContext();
        if (!$id_lang) {
            $id_lang = $context->language->id;
        }

        $delivery = new Address((int)$this->id_address_delivery);
        $invoice = new Address((int)$this->id_address_invoice);

        // New layout system with personalization fields
        $formatted_addresses = array(
            'delivery' => AddressFormat::getFormattedLayoutData($delivery),
            'invoice' => AddressFormat::getFormattedLayoutData($invoice)
        );

        $base_total_tax_inc = $this->getOrderTotal(true);
        $base_total_tax_exc = $this->getOrderTotal(false);

        $total_tax = $base_total_tax_inc - $base_total_tax_exc;

        if ($total_tax < 0) {
            $total_tax = 0;
        }

        $currency = new Currency($this->id_currency);

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
                $reduction = (!Product::getTaxCalculationMethod() ? (float)$product['price_wt'] : (float)$product['price']) - (float)$product['price_without_quantity_discount'];
                $product['reduction_formatted'] = Tools::displayPrice($reduction);
            }
        }

        $gift_products = array();
        $cart_rules = $this->getCartRules();
        $total_shipping = $this->getTotalShippingCost();
        $total_shipping_tax_exc = $this->getTotalShippingCost(null, false);
        $total_products_wt = $this->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $total_products = $this->getOrderTotal(false, Cart::ONLY_PRODUCTS);
        $total_discounts = $this->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
        $total_discounts_tax_exc = $this->getOrderTotal(false, Cart::ONLY_DISCOUNTS);

        // The cart content is altered for display
        foreach ($cart_rules as &$cart_rule) {
            // If the cart rule is automatic (wihtout any code) and include free shipping, it should not be displayed as a cart rule but only set the shipping cost to 0
            if ($cart_rule['free_shipping'] && (empty($cart_rule['code']) || preg_match('/^'.CartRule::BO_ORDER_CODE_PREFIX.'[0-9]+/', $cart_rule['code']))) {
                $cart_rule['value_real'] -= $total_shipping;
                $cart_rule['value_tax_exc'] -= $total_shipping_tax_exc;
                $cart_rule['value_real'] = Tools::ps_round($cart_rule['value_real'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
                $cart_rule['value_tax_exc'] = Tools::ps_round($cart_rule['value_tax_exc'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
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
                    if (empty($product['gift']) && $product['id_product'] == $cart_rule['gift_product'] && $product['id_product_attribute'] == $cart_rule['gift_product_attribute']) {
                        // Update total products
                        $total_products_wt = Tools::ps_round($total_products_wt - $product['price_wt'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
                        $total_products = Tools::ps_round($total_products - $product['price'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);

                        // Update total discounts
                        $total_discounts = Tools::ps_round($total_discounts - $product['price_wt'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
                        $total_discounts_tax_exc = Tools::ps_round($total_discounts_tax_exc - $product['price'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);

                        // Update cart rule value
                        $cart_rule['value_real'] = Tools::ps_round($cart_rule['value_real'] - $product['price_wt'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
                        $cart_rule['value_tax_exc'] = Tools::ps_round($cart_rule['value_tax_exc'] - $product['price'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);

                        // Update product quantity
                        $product['total_wt'] = Tools::ps_round($product['total_wt'] - $product['price_wt'], (int)$currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
                        $product['total'] = Tools::ps_round($product['total'] - $product['price'], (int)$currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
                        $product['cart_quantity']--;

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
                        $gift_product['gift'] = true;
                        $gift_products[] = $gift_product;

                        break; // One gift product per cart rule
                    }
                }
            }
        }

        foreach ($cart_rules as $key => &$cart_rule) {
            if (((float)$cart_rule['value_real'] == 0 && (int)$cart_rule['free_shipping'] == 0)) {
                unset($cart_rules[$key]);
            }
        }

        $summary = array(
            'delivery' => $delivery,
            'delivery_state' => State::getNameById($delivery->id_state),
            'invoice' => $invoice,
            'invoice_state' => State::getNameById($invoice->id_state),
            'formattedAddresses' => $formatted_addresses,
            'products' => array_values($products),
            'gift_products' => $gift_products,
            'discounts' => array_values($cart_rules),
            'is_virtual_cart' => (int)$this->isVirtualCart(),
            'total_discounts' => $total_discounts,
            'total_discounts_tax_exc' => $total_discounts_tax_exc,
            'total_wrapping' => $this->getOrderTotal(true, Cart::ONLY_WRAPPING),
            'total_wrapping_tax_exc' => $this->getOrderTotal(false, Cart::ONLY_WRAPPING),
            'total_shipping' => $total_shipping,
            'total_shipping_tax_exc' => $total_shipping_tax_exc,
            'total_products_wt' => $total_products_wt,
            'total_products' => $total_products,
            'total_price' => $base_total_tax_inc,
            'total_tax' => $total_tax,
            'total_price_without_tax' => $base_total_tax_exc,
            'is_multi_address_delivery' => $this->isMultiAddressDelivery() || ((int)Tools::getValue('multi-shipping') == 1),
            'free_ship' =>!$total_shipping && !count($this->getDeliveryAddressesWithoutCarriers(true, $errors)),
            'carrier' => new Carrier($this->id_carrier, $id_lang),
        );

        $hook = Hook::exec('actionCartSummary', $summary, null, true);
        if (is_array($hook)) {
            $summary = array_merge($summary, (array)array_shift($hook));
        }

        return $summary;
    }

    /**
     * Check if product quantities in Cart are available
     *
     * @param bool $return_product Return the Product with not enough quantity instead
     *
     * @return bool|Product Indicates if there is enough in stock
     */
    public function checkQuantities($return_product = false)
    {
        if (Configuration::isCatalogMode() && !defined('_PS_ADMIN_DIR_')) {
            return false;
        }

        foreach ($this->getProducts() as $product) {
            if (!$this->allow_seperated_package && !$product['allow_oosp'] && StockAvailable::dependsOnStock($product['id_product']) &&
                $product['advanced_stock_management'] && (bool)Context::getContext()->customer->isLogged() && ($delivery = $this->getDeliveryOption()) && !empty($delivery)) {
                $product['stock_quantity'] = StockManager::getStockByCarrier((int)$product['id_product'], (int)$product['id_product_attribute'], $delivery);
            }
            if (!$product['active'] || !$product['available_for_order']
                || (!$product['allow_oosp'] && $product['stock_quantity'] < $product['cart_quantity'])) {
                return $return_product ? $product : false;
            }
            if (!$product['allow_oosp']) {
                $productQuantity = Product::getQuantity(
                    $product['id_product'],
                    $product['id_product_attribute'],
                    null,
                    $this,
                    $product['id_customization']
                );
                if ($productQuantity < 0) {
                    return $return_product ? $product : false;
                }
            }
        }

        return true;
    }

    /**
     * Check if the product can be accessed by the Customer
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
     * Last abandoned Cart
     *
     * @param int $id_customer Customer ID
     *
     * @return bool|int Last abandoned Cart ID
     *                  false if not found
     */
    public static function lastNoneOrderedCart($id_customer)
    {
        $sql = 'SELECT c.`id_cart`
                FROM '._DB_PREFIX_.'cart c
                WHERE NOT EXISTS (SELECT 1 FROM '._DB_PREFIX_.'orders o WHERE o.`id_cart` = c.`id_cart`
                                    AND o.`id_customer` = '.(int)$id_customer.')
                AND c.`id_customer` = '.(int)$id_customer.'
                    '.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'c').'
                ORDER BY c.`date_upd` DESC';

        if (!$id_cart = Db::getInstance()->getValue($sql)) {
            return false;
        }

        return (int)$id_cart;
    }

    /**
     * Check if cart contains only virtual products
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
     * Check if there's a product in the cart
     *
     * @return bool
     */
    public function hasProducts()
    {
        return (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT 1 FROM '._DB_PREFIX_.'cart_product cp '.
            'INNER JOIN '._DB_PREFIX_.'product p
                ON (p.id_product = cp.id_product) '.
            'INNER JOIN '._DB_PREFIX_.'product_shop ps
                ON (ps.id_shop = cp.id_shop AND ps.id_product = p.id_product) '.
            'WHERE cp.id_cart='.(int)$this->id
        );
    }

    /**
     * Return true if the current cart contains a real product
     *
     * @return bool
     */
    public function hasRealProducts()
    {
        return (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT 1 FROM '._DB_PREFIX_.'cart_product cp '.
            'INNER JOIN '._DB_PREFIX_.'product p
                ON (p.is_virtual = 0 AND p.id_product = cp.id_product) '.
            'INNER JOIN '._DB_PREFIX_.'product_shop ps
                ON (ps.id_shop = cp.id_shop AND ps.id_product = p.id_product) '.
            'WHERE cp.id_cart='.(int)$this->id
        );
    }

    /**
     * Build cart object from provided id_order
     *
     * @param int $id_order
     * @return Cart|bool
     */
    public static function getCartByOrderId($id_order)
    {
        if ($id_cart = Cart::getCartIdByOrderId($id_order)) {
            return new Cart((int)$id_cart);
        }

        return false;
    }

    /**
     * Get Cart ID by Order ID
     *
     * @param int $id_order Order ID
     *
     * @return int|bool Cart ID, false if not found
     */
    public static function getCartIdByOrderId($id_order)
    {
        $result = Db::getInstance()->getRow('SELECT `id_cart` FROM '._DB_PREFIX_.'orders WHERE `id_order` = '.(int)$id_order);
        if (!$result || empty($result) || !array_key_exists('id_cart', $result)) {
            return false;
        }
        return $result['id_cart'];
    }

    /**
     * Add customer's text
     *
     * @params int $id_product Product ID
     * @params int $index
     * @params int $type
     * @params string $textValue
     *
     * @return bool Always true
     * @todo: Improve this PHPDoc comment
     */
    public function addTextFieldToProduct($id_product, $index, $type, $text_value)
    {
        return $this->_addCustomization($id_product, 0, $index, $type, $text_value, 0);
    }

    /**
     * Add customer's pictures
     *
     * @return bool Always true
     */
    public function addPictureToProduct($id_product, $index, $type, $file)
    {
        return $this->_addCustomization($id_product, 0, $index, $type, $file, 0);
    }

    /**
     * @deprecated 1.5.5.0
     * @param int $id_product
     * @param $index
     *
     * @return bool
     */
    public function deletePictureToProduct($id_product, $index)
    {
        Tools::displayAsDeprecated('Use deleteCustomizationToProduct() instead');
        return $this->deleteCustomizationToProduct($id_product, 0);
    }

    /**
     * Remove a customer's customization
     *
     * @param int $id_product Product ID
     * @param int $index
     *
     * @return bool
     * @todo: Improve this PHPDoc comment
     */
    public function deleteCustomizationToProduct($id_product, $index)
    {
        $result = true;

        $cust_data = Db::getInstance()->getRow(
            'SELECT cu.`id_customization`, cd.`index`, cd.`value`, cd.`type` FROM `'._DB_PREFIX_.'customization` cu
            LEFT JOIN `'._DB_PREFIX_.'customized_data` cd
            ON cu.`id_customization` = cd.`id_customization`
            WHERE cu.`id_cart` = '.(int)$this->id.'
            AND cu.`id_product` = '.(int)$id_product.'
            AND `index` = '.(int)$index.'
            AND `in_cart` = 0'
        );

        // Delete customization picture if necessary
        if ($cust_data['type'] == 0) {
            $result &= (@unlink(_PS_UPLOAD_DIR_.$cust_data['value']) && @unlink(_PS_UPLOAD_DIR_.$cust_data['value'].'_small'));
        }

        $result &= Db::getInstance()->execute(
            'DELETE FROM `'._DB_PREFIX_.'customized_data`
            WHERE `id_customization` = '.(int)$cust_data['id_customization'].'
            AND `index` = '.(int)$index
        );
        return $result;
    }

    /**
     * Return custom pictures in this cart for a specified product
     *
     * @param int  $id_product  Product ID
     * @param int  $type        Only return customization of this type
     * @param bool $not_in_cart Only return customizations that are not in the cart already
     *
     * @return array Result from DB
     */
    public function getProductCustomization($id_product, $type = null, $not_in_cart = false)
    {
        if (!Customization::isFeatureActive()) {
            return array();
        }

        $result = Db::getInstance()->executeS(
            'SELECT cu.id_customization, cd.index, cd.value, cd.type, cu.in_cart, cu.quantity
            FROM `'._DB_PREFIX_.'customization` cu
            LEFT JOIN `'._DB_PREFIX_.'customized_data` cd ON (cu.`id_customization` = cd.`id_customization`)
            WHERE cu.id_cart = '.(int)$this->id.'
            AND cu.id_product = '.(int)$id_product.
            ($type === Product::CUSTOMIZE_FILE ? ' AND type = '.(int)Product::CUSTOMIZE_FILE : '').
            ($type === Product::CUSTOMIZE_TEXTFIELD ? ' AND type = '.(int)Product::CUSTOMIZE_TEXTFIELD : '').
            ($not_in_cart ? ' AND in_cart = 0' : '')
        );
        return $result;
    }

    /**
     * Get Carts by Customer ID
     *
     * @param int  $id_customer Customer ID
     * @param bool $with_order  Only return Carts that have been converted into an Order
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource DB result
     */
    public static function getCustomerCarts($id_customer, $with_order = true)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT *
        FROM '._DB_PREFIX_.'cart c
        WHERE c.`id_customer` = '.(int)$id_customer.'
        '.(!$with_order ? 'AND NOT EXISTS (SELECT 1 FROM '._DB_PREFIX_.'orders o WHERE o.`id_cart` = c.`id_cart`)' : '').'
        ORDER BY c.`date_add` DESC');
    }

    /**
     * If the carrier name is 0, use this function to replace it with the shop name
     *
     * @param string $echo Text to use
     * @param string $tr   Unused parameter
     *
     * @return string
     * @todo: Remove unused parameter
     */
    public static function replaceZeroByShopName($echo, $tr)
    {
        return ($echo == '0' ? Carrier::getCarrierNameFromShopName() : $echo);
    }

    /**
     * Duplicate this Cart in the database
     *
     * @return array Duplicated cart, with success bool
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

        if (!Customer::customerHasAddress((int)$cart->id_customer, (int)$cart->id_address_delivery)) {
            $cart->id_address_delivery = (int)Address::getFirstCustomerAddressId((int)$cart->id_customer);
        }

        if (!Customer::customerHasAddress((int)$cart->id_customer, (int)$cart->id_address_invoice)) {
            $cart->id_address_invoice = (int)Address::getFirstCustomerAddressId((int)$cart->id_customer);
        }

        if ($cart->id_customer) {
            $cart->secure_key = Cart::$_customer->secure_key;
        }

        $cart->add();

        if (!Validate::isLoadedObject($cart)) {
            return false;
        }

        $success = true;
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'cart_product` WHERE `id_cart` = '.(int)$this->id);

        $orderId = Order::getIdByCartId((int)$this->id);
        $product_gift = array();
        if ($orderId) {
            $product_gift = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT cr.`gift_product`, cr.`gift_product_attribute` FROM `'._DB_PREFIX_.'cart_rule` cr LEFT JOIN `'._DB_PREFIX_.'order_cart_rule` ocr ON (ocr.`id_order` = '.(int)$orderId.') WHERE ocr.`id_cart_rule` = cr.`id_cart_rule`');
        }

        $id_address_delivery = Configuration::get('PS_ALLOW_MULTISHIPPING') ? $cart->id_address_delivery : 0;

        // Customized products: duplicate customizations before products so that we get new id_customizations
        $customs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT *
            FROM '._DB_PREFIX_.'customization c
            LEFT JOIN '._DB_PREFIX_.'customized_data cd ON cd.id_customization = c.id_customization
            WHERE c.id_cart = '.(int)$this->id
        );

        // Get datas from customization table
        $customs_by_id = array();
        foreach ($customs as $custom) {
            if (!isset($customs_by_id[$custom['id_customization']])) {
                $customs_by_id[$custom['id_customization']] = array(
                    'id_product_attribute' => $custom['id_product_attribute'],
                    'id_product' => $custom['id_product'],
                    'quantity' => $custom['quantity']
                );
            }
        }

        // Backward compatibility: if true set customizations quantity to 0, they will be updated in Cart::_updateCustomizationQuantity
        $new_customization_method = (int)Db::getInstance()->getValue('
            SELECT COUNT(`id_customization`) FROM `'._DB_PREFIX_.'cart_product`
            WHERE `id_cart` = '.(int)$this->id.
                ' AND `id_customization` != 0'
            ) > 0;

        // Insert new customizations
        $custom_ids = array();
        foreach ($customs_by_id as $customization_id => $val) {
            if ($new_customization_method) {
                $val['quantity'] = 0;
            }
            Db::getInstance()->execute(
                'INSERT INTO `'._DB_PREFIX_.'customization` (id_cart, id_product_attribute, id_product, `id_address_delivery`, quantity, `quantity_refunded`, `quantity_returned`, `in_cart`)
                VALUES('.(int)$cart->id.', '.(int)$val['id_product_attribute'].', '.(int)$val['id_product'].', '.(int)$id_address_delivery.', '.(int)$val['quantity'].', 0, 0, 1)'
            );
            $custom_ids[$customization_id] = Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();
        }

        // Insert customized_data
        if (count($customs)) {
            $first = true;
            $sql_custom_data = 'INSERT INTO '._DB_PREFIX_.'customized_data (`id_customization`, `type`, `index`, `value`, `id_module`, `price`, `weight`) VALUES ';
            foreach ($customs as $custom) {
                if (!$first) {
                    $sql_custom_data .= ',';
                } else {
                    $first = false;
                }

                $customized_value = $custom['value'];

                if ((int)$custom['type'] == 0) {
                    $customized_value = md5(uniqid(rand(), true));
                    Tools::copy(_PS_UPLOAD_DIR_.$custom['value'], _PS_UPLOAD_DIR_.$customized_value);
                    Tools::copy(_PS_UPLOAD_DIR_.$custom['value'].'_small', _PS_UPLOAD_DIR_.$customized_value.'_small');
                }

                $sql_custom_data .= '('.(int)$custom_ids[$custom['id_customization']].', '.(int)$custom['type'].', '.
                    (int)$custom['index'].', \''.pSQL($customized_value).'\', '.
                    (int)$custom['id_module'].', '.(float)$custom['price'].', '.(float)$custom['weight'].')';
            }
            Db::getInstance()->execute($sql_custom_data);
        }

        foreach ($products as $product) {
            if ($id_address_delivery) {
                if (Customer::customerHasAddress((int)$cart->id_customer, $product['id_address_delivery'])) {
                    $id_address_delivery = $product['id_address_delivery'];
                }
            }

            foreach ($product_gift as $gift) {
                if (isset($gift['gift_product']) && isset($gift['gift_product_attribute']) && (int)$gift['gift_product'] == (int)$product['id_product'] && (int)$gift['gift_product_attribute'] == (int)$product['id_product_attribute']) {
                    $product['quantity'] = (int)$product['quantity'] - 1;
                }
            }

            $id_customization = (int)$product['id_customization'];

            $success &= $cart->updateQty(
                (int)$product['quantity'],
                (int)$product['id_product'],
                (int)$product['id_product_attribute'],
                isset($custom_ids[$id_customization]) ? (int)$custom_ids[$id_customization] : 0,
                'up',
                (int)$id_address_delivery,
                new Shop((int)$cart->id_shop),
                false,
                false
            );
        }

        return array('cart' => $cart, 'success' => $success);
    }

    /**
     * Get Cart rows from DB for the webservice
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource DB result
     */
    public function getWsCartRows()
    {
        return Db::getInstance()->executeS(
            'SELECT id_product, id_product_attribute, quantity, id_address_delivery
            FROM `'._DB_PREFIX_.'cart_product`
            WHERE id_cart = '.(int)$this->id.' AND id_shop = '.(int)Context::getContext()->shop->id
        );
    }

    /**
     * Insert cart rows from webservice
     *
     * @param array $values Values from webservice
     *
     * @return bool Whether the values have been successfully inserted
     * @todo: This function always returns true, make it depend on actual result of DB query
     */
    public function setWsCartRows($values)
    {
        if ($this->deleteAssociations()) {
            $query = 'INSERT INTO `'._DB_PREFIX_.'cart_product`(`id_cart`, `id_product`, `id_product_attribute`, `id_address_delivery`, `quantity`, `date_add`, `id_shop`) VALUES ';

            foreach ($values as $value) {
                $query .= '('.(int)$this->id.', '.(int)$value['id_product'].', '.
                    (isset($value['id_product_attribute']) ? (int)$value['id_product_attribute'] : 'NULL').', '.
                    (isset($value['id_address_delivery']) ? (int)$value['id_address_delivery'] : 0).', '.
                    (int)$value['quantity'].', NOW(), '.(int)Context::getContext()->shop->id.'),';
            }

            Db::getInstance()->execute(rtrim($query, ','));
        }

        return true;
    }

    /**
     * Set delivery Address of a Product in the Cart
     *
     * @param int $id_product              Product ID
     * @param int $id_product_attribute    Product Attribute ID
     * @param int $old_id_address_delivery Old delivery Address ID
     * @param int $new_id_address_delivery New delivery Address ID
     *
     * @return bool Whether the delivery Address of the product in the Cart has been successfully updated
     */
    public function setProductAddressDelivery($id_product, $id_product_attribute, $old_id_address_delivery, $new_id_address_delivery)
    {
        // Check address is linked with the customer
        if (!Customer::customerHasAddress(Context::getContext()->customer->id, $new_id_address_delivery)) {
            return false;
        }

        if ($new_id_address_delivery == $old_id_address_delivery) {
            return false;
        }

        // Checking if the product with the old address delivery exists
        $sql = new DbQuery();
        $sql->select('count(*)');
        $sql->from('cart_product', 'cp');
        $sql->where('id_product = '.(int)$id_product);
        $sql->where('id_product_attribute = '.(int)$id_product_attribute);
        $sql->where('id_address_delivery = '.(int)$old_id_address_delivery);
        $sql->where('id_cart = '.(int)$this->id);
        $result = Db::getInstance()->getValue($sql);

        if ($result == 0) {
            return false;
        }

        // Checking if there is no others similar products with this new address delivery
        $sql = new DbQuery();
        $sql->select('sum(quantity) as qty');
        $sql->from('cart_product', 'cp');
        $sql->where('id_product = '.(int)$id_product);
        $sql->where('id_product_attribute = '.(int)$id_product_attribute);
        $sql->where('id_address_delivery = '.(int)$new_id_address_delivery);
        $sql->where('id_cart = '.(int)$this->id);
        $result = Db::getInstance()->getValue($sql);

        // Removing similar products with this new address delivery
        $sql = 'DELETE FROM '._DB_PREFIX_.'cart_product
            WHERE id_product = '.(int)$id_product.'
            AND id_product_attribute = '.(int)$id_product_attribute.'
            AND id_address_delivery = '.(int)$new_id_address_delivery.'
            AND id_cart = '.(int)$this->id.'
            LIMIT 1';
        Db::getInstance()->execute($sql);

        // Changing the address
        $sql = 'UPDATE '._DB_PREFIX_.'cart_product
            SET `id_address_delivery` = '.(int)$new_id_address_delivery.',
            `quantity` = `quantity` + '.(int)$result.'
            WHERE id_product = '.(int)$id_product.'
            AND id_product_attribute = '.(int)$id_product_attribute.'
            AND id_address_delivery = '.(int)$old_id_address_delivery.'
            AND id_cart = '.(int)$this->id.'
            LIMIT 1';
        Db::getInstance()->execute($sql);

        // Changing the address of the customizations
        $sql = 'UPDATE '._DB_PREFIX_.'customization
            SET `id_address_delivery` = '.(int)$new_id_address_delivery.'
            WHERE id_product = '.(int)$id_product.'
            AND id_product_attribute = '.(int)$id_product_attribute.'
            AND id_address_delivery = '.(int)$old_id_address_delivery.'
            AND id_cart = '.(int)$this->id;
        Db::getInstance()->execute($sql);

        return true;
    }

    /**
     * Set customized data of a product
     *
     * @param Product $product Referenced Product object
     * @param array $customized_datas Customized data
     */
    public function setProductCustomizedDatas(&$product, $customized_datas)
    {
        $product['customizedDatas'] = null;
        if (isset($customized_datas[$product['id_product']][$product['id_product_attribute']])) {
            $product['customizedDatas'] = $customized_datas[$product['id_product']][$product['id_product_attribute']];
        } else {
            $product['customizationQuantityTotal'] = 0;
        }
    }

    /**
     * Duplicate Product
     *
     * @param int  $id_product              Product ID
     * @param int  $id_product_attribute    Product Attribute ID
     * @param int  $id_address_delivery     Delivery Address ID
     * @param int  $new_id_address_delivery New Delivery Address ID
     * @param int  $quantity                Quantity
     * @param bool $keep_quantity           Keep the quantity, do not reset if true
     *
     * @return bool Whether the product has been successfully duplicated
     */
    public function duplicateProduct(
        $id_product,
        $id_product_attribute,
        $id_address_delivery,
        $new_id_address_delivery,
        $quantity = 1,
        $keep_quantity = false
    ) {
        // Check address is linked with the customer
        if (!Customer::customerHasAddress(Context::getContext()->customer->id, $new_id_address_delivery)) {
            return false;
        }

        // Checking the product do not exist with the new address
        $sql = new DbQuery();
        $sql->select('count(*)');
        $sql->from('cart_product', 'c');
        $sql->where('id_product = '.(int)$id_product);
        $sql->where('id_product_attribute = '.(int)$id_product_attribute);
        $sql->where('id_address_delivery = '.(int)$new_id_address_delivery);
        $sql->where('id_cart = '.(int)$this->id);
        $result = Db::getInstance()->getValue($sql);

        if ($result > 0) {
            return false;
        }

        // Duplicating cart_product line
        $sql = 'INSERT INTO '._DB_PREFIX_.'cart_product
            (`id_cart`, `id_product`, `id_shop`, `id_product_attribute`, `quantity`, `date_add`, `id_address_delivery`)
            values(
                '.(int)$this->id.',
                '.(int)$id_product.',
                '.(int)$this->id_shop.',
                '.(int)$id_product_attribute.',
                '.(int)$quantity.',
                NOW(),
                '.(int)$new_id_address_delivery.')';

        Db::getInstance()->execute($sql);

        if (!$keep_quantity) {
            $sql = new DbQuery();
            $sql->select('quantity');
            $sql->from('cart_product', 'c');
            $sql->where('id_product = '.(int)$id_product);
            $sql->where('id_product_attribute = '.(int)$id_product_attribute);
            $sql->where('id_address_delivery = '.(int)$id_address_delivery);
            $sql->where('id_cart = '.(int)$this->id);
            $duplicatedQuantity = Db::getInstance()->getValue($sql);

            if ($duplicatedQuantity > $quantity) {
                $sql = 'UPDATE '._DB_PREFIX_.'cart_product
                    SET `quantity` = `quantity` - '.(int)$quantity.'
                    WHERE id_cart = '.(int)$this->id.'
                    AND id_product = '.(int)$id_product.'
                    AND id_shop = '.(int)$this->id_shop.'
                    AND id_product_attribute = '.(int)$id_product_attribute.'
                    AND id_address_delivery = '.(int)$id_address_delivery;
                Db::getInstance()->execute($sql);
            }
        }

        // Checking if there is customizations
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('customization', 'c');
        $sql->where('id_product = '.(int)$id_product);
        $sql->where('id_product_attribute = '.(int)$id_product_attribute);
        $sql->where('id_address_delivery = '.(int)$id_address_delivery);
        $sql->where('id_cart = '.(int)$this->id);
        $results = Db::getInstance()->executeS($sql);

        foreach ($results as $customization) {
            // Duplicate customization
            $sql = 'INSERT INTO '._DB_PREFIX_.'customization
                (`id_product_attribute`, `id_address_delivery`, `id_cart`, `id_product`, `quantity`, `in_cart`)
                VALUES (
                    '.(int)$customization['id_product_attribute'].',
                    '.(int)$new_id_address_delivery.',
                    '.(int)$customization['id_cart'].',
                    '.(int)$customization['id_product'].',
                    '.(int)$quantity.',
                    '.(int)$customization['in_cart'].')';

            Db::getInstance()->execute($sql);

            // Save last insert ID before doing another query
            $last_id = (int)Db::getInstance()->Insert_ID();

            // Get data from duplicated customizations
            $sql = new DbQuery();
            $sql->select('`type`, `index`, `value`');
            $sql->from('customized_data');
            $sql->where('id_customization = '.$customization['id_customization']);
            $last_row = Db::getInstance()->getRow($sql);

            // Insert new copied data with new customization ID into customized_data table
            $last_row['id_customization'] = $last_id;
            Db::getInstance()->insert('customized_data', $last_row);
        }

        $customization_count = count($results);
        if ($customization_count > 0) {
            $sql = 'UPDATE '._DB_PREFIX_.'cart_product
                SET `quantity` = `quantity` + '.(int)$customization_count * $quantity.'
                WHERE id_cart = '.(int)$this->id.'
                AND id_product = '.(int)$id_product.'
                AND id_shop = '.(int)$this->id_shop.'
                AND id_product_attribute = '.(int)$id_product_attribute.'
                AND id_address_delivery = '.(int)$new_id_address_delivery;
            Db::getInstance()->execute($sql);
        }

        return true;
    }

    /**
     * Update products cart address delivery with the address delivery of the cart
     */
    public function setNoMultishipping()
    {
        $emptyCache = false;
        if (Configuration::get('PS_ALLOW_MULTISHIPPING')) {
            // Upgrading quantities
            $sql = 'SELECT sum(`quantity`) as quantity, id_product, id_product_attribute, count(*) as count
                    FROM `'._DB_PREFIX_.'cart_product`
                    WHERE `id_cart` = '.(int)$this->id.'
                        AND `id_shop` = '.(int)$this->id_shop.'
                    GROUP BY id_product, id_product_attribute
                    HAVING count > 1';

            foreach (Db::getInstance()->executeS($sql) as $product) {
                $sql = 'UPDATE `'._DB_PREFIX_.'cart_product`
                    SET `quantity` = '.$product['quantity'].'
                    WHERE  `id_cart` = '.(int)$this->id.'
                        AND `id_shop` = '.(int)$this->id_shop.'
                        AND id_product = '.$product['id_product'].'
                        AND id_product_attribute = '.$product['id_product_attribute'];
                if (Db::getInstance()->execute($sql)) {
                    $emptyCache = true;
                }
            }

            // Merging multiple lines
            $sql = 'DELETE cp1
                FROM `'._DB_PREFIX_.'cart_product` cp1
                    INNER JOIN `'._DB_PREFIX_.'cart_product` cp2
                    ON (
                        (cp1.id_cart = cp2.id_cart)
                        AND (cp1.id_product = cp2.id_product)
                        AND (cp1.id_product_attribute = cp2.id_product_attribute)
                        AND (cp1.id_address_delivery <> cp2.id_address_delivery)
                        AND (cp1.date_add > cp2.date_add)
                    )';
            Db::getInstance()->execute($sql);
        }

        // Update delivery address for each product line
        $sql = 'UPDATE `'._DB_PREFIX_.'cart_product`
        SET `id_address_delivery` = (
            SELECT `id_address_delivery` FROM `'._DB_PREFIX_.'cart`
            WHERE `id_cart` = '.(int)$this->id.' AND `id_shop` = '.(int)$this->id_shop.'
        )
        WHERE `id_cart` = '.(int)$this->id.'
        '.(Configuration::get('PS_ALLOW_MULTISHIPPING') ? ' AND `id_shop` = '.(int)$this->id_shop : '');

        $cache_id = 'Cart::setNoMultishipping'.(int)$this->id.'-'.(int)$this->id_shop.((isset($this->id_address_delivery) && $this->id_address_delivery) ? '-'.(int)$this->id_address_delivery : '');
        if (!Cache::isStored($cache_id)) {
            if ($result = (bool)Db::getInstance()->execute($sql)) {
                $emptyCache = true;
            }
            Cache::store($cache_id, $result);
        }

        if (Customization::isFeatureActive()) {
            Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'customization`
            SET `id_address_delivery` = (
                SELECT `id_address_delivery` FROM `'._DB_PREFIX_.'cart`
                WHERE `id_cart` = '.(int)$this->id.'
            )
            WHERE `id_cart` = '.(int)$this->id);
        }

        if ($emptyCache) {
            $this->_products = null;
        }
    }

    /**
     * Set an address to all products on the cart without address delivery
     */
    public function autosetProductAddress()
    {
        $id_address_delivery = 0;
        // Get the main address of the customer
        if ((int)$this->id_address_delivery > 0) {
            $id_address_delivery = (int)$this->id_address_delivery;
        } else {
            $id_address_delivery = (int)Address::getFirstCustomerAddressId(Context::getContext()->customer->id);
        }

        if (!$id_address_delivery) {
            return;
        }

        // Update
        $sql = 'UPDATE `'._DB_PREFIX_.'cart_product`
            SET `id_address_delivery` = '.(int)$id_address_delivery.'
            WHERE `id_cart` = '.(int)$this->id.'
                AND (`id_address_delivery` = 0 OR `id_address_delivery` IS NULL)
                AND `id_shop` = '.(int)$this->id_shop;
        Db::getInstance()->execute($sql);

        $sql = 'UPDATE `'._DB_PREFIX_.'customization`
            SET `id_address_delivery` = '.(int)$id_address_delivery.'
            WHERE `id_cart` = '.(int)$this->id.'
                AND (`id_address_delivery` = 0 OR `id_address_delivery` IS NULL)';

        Db::getInstance()->execute($sql);
    }

    public function deleteAssociations()
    {
        return (Db::getInstance()->execute('
                DELETE FROM `'._DB_PREFIX_.'cart_product`
                WHERE `id_cart` = '.(int)$this->id) !== false);
    }

    /**
     * isCarrierInRange
     *
     * Check if the specified carrier is in range
     *
     * @id_carrier int
     * @id_zone int
     */
    public function isCarrierInRange($id_carrier, $id_zone)
    {
        $carrier = new Carrier((int)$id_carrier, Configuration::get('PS_LANG_DEFAULT'));
        $shipping_method = $carrier->getShippingMethod();
        if (!$carrier->range_behavior) {
            return true;
        }

        if ($shipping_method == Carrier::SHIPPING_METHOD_FREE) {
            return true;
        }

        $check_delivery_price_by_weight = Carrier::checkDeliveryPriceByWeight(
            (int)$id_carrier,
            $this->getTotalWeight(),
            $id_zone
        );
        if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && $check_delivery_price_by_weight) {
            return true;
        }

        $check_delivery_price_by_price = Carrier::checkDeliveryPriceByPrice(
            (int)$id_carrier,
            $this->getOrderTotal(
                true,
                Cart::BOTH_WITHOUT_SHIPPING
            ),
            $id_zone,
            (int)$this->id_currency
        );
        if ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && $check_delivery_price_by_price) {
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
        if (!(int)$id_cart) {
            return false;
        }
        return (bool)Db::getInstance()->getValue('
            SELECT `is_guest`
            FROM `'._DB_PREFIX_.'customer` cu
            LEFT JOIN `'._DB_PREFIX_.'cart` ca ON (ca.`id_customer` = cu.`id_customer`)
            WHERE ca.`id_cart` = '.(int)$id_cart);
    }

    /**
     * Are all products of the Cart in stock?
     *
     * @param bool $ignore_virtual Ignore virtual products
     * @param bool $exclusive (DEPRECATED) If true, the validation is exclusive : it must be present product in stock and out of stock
     * @since 1.5.0
     *
     * @return bool False if not all products in the cart are in stock
     */
    public function isAllProductsInStock($ignoreVirtual = false, $exclusive = false)
    {
        if (func_num_args() > 1) {
            @trigger_error(
                '$exclusive parameter is deprecated since version 1.7.3.2 and will be removed in the next major version.',
                E_USER_DEPRECATED
            );
        }
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
                $product['id_customization']
            );

            if (!$exclusive
                && ($productQuantity < 0 && !$availableOutOfStock)
            ) {
                return false;
            } else if ($exclusive) {
                if ($productQuantity <= 0) {
                    $productOutOfStock++;
                } else {
                    $productInStock++;
                }

                if ($productInStock > 0 && $productOutOfStock > 0) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Execute hook displayCarrierList (extraCarrier) and merge them into the $array
     *
     * @param array $array
     */
    public static function addExtraCarriers(&$array)
    {
        $first = true;
        $hook_extracarrier_addr = array();
        foreach (Context::getContext()->cart->getAddressCollection() as $address) {
            $hook = Hook::exec('displayCarrierList', array('address' => $address));
            $hook_extracarrier_addr[$address->id] = $hook;

            if ($first) {
                $array = array_merge(
                    $array,
                    array('HOOK_EXTRACARRIER' => $hook)
                );
                $first = false;
            }
            $array = array_merge(
                $array,
                array('HOOK_EXTRACARRIER_ADDR' => $hook_extracarrier_addr)
            );
        }
    }

    /**
     * Get all the IDs of the delivery Addresses without Carriers
     *
     * @param bool $return_collection Returns sa collection
     * @param array &$error Contains an error message if an error occurs
     *
     * @return array Array of address id or of address object
     */
    public function getDeliveryAddressesWithoutCarriers($return_collection = false, &$error = array())
    {
        $addresses_without_carriers = array();
        foreach ($this->getProducts(false, false, null, false) as $product) {
            if (!in_array($product['id_address_delivery'], $addresses_without_carriers)
                && !count(Carrier::getAvailableCarrierList(new Product($product['id_product']), null, $product['id_address_delivery'], null, null, $error))) {
                $addresses_without_carriers[] = $product['id_address_delivery'];
            }
        }
        if (!$return_collection) {
            return $addresses_without_carriers;
        } else {
            $addresses_instance_without_carriers = array();
            foreach ($addresses_without_carriers as $id_address) {
                $addresses_instance_without_carriers[] = new Address($id_address);
            }
            return $addresses_instance_without_carriers;
        }
    }

    /**
     * Set flag to split lines of products given away and also manually added to cart
     */
    protected function splitGiftsProductsQuantity()
    {
        $this->shouldSplitGiftProductsQuantity = true;

        return $this;
    }

    /**
     * Set flag to merge lines of products given away and also manually added to cart
     */
    protected function mergeGiftsProductsQuantity()
    {
        $this->shouldSplitGiftProductsQuantity = false;

        return $this;
    }

    protected function excludeGiftsDiscountFromTotal()
    {
        $this->shouldExcludeGiftsDiscount = true;

        return $this;
    }

    protected function includeGiftsDiscountInTotal()
    {
        $this->shouldExcludeGiftsDiscount = false;

        return $this;
    }

    /**
     * Get products with gifts and manually added occurrences separated
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
}
