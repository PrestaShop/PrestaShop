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

use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;

/**
 * @property Cart $object
 */
class AdminCartsControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'cart';
        $this->className = 'Cart';
        $this->lang = false;
        $this->explicitSelect = true;

        parent::__construct();

        $this->addRowAction('view');
        $this->addRowAction('delete');
        $this->allow_export = true;
        $this->_orderWay = 'DESC';

        $this->_select = '
            CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) `customer`,
            a.id_cart total,
            ca.name carrier,
            o.id_order,
            IF (
		        IFNULL(o.id_order, \'' . $this->trans('Non ordered', [], 'Admin.Orderscustomers.Feature') . '\') = \'' . $this->trans('Non ordered', [], 'Admin.Orderscustomers.Feature') . '\',
		        IF(TIME_TO_SEC(TIMEDIFF(\'' . pSQL(date('Y-m-d H:i:00', time())) . '\', a.`date_add`)) > 86400, \'' . $this->trans('Abandoned cart', [], 'Admin.Orderscustomers.Feature') . '\',
		        \'' . $this->trans('Non ordered', [], 'Admin.Orderscustomers.Feature') . '\'),
		        o.id_order
            ) AS status,
		    IF(o.id_order, 1, 0) badge_success,
		    IF(o.id_order, 0, 1) badge_danger,
		    IF(co.id_guest, 1, 0) id_guest';
        $this->_join = 'LEFT JOIN ' . _DB_PREFIX_ . 'customer c ON (c.id_customer = a.id_customer)
		LEFT JOIN ' . _DB_PREFIX_ . 'currency cu ON (cu.id_currency = a.id_currency)
		LEFT JOIN ' . _DB_PREFIX_ . 'carrier ca ON (ca.id_carrier = a.id_carrier)
		LEFT JOIN ' . _DB_PREFIX_ . 'orders o ON (o.id_cart = a.id_cart)
		LEFT JOIN (
            SELECT DISTINCT `id_guest`
            FROM `' . _DB_PREFIX_ . 'connections`
            WHERE
                TIME_TO_SEC(TIMEDIFF(\'' . pSQL(date('Y-m-d H:i:00', time())) . '\', `date_add`)) < 1800
       ) AS co ON co.`id_guest` = a.`id_guest`';

        if (Tools::getValue('action') && Tools::getValue('action') == 'filterOnlyAbandonedCarts') {
            $this->_having = 'status = \'' . $this->trans('Abandoned cart', [], 'Admin.Orderscustomers.Feature') . '\'';
        } else {
            $this->_use_found_rows = false;
        }

        $this->fields_list = [
            'id_cart' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ],
            'status' => [
                'title' => $this->trans('Order ID', [], 'Admin.Orderscustomers.Feature'),
                'align' => 'text-center',
                'badge_danger' => true,
                'havingFilter' => true,
            ],
            'customer' => [
                'title' => $this->trans('Customer', [], 'Admin.Global'),
                'filter_key' => 'c!lastname',
            ],
            'total' => [
                'title' => $this->trans('Total', [], 'Admin.Global'),
                'callback' => 'getOrderTotalUsingTaxCalculationMethod',
                'orderby' => false,
                'search' => false,
                'align' => 'text-right',
                'badge_success' => true,
            ],
            'carrier' => [
                'title' => $this->trans('Carrier', [], 'Admin.Shipping.Feature'),
                'align' => 'text-left',
                'filter_key' => 'ca!name',
            ],
            'date_add' => [
                'title' => $this->trans('Date', [], 'Admin.Global'),
                'align' => 'text-left',
                'type' => 'datetime',
                'class' => 'fixed-width-lg',
                'filter_key' => 'a!date_add',
            ],
        ];

        if (Configuration::get('PS_GUEST_CHECKOUT_ENABLED')) {
            $this->fields_list['id_guest'] = [
                'title' => $this->trans('Online', [], 'Admin.Global'),
                'align' => 'text-center',
                'type' => 'bool',
                'havingFilter' => true,
                'class' => 'fixed-width-xs',
            ];
        }

        $this->shopLinkType = 'shop';

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash',
            ],
        ];
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['export_cart'] = [
                'href' => self::$currentIndex . '&exportcart&token=' . $this->token,
                'desc' => $this->trans('Export carts', [], 'Admin.Orderscustomers.Feature'),
                'icon' => 'process-icon-export',
            ];
        }

        parent::initPageHeaderToolbar();
    }

    public function renderKpis()
    {
        $time = time();
        $kpis = [];

        /* The data generation is located in AdminStatsControllerCore */
        $helper = new HelperKpi();
        $helper->id = 'box-conversion-rate';
        $helper->icon = 'icon-sort-by-attributes-alt';
        //$helper->chart = true;
        $helper->color = 'color1';
        $helper->title = $this->trans('Conversion Rate', [], 'Admin.Global');
        $helper->subtitle = $this->trans('30 days', [], 'Admin.Global');
        if (ConfigurationKPI::get('CONVERSION_RATE') !== false) {
            $helper->value = ConfigurationKPI::get('CONVERSION_RATE');
        }
        if (ConfigurationKPI::get('CONVERSION_RATE_CHART') !== false) {
            $helper->data = ConfigurationKPI::get('CONVERSION_RATE_CHART');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats') . '&ajax=1&action=getKpi&kpi=conversion_rate';
        $helper->refresh = (bool) (ConfigurationKPI::get('CONVERSION_RATE_EXPIRE') < $time);
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-carts';
        $helper->icon = 'icon-shopping-cart';
        $helper->color = 'color2';
        $helper->title = $this->trans('Abandoned Carts', [], 'Admin.Orderscustomers.Feature');
        $date_from = date(Context::getContext()->language->date_format_lite, strtotime('-2 day'));
        $date_to = date(Context::getContext()->language->date_format_lite, strtotime('-1 day'));
        $helper->subtitle = $this->trans('From %date1% to %date2%', ['%date1%' => $date_from, '%date2%' => $date_to], 'Admin.Orderscustomers.Feature');
        $helper->href = $this->context->link->getAdminLink('AdminCarts') . '&action=filterOnlyAbandonedCarts';
        if (ConfigurationKPI::get('ABANDONED_CARTS') !== false) {
            $helper->value = ConfigurationKPI::get('ABANDONED_CARTS');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats') . '&ajax=1&action=getKpi&kpi=abandoned_cart';
        $helper->refresh = (bool) (ConfigurationKPI::get('ABANDONED_CARTS_EXPIRE') < $time);
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-average-order';
        $helper->icon = 'icon-money';
        $helper->color = 'color3';
        $helper->title = $this->trans('Average Order Value', [], 'Admin.Orderscustomers.Feature');
        $helper->subtitle = $this->trans('30 days', [], 'Admin.Global');
        if (ConfigurationKPI::get('AVG_ORDER_VALUE') !== false) {
            $helper->value = $this->trans('%amount% tax excl.', ['%amount%' => ConfigurationKPI::get('AVG_ORDER_VALUE')], 'Admin.Orderscustomers.Feature');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats') . '&ajax=1&action=getKpi&kpi=average_order_value';
        $helper->refresh = ConfigurationKPI::get('AVG_ORDER_VALUE_EXPIRE') < $time;
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-net-profit-visitor';
        $helper->icon = 'icon-user';
        $helper->color = 'color4';
        $helper->title = $this->trans('Net Profit per Visitor', [], 'Admin.Orderscustomers.Feature');
        $helper->subtitle = $this->trans('30 days', [], 'Admin.Global');
        if (ConfigurationKPI::get('NETPROFIT_VISITOR') !== false) {
            $helper->value = ConfigurationKPI::get('NETPROFIT_VISITOR');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats') . '&ajax=1&action=getKpi&kpi=netprofit_visitor';
        $helper->refresh = (bool) (ConfigurationKPI::get('NETPROFIT_VISITOR_EXPIRE') < $time);
        $kpis[] = $helper->generate();

        $helper = new HelperKpiRow();
        $helper->kpis = $kpis;

        return $helper->generate();
    }

    /**
     * @return string|void
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws LocalizationException
     */
    public function renderView()
    {
        if (!($cart = $this->loadObject(true))) {
            return;
        }
        /** @var Cart $cart */
        $customer = new Customer($cart->id_customer);
        $currency = new Currency($cart->id_currency);
        $this->context->cart = $cart;
        $this->context->currency = $currency;
        $this->context->customer = $customer;
        $this->toolbar_title = $this->trans('Cart #%ID%', ['%ID%' => $this->context->cart->id], 'Admin.Orderscustomers.Feature');
        $products = $cart->getProducts();
        $summary = $cart->getSummaryDetails();

        /* Display order information */
        $id_order = (int) Order::getIdByCartId($cart->id);
        $order = new Order($id_order);
        if (Validate::isLoadedObject($order)) {
            $tax_calculation_method = $order->getTaxCalculationMethod();
            $id_shop = (int) $order->id_shop;
        } else {
            $id_shop = (int) $cart->id_shop;
            $tax_calculation_method = Group::getPriceDisplayMethod(Group::getCurrent()->id);
        }

        if ($tax_calculation_method == PS_TAX_EXC) {
            $total_products = $summary['total_products'];
            $total_discounts = $summary['total_discounts_tax_exc'];
            $total_wrapping = $summary['total_wrapping_tax_exc'];
            $total_price = $summary['total_price_without_tax'];
            $total_shipping = $summary['total_shipping_tax_exc'];
        } else {
            $total_products = $summary['total_products_wt'];
            $total_discounts = $summary['total_discounts'];
            $total_wrapping = $summary['total_wrapping'];
            $total_price = $summary['total_price'];
            $total_shipping = $summary['total_shipping'];
        }
        foreach ($products as &$product) {
            if ($tax_calculation_method == PS_TAX_EXC) {
                $product['product_price'] = $product['price'];
                $product['product_total'] = $product['total'];
            } else {
                $product['product_price'] = $product['price_wt'];
                $product['product_total'] = $product['total_wt'];
            }
            $image = [];
            if (isset($product['id_product_attribute']) && (int) $product['id_product_attribute']) {
                $image = Db::getInstance()->getRow('SELECT id_image FROM ' . _DB_PREFIX_ . 'product_attribute_image WHERE id_product_attribute = ' . (int) $product['id_product_attribute']);
            }
            if (!isset($image['id_image'])) {
                $image = Db::getInstance()->getRow('SELECT id_image FROM ' . _DB_PREFIX_ . 'image WHERE id_product = ' . (int) $product['id_product'] . ' AND cover = 1');
            }

            $product['qty_in_stock'] = StockAvailable::getQuantityAvailableByProduct($product['id_product'], isset($product['id_product_attribute']) ? $product['id_product_attribute'] : null, (int) $id_shop);

            $image_product = new Image($image['id_image']);
            $product['image'] = (isset($image['id_image']) ? ImageManager::thumbnail(_PS_IMG_DIR_ . 'p/' . $image_product->getExistingImgPath() . '.jpg', 'product_mini_' . (int) $product['id_product'] . (isset($product['id_product_attribute']) ? '_' . (int) $product['id_product_attribute'] : '') . '.jpg', 45, 'jpg') : '--');

            $customized_datas = Product::getAllCustomizedDatas($this->context->cart->id, null, true, null, (int) $product['id_customization']);
            $this->context->cart->setProductCustomizedDatas($product, $customized_datas);
            if ($customized_datas) {
                Product::addProductCustomizationPrice($product, $customized_datas);
            }
        }

        $helper = new HelperKpi();
        $helper->id = 'box-kpi-cart';
        $helper->icon = 'icon-shopping-cart';
        $helper->color = 'color1';
        $helper->title = $this->trans('Total cart', [], 'Admin.Orderscustomers.Feature');
        $helper->subtitle = $this->trans('Cart #%ID%', ['%ID%' => $cart->id], 'Admin.Orderscustomers.Feature');
        $helper->value = $this->context->getCurrentLocale()->formatPrice($total_price, $currency->iso_code);
        $kpi = $helper->generate();

        $this->tpl_view_vars = [
            'kpi' => $kpi,
            'products' => $products,
            'discounts' => $cart->getCartRules(),
            'order' => $order,
            'cart' => $cart,
            'currency' => $currency,
            'customer' => $customer,
            'customer_stats' => $customer->getStats(),
            'total_products' => $total_products,
            'total_discounts' => $total_discounts,
            'total_wrapping' => $total_wrapping,
            'total_price' => $total_price,
            'total_shipping' => $total_shipping,
            'tax_calculation_method' => $tax_calculation_method,
        ];

        return parent::renderView();
    }

    public function ajaxPreProcess()
    {
        if ($this->access('edit')) {
            $id_customer = (int) Tools::getValue('id_customer');
            $customer = new Customer((int) $id_customer);
            $this->context->customer = $customer;
            $id_cart = (int) Tools::getValue('id_cart');
            if (!$id_cart) {
                $id_cart = $customer->getLastEmptyCart(false);
            }
            $this->context->cart = new Cart((int) $id_cart);

            if (!$this->context->cart->id) {
                $this->context->cart->recyclable = false;
                $this->context->cart->gift = false;
            }

            if (!$this->context->cart->id_customer) {
                $this->context->cart->id_customer = $id_customer;
            }
            if (Validate::isLoadedObject($this->context->cart) && $this->context->cart->OrderExists()) {
                return;
            }
            if (!$this->context->cart->secure_key) {
                $this->context->cart->secure_key = $this->context->customer->secure_key;
            }
            if (!$this->context->cart->id_shop) {
                $this->context->cart->id_shop = (int) $this->context->shop->id;
            }
            if (!$this->context->cart->id_lang) {
                $this->context->cart->id_lang = (($id_lang = (int) Tools::getValue('id_lang')) ? $id_lang : Configuration::get('PS_LANG_DEFAULT'));
            }
            if (!$this->context->cart->id_currency) {
                $this->context->cart->id_currency = (($id_currency = (int) Tools::getValue('id_currency')) ? $id_currency : Currency::getDefaultCurrencyId());
            }

            $addresses = $customer->getAddresses((int) $this->context->cart->id_lang);
            $id_address_delivery = (int) Tools::getValue('id_address_delivery');
            $id_address_invoice = (int) Tools::getValue('id_address_delivery');

            if (!$this->context->cart->id_address_invoice && isset($addresses[0])) {
                $this->context->cart->id_address_invoice = (int) $addresses[0]['id_address'];
            } elseif ($id_address_invoice) {
                $this->context->cart->id_address_invoice = (int) $id_address_invoice;
            }
            if (!$this->context->cart->id_address_delivery && isset($addresses[0])) {
                $this->context->cart->id_address_delivery = $addresses[0]['id_address'];
            } elseif ($id_address_delivery) {
                $this->context->cart->id_address_delivery = (int) $id_address_delivery;
            }
            $this->context->cart->setNoMultishipping();
            $this->context->cart->save();
            $currency = new Currency((int) $this->context->cart->id_currency);
            $this->context->currency = $currency;
        }
    }

    public function ajaxProcessDeleteProduct()
    {
        if ($this->access('edit')) {
            $errors = [];
            if ((!$id_product = (int) Tools::getValue('id_product')) || !Validate::isInt($id_product)) {
                $errors[] = $this->trans('Invalid product', [], 'Admin.Catalog.Notification');
            }
            if (($id_product_attribute = (int) Tools::getValue('id_product_attribute')) && !Validate::isInt($id_product_attribute)) {
                $errors[] = $this->trans('Invalid combination', [], 'Admin.Catalog.Notification');
            }
            if (count($errors)) {
                die(json_encode($errors));
            }
            if ($this->context->cart->deleteProduct($id_product, $id_product_attribute, (int) Tools::getValue('id_customization'))) {
                echo json_encode($this->ajaxReturnVars());
            }
        }
    }

    public function ajaxProcessUpdateCustomizationFields()
    {
        $errors = [];
        if ($this->access('edit')) {
            $errors = [];
            if (Tools::getValue('only_display') != 1) {
                if (!$this->context->cart->id || (!$id_product = (int) Tools::getValue('id_product'))) {
                    return;
                }
                $product = new Product((int) $id_product);
                if (!$customization_fields = $product->getCustomizationFieldIds()) {
                    return;
                }
                foreach ($customization_fields as $customization_field) {
                    $field_id = 'customization_' . $id_product . '_' . $customization_field['id_customization_field'];
                    if ($customization_field['type'] == Product::CUSTOMIZE_TEXTFIELD) {
                        if (!Tools::getValue($field_id)) {
                            if ($customization_field['required']) {
                                $errors[] = $this->trans('Please fill in all the required fields.', [], 'Admin.Notifications.Error');
                            }

                            continue;
                        }
                        if (!Validate::isMessage(Tools::getValue($field_id))) {
                            $errors[] = $this->trans('Invalid message.', [], 'Admin.Notifications.Error');
                        }
                        $this->context->cart->addTextFieldToProduct((int) $product->id, (int) $customization_field['id_customization_field'], Product::CUSTOMIZE_TEXTFIELD, Tools::getValue($field_id));
                    } elseif ($customization_field['type'] == Product::CUSTOMIZE_FILE) {
                        if (!isset($_FILES[$field_id]) || !isset($_FILES[$field_id]['tmp_name']) || empty($_FILES[$field_id]['tmp_name'])) {
                            if ($customization_field['required']) {
                                $errors[] = $this->trans('Please fill in all the required fields.', [], 'Admin.Notifications.Error');
                            }

                            continue;
                        }
                        if ($error = ImageManager::validateUpload($_FILES[$field_id], (int) Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE'))) {
                            $errors[] = $error;
                        }
                        if (!($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES[$field_id]['tmp_name'], $tmp_name)) {
                            $errors[] = $this->trans('An error occurred during the image upload process.', [], 'Admin.Catalog.Notification');
                        }
                        $file_name = md5(uniqid((string) mt_rand(0, mt_getrandmax()), true));
                        if (!ImageManager::resize($tmp_name, _PS_UPLOAD_DIR_ . $file_name)) {
                            continue;
                        } elseif (!ImageManager::resize($tmp_name, _PS_UPLOAD_DIR_ . $file_name . '_small', (int) Configuration::get('PS_PRODUCT_PICTURE_WIDTH'), (int) Configuration::get('PS_PRODUCT_PICTURE_HEIGHT'))) {
                            $errors[] = $this->trans('An error occurred during the image upload process.', [], 'Admin.Catalog.Notification');
                        } else {
                            $this->context->cart->addPictureToProduct((int) $product->id, (int) $customization_field['id_customization_field'], Product::CUSTOMIZE_FILE, $file_name);
                        }
                        unlink($tmp_name);
                    }
                }
            }
            $this->setMedia(false);
            $this->initFooter();
            $this->context->smarty->assign(['customization_errors' => implode('<br />', $errors),
                'css_files' => $this->css_files, ]);

            return $this->smartyOutputContent('controllers/orders/form_customization_feedback.tpl');
        }
    }

    public function ajaxProcessUpdateQty()
    {
        if ($this->access('edit')) {
            $errors = [];
            if (!$this->context->cart->id) {
                return;
            }
            $qty = Tools::getValue('qty');
            $id_product = (int) Tools::getValue('id_product');
            if ($this->context->cart->OrderExists()) {
                $errors[] = $this->trans('An order has already been placed with this cart.', [], 'Admin.Catalog.Notification');
            } elseif (!$id_product || !Validate::isLoadedObject($product = new Product((int) $id_product, true, $this->context->language->id))) {
                $errors[] = $this->trans('Invalid product', [], 'Admin.Catalog.Notification');
            } elseif (!$qty || $qty == 0) {
                $errors[] = $this->trans('Invalid quantity', [], 'Admin.Catalog.Notification');
            }

            // Don't try to use a product if not instanciated before due to errors
            $id_customization = (int) Tools::getValue('id_customization', 0);
            $id_product_attribute = Tools::getValue('id_product_attribute');
            if (isset($product) && $product->id) {
                if ($id_product_attribute != 0) {
                    if (!Product::isAvailableWhenOutOfStock($product->out_of_stock) && !ProductAttribute::checkAttributeQty((int) $id_product_attribute, (int) $qty)) {
                        $errors[] = $this->trans('There are not enough products in stock.', [], 'Admin.Catalog.Notification');
                    }
                } elseif (!$product->checkQty((int) $qty)) {
                    $errors[] = $this->trans('There are not enough products in stock.', [], 'Admin.Catalog.Notification');
                }
                if (!$id_customization && !$product->hasAllRequiredCustomizableFields()) {
                    $errors[] = $this->trans('Please fill in all the required fields.', [], 'Admin.Notifications.Error');
                }
                $this->context->cart->save();
            } else {
                $errors[] = $this->trans('This product cannot be added to the cart.', [], 'Admin.Catalog.Notification');
            }

            if (!count($errors)) {
                if ((int) $qty < 0) {
                    $qty = str_replace('-', '', $qty);
                    $operator = 'down';
                } else {
                    $operator = 'up';
                }

                if (!($qty_upd = $this->context->cart->updateQty($qty, $id_product, (int) $id_product_attribute, $id_customization, $operator))) {
                    $errors[] = $this->trans('You already have the maximum quantity available for this product.', [], 'Admin.Catalog.Notification');
                } elseif ($qty_upd < 0 && isset($product)) {
                    $minimal_qty = $id_product_attribute ? ProductAttribute::getAttributeMinimalQty((int) $id_product_attribute) : $product->minimal_quantity;
                    $errors[] = $this->trans('You must add a minimum quantity of %d', [$minimal_qty], 'Admin.Orderscustomers.Notification');
                }
            }

            echo json_encode(array_merge($this->ajaxReturnVars(), ['errors' => $errors]));
        }
    }

    public function ajaxProcessUpdateDeliveryOption()
    {
        if ($this->access('edit')) {
            $delivery_option = Tools::getValue('delivery_option');
            if ($delivery_option !== false) {
                $this->context->cart->setDeliveryOption([$this->context->cart->id_address_delivery => $delivery_option]);
            }
            $recyclable = (int) Tools::getValue('recyclable');
            if (Validate::isBool($recyclable)) {
                $this->context->cart->recyclable = $recyclable;
            }
            $gift = (int) Tools::getValue('gift');
            if (Validate::isBool($gift)) {
                $this->context->cart->gift = $gift;
            }
            if (Validate::isMessage(($gift_message = pSQL(Tools::getValue('gift_message'))))) {
                $this->context->cart->gift_message = $gift_message;
            }
            $this->context->cart->save();
            echo json_encode($this->ajaxReturnVars());
        }
    }

    public function ajaxProcessUpdateOrderMessage()
    {
        if ($this->access('edit')) {
            $id_message = false;
            if ($old_message = Message::getMessageByCartId((int) $this->context->cart->id)) {
                $id_message = $old_message['id_message'];
            }
            $message = new Message((int) $id_message);
            if ($message_content = Tools::getValue('message')) {
                if (Validate::isMessage($message_content)) {
                    $message->message = $message_content;
                    $message->id_cart = (int) $this->context->cart->id;
                    $message->id_customer = (int) $this->context->cart->id_customer;
                    $message->save();
                }
            } elseif (Validate::isLoadedObject($message)) {
                $message->delete();
            }
            echo json_encode($this->ajaxReturnVars());
        }
    }

    public function ajaxProcessUpdateCurrency()
    {
        if ($this->access('edit')) {
            $currency = new Currency((int) Tools::getValue('id_currency'));
            if (Validate::isLoadedObject($currency) && !$currency->deleted && $currency->active) {
                $this->context->cart->id_currency = (int) $currency->id;
                $this->context->currency = $currency;
                $this->context->cart->save();
            }
            echo json_encode($this->ajaxReturnVars());
        }
    }

    public function ajaxProcessUpdateLang()
    {
        if ($this->access('edit')) {
            $lang = new Language((int) Tools::getValue('id_lang'));
            if (Validate::isLoadedObject($lang) && $lang->active) {
                $this->context->cart->id_lang = (int) $lang->id;
                $this->context->cart->save();
            }
            echo json_encode($this->ajaxReturnVars());
        }
    }

    public function ajaxProcessDuplicateOrder()
    {
        if ($this->access('edit')) {
            $errors = [];
            if (!$id_order = Tools::getValue('id_order')) {
                $errors[] = $this->trans('Invalid order', [], 'Admin.Orderscustomers.Notification');
            }
            $cart = Cart::getCartByOrderId($id_order);
            $new_cart = $cart->duplicate();
            if (!$new_cart || !Validate::isLoadedObject($new_cart['cart'])) {
                $errors[] = $this->trans('The order cannot be renewed.', [], 'Admin.Orderscustomers.Notification');
            } elseif (!$new_cart['success']) {
                $errors[] = $this->trans('The order cannot be renewed.', [], 'Admin.Orderscustomers.Notification');
            } else {
                $this->context->cart = $new_cart['cart'];
                echo json_encode($this->ajaxReturnVars());
            }
        }
    }

    public function ajaxProcessDeleteVoucher()
    {
        if ($this->access('edit')) {
            if ($this->context->cart->removeCartRule((int) Tools::getValue('id_cart_rule'))) {
                echo json_encode($this->ajaxReturnVars());
            }
        }
    }

    public function ajaxProcessupdateFreeShipping()
    {
        if ($this->access('edit')) {
            if (!$id_cart_rule = CartRule::getIdByCode(CartRule::BO_ORDER_CODE_PREFIX . (int) $this->context->cart->id)) {
                $cart_rule = new CartRule();
                $cart_rule->code = CartRule::BO_ORDER_CODE_PREFIX . (int) $this->context->cart->id;
                $cart_rule->name = [Configuration::get('PS_LANG_DEFAULT') => $this->trans('Free Shipping', [], 'Admin.Orderscustomers.Feature')];
                $cart_rule->id_customer = (int) $this->context->cart->id_customer;
                $cart_rule->free_shipping = true;
                $cart_rule->quantity = 1;
                $cart_rule->quantity_per_user = 1;
                $cart_rule->minimum_amount_currency = (int) $this->context->cart->id_currency;
                $cart_rule->reduction_currency = (int) $this->context->cart->id_currency;
                $cart_rule->date_from = date('Y-m-d H:i:s', time());
                $cart_rule->date_to = date('Y-m-d H:i:s', time() + 24 * 36000);
                $cart_rule->active = true;
                $cart_rule->add();
            } else {
                $cart_rule = new CartRule((int) $id_cart_rule);
            }

            $this->context->cart->removeCartRule((int) $cart_rule->id);
            if (Tools::getValue('free_shipping')) {
                $this->context->cart->addCartRule((int) $cart_rule->id);
            }

            echo json_encode($this->ajaxReturnVars());
        }
    }

    public function ajaxProcessAddVoucher()
    {
        if ($this->access('edit')) {
            $errors = [];
            $id_cart_rule = Tools::getValue('id_cart_rule');
            if (!$id_cart_rule) {
                $errors[] = $this->trans('Invalid voucher.', [], 'Admin.Catalog.Notification');
            } else {
                $cart_rule = new CartRule((int) $id_cart_rule);
                if (!Validate::isLoadedObject($cart_rule)) {
                    $errors[] = $this->trans('Invalid voucher.', [], 'Admin.Catalog.Notification');
                } elseif ($err = $cart_rule->checkValidity($this->context)) {
                    $errors[] = $err;
                }
                if (!count($errors)) {
                    if (!$this->context->cart->addCartRule((int) $cart_rule->id)) {
                        $errors[] = $this->trans('Can\'t add the voucher.', [], 'Admin.Advparameters.Notification');
                    }
                }
            }
            echo json_encode(array_merge($this->ajaxReturnVars(), ['errors' => $errors]));
        }
    }

    public function ajaxProcessUpdateAddress()
    {
        if ($this->access('edit')) {
            echo json_encode(['addresses' => $this->context->customer->getAddresses((int) $this->context->cart->id_lang)]);
        }
    }

    public function ajaxProcessUpdateAddresses()
    {
        if ($this->access('edit')) {
            if (($id_address_delivery = (int) Tools::getValue('id_address_delivery')) &&
                Validate::isLoadedObject($address_delivery = new Address((int) $id_address_delivery)) &&
                $address_delivery->id_customer == $this->context->cart->id_customer) {
                $this->context->cart->id_address_delivery = (int) $address_delivery->id;
            }

            if (($id_address_invoice = (int) Tools::getValue('id_address_invoice')) &&
                Validate::isLoadedObject($address_invoice = new Address((int) $id_address_invoice)) &&
                $address_invoice->id_customer = $this->context->cart->id_customer) {
                $this->context->cart->id_address_invoice = (int) $address_invoice->id;
            }
            $this->context->cart->save();

            echo json_encode($this->ajaxReturnVars());
        }
    }

    protected function getCartSummary()
    {
        $summary = $this->context->cart->getSummaryDetails(null, true);
        $currency = Context::getContext()->currency;
        if (count($summary['products'])) {
            foreach ($summary['products'] as &$product) {
                $product['numeric_price'] = $product['price'];
                $product['numeric_total'] = $product['total'];
                $product['price'] = str_replace($currency->symbol, '', $this->context->getCurrentLocale()->formatPrice($product['price'], $currency->iso_code));
                $product['total'] = str_replace($currency->symbol, '', $this->context->getCurrentLocale()->formatPrice($product['total'], $currency->iso_code));
                $product['image_link'] = $this->context->link->getImageLink($product['link_rewrite'], $product['id_image'], 'small_default');
                if (!isset($product['attributes_small'])) {
                    $product['attributes_small'] = '';
                }
                $product['customized_datas'] = Product::getAllCustomizedDatas((int) $this->context->cart->id, null, true, null, (int) $product['id_customization']);
            }
        }
        if (count($summary['discounts'])) {
            foreach ($summary['discounts'] as &$voucher) {
                $voucher['value_real'] = $this->context->getCurrentLocale()->formatPrice($voucher['value_real'], $currency->iso_code);
            }
        }

        if (isset($summary['gift_products']) && count($summary['gift_products'])) {
            foreach ($summary['gift_products'] as &$product) {
                $product['image_link'] = $this->context->link->getImageLink($product['link_rewrite'], $product['id_image'], 'small_default');
                if (!isset($product['attributes_small'])) {
                    $product['attributes_small'] = '';
                }
            }
        }

        return $summary;
    }

    protected function getDeliveryOptionList()
    {
        $delivery_option_list_formated = [];
        $delivery_option_list = $this->context->cart->getDeliveryOptionList();

        if (!count($delivery_option_list)) {
            return [];
        }

        $id_default_carrier = (int) Configuration::get('PS_CARRIER_DEFAULT');
        foreach (current($delivery_option_list) as $key => $delivery_option) {
            $name = '';
            $first = true;
            $id_default_carrier_delivery = false;
            foreach ($delivery_option['carrier_list'] as $carrier) {
                if (!$first) {
                    $name .= ', ';
                } else {
                    $first = false;
                }

                $name .= $carrier['instance']->name;

                if ($delivery_option['unique_carrier']) {
                    $name .= ' - ' . $carrier['instance']->delay[$this->context->employee->id_lang];
                }

                if (!$id_default_carrier_delivery) {
                    $id_default_carrier_delivery = (int) $carrier['instance']->id;
                }
                if ($carrier['instance']->id == $id_default_carrier) {
                    $id_default_carrier_delivery = $id_default_carrier;
                }
                if (!$this->context->cart->id_carrier) {
                    $this->context->cart->setDeliveryOption([$this->context->cart->id_address_delivery => (int) $carrier['instance']->id . ',']);
                    $this->context->cart->save();
                }
            }
            $delivery_option_list_formated[] = ['name' => $name, 'key' => $key];
        }

        return $delivery_option_list_formated;
    }

    public function displayAjaxSearchCarts()
    {
        $id_customer = (int) Tools::getValue('id_customer');
        $carts = Cart::getCustomerCarts((int) $id_customer, false);
        $orders = Order::getCustomerOrders((int) $id_customer);

        if (count($carts)) {
            foreach ($carts as $key => &$cart) {
                $cart_obj = new Cart((int) $cart['id_cart']);
                if ($cart['id_cart'] == $this->context->cart->id) {
                    unset($carts[$key]);

                    continue;
                }
                $currency = new Currency((int) $cart['id_currency']);
                $cart['total_price'] = $this->context->getCurrentLocale()->formatPrice($cart_obj->getOrderTotal(), $currency->iso_code);
            }
        }
        if (count($orders)) {
            foreach ($orders as &$order) {
                $currency = new Currency((int) $order['id_currency']);
                $order['total_paid_real'] = $this->context->getCurrentLocale()->formatPrice($order['total_paid_real'], $currency->iso_code);
            }
        }
        if ($orders || $carts) {
            $to_return = array_merge(
                $this->ajaxReturnVars(),
                [
                    'carts' => $carts,
                    'orders' => $orders,
                    'found' => true,
                ]
            );
        } else {
            $to_return = array_merge($this->ajaxReturnVars(), ['found' => false]);
        }

        echo json_encode($to_return);
    }

    public function ajaxReturnVars()
    {
        $id_cart = (int) $this->context->cart->id;
        $message_content = '';
        if ($message = Message::getMessageByCartId((int) $this->context->cart->id)) {
            $message_content = $message['message'];
        }
        $cart_rules = $this->context->cart->getCartRules(CartRule::FILTER_ACTION_SHIPPING);

        $free_shipping = false;
        if (count($cart_rules)) {
            foreach ($cart_rules as $cart_rule) {
                if ($cart_rule['id_cart_rule'] == CartRule::getIdByCode(CartRule::BO_ORDER_CODE_PREFIX . (int) $this->context->cart->id)) {
                    $free_shipping = true;

                    break;
                }
            }
        }

        $addresses = $this->context->customer->getAddresses((int) $this->context->cart->id_lang);

        foreach ($addresses as &$data) {
            $address = new Address((int) $data['id_address']);
            $data['formated_address'] = AddressFormat::generateAddress($address, [], '<br />');
        }

        return [
            'summary' => $this->getCartSummary(),
            'delivery_option_list' => $this->getDeliveryOptionList(),
            'cart' => $this->context->cart,
            'currency' => new Currency($this->context->cart->id_currency),
            'addresses' => $addresses,
            'id_cart' => $id_cart,
            'order_message' => $message_content,
            'link_order' => $this->context->link->getPageLink(
                'order',
                null,
                (int) $this->context->cart->id_lang,
                'step=3&recover_cart=' . $id_cart . '&token_cart=' . md5(_COOKIE_KEY_ . 'recover_cart_' . $id_cart)
            ),
            'free_shipping' => (int) $free_shipping,
        ];
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    /**
     * Display an image as a download.
     */
    public function displayAjaxCustomizationImage()
    {
        if (!Tools::isSubmit('img') || !Tools::isSubmit('name')) {
            return;
        }

        $img = Tools::getValue('img');
        $name = Tools::getValue('name');
        $path = _PS_UPLOAD_DIR_ . $img;

        if (Validate::isMd5($img) && Validate::isGenericName($path)) {
            header('Content-type: image/jpeg');
            header('Content-Disposition: attachment; filename="' . $name . '.jpg"');
            readfile($path);
        }
    }

    public function displayAjaxGetSummary()
    {
        echo json_encode($this->ajaxReturnVars());
    }

    public function ajaxProcessUpdateProductPrice()
    {
        if ($this->access('edit')) {
            SpecificPrice::deleteByIdCart((int) $this->context->cart->id, (int) Tools::getValue('id_product'), (int) Tools::getValue('id_product_attribute'));
            $specific_price = new SpecificPrice();
            $specific_price->id_cart = (int) $this->context->cart->id;
            $specific_price->id_shop = 0;
            $specific_price->id_shop_group = 0;
            $specific_price->id_currency = 0;
            $specific_price->id_country = 0;
            $specific_price->id_group = 0;
            $specific_price->id_customer = (int) $this->context->customer->id;
            $specific_price->id_product = (int) Tools::getValue('id_product');
            $specific_price->id_product_attribute = (int) Tools::getValue('id_product_attribute');
            $specific_price->price = (float) Tools::getValue('price');
            $specific_price->from_quantity = 1;
            $specific_price->reduction = 0;
            $specific_price->reduction_type = 'amount';
            $specific_price->from = '0000-00-00 00:00:00';
            $specific_price->to = '0000-00-00 00:00:00';
            $specific_price->add();
            echo json_encode($this->ajaxReturnVars());
        }
    }

    public static function getOrderTotalUsingTaxCalculationMethod($id_cart)
    {
        $context = Context::getContext();
        $context->cart = new Cart($id_cart);
        $context->currency = new Currency((int) $context->cart->id_currency);
        $context->customer = new Customer((int) $context->cart->id_customer);
        $context->shop = new Shop((int) $context->cart->id_shop);

        return Cart::getTotalCart($id_cart, true, Cart::BOTH_WITHOUT_SHIPPING);
    }

    public function displayDeleteLink($token, $id, $name = null)
    {
        // don't display ordered carts
        foreach ($this->_list as $row) {
            if ($row['id_cart'] == $id && isset($row['id_order']) && is_numeric($row['id_order'])) {
                return;
            }
        }

        return $this->helper->displayDeleteLink($token, $id, $name);
    }

    public function renderList()
    {
        if (!($this->fields_list && is_array($this->fields_list))) {
            return false;
        }
        $this->getList($this->context->language->id);

        $helper = new HelperList();

        // Empty list is ok
        if (!is_array($this->_list)) {
            $this->displayWarning($this->trans('Bad SQL query', [], 'Admin.Notifications.Error') . '<br />' . htmlspecialchars($this->_list_error));

            return false;
        }

        $this->setHelperDisplay($helper);
        $helper->tpl_vars = $this->tpl_list_vars;
        $helper->tpl_delete_link_vars = $this->tpl_delete_link_vars;

        // For compatibility reasons, we have to check standard actions in class attributes
        foreach ($this->actions_available as $action) {
            if (!in_array($action, $this->actions) && isset($this->$action) && $this->$action) {
                $this->actions[] = $action;
            }
        }
        $helper->is_cms = $this->is_cms;
        $skip_list = [];

        foreach ($this->_list as $row) {
            if (isset($row['id_order']) && is_numeric($row['id_order'])) {
                $skip_list[] = $row['id_cart'];
            }
        }

        if (array_key_exists('delete', $helper->list_skip_actions)) {
            $helper->list_skip_actions['delete'] = array_merge($helper->list_skip_actions['delete'], (array) $skip_list);
        } else {
            $helper->list_skip_actions['delete'] = (array) $skip_list;
        }
        $helper->force_show_bulk_actions = true;
        $helper->force_hide_bulk_actions_btn = count($helper->list_skip_actions['delete']) === count($this->_list);

        return $helper->generateList($this->_list, $this->fields_list);
    }

    /**
     * @param string|null $orderBy
     * @param string|null $orderDirection
     *
     * @return string
     *
     * @throws PrestaShopException
     */
    protected function getOrderByClause($orderBy, $orderDirection)
    {
        $this->_orderBy = $this->checkOrderBy($orderBy);
        $this->_orderWay = $this->checkOrderDirection($orderDirection);

        if ($this->_orderBy == 'status') {
            return ' ORDER BY CAST(status AS unsigned)' . $this->_orderWay .
                ($this->_tmpTableFilter ? ') tmpTable WHERE 1' . $this->_tmpTableFilter : '');
        }

        return parent::getOrderByClause($orderBy, $orderDirection);
    }
}
