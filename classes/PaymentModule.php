<?php
/**
 * 2007-2016 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class PaymentModuleCore
 */
abstract class PaymentModuleCore extends Module
{
    /** @var int Current order's id */
    public $currentOrder;
    public $currencies = true;
    public $currencies_mode = 'checkbox';

    const DEBUG_MODE = false;

    /**
     * Install PaymentModule
     *
     * @return bool Indicates whether the PaymentModule was successfully installed
     */
    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        // Insert currencies availability
        if ($this->currencies_mode == 'checkbox') {
            if (!$this->addCheckboxCurrencyRestrictionsForModule()) {
                return false;
            }
        } elseif ($this->currencies_mode == 'radio') {
            if (!$this->addRadioCurrencyRestrictionsForModule()) {
                return false;
            }
        } else {
            Tools::displayError('No currency mode for payment module');
        }

        // Insert countries availability
        $return = $this->addCheckboxCountryRestrictionsForModule();

        // Insert carrier availability
        $return &= $this->addCheckboxCarrierRestrictionsForModule();

        if (!Configuration::get('CONF_'.strtoupper($this->name).'_FIXED')) {
            Configuration::updateValue('CONF_'.strtoupper($this->name).'_FIXED', '0.2');
        }
        if (!Configuration::get('CONF_'.strtoupper($this->name).'_VAR')) {
            Configuration::updateValue('CONF_'.strtoupper($this->name).'_VAR', '2');
        }
        if (!Configuration::get('CONF_'.strtoupper($this->name).'_FIXED_FOREIGN')) {
            Configuration::updateValue('CONF_'.strtoupper($this->name).'_FIXED_FOREIGN', '0.2');
        }
        if (!Configuration::get('CONF_'.strtoupper($this->name).'_VAR_FOREIGN')) {
            Configuration::updateValue('CONF_'.strtoupper($this->name).'_VAR_FOREIGN', '2');
        }

