<?php
/**
 * 2007-2018 PrestaShop.
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
use PrestaShop\PrestaShop\Adapter\StockManager;

/**
 * @property Order $object
 */
class AdminOrdersControllerCore extends AdminController
{
    public $toolbar_title;

    protected $statuses_array = array();

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'order';
        $this->className = 'Order';
        $this->lang = false;
        $this->addRowAction('view');
        $this->explicitSelect = true;
        $this->allow_export = true;
        $this->deleted = false;

        parent::__construct();

        $this->_select = '
		a.id_currency,
		a.id_order AS id_pdf,
		CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
		osl.`name` AS `osname`,
		os.`color`,
		IF((SELECT so.id_order FROM `' . _DB_PREFIX_ . 'orders` so WHERE so.id_customer = a.id_customer AND so.id_order < a.id_order LIMIT 1) > 0, 0, 1) as new,
		country_lang.name as cname,
		IF(a.valid, 1, 0) badge_success';

        $this->_join = '
		LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`)
		INNER JOIN `' . _DB_PREFIX_ . 'address` address ON address.id_address = a.id_address_delivery
		INNER JOIN `' . _DB_PREFIX_ . 'country` country ON address.id_country = country.id_country
		INNER JOIN `' . _DB_PREFIX_ . 'country_lang` country_lang ON (country.`id_country` = country_lang.`id_country` AND country_lang.`id_lang` = ' . (int) $this->context->language->id . ')
		LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON (os.`id_order_state` = a.`current_state`)
		LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = ' . (int) $this->context->language->id . ')';
        $this->_orderBy = 'id_order';
        $this->_orderWay = 'DESC';
        $this->_use_found_rows = true;

        $statuses = OrderState::getOrderStates((int) $this->context->language->id);
        foreach ($statuses as $status) {
            $this->statuses_array[$status['id_order_state']] = $status['name'];
        }

        $this->fields_list = array(
            'id_order' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'reference' => array(
                'title' => $this->trans('Reference', array(), 'Admin.Global'),
            ),
            'new' => array(
                'title' => $this->trans('New client', array(), 'Admin.Orderscustomers.Feature'),
                'align' => 'text-center',
                'type' => 'bool',
                'tmpTableFilter' => true,
                'orderby' => false,
            ),
            'customer' => array(
                'title' => $this->trans('Customer', array(), 'Admin.Global'),
                'havingFilter' => true,
            ),
        );

        if (Configuration::get('PS_B2B_ENABLE')) {
            $this->fields_list = array_merge($this->fields_list, array(
                'company' => array(
                    'title' => $this->trans('Company', array(), 'Admin.Global'),
                    'filter_key' => 'c!company',
                ),
            ));
        }

        $this->fields_list = array_merge($this->fields_list, array(
            'total_paid_tax_incl' => array(
                'title' => $this->trans('Total', array(), 'Admin.Global'),
                'align' => 'text-right',
                'type' => 'price',
                'currency' => true,
                'callback' => 'setOrderCurrency',
                'badge_success' => true,
            ),
            'payment' => array(
                'title' => $this->trans('Payment', array(), 'Admin.Global'),
            ),
            'osname' => array(
                'title' => $this->trans('Status', array(), 'Admin.Global'),
                'type' => 'select',
                'color' => 'color',
                'list' => $this->statuses_array,
                'filter_key' => 'os!id_order_state',
                'filter_type' => 'int',
                'order_key' => 'osname',
            ),
            'date_add' => array(
                'title' => $this->trans('Date', array(), 'Admin.Global'),
                'align' => 'text-right',
                'type' => 'datetime',
                'filter_key' => 'a!date_add',
            ),
            'id_pdf' => array(
                'title' => $this->trans('PDF', array(), 'Admin.Global'),
                'align' => 'text-center',
                'callback' => 'printPDFIcons',
                'orderby' => false,
                'search' => false,
                'remove_onclick' => true,
            ),
        ));

        if (Country::isCurrentlyUsed('country', true)) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT DISTINCT c.id_country, cl.`name`
			FROM `' . _DB_PREFIX_ . 'orders` o
			' . Shop::addSqlAssociation('orders', 'o') . '
			INNER JOIN `' . _DB_PREFIX_ . 'address` a ON a.id_address = o.id_address_delivery
			INNER JOIN `' . _DB_PREFIX_ . 'country` c ON a.id_country = c.id_country
			INNER JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (c.`id_country` = cl.`id_country` AND cl.`id_lang` = ' . (int) $this->context->language->id . ')
			ORDER BY cl.name ASC');

            $country_array = array();
            foreach ($result as $row) {
                $country_array[$row['id_country']] = $row['name'];
            }

            $part1 = array_slice($this->fields_list, 0, 3);
            $part2 = array_slice($this->fields_list, 3);
            $part1['cname'] = array(
                'title' => $this->trans('Delivery', array(), 'Admin.Global'),
                'type' => 'select',
                'list' => $country_array,
                'filter_key' => 'country!id_country',
                'filter_type' => 'int',
                'order_key' => 'cname',
            );
            $this->fields_list = array_merge($part1, $part2);
        }

        $this->shopLinkType = 'shop';
        $this->shopShareDatas = Shop::SHARE_ORDER;

        if (Tools::isSubmit('id_order')) {
            // Save context (in order to apply cart rule)
            $order = new Order((int) Tools::getValue('id_order'));
            $this->context->cart = new Cart($order->id_cart);
            $this->context->customer = new Customer($order->id_customer);
        }

        $this->bulk_actions = array(
            'updateOrderStatus' => array('text' => $this->trans('Change Order Status', array(), 'Admin.Orderscustomers.Feature'), 'icon' => 'icon-refresh'),
        );
    }

    public static function setOrderCurrency($echo, $tr)
    {
        if (!empty($tr['id_currency'])) {
            $idCurrency = (int) $tr['id_currency'];
        } else {
            $order = new Order($tr['id_order']);
            $idCurrency = (int) $order->id_currency;
        }

        return Tools::displayPrice($echo, $idCurrency);
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_order'] = array(
                'href' => self::$currentIndex . '&addorder&token=' . $this->token,
                'desc' => $this->trans('Add new order', array(), 'Admin.Orderscustomers.Feature'),
                'icon' => 'process-icon-new',
            );
        }

        if ($this->display == 'add') {
            unset($this->page_header_toolbar_btn['save']);
        }

        if (Context::getContext()->shop->getContext() != Shop::CONTEXT_SHOP && isset($this->page_header_toolbar_btn['new_order'])
            && Shop::isFeatureActive()) {
            unset($this->page_header_toolbar_btn['new_order']);
        }
    }

    public function renderForm()
    {
        if (Context::getContext()->shop->getContext() != Shop::CONTEXT_SHOP && Shop::isFeatureActive()) {
            $this->errors[] = $this->trans('You have to select a shop before creating new orders.', array(), 'Admin.Orderscustomers.Notification');
        }

        $id_cart = (int) Tools::getValue('id_cart');
        $cart = new Cart((int) $id_cart);
        if ($id_cart && !Validate::isLoadedObject($cart)) {
            $this->errors[] = $this->trans('This cart does not exists', array(), 'Admin.Orderscustomers.Notification');
        }
        if ($id_cart && Validate::isLoadedObject($cart) && !$cart->id_customer) {
            $this->errors[] = $this->trans('The cart must have a customer', array(), 'Admin.Orderscustomers.Notification');
        }
        if (count($this->errors)) {
            return false;
        }

        parent::renderForm();
        unset($this->toolbar_btn['save']);
        $this->addJqueryPlugin(array('autocomplete', 'fancybox', 'typewatch', 'highlight'));

        $defaults_order_state = array('cheque' => (int) Configuration::get('PS_OS_CHEQUE'),
                                                'bankwire' => (int) Configuration::get('PS_OS_BANKWIRE'),
                                                'cashondelivery' => Configuration::get('PS_OS_COD_VALIDATION') ? (int) Configuration::get('PS_OS_COD_VALIDATION') : (int) Configuration::get('PS_OS_PREPARATION'),
                                                'other' => (int) Configuration::get('PS_OS_PAYMENT'), );
        $payment_modules = array();
        foreach (PaymentModule::getInstalledPaymentModules() as $p_module) {
            $payment_modules[] = Module::getInstanceById((int) $p_module['id_module']);
        }

        $this->context->smarty->assign(array(
            'recyclable_pack' => (int) Configuration::get('PS_RECYCLABLE_PACK'),
            'gift_wrapping' => (int) Configuration::get('PS_GIFT_WRAPPING'),
            'cart' => $cart,
            'currencies' => Currency::getCurrenciesByIdShop(Context::getContext()->shop->id),
            'langs' => Language::getLanguages(true, Context::getContext()->shop->id),
            'payment_modules' => $payment_modules,
            'order_states' => OrderState::getOrderStates((int) Context::getContext()->language->id),
            'defaults_order_state' => $defaults_order_state,
            'show_toolbar' => $this->show_toolbar,
            'toolbar_btn' => $this->toolbar_btn,
            'toolbar_scroll' => $this->toolbar_scroll,
            'PS_CATALOG_MODE' => Configuration::get('PS_CATALOG_MODE'),
            'title' => array($this->trans('Orders', array(), 'Admin.Orderscustomers.Feature'), $this->trans('Create order', array(), 'Admin.Orderscustomers.Feature')),
        ));
        $this->content .= $this->createTemplate('form.tpl')->fetch();
    }

    public function initToolbar()
    {
        if ($this->display == 'view') {
            /** @var Order $order */
            $order = $this->loadObject();
            $customer = $this->context->customer;

            if (!Validate::isLoadedObject($order)) {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminOrders'));
            }

            $this->toolbar_title[] = $this->trans(
                'Order %reference% from %firstname% %lastname%',
                array(
                    '%reference%' => $order->reference,
                    '%firstname%' => $customer->firstname,
                    '%lastname%' => $customer->lastname,
                ),
                'Admin.Orderscustomers.Feature'
            );
            $this->addMetaTitle($this->toolbar_title[count($this->toolbar_title) - 1]);

            if ($order->hasBeenShipped()) {
                $type = $this->trans('Return products', array(), 'Admin.Orderscustomers.Feature');
            } elseif ($order->hasBeenPaid()) {
                $type = $this->trans('Standard refund', array(), 'Admin.Orderscustomers.Feature');
            } else {
                $type = $this->trans('Cancel products', array(), 'Admin.Orderscustomers.Feature');
            }

            if (!$order->hasBeenShipped() && !$this->lite_display) {
                $this->toolbar_btn['new'] = array(
                    'short' => 'Create',
                    'href' => '#',
                    'desc' => $this->trans('Add a product', array(), 'Admin.Orderscustomers.Feature'),
                    'class' => 'add_product',
                );
            }

            if (Configuration::get('PS_ORDER_RETURN') && !$this->lite_display) {
                $this->toolbar_btn['standard_refund'] = array(
                    'short' => 'Create',
                    'href' => '',
                    'desc' => $type,
                    'class' => 'process-icon-standardRefund',
                );
            }

            if ($order->hasInvoice() && !$this->lite_display) {
                $this->toolbar_btn['partial_refund'] = array(
                    'short' => 'Create',
                    'href' => '',
                    'desc' => $this->trans('Partial refund', array(), 'Admin.Orderscustomers.Feature'),
                    'class' => 'process-icon-partialRefund',
                );
            }
        }
        $res = parent::initToolbar();
        if (Context::getContext()->shop->getContext() != Shop::CONTEXT_SHOP && isset($this->toolbar_btn['new']) && Shop::isFeatureActive()) {
            unset($this->toolbar_btn['new']);
        }

        return $res;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addJqueryUI('ui.datepicker');
        $this->addJS(_PS_JS_DIR_ . 'vendor/d3.v3.min.js');
        $this->addJS('https://maps.googleapis.com/maps/api/js?v=3.exp');

        if ($this->access('edit') && $this->display == 'view') {
            $this->addJS(_PS_JS_DIR_ . 'admin/orders.js');
            $this->addJS(_PS_JS_DIR_ . 'tools.js');
            $this->addJqueryPlugin('autocomplete');
        }
    }

    public function printPDFIcons($id_order, $tr)
    {
        static $valid_order_state = array();

        $order = new Order($id_order);
        if (!Validate::isLoadedObject($order)) {
            return '';
        }

        if (!isset($valid_order_state[$order->current_state])) {
            $valid_order_state[$order->current_state] = Validate::isLoadedObject($order->getCurrentOrderState());
        }

        if (!$valid_order_state[$order->current_state]) {
            return '';
        }

        $this->context->smarty->assign(array(
            'order' => $order,
            'tr' => $tr,
        ));

        return $this->createTemplate('_print_pdf_icon.tpl')->fetch();
    }

    public function processBulkUpdateOrderStatus()
    {
        if (Tools::isSubmit('submitUpdateOrderStatus')
            && ($id_order_state = (int) Tools::getValue('id_order_state'))) {
            if (true !== $this->access('edit')) {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            } else {
                $order_state = new OrderState($id_order_state);

                if (!Validate::isLoadedObject($order_state)) {
                    $this->errors[] = $this->trans('Order status #%id% cannot be loaded', array('%id%' => $id_order_state), 'Admin.Orderscustomers.Notification');
                } else {
                    foreach (Tools::getValue('orderBox') as $id_order) {
                        $order = new Order((int) $id_order);
                        if (!Validate::isLoadedObject($order)) {
                            $this->errors[] = $this->trans('Order #%d cannot be loaded', array('#%d' => $id_order), 'Admin.Orderscustomers.Notification');
                        } else {
                            $current_order_state = $order->getCurrentOrderState();
                            if ($current_order_state->id == $order_state->id) {
                                $this->errors[] = $this->trans('Order #%d has already been assigned this status.', array('#%d' => $id_order), 'Admin.Orderscustomers.Notification');
                            } else {
                                $history = new OrderHistory();
                                $history->id_order = $order->id;
                                $history->id_employee = (int) $this->context->employee->id;

                                $use_existings_payment = !$order->hasInvoice();
                                $history->changeIdOrderState((int) $order_state->id, $order, $use_existings_payment);

                                $carrier = new Carrier($order->id_carrier, $order->id_lang);
                                $templateVars = array();
                                if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number) {
                                    $templateVars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));
                                }

                                if ($history->addWithemail(true, $templateVars)) {
                                    if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                                        foreach ($order->getProducts() as $product) {
                                            if (StockAvailable::dependsOnStock($product['product_id'])) {
                                                StockAvailable::synchronize($product['product_id'], (int) $product['id_shop']);
                                            }
                                        }
                                    }
                                } else {
                                    $this->errors[] = $this->trans(
                                        'An error occurred while changing the status for order #%d, or we were unable to send an email to the customer.',
                                        array(
                                            '#%d' => $id_order,
                                        ),
                                        'Admin.Orderscustomers.Notification'
                                    );
                                }
                            }
                        }
                    }
                }
            }
            if (!count($this->errors)) {
                Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
            }
        }
    }

    public function renderList()
    {
        if (Tools::isSubmit('submitBulkupdateOrderStatus' . $this->table)) {
            if (Tools::getIsset('cancel')) {
                Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);
            }

            $this->tpl_list_vars['updateOrderStatus_mode'] = true;
            $this->tpl_list_vars['order_statuses'] = $this->statuses_array;
            $this->tpl_list_vars['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
            $this->tpl_list_vars['POST'] = $_POST;
        }

        return parent::renderList();
    }

    public function postProcess()
    {
        // If id_order is sent, we instanciate a new Order object
        if (Tools::isSubmit('id_order') && Tools::getValue('id_order') > 0) {
            $order = new Order(Tools::getValue('id_order'));
            if (!Validate::isLoadedObject($order)) {
                $this->errors[] = $this->trans('The order cannot be found within your database.', array(), 'Admin.Orderscustomers.Notification');
            }
            ShopUrl::cacheMainDomainForShop((int) $order->id_shop);
        }

        /* Update shipping number and carrier */
        if (Tools::isSubmit('submitShippingNumber') && isset($order)) {
            if ($this->access('edit')) {
                $tracking_number = Tools::getValue('shipping_tracking_number');
                $id_carrier = Tools::getValue('shipping_carrier');
                $old_tracking_number = $order->shipping_number;

                $order_carrier = new OrderCarrier(Tools::getValue('id_order_carrier'));
                if (!Validate::isLoadedObject($order_carrier)) {
                    $this->errors[] = $this->trans('The order carrier ID is invalid.', array(), 'Admin.Orderscustomers.Notification');
                } elseif (!empty($tracking_number) && !Validate::isTrackingNumber($tracking_number)) {
                    $this->errors[] = $this->trans('The tracking number is incorrect.', array(), 'Admin.Orderscustomers.Notification');
                } else {
                    //update carrier - ONLY if changed - then refresh shipping cost
                    $old_id_carrier = $order_carrier->id_carrier;
                    if (!empty($id_carrier) && $old_id_carrier != $id_carrier) {
                        $order->id_carrier = (int) $id_carrier;
                        $order_carrier->id_carrier = (int) $id_carrier;
                        $order_carrier->update();
                        $order->refreshShippingCost();
                    }

                    //load fresh order carrier because updated just before
                    $order_carrier = new OrderCarrier((int) Tools::getValue('id_order_carrier'));

                    // update shipping number
                    // Keep these two following lines for backward compatibility, remove on 1.6 version
                    $order->shipping_number = $tracking_number;
                    $order->update();

                    // Update order_carrier
                    $order_carrier->tracking_number = pSQL($tracking_number);
                    if ($order_carrier->update()) {
                        //send mail only if tracking number is different AND not empty
                        if (!empty($tracking_number) && $old_tracking_number != $tracking_number) {
                            if ($order_carrier->sendInTransitEmail($order)) {
                                $customer = new Customer((int) $order->id_customer);
                                $carrier = new Carrier((int) $order->id_carrier, $order->id_lang);

                                Hook::exec('actionAdminOrdersTrackingNumberUpdate', array(
                                    'order' => $order,
                                    'customer' => $customer,
                                    'carrier' => $carrier,
                                ), null, false, true, false, $order->id_shop);

                                Tools::redirectAdmin(self::$currentIndex . '&id_order=' . $order->id . '&vieworder&conf=4&token=' . $this->token);
                            } else {
                                $this->errors[] = $this->trans('An error occurred while sending an email to the customer.', array(), 'Admin.Orderscustomers.Notification');
                            }
                        }
                    } else {
                        $this->errors[] = $this->trans('The order carrier cannot be updated.', array(), 'Admin.Orderscustomers.Notification');
                    }
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitState') && isset($order)) {
            /* Change order status, add a new entry in order history and send an e-mail to the customer if needed */
            if ($this->access('edit')) {
                $order_state = new OrderState(Tools::getValue('id_order_state'));

                if (!Validate::isLoadedObject($order_state)) {
                    $this->errors[] = $this->trans('The new order status is invalid.', array(), 'Admin.Orderscustomers.Notification');
                } else {
                    $current_order_state = $order->getCurrentOrderState();
                    if ($current_order_state->id != $order_state->id) {
                        // Create new OrderHistory
                        $history = new OrderHistory();
                        $history->id_order = $order->id;
                        $history->id_employee = (int) $this->context->employee->id;

                        $use_existings_payment = false;
                        if (!$order->hasInvoice()) {
                            $use_existings_payment = true;
                        }
                        $history->changeIdOrderState((int) $order_state->id, $order, $use_existings_payment);

                        $carrier = new Carrier($order->id_carrier, $order->id_lang);
                        $templateVars = array();
                        if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number) {
                            $templateVars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));
                        }

                        // Save all changes
                        if ($history->addWithemail(true, $templateVars)) {
                            // synchronizes quantities if needed..
                            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                                foreach ($order->getProducts() as $product) {
                                    if (StockAvailable::dependsOnStock($product['product_id'])) {
                                        StockAvailable::synchronize($product['product_id'], (int) $product['id_shop']);
                                    }
                                }
                            }

                            Tools::redirectAdmin(self::$currentIndex . '&id_order=' . (int) $order->id . '&vieworder&token=' . $this->token);
                        }
                        $this->errors[] = $this->trans('An error occurred while changing order status, or we were unable to send an email to the customer.', array(), 'Admin.Orderscustomers.Notification');
                    } else {
                        $this->errors[] = $this->trans('The order has already been assigned this status.', array(), 'Admin.Orderscustomers.Notification');
                    }
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitMessage') && isset($order)) {
            // Add a new message for the current order and send an e-mail to the customer if needed
            if ($this->access('edit')) {
                $customer = new Customer(Tools::getValue('id_customer'));
                if (!Validate::isLoadedObject($customer)) {
                    $this->errors[] = $this->trans('The customer is invalid.', array(), 'Admin.Orderscustomers.Notification');
                } elseif (!Tools::getValue('message')) {
                    $this->errors[] = $this->trans('The message cannot be blank.', array(), 'Admin.Orderscustomers.Notification');
                } else {
                    /* Get message rules and and check fields validity */
                    $rules = call_user_func(array('Message', 'getValidationRules'), 'Message');
                    foreach ($rules['required'] as $field) {
                        if (($value = Tools::getValue($field)) == false && (string) $value != '0') {
                            if (!Tools::getValue('id_' . $this->table) || $field != 'passwd') {
                                $this->errors[] = $this->trans('field %s is required.', array('%s' => $field), 'Admin.Orderscustomers.Notification');
                            }
                        }
                    }
                    foreach ($rules['size'] as $field => $maxLength) {
                        if (Tools::getValue($field) && Tools::strlen(Tools::getValue($field)) > $maxLength) {
                            $this->errors[] = $this->trans(
                                'The %1$s field is too long (%2$d chars max).',
                                array(
                                    '%1$s' => $field,
                                    '%2$d' => $maxLength,
                                ),
                                'Admin.Notifications.Error'
                            );
                        }
                    }
                    foreach ($rules['validate'] as $field => $function) {
                        if (Tools::getValue($field)) {
                            if (!Validate::$function(htmlentities(Tools::getValue($field), ENT_COMPAT, 'UTF-8'))) {
                                $this->errors[] = $this->trans('The %s field is invalid.', array('%s' => $field), 'Admin.Notifications.Error');
                            }
                        }
                    }

                    if (!count($this->errors)) {
                        //check if a thread already exist
                        $id_customer_thread = CustomerThread::getIdCustomerThreadByEmailAndIdOrder($customer->email, $order->id);
                        if (!$id_customer_thread) {
                            $customer_thread = new CustomerThread();
                            $customer_thread->id_contact = 0;
                            $customer_thread->id_customer = (int) $order->id_customer;
                            $customer_thread->id_shop = (int) $this->context->shop->id;
                            $customer_thread->id_order = (int) $order->id;
                            $customer_thread->id_lang = (int) $this->context->language->id;
                            $customer_thread->email = $customer->email;
                            $customer_thread->status = 'open';
                            $customer_thread->token = Tools::passwdGen(12);
                            $customer_thread->add();
                        } else {
                            $customer_thread = new CustomerThread((int) $id_customer_thread);
                        }

                        $customer_message = new CustomerMessage();
                        $customer_message->id_customer_thread = $customer_thread->id;
                        $customer_message->id_employee = (int) $this->context->employee->id;
                        $customer_message->message = Tools::getValue('message');
                        $customer_message->private = Tools::getValue('visibility');

                        if (!$customer_message->add()) {
                            $this->errors[] = $this->trans('An error occurred while saving the message.', array(), 'Admin.Notifications.Error');
                        } elseif ($customer_message->private) {
                            Tools::redirectAdmin(self::$currentIndex . '&id_order=' . (int) $order->id . '&vieworder&conf=11&token=' . $this->token);
                        } else {
                            $message = $customer_message->message;
                            if (Configuration::get('PS_MAIL_TYPE', null, null, $order->id_shop) != Mail::TYPE_TEXT) {
                                $message = Tools::nl2br($customer_message->message);
                            }

                            $orderLanguage = new Language((int) $order->id_lang);
                            $varsTpl = array(
                                '{lastname}' => $customer->lastname,
                                '{firstname}' => $customer->firstname,
                                '{id_order}' => $order->id,
                                '{order_name}' => $order->getUniqReference(),
                                '{message}' => $message,
                            );

                            if (
                                @Mail::Send(
                                    (int) $order->id_lang,
                                    'order_merchant_comment',
                                    $this->trans(
                                        'New message regarding your order',
                                        array(),
                                        'Emails.Subject',
                                        $orderLanguage->locale
                                    ),
                                    $varsTpl, $customer->email,
                                    $customer->firstname . ' ' . $customer->lastname,
                                    null,
                                    null,
                                    null,
                                    null,
                                    _PS_MAIL_DIR_,
                                    true,
                                    (int) $order->id_shop)
                            ) {
                                Tools::redirectAdmin(self::$currentIndex . '&id_order=' . $order->id . '&vieworder&conf=11' . '&token=' . $this->token);
                            }
                        }
                        $this->errors[] = $this->trans('An error occurred while sending an email to the customer.', array(), 'Admin.Orderscustomers.Notification');
                    }
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to delete this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('partialRefund') && isset($order)) {
            // Partial refund from order
            if ($this->access('edit')) {
                if (Tools::isSubmit('partialRefundProduct') && ($refunds = Tools::getValue('partialRefundProduct')) && is_array($refunds)) {
                    $amount = 0;
                    $order_detail_list = array();
                    $full_quantity_list = array();
                    foreach ($refunds as $id_order_detail => $amount_detail) {
                        $quantity = Tools::getValue('partialRefundProductQuantity');
                        if (!$quantity[$id_order_detail]) {
                            continue;
                        }

                        $full_quantity_list[$id_order_detail] = (int) $quantity[$id_order_detail];

                        $order_detail_list[$id_order_detail] = array(
                            'quantity' => (int) $quantity[$id_order_detail],
                            'id_order_detail' => (int) $id_order_detail,
                        );

                        $order_detail = new OrderDetail((int) $id_order_detail);
                        if (empty($amount_detail)) {
                            $order_detail_list[$id_order_detail]['unit_price'] = (!Tools::getValue('TaxMethod') ? $order_detail->unit_price_tax_excl : $order_detail->unit_price_tax_incl);
                            $order_detail_list[$id_order_detail]['amount'] = $order_detail->unit_price_tax_incl * $order_detail_list[$id_order_detail]['quantity'];
                        } else {
                            $order_detail_list[$id_order_detail]['amount'] = (float) str_replace(',', '.', $amount_detail);
                            $order_detail_list[$id_order_detail]['unit_price'] = $order_detail_list[$id_order_detail]['amount'] / $order_detail_list[$id_order_detail]['quantity'];
                        }
                        $amount += $order_detail_list[$id_order_detail]['amount'];
                        if (!$order->hasBeenDelivered() || ($order->hasBeenDelivered() && Tools::isSubmit('reinjectQuantities')) && $order_detail_list[$id_order_detail]['quantity'] > 0) {
                            $this->reinjectQuantity($order_detail, $order_detail_list[$id_order_detail]['quantity']);
                        }
                    }

                    $shipping_cost_amount = (float) str_replace(',', '.', Tools::getValue('partialRefundShippingCost')) ? (float) str_replace(',', '.', Tools::getValue('partialRefundShippingCost')) : false;

                    if ($amount == 0 && $shipping_cost_amount == 0) {
                        if (!empty($refunds)) {
                            $this->errors[] = $this->trans('Please enter a quantity to proceed with your refund.', array(), 'Admin.Orderscustomers.Notification');
                        } else {
                            $this->errors[] = $this->trans('Please enter an amount to proceed with your refund.', array(), 'Admin.Orderscustomers.Notification');
                        }

                        return false;
                    }

                    $choosen = false;
                    $voucher = 0;

                    if ((int) Tools::getValue('refund_voucher_off') == 1) {
                        $amount -= $voucher = (float) Tools::getValue('order_discount_price');
                    } elseif ((int) Tools::getValue('refund_voucher_off') == 2) {
                        $choosen = true;
                        $amount = $voucher = (float) Tools::getValue('refund_voucher_choose');
                    }

                    if ($shipping_cost_amount > 0) {
                        if (!Tools::getValue('TaxMethod')) {
                            $tax = new Tax();
                            $tax->rate = $order->carrier_tax_rate;
                            $tax_calculator = new TaxCalculator(array($tax));
                            $amount += $tax_calculator->addTaxes($shipping_cost_amount);
                        } else {
                            $amount += $shipping_cost_amount;
                        }
                    }

                    $order_carrier = new OrderCarrier((int) $order->getIdOrderCarrier());
                    if (Validate::isLoadedObject($order_carrier)) {
                        $order_carrier->weight = (float) $order->getTotalWeight();
                        if ($order_carrier->update()) {
                            $order->weight = sprintf('%.3f ' . Configuration::get('PS_WEIGHT_UNIT'), $order_carrier->weight);
                        }
                    }

                    if ($amount >= 0) {
                        if (!OrderSlip::create($order, $order_detail_list, $shipping_cost_amount, $voucher, $choosen,
                            (Tools::getValue('TaxMethod') ? false : true))) {
                            $this->errors[] = $this->trans('You cannot generate a partial credit slip.', array(), 'Admin.Orderscustomers.Notification');
                        } else {
                            Hook::exec('actionOrderSlipAdd', array('order' => $order, 'productList' => $order_detail_list, 'qtyList' => $full_quantity_list), null, false, true, false, $order->id_shop);
                            $customer = new Customer((int) ($order->id_customer));
                            $params['{lastname}'] = $customer->lastname;
                            $params['{firstname}'] = $customer->firstname;
                            $params['{id_order}'] = $order->id;
                            $params['{order_name}'] = $order->getUniqReference();
                            $orderLanguage = new Language((int) $order->id_lang);
                            @Mail::Send(
                                (int) $order->id_lang,
                                'credit_slip',
                                $this->trans(
                                    'New credit slip regarding your order',
                                    array(),
                                    'Emails.Subject',
                                    $orderLanguage->locale
                                ),
                                $params,
                                $customer->email,
                                $customer->firstname . ' ' . $customer->lastname,
                                null,
                                null,
                                null,
                                null,
                                _PS_MAIL_DIR_,
                                true,
                                (int) $order->id_shop
                            );
                        }

                        foreach ($order_detail_list as &$product) {
                            $order_detail = new OrderDetail((int) $product['id_order_detail']);
                            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                                StockAvailable::synchronize($order_detail->product_id);
                            }
                        }

                        // Generate voucher
                        if (Tools::isSubmit('generateDiscountRefund') && !count($this->errors) && $amount > 0) {
                            $cart_rule = new CartRule();
                            $cart_rule->description = $this->trans('Credit slip for order #%d', array('#%d' => $order->id), 'Admin.Orderscustomers.Feature');
                            $language_ids = Language::getIDs(false);
                            foreach ($language_ids as $id_lang) {
                                // Define a temporary name
                                $cart_rule->name[$id_lang] = sprintf('V0C%1$dO%2$d', $order->id_customer, $order->id);
                            }

                            // Define a temporary code
                            $cart_rule->code = sprintf('V0C%1$dO%2$d', $order->id_customer, $order->id);
                            $cart_rule->quantity = 1;
                            $cart_rule->quantity_per_user = 1;

                            // Specific to the customer
                            $cart_rule->id_customer = $order->id_customer;
                            $now = time();
                            $cart_rule->date_from = date('Y-m-d H:i:s', $now);
                            $cart_rule->date_to = date('Y-m-d H:i:s', strtotime('+1 year'));
                            $cart_rule->partial_use = 1;
                            $cart_rule->active = 1;

                            $cart_rule->reduction_amount = $amount;
                            $cart_rule->reduction_tax = $order->getTaxCalculationMethod() != PS_TAX_EXC;
                            $cart_rule->minimum_amount_currency = $order->id_currency;
                            $cart_rule->reduction_currency = $order->id_currency;

                            if (!$cart_rule->add()) {
                                $this->errors[] = $this->trans('You cannot generate a voucher.', array(), 'Admin.Orderscustomers.Notification');
                            } else {
                                // Update the voucher code and name
                                foreach ($language_ids as $id_lang) {
                                    $cart_rule->name[$id_lang] = sprintf('V%1$dC%2$dO%3$d', $cart_rule->id, $order->id_customer, $order->id);
                                }
                                $cart_rule->code = sprintf('V%1$dC%2$dO%3$d', $cart_rule->id, $order->id_customer, $order->id);

                                if (!$cart_rule->update()) {
                                    $this->errors[] = $this->trans('You cannot generate a voucher.', array(), 'Admin.Orderscustomers.Notification');
                                } else {
                                    $currency = $this->context->currency;
                                    $customer = new Customer((int) ($order->id_customer));
                                    $params['{lastname}'] = $customer->lastname;
                                    $params['{firstname}'] = $customer->firstname;
                                    $params['{id_order}'] = $order->id;
                                    $params['{order_name}'] = $order->getUniqReference();
                                    $params['{voucher_amount}'] = Tools::displayPrice($cart_rule->reduction_amount, $currency, false);
                                    $params['{voucher_num}'] = $cart_rule->code;
                                    $orderLanguage = new Language((int) $order->id_lang);
                                    @Mail::Send(
                                        (int) $order->id_lang,
                                        'voucher',
                                        $this->trans(
                                            'New voucher for your order #%s',
                                            array($order->reference),
                                            'Emails.Subject',
                                            $orderLanguage->locale
                                        ),
                                        $params,
                                        $customer->email,
                                        $customer->firstname . ' ' . $customer->lastname,
                                        null,
                                        null,
                                        null,
                                        null,
                                        _PS_MAIL_DIR_,
                                        true,
                                        (int) $order->id_shop
                                    );
                                }
                            }
                        }
                    } else {
                        if (!empty($refunds)) {
                            $this->errors[] = $this->trans('Please enter a quantity to proceed with your refund.', array(), 'Admin.Orderscustomers.Notification');
                        } else {
                            $this->errors[] = $this->trans('Please enter an amount to proceed with your refund.', array(), 'Admin.Orderscustomers.Notification');
                        }
                    }

                    // Redirect if no errors
                    if (!count($this->errors)) {
                        Tools::redirectAdmin(self::$currentIndex . '&id_order=' . $order->id . '&vieworder&conf=30&token=' . $this->token);
                    }
                } else {
                    $this->errors[] = $this->trans('The partial refund data is incorrect.', array(), 'Admin.Orderscustomers.Notification');
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to delete this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('cancelProduct') && isset($order)) {
            // Cancel product from order
            if ($this->access('delete')) {
                if (!Tools::isSubmit('id_order_detail') && !Tools::isSubmit('id_customization')) {
                    $this->errors[] = $this->trans('You must select a product.', array(), 'Admin.Orderscustomers.Notification');
                } elseif (!Tools::isSubmit('cancelQuantity') && !Tools::isSubmit('cancelCustomizationQuantity')) {
                    $this->errors[] = $this->trans('You must enter a quantity.', array(), 'Admin.Orderscustomers.Notification');
                } else {
                    $productList = Tools::getValue('id_order_detail');
                    if ($productList) {
                        $productList = array_map('intval', $productList);
                    }

                    $customizationList = Tools::getValue('id_customization');
                    if ($customizationList) {
                        $customizationList = array_map('intval', $customizationList);
                    }

                    $qtyList = Tools::getValue('cancelQuantity');
                    if ($qtyList) {
                        $qtyList = array_map('intval', $qtyList);
                    }

                    $customizationQtyList = Tools::getValue('cancelCustomizationQuantity');
                    if ($customizationQtyList) {
                        $customizationQtyList = array_map('intval', $customizationQtyList);
                    }

                    $full_product_list = $productList;
                    $full_quantity_list = $qtyList;

                    if ($customizationList) {
                        foreach ($customizationList as $key => $id_order_detail) {
                            $full_product_list[(int) $id_order_detail] = $id_order_detail;
                            if (isset($customizationQtyList[$key])) {
                                $full_quantity_list[(int) $id_order_detail] += $customizationQtyList[$key];
                            }
                        }
                    }

                    if ($productList || $customizationList) {
                        if ($productList) {
                            $id_cart = Cart::getCartIdByOrderId($order->id);
                            $customization_quantities = Customization::countQuantityByCart($id_cart);

                            foreach ($productList as $key => $id_order_detail) {
                                $qtyCancelProduct = abs($qtyList[$key]);
                                if (!$qtyCancelProduct) {
                                    $this->errors[] = $this->trans('No quantity has been selected for this product.', array(), 'Admin.Orderscustomers.Notification');
                                }

                                $order_detail = new OrderDetail($id_order_detail);
                                $customization_quantity = 0;
                                if (array_key_exists($order_detail->product_id, $customization_quantities) && array_key_exists($order_detail->product_attribute_id, $customization_quantities[$order_detail->product_id])) {
                                    $customization_quantity = (int) $customization_quantities[$order_detail->product_id][$order_detail->product_attribute_id];
                                }

                                if (($order_detail->product_quantity - $customization_quantity - $order_detail->product_quantity_refunded - $order_detail->product_quantity_return) < $qtyCancelProduct) {
                                    $this->errors[] = $this->trans('An invalid quantity was selected for this product.', array(), 'Admin.Orderscustomers.Notification');
                                }
                            }
                        }
                        if ($customizationList) {
                            $customization_quantities = Customization::retrieveQuantitiesFromIds(array_keys($customizationList));

                            foreach ($customizationList as $id_customization => $id_order_detail) {
                                $qtyCancelProduct = abs($customizationQtyList[$id_customization]);
                                $customization_quantity = $customization_quantities[$id_customization];

                                if (!$qtyCancelProduct) {
                                    $this->errors[] = $this->trans('No quantity has been selected for this product.', array(), 'Admin.Orderscustomers.Notification');
                                }

                                if ($qtyCancelProduct > ($customization_quantity['quantity'] - ($customization_quantity['quantity_refunded'] + $customization_quantity['quantity_returned']))) {
                                    $this->errors[] = $this->trans('An invalid quantity was selected for this product.', array(), 'Admin.Orderscustomers.Notification');
                                }
                            }
                        }

                        if (!count($this->errors) && $productList) {
                            foreach ($productList as $key => $id_order_detail) {
                                $qty_cancel_product = abs($qtyList[$key]);
                                $order_detail = new OrderDetail((int) ($id_order_detail));

                                if (!$order->hasBeenDelivered() || ($order->hasBeenDelivered() && Tools::isSubmit('reinjectQuantities')) && $qty_cancel_product > 0) {
                                    $this->reinjectQuantity($order_detail, $qty_cancel_product);
                                }

                                // Delete product
                                $order_detail = new OrderDetail((int) $id_order_detail);
                                if (!$order->deleteProduct($order, $order_detail, $qty_cancel_product)) {
                                    $this->errors[] = $this->trans('An error occurred while attempting to delete the product.', array(), 'Admin.Orderscustomers.Notification') . ' <span class="bold">' . $order_detail->product_name . '</span>';
                                }
                                // Update weight SUM
                                $order_carrier = new OrderCarrier((int) $order->getIdOrderCarrier());
                                if (Validate::isLoadedObject($order_carrier)) {
                                    $order_carrier->weight = (float) $order->getTotalWeight();
                                    if ($order_carrier->update()) {
                                        $order->weight = sprintf('%.3f ' . Configuration::get('PS_WEIGHT_UNIT'), $order_carrier->weight);
                                    }
                                }

                                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && StockAvailable::dependsOnStock($order_detail->product_id)) {
                                    StockAvailable::synchronize($order_detail->product_id);
                                }
                                Hook::exec('actionProductCancel', array('order' => $order, 'id_order_detail' => (int) $id_order_detail), null, false, true, false, $order->id_shop);
                            }
                        }
                        if (!count($this->errors) && $customizationList) {
                            foreach ($customizationList as $id_customization => $id_order_detail) {
                                $order_detail = new OrderDetail((int) ($id_order_detail));
                                $qtyCancelProduct = abs($customizationQtyList[$id_customization]);
                                if (!$order->deleteCustomization($id_customization, $qtyCancelProduct, $order_detail)) {
                                    $this->errors[] = $this->trans('An error occurred while attempting to delete product customization.', array(), 'Admin.Orderscustomers.Notification') . ' ' . $id_customization;
                                }
                            }
                        }
                        // E-mail params
                        if ((Tools::isSubmit('generateCreditSlip') || Tools::isSubmit('generateDiscount')) && !count($this->errors)) {
                            $customer = new Customer((int) ($order->id_customer));
                            $params['{lastname}'] = $customer->lastname;
                            $params['{firstname}'] = $customer->firstname;
                            $params['{id_order}'] = $order->id;
                            $params['{order_name}'] = $order->getUniqReference();
                        }

                        // Generate credit slip
                        if (Tools::isSubmit('generateCreditSlip') && !count($this->errors)) {
                            $product_list = array();
                            $amount = $order_detail->unit_price_tax_incl * $full_quantity_list[$id_order_detail];

                            $choosen = false;
                            if ((int) Tools::getValue('refund_total_voucher_off') == 1) {
                                $amount -= $voucher = (float) Tools::getValue('order_discount_price');
                            } elseif ((int) Tools::getValue('refund_total_voucher_off') == 2) {
                                $choosen = true;
                                $amount = $voucher = (float) Tools::getValue('refund_total_voucher_choose');
                            }
                            foreach ($full_product_list as $id_order_detail) {
                                $order_detail = new OrderDetail((int) $id_order_detail);
                                $product_list[$id_order_detail] = array(
                                    'id_order_detail' => $id_order_detail,
                                    'quantity' => $full_quantity_list[$id_order_detail],
                                    'unit_price' => $order_detail->unit_price_tax_excl,
                                    'amount' => isset($amount) ? $amount : $order_detail->unit_price_tax_incl * $full_quantity_list[$id_order_detail],
                                );
                            }

                            $shipping = Tools::isSubmit('shippingBack') ? null : false;

                            if (!OrderSlip::create($order, $product_list, $shipping, $voucher, $choosen)) {
                                $this->errors[] = $this->trans('A credit slip cannot be generated.', array(), 'Admin.Orderscustomers.Notification');
                            } else {
                                Hook::exec('actionOrderSlipAdd', array('order' => $order, 'productList' => $full_product_list, 'qtyList' => $full_quantity_list), null, false, true, false, $order->id_shop);
                                $orderLanguage = new Language((int) $order->id_lang);
                                @Mail::Send(
                                    (int) $order->id_lang,
                                    'credit_slip',
                                    $this->trans(
                                        'New credit slip regarding your order',
                                        array(),
                                        'Emails.Subject',
                                        $orderLanguage->locale
                                    ),
                                    $params,
                                    $customer->email,
                                    $customer->firstname . ' ' . $customer->lastname,
                                    null,
                                    null,
                                    null,
                                    null,
                                    _PS_MAIL_DIR_,
                                    true,
                                    (int) $order->id_shop
                                );
                            }
                        }

                        // Generate voucher
                        if (Tools::isSubmit('generateDiscount') && !count($this->errors)) {
                            $cartrule = new CartRule();
                            $language_ids = Language::getIDs((bool) $order);
                            $cartrule->description = $this->trans('Credit card slip for order #%d', array('#%d' => $order->id), 'Admin.Orderscustomers.Feature');
                            foreach ($language_ids as $id_lang) {
                                // Define a temporary name
                                $cartrule->name[$id_lang] = 'V0C' . (int) ($order->id_customer) . 'O' . (int) ($order->id);
                            }
                            // Define a temporary code
                            $cartrule->code = 'V0C' . (int) ($order->id_customer) . 'O' . (int) ($order->id);

                            $cartrule->quantity = 1;
                            $cartrule->quantity_per_user = 1;
                            // Specific to the customer
                            $cartrule->id_customer = $order->id_customer;
                            $now = time();
                            $cartrule->date_from = date('Y-m-d H:i:s', $now);
                            $cartrule->date_to = date('Y-m-d H:i:s', $now + (3600 * 24 * 365.25)); /* 1 year */
                            $cartrule->active = 1;

                            $products = $order->getProducts(false, $full_product_list, $full_quantity_list);

                            $total = 0;
                            foreach ($products as $product) {
                                $total += $product['unit_price_tax_incl'] * $product['product_quantity'];
                            }

                            if (Tools::isSubmit('shippingBack')) {
                                $total += $order->total_shipping;
                            }

                            if ((int) Tools::getValue('refund_total_voucher_off') == 1) {
                                $total -= (float) Tools::getValue('order_discount_price');
                            } elseif ((int) Tools::getValue('refund_total_voucher_off') == 2) {
                                $total = (float) Tools::getValue('refund_total_voucher_choose');
                            }

                            $cartrule->reduction_amount = $total;
                            $cartrule->reduction_tax = true;
                            $cartrule->minimum_amount_currency = $order->id_currency;
                            $cartrule->reduction_currency = $order->id_currency;

                            if (!$cartrule->add()) {
                                $this->errors[] = $this->trans('You cannot generate a voucher.', array(), 'Admin.Orderscustomers.Notification');
                            } else {
                                // Update the voucher code and name
                                foreach ($language_ids as $id_lang) {
                                    $cartrule->name[$id_lang] = 'V' . (int) ($cartrule->id) . 'C' . (int) ($order->id_customer) . 'O' . $order->id;
                                }
                                $cartrule->code = 'V' . (int) ($cartrule->id) . 'C' . (int) ($order->id_customer) . 'O' . $order->id;
                                if (!$cartrule->update()) {
                                    $this->errors[] = $this->trans('You cannot generate a voucher.', array(), 'Admin.Orderscustomers.Notification');
                                } else {
                                    $currency = $this->context->currency;
                                    $params['{voucher_amount}'] = Tools::displayPrice($cartrule->reduction_amount, $currency, false);
                                    $params['{voucher_num}'] = $cartrule->code;
                                    $orderLanguage = new Language((int) $order->id_lang);
                                    @Mail::Send(
                                        (int) $order->id_lang,
                                        'voucher',
                                        $this->trans(
                                            'New voucher for your order #%s',
                                            array($order->reference),
                                            'Emails.Subject',
                                            $orderLanguage->locale
                                        ),
                                        $params,
                                        $customer->email,
                                        $customer->firstname . ' ' . $customer->lastname,
                                        null,
                                        null,
                                        null,
                                        null,
                                        _PS_MAIL_DIR_,
                                        true,
                                        (int) $order->id_shop
                                    );
                                }
                            }
                        }
                    } else {
                        $this->errors[] = $this->trans('No product or quantity has been selected.', array(), 'Admin.Orderscustomers.Notification');
                    }

                    // Redirect if no errors
                    if (!count($this->errors)) {
                        Tools::redirectAdmin(self::$currentIndex . '&id_order=' . $order->id . '&vieworder&conf=31&token=' . $this->token);
                    }
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to delete this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('messageReaded')) {
            Message::markAsReaded(Tools::getValue('messageReaded'), $this->context->employee->id);
        } elseif (Tools::isSubmit('submitAddPayment') && isset($order)) {
            if ($this->access('edit')) {
                $amount = str_replace(',', '.', Tools::getValue('payment_amount'));
                $currency = new Currency(Tools::getValue('payment_currency'));
                $order_has_invoice = $order->hasInvoice();
                if ($order_has_invoice) {
                    $order_invoice = new OrderInvoice(Tools::getValue('payment_invoice'));
                } else {
                    $order_invoice = null;
                }

                if (!Validate::isLoadedObject($order)) {
                    $this->errors[] = $this->trans('The order cannot be found', array(), 'Admin.Orderscustomers.Notification');
                } elseif (!Validate::isNegativePrice($amount) || !(float) $amount) {
                    $this->errors[] = $this->trans('The amount is invalid.', array(), 'Admin.Orderscustomers.Notification');
                } elseif (!Validate::isGenericName(Tools::getValue('payment_method'))) {
                    $this->errors[] = $this->trans('The selected payment method is invalid.', array(), 'Admin.Orderscustomers.Notification');
                } elseif (!Validate::isString(Tools::getValue('payment_transaction_id'))) {
                    $this->errors[] = $this->trans('The transaction ID is invalid.', array(), 'Admin.Orderscustomers.Notification');
                } elseif (!Validate::isLoadedObject($currency)) {
                    $this->errors[] = $this->trans('The selected currency is invalid.', array(), 'Admin.Orderscustomers.Notification');
                } elseif ($order_has_invoice && !Validate::isLoadedObject($order_invoice)) {
                    $this->errors[] = $this->trans('The invoice is invalid.', array(), 'Admin.Orderscustomers.Notification');
                } elseif (!Validate::isDate(Tools::getValue('payment_date'))) {
                    $this->errors[] = $this->trans('The date is invalid', array(), 'Admin.Orderscustomers.Notification');
                } else {
                    if (!$order->addOrderPayment($amount, Tools::getValue('payment_method'), Tools::getValue('payment_transaction_id'), $currency, Tools::getValue('payment_date'), $order_invoice)) {
                        $this->errors[] = $this->trans('An error occurred during payment.', array(), 'Admin.Orderscustomers.Notification');
                    } else {
                        Tools::redirectAdmin(self::$currentIndex . '&id_order=' . $order->id . '&vieworder&conf=4&token=' . $this->token);
                    }
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitEditNote')) {
            $note = Tools::getValue('note');
            $order_invoice = new OrderInvoice((int) Tools::getValue('id_order_invoice'));
            if (Validate::isLoadedObject($order_invoice) && Validate::isCleanHtml($note)) {
                if ($this->access('edit')) {
                    $order_invoice->note = $note;
                    if ($order_invoice->save()) {
                        Tools::redirectAdmin(self::$currentIndex . '&id_order=' . $order_invoice->id_order . '&vieworder&conf=4&token=' . $this->token);
                    } else {
                        $this->errors[] = $this->trans('The invoice note was not saved.', array(), 'Admin.Orderscustomers.Notification');
                    }
                } else {
                    $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
                }
            } else {
                $this->errors[] = $this->trans('Failed to upload the invoice and edit its note.', array(), 'Admin.Orderscustomers.Notification');
            }
        } elseif (Tools::isSubmit('submitAddOrder') && ($id_cart = Tools::getValue('id_cart')) &&
            ($module_name = Tools::getValue('payment_module_name')) &&
            ($id_order_state = Tools::getValue('id_order_state')) && Validate::isModuleName($module_name)) {
            if ($this->access('edit')) {
                if (!Configuration::get('PS_CATALOG_MODE')) {
                    $payment_module = Module::getInstanceByName($module_name);
                } else {
                    $payment_module = new BoOrder();
                }

                $cart = new Cart((int) $id_cart);
                Context::getContext()->currency = new Currency((int) $cart->id_currency);
                Context::getContext()->customer = new Customer((int) $cart->id_customer);

                $bad_delivery = false;
                if (($bad_delivery = (bool) !Address::isCountryActiveById((int) $cart->id_address_delivery))
                    || !Address::isCountryActiveById((int) $cart->id_address_invoice)) {
                    if ($bad_delivery) {
                        $this->errors[] = $this->trans('This delivery address country is not active.', array(), 'Admin.Orderscustomers.Notification');
                    } else {
                        $this->errors[] = $this->trans('This invoice address country is not active.', array(), 'Admin.Orderscustomers.Notification');
                    }
                } else {
                    $employee = new Employee((int) Context::getContext()->cookie->id_employee);
                    $payment_module->validateOrder(
                        (int) $cart->id, (int) $id_order_state,
                        $cart->getOrderTotal(true, Cart::BOTH), $payment_module->displayName, $this->trans('Manual order -- Employee:', array(), 'Admin.Orderscustomers.Feature') . ' ' .
                        substr($employee->firstname, 0, 1) . '. ' . $employee->lastname, array(), null, false, $cart->secure_key
                    );
                    if ($payment_module->currentOrder) {
                        Tools::redirectAdmin(self::$currentIndex . '&id_order=' . $payment_module->currentOrder . '&vieworder' . '&token=' . $this->token);
                    }
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to add this.', array(), 'Admin.Notifications.Error');
            }
        } elseif ((Tools::isSubmit('submitAddressShipping') || Tools::isSubmit('submitAddressInvoice')) && isset($order)) {
            if ($this->access('edit')) {
                $address = new Address(Tools::getValue('id_address'));
                if (Validate::isLoadedObject($address)) {
                    // Update the address on order
                    if (Tools::isSubmit('submitAddressShipping')) {
                        $order->id_address_delivery = $address->id;
                    } elseif (Tools::isSubmit('submitAddressInvoice')) {
                        $order->id_address_invoice = $address->id;
                    }
                    $order->update();
                    $order->refreshShippingCost();

                    Tools::redirectAdmin(self::$currentIndex . '&id_order=' . $order->id . '&vieworder&conf=4&token=' . $this->token);
                } else {
                    $this->errors[] = $this->trans('This address can\'t be loaded', array(), 'Admin.Orderscustomers.Notification');
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitChangeCurrency') && isset($order)) {
            if ($this->access('edit')) {
                if (Tools::getValue('new_currency') != $order->id_currency && !$order->valid) {
                    $old_currency = new Currency($order->id_currency);
                    $currency = new Currency(Tools::getValue('new_currency'));
                    if (!Validate::isLoadedObject($currency)) {
                        throw new PrestaShopException('Can\'t load Currency object');
                    }

                    // Update order detail amount
                    foreach ($order->getOrderDetailList() as $row) {
                        $order_detail = new OrderDetail($row['id_order_detail']);
                        $fields = array(
                            'ecotax',
                            'product_price',
                            'reduction_amount',
                            'total_shipping_price_tax_excl',
                            'total_shipping_price_tax_incl',
                            'total_price_tax_incl',
                            'total_price_tax_excl',
                            'product_quantity_discount',
                            'purchase_supplier_price',
                            'reduction_amount',
                            'reduction_amount_tax_incl',
                            'reduction_amount_tax_excl',
                            'unit_price_tax_incl',
                            'unit_price_tax_excl',
                            'original_product_price',
                        );
                        foreach ($fields as $field) {
                            $order_detail->{$field} = Tools::convertPriceFull($order_detail->{$field}, $old_currency, $currency);
                        }

                        $order_detail->update();
                        $order_detail->updateTaxAmount($order);
                    }

                    $id_order_carrier = (int) $order->getIdOrderCarrier();
                    if ($id_order_carrier) {
                        $order_carrier = $order_carrier = new OrderCarrier((int) $order->getIdOrderCarrier());
                        $order_carrier->shipping_cost_tax_excl = (float) Tools::convertPriceFull($order_carrier->shipping_cost_tax_excl, $old_currency, $currency);
                        $order_carrier->shipping_cost_tax_incl = (float) Tools::convertPriceFull($order_carrier->shipping_cost_tax_incl, $old_currency, $currency);
                        $order_carrier->update();
                    }

                    // Update order && order_invoice amount
                    $fields = array(
                        'total_discounts',
                        'total_discounts_tax_incl',
                        'total_discounts_tax_excl',
                        'total_discount_tax_excl',
                        'total_discount_tax_incl',
                        'total_paid',
                        'total_paid_tax_incl',
                        'total_paid_tax_excl',
                        'total_paid_real',
                        'total_products',
                        'total_products_wt',
                        'total_shipping',
                        'total_shipping_tax_incl',
                        'total_shipping_tax_excl',
                        'total_wrapping',
                        'total_wrapping_tax_incl',
                        'total_wrapping_tax_excl',
                    );

                    $invoices = $order->getInvoicesCollection();
                    if ($invoices) {
                        foreach ($invoices as $invoice) {
                            foreach ($fields as $field) {
                                if (isset($invoice->$field)) {
                                    $invoice->{$field} = Tools::convertPriceFull($invoice->{$field}, $old_currency, $currency);
                                }
                            }
                            $invoice->save();
                        }
                    }

                    foreach ($fields as $field) {
                        if (isset($order->$field)) {
                            $order->{$field} = Tools::convertPriceFull($order->{$field}, $old_currency, $currency);
                        }
                    }

                    // Update currency in order
                    $order->id_currency = $currency->id;
                    // Update exchange rate
                    $order->conversion_rate = (float) $currency->conversion_rate;
                    $order->update();
                } else {
                    $this->errors[] = $this->trans('You cannot change the currency.', array(), 'Admin.Orderscustomers.Notification');
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitGenerateInvoice') && isset($order)) {
            if (!Configuration::get('PS_INVOICE', null, null, $order->id_shop)) {
                $this->errors[] = $this->trans('Invoice management has been disabled.', array(), 'Admin.Orderscustomers.Notification');
            } elseif ($order->hasInvoice()) {
                $this->errors[] = $this->trans('This order already has an invoice.', array(), 'Admin.Orderscustomers.Notification');
            } else {
                $order->setInvoice(true);
                Tools::redirectAdmin(self::$currentIndex . '&id_order=' . $order->id . '&vieworder&conf=4&token=' . $this->token);
            }
        } elseif (Tools::isSubmit('submitDeleteVoucher') && isset($order)) {
            if ($this->access('edit')) {
                $order_cart_rule = new OrderCartRule(Tools::getValue('id_order_cart_rule'));
                if (Validate::isLoadedObject($order_cart_rule) && $order_cart_rule->id_order == $order->id) {
                    if ($order_cart_rule->id_order_invoice) {
                        $order_invoice = new OrderInvoice($order_cart_rule->id_order_invoice);
                        if (!Validate::isLoadedObject($order_invoice)) {
                            throw new PrestaShopException('Can\'t load Order Invoice object');
                        }

                        // Update amounts of Order Invoice
                        $order_invoice->total_discount_tax_excl -= $order_cart_rule->value_tax_excl;
                        $order_invoice->total_discount_tax_incl -= $order_cart_rule->value;

                        $order_invoice->total_paid_tax_excl += $order_cart_rule->value_tax_excl;
                        $order_invoice->total_paid_tax_incl += $order_cart_rule->value;

                        // Update Order Invoice
                        $order_invoice->update();
                    }

                    // Update amounts of order
                    $order->total_discounts -= $order_cart_rule->value;
                    $order->total_discounts_tax_incl -= $order_cart_rule->value;
                    $order->total_discounts_tax_excl -= $order_cart_rule->value_tax_excl;

                    $order->total_paid += $order_cart_rule->value;
                    $order->total_paid_tax_incl += $order_cart_rule->value;
                    $order->total_paid_tax_excl += $order_cart_rule->value_tax_excl;

                    // Delete Order Cart Rule and update Order
                    $order_cart_rule->delete();
                    $order->update();
                    Tools::redirectAdmin(self::$currentIndex . '&id_order=' . $order->id . '&vieworder&conf=4&token=' . $this->token);
                } else {
                    $this->errors[] = $this->trans('You cannot edit this cart rule.', array(), 'Admin.Orderscustomers.Notification');
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitNewVoucher') && isset($order)) {
            if ($this->access('edit')) {
                if (!Tools::getValue('discount_name')) {
                    $this->errors[] = $this->trans('You must specify a name in order to create a new discount.', array(), 'Admin.Orderscustomers.Notification');
                } else {
                    if ($order->hasInvoice()) {
                        // If the discount is for only one invoice
                        if (!Tools::isSubmit('discount_all_invoices')) {
                            $order_invoice = new OrderInvoice(Tools::getValue('discount_invoice'));
                            if (!Validate::isLoadedObject($order_invoice)) {
                                throw new PrestaShopException('Can\'t load Order Invoice object');
                            }
                        }
                    }

                    $cart_rules = array();
                    $discount_value = (float) str_replace(',', '.', Tools::getValue('discount_value'));
                    switch (Tools::getValue('discount_type')) {
                        // Percent type
                        case 1:
                            if ($discount_value < 100) {
                                if (isset($order_invoice)) {
                                    $cart_rules[$order_invoice->id]['value_tax_incl'] = Tools::ps_round($order_invoice->total_paid_tax_incl * $discount_value / 100, 2);
                                    $cart_rules[$order_invoice->id]['value_tax_excl'] = Tools::ps_round($order_invoice->total_paid_tax_excl * $discount_value / 100, 2);

                                    // Update OrderInvoice
                                    $this->applyDiscountOnInvoice($order_invoice, $cart_rules[$order_invoice->id]['value_tax_incl'], $cart_rules[$order_invoice->id]['value_tax_excl']);
                                } elseif ($order->hasInvoice()) {
                                    $order_invoices_collection = $order->getInvoicesCollection();
                                    foreach ($order_invoices_collection as $order_invoice) {
                                        /* @var OrderInvoice $order_invoice */
                                        $cart_rules[$order_invoice->id]['value_tax_incl'] = Tools::ps_round($order_invoice->total_paid_tax_incl * $discount_value / 100, 2);
                                        $cart_rules[$order_invoice->id]['value_tax_excl'] = Tools::ps_round($order_invoice->total_paid_tax_excl * $discount_value / 100, 2);

                                        // Update OrderInvoice
                                        $this->applyDiscountOnInvoice($order_invoice, $cart_rules[$order_invoice->id]['value_tax_incl'], $cart_rules[$order_invoice->id]['value_tax_excl']);
                                    }
                                } else {
                                    $cart_rules[0]['value_tax_incl'] = Tools::ps_round($order->total_paid_tax_incl * $discount_value / 100, 2);
                                    $cart_rules[0]['value_tax_excl'] = Tools::ps_round($order->total_paid_tax_excl * $discount_value / 100, 2);
                                }
                            } else {
                                $this->errors[] = $this->trans('The discount value is invalid.', array(), 'Admin.Orderscustomers.Notification');
                            }
                            break;
                        // Amount type
                        case 2:
                            if (isset($order_invoice)) {
                                if ($discount_value > $order_invoice->total_paid_tax_incl) {
                                    $this->errors[] = $this->trans('The discount value is greater than the order invoice total.', array(), 'Admin.Orderscustomers.Notification');
                                } else {
                                    $cart_rules[$order_invoice->id]['value_tax_incl'] = Tools::ps_round($discount_value, 2);
                                    $cart_rules[$order_invoice->id]['value_tax_excl'] = Tools::ps_round($discount_value / (1 + ($order->getTaxesAverageUsed() / 100)), 2);

                                    // Update OrderInvoice
                                    $this->applyDiscountOnInvoice($order_invoice, $cart_rules[$order_invoice->id]['value_tax_incl'], $cart_rules[$order_invoice->id]['value_tax_excl']);
                                }
                            } elseif ($order->hasInvoice()) {
                                $order_invoices_collection = $order->getInvoicesCollection();
                                foreach ($order_invoices_collection as $order_invoice) {
                                    /** @var OrderInvoice $order_invoice */
                                    if ($discount_value > $order_invoice->total_paid_tax_incl) {
                                        $this->errors[] = $this->trans('The discount value is greater than the order invoice total.', array(), 'Admin.Orderscustomers.Notification') . $order_invoice->getInvoiceNumberFormatted(Context::getContext()->language->id, (int) $order->id_shop) . ')';
                                    } else {
                                        $cart_rules[$order_invoice->id]['value_tax_incl'] = Tools::ps_round($discount_value, 2);
                                        $cart_rules[$order_invoice->id]['value_tax_excl'] = Tools::ps_round($discount_value / (1 + ($order->getTaxesAverageUsed() / 100)), 2);

                                        // Update OrderInvoice
                                        $this->applyDiscountOnInvoice($order_invoice, $cart_rules[$order_invoice->id]['value_tax_incl'], $cart_rules[$order_invoice->id]['value_tax_excl']);
                                    }
                                }
                            } else {
                                if ($discount_value > $order->total_paid_tax_incl) {
                                    $this->errors[] = $this->trans('The discount value is greater than the order total.', array(), 'Admin.Orderscustomers.Notification');
                                } else {
                                    $cart_rules[0]['value_tax_incl'] = Tools::ps_round($discount_value, 2);
                                    $cart_rules[0]['value_tax_excl'] = Tools::ps_round($discount_value / (1 + ($order->getTaxesAverageUsed() / 100)), 2);
                                }
                            }
                            break;
                        // Free shipping type
                        case 3:
                            if (isset($order_invoice)) {
                                if ($order_invoice->total_shipping_tax_incl > 0) {
                                    $cart_rules[$order_invoice->id]['value_tax_incl'] = $order_invoice->total_shipping_tax_incl;
                                    $cart_rules[$order_invoice->id]['value_tax_excl'] = $order_invoice->total_shipping_tax_excl;

                                    // Update OrderInvoice
                                    $this->applyDiscountOnInvoice($order_invoice, $cart_rules[$order_invoice->id]['value_tax_incl'], $cart_rules[$order_invoice->id]['value_tax_excl']);
                                }
                            } elseif ($order->hasInvoice()) {
                                $order_invoices_collection = $order->getInvoicesCollection();
                                foreach ($order_invoices_collection as $order_invoice) {
                                    /** @var OrderInvoice $order_invoice */
                                    if ($order_invoice->total_shipping_tax_incl <= 0) {
                                        continue;
                                    }
                                    $cart_rules[$order_invoice->id]['value_tax_incl'] = $order_invoice->total_shipping_tax_incl;
                                    $cart_rules[$order_invoice->id]['value_tax_excl'] = $order_invoice->total_shipping_tax_excl;

                                    // Update OrderInvoice
                                    $this->applyDiscountOnInvoice($order_invoice, $cart_rules[$order_invoice->id]['value_tax_incl'], $cart_rules[$order_invoice->id]['value_tax_excl']);
                                }
                            } else {
                                $cart_rules[0]['value_tax_incl'] = $order->total_shipping_tax_incl;
                                $cart_rules[0]['value_tax_excl'] = $order->total_shipping_tax_excl;
                            }
                            break;
                        default:
                            $this->errors[] = $this->trans('The discount type is invalid.', array(), 'Admin.Orderscustomers.Notification');
                    }

                    $res = true;
                    foreach ($cart_rules as &$cart_rule) {
                        $cartRuleObj = new CartRule();
                        $cartRuleObj->date_from = date('Y-m-d H:i:s', strtotime('-1 hour', strtotime($order->date_add)));
                        $cartRuleObj->date_to = date('Y-m-d H:i:s', strtotime('+1 hour'));
                        $cartRuleObj->name[Configuration::get('PS_LANG_DEFAULT')] = Tools::getValue('discount_name');
                        $cartRuleObj->quantity = 0;
                        $cartRuleObj->quantity_per_user = 1;
                        if (Tools::getValue('discount_type') == 1) {
                            $cartRuleObj->reduction_percent = $discount_value;
                        } elseif (Tools::getValue('discount_type') == 2) {
                            $cartRuleObj->reduction_amount = $cart_rule['value_tax_excl'];
                        } elseif (Tools::getValue('discount_type') == 3) {
                            $cartRuleObj->free_shipping = 1;
                        }
                        $cartRuleObj->active = 0;
                        if ($res = $cartRuleObj->add()) {
                            $cart_rule['id'] = $cartRuleObj->id;
                        } else {
                            break;
                        }
                    }

                    if ($res) {
                        foreach ($cart_rules as $id_order_invoice => $cart_rule) {
                            // Create OrderCartRule
                            $order_cart_rule = new OrderCartRule();
                            $order_cart_rule->id_order = $order->id;
                            $order_cart_rule->id_cart_rule = $cart_rule['id'];
                            $order_cart_rule->id_order_invoice = $id_order_invoice;
                            $order_cart_rule->name = Tools::getValue('discount_name');
                            $order_cart_rule->value = $cart_rule['value_tax_incl'];
                            $order_cart_rule->value_tax_excl = $cart_rule['value_tax_excl'];
                            $res &= $order_cart_rule->add();

                            $order->total_discounts += $order_cart_rule->value;
                            $order->total_discounts_tax_incl += $order_cart_rule->value;
                            $order->total_discounts_tax_excl += $order_cart_rule->value_tax_excl;
                            $order->total_paid -= $order_cart_rule->value;
                            $order->total_paid_tax_incl -= $order_cart_rule->value;
                            $order->total_paid_tax_excl -= $order_cart_rule->value_tax_excl;
                        }

                        // Update Order
                        $res &= $order->update();
                    }

                    if ($res) {
                        Tools::redirectAdmin(self::$currentIndex . '&id_order=' . $order->id . '&vieworder&conf=4&token=' . $this->token);
                    } else {
                        $this->errors[] = $this->trans('An error occurred during the OrderCartRule creation', array(), 'Admin.Orderscustomers.Notification');
                    }
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('sendStateEmail') && Tools::getValue('sendStateEmail') > 0 && Tools::getValue('id_order') > 0) {
            if ($this->access('edit')) {
                $order_state = new OrderState((int) Tools::getValue('sendStateEmail'));

                if (!Validate::isLoadedObject($order_state)) {
                    $this->errors[] = $this->trans('An error occurred while loading order status.', array(), 'Admin.Orderscustomers.Notification');
                } else {
                    $history = new OrderHistory((int) Tools::getValue('id_order_history'));

                    $carrier = new Carrier($order->id_carrier, $order->id_lang);
                    $templateVars = array();
                    if ($order_state->id == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number) {
                        $templateVars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));
                    }

                    if ($history->sendEmail($order, $templateVars)) {
                        Tools::redirectAdmin(self::$currentIndex . '&id_order=' . $order->id . '&vieworder&conf=10&token=' . $this->token);
                    } else {
                        $this->errors[] = $this->trans('An error occurred while sending the e-mail to the customer.', array(), 'Admin.Orderscustomers.Notification');
                    }
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        }

        parent::postProcess();
    }

    public function renderKpis()
    {
        $time = time();
        $kpis = array();

        /* The data generation is located in AdminStatsControllerCore */

        $helper = new HelperKpi();
        $helper->id = 'box-conversion-rate';
        $helper->icon = 'icon-sort-by-attributes-alt';
        //$helper->chart = true;
        $helper->color = 'color1';
        $helper->title = $this->trans('Conversion Rate', array(), 'Admin.Global');
        $helper->subtitle = $this->trans('30 days', array(), 'Admin.Global');
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
        $helper->title = $this->trans('Abandoned Carts', array(), 'Admin.Global');
        $helper->subtitle = $this->trans('Today', array(), 'Admin.Global');
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
        $helper->title = $this->trans('Average Order Value', array(), 'Admin.Global');
        $helper->subtitle = $this->trans('30 days', array(), 'Admin.Global');
        if (ConfigurationKPI::get('AVG_ORDER_VALUE') !== false) {
            $helper->value = $this->trans('%amount% tax excl.', array('%amount%' => ConfigurationKPI::get('AVG_ORDER_VALUE')), 'Admin.Orderscustomers.Feature');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats') . '&ajax=1&action=getKpi&kpi=average_order_value';
        $helper->refresh = (bool) (ConfigurationKPI::get('AVG_ORDER_VALUE_EXPIRE') < $time);
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-net-profit-visit';
        $helper->icon = 'icon-user';
        $helper->color = 'color4';
        $helper->title = $this->trans('Net Profit per Visit', array(), 'Admin.Orderscustomers.Feature');
        $helper->subtitle = $this->trans('30 days', array(), 'Admin.Orderscustomers.Feature');
        if (ConfigurationKPI::get('NETPROFIT_VISIT') !== false) {
            $helper->value = ConfigurationKPI::get('NETPROFIT_VISIT');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats') . '&ajax=1&action=getKpi&kpi=netprofit_visit';
        $helper->refresh = (bool) (ConfigurationKPI::get('NETPROFIT_VISIT_EXPIRE') < $time);
        $kpis[] = $helper->generate();

        $helper = new HelperKpiRow();
        $helper->kpis = $kpis;

        return $helper->generate();
    }

    public function renderView()
    {
        $order = new Order(Tools::getValue('id_order'));
        if (!Validate::isLoadedObject($order)) {
            $this->errors[] = $this->trans('The order cannot be found within your database.', array(), 'Admin.Orderscustomers.Notification');
        }

        $customer = new Customer($order->id_customer);
        $carrier = new Carrier($order->id_carrier);
        $products = $this->getProducts($order);
        $currency = new Currency((int) $order->id_currency);
        // Carrier module call
        $carrier_module_call = null;
        if ($carrier->is_module) {
            $module = Module::getInstanceByName($carrier->external_module_name);
            if (method_exists($module, 'displayInfoByCart')) {
                $carrier_module_call = call_user_func(array($module, 'displayInfoByCart'), $order->id_cart);
            }
        }

        // Retrieve addresses information
        $addressInvoice = new Address($order->id_address_invoice, $this->context->language->id);
        if (Validate::isLoadedObject($addressInvoice) && $addressInvoice->id_state) {
            $invoiceState = new State((int) $addressInvoice->id_state);
        }

        if ($order->id_address_invoice == $order->id_address_delivery) {
            $addressDelivery = $addressInvoice;
            if (isset($invoiceState)) {
                $deliveryState = $invoiceState;
            }
        } else {
            $addressDelivery = new Address($order->id_address_delivery, $this->context->language->id);
            if (Validate::isLoadedObject($addressDelivery) && $addressDelivery->id_state) {
                $deliveryState = new State((int) ($addressDelivery->id_state));
            }
        }

        $this->toolbar_title = $this->trans(
            'Order #%id% (%ref%) - %firstname% %lastname%',
            array(
                '%id%' => $order->id,
                '%ref%' => $order->reference,
                '%firstname%' => $customer->firstname,
                '%lastname%' => $customer->lastname,
            ),
            'Admin.Orderscustomers.Feature'
        );
        if (Shop::isFeatureActive()) {
            $shop = new Shop((int) $order->id_shop);
            $this->toolbar_title .= ' - ' . $this->trans('Shop: %shop_name%', array('%shop_name%' => $shop->name), 'Admin.Orderscustomers.Feature');
        }

        // gets warehouses to ship products, if and only if advanced stock management is activated
        $warehouse_list = null;

        $order_details = $order->getOrderDetailList();
        foreach ($order_details as $order_detail) {
            $product = new Product($order_detail['product_id']);

            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')
                && $product->advanced_stock_management) {
                $warehouses = Warehouse::getWarehousesByProductId($order_detail['product_id'], $order_detail['product_attribute_id']);
                foreach ($warehouses as $warehouse) {
                    if (!isset($warehouse_list[$warehouse['id_warehouse']])) {
                        $warehouse_list[$warehouse['id_warehouse']] = $warehouse;
                    }
                }
            }
        }

        $payment_methods = array();
        foreach (PaymentModule::getInstalledPaymentModules() as $payment) {
            $module = Module::getInstanceByName($payment['name']);
            if (Validate::isLoadedObject($module) && $module->active) {
                $payment_methods[] = $module->displayName;
            }
        }

        // display warning if there are products out of stock
        $display_out_of_stock_warning = false;
        $current_order_state = $order->getCurrentOrderState();
        if (Configuration::get('PS_STOCK_MANAGEMENT') && (!Validate::isLoadedObject($current_order_state) || ($current_order_state->delivery != 1 && $current_order_state->shipped != 1))) {
            $display_out_of_stock_warning = true;
        }

        // products current stock informations (from stock_available)
        $stockLocationIsAvailable = false;
        foreach ($products as &$product) {
            // Get total customized quantity for current product
            $customized_product_quantity = 0;

            if (is_array($product['customizedDatas'])) {
                foreach ($product['customizedDatas'] as $customizationPerAddress) {
                    foreach ($customizationPerAddress as $customizationId => $customization) {
                        $customized_product_quantity += (int) $customization['quantity'];
                    }
                }
            }

            $product['customized_product_quantity'] = $customized_product_quantity;
            $product['current_stock'] = StockAvailable::getQuantityAvailableByProduct($product['product_id'], $product['product_attribute_id'], $product['id_shop']);
            $resume = OrderSlip::getProductSlipResume($product['id_order_detail']);
            $product['quantity_refundable'] = $product['product_quantity'] - $resume['product_quantity'];
            $product['amount_refundable'] = $product['total_price_tax_excl'] - $resume['amount_tax_excl'];
            $product['amount_refundable_tax_incl'] = $product['total_price_tax_incl'] - $resume['amount_tax_incl'];
            $product['amount_refund'] = $order->getTaxCalculationMethod() ? Tools::displayPrice($resume['amount_tax_excl'], $currency) : Tools::displayPrice($resume['amount_tax_incl'], $currency);
            $product['refund_history'] = OrderSlip::getProductSlipDetail($product['id_order_detail']);
            $product['return_history'] = OrderReturn::getProductReturnDetail($product['id_order_detail']);

            // if the current stock requires a warning
            if ($product['current_stock'] <= 0 && $display_out_of_stock_warning) {
                $this->displayWarning($this->trans('This product is out of stock: ', array(), 'Admin.Orderscustomers.Notification') . ' ' . $product['product_name']);
            }
            if ($product['id_warehouse'] != 0) {
                $warehouse = new Warehouse((int) $product['id_warehouse']);
                $product['warehouse_name'] = $warehouse->name;
                $warehouse_location = WarehouseProductLocation::getProductLocation($product['product_id'], $product['product_attribute_id'], $product['id_warehouse']);
                if (!empty($warehouse_location)) {
                    $product['warehouse_location'] = $warehouse_location;
                } else {
                    $product['warehouse_location'] = false;
                }
            } else {
                $product['warehouse_name'] = '--';
                $product['warehouse_location'] = false;
            }

            if (!empty($product['location'])) {
                $stockLocationIsAvailable = true;
            }
        }

        // Package management for order
        foreach ($products as &$product) {
            $pack_items = $product['cache_is_pack'] ? Pack::getItemTable($product['id_product'], $this->context->language->id, true) : array();
            foreach ($pack_items as &$pack_item) {
                $pack_item['current_stock'] = StockAvailable::getQuantityAvailableByProduct($pack_item['id_product'], $pack_item['id_product_attribute'], $pack_item['id_shop']);
                // if the current stock requires a warning
                if ($product['current_stock'] <= 0 && $display_out_of_stock_warning) {
                    $this->displayWarning($this->trans('This product, included in package (' . $product['product_name'] . ') is out of stock: ', array(), 'Admin.Orderscustomers.Notification') . ' ' . $pack_item['product_name']);
                }
                $this->setProductImageInformations($pack_item);
                if ($pack_item['image'] != null) {
                    $name = 'product_mini_' . (int) $pack_item['id_product'] . (isset($pack_item['id_product_attribute']) ? '_' . (int) $pack_item['id_product_attribute'] : '') . '.jpg';
                    // generate image cache, only for back office
                    $pack_item['image_tag'] = ImageManager::thumbnail(_PS_IMG_DIR_ . 'p/' . $pack_item['image']->getExistingImgPath() . '.jpg', $name, 45, 'jpg');
                    if (file_exists(_PS_TMP_IMG_DIR_ . $name)) {
                        $pack_item['image_size'] = getimagesize(_PS_TMP_IMG_DIR_ . $name);
                    } else {
                        $pack_item['image_size'] = false;
                    }
                }
            }
            $product['pack_items'] = $pack_items;
        }

        $gender = new Gender((int) $customer->id_gender, $this->context->language->id);

        $history = $order->getHistory($this->context->language->id);

        foreach ($history as &$order_state) {
            $order_state['text-color'] = Tools::getBrightness($order_state['color']) < 128 ? 'white' : 'black';
        }

        $shipping_refundable_tax_excl = $order->total_shipping_tax_excl;
        $shipping_refundable_tax_incl = $order->total_shipping_tax_incl;
        $slips = OrderSlip::getOrdersSlip($customer->id, $order->id);
        foreach ($slips as $slip) {
            $shipping_refundable_tax_excl -= $slip['total_shipping_tax_excl'];
            $shipping_refundable_tax_incl -= $slip['total_shipping_tax_incl'];
        }
        $shipping_refundable_tax_excl = max(0, $shipping_refundable_tax_excl);
        $shipping_refundable_tax_incl = max(0, $shipping_refundable_tax_incl);

        // Smarty assign
        $this->tpl_view_vars = array(
            'order' => $order,
            'cart' => new Cart($order->id_cart),
            'customer' => $customer,
            'gender' => $gender,
            'customer_addresses' => $customer->getAddresses($this->context->language->id),
            'addresses' => array(
                'delivery' => $addressDelivery,
                'deliveryState' => isset($deliveryState) ? $deliveryState : null,
                'invoice' => $addressInvoice,
                'invoiceState' => isset($invoiceState) ? $invoiceState : null,
            ),
            'customerStats' => $customer->getStats(),
            'products' => $products,
            'discounts' => $order->getCartRules(),
            'orders_total_paid_tax_incl' => $order->getOrdersTotalPaid(), // Get the sum of total_paid_tax_incl of the order with similar reference
            'total_paid' => $order->getTotalPaid(),
            'returns' => OrderReturn::getOrdersReturn($order->id_customer, $order->id),
            'shipping_refundable_tax_excl' => $shipping_refundable_tax_excl,
            'shipping_refundable_tax_incl' => $shipping_refundable_tax_incl,
            'customer_thread_message' => CustomerThread::getCustomerMessages($order->id_customer, null, $order->id),
            'orderMessages' => OrderMessage::getOrderMessages($order->id_lang),
            'messages' => CustomerThread::getCustomerMessagesOrder($order->id_customer, $order->id),
            'carrier' => new Carrier($order->id_carrier),
            'history' => $history,
            'states' => OrderState::getOrderStates($this->context->language->id),
            'warehouse_list' => $warehouse_list,
            'sources' => ConnectionsSource::getOrderSources($order->id),
            'currentState' => $order->getCurrentOrderState(),
            'currency' => new Currency($order->id_currency),
            'currencies' => Currency::getCurrenciesByIdShop($order->id_shop),
            'previousOrder' => $order->getPreviousOrderId(),
            'nextOrder' => $order->getNextOrderId(),
            'current_index' => self::$currentIndex,
            'carrierModuleCall' => $carrier_module_call,
            'iso_code_lang' => $this->context->language->iso_code,
            'id_lang' => $this->context->language->id,
            'can_edit' => ($this->access('edit')),
            'current_id_lang' => $this->context->language->id,
            'invoices_collection' => $order->getInvoicesCollection(),
            'not_paid_invoices_collection' => $order->getNotPaidInvoicesCollection(),
            'payment_methods' => $payment_methods,
            'invoice_management_active' => Configuration::get('PS_INVOICE', null, null, $order->id_shop),
            'display_warehouse' => (int) Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'),
            'carrier_list' => $this->getCarrierList($order),
            'recalculate_shipping_cost' => (int) Configuration::get('PS_ORDER_RECALCULATE_SHIPPING'),
            'stock_location_is_available' => $stockLocationIsAvailable,
            'HOOK_CONTENT_ORDER' => Hook::exec('displayAdminOrderContentOrder', array(
                'order' => $order,
                'products' => $products,
                'customer' => $customer, )
            ),
            'HOOK_CONTENT_SHIP' => Hook::exec('displayAdminOrderContentShip', array(
                'order' => $order,
                'products' => $products,
                'customer' => $customer, )
            ),
            'HOOK_TAB_ORDER' => Hook::exec('displayAdminOrderTabOrder', array(
                'order' => $order,
                'products' => $products,
                'customer' => $customer, )
            ),
            'HOOK_TAB_SHIP' => Hook::exec('displayAdminOrderTabShip', array(
                'order' => $order,
                'products' => $products,
                'customer' => $customer, )
            ),
        );

        return parent::renderView();
    }

    public function ajaxProcessSearchProducts()
    {
        Context::getContext()->customer = new Customer((int) Tools::getValue('id_customer'));
        $currency = new Currency((int) Tools::getValue('id_currency'));
        if ($products = Product::searchByName((int) $this->context->language->id, pSQL(Tools::getValue('product_search')))) {
            foreach ($products as &$product) {
                // Formatted price
                $product['formatted_price'] = Tools::displayPrice(Tools::convertPrice($product['price_tax_incl'], $currency), $currency);
                // Concret price
                $product['price_tax_incl'] = Tools::ps_round(Tools::convertPrice($product['price_tax_incl'], $currency), 2);
                $product['price_tax_excl'] = Tools::ps_round(Tools::convertPrice($product['price_tax_excl'], $currency), 2);
                $productObj = new Product((int) $product['id_product'], false, (int) $this->context->language->id);
                $combinations = array();
                $attributes = $productObj->getAttributesGroups((int) $this->context->language->id);

                // Tax rate for this customer
                if (Tools::isSubmit('id_address')) {
                    $product['tax_rate'] = $productObj->getTaxesRate(new Address(Tools::getValue('id_address')));
                }

                $product['warehouse_list'] = array();

                foreach ($attributes as $attribute) {
                    if (!isset($combinations[$attribute['id_product_attribute']]['attributes'])) {
                        $combinations[$attribute['id_product_attribute']]['attributes'] = '';
                    }
                    $combinations[$attribute['id_product_attribute']]['attributes'] .= $attribute['attribute_name'] . ' - ';
                    $combinations[$attribute['id_product_attribute']]['id_product_attribute'] = $attribute['id_product_attribute'];
                    $combinations[$attribute['id_product_attribute']]['default_on'] = $attribute['default_on'];
                    if (!isset($combinations[$attribute['id_product_attribute']]['price'])) {
                        $price_tax_incl = Product::getPriceStatic((int) $product['id_product'], true, $attribute['id_product_attribute']);
                        $price_tax_excl = Product::getPriceStatic((int) $product['id_product'], false, $attribute['id_product_attribute']);
                        $combinations[$attribute['id_product_attribute']]['price_tax_incl'] = Tools::ps_round(Tools::convertPrice($price_tax_incl, $currency), 2);
                        $combinations[$attribute['id_product_attribute']]['price_tax_excl'] = Tools::ps_round(Tools::convertPrice($price_tax_excl, $currency), 2);
                        $combinations[$attribute['id_product_attribute']]['formatted_price'] = Tools::displayPrice(Tools::convertPrice($price_tax_excl, $currency), $currency);
                    }
                    if (!isset($combinations[$attribute['id_product_attribute']]['qty_in_stock'])) {
                        $combinations[$attribute['id_product_attribute']]['qty_in_stock'] = StockAvailable::getQuantityAvailableByProduct((int) $product['id_product'], $attribute['id_product_attribute'], (int) $this->context->shop->id);
                    }

                    if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && (int) $product['advanced_stock_management'] == 1) {
                        $product['warehouse_list'][$attribute['id_product_attribute']] = Warehouse::getProductWarehouseList($product['id_product'], $attribute['id_product_attribute']);
                    } else {
                        $product['warehouse_list'][$attribute['id_product_attribute']] = array();
                    }

                    $product['stock'][$attribute['id_product_attribute']] = Product::getRealQuantity($product['id_product'], $attribute['id_product_attribute']);
                }

                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && (int) $product['advanced_stock_management'] == 1) {
                    $product['warehouse_list'][0] = Warehouse::getProductWarehouseList($product['id_product']);
                } else {
                    $product['warehouse_list'][0] = array();
                }

                $product['stock'][0] = StockAvailable::getQuantityAvailableByProduct((int) $product['id_product'], 0, (int) $this->context->shop->id);

                foreach ($combinations as &$combination) {
                    $combination['attributes'] = rtrim($combination['attributes'], ' - ');
                }
                $product['combinations'] = $combinations;

                if ($product['customizable']) {
                    $product_instance = new Product((int) $product['id_product']);
                    $product['customization_fields'] = $product_instance->getCustomizationFields($this->context->language->id);
                }
            }

            $to_return = array(
                'products' => $products,
                'found' => true,
            );
        } else {
            $to_return = array('found' => false);
        }

        $this->content = json_encode($to_return);
    }

    public function ajaxProcessSendMailValidateOrder()
    {
        if ($this->access('edit')) {
            $cart = new Cart((int) Tools::getValue('id_cart'));
            if (Validate::isLoadedObject($cart)) {
                $customer = new Customer((int) $cart->id_customer);
                if (Validate::isLoadedObject($customer)) {
                    $mailVars = array(
                        '{order_link}' => Context::getContext()->link->getPageLink('order', false, (int) $cart->id_lang, 'step=3&recover_cart=' . (int) $cart->id . '&token_cart=' . md5(_COOKIE_KEY_ . 'recover_cart_' . (int) $cart->id)),
                        '{firstname}' => $customer->firstname,
                        '{lastname}' => $customer->lastname,
                    );
                    $cartLanguage = new Language((int) $cart->id_lang);
                    if (
                        Mail::Send(
                            (int) $cart->id_lang,
                            'backoffice_order',
                            $this->trans(
                                'Process the payment of your order',
                                array(),
                                'Emails.Subject',
                                $cartLanguage->locale
                            ),
                            $mailVars,
                            $customer->email,
                            $customer->firstname . ' ' . $customer->lastname,
                            null,
                            null,
                            null,
                            null,
                            _PS_MAIL_DIR_,
                            true,
                            $cart->id_shop)
                    ) {
                        die(json_encode(array('errors' => false, 'result' => $this->trans('The email was sent to your customer.', array(), 'Admin.Orderscustomers.Notification'))));
                    }
                }
            }
            $this->content = json_encode(array('errors' => true, 'result' => $this->trans('Error in sending the email to your customer.', array(), 'Admin.Orderscustomers.Notification')));
        }
    }

    public function ajaxProcessAddProductOnOrder()
    {
        // Load object
        $order = new Order((int) Tools::getValue('id_order'));
        if (!Validate::isLoadedObject($order)) {
            die(json_encode(array(
                'result' => false,
                'error' => $this->trans('The order object cannot be loaded.', array(), 'Admin.Orderscustomers.Notification'),
            )));
        }

        $old_cart_rules = Context::getContext()->cart->getCartRules();

        if ($order->hasBeenShipped()) {
            die(json_encode(array(
                'result' => false,
                'error' => $this->trans('You cannot add products to delivered orders.', array(), 'Admin.Orderscustomers.Notification'),
            )));
        }

        $product_informations = $_POST['add_product'];
        if (isset($_POST['add_invoice'])) {
            $invoice_informations = $_POST['add_invoice'];
        } else {
            $invoice_informations = array();
        }
        $product = new Product($product_informations['product_id'], false, $order->id_lang);
        if (!Validate::isLoadedObject($product)) {
            die(json_encode(array(
                'result' => false,
                'error' => $this->trans('The product object cannot be loaded.', array(), 'Admin.Orderscustomers.Notification'),
            )));
        }

        if (isset($product_informations['product_attribute_id']) && $product_informations['product_attribute_id']) {
            $combination = new Combination($product_informations['product_attribute_id']);
            if (!Validate::isLoadedObject($combination)) {
                die(json_encode(array(
                    'result' => false,
                    'error' => $this->trans('The combination object cannot be loaded.', array(), 'Admin.Orderscustomers.Notification'),
                )));
            }
        }

        // Total method
        $total_method = Cart::BOTH_WITHOUT_SHIPPING;

        // Create new cart
        $cart = new Cart();
        $cart->id_shop_group = $order->id_shop_group;
        $cart->id_shop = $order->id_shop;
        $cart->id_customer = $order->id_customer;
        $cart->id_carrier = $order->id_carrier;
        $cart->id_address_delivery = $order->id_address_delivery;
        $cart->id_address_invoice = $order->id_address_invoice;
        $cart->id_currency = $order->id_currency;
        $cart->id_lang = $order->id_lang;
        $cart->secure_key = $order->secure_key;

        // Save new cart
        $cart->add();

        // Save context (in order to apply cart rule)
        $this->context->cart = $cart;
        $this->context->customer = new Customer($order->id_customer);

        // always add taxes even if there are not displayed to the customer
        $use_taxes = true;

        $initial_product_price_tax_incl = Product::getPriceStatic($product->id, $use_taxes, isset($combination) ? $combination->id : null, 2, null, false, true, 1,
            false, $order->id_customer, $cart->id, $order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)});

        // Creating specific price if needed
        if ($product_informations['product_price_tax_incl'] != $initial_product_price_tax_incl) {
            $specific_price = new SpecificPrice();
            $specific_price->id_shop = 0;
            $specific_price->id_shop_group = 0;
            $specific_price->id_currency = 0;
            $specific_price->id_country = 0;
            $specific_price->id_group = 0;
            $specific_price->id_customer = $order->id_customer;
            $specific_price->id_product = $product->id;
            if (isset($combination)) {
                $specific_price->id_product_attribute = $combination->id;
            } else {
                $specific_price->id_product_attribute = 0;
            }
            $specific_price->price = $product_informations['product_price_tax_excl'];
            $specific_price->from_quantity = 1;
            $specific_price->reduction = 0;
            $specific_price->reduction_type = 'amount';
            $specific_price->reduction_tax = 0;
            $specific_price->from = '0000-00-00 00:00:00';
            $specific_price->to = '0000-00-00 00:00:00';
            $specific_price->add();
        }

        // Add product to cart
        $update_quantity = $cart->updateQty($product_informations['product_quantity'], $product->id, isset($product_informations['product_attribute_id']) ? $product_informations['product_attribute_id'] : null,
            isset($combination) ? $combination->id : null, 'up', 0, new Shop($cart->id_shop));

        if ($update_quantity < 0) {
            // If product has attribute, minimal quantity is set with minimal quantity of attribute
            $minimal_quantity = ($product_informations['product_attribute_id']) ? Attribute::getAttributeMinimalQty($product_informations['product_attribute_id']) : $product->minimal_quantity;
            die(json_encode(array('error' => $this->trans('You must add %d minimum quantity', array('%d' => $minimal_quantity), 'Admin.Orderscustomers.Notification'))));
        } elseif (!$update_quantity) {
            die(json_encode(array('error' => $this->trans('You already have the maximum quantity available for this product.', array(), 'Admin.Orderscustomers.Notification'))));
        }

        // If order is valid, we can create a new invoice or edit an existing invoice
        if ($order->hasInvoice()) {
            $order_invoice = new OrderInvoice($product_informations['invoice']);
            // Create new invoice
            if ($order_invoice->id == 0) {
                // If we create a new invoice, we calculate shipping cost
                $total_method = Cart::BOTH;
                // Create Cart rule in order to make free shipping
                if (isset($invoice_informations['free_shipping']) && $invoice_informations['free_shipping']) {
                    $cart_rule = new CartRule();
                    $cart_rule->id_customer = $order->id_customer;
                    $cart_rule->name = array(
                        Configuration::get('PS_LANG_DEFAULT') => $this->trans('[Generated] CartRule for Free Shipping', array(), 'Admin.Orderscustomers.Notification'),
                    );
                    $cart_rule->date_from = date('Y-m-d H:i:s', time());
                    $cart_rule->date_to = date('Y-m-d H:i:s', time() + 24 * 3600);
                    $cart_rule->quantity = 1;
                    $cart_rule->quantity_per_user = 1;
                    $cart_rule->minimum_amount_currency = $order->id_currency;
                    $cart_rule->reduction_currency = $order->id_currency;
                    $cart_rule->free_shipping = true;
                    $cart_rule->active = 1;
                    $cart_rule->add();

                    // Add cart rule to cart and in order
                    $cart->addCartRule($cart_rule->id);
                    $values = array(
                        'tax_incl' => $cart_rule->getContextualValue(true),
                        'tax_excl' => $cart_rule->getContextualValue(false),
                    );
                    $order->addCartRule($cart_rule->id, $cart_rule->name[Configuration::get('PS_LANG_DEFAULT')], $values);
                }

                $order_invoice->id_order = $order->id;
                if ($order_invoice->number) {
                    Configuration::updateValue('PS_INVOICE_START_NUMBER', false, false, null, $order->id_shop);
                } else {
                    $order_invoice->number = Order::getLastInvoiceNumber() + 1;
                }

                $invoice_address = new Address((int) $order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)});
                $carrier = new Carrier((int) $order->id_carrier);
                $tax_calculator = $carrier->getTaxCalculator($invoice_address);

                $order_invoice->total_paid_tax_excl = Tools::ps_round((float) $cart->getOrderTotal(false, $total_method), 2);
                $order_invoice->total_paid_tax_incl = Tools::ps_round((float) $cart->getOrderTotal($use_taxes, $total_method), 2);
                $order_invoice->total_products = (float) $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
                $order_invoice->total_products_wt = (float) $cart->getOrderTotal($use_taxes, Cart::ONLY_PRODUCTS);
                $order_invoice->total_shipping_tax_excl = (float) $cart->getTotalShippingCost(null, false);
                $order_invoice->total_shipping_tax_incl = (float) $cart->getTotalShippingCost();

                $order_invoice->total_wrapping_tax_excl = abs($cart->getOrderTotal(false, Cart::ONLY_WRAPPING));
                $order_invoice->total_wrapping_tax_incl = abs($cart->getOrderTotal($use_taxes, Cart::ONLY_WRAPPING));
                $order_invoice->shipping_tax_computation_method = (int) $tax_calculator->computation_method;

                // Update current order field, only shipping because other field is updated later
                $order->total_shipping += $order_invoice->total_shipping_tax_incl;
                $order->total_shipping_tax_excl += $order_invoice->total_shipping_tax_excl;
                $order->total_shipping_tax_incl += ($use_taxes) ? $order_invoice->total_shipping_tax_incl : $order_invoice->total_shipping_tax_excl;

                $order->total_wrapping += abs($cart->getOrderTotal($use_taxes, Cart::ONLY_WRAPPING));
                $order->total_wrapping_tax_excl += abs($cart->getOrderTotal(false, Cart::ONLY_WRAPPING));
                $order->total_wrapping_tax_incl += abs($cart->getOrderTotal($use_taxes, Cart::ONLY_WRAPPING));
                $order_invoice->add();

                $order_invoice->saveCarrierTaxCalculator($tax_calculator->getTaxesAmount($order_invoice->total_shipping_tax_excl));

                $order_carrier = new OrderCarrier();
                $order_carrier->id_order = (int) $order->id;
                $order_carrier->id_carrier = (int) $order->id_carrier;
                $order_carrier->id_order_invoice = (int) $order_invoice->id;
                $order_carrier->weight = (float) $cart->getTotalWeight();
                $order_carrier->shipping_cost_tax_excl = (float) $order_invoice->total_shipping_tax_excl;
                $order_carrier->shipping_cost_tax_incl = ($use_taxes) ? (float) $order_invoice->total_shipping_tax_incl : (float) $order_invoice->total_shipping_tax_excl;
                $order_carrier->add();
            } else {
                // Update current invoice
                $order_invoice->total_paid_tax_excl += Tools::ps_round((float) ($cart->getOrderTotal(false, $total_method)), 2);
                $order_invoice->total_paid_tax_incl += Tools::ps_round((float) ($cart->getOrderTotal($use_taxes, $total_method)), 2);
                $order_invoice->total_products += (float) $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
                $order_invoice->total_products_wt += (float) $cart->getOrderTotal($use_taxes, Cart::ONLY_PRODUCTS);
                $order_invoice->update();
            }
        }

        // Create Order detail information
        $order_detail = new OrderDetail();
        $order_detail->createList($order, $cart, $order->getCurrentOrderState(), $cart->getProducts(), (isset($order_invoice) ? $order_invoice->id : 0), $use_taxes, (int) Tools::getValue('add_product_warehouse'));

        // update totals amount of order
        $order->total_products += (float) $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
        $order->total_products_wt += (float) $cart->getOrderTotal($use_taxes, Cart::ONLY_PRODUCTS);

        $order->total_paid += Tools::ps_round((float) ($cart->getOrderTotal(true, $total_method)), 2);
        $order->total_paid_tax_excl += Tools::ps_round((float) ($cart->getOrderTotal(false, $total_method)), 2);
        $order->total_paid_tax_incl += Tools::ps_round((float) ($cart->getOrderTotal($use_taxes, $total_method)), 2);

        if (isset($order_invoice) && Validate::isLoadedObject($order_invoice)) {
            $order->total_shipping = $order_invoice->total_shipping_tax_incl;
            $order->total_shipping_tax_incl = $order_invoice->total_shipping_tax_incl;
            $order->total_shipping_tax_excl = $order_invoice->total_shipping_tax_excl;
        }
        // discount
        $order->total_discounts += (float) abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));
        $order->total_discounts_tax_excl += (float) abs($cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS));
        $order->total_discounts_tax_incl += (float) abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));

        // Save changes of order
        $order->update();

        StockAvailable::synchronize($product->id);

        // Update weight SUM
        $order_carrier = new OrderCarrier((int) $order->getIdOrderCarrier());
        if (Validate::isLoadedObject($order_carrier)) {
            $order_carrier->weight = (float) $order->getTotalWeight();
            if ($order_carrier->update()) {
                $order->weight = sprintf('%.3f ' . Configuration::get('PS_WEIGHT_UNIT'), $order_carrier->weight);
            }
        }

        // Update Tax lines
        $order_detail->updateTaxAmount($order);

        // Delete specific price if exists
        if (isset($specific_price)) {
            $specific_price->delete();
        }

        $products = $this->getProducts($order);

        // Get the last product
        $product = end($products);
        $resume = OrderSlip::getProductSlipResume((int) $product['id_order_detail']);
        $product['quantity_refundable'] = $product['product_quantity'] - $resume['product_quantity'];
        $product['amount_refundable'] = $product['total_price_tax_excl'] - $resume['amount_tax_excl'];
        $product['amount_refund'] = Tools::displayPrice($resume['amount_tax_incl']);
        $product['return_history'] = OrderReturn::getProductReturnDetail((int) $product['id_order_detail']);
        $product['refund_history'] = OrderSlip::getProductSlipDetail((int) $product['id_order_detail']);
        if ($product['id_warehouse'] != 0) {
            $warehouse = new Warehouse((int) $product['id_warehouse']);
            $product['warehouse_name'] = $warehouse->name;
            $warehouse_location = WarehouseProductLocation::getProductLocation($product['product_id'], $product['product_attribute_id'], $product['id_warehouse']);
            if (!empty($warehouse_location)) {
                $product['warehouse_location'] = $warehouse_location;
            } else {
                $product['warehouse_location'] = false;
            }
        } else {
            $product['warehouse_name'] = '--';
            $product['warehouse_location'] = false;
        }

        // Get invoices collection
        $invoice_collection = $order->getInvoicesCollection();

        $invoice_array = array();
        foreach ($invoice_collection as $invoice) {
            /* @var OrderInvoice $invoice */
            $invoice->name = $invoice->getInvoiceNumberFormatted(Context::getContext()->language->id, (int) $order->id_shop);
            $invoice_array[] = $invoice;
        }

        $order = $order->refreshShippingCost();

        // Assign to smarty informations in order to show the new product line
        $this->context->smarty->assign(array(
            'product' => $product,
            'order' => $order,
            'currency' => new Currency($order->id_currency),
            'can_edit' => $this->access('edit'),
            'invoices_collection' => $invoice_collection,
            'current_id_lang' => Context::getContext()->language->id,
            'link' => Context::getContext()->link,
            'current_index' => self::$currentIndex,
            'display_warehouse' => (int) Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'),
        ));

        $this->sendChangedNotification($order);
        $new_cart_rules = Context::getContext()->cart->getCartRules();
        sort($old_cart_rules);
        sort($new_cart_rules);
        $result = array_diff($new_cart_rules, $old_cart_rules);
        $refresh = false;

        $res = true;
        foreach ($result as $cart_rule) {
            $refresh = true;
            // Create OrderCartRule
            $rule = new CartRule($cart_rule['id_cart_rule']);
            $values = array(
                    'tax_incl' => $rule->getContextualValue(true),
                    'tax_excl' => $rule->getContextualValue(false),
                    );
            $order_cart_rule = new OrderCartRule();
            $order_cart_rule->id_order = $order->id;
            $order_cart_rule->id_cart_rule = $cart_rule['id_cart_rule'];
            $order_cart_rule->id_order_invoice = $order_invoice->id;
            $order_cart_rule->name = $cart_rule['name'];
            $order_cart_rule->value = $values['tax_incl'];
            $order_cart_rule->value_tax_excl = $values['tax_excl'];
            $res &= $order_cart_rule->add();

            $order->total_discounts += $order_cart_rule->value;
            $order->total_discounts_tax_incl += $order_cart_rule->value;
            $order->total_discounts_tax_excl += $order_cart_rule->value_tax_excl;
            $order->total_paid -= $order_cart_rule->value;
            $order->total_paid_tax_incl -= $order_cart_rule->value;
            $order->total_paid_tax_excl -= $order_cart_rule->value_tax_excl;
        }

        // Update Order
        $res &= $order->update();

        die(json_encode(array(
            'result' => true,
            'view' => $this->createTemplate('_product_line.tpl')->fetch(),
            'can_edit' => $this->access('add'),
            'order' => $order,
            'invoices' => $invoice_array,
            'documents_html' => $this->createTemplate('_documents.tpl')->fetch(),
            'shipping_html' => $this->createTemplate('_shipping.tpl')->fetch(),
            'discount_form_html' => $this->createTemplate('_discount_form.tpl')->fetch(),
            'refresh' => $refresh,
        )));
    }

    public function sendChangedNotification(Order $order = null)
    {
        if (is_null($order)) {
            $order = new Order(Tools::getValue('id_order'));
        }

        Hook::exec('actionOrderEdited', array('order' => $order));
    }

    public function ajaxProcessLoadProductInformation()
    {
        $order_detail = new OrderDetail(Tools::getValue('id_order_detail'));
        if (!Validate::isLoadedObject($order_detail)) {
            die(json_encode(array(
                'result' => false,
                'error' => $this->trans('The OrderDetail object cannot be loaded.', array(), 'Admin.Orderscustomers.Notification'),
            )));
        }

        $product = new Product($order_detail->product_id);
        if (!Validate::isLoadedObject($product)) {
            die(json_encode(array(
                'result' => false,
                'error' => $this->trans('The product object cannot be loaded.', array(), 'Admin.Orderscustomers.Notification'),
            )));
        }

        $address = new Address(Tools::getValue('id_address'));
        if (!Validate::isLoadedObject($address)) {
            die(json_encode(array(
                'result' => false,
                'error' => $this->trans('The address object cannot be loaded.', array(), 'Admin.Orderscustomers.Notification'),
            )));
        }

        die(json_encode(array(
            'result' => true,
            'product' => $product,
            'tax_rate' => $product->getTaxesRate($address),
            'price_tax_incl' => Product::getPriceStatic($product->id, true, $order_detail->product_attribute_id, 2),
            'price_tax_excl' => Product::getPriceStatic($product->id, false, $order_detail->product_attribute_id, 2),
            'reduction_percent' => $order_detail->reduction_percent,
        )));
    }

    public function ajaxProcessEditProductOnOrder()
    {
        // Return value
        $res = true;

        $order = new Order((int) Tools::getValue('id_order'));
        $order_detail = new OrderDetail((int) Tools::getValue('product_id_order_detail'));
        if (Tools::isSubmit('product_invoice')) {
            $order_invoice = new OrderInvoice((int) Tools::getValue('product_invoice'));
        }

        // Check fields validity
        $this->doEditProductValidation($order_detail, $order, isset($order_invoice) ? $order_invoice : null);

        // If multiple product_quantity, the order details concern a product customized
        $product_quantity = 0;
        if (is_array(Tools::getValue('product_quantity'))) {
            foreach (Tools::getValue('product_quantity') as $id_customization => $qty) {
                // Update quantity of each customization
                Db::getInstance()->update('customization', array('quantity' => (int) $qty), 'id_customization = ' . (int) $id_customization);
                // Calculate the real quantity of the product
                $product_quantity += $qty;
            }
        } else {
            $product_quantity = Tools::getValue('product_quantity');
        }

        $product_price_tax_incl = Tools::ps_round(Tools::getValue('product_price_tax_incl'), 2);
        $product_price_tax_excl = Tools::ps_round(Tools::getValue('product_price_tax_excl'), 2);
        $total_products_tax_incl = $product_price_tax_incl * $product_quantity;
        $total_products_tax_excl = $product_price_tax_excl * $product_quantity;

        // Calculate differences of price (Before / After)
        $diff_price_tax_incl = $total_products_tax_incl - $order_detail->total_price_tax_incl;
        $diff_price_tax_excl = $total_products_tax_excl - $order_detail->total_price_tax_excl;

        // Apply change on OrderInvoice
        if (isset($order_invoice)) {
            // If OrderInvoice to use is different, we update the old invoice and new invoice
            if ($order_detail->id_order_invoice != $order_invoice->id) {
                $old_order_invoice = new OrderInvoice($order_detail->id_order_invoice);
                // We remove cost of products
                $old_order_invoice->total_products -= $order_detail->total_price_tax_excl;
                $old_order_invoice->total_products_wt -= $order_detail->total_price_tax_incl;

                $old_order_invoice->total_paid_tax_excl -= $order_detail->total_price_tax_excl;
                $old_order_invoice->total_paid_tax_incl -= $order_detail->total_price_tax_incl;

                $res &= $old_order_invoice->update();

                $order_invoice->total_products += $order_detail->total_price_tax_excl;
                $order_invoice->total_products_wt += $order_detail->total_price_tax_incl;

                $order_invoice->total_paid_tax_excl += $order_detail->total_price_tax_excl;
                $order_invoice->total_paid_tax_incl += $order_detail->total_price_tax_incl;

                $order_detail->id_order_invoice = $order_invoice->id;
            }
        }

        if ($diff_price_tax_incl != 0 && $diff_price_tax_excl != 0) {
            $order_detail->unit_price_tax_excl = $product_price_tax_excl;
            $order_detail->unit_price_tax_incl = $product_price_tax_incl;

            $order_detail->total_price_tax_incl += $diff_price_tax_incl;
            $order_detail->total_price_tax_excl += $diff_price_tax_excl;

            if (isset($order_invoice)) {
                // Apply changes on OrderInvoice
                $order_invoice->total_products += $diff_price_tax_excl;
                $order_invoice->total_products_wt += $diff_price_tax_incl;

                $order_invoice->total_paid_tax_excl += $diff_price_tax_excl;
                $order_invoice->total_paid_tax_incl += $diff_price_tax_incl;
            }

            // Apply changes on Order
            $order = new Order($order_detail->id_order);
            $order->total_products += $diff_price_tax_excl;
            $order->total_products_wt += $diff_price_tax_incl;

            $order->total_paid += $diff_price_tax_incl;
            $order->total_paid_tax_excl += $diff_price_tax_excl;
            $order->total_paid_tax_incl += $diff_price_tax_incl;

            $res &= $order->update();
        }

        $old_quantity = $order_detail->product_quantity;

        $order_detail->product_quantity = $product_quantity;
        $order_detail->reduction_percent = 0;

        // update taxes
        $res &= $order_detail->updateTaxAmount($order);

        // Save order detail
        $res &= $order_detail->update();

        // Update weight SUM
        $order_carrier = new OrderCarrier((int) $order->getIdOrderCarrier());
        if (Validate::isLoadedObject($order_carrier)) {
            $order_carrier->weight = (float) $order->getTotalWeight();
            $res &= $order_carrier->update();
            if ($res) {
                $order->weight = sprintf('%.3f ' . Configuration::get('PS_WEIGHT_UNIT'), $order_carrier->weight);
            }
        }

        // Save order invoice
        if (isset($order_invoice)) {
            $res &= $order_invoice->update();
        }

        // Update product available quantity
        StockAvailable::updateQuantity($order_detail->product_id, $order_detail->product_attribute_id, ($old_quantity - $order_detail->product_quantity), $order->id_shop);

        $products = $this->getProducts($order);
        // Get the last product
        $product = $products[$order_detail->id];
        $resume = OrderSlip::getProductSlipResume($order_detail->id);
        $product['quantity_refundable'] = $product['product_quantity'] - $resume['product_quantity'];
        $product['amount_refundable'] = $product['total_price_tax_excl'] - $resume['amount_tax_excl'];
        $product['amount_refund'] = Tools::displayPrice($resume['amount_tax_incl']);
        $product['refund_history'] = OrderSlip::getProductSlipDetail($order_detail->id);
        if ($product['id_warehouse'] != 0) {
            $warehouse = new Warehouse((int) $product['id_warehouse']);
            $product['warehouse_name'] = $warehouse->name;
            $warehouse_location = WarehouseProductLocation::getProductLocation($product['product_id'], $product['product_attribute_id'], $product['id_warehouse']);
            if (!empty($warehouse_location)) {
                $product['warehouse_location'] = $warehouse_location;
            } else {
                $product['warehouse_location'] = false;
            }
        } else {
            $product['warehouse_name'] = '--';
            $product['warehouse_location'] = false;
        }

        // Get invoices collection
        $invoice_collection = $order->getInvoicesCollection();

        $invoice_array = array();
        foreach ($invoice_collection as $invoice) {
            /* @var OrderInvoice $invoice */
            $invoice->name = $invoice->getInvoiceNumberFormatted(Context::getContext()->language->id, (int) $order->id_shop);
            $invoice_array[] = $invoice;
        }

        $order = $order->refreshShippingCost();

        $stockLocationIsAvailable = false;
        foreach ($products as $currentProduct) {
            if (!empty($currentProduct['location'])) {
                $stockLocationIsAvailable = true;
                break;
            }
        }

        // Assign to smarty informations in order to show the new product line
        $this->context->smarty->assign(array(
            'product' => $product,
            'order' => $order,
            'currency' => new Currency($order->id_currency),
            'can_edit' => $this->access('edit'),
            'invoices_collection' => $invoice_collection,
            'current_id_lang' => Context::getContext()->language->id,
            'link' => Context::getContext()->link,
            'current_index' => self::$currentIndex,
            'display_warehouse' => (int) Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'),
            'stock_location_is_available' => $stockLocationIsAvailable,
        ));

        if (!$res) {
            die(json_encode(array(
                'result' => $res,
                'error' => $this->trans('An error occurred while editing the product line.', array(), 'Admin.Orderscustomers.Notification'),
            )));
        }

        if (is_array(Tools::getValue('product_quantity'))) {
            $view = $this->createTemplate('_customized_data.tpl')->fetch();
        } else {
            $view = $this->createTemplate('_product_line.tpl')->fetch();
        }

        $this->sendChangedNotification($order);

        die(json_encode(array(
            'result' => $res,
            'view' => $view,
            'can_edit' => $this->access('add'),
            'invoices_collection' => $invoice_collection,
            'order' => $order,
            'invoices' => $invoice_array,
            'documents_html' => $this->createTemplate('_documents.tpl')->fetch(),
            'shipping_html' => $this->createTemplate('_shipping.tpl')->fetch(),
            'customized_product' => is_array(Tools::getValue('product_quantity')),
        )));
    }

    public function ajaxProcessDeleteProductLine()
    {
        $res = true;

        $order_detail = new OrderDetail((int) Tools::getValue('id_order_detail'));
        $order = new Order((int) Tools::getValue('id_order'));

        $this->doDeleteProductLineValidation($order_detail, $order);

        // Update OrderInvoice of this OrderDetail
        if ($order_detail->id_order_invoice != 0) {
            $order_invoice = new OrderInvoice($order_detail->id_order_invoice);
            $order_invoice->total_paid_tax_excl -= $order_detail->total_price_tax_excl;
            $order_invoice->total_paid_tax_incl -= $order_detail->total_price_tax_incl;
            $order_invoice->total_products -= $order_detail->total_price_tax_excl;
            $order_invoice->total_products_wt -= $order_detail->total_price_tax_incl;
            $res &= $order_invoice->update();
        }

        // Update Order
        $order->total_paid -= $order_detail->total_price_tax_incl;
        $order->total_paid_tax_incl -= $order_detail->total_price_tax_incl;
        $order->total_paid_tax_excl -= $order_detail->total_price_tax_excl;
        $order->total_products -= $order_detail->total_price_tax_excl;
        $order->total_products_wt -= $order_detail->total_price_tax_incl;

        $res &= $order->update();

        // Reinject quantity in stock
        $this->reinjectQuantity($order_detail, $order_detail->product_quantity, true);

        // Update weight SUM
        $order_carrier = new OrderCarrier((int) $order->getIdOrderCarrier());
        if (Validate::isLoadedObject($order_carrier)) {
            $order_carrier->weight = (float) $order->getTotalWeight();
            $res &= $order_carrier->update();
            if ($res) {
                $order->weight = sprintf('%.3f ' . Configuration::get('PS_WEIGHT_UNIT'), $order_carrier->weight);
            }
        }

        if (!$res) {
            die(json_encode(array(
                'result' => $res,
                'error' => $this->trans('An error occurred while attempting to delete the product line.', array(), 'Admin.Orderscustomers.Notification'),
            )));
        }

        // Get invoices collection
        $invoice_collection = $order->getInvoicesCollection();

        $invoice_array = array();
        foreach ($invoice_collection as $invoice) {
            /* @var OrderInvoice $invoice */
            $invoice->name = $invoice->getInvoiceNumberFormatted(Context::getContext()->language->id, (int) $order->id_shop);
            $invoice_array[] = $invoice;
        }

        $order = $order->refreshShippingCost();

        // Assign to smarty informations in order to show the new product line
        $this->context->smarty->assign(array(
            'order' => $order,
            'currency' => new Currency($order->id_currency),
            'invoices_collection' => $invoice_collection,
            'current_id_lang' => Context::getContext()->language->id,
            'link' => Context::getContext()->link,
            'current_index' => self::$currentIndex,
        ));

        $this->sendChangedNotification($order);

        die(json_encode(array(
            'result' => $res,
            'order' => $order,
            'invoices' => $invoice_array,
            'documents_html' => $this->createTemplate('_documents.tpl')->fetch(),
            'shipping_html' => $this->createTemplate('_shipping.tpl')->fetch(),
        )));
    }

    protected function doEditProductValidation(OrderDetail $order_detail, Order $order, OrderInvoice $order_invoice = null)
    {
        if (!Validate::isLoadedObject($order_detail)) {
            die(json_encode(array(
                'result' => false,
                'error' => $this->trans('The Order Detail object could not be loaded.', array(), 'Admin.Orderscustomers.Notification'),
            )));
        }

        if (!empty($order_invoice) && !Validate::isLoadedObject($order_invoice)) {
            die(json_encode(array(
                'result' => false,
                'error' => $this->trans('The invoice object cannot be loaded.', array(), 'Admin.Orderscustomers.Notification'),
            )));
        }

        if (!Validate::isLoadedObject($order)) {
            die(json_encode(array(
                'result' => false,
                'error' => $this->trans('The order object cannot be loaded.', array(), 'Admin.Orderscustomers.Notification'),
            )));
        }

        if ($order_detail->id_order != $order->id) {
            die(json_encode(array(
                'result' => false,
                'error' => $this->trans('You cannot edit the order detail for this order.', array(), 'Admin.Orderscustomers.Notification'),
            )));
        }

        // We can't edit a delivered order
        if ($order->hasBeenDelivered()) {
            die(json_encode(array(
                'result' => false,
                'error' => $this->trans('You cannot edit a delivered order.', array(), 'Admin.Orderscustomers.Notification'),
            )));
        }

        if (!empty($order_invoice) && $order_invoice->id_order != Tools::getValue('id_order')) {
            die(json_encode(array(
                'result' => false,
                'error' => $this->trans('You cannot use this invoice for the order', array(), 'Admin.Orderscustomers.Notification'),
            )));
        }

        // Clean price
        $product_price_tax_incl = str_replace(',', '.', Tools::getValue('product_price_tax_incl'));
        $product_price_tax_excl = str_replace(',', '.', Tools::getValue('product_price_tax_excl'));

        if (!Validate::isPrice($product_price_tax_incl) || !Validate::isPrice($product_price_tax_excl)) {
            die(json_encode(array(
                'result' => false,
                'error' => $this->trans('Invalid price', array(), 'Admin.Orderscustomers.Notification'),
            )));
        }

        if (!is_array(Tools::getValue('product_quantity')) && !Validate::isUnsignedInt(Tools::getValue('product_quantity'))) {
            die(json_encode(array(
                'result' => false,
                'error' => $this->trans('Invalid quantity', array(), 'Admin.Orderscustomers.Notification'),
            )));
        } elseif (is_array(Tools::getValue('product_quantity'))) {
            foreach (Tools::getValue('product_quantity') as $qty) {
                if (!Validate::isUnsignedInt($qty)) {
                    die(json_encode(array(
                        'result' => false,
                        'error' => $this->trans('Invalid quantity', array(), 'Admin.Orderscustomers.Notification'),
                    )));
                }
            }
        }
    }

    protected function doDeleteProductLineValidation(OrderDetail $order_detail, Order $order)
    {
        if (!Validate::isLoadedObject($order_detail)) {
            die(json_encode(array(
                'result' => false,
                'error' => $this->trans('The Order Detail object could not be loaded.', array(), 'Admin.Orderscustomers.Notification'),
            )));
        }

        if (!Validate::isLoadedObject($order)) {
            die(json_encode(array(
                'result' => false,
                'error' => $this->trans('The order object cannot be loaded.', array(), 'Admin.Orderscustomers.Notification'),
            )));
        }

        if ($order_detail->id_order != $order->id) {
            die(json_encode(array(
                'result' => false,
                'error' => $this->trans('You cannot delete the order detail.', array(), 'Admin.Orderscustomers.Notification'),
            )));
        }

        // We can't edit a delivered order
        if ($order->hasBeenDelivered()) {
            die(json_encode(array(
                'result' => false,
                'error' => $this->trans('You cannot edit a delivered order.', array(), 'Admin.Orderscustomers.Notification'),
            )));
        }
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    protected function getProducts($order)
    {
        $products = $order->getProducts();

        foreach ($products as &$product) {
            if ($product['image'] != null) {
                $name = 'product_mini_' . (int) $product['product_id'] . (isset($product['product_attribute_id']) ? '_' . (int) $product['product_attribute_id'] : '') . '.jpg';
                // generate image cache, only for back office
                $product['image_tag'] = ImageManager::thumbnail(_PS_IMG_DIR_ . 'p/' . $product['image']->getExistingImgPath() . '.jpg', $name, 45, 'jpg');
                if (file_exists(_PS_TMP_IMG_DIR_ . $name)) {
                    $product['image_size'] = getimagesize(_PS_TMP_IMG_DIR_ . $name);
                } else {
                    $product['image_size'] = false;
                }
            }
        }

        ksort($products);

        return $products;
    }

    /**
     * @param OrderDetail $order_detail
     * @param int $qty_cancel_product
     * @param bool $delete
     */
    protected function reinjectQuantity($order_detail, $qty_cancel_product, $delete = false)
    {
        // Reinject product
        $reinjectable_quantity = (int) $order_detail->product_quantity - (int) $order_detail->product_quantity_reinjected;
        $quantity_to_reinject = $qty_cancel_product > $reinjectable_quantity ? $reinjectable_quantity : $qty_cancel_product;
        // @since 1.5.0 : Advanced Stock Management
        $product_to_inject = new Product($order_detail->product_id, false, (int) $this->context->language->id, (int) $order_detail->id_shop);

        $product = new Product($order_detail->product_id, false, (int) $this->context->language->id, (int) $order_detail->id_shop);

        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $product->advanced_stock_management && $order_detail->id_warehouse != 0) {
            $manager = StockManagerFactory::getManager();
            $movements = StockMvt::getNegativeStockMvts(
                                $order_detail->id_order,
                                $order_detail->product_id,
                                $order_detail->product_attribute_id,
                                $quantity_to_reinject
                            );
            $left_to_reinject = $quantity_to_reinject;
            foreach ($movements as $movement) {
                if ($left_to_reinject > $movement['physical_quantity']) {
                    $quantity_to_reinject = $movement['physical_quantity'];
                }

                $left_to_reinject -= $quantity_to_reinject;
                if (Pack::isPack((int) $product->id)) {
                    // Gets items
                    if ($product->pack_stock_type == Pack::STOCK_TYPE_PRODUCTS_ONLY
                        || $product->pack_stock_type == Pack::STOCK_TYPE_PACK_BOTH
                        || ($product->pack_stock_type == Pack::STOCK_TYPE_DEFAULT
                            && Configuration::get('PS_PACK_STOCK_TYPE') > 0)
                    ) {
                        $products_pack = Pack::getItems((int) $product->id, (int) Configuration::get('PS_LANG_DEFAULT'));
                        // Foreach item
                        foreach ($products_pack as $product_pack) {
                            if ($product_pack->advanced_stock_management == 1) {
                                $manager->addProduct(
                                    $product_pack->id,
                                    $product_pack->id_pack_product_attribute,
                                    new Warehouse($movement['id_warehouse']),
                                    $product_pack->pack_quantity * $quantity_to_reinject,
                                    null,
                                    $movement['price_te'],
                                    true
                                );
                            }
                        }
                    }

                    if ($product->pack_stock_type == Pack::STOCK_TYPE_PACK_ONLY
                        || $product->pack_stock_type == Pack::STOCK_TYPE_PACK_BOTH
                        || ($product->pack_stock_type == Pack::STOCK_TYPE_DEFAULT
                            && (Configuration::get('PS_PACK_STOCK_TYPE') == Pack::STOCK_TYPE_PACK_ONLY
                                || Configuration::get('PS_PACK_STOCK_TYPE') == Pack::STOCK_TYPE_PACK_BOTH)
                        )
                    ) {
                        $manager->addProduct(
                                $order_detail->product_id,
                                $order_detail->product_attribute_id,
                                new Warehouse($movement['id_warehouse']),
                                $quantity_to_reinject,
                                null,
                                $movement['price_te'],
                                true
                            );
                    }
                } else {
                    $manager->addProduct(
                            $order_detail->product_id,
                            $order_detail->product_attribute_id,
                            new Warehouse($movement['id_warehouse']),
                            $quantity_to_reinject,
                            null,
                            $movement['price_te'],
                            true
                        );
                }
            }

            $id_product = $order_detail->product_id;
            if ($delete) {
                $order_detail->delete();
            }
            StockAvailable::synchronize($id_product);
        } elseif ($order_detail->id_warehouse == 0) {
            StockAvailable::updateQuantity(
                $order_detail->product_id,
                $order_detail->product_attribute_id,
                $quantity_to_reinject,
                $order_detail->id_shop,
                true,
                array(
                    'id_order' => $order_detail->id_order,
                    'id_stock_mvt_reason' => Configuration::get('PS_STOCK_CUSTOMER_RETURN_REASON'),
                )
            );

            // sync all stock
            (new StockManager())->updatePhysicalProductQuantity(
                (int) $order_detail->id_shop,
                (int) Configuration::get('PS_OS_ERROR'),
                (int) Configuration::get('PS_OS_CANCELED'),
                null,
                (int) $order_detail->id_order
            );

            if ($delete) {
                $order_detail->delete();
            }
        } else {
            $this->errors[] = $this->trans('This product cannot be re-stocked.', array(), 'Admin.Orderscustomers.Notification');
        }
    }

    /**
     * @param OrderInvoice $order_invoice
     * @param float $value_tax_incl
     * @param float $value_tax_excl
     */
    protected function applyDiscountOnInvoice($order_invoice, $value_tax_incl, $value_tax_excl)
    {
        // Update OrderInvoice
        $order_invoice->total_discount_tax_incl += $value_tax_incl;
        $order_invoice->total_discount_tax_excl += $value_tax_excl;
        $order_invoice->total_paid_tax_incl -= $value_tax_incl;
        $order_invoice->total_paid_tax_excl -= $value_tax_excl;
        $order_invoice->update();
    }

    public function ajaxProcessChangePaymentMethod()
    {
        $customer = new Customer(Tools::getValue('id_customer'));
        $modules = Module::getAuthorizedModules($customer->id_default_group, array($customer->id_shop));
        $authorized_modules = array();

        if (!Validate::isLoadedObject($customer) || !is_array($modules)) {
            die(json_encode(array('result' => false)));
        }

        foreach ($modules as $module) {
            $authorized_modules[] = (int) $module['id_module'];
        }

        $payment_modules = array();

        foreach (PaymentModule::getInstalledPaymentModules() as $p_module) {
            if (in_array((int) $p_module['id_module'], $authorized_modules)) {
                $payment_modules[] = Module::getInstanceById((int) $p_module['id_module']);
            }
        }

        $this->context->smarty->assign(array(
            'payment_modules' => $payment_modules,
        ));

        die(json_encode(array(
            'result' => true,
            'view' => $this->createTemplate('_select_payment.tpl')->fetch(),
        )));
    }

    /**
     * This method allow to add image information on a package detail.
     *
     * @param array &pack_item
     */
    protected function setProductImageInformations(&$pack_item)
    {
        if (isset($pack_item['id_product_attribute']) && $pack_item['id_product_attribute']) {
            $id_image = Db::getInstance()->getValue('
                SELECT `image_shop`.id_image
                FROM `' . _DB_PREFIX_ . 'product_attribute_image` pai' .
                Shop::addSqlAssociation('image', 'pai', true) . '
                WHERE id_product_attribute = ' . (int) $pack_item['id_product_attribute']);
        }

        if (!isset($id_image) || !$id_image) {
            $id_image = Db::getInstance()->getValue('
                SELECT `image_shop`.id_image
                FROM `' . _DB_PREFIX_ . 'image` i' .
                Shop::addSqlAssociation('image', 'i', true, 'image_shop.cover=1') . '
                WHERE i.id_product = ' . (int) $pack_item['id_product']
            );
        }

        $pack_item['image'] = null;
        $pack_item['image_size'] = null;

        if ($id_image) {
            $pack_item['image'] = new Image($id_image);
        }
    }

    /**
     * Get available carrier list for an order.
     *
     * @param object $order
     *
     * @return array $delivery_option_list_formated
     */
    protected function getCarrierList($order)
    {
        $cart = $this->context->cart;
        $groups = Customer::getGroupsStatic((int) $cart->id_customer);
        $address = new Address((int) $cart->id_address_delivery);

        return Carrier::getCarriersForOrder(Address::getZoneById((int) $address->id), $groups, $cart);
    }
}
