<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
use PrestaShop\PrestaShop\Adapter\ServiceLocator;

class OrderCore extends ObjectModel
{
    const ROUND_ITEM = 1;
    const ROUND_LINE = 2;
    const ROUND_TOTAL = 3;

    /** @var int Delivery address id */
    public $id_address_delivery;

    /** @var int Invoice address id */
    public $id_address_invoice;

    public $id_shop_group;

    public $id_shop;

    /** @var int Cart id */
    public $id_cart;

    /** @var int Currency id */
    public $id_currency;

    /** @var int Language id */
    public $id_lang;

    /** @var int Customer id */
    public $id_customer;

    // todo: string received instead of int
    /** @var int Carrier id */
    public $id_carrier;

    /** @var int Order Status id */
    public $current_state;

    /** @var string Secure key */
    public $secure_key;

    /** @var string Payment method */
    public $payment;

    /** @var string Payment module */
    public $module;

    /** @var float Currency exchange rate */
    public $conversion_rate;

    /** @var bool Customer is ok for a recyclable package */
    public $recyclable = 1;

    /** @var bool True if the customer wants a gift wrapping */
    public $gift = 0;

    /** @var string Gift message if specified */
    public $gift_message;

    /** @var bool Mobile Theme */
    public $mobile_theme;

    /**
     * @var string Shipping number
     *
     * @deprecated 1.5.0.4
     * @see OrderCarrier->tracking_number
     */
    public $shipping_number;

    /** @var float Discounts total */
    public $total_discounts;

    public $total_discounts_tax_incl;
    public $total_discounts_tax_excl;

    /** @var float Total to pay */
    public $total_paid;

    /** @var float Total to pay tax included */
    public $total_paid_tax_incl;

    /** @var float Total to pay tax excluded */
    public $total_paid_tax_excl;

    /** @var float Total really paid @deprecated 1.5.0.1 */
    public $total_paid_real;

    /** @var float Products total */
    public $total_products;

    /** @var float Products total tax included */
    public $total_products_wt;

    /** @var float Shipping total */
    public $total_shipping;

    /** @var float Shipping total tax included */
    public $total_shipping_tax_incl;

    /** @var float Shipping total tax excluded */
    public $total_shipping_tax_excl;

    /** @var float Shipping tax rate */
    public $carrier_tax_rate;

    /** @var float Wrapping total */
    public $total_wrapping;

    /** @var float Wrapping total tax included */
    public $total_wrapping_tax_incl;

    /** @var float Wrapping total tax excluded */
    public $total_wrapping_tax_excl;

    /** @var int Invoice number */
    public $invoice_number;

    /** @var int Delivery number */
    public $delivery_number;

    /** @var string Invoice creation date */
    public $invoice_date;

    /** @var string Delivery creation date */
    public $delivery_date;

    /** @var bool Order validity: current order status is logable (usually paid and not canceled) */
    public $valid;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /**
     * @var string Order reference, this reference is not unique, but unique for a payment
     */
    public $reference;

    /**
     * @var int Round mode method used for this order
     */
    public $round_mode;

