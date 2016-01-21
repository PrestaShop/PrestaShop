<?php
/**
 * 2007-2015 PrestaShop
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class OrderDetailControllerCore extends ProductPresentingFrontControllerCore
{
    public $php_self = 'order-detail';
    public $auth = true;
    public $authRedirection = 'history';
    public $ssl = true;

    protected $order_to_display;

    /**
     * Initialize order detail controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Start forms process
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitMessage')) {
            $idOrder = (int)Tools::getValue('id_order');
            $msgText = Tools::getValue('msgText');

            if (!$idOrder || !Validate::isUnsignedId($idOrder)) {
                $this->errors[] = $this->l('The order is no longer valid.');
            } elseif (empty($msgText)) {
                $this->errors[] = $this->l('The message cannot be blank.');
            } elseif (!Validate::isMessage($msgText)) {
                $this->errors[] = $this->l('This message is invalid (HTML is not allowed).');
            }
            if (!count($this->errors)) {
                $order = new Order($idOrder);
                if (Validate::isLoadedObject($order) && $order->id_customer == $this->context->customer->id) {
                    //check if a thread already exist
                    $id_customer_thread = CustomerThread::getIdCustomerThreadByEmailAndIdOrder($this->context->customer->email, $order->id);
                    $id_product = (int)Tools::getValue('id_product');
                    $cm = new CustomerMessage();
                    if (!$id_customer_thread) {
                        $ct = new CustomerThread();
                        $ct->id_contact = 0;
                        $ct->id_customer = (int)$order->id_customer;
                        $ct->id_shop = (int)$this->context->shop->id;
                        if ($id_product && $order->orderContainProduct($id_product)) {
                            $ct->id_product = $id_product;
                        }
                        $ct->id_order = (int)$order->id;
                        $ct->id_lang = (int)$this->context->language->id;
                        $ct->email = $this->context->customer->email;
                        $ct->status = 'open';
                        $ct->token = Tools::passwdGen(12);
                        $ct->add();
                    } else {
                        $ct = new CustomerThread((int)$id_customer_thread);
                        $ct->status = 'open';
                        $ct->update();
                    }

                    $cm->id_customer_thread = $ct->id;
                    $cm->message = $msgText;
                    $cm->ip_address = (int)ip2long($_SERVER['REMOTE_ADDR']);
                    $cm->add();

                    if (!Configuration::get('PS_MAIL_EMAIL_MESSAGE')) {
                        $to = strval(Configuration::get('PS_SHOP_EMAIL'));
                    } else {
                        $to = new Contact((int)Configuration::get('PS_MAIL_EMAIL_MESSAGE'));
                        $to = strval($to->email);
                    }
                    $toName = strval(Configuration::get('PS_SHOP_NAME'));
                    $customer = $this->context->customer;

                    $product = new Product($id_product);
                    $product_name = '';
                    if (Validate::isLoadedObject($product) && isset($product->name[(int)$this->context->language->id])) {
                        $product_name = $product->name[(int)$this->context->language->id];
                    }

                    if (Validate::isLoadedObject($customer)) {
                        Mail::Send(
                            $this->context->language->id,
                            'order_customer_comment',
                            Mail::l('Message from a customer'),
                            array(
                                '{lastname}' => $customer->lastname,
                                '{firstname}' => $customer->firstname,
                                '{email}' => $customer->email,
                                '{id_order}' => (int)$order->id,
                                '{order_name}' => $order->getUniqReference(),
                                '{message}' => Tools::nl2br($msgText),
                                '{product_name}' => $product_name
                            ),
                            $to,
                            $toName,
                            $customer->email,
                            $customer->firstname.' '.$customer->lastname
                        );
                    }

                    $this->success[] = $this->l('Message successfully sent');
                } else {
                    $this->redirect_after = '404';
                    $this->redirect();
                }
            }
        }
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        if (!($id_order = (int)Tools::getValue('id_order')) || !Validate::isUnsignedId($id_order)) {
            $this->redirect_after = '404';
            $this->redirect();
        } else {
            $order = new Order($id_order);
            if (Validate::isLoadedObject($order) && $order->id_customer == $this->context->customer->id) {
                $this->order_to_display['data'] = $this->getTemplateVarOrder($order);
                $this->order_to_display['products'] = $this->getTemplateVarProducts($order);
                $this->order_to_display['history'] = $this->getTemplateVarOrderHistory($order);
                $this->order_to_display['addresses'] = $this->getTemplateVarAddresses($order);
                $this->order_to_display['shipping'] = $this->getTemplateVarShipping($order);
                $this->order_to_display['messages'] = $this->getTemplateVarMessages($order);
                $this->order_to_display['carrier'] = $this->getTemplateVarCarrier($order);

                $this->order_to_display['data']['followup'] = '';
                if ($this->order_to_display['carrier']['url'] && $order->shipping_number) {
                    $this->order_to_display['data']['followup'] = str_replace('@', $order->shipping_number, $this->order_to_display['carrier']['url']);
                }

                $this->context->smarty->assign([
                    'order' => $this->order_to_display,
                    'hook_orderdetaildisplayed' => Hook::exec('displayOrderDetail', ['order' => $order]),
                    'use_tax' => Configuration::get('PS_TAX'),
                ]);
            } else {
                $this->redirect_after = '404';
                $this->redirect();
            }
            unset($order);
        }

        $this->setTemplate('customer/order-detail.tpl');
    }

    public function getTemplateVarOrder($order_object)
    {
        $order_data = $this->objectSerializer->toArray($order_object);

        $order_data['id_order'] = $order_data['id'];
        $order_data['reference'] = Order::getUniqReferenceOf($order_object->id);
        $order_data['order_date'] = Tools::displayDate($order_object->date_add, null, false);
        $order_data['url_to_reorder'] = HistoryController::getUrlToReorder((int)$order_object->id, $this->context);
        $order_data['url_to_invoice'] = HistoryController::getUrlToInvoice($order_object, $this->context);
        $order_data['gift_message'] = nl2br($order_data['gift_message']);
        $order_data['total_products'] = Tools::displayPrice($order_data['total_products'], (int)$order_data['id_currency']);
        $order_data['total_products_wt'] = Tools::displayPrice($order_data['total_products_wt'], (int)$order_data['id_currency']);
        $order_data['total_discounts'] = ($order_data['total_discounts'] > 0) ? Tools::displayPrice($order_data['total_discounts'], (int)$order_data['id_currency']) : 0;
        $order_data['total_shipping'] = ($order_data['total_shipping'] > 0) ? Tools::displayPrice($order_data['total_shipping'], (int)$order_data['id_currency']) : $this->l('Free !');
        $order_data['total_wrapping'] = ($order_data['total_wrapping'] > 0) ? Tools::displayPrice($order_data['total_wrapping'], (int)$order_data['id_currency']) : 0;
        $order_data['total_paid'] = Tools::displayPrice($order_data['total_paid'], (int)$order_data['id_currency']);
        $order_data['return_allowed'] = (int)$order_object->isReturnable();

        return $order_data;
    }

    public function getTemplateVarProducts($order_object)
    {
        $order_products = [];
        $customer = new Customer($order_object->id_customer);
        $include_taxes = (Group::getPriceDisplayMethod($customer->id_default_group) == PS_TAX_INC);
        $order_products = $order_object->getProducts();
        OrderReturn::addReturnedQuantity($order_products, $order_object->id);

        foreach ($order_products as $id_order_product => $order_product) {
            if (!isset($order_product['deleted'])) {
                $order_products[$id_order_product] = $order_product;
                $order_products[$id_order_product]['unit_price'] = Tools::displayPrice(($include_taxes ? $order_product['unit_price_tax_incl'] : $order_product['unit_price_tax_excl']), (int)$order_object->id_currency);
                $order_products[$id_order_product]['total_price'] = Tools::displayPrice(($include_taxes ? $order_product['total_price_tax_incl'] : $order_product['total_price_tax_excl']), (int)$order_object->id_currency);
                $order_products[$id_order_product]['customizations'] = ($order_product['customizedDatas']) ? $this->getTemplateVarCustomization($order_product) : [];
            }
        }

        return $order_products;
    }

    public function getTemplateVarCustomization(array $product)
    {
        $product_customizations = [];
        $imageRetriever = new Adapter_ImageRetriever($this->context->link);

        foreach ($product['customizedDatas'] as $byAddress) {
            foreach ($byAddress as $customization) {
                $presentedCustomization = [
                    'quantity'              => $customization['quantity'],
                    'fields'                => [],
                    'id_customization'      => null
                ];

                foreach ($customization['datas'] as $byType) {
                    $field = [];
                    foreach ($byType as $data) {
                        switch ($data['type']) {
                            case Product::CUSTOMIZE_FILE:
                                $field['type'] = 'image';
                                $field['image'] = $imageRetriever->getCustomizationImage(
                                    $data['value']
                                );
                                break;
                            case Product::CUSTOMIZE_TEXTFIELD:
                                $field['type'] = 'text';
                                $field['text'] = $data['value'];
                                break;
                            default:
                                $field['type'] = null;
                        }
                        $field['label'] = $data['name'];
                        $presentedCustomization['id_customization'] = $data['id_customization'];
                    }
                    $presentedCustomization['fields'][] = $field;
                }

                $product_customizations[] = $presentedCustomization;
            }
        }

        return $product_customizations;
    }

    public function getTemplateVarCarrier($order_object)
    {
        $carrier_object = new Carrier((int)$order_object->id_carrier, (int)$order_object->id_lang);
        $order_carrier = $this->objectSerializer->toArray($carrier_object);

        $order_carrier['name'] = ($carrier_object->name == '0') ? Configuration::get('PS_SHOP_NAME') : $carrier_object->name;

        return $order_carrier;
    }

    public function getTemplateVarOrderHistory($order_object)
    {
        $order_history = [];
        $histories = $order_object->getHistory($this->context->language->id, false, true);

        foreach ($histories as $id_history => $history) {
            $order_history[$id_history] = $history;
            $order_history[$id_history]['history_date'] = Tools::displayDate($history['date_add'], null, false);
            $order_history[$id_history]['contrast'] = (Tools::getBrightness($history['color']) > 128) ? 'dark' : 'bright';
        }

        return $order_history;
    }

    public function getTemplateVarAddresses($order_object)
    {
        $order_addresses = [
            'delivery' => [],
            'invoice' => []
        ];

        $addressDelivery = new Address((int)$order_object->id_address_delivery);
        $addressInvoice = new Address((int)$order_object->id_address_invoice);

        if (!$order_object->isVirtual()) {
            $order_addresses['delivery'] = $this->objectSerializer->toArray($addressDelivery);
            $order_addresses['delivery']['formatted'] = AddressFormat::generateAddress($addressDelivery, array(), '<br />');
        }

        $order_addresses['invoice'] = $this->objectSerializer->toArray($addressInvoice);
        $order_addresses['invoice']['formatted'] = AddressFormat::generateAddress($addressInvoice, array(), '<br />');

        return $order_addresses;
    }

    public function getTemplateVarShipping($order_object)
    {
        $order_shipping = [];
        $include_taxes = !Product::getTaxCalculationMethod((int)$this->context->cart->id_customer) && (int)Configuration::get('PS_TAX');
        $shippings = $order_object->getShipping();

        foreach ($shippings as $id_shipping => $shipping) {
            if (isset($shipping['carrier_name']) && $shipping['carrier_name']) {
                $order_shipping[$id_shipping] = $shipping;
                $order_shipping[$id_shipping]['shipping_date'] = Tools::displayDate($shipping['date_add'], null, false);
                $order_shipping[$id_shipping]['shipping_weight'] = ($shipping['weight'] > 0) ? sprintf('%.3f', $shipping['weight']).' '.Configuration::get('PS_WEIGHT_UNIT') : '-';
                $shipping_cost = (!$order_object->getTaxCalculationMethod()) ? $shipping['shipping_cost_tax_excl'] : $shipping['shipping_cost_tax_incl'];
                $order_shipping[$id_shipping]['shipping_cost'] = ($shipping_cost > 0) ? Tools::displayPrice($shipping_cost, (int)$order_object->id_currency) : $this->l('Free !');

                $tracking_line = '-';
                if ($shipping['tracking_number']) {
                    if ($shipping['url'] && $shipping['tracking_number']) {
                        $tracking_line = '<a href="'.str_replace('@', $shipping['tracking_number'], $shipping['url']).'">'.$shipping['tracking_number'].'</a>';
                    } else {
                        $tracking_line = $shipping['tracking_number'];
                    }
                }

                $order_shipping[$id_shipping]['tracking'] = $tracking_line;
            }
        }

        return $order_shipping;
    }

    public function getTemplateVarMessages($order_object)
    {
        $order_messages = [];
        $customer_messages = CustomerMessage::getMessagesByOrderId((int)$order_object->id, false);

        foreach ($customer_messages as $id_customer_message => $customer_message) {
            $order_messages[$id_customer_message] = $customer_message;
            $order_messages[$id_customer_message]['message'] = nl2br($customer_message['message']);
            $order_messages[$id_customer_message]['message_date'] = Tools::displayDate($customer_message['date_add'], null, true);
            if (isset($customer_message['elastname']) && $customer_message['elastname']) {
                $order_messages[$id_customer_message]['name'] = $customer_message['efirstname'].' '.$customer_message['elastname'];
            } elseif ($customer_message['clastname']) {
                $order_messages[$id_customer_message]['name'] = $customer_message['cfirstname'].' '.$customer_message['clastname'];
            } else {
                $order_messages[$id_customer_message]['name'] = Configuration::get('PS_SHOP_NAME');
            }
        }

        return $order_messages;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        if (($id_order = (int)Tools::getValue('id_order')) && Validate::isUnsignedId($id_order)) {
            $breadcrumb['links'][] =[
                'title' => $this->getTranslator()->trans('Order history', [], 'Breadcrumb'),
                'url' => $this->context->link->getPageLink('history')
            ];
        }

        return $breadcrumb;
    }
}
