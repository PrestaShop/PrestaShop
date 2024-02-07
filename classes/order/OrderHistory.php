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
use PrestaShop\PrestaShop\Adapter\MailTemplate\MailPartialTemplateRenderer;
use PrestaShop\PrestaShop\Adapter\StockManager as StockManagerAdapter;
use PrestaShop\PrestaShop\Core\Stock\StockManager;

class OrderHistoryCore extends ObjectModel
{
    /** @var int Order id */
    public $id_order;

    /** @var int Order status id */
    public $id_order_state;

    /** @var int Employee id for this history entry */
    public $id_employee;

    /** @var string ApiAccess id for this history entry */
    public string $api_client_id = '';

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'order_history',
        'primary' => 'id_order_history',
        'fields' => [
            'id_order' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_order_state' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_employee' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'api_client_id' => ['type' => self::TYPE_STRING, 'size' => 255],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    /**
     * @see  ObjectModel::$webserviceParameters
     */
    protected $webserviceParameters = [
        'objectsNodeName' => 'order_histories',
        'fields' => [
            'id_employee' => ['xlink_resource' => 'employees'],
            'id_order_state' => ['required' => true, 'xlink_resource' => 'order_states'],
            'id_order' => ['xlink_resource' => 'orders'],
        ],
        'objectMethods' => [
            'add' => 'addWs',
        ],
    ];

    /**
     * Sets the new state of the given order.
     *
     * @param int $new_order_state
     * @param int|OrderCore $id_order
     * @param bool $use_existing_payment
     */
    public function changeIdOrderState($new_order_state, $id_order, $use_existing_payment = false)
    {
        if (!$new_order_state || !$id_order) {
            return;
        }

        if (!is_object($id_order) && is_numeric($id_order)) {
            $order = new Order((int) $id_order);
        } elseif (is_object($id_order)) {
            $order = $id_order;
        } else {
            return;
        }

        ShopUrl::cacheMainDomainForShop($order->id_shop);

        $new_os = new OrderState((int) $new_order_state, $order->id_lang);
        $old_os = new OrderState((int) $order->current_state, $order->id_lang);

        // executes hook
        if (in_array($new_os->id, [Configuration::get('PS_OS_PAYMENT'), Configuration::get('PS_OS_WS_PAYMENT')])) {
            // Hook called only for the shop concerned
            Hook::exec('actionPaymentConfirmation', ['id_order' => (int) $order->id], null, false, true, false, $order->id_shop);
        }

        // executes hook
        // Hook called only for the shop concerned
        Hook::exec('actionOrderStatusUpdate', [
            'newOrderStatus' => $new_os,
            'oldOrderStatus' => $old_os,
            'id_order' => (int) $order->id,
        ], null, false, true, false, $order->id_shop);

        if (Validate::isLoadedObject($order) && $new_os instanceof OrderState) {
            $context = Context::getContext();

            // An email is sent the first time a virtual item is validated
            $virtual_products = $order->getVirtualProducts();
            if ($virtual_products && !$old_os->logable && $new_os->logable) {
                $assign = [];
                foreach ($virtual_products as $key => $virtual_product) {
                    $id_product_download = ProductDownload::getIdFromIdProduct($virtual_product['product_id']);
                    $product_download = new ProductDownload($id_product_download);
                    // If this virtual item has an associated file, we'll provide the link to download the file in the email
                    if ($product_download->display_filename != '') {
                        $assign[$key]['name'] = $product_download->display_filename;
                        $dl_link = $product_download->getTextLink($virtual_product['download_hash'])
                            . '&id_order=' . (int) $order->id
                            . '&secure_key=' . $order->secure_key;
                        $assign[$key]['link'] = $dl_link;
                        if (isset($virtual_product['download_deadline']) && $virtual_product['download_deadline'] != '0000-00-00 00:00:00') {
                            $assign[$key]['deadline'] = Tools::displayDate($virtual_product['download_deadline']);
                        }
                        if ($product_download->nb_downloadable != 0) {
                            $assign[$key]['downloadable'] = (int) $product_download->nb_downloadable;
                        }
                    }
                }

                $customer = new Customer((int) $order->id_customer);
                $links = [];
                foreach ($assign as $product) {
                    $complementaryText = [];
                    if (isset($product['deadline'])) {
                        $complementaryText[] = $this->trans('expires on %s.', [htmlspecialchars($product['deadline'])], 'Admin.Orderscustomers.Notification');
                    }
                    if (isset($product['downloadable'])) {
                        $complementaryText[] = $this->trans('downloadable %d time(s)', [(int) $product['downloadable']], 'Admin.Orderscustomers.Notification');
                    }
                    $links[] = [
                        'text' => Tools::htmlentitiesUTF8($product['name']),
                        'url' => $product['link'],
                        'complementary_text' => implode(' ', $complementaryText),
                    ];
                }

                $context = Context::getContext();
                $partialRenderer = new MailPartialTemplateRenderer($context->smarty);

                $links_txt = $partialRenderer->render('download_product_virtual_products.txt', $context->language, $links, true);
                $links_html = $partialRenderer->render('download_product_virtual_products.tpl', $context->language, $links);

                $data = [
                    '{lastname}' => $customer->lastname,
                    '{firstname}' => $customer->firstname,
                    '{id_order}' => (int) $order->id,
                    '{order_name}' => $order->getUniqReference(),
                    '{nbProducts}' => count($virtual_products),
                    '{virtualProducts}' => $links_html,
                    '{virtualProductsTxt}' => $links_txt,
                ];
                // If there is at least one downloadable file
                if (!empty($assign)) {
                    $orderLanguage = new Language((int) $order->id_lang);
                    Mail::Send(
                        (int) $order->id_lang,
                        'download_product',
                        Context::getContext()->getTranslator()->trans(
                            'The virtual product that you bought is available for download',
                            [],
                            'Emails.Subject',
                            $orderLanguage->locale
                        ),
                        $data,
                        $customer->email,
                        $customer->firstname . ' ' . $customer->lastname,
                        null,
                        null,
                        null,
                        null,
                        _PS_MAIL_DIR_,
                        false,
                        (int) $order->id_shop
                    );
                }
            }

            /** @since 1.5.0 : gets the stock manager */
            $manager = null;

            $error_or_canceled_statuses = [Configuration::get('PS_OS_ERROR'), Configuration::get('PS_OS_CANCELED')];

            $employee = null;
            if (!(int) $this->id_employee || !Validate::isLoadedObject(($employee = new Employee((int) $this->id_employee)))) {
                if (!Validate::isLoadedObject($old_os) && $context != null) {
                    // First OrderHistory, there is no $old_os, so $employee is null before here
                    $employee = $context->employee; // filled if from BO and order created (because no old_os)
                    if ($employee) {
                        $this->id_employee = $employee->id;
                    }
                } else {
                    $employee = null;
                }
            }

            // foreach products of the order
            foreach ($order->getProductsDetail() as $product) {
                if (Validate::isLoadedObject($old_os)) {
                    // if becoming logable => adds sale
                    if ($new_os->logable && !$old_os->logable) {
                        ProductSale::addProductSale($product['product_id'], $product['product_quantity']);
                        // @since 1.5.0 - Stock Management
                        if (!Pack::isPack($product['product_id']) &&
                            in_array($old_os->id, $error_or_canceled_statuses)) {
                            StockAvailable::updateQuantity($product['product_id'], $product['product_attribute_id'], -(int) $product['product_quantity'], $order->id_shop);
                        }
                    } elseif (!$new_os->logable && $old_os->logable) {
                        // if becoming unlogable => removes sale
                        ProductSale::removeProductSale($product['product_id'], $product['product_quantity']);

                        // @since 1.5.0 - Stock Management
                        if (!Pack::isPack($product['product_id']) &&
                            in_array($new_os->id, $error_or_canceled_statuses)) {
                            StockAvailable::updateQuantity($product['product_id'], $product['product_attribute_id'], (int) $product['product_quantity'], $order->id_shop);
                        }
                    } elseif (!$new_os->logable && !$old_os->logable &&
                        in_array($new_os->id, $error_or_canceled_statuses) &&
                        !in_array($old_os->id, $error_or_canceled_statuses)
                    ) {
                        // if waiting for payment => payment error/canceled
                        StockAvailable::updateQuantity($product['product_id'], $product['product_attribute_id'], (int) $product['product_quantity'], $order->id_shop);
                    }
                }
                // From here, there is 2 cases : $old_os exists, and we can test shipped state evolution,
                // Or old_os does not exists, and we should consider that initial shipped state is 0 (to allow decrease of stocks)

                // Save movement if :
                // new_os->shipped != old_os->shipped
                if (Validate::isLoadedObject($old_os) && Validate::isLoadedObject($new_os) && $new_os->shipped != $old_os->shipped) {
                    $product_quantity = (int) ($product['product_quantity'] - $product['product_quantity_refunded'] - $product['product_quantity_return']);

                    if ($product_quantity > 0) {
                        $current_shop_context_type = Context::getContext()->shop->getContextType();
                        if ($current_shop_context_type !== Shop::CONTEXT_SHOP) {
                            //change to order shop context
                            $current_shop_group_id = Context::getContext()->shop->getContextShopGroupID();
                            Context::getContext()->shop->setContext(Shop::CONTEXT_SHOP, $order->id_shop);
                        }
                        (new StockManager())->saveMovement(
                            (int) $product['product_id'],
                            (int) $product['product_attribute_id'],
                            (int) $product_quantity * ($new_os->shipped == 1 ? -1 : 1),
                            [
                                'id_order' => $order->id,
                                'id_stock_mvt_reason' => ($new_os->shipped == 1 ? Configuration::get('PS_STOCK_CUSTOMER_ORDER_REASON') : Configuration::get('PS_STOCK_CUSTOMER_ORDER_CANCEL_REASON')),
                            ]
                        );
                        //back to current shop context
                        if ($current_shop_context_type !== Shop::CONTEXT_SHOP && isset($current_shop_group_id)) {
                            Context::getContext()->shop->setContext($current_shop_context_type, $current_shop_group_id);
                        }
                    }
                }
            }
        }

        $this->id_order_state = (int) $new_order_state;

        // changes invoice number of order ?
        if (!Validate::isLoadedObject($new_os) || !Validate::isLoadedObject($order)) {
            throw new PrestaShopException($this->trans('Invalid new order status', [], 'Admin.Orderscustomers.Notification'));
        }

        // the order is valid if and only if the invoice is available and the order is not cancelled
        $order->current_state = $this->id_order_state;
        $order->valid = $new_os->logable;
        $order->update();

        if ($new_os->invoice && !$order->invoice_number) {
            $order->setInvoice($use_existing_payment);
        } elseif ($new_os->delivery && !$order->delivery_number) {
            $order->setDeliverySlip();
        }

        // set orders as paid
        if ($new_os->paid == 1) {
            if ($order->total_paid != 0) {
                $payment_method = Module::getInstanceByName($order->module);
            }

            $invoices = $order->getInvoicesCollection();
            foreach ($invoices as $invoice) {
                /** @var OrderInvoice $invoice */
                $rest_paid = $invoice->getRestPaid();
                if ($rest_paid > 0) {
                    $payment = new OrderPayment();
                    $payment->order_reference = Tools::substr($order->reference, 0, 9);
                    $payment->id_currency = $order->id_currency;
                    $payment->amount = $rest_paid;
                    $payment->payment_method = isset($payment_method) && $payment_method instanceof Module ? $payment_method->displayName : null;
                    $payment->conversion_rate = $order->conversion_rate;
                    $payment->save();

                    // Update total_paid_real value for backward compatibility reasons
                    $order->total_paid_real += $rest_paid;
                    $order->save();

                    Db::getInstance()->insert(
                        'order_invoice_payment',
                        [
                            'id_order_invoice' => (int) $invoice->id,
                            'id_order_payment' => (int) $payment->id,
                            'id_order' => (int) $order->id,
                        ]
                    );
                }
            }
        }

        // updates delivery date even if it was already set by another state change
        if ($new_os->delivery) {
            $order->setDelivery();
        }

        // executes hook
        // Hook called only for the shop concerned
        Hook::exec('actionOrderStatusPostUpdate', [
            'newOrderStatus' => $new_os,
            'oldOrderStatus' => $old_os,
            'id_order' => (int) $order->id,
        ], null, false, true, false, $order->id_shop);

        // sync all stock
        (new StockManagerAdapter())->updatePhysicalProductQuantity(
            (int) $order->id_shop,
            (int) Configuration::get('PS_OS_ERROR'),
            (int) Configuration::get('PS_OS_CANCELED'),
            null,
            (int) $order->id
        );

        ShopUrl::resetMainDomainCache();
    }

    /**
     * @param bool $autodate Optional
     * @param array|bool $template_vars Optional
     * @param Context|null $context Deprecated
     *
     * @return bool
     */
    public function addWithemail($autodate = true, $template_vars = false, Context $context = null)
    {
        $order = new Order($this->id_order);

        if (!$this->add($autodate)) {
            return false;
        }
        Order::cleanHistoryCache();

        if (!$this->sendEmail($order, $template_vars)) {
            return false;
        }

        return true;
    }

    /**
     * @param Order $order
     * @param array|false $template_vars
     *
     * @return bool
     */
    public function sendEmail($order, $template_vars = false)
    {
        $result = Db::getInstance()->getRow('
            SELECT osl.`template`, c.`lastname`, c.`firstname`, osl.`name` AS osname, c.`email`, os.`module_name`, os.`id_order_state`, os.`pdf_invoice`, os.`pdf_delivery`
            FROM `' . _DB_PREFIX_ . 'order_history` oh
                LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON oh.`id_order` = o.`id_order`
                LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON o.`id_customer` = c.`id_customer`
                LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON oh.`id_order_state` = os.`id_order_state`
                LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = o.`id_lang`)
            WHERE oh.`id_order_history` = ' . (int) $this->id . ' AND os.`send_email` = 1');
        if (isset($result['template']) && Validate::isEmail($result['email'])) {
            ShopUrl::cacheMainDomainForShop($order->id_shop);

            $topic = $result['osname'];
            $carrierUrl = '';
            if (Validate::isLoadedObject($carrier = new Carrier((int) $order->id_carrier, $order->id_lang))) {
                $carrierUrl = $carrier->url;
            }
            $data = [
                '{lastname}' => $result['lastname'],
                '{firstname}' => $result['firstname'],
                '{id_order}' => (int) $this->id_order,
                '{order_name}' => $order->getUniqReference(),
                '{followup}' => str_replace('@', $order->getShippingNumber() ?? '', $carrierUrl),
                '{shipping_number}' => $order->getShippingNumber(),
            ];

            if ($result['module_name']) {
                $module = Module::getInstanceByName($result['module_name']);
                if (Validate::isLoadedObject($module) && isset($module->extra_mail_vars) && is_array($module->extra_mail_vars)) {
                    $data = array_merge($data, $module->extra_mail_vars);
                }
            }

            if (is_array($template_vars)) {
                $data = array_merge($data, $template_vars);
            }

            $context = Context::getContext();
            $data['{total_paid}'] = Tools::getContextLocale($context)->formatPrice((float) $order->total_paid, Currency::getIsoCodeById((int) $order->id_currency));

            if (Validate::isLoadedObject($order)) {
                // Attach invoice and / or delivery-slip if they exists and status is set to attach them
                if (($result['pdf_invoice'] || $result['pdf_delivery'])) {
                    $currentLanguage = $context->language;
                    $orderLanguage = new Language((int) $order->id_lang);
                    $context->language = $orderLanguage;
                    $context->getTranslator()->setLocale($orderLanguage->locale);
                    $invoice = $order->getInvoicesCollection();
                    $file_attachement = [];

                    if ($result['pdf_invoice'] && (int) Configuration::get('PS_INVOICE') && $order->invoice_number) {
                        Hook::exec('actionPDFInvoiceRender', ['order_invoice_list' => $invoice]);
                        $pdf = new PDF($invoice, PDF::TEMPLATE_INVOICE, $context->smarty);
                        $file_attachement['invoice']['content'] = $pdf->render(false);
                        $file_attachement['invoice']['name'] = $pdf->getFilename();
                        $file_attachement['invoice']['mime'] = 'application/pdf';
                    }
                    if ($result['pdf_delivery'] && $order->delivery_number) {
                        $pdf = new PDF($invoice, PDF::TEMPLATE_DELIVERY_SLIP, $context->smarty);
                        $file_attachement['delivery']['content'] = $pdf->render(false);
                        $file_attachement['delivery']['name'] = $pdf->getFilename();
                        $file_attachement['delivery']['mime'] = 'application/pdf';
                    }

                    $context->language = $currentLanguage;
                    $context->getTranslator()->setLocale($currentLanguage->locale);
                } else {
                    $file_attachement = null;
                }

                if (!Mail::Send(
                    (int) $order->id_lang,
                    $result['template'],
                    $topic,
                    $data,
                    $result['email'],
                    $result['firstname'] . ' ' . $result['lastname'],
                    null,
                    null,
                    $file_attachement,
                    null,
                    _PS_MAIL_DIR_,
                    false,
                    (int) $order->id_shop
                )) {
                    return false;
                }
            }

            ShopUrl::resetMainDomainCache();
        }

        return true;
    }

    public function add($autodate = true, $null_values = false)
    {
        if (!parent::add($autodate)) {
            return false;
        }

        $order = new Order((int) $this->id_order);
        // Update id_order_state attribute in Order
        $order->current_state = $this->id_order_state;
        $order->update();

        // Hook called only for the shop concerned
        Hook::exec('actionOrderHistoryAddAfter', ['order_history' => $this], null, false, true, false, $order->id_shop);

        return true;
    }

    /**
     * @return int
     */
    public function isValidated()
    {
        return (int) Db::getInstance()->getValue('
        SELECT COUNT(oh.`id_order_history`) AS nb
        FROM `' . _DB_PREFIX_ . 'order_state` os
        LEFT JOIN `' . _DB_PREFIX_ . 'order_history` oh ON (os.`id_order_state` = oh.`id_order_state`)
        WHERE oh.`id_order` = ' . (int) $this->id_order . '
        AND os.`logable` = 1');
    }

    /**
     * Add method for webservice create resource Order History
     * If sendemail=1 GET parameter is present sends email to customer otherwise does not.
     *
     * @return bool
     */
    public function addWs()
    {
        $sendemail = (bool) Tools::getValue('sendemail', false);
        $this->changeIdOrderState($this->id_order_state, $this->id_order);

        if ($sendemail) {
            //Mail::Send requires link object on context and is not set when getting here
            $context = Context::getContext();
            if ($context->link == null) {
                $protocol_link = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
                $protocol_content = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
                $context->link = new Link($protocol_link, $protocol_content);
            }

            return $this->addWithemail();
        } else {
            return $this->add();
        }
    }
}