    /**
     * @var int Round type method used for this order
     */
    public $round_type;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'orders',
        'primary' => 'id_order',
        'fields' => [
            'id_address_delivery' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_address_invoice' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_cart' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_currency' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_shop_group' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_lang' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_customer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_carrier' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'current_state' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'secure_key' => ['type' => self::TYPE_STRING, 'validate' => 'isMd5'],
            'payment' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true],
            'module' => ['type' => self::TYPE_STRING, 'validate' => 'isModuleName', 'required' => true],
            'recyclable' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'gift' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'gift_message' => ['type' => self::TYPE_STRING, 'validate' => 'isMessage'],
            'mobile_theme' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'total_discounts' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
            'total_discounts_tax_incl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
            'total_discounts_tax_excl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
            'total_paid' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true],
            'total_paid_tax_incl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
            'total_paid_tax_excl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
            'total_paid_real' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true],
            'total_products' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true],
            'total_products_wt' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true],
            'total_shipping' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
            'total_shipping_tax_incl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
            'total_shipping_tax_excl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
            'carrier_tax_rate' => ['type' => self::TYPE_FLOAT, 'validate' => 'isFloat'],
            'total_wrapping' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
            'total_wrapping_tax_incl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
            'total_wrapping_tax_excl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
            'round_mode' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'round_type' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'shipping_number' => ['type' => self::TYPE_STRING, 'validate' => 'isTrackingNumber'],
            'conversion_rate' => ['type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true],
            'invoice_number' => ['type' => self::TYPE_INT],
            'delivery_number' => ['type' => self::TYPE_INT],
            'invoice_date' => ['type' => self::TYPE_DATE],
            'delivery_date' => ['type' => self::TYPE_DATE],
            'valid' => ['type' => self::TYPE_BOOL],
            'reference' => ['type' => self::TYPE_STRING],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    protected $webserviceParameters = [
        'objectMethods' => ['add' => 'addWs'],
        'objectNodeName' => 'order',
        'objectsNodeName' => 'orders',
        'fields' => [
            'id_address_delivery' => ['xlink_resource' => 'addresses'],
            'id_address_invoice' => ['xlink_resource' => 'addresses'],
            'id_cart' => ['xlink_resource' => 'carts'],
            'id_currency' => ['xlink_resource' => 'currencies'],
            'id_lang' => ['xlink_resource' => 'languages'],
            'id_customer' => ['xlink_resource' => 'customers'],
            'id_carrier' => ['xlink_resource' => 'carriers'],
            'current_state' => [
                'xlink_resource' => 'order_states',
                'setter' => 'setWsCurrentState',
            ],
            'module' => ['required' => true],
            'invoice_number' => [],
            'invoice_date' => [],
            'delivery_number' => [],
            'delivery_date' => [],
            'valid' => [],
            'date_add' => [],
            'date_upd' => [],
            'shipping_number' => [
                'getter' => 'getWsShippingNumber',
                'setter' => 'setWsShippingNumber',
            ],
        ],
        'associations' => [
            'order_rows' => ['resource' => 'order_row', 'setter' => false, 'virtual_entity' => true,
                'fields' => [
                    'id' => [],
                    'product_id' => ['required' => true, 'xlink_resource' => 'products'],
                    'product_attribute_id' => ['required' => true],
                    'product_quantity' => ['required' => true],
                    'product_name' => ['setter' => false],
                    'product_reference' => ['setter' => false],
                    'product_ean13' => ['setter' => false],
                    'product_isbn' => ['setter' => false],
                    'product_upc' => ['setter' => false],
                    'product_price' => ['setter' => false],
                    'id_customization' => ['required' => false, 'xlink_resource' => 'customizations'],
                    'unit_price_tax_incl' => ['setter' => false],
                    'unit_price_tax_excl' => ['setter' => false],
                ], ],
        ],
    ];

    protected $_taxCalculationMethod = PS_TAX_EXC;

    protected static $_historyCache = [];

    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);

        $is_admin = (is_object(Context::getContext()->controller) && Context::getContext()->controller->controller_type == 'admin');
        if ($this->id_customer && !$is_admin) {
            $customer = new Customer((int) $this->id_customer);
            $this->_taxCalculationMethod = Group::getPriceDisplayMethod((int) $customer->id_default_group);
        } else {
            $this->_taxCalculationMethod = Group::getDefaultPriceDisplayMethod();
        }
    }

    /**
     * @see ObjectModel::getFields()
     *
     * @return array
     */
    public function getFields()
    {
        if (!$this->id_lang) {
            $this->id_lang = Configuration::get('PS_LANG_DEFAULT', null, null, $this->id_shop);
        }

        return parent::getFields();
    }

    public function add($autodate = true, $null_values = true)
    {
        if (parent::add($autodate, $null_values)) {
            return SpecificPrice::deleteByIdCart($this->id_cart);
        }

        return false;
    }

    public function getTaxCalculationMethod()
    {
        return (int) $this->_taxCalculationMethod;
    }

    /**
     * Does NOT delete a product but "cancel" it (which means return/refund/delete it depending of the case).
     *
     * @param $order
     * @param OrderDetail $order_detail
     * @param int $quantity
     *
     * @return bool
     *
     * @throws PrestaShopException
     */
    public function deleteProduct($order, $order_detail, $quantity)
    {
        if (!(int) $this->getCurrentState() || !validate::isLoadedObject($order_detail)) {
            return false;
        }

        if ($this->hasBeenDelivered()) {
            if (!Configuration::get('PS_ORDER_RETURN', null, null, $this->id_shop)) {
                throw new PrestaShopException('PS_ORDER_RETURN is not defined in table configuration');
            }
            $order_detail->product_quantity_return += (int) $quantity;

            return $order_detail->update();
        } elseif ($this->hasBeenPaid()) {
            $order_detail->product_quantity_refunded += (int) $quantity;

            return $order_detail->update();
        }

        return $this->_deleteProduct($order_detail, (int) $quantity);
    }

    /**
     * This function return products of the orders
     * It's similar to Order::getProducts but with similar outputs of Cart::getProducts.
     *
     * @return array
     */
    public function getCartProducts()
    {
        $product_id_list = [];
        $products = $this->getProducts();
        foreach ($products as &$product) {
            $product['id_product_attribute'] = $product['product_attribute_id'];
            $product['cart_quantity'] = $product['product_quantity'];
            $product_id_list[] = $this->id_address_delivery . '_'
                . $product['product_id'] . '_'
                . $product['product_attribute_id'] . '_'
                . (isset($product['id_customization']) ? $product['id_customization'] : '0');
        }
        unset($product);

        $product_list = [];
        foreach ($products as $product) {
            $key = $this->id_address_delivery . '_'
                . $product['id_product'] . '_'
                . (isset($product['id_product_attribute']) ? $product['id_product_attribute'] : '0') . '_'
                . (isset($product['id_customization']) ? $product['id_customization'] : '0');

            if (in_array($key, $product_id_list)) {
                $product_list[] = $product;
            }
        }

        return $product_list;
    }

    /**
     * DOES delete the product.
     *
     * @param OrderDetail $order_detail
     * @param int $quantity
     *
     * @return bool
     *
     * @throws PrestaShopException
     */
    protected function _deleteProduct($order_detail, $quantity)
    {
        $product_price_tax_excl = $order_detail->unit_price_tax_excl * $quantity;
        $product_price_tax_incl = $order_detail->unit_price_tax_incl * $quantity;

        /* Update cart */
        $cart = new Cart($this->id_cart);
        $cart->updateQty($quantity, $order_detail->product_id, $order_detail->product_attribute_id, false, 'down'); // customization are deleted in deleteCustomization
        $cart->update();

        /* Update order */
        $shipping_diff_tax_incl = $this->total_shipping_tax_incl - $cart->getPackageShippingCost($this->id_carrier, true, null, $this->getCartProducts());
        $shipping_diff_tax_excl = $this->total_shipping_tax_excl - $cart->getPackageShippingCost($this->id_carrier, false, null, $this->getCartProducts());
        $this->total_shipping -= $shipping_diff_tax_incl;
        $this->total_shipping_tax_excl -= $shipping_diff_tax_excl;
        $this->total_shipping_tax_incl -= $shipping_diff_tax_incl;
        $this->total_products -= $product_price_tax_excl;
        $this->total_products_wt -= $product_price_tax_incl;
        $this->total_paid -= $product_price_tax_incl + $shipping_diff_tax_incl;
        $this->total_paid_tax_incl -= $product_price_tax_incl + $shipping_diff_tax_incl;
        $this->total_paid_tax_excl -= $product_price_tax_excl + $shipping_diff_tax_excl;
        $this->total_paid_real -= $product_price_tax_incl + $shipping_diff_tax_incl;

        $fields = [
            'total_shipping',
            'total_shipping_tax_excl',
            'total_shipping_tax_incl',
            'total_products',
            'total_products_wt',
            'total_paid',
            'total_paid_tax_incl',
            'total_paid_tax_excl',
            'total_paid_real',
        ];

        /* Prevent from floating precision issues */
        foreach ($fields as $field) {
            if ($this->{$field} < 0) {
                $this->{$field} = 0;
            }
        }

        /* Prevent from floating precision issues */
        foreach ($fields as $field) {
            $this->{$field} = number_format($this->{$field}, Context::getContext()->getComputingPrecision(), '.', '');
        }

        /* Update order detail */
        $order_detail->product_quantity -= (int) $quantity;
        if ($order_detail->product_quantity == 0) {
            if (!$order_detail->delete()) {
                return false;
            }
            if (count($this->getProductsDetail()) == 0) {
                $history = new OrderHistory();
                $history->id_order = (int) $this->id;
                $history->changeIdOrderState(Configuration::get('PS_OS_CANCELED'), $this);
                if (!$history->addWithemail()) {
                    return false;
                }
            }

            return $this->update();
        } else {
            $order_detail->total_price_tax_incl -= $product_price_tax_incl;
            $order_detail->total_price_tax_excl -= $product_price_tax_excl;
            $order_detail->total_shipping_price_tax_incl -= $shipping_diff_tax_incl;
            $order_detail->total_shipping_price_tax_excl -= $shipping_diff_tax_excl;
        }

        return $order_detail->update() && $this->update();
    }

    public function deleteCustomization($id_customization, $quantity, $order_detail)
    {
        if (!(int) $this->getCurrentState()) {
            return false;
        }

        if ($this->hasBeenDelivered()) {
            return Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customization` SET `quantity_returned` = `quantity_returned` + ' . (int) $quantity . ' WHERE `id_customization` = ' . (int) $id_customization . ' AND `id_cart` = ' . (int) $this->id_cart . ' AND `id_product` = ' . (int) $order_detail->product_id);
        } elseif ($this->hasBeenPaid()) {
            return Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customization` SET `quantity_refunded` = `quantity_refunded` + ' . (int) $quantity . ' WHERE `id_customization` = ' . (int) $id_customization . ' AND `id_cart` = ' . (int) $this->id_cart . ' AND `id_product` = ' . (int) $order_detail->product_id);
        }
        if (!Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customization` SET `quantity` = `quantity` - ' . (int) $quantity . ' WHERE `id_customization` = ' . (int) $id_customization . ' AND `id_cart` = ' . (int) $this->id_cart . ' AND `id_product` = ' . (int) $order_detail->product_id)) {
            return false;
        }
        if (!Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'customization` WHERE `quantity` = 0')) {
            return false;
        }

        return $this->_deleteProduct($order_detail, (int) $quantity);
    }

    /**
     * Get order history.
     *
     * @param int $id_lang Language id
     * @param int $id_order_state Filter a specific order status
     * @param int $no_hidden Filter no hidden status
     * @param int $filters Flag to use specific field filter
     *
     * @return array History entries ordered by date DESC
     */
    public function getHistory($id_lang, $id_order_state = false, $no_hidden = false, $filters = 0)
    {
        if (!$id_order_state) {
            $id_order_state = 0;
        }

        $logable = false;
        $delivery = false;
        $paid = false;
        $shipped = false;
        if ($filters > 0) {
            if ($filters & OrderState::FLAG_NO_HIDDEN) {
                $no_hidden = true;
            }
            if ($filters & OrderState::FLAG_DELIVERY) {
                $delivery = true;
            }
            if ($filters & OrderState::FLAG_LOGABLE) {
                $logable = true;
            }
            if ($filters & OrderState::FLAG_PAID) {
                $paid = true;
            }
            if ($filters & OrderState::FLAG_SHIPPED) {
                $shipped = true;
            }
        }

        if (!isset(self::$_historyCache[$this->id . '_' . $id_order_state . '_' . $filters]) || $no_hidden) {
            $id_lang = $id_lang ? (int) $id_lang : 'o.`id_lang`';
            $result = Db::getInstance()->executeS('
            SELECT os.*, oh.*, e.`firstname` as employee_firstname, e.`lastname` as employee_lastname, osl.`name` as ostate_name
            FROM `' . _DB_PREFIX_ . 'orders` o
            LEFT JOIN `' . _DB_PREFIX_ . 'order_history` oh ON o.`id_order` = oh.`id_order`
            LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON os.`id_order_state` = oh.`id_order_state`
            LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = ' . (int) ($id_lang) . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'employee` e ON e.`id_employee` = oh.`id_employee`
            WHERE oh.id_order = ' . (int) $this->id . '
            ' . ($no_hidden ? ' AND os.hidden = 0' : '') . '
            ' . ($logable ? ' AND os.logable = 1' : '') . '
            ' . ($delivery ? ' AND os.delivery = 1' : '') . '
            ' . ($paid ? ' AND os.paid = 1' : '') . '
            ' . ($shipped ? ' AND os.shipped = 1' : '') . '
            ' . ((int) $id_order_state ? ' AND oh.`id_order_state` = ' . (int) $id_order_state : '') . '
            ORDER BY oh.date_add DESC, oh.id_order_history DESC');
            if ($no_hidden) {
                return $result;
            }
            self::$_historyCache[$this->id . '_' . $id_order_state . '_' . $filters] = $result;
        }

        return self::$_historyCache[$this->id . '_' . $id_order_state . '_' . $filters];
    }

    /**
     * Clean static history cache, must be called when an OrderHistory is added as it changes
     * the order history and may change its value for isPaid/isDelivered/... This way calls to
     * getHistory will be up to date.
     */
    public static function cleanHistoryCache()
    {
        self::$_historyCache = [];
    }

    public function getProductsDetail()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT *
        FROM `' . _DB_PREFIX_ . 'order_detail` od
        LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (p.id_product = od.product_id)
        LEFT JOIN `' . _DB_PREFIX_ . 'product_shop` ps ON (ps.id_product = p.id_product AND ps.id_shop = od.id_shop)
        WHERE od.`id_order` = ' . (int) $this->id);
    }

    public function getFirstMessage()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT `message`
            FROM `' . _DB_PREFIX_ . 'message`
            WHERE `id_order` = ' . (int) $this->id . '
            ORDER BY `id_message`
        ');
    }

    /**
     * Marked as deprecated but should not throw any "deprecated" message
     * This function is used in order to keep front office backward compatibility 14 -> 1.5
     * (Order History).
     *
     * @deprecated
     */
    public function setProductPrices(&$row)
    {
        $tax_calculator = OrderDetail::getTaxCalculatorStatic((int) $row['id_order_detail']);
        $row['tax_calculator'] = $tax_calculator;
        $row['tax_rate'] = $tax_calculator->getTotalRate();

        $row['product_price'] = Tools::ps_round($row['unit_price_tax_excl'], 2);
        $row['product_price_wt'] = Tools::ps_round($row['unit_price_tax_incl'], 2);

        $group_reduction = 1;
        if ($row['group_reduction'] > 0) {
            $group_reduction = 1 - $row['group_reduction'] / 100;
        }

        $row['product_price_wt_but_ecotax'] = $row['product_price_wt'] - $row['ecotax'];

        $row['total_wt'] = $row['total_price_tax_incl'];
        $row['total_price'] = $row['total_price_tax_excl'];
    }

    /**
     * Get order products.
     *
     * @param bool $products
     * @param bool $selected_products
     * @param bool $selected_qty
     * @param bool $fullInfos
     *
     * @return array Products with price, quantity (with taxe and without)
     */
    public function getProducts($products = false, $selected_products = false, $selected_qty = false, $fullInfos = true)
    {
        if (!$products) {
            $products = $this->getProductsDetail();
        }

        if (!$fullInfos) {
            return $products;
        }

        $result_array = [];
        foreach ($products as $row) {
            // Change qty if selected
            if ($selected_qty) {
                $row['product_quantity'] = 0;
                foreach ($selected_products as $key => $id_product) {
                    if ($row['id_order_detail'] == $id_product) {
                        $row['product_quantity'] = (int) $selected_qty[$key];
                    }
                }
                if (!$row['product_quantity']) {
                    continue;
                }
            }

            $this->setProductImageInformations($row);
            $this->setProductCurrentStock($row);

            // Backward compatibility 1.4 -> 1.5
            $this->setProductPrices($row);

            $customized_datas = Product::getAllCustomizedDatas($this->id_cart, null, true, null, (int) $row['id_customization']);

            $this->setProductCustomizedDatas($row, $customized_datas);

            // Add information for virtual product
            if ($row['download_hash'] && !empty($row['download_hash'])) {
                $row['filename'] = ProductDownload::getFilenameFromIdProduct((int) $row['product_id']);
                // Get the display filename
                $row['display_filename'] = ProductDownload::getFilenameFromFilename($row['filename']);
            }

            $row['id_address_delivery'] = $this->id_address_delivery;

            if ($customized_datas) {
                Product::addProductCustomizationPrice($row, $customized_datas);
            }

            /* Stock product */
            $result_array[(int) $row['id_order_detail']] = $row;
        }

        return $result_array;
    }

    public static function getIdOrderProduct($id_customer, $id_product)
    {
        return (int) Db::getInstance()->getValue('
            SELECT o.id_order
            FROM ' . _DB_PREFIX_ . 'orders o
            LEFT JOIN ' . _DB_PREFIX_ . 'order_detail od
                ON o.id_order = od.id_order
            WHERE o.id_customer = ' . (int) $id_customer . '
                AND od.product_id = ' . (int) $id_product . '
            ORDER BY o.date_add DESC
        ');
    }

    protected function setProductCustomizedDatas(&$product, $customized_datas)
    {
        $product['customizedDatas'] = null;
        if (isset($customized_datas[$product['product_id']][$product['product_attribute_id']])) {
            $product['customizedDatas'] = $customized_datas[$product['product_id']][$product['product_attribute_id']];
        } else {
            $product['customizationQuantityTotal'] = 0;
        }
    }

    /**
     * This method allow to add stock information on a product detail.
     *
     * If advanced stock management is active, get physical stock of this product in the warehouse associated to the ptoduct for the current order
     * Else get the available quantity of the product in fucntion of the shop associated to the order
     *
     * @param array &$product
     */
    protected function setProductCurrentStock(&$product)
    {
        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')
            && (int) $product['advanced_stock_management'] == 1
            && (int) $product['id_warehouse'] > 0) {
            $product['current_stock'] = StockManagerFactory::getManager()->getProductPhysicalQuantities($product['product_id'], $product['product_attribute_id'], (int) $product['id_warehouse'], true);
        } else {
            $product['current_stock'] = StockAvailable::getQuantityAvailableByProduct($product['product_id'], $product['product_attribute_id'], (int) $this->id_shop);
        }

        $product['location'] = StockAvailable::getLocation($product['product_id'], $product['product_attribute_id'], (int) $this->id_shop);
    }

    /**
     * This method allow to add image information on a product detail.
     *
     * @param array &$product
     */
    protected function setProductImageInformations(&$product)
    {
        if (isset($product['product_attribute_id']) && $product['product_attribute_id']) {
            $id_image = Db::getInstance()->getValue('
                SELECT `image_shop`.id_image
                FROM `' . _DB_PREFIX_ . 'product_attribute_image` pai' .
                Shop::addSqlAssociation('image', 'pai', true) . '
                LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_image` = pai.`id_image`)
                WHERE id_product_attribute = ' . (int) $product['product_attribute_id'] . ' ORDER by i.position ASC');
        }

        if (!isset($id_image) || !$id_image) {
            $id_image = Db::getInstance()->getValue(
                'SELECT `image_shop`.id_image
                FROM `' . _DB_PREFIX_ . 'image` i' .
                Shop::addSqlAssociation('image', 'i', true, 'image_shop.cover=1') . '
                WHERE i.id_product = ' . (int) $product['product_id']
            );
        }

        $product['image'] = null;
        $product['image_size'] = null;

        if ($id_image) {
            $product['image'] = new Image($id_image);
        }
    }

    public function getTaxesAverageUsed()
    {
        return Cart::getTaxesAverageUsed((int) $this->id_cart);
    }

    /**
     * Count virtual products in order.
     *
     * @return int number of virtual products
     */
    public function getVirtualProducts()
    {
        $sql = '
            SELECT `product_id`, `product_attribute_id`, `download_hash`, `download_deadline`
            FROM `' . _DB_PREFIX_ . 'order_detail` od
            WHERE od.`id_order` = ' . (int) $this->id . '
                AND `download_hash` <> \'\'';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Check if order contains (only) virtual products.
     *
     * @param bool $strict If false return true if there are at least one product virtual
     *
     * @return bool true if is a virtual order or false
     */
    public function isVirtual($strict = true)
    {
        $products = $this->getProducts(false, false, false, false);
        if (count($products) < 1) {
            return false;
        }

        $virtual = true;

        foreach ($products as $product) {
            if ($strict === false && (bool) $product['is_virtual']) {
                return true;
            }

            $virtual &= (bool) $product['is_virtual'];
        }

        return $virtual;
    }

    /**
     * @deprecated 1.5.0.1 use Order::getCartRules() instead
     */
    public function getDiscounts($details = false)
    {
        Tools::displayAsDeprecated('Use Order::getCartRules() instead');

        return Order::getCartRules();
    }

    public function getCartRules()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT *
        FROM `' . _DB_PREFIX_ . 'order_cart_rule` ocr
        WHERE ocr.`id_order` = ' . (int) $this->id);
    }

    public static function getDiscountsCustomer($id_customer, $id_cart_rule)
    {
        $cache_id = 'Order::getDiscountsCustomer_' . (int) $id_customer . '-' . (int) $id_cart_rule;
        if (!Cache::isStored($cache_id)) {
            $result = (int) Db::getInstance()->getValue('
            SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'orders` o
            LEFT JOIN ' . _DB_PREFIX_ . 'order_cart_rule ocr ON (ocr.id_order = o.id_order)
            WHERE o.id_customer = ' . (int) $id_customer . '
            AND ocr.id_cart_rule = ' . (int) $id_cart_rule);
            Cache::store($cache_id, $result);

            return $result;
        }

        return Cache::retrieve($cache_id);
    }

    /**
     * Get current order status (eg. Awaiting payment, Delivered...).
     *
     * @return int Order status id
     */
    public function getCurrentState()
    {
        return $this->current_state;
    }

    /**
     * Get current order status name (eg. Awaiting payment, Delivered...).
     *
     * @return array Order status details
     */
    public function getCurrentStateFull($id_lang)
    {
        return Db::getInstance()->getRow('
            SELECT os.`id_order_state`, osl.`name`, os.`logable`, os.`shipped`
            FROM `' . _DB_PREFIX_ . 'order_state` os
            LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (osl.`id_order_state` = os.`id_order_state`)
            WHERE osl.`id_lang` = ' . (int) $id_lang . ' AND os.`id_order_state` = ' . (int) $this->current_state);
    }

    public function hasBeenDelivered()
    {
        return count($this->getHistory((int) $this->id_lang, false, false, OrderState::FLAG_DELIVERY));
    }

    /**
     * Has products returned by the merchant or by the customer?
     */
    public function hasProductReturned()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT IFNULL(SUM(ord.product_quantity), SUM(product_quantity_return))
            FROM `' . _DB_PREFIX_ . 'orders` o
            INNER JOIN `' . _DB_PREFIX_ . 'order_detail` od
            ON od.id_order = o.id_order
            LEFT JOIN `' . _DB_PREFIX_ . 'order_return_detail` ord
            ON ord.id_order_detail = od.id_order_detail
            WHERE o.id_order = ' . (int) $this->id);
    }

    public function hasBeenPaid()
    {
        return count($this->getHistory((int) $this->id_lang, false, false, OrderState::FLAG_PAID));
    }

    public function hasBeenShipped()
    {
        return count($this->getHistory((int) $this->id_lang, false, false, OrderState::FLAG_SHIPPED));
    }

    public function isInPreparation()
    {
        return count($this->getHistory((int) $this->id_lang, Configuration::get('PS_OS_PREPARATION')));
    }

    /**
     * Checks if the current order status is paid and shipped.
     *
     * @return bool
     */
    public function isPaidAndShipped()
    {
        $order_state = $this->getCurrentOrderState();
        if ($order_state && $order_state->paid && $order_state->shipped) {
            return true;
        }

        return false;
    }

    /**
     * Get customer orders.
     *
     * @param int $id_customer Customer id
     * @param bool $show_hidden_status Display or not hidden order statuses
     *
     * @return array Customer orders
     */
    public static function getCustomerOrders($id_customer, $show_hidden_status = false, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $orderStates = OrderState::getOrderStates((int) $context->language->id);
        $indexedOrderStates = [];
        foreach ($orderStates as $orderState) {
            $indexedOrderStates[$orderState['id_order_state']] = $orderState;
        }
        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT o.*,
          (SELECT SUM(od.`product_quantity`) FROM `' . _DB_PREFIX_ . 'order_detail` od WHERE od.`id_order` = o.`id_order`) nb_products,
          (SELECT oh.`id_order_state` FROM `' . _DB_PREFIX_ . 'order_history` oh
           LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON (os.`id_order_state` = oh.`id_order_state`)
           WHERE oh.`id_order` = o.`id_order` ' .
            (!$show_hidden_status ? ' AND os.`hidden` != 1' : '') .
            ' ORDER BY oh.`date_add` DESC, oh.`id_order_history` DESC LIMIT 1) id_order_state
        FROM `' . _DB_PREFIX_ . 'orders` o
        WHERE o.`id_customer` = ' . (int) $id_customer .
            Shop::addSqlRestriction(Shop::SHARE_ORDER) . '
        GROUP BY o.`id_order`
        ORDER BY o.`date_add` DESC');

        if (!$res) {
            return [];
        }

        foreach ($res as $key => $val) {
            // In case order creation crashed midway some data might be absent
            $orderState = !empty($val['id_order_state']) ? $indexedOrderStates[$val['id_order_state']] : null;
            $res[$key]['order_state'] = $orderState['name'] ?: null;
            $res[$key]['invoice'] = $orderState['invoice'] ?: null;
            $res[$key]['order_state_color'] = $orderState['color'] ?: null;
        }

        return $res;
    }

    public static function getOrdersIdByDate($date_from, $date_to, $id_customer = null, $type = null)
    {
        $sql = 'SELECT `id_order`
                FROM `' . _DB_PREFIX_ . 'orders`
                WHERE DATE_ADD(date_upd, INTERVAL -1 DAY) <= \'' . pSQL($date_to) . '\' AND date_upd >= \'' . pSQL($date_from) . '\'
                    ' . Shop::addSqlRestriction()
                    . ($type ? ' AND `' . bqSQL($type) . '_number` != 0' : '')
                    . ($id_customer ? ' AND id_customer = ' . (int) $id_customer : '');
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $orders = [];
        foreach ($result as $order) {
            $orders[] = (int) $order['id_order'];
        }

        return $orders;
    }

    public static function getOrdersWithInformations($limit = null, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $sql = 'SELECT *, (
                    SELECT osl.`name`
                    FROM `' . _DB_PREFIX_ . 'order_state_lang` osl
                    WHERE osl.`id_order_state` = o.`current_state`
                    AND osl.`id_lang` = ' . (int) $context->language->id . '
                    LIMIT 1
                ) AS `state_name`, o.`date_add` AS `date_add`, o.`date_upd` AS `date_upd`
                FROM `' . _DB_PREFIX_ . 'orders` o
                LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = o.`id_customer`)
                WHERE 1
                    ' . Shop::addSqlRestriction(false, 'o') . '
                ORDER BY o.`date_add` DESC
                ' . ((int) $limit ? 'LIMIT 0, ' . (int) $limit : '');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    /**
     * @deprecated since 1.5.0.2
     *
     * @param $date_from
     * @param $date_to
     * @param $id_customer
     * @param $type
     *
     * @return array
     */
    public static function getOrdersIdInvoiceByDate($date_from, $date_to, $id_customer = null, $type = null)
    {
        Tools::displayAsDeprecated();
        $sql = 'SELECT `id_order`
                FROM `' . _DB_PREFIX_ . 'orders`
                WHERE DATE_ADD(invoice_date, INTERVAL -1 DAY) <= \'' . pSQL($date_to) . '\' AND invoice_date >= \'' . pSQL($date_from) . '\'
                    ' . Shop::addSqlRestriction()
                    . ($type ? ' AND `' . bqSQL($type) . '_number` != 0' : '')
                    . ($id_customer ? ' AND id_customer = ' . (int) $id_customer : '') .
                ' ORDER BY invoice_date ASC';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $orders = [];
        foreach ($result as $order) {
            $orders[] = (int) $order['id_order'];
        }

        return $orders;
    }

    /**
     * @deprecated 1.5.0.3
     *
     * @param $id_order_state
     *
     * @return array
     */
    public static function getOrderIdsByStatus($id_order_state)
    {
        Tools::displayAsDeprecated();
        $sql = 'SELECT id_order
                FROM ' . _DB_PREFIX_ . 'orders o
                WHERE o.`current_state` = ' . (int) $id_order_state . '
                ' . Shop::addSqlRestriction(false, 'o') . '
                ORDER BY invoice_date ASC';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $orders = [];
        foreach ($result as $order) {
            $orders[] = (int) $order['id_order'];
        }

        return $orders;
    }

    /**
     * Get product total without taxes.
     *
     * @return Product total without taxes
     */
    public function getTotalProductsWithoutTaxes($products = false)
    {
        return $this->total_products;
    }

    /**
     * Get product total with taxes.
     *
     * @return Product total with taxes
     */
    public function getTotalProductsWithTaxes($products = false)
    {
        if ($this->total_products_wt != '0.00' && !$products) {
            return $this->total_products_wt;
        }
        /* Retro-compatibility (now set directly on the validateOrder() method) */

        if (!$products) {
            $products = $this->getProductsDetail();
        }

        $return = 0;
        foreach ($products as $row) {
            $return += $row['total_price_tax_incl'];
        }

        if (!$products) {
            $this->total_products_wt = $return;
            $this->update();
        }

        return $return;
    }

    /**
     * used to cache order customer.
     */
    protected $cacheCustomer = null;

    /**
     * Get order customer.
     *
     * @return Customer $customer
     */
    public function getCustomer()
    {
        if (null === $this->cacheCustomer) {
            $this->cacheCustomer = new Customer((int) $this->id_customer);
        }

        return $this->cacheCustomer;
    }

    /**
     * Get customer orders number.
     *
     * @param int $id_customer Customer id
     *
     * @return array Customer orders number
     */
    public static function getCustomerNbOrders($id_customer)
    {
        $sql = 'SELECT COUNT(`id_order`) AS nb
                FROM `' . _DB_PREFIX_ . 'orders`
                WHERE `id_customer` = ' . (int) $id_customer
                    . Shop::addSqlRestriction();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        return isset($result['nb']) ? $result['nb'] : 0;
    }

    /**
     * Get an order id by its cart id.
     *
     * @param int $id_cart Cart id
     *
     * @return int Order id
     *
     * @deprecated since 1.7.1.0 Use getIdByCartId() instead
     */
    public static function getOrderByCartId($id_cart)
    {
        return self::getIdByCartId($id_cart);
    }

    /**
     * Get an order object by its cart id.
     *
     * @param int $id_cart Cart id
     *
     * @return OrderCore
     */
    public static function getByCartId($id_cart)
    {
        $id_order = (int) self::getIdByCartId((int) $id_cart);

        return ($id_order > 0) ? new self($id_order) : null;
    }

    /**
     * Get the order id by its cart id.
     *
     * @param int $id_cart Cart id
     *
     * @return int $id_order
     */
    public static function getIdByCartId($id_cart)
    {
        $sql = 'SELECT `id_order`
            FROM `' . _DB_PREFIX_ . 'orders`
            WHERE `id_cart` = ' . (int) $id_cart .
            Shop::addSqlRestriction();

        $result = Db::getInstance()->getValue($sql);

        return !empty($result) ? (int) $result : false;
    }

    /**
     * @deprecated 1.5.0.1
     * @see Order::addCartRule()
     *
     * @param int $id_cart_rule
     * @param string $name
     * @param float $value
     *
     * @return bool
     */
    public function addDiscount($id_cart_rule, $name, $value)
    {
        Tools::displayAsDeprecated('Use Order::addCartRule($id_cart_rule, $name, array(\'tax_incl\' => $value, \'tax_excl\' => \'0.00\')) instead');

        return Order::addCartRule($id_cart_rule, $name, ['tax_incl' => $value, 'tax_excl' => '0.00']);
    }

    /**
     * @since 1.5.0.1
     *
     * @param int $id_cart_rule
     * @param string $name
     * @param array $values
     * @param int $id_order_invoice
     *
     * @return bool
     */
    public function addCartRule($id_cart_rule, $name, $values, $id_order_invoice = 0, $free_shipping = null)
    {
        $order_cart_rule = new OrderCartRule();
        $order_cart_rule->id_order = $this->id;
        $order_cart_rule->id_cart_rule = $id_cart_rule;
        $order_cart_rule->id_order_invoice = $id_order_invoice;
        $order_cart_rule->name = $name;
        $order_cart_rule->value = $values['tax_incl'];
        $order_cart_rule->value_tax_excl = $values['tax_excl'];
        if ($free_shipping === null) {
            $cart_rule = new CartRule($id_cart_rule);
            $free_shipping = $cart_rule->free_shipping;
        }
        $order_cart_rule->free_shipping = (int) $free_shipping;
        $order_cart_rule->add();
    }

    public function getNumberOfDays()
    {
        $nb_return_days = (int) Configuration::get('PS_ORDER_RETURN_NB_DAYS', null, null, $this->id_shop);
        if (!$nb_return_days) {
            return true;
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
        SELECT TO_DAYS("' . date('Y-m-d') . ' 00:00:00") - TO_DAYS(`delivery_date`)  AS days FROM `' . _DB_PREFIX_ . 'orders`
        WHERE `id_order` = ' . (int) $this->id);
        if ($result['days'] <= $nb_return_days) {
            return true;
        }

        return false;
    }

    /**
     * Can this order be returned by the client?
     *
     * @return bool
     */
    public function isReturnable()
    {
        if (Configuration::get('PS_ORDER_RETURN', null, null, $this->id_shop) && $this->isPaidAndShipped()) {
            return $this->getNumberOfDays();
        }

        return false;
    }

    public static function getLastInvoiceNumber()
    {
        $sql = 'SELECT MAX(`number`) FROM `' . _DB_PREFIX_ . 'order_invoice`';
        if (Configuration::get('PS_INVOICE_RESET')) {
            $sql .= ' WHERE DATE_FORMAT(`date_add`, "%Y") = ' . (int) date('Y');
        }

        return Db::getInstance()->getValue($sql);
    }

    public static function setLastInvoiceNumber($order_invoice_id, $id_shop)
    {
        if (!$order_invoice_id) {
            return false;
        }

        $number = Configuration::get('PS_INVOICE_START_NUMBER', null, null, $id_shop);
        // If invoice start number has been set, you clean the value of this configuration
        if ($number) {
            Configuration::updateValue('PS_INVOICE_START_NUMBER', false, false, null, $id_shop);
        }

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'order_invoice` SET number =';

        if ($number) {
            $sql .= (int) $number;
        } else {
            $getNumberSql = '(SELECT new_number FROM (SELECT (MAX(`number`) + 1) AS new_number
                FROM `' . _DB_PREFIX_ . 'order_invoice`' . (Configuration::get('PS_INVOICE_RESET') ?
                ' WHERE DATE_FORMAT(`date_add`, "%Y") = ' . (int) date('Y') : '') . ') AS result)';
            $getNumberSqlRow = Db::getInstance()->getRow($getNumberSql);
            $newInvoiceNumber = $getNumberSqlRow['new_number'];
            $sql .= $newInvoiceNumber;
        }

        $sql .= ' WHERE `id_order_invoice` = ' . (int) $order_invoice_id;

        return Db::getInstance()->execute($sql);
    }

    public function getInvoiceNumber($order_invoice_id)
    {
        if (!$order_invoice_id) {
            return false;
        }

        return Db::getInstance()->getValue(
            'SELECT `number`
            FROM `' . _DB_PREFIX_ . 'order_invoice`
            WHERE `id_order_invoice` = ' . (int) $order_invoice_id
        );
    }

    /**
     * This method allows to generate first invoice of the current order.
     */
    public function setInvoice($use_existing_payment = false)
    {
        if (!$this->hasInvoice()) {
            if ($id = (int) $this->getOrderInvoiceIdIfHasDelivery()) {
                $order_invoice = new OrderInvoice($id);
            } else {
                $order_invoice = new OrderInvoice();
            }
            $order_invoice->id_order = $this->id;
            if (!$id) {
                $order_invoice->number = 0;
            }

            // Save Order invoice

            $this->setInvoiceDetails($order_invoice);

            if (Configuration::get('PS_INVOICE')) {
                $this->setLastInvoiceNumber($order_invoice->id, $this->id_shop);
            }

            // Update order_carrier
            $id_order_carrier = Db::getInstance()->getValue('
                SELECT `id_order_carrier`
                FROM `' . _DB_PREFIX_ . 'order_carrier`
                WHERE `id_order` = ' . (int) $order_invoice->id_order . '
                AND (`id_order_invoice` IS NULL OR `id_order_invoice` = 0)');

            if ($id_order_carrier) {
                $order_carrier = new OrderCarrier($id_order_carrier);
                $order_carrier->id_order_invoice = (int) $order_invoice->id;
                $order_carrier->update();
            }

            // Update order detail
            Db::getInstance()->execute('
                UPDATE `' . _DB_PREFIX_ . 'order_detail`
                SET `id_order_invoice` = ' . (int) $order_invoice->id . '
                WHERE `id_order` = ' . (int) $order_invoice->id_order);

            // Update order payment
            if ($use_existing_payment) {
                $id_order_payments = Db::getInstance()->executeS('
                    SELECT DISTINCT op.id_order_payment
                    FROM `' . _DB_PREFIX_ . 'order_payment` op
                    INNER JOIN `' . _DB_PREFIX_ . 'orders` o ON (o.reference = op.order_reference)
                    LEFT JOIN `' . _DB_PREFIX_ . 'order_invoice_payment` oip ON (oip.id_order_payment = op.id_order_payment)
                    WHERE (oip.id_order != ' . (int) $order_invoice->id_order . ' OR oip.id_order IS NULL) AND o.id_order = ' . (int) $order_invoice->id_order);

                if (count($id_order_payments)) {
                    foreach ($id_order_payments as $order_payment) {
                        Db::getInstance()->execute('
                            INSERT INTO `' . _DB_PREFIX_ . 'order_invoice_payment`
                            SET
                                `id_order_invoice` = ' . (int) $order_invoice->id . ',
                                `id_order_payment` = ' . (int) $order_payment['id_order_payment'] . ',
                                `id_order` = ' . (int) $order_invoice->id_order);
                    }
                    // Clear cache
                    Cache::clean('order_invoice_paid_*');
                }
            }

            // Update order cart rule
            Db::getInstance()->execute('
                UPDATE `' . _DB_PREFIX_ . 'order_cart_rule`
                SET `id_order_invoice` = ' . (int) $order_invoice->id . '
                WHERE `id_order` = ' . (int) $order_invoice->id_order);

            // Keep it for backward compatibility, to remove on 1.6 version
            $this->invoice_date = $order_invoice->date_add;

            if (Configuration::get('PS_INVOICE')) {
                $this->invoice_number = $this->getInvoiceNumber($order_invoice->id);
                $invoice_number = Hook::exec('actionSetInvoice', [
                    get_class($this) => $this,
                    get_class($order_invoice) => $order_invoice,
                    'use_existing_payment' => (bool) $use_existing_payment,
                ]);

                if (is_numeric($invoice_number)) {
                    $this->invoice_number = (int) $invoice_number;
                } else {
                    $this->invoice_number = $this->getInvoiceNumber($order_invoice->id);
                }
            }

            $this->update();
        }
    }

    /**
     * This method allows to fulfill the object order_invoice with sales figures.
     */
    protected function setInvoiceDetails($order_invoice)
    {
        if (!$order_invoice || !is_object($order_invoice)) {
            return;
        }

        $address = new Address((int) $this->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
        $carrier = new Carrier((int) $this->id_carrier);
        $tax_calculator = (Configuration::get('PS_ATCP_SHIPWRAP')) ? ServiceLocator::get('AverageTaxOfProductsTaxCalculator')->setIdOrder($this->id) : $carrier->getTaxCalculator($address);
        $order_invoice->total_discount_tax_excl = $this->total_discounts_tax_excl;
        $order_invoice->total_discount_tax_incl = $this->total_discounts_tax_incl;
        $order_invoice->total_paid_tax_excl = $this->total_paid_tax_excl;
        $order_invoice->total_paid_tax_incl = $this->total_paid_tax_incl;
        $order_invoice->total_products = $this->total_products;
        $order_invoice->total_products_wt = $this->total_products_wt;
        $order_invoice->total_shipping_tax_excl = $this->total_shipping_tax_excl;
        $order_invoice->total_shipping_tax_incl = $this->total_shipping_tax_incl;
        $order_invoice->shipping_tax_computation_method = $tax_calculator->computation_method;
        $order_invoice->total_wrapping_tax_excl = $this->total_wrapping_tax_excl;
        $order_invoice->total_wrapping_tax_incl = $this->total_wrapping_tax_incl;
        $order_invoice->save();

        if (Configuration::get('PS_ATCP_SHIPWRAP')) {
            $wrapping_tax_calculator = ServiceLocator::get('AverageTaxOfProductsTaxCalculator')->setIdOrder($this->id);
        } else {
            $wrapping_tax_manager = TaxManagerFactory::getManager($address, (int) Configuration::get('PS_GIFT_WRAPPING_TAX_RULES_GROUP'));
            $wrapping_tax_calculator = $wrapping_tax_manager->getTaxCalculator();
        }

        $order_invoice->saveCarrierTaxCalculator(
            $tax_calculator->getTaxesAmount(
                $order_invoice->total_shipping_tax_excl,
                $order_invoice->total_shipping_tax_incl,
                Context::getContext()->getComputingPrecision(),
                $this->round_mode
            )
        );
        $order_invoice->saveWrappingTaxCalculator(
            $wrapping_tax_calculator->getTaxesAmount(
                $order_invoice->total_wrapping_tax_excl,
                $order_invoice->total_wrapping_tax_incl,
                Context::getContext()->getComputingPrecision(),
                $this->round_mode
            )
        );
    }

    /**
     * This method allows to generate first delivery slip of the current order.
     */
    public function setDeliverySlip()
    {
        if (!$this->hasInvoice()) {
            $order_invoice = new OrderInvoice();
            $order_invoice->id_order = $this->id;
            $order_invoice->number = 0;
            $this->setInvoiceDetails($order_invoice);
            $this->delivery_date = $order_invoice->date_add;
            $this->delivery_number = $this->getDeliveryNumber($order_invoice->id);
            $this->update();
        }
    }

    public function setDeliveryNumber($order_invoice_id, $id_shop)
    {
        if (!$order_invoice_id) {
            return false;
        }

        $id_shop = shop::getTotalShops() > 1 ? $id_shop : null;

        $number = Configuration::get('PS_DELIVERY_NUMBER', null, null, $id_shop);
        // If delivery slip start number has been set, you clean the value of this configuration
        if ($number) {
            Configuration::updateValue('PS_DELIVERY_NUMBER', false, false, null, $id_shop);
        }

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'order_invoice` SET delivery_number =';

        if ($number) {
            $sql .= (int) $number;
        } else {
            $getNumberSql = '(SELECT new_number FROM (SELECT (MAX(`delivery_number`) + 1) AS new_number
                FROM `' . _DB_PREFIX_ . 'order_invoice`) AS result)';
            $newInvoiceNumber = Db::getInstance()->getValue($getNumberSql);
            $sql .= $newInvoiceNumber;
        }

        $sql .= ' WHERE `id_order_invoice` = ' . (int) $order_invoice_id;

        return Db::getInstance()->execute($sql);
    }

    public function getDeliveryNumber($order_invoice_id)
    {
        if (!$order_invoice_id) {
            return false;
        }

        return Db::getInstance()->getValue(
            'SELECT `delivery_number`
            FROM `' . _DB_PREFIX_ . 'order_invoice`
            WHERE `id_order_invoice` = ' . (int) $order_invoice_id
        );
    }

    public function setDelivery()
    {
        // Get all invoice
        $order_invoice_collection = $this->getInvoicesCollection();
        foreach ($order_invoice_collection as $order_invoice) {
            /** @var OrderInvoice $order_invoice */
            if ($order_invoice->delivery_number) {
                continue;
            }

            // Set delivery number on invoice
            $order_invoice->delivery_number = 0;
            $order_invoice->delivery_date = date('Y-m-d H:i:s');
            // Update Order Invoice
            $order_invoice->update();
            $this->setDeliveryNumber($order_invoice->id, $this->id_shop);
            $this->delivery_number = $this->getDeliveryNumber($order_invoice->id);
        }

        // Keep it for backward compatibility, to remove on 1.6 version
        // Set delivery date
        $this->delivery_date = date('Y-m-d H:i:s');
        // Update object
        $this->update();
    }

    public static function getByDelivery($id_delivery)
    {
        $sql = 'SELECT id_order
                FROM `' . _DB_PREFIX_ . 'orders`
                WHERE `delivery_number` = ' . (int) $id_delivery . '
                ' . Shop::addSqlRestriction();
        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        return new Order((int) $res['id_order']);
    }

    /**
     * Get a collection of orders using reference.
     *
     * @since 1.5.0.14
     *
     * @param string $reference
     *
     * @return PrestaShopCollection Collection of Order
     */
    public static function getByReference($reference)
    {
        $orders = new PrestaShopCollection('Order');
        $orders->where('reference', '=', $reference);

        return $orders;
    }

    /**
     * The combination (reference, email) should be unique, of multiple entries are found, then we take the first one.
     *
     * @param $reference Order reference
     * @param $email customer email address
     *
     * @return Order The first order found
     */
    public static function getByReferenceAndEmail($reference, $email)
    {
        $sql = '
          SELECT id_order
            FROM `' . _DB_PREFIX_ . 'orders` o
            LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (o.`id_customer` = c.`id_customer`)
                WHERE o.`reference` = \'' . pSQL($reference) . '\' AND c.`email` = \'' . pSQL($email) . '\'
        ';

        $id = (int) Db::getInstance()->getValue($sql);

        return new Order($id);
    }

    public function getTotalWeight()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
        SELECT SUM(product_weight * product_quantity)
        FROM ' . _DB_PREFIX_ . 'order_detail
        WHERE id_order = ' . (int) $this->id);

        return (float) $result;
    }

    /**
     * @param int $id_invoice
     *
     * @deprecated 1.5.0.1
     */
    public static function getInvoice($id_invoice)
    {
        Tools::displayAsDeprecated();

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
        SELECT `invoice_number`, `id_order`
        FROM `' . _DB_PREFIX_ . 'orders`
        WHERE invoice_number = ' . (int) $id_invoice);
    }

    public function isAssociatedAtGuest($email)
    {
        if (!$email) {
            return false;
        }
        $sql = 'SELECT COUNT(*)
                FROM `' . _DB_PREFIX_ . 'orders` o
                LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = o.`id_customer`)
                WHERE o.`id_order` = ' . (int) $this->id . '
                    AND c.`email` = \'' . pSQL($email) . '\'
                    AND c.`is_guest` = 1
                    ' . Shop::addSqlRestriction(false, 'c');

        return (bool) Db::getInstance()->getValue($sql);
    }

    /**
     * @param int $id_order
     * @param int $id_customer optionnal
     *
     * @return int id_cart
     */
    public static function getCartIdStatic($id_order, $id_customer = 0)
    {
        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT `id_cart`
            FROM `' . _DB_PREFIX_ . 'orders`
            WHERE `id_order` = ' . (int) $id_order . '
            ' . ($id_customer ? 'AND `id_customer` = ' . (int) $id_customer : ''));
    }

    public function getWsOrderRows()
    {
        $query = '
            SELECT
            `id_order_detail` as `id`,
            `product_id`,
            `product_price`,
            `id_order`,
            `product_attribute_id`,
            `id_customization`,
            `product_quantity`,
            `product_name`,
            `product_reference`,
            `product_ean13`,
            `product_isbn`,
            `product_upc`,
            `unit_price_tax_incl`,
            `unit_price_tax_excl`
            FROM `' . _DB_PREFIX_ . 'order_detail`
            WHERE id_order = ' . (int) $this->id;
        $result = Db::getInstance()->executeS($query);

        return $result;
    }

    /** Set current order status
     * @param int $id_order_state
     * @param int $id_employee (/!\ not optional except for Webservice
     */
    public function setCurrentState($id_order_state, $id_employee = 0)
    {
        if (empty($id_order_state)) {
            return false;
        }
        $history = new OrderHistory();
        $history->id_order = (int) $this->id;
        $history->id_employee = (int) $id_employee;
        $use_existings_payment = !$this->hasInvoice();
        $history->changeIdOrderState((int) $id_order_state, $this, $use_existings_payment);
        $res = Db::getInstance()->getRow('
            SELECT `invoice_number`, `invoice_date`, `delivery_number`, `delivery_date`
            FROM `' . _DB_PREFIX_ . 'orders`
            WHERE `id_order` = ' . (int) $this->id);
        $this->invoice_date = $res['invoice_date'];
        $this->invoice_number = $res['invoice_number'];
        $this->delivery_date = $res['delivery_date'];
        $this->delivery_number = $res['delivery_number'];
        $this->update();

        $history->addWithemail();
    }

    public function addWs($autodate = true, $null_values = false)
    {
        /** @var PaymentModule $payment_module */
        $payment_module = Module::getInstanceByName($this->module);
        $customer = new Customer($this->id_customer);
        $payment_module->validateOrder($this->id_cart, Configuration::get('PS_OS_WS_PAYMENT'), $this->total_paid, $this->payment, null, [], null, false, $customer->secure_key);
        $this->id = $payment_module->currentOrder;

        return true;
    }

    public function deleteAssociations()
    {
        return Db::getInstance()->execute('
                DELETE FROM `' . _DB_PREFIX_ . 'order_detail`
                WHERE `id_order` = ' . (int) $this->id) !== false;
    }

    /**
     * This method return the ID of the previous order.
     *
     * @since 1.5.0.1
     *
     * @return int
     */
    public function getPreviousOrderId()
    {
        return Db::getInstance()->getValue('
            SELECT id_order
            FROM ' . _DB_PREFIX_ . 'orders
            WHERE id_order < ' . (int) $this->id
            . Shop::addSqlRestriction() . '
            ORDER BY id_order DESC');
    }

    /**
     * This method return the ID of the next order.
     *
     * @since 1.5.0.1
     *
     * @return int
     */
    public function getNextOrderId()
    {
        return Db::getInstance()->getValue('
            SELECT id_order
            FROM ' . _DB_PREFIX_ . 'orders
            WHERE id_order > ' . (int) $this->id
            . Shop::addSqlRestriction() . '
            ORDER BY id_order ASC');
    }

    /**
     * Get the an order detail list of the current order.
     *
     * @return array
     */
    public function getOrderDetailList()
    {
        return OrderDetail::getList($this->id);
    }

    /**
     * Generate a unique reference for orders generated with the same cart id
     * This references, is useful for check payment.
     *
     * @return string
     */
    public static function generateReference()
    {
        return strtoupper(Tools::passwdGen(9, 'NO_NUMERIC'));
    }

    public function orderContainProduct($id_product)
    {
        $product_list = $this->getOrderDetailList();
        foreach ($product_list as $product) {
            if ($product['product_id'] == (int) $id_product) {
                return true;
            }
        }

        return false;
    }

    /**
     * This method returns true if at least one order details uses the
     * One After Another tax computation method.
     *
     * @since 1.5.0.1
     *
     * @return bool
     */
    public function useOneAfterAnotherTaxComputationMethod()
    {
        // if one of the order details use the tax computation method the display will be different
        return Db::getInstance()->getValue(
            '
    		SELECT od.`tax_computation_method`
    		FROM `' . _DB_PREFIX_ . 'order_detail_tax` odt
    		LEFT JOIN `' . _DB_PREFIX_ . 'order_detail` od ON (od.`id_order_detail` = odt.`id_order_detail`)
    		WHERE od.`id_order` = ' . (int) $this->id . '
    		AND od.`tax_computation_method` = ' . (int) TaxCalculator::ONE_AFTER_ANOTHER_METHOD
        );
    }

    /**
     * This method allows to get all Order Payment for the current order.
     *
     * @since 1.5.0.1
     *
     * @return PrestaShopCollection Collection of OrderPayment
     */
    public function getOrderPaymentCollection()
    {
        $order_payments = new PrestaShopCollection('OrderPayment');
        $order_payments->where('order_reference', '=', $this->reference);

        return $order_payments;
    }

    /**
     * This method allows to add a payment to the current order.
     *
     * @since 1.5.0.1
     *
     * @param float $amount_paid
     * @param string $payment_method
     * @param string $payment_transaction_id
     * @param Currency $currency
     * @param string $date
     * @param OrderInvoice $order_invoice
     *
     * @return bool
     */
    public function addOrderPayment($amount_paid, $payment_method = null, $payment_transaction_id = null, $currency = null, $date = null, $order_invoice = null)
    {
        $order_payment = new OrderPayment();
        $order_payment->order_reference = $this->reference;
        $order_payment->id_currency = ($currency ? $currency->id : $this->id_currency);
        // we kept the currency rate for historization reasons
        $order_payment->conversion_rate = ($currency ? $currency->conversion_rate : 1);
        // if payment_method is define, we used this
        $order_payment->payment_method = ($payment_method ? $payment_method : $this->payment);
        $order_payment->transaction_id = $payment_transaction_id;
        $order_payment->amount = $amount_paid;
        $order_payment->date_add = ($date ? $date : null);

        // Add time to the date if needed
        if ($order_payment->date_add != null && preg_match('/^[0-9]+-[0-9]+-[0-9]+$/', $order_payment->date_add)) {
            $order_payment->date_add .= ' ' . date('H:i:s');
        }

        // Update total_paid_real value for backward compatibility reasons
        if ($order_payment->id_currency == $this->id_currency) {
            $this->total_paid_real += $order_payment->amount;
        } else {
            $this->total_paid_real += Tools::ps_round(Tools::convertPrice($order_payment->amount, $order_payment->id_currency, false), 2);
        }

        // We put autodate parameter of add method to true if date_add field is null
        $res = $order_payment->add(null === $order_payment->date_add) && $this->update();

        if (!$res) {
            return false;
        }

        if (null !== $order_invoice) {
            $res = Db::getInstance()->execute('
            INSERT INTO `' . _DB_PREFIX_ . 'order_invoice_payment` (`id_order_invoice`, `id_order_payment`, `id_order`)
            VALUES(' . (int) $order_invoice->id . ', ' . (int) $order_payment->id . ', ' . (int) $this->id . ')');

            // Clear cache
            Cache::clean('order_invoice_paid_*');
        }

        return $res;
    }

    /**
     * Returns the correct product taxes breakdown.
     *
     * Get all documents linked to the current order
     *
     * @since 1.5.0.1
     *
     * @return array
     */
    public function getDocuments()
    {
        $invoices = $this->getInvoicesCollection()->getResults();
        foreach ($invoices as $key => $invoice) {
            if (!$invoice->number) {
                unset($invoices[$key]);
            }
        }
        $delivery_slips = $this->getDeliverySlipsCollection()->getResults();
        // @TODO review
        foreach ($delivery_slips as $key => $delivery) {
            $delivery->is_delivery = true;
            $delivery->date_add = $delivery->delivery_date;
            if (!$invoice->delivery_number) {
                unset($delivery_slips[$key]);
            }
        }
        $order_slips = $this->getOrderSlipsCollection()->getResults();

        $documents = array_merge($invoices, $order_slips, $delivery_slips);
        usort($documents, ['Order', 'sortDocuments']);

        return $documents;
    }

    public function getReturn()
    {
        return OrderReturn::getOrdersReturn($this->id_customer, $this->id);
    }

    /**
     * @return array return all shipping method for the current order
     *               state_name sql var is now deprecated - use order_state_name for the state name and carrier_name for the carrier_name
     */
    public function getShipping()
    {
        $results = Db::getInstance()->executeS(
            'SELECT DISTINCT oc.`id_order_invoice`, oc.`weight`, oc.`shipping_cost_tax_excl`, oc.`shipping_cost_tax_incl`, c.`url`, oc.`id_carrier`, c.`name` as `carrier_name`, oc.`date_add`, "Delivery" as `type`, "true" as `can_edit`, oc.`tracking_number`, oc.`id_order_carrier`, osl.`name` as order_state_name, c.`name` as state_name
            FROM `' . _DB_PREFIX_ . 'orders` o
            LEFT JOIN `' . _DB_PREFIX_ . 'order_history` oh
                ON (o.`id_order` = oh.`id_order`)
            LEFT JOIN `' . _DB_PREFIX_ . 'order_carrier` oc
                ON (o.`id_order` = oc.`id_order`)
            LEFT JOIN `' . _DB_PREFIX_ . 'carrier` c
                ON (oc.`id_carrier` = c.`id_carrier`)
            LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl
                ON (oh.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = ' . (int) Context::getContext()->language->id . ')
            WHERE o.`id_order` = ' . (int) $this->id . '
            GROUP BY c.id_carrier'
        );
        foreach ($results as &$row) {
            $row['carrier_name'] = Cart::replaceZeroByShopName($row['carrier_name'], null);
        }

        return $results;
    }

    /**
     * Get all order_slips for the current order.
     *
     * @since 1.5.0.2
     *
     * @return PrestaShopCollection Collection of OrderSlip
     */
    public function getOrderSlipsCollection()
    {
        $order_slips = new PrestaShopCollection('OrderSlip');
        $order_slips->where('id_order', '=', $this->id);

        return $order_slips;
    }

    /**
     * Get all invoices for the current order.
     *
     * @since 1.5.0.1
     *
     * @return PrestaShopCollection Collection of OrderInvoice
     */
    public function getInvoicesCollection()
    {
        $order_invoices = new PrestaShopCollection('OrderInvoice');
        $order_invoices->where('id_order', '=', $this->id);

        return $order_invoices;
    }

    /**
     * Get all delivery slips for the current order.
     *
     * @since 1.5.0.2
     *
     * @return PrestaShopCollection Collection of OrderInvoice
     */
    public function getDeliverySlipsCollection()
    {
        $order_invoices = new PrestaShopCollection('OrderInvoice');
        $order_invoices->where('id_order', '=', $this->id);
        $order_invoices->where('delivery_number', '!=', '0');

        return $order_invoices;
    }

    /**
     * Get all not paid invoices for the current order.
     *
     * @since 1.5.0.2
     *
     * @return PrestaShopCollection Collection of Order invoice not paid
     */
    public function getNotPaidInvoicesCollection()
    {
        $invoices = $this->getInvoicesCollection();
        foreach ($invoices as $key => $invoice) {
            /** @var OrderInvoice $invoice */
            if ($invoice->isPaid()) {
                unset($invoices[$key]);
            }
        }

        return $invoices;
    }

    /**
     * Get total paid.
     *
     * @since 1.5.0.1
     *
     * @param Currency $currency currency used for the total paid of the current order
     *
     * @return float amount in the $currency
     */
    public function getTotalPaid($currency = null)
    {
        if (!$currency) {
            $currency = new Currency($this->id_currency);
        }

        $total = 0;
        // Retrieve all payments
        $payments = $this->getOrderPaymentCollection();
        foreach ($payments as $payment) {
            /** @var OrderPayment $payment */
            if ($payment->id_currency == $currency->id) {
                $total += $payment->amount;
            } else {
                $amount = Tools::convertPrice($payment->amount, $payment->id_currency, false);
                if ($currency->id == Configuration::get('PS_CURRENCY_DEFAULT', null, null, $this->id_shop)) {
                    $total += $amount;
                } else {
                    $total += Tools::convertPrice($amount, $currency->id, true);
                }
            }
        }

        return Tools::ps_round($total, 2);
    }

    /**
     * Get the sum of total_paid_tax_incl of the orders with similar reference.
     *
     * @since 1.5.0.1
     *
     * @return float
     */
    public function getOrdersTotalPaid()
    {
        return Db::getInstance()->getValue(
            'SELECT SUM(total_paid_tax_incl)
            FROM `' . _DB_PREFIX_ . 'orders`
            WHERE `reference` = \'' . pSQL($this->reference) . '\'
            AND `id_cart` = ' . (int) $this->id_cart
        );
    }

    /**
     * This method allows to change the shipping cost of the current order.
     *
     * @since 1.5.0.1
     *
     * @param float $amount
     *
     * @return bool
     */
    public function updateShippingCost($amount)
    {
        $difference = $amount - $this->total_shipping;
        // if the current amount is same as the new, we return true
        if ($difference == 0) {
            return true;
        }

        // update the total_shipping value
        $this->total_shipping = $amount;
        // update the total of this order
        $this->total_paid += $difference;

        // update database
        return $this->update();
    }

    /**
     * Returns the correct product taxes breakdown.
     *
     * @since 1.5.0.1
     *
     * @return array
     */
    public function getProductTaxesBreakdown()
    {
        $tmp_tax_infos = [];
        if ($this->useOneAfterAnotherTaxComputationMethod()) {
            // sum by taxes
            $taxes_by_tax = Db::getInstance()->executeS('
                SELECT odt.`id_order_detail`, t.`name`, t.`rate`, SUM(`total_amount`) AS `total_amount`
                FROM `' . _DB_PREFIX_ . 'order_detail_tax` odt
                LEFT JOIN `' . _DB_PREFIX_ . 'tax` t ON (t.`id_tax` = odt.`id_tax`)
                LEFT JOIN `' . _DB_PREFIX_ . 'order_detail` od ON (od.`id_order_detail` = odt.`id_order_detail`)
                WHERE od.`id_order` = ' . (int) $this->id . '
                GROUP BY odt.`id_tax`
            ');

            // format response
            $tmp_tax_infos = [];
            foreach ($taxes_by_tax as $tax_infos) {
                $tmp_tax_infos[$tax_infos['rate']]['total_amount'] = $tax_infos['tax_amount'];
                $tmp_tax_infos[$tax_infos['rate']]['name'] = $tax_infos['name'];
            }
        } else {
            // sum by order details in order to retrieve real taxes rate
            $taxes_infos = Db::getInstance()->executeS('
            SELECT odt.`id_order_detail`, t.`rate` AS `name`, SUM(od.`total_price_tax_excl`) AS total_price_tax_excl, SUM(t.`rate`) AS rate, SUM(`total_amount`) AS `total_amount`
            FROM `' . _DB_PREFIX_ . 'order_detail_tax` odt
            LEFT JOIN `' . _DB_PREFIX_ . 'tax` t ON (t.`id_tax` = odt.`id_tax`)
            LEFT JOIN `' . _DB_PREFIX_ . 'order_detail` od ON (od.`id_order_detail` = odt.`id_order_detail`)
            WHERE od.`id_order` = ' . (int) $this->id . '
            GROUP BY odt.`id_order_detail`
            ');

            // sum by taxes
            $tmp_tax_infos = [];
            foreach ($taxes_infos as $tax_infos) {
                if (!isset($tmp_tax_infos[$tax_infos['rate']])) {
                    $tmp_tax_infos[$tax_infos['rate']] = ['total_amount' => 0,
                        'name' => 0,
                        'total_price_tax_excl' => 0,
                    ];
                }

                $tmp_tax_infos[$tax_infos['rate']]['total_amount'] += $tax_infos['total_amount'];
                $tmp_tax_infos[$tax_infos['rate']]['name'] = $tax_infos['name'];
                $tmp_tax_infos[$tax_infos['rate']]['total_price_tax_excl'] += $tax_infos['total_price_tax_excl'];
            }
        }

        return $tmp_tax_infos;
    }

    /**
     * Returns the shipping taxes breakdown.
     *
     * @since 1.5.0.1
     *
     * @return array
     */
    public function getShippingTaxesBreakdown()
    {
        $taxes_breakdown = [];

        $shipping_tax_amount = $this->total_shipping_tax_incl - $this->total_shipping_tax_excl;

        if ($shipping_tax_amount > 0) {
            $taxes_breakdown[] = [
                'rate' => $this->carrier_tax_rate,
                'total_amount' => $shipping_tax_amount,
            ];
        }

        return $taxes_breakdown;
    }

    /**
     * Returns the wrapping taxes breakdown.
     *
     * @todo
     *
     * @since 1.5.0.1
     *
     * @return array
     */
    public function getWrappingTaxesBreakdown()
    {
        $taxes_breakdown = [];

        return $taxes_breakdown;
    }

    /**
     * Returns the ecotax taxes breakdown.
     *
     * @since 1.5.0.1
     *
     * @return array
     */
    public function getEcoTaxTaxesBreakdown()
    {
        return Db::getInstance()->executeS(
            '
    		SELECT `ecotax_tax_rate`, SUM(`ecotax`) as `ecotax_tax_excl`, SUM(`ecotax`) as `ecotax_tax_incl`
    		FROM `' . _DB_PREFIX_ . 'order_detail`
    		WHERE `id_order` = ' . (int) $this->id
        );
    }

    /**
     * Has invoice return true if this order has already an invoice.
     *
     * @return bool
     */
    public function hasInvoice()
    {
        return (bool) Db::getInstance()->getValue(
            'SELECT `id_order_invoice`
            FROM `' . _DB_PREFIX_ . 'order_invoice`
            WHERE `id_order` =  ' . (int) $this->id .
            (Configuration::get('PS_INVOICE') ? ' AND `number` > 0' : '')
        );
    }

    /**
     * Has Delivery return true if this order has already a delivery slip.
     *
     * @return bool
     */
    public function hasDelivery()
    {
        return (bool) $this->getOrderInvoiceIdIfHasDelivery();
    }

    /**
     * Get order invoice id if has delivery return id_order_invoice if this order has already a delivery slip.
     *
     * @return int
     */
    public function getOrderInvoiceIdIfHasDelivery()
    {
        return (int) Db::getInstance()->getValue(
            '
            SELECT `id_order_invoice`
            FROM `' . _DB_PREFIX_ . 'order_invoice`
            WHERE `id_order` =  ' . (int) $this->id . '
            AND `delivery_number` > 0'
        );
    }

    /**
     * Get warehouse associated to the order.
     *
     * return array List of warehouse
     */
    public function getWarehouseList()
    {
        $results = Db::getInstance()->executeS(
            'SELECT id_warehouse
            FROM `' . _DB_PREFIX_ . 'order_detail`
            WHERE `id_order` =  ' . (int) $this->id . '
            GROUP BY id_warehouse'
        );
        if (!$results) {
            return [];
        }

        $warehouse_list = [];
        foreach ($results as $row) {
            $warehouse_list[] = $row['id_warehouse'];
        }

        return $warehouse_list;
    }

    /**
     * @since 1.5.0.4
     *
     * @return OrderState or null if Order haven't a state
     */
    public function getCurrentOrderState()
    {
        if ($this->current_state) {
            return new OrderState($this->current_state);
        }

        return null;
    }

    /**
     * @see ObjectModel::getWebserviceObjectList()
     */
    public function getWebserviceObjectList($sql_join, $sql_filter, $sql_sort, $sql_limit)
    {
        $sql_filter .= Shop::addSqlRestriction(Shop::SHARE_ORDER, 'main');

        return parent::getWebserviceObjectList($sql_join, $sql_filter, $sql_sort, $sql_limit);
    }

    /**
     * Get all other orders with the same reference.
     *
     * @since 1.5.0.13
     */
    public function getBrother()
    {
        $collection = new PrestaShopCollection('order');
        $collection->where('reference', '=', $this->reference);
        $collection->where('id_cart', '=', $this->id_cart);
        $collection->where('id_order', '<>', $this->id);

        return $collection;
    }

    /**
     * Get a collection of order payments.
     *
     * @since 1.5.0.13
     */
    public function getOrderPayments()
    {
        return OrderPayment::getByOrderReference($this->reference);
    }

    /**
     * Return a unique reference like : GWJTHMZUN#2.
     *
     * With multishipping, order reference are the same for all orders made with the same cart
     * in this case this method suffix the order reference by a # and the order number
     *
     * @since 1.5.0.14
     */
    public function getUniqReference()
    {
        $query = new DbQuery();
        $query->select('MIN(id_order) as min, MAX(id_order) as max');
        $query->from('orders');
        $query->where('id_cart = ' . (int) $this->id_cart);

        $order = Db::getInstance()->getRow($query);

        if ($order['min'] == $order['max']) {
            return $this->reference;
        } else {
            return $this->reference . '#' . ($this->id + 1 - $order['min']);
        }
    }

    /**
     * Return a unique reference like : GWJTHMZUN#2.
     *
     * With multishipping, order reference are the same for all orders made with the same cart
     * in this case this method suffix the order reference by a # and the order number
     *
     * @since 1.5.0.14
     */
    public static function getUniqReferenceOf($id_order)
    {
        $order = new Order($id_order);

        return $order->getUniqReference();
    }

    /**
     * Return id of carrier.
     *
     * Get id of the carrier used in order
     *
     * @since 1.5.5.0
     */
    public function getIdOrderCarrier()
    {
        return (int) Db::getInstance()->getValue('
                SELECT `id_order_carrier`
                FROM `' . _DB_PREFIX_ . 'order_carrier`
                WHERE `id_order` = ' . (int) $this->id);
    }

    public static function sortDocuments($a, $b)
    {
        if ($a->date_add == $b->date_add) {
            return 0;
        }

        return ($a->date_add < $b->date_add) ? -1 : 1;
    }

    public function getWsShippingNumber()
    {
        $id_order_carrier = Db::getInstance()->getValue('
            SELECT `id_order_carrier`
            FROM `' . _DB_PREFIX_ . 'order_carrier`
            WHERE `id_order` = ' . (int) $this->id);
        if ($id_order_carrier) {
            $order_carrier = new OrderCarrier($id_order_carrier);

            return $order_carrier->tracking_number;
        }

        return $this->shipping_number;
    }

    public function setWsShippingNumber($shipping_number)
    {
        $id_order_carrier = Db::getInstance()->getValue('
            SELECT `id_order_carrier`
            FROM `' . _DB_PREFIX_ . 'order_carrier`
            WHERE `id_order` = ' . (int) $this->id);
        if ($id_order_carrier) {
            $order_carrier = new OrderCarrier($id_order_carrier);
            $order_carrier->tracking_number = $shipping_number;
            $order_carrier->update();
        } else {
            $this->shipping_number = $shipping_number;
        }

        return true;
    }

    /**
     * @deprecated since 1.6.1
     */
    public function getWsCurrentState()
    {
        return $this->getCurrentState();
    }

    public function setWsCurrentState($state)
    {
        if ($this->id) {
            $this->setCurrentState($state);
        }

        return true;
    }

    /**
     * By default this function was made for invoice, to compute tax amounts and balance delta (because of computation made on round values).
     * If you provide $limitToOrderDetails, only these item will be taken into account. This option is useful for order slip for example,
     * where only sublist of the order is refunded.
     *
     * @param $limitToOrderDetails Optional array of OrderDetails to take into account. False by default to take all OrderDetails from the current Order.
     *
     * @return array a list of tax rows applied to the given OrderDetails (or all OrderDetails linked to the current Order)
     */
    public function getProductTaxesDetails($limitToOrderDetails = false)
    {
        $round_type = $this->round_type;
        if ($round_type == 0) {
            // if this is 0, it means the field did not exist
            // at the time the order was made.
            // Set it to old type, which was closest to line.
            $round_type = Order::ROUND_LINE;
        }

        // compute products discount
        $order_discount_tax_excl = $this->total_discounts_tax_excl;

        $free_shipping_tax = 0;
        $product_specific_discounts = [];

        $expected_total_base = $this->total_products - $this->total_discounts_tax_excl;

        foreach ($this->getCartRules() as $order_cart_rule) {
            if ($order_cart_rule['free_shipping'] && $free_shipping_tax === 0) {
                $free_shipping_tax = $this->total_shipping_tax_incl - $this->total_shipping_tax_excl;
                $order_discount_tax_excl -= $this->total_shipping_tax_excl;
                $expected_total_base += $this->total_shipping_tax_excl;
            }

            $cart_rule = new CartRule($order_cart_rule['id_cart_rule']);
            if ($cart_rule->reduction_product > 0) {
                if (empty($product_specific_discounts[$cart_rule->reduction_product])) {
                    $product_specific_discounts[$cart_rule->reduction_product] = 0;
                }

                $product_specific_discounts[$cart_rule->reduction_product] += $order_cart_rule['value_tax_excl'];
                $order_discount_tax_excl -= $order_cart_rule['value_tax_excl'];
            }
        }

        $products_tax = $this->total_products_wt - $this->total_products;
        $discounts_tax = $this->total_discounts_tax_incl - $this->total_discounts_tax_excl;

        // We add $free_shipping_tax because when there is free shipping, the tax that would
        // be paid if there wasn't is included in $discounts_tax.
        $expected_total_tax = $products_tax - $discounts_tax + $free_shipping_tax;
        $actual_total_tax = 0;
        $actual_total_base = 0;

        $order_detail_tax_rows = [];

        $breakdown = [];

        // Get order_details
        $order_details = $limitToOrderDetails ? $limitToOrderDetails : $this->getOrderDetailList();

        $order_ecotax_tax = 0;

        $tax_rates = [];

        foreach ($order_details as $order_detail) {
            $id_order_detail = $order_detail['id_order_detail'];
            $tax_calculator = OrderDetail::getTaxCalculatorStatic($id_order_detail);

            // TODO: probably need to make an ecotax tax breakdown here instead,
            // but it seems unlikely there will be different tax rates applied to the
            // ecotax in the same order in the real world
            $unit_ecotax_tax = $order_detail['ecotax'] * $order_detail['ecotax_tax_rate'] / 100.0;
            $order_ecotax_tax += $order_detail['product_quantity'] * $unit_ecotax_tax;

            $discount_ratio = 0;

            if ($this->total_products > 0) {
                $discount_ratio = ($order_detail['unit_price_tax_excl'] + $order_detail['ecotax']) / $this->total_products;
            }

            // share of global discount
            $discounted_price_tax_excl = $order_detail['unit_price_tax_excl'] - $discount_ratio * $order_discount_tax_excl;
            // specific discount
            if (!empty($product_specific_discounts[$order_detail['product_id']])) {
                $discounted_price_tax_excl -= $product_specific_discounts[$order_detail['product_id']];
            }

            $quantity = $order_detail['product_quantity'];

            foreach ($tax_calculator->taxes as $tax) {
                $tax_rates[$tax->id] = $tax->rate;
            }

            foreach ($tax_calculator->getTaxesAmount($discounted_price_tax_excl) as $id_tax => $unit_amount) {
                $total_tax_base = 0;
                switch ($round_type) {
                    case Order::ROUND_ITEM:
                        $total_tax_base = $quantity * Tools::ps_round($discounted_price_tax_excl, Context::getContext()->getComputingPrecision(), $this->round_mode);
                        $total_amount = $quantity * Tools::ps_round($unit_amount, Context::getContext()->getComputingPrecision(), $this->round_mode);

                        break;
                    case Order::ROUND_LINE:
                        $total_tax_base = Tools::ps_round($quantity * $discounted_price_tax_excl, Context::getContext()->getComputingPrecision(), $this->round_mode);
                        $total_amount = Tools::ps_round($quantity * $unit_amount, Context::getContext()->getComputingPrecision(), $this->round_mode);

                        break;
                    case Order::ROUND_TOTAL:
                        $total_tax_base = $quantity * $discounted_price_tax_excl;
                        $total_amount = $quantity * $unit_amount;

                        break;
                }

                if (!isset($breakdown[$id_tax])) {
                    $breakdown[$id_tax] = ['tax_base' => 0, 'tax_amount' => 0];
                }

                $breakdown[$id_tax]['tax_base'] += $total_tax_base;
                $breakdown[$id_tax]['tax_amount'] += $total_amount;

                $order_detail_tax_rows[] = [
                    'id_order_detail' => $id_order_detail,
                    'id_tax' => $id_tax,
                    'tax_rate' => $tax_rates[$id_tax],
                    'unit_tax_base' => $discounted_price_tax_excl,
                    'total_tax_base' => $total_tax_base,
                    'unit_amount' => $unit_amount,
                    'total_amount' => $total_amount,
                ];
            }
        }

        if (!empty($order_detail_tax_rows)) {
            foreach ($breakdown as $data) {
                $actual_total_tax += Tools::ps_round($data['tax_amount'], Context::getContext()->getComputingPrecision(), $this->round_mode);
                $actual_total_base += Tools::ps_round($data['tax_base'], Context::getContext()->getComputingPrecision(), $this->round_mode);
            }

            $order_ecotax_tax = Tools::ps_round($order_ecotax_tax, Context::getContext()->getComputingPrecision(), $this->round_mode);

            $tax_rounding_error = $expected_total_tax - $actual_total_tax - $order_ecotax_tax;
            if ($tax_rounding_error !== 0) {
                Tools::spreadAmount($tax_rounding_error, Context::getContext()->getComputingPrecision(), $order_detail_tax_rows, 'total_amount');
            }

            $base_rounding_error = $expected_total_base - $actual_total_base;
            if ($base_rounding_error !== 0) {
                Tools::spreadAmount($base_rounding_error, Context::getContext()->getComputingPrecision(), $order_detail_tax_rows, 'total_tax_base');
            }
        }

        return $order_detail_tax_rows;
    }

    /**
     * The primary purpose of this method is to be
     * called at the end of the generation of each order
     * in PaymentModule::validateOrder, to fill in
     * the order_detail_tax table with taxes
     * that will add up in such a way that
     * the sum of the tax amounts in the product tax breakdown
     * is equal to the difference between products with tax and
     * products without tax.
     */
    public function updateOrderDetailTax()
    {
        $order_detail_tax_rows_to_insert = $this->getProductTaxesDetails();

        if (empty($order_detail_tax_rows_to_insert)) {
            return;
        }

        $old_id_order_details = [];
        $values = [];
        foreach ($order_detail_tax_rows_to_insert as $row) {
            $old_id_order_details[] = (int) $row['id_order_detail'];
            $values[] = '(' . (int) $row['id_order_detail'] . ', ' . (int) $row['id_tax'] . ', ' . (float) $row['unit_amount'] . ', ' . (float) $row['total_amount'] . ')';
        }

        // Remove current order_detail_tax'es
        Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'order_detail_tax` WHERE id_order_detail IN (' . implode(', ', $old_id_order_details) . ')'
        );

        // Insert the adjusted ones instead
        Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'order_detail_tax` (id_order_detail, id_tax, unit_amount, total_amount) VALUES ' . implode(', ', $values)
        );
    }

    public function getOrderDetailTaxes()
    {
        return Db::getInstance()->executeS(
            'SELECT od.id_tax_rules_group, od.product_quantity, odt.*, t.* FROM ' . _DB_PREFIX_ . 'orders o ' .
            'INNER JOIN ' . _DB_PREFIX_ . 'order_detail od ON od.id_order = o.id_order ' .
            'INNER JOIN ' . _DB_PREFIX_ . 'order_detail_tax odt ON odt.id_order_detail = od.id_order_detail ' .
            'INNER JOIN ' . _DB_PREFIX_ . 'tax t ON t.id_tax = odt.id_tax ' .
            'WHERE o.id_order = ' . (int) $this->id
        );
    }

    /**
     * Re calculate shipping cost.
     *
     * @return object $order
     */
    public function refreshShippingCost()
    {
        if (empty($this->id)) {
            return false;
        }

        if (!Configuration::get('PS_ORDER_RECALCULATE_SHIPPING')) {
            return $this;
        }

        $fake_cart = new Cart((int) $this->id_cart);
        $new_cart = $fake_cart->duplicate();
        $new_cart = $new_cart['cart'];

        // assign order id_address_delivery to cart
        $new_cart->id_address_delivery = (int) $this->id_address_delivery;

        // assign id_carrier
        $new_cart->id_carrier = (int) $this->id_carrier;

        //remove all products : cart (maybe change in the meantime)
        foreach ($new_cart->getProducts() as $product) {
            $new_cart->deleteProduct((int) $product['id_product'], (int) $product['id_product_attribute']);
        }

        // add real order products
        foreach ($this->getProducts() as $product) {
            $new_cart->updateQty(
                $product['product_quantity'],
                (int) $product['product_id'],
                null,
                false,
                'up',
                0,
                null,
                true,
                true
            ); // - skipAvailabilityCheckOutOfStock
        }

        // get new shipping cost
        $base_total_shipping_tax_incl = (float) $new_cart->getPackageShippingCost((int) $new_cart->id_carrier, true, null);
        $base_total_shipping_tax_excl = (float) $new_cart->getPackageShippingCost((int) $new_cart->id_carrier, false, null);

        // calculate diff price, then apply new order totals
        $diff_shipping_tax_incl = $this->total_shipping_tax_incl - $base_total_shipping_tax_incl;
        $diff_shipping_tax_excl = $this->total_shipping_tax_excl - $base_total_shipping_tax_excl;

        $this->total_shipping_tax_excl -= $diff_shipping_tax_excl;
        $this->total_shipping_tax_incl -= $diff_shipping_tax_incl;
        $this->total_shipping = $this->total_shipping_tax_incl;
        $this->total_paid_tax_excl -= $diff_shipping_tax_excl;
        $this->total_paid_tax_incl -= $diff_shipping_tax_incl;
        $this->total_paid = $this->total_paid_tax_incl;
        $this->update();

        // save order_carrier prices, we'll save order right after this in update() method
        $orderCarrierId = (int) $this->getIdOrderCarrier();
        if ($orderCarrierId > 0) {
            $order_carrier = new OrderCarrier($orderCarrierId);
            $order_carrier->shipping_cost_tax_excl = $this->total_shipping_tax_excl;
            $order_carrier->shipping_cost_tax_incl = $this->total_shipping_tax_incl;
            $order_carrier->update();
        }

        // remove fake cart
        $new_cart->delete();

        return $this;
    }
}