        return $return;
    }

    /**
     * Uninstall the PaymentModule
     *
     * @return bool Indicates whether the PaymentModule was successfully uninstalled
     */
    public function uninstall()
    {
        if (!Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'module_country` WHERE id_module = '.(int) $this->id)
            || !Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'module_currency` WHERE id_module = '.(int) $this->id)
            || !Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'module_group` WHERE id_module = '.(int) $this->id)
            || !Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'module_carrier` WHERE id_module = '.(int) $this->id)) {
            return false;
        }

        return parent::uninstall();
    }

    /**
     * Add checkbox currency restrictions for a new module
     * @param array $shops
     *
     * @return bool
     */
    public function addCheckboxCurrencyRestrictionsForModule(array $shops = array())
    {
        if (!$shops) {
            $shops = Shop::getShops(true, null, true);
        }

        foreach ($shops as $s) {
            if (!Db::getInstance()->execute('
                    INSERT INTO `'._DB_PREFIX_.'module_currency` (`id_module`, `id_shop`, `id_currency`)
                    SELECT '.(int) $this->id.', "'.(int) $s.'", `id_currency` FROM `'._DB_PREFIX_.'currency` WHERE deleted = 0')) {
                return false;
            }
        }

        return true;
    }

    /**
     * Add radio currency restrictions for a new module
     *
     * @param array $shops
     *
     * @return bool
     */
    public function addRadioCurrencyRestrictionsForModule(array $shops = array())
    {
        if (!$shops) {
            $shops = Shop::getShops(true, null, true);
        }

        foreach ($shops as $s) {
            if (!Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'module_currency` (`id_module`, `id_shop`, `id_currency`)
                VALUES ('.(int) $this->id.', "'.(int) $s.'", -2)')) {
                return false;
            }
        }

        return true;
    }

    /**
     * Add checkbox country restrictions for a new module
     * @param array $shops
     *
     * @return bool
     */
    public function addCheckboxCountryRestrictionsForModule(array $shops = array())
    {
        $countries = Country::getCountries((int) Context::getContext()->language->id, true); //get only active country
        $countryIds = array();
        foreach ($countries as $country) {
            $countryIds[] = $country['id_country'];
        }

        return Country::addModuleRestrictions($shops, $countries, array(array('id_module' => (int) $this->id)));
    }

    /**
     * Add checkbox carrier restrictions for a new module
     * @param array $shops
     *
     * @return bool
     */
    public function addCheckboxCarrierRestrictionsForModule(array $shops = array())
    {
        if (!$shops) {
            $shops = Shop::getShops(true, null, true);
        }

        $carriers = Carrier::getCarriers((int) Context::getContext()->language->id);
        $carrierIds = array();
        foreach ($carriers as $carrier) {
            $carrierIds[] = $carrier['id_reference'];
        }

        foreach ($shops as $s) {
            foreach ($carrierIds as $idCarrier) {
                if (!Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'module_carrier` (`id_module`, `id_shop`, `id_reference`)
				VALUES ('.(int) $this->id.', "'.(int) $s.'", '.(int) $idCarrier.')')) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Validate an order in database
     * Function called from a payment module
     *
     * @param int    $idCart
     * @param int    $idOrderState
     * @param float  $amountPaid    Amount really paid by customer (in the default currency)
     * @param string $paymentMethod Payment method (eg. 'Credit card')
     * @param null   $message       Message to attach to order
     * @param array  $extraVars
     * @param null   $currencySpecial
     * @param bool   $dontTouchAmount
     * @param bool   $secureKey
     * @param Shop   $shop
     *
     * @return bool
     * @throws PrestaShopException
     */
    public function validateOrder($idCart, $idOrderState, $amountPaid, $paymentMethod = 'Unknown',
        $message = null, $extraVars = array(), $currencySpecial = null, $dontTouchAmount = false,
        $secureKey = false, Shop $shop = null)
    {
        if (self::DEBUG_MODE) {
            PrestaShopLogger::addLog('PaymentModule::validateOrder - Function called', 1, null, 'Cart', (int) $idCart, true);
        }

        if (!isset($this->context)) {
            $this->context = Context::getContext();
        }
        $this->context->cart = new Cart((int) $idCart);
        $this->context->customer = new Customer((int) $this->context->cart->id_customer);
        // The tax cart is loaded before the customer so re-cache the tax calculation method
        $this->context->cart->setTaxCalculationMethod();

        $this->context->language = new Language((int) $this->context->cart->id_lang);
        $this->context->shop = ($shop ? $shop : new Shop((int) $this->context->cart->id_shop));
        ShopUrl::resetMainDomainCache();
        $idCurrency = $currencySpecial ? (int) $currencySpecial : (int) $this->context->cart->id_currency;
        $this->context->currency = new Currency((int) $idCurrency, null, (int) $this->context->shop->id);
        if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_delivery') {
            $contextCountry = $this->context->country;
        }

        $orderStatus = new OrderState((int) $idOrderState, (int) $this->context->language->id);
        if (!Validate::isLoadedObject($orderStatus)) {
            PrestaShopLogger::addLog('PaymentModule::validateOrder - Order Status cannot be loaded', 3, null, 'Cart', (int) $idCart, true);
            throw new PrestaShopException('Can\'t load Order status');
        }

        if (!$this->active) {
            PrestaShopLogger::addLog('PaymentModule::validateOrder - Module is not active', 3, null, 'Cart', (int) $idCart, true);
            die(Tools::displayError());
        }

        // Does order already exists ?
        if (Validate::isLoadedObject($this->context->cart) && $this->context->cart->orderExists() == false) {
            if ($secureKey !== false && $secureKey != $this->context->cart->secure_key) {
                PrestaShopLogger::addLog('PaymentModule::validateOrder - Secure key does not match', 3, null, 'Cart', (int) $idCart, true);
                die(Tools::displayError());
            }

            // For each package, generate an order
            $deliveryOptionList = $this->context->cart->getDeliveryOptionList();
            $packageList = $this->context->cart->getPackageList();
            $cartDeliveryOption = $this->context->cart->getDeliveryOption();

            // If some delivery options are not defined, or not valid, use the first valid option
            foreach ($deliveryOptionList as $idAddress => $package) {
                if (!isset($cartDeliveryOption[$idAddress]) || !array_key_exists($cartDeliveryOption[$idAddress], $package)) {
                    foreach ($package as $key => $val) {
                        $cartDeliveryOption[$idAddress] = $key;
                        break;
                    }
                }
            }

            $orderList = array();
            $orderDetailList = array();

            do {
                $reference = Order::generateReference();
            } while (Order::getByReference($reference)->count());

            $this->currentOrderReference = $reference;

            $cartTotalPaid = (float)Tools::ps_round((float)$this->context->cart->getOrderTotal(true, Cart::BOTH), 2);

            foreach ($cartDeliveryOption as $idAddress => $keyCarriers) {
                foreach ($deliveryOptionList[$idAddress][$keyCarriers]['carrier_list'] as $idCarrier => $data) {
                    foreach ($data['package_list'] as $idPackage) {
                        // Rewrite the id_warehouse
                        $packageList[$idAddress][$idPackage]['id_warehouse'] = (int)$this->context->cart->getPackageIdWarehouse($packageList[$idAddress][$idPackage], (int) $idCarrier);
                        $packageList[$idAddress][$idPackage]['id_carrier'] = $idCarrier;
                    }
                }
            }
            // Make sure CartRule caches are empty
            CartRule::cleanCache();
            $cartRules = $this->context->cart->getCartRules();
            foreach ($cartRules as $cartRule) {
                if (($rule = new CartRule((int) $cartRule['obj']->id)) && Validate::isLoadedObject($rule)) {
                    if ($error = $rule->checkValidity($this->context, true, true)) {
                        $this->context->cart->removeCartRule((int) $rule->id);
                        if (isset($this->context->cookie) && isset($this->context->cookie->id_customer) && $this->context->cookie->id_customer && !empty($rule->code)) {
                            Tools::redirect('index.php?controller=order&submitAddDiscount=1&discount_name='.urlencode($rule->code));
                        } else {
                            $ruleName = isset($rule->name[(int) $this->context->cart->id_lang]) ? $rule->name[(int) $this->context->cart->id_lang] : $rule->code;
                            $error = sprintf(Tools::displayError('CartRule ID %1s (%2s) used in this cart is not valid and has been withdrawn from cart'), (int) $rule->id, $ruleName);
                            PrestaShopLogger::addLog($error, 3, '0000002', 'Cart', (int)$this->context->cart->id);
                        }
                    }
                }
            }

            foreach ($packageList as $idAddress => $packageByAddress) {
                foreach ($packageByAddress as $idPackage => $package) {
                    /** @var Order $order */
                    $order = new Order();
                    $order->product_list = $package['product_list'];

                    if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_delivery') {
                        $address = new Address((int) $idAddress);
                        $this->context->country = new Country((int)$address->id_country, (int) $this->context->cart->id_lang);
                        if (!$this->context->country->active) {
                            throw new PrestaShopException('The delivery address country is not active.');
                        }
                    }

                    $carrier = null;
                    if (!$this->context->cart->isVirtualCart() && isset($package['id_carrier'])) {
                        $carrier = new Carrier((int) $package['id_carrier'], (int) $this->context->cart->id_lang);
                        $order->id_carrier = (int) $carrier->id;
                        $idCarrier = (int) $carrier->id;
                    } else {
                        $order->id_carrier = 0;
                        $idCarrier = 0;
                    }

                    $order->id_customer = (int) $this->context->cart->id_customer;
                    $order->id_address_invoice = (int) $this->context->cart->id_address_invoice;
                    $order->id_address_delivery = (int) $idAddress;
                    $order->id_currency = $this->context->currency->id;
                    $order->id_lang = (int) $this->context->cart->id_lang;
                    $order->id_cart = (int) $this->context->cart->id;
                    $order->reference = $reference;
                    $order->id_shop = (int) $this->context->shop->id;
                    $order->id_shop_group = (int) $this->context->shop->id_shop_group;

                    $order->secure_key = ($secureKey ? pSQL($secureKey) : pSQL($this->context->customer->secure_key));
                    $order->payment = $paymentMethod;
                    if (isset($this->name)) {
                        $order->module = $this->name;
                    }
                    $order->recyclable = $this->context->cart->recyclable;
                    $order->gift = (int) $this->context->cart->gift;
                    $order->gift_message = $this->context->cart->gift_message;
                    $order->mobile_theme = $this->context->cart->mobile_theme;
                    $order->conversion_rate = $this->context->currency->conversion_rate;
                    $amountPaid = !$dontTouchAmount ? Tools::ps_round((float) $amountPaid, 2) : $amountPaid;
                    $order->total_paid_real = 0;

                    $order->total_products = (float) $this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS, $order->product_list, $idCarrier);
                    $order->total_products_wt = (float) $this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS, $order->product_list, $idCarrier);
                    $order->total_discounts_tax_excl = (float) abs($this->context->cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS, $order->product_list, $idCarrier));
                    $order->total_discounts_tax_incl = (float) abs($this->context->cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS, $order->product_list, $idCarrier));
                    $order->total_discounts = $order->total_discounts_tax_incl;

                    $order->total_shipping_tax_excl = (float) $this->context->cart->getPackageShippingCost((int)$idCarrier, false, null, $order->product_list);
                    $order->total_shipping_tax_incl = (float) $this->context->cart->getPackageShippingCost((int)$idCarrier, true, null, $order->product_list);
                    $order->total_shipping = $order->total_shipping_tax_incl;

                    if (!is_null($carrier) && Validate::isLoadedObject($carrier)) {
                        $order->carrier_tax_rate = $carrier->getTaxesRate(new Address((int) $this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
                    }

                    $order->total_wrapping_tax_excl = (float) abs($this->context->cart->getOrderTotal(false, Cart::ONLY_WRAPPING, $order->product_list, $idCarrier));
                    $order->total_wrapping_tax_incl = (float) abs($this->context->cart->getOrderTotal(true, Cart::ONLY_WRAPPING, $order->product_list, $idCarrier));
                    $order->total_wrapping = $order->total_wrapping_tax_incl;

                    $order->total_paid_tax_excl = (float) Tools::ps_round((float)$this->context->cart->getOrderTotal(false, Cart::BOTH, $order->product_list, $idCarrier), _PS_PRICE_COMPUTE_PRECISION_);
                    $order->total_paid_tax_incl = (float) Tools::ps_round((float)$this->context->cart->getOrderTotal(true, Cart::BOTH, $order->product_list, $idCarrier), _PS_PRICE_COMPUTE_PRECISION_);
                    $order->total_paid = $order->total_paid_tax_incl;
                    $order->round_mode = Configuration::get('PS_PRICE_ROUND_MODE');
                    $order->round_type = Configuration::get('PS_ROUND_TYPE');

                    $order->invoice_date = '0000-00-00 00:00:00';
                    $order->delivery_date = '0000-00-00 00:00:00';

                    if (self::DEBUG_MODE) {
                        PrestaShopLogger::addLog('PaymentModule::validateOrder - Order is about to be added', 1, null, 'Cart', (int) $idCart, true);
                    }

                    // Creating order
                    $result = $order->add();

                    if (!$result) {
                        PrestaShopLogger::addLog('PaymentModule::validateOrder - Order cannot be created', 3, null, 'Cart', (int) $idCart, true);
                        throw new PrestaShopException('Can\'t save Order');
                    }

                    // Amount paid by customer is not the right one -> Status = payment error
                    // We don't use the following condition to avoid the float precision issues : http://www.php.net/manual/en/language.types.float.php
                    // if ($order->total_paid != $order->total_paid_real)
                    // We use number_format in order to compare two string
                    if ($orderStatus->logable && number_format($cartTotalPaid, _PS_PRICE_COMPUTE_PRECISION_) != number_format($amountPaid, _PS_PRICE_COMPUTE_PRECISION_)) {
                        $idOrderState = Configuration::get('PS_OS_ERROR');
                    }

                    $orderList[] = $order;

                    if (self::DEBUG_MODE) {
                        PrestaShopLogger::addLog('PaymentModule::validateOrder - OrderDetail is about to be added', 1, null, 'Cart', (int) $idCart, true);
                    }

                    // Insert new Order detail list using cart for the current order
                    $orderDetail = new OrderDetail(null, null, $this->context);
                    $orderDetail->createList($order, $this->context->cart, $idOrderState, $order->product_list, 0, true, $packageList[$idAddress][$idPackage]['id_warehouse']);
                    $orderDetailList[] = $orderDetail;

                    if (self::DEBUG_MODE) {
                        PrestaShopLogger::addLog('PaymentModule::validateOrder - OrderCarrier is about to be added', 1, null, 'Cart', (int) $idCart, true);
                    }

                    // Adding an entry in order_carrier table
                    if (!is_null($carrier)) {
                        $orderCarrier = new OrderCarrier();
                        $orderCarrier->id_order = (int) $order->id;
                        $orderCarrier->id_carrier = (int) $idCarrier;
                        $orderCarrier->weight = (float) $order->getTotalWeight();
                        $orderCarrier->shipping_cost_tax_excl = (float) $order->total_shipping_tax_excl;
                        $orderCarrier->shipping_cost_tax_incl = (float) $order->total_shipping_tax_incl;
                        $orderCarrier->add();
                    }
                }
            }

            // The country can only change if the address used for the calculation is the delivery address, and if multi-shipping is activated
            if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_delivery') {
                $this->context->country = $contextCountry;
            }

            if (!$this->context->country->active) {
                PrestaShopLogger::addLog('PaymentModule::validateOrder - Country is not active', 3, null, 'Cart', (int) $idCart, true);
                throw new PrestaShopException('The order address country is not active.');
            }

            if (self::DEBUG_MODE) {
                PrestaShopLogger::addLog('PaymentModule::validateOrder - Payment is about to be added', 1, null, 'Cart', (int) $idCart, true);
            }

            // Register Payment only if the order status validate the order
            if ($orderStatus->logable) {
                // $order is the last order loop in the foreach
                // The method addOrderPayment of the class Order make a create a paymentOrder
                // linked to the order reference and not to the order id
                if (isset($extraVars['transaction_id'])) {
                    $transactionId = $extraVars['transaction_id'];
                } else {
                    $transactionId = null;
                }

                if (!$order->addOrderPayment($amountPaid, null, $transactionId)) {
                    PrestaShopLogger::addLog('PaymentModule::validateOrder - Cannot save Order Payment', 3, null, 'Cart', (int) $idCart, true);
                    throw new PrestaShopException('Can\'t save Order Payment');
                }
            }

            // Next !
            $cartRuleUsed = array();
            $products = $this->context->cart->getProducts();

            // Make sure CartRule caches are empty
            CartRule::cleanCache();
            foreach ($orderDetailList as $key => $orderDetail) {
                /** @var OrderDetail $orderDetail */

                $order = $orderList[$key];
                if (isset($order->id)) {
                    if (!$secureKey) {
                        $message .= '<br />'.Tools::displayError('Warning: the secure key is empty, check your payment account before validation');
                    }
                    // Optional message to attach to this order
                    if (isset($message) & !empty($message)) {
                        $msg = new Message();
                        $message = strip_tags($message, '<br>');
                        if (Validate::isCleanHtml($message)) {
                            if (self::DEBUG_MODE) {
                                PrestaShopLogger::addLog('PaymentModule::validateOrder - Message is about to be added', 1, null, 'Cart', (int) $idCart, true);
                            }
                            $msg->message = $message;
                            $msg->id_cart = (int) $idCart;
                            $msg->id_customer = (int) $order->id_customer;
                            $msg->id_order = (int) $order->id;
                            $msg->private = 1;
                            $msg->add();
                        }
                    }

                    // Insert new Order detail list using cart for the current order
                    //$orderDetail = new OrderDetail(null, null, $this->context);
                    //$orderDetail->createList($order, $this->context->cart, $id_order_state);

                    // Construct order detail table for the email
                    $virtualProduct = true;

                    $productVarTplList = array();
                    foreach ($order->product_list as $product) {
                        $price = Product::getPriceStatic((int) $product['id_product'], false, ($product['id_product_attribute'] ? (int) $product['id_product_attribute'] : null), 6, null, false, true, $product['cart_quantity'], false, (int) $order->id_customer, (int) $order->id_cart, (int) $order->{Configuration::get('PS_TAX_ADDRESS_TYPE')}, $specificPrice, true, true, null, true, $product['id_customization']);
                        $priceWithTaxes = Product::getPriceStatic((int) $product['id_product'], true, ($product['id_product_attribute'] ? (int) $product['id_product_attribute'] : null), 2, null, false, true, $product['cart_quantity'], false, (int) $order->id_customer, (int) $order->id_cart, (int) $order->{Configuration::get('PS_TAX_ADDRESS_TYPE')}, $specificPrice, true, true, null, true, $product['id_customization']);

                        $productPrice = Product::getTaxCalculationMethod() == PS_TAX_EXC ? Tools::ps_round($price, 2) : $priceWithTaxes;

                        $productVarTpl = array(
                            'id_product' => $product['id_product'],
                            'reference' => $product['reference'],
                            'name' => $product['name'].(isset($product['attributes']) ? ' - '.$product['attributes'] : ''),
                            'price' => Tools::displayPrice($productPrice * $product['quantity'], $this->context->currency, false),
                            'quantity' => $product['quantity'],
                            'customization' => array(),
                        );

                        if (isset($product['unit_price']) && $product['unit_price']) {
                            $productVarTpl['unit_price'] = Tools::displayPrice($product['unit_price'], $this->context->currency, false);
                            $productVarTpl['unit_price_full'] = Tools::displayPrice($product['unit_price'], $this->context->currency, false).' '.$product['unity'];
                        } else {
                            $productVarTpl['unit_price'] = $productVarTpl['unit_price_full'] = '';
                        }

                        $customizedDatas = Product::getAllCustomizedDatas((int) $order->id_cart, null, true, null, (int) $product['id_customization']);
                        if (isset($customizedDatas[$product['id_product']][$product['id_product_attribute']])) {
                            $productVarTpl['customization'] = array();
                            foreach ($customizedDatas[$product['id_product']][$product['id_product_attribute']][$order->id_address_delivery] as $customization) {
                                $customizationText = '';
                                if (isset($customization['datas'][Product::CUSTOMIZE_TEXTFIELD])) {
                                    foreach ($customization['datas'][Product::CUSTOMIZE_TEXTFIELD] as $text) {
                                        $customizationText .= '<strong>'.$text['name'].'</strong>: '.$text['value'].'<br />';
                                    }
                                }

                                if (isset($customization['datas'][Product::CUSTOMIZE_FILE])) {
                                    $customizationText .= sprintf(Tools::displayError('%d image(s)'), count($customization['datas'][Product::CUSTOMIZE_FILE])).'<br />';
                                }

                                $customizationQuantity = (int)$customization['quantity'];

                                $productVarTpl['customization'][] = array(
                                    'customization_text' => $customizationText,
                                    'customization_quantity' => $customizationQuantity,
                                    'quantity' => Tools::displayPrice($customizationQuantity * $productPrice, $this->context->currency, false)
                                );
                            }
                        }

                        $productVarTplList[] = $productVarTpl;
                        // Check if is not a virutal product for the displaying of shipping
                        if (!$product['is_virtual']) {
                            $virtualProduct &= false;
                        }
                    } // end foreach ($products)

                    $productListTxt = '';
                    $productListHtml = '';
                    if (count($productVarTplList) > 0) {
                        $productListTxt = $this->getEmailTemplateContent('order_conf_product_list.txt', Mail::TYPE_TEXT, $productVarTplList);
                        $productListHtml = $this->getEmailTemplateContent('order_conf_product_list.tpl', Mail::TYPE_HTML, $productVarTplList);
                    }

                    $cartRulesList = array();
                    $totalReductionValueTi = 0;
                    $totalReductionValueTex = 0;
                    foreach ($cartRules as $cartRule) {
                        $package = array('id_carrier' => $order->id_carrier, 'id_address' => $order->id_address_delivery, 'products' => $order->product_list);
                        $values = array(
                            'tax_incl' => $cartRule['obj']->getContextualValue(true, $this->context, CartRule::FILTER_ACTION_ALL_NOCAP, $package),
                            'tax_excl' => $cartRule['obj']->getContextualValue(false, $this->context, CartRule::FILTER_ACTION_ALL_NOCAP, $package),
                        );

                        // If the reduction is not applicable to this order, then continue with the next one
                        if (!$values['tax_excl']) {
                            continue;
                        }

                        // IF
                        //  This is not multi-shipping
                        //  The value of the voucher is greater than the total of the order
                        //  Partial use is allowed
                        //  This is an "amount" reduction, not a reduction in % or a gift
                        // THEN
                        //  The voucher is cloned with a new value corresponding to the remainder
                        if (count($orderList) == 1 && $values['tax_incl'] > ($order->total_products_wt - $totalReductionValueTi) && $cartRule['obj']->partial_use == 1 && $cartRule['obj']->reduction_amount > 0) {
                            // Create a new voucher from the original
                            $voucher = new CartRule((int) $cartRule['obj']->id); // We need to instantiate the CartRule without lang parameter to allow saving it
                            unset($voucher->id);

                            // Set a new voucher code
                            $voucher->code = empty($voucher->code) ? substr(md5($order->id.'-'.$order->id_customer.'-'.$cartRule['obj']->id), 0, 16) : $voucher->code.'-2';
                            if (preg_match('/\-([0-9]{1,2})\-([0-9]{1,2})$/', $voucher->code, $matches) && $matches[1] == $matches[2]) {
                                $voucher->code = preg_replace('/'.$matches[0].'$/', '-'.(intval($matches[1]) + 1), $voucher->code);
                            }

                            // Set the new voucher value
                            if ($voucher->reduction_tax) {
                                $voucher->reduction_amount = ($totalReductionValueTi + $values['tax_incl']) - $order->total_products_wt;

                                // Add total shipping amout only if reduction amount > total shipping
                                if ($voucher->free_shipping == 1 && $voucher->reduction_amount >= $order->total_shipping_tax_incl) {
                                    $voucher->reduction_amount -= $order->total_shipping_tax_incl;
                                }
                            } else {
                                $voucher->reduction_amount = ($totalReductionValueTex + $values['tax_excl']) - $order->total_products;

                                // Add total shipping amout only if reduction amount > total shipping
                                if ($voucher->free_shipping == 1 && $voucher->reduction_amount >= $order->total_shipping_tax_excl) {
                                    $voucher->reduction_amount -= $order->total_shipping_tax_excl;
                                }
                            }
                            if ($voucher->reduction_amount <= 0) {
                                continue;
                            }

                            if ($this->context->customer->isGuest()) {
                                $voucher->id_customer = 0;
                            } else {
                                $voucher->id_customer = $order->id_customer;
                            }

                            $voucher->quantity = 1;
                            $voucher->reduction_currency = $order->id_currency;
                            $voucher->quantity_per_user = 1;
                            $voucher->free_shipping = 0;
                            if ($voucher->add()) {
                                // If the voucher has conditions, they are now copied to the new voucher
                                CartRule::copyConditions($cartRule['obj']->id, $voucher->id);
                                $orderLanguage = new Language((int) $order->id_lang);

                                $params = array(
                                    '{voucher_amount}' => Tools::displayPrice($voucher->reduction_amount, $this->context->currency, false),
                                    '{voucher_num}' => $voucher->code,
                                    '{firstname}' => $this->context->customer->firstname,
                                    '{lastname}' => $this->context->customer->lastname,
                                    '{id_order}' => $order->reference,
                                    '{order_name}' => $order->getUniqReference(),
                                );
                                Mail::Send(
                                    (int) $order->id_lang,
                                    'voucher',
                                    Context::getContext()->getTranslator()->trans(
                                        'New voucher for your order %s',
                                        array($order->reference),
                                        'Emails.Subject',
                                        $orderLanguage->locale
                                    ),
                                    $params,
                                    $this->context->customer->email,
                                    $this->context->customer->firstname.' '.$this->context->customer->lastname,
                                    null,
                                    null,
                                    null,
                                    null,
                                    _PS_MAIL_DIR_,
                                    false,
                                    (int) $order->id_shop
                                );
                            }

                            $values['tax_incl'] = $order->total_products_wt - $totalReductionValueTi;
                            $values['tax_excl'] = $order->total_products - $totalReductionValueTex;
                        }
                        $totalReductionValueTi += $values['tax_incl'];
                        $totalReductionValueTex += $values['tax_excl'];

                        $order->addCartRule($cartRule['obj']->id, $cartRule['obj']->name, $values, 0, $cartRule['obj']->free_shipping);

                        if ($idOrderState != Configuration::get('PS_OS_ERROR') && $idOrderState != Configuration::get('PS_OS_CANCELED') && !in_array($cartRule['obj']->id, $cartRuleUsed)) {
                            $cartRuleUsed[] = $cartRule['obj']->id;

                            // Create a new instance of Cart Rule without id_lang, in order to update its quantity
                            $cartRuleToUpdate = new CartRule((int)$cartRule['obj']->id);
                            $cartRuleToUpdate->quantity = max(0, $cartRuleToUpdate->quantity - 1);
                            $cartRuleToUpdate->update();
                        }

                        $cartRulesList[] = array(
                            'voucher_name' => $cartRule['obj']->name,
                            'voucher_reduction' => ($values['tax_incl'] != 0.00 ? '-' : '').Tools::displayPrice($values['tax_incl'], $this->context->currency, false),
                        );
                    }

                    $cartRulesListTxt = '';
                    $cartRulesListHtml = '';
                    if (count($cartRulesList) > 0) {
                        $cartRulesListTxt = $this->getEmailTemplateContent('order_conf_cart_rules.txt', Mail::TYPE_TEXT, $cartRulesList);
                        $cartRulesListHtml = $this->getEmailTemplateContent('order_conf_cart_rules.tpl', Mail::TYPE_HTML, $cartRulesList);
                    }

                    // Specify order id for message
                    $oldMessage = Message::getMessageByCartId((int)$this->context->cart->id);
                    if ($oldMessage && !$oldMessage['private']) {
                        $updateMessage = new Message((int)$oldMessage['id_message']);
                        $updateMessage->id_order = (int)$order->id;
                        $updateMessage->update();

                        // Add this message in the customer thread
                        $customerThread = new CustomerThread();
                        $customerThread->id_contact = 0;
                        $customerThread->id_customer = (int) $order->id_customer;
                        $customerThread->id_shop = (int) $this->context->shop->id;
                        $customerThread->id_order = (int) $order->id;
                        $customerThread->id_lang = (int) $this->context->language->id;
                        $customerThread->email = $this->context->customer->email;
                        $customerThread->status = 'open';
                        $customerThread->token = Tools::passwdGen(12);
                        $customerThread->add();

                        $customerMessage = new CustomerMessage();
                        $customerMessage->id_customer_thread = $customerThread->id;
                        $customerMessage->id_employee = 0;
                        $customerMessage->message = $updateMessage->message;
                        $customerMessage->private = 1;

                        if (!$customerMessage->add()) {
                            $this->errors[] = Tools::displayError('An error occurred while saving message');
                        }
                    }

                    if (self::DEBUG_MODE) {
                        PrestaShopLogger::addLog('PaymentModule::validateOrder - Hook validateOrder is about to be called', 1, null, 'Cart', (int) $idCart, true);
                    }

                    // Hook validate order
                    Hook::exec('actionValidateOrder', array(
                        'cart' => $this->context->cart,
                        'order' => $order,
                        'customer' => $this->context->customer,
                        'currency' => $this->context->currency,
                        'orderStatus' => $orderStatus
                    ));

                    foreach ($this->context->cart->getProducts() as $product) {
                        if ($orderStatus->logable) {
                            ProductSale::addProductSale((int) $product['id_product'], (int) $product['cart_quantity']);
                        }
                    }

                    if (self::DEBUG_MODE) {
                        PrestaShopLogger::addLog('PaymentModule::validateOrder - Order Status is about to be added', 1, null, 'Cart', (int) $idCart, true);
                    }

                    // Set the order status
                    $newHistory = new OrderHistory();
                    $newHistory->id_order = (int) $order->id;
                    $newHistory->changeIdOrderState((int) $idOrderState, $order, true);
                    $newHistory->addWithemail(true, $extraVars);

                    // Switch to back order if needed
                    if (Configuration::get('PS_STOCK_MANAGEMENT') && ($orderDetail->getStockState() || $orderDetail->product_quantity_in_stock <= 0)) {
                        $history = new OrderHistory();
                        $history->id_order = (int) $order->id;
                        $history->changeIdOrderState(Configuration::get($order->valid ? 'PS_OS_OUTOFSTOCK_PAID' : 'PS_OS_OUTOFSTOCK_UNPAID'), $order, true);
                        $history->addWithemail();
                    }

                    unset($orderDetail);

                    // Order is reloaded because the status just changed
                    $order = new Order((int) $order->id);

                    // Send an e-mail to customer (one order = one email)
                    if ($idOrderState != Configuration::get('PS_OS_ERROR') && $idOrderState != Configuration::get('PS_OS_CANCELED') && $this->context->customer->id) {
                        $invoice = new Address((int) $order->id_address_invoice);
                        $delivery = new Address((int) $order->id_address_delivery);
                        $deliveryState = $delivery->id_state ? new State((int) $delivery->id_state) : false;
                        $invoiceState = $invoice->id_state ? new State((int) $invoice->id_state) : false;

                        $data = array(
                        '{firstname}' => $this->context->customer->firstname,
                        '{lastname}' => $this->context->customer->lastname,
                        '{email}' => $this->context->customer->email,
                        '{delivery_block_txt}' => $this->_getFormatedAddress($delivery, "\n"),
                        '{invoice_block_txt}' => $this->_getFormatedAddress($invoice, "\n"),
                        '{delivery_block_html}' => $this->_getFormatedAddress($delivery, '<br />', array(
                            'firstname'    => '<span style="font-weight:bold;">%s</span>',
                            'lastname'    => '<span style="font-weight:bold;">%s</span>'
                        )),
                        '{invoice_block_html}' => $this->_getFormatedAddress($invoice, '<br />', array(
                                'firstname'    => '<span style="font-weight:bold;">%s</span>',
                                'lastname'    => '<span style="font-weight:bold;">%s</span>'
                        )),
                        '{delivery_company}' => $delivery->company,
                        '{delivery_firstname}' => $delivery->firstname,
                        '{delivery_lastname}' => $delivery->lastname,
                        '{delivery_address1}' => $delivery->address1,
                        '{delivery_address2}' => $delivery->address2,
                        '{delivery_city}' => $delivery->city,
                        '{delivery_postal_code}' => $delivery->postcode,
                        '{delivery_country}' => $delivery->country,
                        '{delivery_state}' => $delivery->id_state ? $deliveryState->name : '',
                        '{delivery_phone}' => ($delivery->phone) ? $delivery->phone : $delivery->phone_mobile,
                        '{delivery_other}' => $delivery->other,
                        '{invoice_company}' => $invoice->company,
                        '{invoice_vat_number}' => $invoice->vat_number,
                        '{invoice_firstname}' => $invoice->firstname,
                        '{invoice_lastname}' => $invoice->lastname,
                        '{invoice_address2}' => $invoice->address2,
                        '{invoice_address1}' => $invoice->address1,
                        '{invoice_city}' => $invoice->city,
                        '{invoice_postal_code}' => $invoice->postcode,
                        '{invoice_country}' => $invoice->country,
                        '{invoice_state}' => $invoice->id_state ? $invoiceState->name : '',
                        '{invoice_phone}' => ($invoice->phone) ? $invoice->phone : $invoice->phone_mobile,
                        '{invoice_other}' => $invoice->other,
                        '{order_name}' => $order->getUniqReference(),
                        '{date}' => Tools::displayDate(date('Y-m-d H:i:s'), null, 1),
                        '{carrier}' => ($virtualProduct || !isset($carrier->name)) ? Tools::displayError('No carrier') : $carrier->name,
                        '{payment}' => Tools::substr($order->payment, 0, 32),
                        '{products}' => $productListHtml,
                        '{products_txt}' => $productListTxt,
                        '{discounts}' => $cartRulesListHtml,
                        '{discounts_txt}' => $cartRulesListTxt,
                        '{total_paid}' => Tools::displayPrice($order->total_paid, $this->context->currency, false),
                        '{total_products}' => Tools::displayPrice(Product::getTaxCalculationMethod() == PS_TAX_EXC ? $order->total_products : $order->total_products_wt, $this->context->currency, false),
                        '{total_discounts}' => Tools::displayPrice($order->total_discounts, $this->context->currency, false),
                        '{total_shipping}' => Tools::displayPrice($order->total_shipping, $this->context->currency, false),
                        '{total_wrapping}' => Tools::displayPrice($order->total_wrapping, $this->context->currency, false),
                        '{total_tax_paid}' => Tools::displayPrice(($order->total_products_wt - $order->total_products) + ($order->total_shipping_tax_incl - $order->total_shipping_tax_excl), $this->context->currency, false));

                        if (is_array($extraVars)) {
                            $data = array_merge($data, $extraVars);
                        }

                        // Join PDF invoice
                        if ((int) Configuration::get('PS_INVOICE') && $orderStatus->invoice && $order->invoice_number) {
                            $orderInvoiceList = $order->getInvoicesCollection();
                            Hook::exec('actionPDFInvoiceRender', array('order_invoice_list' => $orderInvoiceList));
                            $pdf = new PDF($orderInvoiceList, PDF::TEMPLATE_INVOICE, $this->context->smarty);
                            $fileAttachement['content'] = $pdf->render(false);
                            $fileAttachement['name'] = Configuration::get('PS_INVOICE_PREFIX', (int) $order->id_lang, null, $order->id_shop).sprintf('%06d', $order->invoice_number).'.pdf';
                            $fileAttachement['mime'] = 'application/pdf';
                        } else {
                            $fileAttachement = null;
                        }

                        if (self::DEBUG_MODE) {
                            PrestaShopLogger::addLog('PaymentModule::validateOrder - Mail is about to be sent', 1, null, 'Cart', (int) $idCart, true);
                        }

                        $orderLanguage = new Language((int) $order->id_lang);

                        if (Validate::isEmail($this->context->customer->email)) {
                            Mail::Send(
                                (int) $order->id_lang,
                                'order_conf',
                                Context::getContext()->getTranslator()->trans(
                                    'Order confirmation',
                                    array(),
                                    'Emails.Subject',
                                    $orderLanguage->locale
                                ),
                                $data,
                                $this->context->customer->email,
                                $this->context->customer->firstname.' '.$this->context->customer->lastname,
                                null,
                                null,
                                $fileAttachement,
                                null,
                                _PS_MAIL_DIR_,
                                false,
                                (int) $order->id_shop
                            );
                        }
                    }

                    // updates stock in shops
                    if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                        $productList = $order->getProducts();
                        foreach ($productList as $product) {
                            // if the available quantities depends on the physical stock
                            if (StockAvailable::dependsOnStock($product['product_id'])) {
                                // synchronizes
                                StockAvailable::synchronize($product['product_id'], $order->id_shop);
                            }
                        }
                    }

                    $order->updateOrderDetailTax();
                } else {
                    $error = Tools::displayError('Order creation failed');
                    PrestaShopLogger::addLog($error, 4, '0000002', 'Cart', intval($order->id_cart));
                    die($error);
                }
            } // End foreach $order_detail_list

            // Use the last order as currentOrder
            if (isset($order) && $order->id) {
                $this->currentOrder = (int) $order->id;
            }

            if (self::DEBUG_MODE) {
                PrestaShopLogger::addLog('PaymentModule::validateOrder - End of validateOrder', 1, null, 'Cart', (int) $idCart, true);
            }

            return true;
        } else {
            $error = Tools::displayError('Cart cannot be loaded or an order has already been placed using this cart');
            PrestaShopLogger::addLog($error, 4, '0000001', 'Cart', intval($this->context->cart->id));
            die($error);
        }
    }

    /**
     * @param Address $theAddress
     *
     * @deprecated 1.7.0
     */
    protected function _getTxtFormatedAddress($theAddress)
    {
        return $this->getTxtFormatedAddress($theAddress);
    }

    /**
     * @param Address $theAddress that needs to be txt formated
     *
     * @return String the txt formated address block
     *
     * @since 1.7.0
     */
    protected function getTxtFormatedAddress($theAddress)
    {
        $adrFields = AddressFormat::getOrderedAddressFields($theAddress->id_country, false, true);
        $rValues = array();
        foreach ($adrFields as $fieldsLine) {
            $tmpValues = array();
            foreach (explode(' ', $fieldsLine) as $fieldItem) {
                $fieldItem = trim($fieldItem);
                $tmpValues[] = $theAddress->{$fieldItem};
            }
            $rValues[] = implode(' ', $tmpValues);
        }

        $out = implode("\n", $rValues);

        return $out;
    }

    /**
     * @param Address $theAddress
     * @param         $lineSep
     * @param array   $fieldsStyle
     *
     * @deprecated 1.7.0
     */
    protected function _getFormatedAddress(\Address $theAddress, $lineSep, $fieldsStyle = array())
    {
        return $this->getFormattedAddress($theAddress, $lineSep, $fieldsStyle);
    }

    /**
     * @param Object Address $the_address that needs to be txt formated
     *
     * @return String the txt formated address block
     *
     * @since 1.7.0
     */
    protected function getFormattedAddress(\Address $theAddress, $lineSep, $fieldsStyle = array())
    {
        return AddressFormat::generateAddress($theAddress, array('avoid' => array()), $lineSep, ' ', $fieldsStyle);
    }

    /**
     * @param int $currentIdCurrency optional but on 1.5 it will be REQUIRED
     *
     * @return Currency|bool Currency
     *                       `false` if not found
     */
    public function getCurrency($currentIdCurrency = null)
    {
        if (!(int) $currentIdCurrency) {
            $currentIdCurrency = Context::getContext()->currency->id;
        }

        if (!$this->currencies) {
            return false;
        }
        if ($this->currencies_mode == 'checkbox') {
            $currencies = Currency::getPaymentCurrencies($this->id);

            return $currencies;
        } elseif ($this->currencies_mode == 'radio') {
            $currencies = Currency::getPaymentCurrenciesSpecial($this->id);
            $currency = $currencies['id_currency'];
            if ($currency == -1) {
                $idCurrency = (int) $currentIdCurrency;
            } elseif ($currency == -2) {
                $idCurrency = (int) Configuration::get('PS_CURRENCY_DEFAULT');
            } else {
                $idCurrency = $currency;
            }
        }
        if (!isset($idCurrency) || empty($idCurrency)) {
            return false;
        }
        $currency = new Currency((int) $idCurrency);

        return $currency;
    }

    /**
     * Allows specified payment modules to be used by a specific currency
     *
     * @param int   $idCurrency
     * @param array $idModuleList
     *
     * @return bool
     *
     * @since 1.4.5
     */
    public static function addCurrencyPermissions($idCurrency, array $idModuleList = array())
    {
        $values = '';
        if (count($idModuleList) == 0) {
            // fetch all installed module ids
            $modules = PaymentModuleCore::getInstalledPaymentModules();
            foreach ($modules as $module) {
                $idModuleList[] = $module['id_module'];
            }
        }

        foreach ($idModuleList as $idModule) {
            $values .= '('.(int) $idModule.','.(int) $idCurrency.'),';
        }

        if (!empty($values)) {
            return Db::getInstance()->execute('
            INSERT INTO `'._DB_PREFIX_.'module_currency` (`id_module`, `id_currency`)
            VALUES '.rtrim($values, ','));
        }

        return true;
    }

    /**
     * List all installed and active payment modules
     * @see Module::getPaymentModules() if you need a list of module related to the user context
     *
     * @return array module informations
     *
     * @since 1.4.5
     */
    public static function getInstalledPaymentModules()
    {
        $hookPayment = 'Payment';
        if (Db::getInstance()->getValue('SELECT `id_hook` FROM `'._DB_PREFIX_.'hook` WHERE `name` = \'paymentOptions\'')) {
            $hookPayment = 'paymentOptions';
        }

        return Db::getInstance()->executeS('
        SELECT DISTINCT m.`id_module`, h.`id_hook`, m.`name`, hm.`position`
        FROM `'._DB_PREFIX_.'module` m
        LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`'
        .Shop::addSqlRestriction(false, 'hm').'
        LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
        INNER JOIN `'._DB_PREFIX_.'module_shop` ms ON (m.`id_module` = ms.`id_module` AND ms.id_shop='.(int)Context::getContext()->shop->id.')
        WHERE h.`name` = \''.pSQL($hookPayment).'\'');
    }

    /**
     * @param string $moduleName
     *
     * @return bool
     */
    public static function preCall($moduleName)
    {
        if (!parent::preCall($moduleName)) {
            return false;
        }

        if (($moduleInstance = Module::getInstanceByName($moduleName))) {
            /** @var PaymentModule $moduleInstance */
            if (!$moduleInstance->currencies || ($moduleInstance->currencies && count(Currency::checkPaymentCurrencies($moduleInstance->id)))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Fetch the content of $template_name inside the folder
     * current_theme/mails/current_iso_lang/ if found, otherwise in
     * mails/current_iso_lang
     *
     * @param string $templateName template name with extension
     * @param int    $mailType     Mail::TYPE_HTML or Mail::TYPE_TEXT
     * @param array  $var          sent to smarty as 'list'
     *
     * @return string
     */
    protected function getEmailTemplateContent($templateName, $mailType, $var)
    {
        $emailConfiguration = Configuration::get('PS_MAIL_TYPE');
        if ($emailConfiguration != $mailType && $emailConfiguration != Mail::TYPE_BOTH) {
            return '';
        }

        $themeTemplatePath = _PS_THEME_DIR_.'mails'.DIRECTORY_SEPARATOR.$this->context->language->iso_code.DIRECTORY_SEPARATOR.$templateName;
        $defaultMailTemplatePath = _PS_MAIL_DIR_.$this->context->language->iso_code.DIRECTORY_SEPARATOR.$templateName;

        if (Tools::file_exists_cache($themeTemplatePath)) {
            $defaultMailTemplatePath = $themeTemplatePath;
        }

        if (Tools::file_exists_cache($defaultMailTemplatePath)) {
            $this->context->smarty->assign('list', $var);

            return $this->context->smarty->fetch($defaultMailTemplatePath);
        }

        return '';
    }
}
